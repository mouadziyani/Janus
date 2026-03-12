<?php

namespace App\Http\Requests\Api;

class HabitStoreRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|max:100',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'target_days' => 'required|integer|min:1',
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active' => 'boolean',
        ];
    }
}
