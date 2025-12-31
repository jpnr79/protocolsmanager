<?php
// Test script to generate a protocol PDF for user id 2
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

// Build POST data expected by makeProtocol()
$_POST = [];
$_POST['number'] = []; // no items selected
$_POST['type_name'] = [];
$_POST['man_name'] = [];
$_POST['mod_name'] = [];
$_POST['serial'] = [];
$_POST['otherserial'] = [];
$_POST['item_name'] = [];
$_POST['comments'] = [];
$_POST['list'] = 1; // template id
$_POST['user_id'] = 2; // target user
$_POST['notes'] = 'Automated test generation';
$_POST['owner'] = 'glpi';
$_POST['author'] = 'glpi';

// Mark as generate action
$_REQUEST['generate'] = 1;

// Ensure DB is initialized in CLI context
global $DB, $CFG_GLPI;
if (empty($DB)) {
    require_once 'config/config_db.php';
    $DB = new DB();
}

// Define missing GLPI var constants if not present (CLI may not set them)
if (!defined('GLPI_VAR_DIR')) {
    define('GLPI_VAR_DIR', GLPI_ROOT . '/files');
}
if (!defined('GLPI_UPLOAD_DIR')) {
    define('GLPI_UPLOAD_DIR', GLPI_VAR_DIR . '/_uploads');
}
if (!defined('GLPI_PICTURE_DIR')) {
    define('GLPI_PICTURE_DIR', GLPI_VAR_DIR . '/_pictures');
}

// Ensure directories exist
@mkdir(GLPI_VAR_DIR, 0755, true);
@mkdir(GLPI_UPLOAD_DIR, 0755, true);
@mkdir(GLPI_PICTURE_DIR, 0755, true);

global $CFG_GLPI;
$CFG_GLPI['root_doc'] = '';
$CFG_GLPI['admin_email'] = 'admin@example.local';
$CFG_GLPI['admin_email_name'] = 'Admin';


// --- Manual generation sequence (avoids heavy GLPI internals) ---
// Fetch template from DB
$tplId = 1;
$req = $DB->request('glpi_plugin_protocolsmanager_config', ['id' => $tplId]);
if (!($row = $req->current())) {
    echo "Template not found\n";
    exit(1);
}

$content = nl2br($row["content"]);
$content = str_replace("{cur_date}", date("d.m.Y"), $content);
$content = str_replace("{owner}", $_POST['owner'], $content);
$content = str_replace("{admin}", $_POST['author'], $content);
$upper_content = nl2br($row["upper_content"]);
$upper_content = str_replace("{cur_date}", date("d.m.Y"), $upper_content);
$upper_content = str_replace("{owner}", $_POST['owner'], $upper_content);
$upper_content = str_replace("{admin}", $_POST['author'], $upper_content);
$footer = nl2br($row["footer"]);
$title = $row["title"];
$title = str_replace("{owner}", $_POST['owner'], $title);
$title_template = $row["name"];
$full_img_name = $row["logo"];
$font = $row["font"] ?: 'dejavusans';
$fontsize = $row["fontsize"] ?: '9';
$city = $row["city"] ?: '';
$orientation = $row["orientation"] ?: 'Portrait';
$breakword = $row['breakword'] ?? 0;
$islogo = !empty($full_img_name);
$prot_num = 1;
$serial_mode = $row['serial_mode'] ?? 1;
$comments = $_POST['comments'] ?? [];


// Prepare author/owner and include dompdf autoloader
$author = $_POST['author'];
$owner = $_POST['owner'];
$author_state = $row['author_state'] ?? 0;
require_once __DIR__ . '/../dompdf/vendor/autoload.php';

// Render template.php into HTML
ob_start();
include __DIR__ . '/../inc/template.php';
$file_content = ob_get_clean();

// Render PDF with Dompdf
$options = new \Dompdf\Options();
$options->set('defaultFont', $font);
$dompdf = new \Dompdf\Dompdf($options);
$dompdf->loadHtml($file_content);
$dompdf->setPaper('A4', $orientation);
$dompdf->render();

$doc_name = str_replace(' ', '_', $title) . "-" . date('dmY_His') . '.pdf';
$output = $dompdf->output();
$filePathFull = GLPI_UPLOAD_DIR . '/' . $doc_name;
file_put_contents($filePathFull, $output);

// Insert document record into glpi_documents
$sha1 = sha1_file($filePathFull);
$now = date('Y-m-d H:i:s');
$users_id = $_POST['user_id'];
$entity = 0;
$DB->insert('glpi_documents', [
    'entities_id' => $entity,
    'name' => $doc_name,
    'filename' => $doc_name,
    'filepath' => '_uploads',
    'documentcategories_id' => 0,
    'mime' => 'application/pdf',
    'date_mod' => $now,
    'comment' => $_POST['notes'] ?? '',
    'users_id' => $users_id,
    'sha1sum' => $sha1,
    'date_creation' => $now
]);
// Get last inserted id (max id)
$req2 = $DB->request([
    'SELECT' => \Glpi\DBAL\QueryFunction::max('id', 'max'),
    'FROM' => 'glpi_documents'
]);
$doc_row = $req2->current();
$doc_id = $doc_row['max'] ?? null;
if (!$doc_id) {
    echo "Failed to get inserted document id\n";
    exit(1);
}

// Link in protocols table
$DB->insert('glpi_plugin_protocolsmanager_protocols', [
    'name' => $doc_name,
    'gen_date' => $now,
    'author' => $_POST['author'],
    'user_id' => $users_id,
    'document_id' => $doc_id,
    'document_type' => $title_template
]);

// Link document to user
$DB->insert('glpi_documents_items', [
    'documents_id' => $doc_id,
    'items_id' => $users_id,
    'itemtype' => 'User',
    'users_id' => $users_id,
    'date_creation' => $now,
    'date_mod' => $now,
    'date' => $now
]);

echo "Generated document: $doc_name (id=$doc_id)\n";
