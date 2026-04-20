#!/bin/bash
# Create Leases
# Depends on: units, tenants
# Creates: lease_ids for use by transactions

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Leases ==="

# Get IDs from previous steps
TENANT_1=$(get_id "tenant_1")
TENANT_2=$(get_id "tenant_2")
TENANT_3=$(get_id "tenant_3")
UNIT_1=$(get_id "unit_1")
UNIT_2=$(get_id "unit_2")
UNIT_5=$(get_id "unit_5")

if [ -z "$TENANT_1" ] || [ -z "$UNIT_1" ]; then
    error_exit "Required IDs not found. Run 04-contacts.sh and 03-units.sh first"
fi

log "Using tenant_1: $TENANT_1, unit_1: $UNIT_1"

# Reference data from leases-create.json:
# rental_contract_type_id: 13=Yearly, 14=Monthly, 15=Daily
# payment_schedule_id: 4=Monthly, 5=Quarterly, 6=Semi-Annual, 7=Annual
# status_id: 30=New, 31=Active

# Calculate dates
TODAY=$(date '+%Y-%m-%d')
START_DATE=$(date -d "+1 month" '+%Y-%m-%d')
END_DATE=$(date -d "+13 months" '+%Y-%m-%d')
HANDOVER_DATE="$START_DATE"

# Lease 1: Yearly rental for Apartment 101
log "Creating Lease 1: Yearly rental for Unit 1"
response=$(api_call "POST" "/rf/leases" "{
  \"autoGenerateLeaseNumber\": true,
  \"created_at\": \"$TODAY\",
  \"start_date\": \"$START_DATE\",
  \"end_date\": \"$END_DATE\",
  \"handover_date\": \"$HANDOVER_DATE\",
  \"number_of_years\": 1,
  \"number_of_months\": 0,
  \"lease_unit_type\": 1,
  \"tenant_type\": \"individual\",
  \"tenant_id\": $TENANT_1,
  \"tenant\": {
    \"id\": $TENANT_1,
    \"name\": \"Mohammed Al-Saud\",
    \"phone_number\": \"+966509876543\",
    \"national_id\": \"9876543210\"
  },
  \"rental_type\": \"detailed\",
  \"rental_contract_type_id\": 13,
  \"payment_schedule_id\": 4,
  \"lease_escalations_type\": \"fixed\",
  \"rental_total_amount\": 60000,
  \"rf_status_id\": 30,
  \"units\": [
    {
      \"id\": $UNIT_1,
      \"rental_annual_type\": \"total\",
      \"annual_rental_amount\": 60000,
      \"amount_type\": \"total\"
    }
  ]
}")

lease_id=$(extract_id "$response")
if [ -n "$lease_id" ]; then
    save_id "lease_1" "$lease_id"
    log "SUCCESS: Created lease with ID: $lease_id"
else
    log "Response: $response"
    log "WARNING: Failed to create lease (check validation rules)"
fi

# Lease 2: Another yearly rental (if tenant_2 and unit_2 exist)
if [ -n "$TENANT_2" ] && [ -n "$UNIT_2" ]; then
    log "Creating Lease 2: Yearly rental for Unit 2"
    END_DATE_2=$(date -d "+25 months" '+%Y-%m-%d')

    response=$(api_call "POST" "/rf/leases" "{
      \"autoGenerateLeaseNumber\": true,
      \"created_at\": \"$TODAY\",
      \"start_date\": \"$START_DATE\",
      \"end_date\": \"$END_DATE_2\",
      \"handover_date\": \"$HANDOVER_DATE\",
      \"number_of_years\": 2,
      \"number_of_months\": 0,
      \"lease_unit_type\": 1,
      \"tenant_type\": \"individual\",
      \"tenant_id\": $TENANT_2,
      \"tenant\": {
        \"id\": $TENANT_2,
        \"name\": \"Sara Al-Qahtani\",
        \"phone_number\": \"+966508765432\"
      },
      \"rental_type\": \"detailed\",
      \"rental_contract_type_id\": 13,
      \"payment_schedule_id\": 5,
      \"lease_escalations_type\": \"percentage\",
      \"rental_total_amount\": 80000,
      \"rf_status_id\": 30,
      \"units\": [
        {
          \"id\": $UNIT_2,
          \"rental_annual_type\": \"total\",
          \"annual_rental_amount\": 80000,
          \"amount_type\": \"total\"
        }
      ]
    }")

    lease_id=$(extract_id "$response")
    if [ -n "$lease_id" ]; then
        save_id "lease_2" "$lease_id"
        log "SUCCESS: Created lease with ID: $lease_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create lease 2"
    fi
fi

# Lease 3: Commercial lease (if tenant_3 and unit_5 exist)
if [ -n "$TENANT_3" ] && [ -n "$UNIT_5" ]; then
    log "Creating Lease 3: Commercial lease for Office"
    END_DATE_3=$(date -d "+37 months" '+%Y-%m-%d')

    response=$(api_call "POST" "/rf/leases" "{
      \"autoGenerateLeaseNumber\": true,
      \"created_at\": \"$TODAY\",
      \"start_date\": \"$START_DATE\",
      \"end_date\": \"$END_DATE_3\",
      \"handover_date\": \"$HANDOVER_DATE\",
      \"number_of_years\": 3,
      \"number_of_months\": 0,
      \"lease_unit_type\": 1,
      \"tenant_type\": \"company\",
      \"tenant_id\": $TENANT_3,
      \"tenant\": {
        \"id\": $TENANT_3,
        \"name\": \"Tech Solutions LLC\",
        \"phone_number\": \"+966507654321\",
        \"cr_number\": \"1234567890\"
      },
      \"rental_type\": \"detailed\",
      \"rental_contract_type_id\": 13,
      \"payment_schedule_id\": 7,
      \"lease_escalations_type\": \"fixed\",
      \"rental_total_amount\": 120000,
      \"rf_status_id\": 30,
      \"units\": [
        {
          \"id\": $UNIT_5,
          \"rental_annual_type\": \"total\",
          \"annual_rental_amount\": 120000,
          \"amount_type\": \"total\"
        }
      ]
    }")

    lease_id=$(extract_id "$response")
    if [ -n "$lease_id" ]; then
        save_id "lease_3" "$lease_id"
        log "SUCCESS: Created commercial lease with ID: $lease_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create commercial lease"
    fi
fi

log "=== Leases creation complete ==="
cat "$IDS_FILE" | jq '.'
