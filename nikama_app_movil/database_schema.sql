--
-- PostgreSQL database dump
--

\restrict kzIgOgOHcA3DlbqSG1XpI4eCGbxTmOJB1cH50ME7TJ2VhYQzFdnhvH5IsWLp8Pi

-- Dumped from database version 16.13
-- Dumped by pg_dump version 16.13

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
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
-- Name: activity_logs; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.activity_logs (
    id uuid NOT NULL,
    user_id uuid,
    action character varying(255) NOT NULL,
    entity_type character varying(255),
    entity_id uuid,
    old_values json,
    new_values json,
    ip_address inet,
    user_agent text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.activity_logs OWNER TO nikama;

--
-- Name: business_categories; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_categories (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    category_id uuid NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_categories OWNER TO nikama;

--
-- Name: business_commissions; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_commissions (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    commission_type character varying(20) DEFAULT 'percentage'::character varying NOT NULL,
    commission_value numeric(10,2) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    starts_at timestamp(0) without time zone,
    ends_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_commissions OWNER TO nikama;

--
-- Name: business_holiday_exceptions; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_holiday_exceptions (
    id uuid NOT NULL,
    business_location_id uuid NOT NULL,
    exception_date date NOT NULL,
    is_closed boolean DEFAULT true NOT NULL,
    open_time time(0) without time zone,
    close_time time(0) without time zone,
    reason character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_holiday_exceptions OWNER TO nikama;

--
-- Name: business_location_hours; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_location_hours (
    id uuid NOT NULL,
    business_location_id uuid NOT NULL,
    day_of_week character varying(20) NOT NULL,
    opening_time time(0) without time zone,
    closing_time time(0) without time zone,
    is_24_hours boolean DEFAULT false NOT NULL,
    is_closed boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_location_hours OWNER TO nikama;

--
-- Name: business_locations; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_locations (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    name character varying(100) NOT NULL,
    address character varying(255) NOT NULL,
    reference character varying(255),
    province character varying(100) NOT NULL,
    district character varying(100) NOT NULL,
    department character varying(100) NOT NULL,
    country character varying(100) DEFAULT 'Peru'::character varying NOT NULL,
    postal_code character varying(20),
    latitude numeric(10,7),
    longitude numeric(10,7),
    location_phone character varying(20),
    delivery_radius_km numeric(5,2) DEFAULT '5'::numeric NOT NULL,
    delivery_fee numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    estimated_delivery_time_minutes smallint DEFAULT '30'::smallint NOT NULL,
    minimum_delivery_amount numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    is_main boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.business_locations OWNER TO nikama;

--
-- Name: business_payouts; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_payouts (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    amount numeric(10,2) NOT NULL,
    commission_deducted numeric(10,2) NOT NULL,
    net_amount numeric(10,2) NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    transaction_reference character varying(255),
    notes text,
    processed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_payouts OWNER TO nikama;

--
-- Name: business_reviews; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_reviews (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    user_id uuid NOT NULL,
    rating smallint NOT NULL,
    comment text,
    is_visible boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.business_reviews OWNER TO nikama;

--
-- Name: business_users; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.business_users (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    user_id uuid NOT NULL,
    role character varying(30) DEFAULT 'staff'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    joined_at timestamp(0) without time zone,
    last_access_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.business_users OWNER TO nikama;

--
-- Name: businesses; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.businesses (
    id uuid NOT NULL,
    business_name character varying(150) NOT NULL,
    slug character varying(180) NOT NULL,
    legal_name character varying(150),
    ruc character varying(20),
    description text,
    logo_url character varying(255),
    banner_url character varying(255),
    contact_email character varying(255),
    contact_phone character varying(20),
    whatsapp_number character varying(20),
    rating_average numeric(3,2) DEFAULT '0'::numeric NOT NULL,
    total_reviews integer DEFAULT 0 NOT NULL,
    total_orders bigint DEFAULT '0'::bigint NOT NULL,
    minimum_order_amount numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    estimated_preparation_time_minutes integer DEFAULT 15 NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    rejected_reason text,
    accepts_orders boolean DEFAULT true NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    offers_delivery boolean DEFAULT true NOT NULL,
    offers_pickup boolean DEFAULT true NOT NULL,
    is_featured boolean DEFAULT false NOT NULL,
    facebook_url character varying(255),
    instagram_url character varying(255),
    website_url character varying(255),
    verified_at timestamp(0) without time zone,
    approved_at timestamp(0) without time zone,
    suspended_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.businesses OWNER TO nikama;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache OWNER TO nikama;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO nikama;

--
-- Name: cart_item_options; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.cart_item_options (
    id uuid NOT NULL,
    cart_item_id uuid NOT NULL,
    product_option_id uuid NOT NULL,
    additional_price numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cart_item_options OWNER TO nikama;

--
-- Name: cart_items; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.cart_items (
    id uuid NOT NULL,
    cart_id uuid NOT NULL,
    business_id uuid NOT NULL,
    product_id uuid,
    product_combo_id uuid,
    quantity integer DEFAULT 1 NOT NULL,
    notes text,
    unit_price numeric(8,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cart_items OWNER TO nikama;

--
-- Name: carts; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.carts (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    status character varying(30) DEFAULT 'active'::character varying NOT NULL,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.carts OWNER TO nikama;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.categories (
    id uuid NOT NULL,
    parent_id uuid,
    name character varying(100) NOT NULL,
    slug character varying(120) NOT NULL,
    description text,
    icon character varying(255),
    image_url character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.categories OWNER TO nikama;

--
-- Name: customer_addresses; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.customer_addresses (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    label character varying(50) NOT NULL,
    address character varying(255) NOT NULL,
    address_type character varying(30),
    reference character varying(255),
    delivery_notes text,
    contact_name character varying(255),
    contact_phone character varying(20),
    province character varying(100),
    district character varying(100),
    department character varying(100),
    country character varying(100) DEFAULT 'Peru'::character varying NOT NULL,
    postal_code character varying(20),
    latitude numeric(10,7),
    longitude numeric(10,7),
    is_default boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.customer_addresses OWNER TO nikama;

--
-- Name: customer_profiles; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.customer_profiles (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    birth_date date,
    gender character varying(20),
    accept_marketing_emails boolean DEFAULT true NOT NULL,
    accept_notifications boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.customer_profiles OWNER TO nikama;

--
-- Name: deliveries; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.deliveries (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    driver_profile_id uuid,
    business_id uuid NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    assigned_at timestamp(0) without time zone,
    picked_up_at timestamp(0) without time zone,
    delivered_at timestamp(0) without time zone,
    failed_at timestamp(0) without time zone,
    delivery_fee numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.deliveries OWNER TO nikama;

--
-- Name: delivery_zones; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.delivery_zones (
    id uuid NOT NULL,
    business_location_id uuid NOT NULL,
    name character varying(255) NOT NULL,
    polygon_coordinates jsonb,
    delivery_fee numeric(8,2) NOT NULL,
    minimum_order_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.delivery_zones OWNER TO nikama;

--
-- Name: discount_usages; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.discount_usages (
    id uuid NOT NULL,
    discount_id uuid NOT NULL,
    user_id uuid NOT NULL,
    order_id uuid NOT NULL,
    discount_applied numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.discount_usages OWNER TO nikama;

--
-- Name: discounts; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.discounts (
    id uuid NOT NULL,
    business_id uuid,
    created_by_user_id uuid,
    code character varying(50),
    name character varying(255) NOT NULL,
    description text,
    type character varying(20) NOT NULL,
    discount_type character varying(20) NOT NULL,
    discount_value numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    applies_to character varying(20) DEFAULT 'order'::character varying NOT NULL,
    rules json,
    minimum_order_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    maximum_discount_amount numeric(10,2),
    usage_limit integer,
    used_count integer DEFAULT 0 NOT NULL,
    usage_limit_per_user integer,
    starts_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.discounts OWNER TO nikama;

--
-- Name: driver_assignments; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_assignments (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    delivery_id uuid NOT NULL,
    status character varying(30) DEFAULT 'assigned'::character varying NOT NULL,
    assigned_at timestamp(0) without time zone,
    accepted_at timestamp(0) without time zone,
    completed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.driver_assignments OWNER TO nikama;

--
-- Name: driver_documents; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_documents (
    id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    document_type character varying(30) NOT NULL,
    document_url character varying(255) NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    rejected_reason text,
    expires_at date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.driver_documents OWNER TO nikama;

--
-- Name: driver_live_locations; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_live_locations (
    id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    latitude numeric(10,7),
    longitude numeric(10,7),
    is_online boolean DEFAULT false NOT NULL,
    is_available boolean DEFAULT false NOT NULL,
    last_location_updated_at timestamp(0) without time zone,
    last_online_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.driver_live_locations OWNER TO nikama;

--
-- Name: driver_location_logs; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_location_logs (
    id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    latitude numeric(10,7) NOT NULL,
    longitude numeric(10,7) NOT NULL,
    speed_kmh numeric(6,2),
    accuracy_meters numeric(6,2),
    recorded_at timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.driver_location_logs OWNER TO nikama;

--
-- Name: driver_payouts; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_payouts (
    id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    amount numeric(10,2) NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    transaction_reference character varying(255),
    notes text,
    processed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.driver_payouts OWNER TO nikama;

--
-- Name: driver_profiles; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_profiles (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    vehicle_type character varying(30) NOT NULL,
    license_number character varying(20),
    vehicle_brand character varying(100),
    vehicle_model character varying(100),
    vehicle_color character varying(50),
    license_plate character varying(20),
    emergency_contact_name character varying(100),
    emergency_contact_phone character varying(20),
    accepts_cash_payments boolean DEFAULT true NOT NULL,
    rating_average numeric(4,2) DEFAULT '0'::numeric NOT NULL,
    total_deliveries integer DEFAULT 0 NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    rejected_reason text,
    verified_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.driver_profiles OWNER TO nikama;

--
-- Name: driver_reviews; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.driver_reviews (
    id uuid NOT NULL,
    driver_profile_id uuid NOT NULL,
    user_id uuid NOT NULL,
    order_id uuid NOT NULL,
    rating smallint NOT NULL,
    comment text,
    is_visible boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.driver_reviews OWNER TO nikama;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: nikama
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


ALTER TABLE public.failed_jobs OWNER TO nikama;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: nikama
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO nikama;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nikama
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: favorites; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.favorites (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    favoritable_type character varying(255) NOT NULL,
    favoritable_id uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.favorites OWNER TO nikama;

--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: nikama
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


ALTER TABLE public.job_batches OWNER TO nikama;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: nikama
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


ALTER TABLE public.jobs OWNER TO nikama;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: nikama
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO nikama;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nikama
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO nikama;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: nikama
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO nikama;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nikama
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: notifications; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.notifications (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    notifiable_type character varying(255) NOT NULL,
    notifiable_id uuid NOT NULL,
    data text NOT NULL,
    read_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.notifications OWNER TO nikama;

--
-- Name: order_cancellations; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_cancellations (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    cancelled_by_type character varying(20) NOT NULL,
    cancelled_by_id uuid,
    reason_code character varying(50) NOT NULL,
    comment text,
    penalty_applied boolean DEFAULT false NOT NULL,
    penalty_amount numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_cancellations OWNER TO nikama;

--
-- Name: order_issues; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_issues (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    user_id uuid NOT NULL,
    issue_type character varying(30) NOT NULL,
    description text NOT NULL,
    status character varying(20) DEFAULT 'open'::character varying NOT NULL,
    resolution_action character varying(30),
    refund_amount numeric(10,2),
    assigned_admin_id uuid,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_issues OWNER TO nikama;

--
-- Name: order_item_options; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_item_options (
    id uuid NOT NULL,
    order_item_id uuid NOT NULL,
    product_option_id uuid,
    option_group_name character varying(255) NOT NULL,
    option_name character varying(255) NOT NULL,
    additional_price numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_item_options OWNER TO nikama;

--
-- Name: order_items; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_items (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    business_id uuid NOT NULL,
    product_id uuid,
    product_combo_id uuid,
    product_name character varying(255) NOT NULL,
    unit_price numeric(8,2) NOT NULL,
    quantity integer NOT NULL,
    product_description text,
    product_image_url character varying(255),
    subtotal numeric(10,2) NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_items OWNER TO nikama;

--
-- Name: order_refunds; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_refunds (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    payment_id uuid NOT NULL,
    amount numeric(10,2) NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    reason text NOT NULL,
    gateway_refund_id character varying(255),
    processed_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_refunds OWNER TO nikama;

--
-- Name: order_status_history; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.order_status_history (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    status character varying(30) NOT NULL,
    description text,
    changed_by_user_id uuid,
    metadata json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_status_history OWNER TO nikama;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.orders (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    customer_address_id uuid,
    order_number character varying(30) NOT NULL,
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    payment_status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    subtotal numeric(10,2) NOT NULL,
    delivery_fee numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    total numeric(10,2) NOT NULL,
    delivery_address character varying(255),
    delivery_reference character varying(255),
    delivery_latitude numeric(10,7),
    delivery_longitude numeric(10,7),
    confirmed_at timestamp(0) without time zone,
    delivered_at timestamp(0) without time zone,
    cancelled_at timestamp(0) without time zone,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.orders OWNER TO nikama;

--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO nikama;

--
-- Name: payments; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.payments (
    id uuid NOT NULL,
    order_id uuid NOT NULL,
    payment_method character varying(30) NOT NULL,
    provider character varying(50),
    status character varying(30) DEFAULT 'pending'::character varying NOT NULL,
    amount numeric(10,2) NOT NULL,
    transaction_id character varying(255),
    failed_at timestamp(0) without time zone,
    refunded_at timestamp(0) without time zone,
    provider_response json,
    paid_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payments OWNER TO nikama;

--
-- Name: payout_methods; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.payout_methods (
    id uuid NOT NULL,
    owner_type character varying(255) NOT NULL,
    owner_id uuid NOT NULL,
    type character varying(20) NOT NULL,
    provider_name character varying(255) NOT NULL,
    account_number character varying(255),
    cci_number character varying(255),
    holder_name character varying(255) NOT NULL,
    holder_dni character varying(255) NOT NULL,
    is_default boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.payout_methods OWNER TO nikama;

--
-- Name: product_categories; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_categories (
    id uuid NOT NULL,
    product_id uuid NOT NULL,
    category_id uuid NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_categories OWNER TO nikama;

--
-- Name: product_combo_items; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_combo_items (
    id uuid NOT NULL,
    product_combo_id uuid NOT NULL,
    product_id uuid NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_combo_items OWNER TO nikama;

--
-- Name: product_combos; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_combos (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    price numeric(8,2) NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_combos OWNER TO nikama;

--
-- Name: product_images; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_images (
    id uuid NOT NULL,
    product_id uuid NOT NULL,
    image_url character varying(255) NOT NULL,
    alt_text character varying(255),
    is_primary boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_images OWNER TO nikama;

--
-- Name: product_option_groups; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_option_groups (
    id uuid NOT NULL,
    product_id uuid NOT NULL,
    name character varying(255) NOT NULL,
    selection_type character varying(20) DEFAULT 'single'::character varying NOT NULL,
    is_required boolean DEFAULT false NOT NULL,
    min_selections integer DEFAULT 0 NOT NULL,
    max_selections integer DEFAULT 1 NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_option_groups OWNER TO nikama;

--
-- Name: product_options; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_options (
    id uuid NOT NULL,
    product_option_group_id uuid NOT NULL,
    name character varying(255) NOT NULL,
    additional_price numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    is_available boolean DEFAULT true NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.product_options OWNER TO nikama;

--
-- Name: product_reviews; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.product_reviews (
    id uuid NOT NULL,
    product_id uuid NOT NULL,
    user_id uuid NOT NULL,
    order_id uuid,
    rating smallint NOT NULL,
    comment text,
    is_visible boolean DEFAULT true NOT NULL,
    is_verified_purchase boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.product_reviews OWNER TO nikama;

--
-- Name: products; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.products (
    id uuid NOT NULL,
    business_id uuid NOT NULL,
    name character varying(150) NOT NULL,
    slug character varying(180) NOT NULL,
    description text,
    price numeric(8,2) NOT NULL,
    compare_price numeric(8,2),
    sku character varying(100),
    stock_quantity integer DEFAULT 0 NOT NULL,
    track_stock boolean DEFAULT true NOT NULL,
    allow_backorder boolean DEFAULT false NOT NULL,
    status character varying(30) DEFAULT 'draft'::character varying NOT NULL,
    is_featured boolean DEFAULT false NOT NULL,
    requires_preparation boolean DEFAULT true NOT NULL,
    preparation_time_minutes integer,
    weight_grams numeric(8,2),
    main_image_url character varying(255),
    rating_average numeric(3,2) DEFAULT '0'::numeric NOT NULL,
    total_reviews integer DEFAULT 0 NOT NULL,
    total_sales bigint DEFAULT '0'::bigint NOT NULL,
    views_count integer DEFAULT 0 NOT NULL,
    is_available boolean DEFAULT true NOT NULL,
    published_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.products OWNER TO nikama;

--
-- Name: promotional_banners; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.promotional_banners (
    id uuid NOT NULL,
    title character varying(255) NOT NULL,
    image_url character varying(255) NOT NULL,
    action_type character varying(30) NOT NULL,
    action_id uuid,
    action_url character varying(255),
    sort_order integer DEFAULT 0 NOT NULL,
    starts_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.promotional_banners OWNER TO nikama;

--
-- Name: role_user; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.role_user (
    user_id uuid NOT NULL,
    role_id uuid NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.role_user OWNER TO nikama;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.roles (
    id uuid NOT NULL,
    name character varying(50) NOT NULL,
    slug character varying(50) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO nikama;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id uuid,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO nikama;

--
-- Name: system_settings; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.system_settings (
    id uuid NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.system_settings OWNER TO nikama;

--
-- Name: user_devices; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.user_devices (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    device_type character varying(20) NOT NULL,
    fcm_token text NOT NULL,
    device_name character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    last_used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_devices OWNER TO nikama;

--
-- Name: user_providers; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.user_providers (
    id uuid NOT NULL,
    user_id uuid NOT NULL,
    provider character varying(30) NOT NULL,
    provider_id character varying(255) NOT NULL,
    provider_email character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.user_providers OWNER TO nikama;

--
-- Name: users; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    email character varying(255) NOT NULL,
    phone character varying(20),
    dni character varying(10),
    avatar_url character varying(255),
    email_verified_at timestamp(0) without time zone,
    phone_verified_at timestamp(0) without time zone,
    password character varying(255),
    remember_token character varying(100),
    is_active boolean DEFAULT true NOT NULL,
    blocked_at timestamp(0) without time zone,
    last_login_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO nikama;

--
-- Name: wallet_transactions; Type: TABLE; Schema: public; Owner: nikama
--

CREATE TABLE public.wallet_transactions (
    id uuid NOT NULL,
    holder_type character varying(255) NOT NULL,
    holder_id uuid NOT NULL,
    amount numeric(10,2) NOT NULL,
    type character varying(20) NOT NULL,
    transaction_type character varying(30) NOT NULL,
    reference_id uuid,
    description text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.wallet_transactions OWNER TO nikama;

--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: activity_logs activity_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_pkey PRIMARY KEY (id);


--
-- Name: business_holiday_exceptions biz_loc_holiday_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_holiday_exceptions
    ADD CONSTRAINT biz_loc_holiday_unique UNIQUE (business_location_id, exception_date);


--
-- Name: business_categories business_categories_business_id_category_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_categories
    ADD CONSTRAINT business_categories_business_id_category_id_unique UNIQUE (business_id, category_id);


--
-- Name: business_categories business_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_categories
    ADD CONSTRAINT business_categories_pkey PRIMARY KEY (id);


--
-- Name: business_commissions business_commissions_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_commissions
    ADD CONSTRAINT business_commissions_pkey PRIMARY KEY (id);


--
-- Name: business_holiday_exceptions business_holiday_exceptions_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_holiday_exceptions
    ADD CONSTRAINT business_holiday_exceptions_pkey PRIMARY KEY (id);


--
-- Name: business_location_hours business_location_hours_business_location_id_day_of_week_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_location_hours
    ADD CONSTRAINT business_location_hours_business_location_id_day_of_week_unique UNIQUE (business_location_id, day_of_week);


--
-- Name: business_location_hours business_location_hours_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_location_hours
    ADD CONSTRAINT business_location_hours_pkey PRIMARY KEY (id);


--
-- Name: business_locations business_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_locations
    ADD CONSTRAINT business_locations_pkey PRIMARY KEY (id);


--
-- Name: business_payouts business_payouts_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_payouts
    ADD CONSTRAINT business_payouts_pkey PRIMARY KEY (id);


--
-- Name: business_reviews business_reviews_business_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_reviews
    ADD CONSTRAINT business_reviews_business_id_user_id_unique UNIQUE (business_id, user_id);


--
-- Name: business_reviews business_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_reviews
    ADD CONSTRAINT business_reviews_pkey PRIMARY KEY (id);


--
-- Name: business_users business_users_business_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_users
    ADD CONSTRAINT business_users_business_id_user_id_unique UNIQUE (business_id, user_id);


--
-- Name: business_users business_users_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_users
    ADD CONSTRAINT business_users_pkey PRIMARY KEY (id);


--
-- Name: businesses businesses_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.businesses
    ADD CONSTRAINT businesses_pkey PRIMARY KEY (id);


--
-- Name: businesses businesses_ruc_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.businesses
    ADD CONSTRAINT businesses_ruc_unique UNIQUE (ruc);


--
-- Name: businesses businesses_slug_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.businesses
    ADD CONSTRAINT businesses_slug_unique UNIQUE (slug);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cart_item_options cart_item_options_cart_item_id_product_option_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_item_options
    ADD CONSTRAINT cart_item_options_cart_item_id_product_option_id_unique UNIQUE (cart_item_id, product_option_id);


--
-- Name: cart_item_options cart_item_options_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_item_options
    ADD CONSTRAINT cart_item_options_pkey PRIMARY KEY (id);


--
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_unique UNIQUE (slug);


--
-- Name: customer_addresses customer_addresses_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.customer_addresses
    ADD CONSTRAINT customer_addresses_pkey PRIMARY KEY (id);


--
-- Name: customer_profiles customer_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.customer_profiles
    ADD CONSTRAINT customer_profiles_pkey PRIMARY KEY (id);


--
-- Name: customer_profiles customer_profiles_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.customer_profiles
    ADD CONSTRAINT customer_profiles_user_id_unique UNIQUE (user_id);


--
-- Name: deliveries deliveries_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.deliveries
    ADD CONSTRAINT deliveries_pkey PRIMARY KEY (id);


--
-- Name: delivery_zones delivery_zones_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.delivery_zones
    ADD CONSTRAINT delivery_zones_pkey PRIMARY KEY (id);


--
-- Name: discount_usages discount_usages_order_id_discount_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discount_usages
    ADD CONSTRAINT discount_usages_order_id_discount_id_unique UNIQUE (order_id, discount_id);


--
-- Name: discount_usages discount_usages_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discount_usages
    ADD CONSTRAINT discount_usages_pkey PRIMARY KEY (id);


--
-- Name: discounts discounts_code_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_code_unique UNIQUE (code);


--
-- Name: discounts discounts_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_pkey PRIMARY KEY (id);


--
-- Name: driver_assignments driver_assignments_order_id_driver_profile_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_assignments
    ADD CONSTRAINT driver_assignments_order_id_driver_profile_id_unique UNIQUE (order_id, driver_profile_id);


--
-- Name: driver_assignments driver_assignments_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_assignments
    ADD CONSTRAINT driver_assignments_pkey PRIMARY KEY (id);


--
-- Name: driver_documents driver_documents_driver_profile_id_document_type_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_documents
    ADD CONSTRAINT driver_documents_driver_profile_id_document_type_unique UNIQUE (driver_profile_id, document_type);


--
-- Name: driver_documents driver_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_documents
    ADD CONSTRAINT driver_documents_pkey PRIMARY KEY (id);


--
-- Name: driver_live_locations driver_live_locations_driver_profile_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_live_locations
    ADD CONSTRAINT driver_live_locations_driver_profile_id_unique UNIQUE (driver_profile_id);


--
-- Name: driver_live_locations driver_live_locations_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_live_locations
    ADD CONSTRAINT driver_live_locations_pkey PRIMARY KEY (id);


--
-- Name: driver_location_logs driver_location_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_location_logs
    ADD CONSTRAINT driver_location_logs_pkey PRIMARY KEY (id);


--
-- Name: driver_payouts driver_payouts_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_payouts
    ADD CONSTRAINT driver_payouts_pkey PRIMARY KEY (id);


--
-- Name: driver_profiles driver_profiles_license_plate_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_profiles
    ADD CONSTRAINT driver_profiles_license_plate_unique UNIQUE (license_plate);


--
-- Name: driver_profiles driver_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_profiles
    ADD CONSTRAINT driver_profiles_pkey PRIMARY KEY (id);


--
-- Name: driver_profiles driver_profiles_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_profiles
    ADD CONSTRAINT driver_profiles_user_id_unique UNIQUE (user_id);


--
-- Name: driver_reviews driver_reviews_order_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_reviews
    ADD CONSTRAINT driver_reviews_order_id_unique UNIQUE (order_id);


--
-- Name: driver_reviews driver_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_reviews
    ADD CONSTRAINT driver_reviews_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: favorites favorites_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_pkey PRIMARY KEY (id);


--
-- Name: favorites favorites_user_id_favoritable_type_favoritable_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_user_id_favoritable_type_favoritable_id_unique UNIQUE (user_id, favoritable_type, favoritable_id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: notifications notifications_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.notifications
    ADD CONSTRAINT notifications_pkey PRIMARY KEY (id);


--
-- Name: order_cancellations order_cancellations_order_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_cancellations
    ADD CONSTRAINT order_cancellations_order_id_unique UNIQUE (order_id);


--
-- Name: order_cancellations order_cancellations_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_cancellations
    ADD CONSTRAINT order_cancellations_pkey PRIMARY KEY (id);


--
-- Name: order_issues order_issues_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_issues
    ADD CONSTRAINT order_issues_pkey PRIMARY KEY (id);


--
-- Name: order_item_options order_item_options_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_item_options
    ADD CONSTRAINT order_item_options_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: order_refunds order_refunds_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_refunds
    ADD CONSTRAINT order_refunds_pkey PRIMARY KEY (id);


--
-- Name: order_status_history order_status_history_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_pkey PRIMARY KEY (id);


--
-- Name: orders orders_order_number_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_order_number_unique UNIQUE (order_number);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- Name: payout_methods payout_methods_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.payout_methods
    ADD CONSTRAINT payout_methods_pkey PRIMARY KEY (id);


--
-- Name: product_categories product_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_pkey PRIMARY KEY (id);


--
-- Name: product_categories product_categories_product_id_category_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_product_id_category_id_unique UNIQUE (product_id, category_id);


--
-- Name: product_combo_items product_combo_items_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combo_items
    ADD CONSTRAINT product_combo_items_pkey PRIMARY KEY (id);


--
-- Name: product_combo_items product_combo_items_product_combo_id_product_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combo_items
    ADD CONSTRAINT product_combo_items_product_combo_id_product_id_unique UNIQUE (product_combo_id, product_id);


--
-- Name: product_combos product_combos_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combos
    ADD CONSTRAINT product_combos_pkey PRIMARY KEY (id);


--
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- Name: product_option_groups product_option_groups_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_option_groups
    ADD CONSTRAINT product_option_groups_pkey PRIMARY KEY (id);


--
-- Name: product_options product_options_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_options
    ADD CONSTRAINT product_options_pkey PRIMARY KEY (id);


--
-- Name: product_reviews product_reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_pkey PRIMARY KEY (id);


--
-- Name: product_reviews product_reviews_product_id_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_id_user_id_unique UNIQUE (product_id, user_id);


--
-- Name: products products_business_id_slug_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_business_id_slug_unique UNIQUE (business_id, slug);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: promotional_banners promotional_banners_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.promotional_banners
    ADD CONSTRAINT promotional_banners_pkey PRIMARY KEY (id);


--
-- Name: role_user role_user_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_pkey PRIMARY KEY (user_id, role_id);


--
-- Name: roles roles_name_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_unique UNIQUE (name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: roles roles_slug_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_slug_unique UNIQUE (slug);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: system_settings system_settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.system_settings
    ADD CONSTRAINT system_settings_key_unique UNIQUE (key);


--
-- Name: system_settings system_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.system_settings
    ADD CONSTRAINT system_settings_pkey PRIMARY KEY (id);


--
-- Name: products unique_business_sku; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT unique_business_sku UNIQUE (business_id, sku);


--
-- Name: user_devices user_devices_fcm_token_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_devices
    ADD CONSTRAINT user_devices_fcm_token_unique UNIQUE (fcm_token);


--
-- Name: user_devices user_devices_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_devices
    ADD CONSTRAINT user_devices_pkey PRIMARY KEY (id);


--
-- Name: user_providers user_providers_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_providers
    ADD CONSTRAINT user_providers_pkey PRIMARY KEY (id);


--
-- Name: user_providers user_providers_provider_provider_id_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_providers
    ADD CONSTRAINT user_providers_provider_provider_id_unique UNIQUE (provider, provider_id);


--
-- Name: user_providers user_providers_user_id_provider_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_providers
    ADD CONSTRAINT user_providers_user_id_provider_unique UNIQUE (user_id, provider);


--
-- Name: users users_dni_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_dni_unique UNIQUE (dni);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: wallet_transactions wallet_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.wallet_transactions
    ADD CONSTRAINT wallet_transactions_pkey PRIMARY KEY (id);


--
-- Name: activity_logs_entity_type_entity_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX activity_logs_entity_type_entity_id_index ON public.activity_logs USING btree (entity_type, entity_id);


--
-- Name: business_categories_business_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_categories_business_id_is_active_index ON public.business_categories USING btree (business_id, is_active);


--
-- Name: business_commissions_business_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_commissions_business_id_is_active_index ON public.business_commissions USING btree (business_id, is_active);


--
-- Name: business_locations_department_province_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_locations_department_province_index ON public.business_locations USING btree (department, province);


--
-- Name: business_locations_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_locations_is_active_index ON public.business_locations USING btree (is_active);


--
-- Name: business_locations_is_main_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_locations_is_main_index ON public.business_locations USING btree (is_main);


--
-- Name: business_locations_latitude_longitude_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_locations_latitude_longitude_index ON public.business_locations USING btree (latitude, longitude);


--
-- Name: business_locations_main_unique; Type: INDEX; Schema: public; Owner: nikama
--

CREATE UNIQUE INDEX business_locations_main_unique ON public.business_locations USING btree (business_id) WHERE ((is_main = true) AND (deleted_at IS NULL));


--
-- Name: business_locations_province_district_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_locations_province_district_index ON public.business_locations USING btree (province, district);


--
-- Name: business_payouts_business_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_payouts_business_id_status_index ON public.business_payouts USING btree (business_id, status);


--
-- Name: business_reviews_business_id_is_visible_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_reviews_business_id_is_visible_index ON public.business_reviews USING btree (business_id, is_visible);


--
-- Name: business_users_business_id_role_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_users_business_id_role_index ON public.business_users USING btree (business_id, role);


--
-- Name: business_users_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_users_is_active_index ON public.business_users USING btree (is_active);


--
-- Name: business_users_role_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX business_users_role_index ON public.business_users USING btree (role);


--
-- Name: businesses_is_featured_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX businesses_is_featured_index ON public.businesses USING btree (is_featured);


--
-- Name: businesses_status_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX businesses_status_is_active_index ON public.businesses USING btree (status, is_active);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: cart_items_cart_id_business_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX cart_items_cart_id_business_id_index ON public.cart_items USING btree (cart_id, business_id);


--
-- Name: categories_parent_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX categories_parent_id_is_active_index ON public.categories USING btree (parent_id, is_active);


--
-- Name: customer_addresses_default_unique; Type: INDEX; Schema: public; Owner: nikama
--

CREATE UNIQUE INDEX customer_addresses_default_unique ON public.customer_addresses USING btree (user_id) WHERE ((is_default = true) AND (deleted_at IS NULL));


--
-- Name: customer_addresses_department_province_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX customer_addresses_department_province_index ON public.customer_addresses USING btree (department, province);


--
-- Name: customer_addresses_latitude_longitude_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX customer_addresses_latitude_longitude_index ON public.customer_addresses USING btree (latitude, longitude);


--
-- Name: customer_addresses_province_district_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX customer_addresses_province_district_index ON public.customer_addresses USING btree (province, district);


--
-- Name: customer_addresses_user_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX customer_addresses_user_id_is_active_index ON public.customer_addresses USING btree (user_id, is_active);


--
-- Name: customer_addresses_user_id_is_default_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX customer_addresses_user_id_is_default_index ON public.customer_addresses USING btree (user_id, is_default);


--
-- Name: deliveries_driver_profile_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX deliveries_driver_profile_id_status_index ON public.deliveries USING btree (driver_profile_id, status);


--
-- Name: deliveries_order_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX deliveries_order_id_status_index ON public.deliveries USING btree (order_id, status);


--
-- Name: delivery_zones_business_location_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX delivery_zones_business_location_id_is_active_index ON public.delivery_zones USING btree (business_location_id, is_active);


--
-- Name: discount_usages_discount_id_user_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX discount_usages_discount_id_user_id_index ON public.discount_usages USING btree (discount_id, user_id);


--
-- Name: discount_usages_order_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX discount_usages_order_id_index ON public.discount_usages USING btree (order_id);


--
-- Name: discounts_business_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX discounts_business_id_is_active_index ON public.discounts USING btree (business_id, is_active);


--
-- Name: driver_documents_driver_profile_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_documents_driver_profile_id_status_index ON public.driver_documents USING btree (driver_profile_id, status);


--
-- Name: driver_live_locations_is_online_is_available_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_live_locations_is_online_is_available_index ON public.driver_live_locations USING btree (is_online, is_available);


--
-- Name: driver_location_logs_driver_profile_id_recorded_at_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_location_logs_driver_profile_id_recorded_at_index ON public.driver_location_logs USING btree (driver_profile_id, recorded_at);


--
-- Name: driver_location_logs_recorded_at_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_location_logs_recorded_at_index ON public.driver_location_logs USING btree (recorded_at);


--
-- Name: driver_payouts_driver_profile_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_payouts_driver_profile_id_status_index ON public.driver_payouts USING btree (driver_profile_id, status);


--
-- Name: driver_profiles_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_profiles_status_index ON public.driver_profiles USING btree (status);


--
-- Name: driver_reviews_driver_profile_id_is_visible_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX driver_reviews_driver_profile_id_is_visible_index ON public.driver_reviews USING btree (driver_profile_id, is_visible);


--
-- Name: favorites_favoritable_type_favoritable_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX favorites_favoritable_type_favoritable_id_index ON public.favorites USING btree (favoritable_type, favoritable_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX notifications_notifiable_type_notifiable_id_index ON public.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: order_issues_order_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_issues_order_id_index ON public.order_issues USING btree (order_id);


--
-- Name: order_issues_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_issues_status_index ON public.order_issues USING btree (status);


--
-- Name: order_item_options_order_item_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_item_options_order_item_id_index ON public.order_item_options USING btree (order_item_id);


--
-- Name: order_items_order_id_business_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_items_order_id_business_id_index ON public.order_items USING btree (order_id, business_id);


--
-- Name: order_refunds_order_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_refunds_order_id_status_index ON public.order_refunds USING btree (order_id, status);


--
-- Name: order_status_history_order_id_created_at_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX order_status_history_order_id_created_at_index ON public.order_status_history USING btree (order_id, created_at);


--
-- Name: orders_user_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX orders_user_id_status_index ON public.orders USING btree (user_id, status);


--
-- Name: payments_order_id_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX payments_order_id_status_index ON public.payments USING btree (order_id, status);


--
-- Name: payout_methods_owner_type_owner_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX payout_methods_owner_type_owner_id_index ON public.payout_methods USING btree (owner_type, owner_id);


--
-- Name: payout_methods_owner_type_owner_id_is_default_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX payout_methods_owner_type_owner_id_is_default_index ON public.payout_methods USING btree (owner_type, owner_id, is_default);


--
-- Name: product_categories_category_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_categories_category_id_index ON public.product_categories USING btree (category_id);


--
-- Name: product_combo_items_product_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_combo_items_product_id_index ON public.product_combo_items USING btree (product_id);


--
-- Name: product_combos_business_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_combos_business_id_is_active_index ON public.product_combos USING btree (business_id, is_active);


--
-- Name: product_images_product_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_images_product_id_index ON public.product_images USING btree (product_id);


--
-- Name: product_option_groups_product_id_sort_order_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_option_groups_product_id_sort_order_index ON public.product_option_groups USING btree (product_id, sort_order);


--
-- Name: product_options_product_option_group_id_is_available_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_options_product_option_group_id_is_available_index ON public.product_options USING btree (product_option_group_id, is_available);


--
-- Name: product_reviews_product_id_is_visible_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX product_reviews_product_id_is_visible_index ON public.product_reviews USING btree (product_id, is_visible);


--
-- Name: products_is_featured_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX products_is_featured_index ON public.products USING btree (is_featured);


--
-- Name: products_published_at_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX products_published_at_index ON public.products USING btree (published_at);


--
-- Name: products_status_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX products_status_index ON public.products USING btree (status);


--
-- Name: promotional_banners_is_active_sort_order_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX promotional_banners_is_active_sort_order_index ON public.promotional_banners USING btree (is_active, sort_order);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: user_devices_user_id_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX user_devices_user_id_is_active_index ON public.user_devices USING btree (user_id, is_active);


--
-- Name: users_is_active_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX users_is_active_index ON public.users USING btree (is_active);


--
-- Name: wallet_transactions_holder_type_holder_id_index; Type: INDEX; Schema: public; Owner: nikama
--

CREATE INDEX wallet_transactions_holder_type_holder_id_index ON public.wallet_transactions USING btree (holder_type, holder_id);


--
-- Name: activity_logs activity_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.activity_logs
    ADD CONSTRAINT activity_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: business_categories business_categories_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_categories
    ADD CONSTRAINT business_categories_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_categories business_categories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_categories
    ADD CONSTRAINT business_categories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: business_commissions business_commissions_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_commissions
    ADD CONSTRAINT business_commissions_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_holiday_exceptions business_holiday_exceptions_business_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_holiday_exceptions
    ADD CONSTRAINT business_holiday_exceptions_business_location_id_foreign FOREIGN KEY (business_location_id) REFERENCES public.business_locations(id) ON DELETE CASCADE;


--
-- Name: business_location_hours business_location_hours_business_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_location_hours
    ADD CONSTRAINT business_location_hours_business_location_id_foreign FOREIGN KEY (business_location_id) REFERENCES public.business_locations(id) ON DELETE CASCADE;


--
-- Name: business_locations business_locations_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_locations
    ADD CONSTRAINT business_locations_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_payouts business_payouts_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_payouts
    ADD CONSTRAINT business_payouts_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_reviews business_reviews_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_reviews
    ADD CONSTRAINT business_reviews_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_reviews business_reviews_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_reviews
    ADD CONSTRAINT business_reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: business_users business_users_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_users
    ADD CONSTRAINT business_users_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: business_users business_users_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.business_users
    ADD CONSTRAINT business_users_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: cart_item_options cart_item_options_cart_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_item_options
    ADD CONSTRAINT cart_item_options_cart_item_id_foreign FOREIGN KEY (cart_item_id) REFERENCES public.cart_items(id) ON DELETE CASCADE;


--
-- Name: cart_item_options cart_item_options_product_option_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_item_options
    ADD CONSTRAINT cart_item_options_product_option_id_foreign FOREIGN KEY (product_option_id) REFERENCES public.product_options(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_cart_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_foreign FOREIGN KEY (cart_id) REFERENCES public.carts(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_product_combo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_combo_id_foreign FOREIGN KEY (product_combo_id) REFERENCES public.product_combos(id) ON DELETE SET NULL;


--
-- Name: cart_items cart_items_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE SET NULL;


--
-- Name: carts carts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: categories categories_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: customer_addresses customer_addresses_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.customer_addresses
    ADD CONSTRAINT customer_addresses_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: customer_profiles customer_profiles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.customer_profiles
    ADD CONSTRAINT customer_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: deliveries deliveries_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.deliveries
    ADD CONSTRAINT deliveries_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: deliveries deliveries_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.deliveries
    ADD CONSTRAINT deliveries_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE SET NULL;


--
-- Name: deliveries deliveries_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.deliveries
    ADD CONSTRAINT deliveries_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: delivery_zones delivery_zones_business_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.delivery_zones
    ADD CONSTRAINT delivery_zones_business_location_id_foreign FOREIGN KEY (business_location_id) REFERENCES public.business_locations(id) ON DELETE CASCADE;


--
-- Name: discount_usages discount_usages_discount_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discount_usages
    ADD CONSTRAINT discount_usages_discount_id_foreign FOREIGN KEY (discount_id) REFERENCES public.discounts(id) ON DELETE CASCADE;


--
-- Name: discount_usages discount_usages_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discount_usages
    ADD CONSTRAINT discount_usages_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: discount_usages discount_usages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discount_usages
    ADD CONSTRAINT discount_usages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: discounts discounts_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: discounts discounts_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.discounts
    ADD CONSTRAINT discounts_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: driver_assignments driver_assignments_delivery_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_assignments
    ADD CONSTRAINT driver_assignments_delivery_id_foreign FOREIGN KEY (delivery_id) REFERENCES public.deliveries(id) ON DELETE CASCADE;


--
-- Name: driver_assignments driver_assignments_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_assignments
    ADD CONSTRAINT driver_assignments_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_assignments driver_assignments_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_assignments
    ADD CONSTRAINT driver_assignments_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: driver_documents driver_documents_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_documents
    ADD CONSTRAINT driver_documents_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_live_locations driver_live_locations_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_live_locations
    ADD CONSTRAINT driver_live_locations_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_location_logs driver_location_logs_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_location_logs
    ADD CONSTRAINT driver_location_logs_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_payouts driver_payouts_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_payouts
    ADD CONSTRAINT driver_payouts_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_profiles driver_profiles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_profiles
    ADD CONSTRAINT driver_profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: driver_reviews driver_reviews_driver_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_reviews
    ADD CONSTRAINT driver_reviews_driver_profile_id_foreign FOREIGN KEY (driver_profile_id) REFERENCES public.driver_profiles(id) ON DELETE CASCADE;


--
-- Name: driver_reviews driver_reviews_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_reviews
    ADD CONSTRAINT driver_reviews_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: driver_reviews driver_reviews_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.driver_reviews
    ADD CONSTRAINT driver_reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: favorites favorites_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.favorites
    ADD CONSTRAINT favorites_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: order_cancellations order_cancellations_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_cancellations
    ADD CONSTRAINT order_cancellations_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_issues order_issues_assigned_admin_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_issues
    ADD CONSTRAINT order_issues_assigned_admin_id_foreign FOREIGN KEY (assigned_admin_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: order_issues order_issues_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_issues
    ADD CONSTRAINT order_issues_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_issues order_issues_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_issues
    ADD CONSTRAINT order_issues_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: order_item_options order_item_options_order_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_item_options
    ADD CONSTRAINT order_item_options_order_item_id_foreign FOREIGN KEY (order_item_id) REFERENCES public.order_items(id) ON DELETE CASCADE;


--
-- Name: order_item_options order_item_options_product_option_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_item_options
    ADD CONSTRAINT order_item_options_product_option_id_foreign FOREIGN KEY (product_option_id) REFERENCES public.product_options(id) ON DELETE SET NULL;


--
-- Name: order_items order_items_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_product_combo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_combo_id_foreign FOREIGN KEY (product_combo_id) REFERENCES public.product_combos(id) ON DELETE SET NULL;


--
-- Name: order_items order_items_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE SET NULL;


--
-- Name: order_refunds order_refunds_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_refunds
    ADD CONSTRAINT order_refunds_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_refunds order_refunds_payment_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_refunds
    ADD CONSTRAINT order_refunds_payment_id_foreign FOREIGN KEY (payment_id) REFERENCES public.payments(id) ON DELETE CASCADE;


--
-- Name: order_status_history order_status_history_changed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_changed_by_user_id_foreign FOREIGN KEY (changed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: order_status_history order_status_history_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.order_status_history
    ADD CONSTRAINT order_status_history_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: orders orders_customer_address_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_customer_address_id_foreign FOREIGN KEY (customer_address_id) REFERENCES public.customer_addresses(id) ON DELETE SET NULL;


--
-- Name: orders orders_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: payments payments_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: product_categories product_categories_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE CASCADE;


--
-- Name: product_categories product_categories_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_categories
    ADD CONSTRAINT product_categories_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_combo_items product_combo_items_product_combo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combo_items
    ADD CONSTRAINT product_combo_items_product_combo_id_foreign FOREIGN KEY (product_combo_id) REFERENCES public.product_combos(id) ON DELETE CASCADE;


--
-- Name: product_combo_items product_combo_items_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combo_items
    ADD CONSTRAINT product_combo_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_combos product_combos_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_combos
    ADD CONSTRAINT product_combos_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: product_images product_images_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_option_groups product_option_groups_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_option_groups
    ADD CONSTRAINT product_option_groups_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_options product_options_product_option_group_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_options
    ADD CONSTRAINT product_options_product_option_group_id_foreign FOREIGN KEY (product_option_group_id) REFERENCES public.product_option_groups(id) ON DELETE CASCADE;


--
-- Name: product_reviews product_reviews_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_order_id_foreign FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE SET NULL;


--
-- Name: product_reviews product_reviews_product_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_product_id_foreign FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_reviews product_reviews_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.product_reviews
    ADD CONSTRAINT product_reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: products products_business_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_business_id_foreign FOREIGN KEY (business_id) REFERENCES public.businesses(id) ON DELETE CASCADE;


--
-- Name: role_user role_user_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: role_user role_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: sessions sessions_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: user_devices user_devices_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_devices
    ADD CONSTRAINT user_devices_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_providers user_providers_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: nikama
--

ALTER TABLE ONLY public.user_providers
    ADD CONSTRAINT user_providers_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict kzIgOgOHcA3DlbqSG1XpI4eCGbxTmOJB1cH50ME7TJ2VhYQzFdnhvH5IsWLp8Pi

