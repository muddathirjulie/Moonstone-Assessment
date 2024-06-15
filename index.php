<?php
$servername = "localhost";
$username = "creedlarab2cadmin";
$password = "Wqn@9g8fATXJ4-Zvs4W!2Nmy";
$dbname = "creedliving.database.windows.net";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
// Fetch the tree data function fetch_tree_data($conn, $search = '') { $sql =
"SELECT * FROM atest"; if ($search != '') { $sql .= " WHERE name LIKE '%" .
$conn->real_escape_string($search) . "%'"; } $result = $conn->query($sql);
$items = []; while ($row = $result->fetch_assoc()) { $items[] = $row; } return
$items; } $search = isset($_POST['search']) ? $_POST['search'] : ''; $items =
fetch_tree_data($conn, $search); $conn->close(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Tree Structure</title>
    <style>
      ul {
        list-style-type: none;
      }
      li {
        margin-left: 20px;
      }
    </style>
  </head>
  <body>
    <h1>Tree Structure</h1>
    <form method="POST">
      <input
        type="text"
        name="search"
        placeholder="Search..."
        value="<?php echo htmlspecialchars($search); ?>"
      />
      <button type="submit">Filter</button>
    </form>
    <div id="tree"></div>

    <script>
      // Convert PHP array to JavaScript
      const items = <?php echo json_encode($items, JSON_HEX_TAG); ?>;

      function buildTree(items, parentId = null) {
        const root = [];
        for (let item of items) {
          if (item.pef_item_id === parentId) {
            const children = buildTree(items, item.id);
            if (children.length) {
              item.children = children;
            }
            root.push(item);
          }
        }
        return root;
      }

      function renderTree(tree, container) {
        const ul = document.createElement('ul');
        for (let node of tree) {
          const li = document.createElement('li');
          li.textContent = node.name;
          if (node.children) {
            renderTree(node.children, li);
          }
          ul.appendChild(li);
        }
        container.appendChild(ul);
      }

      const tree = buildTree(items);
      const treeContainer = document.getElementById('tree');
      renderTree(tree, treeContainer);
    </script>
  </body>
</html>
