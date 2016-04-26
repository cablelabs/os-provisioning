
/***********************************************************************/
/* Table global_config */
/***********************************************************************/
TRUNCATE global_config;
INSERT INTO `global_config` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `street`, `city`, `phone`, `mail`, `log_level`, `headline1`, `headline2`) VALUES (1, NULL, NULL, NULL, '', '', '', '', '', 1, 'Das Monster', 'The next Generation NMS');


/***********************************************************************/
/* Table phonetariff */
/***********************************************************************/
TRUNCATE phonetariff;
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), '1488', 'ERZNET Marienberg (Standard-Tarif)', 'purchase', 'ERZNET Marienberg (Standard-Tarif)', 0);
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), '1489', 'ERZNET Marienberg (Festnetzflatrate)', 'purchase', 'ERZNET Marienberg (Festnetzflatrate)', 0);
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), '1490', 'ERZNET Marienberg SIP (Standard-Tarif)', 'purchase', 'ERZNET Marienberg SIP (Standard-Tarif)', 1);
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), '1491', 'ERZNET Marienberg SIP (Festnetzflatrate)', 'purchase', 'ERZNET Marienberg SIP (Festnetzflatrate)', 1);
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), 'VOIP0413_ERZ_standard', 'Telefonie Basic', 'sale', 'Telefonie Basic', 1);
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), 'VOIP0413_ERZ_flat', 'Telefonie Flat', 'sale', 'Telefonie Flat', 1);
