<?php

namespace App\Http\Requests\Api;

class HabitLogStoreRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'note' => 'nullable|string',
        ];
    }
}
