<?php

/**
 * Install the plugin
 */
function plugin_protocolsmanager_install(): bool
{
    global $DB;
    
    $version = plugin_version_protocolsmanager();
    
    // Use SQL file for installation
    $sqlFile = __DIR__ . '/install/sql/install.sql';
    if (file_exists($sqlFile)) {
        $DB->runFile($sqlFile);
    }
    
    return true;
}

/**
 * Uninstall the plugin
 */
function plugin_protocolsmanager_uninstall(): bool
{
    global $DB;
    
    $tables = [
        'glpi_plugin_protocolsmanager_protocols',
        'glpi_plugin_protocolsmanager_config',
        'glpi_plugin_protocolsmanager_profiles',
        'glpi_plugin_protocolsmanager_emailconfig'
    ];

    foreach ($tables as $table) {
        if ($DB->tableExists($table)) {
            $DB->query("DROP TABLE IF EXISTS `$table`");
        }
    }

    return true;
}

/**
 * Safe loader to prevent early table access
 */
function plugin_protocolsmanager_getRights(?int $profile_id = null)
{
    global $DB;
    if (!$DB->tableExists('glpi_plugin_protocolsmanager_profiles')) {
        return []; // Avoid query before installation
    }

    return $DB->request([
        'FROM'  => 'glpi_plugin_protocolsmanager_profiles',
        'WHERE' => ['profile_id' => $profile_id ?? 0]
    ])->current();
}