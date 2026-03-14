

//Toggle Between SIgn In & Sign Up Forms
function toggleForms() {
    var signinForm = document.getElementById("signin-form");
    var signUpForm = document.getElementById("signup-form");

    signinForm.classList.toggle("hidden");
    signinForm.classList.toggle("active");
    signUpForm.classList.toggle("hidden");
    signUpForm.classList.toggle("active");


}

//toggle between password & text in password inputs
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);

    if (input.type == "password") {
        input.type = "text";
        btn.textContent = "🌝";
    } else {
        input.type = "password";
        btn.textContent = "👁️"
    }
} 