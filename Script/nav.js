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
/* About Animations */
const observerOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add("visible");
    }
  });
}, observerOptions);
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".fade-in").forEach((el) => {
    observer.observe(el);
  });
});
