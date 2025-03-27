<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getPaginated(int $perPage, int $page): LengthAwarePaginator
    {
        return User::query()->forPage($page, $perPage)->paginate($perPage, ['*'], 'page', $page);
    }
}
