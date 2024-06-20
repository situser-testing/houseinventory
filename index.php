<?php
$inventoryFile = 'inventory.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inventory = json_decode(file_get_contents($inventoryFile), true);

    if (isset($_POST['itemName']) && isset($_POST['itemQuantity'])) {
        $itemName = ucfirst(strtolower(trim($_POST['itemName'])));
        $itemQuantity = intval($_POST['itemQuantity']);
        
        $existingItemIndex = array_search($itemName, array_column($inventory, 'name'));

        if ($existingItemIndex !== false) {
            $inventory[$existingItemIndex]['quantity'] += $itemQuantity;
        } else {
            $inventory[] = ['name' => $itemName, 'quantity' => $itemQuantity];
        }
    } elseif (isset($_POST['decrease']) && isset($_POST['index']) && isset($_POST['decreaseQuantity'])) {
        $index = intval($_POST['index']);
        $decreaseQuantity = intval($_POST['decreaseQuantity']);

        if ($inventory[$index]['quantity'] > $decreaseQuantity) {
            $inventory[$index]['quantity'] -= $decreaseQuantity;
        } else {
            array_splice($inventory, $index, 1);
        }
    }

    file_put_contents($inventoryFile, json_encode($inventory));
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Groceries Inventory</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        header {
            background-color: #007bff;
            color: white; /* Ensure header font color is white */
            text-align: center;
            padding: 1em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .container {
            margin: 2em auto;
            max-width: 800px;
            padding: 2em;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: black; /* Changed to black */
        }
        .input-form, .inventory-list {
            margin-bottom: 2em;
        }
        .input-form input, .search-input {
            width: calc(50% - 1em);
            padding: 0.5em;
            margin-right: 1em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .input-form input[type="number"] {
            width: calc(25% - 1em);
        }
        .input-form button {
            width: calc(25% - 1em);
        }
        .inventory-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .inventory-table th, .inventory-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .inventory-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .inventory-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 0.5em 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-decrease {
            background-color: #dc3545;
            color: white;
            padding: 0.3em 0.6em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-decrease:hover {
            background-color: #c82333;
        }
        .input-decrease {
            width: 60px;
            padding: 0.3em;
            margin-right: 0.5em;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-input {
            margin-bottom: 1em;
            padding: 0.5em;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
    </style>
</head>
<body>
    <header>
        <h1>House Groceries Inventory</h1>
    </header>
    <div class="container">
        <div class="input-form">
            <h2>Add a new item</h2>
            <form id="addItemForm" method="POST" action="">
                <input type="text" name="itemName" id="itemName" placeholder="Item Name" required>
                <input type="number" name="itemQuantity" id="itemQuantity" placeholder="Quantity" required>
                <button class="btn" type="submit">Add Item</button>
            </form>
        </div>
        <div class="inventory-list">
            <h2>Available Groceries</h2>
            <input type="text" id="searchInput" class="search-input" placeholder="Search for an item..." oninput="displayInventory()">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="inventory">
                    <?php
                    $inventory = json_decode(file_get_contents($inventoryFile), true);
                    if (!empty($inventory)) {
                        foreach ($inventory as $index => $item) {
                            echo "<tr>";
                            echo "<td>{$item['name']}</td>";
                            echo "<td>{$item['quantity']}</td>";
                            echo "<td>";
                            echo "<form method='POST' action='' style='display:inline;'>";
                            echo "<input type='number' name='decreaseQuantity' min='1' class='input-decrease' required>";
                            echo "<input type='hidden' name='index' value='{$index}'>";
                            echo "<button class='btn-decrease' type='submit' name='decrease'>Decrease</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function displayInventory() {
            const searchQuery = document.getElementById('searchInput').value.trim().toLowerCase();
            const inventoryTableBody = document.getElementById('inventory');
            const inventory = Array.from(inventoryTableBody.getElementsByTagName('tr'));

            inventory.forEach(row => {
                const itemName = row.getElementsByTagName('td')[0].textContent.toLowerCase();
                if (itemName.includes(searchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>