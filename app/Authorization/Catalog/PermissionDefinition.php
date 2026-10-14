<?php

namespace App\Authorization\Catalog;

final readonly class PermissionDefinition
{
    public function __construct(
        public string $key,
        public Layer $layer,
        public string $resource,
        public string $description,
    ) {}
}
