<?php

namespace App\Authorization\Catalog;

final readonly class Section
{
    public function __construct(
        public string $key,
        public string $page,
        public string $requiredPermission,
    ) {}
}
