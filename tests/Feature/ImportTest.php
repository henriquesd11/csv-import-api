<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Imports;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use DatabaseTransactions;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        // Cria um usuário e gera o token JWT para os testes
        $user = User::firstOrCreate(
            ['email' => 'apiuser@example.com'],
            ['name' => 'API User', 'birth_date' => '1990-01-01']
        );
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_welcome_route_is_public()
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Welcome to Laravel API CSV Importer'
            ]);
    }

    public function test_upload_csv_without_authentication_fails()
    {
        $file = UploadedFile::fake()->create('users.csv', 100, 'text/csv');

        $response = $this->postJson('/api/upload', ['file' => $file]);

        $response->assertStatus(401);
    }

    public function test_upload_csv_with_authentication_success()
    {
        Queue::fake(); // Simula a fila para evitar execução do job

        $file = UploadedFile::fake()->create('users.csv', 100, 'text/csv');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/upload', ['file' => $file]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'message',
                'link_status',
            ]);
    }

    public function test_get_import_status_with_authentication()
    {
        $import = Imports::create(['file_path' => 'uploads/test.csv', 'status' => 'pending']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson("/api/import-status/{$import->id}");

        $response->assertStatus(200)
            ->assertJson(['status' => 'pending']);
    }

    public function test_get_import_status_without_authentication_fails()
    {
        $import = Imports::create(['file_path' => 'uploads/test.csv', 'status' => 'pending']);

        $response = $this->getJson("/api/import-status/{$import->id}");

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Não autenticado.',
                'status' => 'error'
            ]);
    }
}
