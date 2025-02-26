<?php
/**
 * Translatable for team.json data format
 */
class TeamTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    public function getContent($lang, $key) {
        // Team members have { "language_slug": { "en": { "name": "...", ... } } }
        if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            return $this->data['language_slug'][$lang][$key];
        }
        
        // Fallback for nested key
        if (isset($this->data[$key]) && 
            is_array($this->data[$key]) && 
            isset($this->data[$key][$lang])) {
            return $this->data[$key][$lang];
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