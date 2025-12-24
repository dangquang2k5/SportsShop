<?php
require_once 'config.php';

// Test login via API
echo "=== TEST ĐĂNG NHẬP ADMIN ===\n\n";
echo "Phone: 0123456789\n";
echo "Password to test: admin123\n\n";

// Use backend API to test login
$loginData = [
    'phone' => '0123456789',
    'password' => 'admin123'
];

$response = makeApiRequest('/auth/login', 'POST', $loginData);

if ($response['success']) {
    echo "✓✓✓ LOGIN API: SUCCESS ✓✓✓\n";
    echo "Đăng nhập thành công qua API!\n";
    echo "User data: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "✗✗✗ LOGIN API: FAILED ✗✗✗\n";
    echo "Lỗi: " . ($response['message'] ?? 'Không thể đăng nhập') . "\n";
}

echo "\n=== TEST USER ===\n\n";

// Test another user via API
$loginData2 = [
    'phone' => '0987654321',
    'password' => 'admin123'
];

$response2 = makeApiRequest('/auth/login', 'POST', $loginData2);

if ($response2['success']) {
    echo "✓✓✓ USER LOGIN API: SUCCESS ✓✓✓\n";
    echo "Đăng nhập user thành công qua API!\n";
} else {
    echo "✗✗✗ USER LOGIN API: FAILED ✗✗✗\n";
    echo "Lỗi: " . ($response2['message'] ?? 'Không thể đăng nhập user') . "\n";
}
?>
