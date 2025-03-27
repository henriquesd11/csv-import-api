<?php

namespace App\Jobs;

use App\Models\Imports;
use App\Services\CsvImportService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected string $filePath;
    protected Imports $import;
    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, Imports $import)
    {
        $this->filePath = $filePath;
        $this->import = $import;
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(CsvImportService $csvImportService): void
    {
        try {
            $this->import->update(['status' => 'processing']);
            $csvImportService->processCsv($this->filePath);
            $this->import->update(['status' => 'completed']);
        } catch (\Exception $e) {
            $this->import->update(['status' => 'failed']);
            Log::error("Erro ao processar o CSV: " . $e->getMessage());

            throw $e;
        }
    }
}
