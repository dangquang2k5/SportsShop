<?php
// Site Configuration
define('SITE_NAME', 'SportShop - Cửa hàng thể thao trực tuyến');
define('SITE_URL', 'http://localhost');
define('BACKEND_API_URL', getenv('BACKEND_URL') ?: 'http://localhost:3000/api');

// API Helper Functions
function makeApiRequest($endpoint, $method = 'GET', $data = null) {
    $url = BACKEND_API_URL . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => 'Content-Type: application/json',
            'ignore_errors' => true
        ]
    ];
    
    if ($data && $method !== 'GET') {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        return ['success' => false, 'message' => 'API request failed'];
    }
    
    $decoded = json_decode($response, true);
    return $decoded ?: ['success' => false, 'message' => 'Invalid JSON response'];
}

function getFeaturedProducts($limit = 8) {
    $response = makeApiRequest('/products?limit=' . $limit . '&sortBy=RatingAvg&sortOrder=DESC');
    
    if ($response['success'] && isset($response['data']['products'])) {
        return $response['data']['products'];
    }
    
    return [];
}

function getAllCategories() {
    $response = makeApiRequest('/categories');
    
    if ($response['success'] && isset($response['data'])) {
        return $response['data'];
    }
    
    return [];
}

function getAllBrands() {
    $response = makeApiRequest('/brands');
    
    if ($response['success'] && isset($response['data'])) {
        return $response['data'];
    }
    
    return [];
}

// Legacy compatibility functions
function isLoggedIn() {
    return false; // Always return false, let JS handle auth
}

function isAdmin() {
    return false; // Always return false, let JS handle auth
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.');
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>