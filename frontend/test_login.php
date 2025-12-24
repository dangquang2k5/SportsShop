<?php
require_once 'config.php';

<<<<<<< HEAD
// Test login via API
=======
$db = Database::getInstance()->getConnection();

// Test password từ database
$stmt = $db->prepare("SELECT * FROM Users WHERE Phone = ?");
$stmt->execute(['0123456789']);
$user = $stmt->fetch();

>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
echo "=== TEST ĐĂNG NHẬP ADMIN ===\n\n";
echo "Phone: 0123456789\n";
echo "Password to test: admin123\n\n";

<<<<<<< HEAD
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
=======
if ($user) {
    echo "✓ User found in database\n";
    echo "UserID: " . $user['UserID'] . "\n";
    echo "Role: " . $user['Role'] . "\n";
    echo "Status: " . $user['Status'] . "\n";
    echo "Password Hash: " . substr($user['Password'], 0, 30) . "...\n\n";
    
    // Test password verify
    $testPassword = 'admin123';
    $isValid = password_verify($testPassword, $user['Password']);
    
    if ($isValid) {
        echo "✓✓✓ PASSWORD VERIFY: SUCCESS ✓✓✓\n";
        echo "Mật khẩu 'admin123' khớp với hash trong database!\n";
    } else {
        echo "✗✗✗ PASSWORD VERIFY: FAILED ✗✗✗\n";
        echo "Mật khẩu 'admin123' KHÔNG khớp với hash trong database!\n";
        echo "\nĐang tạo hash mới...\n";
        $newHash = password_hash($testPassword, PASSWORD_BCRYPT);
        echo "Hash mới: $newHash\n";
        echo "\nChạy lệnh sau để update:\n";
        echo "UPDATE Users SET Password = '$newHash' WHERE Phone = '0123456789';\n";
    }
} else {
    echo "✗ User NOT found in database\n";
}

echo "\n=== TEST USER ===\n\n";
$stmt->execute(['0987654321']);
$user2 = $stmt->fetch();
if ($user2) {
    echo "✓ User found: " . $user2['Phone'] . "\n";
    $isValid2 = password_verify('admin123', $user2['Password']);
    echo "Password verify: " . ($isValid2 ? "SUCCESS" : "FAILED") . "\n";
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
}
?>
