function showLogin(role) {
    document.getElementById("student-login").classList.add("hidden");
    document.getElementById("teacher-login").classList.add("hidden");

    if (role === "student") {
        document.getElementById("student-login").classList.remove("hidden");
    } else {
        document.getElementById("teacher-login").classList.remove("hidden");
    }
}

function login(role) {
    if (role === "student") {
        let className = document.getElementById("student-class").value.trim();
        let rollNo = document.getElementById("student-roll").value.trim();
        
        if (className === "" || rollNo === "") {
            alert("Please enter both Class Name and Roll No!");
            return;
        }
        
        // Redirect to PHP with query params
        window.location.href = `attendance.php?class=${className}&roll=${rollNo}`;
    } else {
        let teacherId = document.getElementById("teacher-id").value.trim();
        let teacherPass = document.getElementById("teacher-pass").value.trim();
        
        if (teacherId === "" || teacherPass === "") {
            alert("Please enter both Teacher ID and Password!");
            return;
        }

        if (teacherId === "BVIT" && teacherPass === "bvit_0000") {
            // Handle teacher login (Redirect to dashboard)
        window.location.href = `teacher_dashboard.html`;
        }
        
       
    }
}
