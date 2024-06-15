<?php
// Database connection details
$serverName = "creedliving.database.windows.net";
$username = "creedlarab2cadmin";
$password = "Wqn@9g8fATXJ4-Zvs4W!2Nmy";
$database = "creedLivingDB";

// Establishing connection
$conn = sqlsrv_connect($serverName, array(
    "UID" => $username,
    "PWD" => $password,
    "Database" => $database,
    "CharacterSet" => "UTF-8"
));

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Function to recursively build tree
function buildTree($parentId = null, $conn) {
    $tree = array();
    $sql = "SELECT id, name, pef_item_id, order_no FROM atest WHERE pef_item_id = ? ORDER BY order_no";
    $params = array($parentId);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $row['children'] = buildTree($row['id'], $conn);
        $tree[] = $row;
    }
    
    return $tree;
}

// Fetch the root nodes (where pef_item_id is NULL)
$treeData = buildTree(null, $conn);

// Function to recursively format tree as nested unordered list (UL)
function formatTree($tree) {
    $html = '<ul>';
    foreach ($tree as $node) {
        $html .= '<li>' . htmlspecialchars($node['name']);
        if (!empty($node['children'])) {
            $html .= formatTree($node['children']);
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

// Output the formatted tree structure
echo formatTree($treeData);

// Close the connection
sqlsrv_close($conn);
?>
