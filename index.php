<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Sandiganbayan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    
    <div class="flex h-screen overflow-hidden relative">
        
        <!-- Mobile Sidebar Overlay -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-blue-900 dark:bg-gray-800 text-white flex flex-col shadow-lg fixed inset-y-0 left-0 z-50 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
            <div class="p-4 border-b border-blue-800 dark:border-gray-700 flex justify-between items-center">
                <h1 class="text-xl font-bold tracking-wider">Sandiganbayan</h1>
                <button onclick="toggleSidebar()" class="md:hidden text-white focus:outline-none font-bold text-xl">&times;</button>
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="index.php" class="block py-2 px-4 bg-blue-800 dark:bg-gray-700 rounded transition">Inventory</a>
                <a href="reports.php" class="block py-2 px-4 hover:bg-blue-800 dark:hover:bg-gray-700 rounded transition">Reports</a>
            </nav>
            <div class="p-4 border-t border-blue-800 dark:border-gray-700">
                <form action="api/auth.php" method="POST">
                    <input type="hidden" name="action" value="logout">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 py-2 rounded transition">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto w-full">
            <!-- Header -->
            <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center sticky top-0 z-30">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600 dark:text-gray-300 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h2 class="text-xl md:text-2xl font-semibold">Inventory Management</h2>
                </div>
                <button id="themeToggle" class="px-3 py-1.5 md:px-4 md:py-2 text-sm bg-gray-200 dark:bg-gray-700 rounded-lg">Dark Mode</button>
            </header>

            <div class="p-4 md:p-6 space-y-6">
                <!-- Controls -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow flex flex-col md:flex-row justify-between gap-4">
                    <div class="flex w-full md:w-auto">
                        <input type="text" id="searchInput" placeholder="Search items..." class="px-4 py-2 border dark:border-gray-600 rounded-lg dark:bg-gray-700 w-full md:w-64">
                    </div>
                    <div class="flex flex-wrap gap-2 w-full md:w-auto">
                        <button onclick="exportExcel()" class="flex-1 md:flex-none bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Excel</button>
                        <button onclick="exportPDF()" class="flex-1 md:flex-none bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">PDF</button>
                        <button onclick="openModal()" class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Add Item</button>
                    </div>
                </div>

                <!-- Table & Pagination Wrapper -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left min-w-max" id="inventoryTable">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <tr>
                                    <th class="p-4">ID</th>
                                    <th class="p-4">Employee</th>
                                    <th class="p-4">Office</th>
                                    <th class="p-4">Brand</th>
                                    <th class="p-4">Year</th>
                                    <th class="p-4">Qty</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Custom Details</th>
                                    <th class="p-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody" class="divide-y dark:divide-gray-600">
                                <!-- Rows populated by JS -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination Container -->
                    <div id="paginationContainer" class="p-4 border-t dark:border-gray-600 flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Populated by JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal (Unchanged) -->
    <div id="itemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-bold mb-4" id="modalTitle">Add Inventory Item</h3>
            <form id="itemForm" class="space-y-4">
                <input type="hidden" id="itemId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Employee</label><input type="text" id="user_name" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700"></div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Office</label>
                        <div class="flex gap-2">
                            <select id="office_id" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700"></select>
                            <button type="button" onclick="addOffice()" class="bg-gray-200 dark:bg-gray-600 px-3 rounded">+</button>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium mb-1">Brand</label><input type="text" id="brand" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700"></div>
                    <div><label class="block text-sm font-medium mb-1">Year</label><input type="number" id="year" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700"></div>
                    <div><label class="block text-sm font-medium mb-1">Quantity</label><input type="number" id="quantity" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700"></div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select id="status" required class="w-full px-3 py-2 border dark:border-gray-600 rounded dark:bg-gray-700">
                            <option value="Active">Active</option>
                            <option value="Storage">Storage</option>
                            <option value="Defective">Defective</option>
                            <option value="Disposed">Disposed</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 border-t pt-4 dark:border-gray-600">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold">Custom Fields</h4>
                        <button type="button" onclick="addCustomField()" class="text-blue-600 dark:text-blue-400 text-sm">+ Add Field</button>
                    </div>
                    <div id="customFieldsContainer" class="space-y-2"></div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded w-full md:w-auto">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 w-full md:w-auto">Save Item</button>
                </div>
            </form>
        </div>
    </div>
    <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>
    <script src="assets/app.js"></script>
</body>
</html>