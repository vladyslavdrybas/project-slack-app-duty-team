<?php

namespace App\Controller\Metrics;

use App\Controller\AbstractController;
use App\Entity\GithubAccessToken;
use App\Entity\GithubUserContributions;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/metrics/github', name: "metrics_github")]
class GithubMetricsController extends AbstractController
{
    #[Route("/contributions", name: "_contributions", methods: ["GET"])]
    public function contributions(Request $request, HttpClientInterface $httpClient): JsonResponse
    {
        $user = $this->getUser();
        $githubAccessTokens = $this->entityManager->getRepository(GithubAccessToken::class)->findBy([
            'owner' => $user->getId(),
        ]);

        foreach ($githubAccessTokens as $accessToken)
        {
            $this->getStoreMetrics(
                $httpClient,
                $user,
                $accessToken->getUsername(),
                $accessToken->getAccessToken(),
            );
        }

        return $this->json(['message' => 'success'], Response::HTTP_OK);
    }

    protected function getStoreMetrics(
        HttpClientInterface $httpClient,
        User $owner,
        string $githubUsername,
        string $githubAccessToken
    ): void {
        $queryYears = '
            query($userName:String!) {
                user(login: $userName){
                    contributionsCollection {
                        contributionYears
                    }
                }
            }
        ';

        $variables = '{"userName": "'. $githubUsername .'"}';

        $body = [
            'query' => $queryYears,
            'variables' => $variables,
        ];

        $requestBody = json_encode($body);

        $response = $httpClient->request(
            'POST',
            'https://api.github.com/graphql',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $githubAccessToken,
                ],
                'body' => $requestBody,
            ]
        );

        $userContributions = $response->toArray();
        $userContributionYears = $userContributions['data']['user']['contributionsCollection']['contributionYears'];

        $currentYear = (new DateTime())->format('Y');
        $metricsRepo = $this->entityManager->getRepository(GithubUserContributions::class);
        foreach ($userContributionYears as $userContributionYear)
        {
            $githubUserContributions = $metricsRepo->findOneBy([
                'owner' => $owner,
                'year' => $userContributionYear,
            ]);

            if (!$githubUserContributions instanceof GithubUserContributions) {
                $githubUserContributions = new GithubUserContributions();
            } else {
                if ($githubUserContributions->getYear() < $currentYear - 1) {
                    continue;
                }
            }

            $githubUserContributions->setOwner($owner);
            $githubUserContributions->setYear($userContributionYear);

            $from = new DateTime();
            $from->setDate($userContributionYear, 1 , 1)->setTime(0, 0 , 0);
            $to = new DateTime();
            $to->setDate($userContributionYear, 12 , 31)->setTime(23, 59 , 59);

            $query = '
            query($userName:String!) {
                    user(login: $userName){
                        contributionsCollection(from:"'. $from->format(DateTimeInterface::ATOM) .'", to:"'. $to->format(DateTimeInterface::ATOM) .'") {
                            totalCommitContributions
                            totalIssueContributions
                            totalPullRequestContributions
                            totalPullRequestReviewContributions
                            totalRepositoriesWithContributedCommits
                            totalRepositoriesWithContributedIssues
                            totalRepositoriesWithContributedPullRequestReviews
                            totalRepositoriesWithContributedPullRequests
                            totalRepositoryContributions

                            contributionCalendar {
                                totalContributions
                                weeks {
                                    contributionDays {
                                        contributionCount
                                        date
                                        contributionLevel
                                        color
                                        contributionLevel
                                        weekday
                                    }
                                }
                            }
                        }
                    }
                }
            ';

            $body = [
                'query' => $query,
                'variables' => $variables,
            ];

            $requestBody = json_encode($body);

            $response = $httpClient->request(
                'POST',
                'https://api.github.com/graphql',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $githubAccessToken,
                    ],
                    'body' => $requestBody,
                ]
            );

            $contributionsCollection = $response->toArray()['data']['user']['contributionsCollection'];
            $calendar = $contributionsCollection['contributionCalendar'];

            $githubUserContributions->setTotal($calendar['totalContributions']);
            $githubUserContributions->setWeeks($calendar['weeks']);

            $metadata = [
                'totalCommitContributions' => $contributionsCollection['totalCommitContributions'],
                'totalIssueContributions' => $contributionsCollection['totalIssueContributions'],
                'totalPullRequestContributions' => $contributionsCollection['totalPullRequestContributions'],
                'totalPullRequestReviewContributions' => $contributionsCollection['totalPullRequestReviewContributions'],
                'totalRepositoriesWithContributedCommits' => $contributionsCollection['totalRepositoriesWithContributedCommits'],
                'totalRepositoriesWithContributedIssues' => $contributionsCollection['totalRepositoriesWithContributedIssues'],
                'totalRepositoriesWithContributedPullRequestReviews' => $contributionsCollection['totalRepositoriesWithContributedPullRequestReviews'],
                'totalRepositoriesWithContributedPullRequests' => $contributionsCollection['totalRepositoriesWithContributedPullRequests'],
                'totalRepositoryContributions' => $contributionsCollection['totalRepositoryContributions'],
                'totalContributions' => $calendar['totalContributions'],
            ];
            $githubUserContributions->setMetadata($metadata);

            $metricsRepo->add($githubUserContributions);
        }

        if (count($userContributionYears) > 0) {
            $metricsRepo->save();
        }
    }
}
