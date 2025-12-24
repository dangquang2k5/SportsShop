<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

<<<<<<< HEAD
$message = '';
=======
$db = Database::getInstance()->getConnection();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

// Handle brand actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
<<<<<<< HEAD
=======
    // SỬA 1: Dùng tên cột PascalCase
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    if ($action === 'add') {
        $brandName = trim($_POST['BrandName']);
        $description = trim($_POST['BrandDescription']);
        
<<<<<<< HEAD
        // Use backend API to add brand
        $response = makeApiRequest('/brands', 'POST', [
            'brandName' => $brandName,
            'brandDescription' => $description
        ]);
        
        if ($response['success']) {
            $message = 'Đã thêm thương hiệu mới';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể thêm thương hiệu');
        }

    } elseif ($action === 'edit') {
        $brandId = (int)$_POST['BrandID'];
        $brandName = trim($_POST['BrandName']);
        $description = trim($_POST['BrandDescription']);
        
        // Use backend API to update brand
        $response = makeApiRequest('/brands/' . $brandId, 'PUT', [
            'brandName' => $brandName,
            'brandDescription' => $description
        ]);
        
        if ($response['success']) {
            $message = 'Đã cập nhật thương hiệu';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể cập nhật thương hiệu');
        }

    } elseif ($action === 'delete') {
        $brandId = (int)$_POST['BrandID'];
        
        // Use backend API to delete brand
        $response = makeApiRequest('/brands/' . $brandId, 'DELETE');
        
        if ($response['success']) {
            $message = 'Đã xóa thương hiệu';
        } else {
            $message = 'Lỗi: ' . ($response['message'] ?? 'Không thể xóa thương hiệu');
        }
=======
        // SỬA 2: Sửa tên Bảng và Cột. Xóa 'country_origin'.
        $stmt = $db->prepare("INSERT INTO Brand (BrandName, BrandDescription) VALUES (?, ?)");
        $stmt->execute([$brandName, $description]);
        $message = 'Đã thêm thương hiệu mới';

    } elseif ($action === 'edit') {
        $brandId = (int)$_POST['BrandID']; // Sửa 'brand_id'
        $brandName = trim($_POST['BrandName']);
        $description = trim($_POST['BrandDescription']);
        
        // SỬA 3: Sửa tên Bảng và Cột. Xóa 'country_origin'.
        $stmt = $db->prepare("UPDATE Brand SET BrandName = ?, BrandDescription = ? WHERE BrandID = ?");
        $stmt->execute([$brandName, $description, $brandId]);
        $message = 'Đã cập nhật thương hiệu';

    } elseif ($action === 'delete') {
        $brandId = (int)$_POST['BrandID']; // Sửa 'brand_id'
        
        // SỬA 4: Sửa tên Bảng và Cột.
        $stmt = $db->prepare("DELETE FROM Brand WHERE BrandID = ?");
        $stmt->execute([$brandId]);
        $message = 'Đã xóa thương hiệu';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
    }
    
    header('Location: admin_brands.php?message=' . urlencode($message));
    exit;
}

<<<<<<< HEAD
// Get brands from API
$brandsResponse = makeApiRequest('/brands');
$brands = $brandsResponse['success'] ? $brandsResponse['data'] ?? [] : [];

// Set default statistics since we don't have brands count endpoint
$totalBrands = count($brands);
=======
// SỬA 5: Sửa tên Bảng và Cột.
$stmt = $db->query("SELECT * FROM Brand ORDER BY BrandName");
$brands = $stmt->fetchAll();

// SỬA 6: Sửa tên Bảng.
$stmt = $db->query("SELECT COUNT(*) as total FROM Brand");
$totalBrands = $stmt->fetch()['total'];
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Quản lý thương hiệu";
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
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
            <i class="fas fa-tag w-5"></i><span>Thương hiệu</span>
        </a>
        <a href="admin_coupons.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
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
                        <i class="fas fa-tag mr-3"></i>Quản lý thương hiệu
                    </h1>
                    <p class="text-gray-300">Quản lý các thương hiệu sản phẩm</p>
                </div>
                <button onclick="openAddModal()" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-plus mr-2"></i>Thêm thương hiệu
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
            
            <div class="modern-card bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <p class="text-blue-700 dark:text-blue-300 font-semibold">
                        Tổng số thương hiệu: <span class="text-sport-neon"><?php echo $totalBrands; ?></span>
                    </p>
                </div>
            </div>
            
            <!-- Brands Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">ID</th>
                                <th class="px-6 py-4 text-left font-bold">Tên thương hiệu</th>
                                <th class="px-6 py-4 text-left font-bold">Mô tả</th>
                                <th class="px-6 py-4 text-left font-bold">Ngày tạo</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($brands as $index => $brand): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sport-neon">#<?php echo $brand['BrandID']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($brand['BrandName']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars(substr($brand['BrandDescription'] ?? '', 0, 80)) . (strlen($brand['BrandDescription'] ?? '') > 80 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo date('d/m/Y', strtotime($brand['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editBrand(<?php echo htmlspecialchars(json_encode($brand)); ?>)"
                                                class="p-2 rounded-lg glass hover:bg-yellow-500 hover:text-white transition-all duration-300"
                                                title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="BrandID" value="<?php echo $brand['BrandID']; ?>">
                                            <button type="submit" 
                                                    class="p-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                    onclick="return confirm('Xóa thương hiệu này?')"
                                                    title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeAddModal()">
    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl max-w-lg w-full animate-scale-in">
        <form method="POST">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Thêm thương hiệu mới</h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên thương hiệu *</label>
                    <input type="text" name="BrandName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                    <textarea name="BrandDescription" rows="4"
                              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all"></textarea>
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
    <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl max-w-lg w-full animate-scale-in">
        <form method="POST">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Sửa thương hiệu</h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="BrandID" id="edit_BrandID">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên thương hiệu *</label>
                    <input type="text" name="BrandName" id="edit_BrandName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                    <textarea name="BrandDescription" id="edit_BrandDescription" rows="4"
                              class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all"></textarea>
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

function editBrand(brand) {
    document.getElementById('edit_BrandID').value = brand.BrandID;
    document.getElementById('edit_BrandName').value = brand.BrandName;
    document.getElementById('edit_BrandDescription').value = brand.BrandDescription || '';
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include '../includes/layout_footer.php'; ?>