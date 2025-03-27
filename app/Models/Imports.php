<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imports extends Model
{
    protected $fillable = ['file_path', 'status'];
}
