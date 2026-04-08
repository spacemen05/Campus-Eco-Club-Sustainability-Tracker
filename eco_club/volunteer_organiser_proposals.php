<?php
$pageTitle = "Organiser - Proposals";
$active = "org_prop";
include __DIR__ . "/volunteer_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

// TEMP organiser ID (until login)
$organiserId = 3;

$stmt = $pdo->prepare("
  SELECT p.*, u.name AS volunteer_name
  FROM event_proposals p
  JOIN users u ON u.id = p.volunteer_id
  WHERE p.organiser_id = ?
  ORDER BY p.created_at DESC
");
$stmt->execute([$organiserId]);
$rows = $stmt->fetchAll();
?>

<div class="bg-white rounded-2xl border shadow-sm p-6">
  <h2 class="text-xl font-bold mb-2">Event Proposals (Event Organiser)</h2>
  <p class="text-slate-600 text-sm mb-4">Approve or reject pending event proposals.</p>

  <?php if (empty($rows)): ?>
    <div class="rounded-xl bg-slate-50 p-4 text-slate-700">No proposals assigned to you.</div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-3">Volunteer</th>
            <th class="text-left px-4 py-3">Title</th>
            <th class="text-left px-4 py-3">Date</th>
            <th class="text-left px-4 py-3">Time</th>
            <th class="text-left px-4 py-3">Venue</th>
            <th class="text-left px-4 py-3">Cap</th>
            <th class="text-left px-4 py-3">Status</th>
            <th class="text-right px-4 py-3">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($rows as $r): ?>
            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3"><?= htmlspecialchars($r["volunteer_name"]) ?></td>
              <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($r["title"]) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r["event_date"]) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars(substr((string)$r["event_time"], 0, 5)) ?></td>
              <td class="px-4 py-3"><?= htmlspecialchars($r["venue"]) ?></td>
              <td class="px-4 py-3"><?= (int)$r["capacity"] ?></td>
              <td class="px-4 py-3">
                <?php if ($r["status"] === "pending"): ?>
                  <span class="px-2 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">Pending</span>
                <?php elseif ($r["status"] === "approved"): ?>
                  <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">Approved</span>
                <?php else: ?>
                  <span class="px-2 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">Rejected</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <?php if ($r["status"] === "pending"): ?>
                  <a class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700"
                     href="volunteer_organiser_action.php?id=<?= (int)$r["id"] ?>&action=approve"
                     onclick="return confirm('Approve this proposal?');">
                    Approve
                  </a>
                  <a class="px-3 py-2 rounded-xl bg-rose-600 text-white hover:bg-rose-700"
                     href="volunteer_organiser_action.php?id=<?= (int)$r["id"] ?>&action=reject"
                     onclick="return confirm('Reject this proposal?');">
                    Reject
                  </a>
                <?php else: ?>
                  <span class="text-slate-500">No action</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/volunteer_layout_bottom.php"; ?>
