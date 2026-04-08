<?php 
session_start();
require_once __DIR__ . "/includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>Eco-Tracker | Organizer Dashboard</title>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">

    <nav class="glass sticky top-0 z-50 border-b border-green-200 p-4 shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-green-600 p-2 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-green-900 tracking-tight">Eco<span class="text-green-600">Tracker</span></h1>
            </div>
            <div class="flex items-center gap-6">
                <span class="text-gray-600 text-sm hidden md:block">
                    Welcome, <span class="font-bold text-green-700">Event Organiser</span>
                </span>
                <a href="Event_organiser_logout.php" class="bg-red-50 to-red-100 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white px-5 py-2 rounded-full text-sm font-bold transition-all duration-300 shadow-sm">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto py-10 px-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-3xl shadow-xl border border-green-100">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Event</h2>
                    <form action="Event_organiser_process.php" method="POST" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Event Title</label>
                            <input type="text" name="title" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition" placeholder="e.g. Tree Planting">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Venue</label>
                            <input type="text" name="venue" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none" placeholder="Main Hall">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
                                <input type="date" name="event_date" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Capacity</label>
                                <input type="number" name="capacity" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-500 outline-none" placeholder="50">
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-200 transition-all active:scale-95">
                            Launch Event
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-3xl shadow-xl border border-green-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-2xl font-bold text-gray-800">Active Events</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-widest">
                                    <th class="p-6 font-semibold">Event Info</th>
                                    <th class="p-6 font-semibold text-center">Date</th>
                                    <th class="p-6 font-semibold text-center">Status</th>
                                    <th class="p-6 font-semibold text-right">Management</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
                                while ($row = $stmt->fetch()) {
                                    echo "<tr class='hover:bg-green-50/50 transition-colors group'>";
                                    echo "<td class='p-6'>
                                            <div class='font-bold text-gray-800 text-lg'>".htmlspecialchars($row['title'])."</div>
                                            <div class='text-sm text-gray-500 flex items-center gap-1'>
                                                <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' />
                                                </svg> ".htmlspecialchars($row['venue'])."
                                            </div>
                                          </td>";
                                    echo "<td class='p-6 text-center text-gray-600 font-medium'>".$row['event_date']."</td>";
                                    echo "<td class='p-6 text-center'>
                                            <span class='bg-emerald-100 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-tighter'>
                                                ".$row['capacity']." Slots
                                            </span>
                                          </td>";
                                    echo "<td class='p-6 text-right'>
                                            <a href='delete_event.php?id=".$row['id']."' 
                                               onclick=\"return confirm('Are you sure ?');\" 
                                               class='inline-flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition-all'>
                                               <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                  <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' />
                                               </svg> Delete
                                            </a>
                                          </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="container mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-md overflow-hidden border-t-4 border-blue-600">
        <div class="bg-gray-100 px-6 py-4 border-b">
            <h2 class="text-lg font-bold text-gray-700">Attendance Verification & Eco-Reward Board</h2>
        </div>
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                <tr>
                    <th class="p-4">Participant Name</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-center">Eco-Points</th>
                    <th class="p-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
    <?phpdfb
    try {
        // Querying the table you just successfully populated
        $stmt = $pdo->query("SELECT * FROM participants");
        $participants = $stmt->fetchAll();

        if (count($participants) > 0) {
            foreach ($participants as $p) {
                $isAttended = ($p['status'] == 'Attended');
                echo "<tr class='hover:bg-blue-50 transition'>";
                echo "<td class='p-4 font-medium'>".htmlspecialchars($p['name'])."</td>";
                echo "<td class='p-4'><span class='".($isAttended ? "text-green-600 font-bold" : "text-amber-600")."'>".$p['status']."</span></td>";
                echo "<td class='p-4 text-center font-bold text-green-700'>".$p['eco_points']." pts</td>";
                echo "<td class='p-4 text-right'>";
                if (!$isAttended) {
                    echo "<a href='Event_organiser_verify_attendance.php?id=".$p['id']."' class='bg-blue-600 text-white px-4 py-2 rounded-lg text-xs hover:bg-blue-700 transition'>Verify & Award 10pts</a>";
                } else {
                    echo "<span class='text-gray-400 italic text-sm'>✓ Points Awarded</span>";
                }
                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='p-10 text-center text-gray-400'>Table is empty in the database.</td></tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='4' class='p-10 text-center text-red-500 font-bold'>Database Error: " . $e->getMessage() . "</td></tr>";
    }
    ?>
</tbody>
        </table>
    </div>
</div>
</body>
</html>
