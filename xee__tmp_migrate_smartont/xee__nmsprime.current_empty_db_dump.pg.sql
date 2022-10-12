--
-- PostgreSQL database dump
--

-- Dumped from database version 13.10
-- Dumped by pg_dump version 13.10

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

ALTER DATABASE nmsprime OWNER TO nmsprime;
\connect nmsprime
--
-- Name: nmsprime; Type: SCHEMA; Schema: -; Owner: nmsprime
--

CREATE SCHEMA nmsprime;


ALTER SCHEMA nmsprime OWNER TO nmsprime;

--
-- Name: billingbase_userlang; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.billingbase_userlang AS ENUM (
    'de',
    'en'
);


ALTER TYPE nmsprime.billingbase_userlang OWNER TO nmsprime;

--
-- Name: configfile_device; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.configfile_device AS ENUM (
    'cm',
    'mta',
    'tr069',
    'ont'
);


ALTER TYPE nmsprime.configfile_device OWNER TO nmsprime;

--
-- Name: configfile_public; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.configfile_public AS ENUM (
    'yes',
    'no'
);


ALTER TYPE nmsprime.configfile_public OWNER TO nmsprime;

--
-- Name: domain_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.domain_type AS ENUM (
    'SIP',
    'Email',
    'DHCP'
);


ALTER TYPE nmsprime.domain_type OWNER TO nmsprime;

--
-- Name: enviaorderdocument_document_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.enviaorderdocument_document_type AS ENUM (
    'Auftrag',
    'Insolvenz',
    'Kommunikation',
    'NachweisUnternehmer',
    'Portierungsformblatt',
    'Telefonbucheintrag',
    'Vertrag',
    'Vertragsbeend',
    'Vollmacht'
);


ALTER TYPE nmsprime.enviaorderdocument_document_type OWNER TO nmsprime;

--
-- Name: invoice_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.invoice_type AS ENUM (
    'Invoice',
    'CDR'
);


ALTER TYPE nmsprime.invoice_type OWNER TO nmsprime;

--
-- Name: mta_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.mta_type AS ENUM (
    'sip',
    'packetcable'
);


ALTER TYPE nmsprime.mta_type OWNER TO nmsprime;

--
-- Name: numberrange_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.numberrange_type AS ENUM (
    'contract',
    'invoice'
);


ALTER TYPE nmsprime.numberrange_type OWNER TO nmsprime;

--
-- Name: oid_html_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.oid_html_type AS ENUM (
    'text',
    'select',
    'groupbox',
    'textarea'
);


ALTER TYPE nmsprime.oid_html_type OWNER TO nmsprime;

--
-- Name: oid_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.oid_type AS ENUM (
    'i',
    'u',
    's',
    'x',
    'd',
    'n',
    'o',
    't',
    'a',
    'b'
);


ALTER TYPE nmsprime.oid_type OWNER TO nmsprime;

--
-- Name: phonetariff_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.phonetariff_type AS ENUM (
    'purchase',
    'sale',
    'basic',
    'landlineflat',
    'allnetflat'
);


ALTER TYPE nmsprime.phonetariff_type OWNER TO nmsprime;

--
-- Name: phonetariff_voip_protocol; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.phonetariff_voip_protocol AS ENUM (
    'MGCP',
    'SIP'
);


ALTER TYPE nmsprime.phonetariff_voip_protocol OWNER TO nmsprime;

--
-- Name: product_billing_cycle; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.product_billing_cycle AS ENUM (
    'Once',
    'Monthly',
    'Quarterly',
    'Yearly'
);


ALTER TYPE nmsprime.product_billing_cycle OWNER TO nmsprime;

--
-- Name: product_type; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.product_type AS ENUM (
    'Internet',
    'TV',
    'Voip',
    'Device',
    'Credit',
    'Other',
    'Postal'
);


ALTER TYPE nmsprime.product_type OWNER TO nmsprime;

--
-- Name: sepamandate_state; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.sepamandate_state AS ENUM (
    'FRST',
    'RCUR',
    'OOFF',
    'FNAL'
);


ALTER TYPE nmsprime.sepamandate_state OWNER TO nmsprime;

--
-- Name: ticket_priority; Type: TYPE; Schema: nmsprime; Owner: nmsprime
--

CREATE TYPE nmsprime.ticket_priority AS ENUM (
    'Trivial',
    'Minor',
    'Major',
    'Critical'
);


ALTER TYPE nmsprime.ticket_priority OWNER TO nmsprime;

--
-- Name: on_update_current_timestamp_authreminders(); Type: FUNCTION; Schema: nmsprime; Owner: nmsprime
--

CREATE FUNCTION nmsprime.on_update_current_timestamp_authreminders() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.created_at = now();
   RETURN NEW;
END;
$$;


ALTER FUNCTION nmsprime.on_update_current_timestamp_authreminders() OWNER TO nmsprime;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: abilities; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.abilities (
    id bigint NOT NULL,
    name character varying(150),
    title character varying(191),
    entity_id bigint,
    entity_type character varying(150),
    only_owned boolean DEFAULT false,
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone
);


ALTER TABLE nmsprime.abilities OWNER TO nmsprime;

--
-- Name: abilities_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.abilities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.abilities_id_seq OWNER TO nmsprime;

--
-- Name: abilities_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.abilities_id_seq OWNED BY nmsprime.abilities.id;


--
-- Name: accountingrecord; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.accountingrecord (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    contract_id bigint,
    name character varying(191),
    product_id bigint,
    ratio double precision,
    count smallint,
    charge double precision,
    sepaaccount_id bigint,
    invoice_nr bigint,
    settlementrun_id bigint
);


ALTER TABLE nmsprime.accountingrecord OWNER TO nmsprime;

--
-- Name: accountingrecord_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.accountingrecord_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.accountingrecord_id_seq OWNER TO nmsprime;

--
-- Name: accountingrecord_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.accountingrecord_id_seq OWNED BY nmsprime.accountingrecord.id;


--
-- Name: address; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.address (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    lat numeric(9,6) NOT NULL,
    lng numeric(9,6) NOT NULL,
    source character varying(191),
    district character varying(191),
    zip character varying(191),
    city character varying(191),
    street character varying(191),
    house_number character varying(191)
);


ALTER TABLE nmsprime.address OWNER TO nmsprime;

--
-- Name: address_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.address_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.address_id_seq OWNER TO nmsprime;

--
-- Name: address_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.address_id_seq OWNED BY nmsprime.address.id;


--
-- Name: alarm; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.alarm (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    status character varying(191),
    agent_host character varying(191),
    alertname character varying(191),
    host character varying(191),
    instance character varying(191),
    job character varying(191),
    severity character varying(191),
    annotations json,
    "startsAt" timestamp with time zone NOT NULL,
    "endsAt" timestamp with time zone NOT NULL,
    "generatorURL" character varying(191),
    fingerprint character varying(191),
    netelement_id integer NOT NULL
);


ALTER TABLE nmsprime.alarm OWNER TO nmsprime;

--
-- Name: alarm_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.alarm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.alarm_id_seq OWNER TO nmsprime;

--
-- Name: alarm_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.alarm_id_seq OWNED BY nmsprime.alarm.id;


--
-- Name: apartment; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.apartment (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    realty_id bigint,
    number character varying(191),
    floor smallint,
    connected boolean,
    occupied boolean,
    description character varying(191),
    connection_type character varying(191),
    code character varying(191)
);


ALTER TABLE nmsprime.apartment OWNER TO nmsprime;

--
-- Name: apartment_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.apartment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.apartment_id_seq OWNER TO nmsprime;

--
-- Name: apartment_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.apartment_id_seq OWNED BY nmsprime.apartment.id;


--
-- Name: arp; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.arp (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    type character varying(191),
    interface character varying(191),
    name character varying(191),
    mac character varying(191),
    ip inet,
    netelement_id integer NOT NULL
);


ALTER TABLE nmsprime.arp OWNER TO nmsprime;

--
-- Name: arp_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.arp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.arp_id_seq OWNER TO nmsprime;

--
-- Name: arp_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.arp_id_seq OWNED BY nmsprime.arp.id;


--
-- Name: assigned_roles; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.assigned_roles (
    role_id bigint,
    entity_id bigint,
    entity_type character varying(150),
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.assigned_roles OWNER TO nmsprime;

--
-- Name: authreminders; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.authreminders (
    email character varying(191),
    token character varying(191),
    created_at timestamp with time zone
);


ALTER TABLE nmsprime.authreminders OWNER TO nmsprime;

--
-- Name: billingbase; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.billingbase (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    rcd smallint,
    currency character varying(191),
    tax double precision,
    mandate_ref_template character varying(191),
    split boolean,
    termination_fix boolean,
    userlang nmsprime.billingbase_userlang DEFAULT 'de'::nmsprime.billingbase_userlang,
    cdr_offset smallint,
    voip_extracharge_default double precision,
    voip_extracharge_mobile_national double precision,
    cdr_retention_period smallint,
    fluid_valid_dates boolean,
    show_ags boolean DEFAULT false,
    adapt_item_start boolean DEFAULT false
);


ALTER TABLE nmsprime.billingbase OWNER TO nmsprime;

--
-- Name: billingbase_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.billingbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.billingbase_id_seq OWNER TO nmsprime;

--
-- Name: billingbase_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.billingbase_id_seq OWNED BY nmsprime.billingbase.id;


--
-- Name: carriercode; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.carriercode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    carrier_code character varying(4),
    company character varying(191)
);


ALTER TABLE nmsprime.carriercode OWNER TO nmsprime;

--
-- Name: carriercode_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.carriercode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.carriercode_id_seq OWNER TO nmsprime;

--
-- Name: carriercode_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.carriercode_id_seq OWNED BY nmsprime.carriercode.id;


--
-- Name: ccap; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ccap (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    cms integer,
    mtas integer,
    dsgs integer,
    rpds integer,
    dpa1_links_overutilized double precision,
    dpa2_links_overutilized double precision,
    redundancy integer,
    uptime bigint,
    sw_ver text,
    config_compliance character varying(191),
    serial character varying(191),
    cms_offline integer,
    mtas_offline integer,
    rpds_offline integer,
    stbs_offline integer,
    device_type character varying(191),
    model character varying(191),
    city character varying(191),
    site character varying(191),
    cpe_device_count json
);


ALTER TABLE nmsprime.ccap OWNER TO nmsprime;

--
-- Name: ccap_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ccap_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ccap_id_seq OWNER TO nmsprime;

--
-- Name: ccap_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ccap_id_seq OWNED BY nmsprime.ccap.id;


--
-- Name: ccc; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ccc (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    template_filename character varying(191),
    headline1 character varying(191),
    headline2 character varying(191),
    language character varying(191) DEFAULT 'en'::character varying,
    logo character varying(191),
    speedtest_url character varying(191),
    bgimg character varying(191)
);


ALTER TABLE nmsprime.ccc OWNER TO nmsprime;

--
-- Name: ccc_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ccc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ccc_id_seq OWNER TO nmsprime;

--
-- Name: ccc_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ccc_id_seq OWNED BY nmsprime.ccc.id;


--
-- Name: comment; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.comment (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    user_id bigint,
    ticket_id bigint,
    comment text
);


ALTER TABLE nmsprime.comment OWNER TO nmsprime;

--
-- Name: comment_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.comment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.comment_id_seq OWNER TO nmsprime;

--
-- Name: comment_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.comment_id_seq OWNED BY nmsprime.comment.id;


--
-- Name: company; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.company (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    street character varying(191),
    zip character varying(16),
    city character varying(191),
    phone character varying(191),
    fax character varying(191),
    web character varying(191),
    mail character varying(191),
    registration_court_1 character varying(191),
    registration_court_2 character varying(191),
    registration_court_3 character varying(191),
    management character varying(191),
    directorate character varying(191),
    tax_id_nr character varying(191),
    tax_nr character varying(191),
    transfer_reason character varying(191),
    logo character varying(191),
    conn_info_template_fn character varying(191)
);


ALTER TABLE nmsprime.company OWNER TO nmsprime;

--
-- Name: company_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.company_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.company_id_seq OWNER TO nmsprime;

--
-- Name: company_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.company_id_seq OWNED BY nmsprime.company.id;


--
-- Name: configfile; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.configfile (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    text text,
    device nmsprime.configfile_device,
    public nmsprime.configfile_public,
    parent_id bigint,
    firmware character varying(191) DEFAULT ''::character varying,
    is_dummy boolean DEFAULT false,
    monitoring text,
    dashboard character varying(191) DEFAULT '/grafana/d/3-42DM6Gk/cablemodem'::character varying,
    service_profile_id integer,
    is_multiservice_ont boolean DEFAULT false NOT NULL,
    tr069_profile_id integer,
    ont_line_profile_id integer
);


ALTER TABLE nmsprime.configfile OWNER TO nmsprime;

--
-- Name: configfile_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.configfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.configfile_id_seq OWNER TO nmsprime;

--
-- Name: configfile_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.configfile_id_seq OWNED BY nmsprime.configfile.id;


--
-- Name: contact; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.contact (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    firstname1 character varying(191),
    lastname1 character varying(191),
    firstname2 character varying(191),
    lastname2 character varying(191),
    company character varying(191),
    tel character varying(191),
    tel_private character varying(191),
    email1 character varying(191),
    email2 character varying(191),
    street character varying(191),
    house_nr character varying(191),
    city character varying(191),
    zip character varying(191),
    district character varying(191),
    administration boolean,
    invoice_text1 text,
    invoice_text2 text,
    invoice_text3 text
);


ALTER TABLE nmsprime.contact OWNER TO nmsprime;

--
-- Name: contact_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.contact_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contact_id_seq OWNER TO nmsprime;

--
-- Name: contact_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.contact_id_seq OWNED BY nmsprime.contact.id;


--
-- Name: contract; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.contract (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    number character varying(32),
    number2 character varying(32),
    number3 character varying(32),
    number4 character varying(32),
    customer_external_id character varying(60),
    company character varying(191),
    department character varying(191),
    salutation character varying(191),
    academic_degree character varying(191),
    firstname character varying(191),
    lastname character varying(191),
    street character varying(191),
    house_number character varying(20),
    zip character varying(16),
    city character varying(191),
    district character varying(191),
    country_id bigint,
    country_code character varying(2),
    phone character varying(100),
    fax character varying(100),
    email character varying(191),
    birthday date,
    contract_start date,
    contract_end date,
    internet_access boolean,
    purchase_tariff bigint,
    next_purchase_tariff bigint,
    qos_id bigint,
    next_qos_id bigint,
    voip_id bigint,
    next_voip_id bigint,
    sepa_iban character varying(34),
    sepa_bic character varying(11),
    sepa_holder character varying(191),
    sepa_institute character varying(191),
    create_invoice boolean,
    login character varying(32),
    password character varying(64),
    net bigint,
    cluster bigint,
    description text,
    costcenter_id bigint,
    salesman_id bigint,
    has_telephony boolean,
    apartment_nr character varying(191),
    additional character varying(191),
    ground_for_dismissal character varying(191),
    group_contract boolean,
    contact bigint,
    value_date smallint,
    apartment_id bigint,
    contact_id bigint,
    last_amendment date,
    lng numeric(9,6),
    lat numeric(9,6),
    geocode_source character varying(191),
    sep_id character varying(32),
    oto_id character varying(64),
    oto_port smallint,
    oto_socket_usage character varying(64),
    oto_status character varying(32),
    flat_id character varying(32),
    alex_status character varying(32),
    omdf_id character varying(128),
    boc_label character varying(128),
    bof_label character varying(32),
    type character varying(32) DEFAULT 'nmsprime'::character varying
);


ALTER TABLE nmsprime.contract OWNER TO nmsprime;

--
-- Name: contract_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.contract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contract_id_seq OWNER TO nmsprime;

--
-- Name: contract_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.contract_id_seq OWNED BY nmsprime.contract.id;


--
-- Name: coremon; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.coremon (
    id smallint NOT NULL,
    updated_at timestamp(0) with time zone,
    deleted_at timestamp(0) with time zone,
    smartphy_url character varying(191),
    apikey character varying(191)
);


ALTER TABLE nmsprime.coremon OWNER TO nmsprime;

--
-- Name: coremon_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.coremon_id_seq
    AS smallint
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.coremon_id_seq OWNER TO nmsprime;

--
-- Name: coremon_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.coremon_id_seq OWNED BY nmsprime.coremon.id;


--
-- Name: costcenter; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.costcenter (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    number character varying(191),
    sepaaccount_id bigint,
    billing_month smallint,
    description character varying(191)
);


ALTER TABLE nmsprime.costcenter OWNER TO nmsprime;

--
-- Name: costcenter_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.costcenter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.costcenter_id_seq OWNER TO nmsprime;

--
-- Name: costcenter_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.costcenter_id_seq OWNED BY nmsprime.costcenter.id;


--
-- Name: cpe; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.cpe (
    id integer NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_ccap_id integer NOT NULL,
    mac character varying(17),
    ip character varying(64),
    status character varying(64),
    last_reg_time timestamp without time zone,
    last_mac_state character varying(64),
    ds_data_service_grp character varying(64),
    us_data_service_grp character varying(64),
    device_class character varying(64),
    core_ipv6 character varying(64),
    manufacturer_name character varying(64),
    reg_ver character varying(64),
    prim_sid character varying(64),
    reg_priv character varying(64),
    cable_if character varying(64),
    internal_id integer NOT NULL,
    netelement_rpd_id integer
);


ALTER TABLE nmsprime.cpe OWNER TO nmsprime;

--
-- Name: cpe_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.cpe_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.cpe_id_seq OWNER TO nmsprime;

--
-- Name: cpe_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.cpe_id_seq OWNED BY nmsprime.cpe.id;


--
-- Name: debt; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.debt (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    contract_id bigint,
    sepamandate_id bigint,
    invoice_id bigint,
    date date,
    amount numeric(10,2),
    bank_fee numeric(10,2),
    total_fee numeric(10,2),
    description text,
    number character varying(191),
    voucher_nr character varying(191),
    due_date date,
    cleared boolean,
    indicator smallint,
    dunning_date date,
    parent_id bigint,
    missing_amount numeric(10,2),
    extra_fee numeric(10,2)
);


ALTER TABLE nmsprime.debt OWNER TO nmsprime;

--
-- Name: debt_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.debt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.debt_id_seq OWNER TO nmsprime;

--
-- Name: debt_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.debt_id_seq OWNED BY nmsprime.debt.id;


--
-- Name: dfsubscription; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.dfsubscription (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    service_name character varying(191),
    service_id character varying(191),
    contact_no character varying(191),
    contact_first_name character varying(191),
    contact_last_name character varying(191),
    contact_company_name character varying(191),
    contact_street character varying(191),
    contact_street_no character varying(16),
    contact_postal_code character varying(16),
    contact_city character varying(191),
    contact_country character varying(191),
    contact_phone character varying(191),
    contact_email character varying(191),
    contact_notes text,
    subscription_id integer,
    subscription_end_point_id integer,
    sf_sla character varying(16),
    status character varying(32),
    wishdate character varying(32),
    switchdate character varying(32),
    modificationdate character varying(32),
    l1_handover_equipment_name character varying(128),
    l1_handover_equipment_rack character varying(64),
    l1_handover_equipment_slot character varying(64),
    l1_handover_equipment_port character varying(16),
    l1_breakout_cable character varying(128),
    l1_breakout_fiber character varying(16),
    alau_order_ref character varying(191),
    note text,
    contract_id integer NOT NULL
);


ALTER TABLE nmsprime.dfsubscription OWNER TO nmsprime;

--
-- Name: dfsubscription_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.dfsubscription_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dfsubscription_id_seq OWNER TO nmsprime;

--
-- Name: dfsubscription_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.dfsubscription_id_seq OWNED BY nmsprime.dfsubscription.id;


--
-- Name: dfsubscriptionevent; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.dfsubscriptionevent (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    description text,
    status character varying(32) NOT NULL,
    "timestamp" character varying(32) NOT NULL,
    dfsubscription_id integer NOT NULL
);


ALTER TABLE nmsprime.dfsubscriptionevent OWNER TO nmsprime;

--
-- Name: dfsubscriptionevent_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.dfsubscriptionevent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dfsubscriptionevent_id_seq OWNER TO nmsprime;

--
-- Name: dfsubscriptionevent_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.dfsubscriptionevent_id_seq OWNED BY nmsprime.dfsubscriptionevent.id;


--
-- Name: domain; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.domain (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    alias character varying(191),
    type nmsprime.domain_type
);


ALTER TABLE nmsprime.domain OWNER TO nmsprime;

--
-- Name: domain_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.domain_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.domain_id_seq OWNER TO nmsprime;

--
-- Name: domain_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.domain_id_seq OWNED BY nmsprime.domain.id;


--
-- Name: dpa; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.dpa (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    redundancy integer,
    cpu_utilization integer,
    memory_utilization integer,
    sys_uptime character varying(191),
    serial_number character varying(191),
    model character varying(191),
    last_conf_change character varying(191),
    sw_ver text,
    city character varying(191),
    site character varying(191)
);


ALTER TABLE nmsprime.dpa OWNER TO nmsprime;

--
-- Name: dpa_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.dpa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dpa_id_seq OWNER TO nmsprime;

--
-- Name: dpa_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.dpa_id_seq OWNED BY nmsprime.dpa.id;


--
-- Name: dpic_card; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.dpic_card (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    sys_name character varying(191),
    description character varying(191),
    uptime character varying(191),
    bandwidth character varying(191),
    utilization character varying(191),
    netelement_ccap_id integer NOT NULL,
    last_state_change_ms bigint,
    internal_id integer NOT NULL,
    pid character varying(191)
);


ALTER TABLE nmsprime.dpic_card OWNER TO nmsprime;

--
-- Name: dpic_card_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.dpic_card_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dpic_card_id_seq OWNER TO nmsprime;

--
-- Name: dpic_card_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.dpic_card_id_seq OWNED BY nmsprime.dpic_card.id;


--
-- Name: netelement_interface; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.netelement_interface (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    ip inet,
    admin_status character varying(191),
    operational_status character varying(191),
    netelement_id integer NOT NULL,
    if_index integer,
    mac macaddr,
    total_bw bigint,
    inbound_rate bigint,
    outbound_rate bigint,
    prev_outbound_counter bigint,
    prev_inbound_counter bigint,
    total_util double precision,
    inbound_util double precision,
    outbound_util double precision,
    total_error_ratio double precision,
    inbound_error_ratio double precision,
    outbound_error_ratio double precision,
    prev_inbound_error_counter bigint,
    prev_outbound_error_counter bigint,
    inbound_frame_rate bigint,
    outbound_frame_rate bigint,
    inbound_unicast_rate bigint,
    outbound_unicast_rate bigint,
    inbound_unicast_frame_rate bigint,
    outbound_unicast_frame_rate bigint,
    inbound_multicast_rate bigint,
    outbound_multicast_rate bigint,
    inbound_multicast_frame_rate bigint,
    outbound_multicast_frame_rate bigint,
    inbound_broadcast_rate bigint,
    outbound_broadcast_rate bigint,
    inbound_broadcast_frame_rate bigint,
    outbound_broadcast_frame_rate bigint,
    inbound_discard_rate bigint,
    outbound_discard_rate bigint,
    prev_inbound_frame_counter bigint,
    prev_outbound_frame_counter bigint,
    prev_inbound_unicast_counter bigint,
    prev_outbound_unicast_counter bigint,
    prev_inbound_unicast_frame_counter bigint,
    prev_outbound_unicast_frame_counter bigint,
    prev_inbound_multicast_counter bigint,
    prev_outbound_multicast_counter bigint,
    prev_inbound_multicast_frame_counter bigint,
    prev_outbound_multicast_frame_counter bigint,
    prev_inbound_broadcast_counter bigint,
    prev_outbound_broadcast_counter bigint,
    prev_inbound_broadcast_frame_counter bigint,
    prev_outbound_broadcast_frame_counter bigint,
    prev_inbound_discard_counter bigint,
    prev_outbound_discard_counter bigint,
    dpic_card_id bigint,
    prev_oid_polling_timestamp integer
);


ALTER TABLE nmsprime.netelement_interface OWNER TO nmsprime;

--
-- Name: dpic_interface_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.dpic_interface_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dpic_interface_id_seq OWNER TO nmsprime;

--
-- Name: dpic_interface_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.dpic_interface_id_seq OWNED BY nmsprime.netelement_interface.id;


--
-- Name: ekpcode; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ekpcode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    ekp_code character varying(191),
    company character varying(191)
);


ALTER TABLE nmsprime.ekpcode OWNER TO nmsprime;

--
-- Name: ekpcode_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ekpcode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ekpcode_id_seq OWNER TO nmsprime;

--
-- Name: ekpcode_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ekpcode_id_seq OWNED BY nmsprime.ekpcode.id;


--
-- Name: endpoint; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.endpoint (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    hostname character varying(63),
    mac character varying(191),
    description text,
    fixed_ip boolean,
    modem_id bigint,
    ip character varying(191),
    add_reverse character varying(191),
    version character varying(1) DEFAULT '4'::character varying,
    prefix character varying(191),
    qos_id integer,
    device_id smallint,
    acl_id integer,
    rule_id integer,
    state character varying(191)
);


ALTER TABLE nmsprime.endpoint OWNER TO nmsprime;

--
-- Name: endpoint_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.endpoint_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.endpoint_id_seq OWNER TO nmsprime;

--
-- Name: endpoint_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.endpoint_id_seq OWNED BY nmsprime.endpoint.id;


--
-- Name: enviacontract; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.enviacontract (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    external_creation_date timestamp with time zone,
    external_termination_date timestamp with time zone,
    envia_customer_reference character varying(60),
    envia_contract_reference character varying(60),
    state character varying(60),
    start_date date,
    end_date date,
    next_id bigint,
    prev_id bigint,
    end_reason character varying(60),
    lock_level bigint,
    method character varying(4),
    sla_id bigint,
    tariff_id character varying(64),
    variation_id character varying(64),
    contract_id bigint,
    modem_id bigint
);


ALTER TABLE nmsprime.enviacontract OWNER TO nmsprime;

--
-- Name: enviacontract_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.enviacontract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviacontract_id_seq OWNER TO nmsprime;

--
-- Name: enviacontract_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.enviacontract_id_seq OWNED BY nmsprime.enviacontract.id;


--
-- Name: enviaorder; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.enviaorder (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    orderid bigint,
    method character varying(191),
    ordertype_id bigint,
    ordertype character varying(191),
    orderstatus_id bigint,
    orderstatus character varying(191),
    orderdate date,
    ordercomment character varying(191),
    related_order_id bigint,
    customerreference character varying(60),
    contractreference character varying(60),
    contract_id bigint,
    modem_id bigint,
    enviacontract_id bigint,
    last_user_interaction timestamp with time zone
);


ALTER TABLE nmsprime.enviaorder OWNER TO nmsprime;

--
-- Name: enviaorder_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.enviaorder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_id_seq OWNER TO nmsprime;

--
-- Name: enviaorder_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.enviaorder_id_seq OWNED BY nmsprime.enviaorder.id;


--
-- Name: enviaorder_phonenumber; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.enviaorder_phonenumber (
    id bigint NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    enviaorder_id bigint,
    phonenumber_id bigint
);


ALTER TABLE nmsprime.enviaorder_phonenumber OWNER TO nmsprime;

--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.enviaorder_phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_phonenumber_id_seq OWNER TO nmsprime;

--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.enviaorder_phonenumber_id_seq OWNED BY nmsprime.enviaorder_phonenumber.id;


--
-- Name: enviaorderdocument; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.enviaorderdocument (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    document_type nmsprime.enviaorderdocument_document_type,
    mime_type character varying(191),
    filename character varying(191),
    enviaorder_id bigint,
    upload_order_id bigint
);


ALTER TABLE nmsprime.enviaorderdocument OWNER TO nmsprime;

--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.enviaorderdocument_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorderdocument_id_seq OWNER TO nmsprime;

--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.enviaorderdocument_id_seq OWNED BY nmsprime.enviaorderdocument.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.failed_jobs (
    id bigint NOT NULL,
    connection text,
    queue text,
    payload text,
    failed_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    exception text,
    uuid character varying(191)
);


ALTER TABLE nmsprime.failed_jobs OWNER TO nmsprime;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.failed_jobs_id_seq OWNER TO nmsprime;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.failed_jobs_id_seq OWNED BY nmsprime.failed_jobs.id;


--
-- Name: favorite_netelements; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.favorite_netelements (
    id bigint NOT NULL,
    user_id bigint,
    netelement_id bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.favorite_netelements OWNER TO nmsprime;

--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.favorite_netelements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.favorite_netelements_id_seq OWNER TO nmsprime;

--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.favorite_netelements_id_seq OWNED BY nmsprime.favorite_netelements.id;


--
-- Name: fiber_node; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.fiber_node (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    cable_interface character varying(191),
    netelement_id integer NOT NULL,
    mdd_status character varying(191)
);


ALTER TABLE nmsprime.fiber_node OWNER TO nmsprime;

--
-- Name: fiber_node_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.fiber_node_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.fiber_node_id_seq OWNER TO nmsprime;

--
-- Name: fiber_node_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.fiber_node_id_seq OWNED BY nmsprime.fiber_node.id;


--
-- Name: fiber_node_rpd; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.fiber_node_rpd (
    created_at timestamp(0) with time zone,
    fiber_node_id integer NOT NULL,
    rpd_id integer NOT NULL,
    id bigint NOT NULL
);


ALTER TABLE nmsprime.fiber_node_rpd OWNER TO nmsprime;

--
-- Name: fiber_node_rpd_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.fiber_node_rpd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.fiber_node_rpd_id_seq OWNER TO nmsprime;

--
-- Name: fiber_node_rpd_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.fiber_node_rpd_id_seq OWNED BY nmsprime.fiber_node_rpd.id;


--
-- Name: global_config; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.global_config (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    street character varying(191),
    city character varying(191),
    phone character varying(191),
    mail character varying(191),
    log_level bigint,
    headline1 character varying(191),
    headline2 character varying(191),
    default_country_code character varying(2),
    passwordresetinterval bigint DEFAULT '120'::bigint,
    alert1 character varying(191),
    alert2 character varying(191),
    alert3 character varying(191),
    isallnetssidebarenabled boolean DEFAULT false,
    login_img character varying(191)
);


ALTER TABLE nmsprime.global_config OWNER TO nmsprime;

--
-- Name: global_config_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.global_config_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.global_config_id_seq OWNER TO nmsprime;

--
-- Name: global_config_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.global_config_id_seq OWNED BY nmsprime.global_config.id;


--
-- Name: guilog; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.guilog (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    user_id bigint,
    username character varying(191),
    method character varying(191),
    model character varying(191),
    model_id character varying(191),
    text text
);


ALTER TABLE nmsprime.guilog OWNER TO nmsprime;

--
-- Name: guilog_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.guilog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.guilog_id_seq OWNER TO nmsprime;

--
-- Name: guilog_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.guilog_id_seq OWNED BY nmsprime.guilog.id;


--
-- Name: hfcreq; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.hfcreq (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    ro_community character varying(191),
    rw_community character varying(191),
    rkm_server character varying(191),
    rkm_server_username character varying(191),
    rkm_server_password character varying(191),
    video_controller character varying(191),
    video_controller_username character varying(191),
    video_controller_password character varying(191),
    video_encoder character varying(191)
);


ALTER TABLE nmsprime.hfcreq OWNER TO nmsprime;

--
-- Name: hfcreq_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.hfcreq_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.hfcreq_id_seq OWNER TO nmsprime;

--
-- Name: hfcreq_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.hfcreq_id_seq OWNED BY nmsprime.hfcreq.id;


--
-- Name: hubsite; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.hubsite (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL
);


ALTER TABLE nmsprime.hubsite OWNER TO nmsprime;

--
-- Name: hubsite_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.hubsite_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.hubsite_id_seq OWNER TO nmsprime;

--
-- Name: hubsite_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.hubsite_id_seq OWNED BY nmsprime.hubsite.id;


--
-- Name: indices; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.indices (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id bigint,
    parameter_id bigint,
    indices character varying(1024)
);


ALTER TABLE nmsprime.indices OWNER TO nmsprime;

--
-- Name: indices_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.indices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.indices_id_seq OWNER TO nmsprime;

--
-- Name: indices_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.indices_id_seq OWNED BY nmsprime.indices.id;


--
-- Name: inventory; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.inventory (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    netelement_id integer NOT NULL,
    name character varying(191),
    pid character varying(191),
    manufacturer character varying(191),
    serial_nr character varying(191),
    part_nr character varying(191),
    ports integer,
    status character varying(191),
    inserted character varying(191),
    description text
);


ALTER TABLE nmsprime.inventory OWNER TO nmsprime;

--
-- Name: inventory_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.inventory_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.inventory_id_seq OWNER TO nmsprime;

--
-- Name: inventory_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.inventory_id_seq OWNED BY nmsprime.inventory.id;


--
-- Name: invoice; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.invoice (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    contract_id bigint,
    settlementrun_id bigint,
    sepaaccount_id bigint,
    year smallint,
    month smallint,
    filename character varying(191),
    type nmsprime.invoice_type,
    number character varying(191),
    charge double precision,
    charge_gross numeric(8,2)
);


ALTER TABLE nmsprime.invoice OWNER TO nmsprime;

--
-- Name: invoice_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.invoice_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.invoice_id_seq OWNER TO nmsprime;

--
-- Name: invoice_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.invoice_id_seq OWNED BY nmsprime.invoice.id;


--
-- Name: ippool; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ippool (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netgw_id bigint,
    type character varying(191),
    net inet,
    ip_pool_start inet,
    ip_pool_end inet,
    router_ip inet,
    broadcast_ip character varying(191),
    dns1_ip character varying(191),
    dns2_ip character varying(191),
    dns3_ip character varying(191),
    optional text,
    description text,
    version character varying(1) DEFAULT '4'::character varying,
    prefix character varying(191),
    prefix_len character varying(191),
    delegated_len character varying(191),
    active boolean DEFAULT true,
    vendor_class_identifier character varying(191)
);


ALTER TABLE nmsprime.ippool OWNER TO nmsprime;

--
-- Name: ippool_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ippool_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ippool_id_seq OWNER TO nmsprime;

--
-- Name: ippool_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ippool_id_seq OWNED BY nmsprime.ippool.id;


--
-- Name: item; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.item (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    contract_id bigint,
    product_id bigint,
    count bigint DEFAULT '1'::bigint,
    valid_from date,
    valid_from_fixed boolean DEFAULT true,
    valid_to date,
    valid_to_fixed boolean DEFAULT true,
    credit_amount numeric(13,4),
    costcenter_id bigint,
    accounting_text text,
    payed_month smallint,
    smartcardids text
);


ALTER TABLE nmsprime.item OWNER TO nmsprime;

--
-- Name: item_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.item_id_seq OWNER TO nmsprime;

--
-- Name: item_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.item_id_seq OWNED BY nmsprime.item.id;


--
-- Name: jobs; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.jobs (
    id bigint NOT NULL,
    queue character varying(191),
    payload text,
    attempts smallint,
    reserved_at bigint,
    available_at bigint,
    created_at bigint
);


ALTER TABLE nmsprime.jobs OWNER TO nmsprime;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.jobs_id_seq OWNER TO nmsprime;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.jobs_id_seq OWNED BY nmsprime.jobs.id;


--
-- Name: l2tp; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.l2tp (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    netelement_id integer NOT NULL,
    tunnel_id character varying(191),
    remote_tunnel_id character varying(191),
    ip_version smallint,
    ip inet,
    remote_ip inet,
    error_code character varying(191)
);


ALTER TABLE nmsprime.l2tp OWNER TO nmsprime;

--
-- Name: l2tp_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.l2tp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.l2tp_id_seq OWNER TO nmsprime;

--
-- Name: l2tp_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.l2tp_id_seq OWNED BY nmsprime.l2tp.id;


--
-- Name: link; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.link (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    "from" integer NOT NULL,
    "to" integer NOT NULL,
    name character varying(191),
    if_from character varying(191),
    if_to character varying(191),
    type character varying(191),
    state character varying(191),
    description text,
    is_up_from boolean,
    is_up_to boolean,
    speed_from integer,
    speed_to integer,
    utilization_from double precision,
    utilization_to double precision,
    rx_errors_from integer,
    rx_errors_to integer,
    tx_errors_from integer,
    tx_errors_to integer
);


ALTER TABLE nmsprime.link OWNER TO nmsprime;

--
-- Name: link_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.link_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.link_id_seq OWNER TO nmsprime;

--
-- Name: link_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.link_id_seq OWNED BY nmsprime.link.id;


--
-- Name: lldp; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.lldp (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    local_interface_description character varying(191),
    remote_interface_description character varying(191),
    remote_system_name character varying(191),
    remote_system_description text,
    if_index integer,
    netelement_id integer NOT NULL,
    lldp_suffix character varying(191),
    remote_netelement_id integer,
    mac_address character varying(191)
);


ALTER TABLE nmsprime.lldp OWNER TO nmsprime;

--
-- Name: lldp_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.lldp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.lldp_id_seq OWNER TO nmsprime;

--
-- Name: lldp_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.lldp_id_seq OWNED BY nmsprime.lldp.id;


--
-- Name: market; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.market (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL
);


ALTER TABLE nmsprime.market OWNER TO nmsprime;

--
-- Name: market_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.market_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.market_id_seq OWNER TO nmsprime;

--
-- Name: market_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.market_id_seq OWNED BY nmsprime.market.id;


--
-- Name: mibfile; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.mibfile (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    filename character varying(191),
    version character varying(191),
    description text
);


ALTER TABLE nmsprime.mibfile OWNER TO nmsprime;

--
-- Name: mibfile_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.mibfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mibfile_id_seq OWNER TO nmsprime;

--
-- Name: mibfile_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.mibfile_id_seq OWNED BY nmsprime.mibfile.id;


--
-- Name: migrations; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.migrations (
    id bigint NOT NULL,
    migration character varying(255),
    batch bigint
);


ALTER TABLE nmsprime.migrations OWNER TO nmsprime;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.migrations_id_seq OWNER TO nmsprime;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.migrations_id_seq OWNED BY nmsprime.migrations.id;


--
-- Name: modem; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.modem (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    hostname character varying(191),
    contract_id bigint,
    contract_external_id character varying(191),
    contract_ext_creation_date date,
    contract_ext_termination_date date,
    salutation character varying(191),
    company character varying(191),
    department character varying(191),
    firstname character varying(191),
    lastname character varying(191),
    street character varying(191),
    house_number character varying(8),
    zip character varying(16),
    city character varying(191),
    district character varying(191),
    birthday character varying(191),
    country_id bigint,
    country_code character varying(2),
    installation_address_change_date date,
    mac character varying(191),
    us_pwr bigint,
    us_snr bigint,
    ds_pwr bigint,
    ds_snr bigint,
    public boolean,
    internet_access boolean,
    serial_num character varying(191),
    inventar_num character varying(191),
    description text,
    parent bigint,
    configfile_id bigint,
    netelement_id bigint,
    qos_id bigint,
    lng numeric(9,6),
    lat numeric(9,6),
    geocode_source character varying(191),
    number character varying(32),
    tdr double precision,
    fft_max double precision,
    model character varying(191),
    sw_rev character varying(191),
    support_state character varying(30) DEFAULT 'verifying'::character varying,
    ppp_username character varying(64),
    ppp_password character varying(191),
    apartment_nr character varying(191),
    next_passive_id bigint,
    phy_updated_at timestamp with time zone,
    address_to_invoice boolean,
    apartment_id bigint,
    ipv4 inet,
    additional character varying(191),
    fiber_name character varying(191),
    ont_id smallint,
    netgw_id integer,
    frame_id smallint,
    slot_id smallint,
    port_id smallint,
    service_port_id integer,
    or_id character varying(191),
    ont_state character varying(191),
    next_ont_state character varying(191),
    ont_state_switchdate timestamp(0) without time zone
);


ALTER TABLE nmsprime.modem OWNER TO nmsprime;

--
-- Name: modem_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.modem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_id_seq OWNER TO nmsprime;

--
-- Name: modem_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.modem_id_seq OWNED BY nmsprime.modem.id;


--
-- Name: modem_option; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.modem_option (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    modem_id bigint,
    key character varying(191),
    value character varying(191)
);


ALTER TABLE nmsprime.modem_option OWNER TO nmsprime;

--
-- Name: modem_option_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.modem_option_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_option_id_seq OWNER TO nmsprime;

--
-- Name: modem_option_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.modem_option_id_seq OWNED BY nmsprime.modem_option.id;


--
-- Name: mpr; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.mpr (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    value text,
    netelement_id bigint,
    description text
);


ALTER TABLE nmsprime.mpr OWNER TO nmsprime;

--
-- Name: mpr_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.mpr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mpr_id_seq OWNER TO nmsprime;

--
-- Name: mpr_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.mpr_id_seq OWNED BY nmsprime.mpr.id;


--
-- Name: mprgeopos; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.mprgeopos (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    mpr_id bigint,
    lng numeric(9,6),
    lat numeric(9,6)
);


ALTER TABLE nmsprime.mprgeopos OWNER TO nmsprime;

--
-- Name: mprgeopos_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.mprgeopos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mprgeopos_id_seq OWNER TO nmsprime;

--
-- Name: mprgeopos_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.mprgeopos_id_seq OWNED BY nmsprime.mprgeopos.id;


--
-- Name: mta; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.mta (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    modem_id bigint DEFAULT '1'::bigint,
    mac character varying(191),
    hostname character varying(191),
    configfile_id bigint DEFAULT '1'::bigint,
    type nmsprime.mta_type,
    is_dummy boolean DEFAULT false
);


ALTER TABLE nmsprime.mta OWNER TO nmsprime;

--
-- Name: mta_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.mta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mta_id_seq OWNER TO nmsprime;

--
-- Name: mta_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.mta_id_seq OWNED BY nmsprime.mta.id;


--
-- Name: ncs; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ncs (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    cpu_utilization double precision,
    memory_utilization double precision,
    sys_uptime character varying(191),
    serial_number character varying(191),
    model character varying(191),
    sw_ver text,
    city character varying(191),
    site character varying(191),
    redundancy integer
);


ALTER TABLE nmsprime.ncs OWNER TO nmsprime;

--
-- Name: ncs_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ncs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ncs_id_seq OWNER TO nmsprime;

--
-- Name: ncs_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ncs_id_seq OWNED BY nmsprime.ncs.id;


--
-- Name: net; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.net (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL
);


ALTER TABLE nmsprime.net OWNER TO nmsprime;

--
-- Name: net_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.net_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.net_id_seq OWNER TO nmsprime;

--
-- Name: net_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.net_id_seq OWNED BY nmsprime.net.id;


--
-- Name: netelement; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.netelement (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    series bigint,
    options bigint,
    ip inet,
    lng numeric(9,6),
    lat numeric(9,6),
    link character varying(191),
    "user" bigint,
    access bigint,
    net bigint,
    cluster bigint,
    layer bigint,
    descr text,
    infrastructure_file character varying(191),
    draw character varying(191),
    line character varying(191),
    parent_id bigint,
    netelementtype_id bigint,
    community_ro character varying(45),
    community_rw character varying(45),
    address1 character varying(191),
    address2 character varying(191),
    controlling_link character varying(191),
    prov_device_id bigint,
    netgw_id bigint,
    agc_offset double precision,
    rkm_line_number bigint,
    state character varying(191),
    _lft bigint,
    _rgt bigint,
    apartment_id bigint,
    id_name character varying GENERATED ALWAYS AS (
CASE
    WHEN (name IS NULL) THEN ((id)::character varying)::text
    WHEN (id IS NULL) THEN (name)::text
    ELSE ((((id)::character varying)::text || '_'::text) || (name)::text)
END) STORED,
    username character varying(191),
    password character varying(191),
    base_type_id integer,
    online boolean DEFAULT true NOT NULL,
    port integer
);


ALTER TABLE nmsprime.netelement OWNER TO nmsprime;

--
-- Name: netelement_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.netelement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelement_id_seq OWNER TO nmsprime;

--
-- Name: netelement_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.netelement_id_seq OWNED BY nmsprime.netelement.id;


--
-- Name: netelementtype; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.netelementtype (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    vendor character varying(191),
    version character varying(191),
    description text,
    parent_id bigint,
    icon_name character varying(191),
    pre_conf_oid_id bigint,
    pre_conf_value character varying(191),
    pre_conf_time_offset double precision,
    page_reload_time double precision,
    base_type_id numeric,
    sidebar_pos smallint,
    _lft integer,
    _rgt integer
);


ALTER TABLE nmsprime.netelementtype OWNER TO nmsprime;

--
-- Name: netelementtype_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.netelementtype_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelementtype_id_seq OWNER TO nmsprime;

--
-- Name: netelementtype_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.netelementtype_id_seq OWNED BY nmsprime.netelementtype.id;


--
-- Name: netgw; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.netgw (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    hostname character varying(191),
    series character varying(191),
    ip character varying(191),
    community_rw character varying(191),
    community_ro character varying(191),
    company character varying(191),
    network bigint,
    state bigint,
    monitoring bigint,
    support_state character varying(30) DEFAULT 'verifying'::character varying,
    type character varying(191) DEFAULT 'cmts'::character varying,
    username character varying(191),
    password character varying(191),
    ssh_port integer,
    ssh_auto_prov boolean DEFAULT false,
    coa_port integer,
    ipv6 character varying(191),
    nas_secret character varying(191),
    internal_id integer DEFAULT 0
);


ALTER TABLE nmsprime.netgw OWNER TO nmsprime;

--
-- Name: netgw_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.netgw_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netgw_id_seq OWNER TO nmsprime;

--
-- Name: netgw_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.netgw_id_seq OWNED BY nmsprime.netgw.id;


--
-- Name: node; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.node (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id bigint,
    name character varying(191),
    street character varying(191),
    house_nr character varying(191),
    zip character varying(191),
    city character varying(191),
    type character varying(191),
    headend boolean,
    description character varying(191),
    lng numeric(9,6),
    lat numeric(9,6),
    country_code character varying(191),
    geocode_source character varying(191),
    district character varying(191)
);


ALTER TABLE nmsprime.node OWNER TO nmsprime;

--
-- Name: node_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.node_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.node_id_seq OWNER TO nmsprime;

--
-- Name: node_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.node_id_seq OWNED BY nmsprime.node.id;


--
-- Name: notifications; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.notifications (
    id character(36) NOT NULL,
    type character varying(191),
    notifiable_type character varying(191),
    notifiable_id numeric,
    data text,
    read_at timestamp without time zone,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE nmsprime.notifications OWNER TO nmsprime;

--
-- Name: numberrange; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.numberrange (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    start bigint,
    "end" bigint,
    prefix character varying(191) DEFAULT ''::character varying,
    suffix character varying(191) DEFAULT ''::character varying,
    costcenter_id bigint,
    type nmsprime.numberrange_type
);


ALTER TABLE nmsprime.numberrange OWNER TO nmsprime;

--
-- Name: numberrange_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.numberrange_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.numberrange_id_seq OWNER TO nmsprime;

--
-- Name: numberrange_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.numberrange_id_seq OWNED BY nmsprime.numberrange.id;


--
-- Name: oid; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.oid (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    mibfile_id bigint,
    html_type nmsprime.oid_html_type,
    name character varying(191),
    oid character varying(191),
    oid_table boolean,
    type nmsprime.oid_type,
    type_array character varying(191),
    phpcode_pre text,
    phpcode_post text,
    description text,
    name_gui character varying(191),
    unit_divisor bigint,
    startvalue bigint,
    endvalue bigint,
    stepsize numeric(9,4),
    syntax character varying(191),
    access character varying(191),
    value_set text
);


ALTER TABLE nmsprime.oid OWNER TO nmsprime;

--
-- Name: oid_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.oid_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.oid_id_seq OWNER TO nmsprime;

--
-- Name: oid_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.oid_id_seq OWNED BY nmsprime.oid.id;


--
-- Name: oui; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.oui (
    id bigint NOT NULL,
    oui character varying(8) NOT NULL,
    name character varying(191) NOT NULL
);


ALTER TABLE nmsprime.oui OWNER TO nmsprime;

--
-- Name: oui_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.oui_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.oui_id_seq OWNER TO nmsprime;

--
-- Name: oui_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.oui_id_seq OWNED BY nmsprime.oui.id;


--
-- Name: overduedebts; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.overduedebts (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    fee numeric(10,4),
    total boolean,
    dunning_charge1 double precision,
    dunning_charge2 double precision,
    dunning_charge3 double precision,
    dunning_text1 text,
    dunning_text2 text,
    dunning_text3 text,
    payment_period character varying(191),
    import_inet_block_amount double precision,
    import_inet_block_debts smallint,
    import_inet_block_indicator smallint
);


ALTER TABLE nmsprime.overduedebts OWNER TO nmsprime;

--
-- Name: overduedebts_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.overduedebts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.overduedebts_id_seq OWNER TO nmsprime;

--
-- Name: overduedebts_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.overduedebts_id_seq OWNED BY nmsprime.overduedebts.id;


--
-- Name: parameter; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.parameter (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelementtype_id bigint,
    oid_id bigint,
    diff_param boolean DEFAULT false,
    divide_by character varying(191),
    parent_id bigint,
    third_dimension boolean DEFAULT false,
    html_frame character varying(16),
    html_properties text,
    html_id bigint
);


ALTER TABLE nmsprime.parameter OWNER TO nmsprime;

--
-- Name: parameter_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.parameter_id_seq OWNER TO nmsprime;

--
-- Name: parameter_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.parameter_id_seq OWNED BY nmsprime.parameter.id;


--
-- Name: permissions; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.permissions (
    ability_id bigint NOT NULL,
    entity_id bigint,
    entity_type character varying(150),
    forbidden boolean DEFAULT false,
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.permissions OWNER TO nmsprime;

--
-- Name: phonebookentry; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.phonebookentry (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    phonenumbermanagement_id bigint,
    reverse_search character(1),
    publish_in_print_media character(1),
    publish_in_electronic_media character(1),
    directory_assistance character(1),
    entry_type character(1),
    publish_address character(1),
    company character varying(191),
    salutation character varying(191),
    academic_degree character varying(191),
    noble_rank character varying(191),
    nobiliary_particle character varying(191),
    lastname character varying(191),
    other_name_suffix character varying(191),
    firstname character varying(191),
    street character varying(191),
    houseno character varying(191),
    zipcode character varying(191),
    city character varying(191),
    urban_district character varying(191),
    business character varying(191),
    usage character(1),
    tag character varying(191),
    external_creation_date date,
    external_update_date date
);


ALTER TABLE nmsprime.phonebookentry OWNER TO nmsprime;

--
-- Name: phonebookentry_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.phonebookentry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonebookentry_id_seq OWNER TO nmsprime;

--
-- Name: phonebookentry_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.phonebookentry_id_seq OWNED BY nmsprime.phonebookentry.id;


--
-- Name: phonenumber; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.phonenumber (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    mta_id bigint DEFAULT '1'::bigint,
    port smallint,
    country_code character varying(191),
    prefix_number character varying(191),
    number character varying(191),
    username character varying(191),
    password character varying(191),
    sipdomain character varying(191),
    active boolean,
    is_dummy boolean DEFAULT false,
    contract_external_id character varying(191)
);


ALTER TABLE nmsprime.phonenumber OWNER TO nmsprime;

--
-- Name: phonenumber_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumber_id_seq OWNER TO nmsprime;

--
-- Name: phonenumber_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.phonenumber_id_seq OWNED BY nmsprime.phonenumber.id;


--
-- Name: phonenumbermanagement; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.phonenumbermanagement (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    phonenumber_id bigint DEFAULT '1'::bigint,
    trcclass bigint,
    voipaccount_ext_creation_date timestamp with time zone,
    activation_date date,
    external_activation_date date,
    porting_in boolean DEFAULT false,
    carrier_in bigint,
    ekp_in bigint,
    deactivation_date date,
    external_deactivation_date date,
    voipaccount_ext_termination_date timestamp with time zone,
    porting_out boolean DEFAULT false,
    carrier_out bigint,
    ekp_out bigint,
    subscriber_company character varying(191),
    subscriber_department character varying(191),
    subscriber_salutation character varying(191),
    subscriber_academic_degree character varying(191),
    subscriber_firstname character varying(191),
    subscriber_lastname character varying(191),
    subscriber_street character varying(191),
    subscriber_house_number character varying(8),
    subscriber_zip character varying(16),
    subscriber_city character varying(191),
    subscriber_district character varying(191),
    subscriber_country bigint,
    autogenerated boolean DEFAULT false,
    enviacontract_id bigint
);


ALTER TABLE nmsprime.phonenumbermanagement OWNER TO nmsprime;

--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.phonenumbermanagement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumbermanagement_id_seq OWNER TO nmsprime;

--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.phonenumbermanagement_id_seq OWNED BY nmsprime.phonenumbermanagement.id;


--
-- Name: phonetariff; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.phonetariff (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    external_identifier character varying(191),
    name character varying(191),
    type nmsprime.phonetariff_type,
    description character varying(191),
    usable boolean DEFAULT true,
    voip_protocol nmsprime.phonetariff_voip_protocol
);


ALTER TABLE nmsprime.phonetariff OWNER TO nmsprime;

--
-- Name: phonetariff_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.phonetariff_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonetariff_id_seq OWNER TO nmsprime;

--
-- Name: phonetariff_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.phonetariff_id_seq OWNED BY nmsprime.phonetariff.id;


--
-- Name: product; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.product (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    type nmsprime.product_type,
    qos_id smallint,
    voip_sales_tariff_id bigint,
    voip_purchase_tariff_id bigint,
    billing_cycle nmsprime.product_billing_cycle,
    maturity character varying(20),
    costcenter_id bigint,
    price numeric(13,4),
    tax boolean,
    bundled_with_voip boolean DEFAULT false,
    email_count bigint,
    period_of_notice character varying(20),
    maturity_min character varying(20),
    proportional boolean,
    record_monthly boolean,
    deprecated boolean,
    markon numeric(8,2)
);


ALTER TABLE nmsprime.product OWNER TO nmsprime;

--
-- Name: product_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.product_id_seq OWNER TO nmsprime;

--
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.product_id_seq OWNED BY nmsprime.product.id;


--
-- Name: provbase; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.provbase (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    provisioning_server character varying(191),
    ro_community character varying(191),
    rw_community character varying(191),
    domain_name character varying(191),
    dns_password character varying(191),
    dhcp_def_lease_time bigint,
    dhcp_max_lease_time bigint,
    startid_contract bigint,
    startid_modem bigint,
    startid_endpoint bigint,
    max_cpe smallint,
    ds_rate_coefficient double precision,
    us_rate_coefficient double precision,
    multiple_provisioning_systems boolean,
    additional_modem_reset boolean,
    modem_edit_page_new_tab boolean,
    random_ip_allocation boolean DEFAULT false,
    ppp_session_timeout bigint DEFAULT '86400'::bigint,
    auto_factory_reset boolean DEFAULT false,
    acct_interim_interval bigint DEFAULT '300'::bigint
);


ALTER TABLE nmsprime.provbase OWNER TO nmsprime;

--
-- Name: provbase_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.provbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provbase_id_seq OWNER TO nmsprime;

--
-- Name: provbase_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.provbase_id_seq OWNED BY nmsprime.provbase.id;


--
-- Name: provmon; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.provmon (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    start_frequency bigint,
    stop_frequency bigint,
    span bigint
);


ALTER TABLE nmsprime.provmon OWNER TO nmsprime;

--
-- Name: provmon_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.provmon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provmon_id_seq OWNER TO nmsprime;

--
-- Name: provmon_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.provmon_id_seq OWNED BY nmsprime.provmon.id;


--
-- Name: provvoip; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.provvoip (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    startid_mta bigint,
    mta_domain character varying(191),
    default_sip_registrar character varying(191),
    default_country_code character varying(191)
);


ALTER TABLE nmsprime.provvoip OWNER TO nmsprime;

--
-- Name: provvoip_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.provvoip_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provvoip_id_seq OWNER TO nmsprime;

--
-- Name: provvoip_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.provvoip_id_seq OWNED BY nmsprime.provvoip.id;


--
-- Name: qos; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.qos (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    ds_rate_max double precision,
    us_rate_max double precision,
    ds_rate_max_help bigint,
    us_rate_max_help bigint,
    name character varying(191),
    ds_name character varying(191),
    us_name character varying(191),
    type character varying(191) DEFAULT 'default'::character varying,
    vlan_id smallint DEFAULT '0'::smallint,
    ont_line_profile_id integer,
    service_profile_id integer,
    gem_port integer,
    traffic_table_in integer,
    traffic_table_out integer
);


ALTER TABLE nmsprime.qos OWNER TO nmsprime;

--
-- Name: qos_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.qos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.qos_id_seq OWNER TO nmsprime;

--
-- Name: qos_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.qos_id_seq OWNED BY nmsprime.qos.id;


--
-- Name: radacct; Type: FOREIGN TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE FOREIGN TABLE nmsprime.radacct (
    radacctid bigint NOT NULL,
    acctsessionid text NOT NULL,
    acctuniqueid text NOT NULL,
    username text,
    groupname text,
    realm text,
    nasipaddress inet NOT NULL,
    nasportid text,
    nasporttype text,
    acctstarttime timestamp with time zone,
    acctupdatetime timestamp with time zone,
    acctstoptime timestamp with time zone,
    acctinterval bigint,
    acctsessiontime bigint,
    acctauthentic text,
    connectinfo_start text,
    connectinfo_stop text,
    acctinputoctets bigint,
    acctoutputoctets bigint,
    calledstationid text,
    callingstationid text,
    acctterminatecause text,
    servicetype text,
    framedprotocol text,
    framedipaddress inet
)
SERVER "nmsprime-radius"
OPTIONS (
    schema_name 'public',
    table_name 'radacct'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN radacctid OPTIONS (
    column_name 'radacctid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctsessionid OPTIONS (
    column_name 'acctsessionid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctuniqueid OPTIONS (
    column_name 'acctuniqueid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN username OPTIONS (
    column_name 'username'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN groupname OPTIONS (
    column_name 'groupname'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN realm OPTIONS (
    column_name 'realm'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN nasipaddress OPTIONS (
    column_name 'nasipaddress'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN nasportid OPTIONS (
    column_name 'nasportid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN nasporttype OPTIONS (
    column_name 'nasporttype'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctstarttime OPTIONS (
    column_name 'acctstarttime'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctupdatetime OPTIONS (
    column_name 'acctupdatetime'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctstoptime OPTIONS (
    column_name 'acctstoptime'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctinterval OPTIONS (
    column_name 'acctinterval'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctsessiontime OPTIONS (
    column_name 'acctsessiontime'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctauthentic OPTIONS (
    column_name 'acctauthentic'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN connectinfo_start OPTIONS (
    column_name 'connectinfo_start'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN connectinfo_stop OPTIONS (
    column_name 'connectinfo_stop'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctinputoctets OPTIONS (
    column_name 'acctinputoctets'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctoutputoctets OPTIONS (
    column_name 'acctoutputoctets'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN calledstationid OPTIONS (
    column_name 'calledstationid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN callingstationid OPTIONS (
    column_name 'callingstationid'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN acctterminatecause OPTIONS (
    column_name 'acctterminatecause'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN servicetype OPTIONS (
    column_name 'servicetype'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN framedprotocol OPTIONS (
    column_name 'framedprotocol'
);
ALTER FOREIGN TABLE nmsprime.radacct ALTER COLUMN framedipaddress OPTIONS (
    column_name 'framedipaddress'
);


ALTER FOREIGN TABLE nmsprime.radacct OWNER TO nmsprime;

--
-- Name: realty; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.realty (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    node_id bigint,
    name character varying(191),
    number character varying(191),
    street character varying(191),
    house_nr character varying(191),
    district character varying(191),
    zip character varying(191),
    city character varying(191),
    expansion_degree character varying(191),
    concession_agreement character varying(191),
    agreement_from date,
    agreement_to date,
    last_restoration_on date,
    description character varying(191),
    contact_id bigint,
    contact_local_id bigint,
    lng numeric(9,6),
    lat numeric(9,6),
    geocode_source character varying(191),
    country_code character varying(191),
    contract_id bigint
);


ALTER TABLE nmsprime.realty OWNER TO nmsprime;

--
-- Name: realty_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.realty_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.realty_id_seq OWNER TO nmsprime;

--
-- Name: realty_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.realty_id_seq OWNED BY nmsprime.realty.id;


--
-- Name: roles; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.roles (
    id bigint NOT NULL,
    name character varying(150),
    title character varying(191),
    description character varying(191),
    rank bigint,
    level bigint,
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone
);


ALTER TABLE nmsprime.roles OWNER TO nmsprime;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.roles_id_seq OWNER TO nmsprime;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.roles_id_seq OWNED BY nmsprime.roles.id;


--
-- Name: rpa; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.rpa (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    sys_uptime character varying(191),
    serial_number character varying(191),
    model character varying(191),
    last_conf_change character varying(191),
    sw_ver text,
    cpu_utilization character varying(191),
    memory_utilization character varying(191),
    city character varying(191),
    site character varying(191)
);


ALTER TABLE nmsprime.rpa OWNER TO nmsprime;

--
-- Name: rpa_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.rpa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.rpa_id_seq OWNER TO nmsprime;

--
-- Name: rpa_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.rpa_id_seq OWNED BY nmsprime.rpa.id;


--
-- Name: rpd; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.rpd (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    name character varying(64),
    mac character varying(17),
    aux boolean,
    uptime character varying(191),
    type character varying(64),
    vendor character varying(64),
    serial_num character varying(64),
    model character varying(64),
    sw_ver character varying(64),
    ccap_if character varying(64),
    status character varying(64),
    city character varying(64),
    site character varying(64),
    fiber_node_name character varying(191),
    bc_vid_oob_if character varying(191),
    nc_vid_oob_if character varying(191),
    ds_data_cont_profile character varying(191),
    us_data_cont_profile character varying(191),
    cin_rpa character varying(191),
    cin_dpa character varying(191),
    ccap_core_name character varying(191),
    hubsite_name character varying(191),
    cm_no_service_group integer,
    dpa character varying(191),
    rpa1 character varying(191),
    rpa2 character varying(191),
    netelement_ccap_id bigint,
    state_changed_at character varying(191),
    cable_if character varying(191),
    internal_id character varying(191),
    service_template character varying(191)
);


ALTER TABLE nmsprime.rpd OWNER TO nmsprime;

--
-- Name: rpd_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.rpd_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.rpd_id_seq OWNER TO nmsprime;

--
-- Name: rpd_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.rpd_id_seq OWNED BY nmsprime.rpd.id;


--
-- Name: rpd_session; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.rpd_session (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    rpd_id bigint NOT NULL,
    session_id bigint,
    address_type integer,
    local_id integer,
    internal_id character varying(191),
    ip inet
);


ALTER TABLE nmsprime.rpd_session OWNER TO nmsprime;

--
-- Name: rpd_session_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.rpd_session_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.rpd_session_id_seq OWNER TO nmsprime;

--
-- Name: rpd_session_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.rpd_session_id_seq OWNED BY nmsprime.rpd_session.id;


--
-- Name: salesman; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.salesman (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    firstname character varying(191),
    lastname character varying(191),
    commission double precision,
    products character varying(191),
    description character varying(191)
);


ALTER TABLE nmsprime.salesman OWNER TO nmsprime;

--
-- Name: salesman_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.salesman_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.salesman_id_seq OWNER TO nmsprime;

--
-- Name: salesman_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.salesman_id_seq OWNED BY nmsprime.salesman.id;


--
-- Name: scan_range; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.scan_range (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netelement_id integer NOT NULL,
    community_ro character varying(191),
    range cidr NOT NULL
);


ALTER TABLE nmsprime.scan_range OWNER TO nmsprime;

--
-- Name: scan_range_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.scan_range_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.scan_range_id_seq OWNER TO nmsprime;

--
-- Name: scan_range_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.scan_range_id_seq OWNED BY nmsprime.scan_range.id;


--
-- Name: sensor; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.sensor (
    id bigint NOT NULL,
    name character varying(191),
    status character varying(191),
    value numeric(12,2),
    unit character varying(191),
    core_element_type character varying(191) NOT NULL,
    core_element_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    cpu_util integer,
    buffer_util integer,
    fru_status character varying(191),
    serial_num character varying(191),
    revision character varying(191),
    part_num character varying(191),
    scale character varying(25),
    "precision" integer,
    internal_id character varying(191),
    rate integer
);


ALTER TABLE nmsprime.sensor OWNER TO nmsprime;

--
-- Name: sensor_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.sensor_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sensor_id_seq OWNER TO nmsprime;

--
-- Name: sensor_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.sensor_id_seq OWNED BY nmsprime.sensor.id;


--
-- Name: sepaaccount; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.sepaaccount (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    holder character varying(191),
    creditorid character varying(35),
    iban character varying(34),
    bic character varying(11),
    institute character varying(191),
    company_id bigint,
    invoice_headline character varying(191),
    invoice_text character varying(191),
    invoice_text_negativ character varying(191),
    invoice_text_sepa character varying(191),
    invoice_text_sepa_negativ character varying(191),
    template_invoice character varying(191),
    template_cdr character varying(191),
    description character varying(191),
    invoice_nr_start bigint
);


ALTER TABLE nmsprime.sepaaccount OWNER TO nmsprime;

--
-- Name: sepaaccount_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.sepaaccount_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepaaccount_id_seq OWNER TO nmsprime;

--
-- Name: sepaaccount_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.sepaaccount_id_seq OWNED BY nmsprime.sepaaccount.id;


--
-- Name: sepamandate; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.sepamandate (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    contract_id bigint,
    reference character varying(191),
    signature_date date,
    holder character varying(191),
    iban character varying(34),
    bic character varying(11),
    institute character varying(191),
    valid_from date,
    valid_to date,
    state nmsprime.sepamandate_state,
    costcenter_id bigint,
    disable boolean,
    description text
);


ALTER TABLE nmsprime.sepamandate OWNER TO nmsprime;

--
-- Name: sepamandate_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.sepamandate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepamandate_id_seq OWNER TO nmsprime;

--
-- Name: sepamandate_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.sepamandate_id_seq OWNED BY nmsprime.sepamandate.id;


--
-- Name: settlementrun; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.settlementrun (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    executed_at timestamp with time zone,
    uploaded_at timestamp with time zone,
    year smallint,
    month smallint,
    path character varying(191),
    description character varying(191),
    verified boolean,
    fullrun boolean
);


ALTER TABLE nmsprime.settlementrun OWNER TO nmsprime;

--
-- Name: settlementrun_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.settlementrun_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.settlementrun_id_seq OWNER TO nmsprime;

--
-- Name: settlementrun_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.settlementrun_id_seq OWNED BY nmsprime.settlementrun.id;


--
-- Name: sla; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.sla (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    license character varying(191)
);


ALTER TABLE nmsprime.sla OWNER TO nmsprime;

--
-- Name: sla_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.sla_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sla_id_seq OWNER TO nmsprime;

--
-- Name: sla_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.sla_id_seq OWNED BY nmsprime.sla.id;


--
-- Name: smartont; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.smartont (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    default_service_name character varying(191),
    default_service_id character varying(191),
    default_contact_first_name character varying(191),
    default_contact_last_name character varying(191),
    default_contact_company character varying(191),
    default_contact_phone character varying(191),
    default_boc_label character varying(191),
    default_configfile_id integer,
    default_qos_id integer,
    default_mgmt_qos_id integer
);


ALTER TABLE nmsprime.smartont OWNER TO nmsprime;

--
-- Name: smartont_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.smartont_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.smartont_id_seq OWNER TO nmsprime;

--
-- Name: smartont_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.smartont_id_seq OWNED BY nmsprime.smartont.id;


--
-- Name: supportrequest; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.supportrequest (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    category character varying(191),
    priority character varying(191),
    mail character varying(191),
    phone character varying(191),
    text character varying(191),
    sla_name character varying(191)
);


ALTER TABLE nmsprime.supportrequest OWNER TO nmsprime;

--
-- Name: supportrequest_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.supportrequest_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.supportrequest_id_seq OWNER TO nmsprime;

--
-- Name: supportrequest_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.supportrequest_id_seq OWNED BY nmsprime.supportrequest.id;


--
-- Name: temperature; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.temperature (
    id bigint NOT NULL,
    internal_id integer NOT NULL,
    name character varying(50) NOT NULL,
    status character varying(20) NOT NULL,
    value numeric(4,2) NOT NULL,
    threshold numeric(5,2) NOT NULL,
    core_element_type character varying(191) NOT NULL,
    core_element_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE nmsprime.temperature OWNER TO nmsprime;

--
-- Name: temperature_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.temperature_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.temperature_id_seq OWNER TO nmsprime;

--
-- Name: temperature_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.temperature_id_seq OWNED BY nmsprime.temperature.id;


--
-- Name: ticket; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ticket (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    description text,
    user_id bigint,
    state character varying(191),
    priority nmsprime.ticket_priority,
    duedate timestamp with time zone,
    ticketable_type character varying(191),
    ticketable_id numeric,
    started_at timestamp with time zone,
    finished_at timestamp with time zone
);


ALTER TABLE nmsprime.ticket OWNER TO nmsprime;

--
-- Name: ticket_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_id_seq OWNER TO nmsprime;

--
-- Name: ticket_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ticket_id_seq OWNED BY nmsprime.ticket.id;


--
-- Name: ticket_type; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ticket_type (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    parent_id bigint,
    description character varying(191)
);


ALTER TABLE nmsprime.ticket_type OWNER TO nmsprime;

--
-- Name: ticket_type_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ticket_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_id_seq OWNER TO nmsprime;

--
-- Name: ticket_type_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ticket_type_id_seq OWNED BY nmsprime.ticket_type.id;


--
-- Name: ticket_type_ticket; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ticket_type_ticket (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    ticket_type_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_type_ticket OWNER TO nmsprime;

--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ticket_type_ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_ticket_id_seq OWNER TO nmsprime;

--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ticket_type_ticket_id_seq OWNED BY nmsprime.ticket_type_ticket.id;


--
-- Name: ticket_user; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ticket_user (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    user_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_user OWNER TO nmsprime;

--
-- Name: ticket_user_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ticket_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_user_id_seq OWNER TO nmsprime;

--
-- Name: ticket_user_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ticket_user_id_seq OWNED BY nmsprime.ticket_user.id;


--
-- Name: ticketsystem; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.ticketsystem (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    noreplymail character varying(191),
    noreplyname character varying(191),
    distance bigint DEFAULT '1'::bigint,
    modemcount bigint DEFAULT '1'::bigint,
    opentickets bigint DEFAULT '1'::bigint
);


ALTER TABLE nmsprime.ticketsystem OWNER TO nmsprime;

--
-- Name: ticketsystem_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.ticketsystem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticketsystem_id_seq OWNER TO nmsprime;

--
-- Name: ticketsystem_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.ticketsystem_id_seq OWNED BY nmsprime.ticketsystem.id;


--
-- Name: trcclass; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.trcclass (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    trc_id bigint,
    trc_short character varying(191),
    trc_description character varying(191)
);


ALTER TABLE nmsprime.trcclass OWNER TO nmsprime;

--
-- Name: trcclass_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.trcclass_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.trcclass_id_seq OWNER TO nmsprime;

--
-- Name: trcclass_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.trcclass_id_seq OWNED BY nmsprime.trcclass.id;


--
-- Name: users; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.users (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    first_name character varying(191),
    last_name character varying(191),
    email character varying(191),
    phonenumber character varying(191),
    login_name character varying(191),
    password character varying(60),
    api_token character varying(80),
    description character varying(191),
    active boolean DEFAULT true,
    remember_token character varying(100),
    language character varying(191) DEFAULT 'en'::character varying,
    last_login_at timestamp with time zone,
    password_changed_at timestamp with time zone,
    initial_dashboard character varying(191),
    geopos_updated_at timestamp with time zone,
    lng numeric(9,6),
    lat numeric(9,6),
    hastruck boolean DEFAULT false,
    theme_color character varying(50) DEFAULT 'default_theme_config.css'::character varying NOT NULL
);


ALTER TABLE nmsprime.users OWNER TO nmsprime;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.users_id_seq OWNER TO nmsprime;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.users_id_seq OWNED BY nmsprime.users.id;


--
-- Name: voipmon; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.voipmon (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    delete_record_interval integer DEFAULT 14 NOT NULL
);


ALTER TABLE nmsprime.voipmon OWNER TO nmsprime;

--
-- Name: voipmon_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.voipmon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.voipmon_id_seq OWNER TO nmsprime;

--
-- Name: voipmon_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.voipmon_id_seq OWNED BY nmsprime.voipmon.id;


--
-- Name: websockets_statistics_entries; Type: TABLE; Schema: nmsprime; Owner: nmsprime
--

CREATE TABLE nmsprime.websockets_statistics_entries (
    id bigint NOT NULL,
    app_id character varying(191),
    peak_connection_count bigint,
    websocket_message_count bigint,
    api_message_count bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.websockets_statistics_entries OWNER TO nmsprime;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: nmsprime
--

CREATE SEQUENCE nmsprime.websockets_statistics_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.websockets_statistics_entries_id_seq OWNER TO nmsprime;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: nmsprime
--

ALTER SEQUENCE nmsprime.websockets_statistics_entries_id_seq OWNED BY nmsprime.websockets_statistics_entries.id;


--
-- Name: abilities id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.abilities ALTER COLUMN id SET DEFAULT nextval('nmsprime.abilities_id_seq'::regclass);


--
-- Name: accountingrecord id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.accountingrecord ALTER COLUMN id SET DEFAULT nextval('nmsprime.accountingrecord_id_seq'::regclass);


--
-- Name: address id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.address ALTER COLUMN id SET DEFAULT nextval('nmsprime.address_id_seq'::regclass);


--
-- Name: alarm id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.alarm ALTER COLUMN id SET DEFAULT nextval('nmsprime.alarm_id_seq'::regclass);


--
-- Name: apartment id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.apartment ALTER COLUMN id SET DEFAULT nextval('nmsprime.apartment_id_seq'::regclass);


--
-- Name: arp id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.arp ALTER COLUMN id SET DEFAULT nextval('nmsprime.arp_id_seq'::regclass);


--
-- Name: billingbase id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.billingbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.billingbase_id_seq'::regclass);


--
-- Name: carriercode id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.carriercode ALTER COLUMN id SET DEFAULT nextval('nmsprime.carriercode_id_seq'::regclass);


--
-- Name: ccap id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ccap ALTER COLUMN id SET DEFAULT nextval('nmsprime.ccap_id_seq'::regclass);


--
-- Name: ccc id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ccc ALTER COLUMN id SET DEFAULT nextval('nmsprime.ccc_id_seq'::regclass);


--
-- Name: comment id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.comment ALTER COLUMN id SET DEFAULT nextval('nmsprime.comment_id_seq'::regclass);


--
-- Name: company id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.company ALTER COLUMN id SET DEFAULT nextval('nmsprime.company_id_seq'::regclass);


--
-- Name: configfile id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.configfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.configfile_id_seq'::regclass);


--
-- Name: contact id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.contact ALTER COLUMN id SET DEFAULT nextval('nmsprime.contact_id_seq'::regclass);


--
-- Name: contract id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.contract ALTER COLUMN id SET DEFAULT nextval('nmsprime.contract_id_seq'::regclass);


--
-- Name: coremon id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.coremon ALTER COLUMN id SET DEFAULT nextval('nmsprime.coremon_id_seq'::regclass);


--
-- Name: costcenter id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.costcenter ALTER COLUMN id SET DEFAULT nextval('nmsprime.costcenter_id_seq'::regclass);


--
-- Name: cpe id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.cpe ALTER COLUMN id SET DEFAULT nextval('nmsprime.cpe_id_seq'::regclass);


--
-- Name: debt id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.debt ALTER COLUMN id SET DEFAULT nextval('nmsprime.debt_id_seq'::regclass);


--
-- Name: dfsubscription id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dfsubscription ALTER COLUMN id SET DEFAULT nextval('nmsprime.dfsubscription_id_seq'::regclass);


--
-- Name: dfsubscriptionevent id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dfsubscriptionevent ALTER COLUMN id SET DEFAULT nextval('nmsprime.dfsubscriptionevent_id_seq'::regclass);


--
-- Name: domain id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.domain ALTER COLUMN id SET DEFAULT nextval('nmsprime.domain_id_seq'::regclass);


--
-- Name: dpa id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dpa ALTER COLUMN id SET DEFAULT nextval('nmsprime.dpa_id_seq'::regclass);


--
-- Name: dpic_card id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dpic_card ALTER COLUMN id SET DEFAULT nextval('nmsprime.dpic_card_id_seq'::regclass);


--
-- Name: ekpcode id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ekpcode ALTER COLUMN id SET DEFAULT nextval('nmsprime.ekpcode_id_seq'::regclass);


--
-- Name: endpoint id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.endpoint ALTER COLUMN id SET DEFAULT nextval('nmsprime.endpoint_id_seq'::regclass);


--
-- Name: enviacontract id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviacontract ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviacontract_id_seq'::regclass);


--
-- Name: enviaorder id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorder ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_id_seq'::regclass);


--
-- Name: enviaorder_phonenumber id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorder_phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_phonenumber_id_seq'::regclass);


--
-- Name: enviaorderdocument id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorderdocument ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorderdocument_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.failed_jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.failed_jobs_id_seq'::regclass);


--
-- Name: favorite_netelements id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.favorite_netelements ALTER COLUMN id SET DEFAULT nextval('nmsprime.favorite_netelements_id_seq'::regclass);


--
-- Name: fiber_node id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.fiber_node ALTER COLUMN id SET DEFAULT nextval('nmsprime.fiber_node_id_seq'::regclass);


--
-- Name: fiber_node_rpd id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.fiber_node_rpd ALTER COLUMN id SET DEFAULT nextval('nmsprime.fiber_node_rpd_id_seq'::regclass);


--
-- Name: global_config id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.global_config ALTER COLUMN id SET DEFAULT nextval('nmsprime.global_config_id_seq'::regclass);


--
-- Name: guilog id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.guilog ALTER COLUMN id SET DEFAULT nextval('nmsprime.guilog_id_seq'::regclass);


--
-- Name: hfcreq id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.hfcreq ALTER COLUMN id SET DEFAULT nextval('nmsprime.hfcreq_id_seq'::regclass);


--
-- Name: hubsite id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.hubsite ALTER COLUMN id SET DEFAULT nextval('nmsprime.hubsite_id_seq'::regclass);


--
-- Name: indices id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.indices ALTER COLUMN id SET DEFAULT nextval('nmsprime.indices_id_seq'::regclass);


--
-- Name: inventory id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.inventory ALTER COLUMN id SET DEFAULT nextval('nmsprime.inventory_id_seq'::regclass);


--
-- Name: invoice id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.invoice ALTER COLUMN id SET DEFAULT nextval('nmsprime.invoice_id_seq'::regclass);


--
-- Name: ippool id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ippool ALTER COLUMN id SET DEFAULT nextval('nmsprime.ippool_id_seq'::regclass);


--
-- Name: item id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.item ALTER COLUMN id SET DEFAULT nextval('nmsprime.item_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.jobs_id_seq'::regclass);


--
-- Name: l2tp id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.l2tp ALTER COLUMN id SET DEFAULT nextval('nmsprime.l2tp_id_seq'::regclass);


--
-- Name: link id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.link ALTER COLUMN id SET DEFAULT nextval('nmsprime.link_id_seq'::regclass);


--
-- Name: lldp id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.lldp ALTER COLUMN id SET DEFAULT nextval('nmsprime.lldp_id_seq'::regclass);


--
-- Name: market id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.market ALTER COLUMN id SET DEFAULT nextval('nmsprime.market_id_seq'::regclass);


--
-- Name: mibfile id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mibfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.mibfile_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.migrations ALTER COLUMN id SET DEFAULT nextval('nmsprime.migrations_id_seq'::regclass);


--
-- Name: modem id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.modem ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_id_seq'::regclass);


--
-- Name: modem_option id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.modem_option ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_option_id_seq'::regclass);


--
-- Name: mpr id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mpr ALTER COLUMN id SET DEFAULT nextval('nmsprime.mpr_id_seq'::regclass);


--
-- Name: mprgeopos id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mprgeopos ALTER COLUMN id SET DEFAULT nextval('nmsprime.mprgeopos_id_seq'::regclass);


--
-- Name: mta id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mta ALTER COLUMN id SET DEFAULT nextval('nmsprime.mta_id_seq'::regclass);


--
-- Name: ncs id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ncs ALTER COLUMN id SET DEFAULT nextval('nmsprime.ncs_id_seq'::regclass);


--
-- Name: net id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.net ALTER COLUMN id SET DEFAULT nextval('nmsprime.net_id_seq'::regclass);


--
-- Name: netelement id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelement ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelement_id_seq'::regclass);


--
-- Name: netelement_interface id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelement_interface ALTER COLUMN id SET DEFAULT nextval('nmsprime.dpic_interface_id_seq'::regclass);


--
-- Name: netelementtype id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelementtype ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelementtype_id_seq'::regclass);


--
-- Name: netgw id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netgw ALTER COLUMN id SET DEFAULT nextval('nmsprime.netgw_id_seq'::regclass);


--
-- Name: node id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.node ALTER COLUMN id SET DEFAULT nextval('nmsprime.node_id_seq'::regclass);


--
-- Name: numberrange id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.numberrange ALTER COLUMN id SET DEFAULT nextval('nmsprime.numberrange_id_seq'::regclass);


--
-- Name: oid id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.oid ALTER COLUMN id SET DEFAULT nextval('nmsprime.oid_id_seq'::regclass);


--
-- Name: oui id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.oui ALTER COLUMN id SET DEFAULT nextval('nmsprime.oui_id_seq'::regclass);


--
-- Name: overduedebts id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.overduedebts ALTER COLUMN id SET DEFAULT nextval('nmsprime.overduedebts_id_seq'::regclass);


--
-- Name: parameter id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.parameter ALTER COLUMN id SET DEFAULT nextval('nmsprime.parameter_id_seq'::regclass);


--
-- Name: phonebookentry id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonebookentry ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonebookentry_id_seq'::regclass);


--
-- Name: phonenumber id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumber_id_seq'::regclass);


--
-- Name: phonenumbermanagement id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonenumbermanagement ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumbermanagement_id_seq'::regclass);


--
-- Name: phonetariff id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonetariff ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonetariff_id_seq'::regclass);


--
-- Name: product id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.product ALTER COLUMN id SET DEFAULT nextval('nmsprime.product_id_seq'::regclass);


--
-- Name: provbase id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.provbase_id_seq'::regclass);


--
-- Name: provmon id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provmon ALTER COLUMN id SET DEFAULT nextval('nmsprime.provmon_id_seq'::regclass);


--
-- Name: provvoip id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provvoip ALTER COLUMN id SET DEFAULT nextval('nmsprime.provvoip_id_seq'::regclass);


--
-- Name: qos id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.qos ALTER COLUMN id SET DEFAULT nextval('nmsprime.qos_id_seq'::regclass);


--
-- Name: realty id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.realty ALTER COLUMN id SET DEFAULT nextval('nmsprime.realty_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.roles ALTER COLUMN id SET DEFAULT nextval('nmsprime.roles_id_seq'::regclass);


--
-- Name: rpa id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpa ALTER COLUMN id SET DEFAULT nextval('nmsprime.rpa_id_seq'::regclass);


--
-- Name: rpd id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd ALTER COLUMN id SET DEFAULT nextval('nmsprime.rpd_id_seq'::regclass);


--
-- Name: rpd_session id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd_session ALTER COLUMN id SET DEFAULT nextval('nmsprime.rpd_session_id_seq'::regclass);


--
-- Name: salesman id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.salesman ALTER COLUMN id SET DEFAULT nextval('nmsprime.salesman_id_seq'::regclass);


--
-- Name: scan_range id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.scan_range ALTER COLUMN id SET DEFAULT nextval('nmsprime.scan_range_id_seq'::regclass);


--
-- Name: sensor id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sensor ALTER COLUMN id SET DEFAULT nextval('nmsprime.sensor_id_seq'::regclass);


--
-- Name: sepaaccount id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sepaaccount ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepaaccount_id_seq'::regclass);


--
-- Name: sepamandate id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sepamandate ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepamandate_id_seq'::regclass);


--
-- Name: settlementrun id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.settlementrun ALTER COLUMN id SET DEFAULT nextval('nmsprime.settlementrun_id_seq'::regclass);


--
-- Name: sla id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sla ALTER COLUMN id SET DEFAULT nextval('nmsprime.sla_id_seq'::regclass);


--
-- Name: smartont id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.smartont ALTER COLUMN id SET DEFAULT nextval('nmsprime.smartont_id_seq'::regclass);


--
-- Name: supportrequest id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.supportrequest ALTER COLUMN id SET DEFAULT nextval('nmsprime.supportrequest_id_seq'::regclass);


--
-- Name: temperature id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.temperature ALTER COLUMN id SET DEFAULT nextval('nmsprime.temperature_id_seq'::regclass);


--
-- Name: ticket id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_id_seq'::regclass);


--
-- Name: ticket_type id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_type ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_id_seq'::regclass);


--
-- Name: ticket_type_ticket id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_type_ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_ticket_id_seq'::regclass);


--
-- Name: ticket_user id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_user ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_user_id_seq'::regclass);


--
-- Name: ticketsystem id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticketsystem ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticketsystem_id_seq'::regclass);


--
-- Name: trcclass id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.trcclass ALTER COLUMN id SET DEFAULT nextval('nmsprime.trcclass_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.users ALTER COLUMN id SET DEFAULT nextval('nmsprime.users_id_seq'::regclass);


--
-- Name: voipmon id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.voipmon ALTER COLUMN id SET DEFAULT nextval('nmsprime.voipmon_id_seq'::regclass);


--
-- Name: websockets_statistics_entries id; Type: DEFAULT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.websockets_statistics_entries ALTER COLUMN id SET DEFAULT nextval('nmsprime.websockets_statistics_entries_id_seq'::regclass);


--
-- Data for Name: abilities; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.abilities (id, name, title, entity_id, entity_type, only_owned, scope, created_at, updated_at, deleted_at) FROM stdin;
1	*	Manage users	\N	App\\User	t	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
2	*	All abilities	\N	*	f	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
3	view	View everything	\N	*	f	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
4	use api	Use api	\N	\N	f	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
5	see income chart	See income chart	\N	\N	f	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
6	*	Manage roles	\N	roles	f	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
7	view_analysis_pages_of	View analysis pages of everything	\N	*	f	\N	2022-05-06 12:25:07+02	2022-05-06 12:25:07+02	\N
8	*	Manage global configs	\N	App\\GlobalConfig	f	\N	2022-05-06 12:25:13+02	2022-05-06 12:25:13+02	\N
9	view	View gui logs	\N	App\\GuiLog	f	\N	2022-05-06 12:25:13+02	2022-05-06 12:25:13+02	\N
53	view_analysis_pages_of	View analysis pages of modems	\N	Modules\\ProvBase\\Entities\\Modem	f	\N	2022-05-06 12:29:18+02	2022-05-06 12:29:18+02	\N
54	view_analysis_pages_of	View analysis pages of netgw	\N	Modules\\ProvBase\\Entities\\NetGw	f	\N	2022-05-06 12:29:18+02	2022-05-06 12:29:18+02	\N
55	download	Download settlement runs	\N	Modules\\BillingBase\\Entities\\SettlementRun	f	\N	2022-05-06 12:29:18+02	2022-05-06 12:29:18+02	\N
\.


--
-- Data for Name: accountingrecord; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.accountingrecord (id, created_at, updated_at, deleted_at, contract_id, name, product_id, ratio, count, charge, sepaaccount_id, invoice_nr, settlementrun_id) FROM stdin;
\.


--
-- Data for Name: address; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.address (id, created_at, updated_at, deleted_at, lat, lng, source, district, zip, city, street, house_number) FROM stdin;
\.


--
-- Data for Name: alarm; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.alarm (id, created_at, updated_at, deleted_at, status, agent_host, alertname, host, instance, job, severity, annotations, "startsAt", "endsAt", "generatorURL", fingerprint, netelement_id) FROM stdin;
\.


--
-- Data for Name: apartment; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.apartment (id, created_at, updated_at, deleted_at, realty_id, number, floor, connected, occupied, description, connection_type, code) FROM stdin;
\.


--
-- Data for Name: arp; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.arp (id, created_at, updated_at, deleted_at, type, interface, name, mac, ip, netelement_id) FROM stdin;
\.


--
-- Data for Name: assigned_roles; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.assigned_roles (role_id, entity_id, entity_type, scope, created_at, updated_at) FROM stdin;
1	1	App\\User	\N	\N	\N
2	1	App\\User	\N	\N	\N
\.


--
-- Data for Name: authreminders; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.authreminders (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: billingbase; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.billingbase (id, created_at, updated_at, deleted_at, rcd, currency, tax, mandate_ref_template, split, termination_fix, userlang, cdr_offset, voip_extracharge_default, voip_extracharge_mobile_national, cdr_retention_period, fluid_valid_dates, show_ags, adapt_item_start) FROM stdin;
1	\N	\N	\N	0	EUR	19	\N	f	f	de	1	0	0	6	f	f	f
\.


--
-- Data for Name: carriercode; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.carriercode (id, created_at, updated_at, deleted_at, carrier_code, company) FROM stdin;
1	\N	\N	\N	0	-
2	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D001	Telekom Deutschland GmbH
3	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D003	multiConnect GmbH
4	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D004	NETCOLOGNE GmbH
5	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D007	M-net Telekommunikations GmbH
6	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D008	Verizon Deutschland GmbH
7	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D009	Vodafone GmbH
8	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D011	1&1 Versatel Deutschland GmbH
9	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D012	BT (Germany) GmbH & Co. OHG
10	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D013	EWE TEL GmbH
11	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D017	Colt Technology Services GmbH
12	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D019	Telefonica Germany GmbH & Co. OHG
13	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D020	dtms GmbH
14	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D021	01024 Telefondienste GmbH
15	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D022	Communication Services TELE2 GmbH
16	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D023	HeLi NET Telekommunikation GmbH & Co. KG
17	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D025	IN-telegence GmbH
18	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D027	Ventelo GmbH
19	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D028	3U Telecom GmbH
20	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D030	Younip Telecom GmbH
21	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D031	EWE TEL GmbH
22	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D032	DOKOM Gesellschaft für Telekommunikation mbH
23	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D033	01049 GmbH
24	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D034	11 88 0 Solutions AG
25	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D035	BITel Gesellschaft für Telekommunikation mbH
26	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D037	WOBCOM GmbH Wolfsburg für Telekommunikation und Dienstleistungen
27	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D038	TNG Stadtnetz GmbH
28	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D040	Westend Management GmbH
29	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D043	M-net Telekommunikations GmbH
30	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D045	HLkomm Telekommunikations GmbH
31	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D046	envia TEL GmbH
32	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D047	01051 Telecom GmbH
33	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D048	EWE TEL GmbH
34	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D050	htp GmbH
35	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D051	Callax Telecom Services GmbH
36	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D052	M-net Telekommunikations GmbH
37	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D053	Breitbandkabelgesellschaft mbH
38	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D054	NETAACHEN GmbH
39	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D056	Vodafone GmbH
40	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D057	envia TEL GmbH
41	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D058	Carrier 1 International AG
42	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D061	Telefonica Germany GmbH & Co. OHG
43	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D063	Kube & Au GmbH
44	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D065	Telefonica Germany GmbH & Co. OHG
45	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D070	First Communication GmbH
46	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D071	Netcom Kassel GmbH
47	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D072	ENTEGA Medianet GmbH
48	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D073	dtms GmbH
49	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D074	DATA CMR GmbH & Co.KG
50	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D075	Median Telecom GmbH
51	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D076	Stadtwerke Schwedt GmbH
52	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D078	Vodafone GmbH
53	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D081	Daten- und Telekommunikations-GmbH Dessau
54	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D084	VSE NET GmbH
55	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D085	EWE TEL GmbH
56	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D086	Mega Communications GmbH
57	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D087	TeleNEC Telekommunikation Neustadt GmbH & Co.KG
58	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D088	010023 GmbH
59	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D089	EWE TEL GmbH
60	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D090	010023 GmbH
61	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D093	1&1 Versatel Deutschland GmbH
62	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D094	Spider Telecom GmbH
63	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D095	MPA NET Gesellschaft für Telekommunikation mbH
64	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D097	multiConnect GmbH
65	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D098	GöTel GmbH
66	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D100	Goodlines GmbH
67	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D101	wilhelm.tel GmbH
68	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D104	OneTel Telecommunication GmbH
69	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D105	FirstMark Communications Deutschland GmbH
70	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D106	smart-DSL GmbH
71	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D107	3U Telecom GmbH
72	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D108	mr. next id GmbH & Co.KG
73	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D109	Thüringer Netkom GmbH
74	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D113	inopla GmbH
75	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D114	Deutsche Telefon Standard AG
76	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D115	MDCC Magdeburg City-Com GmbH
77	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D117	HFO Telecom Vertriebs GmbH
78	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D118	EXACOR GmbH
79	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D120	Vodafone NRW GmbH
80	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D121	TELTA Citynetz Eberswalde GmbH
81	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D122	01018 GmbH
82	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D123	Plusnet GmbH
83	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D124	Telekom Deutschland GmbH
84	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D125	nexnet GmbH
85	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D127	Vodafone BW GmbH
86	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D129	01081 Telecom GmbH
87	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D130	Maestro Telecom GmbH
88	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D131	CALLAX Holding GmbH
89	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D133	PFALZKOM GmbH
90	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D134	01071 Telecom GmbH
91	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D135	Callax Telecom Services GmbH
92	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D137	Broadnet Services GmbH
93	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D138	01066 GmbH
94	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D139	01059 GmbH
95	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D141	envito GmbH & Co.KG
96	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D142	net services GmbH & Co. KG
97	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D143	First Telecom GmbH
98	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D144	sdt.net AG
99	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D145	Callax Telecom Services GmbH
100	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D146	[netzquadrat] Gesellschaft für Telekommunikation mbH
101	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D147	Maestro Telecom GmbH
102	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D148	01081 Telecom GmbH
103	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D150	Telekom Deutschland GmbH
104	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D153	amplus AG
105	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D154	PLANinterNET VoIP-GmbH
106	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D155	TNG Stadtnetz GmbH
107	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D156	one4one Services GmbH
108	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D157	Kabelfernsehen München
109	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D158	Telogic Germany GmbH
110	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D159	ccn corporate communication networks GmbH
111	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D160	PGmedia Telecom GmbH
112	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D163	dus.net GmbH
113	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D164	Callax Telecom Services GmbH
114	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D166	Callax Telecom Services GmbH
115	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D167	DNS:Net Internet Service GmbH
116	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D168	bn:t Blatzheim Networks Telecom GmbH
117	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D170	01057 Protel GmbH
118	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D171	toplink GmbH
119	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D172	Callax Telecom Services GmbH
120	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D173	Callax Telecom Services GmbH
121	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D174	T-Systems Business Services GmbH
122	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D175	Callax Telecom Services GmbH
123	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D176	01051 Telecom GmbH
124	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D178	MK Netzdienste GmbH & Co. KG
125	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D179	Callax Telecom Services GmbH
126	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D180	Outbox AG
127	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D181	Callax Telecom Services GmbH
128	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D182	DOKOM Gesellschaft für Telekommunikation mbH
129	2022-05-06 12:26:07+02	2022-05-06 12:26:07+02	\N	D183	Discount Telecom S&V GmbH
130	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D184	Forester GmbH
131	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D185	01081 Telecom GmbH
132	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D186	TeleSon Vertriebs GmbH
133	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D188	Inocom GmbH
134	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D189	PURtel.com GmbH
135	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D190	economore GmbH & Co. KG
136	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D191	Vodafone Kabel Deutschland GmbH
137	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D193	YIPL OHG
138	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D194	G-FIT Gesellschaft für innovativeTelekommunikationsdienste mbH & Co. KG
139	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D195	01071 Telecom GmbH
140	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D197	vitroconnect systems GmbH
141	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D198	Callax Telecom Services GmbH
142	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D199	010091 UG (haftungsbeschränkt)
143	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D201	1&1 Telecom GmbH
144	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D202	inexio Informationstechnologie und Telekommunikation GmbH66740 Saarlouis
145	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D204	eco Service GmbH
146	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D205	Truphone GmbH
147	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D206	LineCall Telecom GmbH
148	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D207	OpenNumbers GmbH
149	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D208	Orange Business Germany GmbH
150	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D209	Televersa Online GmbH
151	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D210	01085 GmbH
152	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D211	Fonfriends Telecom GmbH
153	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D212	AS-Infodienste GmbH
154	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D213	SNT Greifswald GmbH
155	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D214	VSE NET GmbH
156	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D215	OVH GmbH
157	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D216	meetyoo conferencing GmbH
158	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D217	Smart Products GmbH
159	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D218	[netzquadrat] Gesellschaft für Telekommunikation mbH
160	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D219	Teleservice Company GmbH
161	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D220	VOXBONE SA
162	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D222	IN-telegence GmbH
163	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D223	42one GmbH
164	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D224	wel.de Gesellschaft für Informationsdienste mbh
165	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D225	TeleVita Kommunikationsdienste GmbH (i.Gr.)
166	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D226	Spider Telecom GmbH
167	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D228	Verizon Deutschland GmbH
168	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D229	11 88 0 Solutions AG
169	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D230	01018 GmbH
170	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D231	Enghouse Networks (Germany) GmbH
171	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D232	11 88 0 Internet Services AG
172	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D233	11 88 0 Internet Services AG
173	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D235	M-net Telekommunikations GmbH
174	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D239	MEGA MOBILES GmbH
175	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D240	Equada GmbH
176	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D241	Colt Technology Services GmbH
177	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D242	Alnitak GmbH
178	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D244	IKTel UG
179	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D245	Gossip GmbH
180	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D246	Kube & Au GmbH
181	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D247	010091 UG (haftungsbeschränkt)
182	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D248	[netzquadrat] Gesellschaft für Telekommunikation mbH
183	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D249	Lycamobile Europe Ltd., London, United KingdomEmpfangsbevollmächtigter: Dr. Peter Schmitz, JUCONOMY RA, Mörsenbroicher Weg 200
184	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D250	Teleflash GmbH
185	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D251	Dritte Hansestar GmbH
186	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D252	OpenNumbers GmbH
187	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D253	talkyou telecom UG (haftungsbeschränkt)
188	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D254	01073 GmbH
189	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D257	Younip Telecom GmbH
190	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D258	LineCall GmbH
191	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D259	KADSOFT Computer GmbH Freital Kommunikation-Automation-Datentechnik
192	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D260	mr. next id GmbH & Co.KG
193	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D261	Sipgate Wireless GmbH
194	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D262	just digits GmbH
195	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D263	inexio Informationstechnologie und Telekommunikation GmbH66740 Saarlouis
196	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D264	01048 Telecom GmbH
197	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D265	Northern Access GmbH
198	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D266	mr. communication GmbH
199	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D267	HLkomm Telekommunikations GmbH
200	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D268	MK Netzdienste GmbH & Co. KG
201	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D270	G-FIT Gesellschaft für innovativeTelekommunikationsdienste mbH & Co. KG
202	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D271	Outbox AG
203	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D273	First Telecom GmbH
204	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D274	First Communication GmbH
205	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D275	FPS InformationsSysteme GmbH
206	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D276	Herzo Media GmbH & Co.KG
207	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D277	EXACOR GmbH
208	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D278	pop-interactive GmbH
209	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D279	Freikom Stefan Frech & Thorsten Reimer GbR
210	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D280	Ilm-Provider UG (haftungsbeschränkt)
211	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D281	TELE AG
212	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D282	dtms GmbH
213	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D283	wilhelm.tel GmbH
214	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D284	ACARA Telecom GmbH
215	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D286	01051 Telecom GmbH
216	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D287	ENTEGA Medianet GmbH
217	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D288	EPCAN GmbH
218	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D289	bn:t Blatzheim Networks Telecom GmbH
219	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D290	yuilop s.l.
220	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D291	Stadtwerke Schwedt GmbH
221	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D292	BT (Germany) GmbH & Co. OHG
222	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D294	SBC (Germany) GmbH & Co.KG
223	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D296	Marco Bungalski GmbH
224	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D297	Netcom Kassel GmbH
225	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D298	[netzquadrat] Gesellschaft für Telekommunikation mbH
226	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D299	GDV Dienstleistungsgesellschaft GmbH & Co.KG
227	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D301	Aleando GmbH
228	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D302	net-and-phone GmbH
229	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D303	Stadtnetz Bamberg Gesellschaft für Telekommunikation mbH
230	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D304	SCHREGO Communications LTD
231	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D306	argon networks UG
232	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D307	Senel, Mustafa, Bosporus Solutions
233	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D308	PrimaCom Berlin GmbH
234	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D309	SBC (Germany) GmbH & Co.KG
235	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D310	toplink GmbH
236	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D311	interactive digital media GmbH
237	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D312	M.I.T. Media Info Transfer GmbH
238	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D313	VOXBONE SA
239	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D314	01051 Telecom GmbH
240	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D315	01081 Telecom GmbH
241	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D316	01081 Telecom GmbH
242	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D317	Maestro Telecom GmbH
243	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D318	Maestro Telecom GmbH
244	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D319	01071 Telecom GmbH
245	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D320	01071 Telecom GmbH
246	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D321	htp GmbH
247	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D322	CARRIER-SERVICES.de GmbH
248	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D323	ecotel communication ag
249	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D326	Naka AG
250	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D327	Talk.to FZC
251	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D328	WOBCOM GmbH Wolfsburg für Telekommunikation und Dienstleistungen
252	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D329	TelcoVillage GmbH
253	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D330	willy tel GmbH
254	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D331	willy tel GmbH
255	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D333	Mobiquithings, Hagelsbergstraße 10
256	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D334	süc // dacor GmbH
257	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D335	Twilio Ireland Ltd.
258	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D336	ITS AG
259	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D337	Tismi BV
260	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D338	Sunak, Oktay e.K., ST-Technologie
261	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D339	CarDeluxe GmbH & Co.KG
262	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D340	MCN Telekommunikacios Szolgaltato Kortlatolt Felelösegü Tarsasag
263	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D341	TeleForte Telekommunikations AG
264	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D342	CALLAX Holding GmbH
265	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D343	Callax Telecom Services GmbH
266	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D344	Callax Telecom Services GmbH
267	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D345	Callax Telecom Services GmbH
268	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D346	Callax Telecom Services GmbH
269	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D347	Callax Telecom Services GmbH
270	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D348	Callax Telecom Services GmbH
271	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D349	Callax Telecom Services GmbH
272	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D350	Callax Telecom Services GmbH
273	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D351	Callax Telecom Services GmbH
274	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D352	Callax Telecom Services GmbH
275	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D353	BITel Gesellschaft für Telekommunikation mbH
276	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D354	Linea Service GmbH
277	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D355	Thüringer Netkom GmbH
278	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D356	Mango Office GmbH
279	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D358	Matelso GmbH
280	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D359	PLANinterNET VoIP-GmbH
281	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D360	MPA NET Gesellschaft für Telekommunikation mbH
282	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D363	PLANinterNET VoIP-GmbH
283	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D364	Nexiu GmbH
284	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D365	HeLi NET Telekommunikation GmbH & Co. KG
285	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D366	Viatel Deutschland GmbH
286	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D367	schnell-im-netz.de GmbH & Co. KG
287	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D368	PFALZKOM GmbH
288	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D369	TELE AG
289	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D370	multiConnect GmbH
290	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D371	SOCO Network Solutions GmbH
291	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D373	Axxess Solutions GmbH
292	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D374	Axxess Solutions GmbH
293	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D375	Net-Spacy IT-Services GmbH
294	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D376	Viatel Global Services Deutschland GmbH
295	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D377	Rottleb, Kai -KNT Internet-
296	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D378	Dolphin IT-Systeme e.K.
297	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D379	sdt.net AG
298	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D380	Rack, Michael Reiner -RSM Freilassing-
299	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D381	COMBILE GmbH Mobile System Builders
300	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D382	Westend Management GmbH
301	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D383	01072 GmbH
302	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D384	01072 GmbH
303	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D385	01073 GmbH
304	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D386	eSTART Telecom GmbH
305	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D387	eSTART Telecom GmbH
306	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D388	01049 GmbH
307	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D389	MyShop Services GmbH
308	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D390	01085 GmbH
309	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D391	01059 GmbH
310	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D392	01066 GmbH
311	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D393	MyShop Services GmbH
312	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D394	Tulp Solutions B.V.
313	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D395	meetyoo conferencing GmbH
314	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D396	VoiceON Telecom Services GmbH
315	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D397	Tele Columbus AG
316	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D399	SCHÖNENBERG-COMPUTER GmbH
317	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D400	GöTel GmbH
318	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D401	D & T Internet GmbH
319	2022-05-06 12:26:08+02	2022-05-06 12:26:08+02	\N	D402	RelAix Networks GmbH
320	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D403	Belgacom International Carrier Services SA
321	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D404	tenios GmbH
322	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D405	Via-Vox GmbH
323	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D406	Portunity GmbH
324	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D407	dtms GmbH
325	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D408	Data Networks Communication and Consulting GmbH
326	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D409	Drillisch Online AG
327	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D410	mobilcom-debitel GmbH
328	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D411	QuestNet GmbH
329	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D412	telkodata GmbH
330	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D413	Nord-IT Bremen UG (haftungsbeschränkt)
331	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D414	walter cloud services GmbH
332	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D415	QTELO GmbH
333	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D416	QTELO GmbH
334	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D417	1N Telecom GmbH
335	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D418	Stadtwerke Bochum GmbH
336	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D419	MiSaxNet GmbH
337	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D420	Marco Bungalski GmbH
338	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D421	Local Exchange Global Operation Services (LEGOS)
339	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D422	NX4 Networks GmbH
340	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D423	Deutsche Glasfaser Wholesale GmbH
341	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D424	TRILUCOM Holding S.A.
342	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D425	Stahlberg, Alexander
343	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D426	nfon AG
344	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D427	GGEW net GmbH
345	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D428	NU Informationssysteme GmbH
346	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D429	WEMAG AG
347	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D430	autphone GmbH
348	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D431	AlphaCron Datensysteme, Inh. Marc Pauls
349	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D432	Intersaar GmbH
350	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D433	Brandl Services GmbH
351	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D434	42com Telecommunication GmbH
352	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D435	Stadtwerke Bühl GmbH
353	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D436	DIABOLOCOM GmbH, c/o Pramex International GmbH
354	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D437	Weissblau-breitband UG (haftungsbeschränkt)
355	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D438	NL Bandwidth B.V.
356	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D439	Macrogate IP Systems GmbH
357	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D440	New Fiber Networks GmbH
358	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D441	Stiegeler Internet Service GmbH
359	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D442	HDone GmbH
360	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D443	init.voice GmbH
361	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D444	VoiceON Telecom Services GmbH
362	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D445	TeleData GmbH
363	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D446	KEVAG Telekom GmbH
364	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D447	PHILUNET GmbH
365	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D449	42one GmbH
366	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D450	Tecsee GmbH
367	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	D999	Bundesnetzagentur
\.


--
-- Data for Name: ccap; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ccap (id, created_at, updated_at, deleted_at, netelement_id, cms, mtas, dsgs, rpds, dpa1_links_overutilized, dpa2_links_overutilized, redundancy, uptime, sw_ver, config_compliance, serial, cms_offline, mtas_offline, rpds_offline, stbs_offline, device_type, model, city, site, cpe_device_count) FROM stdin;
\.


--
-- Data for Name: ccc; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ccc (id, created_at, updated_at, deleted_at, template_filename, headline1, headline2, language, logo, speedtest_url, bgimg) FROM stdin;
1	\N	\N	\N	\N	\N	\N	en	\N	\N	\N
\.


--
-- Data for Name: comment; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.comment (id, created_at, updated_at, deleted_at, user_id, ticket_id, comment) FROM stdin;
\.


--
-- Data for Name: company; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.company (id, created_at, updated_at, deleted_at, name, street, zip, city, phone, fax, web, mail, registration_court_1, registration_court_2, registration_court_3, management, directorate, tax_id_nr, tax_nr, transfer_reason, logo, conn_info_template_fn) FROM stdin;
1	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	NMS Prime	Dörfelstraße 7	09496	Marienberg	\N	\N	www.nmsprime.com	support@nmsprime.com	\N	\N	\N	\N	\N	\N	\N	\N	nmsprime.pdf	default_coninfo.tex
\.


--
-- Data for Name: configfile; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.configfile (id, created_at, updated_at, deleted_at, name, text, device, public, parent_id, firmware, is_dummy, monitoring, dashboard, service_profile_id, is_multiservice_ont, tr069_profile_id, ont_line_profile_id) FROM stdin;
1	\N	\N	2022-05-06 12:25:36+02	dummy-cfg-cm	\N	cm	yes	\N	\N	t	\N	/grafana/d/3-42DM6Gk/cablemodem	\N	f	\N	\N
2	\N	\N	2022-05-06 12:25:36+02	dummy-cfg-mta	\N	mta	yes	\N	\N	t	\N	/grafana/d/3-42DM6Gk/cablemodem	\N	f	\N	\N
3	2023-04-19 17:51:27+02	2023-04-19 17:51:27+02	\N	Generic ONT	\N	ont	yes	\N		f	\N	\N	\N	f	\N	\N
\.


--
-- Data for Name: contact; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.contact (id, created_at, updated_at, deleted_at, firstname1, lastname1, firstname2, lastname2, company, tel, tel_private, email1, email2, street, house_nr, city, zip, district, administration, invoice_text1, invoice_text2, invoice_text3) FROM stdin;
\.


--
-- Data for Name: contract; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.contract (id, created_at, updated_at, deleted_at, number, number2, number3, number4, customer_external_id, company, department, salutation, academic_degree, firstname, lastname, street, house_number, zip, city, district, country_id, country_code, phone, fax, email, birthday, contract_start, contract_end, internet_access, purchase_tariff, next_purchase_tariff, qos_id, next_qos_id, voip_id, next_voip_id, sepa_iban, sepa_bic, sepa_holder, sepa_institute, create_invoice, login, password, net, cluster, description, costcenter_id, salesman_id, has_telephony, apartment_nr, additional, ground_for_dismissal, group_contract, contact, value_date, apartment_id, contact_id, last_amendment, lng, lat, geocode_source, sep_id, oto_id, oto_port, oto_socket_usage, oto_status, flat_id, alex_status, omdf_id, boc_label, bof_label, type) FROM stdin;
2	2023-04-19 16:56:24+02	2023-04-19 16:56:24+02	\N	\N	\N	\N	\N	\N	n/a	n/a	\N	\N	n/a	n/a	n/a	n/a	n/a	n/a	\N	\N		\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	“Storage” holding free ONT. Change parameters to your needs.	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	OTO_STORAGE
\.


--
-- Data for Name: coremon; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.coremon (id, updated_at, deleted_at, smartphy_url, apikey) FROM stdin;
1	2023-04-05 16:55:43+02	\N	\N	\N
\.


--
-- Data for Name: costcenter; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.costcenter (id, created_at, updated_at, deleted_at, name, number, sepaaccount_id, billing_month, description) FROM stdin;
1	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	NMS Prime	\N	1	6	\N
\.


--
-- Data for Name: cpe; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.cpe (id, created_at, updated_at, deleted_at, netelement_ccap_id, mac, ip, status, last_reg_time, last_mac_state, ds_data_service_grp, us_data_service_grp, device_class, core_ipv6, manufacturer_name, reg_ver, prim_sid, reg_priv, cable_if, internal_id, netelement_rpd_id) FROM stdin;
\.


--
-- Data for Name: debt; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.debt (id, created_at, updated_at, deleted_at, contract_id, sepamandate_id, invoice_id, date, amount, bank_fee, total_fee, description, number, voucher_nr, due_date, cleared, indicator, dunning_date, parent_id, missing_amount, extra_fee) FROM stdin;
\.


--
-- Data for Name: dfsubscription; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.dfsubscription (id, created_at, updated_at, deleted_at, service_name, service_id, contact_no, contact_first_name, contact_last_name, contact_company_name, contact_street, contact_street_no, contact_postal_code, contact_city, contact_country, contact_phone, contact_email, contact_notes, subscription_id, subscription_end_point_id, sf_sla, status, wishdate, switchdate, modificationdate, l1_handover_equipment_name, l1_handover_equipment_rack, l1_handover_equipment_slot, l1_handover_equipment_port, l1_breakout_cable, l1_breakout_fiber, alau_order_ref, note, contract_id) FROM stdin;
\.


--
-- Data for Name: dfsubscriptionevent; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.dfsubscriptionevent (id, created_at, updated_at, deleted_at, description, status, "timestamp", dfsubscription_id) FROM stdin;
\.


--
-- Data for Name: domain; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.domain (id, created_at, updated_at, deleted_at, name, alias, type) FROM stdin;
\.


--
-- Data for Name: dpa; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.dpa (id, created_at, updated_at, deleted_at, netelement_id, redundancy, cpu_utilization, memory_utilization, sys_uptime, serial_number, model, last_conf_change, sw_ver, city, site) FROM stdin;
\.


--
-- Data for Name: dpic_card; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.dpic_card (id, created_at, updated_at, deleted_at, sys_name, description, uptime, bandwidth, utilization, netelement_ccap_id, last_state_change_ms, internal_id, pid) FROM stdin;
\.


--
-- Data for Name: ekpcode; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ekpcode (id, created_at, updated_at, deleted_at, ekp_code, company) FROM stdin;
1	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/019	TELTA Citynetz GmbH
2	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/031	Orange Business Germany GmbH
3	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/035	HFO Home GmbH
4	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/040	TelemaxX Telekommunikation GmbH
5	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/073	Andreas Muth, Antennenbaubetrieb
6	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/074	intersaar GmbH
7	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/086	Avaya GmbH & Co. KG
8	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	00/097	WEMACOM Telekommunikation GmbH
9	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/101	wilhelm.tel GmbH
10	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/121	Fernseh GmbH Niederdorf
11	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/133	Unitymedia Hessen GmbH & Co. KG
12	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/135	Kabelfernsehen Muenchen Servicenter GmbH & Co. KG
13	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/140	Ost Tel Com Osttelecommunications-GmbH
14	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/152	GöTel GmbH
15	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/159	COM-IN Telekommunikations GmbH
16	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/172	Telemark Telekommunikationsgesellschaft Mark mbH
17	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/188	ACO Computerservice GmbH
18	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/196	I-NetPartner GmbH Online Services
19	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	00/212	Bisping & Bisping GmbH & Co. KG
20	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/007	LKG Lausitzer Kabelbetriebsgesellschaft mbH
21	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/009	KKG Kabelkommunikation Guestrow GmbH
22	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/020	Telepark Passau GmbH
23	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/036	FAKS, Frankfurter Antennen- und Kommunikationsservice GmbH
24	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/066	BT (Germany) GmbH & Co. oHG
25	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/070	DNS:NET Internet Service GmbH
26	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/079	HeLi NET Telekommunikation GmbH & Co. KG
27	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/088	SOCO Network Solutions GmbH
28	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/099	HL komm Telekommunikations GmbH
29	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/102	Telekabel Bremen oHG
30	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/104	true global communications GmbH
31	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/111	City TV-Kabelservice GmbH
32	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/124	Bernd Garthoff, tv-video-hifi-antennentechnik
33	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/150	Titan Networks GmbH
34	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/161	SWU Telenet GmbH
35	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	01/174	Kabel + Satellit Bergen Kommunikationstechnik GmbH
36	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/004	x.CableCom GmbH
37	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/035	Satelliten- und Kabelfernsehanlagen / Industrievertretung GmbH
38	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/040	km3 teledienst GmbH
39	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/047	NeckarCom – Eine Marke der NetCom BW
40	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/051	regionetz.net
41	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/064	Antronic Kommunikations Service GmbH
42	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/069	willy.tel GmbH
43	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/078	Thüringer Netkom GmbH
44	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/079	teliko GmbH
45	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/107	MEDIACOM Kabelservice GmbH
46	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/110	Funk und Technik GmbH Forst
47	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/130	SWT trilan GmbH
48	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/149	Kabel-TV Aue e.V.
49	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/152	KKS Kabel-Kommunikationsservice GmbH
50	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/158	Hughes Network Systems GmbH
51	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	02/181	MK Netzdienste GmbH & Co. KG
52	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/016	RegioNet Schweinfurt GmbH
53	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/018	Tele Columbus Sachsen-Thüringen GmbH
54	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/026	telesparfon GmbH
55	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/033	Ventelo GmbH
56	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/034	prima call GmbH
57	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/041	Girrbach - HiFi , Video, TV, Service GmbH
58	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/049	GGEW net GmbH
59	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/063	Elementmedia GmbH
60	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/076	süc//dacor GmbH
61	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/089	skyDSL Deutschland GmbH
62	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/111	1click2 Internet Services GmbH
63	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	03/130	Interoute Germany GmbH
64	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/002	42com Telecommunication GmbH
65	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/047	ADDIX Internet Services GmbH
66	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/063	net services GmbH & Co. KG
67	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/079	KurpfalzTel Gesellschaft für Telekommunikation mbH
68	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/095	Stadtwerke Flensburg GmbH
69	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/105	NEFtv GmbH
70	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/129	S-IT Informationstechnologie Betreiber GmbH & Co. KG im Nordschwarzwald
71	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/169	ccn corporate communication networks GmbH
72	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/200	Steffen Perlwitz, Vertrieb von Hard- und Software
73	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/201	Unifone GmbH
74	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/206	Stadtwerke Annaberg-Buchholz GmbH
75	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/231	ANT Granowski GmbH
76	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/244	Portunity GmbH
77	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/247	PLANinterNET VoIP-GmbH
78	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/256	rockenstein AG
79	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/268	Funknetz HG
80	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/269	Stadtwerke Marburg GmbH
81	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/276	Horst Wierling, Antennenbau/Fachhandel
82	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/284	Helmut Dräger, ANTEC
83	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/323	ATS Vertriebs- und Handelsgesellschaft UG & Co.KG
84	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/331	Bernd Asser, Antennentechnik
85	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	04/339	Antennenservice Thomas
86	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/029	Großgemeinschaftsantennenanlage Oberes Sprottental
87	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/044	I. Schneider Gemeinschafts-Antennenanlagen & Kommunikations-Systeme e.K. Inhaber Jens Schneider
88	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/052	Interessengemeinschaft \\"Gemeinschaftsantenne\\" e.V.
89	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/067	Kabelcom Rheinhessen GmbH
90	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/074	ACS Hartmann
91	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/087	Mietho & Bär GbR
92	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/088	narelic Nachrichtenelektronik GmbH
93	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/113	SELLHUSEN GmbH
94	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/119	TEGRO Kabelbau GmbH
95	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/128	TKN Telekabel - Nord GmbH
96	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/131	TV-Netzgesellschaft mbH Guben
97	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/189	L & N Kabel- und Kommunikationssysteme GmbH
98	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/191	Tele Columbus Netze Berlin GmbH
99	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/193	Mietho & Bär Kabelkom GmbH
100	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/207	Andreas Stolle
101	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/212	HFO Telecom GmbH
102	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/217	Global Village GmbH
103	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/244	outbox AG
104	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/250	TNG Stadtnetz GmbH
105	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/253	reputatio AG
106	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/262	TBits.net GmbH
107	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/275	Tele Columbus Berlin-Brandenburg GmbH & Co.KG
108	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/279	reventix GmbH
109	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/295	mr. next id GmbH
110	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/314	terralink networks GmbH
111	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/326	omnidat Gesellschaft für individuelle Netzwerk- und Kommunikationslösungen mbH
112	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/349	m3connect GmbH
113	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/358	RS Gesellschaft fuer Informationstechnik mbH & Co. KG
114	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/367	AJE Consulting GmbH & Co. KG
115	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/383	Steffen Hartmut Allstädt, Doergi.Net
116	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/401	VPN Deutschland Ltd. & Co. KG
117	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/411	MEK-cable GbR
118	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/412	D/W Elektrofunk GmbH
119	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/414	NU Informationssysteme GmbH
120	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	05/416	Sachs Kommunikationssysteme GmbH
121	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/013	Günther Schuster, S - SYSTEMS
122	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/015	PTC Telecom GmbH
123	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/019	Verizon Deutschland GmbH
124	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/026	DW-TelefonDienste
125	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/078	Ackermann & Pfetzing GbR
126	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/114	Unitymedia NRW GmbH
127	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/118	congstar GmbH
128	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/127	Gemeinschaftsantenne Schmalkalden e.V.
129	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/137	InSysCo Datensysteme GmbH
130	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/145	overturn technologies GmbH
131	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/153	optimaconnect GmbH
132	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/157	purtel.com GmbH
133	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/164	Inter.net Germany GmbH
134	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/165	FAG Fernseh-Antennen-Gemeinschaft Bad Steben e.V.
135	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/181	Stadtwerke Lübeck GmbH
136	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/197	010090 GmbH
137	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/213	CNT AG
138	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/218	Telekommunikation Stefan Kienhöfer (Airnode)
139	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/224	Elektrizitäts-Genossenschaft Tacherting-Feichten eG
140	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/281	tenios GmbH
141	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/289	dus.net GmbH
142	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/296	NYNEX satellite OHG
143	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/306	DEK Telecom GmbH
144	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/311	AlphaCron Datensysteme
145	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/319	MDDSL Mitteldeutsche Gesellschaft für Kommunikation mbH
146	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/325	Norman Dietzen (Wireless-DSL - Eifel-DSL)
147	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/336	Voxbone SA
148	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/341	voipGATE S.A.
149	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/363	GWS Stadtwerke Hameln GmbH
150	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/364	HochrheinNET GmbH
151	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/367	Susann Höhne Mobil24-direct
152	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	06/372	Netcommunity GmbH
153	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/007	vocatel business gmbh
154	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/027	AS Antennenanlagen-Service GmbH
155	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/033	AVACOMM Systems GmbH
156	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/034	VICTORVOX
157	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/043	Kadsoft Computer GmbH Freital
158	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/046	Computertechnik Trenkle e.K.
159	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/079	G-FIT Gesellschaft für innovative Telekommunikationsdienste mbH & Co. KG
160	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/097	Broadsoft Germany GmbH
161	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/138	Tele Columbus Multimedia GmbH
162	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/172	NFON AG
163	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/183	Feedback Mobile Services GmbH
164	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/198	G. Ziegelmeier e.K
165	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/214	Server-Ware GmbH
166	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/228	DALASON GmbH
167	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/232	Stadtwerke Wedel GmbH
168	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/262	DeTeWe Communications GmbH
169	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/281	FPS Informationssysteme GmbH
170	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/313	toplink GmbH
171	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/320	TeamFON GmbH
172	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	07/348	WiSoTEL
173	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/010	inexio Informationstechnologie und Telekommunikation GmbH
174	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/013	Deutsche Telefon Standard AG
175	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/014	AEP Plückhahn Netze GmbH
176	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/016	3U TELECOM GmbH
177	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/042	Brandl Services GmbH
178	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/049	BBS-Mitteldeutsche Breitbandsystem UG
179	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/086	AirIT Services GmbH
180	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/115	WTC Wohnen & TeleCommunication GmbH & Co. KG
181	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/132	equada GmbH
182	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/140	Stadtwerke Einbeck GmbH
183	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/144	RelAix Networks GmbH
184	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/156	KNT Internet
185	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/158	Netzquadrat
186	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/170	Agiliscom AG
187	2022-05-06 12:26:10+02	2022-05-06 12:26:10+02	\N	08/185	Stadtwerke Trostberg Telekommunikation GmbH
188	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/192	Photonium NetSolutions GmbH
189	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/202	Arche Netvision GmbH
190	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/213	Tele Columbus Hessen GmbH
191	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/214	BBcom Berlin-Brandenburgische Communikationsgesellschaft mbH
192	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/220	R&M SimplyTechnnologies GmbH
193	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/227	Stadtwerke Schwedt GmbH
194	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/231	Regionalantenne Cunnersdorf e.V.
195	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/253	E-Plus Mobilfunk GmbH
196	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/262	TeleForte AG
197	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/289	BORnet GmbH
198	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/313	Schönenberg-Computer GmbH
199	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/316	Tal.de Klaus Internet Service GmbH
200	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/318	RST Datentechnik GmbH
201	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/320	easybell GmbH
202	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/321	ip-fabric GmbH
203	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/322	Stiegeler Internet Service GmbH
204	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/324	Freikom GmbH
205	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/331	Damovo Deutschland GmbH & Co. KG
206	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/338	JOBST DSL e.K.
207	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/345	ICN GmbH + Co. KG
208	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	08/346	Antennen Einert e.K.
209	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/003	ANTEC Servicepool GmbH
210	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/038	Tele-System Harz GmbH
211	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/042	Trusted Network GmbH
212	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/057	mieX Deutschland GmbH
213	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/059	meetyoo conferencing GmbH
214	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/080	RSM Freilassing, Michael Reiner Rack
215	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/091	COS-on-Air OHG
216	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/102	WDSL-Oberlausitz
217	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/106	Teleport GmbH
218	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/118	sewikom GmbH
219	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/123	RMS-systems Datenverarbeitungs GmbH
220	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/125	Drahtlos DSL GmbH Mittelsachsen
221	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/132	net-and-phone GmbH
222	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/148	LWLcom GmbH
223	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/172	W M B Kabelservice GmbH
224	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/174	Voiceworks GmbH
225	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/175	Felkatec Software GmbH & Co. KG
226	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/182	purpur Networks GmbH
227	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/193	tkrz Stadtwerke GmbH
228	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/216	amplus AG
229	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/222	Ilm-Provider UG (haftungsbeschränkt)
230	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/258	Marienberg GmbH
231	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/261	TECOSI ATF GmbH
232	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/268	Innofactory GmbH
233	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/276	Breitbandservice Gantert GmbH & Co.KG
234	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/289	RK² Systemintegration GmbH
235	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/296	Thüga SmartService GmbH
236	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/311	DCC GmbH
237	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/318	Unser Ortsnetz GmbH
238	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/320	Moderne Kommunikationstechnologie Hohentengen GmbH
239	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/324	e.wa riss Netze GmbH
240	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	09/326	Mass Response Deutschland GmbH
241	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/006	schnell-im-netz.de GmbH & Co.KG
242	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/013	ENNI Energie & Umwelt Niederrhein GmbH
243	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/029	D&T Internet GmbH
244	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/054	Schleusinger Media Service GmbH
245	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/060	HessenKom GmbH & Co KG
246	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/061	Project66 IT-Service & Design Niels Rosenhahn
247	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/081	KSP-Kabelservice Prenzlau GmbH
248	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/094	Filiago GmbH & Co. KG
249	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/120	newone GmbH
250	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/131	TKN Deutschland GmbH
251	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/133	Hofmeir Media GmbH
252	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/141	PFALZconnect GmbH
253	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/161	DSI GmbH Daten Service Informationssysteme
254	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/181	vio:networks GmbH
255	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/190	Wittenberg-net GmbH
256	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/196	Dhom & Johannsen GbR
257	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/213	inopla GmbH
258	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/217	GP-Elektronik e.K.
259	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/240	COLT Technology Services GmbH
260	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/251	A+U Baumann oHG
261	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/253	Wichmann Internet Services GmbH
262	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/257	TELE AG
263	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/283	HNS GmbH
264	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/305	Stadtwerke Konstanz GmbH
265	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/314	Stern Kom GmbH
266	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/323	Stadtwerke Itzehoe GmbH
267	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/329	epcan GmbH
268	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/330	Gemeinschaftsantenne Struth-Helmersdorf und Floh-Seligenthal OT Schnellbach e.V.
269	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/334	smart-DSL GmbH
270	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/339	encoLine GmbH
271	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/350	AnWaDi.de
272	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/351	Martin Prager, NbIServ
273	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/360	Marco Bungalski GmbH
274	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	10/399	OpenNumbers GmbH
275	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/004	nexiu GmbH
276	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/013	matelso GmbH
277	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/014	D-Connect GmbH
278	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/043	Multiconnect GmbH
279	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/048	PHILIS IT
280	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/057	DATA CMR GmbH & Co. KG
281	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/063	MUENET GmbH
282	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/086	DSLmobil GmbH
283	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/093	tel.quick GmbH & Co. KG
284	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/094	Tele System GmbH Rostock
285	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/095	d+p breitbandconcept UG
286	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/097	IN-telegence GmbH
287	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/113	INCAS GmbH
288	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/124	Vereinigte Stadtwerke Media GmbH
289	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/131	telsakom GmbH
290	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/145	PIRONET NDH Datacenter AG & Co. KG
291	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/146	Globalways AG
292	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/152	Elektrizitätswerk Goldbach-Hösbach GmbH & Co. KG
293	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/153	innogy TelNet GmbH
294	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/172	CEMI Service GmbH
295	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/174	Truphone GmbH
296	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/176	pinnau.com GmbH & Co. KG
297	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/183	Stadtwerke Hammelburg GmbH
298	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/195	Snellstar GmbH
299	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/212	Stadtwerke Barmstedt Xtra GmbH
300	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/213	GWHtel Gmbh & Co.KG
301	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/224	vitroconnect GmbH
302	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/247	Breitband Hoyerswerda GmbH
303	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/268	dng IT GmbH & Co. KG
304	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/298	Martens Deutsche Telekabel GmbH
305	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/305	Sipgate GmbH
306	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/307	Stadtwerke Eckernförde GmbH
307	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/308	Vodafone Kabel Deutschland GmbH
308	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/331	Primacom Berlin GmbH
309	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/358	EMOTEC NETWORKS GmbH
310	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/369	Unitymedia BW GmbH
311	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/390	Internet & Go GmbH
312	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/391	comjoo business solutions GmbH
313	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/392	OpenXS GmbH
314	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/400	QUiX Breitband GmbH
315	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	11/411	Gemeindewerke Hohenwestedt GmbH
316	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/017	1&1 Telecom GmbH
317	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/019	Milde Software Solutions (MSS)
318	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/037	Hofnetz und IT Services GmbH
319	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/038	WEBDISCOUNT GmbH & Co. KG
320	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/043	KNH - TV Limited
321	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/048	KomMITT-Ratingen GmbH
322	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/054	net services GmbH & Co KG
323	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/060	Stadtwerke Engen GmbH
324	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/063	TELEflash GmbH
325	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/067	Lightup Network Solutions GmbH & Co. KG
326	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/068	MVD Multimedia Verbundnetz Dresden GmbH
327	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/073	KABEL-TV-BINZ GmbH & Co.KG
328	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/074	Stadtwerke Ahrensburg GmbH
329	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/086	WIFISAX UG
330	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/094	Sat Internet Services GmbH
331	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/098	KOMNEXX GmbH
332	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/119	Web + Phone GmbH
333	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/125	media construct gmbH
334	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/129	WIFISAX UG
335	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/133	EWR Netz GmbH
336	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/140	C&S Breitband GmbH
337	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/149	Buchholz Digital GmbH
338	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/169	NetTask GmbH
339	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/183	TEFIS-Technique & Financial Service Ltd.& Co.KG Zweigstelle Frankfurt
340	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/184	mWerk GmbH
341	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/195	oscon itk GmbH
342	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/198	VoiceON Telecom Services GmbH
343	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/199	Umbra Networks Gesellschaft für Telekommunikation
344	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/209	Stadtwerke Bochum GmbH
345	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/219	Stadtwerke Geesthacht GmbH
346	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/223	SWW Wunsiedel GmbH
347	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/239	teutel GmbH
348	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/242	amisol GmbH
349	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/264	ITS AG
350	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	12/345	Bundesnetzagentur
351	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/002	siebnich.com - it.competence!
352	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/018	TEFONIX UG
353	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/037	gustav internet GmbH & Co. KG
354	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/049	Glöckle direct GmbH
355	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/051	simple Communication GmbH
356	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/057	primastrom GmbH
357	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/065	hugo internet GmbH & Co. KG
358	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/069	Stadtwerke Finsterwalde GmbH
359	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/084	Dennis Spiegel, Spiegel-Bild
360	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/095	Deutsche Glasfaser Wholesale GmbH
361	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/122	Disquom Funktechnik GmbH
362	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/132	Rhönnet GmbH
363	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/145	echtschnell GmbH
364	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/146	In(n) Energie GmbH
365	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/152	pepcom GmbH
366	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/158	ansit-com GmbH
367	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/186	werknetz internet GmbH & Co. KG
368	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/189	TeGe Deutschland GmbH
369	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/190	wirsNET Kabeldienstleistungen UG (haftungsbeschränkt)
370	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/201	NEW Niederrhein Energie und Wasser GmbH
371	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/206	NTTCable Service KG
372	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/211	Luxembourg Online SA
373	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/216	Deutsche Gesellschaft für Kommunikation und Service mbH
374	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/222	media.tel Informationsdienstleistungs GmbH
375	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	13/225	Fl!nk GmbH
376	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/017	Speedloc Datacenter
377	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/035	Arcor
378	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/036	Orbitcom GmbH
379	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/074	Antennengemeinschaft ERZNET AG
380	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/086	Jonny Rönnefahrt Chono Gruppe Deutschland
381	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/104	Osburg Systems GmbH
382	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/106	Olfenkom GmbH
383	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/118	SEDHO GmbH
384	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/119	voxenergie GmbH
385	2022-05-06 12:26:11+02	2022-05-06 12:26:11+02	\N	14/129	telenetwork AG
386	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/133	Telekabel Riesa GmbH
387	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/134	dd Handelsgesellschaft mbH
388	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/140	YplaY Germany GmbH
389	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/157	dtms GmbH
390	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/160	OR Network GmbH
391	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/162	GVG Glasfaser GmbH
392	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/167	Peoplefone GmbH
393	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/169	Deutsche Glasfaser Business GmbH
394	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/175	MCN Telekommunikacios Szolgaltato Korlatolt Felelösegü Tarsasag
395	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/178	Servario Networks GmbH
396	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/179	NetCom Access Gesellschaft für Telekommunikation
397	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/184	G B C Kommunikationssysteme GmbH
398	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/185	Eckhard Möbius, Möbius Kommunikation Elektronik
399	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/189	DS Computer Service
400	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/191	Breitbandversorgung Rhein-Neckar GmbH
401	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/206	MIXvoip SA
402	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/208	Mango Office GmbH
403	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/210	Friedrich Kecht und Gerhard Guggenbichler GbR, RuhNet
404	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/216	EnoCom GmbH
405	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/221	Olaf Stenzel, PyloNet
406	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/252	foncloud GmbH & Co. KG
407	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/267	Carsten Dickenscheid IT-Service
408	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/273	Stadtwerke SchwerinGmbH (SWS)
409	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	14/278	Rekers Digitaltechnik GmbH & Co. KG
410	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/004	MEGA Monheimer Elektrizitäts- und Gasversorgung GmbH
411	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/006	fonial GmbH
412	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/009	Stadtwerke Dorfen GmbH
413	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/030	E.Net GmbH & Co KG
414	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/043	Stadtwerke Eutin GmbH
415	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/044	Stadtwerke Merseburg GmbH
416	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/045	Flow7 GmbH
417	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/048	T-Mobile HotSpot GmbH
418	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/053	telegra GmbH
419	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/057	Deutsche Glasfaser Home GmbH
420	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/070	AnschlussWerk GmbH
421	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/071	iks informations- und kommunikations Systeme GmbH
422	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/072	GETEC media GmbH
423	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/075	Schleswiger Stadtwerke GmbH
424	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/078	Radio Bachmann GmbH
425	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/083	CompuNet Systems GmbH
426	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/084	Dolphin IT-Systeme e.K.
427	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/086	ZEAG Energie AG
428	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/091	Unify Deutschland GmbH & Co. KG
429	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/102	weissblau-breitband UG (haftungsbeschänkt)
430	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/104	Elektro - Garten
431	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/105	Stadtwerke Schneverdingen-Neuenkirchen GmbH
432	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/110	TRILUCom Holding S.A.
433	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/113	Stephan Konheiser EDV-Studio
434	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/119	Getinternet GmbH
435	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/124	itelco-consult GmbH
436	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/126	FNOH-DSL Südheide GmbH
437	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/143	Axxess Solutions GmbH
438	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/145	Cloud Communication Service GmbH
439	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/147	My-Cloudline Deutschland GmbH
440	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/148	getSolve IT GmbH
441	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/158	Schiffel IT-Service GmbH
442	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/165	Breitbandversorgung Münsterland GmbH
443	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/179	StarCom-Bauer GmbH
444	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/190	NX4 Networks GmbH
445	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/196	Stadtwerke Kaltenkirchen GmbH
446	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/199	Freikom GmbH
447	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/203	zollernalb-data GmbH
448	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/209	gnTel GmbH
449	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/214	Vater Service4Net GmbH
450	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/233	Stadtwerke Neustrelitz GmbH
451	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	15/239	SOLIDAS Media GmbH
452	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/005	Stadtwerke Annaberg-Buchholz Energie AG
453	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/008	Jens Dreiheller / Lodaya Technologies {Einzelunternehmen ohne HR-Eintrag)
454	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/013	WINatNet Provider und Netze GmbH
455	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/019	Stadtwerke Bad Reichenhall KU
456	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/024	telkodata GmbH
457	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/037	SUPERTEL A/S
458	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/054	Business Phone Net GmbH & Co. KG
459	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/087	NGN Telecom GmbH
460	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/096	Stadtwerke - Strom Plauen GmbH & Co. KG
461	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/110	ProCRM IT-Systems GmbH
462	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/114	Glasfaser Hengersberg GmbH
463	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/117	COMULUS AG
464	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/128	Gemeindewerke Nümbrecht GmbH
465	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/140	MoellerEDV - Klaus A. Möller
466	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/145	GateStream
467	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/152	eins energie in sachsen GmbH & Co. KG
468	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/157	Georg Kröber Webservice
469	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/172	countrycode 49 UG
470	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/189	SVO Vertrieb GmbH
471	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/192	Ziegelmeier GmbH & Co. Antennenservice KG
472	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/219	ngena GmbH
473	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/230	Tele Columbus AG
474	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/231	NetCom Access Gesellschaft für Telekommunikation
475	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/233	KAEMI GmbH
476	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/237	euromicron Deutschland GmbH
477	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/244	Muth Citynetz Halle GmbH
478	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/246	Breitbandversorgung Ostwestfalen GmbH
479	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/256	Via-Vox GmbH
480	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/270	Stadtwerke Teterow GmbH
481	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/271	QuaeroSys UG (haftungsbeschränkt)
482	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/279	Stadtwerke Erkrath GmbH
483	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	16/287	DNCC GmbH
484	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/002	Stadtwerke Forchheim GmbH
485	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/004	Unify Software and Solutions GmbH & Co. KG
486	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/005	Unify Communications and Collaboration GmbH & Co. KG
487	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/047	WEMAG AG
488	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/058	oneCentral B.V.
489	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/060	CNS Network GmbH
490	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/073	kamikom GmbH
491	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/076	MiSaxNet GmbH
492	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/094	Stadtwerke Velbert GmbH
493	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/106	Stadtwerke Hilden GmbH
494	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/113	Gesellschaft für Digitale Werte mbH
495	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/115	Breitbandversorgung Deutschland GmbH
496	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/118	DMRZ Deutsches Mittelstandsrechenzentrum Betreibergesellschaft mbH
497	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/119	Rapidata GmbH
498	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/126	Landwerke M-V Breitband GmbH
499	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/130	inovisio communications GmbH
500	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/135	Stadtwerke Nortorf Breitband GmbH
501	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/142	ingenia digitale Netze GmbH
502	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/145	Surf-Paradies Internet Service GmbH
503	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/161	ETN EmslandTel.Net GmbH & Co. KG
504	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/164	Q-MEX Networks
505	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/168	Nord-IT Bremen Beteiligungs-UG(haftungsbeschränkt)&CO.KG
506	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/170	Barbara Ilchmann-Breu COMPRO Computerprogramme - COMPRO-TEL-NET
507	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/183	Hightel GmbH
508	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/186	globalcarrier telecom GmbH
509	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/191	Office-ED Computer GmbH
510	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/192	PROsave GmbH
511	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/208	Francotyp-Postalia Vertrieb und Service GmbH
512	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/210	Kumeno GmbH
513	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/216	Stadtwerke Oldenburg in Holstein Media GmbH
514	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/222	Tell-Phone Deutschland GmbH
515	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/228	telba AG
516	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/229	Telion Kommunikationsgesellschaft UG (haftungsbeschränkt)
517	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/230	Fa. Schoefisch
518	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/238	SPIEKEROOGkom // Wolf Lindenthal
519	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/244	SQUILD GmbH
520	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/258	Twilio Inc.
521	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/260	D.T.NET Service OHG
522	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	17/261	DCC Duisburg CityCom GmbH
523	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/001	1N Telecom GmbH
524	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/010	MIXvoip GmbH
525	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/023	Emden Digital GmbH
526	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/027	Stadtwerke Pasewalk GmbH
527	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/044	Netsign Networks GmbH
528	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/046	Stadtwerke Meerane GmbH
529	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/054	Anycall GmbH
530	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/064	cratchmere.com gmbh
531	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/068	Stadtwerke Rostock AG
532	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/076	Stadtwerke Ludwigsburg-Kornwestheim GmbH
533	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/096	maxIT Consulting GmbH
534	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/102	Stadtwerke Bühl GmbH
535	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/136	Herbst Datentechnik GmbH
536	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/149	Harmatec GmbH
537	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/156	nvb Nordhorner Versorgungsbetriebe GmbH
538	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/159	autphone GmbH
539	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/164	EuraLink
540	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	18/192	SWaP GmbH Surf, Watch & Phone
541	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	19/054	Inkotec GbR
542	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	19/096	GREENFIBER Internet & Dienste GmbH
543	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	93/007	Telekom Deutschland GmbH
544	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	93/090	KEVAG Telekom GmbH
545	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	94/309	bn:t Blatzheim Networks Telecom GmbH
546	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	95/032	NetCologne Gesellschaft für Telekommunikation mbH
547	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	95/095	Alice, Hansenet, O2, O2 Genion
548	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	95/137	1&1 Versatel Deutschland GmbH
549	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/058	Communication Services Tele2 GmbH
550	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/084	sdt.net AG
551	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/151	Genias Internet (Inh. Stefan Englhardt)
552	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/161	TELCAT MULTICOM GmbH
553	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/194	Tele Columbus Sachsen-Anhalt GmbH
554	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/205	WOBCOM GmbH
555	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/211	NETHINKS GmbH
556	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	96/224	EFN eifel-net Internet-Provider GmbH
557	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/018	Tele Columbus Kabel Service GmbH
558	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/031	Teleco GmbH Cottbus Telekommunikation
559	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/033	Antennen Schulze GmbH
560	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/084	GELSEN-NET Kommunikationsgesellschaft mbH
561	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/119	SWN Stadtwerke Neumünster GmbH
562	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/163	M-net Telekommunikations GmbH
563	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/165	Talkline, Cellway
564	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/170	LüneCom Kommunikationslösungen GmbH
565	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/174	E-Plus Service GmbH & Co. KG
566	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/186	electronic anders
567	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/238	imos Gesellschaft für Internet-Marketing und Online-Services mbH
568	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/244	DOKOM Gesellschaft für Telekommunikation mbH
569	2022-05-06 12:26:12+02	2022-05-06 12:26:12+02	\N	97/245	Osnatel GmbH
570	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	97/246	ENTEGA Medianet GmbH
571	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/016	e.discom Telekommunikations GmbH
572	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/023	RFT kabel Brandenburg GmbH
573	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/030	TeleData GmbH
574	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/037	MEGA Communications GmbH
575	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/047	Median Telecom GmbH
576	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/048	R-KOM Regensburger Telekommunikationsgesellschaft mbH & Co. KG
577	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/067	VSE NET GmbH
578	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/068	Broadnet AG
579	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/084	MDCC Magdeburg-City-Com GmbH
580	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/085	Netcom Kassel Gesellschaft für Telekommunikation mbH
581	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/090	Pfalzkom Gesellschaft für Telekommunikation mbH
582	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/094	komro GmbH
583	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/105	Daten- und Telekommunikations GmbH Dessau
584	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/112	envia TEL GmbH
585	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/112X	envia TEL (interne Portierung)
586	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/122	Telenec Telekommunikation Neustadt GmbH
587	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/139	TMR Telekommunikation Mittleres Ruhrgebiet GmbH
588	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/141	operator Telekommunikation International AG
589	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/149	Tele Columbus Cottbus GmbH
590	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	98/150	REKA Regionalservice Kabelfernsehen GmbH
591	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/001	WiTCOM Wiesbadener Informations-und Telekommunikations GmbHGmbH
592	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/002	neu-medianet GmbH
593	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/017	BITel Gesellschaft für Telekommunikation mbH
594	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/021	htp GmbH
595	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/028	Neckarcom
596	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/029	TKS Telepost Kabel-Service Kaiserslautern GmbH & Co. KG
597	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/038	LEW TelNet GmbH
598	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/069	Mandala Internet, EDV-Servis GmbH
599	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/083	planet 33 AG telecommunications internet security
600	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/121	telego! GmbH
601	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/136	ecotel communication ag
602	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/138	Telekommunikation Lindau GmbH
603	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/147	TraveKom Telekommunikationsgesellschaft mbH
604	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/149	Christian Wiesner, VIDEOPRO
605	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/164	Stadtnetz Bamberg Gesellschaft für Telekommunikation mbH
606	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/174	Herzo Media GmbH & Co. KG
607	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	99/180	K-net Telekommunikation GmbH
608	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	n.v.	EKP nicht vorhanden
609	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/001	Gesmo Gesellschaft für Mobilfunkservice
610	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/003	Kabel BW
611	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/004	Tele Columbus AG
612	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/005	Osnatel GmbH
613	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/006	Osnatel GmbH
614	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/008	placetel
615	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/009	Sat Internet Services GmbH
616	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/011	Telefonica Germany GmbH
617	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/012	Outland-net GmbH
618	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/013	manCityNet
619	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/014	primacall GmbH
620	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/015	RIKOM GmbH
621	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/016	Pri-Tel GmbH
622	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/017	BREKO Bundesverband Breitbandkommunikation e.V.
623	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/018	NES Elektro & Service GmbH
624	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/019	NetCom BW GmbH
625	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	xx/020	Northern Access GmbH
\.


--
-- Data for Name: endpoint; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.endpoint (id, created_at, updated_at, deleted_at, hostname, mac, description, fixed_ip, modem_id, ip, add_reverse, version, prefix, qos_id, device_id, acl_id, rule_id, state) FROM stdin;
\.


--
-- Data for Name: enviacontract; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.enviacontract (id, created_at, updated_at, deleted_at, external_creation_date, external_termination_date, envia_customer_reference, envia_contract_reference, state, start_date, end_date, next_id, prev_id, end_reason, lock_level, method, sla_id, tariff_id, variation_id, contract_id, modem_id) FROM stdin;
\.


--
-- Data for Name: enviaorder; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.enviaorder (id, created_at, updated_at, deleted_at, orderid, method, ordertype_id, ordertype, orderstatus_id, orderstatus, orderdate, ordercomment, related_order_id, customerreference, contractreference, contract_id, modem_id, enviacontract_id, last_user_interaction) FROM stdin;
\.


--
-- Data for Name: enviaorder_phonenumber; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.enviaorder_phonenumber (id, created_at, updated_at, deleted_at, enviaorder_id, phonenumber_id) FROM stdin;
\.


--
-- Data for Name: enviaorderdocument; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.enviaorderdocument (id, created_at, updated_at, deleted_at, document_type, mime_type, filename, enviaorder_id, upload_order_id) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.failed_jobs (id, connection, queue, payload, failed_at, exception, uuid) FROM stdin;
\.


--
-- Data for Name: favorite_netelements; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.favorite_netelements (id, user_id, netelement_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: fiber_node; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.fiber_node (id, created_at, updated_at, deleted_at, name, cable_interface, netelement_id, mdd_status) FROM stdin;
\.


--
-- Data for Name: fiber_node_rpd; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.fiber_node_rpd (created_at, fiber_node_id, rpd_id, id) FROM stdin;
\.


--
-- Data for Name: global_config; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.global_config (id, created_at, updated_at, deleted_at, name, street, city, phone, mail, log_level, headline1, headline2, default_country_code, passwordresetinterval, alert1, alert2, alert3, isallnetssidebarenabled, login_img) FROM stdin;
1	\N	\N	\N	\N	\N	\N	\N	\N	1	NMS Prime	The next Generation NMS	DE	120	\N	\N	\N	f	\N
\.


--
-- Data for Name: guilog; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.guilog (id, created_at, updated_at, deleted_at, user_id, username, method, model, model_id, text) FROM stdin;
1	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N	0	cronjob	created	Role	1	
2	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N	0	cronjob	created	Role	2	
3	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N	0	cronjob	created	Role	3	
4	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N	0	cronjob	created	Role	4	
5	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N	0	cronjob	updated	Role	1	rank: 100->101
6	2022-05-06 12:25:07+02	2022-05-06 12:25:07+02	\N	0	cronjob	updated	User	1	api_token: ->is8SjrF1dFOAo8x5P4pgxiJB45ebZeLYe335ZjvJLSVi1PvGLbVLygbJbAaBrWUQIpGHJefSaXf5hZw7
7	2022-05-06 12:25:13+02	2022-05-06 12:25:13+02	\N	0	cronjob	created	Role	5	
8	2022-05-06 12:25:17+02	2022-05-06 12:25:17+02	\N	0	cronjob	updated	NetElementType	3	name: Cmts->NetGw
9	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	1	base_type: 0->1
10	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	2	base_type: 0->2
11	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	3	base_type: 0->3
12	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	4	base_type: 0->4
13	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	5	base_type: 0->5
14	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	6	base_type: 0->6
15	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	7	base_type: 0->7
16	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	8	base_type: 0->8
17	2022-05-06 12:25:18+02	2022-05-06 12:25:18+02	\N	0	cronjob	updated	NetElementType	9	base_type: 0->9
18	2022-05-06 12:25:26+02	2022-05-06 12:25:26+02	\N	0	cronjob	created	Ticketsystem	1	
19	2022-05-06 12:25:49+02	2022-05-06 12:25:49+02	\N	0	cronjob	updated	ProvBase	1	dns_password: ->fe1fQGXpZYma9wH/+vNyLA==
20	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	0	cronjob	created	PhoneTariff	1	
21	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	0	cronjob	created	PhoneTariff	2	
22	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	0	cronjob	created	PhoneTariff	3	
23	2022-05-06 12:26:09+02	2022-05-06 12:26:09+02	\N	0	cronjob	created/updated	CarrierCode	-1	366 entries created/updated
24	2022-05-06 12:26:13+02	2022-05-06 12:26:13+02	\N	0	cronjob	created/updated	EkpCode	-1	634 entries created/updated
25	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	0	cronjob	created	Company	1	
26	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	0	cronjob	created	SepaAccount	1	
27	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	0	cronjob	created	CostCenter	1	
28	2023-04-05 16:55:44+02	2023-04-05 16:55:44+02	\N	0	cronjob	created	VoipMon	1	
29	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	3	_lft: ->1, _rgt: ->2
30	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	4	_lft: ->3, _rgt: ->4
31	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	5	_lft: ->5, _rgt: ->6
32	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	6	_lft: ->7, _rgt: ->8
33	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	7	_lft: ->9, _rgt: ->10
34	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	9	_lft: ->12, _rgt: ->13
35	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	8	_lft: ->11, _rgt: ->14
36	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	10	_lft: ->15, _rgt: ->16
37	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	12	_lft: ->18, _rgt: ->19
38	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	13	_lft: ->20, _rgt: ->21
39	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	14	_lft: ->22, _rgt: ->23
40	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	11	_lft: ->17, _rgt: ->24
41	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	15	_lft: ->25, _rgt: ->26
42	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	2	_lft: ->28, _rgt: ->29
43	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	1	_lft: ->27, _rgt: ->30
44	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	16	_lft: ->31, _rgt: ->32
45	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	17	_lft: ->33, _rgt: ->34
46	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	18	_lft: ->35, _rgt: ->36
47	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	19	_lft: ->37, _rgt: ->38
48	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	20	_lft: ->39, _rgt: ->40
49	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	21	_lft: ->41, _rgt: ->42
50	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	22	_lft: ->43, _rgt: ->44
51	2023-04-05 16:59:54+02	2023-04-05 16:59:54+02	\N	0	cronjob	updated	NetElementType	23	_lft: ->45, _rgt: ->46
52	2023-04-19 16:56:24+02	2023-04-19 16:56:24+02	\N	0	cronjob	created	Contract	2	
53	2023-04-19 17:51:27+02	2023-04-19 17:51:27+02	\N	0	cronjob	created	Configfile	3	
54	2023-05-09 17:00:03+02	2023-05-09 17:00:03+02	\N	1	superuser initial	updated	User	1	last_login_at: ->2023-05-09 17:00:03+0200
55	2023-05-09 17:01:37+02	2023-05-09 17:01:37+02	\N	1	superuser initial	updated	User	1	last_login_at: 2023-05-09 17:00:03+02->2023-05-09 17:01:37+0200
56	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	Qos	2	
57	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	8	
58	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	9	
59	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	10	
60	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	11	
61	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	12	
62	2023-05-09 17:04:59+02	2023-05-09 17:04:59+02	\N	1	superuser initial	created	RadGroupReply	13	
63	2023-05-09 17:05:21+02	2023-05-09 17:05:21+02	\N	1	superuser initial	created	Modem	2	
64	2023-05-09 17:05:21+02	2023-05-09 17:05:21+02	\N	1	superuser initial	updated	Modem	2	
\.


--
-- Data for Name: hfcreq; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.hfcreq (id, created_at, updated_at, deleted_at, ro_community, rw_community, rkm_server, rkm_server_username, rkm_server_password, video_controller, video_controller_username, video_controller_password, video_encoder) FROM stdin;
1	\N	\N	\N	public	private	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: hubsite; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.hubsite (id, created_at, updated_at, deleted_at, netelement_id) FROM stdin;
\.


--
-- Data for Name: indices; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.indices (id, created_at, updated_at, deleted_at, netelement_id, parameter_id, indices) FROM stdin;
\.


--
-- Data for Name: inventory; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.inventory (id, created_at, updated_at, netelement_id, name, pid, manufacturer, serial_nr, part_nr, ports, status, inserted, description) FROM stdin;
\.


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.invoice (id, created_at, updated_at, deleted_at, contract_id, settlementrun_id, sepaaccount_id, year, month, filename, type, number, charge, charge_gross) FROM stdin;
\.


--
-- Data for Name: ippool; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ippool (id, created_at, updated_at, deleted_at, netgw_id, type, net, ip_pool_start, ip_pool_end, router_ip, broadcast_ip, dns1_ip, dns2_ip, dns3_ip, optional, description, version, prefix, prefix_len, delegated_len, active, vendor_class_identifier) FROM stdin;
\.


--
-- Data for Name: item; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.item (id, created_at, updated_at, deleted_at, contract_id, product_id, count, valid_from, valid_from_fixed, valid_to, valid_to_fixed, credit_amount, costcenter_id, accounting_text, payed_month, smartcardids) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: l2tp; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.l2tp (id, created_at, updated_at, netelement_id, tunnel_id, remote_tunnel_id, ip_version, ip, remote_ip, error_code) FROM stdin;
\.


--
-- Data for Name: link; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.link (id, created_at, updated_at, deleted_at, "from", "to", name, if_from, if_to, type, state, description, is_up_from, is_up_to, speed_from, speed_to, utilization_from, utilization_to, rx_errors_from, rx_errors_to, tx_errors_from, tx_errors_to) FROM stdin;
\.


--
-- Data for Name: lldp; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.lldp (id, created_at, updated_at, deleted_at, local_interface_description, remote_interface_description, remote_system_name, remote_system_description, if_index, netelement_id, lldp_suffix, remote_netelement_id, mac_address) FROM stdin;
\.


--
-- Data for Name: market; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.market (id, created_at, updated_at, deleted_at, netelement_id) FROM stdin;
\.


--
-- Data for Name: mibfile; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.mibfile (id, created_at, updated_at, deleted_at, name, filename, version, description) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.migrations (id, migration, batch) FROM stdin;
1	2015_11_24_000100_create_Authuser_table	1
2	2015_11_24_000200_create_Authmeta_table	1
3	2015_11_24_000300_create_Authcore_table	1
4	2015_11_24_000400_create_Authusermeta_table	1
5	2015_11_24_000500_create_Authmetacore_table	1
6	2015_11_25_113333_create_password_reminders_table	1
7	2015_12_15_100000_create_GlobalConfig_table	1
8	2016_02_22_104731_alter_Authuser_table	1
9	2016_06_13_162600_create_GuiLog_table	1
10	2017_07_20_175020_create_jobs_table	1
11	2017_09_11_120337_create_failed_jobs_table	1
12	2017_11_09_000100_UpdateAuthusersUndoUnique	1
13	2017_11_29_000100_RenameAuthmetaToAuthrole	1
14	2018_04_06_112500_shift_failed_jobs_table	1
15	2018_04_06_112800_shift_jobs_table	1
16	2018_05_14_165239_create_bouncer_tables	1
17	2018_05_16_203941_csv_data	1
18	2018_05_18_000100_install_php_config_base	1
19	2018_08_08_000100_change_engine_to_innodb	1
20	2018_08_24_134622_update_users_table	1
21	2018_09_07_000100_create_sla_table	1
22	2018_09_07_000200_create_support_request_table	1
23	2018_09_28_000100_install_remove_self_handling	1
24	2018_11_30_095454_bugfix_roles_table	1
25	2018_11_30_115147_update_users_table_last_login	1
26	2019_07_01_114915_update_bouncer_package	1
27	2019_07_01_163135_set_empty_strings_to_null	1
28	2019_07_07_181904_create_column_initial_dashboard_to_users_table	1
29	2019_07_18_110517_add_alerts_fields_to_globalconfig_table	1
30	2020_01_15_145111_relationship_fixes	1
31	2020_04_28_094205_drop_csv_data_table	1
32	2020_06_20_000100_install_upload_limit	1
33	2020_08_10_100100_update_users_add_geopos	1
34	2020_09_29_102103_add_api_token_to_user_table	1
35	2020_10_12_150219_install_update_to_php_73	1
36	2020_11_24_100100_update_users_add_truck_flag	1
37	2020_12_16_025647_create_notifications_table	1
38	2021_00_00_000000_create_websockets_statistics_entries_table	1
39	2021_01_07_085422_add_phonenumber_field_to_users_table	1
40	2021_05_12_000100_enableSupervisor	1
41	2021_06_18_000100_initIndexTableCountCache	1
42	2021_07_16_161117_add_all_nets_sidebar_switch_to_global_config_table	1
43	2021_08_09_230446_update_coordinates_in_users_table	1
44	2021_09_30_084314_set_default_to_initial_dashboard	1
45	2021_10_07_165929_install_update_to_php_80	1
46	2021_10_12_142101_install_switch_to_inno_db_barracuda_cacti_compatibility	1
47	2015_10_22_203431_create_DeviceType_table	2
48	2015_11_19_133640_create_Tree_table	2
49	2015_12_15_000100_create_HfcBase_table	2
50	2016_12_07_000100_rename_Tree_to_NetElement_table	2
51	2016_12_29_000200_rename_DeviceTypetoNetElementType_table	2
52	2017_11_20_141835_update_NetElement_table	2
53	2018_03_05_000200_update_NetElementType_autoinc_offset	2
54	2018_03_12_000100_update_NetElementTypeChangeTimeOffset	2
55	2018_07_23_000100_update_NetElementType_add_ups	2
56	2018_07_23_000200_update_NetElement_rename_address3	2
57	2018_08_08_000100_change_engine_to_innodb_for_hfcbase	2
58	2018_08_08_000100_change_engine_to_innodb_for_hfcreq	2
59	2018_11_06_000100_update_Netelement_add_agc_offset	2
60	2019_07_29_000100_update_Netelement_add_id_name	2
61	2019_08_12_000100_updateNetelementRenameCmts	2
62	2019_08_12_000100_updateNetelementTypeRenameCmts	2
63	2020_01_13_110610_set_empty_strings_to_null_hfc_req	2
64	2020_01_15_123421_relationship_fixes_hfc_req	2
65	2020_02_15_000100_updateNetElementRemoveDeprecatedDummyModels	2
66	2020_03_11_000100_updateNetElementTypeAddTap	2
67	2020_03_16_000100_updateNetElementAddSatKabelParts	2
68	2020_03_24_000100_updateHfcBaseAddRkmServer	2
69	2021_02_19_000100_renameTableHfcBaseToHfcReq	2
70	2021_03_29_211536_add_left_right_to_netelement_table	2
71	2021_05_31_235212_set_parent_null_when_deleted_or_notexisting_netelement_at_netelement_table	2
72	2021_06_09_171954_add_netelement_hfc_req_relation_table_indices	2
73	2021_06_09_180913_add_netelementtype_hfc_req_relation_table_indices	2
74	2021_06_11_121652_add_base_type_to_netelementtype_table	2
75	2021_06_17_091438_create_favorite_netelements_table	2
76	2021_08_09_230321_update_coordinates_in_netelement_table	2
77	2021_09_02_155227_change_kml_file_upload_to_generic_gps_file_upload_in_netelement_table	2
78	2021_09_02_173036_rename_kml_file_to_geojson_in_netelement_table	2
79	2021_11_18_073813_move_netelementtype_ids_up_by_defined_margin	2
80	2021_11_28_212436_add_passive_netelementtypes	2
81	2021_11_30_111135_rename_base_type_to_base_type_id_in_netelementtype_table	2
82	2017_09_19_124952_create_Ticket_table	3
83	2017_09_19_125006_create_ticket_user_table	3
84	2017_09_19_125035_create_Comment_table	3
85	2017_12_04_000100_create_TicketType_table	3
86	2017_12_04_000200_create_tickettype_ticket_table	3
87	2017_12_04_000300_update_ticketadaptforTicketTypeMVC	3
88	2018_08_08_000100_change_engine_to_innodb_for_ticketsystem	3
89	2019_01_15_151246_addTicketConfigToGlobalConfig	3
90	2020_01_09_131251_addPropertyManagementRelationsToTicket	3
91	2020_01_13_110734_set_empty_strings_to_null_ticketsystem	3
92	2020_01_15_121538_relationship_fixes_ticket_system	3
93	2020_04_02_114045_renameTickettype_ticket	3
94	2020_09_28_095350_make_tickets_polymorphic_relation	3
95	2020_10_26_094630_create_ticketsystem_table	3
96	2020_11_02_093739_create_auto_ticket_parameter_weights	3
97	2020_11_11_093845_setTicketStateToVarchar	3
98	2020_11_11_102653_rename_ticket_states_to_new_format	3
99	2020_12_11_105757_add_start_and_finish_to_ticket_table	3
100	2015_06_30_110351_create_Modem_table	4
101	2015_07_02_071458_create_Endpoint_table	4
102	2015_07_07_094016_create_Configfile_table	4
103	2015_07_13_135214_create_Qos_table	4
104	2015_10_02_141239_create_Cmts_table	4
105	2015_10_06_144421_create_IpPool_table	4
106	2015_12_02_162244_create_Contract_table	4
107	2015_12_14_100000_create_ProvBase_table	4
108	2016_02_19_093022_update_Modem_xy_field_table	4
109	2016_03_15_000200_update_Contract_for_billing_table	4
110	2016_04_06_000100_update_Contract_for_envia_table	4
111	2016_04_20_000000_update_Contract_for_tariff_information_table	4
112	2016_05_25_000100_update_Modem_for_installation_address_table	4
113	2016_08_03_000100_update_Contract_for_moving_envia_contract_to_modem	4
114	2016_08_03_000200_update_Modem_for_moving_envia_contract_to_modem	4
115	2016_08_03_000400_update_Modem_add_installation_address_change_date	4
116	2016_11_23_000100_update_Contract_uniquenumberfield	4
117	2016_12_05_000100_update_Configfile_add_cvc	4
118	2016_12_08_000200_update_modem_table_rename_tree	4
119	2016_12_09_000100_update_Contract_add_telephony_only	4
120	2017_01_13_000100_create_Domain_table	4
121	2017_05_17_000200_update_Modem_add_hf_values	4
122	2017_07_04_000100_update_Contract_uniquenumberfieldrevert	4
123	2017_09_05_000100_update_Contract_voip_id_nullable	4
124	2017_10_05_000100_update_ProvBaseAddMaxCpe	4
125	2017_12_06_000100_update_Modem_add_tdr	4
126	2018_03_21_000100_update_Endpoint_fixed_ip	4
127	2018_04_05_000100_install_named_ddns	4
128	2018_05_09_000100_add_country_code_fields	4
129	2018_05_22_000100_add_geocode_source_field	4
130	2018_06_15_000100_update_ProvBase_add_rate_coefficient	4
131	2018_06_25_000100_install_dhcpd_rename	4
132	2018_07_16_000200_update_modem_add_model_sw	4
133	2018_07_18_000100_update_ProvBase_add_multiple_provisioning_systems	4
134	2018_08_08_000100_change_engine_to_innodb_for_provbase	4
135	2019_02_13_000100_update_Contract_birthday_nullable	4
136	2019_02_27_000100_update_ContractModemRenameColumns	4
137	2019_03_12_000100_update_ProvBase_add_additional_modem_reset	4
138	2019_04_24_000100_populate_tftpd_cm_log	4
139	2019_04_29_154953_add_modem_page_opening_option	4
140	2019_05_29_014631_add_field_support_state_to_modem_table	4
141	2019_05_29_014716_add_field_support_state_to_cmts_table	4
142	2019_05_29_175211_addTR69Device	4
143	2019_07_18_144744_removeTypeFromConfigfile	4
144	2019_07_31_000100_update_Endpoint_add_reverse	4
145	2019_08_01_000100_installInitRadiusAndAcs	4
146	2019_08_01_000200_add_default_genie_acs_parameter_provisions_and_presets	4
147	2019_08_12_000100_renameCmtsToNetGw	4
148	2019_10_24_000100_update_GeopositionColumnType	4
149	2019_10_29_000100_update_ContractModemAddApartmentNr	4
150	2020_01_07_000100_install_create_salutation_and_academic_degree_files	4
151	2020_01_07_094323_add_configfile_id_to_index	4
152	2020_01_13_110702_set_empty_strings_to_null_prov_base	4
153	2020_01_15_112423_relationship_fixes_prov_base	4
154	2020_01_23_111514_set_index_for_netelements_in_modem_table	4
155	2020_01_27_134046_remove_cmts_abilities	4
156	2020_02_12_000100_update_endpoint_make_ip_nullable	4
157	2020_03_13_000100_installSetFsHostname	4
158	2020_03_20_115843_delete_cvc	4
159	2020_03_25_000100_addNetGwCredentials	4
160	2020_04_07_000100_addDsUsName	4
161	2020_04_11_000100_useSqlippool	4
162	2020_04_12_000100_addRandomIpAllocation	4
163	2020_06_16_000100_installDisableRadiusDetailLogging	4
164	2020_06_22_000100_updateModemMacNullable	4
165	2020_06_24_000100_update_provmon_store_dns_key	4
166	2020_07_08_000100_addNetGwCoAPort	4
167	2020_08_17_000100_updateIpPoolAddV6	4
168	2020_08_19_000100_updateNetGwAddIpv6	4
169	2020_08_26_000100_updateEndpointMacNullable	4
170	2020_09_02_000100_updateEndpointAddV6	4
171	2020_09_14_000100_addPPPSessionTimeout	4
172	2020_09_29_000100_installRestartNMSD	4
173	2020_10_21_000100_updateConfigfileAddMonitoring	4
174	2020_11_05_000100_installKea	4
175	2020_11_28_000100_updateContractAddAdditionalInfo	4
176	2021_01_11_000100_addAutoFactoryReset	4
177	2021_01_18_000100_addAcctInterimInterval	4
178	2021_01_25_103835_addGroundForDismissalToContract	4
179	2021_02_12_000100_installUpdate254To300	4
180	2021_02_18_000100_updateContractPhonenumberNullable	4
181	2021_03_16_105242_add_passive_element_relation_to_modem_table	4
182	2021_05_31_000100_updateContractSetNullableColumnsToNull	4
183	2021_06_09_155202_add_modem_prov_base_relation_table_indices	4
184	2021_06_09_155211_add_contract_prov_base_relation_table_indices	4
185	2021_06_09_170308_add_configfile_prov_base_relation_table_indices	4
186	2021_06_09_170651_add_endpoint_prov_base_relation_table_indices	4
187	2021_06_09_170948_add_ippool_prov_base_relation_table_indices	4
188	2021_06_16_000100_update_configfile_add_dashboard	4
189	2021_06_22_000100_updateContractAddIndex	4
190	2021_06_30_000100_update_ip_pool_add_active	4
191	2021_08_09_230411_update_coordinates_in_modem_table	4
192	2021_08_10_004202_update_coordinates_in_contract_table	4
193	2021_08_23_000100_update_modem_add_phy_timestamp	4
194	2021_08_24_000100_update_modem_add_ipv4	4
195	2021_10_04_000100_updateContractAddGroupContractFlag	4
196	2021_10_26_153853_update_net_gw_add_ssh_port	4
197	2021_12_06_000100_update_configfile_dashboard_nullable	4
198	2022_01_31_100645_create_modem_option_table	4
199	2022_02_18_142850_update_monitoring_presets	4
200	2022_03_07_00100_update_DhcpDirectoryPermissions	4
201	2015_10_07_093323_create_Mta_table	5
202	2015_10_21_092701_create_Phonenumber_table	5
203	2015_12_15_000200_create_ProvVoip_table	5
204	2015_12_21_000001_create_PhonenumberManagement_table	5
205	2016_02_10_170000_create_CarrierCode_table	5
206	2016_04_20_000001_create_PhoneTariff_table	5
207	2016_05_04_000100_create_PhonebookEntry_table	5
208	2016_05_24_000100_update_PhonenumberManagement_for_ekpcodes_table	5
209	2016_05_24_170000_create_EkpCode_table	5
210	2016_06_08_000100_update_PhonenumberManagement_for_nullable_dates	5
211	2016_06_28_000100_update_PhonenumberManagement_for_external_dates	5
212	2016_08_10_000100_update_PhonenumberManagement_for_subscriber_district	5
213	2016_10_26_000001_create_TRCClassMovedToProvVoip_table	5
214	2016_11_07_000100_update_ProvVoip_table_add_mta_domain	5
215	2016_11_30_000100_update_PhonenumberManagement_for_autogenerate_marker	5
216	2017_04_25_000100_update_Phonenumber_for_storing_external_contract_reference	5
217	2017_05_10_000100_update_PhonenumberManagement_for_enviacontract_id	5
218	2017_05_24_000001_update_PhoneTariff_table_add_method_col	5
219	2017_06_21_000001_update_TRCClass_table_make_trc_id_nullable	5
220	2018_05_17_000100_update_PhonebookEntry_rename_usage_col	5
221	2018_08_08_000100_change_engine_to_innodb_for_provvoip	5
222	2018_08_15_000100_update_PhonebookEntry_table_add_salutation_col	5
223	2019_06_21_164239_modify_datatype_country_code_field_phonenumber_table	5
224	2020_01_07_094323_add_configfile_id_to_index_mta	5
225	2020_01_13_110716_set_empty_strings_to_null_prov_voip	5
226	2020_02_22_000100_updateMtaMacNullable	5
227	2020_07_15_000100_addDefaultRegistarCountryCode	5
228	2020_10_22_000010_updatePhoneTariffExtendType	5
229	2021_06_09_155232_add_mta_table_indices	5
230	2021_06_09_155240_add_phonenumber_table_indices	5
231	2018_05_18_000100_install_php_config_provmon	6
232	2018_08_08_000100_change_engine_to_innodb_for_provmon	6
233	2018_09_06_000100_install_update_cisco_cmts_template	6
234	2019_03_20_000100_install_set_charset_cacti	6
235	2019_07_22_000100_install_use_modempoller_nmsprime	6
236	2020_01_13_110706_set_empty_strings_to_null_prov_mon	6
237	2020_03_26_153025_create_provmon_table	6
238	2021_04_20_000100_updateModemIps	6
239	2021_04_20_000100_useCactiBoost	6
240	2021_06_22_000100_use_cmts_poller	6
241	2021_06_30_000100_use_timescaledb_and_grafana	6
242	2021_09_01_000100_disable_cacti_modem_monitoring	6
243	2021_09_29_161526_create_temp_psql_table_for_modem_metadata	6
244	2021_11_08_103704_set_grafana_secret	6
245	2021_12_17_134027_install_switch_to_inno_db_barracuda_for_cacti_compatibility	6
246	2016_02_19_135607_create_mpr_table	7
247	2016_02_19_143809_create_mprgeopos_table	7
248	2016_12_08_000100_update_mpr_table_rename_tree	7
249	2018_08_08_000100_change_engine_to_innodb_for_hfccustomer	7
250	2020_01_13_110557_set_empty_strings_to_null_hfc_customer	7
251	2020_01_15_123440_relationship_fixes_hfc_customer	7
252	2021_06_09_181059_add_mpr_hfc_req_relation_table_indices	7
253	2021_06_09_212726_add_mprgeopos_relation_table_indices	7
254	2021_07_06_000100_update_mpr_table_reorder_and_remove_prio_fields	7
255	2021_08_10_014552_update_coordinates_in_mprgeopos_table	7
256	2016_02_24_100223_create_Product_table	8
257	2016_02_25_161317_create_Item_table	8
258	2016_03_07_150000_create_AccountingRecord_table	8
259	2016_03_09_142605_create_SepaMandate_table	8
260	2016_03_16_160707_create_SepaAccount_table	8
261	2016_03_16_161851_create_CostCenter_table	8
262	2016_03_16_163414_create_BillingBase_table	8
263	2016_04_04_164302_create_Company_table	8
264	2016_04_19_166555_create_Salesman_table	8
265	2016_05_27_100000_create_SettlementRun_table	8
266	2016_06_15_000100_update_Product_table_for_bundled_flag	8
267	2016_06_15_000200_update_Item_table_for_fixed_dates_flags	8
268	2016_07_27_000100_update_CostCenter_table_bugfix	8
269	2016_09_08_164300_update_BillingBase_table	8
270	2016_10_10_115738_create_Invoice_table	8
271	2016_10_18_100100_update_BillingBaseaddCdrOffset_table	8
272	2016_11_14_000100_update_BillingBaseaddVoipPrices	8
273	2017_01_13_00000_update_Product_add_email	8
274	2017_05_18_000100_update_CostCenterMvInvoiceNrStart	8
275	2017_06_02_000100_update_CompanyAddConnInfoTemplate	8
276	2017_06_12_000100_update_ItemIncreaseDecimalCountOfCreditAmount	8
277	2017_09_11_122729_create_NumberRange_table	8
278	2017_10_26_000100_update_SepaMandateAddCostCenterID	8
279	2018_01_30_000100_update_BillingBaseaddRetentionPeriod	8
280	2018_02_01_000100_update_ProductaddMaturityandPeriodofNotice	8
281	2018_02_21_000100_update_BillingBaseaddFluidDatesFlag	8
282	2018_05_02_000100_update_BillingBaseaddAGsCheckbox	8
283	2018_05_28_001000_update_InvoiceaddSepaAccountID	8
284	2018_07_18_001000_update_AccountingRecordRenameColumn	8
285	2018_07_25_001000_update_ItemAddIndex	8
286	2018_08_08_000100_change_engine_to_innodb_for_billingbase	8
287	2018_10_02_001000_update_ItemCorrectCount	8
288	2018_11_06_001000_update_ProductAddMaturityMin	8
289	2019_01_11_104933_changeProductTypesToPostalDelivery	8
290	2019_02_18_001000_update_ProductAddProportional	8
291	2019_02_21_001000_update_TablesAddIndex	8
292	2019_03_14_001000_update_SettlementRunAddFullRunFlag	8
293	2019_05_21_000100_renameSepaMandateColumns	8
294	2019_05_27_001000_update_SettlementRunAddUploadTimestamp	8
295	2019_05_28_000100_update_ContractaddValueDate	8
296	2019_06_13_000100_update_SettlementRunAddExecutionTimestamp	8
297	2019_07_31_000100_update_NumberRangeSetDefaults	8
298	2019_09_24_001000_changeFloatToDecimal	8
299	2019_10_11_001000_update_ItemIncreaseCount	8
300	2019_10_16_000100_update_BillingBaseaddAdaptItemFlag	8
301	2019_11_07_001000_update_ProductItemIncreaseDecimalCount	8
302	2019_12_10_000100_update_ProductAddRecordMonthlyFlag	8
303	2019_12_17_000100_update_ModemAddAddressToInvoiceFlag	8
304	2020_01_13_110506_set_empty_strings_to_null_billing_base	8
305	2020_01_15_114742_relationship_fixes_billing_base	8
306	2020_01_16_103046_addDeprecatedProductColumn	8
307	2020_03_11_000100_updateAccountingRecordAddRelation	8
308	2020_05_12_000100_update_InvoiceaddChargeGross	8
309	2020_06_16_000100_updateBillingBaseChangeCurrencyColumn	8
310	2020_06_16_000200_insertBillingDefaultModels	8
311	2020_10_29_000100_update_ProductAddMarkon	8
312	2021_05_31_000100_updateProductSetNullableColumnsToNull	8
313	2021_11_29_000100_updateItemAddSmartCardIds	8
314	2021_12_06_000100_updateItemFixSmartCardIds	8
315	2019_04_16_001000_create_Debt_table	9
316	2019_05_28_000100_create_Dunning_table	9
317	2019_08_14_000100_update_DebtAddColumns	9
318	2019_09_02_000100_update_DebtAddNewColumns	9
319	2019_09_11_000100_rename_DunningToOverdueDebts	9
320	2019_09_19_000100_update_OverdueDebtsAddColumns	9
321	2019_09_24_000100_update_DebtChangeColumns	9
322	2020_01_13_110640_set_empty_strings_to_null_overdue_debts	9
323	2020_01_15_122431_relationship_fixes_overdue_debts	9
324	2020_01_15_122921_update_overdue_debts_table	9
325	2021_12_09_001000_updateDebtTableChangeColumns	9
326	2021_12_06_095327_fix_initial_dashboard_workforce_in_users_table	10
327	2016_01_26_000001_create_EnviaOrder_table	11
328	2016_03_02_000001_create_TRCClass_table	11
329	2016_03_15_000001_create_EnviaOrderDocument_table	11
330	2016_06_28_000100_update_EnviaOrder_for_method	11
331	2016_08_03_000300_update_EnviaOrder_extend_with_modem_id	11
332	2016_12_06_000100_create_EnviaOrderPhonenumber_table	11
333	2016_12_06_000200_update_EnviaOrder_remove_phonenumber_id	11
334	2017_05_09_000100_create_EnviaContract_table	11
335	2017_05_10_000200_update_EnviaOrder_add_enviacontract_id	11
336	2017_08_15_000000_update_EnviaContract_add_state	11
337	2018_08_08_000100_change_engine_to_innodb_for_provvoipenvia	11
338	2019_05_21_000100_update_EnviaContract_change_tariff_variation_type	11
339	2020_01_13_110726_set_empty_strings_to_null_prov_voip_envia	11
340	2016_05_13_155022_create_CccAuthuser_table	12
341	2016_06_06_143447_create_Ccc_table	12
342	2016_10_18_100000_update_CccaddHeadline_table	12
343	2016_11_22_000100_update_CccAuthusersRename_table	12
344	2017_10_04_000100_update_CccAuthuserMove_table	12
345	2018_01_31_000100_update_CccAuthuserUndoUnique	12
346	2018_08_08_000100_change_engine_to_innodb_for_ccc	12
347	2018_10_12_112957_edit_Ccc_table	12
348	2019_07_31_000100_update_CccAuthuserSetNullableFields	12
349	2020_01_13_110520_set_empty_strings_to_null_ccc	12
350	2020_03_18_000100_update_CccAuthuserMakeNameFieldsNullable	12
351	2020_12_11_000100_update_CccAddLogo	12
352	2021_04_19_000100_updateCccAddSpeedtestUrl	12
353	2021_07_16_000100_update_CccAuthuser_add_newsletter	12
354	2015_10_22_154622_create_SnmpMib_table	13
355	2015_10_30_201531_create_SnmpValue_table	13
356	2016_12_15_000100_rename_SnmpMibtoOID_table	13
357	2016_12_15_000200_create_MibFile_table	13
358	2016_12_15_000300_update_SnmpValue_table	13
359	2016_12_16_000400_create_Parameter_table	13
360	2017_03_06_000100_create_Indices_table	13
361	2018_02_22_000100_update_ParameterChangeColumn	13
362	2018_02_22_000200_update_IndicesExtendStringLength	13
363	2018_02_26_000100_drop_SnmpValueTable	13
364	2018_03_05_000200_update_Parameter_autoinc_offset	13
365	2018_08_08_000100_change_engine_to_innodb_for_hfcsnmp	13
366	2020_01_13_110614_set_empty_strings_to_null_hfc_snmp	13
367	2020_01_15_123508_relationship_fixes_hfc_snmp	13
368	2020_05_29_000100_updateOIDChangeStepsize	13
369	2020_09_02_000100_updateNetElementTypeAddHaSlave	13
370	2018_08_08_000100_change_engine_to_innodb_for_dashboard	14
371	2018_10_22_143505_createContractsToJson	14
372	2020_01_13_110529_set_empty_strings_to_null_dashboard	14
373	2022_01_18_092957_run_modem_statistic_command_to_update_json	14
374	2021_09_20_000100_switchMysqlToPgsql	15
375	2022_03_25_000100_AddRkmServerAsNetelementtype	15
376	2022_04_01_000100_updateNetElementTypeAddSidebarPos	15
377	2022_04_08_000100_insertNetelementtypesForCoreMon	15
378	2022_03_22_00100_connectRadiusAndNmsprimeDb	16
379	2022_04_20_000100_drop_transitional_schema	17
380	2022_08_18_111451_add_additional_field_to_contract_and_modem	18
381	2022_11_18_000100_logrotate_mongod	18
382	2022_04_29_000100_updateGlobalConfigAddLoginBackgroundImages	19
383	2022_04_29_105230_createRpdTable	19
384	2022_05_09_000100_create_link_table	19
385	2022_05_25_080836_create_cpe_table	19
386	2022_05_30_105851_additional_rpd_columns	19
387	2022_06_20_092446_create_coremon_tables	19
388	2022_06_24_085358_add_theme_color_column_to_users_table	19
389	2022_07_04_000100_update_ccap_add_summary_cols	19
390	2022_07_04_000200_create_inventory_table	19
391	2022_07_04_000300_create_l2tp_tunnel_table	19
392	2022_07_08_082200_create_alarm_table	19
393	2022_07_19_000100_create_scan_range	19
394	2022_07_21_113720_add_base_netelement_type_id_to_netelement_table	19
395	2022_07_21_130526_change_netelement_type_to_nested_set	19
396	2022_07_26_115157_create_webhook_user	19
397	2022_08_04_000100_change_ip_pool_type_convert_enum_add_vendor_class_identifier	19
398	2022_08_16_000136_install_add_linux_user_nmsprime	19
399	2022_08_22_000100_update_link_add_stats	19
400	2022_08_24_115739_create_arp_table	19
401	2022_08_25_160640_create_dpic_interface_table	19
402	2022_09_06_103337_add_uuid_to_failed_jobs_table	19
403	2022_09_07_155559_create_dpic_card_table	19
404	2022_09_09_102605_create_fiber_node_table	19
405	2022_09_21_181810_add_fiber_name_field_to_modem_table	19
406	2022_09_27_105343_create_symlink_topology_device	19
407	2022_10_06_102605_add_last_amendment_to_contract_table	19
408	2022_10_12_073655_create_temperature_table	19
409	2022_10_12_134555_add_internal_index_column_to_cpe_table	19
410	2022_10_12_134555_create_sensor_table	19
411	2022_10_19_000100_add_online_field_to_netelement_table	19
412	2022_10_21_000100_create_fiber_node_rpd_pivot_table	19
413	2022_10_21_000200_add_number_cms_offline_to_ccap	19
414	2022_10_21_000300_add_mdd_status_to_fiber_node	19
415	2022_10_25_223907_add_deviceType_model_city_site_to_ccap	19
416	2022_11_07_000100_create_coremon_table	19
417	2022_11_10_095943_change_cpe_table_rpd_ccap_relations	19
418	2022_11_12_095943_add_cpe_device_count_to_ccap	19
419	2022_11_14_114226_change_dpic_card_table_and_add_internal_index	19
420	2022_11_18_130247_add_extra_fields_to_dpa_table	19
421	2022_11_20_145236_add_system_information_fields_to_dpa_table	19
422	2022_11_22_000100_remove_notif_mail_from_provbase	19
423	2022_11_25_000100_add_fields_to_rpd_table	19
424	2022_11_25_164916_alter_type_of_sw_ver_ccap_table	19
425	2022_11_30_133337_create_lldp_table	19
426	2022_12_01_144807_add_interface_index_to_interface_table	19
427	2022_12_02_135026_rename_dpic_interface_table	19
428	2022_12_03_153035_add_multiple_columns_to_netelement_interface	19
429	2022_12_09_000100_create_oui_table	19
430	2022_12_09_100100_update_cpe_remove_fiber_node_columns	19
431	2022_12_09_175421_add_system_information_fields_to_rpa_table	19
432	2022_12_12_111406_add_summary_metrics_fields_to_rpa_table	19
433	2022_12_13_145201_add_extra_fields_to_sensor_table	19
434	2022_12_13_150136_set_nullable_temperature_id_and_unit_sensor_table	19
435	2022_12_16_140146_add_rpa_dpa_columns_to_lldp_table	19
436	2022_12_19_130308_add_summary_metrics_fields_to_ncs_table	19
437	2022_12_19_130346_add_system_info_fields_to_ncs_table	19
438	2022_12_20_000100_update_rpd_fix_unique_constraints	19
439	2022_12_23_000100_update_fiber_node_rpd_add_id	19
440	2023_01_09_110027_add_internal_id_to_rpd_table	19
441	2023_01_11_134503_change_precision_value_sensor_table	19
442	2023_01_12_112709_adjust_sensors_table_for_rpd	19
443	2023_01_17_132209_alter_type_of_cpu_and_memory_utilization_ncs_table	19
444	2023_01_17_164333_add_rate_to_sensor_table	19
445	2023_01_19_150416_create_rpd_sessions_table	19
446	2023_01_24_000100_update_rpd_add_service_template	19
447	2023_01_24_124054_add_rpd_columns_for_netelement_interface	19
448	2023_02_01_085714_add_unique_key_for_arp	19
449	2023_02_01_150608_change_ip_type_to_inet_netelement_table	19
450	2023_02_02_093000_adjust_unique_key_and_fix_nullable_dpic_card	19
451	2023_02_03_045858_adjust_dpic_card_table	19
452	2023_02_03_151035_change_unique_key_for_arp	19
453	2023_02_07_112202_add_unique_key_for_rpd_interfaces	19
454	2023_02_08_102427_change_type_of_internal_id_sensor_table	19
455	2023_02_10_010200_create_dpic_card_to_netelement_interface_relation	19
456	2023_02_13_112000_change_unique_key_of_netelement_interfaces	19
457	2023_02_14_113542_add_city_and_site_to_dpa_table	19
458	2023_02_14_134141_add_city_and_site_to_ncs_table	19
459	2023_02_14_141337_add_city_and_site_to_rpa_table	19
460	2023_02_16_092826_change_type_of_uptime_ccap_table	19
461	2023_02_17_112535_add_ip_to_rpd_session_table	19
462	2023_02_21_061245_add_oid_polling_time_to_netelement_interface_table	19
463	2023_03_02_172703_add_redundancy_to_ncs_table	19
464	2023_03_03_045525_alter_precision_for_threshold_column_in_temperature_table	19
465	2023_03_03_132516_create_voipmon_table	19
466	2023_03_22_110310_add_port_field_to_netelement_table	19
467	2022_07_21_125420_change_netelementtype_to_nested_set	20
468	2022_05_16_074835_set_icinga_api_password_in_env	21
469	2023_03_17_154939_add_geodata_to_contract_table	22
470	2023_03_30_130630_create_table_address	22
471	2022_01_18_173823_update_contract_add_smartont_fields	23
472	2022_02_15_000100_addOntDevice	23
473	2022_02_16_000100_update_modem_add_ontid	23
474	2022_03_09_000100_update_qos_add_ont_fields	23
475	2022_03_15_000100_update_modem_add_ont_fields	23
476	2022_03_23_000100_update_qos_add_more_ont_fields	23
477	2022_03_29_000100_update_netgw_add_internal_id	23
478	2022_04_27_000100_update_modem_add_ont_state_fields	23
479	2022_05_24_000100_update_configfile_add_service_profile_id	23
480	2022_06_07_000100_update_endpoint_add_smartont_fields	23
481	2022_06_29_000100_update_endpoint_add_smartont_fields_acl_rule	23
482	2022_06_29_000200_update_configfile_add_multiservice	23
483	2022_07_06_000100_update_configfile_add_smartont_fields_tr069profile_ontlineprofile	23
484	2022_07_13_000100_update_endpoint_add_smartont_field_state	23
485	2022_01_19_154141_create_dfsubscriptionevent_table	24
486	2022_01_19_154168_create_dfsubscription_table	24
487	2022_02_09_153215_create_SmartOnt_table	25
488	2022_02_15_000200_createGenericOntConfigfile	25
489	2022_04_12_000100_udpdate_SmartOnt_table_add_remove_fields	25
490	2022_04_27_000100_udpdate_SmartOnt_table_add_default_qos_configfile	26
491	2022_07_13_000100_udpdate_SmartOnt_table_add_default_mgmt_qos	27
\.


--
-- Data for Name: modem; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.modem (id, created_at, updated_at, deleted_at, name, hostname, contract_id, contract_external_id, contract_ext_creation_date, contract_ext_termination_date, salutation, company, department, firstname, lastname, street, house_number, zip, city, district, birthday, country_id, country_code, installation_address_change_date, mac, us_pwr, us_snr, ds_pwr, ds_snr, public, internet_access, serial_num, inventar_num, description, parent, configfile_id, netelement_id, qos_id, lng, lat, geocode_source, number, tdr, fft_max, model, sw_rev, support_state, ppp_username, ppp_password, apartment_nr, next_passive_id, phy_updated_at, address_to_invoice, apartment_id, ipv4, additional, fiber_name, ont_id, netgw_id, frame_id, slot_id, port_id, service_port_id, or_id, ont_state, next_ont_state, ont_state_switchdate) FROM stdin;
\.


--
-- Data for Name: modem_option; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.modem_option (id, created_at, updated_at, deleted_at, modem_id, key, value) FROM stdin;
\.


--
-- Data for Name: mpr; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.mpr (id, created_at, updated_at, deleted_at, name, value, netelement_id, description) FROM stdin;
\.


--
-- Data for Name: mprgeopos; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.mprgeopos (id, created_at, updated_at, deleted_at, mpr_id, lng, lat) FROM stdin;
\.


--
-- Data for Name: mta; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.mta (id, created_at, updated_at, deleted_at, modem_id, mac, hostname, configfile_id, type, is_dummy) FROM stdin;
1	\N	\N	2022-05-06 12:26:03+02	1	\N	dummy-mta-sip	1	sip	t
2	\N	\N	2022-05-06 12:26:03+02	1	\N	dummy-mta-packetcable	1	packetcable	t
\.


--
-- Data for Name: ncs; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ncs (id, created_at, updated_at, deleted_at, netelement_id, cpu_utilization, memory_utilization, sys_uptime, serial_number, model, sw_ver, city, site, redundancy) FROM stdin;
\.


--
-- Data for Name: net; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.net (id, created_at, updated_at, deleted_at, netelement_id) FROM stdin;
\.


--
-- Data for Name: netelement; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.netelement (id, created_at, updated_at, deleted_at, name, series, options, ip, lng, lat, link, "user", access, net, cluster, layer, descr, infrastructure_file, draw, line, parent_id, netelementtype_id, community_ro, community_rw, address1, address2, controlling_link, prov_device_id, netgw_id, agc_offset, rkm_line_number, state, _lft, _rgt, apartment_id, username, password, base_type_id, online, port) FROM stdin;
\.


--
-- Data for Name: netelement_interface; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.netelement_interface (id, created_at, updated_at, deleted_at, name, ip, admin_status, operational_status, netelement_id, if_index, mac, total_bw, inbound_rate, outbound_rate, prev_outbound_counter, prev_inbound_counter, total_util, inbound_util, outbound_util, total_error_ratio, inbound_error_ratio, outbound_error_ratio, prev_inbound_error_counter, prev_outbound_error_counter, inbound_frame_rate, outbound_frame_rate, inbound_unicast_rate, outbound_unicast_rate, inbound_unicast_frame_rate, outbound_unicast_frame_rate, inbound_multicast_rate, outbound_multicast_rate, inbound_multicast_frame_rate, outbound_multicast_frame_rate, inbound_broadcast_rate, outbound_broadcast_rate, inbound_broadcast_frame_rate, outbound_broadcast_frame_rate, inbound_discard_rate, outbound_discard_rate, prev_inbound_frame_counter, prev_outbound_frame_counter, prev_inbound_unicast_counter, prev_outbound_unicast_counter, prev_inbound_unicast_frame_counter, prev_outbound_unicast_frame_counter, prev_inbound_multicast_counter, prev_outbound_multicast_counter, prev_inbound_multicast_frame_counter, prev_outbound_multicast_frame_counter, prev_inbound_broadcast_counter, prev_outbound_broadcast_counter, prev_inbound_broadcast_frame_counter, prev_outbound_broadcast_frame_counter, prev_inbound_discard_counter, prev_outbound_discard_counter, dpic_card_id, prev_oid_polling_timestamp) FROM stdin;
\.


--
-- Data for Name: netelementtype; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.netelementtype (id, created_at, updated_at, deleted_at, name, vendor, version, description, parent_id, icon_name, pre_conf_oid_id, pre_conf_value, pre_conf_time_offset, page_reload_time, base_type_id, sidebar_pos, _lft, _rgt) FROM stdin;
3	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	NetGw	\N	\N	\N	\N	\N	\N	\N	0	0	3	\N	1	2
4	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	Amplifier	\N	\N	\N	\N	\N	\N	\N	0	0	4	\N	3	4
5	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	Node	\N	\N	\N	\N	\N	\N	\N	0	0	5	\N	5	6
6	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	Data	\N	\N	\N	\N	\N	\N	\N	0	0	6	\N	7	8
7	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	UPS	\N	\N	\N	\N	\N	\N	\N	0	0	7	\N	9	10
9	2022-05-06 12:25:18+02	2023-04-05 16:59:54+02	\N	Tap-Port	\N	\N	\N	8	\N	\N	\N	\N	\N	9	\N	12	13
8	2022-05-06 12:25:18+02	2023-04-05 16:59:54+02	\N	Tap	\N	\N	\N	\N	\N	\N	\N	\N	\N	8	\N	11	14
10	2022-05-06 12:28:48+02	2023-04-05 16:59:54+02	\N	NMSPrime HA slave	\N	\N	\N	\N	\N	\N	\N	\N	\N	0	\N	15	16
12	2022-05-06 12:25:19+02	2023-04-05 16:59:54+02	\N	Splitter	\N	\N	\N	11	\N	\N	\N	\N	\N	11	\N	18	19
13	2022-05-06 12:25:19+02	2023-04-05 16:59:54+02	\N	Amplifier	\N	\N	\N	11	\N	\N	\N	\N	\N	11	\N	20	21
14	2022-05-06 12:25:19+02	2023-04-05 16:59:54+02	\N	Node	\N	\N	\N	11	\N	\N	\N	\N	\N	11	\N	22	23
11	2022-05-06 12:25:19+02	2023-04-05 16:59:54+02	\N	Passives	\N	\N	\N	\N	\N	\N	\N	\N	\N	11	\N	17	24
15	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	RKM-Server	SAT-Kabel	\N	\N	\N	\N	\N	\N	\N	\N	15	\N	25	26
2	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	Cluster	\N	\N	\N	1	\N	\N	\N	0	0	2	\N	28	29
1	2022-05-06 12:25:17+02	2023-04-05 16:59:54+02	\N	Net	\N	\N	\N	\N	\N	\N	\N	0	0	1	1	27	30
16	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	Market	\N	\N	\N	\N	\N	\N	\N	\N	\N	16	2	31	32
17	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	Hub	\N	\N	\N	\N	\N	\N	\N	\N	\N	17	3	33	34
18	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	CCAP Core	\N	\N	\N	\N	\N	\N	\N	\N	\N	18	4	35	36
19	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	Core Leaf	\N	\N	\N	\N	\N	\N	\N	\N	\N	19	5	37	38
20	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	Spine	\N	\N	\N	\N	\N	\N	\N	\N	\N	20	6	39	40
21	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	Node Leaf	\N	\N	\N	\N	\N	\N	\N	\N	\N	21	7	41	42
22	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	RPD	\N	\N	\N	\N	\N	\N	\N	\N	\N	22	8	43	44
23	2022-05-06 15:54:01+02	2023-04-05 16:59:54+02	\N	CM	\N	\N	\N	\N	\N	\N	\N	\N	\N	23	9	45	46
\.


--
-- Data for Name: netgw; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.netgw (id, created_at, updated_at, deleted_at, hostname, series, ip, community_rw, community_ro, company, network, state, monitoring, support_state, type, username, password, ssh_port, ssh_auto_prov, coa_port, ipv6, nas_secret, internal_id) FROM stdin;
\.


--
-- Data for Name: node; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.node (id, created_at, updated_at, deleted_at, netelement_id, name, street, house_nr, zip, city, type, headend, description, lng, lat, country_code, geocode_source, district) FROM stdin;
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: numberrange; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.numberrange (id, created_at, updated_at, deleted_at, name, start, "end", prefix, suffix, costcenter_id, type) FROM stdin;
\.


--
-- Data for Name: oid; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.oid (id, created_at, updated_at, deleted_at, mibfile_id, html_type, name, oid, oid_table, type, type_array, phpcode_pre, phpcode_post, description, name_gui, unit_divisor, startvalue, endvalue, stepsize, syntax, access, value_set) FROM stdin;
\.


--
-- Data for Name: oui; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.oui (id, oui, name) FROM stdin;
\.


--
-- Data for Name: overduedebts; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.overduedebts (id, created_at, updated_at, deleted_at, fee, total, dunning_charge1, dunning_charge2, dunning_charge3, dunning_text1, dunning_text2, dunning_text3, payment_period, import_inet_block_amount, import_inet_block_debts, import_inet_block_indicator) FROM stdin;
1	\N	\N	\N	0.0000	f	\N	\N	\N	\N	\N	\N	14D	\N	\N	\N
\.


--
-- Data for Name: parameter; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.parameter (id, created_at, updated_at, deleted_at, netelementtype_id, oid_id, diff_param, divide_by, parent_id, third_dimension, html_frame, html_properties, html_id) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.permissions (ability_id, entity_id, entity_type, forbidden, scope, created_at, updated_at) FROM stdin;
1	1	App\\User	f	\N	\N	\N
2	1	roles	f	\N	\N	\N
2	3	roles	f	\N	\N	\N
3	4	roles	f	\N	\N	\N
4	3	roles	t	\N	\N	\N
5	3	roles	t	\N	\N	\N
6	3	roles	t	\N	\N	\N
7	4	roles	f	\N	\N	\N
8	5	roles	f	\N	\N	\N
9	5	roles	f	\N	\N	\N
\.


--
-- Data for Name: phonebookentry; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.phonebookentry (id, created_at, updated_at, deleted_at, phonenumbermanagement_id, reverse_search, publish_in_print_media, publish_in_electronic_media, directory_assistance, entry_type, publish_address, company, salutation, academic_degree, noble_rank, nobiliary_particle, lastname, other_name_suffix, firstname, street, houseno, zipcode, city, urban_district, business, usage, tag, external_creation_date, external_update_date) FROM stdin;
\.


--
-- Data for Name: phonenumber; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.phonenumber (id, created_at, updated_at, deleted_at, mta_id, port, country_code, prefix_number, number, username, password, sipdomain, active, is_dummy, contract_external_id) FROM stdin;
300000	\N	\N	2022-05-06 12:26:03+02	1	0	0049	0000	00000		\N		t	t	\N
\.


--
-- Data for Name: phonenumbermanagement; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.phonenumbermanagement (id, created_at, updated_at, deleted_at, phonenumber_id, trcclass, voipaccount_ext_creation_date, activation_date, external_activation_date, porting_in, carrier_in, ekp_in, deactivation_date, external_deactivation_date, voipaccount_ext_termination_date, porting_out, carrier_out, ekp_out, subscriber_company, subscriber_department, subscriber_salutation, subscriber_academic_degree, subscriber_firstname, subscriber_lastname, subscriber_street, subscriber_house_number, subscriber_zip, subscriber_city, subscriber_district, subscriber_country, autogenerated, enviacontract_id) FROM stdin;
\.


--
-- Data for Name: phonetariff; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.phonetariff (id, created_at, updated_at, deleted_at, external_identifier, name, type, description, usable, voip_protocol) FROM stdin;
1	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	1	Basic	basic	\N	t	SIP
2	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	2	Landline flat	landlineflat	\N	t	SIP
3	2022-05-06 12:26:06+02	2022-05-06 12:26:06+02	\N	3	Allnetflat	allnetflat	\N	t	SIP
\.


--
-- Data for Name: product; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.product (id, created_at, updated_at, deleted_at, name, type, qos_id, voip_sales_tariff_id, voip_purchase_tariff_id, billing_cycle, maturity, costcenter_id, price, tax, bundled_with_voip, email_count, period_of_notice, maturity_min, proportional, record_monthly, deprecated, markon) FROM stdin;
\.


--
-- Data for Name: provbase; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.provbase (id, created_at, updated_at, deleted_at, provisioning_server, ro_community, rw_community, domain_name, dns_password, dhcp_def_lease_time, dhcp_max_lease_time, startid_contract, startid_modem, startid_endpoint, max_cpe, ds_rate_coefficient, us_rate_coefficient, multiple_provisioning_systems, additional_modem_reset, modem_edit_page_new_tab, random_ip_allocation, ppp_session_timeout, auto_factory_reset, acct_interim_interval) FROM stdin;
1	\N	2022-05-06 12:25:49+02	\N	172.20.0.1	public	private	nmsprime.test	fe1fQGXpZYma9wH/+vNyLA==	86400	172800	0	0	0	2	1	1	f	f	f	f	86400	f	300
\.


--
-- Data for Name: provmon; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.provmon (id, created_at, updated_at, deleted_at, start_frequency, stop_frequency, span) FROM stdin;
1	\N	\N	\N	154	866	8
\.


--
-- Data for Name: provvoip; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.provvoip (id, created_at, updated_at, deleted_at, startid_mta, mta_domain, default_sip_registrar, default_country_code) FROM stdin;
1	\N	\N	\N	300000	\N	\N	\N
\.


--
-- Data for Name: qos; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.qos (id, created_at, updated_at, deleted_at, ds_rate_max, us_rate_max, ds_rate_max_help, us_rate_max_help, name, ds_name, us_name, type, vlan_id, ont_line_profile_id, service_profile_id, gem_port, traffic_table_in, traffic_table_out) FROM stdin;
\.


--
-- Data for Name: realty; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.realty (id, created_at, updated_at, deleted_at, node_id, name, number, street, house_nr, district, zip, city, expansion_degree, concession_agreement, agreement_from, agreement_to, last_restoration_on, description, contact_id, contact_local_id, lng, lat, geocode_source, country_code, contract_id) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.roles (id, name, title, description, rank, level, scope, created_at, updated_at, deleted_at) FROM stdin;
1	admin	Admin	Is allowed to do everything. Used for the initial user which can add other users.	101	\N	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
2	every_net	EveryNet	Is allowed to access every net. Used for the initial user which can add other users.	99	\N	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
3	support	Support	\N	0	\N	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
4	guest	Guest	\N	0	\N	\N	2022-05-06 12:25:06+02	2022-05-06 12:25:06+02	\N
5	accounting	Accounting	Only accounting relevant stuff	40	\N	\N	2022-05-06 12:25:13+02	2022-05-06 12:25:13+02	\N
\.


--
-- Data for Name: rpa; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.rpa (id, created_at, updated_at, deleted_at, netelement_id, sys_uptime, serial_number, model, last_conf_change, sw_ver, cpu_utilization, memory_utilization, city, site) FROM stdin;
\.


--
-- Data for Name: rpd; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.rpd (id, created_at, updated_at, deleted_at, netelement_id, name, mac, aux, uptime, type, vendor, serial_num, model, sw_ver, ccap_if, status, city, site, fiber_node_name, bc_vid_oob_if, nc_vid_oob_if, ds_data_cont_profile, us_data_cont_profile, cin_rpa, cin_dpa, ccap_core_name, hubsite_name, cm_no_service_group, dpa, rpa1, rpa2, netelement_ccap_id, state_changed_at, cable_if, internal_id, service_template) FROM stdin;
\.


--
-- Data for Name: rpd_session; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.rpd_session (id, created_at, updated_at, deleted_at, rpd_id, session_id, address_type, local_id, internal_id, ip) FROM stdin;
\.


--
-- Data for Name: salesman; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.salesman (id, created_at, updated_at, deleted_at, firstname, lastname, commission, products, description) FROM stdin;
\.


--
-- Data for Name: scan_range; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.scan_range (id, created_at, updated_at, deleted_at, netelement_id, community_ro, range) FROM stdin;
\.


--
-- Data for Name: sensor; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.sensor (id, name, status, value, unit, core_element_type, core_element_id, created_at, updated_at, deleted_at, cpu_util, buffer_util, fru_status, serial_num, revision, part_num, scale, "precision", internal_id, rate) FROM stdin;
\.


--
-- Data for Name: sepaaccount; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.sepaaccount (id, created_at, updated_at, deleted_at, name, holder, creditorid, iban, bic, institute, company_id, invoice_headline, invoice_text, invoice_text_negativ, invoice_text_sepa, invoice_text_sepa_negativ, template_invoice, template_cdr, description, invoice_nr_start) FROM stdin;
1	2022-05-06 12:27:36+02	2022-05-06 12:27:36+02	\N	NMS Prime		0123456789	0123456789	\N	\N	1	Invoice	Please transfer the total amount with the following transfer reason within 14 days to the noted bank account:	\N	\N	\N	default-invoice-template.tex	default-cdr-template.tex	The inserted data appears on the invoices of your customers - this is a default added during the automatic installation.	\N
\.


--
-- Data for Name: sepamandate; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.sepamandate (id, created_at, updated_at, deleted_at, contract_id, reference, signature_date, holder, iban, bic, institute, valid_from, valid_to, state, costcenter_id, disable, description) FROM stdin;
\.


--
-- Data for Name: settlementrun; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.settlementrun (id, created_at, updated_at, deleted_at, executed_at, uploaded_at, year, month, path, description, verified, fullrun) FROM stdin;
\.


--
-- Data for Name: sla; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.sla (id, created_at, updated_at, deleted_at, name, license) FROM stdin;
1	\N	\N	\N	\N	\N
\.


--
-- Data for Name: smartont; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.smartont (id, created_at, updated_at, deleted_at, default_service_name, default_service_id, default_contact_first_name, default_contact_last_name, default_contact_company, default_contact_phone, default_boc_label, default_configfile_id, default_qos_id, default_mgmt_qos_id) FROM stdin;
1	\N	\N	\N	n/a	n/a	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: supportrequest; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.supportrequest (id, created_at, updated_at, deleted_at, category, priority, mail, phone, text, sla_name) FROM stdin;
\.


--
-- Data for Name: temperature; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.temperature (id, internal_id, name, status, value, threshold, core_element_type, core_element_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- Data for Name: ticket; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ticket (id, created_at, updated_at, deleted_at, name, description, user_id, state, priority, duedate, ticketable_type, ticketable_id, started_at, finished_at) FROM stdin;
\.


--
-- Data for Name: ticket_type; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ticket_type (id, created_at, updated_at, deleted_at, name, parent_id, description) FROM stdin;
\.


--
-- Data for Name: ticket_type_ticket; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ticket_type_ticket (id, created_at, ticket_type_id, ticket_id) FROM stdin;
\.


--
-- Data for Name: ticket_user; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ticket_user (id, created_at, updated_at, deleted_at, user_id, ticket_id) FROM stdin;
\.


--
-- Data for Name: ticketsystem; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.ticketsystem (id, created_at, updated_at, deleted_at, noreplymail, noreplyname, distance, modemcount, opentickets) FROM stdin;
1	2022-05-06 12:25:26+02	2022-05-06 12:25:26+02	\N			1	1	1
\.


--
-- Data for Name: trcclass; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.trcclass (id, created_at, updated_at, deleted_at, trc_id, trc_short, trc_description) FROM stdin;
1	\N	\N	\N	0	n/a	Dummy entry – no TRC classes known.
2	\N	\N	\N	\N	n/a	unknown or not set
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.users (id, created_at, updated_at, deleted_at, first_name, last_name, email, phonenumber, login_name, password, api_token, description, active, remember_token, language, last_login_at, password_changed_at, initial_dashboard, geopos_updated_at, lng, lat, hastruck, theme_color) FROM stdin;
2	\N	\N	\N	web	hook	someone@example.com	\N	webhook	$2y$10$yC0ruwwf.7VXo6De0JeSge/36.ExsXBp6UOfwRE2Rfkk4Q0.e6ACi	\N	\N	t	\N	en	\N	\N	\N	\N	\N	\N	f	default_theme_config.css
1	\N	2023-05-09 17:01:37+02	\N	superuser	initial	root@localhost	\N	root	$2y$10$XTI.hWHitQxiwMTegkH/Xuc3mWDwq6ZWmv/tp1FF.l9HXhvoTl8La	is8SjrF1dFOAo8x5P4pgxiJB45ebZeLYe335ZjvJLSVi1PvGLbVLygbJbAaBrWUQIpGHJefSaXf5hZw7	Superuser to do base config. Initial password is “toor” – change this ASAP or delete this user!!	t	cUOtQfowyOyrau1IdIQ4AkWZJupblb07LYhWQnQ31QVDXvL3cltwzlUoh45X	en	2023-05-09 17:01:37+02	\N	\N	\N	\N	\N	f	default_theme_config.css
\.


--
-- Data for Name: voipmon; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.voipmon (id, created_at, updated_at, deleted_at, delete_record_interval) FROM stdin;
1	2023-04-05 16:55:44+02	2023-04-05 16:55:44+02	\N	14
\.


--
-- Data for Name: websockets_statistics_entries; Type: TABLE DATA; Schema: nmsprime; Owner: nmsprime
--

COPY nmsprime.websockets_statistics_entries (id, app_id, peak_connection_count, websocket_message_count, api_message_count, created_at, updated_at) FROM stdin;
\.


--
-- Name: abilities_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.abilities_id_seq', 55, true);


--
-- Name: accountingrecord_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.accountingrecord_id_seq', 1, true);


--
-- Name: address_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.address_id_seq', 1, false);


--
-- Name: alarm_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.alarm_id_seq', 1, false);


--
-- Name: apartment_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.apartment_id_seq', 1, true);


--
-- Name: arp_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.arp_id_seq', 1, false);


--
-- Name: billingbase_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.billingbase_id_seq', 1, true);


--
-- Name: carriercode_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.carriercode_id_seq', 367, true);


--
-- Name: ccap_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ccap_id_seq', 1, false);


--
-- Name: ccc_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ccc_id_seq', 1, true);


--
-- Name: comment_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.comment_id_seq', 1, true);


--
-- Name: company_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.company_id_seq', 1, true);


--
-- Name: configfile_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.configfile_id_seq', 3, true);


--
-- Name: contact_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.contact_id_seq', 1, true);


--
-- Name: contract_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.contract_id_seq', 2, true);


--
-- Name: coremon_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.coremon_id_seq', 1, false);


--
-- Name: costcenter_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.costcenter_id_seq', 1, true);


--
-- Name: cpe_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.cpe_id_seq', 1, false);


--
-- Name: debt_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.debt_id_seq', 1, true);


--
-- Name: dfsubscription_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.dfsubscription_id_seq', 1, false);


--
-- Name: dfsubscriptionevent_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.dfsubscriptionevent_id_seq', 1, false);


--
-- Name: domain_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.domain_id_seq', 1, true);


--
-- Name: dpa_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.dpa_id_seq', 1, false);


--
-- Name: dpic_card_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.dpic_card_id_seq', 1, false);


--
-- Name: dpic_interface_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.dpic_interface_id_seq', 1, false);


--
-- Name: ekpcode_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ekpcode_id_seq', 625, true);


--
-- Name: endpoint_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.endpoint_id_seq', 1, true);


--
-- Name: enviacontract_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.enviacontract_id_seq', 1, true);


--
-- Name: enviaorder_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.enviaorder_id_seq', 1, true);


--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.enviaorder_phonenumber_id_seq', 1, true);


--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.enviaorderdocument_id_seq', 1, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.failed_jobs_id_seq', 4059, true);


--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.favorite_netelements_id_seq', 1, true);


--
-- Name: fiber_node_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.fiber_node_id_seq', 1, false);


--
-- Name: fiber_node_rpd_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.fiber_node_rpd_id_seq', 1, false);


--
-- Name: global_config_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.global_config_id_seq', 1, true);


--
-- Name: guilog_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.guilog_id_seq', 64, true);


--
-- Name: hfcreq_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.hfcreq_id_seq', 1, true);


--
-- Name: hubsite_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.hubsite_id_seq', 1, false);


--
-- Name: indices_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.indices_id_seq', 1, true);


--
-- Name: inventory_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.inventory_id_seq', 1, false);


--
-- Name: invoice_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.invoice_id_seq', 1, true);


--
-- Name: ippool_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ippool_id_seq', 1, true);


--
-- Name: item_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.item_id_seq', 1, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.jobs_id_seq', 21095, true);


--
-- Name: l2tp_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.l2tp_id_seq', 1, false);


--
-- Name: link_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.link_id_seq', 1, false);


--
-- Name: lldp_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.lldp_id_seq', 1, false);


--
-- Name: market_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.market_id_seq', 1, false);


--
-- Name: mibfile_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.mibfile_id_seq', 1, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.migrations_id_seq', 494, true);


--
-- Name: modem_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.modem_id_seq', 2, true);


--
-- Name: modem_option_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.modem_option_id_seq', 1, true);


--
-- Name: mpr_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.mpr_id_seq', 1, true);


--
-- Name: mprgeopos_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.mprgeopos_id_seq', 1, true);


--
-- Name: mta_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.mta_id_seq', 2, true);


--
-- Name: ncs_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ncs_id_seq', 1, false);


--
-- Name: net_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.net_id_seq', 1, false);


--
-- Name: netelement_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.netelement_id_seq', 1, true);


--
-- Name: netelementtype_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.netelementtype_id_seq', 14, true);


--
-- Name: netgw_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.netgw_id_seq', 1, true);


--
-- Name: node_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.node_id_seq', 1, true);


--
-- Name: numberrange_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.numberrange_id_seq', 1, true);


--
-- Name: oid_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.oid_id_seq', 1, true);


--
-- Name: oui_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.oui_id_seq', 1, false);


--
-- Name: overduedebts_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.overduedebts_id_seq', 1, true);


--
-- Name: parameter_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.parameter_id_seq', 1, true);


--
-- Name: phonebookentry_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.phonebookentry_id_seq', 1, true);


--
-- Name: phonenumber_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.phonenumber_id_seq', 300000, true);


--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.phonenumbermanagement_id_seq', 1, true);


--
-- Name: phonetariff_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.phonetariff_id_seq', 3, true);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.product_id_seq', 1, true);


--
-- Name: provbase_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.provbase_id_seq', 1, true);


--
-- Name: provmon_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.provmon_id_seq', 1, true);


--
-- Name: provvoip_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.provvoip_id_seq', 1, true);


--
-- Name: qos_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.qos_id_seq', 2, true);


--
-- Name: realty_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.realty_id_seq', 1, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.roles_id_seq', 5, true);


--
-- Name: rpa_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.rpa_id_seq', 1, false);


--
-- Name: rpd_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.rpd_id_seq', 1, false);


--
-- Name: rpd_session_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.rpd_session_id_seq', 1, false);


--
-- Name: salesman_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.salesman_id_seq', 1, true);


--
-- Name: scan_range_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.scan_range_id_seq', 1, false);


--
-- Name: sensor_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.sensor_id_seq', 1, false);


--
-- Name: sepaaccount_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.sepaaccount_id_seq', 1, true);


--
-- Name: sepamandate_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.sepamandate_id_seq', 1, true);


--
-- Name: settlementrun_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.settlementrun_id_seq', 1, true);


--
-- Name: sla_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.sla_id_seq', 1, true);


--
-- Name: smartont_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.smartont_id_seq', 1, true);


--
-- Name: supportrequest_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.supportrequest_id_seq', 1, true);


--
-- Name: temperature_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.temperature_id_seq', 1, false);


--
-- Name: ticket_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ticket_id_seq', 2, true);


--
-- Name: ticket_type_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ticket_type_id_seq', 1, true);


--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ticket_type_ticket_id_seq', 1, true);


--
-- Name: ticket_user_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ticket_user_id_seq', 1, true);


--
-- Name: ticketsystem_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.ticketsystem_id_seq', 1, true);


--
-- Name: trcclass_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.trcclass_id_seq', 2, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.users_id_seq', 2, true);


--
-- Name: voipmon_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.voipmon_id_seq', 1, true);


--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: nmsprime
--

SELECT pg_catalog.setval('nmsprime.websockets_statistics_entries_id_seq', 1, true);


--
-- Name: address address_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.address
    ADD CONSTRAINT address_pkey PRIMARY KEY (id);


--
-- Name: alarm alarm_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.alarm
    ADD CONSTRAINT alarm_pkey PRIMARY KEY (id);


--
-- Name: arp arp_netelement_id_ip_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.arp
    ADD CONSTRAINT arp_netelement_id_ip_unique UNIQUE (netelement_id, ip);


--
-- Name: arp arp_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.arp
    ADD CONSTRAINT arp_pkey PRIMARY KEY (id);


--
-- Name: ccap ccap_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ccap
    ADD CONSTRAINT ccap_pkey PRIMARY KEY (id);


--
-- Name: coremon coremon_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.coremon
    ADD CONSTRAINT coremon_pkey PRIMARY KEY (id);


--
-- Name: cpe cpe_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.cpe
    ADD CONSTRAINT cpe_pkey PRIMARY KEY (id);


--
-- Name: dfsubscription dfsubscription_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dfsubscription
    ADD CONSTRAINT dfsubscription_pkey PRIMARY KEY (id);


--
-- Name: dfsubscriptionevent dfsubscriptionevent_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dfsubscriptionevent
    ADD CONSTRAINT dfsubscriptionevent_pkey PRIMARY KEY (id);


--
-- Name: dpa dpa_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dpa
    ADD CONSTRAINT dpa_pkey PRIMARY KEY (id);


--
-- Name: dpic_card dpic_card_internal_id_netelement_ccap_id_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dpic_card
    ADD CONSTRAINT dpic_card_internal_id_netelement_ccap_id_unique UNIQUE (internal_id, netelement_ccap_id);


--
-- Name: dpic_card dpic_card_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.dpic_card
    ADD CONSTRAINT dpic_card_pkey PRIMARY KEY (id);


--
-- Name: netelement_interface dpic_interface_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelement_interface
    ADD CONSTRAINT dpic_interface_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fiber_node fiber_node_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.fiber_node
    ADD CONSTRAINT fiber_node_pkey PRIMARY KEY (id);


--
-- Name: fiber_node_rpd fiber_node_rpd_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.fiber_node_rpd
    ADD CONSTRAINT fiber_node_rpd_pkey PRIMARY KEY (id);


--
-- Name: hubsite hubsite_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.hubsite
    ADD CONSTRAINT hubsite_pkey PRIMARY KEY (id);


--
-- Name: abilities idx_17263_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.abilities
    ADD CONSTRAINT idx_17263_primary PRIMARY KEY (id);


--
-- Name: accountingrecord idx_17273_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.accountingrecord
    ADD CONSTRAINT idx_17273_primary PRIMARY KEY (id);


--
-- Name: apartment idx_17279_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.apartment
    ADD CONSTRAINT idx_17279_primary PRIMARY KEY (id);


--
-- Name: billingbase idx_17294_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.billingbase
    ADD CONSTRAINT idx_17294_primary PRIMARY KEY (id);


--
-- Name: carriercode idx_17303_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.carriercode
    ADD CONSTRAINT idx_17303_primary PRIMARY KEY (id);


--
-- Name: ccc idx_17309_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ccc
    ADD CONSTRAINT idx_17309_primary PRIMARY KEY (id);


--
-- Name: comment idx_17319_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.comment
    ADD CONSTRAINT idx_17319_primary PRIMARY KEY (id);


--
-- Name: company idx_17328_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.company
    ADD CONSTRAINT idx_17328_primary PRIMARY KEY (id);


--
-- Name: configfile idx_17337_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.configfile
    ADD CONSTRAINT idx_17337_primary PRIMARY KEY (id);


--
-- Name: contact idx_17349_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.contact
    ADD CONSTRAINT idx_17349_primary PRIMARY KEY (id);


--
-- Name: contract idx_17358_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.contract
    ADD CONSTRAINT idx_17358_primary PRIMARY KEY (id);


--
-- Name: costcenter idx_17367_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.costcenter
    ADD CONSTRAINT idx_17367_primary PRIMARY KEY (id);


--
-- Name: debt idx_17376_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.debt
    ADD CONSTRAINT idx_17376_primary PRIMARY KEY (id);


--
-- Name: domain idx_17385_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.domain
    ADD CONSTRAINT idx_17385_primary PRIMARY KEY (id);


--
-- Name: ekpcode idx_17391_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ekpcode
    ADD CONSTRAINT idx_17391_primary PRIMARY KEY (id);


--
-- Name: endpoint idx_17397_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.endpoint
    ADD CONSTRAINT idx_17397_primary PRIMARY KEY (id);


--
-- Name: enviacontract idx_17407_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviacontract
    ADD CONSTRAINT idx_17407_primary PRIMARY KEY (id);


--
-- Name: enviaorder idx_17413_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorder
    ADD CONSTRAINT idx_17413_primary PRIMARY KEY (id);


--
-- Name: enviaorderdocument idx_17422_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorderdocument
    ADD CONSTRAINT idx_17422_primary PRIMARY KEY (id);


--
-- Name: enviaorder_phonenumber idx_17428_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.enviaorder_phonenumber
    ADD CONSTRAINT idx_17428_primary PRIMARY KEY (id);


--
-- Name: failed_jobs idx_17434_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.failed_jobs
    ADD CONSTRAINT idx_17434_primary PRIMARY KEY (id);


--
-- Name: favorite_netelements idx_17444_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.favorite_netelements
    ADD CONSTRAINT idx_17444_primary PRIMARY KEY (id);


--
-- Name: global_config idx_17450_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.global_config
    ADD CONSTRAINT idx_17450_primary PRIMARY KEY (id);


--
-- Name: guilog idx_17461_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.guilog
    ADD CONSTRAINT idx_17461_primary PRIMARY KEY (id);


--
-- Name: hfcreq idx_17470_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.hfcreq
    ADD CONSTRAINT idx_17470_primary PRIMARY KEY (id);


--
-- Name: indices idx_17479_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.indices
    ADD CONSTRAINT idx_17479_primary PRIMARY KEY (id);


--
-- Name: invoice idx_17488_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.invoice
    ADD CONSTRAINT idx_17488_primary PRIMARY KEY (id);


--
-- Name: ippool idx_17494_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ippool
    ADD CONSTRAINT idx_17494_primary PRIMARY KEY (id);


--
-- Name: item idx_17505_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.item
    ADD CONSTRAINT idx_17505_primary PRIMARY KEY (id);


--
-- Name: jobs idx_17517_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.jobs
    ADD CONSTRAINT idx_17517_primary PRIMARY KEY (id);


--
-- Name: mibfile idx_17526_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mibfile
    ADD CONSTRAINT idx_17526_primary PRIMARY KEY (id);


--
-- Name: migrations idx_17535_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.migrations
    ADD CONSTRAINT idx_17535_primary PRIMARY KEY (id);


--
-- Name: modem idx_17541_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.modem
    ADD CONSTRAINT idx_17541_primary PRIMARY KEY (id);


--
-- Name: modem_option idx_17551_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.modem_option
    ADD CONSTRAINT idx_17551_primary PRIMARY KEY (id);


--
-- Name: mpr idx_17557_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mpr
    ADD CONSTRAINT idx_17557_primary PRIMARY KEY (id);


--
-- Name: mprgeopos idx_17566_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mprgeopos
    ADD CONSTRAINT idx_17566_primary PRIMARY KEY (id);


--
-- Name: mta idx_17575_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.mta
    ADD CONSTRAINT idx_17575_primary PRIMARY KEY (id);


--
-- Name: netelement idx_17596_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelement
    ADD CONSTRAINT idx_17596_primary PRIMARY KEY (id);


--
-- Name: netelementtype idx_17605_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelementtype
    ADD CONSTRAINT idx_17605_primary PRIMARY KEY (id);


--
-- Name: netgw idx_17614_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netgw
    ADD CONSTRAINT idx_17614_primary PRIMARY KEY (id);


--
-- Name: node idx_17626_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.node
    ADD CONSTRAINT idx_17626_primary PRIMARY KEY (id);


--
-- Name: notifications idx_17633_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.notifications
    ADD CONSTRAINT idx_17633_primary PRIMARY KEY (id);


--
-- Name: numberrange idx_17641_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.numberrange
    ADD CONSTRAINT idx_17641_primary PRIMARY KEY (id);


--
-- Name: oid idx_17652_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.oid
    ADD CONSTRAINT idx_17652_primary PRIMARY KEY (id);


--
-- Name: overduedebts idx_17661_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.overduedebts
    ADD CONSTRAINT idx_17661_primary PRIMARY KEY (id);


--
-- Name: parameter idx_17670_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.parameter
    ADD CONSTRAINT idx_17670_primary PRIMARY KEY (id);


--
-- Name: phonebookentry idx_17685_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonebookentry
    ADD CONSTRAINT idx_17685_primary PRIMARY KEY (id);


--
-- Name: phonenumber idx_17694_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonenumber
    ADD CONSTRAINT idx_17694_primary PRIMARY KEY (id);


--
-- Name: phonenumbermanagement idx_17705_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonenumbermanagement
    ADD CONSTRAINT idx_17705_primary PRIMARY KEY (id);


--
-- Name: phonetariff idx_17718_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.phonetariff
    ADD CONSTRAINT idx_17718_primary PRIMARY KEY (id);


--
-- Name: product idx_17728_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.product
    ADD CONSTRAINT idx_17728_primary PRIMARY KEY (id);


--
-- Name: provbase idx_17735_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provbase
    ADD CONSTRAINT idx_17735_primary PRIMARY KEY (id);


--
-- Name: provmon idx_17748_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provmon
    ADD CONSTRAINT idx_17748_primary PRIMARY KEY (id);


--
-- Name: provvoip idx_17754_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.provvoip
    ADD CONSTRAINT idx_17754_primary PRIMARY KEY (id);


--
-- Name: qos idx_17763_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.qos
    ADD CONSTRAINT idx_17763_primary PRIMARY KEY (id);


--
-- Name: realty idx_17858_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.realty
    ADD CONSTRAINT idx_17858_primary PRIMARY KEY (id);


--
-- Name: roles idx_17867_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.roles
    ADD CONSTRAINT idx_17867_primary PRIMARY KEY (id);


--
-- Name: salesman idx_17876_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.salesman
    ADD CONSTRAINT idx_17876_primary PRIMARY KEY (id);


--
-- Name: sepaaccount idx_17885_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sepaaccount
    ADD CONSTRAINT idx_17885_primary PRIMARY KEY (id);


--
-- Name: sepamandate idx_17894_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sepamandate
    ADD CONSTRAINT idx_17894_primary PRIMARY KEY (id);


--
-- Name: settlementrun idx_17903_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.settlementrun
    ADD CONSTRAINT idx_17903_primary PRIMARY KEY (id);


--
-- Name: sla idx_17909_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sla
    ADD CONSTRAINT idx_17909_primary PRIMARY KEY (id);


--
-- Name: supportrequest idx_17915_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.supportrequest
    ADD CONSTRAINT idx_17915_primary PRIMARY KEY (id);


--
-- Name: ticket idx_17924_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket
    ADD CONSTRAINT idx_17924_primary PRIMARY KEY (id);


--
-- Name: ticketsystem idx_17933_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticketsystem
    ADD CONSTRAINT idx_17933_primary PRIMARY KEY (id);


--
-- Name: ticket_type idx_17942_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_type
    ADD CONSTRAINT idx_17942_primary PRIMARY KEY (id);


--
-- Name: ticket_type_ticket idx_17948_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_type_ticket
    ADD CONSTRAINT idx_17948_primary PRIMARY KEY (id);


--
-- Name: ticket_user idx_17954_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ticket_user
    ADD CONSTRAINT idx_17954_primary PRIMARY KEY (id);


--
-- Name: trcclass idx_17960_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.trcclass
    ADD CONSTRAINT idx_17960_primary PRIMARY KEY (id);


--
-- Name: users idx_17966_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.users
    ADD CONSTRAINT idx_17966_primary PRIMARY KEY (id);


--
-- Name: websockets_statistics_entries idx_17978_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.websockets_statistics_entries
    ADD CONSTRAINT idx_17978_primary PRIMARY KEY (id);


--
-- Name: inventory inventory_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.inventory
    ADD CONSTRAINT inventory_pkey PRIMARY KEY (id);


--
-- Name: l2tp l2tp_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.l2tp
    ADD CONSTRAINT l2tp_pkey PRIMARY KEY (id);


--
-- Name: link link_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.link
    ADD CONSTRAINT link_pkey PRIMARY KEY (id);


--
-- Name: lldp lldp_netelement_id_lldp_suffix_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.lldp
    ADD CONSTRAINT lldp_netelement_id_lldp_suffix_unique UNIQUE (netelement_id, lldp_suffix);


--
-- Name: lldp lldp_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.lldp
    ADD CONSTRAINT lldp_pkey PRIMARY KEY (id);


--
-- Name: market market_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.market
    ADD CONSTRAINT market_pkey PRIMARY KEY (id);


--
-- Name: ncs ncs_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.ncs
    ADD CONSTRAINT ncs_pkey PRIMARY KEY (id);


--
-- Name: net net_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.net
    ADD CONSTRAINT net_pkey PRIMARY KEY (id);


--
-- Name: netelement_interface netelement_interface_netelement_id_if_index_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.netelement_interface
    ADD CONSTRAINT netelement_interface_netelement_id_if_index_unique UNIQUE (netelement_id, if_index);


--
-- Name: oui oui_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.oui
    ADD CONSTRAINT oui_pkey PRIMARY KEY (id);


--
-- Name: rpa rpa_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpa
    ADD CONSTRAINT rpa_pkey PRIMARY KEY (id);


--
-- Name: rpd rpd_mac_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd
    ADD CONSTRAINT rpd_mac_unique UNIQUE (mac);


--
-- Name: rpd rpd_netelement_id_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd
    ADD CONSTRAINT rpd_netelement_id_unique UNIQUE (netelement_id);


--
-- Name: rpd rpd_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd
    ADD CONSTRAINT rpd_pkey PRIMARY KEY (id);


--
-- Name: rpd_session rpd_session_internal_id_unique; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd_session
    ADD CONSTRAINT rpd_session_internal_id_unique UNIQUE (internal_id);


--
-- Name: rpd_session rpd_session_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.rpd_session
    ADD CONSTRAINT rpd_session_pkey PRIMARY KEY (id);


--
-- Name: scan_range scan_range_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.scan_range
    ADD CONSTRAINT scan_range_pkey PRIMARY KEY (id);


--
-- Name: sensor sensor_internal_id_unique_idx; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sensor
    ADD CONSTRAINT sensor_internal_id_unique_idx UNIQUE (internal_id, core_element_id, core_element_type);


--
-- Name: sensor sensor_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.sensor
    ADD CONSTRAINT sensor_pkey PRIMARY KEY (id);


--
-- Name: smartont smartont_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.smartont
    ADD CONSTRAINT smartont_pkey PRIMARY KEY (id);


--
-- Name: temperature temperature_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.temperature
    ADD CONSTRAINT temperature_pkey PRIMARY KEY (id);


--
-- Name: voipmon voipmon_pkey; Type: CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.voipmon
    ADD CONSTRAINT voipmon_pkey PRIMARY KEY (id);


--
-- Name: idx_17263_abilities_scope_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17263_abilities_scope_index ON nmsprime.abilities USING btree (scope);


--
-- Name: idx_17279_apartment_realty_id_foreign; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17279_apartment_realty_id_foreign ON nmsprime.apartment USING btree (realty_id);


--
-- Name: idx_17286_assigned_roles_entity_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17286_assigned_roles_entity_index ON nmsprime.assigned_roles USING btree (entity_id, entity_type, scope);


--
-- Name: idx_17286_assigned_roles_role_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17286_assigned_roles_role_id_index ON nmsprime.assigned_roles USING btree (role_id);


--
-- Name: idx_17286_assigned_roles_scope_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17286_assigned_roles_scope_index ON nmsprime.assigned_roles USING btree (scope);


--
-- Name: idx_17289_authreminders_email_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17289_authreminders_email_index ON nmsprime.authreminders USING btree (email);


--
-- Name: idx_17289_authreminders_token_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17289_authreminders_token_index ON nmsprime.authreminders USING btree (token);


--
-- Name: idx_17303_carriercode_carrier_code_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17303_carriercode_carrier_code_unique ON nmsprime.carriercode USING btree (carrier_code);


--
-- Name: idx_17337_configfile_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17337_configfile_parent_id_index ON nmsprime.configfile USING btree (parent_id);


--
-- Name: idx_17358_contract_apartment_id_foreign; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_apartment_id_foreign ON nmsprime.contract USING btree (apartment_id);


--
-- Name: idx_17358_contract_costcenter_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_costcenter_id_index ON nmsprime.contract USING btree (costcenter_id);


--
-- Name: idx_17358_contract_next_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_next_qos_id_index ON nmsprime.contract USING btree (next_qos_id);


--
-- Name: idx_17358_contract_number_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_number_index ON nmsprime.contract USING btree (number);


--
-- Name: idx_17358_contract_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_qos_id_index ON nmsprime.contract USING btree (qos_id);


--
-- Name: idx_17358_contract_salesman_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17358_contract_salesman_id_index ON nmsprime.contract USING btree (salesman_id);


--
-- Name: idx_17391_ekpcode_ekp_code_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17391_ekpcode_ekp_code_unique ON nmsprime.ekpcode USING btree (ekp_code);


--
-- Name: idx_17397_endpoint_modem_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17397_endpoint_modem_id_index ON nmsprime.endpoint USING btree (modem_id);


--
-- Name: idx_17413_enviaorder_orderid_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17413_enviaorder_orderid_unique ON nmsprime.enviaorder USING btree (orderid);


--
-- Name: idx_17444_favorite_netelements_user_id_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17444_favorite_netelements_user_id_netelement_id_index ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);


--
-- Name: idx_17444_favorite_netelements_user_id_netelement_id_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17444_favorite_netelements_user_id_netelement_id_unique ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);


--
-- Name: idx_17494_ippool_netgw_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17494_ippool_netgw_id_index ON nmsprime.ippool USING btree (netgw_id);


--
-- Name: idx_17505_by_contract_id; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17505_by_contract_id ON nmsprime.item USING btree (contract_id);


--
-- Name: idx_17505_by_product_id; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17505_by_product_id ON nmsprime.item USING btree (product_id);


--
-- Name: idx_17517_jobs_queue_reserved_at_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17517_jobs_queue_reserved_at_index ON nmsprime.jobs USING btree (queue, reserved_at);


--
-- Name: idx_17541_modem_configfile_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17541_modem_configfile_id_index ON nmsprime.modem USING btree (configfile_id);


--
-- Name: idx_17541_modem_contract_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17541_modem_contract_id_index ON nmsprime.modem USING btree (contract_id);


--
-- Name: idx_17541_modem_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17541_modem_netelement_id_index ON nmsprime.modem USING btree (netelement_id);


--
-- Name: idx_17541_modem_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17541_modem_qos_id_index ON nmsprime.modem USING btree (qos_id);


--
-- Name: idx_17557_mpr_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17557_mpr_netelement_id_index ON nmsprime.mpr USING btree (netelement_id);


--
-- Name: idx_17566_mprgeopos_mpr_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17566_mprgeopos_mpr_id_index ON nmsprime.mprgeopos USING btree (mpr_id);


--
-- Name: idx_17575_mta_configfile_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17575_mta_configfile_id_index ON nmsprime.mta USING btree (configfile_id);


--
-- Name: idx_17575_mta_modem_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17575_mta_modem_id_index ON nmsprime.mta USING btree (modem_id);


--
-- Name: idx_17596_netelement_netelementtype_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17596_netelement_netelementtype_id_index ON nmsprime.netelement USING btree (netelementtype_id);


--
-- Name: idx_17596_netelement_netgw_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17596_netelement_netgw_id_index ON nmsprime.netelement USING btree (netgw_id);


--
-- Name: idx_17596_netelement_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17596_netelement_parent_id_index ON nmsprime.netelement USING btree (parent_id);


--
-- Name: idx_17605_netelementtype_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17605_netelementtype_parent_id_index ON nmsprime.netelementtype USING btree (parent_id);


--
-- Name: idx_17626_node_netelement_id_foreign; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17626_node_netelement_id_foreign ON nmsprime.node USING btree (netelement_id);


--
-- Name: idx_17633_notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17633_notifications_notifiable_type_notifiable_id_index ON nmsprime.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: idx_17679_permissions_ability_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17679_permissions_ability_id_index ON nmsprime.permissions USING btree (ability_id);


--
-- Name: idx_17679_permissions_entity_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17679_permissions_entity_index ON nmsprime.permissions USING btree (entity_id, entity_type, scope);


--
-- Name: idx_17679_permissions_scope_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17679_permissions_scope_index ON nmsprime.permissions USING btree (scope);


--
-- Name: idx_17694_phonenumber_mta_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17694_phonenumber_mta_id_index ON nmsprime.phonenumber USING btree (mta_id);


--
-- Name: idx_17858_realty_node_id_foreign; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17858_realty_node_id_foreign ON nmsprime.realty USING btree (node_id);


--
-- Name: idx_17867_roles_name_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17867_roles_name_unique ON nmsprime.roles USING btree (name, scope);


--
-- Name: idx_17867_roles_scope_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17867_roles_scope_index ON nmsprime.roles USING btree (scope);


--
-- Name: idx_17894_by_contract_id; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17894_by_contract_id ON nmsprime.sepamandate USING btree (contract_id);


--
-- Name: idx_17894_by_costcenter_id; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17894_by_costcenter_id ON nmsprime.sepamandate USING btree (costcenter_id);


--
-- Name: idx_17924_ticket_ticketable_type_ticketable_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX idx_17924_ticket_ticketable_type_ticketable_id_index ON nmsprime.ticket USING btree (ticketable_type, ticketable_id);


--
-- Name: idx_17960_trc_id; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17960_trc_id ON nmsprime.trcclass USING btree (trc_id);


--
-- Name: idx_17960_trcclass_trc_id_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17960_trcclass_trc_id_unique ON nmsprime.trcclass USING btree (trc_id);


--
-- Name: idx_17966_users_api_token_unique; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE UNIQUE INDEX idx_17966_users_api_token_unique ON nmsprime.users USING btree (api_token);


--
-- Name: sensor_core_element_type_core_element_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX sensor_core_element_type_core_element_id_index ON nmsprime.sensor USING btree (core_element_type, core_element_id);


--
-- Name: temperature_core_element_type_core_element_id_index; Type: INDEX; Schema: nmsprime; Owner: nmsprime
--

CREATE INDEX temperature_core_element_type_core_element_id_index ON nmsprime.temperature USING btree (core_element_type, core_element_id);


--
-- Name: authreminders on_update_current_timestamp; Type: TRIGGER; Schema: nmsprime; Owner: nmsprime
--

CREATE TRIGGER on_update_current_timestamp BEFORE UPDATE ON nmsprime.authreminders FOR EACH ROW EXECUTE FUNCTION nmsprime.on_update_current_timestamp_authreminders();


--
-- Name: assigned_roles assigned_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.assigned_roles
    ADD CONSTRAINT assigned_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES nmsprime.roles(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions permissions_ability_id_foreign; Type: FK CONSTRAINT; Schema: nmsprime; Owner: nmsprime
--

ALTER TABLE ONLY nmsprime.permissions
    ADD CONSTRAINT permissions_ability_id_foreign FOREIGN KEY (ability_id) REFERENCES nmsprime.abilities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

