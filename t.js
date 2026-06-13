$(document).ready(function () {
    // Show/hide batch selection for lab
    $("input[name='type']").change(function () {
        $("#batch-selection").toggle(this.value === "lab");
    });

    // Load students dynamically
    $("#load-students").click(function () {
        let selectedClass = $("#class-select").val();
        let selectedSubject = $("#subject-select").val();
        let selectedType = $("input[name='type']:checked").val();
        let selectedBatch = $("#batch-select").val();

        if (!selectedClass || !selectedSubject) {
            alert("Please select a class and subject.");
            return;
        }

        $.ajax({
            url: "fetch_students.php",
            method: "POST",
            data: { class: selectedClass, subject: selectedSubject, type: selectedType, batch: selectedBatch },
            success: function (response) {
                $("#students-list").html(response);
                $("#mark-attendance-btn").show();
            }
        });
    });

    // Mark attendance
    $("#mark-attendance-btn").click(function () {
        let attendanceData = [];
        $(".attendance-checkbox:checked").each(function () {
            attendanceData.push($(this).data("roll"));
        });

        if (attendanceData.length === 0) {
            alert("No students selected!");
            return;
        }

        $.ajax({
            url: "mark_attendance.php",
            method: "POST",
            data: { attendance: attendanceData },
            success: function (response) {
                alert(response);
            }
        });
    });
});

// Logout function
function logout() {
    alert("Logging out...");
    window.location.href = "login.html";
}
