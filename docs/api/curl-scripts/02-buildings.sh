#!/bin/bash
# Create Buildings
# Depends on: communities (community_1, community_2)
# Creates: building_ids for use by units

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Buildings ==="

# Get community IDs from previous step
COMMUNITY_1=$(get_id "community_1")
COMMUNITY_2=$(get_id "community_2")

if [ -z "$COMMUNITY_1" ]; then
    error_exit "community_1 not found. Run 01-communities.sh first"
fi

log "Using community_1: $COMMUNITY_1"

# Building 1: Residential Building A
log "Creating Building 1: Tower A"
response=$(api_call "POST" "/rf/buildings" "{
  \"name\": \"Tower A\",
  \"rf_community_id\": $COMMUNITY_1,
  \"floors_count\": 10
}")

building_id=$(extract_id "$response")
if [ -n "$building_id" ]; then
    save_id "building_1" "$building_id"
    log "SUCCESS: Created Tower A with ID: $building_id"
else
    log "Response: $response"
    error_exit "Failed to create building"
fi

# Building 2: Residential Building B
log "Creating Building 2: Tower B"
response=$(api_call "POST" "/rf/buildings" "{
  \"name\": \"Tower B\",
  \"rf_community_id\": $COMMUNITY_1,
  \"floors_count\": 8
}")

building_id=$(extract_id "$response")
if [ -n "$building_id" ]; then
    save_id "building_2" "$building_id"
    log "SUCCESS: Created Tower B with ID: $building_id"
else
    log "Response: $response"
    log "WARNING: Failed to create Tower B"
fi

# Building 3: Commercial Building (if community_2 exists)
if [ -n "$COMMUNITY_2" ]; then
    log "Creating Building 3: Business Center"
    response=$(api_call "POST" "/rf/buildings" "{
      \"name\": \"Business Center\",
      \"rf_community_id\": $COMMUNITY_2,
      \"floors_count\": 15
    }")

    building_id=$(extract_id "$response")
    if [ -n "$building_id" ]; then
        save_id "building_3" "$building_id"
        log "SUCCESS: Created Business Center with ID: $building_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create Business Center"
    fi
fi

log "=== Buildings creation complete ==="
cat "$IDS_FILE" | jq '.'
