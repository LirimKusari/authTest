<?php

namespace App\Authorization\Catalog;

final readonly class FieldSet
{
    /**
     * @param  list<string>  $attributes
     */
    public function __construct(
        public string $key,
        public string $resource,
        public array $attributes,
        public string $readPermission,
        public ?string $writePermission,
    ) {}
}
