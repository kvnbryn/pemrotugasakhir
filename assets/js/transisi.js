document.addEventListener("DOMContentLoaded", function () {
  const mainContent = document.querySelector("main");

  if (mainContent) {
    mainContent.classList.add("slide-in");
    setTimeout(() => {
      mainContent.classList.add("slide-in-active");
    }, 50);
  }
});
