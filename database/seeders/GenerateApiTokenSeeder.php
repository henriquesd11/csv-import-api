<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Tymon\JWTAuth\Facades\JWTAuth;

class GenerateApiTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'apiuser@example.com'],
            [
                'name' => 'API User',
                'birth_date' => '1990-01-01'
            ]
        );

        $token = JWTAuth::fromUser($user);

        echo "Token de Acesso Gerado: " . $token . PHP_EOL;

        file_put_contents(base_path('.env'), "API_TOKEN={$token}\n", FILE_APPEND);
    }
}
