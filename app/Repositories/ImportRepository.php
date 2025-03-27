<?php

namespace App\Repositories;

use App\Models\Imports;

class ImportRepository
{
    public function create(array $data): Imports
    {
        return Imports::create($data);
    }

    public function findById(int $id): Imports
    {
        return Imports::findOrFail($id);
    }
}
