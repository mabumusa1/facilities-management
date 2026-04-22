<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Community;
use App\Models\ExcelSheet;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\InvoiceSetting;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentDBatchGetEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent D Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_route_names_exist(): void
    {
        $expectedRoutes = [
            'rf.communities.off-plan-sale',
            'rf.company-profile',
            'rf.contacts.statistics',
            'rf.excel-sheets.index',
            'rf.facilities.index',
            'rf.invoices.index',
            'rf.leads.create',
            'rf.leases.index',
            'rf.leases.show',
            'rf.leases.create',
            'rf.leases.expiring',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_company_profile_contacts_statistics_and_lead_create_endpoints_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUser();

        InvoiceSetting::query()->create([
            'company_name' => 'Agent D Co',
            'logo' => null,
            'address' => 'Riyadh',
            'vat' => 15,
            'instructions' => 'Pay before due date.',
            'notes' => 'Thanks.',
            'vat_number' => 'VAT-123',
            'cr_number' => 'CR-456',
            'account_tenant_id' => $tenant->id,
        ]);

        SystemSetting::query()->create([
            'key' => 'bank-details',
            'payload' => [
                'beneficiary_name' => 'Agent D Beneficiary',
                'bank_name' => 'Agent D Bank',
                'account_number' => '1234567890',
                'iban' => 'SA1234567890123456789012',
            ],
            'account_tenant_id' => $tenant->id,
        ]);

        Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Owner::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Professional::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Lead::query()->create([
            'name' => 'Agent D Lead',
            'first_name' => 'Agent',
            'last_name' => 'Lead',
            'phone_number' => '0500000001',
            'email' => 'agent-d-lead@example.test',
            'account_tenant_id' => $tenant->id,
        ]);

        $leadStatus = Status::factory()->create([
            'type' => 'lead',
            'name' => 'New',
            'name_ar' => 'جديد',
            'name_en' => 'New',
        ]);

        $leadSource = LeadSource::factory()->create([
            'name' => 'Website',
            'name_ar' => 'الموقع',
            'name_en' => 'Website',
        ]);

        $companyProfileResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.company-profile'));

        $companyProfileResponse
            ->assertOk()
            ->assertJsonPath('data.company_name', 'Agent D Co')
            ->assertJsonPath('data.bank_name', 'Agent D Bank')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'company_name',
                    'logo',
                    'address',
                    'vat',
                    'vat_number',
                    'cr_number',
                    'instructions',
                    'notes',
                    'beneficiary_name',
                    'bank_name',
                    'account_number',
                    'iban',
                ],
            ]);

        $contactsStatisticsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.contacts.statistics'));

        $contactsStatisticsResponse
            ->assertOk()
            ->assertJsonPath('data.tenants', 1)
            ->assertJsonPath('data.owners', 1)
            ->assertJsonPath('data.professionals', 1)
            ->assertJsonPath('data.admins', 1)
            ->assertJsonPath('data.leads', 1)
            ->assertJsonPath('data.total_contacts', 5);

        $leadCreateResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leads.create'));

        $leadCreateResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'statuses',
                    'sources',
                    'priorities',
                ],
            ]);

        $statusIds = collect($leadCreateResponse->json('data.statuses'))->pluck('id')->all();
        $sourceIds = collect($leadCreateResponse->json('data.sources'))->pluck('id')->all();

        $this->assertContains($leadStatus->id, $statusIds);
        $this->assertContains($leadSource->id, $sourceIds);
    }

    public function test_rf_community_excel_facility_and_invoice_endpoints_return_paginated_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $offPlanCommunity = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Off Plan Community',
            'is_off_plan_sale' => true,
        ]);

        Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Regular Community',
            'is_off_plan_sale' => false,
        ]);

        $sheet = ExcelSheet::query()->create([
            'type' => 'general',
            'file_path' => '/storage/imports/excel/agent-d.xlsx',
            'file_name' => 'agent-d.xlsx',
            'status' => 'uploaded',
            'error_details' => null,
            'rf_community_id' => $offPlanCommunity->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $facilityCategory = FacilityCategory::factory()->create();

        $facility = Facility::query()->create([
            'category_id' => $facilityCategory->id,
            'community_id' => $offPlanCommunity->id,
            'name' => 'Pool',
            'name_ar' => 'مسبح',
            'name_en' => 'Pool',
            'description' => 'Community pool',
            'capacity' => 20,
            'open_time' => '08:00',
            'close_time' => '22:00',
            'booking_fee' => 50,
            'is_active' => true,
            'requires_approval' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        $invoiceStatus = Status::factory()->create([
            'type' => 'invoice',
            'name' => 'Pending',
            'name_ar' => 'قيد الانتظار',
            'name_en' => 'Pending',
        ]);

        $transactionCategory = Setting::factory()->create([
            'type' => 'transaction_category',
            'name' => 'Rent',
            'name_ar' => 'إيجار',
            'name_en' => 'Rent',
        ]);

        $transactionType = Setting::factory()->create([
            'type' => 'transaction_type',
            'name' => 'Invoice',
            'name_ar' => 'فاتورة',
            'name_en' => 'Invoice',
        ]);

        $transaction = Transaction::query()->create([
            'category_id' => $transactionCategory->id,
            'type_id' => $transactionType->id,
            'status_id' => $invoiceStatus->id,
            'amount' => 5000,
            'tax_amount' => 750,
            'due_on' => now()->addDays(14)->toDateString(),
            'details' => 'Agent D invoice',
            'is_paid' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        $offPlanSaleResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.communities.off-plan-sale'));

        $offPlanSaleResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'city',
                        'district',
                        'sales_commission_rate',
                        'rental_commission_rate',
                        'buildings_count',
                        'units_count',
                        'map',
                        'images',
                        'is_selected_property',
                        'count_selected_property',
                        'requests_count',
                        'total_income',
                        'is_market_place',
                        'is_buy',
                        'community_marketplace_type',
                        'is_off_plan_sale',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $offPlanIds = collect($offPlanSaleResponse->json('data'))->pluck('id')->all();

        $this->assertContains($offPlanCommunity->id, $offPlanIds);

        $excelSheetsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.excel-sheets.index'));

        $excelSheetsResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'type',
                        'file_path',
                        'file_name',
                        'status',
                        'error_details',
                        'rf_community_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $sheetIds = collect($excelSheetsResponse->json('data'))->pluck('id')->all();

        $this->assertContains($sheet->id, $sheetIds);

        $facilitiesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.facilities.index'));

        $facilitiesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                        'description',
                        'capacity',
                        'open_time',
                        'close_time',
                        'booking_fee',
                        'is_active',
                        'requires_approval',
                        'category',
                        'community',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $facilityIds = collect($facilitiesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($facility->id, $facilityIds);

        $invoicesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.invoices.index'));

        $invoicesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'amount',
                        'tax_amount',
                        'is_paid',
                        'due_on',
                        'details',
                        'lease',
                        'unit',
                        'status',
                        'category',
                        'subcategory',
                        'type',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $transactionIds = collect($invoicesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($transaction->id, $transactionIds);
    }

    public function test_rf_lease_index_show_create_and_expiring_endpoints_return_json_contract_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
            'first_name' => 'Lease',
            'last_name' => 'Tenant',
            'email' => 'lease-tenant@example.test',
            'phone_number' => '0501111111',
            'national_id' => '1111111111',
        ]);

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_ar' => 'نشط',
            'name_en' => 'Active',
        ]);

        $unitStatus = Status::factory()->create([
            'type' => 'unit',
            'name' => 'Available',
            'name_ar' => 'متاح',
            'name_en' => 'Available',
        ]);

        $leaseUnitType = UnitCategory::factory()->create([
            'name' => 'Residential',
            'name_ar' => 'سكني',
            'name_en' => 'Residential',
        ]);

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Standard',
            'name_ar' => 'قياسي',
            'name_en' => 'Standard',
        ]);

        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_ar' => 'شهري',
            'name_en' => 'Monthly',
        ]);

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
            'category_id' => $leaseUnitType->id,
            'status_id' => $unitStatus->id,
            'map' => ['lat' => 24.7136, 'lng' => 46.6753],
        ]);

        $lease = Lease::factory()->create([
            'account_tenant_id' => $tenant->id,
            'tenant_id' => $resident->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitType->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'contract_number' => 'LEASE-D-001',
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'handover_date' => now()->subDays(30)->toDateString(),
        ]);

        $lease->units()->attach($unit->id, [
            'rental_annual_type' => 'monthly',
            'annual_rental_amount' => 12000,
            'net_area' => 100,
            'meter_cost' => 25,
        ]);

        Lease::factory()->create([
            'account_tenant_id' => $tenant->id,
            'tenant_id' => $resident->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitType->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'contract_number' => 'LEASE-D-002',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->addDays(90)->toDateString(),
            'handover_date' => now()->subDays(5)->toDateString(),
        ]);

        $leasesIndexResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leases.index'));

        $leasesIndexResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'contract_number',
                        'lease_unit_type',
                        'tenant',
                        'units',
                        'status',
                        'start_date',
                        'end_date',
                        'handover_date',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $leaseIds = collect($leasesIndexResponse->json('data'))->pluck('id')->all();

        $this->assertContains($lease->id, $leaseIds);

        $leaseCreateResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leases.create'));

        $leaseCreateResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'tenants',
                    'statuses',
                    'unit_categories',
                    'units',
                    'admins',
                ],
                'specifications' => [
                    'rental_contract_type',
                    'payment_schedule',
                ],
            ]);

        $leaseShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leases.show', $lease));

        $leaseShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $lease->id)
            ->assertJsonPath('data.contract_number', 'LEASE-D-001')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'contract_number',
                    'tenant',
                    'units',
                    'lease_unit_type',
                    'status',
                    'rental_contract_type',
                    'payment_schedule',
                    'start_date',
                    'end_date',
                    'handover_date',
                    'rental_total_amount',
                    'security_deposit_amount',
                    'terms_conditions',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $literalLeaseShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/leases/'.$lease->id);

        $literalLeaseShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $lease->id);

        $expiringResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leases.expiring'));

        $expiringResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'contract_number',
                        'end_date',
                        'tenant',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $expiringLeaseIds = collect($expiringResponse->json('data'))->pluck('id')->all();

        $this->assertContains($lease->id, $expiringLeaseIds);
    }
}
