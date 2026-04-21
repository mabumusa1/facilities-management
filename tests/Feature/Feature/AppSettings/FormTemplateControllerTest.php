<?php

namespace Tests\Feature\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\FormTemplate;
use App\Models\RequestCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FormTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Forms Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    /**
     * @return array{0: RequestCategory, 1: Community, 2: Building}
     */
    private function formDependencies(int $tenantId): array
    {
        $category = RequestCategory::factory()->create([
            'name_en' => 'Maintenance',
            'status' => true,
        ]);

        $community = Community::factory()->create([
            'name' => 'Blue Waters',
            'account_tenant_id' => $tenantId,
        ]);

        $building = Building::factory()->create([
            'name' => 'Tower A',
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenantId,
        ]);

        return [$category, $community, $building];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function schemaPayload(): array
    {
        return [
            'fields' => [
                ['key' => 'full_name', 'label' => 'Full Name', 'type' => 'text', 'required' => true],
                ['key' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'required' => false],
            ],
        ];
    }

    public function test_store_creates_form_template_and_redirects_to_preview(): void
    {
        $tenant = $this->authenticateUser();
        [$category, $community, $building] = $this->formDependencies($tenant->id);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('settings.forms.store'), [
                'name' => 'Visitor Request Form',
                'description' => 'Used by front desk.',
                'request_category_id' => $category->id,
                'community_id' => $community->id,
                'building_id' => $building->id,
                'schema' => $this->schemaPayload(),
                'is_active' => true,
            ]);

        $template = FormTemplate::query()->first();

        $response->assertRedirect(route('settings.forms.preview', $template));
        $this->assertDatabaseHas('rf_form_templates', [
            'id' => $template?->id,
            'name' => 'Visitor Request Form',
            'community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('settings.forms.preview', $template))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/forms/Preview')
                ->where('template.name', 'Visitor Request Form')
                ->has('requiredFields', 1)
            );
    }

    public function test_select_building_filters_buildings_by_selected_community(): void
    {
        $tenant = $this->authenticateUser();
        [, $community] = $this->formDependencies($tenant->id);

        $otherCommunity = Community::factory()->create([
            'name' => 'Green Village',
            'account_tenant_id' => $tenant->id,
        ]);

        Building::factory()->create([
            'name' => 'Other Tower',
            'rf_community_id' => $otherCommunity->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('settings.forms.select-building', ['community_id' => $community->id]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('app-settings/settings/forms/SelectBuilding')
                ->where('selectedCommunityId', $community->id)
                ->has('buildings', 1)
                ->where('buildings.0.rf_community_id', $community->id)
            );
    }

    public function test_destroy_removes_template_record(): void
    {
        $tenant = $this->authenticateUser();
        [$category, $community, $building] = $this->formDependencies($tenant->id);

        $template = FormTemplate::create([
            'name' => 'Delete Me',
            'request_category_id' => $category->id,
            'community_id' => $community->id,
            'building_id' => $building->id,
            'schema' => ['fields' => []],
            'is_active' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('settings.forms.destroy', $template));

        $response->assertRedirect(route('settings.forms.index'));
        $this->assertDatabaseMissing('rf_form_templates', [
            'id' => $template->id,
        ]);
    }
}
