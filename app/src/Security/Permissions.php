<?php

declare(strict_types=1);

namespace App\Security;

interface Permissions
{
    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';
}
