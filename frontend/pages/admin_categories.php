<?php
require_once '../config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

<<<<<<< HEAD
=======
$db = Database::getInstance()->getConnection();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
$message = '';
$messageType = 'success';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'add') {
            $categoryName = trim($_POST['CategoryName']);
            $description = trim($_POST['CategoryDescription']);
            
            if (empty($categoryName)) {
                throw new Exception('Tên danh mục không được để trống');
            }
            
<<<<<<< HEAD
            // Use backend API to add category
            $response = makeApiRequest('/categories', 'POST', [
                'categoryName' => $categoryName,
                'categoryDescription' => $description
            ]);
            
            if ($response['success']) {
                $message = 'Thêm danh mục thành công!';
            } else {
                throw new Exception($response['message'] ?? 'Không thể thêm danh mục');
            }
=======
            $stmt = $db->prepare("INSERT INTO Categories (CategoryName, CategoryDescription) VALUES (?, ?)");
            $stmt->execute([$categoryName, $description]);
            $message = 'Thêm danh mục thành công!';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

        } elseif ($action === 'edit') {
            $categoryId = (int)$_POST['CategoryID'];
            $categoryName = trim($_POST['CategoryName']);
            $description = trim($_POST['CategoryDescription']);
            
            if (empty($categoryName)) {
                throw new Exception('Tên danh mục không được để trống');
            }
            
<<<<<<< HEAD
            // Use backend API to update category
            $response = makeApiRequest('/categories/' . $categoryId, 'PUT', [
                'categoryName' => $categoryName,
                'categoryDescription' => $description
            ]);
            
            if ($response['success']) {
                $message = 'Cập nhật danh mục thành công!';
            } else {
                throw new Exception($response['message'] ?? 'Không thể cập nhật danh mục');
            }
=======
            $stmt = $db->prepare("UPDATE Categories SET CategoryName = ?, CategoryDescription = ? WHERE CategoryID = ?");
            $stmt->execute([$categoryName, $description, $categoryId]);
            $message = 'Cập nhật danh mục thành công!';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

        } elseif ($action === 'delete') {
            $categoryId = (int)$_POST['CategoryID'];
            
<<<<<<< HEAD
            // Use backend API to delete category
            $response = makeApiRequest('/categories/' . $categoryId, 'DELETE');
            
            if ($response['success']) {
                $message = 'Xóa danh mục thành công!';
            } else {
                throw new Exception($response['message'] ?? 'Không thể xóa danh mục');
            }
=======
            // Check if category has products
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM Product WHERE CategoryID = ?");
            $stmt->execute([$categoryId]);
            $count = $stmt->fetch()['count'];
            
            if ($count > 0) {
                throw new Exception('Không thể xóa danh mục đang có sản phẩm!');
            }
            
            $stmt = $db->prepare("DELETE FROM Categories WHERE CategoryID = ?");
            $stmt->execute([$categoryId]);
            $message = 'Xóa danh mục thành công!';
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345
        }
        
        // Redirect to avoid form resubmission
        if ($message) {
            header('Location: admin_categories.php?message=' . urlencode($message) . '&type=' . $messageType);
            exit;
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = 'error';
    }
}

<<<<<<< HEAD
// Get categories from API
$categoriesResponse = makeApiRequest('/categories');
$categories = $categoriesResponse['success'] ? $categoriesResponse['data'] ?? [] : [];
=======
// Get all categories
$stmt = $db->query("SELECT c.*, COUNT(p.ProductID) as ProductCount 
                    FROM Categories c 
                    LEFT JOIN Product p ON c.CategoryID = p.CategoryID 
                    GROUP BY c.CategoryID 
                    ORDER BY c.CategoryName");
$categories = $stmt->fetchAll();
>>>>>>> 3d6d58ed3875cc3c551e3fe1991339ab7637c345

$pageTitle = "Quản lý Danh mục";
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
        <a href="admin_categories.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl bg-gradient-to-r from-sport-neon to-blue-600 text-white font-semibold">
            <i class="fas fa-th-large w-5"></i><span>Danh mục</span>
        </a>
        <a href="admin_brands.php" class="flex items-center space-x-3 px-4 py-3 rounded-xl glass hover:bg-gray-100 dark:hover:bg-sport-blue text-gray-700 dark:text-gray-300 transition-all duration-300">
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
                        <i class="fas fa-th-large mr-3"></i>Quản lý danh mục
                    </h1>
                    <p class="text-gray-300">Quản lý các danh mục sản phẩm</p>
                </div>
                <button onclick="openAddModal()" class="btn-neon px-6 py-3 relative z-10">
                    <i class="fas fa-plus mr-2"></i>Thêm danh mục
                </button>
            </div>
        </div>
    </section>

    <section class="py-8 bg-gray-50 dark:bg-sport-navy">
        <div class="container mx-auto px-4 lg:px-8">
            <?php if (isset($_GET['message'])): ?>
                <?php $type = $_GET['type'] ?? 'success'; ?>
                <div class="modern-card bg-<?php echo $type === 'success' ? 'green' : 'red'; ?>-50 dark:bg-<?php echo $type === 'success' ? 'green' : 'red'; ?>-900/20 border-l-4 border-<?php echo $type === 'success' ? 'green' : 'red'; ?>-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $type === 'success' ? 'check' : 'exclamation'; ?>-circle text-<?php echo $type === 'success' ? 'green' : 'red'; ?>-500 text-xl mr-3"></i>
                        <p class="text-<?php echo $type === 'success' ? 'green' : 'red'; ?>-700 dark:text-<?php echo $type === 'success' ? 'green' : 'red'; ?>-300 font-semibold"><?php echo htmlspecialchars($_GET['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="modern-card bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-50 dark:bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-900/20 border-l-4 border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-500 rounded-xl p-4 mb-6 animate-slide-down">
                    <div class="flex items-center">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-500 text-xl mr-3"></i>
                        <p class="text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 dark:text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-300 font-semibold"><?php echo $message; ?></p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Categories Table -->
            <div class="modern-card bg-white dark:bg-sport-blue rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        Danh sách danh mục (<?php echo count($categories); ?>)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-sport-neon to-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">ID</th>
                                <th class="px-6 py-4 text-left font-bold">Tên danh mục</th>
                                <th class="px-6 py-4 text-left font-bold">Mô tả</th>
                                <th class="px-6 py-4 text-left font-bold">Số sản phẩm</th>
                                <th class="px-6 py-4 text-center font-bold">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $index => $cat): ?>
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-sport-navy transition-colors animate-slide-up" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-sport-neon">#<?php echo $cat['CategoryID']; ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($cat['CategoryName']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                    <?php echo htmlspecialchars(substr($cat['CategoryDescription'] ?? '', 0, 80)) . (strlen($cat['CategoryDescription'] ?? '') > 80 ? '...' : ''); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-semibold">
                                        <?php echo $cat['ProductCount']; ?> sản phẩm
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)"
                                                class="p-2 rounded-lg glass hover:bg-yellow-500 hover:text-white transition-all duration-300"
                                                title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="CategoryID" value="<?php echo $cat['CategoryID']; ?>">
                                            <button type="submit" 
                                                    class="p-2 rounded-lg glass hover:bg-red-500 hover:text-white transition-all duration-300"
                                                    onclick="return confirm('Xóa danh mục này? Lưu ý: Chỉ có thể xóa danh mục không có sản phẩm.')"
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
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Thêm danh mục mới</h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên danh mục *</label>
                    <input type="text" name="CategoryName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                    <textarea name="CategoryDescription" rows="4"
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
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Sửa danh mục</h3>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="CategoryID" id="edit_CategoryID">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tên danh mục *</label>
                    <input type="text" name="CategoryName" id="edit_CategoryName" required
                           class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-sport-navy text-gray-900 dark:text-white focus:border-sport-neon transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mô tả</label>
                    <textarea name="CategoryDescription" id="edit_CategoryDescription" rows="4"
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

function editCategory(cat) {
    document.getElementById('edit_CategoryID').value = cat.CategoryID;
    document.getElementById('edit_CategoryName').value = cat.CategoryName;
    document.getElementById('edit_CategoryDescription').value = cat.CategoryDescription || '';
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include '../includes/layout_footer.php'; ?>
