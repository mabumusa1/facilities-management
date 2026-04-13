<?php

namespace Tests\Feature;

use App\Models\Lease;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\LeaseExpiringNotification;
use App\Notifications\ServiceRequestCreatedNotification;
use App\Notifications\ServiceRequestStatusChangedNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected User $user;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'contact_type' => 'admin',
            'notification_preferences' => null,
        ]);
        $this->notificationService = new NotificationService;
    }

    public function test_notifications_page_is_accessible_for_authenticated_user(): void
    {
        $this->withoutVite();

        $response = $this->actingAs($this->user)->get('/notifications');

        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('notifications/index')
            ->has('notifications')
            ->has('statistics')
            ->has('preferences')
        );
    }

    public function test_notifications_page_requires_authentication(): void
    {
        $response = $this->get('/notifications');

        $response->assertRedirect('/login');
    }

    public function test_api_list_returns_notifications(): void
    {
        $this->createTestNotification();

        $response = $this->actingAs($this->user)->get('/api/notifications');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'unread_count',
        ]);
    }

    public function test_api_unread_returns_only_unread_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification(true);

        $response = $this->actingAs($this->user)->get('/api/notifications/unread');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'notifications',
            'count',
        ]);
        $this->assertEquals(1, $response->json('count'));
    }

    public function test_api_unread_count_returns_count(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();

        $response = $this->actingAs($this->user)->get('/api/notifications/unread-count');

        $response->assertStatus(200);
        $response->assertJsonStructure(['count']);
        $this->assertEquals(2, $response->json('count'));
    }

    public function test_api_mark_as_read_marks_notification(): void
    {
        $notification = $this->createTestNotification();

        $response = $this->actingAs($this->user)->post("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_api_mark_as_read_fails_for_other_users_notification(): void
    {
        $otherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $notification = $this->createTestNotification(false, $otherUser);

        $response = $this->actingAs($this->user)->post("/api/notifications/{$notification->id}/read");

        $response->assertStatus(403);
    }

    public function test_api_mark_all_as_read_marks_all_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();
        $this->createTestNotification();

        $this->assertEquals(3, $this->user->unreadNotifications()->count());

        $response = $this->actingAs($this->user)->post('/api/notifications/read-all');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_api_delete_notification(): void
    {
        $notification = $this->createTestNotification();

        $response = $this->actingAs($this->user)->delete("/api/notifications/{$notification->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_api_delete_fails_for_other_users_notification(): void
    {
        $otherUser = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $notification = $this->createTestNotification(false, $otherUser);

        $response = $this->actingAs($this->user)->delete("/api/notifications/{$notification->id}");

        $response->assertStatus(403);
    }

    public function test_api_delete_all_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();
        $this->createTestNotification();

        $this->assertEquals(3, $this->user->notifications()->count());

        $response = $this->actingAs($this->user)->delete('/api/notifications');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, $this->user->notifications()->count());
    }

    public function test_api_get_preferences(): void
    {
        $response = $this->actingAs($this->user)->get('/api/notifications/preferences');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'preferences' => [
                'email_lease_expiring',
                'email_service_request',
                'email_payment_reminder',
                'push_lease_expiring',
                'push_service_request',
                'push_payment_reminder',
                'inapp_enabled',
            ],
        ]);
    }

    public function test_api_update_preferences(): void
    {
        $response = $this->actingAs($this->user)->put('/api/notifications/preferences', [
            'email_lease_expiring' => false,
            'push_service_request' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'preferences' => [
                'email_lease_expiring' => false,
                'push_service_request' => false,
            ],
        ]);

        $this->user->refresh();
        $this->assertFalse($this->user->notification_preferences['email_lease_expiring']);
        $this->assertFalse($this->user->notification_preferences['push_service_request']);
    }

    public function test_api_statistics_returns_correct_data(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();
        $this->createTestNotification(true);

        $response = $this->actingAs($this->user)->get('/api/notifications/statistics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total',
            'unread',
            'read',
            'today',
            'this_week',
        ]);

        $this->assertEquals(3, $response->json('total'));
        $this->assertEquals(2, $response->json('unread'));
        $this->assertEquals(1, $response->json('read'));
    }

    public function test_notification_service_get_user_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();

        $notifications = $this->notificationService->getUserNotifications($this->user);

        $this->assertCount(2, $notifications);
    }

    public function test_notification_service_get_unread_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification(true);

        $unread = $this->notificationService->getUnreadNotifications($this->user);

        $this->assertCount(1, $unread);
    }

    public function test_notification_service_get_unread_count(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();
        $this->createTestNotification(true);

        $count = $this->notificationService->getUnreadCount($this->user);

        $this->assertEquals(2, $count);
    }

    public function test_notification_service_mark_as_read(): void
    {
        $notification = $this->createTestNotification();

        $this->notificationService->markAsRead($notification);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_notification_service_mark_all_as_read(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();

        $this->notificationService->markAllAsRead($this->user);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    public function test_notification_service_delete_notification(): void
    {
        $notification = $this->createTestNotification();

        $this->notificationService->deleteNotification($notification);

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_notification_service_delete_all_notifications(): void
    {
        $this->createTestNotification();
        $this->createTestNotification();

        $this->notificationService->deleteAllNotifications($this->user);

        $this->assertEquals(0, $this->user->notifications()->count());
    }

    public function test_notification_service_get_preferences_with_defaults(): void
    {
        $preferences = $this->notificationService->getNotificationPreferences($this->user);

        $this->assertTrue($preferences['email_lease_expiring']);
        $this->assertTrue($preferences['email_service_request']);
        $this->assertTrue($preferences['email_payment_reminder']);
        $this->assertTrue($preferences['push_lease_expiring']);
        $this->assertTrue($preferences['push_service_request']);
        $this->assertTrue($preferences['push_payment_reminder']);
        $this->assertTrue($preferences['inapp_enabled']);
    }

    public function test_notification_service_update_preferences(): void
    {
        $this->notificationService->updateNotificationPreferences($this->user, [
            'email_lease_expiring' => false,
        ]);

        $this->user->refresh();
        $preferences = $this->notificationService->getNotificationPreferences($this->user);

        $this->assertFalse($preferences['email_lease_expiring']);
        $this->assertTrue($preferences['email_service_request']);
    }

    public function test_notification_service_get_statistics(): void
    {
        $this->createTestNotification();
        $this->createTestNotification(true);

        $stats = $this->notificationService->getNotificationStatistics($this->user);

        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['unread']);
        $this->assertEquals(1, $stats['read']);
        $this->assertArrayHasKey('today', $stats);
        $this->assertArrayHasKey('this_week', $stats);
    }

    public function test_lease_expiring_notification_can_be_sent(): void
    {
        Notification::fake();

        // Create a mock lease with the required properties
        $lease = $this->createMockLease();

        $this->notificationService->sendLeaseExpiringNotification($lease, $this->user);

        Notification::assertSentTo($this->user, LeaseExpiringNotification::class);
    }

    public function test_service_request_created_notification_can_be_sent(): void
    {
        Notification::fake();

        $serviceRequest = $this->createMockServiceRequest();

        $this->notificationService->sendServiceRequestCreatedNotification($serviceRequest, $this->user);

        Notification::assertSentTo($this->user, ServiceRequestCreatedNotification::class);
    }

    public function test_service_request_status_changed_notification_can_be_sent(): void
    {
        Notification::fake();

        $serviceRequest = $this->createMockServiceRequest();

        $this->notificationService->sendServiceRequestStatusChangedNotification(
            $serviceRequest,
            $this->user,
            'open',
            'in_progress'
        );

        Notification::assertSentTo($this->user, ServiceRequestStatusChangedNotification::class);
    }

    public function test_lease_expiring_notification_contains_correct_data(): void
    {
        $lease = $this->createMockLease();

        $notification = new LeaseExpiringNotification($lease);
        $data = $notification->toArray($this->user);

        $this->assertEquals('lease_expiring', $data['type']);
        $this->assertEquals('Lease Expiring Soon', $data['title']);
        $this->assertEquals($lease->id, $data['lease_id']);
        $this->assertEquals($lease->unit_id, $data['unit_id']);
        $this->assertEquals('warning', $data['severity']);
    }

    public function test_service_request_created_notification_contains_correct_data(): void
    {
        $serviceRequest = $this->createMockServiceRequest();

        $notification = new ServiceRequestCreatedNotification($serviceRequest);
        $data = $notification->toArray($this->user);

        $this->assertEquals('service_request_created', $data['type']);
        $this->assertEquals('Service Request Created', $data['title']);
        $this->assertEquals($serviceRequest->id, $data['service_request_id']);
        $this->assertEquals('info', $data['severity']);
    }

    public function test_service_request_status_changed_notification_contains_correct_data(): void
    {
        $serviceRequest = $this->createMockServiceRequest();

        $notification = new ServiceRequestStatusChangedNotification(
            $serviceRequest,
            'in_progress',
            'completed'
        );
        $data = $notification->toArray($this->user);

        $this->assertEquals('service_request_status_changed', $data['type']);
        $this->assertEquals('Service Request Updated', $data['title']);
        $this->assertEquals($serviceRequest->id, $data['service_request_id']);
        $this->assertEquals('in_progress', $data['old_status']);
        $this->assertEquals('completed', $data['new_status']);
        $this->assertEquals('success', $data['severity']);
    }

    public function test_notifications_use_database_channel(): void
    {
        $lease = $this->createMockLease();

        $notification = new LeaseExpiringNotification($lease);
        $channels = $notification->via($this->user);

        $this->assertContains('database', $channels);
        $this->assertContains('mail', $channels);
    }

    protected function createMockLease(): Lease
    {
        // Create a mock lease object without using the factory
        $unit = new Unit([
            'id' => 1,
            'tenant_id' => $this->tenant->id,
            'unit_number' => 'A101',
        ]);
        $unit->exists = true;
        $unit->id = 1;

        $lease = new Lease([
            'id' => 1,
            'tenant_id' => $this->tenant->id,
            'unit_id' => 1,
            'end_date' => now()->addDays(30),
        ]);
        $lease->exists = true;
        $lease->id = 1;

        // Set the unit relationship manually
        $lease->setRelation('unit', $unit);

        return $lease;
    }

    protected function createMockServiceRequest(): ServiceRequest
    {
        $serviceRequest = new ServiceRequest([
            'id' => 1,
            'tenant_id' => $this->tenant->id,
            'priority' => 'high',
            'status' => 'open',
            'description' => 'Test request',
        ]);
        $serviceRequest->exists = true;
        $serviceRequest->id = 1;

        return $serviceRequest;
    }

    protected function createTestNotification(bool $read = false, ?User $user = null): DatabaseNotification
    {
        $user = $user ?? $this->user;

        $notification = DatabaseNotification::create([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'test',
                'title' => 'Test Notification',
                'message' => 'This is a test notification',
            ],
            'read_at' => $read ? now() : null,
        ]);

        return $notification;
    }
}
