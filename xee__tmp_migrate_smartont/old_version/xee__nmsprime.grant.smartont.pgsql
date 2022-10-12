\connect nmsprime

GRANT ALL ON SCHEMA nmsprime TO nmsprime;
GRANT USAGE ON SCHEMA nmsprime TO grafana;



GRANT ALL ON TABLE nmsprime.abilities TO nmsprime;
GRANT SELECT ON TABLE nmsprime.abilities TO grafana;



GRANT ALL ON SEQUENCE nmsprime.abilities_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.accountingrecord TO nmsprime;
GRANT SELECT ON TABLE nmsprime.accountingrecord TO grafana;



GRANT ALL ON SEQUENCE nmsprime.accountingrecord_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.apartment TO nmsprime;
GRANT SELECT ON TABLE nmsprime.apartment TO grafana;



GRANT ALL ON SEQUENCE nmsprime.apartment_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.assigned_roles TO nmsprime;
GRANT SELECT ON TABLE nmsprime.assigned_roles TO grafana;



GRANT ALL ON TABLE nmsprime.authreminders TO nmsprime;
GRANT SELECT ON TABLE nmsprime.authreminders TO grafana;



GRANT ALL ON TABLE nmsprime.billingbase TO nmsprime;
GRANT SELECT ON TABLE nmsprime.billingbase TO grafana;



GRANT ALL ON SEQUENCE nmsprime.billingbase_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.carriercode TO nmsprime;
GRANT SELECT ON TABLE nmsprime.carriercode TO grafana;



GRANT ALL ON SEQUENCE nmsprime.carriercode_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ccc TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ccc TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ccc_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.comment TO nmsprime;
GRANT SELECT ON TABLE nmsprime.comment TO grafana;



GRANT ALL ON SEQUENCE nmsprime.comment_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.company TO nmsprime;
GRANT SELECT ON TABLE nmsprime.company TO grafana;



GRANT ALL ON SEQUENCE nmsprime.company_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.configfile TO nmsprime;
GRANT SELECT ON TABLE nmsprime.configfile TO grafana;



GRANT ALL ON SEQUENCE nmsprime.configfile_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.contact TO nmsprime;
GRANT SELECT ON TABLE nmsprime.contact TO grafana;



GRANT ALL ON SEQUENCE nmsprime.contact_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.contract TO nmsprime;
GRANT SELECT ON TABLE nmsprime.contract TO grafana;



GRANT ALL ON SEQUENCE nmsprime.contract_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.costcenter TO nmsprime;
GRANT SELECT ON TABLE nmsprime.costcenter TO grafana;



GRANT ALL ON SEQUENCE nmsprime.costcenter_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.debt TO nmsprime;
GRANT SELECT ON TABLE nmsprime.debt TO grafana;



GRANT ALL ON SEQUENCE nmsprime.debt_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.dfsubscription TO nmsprime;
GRANT SELECT ON TABLE nmsprime.dfsubscription TO grafana;



GRANT ALL ON SEQUENCE nmsprime.dfsubscription_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.dfsubscriptionevent TO nmsprime;
GRANT SELECT ON TABLE nmsprime.dfsubscriptionevent TO grafana;



GRANT ALL ON SEQUENCE nmsprime.dfsubscriptionevent_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.domain TO nmsprime;
GRANT SELECT ON TABLE nmsprime.domain TO grafana;



GRANT ALL ON SEQUENCE nmsprime.domain_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ekpcode TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ekpcode TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ekpcode_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.endpoint TO nmsprime;
GRANT SELECT ON TABLE nmsprime.endpoint TO grafana;



GRANT ALL ON SEQUENCE nmsprime.endpoint_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.enviacontract TO nmsprime;
GRANT SELECT ON TABLE nmsprime.enviacontract TO grafana;



GRANT ALL ON SEQUENCE nmsprime.enviacontract_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.enviaorder TO nmsprime;
GRANT SELECT ON TABLE nmsprime.enviaorder TO grafana;



GRANT ALL ON SEQUENCE nmsprime.enviaorder_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.enviaorder_phonenumber TO nmsprime;
GRANT SELECT ON TABLE nmsprime.enviaorder_phonenumber TO grafana;



GRANT ALL ON SEQUENCE nmsprime.enviaorder_phonenumber_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.enviaorderdocument TO nmsprime;
GRANT SELECT ON TABLE nmsprime.enviaorderdocument TO grafana;



GRANT ALL ON SEQUENCE nmsprime.enviaorderdocument_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.failed_jobs TO nmsprime;
GRANT SELECT ON TABLE nmsprime.failed_jobs TO grafana;



GRANT ALL ON SEQUENCE nmsprime.failed_jobs_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.favorite_netelements TO nmsprime;
GRANT SELECT ON TABLE nmsprime.favorite_netelements TO grafana;



GRANT ALL ON SEQUENCE nmsprime.favorite_netelements_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.global_config TO nmsprime;
GRANT SELECT ON TABLE nmsprime.global_config TO grafana;



GRANT ALL ON SEQUENCE nmsprime.global_config_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.guilog TO nmsprime;
GRANT SELECT ON TABLE nmsprime.guilog TO grafana;



GRANT ALL ON SEQUENCE nmsprime.guilog_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.hfcreq TO nmsprime;
GRANT SELECT ON TABLE nmsprime.hfcreq TO grafana;



GRANT ALL ON SEQUENCE nmsprime.hfcreq_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.indices TO nmsprime;
GRANT SELECT ON TABLE nmsprime.indices TO grafana;



GRANT ALL ON SEQUENCE nmsprime.indices_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.invoice TO nmsprime;
GRANT SELECT ON TABLE nmsprime.invoice TO grafana;



GRANT ALL ON SEQUENCE nmsprime.invoice_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ippool TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ippool TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ippool_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.item TO nmsprime;
GRANT SELECT ON TABLE nmsprime.item TO grafana;



GRANT ALL ON SEQUENCE nmsprime.item_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.jobs TO nmsprime;
GRANT SELECT ON TABLE nmsprime.jobs TO grafana;



GRANT ALL ON SEQUENCE nmsprime.jobs_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.mibfile TO nmsprime;
GRANT SELECT ON TABLE nmsprime.mibfile TO grafana;



GRANT ALL ON SEQUENCE nmsprime.mibfile_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.migrations TO nmsprime;
GRANT SELECT ON TABLE nmsprime.migrations TO grafana;



GRANT ALL ON SEQUENCE nmsprime.migrations_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.modem TO nmsprime;
GRANT SELECT ON TABLE nmsprime.modem TO grafana;



GRANT ALL ON SEQUENCE nmsprime.modem_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.modem_option TO nmsprime;
GRANT SELECT ON TABLE nmsprime.modem_option TO grafana;



GRANT ALL ON SEQUENCE nmsprime.modem_option_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.mpr TO nmsprime;
GRANT SELECT ON TABLE nmsprime.mpr TO grafana;



GRANT ALL ON SEQUENCE nmsprime.mpr_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.mprgeopos TO nmsprime;
GRANT SELECT ON TABLE nmsprime.mprgeopos TO grafana;



GRANT ALL ON SEQUENCE nmsprime.mprgeopos_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.mta TO nmsprime;
GRANT SELECT ON TABLE nmsprime.mta TO grafana;



GRANT ALL ON SEQUENCE nmsprime.mta_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.netelement TO nmsprime;
GRANT SELECT ON TABLE nmsprime.netelement TO grafana;



GRANT ALL ON SEQUENCE nmsprime.netelement_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.netelementtype TO nmsprime;
GRANT SELECT ON TABLE nmsprime.netelementtype TO grafana;



GRANT ALL ON SEQUENCE nmsprime.netelementtype_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.netgw TO nmsprime;
GRANT SELECT ON TABLE nmsprime.netgw TO grafana;



GRANT ALL ON SEQUENCE nmsprime.netgw_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.node TO nmsprime;
GRANT SELECT ON TABLE nmsprime.node TO grafana;



GRANT ALL ON SEQUENCE nmsprime.node_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.notifications TO nmsprime;
GRANT SELECT ON TABLE nmsprime.notifications TO grafana;



GRANT ALL ON TABLE nmsprime.numberrange TO nmsprime;
GRANT SELECT ON TABLE nmsprime.numberrange TO grafana;



GRANT ALL ON SEQUENCE nmsprime.numberrange_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.oid TO nmsprime;
GRANT SELECT ON TABLE nmsprime.oid TO grafana;



GRANT ALL ON SEQUENCE nmsprime.oid_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.overduedebts TO nmsprime;
GRANT SELECT ON TABLE nmsprime.overduedebts TO grafana;



GRANT ALL ON SEQUENCE nmsprime.overduedebts_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.parameter TO nmsprime;
GRANT SELECT ON TABLE nmsprime.parameter TO grafana;



GRANT ALL ON SEQUENCE nmsprime.parameter_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.permissions TO nmsprime;
GRANT SELECT ON TABLE nmsprime.permissions TO grafana;



GRANT ALL ON TABLE nmsprime.phonebookentry TO nmsprime;
GRANT SELECT ON TABLE nmsprime.phonebookentry TO grafana;



GRANT ALL ON SEQUENCE nmsprime.phonebookentry_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.phonenumber TO nmsprime;
GRANT SELECT ON TABLE nmsprime.phonenumber TO grafana;



GRANT ALL ON SEQUENCE nmsprime.phonenumber_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.phonenumbermanagement TO nmsprime;
GRANT SELECT ON TABLE nmsprime.phonenumbermanagement TO grafana;



GRANT ALL ON SEQUENCE nmsprime.phonenumbermanagement_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.phonetariff TO nmsprime;
GRANT SELECT ON TABLE nmsprime.phonetariff TO grafana;



GRANT ALL ON SEQUENCE nmsprime.phonetariff_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.product TO nmsprime;
GRANT SELECT ON TABLE nmsprime.product TO grafana;



GRANT ALL ON SEQUENCE nmsprime.product_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.provbase TO nmsprime;
GRANT SELECT ON TABLE nmsprime.provbase TO grafana;



GRANT ALL ON SEQUENCE nmsprime.provbase_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.provmon TO nmsprime;
GRANT SELECT ON TABLE nmsprime.provmon TO grafana;



GRANT ALL ON SEQUENCE nmsprime.provmon_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.provvoip TO nmsprime;
GRANT SELECT ON TABLE nmsprime.provvoip TO grafana;



GRANT ALL ON SEQUENCE nmsprime.provvoip_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.qos TO nmsprime;
GRANT SELECT ON TABLE nmsprime.qos TO grafana;



GRANT ALL ON SEQUENCE nmsprime.qos_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.realty TO nmsprime;
GRANT SELECT ON TABLE nmsprime.realty TO grafana;



GRANT ALL ON SEQUENCE nmsprime.realty_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.roles TO nmsprime;
GRANT SELECT ON TABLE nmsprime.roles TO grafana;



GRANT ALL ON SEQUENCE nmsprime.roles_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.salesman TO nmsprime;
GRANT SELECT ON TABLE nmsprime.salesman TO grafana;



GRANT ALL ON SEQUENCE nmsprime.salesman_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.sepaaccount TO nmsprime;
GRANT SELECT ON TABLE nmsprime.sepaaccount TO grafana;



GRANT ALL ON SEQUENCE nmsprime.sepaaccount_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.sepamandate TO nmsprime;
GRANT SELECT ON TABLE nmsprime.sepamandate TO grafana;



GRANT ALL ON SEQUENCE nmsprime.sepamandate_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.settlementrun TO nmsprime;
GRANT SELECT ON TABLE nmsprime.settlementrun TO grafana;



GRANT ALL ON SEQUENCE nmsprime.settlementrun_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.sla TO nmsprime;
GRANT SELECT ON TABLE nmsprime.sla TO grafana;



GRANT ALL ON SEQUENCE nmsprime.sla_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.smartont TO nmsprime;
GRANT SELECT ON TABLE nmsprime.smartont TO grafana;



GRANT ALL ON SEQUENCE nmsprime.smartont_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.supportrequest TO nmsprime;
GRANT SELECT ON TABLE nmsprime.supportrequest TO grafana;



GRANT ALL ON SEQUENCE nmsprime.supportrequest_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ticket TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ticket TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ticket_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ticket_type TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ticket_type TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ticket_type_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ticket_type_ticket TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ticket_type_ticket TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ticket_type_ticket_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ticket_user TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ticket_user TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ticket_user_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.ticketsystem TO nmsprime;
GRANT SELECT ON TABLE nmsprime.ticketsystem TO grafana;



GRANT ALL ON SEQUENCE nmsprime.ticketsystem_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.trcclass TO nmsprime;
GRANT SELECT ON TABLE nmsprime.trcclass TO grafana;



GRANT ALL ON SEQUENCE nmsprime.trcclass_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.users TO nmsprime;
GRANT SELECT ON TABLE nmsprime.users TO grafana;



GRANT ALL ON SEQUENCE nmsprime.users_id_seq TO nmsprime;



GRANT ALL ON TABLE nmsprime.websockets_statistics_entries TO nmsprime;
GRANT SELECT ON TABLE nmsprime.websockets_statistics_entries TO grafana;



GRANT ALL ON SEQUENCE nmsprime.websockets_statistics_entries_id_seq TO nmsprime;



