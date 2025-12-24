<?php
require_once '../config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

<<<<<<< HEAD
=======
$db = Database::getInstance()->getConnection();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_info') {
        $firstName = sanitizeInput($_POST['first_name']);
        $lastName = sanitizeInput($_POST['last_name']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        
        if (empty($firstName) || empty($lastName) || empty($phone)) {
            $error = 'Vui lòng điền đầy đủ thông tin bắt buộc';
        } else {
<<<<<<< HEAD
            // Use backend API for profile update
            $response = makeApiRequest('/users/profile', 'PUT', [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'address' => $address
            ]);
            
            if ($response['success']) {
                $message = 'Cập nhật thông tin thành công!';
            } else {
                $error = $response['message'] ?? 'Có lỗi xảy ra khi cập nhật thông tin';
=======
            $stmt = $db->prepare("UPDATE Users SET FirstName = ?, LastName = ?, Phone = ?, Address = ? WHERE UserID = ?");
            if ($stmt->execute([$firstName, $lastName, $phone, $address, $_SESSION['user_id']])) {
                $message = 'Cập nhật thông tin thành công!';
            } else {
                $error = 'Có lỗi xảy ra khi cập nhật thông tin';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            }
        }
    } elseif ($_POST['action'] === 'change_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
<<<<<<< HEAD
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Vui lòng điền đầy đủ thông tin';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Mật khẩu mới không khớp';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } else {
            // Use backend API for password change
            $response = makeApiRequest('/users/change-password', 'PUT', [
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword
            ]);
            
            if ($response['success']) {
                $message = 'Đổi mật khẩu thành công!';
            } else {
                $error = $response['message'] ?? 'Có lỗi xảy ra khi đổi mật khẩu';
=======
        // Get current password from database
        $stmt = $db->prepare("SELECT Password FROM Users WHERE UserID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userPassword = $stmt->fetch();
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'Vui lòng điền đầy đủ thông tin';
        } elseif (!password_verify($currentPassword, $userPassword['Password'])) {
            $error = 'Mật khẩu hiện tại không đúng';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Mật khẩu mới và xác nhận mật khẩu không khớp';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Mật khẩu mới phải có ít nhất 6 ký tự';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
            if ($stmt->execute([$hashedPassword, $_SESSION['user_id']])) {
                $message = 'Đổi mật khẩu thành công!';
            } else {
                $error = 'Có lỗi xảy ra khi đổi mật khẩu';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
            }
        }
    }
}

<<<<<<< HEAD
// Get user info from API
$userResponse = makeApiRequest('/auth/me');
$user = $userResponse['success'] ? $userResponse['data'] : null;

// Get user orders from API
$ordersResponse = makeApiRequest('/orders?limit=5');
$orders = $ordersResponse['success'] ? $ordersResponse['data']['orders'] ?? [] : [];
=======
// Get user info
$stmt = $db->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user orders
$stmt = $db->prepare("
    SELECT * FROM Orders 
    WHERE UserID = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Get order items for each order
$orderDetails = [];
foreach ($orders as $order) {
    $stmt = $db->prepare("
        SELECT 
            od.*, 
            pd.Size, 
            pd.Color, 
            pd.Image as VariantImage,
            p.ProductName,
            p.Price,
            p.MainImage
        FROM OrderDetails od
        JOIN ProductDetail pd ON od.ProductDetailID = pd.ProductDetailID
        JOIN Product p ON pd.ProductID = p.ProductID
        WHERE od.OrderID = ?
    ");
    $stmt->execute([$order['OrderID']]);
    $orderDetails[$order['OrderID']] = $stmt->fetchAll();
}
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Tài khoản của tôi";
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Page Header -->
<section class="relative py-12 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 lg:px-8 relative z-10">
        <div class="flex items-center justify-between animate-fade-in">
            <div>
                <h1 class="text-4xl font-black text-white mb-2">
                    <i class="fas fa-user-circle mr-3"></i>Tài khoản của tôi
                </h1>
                <p class="text-gray-300">
                    Xin chào, <span class="text-sport-neon font-bold"><?php echo htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']); ?></span>
                </p>
            </div>
            <a href="logout.php" class="px-6 py-3 rounded-xl glass text-white hover:bg-red-500 transition-all duration-300">
                <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
            </a>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-12 bg-gray-50 dark:bg-sport-navy">
    <div class="container mx-auto px-4 lg:px-8">
        
        <?php if ($message): ?>
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500 rounded-2xl text-green-700 dark:text-green-400 animate-scale-in">
            <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-500/20 border border-red-500 rounded-2xl text-red-700 dark:text-red-400 animate-shake">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg space-y-2">
                    <a href="#info" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
                        <i class="fas fa-user w-5"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                    <a href="#orders" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy text-gray-700 dark:text-gray-300 transition-all duration-300">
                        <i class="fas fa-shopping-bag w-5"></i>
                        <span>Đơn hàng của tôi</span>
                    </a>
                    <a href="#password" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy text-gray-700 dark:text-gray-300 transition-all duration-300">
                        <i class="fas fa-lock w-5"></i>
                        <span>Đổi mật khẩu</span>
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg mt-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thống kê</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tổng đơn hàng:</span>
                            <span class="font-bold text-sport-neon"><?php echo count($orders); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Điểm thành viên:</span>
                            <span class="font-bold text-yellow-500">0 điểm</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Hạng thành viên:</span>
                            <span class="px-3 py-1 rounded-full bg-gradient-to-r from-sport-neon to-blue-600 text-white text-sm font-bold">
                                Bạc
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Info -->
                <div id="info" class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-user mr-3 text-sport-neon"></i>
                            Thông tin cá nhân
                        </h3>
                        <button onclick="toggleEditMode()" id="editBtn" class="px-4 py-2 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy transition-all duration-300">
                            <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                        </button>
                    </div>
                    
                    <!-- View Mode -->
                    <div id="viewMode" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Họ</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['FirstName']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Tên</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['LastName']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Email</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['Email']); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Số điện thoại</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['Phone']); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Địa chỉ</label>
                            <p class="text-lg font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user['Address']); ?></p>
                        </div>
                    </div>
                    
                    <!-- Edit Mode -->
                    <form method="POST" id="editMode" class="hidden">
                        <input type="hidden" name="action" value="update_info">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Họ <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['FirstName']); ?>" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['LastName']); ?>" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                                <input type="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-sport-navy/50 text-gray-900 dark:text-white cursor-not-allowed">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Email không thể thay đổi</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Số điện thoại <span class="text-red-500">*</span></label>
                                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['Phone']); ?>" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Địa chỉ</label>
                                <textarea name="address" rows="3"
                                          class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"><?php echo htmlspecialchars($user['Address']); ?></textarea>
                            </div>
                        </div>
                        <div class="flex gap-3 mt-6">
                            <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transition-all duration-300">
                                <i class="fas fa-save mr-2"></i>Lưu thay đổi
                            </button>
                            <button type="button" onclick="toggleEditMode()" class="px-6 py-3 rounded-xl glass hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold transition-all duration-300">
                                <i class="fas fa-times mr-2"></i>Hủy
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Change Password -->
                <div id="password" class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-lock mr-3 text-sport-neon"></i>
                        Đổi mật khẩu
                    </h3>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                                <input type="password" name="current_password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                       placeholder="Nhập mật khẩu hiện tại">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" name="new_password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                       placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon focus:ring-2 focus:ring-sport-neon/20 transition-all duration-300"
                                       placeholder="Nhập lại mật khẩu mới">
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-purple-500/50 transition-all duration-300">
                            <i class="fas fa-key mr-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>
                
                <!-- Orders -->
                <div id="orders" class="modern-card bg-white dark:bg-sport-blue rounded-2xl p-6 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-shopping-bag mr-3 text-sport-neon"></i>
                        Đơn hàng của tôi
                    </h3>
                    
                    <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-bag text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Bạn chưa có đơn hàng nào</p>
                        <a href="products.php" class="btn-neon inline-block px-6 py-3 relative z-10">
                            <i class="fas fa-shopping-cart mr-2"></i>Mua sắm ngay
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($orders as $order): ?>
                        <div class="p-4 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy transition-all duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">Đơn hàng #<?php echo $order['OrderID']; ?></p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </p>
                                </div>
                                <span class="px-4 py-2 rounded-full text-sm font-bold <?php 
                                    echo $order['Status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                        ($order['Status'] == 'delivered' ? 'bg-green-100 text-green-700' : 
                                        'bg-blue-100 text-blue-700'); 
                                ?>">
                                    <?php echo ucfirst($order['Status']); ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-2xl font-black text-sport-neon">
                                    <?php echo formatPrice($order['TotalAmount']); ?>
                                </p>
                                <button onclick="showOrderDetails(<?php echo $order['OrderID']; ?>)" class="px-4 py-2 rounded-xl glass hover:bg-sport-neon hover:text-white transition-all duration-300">
                                    Chi tiết
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Order Details Modal -->
<div id="orderModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-sport-blue rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden animate-scale-in">
        <div class="bg-gradient-to-r from-sport-neon to-blue-600 p-6 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-white flex items-center">
                <i class="fas fa-receipt mr-3"></i>
                <span id="modalOrderTitle">Chi tiết đơn hàng</span>
            </h3>
            <button onclick="closeOrderModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition-all duration-300">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-180px)]">
            <!-- Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 rounded-xl glass">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Mã đơn hàng</p>
                    <p class="font-bold text-gray-900 dark:text-white" id="modalOrderId"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Ngày đặt</p>
                    <p class="font-bold text-gray-900 dark:text-white" id="modalOrderDate"></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Trạng thái</p>
                    <span id="modalOrderStatus" class="px-4 py-2 rounded-full text-sm font-bold"></span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tổng tiền</p>
                    <p class="font-bold text-sport-neon text-xl" id="modalOrderTotal"></p>
                </div>
            </div>
            
            <!-- Shipping Info -->
            <div class="mb-6 p-4 rounded-xl glass">
                <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-truck mr-2 text-sport-neon"></i>
                    Thông tin giao hàng
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Người nhận</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="modalShippingName"></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Số điện thoại</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="modalShippingPhone"></p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Địa chỉ</p>
                        <p class="font-semibold text-gray-900 dark:text-white" id="modalShippingAddress"></p>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div>
                <h4 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class="fas fa-box mr-2 text-sport-neon"></i>
                    Sản phẩm
                </h4>
                <div id="modalOrderItems" class="space-y-3">
                    <!-- Items will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
        
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-sport-navy">
            <button onclick="closeOrderModal()" class="w-full py-3 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-sport-neon/50 transition-all duration-300">
                <i class="fas fa-check mr-2"></i>Đóng
            </button>
        </div>
    </div>
</div>

<script>
const orderDetailsData = <?php echo json_encode($orderDetails, JSON_UNESCAPED_UNICODE); ?>;
const ordersData = <?php echo json_encode(array_column($orders, null, 'OrderID'), JSON_UNESCAPED_UNICODE); ?>;

function showOrderDetails(orderId) {
    const order = ordersData[orderId];
    const items = orderDetailsData[orderId];
    
    if (!order || !items) return;
    
    // Set order info
    document.getElementById('modalOrderTitle').textContent = 'Chi tiết đơn hàng #' + orderId;
    document.getElementById('modalOrderId').textContent = '#' + orderId;
    document.getElementById('modalOrderDate').textContent = formatDate(order.created_at);
    document.getElementById('modalOrderTotal').textContent = formatPrice(order.TotalAmount);
    
    // Set status with color
    const statusEl = document.getElementById('modalOrderStatus');
    let statusClass = '';
    let statusText = '';
    
    switch(order.Status) {
        case 'pending':
            statusClass = 'bg-yellow-100 text-yellow-700';
            statusText = 'Chờ xử lý';
            break;
        case 'processing':
            statusClass = 'bg-blue-100 text-blue-700';
            statusText = 'Đang xử lý';
            break;
        case 'shipping':
            statusClass = 'bg-purple-100 text-purple-700';
            statusText = 'Đang giao';
            break;
        case 'delivered':
            statusClass = 'bg-green-100 text-green-700';
            statusText = 'Đã giao';
            break;
        case 'cancelled':
            statusClass = 'bg-red-100 text-red-700';
            statusText = 'Đã hủy';
            break;
        default:
            statusClass = 'bg-gray-100 text-gray-700';
            statusText = order.Status;
    }
    
    statusEl.className = 'px-4 py-2 rounded-full text-sm font-bold ' + statusClass;
    statusEl.textContent = statusText;
    
    // Set shipping info
    document.getElementById('modalShippingName').textContent = order.GuestName || '-';
    document.getElementById('modalShippingPhone').textContent = order.GuestPhone || '-';
    document.getElementById('modalShippingAddress').textContent = order.Address || '-';
    
    // Set order items
    const itemsContainer = document.getElementById('modalOrderItems');
    itemsContainer.innerHTML = '';
    
    items.forEach(item => {
        const itemEl = document.createElement('div');
        itemEl.className = 'flex items-center gap-4 p-4 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-navy transition-all duration-300';
        
        const imageSrc = item.VariantImage || item.MainImage || '/images/placeholder.jpg';
        
        itemEl.innerHTML = `
            <img src="${imageSrc}" alt="${item.ProductName}" class="w-20 h-20 object-cover rounded-lg">
            <div class="flex-1">
                <h5 class="font-bold text-gray-900 dark:text-white mb-1">${item.ProductName}</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Màu: <span class="font-semibold">${item.Color}</span> | 
                    Size: <span class="font-semibold">${item.Size}</span>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Số lượng: <span class="font-semibold">${item.Quantity}</span>
                </p>
            </div>
            <div class="text-right">
                <p class="font-bold text-sport-neon text-lg">${formatPrice(item.Price)}</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Thành tiền:</p>
                <p class="font-bold text-gray-900 dark:text-white">${formatPrice(item.Price * item.Quantity)}</p>
            </div>
        `;
        
        itemsContainer.appendChild(itemEl);
    });
    
    // Show modal
    document.getElementById('orderModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
    document.body.style.overflow = '';
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + ' ₫';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Close modal when clicking outside
document.getElementById('orderModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderModal();
    }
});

function toggleEditMode() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const editBtn = document.getElementById('editBtn');
    
    if (viewMode.classList.contains('hidden')) {
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
        editBtn.innerHTML = '<i class="fas fa-edit mr-2"></i>Chỉnh sửa';
    } else {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        editBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>Xem';
    }
}
</script>

<?php include '../includes/layout_footer.php'; ?>
