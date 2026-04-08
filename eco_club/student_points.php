<?php
session_start();

$pageTitle = "Eco Points";
$active = "points";
include __DIR__ . "/student_layout_top.php";
require_once __DIR__ . "/includes/db.php";


$studentId = (int)($_SESSION['user_id'] ?? 0); 

if ($studentId <= 0) {
  echo "Not logged in.";
  exit;
}

// Get the same rows that you display in history
$historyStmt = $pdo->prepare("
  SELECT category, points, created_at
  FROM recycling_logs
  WHERE student_id = ?
  ORDER BY created_at DESC
");
$historyStmt->execute([$studentId]);
$rows = $historyStmt->fetchAll();

// Convert databse rows to your UI format (reason/points/date)
$history = [];
$total = 0;

foreach ($rows as $r) {
  $p = (int)$r['points'];
  $total += $p;

  $history[] = [
    "reason" => "Recycling (" . $r['category'] . ")",
    "points" => $p,
    "date"   => date("d M Y", strtotime($r['created_at'])),
  ];
}
?>

<div class="bg-white rounded-2xl border shadow-sm p-6 mb-4">
  <div class="text-sm text-slate-500">Total Eco Points</div>
  <div class="text-4xl font-extrabold mt-1"><?= $total ?></div>
</div>

<div class="bg-white rounded-2xl border shadow-sm p-5">
  <h2 class="text-lg font-bold mb-3">Points History</h2>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-4 py-3">Reason</th>
          <th class="text-left px-4 py-3">Points</th>
          <th class="text-left px-4 py-3">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($history as $h): ?>
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-medium"><?= htmlspecialchars($h['reason']) ?></td>
            <td class="px-4 py-3 font-bold"><?= (int)$h['points'] ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($h['date']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . "/student_layout_bottom.php"; ?>