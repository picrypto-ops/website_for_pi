<?php
/**
 * Structured Data Generator
 *
 * Generates Schema.org JSON-LD structured data for different page types.
 * Uses data from structured-data.json for constants and configuration.
 */

/**
 * Load structured data configuration from JSON
 * 
 * @return array The structured data configuration
 */
function loadStructuredDataConfig() {
    $configFile = __DIR__ . '/../../data/structured-data.json';
    if (!file_exists($configFile)) {
        error_log("Structured data config file not found: $configFile");
        return [];
    }
    
    $config = json_decode(file_get_contents($configFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error parsing structured data config: " . json_last_error_msg());
        return [];
    }
    
    return $config;
}

/**
 * Generate structured data for the current page
 * 
 * @param string $page Current page identifier
 * @param array $data Additional data specific to the current page
 * @return string JSON-LD formatted structured data
 */
function generateStructuredData($page, $data = []) {
    $config = loadStructuredDataConfig();
    if (empty($config)) {
        return '';
    }
    
    // Default organization data (always included)
    $structuredData = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => $config['organization']['name'],
        "url" => defined('BASE_URL') ? BASE_URL : $config['organization']['url'],
        "logo" => defined('BASE_URL') ? BASE_URL . $config['organization']['logo'] : $config['organization']['logo'],
        "sameAs" => [
            $config['organization']['socialProfiles']['facebook'],
            $config['organization']['socialProfiles']['linkedin'],
            $config['organization']['socialProfiles']['twitter']
        ]
    ];
    
    // Add address if available
    if (isset($config['organization']['address'])) {
        $structuredData["address"] = [
            "@type" => "PostalAddress",
            "streetAddress" => $config['organization']['address']['streetAddress'],
            "addressLocality" => $config['organization']['address']['addressLocality'],
            "addressRegion" => $config['organization']['address']['addressRegion'],
            "postalCode" => $config['organization']['address']['postalCode'],
            "addressCountry" => $config['organization']['address']['addressCountry']
        ];
    }

    // Add founding info if available
    if (isset($config['organization']['foundingDate'])) {
        $structuredData["foundingDate"] = $config['organization']['foundingDate'];
    }
    
    if (isset($config['organization']['founders'])) {
        $structuredData["founder"] = [
            "@type" => "Person",
            "name" => $config['organization']['founders']
        ];
    }

    // Page-specific structured data
    switch ($page) {
        case 'home':
            $structuredData["@type"] = $config['pages']['home']['type'];
            $structuredData["description"] = $config['pages']['home']['description'];
            
            // Add headline if available
            if (isset($config['pages']['home']['title'])) {
                $structuredData["headline"] = strip_tags($config['pages']['home']['title']);
            }
            
            // Add WebSite specific markup
            $structuredData["potentialAction"] = [
                "@type" => "SearchAction",
                "target" => defined('BASE_URL') ? BASE_URL . "/search?q={search_term_string}" : "/search?q={search_term_string}",
                "query-input" => "required name=search_term_string"
            ];
            break;
            
        case 'about':
            $structuredData["@type"] = $config['pages']['about']['type'];
            $structuredData["description"] = $config['pages']['about']['description'];
            
            // Add headline if available
            if (isset($config['pages']['about']['title'])) {
                $structuredData["headline"] = $config['pages']['about']['title'];
            }
            break;
            
        case 'contact':
            $structuredData["@type"] = $config['pages']['contact']['type'];
            $structuredData["description"] = $config['pages']['contact']['description'];
            
            // Add headline if available
            if (isset($config['pages']['contact']['title'])) {
                $structuredData["headline"] = $config['pages']['contact']['title'];
            }
            
            $structuredData["contactPoint"] = [
                "@type" => "ContactPoint",
                "telephone" => $data['phone'] ?? $config['organization']['contactPoint']['telephone'],
                "email" => $data['email'] ?? $config['organization']['contactPoint']['email'],
                "contactType" => $config['organization']['contactPoint']['contactType']
            ];
            break;
            
        case 'our-team':
            $structuredData["@type"] = $config['pages']['our-team']['type'];
            $structuredData["description"] = $config['pages']['our-team']['description'];
            
            // Add headline if available
            if (isset($config['pages']['our-team']['title'])) {
                $structuredData["headline"] = $config['pages']['our-team']['title'];
            }
            break;
            
        case 'team-member':
            // Create a different schema for individual team members
            $structuredData = [
                "@context" => "https://schema.org",
                "@type" => "Person",
                "name" => $data['name'] ?? '',
                "jobTitle" => $data['title'] ?? '',
                "description" => isset($data['bio']) ? strip_tags($data['bio']) : '',
                "image" => defined('BASE_URL') ? BASE_URL . "/assets/images/team/" . ($data['image'] ?? '') : "/assets/images/team/" . ($data['image'] ?? ''),
                "worksFor" => [
                    "@type" => "Organization",
                    "name" => $config['organization']['name'],
                    "url" => defined('BASE_URL') ? BASE_URL : $config['organization']['url']
                ]
            ];
            
            // Add social profiles if available
            if (!empty($data['linkedin'])) {
                $structuredData["sameAs"] = [$data['linkedin']];
            }
            break;
            
        case 'segment':
            // For segment pages (investment_banking or asset_management)
            $segmentId = $data['segment_id'] ?? '';
            if (isset($config['pages'][$segmentId])) {
                $structuredData["@type"] = $config['pages'][$segmentId]['type'];
                $structuredData["name"] = $config['pages'][$segmentId]['name'];
                $structuredData["description"] = $config['pages'][$segmentId]['description'];
                
                // Add headline/title if available
                if (isset($config['pages'][$segmentId]['title'])) {
                    $structuredData["headline"] = $config['pages'][$segmentId]['title'];
                }
                
                // Add offer details for services
                $structuredData["offers"] = [
                    "@type" => "Offer",
                    "availability" => "https://schema.org/InStock",
                    "price" => "0",  // Consulting services often don't show price
                    "priceCurrency" => "ILS",
                    "seller" => [
                        "@type" => "Organization",
                        "name" => $config['organization']['name']
                    ]
                ];
            }
            break;
    }

    // Also generate breadcrumb structured data
    $breadcrumbData = generateBreadcrumbData($page, $data);
    
    // Return both structured data objects
    return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . 
           ($breadcrumbData ? "\n" . json_encode($breadcrumbData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : '');
}

/**
 * Generate breadcrumb structured data
 * 
 * @param string $page Current page identifier
 * @param array $data Additional data specific to the current page
 * @return array Breadcrumb structured data
 */
function generateBreadcrumbData($page, $data = []) {
    $config = loadStructuredDataConfig();
    if (empty($config)) {
        return null;
    }
    
    $baseUrl = defined('BASE_URL') ? BASE_URL : $config['organization']['url'];
    $lang = isset($data['lang']) ? $data['lang'] : 'en';
    
    // Start with the home page
    $breadcrumbs = [
        [
            "@type" => "ListItem",
            "position" => 1,
            "name" => $config['pages']['home']['name'],
            "item" => "{$baseUrl}/?page=home&lang={$lang}"
        ]
    ];
    
    // Add current page to breadcrumb
    if ($page !== 'home') {
        $position = 2;
        
        // Handle segment pages
        if ($page === 'segment' && isset($data['segment_id']) && isset($config['pages'][$data['segment_id']])) {
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => $position,
                "name" => $config['pages'][$data['segment_id']]['name'],
                "item" => "{$baseUrl}/?page=segment&id={$data['segment_id']}&lang={$lang}"
            ];
        } 
        // Handle team member pages
        else if ($page === 'team-member' && isset($data['name'])) {
            // First add the team page
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => $position++,
                "name" => $config['pages']['our-team']['name'],
                "item" => "{$baseUrl}/?page=our-team&lang={$lang}"
            ];
            
            // Then add the team member
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => $position,
                "name" => $data['name'],
                "item" => "{$baseUrl}/?page=team-member&id={$data['id']}&lang={$lang}"
            ];
        }
        // Handle regular pages
        else if (isset($config['pages'][$page])) {
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => $position,
                "name" => $config['pages'][$page]['name'],
                "item" => "{$baseUrl}/?page={$page}&lang={$lang}"
            ];
        }
    }
    
    // Return complete breadcrumb data
    return [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $breadcrumbs
    ];
}

/**
 * Output structured data as JSON-LD
 * 
 * @param string $page Current page identifier
 * @param array $data Additional data
 * @return string HTML script tag with JSON-LD data
 */
function outputStructuredData($page, $data = []) {
    $structuredData = generateStructuredData($page, $data);
    if (empty($structuredData)) {
        return '';
    }
    
    // Split multiple JSON objects if present
    $jsonObjects = explode("\n", $structuredData);
    $output = '';
    
    foreach ($jsonObjects as $json) {
        if (!empty(trim($json))) {
            $output .= '<script type="application/ld+json">' . PHP_EOL;
            $output .= $json . PHP_EOL;
            $output .= '</script>' . PHP_EOL;
        }
    }
    
    return $output;
} 