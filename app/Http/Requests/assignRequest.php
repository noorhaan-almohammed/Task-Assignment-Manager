<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class assignRequest extends FormRequest
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
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    public function validatedWithCasts ()
    {
        return $this->safe()->merge([
            'assign_date' => now(),
            'due_date' => now()->addDays($this->input('execute_time')),
        ]);
    }
    public function messages(): array
    {
        return [
            'user_id.exists' => 'User ID Not Found.',
        ];
    }

}
