document.getElementById("defaulter").addEventListener("click", function () {
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

    // Send data to PHP using Fetch API
    fetch("defaulter.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: formData.toString()
    })
    .then(response => response.text())  // Read response as text first
    .then(text => {
        if (!text.trim()) {  // If response is empty
            alert("No defaulter data found for the selected month.");
            return;
        }
        return JSON.parse(text);  // Convert text to JSON
    })
    .then(data => {
        if (data && data.success) {
            window.location.href = data.file_url;  // Download the Excel file
        } else {
            alert("No defaulter data found.");
        }
    })
    .catch(error => console.error("Error exporting defaulter data:", error));
    
});
