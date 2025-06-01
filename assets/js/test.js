document.addEventListener("DOMContentLoaded", function () {
  const timerDisplay = document.getElementById("time");
  const testForm = document.getElementById("testForm");

  // Ambil data-timeleft dari container
  const testContainer = document.querySelector(".test-container");
  let timeLeft = parseInt(testContainer.getAttribute("data-timeleft"), 10) || 0;

  function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    seconds = seconds < 10 ? "0" + seconds : seconds;
    timerDisplay.textContent = `${minutes}:${seconds}`;
    timeLeft--;

    if (timeLeft < 0) {
      clearInterval(timerInterval);
      timerDisplay.textContent = "Waktu Habis!";
      if (testForm) {
        alert(
          "Waktu pengerjaan tes telah habis. Jawaban Anda akan otomatis dikirim."
        );
        testForm.submit();
      }
    }
  }

  updateTimer();
  const timerInterval = setInterval(updateTimer, 1000);
});
