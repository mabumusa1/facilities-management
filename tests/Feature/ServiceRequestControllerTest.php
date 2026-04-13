<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tenant first
        Tenant::factory()->create(['id' => 1]);

        // Create user with tenant and statuses
        $this->user = User::factory()->create([
            'tenant_id' => 1,
        ]);

        Status::factory()->create([
            'id' => 1,
            'name' => 'New',
            'domain' => 'request',
            'slug' => 'request_new',
        ]);

        Status::factory()->create([
            'id' => 2,
            'name' => 'Assigned',
            'domain' => 'request',
            'slug' => 'request_assigned',
        ]);
    }

    public function test_index_displays_service_requests(): void
    {
        $category = ServiceRequestCategory::factory()->create();
        ServiceRequest::factory()->count(3)->create([
            'category_id' => $category->id,
            'requester_id' => Contact::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('service-requests.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('service-requests/index')
            ->has('requests.data', 3)
        );
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('service-requests.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('service-requests/create')
            ->has('categories')
        );
    }

    public function test_show_displays_service_request(): void
    {
        $request = ServiceRequest::factory()->create([
            'category_id' => ServiceRequestCategory::factory()->create()->id,
            'requester_id' => Contact::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('service-requests.show', $request));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('service-requests/show')
            ->has('request')
        );
    }

    public function test_edit_displays_form(): void
    {
        $request = ServiceRequest::factory()->create([
            'category_id' => ServiceRequestCategory::factory()->create()->id,
            'requester_id' => Contact::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('service-requests.edit', $request));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('service-requests/edit')
            ->has('request')
        );
    }

    public function test_store_creates_service_request(): void
    {
        $category = ServiceRequestCategory::factory()->create();

        $data = [
            'category_id' => $category->id,
            'requester_type' => 'Owner',
            'title' => 'Test Request',
            'description' => 'Test Description',
            'priority' => 'medium',
        ];

        $response = $this->actingAs($this->user)->post(route('service-requests.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_requests', [
            'title' => 'Test Request',
            'requester_id' => $this->user->id,
        ]);
    }

    public function test_update_modifies_service_request(): void
    {
        $request = ServiceRequest::factory()->create([
            'category_id' => ServiceRequestCategory::factory()->create()->id,
            'requester_id' => $this->user->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($this->user)->put(
            route('service-requests.update', $request),
            ['title' => 'Updated Title']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('service_requests', [
            'id' => $request->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_destroy_deletes_service_request(): void
    {
        $request = ServiceRequest::factory()->create([
            'category_id' => ServiceRequestCategory::factory()->create()->id,
            'requester_id' => Contact::factory()->create()->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('service-requests.destroy', $request));

        $response->assertRedirect();
        $this->assertSoftDeleted('service_requests', ['id' => $request->id]);
    }
}
