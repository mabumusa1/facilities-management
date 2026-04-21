<?php

namespace Tests\Feature\Feature\Phase10;

use App\Models\AccountMembership;
use App\Models\Lease;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Notifications\Channels\PushChannel;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\WorkflowStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Phase 10 Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    public function test_request_status_workflow_blocks_invalid_transition(): void
    {
        [$user, $tenant] = $this->authenticateUser();

        $newStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'New',
            'priority' => 1,
        ]);

        $resolvedStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'Resolved',
            'priority' => 9,
        ]);

        $category = RequestCategory::factory()->create();
        $subcategory = RequestSubcategory::factory()->create(['category_id' => $category->id]);

        $serviceRequest = ServiceRequest::create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'status_id' => $newStatus->id,
            'requester_type' => $user::class,
            'requester_id' => $user->id,
            'title' => 'Workflow Request',
            'description' => 'Initial state',
            'priority' => 'high',
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('requests.update', $serviceRequest), [
                'status_id' => $resolvedStatus->id,
                'description' => 'Attempt invalid transition',
            ])
            ->assertSessionHasErrors(['status_id']);

        $this->assertSame($newStatus->id, (int) $serviceRequest->fresh()->status_id);
    }

    public function test_request_status_workflow_allows_valid_transition_and_sends_multichannel_notification(): void
    {
        [$user, $tenant] = $this->authenticateUser();

        Notification::fake();

        $newStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'New',
            'priority' => 1,
        ]);

        $assignedStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'Assigned',
            'priority' => 2,
        ]);

        $category = RequestCategory::factory()->create();
        $subcategory = RequestSubcategory::factory()->create(['category_id' => $category->id]);

        $serviceRequest = ServiceRequest::create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'status_id' => $newStatus->id,
            'requester_type' => $user::class,
            'requester_id' => $user->id,
            'title' => 'Workflow Request',
            'description' => 'Initial state',
            'priority' => 'high',
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('requests.update', $serviceRequest), [
                'status_id' => $assignedStatus->id,
                'description' => 'Valid transition',
            ])
            ->assertRedirect(route('requests.show', $serviceRequest));

        $this->assertSame($assignedStatus->id, (int) $serviceRequest->fresh()->status_id);

        Notification::assertSentTo(
            $user,
            WorkflowStatusChangedNotification::class,
            function (WorkflowStatusChangedNotification $notification, array $channels, object $notifiable) use ($serviceRequest): bool {
                return in_array('database', $channels, true)
                    && in_array('mail', $channels, true)
                    && in_array(SmsChannel::class, $channels, true)
                    && in_array(PushChannel::class, $channels, true)
                    && (int) data_get($notification->toDatabase($notifiable), 'resource_id') === $serviceRequest->id;
            },
        );
    }

    public function test_can_create_sublease_from_parent_lease(): void
    {
        [, $tenant] = $this->authenticateUser();

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name_en' => 'New Contract',
            'priority' => 1,
        ]);

        $parentLease = Lease::factory()->create([
            'status_id' => $leaseStatus->id,
            'account_tenant_id' => $tenant->id,
            'is_sub_lease' => false,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $parentLease->units()->sync([
            $unit->id => [
                'rental_annual_type' => 'annual',
                'annual_rental_amount' => 120000,
                'net_area' => 120,
                'meter_cost' => 15,
            ],
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('leases.subleases.create', $parentLease))
            ->assertOk();

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('leases.subleases.store', $parentLease), [
                'contract_number' => 'LC-SUB-2026-001',
                'tenant_id' => $parentLease->tenant_id,
                'status_id' => $leaseStatus->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'handover_date' => now()->toDateString(),
                'rental_total_amount' => 45000,
                'security_deposit_amount' => 5000,
                'legal_representative' => 'Sub Lease Contact',
            ])
            ->assertRedirect();

        $sublease = Lease::query()
            ->where('contract_number', 'LC-SUB-2026-001')
            ->first();

        $this->assertNotNull($sublease);
        $this->assertDatabaseHas('rf_leases', [
            'id' => $sublease?->id,
            'parent_lease_id' => $parentLease->id,
            'is_sub_lease' => true,
        ]);

        $this->assertTrue($sublease?->units()->where('rf_units.id', $unit->id)->exists() ?? false);
    }

    public function test_index_pages_apply_filters_and_per_page_controls(): void
    {
        [$user, $tenant] = $this->authenticateUser();

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name_en' => 'Active Contract',
            'priority' => 2,
        ]);

        $leaseOtherStatus = Status::factory()->create([
            'type' => 'lease',
            'name_en' => 'New Contract',
            'priority' => 1,
        ]);

        $leaseTenant = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'contract_number' => 'LEASE-ALPHA',
            'tenant_id' => $leaseTenant->id,
            'status_id' => $leaseStatus->id,
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'contract_number' => 'LEASE-BETA',
            'status_id' => $leaseOtherStatus->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('leases.index', [
                'search' => 'ALPHA',
                'status_id' => $leaseStatus->id,
                'tenant_id' => $leaseTenant->id,
                'per_page' => 5,
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('leasing/leases/Index')
                ->where('filters.search', 'ALPHA')
                ->where('filters.per_page', '5')
                ->where('leases.per_page', 5)
                ->has('leases.data', 1)
            );

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'New',
            'priority' => 1,
        ]);

        $requestOtherStatus = Status::factory()->create([
            'type' => 'request',
            'name_en' => 'Assigned',
            'priority' => 2,
        ]);

        $requestCategory = RequestCategory::factory()->create();
        $requestSubcategory = RequestSubcategory::factory()->create(['category_id' => $requestCategory->id]);

        ServiceRequest::create([
            'category_id' => $requestCategory->id,
            'subcategory_id' => $requestSubcategory->id,
            'status_id' => $requestStatus->id,
            'requester_type' => $user::class,
            'requester_id' => $user->id,
            'title' => 'Alpha Request',
            'description' => 'Needs service',
            'priority' => 'high',
            'account_tenant_id' => $tenant->id,
        ]);

        ServiceRequest::create([
            'category_id' => $requestCategory->id,
            'subcategory_id' => $requestSubcategory->id,
            'status_id' => $requestOtherStatus->id,
            'requester_type' => $user::class,
            'requester_id' => $user->id,
            'title' => 'Beta Request',
            'description' => 'Other service',
            'priority' => 'low',
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('requests.index', [
                'search' => 'Alpha',
                'status_id' => $requestStatus->id,
                'category_id' => $requestCategory->id,
                'priority' => 'high',
                'per_page' => 5,
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('requests/Index')
                ->where('filters.search', 'Alpha')
                ->where('filters.per_page', '5')
                ->where('requests.per_page', 5)
                ->has('requests.data', 1)
            );

        $invoiceStatus = Status::factory()->create([
            'type' => 'invoice',
            'name_en' => 'Pending',
            'priority' => 1,
        ]);

        $invoiceOtherStatus = Status::factory()->create([
            'type' => 'invoice',
            'name_en' => 'Paid',
            'priority' => 2,
        ]);

        $transactionCategory = Setting::factory()->create([
            'type' => 'transaction_category',
            'name_en' => 'Rent',
        ]);

        $transactionType = Setting::factory()->create([
            'type' => 'transaction_type',
            'name_en' => 'Invoice',
        ]);

        Transaction::factory()->create([
            'status_id' => $invoiceStatus->id,
            'category_id' => $transactionCategory->id,
            'type_id' => $transactionType->id,
            'details' => 'Alpha transaction',
            'is_paid' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::factory()->create([
            'status_id' => $invoiceOtherStatus->id,
            'category_id' => $transactionCategory->id,
            'type_id' => $transactionType->id,
            'details' => 'Beta transaction',
            'is_paid' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('transactions.index', [
                'search' => 'Alpha',
                'status_id' => $invoiceStatus->id,
                'category_id' => $transactionCategory->id,
                'is_paid' => 0,
                'per_page' => 5,
            ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('accounting/transactions/Index')
                ->where('filters.search', 'Alpha')
                ->where('filters.per_page', '5')
                ->where('transactions.per_page', 5)
                ->has('transactions.data', 1)
            );
    }
}
