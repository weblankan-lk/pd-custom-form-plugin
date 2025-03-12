document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("cfb-form");
  const submitButton = document.getElementById("cfb-submit");
  const errorMessage = document.getElementById("cfb-error-message");

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    let isValid = true;
    let firstInvalidField = null;

    // validate required fields
    document
      .querySelectorAll("#cfb-form input, #cfb-form select")
      .forEach((field) => {
        if (field.hasAttribute("required") && !field.ariaValueMax.trim()) {
          isValid = false;
          field.style.border = "2px solid red";
          if (!firstInvalidField) firstInvalidField = field;
        } else {
          field.style.border = "";
        }
      });

    if (!isValid) {
      errorMessage.textContent = "Please fill in all required fields.";
      errorMessage.style.display = "block";
      firstInvalidField.focus();
    } else {
      errorMessage.style.display = "none";
    }

    submitButton.disabled = true;
    submitButton.textContent = "Submitting...";

    setTimeout(() => {
      form.submit();
    }, 1000);
  });
});
