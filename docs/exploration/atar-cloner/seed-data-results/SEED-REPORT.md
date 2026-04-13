# Data Seeding Report

**Timestamp:** 2026-04-10T22:18:01.399Z

## Lookups Discovered

- Countries: 1
- Cities: 26
- Districts: 1049
- Valid Category ID: 2
- Valid Type ID: 5

## Created Data

- Communities: 1
- Buildings: 4
- Units: 0

## Validation Discovery

### community - Attempt 1
- Status: 200
- Body: `{"name":"Test Community 1","country_id":1,"currency_id":1,"city_id":1,"district_id":1}`

### building - Attempt 1
- Status: 200
- Body: `{"name":"Test Building 1","rf_community_id":1}`

### unit - Attempt 3
- Status: 422
- Body: `{"name":"Unit-Cat1-Type1","category_id":1,"type_id":1,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "category_id": [
    "category id غير موجود"
  ],
  "type_id": [
    "type id غير موجود"
  ]
}
```

### unit - Attempt 4
- Status: 422
- Body: `{"name":"Unit-Cat1-Type2","category_id":1,"type_id":2,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "category_id": [
    "category id غير موجود"
  ]
}
```

### unit - Attempt 5
- Status: 422
- Body: `{"name":"Unit-Cat1-Type3","category_id":1,"type_id":3,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "category_id": [
    "category id غير موجود"
  ]
}
```

### unit - Attempt 6
- Status: 422
- Body: `{"name":"Unit-Cat1-Type4","category_id":1,"type_id":4,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "category_id": [
    "category id غير موجود"
  ]
}
```

### unit - Attempt 7
- Status: 422
- Body: `{"name":"Unit-Cat1-Type5","category_id":1,"type_id":5,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "category_id": [
    "category id غير موجود"
  ]
}
```

### unit - Attempt 8
- Status: 422
- Body: `{"name":"Unit-Cat2-Type1","category_id":2,"type_id":1,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
{
  "type_id": [
    "type id غير موجود"
  ]
}
```

### unit - Attempt 9
- Status: 400
- Body: `{"name":"Unit-Cat2-Type2","category_id":2,"type_id":2,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
[]
```

### unit - Attempt 10
- Status: 400
- Body: `{"name":"Unit-Cat2-Type3","category_id":2,"type_id":3,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
[]
```

### unit - Attempt 11
- Status: 400
- Body: `{"name":"Unit-Cat2-Type4","category_id":2,"type_id":4,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
[]
```

### unit - Attempt 12
- Status: 400
- Body: `{"name":"Unit-Cat2-Type5","category_id":2,"type_id":5,"rf_community_id":1,"rf_building_id":1}`
- Errors:
```json
[]
```

