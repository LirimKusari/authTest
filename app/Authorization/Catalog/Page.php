<?php

namespace App\Authorization\Catalog;

final readonly class Page
{
    public function __construct(
        public string $key,
        public string $resource,
        public string $requiredPermission,
    ) {}
}
