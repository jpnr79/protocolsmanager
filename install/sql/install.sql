DROP TABLE IF EXISTS `glpi_plugin_protocolsmanager_profiles`;
CREATE TABLE `glpi_plugin_protocolsmanager_profiles` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `profile_id` int unsigned,
    `plugin_conf` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `tab_access` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `make_access` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `delete_access` char(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `glpi_plugin_protocolsmanager_config`;
CREATE TABLE `glpi_plugin_protocolsmanager_config` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci,
    `font` varchar(255) COLLATE utf8mb4_unicode_ci,
    `fontsize` varchar(255) COLLATE utf8mb4_unicode_ci,
    `logo` varchar(255) COLLATE utf8mb4_unicode_ci,
    `logo_width` int,
    `logo_height` int,
    `content` text COLLATE utf8mb4_unicode_ci,
    `footer` text COLLATE utf8mb4_unicode_ci,
    `city` varchar(255) COLLATE utf8mb4_unicode_ci,
    `serial_mode` int,
    `column1` varchar(255) COLLATE utf8mb4_unicode_ci,
    `column2` varchar(255) COLLATE utf8mb4_unicode_ci,
    `orientation` varchar(10) COLLATE utf8mb4_unicode_ci,
    `breakword` int,
    `email_mode` int,
    `upper_content` text COLLATE utf8mb4_unicode_ci,
    `email_template` int,
    `author_name` varchar(255) COLLATE utf8mb4_unicode_ci,
    `author_state` int,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `glpi_plugin_protocolsmanager_config`
    (name, title, font, fontsize, content, footer, city, serial_mode, orientation, breakword, email_mode, author_name, author_state)
VALUES
    ('Equipment report',
     'Certificate of delivery of {owner}',
     'Roboto',
     '9',
     'User: \n I have read the terms of use of IT equipment in the Example Company.',
     'Example Company \n Example Street 21 \n 01-234 Example City',
     'Example city',
     1,
     'Portrait',
     1,
     2,
     'Test Division',
     1),
    ('Equipment report 2',
     'Certificate of delivery of {owner}',
     'Roboto',
     '9',
     'User: \n I have read the terms of use of IT equipment in the Example Company.',
     'Example Company \n Example Street 21 \n 01-234 Example City',
     'Example city',
     1,
     'Portrait',
     1,
     2,
     'Test Division',
     1);

DROP TABLE IF EXISTS `glpi_plugin_protocolsmanager_emailconfig`;
CREATE TABLE `glpi_plugin_protocolsmanager_emailconfig` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `tname` varchar(255) COLLATE utf8mb4_unicode_ci,
    `send_user` int,
    `email_content` text COLLATE utf8mb4_unicode_ci,
    `email_subject` varchar(255) COLLATE utf8mb4_unicode_ci,
    `email_footer` varchar(255) COLLATE utf8mb4_unicode_ci,
    `recipients` varchar(255) COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `glpi_plugin_protocolsmanager_emailconfig`
    (tname, send_user, email_content, email_subject, recipients)
VALUES
    ('Email default', 2, 'Testmail', 'Testmail', 'Testmail');

DROP TABLE IF EXISTS `glpi_plugin_protocolsmanager_protocols`;
CREATE TABLE `glpi_plugin_protocolsmanager_protocols` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) COLLATE utf8mb4_unicode_ci,
    `user_id` int unsigned,
    `gen_date` timestamp NULL DEFAULT NULL,
    `author` varchar(255) COLLATE utf8mb4_unicode_ci,
    `document_id` int unsigned,
    `document_type` varchar(255) COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
