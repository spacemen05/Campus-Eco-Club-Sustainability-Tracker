<?php
session_start();
require_once __DIR__ . "/includes/db.php"; 


if (!isset($_SESSION["user_id"]) || strtolower($_SESSION["role"] ?? "") !== "admin") {
    header("Location: login.php");
    exit();
}


$start_date = $_GET["start_date"] ?? "";
$end_date = $_GET["end_date"] ?? "";
$category_filter = $_GET["category"] ?? "All";


$sql = "
    SELECT rl.*, u.name AS volunteer_name
    FROM recycling_logs rl
    LEFT JOIN users u ON u.id = rl.volunteer_id
    WHERE 1=1
";
$params = [];

if (!empty($start_date) && !empty($end_date)) {

    $sql .= " AND rl.created_at BETWEEN ? AND ?";
    $params[] = $start_date . " 00:00:00";
    $params[] = $end_date . " 23:59:59";
}

if ($category_filter !== "All") {
    $sql .= " AND rl.category = ?";
    $params[] = $category_filter;
}

$sql .= " ORDER BY rl.created_at DESC";

// CSV Export
if (isset($_POST["export_csv"])) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filename = "EcoClub_Report_" . date("Y-m-d") . ".csv";
    header("Content-Type: text/csv");
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen("php://output", "w");
    fputcsv($output, ["Log ID", "Volunteer", "Category", "Quantity", "Points", "Created At"]);
    foreach ($data as $row) {
        fputcsv($output, [
            $row["id"],
            $row["volunteer_name"] ?? ("User #".$row["volunteer_id"]),
            $row["category"],
            $row["quantity"],
            $row["points"],
            $row["created_at"]
        ]);
    }
    fclose($output);
    exit();
}


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total_transactions = count($rows);
$total_points = 0;
$total_quantity = 0.0;

foreach ($rows as $r) {
    $total_points += (int)($r["points"] ?? 0);
    $total_quantity += (float)($r["quantity"] ?? 0); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - EcoClub Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 500: '#10B981', 600: '#059669', 900: '#064E3B' } } } } }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

<div class="flex h-screen overflow-hidden">

    <aside class="w-64 bg-slate-900 text-white flex flex-col fixed h-full transition-all duration-300">
        <div class="h-16 flex items-center justify-center border-b border-slate-800">
            <h1 class="text-2xl font-bold text-brand-500"><i class="fa-solid fa-leaf mr-2"></i>Admin</h1>
        </div>
        <nav class="flex-1 px-2 py-4 space-y-2">
            <a href="admindashboard.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-chart-line w-6"></i> <span class="font-medium">Dashboard</span>
            </a>
            <a href="users.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-users w-6"></i> <span class="font-medium">Manage Users</span>
            </a>
            <a href="adminecopoints.php" class="flex items-center px-4 py-3 text-slate-400 hover:bg-slate-800 hover:text-white rounded-lg transition-colors">
                <i class="fa-solid fa-coins w-6"></i> <span class="font-medium">Eco-Points</span>
            </a>
            <a href="adminreports.php" class="flex items-center px-4 py-3 bg-brand-600 text-white rounded-lg transition-colors">
                <i class="fa-solid fa-file-invoice w-6"></i> <span class="font-medium">Reports</span>
            </a>
        </nav>
    </aside>

    <main class="flex-1 ml-64 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">System Reports</h2>
            <div class="h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold border border-brand-200">SA</div>
        </header>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>"
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 w-40">
                        <option value="All" <?= $category_filter==="All" ? "selected" : "" ?>>All</option>
                        <option value="plastic" <?= $category_filter==="plastic" ? "selected" : "" ?>>Plastic</option>
                        <option value="paper" <?= $category_filter==="paper" ? "selected" : "" ?>>Paper</option>
                        <option value="metal" <?= $category_filter==="metal" ? "selected" : "" ?>>Metal</option>
                        <option value="glass" <?= $category_filter==="glass" ? "selected" : "" ?>>Glass</option>
                        <option value="other" <?= $category_filter==="other" ? "selected" : "" ?>>Other</option>
                    </select>
                </div>

                <button type="submit" class="px-6 py-2 bg-slate-800 text-white font-medium rounded-lg hover:bg-slate-900 transition-colors">
                    <i class="fa-solid fa-filter mr-2"></i> Filter
                </button>

                <div class="ml-auto flex gap-2">
                    <button type="button" onclick="generatePDF()" class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Export PDF
                    </button>

                    <form method="POST">
                        <input type="hidden" name="export_csv" value="1">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fa-solid fa-file-csv mr-2"></i> Export CSV
                        </button>
                    </form>
                </div>
            </form>
        </div>

        <div id="report-content">

            <div class="mb-4">
                <h3 class="text-xl font-bold">EcoClub Performance Report</h3>
                <p class="text-sm text-gray-500">Generated on: <?= date("Y-m-d H:i") ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <p class="text-sm text-blue-600 font-semibold uppercase">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-800"><?= $total_transactions ?></p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                    <p class="text-sm text-green-600 font-semibold uppercase">Total Quantity (sum)</p>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($total_quantity, 1) ?></p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    <p class="text-sm text-yellow-600 font-semibold uppercase">Points Generated</p>
                    <p class="text-2xl font-bold text-gray-800"><?= number_format($total_points) ?> pts</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100 text-xs uppercase font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Created</th>
                            <th class="px-6 py-3">Volunteer</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Quantity</th>
                            <th class="px-6 py-3">Points</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($rows)): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["created_at"] ?? "") ?></td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        <?= htmlspecialchars($row["volunteer_name"] ?? ("User #".$row["volunteer_id"])) ?>
                                    </td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["category"] ?? "") ?></td>
                                    <td class="px-6 py-4"><?= htmlspecialchars($row["quantity"] ?? "") ?></td>
                                    <td class="px-6 py-4 font-bold text-brand-600">+<?= htmlspecialchars($row["points"] ?? 0) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">No records found for this selection.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </main>
</div>

<script>
    function generatePDF() {
        const element = document.getElementById('report-content');
        const opt = {
            margin: 0.5,
            filename: 'EcoClub_Report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

</body>
</html>
