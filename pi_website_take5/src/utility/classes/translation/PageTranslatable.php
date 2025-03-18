<?php
/**
 * Translatable for pages.json data format
 */
class PageTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    protected function key_exists($lang, $key) {
        // Structure: { "language_slug": { "en": { "key": "..." } } }
        return isset($this->data['language_slug']) && 
               isset($this->data['language_slug'][$lang]) && 
               isset($this->data['language_slug'][$lang][$key]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function get_key($lang, $key) {
        // Structure: { "language_slug": { "en": { "key": "..." } } }
        if ($this->key_exists($lang, $key)) {
            return $this->data['language_slug'][$lang][$key];
        }
        
        throw new Exception("Translation key '{$key}' not found for language '{$lang}'");
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