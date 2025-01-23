<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateAssistantRequest extends FormRequest
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
            'model' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:512',
            'instructions' => 'required|string|max:2000',
            'tools' => 'nullable|array',
            'top_p' => 'required|numeric|min:0.1|max:1',
            'temperature' => 'required|numeric|min:0.1|max:2'
        ];
    }

    /**
     * Format the tools field.
     */
    protected function formatTools(?array $tools): array
    {
        return $tools ? array_map(fn($tool) => ['type' => $tool], $tools) : [];
    }

    /**
     * Customize the validated data returned by the request.
     *
     * @param string|null $key
     * @param bool $stripNulls
     * @return array
     */
    public function validated($key = null, $stripNulls = true): array
    {
        $validated = parent::validated($key, $stripNulls);

        // Cast 'top_p' and 'temperature' to float
        if (isset($validated['top_p'])) {
            $validated['top_p'] = (float) $validated['top_p'];
        }
        if (isset($validated['temperature'])) {
            $validated['temperature'] = (float) $validated['temperature'];
        }

        // Format the tools
        $validated['tools'] = $this->formatTools($validated['tools'] ?? null);

        return $validated;
    }
}
