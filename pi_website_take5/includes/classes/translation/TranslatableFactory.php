<?php
/**
 * Factory for creating translatable objects with automatic data loading
 */
class TranslatableFactory {
    /**
     * Debug flag - set to true to enable debug output
     */
    public static $debug = true;
    
    /**
     * Loaded data cache
     */
    private static $dataCache = [];
    
    /**
     * Flag to force data refresh on next access
     */
    private static $refreshDataFlag = false;
    
    /**
     * Initialize the factory and preload commonly used data
     * 
     * @param array $preloadFiles Array of file names to preload
     */
    public static function initialize($preloadFiles = ['general', 'pages']) {
        foreach ($preloadFiles as $file) {
            self::loadDataInternal($file);
        }
        if (self::$debug) error_log("TranslatableFactory: Initialized with preloaded files: " . implode(', ', $preloadFiles));
    }
    
    /**
     * Flag all data to be refreshed on next access
     */
    public static function refreshData() {
        self::$dataCache = [];
        self::$refreshDataFlag = true;
        if (self::$debug) error_log("TranslatableFactory: Data refresh requested");
    }
    
    /**
     * Internal method to load JSON data from file with improved error handling
     * 
     * @param string $file The name of the JSON file without extension
     * @param bool $forceRefresh Force refresh from file even if cached
     * @return array The decoded JSON data or empty array on error
     */
    private static function loadDataInternal($file, $forceRefresh = false) {
        // Check if data is already cached in memory and refresh not forced
        if (!$forceRefresh && !self::$refreshDataFlag && isset(self::$dataCache[$file])) {
            if (self::$debug) error_log("TranslatableFactory: Using cached data for $file");
            return self::$dataCache[$file];
        }
        
        $jsonFile = "data/{$file}.json";
        
        // Check if file exists
        if (!file_exists($jsonFile)) {
            if (self::$debug) error_log("TranslatableFactory: JSON file not found: {$jsonFile}");
            self::$dataCache[$file] = [];
            return [];
        }
        
        // Get file contents
        $jsonContent = file_get_contents($jsonFile);
        if ($jsonContent === false) {
            if (self::$debug) error_log("TranslatableFactory: Could not read JSON file: {$jsonFile}");
            self::$dataCache[$file] = [];
            return [];
        }
        
        // Decode JSON
        $data = json_decode($jsonContent, true);
        
        // Check for JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (self::$debug) error_log("TranslatableFactory: JSON decode error for file {$jsonFile}: " . json_last_error_msg());
            self::$dataCache[$file] = [];
            return [];
        }
        
        // Cache the data
        self::$dataCache[$file] = $data;
        
        if (self::$debug) error_log("TranslatableFactory: Successfully loaded data from {$jsonFile}");
        
        // Reset refresh flag after successful load
        self::$refreshDataFlag = false;
        
        // Return data
        return $data;
    }
    
    /**
     * Public method to get data - for backwards compatibility
     * 
     * @param string $file The name of the JSON file without extension
     * @param bool $forceRefresh Force refresh from file
     * @return array The decoded JSON data
     * @deprecated Use createFromFile instead for translations
     */
    public static function loadData($file, $forceRefresh = false) {
        return self::loadDataInternal($file, $forceRefresh);
    }
    
    /**
     * Get specific data item by key
     * 
     * @param string $file The name of the JSON file without extension
     * @param string $key The key to retrieve
     * @return array The data or empty array if not found
     */
    public static function getData($file, $key = null) {
        $data = self::loadDataInternal($file);
        
        if ($key !== null) {
            return isset($data[$key]) ? $data[$key] : [];
        }
        
        return $data;
    }
    
    /**
     * Create a translatable object for the given type and data
     * 
     * @param string $type The type of data ('general', 'page', 'product', 'segment', 'team')
     * @param mixed $data The data containing translatable content or data key if string
     * @param string|null $file Optional JSON file to load data from if $data is a string key
     * @return TranslatableInterface A translatable object
     */
    public static function create($type, $data = null, $file = null) {
        // Handle array input for data when it should be a string key
        if (is_array($data) && $file !== null) {
            if (self::$debug) error_log("TranslatableFactory: Received array for data parameter when file is specified");
            // This means we already have the data, so we don't need to load it from a file
            $file = null;
        }
        
        // Auto-load data if needed
        if ($data === null && $file !== null) {
            // Load the entire file
            $data = self::loadDataInternal($file);
            if (self::$debug) error_log("TranslatableFactory: Auto-loaded entire file '$file'");
        } else if (is_string($data) && $file !== null) {
            // Load specific key from file
            $allData = self::loadDataInternal($file);
            $dataKey = $data; // Store the key name
            if (isset($allData[$dataKey])) {
                $data = $allData[$dataKey];
                if (self::$debug) error_log("TranslatableFactory: Auto-loaded data for key '$dataKey' from file '$file'");
            } else {
                if (self::$debug) error_log("TranslatableFactory: Warning - Key '$dataKey' not found in file '$file'");
                $data = [];
            }
        }
        
        if ($data === null || !is_array($data)) {
            if (self::$debug) error_log("TranslatableFactory: Data is null or not an array, using empty array");
            $data = [];
        }
        
        $result = null;
        
        switch ($type) {
            case 'general':
                $result = new GeneralTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created GeneralTranslatable for type: $type");
                break;
            case 'page':
                $result = new PageTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created PageTranslatable for type: $type");
                break;
            case 'product':
                $result = new ProductTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created ProductTranslatable for type: $type");
                break;
            case 'segment':
                $result = new SegmentTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created SegmentTranslatable for type: $type");
                break;
            case 'team':
                $result = new TeamTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created TeamTranslatable for type: $type");
                break;
            default:
                // Default to general translatable for unknown types
                $result = new GeneralTranslatable($data);
                if (self::$debug) error_log("TranslatableFactory: Created default GeneralTranslatable for unknown type: $type");
        }
        
        return $result;
    }
    
    /**
     * Create a translatable directly from a data file and key
     * 
     * @param string $type The type of translatable
     * @param string $file The data file name without extension
     * @param string|null $key Optional key to access within the data file
     * @return TranslatableInterface A translatable object
     */
    public static function createFromFile($type, $file, $key = null) {
        if ($key !== null) {
            if (self::$debug) error_log("TranslatableFactory: Creating $type from file $file with key $key");
            return self::create($type, $key, $file);
        } else {
            if (self::$debug) error_log("TranslatableFactory: Creating $type from full file $file");
            return self::create($type, null, $file);
        }
    }
    
    /**
     * Get translatable for general content with automatic data loading
     * 
     * @param string $key Optional specific general content key
     * @return TranslatableInterface A translatable object
     */
    public static function general($key = null) {
        if (is_array($key)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for key parameter, using first array key");
            $key = array_key_first($key);
        }
        
        return self::createFromFile('general', 'general', $key);
    }
    
    /**
     * Get translatable for page content with automatic data loading
     * 
     * @param string $pageKey The page key (home, about, etc.)
     * @return TranslatableInterface A translatable object
     */
    public static function page($pageKey) {
        if (is_array($pageKey)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for pageKey parameter, using first array key");
            $pageKey = array_key_first($pageKey);
        }
        
        return self::createFromFile('page', 'pages', $pageKey);
    }
    
    /**
     * Get translatable for product content with automatic data loading
     * 
     * @param string $productKey The product key
     * @return TranslatableInterface A translatable object
     */
    public static function product($productKey) {
        if (is_array($productKey)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for productKey parameter, using first array key");
            $productKey = array_key_first($productKey);
        }
        
        return self::createFromFile('product', 'products', $productKey);
    }
    
    /**
     * Get translatable for segment content with automatic data loading
     * 
     * @param string $segmentKey The segment key
     * @return TranslatableInterface A translatable object
     */
    public static function segment($segmentKey) {
        if (is_array($segmentKey)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for segmentKey parameter, using first array key");
            $segmentKey = array_key_first($segmentKey);
        }
        
        return self::createFromFile('segment', 'segments', $segmentKey);
    }
    
    /**
     * Detect the type of data and create appropriate translatable
     * 
     * @param array $data The data to analyze
     * @return TranslatableInterface A translatable object
     */
    public static function createFromData($data) {
        if ($data === null || !is_array($data)) {
            if (self::$debug) error_log("TranslatableFactory: Data is null or not an array, returning default GeneralTranslatable");
            return new GeneralTranslatable([]);
        }
        
        if (isset($data['page_slug'])) {
            if (self::$debug) error_log("TranslatableFactory: Detected page_slug, creating PageTranslatable");
            return new PageTranslatable($data);
        } elseif (isset($data['product_slug'])) {
            if (self::$debug) error_log("TranslatableFactory: Detected product_slug, creating ProductTranslatable");
            return new ProductTranslatable($data);
        } elseif (isset($data['segment_slug'])) {
            if (self::$debug) error_log("TranslatableFactory: Detected segment_slug, creating SegmentTranslatable");
            return new SegmentTranslatable($data);
        } elseif (isset($data['term_slug'])) {
            if (self::$debug) error_log("TranslatableFactory: Detected term_slug, creating GeneralTranslatable");
            return new GeneralTranslatable($data);
        } elseif (isset($data['name_slug'])) {
            if (self::$debug) error_log("TranslatableFactory: Detected name_slug, creating TeamTranslatable");
            return new TeamTranslatable($data);
        }
        
        // Default fallback
        if (self::$debug) error_log("TranslatableFactory: No identifiable slug found, returning default GeneralTranslatable");
        return new GeneralTranslatable($data);
    }
    
    /**
     * Print debug message to browser
     * 
     * @param string $message The debug message
     */
    public static function debugToScreen($message) {
        if (self::$debug) {
            echo "<div style='background: #eee; padding: 5px; margin: 5px; border: 1px solid #ccc;'>";
            echo "<strong>TranslatableFactory Debug:</strong> " . htmlspecialchars($message);
            echo "</div>";
        }
    }
} 