CREATE DATABASE IF NOT EXISTS SportsStoreDB
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE SportsStoreDB;

CREATE TABLE Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Phone VARCHAR(20) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Address TEXT NOT NULL,
    Status Boolean DEFAULT 1 NOT NULL,
    Role ENUM('customer', 'admin') DEFAULT 'customer' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (Email),
    INDEX idx_phone (Phone),
    INDEX idx_role (Role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Categories (
    CategoryID INT AUTO_INCREMENT PRIMARY KEY,
    CategoryName VARCHAR(100) UNIQUE NOT NULL,
    CategoryDescription TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Brand (
    BrandID INT AUTO_INCREMENT PRIMARY KEY,
    BrandName VARCHAR(100) UNIQUE NOT NULL,
    BrandDescription TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Product (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    ProductName VARCHAR(150) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    Status ENUM('active', 'inactive', 'out_of_stock') DEFAULT 'active' NOT NULL,
    MainImage VARCHAR(255),
    Description TEXT,
    RatingAvg DECIMAL(3, 2) DEFAULT 0.00,
    CategoryID INT,
    BrandID INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (CategoryID) REFERENCES Categories(CategoryID) ON DELETE SET NULL,
    FOREIGN KEY (BrandID) REFERENCES Brand(BrandID) ON DELETE SET NULL,
    INDEX idx_category_id (CategoryID),
    INDEX idx_brand_id (BrandID),
    INDEX idx_product_name (ProductName),
    INDEX idx_price (Price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ProductDetail (
    ProductDetailID INT AUTO_INCREMENT PRIMARY KEY,
    ProductID INT NOT NULL,
    Size VARCHAR(20) NOT NULL,
    Color VARCHAR(50) NOT NULL,
    Quantity INT DEFAULT 0 NOT NULL,
    Image VARCHAR(255),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID) ON DELETE CASCADE,
    UNIQUE KEY uk_product_variant (ProductID, Size, Color),
    INDEX idx_product_id (ProductID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Cart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    INDEX idx_user_id (UserID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE CartItems (
    CartItemsID INT AUTO_INCREMENT PRIMARY KEY,
    CartID INT NOT NULL,
    ProductDetailID INT NOT NULL,
    Quantity INT DEFAULT 1 NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (CartID) REFERENCES Cart(CartID) ON DELETE CASCADE,
    FOREIGN KEY (ProductDetailID) REFERENCES ProductDetail(ProductDetailID) ON DELETE CASCADE,
    INDEX idx_cart_id (CartID),
    INDEX idx_product_detail_id (ProductDetailID),
    CHECK (Quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Voucher (
    VoucherID INT AUTO_INCREMENT PRIMARY KEY,
    VoucherCode VARCHAR(50) UNIQUE NOT NULL,
    DiscountValue DECIMAL(10,2) NOT NULL,
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL,
    Quantity INT NOT NULL,
    MinOrderValue DECIMAL(10,2) DEFAULT 0.00 NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_voucher_code (VoucherCode),
    CHECK (DiscountValue > 0),
    CHECK (Quantity >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT DEFAULT NULL,
    GuestName VARCHAR(100),
    GuestEmail VARCHAR(100),
    GuestPhone VARCHAR(20),
    TotalAmount DECIMAL(12,2) NOT NULL,
    Address VARCHAR(255) NOT NULL,
    Status ENUM('pending', 'processing', 'shipped', 'delivered', 'canceled') DEFAULT 'pending' NOT NULL,
    VoucherID INT DEFAULT NULL,
    Note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE SET NULL,
    FOREIGN KEY (VoucherID) REFERENCES Voucher(VoucherID) ON DELETE SET NULL,
    INDEX idx_user_id (UserID),
    INDEX idx_status (Status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE OrderDetails (
    OrderDetailID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    ProductDetailID INT NOT NULL,
    Quantity INT NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (ProductDetailID) REFERENCES ProductDetail(ProductDetailID) ON DELETE RESTRICT,
    INDEX idx_order_id (OrderID),
    INDEX idx_product_detail_id (ProductDetailID),
    CHECK (Quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Reviews (
    ReviewID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    ProductID INT NOT NULL,
    Content TEXT NULL,
    Rating INT NOT NULL,
    Status ENUM('pending', 'approved', 'hidden') DEFAULT 'pending' NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID) ON DELETE CASCADE,
    INDEX idx_user_id (UserID),
    INDEX idx_product_id (ProductID),
    INDEX idx_status (Status),
    CHECK (Rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE InventoryLog (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    ProductDetailID INT NOT NULL,
    QuantityIn INT DEFAULT 0 NOT NULL,
    QuantityOut INT DEFAULT 0 NOT NULL,
    Remaining INT NOT NULL,
    Reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ProductDetailID) REFERENCES ProductDetail(ProductDetailID) ON DELETE CASCADE,
    INDEX idx_product_detail_id (ProductDetailID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Sessions (
    session_id VARCHAR(128) NOT NULL PRIMARY KEY,
    user_id INT DEFAULT NULL,
    session_data TEXT NOT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PHAN 2: TRIGGERS (TU DONG HOA)
-- ============================================

-- BUOC 1: Cot RatingAvg da duoc them trong CREATE TABLE Product (line 67)
-- Khong can ALTER TABLE nua

DELIMITER //

-- Trigger 1: Tu dong tru kho va ghi log khi dat hang
CREATE TRIGGER trg_AfterInsertOrderDetail
AFTER INSERT ON OrderDetails
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    -- 1. Trừ số lượng tồn kho trong bảng ProductDetail
    UPDATE ProductDetail
    SET Quantity = Quantity - NEW.Quantity
    WHERE ProductDetailID = NEW.ProductDetailID;
    
    -- 2. Lấy số lượng tồn kho còn lại
    SELECT Quantity INTO current_stock
    FROM ProductDetail
    WHERE ProductDetailID = NEW.ProductDetailID;
    
    -- 3. Ghi vào nhật ký kho (InventoryLog)
    INSERT INTO InventoryLog (ProductDetailID, QuantityOut, Remaining, Reason)
    VALUES (NEW.ProductDetailID, NEW.Quantity, current_stock, CONCAT('Bán đơn hàng #', NEW.OrderID));
END//

-- Trigger 2: Tự động tính điểm đánh giá trung bình
CREATE TRIGGER trg_AfterInsertOrUpdateReview
AFTER INSERT ON Reviews
FOR EACH ROW
BEGIN
    UPDATE Product
    SET RatingAvg = (
        SELECT AVG(Rating)
        FROM Reviews
        WHERE ProductID = NEW.ProductID AND Status = 'approved'
    )
    WHERE ProductID = NEW.ProductID;
END//

-- *** TRIGGER MỚI (Rất quan trọng): Hoàn kho khi hủy đơn ***
CREATE TRIGGER trg_AfterUpdateOrder_Cancel
AFTER UPDATE ON Orders
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE d_product_detail_id INT;
    DECLARE d_quantity INT;
    DECLARE current_stock INT;
    
    -- Tạo con trỏ để lặp qua các món hàng trong đơn bị hủy
    DECLARE cur_order_items CURSOR FOR 
        SELECT ProductDetailID, Quantity 
        FROM OrderDetails 
        WHERE OrderID = OLD.OrderID;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Chỉ chạy logic nếu đơn hàng CHUYỂN SANG 'canceled'
    -- và trạng thái TRƯỚC ĐÓ không phải là 'canceled'
    IF NEW.Status = 'canceled' AND OLD.Status != 'canceled' THEN
        OPEN cur_order_items;
        
        read_loop: LOOP
            FETCH cur_order_items INTO d_product_detail_id, d_quantity;
            IF done THEN
                LEAVE read_loop;
            END IF;
            
            -- 1. Cộng (hoàn trả) số lượng vào ProductDetail
            UPDATE ProductDetail
            SET Quantity = Quantity + d_quantity
            WHERE ProductDetailID = d_product_detail_id;
            
            -- 2. Lấy số lượng tồn kho mới
            SELECT Quantity INTO current_stock
            FROM ProductDetail
            WHERE ProductDetailID = d_product_detail_id;
            
            -- 3. Ghi log hoàn kho
            INSERT INTO InventoryLog (ProductDetailID, QuantityIn, Remaining, Reason)
            VALUES (d_product_detail_id, d_quantity, current_stock, CONCAT('Hủy/Hoàn trả đơn hàng #', OLD.OrderID));
            
        END LOOP;
        
        CLOSE cur_order_items;
    END IF;
END//


-- ============================================
-- PHẦN 3: STORED PROCEDURES (NGHIỆP VỤ)
-- ============================================

-- *** PROCEDURE MỚI 1: Thêm vào giỏ hàng (Tự động kiểm tra) ***
CREATE PROCEDURE sp_AddToCart(IN p_UserID INT, IN p_ProductDetailID INT, IN p_Quantity INT)
BEGIN
    DECLARE v_CartID INT;
    DECLARE v_ExistingQuantity INT;
    DECLARE v_Price DECIMAL(10,2);
    
    -- Lấy CartID của User
    SELECT CartID INTO v_CartID FROM Cart WHERE UserID = p_UserID;
    
    -- Nếu User chưa có giỏ hàng, tạo mới
    IF v_CartID IS NULL THEN
        INSERT INTO Cart (UserID) VALUES (p_UserID);
        SET v_CartID = LAST_INSERT_ID();
    END IF;
    
    -- Kiểm tra xem sản phẩm đã có trong giỏ chưa
    SELECT Quantity INTO v_ExistingQuantity 
    FROM CartItems 
    WHERE CartID = v_CartID AND ProductDetailID = p_ProductDetailID;
    
    -- Lấy giá sản phẩm (từ bảng Product, join qua ProductDetail)
    SELECT p.Price INTO v_Price 
    FROM Product p
    JOIN ProductDetail pd ON p.ProductID = pd.ProductID
    WHERE pd.ProductDetailID = p_ProductDetailID;
    
    IF v_ExistingQuantity IS NOT NULL THEN
        -- Nếu có, UPDATE số lượng
        UPDATE CartItems
        SET Quantity = Quantity + p_Quantity
        WHERE CartID = v_CartID AND ProductDetailID = p_ProductDetailID;
    ELSE
        -- Nếu chưa, INSERT mới
        INSERT INTO CartItems (CartID, ProductDetailID, Quantity, Price)
        VALUES (v_CartID, p_ProductDetailID, p_Quantity, v_Price);
    END IF;
END//

-- *** PROCEDURE MỚI 2: Lấy chi tiết giỏ hàng của User ***
CREATE PROCEDURE sp_GetUserCart(IN p_UserID INT)
BEGIN
    SELECT 
        ci.CartItemsID,
        p.ProductID,
        pd.ProductDetailID,
        p.ProductName,
        pd.Size,
        pd.Color,
        pd.Image,
        ci.Quantity,
        ci.Price,
        (ci.Quantity * ci.Price) AS SubTotal
    FROM CartItems ci
    JOIN Cart c ON ci.CartID = c.CartID
    JOIN ProductDetail pd ON ci.ProductDetailID = pd.ProductDetailID
    JOIN Product p ON pd.ProductID = p.ProductID
    WHERE c.UserID = p_UserID;
END//

-- *** PROCEDURE MỚI 3: Nhập kho (Restock) ***
CREATE PROCEDURE sp_ImportStock(IN p_ProductDetailID INT, IN p_QuantityIn INT, IN p_Reason VARCHAR(255))
BEGIN
    DECLARE current_stock INT;
    
    -- 1. Cộng thêm hàng vào kho
    UPDATE ProductDetail
    SET Quantity = Quantity + p_QuantityIn
    WHERE ProductDetailID = p_ProductDetailID;

    -- 2. Lấy số lượng tồn kho mới
    SELECT Quantity INTO current_stock
    FROM ProductDetail
    WHERE ProductDetailID = p_ProductDetailID;
            
    -- 3. Ghi log nhập kho
    INSERT INTO InventoryLog (ProductDetailID, QuantityIn, Remaining, Reason)
    VALUES (p_ProductDetailID, p_QuantityIn, current_stock, p_Reason);
END//

-- PROCEDURE 4: Lấy sản phẩm bán chạy nhất (Đã sửa)
CREATE PROCEDURE GetBestSellingProducts(IN limit_count INT)
BEGIN
    SELECT 
        p.ProductID,
        p.ProductName,
        p.Price,
        p.MainImage,
        b.BrandName,
        SUM(od.Quantity) as total_sold
    FROM Product p
    JOIN Brand b ON p.BrandID = b.BrandID
    JOIN ProductDetail pd ON p.ProductID = pd.ProductID
    JOIN OrderDetails od ON pd.ProductDetailID = od.ProductDetailID
    JOIN Orders o ON od.OrderID = o.OrderID
    WHERE o.Status IN ('delivered', 'shipped')
    GROUP BY p.ProductID, p.ProductName, p.Price, p.MainImage, b.BrandName
    ORDER BY total_sold DESC
    LIMIT limit_count;
END//

-- PROCEDURE 5: Lấy doanh thu theo ngày (Đã sửa)
CREATE PROCEDURE GetRevenueByDateRange(IN p_start_date DATE, IN p_end_date DATE)
BEGIN
    SELECT 
        DATE(created_at) as order_day,
        COUNT(OrderID) as total_orders,
        SUM(TotalAmount) as daily_revenue
    FROM Orders
    WHERE DATE(created_at) BETWEEN p_start_date AND p_end_date
        AND Status IN ('delivered', 'shipped')
    GROUP BY DATE(created_at)
    ORDER BY order_day DESC;
END//

DELIMITER ; -- Trả Delimiter về mặc định

-- ============================================
-- PHẦN 4: CẤP QUYỀN
-- ============================================
-- GRANT ALL PRIVILEGES ON SportStoreDB.* TO 'root'@'%';
-- FLUSH PRIVILEGES;
-- ============================================
-- SAMPLE DATA FOR SPORTSHOP DATABASE
-- ============================================

USE SportsStoreDB;

-- ============================================
-- 0. USERS (Admin và Customer)
-- ============================================
INSERT INTO Users (FirstName, LastName, Email, Phone, Password, Address, Role, Status) VALUES
('Admin', 'Sport Store', 'admin@sportstore.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Admin Street, Hanoi', 'admin', 1),
('Nguyen Van', 'An', 'nguyenvanan@gmail.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Customer Street, HCMC', 'customer', 1);

-- ============================================
-- 1. BRANDS
-- ============================================
INSERT INTO Brand (BrandName, BrandDescription) VALUES
('Nike', 'Thương hiệu thể thao hàng đầu thế giới'),
('Adidas', 'Thương hiệu thể thao Đức với lịch sử lâu đời'),
('Puma', 'Thương hiệu thể thao Đức năng động'),
('Under Armour', 'Thương hiệu Mỹ chuyên trang phục thể thao'),
('New Balance', 'Thương hiệu giày thể thao Mỹ'),
('Reebok', 'Thương hiệu thể thao quốc tế'),
('Asics', 'Thương hiệu Nhật chuyên giày chạy bộ'),
('Mizuno', 'Thương hiệu Nhật Bản chất lượng cao'),
('Converse', 'Thương hiệu giày cổ điển'),
('Vans', 'Thương hiệu giày skateboard'),
('Fila', 'Thương hiệu thể thao Italia-Hàn Quốc'),
('Skechers', 'Thương hiệu giày thoải mái Mỹ'),
('Champion', 'Thương hiệu streetwear kinh điển'),
('The North Face', 'Thương hiệu outdoor hàng đầu'),
('Columbia', 'Thương hiệu outdoor và sportswear');

-- ============================================
-- 2. CATEGORIES
-- ============================================
INSERT INTO Categories (CategoryName, CategoryDescription) VALUES
('Giày thể thao', 'Các loại giày dành cho thể thao'),
('Áo thể thao', 'Áo thun, áo polo, áo khoác'),
('Quần thể thao', 'Quần short, quần dài, legging'),
('Phụ kiện', 'Tất, băng đô, găng tay'),
('Túi xách', 'Ba lô, túi thể thao'),
('Đồ bơi', 'Quần áo bơi, kính bơi'),
('Đồ tập gym', 'Trang phục gym chuyên dụng'),
('Giày chạy bộ', 'Giày chuyên dụng chạy bộ'),
('Giày bóng đá', 'Giày chuyên dụng bóng đá'),
('Giày tennis', 'Giày chuyên dụng tennis'),
('Giày bóng rổ', 'Đồ bóng rổ chuyên nghiệp'),
('Đồ yoga', 'Trang phục yoga và pilates'),
('Quần áo outdoor', 'Quần áo dã ngoại leo núi');

-- ============================================
-- 3. VOUCHERS
-- ============================================
INSERT INTO Voucher (VoucherCode, DiscountValue, MinOrderValue, Quantity, StartDate, EndDate) VALUES
('WELCOME2025', 100000, 500000, 100, '2025-01-01', '2025-12-31'),
('SUMMER50', 50000, 300000, 200, '2025-06-01', '2025-08-31'),
('NEWYEAR2025', 200000, 1000000, 50, '2025-01-01', '2025-01-31'),
('FLASH30', 30000, 200000, 500, '2025-01-01', '2025-12-31'),
('VIP100', 100000, 800000, 150, '2025-01-01', '2025-12-31'),
('SPORT20', 20000, 150000, 300, '2025-01-01', '2025-12-31'),
('MEGA200', 200000, 1500000, 30, '2025-01-01', '2025-12-31'),
('FREESHIP', 30000, 0, 1000, '2025-01-01', '2025-12-31'),
('AUTUMN150', 150000, 900000, 80, '2025-09-01', '2025-11-30'),
('BLACKFRIDAY', 500000, 2000000, 20, '2025-11-24', '2025-11-30'),
('STUDENT15', 15000, 100000, 400, '2025-01-01', '2025-12-31'),
('MEMBER250', 250000, 1200000, 60, '2025-01-01', '2025-12-31'),
('WEEKEND50', 50000, 400000, 250, '2025-01-01', '2025-12-31');

-- ============================================
-- 4. PRODUCTS - NIKE
-- ============================================
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Nike Air Max 270', 3500000, 1, 1, 'Giày Nike Air Max 270 với đệm khí Max Air lớn nhất', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/99486859-0ff3-46b4-949b-2d16af2ad421/custom-nike-air-max-270-by-you.png'),
('Nike Air Force 1', 2800000, 1, 1, 'Giày Nike Air Force 1 - biểu tượng phong cách đường phố', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/b7d9211c-26e7-431a-ac24-b0540fb3c00f/air-force-1-07-shoes-WrLlWX.png'),
('Nike React Infinity Run', 4200000, 8, 1, 'Giày chạy bộ Nike React Infinity Run giảm chấn thương', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/awjogtdnqxniqqk0wpgf/react-infinity-3-road-running-shoes-XJMHZl.png'),
('Nike Zoom Pegasus 40', 3800000, 8, 1, 'Giày chạy bộ Nike Zoom Pegasus 40 - lựa chọn runner', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/a8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/zoom-pegasus-40-road-running-shoes-zJMHZl.png'),
('Nike Mercurial Vapor', 4500000, 9, 1, 'Giày bóng đá Nike Mercurial Vapor tốc độ tối đa', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/b8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/mercurial-vapor-15-elite-fg-football-boots-zJMHZl.png'),
('Nike Dri-FIT Training Tee', 650000, 2, 1, 'Áo thun Nike Dri-FIT thấm hút mồ hôi nhanh', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/c8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/dri-fit-training-tee.png'),
('Nike Pro Compression Shirt', 850000, 7, 1, 'Áo compression Nike Pro hỗ trợ cơ bắp', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/d8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/pro-compression-shirt.png'),
('Nike Sportswear Hoodie', 1200000, 2, 1, 'Áo khoác hoodie Nike Sportswear cotton mềm mại', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/e8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/sportswear-club-hoodie.png'),
('Nike Flex Stride Shorts', 750000, 3, 1, 'Quần short Nike Flex Stride co giãn 4 chiều', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/f8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/flex-stride-shorts.png'),
('Nike Pro Training Tights', 950000, 7, 1, 'Quần legging Nike Pro hỗ trợ cơ bắp', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/g8b13a6f-c9e5-47f7-8a2e-e4f9d3b6c4d5/pro-training-tights.png');

-- ADIDAS PRODUCTS
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Adidas Ultraboost 23', 4800000, 8, 2, 'Giày Adidas Ultraboost 23 công nghệ Boost độc quyền', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/ultraboost-23.jpg'),
('Adidas Superstar', 2500000, 1, 2, 'Giày Adidas Superstar - biểu tượng vỏ sò', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/superstar.jpg'),
('Adidas Stan Smith', 2300000, 1, 2, 'Giày Adidas Stan Smith tối giản thanh lịch', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/stan-smith.jpg'),
('Adidas Predator Edge', 4200000, 9, 2, 'Giày bóng đá Adidas Predator Edge kiểm soát bóng tốt', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/predator-edge.jpg'),
('Adidas Techfit Tee', 680000, 2, 2, 'Áo Adidas Techfit với AEROREADY', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/techfit-tee.jpg'),
('Adidas Tiro Track Pants', 850000, 3, 2, 'Quần Adidas Tiro với 3 sọc iconic', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/tiro-track-pants.jpg'),
('Adidas Z.N.E Hoodie', 1500000, 2, 2, 'Áo khoác Adidas Z.N.E thiết kế hiện đại', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/zne-hoodie.jpg');

-- PUMA PRODUCTS  
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Puma RS-X', 2800000, 1, 3, 'Giày Puma RS-X chunky sneaker thịnh hành', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/rsx.jpg'),
('Puma Suede Classic', 2200000, 1, 3, 'Giày Puma Suede Classic da lộn cao cấp', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/suede-classic.jpg'),
('Puma Future Z', 3800000, 9, 3, 'Giày bóng đá Puma Future Z FUZIONFIT+', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/future-z.jpg'),
('Puma Velocity Nitro', 3200000, 8, 3, 'Giày chạy Puma Velocity Nitro foam nhẹ', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/velocity-nitro.jpg'),
('Puma Training Tee', 550000, 2, 3, 'Áo Puma dryCELL thấm hút tốt', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/training-tee.jpg'),
('Puma Essentials Shorts', 650000, 3, 3, 'Quần short Puma Essentials thoải mái', 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa/essentials-shorts.jpg');

-- UNDER ARMOUR
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('UA HOVR Phantom 3', 3600000, 8, 4, 'Giày UA HOVR Phantom 3 giảm chấn động', 'https://underarmour.scene7.com/is/image/Underarmour/hovr-phantom-3'),
('UA Curry Flow 10', 4500000, 1, 4, 'Giày bóng rổ UA Curry Flow 10', 'https://underarmour.scene7.com/is/image/Underarmour/curry-flow-10'),
('UA HeatGear Shirt', 780000, 7, 4, 'Áo compression UA HeatGear giữ mát', 'https://underarmour.scene7.com/is/image/Underarmour/heatgear-shirt'),
('UA Tech 2.0 Tee', 650000, 2, 4, 'Áo UA Tech 2.0 thoáng khí nhanh khô', 'https://underarmour.scene7.com/is/image/Underarmour/tech-20-tee'),
('UA Rival Fleece Hoodie', 1350000, 2, 4, 'Áo hoodie UA Rival Fleece ấm áp', 'https://underarmour.scene7.com/is/image/Underarmour/rival-fleece-hoodie');

-- NEW BALANCE
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('New Balance 990v6', 5200000, 1, 5, 'Giày NB 990v6 Made in USA cao cấp', 'https://nb.scene7.com/is/image/NB/990v6'),
('New Balance 1080v12', 4200000, 8, 5, 'Giày chạy NB 1080v12 Fresh Foam X', 'https://nb.scene7.com/is/image/NB/1080v12'),
('New Balance 574', 2600000, 1, 5, 'Giày NB 574 thiết kế retro đa năng', 'https://nb.scene7.com/is/image/NB/574'),
('NB Impact Run Singlet', 580000, 2, 5, 'Áo ba lỗ NB Impact Run siêu nhẹ', 'https://nb.scene7.com/is/image/NB/impact-run-singlet'),
('NB Accelerate Shorts', 720000, 3, 5, 'Quần short NB Accelerate với NB DRY', 'https://nb.scene7.com/is/image/NB/accelerate-shorts');

-- REEBOK
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Reebok Nano X3', 3400000, 7, 6, 'Giày gym Reebok Nano X3 cho CrossFit', 'https://assets.reebok.com/images/h_840,f_auto,q_auto/nano-x3'),
('Reebok Club C 85', 2100000, 1, 6, 'Giày Reebok Club C 85 tennis cổ điển', 'https://assets.reebok.com/images/h_840,f_auto,q_auto/club-c-85'),
('Reebok Classic Leather', 2300000, 1, 6, 'Giày Reebok Classic Leather da thật', 'https://assets.reebok.com/images/h_840,f_auto,q_auto/classic-leather'),
('Reebok Workout Tee', 520000, 2, 6, 'Áo Reebok Workout Ready Speedwick', 'https://assets.reebok.com/images/h_840,f_auto,q_auto/workout-ready-tee');

-- ASICS
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Asics Gel-Kayano 30', 4800000, 8, 7, 'Giày Asics Gel-Kayano 30 công nghệ GEL', 'https://images.asics.com/is/image/asics/gel-kayano-30'),
('Asics Gel-Nimbus 25', 4600000, 8, 7, 'Giày Asics Gel-Nimbus 25 đệm êm ái', 'https://images.asics.com/is/image/asics/gel-nimbus-25'),
('Asics Gel-Resolution 9', 3800000, 10, 7, 'Giày tennis Asics Gel-Resolution 9', 'https://images.asics.com/is/image/asics/gel-resolution-9'),
('Asics Core Running Tee', 620000, 2, 7, 'Áo chạy Asics Core thoáng khí', 'https://images.asics.com/is/image/asics/core-running-tee');

-- MIZUNO
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Mizuno Wave Rider 27', 3800000, 8, 8, 'Giày Mizuno Wave Rider 27 công nghệ Wave', 'https://mizuno.com/images/wave-rider-27'),
('Mizuno Wave Sky 7', 4200000, 8, 8, 'Giày Mizuno Wave Sky 7 đệm tối đa', 'https://mizuno.com/images/wave-sky-7'),
('Mizuno Morelia Neo IV', 4800000, 9, 8, 'Giày bóng đá Mizuno Morelia Neo IV da kangaroo', 'https://mizuno.com/images/morelia-neo-4');

-- CONVERSE
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Converse Chuck Taylor', 1500000, 1, 9, 'Giày Converse Chuck Taylor All Star biểu tượng', 'https://www.converse.com/dw/image/v2/chuck-taylor-all-star'),
('Converse Chuck 70', 1800000, 1, 9, 'Giày Converse Chuck 70 phiên bản cao cấp', 'https://www.converse.com/dw/image/v2/chuck-70'),
('Converse Run Star Hike', 2200000, 1, 9, 'Giày Converse Run Star Hike đế platform', 'https://www.converse.com/dw/image/v2/run-star-hike');

-- VANS
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Vans Old Skool', 1600000, 1, 10, 'Giày Vans Old Skool sọc Sidestripe', 'https://images.vans.com/is/image/Vans/old-skool'),
('Vans Authentic', 1400000, 1, 10, 'Giày Vans Authentic thiết kế cổ điển', 'https://images.vans.com/is/image/Vans/authentic'),
('Vans Sk8-Hi', 1800000, 1, 10, 'Giày Vans Sk8-Hi cổ cao hỗ trợ mắt cá', 'https://images.vans.com/is/image/Vans/sk8-hi'),
('Vans UltraRange EXO', 2400000, 1, 10, 'Giày Vans UltraRange EXO đa địa hình', 'https://images.vans.com/is/image/Vans/ultrarange-exo');

-- FILA
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Fila Disruptor II', 1900000, 1, 11, 'Giày Fila Disruptor II chunky sneaker nổi tiếng', 'https://www.fila.com/disruptor-2.jpg'),
('Fila Ray Tracer', 2100000, 1, 11, 'Giày Fila Ray Tracer phong cách retro', 'https://www.fila.com/ray-tracer.jpg'),
('Fila Court Deluxe', 1700000, 1, 11, 'Giày tennis Fila Court Deluxe cổ điển', 'https://www.fila.com/court-deluxe.jpg'),
('Fila Heritage Tee', 450000, 2, 11, 'Áo Fila Heritage logo vintage', 'https://www.fila.com/heritage-tee.jpg'),
('Fila Track Jacket', 980000, 2, 11, 'Áo khoác Fila Track phong cách retro', 'https://www.fila.com/track-jacket.jpg');

-- SKECHERS
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Skechers D\'Lux Walker', 2200000, 1, 12, 'Giày Skechers D\'Lux Walker thoải mái tối đa', 'https://www.skechers.com/dlux-walker.jpg'),
('Skechers Go Walk 6', 1800000, 1, 12, 'Giày Skechers Go Walk 6 siêu nhẹ', 'https://www.skechers.com/gowalk-6.jpg'),
('Skechers Flex Appeal', 1650000, 1, 12, 'Giày Skechers Flex Appeal cho phụ nữ', 'https://www.skechers.com/flex-appeal.jpg'),
('Skechers Performance Tee', 420000, 2, 12, 'Áo Skechers Performance thoáng khí', 'https://www.skechers.com/performance-tee.jpg');

-- CHAMPION
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Champion Reverse Weave Hoodie', 1400000, 2, 13, 'Áo hoodie Champion Reverse Weave iconic', 'https://www.champion.com/reverse-weave-hoodie.jpg'),
('Champion Heritage Tee', 550000, 2, 13, 'Áo Champion Heritage logo cổ điển', 'https://www.champion.com/heritage-tee.jpg'),
('Champion Powerblend Sweatshirt', 880000, 2, 13, 'Áo sweatshirt Champion Powerblend', 'https://www.champion.com/powerblend-sweatshirt.jpg'),
('Champion Jersey Shorts', 480000, 3, 13, 'Quần short Champion Jersey thoải mái', 'https://www.champion.com/jersey-shorts.jpg'),
('Champion Script Logo Joggers', 720000, 3, 13, 'Quần jogger Champion Script Logo', 'https://www.champion.com/script-joggers.jpg');

-- THE NORTH FACE
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('The North Face Nuptse Jacket', 7500000, 13, 14, 'Áo phông The North Face Nuptse huyền thoại', 'https://www.thenorthface.com/nuptse-jacket.jpg'),
('The North Face Denali Fleece', 3200000, 2, 14, 'Áo fleece The North Face Denali ấm áp', 'https://www.thenorthface.com/denali-fleece.jpg'),
('The North Face Essential Tee', 680000, 2, 14, 'Áo The North Face Essential cotton', 'https://www.thenorthface.com/essential-tee.jpg'),
('The North Face Explore Pant', 1450000, 13, 14, 'Quần dã ngoại The North Face Explore', 'https://www.thenorthface.com/explore-pant.jpg'),
('The North Face Base Camp Backpack', 2800000, 5, 14, 'Ba lô The North Face Base Camp', 'https://www.thenorthface.com/base-camp-backpack.jpg');

-- COLUMBIA
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Columbia Newton Ridge Hiking', 2900000, 1, 15, 'Giày leo núi Columbia Newton Ridge', 'https://www.columbia.com/newton-ridge.jpg'),
('Columbia Silver Ridge Shirt', 980000, 2, 15, 'Áo Columbia Silver Ridge chống nắng', 'https://www.columbia.com/silver-ridge-shirt.jpg'),
('Columbia Omni-Heat Jacket', 4500000, 13, 15, 'Áo khoác Columbia Omni-Heat giữ nhiệt', 'https://www.columbia.com/omni-heat-jacket.jpg'),
('Columbia Cargo Pants', 1250000, 13, 15, 'Quần Columbia Cargo đa túi', 'https://www.columbia.com/cargo-pants.jpg');

-- THÊM SẢN PHẨM TỪ NIKE
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Nike LeBron 21', 5200000, 11, 1, 'Giày bóng rổ Nike LeBron 21', 'https://static.nike.com/lebron-21.jpg'),
('Nike KD 16', 4800000, 11, 1, 'Giày bóng rổ Nike KD 16', 'https://static.nike.com/kd-16.jpg'),
('Nike Yoga Dri-FIT', 880000, 12, 1, 'Áo yoga Nike Dri-FIT thoáng mát', 'https://static.nike.com/yoga-dri-fit.jpg'),
('Nike Zenvy Leggings', 1350000, 12, 1, 'Quần legging Nike Zenvy yoga', 'https://static.nike.com/zenvy-leggings.jpg'),
('Nike Brasilia Backpack', 890000, 5, 1, 'Ba lô Nike Brasilia thể thao', 'https://static.nike.com/brasilia-backpack.jpg');

-- THÊM SẢN PHẨM TỪ ADIDAS
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Adidas Harden Vol 7', 4600000, 11, 2, 'Giày bóng rổ Adidas Harden Vol 7', 'https://assets.adidas.com/harden-vol7.jpg'),
('Adidas Dame 8', 4200000, 11, 2, 'Giày bóng rổ Adidas Dame 8', 'https://assets.adidas.com/dame-8.jpg'),
('Adidas Yoga Flow Tank', 780000, 12, 2, 'Áo yoga Adidas Flow Tank', 'https://assets.adidas.com/yoga-flow-tank.jpg'),
('Adidas 4DFWD Running', 5500000, 8, 2, 'Giày chạy Adidas 4DFWD công nghệ 4D', 'https://assets.adidas.com/4dfwd.jpg'),
('Adidas Classic Backpack', 950000, 5, 2, 'Ba lô Adidas Classic 3 sọc', 'https://assets.adidas.com/classic-backpack.jpg');

-- THÊM SẢN PHẨM TỪ PUMA
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
('Puma Clyde All-Pro', 3900000, 11, 3, 'Giày bóng rổ Puma Clyde All-Pro', 'https://images.puma.com/clyde-all-pro.jpg'),
('Puma MB.02', 4100000, 11, 3, 'Giày bóng rổ Puma MB.02 LaMelo Ball', 'https://images.puma.com/mb-02.jpg'),
('Puma Studio Yoga Top', 690000, 12, 3, 'Áo yoga Puma Studio', 'https://images.puma.com/studio-yoga-top.jpg'),
('Puma Evercat Backpack', 780000, 5, 3, 'Ba lô Puma Evercat đa năng', 'https://images.puma.com/evercat-backpack.jpg');

-- ============================================
-- PRODUCT DETAILS (Size và Màu sắc)
-- ============================================

USE SportsStoreDB;

-- Helper: Tạo chi tiết cho giày (sizes 38-43, nhiều màu)
-- Nike Air Max 270 (ProductID = 1)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(1, '38', 'Đen', 15), (1, '39', 'Đen', 20), (1, '40', 'Đen', 25), (1, '41', 'Đen', 30), (1, '42', 'Đen', 25), (1, '43', 'Đen', 20),
(1, '38', 'Trắng', 15), (1, '39', 'Trắng', 20), (1, '40', 'Trắng', 25), (1, '41', 'Trắng', 30), (1, '42', 'Trắng', 25), (1, '43', 'Trắng', 20),
(1, '40', 'Xanh Navy', 20), (1, '41', 'Xanh Navy', 25), (1, '42', 'Xanh Navy', 20);

-- Nike Air Force 1 (ProductID = 2)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(2, '38', 'Trắng', 20), (2, '39', 'Trắng', 25), (2, '40', 'Trắng', 30), (2, '41', 'Trắng', 35), (2, '42', 'Trắng', 30), (2, '43', 'Trắng', 25),
(2, '40', 'Đen', 25), (2, '41', 'Đen', 30), (2, '42', 'Đen', 25),
(2, '40', 'Hồng', 18), (2, '41', 'Hồng', 22), (2, '42', 'Hồng', 18);

-- Nike React Infinity Run (ProductID = 3)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(3, '39', 'Đen/Trắng', 15), (3, '40', 'Đen/Trắng', 20), (3, '41', 'Đen/Trắng', 25), (3, '42', 'Đen/Trắng', 20), (3, '43', 'Đen/Trắng', 15),
(3, '40', 'Xanh Dương', 18), (3, '41', 'Xanh Dương', 22), (3, '42', 'Xanh Dương', 18);

-- Nike Zoom Pegasus 40 (ProductID = 4)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(4, '38', 'Đen', 12), (4, '39', 'Đen', 18), (4, '40', 'Đen', 22), (4, '41', 'Đen', 25), (4, '42', 'Đen', 22), (4, '43', 'Đen', 18),
(4, '40', 'Xám', 20), (4, '41', 'Xám', 24), (4, '42', 'Xám', 20);

-- Nike Mercurial Vapor (ProductID = 5)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(5, '39', 'Đỏ', 12), (5, '40', 'Đỏ', 18), (5, '41', 'Đỏ', 20), (5, '42', 'Đỏ', 18), (5, '43', 'Đỏ', 12),
(5, '40', 'Xanh Lá', 15), (5, '41', 'Xanh Lá', 18), (5, '42', 'Xanh Lá', 15);

-- Nike Dri-FIT Training Tee (ProductID = 6)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(6, 'S', 'Đen', 30), (6, 'M', 'Đen', 40), (6, 'L', 'Đen', 35), (6, 'XL', 'Đen', 25),
(6, 'S', 'Trắng', 30), (6, 'M', 'Trắng', 40), (6, 'L', 'Trắng', 35), (6, 'XL', 'Trắng', 25),
(6, 'M', 'Xanh Navy', 35), (6, 'L', 'Xanh Navy', 30), (6, 'XL', 'Xanh Navy', 20);

-- Nike Pro Compression Shirt (ProductID = 7)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(7, 'S', 'Đen', 25), (7, 'M', 'Đen', 35), (7, 'L', 'Đen', 30), (7, 'XL', 'Đen', 20),
(7, 'M', 'Xám', 30), (7, 'L', 'Xám', 25), (7, 'XL', 'Xám', 18);

-- Nike Sportswear Hoodie (ProductID = 8)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(8, 'S', 'Đen', 20), (8, 'M', 'Đen', 30), (8, 'L', 'Đen', 25), (8, 'XL', 'Đen', 18),
(8, 'M', 'Xám', 25), (8, 'L', 'Xám', 22), (8, 'XL', 'Xám', 15);

-- Nike Flex Stride Shorts (ProductID = 9)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(9, 'S', 'Đen', 25), (9, 'M', 'Đen', 35), (9, 'L', 'Đen', 30), (9, 'XL', 'Đen', 20),
(9, 'M', 'Xanh Navy', 30), (9, 'L', 'Xanh Navy', 25);

-- Nike Pro Training Tights (ProductID = 10)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(10, 'S', 'Đen', 22), (10, 'M', 'Đen', 32), (10, 'L', 'Đen', 28), (10, 'XL', 'Đen', 18);

-- Adidas Ultraboost 23 (ProductID = 11)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(11, '38', 'Trắng', 15), (11, '39', 'Trắng', 20), (11, '40', 'Trắng', 25), (11, '41', 'Trắng', 28), (11, '42', 'Trắng', 25), (11, '43', 'Trắng', 18),
(11, '40', 'Đen', 22), (11, '41', 'Đen', 26), (11, '42', 'Đen', 22);

-- Adidas Superstar (ProductID = 12)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(12, '38', 'Trắng/Đen', 18), (12, '39', 'Trắng/Đen', 24), (12, '40', 'Trắng/Đen', 28), (12, '41', 'Trắng/Đen', 30), (12, '42', 'Trắng/Đen', 28), (12, '43', 'Trắng/Đen', 22),
(12, '40', 'Đen/Trắng', 20), (12, '41', 'Đen/Trắng', 24), (12, '42', 'Đen/Trắng', 20);

-- Adidas Stan Smith (ProductID = 13)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(13, '38', 'Trắng/Xanh', 16), (13, '39', 'Trắng/Xanh', 22), (13, '40', 'Trắng/Xanh', 26), (13, '41', 'Trắng/Xanh', 28), (13, '42', 'Trắng/Xanh', 26), (13, '43', 'Trắng/Xanh', 20),
(13, '40', 'Trắng/Đen', 22), (13, '41', 'Trắng/Đen', 25), (13, '42', 'Trắng/Đen', 22);

-- Adidas Predator Edge (ProductID = 14)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(14, '39', 'Đỏ', 14), (14, '40', 'Đỏ', 18), (14, '41', 'Đỏ', 22), (14, '42', 'Đỏ', 18), (14, '43', 'Đỏ', 14),
(14, '40', 'Đen', 16), (14, '41', 'Đen', 20), (14, '42', 'Đen', 16);

-- Adidas Techfit Tee (ProductID = 15)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(15, 'S', 'Đen', 28), (15, 'M', 'Đen', 38), (15, 'L', 'Đen', 32), (15, 'XL', 'Đen', 22),
(15, 'M', 'Xanh Navy', 32), (15, 'L', 'Xanh Navy', 28);

-- Adidas Tiro Track Pants (ProductID = 16)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(16, 'S', 'Đen', 26), (16, 'M', 'Đen', 36), (16, 'L', 'Đen', 30), (16, 'XL', 'Đen', 20),
(16, 'M', 'Xanh Navy', 30), (16, 'L', 'Xanh Navy', 26);

-- Adidas Z.N.E Hoodie (ProductID = 17)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(17, 'S', 'Đen', 18), (17, 'M', 'Đen', 28), (17, 'L', 'Đen', 24), (17, 'XL', 'Đen', 16),
(17, 'M', 'Xám', 24), (17, 'L', 'Xám', 20);

-- Puma RS-X (ProductID = 18)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(18, '38', 'Đa Màu', 14), (18, '39', 'Đa Màu', 20), (18, '40', 'Đa Màu', 24), (18, '41', 'Đa Màu', 26), (18, '42', 'Đa Màu', 24), (18, '43', 'Đa Màu', 18),
(18, '40', 'Đen/Trắng', 20), (18, '41', 'Đen/Trắng', 22), (18, '42', 'Đen/Trắng', 20);

-- Puma Suede Classic (ProductID = 19)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(19, '38', 'Đen', 16), (19, '39', 'Đen', 22), (19, '40', 'Đen', 26), (19, '41', 'Đen', 28), (19, '42', 'Đen', 26), (19, '43', 'Đen', 20),
(19, '40', 'Xanh Navy', 22), (19, '41', 'Xanh Navy', 24), (19, '42', 'Xanh Navy', 22);

-- Puma Future Z (ProductID = 20)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(20, '39', 'Vàng', 12), (20, '40', 'Vàng', 16), (20, '41', 'Vàng', 20), (20, '42', 'Vàng', 16), (20, '43', 'Vàng', 12);

-- Puma Velocity Nitro (ProductID = 21)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(21, '39', 'Xanh Dương', 14), (21, '40', 'Xanh Dương', 18), (21, '41', 'Xanh Dương', 22), (21, '42', 'Xanh Dương', 18), (21, '43', 'Xanh Dương', 14);

-- Puma Training Tee (ProductID = 22)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(22, 'S', 'Đen', 26), (22, 'M', 'Đen', 36), (22, 'L', 'Đen', 30), (22, 'XL', 'Đen', 20),
(22, 'M', 'Trắng', 32), (22, 'L', 'Trắng', 28);

-- Puma Essentials Shorts (ProductID = 23)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(23, 'S', 'Đen', 24), (23, 'M', 'Đen', 34), (23, 'L', 'Đen', 28), (23, 'XL', 'Đen', 18);

-- Under Armour HOVR Phantom 3 (ProductID = 24)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(24, '39', 'Đen', 14), (24, '40', 'Đen', 18), (24, '41', 'Đen', 22), (24, '42', 'Đen', 18), (24, '43', 'Đen', 14),
(24, '40', 'Xám', 16), (24, '41', 'Xám', 20), (24, '42', 'Xám', 16);

-- Under Armour Curry Flow 10 (ProductID = 25)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(25, '39', 'Trắng/Vàng', 12), (25, '40', 'Trắng/Vàng', 16), (25, '41', 'Trắng/Vàng', 20), (25, '42', 'Trắng/Vàng', 16), (25, '43', 'Trắng/Vàng', 12);

-- Under Armour HeatGear Shirt (ProductID = 26)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(26, 'S', 'Đen', 24), (26, 'M', 'Đen', 34), (26, 'L', 'Đen', 28), (26, 'XL', 'Đen', 18);

-- Under Armour Tech 2.0 Tee (ProductID = 27)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(27, 'S', 'Đen', 26), (27, 'M', 'Đen', 36), (27, 'L', 'Đen', 30), (27, 'XL', 'Đen', 20),
(27, 'M', 'Xám', 32), (27, 'L', 'Xám', 28);

-- Under Armour Rival Fleece Hoodie (ProductID = 28)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(28, 'S', 'Đen', 18), (28, 'M', 'Đen', 28), (28, 'L', 'Đen', 24), (28, 'XL', 'Đen', 16);

-- New Balance 990v6 (ProductID = 29)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(29, '38', 'Xám', 12), (29, '39', 'Xám', 16), (29, '40', 'Xám', 20), (29, '41', 'Xám', 22), (29, '42', 'Xám', 20), (29, '43', 'Xám', 16);

-- New Balance 1080v12 (ProductID = 30)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(30, '39', 'Xanh Dương', 14), (30, '40', 'Xanh Dương', 18), (30, '41', 'Xanh Dương', 22), (30, '42', 'Xanh Dương', 18), (30, '43', 'Xanh Dương', 14);

-- New Balance 574 (ProductID = 31)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(31, '38', 'Xám/Xanh', 16), (31, '39', 'Xám/Xanh', 22), (31, '40', 'Xám/Xanh', 26), (31, '41', 'Xám/Xanh', 28), (31, '42', 'Xám/Xanh', 26), (31, '43', 'Xám/Xanh', 20);

-- NB Impact Run Singlet (ProductID = 32)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(32, 'S', 'Đen', 24), (32, 'M', 'Đen', 34), (32, 'L', 'Đen', 28), (32, 'XL', 'Đen', 18);

-- NB Accelerate Shorts (ProductID = 33)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(33, 'S', 'Đen', 22), (33, 'M', 'Đen', 32), (33, 'L', 'Đen', 26), (33, 'XL', 'Đen', 16);

-- Reebok Nano X3 (ProductID = 34)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(34, '39', 'Đen', 14), (34, '40', 'Đen', 18), (34, '41', 'Đen', 22), (34, '42', 'Đen', 18), (34, '43', 'Đen', 14);

-- Reebok Club C 85 (ProductID = 35)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(35, '38', 'Trắng/Xanh', 16), (35, '39', 'Trắng/Xanh', 22), (35, '40', 'Trắng/Xanh', 26), (35, '41', 'Trắng/Xanh', 28), (35, '42', 'Trắng/Xanh', 26), (35, '43', 'Trắng/Xanh', 20);

-- Reebok Classic Leather (ProductID = 36)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(36, '38', 'Đen', 14), (36, '39', 'Đen', 20), (36, '40', 'Đen', 24), (36, '41', 'Đen', 26), (36, '42', 'Đen', 24), (36, '43', 'Đen', 18);

-- Reebok Workout Tee (ProductID = 37)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(37, 'S', 'Đen', 24), (37, 'M', 'Đen', 34), (37, 'L', 'Đen', 28), (37, 'XL', 'Đen', 18);

-- Asics Gel-Kayano 30 (ProductID = 38)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(38, '39', 'Xanh Dương', 12), (38, '40', 'Xanh Dương', 16), (38, '41', 'Xanh Dương', 20), (38, '42', 'Xanh Dương', 16), (38, '43', 'Xanh Dương', 12);

-- Asics Gel-Nimbus 25 (ProductID = 39)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(39, '39', 'Đen', 12), (39, '40', 'Đen', 16), (39, '41', 'Đen', 20), (39, '42', 'Đen', 16), (39, '43', 'Đen', 12);

-- Asics Gel-Resolution 9 (ProductID = 40)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(40, '39', 'Trắng/Xanh', 12), (40, '40', 'Trắng/Xanh', 16), (40, '41', 'Trắng/Xanh', 20), (40, '42', 'Trắng/Xanh', 16), (40, '43', 'Trắng/Xanh', 12);

-- Asics Core Running Tee (ProductID = 41)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(41, 'S', 'Đen', 22), (41, 'M', 'Đen', 32), (41, 'L', 'Đen', 26), (41, 'XL', 'Đen', 16);

-- Mizuno Wave Rider 27 (ProductID = 42)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(42, '39', 'Cam', 12), (42, '40', 'Cam', 16), (42, '41', 'Cam', 20), (42, '42', 'Cam', 16), (42, '43', 'Cam', 12);

-- Mizuno Wave Sky 7 (ProductID = 43)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(43, '39', 'Xanh Lá', 12), (43, '40', 'Xanh Lá', 16), (43, '41', 'Xanh Lá', 20), (43, '42', 'Xanh Lá', 16), (43, '43', 'Xanh Lá', 12);

-- Mizuno Morelia Neo IV (ProductID = 44)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(44, '39', 'Đen', 10), (44, '40', 'Đen', 14), (44, '41', 'Đen', 18), (44, '42', 'Đen', 14), (44, '43', 'Đen', 10);

-- Converse Chuck Taylor (ProductID = 45)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(45, '38', 'Đen', 18), (45, '39', 'Đen', 24), (45, '40', 'Đen', 28), (45, '41', 'Đen', 30), (45, '42', 'Đen', 28), (45, '43', 'Đen', 22),
(45, '38', 'Trắng', 18), (45, '39', 'Trắng', 24), (45, '40', 'Trắng', 28), (45, '41', 'Trắng', 30), (45, '42', 'Trắng', 28), (45, '43', 'Trắng', 22),
(45, '40', 'Đỏ', 20), (45, '41', 'Đỏ', 24), (45, '42', 'Đỏ', 20);

-- Converse Chuck 70 (ProductID = 46)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(46, '38', 'Đen', 16), (46, '39', 'Đen', 22), (46, '40', 'Đen', 26), (46, '41', 'Đen', 28), (46, '42', 'Đen', 26), (46, '43', 'Đen', 20);

-- Converse Run Star Hike (ProductID = 47)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(47, '38', 'Đen', 14), (47, '39', 'Đen', 18), (47, '40', 'Đen', 22), (47, '41', 'Đen', 24), (47, '42', 'Đen', 22), (47, '43', 'Đen', 16);

-- Vans Old Skool (ProductID = 48)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(48, '38', 'Đen/Trắng', 18), (48, '39', 'Đen/Trắng', 24), (48, '40', 'Đen/Trắng', 28), (48, '41', 'Đen/Trắng', 30), (48, '42', 'Đen/Trắng', 28), (48, '43', 'Đen/Trắng', 22);

-- Vans Authentic (ProductID = 49)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(49, '38', 'Đen', 16), (49, '39', 'Đen', 22), (49, '40', 'Đen', 26), (49, '41', 'Đen', 28), (49, '42', 'Đen', 26), (49, '43', 'Đen', 20),
(49, '40', 'Trắng', 22), (49, '41', 'Trắng', 26), (49, '42', 'Trắng', 22);

-- Vans Sk8-Hi (ProductID = 50)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(50, '38', 'Đen', 14), (50, '39', 'Đen', 20), (50, '40', 'Đen', 24), (50, '41', 'Đen', 26), (50, '42', 'Đen', 24), (50, '43', 'Đen', 18);

-- Vans UltraRange EXO (ProductID = 51)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(51, '38', 'Xám', 12), (51, '39', 'Xám', 18), (51, '40', 'Xám', 22), (51, '41', 'Xám', 24), (51, '42', 'Xám', 22), (51, '43', 'Xám', 16);

-- Fila Disruptor II (ProductID = 52)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(52, '38', 'Trắng', 16), (52, '39', 'Trắng', 22), (52, '40', 'Trắng', 26), (52, '41', 'Trắng', 28), (52, '42', 'Trắng', 26), (52, '43', 'Trắng', 20),
(52, '40', 'Đen', 20), (52, '41', 'Đen', 24), (52, '42', 'Đen', 20);

-- Fila Ray Tracer (ProductID = 53)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(53, '38', 'Trắng/Đỏ', 14), (53, '39', 'Trắng/Đỏ', 20), (53, '40', 'Trắng/Đỏ', 24), (53, '41', 'Trắng/Đỏ', 26), (53, '42', 'Trắng/Đỏ', 24), (53, '43', 'Trắng/Đỏ', 18);

-- Fila Court Deluxe (ProductID = 54)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(54, '38', 'Trắng', 14), (54, '39', 'Trắng', 18), (54, '40', 'Trắng', 22), (54, '41', 'Trắng', 24), (54, '42', 'Trắng', 22), (54, '43', 'Trắng', 16);

-- Fila Heritage Tee (ProductID = 55)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(55, 'S', 'Trắng', 22), (55, 'M', 'Trắng', 32), (55, 'L', 'Trắng', 26), (55, 'XL', 'Trắng', 18),
(55, 'M', 'Đen', 28), (55, 'L', 'Đen', 24);

-- Fila Track Jacket (ProductID = 56)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(56, 'S', 'Xanh Navy', 16), (56, 'M', 'Xanh Navy', 26), (56, 'L', 'Xanh Navy', 22), (56, 'XL', 'Xanh Navy', 14),
(56, 'M', 'Đen', 24), (56, 'L', 'Đen', 20);

-- Skechers D'Lux Walker (ProductID = 57)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(57, '38', 'Đen', 14), (57, '39', 'Đen', 18), (57, '40', 'Đen', 22), (57, '41', 'Đen', 24), (57, '42', 'Đen', 22), (57, '43', 'Đen', 16),
(57, '40', 'Xám', 18), (57, '41', 'Xám', 20), (57, '42', 'Xám', 18);

-- Skechers Go Walk 6 (ProductID = 58)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(58, '38', 'Xám', 16), (58, '39', 'Xám', 20), (58, '40', 'Xám', 24), (58, '41', 'Xám', 26), (58, '42', 'Xám', 24), (58, '43', 'Xám', 18);

-- Skechers Flex Appeal (ProductID = 59)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(59, '36', 'Hồng', 18), (59, '37', 'Hồng', 22), (59, '38', 'Hồng', 26), (59, '39', 'Hồng', 28), (59, '40', 'Hồng', 24), (59, '41', 'Hồng', 18),
(59, '38', 'Đen', 20), (59, '39', 'Đen', 24), (59, '40', 'Đen', 20);

-- Skechers Performance Tee (ProductID = 60)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(60, 'S', 'Đen', 24), (60, 'M', 'Đen', 34), (60, 'L', 'Đen', 28), (60, 'XL', 'Đen', 18);

-- Champion Reverse Weave Hoodie (ProductID = 61)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(61, 'S', 'Xám', 18), (61, 'M', 'Xám', 28), (61, 'L', 'Xám', 24), (61, 'XL', 'Xám', 16),
(61, 'M', 'Đen', 26), (61, 'L', 'Đen', 22);

-- Champion Heritage Tee (ProductID = 62)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(62, 'S', 'Trắng', 22), (62, 'M', 'Trắng', 32), (62, 'L', 'Trắng', 26), (62, 'XL', 'Trắng', 18),
(62, 'M', 'Đen', 30), (62, 'L', 'Đen', 24);

-- Champion Powerblend Sweatshirt (ProductID = 63)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(63, 'S', 'Xanh Navy', 20), (63, 'M', 'Xanh Navy', 30), (63, 'L', 'Xanh Navy', 26), (63, 'XL', 'Xanh Navy', 16);

-- Champion Jersey Shorts (ProductID = 64)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(64, 'S', 'Đen', 24), (64, 'M', 'Đen', 34), (64, 'L', 'Đen', 28), (64, 'XL', 'Đen', 18),
(64, 'M', 'Xám', 30), (64, 'L', 'Xám', 26);

-- Champion Script Logo Joggers (ProductID = 65)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(65, 'S', 'Đen', 22), (65, 'M', 'Đen', 32), (65, 'L', 'Đen', 28), (65, 'XL', 'Đen', 18);

-- The North Face Nuptse Jacket (ProductID = 66)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(66, 'S', 'Đen', 12), (66, 'M', 'Đen', 18), (66, 'L', 'Đen', 16), (66, 'XL', 'Đen', 10),
(66, 'M', 'Đỏ', 14), (66, 'L', 'Đỏ', 12);

-- The North Face Denali Fleece (ProductID = 67)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(67, 'S', 'Xám', 16), (67, 'M', 'Xám', 24), (67, 'L', 'Xám', 20), (67, 'XL', 'Xám', 14),
(67, 'M', 'Đen', 22), (67, 'L', 'Đen', 18);

-- The North Face Essential Tee (ProductID = 68)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(68, 'S', 'Trắng', 24), (68, 'M', 'Trắng', 34), (68, 'L', 'Trắng', 28), (68, 'XL', 'Trắng', 20),
(68, 'M', 'Đen', 32), (68, 'L', 'Đen', 26);

-- The North Face Explore Pant (ProductID = 69)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(69, 'S', 'Đen', 18), (69, 'M', 'Đen', 28), (69, 'L', 'Đen', 24), (69, 'XL', 'Đen', 16),
(69, 'M', 'Nâu', 24), (69, 'L', 'Nâu', 20);

-- The North Face Base Camp Backpack (ProductID = 70)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(70, 'M', 'Đen', 20), (70, 'L', 'Đen', 18),
(70, 'M', 'Đỏ', 16), (70, 'L', 'Đỏ', 14);

-- Columbia Newton Ridge Hiking (ProductID = 71)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(71, '39', 'Nâu', 14), (71, '40', 'Nâu', 18), (71, '41', 'Nâu', 22), (71, '42', 'Nâu', 20), (71, '43', 'Nâu', 14),
(71, '40', 'Đen', 16), (71, '41', 'Đen', 20), (71, '42', 'Đen', 16);

-- Columbia Silver Ridge Shirt (ProductID = 72)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(72, 'S', 'Xanh', 20), (72, 'M', 'Xanh', 30), (72, 'L', 'Xanh', 26), (72, 'XL', 'Xanh', 18),
(72, 'M', 'Trắng', 26), (72, 'L', 'Trắng', 22);

-- Columbia Omni-Heat Jacket (ProductID = 73)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(73, 'S', 'Đen', 14), (73, 'M', 'Đen', 22), (73, 'L', 'Đen', 18), (73, 'XL', 'Đen', 12),
(73, 'M', 'Xanh Navy', 18), (73, 'L', 'Xanh Navy', 14);

-- Columbia Cargo Pants (ProductID = 74)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(74, 'S', 'Nâu', 18), (74, 'M', 'Nâu', 28), (74, 'L', 'Nâu', 24), (74, 'XL', 'Nâu', 16),
(74, 'M', 'Đen', 26), (74, 'L', 'Đen', 22);

-- Nike LeBron 21 (ProductID = 75)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(75, '39', 'Tím/Vàng', 12), (75, '40', 'Tím/Vàng', 16), (75, '41', 'Tím/Vàng', 20), (75, '42', 'Tím/Vàng', 18), (75, '43', 'Tím/Vàng', 12),
(75, '40', 'Đen', 14), (75, '41', 'Đen', 18), (75, '42', 'Đen', 14);

-- Nike KD 16 (ProductID = 76)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(76, '39', 'Xanh/Cam', 12), (76, '40', 'Xanh/Cam', 16), (76, '41', 'Xanh/Cam', 20), (76, '42', 'Xanh/Cam', 16), (76, '43', 'Xanh/Cam', 12);

-- Nike Yoga Dri-FIT (ProductID = 77)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(77, 'S', 'Hồng', 24), (77, 'M', 'Hồng', 34), (77, 'L', 'Hồng', 28), (77, 'XL', 'Hồng', 18),
(77, 'M', 'Đen', 32), (77, 'L', 'Đen', 26);

-- Nike Zenvy Leggings (ProductID = 78)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(78, 'S', 'Đen', 22), (78, 'M', 'Đen', 32), (78, 'L', 'Đen', 28), (78, 'XL', 'Đen', 18),
(78, 'M', 'Xám', 28), (78, 'L', 'Xám', 24);

-- Nike Brasilia Backpack (ProductID = 79)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(79, 'M', 'Đen', 30), (79, 'L', 'Đen', 25),
(79, 'M', 'Xanh Navy', 26), (79, 'L', 'Xanh Navy', 22);

-- Adidas Harden Vol 7 (ProductID = 80)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(80, '39', 'Đen/Trắng', 12), (80, '40', 'Đen/Trắng', 16), (80, '41', 'Đen/Trắng', 20), (80, '42', 'Đen/Trắng', 16), (80, '43', 'Đen/Trắng', 12);

-- Adidas Dame 8 (ProductID = 81)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(81, '39', 'Xanh/Vàng', 12), (81, '40', 'Xanh/Vàng', 16), (81, '41', 'Xanh/Vàng', 20), (81, '42', 'Xanh/Vàng', 16), (81, '43', 'Xanh/Vàng', 12);

-- Adidas Yoga Flow Tank (ProductID = 82)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(82, 'S', 'Hồng', 22), (82, 'M', 'Hồng', 32), (82, 'L', 'Hồng', 26), (82, 'XL', 'Hồng', 18),
(82, 'M', 'Đen', 30), (82, 'L', 'Đen', 24);

-- Adidas 4DFWD Running (ProductID = 83)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(83, '39', 'Trắng', 10), (83, '40', 'Trắng', 14), (83, '41', 'Trắng', 18), (83, '42', 'Trắng', 14), (83, '43', 'Trắng', 10),
(83, '40', 'Đen', 12), (83, '41', 'Đen', 16), (83, '42', 'Đen', 12);

-- Adidas Classic Backpack (ProductID = 84)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(84, 'M', 'Đen', 28), (84, 'L', 'Đen', 24),
(84, 'M', 'Xanh Navy', 24), (84, 'L', 'Xanh Navy', 20);

-- Puma Clyde All-Pro (ProductID = 85)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(85, '39', 'Trắng/Đỏ', 12), (85, '40', 'Trắng/Đỏ', 16), (85, '41', 'Trắng/Đỏ', 20), (85, '42', 'Trắng/Đỏ', 16), (85, '43', 'Trắng/Đỏ', 12);

-- Puma MB.02 (ProductID = 86)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(86, '39', 'Xanh/Hồng', 12), (86, '40', 'Xanh/Hồng', 16), (86, '41', 'Xanh/Hồng', 20), (86, '42', 'Xanh/Hồng', 16), (86, '43', 'Xanh/Hồng', 12);

-- Puma Studio Yoga Top (ProductID = 87)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(87, 'S', 'Hồng', 22), (87, 'M', 'Hồng', 32), (87, 'L', 'Hồng', 26), (87, 'XL', 'Hồng', 18),
(87, 'M', 'Xanh', 28), (87, 'L', 'Xanh', 24);

-- Puma Evercat Backpack (ProductID = 88)
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
(88, 'M', 'Đen', 26), (88, 'L', 'Đen', 22),
(88, 'M', 'Xám', 22), (88, 'L', 'Xám', 18);

-- ============================================
-- SAMPLE ORDERS (Đơn hàng mẫu)
-- ============================================

-- Đơn hàng từ user đã đăng ký (UserID = 2) - Nike Air Max 270
INSERT INTO Orders (UserID, TotalAmount, Address, Status, Note) VALUES
(2, 7300000, '456 Customer Street, HCMC', 'delivered', 'Giao hàng nhanh trong giờ hành chính');

INSERT INTO OrderDetails (OrderID, ProductDetailID, Quantity, Price) VALUES
(1, 1, 1, 3500000),
(1, 16, 1, 2800000),
(1, 28, 2, 650000);

-- Đơn hàng từ khách không đăng ký (Guest Checkout)
INSERT INTO Orders (UserID, GuestName, GuestEmail, GuestPhone, TotalAmount, Address, Status, Note) VALUES
(NULL, 'Tran Thi B', 'tranthib@gmail.com', '0901234567', 5600000, '789 Guest Street, Danang', 'processing', 'Gọi trước khi giao'),
(NULL, 'Le Van C', 'levanc@yahoo.com', '0912345678', 3200000, '321 Another Street, Hanoi', 'pending', NULL);

INSERT INTO OrderDetails (OrderID, ProductDetailID, Quantity, Price) VALUES
(2, 20, 1, 2800000),
(2, 31, 2, 850000),
(2, 40, 2, 550000);

INSERT INTO OrderDetails (OrderID, ProductDetailID, Quantity, Price) VALUES
(3, 89, 2, 1500000),
(3, 28, 1, 650000);

-- Thêm giỏ hàng cho user đã đăng ký
INSERT INTO Cart (UserID) VALUES (2);

INSERT INTO CartItems (CartID, ProductDetailID, Quantity, Price) VALUES
(1, 20, 1, 2800000),
(1, 56, 1, 4800000);

-- Thêm review mẫu
INSERT INTO Reviews (UserID, ProductID, Content, Rating, Status) VALUES
(2, 1, 'Giày rất tốt, đi êm chân và thoáng khí!', 5, 'approved'),
(2, 2, 'Nike Air Force 1 chất lượng tuyệt vời, đúng như mô tả', 5, 'approved'),
(2, 52, 'Giày Fila rất đẹp và trendy, mình rất hài lòng', 4, 'approved');
