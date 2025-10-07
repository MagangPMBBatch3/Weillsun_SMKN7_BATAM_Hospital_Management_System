<?php

namespace App\Http\Requests;

use App\Models\UsersProfile\UsersProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users_profile')->ignore($this->user()->profile?->id),
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'telepon' => ['nullable', 'numeric', 'min:12'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'tenaga_medis_id' => ['nullable', 'exists:tenaga_medis,id'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }
}
