<?php
chdir(__DIR__ . '/../../..');

// Simple web integration test that logs in and posts a valid generate form
$base = 'http://localhost';
cookie = __DIR__ . '/.cookies_test';
@unlink($cookie);

// helper
function run_cmd($cmd) {
    $out = [];
    $rc = 0;
    exec($cmd, $out, $rc);
    return ["out" => implode("\n", $out), "rc" => $rc];
}

// 1) Get homepage and extract _glpi_csrf_token
$res = run_cmd("curl -s -c $cookie '$base/'");
$home = $res['out'];
if (!preg_match('/name="_glpi_csrf_token"\s+value="([^"]+)"/', $home, $m)) {
    echo "Failed to extract home CSRF token\n";
    exit(1);
}
$token = $m[1];

// 2) Login as testadmin / P@ssw0rd!
$login_cmd = "curl -s -L -c $cookie -b $cookie -X POST -F '_glpi_csrf_token=$token' -F 'login_name=testadmin' -F 'login_password=P@ssw0rd!' -F 'auth=local' '$base/front/login.php'";
$res = run_cmd($login_cmd);
if ($res['rc'] !== 0) {
    echo "Login curl failed\n";
    exit(1);
}

// 3) Request user form to get fresh CSRF token for authenticated session
$res = run_cmd("curl -s -b $cookie '$base/front/user.form.php?id=2'");
if (!preg_match('/name="_glpi_csrf_token"\s+value="([^"]+)"/', $res['out'], $m2)) {
    echo "Failed to extract userform CSRF token\n";
    exit(1);
}
$token2 = $m2[1];

// 4) Post generate form with a real computer id (1) assigned to user 2
$post_file = __DIR__ . '/.gen_post_body.html';
$post_cmd = "curl -s -b $cookie -D /tmp/gen_headers_test -o $post_file -X POST \
  -F '_glpi_csrf_token=$token2' \
  -F 'generate=1' \
  -F 'list=1' \
  -F 'user_id=2' \
  -F 'notes=web test auto' \
  -F 'owner=test' \
  -F 'author=testadmin' \
  -F 'number[]=0' \
  -F 'classes[]=Computer' \
  -F 'ids[]=1' \
  -F 'type_name[]=Computer' \
  -F 'man_name[]=TestMan' \
  -F 'mod_name[]=TestModel' \
  -F 'serial[]=12345' \
  -F 'otherserial[]=' \
  -F 'item_name[]=teste' \
  -F 'comments[]=automated' \
  '$base/plugins/protocolsmanager/front/generate.form.php'";
$res = run_cmd($post_cmd);
if ($res['rc'] !== 0) {
    echo "Generate POST failed\n";
    exit(1);
}

// 5) Check DB for last protocol and verify glpi_documents_items link exists
$db_check = "mysql -N -u glpi -p'YourStrongPassword' -e \"SELECT document_id FROM glpi_plugin_protocolsmanager_protocols ORDER BY id DESC LIMIT 1;\" glpi";
$res = run_cmd($db_check);
$doc_id = trim($res['out']);
if (empty($doc_id)) {
    echo "No protocol row found\n";
    exit(1);
}

$link_check = "mysql -N -u glpi -p'YourStrongPassword' -e \"SELECT COUNT(*) FROM glpi_documents_items WHERE documents_id = $doc_id AND items_id = 1;\" glpi";
$res = run_cmd($link_check);
$count = (int)trim($res['out']);
if ($count > 0) {
    echo "Test succeeded: document $doc_id linked to item 1 (count=$count)\n";
    exit(0);
} else {
    echo "Test failed: document $doc_id not linked to item 1\n";
    exit(2);
}
