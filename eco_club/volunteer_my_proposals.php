<?php
$pageTitle = "My Proposals";
$active = "plan";
include __DIR__ . "/volunteer_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

$volunteerId = 1;

$stmt = $pdo->prepare("
  SELECT id, title, event_date, event_time, venue, capacity, status, admin_note, created_at
  FROM event_proposals
  WHERE volunteer_id = ?
  ORDER BY created_at DESC
");
$stmt->execute([$volunteerId]);
$rows = $stmt->fetchAll();
?>

<div class="panel border rounded-3xl shadow-sm p-6">
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-xl font-extrabold">My Event Proposals</h2>
    <a class="btn px-3 py-2 rounded-2xl bg-slate-900 text-white hover:opacity-90" href="plan_event.php">
      New Proposal
    </a>
  </div>

  <?php if (empty($rows)): ?>
    <div class="rounded-2xl bg-white/70 border p-4 text-slate-700">No proposals yet.</div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-3">Title</th>
            <th class="text-left px-4 py-3">Date</th>
            <th class="text-left px-4 py-3">Time</th>
            <th class="text-left px-4 py-3">Venue</th>
            <th class="text-left px-4 py-3">Cap</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-left px-4 py-3">Admin Note</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($rows as $r): ?>
            <tr class="trow">
              <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($r['title']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['event_date']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars(substr((string)$r['event_time'],0,5)) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['venue']) ?></td>
              <td class="px-4 py-3"><?= (int)$r['capacity'] ?></td>
              <td class="px-4 py-3 font-bold"><?= htmlspecialchars($r['status']) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r['admin_note'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/volunteer_layout_bottom.php"; ?>
