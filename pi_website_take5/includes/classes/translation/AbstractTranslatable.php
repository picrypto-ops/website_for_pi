<?php
/**
 * Abstract base class for translatable content
 */
abstract class AbstractTranslatable implements TranslatableInterface {
    /**
     * Data array containing translatable content
     * @var array
     */
    protected $data;
    
    /**
     * Constructor
     * 
     * @param array $data Data containing translatable content
     */
    public function __construct($data) {
        $this->data = $data;
    }
    
    /**
     * Get specific translated content
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @param string $default Default value if content not found
     * @return string Translated content
     */
    public function getContent($lang, $key, $default = '') {
        if ($this->key_exists($lang, $key)) {
            return $this->get_key($lang, $key);
        }
        return $default;
    }
    
    /**
     * Check if specific translated content exists
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @return bool True if content exists, false otherwise
     */
    public function hasContent($lang, $key) {
        return $this->key_exists($lang, $key);
    }
    
    /**
     * Check if a key exists for a specific language
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @return bool True if key exists, false otherwise
     */
    protected abstract function key_exists($lang, $key);
    
    /**
     * Get the value for a specific key and language
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @return string Value for the key
     * @throws Exception If the key does not exist
     */
    protected abstract function get_key($lang, $key);
    
    /**
     * Default implementation returning empty string when content not found
     * 
     * @return string Empty string
     */
    protected function getDefaultContent() {
        return '';
    }
} 