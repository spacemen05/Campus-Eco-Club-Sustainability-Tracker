<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$pageTitle = "Plan / Propose Event";
$active = "plan";
include __DIR__ . "/volunteer_layout_top.php";

/* ✅ PERFECT IDs (no hardcode) */
$volunteerId = (int)($_SESSION['user_id'] ?? 0);
if ($volunteerId <= 0) { header("Location: login.php"); exit; }

$orgStmt = $pdo->prepare("SELECT id FROM users WHERE role='Admin' ORDER BY id ASC LIMIT 1");
$orgStmt->execute();
$organiserId = (int)$orgStmt->fetchColumn();
if ($organiserId <= 0) { die("No Admin account found to act as organiser."); }

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title = trim($_POST["title"] ?? "");
  $date  = trim($_POST["event_date"] ?? "");
  $time  = trim($_POST["event_time"] ?? "");
  $venue = trim($_POST["venue"] ?? "");
  $desc  = trim($_POST["description"] ?? "");
  $cap   = (int)($_POST["capacity"] ?? 0);

  if ($title === "" || $date === "" || $time === "" || $venue === "" || $cap <= 0) {
    $error = "Please fill in all required fields (title, date, time, venue, capacity).";
  } else {
    try {
      $stmt = $pdo->prepare("
        INSERT INTO event_proposals
          (volunteer_id, organiser_id, title, event_date, event_time, venue, description, capacity, status)
        VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
      ");
      $stmt->execute([$volunteerId, $organiserId, $title, $date, $time, $venue, $desc, $cap]);

      $success = true;
    } catch (Throwable $e) {
      $error = "DB Error: " . $e->getMessage();
    }
  }
}
?>

<div class="max-w-2xl">
  <?php if ($success): ?>
    <div class="mb-4 p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100">
      Proposal submitted  Waiting for Event Organiser approval.
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="mb-4 p-4 rounded-2xl bg-rose-50 text-rose-800 border border-rose-100">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-2xl border shadow-sm p-6">
    <h2 class="text-xl font-bold">Plan / Propose Event</h2>
    <p class="text-slate-600 text-sm mt-1">Submit an event proposal. Event Organiser will approve/reject.</p>

    <form class="mt-5 space-y-4" method="post">
      <div>
        <label class="text-sm font-semibold">Title *</label>
        <input name="title" class="w-full mt-1 border rounded-xl p-2" required placeholder="e.g. Beach Cleanup">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold">Event Date *</label>
          <input type="date" name="event_date" class="w-full mt-1 border rounded-xl p-2" required>
        </div>

        <div>
          <label class="text-sm font-semibold">Event Time *</label>
          <input type="time" name="event_time" class="w-full mt-1 border rounded-xl p-2" required>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold">Venue *</label>
        <input name="venue" class="w-full mt-1 border rounded-xl p-2" required placeholder="e.g. MMU Park">
      </div>

      <div>
        <label class="text-sm font-semibold">Capacity *</label>
        <input type="number" name="capacity" class="w-full mt-1 border rounded-xl p-2" required min="1" placeholder="e.g. 50">
      </div>

      <div>
        <label class="text-sm font-semibold">Description</label>
        <textarea name="description" class="w-full mt-1 border rounded-xl p-2" rows="4" placeholder="Optional details"></textarea>
      </div>

      <button class="px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
        Submit Proposal
      </button>
    </form>
  </div>
</div>

<?php include __DIR__ . "/volunteer_layout_bottom.php"; ?>
