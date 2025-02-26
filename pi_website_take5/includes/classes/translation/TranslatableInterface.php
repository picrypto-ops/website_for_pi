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
     * @return string Translated content
     */
    public function getContent($lang, $key);
    
    /**
     * Get all translated content for a language
     * 
     * @param string $lang Language code
     * @return array All translated content
     */
    public function getAllContent($lang);
} 