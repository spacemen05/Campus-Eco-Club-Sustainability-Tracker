<?php
$pageTitle = "Organiser Action";
$active = "org_prop";
include __DIR__ . "/volunteer_layout_top.php";
require_once __DIR__ . "/includes/db.php";

$organiserId = 3; 

$id = (int)($_GET["id"] ?? 0);
$action = $_GET["action"] ?? "";

$msg = "";
$type = "ok";

try {
  if ($id <= 0) throw new Exception("Invalid proposal id.");
  if (!in_array($action, ["approve", "reject"], true)) throw new Exception("Invalid action.");

  $pdo->beginTransaction();

  $stmt = $pdo->prepare("SELECT * FROM event_proposals WHERE id=? FOR UPDATE");
  $stmt->execute([$id]);
  $p = $stmt->fetch();

  if (!$p) throw new Exception("Proposal not found.");
  if ((int)$p["organiser_id"] !== $organiserId) throw new Exception("This proposal is not assigned to you.");
  if ($p["status"] !== "pending") throw new Exception("Only pending proposals can be changed.");

  if ($action === "approve") {
    $stmt = $pdo->prepare("
      INSERT INTO events (title, event_date, event_time, venue, description, capacity, status)
      VALUES (?, ?, ?, ?, ?, ?, 'open')
    ");
    $stmt->execute([
      $p["title"],
      $p["event_date"],
      $p["event_time"],
      $p["venue"],
      $p["description"],
      (int)$p["capacity"]
    ]);

    $stmt = $pdo->prepare("UPDATE event_proposals SET status='approved' WHERE id=?");
    $stmt->execute([$id]);

    $msg = "Approved ✅ Proposal moved into events table.";
  } else {
    $stmt = $pdo->prepare("UPDATE event_proposals SET status='rejected' WHERE id=?");
    $stmt->execute([$id]);

    $msg = "Rejected ❌ Proposal rejected.";
  }

  $pdo->commit();
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  $type = "err";
  $msg = $e->getMessage();
}
?>

<div class="max-w-2xl">
  <div class="bg-white rounded-2xl border shadow-sm p-6">
    <div class="<?= $type === "ok" ? "text-emerald-700" : "text-rose-700" ?> font-bold text-lg">
      <?= htmlspecialchars($msg) ?>
    </div>

    <div class="mt-5 flex gap-2">
      <a class="px-4 py-2 rounded-xl bg-slate-900 text-white hover:opacity-90" href="volunteer_organiser_proposals.php">
        Back
      </a>
      <a class="px-4 py-2 rounded-xl border bg-white hover:bg-slate-50" href="volunteer_events.php">
        View Events
      </a>
    </div>
  </div>
</div>

<?php include __DIR__ . "/volunteer_layout_bottom.php"; ?>
