<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

$message = '';

// Handle coupon actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $voucherCode = strtoupper(trim($_POST['VoucherCode']));
        $discountValue = (float)$_POST['DiscountValue'];
        $minOrderValue = (float)$_POST['MinOrderValue'];
        $startDate = $_POST['StartDate'];
        $endDate = $_POST['EndDate'];
        $quantity = (int)$_POST['Quantity'];
        
        // Use backend API to add voucher
        $response = makeApiRequest('/vouchers', 'POST', [
            'voucherCode' => $voucherCode,
            'discountValue' => $discountValue,
            'minOrderValue' => $minOrderValue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'quantity' => $quantity
        ]);
        
        if ($response['success']) {
            $message = 'Đã thêm mã giảm giá mới';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể thêm mã giảm giá');
        }

    } elseif ($action === 'edit') {
        $voucherId = (int)$_POST['VoucherID'];
        $voucherCode = strtoupper(trim($_POST['VoucherCode']));
        $discountValue = (float)$_POST['DiscountValue'];
        $minOrderValue = (float)$_POST['MinOrderValue'];
        $startDate = $_POST['StartDate'];
        $endDate = $_POST['EndDate'];
        $quantity = (int)$_POST['Quantity'];
        
        // Use backend API to update voucher
        $response = makeApiRequest('/vouchers/' . $voucherId, 'PUT', [
            'voucherCode' => $voucherCode,
            'discountValue' => $discountValue,
            'minOrderValue' => $minOrderValue,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'quantity' => $quantity
        ]);
        
        if ($response['success']) {
            $message = 'Đã cập nhật mã giảm giá';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể cập nhật mã giảm giá');
        }

    } elseif ($action === 'delete') {
        $voucherId = (int)$_POST['VoucherID'];
        
        // Use backend API to delete voucher
        $response = makeApiRequest('/vouchers/' . $voucherId, 'DELETE');
        
        if ($response['success']) {
            $message = 'Đã xóa mã giảm giá';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể xóa mã giảm giá');
        }
    }
    
    header('Location: admin_coupons.php?message=' . urlencode($message));
    exit;
}

// Get vouchers from API
$couponsResponse = makeApiRequest('/vouchers');
$coupons = $couponsResponse['success'] ? $couponsResponse['data'] ?? [] : [];

// Set default statistics since we don't have count endpoints
$totalCoupons = count($coupons);
$activeCoupons = 0;
$expiredCoupons = 0;

foreach ($coupons as $coupon) {
    $endDate = $coupon['EndDate'] ?? '';
    if ($endDate && strtotime($endDate) >= time()) {
        $activeCoupons++;
    } else {
        $expiredCoupons++;
    }
}

$pageTitle = "Quản lý mã giảm giá";
$isInPages = true;
include '../includes/layout_header.php';
?>

<!-- Admin Sidebar -->
<div class="fixed top-20 left-0 h-screen w-64 bg-white dark:bg-sport-navy border-r border-gray-200 dark:border-gray-800 z-30 transition-transform duration-300" id="admin-sidebar">
    <div class="p-6 space-y-2">
        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
            <i class="fas fa-shield-alt mr-3 text-sport-neon"></i>
            Quản trị viên
        </h3>
        
        <a href="admin_dashboard.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-chart-line w-5"></i><span>Dashboard</span>
        </a>
        <a href="admin_products.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-box w-5"></i><span>Sản phẩm</span>
        </a>
        <a href="admin_orders.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-shopping-cart w-5"></i><span>Đơn hàng</span>
        </a>
        <a href="admin_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-users w-5"></i><span>Người dùng</span>
        </a>
        <a href="admin_categories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-th-large w-5"></i><span>Danh mục</span>
        </a>
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-tag w-5"></i><span>Thương hiệu</span>
        </a>
        <a href="admin_coupons.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
            <i class="fas fa-ticket-alt w-5"></i><span>Mã giảm giá</span>
        </a>
        <a href="admin_reviews.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
            <i class="fas fa-star w-5"></i><span>Đánh giá</span>
        </a>
        
        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
            <a href="../index.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
                <i class="fas fa-home w-5"></i><span>Về trang chủ</span>
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="ml-0 lg:ml-64 transition-all duration-300">
    <!-- Page Header -->
    <section class="relative py-12 bg-gradient-to-br from-sport-navy to-sport-blue overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-sport-neon opacity-10 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 lg:px-8 relative z-10">
            <div class="flex items-center justify-between">
                <div class="animate-fade-in">
                    <h1 class="text-4xl font-black text-white mb-2">
                        <i class="fas fa-ticket-alt mr-3"></i>Quản lý mã giảm giá
                    </h1>
                    <p class="text-gray-300">Tạo và quản lý voucher giảm giá</p>
                </div>
                <button onclick="openAddModal()" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-plus mr-2"></i>Thêm mã giảm giá
                </button>
            </div>
        </div>
    </section>

    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <?php if (isset($_GET['message'])): ?>
                <div class="modern-card bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-700 dark:text-green-300 font-semibold"><?php echo htmlspecialchars($_GET['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="modern-card bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-blue-100 mb-1">Tổng mã giảm giá</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($totalCoupons); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="modern-card bg-gradient-to-br from-green-500 to-emerald-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-green-100 mb-1">Còn hạn</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($activeCoupons); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="modern-card bg-gradient-to-br from-gray-500 to-gray-700 rounded-2xl p-6 text-white shadow-lg animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-gray-100 mb-1">Đã hết hạn</p>
                            <h3 class="text-3xl font-black"><?php echo number_format($expiredCoupons); ?></h3>
                        </div>
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <i class="fas fa-ban text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coupons Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Danh sách mã giảm giá (<?php echo count($coupons); ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">ID</th>
                                <th class="px-6 py-4 text-left font-bold">Mã</th>
                                <th class="px-6 py-4 text-left font-bold">Giá trị giảm</th>
                                <th class="px-6 py-4 text-left font-bold">Đơn tối thiểu</th>
                                <th class="px-6 py-4 text-left font-bold">Số lượng</th>
                                <th class="px-6 py-4 text-left font-bold">Thời gian</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($coupons)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-inbox text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                        <p class="text-gray-500 dark:text-gray-400">Không có mã giảm giá nào</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($coupons as $index => $coupon): ?>
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-sport-neon">#<?php echo $coupon['VoucherID']; ?></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-gradient-to-r from-sport-neon to-blue-600 text-white font-mono font-bold rounded-lg">
                                            <?php echo htmlspecialchars($coupon['VoucherCode']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-sport-neon"><?php echo formatPrice($coupon['DiscountValue']); ?> ₫</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo formatPrice($coupon['MinOrderValue']); ?> ₫
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <?php echo number_format($coupon['Quantity']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600 dark:text-gray-400">
                                        <div><?php echo date('d/m/Y', strtotime($coupon['StartDate'])); ?></div>
                                        <div class="text-gray-400">đến</div>
                                        <div><?php echo date('d/m/Y', strtotime($coupon['EndDate'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <button onclick="editCoupon(<?php echo htmlspecialchars(json_encode($coupon)); ?>)"
                                                    class="p-2 rounded-lg glass hover:bg-yellow-500 hover:text-white transition-all duration-300"
                                                    title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="VoucherID" value="<?php echo $coupon['VoucherID']; ?>">
                                                <button type="submit" 
                                                        class="p-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                        onclick="return confirm('Xóa mã giảm giá này?')"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAddModal()">
    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl max-w-2xl w-full animate-scale-in">
        <form method="POST">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Thêm mã giảm giá mới</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mã giảm giá *</label>
                    <input type="text" name="VoucherCode" required style="text-transform: uppercase;"
                           placeholder="VD: SALE50K"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Giá trị giảm (VNĐ) *</label>
                    <input type="number" name="DiscountValue" min="0" step="1000" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Đơn tối thiểu (VNĐ) *</label>
                    <input type="number" name="MinOrderValue" min="0" step="1000" value="0" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Số lượng *</label>
                    <input type="number" name="Quantity" min="0" step="1" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngày bắt đầu *</label>
                    <input type="date" name="StartDate" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngày kết thúc *</label>
                    <input type="date" name="EndDate" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeAddModal()" 
                        class="px-6 py-3 rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    Hủy
                </button>
                <button type="submit" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-plus mr-2"></i>Thêm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeEditModal()">
    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl max-w-2xl w-full animate-scale-in">
        <form method="POST">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Sửa mã giảm giá</h3>
            </div>
            <div class="p-6 grid grid-cols-2 gap-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="VoucherID" id="edit_VoucherID">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mã giảm giá *</label>
                    <input type="text" name="VoucherCode" id="edit_VoucherCode" required style="text-transform: uppercase;"
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Giá trị giảm (VNĐ) *</label>
                    <input type="number" name="DiscountValue" id="edit_DiscountValue" min="0" step="1000" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Đơn tối thiểu (VNĐ) *</label>
                    <input type="number" name="MinOrderValue" id="edit_MinOrderValue" min="0" step="1000" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Số lượng *</label>
                    <input type="number" name="Quantity" id="edit_Quantity" min="0" step="1" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngày bắt đầu *</label>
                    <input type="date" name="StartDate" id="edit_StartDate" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Ngày kết thúc *</label>
                    <input type="date" name="EndDate" id="edit_EndDate" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" 
                        class="px-6 py-3 rounded-xl glass text-gray-700 dark:text-gray-300 font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    Hủy
                </button>
                <button type="submit" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function editCoupon(coupon) {
    document.getElementById('edit_VoucherID').value = coupon.VoucherID;
    document.getElementById('edit_VoucherCode').value = coupon.VoucherCode;
    document.getElementById('edit_DiscountValue').value = coupon.DiscountValue;
    document.getElementById('edit_MinOrderValue').value = coupon.MinOrderValue;
    document.getElementById('edit_Quantity').value = coupon.Quantity;
    document.getElementById('edit_StartDate').value = coupon.StartDate;
    document.getElementById('edit_EndDate').value = coupon.EndDate;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include '../includes/layout_footer.php'; ?>
