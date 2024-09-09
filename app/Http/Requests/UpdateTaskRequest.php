<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Rules\GreaterThanValue;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules(): array
    {

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'priority_id' => 'sometimes|integer|exists:priority_tasks,id',
            'execute_time' => 'sometimes', 'integer', 'min:1',
            'user_id' => 'sometimes|nullable|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Title must be a string.',
            'description.string' => 'Description must be a string.',
            'priority_id.integer' => 'Priority ID must be an integer.',
            'execute_time.min' => 'Execute time must be greater than 0',
            'user_id.integer' => 'User ID must be an integer.',
            'user_id.exists' => 'The selected user ID does not exist.',
        ];
    }
}
