const API = "/BSIT-2E-G1-Group1-EduTrack/Api/register.php";

// ELEMENTS
let registerform = $("#registerForm");
let fullName = $("#fullName");
let regemail = $("#regemail");
let regpassword = $("#regpassword");
let confirmPassword = $("#confirmPassword");
let registerBtn = $("#registerBtn");

let toggleSPassword = $("#toggleSignupPassword");
let toggleCPassword = $("#toggleConfirmPassword");

// PATTERNS
const namePattern = /^[A-Za-z\s]{3,}$/;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

// STORE FUNCTION
function store() {
  let payload = {
    fullName: fullName.val(),
    regemail: regemail.val(),
    regpassword: regpassword.val(),
  };

  $.ajax({
    url: API,
    type: "POST",
    dataType: "json",
    data: {
      action: "store",
      payload: JSON.stringify(payload),
    },
    success: function (response) {
      alert(response.message);

      if (response.status === "success") {
        window.location.href = "/BSIT-2E-G1-Group1-EduTrack/index.php";
      }
    },
    error: function (error) {
      alert("Something went wrong");
    },
  });
}

// PASSWORD TOGGLE
toggleSPassword.on("click", function () {
  regpassword.attr(
    "type",
    regpassword.attr("type") === "password" ? "text" : "password",
  );
});

toggleCPassword.on("click", function () {
  confirmPassword.attr(
    "type",
    confirmPassword.attr("type") === "password" ? "text" : "password",
  );
});

// ERROR HANDLING
function getErrorElement(input) {
  switch (input.attr("id")) {
    case "fullName":
      return $("#fullNameError");
    case "regemail":
      return $("#signupEmailErr");
    case "regpassword":
      return $("#signupPassErr");
    case "confirmPassword":
      return $("#confirmPassErr");
  }
}

function setError(input, message) {
  let error = getErrorElement(input);
  if (error) error.text(message);
  input.css("border", "1px solid red");
}

function setSuccess(input) {
  let error = getErrorElement(input);
  if (error) error.text("");
  input.css("border", "1px solid green");
}

// VALIDATION
function validateInputs() {
  let isValid = true;

  if (!namePattern.test(fullName.val().trim())) {
    setError(fullName, "Full Name must be at least 3 characters.");
    isValid = false;
  } else setSuccess(fullName);

  if (!emailPattern.test(regemail.val().trim())) {
    setError(regemail, "Invalid email.");
    isValid = false;
  } else setSuccess(regemail);

  if (!passwordPattern.test(regpassword.val())) {
    setError(regpassword, "Weak password.");
    isValid = false;
  } else setSuccess(regpassword);

  if (
    confirmPassword.val() !== regpassword.val() ||
    confirmPassword.val() === ""
  ) {
    setError(confirmPassword, "Passwords do not match.");
    isValid = false;
  } else setSuccess(confirmPassword);

  registerBtn.prop("disabled", !isValid);

  return isValid;
}

// EVENTS
fullName.on("blur", validateInputs);
regemail.on("blur", validateInputs);
regpassword.on("blur", validateInputs);
confirmPassword.on("blur", validateInputs);

registerform.on("input", validateInputs);

registerform.on("submit", function (e) {
  e.preventDefault();

  if (validateInputs()) {
    store();
    registerform[0].reset(); // reset form
    registerBtn.prop("disabled", true);
  }
});
