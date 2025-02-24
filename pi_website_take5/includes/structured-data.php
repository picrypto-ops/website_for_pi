<?php
function generateStructuredData($page, $data = []) {
    $structuredData = [
        "@context" => "https://schema.org",
        "@type" => "Organization",
        "name" => "PI Group",
        "url" => BASE_URL,
        "logo" => BASE_URL . "/assets/images/logo.png",
        "sameAs" => [
            "https://www.facebook.com/PIGroup",
            "https://www.linkedin.com/company/pigroup",
            "https://twitter.com/PIGroup"
        ]
    ];

    switch ($page) {
        case 'home':
            $structuredData["@type"] = "WebSite";
            break;
        case 'about':
            $structuredData["@type"] = "AboutPage";
            $structuredData["description"] = t('about_us_description');
            break;
        case 'contact':
            $structuredData["@type"] = "ContactPage";
            $structuredData["contactPoint"] = [
                "@type" => "ContactPoint",
                "telephone" => $data['phone'],
                "contactType" => "customer service"
            ];
            break;
        case 'team-member':
            $structuredData = [
                "@context" => "https://schema.org",
                "@type" => "Person",
                "name" => $data['name'],
                "jobTitle" => $data['title'],
                "description" => t($data['short_bio']),
                "image" => BASE_URL . "/assets/images/team/" . $data['image'],
                "worksFor" => [
                    "@type" => "Organization",
                    "name" => "PI Group"
                ]
            ];
            break;
    }

    return json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

