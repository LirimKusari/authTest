<?php

namespace App\Authorization\Enforcement;

final readonly class AbilityMap
{
    /**
     * @param  array<string, bool>  $actions
     * @param  list<string>  $sections
     * @param  list<string>  $readableFields
     * @param  list<string>  $writableFields
     */
    public function __construct(
        public array $actions,
        public array $sections,
        public array $readableFields,
        public array $writableFields,
    ) {}

    /**
     * @return array{actions: array<string, bool>, sections: list<string>, readable_fields: list<string>, writable_fields: list<string>}
     */
    public function toArray(): array
    {
        return [
            'actions' => $this->actions,
            'sections' => $this->sections,
            'readable_fields' => $this->readableFields,
            'writable_fields' => $this->writableFields,
        ];
    }
}
