# Atar API Exploration Summary

## Overview
This document summarizes the API exploration done for the Atar property management platform at `api.goatar.com`.

## API Configuration
- **Base URL**: `https://api.goatar.com/api-management`
- **Authentication**: Bearer Token
- **Tenant Header**: `X-Tenant: testbusiness123`

## Successfully Working Endpoints

### Communities (Projects)
- `GET /rf/communities` - List all communities
- `POST /rf/communities` - Create community
- `PUT /rf/communities/{id}` - Update community
- `GET /rf/communities/{id}` - Get community details
- `GET /rf/units/create` - Get unit creation spec (categories, types, specifications)

**Required Fields for Community Creation:**
```json
{
  "name": "string (required)",
  "country_id": "number (required, e.g., 1 for Saudi Arabia)",
  "currency_id": "number (required, e.g., 1 for SAR)",
  "city_id": "number (required)",
  "district_id": "number (required)"
}
```

### Buildings
- `GET /rf/buildings` - List all buildings
- `POST /rf/buildings` - Create building
- `PUT /rf/buildings/{id}` - Update building
- `GET /rf/buildings/{id}` - Get building details

**Required Fields for Building Creation:**
```json
{
  "name": "string (required)",
  "rf_community_id": "number (required)"
}
```

**Optional Fields:**
- `no_floors`: number
- `year_build`: number
- `description`: string

### Owners
- `GET /rf/owners` - List owners
- `POST /rf/owners` - Create owner

**Required Fields for Owner Creation:**
```json
{
  "first_name": "string (required)",
  "last_name": "string (required)",
  "phone_country_code": "string (required, e.g., 'SA')",
  "phone_number": "string (required, without country code prefix, e.g., '500000001')"
}
```

**Optional Fields:**
- `email`: string
- `national_id`: string (must be unique)

### Statuses
- `GET /rf/statuses?type=unit` - Get unit statuses

**Unit Status IDs:**
- 23: مباعة (Sold)
- 24: مباعة و مؤجرة (Sold & Rented)
- 25: مؤجرة (Rented)
- 26: متاحة (Available)

### Other Working Endpoints
- `GET /rf/admins` - List admin users
- `GET /rf/tenants` - List tenants
- `GET /rf/modules` - List active modules
- `GET /statuses` - List all statuses
- `GET /plans` - List subscription plans

## Unit Categories and Types

### Residential (category_id: 2)
| Type ID | Arabic Name | English Type |
|---------|-------------|--------------|
| 17 | شقة | apartment |
| 18 | بنتهاوس | penthouse |
| 19 | شقة دوبلكس | duplex_apartment |
| 20 | فيلا دوبلكس | duplex_villa |
| 21 | دور | floor_apartment |
| 22 | فيلا | villa |
| 24 | تاون هاوس | townhouse |
| 25 | أرض | land |

### Commercial (category_id: 3)
| Type ID | Arabic Name | English Type |
|---------|-------------|--------------|
| 26 | محل | retail_store |
| 27 | مطعم /مقهى | f&b_outlet |
| 28 | مستودع | warehouse |
| 29 | مخزن | storage |
| 30 | مكتب | office |
| 31 | أرض | land |
| 135 | معرض | Showroom |
| 136 | كشك | kiosk |
| 137 | مكتب تنفيذي | executive_office |
| 138 | مكتب مشترك | shared_office |
| 139 | مبنى | building |
| 140 | برج | tower |

## Unit Specifications

### Residential Unit Specifications
| Spec ID | Arabic Name | English Type | Input Type |
|---------|-------------|--------------|------------|
| 4 | صافي مساحة الوحدة (متر مربع) | Net_Unit_Area | text |
| 5 | غرف النوم | Bedrooms | counter |
| 6 | دورات المياه | Bathrooms | counter |
| 7 | المجالس | Guest_Rooms | counter |
| 8 | الصالات | Lounges | counter |
| 9 | رقم الدور | Floor_No | counter |
| 10 | عدد المواقف | Parking_Spaces | counter |
| 11 | رقم عداد الكهرباء | Electricity_Meter_No | text |
| 12 | رقم عداد المياه | Water_Meter_No | text |
| 13 | نوع التكييف | Air_Conditioning | select |
| 14 | نوع المواقف | Parking_Type | select |
| 15 | حالة التأثيث | Furnishing_Type | select |
| 16 | نوع المطبخ | Kitchen_Type | select |

### Specification Options
**Air Conditioning (13):**
- 32: مركزي (Central)
- 33: سبليت (Split)
- 34: سبليت مخفي (Hidden Split)
- 35: شباك (Window)
- 36: صحراوي (Desert)
- 37: أخرى (Other)
- 38: غير متوفر (Not Available)

## Amenities

### Residential Amenities
| ID | Arabic Name | English Name |
|----|-------------|--------------|
| 55 | دخول ذكي | Smart access |
| 56 | مواقف سيارات | Parking |
| 58 | حديقة خارجية | Outdoor garden |
| 59 | شرفة | Balcony |
| 60 | جهاز إنذار الحريق | Fire alarm |
| 61 | مطبخ | Kitchen |
| 62 | غسالة | Washing machine |
| 63 | واي فاي | WiFi |
| 64 | مناسب لذوي الاحتياجات الخاصة | Wheelchair accessible |
| 65 | نوافذ بزجاج مزدوج | Double glazed windows |
| 66 | مصعد | Elevator |
| 67 | منزل ذكي | Smart home |

### Commercial Amenities
| ID | Arabic Name | English Name |
|----|-------------|--------------|
| 57 | مساحات عمل مشتركة | Co-working space |
| 68 | مناسب لذوي الاحتياجات الخاصة | Wheelchair accessible |
| 69 | أنظمة إنذار | Alarm systems |
| 70 | مناطق استراحة | Breakout areas |
| 71 | قاعة اجتماعات | Conference room |
| 72 | مصعد | Elevator |
| 73 | أنظمة موفرة للطاقة | Energy-efficient systems |
| 74 | إنترنت عالي السرعة | High-speed internet |

### Units - WORKING!
- `GET /rf/units` - List all units
- `POST /rf/units` - Create unit ✅
- `GET /rf/units/{id}` - Get unit details
- `PUT /rf/units/{id}` - Update unit

**Required Fields for Unit Creation:**
```json
{
  "name": "string (required)",
  "category_id": "number (required, 2=Residential, 3=Commercial)",
  "type_id": "number (required, must match category)",
  "rf_community_id": "number (required)",
  "map": {
    "latitude": "number (required)",
    "longitude": "number (required)",
    "place_id": "string (required, Google Places ID)",
    "districtName": "string (required)",
    "formattedAddress": "string (required)",
    "latitudeDelta": "number (required, e.g., 0.02)",
    "longitudeDelta": "number (required, e.g., 0.009)",
    "mapsLink": "string (required, Google Maps URL)"
  }
}
```

**Optional Fields:**
- `rf_building_id`: number
- `rf_status_id`: number (default: 26 for Vacant)
- `specifications`: array
- `features`: array (amenity IDs)
- `photos`: array
- `documents`: array
- `floor_plans`: array
- `rooms`: array
- `areas`: array

**Example Working Request:**
```json
{
  "name": "Test Unit API v2",
  "category_id": 2,
  "type_id": 17,
  "rf_community_id": 1,
  "rf_building_id": 1,
  "rf_status_id": 26,
  "map": {
    "latitude": 24.7103488,
    "longitude": 46.6878464,
    "place_id": "ChIJhz3eOQADLz4R_cqAJEJr0Qw",
    "districtName": "3287 ,RHSA7555",
    "formattedAddress": "RHSA7555, 3287 Wadi Al Junah, As Sulimaniyah, 7555, Riyadh 12245, Saudi Arabia",
    "latitudeDelta": 0.02,
    "longitudeDelta": 0.009244060475161988,
    "mapsLink": "https://www.google.com/maps/search/?api=1&query=24.7103488,46.6878464"
  },
  "specifications": [],
  "features": []
}
```

**IMPORTANT: The `map` object must include ALL fields (latitude, longitude, place_id, districtName, formattedAddress, latitudeDelta, longitudeDelta, mapsLink). Partial map objects will cause a 400 error.**

## Known Issues

### Unit Creation - Partial Map Object Causes 400 Error
If the `map` object is missing any of the required fields (latitude, longitude, place_id, districtName, formattedAddress, latitudeDelta, longitudeDelta, mapsLink), the API returns:
```json
{
  "code": 400,
  "message": "messages.unit_creation_failed",
  "errors": []
}
```
**Solution**: Always include ALL map fields in the request.

### Endpoints Returning 500 Errors
- `GET /rf/units/import`
- `GET /rf/units/export`
- `GET /rf/units/bulk`
- `GET /rf/units/batch`
- `GET /rf/owners/create`

## Log Files Generated
- `unit-investigation-log.json` - Unit creation attempts
- `subscription-investigation-log.json` - Subscription/feature exploration
- `owners-investigation-log.json` - Owner-related exploration
- `complete-owner-log.json` - Successful owner creation
- `activate-property-log.json` - Activation attempts
- `minimal-unit-test-log.json` - Various unit creation attempts
- `unit-create-spec.json` - Full unit creation specification
- `create-unit-correct-log.json` - Successful unit creation via API
- `all-api-logs.json` - Consolidated API logs
- `endpoint-summary.json` - Summary of all tested endpoints

## Test Data Created
- **Community ID 1**: "Test Community 1" (Riyadh, Saudi Arabia)
- **Buildings**: IDs 1-4 (Test Building 1, Building A, Building B, Building C)
- **Owner ID 3**: Mohammed Al-Saud (+966500000002)
- **Units Created**:
  - ID 1: Unit 101 (Apartment, Test Building 1) - via Web UI
  - ID 2: Penthouse Suite 501 (Penthouse, Building A) - via Web UI
  - ID 3: Test Unit API v2 (Apartment, Test Building 1) - via API

## Subscription Plans Available
1. Starter Plan (89.55 SAR/month)
2. Pro Plan (details in subscription-investigation-log.json)
3. Enterprise Plan (details in subscription-investigation-log.json)

## Resolved Issues
- **Unit Creation**: Now working! The key was including the complete `map` object with all required fields.

## Next Steps (Optional)
1. Test unit update (PUT) endpoint
2. Test unit deletion
3. Explore leasing module
4. Explore accounting module
5. Test marketplace functionality
