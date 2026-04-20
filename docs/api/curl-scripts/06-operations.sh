#!/bin/bash
# Create Operations (Transactions, Service Requests)
# Depends on: units, leases, professionals
# Creates: transaction_ids, request_ids

set -e
source "$(dirname "$0")/00-config.sh"

log "=== Creating Operations ==="

# Get IDs from previous steps
UNIT_1=$(get_id "unit_1")
UNIT_2=$(get_id "unit_2")
LEASE_1=$(get_id "lease_1")
PROFESSIONAL_1=$(get_id "professional_1")

# Reference data from request-categories.json:
# category_id: 1=Maintenance, 2=Offers, etc.
# status_id: 1=New, 3=Completed, etc.

# ===================
# SERVICE REQUESTS
# ===================
log "--- Creating Service Requests ---"

if [ -n "$UNIT_1" ]; then
    # Request 1: Maintenance request
    log "Creating Request 1: AC Maintenance"
    response=$(api_call "POST" "/rf/requests" "{
      \"unit_id\": $UNIT_1,
      \"category_id\": 1,
      \"sub_category_id\": 1,
      \"description\": \"Air conditioning unit not cooling properly. Needs inspection and maintenance.\",
      \"rf_status_id\": 1
    }")

    request_id=$(extract_id "$response")
    if [ -n "$request_id" ]; then
        save_id "request_1" "$request_id"
        log "SUCCESS: Created maintenance request with ID: $request_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create maintenance request"
    fi

    # Request 2: Plumbing request
    log "Creating Request 2: Plumbing Issue"
    response=$(api_call "POST" "/rf/requests" "{
      \"unit_id\": $UNIT_1,
      \"category_id\": 1,
      \"sub_category_id\": 2,
      \"description\": \"Water leak in bathroom. Needs urgent repair.\",
      \"rf_status_id\": 1
    }")

    request_id=$(extract_id "$response")
    if [ -n "$request_id" ]; then
        save_id "request_2" "$request_id"
        log "SUCCESS: Created plumbing request with ID: $request_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create plumbing request"
    fi
fi

if [ -n "$UNIT_2" ]; then
    # Request 3: Electrical request
    log "Creating Request 3: Electrical Issue"
    response=$(api_call "POST" "/rf/requests" "{
      \"unit_id\": $UNIT_2,
      \"category_id\": 1,
      \"sub_category_id\": 3,
      \"description\": \"Living room lights flickering. Electrical inspection needed.\",
      \"rf_status_id\": 1
    }")

    request_id=$(extract_id "$response")
    if [ -n "$request_id" ]; then
        save_id "request_3" "$request_id"
        log "SUCCESS: Created electrical request with ID: $request_id"
    else
        log "Response: $response"
        log "WARNING: Failed to create electrical request"
    fi
fi

# ===================
# TRANSACTIONS (if endpoint works)
# ===================
log "--- Creating Transactions ---"

# Note: Transaction creation may require different endpoint or additional setup
# Attempting to create sample transactions

if [ -n "$UNIT_1" ]; then
    TODAY=$(date '+%Y-%m-%d')
    DUE_DATE=$(date -d "+30 days" '+%Y-%m-%d')

    log "Creating Transaction 1: Rent Payment"
    response=$(api_call "POST" "/rf/transactions" "{
      \"amount\": 5000,
      \"category\": \"rent\",
      \"due_on\": \"$DUE_DATE\",
      \"unit\": {\"id\": $UNIT_1},
      \"description\": \"Monthly rent payment for May 2026\"
    }")

    transaction_id=$(extract_id "$response")
    if [ -n "$transaction_id" ]; then
        save_id "transaction_1" "$transaction_id"
        log "SUCCESS: Created transaction with ID: $transaction_id"
    else
        log "Response: $response"
        log "WARNING: Transaction creation may require different payload format"
    fi
fi

log "=== Operations creation complete ==="
cat "$IDS_FILE" | jq '.'
