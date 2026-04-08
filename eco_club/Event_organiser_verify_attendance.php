<?php
session_start();
require_once __DIR__ . "/includes/db.php";

if (isset($_GET['id'])) {
    $participant_id = $_GET['id'];

    try {
        // Logic: Update status to Attended and set eco_points to 10
        $sql = "UPDATE participants SET status = 'Attended', eco_points = 10 WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$participant_id]);

        // Redirect back to dashboard with a success message
        header("Location: Event_organiser_index.php?notif=points_awarded");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>