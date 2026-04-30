<?php

namespace App\Actions;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConvertLeadToContact
{
    public const CONTACT_TYPE_OWNER = 'owner';

    public const CONTACT_TYPE_RESIDENT = 'resident';

    /**
     * Dedup key: tenant_id + email OR tenant_id + phone_number
     * Returns the first matching Owner or Resident, or null if none found.
     *
     * @return Owner|Resident|null
     */
    public function findDuplicate(Lead $lead): ?Model
    {
        $tenantId = Tenant::current()?->id;
        $email = $lead->email;
        $phone = $lead->phone_number;

        // Check owners first
        $ownerQuery = Owner::withoutGlobalScopes()
            ->where('account_tenant_id', $tenantId)
            ->where(function ($q) use ($email, $phone): void {
                if ($email) {
                    $q->orWhere('email', $email);
                }
                if ($phone) {
                    $q->orWhere('phone_number', $phone);
                }
            });

        $owner = $ownerQuery->first();

        if ($owner !== null) {
            return $owner;
        }

        // Check residents
        $residentQuery = Resident::withoutGlobalScopes()
            ->where('account_tenant_id', $tenantId)
            ->where(function ($q) use ($email, $phone): void {
                if ($email) {
                    $q->orWhere('email', $email);
                }
                if ($phone) {
                    $q->orWhere('phone_number', $phone);
                }
            });

        return $residentQuery->first();
    }

    /**
     * Execute conversion: create or link contact, update lead status, record activity.
     *
     * @param  string  $contactType  'owner' or 'resident'
     * @param  bool  $linkToExisting  When a duplicate is found, link instead of creating
     * @param  int|null  $existingContactId  Required when $linkToExisting = true
     * @return Owner|Resident
     */
    public function execute(
        Lead $lead,
        string $contactType,
        bool $linkToExisting = false,
        ?int $existingContactId = null,
        ?int $actorUserId = null,
    ): Model {
        return DB::transaction(function () use ($lead, $contactType, $linkToExisting, $existingContactId, $actorUserId): Model {
            // Re-check under a row-level lock to prevent a race where two
            // concurrent requests both pass the pre-transaction idempotency
            // guard and then both attempt to createContact().
            $lead = Lead::lockForUpdate()->findOrFail($lead->id);
            if ($lead->isConverted()) {
                abort(422, 'Lead already converted.');
            }

            $convertedStatus = Status::where('type', 'lead')
                ->where('name_en', 'Converted')
                ->firstOrFail();

            if ($linkToExisting && $existingContactId !== null) {
                $contact = $this->resolveExistingContact($contactType, $existingContactId);
            } else {
                $contact = $this->createContact($lead, $contactType);
            }

            $contactClass = $contact::class;

            $lead->update([
                'status_id' => $convertedStatus->id,
                'converted_contact_id' => $contact->id,
                'converted_contact_type' => $contactClass,
                'converted_at' => now(),
            ]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $actorUserId,
                'type' => LeadActivity::TYPE_CONVERTED,
                'data' => [
                    'contact_type' => $contactType,
                    'contact_id' => $contact->id,
                    'contact_name' => $this->contactDisplayName($contact),
                    'linked_existing' => $linkToExisting && $existingContactId !== null,
                ],
            ]);

            return $contact;
        });
    }

    /**
     * Create a new Owner or Resident from the lead's data.
     *
     * @return Owner|Resident
     */
    private function createContact(Lead $lead, string $contactType): Model
    {
        $tenantId = Tenant::current()?->id;

        // Split name_en into first/last (best-effort)
        $nameParts = explode(' ', trim((string) ($lead->name_en ?? $lead->name ?? '')), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Split name_ar into first/last (best-effort)
        $arParts = explode(' ', trim((string) ($lead->name_ar ?? '')), 2);
        $firstNameAr = $arParts[0] ?: null;
        $lastNameAr = $arParts[1] ?? null ?: null;

        $attributes = [
            'first_name' => $firstName ?: 'Unknown',
            'last_name' => $lastName ?: '',
            'first_name_ar' => $firstNameAr,
            'last_name_ar' => $lastNameAr,
            'email' => $lead->email,
            'phone_number' => $lead->phone_number ?? '',
            'phone_country_code' => $lead->phone_country_code ?? 'SA',
            'account_tenant_id' => $tenantId,
        ];

        if ($contactType === self::CONTACT_TYPE_RESIDENT) {
            return Resident::create($attributes);
        }

        return Owner::create($attributes);
    }

    /**
     * Resolve an existing contact by type and ID.
     *
     * @return Owner|Resident
     */
    private function resolveExistingContact(string $contactType, int $contactId): Model
    {
        if ($contactType === self::CONTACT_TYPE_RESIDENT) {
            return Resident::withoutGlobalScopes()->findOrFail($contactId);
        }

        return Owner::withoutGlobalScopes()->findOrFail($contactId);
    }

    private function contactDisplayName(Model $contact): string
    {
        return trim(($contact->first_name ?? '').' '.($contact->last_name ?? ''));
    }
}
