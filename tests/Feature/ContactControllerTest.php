<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    private const CONTACTS_ROUTE = '/contacts';

    private const CONTACTS_CREATE_ROUTE = '/contacts/create';

    private const ADMIN_CONTACTS_CREATE_ROUTE = self::CONTACTS_CREATE_ROUTE.'?type=admin';

    protected Tenant $tenant;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /**
     * Captured parity: admin contacts require a manager role and role is persisted.
     */
    public function test_store_creates_admin_contact_with_valid_role(): void
    {
        $response = $this->actingAs($this->user)
            ->post(self::CONTACTS_ROUTE, [
                'contact_type' => 'admin',
                'role' => 'Admins',
                'first_name' => 'Alice',
                'last_name' => 'Manager',
                'email' => 'alice.manager@example.com',
                'phone_number' => '+966500000111',
                'phone_country_code' => 'sa',
                'national_phone_number' => '0500000111',
            ]);

        $contact = Contact::query()->where('email', 'alice.manager@example.com')->first();

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('contacts.show', ['contact' => $contact]));
        $this->assertDatabaseHas('contacts', [
            'id' => $contact?->id,
            'tenant_id' => $this->tenant->id,
            'contact_type' => 'admin',
            'role' => 'Admins',
            'phone_country_code' => 'SA',
        ]);
    }

    public function test_store_rejects_admin_contact_without_role(): void
    {
        $response = $this->actingAs($this->user)
            ->from(self::ADMIN_CONTACTS_CREATE_ROUTE)
            ->post(self::CONTACTS_ROUTE, [
                'contact_type' => 'admin',
                'first_name' => 'No',
                'last_name' => 'Role',
                'email' => 'no-role@example.com',
                'phone_number' => '+966500000222',
                'phone_country_code' => 'SA',
                'national_phone_number' => '0500000222',
            ]);

        $response->assertRedirect(self::ADMIN_CONTACTS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('contacts', [
            'email' => 'no-role@example.com',
        ]);
    }

    public function test_update_keeps_existing_admin_role_when_role_is_not_submitted(): void
    {
        $contact = Contact::factory()
            ->admin()
            ->forTenant($this->tenant)
            ->create([
                'role' => 'serviceManagers',
                'email' => 'existing-admin@example.com',
            ]);

        $response = $this->actingAs($this->user)
            ->put(self::CONTACTS_ROUTE.'/'.$contact->id, [
                'first_name' => 'Updated',
                'last_name' => 'Admin',
                'email' => 'existing-admin@example.com',
                'phone_number' => '+966500009999',
                'phone_country_code' => 'sa',
                'national_phone_number' => '0500009999',
                'active' => true,
            ]);

        $response->assertSessionHasNoErrors();
        $contact->refresh();

        $this->assertSame('serviceManagers', $contact->role);
        $this->assertSame('Updated', $contact->first_name);
        $this->assertSame('SA', $contact->phone_country_code);
    }

    public function test_update_clears_role_when_contact_changes_from_admin_to_non_admin(): void
    {
        $contact = Contact::factory()
            ->admin()
            ->forTenant($this->tenant)
            ->create([
                'role' => 'marketingManagers',
                'email' => 'switch-role@example.com',
            ]);

        $response = $this->actingAs($this->user)
            ->put(self::CONTACTS_ROUTE.'/'.$contact->id, [
                'contact_type' => 'owner',
                'first_name' => 'Owner',
                'last_name' => 'Now',
                'email' => 'switch-role@example.com',
                'phone_number' => '+966500001111',
                'phone_country_code' => 'SA',
                'national_phone_number' => '0500001111',
                'active' => true,
            ]);

        $response->assertSessionHasNoErrors();
        $contact->refresh();

        $this->assertSame('owner', $contact->contact_type);
        $this->assertNull($contact->role);
    }

    public function test_store_rejects_unknown_admin_role_value(): void
    {
        $response = $this->actingAs($this->user)
            ->from(self::ADMIN_CONTACTS_CREATE_ROUTE)
            ->post(self::CONTACTS_ROUTE, [
                'contact_type' => 'admin',
                'role' => 'invalid-role',
                'first_name' => 'Bad',
                'last_name' => 'Role',
                'email' => 'bad-role@example.com',
                'phone_number' => '+966500000333',
                'phone_country_code' => 'SA',
                'national_phone_number' => '0500000333',
            ]);

        $response->assertRedirect(self::ADMIN_CONTACTS_CREATE_ROUTE);
        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('contacts', [
            'email' => 'bad-role@example.com',
        ]);
    }
}
