<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTask extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:35|unique:tasks,title',
            'description' => 'sometimes|string|max:225',
            'status_id' => 'sometimes|integer|exists:statuses',
            'priority_id' => 'sometimes|integer|exists:priorities',
            'execute_time' => 'sometimes|integer',
        ];
    }
}
