#!/bin/bash
# Configuration for goatar.com API curl scripts
# Generated: 2026-04-20

# API Base URLs
export BASE_URL="https://api.goatar.com/api-management"
export TENANCY_URL="https://api.goatar.com/tenancy/api"

# Authentication (from user's session)
export AUTH_TOKEN="1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv"
export TENANT_ID="scantest2026apr"

# Headers
export AUTH="Authorization: Bearer $AUTH_TOKEN"
export TENANT="X-Tenant: $TENANT_ID"
export CONTENT_TYPE="Content-Type: application/json"
export LOCALE_EN="X-App-Locale: en"
export LOCALE_AR="X-App-Locale: ar"

# Storage for created IDs (used across scripts)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export IDS_FILE="$SCRIPT_DIR/created_ids.json"

# Initialize IDs file if not exists
if [ ! -f "$IDS_FILE" ]; then
    echo '{}' > "$IDS_FILE"
fi

# Helper function: Make API call and handle response
api_call() {
    local method="$1"
    local endpoint="$2"
    local data="$3"
    local base_url="${4:-$BASE_URL}"

    if [ -n "$data" ]; then
        response=$(curl -s -w "\n%{http_code}" -X "$method" "$base_url$endpoint" \
            -H "$AUTH" \
            -H "$TENANT" \
            -H "$CONTENT_TYPE" \
            -H "$LOCALE_EN" \
            -d "$data")
    else
        response=$(curl -s -w "\n%{http_code}" -X "$method" "$base_url$endpoint" \
            -H "$AUTH" \
            -H "$TENANT" \
            -H "$LOCALE_EN")
    fi

    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | head -n -1)

    echo "$body"
    return $((http_code == 200 || http_code == 201 ? 0 : 1))
}

# Helper function: Save created ID to IDs file
save_id() {
    local entity="$1"
    local id="$2"
    local temp_file=$(mktemp)
    jq --arg entity "$entity" --arg id "$id" '.[$entity] = ($id | tonumber)' "$IDS_FILE" > "$temp_file"
    mv "$temp_file" "$IDS_FILE"
    echo "Saved $entity ID: $id"
}

# Helper function: Get saved ID
get_id() {
    local entity="$1"
    jq -r --arg entity "$entity" '.[$entity] // empty' "$IDS_FILE"
}

# Helper function: Save array of IDs
save_ids_array() {
    local entity="$1"
    shift
    local ids=("$@")
    local temp_file=$(mktemp)
    jq --arg entity "$entity" --argjson ids "$(printf '%s\n' "${ids[@]}" | jq -R . | jq -s .)" '.[$entity] = $ids' "$IDS_FILE" > "$temp_file"
    mv "$temp_file" "$IDS_FILE"
    echo "Saved $entity IDs: ${ids[*]}"
}

# Helper function: Log with timestamp
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# Helper function: Error exit
error_exit() {
    log "ERROR: $1"
    exit 1
}

# Helper function: Extract ID from response
extract_id() {
    local response="$1"
    echo "$response" | jq -r '.data.id // .id // empty'
}

# Test API connection
test_connection() {
    log "Testing API connection..."
    response=$(api_call "GET" "/rf/modules")
    if [ $? -eq 0 ]; then
        log "API connection successful"
        return 0
    else
        error_exit "API connection failed: $response"
    fi
}

log "Configuration loaded"
log "BASE_URL: $BASE_URL"
log "TENANT: $TENANT_ID"
