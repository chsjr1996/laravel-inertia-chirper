<?php

namespace App\Http\Requests;

use App\Models\Chirp;
use Illuminate\Foundation\Http\FormRequest;

class ChirpUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $chirp = Chirp::find($this->route("chirp"))->first();

        return $this->user()->can("update", $chirp);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "message" => "required|string|max:255",
        ];
    }
}
