const registerform = document.getElementById("registerForm");
const fullName = document.getElementById("fullName");
const regemail = document.getElementById("regemail");
const regpassword = document.getElementById("regpassword");
const confirmPassword = document.getElementById("confirmPassword");
const registerBtn = document.getElementById("registerBtn");
const successMsg = document.getElementById("successMessage");
const toggleSPassword = document.getElementById("toggleSignupPassword");
const toggleCPassword = document.getElementById("toggleConfirmPassword");

const namePattern = /^[A-Za-z\s]{3,}$/;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

function getErrorElement(input) {
  switch (input.id) {
    case "fullName":
      return document.getElementById("fullNameError");
    case "regemail":
      return document.getElementById("signupEmailErr");
    case "regpassword":
      return document.getElementById("signupPassErr");
    case "confirmPassword":
      return document.getElementById("confirmPassErr");
    default:
      return null;
  }
}

function setError(input, message) {
  const error = getErrorElement(input);
  if (error) error.textContent = message;
  input.style.border = "1px solid red";
}

function setSuccess(input) {
  const error = getErrorElement(input);
  if (error) error.textContent = "";
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

  if (!emailPattern.test(regemail.value.trim())) {
    setError(regemail, "Please enter a valid email address.");
    isValid = false;
  } else {
    setSuccess(regemail);
  }

  if (!passwordPattern.test(regpassword.value)) {
    setError(
      regpassword,
      "Password must be at least 8 characters with 1 uppercase, 1 lowercase and 1 number.",
    );
    isValid = false;
  } else {
    setSuccess(regpassword);
  }

  if (
    confirmPassword.value !== regpassword.value ||
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
regemail.addEventListener("blur", validateInputs);
regpassword.addEventListener("blur", validateInputs);
confirmPassword.addEventListener("blur", validateInputs);

registerform.addEventListener("input", validateInputs);
registerform.addEventListener("submit", function (e) {
  e.preventDefault();

  if (validateInputs()) {
    alert("Registration successful! You may now log in.");
    registerform.reset();
    registerBtn.disabled = true;
  }
});

// SHOW/HIDE PASSWORD
toggleSPassword.addEventListener("click", () => {
  regpassword.type = regpassword.type === "password" ? "text" : "password";
});

toggleCPassword.addEventListener("click", () => {
  confirmPassword.type =
    confirmPassword.type === "password" ? "text" : "password";
});
