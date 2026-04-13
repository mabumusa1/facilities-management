# pretty-js clean extraction

Noise removed: powerbi, firebase, telemetry urls, malformed endpoint fragments.

Routes: 42
API bindings: 29
API endpoints: 129
Frontend logic signals: 59

## Routes

1. /contacts
2. /contacts/${t}/form
3. /contacts/${uU.Manager}
4. /contacts/${uU.Manager}/form
5. /contacts/${uU.Owner}
6. /contacts/${uU.ServiceProfessional}
7. /contacts/${uU.Tenant}
8. /dashboard
9. /dashboard/booking-contracts
10. /dashboard/bookings
11. /dashboard/directory
12. /dashboard/issues
13. /dashboard/offers
14. /dashboard/reports
15. /dashboard/suggestions
16. /dashboard/system-reports
17. /dashboard/system-reports/Lease
18. /dashboard/system-reports/maintenance
19. /dashboard/visits
20. /leasing
21. /leasing/apps
22. /leasing/leases
23. /leasing/quotes
24. /leasing/visits
25. /marketplace
26. /marketplace/customers
27. /marketplace/listing
28. /more
29. /pricing
30. /properties-list
31. /properties-list/buildings
32. /properties-list/communities
33. /properties-list/units
34. /requests
35. /requests?type=${fU.homeServices}
36. /requests?type=${fU.neighbourhoodServices}
37. /requests/${t?.id}?type=${t?.category}
38. /requests/create?type=${n}
39. /requests/history?type=${n}
40. /transactions
41. /visitor-access
42. /visitor-access/visitor-details/${t?.id}

## API variable bindings

1. _$ -> co /api-management/rf/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
2. b$ -> co /api-management/rf/${e}/attach/property/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
3. C$ -> co /api-management/rf/companies/change-status/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
4. c3 -> co /api-management/rf/requests/categories/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
5. d3 -> lo /api-management/rf/requests/categories (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
6. DU -> uo /api-management/request-category (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L961 (srcL57961)
7. e -> co /api-management/rf/requests/change-status/canceled (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L944 (srcL86944)
8. E6 -> uo /api-management/rf/facilities/${t} (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L581 (srcL91581)
9. EU -> co /api-management/request-category (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L961 (srcL57961)
10. GJ -> co /api-management/marketplace/admin/settings/sales/store (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
11. h3 -> co /api-management/rf/requests/service-settings/updateOrCreate (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
12. i3 -> lo /api-management/rf/requests/types/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
13. J -> lo /api-management/marketplace/admin/settings/visits (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
14. j6 -> co /api-management/rf/facilities (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L581 (srcL91581)
15. k$ -> lo /api-management/rf/users/rates/${e} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
16. KJ -> lo /api-management/marketplace/admin/settings/sales (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
17. L$ -> lo /api-management/rf/attach/community/${e}?is_paginate=1&query=${n}&page=${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
18. l3 -> po /api-management/rf/requests/types/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
19. M$ -> lo /api-management/rf/${e}/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
20. o3 -> co /api-management/rf/requests/sub-categories/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
21. OU -> lo /api-management/rf/requests/download/${t}/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L971 (srcL57971)
22. P$ -> lo /api-management/rf/${t}/${e} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L972 (srcL75972)
23. p3 -> lo /api-management/rf/requests/service-settings/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
24. qJ -> co /api-management/marketplace/admin/settings/banks/store (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
25. S$ -> lo /api-management/rf/attach/building/${e}?is_paginate=1&query=${n}&page=${t}&is_active=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
26. s3 -> co /api-management/rf/requests/types/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
27. T$ -> co /api-management/rf/professionals/attach/category/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
28. u3 -> lo /api-management/rf/users/requests/available-slots?rf_sub_category_id=${e}&start_date=${t} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
29. w$ -> co /api-management/rf/${e}/change-status/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)

## API modules by volume

1. marketplace/admin -> 27
2. rf/users -> 13
3. rf/requests -> 12
4. rf/leases -> 9
5. rf/communities -> 5
6. rf/${e} -> 4
7. new/complaints -> 3
8. rf/admins -> 3
9. rf/announcements -> 3
10. rf/facilities -> 3
11. rf/leads -> 3
12. rf/${t} -> 2
13. rf/attach -> 2
14. rf/buildings -> 2
15. rf/companies -> 2
16. rf/sub-leases -> 2
17. contacts?role=${t}&search=${e}&sort_dir=latest&page=${n}&active=1 -> 1
18. contacts/${e} -> 1
19. countries -> 1
20. dashboard/require-attentions -> 1
21. dashboard/requires-attention -> 1
22. leases/${e} -> 1
23. marketplace/favorites -> 1
24. new/complaints?search=${e}&page=${t}&status -> 1
25. notifications?per_page=${t}&page=${e} -> 1

## API endpoints

1. /api-management/rf/leads/${e} (count 4) :: chunks/chunk-0101-L100001-L101000-S072.js:L435 (srcL100435) | chunks/chunk-0101-L100001-L101000-S072.js:L462 (srcL100462)
2. /api-management/rf/leases (count 4) :: chunks/chunk-0077-L076001-L077000-S088.js:L130 (srcL76130) | chunks/chunk-0091-L090001-L091000-S059.js:L923 (srcL90923)
3. /api-management/rf/admins (count 3) :: chunks/chunk-0101-L100001-L101000-S072.js:L870 (srcL100870) | chunks/chunk-0116-L115001-L116000-S034.js:L579 (srcL115579)
4. /api-management/rf/communities/${e} (count 3) :: chunks/chunk-0122-L121001-L122000-S077.js:L317 (srcL121317) | chunks/chunk-0120-L119001-L120000-S060.js:L245 (srcL119245)
5. /api-management/rf/leads (count 3) :: chunks/chunk-0101-L100001-L101000-S072.js:L370 (srcL100370) | chunks/chunk-0101-L100001-L101000-S072.js:L410 (srcL100410)
6. /api-management/rf/leases/${e} (count 3) :: chunks/chunk-0095-L094001-L095000-S096.js:L100 (srcL94100) | chunks/chunk-0091-L090001-L091000-S059.js:L569 (srcL90569)
7. /api-management/rf/requests/categories (count 3) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174) | chunks/chunk-0058-L057001-L058000-S076.js:L961 (srcL57961)
8. /api-management/marketplace/admin/visits (count 2) :: chunks/chunk-0099-L098001-L099000-S064.js:L935 (srcL98935) | chunks/chunk-0100-L099001-L100000-S049.js:L46 (srcL99046)
9. /api-management/request-category (count 2) :: chunks/chunk-0058-L057001-L058000-S076.js:L961 (srcL57961) | chunks/chunk-0058-L057001-L058000-S076.js:L961 (srcL57961)
10. /api-management/rf/announcements (count 2) :: chunks/chunk-0085-L084001-L085000-S055.js:L591 (srcL84591) | chunks/chunk-0085-L084001-L085000-S055.js:L603 (srcL84603)
11. /api-management/rf/communities?is_paginate=1 (count 2) :: chunks/chunk-0089-L088001-L089000-S048.js:L685 (srcL88685) | chunks/chunk-0089-L088001-L089000-S048.js:L716 (srcL88716)
12. /api-management/rf/companies/${e} (count 2) :: chunks/chunk-0076-L075001-L076000-S116.js:L947 (srcL75947) | chunks/chunk-0077-L076001-L077000-S088.js:L20 (srcL76020)
13. /api-management/rf/facilities/${e} (count 2) :: chunks/chunk-0092-L091001-L092000-S050.js:L581 (srcL91581) | chunks/chunk-0093-L092001-L093000-S018.js:L324 (srcL92324)
14. /api-management/rf/requests/types/${e} (count 2) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174) | chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
15. /api-management/rf/sub-leases/${e} (count 2) :: chunks/chunk-0096-L095001-L096000-S028.js:L34 (srcL95034) | chunks/chunk-0096-L095001-L096000-S028.js:L83 (srcL95083)
16. /api-management/contacts?role=${t}&search=${e}&sort_dir=latest&page=${n}&active=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L905 (srcL75905)
17. /api-management/contacts/${e}/accept-privacy-policy (count 1) :: chunks/chunk-0084-L083001-L084000-S039.js:L796 (srcL83796)
18. /api-management/countries (count 1) :: chunks/chunk-0003-L002001-L003000-S151.js:L111 (srcL2111)
19. /api-management/dashboard/require-attentions/expiringLeases?type=${e}&page=${t} (count 1) :: chunks/chunk-0090-L089001-L090000-S047.js:L275 (srcL89275)
20. /api-management/dashboard/requires-attention (count 1) :: chunks/chunk-0085-L084001-L085000-S055.js:L423 (srcL84423)
21. /api-management/leases/${e} (count 1) :: chunks/chunk-0124-L123001-L124000-S236.js:L703 (srcL123703)
22. /api-management/marketplace/admin/bookings/change-status/send-contract/${e} (count 1) :: chunks/chunk-0123-L122001-L123000-S009.js:L991 (srcL122991)
23. /api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0 (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L873 (srcL99873)
24. /api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0&is_off_plan_sale=0 (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L882 (srcL99882)
25. /api-management/marketplace/admin/communities?is_paginate=1&is_market_place=1 (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L843 (srcL99843)
26. /api-management/marketplace/admin/communities/list/${e} (count 1) :: chunks/chunk-0118-L117001-L118000-S032.js:L705 (srcL117705)
27. /api-management/marketplace/admin/communities/resend/bulk-payments/${e} (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L380 (srcL119380)
28. /api-management/marketplace/admin/communities/resend/bulk-reminder/${e} (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L395 (srcL119395)
29. /api-management/marketplace/admin/communities/resend/payment-schedules/failed/${e} (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L210 (srcL119210)
30. /api-management/marketplace/admin/communities/unlist/${e} (count 1) :: chunks/chunk-0118-L117001-L118000-S032.js:L383 (srcL117383)
31. /api-management/marketplace/admin/communities/update-sales-information/${e} (count 1) :: chunks/chunk-0122-L121001-L122000-S077.js:L362 (srcL121362)
32. /api-management/marketplace/admin/settings/banks (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
33. /api-management/marketplace/admin/settings/banks/store (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
34. /api-management/marketplace/admin/settings/sales (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
35. /api-management/marketplace/admin/settings/sales/store (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
36. /api-management/marketplace/admin/settings/visits (count 1) :: chunks/chunk-0081-L080001-L081000-S150.js:L52 (srcL80052)
37. /api-management/marketplace/admin/settings/visits/store (count 1) :: chunks/chunk-0101-L100001-L101000-S072.js:L320 (srcL100320)
38. /api-management/marketplace/admin/units (count 1) :: chunks/chunk-0101-L100001-L101000-S072.js:L541 (srcL100541)
39. /api-management/marketplace/admin/units/${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L574 (srcL118574)
40. /api-management/marketplace/admin/units/missing/${a} (count 1) :: chunks/chunk-0101-L100001-L101000-S072.js:L553 (srcL100553)
41. /api-management/marketplace/admin/units/prices-visibility/${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L656 (srcL118656)
42. /api-management/marketplace/admin/units/prices-visibility/all/${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L644 (srcL118644)
43. /api-management/marketplace/admin/units/statistic/${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L495 (srcL118495)
44. /api-management/marketplace/admin/visits/${e} (count 1) :: chunks/chunk-0099-L098001-L099000-S064.js:L985 (srcL98985)
45. /api-management/marketplace/admin/visits/assign/owner-visit/${e} (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L127 (srcL99127)
46. /api-management/marketplace/admin/visits/completed/${e} (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L186 (srcL99186)
47. /api-management/marketplace/admin/visits/rejected/${e} (count 1) :: chunks/chunk-0100-L099001-L100000-S049.js:L168 (srcL99168)
48. /api-management/marketplace/favorites/communities/${e} (count 1) :: chunks/chunk-0118-L117001-L118000-S032.js:L566 (srcL117566)
49. /api-management/new/complaints?search=${e}&page=${t}&status (count 1) :: chunks/chunk-0080-L079001-L080000-S065.js:L51 (srcL79051)
50. /api-management/new/complaints/${e}/assign (count 1) :: chunks/chunk-0079-L078001-L079000-S023.js:L930 (srcL78930)
51. /api-management/new/complaints/${e}/cancel (count 1) :: chunks/chunk-0079-L078001-L079000-S023.js:L864 (srcL78864)
52. /api-management/new/complaints/${e}/resolve (count 1) :: chunks/chunk-0080-L079001-L080000-S065.js:L646 (srcL79646)
53. /api-management/notifications?per_page=${t}&page=${e} (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L14 (srcL86014)
54. /api-management/notifications/${e}/mark-as-read (count 1) :: chunks/chunk-0056-L055001-L056000-S026.js:L488 (srcL55488)
55. /api-management/notifications/mark-all-as-read (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L45 (srcL86045)
56. /api-management/notifications/unread-count (count 1) :: chunks/chunk-0059-L058001-L059000-S045.js:L772 (srcL58772)
57. /api-management/request-category/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L969 (srcL57969)
58. /api-management/request-sub-category/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L964 (srcL57964)
59. /api-management/rf/${e} (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L141 (srcL76141)
60. /api-management/rf/${e}/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
61. /api-management/rf/${e}/attach/property/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
62. /api-management/rf/${e}/change-status/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
63. /api-management/rf/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
64. /api-management/rf/${t}/${e} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L972 (srcL75972)
65. /api-management/rf/admins?query=${e}&sort_dir=latest&page=${t}&is_paginate=1&active=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L936 (srcL75936)
66. /api-management/rf/admins/check-validate (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L269 (srcL76269)
67. /api-management/rf/admins/manager-roles (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L241 (srcL76241)
68. /api-management/rf/announcements/${e} (count 1) :: chunks/chunk-0085-L084001-L085000-S055.js:L550 (srcL84550)
69. /api-management/rf/announcements/${t} (count 1) :: chunks/chunk-0085-L084001-L085000-S055.js:L591 (srcL84591)
70. /api-management/rf/attach/building/${e}?is_paginate=1&query=${n}&page=${t}&is_active=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
71. /api-management/rf/attach/community/${e}?is_paginate=1&query=${n}&page=${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
72. /api-management/rf/buildings (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L352 (srcL118352)
73. /api-management/rf/buildings?is_active=1& (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L265 (srcL76265)
74. /api-management/rf/buildings?rf_community_id=${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L410 (srcL118410)
75. /api-management/rf/buildings/${e} (count 1) :: chunks/chunk-0119-L118001-L119000-S053.js:L386 (srcL118386)
76. /api-management/rf/common-lists (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L935 (srcL86935)
77. /api-management/rf/communities?is_active=1& (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L260 (srcL76260)
78. /api-management/rf/communities?is_paginate=1&has_facility=1&search=${e}&page=${t} (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L584 (srcL91584)
79. /api-management/rf/communities/${e}/off-plan-sale/license (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L347 (srcL119347)
80. /api-management/rf/communities/${e}/off-plan-sale/payments/${t}/complete (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L365 (srcL119365)
81. /api-management/rf/communities/edaat/product-codes (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L149 (srcL119149)
82. /api-management/rf/communities/off-plan-sale (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L325 (srcL119325)
83. /api-management/rf/companies?is_paginate=1&page=${t}&search=${e}&is_active=${n} (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L181 (srcL76181)
84. /api-management/rf/companies?search=${e}&sort_dir=latest&page=${t}&is_paginate=1&is_active=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L923 (srcL75923)
85. /api-management/rf/companies/change-status/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
86. /api-management/rf/contacts/statistics (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L208 (srcL76208)
87. /api-management/rf/facilities (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L581 (srcL91581)
88. /api-management/rf/facilities?query=${e}&community_id=${t} (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L954 (srcL91954)
89. /api-management/rf/facilities/${t} (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L581 (srcL91581)
90. /api-management/rf/family-members?parent_id=${e} (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L238 (srcL76238)
91. /api-management/rf/leads/${e}/convert (count 1) :: chunks/chunk-0116-L115001-L116000-S034.js:L995 (srcL115995)
92. /api-management/rf/leases/change-status/move-out (count 1) :: chunks/chunk-0098-L097001-L098000-S020.js:L224 (srcL97224)
93. /api-management/rf/leases/change-status/terminate (count 1) :: chunks/chunk-0098-L097001-L098000-S020.js:L230 (srcL97230)
94. /api-management/rf/leases/create (count 1) :: chunks/chunk-0091-L090001-L091000-S059.js:L575 (srcL90575)
95. /api-management/rf/leases/renew/step-${t} (count 1) :: chunks/chunk-0091-L090001-L091000-S059.js:L991 (srcL90991)
96. /api-management/rf/leases/renew/store (count 1) :: chunks/chunk-0091-L090001-L091000-S059.js:L946 (srcL90946)
97. /api-management/rf/leases/statistics (count 1) :: chunks/chunk-0097-L096001-L097000-S027.js:L469 (srcL96469)
98. /api-management/rf/leases/step-${t} (count 1) :: chunks/chunk-0091-L090001-L091000-S059.js:L970 (srcL90970)
99. /api-management/rf/modules (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L979 (srcL57979)
100. /api-management/rf/professionals/attach/category/${t} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
101. /api-management/rf/related-companies?company_id=${e} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L983 (srcL75983)
102. /api-management/rf/requests/categories/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
103. /api-management/rf/requests/change-status/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L869 (srcL57869)
104. /api-management/rf/requests/change-status/canceled (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L944 (srcL86944)
105. /api-management/rf/requests/download/${t}/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L971 (srcL57971)
106. /api-management/rf/requests/service-settings/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
107. /api-management/rf/requests/service-settings/updateOrCreate (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
108. /api-management/rf/requests/sub-categories?category_id=${e}${n} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L170 (srcL87170)
109. /api-management/rf/requests/sub-categories/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
110. /api-management/rf/requests/types/change-status/${e} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
111. /api-management/rf/requests/types/list/${e}${n} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L173 (srcL87173)
112. /api-management/rf/statuses (count 1) :: chunks/chunk-0003-L002001-L003000-S151.js:L120 (srcL2120)
113. /api-management/rf/sub-leases (count 1) :: chunks/chunk-0096-L095001-L096000-S028.js:L56 (srcL95056)
114. /api-management/rf/tenants (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L152 (srcL76152)
115. /api-management/rf/tenants?query=${e}&sort_dir=latest&page=${t}&is_paginate=1&active=1&not_has_family_member=1 (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L910 (srcL75910)
116. /api-management/rf/transactions/ (count 1) :: chunks/chunk-0077-L076001-L077000-S088.js:L227 (srcL76227)
117. /api-management/rf/users/rates/${e} (count 1) :: chunks/chunk-0076-L075001-L076000-S116.js:L901 (srcL75901)
118. /api-management/rf/users/requests (count 1) :: chunks/chunk-0090-L089001-L090000-S047.js:L77 (srcL89077)
119. /api-management/rf/users/requests/${e} (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L894 (srcL57894)
120. /api-management/rf/users/requests/${t} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L330 (srcL87330)
121. /api-management/rf/users/requests/available-slots?rf_sub_category_id=${e}&start_date=${t} (count 1) :: chunks/chunk-0088-L087001-L088000-S082.js:L174 (srcL87174)
122. /api-management/rf/users/requests/categories?is_paginate=0 (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L892 (srcL57892)
123. /api-management/rf/users/requests/professionals?rf_request_id=${e} (count 1) :: chunks/chunk-0087-L086001-L087000-S041.js:L809 (srcL86809)
124. /api-management/rf/users/requests/sub-categories?has_types=true&category_id= (count 1) :: chunks/chunk-0089-L088001-L089000-S048.js:L970 (srcL88970)
125. /api-management/rf/users/requests/types (count 1) :: chunks/chunk-0058-L057001-L058000-S076.js:L871 (srcL57871)
126. /api-management/rf/users/visitor-access (count 1) :: chunks/chunk-0099-L098001-L099000-S064.js:L236 (srcL98236)
127. /api-management/rf/users/visitor-access/${e} (count 1) :: chunks/chunk-0099-L098001-L099000-S064.js:L216 (srcL98216)
128. /api-management/rf/users/visitor-access/${e}/approve (count 1) :: chunks/chunk-0099-L098001-L099000-S064.js:L406 (srcL98406)
129. /api-management/rf/users/visitor-access/${e}/reject (count 1) :: chunks/chunk-0099-L098001-L099000-S064.js:L439 (srcL98439)

## Key values

### localStorage keys

1. token
2. X-Tenant
3. user
4. loggedIn
5. ejar
6. plan
7. planFeatures

### API/tenant config values

1. _O = fcm_token_details_db
2. ao = https://api.goatar.com
3. CF = TENANTS
4. MF = TENANTS_All
5. MoveInTenantToLease = MOVE_IN_TENANT_TO_LEASE
6. MoveOutTenantToLease = MOVE_OUT_TENANT_TO_LEASE
7. TENANT = tenant
8. Tenant = tenants
9. Tenant = Tenants
10. TENANT_DETAILS_STEP = tenantDetailsStep
11. TENANT_TYPE = tenantType
12. Tenants = tenants
13. TenantStatementReports = tenantStatementReports
14. vZ = https://api.goatar.com
15. xO = fcm_token_object_Store

### Domain constants

1. COMMUNITY = community (count 4) :: chunks/chunk-0120-L119001-L120000-S060.js:L142 (srcL119142) | chunks/chunk-0092-L091001-L092000-S050.js:L666 (srcL91666)
2. COMPANY = company (count 3) :: chunks/chunk-0076-L075001-L076000-S116.js:L833 (srcL75833) | chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
3. RENT = rent (count 2) :: chunks/chunk-0099-L098001-L099000-S064.js:L813 (srcL98813) | chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
4. ABOUT_THIS_UNIT = about (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
5. ACCEPTED_LEASE_QUOTE_NOTIFICATION = marketplace_lease_quote_accepted_by_customer (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
6. ADD_BOOKING_COMMENT_NOTIFICATION = MARKETPLACE_BOOKING_UNIT_COMMENT (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
7. AUTO_GEN_LEAS_NUM = autoGenerateLeaseNumber (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
8. BOOKINGS = FACILITY_BOOKING (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
9. BUILDING = building (count 1) :: chunks/chunk-0092-L091001-L092000-S050.js:L666 (srcL91666)
10. BUILDING_CONTEXT = buildingContext (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
11. BUY_AMOUNT_TYPE = buyAmountType (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
12. CAN_ADD_RENTAL_DETAILS = canAddRentalDetails (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
13. CANCELED_MARKETPLACE_VISIT = canceled_marketplace_visit (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
14. CATEGORY_ID = category_id (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
15. COMMUNITY_CONTEXT = communityContext (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
16. COMMUNITY_ID = communityId (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L142 (srcL119142)
17. COMMUNITY_NAME = communityName (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L142 (srcL119142)
18. COMPANY_ID = company_id (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
19. COMPANY_LOGO = companyLogo (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
20. COMPANY_NAME_AR = companyNameAr (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
21. COMPANY_NAME_EN = companyNameEn (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
22. COMPANY_REGISTRATION_NO = companyRegistrationNo (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
23. COMPLAINT = COMPLAINT (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
24. COMPLETED_MARKETPLACE_VISIT = completed_marketplace_visit (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
25. CONTRACT_CREATION_DATE = contractCreationDate (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
26. CONTRACT_DATES_STEP = contractDatesStep (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
27. ESCALATION_END_DATE = end_date (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
28. ESCALATION_START_DATE = start_date (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
29. ESCALATION_TYPE = escalationType (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
30. ESCALATION_VALUE = escalationValue (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
31. ESCALATIONS = escalations (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
32. HAS_LEASE_ESCALATION = hasLeaseEscalation (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
33. LEASE_DETAILS_STEP = leaseDetailsStep (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
34. LEASE_END_DATE = leaseEndDate (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
35. LEASE_ID = rf_lease_id (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
36. LEASE_NUMBER = leaseNumber (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
37. LEASE_START_DATE = leaseStartDate (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
38. LEASE_TYPE = lease_unit_type (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
39. LEASING = lease (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
40. MARKETPLACE = marketplace (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
41. MARKETPLACE_APPROVE_RENT_APPLICATION_LEASE = marketplace_approve_rent_application_lease (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
42. MARKETPLACE_BOOKING_ALL_UNIT_PAYMENT_NOTIFICATIONS_SENT = marketplace_booking_all_unit_payment_notifications_sent (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
43. MARKETPLACE_BOOKING_APPROVED = marketplace_booking_unit_approved (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
44. MARKETPLACE_BOOKING_CONTRACT_CANCELLED = marketplace_booking_unit_contract_cancelled (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
45. MARKETPLACE_BOOKING_CONTRACT_SENT = marketplace_booking_unit_contract_sent (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
46. MARKETPLACE_BOOKING_CONTRACT_SIGNED = marketplace_booking_unit_contract_signed (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
47. MARKETPLACE_BOOKING_OWNERSHIP_TRANSFERRED = marketplace_booking_unit_ownership_transferred (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
48. MARKETPLACE_BOOKING_PAYMENT_AMOUNT = marketplace_booking_unit_payment_amount (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
49. MARKETPLACE_BOOKING_PROPERTY_APPROVED = MARKETPLACE_BOOKING_PROEPRTY_APPROVED (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
50. MARKETPLACE_BOOKING_PROPERTY_CANCELED = MARKETPLACE_BOOKING_PROEPRTY_CANCELED (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
51. MARKETPLACE_BOOKING_PROPERTY_NEW = MARKETPLACE_BOOKING_PROEPRTY_NEW (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
52. MARKETPLACE_BOOKING_UNIT_CANCELED = marketplace_booking_unit_canceled (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
53. MARKETPLACE_BOOKING_UNIT_CONFIRMED = marketplace_booking_unit_confirmed (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
54. MARKETPLACE_BOOKING_UNIT_NEW = marketplace_booking_unit_new (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
55. MARKETPLACE_BOOKING_UNIT_PAYMENT_REMINDER = marketplace_booking_unit_payment_reminder (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
56. MARKETPLACE_BOOKING_UNIT_PAYMENT_SENT = marketplace_booking_unit_payment_notifications_sent (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
57. MARKETPLACE_BOOKING_UNIT_REJECTED = marketplace_booking_unit_rejected (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
58. MARKETPLACE_COMMUNITY_INTEREST = marketplace_community_interest (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
59. MARKETPLACE_LEASE_QUOTE_CANCELED_BY_ADMIN = marketplace_lease_quote_canceled_by_admin (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
60. MARKETPLACE_NEW_RENT_APPLICATION_LEASE = marketplace_new_rent_application_lease (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
61. MARKETPLACE_REJECT_RENT_APPLICATION_LEASE_UNIT = marketplace_reject_rent_application_lease (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
62. NEW_BOOKING = NewBookingRequestNotification (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
63. NEW_LEASE_QUOTE_NOTIFICATION = marketplace_lease_quote_new_by_customer (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
64. NOTIFICATION_CLICKED = notification-clicked (count 1) :: chunks/chunk-0042-L041001-L042000-S002.js:L434 (srcL41434)
65. OFFER_REQUEST = OFFER_REQUEST (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
66. PAYMENT = PAYMENT (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
67. PAYMENT_FREQUENCY = paymentFrequency (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
68. PAYMENTS = payments (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L142 (srcL119142)
69. PER_UNIT = detailed (count 1) :: chunks/chunk-0091-L090001-L091000-S059.js:L21 (srcL90021)
70. REJECTED_LEASE_QUOTE_NOTIFICATION = marketplace_lease_quote_rejected_by_customer (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
71. REJECTED_MARKETPLACE_VISIT = rejected_marketplace_visit (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
72. RENT_AMOUNT_TYPE = rentAmountType (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
73. RENT_INDEX = rentIndex (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
74. RENTAL_AMOUNT = rental_amount (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
75. RENTAL_CONTRACT_TYPE = rentalContractType (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
76. RENTAL_PAYMENT_TYPE_UNIT = amount_type (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
77. RENTAL_TRANSACTION_SCHEDULE = rentalTransactionSchedule (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
78. REQUESTS = REQUEST (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
79. REVIEW_TRANSACTIONS = reviewTransactions (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
80. RF_BUILDING_ID = rf_building_id (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
81. RF_COMMUNITY_ID = rf_community_id (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
82. SCHEDULED_MARKETPLACE_VISIT = scheduled_marketplace_visit (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
83. SEND_PAYMENT = sendPayment (count 1) :: chunks/chunk-0120-L119001-L120000-S060.js:L232 (srcL119232)
84. SERVICE_REQUEST_COMMENT = SERVICE_REQUEST_COMMENT (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
85. TENANT = tenant (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
86. TENANT_DETAILS_STEP = tenantDetailsStep (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
87. TENANT_TYPE = tenantType (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
88. TOTAL_ANNUAL_FOR_ALL_UNITS = rental_total_amount (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
89. TOTAL_RENT = totalRental (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
90. TRANSACTION = TRANSACTION (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)
91. TRANSACTIONS_MARK_AS_PAID = transactionsMarkAsPaid (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
92. TYPE = type (count 1) :: chunks/chunk-0121-L120001-L121000-S004.js:L19 (srcL120019)
93. UNIT_SELECTION_STEP = unitSelectionStep (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
94. UNITS = units (count 1) :: chunks/chunk-0002-L001001-L002000-S014.js:L105 (srcL1105)
95. VISITOR_REQ = VISITOR_ACCESS_REQUEST (count 1) :: chunks/chunk-0053-L052001-L053000-S025.js:L508 (srcL52508)

## Frontend logic signals

1. chunks/chunk-0125-L124001-L124909-S284.js:L482 (srcL124482) :: invalidateQueries(e, t, n) {
2. chunks/chunk-0125-L124001-L124909-S284.js:L677 (srcL124677) :: } = Gc(), [y, v] = Dt.useState(JSON.parse(localStorage.getItem("ejar"))), _ = Ft(), x = Ht(), {
3. chunks/chunk-0003-L002001-L003000-S151.js:L709 (srcL2709) :: t && t.refetch({
4. chunks/chunk-0003-L002001-L003000-S151.js:L716 (srcL2716) :: t && t.refetch({
5. chunks/chunk-0095-L094001-L095000-S096.js:L106 (srcL94106) :: o(!1), l(!0), r.invalidateQueries([_H]), r.invalidateQueries([CH]), r.invalidateQueries([vH]), r.invalidateQueries([_F]), r.invalidateQueries([aF]), r.invalidateQueries(["SUBLEASES"]), r.invalidateQueries([tF])
6. chunks/chunk-0075-L074001-L075000-S083.js:L819 (srcL74819) :: if (s.invalidateQueries(t?.[0]), i.onSuccess && c === l.Config.length - 1) return void i.onSuccess(n);
7. chunks/chunk-0075-L074001-L075000-S083.js:L837 (srcL74837) :: if (s.invalidateQueries(t[0]), i.onSuccess) return void i.onSuccess(n);
8. chunks/chunk-0088-L087001-L088000-S082.js:L333 (srcL87333) :: o.invalidateQueries({
9. chunks/chunk-0122-L121001-L122000-S077.js:L368 (srcL121368) :: Zi.success(t.message), n.invalidateQueries({
10. chunks/chunk-0058-L057001-L058000-S076.js:L996 (srcL57996) :: t.invalidateQueries([sF]), no.emit("force-sidebar-refresh", !0)
11. chunks/chunk-0094-L093001-L094000-S073.js:L203 (srcL93203) :: })), t && h.invalidateQueries(Array.isArray(t) ? t : [t]), s && u(s), a()
12. chunks/chunk-0094-L093001-L094000-S073.js:L407 (srcL93407) :: g(), t.invalidateQueries([AF]), s && t.invalidateQueries([AF, e]), a(-1), fq(AF, !1)
13. chunks/chunk-0094-L093001-L094000-S073.js:L461 (srcL93461) :: s && (await j(e?.id), t.invalidateQueries([AF, i]))
14. chunks/chunk-0080-L079001-L080000-S065.js:L297 (srcL79297) :: }), r.invalidateQueries(["COMPLAINTS"])
15. chunks/chunk-0080-L079001-L080000-S065.js:L651 (srcL79651) :: }), Zi.success(o("common.success")), l.invalidateQueries(["COMPLAINTS"]), t(), i && s(i)
16. chunks/chunk-0080-L079001-L080000-S065.js:L896 (srcL79896) :: }), s.invalidateQueries(["COMPLAINTS"]), n("/dashboard/issues")
17. chunks/chunk-0099-L098001-L099000-S064.js:L406 (srcL98406) :: d(!0), await (async e => (await co(`/api-management/rf/users/visitor-access/${e}/approve`)).data)(t), r(), i.invalidateQueries(n), d(!1)
18. chunks/chunk-0120-L119001-L120000-S060.js:L331 (srcL119331) :: Zi.success(t("offPlanSaleCreatedSuccessfully")), r.invalidateQueries({
19. chunks/chunk-0120-L119001-L120000-S060.js:L333 (srcL119333) :: }), r.invalidateQueries({
20. chunks/chunk-0120-L119001-L120000-S060.js:L353 (srcL119353) :: Zi.success(t("unitForm.offPlanSaleUpdatedSuccessfully")), r.invalidateQueries({
21. chunks/chunk-0120-L119001-L120000-S060.js:L371 (srcL119371) :: Zi.success(t("unitForm.paymentMarkedAsComplete")), r.invalidateQueries({
22. chunks/chunk-0120-L119001-L120000-S060.js:L386 (srcL119386) :: Zi.success(t("unitForm.paymentNotificationsSentSuccessfully")), r.invalidateQueries({
23. chunks/chunk-0120-L119001-L120000-S060.js:L401 (srcL119401) :: Zi.success(t("unitForm.paymentRemindersSentSuccessfully")), r.invalidateQueries({
24. chunks/chunk-0091-L090001-L091000-S059.js:L4 (srcL90004) :: s.invalidateQueries([tF]), o("/properties-list/units")
25. chunks/chunk-0091-L090001-L091000-S059.js:L933 (srcL90933) :: r.invalidateQueries([aH]), r.invalidateQueries([tF]), g.reset(q4()), localStorage.removeItem(fi), n("/leasing/leases")
26. chunks/chunk-0091-L090001-L091000-S059.js:L955 (srcL90955) :: r.invalidateQueries([aH]), r.invalidateQueries([aH, i]), r.invalidateQueries([tF]), g.reset(q4()), localStorage.removeItem(fi), n("/leasing/leases")
27. chunks/chunk-0085-L084001-L085000-S055.js:L862 (srcL84862) :: } = Qc(), n = Ht(), r = ii(), [a, i] = Dt.useState(!0), o = "All Time", s = t?.ENABLE_SEND_ANNOUNCEMENT && r?.can(qI.View, $I.Announcements), l = t?.ENABLE_OFFERS && r?.can(qI.View, $I.Offers), d = !1 === n?.state?.st...
28. chunks/chunk-0119-L118001-L119000-S053.js:L521 (srcL118521) :: d.invalidateQueries(), Zi.success(c("marketplace.unListAllSuccess"))
29. chunks/chunk-0119-L118001-L119000-S053.js:L532 (srcL118532) :: d.invalidateQueries()
30. chunks/chunk-0119-L118001-L119000-S053.js:L550 (srcL118550) :: d.invalidateQueries()
31. chunks/chunk-0119-L118001-L119000-S053.js:L561 (srcL118561) :: d.invalidateQueries([TH, t]), d.invalidateQueries([LH, n, t]), d.invalidateQueries([kH, n, t]), d.invalidateQueries([SH, "", 1]), Zi.success(c("marketplace.listUnitSuccess"))
32. chunks/chunk-0119-L118001-L119000-S053.js:L581 (srcL118581) :: d.invalidateQueries([TH, t]), d.invalidateQueries([LH, n, t]), d.invalidateQueries([kH, n, t]), Zi.success(c("marketplace.editUnitSuccess"))
33. chunks/chunk-0119-L118001-L119000-S053.js:L646 (srcL118646) :: }))(v, p ? "hide" : "show"), await x.invalidateQueries({
34. chunks/chunk-0119-L118001-L119000-S053.js:L656 (srcL118656) :: y(parseInt(e)), await (async e => await co(`/api-management/marketplace/admin/units/prices-visibility/${e}`))(e), await x.invalidateQueries({
35. chunks/chunk-0100-L099001-L100000-S049.js:L136 (srcL99136) :: }), r.invalidateQueries([DH])
36. chunks/chunk-0100-L099001-L100000-S049.js:L179 (srcL99179) :: a.invalidateQueries([DH, e]), Zi.success(r("visitRejectedSuccessfully")), t(), c(), a.invalidateQueries([DH])
37. chunks/chunk-0100-L099001-L100000-S049.js:L194 (srcL99194) :: a.invalidateQueries([DH, e]), Zi.success(r("visitCompletedSuccessfully")), t(), n(!1), a.invalidateQueries([DH])
38. chunks/chunk-0059-L058001-L059000-S045.js:L6 (srcL58006) :: n && (a(n), t.invalidateQueries([DF]))
39. chunks/chunk-0087-L086001-L087000-S041.js:L47 (srcL86047) :: })(), i(!1), r.invalidateQueries([SF]), r.invalidateQueries([LF])
40. chunks/chunk-0087-L086001-L087000-S041.js:L824 (srcL86824) :: }), i.invalidateQueries([hF, t]), i.invalidateQueries([aF]), s(), Zi.success(a("common.success")), r()
41. chunks/chunk-0087-L086001-L087000-S041.js:L946 (srcL86946) :: o.invalidateQueries({
42. chunks/chunk-0116-L115001-L116000-S034.js:L982 (srcL115982) :: n.invalidateQueries({
43. chunks/chunk-0116-L115001-L116000-S034.js:L984 (srcL115984) :: }), n.invalidateQueries({
44. chunks/chunk-0118-L117001-L118000-S032.js:L161 (srcL117161) :: await m(n), await l.invalidateQueries({
45. chunks/chunk-0118-L117001-L118000-S032.js:L389 (srcL117389) :: s(null), l.invalidateQueries([SH]), Zi.success(d("marketplace.unlisted"))
46. chunks/chunk-0118-L117001-L118000-S032.js:L715 (srcL117715) :: c.invalidateQueries([SH]), Zi.success(u("marketplace.listed")), r(), s([])
47. chunks/chunk-0074-L073001-L074000-S028.js:L815 (srcL73815) :: } = Gn(), o = JSON.parse(localStorage.getItem("user") || "{}"), l = Ht(), d = Ys(), c = s(), u = ce(c.breakpoints.down("md")), [p, h] = Dt.useState(), [m, f] = Dt.useState(!0), [g, y] = Dt.useState(!1);
48. chunks/chunk-0074-L073001-L074000-S028.js:L823 (srcL73823) :: d.invalidateQueries({
49. chunks/chunk-0074-L073001-L074000-S028.js:L825 (srcL73825) :: }), d.invalidateQueries({
50. chunks/chunk-0096-L095001-L096000-S028.js:L42 (srcL95042) :: }), i.invalidateQueries([rH, n])
51. chunks/chunk-0096-L095001-L096000-S028.js:L67 (srcL95067) :: }), i.invalidateQueries([rH, n])
52. chunks/chunk-0096-L095001-L096000-S028.js:L94 (srcL95094) :: } : e)), i.invalidateQueries([rH, n])
53. chunks/chunk-0056-L055001-L056000-S026.js:L489 (srcL55489) :: })(e.id), t.invalidateQueries([LF]);
54. chunks/chunk-0056-L055001-L056000-S026.js:L510 (srcL55510) :: return t.invalidateQueries({
55. chunks/chunk-0056-L055001-L056000-S026.js:L512 (srcL55512) :: }), t.invalidateQueries({
56. chunks/chunk-0056-L055001-L056000-S026.js:L597 (srcL55597) :: return t.invalidateQueries({
57. chunks/chunk-0056-L055001-L056000-S026.js:L619 (srcL55619) :: return t.invalidateQueries({
58. chunks/chunk-0053-L052001-L053000-S025.js:L438 (srcL52438) :: n.language !== a?.code && (n.changeLanguage(a?.code), bo.defaults.headers.common["X-App-Locale"] = a?.code, bo.defaults.headers["X-App-Locale"] = a?.code, o.invalidateQueries(), window.location.reload()), document.doc...
59. chunks/chunk-0098-L097001-L098000-S020.js:L236 (srcL97236) :: r.invalidateQueries([rH, t]), a(`/leasing/details/${t}`)

## API config signals

1. chunks/chunk-0125-L124001-L124909-S284.js:L754 (srcL124754) :: })(), localStorage.removeItem("loggedIn"), localStorage.removeItem("user"), localStorage.removeItem(fi), d(!1), u(""), localStorage.removeItem("X-Tenant"), localStorage.removeItem("token"), localStorage.removeItem("pl...
2. chunks/chunk-0083-L082001-L083000-S255.js:L456 (srcL82456) :: localStorage.setItem("X-Tenant", t), oo = t;
3. chunks/chunk-0124-L123001-L124000-S236.js:L987 (srcL123987) :: n.changeLanguage(t), x.clear(), document.documentElement.lang = t, bo.defaults.headers.common["X-App-Locale"] = t, bo.defaults.headers["X-App-Locale"] = t, _(e)
4. chunks/chunk-0003-L002001-L003000-S151.js:L39 (srcL2039) :: oo = localStorage.getItem("X-Tenant");
5. chunks/chunk-0003-L002001-L003000-S151.js:L46 (srcL2046) :: Authorization: `Bearer ${so()}`,
6. chunks/chunk-0003-L002001-L003000-S151.js:L47 (srcL2047) :: "X-App-Locale": $n.language,
7. chunks/chunk-0003-L002001-L003000-S151.js:L50 (srcL2050) :: "X-Tenant": n?.tenant || oo || "-"
8. chunks/chunk-0003-L002001-L003000-S151.js:L59 (srcL2059) :: Authorization: `Bearer ${so()}`,
9. chunks/chunk-0003-L002001-L003000-S151.js:L60 (srcL2060) :: "X-Tenant": oo,
10. chunks/chunk-0003-L002001-L003000-S151.js:L61 (srcL2061) :: "X-App-Locale": $n.language,
11. chunks/chunk-0003-L002001-L003000-S151.js:L74 (srcL2074) :: Authorization: `Bearer ${so()}`,
12. chunks/chunk-0003-L002001-L003000-S151.js:L75 (srcL2075) :: "X-Tenant": oo,
13. chunks/chunk-0003-L002001-L003000-S151.js:L76 (srcL2076) :: "X-App-Locale": $n.language,
14. chunks/chunk-0003-L002001-L003000-S151.js:L85 (srcL2085) :: Authorization: `Bearer ${so()}`,
15. chunks/chunk-0003-L002001-L003000-S151.js:L86 (srcL2086) :: "X-Tenant": oo,
16. chunks/chunk-0003-L002001-L003000-S151.js:L87 (srcL2087) :: "X-App-Locale": $n.language,
17. chunks/chunk-0003-L002001-L003000-S151.js:L104 (srcL2104) :: Authorization: `Bearer ${so()}`,
18. chunks/chunk-0003-L002001-L003000-S151.js:L105 (srcL2105) :: "X-App-Locale": $n.language,
19. chunks/chunk-0003-L002001-L003000-S151.js:L112 (srcL2112) :: localStorage.setItem("token", e), io = e, bo.defaults.headers.common.Authorization = `Bearer ${e}` || "";
20. chunks/chunk-0003-L002001-L003000-S151.js:L119 (srcL2119) :: return localStorage.setItem("token", n.token), localStorage.setItem("X-Tenant", n.business_name), bo.defaults.headers.common["X-Tenant"] = n.business_name, bo.defaults.headers.common.Authorization = `Bearer ${n.token}...
21. chunks/chunk-0003-L002001-L003000-S151.js:L125 (srcL2125) :: baseURL: "https://api.goatar.com",
22. chunks/chunk-0003-L002001-L003000-S151.js:L127 (srcL2127) :: "X-App-Locale": $n.language,
23. chunks/chunk-0003-L002001-L003000-S151.js:L132 (srcL2132) :: xo.defaults.headers.common["X-Tenant"] = oo || "", xo.defaults.headers.common.Authorization = `Bearer ${so()}` || "";
24. chunks/chunk-0003-L002001-L003000-S151.js:L134 (srcL2134) :: baseURL: "https://api.goatar.com/api-management",
25. chunks/chunk-0003-L002001-L003000-S151.js:L136 (srcL2136) :: "X-App-Locale": $n.language,
26. chunks/chunk-0003-L002001-L003000-S151.js:L142 (srcL2142) :: baseURL: "https://api.goatar.com/api-management",
27. chunks/chunk-0003-L002001-L003000-S151.js:L144 (srcL2144) :: "X-App-Locale": $n.language,
28. chunks/chunk-0003-L002001-L003000-S151.js:L156 (srcL2156) :: bo.defaults.headers.common["X-Tenant"] = oo || "", bo.defaults.headers.common.Authorization = `Bearer ${so()}` || "", bo.defaults.headers.common["X-App-Locale"] = $n.language, bo.defaults.headers["X-App-Locale"] = $n....
29. chunks/chunk-0003-L002001-L003000-S151.js:L157 (srcL2157) :: bo.defaults.headers.common["X-App-Locale"] = $n.language, bo.defaults.headers["X-App-Locale"] = $n.language
30. chunks/chunk-0003-L002001-L003000-S151.js:L160 (srcL2160) :: e && (bo.defaults.headers.common["X-Tenant"] = e || ""), t && (bo.defaults.headers.common.Authorization = `Bearer ${t}` || "")
31. chunks/chunk-0003-L002001-L003000-S151.js:L162 (srcL2162) :: So = e => Qt.isAxiosError(e),
32. chunks/chunk-0059-L058001-L059000-S045.js:L650 (srcL58650) :: r.changeLanguage(t), b.clear(), document.documentElement.lang = t, bo.defaults.headers.common["X-App-Locale"] = t, bo.defaults.headers["X-App-Locale"] = t, x(e)
33. chunks/chunk-0053-L052001-L053000-S025.js:L438 (srcL52438) :: n.language !== a?.code && (n.changeLanguage(a?.code), bo.defaults.headers.common["X-App-Locale"] = a?.code, bo.defaults.headers["X-App-Locale"] = a?.code, o.invalidateQueries(), window.location.reload()), document.doc...
34. chunks/chunk-0053-L052001-L053000-S025.js:L444 (srcL52444) :: n.changeLanguage(t), o.clear(), document.documentElement.lang = t, bo.defaults.headers.common["X-App-Locale"] = t, bo.defaults.headers["X-App-Locale"] = t, i(Jc.find(e => e.code === t))
35. chunks/chunk-0041-L040001-L041000-S003.js:L776 (srcL40776) :: return n.append("Authorization", function(e) {
