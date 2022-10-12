\connect nmsprime

ALTER TABLE ONLY nmsprime.abilities
    ADD CONSTRAINT idx_17263_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.accountingrecord
    ADD CONSTRAINT idx_17273_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.apartment
    ADD CONSTRAINT idx_17279_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.billingbase
    ADD CONSTRAINT idx_17294_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.carriercode
    ADD CONSTRAINT idx_17303_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ccc
    ADD CONSTRAINT idx_17309_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.comment
    ADD CONSTRAINT idx_17319_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.company
    ADD CONSTRAINT idx_17328_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.configfile
    ADD CONSTRAINT idx_17337_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.contact
    ADD CONSTRAINT idx_17349_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.contract
    ADD CONSTRAINT idx_17358_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.costcenter
    ADD CONSTRAINT idx_17367_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.debt
    ADD CONSTRAINT idx_17376_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.domain
    ADD CONSTRAINT idx_17385_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ekpcode
    ADD CONSTRAINT idx_17391_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.endpoint
    ADD CONSTRAINT idx_17397_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviacontract
    ADD CONSTRAINT idx_17407_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorder
    ADD CONSTRAINT idx_17413_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorderdocument
    ADD CONSTRAINT idx_17422_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.enviaorder_phonenumber
    ADD CONSTRAINT idx_17428_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.failed_jobs
    ADD CONSTRAINT idx_17434_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.favorite_netelements
    ADD CONSTRAINT idx_17444_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.global_config
    ADD CONSTRAINT idx_17450_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.guilog
    ADD CONSTRAINT idx_17461_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.hfcreq
    ADD CONSTRAINT idx_17470_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.indices
    ADD CONSTRAINT idx_17479_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.invoice
    ADD CONSTRAINT idx_17488_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ippool
    ADD CONSTRAINT idx_17494_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.item
    ADD CONSTRAINT idx_17505_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.jobs
    ADD CONSTRAINT idx_17517_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mibfile
    ADD CONSTRAINT idx_17526_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.migrations
    ADD CONSTRAINT idx_17535_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.modem
    ADD CONSTRAINT idx_17541_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.modem_option
    ADD CONSTRAINT idx_17551_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mpr
    ADD CONSTRAINT idx_17557_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mprgeopos
    ADD CONSTRAINT idx_17566_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.mta
    ADD CONSTRAINT idx_17575_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netelement
    ADD CONSTRAINT idx_17596_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netelementtype
    ADD CONSTRAINT idx_17605_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.netgw
    ADD CONSTRAINT idx_17614_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.node
    ADD CONSTRAINT idx_17626_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.notifications
    ADD CONSTRAINT idx_17633_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.numberrange
    ADD CONSTRAINT idx_17641_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.oid
    ADD CONSTRAINT idx_17652_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.overduedebts
    ADD CONSTRAINT idx_17661_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.parameter
    ADD CONSTRAINT idx_17670_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonebookentry
    ADD CONSTRAINT idx_17685_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonenumber
    ADD CONSTRAINT idx_17694_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonenumbermanagement
    ADD CONSTRAINT idx_17705_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.phonetariff
    ADD CONSTRAINT idx_17718_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.product
    ADD CONSTRAINT idx_17728_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provbase
    ADD CONSTRAINT idx_17735_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provmon
    ADD CONSTRAINT idx_17748_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.provvoip
    ADD CONSTRAINT idx_17754_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.qos
    ADD CONSTRAINT idx_17763_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.realty
    ADD CONSTRAINT idx_17858_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.roles
    ADD CONSTRAINT idx_17867_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.salesman
    ADD CONSTRAINT idx_17876_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sepaaccount
    ADD CONSTRAINT idx_17885_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sepamandate
    ADD CONSTRAINT idx_17894_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.settlementrun
    ADD CONSTRAINT idx_17903_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.sla
    ADD CONSTRAINT idx_17909_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.supportrequest
    ADD CONSTRAINT idx_17915_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket
    ADD CONSTRAINT idx_17924_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticketsystem
    ADD CONSTRAINT idx_17933_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_type
    ADD CONSTRAINT idx_17942_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_type_ticket
    ADD CONSTRAINT idx_17948_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.ticket_user
    ADD CONSTRAINT idx_17954_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.trcclass
    ADD CONSTRAINT idx_17960_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.users
    ADD CONSTRAINT idx_17966_primary PRIMARY KEY (id);



ALTER TABLE ONLY nmsprime.websockets_statistics_entries
    ADD CONSTRAINT idx_17978_primary PRIMARY KEY (id);



CREATE INDEX idx_17263_abilities_scope_index ON nmsprime.abilities USING btree (scope);



CREATE INDEX idx_17279_apartment_realty_id_foreign ON nmsprime.apartment USING btree (realty_id);



CREATE INDEX idx_17286_assigned_roles_entity_index ON nmsprime.assigned_roles USING btree (entity_id, entity_type, scope);



CREATE INDEX idx_17286_assigned_roles_role_id_index ON nmsprime.assigned_roles USING btree (role_id);



CREATE INDEX idx_17286_assigned_roles_scope_index ON nmsprime.assigned_roles USING btree (scope);



CREATE INDEX idx_17289_authreminders_email_index ON nmsprime.authreminders USING btree (email);



CREATE INDEX idx_17289_authreminders_token_index ON nmsprime.authreminders USING btree (token);



CREATE UNIQUE INDEX idx_17303_carriercode_carrier_code_unique ON nmsprime.carriercode USING btree (carrier_code);



CREATE INDEX idx_17337_configfile_parent_id_index ON nmsprime.configfile USING btree (parent_id);



CREATE INDEX idx_17358_contract_apartment_id_foreign ON nmsprime.contract USING btree (apartment_id);



CREATE INDEX idx_17358_contract_costcenter_id_index ON nmsprime.contract USING btree (costcenter_id);



CREATE INDEX idx_17358_contract_next_qos_id_index ON nmsprime.contract USING btree (next_qos_id);



CREATE INDEX idx_17358_contract_number_index ON nmsprime.contract USING btree (number);



CREATE INDEX idx_17358_contract_qos_id_index ON nmsprime.contract USING btree (qos_id);



CREATE INDEX idx_17358_contract_salesman_id_index ON nmsprime.contract USING btree (salesman_id);



CREATE UNIQUE INDEX idx_17391_ekpcode_ekp_code_unique ON nmsprime.ekpcode USING btree (ekp_code);



CREATE INDEX idx_17397_endpoint_modem_id_index ON nmsprime.endpoint USING btree (modem_id);



CREATE UNIQUE INDEX idx_17413_enviaorder_orderid_unique ON nmsprime.enviaorder USING btree (orderid);



CREATE INDEX idx_17444_favorite_netelements_user_id_netelement_id_index ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);



CREATE UNIQUE INDEX idx_17444_favorite_netelements_user_id_netelement_id_unique ON nmsprime.favorite_netelements USING btree (user_id, netelement_id);



CREATE INDEX idx_17494_ippool_netgw_id_index ON nmsprime.ippool USING btree (netgw_id);



CREATE INDEX idx_17505_by_contract_id ON nmsprime.item USING btree (contract_id);



CREATE INDEX idx_17505_by_product_id ON nmsprime.item USING btree (product_id);



CREATE INDEX idx_17517_jobs_queue_reserved_at_index ON nmsprime.jobs USING btree (queue, reserved_at);



CREATE INDEX idx_17541_modem_configfile_id_index ON nmsprime.modem USING btree (configfile_id);



CREATE INDEX idx_17541_modem_contract_id_index ON nmsprime.modem USING btree (contract_id);



CREATE INDEX idx_17541_modem_netelement_id_index ON nmsprime.modem USING btree (netelement_id);



CREATE INDEX idx_17541_modem_qos_id_index ON nmsprime.modem USING btree (qos_id);



CREATE INDEX idx_17557_mpr_netelement_id_index ON nmsprime.mpr USING btree (netelement_id);



CREATE INDEX idx_17566_mprgeopos_mpr_id_index ON nmsprime.mprgeopos USING btree (mpr_id);



CREATE INDEX idx_17575_mta_configfile_id_index ON nmsprime.mta USING btree (configfile_id);



CREATE INDEX idx_17575_mta_modem_id_index ON nmsprime.mta USING btree (modem_id);



CREATE INDEX idx_17596_netelement_netelementtype_id_index ON nmsprime.netelement USING btree (netelementtype_id);



CREATE INDEX idx_17596_netelement_netgw_id_index ON nmsprime.netelement USING btree (netgw_id);



CREATE INDEX idx_17596_netelement_parent_id_index ON nmsprime.netelement USING btree (parent_id);



CREATE INDEX idx_17605_netelementtype_parent_id_index ON nmsprime.netelementtype USING btree (parent_id);



CREATE INDEX idx_17626_node_netelement_id_foreign ON nmsprime.node USING btree (netelement_id);



CREATE INDEX idx_17633_notifications_notifiable_type_notifiable_id_index ON nmsprime.notifications USING btree (notifiable_type, notifiable_id);



CREATE INDEX idx_17679_permissions_ability_id_index ON nmsprime.permissions USING btree (ability_id);



CREATE INDEX idx_17679_permissions_entity_index ON nmsprime.permissions USING btree (entity_id, entity_type, scope);



CREATE INDEX idx_17679_permissions_scope_index ON nmsprime.permissions USING btree (scope);



CREATE INDEX idx_17694_phonenumber_mta_id_index ON nmsprime.phonenumber USING btree (mta_id);



CREATE INDEX idx_17858_realty_node_id_foreign ON nmsprime.realty USING btree (node_id);



CREATE UNIQUE INDEX idx_17867_roles_name_unique ON nmsprime.roles USING btree (name, scope);



CREATE INDEX idx_17867_roles_scope_index ON nmsprime.roles USING btree (scope);



CREATE INDEX idx_17894_by_contract_id ON nmsprime.sepamandate USING btree (contract_id);



CREATE INDEX idx_17894_by_costcenter_id ON nmsprime.sepamandate USING btree (costcenter_id);



CREATE INDEX idx_17924_ticket_ticketable_type_ticketable_id_index ON nmsprime.ticket USING btree (ticketable_type, ticketable_id);



CREATE UNIQUE INDEX idx_17960_trc_id ON nmsprime.trcclass USING btree (trc_id);



CREATE UNIQUE INDEX idx_17960_trcclass_trc_id_unique ON nmsprime.trcclass USING btree (trc_id);



CREATE UNIQUE INDEX idx_17966_users_api_token_unique ON nmsprime.users USING btree (api_token);



CREATE TRIGGER on_update_current_timestamp BEFORE UPDATE ON nmsprime.authreminders FOR EACH ROW EXECUTE FUNCTION nmsprime.on_update_current_timestamp_authreminders();



ALTER TABLE ONLY nmsprime.assigned_roles
    ADD CONSTRAINT assigned_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES nmsprime.roles(id) ON UPDATE CASCADE ON DELETE CASCADE;



ALTER TABLE ONLY nmsprime.permissions
    ADD CONSTRAINT permissions_ability_id_foreign FOREIGN KEY (ability_id) REFERENCES nmsprime.abilities(id) ON UPDATE CASCADE ON DELETE CASCADE;
