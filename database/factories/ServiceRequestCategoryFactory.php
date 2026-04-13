<?php

namespace Database\Factories;

use App\Models\ServiceRequestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceRequestCategory>
 */
class ServiceRequestCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'name_ar' => null,
            'description' => $this->faker->sentence(),
            'description_ar' => null,
            'active' => true,
            'has_sub_categories' => false,
            'icon_id' => null,
            'service_settings' => [
                'visibilities' => [
                    'hide_resident_number' => false,
                    'hide_resident_name' => false,
                    'hide_professional_number_and_name' => false,
                    'show_unified_number_only' => false,
                ],
                'permissions' => [
                    'manager_close_Request' => false,
                    'not_require_professional_enter_request_code' => false,
                    'not_require_professional_upload_request_photo' => false,
                    'attachments_required' => false,
                    'allow_professional_reschedule' => false,
                ],
            ],
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    /**
     * Indicate that the category has subcategories.
     */
    public function withSubcategories(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_sub_categories' => true,
        ]);
    }

    /**
     * Indicate that attachments are required for this category.
     */
    public function requiresAttachments(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['service_settings'];
            $settings['permissions']['attachments_required'] = true;

            return ['service_settings' => $settings];
        });
    }

    /**
     * Indicate that manager can close requests for this category.
     */
    public function managerCanClose(): static
    {
        return $this->state(function (array $attributes) {
            $settings = $attributes['service_settings'];
            $settings['permissions']['manager_close_Request'] = true;

            return ['service_settings' => $settings];
        });
    }

    /**
     * Create the "Unit Services" category.
     */
    public function unitServices(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Unit Services',
            'name_ar' => 'خدمات الوحدات',
            'description' => 'Services for units',
            'description_ar' => 'للخدمات الخاصة بالوحدات',
            'has_sub_categories' => true,
        ]);
    }

    /**
     * Create the "Common Area Requests" category.
     */
    public function commonAreaRequests(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Common Area Requests',
            'name_ar' => 'طلبات المناطق المشتركة',
            'description' => 'Services for common areas',
            'description_ar' => 'للخدمات الخاصة بالمناطق المشتركة',
            'has_sub_categories' => true,
        ]);
    }

    /**
     * Create the "Visitor Access Requests" category.
     */
    public function visitorAccessRequests(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Visitor Access Requests',
            'name_ar' => 'طلبات تصاريح الزوار',
            'description' => 'Visitor entry permit requests',
            'description_ar' => 'لطلبات تصاريح دخول الزوار',
            'has_sub_categories' => false,
        ]);
    }
}
