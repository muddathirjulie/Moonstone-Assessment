const express = require('express');
const sql = require('mssql');
const path = require('path');

const app = express();
const port = process.env.PORT || 3000;

// Configuration for your SQL Server
const config = {
  user: 'creedlarab2cadmin',
  password: 'Wqn@9g8fATXJ4-Zvs4W!2Nmy',
  server: 'creedliving.database.windows.net', // You may need to change this to your SQL Server instance name
  database: 'creedLivingDB',
  options: {
    encrypt: true, // Use if you're on Azure
    enableArithAbort: true // Recommended setting
  }
};

// Function to connect to SQL Server
async function connectToDatabase() {
  try {
    await sql.connect(config);
    console.log('Connected to SQL Server');
  } catch (err) {
    console.error('Error connecting to SQL Server:', err);
  }
}

connectToDatabase();

// Serve static files (like HTML and client-side JS)
app.use(express.static(path.join(__dirname, 'public')));

// Route to fetch and format the data
app.get('/tree-data', async (req, res) => {
  try {
    const result = await sql.query`SELECT id, name, pef_item_id, order_no FROM dbo.atest`;
    const formattedData = formatData(result.recordset);
    res.json(formattedData);
  } catch (err) {
    console.error('Error fetching data:', err);
    res.status(500).send('Error fetching data');
  }
});

// Function to format data into hierarchical structure
function formatData(data) {
  const map = new Map();
  let root;

  data.forEach(item => {
    map.set(item.id, { ...item, children: [] });
  });

  data.forEach(item => {
    const node = map.get(item.id);
    if (item.pef_item_id !== null) {
      const parent = map.get(item.pef_item_id);
      if (parent) {
        parent.children.push(node);
      }
    } else {
      root = node;
    }
  });

  return root;
}

// Default route to serve index.html
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// Start the server
app.listen(port, () => {
  console.log(`Server running at http://localhost:${port}`);
});
