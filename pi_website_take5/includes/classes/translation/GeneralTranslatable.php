<?php
/**
 * Translatable for general.json data format
 */
class GeneralTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    public function getContent($lang, $key) {
        // Structure: { "term_slug": "X", "language_slug": { "en": { "label": "..." } } }
        if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            return $this->data['language_slug'][$lang][$key];
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