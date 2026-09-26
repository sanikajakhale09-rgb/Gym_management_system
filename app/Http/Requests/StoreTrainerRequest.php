<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // रूट किंवा रिक्वेस्टमधून ट्रेनरचा आयडी मिळवणे (डेटा अपडेट करताना वापरण्यासाठी)
        $trainerId = $this->route('id') ?? $this->route('trainer');

        return [
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            // इथे ignore वापरल्यामुळे एडिट करताना स्वतःचा ई-मेल आधीपासून अस्तित्वात असल्याची एरर येणार नाही
            'email' => [
                'required',
                'email',
                Rule::unique('trainers', 'email')->ignore($trainerId),
            ],
            'mobile' => 'required|digits:10',
            'specialization' => 'required|string|max:255',
            'experience' => 'required|integer|min:0',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.exists' => 'The selected branch is invalid or does not belong to your gym.',
            'mobile.digits' => 'Please enter a valid 10-digit mobile number.',
            'email.unique' => 'This email address has already been taken.',
        ];
    }
}