#!/bin/bash
# Create Communities
# Depends on: nothing (uses reference data only)
# Creates: community_id for use by buildings, units, facilities

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Communities ==="

# Reference data from seeder-raw:
# country_id: 1 (Saudi Arabia)
# currency_id: 1 (SAR)
# city_id: 1 (Riyadh), 4 (Jeddah)
# district_id: 1 (Al Diriyah), 15 (Al Olaya)

# Community 1: Residential Community in Riyadh
log "Creating Community 1: Al Nakheel Residence"
response=$(api_call "POST" "/rf/communities" '{
  "name": "Al Nakheel Residence",
  "name_ar": "سكن النخيل",
  "name_en": "Al Nakheel Residence",
  "country_id": 1,
  "currency_id": 1,
  "city_id": 1,
  "district_id": 15,
  "latitude": 24.7136,
  "longitude": 46.6753,
  "description": "Premium residential community in Al Olaya district"
}')

community_id=$(extract_id "$response")
if [ -n "$community_id" ]; then
    save_id "community_1" "$community_id"
    log "SUCCESS: Created community with ID: $community_id"
else
    log "Response: $response"
    error_exit "Failed to create community"
fi

# Community 2: Commercial Community
log "Creating Community 2: Business Park Tower"
response=$(api_call "POST" "/rf/communities" '{
  "name": "Business Park Tower",
  "name_ar": "برج بيزنس بارك",
  "name_en": "Business Park Tower",
  "country_id": 1,
  "currency_id": 1,
  "city_id": 1,
  "district_id": 36,
  "latitude": 24.6947,
  "longitude": 46.6853,
  "description": "Premium commercial tower in King Fahd district"
}')

community_id=$(extract_id "$response")
if [ -n "$community_id" ]; then
    save_id "community_2" "$community_id"
    log "SUCCESS: Created commercial community with ID: $community_id"
else
    log "Response: $response"
    log "WARNING: Failed to create commercial community (may already exist)"
fi

log "=== Communities creation complete ==="
log "Created IDs saved to: $IDS_FILE"
cat "$IDS_FILE" | jq '.'
