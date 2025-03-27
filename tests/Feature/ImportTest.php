<?php

namespace Tests\Feature;

use App\Jobs\ProcessCsvImport;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_upload_csv_successfully()
    {
        Queue::fake();

        $file = UploadedFile::fake()->create('users.csv', 100, 'text/csv');

        $response = $this->postJson('/api/upload', ['file' => $file]);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'message',
                'link_status',
            ]);
    }
}
