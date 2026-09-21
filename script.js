// =====================================================
// STUDENTHUB PORTAL - COMPLETE JAVASCRIPT
// =====================================================



// =====================================================
// LOGIN
// =====================================================

function saveData() {

    const username =
        document.getElementById("username").value.trim();

    const password =
        document.getElementById("password").value;


    if (username === "" || password === "") {

        alert("Please enter Email/Username and Password.");

        return;
    }


    localStorage.setItem("loggedIn", "true");

    localStorage.setItem("user", username);


    alert("Login successful!");

    window.location.href = "home.html";
}



// =====================================================
// LOGIN USER
// =====================================================

function loginUser(event) {

    event.preventDefault();


    const username =
        document.getElementById("username").value.trim();

    const password =
        document.getElementById("password").value;


    if (username === "" || password === "") {

        alert("Please enter Email/Username and Password.");

        return;
    }


    localStorage.setItem("loggedIn", "true");

    localStorage.setItem("user", username);


    alert("Login successful!");

    window.location.href = "home.html";
}



// =====================================================
// LOGOUT
// =====================================================

function logout() {

    localStorage.removeItem("loggedIn");

    localStorage.removeItem("user");


    alert("You have been logged out successfully!");

    window.location.href = "login.html";
}



// =====================================================
// CHECK LOGIN
// =====================================================

function checkLogin() {

    if (localStorage.getItem("loggedIn") !== "true") {

        alert("Please login first.");

        window.location.href = "login.html";
    }
}



// =====================================================
// REGISTRATION VALIDATION
// =====================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {


        const registrationForm =
            document.getElementById("registrationForm");


        if (registrationForm) {

            registrationForm.addEventListener(
                "submit",
                function (event) {

                    event.preventDefault();


                    // ================================
                    // GET REGISTRATION VALUES
                    // ================================

                    const name =
                        document
                            .getElementById("name")
                            .value
                            .trim();


                    const email =
                        document
                            .getElementById("email")
                            .value
                            .trim();


                    const mobile =
                        document
                            .getElementById("mobile")
                            .value
                            .trim();


                    const password =
                        document
                            .getElementById("password")
                            .value;


                    const confirmPassword =
                        document
                            .getElementById("confirmPassword")
                            .value;


                    const gender =
                        document.querySelector(
                            'input[name="gender"]:checked'
                        );


                    const terms =
                        document.getElementById("terms").checked;



                    // ================================
                    // NAME VALIDATION
                    // ================================

                    const namePattern =
                        /^[A-Za-z ]{2,50}$/;


                    if (!namePattern.test(name)) {

                        alert(
                            "Invalid Name!\n\n" +
                            "Name must contain only letters and spaces."
                        );

                        document
                            .getElementById("name")
                            .focus();

                        return;
                    }



                    // ================================
                    // EMAIL VALIDATION
                    // ================================

                    const emailPattern =
                        /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;


                    if (!emailPattern.test(email)) {

                        alert(
                            "Invalid Email!\n\n" +
                            "Please enter a complete email address.\n\n" +
                            "Example: abc@gmail.com"
                        );

                        document
                            .getElementById("email")
                            .focus();

                        return;
                    }



                    // ================================
                    // MOBILE VALIDATION
                    // ================================

                    const mobilePattern =
                        /^[6-9][0-9]{9}$/;


                    if (!mobilePattern.test(mobile)) {

                        alert(
                            "Invalid Mobile Number!\n\n" +
                            "Mobile number must contain exactly 10 digits " +
                            "and start with 6, 7, 8 or 9."
                        );

                        document
                            .getElementById("mobile")
                            .focus();

                        return;
                    }



                    // ================================
                    // PASSWORD VALIDATION
                    // ================================

                    const passwordPattern =
                        /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/;


                    if (!passwordPattern.test(password)) {

                        alert(
                            "Invalid Password!\n\n" +
                            "Password must contain:\n" +
                            "• Minimum 8 characters\n" +
                            "• At least 1 uppercase letter\n" +
                            "• At least 1 lowercase letter\n" +
                            "• At least 1 number"
                        );

                        document
                            .getElementById("password")
                            .focus();

                        return;
                    }



                    // ================================
                    // CONFIRM PASSWORD
                    // ================================

                    if (password !== confirmPassword) {

                        alert(
                            "Password and Confirm Password do not match."
                        );

                        document
                            .getElementById("confirmPassword")
                            .focus();

                        return;
                    }



                    // ================================
                    // GENDER
                    // ================================

                    if (!gender) {

                        alert("Please select your Gender.");

                        return;
                    }



                    // ================================
                    // TERMS
                    // ================================

                    if (!terms) {

                        alert(
                            "Please accept the Terms & Conditions."
                        );

                        return;
                    }



                    // ================================
                    // SAVE REGISTRATION
                    // ================================

                    localStorage.setItem(
                        "registered",
                        "true"
                    );


                    localStorage.setItem(
                        "loggedIn",
                        "true"
                    );


                    localStorage.setItem(
                        "user",
                        email
                    );


                    localStorage.setItem(
                        "studentName",
                        name
                    );


                    // ================================
                    // SUCCESS
                    // ================================

                    alert(
                        "Registration successful!"
                    );


                    window.location.href =
                        "home.html";

                }
            );
        }



        // =================================================
        // PROFILE VALIDATION
        // =================================================

        const profileForm =
            document.getElementById("profileForm");


        if (profileForm) {

            setupProfileValidation(profileForm);

        }

    }
);



// =====================================================
// PROFILE VALIDATION FUNCTION
// =====================================================

function setupProfileValidation(form) {


    // =================================================
    // DATE OF BIRTH - MAXIMUM TODAY
    // =================================================

    const dob =
        document.getElementById("dob");


    if (dob) {

        const today =
            new Date();


        const year =
            today.getFullYear();


        const month =
            String(today.getMonth() + 1)
                .padStart(2, "0");


        const day =
            String(today.getDate())
                .padStart(2, "0");


        dob.max =
            year + "-" + month + "-" + day;
    }



    // =================================================
    // PROFILE FORM SUBMIT
    // =================================================

    form.addEventListener(
        "submit",
        function (event) {


            event.preventDefault();



            // =================================================
            // GET PROFILE VALUES
            // =================================================

            const name =
                document
                    .getElementById("profileName")
                    .value
                    .trim();


            const enrollment =
                document
                    .getElementById("enrollment")
                    .value
                    .trim();


            const email =
                document
                    .getElementById("profileEmail")
                    .value
                    .trim();


            const mobile =
                document
                    .getElementById("profileMobile")
                    .value
                    .trim();


            const department =
                document
                    .getElementById("department")
                    .value
                    .trim();


            const semester =
                document
                    .getElementById("semester")
                    .value
                    .trim();


            const dobValue =
                document
                    .getElementById("dob")
                    .value;


            const address =
                document
                    .getElementById("address")
                    .value
                    .trim();


            const gender =
                document.querySelector(
                    'input[name="gender"]:checked'
                );



            // =================================================
            // NAME VALIDATION
            // =================================================

            const profileNamePattern =
                /^[A-Za-z ]{2,50}$/;


            if (!profileNamePattern.test(name)) {

                showProfileError(
                    "profileName",
                    "nameError",
                    "Name must contain only letters and spaces."
                );

                return;
            }



            // =================================================
            // ENROLLMENT VALIDATION
            // =================================================

            const enrollmentPattern =
                /^[A-Za-z0-9/-]{5,20}$/;


            if (!enrollmentPattern.test(enrollment)) {

                showProfileError(
                    "enrollment",
                    "enrollmentError",
                    "Enter a valid enrollment number."
                );

                return;
            }



            // =================================================
            // EMAIL VALIDATION
            // =================================================

            const profileEmailPattern =
                /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;


            if (!profileEmailPattern.test(email)) {

                showProfileError(
                    "profileEmail",
                    "emailError",
                    "Enter a valid email. Example: abc@gmail.com"
                );

                return;
            }



            // =================================================
            // MOBILE VALIDATION
            // =================================================

            const profileMobilePattern =
                /^[6-9][0-9]{9}$/;


            if (!profileMobilePattern.test(mobile)) {

                showProfileError(
                    "profileMobile",
                    "mobileError",
                    "Enter exactly 10 digits starting with 6, 7, 8 or 9."
                );

                return;
            }



            // =================================================
            // DEPARTMENT VALIDATION
            // =================================================

            const departmentPattern =
                /^[A-Za-z &.-]{2,50}$/;


            if (!departmentPattern.test(department)) {

                showProfileError(
                    "department",
                    "departmentError",
                    "Enter a valid department name."
                );

                return;
            }



            // =================================================
            // SEMESTER VALIDATION
            // =================================================

            const semesterPattern =
                /^(1|2|3|4|5|6|7|8|semester\s*(1|2|3|4|5|6|7|8))$/i;


            if (!semesterPattern.test(semester)) {

                showProfileError(
                    "semester",
                    "semesterError",
                    "Enter a valid semester from 1 to 8."
                );

                return;
            }



            // =================================================
            // GENDER VALIDATION
            // =================================================

            if (!gender) {

                showGenderError(
                    "genderError",
                    "Please select your Gender."
                );

                return;
            }



            // =================================================
            // DATE OF BIRTH VALIDATION
            // =================================================

            if (dobValue === "") {

                showProfileError(
                    "dob",
                    "dobError",
                    "Please select your Date of Birth."
                );

                return;
            }


            const selectedDate =
                new Date(dobValue);


            const currentDate =
                new Date();


            currentDate.setHours(
                0,
                0,
                0,
                0
            );


            if (selectedDate > currentDate) {

                showProfileError(
                    "dob",
                    "dobError",
                    "Date of Birth cannot be a future date."
                );

                return;
            }



            // =================================================
            // ADDRESS VALIDATION
            // =================================================

            if (address.length < 10) {

                showProfileError(
                    "address",
                    "addressError",
                    "Please enter a complete address (minimum 10 characters)."
                );

                return;
            }



            // =================================================
            // SAVE PROFILE DATA
            // =================================================

            localStorage.setItem(
                "profileName",
                name
            );


            localStorage.setItem(
                "enrollment",
                enrollment
            );


            localStorage.setItem(
                "profileEmail",
                email
            );


            localStorage.setItem(
                "profileMobile",
                mobile
            );


            localStorage.setItem(
                "department",
                department
            );


            localStorage.setItem(
                "semester",
                semester
            );


            localStorage.setItem(
                "gender",
                gender.value
            );


            localStorage.setItem(
                "dob",
                dobValue
            );


            localStorage.setItem(
                "address",
                address
            );


            localStorage.setItem(
                "studentName",
                name
            );



            // =================================================
            // SUCCESS
            // =================================================

            alert(
                "Profile updated successfully!"
            );

        }
    );
}



// =====================================================
// PROFILE ERROR FUNCTION
// =====================================================

function showProfileError(
    inputId,
    errorId,
    message
) {

    const input =
        document.getElementById(inputId);


    const error =
        document.getElementById(errorId);


    if (input) {

        input.classList.add(
            "input-error"
        );

        input.focus();
    }


    if (error) {

        error.innerText =
            message;

        error.style.display =
            "block";
    }


    // If error-message element is not present,
    // still show alert.

    if (!error) {

        alert(message);
    }
}



// =====================================================
// GENDER ERROR
// =====================================================

function showGenderError(
    errorId,
    message
) {

    const error =
        document.getElementById(errorId);


    if (error) {

        error.innerText =
            message;

        error.style.display =
            "block";

    } else {

        alert(message);
    }
}



// =====================================================
// PROFILE IMAGE VALIDATION + PREVIEW
// =====================================================

function loadImage(event) {

    const file =
        event.target.files[0];


    const error =
        document.getElementById("photoError");


    if (error) {

        error.innerText = "";

        error.style.display =
            "none";
    }


    if (!file) {

        return;
    }



    // =================================================
    // IMAGE TYPE
    // =================================================

    if (!file.type.startsWith("image/")) {

        if (error) {

            error.innerText =
                "Please select a valid image file.";

            error.style.display =
                "block";

        } else {

            alert(
                "Please select a valid image file."
            );
        }


        event.target.value = "";

        return;
    }



    // =================================================
    // IMAGE SIZE
    // =================================================

    if (file.size > 2 * 1024 * 1024) {

        if (error) {

            error.innerText =
                "Profile photo must be less than 2 MB.";

            error.style.display =
                "block";

        } else {

            alert(
                "Profile photo must be less than 2 MB."
            );
        }


        event.target.value = "";

        return;
    }



    // =================================================
    // DISPLAY IMAGE
    // =================================================

    const image =
        document.getElementById("preview");


    const defaultProfile =
        document.getElementById("defaultProfile");


    if (image) {

        image.src =
            URL.createObjectURL(file);

        image.style.display =
            "block";
    }


    if (defaultProfile) {

        defaultProfile.style.display =
            "none";
    }

}