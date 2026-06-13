let questions = [];
let currentQuestionIndex = 0;
let score = 0;
let timer;

document.addEventListener("DOMContentLoaded", function () {
    const startButton = document.getElementById("startQuiz");
    const difficultySelect = document.getElementById("difficulty");

    startButton.addEventListener("click", function () {
        let difficulty = difficultySelect.value;
        console.log("Selected Difficulty:", difficulty); // Debugging

        fetch(`get_questions.php?difficulty=${difficulty}`)
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Questions:", data); // Debugging

                if (!data || data.length === 0) {
                    alert("No questions found for this difficulty level!");
                    return;
                }

                // Store questions and reset the game state
                questions = data;
                currentQuestionIndex = 0;
                score = 0;

                document.getElementById("quiz-container").style.display = "block";
                showQuestion(); // Function to display the first question
            })
            .catch(error => console.error("Error fetching questions:", error));
    });
});

function showQuestion() {
    if (currentQuestionIndex < questions.length) {
        let questionElement = document.getElementById("question");
        let optionsElement = document.getElementById("options");

        let currentQuestion = questions[currentQuestionIndex];
        questionElement.textContent = currentQuestion.question;
        
        optionsElement.innerHTML = "";
        currentQuestion.options.forEach((option, index) => {
            let btn = document.createElement("button");
            btn.textContent = option;
            btn.classList.add("option-btn");
            btn.onclick = function () { checkAnswer(option, currentQuestion.correct_answer); };
            optionsElement.appendChild(btn);
        });
    } else {
        showResult();
    }
}

function checkAnswer(selected, correct) {
    if (selected === correct) {
        score++;
    }
    currentQuestionIndex++;
    showQuestion();
}

function showResult() {
    document.getElementById("quiz-container").innerHTML = `
        <h2>Your Score: ${score} / ${questions.length}</h2>
        <button onclick="location.reload()">Play Again</button>
    `;
}



function endQuiz() {
    clearTimeout(timer);
    let playerName = prompt("Enter your name for the high score:");
    fetch("save_score.php", {
        method: "POST",
        body: new URLSearchParams({ player_name: playerName, score: score })
    })
    .then(() => alert("Quiz Over! Your Score: " + score));
}

function loadHighScore() {
    fetch("get_high_score.php")
        .then(response => response.json())
        .then(data => {
            document.getElementById("high-score").innerText = `High Score: ${data.player_name} - ${data.score}`;
        });
}

window.onload = loadHighScore;
