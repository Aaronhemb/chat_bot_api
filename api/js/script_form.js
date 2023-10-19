document.addEventListener("DOMContentLoaded", function() {
  const submitBtn = document.getElementById("submit-btn");
  const fullNameInput = document.getElementById("full-name");
  const emailInput = document.getElementById("email");
  const phoneInput = document.getElementById("phone");
  const errorMessage = document.getElementById("error-message");

  submitBtn.addEventListener("click", function(event) {
    if (
      fullNameInput.value.trim() === "" ||
      emailInput.value.trim() === "" ||
      phoneInput.value.trim() === ""
    ) {
      errorMessage.textContent = "Por favor, complete todos los campos.";
      event.preventDefault(); // Evitar el envío del formulario
    } else {
      errorMessage.textContent = ""; // Borra el mensaje de error si los campos están completos
      // Almacena los valores del formulario en el almacenamiento local
      localStorage.setItem("formSubmitted", "true");
      localStorage.setItem("fullName", fullNameInput.value);
      localStorage.setItem("email", emailInput.value);
      localStorage.setItem("phone", phoneInput.value);
    }
  });
});