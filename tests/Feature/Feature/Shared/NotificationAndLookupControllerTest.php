<?php

namespace Tests\Feature\Feature\Shared;

use App\Models\AccountMembership;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NotificationAndLookupControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Shared Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    private function notifyUser(User $user, string $text): void
    {
        $user->notify(new class($text) extends Notification
        {
            public function __construct(private readonly string $text) {}

            public function via(object $notifiable): array
            {
                return ['database'];
            }

            public function toDatabase(object $notifiable): array
            {
                return ['text' => $this->text];
            }
        });
    }

    /**
     * @return array{0: LeadSource, 1: Status}
     */
    private function leadDependencies(): array
    {
        $source = LeadSource::factory()->create([
            'name_en' => 'Website',
        ]);

        $status = Status::factory()->create([
            'type' => 'lead',
            'name_en' => 'New',
        ]);

        return [$source, $status];
    }

    public function test_notifications_page_renders_and_mark_as_read_updates_record(): void
    {
        [$user, $tenant] = $this->authenticateUser();
        $this->notifyUser($user, 'Document approval pending');

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('notifications.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('notifications/Index')
                ->has('notifications.data', 1)
                ->where('unreadCount', 1)
            );

        $notification = $user->notifications()->firstOrFail();

        $markResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('notifications.mark-as-read', $notification->id));

        $markResponse
            ->assertOk()
            ->assertJsonPath('data.id', $notification->id);

        $this->assertNotNull($notification->fresh()?->read_at);
    }

    public function test_mark_all_as_read_clears_unread_count(): void
    {
        [$user, $tenant] = $this->authenticateUser();
        $this->notifyUser($user, 'Lead assigned to you');
        $this->notifyUser($user, 'Visit rescheduled');

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson(route('notifications.mark-all-as-read'));

        $response
            ->assertOk()
            ->assertJsonPath('data.count', 2);

        $unreadCount = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('notifications.unread-count'));

        $unreadCount
            ->assertOk()
            ->assertJsonPath('data.count', 0);
    }

    public function test_lookup_endpoints_return_expected_contract_shapes(): void
    {
        [, $tenant] = $this->authenticateUser();
        [$source, $status] = $this->leadDependencies();

        Lead::create([
            'name' => 'Demo Lead',
            'phone_number' => '0555555555',
            'email' => 'lead@example.com',
            'source_id' => $source->id,
            'status_id' => $status->id,
            'interested' => 'sale',
            'account_tenant_id' => $tenant->id,
        ]);

        $modules = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.modules'));

        $modules
            ->assertOk()
            ->assertJsonCount(7, 'data')
            ->assertJsonStructure([
                'data' => [['id', 'title', 'is_active']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $leads = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leads'));

        $leads
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Demo Lead')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'phone_number',
                    'status' => ['id', 'value'],
                    'source' => ['id', 'value'],
                ]],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);

        $statuses = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.statuses'));

        $statuses
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }
}
