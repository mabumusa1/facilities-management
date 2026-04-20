#!/bin/bash
# Master script to run all curl scripts in dependency order
# Usage: ./run-all.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$SCRIPT_DIR"

echo "=============================================="
echo "  goatar.com Database Population Script"
echo "=============================================="
echo ""
echo "This script will create test data in the following order:"
echo "  1. Communities (properties)"
echo "  2. Buildings"
echo "  3. Units"
echo "  4. Contacts (owners, tenants, admins, professionals)"
echo "  5. Leases"
echo "  6. Operations (requests, transactions)"
echo ""

# Check if config exists
if [ ! -f "00-config.sh" ]; then
    echo "ERROR: 00-config.sh not found!"
    echo "Please ensure configuration file exists with valid auth token."
    exit 1
fi

# Source config to test connection
source ./00-config.sh
test_connection

# Cleanup previous IDs file
if [ -f "$IDS_FILE" ]; then
    echo "Previous IDs file found. Backing up..."
    mv "$IDS_FILE" "${IDS_FILE}.bak.$(date +%Y%m%d_%H%M%S)"
fi
echo '{}' > "$IDS_FILE"

echo ""
echo "Starting data creation..."
echo ""

# Run scripts in order
scripts=(
    "01-communities.sh"
    "02-buildings.sh"
    "03-units.sh"
    "04-contacts.sh"
    "05-leases.sh"
    "06-operations.sh"
)

for script in "${scripts[@]}"; do
    if [ -f "$script" ]; then
        echo ""
        echo "========================================"
        echo "Running: $script"
        echo "========================================"
        chmod +x "$script"
        ./"$script"

        if [ $? -ne 0 ]; then
            echo "ERROR: $script failed!"
            echo "Check the output above for details."
            exit 1
        fi
    else
        echo "WARNING: $script not found, skipping..."
    fi
done

echo ""
echo "=============================================="
echo "  Data Population Complete!"
echo "=============================================="
echo ""
echo "Created IDs:"
cat "$IDS_FILE" | jq '.'
echo ""
echo "IDs saved to: $IDS_FILE"
echo ""
echo "To verify, run:"
echo "  curl -s \"\$BASE_URL/rf/communities\" -H \"\$AUTH\" -H \"\$TENANT\" | jq '.data | length'"
echo "  curl -s \"\$BASE_URL/rf/buildings\" -H \"\$AUTH\" -H \"\$TENANT\" | jq '.data | length'"
echo "  curl -s \"\$BASE_URL/rf/units\" -H \"\$AUTH\" -H \"\$TENANT\" | jq '.data | length'"
