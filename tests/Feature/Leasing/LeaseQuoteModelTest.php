<?php

namespace Tests\Feature\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LeaseQuoteModelTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function authenticateUserWithTenant(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Leasing Test Account '.fake()->unique()->word()]);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return [$user, $tenant];
    }

    public function test_factory_creates_a_row_with_account_tenant_id_auto_populated(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        $quote = LeaseQuote::factory()->create();

        $this->assertModelExists($quote);
        $this->assertSame($tenant->id, $quote->account_tenant_id);
    }

    public function test_global_scope_filters_out_cross_tenant_rows(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        // Create a quote belonging to the active tenant.
        $ownQuote = LeaseQuote::factory()->create();

        // Create a second tenant and switch context to create its quote.
        $otherTenant = Tenant::create(['name' => 'Other Tenant '.fake()->unique()->word()]);
        $otherTenant->makeCurrent();

        LeaseQuote::factory()->create();

        // Switch back to the original tenant.
        $tenant->makeCurrent();

        $quotes = LeaseQuote::all();

        $this->assertCount(1, $quotes);
        $this->assertTrue($quotes->contains($ownQuote));
    }

    public function test_relationships_load_correctly(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        $unit = Unit::factory()->create(['account_tenant_id' => $tenant->id]);
        $contact = Resident::factory()->create(['account_tenant_id' => $tenant->id]);
        $status = Status::factory()->create(['type' => 'lease_quote', 'name_en' => 'draft']);
        $paymentFrequency = Setting::factory()->create(['type' => 'payment_frequency']);
        $createdBy = Admin::factory()->create(['account_tenant_id' => $tenant->id]);

        $quote = LeaseQuote::factory()->create([
            'unit_id' => $unit->id,
            'contact_id' => $contact->id,
            'status_id' => $status->id,
            'payment_frequency_id' => $paymentFrequency->id,
            'created_by_id' => $createdBy->id,
        ]);

        $fresh = LeaseQuote::with(['unit', 'contact', 'status'])->find($quote->id);

        $this->assertInstanceOf(Unit::class, $fresh->unit);
        $this->assertSame($unit->id, $fresh->unit->id);

        $this->assertInstanceOf(Resident::class, $fresh->contact);
        $this->assertSame($contact->id, $fresh->contact->id);

        $this->assertInstanceOf(Status::class, $fresh->status);
        $this->assertSame($status->id, $fresh->status->id);
    }

    public function test_expiry_command_transitions_non_terminal_quotes_past_valid_until_to_expired(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        $draftStatus = Status::factory()->create(['type' => 'lease_quote', 'name_en' => 'draft']);
        $sentStatus = Status::factory()->create(['type' => 'lease_quote', 'name_en' => 'sent']);
        $expiredStatus = Status::factory()->create(['type' => 'lease_quote', 'name_en' => 'expired']);
        $acceptedStatus = Status::factory()->create(['type' => 'lease_quote', 'name_en' => 'accepted']);

        // Non-terminal quote whose valid_until is in the past — should be expired.
        $staleQuote = LeaseQuote::factory()->create([
            'status_id' => $draftStatus->id,
            'valid_until' => now()->subDay(),
        ]);

        // Non-terminal quote still within validity — should remain unchanged.
        $activeQuote = LeaseQuote::factory()->create([
            'status_id' => $sentStatus->id,
            'valid_until' => now()->addDay(),
        ]);

        // Terminal quote (accepted) past valid_until — expiry must NOT touch it.
        $terminalQuote = LeaseQuote::factory()->create([
            'status_id' => $acceptedStatus->id,
            'valid_until' => now()->subDay(),
        ]);

        $this->artisan(ExpireLeaseQuotes::class)->assertSuccessful();

        $this->assertSame($expiredStatus->id, $staleQuote->fresh()->status_id);
        $this->assertSame($sentStatus->id, $activeQuote->fresh()->status_id);
        $this->assertSame($acceptedStatus->id, $terminalQuote->fresh()->status_id);
    }

    public function test_additional_charges_and_special_conditions_cast_to_array(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        $additionalCharges = [
            ['label' => ['en' => 'Maintenance', 'ar' => 'صيانة'], 'amount' => 500.00],
        ];
        $specialConditions = ['en' => 'No pets allowed', 'ar' => 'لا يسمح بالحيوانات الأليفة'];

        $quote = LeaseQuote::factory()->create([
            'additional_charges' => $additionalCharges,
            'special_conditions' => $specialConditions,
        ]);

        $fresh = $quote->fresh();

        $this->assertIsArray($fresh->additional_charges);
        $this->assertSame('Maintenance', $fresh->additional_charges[0]['label']['en']);

        $this->assertIsArray($fresh->special_conditions);
        $this->assertSame('No pets allowed', $fresh->special_conditions['en']);
    }

    public function test_revision_chain_parent_quote_relationship(): void
    {
        [, $tenant] = $this->authenticateUserWithTenant();
        $tenant->makeCurrent();

        $parentQuote = LeaseQuote::factory()->create(['version' => 1]);

        $revision = LeaseQuote::factory()->create([
            'parent_quote_id' => $parentQuote->id,
            'version' => 2,
        ]);

        $this->assertSame($parentQuote->id, $revision->parentQuote->id);
        $this->assertTrue($parentQuote->revisions->contains($revision));
    }
}
