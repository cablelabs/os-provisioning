

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


CREATE SCHEMA nmsprime;


ALTER SCHEMA nmsprime OWNER TO nmsprime;


CREATE TYPE nmsprime.billingbase_userlang AS ENUM (
    'de',
    'en'
);


ALTER TYPE nmsprime.billingbase_userlang OWNER TO nmsprime;


CREATE TYPE nmsprime.configfile_device AS ENUM (
    'cm',
    'mta',
    'tr069',
    'ont'
);


ALTER TYPE nmsprime.configfile_device OWNER TO nmsprime;


CREATE TYPE nmsprime.configfile_public AS ENUM (
    'yes',
    'no'
);


ALTER TYPE nmsprime.configfile_public OWNER TO nmsprime;


CREATE TYPE nmsprime.domain_type AS ENUM (
    'SIP',
    'Email',
    'DHCP'
);


ALTER TYPE nmsprime.domain_type OWNER TO nmsprime;


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


CREATE TYPE nmsprime.invoice_type AS ENUM (
    'Invoice',
    'CDR'
);


ALTER TYPE nmsprime.invoice_type OWNER TO nmsprime;


CREATE TYPE nmsprime.ippool_type AS ENUM (
    'CM',
    'CPEPub',
    'CPEPriv',
    'MTA'
);


ALTER TYPE nmsprime.ippool_type OWNER TO nmsprime;


CREATE TYPE nmsprime.mta_type AS ENUM (
    'sip',
    'packetcable'
);


ALTER TYPE nmsprime.mta_type OWNER TO nmsprime;


CREATE TYPE nmsprime.numberrange_type AS ENUM (
    'contract',
    'invoice'
);


ALTER TYPE nmsprime.numberrange_type OWNER TO nmsprime;


CREATE TYPE nmsprime.oid_html_type AS ENUM (
    'text',
    'select',
    'groupbox',
    'textarea'
);


ALTER TYPE nmsprime.oid_html_type OWNER TO nmsprime;


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


CREATE TYPE nmsprime.phonetariff_type AS ENUM (
    'purchase',
    'sale',
    'basic',
    'landlineflat',
    'allnetflat'
);


ALTER TYPE nmsprime.phonetariff_type OWNER TO nmsprime;


CREATE TYPE nmsprime.phonetariff_voip_protocol AS ENUM (
    'MGCP',
    'SIP'
);


ALTER TYPE nmsprime.phonetariff_voip_protocol OWNER TO nmsprime;


CREATE TYPE nmsprime.product_billing_cycle AS ENUM (
    'Once',
    'Monthly',
    'Quarterly',
    'Yearly'
);


ALTER TYPE nmsprime.product_billing_cycle OWNER TO nmsprime;


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


CREATE TYPE nmsprime.sepamandate_state AS ENUM (
    'FRST',
    'RCUR',
    'OOFF',
    'FNAL'
);


ALTER TYPE nmsprime.sepamandate_state OWNER TO nmsprime;


CREATE TYPE nmsprime.ticket_priority AS ENUM (
    'Trivial',
    'Minor',
    'Major',
    'Critical'
);


ALTER TYPE nmsprime.ticket_priority OWNER TO nmsprime;


CREATE FUNCTION nmsprime.on_update_current_timestamp_authreminders() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.created_at = now();
   RETURN NEW;
END;
$$;


ALTER FUNCTION nmsprime.on_update_current_timestamp_authreminders() OWNER TO nmsprime;



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


CREATE SEQUENCE nmsprime.abilities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.abilities_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.abilities_id_seq OWNED BY nmsprime.abilities.id;



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


CREATE SEQUENCE nmsprime.accountingrecord_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.accountingrecord_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.accountingrecord_id_seq OWNED BY nmsprime.accountingrecord.id;



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


CREATE SEQUENCE nmsprime.apartment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.apartment_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.apartment_id_seq OWNED BY nmsprime.apartment.id;



CREATE TABLE nmsprime.assigned_roles (
    role_id bigint,
    entity_id bigint,
    entity_type character varying(150),
    scope bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.assigned_roles OWNER TO nmsprime;


CREATE TABLE nmsprime.authreminders (
    email character varying(191),
    token character varying(191),
    created_at timestamp with time zone
);


ALTER TABLE nmsprime.authreminders OWNER TO nmsprime;


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


CREATE SEQUENCE nmsprime.billingbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.billingbase_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.billingbase_id_seq OWNED BY nmsprime.billingbase.id;



CREATE TABLE nmsprime.carriercode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    carrier_code character varying(4),
    company character varying(191)
);


ALTER TABLE nmsprime.carriercode OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.carriercode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.carriercode_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.carriercode_id_seq OWNED BY nmsprime.carriercode.id;



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


ALTER TABLE nmsprime.ccc OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.ccc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ccc_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ccc_id_seq OWNED BY nmsprime.ccc.id;



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


CREATE SEQUENCE nmsprime.comment_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.comment_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.comment_id_seq OWNED BY nmsprime.comment.id;



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


CREATE SEQUENCE nmsprime.company_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.company_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.company_id_seq OWNED BY nmsprime.company.id;



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
    service_profile_id bigint,
    ont_line_profile_id bigint,
    tr069_profile_id bigint,
    is_multiservice_ont boolean DEFAULT false
);


ALTER TABLE nmsprime.configfile OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.configfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.configfile_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.configfile_id_seq OWNED BY nmsprime.configfile.id;



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


CREATE SEQUENCE nmsprime.contact_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contact_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.contact_id_seq OWNED BY nmsprime.contact.id;



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
    sep_id character varying(32),
    oto_id character varying(64),
    oto_port integer,
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


CREATE SEQUENCE nmsprime.contract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.contract_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.contract_id_seq OWNED BY nmsprime.contract.id;



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


CREATE SEQUENCE nmsprime.costcenter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.costcenter_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.costcenter_id_seq OWNED BY nmsprime.costcenter.id;



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


CREATE SEQUENCE nmsprime.debt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.debt_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.debt_id_seq OWNED BY nmsprime.debt.id;



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
    subscription_id bigint,
    subscription_end_point_id bigint,
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
    contract_id bigint
);


ALTER TABLE nmsprime.dfsubscription OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.dfsubscription_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dfsubscription_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.dfsubscription_id_seq OWNED BY nmsprime.dfsubscription.id;



CREATE TABLE nmsprime.dfsubscriptionevent (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    description text,
    status character varying(32),
    "timestamp" character varying(32),
    dfsubscription_id bigint
);


ALTER TABLE nmsprime.dfsubscriptionevent OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.dfsubscriptionevent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.dfsubscriptionevent_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.dfsubscriptionevent_id_seq OWNED BY nmsprime.dfsubscriptionevent.id;



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


CREATE SEQUENCE nmsprime.domain_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.domain_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.domain_id_seq OWNED BY nmsprime.domain.id;



CREATE TABLE nmsprime.ekpcode (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    ekp_code character varying(191),
    company character varying(191)
);


ALTER TABLE nmsprime.ekpcode OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.ekpcode_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ekpcode_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ekpcode_id_seq OWNED BY nmsprime.ekpcode.id;



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
    qos_id bigint,
    device_id integer,
    acl_id bigint,
    rule_id bigint,
    state character varying(191)
);


ALTER TABLE nmsprime.endpoint OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.endpoint_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.endpoint_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.endpoint_id_seq OWNED BY nmsprime.endpoint.id;



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


CREATE SEQUENCE nmsprime.enviacontract_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviacontract_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.enviacontract_id_seq OWNED BY nmsprime.enviacontract.id;



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


CREATE SEQUENCE nmsprime.enviaorder_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.enviaorder_id_seq OWNED BY nmsprime.enviaorder.id;



CREATE TABLE nmsprime.enviaorder_phonenumber (
    id bigint NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    deleted_at timestamp without time zone,
    enviaorder_id bigint,
    phonenumber_id bigint
);


ALTER TABLE nmsprime.enviaorder_phonenumber OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.enviaorder_phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorder_phonenumber_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.enviaorder_phonenumber_id_seq OWNED BY nmsprime.enviaorder_phonenumber.id;



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


CREATE SEQUENCE nmsprime.enviaorderdocument_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.enviaorderdocument_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.enviaorderdocument_id_seq OWNED BY nmsprime.enviaorderdocument.id;



CREATE TABLE nmsprime.failed_jobs (
    id bigint NOT NULL,
    connection text,
    queue text,
    payload text,
    failed_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    exception text
);


ALTER TABLE nmsprime.failed_jobs OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.failed_jobs_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.failed_jobs_id_seq OWNED BY nmsprime.failed_jobs.id;



CREATE TABLE nmsprime.favorite_netelements (
    id bigint NOT NULL,
    user_id bigint,
    netelement_id bigint,
    created_at timestamp with time zone,
    updated_at timestamp with time zone
);


ALTER TABLE nmsprime.favorite_netelements OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.favorite_netelements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.favorite_netelements_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.favorite_netelements_id_seq OWNED BY nmsprime.favorite_netelements.id;



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


ALTER TABLE nmsprime.global_config OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.global_config_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.global_config_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.global_config_id_seq OWNED BY nmsprime.global_config.id;



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


CREATE SEQUENCE nmsprime.guilog_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.guilog_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.guilog_id_seq OWNED BY nmsprime.guilog.id;



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


CREATE SEQUENCE nmsprime.hfcreq_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.hfcreq_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.hfcreq_id_seq OWNED BY nmsprime.hfcreq.id;



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


CREATE SEQUENCE nmsprime.indices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.indices_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.indices_id_seq OWNED BY nmsprime.indices.id;



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


CREATE SEQUENCE nmsprime.invoice_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.invoice_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.invoice_id_seq OWNED BY nmsprime.invoice.id;



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


ALTER TABLE nmsprime.ippool OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.ippool_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ippool_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ippool_id_seq OWNED BY nmsprime.ippool.id;



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


CREATE SEQUENCE nmsprime.item_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.item_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.item_id_seq OWNED BY nmsprime.item.id;



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


CREATE SEQUENCE nmsprime.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.jobs_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.jobs_id_seq OWNED BY nmsprime.jobs.id;



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


CREATE SEQUENCE nmsprime.mibfile_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mibfile_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.mibfile_id_seq OWNED BY nmsprime.mibfile.id;



CREATE TABLE nmsprime.migrations (
    id bigint NOT NULL,
    migration character varying(255),
    batch bigint
);


ALTER TABLE nmsprime.migrations OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.migrations_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.migrations_id_seq OWNED BY nmsprime.migrations.id;



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
    apartment_id bigint,
    ont_id integer,
    netgw_id bigint,
    frame_id integer,
    slot_id integer,
    port_id integer,
    service_port_id bigint,
    or_id character varying(191),
    ont_state character varying(191),
    next_ont_state character varying(191),
    ont_state_switchdate timestamp with time zone
);


ALTER TABLE nmsprime.modem OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.modem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.modem_id_seq OWNED BY nmsprime.modem.id;



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


CREATE SEQUENCE nmsprime.modem_option_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.modem_option_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.modem_option_id_seq OWNED BY nmsprime.modem_option.id;



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


CREATE SEQUENCE nmsprime.mpr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mpr_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.mpr_id_seq OWNED BY nmsprime.mpr.id;



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


CREATE SEQUENCE nmsprime.mprgeopos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mprgeopos_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.mprgeopos_id_seq OWNED BY nmsprime.mprgeopos.id;



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


CREATE SEQUENCE nmsprime.mta_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.mta_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.mta_id_seq OWNED BY nmsprime.mta.id;



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
    ELSE (((id)::character varying)::text || '_'::text) || ((name)::text)
END) STORED
);


ALTER TABLE nmsprime.netelement OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.netelement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelement_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.netelement_id_seq OWNED BY nmsprime.netelement.id;



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


ALTER TABLE nmsprime.netelementtype OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.netelementtype_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netelementtype_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.netelementtype_id_seq OWNED BY nmsprime.netelementtype.id;



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
    internal_id bigint DEFAULT '0'::bigint
);


ALTER TABLE nmsprime.netgw OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.netgw_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.netgw_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.netgw_id_seq OWNED BY nmsprime.netgw.id;



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


CREATE SEQUENCE nmsprime.node_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.node_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.node_id_seq OWNED BY nmsprime.node.id;



CREATE TABLE nmsprime.notifications (
    id character(36),
    type character varying(191),
    notifiable_type character varying(191),
    notifiable_id numeric,
    data text,
    read_at timestamp without time zone,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE nmsprime.notifications OWNER TO nmsprime;


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


CREATE SEQUENCE nmsprime.numberrange_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.numberrange_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.numberrange_id_seq OWNED BY nmsprime.numberrange.id;



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


CREATE SEQUENCE nmsprime.oid_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.oid_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.oid_id_seq OWNED BY nmsprime.oid.id;



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


CREATE SEQUENCE nmsprime.overduedebts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.overduedebts_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.overduedebts_id_seq OWNED BY nmsprime.overduedebts.id;



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


CREATE SEQUENCE nmsprime.parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.parameter_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.parameter_id_seq OWNED BY nmsprime.parameter.id;



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


CREATE SEQUENCE nmsprime.phonebookentry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonebookentry_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.phonebookentry_id_seq OWNED BY nmsprime.phonebookentry.id;



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


CREATE SEQUENCE nmsprime.phonenumber_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumber_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.phonenumber_id_seq OWNED BY nmsprime.phonenumber.id;



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


CREATE SEQUENCE nmsprime.phonenumbermanagement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonenumbermanagement_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.phonenumbermanagement_id_seq OWNED BY nmsprime.phonenumbermanagement.id;



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


CREATE SEQUENCE nmsprime.phonetariff_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.phonetariff_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.phonetariff_id_seq OWNED BY nmsprime.phonetariff.id;



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


CREATE SEQUENCE nmsprime.product_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.product_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.product_id_seq OWNED BY nmsprime.product.id;



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


ALTER TABLE nmsprime.provbase OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.provbase_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provbase_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.provbase_id_seq OWNED BY nmsprime.provbase.id;



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


CREATE SEQUENCE nmsprime.provmon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provmon_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.provmon_id_seq OWNED BY nmsprime.provmon.id;



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


CREATE SEQUENCE nmsprime.provvoip_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.provvoip_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.provvoip_id_seq OWNED BY nmsprime.provvoip.id;



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
    vlan_id integer DEFAULT 0,
    ont_line_profile_id bigint,
    service_profile_id bigint,
    gem_port bigint,
    traffic_table_in bigint,
    traffic_table_out bigint
);


ALTER TABLE nmsprime.qos OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.qos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.qos_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.qos_id_seq OWNED BY nmsprime.qos.id;



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


CREATE SEQUENCE nmsprime.realty_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.realty_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.realty_id_seq OWNED BY nmsprime.realty.id;



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


CREATE SEQUENCE nmsprime.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.roles_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.roles_id_seq OWNED BY nmsprime.roles.id;



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


CREATE SEQUENCE nmsprime.salesman_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.salesman_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.salesman_id_seq OWNED BY nmsprime.salesman.id;



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


CREATE SEQUENCE nmsprime.sepaaccount_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepaaccount_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.sepaaccount_id_seq OWNED BY nmsprime.sepaaccount.id;



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


CREATE SEQUENCE nmsprime.sepamandate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sepamandate_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.sepamandate_id_seq OWNED BY nmsprime.sepamandate.id;



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


CREATE SEQUENCE nmsprime.settlementrun_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.settlementrun_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.settlementrun_id_seq OWNED BY nmsprime.settlementrun.id;



CREATE TABLE nmsprime.sla (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    name character varying(191),
    license character varying(191)
);


ALTER TABLE nmsprime.sla OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.sla_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.sla_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.sla_id_seq OWNED BY nmsprime.sla.id;



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
    default_configfile_id bigint,
    default_qos_id bigint,
    default_mgmt_qos_id bigint
);


ALTER TABLE nmsprime.smartont OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.smartont_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.smartont_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.smartont_id_seq OWNED BY nmsprime.smartont.id;



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


CREATE SEQUENCE nmsprime.supportrequest_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.supportrequest_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.supportrequest_id_seq OWNED BY nmsprime.supportrequest.id;



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


CREATE SEQUENCE nmsprime.ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ticket_id_seq OWNED BY nmsprime.ticket.id;



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


CREATE SEQUENCE nmsprime.ticket_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ticket_type_id_seq OWNED BY nmsprime.ticket_type.id;



CREATE TABLE nmsprime.ticket_type_ticket (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    ticket_type_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_type_ticket OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.ticket_type_ticket_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_type_ticket_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ticket_type_ticket_id_seq OWNED BY nmsprime.ticket_type_ticket.id;



CREATE TABLE nmsprime.ticket_user (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    user_id bigint,
    ticket_id bigint
);


ALTER TABLE nmsprime.ticket_user OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.ticket_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticket_user_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ticket_user_id_seq OWNED BY nmsprime.ticket_user.id;



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


CREATE SEQUENCE nmsprime.ticketsystem_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.ticketsystem_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.ticketsystem_id_seq OWNED BY nmsprime.ticketsystem.id;



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


CREATE SEQUENCE nmsprime.trcclass_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.trcclass_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.trcclass_id_seq OWNED BY nmsprime.trcclass.id;



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


ALTER TABLE nmsprime.users OWNER TO nmsprime;


CREATE SEQUENCE nmsprime.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.users_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.users_id_seq OWNED BY nmsprime.users.id;



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


CREATE SEQUENCE nmsprime.websockets_statistics_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE nmsprime.websockets_statistics_entries_id_seq OWNER TO nmsprime;


ALTER SEQUENCE nmsprime.websockets_statistics_entries_id_seq OWNED BY nmsprime.websockets_statistics_entries.id;



ALTER TABLE ONLY nmsprime.abilities ALTER COLUMN id SET DEFAULT nextval('nmsprime.abilities_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.accountingrecord ALTER COLUMN id SET DEFAULT nextval('nmsprime.accountingrecord_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.apartment ALTER COLUMN id SET DEFAULT nextval('nmsprime.apartment_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.billingbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.billingbase_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.carriercode ALTER COLUMN id SET DEFAULT nextval('nmsprime.carriercode_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ccc ALTER COLUMN id SET DEFAULT nextval('nmsprime.ccc_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.comment ALTER COLUMN id SET DEFAULT nextval('nmsprime.comment_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.company ALTER COLUMN id SET DEFAULT nextval('nmsprime.company_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.configfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.configfile_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.contact ALTER COLUMN id SET DEFAULT nextval('nmsprime.contact_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.contract ALTER COLUMN id SET DEFAULT nextval('nmsprime.contract_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.costcenter ALTER COLUMN id SET DEFAULT nextval('nmsprime.costcenter_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.debt ALTER COLUMN id SET DEFAULT nextval('nmsprime.debt_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.dfsubscription ALTER COLUMN id SET DEFAULT nextval('nmsprime.dfsubscription_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.dfsubscriptionevent ALTER COLUMN id SET DEFAULT nextval('nmsprime.dfsubscriptionevent_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.domain ALTER COLUMN id SET DEFAULT nextval('nmsprime.domain_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ekpcode ALTER COLUMN id SET DEFAULT nextval('nmsprime.ekpcode_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.endpoint ALTER COLUMN id SET DEFAULT nextval('nmsprime.endpoint_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.enviacontract ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviacontract_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.enviaorder ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.enviaorder_phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorder_phonenumber_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.enviaorderdocument ALTER COLUMN id SET DEFAULT nextval('nmsprime.enviaorderdocument_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.failed_jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.failed_jobs_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.favorite_netelements ALTER COLUMN id SET DEFAULT nextval('nmsprime.favorite_netelements_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.global_config ALTER COLUMN id SET DEFAULT nextval('nmsprime.global_config_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.guilog ALTER COLUMN id SET DEFAULT nextval('nmsprime.guilog_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.hfcreq ALTER COLUMN id SET DEFAULT nextval('nmsprime.hfcreq_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.indices ALTER COLUMN id SET DEFAULT nextval('nmsprime.indices_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.invoice ALTER COLUMN id SET DEFAULT nextval('nmsprime.invoice_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ippool ALTER COLUMN id SET DEFAULT nextval('nmsprime.ippool_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.item ALTER COLUMN id SET DEFAULT nextval('nmsprime.item_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.jobs ALTER COLUMN id SET DEFAULT nextval('nmsprime.jobs_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.mibfile ALTER COLUMN id SET DEFAULT nextval('nmsprime.mibfile_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.migrations ALTER COLUMN id SET DEFAULT nextval('nmsprime.migrations_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.modem ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.modem_option ALTER COLUMN id SET DEFAULT nextval('nmsprime.modem_option_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.mpr ALTER COLUMN id SET DEFAULT nextval('nmsprime.mpr_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.mprgeopos ALTER COLUMN id SET DEFAULT nextval('nmsprime.mprgeopos_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.mta ALTER COLUMN id SET DEFAULT nextval('nmsprime.mta_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.netelement ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelement_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.netelementtype ALTER COLUMN id SET DEFAULT nextval('nmsprime.netelementtype_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.netgw ALTER COLUMN id SET DEFAULT nextval('nmsprime.netgw_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.node ALTER COLUMN id SET DEFAULT nextval('nmsprime.node_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.numberrange ALTER COLUMN id SET DEFAULT nextval('nmsprime.numberrange_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.oid ALTER COLUMN id SET DEFAULT nextval('nmsprime.oid_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.overduedebts ALTER COLUMN id SET DEFAULT nextval('nmsprime.overduedebts_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.parameter ALTER COLUMN id SET DEFAULT nextval('nmsprime.parameter_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.phonebookentry ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonebookentry_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.phonenumber ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumber_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.phonenumbermanagement ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonenumbermanagement_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.phonetariff ALTER COLUMN id SET DEFAULT nextval('nmsprime.phonetariff_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.product ALTER COLUMN id SET DEFAULT nextval('nmsprime.product_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.provbase ALTER COLUMN id SET DEFAULT nextval('nmsprime.provbase_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.provmon ALTER COLUMN id SET DEFAULT nextval('nmsprime.provmon_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.provvoip ALTER COLUMN id SET DEFAULT nextval('nmsprime.provvoip_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.qos ALTER COLUMN id SET DEFAULT nextval('nmsprime.qos_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.realty ALTER COLUMN id SET DEFAULT nextval('nmsprime.realty_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.roles ALTER COLUMN id SET DEFAULT nextval('nmsprime.roles_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.salesman ALTER COLUMN id SET DEFAULT nextval('nmsprime.salesman_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.sepaaccount ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepaaccount_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.sepamandate ALTER COLUMN id SET DEFAULT nextval('nmsprime.sepamandate_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.settlementrun ALTER COLUMN id SET DEFAULT nextval('nmsprime.settlementrun_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.sla ALTER COLUMN id SET DEFAULT nextval('nmsprime.sla_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.smartont ALTER COLUMN id SET DEFAULT nextval('nmsprime.smartont_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.supportrequest ALTER COLUMN id SET DEFAULT nextval('nmsprime.supportrequest_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ticket_type ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ticket_type_ticket ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_type_ticket_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ticket_user ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticket_user_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.ticketsystem ALTER COLUMN id SET DEFAULT nextval('nmsprime.ticketsystem_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.trcclass ALTER COLUMN id SET DEFAULT nextval('nmsprime.trcclass_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.users ALTER COLUMN id SET DEFAULT nextval('nmsprime.users_id_seq'::regclass);



ALTER TABLE ONLY nmsprime.websockets_statistics_entries ALTER COLUMN id SET DEFAULT nextval('nmsprime.websockets_statistics_entries_id_seq'::regclass);

