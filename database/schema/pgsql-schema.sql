--
-- PostgreSQL database dump
--

\restrict X6WGykNtlyBV74PbG1UWCssiJis5MEgTP26nXCrgZGf9sufCLcVODcyIohcCuYb

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
-- Name: feature_flag_audit_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feature_flag_audit_logs (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    user_id bigint NOT NULL,
    flag_key character varying(100) NOT NULL,
    action character varying(20) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: feature_flag_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feature_flag_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feature_flag_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feature_flag_audit_logs_id_seq OWNED BY public.feature_flag_audit_logs.id;


--
-- Name: feature_flag_overrides; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feature_flag_overrides (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    flag_key character varying(100) NOT NULL,
    enabled boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: feature_flag_overrides_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feature_flag_overrides_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feature_flag_overrides_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feature_flag_overrides_id_seq OWNED BY public.feature_flag_overrides.id;


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
-- Name: invite_codes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.invite_codes (
    id bigint NOT NULL,
    code character varying(255) NOT NULL,
    contact_id bigint,
    tenant_id bigint,
    created_by bigint,
    used_by bigint,
    used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: invite_codes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.invite_codes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: invite_codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.invite_codes_id_seq OWNED BY public.invite_codes.id;


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
-- Name: lead_activities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lead_activities (
    id bigint NOT NULL,
    lead_id bigint NOT NULL,
    user_id bigint,
    type character varying(30) NOT NULL,
    data json,
    created_at timestamp(0) without time zone
);


--
-- Name: lead_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lead_activities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lead_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lead_activities_id_seq OWNED BY public.lead_activities.id;


--
-- Name: lease_amendments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_amendments (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    amended_by bigint NOT NULL,
    reason text NOT NULL,
    changes json NOT NULL,
    addendum_media_id bigint,
    amendment_number integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lease_amendments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_amendments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_amendments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_amendments_id_seq OWNED BY public.lease_amendments.id;


--
-- Name: lease_kyc_documents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_kyc_documents (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    document_type_id bigint NOT NULL,
    is_required boolean DEFAULT false NOT NULL,
    original_file_name character varying(255) NOT NULL,
    stored_path character varying(255) NOT NULL,
    mime_type character varying(255),
    file_size bigint,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: lease_kyc_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_kyc_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_kyc_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_kyc_documents_id_seq OWNED BY public.lease_kyc_documents.id;


--
-- Name: lease_notices; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_notices (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    tenant_id bigint NOT NULL,
    sent_by bigint NOT NULL,
    type character varying(255) NOT NULL,
    subject_en character varying(255) NOT NULL,
    body_en text NOT NULL,
    subject_ar character varying(255) NOT NULL,
    body_ar text NOT NULL,
    sent_at timestamp(0) without time zone,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lease_notices_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_notices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_notices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_notices_id_seq OWNED BY public.lease_notices.id;


--
-- Name: lease_quotes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_quotes (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    quote_number character varying(255),
    unit_id bigint NOT NULL,
    contact_id bigint NOT NULL,
    contract_type_id bigint,
    status_id bigint NOT NULL,
    duration_months integer NOT NULL,
    start_date date NOT NULL,
    rent_amount numeric(15,2) NOT NULL,
    payment_frequency_id bigint NOT NULL,
    security_deposit numeric(15,2) DEFAULT '0'::numeric NOT NULL,
    additional_charges json,
    special_conditions json,
    valid_until timestamp(0) without time zone NOT NULL,
    version integer DEFAULT 1 NOT NULL,
    parent_quote_id bigint,
    marketplace_unit_id bigint,
    created_by_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    public_token uuid,
    revision_note text,
    email_subject_prefix character varying(255),
    rejection_reason text
);


--
-- Name: lease_quotes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_quotes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_quotes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_quotes_id_seq OWNED BY public.lease_quotes.id;


--
-- Name: lease_renewal_offers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.lease_renewal_offers (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    status_id bigint NOT NULL,
    new_start_date date NOT NULL,
    duration_months integer NOT NULL,
    new_rent_amount numeric(12,2) NOT NULL,
    payment_frequency character varying(255),
    contract_type_id bigint,
    valid_until date NOT NULL,
    message_en text,
    message_ar text,
    created_by bigint NOT NULL,
    decided_at timestamp(0) without time zone,
    decided_by bigint,
    converted_lease_id bigint,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: lease_renewal_offers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.lease_renewal_offers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: lease_renewal_offers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.lease_renewal_offers_id_seq OWNED BY public.lease_renewal_offers.id;


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
    updated_at timestamp(0) without time zone,
    sort_order integer DEFAULT 0 NOT NULL,
    is_primary boolean DEFAULT false NOT NULL
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
    model_id bigint NOT NULL,
    community_id bigint,
    building_id bigint,
    service_type_id bigint,
    id bigint NOT NULL
);


--
-- Name: model_has_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.model_has_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: model_has_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.model_has_roles_id_seq OWNED BY public.model_has_roles.id;


--
-- Name: move_out_deductions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.move_out_deductions (
    id bigint NOT NULL,
    move_out_id bigint NOT NULL,
    label_en character varying(255) NOT NULL,
    label_ar character varying(255) NOT NULL,
    amount numeric(12,2) NOT NULL,
    reason character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: move_out_deductions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.move_out_deductions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: move_out_deductions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_out_deductions_id_seq OWNED BY public.move_out_deductions.id;


--
-- Name: move_out_rooms; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.move_out_rooms (
    id bigint NOT NULL,
    move_out_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    condition character varying(255),
    notes text,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: move_out_rooms_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.move_out_rooms_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: move_out_rooms_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_out_rooms_id_seq OWNED BY public.move_out_rooms.id;


--
-- Name: move_outs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.move_outs (
    id bigint NOT NULL,
    lease_id bigint NOT NULL,
    move_out_date date NOT NULL,
    reason character varying(255) NOT NULL,
    status_id bigint NOT NULL,
    initiated_by bigint NOT NULL,
    account_tenant_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: move_outs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.move_outs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: move_outs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.move_outs_id_seq OWNED BY public.move_outs.id;


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
    updated_at timestamp(0) without time zone,
    subject character varying(255),
    action character varying(255),
    name_en character varying(255),
    name_ar character varying(255),
    account_tenant_id bigint
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
-- Name: report_snapshots; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.report_snapshots (
    id bigint NOT NULL,
    account_tenant_id bigint,
    report_type character varying(255) NOT NULL,
    period_start date,
    period_end date,
    generated_at timestamp(0) without time zone,
    payload jsonb,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    requested_by_user_id bigint,
    filters jsonb,
    error_message text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: report_snapshots_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.report_snapshots_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: report_snapshots_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.report_snapshots_id_seq OWNED BY public.report_snapshots.id;


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
    account_tenant_id bigint,
    scheduled_at timestamp(0) without time zone,
    audience_type character varying(255) DEFAULT 'all'::character varying NOT NULL,
    audience_id bigint,
    is_priority boolean DEFAULT false NOT NULL,
    attachments json
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
-- Name: rf_app_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_app_settings (
    id bigint NOT NULL,
    account_tenant_id bigint,
    sidebar_label_overrides json,
    favicon_path character varying(255),
    login_bg_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    lease_alert_thresholds json
);


--
-- Name: rf_app_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_app_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_app_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_app_settings_id_seq OWNED BY public.rf_app_settings.id;


--
-- Name: rf_bank_accounts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_bank_accounts (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    community_id bigint,
    bank_name character varying(255) NOT NULL,
    account_name character varying(255) NOT NULL,
    account_number character varying(30) NOT NULL,
    iban character varying(34),
    currency character varying(3) DEFAULT 'SAR'::character varying NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_bank_accounts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_bank_accounts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_bank_accounts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_bank_accounts_id_seq OWNED BY public.rf_bank_accounts.id;


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
    listed_percentage numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    working_days json,
    latitude numeric(10,7),
    longitude numeric(11,7)
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
-- Name: rf_complaints; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_complaints (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    resident_id bigint,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    category character varying(255),
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    assigned_to bigint,
    resolution_notes text,
    resolved_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_complaints_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_complaints_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_complaints_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_complaints_id_seq OWNED BY public.rf_complaints.id;


--
-- Name: rf_contact_activities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_contact_activities (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    contact_type character varying(255) NOT NULL,
    contact_id bigint NOT NULL,
    event_type character varying(255) NOT NULL,
    metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_contact_activities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_contact_activities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_contact_activities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_contact_activities_id_seq OWNED BY public.rf_contact_activities.id;


--
-- Name: rf_contact_documents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_contact_documents (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    contact_type character varying(255) NOT NULL,
    contact_id bigint NOT NULL,
    type character varying(255) NOT NULL,
    file_path character varying(255) NOT NULL,
    original_name character varying(255) NOT NULL,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_contact_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_contact_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_contact_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_contact_documents_id_seq OWNED BY public.rf_contact_documents.id;


--
-- Name: rf_contract_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_contract_types (
    id bigint NOT NULL,
    account_tenant_id bigint,
    name_en character varying(255) NOT NULL,
    name_ar character varying(255),
    default_payment_terms_days smallint,
    default_escalation_type character varying(50),
    is_active boolean DEFAULT true NOT NULL,
    sort_order smallint DEFAULT '0'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_contract_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_contract_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_contract_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_contract_types_id_seq OWNED BY public.rf_contract_types.id;


--
-- Name: rf_dependents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_dependents (
    id bigint NOT NULL,
    dependable_type character varying(255) NOT NULL,
    dependable_id bigint NOT NULL,
    first_name character varying(255),
    last_name character varying(255),
    phone_number character varying(255),
    phone_country_code character varying(255),
    email character varying(255),
    national_id character varying(255),
    gender character varying(255),
    birthdate date,
    relationship character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    first_name_ar character varying(255),
    last_name_ar character varying(255)
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
-- Name: rf_directory_entries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_directory_entries (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    category character varying(255),
    phone_number character varying(20),
    email character varying(255),
    description text,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    created_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_directory_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_directory_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_directory_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_directory_entries_id_seq OWNED BY public.rf_directory_entries.id;


--
-- Name: rf_document_records; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_document_records (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    document_template_version_id bigint NOT NULL,
    source_type character varying(255),
    source_id bigint,
    generated_at timestamp(0) without time zone,
    file_path character varying(255),
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    signing_token character varying(64),
    sent_at timestamp(0) without time zone
);


--
-- Name: rf_document_records_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_document_records_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_document_records_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_document_records_id_seq OWNED BY public.rf_document_records.id;


--
-- Name: rf_document_signatures; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_document_signatures (
    id bigint NOT NULL,
    document_record_id bigint NOT NULL,
    signer_name character varying(255) NOT NULL,
    signer_email character varying(255) NOT NULL,
    signed_at timestamp(0) without time zone,
    ip_address character varying(45),
    otp_verified_at timestamp(0) without time zone,
    signed_file_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_document_signatures_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_document_signatures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_document_signatures_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_document_signatures_id_seq OWNED BY public.rf_document_signatures.id;


--
-- Name: rf_document_templates; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_document_templates (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    name json NOT NULL,
    type character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    format character varying(255) DEFAULT 'word_upload'::character varying NOT NULL,
    created_by bigint,
    current_version_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


--
-- Name: rf_document_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_document_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_document_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_document_templates_id_seq OWNED BY public.rf_document_templates.id;


--
-- Name: rf_document_versions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_document_versions (
    id bigint NOT NULL,
    document_template_id bigint NOT NULL,
    version_number integer NOT NULL,
    body text,
    file_path character varying(255),
    merge_fields json,
    published_at timestamp(0) without time zone,
    created_by bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_document_versions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_document_versions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_document_versions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_document_versions_id_seq OWNED BY public.rf_document_versions.id;


--
-- Name: rf_excel_sheet_imports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_excel_sheet_imports (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    excel_sheet_id bigint NOT NULL,
    imported_by bigint,
    imported_at timestamp(0) without time zone,
    total_rows integer DEFAULT 0 NOT NULL,
    successful_rows integer DEFAULT 0 NOT NULL,
    failed_rows integer DEFAULT 0 NOT NULL,
    error_details json,
    error_report_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_excel_sheet_imports_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_excel_sheet_imports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_excel_sheet_imports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_excel_sheet_imports_id_seq OWNED BY public.rf_excel_sheet_imports.id;


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
    updated_at timestamp(0) without time zone,
    import_type character varying(255),
    column_schema json,
    template_file_path character varying(255),
    total_rows integer,
    success_count integer,
    error_count integer,
    meta json
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
    account_tenant_id bigint,
    currency character varying(3) DEFAULT 'SAR'::character varying NOT NULL,
    type character varying(255) DEFAULT 'other'::character varying NOT NULL,
    pricing_mode character varying(255) DEFAULT 'free'::character varying NOT NULL,
    requires_booking boolean DEFAULT false NOT NULL,
    booking_horizon_days integer DEFAULT 14 NOT NULL,
    cancellation_hours_before integer DEFAULT 2 NOT NULL,
    min_booking_duration_minutes integer DEFAULT 30 NOT NULL,
    max_booking_duration_minutes integer,
    contract_required boolean DEFAULT false NOT NULL,
    notes text
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
-- Name: rf_facility_availability_rules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facility_availability_rules (
    id bigint NOT NULL,
    facility_id bigint NOT NULL,
    day_of_week smallint NOT NULL,
    open_time time(0) without time zone NOT NULL,
    close_time time(0) without time zone NOT NULL,
    slot_duration_minutes integer DEFAULT 60 NOT NULL,
    max_concurrent_bookings integer DEFAULT 1 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_facility_availability_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_facility_availability_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_facility_availability_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_facility_availability_rules_id_seq OWNED BY public.rf_facility_availability_rules.id;


--
-- Name: rf_facility_bookings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facility_bookings (
    id bigint NOT NULL,
    facility_id bigint NOT NULL,
    status_id bigint,
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
    deleted_at timestamp(0) without time zone,
    account_tenant_id bigint,
    booked_by_type character varying(255),
    start_at timestamp(0) without time zone,
    end_at timestamp(0) without time zone,
    cancelled_at timestamp(0) without time zone,
    cancellation_reason character varying(255),
    cancellation_by_type character varying(255),
    invoice_id bigint,
    contract_document_id bigint,
    checked_in_at timestamp(0) without time zone,
    checked_out_at timestamp(0) without time zone,
    checked_in_by bigint,
    purpose character varying(255)
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
-- Name: rf_facility_waitlist; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_facility_waitlist (
    id bigint NOT NULL,
    facility_id bigint NOT NULL,
    resident_id bigint NOT NULL,
    requested_start_at timestamp(0) without time zone NOT NULL,
    requested_end_at timestamp(0) without time zone NOT NULL,
    notified_at timestamp(0) without time zone,
    ttl_expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    user_id bigint,
    account_tenant_id bigint,
    date date,
    start_time character varying(5),
    end_time character varying(5)
);


--
-- Name: rf_facility_waitlist_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_facility_waitlist_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_facility_waitlist_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_facility_waitlist_id_seq OWNED BY public.rf_facility_waitlist.id;


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
    updated_at timestamp(0) without time zone,
    name_en character varying(255),
    name_ar character varying(255),
    logo_path character varying(255),
    logo_ar_path character varying(255),
    timezone character varying(64) DEFAULT 'UTC'::character varying NOT NULL,
    primary_color character varying(7),
    invoice_prefix character varying(20) DEFAULT 'INV'::character varying NOT NULL,
    invoice_next_sequence bigint DEFAULT '1'::bigint NOT NULL,
    payment_terms_days smallint DEFAULT '30'::smallint NOT NULL,
    late_payment_penalty_pct numeric(5,2),
    late_payment_grace_days smallint DEFAULT '0'::smallint NOT NULL,
    footer_text_en text,
    footer_text_ar text,
    show_vat_number boolean DEFAULT true NOT NULL
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
    updated_at timestamp(0) without time zone,
    name_en character varying(255),
    name_ar character varying(255),
    phone_country_code character varying(5),
    notes text,
    assigned_to_user_id bigint,
    lost_reason text
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
    deleted_at timestamp(0) without time zone,
    quote_id bigint,
    kyc_complete boolean DEFAULT false NOT NULL,
    kyc_submitted_at timestamp(0) without time zone,
    approved_by_id bigint,
    approved_at timestamp(0) without time zone,
    rejected_by_id bigint,
    rejected_at timestamp(0) without time zone,
    rejection_reason text,
    current_amendment_number integer DEFAULT 0 NOT NULL
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
-- Name: rf_notification_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_notification_preferences (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    trigger_key character varying(255) NOT NULL,
    domain character varying(255) NOT NULL,
    email_enabled boolean DEFAULT true NOT NULL,
    sms_enabled boolean DEFAULT false NOT NULL,
    email_template json,
    sms_template json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_notification_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_notification_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_notification_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_notification_preferences_id_seq OWNED BY public.rf_notification_preferences.id;


--
-- Name: rf_owner_registrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_owner_registrations (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    first_name character varying(255) NOT NULL,
    last_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    phone_number character varying(20) NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    submitted_data json,
    reviewed_by bigint,
    reviewed_at timestamp(0) without time zone,
    review_notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_owner_registrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_owner_registrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_owner_registrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_owner_registrations_id_seq OWNED BY public.rf_owner_registrations.id;


--
-- Name: rf_owners; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_owners (
    id bigint NOT NULL,
    first_name character varying(255),
    last_name character varying(255),
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
    deleted_at timestamp(0) without time zone,
    first_name_ar character varying(255),
    last_name_ar character varying(255),
    id_type character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL
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
    first_name character varying(255),
    last_name character varying(255),
    email character varying(255),
    phone_number character varying(255) NOT NULL,
    phone_country_code character varying(255) NOT NULL,
    national_id character varying(255),
    image character varying(255),
    active boolean DEFAULT true NOT NULL,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    first_name_ar character varying(255),
    last_name_ar character varying(255),
    id_type character varying(255),
    national_phone_number character varying(255),
    deleted_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL
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
-- Name: rf_receipts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_receipts (
    id bigint NOT NULL,
    transaction_id bigint NOT NULL,
    account_tenant_id bigint,
    status character varying(30) DEFAULT 'generated'::character varying NOT NULL,
    pdf_path character varying(500),
    sent_at timestamp(0) without time zone,
    sent_to_name character varying(255),
    sent_to_email character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_receipts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_receipts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_receipts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_receipts_id_seq OWNED BY public.rf_receipts.id;


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
    completed_at timestamp(0) without time zone,
    scheduled_date date,
    completed_date date,
    service_category_id bigint,
    urgency character varying(255) DEFAULT 'normal'::character varying NOT NULL,
    room_location character varying(255),
    sla_response_due_at timestamp(0) without time zone,
    sla_resolution_due_at timestamp(0) without time zone,
    service_subcategory_id bigint,
    assigned_to_user_id bigint,
    sla_breach_response boolean DEFAULT false NOT NULL,
    sla_breach_resolution boolean DEFAULT false NOT NULL,
    rating smallint,
    feedback text,
    source_complaint_id bigint
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
-- Name: rf_service_request_messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_service_request_messages (
    id bigint NOT NULL,
    service_request_id bigint NOT NULL,
    sender_type character varying(255) NOT NULL,
    sender_id bigint NOT NULL,
    body text NOT NULL,
    is_internal boolean DEFAULT false NOT NULL,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_service_request_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_service_request_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_service_request_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_service_request_messages_id_seq OWNED BY public.rf_service_request_messages.id;


--
-- Name: rf_service_request_timeline_events; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_service_request_timeline_events (
    id bigint NOT NULL,
    service_request_id bigint NOT NULL,
    event_type character varying(50) NOT NULL,
    actor_type character varying(255),
    actor_id bigint,
    metadata json,
    account_tenant_id bigint,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: rf_service_request_timeline_events_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_service_request_timeline_events_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_service_request_timeline_events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_service_request_timeline_events_id_seq OWNED BY public.rf_service_request_timeline_events.id;


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
    updated_at timestamp(0) without time zone,
    account_tenant_id bigint
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
    updated_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    subtype character varying(255),
    metadata json,
    sort_order integer DEFAULT 0 NOT NULL
);


--
-- Name: rf_settings_audit_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_settings_audit_logs (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    user_id bigint,
    setting_group character varying(255) NOT NULL,
    setting_key character varying(255) NOT NULL,
    old_value text,
    new_value text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_settings_audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_settings_audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_settings_audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_settings_audit_logs_id_seq OWNED BY public.rf_settings_audit_logs.id;


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
-- Name: rf_suggestions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_suggestions (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    resident_id bigint,
    title character varying(255) NOT NULL,
    description text NOT NULL,
    is_anonymous boolean DEFAULT false NOT NULL,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    upvotes_count integer DEFAULT 0 NOT NULL,
    reviewed_by bigint,
    admin_response text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_suggestions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_suggestions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_suggestions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_suggestions_id_seq OWNED BY public.rf_suggestions.id;


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
    first_name character varying(255),
    last_name character varying(255),
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
    deleted_at timestamp(0) without time zone,
    first_name_ar character varying(255),
    last_name_ar character varying(255),
    id_type character varying(255),
    status character varying(255) DEFAULT 'active'::character varying NOT NULL
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
    category_id bigint,
    subcategory_id bigint,
    type_id bigint,
    status_id bigint,
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
    deleted_at timestamp(0) without time zone,
    direction character varying(20) DEFAULT 'money_in'::character varying NOT NULL,
    payment_method character varying(30),
    reference_number character varying(100),
    is_reconciled boolean DEFAULT false NOT NULL,
    reconciled_at timestamp(0) without time zone,
    reconciled_by bigint
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
-- Name: rf_unit_ownerships; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_ownerships (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    owner_id bigint NOT NULL,
    unit_id bigint NOT NULL,
    ownership_type character varying(255) DEFAULT 'full'::character varying NOT NULL,
    ownership_percentage numeric(5,2) DEFAULT '100'::numeric NOT NULL,
    start_date date,
    end_date date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_ownerships_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_ownerships_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_ownerships_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_ownerships_id_seq OWNED BY public.rf_unit_ownerships.id;


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
-- Name: rf_unit_status_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_unit_status_history (
    id bigint NOT NULL,
    account_tenant_id bigint NOT NULL,
    unit_id bigint NOT NULL,
    from_status character varying(255),
    to_status character varying(255) NOT NULL,
    changed_by bigint,
    reason text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_unit_status_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_unit_status_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_unit_status_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_unit_status_history_id_seq OWNED BY public.rf_unit_status_history.id;


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
    tenant_id bigint,
    status character varying(255) DEFAULT 'available'::character varying NOT NULL,
    currency_id bigint,
    asking_rent_amount numeric(12,2),
    rent_period character varying(255),
    CONSTRAINT rf_units_rent_period_check CHECK (((rent_period)::text = ANY (ARRAY[('month'::character varying)::text, ('year'::character varying)::text])))
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
-- Name: rf_visitor_access_settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_visitor_access_settings (
    id bigint NOT NULL,
    account_tenant_id bigint,
    community_id bigint NOT NULL,
    require_id_verification boolean DEFAULT false NOT NULL,
    allow_walk_in boolean DEFAULT true NOT NULL,
    qr_expiry_minutes smallint DEFAULT '1440'::smallint NOT NULL,
    max_uses_per_invitation smallint DEFAULT '1'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_visitor_access_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_visitor_access_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_visitor_access_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_visitor_access_settings_id_seq OWNED BY public.rf_visitor_access_settings.id;


--
-- Name: rf_visitor_invitations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_visitor_invitations (
    id bigint NOT NULL,
    account_tenant_id bigint,
    community_id bigint,
    resident_id bigint NOT NULL,
    visitor_name character varying(255) NOT NULL,
    visitor_phone character varying(50),
    visitor_purpose character varying(50) DEFAULT 'visit'::character varying NOT NULL,
    expected_at timestamp(0) without time zone NOT NULL,
    valid_until timestamp(0) without time zone NOT NULL,
    status character varying(50) DEFAULT 'pending'::character varying NOT NULL,
    notes text,
    qr_code_token character(32) NOT NULL,
    qr_code_sent_via character varying(50) DEFAULT 'none'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_visitor_invitations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_visitor_invitations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_visitor_invitations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_visitor_invitations_id_seq OWNED BY public.rf_visitor_invitations.id;


--
-- Name: rf_visitor_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rf_visitor_logs (
    id bigint NOT NULL,
    account_tenant_id bigint,
    invitation_id bigint,
    community_id bigint NOT NULL,
    visitor_name character varying(255) NOT NULL,
    visitor_phone character varying(50),
    purpose character varying(50),
    gate_officer_id bigint NOT NULL,
    entry_at timestamp(0) without time zone NOT NULL,
    exit_at timestamp(0) without time zone,
    id_verified boolean DEFAULT false NOT NULL,
    photo_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: rf_visitor_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rf_visitor_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rf_visitor_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rf_visitor_logs_id_seq OWNED BY public.rf_visitor_logs.id;


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
    updated_at timestamp(0) without time zone,
    name_ar character varying(255),
    name_en character varying(255),
    type character varying(255),
    account_tenant_id bigint
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
-- Name: service_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_categories (
    id bigint NOT NULL,
    account_tenant_id bigint,
    name_en character varying(255) NOT NULL,
    name_ar character varying(255) NOT NULL,
    icon character varying(255) DEFAULT '🔧'::character varying NOT NULL,
    response_sla_hours integer,
    resolution_sla_hours integer,
    default_assignee_id bigint,
    require_completion_photo boolean DEFAULT false NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_categories_id_seq OWNED BY public.service_categories.id;


--
-- Name: service_category_communities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_category_communities (
    service_category_id bigint NOT NULL,
    community_id bigint NOT NULL
);


--
-- Name: service_subcategories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_subcategories (
    id bigint NOT NULL,
    service_category_id bigint NOT NULL,
    name_en character varying(255) NOT NULL,
    name_ar character varying(255) NOT NULL,
    response_sla_hours integer,
    resolution_sla_hours integer,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_subcategories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_subcategories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_subcategories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_subcategories_id_seq OWNED BY public.service_subcategories.id;


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
    phone_number character varying(20),
    locale character varying(5),
    avatar_path character varying(255),
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    invitation_token character varying(64),
    invitation_expires_at timestamp(0) without time zone
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
-- Name: feature_flag_audit_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_flag_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.feature_flag_audit_logs_id_seq'::regclass);


--
-- Name: feature_flag_overrides id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_flag_overrides ALTER COLUMN id SET DEFAULT nextval('public.feature_flag_overrides_id_seq'::regclass);


--
-- Name: feature_unit id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_unit ALTER COLUMN id SET DEFAULT nextval('public.feature_unit_id_seq'::regclass);


--
-- Name: invite_codes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes ALTER COLUMN id SET DEFAULT nextval('public.invite_codes_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: lead_activities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lead_activities ALTER COLUMN id SET DEFAULT nextval('public.lead_activities_id_seq'::regclass);


--
-- Name: lease_amendments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_amendments ALTER COLUMN id SET DEFAULT nextval('public.lease_amendments_id_seq'::regclass);


--
-- Name: lease_kyc_documents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_kyc_documents ALTER COLUMN id SET DEFAULT nextval('public.lease_kyc_documents_id_seq'::regclass);


--
-- Name: lease_notices id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices ALTER COLUMN id SET DEFAULT nextval('public.lease_notices_id_seq'::regclass);


--
-- Name: lease_quotes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes ALTER COLUMN id SET DEFAULT nextval('public.lease_quotes_id_seq'::regclass);


--
-- Name: lease_renewal_offers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers ALTER COLUMN id SET DEFAULT nextval('public.lease_renewal_offers_id_seq'::regclass);


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
-- Name: model_has_roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles ALTER COLUMN id SET DEFAULT nextval('public.model_has_roles_id_seq'::regclass);


--
-- Name: move_out_deductions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_deductions ALTER COLUMN id SET DEFAULT nextval('public.move_out_deductions_id_seq'::regclass);


--
-- Name: move_out_rooms id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_rooms ALTER COLUMN id SET DEFAULT nextval('public.move_out_rooms_id_seq'::regclass);


--
-- Name: move_outs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_outs ALTER COLUMN id SET DEFAULT nextval('public.move_outs_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: professional_subcategories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.professional_subcategories ALTER COLUMN id SET DEFAULT nextval('public.professional_subcategories_id_seq'::regclass);


--
-- Name: report_snapshots id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.report_snapshots ALTER COLUMN id SET DEFAULT nextval('public.report_snapshots_id_seq'::regclass);


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
-- Name: rf_app_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_app_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_app_settings_id_seq'::regclass);


--
-- Name: rf_bank_accounts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_bank_accounts ALTER COLUMN id SET DEFAULT nextval('public.rf_bank_accounts_id_seq'::regclass);


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
-- Name: rf_complaints id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_complaints ALTER COLUMN id SET DEFAULT nextval('public.rf_complaints_id_seq'::regclass);


--
-- Name: rf_contact_activities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_activities ALTER COLUMN id SET DEFAULT nextval('public.rf_contact_activities_id_seq'::regclass);


--
-- Name: rf_contact_documents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_documents ALTER COLUMN id SET DEFAULT nextval('public.rf_contact_documents_id_seq'::regclass);


--
-- Name: rf_contract_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contract_types ALTER COLUMN id SET DEFAULT nextval('public.rf_contract_types_id_seq'::regclass);


--
-- Name: rf_dependents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_dependents ALTER COLUMN id SET DEFAULT nextval('public.rf_dependents_id_seq'::regclass);


--
-- Name: rf_directory_entries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_directory_entries ALTER COLUMN id SET DEFAULT nextval('public.rf_directory_entries_id_seq'::regclass);


--
-- Name: rf_document_records id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_records ALTER COLUMN id SET DEFAULT nextval('public.rf_document_records_id_seq'::regclass);


--
-- Name: rf_document_signatures id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_signatures ALTER COLUMN id SET DEFAULT nextval('public.rf_document_signatures_id_seq'::regclass);


--
-- Name: rf_document_templates id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_templates ALTER COLUMN id SET DEFAULT nextval('public.rf_document_templates_id_seq'::regclass);


--
-- Name: rf_document_versions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_versions ALTER COLUMN id SET DEFAULT nextval('public.rf_document_versions_id_seq'::regclass);


--
-- Name: rf_excel_sheet_imports id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheet_imports ALTER COLUMN id SET DEFAULT nextval('public.rf_excel_sheet_imports_id_seq'::regclass);


--
-- Name: rf_excel_sheets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheets ALTER COLUMN id SET DEFAULT nextval('public.rf_excel_sheets_id_seq'::regclass);


--
-- Name: rf_facilities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facilities ALTER COLUMN id SET DEFAULT nextval('public.rf_facilities_id_seq'::regclass);


--
-- Name: rf_facility_availability_rules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_availability_rules ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_availability_rules_id_seq'::regclass);


--
-- Name: rf_facility_bookings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_bookings ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_bookings_id_seq'::regclass);


--
-- Name: rf_facility_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_categories ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_categories_id_seq'::regclass);


--
-- Name: rf_facility_waitlist id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_waitlist ALTER COLUMN id SET DEFAULT nextval('public.rf_facility_waitlist_id_seq'::regclass);


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
-- Name: rf_notification_preferences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_notification_preferences ALTER COLUMN id SET DEFAULT nextval('public.rf_notification_preferences_id_seq'::regclass);


--
-- Name: rf_owner_registrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owner_registrations ALTER COLUMN id SET DEFAULT nextval('public.rf_owner_registrations_id_seq'::regclass);


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
-- Name: rf_receipts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_receipts ALTER COLUMN id SET DEFAULT nextval('public.rf_receipts_id_seq'::regclass);


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
-- Name: rf_service_request_messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_messages ALTER COLUMN id SET DEFAULT nextval('public.rf_service_request_messages_id_seq'::regclass);


--
-- Name: rf_service_request_timeline_events id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_timeline_events ALTER COLUMN id SET DEFAULT nextval('public.rf_service_request_timeline_events_id_seq'::regclass);


--
-- Name: rf_service_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_service_settings_id_seq'::regclass);


--
-- Name: rf_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_settings_id_seq'::regclass);


--
-- Name: rf_settings_audit_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings_audit_logs ALTER COLUMN id SET DEFAULT nextval('public.rf_settings_audit_logs_id_seq'::regclass);


--
-- Name: rf_statuses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_statuses ALTER COLUMN id SET DEFAULT nextval('public.rf_statuses_id_seq'::regclass);


--
-- Name: rf_suggestions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_suggestions ALTER COLUMN id SET DEFAULT nextval('public.rf_suggestions_id_seq'::regclass);


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
-- Name: rf_unit_ownerships id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_ownerships_id_seq'::regclass);


--
-- Name: rf_unit_rooms id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_rooms ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_rooms_id_seq'::regclass);


--
-- Name: rf_unit_specifications id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_specifications ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_specifications_id_seq'::regclass);


--
-- Name: rf_unit_status_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_status_history ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_status_history_id_seq'::regclass);


--
-- Name: rf_unit_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_types ALTER COLUMN id SET DEFAULT nextval('public.rf_unit_types_id_seq'::regclass);


--
-- Name: rf_units id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units ALTER COLUMN id SET DEFAULT nextval('public.rf_units_id_seq'::regclass);


--
-- Name: rf_visitor_access_settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_access_settings ALTER COLUMN id SET DEFAULT nextval('public.rf_visitor_access_settings_id_seq'::regclass);


--
-- Name: rf_visitor_invitations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_invitations ALTER COLUMN id SET DEFAULT nextval('public.rf_visitor_invitations_id_seq'::regclass);


--
-- Name: rf_visitor_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_logs ALTER COLUMN id SET DEFAULT nextval('public.rf_visitor_logs_id_seq'::regclass);


--
-- Name: rf_working_days id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_working_days ALTER COLUMN id SET DEFAULT nextval('public.rf_working_days_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: service_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_categories ALTER COLUMN id SET DEFAULT nextval('public.service_categories_id_seq'::regclass);


--
-- Name: service_subcategories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_subcategories ALTER COLUMN id SET DEFAULT nextval('public.service_subcategories_id_seq'::regclass);


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
-- Name: feature_flag_audit_logs feature_flag_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_flag_audit_logs
    ADD CONSTRAINT feature_flag_audit_logs_pkey PRIMARY KEY (id);


--
-- Name: feature_flag_overrides feature_flag_overrides_account_tenant_id_flag_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_flag_overrides
    ADD CONSTRAINT feature_flag_overrides_account_tenant_id_flag_key_unique UNIQUE (account_tenant_id, flag_key);


--
-- Name: feature_flag_overrides feature_flag_overrides_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feature_flag_overrides
    ADD CONSTRAINT feature_flag_overrides_pkey PRIMARY KEY (id);


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
-- Name: invite_codes invite_codes_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes
    ADD CONSTRAINT invite_codes_code_unique UNIQUE (code);


--
-- Name: invite_codes invite_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes
    ADD CONSTRAINT invite_codes_pkey PRIMARY KEY (id);


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
-- Name: lead_activities lead_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lead_activities
    ADD CONSTRAINT lead_activities_pkey PRIMARY KEY (id);


--
-- Name: lease_amendments lease_amendments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_amendments
    ADD CONSTRAINT lease_amendments_pkey PRIMARY KEY (id);


--
-- Name: lease_kyc_documents lease_kyc_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_kyc_documents
    ADD CONSTRAINT lease_kyc_documents_pkey PRIMARY KEY (id);


--
-- Name: lease_notices lease_notices_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices
    ADD CONSTRAINT lease_notices_pkey PRIMARY KEY (id);


--
-- Name: lease_quotes lease_quotes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_pkey PRIMARY KEY (id);


--
-- Name: lease_quotes lease_quotes_public_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_public_token_unique UNIQUE (public_token);


--
-- Name: lease_quotes lease_quotes_quote_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_quote_number_unique UNIQUE (quote_number);


--
-- Name: lease_renewal_offers lease_renewal_offers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_pkey PRIMARY KEY (id);


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
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (id);


--
-- Name: move_out_deductions move_out_deductions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_deductions
    ADD CONSTRAINT move_out_deductions_pkey PRIMARY KEY (id);


--
-- Name: move_out_rooms move_out_rooms_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_rooms
    ADD CONSTRAINT move_out_rooms_pkey PRIMARY KEY (id);


--
-- Name: move_outs move_outs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_outs
    ADD CONSTRAINT move_outs_pkey PRIMARY KEY (id);


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
-- Name: report_snapshots report_snapshots_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.report_snapshots
    ADD CONSTRAINT report_snapshots_pkey PRIMARY KEY (id);


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
-- Name: rf_app_settings rf_app_settings_account_tenant_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_app_settings
    ADD CONSTRAINT rf_app_settings_account_tenant_id_unique UNIQUE (account_tenant_id);


--
-- Name: rf_app_settings rf_app_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_app_settings
    ADD CONSTRAINT rf_app_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_bank_accounts rf_bank_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_bank_accounts
    ADD CONSTRAINT rf_bank_accounts_pkey PRIMARY KEY (id);


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
-- Name: rf_complaints rf_complaints_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_complaints
    ADD CONSTRAINT rf_complaints_pkey PRIMARY KEY (id);


--
-- Name: rf_contact_activities rf_contact_activities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_activities
    ADD CONSTRAINT rf_contact_activities_pkey PRIMARY KEY (id);


--
-- Name: rf_contact_documents rf_contact_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_documents
    ADD CONSTRAINT rf_contact_documents_pkey PRIMARY KEY (id);


--
-- Name: rf_contract_types rf_contract_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contract_types
    ADD CONSTRAINT rf_contract_types_pkey PRIMARY KEY (id);


--
-- Name: rf_contract_types rf_contract_types_tenant_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contract_types
    ADD CONSTRAINT rf_contract_types_tenant_name_unique UNIQUE (account_tenant_id, name_en);


--
-- Name: rf_dependents rf_dependents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_dependents
    ADD CONSTRAINT rf_dependents_pkey PRIMARY KEY (id);


--
-- Name: rf_directory_entries rf_directory_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_directory_entries
    ADD CONSTRAINT rf_directory_entries_pkey PRIMARY KEY (id);


--
-- Name: rf_document_records rf_document_records_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_records
    ADD CONSTRAINT rf_document_records_pkey PRIMARY KEY (id);


--
-- Name: rf_document_records rf_document_records_signing_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_records
    ADD CONSTRAINT rf_document_records_signing_token_unique UNIQUE (signing_token);


--
-- Name: rf_document_signatures rf_document_signatures_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_signatures
    ADD CONSTRAINT rf_document_signatures_pkey PRIMARY KEY (id);


--
-- Name: rf_document_templates rf_document_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_templates
    ADD CONSTRAINT rf_document_templates_pkey PRIMARY KEY (id);


--
-- Name: rf_document_versions rf_document_versions_document_template_id_version_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_versions
    ADD CONSTRAINT rf_document_versions_document_template_id_version_number_unique UNIQUE (document_template_id, version_number);


--
-- Name: rf_document_versions rf_document_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_versions
    ADD CONSTRAINT rf_document_versions_pkey PRIMARY KEY (id);


--
-- Name: rf_excel_sheet_imports rf_excel_sheet_imports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheet_imports
    ADD CONSTRAINT rf_excel_sheet_imports_pkey PRIMARY KEY (id);


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
-- Name: rf_facility_availability_rules rf_facility_availability_rules_facility_id_day_of_week_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_availability_rules
    ADD CONSTRAINT rf_facility_availability_rules_facility_id_day_of_week_unique UNIQUE (facility_id, day_of_week);


--
-- Name: rf_facility_availability_rules rf_facility_availability_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_availability_rules
    ADD CONSTRAINT rf_facility_availability_rules_pkey PRIMARY KEY (id);


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
-- Name: rf_facility_waitlist rf_facility_waitlist_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_waitlist
    ADD CONSTRAINT rf_facility_waitlist_pkey PRIMARY KEY (id);


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
-- Name: rf_invoice_settings rf_invoice_settings_account_tenant_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_invoice_settings
    ADD CONSTRAINT rf_invoice_settings_account_tenant_unique UNIQUE (account_tenant_id);


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
-- Name: rf_leases rf_leases_quote_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_quote_id_unique UNIQUE (quote_id);


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
-- Name: rf_notification_preferences rf_notification_preferences_account_tenant_id_trigger_key_uniqu; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_notification_preferences
    ADD CONSTRAINT rf_notification_preferences_account_tenant_id_trigger_key_uniqu UNIQUE (account_tenant_id, trigger_key);


--
-- Name: rf_notification_preferences rf_notification_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_notification_preferences
    ADD CONSTRAINT rf_notification_preferences_pkey PRIMARY KEY (id);


--
-- Name: rf_owner_registrations rf_owner_registrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owner_registrations
    ADD CONSTRAINT rf_owner_registrations_pkey PRIMARY KEY (id);


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
-- Name: rf_receipts rf_receipts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_receipts
    ADD CONSTRAINT rf_receipts_pkey PRIMARY KEY (id);


--
-- Name: rf_receipts rf_receipts_transaction_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_receipts
    ADD CONSTRAINT rf_receipts_transaction_id_unique UNIQUE (transaction_id);


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
-- Name: rf_requests rf_requests_tenant_request_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_tenant_request_code_unique UNIQUE (account_tenant_id, request_code);


--
-- Name: rf_service_manager_types rf_service_manager_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_manager_types
    ADD CONSTRAINT rf_service_manager_types_pkey PRIMARY KEY (id);


--
-- Name: rf_service_request_messages rf_service_request_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_messages
    ADD CONSTRAINT rf_service_request_messages_pkey PRIMARY KEY (id);


--
-- Name: rf_service_request_timeline_events rf_service_request_timeline_events_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_timeline_events
    ADD CONSTRAINT rf_service_request_timeline_events_pkey PRIMARY KEY (id);


--
-- Name: rf_service_settings rf_service_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings
    ADD CONSTRAINT rf_service_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_service_settings rf_service_settings_tenant_category_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings
    ADD CONSTRAINT rf_service_settings_tenant_category_unique UNIQUE (account_tenant_id, category_id);


--
-- Name: rf_settings_audit_logs rf_settings_audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings_audit_logs
    ADD CONSTRAINT rf_settings_audit_logs_pkey PRIMARY KEY (id);


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
-- Name: rf_suggestions rf_suggestions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_suggestions
    ADD CONSTRAINT rf_suggestions_pkey PRIMARY KEY (id);


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
-- Name: rf_unit_ownerships rf_unit_ownerships_owner_id_unit_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships
    ADD CONSTRAINT rf_unit_ownerships_owner_id_unit_id_unique UNIQUE (owner_id, unit_id);


--
-- Name: rf_unit_ownerships rf_unit_ownerships_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships
    ADD CONSTRAINT rf_unit_ownerships_pkey PRIMARY KEY (id);


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
-- Name: rf_unit_status_history rf_unit_status_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_status_history
    ADD CONSTRAINT rf_unit_status_history_pkey PRIMARY KEY (id);


--
-- Name: rf_unit_types rf_unit_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_types
    ADD CONSTRAINT rf_unit_types_pkey PRIMARY KEY (id);


--
-- Name: rf_units rf_units_building_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_building_name_unique UNIQUE (rf_building_id, name);


--
-- Name: rf_units rf_units_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_pkey PRIMARY KEY (id);


--
-- Name: rf_visitor_access_settings rf_visitor_access_settings_community_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_access_settings
    ADD CONSTRAINT rf_visitor_access_settings_community_id_unique UNIQUE (community_id);


--
-- Name: rf_visitor_access_settings rf_visitor_access_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_access_settings
    ADD CONSTRAINT rf_visitor_access_settings_pkey PRIMARY KEY (id);


--
-- Name: rf_visitor_invitations rf_visitor_invitations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_invitations
    ADD CONSTRAINT rf_visitor_invitations_pkey PRIMARY KEY (id);


--
-- Name: rf_visitor_invitations rf_visitor_invitations_qr_code_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_invitations
    ADD CONSTRAINT rf_visitor_invitations_qr_code_token_unique UNIQUE (qr_code_token);


--
-- Name: rf_visitor_logs rf_visitor_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_logs
    ADD CONSTRAINT rf_visitor_logs_pkey PRIMARY KEY (id);


--
-- Name: rf_facility_waitlist rf_waitlist_position_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_waitlist
    ADD CONSTRAINT rf_waitlist_position_unique UNIQUE (facility_id, resident_id, requested_start_at);


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
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: roles roles_tenant_name_guard_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_tenant_name_guard_unique UNIQUE (account_tenant_id, name, guard_name);


--
-- Name: service_categories service_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_categories
    ADD CONSTRAINT service_categories_pkey PRIMARY KEY (id);


--
-- Name: service_category_communities service_category_communities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_category_communities
    ADD CONSTRAINT service_category_communities_pkey PRIMARY KEY (service_category_id, community_id);


--
-- Name: service_subcategories service_subcategories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_subcategories
    ADD CONSTRAINT service_subcategories_pkey PRIMARY KEY (id);


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
-- Name: feature_flag_audit_logs_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX feature_flag_audit_logs_account_tenant_id_index ON public.feature_flag_audit_logs USING btree (account_tenant_id);


--
-- Name: feature_flag_audit_logs_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX feature_flag_audit_logs_user_id_index ON public.feature_flag_audit_logs USING btree (user_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: lease_amendments_lease_id_amendment_number_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lease_amendments_lease_id_amendment_number_index ON public.lease_amendments USING btree (lease_id, amendment_number);


--
-- Name: lease_kyc_documents_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lease_kyc_documents_account_tenant_id_index ON public.lease_kyc_documents USING btree (account_tenant_id);


--
-- Name: lease_renewal_offers_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lease_renewal_offers_account_tenant_id_index ON public.lease_renewal_offers USING btree (account_tenant_id);


--
-- Name: lease_renewal_offers_lease_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lease_renewal_offers_lease_id_index ON public.lease_renewal_offers USING btree (lease_id);


--
-- Name: lease_renewal_offers_status_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX lease_renewal_offers_status_id_index ON public.lease_renewal_offers USING btree (status_id);


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
-- Name: model_has_roles_building_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_building_id_index ON public.model_has_roles USING btree (building_id);


--
-- Name: model_has_roles_community_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_community_id_index ON public.model_has_roles USING btree (community_id);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: model_has_roles_scope_unique; Type: INDEX; Schema: public; Owner: -
--

CREATE UNIQUE INDEX model_has_roles_scope_unique ON public.model_has_roles USING btree (role_id, model_id, model_type, COALESCE(community_id, (0)::bigint), COALESCE(building_id, (0)::bigint), COALESCE(service_type_id, (0)::bigint));


--
-- Name: model_has_roles_service_type_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_service_type_id_index ON public.model_has_roles USING btree (service_type_id);


--
-- Name: move_outs_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX move_outs_account_tenant_id_index ON public.move_outs USING btree (account_tenant_id);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: permissions_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX permissions_account_tenant_id_index ON public.permissions USING btree (account_tenant_id);


--
-- Name: permissions_subject_action_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX permissions_subject_action_index ON public.permissions USING btree (subject, action);


--
-- Name: report_snapshots_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX report_snapshots_account_tenant_id_index ON public.report_snapshots USING btree (account_tenant_id);


--
-- Name: report_snapshots_account_tenant_id_report_type_period_start_ind; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX report_snapshots_account_tenant_id_report_type_period_start_ind ON public.report_snapshots USING btree (account_tenant_id, report_type, period_start);


--
-- Name: report_snapshots_account_tenant_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX report_snapshots_account_tenant_id_status_index ON public.report_snapshots USING btree (account_tenant_id, status);


--
-- Name: report_snapshots_report_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX report_snapshots_report_type_index ON public.report_snapshots USING btree (report_type);


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
-- Name: rf_contact_activities_contact_type_contact_id_event_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_contact_activities_contact_type_contact_id_event_type_index ON public.rf_contact_activities USING btree (contact_type, contact_id, event_type);


--
-- Name: rf_contact_activities_contact_type_contact_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_contact_activities_contact_type_contact_id_index ON public.rf_contact_activities USING btree (contact_type, contact_id);


--
-- Name: rf_contact_documents_contact_type_contact_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_contact_documents_contact_type_contact_id_index ON public.rf_contact_documents USING btree (contact_type, contact_id);


--
-- Name: rf_contract_types_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_contract_types_account_tenant_id_index ON public.rf_contract_types USING btree (account_tenant_id);


--
-- Name: rf_dependents_dependable_type_dependable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_dependents_dependable_type_dependable_id_index ON public.rf_dependents USING btree (dependable_type, dependable_id);


--
-- Name: rf_document_records_source_type_source_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_document_records_source_type_source_id_index ON public.rf_document_records USING btree (source_type, source_id);


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
-- Name: rf_facility_bookings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_facility_bookings_account_tenant_id_index ON public.rf_facility_bookings USING btree (account_tenant_id);


--
-- Name: rf_facility_bookings_booker_type_booker_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_facility_bookings_booker_type_booker_id_index ON public.rf_facility_bookings USING btree (booker_type, booker_id);


--
-- Name: rf_facility_bookings_calendar_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_facility_bookings_calendar_idx ON public.rf_facility_bookings USING btree (facility_id, start_at, status_id);


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
-- Name: rf_leases_approved_by_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_leases_approved_by_id_index ON public.rf_leases USING btree (approved_by_id);


--
-- Name: rf_leases_quote_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_leases_quote_id_index ON public.rf_leases USING btree (quote_id);


--
-- Name: rf_leases_rejected_by_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_leases_rejected_by_id_index ON public.rf_leases USING btree (rejected_by_id);


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
-- Name: rf_owners_tenant_phone_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_owners_tenant_phone_index ON public.rf_owners USING btree (account_tenant_id, national_phone_number);


--
-- Name: rf_professionals_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_professionals_account_tenant_id_index ON public.rf_professionals USING btree (account_tenant_id);


--
-- Name: rf_receipts_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_receipts_account_tenant_id_index ON public.rf_receipts USING btree (account_tenant_id);


--
-- Name: rf_requests_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_account_tenant_id_index ON public.rf_requests USING btree (account_tenant_id);


--
-- Name: rf_requests_assigned_to_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_assigned_to_user_id_index ON public.rf_requests USING btree (assigned_to_user_id);


--
-- Name: rf_requests_requester_type_requester_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_requester_type_requester_id_index ON public.rf_requests USING btree (requester_type, requester_id);


--
-- Name: rf_requests_tenant_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_tenant_created_at_index ON public.rf_requests USING btree (account_tenant_id, created_at);


--
-- Name: rf_requests_tenant_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_requests_tenant_status_index ON public.rf_requests USING btree (account_tenant_id, status_id);


--
-- Name: rf_service_request_messages_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_messages_account_tenant_id_index ON public.rf_service_request_messages USING btree (account_tenant_id);


--
-- Name: rf_service_request_messages_sender_type_sender_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_messages_sender_type_sender_id_index ON public.rf_service_request_messages USING btree (sender_type, sender_id);


--
-- Name: rf_service_request_messages_service_request_id_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_messages_service_request_id_created_at_index ON public.rf_service_request_messages USING btree (service_request_id, created_at);


--
-- Name: rf_service_request_timeline_events_account_tenant_id_event_type; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_timeline_events_account_tenant_id_event_type ON public.rf_service_request_timeline_events USING btree (account_tenant_id, event_type, created_at);


--
-- Name: rf_service_request_timeline_events_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_timeline_events_account_tenant_id_index ON public.rf_service_request_timeline_events USING btree (account_tenant_id);


--
-- Name: rf_service_request_timeline_events_actor_type_actor_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_timeline_events_actor_type_actor_id_index ON public.rf_service_request_timeline_events USING btree (actor_type, actor_id);


--
-- Name: rf_service_request_timeline_events_service_request_id_created_a; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_request_timeline_events_service_request_id_created_a ON public.rf_service_request_timeline_events USING btree (service_request_id, created_at);


--
-- Name: rf_service_settings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_service_settings_account_tenant_id_index ON public.rf_service_settings USING btree (account_tenant_id);


--
-- Name: rf_settings_audit_logs_account_tenant_id_setting_group_setting_; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_settings_audit_logs_account_tenant_id_setting_group_setting_ ON public.rf_settings_audit_logs USING btree (account_tenant_id, setting_group, setting_key);


--
-- Name: rf_settings_audit_logs_setting_group_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_settings_audit_logs_setting_group_index ON public.rf_settings_audit_logs USING btree (setting_group);


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
-- Name: rf_tenants_tenant_phone_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_tenants_tenant_phone_index ON public.rf_tenants USING btree (account_tenant_id, national_phone_number);


--
-- Name: rf_transactions_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_account_tenant_id_index ON public.rf_transactions USING btree (account_tenant_id);


--
-- Name: rf_transactions_assignee_type_assignee_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_assignee_type_assignee_id_index ON public.rf_transactions USING btree (assignee_type, assignee_id);


--
-- Name: rf_transactions_direction_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_transactions_direction_index ON public.rf_transactions USING btree (direction);


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
-- Name: rf_visitor_access_settings_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_visitor_access_settings_account_tenant_id_index ON public.rf_visitor_access_settings USING btree (account_tenant_id);


--
-- Name: rf_visitor_invitations_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_visitor_invitations_account_tenant_id_index ON public.rf_visitor_invitations USING btree (account_tenant_id);


--
-- Name: rf_visitor_logs_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_visitor_logs_account_tenant_id_index ON public.rf_visitor_logs USING btree (account_tenant_id);


--
-- Name: rf_waitlist_fifo_idx; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX rf_waitlist_fifo_idx ON public.rf_facility_waitlist USING btree (facility_id, requested_start_at, created_at);


--
-- Name: roles_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX roles_account_tenant_id_index ON public.roles USING btree (account_tenant_id);


--
-- Name: service_categories_account_tenant_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_categories_account_tenant_id_index ON public.service_categories USING btree (account_tenant_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: users_invitation_token_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_invitation_token_index ON public.users USING btree (invitation_token);


--
-- Name: users_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_status_index ON public.users USING btree (status);


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
-- Name: invite_codes invite_codes_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes
    ADD CONSTRAINT invite_codes_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: invite_codes invite_codes_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes
    ADD CONSTRAINT invite_codes_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES public.tenants(id);


--
-- Name: invite_codes invite_codes_used_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.invite_codes
    ADD CONSTRAINT invite_codes_used_by_foreign FOREIGN KEY (used_by) REFERENCES public.users(id);


--
-- Name: lead_activities lead_activities_lead_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lead_activities
    ADD CONSTRAINT lead_activities_lead_id_foreign FOREIGN KEY (lead_id) REFERENCES public.rf_leads(id) ON DELETE CASCADE;


--
-- Name: lead_activities lead_activities_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lead_activities
    ADD CONSTRAINT lead_activities_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: lease_amendments lease_amendments_amended_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_amendments
    ADD CONSTRAINT lease_amendments_amended_by_foreign FOREIGN KEY (amended_by) REFERENCES public.users(id);


--
-- Name: lease_amendments lease_amendments_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_amendments
    ADD CONSTRAINT lease_amendments_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: lease_kyc_documents lease_kyc_documents_document_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_kyc_documents
    ADD CONSTRAINT lease_kyc_documents_document_type_id_foreign FOREIGN KEY (document_type_id) REFERENCES public.rf_settings(id) ON DELETE RESTRICT;


--
-- Name: lease_kyc_documents lease_kyc_documents_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_kyc_documents
    ADD CONSTRAINT lease_kyc_documents_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: lease_notices lease_notices_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices
    ADD CONSTRAINT lease_notices_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE SET NULL;


--
-- Name: lease_notices lease_notices_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices
    ADD CONSTRAINT lease_notices_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: lease_notices lease_notices_sent_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices
    ADD CONSTRAINT lease_notices_sent_by_foreign FOREIGN KEY (sent_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: lease_notices lease_notices_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_notices
    ADD CONSTRAINT lease_notices_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES public.rf_tenants(id) ON DELETE CASCADE;


--
-- Name: lease_quotes lease_quotes_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: lease_quotes lease_quotes_contact_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_contact_id_foreign FOREIGN KEY (contact_id) REFERENCES public.rf_tenants(id) ON DELETE CASCADE;


--
-- Name: lease_quotes lease_quotes_contract_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_contract_type_id_foreign FOREIGN KEY (contract_type_id) REFERENCES public.rf_contract_types(id) ON DELETE SET NULL;


--
-- Name: lease_quotes lease_quotes_created_by_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_created_by_id_foreign FOREIGN KEY (created_by_id) REFERENCES public.rf_admins(id) ON DELETE RESTRICT;


--
-- Name: lease_quotes lease_quotes_marketplace_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_marketplace_unit_id_foreign FOREIGN KEY (marketplace_unit_id) REFERENCES public.rf_marketplace_units(id) ON DELETE SET NULL;


--
-- Name: lease_quotes lease_quotes_parent_quote_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_parent_quote_id_foreign FOREIGN KEY (parent_quote_id) REFERENCES public.lease_quotes(id) ON DELETE SET NULL;


--
-- Name: lease_quotes lease_quotes_payment_frequency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_payment_frequency_id_foreign FOREIGN KEY (payment_frequency_id) REFERENCES public.rf_settings(id) ON DELETE RESTRICT;


--
-- Name: lease_quotes lease_quotes_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id) ON DELETE RESTRICT;


--
-- Name: lease_quotes lease_quotes_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_quotes
    ADD CONSTRAINT lease_quotes_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


--
-- Name: lease_renewal_offers lease_renewal_offers_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id);


--
-- Name: lease_renewal_offers lease_renewal_offers_contract_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_contract_type_id_foreign FOREIGN KEY (contract_type_id) REFERENCES public.rf_settings(id);


--
-- Name: lease_renewal_offers lease_renewal_offers_converted_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_converted_lease_id_foreign FOREIGN KEY (converted_lease_id) REFERENCES public.rf_leases(id);


--
-- Name: lease_renewal_offers lease_renewal_offers_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id);


--
-- Name: lease_renewal_offers lease_renewal_offers_decided_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_decided_by_foreign FOREIGN KEY (decided_by) REFERENCES public.users(id);


--
-- Name: lease_renewal_offers lease_renewal_offers_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: lease_renewal_offers lease_renewal_offers_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.lease_renewal_offers
    ADD CONSTRAINT lease_renewal_offers_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


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
-- Name: move_out_deductions move_out_deductions_move_out_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_deductions
    ADD CONSTRAINT move_out_deductions_move_out_id_foreign FOREIGN KEY (move_out_id) REFERENCES public.move_outs(id) ON DELETE CASCADE;


--
-- Name: move_out_rooms move_out_rooms_move_out_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_out_rooms
    ADD CONSTRAINT move_out_rooms_move_out_id_foreign FOREIGN KEY (move_out_id) REFERENCES public.move_outs(id) ON DELETE CASCADE;


--
-- Name: move_outs move_outs_initiated_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_outs
    ADD CONSTRAINT move_outs_initiated_by_foreign FOREIGN KEY (initiated_by) REFERENCES public.users(id);


--
-- Name: move_outs move_outs_lease_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_outs
    ADD CONSTRAINT move_outs_lease_id_foreign FOREIGN KEY (lease_id) REFERENCES public.rf_leases(id) ON DELETE CASCADE;


--
-- Name: move_outs move_outs_status_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.move_outs
    ADD CONSTRAINT move_outs_status_id_foreign FOREIGN KEY (status_id) REFERENCES public.rf_statuses(id);


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
-- Name: report_snapshots report_snapshots_requested_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.report_snapshots
    ADD CONSTRAINT report_snapshots_requested_by_user_id_foreign FOREIGN KEY (requested_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_bank_accounts rf_bank_accounts_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_bank_accounts
    ADD CONSTRAINT rf_bank_accounts_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_bank_accounts rf_bank_accounts_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_bank_accounts
    ADD CONSTRAINT rf_bank_accounts_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE SET NULL;


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
-- Name: rf_complaints rf_complaints_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_complaints
    ADD CONSTRAINT rf_complaints_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_complaints rf_complaints_assigned_to_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_complaints
    ADD CONSTRAINT rf_complaints_assigned_to_foreign FOREIGN KEY (assigned_to) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_contact_activities rf_contact_activities_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_activities
    ADD CONSTRAINT rf_contact_activities_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_contact_documents rf_contact_documents_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_contact_documents
    ADD CONSTRAINT rf_contact_documents_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_directory_entries rf_directory_entries_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_directory_entries
    ADD CONSTRAINT rf_directory_entries_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_directory_entries rf_directory_entries_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_directory_entries
    ADD CONSTRAINT rf_directory_entries_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_document_records rf_document_records_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_records
    ADD CONSTRAINT rf_document_records_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_document_records rf_document_records_document_template_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_records
    ADD CONSTRAINT rf_document_records_document_template_version_id_foreign FOREIGN KEY (document_template_version_id) REFERENCES public.rf_document_versions(id) ON DELETE CASCADE;


--
-- Name: rf_document_signatures rf_document_signatures_document_record_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_signatures
    ADD CONSTRAINT rf_document_signatures_document_record_id_foreign FOREIGN KEY (document_record_id) REFERENCES public.rf_document_records(id) ON DELETE CASCADE;


--
-- Name: rf_document_templates rf_document_templates_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_templates
    ADD CONSTRAINT rf_document_templates_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_document_templates rf_document_templates_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_templates
    ADD CONSTRAINT rf_document_templates_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_document_versions rf_document_versions_created_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_versions
    ADD CONSTRAINT rf_document_versions_created_by_foreign FOREIGN KEY (created_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_document_versions rf_document_versions_document_template_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_document_versions
    ADD CONSTRAINT rf_document_versions_document_template_id_foreign FOREIGN KEY (document_template_id) REFERENCES public.rf_document_templates(id) ON DELETE CASCADE;


--
-- Name: rf_excel_sheet_imports rf_excel_sheet_imports_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheet_imports
    ADD CONSTRAINT rf_excel_sheet_imports_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_excel_sheet_imports rf_excel_sheet_imports_excel_sheet_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheet_imports
    ADD CONSTRAINT rf_excel_sheet_imports_excel_sheet_id_foreign FOREIGN KEY (excel_sheet_id) REFERENCES public.rf_excel_sheets(id) ON DELETE CASCADE;


--
-- Name: rf_excel_sheet_imports rf_excel_sheet_imports_imported_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_excel_sheet_imports
    ADD CONSTRAINT rf_excel_sheet_imports_imported_by_foreign FOREIGN KEY (imported_by) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_facility_availability_rules rf_facility_availability_rules_facility_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_availability_rules
    ADD CONSTRAINT rf_facility_availability_rules_facility_id_foreign FOREIGN KEY (facility_id) REFERENCES public.rf_facilities(id) ON DELETE CASCADE;


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
-- Name: rf_facility_waitlist rf_facility_waitlist_facility_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_waitlist
    ADD CONSTRAINT rf_facility_waitlist_facility_id_foreign FOREIGN KEY (facility_id) REFERENCES public.rf_facilities(id) ON DELETE CASCADE;


--
-- Name: rf_facility_waitlist rf_facility_waitlist_resident_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_facility_waitlist
    ADD CONSTRAINT rf_facility_waitlist_resident_id_foreign FOREIGN KEY (resident_id) REFERENCES public.rf_tenants(id) ON DELETE CASCADE;


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
-- Name: rf_leads rf_leads_assigned_to_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leads
    ADD CONSTRAINT rf_leads_assigned_to_user_id_foreign FOREIGN KEY (assigned_to_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_leases rf_leases_approved_by_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_approved_by_id_foreign FOREIGN KEY (approved_by_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_leases rf_leases_rejected_by_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_leases
    ADD CONSTRAINT rf_leases_rejected_by_id_foreign FOREIGN KEY (rejected_by_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_notification_preferences rf_notification_preferences_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_notification_preferences
    ADD CONSTRAINT rf_notification_preferences_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_owner_registrations rf_owner_registrations_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owner_registrations
    ADD CONSTRAINT rf_owner_registrations_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_owner_registrations rf_owner_registrations_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_owner_registrations
    ADD CONSTRAINT rf_owner_registrations_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_payments rf_payments_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_payments
    ADD CONSTRAINT rf_payments_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.rf_transactions(id) ON DELETE CASCADE;


--
-- Name: rf_receipts rf_receipts_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_receipts
    ADD CONSTRAINT rf_receipts_transaction_id_foreign FOREIGN KEY (transaction_id) REFERENCES public.rf_transactions(id) ON DELETE CASCADE;


--
-- Name: rf_request_subcategories rf_request_subcategories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_request_subcategories
    ADD CONSTRAINT rf_request_subcategories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_request_categories(id) ON DELETE CASCADE;


--
-- Name: rf_requests rf_requests_assigned_to_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_assigned_to_user_id_foreign FOREIGN KEY (assigned_to_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_requests rf_requests_service_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_service_category_id_foreign FOREIGN KEY (service_category_id) REFERENCES public.service_categories(id) ON DELETE SET NULL;


--
-- Name: rf_requests rf_requests_service_subcategory_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_requests
    ADD CONSTRAINT rf_requests_service_subcategory_id_foreign FOREIGN KEY (service_subcategory_id) REFERENCES public.service_subcategories(id) ON DELETE SET NULL;


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
-- Name: rf_service_request_messages rf_service_request_messages_service_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_messages
    ADD CONSTRAINT rf_service_request_messages_service_request_id_foreign FOREIGN KEY (service_request_id) REFERENCES public.rf_requests(id) ON DELETE CASCADE;


--
-- Name: rf_service_request_timeline_events rf_service_request_timeline_events_service_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_request_timeline_events
    ADD CONSTRAINT rf_service_request_timeline_events_service_request_id_foreign FOREIGN KEY (service_request_id) REFERENCES public.rf_requests(id) ON DELETE CASCADE;


--
-- Name: rf_service_settings rf_service_settings_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_service_settings
    ADD CONSTRAINT rf_service_settings_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.rf_request_categories(id) ON DELETE CASCADE;


--
-- Name: rf_settings_audit_logs rf_settings_audit_logs_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings_audit_logs
    ADD CONSTRAINT rf_settings_audit_logs_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_settings_audit_logs rf_settings_audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings_audit_logs
    ADD CONSTRAINT rf_settings_audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_settings rf_settings_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_settings
    ADD CONSTRAINT rf_settings_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.rf_settings(id) ON DELETE SET NULL;


--
-- Name: rf_suggestions rf_suggestions_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_suggestions
    ADD CONSTRAINT rf_suggestions_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_suggestions rf_suggestions_reviewed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_suggestions
    ADD CONSTRAINT rf_suggestions_reviewed_by_foreign FOREIGN KEY (reviewed_by) REFERENCES public.users(id) ON DELETE SET NULL;


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
-- Name: rf_unit_ownerships rf_unit_ownerships_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships
    ADD CONSTRAINT rf_unit_ownerships_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_unit_ownerships rf_unit_ownerships_owner_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships
    ADD CONSTRAINT rf_unit_ownerships_owner_id_foreign FOREIGN KEY (owner_id) REFERENCES public.rf_owners(id) ON DELETE CASCADE;


--
-- Name: rf_unit_ownerships rf_unit_ownerships_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_ownerships
    ADD CONSTRAINT rf_unit_ownerships_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


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
-- Name: rf_unit_status_history rf_unit_status_history_account_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_status_history
    ADD CONSTRAINT rf_unit_status_history_account_tenant_id_foreign FOREIGN KEY (account_tenant_id) REFERENCES public.tenants(id) ON DELETE CASCADE;


--
-- Name: rf_unit_status_history rf_unit_status_history_changed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_status_history
    ADD CONSTRAINT rf_unit_status_history_changed_by_foreign FOREIGN KEY (changed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: rf_unit_status_history rf_unit_status_history_unit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_unit_status_history
    ADD CONSTRAINT rf_unit_status_history_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES public.rf_units(id) ON DELETE CASCADE;


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
-- Name: rf_units rf_units_currency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_units
    ADD CONSTRAINT rf_units_currency_id_foreign FOREIGN KEY (currency_id) REFERENCES public.currencies(id) ON DELETE SET NULL;


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
-- Name: rf_visitor_access_settings rf_visitor_access_settings_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_access_settings
    ADD CONSTRAINT rf_visitor_access_settings_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id);


--
-- Name: rf_visitor_invitations rf_visitor_invitations_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_invitations
    ADD CONSTRAINT rf_visitor_invitations_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id);


--
-- Name: rf_visitor_invitations rf_visitor_invitations_resident_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_invitations
    ADD CONSTRAINT rf_visitor_invitations_resident_id_foreign FOREIGN KEY (resident_id) REFERENCES public.users(id);


--
-- Name: rf_visitor_logs rf_visitor_logs_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_logs
    ADD CONSTRAINT rf_visitor_logs_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id);


--
-- Name: rf_visitor_logs rf_visitor_logs_gate_officer_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_logs
    ADD CONSTRAINT rf_visitor_logs_gate_officer_id_foreign FOREIGN KEY (gate_officer_id) REFERENCES public.users(id);


--
-- Name: rf_visitor_logs rf_visitor_logs_invitation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rf_visitor_logs
    ADD CONSTRAINT rf_visitor_logs_invitation_id_foreign FOREIGN KEY (invitation_id) REFERENCES public.rf_visitor_invitations(id);


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
-- Name: service_categories service_categories_default_assignee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_categories
    ADD CONSTRAINT service_categories_default_assignee_id_foreign FOREIGN KEY (default_assignee_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: service_category_communities service_category_communities_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_category_communities
    ADD CONSTRAINT service_category_communities_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.rf_communities(id) ON DELETE CASCADE;


--
-- Name: service_category_communities service_category_communities_service_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_category_communities
    ADD CONSTRAINT service_category_communities_service_category_id_foreign FOREIGN KEY (service_category_id) REFERENCES public.service_categories(id) ON DELETE CASCADE;


--
-- Name: service_subcategories service_subcategories_service_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_subcategories
    ADD CONSTRAINT service_subcategories_service_category_id_foreign FOREIGN KEY (service_category_id) REFERENCES public.service_categories(id) ON DELETE CASCADE;


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

\unrestrict X6WGykNtlyBV74PbG1UWCssiJis5MEgTP26nXCrgZGf9sufCLcVODcyIohcCuYb

--
-- PostgreSQL database dump
--

\restrict 33EJU7E7s0D0OAztJY6FZX7sREnwmytuPQMLWuvdchZC19fe7fI2w4JwYsIY48z

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
83	2026_04_23_092710_extend_model_has_roles_table	2
84	2026_04_23_092710_extend_roles_table	2
85	2026_04_23_092711_extend_permissions_table	2
86	2026_04_23_101408_add_bilingual_and_tenant_columns_to_permissions_table	2
87	2026_04_23_174440_fix_model_has_roles_primary_key_for_scope_rows	2
88	2026_04_24_150403_add_bilingual_name_and_id_type_to_rf_tenants_table	2
89	2026_04_24_150406_add_bilingual_name_and_id_type_to_rf_owners_table	2
90	2026_04_24_150415_add_bilingual_name_and_id_type_to_rf_professionals_table	2
91	2026_04_24_150416_add_bilingual_name_to_rf_dependents_table	2
92	2026_04_24_153714_add_metadata_to_rf_communities_table	2
93	2026_04_24_212814_add_account_tenant_id_to_rf_service_settings_table	2
94	2026_04_24_212814_add_settings_columns_to_rf_invoice_settings_table	2
95	2026_04_24_212817_create_rf_contract_types_table	2
96	2026_04_24_212818_create_rf_app_settings_table	2
97	2026_04_25_020010_relax_phone_unique_indexes_on_contacts_tables	2
98	2026_04_25_121258_add_profile_columns_to_users_table	2
99	2026_04_25_122535_create_lease_quotes_table	2
100	2026_04_25_122652_add_is_active_is_default_to_rf_settings_table	2
101	2026_04_25_124555_create_rf_document_templates_table	2
102	2026_04_25_124555_create_rf_document_versions_table	2
103	2026_04_25_124556_create_rf_document_records_table	2
104	2026_04_25_124557_create_rf_document_signatures_table	2
105	2026_04_25_130000_add_service_request_columns_to_rf_requests_table	2
106	2026_04_25_130001_create_rf_service_request_messages_table	2
107	2026_04_25_130002_create_rf_service_request_timeline_events_table	2
108	2026_04_25_200000_add_public_token_to_lease_quotes_table	2
109	2026_04_25_204129_create_rf_visitor_invitations_table	2
110	2026_04_25_204130_create_rf_visitor_logs_table	2
111	2026_04_25_204131_create_rf_visitor_access_settings_table	2
112	2026_04_25_204141_add_schema_extensions_to_rf_facilities_table	2
113	2026_04_25_204142_create_rf_facility_availability_rules_table	2
114	2026_04_25_204143_add_schema_extensions_to_rf_facility_bookings_table	2
115	2026_04_25_204144_create_rf_facility_waitlist_table	2
116	2026_04_25_204221_create_report_snapshots_table	2
117	2026_04_25_210831_add_money_in_fields_to_rf_transactions	2
118	2026_04_25_210833_create_rf_receipts_table	2
119	2026_04_25_212438_make_community_id_nullable_on_rf_visitor_invitations_table	2
120	2026_04_25_220000_change_qr_code_token_to_char32_on_rf_visitor_invitations_table	2
121	2026_04_25_230000_add_quote_id_and_kyc_columns_to_rf_leases_table	2
122	2026_04_25_230001_create_lease_kyc_documents_table	2
123	2026_04_25_230002_add_metadata_sort_order_to_rf_settings_table	2
124	2026_04_26_022821_add_revision_columns_to_lease_quotes_table	2
125	2026_04_26_022836_create_service_categories_table	2
126	2026_04_26_022841_create_service_subcategories_table	2
127	2026_04_26_022846_create_service_category_communities_table	2
128	2026_04_26_022850_add_service_category_id_to_rf_requests_table	2
129	2026_04_26_023316_add_signing_columns_to_rf_document_records_table	2
130	2026_04_26_063935_add_columns_to_rf_excel_sheets_table	2
131	2026_04_26_063935_create_rf_excel_sheet_imports_table	2
132	2026_04_26_064638_add_urgency_sla_columns_to_rf_requests_table	2
133	2026_04_26_070515_add_unique_constraint_to_quote_id_on_rf_leases_table	2
134	2026_04_26_074600_add_approval_columns_to_rf_leases_table	2
135	2026_04_26_074734_add_assigned_to_user_id_to_rf_requests_table	2
136	2026_04_26_075607_create_rf_owner_registrations_table	2
137	2026_04_26_081322_add_gap_columns_to_rf_announcements_table	2
138	2026_04_26_081323_create_rf_complaints_table	2
139	2026_04_26_081326_create_rf_suggestions_table	2
140	2026_04_26_081328_create_rf_directory_entries_table	2
141	2026_04_26_083144_create_rf_unit_status_history_table	2
142	2026_04_26_083151_add_status_column_to_rf_units_table	2
143	2026_04_26_084022_add_sort_order_and_is_primary_to_media_table	2
144	2026_04_26_091510_create_rf_notification_preferences_table	2
145	2026_04_26_091510_create_rf_settings_audit_logs_table	2
146	2026_04_26_095621_create_rf_contact_documents_table	2
147	2026_04_26_095621_create_rf_unit_ownerships_table	2
148	2026_04_26_095622_add_soft_deletes_to_contacts_tables	2
149	2026_04_26_095622_create_rf_contact_activities_table	2
150	2026_04_26_104342_add_sla_rating_to_rf_requests_table	2
151	2026_04_26_104622_add_source_complaint_id_to_rf_requests_table	2
152	2026_04_26_112653_add_checkin_columns_to_rf_facility_bookings_table	2
153	2026_04_26_114543_add_reconciled_to_rf_transactions_table	2
154	2026_04_26_114543_create_rf_bank_accounts_table	2
155	2026_04_26_115723_make_transaction_fks_nullable	2
156	2026_04_27_100000_create_feature_flag_overrides_table	2
157	2026_04_27_100001_create_feature_flag_audit_logs_table	2
158	2026_04_27_102457_create_invite_codes_table	2
159	2026_04_27_102558_mark_existing_users_as_verified	2
160	2026_04_27_105837_add_user_status_and_invitation_columns_to_users_table	2
161	2026_04_28_100855_add_pricing_columns_to_rf_units_table	2
162	2026_04_28_100948_extend_excel_sheets_for_unit_imports	2
163	2026_04_28_101013_add_unique_index_to_rf_units_for_import	2
164	2026_04_29_064820_create_lease_amendments_table	3
165	2026_04_29_064823_add_current_amendment_number_to_rf_leases_table	3
166	2026_04_29_084144_add_lease_alert_thresholds_to_rf_app_settings_table	4
167	2026_04_30_083942_enhance_rf_leads_table	5
168	2026_04_29_070524_create_lease_notices_table	6
169	2026_04_29_084029_create_lease_renewal_offers_table	6
170	2026_04_29_084118_create_move_outs_table	6
171	2026_04_29_084121_create_move_out_rooms_table	6
172	2026_04_29_084126_create_move_out_deductions_table	6
173	2026_04_29_084128_add_move_out_status_to_rf_statuses_table	6
174	2026_04_29_140359_add_nullable_condition_to_move_out_rooms_table	6
175	2026_04_29_212530_fix_move_out_status_id_collision	6
176	2026_04_29_222500_add_renewal_statuses_to_rf_statuses_table	6
177	2026_04_30_091735_create_lead_activities_table	6
178	2026_04_30_091854_add_assignment_and_lost_reason_to_rf_leads_table	6
179	2026_04_30_095145_add_excel_import_lead_source	6
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 179, true);


--
-- PostgreSQL database dump complete
--

\unrestrict 33EJU7E7s0D0OAztJY6FZX7sREnwmytuPQMLWuvdchZC19fe7fI2w4JwYsIY48z

