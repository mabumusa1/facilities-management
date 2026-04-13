<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AnnouncementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_announcements_index_page_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get('/announcements')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('announcements/index')
                ->has('announcements')
                ->has('statistics')
                ->has('filters')
            );
    }

    public function test_announcements_index_shows_tenant_announcements(): void
    {
        $announcement = Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $otherTenant = Tenant::factory()->create();
        Announcement::factory()->create([
            'tenant_id' => $otherTenant->id,
            'created_by' => User::factory()->create(['tenant_id' => $otherTenant->id])->id,
        ]);

        $this->actingAs($this->user)
            ->get('/announcements')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('announcements/index')
                ->has('announcements.data', 1)
                ->where('announcements.data.0.id', $announcement->id)
            );
    }

    public function test_announcements_can_be_filtered_by_status(): void
    {
        Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'status' => 'draft',
        ]);

        Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get('/announcements?status=draft')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('announcements.data', 1)
                ->where('announcements.data.0.status', 'draft')
            );
    }

    public function test_announcements_create_page_can_be_rendered(): void
    {
        $this->actingAs($this->user)
            ->get('/announcements/create')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('announcements/create')
            );
    }

    public function test_announcement_can_be_created(): void
    {
        $data = [
            'title' => 'Test Announcement',
            'description' => 'This is a test announcement description.',
            'start_date' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_date' => now()->addDays(7)->format('Y-m-d'),
            'end_time' => '17:00',
            'is_visible' => true,
            'priority' => 'normal',
            'notify_user_types' => ['tenant', 'owner'],
        ];

        $this->actingAs($this->user)
            ->post('/announcements', $data)
            ->assertRedirect();

        $this->assertDatabaseHas('announcements', [
            'title' => 'Test Announcement',
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_announcement_creation_requires_valid_data(): void
    {
        $this->actingAs($this->user)
            ->post('/announcements', [])
            ->assertSessionHasErrors(['title', 'description', 'start_date', 'start_time', 'end_date', 'end_time']);
    }

    public function test_announcement_show_page_can_be_rendered(): void
    {
        $announcement = Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get("/announcements/{$announcement->id}")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('announcements/show')
                ->has('announcement')
                ->where('announcement.id', $announcement->id)
            );
    }

    public function test_announcement_edit_page_can_be_rendered(): void
    {
        $announcement = Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get("/announcements/{$announcement->id}/edit")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('announcements/edit')
                ->has('announcement')
                ->where('announcement.id', $announcement->id)
            );
    }

    public function test_announcement_can_be_updated(): void
    {
        $announcement = Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'title' => 'Original Title',
        ]);

        $this->actingAs($this->user)
            ->put("/announcements/{$announcement->id}", [
                'title' => 'Updated Title',
                'description' => 'Updated description',
                'start_date' => now()->format('Y-m-d'),
                'start_time' => '10:00',
                'end_date' => now()->addDays(14)->format('Y-m-d'),
                'end_time' => '18:00',
                'priority' => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'title' => 'Updated Title',
            'priority' => 'high',
        ]);
    }

    public function test_announcement_can_be_deleted(): void
    {
        $announcement = Announcement::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->delete("/announcements/{$announcement->id}")
            ->assertRedirect('/announcements');

        $this->assertSoftDeleted('announcements', [
            'id' => $announcement->id,
        ]);
    }

    public function test_draft_announcement_can_be_published(): void
    {
        $announcement = Announcement::factory()->draft()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(7),
        ]);

        $this->actingAs($this->user)
            ->post("/announcements/{$announcement->id}/publish")
            ->assertRedirect();

        $announcement->refresh();
        $this->assertEquals('active', $announcement->status);
    }

    public function test_active_announcement_can_be_cancelled(): void
    {
        $announcement = Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->post("/announcements/{$announcement->id}/cancel")
            ->assertRedirect();

        $announcement->refresh();
        $this->assertEquals('cancelled', $announcement->status);
    }

    public function test_directory_page_can_be_rendered(): void
    {
        Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->get('/directory')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('directory/index')
                ->has('announcements')
            );
    }

    public function test_directory_only_shows_active_visible_announcements(): void
    {
        Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'is_visible' => true,
        ]);

        Announcement::factory()->draft()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
            'is_visible' => false,
        ]);

        $this->actingAs($this->user)
            ->get('/directory')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('announcements', 1)
            );
    }

    // API Tests

    public function test_api_list_returns_announcements(): void
    {
        Announcement::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/announcements')
            ->assertOk()
            ->assertJsonStructure([
                'announcements' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'status', 'priority'],
                    ],
                ],
            ]);
    }

    public function test_api_active_returns_only_active_announcements(): void
    {
        Announcement::factory()->active()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Announcement::factory()->draft()->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/announcements/active')
            ->assertOk();

        $this->assertCount(1, $response->json('announcements'));
    }

    public function test_api_statistics_returns_correct_counts(): void
    {
        Announcement::factory()->draft()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        Announcement::factory()->active()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/announcements/statistics')
            ->assertOk()
            ->assertJson([
                'total' => 5,
                'draft' => 2,
                'active' => 3,
            ]);
    }
}
