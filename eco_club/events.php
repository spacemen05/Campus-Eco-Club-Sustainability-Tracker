<?php
$pageTitle = "Upcoming Events";
$active = "events";
include __DIR__ . "/_layout_top.php";
require_once __DIR__ . "/../includes/db.php";

$events = $pdo->query("
  SELECT id, title, event_date, event_time, venue, status
  FROM events
  ORDER BY event_date ASC, event_time ASC
")->fetchAll();
?>

<div class="panel border rounded-3xl shadow-sm p-6">
  <div class="flex items-center justify-between gap-3 mb-4">
    <div>
      <h2 class="text-xl font-extrabold">Upcoming Events</h2>
      <p class="text-slate-600 text-sm">Loaded from MySQL</p>
    </div>
  </div>

  <?php if (empty($events)): ?>
    <div class="rounded-2xl bg-white/70 border p-4 text-slate-700">
      No upcoming events.
    </div>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-5 py-3">Event</th>
            <th class="text-left px-5 py-3">Date</th>
            <th class="text-left px-5 py-3">Time</th>
            <th class="text-left px-5 py-3">Venue</th>
            <th class="text-left px-5 py-3">Status</th>
            <th class="text-right px-5 py-3">Action</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          <?php foreach ($events as $e): ?>
            <?php
              $status = $e['status'] ?? 'open';
              $pill = ($status === 'open')
                ? "bg-emerald-100 text-emerald-800"
                : (($status === 'limited')
                  ? "bg-amber-100 text-amber-800"
                  : "bg-rose-100 text-rose-800");
            ?>
            <tr class="trow">
              <td class="px-5 py-3 font-semibold"><?= htmlspecialchars($e['title']) ?></td>
              <td class="px-5 py-3"><?= htmlspecialchars($e['event_date']) ?></td>
              <td class="px-5 py-3"><?= htmlspecialchars(substr((string)$e['event_time'], 0, 5)) ?></td>
              <td class="px-5 py-3"><?= htmlspecialchars($e['venue']) ?></td>
              <td class="px-5 py-3">
                <span class="px-2 py-1 rounded-full text-xs <?= $pill ?>">
                  <?= htmlspecialchars($status) ?>
                </span>
              </td>
              <td class="px-5 py-3 text-right whitespace-nowrap">
                <a class="btn px-3 py-2 rounded-2xl border bg-white/70 hover:bg-white"
                   href="event_view.php?id=<?= (int)$e['id'] ?>">
                  View
                </a>

                <a class="btn px-3 py-2 rounded-2xl bg-emerald-600 text-white hover:bg-emerald-700"
                   href="event_signup.php?id=<?= (int)$e['id'] ?>">
                  Sign Up
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>

      </table>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . "/_layout_bottom.php"; ?>
