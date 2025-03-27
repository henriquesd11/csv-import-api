<?php

namespace Tests\Feature;

use App\Enums\ImportResponses;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\CsvImportService;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class CsvImportServiceTest extends TestCase
{
    use DatabaseTransactions;
    protected $userRepository;
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepository::class);
        $this->service = new CsvImportService($this->userRepository);
    }

    public function test_process_csv_successfully_creates_users()
    {
        $this->userRepository->shouldReceive('create')
            ->once()
            ->with([
                'name' => 'João',
                'email' => 'joao@example.com',
                'birth_date' => '1990-01-01'
            ])
            ->andReturn(new User(['name' => 'teste', 'email' => 'teste@teste.com', 'birth_date' => '1990-01-01']));

        $csvContent = "name,email,birth_date\nJoão,joao@example.com,1990-01-01";
        $filePath = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'test_success.csv';
        file_put_contents(storage_path('app/' . $filePath), $csvContent);

        // Executa o método
        $this->service->processCsv($filePath);

        // Limpeza
        unlink(storage_path('app/' . $filePath));

        $this->assertTrue(true);
    }

    /**
     * @throws Exception
     */
    public function test_process_csv_with_invalid_data_logs_warning()
    {
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === ImportResponses::VALIDATION_ERROR->value &&
                    $context['data']['email'] === 'invalid-email' &&
                    isset($context['errors']);
            });

        $this->userRepository->shouldNotReceive('create');

        $csvContent = "name,email,birth_date\nJoão,invalid-email,1990-01-01";
        $filePath = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'test_invalid.csv';
        file_put_contents(storage_path('app/' . $filePath), $csvContent);

        $this->service->processCsv($filePath);

        unlink(storage_path('app/' . $filePath));

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
