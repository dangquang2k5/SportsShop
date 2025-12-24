<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - SportShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../assets/js/api-client.js"></script>
</head>

<body class="bg-gray-50">

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-900 to-purple-900 text-white py-6 shadow-lg">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold">
                <i class="fas fa-shopping-bag mr-2"></i>Sản phẩm thể thao
            </h1>
        </div>
    </header>

    <!-- Filters -->
    <section class="bg-white shadow-sm py-4 sticky top-0 z-10">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap gap-4 items-center">
                <input type="text" id="search-input" placeholder="Tìm kiếm sản phẩm..."
                    class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">

                <select id="category-filter" class="px-4 py-2 border rounded-lg">
                    <option value="">Tất cả danh mục</option>
                </select>

                <select id="brand-filter" class="px-4 py-2 border rounded-lg">
                    <option value="">Tất cả thương hiệu</option>
                </select>

                <select id="sort-by" class="px-4 py-2 border rounded-lg">
                    <option value="created_at:DESC">Mới nhất</option>
                    <option value="Price:ASC">Giá thấp đến cao</option>
                    <option value="Price:DESC">Giá cao đến thấp</option>
                    <option value="ProductName:ASC">Tên A-Z</option>
                </select>

                <button onclick="loadProducts()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Tìm kiếm
                </button>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="container mx-auto px-4 py-8">
        <!-- Loading State -->
        <div id="loading" class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Đang tải sản phẩm...</p>
        </div>

        <!-- Error State -->
        <div id="error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <span id="error-message"></span>
        </div>

        <!-- Products Grid -->
        <div id="products-grid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 hidden">
            <!-- Products will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-lg">Không tìm thấy sản phẩm nào</p>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="hidden mt-8 flex justify-center items-center gap-2">
            <button onclick="changePage(-1)" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span id="page-info" class="px-4 py-2">Trang 1 / 1</span>
            <button onclick="changePage(1)" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </section>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        let categories = [];
        let brands = [];

        // Load categories and brands
        async function loadFilters() {
            try {
                const [categoriesRes, brandsRes] = await Promise.all([
                    api.getCategories(),
                    api.getBrands()
                ]);

                categories = categoriesRes.data;
                brands = brandsRes.data;

                // Populate category filter
                const categorySelect = document.getElementById('category-filter');
                categories.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.CategoryID;
                    option.textContent = cat.CategoryName;
                    categorySelect.appendChild(option);
                });

                // Populate brand filter
                const brandSelect = document.getElementById('brand-filter');
                brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.BrandID;
                    option.textContent = brand.BrandName;
                    brandSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading filters:', error);
            }
        }

        // Load products
        async function loadProducts() {
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const grid = document.getElementById('products-grid');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.getElementById('pagination');

            // Show loading
            loading.classList.remove('hidden');
            error.classList.add('hidden');
            grid.classList.add('hidden');
            emptyState.classList.add('hidden');
            pagination.classList.add('hidden');

            try {
                // Get filter values
                const search = document.getElementById('search-input').value;
                const categoryId = document.getElementById('category-filter').value;
                const brandId = document.getElementById('brand-filter').value;
                const sortBy = document.getElementById('sort-by').value.split(':');

                const params = {
                    page: currentPage,
                    limit: 12,
                    ...(search && { search }),
                    ...(categoryId && { categoryId }),
                    ...(brandId && { brandId }),
                    sortBy: sortBy[0],
                    sortOrder: sortBy[1]
                };

                const response = await api.getProducts(params);

                loading.classList.add('hidden');

                if (response.success && response.data.products.length > 0) {
                    displayProducts(response.data.products);
                    totalPages = response.data.pagination.totalPages;
                    updatePagination();
                    grid.classList.remove('hidden');
                    pagination.classList.remove('hidden');
                } else {
                    emptyState.classList.remove('hidden');
                }
            } catch (err) {
                loading.classList.add('hidden');
                error.classList.remove('hidden');
                document.getElementById('error-message').textContent = err.message;
            }
        }

        // Display products
        function displayProducts(products) {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = products.map(product => `
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="relative">
                        <img src="${product.MainImage || 'https://via.placeholder.com/300'}" 
                             alt="${product.ProductName}"
                             class="w-full h-64 object-cover">
                        ${product.TotalStock <= 0 ? '<div class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm">Hết hàng</div>' : ''}
                    </div>
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">${product.BrandName || 'No Brand'}</div>
                        <h3 class="font-bold text-lg mb-2 line-clamp-2">${product.ProductName}</h3>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl font-bold text-blue-600">${formatPrice(product.Price)}₫</span>
                            ${product.RatingAvg > 0 ? `
                                <div class="flex items-center">
                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                    <span class="text-sm">${product.RatingAvg}</span>
                                </div>
                            ` : ''}
                        </div>
                        <button onclick="viewProduct(${product.ProductID})" 
                                class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Xem chi tiết
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Pagination
        function updatePagination() {
            document.getElementById('page-info').textContent = `Trang ${currentPage} / ${totalPages}`;
        }

        function changePage(delta) {
            const newPage = currentPage + delta;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                loadProducts();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // View product detail
        function viewProduct(productId) {
            window.location.href = `product_detail.php?id=${productId}`;
        }

        // Format price
        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadFilters();
            loadProducts();

            // Search on Enter
            document.getElementById('search-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    currentPage = 1;
                    loadProducts();
                }
            });
        });
    </script>
</body>

</html>