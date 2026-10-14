<?php

namespace App\Authorization\Enforcement;

use App\Models\User;

class WriteGuard
{
    public function __construct(private Authorizer $authorizer) {}

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    public function permitted(User $user, string $resource, array $input): array
    {
        $mask = $this->authorizer->writeMask($user, $resource);
        $submitted = array_keys($input);
        $forbidden = array_values(array_diff($submitted, $mask));

        if ($forbidden !== []) {
            throw new ForbiddenFieldException($forbidden);
        }

        $permitted = [];

        foreach ($mask as $attribute) {
            if (array_key_exists($attribute, $input)) {
                $permitted[$attribute] = $input[$attribute];
            }
        }

        return $permitted;
    }
}
