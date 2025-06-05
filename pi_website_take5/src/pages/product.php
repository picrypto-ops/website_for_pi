@use "../base/variables" as variables;

// ==========================================================================
// Block: product-card
// Attempt 4: Implementing side-by-side internal layout (Icon | Text Block)
// ==========================================================================
.product-card {
  // Positioning & Box Model
  display: flex; // Keep card as flex container (column)
  flex-direction: column; // Overall direction is still column
  box-sizing: border-box;
  width: 100%;
  min-height: 170px; // Keep minimum height from attempt 3
  height: 100%;
  padding: 20px; // Keep reduced padding
  position: relative;
  isolation: isolate;
  overflow: hidden;

  // Visual
  background: variables.$card-bg-color;
  border: 1px solid rgb(from variables.$primary-color r g b / 8%);
  border-radius: 12px;
  border-bottom: 4px solid variables.$primary-color;
  box-shadow: 0 4px 15px rgb(0 0 0 / 8%);
  color: variables.$text-color;
  text-decoration: none;
  // text-align: center; // REMOVED - alignment handled by children

  // Transitions
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);

  // Pseudo-element for hover effect
  &::before {
    content: ''; position: absolute; top: -100%; left: 0; width: 100%; height: 100%;
    background: linear-gradient(135deg, rgb(255 255 255 / 20%), rgb(255 255 255 / 5%));
    transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    z-index: 1; pointer-events: none;
  }

  // Hover State (Triggered by parent .card-link:hover > &)
  .card-link:hover &,
  .card-link:focus & {
     transform: translateY(-8px) scale(1.01);
     box-shadow: 0 16px 32px rgb(0 0 0 / 15%);
     &::before { transform: translateY(200%); }
     .product-card__icon-image { transform: scale(1.05) rotate(5deg); }
     .product-card__title { color: variables.$primary-color; }
  }

  // --- Element: Body (NEW WRAPPER for Icon + Text Block) ---
  // This allows icon and text block to sit side-by-side
  &__body {
    display: flex;
    flex-direction: row; // Icon and Text side-by-side
    align-items: center; // Vertically align icon and text block
    gap: 15px; // Space between icon and text block
    flex-grow: 1; // Allow body to fill card height
    width: 100%;
  }

  // --- Element: Icon Container ---
  // Now a flex item within __body
  &__icon {
    width: 70px; // Keep size from previous attempt
    height: 70px;
    // margin: 0 auto 15px; // REMOVED margin auto
    margin: 0; // Reset margin
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0; // Prevent icon from shrinking
  }

  // --- Element: Icon Image ---
  &__icon-image {
    display: block; max-width: 100%; max-height: 100%; object-fit: contain;
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  }

  // --- Element: Text Container ---
  // Now a flex item within __body
  &__text {
    flex-grow: 1; // Allow text block to take remaining width
    display: flex;
    flex-direction: column; // Stack title and slogan vertically
    justify-content: center; // Adjust as needed (flex-start?)
    text-align: left; // Align text left (adjust for RTL below)

    html[dir="rtl"] & {
      text-align: right; // Align text right for RTL
    }
  }

  // --- Element: Title (h3) ---
  &__title {
    margin-top: 0;
    margin-bottom: 5px; // Reduced margin between title and slogan
    color: variables.$text-color;
    font-size: 1.6rem; // Keep adjusted size
    font-weight: 600;
    line-height: 1.3;
    white-space: normal; // Allow title to wrap if needed
    transition: color 0.3s ease;
    // Remove text-overflow/ellipsis for wrapping
  }

  // --- Element: Slogan (p) ---
  &__slogan {
    margin: 0;
    color: variables.$text-muted;
    font-size: 0.95rem; // Slightly increased slogan size
    line-height: 1.45; // Slightly increased line height
    // min-height: calc(1.45em * 2); // Keep space for 2 lines
    // Remove overflow/ellipsis styles if needed
  }
}

// ==========================================================================
// Block: product-page (Styles for the product detail page template)
// NO CHANGES NEEDED HERE FROM PREVIOUS VERSION
// ==========================================================================
.product-page {
  // ... styles remain the same ...
  padding: 60px 0;
  @media (max-width: 768px) { padding: 30px 0; }

  &__header { display: flex; align-items: center; margin-bottom: 1.5rem; @media (max-width: 768px) { flex-direction: column; align-items: flex-start; } }
  &__logo { flex-shrink: 0; margin-right: 30px; width: 150px; html[dir="rtl"] & { margin-right: 0; margin-left: 30px; } @media (max-width: 768px) { margin-right: 0; margin-left: 0; margin-bottom: 1.5rem; } }
  &__logo-image { display: block; max-width: 100%; height: auto; }
  &__title-area { flex-grow: 1; }
  &__title { margin-top: 0; margin-bottom: 0.5rem; color: variables.$text-color; font-size: 2.5rem; @media (max-width: 768px) { font-size: 2rem; } }
  &__slogan { margin: 0; color: variables.$text-muted; font-size: 1.25rem; font-style: italic; }
  &__content { max-width: 900px; margin: 0 auto 4rem; }
  &__description-paragraph { margin-bottom: 1.5rem; color: variables.$text-color; font-size: 1.1rem; line-height: 1.8; &:first-of-type { font-size: 1.2rem; font-weight: 500; } ul, ol { margin-bottom: 1rem; padding-left: 1.5rem; html[dir="rtl"] & { padding-left: 0; padding-right: 1.5rem; } } li { margin-bottom: 0.5rem; } }
  &__image-container { position: relative; width: 70%; margin: 2.5rem auto; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 30px rgb(0 0 0 / 15%); @media (max-width: 768px) { width: 90%; } &::after { content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 30%; background: linear-gradient(to top, rgb(0 0 0 / 50%), rgb(0 0 0 / 0%)); pointer-events: none; } }
  &__image { display: block; width: 100%; height: auto; transition: transform 0.5s ease; }
  .product-page__image-container:hover &__image { transform: scale(1.02); }
  &__image-caption { position: absolute; bottom: 20px; left: 25px; z-index: 2; max-width: 80%; color: #fff; font-size: 1rem; text-shadow: 0 1px 3px rgb(0 0 0 / 70%); html[dir="rtl"] & { left: auto; right: 25px; text-align: right; } }
  &__section-title { position: relative; margin-top: 4rem; margin-bottom: 2rem; font-size: 1.75rem; font-weight: 600; padding-bottom: 10px; &::after { content: ''; position: absolute; bottom: 0; left: 0; width: 60px; height: 3px; background-color: variables.$primary-color; html[dir="rtl"] & { left: auto; right: 0; } } }
  &__team-grid { /* Use .card-grid */ }
  &__back-link-container { margin-top: 3rem; text-align: center; }
  &__back-link { /* Style applied via .button class in PHP */ }
  &__back-link-arrow { margin-right: 0.5rem; font-size: 1.2rem; html[dir="rtl"] & { margin-right: 0; margin-left: 0.5rem; display: inline-block; transform: rotate(180deg); } }
}