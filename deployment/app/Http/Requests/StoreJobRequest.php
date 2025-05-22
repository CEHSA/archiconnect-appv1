<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only clients can create jobs through this request
        return Auth::check() && Auth::user()->role === User::ROLE_CLIENT;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'skills_required' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'not_to_exceed_budget' => 'nullable|numeric|min:0',
        ];
    }
}
