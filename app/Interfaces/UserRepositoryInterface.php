<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $data);

    public function getPaginated(int $perPage, int $page);
}
