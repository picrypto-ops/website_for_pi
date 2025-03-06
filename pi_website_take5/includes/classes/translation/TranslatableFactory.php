<?php
/**
 * Factory for creating translatable objects with automatic data loading
 */
class TranslatableFactory {
    /**
     * Debug flag - set to true to enable debug output
     */
    public static $debug = false;
    
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
        
        // Load all products data
        $allProducts = self::loadDataInternal('products');
        $productData = null;
        
        // Search for the product in the nested structure
        if (!empty($productKey) && is_array($allProducts)) {
            foreach ($allProducts as $categorySlug => $categoryProducts) {
                if (!is_array($categoryProducts)) continue;
                
                if (isset($categoryProducts[$productKey])) {
                    $productData = $categoryProducts[$productKey];
                    break;
                }
            }
        }
        
        // If product was found, create a translatable from the data
        if ($productData) {
            return new ProductTranslatable($productData);
        }
        
        // Fallback to old method (might not work due to nesting)
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
     * Get translatable for team member content with automatic data loading
     * 
     * @param string $memberKey The team member key
     * @return TranslatableInterface A translatable object
     */
    public static function teamMember($memberKey) {
        if (is_array($memberKey)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for memberKey parameter, using first array key");
            $memberKey = array_key_first($memberKey);
        }
        
        // Load all team data
        $allTeam = self::loadDataInternal('team');
        $memberData = null;
        
        // Search for the member in the nested team structure
        if (!empty($memberKey) && is_array($allTeam)) {
            foreach ($allTeam as $groupSlug => $groupMembers) {
                if (!is_array($groupMembers)) continue;
                
                if (isset($groupMembers[$memberKey])) {
                    $memberData = $groupMembers[$memberKey];
                    break;
                }
            }
        }
        
        // If member was found, create a translatable from the data
        if ($memberData) {
            return new TeamTranslatable($memberData);
        }
        
        // Fallback to old method
        return self::createFromFile('team', 'team', $memberKey);
    }
    
    /**
     * Get translatable for enhanced team member content with product roles
     * 
     * @param string $memberKey The team member key
     * @return TranslatableInterface A translatable object
     */
    public static function teamMemberEnhanced($memberKey) {
        if (is_array($memberKey)) {
            if (self::$debug) error_log("TranslatableFactory: Received array for memberKey parameter, using first array key");
            $memberKey = array_key_first($memberKey);
        }
        
        // Load all team data
        $allTeam = self::loadDataInternal('team');
        $memberData = null;
        
        // Search for the member in the nested team structure
        if (!empty($memberKey) && is_array($allTeam)) {
            foreach ($allTeam as $groupSlug => $groupMembers) {
                if (!is_array($groupMembers)) continue;
                
                foreach ($groupMembers as $member) {
                    if (isset($member['name_slug']) && $member['name_slug'] === $memberKey) {
                        $memberData = $member;
                        break 2;
                    }
                }
            }
        }
        
        if ($memberData === null) {
            if (self::$debug) error_log("TranslatableFactory: Team member with key '$memberKey' not found");
            return new TeamMemberEnhanced([], []);
        }
        
        // Load team_products data to get roles
        $teamProducts = self::loadDataInternal('teams_products');
        $productRoles = [];
        
        if (isset($teamProducts['team_products']) && is_array($teamProducts['team_products'])) {
            foreach ($teamProducts['team_products'] as $role) {
                if (isset($role['name_slug']) && $role['name_slug'] === $memberKey) {
                    $productRoles[] = $role;
                }
            }
        }
        
        // Get all product and segment data for reference
        $products = self::loadDataInternal('products');
        $segments = self::loadDataInternal('segments');
        
        // Enhance product roles with product and segment data
        $enhancedRoles = [];
        foreach ($productRoles as $role) {
            $enhancedRole = $role;
            
            // Add product data if available
            if (isset($role['product_slug']) && $role['product_slug'] !== null) {
                $productFound = false;
                
                // Products are nested by segment, so we need to search differently
                foreach ($products as $segmentSlug => $segmentProducts) {
                    if (!is_array($segmentProducts)) continue;
                    
                    foreach ($segmentProducts as $productSlug => $product) {
                        if ($productSlug === $role['product_slug']) {
                            $enhancedRole['product_data'] = $product;
                            $productFound = true;
                            break 2;
                        }
                    }
                }
                
                // If not found in nested structure, try flat search
                if (!$productFound) {
                    foreach ($products as $productKey => $product) {
                        if (is_array($product) && isset($product['product_slug']) && $product['product_slug'] === $role['product_slug']) {
                            $enhancedRole['product_data'] = $product;
                            break;
                        }
                    }
                }
            }
            
            // Add segment data if available
            if (isset($role['segment_slug']) && $role['segment_slug'] !== null) {
                foreach ($segments as $segment) {
                    if (isset($segment['segment_slug']) && $segment['segment_slug'] === $role['segment_slug']) {
                        $enhancedRole['segment_data'] = $segment;
                        break;
                    }
                }
            }
            
            $enhancedRoles[] = $enhancedRole;
        }
        
        // Check if the member has display_product_roles flag
        $displayProductRoles = isset($memberData['display_product_roles']) ? $memberData['display_product_roles'] : false;
        
        // If display_product_roles is false, we will still return the roles but they won't be displayed
        $memberData['display_product_roles'] = $displayProductRoles;
        
        return new TeamMemberEnhanced($memberData, $enhancedRoles);
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
    
    /**
     * Debug method to check if a key exists in general.json
     * 
     * @param string $key The key to check
     * @return bool True if the key exists, false otherwise
     */
    public static function KeyExists($file, $key) {
        $data = self::loadDataInternal($file);
        return isset($data[$key]);
    }
} 