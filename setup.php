<?php

// Plugin version info
function plugin_version_protocolsmanager(): array
{
    return [
        'name'         => __('Protocols manager', 'protocolsmanager'),
        'version'      => '1.5.6.2',
        'author'       => 'Mikail',
        'license'      => 'GPLv3+',
        'homepage'     => 'https://github.com/CanMik/protocolsmanager',
        'requirements' => [
            'glpi' => [
                'min' => '10.0.0',
                'max' => '11.0.0'
            ],
            'php'  => [
                'min' => '7.4'
            ]
        ]
    ];
}

// Config check
function plugin_protocolsmanager_check_config(): bool
{
    return true;
}

// Prerequisites check
function plugin_protocolsmanager_check_prerequisites(): bool
{
    if (version_compare(GLPI_VERSION, '10.0.0', '<') || version_compare(GLPI_VERSION, '11.0.0', '>')) {
        if (method_exists('Plugin', 'messageIncompatible')) {
            Plugin::messageIncompatible('core', '10.0.0', '11.0.0');
        } else {
            echo __('This plugin requires GLPI >= 10.0.0 and < 11.0.0', 'protocolsmanager');
        }
        return false;
    }
    return true;
}

// Init plugin hooks
function plugin_init_protocolsmanager(): void
{
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;
    $PLUGIN_HOOKS['add_css']['protocolsmanager']        = 'css/styles.css';

    // Register tabs for supported item types
    $tabTargets = [
        'User', 'Printer', 'Peripheral', 'Computer',
        'Phone', 'Line', 'Monitor'
    ];
    foreach ($tabTargets as $target) {
        Plugin::registerClass('PluginProtocolsmanagerGenerate', ['addtabon' => [$target]]);
    }

    Plugin::registerClass('PluginProtocolsmanagerProfile', ['addtabon' => ['Profile']]);
    Plugin::registerClass('PluginProtocolsmanagerConfig',  ['addtabon' => ['Config']]);

    // Config page & menu need to relog to have access to menu
    if (PluginProtocolsmanagerProfile::currentUserHasRight('plugin_conf')) {
        $PLUGIN_HOOKS['menu_toadd']['protocolsmanager'] = ['config' => 'PluginProtocolsmanagerMenu'];
        $PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
    }

}