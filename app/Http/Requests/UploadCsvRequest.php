<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadCsvRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,txt'
        ];
    }
}
