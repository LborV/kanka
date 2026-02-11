<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarTimeEntry extends FormRequest
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
            'name' => 'required|string|max:191',
            'entity_id' => 'nullable|integer|exists:entities,id',
            'day' => 'required|integer',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'start_hour' => 'required|integer|min:0',
            'start_minute' => 'required|integer|min:0|max:59',
            'duration' => 'required|integer|min:1',
            'comment' => 'nullable|string',
            'colour' => 'nullable|string|max:20',
            'visibility_id' => 'nullable|exists:visibilities,id',
        ];
    }
}
