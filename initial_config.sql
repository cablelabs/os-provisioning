
/***********************************************************************/
/* Table global_config */
/***********************************************************************/
TRUNCATE global_config;
INSERT INTO `global_config` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `street`, `city`, `phone`, `mail`, `log_level`, `headline1`, `headline2`) VALUES (1, NULL, NULL, NULL, '', '', '', '', '', 1, 'Das Monster', 'The next Generation NMS');


/***********************************************************************/
/* Table phonetariff */
/***********************************************************************/
TRUNCATE phonetariff;
INSERT INTO phonetariff (created_at, external_identifier, name, type, description, usable) VALUES (NOW(), '1489', 'ERZNET Marienberg (Festnetzflatrate)', 1, 'ERZNET Marienberg (Festnetzflatrate)', 0);
