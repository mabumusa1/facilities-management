<?php

namespace Database\Factories;

use App\Models\InvoiceSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceSetting>
 */
class InvoiceSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'name_en' => fake()->company(),
            'name_ar' => null,
            'logo' => null,
            'logo_path' => null,
            'logo_ar_path' => null,
            'address' => fake()->address(),
            'vat' => '15.00',
            'vat_number' => fake()->numerify('3##########3'),
            'cr_number' => fake()->numerify('10########'),
            'instructions' => null,
            'notes' => null,
            'timezone' => 'UTC',
            'primary_color' => null,
            'invoice_prefix' => 'INV',
            'invoice_next_sequence' => 1,
            'payment_terms_days' => 30,
            'late_payment_penalty_pct' => null,
            'late_payment_grace_days' => 0,
            'footer_text_en' => null,
            'footer_text_ar' => null,
            'show_vat_number' => true,
        ];
    }
}
