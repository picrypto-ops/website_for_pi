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
     * Default implementation returning empty string when content not found
     * 
     * @return string Empty string
     */
    protected function getDefaultContent() {
        return '';
    }
} 