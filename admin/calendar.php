<?php
// Start the session at the very beginning
session_start();

include '../db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Initialize session arrays for new event types and names
if (!isset($_SESSION['new_event_types'])) {
    $_SESSION['new_event_types'] = [];
}
if (!isset($_SESSION['new_event_names'])) {
    $_SESSION['new_event_names'] = [];
}

// Handle AJAX request for saving new event type
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_event_type') {
    $newEventType = $_POST['event_type'];
    if (!in_array($newEventType, $_SESSION['new_event_types'])) {
        $_SESSION['new_event_types'][] = $newEventType;
    }
    exit();
}

// Handle AJAX request for saving new event name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_event_name') {
    $newEventName = $_POST['event_name'];
    if (!in_array($newEventName, $_SESSION['new_event_names'])) {
        $_SESSION['new_event_names'][] = $newEventName;
    }
    exit();
}

// Fetch event types, event names, and badges
$event_types = $conn->query("SELECT DISTINCT type FROM events");
$event_names = $conn->query("SELECT DISTINCT name FROM events");
$badges = $conn->query("SELECT * FROM badges");

// Fetch all scheduled events with badge names
$scheduled_events = $conn->query("
    SELECT e.*, b.badge_name 
    FROM events e
    LEFT JOIN event_badges eb ON e.id = eb.event_id
    LEFT JOIN badges b ON eb.badge_id = b.badge_id
");

// Handle form submission for saving/updating events
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_event'])) {
    $event_type = $_POST['event_type'];
    $event_name = $_POST['event_name'];
    $badge_id = ($_POST['badge'] == 'all') ? 0 : $_POST['badge']; // Ensure 'all' is saved as 0
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Insert into events table
    $stmt = $conn->prepare("INSERT INTO events (type, name, badge_id, location, date, time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $event_type, $event_name, $badge_id, $location, $date, $time);
    $stmt->execute();

    // Get the last inserted event ID
    $event_id = $stmt->insert_id;

    // If a specific badge is selected (not "all"), insert into event_badges table
    if ($badge_id != 0) {
        $stmt = $conn->prepare("INSERT INTO event_badges (event_id, badge_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $event_id, $badge_id);
        $stmt->execute();
    }

    // Add notification for event creation
    $conn->query("INSERT INTO notifications (type, message) VALUES 
        ('event', 'A new event has been added to the calendar!')");

    // Clear session variables after saving
    $_SESSION['new_event_types'] = [];
    $_SESSION['new_event_names'] = [];

    header("Location: calendar.php");
    exit();
}

// Handle deleting events
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];

    // Delete from event_badges table first
    $conn->query("DELETE FROM event_badges WHERE event_id='$event_id'");

    // Delete from events table
    $conn->query("DELETE FROM events WHERE id='$event_id'");

    // Add notification for event deletion
    $conn->query("INSERT INTO notifications (type, message) VALUES 
        ('event', 'An event has been removed from the calendar!')");

    header("Location: calendar.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Calendar | Smart Campus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <!-- Include the header.php file -->
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h3 class="mt-4">Schedule / Update Events</h3>

        <form method="POST" action="calendar.php">
            <div class="row">
                <div class="col-md-4">
                    <label>Event Type</label>
                    <div class="input-group">
                        <select name="event_type" class="form-control" id="eventTypeSelect">
                            <?php while ($row = $event_types->fetch_assoc()): ?>
                                <option value="<?php echo $row['type']; ?>"><?php echo $row['type']; ?></option>
                            <?php endwhile; ?>
                            <?php foreach ($_SESSION['new_event_types'] as $type): ?>
                                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addEventTypeModal">+</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Event Name</label>
                    <div class="input-group">
                        <select name="event_name" class="form-control" id="eventNameSelect">
                            <?php while ($row = $event_names->fetch_assoc()): ?>
                                <option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                            <?php foreach ($_SESSION['new_event_names'] as $name): ?>
                                <option value="<?php echo $name; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addEventNameModal">+</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label>Badge</label>
                    <select name="badge" class="form-control">
                        <option value="all">For All</option>
                        <?php while ($row = $badges->fetch_assoc()): ?>
                            <option value="<?php echo $row['badge_id']; ?>"><?php echo $row['badge_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4">
                    <label>Location</label>
                    <input type="text" name="location" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" name="save_event" class="btn btn-primary">Save</button>
            </div>
        </form>

        <h3 class="mt-4">Scheduled Events</h3>
        <table class="table">
            <tr>
                <th>Event Type</th>
                <th>Event Name</th>
                <th>Badge</th>
                <th>Location</th>
                <th>Date</th>
                <th>Time</th>
                <th>Action</th>
            </tr>
            <?php while ($event = $scheduled_events->fetch_assoc()): ?>
            <tr>
                <td><?php echo $event['type']; ?></td>
                <td><?php echo $event['name']; ?></td>
                <td>
                    <?php
                    if ($event['badge_id'] == 0) {
                        echo "For All Students";
                    } else {
                        echo $event['badge_name']; // Display the badge name
                    }
                    ?>
                </td>
                <td><?php echo $event['location']; ?></td>
                <td><?php echo $event['date']; ?></td>
                <td><?php echo $event['time']; ?></td>
                <td>
                    <a href="calendar.php?delete_event=<?php echo $event['id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        <!-- Add Event Type Modal -->
        <div class="modal fade" id="addEventTypeModal" tabindex="-1" aria-labelledby="addEventTypeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventTypeModalLabel">Add Event Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="newEventType" class="form-control" placeholder="Enter Event Type">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveEventType">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Event Name Modal -->
        <div class="modal fade" id="addEventNameModal" tabindex="-1" aria-labelledby="addEventNameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventNameModalLabel">Add Event Name</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="newEventName" class="form-control" placeholder="Enter Event Name">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveEventName">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    $(document).ready(function() {
        // Save new event type
        $('#saveEventType').click(function() {
            var newEventType = $('#newEventType').val();
            if (newEventType) {
                $.ajax({
                    url: 'calendar.php',
                    type: 'POST',
                    data: { action: 'save_event_type', event_type: newEventType },
                    success: function(response) {
                        $('#addEventTypeModal').modal('hide'); // Close the modal
                        $('#newEventType').val(''); // Clear the input
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error("Error saving event type: " + error);
                    }
                });
            }
        });

        // Save new event name
        $('#saveEventName').click(function() {
            var newEventName = $('#newEventName').val();
            if (newEventName) {
                $.ajax({
                    url: 'calendar.php',
                    type: 'POST',
                    data: { action: 'save_event_name', event_name: newEventName },
                    success: function(response) {
                        $('#addEventNameModal').modal('hide'); // Close the modal
                        $('#newEventName').val(''); // Clear the input
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function(xhr, status, error) {
                        console.error("Error saving event name: " + error);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>