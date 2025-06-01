document.addEventListener("DOMContentLoaded", function () {
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      let isValid = true;

      const usernameInput = document.getElementById("username");
      const usernameError = document.getElementById("usernameError");
      if (usernameInput.value.trim().length < 3) {
        isValid = false;
        usernameError.textContent = "Username minimal 3 karakter.";
        usernameInput.style.borderColor = "red";
      } else {
        usernameError.textContent = "";
        usernameInput.style.borderColor = "#ddd";
      }

      const emailInput = document.getElementById("email");
      const emailError = document.getElementById("emailError");
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailInput.value.trim())) {
        isValid = false;
        emailError.textContent = "Format email tidak valid.";
        emailInput.style.borderColor = "red";
      } else {
        emailError.textContent = "";
        emailInput.style.borderColor = "#ddd";
      }

      const passwordInput = document.getElementById("password");
      const passwordError = document.getElementById("passwordError");
      if (passwordInput.value.length < 6) {
        isValid = false;
        passwordError.textContent = "Password minimal 6 karakter.";
        passwordInput.style.borderColor = "red";
      } else {
        passwordError.textContent = "";
        passwordInput.style.borderColor = "#ddd";
      }

      const confirmPasswordInput = document.getElementById("confirm_password");
      const confirmPasswordError = document.getElementById(
        "confirmPasswordError"
      );
      if (confirmPasswordInput.value !== passwordInput.value) {
        isValid = false;
        confirmPasswordError.textContent = "Konfirmasi password tidak cocok.";
        confirmPasswordInput.style.borderColor = "red";
      } else {
        confirmPasswordError.textContent = "";
        confirmPasswordInput.style.borderColor = "#ddd";
      }

      if (!isValid) {
        event.preventDefault();
      }
    });
  }
});
