const form = document.getElementById("registerForm");
const fullName = document.getElementById("fullName");
const email = document.getElementById("email");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const registerBtn = document.getElementById("registerBtn");
const successMsg = document.getElementById("successMessage");

const namePattern = /^[A-Za-z\s]{3,}$/;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

function setError(input, message) {
  const inputGroup = input.closest(".input-group");
  const error = inputGroup.querySelector(".error");

  error.textContent = message;
  input.style.border = "1px solid red";
}

function setSuccess(input) {
  const inputGroup = input.closest(".input-group");
  const error = inputGroup.querySelector(".error");

  error.textContent = "";
  input.style.border = "1px solid green";
}

function validateInputs() {
  let isValid = true;

  if (!namePattern.test(fullName.value.trim())) {
    setError(
      fullName,
      "Full Name must be at least 3 characters (letters and spaces only).",
    );
    isValid = false;
  } else {
    setSuccess(fullName);
  }

  if (!emailPattern.test(email.value.trim())) {
    setError(email, "Please enter a valid email address.");
    isValid = false;
  } else {
    setSuccess(email);
  }

  if (!passwordPattern.test(password.value)) {
    setError(
      password,
      "Password must be at least 8 characters with 1 uppercase, 1 lowercase and 1 number.",
    );
    isValid = false;
  } else {
    setSuccess(password);
  }

  if (
    confirmPassword.value !== password.value ||
    confirmPassword.value === ""
  ) {
    setError(confirmPassword, "Passwords do not match.");
    isValid = false;
  } else {
    setSuccess(confirmPassword);
  }

  registerBtn.disabled = !isValid;

  return isValid;
}

fullName.addEventListener("blur", validateInputs);
email.addEventListener("blur", validateInputs);
password.addEventListener("blur", validateInputs);
confirmPassword.addEventListener("blur", validateInputs);

form.addEventListener("input", validateInputs);

form.addEventListener("submit", function (e) {
  e.preventDefault();

  if (validateInputs()) {
    alert("Registration successful! You may now log in.");
    form.reset();
    registerBtn.disabled = true;
  }
});

document.querySelectorAll(".toggle-btn").forEach((toggle) => {
  toggle.addEventListener("click", function () {
    const targetInput = document.getElementById(this.dataset.target);

    if (targetInput.type === "password") {
      targetInput.type = "text";
      this.textContent = "Hide";
    } else {
      targetInput.type = "password";
      this.textContent = "Show";
    }
  });
});
