document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      let isValid = true;

      const loginIdentifierInput = document.getElementById("login_identifier");
      const loginIdentifierError = document.getElementById(
        "loginIdentifierError"
      );
      if (loginIdentifierInput.value.trim() === "") {
        isValid = false;
        loginIdentifierError.textContent =
          "Username atau Email tidak boleh kosong.";
        loginIdentifierInput.style.borderColor = "red";
      } else {
        loginIdentifierError.textContent = "";
        loginIdentifierInput.style.borderColor = "#ddd";
      }

      const passwordInput = document.getElementById("password");
      const passwordError = document.getElementById("passwordError");
      if (passwordInput.value === "") {
        isValid = false;
        passwordError.textContent = "Password tidak boleh kosong.";
        passwordInput.style.borderColor = "red";
      } else {
        passwordError.textContent = "";
        passwordInput.style.borderColor = "#ddd";
      }

      if (!isValid) {
        event.preventDefault();
      }
    });
  }
});
