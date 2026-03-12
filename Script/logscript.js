// LOGIN PAGE ELEMENTS
const email = document.getElementById("email");
const password = document.getElementById("password");
const loginBtn = document.getElementById("loginBtn");

const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");
const loginError = document.getElementById("loginError");

const form = document.getElementById("loginForm");
const togglePassword = document.getElementById("toggleLoginPassword");

// FAKE CREDENTIALS (for testing)
const validEmail = "admin@gmail.com";
const validPassword = "Admin123";

// VALIDATION FUNCTIONS
function validateEmail() {
  const pattern = /^\S+@\S+\.\S+$/;
  if (email.value.trim() === "") {
    emailError.textContent = "Email is required";
    return false;
  } else if (!pattern.test(email.value.trim())) {
    emailError.textContent = "Invalid email format";
    return false;
  } else {
    emailError.textContent = "";
    return true;
  }
}

function validatePassword() {
  if (password.value.trim() === "") {
    passwordError.textContent = "Password is required";
    return false;
  } else {
    passwordError.textContent = "";
    return true;
  }
}

// ENABLE BUTTON
function checkForm() {
  loginError.textContent = ""; // clear general error
  if (validateEmail() && validatePassword()) {
    loginBtn.disabled = false;
  } else {
    loginBtn.disabled = true;
  }
}

// EVENTS
email.addEventListener("blur", validateEmail);
password.addEventListener("blur", validatePassword);

email.addEventListener("input", checkForm);
password.addEventListener("input", checkForm);

// SHOW/HIDE PASSWORD
togglePassword.addEventListener("click", () => {
  password.type = password.type === "password" ? "text" : "password";
});

// FORM SUBMIT
form.addEventListener("submit", (e) => {
  e.preventDefault();
  if (validateEmail() && validatePassword()) {
    if (email.value === validEmail && password.value === validPassword) {
      alert("Login successful!");
      form.reset();
      loginBtn.disabled = true;
    } else {
      loginError.textContent = "Invalid email or password";
    }
  }
});
