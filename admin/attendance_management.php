<?php
session_start();
include '../db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Fetch all events
$events = $conn->query("SELECT id, name, date FROM events ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management | Smart Campus</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <?php include 'sidebar.php'; ?>
    <?php include 'header.php'; ?>

    <div class="main-content">
        <h3>Attendance Management</h3>

        <!-- Submit Attendance Section -->
        <form id="attendanceForm">
            <!-- Select Event -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <label>Select Event</label>
                    <select id="eventSelect" class="form-control">
                        <option value="">Select an event</option>
                        <?php while ($event = $events->fetch_assoc()): ?>
                            <option value="<?php echo $event['id']; ?>">
                                <?php echo $event['name'] . " (" . $event['date'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Event Date -->
                <div class="col-md-4">
                    <label>Event Date</label>
                    <input type="text" id="eventDate" class="form-control" readonly>
                </div>

                <!-- Attendance Status -->
                <div class="col-md-4">
                    <label>Attendance Marked</label>
                    <input type="text" id="attendanceStatus" class="form-control" readonly>
                </div>
            </div>

            <!-- Select Badge -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <label>Select Badge</label>
                    <select id="badgeSelect" class="form-control" disabled>
                        <option value="">Select an event first</option>
                    </select>
                </div>
            </div>

            <!-- Select Students -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <label>Select Students</label>
                    <div id="studentSelectContainer" class="form-control" style="height: 150px; overflow-y: auto;" disabled>
                        <!-- Checkboxes will be dynamically added here -->
                        <div id="studentSelect"></div>
                    </div>
                </div>
            </div>

            <!-- Attendance Options -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <label>Mark Attendance</label>
                    <select id="attendanceType" class="form-control">
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Submit Attendance</button>
                </div>
            </div>
        </form>

        <!-- View Attendance Section -->
        <div class="mt-5">
            <h3>View Attendance</h3>

            <!-- Select Event for Viewing -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <label>Select Event</label>
                    <select id="viewEventSelect" class="form-control">
                        <option value="">Select an event</option>
                        <?php 
                        $events->data_seek(0); // Reset the events result pointer
                        while ($event = $events->fetch_assoc()): ?>
                            <option value="<?php echo $event['id']; ?>">
                                <?php echo $event['name'] . " (" . $event['date'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Event Date -->
                <div class="col-md-4">
                    <label>Event Date</label>
                    <input type="text" id="viewEventDate" class="form-control" readonly>
                </div>

                <!-- Attendance Status -->
                <div class="col-md-4">
                    <label>Attendance Marked</label>
                    <input type="text" id="viewAttendanceStatus" class="form-control" readonly>
                </div>
            </div>

            <!-- Select Badge for Viewing -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <label>Select Badge</label>
                    <select id="viewBadgeSelect" class="form-control" disabled>
                        <option value="">Select an event first</option>
                    </select>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <label>Search Students</label>
                    <input type="text" id="searchStudent" class="form-control" placeholder="Search by name or ID" disabled>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="attendanceTableContainer" class="form-control" style="height: 300px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // Load badges when event is selected (Submit Attendance)
        $("#eventSelect").change(function() {
            let eventId = $(this).val();
            if (eventId) {
                $.get("fetch_badges.php", { event_id: eventId }, function(data) {
                    $("#badgeSelect").html(data).prop("disabled", false);
                });

                $.get("fetch_event_details.php", { event_id: eventId }, function(response) {
                    $("#eventDate").val(response.date);
                    $("#attendanceStatus").val(response.status);
                }, "json");
            } else {
                $("#badgeSelect").prop("disabled", true).html('<option value="">Select an event first</option>');
                $("#studentSelectContainer").prop("disabled", true).html('');
            }
        });

        // Load students when badge is selected (Submit Attendance)
        $("#badgeSelect").change(function() {
            let badgeId = $(this).val();
            if (badgeId) {
                $.get("fetch_students.php", { badge_id: badgeId }, function(data) {
                    let html = $(data).map(function() {
                        return `
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="students" value="${$(this).val()}" id="student${$(this).val()}">
                                <label class="form-check-label" for="student${$(this).val()}">
                                    ${$(this).text()}
                                </label>
                            </div>
                        `;
                    }).get().join('');
                    $("#studentSelect").html(html);
                    $("#studentSelectContainer").prop("disabled", false);
                });
            } else {
                $("#studentSelect").html('');
                $("#studentSelectContainer").prop("disabled", true);
            }
        });

        // Submit Attendance
        $("#attendanceForm").submit(function(e) {
            e.preventDefault();
            
            let eventId = $("#eventSelect").val();
            let badgeId = $("#badgeSelect").val();
            let selectedStudents = [];
            $("input[name='students']:checked").each(function() {
                selectedStudents.push($(this).val());
            });
            let attendanceType = $("#attendanceType").val();

            if (!eventId || !badgeId || selectedStudents.length === 0) {
                alert("Please select all fields and at least one student!");
                return;
            }

            $.post("submit_attendance.php", { 
                event_id: eventId, 
                badge_id: badgeId, 
                students: selectedStudents, 
                status: attendanceType 
            }, function(response) {
                alert(response.message);
                location.reload();
            }, "json");
        });

        // Load badges when event is selected (View Attendance)
        $("#viewEventSelect").change(function() {
            let eventId = $(this).val();
            if (eventId) {
                $.get("fetch_badges.php", { event_id: eventId }, function(data) {
                    $("#viewBadgeSelect").html(data).prop("disabled", false);
                });

                $.get("fetch_event_details.php", { event_id: eventId }, function(response) {
                    $("#viewEventDate").val(response.date);
                    $("#viewAttendanceStatus").val(response.status);
                }, "json");
            } else {
                $("#viewBadgeSelect").prop("disabled", true).html('<option value="">Select an event first</option>');
                $("#attendanceTableBody").html('');
                $("#searchStudent").prop("disabled", true);
            }
        });

        // Load attendance data when badge is selected (View Attendance)
        $("#viewBadgeSelect").change(function() {
            let badgeId = $(this).val();
            let eventId = $("#viewEventSelect").val(); // Get the selected event_id

            if (badgeId && eventId) {
                $.get("fetch_attendance.php", { badge_id: badgeId, event_id: eventId }, function(data) {
                    let html = data.map(student => `
                        <tr>
                            <td>${student.name}</td>
                            <td>${student.uni_ID}</td>
                            <td>${student.status}</td>
                        </tr>
                    `).join('');
                    $("#attendanceTableBody").html(html);
                    $("#searchStudent").prop("disabled", false);
                }, "json");
            } else {
                $("#attendanceTableBody").html('');
                $("#searchStudent").prop("disabled", true);
            }
        });

        // Search Students
        $("#searchStudent").on("input", function() {
            let searchText = $(this).val().toLowerCase();
            $("#attendanceTableBody tr").each(function() {
                let studentName = $(this).find("td:first").text().toLowerCase();
                let studentID = $(this).find("td:nth-child(2)").text().toLowerCase();
                if (studentName.includes(searchText) || studentID.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>

</body>
</html>