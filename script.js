// ================= LOGIN =================

function saveData() {

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    // Check that fields are not empty
    if (username === "" || password === "") {

        alert("Please enter Email/Username and Password");

        return;
    }

    // Store login information
    localStorage.setItem("loggedIn", "true");
    localStorage.setItem("user", username);

    alert("Login successful!");

    // Open Home Page
    window.location.href = "home.html";
}


// ================= NEW LOGIN FUNCTION =================
// This works with:
// <form onsubmit="loginUser(event)">

function loginUser(event) {

    event.preventDefault();

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    // Check that fields are not empty
    if (username === "" || password === "") {

        alert("Please enter Email/Username and Password");

        return;
    }

    // Store login information
    localStorage.setItem("loggedIn", "true");
    localStorage.setItem("user", username);

    alert("Login successful!");

    // Open Home Page
    window.location.href = "home.html";
}


// ================= LOGOUT =================

function logout() {

    // Remove login information
    localStorage.removeItem("loggedIn");
    localStorage.removeItem("user");

    // Optional registration information
    localStorage.removeItem("studentName");
    localStorage.removeItem("registered");

    alert("You have been logged out successfully!");

    // Return to Login Page
    window.location.href = "login.html";
}


// ================= CHECK LOGIN =================

function checkLogin() {

    if (localStorage.getItem("loggedIn") !== "true") {

        alert("Please login first.");

        window.location.href = "login.html";

    }

}


// ================= REGISTRATION =================

function registerUser() {

    // Registration successful
    localStorage.setItem("loggedIn", "true");

    localStorage.setItem("registered", "true");

    alert("Registration successful!");

    // Open Home Page
    window.location.href = "home.html";

}