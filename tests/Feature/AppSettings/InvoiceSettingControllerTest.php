<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_guests_cannot_access_invoice_settings(): void
    {
        $response = $this->get(route('app-settings.invoice.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_invoice_settings(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('app-settings.invoice.edit'));

        $response->assertOk();
    }

    public function test_authenticated_user_can_update_invoice_settings(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.invoice.update'), [
            'company_name' => 'Acme Corp',
            'address' => '123 Main St',
            'vat' => 15,
            'vat_number' => 'VAT123456',
            'cr_number' => 'CR789',
            'instructions' => 'Pay within 30 days',
            'notes' => 'Thank you for your business',
        ]);

        $response->assertRedirect(route('app-settings.invoice.edit'));
        $this->assertDatabaseHas('rf_invoice_settings', [
            'company_name' => 'Acme Corp',
            'vat_number' => 'VAT123456',
        ]);
    }

    public function test_invoice_settings_requires_company_name(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.invoice.update'), [
            'address' => '123 Main St',
            'vat' => 15,
        ]);

        $response->assertSessionHasErrors(['company_name']);
    }
}
