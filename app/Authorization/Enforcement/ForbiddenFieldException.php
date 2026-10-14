<?php

namespace App\Authorization\Enforcement;

use RuntimeException;

class ForbiddenFieldException extends RuntimeException
{
    /**
     * @param  list<string>  $fields
     */
    public function __construct(private array $fields)
    {
        parent::__construct('You are not allowed to write these fields.');
    }

    /**
     * @return list<string>
     */
    public function fields(): array
    {
        return $this->fields;
    }
}
