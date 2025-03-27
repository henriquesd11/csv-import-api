<?php

namespace App\Services;

use App\Enums\ImportResponses;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CsvImportService
{
    protected $userRespository;

    public function __construct(UserRepository $userRespository)
    {
        $this->userRespository = $userRespository;
    }

    /**
     * @throws Exception
     */
    public function processCsv(string $filePath): void
    {
        $normalizedPath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . $normalizedPath);

        $this->validIfFileExists($fullPath);
        $file = fopen($fullPath, 'r');
        $this->validOpenFile($file, $fullPath);

        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $data = [
                'name' => $row[0],
                'email' => $row[1],
                'birth_date' => $row[2],
            ];

            $validator = Validator::make($data, [
                'name' => 'required|string|min:3|max:255',
                'email' => 'required|email|unique:users,email',
                'birth_date' => 'required|date_format:Y-m-d',
            ]);

            if ($validator->fails()) {
                Log::warning(
                    ImportResponses::VALIDATION_ERROR->value, ['data' => $data, 'errors' => $validator->errors()]
                );
                continue;
            }

            $this->userRespository->create($data);
        }

        fclose($file);
    }

    /**
     * @throws Exception
     */
    public function validIfFileExists(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            Log::error(ImportResponses::FILE_NOT_FOUND->value, ['file_path' => $filePath]);

            throw new Exception(ImportResponses::FILE_NOT_FOUND->value . $filePath);
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function validOpenFile($file, string $fulPath): bool
    {
        if (!$file) {
            Log::error(ImportResponses::ERROR_OPENING_FILE->value . $fulPath);

            throw new Exception(ImportResponses::ERROR_OPENING_FILE->value . $fulPath);
        }

        return true;
    }
}
