<?php

namespace App\Http\Controllers;

use App\Enums\ImportResponses;
use App\Http\Requests\UploadCsvRequest;
use App\Jobs\ProcessCsvImport;
use App\Models\Imports;
use App\Repositories\ImportRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ImportController extends Controller
{
    protected $importRepository;

    public function __construct(ImportRepository $importRepository)
    {
        $this->importRepository = $importRepository;
    }

    public function upload(UploadCsvRequest $request): JsonResponse
    {
        $path = 'public/' . $request->file('file')->store('uploads', 'public');

        $fullPath = storage_path(
            'app' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path)
        );
        Log::info(ImportResponses::LOG_SAVED_IN->value . $fullPath);

        $import = Imports::create(['file_path' => $path]);

        ProcessCsvImport::dispatch($path, $import);

        return response()->json([
            'message' => ImportResponses::LOG_SAVED_IN,
            'link_status' => env('APP_URL') . "/api/import-status/{$import->id}",
        ], Response::HTTP_ACCEPTED);
    }

    public function status(int $id)
    {
        $import = $this->importRepository->findById($id);

        return response()->json([
            'status' => $import->status
        ]);
    }
}
