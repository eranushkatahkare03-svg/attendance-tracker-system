document.getElementById("send_mail").addEventListener("click", function () {
    const selectedClass = document.getElementById("class-select").value.trim();
    const selectedDate = document.getElementById("attendance-date").value.trim();

    if (!selectedClass || !selectedDate) {
        alert("Please select Class and Date!");
        return;
    }

    // Extract month and year from the selected date
    const dateObj = new Date(selectedDate);
    const month = dateObj.getMonth() + 1;  // JS months are 0-indexed, so add 1
    const year = dateObj.getFullYear();

    // Prepare data to send
    const formData = new URLSearchParams();
    formData.append("class_name", selectedClass);
    formData.append("month", month);
    formData.append("year", year);
console.log(formData.toString());
    // Send data to PHP using Fetch API
    fetch("testmail/mail.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    })

    
});
