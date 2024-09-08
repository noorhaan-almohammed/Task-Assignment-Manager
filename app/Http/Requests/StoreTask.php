<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class StoreTask extends FormRequest
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
            'title' => 'required|string|max:35|unique:tasks,title',
            'description' => 'required|string|max:225',
            'user_id' => 'required|integer|exists:users',
            'status_id' => 'required|integer|exists:statuses',
            'priority_id' => 'required|integer|exists:priorities',
            'execute_time' => 'required|integer',
            'rate' => 'nullable|integer|min:0|max:5'
        ];
    }
}

