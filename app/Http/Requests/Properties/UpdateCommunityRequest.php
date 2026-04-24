<?php

namespace App\Http\Requests\Properties;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Form Request for updating a community.
 *
 * Coordinate pair constraint: latitude and longitude must both be present or
 * both absent. A payload with only one coordinate set is a data hazard because
 * downstream integrations expect a complete lat/lng pair.
 */
class UpdateCommunityRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'sales_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rental_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:rf_amenities,id'],
            'working_days' => ['nullable', 'array'],
            'working_days.*' => ['string', 'in:sat,sun,mon,tue,wed,thu,fri'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Add after-validation hooks.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $latitude = $this->input('latitude');
                $longitude = $this->input('longitude');

                $hasLat = $latitude !== null && $latitude !== '';
                $hasLng = $longitude !== null && $longitude !== '';

                if ($hasLat && ! $hasLng) {
                    $validator->errors()->add('longitude', __('validation.required_with', ['attribute' => 'longitude', 'values' => 'latitude']));
                }

                if ($hasLng && ! $hasLat) {
                    $validator->errors()->add('latitude', __('validation.required_with', ['attribute' => 'latitude', 'values' => 'longitude']));
                }
            },
        ];
    }
}
