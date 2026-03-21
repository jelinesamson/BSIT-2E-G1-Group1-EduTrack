// LOGIN PAGE ELEMENTS
let apiLog = "/BSIT-2E-G1-Group1-EduTrack/Api/login.php";
let email = $("#email");
let password = $("#password");
let loginBtn = $("#loginBtn");

let emailError = $("#emailError");
let passwordError = $("#passwordError");
let loginError = $("#loginError");

let form = $("#loginForm");
let togglePassword = $("#toggleLoginPassword");

function postOne() {
  let payload = {
    email: email.val(),
    password: password.val(),
  };

  $.ajax({
    url: apiLog,
    type: "POST",
    data: "action=postOne&payload=" + JSON.stringify(payload),
    dataType: "json",
    success: function (response) {
      alert(response.message);
      if (response.status == "success") {
        window.location.href =
          " /BSIT-2E-G1-Group1-EduTrack/Html/dashboard.php";
      }
    },
    error: function (error) {
      alert(error.message);
    },
  });
}

// VALIDATION FUNCTIONS
function validateEmail() {
  const pattern = /^\S+@\S+\.\S+$/;
  if (email.val().trim() === "") {
    emailError.text("Email is required");
    return false;
  } else if (!pattern.test(email.val().trim())) {
    emailError.text("Invalid email format");
    return false;
  } else {
    emailError.text("");
    return true;
  }
}

function validatePassword() {
  if (password.val().trim() === "") {
    passwordError.text("Password is required");
    return false;
  } else {
    passwordError.text("");
    return true;
  }
}

// ENABLE BUTTON
function checkForm() {
  loginError.text(""); // clear general error
  if (validateEmail() && validatePassword()) {
    loginBtn.prop("disabled", false);
  } else {
    loginBtn.prop("disabled", true);
  }
}

// EVENTS
email.on("blur", validateEmail);
password.on("blur", validatePassword);

email.on("input", checkForm);
password.on("input", checkForm);

// SHOW/HIDE PASSWORD
togglePassword.on("click", function () {
  password.attr(
    "type",
    password.attr("type") === "password" ? "text" : "password",
  );
});

// FORM SUBMIT
form.on("submit", function (e) {
  e.preventDefault();
  if (validateEmail() && validatePassword()) {
    if (email.val() != "" && password.val() != "") {
      postOne();
      form[0].reset();
      loginBtn.prop("disabled", true);
    }
  }
});
