<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\JobPosting;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure the authenticated user is a freelancer
        if (!Auth::check() || Auth::user()->role !== 'freelancer') {
            return false;
        }

        // Ensure the job_posting_id exists and belongs to this freelancer or is generally available
        // For now, we assume job_posting_id is always provided for applications via this route
        $jobPosting = JobPosting::find($this->input('job_posting_id'));
        if (!$jobPosting || $jobPosting->freelancer_id !== Auth::id()) {
             // If job_posting_id is not for this freelancer, deny.
             // Could be expanded if general applications to non-posted jobs are allowed.
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_posting_id' => ['required', 'integer', 'exists:job_postings,id'],
            'cover_letter' => ['required', 'string', 'max:5000'],
            'proposed_rate' => ['nullable', 'numeric', 'min:0'],
            'estimated_timeline' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'job_posting_id.exists' => 'The specified job posting does not exist or is not available to you.',
            'cover_letter.required' => 'A cover letter is required to apply.',
        ];
    }
}
