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
    group: string | null;
};

export type Setting = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    type: string;
    parent_id: number | null;
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
    source_id: number | null;
    accepted_invite: boolean;
    created_at: string;
    updated_at: string;
    // Computed
    name?: string;
    // Relationships
    units?: Unit[];
    leases?: Lease[];
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
    created_at: string;
    updated_at: string;
    // Relationships
    tenant?: Resident;
    status?: Status;
    units?: Unit[];
    transactions?: Transaction[];
    // Computed
    total_unpaid_amount?: string;
    unpaid_transactions_count?: number;
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
    due_date: string;
    is_paid: boolean;
    paid_date: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    lease?: Lease;
    unit?: Unit;
    status?: Status;
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
    requester_id: number;
    requester_type: string;
    assignee_id: number | null;
    assignee_type: string | null;
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

export type Facility = {
    id: number;
    name: string;
    name_ar: string | null;
    name_en: string | null;
    category_id: number;
    community_id: number;
    capacity: number | null;
    status: boolean;
    created_at: string;
    updated_at: string;
    // Relationships
    category?: FacilityCategory;
    community?: Community;
};

export type FacilityBooking = {
    id: number;
    facility_id: number;
    resident_id: number;
    status_id: number;
    booking_date: string;
    start_time: string;
    end_time: string;
    notes: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    facility?: Facility;
    resident?: Resident;
    status?: Status;
};

// ---------------------------------------------------------------------------
// Communication Models
// ---------------------------------------------------------------------------

export type Announcement = {
    id: number;
    title: string;
    body: string;
    community_id: number | null;
    building_id: number | null;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    // Relationships
    community?: Community;
    building?: Building;
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
