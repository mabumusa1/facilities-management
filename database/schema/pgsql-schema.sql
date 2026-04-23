--
-- PostgreSQL database dump
--

\restrict y1Ywmy9By12UiX4fSgSxxWTTvCGEhY24rje68OkZbyg67F6I6gc6BRHgG4uSf25

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3 (Ubuntu 18.3-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: account_memberships; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.account_memberships (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    role character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: account_memberships_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.account_memberships_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: account_memberships_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.account_memberships_id_seq OWNED BY public.account_memberships.id;


--
-- Name: account_subscription_usage; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.account_subscription_usage (
    id bigint NOT NULL,
    subscription_id bigint NOT NULL,
    feature_id bigint NOT NULL,
    used smallint NOT NULL,
    timezone character varying(255),
    valid_until timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: account_subscription_usage_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.account_subscription_usage_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: account_subscription_usage_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.account_subscription_usage_id_seq OWNED BY public.account_subscription_usage.id;


--
-- Name: account_subscriptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.account_subscriptions (
    id bigint NOT NULL,
    subscriber_type character varying(255) NOT NULL,
    subscriber_id bigint NOT NULL,
    plan_id bigint NOT NULL,
    name json NOT NULL,
    slug character varying(255) NOT NULL,
    description json,
    timezone character varying(255),
    trial_ends_at timestamp(0) without time zone,
    starts_at timestamp(0) without time zone,
    ends_at timestamp(0) without time zone,
    canceled_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: account_subscriptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.account_subscriptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: account_subscriptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.account_subscriptions_id_seq OWNED BY public.account_subscriptions.id;


--
-- Name: admin_buildings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admin_buildings (
    id bigint NOT NULL,
    admin_id bigint NOT NULL,
    building_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: admin_buildings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admin_buildings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admin_buildings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admin_buildings_id_seq OWNED BY public.admin_buildings.id;


--
-- Name: admin_communities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admin_communities (
    id bigint NOT NULL,
    admin_id bigint NOT NULL,
    community_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: admin_communities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admin_communities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admin_communities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admin_communities_id_seq OWNED BY public.admin_communities.id;


--
-- Name: admin_service_manager_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.admin_service_manager_types (
    id bigint NOT NULL,
    admin_id bigint NOT NULL,
    service_manager_type_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: admin_service_manager_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.admin_service_manager_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: admin_service_manager_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.admin_service_manager_types_id_seq OWNED BY public.admin_service_manager_types.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cities (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    country_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: cities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cities_id_seq OWNED BY public.cities.id;


--
-- Name: community_amenities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.community_amenities (
    id bigint NOT NULL,
    community_id bigint NOT NULL,
    amenity_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: community_amenities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.community_amenities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: community_amenities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.community_amenities_id_seq OWNED BY public.community_amenities.id;


--
-- Name: countries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.countries (
    id bigint NOT NULL,
    iso2 character varying(2) NOT NULL,
    iso3 character varying(3) NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    dial character varying(255),
    currency character varying(255),
    capital character varying(255),
    continent character varying(2),
    unicode character varying(255),
    excel character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: countries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.countries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: countries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.countries_id_seq OWNED BY public.countries.id;


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.currencies (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    code character varying(255) NOT NULL,
    symbol character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: currencies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.currencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: currencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.currencies_id_seq OWNED BY public.currencies.id;


--
-- Name: districts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.districts (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    city_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: districts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.districts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: districts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.districts_id_seq OWNED BY public.districts.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: feature_unit; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feature_unit (
    id bigint NOT NULL,
    feature_id bigint NOT NULL,
    unit_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: feature_unit_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feature_unit_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feature_unit_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feature_unit_id_seq OWNED BY public.feature_unit.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: lease_units; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_units (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    unit_id bigint NOT NULL,
    rental_annual_type character varying(255),
    annual_rental_amount numeric(12,2),
    net_area numeric(10,2),
    meter_cost numeric(10,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lease_units_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_units_id_seq OWNED BY public.lease_units.id;


--
-- Name: media; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.media (
    id bigint NOT NULL,
    url character varying(255) NOT NULL,
    name character varying(255),
    notes character varying(255),
    mediable_type character varying(255) NOT NULL,
    mediable_id bigint NOT NULL,
    collection character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: media_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.media_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: media_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.media_id_seq OWNED BY public.media.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id bigint NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: professional_subcategories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.professional_subcategories (
    id bigint NOT NULL,
    professional_id bigint NOT NULL,
    subcategory_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: professional_subcategories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.professional_subcategories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: professional_subcategories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.professional_subcategories_id_seq OWNED BY public.professional_subcategories.id;


--
-- Name: rf_admins; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_admins (
    id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255),
    phone_number character varying(255) NOT NULL,
    full_phone_number character varying(255),
    phone_country_code character varying(255) NOT NULL,
    national_id character varying(255),
    nationality_id bigint,
    gender character varying(255),
    georgian_birthdate date,
    image character varying(255),
    role character varying(255) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    last_login_at timestamp(0) without time zone,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_admins_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_admins_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_admins_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_admins_id_seq OWNED BY public.rf_admins.id;


--
-- Name: rf_amenities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_amenities (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    icon character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_amenities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_amenities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_amenities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_amenities_id_seq OWNED BY public.rf_amenities.id;


--
-- Name: rf_announcements; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_announcements (
    id bigint NOT NULL,
    community_id bigint,
    title character varying(255) NOT NULL,
    content text CONSTRAINT rf_announcements_body_not_null NOT NULL,
    status boolean DEFAULT false CONSTRAINT rf_announcements_is_published_not_null NOT NULL,
    published_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    building_id bigint,
    account_tenant_id bigint
);


--
-- Name: rf_announcements_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_announcements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_announcements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_announcements_id_seq OWNED BY public.rf_announcements.id;


--
-- Name: rf_buildings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_buildings (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    rf_community_id bigint NOT NULL,
    city_id bigint,
    district_id bigint,
    account_tenant_id bigint,
    no_floors integer DEFAULT 0,
    year_build integer,
    map json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_buildings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_buildings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_buildings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_buildings_id_seq OWNED BY public.rf_buildings.id;


--
-- Name: rf_common_lists; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_common_lists (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    type character varying(255) NOT NULL,
    priority integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_common_lists_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_common_lists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_common_lists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_common_lists_id_seq OWNED BY public.rf_common_lists.id;


--
-- Name: rf_communities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_communities (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    country_id bigint NOT NULL,
    currency_id bigint NOT NULL,
    city_id bigint NOT NULL,
    district_id bigint NOT NULL,
    account_tenant_id bigint,
    sales_commission_rate numeric(10,2),
    rental_commission_rate numeric(10,2),
    map json,
    is_market_place boolean DEFAULT false NOT NULL,
    is_buy boolean DEFAULT false NOT NULL,
    community_marketplace_type character varying(255),
    is_off_plan_sale boolean DEFAULT false NOT NULL,
    is_selected_property boolean DEFAULT false NOT NULL,
    count_selected_property integer DEFAULT 0 NOT NULL,
    total_income numeric(12,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    description text,
    product_code character varying(255),
    license_number character varying(255),
    license_issue_date date,
    license_expiry_date date,
    completion_percent integer DEFAULT 0 NOT NULL,
    allow_cash_sale boolean DEFAULT false NOT NULL,
    allow_bank_financing boolean DEFAULT false NOT NULL,
    listed_percentage numeric(5,2) DEFAULT '0'::numeric NOT NULL
);


--
-- Name: rf_communities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_communities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_communities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_communities_id_seq OWNED BY public.rf_communities.id;


--
-- Name: rf_dependents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_dependents (
    id bigint NOT NULL,
    dependable_type character varying(255) NOT NULL,
    dependable_id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255),
    phone_number character varying(255),
    phone_country_code character varying(255),
    email character varying(255),
    national_id character varying(255),
    gender character varying(255),
    birthdate date,
    relationship character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_dependents_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_dependents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_dependents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_dependents_id_seq OWNED BY public.rf_dependents.id;


--
-- Name: rf_excel_sheets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_excel_sheets (
    id bigint NOT NULL,
    type character varying(255) DEFAULT 'general'::character varying NOT NULL,
    file_path character varying(255) NOT NULL,
    file_name character varying(255),
    status character varying(255) DEFAULT 'uploaded'::character varying NOT NULL,
    error_details json,
    rf_community_id bigint,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_excel_sheets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_excel_sheets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_excel_sheets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_excel_sheets_id_seq OWNED BY public.rf_excel_sheets.id;


--
-- Name: rf_facilities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facilities (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    description text,
    capacity integer,
    open_time time(0) without time zone,
    close_time time(0) without time zone,
    booking_fee numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    requires_approval boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    community_id bigint,
    account_tenant_id bigint
);


--
-- Name: rf_facilities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_facilities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_facilities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_facilities_id_seq OWNED BY public.rf_facilities.id;


--
-- Name: rf_facility_bookings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facility_bookings (
    id bigint NOT NULL,
    facility_id bigint NOT NULL,
    status_id bigint NOT NULL,
    booker_type character varying(255) NOT NULL,
    booker_id bigint NOT NULL,
    booking_date date NOT NULL,
    start_time time(0) without time zone NOT NULL,
    end_time time(0) without time zone NOT NULL,
    number_of_guests integer,
    notes text,
    approved_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_facility_bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_facility_bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_facility_bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_facility_bookings_id_seq OWNED BY public.rf_facility_bookings.id;


--
-- Name: rf_facility_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facility_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    status boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_facility_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_facility_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_facility_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_facility_categories_id_seq OWNED BY public.rf_facility_categories.id;


--
-- Name: rf_featured_services; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_featured_services (
    id bigint NOT NULL,
    subcategory_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    title_ar character varying(255),
    title_en character varying(255),
    description text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_featured_services_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_featured_services_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_featured_services_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_featured_services_id_seq OWNED BY public.rf_featured_services.id;


--
-- Name: rf_features; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_features (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    icon character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    type character varying(255)
);


--
-- Name: rf_features_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_features_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_features_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_features_id_seq OWNED BY public.rf_features.id;


--
-- Name: rf_form_templates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_form_templates (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    request_category_id bigint,
    community_id bigint,
    building_id bigint,
    schema json,
    is_active boolean DEFAULT true NOT NULL,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_form_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_form_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_form_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_form_templates_id_seq OWNED BY public.rf_form_templates.id;


--
-- Name: rf_invoice_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_invoice_settings (
    id bigint NOT NULL,
    company_name character varying(255) NOT NULL,
    logo character varying(255),
    address text,
    vat numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    instructions text,
    notes text,
    vat_number character varying(255),
    cr_number character varying(255),
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_invoice_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_invoice_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_invoice_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_invoice_settings_id_seq OWNED BY public.rf_invoice_settings.id;


--
-- Name: rf_lead_sources; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_lead_sources (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_lead_sources_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_lead_sources_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_lead_sources_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_lead_sources_id_seq OWNED BY public.rf_lead_sources.id;


--
-- Name: rf_leads; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_leads (
    id bigint NOT NULL,
    name character varying(255),
    first_name character varying(255),
    last_name character varying(255),
    phone_number character varying(255),
    email character varying(255),
    source_id bigint,
    status_id bigint,
    priority_id bigint,
    lead_owner_id bigint,
    interested character varying(255),
    lead_last_contact_at timestamp(0) without time zone,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_leads_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_leads_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_leads_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_leads_id_seq OWNED BY public.rf_leads.id;


--
-- Name: rf_lease_additional_fees; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_lease_additional_fees (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    amount numeric(12,2) NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_lease_additional_fees_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_lease_additional_fees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_lease_additional_fees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_lease_additional_fees_id_seq OWNED BY public.rf_lease_additional_fees.id;


--
-- Name: rf_lease_escalations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_lease_escalations (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    year integer NOT NULL,
    type character varying(255) NOT NULL,
    value numeric(12,2) NOT NULL,
    new_amount numeric(12,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_lease_escalations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_lease_escalations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_lease_escalations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_lease_escalations_id_seq OWNED BY public.rf_lease_escalations.id;


--
-- Name: rf_leases; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_leases (
    id bigint NOT NULL,
    contract_number character varying(255) NOT NULL,
    tenant_id bigint NOT NULL,
    status_id bigint NOT NULL,
    lease_unit_type_id bigint NOT NULL,
    rental_contract_type_id bigint NOT NULL,
    payment_schedule_id bigint NOT NULL,
    created_by_id bigint NOT NULL,
    deal_owner_id bigint,
    account_tenant_id bigint,
    start_date date NOT NULL,
    end_date date NOT NULL,
    handover_date date NOT NULL,
    actual_end_at date,
    tenant_type character varying(255) NOT NULL,
    rental_type character varying(255) NOT NULL,
    rental_total_amount numeric(12,2) NOT NULL,
    security_deposit_amount numeric(12,2),
    security_deposit_due_date date,
    lease_escalations_type character varying(255),
    terms_conditions text,
    is_terms boolean DEFAULT false NOT NULL,
    is_sub_lease boolean DEFAULT false NOT NULL,
    parent_lease_id bigint,
    legal_representative character varying(255),
    fit_out_status character varying(255),
    free_period integer DEFAULT 0 NOT NULL,
    number_of_years integer,
    number_of_months integer,
    number_of_days integer,
    is_renew boolean DEFAULT false NOT NULL,
    is_move_out boolean DEFAULT false NOT NULL,
    is_old boolean DEFAULT false NOT NULL,
    pdf_url character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_leases_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_leases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_leases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_leases_id_seq OWNED BY public.rf_leases.id;


--
-- Name: rf_manager_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_manager_roles (
    id bigint NOT NULL,
    role character varying(255) NOT NULL,
    name_ar character varying(255) NOT NULL,
    name_en character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_manager_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_manager_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_manager_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_manager_roles_id_seq OWNED BY public.rf_manager_roles.id;


--
-- Name: rf_marketplace_offers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_marketplace_offers (
    id bigint NOT NULL,
    unit_id bigint NOT NULL,
    account_tenant_id bigint,
    title character varying(255) NOT NULL,
    description text,
    discount_type character varying(255) DEFAULT 'percentage'::character varying NOT NULL,
    discount_value numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    start_date date,
    end_date date,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_marketplace_offers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_marketplace_offers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_marketplace_offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_marketplace_offers_id_seq OWNED BY public.rf_marketplace_offers.id;


--
-- Name: rf_marketplace_units; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_marketplace_units (
    id bigint NOT NULL,
    unit_id bigint NOT NULL,
    listing_type character varying(255) NOT NULL,
    price numeric(12,2) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_marketplace_units_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_marketplace_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_marketplace_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_marketplace_units_id_seq OWNED BY public.rf_marketplace_units.id;


--
-- Name: rf_marketplace_visits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_marketplace_visits (
    id bigint NOT NULL,
    marketplace_unit_id bigint NOT NULL,
    status_id bigint,
    visitor_name character varying(255),
    visitor_phone character varying(255),
    scheduled_at timestamp(0) without time zone,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_marketplace_visits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_marketplace_visits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_marketplace_visits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_marketplace_visits_id_seq OWNED BY public.rf_marketplace_visits.id;


--
-- Name: rf_owners; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_owners (
    id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255),
    phone_number character varying(255) NOT NULL,
    national_phone_number character varying(255),
    phone_country_code character varying(255) NOT NULL,
    national_id character varying(255),
    nationality_id bigint,
    gender character varying(255),
    georgian_birthdate date,
    image character varying(255),
    active boolean DEFAULT true NOT NULL,
    last_active timestamp(0) without time zone,
    relation character varying(255),
    relation_key character varying(255),
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_owners_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_owners_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_owners_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_owners_id_seq OWNED BY public.rf_owners.id;


--
-- Name: rf_payments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_payments (
    id bigint NOT NULL,
    transaction_id bigint NOT NULL,
    amount numeric(12,2) NOT NULL,
    payment_date date NOT NULL,
    payment_method character varying(255),
    reference character varying(255),
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_payments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_payments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_payments_id_seq OWNED BY public.rf_payments.id;


--
-- Name: rf_professionals; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_professionals (
    id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255),
    phone_number character varying(255) NOT NULL,
    phone_country_code character varying(255) NOT NULL,
    national_id character varying(255),
    image character varying(255),
    active boolean DEFAULT true NOT NULL,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_professionals_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_professionals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_professionals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_professionals_id_seq OWNED BY public.rf_professionals.id;


--
-- Name: rf_request_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_request_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    description character varying(255),
    status boolean DEFAULT true NOT NULL,
    has_sub_categories boolean DEFAULT false NOT NULL,
    icon_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_request_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_request_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_request_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_request_categories_id_seq OWNED BY public.rf_request_categories.id;


--
-- Name: rf_request_subcategories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_request_subcategories (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    status boolean DEFAULT true NOT NULL,
    icon_id bigint,
    start time(0) without time zone,
    "end" time(0) without time zone,
    is_all_day boolean,
    terms_and_conditions text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_request_subcategories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_request_subcategories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_request_subcategories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_request_subcategories_id_seq OWNED BY public.rf_request_subcategories.id;


--
-- Name: rf_requests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_requests (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    subcategory_id bigint,
    status_id bigint NOT NULL,
    requester_type character varying(255) NOT NULL,
    requester_id bigint NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    preferred_date date,
    preferred_time time(0) without time zone,
    priority character varying(255),
    admin_notes text,
    resolved_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    unit_id bigint,
    community_id bigint,
    building_id bigint,
    professional_id bigint,
    request_code character varying(255),
    account_tenant_id bigint,
    assigned_at timestamp(0) without time zone,
    completed_at timestamp(0) without time zone
);


--
-- Name: rf_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_requests_id_seq OWNED BY public.rf_requests.id;


--
-- Name: rf_service_manager_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_service_manager_types (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_service_manager_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_service_manager_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_service_manager_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_service_manager_types_id_seq OWNED BY public.rf_service_manager_types.id;


--
-- Name: rf_service_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_service_settings (
    id bigint NOT NULL,
    category_id bigint NOT NULL,
    visibilities json,
    permissions json,
    submit_request_before_type character varying(255),
    submit_request_before_value integer,
    capacity_type character varying(255),
    capacity_value integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_service_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_service_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_service_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_service_settings_id_seq OWNED BY public.rf_service_settings.id;


--
-- Name: rf_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_settings (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    type character varying(255) NOT NULL,
    parent_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_settings_id_seq OWNED BY public.rf_settings.id;


--
-- Name: rf_statuses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_statuses (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    priority integer DEFAULT 1 NOT NULL,
    type character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_statuses_id_seq OWNED BY public.rf_statuses.id;


--
-- Name: rf_system_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_system_settings (
    id bigint NOT NULL,
    key character varying(255) NOT NULL,
    payload json,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_system_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_system_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_system_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_system_settings_id_seq OWNED BY public.rf_system_settings.id;


--
-- Name: rf_tenants; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_tenants (
    id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255),
    phone_number character varying(255) NOT NULL,
    national_phone_number character varying(255),
    phone_country_code character varying(255) NOT NULL,
    national_id character varying(255),
    nationality_id bigint,
    gender character varying(255),
    georgian_birthdate date,
    image character varying(255),
    active boolean DEFAULT true NOT NULL,
    last_active timestamp(0) without time zone,
    source_id bigint,
    accepted_invite boolean DEFAULT false NOT NULL,
    relation character varying(255),
    relation_key character varying(255),
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_tenants_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_tenants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_tenants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_tenants_id_seq OWNED BY public.rf_tenants.id;


--
-- Name: rf_transaction_additional_fees; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_transaction_additional_fees (
    id bigint NOT NULL,
    transaction_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    amount numeric(12,2) NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_transaction_additional_fees_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_transaction_additional_fees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_transaction_additional_fees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_transaction_additional_fees_id_seq OWNED BY public.rf_transaction_additional_fees.id;


--
-- Name: rf_transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_transactions (
    id bigint NOT NULL,
    lease_id bigint,
    unit_id bigint,
    category_id bigint NOT NULL,
    subcategory_id bigint,
    type_id bigint NOT NULL,
    status_id bigint NOT NULL,
    assignee_type character varying(255),
    assignee_id bigint,
    account_tenant_id bigint,
    amount numeric(12,2) NOT NULL,
    tax_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    rental_amount numeric(12,2),
    additional_fees_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    vat numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    due_on date NOT NULL,
    details text,
    lease_number character varying(255),
    is_paid boolean DEFAULT false NOT NULL,
    is_old boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_transactions_id_seq OWNED BY public.rf_transactions.id;


--
-- Name: rf_unit_areas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_areas (
    id bigint NOT NULL,
    unit_id bigint NOT NULL,
    type character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    size numeric(10,2),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_areas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_areas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_areas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_areas_id_seq OWNED BY public.rf_unit_areas.id;


--
-- Name: rf_unit_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_categories (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    icon character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_categories_id_seq OWNED BY public.rf_unit_categories.id;


--
-- Name: rf_unit_rooms; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_rooms (
    id bigint NOT NULL,
    unit_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    count integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_rooms_id_seq OWNED BY public.rf_unit_rooms.id;


--
-- Name: rf_unit_specifications; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_specifications (
    id bigint NOT NULL,
    unit_id bigint NOT NULL,
    key character varying(255) NOT NULL,
    value character varying(255),
    name_ar character varying(255),
    name_en character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_specifications_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_specifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_specifications_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_specifications_id_seq OWNED BY public.rf_unit_specifications.id;


--
-- Name: rf_unit_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_types (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    name_ar character varying(255),
    name_en character varying(255),
    icon character varying(255),
    category_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_types_id_seq OWNED BY public.rf_unit_types.id;


--
-- Name: rf_units; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_units (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    rf_community_id bigint NOT NULL,
    rf_building_id bigint,
    category_id bigint NOT NULL,
    type_id bigint NOT NULL,
    status_id bigint NOT NULL,
    city_id bigint,
    district_id bigint,
    account_tenant_id bigint,
    year_build integer,
    net_area numeric(10,2),
    floor_no integer,
    about text,
    map json,
    is_market_place boolean DEFAULT false NOT NULL,
    is_buy boolean DEFAULT false NOT NULL,
    is_off_plan_sale boolean DEFAULT false NOT NULL,
    renewal_status boolean DEFAULT false NOT NULL,
    marketplace_booking_unit_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    owner_id bigint,
    tenant_id bigint
);


--
-- Name: rf_units_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_units_id_seq OWNED BY public.rf_units.id;


--
-- Name: rf_working_days; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_working_days (
    id bigint NOT NULL,
    subcategory_id bigint NOT NULL,
    day character varying(255) NOT NULL,
    start time(0) without time zone,
    "end" time(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_working_days_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_working_days_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_working_days_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_working_days_id_seq OWNED BY public.rf_working_days.id;


--
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: subcategory_buildings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subcategory_buildings (
    id bigint NOT NULL,
    subcategory_id bigint NOT NULL,
    building_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: subcategory_buildings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subcategory_buildings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subcategory_buildings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subcategory_buildings_id_seq OWNED BY public.subcategory_buildings.id;


--
-- Name: subcategory_communities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subcategory_communities (
    id bigint NOT NULL,
    subcategory_id bigint NOT NULL,
    community_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: subcategory_communities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subcategory_communities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subcategory_communities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subcategory_communities_id_seq OWNED BY public.subcategory_communities.id;


--
-- Name: subscription_plan_features; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subscription_plan_features (
    id bigint NOT NULL,
    plan_id bigint NOT NULL,
    name json NOT NULL,
    slug character varying(255) NOT NULL,
    description json,
    value character varying(255) NOT NULL,
    resettable_period smallint DEFAULT '0'::smallint NOT NULL,
    resettable_interval character varying(255) DEFAULT 'month'::character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: subscription_plan_features_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subscription_plan_features_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subscription_plan_features_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subscription_plan_features_id_seq OWNED BY public.subscription_plan_features.id;


--
-- Name: subscription_plans; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.subscription_plans (
    id bigint NOT NULL,
    name json NOT NULL,
    slug character varying(255) NOT NULL,
    description json,
    is_active boolean DEFAULT true NOT NULL,
    price numeric(8,2) DEFAULT 0.00 NOT NULL,
    signup_fee numeric(8,2) DEFAULT 0.00 NOT NULL,
    currency character varying(3) NOT NULL,
    trial_period smallint DEFAULT '0'::smallint NOT NULL,
    trial_interval character varying(255) DEFAULT 'day'::character varying NOT NULL,
    invoice_period smallint DEFAULT '0'::smallint NOT NULL,
    invoice_interval character varying(255) DEFAULT 'month'::character varying NOT NULL,
    grace_period smallint DEFAULT '0'::smallint NOT NULL,
    grace_interval character varying(255) DEFAULT 'day'::character varying NOT NULL,
    prorate_day smallint,
    prorate_period smallint,
    prorate_extend_due smallint,
    active_subscribers_limit smallint,
    sort_order smallint DEFAULT '0'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: subscription_plans_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.subscription_plans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: subscription_plans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.subscription_plans_id_seq OWNED BY public.subscription_plans.id;


--
-- Name: tenants; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tenants (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    domain character varying(255),
    database character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: tenants_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.tenants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: tenants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.tenants_id_seq OWNED BY public.tenants.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone,
    phone_number character varying(20)
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: account_memberships id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_memberships ALTER COLUMN id SET DEFAULT nextval('public.account_memberships_id_seq'::regclass);


--
-- Name: account_subscription_usage id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_subscription_usage ALTER COLUMN id SET DEFAULT nextval('public.account_subscription_usage_id_seq'::regclass);


--
-- Name: account_subscriptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_subscriptions ALTER COLUMN id SET DEFAULT nextval('public.account_subscriptions_id_seq'::regclass);


--
-- Name: admin_buildings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_buildings ALTER COLUMN id SET DEFAULT nextval('public.admin_buildings_id_seq'::regclass);


--
-- Name: admin_communities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_communities ALTER COLUMN id SET DEFAULT nextval('public.admin_communities_id_seq'::regclass);


--
-- Name: admin_service_manager_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_service_manager_types ALTER COLUMN id SET DEFAULT nextval('public.admin_service_manager_types_id_seq'::regclass);


--
-- Name: cities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities ALTER COLUMN id SET DEFAULT nextval('public.cities_id_seq'::regclass);


--
-- Name: community_amenities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.community_amenities ALTER COLUMN id SET DEFAULT nextval('public.community_amenities_id_seq'::regclass);


--
-- Name: countries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries ALTER COLUMN id SET DEFAULT nextval('public.countries_id_seq'::regclass);


--
-- Name: currencies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.currencies ALTER COLUMN id SET DEFAULT nextval('public.currencies_id_seq'::regclass);


--
-- Name: districts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.districts ALTER COLUMN id SET DEFAULT nextval('public.districts_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: feature_unit id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit ALTER COLUMN id SET DEFAULT nextval('public.feature_unit_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: lease_units id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_units ALTER COLUMN id SET DEFAULT nextval('public.lease_units_id_seq'::regclass);


--
-- Name: media id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.media ALTER COLUMN id SET DEFAULT nextval('public.media_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: professional_subcategories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories ALTER COLUMN id SET DEFAULT nextval('public.professional_subcategories_id_seq'::regclass);


--
-- Name: rf_admins id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_admins ALTER COLUMN id SET DEFAULT nextval('public.rf_admins_id_seq'::regclass);


--
-- Name: rf_amenities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_amenities ALTER COLUMN id SET DEFAULT nextval('public.rf_amenities_id_seq'::regclass);


--
-- Name: rf_announcements id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_announcements ALTER COLUMN id SET DEFAULT nextval('public.rf_announcements_id_seq'::regclass);


--
-- Name: rf_buildings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_buildings ALTER COLUMN id SET DEFAULT nextval('public.rf_buildings_id_seq'::regclass);


--
-- Name: rf_common_lists id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_common_lists ALTER COLUMN id SET DEFAULT nextval('public.rf_common_lists_id_seq'::regclass);


--
-- Name: rf_communities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities ALTER COLUMN id SET DEFAULT nextval('public.rf_communities_id_seq'::regclass);


--
-- Name: rf_dependents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_dependents ALTER COLUMN id SET DEFAULT nextval('public.rf_dependents_id_seq'::regclass);


--
-- Name: rf_excel_sheets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheets ALTER COLUMN id SET DEFAULT nextval('public.rf_excel_sheets_id_seq'::regclass);


--
-- Name: rf_facilities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facilities ALTER COLUMN id SET DEFAULT nextval('public.rf_facilities_id_seq'::regclass);


--
-- Name: rf_facility_bookings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_bookings ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_bookings_id_seq'::regclass);


--
-- Name: rf_facility_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_categories ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_categories_id_seq'::regclass);


--
-- Name: rf_featured_services id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_featured_services ALTER COLUMN id SET DEFAULT nextval('public.rf_featured_services_id_seq'::regclass);


--
-- Name: rf_features id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_features ALTER COLUMN id SET DEFAULT nextval('public.rf_features_id_seq'::regclass);


--
-- Name: rf_form_templates id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_form_templates ALTER COLUMN id SET DEFAULT nextval('public.rf_form_templates_id_seq'::regclass);


--
-- Name: rf_invoice_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_invoice_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_invoice_settings_id_seq'::regclass);


--
-- Name: rf_lead_sources id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lead_sources ALTER COLUMN id SET DEFAULT nextval('public.rf_lead_sources_id_seq'::regclass);


--
-- Name: rf_leads id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leads ALTER COLUMN id SET DEFAULT nextval('public.rf_leads_id_seq'::regclass);


--
-- Name: rf_lease_additional_fees id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_additional_fees ALTER COLUMN id SET DEFAULT nextval('public.rf_lease_additional_fees_id_seq'::regclass);


--
-- Name: rf_lease_escalations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_escalations ALTER COLUMN id SET DEFAULT nextval('public.rf_lease_escalations_id_seq'::regclass);


--
-- Name: rf_leases id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases ALTER COLUMN id SET DEFAULT nextval('public.rf_leases_id_seq'::regclass);


--
-- Name: rf_manager_roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_manager_roles ALTER COLUMN id SET DEFAULT nextval('public.rf_manager_roles_id_seq'::regclass);


--
-- Name: rf_marketplace_offers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_offers ALTER COLUMN id SET DEFAULT nextval('public.rf_marketplace_offers_id_seq'::regclass);


--
-- Name: rf_marketplace_units id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_units ALTER COLUMN id SET DEFAULT nextval('public.rf_marketplace_units_id_seq'::regclass);


--
-- Name: rf_marketplace_visits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_visits ALTER COLUMN id SET DEFAULT nextval('public.rf_marketplace_visits_id_seq'::regclass);


--
-- Name: rf_owners id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owners ALTER COLUMN id SET DEFAULT nextval('public.rf_owners_id_seq'::regclass);


--
-- Name: rf_payments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_payments ALTER COLUMN id SET DEFAULT nextval('public.rf_payments_id_seq'::regclass);


--
-- Name: rf_professionals id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_professionals ALTER COLUMN id SET DEFAULT nextval('public.rf_professionals_id_seq'::regclass);


--
-- Name: rf_request_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_categories ALTER COLUMN id SET DEFAULT nextval('public.rf_request_categories_id_seq'::regclass);


--
-- Name: rf_request_subcategories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_subcategories ALTER COLUMN id SET DEFAULT nextval('public.rf_request_subcategories_id_seq'::regclass);


--
-- Name: rf_requests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests ALTER COLUMN id SET DEFAULT nextval('public.rf_requests_id_seq'::regclass);


--
-- Name: rf_service_manager_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_manager_types ALTER COLUMN id SET DEFAULT nextval('public.rf_service_manager_types_id_seq'::regclass);


--
-- Name: rf_service_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_service_settings_id_seq'::regclass);


--
-- Name: rf_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_settings_id_seq'::regclass);


--
-- Name: rf_statuses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_statuses ALTER COLUMN id SET DEFAULT nextval('public.rf_statuses_id_seq'::regclass);


--
-- Name: rf_system_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_system_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_system_settings_id_seq'::regclass);


--
-- Name: rf_tenants id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_tenants ALTER COLUMN id SET DEFAULT nextval('public.rf_tenants_id_seq'::regclass);


--
-- Name: rf_transaction_additional_fees id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transaction_additional_fees ALTER COLUMN id SET DEFAULT nextval('public.rf_transaction_additional_fees_id_seq'::regclass);


--
-- Name: rf_transactions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transactions ALTER COLUMN id SET DEFAULT nextval('public.rf_transactions_id_seq'::regclass);


--
-- Name: rf_unit_areas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_areas ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_areas_id_seq'::regclass);


--
-- Name: rf_unit_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_categories ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_categories_id_seq'::regclass);


--
-- Name: rf_unit_rooms id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_rooms ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_rooms_id_seq'::regclass);


--
-- Name: rf_unit_specifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_specifications ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_specifications_id_seq'::regclass);


--
-- Name: rf_unit_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_types ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_types_id_seq'::regclass);


--
-- Name: rf_units id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units ALTER COLUMN id SET DEFAULT nextval('public.rf_units_id_seq'::regclass);


--
-- Name: rf_working_days id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_working_days ALTER COLUMN id SET DEFAULT nextval('public.rf_working_days_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: subcategory_buildings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_buildings ALTER COLUMN id SET DEFAULT nextval('public.subcategory_buildings_id_seq'::regclass);


--
-- Name: subcategory_communities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_communities ALTER COLUMN id SET DEFAULT nextval('public.subcategory_communities_id_seq'::regclass);


--
-- Name: subscription_plan_features id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plan_features ALTER COLUMN id SET DEFAULT nextval('public.subscription_plan_features_id_seq'::regclass);


--
-- Name: subscription_plans id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plans ALTER COLUMN id SET DEFAULT nextval('public.subscription_plans_id_seq'::regclass);


--
-- Name: tenants id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants ALTER COLUMN id SET DEFAULT nextval('public.tenants_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: account_memberships account_memberships_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_memberships
    ADD CONSTRAINT account_memberships_pkey PRIMARY KEY (id);


--
-- Name: account_memberships account_memberships_user_id_account_tenant_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_memberships
    ADD CONSTRAINT account_memberships_user_id_account_tenant_id_unique UNIQUE (user_id, account_tenant_id);


--
-- Name: account_subscription_usage account_subscription_usage_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_subscription_usage
    ADD CONSTRAINT account_subscription_usage_pkey PRIMARY KEY (id);


--
-- Name: account_subscriptions account_subscriptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_subscriptions
    ADD CONSTRAINT account_subscriptions_pkey PRIMARY KEY (id);


--
-- Name: admin_buildings admin_buildings_admin_id_building_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_buildings
    ADD CONSTRAINT admin_buildings_admin_id_building_id_unique UNIQUE (admin_id, building_id);


--
-- Name: admin_buildings admin_buildings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_buildings
    ADD CONSTRAINT admin_buildings_pkey PRIMARY KEY (id);


--
-- Name: admin_communities admin_communities_admin_id_community_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_communities
    ADD CONSTRAINT admin_communities_admin_id_community_id_unique UNIQUE (admin_id, community_id);


--
-- Name: admin_communities admin_communities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_communities
    ADD CONSTRAINT admin_communities_pkey PRIMARY KEY (id);


--
-- Name: admin_service_manager_types admin_service_manager_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_service_manager_types
    ADD CONSTRAINT admin_service_manager_types_pkey PRIMARY KEY (id);


--
-- Name: admin_service_manager_types admin_smt_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_service_manager_types
    ADD CONSTRAINT admin_smt_unique UNIQUE (admin_id, service_manager_type_id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cities cities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities
    ADD CONSTRAINT cities_pkey PRIMARY KEY (id);


--
-- Name: community_amenities community_amenities_community_id_amenity_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.community_amenities
    ADD CONSTRAINT community_amenities_community_id_amenity_id_unique UNIQUE (community_id, amenity_id);


--
-- Name: community_amenities community_amenities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.community_amenities
    ADD CONSTRAINT community_amenities_pkey PRIMARY KEY (id);


--
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- Name: currencies currencies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.currencies
    ADD CONSTRAINT currencies_pkey PRIMARY KEY (id);


--
-- Name: districts districts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: feature_unit feature_unit_feature_id_unit_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit
    ADD CONSTRAINT feature_unit_feature_id_unit_id_unique UNIQUE (feature_id, unit_id);


--
-- Name: feature_unit feature_unit_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit
    ADD CONSTRAINT feature_unit_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: lease_units lease_units_lease_id_unit_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_units
    ADD CONSTRAINT lease_units_lease_id_unit_id_unique UNIQUE (lease_id, unit_id);


--
-- Name: lease_units lease_units_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_units
    ADD CONSTRAINT lease_units_pkey PRIMARY KEY (id);


--
-- Name: media media_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.media
    ADD CONSTRAINT media_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: professional_subcategories professional_subcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories
    ADD CONSTRAINT professional_subcategories_pkey PRIMARY KEY (id);


--
-- Name: professional_subcategories professional_subcategories_professional_id_subcategory_id_uniqu; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories
    ADD CONSTRAINT professional_subcategories_professional_id_subcategory_id_uniqu UNIQUE (professional_id, subcategory_id);


--
-- Name: rf_admins rf_admins_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_admins
    ADD CONSTRAINT rf_admins_pkey PRIMARY KEY (id);


--
-- Name: rf_amenities rf_amenities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_amenities
    ADD CONSTRAINT rf_amenities_pkey PRIMARY KEY (id);


--
-- Name: rf_announcements rf_announcements_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_announcements
    ADD CONSTRAINT rf_announcements_pkey PRIMARY KEY (id);


--
-- Name: rf_buildings rf_buildings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_buildings
    ADD CONSTRAINT rf_buildings_pkey PRIMARY KEY (id);


--
-- Name: rf_common_lists rf_common_lists_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_common_lists
    ADD CONSTRAINT rf_common_lists_pkey PRIMARY KEY (id);


--
-- Name: rf_communities rf_communities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities
    ADD CONSTRAINT rf_communities_pkey PRIMARY KEY (id);


--
-- Name: rf_dependents rf_dependents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_dependents
    ADD CONSTRAINT rf_dependents_pkey PRIMARY KEY (id);


--
-- Name: rf_excel_sheets rf_excel_sheets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheets
    ADD CONSTRAINT rf_excel_sheets_pkey PRIMARY KEY (id);


--
-- Name: rf_facilities rf_facilities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facilities
    ADD CONSTRAINT rf_facilities_pkey PRIMARY KEY (id);


--
-- Name: rf_facility_bookings rf_facility_bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_bookings
    ADD CONSTRAINT rf_facility_bookings_pkey PRIMARY KEY (id);


--
-- Name: rf_facility_categories rf_facility_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_categories
    ADD CONSTRAINT rf_facility_categories_pkey PRIMARY KEY (id);


--
-- Name: rf_featured_services rf_featured_services_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_featured_services
    ADD CONSTRAINT rf_featured_services_pkey PRIMARY KEY (id);


--
-- Name: rf_features rf_features_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_features
    ADD CONSTRAINT rf_features_pkey PRIMARY KEY (id);


--
-- Name: rf_form_templates rf_form_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_form_templates
    ADD CONSTRAINT rf_form_templates_pkey PRIMARY KEY (id);


--
-- Name: rf_invoice_settings rf_invoice_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_invoice_settings
    ADD CONSTRAINT rf_invoice_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_lead_sources rf_lead_sources_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lead_sources
    ADD CONSTRAINT rf_lead_sources_pkey PRIMARY KEY (id);


--
-- Name: rf_leads rf_leads_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leads
    ADD CONSTRAINT rf_leads_pkey PRIMARY KEY (id);


--
-- Name: rf_lease_additional_fees rf_lease_additional_fees_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_additional_fees
    ADD CONSTRAINT rf_lease_additional_fees_pkey PRIMARY KEY (id);


--
-- Name: rf_lease_escalations rf_lease_escalations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_escalations
    ADD CONSTRAINT rf_lease_escalations_pkey PRIMARY KEY (id);


--
-- Name: rf_leases rf_leases_contract_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_contract_number_unique UNIQUE (contract_number);


--
-- Name: rf_leases rf_leases_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_pkey PRIMARY KEY (id);


--
-- Name: rf_manager_roles rf_manager_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_manager_roles
    ADD CONSTRAINT rf_manager_roles_pkey PRIMARY KEY (id);


--
-- Name: rf_marketplace_offers rf_marketplace_offers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_offers
    ADD CONSTRAINT rf_marketplace_offers_pkey PRIMARY KEY (id);


--
-- Name: rf_marketplace_units rf_marketplace_units_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_units
    ADD CONSTRAINT rf_marketplace_units_pkey PRIMARY KEY (id);


--
-- Name: rf_marketplace_visits rf_marketplace_visits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_visits
    ADD CONSTRAINT rf_marketplace_visits_pkey PRIMARY KEY (id);


--
-- Name: rf_owners rf_owners_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owners
    ADD CONSTRAINT rf_owners_pkey PRIMARY KEY (id);


--
-- Name: rf_payments rf_payments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_payments
    ADD CONSTRAINT rf_payments_pkey PRIMARY KEY (id);


--
-- Name: rf_professionals rf_professionals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_professionals
    ADD CONSTRAINT rf_professionals_pkey PRIMARY KEY (id);


--
-- Name: rf_request_categories rf_request_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_categories
    ADD CONSTRAINT rf_request_categories_pkey PRIMARY KEY (id);


--
-- Name: rf_request_subcategories rf_request_subcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_subcategories
    ADD CONSTRAINT rf_request_subcategories_pkey PRIMARY KEY (id);


--
-- Name: rf_requests rf_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_pkey PRIMARY KEY (id);


--
-- Name: rf_service_manager_types rf_service_manager_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_manager_types
    ADD CONSTRAINT rf_service_manager_types_pkey PRIMARY KEY (id);


--
-- Name: rf_service_settings rf_service_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings
    ADD CONSTRAINT rf_service_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_settings rf_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings
    ADD CONSTRAINT rf_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_statuses rf_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_statuses
    ADD CONSTRAINT rf_statuses_pkey PRIMARY KEY (id);


--
-- Name: rf_system_settings rf_system_settings_account_tenant_id_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_system_settings
    ADD CONSTRAINT rf_system_settings_account_tenant_id_key_unique UNIQUE (account_tenant_id, key);


--
-- Name: rf_system_settings rf_system_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_system_settings
    ADD CONSTRAINT rf_system_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_tenants rf_tenants_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_tenants
    ADD CONSTRAINT rf_tenants_pkey PRIMARY KEY (id);


--
-- Name: rf_transaction_additional_fees rf_transaction_additional_fees_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transaction_additional_fees
    ADD CONSTRAINT rf_transaction_additional_fees_pkey PRIMARY KEY (id);


--
-- Name: rf_transactions rf_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transactions
    ADD CONSTRAINT rf_transactions_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_areas rf_unit_areas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_areas
    ADD CONSTRAINT rf_unit_areas_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_categories rf_unit_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_categories
    ADD CONSTRAINT rf_unit_categories_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_rooms rf_unit_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_rooms
    ADD CONSTRAINT rf_unit_rooms_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_specifications rf_unit_specifications_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_specifications
    ADD CONSTRAINT rf_unit_specifications_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_types rf_unit_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_types
    ADD CONSTRAINT rf_unit_types_pkey PRIMARY KEY (id);


--
-- Name: rf_units rf_units_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_pkey PRIMARY KEY (id);


--
-- Name: rf_working_days rf_working_days_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_working_days
    ADD CONSTRAINT rf_working_days_pkey PRIMARY KEY (id);


--
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: subcategory_buildings subcategory_buildings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_buildings
    ADD CONSTRAINT subcategory_buildings_pkey PRIMARY KEY (id);


--
-- Name: subcategory_buildings subcategory_buildings_subcategory_id_building_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_buildings
    ADD CONSTRAINT subcategory_buildings_subcategory_id_building_id_unique UNIQUE (subcategory_id, building_id);


--
-- Name: subcategory_communities subcategory_communities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_communities
    ADD CONSTRAINT subcategory_communities_pkey PRIMARY KEY (id);


--
-- Name: subcategory_communities subcategory_communities_subcategory_id_community_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_communities
    ADD CONSTRAINT subcategory_communities_subcategory_id_community_id_unique UNIQUE (subcategory_id, community_id);


--
-- Name: subscription_plan_features subscription_plan_features_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plan_features
    ADD CONSTRAINT subscription_plan_features_pkey PRIMARY KEY (id);


--
-- Name: subscription_plan_features subscription_plan_features_plan_id_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plan_features
    ADD CONSTRAINT subscription_plan_features_plan_id_slug_unique UNIQUE (plan_id, slug);


--
-- Name: subscription_plans subscription_plans_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plans
    ADD CONSTRAINT subscription_plans_pkey PRIMARY KEY (id);


--
-- Name: subscription_plans subscription_plans_slug_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subscription_plans
    ADD CONSTRAINT subscription_plans_slug_unique UNIQUE (slug);


--
-- Name: tenants tenants_database_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_database_unique UNIQUE (database);


--
-- Name: tenants tenants_domain_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_domain_unique UNIQUE (domain);


--
-- Name: tenants tenants_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: account_memberships_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX account_memberships_account_tenant_id_index ON public.account_memberships USING btree (account_tenant_id);


--
-- Name: account_subscriptions_subscriber_type_subscriber_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX account_subscriptions_subscriber_type_subscriber_id_index ON public.account_subscriptions USING btree (subscriber_type, subscriber_id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: media_collection_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_collection_index ON public.media USING btree (collection);


--
-- Name: media_mediable_type_mediable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX media_mediable_type_mediable_id_index ON public.media USING btree (mediable_type, mediable_id);


--
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: rf_admins_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_admins_account_tenant_id_index ON public.rf_admins USING btree (account_tenant_id);


--
-- Name: rf_announcements_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_announcements_account_tenant_id_index ON public.rf_announcements USING btree (account_tenant_id);


--
-- Name: rf_buildings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_buildings_account_tenant_id_index ON public.rf_buildings USING btree (account_tenant_id);


--
-- Name: rf_common_lists_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_common_lists_type_index ON public.rf_common_lists USING btree (type);


--
-- Name: rf_communities_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_communities_account_tenant_id_index ON public.rf_communities USING btree (account_tenant_id);


--
-- Name: rf_dependents_dependable_type_dependable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_dependents_dependable_type_dependable_id_index ON public.rf_dependents USING btree (dependable_type, dependable_id);


--
-- Name: rf_excel_sheets_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_excel_sheets_account_tenant_id_index ON public.rf_excel_sheets USING btree (account_tenant_id);


--
-- Name: rf_excel_sheets_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_excel_sheets_status_index ON public.rf_excel_sheets USING btree (status);


--
-- Name: rf_facilities_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_facilities_account_tenant_id_index ON public.rf_facilities USING btree (account_tenant_id);


--
-- Name: rf_facility_bookings_booker_type_booker_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_facility_bookings_booker_type_booker_id_index ON public.rf_facility_bookings USING btree (booker_type, booker_id);


--
-- Name: rf_form_templates_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_form_templates_account_tenant_id_index ON public.rf_form_templates USING btree (account_tenant_id);


--
-- Name: rf_invoice_settings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_invoice_settings_account_tenant_id_index ON public.rf_invoice_settings USING btree (account_tenant_id);


--
-- Name: rf_leads_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_leads_account_tenant_id_index ON public.rf_leads USING btree (account_tenant_id);


--
-- Name: rf_leases_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_leases_account_tenant_id_index ON public.rf_leases USING btree (account_tenant_id);


--
-- Name: rf_marketplace_offers_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_marketplace_offers_account_tenant_id_index ON public.rf_marketplace_offers USING btree (account_tenant_id);


--
-- Name: rf_marketplace_offers_discount_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_marketplace_offers_discount_type_index ON public.rf_marketplace_offers USING btree (discount_type);


--
-- Name: rf_owners_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_owners_account_tenant_id_index ON public.rf_owners USING btree (account_tenant_id);


--
-- Name: rf_professionals_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_professionals_account_tenant_id_index ON public.rf_professionals USING btree (account_tenant_id);


--
-- Name: rf_requests_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_account_tenant_id_index ON public.rf_requests USING btree (account_tenant_id);


--
-- Name: rf_requests_requester_type_requester_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_requester_type_requester_id_index ON public.rf_requests USING btree (requester_type, requester_id);


--
-- Name: rf_settings_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_settings_type_index ON public.rf_settings USING btree (type);


--
-- Name: rf_statuses_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_statuses_type_index ON public.rf_statuses USING btree (type);


--
-- Name: rf_system_settings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_system_settings_account_tenant_id_index ON public.rf_system_settings USING btree (account_tenant_id);


--
-- Name: rf_tenants_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_tenants_account_tenant_id_index ON public.rf_tenants USING btree (account_tenant_id);


--
-- Name: rf_transactions_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_account_tenant_id_index ON public.rf_transactions USING btree (account_tenant_id);


--
-- Name: rf_transactions_assignee_type_assignee_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_assignee_type_assignee_id_index ON public.rf_transactions USING btree (assignee_type, assignee_id);


--
-- Name: rf_transactions_due_on_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_due_on_index ON public.rf_transactions USING btree (due_on);


--
-- Name: rf_transactions_lease_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_lease_id_index ON public.rf_transactions USING btree (lease_id);


--
-- Name: rf_units_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_units_account_tenant_id_index ON public.rf_units USING btree (account_tenant_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: account_memberships account_memberships_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.account_memberships
    ADD CONSTRAINT account_memberships_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: admin_buildings admin_buildings_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_buildings
    ADD CONSTRAINT admin_buildings_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.rf_admins(id) ON DELETE CASCADE;


--
-- Name: admin_buildings admin_buildings_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_buildings
    ADD CONSTRAINT admin_buildings_building_id_foreign FOREIGN KEY (building_id) REFERENCES public.rf_buildings(id) ON DELETE CASCADE;


--
-- Name: admin_communities admin_communities_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_communities
    ADD CONSTRAINT admin_communities_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.rf_admins(id) ON DELETE CASCADE;


--
-- Name: admin_communities admin_communities_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_communities
    ADD CONSTRAINT admin_communities_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: admin_service_manager_types admin_service_manager_types_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_service_manager_types
    ADD CONSTRAINT admin_service_manager_types_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES public.rf_admins(id) ON DELETE CASCADE;


--
-- Name: admin_service_manager_types admin_service_manager_types_service_manager_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.admin_service_manager_types
    ADD CONSTRAINT admin_service_manager_types_service_manager_type_id_foreign FOREIGN KEY (service_manager_type_id) REFERENCES public.rf_service_manager_types(id) ON DELETE CASCADE;


--
-- Name: cities cities_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities
    ADD CONSTRAINT cities_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE CASCADE;


--
-- Name: community_amenities community_amenities_amenity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.community_amenities
    ADD CONSTRAINT community_amenities_amenity_id_foreign FOREIGN KEY (amenity_id) REFERENCES public.rf_amenities(id) ON DELETE CASCADE;


--
-- Name: community_amenities community_amenities_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.community_amenities
    ADD CONSTRAINT community_amenities_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: districts districts_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.districts
    ADD CONSTRAINT districts_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id) ON DELETE CASCADE;


--
-- Name: feature_unit feature_unit_feature_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit
    ADD CONSTRAINT feature_unit_feature_id_foreign FOREIGN KEY (feature_id) REFERENCES public.rf_features(id) ON DELETE CASCADE;


--
-- Name: feature_unit feature_unit_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit
    ADD CONSTRAINT feature_unit_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: lease_units lease_units_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_units
    ADD CONSTRAINT lease_units_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: lease_units lease_units_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_units
    ADD CONSTRAINT lease_units_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: professional_subcategories professional_subcategories_professional_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories
    ADD CONSTRAINT professional_subcategories_professional_id_foreign FOREIGN KEY (professional_id) REFERENCES public.rf_professionals(id) ON DELETE CASCADE;


--
-- Name: professional_subcategories professional_subcategories_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories
    ADD CONSTRAINT professional_subcategories_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE CASCADE;


--
-- Name: rf_announcements rf_announcements_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_announcements
    ADD CONSTRAINT rf_announcements_building_id_foreign FOREIGN KEY (building_id) REFERENCES public.rf_buildings(id) ON DELETE SET NULL;


--
-- Name: rf_announcements rf_announcements_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_announcements
    ADD CONSTRAINT rf_announcements_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


--
-- Name: rf_buildings rf_buildings_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_buildings
    ADD CONSTRAINT rf_buildings_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id);


--
-- Name: rf_buildings rf_buildings_district_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_buildings
    ADD CONSTRAINT rf_buildings_district_id_foreign FOREIGN KEY (district_id) REFERENCES public.districts(id);


--
-- Name: rf_buildings rf_buildings_rf_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_buildings
    ADD CONSTRAINT rf_buildings_rf_community_id_foreign FOREIGN KEY (rf_community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: rf_communities rf_communities_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities
    ADD CONSTRAINT rf_communities_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id);


--
-- Name: rf_communities rf_communities_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities
    ADD CONSTRAINT rf_communities_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id);


--
-- Name: rf_communities rf_communities_currency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities
    ADD CONSTRAINT rf_communities_currency_id_foreign FOREIGN KEY (currency_id) REFERENCES public.currencies(id);


--
-- Name: rf_communities rf_communities_district_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_communities
    ADD CONSTRAINT rf_communities_district_id_foreign FOREIGN KEY (district_id) REFERENCES public.districts(id);


--
-- Name: rf_excel_sheets rf_excel_sheets_rf_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheets
    ADD CONSTRAINT rf_excel_sheets_rf_community_id_foreign FOREIGN KEY (rf_community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


--
-- Name: rf_facilities rf_facilities_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facilities
    ADD CONSTRAINT rf_facilities_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_facility_categories(id) ON DELETE CASCADE;


--
-- Name: rf_facilities rf_facilities_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facilities
    ADD CONSTRAINT rf_facilities_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


--
-- Name: rf_facility_bookings rf_facility_bookings_facility_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_bookings
    ADD CONSTRAINT rf_facility_bookings_facility_id_foreign FOREIGN KEY (facility_id) REFERENCES public.rf_facilities(id) ON DELETE CASCADE;


--
-- Name: rf_facility_bookings rf_facility_bookings_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_bookings
    ADD CONSTRAINT rf_facility_bookings_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


--
-- Name: rf_featured_services rf_featured_services_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_featured_services
    ADD CONSTRAINT rf_featured_services_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE CASCADE;


--
-- Name: rf_form_templates rf_form_templates_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_form_templates
    ADD CONSTRAINT rf_form_templates_building_id_foreign FOREIGN KEY (building_id) REFERENCES public.rf_buildings(id) ON DELETE SET NULL;


--
-- Name: rf_form_templates rf_form_templates_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_form_templates
    ADD CONSTRAINT rf_form_templates_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


--
-- Name: rf_form_templates rf_form_templates_request_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_form_templates
    ADD CONSTRAINT rf_form_templates_request_category_id_foreign FOREIGN KEY (request_category_id) REFERENCES public.rf_request_categories(id) ON DELETE SET NULL;


--
-- Name: rf_leads rf_leads_source_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leads
    ADD CONSTRAINT rf_leads_source_id_foreign FOREIGN KEY (source_id) REFERENCES public.rf_lead_sources(id) ON DELETE SET NULL;


--
-- Name: rf_leads rf_leads_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leads
    ADD CONSTRAINT rf_leads_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id) ON DELETE SET NULL;


--
-- Name: rf_lease_additional_fees rf_lease_additional_fees_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_additional_fees
    ADD CONSTRAINT rf_lease_additional_fees_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: rf_lease_escalations rf_lease_escalations_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_lease_escalations
    ADD CONSTRAINT rf_lease_escalations_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: rf_leases rf_leases_lease_unit_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_lease_unit_type_id_foreign FOREIGN KEY (lease_unit_type_id) REFERENCES public.rf_unit_categories(id);


--
-- Name: rf_leases rf_leases_parent_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_parent_lease_id_foreign FOREIGN KEY (parent_lease_id) REFERENCES public.rf_leases(id) ON DELETE SET NULL;


--
-- Name: rf_leases rf_leases_payment_schedule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_payment_schedule_id_foreign FOREIGN KEY (payment_schedule_id) REFERENCES public.rf_settings(id);


--
-- Name: rf_leases rf_leases_rental_contract_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_rental_contract_type_id_foreign FOREIGN KEY (rental_contract_type_id) REFERENCES public.rf_settings(id);


--
-- Name: rf_leases rf_leases_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


--
-- Name: rf_leases rf_leases_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES public.rf_tenants(id);


--
-- Name: rf_marketplace_offers rf_marketplace_offers_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_offers
    ADD CONSTRAINT rf_marketplace_offers_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: rf_marketplace_units rf_marketplace_units_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_units
    ADD CONSTRAINT rf_marketplace_units_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: rf_marketplace_visits rf_marketplace_visits_marketplace_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_visits
    ADD CONSTRAINT rf_marketplace_visits_marketplace_unit_id_foreign FOREIGN KEY (marketplace_unit_id) REFERENCES public.rf_marketplace_units(id) ON DELETE CASCADE;


--
-- Name: rf_marketplace_visits rf_marketplace_visits_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_marketplace_visits
    ADD CONSTRAINT rf_marketplace_visits_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id) ON DELETE SET NULL;


--
-- Name: rf_payments rf_payments_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_payments
    ADD CONSTRAINT rf_payments_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.rf_transactions(id) ON DELETE CASCADE;


--
-- Name: rf_request_subcategories rf_request_subcategories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_subcategories
    ADD CONSTRAINT rf_request_subcategories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_request_categories(id) ON DELETE CASCADE;


--
-- Name: rf_requests rf_requests_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_building_id_foreign FOREIGN KEY (building_id) REFERENCES public.rf_buildings(id) ON DELETE SET NULL;


--
-- Name: rf_requests rf_requests_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_request_categories(id) ON DELETE CASCADE;


--
-- Name: rf_requests rf_requests_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


--
-- Name: rf_requests rf_requests_professional_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_professional_id_foreign FOREIGN KEY (professional_id) REFERENCES public.rf_professionals(id) ON DELETE SET NULL;


--
-- Name: rf_requests rf_requests_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


--
-- Name: rf_requests rf_requests_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE SET NULL;


--
-- Name: rf_requests rf_requests_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE SET NULL;


--
-- Name: rf_service_settings rf_service_settings_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings
    ADD CONSTRAINT rf_service_settings_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_request_categories(id) ON DELETE CASCADE;


--
-- Name: rf_settings rf_settings_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings
    ADD CONSTRAINT rf_settings_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.rf_settings(id) ON DELETE SET NULL;


--
-- Name: rf_transaction_additional_fees rf_transaction_additional_fees_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transaction_additional_fees
    ADD CONSTRAINT rf_transaction_additional_fees_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.rf_transactions(id) ON DELETE CASCADE;


--
-- Name: rf_transactions rf_transactions_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transactions
    ADD CONSTRAINT rf_transactions_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE SET NULL;


--
-- Name: rf_transactions rf_transactions_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transactions
    ADD CONSTRAINT rf_transactions_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


--
-- Name: rf_transactions rf_transactions_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_transactions
    ADD CONSTRAINT rf_transactions_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE SET NULL;


--
-- Name: rf_unit_areas rf_unit_areas_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_areas
    ADD CONSTRAINT rf_unit_areas_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: rf_unit_rooms rf_unit_rooms_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_rooms
    ADD CONSTRAINT rf_unit_rooms_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: rf_unit_specifications rf_unit_specifications_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_specifications
    ADD CONSTRAINT rf_unit_specifications_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: rf_unit_types rf_unit_types_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_types
    ADD CONSTRAINT rf_unit_types_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_unit_categories(id) ON DELETE CASCADE;


--
-- Name: rf_units rf_units_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_unit_categories(id);


--
-- Name: rf_units rf_units_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id);


--
-- Name: rf_units rf_units_district_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_district_id_foreign FOREIGN KEY (district_id) REFERENCES public.districts(id);


--
-- Name: rf_units rf_units_rf_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_rf_building_id_foreign FOREIGN KEY (rf_building_id) REFERENCES public.rf_buildings(id) ON DELETE SET NULL;


--
-- Name: rf_units rf_units_rf_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_rf_community_id_foreign FOREIGN KEY (rf_community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: rf_units rf_units_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


--
-- Name: rf_units rf_units_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_type_id_foreign FOREIGN KEY (type_id) REFERENCES public.rf_unit_types(id);


--
-- Name: rf_working_days rf_working_days_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_working_days
    ADD CONSTRAINT rf_working_days_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: subcategory_buildings subcategory_buildings_building_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_buildings
    ADD CONSTRAINT subcategory_buildings_building_id_foreign FOREIGN KEY (building_id) REFERENCES public.rf_buildings(id) ON DELETE CASCADE;


--
-- Name: subcategory_buildings subcategory_buildings_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_buildings
    ADD CONSTRAINT subcategory_buildings_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE CASCADE;


--
-- Name: subcategory_communities subcategory_communities_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_communities
    ADD CONSTRAINT subcategory_communities_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: subcategory_communities subcategory_communities_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.subcategory_communities
    ADD CONSTRAINT subcategory_communities_subcategory_id_foreign FOREIGN KEY (subcategory_id) REFERENCES public.rf_request_subcategories(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict y1Ywmy9By12UiX4fSgSxxWTTvCGEhY24rje68OkZbyg67F6I6gc6BRHgG4uSf25

--
-- PostgreSQL database dump
--

\restrict X2qeeBxidjMXbk405c2oxOmAO2sbjogkLs6JIbEjR5A9NKWW8pTtdYEi6mxA7qA

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3 (Ubuntu 18.3-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_08_14_170933_add_two_factor_columns_to_users_table	1
5	2026_04_20_011737_create_permission_tables	1
6	2026_04_20_012326_create_landlord_tenants_table	1
7	2026_04_20_021400_create_countries_table	1
8	2026_04_20_021401_create_cities_table	1
9	2026_04_20_021402_create_districts_table	1
10	2026_04_20_021403_create_currencies_table	1
11	2026_04_20_021404_create_statuses_table	1
12	2026_04_20_021405_create_settings_table	1
13	2026_04_20_021406_create_unit_categories_table	1
14	2026_04_20_021407_create_unit_types_table	1
15	2026_04_20_021408_create_media_table	1
16	2026_04_20_022510_create_account_memberships_table	1
17	2026_04_20_022511_create_communities_table	1
18	2026_04_20_022512_create_buildings_table	1
19	2026_04_20_022513_create_units_table	1
20	2026_04_20_022514_create_residents_table	1
21	2026_04_20_022515_create_owners_table	1
22	2026_04_20_022516_create_admins_table	1
23	2026_04_20_022517_create_manager_roles_table	1
24	2026_04_20_022518_create_professionals_table	1
25	2026_04_20_023000_create_leases_table	1
26	2026_04_20_023001_create_lease_units_table	1
27	2026_04_20_023002_create_lease_additional_fees_table	1
28	2026_04_20_023003_create_lease_escalations_table	1
29	2026_04_20_023004_create_transactions_table	1
30	2026_04_20_023005_create_payments_table	1
31	2026_04_20_023007_create_transaction_additional_fees_table	1
32	2026_04_20_023500_create_request_categories_table	1
33	2026_04_20_023501_create_request_subcategories_table	1
34	2026_04_20_023502_create_service_settings_table	1
35	2026_04_20_023503_create_working_days_table	1
36	2026_04_20_023504_create_featured_services_table	1
37	2026_04_20_023506_create_requests_table	1
38	2026_04_20_023507_create_facility_categories_table	1
39	2026_04_20_023508_create_facilities_table	1
40	2026_04_20_023509_create_facility_bookings_table	1
41	2026_04_20_023510_create_announcements_table	1
42	2026_04_20_024600_create_dependents_table	1
43	2026_04_20_024601_create_lead_sources_table	1
44	2026_04_20_024602_create_unit_specifications_table	1
45	2026_04_20_024603_create_unit_rooms_table	1
46	2026_04_20_024604_create_unit_areas_table	1
47	2026_04_20_024605_create_features_table	1
48	2026_04_20_024606_create_amenities_table	1
49	2026_04_20_024607_create_feature_unit_table	1
50	2026_04_20_024608_create_community_amenities_table	1
51	2026_04_20_024609_create_marketplace_units_table	1
52	2026_04_20_024610_create_marketplace_visits_table	1
53	2026_04_20_045641_add_columns_to_rf_communities_table	1
54	2026_04_20_045641_add_community_id_to_rf_facilities_table	1
55	2026_04_20_045641_modify_rf_announcements_table	1
56	2026_04_20_045642_add_columns_to_rf_requests_table	1
57	2026_04_20_045642_add_owner_tenant_to_rf_units_table	1
58	2026_04_20_045653_create_common_lists_table	1
59	2026_04_20_045653_create_invoice_settings_table	1
60	2026_04_20_045653_create_leads_table	1
61	2026_04_20_045653_create_service_manager_types_table	1
62	2026_04_20_045654_create_admin_buildings_table	1
63	2026_04_20_045654_create_admin_communities_table	1
64	2026_04_20_045654_create_professional_subcategories_table	1
65	2026_04_20_045654_create_subcategory_buildings_table	1
66	2026_04_20_045654_create_subcategory_communities_table	1
67	2026_04_20_045655_create_admin_service_manager_types_table	1
68	2026_04_20_065752_make_tenants_domain_and_database_nullable	1
69	2026_04_20_083202_add_type_to_features_table	1
70	2026_04_20_163146_create_system_settings_table	1
71	2026_04_20_163147_create_form_templates_table	1
72	2026_04_20_163148_create_excel_sheets_table	1
73	2026_04_20_163149_create_notifications_table	1
74	2026_04_20_191836_create_marketplace_offers_table	1
75	2026_04_22_021233_create_plans_table	1
76	2026_04_22_021234_create_plan_features_table	1
77	2026_04_22_021235_create_plan_subscriptions_table	1
78	2026_04_22_021236_create_plan_subscription_usage_table	1
79	2026_04_22_021237_remove_unique_slug_on_subscriptions_table	1
80	2026_04_22_021238_update_unique_keys_on_features_table	1
81	2026_04_22_021239_remove_cancels_at_from_subscriptions_table	1
82	2026_04_22_042005_add_phone_number_to_users_table	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 82, true);


--
-- PostgreSQL database dump complete
--

\unrestrict X2qeeBxidjMXbk405c2oxOmAO2sbjogkLs6JIbEjR5A9NKWW8pTtdYEi6mxA7qA

