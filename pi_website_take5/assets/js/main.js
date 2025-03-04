/**
 * Main JavaScript file
 * Initializes all components and modules for the website
 */

import AssetManagementIcons from './asset_management_icons.js';

document.addEventListener("DOMContentLoaded", () => {
  // Mobile menu toggle
  const menuToggle = document.querySelector(".menu-toggle")
  const nav = document.querySelector("nav ul")

  if (menuToggle && nav) {
    menuToggle.addEventListener("click", () => {
      nav.classList.toggle("show")
    })
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      document.querySelector(this.getAttribute("href")).scrollIntoView({
        behavior: "smooth",
      })
    })
  })

  // Form validation
  const contactForm = document.querySelector("#contact-form")
  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault()
      if (validateForm()) {
        // Submit the form
        this.submit()
      }
    })
  }

  // Initialize Asset Management Icons
  const assetManagementIcons = new AssetManagementIcons();
  assetManagementIcons.init({
    // Override default selectors if needed
    leftHandSelector: '.asset-management-left-hand',
    rightHandSelector: '.asset-management-right-hand',
    combinedSelector: '.asset-management-combined-hands'
  });

  // Make cards clickable
  function handleCardClick(event) {
    const card = event.currentTarget;
    const link = card.querySelector('a');
    if (link && !event.target.closest('a')) {
      link.click();
    }
  }

  // Add click handlers to all cards
  const cards = document.querySelectorAll('.product-card, .team-card, .segment-card');
  cards.forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', handleCardClick);
    
    // Prevent double click on links inside cards
    const links = card.querySelectorAll('a');
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        e.stopPropagation();
      });
    });
  });

  // Initialize lazy loading for images
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const image = entry.target
          image.src = image.dataset.src
          image.classList.remove("lazy")
          imageObserver.unobserve(image)
        }
      })
    })

    document.querySelectorAll("img.lazy").forEach((img) => {
      imageObserver.observe(img)
    })
  } else {
    // Fallback for browsers that don't support IntersectionObserver
    document.querySelectorAll("img.lazy").forEach((img) => {
      img.src = img.dataset.src
      img.classList.remove("lazy")
    })
  }
});

// Form validation functions
function validateForm() {
  const form = document.querySelector("#contact-form")
  const nameInput = form.querySelector("#name")
  const emailInput = form.querySelector("#email")
  const subjectInput = form.querySelector("#subject")
  const messageInput = form.querySelector("#message")

  let isValid = true

  if (nameInput.value.trim() === "") {
    showError(nameInput, "Name is required")
    isValid = false
  } else {
    clearError(nameInput)
  }

  if (emailInput.value.trim() === "") {
    showError(emailInput, "Email is required")
    isValid = false
  } else if (!isValidEmail(emailInput.value)) {
    showError(emailInput, "Please enter a valid email address")
    isValid = false
  } else {
    clearError(emailInput)
  }

  if (subjectInput.value.trim() === "") {
    showError(subjectInput, "Subject is required")
    isValid = false
  } else {
    clearError(subjectInput)
  }

  if (messageInput.value.trim() === "") {
    showError(messageInput, "Message is required")
    isValid = false
  } else {
    clearError(messageInput)
  }

  return isValid
}

function showError(input, message) {
  const formGroup = input.closest(".form-group")
  const error = formGroup.querySelector(".error-message") || document.createElement("div")
  error.className = "error-message"
  error.textContent = message
  if (!formGroup.querySelector(".error-message")) {
    formGroup.appendChild(error)
  }
  input.classList.add("error")
}

function clearError(input) {
  const formGroup = input.closest(".form-group")
  const error = formGroup.querySelector(".error-message")
  if (error) {
    formGroup.removeChild(error)
  }
  input.classList.remove("error")
}

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

