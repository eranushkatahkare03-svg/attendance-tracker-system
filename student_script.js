// Sample Attendance Data (Replace with real database data)
const studentData = {
    class: "CSE-5A",
    rollNo: "123",
    attendance: [
        { subject: "Math", percentage: 85 },
        { subject: "Physics", percentage: 90 },
        { subject: "Computer Science", percentage: 95 },
        { subject: "English", percentage: 80 }
    ]
};

// Display Student Info
document.getElementById('student-class').textContent = studentData.class;
document.getElementById('student-roll').textContent = studentData.rollNo;

// Populate Attendance Table
let totalPercentage = 0;
let tableBody = document.getElementById("attendance-table");
studentData.attendance.forEach((subject) => {
    let row = `<tr>
        <td>${subject.subject}</td>
        <td>${subject.percentage}%</td>
    </tr>`;
    tableBody.innerHTML += row;
    totalPercentage += subject.percentage;
});

// Calculate Overall Attendance
let overall = (totalPercentage / studentData.attendance.length).toFixed(2);
document.getElementById("overall-attendance").textContent = overall;

// Logout Function
function logout() {
    alert("Logging out...");
    window.location.href = "login.html";
}
