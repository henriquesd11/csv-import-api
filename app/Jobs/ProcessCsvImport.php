<?php

namespace App\Jobs;

use App\Enums\ImportResponses;
use App\Enums\ImportStatusResponses;
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
            $this->import->update(['status' => ImportStatusResponses::PROCESSING]);
            $csvImportService->processCsv($this->filePath);
            $this->import->update(['status' => ImportStatusResponses::COMPLETED]);
        } catch (\Exception $e) {
            $this->import->update(['status' => ImportStatusResponses::COMPLETED]);
            Log::error(ImportResponses::FILE_ADDED_TO_IMPORT_QUEUE->value . $e->getMessage());

            throw $e;
        }
    }
}
