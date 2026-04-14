<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactEntityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /**
     * Captured page: /contacts/tenants
     */
    public function test_contacts_tenants_alias_renders_index_with_tenant_filter(): void
    {
        Contact::factory()->tenant()->forTenant($this->tenant)->create();
        Contact::factory()->owner()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get('/contacts/tenants');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('contacts/index')
            ->where('filters.type', 'tenant')
        );
    }

    /**
     * Captured page: /contacts/owners
     */
    public function test_contacts_owners_alias_renders_index_with_owner_filter(): void
    {
        Contact::factory()->tenant()->forTenant($this->tenant)->create();
        Contact::factory()->owner()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get('/contacts/owners');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('contacts/index')
            ->where('filters.type', 'owner')
        );
    }

    /**
     * Captured page: /contacts/Tenant/form
     */
    public function test_contacts_legacy_form_alias_maps_to_contact_type_create_page(): void
    {
        $response = $this->actingAs($this->user)->get('/contacts/Tenant/form');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('contacts/create')
            ->where('contactType', 'tenant')
        );
    }

    /**
     * Captured page: /contacts/{id}/form
     */
    public function test_contacts_legacy_edit_alias_renders_edit_page(): void
    {
        $contact = Contact::factory()->tenant()->forTenant($this->tenant)->create();

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}/form");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('contacts/edit')
            ->has('contact')
        );
    }
}
