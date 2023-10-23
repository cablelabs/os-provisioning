--
-- PostgreSQL database dump
--

-- Dumped from database version 13.4
-- Dumped by pg_dump version 13.4

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
-- Name: cccauthuser; Type: TABLE; Schema: public; Owner: nmsprime_ccc
--

CREATE TABLE public.cccauthuser (
    id bigint NOT NULL,
    created_at timestamp with time zone,
    updated_at timestamp with time zone,
    deleted_at timestamp with time zone,
    first_name character varying(191),
    last_name character varying(191),
    email character varying(191),
    login_name character varying(191) NOT NULL,
    password character varying(60) NOT NULL,
    description character varying(255),
    active boolean DEFAULT true NOT NULL,
    contract_id bigint NOT NULL,
    remember_token character varying(100),
    newsletter boolean DEFAULT false NOT NULL
);


ALTER TABLE public.cccauthuser OWNER TO nmsprime_ccc;

--
-- Name: cccauthuser_id_seq; Type: SEQUENCE; Schema: public; Owner: nmsprime_ccc
--

CREATE SEQUENCE public.cccauthuser_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.cccauthuser_id_seq OWNER TO nmsprime_ccc;

--
-- Name: cccauthuser_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: nmsprime_ccc
--

ALTER SEQUENCE public.cccauthuser_id_seq OWNED BY public.cccauthuser.id;


--
-- Name: cccauthuser id; Type: DEFAULT; Schema: public; Owner: nmsprime_ccc
--

ALTER TABLE ONLY public.cccauthuser ALTER COLUMN id SET DEFAULT nextval('public.cccauthuser_id_seq'::regclass);


--
-- Data for Name: cccauthuser; Type: TABLE DATA; Schema: public; Owner: nmsprime_ccc
--

COPY public.cccauthuser (id, created_at, updated_at, deleted_at, first_name, last_name, email, login_name, password, description, active, contract_id, remember_token, newsletter) FROM stdin;
\.


--
-- Name: cccauthuser_id_seq; Type: SEQUENCE SET; Schema: public; Owner: nmsprime_ccc
--

SELECT pg_catalog.setval('public.cccauthuser_id_seq', 1, false);


--
-- Name: cccauthuser idx_381572_primary; Type: CONSTRAINT; Schema: public; Owner: nmsprime_ccc
--

ALTER TABLE ONLY public.cccauthuser
    ADD CONSTRAINT idx_381572_primary PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

