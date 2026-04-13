<?php

namespace Tests\Feature;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use Tests\TestCase;

class PermissionActionsSubjectsTest extends TestCase
{
    public function test_permission_action_has_all_expected_values(): void
    {
        $actions = PermissionAction::cases();

        $this->assertCount(7, $actions);
        $this->assertContains(PermissionAction::View, $actions);
        $this->assertContains(PermissionAction::Create, $actions);
        $this->assertContains(PermissionAction::Edit, $actions);
        $this->assertContains(PermissionAction::Delete, $actions);
        $this->assertContains(PermissionAction::Manage, $actions);
        $this->assertContains(PermissionAction::Close, $actions);
        $this->assertContains(PermissionAction::Assign, $actions);
    }

    public function test_permission_action_labels(): void
    {
        $this->assertEquals('View', PermissionAction::View->label());
        $this->assertEquals('Create', PermissionAction::Create->label());
        $this->assertEquals('Edit', PermissionAction::Edit->label());
        $this->assertEquals('Delete', PermissionAction::Delete->label());
        $this->assertEquals('Manage', PermissionAction::Manage->label());
        $this->assertEquals('Close', PermissionAction::Close->label());
        $this->assertEquals('Assign', PermissionAction::Assign->label());
    }

    public function test_permission_action_arabic_labels(): void
    {
        $this->assertEquals('عرض', PermissionAction::View->labelAr());
        $this->assertEquals('إنشاء', PermissionAction::Create->labelAr());
        $this->assertEquals('تعديل', PermissionAction::Edit->labelAr());
        $this->assertEquals('حذف', PermissionAction::Delete->labelAr());
        $this->assertEquals('إدارة', PermissionAction::Manage->labelAr());
        $this->assertEquals('إغلاق', PermissionAction::Close->labelAr());
        $this->assertEquals('تعيين', PermissionAction::Assign->labelAr());
    }

    public function test_permission_action_destructive_check(): void
    {
        $this->assertTrue(PermissionAction::Delete->isDestructive());
        $this->assertFalse(PermissionAction::View->isDestructive());
        $this->assertFalse(PermissionAction::Create->isDestructive());
        $this->assertFalse(PermissionAction::Edit->isDestructive());
        $this->assertFalse(PermissionAction::Manage->isDestructive());
    }

    public function test_permission_action_readonly_check(): void
    {
        $this->assertTrue(PermissionAction::View->isReadOnly());
        $this->assertFalse(PermissionAction::Create->isReadOnly());
        $this->assertFalse(PermissionAction::Edit->isReadOnly());
        $this->assertFalse(PermissionAction::Delete->isReadOnly());
        $this->assertFalse(PermissionAction::Manage->isReadOnly());
    }

    public function test_permission_action_crud_returns_four_actions(): void
    {
        $crud = PermissionAction::crud();

        $this->assertCount(4, $crud);
        $this->assertContains(PermissionAction::View, $crud);
        $this->assertContains(PermissionAction::Create, $crud);
        $this->assertContains(PermissionAction::Edit, $crud);
        $this->assertContains(PermissionAction::Delete, $crud);
        $this->assertNotContains(PermissionAction::Manage, $crud);
    }

    public function test_permission_action_values(): void
    {
        $values = PermissionAction::values();

        $this->assertContains('view', $values);
        $this->assertContains('create', $values);
        $this->assertContains('edit', $values);
        $this->assertContains('delete', $values);
        $this->assertContains('manage', $values);
        $this->assertContains('close', $values);
        $this->assertContains('assign', $values);
    }

    public function test_permission_subject_has_all_expected_values(): void
    {
        $subjects = PermissionSubject::cases();

        $this->assertCount(27, $subjects);
    }

    public function test_permission_subject_properties_module(): void
    {
        $this->assertEquals('properties', PermissionSubject::Communities->module());
        $this->assertEquals('properties', PermissionSubject::Buildings->module());
        $this->assertEquals('properties', PermissionSubject::Units->module());
        $this->assertEquals('properties', PermissionSubject::Facilities->module());
    }

    public function test_permission_subject_contacts_module(): void
    {
        $this->assertEquals('contacts', PermissionSubject::Owners->module());
        $this->assertEquals('contacts', PermissionSubject::Tenants->module());
        $this->assertEquals('contacts', PermissionSubject::Admins->module());
        $this->assertEquals('contacts', PermissionSubject::Professionals->module());
    }

    public function test_permission_subject_leasing_module(): void
    {
        $this->assertEquals('leasing', PermissionSubject::Leases->module());
        $this->assertEquals('leasing', PermissionSubject::SubLeases->module());
        $this->assertEquals('leasing', PermissionSubject::Quotes->module());
        $this->assertEquals('leasing', PermissionSubject::Applications->module());
    }

    public function test_permission_subject_transactions_module(): void
    {
        $this->assertEquals('transactions', PermissionSubject::Transactions->module());
        $this->assertEquals('transactions', PermissionSubject::Invoices->module());
        $this->assertEquals('transactions', PermissionSubject::Payments->module());
        $this->assertEquals('transactions', PermissionSubject::FinancialReports->module());
    }

    public function test_permission_subject_service_requests_module(): void
    {
        $this->assertEquals('service-requests', PermissionSubject::ServiceRequests->module());
        $this->assertEquals('service-requests', PermissionSubject::ServiceCategories->module());
    }

    public function test_permission_subject_operations_module(): void
    {
        $this->assertEquals('operations', PermissionSubject::VisitorAccess->module());
        $this->assertEquals('operations', PermissionSubject::FacilityBookings->module());
    }

    public function test_permission_subject_marketplace_module(): void
    {
        $this->assertEquals('marketplace', PermissionSubject::Marketplace->module());
        $this->assertEquals('marketplace', PermissionSubject::MarketplaceListings->module());
    }

    public function test_permission_subject_settings_module(): void
    {
        $this->assertEquals('settings', PermissionSubject::Settings->module());
        $this->assertEquals('settings', PermissionSubject::Announcements->module());
        $this->assertEquals('settings', PermissionSubject::Notifications->module());
        $this->assertEquals('settings', PermissionSubject::Reports->module());
        $this->assertEquals('settings', PermissionSubject::Users->module());
    }

    public function test_permission_subject_labels(): void
    {
        $this->assertEquals('Communities', PermissionSubject::Communities->label());
        $this->assertEquals('Buildings', PermissionSubject::Buildings->label());
        $this->assertEquals('Sub-leases', PermissionSubject::SubLeases->label());
        $this->assertEquals('Service Requests', PermissionSubject::ServiceRequests->label());
        $this->assertEquals('Financial Reports', PermissionSubject::FinancialReports->label());
    }

    public function test_permission_subject_applicable_actions_for_crud_subjects(): void
    {
        $communityActions = PermissionSubject::Communities->applicableActions();

        $this->assertContains(PermissionAction::View, $communityActions);
        $this->assertContains(PermissionAction::Create, $communityActions);
        $this->assertContains(PermissionAction::Edit, $communityActions);
        $this->assertContains(PermissionAction::Delete, $communityActions);
        $this->assertContains(PermissionAction::Manage, $communityActions);
    }

    public function test_permission_subject_service_requests_has_additional_actions(): void
    {
        $serviceRequestActions = PermissionSubject::ServiceRequests->applicableActions();

        $this->assertContains(PermissionAction::View, $serviceRequestActions);
        $this->assertContains(PermissionAction::Create, $serviceRequestActions);
        $this->assertContains(PermissionAction::Edit, $serviceRequestActions);
        $this->assertContains(PermissionAction::Delete, $serviceRequestActions);
        $this->assertContains(PermissionAction::Manage, $serviceRequestActions);
        $this->assertContains(PermissionAction::Close, $serviceRequestActions);
        $this->assertContains(PermissionAction::Assign, $serviceRequestActions);
    }

    public function test_permission_subject_readonly_subjects(): void
    {
        $reportActions = PermissionSubject::FinancialReports->applicableActions();

        $this->assertContains(PermissionAction::View, $reportActions);
        $this->assertNotContains(PermissionAction::Create, $reportActions);
        $this->assertNotContains(PermissionAction::Edit, $reportActions);
        $this->assertNotContains(PermissionAction::Delete, $reportActions);
    }

    public function test_permission_subject_settings_actions(): void
    {
        $settingsActions = PermissionSubject::Settings->applicableActions();

        $this->assertContains(PermissionAction::View, $settingsActions);
        $this->assertContains(PermissionAction::Edit, $settingsActions);
        $this->assertContains(PermissionAction::Manage, $settingsActions);
        $this->assertNotContains(PermissionAction::Create, $settingsActions);
        $this->assertNotContains(PermissionAction::Delete, $settingsActions);
    }

    public function test_permission_for_generates_correct_string(): void
    {
        $this->assertEquals(
            'view-communities',
            PermissionSubject::Communities->permissionFor(PermissionAction::View)
        );
        $this->assertEquals(
            'create-buildings',
            PermissionSubject::Buildings->permissionFor(PermissionAction::Create)
        );
        $this->assertEquals(
            'edit-leases',
            PermissionSubject::Leases->permissionFor(PermissionAction::Edit)
        );
        $this->assertEquals(
            'delete-transactions',
            PermissionSubject::Transactions->permissionFor(PermissionAction::Delete)
        );
        $this->assertEquals(
            'manage-users',
            PermissionSubject::Users->permissionFor(PermissionAction::Manage)
        );
    }

    public function test_all_permissions_for_subject(): void
    {
        $communityPermissions = PermissionSubject::Communities->allPermissions();

        $this->assertContains('view-communities', $communityPermissions);
        $this->assertContains('create-communities', $communityPermissions);
        $this->assertContains('edit-communities', $communityPermissions);
        $this->assertContains('delete-communities', $communityPermissions);
        $this->assertContains('manage-communities', $communityPermissions);
    }

    public function test_by_module_returns_correct_subjects(): void
    {
        $propertySubjects = PermissionSubject::byModule('properties');

        $this->assertContains(PermissionSubject::Communities, $propertySubjects);
        $this->assertContains(PermissionSubject::Buildings, $propertySubjects);
        $this->assertContains(PermissionSubject::Units, $propertySubjects);
        $this->assertContains(PermissionSubject::Facilities, $propertySubjects);
        $this->assertNotContains(PermissionSubject::Leases, $propertySubjects);
    }

    public function test_modules_returns_unique_list(): void
    {
        $modules = PermissionSubject::modules();

        $this->assertContains('properties', $modules);
        $this->assertContains('contacts', $modules);
        $this->assertContains('leasing', $modules);
        $this->assertContains('transactions', $modules);
        $this->assertContains('service-requests', $modules);
        $this->assertContains('operations', $modules);
        $this->assertContains('marketplace', $modules);
        $this->assertContains('settings', $modules);
    }

    public function test_permission_subject_values(): void
    {
        $values = PermissionSubject::values();

        $this->assertContains('communities', $values);
        $this->assertContains('buildings', $values);
        $this->assertContains('units', $values);
        $this->assertContains('leases', $values);
        $this->assertContains('sub-leases', $values);
        $this->assertContains('service-requests', $values);
    }

    public function test_permission_generation_consistency(): void
    {
        // Verify that all subjects generate valid permissions
        foreach (PermissionSubject::cases() as $subject) {
            $permissions = $subject->allPermissions();

            $this->assertIsArray($permissions);
            $this->assertNotEmpty($permissions);

            foreach ($permissions as $permission) {
                $this->assertIsString($permission);
                $this->assertStringContainsString('-', $permission);
                $this->assertStringContainsString($subject->value, $permission);
            }
        }
    }

    public function test_module_grouping_is_complete(): void
    {
        // Every subject should belong to a module
        foreach (PermissionSubject::cases() as $subject) {
            $module = $subject->module();
            $this->assertIsString($module);
            $this->assertNotEmpty($module);
        }
    }

    public function test_applicable_actions_are_valid(): void
    {
        // Every subject should have applicable actions
        foreach (PermissionSubject::cases() as $subject) {
            $actions = $subject->applicableActions();
            $this->assertIsArray($actions);
            $this->assertNotEmpty($actions);

            foreach ($actions as $action) {
                $this->assertInstanceOf(PermissionAction::class, $action);
            }
        }
    }
}
