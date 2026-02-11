<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateCalendarWeather extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'year' => 'required|integer',
            'month_start' => 'required|integer|min:1',
            'month_end' => 'required|integer|min:1|gte:month_start',
            'overwrite' => 'nullable|boolean',
        ];
    }
}
