/**
 * Run this script in the browser console while logged into goatar.com
 * It fetches districts per city and builds the city_id mapping.
 * 
 * Usage:
 * 1. Log in to https://app.goatar.com
 * 2. Open browser DevTools (F12) → Console
 * 3. Paste and run this script
 * 4. Copy the resulting JSON and save to docs/api/seeder-raw/district-city-mapping.json
 */
(async () => {
    const TENANCY_URL = 'https://api.goatar.com/tenancy/api';
    const token = localStorage.getItem('token') || prompt('Enter your Bearer token:');
    const tenant = localStorage.getItem('tenant') || prompt('Enter tenant ID (e.g. scantest2026apr):');
    
    const cities = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26];
    const mapping = {};
    
    for (const cityId of cities) {
        try {
            const resp = await fetch(`${TENANCY_URL}/districts/${cityId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-Tenant': tenant,
                    'X-App-Locale': 'en',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            const districts = data.data || [];
            console.log(`City ${cityId}: ${districts.length} districts`);
            for (const d of districts) {
                mapping[d.id] = cityId;
            }
        } catch (e) {
            console.error(`Error fetching city ${cityId}:`, e);
        }
    }
    
    const result = JSON.stringify(mapping, null, 2);
    console.log('=== DISTRICT-CITY MAPPING ===');
    console.log(result);
    console.log('=== Copy the above JSON and save to docs/api/seeder-raw/district-city-mapping.json ===');
    
    // Also try to copy to clipboard
    try {
        await navigator.clipboard.writeText(result);
        console.log('✅ Copied to clipboard!');
    } catch (e) {
        console.log('Could not copy to clipboard. Please copy manually from above.');
    }
})();
