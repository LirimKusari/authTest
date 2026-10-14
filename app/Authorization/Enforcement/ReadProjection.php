<?php

namespace App\Authorization\Enforcement;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ReadProjection
{
    public function __construct(private Authorizer $authorizer) {}

    /**
     * @return array{data: array<string, mixed>, abilities: array<string, mixed>}
     */
    public function present(User $user, string $pageKey, Model $model): array
    {
        return [
            'data' => $this->masked($user, $model),
            'abilities' => $this->authorizer->abilities($user, $pageKey, $model)->toArray(),
        ];
    }

    /**
     * @param  Collection<int, Model>  $models
     * @return array{data: list<array<string, mixed>>}
     */
    public function collection(User $user, string $pageKey, Collection $models): array
    {
        return [
            'data' => $models
                ->map(function (Model $model) use ($user, $pageKey) {
                    return $this->masked($user, $model) + [
                        'abilities' => $this->authorizer->abilities($user, $pageKey, $model)->toArray(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function masked(User $user, Model $model): array
    {
        $payload = ['id' => $model->getKey()];

        foreach ($this->authorizer->readMask($user, $this->authorizer->resourceOf($model)) as $attribute) {
            $payload[$attribute] = $model->getAttribute($attribute);
        }

        return $payload;
    }
}
