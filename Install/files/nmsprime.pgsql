--
-- PostgreSQL database dump
--

-- Dumped from database version 13.6
-- Dumped by pg_dump version 13.6

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

--
-- Name: nmsprime; Type: DATABASE; Schema: -; Owner: postgres
--

CREATE DATABASE nmsprime WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE = 'en_US.UTF-8';


ALTER DATABASE nmsprime OWNER TO postgres;

\connect nmsprime

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

--
-- Name: nmsprime; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA nmsprime;


ALTER SCHEMA nmsprime OWNER TO postgres;

--
-- Name: billingbase_userlang; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.billingbase_userlang AS ENUM (
    'de',
    'en'
);


ALTER TYPE nmsprime.billingbase_userlang OWNER TO postgres;

--
-- Name: configfile_device; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.configfile_device AS ENUM (
    'cm',
    'mta',
    'tr069'
);


ALTER TYPE nmsprime.configfile_device OWNER TO postgres;

--
-- Name: configfile_public; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.configfile_public AS ENUM (
    'yes',
    'no'
);


ALTER TYPE nmsprime.configfile_public OWNER TO postgres;

--
-- Name: domain_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.domain_type AS ENUM (
    'SIP',
    'Email',
    'DHCP'
);


ALTER TYPE nmsprime.domain_type OWNER TO postgres;

--
-- Name: enviaorderdocument_document_type; Type: TYPE; Schema: nmsprime; Owner: postgres
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


ALTER TYPE nmsprime.enviaorderdocument_document_type OWNER TO postgres;

--
-- Name: invoice_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.invoice_type AS ENUM (
    'Invoice',
    'CDR'
);


ALTER TYPE nmsprime.invoice_type OWNER TO postgres;

--
-- Name: ippool_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.ippool_type AS ENUM (
    'CM',
    'CPEPub',
    'CPEPriv',
    'MTA'
);


ALTER TYPE nmsprime.ippool_type OWNER TO postgres;

--
-- Name: mta_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.mta_type AS ENUM (
    'sip',
    'packetcable'
);


ALTER TYPE nmsprime.mta_type OWNER TO postgres;

--
-- Name: numberrange_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.numberrange_type AS ENUM (
    'contract',
    'invoice'
);


ALTER TYPE nmsprime.numberrange_type OWNER TO postgres;

--
-- Name: oid_html_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.oid_html_type AS ENUM (
    'text',
    'select',
    'groupbox',
    'textarea'
);


ALTER TYPE nmsprime.oid_html_type OWNER TO postgres;

--
-- Name: oid_type; Type: TYPE; Schema: nmsprime; Owner: postgres
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


ALTER TYPE nmsprime.oid_type OWNER TO postgres;

--
-- Name: phonetariff_type; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.phonetariff_type AS ENUM (
    'purchase',
    'sale',
    'basic',
    'landlineflat',
    'allnetflat'
);


ALTER TYPE nmsprime.phonetariff_type OWNER TO postgres;

--
-- Name: phonetariff_voip_protocol; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.phonetariff_voip_protocol AS ENUM (
    'MGCP',
    'SIP'
);


ALTER TYPE nmsprime.phonetariff_voip_protocol OWNER TO postgres;

--
-- Name: product_billing_cycle; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.product_billing_cycle AS ENUM (
    'Once',
    'Monthly',
    'Quarterly',
    'Yearly'
);


ALTER TYPE nmsprime.product_billing_cycle OWNER TO postgres;

--
-- Name: product_type; Type: TYPE; Schema: nmsprime; Owner: postgres
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


ALTER TYPE nmsprime.product_type OWNER TO postgres;

--
-- Name: sepamandate_state; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.sepamandate_state AS ENUM (
    'FRST',
    'RCUR',
    'OOFF',
    'FNAL'
);


ALTER TYPE nmsprime.sepamandate_state OWNER TO postgres;

--
-- Name: ticket_priority; Type: TYPE; Schema: nmsprime; Owner: postgres
--

CREATE TYPE nmsprime.ticket_priority AS ENUM (
    'Trivial',
    'Minor',
    'Major',
    'Critical'
);


ALTER TYPE nmsprime.ticket_priority OWNER TO postgres;

--
-- Name: on_update_current_timestamp_authreminders(); Type: FUNCTION; Schema: nmsprime; Owner: postgres
--

CREATE FUNCTION nmsprime.on_update_current_timestamp_authreminders() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.created_at = now();
   RETURN NEW;
END;
$$;


ALTER FUNCTION nmsprime.on_update_current_timestamp_authreminders() OWNER TO postgres;

--
-- Name: on_update_current_timestamp_radpostauth(); Type: FUNCTION; Schema: nmsprime; Owner: postgres
--

CREATE FUNCTION nmsprime.on_update_current_timestamp_radpostauth() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.authdate = now();
   RETURN NEW;
END;
$$;


ALTER FUNCTION nmsprime.on_update_current_timestamp_radpostauth() OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: abilities; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.abilities OWNER TO postgres;

--
-- Name: abilities_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.abilities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.abilities_id_seq OWNER TO postgres;

--
-- Name: abilities_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.abilities_id_seq OWNED BY nmsprime.abilities.id;


--
-- Name: accountingrecord; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.accountingrecord OWNER TO postgres;

--
-- Name: accountingrecord_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.accountingrecord_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.accountingrecord_id_seq OWNER TO postgres;

--
-- Name: accountingrecord_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.accountingrecord_id_seq OWNED BY nmsprime.accountingrecord.id;


--
-- Name: apartment; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.apartment OWNER TO postgres;

--
-- Name: apartment_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.apartment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.apartment_id_seq OWNER TO postgres;

--
-- Name: apartment_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.apartment_id_seq OWNED BY nmsprime.apartment.id;


--
-- Name: assigned_roles; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.assigned_roles (
    role_id bigint,
    entity_id bigint,
    entity_type character varying(150),
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.assigned_roles OWNER TO postgres;

--
-- Name: authreminders; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.authreminders (
    email character varying(191),
    token character varying(191),
    created_at timestamp with time zone
);


ALTER TABLE nmsprime.authreminders OWNER TO postgres;

--
-- Name: billingbase; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.billingbase OWNER TO postgres;

--
-- Name: billingbase_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.billingbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.billingbase_id_seq OWNER TO postgres;

--
-- Name: billingbase_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.billingbase_id_seq OWNED BY nmsprime.billingbase.id;


--
-- Name: carriercode; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.carriercode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    carrier_code character varying(4),
    company character varying(191)
);


ALTER TABLE nmsprime.carriercode OWNER TO postgres;

--
-- Name: carriercode_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.carriercode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.carriercode_id_seq OWNER TO postgres;

--
-- Name: carriercode_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.carriercode_id_seq OWNED BY nmsprime.carriercode.id;


--
-- Name: ccc; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    speedtest_url character varying(191)
);


ALTER TABLE nmsprime.ccc OWNER TO postgres;

--
-- Name: ccc_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ccc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ccc_id_seq OWNER TO postgres;

--
-- Name: ccc_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ccc_id_seq OWNED BY nmsprime.ccc.id;


--
-- Name: comment; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.comment OWNER TO postgres;

--
-- Name: comment_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.comment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.comment_id_seq OWNER TO postgres;

--
-- Name: comment_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.comment_id_seq OWNED BY nmsprime.comment.id;


--
-- Name: company; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.company OWNER TO postgres;

--
-- Name: company_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.company_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.company_id_seq OWNER TO postgres;

--
-- Name: company_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.company_id_seq OWNED BY nmsprime.company.id;


--
-- Name: configfile; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    dashboard character varying(191) DEFAULT '/grafana/d/3-42DM6Gk/cablemodem'::character varying
);


ALTER TABLE nmsprime.configfile OWNER TO postgres;

--
-- Name: configfile_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.configfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.configfile_id_seq OWNER TO postgres;

--
-- Name: configfile_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.configfile_id_seq OWNED BY nmsprime.configfile.id;


--
-- Name: contact; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.contact OWNER TO postgres;

--
-- Name: contact_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.contact_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contact_id_seq OWNER TO postgres;

--
-- Name: contact_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.contact_id_seq OWNED BY nmsprime.contact.id;


--
-- Name: contract; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    house_number character varying(8),
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
    contact_id bigint
);


ALTER TABLE nmsprime.contract OWNER TO postgres;

--
-- Name: contract_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.contract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contract_id_seq OWNER TO postgres;

--
-- Name: contract_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.contract_id_seq OWNED BY nmsprime.contract.id;


--
-- Name: costcenter; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.costcenter OWNER TO postgres;

--
-- Name: costcenter_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.costcenter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.costcenter_id_seq OWNER TO postgres;

--
-- Name: costcenter_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.costcenter_id_seq OWNED BY nmsprime.costcenter.id;


--
-- Name: debt; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    indicator boolean,
    dunning_date date,
    parent_id bigint,
    missing_amount numeric(10,2),
    extra_fee numeric(10,2)
);


ALTER TABLE nmsprime.debt OWNER TO postgres;

--
-- Name: debt_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.debt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.debt_id_seq OWNER TO postgres;

--
-- Name: debt_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.debt_id_seq OWNED BY nmsprime.debt.id;


--
-- Name: domain; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.domain OWNER TO postgres;

--
-- Name: domain_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.domain_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.domain_id_seq OWNER TO postgres;

--
-- Name: domain_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.domain_id_seq OWNED BY nmsprime.domain.id;


--
-- Name: ekpcode; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.ekpcode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    ekp_code character varying(191),
    company character varying(191)
);


ALTER TABLE nmsprime.ekpcode OWNER TO postgres;

--
-- Name: ekpcode_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ekpcode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ekpcode_id_seq OWNER TO postgres;

--
-- Name: ekpcode_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ekpcode_id_seq OWNED BY nmsprime.ekpcode.id;


--
-- Name: endpoint; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    prefix character varying(191)
);


ALTER TABLE nmsprime.endpoint OWNER TO postgres;

--
-- Name: endpoint_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.endpoint_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.endpoint_id_seq OWNER TO postgres;

--
-- Name: endpoint_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.endpoint_id_seq OWNED BY nmsprime.endpoint.id;


--
-- Name: enviacontract; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.enviacontract OWNER TO postgres;

--
-- Name: enviacontract_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.enviacontract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviacontract_id_seq OWNER TO postgres;

--
-- Name: enviacontract_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.enviacontract_id_seq OWNED BY nmsprime.enviacontract.id;


--
-- Name: enviaorder; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.enviaorder OWNER TO postgres;

--
-- Name: enviaorder_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.enviaorder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_id_seq OWNER TO postgres;

--
-- Name: enviaorder_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.enviaorder_id_seq OWNED BY nmsprime.enviaorder.id;


--
-- Name: enviaorder_phonenumber; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.enviaorder_phonenumber (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    enviaorder_id bigint,
    phonenumber_id bigint
);


ALTER TABLE nmsprime.enviaorder_phonenumber OWNER TO postgres;

--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.enviaorder_phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_phonenumber_id_seq OWNER TO postgres;

--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.enviaorder_phonenumber_id_seq OWNED BY nmsprime.enviaorder_phonenumber.id;


--
-- Name: enviaorderdocument; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.enviaorderdocument OWNER TO postgres;

--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.enviaorderdocument_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorderdocument_id_seq OWNER TO postgres;

--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.enviaorderdocument_id_seq OWNED BY nmsprime.enviaorderdocument.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.failed_jobs (
    id bigint NOT NULL,
    connection text,
    queue text,
    payload text,
    failed_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    exception text
);


ALTER TABLE nmsprime.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.failed_jobs_id_seq OWNED BY nmsprime.failed_jobs.id;


--
-- Name: favorite_netelements; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.favorite_netelements (
    id bigint NOT NULL,
    user_id bigint,
    netelement_id bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.favorite_netelements OWNER TO postgres;

--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.favorite_netelements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.favorite_netelements_id_seq OWNER TO postgres;

--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.favorite_netelements_id_seq OWNED BY nmsprime.favorite_netelements.id;


--
-- Name: global_config; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    isallnetssidebarenabled boolean DEFAULT false
);


ALTER TABLE nmsprime.global_config OWNER TO postgres;

--
-- Name: global_config_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.global_config_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.global_config_id_seq OWNER TO postgres;

--
-- Name: global_config_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.global_config_id_seq OWNED BY nmsprime.global_config.id;


--
-- Name: guilog; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.guilog OWNER TO postgres;

--
-- Name: guilog_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.guilog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.guilog_id_seq OWNER TO postgres;

--
-- Name: guilog_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.guilog_id_seq OWNED BY nmsprime.guilog.id;


--
-- Name: hfcreq; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.hfcreq OWNER TO postgres;

--
-- Name: hfcreq_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.hfcreq_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.hfcreq_id_seq OWNER TO postgres;

--
-- Name: hfcreq_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.hfcreq_id_seq OWNED BY nmsprime.hfcreq.id;


--
-- Name: indices; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.indices OWNER TO postgres;

--
-- Name: indices_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.indices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.indices_id_seq OWNER TO postgres;

--
-- Name: indices_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.indices_id_seq OWNED BY nmsprime.indices.id;


--
-- Name: invoice; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.invoice OWNER TO postgres;

--
-- Name: invoice_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.invoice_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.invoice_id_seq OWNER TO postgres;

--
-- Name: invoice_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.invoice_id_seq OWNED BY nmsprime.invoice.id;


--
-- Name: ippool; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.ippool (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    netgw_id bigint,
    type nmsprime.ippool_type,
    net character varying(191),
    netmask character varying(191),
    ip_pool_start character varying(191),
    ip_pool_end character varying(191),
    router_ip character varying(191),
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
    active boolean DEFAULT true
);


ALTER TABLE nmsprime.ippool OWNER TO postgres;

--
-- Name: ippool_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ippool_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ippool_id_seq OWNER TO postgres;

--
-- Name: ippool_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ippool_id_seq OWNED BY nmsprime.ippool.id;


--
-- Name: item; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.item OWNER TO postgres;

--
-- Name: item_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.item_id_seq OWNER TO postgres;

--
-- Name: item_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.item_id_seq OWNED BY nmsprime.item.id;


--
-- Name: jobs; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.jobs_id_seq OWNED BY nmsprime.jobs.id;


--
-- Name: mibfile; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.mibfile OWNER TO postgres;

--
-- Name: mibfile_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.mibfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mibfile_id_seq OWNER TO postgres;

--
-- Name: mibfile_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.mibfile_id_seq OWNED BY nmsprime.mibfile.id;


--
-- Name: migrations; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.migrations (
    id bigint NOT NULL,
    migration character varying(255),
    batch bigint
);


ALTER TABLE nmsprime.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.migrations_id_seq OWNED BY nmsprime.migrations.id;


--
-- Name: modem; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    ipv4 bigint,
    address_to_invoice boolean,
    apartment_id bigint
);


ALTER TABLE nmsprime.modem OWNER TO postgres;

--
-- Name: modem_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.modem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_id_seq OWNER TO postgres;

--
-- Name: modem_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.modem_id_seq OWNED BY nmsprime.modem.id;


--
-- Name: modem_option; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.modem_option OWNER TO postgres;

--
-- Name: modem_option_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.modem_option_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_option_id_seq OWNER TO postgres;

--
-- Name: modem_option_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.modem_option_id_seq OWNED BY nmsprime.modem_option.id;


--
-- Name: mpr; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.mpr (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    type bigint,
    value text,
    netelement_id bigint,
    prio bigint,
    description text
);


ALTER TABLE nmsprime.mpr OWNER TO postgres;

--
-- Name: mpr_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.mpr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mpr_id_seq OWNER TO postgres;

--
-- Name: mpr_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.mpr_id_seq OWNED BY nmsprime.mpr.id;


--
-- Name: mprgeopos; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.mprgeopos (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    mpr_id bigint,
    lng numeric(9,6),
    lat numeric(9,6),
    description text
);


ALTER TABLE nmsprime.mprgeopos OWNER TO postgres;

--
-- Name: mprgeopos_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.mprgeopos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mprgeopos_id_seq OWNER TO postgres;

--
-- Name: mprgeopos_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.mprgeopos_id_seq OWNED BY nmsprime.mprgeopos.id;


--
-- Name: mta; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.mta OWNER TO postgres;

--
-- Name: mta_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.mta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mta_id_seq OWNER TO postgres;

--
-- Name: mta_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.mta_id_seq OWNED BY nmsprime.mta.id;


--
-- Name: nas; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.nas (
    id bigint NOT NULL,
    nasname character varying(128),
    shortname character varying(32),
    type character varying(30) DEFAULT 'other'::character varying,
    ports integer,
    secret character varying(60) DEFAULT 'secret'::character varying,
    server character varying(64),
    community character varying(50),
    description character varying(200) DEFAULT 'RADIUS Client'::character varying
);


ALTER TABLE nmsprime.nas OWNER TO postgres;

--
-- Name: nas_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.nas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.nas_id_seq OWNER TO postgres;

--
-- Name: nas_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.nas_id_seq OWNED BY nmsprime.nas.id;


--
-- Name: netelement; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.netelement (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    series bigint,
    options bigint,
    ip character varying(191),
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
    ELSE (((name)::text || '_'::text) || ((id)::character varying)::text)
END) STORED
);


ALTER TABLE nmsprime.netelement OWNER TO postgres;

--
-- Name: netelement_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.netelement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelement_id_seq OWNER TO postgres;

--
-- Name: netelement_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.netelement_id_seq OWNED BY nmsprime.netelement.id;


--
-- Name: netelementtype; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    base_type_id numeric
);


ALTER TABLE nmsprime.netelementtype OWNER TO postgres;

--
-- Name: netelementtype_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.netelementtype_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelementtype_id_seq OWNER TO postgres;

--
-- Name: netelementtype_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.netelementtype_id_seq OWNED BY nmsprime.netelementtype.id;


--
-- Name: netgw; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    ipv6 character varying(191)
);


ALTER TABLE nmsprime.netgw OWNER TO postgres;

--
-- Name: netgw_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.netgw_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netgw_id_seq OWNER TO postgres;

--
-- Name: netgw_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.netgw_id_seq OWNED BY nmsprime.netgw.id;


--
-- Name: node; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.node OWNER TO postgres;

--
-- Name: node_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.node_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.node_id_seq OWNER TO postgres;

--
-- Name: node_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.node_id_seq OWNED BY nmsprime.node.id;


--
-- Name: notifications; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.notifications (
    id character(36),
    type character varying(191),
    notifiable_type character varying(191),
    notifiable_id numeric,
    data text,
    read_at timestamp with time zone,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.notifications OWNER TO postgres;

--
-- Name: numberrange; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.numberrange OWNER TO postgres;

--
-- Name: numberrange_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.numberrange_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.numberrange_id_seq OWNER TO postgres;

--
-- Name: numberrange_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.numberrange_id_seq OWNED BY nmsprime.numberrange.id;


--
-- Name: oid; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.oid OWNER TO postgres;

--
-- Name: oid_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.oid_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.oid_id_seq OWNER TO postgres;

--
-- Name: oid_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.oid_id_seq OWNED BY nmsprime.oid.id;


--
-- Name: overduedebts; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.overduedebts OWNER TO postgres;

--
-- Name: overduedebts_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.overduedebts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.overduedebts_id_seq OWNER TO postgres;

--
-- Name: overduedebts_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.overduedebts_id_seq OWNED BY nmsprime.overduedebts.id;


--
-- Name: parameter; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.parameter OWNER TO postgres;

--
-- Name: parameter_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.parameter_id_seq OWNER TO postgres;

--
-- Name: parameter_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.parameter_id_seq OWNED BY nmsprime.parameter.id;


--
-- Name: permissions; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.permissions OWNER TO postgres;

--
-- Name: phonebookentry; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.phonebookentry OWNER TO postgres;

--
-- Name: phonebookentry_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.phonebookentry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonebookentry_id_seq OWNER TO postgres;

--
-- Name: phonebookentry_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.phonebookentry_id_seq OWNED BY nmsprime.phonebookentry.id;


--
-- Name: phonenumber; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.phonenumber OWNER TO postgres;

--
-- Name: phonenumber_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumber_id_seq OWNER TO postgres;

--
-- Name: phonenumber_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.phonenumber_id_seq OWNED BY nmsprime.phonenumber.id;


--
-- Name: phonenumbermanagement; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.phonenumbermanagement OWNER TO postgres;

--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.phonenumbermanagement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumbermanagement_id_seq OWNER TO postgres;

--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.phonenumbermanagement_id_seq OWNED BY nmsprime.phonenumbermanagement.id;


--
-- Name: phonetariff; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.phonetariff OWNER TO postgres;

--
-- Name: phonetariff_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.phonetariff_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonetariff_id_seq OWNER TO postgres;

--
-- Name: phonetariff_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.phonetariff_id_seq OWNED BY nmsprime.phonetariff.id;


--
-- Name: product; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.product OWNER TO postgres;

--
-- Name: product_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.product_id_seq OWNER TO postgres;

--
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.product_id_seq OWNED BY nmsprime.product.id;


--
-- Name: provbase; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.provbase (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    provisioning_server character varying(191),
    ro_community character varying(191),
    rw_community character varying(191),
    notif_mail character varying(191),
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


ALTER TABLE nmsprime.provbase OWNER TO postgres;

--
-- Name: provbase_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.provbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provbase_id_seq OWNER TO postgres;

--
-- Name: provbase_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.provbase_id_seq OWNED BY nmsprime.provbase.id;


--
-- Name: provmon; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.provmon OWNER TO postgres;

--
-- Name: provmon_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.provmon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provmon_id_seq OWNER TO postgres;

--
-- Name: provmon_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.provmon_id_seq OWNED BY nmsprime.provmon.id;


--
-- Name: provvoip; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.provvoip OWNER TO postgres;

--
-- Name: provvoip_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.provvoip_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provvoip_id_seq OWNER TO postgres;

--
-- Name: provvoip_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.provvoip_id_seq OWNED BY nmsprime.provvoip.id;


--
-- Name: qos; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    us_name character varying(191)
);


ALTER TABLE nmsprime.qos OWNER TO postgres;

--
-- Name: qos_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.qos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.qos_id_seq OWNER TO postgres;

--
-- Name: qos_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.qos_id_seq OWNED BY nmsprime.qos.id;


--
-- Name: radacct; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radacct (
    radacctid bigint NOT NULL,
    acctsessionid character varying(64) DEFAULT ''::character varying,
    acctuniqueid character varying(32) DEFAULT ''::character varying,
    username character varying(64) DEFAULT ''::character varying,
    groupname character varying(64) DEFAULT ''::character varying,
    realm character varying(64) DEFAULT ''::character varying,
    nasipaddress character varying(15) DEFAULT ''::character varying,
    nasportid character varying(50),
    nasporttype character varying(32),
    acctstarttime timestamp with time zone,
    acctupdatetime timestamp with time zone,
    acctstoptime timestamp with time zone,
    acctinterval bigint,
    acctsessiontime bigint,
    acctauthentic character varying(32),
    connectinfo_start character varying(50),
    connectinfo_stop character varying(50),
    acctinputoctets bigint,
    acctoutputoctets bigint,
    calledstationid character varying(50) DEFAULT ''::character varying,
    callingstationid character varying(50) DEFAULT ''::character varying,
    acctterminatecause character varying(32) DEFAULT ''::character varying,
    servicetype character varying(32),
    framedprotocol character varying(32),
    framedipaddress character varying(15) DEFAULT ''::character varying
);


ALTER TABLE nmsprime.radacct OWNER TO postgres;

--
-- Name: radacct_radacctid_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radacct_radacctid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radacct_radacctid_seq OWNER TO postgres;

--
-- Name: radacct_radacctid_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radacct_radacctid_seq OWNED BY nmsprime.radacct.radacctid;


--
-- Name: radcheck; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radcheck (
    id bigint NOT NULL,
    username character varying(64) DEFAULT ''::character varying,
    attribute character varying(64) DEFAULT ''::character varying,
    op character(2) DEFAULT '=='::bpchar,
    value character varying(253) DEFAULT ''::character varying
);


ALTER TABLE nmsprime.radcheck OWNER TO postgres;

--
-- Name: radcheck_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radcheck_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radcheck_id_seq OWNER TO postgres;

--
-- Name: radcheck_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radcheck_id_seq OWNED BY nmsprime.radcheck.id;


--
-- Name: radgroupcheck; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radgroupcheck (
    id bigint NOT NULL,
    groupname character varying(64) DEFAULT ''::character varying,
    attribute character varying(64) DEFAULT ''::character varying,
    op character(2) DEFAULT '=='::bpchar,
    value character varying(253) DEFAULT ''::character varying
);


ALTER TABLE nmsprime.radgroupcheck OWNER TO postgres;

--
-- Name: radgroupcheck_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radgroupcheck_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radgroupcheck_id_seq OWNER TO postgres;

--
-- Name: radgroupcheck_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radgroupcheck_id_seq OWNED BY nmsprime.radgroupcheck.id;


--
-- Name: radgroupreply; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radgroupreply (
    id bigint NOT NULL,
    groupname character varying(64) DEFAULT ''::character varying,
    attribute character varying(64) DEFAULT ''::character varying,
    op character(2) DEFAULT '='::bpchar,
    value character varying(253) DEFAULT ''::character varying
);


ALTER TABLE nmsprime.radgroupreply OWNER TO postgres;

--
-- Name: radgroupreply_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radgroupreply_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radgroupreply_id_seq OWNER TO postgres;

--
-- Name: radgroupreply_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radgroupreply_id_seq OWNED BY nmsprime.radgroupreply.id;


--
-- Name: radippool; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radippool (
    id bigint NOT NULL,
    pool_name character varying(30),
    framedipaddress character varying(15) DEFAULT ''::character varying,
    nasipaddress character varying(15) DEFAULT ''::character varying,
    calledstationid character varying(30),
    callingstationid character varying(30),
    expiry_time timestamp with time zone,
    username character varying(64) DEFAULT ''::character varying,
    pool_key character varying(30)
);


ALTER TABLE nmsprime.radippool OWNER TO postgres;

--
-- Name: radippool_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radippool_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radippool_id_seq OWNER TO postgres;

--
-- Name: radippool_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radippool_id_seq OWNED BY nmsprime.radippool.id;


--
-- Name: radpostauth; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radpostauth (
    id bigint NOT NULL,
    username character varying(64) DEFAULT ''::character varying,
    pass character varying(64) DEFAULT ''::character varying,
    reply character varying(32) DEFAULT ''::character varying,
    authdate timestamp with time zone
);


ALTER TABLE nmsprime.radpostauth OWNER TO postgres;

--
-- Name: radpostauth_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radpostauth_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radpostauth_id_seq OWNER TO postgres;

--
-- Name: radpostauth_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radpostauth_id_seq OWNED BY nmsprime.radpostauth.id;


--
-- Name: radreply; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radreply (
    id bigint NOT NULL,
    username character varying(64) DEFAULT ''::character varying,
    attribute character varying(64) DEFAULT ''::character varying,
    op character(2) DEFAULT '='::bpchar,
    value character varying(253) DEFAULT ''::character varying
);


ALTER TABLE nmsprime.radreply OWNER TO postgres;

--
-- Name: radreply_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radreply_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radreply_id_seq OWNER TO postgres;

--
-- Name: radreply_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radreply_id_seq OWNED BY nmsprime.radreply.id;


--
-- Name: radusergroup; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.radusergroup (
    id bigint NOT NULL,
    username character varying(64) DEFAULT ''::character varying,
    groupname character varying(64) DEFAULT ''::character varying,
    priority bigint DEFAULT '1'::bigint
);


ALTER TABLE nmsprime.radusergroup OWNER TO postgres;

--
-- Name: radusergroup_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.radusergroup_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.radusergroup_id_seq OWNER TO postgres;

--
-- Name: radusergroup_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.radusergroup_id_seq OWNED BY nmsprime.radusergroup.id;


--
-- Name: realty; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.realty OWNER TO postgres;

--
-- Name: realty_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.realty_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.realty_id_seq OWNER TO postgres;

--
-- Name: realty_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.realty_id_seq OWNED BY nmsprime.realty.id;


--
-- Name: roles; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.roles OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.roles_id_seq OWNER TO postgres;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.roles_id_seq OWNED BY nmsprime.roles.id;


--
-- Name: salesman; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.salesman OWNER TO postgres;

--
-- Name: salesman_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.salesman_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.salesman_id_seq OWNER TO postgres;

--
-- Name: salesman_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.salesman_id_seq OWNED BY nmsprime.salesman.id;


--
-- Name: sepaaccount; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.sepaaccount OWNER TO postgres;

--
-- Name: sepaaccount_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.sepaaccount_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepaaccount_id_seq OWNER TO postgres;

--
-- Name: sepaaccount_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.sepaaccount_id_seq OWNED BY nmsprime.sepaaccount.id;


--
-- Name: sepamandate; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.sepamandate OWNER TO postgres;

--
-- Name: sepamandate_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.sepamandate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepamandate_id_seq OWNER TO postgres;

--
-- Name: sepamandate_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.sepamandate_id_seq OWNED BY nmsprime.sepamandate.id;


--
-- Name: settlementrun; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.settlementrun OWNER TO postgres;

--
-- Name: settlementrun_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.settlementrun_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.settlementrun_id_seq OWNER TO postgres;

--
-- Name: settlementrun_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.settlementrun_id_seq OWNED BY nmsprime.settlementrun.id;


--
-- Name: sla; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.sla (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    license character varying(191)
);


ALTER TABLE nmsprime.sla OWNER TO postgres;

--
-- Name: sla_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.sla_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sla_id_seq OWNER TO postgres;

--
-- Name: sla_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.sla_id_seq OWNED BY nmsprime.sla.id;


--
-- Name: supportrequest; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.supportrequest OWNER TO postgres;

--
-- Name: supportrequest_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.supportrequest_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.supportrequest_id_seq OWNER TO postgres;

--
-- Name: supportrequest_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.supportrequest_id_seq OWNED BY nmsprime.supportrequest.id;


--
-- Name: ticket; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.ticket OWNER TO postgres;

--
-- Name: ticket_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_id_seq OWNER TO postgres;

--
-- Name: ticket_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ticket_id_seq OWNED BY nmsprime.ticket.id;


--
-- Name: ticket_type; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.ticket_type OWNER TO postgres;

--
-- Name: ticket_type_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ticket_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_id_seq OWNER TO postgres;

--
-- Name: ticket_type_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ticket_type_id_seq OWNED BY nmsprime.ticket_type.id;


--
-- Name: ticket_type_ticket; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.ticket_type_ticket (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    ticket_type_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_type_ticket OWNER TO postgres;

--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ticket_type_ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_ticket_id_seq OWNER TO postgres;

--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ticket_type_ticket_id_seq OWNED BY nmsprime.ticket_type_ticket.id;


--
-- Name: ticket_user; Type: TABLE; Schema: nmsprime; Owner: postgres
--

CREATE TABLE nmsprime.ticket_user (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    user_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_user OWNER TO postgres;

--
-- Name: ticket_user_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ticket_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_user_id_seq OWNER TO postgres;

--
-- Name: ticket_user_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ticket_user_id_seq OWNED BY nmsprime.ticket_user.id;


--
-- Name: ticketsystem; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.ticketsystem OWNER TO postgres;

--
-- Name: ticketsystem_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.ticketsystem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticketsystem_id_seq OWNER TO postgres;

--
-- Name: ticketsystem_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.ticketsystem_id_seq OWNED BY nmsprime.ticketsystem.id;


--
-- Name: trcclass; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.trcclass OWNER TO postgres;

--
-- Name: trcclass_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.trcclass_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.trcclass_id_seq OWNER TO postgres;

--
-- Name: trcclass_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.trcclass_id_seq OWNED BY nmsprime.trcclass.id;


--
-- Name: users; Type: TABLE; Schema: nmsprime; Owner: postgres
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
    hastruck boolean DEFAULT false
);


ALTER TABLE nmsprime.users OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.users_id_seq OWNER TO postgres;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.users_id_seq OWNED BY nmsprime.users.id;


--
-- Name: websockets_statistics_entries; Type: TABLE; Schema: nmsprime; Owner: postgres
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


ALTER TABLE nmsprime.websockets_statistics_entries OWNER TO postgres;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE; Schema: nmsprime; Owner: postgres
--

CREATE SEQUENCE nmsprime.websockets_statistics_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.websockets_statistics_entries_id_seq OWNER TO postgres;

--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: nmsprime; Owner: postgres
--

ALTER SEQUENCE nmsprime.websockets_statistics_entries_id_seq OWNED BY nmsprime.websockets_statistics_entries.id;


--
-- Name: abilities id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.abilities ALTER COLUMN id SET DEFAULT nextval('nmsprime.abilities_id_seq'::regclass);


--
-- Name: accountingrecord id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.accountingrecord ALTER COLUMN id SET DEFAULT nextval('nmsprime.accountingrecord_id_seq'::regclass);


--
-- Name: apartment id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.apartment ALTER COLUMN id SET DEFAULT nextval('nmsprime.apartment_id_seq'::regclass);


--
-- Name: billingbase id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.billingbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.billingbase_id_seq'::regclass);


--
-- Name: carriercode id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.carriercode ALTER COLUMN id SET DEFAULT nextval('nmsprime.carriercode_id_seq'::regclass);


--
-- Name: ccc id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ccc ALTER COLUMN id SET DEFAULT nextval('nmsprime.ccc_id_seq'::regclass);


--
-- Name: comment id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.comment ALTER COLUMN id SET DEFAULT nextval('nmsprime.comment_id_seq'::regclass);


--
-- Name: company id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.company ALTER COLUMN id SET DEFAULT nextval('nmsprime.company_id_seq'::regclass);


--
-- Name: configfile id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.configfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.configfile_id_seq'::regclass);


--
-- Name: contact id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.contact ALTER COLUMN id SET DEFAULT nextval('nmsprime.contact_id_seq'::regclass);


--
-- Name: contract id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.contract ALTER COLUMN id SET DEFAULT nextval('nmsprime.contract_id_seq'::regclass);


--
-- Name: costcenter id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.costcenter ALTER COLUMN id SET DEFAULT nextval('nmsprime.costcenter_id_seq'::regclass);


--
-- Name: debt id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.debt ALTER COLUMN id SET DEFAULT nextval('nmsprime.debt_id_seq'::regclass);


--
-- Name: domain id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.domain ALTER COLUMN id SET DEFAULT nextval('nmsprime.domain_id_seq'::regclass);


--
-- Name: ekpcode id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ekpcode ALTER COLUMN id SET DEFAULT nextval('nmsprime.ekpcode_id_seq'::regclass);


--
-- Name: endpoint id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.endpoint ALTER COLUMN id SET DEFAULT nextval('nmsprime.endpoint_id_seq'::regclass);


--
-- Name: enviacontract id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviacontract ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviacontract_id_seq'::regclass);


--
-- Name: enviaorder id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorder ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_id_seq'::regclass);


--
-- Name: enviaorder_phonenumber id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorder_phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_phonenumber_id_seq'::regclass);


--
-- Name: enviaorderdocument id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorderdocument ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorderdocument_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.failed_jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.failed_jobs_id_seq'::regclass);


--
-- Name: favorite_netelements id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.favorite_netelements ALTER COLUMN id SET DEFAULT nextval('nmsprime.favorite_netelements_id_seq'::regclass);


--
-- Name: global_config id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.global_config ALTER COLUMN id SET DEFAULT nextval('nmsprime.global_config_id_seq'::regclass);


--
-- Name: guilog id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.guilog ALTER COLUMN id SET DEFAULT nextval('nmsprime.guilog_id_seq'::regclass);


--
-- Name: hfcreq id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.hfcreq ALTER COLUMN id SET DEFAULT nextval('nmsprime.hfcreq_id_seq'::regclass);


--
-- Name: indices id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.indices ALTER COLUMN id SET DEFAULT nextval('nmsprime.indices_id_seq'::regclass);


--
-- Name: invoice id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.invoice ALTER COLUMN id SET DEFAULT nextval('nmsprime.invoice_id_seq'::regclass);


--
-- Name: ippool id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ippool ALTER COLUMN id SET DEFAULT nextval('nmsprime.ippool_id_seq'::regclass);


--
-- Name: item id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.item ALTER COLUMN id SET DEFAULT nextval('nmsprime.item_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.jobs_id_seq'::regclass);


--
-- Name: mibfile id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mibfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.mibfile_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.migrations ALTER COLUMN id SET DEFAULT nextval('nmsprime.migrations_id_seq'::regclass);


--
-- Name: modem id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.modem ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_id_seq'::regclass);


--
-- Name: modem_option id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.modem_option ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_option_id_seq'::regclass);


--
-- Name: mpr id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mpr ALTER COLUMN id SET DEFAULT nextval('nmsprime.mpr_id_seq'::regclass);


--
-- Name: mprgeopos id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mprgeopos ALTER COLUMN id SET DEFAULT nextval('nmsprime.mprgeopos_id_seq'::regclass);


--
-- Name: mta id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mta ALTER COLUMN id SET DEFAULT nextval('nmsprime.mta_id_seq'::regclass);


--
-- Name: nas id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.nas ALTER COLUMN id SET DEFAULT nextval('nmsprime.nas_id_seq'::regclass);


--
-- Name: netelement id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netelement ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelement_id_seq'::regclass);


--
-- Name: netelementtype id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netelementtype ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelementtype_id_seq'::regclass);


--
-- Name: netgw id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netgw ALTER COLUMN id SET DEFAULT nextval('nmsprime.netgw_id_seq'::regclass);


--
-- Name: node id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.node ALTER COLUMN id SET DEFAULT nextval('nmsprime.node_id_seq'::regclass);


--
-- Name: numberrange id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.numberrange ALTER COLUMN id SET DEFAULT nextval('nmsprime.numberrange_id_seq'::regclass);


--
-- Name: oid id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.oid ALTER COLUMN id SET DEFAULT nextval('nmsprime.oid_id_seq'::regclass);


--
-- Name: overduedebts id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.overduedebts ALTER COLUMN id SET DEFAULT nextval('nmsprime.overduedebts_id_seq'::regclass);


--
-- Name: parameter id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.parameter ALTER COLUMN id SET DEFAULT nextval('nmsprime.parameter_id_seq'::regclass);


--
-- Name: phonebookentry id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonebookentry ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonebookentry_id_seq'::regclass);


--
-- Name: phonenumber id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumber_id_seq'::regclass);


--
-- Name: phonenumbermanagement id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonenumbermanagement ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumbermanagement_id_seq'::regclass);


--
-- Name: phonetariff id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonetariff ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonetariff_id_seq'::regclass);


--
-- Name: product id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.product ALTER COLUMN id SET DEFAULT nextval('nmsprime.product_id_seq'::regclass);


--
-- Name: provbase id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.provbase_id_seq'::regclass);


--
-- Name: provmon id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provmon ALTER COLUMN id SET DEFAULT nextval('nmsprime.provmon_id_seq'::regclass);


--
-- Name: provvoip id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provvoip ALTER COLUMN id SET DEFAULT nextval('nmsprime.provvoip_id_seq'::regclass);


--
-- Name: qos id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.qos ALTER COLUMN id SET DEFAULT nextval('nmsprime.qos_id_seq'::regclass);


--
-- Name: radacct radacctid; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radacct ALTER COLUMN radacctid SET DEFAULT nextval('nmsprime.radacct_radacctid_seq'::regclass);


--
-- Name: radcheck id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radcheck ALTER COLUMN id SET DEFAULT nextval('nmsprime.radcheck_id_seq'::regclass);


--
-- Name: radgroupcheck id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radgroupcheck ALTER COLUMN id SET DEFAULT nextval('nmsprime.radgroupcheck_id_seq'::regclass);


--
-- Name: radgroupreply id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radgroupreply ALTER COLUMN id SET DEFAULT nextval('nmsprime.radgroupreply_id_seq'::regclass);


--
-- Name: radippool id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radippool ALTER COLUMN id SET DEFAULT nextval('nmsprime.radippool_id_seq'::regclass);


--
-- Name: radpostauth id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radpostauth ALTER COLUMN id SET DEFAULT nextval('nmsprime.radpostauth_id_seq'::regclass);


--
-- Name: radreply id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radreply ALTER COLUMN id SET DEFAULT nextval('nmsprime.radreply_id_seq'::regclass);


--
-- Name: radusergroup id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radusergroup ALTER COLUMN id SET DEFAULT nextval('nmsprime.radusergroup_id_seq'::regclass);


--
-- Name: realty id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.realty ALTER COLUMN id SET DEFAULT nextval('nmsprime.realty_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.roles ALTER COLUMN id SET DEFAULT nextval('nmsprime.roles_id_seq'::regclass);


--
-- Name: salesman id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.salesman ALTER COLUMN id SET DEFAULT nextval('nmsprime.salesman_id_seq'::regclass);


--
-- Name: sepaaccount id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sepaaccount ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepaaccount_id_seq'::regclass);


--
-- Name: sepamandate id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sepamandate ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepamandate_id_seq'::regclass);


--
-- Name: settlementrun id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.settlementrun ALTER COLUMN id SET DEFAULT nextval('nmsprime.settlementrun_id_seq'::regclass);


--
-- Name: sla id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sla ALTER COLUMN id SET DEFAULT nextval('nmsprime.sla_id_seq'::regclass);


--
-- Name: supportrequest id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.supportrequest ALTER COLUMN id SET DEFAULT nextval('nmsprime.supportrequest_id_seq'::regclass);


--
-- Name: ticket id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_id_seq'::regclass);


--
-- Name: ticket_type id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_type ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_id_seq'::regclass);


--
-- Name: ticket_type_ticket id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_type_ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_ticket_id_seq'::regclass);


--
-- Name: ticket_user id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_user ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_user_id_seq'::regclass);


--
-- Name: ticketsystem id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticketsystem ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticketsystem_id_seq'::regclass);


--
-- Name: trcclass id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.trcclass ALTER COLUMN id SET DEFAULT nextval('nmsprime.trcclass_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.users ALTER COLUMN id SET DEFAULT nextval('nmsprime.users_id_seq'::regclass);


--
-- Name: websockets_statistics_entries id; Type: DEFAULT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.websockets_statistics_entries ALTER COLUMN id SET DEFAULT nextval('nmsprime.websockets_statistics_entries_id_seq'::regclass);


--
-- Data for Name: abilities; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.abilities (id, name, title, entity_id, entity_type, only_owned, scope, created_at, updated_at, deleted_at) FROM stdin;
1	*	Manage users	\N	App\\User	t	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
2	*	All abilities	\N	*	f	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
3	view	View everything	\N	*	f	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
4	use api	Use api	\N	\N	f	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
5	see income chart	See income chart	\N	\N	f	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
6	*	Manage roles	\N	roles	f	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
8	*	Manage global configs	\N	App\\GlobalConfig	f	\N	2022-02-18 08:43:34+01	2022-02-18 08:43:34+01	\N
9	view	View gui logs	\N	App\\GuiLog	f	\N	2022-02-18 08:43:34+01	2022-02-18 08:43:34+01	\N
58	view_analysis_pages_of	View analysis pages of modems	\N	Modules\\ProvBase\\Entities\\Modem	f	\N	2022-02-18 08:47:20+01	2022-02-18 08:47:20+01	\N
59	view_analysis_pages_of	View analysis pages of netgw	\N	Modules\\ProvBase\\Entities\\NetGw	f	\N	2022-02-18 08:47:20+01	2022-02-18 08:47:20+01	\N
60	download	Download settlement runs	\N	Modules\\BillingBase\\Entities\\SettlementRun	f	\N	2022-02-18 08:47:20+01	2022-02-18 08:47:20+01	\N
\.


--
-- Data for Name: accountingrecord; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.accountingrecord (id, created_at, updated_at, deleted_at, contract_id, name, product_id, ratio, count, charge, sepaaccount_id, invoice_nr, settlementrun_id) FROM stdin;
\.


--
-- Data for Name: apartment; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.apartment (id, created_at, updated_at, deleted_at, realty_id, number, floor, connected, occupied, description, connection_type, code) FROM stdin;
\.


--
-- Data for Name: assigned_roles; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.assigned_roles (role_id, entity_id, entity_type, scope, created_at, updated_at) FROM stdin;
1	1	App\\User	\N	\N	\N
2	1	App\\User	\N	\N	\N
\.


--
-- Data for Name: authreminders; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.authreminders (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: billingbase; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.billingbase (id, created_at, updated_at, deleted_at, rcd, currency, tax, mandate_ref_template, split, termination_fix, userlang, cdr_offset, voip_extracharge_default, voip_extracharge_mobile_national, cdr_retention_period, fluid_valid_dates, show_ags, adapt_item_start) FROM stdin;
1	\N	\N	\N	0	EUR	19	\N	f	f	de	1	0	0	6	f	f	f
\.


--
-- Data for Name: carriercode; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.carriercode (id, created_at, updated_at, deleted_at, carrier_code, company) FROM stdin;
1	\N	\N	\N	0	-
2	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D001	Telekom Deutschland GmbH
3	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D003	multiConnect GmbH
4	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D004	NETCOLOGNE GmbH
5	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D007	M-net Telekommunikations GmbH
6	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D008	Verizon Deutschland GmbH
7	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D009	Vodafone GmbH
8	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D011	1&1 Versatel Deutschland GmbH
9	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D012	BT (Germany) GmbH & Co. OHG
10	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D013	EWE TEL GmbH
11	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D017	Colt Technology Services GmbH
12	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D019	Telefonica Germany GmbH & Co. OHG
13	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D020	dtms GmbH
14	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D021	01024 Telefondienste GmbH
15	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D022	Communication Services TELE2 GmbH
16	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D023	HeLi NET Telekommunikation GmbH & Co. KG
17	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D025	IN-telegence GmbH
18	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D027	Ventelo GmbH
19	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D028	3U Telecom GmbH
20	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D030	Younip Telecom GmbH
21	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D031	EWE TEL GmbH
22	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D032	DOKOM Gesellschaft für Telekommunikation mbH
23	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D033	01049 GmbH
24	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D034	11 88 0 Solutions AG
25	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D035	BITel Gesellschaft für Telekommunikation mbH
26	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D037	WOBCOM GmbH Wolfsburg für Telekommunikation und Dienstleistungen
27	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D038	TNG Stadtnetz GmbH
28	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D040	Westend Management GmbH
29	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D043	M-net Telekommunikations GmbH
30	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D045	HLkomm Telekommunikations GmbH
31	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D046	envia TEL GmbH
32	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D047	01051 Telecom GmbH
33	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D048	EWE TEL GmbH
34	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D050	htp GmbH
35	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D051	Callax Telecom Services GmbH
36	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D052	M-net Telekommunikations GmbH
37	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D053	Breitbandkabelgesellschaft mbH
38	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D054	NETAACHEN GmbH
39	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D056	Vodafone GmbH
40	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D057	envia TEL GmbH
41	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D058	Carrier 1 International AG
42	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D061	Telefonica Germany GmbH & Co. OHG
43	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D063	Kube & Au GmbH
44	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D065	Telefonica Germany GmbH & Co. OHG
45	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D070	First Communication GmbH
46	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D071	Netcom Kassel GmbH
47	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D072	ENTEGA Medianet GmbH
48	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D073	dtms GmbH
49	2022-02-18 08:44:18+01	2022-02-18 08:44:18+01	\N	D074	DATA CMR GmbH & Co.KG
50	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D075	Median Telecom GmbH
51	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D076	Stadtwerke Schwedt GmbH
52	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D078	Vodafone GmbH
53	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D081	Daten- und Telekommunikations-GmbH Dessau
54	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D084	VSE NET GmbH
55	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D085	EWE TEL GmbH
56	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D086	Mega Communications GmbH
57	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D087	TeleNEC Telekommunikation Neustadt GmbH & Co.KG
58	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D088	010023 GmbH
59	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D089	EWE TEL GmbH
60	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D090	010023 GmbH
61	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D093	1&1 Versatel Deutschland GmbH
62	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D094	Spider Telecom GmbH
63	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D095	MPA NET Gesellschaft für Telekommunikation mbH
64	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D097	multiConnect GmbH
65	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D098	GöTel GmbH
66	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D100	Goodlines GmbH
67	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D101	wilhelm.tel GmbH
68	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D104	OneTel Telecommunication GmbH
69	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D105	FirstMark Communications Deutschland GmbH
70	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D106	smart-DSL GmbH
71	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D107	3U Telecom GmbH
72	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D108	mr. next id GmbH & Co.KG
73	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D109	Thüringer Netkom GmbH
74	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D113	inopla GmbH
75	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D114	Deutsche Telefon Standard AG
76	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D115	MDCC Magdeburg City-Com GmbH
77	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D117	HFO Telecom Vertriebs GmbH
78	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D118	EXACOR GmbH
79	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D120	Vodafone NRW GmbH
80	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D121	TELTA Citynetz Eberswalde GmbH
81	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D122	01018 GmbH
82	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D123	Plusnet GmbH
83	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D124	Telekom Deutschland GmbH
84	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D125	nexnet GmbH
85	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D127	Vodafone BW GmbH
86	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D129	01081 Telecom GmbH
87	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D130	Maestro Telecom GmbH
88	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D131	CALLAX Holding GmbH
89	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D133	PFALZKOM GmbH
90	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D134	01071 Telecom GmbH
91	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D135	Callax Telecom Services GmbH
92	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D137	Broadnet Services GmbH
93	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D138	01066 GmbH
94	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D139	01059 GmbH
95	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D141	envito GmbH & Co.KG
96	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D142	net services GmbH & Co. KG
97	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D143	First Telecom GmbH
98	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D144	sdt.net AG
99	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D145	Callax Telecom Services GmbH
100	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D146	[netzquadrat] Gesellschaft für Telekommunikation mbH
101	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D147	Maestro Telecom GmbH
102	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D148	01081 Telecom GmbH
103	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D150	Telekom Deutschland GmbH
104	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D153	amplus AG
105	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D154	PLANinterNET VoIP-GmbH
106	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D155	TNG Stadtnetz GmbH
107	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D156	one4one Services GmbH
108	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D157	Kabelfernsehen München
109	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D158	Telogic Germany GmbH
110	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D159	ccn corporate communication networks GmbH
111	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D160	PGmedia Telecom GmbH
112	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D163	dus.net GmbH
113	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D164	Callax Telecom Services GmbH
114	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D166	Callax Telecom Services GmbH
115	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D167	DNS:Net Internet Service GmbH
116	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D168	bn:t Blatzheim Networks Telecom GmbH
117	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D170	01057 Protel GmbH
118	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D171	toplink GmbH
119	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D172	Callax Telecom Services GmbH
120	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D173	Callax Telecom Services GmbH
121	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D174	T-Systems Business Services GmbH
122	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D175	Callax Telecom Services GmbH
123	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D176	01051 Telecom GmbH
124	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D178	MK Netzdienste GmbH & Co. KG
125	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D179	Callax Telecom Services GmbH
126	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D180	Outbox AG
127	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D181	Callax Telecom Services GmbH
128	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D182	DOKOM Gesellschaft für Telekommunikation mbH
129	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D183	Discount Telecom S&V GmbH
130	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D184	Forester GmbH
131	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D185	01081 Telecom GmbH
132	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D186	TeleSon Vertriebs GmbH
133	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D188	Inocom GmbH
134	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D189	PURtel.com GmbH
135	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D190	economore GmbH & Co. KG
136	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D191	Vodafone Kabel Deutschland GmbH
137	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D193	YIPL OHG
138	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D194	G-FIT Gesellschaft für innovativeTelekommunikationsdienste mbH & Co. KG
139	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D195	01071 Telecom GmbH
140	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D197	vitroconnect systems GmbH
141	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D198	Callax Telecom Services GmbH
142	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D199	010091 UG (haftungsbeschränkt)
143	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D201	1&1 Telecom GmbH
144	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D202	inexio Informationstechnologie und Telekommunikation GmbH66740 Saarlouis
145	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D204	eco Service GmbH
146	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D205	Truphone GmbH
147	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D206	LineCall Telecom GmbH
148	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D207	OpenNumbers GmbH
149	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D208	Orange Business Germany GmbH
150	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D209	Televersa Online GmbH
151	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D210	01085 GmbH
152	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D211	Fonfriends Telecom GmbH
153	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D212	AS-Infodienste GmbH
154	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D213	SNT Greifswald GmbH
155	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D214	VSE NET GmbH
156	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D215	OVH GmbH
157	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D216	meetyoo conferencing GmbH
158	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D217	Smart Products GmbH
159	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D218	[netzquadrat] Gesellschaft für Telekommunikation mbH
160	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D219	Teleservice Company GmbH
161	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D220	VOXBONE SA
162	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D222	IN-telegence GmbH
163	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D223	42one GmbH
164	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D224	wel.de Gesellschaft für Informationsdienste mbh
165	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D225	TeleVita Kommunikationsdienste GmbH (i.Gr.)
166	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D226	Spider Telecom GmbH
167	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D228	Verizon Deutschland GmbH
168	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D229	11 88 0 Solutions AG
169	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D230	01018 GmbH
170	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D231	Enghouse Networks (Germany) GmbH
171	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D232	11 88 0 Internet Services AG
172	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D233	11 88 0 Internet Services AG
173	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D235	M-net Telekommunikations GmbH
174	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D239	MEGA MOBILES GmbH
175	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D240	Equada GmbH
176	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D241	Colt Technology Services GmbH
177	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D242	Alnitak GmbH
178	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D244	IKTel UG
179	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D245	Gossip GmbH
180	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D246	Kube & Au GmbH
181	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D247	010091 UG (haftungsbeschränkt)
182	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D248	[netzquadrat] Gesellschaft für Telekommunikation mbH
183	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D249	Lycamobile Europe Ltd., London, United KingdomEmpfangsbevollmächtigter: Dr. Peter Schmitz, JUCONOMY RA, Mörsenbroicher Weg 200
184	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D250	Teleflash GmbH
185	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D251	Dritte Hansestar GmbH
186	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D252	OpenNumbers GmbH
187	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D253	talkyou telecom UG (haftungsbeschränkt)
188	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D254	01073 GmbH
189	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D257	Younip Telecom GmbH
190	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D258	LineCall GmbH
191	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D259	KADSOFT Computer GmbH Freital Kommunikation-Automation-Datentechnik
192	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D260	mr. next id GmbH & Co.KG
193	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D261	Sipgate Wireless GmbH
194	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D262	just digits GmbH
195	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D263	inexio Informationstechnologie und Telekommunikation GmbH66740 Saarlouis
196	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D264	01048 Telecom GmbH
197	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D265	Northern Access GmbH
198	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D266	mr. communication GmbH
199	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D267	HLkomm Telekommunikations GmbH
200	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D268	MK Netzdienste GmbH & Co. KG
201	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D270	G-FIT Gesellschaft für innovativeTelekommunikationsdienste mbH & Co. KG
202	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D271	Outbox AG
203	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D273	First Telecom GmbH
204	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D274	First Communication GmbH
205	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D275	FPS InformationsSysteme GmbH
206	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D276	Herzo Media GmbH & Co.KG
207	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D277	EXACOR GmbH
208	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D278	pop-interactive GmbH
209	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D279	Freikom Stefan Frech & Thorsten Reimer GbR
210	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D280	Ilm-Provider UG (haftungsbeschränkt)
211	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D281	TELE AG
212	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D282	dtms GmbH
213	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D283	wilhelm.tel GmbH
214	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D284	ACARA Telecom GmbH
215	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D286	01051 Telecom GmbH
216	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D287	ENTEGA Medianet GmbH
217	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D288	EPCAN GmbH
218	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D289	bn:t Blatzheim Networks Telecom GmbH
219	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D290	yuilop s.l.
220	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D291	Stadtwerke Schwedt GmbH
221	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D292	BT (Germany) GmbH & Co. OHG
222	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D294	SBC (Germany) GmbH & Co.KG
223	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D296	Marco Bungalski GmbH
224	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D297	Netcom Kassel GmbH
225	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D298	[netzquadrat] Gesellschaft für Telekommunikation mbH
226	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D299	GDV Dienstleistungsgesellschaft GmbH & Co.KG
227	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D301	Aleando GmbH
228	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D302	net-and-phone GmbH
229	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D303	Stadtnetz Bamberg Gesellschaft für Telekommunikation mbH
230	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D304	SCHREGO Communications LTD
231	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D306	argon networks UG
232	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D307	Senel, Mustafa, Bosporus Solutions
233	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D308	PrimaCom Berlin GmbH
234	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D309	SBC (Germany) GmbH & Co.KG
235	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D310	toplink GmbH
236	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D311	interactive digital media GmbH
237	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D312	M.I.T. Media Info Transfer GmbH
238	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D313	VOXBONE SA
239	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D314	01051 Telecom GmbH
240	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D315	01081 Telecom GmbH
241	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D316	01081 Telecom GmbH
242	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D317	Maestro Telecom GmbH
243	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D318	Maestro Telecom GmbH
244	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D319	01071 Telecom GmbH
245	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D320	01071 Telecom GmbH
246	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D321	htp GmbH
247	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D322	CARRIER-SERVICES.de GmbH
248	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D323	ecotel communication ag
249	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D326	Naka AG
250	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D327	Talk.to FZC
251	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D328	WOBCOM GmbH Wolfsburg für Telekommunikation und Dienstleistungen
252	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D329	TelcoVillage GmbH
253	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D330	willy tel GmbH
254	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D331	willy tel GmbH
255	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D333	Mobiquithings, Hagelsbergstraße 10
256	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D334	süc // dacor GmbH
257	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D335	Twilio Ireland Ltd.
258	2022-02-18 08:44:19+01	2022-02-18 08:44:19+01	\N	D336	ITS AG
259	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D337	Tismi BV
260	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D338	Sunak, Oktay e.K., ST-Technologie
261	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D339	CarDeluxe GmbH & Co.KG
262	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D340	MCN Telekommunikacios Szolgaltato Kortlatolt Felelösegü Tarsasag
263	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D341	TeleForte Telekommunikations AG
264	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D342	CALLAX Holding GmbH
265	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D343	Callax Telecom Services GmbH
266	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D344	Callax Telecom Services GmbH
267	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D345	Callax Telecom Services GmbH
268	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D346	Callax Telecom Services GmbH
269	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D347	Callax Telecom Services GmbH
270	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D348	Callax Telecom Services GmbH
271	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D349	Callax Telecom Services GmbH
272	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D350	Callax Telecom Services GmbH
273	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D351	Callax Telecom Services GmbH
274	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D352	Callax Telecom Services GmbH
275	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D353	BITel Gesellschaft für Telekommunikation mbH
276	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D354	Linea Service GmbH
277	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D355	Thüringer Netkom GmbH
278	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D356	Mango Office GmbH
279	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D358	Matelso GmbH
280	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D359	PLANinterNET VoIP-GmbH
281	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D360	MPA NET Gesellschaft für Telekommunikation mbH
282	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D363	PLANinterNET VoIP-GmbH
283	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D364	Nexiu GmbH
284	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D365	HeLi NET Telekommunikation GmbH & Co. KG
285	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D366	Viatel Deutschland GmbH
286	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D367	schnell-im-netz.de GmbH & Co. KG
287	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D368	PFALZKOM GmbH
288	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D369	TELE AG
289	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D370	multiConnect GmbH
290	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D371	SOCO Network Solutions GmbH
291	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D373	Axxess Solutions GmbH
292	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D374	Axxess Solutions GmbH
293	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D375	Net-Spacy IT-Services GmbH
294	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D376	Viatel Global Services Deutschland GmbH
295	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D377	Rottleb, Kai -KNT Internet-
296	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D378	Dolphin IT-Systeme e.K.
297	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D379	sdt.net AG
298	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D380	Rack, Michael Reiner -RSM Freilassing-
299	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D381	COMBILE GmbH Mobile System Builders
300	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D382	Westend Management GmbH
301	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D383	01072 GmbH
302	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D384	01072 GmbH
303	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D385	01073 GmbH
304	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D386	eSTART Telecom GmbH
305	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D387	eSTART Telecom GmbH
306	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D388	01049 GmbH
307	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D389	MyShop Services GmbH
308	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D390	01085 GmbH
309	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D391	01059 GmbH
310	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D392	01066 GmbH
311	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D393	MyShop Services GmbH
312	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D394	Tulp Solutions B.V.
313	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D395	meetyoo conferencing GmbH
314	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D396	VoiceON Telecom Services GmbH
315	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D397	Tele Columbus AG
316	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D399	SCHÖNENBERG-COMPUTER GmbH
317	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D400	GöTel GmbH
318	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D401	D & T Internet GmbH
319	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D402	RelAix Networks GmbH
320	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D403	Belgacom International Carrier Services SA
321	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D404	tenios GmbH
322	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D405	Via-Vox GmbH
323	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D406	Portunity GmbH
324	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D407	dtms GmbH
325	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D408	Data Networks Communication and Consulting GmbH
326	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D409	Drillisch Online AG
327	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D410	mobilcom-debitel GmbH
328	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D411	QuestNet GmbH
329	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D412	telkodata GmbH
330	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D413	Nord-IT Bremen UG (haftungsbeschränkt)
331	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D414	walter cloud services GmbH
332	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D415	QTELO GmbH
333	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D416	QTELO GmbH
334	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D417	1N Telecom GmbH
335	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D418	Stadtwerke Bochum GmbH
336	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D419	MiSaxNet GmbH
337	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D420	Marco Bungalski GmbH
338	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D421	Local Exchange Global Operation Services (LEGOS)
339	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D422	NX4 Networks GmbH
340	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D423	Deutsche Glasfaser Wholesale GmbH
341	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D424	TRILUCOM Holding S.A.
342	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D425	Stahlberg, Alexander
343	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D426	nfon AG
344	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D427	GGEW net GmbH
345	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D428	NU Informationssysteme GmbH
346	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D429	WEMAG AG
347	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D430	autphone GmbH
348	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D431	AlphaCron Datensysteme, Inh. Marc Pauls
349	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D432	Intersaar GmbH
350	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D433	Brandl Services GmbH
351	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D434	42com Telecommunication GmbH
352	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D435	Stadtwerke Bühl GmbH
353	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D436	DIABOLOCOM GmbH, c/o Pramex International GmbH
354	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D437	Weissblau-breitband UG (haftungsbeschränkt)
355	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D438	NL Bandwidth B.V.
356	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D439	Macrogate IP Systems GmbH
357	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D440	New Fiber Networks GmbH
358	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D441	Stiegeler Internet Service GmbH
359	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D442	HDone GmbH
360	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D443	init.voice GmbH
361	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D444	VoiceON Telecom Services GmbH
362	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D445	TeleData GmbH
363	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D446	KEVAG Telekom GmbH
364	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D447	PHILUNET GmbH
365	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D449	42one GmbH
366	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D450	Tecsee GmbH
367	2022-02-18 08:44:20+01	2022-02-18 08:44:20+01	\N	D999	Bundesnetzagentur
\.


--
-- Data for Name: ccc; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ccc (id, created_at, updated_at, deleted_at, template_filename, headline1, headline2, language, logo, speedtest_url) FROM stdin;
1	\N	\N	\N	\N	\N	\N	en	\N	\N
\.


--
-- Data for Name: comment; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.comment (id, created_at, updated_at, deleted_at, user_id, ticket_id, comment) FROM stdin;
\.


--
-- Data for Name: company; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.company (id, created_at, updated_at, deleted_at, name, street, zip, city, phone, fax, web, mail, registration_court_1, registration_court_2, registration_court_3, management, directorate, tax_id_nr, tax_nr, transfer_reason, logo, conn_info_template_fn) FROM stdin;
1	2022-02-18 08:45:36+01	2022-02-18 08:45:36+01	\N	NMS Prime	Dörfelstraße 7	09496	Marienberg	\N	\N	www.nmsprime.com	support@nmsprime.com	\N	\N	\N	\N	\N	\N	\N	\N	nmsprime.pdf	default_coninfo.tex
\.


--
-- Data for Name: configfile; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.configfile (id, created_at, updated_at, deleted_at, name, text, device, public, parent_id, firmware, is_dummy, monitoring, dashboard) FROM stdin;
1	2022-02-18 11:09:38+01	2022-02-18 11:09:38+01	\N	Base	/* Mandatory */\r\nUsServiceFlow\r\n{\r\n    UsServiceFlowRef 101;\r\n    QosParamSetType 7;\r\n    MaxRateSustained {qos.us_rate_max_help.0};\r\n    MaxTrafficBurst {{qos.us_rate_max_help.0},*,0.1875};\r\n}\r\nDsServiceFlow\r\n{\r\n    DsServiceFlowRef 1;\r\n    QosParamSetType 7;\r\n    MaxRateSustained {qos.ds_rate_max_help.0};\r\n}\r\n\r\n/* Optional */\r\nModemCapabilities\r\n{\r\n    ConcatenationSupport 1;\r\n    IGMPSupport 1;\r\n}\r\nGlobalPrivacyEnable 0; /* enabled - now BaselinePrivacy is mandatory */\r\nBaselinePrivacy\r\n{\r\n    AuthTimeout 10;\r\n    ReAuthTimeout 10;\r\n    AuthGraceTime 600;\r\n    OperTimeout 10;\r\n    ReKeyTimeout 10;\r\n    TEKGraceTime 1800;\r\n    AuthRejectTimeout 60;\r\n    SAMapWaitTimeout 1;\r\n    SAMapMaxRetries 4;\r\n}\r\n\r\n/* DOCS-CABLE-DEVICE-MIB rfc4639 */\r\n/* SNMP Access to CM */\r\nSnmpMibObject sysLocation.0 String "{test}"; /* strings always inside apostrophes! */\r\nSnmpMibObject docsDevNmAccessIp.10 IPAddress 172.20.0.1 ;\r\nSnmpMibObject docsDevNmAccessIp.20 IPAddress 172.20.0.1 ;\r\nSnmpMibObject docsDevNmAccessIpMask.10 IPAddress 255.255.255.255 ;\r\nSnmpMibObject docsDevNmAccessIpMask.20 IPAddress 255.255.255.255 ;\r\nSnmpMibObject docsDevNmAccessCommunity.10 String "{provbase.ro_community.0}" ;\r\nSnmpMibObject docsDevNmAccessCommunity.20 String "{provbase.rw_community.0}" ;\r\nSnmpMibObject docsDevNmAccessControl.10 Integer 2; /* read */\r\nSnmpMibObject docsDevNmAccessControl.20 Integer 3; /* readWrite */\r\nSnmpMibObject docsDevNmAccessInterfaces.10 String "@" ;\r\nSnmpMibObject docsDevNmAccessInterfaces.20 String "@" ;\r\nSnmpMibObject docsDevNmAccessStatus.10 Integer 4; /* createAndGo */\r\nSnmpMibObject docsDevNmAccessStatus.20 Integer 4; /* createAndGo */\r\n\r\n/* Firewall */\r\n/* Layer2: filter possibly harmful Ethernet traffic */\r\nSnmpMibObject docsDevFilterLLCUnmatchedAction.0 Integer 1; /* discard if not matching */\r\nSnmpMibObject docsDevFilterLLCStatus.1 Integer 4; /* createAndGo */\r\nSnmpMibObject docsDevFilterLLCStatus.2 Integer 4; /* createAndGo */\r\nSnmpMibObject docsDevFilterLLCIfIndex.1 Integer 0 ; /* all Interfaces */\r\nSnmpMibObject docsDevFilterLLCIfIndex.2 Integer 0 ; /* all Interfaces */\r\nSnmpMibObject docsDevFilterLLCProtocolType.1 Integer 1; /* ethertype (default) */\r\nSnmpMibObject docsDevFilterLLCProtocolType.2 Integer 1; /* ethertype (default) */\r\n\r\n/* Layer 3 */\r\nSnmpMibObject docsDevFilterLLCProtocol.1 Integer 2048 ; /* ? depends on docsDevFilterLLCProtocolType */\r\nSnmpMibObject docsDevFilterLLCProtocol.2 Integer 2054 ; /* IPv4 !? */\r\nSnmpMibObject docsDevFilterIpDefault.0 Integer 2; /* accept for further processing when not matching an prior specified IP filter*/\r\nSnmpMibObject docsDevFilterIpStatus.3 Integer 4; /* createAndGo a filter row */\r\nSnmpMibObject docsDevFilterIpControl.3 Integer 1; /* discard */\r\nSnmpMibObject docsDevFilterIpIfIndex.3 Integer 1 ; /* all customer facing interfaces */\r\nSnmpMibObject docsDevFilterIpDirection.3 Integer 1; /* inbound traffic */\r\nSnmpMibObject docsDevFilterIpBroadcast.3 Integer 2; /* false - applies to all traffic */\r\nSnmpMibObject docsDevFilterIpSaddr.3 IPAddress 0.0.0.0 ; /* all ip addresses - masked against docsDevFilterIpSmask */\r\nSnmpMibObject docsDevFilterIpSmask.3 IPAddress 0.0.0.0 ; /* all ip addresses */\r\nSnmpMibObject docsDevFilterIpDaddr.3 IPAddress 0.0.0.0 ; /* all ip addresses */\r\nSnmpMibObject docsDevFilterIpDmask.3 IPAddress 0.0.0.0 ; /* all ip addresses */\r\nSnmpMibObject docsDevFilterIpProtocol.3 Integer 17 ; /* udp */\r\nSnmpMibObject docsDevFilterIpSourcePortLow.3 Integer 67 ; /* udp */\r\nSnmpMibObject docsDevFilterIpSourcePortHigh.3 Integer 67 ; /* udp */\r\nSnmpMibObject docsDevFilterIpDestPortLow.3 Integer 0 ;\r\nSnmpMibObject docsDevFilterIpDestPortHigh.3 Integer 65535 ;	cm	yes	\N	\N	f	\N	/grafana/d/3-42DM6Gk/cablemodem
2	\N	\N	\N	Base-MTA	\N	mta	yes	\N	\N	t	\N	/grafana/d/3-42DM6Gk/cablemodem
\.


--
-- Data for Name: contact; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.contact (id, created_at, updated_at, deleted_at, firstname1, lastname1, firstname2, lastname2, company, tel, tel_private, email1, email2, street, house_nr, city, zip, district, administration, invoice_text1, invoice_text2, invoice_text3) FROM stdin;
\.


--
-- Data for Name: contract; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.contract (id, created_at, updated_at, deleted_at, number, number2, number3, number4, customer_external_id, company, department, salutation, academic_degree, firstname, lastname, street, house_number, zip, city, district, country_id, country_code, phone, fax, email, birthday, contract_start, contract_end, internet_access, purchase_tariff, next_purchase_tariff, qos_id, next_qos_id, voip_id, next_voip_id, sepa_iban, sepa_bic, sepa_holder, sepa_institute, create_invoice, login, password, net, cluster, description, costcenter_id, salesman_id, has_telephony, apartment_nr, additional, ground_for_dismissal, group_contract, contact, value_date, apartment_id, contact_id) FROM stdin;
500000	2022-02-18 11:08:19+01	2022-02-19 00:03:04+01	\N	10000	\N	\N	\N	\N	\N	\N	Herr	\N	Max	MuMann	MuStreet	125d	01234	MuCity	\N	\N	\N	\N	\N	\N	1990-02-06	2022-02-18	\N	t	\N	\N	\N	\N	\N	\N			\N	\N	t	\N	\N	\N	\N	\N	1	\N	t	\N	\N	\N	f	0	\N	\N	\N
\.


--
-- Data for Name: costcenter; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.costcenter (id, created_at, updated_at, deleted_at, name, number, sepaaccount_id, billing_month, description) FROM stdin;
1	2022-02-18 08:45:36+01	2022-02-18 08:45:36+01	\N	NMS Prime	\N	1	6	\N
\.


--
-- Data for Name: debt; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.debt (id, created_at, updated_at, deleted_at, contract_id, sepamandate_id, invoice_id, date, amount, bank_fee, total_fee, description, number, voucher_nr, due_date, cleared, indicator, dunning_date, parent_id, missing_amount, extra_fee) FROM stdin;
\.


--
-- Data for Name: domain; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.domain (id, created_at, updated_at, deleted_at, name, alias, type) FROM stdin;
\.


--
-- Data for Name: ekpcode; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ekpcode (id, created_at, updated_at, deleted_at, ekp_code, company) FROM stdin;
1	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/019	TELTA Citynetz GmbH
2	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/031	Orange Business Germany GmbH
3	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/035	HFO Home GmbH
4	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/040	TelemaxX Telekommunikation GmbH
5	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/073	Andreas Muth, Antennenbaubetrieb
6	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/074	intersaar GmbH
7	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/086	Avaya GmbH & Co. KG
8	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/097	WEMACOM Telekommunikation GmbH
9	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/101	wilhelm.tel GmbH
10	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/121	Fernseh GmbH Niederdorf
11	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/133	Unitymedia Hessen GmbH & Co. KG
12	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/135	Kabelfernsehen Muenchen Servicenter GmbH & Co. KG
13	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/140	Ost Tel Com Osttelecommunications-GmbH
14	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/152	GöTel GmbH
15	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/159	COM-IN Telekommunikations GmbH
16	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/172	Telemark Telekommunikationsgesellschaft Mark mbH
17	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/188	ACO Computerservice GmbH
18	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/196	I-NetPartner GmbH Online Services
19	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	00/212	Bisping & Bisping GmbH & Co. KG
20	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/007	LKG Lausitzer Kabelbetriebsgesellschaft mbH
21	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/009	KKG Kabelkommunikation Guestrow GmbH
22	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/020	Telepark Passau GmbH
23	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/036	FAKS, Frankfurter Antennen- und Kommunikationsservice GmbH
24	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/066	BT (Germany) GmbH & Co. oHG
25	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/070	DNS:NET Internet Service GmbH
26	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/079	HeLi NET Telekommunikation GmbH & Co. KG
27	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/088	SOCO Network Solutions GmbH
28	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/099	HL komm Telekommunikations GmbH
29	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/102	Telekabel Bremen oHG
30	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/104	true global communications GmbH
31	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/111	City TV-Kabelservice GmbH
32	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/124	Bernd Garthoff, tv-video-hifi-antennentechnik
33	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/150	Titan Networks GmbH
34	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/161	SWU Telenet GmbH
35	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	01/174	Kabel + Satellit Bergen Kommunikationstechnik GmbH
36	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/004	x.CableCom GmbH
37	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/035	Satelliten- und Kabelfernsehanlagen / Industrievertretung GmbH
38	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/040	km3 teledienst GmbH
39	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/047	NeckarCom – Eine Marke der NetCom BW
40	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/051	regionetz.net
41	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/064	Antronic Kommunikations Service GmbH
42	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/069	willy.tel GmbH
43	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/078	Thüringer Netkom GmbH
44	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/079	teliko GmbH
45	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/107	MEDIACOM Kabelservice GmbH
46	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/110	Funk und Technik GmbH Forst
47	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/130	SWT trilan GmbH
48	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/149	Kabel-TV Aue e.V.
49	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/152	KKS Kabel-Kommunikationsservice GmbH
50	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/158	Hughes Network Systems GmbH
51	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	02/181	MK Netzdienste GmbH & Co. KG
52	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/016	RegioNet Schweinfurt GmbH
53	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/018	Tele Columbus Sachsen-Thüringen GmbH
54	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/026	telesparfon GmbH
55	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/033	Ventelo GmbH
56	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/034	prima call GmbH
57	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/041	Girrbach - HiFi , Video, TV, Service GmbH
58	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/049	GGEW net GmbH
59	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/063	Elementmedia GmbH
60	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/076	süc//dacor GmbH
61	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/089	skyDSL Deutschland GmbH
62	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/111	1click2 Internet Services GmbH
63	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	03/130	Interoute Germany GmbH
64	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/002	42com Telecommunication GmbH
65	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/047	ADDIX Internet Services GmbH
66	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/063	net services GmbH & Co. KG
67	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/079	KurpfalzTel Gesellschaft für Telekommunikation mbH
68	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/095	Stadtwerke Flensburg GmbH
69	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/105	NEFtv GmbH
70	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/129	S-IT Informationstechnologie Betreiber GmbH & Co. KG im Nordschwarzwald
71	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/169	ccn corporate communication networks GmbH
72	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/200	Steffen Perlwitz, Vertrieb von Hard- und Software
73	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/201	Unifone GmbH
74	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/206	Stadtwerke Annaberg-Buchholz GmbH
75	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/231	ANT Granowski GmbH
76	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/244	Portunity GmbH
77	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/247	PLANinterNET VoIP-GmbH
78	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/256	rockenstein AG
79	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/268	Funknetz HG
80	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/269	Stadtwerke Marburg GmbH
81	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/276	Horst Wierling, Antennenbau/Fachhandel
82	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/284	Helmut Dräger, ANTEC
83	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/323	ATS Vertriebs- und Handelsgesellschaft UG & Co.KG
84	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/331	Bernd Asser, Antennentechnik
85	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	04/339	Antennenservice Thomas
86	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/029	Großgemeinschaftsantennenanlage Oberes Sprottental
87	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/044	I. Schneider Gemeinschafts-Antennenanlagen & Kommunikations-Systeme e.K. Inhaber Jens Schneider
88	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/052	Interessengemeinschaft \\"Gemeinschaftsantenne\\" e.V.
89	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/067	Kabelcom Rheinhessen GmbH
90	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/074	ACS Hartmann
91	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/087	Mietho & Bär GbR
92	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/088	narelic Nachrichtenelektronik GmbH
93	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/113	SELLHUSEN GmbH
94	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/119	TEGRO Kabelbau GmbH
95	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/128	TKN Telekabel - Nord GmbH
96	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/131	TV-Netzgesellschaft mbH Guben
97	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/189	L & N Kabel- und Kommunikationssysteme GmbH
98	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/191	Tele Columbus Netze Berlin GmbH
99	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/193	Mietho & Bär Kabelkom GmbH
100	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/207	Andreas Stolle
101	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/212	HFO Telecom GmbH
102	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/217	Global Village GmbH
103	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/244	outbox AG
104	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/250	TNG Stadtnetz GmbH
105	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/253	reputatio AG
106	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/262	TBits.net GmbH
107	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/275	Tele Columbus Berlin-Brandenburg GmbH & Co.KG
108	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/279	reventix GmbH
109	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/295	mr. next id GmbH
110	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/314	terralink networks GmbH
111	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/326	omnidat Gesellschaft für individuelle Netzwerk- und Kommunikationslösungen mbH
112	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/349	m3connect GmbH
113	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/358	RS Gesellschaft fuer Informationstechnik mbH & Co. KG
114	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/367	AJE Consulting GmbH & Co. KG
115	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/383	Steffen Hartmut Allstädt, Doergi.Net
116	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/401	VPN Deutschland Ltd. & Co. KG
117	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/411	MEK-cable GbR
118	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/412	D/W Elektrofunk GmbH
119	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/414	NU Informationssysteme GmbH
120	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	05/416	Sachs Kommunikationssysteme GmbH
121	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/013	Günther Schuster, S - SYSTEMS
122	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/015	PTC Telecom GmbH
123	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/019	Verizon Deutschland GmbH
124	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/026	DW-TelefonDienste
125	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/078	Ackermann & Pfetzing GbR
126	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/114	Unitymedia NRW GmbH
127	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/118	congstar GmbH
128	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/127	Gemeinschaftsantenne Schmalkalden e.V.
129	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/137	InSysCo Datensysteme GmbH
130	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/145	overturn technologies GmbH
131	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/153	optimaconnect GmbH
132	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/157	purtel.com GmbH
133	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/164	Inter.net Germany GmbH
134	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/165	FAG Fernseh-Antennen-Gemeinschaft Bad Steben e.V.
135	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/181	Stadtwerke Lübeck GmbH
136	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/197	010090 GmbH
137	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/213	CNT AG
138	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/218	Telekommunikation Stefan Kienhöfer (Airnode)
139	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/224	Elektrizitäts-Genossenschaft Tacherting-Feichten eG
140	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/281	tenios GmbH
141	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/289	dus.net GmbH
142	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/296	NYNEX satellite OHG
143	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/306	DEK Telecom GmbH
144	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/311	AlphaCron Datensysteme
145	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/319	MDDSL Mitteldeutsche Gesellschaft für Kommunikation mbH
146	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/325	Norman Dietzen (Wireless-DSL - Eifel-DSL)
147	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/336	Voxbone SA
148	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/341	voipGATE S.A.
149	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/363	GWS Stadtwerke Hameln GmbH
150	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/364	HochrheinNET GmbH
151	2022-02-18 08:44:21+01	2022-02-18 08:44:21+01	\N	06/367	Susann Höhne Mobil24-direct
152	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	06/372	Netcommunity GmbH
153	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/007	vocatel business gmbh
154	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/027	AS Antennenanlagen-Service GmbH
155	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/033	AVACOMM Systems GmbH
156	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/034	VICTORVOX
157	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/043	Kadsoft Computer GmbH Freital
158	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/046	Computertechnik Trenkle e.K.
159	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/079	G-FIT Gesellschaft für innovative Telekommunikationsdienste mbH & Co. KG
160	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/097	Broadsoft Germany GmbH
161	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/138	Tele Columbus Multimedia GmbH
162	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/172	NFON AG
163	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/183	Feedback Mobile Services GmbH
164	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/198	G. Ziegelmeier e.K
165	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/214	Server-Ware GmbH
166	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/228	DALASON GmbH
167	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/232	Stadtwerke Wedel GmbH
168	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/262	DeTeWe Communications GmbH
169	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/281	FPS Informationssysteme GmbH
170	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/313	toplink GmbH
171	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/320	TeamFON GmbH
172	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	07/348	WiSoTEL
173	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/010	inexio Informationstechnologie und Telekommunikation GmbH
174	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/013	Deutsche Telefon Standard AG
175	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/014	AEP Plückhahn Netze GmbH
176	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/016	3U TELECOM GmbH
177	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/042	Brandl Services GmbH
178	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/049	BBS-Mitteldeutsche Breitbandsystem UG
179	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/086	AirIT Services GmbH
180	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/115	WTC Wohnen & TeleCommunication GmbH & Co. KG
181	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/132	equada GmbH
182	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/140	Stadtwerke Einbeck GmbH
183	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/144	RelAix Networks GmbH
184	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/156	KNT Internet
185	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/158	Netzquadrat
186	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/170	Agiliscom AG
187	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/185	Stadtwerke Trostberg Telekommunikation GmbH
188	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/192	Photonium NetSolutions GmbH
189	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/202	Arche Netvision GmbH
190	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/213	Tele Columbus Hessen GmbH
191	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/214	BBcom Berlin-Brandenburgische Communikationsgesellschaft mbH
192	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/220	R&M SimplyTechnnologies GmbH
193	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/227	Stadtwerke Schwedt GmbH
194	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/231	Regionalantenne Cunnersdorf e.V.
195	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/253	E-Plus Mobilfunk GmbH
196	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/262	TeleForte AG
197	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/289	BORnet GmbH
198	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/313	Schönenberg-Computer GmbH
199	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/316	Tal.de Klaus Internet Service GmbH
200	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/318	RST Datentechnik GmbH
201	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/320	easybell GmbH
202	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/321	ip-fabric GmbH
203	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/322	Stiegeler Internet Service GmbH
204	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/324	Freikom GmbH
205	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/331	Damovo Deutschland GmbH & Co. KG
206	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/338	JOBST DSL e.K.
207	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/345	ICN GmbH + Co. KG
208	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	08/346	Antennen Einert e.K.
209	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/003	ANTEC Servicepool GmbH
210	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/038	Tele-System Harz GmbH
211	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/042	Trusted Network GmbH
212	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/057	mieX Deutschland GmbH
213	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/059	meetyoo conferencing GmbH
214	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/080	RSM Freilassing, Michael Reiner Rack
215	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/091	COS-on-Air OHG
216	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/102	WDSL-Oberlausitz
217	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/106	Teleport GmbH
218	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/118	sewikom GmbH
219	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/123	RMS-systems Datenverarbeitungs GmbH
220	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/125	Drahtlos DSL GmbH Mittelsachsen
221	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/132	net-and-phone GmbH
222	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/148	LWLcom GmbH
223	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/172	W M B Kabelservice GmbH
224	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/174	Voiceworks GmbH
225	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/175	Felkatec Software GmbH & Co. KG
226	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/182	purpur Networks GmbH
227	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/193	tkrz Stadtwerke GmbH
228	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/216	amplus AG
229	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/222	Ilm-Provider UG (haftungsbeschränkt)
230	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/258	Marienberg GmbH
231	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/261	TECOSI ATF GmbH
232	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/268	Innofactory GmbH
233	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/276	Breitbandservice Gantert GmbH & Co.KG
234	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/289	RK² Systemintegration GmbH
235	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/296	Thüga SmartService GmbH
236	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/311	DCC GmbH
237	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/318	Unser Ortsnetz GmbH
238	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/320	Moderne Kommunikationstechnologie Hohentengen GmbH
239	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/324	e.wa riss Netze GmbH
240	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	09/326	Mass Response Deutschland GmbH
241	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/006	schnell-im-netz.de GmbH & Co.KG
242	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/013	ENNI Energie & Umwelt Niederrhein GmbH
243	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/029	D&T Internet GmbH
244	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/054	Schleusinger Media Service GmbH
245	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/060	HessenKom GmbH & Co KG
246	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/061	Project66 IT-Service & Design Niels Rosenhahn
247	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/081	KSP-Kabelservice Prenzlau GmbH
248	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/094	Filiago GmbH & Co. KG
249	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/120	newone GmbH
250	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/131	TKN Deutschland GmbH
251	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/133	Hofmeir Media GmbH
252	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/141	PFALZconnect GmbH
253	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/161	DSI GmbH Daten Service Informationssysteme
254	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/181	vio:networks GmbH
255	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/190	Wittenberg-net GmbH
256	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/196	Dhom & Johannsen GbR
257	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/213	inopla GmbH
258	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/217	GP-Elektronik e.K.
259	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/240	COLT Technology Services GmbH
260	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/251	A+U Baumann oHG
261	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/253	Wichmann Internet Services GmbH
262	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/257	TELE AG
263	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/283	HNS GmbH
264	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/305	Stadtwerke Konstanz GmbH
265	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/314	Stern Kom GmbH
266	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/323	Stadtwerke Itzehoe GmbH
267	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/329	epcan GmbH
268	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/330	Gemeinschaftsantenne Struth-Helmersdorf und Floh-Seligenthal OT Schnellbach e.V.
269	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/334	smart-DSL GmbH
270	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/339	encoLine GmbH
271	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/350	AnWaDi.de
272	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/351	Martin Prager, NbIServ
273	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/360	Marco Bungalski GmbH
274	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	10/399	OpenNumbers GmbH
275	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/004	nexiu GmbH
276	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/013	matelso GmbH
277	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/014	D-Connect GmbH
278	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/043	Multiconnect GmbH
279	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/048	PHILIS IT
280	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/057	DATA CMR GmbH & Co. KG
281	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/063	MUENET GmbH
282	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/086	DSLmobil GmbH
283	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/093	tel.quick GmbH & Co. KG
284	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/094	Tele System GmbH Rostock
285	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/095	d+p breitbandconcept UG
286	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/097	IN-telegence GmbH
287	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/113	INCAS GmbH
288	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/124	Vereinigte Stadtwerke Media GmbH
289	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/131	telsakom GmbH
290	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/145	PIRONET NDH Datacenter AG & Co. KG
291	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/146	Globalways AG
292	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/152	Elektrizitätswerk Goldbach-Hösbach GmbH & Co. KG
293	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/153	innogy TelNet GmbH
294	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/172	CEMI Service GmbH
295	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/174	Truphone GmbH
296	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/176	pinnau.com GmbH & Co. KG
297	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/183	Stadtwerke Hammelburg GmbH
298	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/195	Snellstar GmbH
299	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/212	Stadtwerke Barmstedt Xtra GmbH
300	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/213	GWHtel Gmbh & Co.KG
301	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/224	vitroconnect GmbH
302	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/247	Breitband Hoyerswerda GmbH
303	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/268	dng IT GmbH & Co. KG
304	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/298	Martens Deutsche Telekabel GmbH
305	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/305	Sipgate GmbH
306	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/307	Stadtwerke Eckernförde GmbH
307	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/308	Vodafone Kabel Deutschland GmbH
308	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/331	Primacom Berlin GmbH
309	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/358	EMOTEC NETWORKS GmbH
310	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/369	Unitymedia BW GmbH
311	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/390	Internet & Go GmbH
312	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/391	comjoo business solutions GmbH
313	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/392	OpenXS GmbH
314	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/400	QUiX Breitband GmbH
315	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	11/411	Gemeindewerke Hohenwestedt GmbH
316	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/017	1&1 Telecom GmbH
317	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/019	Milde Software Solutions (MSS)
318	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/037	Hofnetz und IT Services GmbH
319	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/038	WEBDISCOUNT GmbH & Co. KG
320	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/043	KNH - TV Limited
321	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/048	KomMITT-Ratingen GmbH
322	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/054	net services GmbH & Co KG
323	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/060	Stadtwerke Engen GmbH
324	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/063	TELEflash GmbH
325	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/067	Lightup Network Solutions GmbH & Co. KG
326	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/068	MVD Multimedia Verbundnetz Dresden GmbH
327	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/073	KABEL-TV-BINZ GmbH & Co.KG
328	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/074	Stadtwerke Ahrensburg GmbH
329	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/086	WIFISAX UG
330	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/094	Sat Internet Services GmbH
331	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/098	KOMNEXX GmbH
332	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/119	Web + Phone GmbH
333	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/125	media construct gmbH
334	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/129	WIFISAX UG
335	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/133	EWR Netz GmbH
336	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/140	C&S Breitband GmbH
337	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/149	Buchholz Digital GmbH
338	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/169	NetTask GmbH
339	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/183	TEFIS-Technique & Financial Service Ltd.& Co.KG Zweigstelle Frankfurt
340	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/184	mWerk GmbH
341	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/195	oscon itk GmbH
342	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/198	VoiceON Telecom Services GmbH
343	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/199	Umbra Networks Gesellschaft für Telekommunikation
344	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/209	Stadtwerke Bochum GmbH
345	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/219	Stadtwerke Geesthacht GmbH
346	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/223	SWW Wunsiedel GmbH
347	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/239	teutel GmbH
348	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/242	amisol GmbH
349	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/264	ITS AG
350	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	12/345	Bundesnetzagentur
351	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/002	siebnich.com - it.competence!
352	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/018	TEFONIX UG
353	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/037	gustav internet GmbH & Co. KG
354	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/049	Glöckle direct GmbH
355	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/051	simple Communication GmbH
356	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/057	primastrom GmbH
357	2022-02-18 08:44:22+01	2022-02-18 08:44:22+01	\N	13/065	hugo internet GmbH & Co. KG
358	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/069	Stadtwerke Finsterwalde GmbH
359	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/084	Dennis Spiegel, Spiegel-Bild
360	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/095	Deutsche Glasfaser Wholesale GmbH
361	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/122	Disquom Funktechnik GmbH
362	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/132	Rhönnet GmbH
363	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/145	echtschnell GmbH
364	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/146	In(n) Energie GmbH
365	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/152	pepcom GmbH
366	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/158	ansit-com GmbH
367	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/186	werknetz internet GmbH & Co. KG
368	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/189	TeGe Deutschland GmbH
369	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/190	wirsNET Kabeldienstleistungen UG (haftungsbeschränkt)
370	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/201	NEW Niederrhein Energie und Wasser GmbH
371	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/206	NTTCable Service KG
372	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/211	Luxembourg Online SA
373	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/216	Deutsche Gesellschaft für Kommunikation und Service mbH
374	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/222	media.tel Informationsdienstleistungs GmbH
375	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	13/225	Fl!nk GmbH
376	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/017	Speedloc Datacenter
377	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/035	Arcor
378	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/036	Orbitcom GmbH
379	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/074	Antennengemeinschaft ERZNET AG
380	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/086	Jonny Rönnefahrt Chono Gruppe Deutschland
381	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/104	Osburg Systems GmbH
382	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/106	Olfenkom GmbH
383	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/118	SEDHO GmbH
384	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/119	voxenergie GmbH
385	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/129	telenetwork AG
386	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/133	Telekabel Riesa GmbH
387	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/134	dd Handelsgesellschaft mbH
388	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/140	YplaY Germany GmbH
389	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/157	dtms GmbH
390	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/160	OR Network GmbH
391	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/162	GVG Glasfaser GmbH
392	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/167	Peoplefone GmbH
393	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/169	Deutsche Glasfaser Business GmbH
394	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/175	MCN Telekommunikacios Szolgaltato Korlatolt Felelösegü Tarsasag
395	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/178	Servario Networks GmbH
396	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/179	NetCom Access Gesellschaft für Telekommunikation
397	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/184	G B C Kommunikationssysteme GmbH
398	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/185	Eckhard Möbius, Möbius Kommunikation Elektronik
399	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/189	DS Computer Service
400	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/191	Breitbandversorgung Rhein-Neckar GmbH
401	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/206	MIXvoip SA
402	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/208	Mango Office GmbH
403	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/210	Friedrich Kecht und Gerhard Guggenbichler GbR, RuhNet
404	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/216	EnoCom GmbH
405	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/221	Olaf Stenzel, PyloNet
406	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/252	foncloud GmbH & Co. KG
407	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/267	Carsten Dickenscheid IT-Service
408	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/273	Stadtwerke SchwerinGmbH (SWS)
409	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	14/278	Rekers Digitaltechnik GmbH & Co. KG
410	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/004	MEGA Monheimer Elektrizitäts- und Gasversorgung GmbH
411	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/006	fonial GmbH
412	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/009	Stadtwerke Dorfen GmbH
413	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/030	E.Net GmbH & Co KG
414	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/043	Stadtwerke Eutin GmbH
415	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/044	Stadtwerke Merseburg GmbH
416	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/045	Flow7 GmbH
417	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/048	T-Mobile HotSpot GmbH
418	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/053	telegra GmbH
419	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/057	Deutsche Glasfaser Home GmbH
420	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/070	AnschlussWerk GmbH
421	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/071	iks informations- und kommunikations Systeme GmbH
422	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/072	GETEC media GmbH
423	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/075	Schleswiger Stadtwerke GmbH
424	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/078	Radio Bachmann GmbH
425	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/083	CompuNet Systems GmbH
426	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/084	Dolphin IT-Systeme e.K.
427	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/086	ZEAG Energie AG
428	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/091	Unify Deutschland GmbH & Co. KG
429	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/102	weissblau-breitband UG (haftungsbeschänkt)
430	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/104	Elektro - Garten
431	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/105	Stadtwerke Schneverdingen-Neuenkirchen GmbH
432	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/110	TRILUCom Holding S.A.
433	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/113	Stephan Konheiser EDV-Studio
434	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/119	Getinternet GmbH
435	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/124	itelco-consult GmbH
436	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/126	FNOH-DSL Südheide GmbH
437	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/143	Axxess Solutions GmbH
438	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/145	Cloud Communication Service GmbH
439	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/147	My-Cloudline Deutschland GmbH
440	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/148	getSolve IT GmbH
441	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/158	Schiffel IT-Service GmbH
442	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/165	Breitbandversorgung Münsterland GmbH
443	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/179	StarCom-Bauer GmbH
444	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/190	NX4 Networks GmbH
445	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/196	Stadtwerke Kaltenkirchen GmbH
446	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/199	Freikom GmbH
447	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/203	zollernalb-data GmbH
448	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/209	gnTel GmbH
449	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/214	Vater Service4Net GmbH
450	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/233	Stadtwerke Neustrelitz GmbH
451	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	15/239	SOLIDAS Media GmbH
452	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/005	Stadtwerke Annaberg-Buchholz Energie AG
453	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/008	Jens Dreiheller / Lodaya Technologies {Einzelunternehmen ohne HR-Eintrag)
454	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/013	WINatNet Provider und Netze GmbH
455	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/019	Stadtwerke Bad Reichenhall KU
456	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/024	telkodata GmbH
457	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/037	SUPERTEL A/S
458	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/054	Business Phone Net GmbH & Co. KG
459	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/087	NGN Telecom GmbH
460	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/096	Stadtwerke - Strom Plauen GmbH & Co. KG
461	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/110	ProCRM IT-Systems GmbH
462	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/114	Glasfaser Hengersberg GmbH
463	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/117	COMULUS AG
464	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/128	Gemeindewerke Nümbrecht GmbH
465	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/140	MoellerEDV - Klaus A. Möller
466	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/145	GateStream
467	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/152	eins energie in sachsen GmbH & Co. KG
468	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/157	Georg Kröber Webservice
469	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/172	countrycode 49 UG
470	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/189	SVO Vertrieb GmbH
471	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/192	Ziegelmeier GmbH & Co. Antennenservice KG
472	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/219	ngena GmbH
473	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/230	Tele Columbus AG
474	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/231	NetCom Access Gesellschaft für Telekommunikation
475	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/233	KAEMI GmbH
476	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/237	euromicron Deutschland GmbH
477	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/244	Muth Citynetz Halle GmbH
478	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/246	Breitbandversorgung Ostwestfalen GmbH
479	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/256	Via-Vox GmbH
480	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/270	Stadtwerke Teterow GmbH
481	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/271	QuaeroSys UG (haftungsbeschränkt)
482	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/279	Stadtwerke Erkrath GmbH
483	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	16/287	DNCC GmbH
484	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/002	Stadtwerke Forchheim GmbH
485	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/004	Unify Software and Solutions GmbH & Co. KG
486	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/005	Unify Communications and Collaboration GmbH & Co. KG
487	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/047	WEMAG AG
488	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/058	oneCentral B.V.
489	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/060	CNS Network GmbH
490	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/073	kamikom GmbH
491	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/076	MiSaxNet GmbH
492	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/094	Stadtwerke Velbert GmbH
493	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/106	Stadtwerke Hilden GmbH
494	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/113	Gesellschaft für Digitale Werte mbH
495	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/115	Breitbandversorgung Deutschland GmbH
496	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/118	DMRZ Deutsches Mittelstandsrechenzentrum Betreibergesellschaft mbH
497	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/119	Rapidata GmbH
498	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/126	Landwerke M-V Breitband GmbH
499	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/130	inovisio communications GmbH
500	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/135	Stadtwerke Nortorf Breitband GmbH
501	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/142	ingenia digitale Netze GmbH
502	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/145	Surf-Paradies Internet Service GmbH
503	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/161	ETN EmslandTel.Net GmbH & Co. KG
504	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/164	Q-MEX Networks
505	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/168	Nord-IT Bremen Beteiligungs-UG(haftungsbeschränkt)&CO.KG
506	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/170	Barbara Ilchmann-Breu COMPRO Computerprogramme - COMPRO-TEL-NET
507	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/183	Hightel GmbH
508	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/186	globalcarrier telecom GmbH
509	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/191	Office-ED Computer GmbH
510	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/192	PROsave GmbH
511	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/208	Francotyp-Postalia Vertrieb und Service GmbH
512	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/210	Kumeno GmbH
513	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/216	Stadtwerke Oldenburg in Holstein Media GmbH
514	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/222	Tell-Phone Deutschland GmbH
515	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/228	telba AG
516	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/229	Telion Kommunikationsgesellschaft UG (haftungsbeschränkt)
517	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/230	Fa. Schoefisch
518	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/238	SPIEKEROOGkom // Wolf Lindenthal
519	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/244	SQUILD GmbH
520	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/258	Twilio Inc.
521	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/260	D.T.NET Service OHG
522	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	17/261	DCC Duisburg CityCom GmbH
523	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/001	1N Telecom GmbH
524	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/010	MIXvoip GmbH
525	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/023	Emden Digital GmbH
526	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/027	Stadtwerke Pasewalk GmbH
527	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/044	Netsign Networks GmbH
528	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/046	Stadtwerke Meerane GmbH
529	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/054	Anycall GmbH
530	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/064	cratchmere.com gmbh
531	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/068	Stadtwerke Rostock AG
532	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/076	Stadtwerke Ludwigsburg-Kornwestheim GmbH
533	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/096	maxIT Consulting GmbH
534	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/102	Stadtwerke Bühl GmbH
535	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/136	Herbst Datentechnik GmbH
536	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/149	Harmatec GmbH
537	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/156	nvb Nordhorner Versorgungsbetriebe GmbH
538	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/159	autphone GmbH
539	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/164	EuraLink
540	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	18/192	SWaP GmbH Surf, Watch & Phone
541	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	19/054	Inkotec GbR
542	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	19/096	GREENFIBER Internet & Dienste GmbH
543	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	93/007	Telekom Deutschland GmbH
544	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	93/090	KEVAG Telekom GmbH
545	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	94/309	bn:t Blatzheim Networks Telecom GmbH
546	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	95/032	NetCologne Gesellschaft für Telekommunikation mbH
547	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	95/095	Alice, Hansenet, O2, O2 Genion
548	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	95/137	1&1 Versatel Deutschland GmbH
549	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/058	Communication Services Tele2 GmbH
550	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/084	sdt.net AG
551	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/151	Genias Internet (Inh. Stefan Englhardt)
552	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/161	TELCAT MULTICOM GmbH
553	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/194	Tele Columbus Sachsen-Anhalt GmbH
554	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/205	WOBCOM GmbH
555	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/211	NETHINKS GmbH
556	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	96/224	EFN eifel-net Internet-Provider GmbH
557	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/018	Tele Columbus Kabel Service GmbH
558	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/031	Teleco GmbH Cottbus Telekommunikation
559	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/033	Antennen Schulze GmbH
560	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/084	GELSEN-NET Kommunikationsgesellschaft mbH
561	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/119	SWN Stadtwerke Neumünster GmbH
562	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/163	M-net Telekommunikations GmbH
563	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/165	Talkline, Cellway
564	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/170	LüneCom Kommunikationslösungen GmbH
565	2022-02-18 08:44:23+01	2022-02-18 08:44:23+01	\N	97/174	E-Plus Service GmbH & Co. KG
566	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	97/186	electronic anders
567	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	97/238	imos Gesellschaft für Internet-Marketing und Online-Services mbH
568	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	97/244	DOKOM Gesellschaft für Telekommunikation mbH
569	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	97/245	Osnatel GmbH
570	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	97/246	ENTEGA Medianet GmbH
571	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/016	e.discom Telekommunikations GmbH
572	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/023	RFT kabel Brandenburg GmbH
573	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/030	TeleData GmbH
574	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/037	MEGA Communications GmbH
575	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/047	Median Telecom GmbH
576	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/048	R-KOM Regensburger Telekommunikationsgesellschaft mbH & Co. KG
577	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/067	VSE NET GmbH
578	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/068	Broadnet AG
579	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/084	MDCC Magdeburg-City-Com GmbH
580	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/085	Netcom Kassel Gesellschaft für Telekommunikation mbH
581	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/090	Pfalzkom Gesellschaft für Telekommunikation mbH
582	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/094	komro GmbH
583	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/105	Daten- und Telekommunikations GmbH Dessau
584	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/112	envia TEL GmbH
585	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/112X	envia TEL (interne Portierung)
586	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/122	Telenec Telekommunikation Neustadt GmbH
587	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/139	TMR Telekommunikation Mittleres Ruhrgebiet GmbH
588	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/141	operator Telekommunikation International AG
589	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/149	Tele Columbus Cottbus GmbH
590	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	98/150	REKA Regionalservice Kabelfernsehen GmbH
591	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/001	WiTCOM Wiesbadener Informations-und Telekommunikations GmbHGmbH
592	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/002	neu-medianet GmbH
593	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/017	BITel Gesellschaft für Telekommunikation mbH
594	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/021	htp GmbH
595	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/028	Neckarcom
596	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/029	TKS Telepost Kabel-Service Kaiserslautern GmbH & Co. KG
597	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/038	LEW TelNet GmbH
598	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/069	Mandala Internet, EDV-Servis GmbH
599	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/083	planet 33 AG telecommunications internet security
600	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/121	telego! GmbH
601	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/136	ecotel communication ag
602	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/138	Telekommunikation Lindau GmbH
603	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/147	TraveKom Telekommunikationsgesellschaft mbH
604	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/149	Christian Wiesner, VIDEOPRO
605	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/164	Stadtnetz Bamberg Gesellschaft für Telekommunikation mbH
606	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/174	Herzo Media GmbH & Co. KG
607	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	99/180	K-net Telekommunikation GmbH
608	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	n.v.	EKP nicht vorhanden
609	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/001	Gesmo Gesellschaft für Mobilfunkservice
610	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/003	Kabel BW
611	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/004	Tele Columbus AG
612	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/005	Osnatel GmbH
613	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/006	Osnatel GmbH
614	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/008	placetel
615	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/009	Sat Internet Services GmbH
616	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/011	Telefonica Germany GmbH
617	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/012	Outland-net GmbH
618	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/013	manCityNet
619	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/014	primacall GmbH
620	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/015	RIKOM GmbH
621	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/016	Pri-Tel GmbH
622	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/017	BREKO Bundesverband Breitbandkommunikation e.V.
623	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/018	NES Elektro & Service GmbH
624	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/019	NetCom BW GmbH
625	2022-02-18 08:44:24+01	2022-02-18 08:44:24+01	\N	xx/020	Northern Access GmbH
\.


--
-- Data for Name: endpoint; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.endpoint (id, created_at, updated_at, deleted_at, hostname, mac, description, fixed_ip, modem_id, ip, add_reverse, version, prefix) FROM stdin;
\.


--
-- Data for Name: enviacontract; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.enviacontract (id, created_at, updated_at, deleted_at, external_creation_date, external_termination_date, envia_customer_reference, envia_contract_reference, state, start_date, end_date, next_id, prev_id, end_reason, lock_level, method, sla_id, tariff_id, variation_id, contract_id, modem_id) FROM stdin;
\.


--
-- Data for Name: enviaorder; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.enviaorder (id, created_at, updated_at, deleted_at, orderid, method, ordertype_id, ordertype, orderstatus_id, orderstatus, orderdate, ordercomment, related_order_id, customerreference, contractreference, contract_id, modem_id, enviacontract_id, last_user_interaction) FROM stdin;
\.


--
-- Data for Name: enviaorder_phonenumber; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.enviaorder_phonenumber (id, created_at, updated_at, deleted_at, enviaorder_id, phonenumber_id) FROM stdin;
\.


--
-- Data for Name: enviaorderdocument; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.enviaorderdocument (id, created_at, updated_at, deleted_at, document_type, mime_type, filename, enviaorder_id, upload_order_id) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.failed_jobs (id, connection, queue, payload, failed_at, exception) FROM stdin;
\.


--
-- Data for Name: favorite_netelements; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.favorite_netelements (id, user_id, netelement_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: global_config; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.global_config (id, created_at, updated_at, deleted_at, name, street, city, phone, mail, log_level, headline1, headline2, default_country_code, passwordresetinterval, alert1, alert2, alert3, isallnetssidebarenabled) FROM stdin;
1	\N	\N	\N	\N	\N	\N	\N	\N	1	NMS Prime	The next Generation NMS	DE	120	\N	\N	\N	f
\.


--
-- Data for Name: guilog; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.guilog (id, created_at, updated_at, deleted_at, user_id, username, method, model, model_id, text) FROM stdin;
\.


--
-- Data for Name: hfcreq; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.hfcreq (id, created_at, updated_at, deleted_at, ro_community, rw_community, rkm_server, rkm_server_username, rkm_server_password, video_controller, video_controller_username, video_controller_password, video_encoder) FROM stdin;
1	\N	\N	\N	public	private	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: indices; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.indices (id, created_at, updated_at, deleted_at, netelement_id, parameter_id, indices) FROM stdin;
\.


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.invoice (id, created_at, updated_at, deleted_at, contract_id, settlementrun_id, sepaaccount_id, year, month, filename, type, number, charge, charge_gross) FROM stdin;
\.


--
-- Data for Name: ippool; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ippool (id, created_at, updated_at, deleted_at, netgw_id, type, net, netmask, ip_pool_start, ip_pool_end, router_ip, broadcast_ip, dns1_ip, dns2_ip, dns3_ip, optional, description, version, prefix, prefix_len, delegated_len, active) FROM stdin;
\.


--
-- Data for Name: item; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.item (id, created_at, updated_at, deleted_at, contract_id, product_id, count, valid_from, valid_from_fixed, valid_to, valid_to_fixed, credit_amount, costcenter_id, accounting_text, payed_month, smartcardids) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: mibfile; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.mibfile (id, created_at, updated_at, deleted_at, name, filename, version, description) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.migrations (id, migration, batch) FROM stdin;
\.


--
-- Data for Name: modem; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.modem (id, created_at, updated_at, deleted_at, name, hostname, contract_id, contract_external_id, contract_ext_creation_date, contract_ext_termination_date, salutation, company, department, firstname, lastname, street, house_number, zip, city, district, birthday, country_id, country_code, installation_address_change_date, mac, us_pwr, us_snr, ds_pwr, ds_snr, public, internet_access, serial_num, inventar_num, description, parent, configfile_id, netelement_id, qos_id, lng, lat, geocode_source, number, tdr, fft_max, model, sw_rev, support_state, ppp_username, ppp_password, apartment_nr, next_passive_id, phy_updated_at, ipv4, address_to_invoice, apartment_id) FROM stdin;
\.


--
-- Data for Name: modem_option; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.modem_option (id, created_at, updated_at, deleted_at, modem_id, key, value) FROM stdin;
\.


--
-- Data for Name: mpr; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.mpr (id, created_at, updated_at, deleted_at, name, type, value, netelement_id, prio, description) FROM stdin;
\.


--
-- Data for Name: mprgeopos; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.mprgeopos (id, created_at, updated_at, deleted_at, name, mpr_id, lng, lat, description) FROM stdin;
\.


--
-- Data for Name: mta; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.mta (id, created_at, updated_at, deleted_at, modem_id, mac, hostname, configfile_id, type, is_dummy) FROM stdin;
\.


--
-- Data for Name: nas; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.nas (id, nasname, shortname, type, ports, secret, server, community, description) FROM stdin;
\.


--
-- Data for Name: netelement; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.netelement (id, created_at, updated_at, deleted_at, name, series, options, ip, lng, lat, link, "user", access, net, cluster, layer, descr, infrastructure_file, draw, line, parent_id, netelementtype_id, community_ro, community_rw, address1, address2, controlling_link, prov_device_id, netgw_id, agc_offset, rkm_line_number, state, _lft, _rgt, apartment_id) FROM stdin;
\.


--
-- Data for Name: netelementtype; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.netelementtype (id, created_at, updated_at, deleted_at, name, vendor, version, description, parent_id, icon_name, pre_conf_oid_id, pre_conf_value, pre_conf_time_offset, page_reload_time, base_type_id) FROM stdin;
1	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	Net	\N	\N	\N	\N	\N	\N	\N	0	0	1
2	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	Cluster	\N	\N	\N	1	\N	\N	\N	0	0	2
3	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	NetGw	\N	\N	\N	\N	\N	\N	\N	0	0	3
4	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	Amplifier	\N	\N	\N	\N	\N	\N	\N	0	0	4
5	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	Node	\N	\N	\N	\N	\N	\N	\N	0	0	5
6	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	Data	\N	\N	\N	\N	\N	\N	\N	0	0	6
7	2022-02-18 08:44:07+01	2022-02-18 08:44:09+01	\N	UPS	\N	\N	\N	\N	\N	\N	\N	0	0	7
8	2022-02-18 08:44:08+01	2022-02-18 08:44:09+01	\N	Tap	\N	\N	\N	\N	\N	\N	\N	\N	\N	8
9	2022-02-18 08:44:08+01	2022-02-18 08:44:09+01	\N	Tap-Port	\N	\N	\N	8	\N	\N	\N	\N	\N	9
11	2022-02-18 08:44:09+01	2022-02-18 08:44:09+01	\N	Passives	\N	\N	\N	\N	\N	\N	\N	\N	\N	11
12	2022-02-18 08:44:09+01	2022-02-18 08:44:09+01	\N	Splitter	\N	\N	\N	11	\N	\N	\N	\N	\N	11
13	2022-02-18 08:44:09+01	2022-02-18 08:44:09+01	\N	Amplifier	\N	\N	\N	11	\N	\N	\N	\N	\N	11
14	2022-02-18 08:44:09+01	2022-02-18 08:44:09+01	\N	Node	\N	\N	\N	11	\N	\N	\N	\N	\N	11
10	2022-02-18 08:46:22+01	2022-02-18 08:46:22+01	\N	NMSPrime HA slave	\N	\N	\N	\N	\N	\N	\N	\N	\N	10
\.


--
-- Data for Name: netgw; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.netgw (id, created_at, updated_at, deleted_at, hostname, series, ip, community_rw, community_ro, company, network, state, monitoring, support_state, type, username, password, ssh_port, ssh_auto_prov, coa_port, ipv6) FROM stdin;
\.


--
-- Data for Name: node; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.node (id, created_at, updated_at, deleted_at, netelement_id, name, street, house_nr, zip, city, type, headend, description, lng, lat, country_code, geocode_source, district) FROM stdin;
\.


--
-- Data for Name: notifications; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.notifications (id, type, notifiable_type, notifiable_id, data, read_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: numberrange; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.numberrange (id, created_at, updated_at, deleted_at, name, start, "end", prefix, suffix, costcenter_id, type) FROM stdin;
\.


--
-- Data for Name: oid; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.oid (id, created_at, updated_at, deleted_at, mibfile_id, html_type, name, oid, oid_table, type, type_array, phpcode_pre, phpcode_post, description, name_gui, unit_divisor, startvalue, endvalue, stepsize, syntax, access, value_set) FROM stdin;
\.


--
-- Data for Name: overduedebts; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.overduedebts (id, created_at, updated_at, deleted_at, fee, total, dunning_charge1, dunning_charge2, dunning_charge3, dunning_text1, dunning_text2, dunning_text3, payment_period, import_inet_block_amount, import_inet_block_debts, import_inet_block_indicator) FROM stdin;
1	\N	\N	\N	0.0000	f	\N	\N	\N	\N	\N	\N	14D	\N	\N	\N
\.


--
-- Data for Name: parameter; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.parameter (id, created_at, updated_at, deleted_at, netelementtype_id, oid_id, diff_param, divide_by, parent_id, third_dimension, html_frame, html_properties, html_id) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.permissions (ability_id, entity_id, entity_type, forbidden, scope, created_at, updated_at) FROM stdin;
1	1	App\\User	f	\N	\N	\N
2	1	roles	f	\N	\N	\N
2	3	roles	f	\N	\N	\N
3	4	roles	f	\N	\N	\N
4	3	roles	t	\N	\N	\N
5	3	roles	t	\N	\N	\N
6	3	roles	t	\N	\N	\N
8	5	roles	f	\N	\N	\N
9	5	roles	f	\N	\N	\N
1	1	App\\User	f	\N	\N	\N
2	1	roles	f	\N	\N	\N
2	3	roles	f	\N	\N	\N
3	4	roles	f	\N	\N	\N
4	3	roles	t	\N	\N	\N
5	3	roles	t	\N	\N	\N
6	3	roles	t	\N	\N	\N
8	5	roles	f	\N	\N	\N
9	5	roles	f	\N	\N	\N
\.


--
-- Data for Name: phonebookentry; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.phonebookentry (id, created_at, updated_at, deleted_at, phonenumbermanagement_id, reverse_search, publish_in_print_media, publish_in_electronic_media, directory_assistance, entry_type, publish_address, company, salutation, academic_degree, noble_rank, nobiliary_particle, lastname, other_name_suffix, firstname, street, houseno, zipcode, city, urban_district, business, usage, tag, external_creation_date, external_update_date) FROM stdin;
\.


--
-- Data for Name: phonenumber; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.phonenumber (id, created_at, updated_at, deleted_at, mta_id, port, country_code, prefix_number, number, username, password, sipdomain, active, is_dummy, contract_external_id) FROM stdin;
\.


--
-- Data for Name: phonenumbermanagement; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.phonenumbermanagement (id, created_at, updated_at, deleted_at, phonenumber_id, trcclass, voipaccount_ext_creation_date, activation_date, external_activation_date, porting_in, carrier_in, ekp_in, deactivation_date, external_deactivation_date, voipaccount_ext_termination_date, porting_out, carrier_out, ekp_out, subscriber_company, subscriber_department, subscriber_salutation, subscriber_academic_degree, subscriber_firstname, subscriber_lastname, subscriber_street, subscriber_house_number, subscriber_zip, subscriber_city, subscriber_district, subscriber_country, autogenerated, enviacontract_id) FROM stdin;
\.


--
-- Data for Name: phonetariff; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.phonetariff (id, created_at, updated_at, deleted_at, external_identifier, name, type, description, usable, voip_protocol) FROM stdin;
1	2022-02-18 08:44:17+01	2022-02-18 08:44:17+01	\N	1	Basic	basic	\N	t	\N
2	2022-02-18 08:44:17+01	2022-02-18 08:44:17+01	\N	2	Landline flat	landlineflat	\N	t	\N
3	2022-02-18 08:44:17+01	2022-02-18 08:44:17+01	\N	3	Allnetflat	allnetflat	\N	t	\N
\.


--
-- Data for Name: product; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.product (id, created_at, updated_at, deleted_at, name, type, qos_id, voip_sales_tariff_id, voip_purchase_tariff_id, billing_cycle, maturity, costcenter_id, price, tax, bundled_with_voip, email_count, period_of_notice, maturity_min, proportional, record_monthly, deprecated, markon) FROM stdin;
\.


--
-- Data for Name: provbase; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.provbase (id, created_at, updated_at, deleted_at, provisioning_server, ro_community, rw_community, notif_mail, domain_name, dns_password, dhcp_def_lease_time, dhcp_max_lease_time, startid_contract, startid_modem, startid_endpoint, max_cpe, ds_rate_coefficient, us_rate_coefficient, multiple_provisioning_systems, additional_modem_reset, modem_edit_page_new_tab, random_ip_allocation, ppp_session_timeout, auto_factory_reset, acct_interim_interval) FROM stdin;
1	\N	2022-02-18 08:43:53+01	\N	172.20.0.1	public	private	\N	nmsprime.test	3KHc51lgxGSOUvpUxlFv7A==	86400	172800	0	0	0	2	1	1	f	f	f	f	86400	f	300
\.


--
-- Data for Name: provmon; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.provmon (id, created_at, updated_at, deleted_at, start_frequency, stop_frequency, span) FROM stdin;
1	\N	\N	\N	154	866	8
\.


--
-- Data for Name: provvoip; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.provvoip (id, created_at, updated_at, deleted_at, startid_mta, mta_domain, default_sip_registrar, default_country_code) FROM stdin;
1	\N	\N	\N	300000	\N	\N	\N
\.


--
-- Data for Name: qos; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.qos (id, created_at, updated_at, deleted_at, ds_rate_max, us_rate_max, ds_rate_max_help, us_rate_max_help, name, ds_name, us_name) FROM stdin;
1	2022-02-18 12:57:42+01	2022-02-18 12:57:42+01	\N	1000	400	1000000000	400000000	MuQoS	Down	Up
\.


--
-- Data for Name: radacct; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radacct (radacctid, acctsessionid, acctuniqueid, username, groupname, realm, nasipaddress, nasportid, nasporttype, acctstarttime, acctupdatetime, acctstoptime, acctinterval, acctsessiontime, acctauthentic, connectinfo_start, connectinfo_stop, acctinputoctets, acctoutputoctets, calledstationid, callingstationid, acctterminatecause, servicetype, framedprotocol, framedipaddress) FROM stdin;
\.


--
-- Data for Name: radcheck; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radcheck (id, username, attribute, op, value) FROM stdin;
\.


--
-- Data for Name: radgroupcheck; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radgroupcheck (id, groupname, attribute, op, value) FROM stdin;
\.


--
-- Data for Name: radgroupreply; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radgroupreply (id, groupname, attribute, op, value) FROM stdin;
1	0	Port-Limit	:=	1
2	0	Framed-MTU	:=	1492
3	0	Framed-Protocol	:=	PPP
4	0	Service-Type	:=	Framed-User
5	0	Acct-Interim-Interval	:=	300
6	0	Session-Timeout	:=	86400
7	0	Fall-Through	= 	Yes
8	1	Ascend-Xmit-Rate	:=	1000000000
9	1	WISPr-Bandwidth-Max-Down	:=	1000000000
10	1	Cisco-Avpair	+=	ip:qos-policy-out=add-class(sub,(class-default),police(1000000000))
11	1	Ascend-Data-Rate	:=	400000000
12	1	WISPr-Bandwidth-Max-Up	:=	400000000
13	1	Cisco-Avpair	+=	ip:qos-policy-in=add-class(sub,(class-default),police(400000000))
14	1	Cisco-Avpair	+=	ip:sub-qos-policy-out=Down
15	1	Cisco-Avpair	+=	ip:sub-qos-policy-in=Up
\.


--
-- Data for Name: radippool; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radippool (id, pool_name, framedipaddress, nasipaddress, calledstationid, callingstationid, expiry_time, username, pool_key) FROM stdin;
\.


--
-- Data for Name: radpostauth; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radpostauth (id, username, pass, reply, authdate) FROM stdin;
\.


--
-- Data for Name: radreply; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radreply (id, username, attribute, op, value) FROM stdin;
\.


--
-- Data for Name: radusergroup; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.radusergroup (id, username, groupname, priority) FROM stdin;
\.


--
-- Data for Name: realty; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.realty (id, created_at, updated_at, deleted_at, node_id, name, number, street, house_nr, district, zip, city, expansion_degree, concession_agreement, agreement_from, agreement_to, last_restoration_on, description, contact_id, contact_local_id, lng, lat, geocode_source, country_code, contract_id) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.roles (id, name, title, description, rank, level, scope, created_at, updated_at, deleted_at) FROM stdin;
1	admin	Admin	Is allowed to do everything. Used for the initial user which can add other users.	101	\N	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
2	every_net	EveryNet	Is allowed to access every net. Used for the initial user which can add other users.	99	\N	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
3	support	Support	\N	0	\N	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
4	guest	Guest	\N	0	\N	\N	2022-02-18 08:43:27+01	2022-02-18 08:43:27+01	\N
5	accounting	Accounting	Only accounting relevant stuff	40	\N	\N	2022-02-18 08:43:34+01	2022-02-18 08:43:34+01	\N
\.


--
-- Data for Name: salesman; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.salesman (id, created_at, updated_at, deleted_at, firstname, lastname, commission, products, description) FROM stdin;
\.


--
-- Data for Name: sepaaccount; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.sepaaccount (id, created_at, updated_at, deleted_at, name, holder, creditorid, iban, bic, institute, company_id, invoice_headline, invoice_text, invoice_text_negativ, invoice_text_sepa, invoice_text_sepa_negativ, template_invoice, template_cdr, description, invoice_nr_start) FROM stdin;
1	2022-02-18 08:45:36+01	2022-02-18 08:45:36+01	\N	NMS Prime		0123456789	0123456789	\N	\N	1	Invoice	Please transfer the total amount with the following transfer reason within 14 days to the noted bank account:	\N	\N	\N	default-invoice-template.tex	default-cdr-template.tex	The inserted data appears on the invoices of your customers - this is a default added during the automatic installation.	\N
\.


--
-- Data for Name: sepamandate; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.sepamandate (id, created_at, updated_at, deleted_at, contract_id, reference, signature_date, holder, iban, bic, institute, valid_from, valid_to, state, costcenter_id, disable, description) FROM stdin;
\.


--
-- Data for Name: settlementrun; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.settlementrun (id, created_at, updated_at, deleted_at, executed_at, uploaded_at, year, month, path, description, verified, fullrun) FROM stdin;
\.


--
-- Data for Name: sla; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.sla (id, created_at, updated_at, deleted_at, name, license) FROM stdin;
1	\N	\N	\N	\N	\N
\.


--
-- Data for Name: supportrequest; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.supportrequest (id, created_at, updated_at, deleted_at, category, priority, mail, phone, text, sla_name) FROM stdin;
\.


--
-- Data for Name: ticket; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ticket (id, created_at, updated_at, deleted_at, name, description, user_id, state, priority, duedate, ticketable_type, ticketable_id, started_at, finished_at) FROM stdin;
\.


--
-- Data for Name: ticket_type; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ticket_type (id, created_at, updated_at, deleted_at, name, parent_id, description) FROM stdin;
\.


--
-- Data for Name: ticket_type_ticket; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ticket_type_ticket (id, created_at, ticket_type_id, ticket_id) FROM stdin;
\.


--
-- Data for Name: ticket_user; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ticket_user (id, created_at, updated_at, deleted_at, user_id, ticket_id) FROM stdin;
\.


--
-- Data for Name: ticketsystem; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.ticketsystem (id, created_at, updated_at, deleted_at, noreplymail, noreplyname, distance, modemcount, opentickets) FROM stdin;
1	2022-02-18 08:45:43+01	2022-02-18 08:45:43+01	\N			1	1	1
\.


--
-- Data for Name: trcclass; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.trcclass (id, created_at, updated_at, deleted_at, trc_id, trc_short, trc_description) FROM stdin;
1	\N	\N	\N	0	n/a	Dummy entry – no TRC classes known.
2	\N	\N	\N	\N	n/a	unknown or not set
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.users (id, created_at, updated_at, deleted_at, first_name, last_name, email, phonenumber, login_name, password, api_token, description, active, remember_token, language, last_login_at, password_changed_at, initial_dashboard, geopos_updated_at, lng, lat, hastruck) FROM stdin;
1	\N	2022-03-02 08:41:34+01	\N	superuser	initial	root@localhost	\N	root	$2y$10$FmNUYYYptfSu5Nye5UBkaeWuD8jugDriiAq3TPSBEUv4rRUTeEClu	3KWxQuu3OxHkQGrGfe4w83m9LirxIysEjNmlLGDyMlvhLFDeCvP6AkgxsffZAqgF1PBHFYLciL0EpodW	Superuser to do base config. Initial password is “toor” – change this ASAP or delete this user!!	t	\N	en	2022-03-02 08:41:34+01	\N	\N	2022-02-18 13:22:52+01	12.939680	50.828728	f
\.


--
-- Data for Name: websockets_statistics_entries; Type: TABLE DATA; Schema: nmsprime; Owner: postgres
--

COPY nmsprime.websockets_statistics_entries (id, app_id, peak_connection_count, websocket_message_count, api_message_count, created_at, updated_at) FROM stdin;
\.


--
-- Name: abilities_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.abilities_id_seq', 60, true);


--
-- Name: accountingrecord_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.accountingrecord_id_seq', 1, true);


--
-- Name: apartment_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.apartment_id_seq', 1, true);


--
-- Name: billingbase_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.billingbase_id_seq', 1, true);


--
-- Name: carriercode_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.carriercode_id_seq', 367, true);


--
-- Name: ccc_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ccc_id_seq', 1, true);


--
-- Name: comment_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.comment_id_seq', 1, true);


--
-- Name: company_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.company_id_seq', 1, true);


--
-- Name: configfile_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.configfile_id_seq', 3, true);


--
-- Name: contact_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.contact_id_seq', 1, true);


--
-- Name: contract_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.contract_id_seq', 1, true);


--
-- Name: costcenter_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.costcenter_id_seq', 1, true);


--
-- Name: debt_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.debt_id_seq', 1, true);


--
-- Name: domain_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.domain_id_seq', 1, true);


--
-- Name: ekpcode_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ekpcode_id_seq', 625, true);


--
-- Name: endpoint_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.endpoint_id_seq', 1, true);


--
-- Name: enviacontract_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.enviacontract_id_seq', 1, true);


--
-- Name: enviaorder_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.enviaorder_id_seq', 1, true);


--
-- Name: enviaorder_phonenumber_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.enviaorder_phonenumber_id_seq', 1, true);


--
-- Name: enviaorderdocument_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.enviaorderdocument_id_seq', 1, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.failed_jobs_id_seq', 1, false);


--
-- Name: favorite_netelements_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.favorite_netelements_id_seq', 1, true);


--
-- Name: global_config_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.global_config_id_seq', 1, true);


--
-- Name: guilog_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.guilog_id_seq', 1, false);


--
-- Name: hfcreq_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.hfcreq_id_seq', 1, true);


--
-- Name: indices_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.indices_id_seq', 1, true);


--
-- Name: invoice_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.invoice_id_seq', 1, true);


--
-- Name: ippool_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ippool_id_seq', 1, true);


--
-- Name: item_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.item_id_seq', 1, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.jobs_id_seq', 1, true);


--
-- Name: mibfile_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.mibfile_id_seq', 1, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.migrations_id_seq', 1, true);


--
-- Name: modem_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.modem_id_seq', 1, true);


--
-- Name: modem_option_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.modem_option_id_seq', 1, true);


--
-- Name: mpr_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.mpr_id_seq', 1, true);


--
-- Name: mprgeopos_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.mprgeopos_id_seq', 1, true);


--
-- Name: mta_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.mta_id_seq', 1, false);


--
-- Name: nas_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.nas_id_seq', 1, true);


--
-- Name: netelement_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.netelement_id_seq', 1, false);


--
-- Name: netelementtype_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.netelementtype_id_seq', 1000, false);


--
-- Name: netgw_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.netgw_id_seq', 1, true);


--
-- Name: node_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.node_id_seq', 1, true);


--
-- Name: numberrange_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.numberrange_id_seq', 1, true);


--
-- Name: oid_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.oid_id_seq', 1, true);


--
-- Name: overduedebts_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.overduedebts_id_seq', 1, true);


--
-- Name: parameter_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.parameter_id_seq', 1, true);


--
-- Name: phonebookentry_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.phonebookentry_id_seq', 1, true);


--
-- Name: phonenumber_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.phonenumber_id_seq', 1, false);


--
-- Name: phonenumbermanagement_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.phonenumbermanagement_id_seq', 1, true);


--
-- Name: phonetariff_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.phonetariff_id_seq', 3, true);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.product_id_seq', 1, true);


--
-- Name: provbase_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.provbase_id_seq', 1, true);


--
-- Name: provmon_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.provmon_id_seq', 1, true);


--
-- Name: provvoip_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.provvoip_id_seq', 1, true);


--
-- Name: qos_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.qos_id_seq', 2, true);


--
-- Name: radacct_radacctid_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radacct_radacctid_seq', 1, true);


--
-- Name: radcheck_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radcheck_id_seq', 1, true);


--
-- Name: radgroupcheck_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radgroupcheck_id_seq', 1, true);


--
-- Name: radgroupreply_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radgroupreply_id_seq', 15, true);


--
-- Name: radippool_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radippool_id_seq', 1, true);


--
-- Name: radpostauth_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radpostauth_id_seq', 1, true);


--
-- Name: radreply_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radreply_id_seq', 1, true);


--
-- Name: radusergroup_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.radusergroup_id_seq', 1, true);


--
-- Name: realty_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.realty_id_seq', 1, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.roles_id_seq', 5, true);


--
-- Name: salesman_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.salesman_id_seq', 1, true);


--
-- Name: sepaaccount_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.sepaaccount_id_seq', 1, true);


--
-- Name: sepamandate_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.sepamandate_id_seq', 1, true);


--
-- Name: settlementrun_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.settlementrun_id_seq', 1, true);


--
-- Name: sla_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.sla_id_seq', 1, true);


--
-- Name: supportrequest_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.supportrequest_id_seq', 1, true);


--
-- Name: ticket_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ticket_id_seq', 1, true);


--
-- Name: ticket_type_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ticket_type_id_seq', 1, true);


--
-- Name: ticket_type_ticket_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ticket_type_ticket_id_seq', 1, true);


--
-- Name: ticket_user_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ticket_user_id_seq', 1, true);


--
-- Name: ticketsystem_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.ticketsystem_id_seq', 1, true);


--
-- Name: trcclass_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.trcclass_id_seq', 2, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.users_id_seq', 1, true);


--
-- Name: websockets_statistics_entries_id_seq; Type: SEQUENCE SET; Schema: nmsprime; Owner: postgres
--

SELECT pg_catalog.setval('nmsprime.websockets_statistics_entries_id_seq', 1, true);


--
-- Name: abilities idx_17263_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.abilities
    ADD CONSTRAINT idx_17263_primary PRIMARY KEY (id);


--
-- Name: accountingrecord idx_17273_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.accountingrecord
    ADD CONSTRAINT idx_17273_primary PRIMARY KEY (id);


--
-- Name: apartment idx_17279_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.apartment
    ADD CONSTRAINT idx_17279_primary PRIMARY KEY (id);


--
-- Name: billingbase idx_17294_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.billingbase
    ADD CONSTRAINT idx_17294_primary PRIMARY KEY (id);


--
-- Name: carriercode idx_17303_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.carriercode
    ADD CONSTRAINT idx_17303_primary PRIMARY KEY (id);


--
-- Name: ccc idx_17309_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ccc
    ADD CONSTRAINT idx_17309_primary PRIMARY KEY (id);


--
-- Name: comment idx_17319_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.comment
    ADD CONSTRAINT idx_17319_primary PRIMARY KEY (id);


--
-- Name: company idx_17328_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.company
    ADD CONSTRAINT idx_17328_primary PRIMARY KEY (id);


--
-- Name: configfile idx_17337_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.configfile
    ADD CONSTRAINT idx_17337_primary PRIMARY KEY (id);


--
-- Name: contact idx_17349_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.contact
    ADD CONSTRAINT idx_17349_primary PRIMARY KEY (id);


--
-- Name: contract idx_17358_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.contract
    ADD CONSTRAINT idx_17358_primary PRIMARY KEY (id);


--
-- Name: costcenter idx_17367_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.costcenter
    ADD CONSTRAINT idx_17367_primary PRIMARY KEY (id);


--
-- Name: debt idx_17376_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.debt
    ADD CONSTRAINT idx_17376_primary PRIMARY KEY (id);


--
-- Name: domain idx_17385_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.domain
    ADD CONSTRAINT idx_17385_primary PRIMARY KEY (id);


--
-- Name: ekpcode idx_17391_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ekpcode
    ADD CONSTRAINT idx_17391_primary PRIMARY KEY (id);


--
-- Name: endpoint idx_17397_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.endpoint
    ADD CONSTRAINT idx_17397_primary PRIMARY KEY (id);


--
-- Name: enviacontract idx_17407_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviacontract
    ADD CONSTRAINT idx_17407_primary PRIMARY KEY (id);


--
-- Name: enviaorder idx_17413_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorder
    ADD CONSTRAINT idx_17413_primary PRIMARY KEY (id);


--
-- Name: enviaorderdocument idx_17422_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorderdocument
    ADD CONSTRAINT idx_17422_primary PRIMARY KEY (id);


--
-- Name: enviaorder_phonenumber idx_17428_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.enviaorder_phonenumber
    ADD CONSTRAINT idx_17428_primary PRIMARY KEY (id);


--
-- Name: failed_jobs idx_17434_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.failed_jobs
    ADD CONSTRAINT idx_17434_primary PRIMARY KEY (id);


--
-- Name: favorite_netelements idx_17444_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.favorite_netelements
    ADD CONSTRAINT idx_17444_primary PRIMARY KEY (id);


--
-- Name: global_config idx_17450_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.global_config
    ADD CONSTRAINT idx_17450_primary PRIMARY KEY (id);


--
-- Name: guilog idx_17461_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.guilog
    ADD CONSTRAINT idx_17461_primary PRIMARY KEY (id);


--
-- Name: hfcreq idx_17470_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.hfcreq
    ADD CONSTRAINT idx_17470_primary PRIMARY KEY (id);


--
-- Name: indices idx_17479_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.indices
    ADD CONSTRAINT idx_17479_primary PRIMARY KEY (id);


--
-- Name: invoice idx_17488_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.invoice
    ADD CONSTRAINT idx_17488_primary PRIMARY KEY (id);


--
-- Name: ippool idx_17494_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ippool
    ADD CONSTRAINT idx_17494_primary PRIMARY KEY (id);


--
-- Name: item idx_17505_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.item
    ADD CONSTRAINT idx_17505_primary PRIMARY KEY (id);


--
-- Name: jobs idx_17517_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.jobs
    ADD CONSTRAINT idx_17517_primary PRIMARY KEY (id);


--
-- Name: mibfile idx_17526_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mibfile
    ADD CONSTRAINT idx_17526_primary PRIMARY KEY (id);


--
-- Name: migrations idx_17535_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.migrations
    ADD CONSTRAINT idx_17535_primary PRIMARY KEY (id);


--
-- Name: modem idx_17541_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.modem
    ADD CONSTRAINT idx_17541_primary PRIMARY KEY (id);


--
-- Name: modem_option idx_17551_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.modem_option
    ADD CONSTRAINT idx_17551_primary PRIMARY KEY (id);


--
-- Name: mpr idx_17557_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mpr
    ADD CONSTRAINT idx_17557_primary PRIMARY KEY (id);


--
-- Name: mprgeopos idx_17566_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mprgeopos
    ADD CONSTRAINT idx_17566_primary PRIMARY KEY (id);


--
-- Name: mta idx_17575_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.mta
    ADD CONSTRAINT idx_17575_primary PRIMARY KEY (id);


--
-- Name: nas idx_17584_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.nas
    ADD CONSTRAINT idx_17584_primary PRIMARY KEY (id);


--
-- Name: netelement idx_17596_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netelement
    ADD CONSTRAINT idx_17596_primary PRIMARY KEY (id);


--
-- Name: netelementtype idx_17605_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netelementtype
    ADD CONSTRAINT idx_17605_primary PRIMARY KEY (id);


--
-- Name: netgw idx_17614_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.netgw
    ADD CONSTRAINT idx_17614_primary PRIMARY KEY (id);


--
-- Name: node idx_17626_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.node
    ADD CONSTRAINT idx_17626_primary PRIMARY KEY (id);


--
-- Name: notifications idx_17633_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.notifications
    ADD CONSTRAINT idx_17633_primary PRIMARY KEY (id);


--
-- Name: numberrange idx_17641_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.numberrange
    ADD CONSTRAINT idx_17641_primary PRIMARY KEY (id);


--
-- Name: oid idx_17652_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.oid
    ADD CONSTRAINT idx_17652_primary PRIMARY KEY (id);


--
-- Name: overduedebts idx_17661_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.overduedebts
    ADD CONSTRAINT idx_17661_primary PRIMARY KEY (id);


--
-- Name: parameter idx_17670_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.parameter
    ADD CONSTRAINT idx_17670_primary PRIMARY KEY (id);


--
-- Name: phonebookentry idx_17685_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonebookentry
    ADD CONSTRAINT idx_17685_primary PRIMARY KEY (id);


--
-- Name: phonenumber idx_17694_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonenumber
    ADD CONSTRAINT idx_17694_primary PRIMARY KEY (id);


--
-- Name: phonenumbermanagement idx_17705_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonenumbermanagement
    ADD CONSTRAINT idx_17705_primary PRIMARY KEY (id);


--
-- Name: phonetariff idx_17718_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.phonetariff
    ADD CONSTRAINT idx_17718_primary PRIMARY KEY (id);


--
-- Name: product idx_17728_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.product
    ADD CONSTRAINT idx_17728_primary PRIMARY KEY (id);


--
-- Name: provbase idx_17735_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provbase
    ADD CONSTRAINT idx_17735_primary PRIMARY KEY (id);


--
-- Name: provmon idx_17748_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provmon
    ADD CONSTRAINT idx_17748_primary PRIMARY KEY (id);


--
-- Name: provvoip idx_17754_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.provvoip
    ADD CONSTRAINT idx_17754_primary PRIMARY KEY (id);


--
-- Name: qos idx_17763_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.qos
    ADD CONSTRAINT idx_17763_primary PRIMARY KEY (id);


--
-- Name: radacct idx_17772_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radacct
    ADD CONSTRAINT idx_17772_primary PRIMARY KEY (radacctid);


--
-- Name: radcheck idx_17791_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radcheck
    ADD CONSTRAINT idx_17791_primary PRIMARY KEY (id);


--
-- Name: radgroupcheck idx_17801_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radgroupcheck
    ADD CONSTRAINT idx_17801_primary PRIMARY KEY (id);


--
-- Name: radgroupreply idx_17811_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radgroupreply
    ADD CONSTRAINT idx_17811_primary PRIMARY KEY (id);


--
-- Name: radippool idx_17821_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radippool
    ADD CONSTRAINT idx_17821_primary PRIMARY KEY (id);


--
-- Name: radpostauth idx_17830_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radpostauth
    ADD CONSTRAINT idx_17830_primary PRIMARY KEY (id);


--
-- Name: radreply idx_17839_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radreply
    ADD CONSTRAINT idx_17839_primary PRIMARY KEY (id);


--
-- Name: radusergroup idx_17849_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.radusergroup
    ADD CONSTRAINT idx_17849_primary PRIMARY KEY (id);


--
-- Name: realty idx_17858_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.realty
    ADD CONSTRAINT idx_17858_primary PRIMARY KEY (id);


--
-- Name: roles idx_17867_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.roles
    ADD CONSTRAINT idx_17867_primary PRIMARY KEY (id);


--
-- Name: salesman idx_17876_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.salesman
    ADD CONSTRAINT idx_17876_primary PRIMARY KEY (id);


--
-- Name: sepaaccount idx_17885_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sepaaccount
    ADD CONSTRAINT idx_17885_primary PRIMARY KEY (id);


--
-- Name: sepamandate idx_17894_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sepamandate
    ADD CONSTRAINT idx_17894_primary PRIMARY KEY (id);


--
-- Name: settlementrun idx_17903_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.settlementrun
    ADD CONSTRAINT idx_17903_primary PRIMARY KEY (id);


--
-- Name: sla idx_17909_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.sla
    ADD CONSTRAINT idx_17909_primary PRIMARY KEY (id);


--
-- Name: supportrequest idx_17915_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.supportrequest
    ADD CONSTRAINT idx_17915_primary PRIMARY KEY (id);


--
-- Name: ticket idx_17924_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket
    ADD CONSTRAINT idx_17924_primary PRIMARY KEY (id);


--
-- Name: ticketsystem idx_17933_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticketsystem
    ADD CONSTRAINT idx_17933_primary PRIMARY KEY (id);


--
-- Name: ticket_type idx_17942_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_type
    ADD CONSTRAINT idx_17942_primary PRIMARY KEY (id);


--
-- Name: ticket_type_ticket idx_17948_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_type_ticket
    ADD CONSTRAINT idx_17948_primary PRIMARY KEY (id);


--
-- Name: ticket_user idx_17954_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.ticket_user
    ADD CONSTRAINT idx_17954_primary PRIMARY KEY (id);


--
-- Name: trcclass idx_17960_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.trcclass
    ADD CONSTRAINT idx_17960_primary PRIMARY KEY (id);


--
-- Name: users idx_17966_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.users
    ADD CONSTRAINT idx_17966_primary PRIMARY KEY (id);


--
-- Name: websockets_statistics_entries idx_17978_primary; Type: CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.websockets_statistics_entries
    ADD CONSTRAINT idx_17978_primary PRIMARY KEY (id);


--
-- Name: idx_17263_abilities_scope_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17263_abilities_scope_index ON nmsprime.abilities USING btree (scope);


--
-- Name: idx_17279_apartment_realty_id_foreign; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17279_apartment_realty_id_foreign ON nmsprime.apartment USING btree (realty_id);


--
-- Name: idx_17286_assigned_roles_entity_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17286_assigned_roles_entity_index ON nmsprime.assigned_roles USING btree (entity_id, entity_type, scope);


--
-- Name: idx_17286_assigned_roles_role_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17286_assigned_roles_role_id_index ON nmsprime.assigned_roles USING btree (role_id);


--
-- Name: idx_17286_assigned_roles_scope_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17286_assigned_roles_scope_index ON nmsprime.assigned_roles USING btree (scope);


--
-- Name: idx_17289_authreminders_email_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17289_authreminders_email_index ON nmsprime.authreminders USING btree (email);


--
-- Name: idx_17289_authreminders_token_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17289_authreminders_token_index ON nmsprime.authreminders USING btree (token);


--
-- Name: idx_17303_carriercode_carrier_code_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17303_carriercode_carrier_code_unique ON nmsprime.carriercode USING btree (carrier_code);


--
-- Name: idx_17337_configfile_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17337_configfile_parent_id_index ON nmsprime.configfile USING btree (parent_id);


--
-- Name: idx_17358_contract_apartment_id_foreign; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_apartment_id_foreign ON nmsprime.contract USING btree (apartment_id);


--
-- Name: idx_17358_contract_costcenter_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_costcenter_id_index ON nmsprime.contract USING btree (costcenter_id);


--
-- Name: idx_17358_contract_next_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_next_qos_id_index ON nmsprime.contract USING btree (next_qos_id);


--
-- Name: idx_17358_contract_number_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_number_index ON nmsprime.contract USING btree (number);


--
-- Name: idx_17358_contract_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_qos_id_index ON nmsprime.contract USING btree (qos_id);


--
-- Name: idx_17358_contract_salesman_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17358_contract_salesman_id_index ON nmsprime.contract USING btree (salesman_id);


--
-- Name: idx_17391_ekpcode_ekp_code_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17391_ekpcode_ekp_code_unique ON nmsprime.ekpcode USING btree (ekp_code);


--
-- Name: idx_17397_endpoint_modem_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17397_endpoint_modem_id_index ON nmsprime.endpoint USING btree (modem_id);


--
-- Name: idx_17413_enviaorder_orderid_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17413_enviaorder_orderid_unique ON nmsprime.enviaorder USING btree (orderid);


--
-- Name: idx_17444_favorite_netelements_user_id_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17444_favorite_netelements_user_id_netelement_id_index ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);


--
-- Name: idx_17444_favorite_netelements_user_id_netelement_id_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17444_favorite_netelements_user_id_netelement_id_unique ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);


--
-- Name: idx_17494_ippool_netgw_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17494_ippool_netgw_id_index ON nmsprime.ippool USING btree (netgw_id);


--
-- Name: idx_17505_by_contract_id; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17505_by_contract_id ON nmsprime.item USING btree (contract_id);


--
-- Name: idx_17505_by_product_id; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17505_by_product_id ON nmsprime.item USING btree (product_id);


--
-- Name: idx_17517_jobs_queue_reserved_at_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17517_jobs_queue_reserved_at_index ON nmsprime.jobs USING btree (queue, reserved_at);


--
-- Name: idx_17541_modem_configfile_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17541_modem_configfile_id_index ON nmsprime.modem USING btree (configfile_id);


--
-- Name: idx_17541_modem_contract_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17541_modem_contract_id_index ON nmsprime.modem USING btree (contract_id);


--
-- Name: idx_17541_modem_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17541_modem_netelement_id_index ON nmsprime.modem USING btree (netelement_id);


--
-- Name: idx_17541_modem_qos_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17541_modem_qos_id_index ON nmsprime.modem USING btree (qos_id);


--
-- Name: idx_17557_mpr_netelement_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17557_mpr_netelement_id_index ON nmsprime.mpr USING btree (netelement_id);


--
-- Name: idx_17566_mprgeopos_mpr_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17566_mprgeopos_mpr_id_index ON nmsprime.mprgeopos USING btree (mpr_id);


--
-- Name: idx_17575_mta_configfile_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17575_mta_configfile_id_index ON nmsprime.mta USING btree (configfile_id);


--
-- Name: idx_17575_mta_modem_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17575_mta_modem_id_index ON nmsprime.mta USING btree (modem_id);


--
-- Name: idx_17584_nasname; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17584_nasname ON nmsprime.nas USING btree (nasname);


--
-- Name: idx_17596_netelement_netelementtype_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17596_netelement_netelementtype_id_index ON nmsprime.netelement USING btree (netelementtype_id);


--
-- Name: idx_17596_netelement_netgw_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17596_netelement_netgw_id_index ON nmsprime.netelement USING btree (netgw_id);


--
-- Name: idx_17596_netelement_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17596_netelement_parent_id_index ON nmsprime.netelement USING btree (parent_id);


--
-- Name: idx_17605_netelementtype_parent_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17605_netelementtype_parent_id_index ON nmsprime.netelementtype USING btree (parent_id);


--
-- Name: idx_17626_node_netelement_id_foreign; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17626_node_netelement_id_foreign ON nmsprime.node USING btree (netelement_id);


--
-- Name: idx_17633_notifications_notifiable_type_notifiable_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17633_notifications_notifiable_type_notifiable_id_index ON nmsprime.notifications USING btree (notifiable_type, notifiable_id);


--
-- Name: idx_17679_permissions_ability_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17679_permissions_ability_id_index ON nmsprime.permissions USING btree (ability_id);


--
-- Name: idx_17679_permissions_entity_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17679_permissions_entity_index ON nmsprime.permissions USING btree (entity_id, entity_type, scope);


--
-- Name: idx_17679_permissions_scope_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17679_permissions_scope_index ON nmsprime.permissions USING btree (scope);


--
-- Name: idx_17694_phonenumber_mta_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17694_phonenumber_mta_id_index ON nmsprime.phonenumber USING btree (mta_id);


--
-- Name: idx_17772_acctinterval; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_acctinterval ON nmsprime.radacct USING btree (acctinterval);


--
-- Name: idx_17772_acctsessionid; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_acctsessionid ON nmsprime.radacct USING btree (acctsessionid);


--
-- Name: idx_17772_acctsessiontime; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_acctsessiontime ON nmsprime.radacct USING btree (acctsessiontime);


--
-- Name: idx_17772_acctstarttime; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_acctstarttime ON nmsprime.radacct USING btree (acctstarttime);


--
-- Name: idx_17772_acctstoptime; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_acctstoptime ON nmsprime.radacct USING btree (acctstoptime);


--
-- Name: idx_17772_acctuniqueid; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17772_acctuniqueid ON nmsprime.radacct USING btree (acctuniqueid);


--
-- Name: idx_17772_bulk_close; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_bulk_close ON nmsprime.radacct USING btree (acctstoptime, nasipaddress, acctstarttime);


--
-- Name: idx_17772_framedipaddress; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_framedipaddress ON nmsprime.radacct USING btree (framedipaddress);


--
-- Name: idx_17772_nasipaddress; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_nasipaddress ON nmsprime.radacct USING btree (nasipaddress);


--
-- Name: idx_17772_username; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17772_username ON nmsprime.radacct USING btree (username);


--
-- Name: idx_17791_username; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17791_username ON nmsprime.radcheck USING btree (username);


--
-- Name: idx_17801_groupname; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17801_groupname ON nmsprime.radgroupcheck USING btree (groupname);


--
-- Name: idx_17811_groupname; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17811_groupname ON nmsprime.radgroupreply USING btree (groupname);


--
-- Name: idx_17821_framedipaddress; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17821_framedipaddress ON nmsprime.radippool USING btree (framedipaddress);


--
-- Name: idx_17821_radippool_nasip_poolkey_ipaddress; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17821_radippool_nasip_poolkey_ipaddress ON nmsprime.radippool USING btree (nasipaddress, pool_key, framedipaddress);


--
-- Name: idx_17821_radippool_poolname_expire; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17821_radippool_poolname_expire ON nmsprime.radippool USING btree (pool_name, expiry_time);


--
-- Name: idx_17839_username; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17839_username ON nmsprime.radreply USING btree (username);


--
-- Name: idx_17849_username; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17849_username ON nmsprime.radusergroup USING btree (username);


--
-- Name: idx_17858_realty_node_id_foreign; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17858_realty_node_id_foreign ON nmsprime.realty USING btree (node_id);


--
-- Name: idx_17867_roles_name_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17867_roles_name_unique ON nmsprime.roles USING btree (name, scope);


--
-- Name: idx_17867_roles_scope_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17867_roles_scope_index ON nmsprime.roles USING btree (scope);


--
-- Name: idx_17894_by_contract_id; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17894_by_contract_id ON nmsprime.sepamandate USING btree (contract_id);


--
-- Name: idx_17894_by_costcenter_id; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17894_by_costcenter_id ON nmsprime.sepamandate USING btree (costcenter_id);


--
-- Name: idx_17924_ticket_ticketable_type_ticketable_id_index; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE INDEX idx_17924_ticket_ticketable_type_ticketable_id_index ON nmsprime.ticket USING btree (ticketable_type, ticketable_id);


--
-- Name: idx_17960_trc_id; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17960_trc_id ON nmsprime.trcclass USING btree (trc_id);


--
-- Name: idx_17960_trcclass_trc_id_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17960_trcclass_trc_id_unique ON nmsprime.trcclass USING btree (trc_id);


--
-- Name: idx_17966_users_api_token_unique; Type: INDEX; Schema: nmsprime; Owner: postgres
--

CREATE UNIQUE INDEX idx_17966_users_api_token_unique ON nmsprime.users USING btree (api_token);


--
-- Name: authreminders on_update_current_timestamp; Type: TRIGGER; Schema: nmsprime; Owner: postgres
--

CREATE TRIGGER on_update_current_timestamp BEFORE UPDATE ON nmsprime.authreminders FOR EACH ROW EXECUTE FUNCTION nmsprime.on_update_current_timestamp_authreminders();


--
-- Name: radpostauth on_update_current_timestamp; Type: TRIGGER; Schema: nmsprime; Owner: postgres
--

CREATE TRIGGER on_update_current_timestamp BEFORE UPDATE ON nmsprime.radpostauth FOR EACH ROW EXECUTE FUNCTION nmsprime.on_update_current_timestamp_radpostauth();


--
-- Name: assigned_roles assigned_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.assigned_roles
    ADD CONSTRAINT assigned_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES nmsprime.roles(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: permissions permissions_ability_id_foreign; Type: FK CONSTRAINT; Schema: nmsprime; Owner: postgres
--

ALTER TABLE ONLY nmsprime.permissions
    ADD CONSTRAINT permissions_ability_id_foreign FOREIGN KEY (ability_id) REFERENCES nmsprime.abilities(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: SCHEMA nmsprime; Type: ACL; Schema: -; Owner: postgres
--

GRANT USAGE ON SCHEMA nmsprime TO nmsprime;


--
-- PostgreSQL database dump complete
--

