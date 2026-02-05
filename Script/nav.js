fetch("nav.html")
  .then((response) => response.text())
  .then((data) => {
    document.getElementById("navbar-placeholder").innerHTML = data;

    const current = location.pathname;
    const links = document.querySelectorAll(".nav-link");

    links.forEach((link) => {
      if (current.includes(link.getAttribute("href"))) {
        link.classList.add("active");
      }
    });
  });
function validateLogin() {
  const email = document.getElementById("loginEmail").value.trim();
  const emailerr = document.getElementById("loginEmailErr");

  const password = document.getElementById("loginPassword").value.trim();
  const passerr = document.getElementById("loginPassErr");

  emailerr.innerText = "";
  passerr.innerText = "";

  if (email === "") {
    emailerr.innerText = "Please enter your email address";
    return false;
  }

  if (password === "") {
    passerr.innerText = "Please enter your password";
    return false;
  }

  alert("Logged in successfully!");
  return true;
}

window.addEventListener("load", () => {
  document.querySelector(".hero-section").classList.add("show");
});

window.addEventListener("load", () => {
  document.body.classList.add("page-loaded");
});

function validateSignup() {
  const firstn = document.getElementById("signupFirstN").value.trim();
  const firstnerr = document.getElementById("signupFirstNErr");

  const lastn = document.getElementById("signupLastN").value.trim();
  const lastnerr = document.getElementById("signupLastNErr");

  const email = document.getElementById("signupEmail").value.trim();
  const emailerr = document.getElementById("signupEmailErr");

  const password = document.getElementById("signupPassword").value.trim();
  const passerr = document.getElementById("signupPassErr");

  firstnerr.innerText = "";
  lastnerr.innerText = "";
  emailerr.innerText = "";
  passerr.innerText = "";

  if (firstn === "") {
    firstnerr.innerText = "Please enter your first name";
    return false;
  }

  if (lastn === "") {
    lastnerr.innerText = "Please enter your last name";
    return false;
  }

  if (email === "") {
    emailerr.innerText = "Please enter your email address";
    return false;
  }

  if (password === "") {
    passerr.innerText = "Please enter your password";
    return false;
  }

  alert("Sign up successfully!");
  return true;
}

window.addEventListener("load", () => {
  document.querySelector(".hero-section").classList.add("show");
});

window.addEventListener("load", () => {
  document.body.classList.add("page-loaded");
});

document.querySelectorAll(".contact-card").forEach((card) => {
  card.addEventListener("mouseenter", function () {
    this.style.transform = "translateY(-5px)";
  });

  card.addEventListener("mouseleave", function () {
    this.style.transform = "translateY(0)";
  });
});
