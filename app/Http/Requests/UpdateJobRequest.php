<?php

namespace App\Http\Requests;

use App\Models\Job;
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
        $job = $this->route('job');

        // Admin can update any job
        if (Auth::user() && Auth::user()->role === 'admin') {
            return true;
        }

        // Client can update their own jobs
        if (Auth::user() && Auth::user()->role === 'client') {
            return $job && Auth::id() === $job->user_id;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget' => 'nullable|numeric|min:0',
            'skills_required' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'not_to_exceed_budget' => 'nullable|numeric|min:0',
        ];

        // Admin can update status
        if (Auth::user() && Auth::user()->role === 'admin') {
            $rules['status'] = ['nullable', Rule::in(['pending', 'open', 'in_progress', 'submitted', 'under_review', 'approved', 'completed', 'on_hold', 'cancelled', 'closed'])];
        }

        return $rules;
    }
}
