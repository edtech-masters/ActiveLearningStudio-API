<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class XapiParamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'agent' => 'nullable|string|max:255', // Example: agent must be a UUID (if provided)
            'verb' => 'nullable|string|max:255', // Example: verb must be a string
            'activity' => 'nullable|string|max:255', // Example: activity must be a string
            'related_agents' => 'nullable|boolean',
            'related_activities' => 'nullable|boolean',
            'since' => 'nullable|date', // Since must be a valid date (if provided)
            'until' => 'nullable|date', // Until must be a valid date (if provided)
            'limit' => 'nullable|integer|min:1|max:1000', // Limit must be an integer between 1 and 1000
            'format' => 'nullable|in:json,exact,ids', // Format must be one of: json, exact, ids
            'attachments' => 'nullable|boolean', // Attachments must be a boolean (true/false)
            'ascending' => 'nullable|boolean', // 1 or 0
        ];
    }
}
