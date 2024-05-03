<?php

namespace App\DataTransferObject;

class EmailDto implements IDataTransferObject
{
    public function __construct(
       readonly public string $email,
       readonly public bool $primary = false,
       readonly public bool $verified = false,
       readonly public ?string $visibility = null,
    ) {}
}
