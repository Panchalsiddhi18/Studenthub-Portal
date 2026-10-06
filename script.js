/* =========================================================
   STUDENTHUB - COMPLETE CLEAN JAVASCRIPT
   ========================================================= */


/* =========================================================
   1. LOGIN SYSTEM
   ========================================================= */

function loginUser(event) {

    event.preventDefault();

    const emailOrUsername =
        document.getElementById("username").value.trim();

    const password =
        document.getElementById("password").value;

    const emailRegex =
        /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

    const usernameRegex =
        /^[A-Za-z][A-Za-z0-9._-]{2,29}$/;

    const passwordRegex =
        /^(?=.*[A-Za-z])(?=.*[0-9]).{6,}$/;


    if (
        !emailRegex.test(emailOrUsername) &&
        !usernameRegex.test(emailOrUsername)
    ) {

        alert("Please enter a valid email or username.");
        return;
    }


    if (!passwordRegex.test(password)) {

        alert(
            "Password must contain at least 6 characters and include letters and numbers."
        );

        return;
    }


    localStorage.setItem("loggedIn", "true");
    localStorage.setItem("user", emailOrUsername);
    localStorage.setItem("studentName", emailOrUsername);


    alert("Login successful!");

    window.location.href = "Home.html";
}


/* =========================================================
   2. LOGOUT SYSTEM
   ========================================================= */

function logout() {

    localStorage.removeItem("loggedIn");
    localStorage.removeItem("user");

    alert("You have been logged out.");

    window.location.href = "Login.html";
}


/* =========================================================
   3. LOGIN CHECK
   ========================================================= */

function checkLogin() {

    const loggedIn =
        localStorage.getItem("loggedIn");

    if (loggedIn !== "true") {

        window.location.href = "Login.html";
    }
}


/* =========================================================
   4. REGISTRATION VALIDATION
   ========================================================= */

function setupRegistrationValidation() {

    const registrationForm =
        document.getElementById("registrationForm");

    if (!registrationForm) return;


    registrationForm.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const name =
                document.getElementById("name").value.trim();

            const email =
                document.getElementById("email").value.trim();

            const mobile =
                document.getElementById("mobile").value.trim();

            const password =
                document.getElementById("password").value;

            const confirmPassword =
                document.getElementById("confirmPassword").value;

            const course =
                document.getElementById("course").value;

            const year =
                document.getElementById("year").value;

            const gender =
                document.querySelector(
                    'input[name="gender"]:checked'
                );

            const terms =
                document.getElementById("terms");


            const nameRegex =
                /^[A-Za-z ]{2,50}$/;

            const emailRegex =
                /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

            const mobileRegex =
                /^[6-9][0-9]{9}$/;

            const passwordRegex =
                /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,}$/;


            if (!nameRegex.test(name)) {

                alert("Please enter a valid name.");
                return;
            }


            if (!emailRegex.test(email)) {

                alert("Please enter a valid email address.");
                return;
            }


            if (!mobileRegex.test(mobile)) {

                alert(
                    "Please enter a valid 10-digit mobile number."
                );

                return;
            }


            if (!passwordRegex.test(password)) {

                alert(
                    "Password must contain at least 8 characters, one uppercase letter, one lowercase letter and one number."
                );

                return;
            }


            if (password !== confirmPassword) {

                alert("Passwords do not match.");
                return;
            }


            if (!course) {

                alert("Please select your course.");
                return;
            }


            if (!year) {

                alert("Please select your year.");
                return;
            }


            if (!gender) {

                alert("Please select your gender.");
                return;
            }


            if (!terms || !terms.checked) {

                alert(
                    "Please accept the terms and conditions."
                );

                return;
            }


            localStorage.setItem(
                "registrationData",
                JSON.stringify({
                    name: name,
                    email: email,
                    mobile: mobile,
                    course: course,
                    year: year,
                    gender: gender.value
                })
            );


            localStorage.setItem(
                "studentName",
                name
            );


            alert("Registration successful!");

            window.location.href = "Home.html";
        }
    );
}


/* =========================================================
   5. PROFILE VALIDATION
   ========================================================= */

function setupProfileValidation(form) {

    if (!form) return;


    form.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const name =
                document.getElementById("profileName")?.value.trim();

            const enrollment =
                document.getElementById("enrollment")?.value.trim();

            const email =
                document.getElementById("profileEmail")?.value.trim();

            const mobile =
                document.getElementById("profileMobile")?.value.trim();

            const department =
                document.getElementById("department")?.value.trim();

            const semester =
                document.getElementById("semester")?.value.trim();

            const dob =
                document.getElementById("dob")?.value;

            const gender =
                document.querySelector(
                    'input[name="gender"]:checked'
                );

            const address =
                document.getElementById("address")?.value.trim();


            const nameRegex =
                /^[A-Za-z ]{2,50}$/;

            const enrollmentRegex =
                /^[A-Za-z0-9/-]{5,20}$/;

            const emailRegex =
                /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

            const mobileRegex =
                /^[6-9][0-9]{9}$/;


            if (name && !nameRegex.test(name)) {

                showProfileError(
                    "profileName",
                    "Please enter a valid name."
                );

                return;
            }


            if (
                enrollment &&
                !enrollmentRegex.test(enrollment)
            ) {

                showProfileError(
                    "enrollment",
                    "Please enter a valid enrollment number."
                );

                return;
            }


            if (email && !emailRegex.test(email)) {

                showProfileError(
                    "profileEmail",
                    "Please enter a valid email."
                );

                return;
            }


            if (mobile && !mobileRegex.test(mobile)) {

                showProfileError(
                    "profileMobile",
                    "Please enter a valid 10-digit mobile number."
                );

                return;
            }


            if (!department) {

                showProfileError(
                    "department",
                    "Please enter your department."
                );

                return;
            }


            if (!semester) {

                showProfileError(
                    "semester",
                    "Please enter your semester."
                );

                return;
            }


            if (!dob) {

                showProfileError(
                    "dob",
                    "Please select your date of birth."
                );

                return;
            }


            if (!gender) {

                showGenderError(
                    "Please select your gender."
                );

                return;
            }


            if (!address) {

                showProfileError(
                    "address",
                    "Please enter your address."
                );

                return;
            }


            const profileData = {

                name: name,
                enrollment: enrollment,
                email: email,
                mobile: mobile,
                department: department,
                semester: semester,
                dob: dob,
                gender: gender.value,
                address: address

            };


            localStorage.setItem(
                "profileData",
                JSON.stringify(profileData)
            );


            localStorage.setItem(
                "studentName",
                name
            );


            alert(
                "Profile updated successfully!"
            );


            loadProfileData();

            setupSharedProfilePhoto();

            updateUserName();
        }
    );
}


/* =========================================================
   6. PROFILE ERROR FUNCTIONS
   ========================================================= */

function showProfileError(inputId, message) {

    const input =
        document.getElementById(inputId);

    if (!input) return;


    input.classList.add("input-error");


    let error =
        input.parentElement.querySelector(
            ".error-message"
        );


    if (!error) {

        error =
            document.createElement("small");

        error.className =
            "error-message";

        input.parentElement.appendChild(error);
    }


    error.textContent = message;
}


function showGenderError(message) {

    const genderInputs =
        document.querySelectorAll(
            'input[name="gender"]'
        );

    if (!genderInputs.length) {

        alert(message);
        return;
    }


    const genderContainer =
        genderInputs[0].parentElement;


    let error =
        genderContainer.querySelector(
            ".error-message"
        );


    if (!error) {

        error =
            document.createElement("small");

        error.className =
            "error-message";

        genderContainer.appendChild(error);
    }


    error.textContent = message;
}


/* =========================================================
   7. PROFILE IMAGE UPLOAD
   ========================================================= */

function loadImage(event) {

    const file =
        event.target.files[0];

    if (!file) return;


    if (!file.type.startsWith("image/")) {

        alert(
            "Please select a valid image file."
        );

        event.target.value = "";

        return;
    }


    if (file.size > 2 * 1024 * 1024) {

        alert(
            "Image size must be less than 2 MB."
        );

        event.target.value = "";

        return;
    }


    const reader =
        new FileReader();


    reader.onload =
        function (e) {

            const preview =
                document.getElementById(
                    "preview"
                );

            const defaultProfile =
                document.getElementById(
                    "defaultProfile"
                );


            if (preview) {

                preview.src =
                    e.target.result;

                preview.style.display =
                    "block";
            }


            if (defaultProfile) {

                defaultProfile.style.display =
                    "none";
            }


            const img =
                new Image();


            img.onload =
                function () {

                    const canvas =
                        document.createElement(
                            "canvas"
                        );


                    const maxSize = 500;

                    let width =
                        img.width;

                    let height =
                        img.height;


                    if (width > height) {

                        if (width > maxSize) {

                            height =
                                height *
                                (maxSize / width);

                            width =
                                maxSize;
                        }

                    } else {

                        if (height > maxSize) {

                            width =
                                width *
                                (maxSize / height);

                            height =
                                maxSize;
                        }
                    }


                    canvas.width =
                        width;

                    canvas.height =
                        height;


                    const ctx =
                        canvas.getContext(
                            "2d"
                        );


                    ctx.drawImage(
                        img,
                        0,
                        0,
                        width,
                        height
                    );


                    const compressedImage =
                        canvas.toDataURL(
                            "image/jpeg",
                            0.8
                        );


                    localStorage.setItem(
                        "profilePhoto",
                        compressedImage
                    );


                    setupSharedProfilePhoto();

                    updateAllProfilePhotos();
                };


            img.src =
                e.target.result;
        };


    reader.readAsDataURL(file);
}


/* =========================================================
   8. SHARED PROFILE PHOTO
   ========================================================= */

function setupSharedProfilePhoto() {

    const savedPhoto =
        localStorage.getItem(
            "profilePhoto"
        );

    if (!savedPhoto) return;


    const images =
        document.querySelectorAll(
            ".profile-photo, .user-profile-img, .shared-profile-photo"
        );


    images.forEach(
        function (img) {

            img.src =
                savedPhoto;
        }
    );
}


/* =========================================================
   9. LOAD PROFILE DATA
   ========================================================= */

function loadProfileData() {

    const savedData =
        localStorage.getItem(
            "profileData"
        );


    if (savedData) {

        const data =
            JSON.parse(savedData);


        const fields = {

            profileName: data.name,
            enrollment: data.enrollment,
            profileEmail: data.email,
            profileMobile: data.mobile,
            department: data.department,
            semester: data.semester,
            dob: data.dob,
            address: data.address

        };


        Object.keys(fields).forEach(
            function (id) {

                const element =
                    document.getElementById(id);


                if (
                    element &&
                    fields[id] !== undefined
                ) {

                    element.value =
                        fields[id];
                }
            }
        );


        if (data.gender) {

            const genderInput =
                document.querySelector(
                    `input[name="gender"][value="${data.gender}"]`
                );


            if (genderInput) {

                genderInput.checked =
                    true;
            }
        }
    }


    const savedPhoto =
        localStorage.getItem(
            "profilePhoto"
        );


    if (savedPhoto) {

        const preview =
            document.getElementById(
                "preview"
            );

        const defaultProfile =
            document.getElementById(
                "defaultProfile"
            );


        if (preview) {

            preview.src =
                savedPhoto;

            preview.style.display =
                "block";
        }


        if (defaultProfile) {

            defaultProfile.style.display =
                "none";
        }
    }
}


/* =========================================================
   10. PROFILE PHOTO STORAGE
   ========================================================= */

function setupProfilePhotoStorage() {

    const savedPhoto =
        localStorage.getItem(
            "profilePhoto"
        );

    if (!savedPhoto) return;


    const preview =
        document.getElementById(
            "preview"
        );

    const defaultProfile =
        document.getElementById(
            "defaultProfile"
        );


    if (preview) {

        preview.src =
            savedPhoto;

        preview.style.display =
            "block";
    }


    if (defaultProfile) {

        defaultProfile.style.display =
            "none";
    }
}


/* =========================================================
   11. HAMBURGER MENU
   ========================================================= */

function setupHamburgerMenu() {

    const nav =
        document.querySelector("nav");

    if (!nav) return;


    if (
        document.getElementById(
            "hamburgerMenu"
        )
    ) {

        return;
    }


    const hamburgerButton =
        document.createElement(
            "button"
        );


    hamburgerButton.id =
        "hamburgerMenu";

    hamburgerButton.className =
        "hamburger-menu";

    hamburgerButton.innerHTML =
        "☰";


    nav.insertBefore(
        hamburgerButton,
        nav.firstChild
    );


    hamburgerButton.addEventListener(
        "click",
        function () {

            nav.classList.toggle(
                "nav-open"
            );


            hamburgerButton.innerHTML =
                nav.classList.contains(
                    "nav-open"
                )
                    ? "✕"
                    : "☰";
        }
    );
}


/* =========================================================
   12. DARK / LIGHT THEME
   ========================================================= */

function setupThemeToggle() {

    const nav =
        document.querySelector("nav");

    if (!nav) return;


    if (
        document.getElementById(
            "themeToggle"
        )
    ) {

        return;
    }


    const themeButton =
        document.createElement(
            "button"
        );


    themeButton.id =
        "themeToggle";


    const savedTheme =
        localStorage.getItem(
            "studentHubTheme"
        );


    if (savedTheme === "dark") {

        document.body.classList.add(
            "dark-theme"
        );


        themeButton.innerHTML =
            "☀️ Light";

    } else {

        themeButton.innerHTML =
            "🌙 Dark";
    }


    themeButton.addEventListener(
        "click",
        function () {

            document.body.classList.toggle(
                "dark-theme"
            );


            if (
                document.body.classList.contains(
                    "dark-theme"
                )
            ) {

                localStorage.setItem(
                    "studentHubTheme",
                    "dark"
                );


                themeButton.innerHTML =
                    "☀️ Light";

            } else {

                localStorage.setItem(
                    "studentHubTheme",
                    "light"
                );


                themeButton.innerHTML =
                    "🌙 Dark";
            }
        }
    );


    nav.appendChild(
        themeButton
    );
}


/* =========================================================
   13. NOTIFICATION BANNER
   ========================================================= */

function createNotificationBanner() {

    if (
        document.getElementById(
            "studentNotification"
        )
    ) {

        return;
    }


    const banner =
        document.createElement(
            "div"
        );


    banner.id =
        "studentNotification";

    banner.className =
        "student-notification";


    banner.innerHTML = `
        <span>
            📢 StudentHub Notice: Check the latest student updates!
        </span>

        <button id="closeNotification">
            ×
        </button>
    `;


    document.body.prepend(
        banner
    );


    const closeButton =
        document.getElementById(
            "closeNotification"
        );


    if (closeButton) {

        closeButton.addEventListener(
            "click",
            function () {

                banner.style.display =
                    "none";
            }
        );
    }


    setTimeout(
        function () {

            if (banner) {

                banner.style.display =
                    "none";
            }

        },
        8000
    );
}


/* =========================================================
   14. MODAL POPUP
   ========================================================= */

function setupModalPopup() {

    const modal =
        document.getElementById(
            "studentModal"
        );


    if (!modal) return;


    const closeButton =
        modal.querySelector(
            ".modal-close"
        );


    if (closeButton) {

        closeButton.addEventListener(
            "click",
            function () {

                closeStudentModal(
                    modal
                );
            }
        );
    }


    modal.addEventListener(
        "click",
        function (event) {

            if (
                event.target === modal
            ) {

                closeStudentModal(
                    modal
                );
            }
        }
    );


    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                modal.classList.contains(
                    "show"
                )
            ) {

                closeStudentModal(
                    modal
                );
            }
        }
    );


    const modalButtons =
        document.querySelectorAll(
            "[data-modal], .open-modal, #openModal"
        );


    modalButtons.forEach(
        function (button) {

            button.addEventListener(
                "click",
                function () {

                    openStudentModal(
                        modal
                    );
                }
            );
        }
    );
}


function openStudentModal(modal) {

    if (!modal) {

        modal =
            document.getElementById(
                "studentModal"
            );
    }


    if (!modal) return;


    modal.classList.add(
        "show"
    );

    modal.style.display =
        "flex";


    document.body.style.overflow =
        "hidden";
}


function closeStudentModal(modal) {

    if (!modal) {

        modal =
            document.getElementById(
                "studentModal"
            );
    }


    if (!modal) return;


    modal.classList.remove(
        "show"
    );

    modal.style.display =
        "none";


    document.body.style.overflow =
        "";
}


/* =========================================================
   15. CONTENT SLIDER
   ========================================================= */

function setupContentSlider() {

    const sliders =
        document.querySelectorAll(
            ".content-slider, .slider"
        );


    sliders.forEach(
        function (slider) {

            setupSingleSlider(
                slider
            );
        }
    );
}


function setupSingleSlider(slider) {

    const slides =
        slider.querySelectorAll(
            ".slide"
        );


    if (slides.length <= 1) return;


    let currentSlide = 0;


    slides.forEach(
        function (slide, index) {

            slide.style.display =
                index === 0
                    ? "block"
                    : "none";
        }
    );


    setInterval(
        function () {

            slides[currentSlide].style.display =
                "none";


            currentSlide =
                (currentSlide + 1) %
                slides.length;


            slides[currentSlide].style.display =
                "block";

        },
        5000
    );
}


/* =========================================================
   16. FAQ
   ========================================================= */

function setupFAQ() {

    const faqItems =
        document.querySelectorAll(
            ".faq-item"
        );


    faqItems.forEach(
        function (item) {

            const question =
                item.querySelector(
                    ".faq-question"
                );

            const answer =
                item.querySelector(
                    ".faq-answer"
                );


            if (
                !question ||
                !answer
            ) {

                return;
            }


            answer.style.display =
                "none";


            question.addEventListener(
                "click",
                function () {

                    const isActive =
                        item.classList.contains(
                            "active"
                        );


                    faqItems.forEach(
                        function (otherItem) {

                            otherItem.classList.remove(
                                "active"
                            );


                            const otherAnswer =
                                otherItem.querySelector(
                                    ".faq-answer"
                                );


                            if (otherAnswer) {

                                otherAnswer.style.display =
                                    "none";
                            }
                        }
                    );


                    if (!isActive) {

                        item.classList.add(
                            "active"
                        );


                        answer.style.display =
                            "block";
                    }
                }
            );
        }
    );
}


/* =========================================================
   17. PROFILE NAME UPDATE
   ========================================================= */

function updateUserName() {

    const studentName =
        localStorage.getItem(
            "studentName"
        );


    if (!studentName) return;


    const nameElements =
        document.querySelectorAll(
            ".student-name, #studentName, .user-name"
        );


    nameElements.forEach(
        function (element) {

            element.textContent =
                studentName;
        }
    );
}


/* =========================================================
   18. UPDATE ALL PROFILE PHOTOS
   ========================================================= */

function updateAllProfilePhotos() {

    const savedPhoto =
        localStorage.getItem(
            "profilePhoto"
        );


    if (!savedPhoto) return;


    const images =
        document.querySelectorAll(
            'img[src*="profile"], img[src*="user"], img[src*="avatar"], .profile-photo'
        );


    images.forEach(
        function (img) {

            img.src =
                savedPhoto;
        }
    );
}


/* =========================================================
   19. LOGOUT BUTTONS
   ========================================================= */

function setupLogoutButtons() {

    const logoutButtons =
        document.querySelectorAll(
            ".logout-btn, [href='Login.html']"
        );


    logoutButtons.forEach(
        function (button) {

            if (
                button.textContent
                    .trim()
                    .toLowerCase()
                    .includes(
                        "logout"
                    )
            ) {

                button.addEventListener(
                    "click",
                    function (event) {

                        event.preventDefault();

                        logout();
                    }
                );
            }
        }
    );
}


/* =========================================================
   20. PRIVATE PAGE PROTECTION
   ========================================================= */

function protectPrivatePage() {

    const privatePages = [

        "dashboard.html",
        "Dashboard.html",

        "profile.html",
        "Profile.html",

        "events.html",
        "Events.html",

        "feedback.html",
        "Feedback.html",

        "contact.html",
        "Contact.html",

        "timetable.html",
        "Timetable.html",

        "attendance.html",
        "Attendance.html",

        "fees.html",
        "Fees.html",

        "result.html",
        "Result.html"

    ];


    const currentPage =
        window.location.pathname
            .split("/")
            .pop();


    if (
        privatePages.includes(
            currentPage
        )
    ) {

        const loggedIn =
            localStorage.getItem(
                "loggedIn"
            );


        if (
            loggedIn !== "true"
        ) {

            window.location.href =
                "Login.html";
        }
    }
}


/* =========================================================
   21. LOGIN PAGE REDIRECTION
   ========================================================= */

function redirectLoggedInUser() {

    /*
       Kept intentionally empty.
       Login page can still be opened
       even if the user is already logged in.
    */
}


/* =========================================================
   22. REMOVE INPUT ERRORS
   ========================================================= */

function setupInputErrorRemoval() {

    const inputs =
        document.querySelectorAll(
            "input, select, textarea"
        );


    inputs.forEach(
        function (input) {

            input.addEventListener(
                "input",
                function () {

                    input.classList.remove(
                        "input-error"
                    );


                    const error =
                        input.parentElement
                            ?.querySelector(
                                ".error-message"
                            );


                    if (error) {

                        error.remove();
                    }
                }
            );
        }
    );
}


/* =========================================================
   23. PROFILE IMAGE INPUT
   ========================================================= */

function setupProfileImageInput() {

    const imageInput =
        document.getElementById(
            "profileImage"
        ) ||
        document.querySelector(
            'input[type="file"]'
        );


    if (!imageInput) return;


    imageInput.addEventListener(
        "change",
        loadImage
    );
}


/* =========================================================
   24. SMOOTH SCROLL
   ========================================================= */

function setupSmoothScroll() {

    const links =
        document.querySelectorAll(
            'a[href^="#"]'
        );


    links.forEach(
        function (link) {

            link.addEventListener(
                "click",
                function (event) {

                    const targetId =
                        link.getAttribute(
                            "href"
                        );


                    if (
                        !targetId ||
                        targetId === "#"
                    ) {

                        return;
                    }


                    const target =
                        document.querySelector(
                            targetId
                        );


                    if (target) {

                        event.preventDefault();


                        target.scrollIntoView({
                            behavior: "smooth"
                        });
                    }
                }
            );
        }
    );
}


/* =========================================================
   25. CURRENT YEAR
   ========================================================= */

function setCurrentYear() {

    const yearElements =
        document.querySelectorAll(
            "#currentYear, .current-year"
        );


    yearElements.forEach(
        function (element) {

            element.textContent =
                new Date().getFullYear();
        }
    );
}


/* =========================================================
   26. HERO IMAGE SLIDER
   ========================================================= */

let currentHeroSlide = 0;


const heroImages = [

    "images/student_portal_sharp_rectangle.png",

    "images/login page.png",

    "images/student_portal_sharp_rectangle.png"

];


function showHeroSlide(index) {

    const image =
        document.getElementById(
            "heroSliderImage"
        );


    const dots =
        document.querySelectorAll(
            ".hero-dot"
        );


    if (!image) return;


    if (
        index >= heroImages.length
    ) {

        currentHeroSlide = 0;

    }
    else if (
        index < 0
    ) {

        currentHeroSlide =
            heroImages.length - 1;

    }
    else {

        currentHeroSlide =
            index;
    }


    image.src =
        heroImages[
            currentHeroSlide
        ];


    dots.forEach(
        function (dot, i) {

            dot.classList.toggle(
                "active",
                i === currentHeroSlide
            );
        }
    );
}


function nextHeroSlide() {

    showHeroSlide(
        currentHeroSlide + 1
    );
}


function previousHeroSlide() {

    showHeroSlide(
        currentHeroSlide - 1
    );
}


/* =========================================================
   27. MAIN INITIALIZATION
   ========================================================= */

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* Registration */

        setupRegistrationValidation();


        /* Profile */

        const profileForm =
            document.getElementById(
                "profileForm"
            );


        if (profileForm) {

            setupProfileValidation(
                profileForm
            );
        }


        /* Events */

        loadEventsFromJSON();

        setupEventSearch();

        setupEventFilter();

        setupEventSorting();


        /* Profile data */

        loadProfileData();

        setupProfilePhotoStorage();

        setupSharedProfilePhoto();


        /* Theme */

        setupThemeToggle();


        /* Notification */

        createNotificationBanner();


        /* Modal */

        setupModalPopup();


        /* Slider */

        setupContentSlider();


        /* FAQ */

        setupFAQ();


        /* Hamburger */

        setupHamburgerMenu();


        /* User information */

        updateUserName();

        updateAllProfilePhotos();


        /* Logout */

        setupLogoutButtons();


        /* Page protection */

        protectPrivatePage();

        redirectLoggedInUser();


        /* Form errors */

        setupInputErrorRemoval();


        /* Profile image */

        setupProfileImageInput();


        /* Smooth scrolling */

        setupSmoothScroll();


        /* Footer year */

        setCurrentYear();

    }
);


/* =========================================================
   28. UPDATE AFTER PAGE LOAD
   ========================================================= */

window.addEventListener(
    "load",
    function () {

        setupSharedProfilePhoto();

        updateAllProfilePhotos();

        updateUserName();

    }
);


/* =========================================================
   29. EVENTS - FETCH, SEARCH, FILTER,
       SORTING & PAGINATION
   ========================================================= */

let allEvents = [];

let currentEventPage = 1;

const eventsPerPage = 5;


/* =========================================================
   LOAD EVENTS FROM JSON
   ========================================================= */

function loadEventsFromJSON() {

    const eventsTableBody =
        document.getElementById(
            "eventsTableBody"
        );


    if (!eventsTableBody) {

        return;
    }


    fetch("data/events.json")

        .then(
            function (response) {

                if (!response.ok) {

                    throw new Error(
                        "Unable to load events.json"
                    );
                }


                return response.json();

            }
        )

        .then(
            function (events) {

                allEvents = events;

                currentEventPage = 1;

                renderEvents();

            }
        )

        .catch(
            function (error) {

                console.error(
                    "Error loading events:",
                    error
                );


                eventsTableBody.innerHTML = `
                    <tr>
                        <td colspan="3">
                            Unable to load events.
                        </td>
                    </tr>
                `;
            }
        );
}


/* =========================================================
   RENDER EVENTS
   ========================================================= */

function renderEvents() {

    const eventsTableBody =
        document.getElementById(
            "eventsTableBody"
        );


    if (!eventsTableBody) {

        return;
    }


    /* ---------- SEARCH ---------- */

    const searchInput =
        document.getElementById(
            "eventSearch"
        );


    const searchText =
        searchInput
            ? searchInput.value
                .toLowerCase()
                .trim()
            : "";


    /* ---------- CATEGORY FILTER ---------- */

    const categoryFilter =
        document.getElementById(
            "eventCategory"
        );


    const selectedCategory =
        categoryFilter
            ? categoryFilter.value.toLowerCase()
            : "all";


    /* ---------- SORTING ---------- */

    const sortSelect =
        document.getElementById(
            "eventSort"
        );


    const selectedSort =
        sortSelect
            ? sortSelect.value
            : "default";


    /* ---------- FILTER EVENTS ---------- */

    let filteredEvents =
        allEvents.filter(
            function (event) {

                const eventText = (

                    event.title +
                    " " +
                    event.date +
                    " " +
                    event.venue +
                    " " +
                    event.category

                ).toLowerCase();


                const matchesSearch =
                    eventText.includes(
                        searchText
                    );


                const matchesCategory =
                    selectedCategory === "all" ||
                    event.category.toLowerCase() ===
                    selectedCategory;


                return (
                    matchesSearch &&
                    matchesCategory
                );

            }
        );


    /* ---------- SORT EVENTS ---------- */

    if (
        selectedSort === "nameAsc"
    ) {

        filteredEvents.sort(
            function (a, b) {

                return a.title.localeCompare(
                    b.title
                );

            }
        );
    }


    if (
        selectedSort === "nameDesc"
    ) {

        filteredEvents.sort(
            function (a, b) {

                return b.title.localeCompare(
                    a.title
                );

            }
        );
    }


    if (
        selectedSort === "dateAsc"
    ) {

        filteredEvents.sort(
            function (a, b) {

                return new Date(a.date) -
                       new Date(b.date);

            }
        );
    }


    if (
        selectedSort === "dateDesc"
    ) {

        filteredEvents.sort(
            function (a, b) {

                return new Date(b.date) -
                       new Date(a.date);

            }
        );
    }


    /* ---------- PAGINATION ---------- */

    const totalPages =
        Math.ceil(
            filteredEvents.length /
            eventsPerPage
        );


    if (
        totalPages > 0 &&
        currentEventPage > totalPages
    ) {

        currentEventPage =
            totalPages;
    }


    if (totalPages === 0) {

        eventsTableBody.innerHTML = `
            <tr>
                <td colspan="3">
                    No events found.
                </td>
            </tr>
        `;


        renderEventPagination(0);

        return;
    }


    const startIndex =
        (currentEventPage - 1) *
        eventsPerPage;


    const endIndex =
        startIndex +
        eventsPerPage;


    const eventsToDisplay =
        filteredEvents.slice(
            startIndex,
            endIndex
        );


    /* ---------- DISPLAY EVENTS ---------- */

    eventsTableBody.innerHTML = "";


    eventsToDisplay.forEach(
        function (event) {

            const row =
                document.createElement(
                    "tr"
                );


            row.setAttribute(
                "data-category",
                event.category.toLowerCase()
            );


            row.innerHTML = `
                <td>${event.title}</td>
                <td>${event.date}</td>
                <td>${event.venue}</td>
            `;


            eventsTableBody.appendChild(
                row
            );

        }
    );


    /* ---------- PAGINATION BUTTONS ---------- */

    renderEventPagination(
        totalPages
    );
}


/* =========================================================
   EVENT SEARCH
   ========================================================= */

function setupEventSearch() {

    const searchInput =
        document.getElementById(
            "eventSearch"
        );


    if (!searchInput) {

        return;
    }


    searchInput.addEventListener(
        "input",
        function () {

            currentEventPage = 1;

            renderEvents();

        }
    );
}


/* =========================================================
   EVENT CATEGORY FILTER
   ========================================================= */

function setupEventFilter() {

    const filter =
        document.getElementById(
            "eventCategory"
        );


    if (!filter) {

        return;
    }


    filter.addEventListener(
        "change",
        function () {

            currentEventPage = 1;

            renderEvents();

        }
    );
}


/* =========================================================
   EVENT SORTING
   ========================================================= */

function setupEventSorting() {

    const sortSelect =
        document.getElementById(
            "eventSort"
        );


    if (!sortSelect) {

        return;
    }


    sortSelect.addEventListener(
        "change",
        function () {

            currentEventPage = 1;

            renderEvents();

        }
    );
}


/* =========================================================
   EVENT PAGINATION
   ========================================================= */

function renderEventPagination(
    totalPages
) {

    const pagination =
        document.getElementById(
            "eventPagination"
        );


    if (!pagination) {

        return;
    }


    pagination.innerHTML = "";


    if (totalPages <= 1) {

        return;
    }


    /* ---------- PREVIOUS BUTTON ---------- */

    const previousButton =
        document.createElement(
            "button"
        );


    previousButton.textContent =
        "Previous";


    previousButton.disabled =
        currentEventPage === 1;


    previousButton.addEventListener(
        "click",
        function () {

            if (
                currentEventPage > 1
            ) {

                currentEventPage--;

                renderEvents();
            }

        }
    );


    pagination.appendChild(
        previousButton
    );


    /* ---------- PAGE NUMBERS ---------- */

    for (
        let page = 1;
        page <= totalPages;
        page++
    ) {

        const pageButton =
            document.createElement(
                "button"
            );


        pageButton.textContent =
            page;


        if (
            page === currentEventPage
        ) {

            pageButton.classList.add(
                "active"
            );
        }


        pageButton.addEventListener(
            "click",
            function () {

                currentEventPage =
                    page;

                renderEvents();

            }
        );


        pagination.appendChild(
            pageButton
        );
    }


    /* ---------- NEXT BUTTON ---------- */

    const nextButton =
        document.createElement(
            "button"
        );


    nextButton.textContent =
        "Next";


    nextButton.disabled =
        currentEventPage === totalPages;


    nextButton.addEventListener(
        "click",
        function () {

            if (
                currentEventPage <
                totalPages
            ) {

                currentEventPage++;

                renderEvents();
            }

        }
    );


    pagination.appendChild(
        nextButton
    );
}