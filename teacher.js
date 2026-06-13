// Function to Show Different Sections
function showSection(sectionId) {
    document.querySelectorAll(".dashboard-container").forEach((section) => {
        section.style.display = "none";
    });
    document.getElementById(sectionId).style.display = "block";
}

// Show/Hide Batch Selection Based on Subject (Practical Needs Batches)
document.getElementById("subject-select").addEventListener("change", function () {
    const selectedSubject = this.value;
    const batchSelection = document.getElementById("batch-selection");

    if (selectedSubject.includes("-PR")) {
        batchSelection.style.display = "block"; // Show batch selection for practicals
    } else {
        batchSelection.style.display = "none"; // Hide batch selection for theory subjects
    }
});

// **Function to Load Students for a Selected Class**
function loadStudents() {
    const selectedClass = document.getElementById("class-select").value;
    const selectedSubject = document.getElementById("subject-select").value;
    const selectedDate = document.getElementById("attendance-date").value;
    
    let batch = "";
    if (selectedSubject.includes("-PR")) {
        const batchSelect = document.getElementById("batch-select");
        batch = batchSelect ? batchSelect.value.replace("Batch ", "") : "";
    }

    let studentsList = document.getElementById("students-list");
    //studentsList.innerHTML = ""; // Clear previous list

    if (!selectedClass || !selectedSubject || !selectedDate || (selectedSubject.includes("-PR") && !batch)) {
        alert("Please select Class, Subject, Date, and Batch (if applicable)!");
        return;
    }

    // ✅ Fetch students only
    fetch(`get_students.php?class=${selectedClass}&subject=${selectedSubject}&batch=${batch}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.students || data.students.length === 0) {
                alert("No students found!");
                return;
            }

            data.students.forEach((student) => {
                let studentItem = document.createElement("div");
                studentItem.classList.add("student-list");

                studentItem.innerHTML = `
                    <span>${student.roll_no} - ${student.student_name}</span>
                    <input type="checkbox" class="attendance-checkbox" data-roll="${student.roll_no}">
                `;
                studentsList.appendChild(studentItem);
            });

            document.getElementById("select-all-container").style.display = "block";
            document.getElementById("mark-attendance-btn").style.display = "block";

            // ✅ Add listener to "Select All" checkbox
            const selectAllCheckbox = document.getElementById("select-all-checkbox");
            selectAllCheckbox.checked = false; // reset state
            selectAllCheckbox.addEventListener("change", function () {
                const checkboxes = document.querySelectorAll(".attendance-checkbox");
                checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
            });
    

            document.getElementById("mark-attendance-btn").style.display = "block";
        })
        .catch(error => console.error("Error loading students:", error));
}
// **Function to Load Classes from Backend**
function loadClasses() {
    fetch("get_classes.php")
        .then(response => response.json())
        .then(data => {
            let classDropdown = document.getElementById("student-class-select");
            let deleteClassDropdown = document.getElementById("delete-class-select");

            classDropdown.innerHTML = "";
            deleteClassDropdown.innerHTML = "";

            data.forEach(cls => {
                let option = new Option(cls.class_name, cls.class_name);
                classDropdown.add(option);
                deleteClassDropdown.add(option.cloneNode(true));
            });
        })
        .catch(error => console.error("Error fetching classes:", error));
}

// **Function to Add a Class**
function addClass() {
    const className = document.getElementById("new-class-name").value;

    if (!className) {
        alert("Enter a class name!");
        return;
    }

    fetch("add_class.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `class_name=${className}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadClasses(); // Refresh class list
    })
    .catch(error => console.error("Error adding class:", error));
}

//add subjects
function addSubjects() {
    const selectedClass = document.getElementById("class-name").value;
    const subject = document.getElementById("subjects").value;
    
    if (!selectedClass) return; // Do nothing if no class is selected

    // ✅ Fetch subjects from database based on selected class
    fetch("add_subjects.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `class_name=${selectedClass}&subjects=${subject}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadClasses(); // Refresh class list
    })
        .catch(error => console.error("Error adding subjects:", error));
}


function loadSubjects() {
    const selectedClass = document.getElementById("class-select").value;
    const subjectSelect = document.getElementById("subject-select");

    subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Reset dropdown

    if (!selectedClass) {
        console.error("No class selected!");
        return; // Stop execution if no class is selected
    }

    console.log(selectedClass);
    const url = `get_subjects.php?class=${selectedClass}`;
    console.log("Fetching subjects from:", url); // ✅ Debugging

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("Received data:", data);
            
            if (data.error) {
                console.error("Error:", data.error);
                return;
            }

            subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Reset again

            if (data.subjects.length > 0) {
                data.subjects.forEach(subject => {
                    let option = document.createElement("option");
                    option.value = subject;
                    option.textContent = subject;
                    subjectSelect.appendChild(option);
                });
            } else {
                subjectSelect.innerHTML = '<option value="">No subjects found</option>';
            }
        })
        .catch(error => console.error("Error fetching subjects:", error));
}

window.onload = loadClasses;


// **Function to Delete a Class**
function deleteClass() {
    const className = document.getElementById("delete-class-select").value;

    fetch("delete_class.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `class_name=${className}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadClasses(); // Refresh class list
    })
    .catch(error => console.error("Error deleting class:", error));
}

// **Function to Add a Student**
function addStudent() {
    const rollNo = document.getElementById("student-roll").value.trim();
    const studentName = document.getElementById("student-name").value.trim();
    const batch = document.getElementById("batch").value.trim();
    const class_n = document.getElementById("student-class-select").value.trim();
    const parentEmail = document.getElementById("parent-email").value.trim();

    if (!class_n || !rollNo || !studentName || !batch || !parentEmail) {
        alert("❌ Please fill all fields!");
        return;
    }

    console.log("📤 Sending Data:", {
        class_name: class_n,
        roll_no: rollNo,
        student_name: studentName,
        batch: batch,
        parent_email: parentEmail
    });

    fetch('add_student.php', {
        method: 'POST',
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            class_name: class_n,
            roll_no: rollNo,
            student_name: studentName,
            batch: batch,
            parent_email: parentEmail
                })
    })
    
    .then(response => response.json())
    .then(data => {
        console.log("📩 Response Received:", data);
        alert(data.message);
        if (data.success) {
            document.getElementById("student-roll").value = "";
            document.getElementById("student-name").value = "";
            document.getElementById("batch").value = "";
            document.getElementById("parent-email").value = "";
            loadStudentOptions(); // Reload student list
        }
    })
    .catch(error => console.error("❌ Error adding student:", error));
}





// **Function to Delete a Student**
function deleteStudent() {
    const deleteSelect = document.getElementById("delete-student-select");
    const rollNo = deleteSelect.value;

    if (!rollNo) {
        alert("Please select a student to delete.");
        return;
    }

    fetch("delete_student.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `roll_no=${encodeURIComponent(rollNo)}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadStudentOptions(); // Refresh dropdown
    })
    .catch(error => console.error("Error deleting student:", error));
}


// **Function to Update a Student**
function updateStudent() {
    const studentRoll = document.getElementById("update-student-roll").value;
    const newStudentName = document.getElementById("update-student-name").value;

    if (!studentRoll || !newStudentName) {
        alert("Fill in all fields!");
        return;
    }  

    fetch("update_student.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `roll_no=${studentRoll}&name=${newStudentName}`
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        loadStudents(); // Refresh student list
    })
    .catch(error => console.error("Error updating student:", error));
}

function loadStudentOptions() {
    const selectedClass = document.getElementById("student-class-select").value; // Get the selected class
    fetch(`populate_student.php?class=${selectedClass}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const deleteSelect = document.getElementById("delete-student-select");
            deleteSelect.innerHTML = '<option value="">Select Student</option>';
            
            data.students.forEach(student => {
                let option = document.createElement("option");
                option.value = student.roll_no;
                option.textContent = `${student.student_name}`;
                deleteSelect.appendChild(option);
            });
        }
    })
    .catch(error => console.error("Error loading students:", error));
}


// Call loadStudents when the page loads
document.getElementById("student-class-select").addEventListener("change", loadStudentOptions);

// Call loadStudents when the page loads
function markAttendance() {
    const selectedClass = document.getElementById("class-select").value;
    const selectedSubject = document.getElementById("subject-select").value;
    const selectedDate = document.getElementById("attendance-date").value;

    let batch = "";
    const batchSelect = document.getElementById("batch-select");
    if (batchSelect) {
        batch = batchSelect.value.replace("Batch ", "");
    }

    const selectedStudents = [];
    document.querySelectorAll('.attendance-checkbox:checked').forEach(checkbox => {
        selectedStudents.push(checkbox.dataset.roll);
    });

    if (selectedStudents.length === 0) {
        alert("No students selected.");
        return;
    }

    console.log("Sending data:", {
        className: selectedClass,
        subjectName: selectedSubject,
        date: selectedDate,
        batch: batch, // ✅ Corrected
        students: selectedStudents
    });

    fetch("mark_attendance.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            className:selectedClass, 
            subjectName:selectedSubject, 
            date:selectedDate,
            batch:batch, // ✅ Corrected
            students:selectedStudents.length > 0 ? selectedStudents : null 
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message || "Attendance marked successfully!"); // Show alert with response message
    })
    .catch(error => console.error("Error:", error));


}


function create_exl() {
    const selectedClass = document.getElementById("class-select").value;
    const selectedSubject = document.getElementById("subject-select").value;
    const selectedDate = document.getElementById("attendance-date").value;

    // Debugging: Log selected values
    console.log("Class:", selectedClass);
    console.log("Subject:", selectedSubject);
    console.log("Date:", selectedDate);

    if (selectedClass.trim() === "" || selectedSubject.trim() === "" || selectedDate.trim() === "") {
        alert("❌ Please select class, subject, and date.");
        return;
    }

    // Create form data for POST request
    const formData = new URLSearchParams();
    formData.append("class", selectedClass);
    formData.append("subject", selectedSubject);
    formData.append("date", selectedDate);

    // Fetch request to generate Excel file
    fetch("attendance_export.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text); });
        }
        return response.blob(); // Convert response to file
    })
    .then(blob => {
        let link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = `Attendance_${selectedClass}_${selectedSubject}_${selectedDate}.xlsx`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        alert("✅ Attendance file downloaded successfully!");
    })
    .catch(error => {
        console.error("Error:", error);
        alert("❌ Failed to download Excel file. " + error.message);
    });
}

// **Logout Function**
function logout() {
    alert("Logging out...");
    window.location.href = "login.html";
}

// **Load Classes When Page Loads**
window.onload = function () {
    loadStudentOptions();
    loadClasses();
    
}; 

//Select all
let allSelected = false;

function selectAll() {
    const checkboxes = document.querySelectorAll(".attendance-checkbox");
    allSelected = !allSelected;
    checkboxes.forEach(checkbox => {
        checkbox.checked = allSelected;
    });

    // Update button text if you want
    document.getElementById("select-all-btn").textContent = allSelected ? "Deselect All" : "Select All";
}
