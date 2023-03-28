<?php

// Get the name and the version of the plugin - Needed
function plugin_version_protocolsmanager()
{

        return [
                'name' => "Protocols manager",
                'version'        => '1.5.3.4',
                'author'         => 'Mikail',
                'license'        => 'GPLv3+',
                'homepage'       => 'https://github.com/CanMik/protocolsmanager',
                'requirements' => [
                        'glpi' => [
                                'min' => '10.0.0',
                                'max' => '11.0.0'
                        ],
                        'php' => [
                                'min' => '7.4'
                        ]
                ]
        ];
}

// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_protocolsmanager_check_config()
{
        return true;
}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_protocolsmanager_check_prerequisites()
{
        if (version_compare(GLPI_VERSION, '10.0.0', 'lt') || version_compare(GLPI_VERSION, '11.0.0', 'gt')) {
                if (method_exists('Plugin', 'messageIncompatible')) {
                        //since GLPI 9.2
                        Plugin::messageIncompatible('core', '10.0.0', '11.0.0');
                } else {
                        echo "This plugin requires GLPI >= 10.0.0 and < 11.0.0";
                }
                return false;
        }
        return true;
}

// Init the hooks of the plugins -Needed
function plugin_init_protocolsmanager()
{
        global $PLUGIN_HOOKS;

        $PLUGIN_HOOKS['csrf_compliant']['protocolsmanager'] = true;

        $PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';

        Plugin::registerClass('PluginProtocolsmanagerGenerate', array('addtabon' => array('User')));

        Plugin::registerClass('PluginProtocolsmanagerProfile', array('addtabon' => array('Profile')));

        Plugin::registerClass('PluginProtocolsmanagerConfig', array('addtabon' => array('Config')));

        $PLUGIN_HOOKS['add_css']['protocolsmanager'] = 'css/styles.css';
        if (Session::haveRight("config", READ)) {
                $PLUGIN_HOOKS["menu_toadd"]['protocolsmanager'] = ['config' => 'PluginProtocolsmanagerMenu'];
        }

        if (Session::haveRight('config', READ)) {
                $PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
        }

        // if (Session::getLoginUserID()) {

        //         // Config page
        //         if (Session::haveRight("config", READ) || Session::haveRight("config", UPDATE)) {
        //                 $PLUGIN_HOOKS['config_page']['protocolsmanager'] = 'front/config.form.php';
        //         }
        //         // Menu page
        //         if (Session::haveRight("config", READ)) {
        //                 $PLUGIN_HOOKS["menu_toadd"]['protocolsmanager'] = ['config' => 'PluginProtocolsmanagerMenu'];
        //         }
        // }
}
