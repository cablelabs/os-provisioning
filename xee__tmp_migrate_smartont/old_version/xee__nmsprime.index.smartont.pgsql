\connect nmsprime

ALTER TABLE ONLY nmsprime.abilities
    ADD CONSTRAINT idx_59947_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.accountingrecord
    ADD CONSTRAINT idx_59957_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.apartment
    ADD CONSTRAINT idx_59963_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.billingbase
    ADD CONSTRAINT idx_59978_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.carriercode
    ADD CONSTRAINT idx_59987_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ccc
    ADD CONSTRAINT idx_59993_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.comment
    ADD CONSTRAINT idx_60003_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.company
    ADD CONSTRAINT idx_60012_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.configfile
    ADD CONSTRAINT idx_60021_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.contact
    ADD CONSTRAINT idx_60034_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.contract
    ADD CONSTRAINT idx_60043_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.costcenter
    ADD CONSTRAINT idx_60053_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.debt
    ADD CONSTRAINT idx_60062_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.dfsubscription
    ADD CONSTRAINT idx_60071_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.dfsubscriptionevent
    ADD CONSTRAINT idx_60080_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.domain
    ADD CONSTRAINT idx_60089_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ekpcode
    ADD CONSTRAINT idx_60095_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.endpoint
    ADD CONSTRAINT idx_60101_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviacontract
    ADD CONSTRAINT idx_60111_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorder
    ADD CONSTRAINT idx_60117_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorderdocument
    ADD CONSTRAINT idx_60126_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorder_phonenumber
    ADD CONSTRAINT idx_60132_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.failed_jobs
    ADD CONSTRAINT idx_60138_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.favorite_netelements
    ADD CONSTRAINT idx_60148_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.global_config
    ADD CONSTRAINT idx_60154_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.guilog
    ADD CONSTRAINT idx_60165_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.hfcreq
    ADD CONSTRAINT idx_60174_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.indices
    ADD CONSTRAINT idx_60183_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.invoice
    ADD CONSTRAINT idx_60192_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ippool
    ADD CONSTRAINT idx_60198_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.item
    ADD CONSTRAINT idx_60209_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.jobs
    ADD CONSTRAINT idx_60221_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mibfile
    ADD CONSTRAINT idx_60230_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.migrations
    ADD CONSTRAINT idx_60239_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.modem
    ADD CONSTRAINT idx_60245_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.modem_option
    ADD CONSTRAINT idx_60255_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mpr
    ADD CONSTRAINT idx_60261_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mprgeopos
    ADD CONSTRAINT idx_60270_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mta
    ADD CONSTRAINT idx_60276_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netelement
    ADD CONSTRAINT idx_60285_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netelementtype
    ADD CONSTRAINT idx_60294_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netgw
    ADD CONSTRAINT idx_60303_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.node
    ADD CONSTRAINT idx_60316_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.notifications
    ADD CONSTRAINT idx_60323_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.numberrange
    ADD CONSTRAINT idx_60331_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.oid
    ADD CONSTRAINT idx_60342_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.overduedebts
    ADD CONSTRAINT idx_60351_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.parameter
    ADD CONSTRAINT idx_60360_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonebookentry
    ADD CONSTRAINT idx_60375_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonenumber
    ADD CONSTRAINT idx_60384_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonenumbermanagement
    ADD CONSTRAINT idx_60395_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonetariff
    ADD CONSTRAINT idx_60408_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.product
    ADD CONSTRAINT idx_60418_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provbase
    ADD CONSTRAINT idx_60425_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provmon
    ADD CONSTRAINT idx_60438_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provvoip
    ADD CONSTRAINT idx_60444_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.qos
    ADD CONSTRAINT idx_60453_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.realty
    ADD CONSTRAINT idx_60464_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.roles
    ADD CONSTRAINT idx_60473_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.salesman
    ADD CONSTRAINT idx_60482_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sepaaccount
    ADD CONSTRAINT idx_60491_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sepamandate
    ADD CONSTRAINT idx_60500_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.settlementrun
    ADD CONSTRAINT idx_60509_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sla
    ADD CONSTRAINT idx_60515_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.smartont
    ADD CONSTRAINT idx_60521_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.supportrequest
    ADD CONSTRAINT idx_60530_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket
    ADD CONSTRAINT idx_60539_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticketsystem
    ADD CONSTRAINT idx_60548_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_type
    ADD CONSTRAINT idx_60557_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_type_ticket
    ADD CONSTRAINT idx_60563_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_user
    ADD CONSTRAINT idx_60569_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.trcclass
    ADD CONSTRAINT idx_60575_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.users
    ADD CONSTRAINT idx_60581_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.websockets_statistics_entries
    ADD CONSTRAINT idx_60593_primary PRIMARY KEY (id);



CREATE INDEX idx_59947_abilities_scope_index ON nmsprime.abilities USING btree (scope);



CREATE INDEX idx_59963_apartment_realty_id_foreign ON nmsprime.apartment USING btree (realty_id);



CREATE INDEX idx_59970_assigned_roles_entity_index ON nmsprime.assigned_roles USING btree (entity_id, entity_type, scope);



CREATE INDEX idx_59970_assigned_roles_role_id_index ON nmsprime.assigned_roles USING btree (role_id);



CREATE INDEX idx_59970_assigned_roles_scope_index ON nmsprime.assigned_roles USING btree (scope);



CREATE INDEX idx_59973_authreminders_email_index ON nmsprime.authreminders USING btree (email);



CREATE INDEX idx_59973_authreminders_token_index ON nmsprime.authreminders USING btree (token);



CREATE UNIQUE INDEX idx_59987_carriercode_carrier_code_unique ON nmsprime.carriercode USING btree (carrier_code);



CREATE INDEX idx_60021_configfile_parent_id_index ON nmsprime.configfile USING btree (parent_id);



CREATE INDEX idx_60043_contract_apartment_id_foreign ON nmsprime.contract USING btree (apartment_id);



CREATE INDEX idx_60043_contract_costcenter_id_index ON nmsprime.contract USING btree (costcenter_id);



CREATE INDEX idx_60043_contract_next_qos_id_index ON nmsprime.contract USING btree (next_qos_id);



CREATE INDEX idx_60043_contract_number_index ON nmsprime.contract USING btree (number);



CREATE INDEX idx_60043_contract_qos_id_index ON nmsprime.contract USING btree (qos_id);



CREATE INDEX idx_60043_contract_salesman_id_index ON nmsprime.contract USING btree (salesman_id);



CREATE UNIQUE INDEX idx_60095_ekpcode_ekp_code_unique ON nmsprime.ekpcode USING btree (ekp_code);



CREATE INDEX idx_60101_endpoint_modem_id_index ON nmsprime.endpoint USING btree (modem_id);



CREATE UNIQUE INDEX idx_60117_enviaorder_orderid_unique ON nmsprime.enviaorder USING btree (orderid);



CREATE INDEX idx_60148_favorite_netelements_user_id_netelement_id_index ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);



CREATE UNIQUE INDEX idx_60148_favorite_netelements_user_id_netelement_id_unique ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);



CREATE INDEX idx_60198_ippool_netgw_id_index ON nmsprime.ippool USING btree (netgw_id);



CREATE INDEX idx_60209_by_contract_id ON nmsprime.item USING btree (contract_id);



CREATE INDEX idx_60209_by_product_id ON nmsprime.item USING btree (product_id);



CREATE INDEX idx_60221_jobs_queue_reserved_at_index ON nmsprime.jobs USING btree (queue, reserved_at);



CREATE INDEX idx_60245_modem_configfile_id_index ON nmsprime.modem USING btree (configfile_id);



CREATE INDEX idx_60245_modem_contract_id_index ON nmsprime.modem USING btree (contract_id);



CREATE INDEX idx_60245_modem_netelement_id_index ON nmsprime.modem USING btree (netelement_id);



CREATE INDEX idx_60245_modem_qos_id_index ON nmsprime.modem USING btree (qos_id);



CREATE INDEX idx_60261_mpr_netelement_id_index ON nmsprime.mpr USING btree (netelement_id);



CREATE INDEX idx_60270_mprgeopos_mpr_id_index ON nmsprime.mprgeopos USING btree (mpr_id);



CREATE INDEX idx_60276_mta_configfile_id_index ON nmsprime.mta USING btree (configfile_id);



CREATE INDEX idx_60276_mta_modem_id_index ON nmsprime.mta USING btree (modem_id);



CREATE INDEX idx_60285_netelement_netelementtype_id_index ON nmsprime.netelement USING btree (netelementtype_id);



CREATE INDEX idx_60285_netelement_netgw_id_index ON nmsprime.netelement USING btree (netgw_id);



CREATE INDEX idx_60285_netelement_parent_id_index ON nmsprime.netelement USING btree (parent_id);



CREATE INDEX idx_60294_netelementtype_parent_id_index ON nmsprime.netelementtype USING btree (parent_id);



CREATE INDEX idx_60316_node_netelement_id_foreign ON nmsprime.node USING btree (netelement_id);



CREATE INDEX idx_60323_notifications_notifiable_type_notifiable_id_index ON nmsprime.notifications USING btree (notifiable_type, notifiable_id);



CREATE INDEX idx_60369_permissions_ability_id_index ON nmsprime.permissions USING btree (ability_id);



CREATE INDEX idx_60369_permissions_entity_index ON nmsprime.permissions USING btree (entity_id, entity_type, scope);



CREATE INDEX idx_60369_permissions_scope_index ON nmsprime.permissions USING btree (scope);



CREATE INDEX idx_60384_phonenumber_mta_id_index ON nmsprime.phonenumber USING btree (mta_id);



CREATE INDEX idx_60464_realty_node_id_foreign ON nmsprime.realty USING btree (node_id);



CREATE UNIQUE INDEX idx_60473_roles_name_unique ON nmsprime.roles USING btree (name, scope);



CREATE INDEX idx_60473_roles_scope_index ON nmsprime.roles USING btree (scope);



CREATE INDEX idx_60500_by_contract_id ON nmsprime.sepamandate USING btree (contract_id);



CREATE INDEX idx_60500_by_costcenter_id ON nmsprime.sepamandate USING btree (costcenter_id);



CREATE INDEX idx_60539_ticket_ticketable_type_ticketable_id_index ON nmsprime.ticket USING btree (ticketable_type, ticketable_id);



CREATE UNIQUE INDEX idx_60575_trc_id ON nmsprime.trcclass USING btree (trc_id);



CREATE UNIQUE INDEX idx_60575_trcclass_trc_id_unique ON nmsprime.trcclass USING btree (trc_id);



CREATE UNIQUE INDEX idx_60581_users_api_token_unique ON nmsprime.users USING btree (api_token);



CREATE TRIGGER on_update_current_timestamp BEFORE UPDATE ON nmsprime.authreminders FOR EACH ROW EXECUTE FUNCTION nmsprime.on_update_current_timestamp_authreminders();



ALTER TABLE ONLY nmsprime.assigned_roles
    ADD CONSTRAINT assigned_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES nmsprime.roles(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY nmsprime.permissions
    ADD CONSTRAINT permissions_ability_id_foreign FOREIGN KEY (ability_id) REFERENCES nmsprime.abilities(id) ON UPDATE CASCADE ON DELETE CASCADE;
