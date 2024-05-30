<?php
declare(strict_types=1);

namespace App\Services\SlackBot\DataTransferObject;

use Symfony\Component\Serializer\Annotation\SerializedName;

class ContainerDto
{
    public function __construct(
        #[SerializedName('view_id')]
        public ?string $id,
        public ?string $type,
    ) {}
}
