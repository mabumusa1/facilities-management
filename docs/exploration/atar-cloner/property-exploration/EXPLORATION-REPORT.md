# Property Exploration Report

**Generated:** 2026-04-10T22:12:28.894Z

**Total Requests:** 90

## Summary

| Category | Requests | Success | Errors |
|----------|----------|---------|--------|
| List communities | 5 | 5 | 0 |
| Create community - empty body | 1 | 0 | 1 |
| Create community - partial data | 1 | 0 | 1 |
| Store community - empty | 1 | 0 | 1 |
| List buildings | 3 | 3 | 0 |
| Create building - empty | 1 | 0 | 1 |
| Store building - empty | 1 | 0 | 1 |
| Create building - partial | 1 | 0 | 1 |
| List units | 5 | 5 | 0 |
| Create unit - empty | 1 | 0 | 1 |
| Store unit - empty | 1 | 0 | 1 |
| Create unit - partial | 1 | 0 | 1 |
| Lookup | 13 | 6 | 7 |
| Detail | 8 | 0 | 8 |
| Update | 9 | 0 | 9 |
| Delete | 6 | 0 | 6 |
| Get common lists | 1 | 1 | 0 |
| Create full community | 1 | 0 | 1 |
| Store full community | 1 | 0 | 1 |
| Create full building | 1 | 0 | 1 |
| Store full building | 1 | 0 | 1 |
| Create unit 1 | 1 | 0 | 1 |
| Store unit 1 | 1 | 0 | 1 |
| Create unit 2 | 1 | 0 | 1 |
| Store unit 2 | 1 | 0 | 1 |
| Create unit 3 | 1 | 0 | 1 |
| Store unit 3 | 1 | 0 | 1 |
| Filter | 14 | 14 | 0 |
| Excel | 7 | 0 | 7 |

## Validation Errors Discovered

### Community Creation
```json
{
  "name": [
    "الحقل الاسم مطلوب."
  ],
  "country_id": [
    "الحقل country id مطلوب."
  ],
  "currency_id": [
    "الحقل currency id مطلوب."
  ],
  "city_id": [
    "الحقل city id مطلوب."
  ],
  "district_id": [
    "الحقل district id مطلوب."
  ]
}
```

### Building Creation
```json
{
  "name": [
    "الحقل الاسم مطلوب."
  ],
  "rf_community_id": [
    "الحقل rf community id مطلوب."
  ]
}
```

### Unit Creation
```json
{
  "name": [
    "الحقل الاسم مطلوب."
  ],
  "category_id": [
    "الحقل category id مطلوب."
  ],
  "type_id": [
    "الحقل type id مطلوب."
  ],
  "rf_community_id": [
    "الحقل rf community id مطلوب."
  ]
}
```


## All Requests

### 1. GET /rf/communities?is_paginate=1
- **Status:** 200
- **Duration:** 1237ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المشاريع بنجاح",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 25
    }
  },
  "meta": []
}
```

### 2. GET /rf/communities?is_active=1
- **Status:** 200
- **Duration:** 526ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المشاريع بنجاح",
  "data": [],
  "meta": []
}
```

### 3. GET /rf/communities
- **Status:** 200
- **Duration:** 330ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المشاريع بنجاح",
  "data": [],
  "meta": []
}
```

### 4. GET /marketplace/admin/communities?is_paginate=1&is_market_place=0
- **Status:** 200
- **Duration:** 394ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 15
    }
  },
  "meta": []
}
```

### 5. GET /marketplace/admin/communities?is_paginate=1&is_market_place=1
- **Status:** 200
- **Duration:** 419ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 15
    }
  },
  "meta": []
}
```

### 6. POST /rf/communities
- **Status:** 422
- **Duration:** 511ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "country_id": [
      "الحقل country id مطلوب."
    ],
    "currency_id": [
      "الحقل currency id مطلوب."
    ],
    "city_id": [
      "الحقل city id مطلوب."
    ],
    "district_id": [
      "الحقل district id مطلوب."
    ]
  }
}
```

### 7. POST /rf/communities
- **Status:** 422
- **Duration:** 321ms
- **Request Body:**
```json
{
  "name_en": "Test Community",
  "name_ar": "مجتمع تجريبي"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "country_id": [
      "الحقل country id مطلوب."
    ],
    "currency_id": [
      "الحقل currency id مطلوب."
    ],
    "city_id": [
      "الحقل city id مطلوب."
    ],
    "district_id": [
      "الحقل district id مطلوب."
    ]
  }
}
```

### 8. POST /rf/communities/store
- **Status:** 405
- **Duration:** 505ms
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 9. GET /rf/buildings
- **Status:** 200
- **Duration:** 402ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المباني بنجاح",
  "data": [],
  "meta": []
}
```

### 10. GET /rf/buildings?is_active=1
- **Status:** 200
- **Duration:** 310ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المباني بنجاح",
  "data": [],
  "meta": []
}
```

### 11. GET /rf/buildings?is_paginate=1
- **Status:** 200
- **Duration:** 328ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المباني بنجاح",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 25
    }
  },
  "meta": []
}
```

### 12. POST /rf/buildings
- **Status:** 422
- **Duration:** 507ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 13. POST /rf/buildings/store
- **Status:** 405
- **Duration:** 414ms
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 14. POST /rf/buildings
- **Status:** 422
- **Duration:** 601ms
- **Request Body:**
```json
{
  "name_en": "Test Building",
  "name_ar": "مبنى تجريبي",
  "community_id": 1
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 15. GET /rf/units
- **Status:** 200
- **Duration:** 303ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 16. GET /rf/units?is_paginate=1
- **Status:** 200
- **Duration:** 323ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 25
    }
  },
  "meta": []
}
```

### 17. GET /rf/units?is_active=1
- **Status:** 200
- **Duration:** 404ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 18. GET /marketplace/admin/units
- **Status:** 200
- **Duration:** 333ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 19. GET /marketplace/admin/units?is_paginate=1
- **Status:** 200
- **Duration:** 302ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": {
    "list": [],
    "paginator": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 15
    }
  },
  "meta": []
}
```

### 20. POST /rf/units
- **Status:** 422
- **Duration:** 323ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 21. POST /rf/units/store
- **Status:** 405
- **Duration:** 410ms
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 22. POST /rf/units
- **Status:** 422
- **Duration:** 409ms
- **Request Body:**
```json
{
  "name_en": "Unit 101",
  "name_ar": "وحدة 101",
  "building_id": 1,
  "unit_type": "apartment"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 23. GET /rf/unit-types
- **Status:** 404
- **Duration:** 261ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 24. GET /rf/property-types
- **Status:** 404
- **Duration:** 271ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 25. GET /rf/amenities
- **Status:** 200
- **Duration:** 710ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 26. GET /rf/facilities
- **Status:** 200
- **Duration:** 301ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 27. GET /rf/floor-plans
- **Status:** 404
- **Duration:** 327ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 28. GET /rf/rental-types
- **Status:** 404
- **Duration:** 339ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 29. GET /rf/payment-schedules
- **Status:** 404
- **Duration:** 352ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 30. GET /rf/contract-types
- **Status:** 404
- **Duration:** 250ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 31. GET /rf/unit-statuses
- **Status:** 404
- **Duration:** 308ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 32. GET /rf/common-lists
- **Status:** 200
- **Duration:** 321ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [
    {
      "id": 1,
      "name": "معلومات الطلب غير صحيحة",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 2,
      "name": "تم إلغاءه بناءً على طلب الساكن",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 3,
      "name": "الساكن غير متوفر",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 4,
      "name": "خارج نطاق الضمان",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 5,
      "name": "أخرى",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 6,
      "name": "ليس ضمن نطاق خدمتي",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 7,
      "name": "لست متاح",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 8,
      "name": "طلب مكرر",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 9,
      "name": "أخرى",
      "created_at": null,
      "pri...
```

### 33. GET /rf/common-lists?type=unit_types
- **Status:** 200
- **Duration:** 306ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 34. GET /rf/common-lists?type=property_types
- **Status:** 200
- **Duration:** 321ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 35. GET /rf/common-lists?type=amenities
- **Status:** 200
- **Duration:** 315ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [],
  "meta": []
}
```

### 36. GET /rf/communities/1
- **Status:** 404
- **Duration:** 305ms
- **Response:**
```json
{
  "success": false,
  "message": "RfCommunit not Found."
}
```

### 37. GET /rf/communities/1/buildings
- **Status:** 404
- **Duration:** 321ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 38. GET /rf/communities/1/units
- **Status:** 404
- **Duration:** 313ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 39. GET /rf/buildings/1
- **Status:** 404
- **Duration:** 314ms
- **Response:**
```json
{
  "success": false,
  "message": "RfBuildin not Found."
}
```

### 40. GET /rf/buildings/1/units
- **Status:** 404
- **Duration:** 314ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 41. GET /rf/units/1
- **Status:** 404
- **Duration:** 515ms
- **Response:**
```json
{
  "success": false,
  "message": "RfUni not Found."
}
```

### 42. GET /rf/units/1/leases
- **Status:** 404
- **Duration:** 333ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 43. GET /rf/units/1/transactions
- **Status:** 404
- **Duration:** 500ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 44. PUT /rf/communities/1
- **Status:** 400
- **Duration:** 339ms
- **Response:**
```json
{
  "code": 400,
  "message": "حدث خطأ ما, برجاء المحاوله لاحقا.",
  "errors": []
}
```

### 45. PATCH /rf/communities/1
- **Status:** 400
- **Duration:** 383ms
- **Response:**
```json
{
  "code": 400,
  "message": "حدث خطأ ما, برجاء المحاوله لاحقا.",
  "errors": []
}
```

### 46. PUT /rf/buildings/1
- **Status:** 400
- **Duration:** 421ms
- **Response:**
```json
{
  "code": 400,
  "message": "حدث خطأ ما, برجاء المحاوله لاحقا.",
  "errors": []
}
```

### 47. PATCH /rf/buildings/1
- **Status:** 400
- **Duration:** 315ms
- **Response:**
```json
{
  "code": 400,
  "message": "حدث خطأ ما, برجاء المحاوله لاحقا.",
  "errors": []
}
```

### 48. PUT /rf/units/1
- **Status:** 422
- **Duration:** 410ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 49. PATCH /rf/units/1
- **Status:** 422
- **Duration:** 276ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 50. POST /rf/communities/1/update
- **Status:** 404
- **Duration:** 251ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 51. POST /rf/buildings/1/update
- **Status:** 404
- **Duration:** 512ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 52. POST /rf/units/1/update
- **Status:** 404
- **Duration:** 7590ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 53. DELETE /rf/communities/999999
- **Status:** 405
- **Duration:** 245ms
- **Response:**
```json
{
  "message": "The DELETE method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 54. DELETE /rf/buildings/999999
- **Status:** 405
- **Duration:** 442ms
- **Response:**
```json
{
  "message": "The DELETE method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 55. DELETE /rf/units/999999
- **Status:** 405
- **Duration:** 262ms
- **Response:**
```json
{
  "message": "The DELETE method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 56. POST /rf/communities/999999/delete
- **Status:** 404
- **Duration:** 442ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 57. POST /rf/buildings/999999/delete
- **Status:** 404
- **Duration:** 686ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 58. POST /rf/units/999999/delete
- **Status:** 404
- **Duration:** 7381ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 59. GET /rf/common-lists
- **Status:** 200
- **Duration:** 272ms
- **Response:**
```json
{
  "code": 200,
  "message": "",
  "data": [
    {
      "id": 1,
      "name": "معلومات الطلب غير صحيحة",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 2,
      "name": "تم إلغاءه بناءً على طلب الساكن",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 3,
      "name": "الساكن غير متوفر",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 4,
      "name": "خارج نطاق الضمان",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 5,
      "name": "أخرى",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 6,
      "name": "ليس ضمن نطاق خدمتي",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 7,
      "name": "لست متاح",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 8,
      "name": "طلب مكرر",
      "created_at": null,
      "priority": "0"
    },
    {
      "id": 9,
      "name": "أخرى",
      "created_at": null,
      "pri...
```

### 60. POST /rf/communities
- **Status:** 422
- **Duration:** 355ms
- **Request Body:**
```json
{
  "name_en": "Test Community 1775859200799",
  "name_ar": "مجتمع تجريبي 1775859200799",
  "description_en": "A test community for API exploration",
  "description_ar": "مجتمع تجريبي لاستكشاف API",
  "address_en": "123 Test Street",
  "address_ar": "شارع التجربة 123",
  "city_id": 1,
  "district_id": 1,
  "latitude": 24.7136,
  "longitude": 46.6753,
  "is_active": true,
  "status": "active"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "country_id": [
      "الحقل country id مطلوب."
    ],
    "currency_id": [
      "الحقل currency id مطلوب."
    ]
  }
}
```

### 61. POST /rf/communities/store
- **Status:** 405
- **Duration:** 298ms
- **Request Body:**
```json
{
  "name_en": "Test Community 1775859200799",
  "name_ar": "مجتمع تجريبي 1775859200799",
  "description_en": "A test community for API exploration",
  "description_ar": "مجتمع تجريبي لاستكشاف API",
  "address_en": "123 Test Street",
  "address_ar": "شارع التجربة 123",
  "city_id": 1,
  "district_id": 1,
  "latitude": 24.7136,
  "longitude": 46.6753,
  "is_active": true,
  "status": "active"
}
```
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 62. POST /rf/buildings
- **Status:** 422
- **Duration:** 408ms
- **Request Body:**
```json
{
  "name_en": "Test Building 1775859201452",
  "name_ar": "مبنى تجريبي 1775859201452",
  "description_en": "A test building",
  "description_ar": "مبنى تجريبي",
  "community_id": 1,
  "number_of_floors": 5,
  "number_of_units": 20,
  "year_built": 2020,
  "is_active": true
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 63. POST /rf/buildings/store
- **Status:** 405
- **Duration:** 409ms
- **Request Body:**
```json
{
  "name_en": "Test Building 1775859201452",
  "name_ar": "مبنى تجريبي 1775859201452",
  "description_en": "A test building",
  "description_ar": "مبنى تجريبي",
  "community_id": 1,
  "number_of_floors": 5,
  "number_of_units": 20,
  "year_built": 2020,
  "is_active": true
}
```
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 64. POST /rf/units
- **Status:** 422
- **Duration:** 1640ms
- **Request Body:**
```json
{
  "name_en": "Unit 100",
  "name_ar": "وحدة 100",
  "unit_number": "100",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "apartment",
  "floor_number": 1,
  "bedrooms": 1,
  "bathrooms": 1,
  "area": 100,
  "area_unit": "sqm",
  "rental_price": 50000,
  "sale_price": 500000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 65. POST /rf/units/store
- **Status:** 405
- **Duration:** 272ms
- **Request Body:**
```json
{
  "name_en": "Unit 100",
  "name_ar": "وحدة 100",
  "unit_number": "100",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "apartment",
  "floor_number": 1,
  "bedrooms": 1,
  "bathrooms": 1,
  "area": 100,
  "area_unit": "sqm",
  "rental_price": 50000,
  "sale_price": 500000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 66. POST /rf/units
- **Status:** 422
- **Duration:** 443ms
- **Request Body:**
```json
{
  "name_en": "Unit 101",
  "name_ar": "وحدة 101",
  "unit_number": "101",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "studio",
  "floor_number": 2,
  "bedrooms": 2,
  "bathrooms": 2,
  "area": 150,
  "area_unit": "sqm",
  "rental_price": 60000,
  "sale_price": 600000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 67. POST /rf/units/store
- **Status:** 405
- **Duration:** 1434ms
- **Request Body:**
```json
{
  "name_en": "Unit 101",
  "name_ar": "وحدة 101",
  "unit_number": "101",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "studio",
  "floor_number": 2,
  "bedrooms": 2,
  "bathrooms": 2,
  "area": 150,
  "area_unit": "sqm",
  "rental_price": 60000,
  "sale_price": 600000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 68. POST /rf/units
- **Status:** 422
- **Duration:** 320ms
- **Request Body:**
```json
{
  "name_en": "Unit 102",
  "name_ar": "وحدة 102",
  "unit_number": "102",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "villa",
  "floor_number": 3,
  "bedrooms": 3,
  "bathrooms": 3,
  "area": 200,
  "area_unit": "sqm",
  "rental_price": 70000,
  "sale_price": 700000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "الحقل الاسم مطلوب."
    ],
    "category_id": [
      "الحقل category id مطلوب."
    ],
    "type_id": [
      "الحقل type id مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

### 69. POST /rf/units/store
- **Status:** 405
- **Duration:** 405ms
- **Request Body:**
```json
{
  "name_en": "Unit 102",
  "name_ar": "وحدة 102",
  "unit_number": "102",
  "building_id": 1,
  "community_id": 1,
  "unit_type": "villa",
  "floor_number": 3,
  "bedrooms": 3,
  "bathrooms": 3,
  "area": 200,
  "area_unit": "sqm",
  "rental_price": 70000,
  "sale_price": 700000,
  "is_active": true,
  "is_available": true,
  "status": "available"
}
```
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 70. GET /rf/units?status=available
- **Status:** 200
- **Duration:** 522ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 71. GET /rf/units?status=occupied
- **Status:** 200
- **Duration:** 981ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 72. GET /rf/units?unit_type=apartment
- **Status:** 200
- **Duration:** 469ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 73. GET /rf/units?bedrooms=2
- **Status:** 200
- **Duration:** 722ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 74. GET /rf/units?min_price=10000&max_price=100000
- **Status:** 200
- **Duration:** 517ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 75. GET /rf/units?community_id=1
- **Status:** 200
- **Duration:** 480ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 76. GET /rf/units?building_id=1
- **Status:** 200
- **Duration:** 352ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 77. GET /rf/units?search=test
- **Status:** 200
- **Duration:** 2118ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 78. GET /rf/units?sort_by=price&sort_order=asc
- **Status:** 200
- **Duration:** 346ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 79. GET /rf/units?sort_by=created_at&sort_order=desc
- **Status:** 200
- **Duration:** 393ms
- **Response:**
```json
{
  "code": 200,
  "message": "messages.units_retrieved",
  "data": [],
  "meta": []
}
```

### 80. GET /rf/buildings?community_id=1
- **Status:** 200
- **Duration:** 3409ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المباني بنجاح",
  "data": [],
  "meta": []
}
```

### 81. GET /rf/buildings?search=test
- **Status:** 200
- **Duration:** 426ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المباني بنجاح",
  "data": [],
  "meta": []
}
```

### 82. GET /rf/communities?search=test
- **Status:** 200
- **Duration:** 1335ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المشاريع بنجاح",
  "data": [],
  "meta": []
}
```

### 83. GET /rf/communities?city_id=1
- **Status:** 200
- **Duration:** 471ms
- **Response:**
```json
{
  "code": 200,
  "message": "تم استرجاع المشاريع بنجاح",
  "data": [],
  "meta": []
}
```

### 84. GET /rf/units/export
- **Status:** 500
- **Duration:** 1327ms
- **Response:**
```json
{
  "message": "Server Error"
}
```

### 85. GET /rf/units/export/excel
- **Status:** 404
- **Duration:** 244ms
- **Response:**
```json
{
  "success": false,
  "message": "URL link not Found."
}
```

### 86. GET /rf/units/template
- **Status:** 500
- **Duration:** 300ms
- **Response:**
```json
{
  "message": "Server Error"
}
```

### 87. GET /rf/buildings/export
- **Status:** 500
- **Duration:** 451ms
- **Response:**
```json
{
  "message": "Server Error"
}
```

### 88. GET /rf/communities/export
- **Status:** 500
- **Duration:** 308ms
- **Response:**
```json
{
  "message": "Server Error"
}
```

### 89. POST /rf/units/import
- **Status:** 405
- **Duration:** 427ms
- **Response:**
```json
{
  "message": "The POST method is not supported for this route. Supported methods: GET, HEAD, PUT, PATCH."
}
```

### 90. POST /rf/excel-sheets
- **Status:** 422
- **Duration:** 299ms
- **Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "file": [
      "الحقل الملف مطلوب."
    ],
    "rf_community_id": [
      "الحقل rf community id مطلوب."
    ]
  }
}
```

