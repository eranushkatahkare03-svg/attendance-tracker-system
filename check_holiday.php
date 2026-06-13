<?php
// Get the current year and country code (change 'IN' if needed)
$year = date("Y");
$country = "IN";  // Change this to your country's ISO code
$url = "https://date.nager.at/api/v2/PublicHolidays/$year/$country";

// Fetch holiday data from API
$response = file_get_contents($url);
$holidays = json_decode($response, true);

// Get today's date
//$today = date('Y-m-d');
$today = date('Y-m-d');

// Extract only the holiday dates
$holiday_dates = array_column($holidays, 'date');

if (in_array($today, $holiday_dates)) {
    echo "Today is a public holiday! Attendance not required.";
} else {
    echo "Proceed with attendance marking.";
    // Call your attendance recording function here
    include 'teacher_dashboard.html';
}
?>
