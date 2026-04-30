<?php

namespace Database\Seeders;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * RbacSeeder — seeds the 192 system-wide permissions and 12 default roles.
 *
 * ## Permission matrix
 * 32 PermissionSubject cases × 6 PermissionAction cases = 192 permissions.
 * All system-wide permissions have account_tenant_id = NULL.
 *
 * ## Role presets (permission assignment rationale)
 *
 * UserRole defaults:
 *   - accountAdmins : ALL 186 permissions — full platform control for the account owner.
 *   - admins        : All subjects except companyProfile, invoiceSettings, leaseSettings
 *                     (billing/legal config kept for account owner only).
 *   - managers      : Operational subjects only; excludes admin/settings subjects.
 *   - owners        : Property-owner self-service: VIEW+CREATE+UPDATE on their own resources.
 *   - tenants       : Resident self-service: VIEW+CREATE on day-to-day subjects.
 *   - dependents    : Read-only access to shared amenities and announcements.
 *   - professionals : VIEW+UPDATE on service-related subjects (handling work orders).
 *
 * AdminRole defaults (back-office staff accounts):
 *   - Admins                  : ALL 186 permissions (mirror of accountAdmins for back-office).
 *   - accountingManagers      : Finance-related subjects only, all actions.
 *   - serviceManagers         : Service/maintenance subjects only, all actions.
 *   - marketingManagers       : Marketplace and marketing subjects only, all actions.
 *   - salesAndLeasingManagers : Leasing pipeline subjects only, all actions.
 *
 * ## Arabic translation map
 * TODO: Arabic review — all name_ar values below are literal/translated by map and must
 * be reviewed by a native Arabic speaker before this seeder merges to production.
 */
class RbacSeeder extends Seeder
{
    /** @var array<string, string> Arabic noun labels keyed by PermissionSubject::value */
    private const SUBJECT_AR = [
        'communities' => 'المجتمعات',
        'buildings' => 'المباني',
        'units' => 'الوحدات',
        'leases' => 'عقود الإيجار',
        'subLeases' => 'عقود الإيجار الفرعية',
        'transactions' => 'المعاملات',
        'payments' => 'المدفوعات',
        'owners' => 'الملاك',
        'tenants' => 'المستأجرون',
        'dependents' => 'المعالون',
        'admins' => 'المسؤولون',
        'professionals' => 'المهنيون',
        'homeServices' => 'الخدمات المنزلية',
        'neighbourhoodServices' => 'خدمات الحي',
        'visitorAccess' => 'دخول الزوار',
        'facilityBookings' => 'حجوزات المرافق',
        'managerRequests' => 'طلبات المدير',
        'facilities' => 'المرافق',
        'announcements' => 'الإعلانات',
        'directories' => 'الأدلة',
        'suggestions' => 'الاقتراحات',
        'complaints' => 'الشكاوى',
        'marketPlaces' => 'الأسواق',
        'marketPlaceBookings' => 'حجوزات الأسواق',
        'marketPlaceVisits' => 'زيارات الأسواق',
        'offerRequests' => 'طلبات العروض',
        'reports' => 'التقارير',
        'settings' => 'الإعدادات',
        'companyProfile' => 'ملف الشركة',
        'invoiceSettings' => 'إعدادات الفواتير',
        'leaseSettings' => 'إعدادات الإيجار',
        'leads' => 'العملاء المحتملون',
    ];

    /** @var array<string, string> Arabic verb labels keyed by PermissionAction::value */
    private const ACTION_AR = [
        'VIEW' => 'عرض',
        'CREATE' => 'إنشاء',
        'UPDATE' => 'تعديل',
        'APPROVE' => 'موافقة',
        'DELETE' => 'حذف',
        'RESTORE' => 'استعادة',
        'FORCE_DELETE' => 'حذف نهائي',
    ];

    /** @var array<string, string> English verb labels keyed by PermissionAction::value */
    private const ACTION_EN = [
        'VIEW' => 'View',
        'CREATE' => 'Create',
        'UPDATE' => 'Update',
        'APPROVE' => 'Approve',
        'DELETE' => 'Delete',
        'RESTORE' => 'Restore',
        'FORCE_DELETE' => 'Force Delete',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function () {
            $allNames = $this->seedPermissions();
            $this->deleteOrphanedSystemPermissions($allNames);
            $this->seedRoles();
            $this->syncRolePermissions();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /** @return list<string> All 186 permission names created/updated */
    private function seedPermissions(): array
    {
        $allNames = [];

        foreach (PermissionSubject::cases() as $subject) {
            foreach (PermissionAction::cases() as $action) {
                $name = "{$subject->value}.{$action->value}";
                $allNames[] = $name;

                Permission::withoutGlobalScopes()->updateOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'subject' => $subject->value,
                        'action' => $action->value,
                        'name_en' => $this->makeNameEn($subject, $action),
                        'name_ar' => $this->makeNameAr($subject, $action),
                        'account_tenant_id' => null,
                    ]
                );
            }
        }

        return $allNames;
    }

    /** @param list<string> $allNames */
    private function deleteOrphanedSystemPermissions(array $allNames): void
    {
        Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereNotIn('name', $allNames)
            ->delete();
    }

    private function seedRoles(): void
    {
        $roles = [
            // UserRole defaults
            [
                'name' => 'accountAdmins',
                'type' => RoleType::UserRole,
                'name_en' => 'Account Admin',
                'name_ar' => 'مسؤول الحساب',
            ],
            [
                'name' => 'admins',
                'type' => RoleType::UserRole,
                'name_en' => 'Admin',
                'name_ar' => 'مسؤول',
            ],
            [
                'name' => 'managers',
                'type' => RoleType::UserRole,
                'name_en' => 'Manager',
                'name_ar' => 'مدير',
            ],
            [
                'name' => 'owners',
                'type' => RoleType::UserRole,
                'name_en' => 'Owner',
                'name_ar' => 'مالك',
            ],
            [
                'name' => 'tenants',
                'type' => RoleType::UserRole,
                'name_en' => 'Tenant',
                'name_ar' => 'مستأجر',
            ],
            [
                'name' => 'dependents',
                'type' => RoleType::UserRole,
                'name_en' => 'Dependent',
                'name_ar' => 'معال',
            ],
            [
                'name' => 'professionals',
                'type' => RoleType::UserRole,
                'name_en' => 'Professional',
                'name_ar' => 'مهني',
            ],
            // AdminRole defaults
            [
                'name' => 'Admins',
                'type' => RoleType::AdminRole,
                'name_en' => 'System Admin',
                'name_ar' => 'مسؤول النظام',
            ],
            [
                'name' => 'accountingManagers',
                'type' => RoleType::AdminRole,
                'name_en' => 'Accounting Manager',
                'name_ar' => 'مدير الحسابات',
            ],
            [
                'name' => 'serviceManagers',
                'type' => RoleType::AdminRole,
                'name_en' => 'Service Manager',
                'name_ar' => 'مدير الخدمات',
            ],
            [
                'name' => 'marketingManagers',
                'type' => RoleType::AdminRole,
                'name_en' => 'Marketing Manager',
                'name_ar' => 'مدير التسويق',
            ],
            [
                'name' => 'salesAndLeasingManagers',
                'type' => RoleType::AdminRole,
                'name_en' => 'Sales & Leasing Manager',
                'name_ar' => 'مدير المبيعات والإيجار',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::withoutGlobalScopes()->updateOrCreate(
                [
                    'name' => $roleData['name'],
                    'guard_name' => 'web',
                    'account_tenant_id' => null,
                ],
                [
                    'type' => $roleData['type'],
                    'name_en' => $roleData['name_en'],
                    'name_ar' => $roleData['name_ar'],
                ]
            );
        }
    }

    private function syncRolePermissions(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $allSubjects = PermissionSubject::cases();
        $allActions = PermissionAction::cases();

        $this->syncRole('accountAdmins', $allSubjects, $allActions);
        $this->grantSpecialPermissions('accountAdmins', ['transactions.SEND_RECEIPT']);

        $adminSubjects = array_filter(
            $allSubjects,
            fn (PermissionSubject $s) => ! in_array($s, [
                PermissionSubject::CompanyProfile,
                PermissionSubject::InvoiceSettings,
                PermissionSubject::LeaseSettings,
            ])
        );
        $this->syncRole('admins', array_values($adminSubjects), $allActions);
        $this->grantSpecialPermissions('admins', ['transactions.SEND_RECEIPT']);

        $managerSubjects = [
            PermissionSubject::Communities,
            PermissionSubject::Buildings,
            PermissionSubject::Units,
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::Owners,
            PermissionSubject::Tenants,
            PermissionSubject::Dependents,
            PermissionSubject::Professionals,
            PermissionSubject::FacilityBookings,
            PermissionSubject::ManagerRequests,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::Directories,
            PermissionSubject::Suggestions,
            PermissionSubject::Complaints,
            PermissionSubject::MarketPlaces,
            PermissionSubject::MarketPlaceBookings,
            PermissionSubject::MarketPlaceVisits,
            PermissionSubject::OfferRequests,
            PermissionSubject::Reports,
            PermissionSubject::VisitorAccess,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
            PermissionSubject::Leads,
        ];
        $this->syncRole('managers', $managerSubjects, $allActions);
        $this->grantSpecialPermissions('managers', ['transactions.SEND_RECEIPT']);

        $ownerSubjects = [
            PermissionSubject::Units,
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::FacilityBookings,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::MarketPlaces,
            PermissionSubject::MarketPlaceBookings,
            PermissionSubject::OfferRequests,
            PermissionSubject::Reports,
        ];
        $ownerActions = [
            PermissionAction::View,
            PermissionAction::Create,
            PermissionAction::Update,
        ];
        $this->syncRole('owners', $ownerSubjects, $ownerActions);

        $tenantSubjects = [
            PermissionSubject::Leases,
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::FacilityBookings,
            PermissionSubject::Facilities,
            PermissionSubject::Announcements,
            PermissionSubject::Directories,
            PermissionSubject::Suggestions,
            PermissionSubject::Complaints,
            PermissionSubject::MarketPlaces,
            PermissionSubject::VisitorAccess,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
        ];
        $tenantActions = [PermissionAction::View, PermissionAction::Create];
        $this->syncRole('tenants', $tenantSubjects, $tenantActions);

        $dependentSubjects = [
            PermissionSubject::Announcements,
            PermissionSubject::Facilities,
            PermissionSubject::FacilityBookings,
        ];
        $this->syncRole('dependents', $dependentSubjects, [PermissionAction::View]);

        $professionalSubjects = [
            PermissionSubject::ManagerRequests,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
        ];
        $professionalActions = [PermissionAction::View, PermissionAction::Update];
        $this->syncRole('professionals', $professionalSubjects, $professionalActions);

        // AdminRole presets
        $this->syncRole('Admins', $allSubjects, $allActions);
        $this->grantSpecialPermissions('Admins', ['transactions.SEND_RECEIPT']);

        $accountingSubjects = [
            PermissionSubject::Transactions,
            PermissionSubject::Payments,
            PermissionSubject::Leases,
            PermissionSubject::Reports,
            PermissionSubject::InvoiceSettings,
            PermissionSubject::LeaseSettings,
            PermissionSubject::Tenants,
            PermissionSubject::Owners,
        ];
        $this->syncRole('accountingManagers', $accountingSubjects, $allActions);
        $this->grantSpecialPermissions('accountingManagers', ['transactions.SEND_RECEIPT']);

        $serviceSubjects = [
            PermissionSubject::ManagerRequests,
            PermissionSubject::HomeServices,
            PermissionSubject::NeighbourhoodServices,
            PermissionSubject::Facilities,
            PermissionSubject::FacilityBookings,
            PermissionSubject::Professionals,
            PermissionSubject::Suggestions,
            PermissionSubject::Complaints,
        ];
        $this->syncRole('serviceManagers', $serviceSubjects, $allActions);

        $marketingSubjects = [
            PermissionSubject::MarketPlaces,
            PermissionSubject::MarketPlaceBookings,
            PermissionSubject::MarketPlaceVisits,
            PermissionSubject::OfferRequests,
            PermissionSubject::Announcements,
            PermissionSubject::Directories,
        ];
        $this->syncRole('marketingManagers', $marketingSubjects, $allActions);

        $salesSubjects = [
            PermissionSubject::Leases,
            PermissionSubject::SubLeases,
            PermissionSubject::Units,
            PermissionSubject::Owners,
            PermissionSubject::Tenants,
            PermissionSubject::Transactions,
            PermissionSubject::Reports,
            PermissionSubject::LeaseSettings,
            PermissionSubject::Leads,
        ];
        $this->syncRole('salesAndLeasingManagers', $salesSubjects, $allActions);
        $this->grantSpecialPermissions('salesAndLeasingManagers', ['transactions.SEND_RECEIPT']);
    }

    /**
     * Grant additional named permissions to a role that are not part of the
     * standard subject × action cartesian matrix (e.g. transactions.SEND_RECEIPT).
     *
     * @param  list<string>  $permissionNames
     */
    private function grantSpecialPermissions(string $roleName, array $permissionNames): void
    {
        $role = Role::withoutGlobalScopes()
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $permissions = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereIn('name', $permissionNames)
            ->get();

        $role->givePermissionTo($permissions);
    }

    /**
     * @param  list<PermissionSubject>  $subjects
     * @param  list<PermissionAction>  $actions
     */
    private function syncRole(string $roleName, array $subjects, array $actions): void
    {
        $role = Role::withoutGlobalScopes()
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $permissionNames = [];
        foreach ($subjects as $subject) {
            foreach ($actions as $action) {
                $permissionNames[] = "{$subject->value}.{$action->value}";
            }
        }

        $permissions = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereIn('name', $permissionNames)
            ->get();

        $role->syncPermissions($permissions);
    }

    private function makeNameEn(PermissionSubject $subject, PermissionAction $action): string
    {
        $verb = self::ACTION_EN[$action->value];
        $noun = ucfirst(preg_replace('/(?<!^)([A-Z])/', ' $1', $subject->value) ?? $subject->value);

        return "{$verb} {$noun}";
    }

    private function makeNameAr(PermissionSubject $subject, PermissionAction $action): string
    {
        $verb = self::ACTION_AR[$action->value];
        $noun = self::SUBJECT_AR[$subject->value] ?? $subject->value;

        return "{$verb} {$noun}";
    }
}
