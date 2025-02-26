<?php
/**
 * Translatable for products.json data format
 */
class ProductTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    public function getContent($lang, $key) {
        // Structure: { "product_slug": "X", "language_slug": { "en": { "name": "...", ... } } }
        if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            return $this->data['language_slug'][$lang][$key];
        }
        
        // Fallback for direct key access
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        
        return $this->getDefaultContent();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllContent($lang) {
        if (isset($this->data['language_slug']) && isset($this->data['language_slug'][$lang])) {
            return $this->data['language_slug'][$lang];
        }
        
        return [];
    }
} 