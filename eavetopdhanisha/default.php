<?php
// Form handling at the top of the file
session_start();

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone
function validatePhone($phone) {
    return preg_match('/^\+?\d[\d\s-]{8,}$/', $phone);
}

// Function to save data to JSON file
function saveToJSON($filename, $data) {
    $jsonFile = __DIR__ . '/data/' . $filename;
    
    // Create data directory if it doesn't exist
    if (!file_exists(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }
    
    // Read existing data
    $existingData = [];
    if (file_exists($jsonFile)) {
        $existingData = json_decode(file_get_contents($jsonFile), true) ?: [];
    }
    
    // Add new data with timestamp
    $data['timestamp'] = date('Y-m-d H:i:s');
    $data['ip'] = $_SERVER['REMOTE_ADDR'];
    $existingData[] = $data;
    
    // Save back to file
    return file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];
    
    // Main Lead Form
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'lead') {
        $name = sanitizeInput($_POST['name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $unitType = sanitizeInput($_POST['unitType'] ?? '');
        $visit = sanitizeInput($_POST['visit'] ?? '');
        
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (!validatePhone($phone)) $errors[] = 'Valid phone number is required';
        if (!empty($email) && !validateEmail($email)) $errors[] = 'Valid email is required';
        
        if (empty($errors)) {
            $data = [
                'form_type' => 'main_lead',
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'unit_type' => $unitType,
                'visit_timing' => $visit
            ];
            
            if (saveToJSON('leads.json', $data)) {
                $response['success'] = true;
                $response['message'] = 'Thank you! We will reach out shortly.';
                
                // Send email notification (optional)
                $to = 'your-email@example.com'; // Change this
                $subject = 'New Lead - EAVETOP DHANISHA';
                $message = "New lead received:\n\n" . 
                          "Name: $name\n" .
                          "Phone: $phone\n" .
                          "Email: $email\n" .
                          "Unit Type: $unitType\n" .
                          "Visit: $visit";
                $headers = 'From: noreply@eavetopshelters.com';
                
                // Uncomment to enable email
                // mail($to, $subject, $message, $headers);
            } else {
                $response['message'] = 'Error saving data. Please try again.';
            }
        } else {
            $response['message'] = implode(', ', $errors);
        }
    }
    
    // Callback Form
    elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'callback') {
        $name = sanitizeInput($_POST['cbname'] ?? '');
        $phone = sanitizeInput($_POST['cbphone'] ?? '');
        
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (!validatePhone($phone)) $errors[] = 'Valid phone number is required';
        
        if (empty($errors)) {
            $data = [
                'form_type' => 'callback_request',
                'name' => $name,
                'phone' => $phone
            ];
            
            if (saveToJSON('callbacks.json', $data)) {
                $response['success'] = true;
                $response['message'] = 'Thanks! We\'ll call you soon.';
            } else {
                $response['message'] = 'Error saving data. Please try again.';
            }
        } else {
            $response['message'] = implode(', ', $errors);
        }
    }
    
    // Floor Plan Form
    elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'floor_plan') {
        $name = sanitizeInput($_POST['fp-name'] ?? '');
        $phone = sanitizeInput($_POST['fp-phone'] ?? '');
        $email = sanitizeInput($_POST['fp-email'] ?? '');
        $action = sanitizeInput($_POST['fp-action'] ?? '');
        $unitInfo = $_POST['fp-unit-info'] ?? '';
        
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (!validatePhone($phone)) $errors[] = 'Valid phone number is required';
        if (!empty($email) && !validateEmail($email)) $errors[] = 'Valid email is required';
        
        if (empty($errors)) {
            $data = [
                'form_type' => 'floor_plan_request',
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'action' => $action,
                'unit_info' => json_decode($unitInfo, true)
            ];
            
            if (saveToJSON('floor_plan_requests.json', $data)) {
                $response['success'] = true;
                $response['message'] = 'Thank you! Floor plan details will be sent shortly.';
                $response['action'] = $action;
            } else {
                $response['message'] = 'Error saving data. Please try again.';
            }
        } else {
            $response['message'] = implode(', ', $errors);
        }
    }
    
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=5" name="viewport" />
    <meta content="#0F3D3E" name="theme-color" />
    <title>
        EAVETOP DHANISHA — Luxury 2 &amp; 3 BHK Flats @ Bilekahalli, Off Bannerghatta Road | EAVETOP SHELTERS
    </title>
    <meta
        content="EAVETOP DHANISHA offers Luxury 2 &amp; 3 BHK flats at Bilekahalli, Off Bannerghatta Road, Bengaluru. BBMP Approved, Vaastu compliant, RCC Zone II structure. Close to IIMB, Fortis Hospital, Royal Meenakshi Mall, Vega City, Cinepolis SJR, and metro access. Book a site visit."
        name="description" />
    <link href="https://www.example.com/eavetop-dhanisha" rel="canonical" />
    <!-- Open Graph -->
    <meta content="website" property="og:type" />
    <meta content="EAVETOP DHANISHA — Luxury 2 &amp; 3 BHK Flats @ Bilekahalli, Off Bannerghatta Road"
        property="og:title" />
    <meta
        content="BBMP Approved, Vaastu compliant 2 &amp; 3 BHK apartments near IIMB, Fortis, Royal Meenakshi Mall, Vega City &amp; metro. Book a visit."
        property="og:description" />
    <meta content="https://www.example.com/eavetop-dhanisha" property="og:url" />
    <meta content="https://placehold.co/1200x630/png?text=EAVETOP+DHANISHA+Hero+Image" property="og:image" />
    <meta content="EAVETOP SHELTERS" property="og:site_name" />
    <!-- Twitter -->
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="EAVETOP DHANISHA — Luxury 2 &amp; 3 BHK Flats @ Bilekahalli, Off Bannerghatta Road"
        name="twitter:title" />
    <meta
        content="BBMP Approved, Vaastu compliant apartments near IT hubs, schools, hospitals, malls and metro in South Bengaluru."
        name="twitter:description" />
    <meta content="https://placehold.co/1200x630/png?text=EAVETOP+DHANISHA+Hero+Image" name="twitter:image" />
    <!-- Fonts (minimal) -->
    <link crossorigin="" href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap"
        rel="stylesheet" />
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com">
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
                        serif: ['Playfair Display', 'ui-serif', 'Georgia', 'Cambria', 'Times New Roman', 'Times', 'serif'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#0F3D3E',    // deep teal
                            dark: '#0B2D2E',
                            light: '#156064',
                            gold: '#C7A76B',       // premium accent
                            fog: '#F4F6F7',
                        },
                        ink: {
                            900: '#0E1215',
                            800: '#1A2227',
                            700: '#2A343B',
                            600: '#44515B',
                            500: '#6B7780',
                            400: '#8D99A1',
                            300: '#B1BAC0',
                            200: '#D5DBDF',
                            100: '#E9EEF1',
                        }
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(0,0,0,0.08)',
                        glow: '0 8px 24px rgba(199, 167, 107, 0.35)',
                    },
                    borderRadius: {
                        mdx: '14px',
                        lgx: '18px',
                    },
                    transitionTimingFunction: {
                        smooth: 'cubic-bezier(0.22, 1, 0.36, 1)',
                    },
                    screens: {
                        'xs': '420px',
                    },
                    fontSize: {
                        // Mobile-optimized font sizes
                        'xs': ['0.75rem', { lineHeight: '1rem' }],
                        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
                        'base': ['1rem', { lineHeight: '1.5rem' }],
                        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
                        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
                        '2xl': ['1.5rem', { lineHeight: '2rem' }],
                        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
                        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
                        '5xl': ['3rem', { lineHeight: '1.2' }],
                    }
                }
            }
        }
    </script>
    <!-- Enhanced Mobile Styles -->
    <style>
        :root {
            --brand: #0F3D3E;
            --brand-dark: #0B2D2E;
            --brand-gold: #C7A76B;
            --ink-900: #0E1215;
            --ink-600: #44515B;
            --bg: #FFFFFF;
            --radius-md: 14px;
            --radius-lg: 18px;
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.08);
            --shadow-gold: 0 8px 24px rgba(199, 167, 107, 0.35);
            --safe-area-inset-bottom: env(safe-area-inset-bottom, 0);
        }

        /* Mobile-first typography */
        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        /* Smooth scrolling with overscroll for iOS */
        body {
            overscroll-behavior-y: none;
            -webkit-overflow-scrolling: touch;
        }

        /* Better touch targets */
        button,
        a,
        input,
        select,
        textarea {
            min-height: 44px;
            touch-action: manipulation;
        }

        /* Mobile input zoom prevention */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            font-size: 16px !important;
        }

        /* Safe area padding for iOS */
        .safe-bottom {
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        .brand-gradient {
            background-image: linear-gradient(135deg, var(--brand), #17494B 55%, #1C5E61 100%);
        }

        .accent-border {
            box-shadow: inset 0 0 0 1px rgba(199, 167, 107, 0.35);
        }

        .gold-underline {
            background-image: linear-gradient(transparent 70%, rgba(199, 167, 107, 0.3) 70%);
        }

        /* Reveal animations - disabled on mobile for performance */
        @media (min-width: 768px) {
            [data-reveal] {
                opacity: 0;
                transform: translateY(16px);
                transition: opacity .7s ease, transform .7s ease;
            }

            [data-reveal].is-visible {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 767px) {
            [data-reveal] {
                opacity: 1 !important;
                transform: none !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }

            [data-reveal] {
                opacity: 1 !important;
                transform: none !important;
            }
        }

        /* Mobile menu overlay - improved */
        .menu-panel {
            transform: translateX(100%);
            transition: transform .3s cubic-bezier(0.22, 1, 0.36, 1);
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 320px;
            z-index: 50;
            background: white;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .menu-panel.open {
            transform: translateX(0);
        }

        .menu-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 49;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease, visibility .3s ease;
        }

        .menu-backdrop.open {
            opacity: 1;
            visibility: visible;
        }

        /* Lightbox improvements */
        .lightbox-open {
            overflow: hidden;
        }

        .lightbox-backdrop {
            background: rgba(14, 18, 21, .92);
            backdrop-filter: blur(4px);
        }

        /* Focus improvements */
        .focus-ring:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(199, 167, 107, .55);
            border-radius: 10px;
        }

        /* No scrollbar utility */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Mobile sticky bottom bar improvements */
        .mobile-sticky-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 40;
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* Swipeable hint */
        .swipe-hint {
            position: relative;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }

        .swipe-hint>* {
            scroll-snap-align: start;
        }

        /* Loading skeleton */
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
    </style>
    <!-- JSON-LD Schema (unchanged) -->
    <script type="application/ld+json">
   {
    "@context": "https://schema.org",
    "@type": "ApartmentComplex",
    "name": "EAVETOP DHANISHA",
    "slogan": "Luxury 2 & 3 BHK Flats @ Bilekahalli, Off Bannerghatta Road",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "15B, 4th Cross, Annayappa Garden, Bilekahalli Main Road",
      "addressLocality": "Bengaluru",
      "addressRegion": "Karnataka",
      "postalCode": "560076",
      "addressCountry": "IN"
    },
    "telephone": "+919066626662",
    "url": "https://www.example.com/eavetop-dhanisha",
    "amenityFeature": [
      {"@type":"LocationFeatureSpecification","name":"Party Hall"},
      {"@type":"LocationFeatureSpecification","name":"Gym"},
      {"@type":"LocationFeatureSpecification","name":"Swimming Pool"},
      {"@type":"LocationFeatureSpecification","name":"Children Play Area"},
      {"@type":"LocationFeatureSpecification","name":"Power Backup"},
      {"@type":"LocationFeatureSpecification","name":"Car Parking"},
      {"@type":"LocationFeatureSpecification","name":"Rain Water Harvesting"},
      {"@type":"LocationFeatureSpecification","name":"CCTV Surveillance"},
      {"@type":"LocationFeatureSpecification","name":"24 Hrs Water Supply"},
      {"@type":"LocationFeatureSpecification","name":"24 Hrs Security"}
    ],
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": 12.9000,
      "longitude": 77.6000
    },
    "hasMap": "https://goo.gl/maps/placeholder"
  }
  </script>
</head>

<body class="bg-white text-ink-900 antialiased">
    <a class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:bg-brand text-white px-3 py-2 rounded-md"
        href="#main">
        Skip to content
    </a>
    <!-- Header - Mobile Optimized -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-ink-100">
        <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between">
            <a aria-label="EAVETOP DHANISHA home" class="flex items-center gap-2 sm:gap-3" href="#">
                <div
                    class="h-8 w-8 sm:h-9 sm:w-9 rounded-lg bg-brand flex items-center justify-center text-white font-semibold text-sm">
                    ED
                </div>
                <div class="leading-tight">
                    <div class="font-serif text-base sm:text-lg tracking-tight">
                        EAVETOP DHANISHA
                    </div>
                    <div class="text-[10px] sm:text-xs text-ink-500">
                        By EAVETOP SHELTERS
                    </div>
                </div>
            </a>
            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm">
                <a aria-label="About" class="hover:text-brand transition-colors" href="#about">
                    About
                </a>
                <a aria-label="Floor Plans" class="hover:text-brand transition-colors" href="#plans">
                    Floor Plans
                </a>
                <a aria-label="Amenities" class="hover:text-brand transition-colors" href="#amenities">
                    Amenities
                </a>
                <a aria-label="Specifications" class="hover:text-brand transition-colors" href="#specs">
                    Specifications
                </a>
                <a aria-label="Location" class="hover:text-brand transition-colors" href="#location">
                    Location
                </a>
                <a aria-label="Gallery" class="hover:text-brand transition-colors" href="#gallery">
                    Gallery
                </a>
                <a aria-label="Contact" class="hover:text-brand transition-colors" href="#contact">
                    Contact
                </a>
                <a class="inline-flex items-center gap-2 bg-brand text-white px-4 py-2.5 rounded-lg hover:bg-brand-dark transition-colors focus-ring"
                    href="#lead">
                    Book a Visit
                </a>
            </nav>
            <!-- Mobile CTA and Menu -->
            <div class="flex items-center gap-2 lg:hidden">
                <a aria-label="Call us"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-brand text-white"
                    href="tel:+919066626662">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                        <path
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                            stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg>
                </a>
                <button aria-controls="mobileMenu" aria-expanded="false" aria-label="Open menu"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-ink-200 hover:bg-ink-50 transition-colors focus-ring"
                    id="menuToggle">
                    <svg class="w-5 h-5 text-ink-800" fill="none" stroke="currentColor" stroke-width="2"
                        viewbox="0 0 24 24">
                        <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </header>
    <!-- Mobile menu backdrop -->
    <div class="menu-backdrop" id="menuBackdrop">
    </div>
    <!-- Mobile menu panel -->
    <nav aria-label="Mobile navigation" class="menu-panel" id="mobileMenu">
        <div class="flex items-center justify-between p-4 border-b border-ink-100">
            <div class="font-serif text-lg">
                Menu
            </div>
            <button aria-label="Close menu"
                class="inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-ink-50 transition-colors"
                id="menuClose">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round">
                    </path>
                </svg>
            </button>
        </div>
        <div class="p-4 space-y-1">
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#about">
                About
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#plans">
                Floor Plans
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#amenities">
                Amenities
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#specs">
                Specifications
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#location">
                Location
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#gallery">
                Gallery
            </a>
            <a class="block py-3 px-3 rounded-lg hover:bg-ink-50 transition-colors" href="#contact">
                Contact
            </a>
        </div>
        <div class="p-4 border-t border-ink-100">
            <a class="block w-full text-center bg-brand text-white px-4 py-3 rounded-lg hover:bg-brand-dark transition-colors"
                href="#lead">
                Book a Visit
            </a>
            <a class="mt-2 block w-full text-center border border-ink-200 px-4 py-3 rounded-lg hover:bg-ink-50 transition-colors"
                href="tel:+919066626662">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                        <path
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                            stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg>
                    Call +91 90666 26662
                </span>
            </a>
        </div>
    </nav>
    <main id="main">
        <!-- Hero - Mobile Optimized -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 brand-gradient">
            </div>
            <div aria-hidden="true" class="absolute inset-0 -z-10 opacity-10"
                style="background-image: radial-gradient(600px 300px at 10% 10%, rgba(255,255,255,0.5), transparent 60%), radial-gradient(700px 400px at 90% 30%, rgba(199,167,107,0.25), transparent 60%);">
            </div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-20">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-center">
                    <div class="text-white order-2 lg:order-1" data-reveal="">
                        <!-- Badge -->
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 ring-1 ring-white/20 text-[11px] sm:text-xs tracking-wide">
                            <span class="hidden sm:inline">
                                BBMP Approved • As per Vaastu • RCC Structure
                            </span>
                            <span class="sm:hidden">
                                BBMP Approved • Vaastu Compliant
                            </span>
                        </div>
                        <!-- Heading - Responsive -->
                        <h1 class="mt-4 font-serif text-2xl xs:text-3xl sm:text-4xl md:text-5xl leading-tight">
                            EAVETOP DHANISHA
                            <span class="block text-xl xs:text-2xl sm:text-3xl md:text-4xl mt-2 text-white/90">
                                Luxury 2 &amp; 3 BHK Flats @ Bilekahalli
                            </span>
                        </h1>
                        <p class="mt-4 text-base sm:text-lg text-white/90">
                            Home is where you always want to be.
                        </p>
                        <p class="mt-3 text-sm sm:text-base text-white/85 max-w-prose">
                            Live close to what matters: IIM Bangalore, Fortis Hospital, Royal Meenakshi Mall, and metro
                            access—right
                            in South Bengaluru's prime corridor.
                        </p>
                        <!-- CTAs - Mobile Optimized -->
                        <div class="mt-6 flex flex-col xs:flex-row gap-3">
                            <a class="inline-flex items-center justify-center gap-2 bg-white text-brand font-medium px-5 py-3 rounded-lg hover:bg-ink-100 transition-colors focus-ring"
                                href="tel:+919066626662">
                                <svg class="text-brand" fill="none" height="18" viewbox="0 0 24 24" width="18">
                                    <path
                                        d="M22 16.92v2a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.13 1h2A2 2 0 0 1 8 2.72 13 13 0 0 0 9.46 6a2 2 0 0 1-.45 2.11L8 9.14a16 16 0 0 0 6 6l1-1a2 2 0 0 1 2.11-.45 13 13 0 0 0 3.31 1.51A2 2 0 0 1 22 16.92Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-width="2">
                                    </path>
                                </svg>
                                Call Now
                            </a>
                            <a class="inline-flex items-center justify-center gap-2 bg-brand-gold text-ink-900 font-medium px-5 py-3 rounded-lg hover:shadow-glow transition focus-ring"
                                href="#lead">
                                Book a Visit
                            </a>
                        </div>
                        <!-- Stats Cards - Mobile Optimized -->
                        <div class="mt-6 sm:mt-8 grid grid-cols-2 gap-3 max-w-lg">
                            <div class="rounded-lg bg-white/10 ring-1 ring-white/20 p-3 sm:p-4">
                                <div class="text-xs sm:text-sm text-white/70">
                                    Configurations
                                </div>
                                <div class="text-base sm:text-lg font-semibold">
                                    2 &amp; 3 BHK
                                </div>
                            </div>
                            <div class="rounded-lg bg-white/10 ring-1 ring-white/20 p-3 sm:p-4">
                                <div class="text-xs sm:text-sm text-white/70">
                                    Location
                                </div>
                                <div class="text-base sm:text-lg font-semibold">
                                    Off Bannerghatta Rd
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Lead Form - Mobile Optimized (UPDATED - Removed Message field) -->
                    <div class="relative order-1 lg:order-2" data-reveal="" id="lead">
                        <div class="rounded-xl sm:rounded-lgx bg-white shadow-soft p-5 sm:p-6 md:p-8">
                            <h2 class="font-serif text-xl sm:text-2xl text-ink-900">
                                Enquire Now
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-ink-600">
                                Get availability, pricing, and site-visit slots.
                            </p>
                            <form class="mt-4 sm:mt-5 space-y-4" id="leadForm" novalidate="">
                                <div>
                                    <label class="block text-sm font-medium text-ink-700 mb-1" for="name">
                                        Full Name
                                    </label>
                                    <input autocomplete="name"
                                        class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                        id="name" name="name" placeholder="Your name" required="" type="text" />
                                    <p class="mt-1 hidden text-xs text-red-600" data-error-for="name">
                                        Please enter your name.
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 mb-1" for="phone">
                                            Phone
                                        </label>
                                        <input
                                            class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                            id="phone" inputmode="tel" name="phone" placeholder="+91 9XXXXXXXXX"
                                            required="" type="tel" />
                                        <p class="mt-1 hidden text-xs text-red-600" data-error-for="phone">
                                            Enter a valid phone.
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 mb-1" for="email">
                                            Email
                                        </label>
                                        <input autocomplete="email"
                                            class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                            id="email" name="email" placeholder="name@email.com" type="email" />
                                        <p class="mt-1 hidden text-xs text-red-600" data-error-for="email">
                                            Enter a valid email.
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 mb-1" for="unitType">
                                            Preferred Unit
                                        </label>
                                        <select
                                            class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                            id="unitType" name="unitType">
                                            <option>
                                                2 BHK
                                            </option>
                                            <option>
                                                3 BHK
                                            </option>
                                            <option>
                                                Undecided
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-ink-700 mb-1" for="visit">
                                            Visit Timing
                                        </label>
                                        <select
                                            class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                            id="visit" name="visit">
                                            <option>
                                                Within 48 hours
                                            </option>
                                            <option>
                                                This week
                                            </option>
                                            <option>
                                                This weekend
                                            </option>
                                            <option>
                                                Next week
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2 text-xs text-ink-600">
                                    <input checked="" class="mt-1 rounded border-ink-300 text-brand focus:ring-brand"
                                        id="consent" type="checkbox" />
                                    <label for="consent">
                                        I agree to be contacted for this enquiry.
                                    </label>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <button
                                        class="flex-1 inline-flex justify-center items-center px-5 py-3 rounded-lg bg-brand text-white hover:bg-brand-dark transition-colors focus-ring"
                                        type="submit">
                                        Submit Enquiry
                                    </button>
                                    <a class="flex-1 inline-flex items-center justify-center px-4 py-3 rounded-lg border border-ink-200 text-ink-800 hover:bg-ink-50 transition-colors focus-ring"
                                        href="tel:+919066116688">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                            viewbox="0 0 24 24">
                                            <path
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                                                stroke-linecap="round" stroke-linejoin="round">
                                            </path>
                                        </svg>
                                        Call Now
                                    </a>
                                </div>
                                <p aria-live="polite" class="text-sm mt-2" id="formStatus" role="status">
                                </p>
                            </form>
                            <div class="mt-4 text-[10px] sm:text-xs text-ink-500">
                                BBMP Approved • As per Vaastu • RCC framed structure
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- About - Mobile Optimized -->
        <section class="py-12 sm:py-16 lg:py-20" id="about">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-8 lg:gap-10 items-center">
                    <div data-reveal="" class="is-visible">
                        <h2 class="font-serif text-2xl sm:text-3xl">
                            Crafted for comfort, designed for life
                        </h2>
                        <p class="mt-4 text-sm sm:text-base text-ink-700">
                            EAVETOP DHANISHA brings together thoughtful planning, refined finishes, and reliable build
                            quality in a
                            calm pocket of Bilekahalli, just off Bannerghatta Road. With Vaastu-compliant layouts and
                            curated
                            amenities, your home stays functional and future-ready.
                        </p>
                        <p class="mt-3 text-sm sm:text-base text-ink-700">
                            Built by EAVETOP SHELTERS with a commitment to integrity, transparent processes, and
                            on-schedule delivery.
                        </p>
                        <!-- Features List - Mobile Optimized -->
                        <ul class="mt-6 space-y-3 text-sm sm:text-base text-ink-800">
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    BBMP Approved. Clear titles.
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Vaastu-compliant homes.
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    RCC framed structure (Zone II).
                                </span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Near IT hubs, schools, hospitals &amp; metro.
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="relative is-visible" data-reveal="">
                        <img alt="Premium residential facade at EAVETOP DHANISHA, showing modern architecture with large windows and landscaped surroundings"
                            decoding="async" height="1404" loading="lazy"
                            src="https://i.ibb.co/8gTFNT5N/Evetop-elevation.png" width="2140"
                            class="w-full h-auto rounded-lg sm:rounded-lgx shadow-soft object-cover">
                        <!-- Badge - Hidden on mobile -->
                        <div
                            class="absolute -bottom-3 -right-3 bg-white/95 backdrop-blur rounded-lg sm:rounded-mdx shadow-soft p-3 hidden sm:flex items-center gap-3">
                            <svg fill="none" height="26" viewBox="0 0 24 24" width="26">
                                <path d="M12 2l7 7-7 7-7-7 7-7z" stroke="#C7A76B" stroke-width="1.5">
                                </path>
                            </svg>
                            <div class="text-xs sm:text-sm">
                                <span class="font-medium">
                                    Ready for your next chapter
                                </span>
                                <br>
                                <span class="text-ink-600">
                                    Thoughtful plans. Lasting value.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Floor Plans - Mobile Optimized (UPDATED - Changed download links to buttons) -->
        <section class="py-12 sm:py-16 lg:py-20 bg-brand-fog" id="plans">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                    <div data-reveal="">
                        <h2 class="font-serif text-2xl sm:text-3xl">
                            Floor plans and unit mix
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-ink-700">
                            Efficient 2 BHK and expansive 3 BHK options.
                        </p>
                    </div>
                    <!-- Desktop filters -->
                    <div class="hidden sm:flex gap-2" data-reveal="">
                        <button
                            class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm hover:bg-white transition-colors"
                            data-filter="all">
                            All
                        </button>
                        <button
                            class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm hover:bg-white transition-colors"
                            data-filter="2">
                            2 BHK
                        </button>
                        <button
                            class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm hover:bg-white transition-colors"
                            data-filter="3">
                            3 BHK
                        </button>
                    </div>
                </div>
                <!-- Mobile filters - Horizontal scroll -->
                <div class="mt-4 sm:hidden overflow-x-auto no-scrollbar" data-reveal="">
                    <div class="flex gap-2">
                        <button
                            class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm bg-white whitespace-nowrap"
                            data-filter="all">
                            All Units
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm whitespace-nowrap"
                            data-filter="2">
                            2 BHK
                        </button>
                        <button class="filter-btn px-4 py-2 rounded-lg border border-ink-200 text-sm whitespace-nowrap"
                            data-filter="3">
                            3 BHK
                        </button>
                    </div>
                </div>
                <!-- Units Grid - Mobile Optimized -->
                <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="unitGrid">
                    <!-- Unit Cards - Mobile Optimized -->
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="3" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 001
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                3 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1690 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    North
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="3 BHK 1690 sft North-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1690+sft+North-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1690+sft+North-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="3" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 002
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                3 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1460 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    North
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="3 BHK 1460 sft North-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1460+sft+North-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1460+sft+North-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="3" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 003
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                3 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1510 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    East
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="3 BHK 1510 sft East-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1510+sft+East-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1510+sft+East-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="3" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 004
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                3 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1470 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    East
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="3 BHK 1470 sft East-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1470+sft+East-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1470+sft+East-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="2" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 005
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                2 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1180 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    North
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="2 BHK 1180 sft North-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=2+BHK+1180+sft+North-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=2+BHK+1180+sft+North-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                    <article class="unit-card rounded-lg bg-white shadow-soft p-4 sm:p-5" data-bhk="3" data-reveal="">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-medium text-base">
                                Unit 006
                            </h3>
                            <span
                                class="inline-flex items-center rounded-full bg-ink-100 text-ink-700 text-xs px-2.5 py-1">
                                3 BHK
                            </span>
                        </div>
                        <dl class="text-sm grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Area
                                </dt>
                                <dd class="font-medium">
                                    1405 sft
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs text-ink-600">
                                    Facing
                                </dt>
                                <dd class="font-medium">
                                    North
                                </dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex gap-2">
                            <button
                                class="view-plan flex-1 px-3 py-2.5 rounded-lg bg-brand text-white text-sm hover:bg-brand-dark transition-colors"
                                data-alt="3 BHK 1405 sft North-facing floor plan"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1405+sft+North-facing+floor+plan">
                                View
                            </button>
                            <button
                                class="download-plan flex-1 px-3 py-2.5 rounded-lg border border-ink-200 text-sm text-center hover:bg-ink-50 transition-colors"
                                data-img="https://placehold.co/600x400/png?text=3+BHK+1405+sft+North-facing+floor+plan">
                                Download
                            </button>
                        </div>
                    </article>
                </div>
            </div>
        </section>
        
         <!-- Floor Plans Section -->
    <section id="floor-plans" class="py-12 md:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Floor Plans</h2>
            <p class="text-slate-700 mt-2 text-sm md:text-base">
                Sample 3D floor plan visuals include Unit 01 (3 BHK, 1690 sft, North-facing) and Unit 05 (2 BHK, 1180 sft, East-facing). 
                Typical floor plans show a central corridor with lift lobby and East/North-facing apartments to optimize light, ventilation, and privacy.
            </p>
            <div class="grid md:grid-cols-2 gap-4 md:gap-6 mt-8">
                <div class="reveal rounded-2xl overflow-hidden border border-slate-200">
                    <div class="bg-slate-100 h-48 md:h-56 flex items-center justify-center text-slate-500 text-xs md:text-sm p-4 text-center">
                        Floor Plan Placeholder — Unit 01 (3 BHK, 1690 sft, North)
                    </div>
                    <div class="p-4 md:p-5">
                        <div class="font-semibold text-sm md:text-base">Unit 01 — 3 BHK</div>
                        <div class="text-xs md:text-sm text-slate-600">1690 sft · North-facing · Multiple balconies</div>
                    </div>
                </div>
                <div class="reveal rounded-2xl overflow-hidden border border-slate-200">
                    <div class="bg-slate-100 h-48 md:h-56 flex items-center justify-center text-slate-500 text-xs md:text-sm p-4 text-center">
                        Floor Plan Placeholder — Unit 05 (2 BHK, 1180 sft, East)
                    </div>
                    <div class="p-4 md:p-5">
                        <div class="font-semibold text-sm md:text-base">Unit 05 — 2 BHK</div>
                        <div class="text-xs md:text-sm text-slate-600">1180 sft · East-facing · Efficient circulation</div>
                    </div>
                </div>
            </div>
            <!--<p class="text-xs text-slate-500 mt-3">Replace placeholders with final plan images.</p>-->
        </div>
    </section>
        
        <!-- Amenities - Mobile Optimized -->
        <section class="py-12 sm:py-16 lg:py-20" id="amenities">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-2xl" data-reveal="">
                    <h2 class="font-serif text-2xl sm:text-3xl">
                        Amenities that elevate everyday
                    </h2>
                    <p class="mt-2 text-sm sm:text-base text-ink-700">
                        Designed for family time, fitness, and peace of mind.
                    </p>
                </div>
                <!-- Amenities Grid - Mobile Optimized -->
                <div class="mt-6 sm:mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-5" data-reveal="">
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-glass-cheers text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Party Hall
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-dumbbell text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Gym
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-swimmer text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Swimming Pool
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-child text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Play Area
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-bolt text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Power Backup
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-car text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Car Parking
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-cloud-rain text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            Rain Harvesting
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-video text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            CCTV
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-tint text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            24/7 Water
                        </div>
                    </div>
                    <div
                        class="rounded-lg border border-ink-100 p-4 sm:p-5 hover:shadow-soft transition-shadow flex items-center gap-3">
                        <i class="fas fa-shield-alt text-brand-gold text-xl w-6 text-center">
                        </i>
                        <div class="text-sm sm:text-base font-medium">
                            24/7 Security
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Specifications - Mobile Optimized -->
        <section class="py-12 sm:py-16 lg:py-20 bg-brand-fog" id="specs">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl" data-reveal="">
                    <h2 class="font-serif text-2xl sm:text-3xl">
                        Specifications
                    </h2>
                    <p class="mt-2 text-sm sm:text-base text-ink-700">
                        Quality materials and finishes for longevity and comfort.
                    </p>
                </div>
                <!-- Specs Grid - Mobile Optimized -->
                <div class="mt-6 sm:mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Structure
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • RCC framed, Zone II regulations
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Walls
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • 6" external hollow blocks
                            </li>
                            <li>
                                • 4" internal solid blocks
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Flooring
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Vitrified tiles in living spaces
                            </li>
                            <li>
                                • Anti-skid in balconies
                            </li>
                            <li>
                                • Granite in common areas
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Doors &amp; Windows
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Teak main door
                            </li>
                            <li>
                                • Sal wood internal doors
                            </li>
                            <li>
                                • uPVC windows with MS grills
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Kitchen
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Granite counter &amp; SS sink
                            </li>
                            <li>
                                • Tile dado 2' above counter
                            </li>
                            <li>
                                • Provisions for appliances
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Painting
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Internal: Emulsion paint
                            </li>
                            <li>
                                • External: Weather-proof Ace
                            </li>
                            <li>
                                • Enamel for doors/grills
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Electrical
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Concealed copper wiring
                            </li>
                            <li>
                                • AC point in master bedroom
                            </li>
                            <li>
                                • Modular switches (Anchor Roma)
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Bathrooms
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • Anti-skid tiles, 7' dado
                            </li>
                            <li>
                                • Wall-mounted commodes
                            </li>
                            <li>
                                • CP fittings (Jaquar/Hindware)
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-lg bg-white p-4 sm:p-5 shadow-soft" data-reveal="">
                        <h3 class="font-medium text-base">
                            Lift
                        </h3>
                        <ul class="mt-2 text-xs sm:text-sm text-ink-700 space-y-1">
                            <li>
                                • 2 lifts, 6-passenger capacity
                            </li>
                            <li>
                                • ISI make
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- Location - Mobile Optimized -->
        <section class="py-12 sm:py-16 lg:py-20" id="location">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-8 lg:gap-10 items-start">
                    <div data-reveal="" class="is-visible">
                        <h2 class="font-serif text-2xl sm:text-3xl">
                            Connected to what matters
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-ink-700">
                            A calm residential pocket with quick access to IT corridors, education, healthcare, and
                            metro.
                        </p>
                        <!-- Location List - Mobile Optimized -->
                        <ul class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm sm:text-base text-ink-800">
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Bannerghatta Road corridor
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Royal Meenakshi Mall
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Fortis Hospital
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    IIM Bangalore
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Vega City Mall
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-1.5 h-2 w-2 rounded-full bg-brand-gold flex-shrink-0">
                                </span>
                                <span>
                                    Metro connectivity
                                </span>
                            </li>
                        </ul>
                        <!-- CTAs - Mobile Optimized -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg border border-ink-200 hover:bg-ink-50 transition-colors focus-ring"
                                href="https://goo.gl/maps/placeholder" rel="noopener" target="_blank">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                        stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round"
                                        stroke-linejoin="round">
                                    </path>
                                </svg>
                                Open in Maps
                            </a>
                            <a class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-lg bg-brand text-white hover:bg-brand-dark transition-colors focus-ring"
                                href="#lead">
                                Book a Site Visit
                            </a>
                        </div>
                    </div>
                    <div class="relative is-visible" data-reveal="">
                        <div class="rounded-lg sm:rounded-lgx overflow-hidden shadow-soft">
                            <!-- Responsive Map Container -->
                            <div class="relative w-full aspect-[4/3] sm:aspect-[16/10]">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15556.534660711279!2d77.6053846!3d12.899125500000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bae15202db06f77%3A0xa504fb0991814626!2sPunjab%20National%20Bank%20%26%20ATM!5e0!3m2!1sen!2sin!4v1757403224796!5m2!1sen!2sin"
                                    class="absolute inset-0 w-full h-full" style="border:0;" allowfullscreen=""
                                    loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-ink-500">
                            Map for representation. Use the link to navigate.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact - Mobile Optimized -->
        <section class="py-12 sm:py-16 lg:py-20" id="contact">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-3 gap-8 lg:gap-10">
                    <div class="lg:col-span-2" data-reveal="">
                        <h2 class="font-serif text-2xl sm:text-3xl">
                            Contact &amp; Booking
                        </h2>
                        <p class="mt-2 text-sm sm:text-base text-ink-700">
                            We're here to help with plans, pricing, and site visits.
                        </p>
                        <!-- Contact Cards - Mobile Optimized -->
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                            <div class="rounded-lg border border-ink-100 p-4 sm:p-5">
                                <div class="text-xs sm:text-sm text-ink-600">
                                    Primary Contacts
                                </div>
                                <div class="mt-2 space-y-1">
                                    <a class="block text-base font-medium hover:text-brand transition-colors"
                                        href="tel:+919066626662">
                                        +91 90666 26662
                                    </a>
                                    <a class="block text-base font-medium hover:text-brand transition-colors"
                                        href="tel:+919066116688">
                                        +91 90661 16688
                                    </a>
                                </div>
                            </div>
                            <div class="rounded-lg border border-ink-100 p-4 sm:p-5">
                                <div class="text-xs sm:text-sm text-ink-600">
                                    Project Office
                                </div>
                                <address class="not-italic mt-2 text-sm">
                                    15B, 4th Cross, Annayappa Garden,
                                    <br />
                                    Bilekahalli Main Road, Bengaluru – 560076
                                </address>
                            </div>
                        </div>
                        <!-- QR Code - Mobile Optimized -->
                        <div class="mt-6 flex items-center gap-4">
                            <div
                                class="w-24 h-24 sm:w-32 sm:h-32 rounded-lg border border-ink-100 flex items-center justify-center bg-white">
                                <img alt="QR code to scan and reach EAVETOP DHANISHA project office location"
                                    class="w-20 h-20 sm:w-28 sm:h-28 object-contain" decoding="async" height="112"
                                    loading="lazy"
                                    src="https://storage.googleapis.com/a1aa/image/4c2ae11b-8cb1-4d11-547c-d009a9dbca63.jpg"
                                    width="112" />
                            </div>
                            <div>
                                <div class="font-medium text-sm sm:text-base">
                                    Scan to Reach Us
                                </div>
                                <p class="text-ink-600 text-xs sm:text-sm">
                                    Get directions on your phone.
                                </p>
                            </div>
                        </div>
                        <!-- Disclaimer - Mobile Optimized -->
                        <div class="mt-8 p-3 bg-ink-50 rounded-lg text-[10px] sm:text-xs text-ink-600" id="disclaimer">
                            <strong>
                                Legal Disclaimer:
                            </strong>
                            The brochure/website content is conceptual and for reference only. Specifications,
                            elevations, floor
                            plans, and areas are subject to change without prior notice.
                        </div>
                    </div>
                    <!-- Callback Form - Mobile Optimized -->
                    <div class="lg:col-span-1" data-reveal="">
                        <div class="rounded-lg sm:rounded-lgx bg-white shadow-soft p-5 sm:p-6">
                            <h3 class="font-medium text-base sm:text-lg">
                                Have questions?
                            </h3>
                            <p class="text-xs sm:text-sm text-ink-700 mt-1">
                                Leave your details for a callback.
                            </p>
                            <form class="mt-4 space-y-3" id="callbackForm" novalidate="">
                                <input
                                    class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                    id="cbname" placeholder="Full Name" required="" type="text" />
                                <input
                                    class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                    id="cbphone" placeholder="Phone Number" required="" type="tel" />
                                <button
                                    class="w-full inline-flex justify-center items-center px-4 py-3 rounded-lg bg-brand text-white hover:bg-brand-dark transition-colors focus-ring"
                                    type="submit">
                                    Request Callback
                                </button>
                                <p aria-live="polite" class="text-sm" id="cbStatus" role="status">
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer - Mobile Optimized -->
    <footer class="border-t border-ink-100 py-6 sm:py-8 safe-bottom">
        <div
            class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs sm:text-sm text-ink-600">
                ©
                <span id="year">
                </span>
                EAVETOP SHELTERS. All rights reserved.
            </div>
            <div class="flex items-center gap-4 sm:gap-6 text-xs sm:text-sm">
                <a class="hover:text-brand transition-colors" href="#disclaimer">
                    Disclaimer
                </a>
                <a class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-brand text-white hover:bg-brand-dark transition-colors"
                    href="#lead">
                    Enquire
                </a>
            </div>
        </div>
    </footer>
    <!-- Mobile Sticky Bottom Bar - Enhanced -->
    <div class="mobile-sticky-bar sm:hidden">
        <div class="mx-3 mb-3 rounded-xl shadow-soft overflow-hidden bg-white border border-ink-100">
            <div class="grid grid-cols-2">
                <a class="flex items-center justify-center gap-2 py-4 text-brand font-medium active:bg-ink-50"
                    href="tel:+919066626662">
                    <svg fill="none" height="18" viewbox="0 0 24 24" width="18">
                        <path
                            d="M22 16.92v2a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.13 1h2A2 2 0 0 1 8 2.72 13 13 0 0 0 9.46 6a2 2 0 0 1-.45 2.11L8 9.14a16 16 0 0 0 6 6l1-1a2 2 0 0 1 2.11-.45 13 13 0 0 0 3.31 1.51A2 2 0 0 1 22 16.92Z"
                            stroke="currentColor" stroke-linecap="round" stroke-width="2">
                        </path>
                    </svg>
                    Call
                </a>
                <button
                    class="flex items-center justify-center gap-2 py-4 bg-brand text-white font-medium active:bg-brand-dark"
                    id="stickyEnquire">
                    Enquire Now
                </button>
            </div>
        </div>
    </div>
    <!-- Lightbox Modal - Mobile Optimized -->
    <div aria-labelledby="lightboxLabel" aria-modal="true"
        class="hidden fixed inset-0 z-50 lightbox-backdrop items-center justify-center p-3 sm:p-4" id="lightbox"
        role="dialog">
        <div class="absolute inset-0" data-close="">
        </div>
        <div class="relative max-w-5xl w-full mx-auto">
            <div class="bg-white rounded-lg sm:rounded-lgx shadow-soft overflow-hidden">
                <div class="flex items-center justify-between p-3 border-b border-ink-100">
                    <div class="text-sm font-medium truncate pr-2" id="lightboxLabel">
                        Preview
                    </div>
                    <button aria-label="Close preview"
                        class="p-2 rounded-lg hover:bg-ink-50 transition-colors focus-ring flex-shrink-0" data-close="">
                        <svg fill="none" height="20" viewbox="0 0 24 24" width="20">
                            <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-linecap="round"
                                stroke-width="2">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="relative bg-ink-100">
                    <img alt="" class="w-full max-h-[70vh] sm:max-h-[80vh] object-contain" id="lightboxImg" src="" />
                    <!-- Navigation buttons - Mobile Optimized -->
                    <button aria-label="Previous image"
                        class="absolute left-2 top-1/2 -translate-y-1/2 p-2 sm:p-3 rounded-full bg-white/90 hover:bg-white shadow-soft focus-ring"
                        id="prevBtn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </button>
                    <button aria-label="Next image"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 sm:p-3 rounded-full bg-white/90 hover:bg-white shadow-soft focus-ring"
                        id="nextBtn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewbox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: Floor Plan Enquiry Modal -->
    <div aria-labelledby="floorPlanModalLabel" aria-modal="true"
        class="hidden fixed inset-0 z-50 lightbox-backdrop items-center justify-center p-3 sm:p-4" id="floorPlanModal"
        role="dialog">
        <div class="absolute inset-0" data-floor-close=""></div>
        <div class="relative max-w-md w-full mx-auto">
            <div class="bg-white rounded-lg sm:rounded-lgx shadow-soft overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-ink-100">
                    <div>
                        <h3 class="text-lg font-medium" id="floorPlanModalLabel">Get Floor Plan Details</h3>
                        <p class="text-xs text-ink-600 mt-1" id="floorPlanUnit">Unit Details</p>
                    </div>
                    <button aria-label="Close modal"
                        class="p-2 rounded-lg hover:bg-ink-50 transition-colors focus-ring flex-shrink-0"
                        data-floor-close="">
                        <svg fill="none" height="20" viewbox="0 0 24 24" width="20">
                            <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-linecap="round"
                                stroke-width="2"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-5 sm:p-6">
                    <form class="space-y-4" id="floorPlanForm" novalidate="">
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1" for="fp-name">
                                Full Name *
                            </label>
                            <input autocomplete="name"
                                class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                id="fp-name" name="fp-name" placeholder="Your name" required="" type="text" />
                            <p class="mt-1 hidden text-xs text-red-600" data-error-for="fp-name">
                                Please enter your name.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1" for="fp-phone">
                                Phone Number *
                            </label>
                            <input
                                class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                id="fp-phone" inputmode="tel" name="fp-phone" placeholder="+91 9XXXXXXXXX" required=""
                                type="tel" />
                            <p class="mt-1 hidden text-xs text-red-600" data-error-for="fp-phone">
                                Enter a valid phone number.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1" for="fp-email">
                                Email Address
                            </label>
                            <input autocomplete="email"
                                class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                id="fp-email" name="fp-email" placeholder="name@email.com" type="email" />
                            <p class="mt-1 hidden text-xs text-red-600" data-error-for="fp-email">
                                Enter a valid email.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1" for="fp-action">
                                I want to
                            </label>
                            <select
                                class="w-full rounded-lg border border-ink-200 px-3 py-2.5 focus:border-brand focus:ring-2 focus:ring-brand/30 transition-colors"
                                id="fp-action" name="fp-action">
                                <option value="view">View Floor Plan</option>
                                <option value="download">Download Floor Plan</option>
                                <option value="both">View & Download</option>
                                <option value="details">Get More Details</option>
                            </select>
                        </div>
                        <input type="hidden" id="fp-unit-info" name="fp-unit-info" value="">
                        <div class="flex items-start gap-2 text-xs text-ink-600">
                            <input checked="" class="mt-1 rounded border-ink-300 text-brand focus:ring-brand"
                                id="fp-consent" type="checkbox" />
                            <label for="fp-consent">
                                I agree to be contacted for this enquiry.
                            </label>
                        </div>
                        <button
                            class="w-full inline-flex justify-center items-center px-5 py-3 rounded-lg bg-brand text-white hover:bg-brand-dark transition-colors focus-ring"
                            type="submit">
                            Submit Request
                        </button>
                        <p aria-live="polite" class="text-sm text-center" id="fpFormStatus" role="status"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Updated JavaScript with AJAX form handling -->
    <script>
        // Utilities
        const qs = (s, el = document) => el.querySelector(s);
        const qsa = (s, el = document) => Array.from(el.querySelectorAll(s));

        // Year
        qs('#year').textContent = new Date().getFullYear();

        // Enhanced Mobile Menu
        const menuToggle = qs('#menuToggle');
        const menuClose = qs('#menuClose');
        const mobileMenu = qs('#mobileMenu');
        const menuBackdrop = qs('#menuBackdrop');

        function openMenu() {
            menuToggle?.setAttribute('aria-expanded', 'true');
            mobileMenu?.classList.add('open');
            menuBackdrop?.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeMenu() {
            menuToggle?.setAttribute('aria-expanded', 'false');
            mobileMenu?.classList.remove('open');
            menuBackdrop?.classList.remove('open');
            document.body.style.overflow = '';
        }

        menuToggle?.addEventListener('click', openMenu);
        menuClose?.addEventListener('click', closeMenu);
        menuBackdrop?.addEventListener('click', closeMenu);

        // Close menu on link click
        qsa('#mobileMenu a').forEach(a => a.addEventListener('click', () => {
            closeMenu();
        }));

        // Smooth scroll for anchor links
        qsa('a[href^="#"]').forEach(link => {
            link.addEventListener('click', (e) => {
                const id = link.getAttribute('href');
                if (id.length > 1 && qs(id)) {
                    e.preventDefault();
                    const target = qs(id);
                    const headerHeight = qs('header').offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 10;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            })
        });

        // Sticky enquire button
        qs('#stickyEnquire')?.addEventListener('click', () => {
            qs('#lead')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        // Reveal on scroll - Only for desktop
        if (window.innerWidth >= 768) {
            const revealEls = qsa('[data-reveal]');
            const io = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            revealEls.forEach(el => io.observe(el));
        }

        // Filters for units
        const filterBtns = qsa('.filter-btn');
        const unitCards = qsa('.unit-card');
        function setFilter(type) {
            unitCards.forEach(card => {
                card.classList.toggle('hidden', !(type === 'all' || card.dataset.bhk === type));
            });
            filterBtns.forEach(btn => {
                btn.classList.toggle('bg-white', btn.dataset.filter === type);
                btn.classList.toggle('bg-ink-50', btn.dataset.filter !== type);
            });
        }
        filterBtns.forEach(btn => btn.addEventListener('click', () => setFilter(btn.dataset.filter)));
        setFilter('all');

        // Enhanced Lightbox with touch support
        const lightbox = qs('#lightbox');
        const lightboxImg = qs('#lightboxImg');
        const galleryItems = qsa('.gallery-item');
        let galleryOrder = [];
        let currentIndex = 0;

        function openLightbox(index) {
            document.documentElement.classList.add('lightbox-open');
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            currentIndex = index;
            const { img, alt } = galleryOrder[currentIndex];
            lightboxImg.src = img;
            lightboxImg.alt = alt || 'Preview';
            qs('#lightboxLabel').textContent = alt || 'Preview';
            qs('#prevBtn').disabled = currentIndex === 0;
            qs('#nextBtn').disabled = currentIndex === galleryOrder.length - 1;
        }

        function closeLightbox() {
            lightbox.classList.add('hidden');
            lightbox.classList.remove('flex');
            document.documentElement.classList.remove('lightbox-open');
            lightboxImg.src = '';
            lightboxImg.alt = '';
        }

        function buildOrderFrom(trigger) {
            const parentSection = trigger.closest('section') || document;
            const items = qsa('.gallery-item', parentSection);
            galleryOrder = items.map(el => ({ img: el.dataset.img, alt: el.dataset.alt, el }));
            return items.indexOf(trigger);
        }

        galleryItems.forEach(item => {
            item.addEventListener('click', () => {
                const idxInSection = buildOrderFrom(item);
                openLightbox(idxInSection >= 0 ? idxInSection : 0);
            });
        });

        // Navigation
        qs('#prevBtn')?.addEventListener('click', () => {
            if (currentIndex > 0) openLightbox(currentIndex - 1);
        });
        qs('#nextBtn')?.addEventListener('click', () => {
            if (currentIndex < galleryOrder.length - 1) openLightbox(currentIndex + 1);
        });

        // Close
        qsa('[data-close]', lightbox).forEach(el => el.addEventListener('click', closeLightbox));
        document.addEventListener('keydown', (e) => {
            if (lightbox.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') qs('#nextBtn')?.click();
            if (e.key === 'ArrowLeft') qs('#prevBtn')?.click();
        });

        // Enhanced Touch swipe for mobile
        let touchStartX = null;
        let touchStartY = null;

        lightbox?.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }, { passive: true });

        lightbox?.addEventListener('touchend', (e) => {
            if (touchStartX == null || touchStartY == null) return;

            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const dx = touchEndX - touchStartX;
            const dy = touchEndY - touchStartY;

            // Only trigger if horizontal swipe is stronger than vertical
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
                if (dx < 0 && currentIndex < galleryOrder.length - 1) qs('#nextBtn')?.click();
                if (dx > 0 && currentIndex > 0) qs('#prevBtn')?.click();
            }

            touchStartX = null;
            touchStartY = null;
        });

        // Form validation
        function validateEmail(v) {
            return !v || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
        }

        function validatePhone(v) {
            return /^\+?\d[\d\s-]{8,}$/.test(v);
        }

        // AJAX form submission
        async function submitFormData(formData) {
            try {
                const response = await fetch('<?php echo $_SERVER["PHP_SELF"]; ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error:', error);
                return { success: false, message: 'Network error. Please try again.' };
            }
        }

        // Lead form submission
        qs('#leadForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = qs('#name');
            const phone = qs('#phone');
            const email = qs('#email');
            const unitType = qs('#unitType');
            const visit = qs('#visit');
            const statusEl = qs('#formStatus');
            let valid = true;

            // Reset errors
            qsa('[data-error-for]').forEach(el => el.classList.add('hidden'));

            if (!name.value.trim()) {
                valid = false;
                qs('[data-error-for="name"]')?.classList.remove('hidden');
            }
            if (!validatePhone(phone.value.trim())) {
                valid = false;
                qs('[data-error-for="phone"]')?.classList.remove('hidden');
            }
            if (email.value && !validateEmail(email.value.trim())) {
                valid = false;
                qs('[data-error-for="email"]')?.classList.remove('hidden');
            }

            if (!valid) {
                statusEl.textContent = 'Please correct the highlighted fields.';
                statusEl.className = 'text-sm mt-2 text-red-600';
                return;
            }

            statusEl.textContent = 'Submitting...';
            statusEl.className = 'text-sm mt-2 text-ink-600';

            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('form_type', 'lead');
            formData.append('name', name.value);
            formData.append('phone', phone.value);
            formData.append('email', email.value);
            formData.append('unitType', unitType.value);
            formData.append('visit', visit.value);

            const result = await submitFormData(formData);

            if (result.success) {
                statusEl.textContent = result.message;
                statusEl.className = 'text-sm mt-2 text-green-700';
                e.target.reset();
                if (window.innerWidth < 768) {
                    showToast('Form submitted successfully!');
                }
            } else {
                statusEl.textContent = result.message;
                statusEl.className = 'text-sm mt-2 text-red-600';
            }
        });

        // Callback form submission
        qs('#callbackForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = qs('#cbname');
            const phone = qs('#cbphone');
            const status = qs('#cbStatus');

            if (!name.value.trim() || !validatePhone(phone.value.trim())) {
                status.textContent = 'Please enter valid details.';
                status.className = 'text-sm text-red-600';
                return;
            }

            status.textContent = 'Requesting callback...';
            status.className = 'text-sm text-ink-600';

            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('form_type', 'callback');
            formData.append('cbname', name.value);
            formData.append('cbphone', phone.value);

            const result = await submitFormData(formData);

            if (result.success) {
                status.textContent = result.message;
                status.className = 'text-sm text-green-700';
                e.target.reset();
                if (window.innerWidth < 768) {
                    showToast('Callback requested!');
                }
            } else {
                status.textContent = result.message;
                status.className = 'text-sm text-red-600';
            }
        });

        // Simple toast notification for mobile
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 left-4 right-4 bg-ink-900 text-white px-4 py-3 rounded-lg shadow-soft z-50 text-sm';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Floor Plan Modal functionality
        const floorPlanModal = qs('#floorPlanModal');
        const floorPlanUnit = qs('#floorPlanUnit');
        const fpUnitInfo = qs('#fp-unit-info');
        let currentFloorPlanData = null;

        function openFloorPlanModal(unitData) {
            currentFloorPlanData = unitData;
            floorPlanModal.classList.remove('hidden');
            floorPlanModal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Update modal with unit information
            floorPlanUnit.textContent = `${unitData.unit} - ${unitData.bhk} - ${unitData.area} - ${unitData.facing} facing`;
            fpUnitInfo.value = JSON.stringify(unitData);
        }

        function closeFloorPlanModal() {
            floorPlanModal.classList.add('hidden');
            floorPlanModal.classList.remove('flex');
            document.body.style.overflow = '';
            qs('#floorPlanForm').reset();
            qs('#fpFormStatus').textContent = '';
        }

        // Close modal handlers
        qsa('[data-floor-close]').forEach(el => el.addEventListener('click', closeFloorPlanModal));

        // Floor plan form submission
        qs('#floorPlanForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = qs('#fp-name');
            const phone = qs('#fp-phone');
            const email = qs('#fp-email');
            const action = qs('#fp-action');
            const status = qs('#fpFormStatus');
            let valid = true;

            // Reset errors
            qsa('[data-error-for]', qs('#floorPlanForm')).forEach(el => el.classList.add('hidden'));

            if (!name.value.trim()) {
                valid = false;
                qs('[data-error-for="fp-name"]')?.classList.remove('hidden');
            }
            if (!validatePhone(phone.value.trim())) {
                valid = false;
                qs('[data-error-for="fp-phone"]')?.classList.remove('hidden');
            }
            if (email.value && !validateEmail(email.value.trim())) {
                valid = false;
                qs('[data-error-for="fp-email"]')?.classList.remove('hidden');
            }

            if (!valid) {
                status.textContent = 'Please correct the highlighted fields.';
                status.className = 'text-sm text-center text-red-600';
                return;
            }

            status.textContent = 'Submitting...';
            status.className = 'text-sm text-center text-ink-600';

            const formData = new FormData();
            formData.append('ajax', '1');
            formData.append('form_type', 'floor_plan');
            formData.append('fp-name', name.value);
            formData.append('fp-phone', phone.value);
            formData.append('fp-email', email.value);
            formData.append('fp-action', action.value);
            formData.append('fp-unit-info', fpUnitInfo.value);

            const result = await submitFormData(formData);

            if (result.success) {
                status.textContent = result.message;
                status.className = 'text-sm text-center text-green-700';

                if (result.action === 'view' || result.action === 'both') {
                    setTimeout(() => {
                        closeFloorPlanModal();
                        if (currentFloorPlanData && currentFloorPlanData.img) {
                            window.open(currentFloorPlanData.img, '_blank');
                        }
                    }, 1500);
                } else {
                    setTimeout(closeFloorPlanModal, 2000);
                }

                if (window.innerWidth < 768) {
                    showToast('Request submitted successfully!');
                }
            } else {
                status.textContent = result.message;
                status.className = 'text-sm text-center text-red-600';
            }
        });

        // Update floor plan button click handlers
        document.addEventListener('DOMContentLoaded', () => {
            // Get all unit cards
            const unitCards = qsa('.unit-card');

            unitCards.forEach(card => {
                const viewBtn = card.querySelector('.view-plan');
                const downloadBtn = card.querySelector('.download-plan');

                // Get unit data from the card
                const unitName = card.querySelector('h3').textContent.trim();
                const bhk = card.querySelector('span').textContent.trim();
                const area = card.querySelectorAll('dd')[0].textContent.trim();
                const facing = card.querySelectorAll('dd')[1].textContent.trim();
                const imgUrl = viewBtn?.dataset.img || downloadBtn?.dataset.img || '';

                const unitData = {
                    unit: unitName,
                    bhk: bhk,
                    area: area,
                    facing: facing,
                    img: imgUrl
                };

                // Replace view button functionality
                if (viewBtn) {
                    viewBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        openFloorPlanModal(unitData);
                    });
                }

                // Replace download button functionality
                if (downloadBtn) {
                    downloadBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        openFloorPlanModal(unitData);
                    });
                }
            });
        });

        // Prevent zoom on input focus (iOS)
        document.addEventListener('gesturestart', e => e.preventDefault());
    </script>
</body>

</html>