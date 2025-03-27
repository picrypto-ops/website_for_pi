<?php
/**
 * Translatable for products.json data format
 */
class ProductTranslatable extends AbstractTranslatable {
    /**
     * {@inheritdoc}
     */
    protected function key_exists($lang, $key) {
        // Structure: { "language_slug": { "en": { "name": "...", "description": "..." } } }
        return isset($this->data['language_slug']) && 
               isset($this->data['language_slug'][$lang]) && 
               isset($this->data['language_slug'][$lang][$key]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function get_key($lang, $key) {
        // Structure: { "language_slug": { "en": { "name": "...", "description": "..." } } }
        if ($this->key_exists($lang, $key)) {
            $content = $this->data['language_slug'][$lang][$key];
            
            // If the content is an array (like for descriptions), process each item
            if (is_array($content)) {
                foreach ($content as $index => $item) {
                    $content[$index] = $this->replace_variables($item);
                }
            } else {
                // Process single string content
                $content = $this->replace_variables($content);
            }
            
            return $content;
        }
        
        throw new Exception("Translation key '{$key}' not found for language '{$lang}'");
    }
    
    /**
     * Replace variable placeholders in content with actual values from product data
     * 
     * @param string $content The content with placeholders
     * @return string The content with placeholders replaced with values
     */
    protected function replace_variables($content) {
        // Debug statement to log the content before replacement
        error_log("ProductTranslatable: Content before replacement: " . substr($content, 0, 100));
        
        // Search for placeholders like {{variable_name}}
        if (preg_match_all('/\{\{([a-zA-Z_]+)\}\}/', $content, $matches)) {
            error_log("ProductTranslatable: Found variables: " . implode(", ", $matches[1]));
            
            foreach ($matches[1] as $variable_name) {
                // Check if the variable exists at the product level
                if (isset($this->data[$variable_name])) {
                    $value = $this->data[$variable_name];
                    error_log("ProductTranslatable: Replacing {{" . $variable_name . "}} with " . $value);
                    $content = str_replace('{{'.$variable_name.'}}', $value, $content);
                } else {
                    error_log("ProductTranslatable: Variable not found in data: " . $variable_name);
                }
            }
        } else {
            error_log("ProductTranslatable: No variables found in content");
        }
        
        // Debug statement to log the content after replacement
        error_log("ProductTranslatable: Content after replacement: " . substr($content, 0, 100));
        
        return $content;
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