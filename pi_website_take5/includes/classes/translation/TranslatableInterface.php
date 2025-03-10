<?php
/**
 * Interface for translatable content from JSON data
 */
interface TranslatableInterface {
    /**
     * Get specific translated content
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @param string $default Default value if content not found
     * @return string Translated content
     */
    public function getContent($lang, $key, $default = '');
    
    /**
     * Check if specific translated content exists
     * 
     * @param string $lang Language code
     * @param string $key Content key
     * @return bool True if content exists, false otherwise
     */
    public function hasContent($lang, $key);
    
    /**
     * Get all translated content for a language
     * 
     * @param string $lang Language code
     * @return array All translated content
     */
    public function getAllContent($lang);
} 