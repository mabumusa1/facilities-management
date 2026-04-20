# API Test Log - 2026-04-20

## Successful Requests

### Communities (01-communities.sh)

**Request 1: Al Nakheel Residence**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/communities" \
  -H "Authorization: Bearer 1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv" \
  -H "X-Tenant: scantest2026apr" \
  -H "Content-Type: application/json" \
  -H "X-App-Locale: en" \
  -d '{
    "name": "Al Nakheel Residence",
    "country_id": 1,
    "currency_id": 1,
    "city_id": 1,
    "district_id": 15,
    "latitude": 24.7136,
    "longitude": 46.6753
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":3,...}}`

**Request 2: Business Park Tower**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/communities" \
  -H "Authorization: Bearer 1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv" \
  -H "X-Tenant: scantest2026apr" \
  -H "Content-Type: application/json" \
  -H "X-App-Locale: en" \
  -d '{
    "name": "Business Park Tower",
    "country_id": 1,
    "currency_id": 1,
    "city_id": 1,
    "district_id": 36,
    "latitude": 24.7500,
    "longitude": 46.6500
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":4,...}}`

---

### Buildings (02-buildings.sh)

**Request 1: Tower A in Community 3**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/buildings" \
  -H "Authorization: Bearer 1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv" \
  -H "X-Tenant: scantest2026apr" \
  -H "Content-Type: application/json" \
  -H "X-App-Locale: en" \
  -d '{
    "name": "Tower A",
    "rf_community_id": 3,
    "floors_count": 10
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":2,...}}`

**Request 2: Tower B in Community 3**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/buildings" \
  -d '{"name": "Tower B", "rf_community_id": 3, "floors_count": 8}'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":3,...}}`

**Request 3: Business Center in Community 4**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/buildings" \
  -d '{"name": "Business Center", "rf_community_id": 4, "floors_count": 5}'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":4,...}}`

---

### Owners (04-contacts.sh)

**Request 1: Ahmed Al-Mansouri**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/owners" \
  -H "Authorization: Bearer 1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv" \
  -H "X-Tenant: scantest2026apr" \
  -H "Content-Type: application/json" \
  -H "X-App-Locale: en" \
  -d '{
    "first_name": "Ahmed",
    "last_name": "Al-Mansouri",
    "phone_country_code": "SA",
    "phone_number": "501234567",
    "email": "ahmed.almansouri@example.com",
    "national_id": "1234567890"
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":6,...}}`

**Request 2: Fatima Al-Rashid**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/owners" \
  -d '{
    "first_name": "Fatima",
    "last_name": "Al-Rashid",
    "phone_country_code": "SA",
    "phone_number": "502345678",
    "email": "fatima.alrashid@example.com"
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":7,...}}`

---

### Tenants (04-contacts.sh)

**Request 1: Mohammed Al-Saud (Individual)**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/tenants" \
  -d '{
    "first_name": "Mohammed",
    "last_name": "Al-Saud",
    "phone_country_code": "SA",
    "phone_number": "509876543",
    "email": "mohammed.alsaud@example.com",
    "type": "individual",
    "national_id": "9876543210"
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":8,...}}`

**Request 2: Sara Al-Qahtani (Individual)**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/tenants" \
  -d '{
    "first_name": "Sara",
    "last_name": "Al-Qahtani",
    "phone_country_code": "SA",
    "phone_number": "508765432",
    "email": "sara.alqahtani@example.com",
    "type": "individual"
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":9,...}}`

**Request 3: Tech Solutions LLC (Company)**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/tenants" \
  -d '{
    "first_name": "Tech Solutions",
    "last_name": "LLC",
    "phone_country_code": "SA",
    "phone_number": "507654321",
    "email": "contact@techsolutions.sa",
    "type": "company",
    "company_name": "Tech Solutions LLC",
    "cr_number": "1234567890"
  }'
```
**Response:** `{"code":200,"message":"Record Created successfully.","data":{"id":10,...}}`

---

### Professionals (04-contacts.sh)

**Request 1: Ali Hassan (Maintenance)**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/professionals" \
  -d '{
    "first_name": "Ali",
    "last_name": "Hassan",
    "phone_country_code": "SA",
    "phone_number": "503333333",
    "email": "ali.hassan@maintenance.sa",
    "service_types": [1, 2]
  }'
```
**Response:**
```json
{
  "code": 200,
  "message": "Record Created successfully.",
  "data": {
    "id": 11,
    "name": "Ali Hassan",
    "image": null,
    "phone_number": "+966503333333",
    "job_title": null,
    "created_at": "2026-04-20T02:45:40.000000Z",
    "rate": 0
  },
  "meta": []
}
```

**Request 2: Omar Al-Mutairi (Electrician)**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/professionals" \
  -d '{
    "first_name": "Omar",
    "last_name": "Al-Mutairi",
    "phone_country_code": "SA",
    "phone_number": "504444444",
    "email": "omar.almutairi@electrical.sa",
    "service_types": [3]
  }'
```
**Response:**
```json
{
  "code": 200,
  "message": "Record Created successfully.",
  "data": {
    "id": 12,
    "name": "Omar Al-Mutairi",
    "image": null,
    "phone_number": "+966504444444",
    "job_title": null,
    "created_at": "2026-04-20T02:45:51.000000Z",
    "rate": 0
  },
  "meta": []
}
```

---

## Failed Requests

### Units (03-units.sh) - ALL FAILED

**Attempt 1: Basic payload**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/units" \
  -d '{"name": "Unit 201", "category_id": 2, "type_id": 17, "rf_community_id": 3}'
```
**Response:** `{"code":400,"message":"messages.unit_creation_failed","errors":[]}`

**Attempt 2: With building_id**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/units" \
  -d '{"name": "Unit 201", "category_id": 2, "type_id": 17, "rf_community_id": 3, "rf_building_id": 2}'
```
**Response:** `{"code":400,"message":"messages.unit_creation_failed","errors":[]}`

**Attempt 3: With status_id**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/units" \
  -d '{"name": "Unit 201", "category_id": 2, "type_id": 17, "rf_community_id": 3, "rf_building_id": 2, "rf_status_id": 26}'
```
**Response:** `{"code":400,"message":"messages.unit_creation_failed","errors":[]}`

**Attempt 4: String IDs**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/units" \
  -d '{"name": "Unit 201", "category_id": "2", "type_id": "17", "rf_community_id": "3"}'
```
**Response:** `{"code":400,"message":"messages.unit_creation_failed","errors":[]}`

**Attempt 5: Using existing community 1**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/units" \
  -d '{"name": "Unit 102", "category_id": 2, "type_id": 17, "rf_community_id": 1, "rf_building_id": 1}'
```
**Response:** `{"code":400,"message":"messages.unit_creation_failed","errors":[]}`

**Note:** The API returns a generic error with empty `errors` array, making it impossible to determine the actual validation issue. This may be a quota/permission restriction.

---

### Leases (05-leases.sh) - FAILED (302 Redirect)

**Request:**
```bash
curl -X POST "https://api.goatar.com/api-management/rf/leases" \
  -H "Authorization: Bearer 1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv" \
  -H "X-Tenant: scantest2026apr" \
  -H "Content-Type: application/json" \
  -H "X-App-Locale: en" \
  -d '{
    "autoGenerateLeaseNumber": true,
    "created_at": "2026-04-20",
    "start_date": "2026-05-01",
    "end_date": "2027-04-30",
    "handover_date": "2026-05-01",
    "number_of_years": 1,
    "lease_unit_type": 1,
    "tenant_type": "individual",
    "tenant": {
      "id": 8,
      "national_id": "9876543210"
    },
    "rental_type": "detailed",
    "rental_contract_type_id": 13,
    "payment_schedule_id": 4,
    "units": [
      {
        "id": 1,
        "rental_amount": 50000,
        "amount_type": "total"
      }
    ]
  }'
```

**Response (HTTP Headers):**
```
HTTP/2 302
location: http://api.goatar.com
x-ratelimit-limit: 5
x-ratelimit-remaining: 4
```

**Response Body:** HTML redirect page to `http://api.goatar.com`

**Note:** The API returns a 302 redirect instead of processing the request. This suggests the auth token doesn't have permission to create leases, or there's server-side middleware blocking the request.

---

## Verification Queries

**Count Communities:**
```bash
curl -s "https://api.goatar.com/api-management/rf/communities" \
  -H "Authorization: Bearer ..." -H "X-Tenant: scantest2026apr" | jq '.data | length'
# Result: 4
```

**Count Buildings:**
```bash
curl -s "https://api.goatar.com/api-management/rf/buildings" \
  -H "Authorization: Bearer ..." -H "X-Tenant: scantest2026apr" | jq '.data | length'
# Result: 4
```

**Count Units:**
```bash
curl -s "https://api.goatar.com/api-management/rf/units" \
  -H "Authorization: Bearer ..." -H "X-Tenant: scantest2026apr" | jq '.data | length'
# Result: 1 (pre-existing)
```

**Count Admins:**
```bash
curl -s "https://api.goatar.com/api-management/rf/admins" \
  -H "Authorization: Bearer ..." -H "X-Tenant: scantest2026apr" | jq '.data | length'
# Result: 2
```

**Count Professionals:**
```bash
curl -s "https://api.goatar.com/api-management/rf/professionals" \
  -H "Authorization: Bearer ..." -H "X-Tenant: scantest2026apr" | jq '.data | length'
# Result: 2 (IDs 11, 12)
```

---

## Existing Unit Structure (for reference)

**GET /rf/units/1:**
```json
{
  "id": 1,
  "name": "Unit 101",
  "category": {"id": 2, "name": "Residential"},
  "type": {"id": 17, "name": "Apartment"},
  "status": {"id": 26, "name": "Vacant"},
  "rf_community": {"id": 1, "name": "Test Community Alpha"},
  "rf_building": {"id": 1, "name": "Tower A"},
  "city": {"id": 1, "name": "Riyadh"},
  "district": {"id": 1, "name": "Al Diriyah"}
}
```

---

## Validation Error Encountered

**phone_country_code Error:**
- Initial attempt: `"phone_country_code": "+966"`
- Error: `"phone_country_code should not exceed 2 characters"`
- Fix: Use ISO code `"phone_country_code": "SA"`

---

## Created IDs Summary

```json
{
  "community_1": 3,
  "community_2": 4,
  "building_1": 2,
  "building_2": 3,
  "building_3": 4,
  "owner_1": 6,
  "owner_2": 7,
  "tenant_1": 8,
  "tenant_2": 9,
  "tenant_3": 10,
  "professional_1": 11,
  "professional_2": 12
}
```
