#!/bin/bash
# Create Contacts (Owners, Tenants, Admins, Professionals)
# Depends on: nothing (standalone entities)
# Creates: owner_ids, tenant_ids, admin_ids, professional_ids

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Contacts ==="

# ===================
# OWNERS
# ===================
log "--- Creating Owners ---"

# Owner 1
log "Creating Owner 1: Ahmed Al-Mansouri"
response=$(api_call "POST" "/rf/owners" '{
  "first_name": "Ahmed",
  "last_name": "Al-Mansouri",
  "phone_country_code": "SA",
  "phone_number": "501234567",
  "email": "ahmed.almansouri@example.com",
  "national_id": "1234567890"
}')

owner_id=$(extract_id "$response")
if [ -n "$owner_id" ]; then
    save_id "owner_1" "$owner_id"
    log "SUCCESS: Created owner with ID: $owner_id"
else
    log "Response: $response"
    log "WARNING: Failed to create owner (may already exist)"
fi

# Owner 2
log "Creating Owner 2: Fatima Al-Rashid"
response=$(api_call "POST" "/rf/owners" '{
  "first_name": "Fatima",
  "last_name": "Al-Rashid",
  "phone_country_code": "SA",
  "phone_number": "502345678",
  "email": "fatima.alrashid@example.com"
}')

owner_id=$(extract_id "$response")
if [ -n "$owner_id" ]; then
    save_id "owner_2" "$owner_id"
    log "SUCCESS: Created owner with ID: $owner_id"
else
    log "Response: $response"
    log "WARNING: Failed to create owner"
fi

# ===================
# TENANTS
# ===================
log "--- Creating Tenants ---"

# Tenant 1: Individual
log "Creating Tenant 1: Mohammed Al-Saud"
response=$(api_call "POST" "/rf/tenants" '{
  "first_name": "Mohammed",
  "last_name": "Al-Saud",
  "phone_country_code": "SA",
  "phone_number": "509876543",
  "email": "mohammed.alsaud@example.com",
  "type": "individual",
  "national_id": "9876543210"
}')

tenant_id=$(extract_id "$response")
if [ -n "$tenant_id" ]; then
    save_id "tenant_1" "$tenant_id"
    log "SUCCESS: Created tenant with ID: $tenant_id"
else
    log "Response: $response"
    log "WARNING: Failed to create tenant (may already exist)"
fi

# Tenant 2: Individual
log "Creating Tenant 2: Sara Al-Qahtani"
response=$(api_call "POST" "/rf/tenants" '{
  "first_name": "Sara",
  "last_name": "Al-Qahtani",
  "phone_country_code": "SA",
  "phone_number": "508765432",
  "email": "sara.alqahtani@example.com",
  "type": "individual"
}')

tenant_id=$(extract_id "$response")
if [ -n "$tenant_id" ]; then
    save_id "tenant_2" "$tenant_id"
    log "SUCCESS: Created tenant with ID: $tenant_id"
else
    log "Response: $response"
    log "WARNING: Failed to create tenant"
fi

# Tenant 3: Company
log "Creating Tenant 3: Tech Solutions LLC"
response=$(api_call "POST" "/rf/tenants" '{
  "first_name": "Tech Solutions",
  "last_name": "LLC",
  "phone_country_code": "SA",
  "phone_number": "507654321",
  "email": "contact@techsolutions.sa",
  "type": "company",
  "company_name": "Tech Solutions LLC",
  "cr_number": "1234567890"
}')

tenant_id=$(extract_id "$response")
if [ -n "$tenant_id" ]; then
    save_id "tenant_3" "$tenant_id"
    log "SUCCESS: Created company tenant with ID: $tenant_id"
else
    log "Response: $response"
    log "WARNING: Failed to create company tenant"
fi

# ===================
# ADMINS
# ===================
log "--- Creating Admins ---"

# Reference: manager-roles.json
# role_id: 1=Manager, 2=Accountant, 3=Customer Service, etc.

# Admin 1: Property Manager
log "Creating Admin 1: Khalid Al-Farhan (Manager)"
response=$(api_call "POST" "/rf/admins" '{
  "first_name": "Khalid",
  "last_name": "Al-Farhan",
  "phone_country_code": "SA",
  "phone_number": "501111111",
  "email": "khalid.alfarhan@property.sa",
  "role": 1
}')

admin_id=$(extract_id "$response")
if [ -n "$admin_id" ]; then
    save_id "admin_1" "$admin_id"
    log "SUCCESS: Created admin with ID: $admin_id"
else
    log "Response: $response"
    log "WARNING: Failed to create admin (may already exist or require different role format)"
fi

# Admin 2: Accountant
log "Creating Admin 2: Noura Al-Zahrani (Accountant)"
response=$(api_call "POST" "/rf/admins" '{
  "first_name": "Noura",
  "last_name": "Al-Zahrani",
  "phone_country_code": "SA",
  "phone_number": "502222222",
  "email": "noura.alzahrani@property.sa",
  "role": 2
}')

admin_id=$(extract_id "$response")
if [ -n "$admin_id" ]; then
    save_id "admin_2" "$admin_id"
    log "SUCCESS: Created admin with ID: $admin_id"
else
    log "Response: $response"
    log "WARNING: Failed to create accountant admin"
fi

# ===================
# PROFESSIONALS
# ===================
log "--- Creating Professionals ---"

# Professional 1: Maintenance Technician
log "Creating Professional 1: Ali Hassan (Maintenance)"
response=$(api_call "POST" "/rf/professionals" '{
  "first_name": "Ali",
  "last_name": "Hassan",
  "phone_country_code": "SA",
  "phone_number": "503333333",
  "email": "ali.hassan@maintenance.sa",
  "service_types": [1, 2]
}')

professional_id=$(extract_id "$response")
if [ -n "$professional_id" ]; then
    save_id "professional_1" "$professional_id"
    log "SUCCESS: Created professional with ID: $professional_id"
else
    log "Response: $response"
    log "WARNING: Failed to create professional"
fi

# Professional 2: Electrician
log "Creating Professional 2: Omar Al-Mutairi (Electrician)"
response=$(api_call "POST" "/rf/professionals" '{
  "first_name": "Omar",
  "last_name": "Al-Mutairi",
  "phone_country_code": "SA",
  "phone_number": "504444444",
  "email": "omar.almutairi@electrical.sa",
  "service_types": [3]
}')

professional_id=$(extract_id "$response")
if [ -n "$professional_id" ]; then
    save_id "professional_2" "$professional_id"
    log "SUCCESS: Created professional with ID: $professional_id"
else
    log "Response: $response"
    log "WARNING: Failed to create electrician professional"
fi

log "=== Contacts creation complete ==="
cat "$IDS_FILE" | jq '.'
