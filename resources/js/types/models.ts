// ---------------------------------------------------------------------------
// Reference / Lookup Models
// ---------------------------------------------------------------------------

export type Country = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    code: string | null;
    phone_code: string | null;
    currency: string | null;
};

export type City = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    country_id: number;
    country?: Country;
};

export type District = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    city_id: number;
    city?: City;
};

export type Currency = {
    id: number;
    name: string;
    code: string | null;
    symbol: string | null;
};

export type Status = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    priority?: number | null;
    type?: string | null;
    group?: string | null;
    created_at?: string;
};

export type Setting = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    type: string;
    parent_id: number | null;
    created_at?: string;
};

export type CommonList = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    type: string | null;
    priority: number | null;
    created_at?: string;
};

export type Module = {
    id: number;
    title: string;
    is_active: string | boolean;
};

// ---------------------------------------------------------------------------
// Property Models
// ---------------------------------------------------------------------------

export type UnitCategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    icon: string | null;
    types?: UnitType[];
};

export type UnitType = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    icon: string | null;
    category_id: number;
    category?: UnitCategory;
};

export type Community = {
    id: number;
    name: string;
    country_id: number;
    currency_id: number;
    city_id: number;
    district_id: number;
    sales_commission_rate: string | null;
    rental_commission_rate: string | null;
    map: Record<string, unknown> | null;
    working_days: string[] | null;
    latitude: string | null;
    longitude: string | null;
    is_market_place: boolean;
    is_buy: boolean;
    community_marketplace_type: 'rent' | 'sale' | 'both' | null;
    is_off_plan_sale: boolean;
    is_selected_property: boolean;
    count_selected_property: number;
    total_income: string;
    created_at: string;
    updated_at: string;
    // Relationships
    country?: Country;
    currency?: Currency;
    city?: City;
    district?: District;
    buildings?: Building[];
    facilities?: Facility[];
    amenities?: Pick<Amenity, 'id' | 'name' | 'name_en' | 'name_ar'>[];
    // Computed
    buildings_count?: number;
    units_count?: number;
    requests_count?: number;
};

export type Building = {
    id: number;
    name: string;
    rf_community_id: number;
    city_id: number | null;
    district_id: number | null;
    no_floors: number | null;
    year_build: string | null;
    map: Record<string, unknown> | null;
    created_at: string;
    updated_at: string;
    // Relationships
    community?: Community;
    city?: City;
    district?: District;
    units?: Unit[];
    // Computed
    units_count?: number;
};

export type Unit = {
    id: number;
    name: string;
    rf_community_id: number;
    rf_building_id: number | null;
    category_id: number;
    type_id: number;
    status_id: number;
    owner_id: number | null;
    tenant_id: number | null;
    city_id: number | null;
    district_id: number | null;
    year_build: string | null;
    net_area: string | null;
    floor_no: number | null;
    about: string | null;
    map: Record<string, unknown> | null;
    is_market_place: boolean;
    is_buy: boolean;
    is_off_plan_sale: boolean;
    renewal_status: boolean;
    currency_id: number | null;
    asking_rent_amount: string | null;
    rent_period: 'month' | 'year' | null;
    created_at: string;
    updated_at: string;
    // Relationships
    community?: Community;
    building?: Building;
    category?: UnitCategory;
    type?: UnitType;
    status?: Status;
    city?: City;
    district?: District;
    owner?: Owner;
    tenant?: Resident;
    currency?: Currency;
    specifications?: UnitSpecification[];
    rooms?: UnitRoom[];
    areas?: UnitArea[];
    features?: Feature[];
    photos?: Media[];
    floor_plans?: Media[];
    documents?: Media[];
    marketplace_listings?: MarketplaceUnit[];
};

// ---------------------------------------------------------------------------
// Contact Models
// ---------------------------------------------------------------------------

export type Owner = {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone_number: string;
    national_phone_number: string | null;
    phone_country_code: string;
    national_id: string | null;
    nationality_id: number | null;
    gender: 'male' | 'female' | null;
    georgian_birthdate: string | null;
    image: string | null;
    active: boolean;
    last_active: string | null;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
    // Relationships
    units?: Unit[];
    units_count?: number;
};

export type Resident = {
    id: number;
    first_name: string | null;
    first_name_ar: string | null;
    last_name: string | null;
    last_name_ar: string | null;
    email: string | null;
    phone_number: string;
    national_phone_number: string | null;
    phone_country_code: string;
    national_id: string | null;
    id_type: string | null;
    nationality_id: number | null;
    gender: 'male' | 'female' | null;
    georgian_birthdate: string | null;
    image: string | null;
    active: boolean;
    last_active: string | null;
    source_id: number | null;
    accepted_invite: boolean;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
    // Relationships
    units?: Unit[];
    leases?: Lease[];
    dependents?: Dependent[];
    units_count?: number;
    leases_count?: number;
};

export type Admin = {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone_number: string;
    phone_country_code: string;
    national_id: string | null;
    nationality_id: number | null;
    gender: 'male' | 'female' | null;
    georgian_birthdate: string | null;
    image: string | null;
    role: 'Admins' | 'accountingManagers' | 'serviceManagers' | 'marketingManagers' | 'salesAndLeasingManagers';
    active: boolean;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
};

export type Professional = {
    id: number;
    first_name: string;
    last_name: string;
    email: string | null;
    phone_number: string;
    phone_country_code: string;
    national_id: string | null;
    image: string | null;
    active: boolean;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
};

export type Lead = {
    id: number;
    name: string | null;
    name_en: string | null;
    name_ar: string | null;
    first_name: string | null;
    last_name: string | null;
    phone_number: string;
    phone_country_code: string | null;
    email: string | null;
    source_id: number | null;
    status_id: number | null;
    priority_id: number | null;
    lead_owner_id: number | null;
    assigned_to_user_id: number | null;
    interested: string | null;
    lead_last_contact_at: string | null;
    notes: string | null;
    lost_reason: string | null;
    created_at: string;
    updated_at: string;
    source?: { id: number; name: string; name_en: string | null; name_ar: string | null };
    status?: { id: number; name: string; name_en: string | null; name_ar: string | null };
    lead_owner?: Admin | null;
    assigned_to?: { id: number; name: string; email: string } | null;
};

export type LeadActivity = {
    id: number;
    type: 'assigned' | 'unassigned' | 'status_change' | 'note' | 'converted';
    data: Record<string, string | null> | null;
    created_at: string | null;
    actor: { id: number; name: string } | null;
};

export type Dependent = {
    id: number;
    first_name: string;
    last_name: string;
    phone_number: string | null;
    phone_country_code: string | null;
    relation: string | null;
    resident_id: number;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
};

// ---------------------------------------------------------------------------
// Lease & Finance Models
// ---------------------------------------------------------------------------

export type LeaseUnit = {
    id: number;
    lease_id: number;
    unit_id: number;
    rental_annual_type: string | null;
    annual_rental_amount: string | null;
    net_area: string | null;
    meter_cost: string | null;
    created_at: string;
    updated_at: string;
    unit?: Unit;
};

export type LeaseAdditionalFee = {
    id: number;
    lease_id: number;
    name: string | null;
    description: string | null;
    amount: string | null;
    calculation_basis_id: number | null;
    frequency_id: number | null;
    status_id: number | null;
    created_at: string;
    updated_at: string;
    calculation_basis?: Setting;
    frequency?: Setting;
    status?: Status;
};

export type LeaseEscalation = {
    id: number;
    lease_id: number;
    type: string | null;
    amount: string | null;
    rate: string | null;
    start_at: string | null;
    end_at: string | null;
    created_at: string;
    updated_at: string;
};

export type TransactionAdditionalFee = {
    id: number;
    transaction_id: number;
    name: string;
    amount: string;
    created_at: string;
    updated_at: string;
};

export type LeaseAmendment = {
    id: number;
    lease_id: number;
    amended_by: number;
    reason: string;
    changes: Record<string, { from: string | string[] | null; to: string | string[] | null }>;
    addendum_media_id: number | null;
    amendment_number: number;
    created_at: string;
    updated_at: string;
    amended_by_user?: User;
};

export type Lease = {
    id: number;
    contract_number: string;
    tenant_id: number;
    status_id: number;
    lease_unit_type_id: number;
    rental_contract_type_id: number;
    payment_schedule_id: number;
    created_by_id: number;
    deal_owner_id: number | null;
    start_date: string;
    end_date: string;
    handover_date: string;
    actual_end_at: string | null;
    tenant_type: 'individual' | 'company';
    rental_type: 'total' | 'detailed';
    rental_total_amount: string;
    security_deposit_amount: string | null;
    security_deposit_due_date: string | null;
    lease_escalations_type: 'fixed' | 'percentage' | null;
    terms_conditions: string | null;
    is_terms: boolean;
    is_sub_lease: boolean;
    parent_lease_id: number | null;
    legal_representative: string | null;
    fit_out_status: string | null;
    free_period: number;
    number_of_years: number | null;
    number_of_months: number | null;
    number_of_days: number | null;
    is_renew: boolean;
    is_move_out: boolean;
    is_old: boolean;
    pdf_url: string | null;
    quote_id: number | null;
    kyc_complete: boolean | null;
    kyc_submitted_at: string | null;
    approved_by_id: number | null;
    approved_at: string | null;
    rejected_by_id: number | null;
    rejected_at: string | null;
    rejection_reason: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    tenant?: Resident;
    status?: Status;
    units?: Unit[];
    lease_units?: LeaseUnit[];
    transactions?: Transaction[];
    additional_fees?: LeaseAdditionalFee[];
    escalations?: LeaseEscalation[];
    created_by?: Admin;
    deal_owner?: Admin;
    approved_by?: User;
    rejected_by?: User;
    subleases?: Lease[];
    parent_lease?: Lease | null;
    amendments?: AmendmentEntry[];
    // Computed
    total_unpaid_amount?: string;
    unpaid_transactions_count?: number;
    current_amendment_number?: number;
};

export type AmendmentEntry = {
    id: number;
    amendment_number: number;
    reason: string;
    changes: Record<string, { from: string | string[] | null; to: string | string[] | null }>;
    addendum_media_id: number | null;
    amended_by: { id: number; name: string } | null;
    created_at: string;
};

export type Transaction = {
    id: number;
    lease_id: number | null;
    unit_id: number | null;
    category_id: number;
    subcategory_id: number | null;
    type_id: number;
    status_id: number;
    assignee_id: number;
    amount: string;
    tax_amount: string;
    rental_amount: string | null;
    due_on?: string;
    due_date?: string;
    is_paid: boolean;
    paid_date: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    lease?: Lease;
    unit?: Unit;
    status?: Status;
    category?: Setting;
    subcategory?: Setting;
    type?: Setting;
    additional_fees?: TransactionAdditionalFee[];
    // Computed
    paid?: string;
    left?: string;
};

export type Payment = {
    id: number;
    transaction_id: number;
    amount: string;
    payment_date: string;
    payment_method: string | null;
    reference_number: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    transaction?: Transaction;
};

// ---------------------------------------------------------------------------
// Request & Service Models
// ---------------------------------------------------------------------------

export type RequestCategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    icon: string | null;
    subcategories?: RequestSubcategory[];
    serviceSettings?: ServiceSetting[];
};

export type RequestSubcategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    category_id: number;
    icon: string | null;
    category?: RequestCategory;
    requests_count?: number;
};

export type ServiceSetting = {
    id: number;
    category_id: number;
    visibilities: Record<string, boolean> | null;
    permissions: Record<string, boolean> | null;
    submit_request_before_type: string | null;
    submit_request_before_value: number | null;
    capacity_type: string | null;
    capacity_value: number | null;
    created_at: string;
    updated_at: string;
    category?: RequestCategory;
};

export type ServiceRequest = {
    id: number;
    title: string | null;
    description: string | null;
    category_id: number;
    subcategory_id: number | null;
    status_id: number;
    priority: string | null;
    unit_id: number | null;
    community_id: number | null;
    building_id: number | null;
    professional_id: number | null;
    requester_id: number;
    requester_type: string;
    assignee_id: number | null;
    assignee_type: string | null;
    request_code: string | null;
    admin_notes: string | null;
    assigned_at: string | null;
    scheduled_date: string | null;
    completed_date: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    category?: RequestCategory;
    subcategory?: RequestSubcategory;
    status?: Status;
    unit?: Unit;
    community?: Community;
    building?: Building;
    requester?: Resident | Owner | Admin | Professional;
    assignee?: Admin | Professional | null;
    professional?: Professional | null;
};

// ---------------------------------------------------------------------------
// Facility Models
// ---------------------------------------------------------------------------

export type FacilityCategory = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
};

export type FacilityAvailabilityRule = {
    id: number;
    facility_id: number;
    day_of_week: number;
    open_time: string;
    close_time: string;
    slot_duration_minutes: number;
    max_concurrent_bookings: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
};

export type Facility = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    category_id: number;
    community_id: number;
    capacity: number | null;
    is_active: boolean;
    // Extended schema fields
    booking_fee: string | null;
    currency: string | null;
    type: string | null;
    pricing_mode: 'free' | 'per_session' | 'per_hour' | null;
    requires_booking: boolean;
    booking_horizon_days: number;
    cancellation_hours_before: number;
    min_booking_duration_minutes: number;
    max_booking_duration_minutes: number | null;
    contract_required: boolean;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    category?: FacilityCategory;
    community?: Community;
    availability_rules?: FacilityAvailabilityRule[];
};

export type FacilitySlot = {
    start: string;
    end: string;
    status: 'available' | 'full';
    remaining_capacity: number;
};

export type FacilityBooking = {
    id: number;
    facility_id: number;
    status_id: number;
    booker_type: string;
    booker_id: number;
    booking_date: string;
    start_time: string;
    end_time: string;
    number_of_guests: number | null;
    notes: string | null;
    approved_at: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    facility?: Facility;
    resident?: Resident;
    booker?: Resident | Owner | Admin | Professional;
    status?: Status;
};

/**
 * Lightweight booking shape returned by the calendar AJAX endpoints.
 * Different from FacilityBooking which maps the full Eloquent model.
 */
export type CalendarBooking = {
    id: number;
    facility_id: number;
    facility_name: string;
    booker_name: string;
    booker_type: string;
    booking_date: string;
    start_time: string;
    end_time: string;
    status_id: number;
    status_name: string;
    notes: string | null;
    /** Only present on the show (popover) endpoint. */
    invoice_id?: number | null;
    can_checkin?: boolean;
    can_cancel?: boolean;
    can_update?: boolean;
};

// ---------------------------------------------------------------------------
// Communication Models
// ---------------------------------------------------------------------------

export type Announcement = {
    id: number;
    title: string;
    body?: string;
    content?: string;
    community_id: number | null;
    building_id: number | null;
    status?: boolean;
    is_published?: boolean;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    community?: Community;
    building?: Building;
};

export type WorkingDay = {
    id: number;
    subcategory_id: number;
    day_name: string;
    from_time: string | null;
    to_time: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
};

export type FeaturedService = {
    id: number;
    subcategory_id: number;
    name: string;
    status: boolean;
    icon_id: number | null;
    created_at: string;
    updated_at: string;
};

export type Feature = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    icon: string | null;
    active: boolean;
    created_at: string;
    updated_at: string;
};

export type Amenity = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    icon: string | null;
    active: boolean;
    created_at: string;
    updated_at: string;
};

export type MarketplaceUnit = {
    id: number;
    unit_id: number;
    listing_type: 'rent' | 'sale' | 'both';
    price: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    unit?: Unit;
    visits?: MarketplaceVisit[];
};

export type MarketplaceVisit = {
    id: number;
    marketplace_unit_id: number;
    status_id: number | null;
    visitor_name: string | null;
    visitor_phone: string | null;
    scheduled_at: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    marketplace_unit?: MarketplaceUnit;
    status?: Status;
};

export type Media = {
    id: number;
    url: string;
    name: string;
    notes: string | null;
    collection: string;
    mediable_type: string;
    mediable_id: number;
    created_at: string;
    updated_at: string;
};

export type UnitSpecification = {
    id: number;
    unit_id: number;
    key: string;
    value: string;
    created_at: string;
    updated_at: string;
};

export type UnitRoom = {
    id: number;
    unit_id: number;
    room_type: string;
    count: number;
    created_at: string;
    updated_at: string;
};

export type UnitArea = {
    id: number;
    unit_id: number;
    area_type: string;
    value: string;
    created_at: string;
    updated_at: string;
};

export type InvoiceSetting = {
    id: number;
    company_name: string;
    logo: string | null;
    address: string | null;
    vat: string | null;
    vat_number: string | null;
    cr_number: string | null;
    instructions: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
};

export type Notification = {
    id: string;
    text: string;
    data: Record<string, unknown>;
    type: string;
    read: string | null;
    created_at: string;
};

export type NotificationUnreadCount = {
    count: number;
};

export type DashboardRequiresAttention = {
    key: string;
    title: string;
    count: number;
    href: string;
};

// ---------------------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------------------

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
};

// ---------------------------------------------------------------------------
// Filter / Form helpers
// ---------------------------------------------------------------------------

export type SelectOption = {
    value: string | number;
    label: string;
};
