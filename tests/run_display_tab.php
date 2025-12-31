<?php
// Test script to call displayTabContentForItem for user id 2
chdir(__DIR__ . '/../../..'); // change to GLPI root
require_once 'vendor/autoload.php';
require_once 'src/autoload/constants.php';
require_once 'inc/includes.php';

// Start session and set necessary session vars
session_start();
$_SESSION["glpiID"] = 2; // admin user
$_SESSION['glpiactiveprofile']['id'] = 4; // profile id for user 2
$_SESSION['glpiactive_entity'] = 0;
$_SESSION['valid_id'] = session_id();

// Ensure DB is initialized in CLI context
global $DB, $CFG_GLPI;
if (empty($DB)) {
    require_once 'config/config_db.php';
    $DB = new DB();
}

// Define GLPI plugins directories constant for CLI tests
if (!defined('GLPI_PLUGINS_DIRECTORIES')) {
    define('GLPI_PLUGINS_DIRECTORIES', [GLPI_ROOT . '/plugins']);
}

// Load a User instance
$user_id = 2;
$user = new User();
if (!$user->getFromDB($user_id)) {
    echo "User $user_id not found\n";
    exit(1);
}

// Call the plugin tab display
require_once __DIR__ . '/../inc/generate.class.php';
PluginProtocolsmanagerGenerate::displayTabContentForItem($user);

echo "\nDisplayTabContentForItem executed\n";
