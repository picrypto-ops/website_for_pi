<?php
/**
 * Enhanced Translatable for team members with product roles
 */
class TeamMemberEnhanced extends AbstractTranslatable {
    /**
     * Team member data
     * @var array
     */
    protected $memberData;
    
    /**
     * Product roles data for the team member
     * @var array
     */
    protected $productRoles = [];
    
    /**
     * Constructor
     * 
     * @param array $memberData Team member data
     * @param array $productRoles Product roles for the team member
     */
    public function __construct($memberData, $productRoles = []) {
        parent::__construct($memberData);
        $this->memberData = $memberData;
        $this->productRoles = $productRoles;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function key_exists($lang, $key) {
        // Check if the key is 'product_roles'
        if ($key === 'product_roles') {
            return true;
        }
        
        // First check in main member data
        if (isset($this->memberData[$key])) {
            // Direct key for non-translatable content
            return true;
        } else if (isset($this->memberData['language_slug']) && 
            isset($this->memberData['language_slug'][$lang]) && 
            isset($this->memberData['language_slug'][$lang][$key])) {
            // Translated key
            return true;
        }
        
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function get_key($lang, $key) {
        // Special handling for product_roles
        if ($key === 'product_roles') {
            return $this->productRoles;
        }
        
        // Special handling for position when display_product_roles is true
        if ($key === 'position' && isset($this->memberData['display_product_roles']) && $this->memberData['display_product_roles'] === true) {
            $positionRoles = [];
            
            // Process each product role
            foreach ($this->productRoles as $role) {
                if (isset($role['language_slug'][$lang]['title'])) {
                    $roleData = [
                        'segment_slug' => $role['segment_slug'] ?? null,
                        'product_slug' => $role['product_slug'] ?? null,
                        'title' => $role['language_slug'][$lang]['title']
                    ];
                    
                    // Try to get product name from product_data (first approach)
                    $productName = null;
                    if (isset($role['product_data']) && 
                        isset($role['product_data']['language_slug']) && 
                        isset($role['product_data']['language_slug'][$lang]) && 
                        isset($role['product_data']['language_slug'][$lang]['name'])) {
                        $productName = $role['product_data']['language_slug'][$lang]['name'];
                    } 
                    // If first approach fails, try to find it through the productRoles array
                    else {
                        // Look through all product roles to find products with matching product_slug
                        foreach ($this->productRoles as $searchRole) {
                            if (isset($searchRole['product_data']) && 
                                isset($searchRole['product_slug']) && 
                                $searchRole['product_slug'] === $role['product_slug']) {
                                if (isset($searchRole['product_data']['language_slug'][$lang]['name'])) {
                                    $productName = $searchRole['product_data']['language_slug'][$lang]['name'];
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Manually fetch product from TranslatableFactory if still not found
                    if ($productName === null && isset($role['product_slug']) && $role['product_slug'] !== null) {
                        try {
                            $productTranslatable = TranslatableFactory::product($role['product_slug']);
                            if ($productTranslatable !== null) {
                                $productName = $productTranslatable->getContent($lang, 'name', null);
                            }
                        } catch (Exception $e) {
                            // Silently fail and continue
                        }
                    }
                    
                    if ($productName !== null) {
                        $roleData['product_name'] = $productName;
                    }
                    
                    $positionRoles[] = $roleData;
                }
            }
            
            // Return the array of roles if not empty, otherwise fall back to regular position
            if (!empty($positionRoles)) {
                return $positionRoles;
            }
        }
        
        // Main data handling
        if (isset($this->memberData[$key])) {
            // Direct key for non-translatable content
            return $this->memberData[$key];
        } else if (isset($this->memberData['language_slug']) && 
            isset($this->memberData['language_slug'][$lang]) && 
            isset($this->memberData['language_slug'][$lang][$key])) {
            // Translated key
            return $this->memberData['language_slug'][$lang][$key];
        }
        
        throw new Exception("Translation key '{$key}' not found for language '{$lang}'");
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllContent($lang) {
        $result = [];
        
        // Include all direct properties
        foreach ($this->memberData as $key => $value) {
            if ($key !== 'language_slug') {
                $result[$key] = $value;
            }
        }
        
        // Include all language-specific properties
        if (isset($this->memberData['language_slug']) && isset($this->memberData['language_slug'][$lang])) {
            foreach ($this->memberData['language_slug'][$lang] as $key => $value) {
                // Special handling for 'position' when display_product_roles is true
                if ($key === 'position' && isset($this->memberData['display_product_roles']) && 
                    $this->memberData['display_product_roles'] === true) {
                    // Use get_key to handle the position transformation
                    $result[$key] = $this->get_key($lang, 'position');
                } else {
                    $result[$key] = $value;
                }
            }
        }
        
        // Add product roles
        $result['product_roles'] = $this->productRoles;
        
        return $result;
    }
    
    /**
     * Get all product roles for the team member
     * 
     * @param string $lang Language code
     * @return array Array of product roles with segment, product, and title information
     */
    public function getProductRoles($lang) {
        return $this->productRoles;
    }
} 