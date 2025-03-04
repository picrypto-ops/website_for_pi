<?php
/**
 * Translatable for team.json data format
 */
class TeamTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    protected function key_exists($lang, $key) {
        // Structure: { "name": "X", "position": "Y", ... }
        if (isset($this->data[$key])) {
            // Direct key for non-translatable content
            return true;
        } else if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            // Translated key
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function get_key($lang, $key) {
        // Structure: { "name": "X", "position": "Y", ... }
        if (isset($this->data[$key])) {
            // Direct key for non-translatable content
            return $this->data[$key];
        } else if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            // Translated key
            return $this->data['language_slug'][$lang][$key];
        }
        
        throw new Exception("Translation key '{$key}' not found for language '{$lang}'");
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllContent($lang) {
        if (isset($this->data['language_slug']) && isset($this->data['language_slug'][$lang])) {
            $result = $this->data['language_slug'][$lang];
            
            // Merge direct properties (not language dependent)
            foreach ($this->data as $key => $value) {
                if ($key !== 'language_slug') {
                    $result[$key] = $value;
                }
            }
            
            return $result;
        }
        
        // Return just the direct properties if no language data
        $result = [];
        foreach ($this->data as $key => $value) {
            if ($key !== 'language_slug') {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }
} 