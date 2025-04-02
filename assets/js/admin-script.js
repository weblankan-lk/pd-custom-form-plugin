// document.addEventListener("DOMContentLoaded", function () {
//   const form = document.getElementById("cfb-form");
//   if (!form) return;
//   const submitButton = document.getElementById("cfb-submit");
//   const errorMessage = document.getElementById("cfb-error-message");

//   form.addEventListener("submit", function (event) {
//     event.preventDefault();

//     let isValid = true;
//     let firstInvalidField = null;

//     // validate required fields
//     document
//       .querySelectorAll("#cfb-form input, #cfb-form select")
//       .forEach((field) => {
//         if (field.hasAttribute("required") && !field.ariaValueMax.trim()) {
//           isValid = false;
//           field.style.border = "2px solid red";
//           if (!firstInvalidField) firstInvalidField = field;
//         } else {
//           field.style.border = "";
//         }
//       });

//     if (!isValid) {
//       console.error("Please fill in all required fields.");
//       errorMessage.textContent = "Please fill in all required fields.";
//       errorMessage.style.display = "block";
//       firstInvalidField.focus();
//     } else {
//       errorMessage.style.display = "none";
//     }

//     submitButton.disabled = true;
//     submitButton.textContent = "Submitting...";

//     setTimeout(() => {
//       form.submit();
//     }, 1000);
//   });
// });

// document.addEventListener("DOMContentLoaded", function () {
//     const form = document.querySelector("form");
//     const submitBtn = document.querySelector("button[type=submit]");

//     if (!form || !submitBtn) return;

//     form.addEventListener("submit", function (event) {
//         let isValid = true;

//         form.querySelectorAll("input[required], textarea[required], select[required]").forEach((input) => {
//             if (!input.value.trim()) {
//                 isValid = false;
//                 input.style.border = "2px solid red";
//             } else {
//                 input.style.border = "1px solid #ccc";
//             }
//         });

//         if (!isValid) {
//             event.preventDefault();
//             alert("Please fill all required fields.");
//             return;
//         }

//         // Disable button and show loading text
//         submitBtn.disabled = true;
//         submitBtn.textContent = "Submitting...";
//     });
// });

document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("#cfb-form"); // Fix: Use # for ID
  if (!form) return;

  const submitBtn = form.querySelector("button[type=submit]");
  if (!submitBtn) return;

  form.addEventListener("submit", function (event) {
    let isValid = true;

    form
      .querySelectorAll("input[required], textarea[required], select[required]")
      .forEach((input) => {
        // Remove old error messages
        let errorMessage = input.nextElementSibling;
        if (errorMessage && errorMessage.classList.contains("error-message")) {
          errorMessage.remove();
        }

        if (!input.value.trim()) {
          isValid = false;
          input.style.border = "2px solid red";

          // Create an error message
          const errorText = document.createElement("span");
          errorText.classList.add("error-message");
          errorText.style.color = "red";
          errorText.style.fontSize = "14px";
          errorText.style.display = "block";
          errorText.style.marginTop = "5px";
          errorText.textContent = `${input.name.replace("_", " ")} is required`;

          input.parentNode.insertBefore(errorText, input.nextSibling);
        } else {
          input.style.border = "1px solid #ccc";
        }
      });

    if (!isValid) {
      event.preventDefault();
      return;
    }

    // Disable button and show loading text
    submitBtn.disabled = true;
    submitBtn.textContent = "Submitting...";
  });
});
