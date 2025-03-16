<?php
session_start();
include '../db.php';

// Ensure user is logged in and has lecturer privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all events updated by admin
$events = $conn->query("
    SELECT e.*, b.badge_name 
    FROM events e
    LEFT JOIN badges b ON e.badge_id = b.badge_id
");

// Prepare event data for FullCalendar
$event_data = [];
while ($event = $events->fetch_assoc()) {
    $event_data[] = [
        'title' => $event['name'],
        'start' => $event['date'] . "T" . $event['time'],
        'location' => $event['location'],
        'backgroundColor' => '#4CAF50', // Green color for events
        'borderColor' => '#388E3C', // Darker green for borders
        'textColor' => '#ffffff', // White text for events
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Calendar | Smart Campus</title>
    
    <!-- Bootstrap & FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <!-- jQuery and FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    
    <!-- Custom CSS for Improved UI -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 250px; /* Adjust based on sidebar width */
            padding: 100px;
        }
        .table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color:rgb(184, 52, 87);
            color: #fff;
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }
        #calendar {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .fc-toolbar {
            margin-bottom: 20px;
        }
        .fc-toolbar-title {
            font-size: 1.5em;
            color:rgb(184, 52, 87);
        }
        .fc-button {
            background-color:rgb(184, 52, 87) !important;
            border: none !important;
            color: #fff !important;
            transition: background-color 0.3s ease;
        }
        .fc-button:hover {
            background-color:rgb(184, 52, 87) !important;
        }
        .fc-event {
            border-radius: 4px;
            padding: 5px;
            font-size: 0.9em;
        }
        .fc-event:hover {
            opacity: 0.9;
            transform: scale(1.02);
            transition: all 0.3s ease;
        }
        .form-control, .form-select {
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 8px;
        }
        .form-control:focus, .form-select:focus {
            border-color:rgb(184, 52, 87);
            box-shadow: 0 0 5px rgba(184, 52, 87);
        }
        .d-flex {
            gap: 10px;
        }
    </style>
</head>
<body>

    <?php include 'lecturer-header.php'; ?>
    <?php include 'lec_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h3 class="mb-4">Events Table</h3>
            
            <!-- Search and Category Filter -->
            <div class="d-flex justify-content-between mb-4">
                <div>
                    <label for="categoryFilter" class="form-label">Category Filter: </label>
                    <select id="categoryFilter" class="form-select">
                        <option value="all">Show All</option>
                        <?php
                        $types = $conn->query("SELECT DISTINCT type FROM events");
                        while ($type = $types->fetch_assoc()) {
                            echo "<option value='{$type['type']}'>{$type['type']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search events...">
                </div>
            </div>

            <!-- Events Table -->
            <table class="table table-hover" id="eventsTable">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Venue</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($event_data as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo htmlspecialchars($event['location']); ?></td>
                            <td><?php echo date("F j, Y g:i A", strtotime($event['start'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Calendar Section -->
            <div class="mt-5">
                <h3 class="mb-4">Event Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Initialize FullCalendar
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: <?php echo json_encode($event_data); ?>,
                    eventColor: '#4CAF50', // Default event color
                    eventTextColor: '#ffffff', // Event text color
                    eventDisplay: 'block', // Display events as blocks
                    eventMouseEnter: function(info) {
                        $(info.el).popover({
                            title: info.event.title,
                            content: 'Location: ' + info.event.extendedProps.location,
                            trigger: 'hover',
                            placement: 'top',
                            container: 'body',
                            html: true
                        }).popover('show');
                    },
                    eventMouseLeave: function(info) {
                        $(info.el).popover('dispose');
                    },
                    eventClick: function(info) {
                        alert('Event: ' + info.event.title + '\nLocation: ' + info.event.extendedProps.location);
                    }
                });
                calendar.render();
            } else {
                console.error("Calendar element not found!");
            }

            // Search Functionality
            $("#searchInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#eventsTable tbody tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Category Filter
            $("#categoryFilter").on("change", function () {
                var filter = $(this).val().toLowerCase();
                $("#eventsTable tbody tr").filter(function () {
                    $(this).toggle(filter === "all" || $(this).find("td:first").text().toLowerCase().indexOf(filter) > -1);
                });
            });
        });
    </script>

</body>
</html>