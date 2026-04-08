<?php
session_start();
require_once __DIR__ . "/includes/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$pageTitle = "My Registrations";
$active = "my_reg";
include __DIR__ . "/student_layout_top.php";

$studentId = (int)$_SESSION['user_id']; 

$stmt = $pdo->prepare("
  SELECT
    r.id AS reg_id,
    e.id AS event_id,
    e.title,
    e.event_date,
    e.event_time,
    e.venue,
    e.status,
    r.created_at
  FROM registrations r
  JOIN events e ON e.id = r.event_id
  WHERE r.student_id = ?
  ORDER BY r.created_at DESC
");

$stmt->execute([$studentId]);
$rows = $stmt->fetchAll();
?>

<div class="panel border rounded-3xl shadow-sm p-6">
  <h2 class="text-xl font-extrabold mb-3">My Event Registrations</h2>
  <p class="text-slate-600 text-sm mb-4">Loaded from MySQL </p>

  <?php if (empty($rows)): ?>
    <div class="rounded-2xl bg-white/70 border p-4 text-slate-700">No registrations yet.</div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-3">Event</th>
            <th class="text-left px-4 py-3">Date</th>
            <th class="text-left px-4 py-3">Time</th>
            <th class="text-left px-4 py-3">Venue</th>
            <th class="text-left px-4 py-3">Registered</th>
            <th class="text-right px-4 py-3">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <?php foreach ($rows as $r): ?>
            <tr class="trow">
              <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($r['title']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['event_date']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars(substr($r['event_time'], 0, 5)) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['venue']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['created_at']) ?></td>

              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a class="btn px-3 py-2 rounded-2xl border bg-white/70 hover:bg-white"
                   href="student_event_view.php?id=<?= (int)$r['event_id'] ?>">
                  View
                </a>

                <a class="btn px-3 py-2 rounded-2xl bg-rose-600 text-white hover:bg-rose-700"
                   href="student_cancel_registration.php?reg_id=<?= (int)$r['reg_id'] ?>"
                   onclick="return confirm('Cancel this registration?');">
                  Cancel
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/student_layout_bottom.php"; ?>
