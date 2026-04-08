<?php
session_start();
require_once __DIR__ . "/includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $venue = $_POST['venue'];
    $date = $_POST['event_date'];
    $capacity = $_POST['capacity'];

    try {
        $sql = "INSERT INTO events (title, venue, event_date, capacity) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $venue, $date, $capacity]);

        // Fix: Point back to your actual dashboard filename
        header("Location: Event_organiser_index.php?status=success");
        exit(); 
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>