<?php
// ============================================================
// Database Connection to XAMPP MySQL
// ============================================================
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "laravel_crud";

// Create database & table if not exists automatically
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE IF NOT EXISTS `$db`");
$conn->select_db($db);
$conn->query("CREATE TABLE IF NOT EXISTS `items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$message = "";

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = $_POST['name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $price = floatval($_POST['price'] ?? 0);

        $stmt = $conn->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $desc, $price);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?msg=created");
        exit;
    }

    if ($action === 'update') {
        $id = intval($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $desc = $_POST['description'] ?? '';
        $price = floatval($_POST['price'] ?? 0);

        $stmt = $conn->prepare("UPDATE items SET name=?, description=?, price=? WHERE id=?");
        $stmt->bind_param("ssdi", $name, $desc, $price, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?msg=updated");
        exit;
    }

    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM items WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?msg=deleted");
        exit;
    }
}

// Fetch all items
$result = $conn->query("SELECT * FROM items ORDER BY id DESC");
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

// Check edit mode
$editItem = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ItemManager - XAMPP MySQL CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-800 font-sans min-h-screen flex flex-col justify-between">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 text-xl font-bold text-indigo-600 hover:text-indigo-700 transition">
                <i class="fa-solid fa-boxes-stacked text-2xl"></i>
                <span>ItemManager</span>
            </a>
            <a href="index.php?action=create" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <i class="fa-solid fa-plus"></i>
                <span>Add Item</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 w-full">
        <!-- Flash Alert -->
        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 flex items-center justify-between p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <p class="text-sm font-medium">
                        <?php 
                            if ($_GET['msg'] === 'created') echo "Item created and saved to XAMPP database!";
                            if ($_GET['msg'] === 'updated') echo "Item updated successfully in XAMPP database!";
                            if ($_GET['msg'] === 'deleted') echo "Item removed from XAMPP database!";
                        ?>
                    </p>
                </div>
                <a href="index.php" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($isCreate || $editItem): ?>
            <!-- FORM VIEW (CREATE / EDIT) -->
            <div class="max-w-xl mx-auto">
                <a href="index.php" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 mb-4 transition font-medium">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to items list</span>
                </a>

                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="mb-6">
                        <h1 class="text-xl font-bold text-gray-900"><?= $editItem ? 'Edit Item #' . $editItem['id'] : 'Add New Item' ?></h1>
                        <p class="text-xs text-gray-500 mt-1">Directly saves to XAMPP MySQL database (<code>laravel_crud</code> table <code>items</code>).</p>
                    </div>

                    <form action="index.php" method="POST" class="space-y-5">
                        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
                        <?php if ($editItem): ?>
                            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
                        <?php endif; ?>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Item Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Wireless Mouse"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Price ($)</label>
                            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($editItem['price'] ?? '') ?>" placeholder="0.00"
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">Description</label>
                            <textarea name="description" rows="4" placeholder="Briefly describe this item..."
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition" required><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <a href="index.php" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                                Cancel
                            </a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition inline-flex items-center gap-2">
                                <i class="fa-solid fa-check"></i>
                                <span><?= $editItem ? 'Update in XAMPP' : 'Save to XAMPP' ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- INDEX VIEW (TABLE) -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Inventory Items (XAMPP Database)</h1>
                        <p class="text-xs text-gray-500 mt-1">Live data from MySQL table <code>laravel_crud.items</code></p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full w-fit">
                        <i class="fa-solid fa-database text-emerald-600"></i>
                        Total: <?= count($items) ?> record(s)
                    </span>
                </div>

                <?php if (empty($items)): ?>
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                            <i class="fa-regular fa-folder-open"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">No items found in XAMPP</h3>
                        <p class="text-sm text-gray-500 mt-1 mb-6">Add your first item to save it directly into MySQL.</p>
                        <a href="index.php?action=create" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Item Now</span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="py-3.5 px-6">ID</th>
                                    <th class="py-3.5 px-6">Item Name</th>
                                    <th class="py-3.5 px-6">Description</th>
                                    <th class="py-3.5 px-6">Price</th>
                                    <th class="py-3.5 px-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php foreach ($items as $item): ?>
                                <tr class="hover:bg-gray-50/60 transition">
                                    <td class="py-4 px-6 text-gray-400 font-mono text-xs">#<?= $item['id'] ?></td>
                                    <td class="py-4 px-6 font-semibold text-gray-900"><?= htmlspecialchars($item['name']) ?></td>
                                    <td class="py-4 px-6 text-gray-600 max-w-xs truncate"><?= htmlspecialchars($item['description']) ?></td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold text-xs rounded-md">
                                            $<?= number_format($item['price'], 2) ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="index.php?edit=<?= $item['id'] ?>" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                <span>Edit</span>
                                            </a>
                                            <form action="index.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" class="inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500 mt-auto">
        Live XAMPP MySQL CRUD &bull; Database: <code>laravel_crud</code>
    </footer>

</body>
</html>
