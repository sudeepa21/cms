$(document).ready(function () {
    $("#saveEventType").click(function () {
        let newEventType = $("#newEventType").val();
        if (newEventType) {
            $.post("ajax_handler.php", { new_event_type: newEventType }, function (response) {
                if (response === "success") {
                    $("#event_type").append(`<option value="${newEventType}" selected>${newEventType}</option>`);
                    $("#addEventTypeModal").modal('hide');
                }
            });
        }
    });

    $("#saveEventName").click(function () {
        let newEventName = $("#newEventName").val();
        if (newEventName) {
            $.post("ajax_handler.php", { new_event_name: newEventName }, function (response) {
                if (response === "success") {
                    $("#event_name").append(`<option value="${newEventName}" selected>${newEventName}</option>`);
                    $("#addEventNameModal").modal('hide');
                }
            });
        }
    });
});
