<?php
/**
 * Translatable for general.json data format
 */
class GeneralTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    protected function key_exists($lang, $key) {
        // Structure: { "term_slug": "X", "language_slug": { "en": { "label": "..." } } }
        
        // Check if we're trying to access a top-level key from the general data
        if (isset($this->data[$key]) && 
            isset($this->data[$key]['language_slug']) && 
            isset($this->data[$key]['language_slug'][$lang]) && 
            isset($this->data[$key]['language_slug'][$lang]['label'])) {
            return true;
        }
        
        // Fallback to original implementation for compatibility
        if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function get_key($lang, $key) {
        // Structure: { "term_slug": "X", "language_slug": { "en": { "label": "..." } } }
        
        // Check if we're trying to access a top-level key from the general data
        if (isset($this->data[$key]) && 
            isset($this->data[$key]['language_slug']) && 
            isset($this->data[$key]['language_slug'][$lang]) && 
            isset($this->data[$key]['language_slug'][$lang]['label'])) {
            
            // Successfully found the translation
            return $this->data[$key]['language_slug'][$lang]['label'];
        }
        
        // Fallback to original implementation for compatibility
        if (isset($this->data['language_slug']) && 
            isset($this->data['language_slug'][$lang]) && 
            isset($this->data['language_slug'][$lang][$key])) {
            
            // Found translation in the original structure
            return $this->data['language_slug'][$lang][$key];
        }
        
        // Translation not found - log for debugging if debug is enabled
        if (TranslatableFactory::$debug) {
            $backtraceInfo = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller = isset($backtraceInfo[1]) ? $backtraceInfo[1] : $backtraceInfo[0];
            $file = isset($caller['file']) ? basename($caller['file']) : 'unknown';
            $line = isset($caller['line']) ? $caller['line'] : 'unknown';
            
            error_log("Translation missing: Key '{$key}' not found for language '{$lang}' in {$file}:{$line}");
        }
        
        // This shouldn't normally happen since key_exists should be called first
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