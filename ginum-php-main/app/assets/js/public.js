// password eye toggle
const passwordInput = document.getElementById("password");
const eyeIcon = document.getElementById("eyeIcon");
const toggleButton = document.getElementById("togglePassword");

toggleButton.addEventListener("click", function () {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    eyeIcon.classList.remove("far", "fa-eye");
    eyeIcon.classList.add("fas", "fa-eye-slash");
  } else {
    passwordInput.type = "password";
    eyeIcon.classList.remove("fas", "fa-eye-slash");
    eyeIcon.classList.add("far", "fa-eye");
  }
});
