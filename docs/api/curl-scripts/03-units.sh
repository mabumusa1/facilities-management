#!/bin/bash
# Create Units
# Depends on: communities, buildings
# Creates: unit_ids for use by leases, transactions, requests

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Units ==="

# Get IDs from previous steps
COMMUNITY_1=$(get_id "community_1")
COMMUNITY_2=$(get_id "community_2")
BUILDING_1=$(get_id "building_1")
BUILDING_2=$(get_id "building_2")
BUILDING_3=$(get_id "building_3")

if [ -z "$COMMUNITY_1" ] || [ -z "$BUILDING_1" ]; then
    error_exit "Required IDs not found. Run 01-communities.sh and 02-buildings.sh first"
fi

log "Using community_1: $COMMUNITY_1, building_1: $BUILDING_1"

# Reference data from units-create.json:
# category_id: 2=Residential, 3=Commercial
# type_id (Residential): 17=Apartment, 22=Villa, 18=Penthouse
# type_id (Commercial): 26=Store, 30=Office, 135=Showroom
# status_id: 26=Vacant, 25=Leased

unit_ids=()

# Unit 1: 2BR Apartment (Vacant)
log "Creating Unit 1: 2BR Apartment 101"
response=$(api_call "POST" "/rf/units" "{
  \"name\": \"Apt 101\",
  \"rf_community_id\": $COMMUNITY_1,
  \"rf_building_id\": $BUILDING_1,
  \"category_id\": 2,
  \"type_id\": 17,
  \"rf_status_id\": 26,
  \"floor_number\": 1,
  \"total_area\": 120,
  \"specifications\": {
    \"bedrooms\": 2,
    \"bathrooms\": 2,
    \"floor_no\": 1,
    \"parking_space\": 1
  }
}")

unit_id=$(extract_id "$response")
if [ -n "$unit_id" ]; then
    save_id "unit_1" "$unit_id"
    unit_ids+=("$unit_id")
    log "SUCCESS: Created Apt 101 with ID: $unit_id"
else
    log "Response: $response"
    error_exit "Failed to create unit"
fi

# Unit 2: 3BR Apartment (Vacant)
log "Creating Unit 2: 3BR Apartment 102"
response=$(api_call "POST" "/rf/units" "{
  \"name\": \"Apt 102\",
  \"rf_community_id\": $COMMUNITY_1,
  \"rf_building_id\": $BUILDING_1,
  \"category_id\": 2,
  \"type_id\": 17,
  \"rf_status_id\": 26,
  \"floor_number\": 1,
  \"total_area\": 150,
  \"specifications\": {
    \"bedrooms\": 3,
    \"bathrooms\": 2,
    \"floor_no\": 1,
    \"parking_space\": 1
  }
}")

unit_id=$(extract_id "$response")
if [ -n "$unit_id" ]; then
    save_id "unit_2" "$unit_id"
    unit_ids+=("$unit_id")
    log "SUCCESS: Created Apt 102 with ID: $unit_id"
else
    log "Response: $response"
    log "WARNING: Failed to create Apt 102"
fi

# Unit 3: Penthouse (Vacant)
log "Creating Unit 3: Penthouse 1001"
response=$(api_call "POST" "/rf/units" "{
  \"name\": \"PH 1001\",
  \"rf_community_id\": $COMMUNITY_1,
  \"rf_building_id\": $BUILDING_1,
  \"category_id\": 2,
  \"type_id\": 18,
  \"rf_status_id\": 26,
  \"floor_number\": 10,
  \"total_area\": 300,
  \"specifications\": {
    \"bedrooms\": 4,
    \"bathrooms\": 3,
    \"floor_no\": 10,
    \"parking_space\": 2
  }
}")

unit_id=$(extract_id "$response")
if [ -n "$unit_id" ]; then
    save_id "unit_3" "$unit_id"
    unit_ids+=("$unit_id")
    log "SUCCESS: Created PH 1001 with ID: $unit_id"
else
    log "Response: $response"
    log "WARNING: Failed to create Penthouse"
fi

# Unit 4: Villa in Building 2 (if exists)
if [ -n "$BUILDING_2" ]; then
    log "Creating Unit 4: Villa 201"
    response=$(api_call "POST" "/rf/units" "{
      \"name\": \"Villa 201\",
      \"rf_community_id\": $COMMUNITY_1,
      \"rf_building_id\": $BUILDING_2,
      \"category_id\": 2,
      \"type_id\": 22,
      \"rf_status_id\": 26,
      \"floor_number\": 0,
      \"total_area\": 400,
      \"specifications\": {
        \"bedrooms\": 5,
        \"bathrooms\": 4,
        \"parking_space\": 3
      }
    }")

    unit_id=$(extract_id "$response")
    if [ -n "$unit_id" ]; then
        save_id "unit_4" "$unit_id"
        unit_ids+=("$unit_id")
        log "SUCCESS: Created Villa 201 with ID: $unit_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create Villa"
    fi
fi

# Unit 5: Commercial Office (if community_2 and building_3 exist)
if [ -n "$COMMUNITY_2" ] && [ -n "$BUILDING_3" ]; then
    log "Creating Unit 5: Office 301"
    response=$(api_call "POST" "/rf/units" "{
      \"name\": \"Office 301\",
      \"rf_community_id\": $COMMUNITY_2,
      \"rf_building_id\": $BUILDING_3,
      \"category_id\": 3,
      \"type_id\": 30,
      \"rf_status_id\": 26,
      \"floor_number\": 3,
      \"total_area\": 100,
      \"specifications\": {
        \"floor_no\": 3,
        \"fit_out_status\": \"shell_core\"
      }
    }")

    unit_id=$(extract_id "$response")
    if [ -n "$unit_id" ]; then
        save_id "unit_5" "$unit_id"
        unit_ids+=("$unit_id")
        log "SUCCESS: Created Office 301 with ID: $unit_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create Office"
    fi

    # Unit 6: Retail Store
    log "Creating Unit 6: Store G01"
    response=$(api_call "POST" "/rf/units" "{
      \"name\": \"Store G01\",
      \"rf_community_id\": $COMMUNITY_2,
      \"rf_building_id\": $BUILDING_3,
      \"category_id\": 3,
      \"type_id\": 26,
      \"rf_status_id\": 26,
      \"floor_number\": 0,
      \"total_area\": 80,
      \"specifications\": {
        \"floor_no\": 0,
        \"fit_out_status\": \"fitted\"
      }
    }")

    unit_id=$(extract_id "$response")
    if [ -n "$unit_id" ]; then
        save_id "unit_6" "$unit_id"
        unit_ids+=("$unit_id")
        log "SUCCESS: Created Store G01 with ID: $unit_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create Store"
    fi
fi

log "=== Units creation complete ==="
log "Created ${#unit_ids[@]} units"
cat "$IDS_FILE" | jq '.'
