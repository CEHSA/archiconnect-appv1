<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can update jobs through this request
        return Auth::guard('admin')->check();
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
            'user_id' => 'nullable|exists:users,id,role,' . User::ROLE_CLIENT,
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'not_to_exceed_budget' => 'nullable|numeric|min:0',
            'skills_required' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'open', 'in_progress', 'submitted', 'under_review', 'approved', 'completed', 'on_hold', 'cancelled', 'closed'])],
            'assigned_freelancer_id' => 'nullable|exists:users,id,role,' . User::ROLE_FREELANCER, // Assuming User::ROLE_FREELANCER constant exists
        ];
    }
}
