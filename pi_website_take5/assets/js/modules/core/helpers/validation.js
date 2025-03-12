/**
 * Validation Helpers
 * Contains utility functions for form validation
 */

/**
 * Validates a form using specified rules
 * @param {HTMLFormElement} form - The form element to validate
 * @return {boolean} - True if the form is valid, false otherwise
 */
export function validateForm(form) {
  const nameInput = form.querySelector("#name")
  const emailInput = form.querySelector("#email")
  const subjectInput = form.querySelector("#subject")
  const messageInput = form.querySelector("#message")

  let isValid = true

  if (nameInput && nameInput.value.trim() === "") {
    showError(nameInput, "Name is required")
    isValid = false
  } else if (nameInput) {
    clearError(nameInput)
  }

  if (emailInput && emailInput.value.trim() === "") {
    showError(emailInput, "Email is required")
    isValid = false
  } else if (emailInput && !isValidEmail(emailInput.value)) {
    showError(emailInput, "Please enter a valid email address")
    isValid = false
  } else if (emailInput) {
    clearError(emailInput)
  }

  if (subjectInput && subjectInput.value.trim() === "") {
    showError(subjectInput, "Subject is required")
    isValid = false
  } else if (subjectInput) {
    clearError(subjectInput)
  }

  if (messageInput && messageInput.value.trim() === "") {
    showError(messageInput, "Message is required")
    isValid = false
  } else if (messageInput) {
    clearError(messageInput)
  }

  return isValid
}

/**
 * Shows an error message for a form field
 * @param {HTMLElement} input - The input element
 * @param {string} message - The error message to display
 */
export function showError(input, message) {
  const formGroup = input.closest(".form-group")
  const error = formGroup.querySelector(".error-message") || document.createElement("div")
  error.className = "error-message"
  error.textContent = message
  if (!formGroup.querySelector(".error-message")) {
    formGroup.appendChild(error)
  }
  input.classList.add("error")
}

/**
 * Clears an error message for a form field
 * @param {HTMLElement} input - The input element
 */
export function clearError(input) {
  const formGroup = input.closest(".form-group")
  const error = formGroup.querySelector(".error-message")
  if (error) {
    formGroup.removeChild(error)
  }
  input.classList.remove("error")
}

/**
 * Validates an email address
 * @param {string} email - The email address to validate
 * @return {boolean} - True if the email is valid, false otherwise
 */
export function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
} 