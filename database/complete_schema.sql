SET NAMES utf8mb4;

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
('Nike', 'Thương hiệu thể thao hàng đầu thế giới, tiên phong trong đổi mới công nghệ và truyền cảm hứng cho mọi vận động viên.'), 
('Adidas', 'Biểu tượng thể thao toàn cầu đến từ Đức, nổi tiếng với công nghệ đế Boost êm ái và thiết kế ba sọc kinh điển.'), 
('Puma', 'Thương hiệu thể thao Đức năng động, kết hợp hoàn hảo giữa hiệu suất tốc độ và phong cách thời trang đường phố.'), 
('Yonex', 'Thương hiệu Nhật Bản thống trị thế giới cầu lông với công nghệ Carbon tiên tiến và độ chính xác tuyệt đối.'), 
('Lining', 'Thương hiệu thể thao quốc tế cao cấp, mang đến các sản phẩm chất lượng với thiết kế thời thượng và giá thành hợp lý.'), 
('Mizuno', 'Thương hiệu Nhật Bản lâu đời, cam kết chất lượng hoàn hảo và sự tỉ mỉ trong từng sản phẩm chạy bộ và bóng đá.'), 
('Under Armour', 'Tiên phong trong trang phục hiệu suất cao, nổi tiếng với công nghệ vải co giãn và thấm hút mồ hôi vượt trội.'), 
('Wilson', 'Nhà sản xuất dụng cụ thể thao hàng đầu của Mỹ, chuyên về vợt Tennis và các loại bóng thi đấu chuyên nghiệp.'), 
('Molten', 'Thương hiệu Nhật Bản chuyên cung cấp bóng thi đấu chính thức (Official Game Ball) cho các giải đấu quốc tế lớn như FIBA.'), 
('Coolmate', 'Thương hiệu thời trang nam Việt Nam ứng dụng công nghệ, mang lại sự thoải mái tối đa và trải nghiệm mua sắm tiện lợi.');

-- ============================================
-- 2. CATEGORIES
-- ============================================
INSERT INTO Categories (CategoryName, CategoryDescription) VALUES
('Bóng Đá', 'Thỏa mãn đam mê túc cầu với đầy đủ trang thiết bị từ giày đá bóng sân cỏ nhân tạo/tự nhiên, quần áo thi đấu CLB mùa giải mới, găng tay thủ môn đến các phụ kiện hỗ trợ tập luyện.'), 
('Cầu Lông', 'Tổng hợp dụng cụ cầu lông chính hãng bao gồm vợt trợ lực công nghệ cao, giày chuyên dụng bám sân, cầu thi đấu tiêu chuẩn và các phụ kiện như bao vợt, quấn cán.'), 
('Gym & Fitness', 'Kiến tạo vóc dáng hoàn hảo với hệ thống dụng cụ tập thể hình đa dạng, thảm Yoga định tuyến, tạ tay các loại và trang phục tập luyện co giãn, thấm hút mồ hôi tối ưu.'), 
('Chạy Bộ (Running)', 'Đồng hành trên mọi cung đường với các dòng giày chạy bộ công nghệ đệm êm ái, trang phục thoáng khí siêu nhẹ và các phụ kiện bó cơ hỗ trợ tăng thành tích marathon.'), 
('Bóng Rổ', 'Thế giới của các Baller với những đôi giày bóng rổ hiệu suất cao bảo vệ cổ chân, bóng thi đấu tiêu chuẩn FIBA và những set đồ Jersey đậm chất văn hóa bóng rổ.'), 
('Quần Vợt (Tennis)', 'Cung cấp trang thiết bị Quần Vợt đẳng cấp bao gồm vợt trợ lực kiểm soát bóng, giày tennis đế bền chống mài mòn và các phụ kiện thi đấu chuyên nghiệp.'), 
('Phụ Kiện & Bảo Hộ', 'Tối ưu hóa hiệu suất và bảo vệ cơ thể với các phụ kiện thiết yếu như bình nước thể thao, túi xách đa năng, băng bó chấn thương và các sản phẩm hỗ trợ phục hồi.');


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
-- 4. PRODUCTS
-- ============================================
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
-- --- BÓNG ĐÁ (1-20) ---
('Giày Nike Mercurial Vapor 15 Elite', 2800000, 1, 1, 'Siêu phẩm giày đá bóng sân cỏ nhân tạo với bộ đệm Air Zoom đột phá giúp bứt tốc ngoạn mục. Upper Vaporposite+ ôm sát bàn chân, mang lại cảm giác bóng chân thực nhất.', 'img/foot_1.jpg'),
('Giày Adidas Predator Accuracy .3', 1900000, 1, 2, 'Vũ khí kiểm soát bóng tối thượng. Công nghệ High Definition Grip với các vân cao su 3D dập nổi giúp tăng độ xoáy và độ chính xác trong từng đường chuyền.', 'img/foot_2.jpg'),
('Giày Puma Future Ultimate', 2200000, 1, 3, 'Thiết kế đột phá với dải thun Fuzionfit360 ôm trọn bàn chân, cho phép thi đấu linh hoạt dù có buộc dây hay không. Đế Dynamic Motion System hỗ trợ xoay trở cực nhanh.', 'img/foot_3.jpg'),
('Giày Mizuno Morelia Neo 3 Pro', 2600000, 1, 6, 'Đẳng cấp da Kangaroo thật siêu mềm, mang lại cảm giác "đi như không đi". Form giày thiết kế chuẩn cho bàn chân người châu Á, hỗ trợ tối đa cảm giác bóng.', 'img/foot_4.jpg'),
('Giày Nike Phantom GX Academy', 1850000, 1, 1, 'Mở rộng vùng tiếp xúc bóng nhờ hệ thống dây lệch, kết hợp lớp phủ NikeSkin giúp thực hiện những cú sút xoáy hiểm hóc và kiểm soát trận đấu dễ dàng.', 'img/foot_5.jpg'),
('Bóng Động Lực UHV 2.07', 550000, 1, 5, 'Bóng thi đấu chính thức tại V-League, đạt chuẩn FIFA Quality Pro. Vỏ bóng da PU cao cấp chống thấm nước, độ nảy ổn định và quỹ đạo bay chính xác.', 'img/foot_6.jpg'),
('Bóng Adidas Champions League', 850000, 1, 2, 'Phiên bản bóng thi đấu cúp C1 Châu Âu. Cấu trúc liên kết nhiệt không đường may giúp bề mặt bóng mượt mà, hạn chế thấm nước và giữ hơi cực tốt.', 'img/foot_7.jpg'),
('Áo Đấu Man Utd Home 2024', 250000, 1, 2, 'Áo đấu sân nhà mùa giải mới với họa tiết hoa hồng Lancashire chìm tinh tế. Chất vải thun lạnh Mè Thái cao cấp, thoáng khí và thấm hút mồ hôi cực nhanh.', 'img/foot_8.jpg'),
('Áo Đấu Real Madrid Home 2024', 250000, 1, 2, 'Thiết kế hoàng gia với màu trắng kinh điển phối viền vàng sang trọng. Logo thêu sắc nét, chất liệu vải co giãn 4 chiều hỗ trợ vận động cường độ cao.', 'img/foot_9.jpg'),
('Áo Đấu Tuyển Việt Nam 2024', 350000, 1, 5, 'Sản phẩm chính hãng Grand Sport với niềm tự hào cờ đỏ sao vàng. Công nghệ vải siêu nhẹ, thoát nhiệt tốt, phù hợp với khí hậu nóng ẩm tại Việt Nam.', 'img/foot_10.jpg'),
('Bộ Quần Áo Bóng Đá Không Logo CP', 150000, 1, 10, 'Giải pháp hoàn hảo cho đội bóng phủi. Chất thun lạnh thể thao bền bỉ, không bai dão, dễ dàng in ấn tên số và logo đội bóng theo yêu cầu.', 'img/foot_11.jpg'),
('Găng Tay Thủ Môn Adidas Predator', 1200000, 1, 2, 'Găng tay thủ môn chuyên nghiệp với mút URG 2.0 dính bóng như nam châm. Mặt lưng có gai cao su Demonskin hỗ trợ đấm bóng phá vây hiệu quả.', 'img/foot_12.jpg'),
('Găng Tay Nike Vapor Grip 3', 1500000, 1, 1, 'Công nghệ ACC (All Conditions Control) giúp bắt dính trong mọi điều kiện thời tiết. Thiết kế Grip3 ôm ngón cái, trỏ và út giúp tăng diện tích tiếp xúc bóng.', 'img/foot_13.jpg'),
('Tất Chống Trượt Aolikes', 50000, 1, 10, 'Hệ thống hạt cao su dưới lòng bàn chân giúp tăng ma sát tuyệt đối với lót giày, loại bỏ tình trạng trượt chân trong giày, hạn chế phồng rộp.', 'img/foot_14.jpg'),
('Bó Gối Đá Bóng Dài', 120000, 1, 10, 'Bảo vệ toàn diện khớp gối và dây chằng, giảm thiểu nguy cơ chấn thương khi va chạm. Chất liệu thun co giãn, thoáng khí, không gây hầm bí.', 'img/foot_15.jpg'),
('Giày Kamito QH19', 650000, 1, 5, 'Bộ sưu tập độc quyền của cầu thủ Quang Hải. Đinh dăm bền bỉ bám sân, da KA-FIBER siêu bền, phù hợp với mặt sân cỏ nhân tạo tại Việt Nam.', 'img/foot_16.jpg'),
('Áo Bib Tập Luyện', 30000, 1, 10, 'Áo lưới chia đội màu sắc nổi bật, trọng lượng siêu nhẹ, thoáng mát, là phụ kiện không thể thiếu cho các buổi tập chiến thuật.', 'img/foot_17.jpg'),
('Quần Short Đá Banh Nike', 350000, 1, 1, 'Quần đùi tập luyện sử dụng công nghệ Dri-FIT đẩy mồ hôi ra bề mặt vải để bay hơi nhanh hơn. Có túi khóa kéo tiện lợi để đựng đồ cá nhân.', 'img/foot_18.jpg'),
('Áo Giữ Nhiệt Body Đá Bóng', 150000, 1, 10, 'Lớp "da thứ hai" giúp giữ ấm cơ thể trong mùa đông, đồng thời bó cơ nhẹ giúp giảm rung chấn và trầy xước khi té ngã trên sân.', 'img/foot_19.jpg'),
('Túi Đựng Giày 2 Ngăn', 120000, 1, 2, 'Thiết kế thông minh với 2 ngăn riêng biệt: ngăn chính đựng giày và ngăn phụ đựng quần áo, ví, điện thoại. Chất liệu vải dù chống thấm nước.', 'img/foot_20.jpg'),

-- --- CẦU LÔNG (21-35) ---
('Vợt Yonex Astrox 77 Pro', 3200000, 2, 4, 'Cây vợt thiên công mạnh mẽ được tin dùng bởi các VĐV hàng đầu. Hệ thống Rotational Generator System giúp vợt cân bằng, phục hồi nhanh sau mỗi cú đập.', 'img/bad_1.jpg'),
('Vợt Yonex Nanoflare 800', 3500000, 2, 4, 'Dòng vợt nhẹ đầu siêu tốc độ, khung vợt Razor Frame siêu mỏng giúp cắt gió, mang lại những cú phản tạt và phòng thủ nhanh đến chóng mặt.', 'img/bad_2.jpg'),
('Vợt Lining Axforce 80', 3100000, 2, 5, 'Siêu phẩm tấn công của Lining với trục vợt siêu mỏng 6.6mm. Công nghệ Box Wing Frame giúp ổn định mặt vợt, cho những cú Smash uy lực.', 'img/bad_3.jpg'),
('Vợt Lining Halbertec 8000', 3600000, 2, 5, 'Cây vợt cân bằng hoàn hảo, kiểm soát cầu tối ưu. Thân vợt dẻo dai giúp trợ lực tốt cho người chơi phong trào, phù hợp lối đánh điều cầu.', 'img/bad_4.jpg'),
('Vợt Mizuno Fortius 10', 2800000, 2, 6, 'Khung vợt cứng cáp chịu được mức căng cao. Công nghệ Torque Technology T8 giúp truyền tải tối đa lực từ tay người chơi vào quả cầu.', 'img/bad_5.jpg'),
('Giày Yonex Eclipsion Z3', 2400000, 2, 4, 'Sở hữu đế Power Cushion+ độc quyền giúp hấp thụ chấn động và chuyển hóa thành năng lượng cho bước di chuyển tiếp theo. Ổn định cổ chân tuyệt vời.', 'img/bad_6.jpg'),
('Giày Lining Saga Pro', 1800000, 2, 5, 'Đế cao su Non-marking bám sân cực tốt, chống trơn trượt. Công nghệ Li-Ning Cloud ở đế giữa giúp giảm chấn êm ái cho những pha dậm nhảy.', 'img/bad_7.jpg'),
('Giày Mizuno Wave Claw 2', 2100000, 2, 6, 'Dòng giày siêu nhẹ chuyên dụng cho lối đánh tốc độ. Công nghệ Mizuno Wave giúp phân tán lực tác động, bảo vệ gót chân và đầu gối hiệu quả.', 'img/bad_8.jpg'),
('Áo Cầu Lông Yonex Thi Đấu', 450000, 2, 4, 'Công nghệ làm mát VeryCool Xylitol giúp giảm nhiệt độ cơ thể tới 3 độ C. Vải co giãn đa chiều hỗ trợ tối đa cho các động tác vung vợt.', 'img/bad_9.jpg'),
('Quần Cầu Lông Yonex', 320000, 2, 4, 'Thiết kế xẻ tà rộng rãi giúp di chuyển bước sải dài dễ dàng. Chất liệu vải mè kim cương thoáng khí, không bám dính vào da khi đổ mồ hôi.', 'img/bad_10.jpg'),
('Váy Cầu Lông Nữ Xếp Ly', 280000, 2, 5, 'Thời trang và năng động với thiết kế xếp ly bay bổng. Tích hợp quần lót bảo hộ co giãn bên trong giúp bạn tự tin trong mọi pha cứu cầu.', 'img/bad_11.jpg'),
('Ống Cầu Thành Công', 180000, 2, 10, 'Loại cầu lông được ưa chuộng nhất tại Việt Nam. Lông cầu dai, đường bay ổn định, ít bị gãy lông, phù hợp cho cả tập luyện và thi đấu phong trào.', 'img/bad_12.jpg'),
('Ống Cầu Yonex AS40', 650000, 2, 4, 'Cầu thi đấu tiêu chuẩn quốc tế BWF. Được làm từ lông ngỗng cao cấp loại 1, đảm bảo quỹ đạo bay chuẩn xác tuyệt đối và độ bền vượt trội.', 'img/bad_13.jpg'),
('Cuốn Cán Vợt Yonex (Hộp 3 cái)', 120000, 2, 4, 'Chất liệu cao su non tổng hợp bám tay, thấm hút mồ hôi tốt. Giúp cầm vợt chắc chắn, tránh trơn trượt trong những pha đập cầu mạnh.', 'img/bad_14.jpg'),
('Bao Vợt Cầu Lông 2 Ngăn', 450000, 2, 5, 'Có lớp lót cách nhiệt bảo vệ vợt khỏi nhiệt độ cao. Sức chứa lớn (4-6 vợt) cùng ngăn đựng giày và quần áo riêng biệt.', 'img/bad_15.jpg'),

-- --- GYM & FITNESS (36-55) ---
('Áo Tanktop Nam Under Armour', 450000, 3, 7, 'Thuộc bộ sưu tập Project Rock của The Rock. Thiết kế khoét nách sâu khoe trọn cơ bắp, chất vải Cotton-Poly mềm mại và thấm hút mồ hôi.', 'img/gym_1.jpg'),
('Áo Thun Gym Shark Body', 350000, 3, 10, 'Form Slimfit ôm sát tôn lên đường nét cơ thể vạm vỡ. Chất liệu thun lạnh 4 chiều co giãn cực tốt, hỗ trợ tối đa các bài tập Upper Body.', 'img/gym_2.jpg'),
('Quần Jogger Tập Gym Nam', 320000, 3, 10, 'Phong cách Athleisure vừa tập gym vừa đi chơi. Vải nỉ da cá dày dặn nhưng thoáng khí, bo gấu gọn gàng, tôn dáng chân.', 'img/gym_3.jpg'),
('Quần Short Gym 2 Lớp', 250000, 3, 1, 'Thiết kế 2 trong 1 với lớp legging bó cơ bên trong giúp giữ ấm cơ đùi và ngăn ngừa ma sát, lớp ngoài xẻ tà giúp Squat thoải mái.', 'img/gym_4.jpg'),
('Áo Bra Nike Swoosh', 650000, 3, 1, 'Hỗ trợ nâng đỡ mức độ vừa (Medium Support), phù hợp cho Gym và Yoga. Đệm mút liền mạch thoáng khí, đai lưng chắc chắn không gây hằn da.', 'img/gym_5.jpg'),
('Quần Legging Lululemon (Rep)', 450000, 3, 10, 'Chất vải bơ (Buttery soft) mềm mịn như làn da thứ hai. Lưng quần cạp cao giúp gen bụng, nâng mông và định hình vóc dáng hoàn hảo.', 'img/gym_6.jpg'),
('Áo Croptop Tập Gym Nữ', 220000, 3, 10, 'Thiết kế thời thượng khoe eo thon. Chất liệu thun tăm co giãn, thấm hút mồ hôi nhanh chóng, giúp bạn luôn khô thoáng trong buổi tập.', 'img/gym_7.jpg'),
('Găng Tay Tập Gym Có Cuốn', 150000, 3, 10, 'Bảo vệ lòng bàn tay khỏi chai sạn. Tích hợp đai cuốn cổ tay dài giúp cố định khớp cổ tay, hỗ trợ đẩy tạ nặng an toàn hơn.', 'img/gym_8.jpg'),
('Đai Lưng Cứng Valeo', 250000, 3, 10, 'Phụ kiện bắt buộc cho các bài Squat và Deadlift nặng. Giúp nén ổ bụng, bảo vệ cột sống thắt lưng và ngăn ngừa chấn thương thoát vị.', 'img/gym_9.jpg'),
('Thảm Tập Yoga TPE 8mm', 180000, 3, 10, 'Chất liệu TPE đúc nguyên khối thân thiện môi trường. Độ dày 8mm êm ái bảo vệ đầu gối và khủy tay. Bề mặt chống trượt 2 mặt an toàn.', 'img/gym_10.jpg'),
('Bóng Yoga Chống Nổ', 120000, 3, 10, 'Chịu lực lên đến 200kg, công nghệ chống nổ (xì hơi từ từ khi bị thủng). Hỗ trợ các bài tập thăng bằng, cơ bụng và phục hồi chức năng.', 'img/gym_11.jpg'),
('Bộ Dây Kháng Lực Miniband', 80000, 3, 10, 'Set gồm 5 dây với 5 mức kháng lực từ nhẹ đến siêu nặng. Phụ kiện nhỏ gọn nhưng hiệu quả tuyệt vời để tập mông đùi tại nhà.', 'img/gym_12.jpg'),
('Tạ Tay Bọc Nhựa 5kg', 60000, 3, 10, 'Lõi bê tông đặc, bọc nhựa ABS cao cấp chống va đập, không làm trầy xước sàn nhà. Thiết kế tay cầm vừa vặn, chống trơn trượt.', 'img/gym_13.jpg'),
('Con Lăn Tập Bụng', 150000, 3, 10, 'Bánh xe to, vận hành êm ái. Tích hợp lò xo trợ lực giúp hồi vị dễ dàng, tác động sâu vào cơ bụng 6 múi và cơ liên sườn.', 'img/gym_14.jpg'),
('Dây Nhảy Thể Lực Cáp', 50000, 3, 10, 'Dây cáp bọc nhựa siêu bền, trục bi xoay 360 độ giúp dây quay tốc độ cao mà không bị rối. Bài tập Cardio đốt mỡ hiệu quả nhất.', 'img/gym_15.jpg'),
('Bình Lắc Whey 700ml', 80000, 3, 10, 'Nhựa PP an toàn sức khỏe (BPA Free). Có quả cầu lò xo inox giúp đánh tan bột Whey/Mass nhanh chóng mà không bị vón cục.', 'img/gym_16.jpg'),
('Áo Stringer Gold Gym', 150000, 3, 10, 'Biểu tượng của thể hình cổ điển. Áo 3 lỗ dây nhỏ giúp khoe trọn cơ vai và lưng xô, tạo động lực tập luyện mạnh mẽ.', 'img/gym_17.jpg'),
('Quần Biker Short Nữ', 200000, 3, 10, 'Xu hướng thời trang phòng tập mới. Độ dài ngang đùi năng động, ôm sát cơ thể, phù hợp cho cả tập luyện lẫn dạo phố.', 'img/gym_18.jpg'),
('Băng Đô Headband', 40000, 3, 1, 'Ngăn mồ hôi trán chảy xuống mắt gây cay mắt. Chất liệu Cotton thấm hút cực tốt, co giãn vừa vặn mọi kích cỡ đầu.', 'img/gym_19.jpg'),
('Túi Trống Gym Có Ngăn Giày', 280000, 3, 2, 'Thiết kế hình trụ năng động, dung tích lớn 25L. Có ngăn đựng giày riêng biệt có lỗ thoáng khí, ngăn mùi khó chịu ám vào quần áo.', 'img/gym_20.jpg'),

-- --- CHẠY BỘ (56-70) ---
('Giày Nike Air Zoom Pegasus 40', 3100000, 4, 1, 'Đôi giày chạy bộ quốc dân phiên bản thứ 40. Đệm Nike React kết hợp 2 túi Zoom Air mang lại độ nảy và êm ái tuyệt vời cho việc chạy hàng ngày.', 'img/run_1.jpg'),
('Giày Adidas Ultraboost Light', 3800000, 4, 2, 'Công nghệ Boost nhẹ hơn 30% so với bản cũ, hoàn trả năng lượng tối đa. Thân giày Primeknit ôm chân như một chiếc tất.', 'img/run_2.jpg'),
('Giày Nike Zoom Fly 5', 3500000, 4, 1, 'Được trang bị đĩa Carbon Flyplate giúp đẩy người về phía trước, phù hợp cho các bài chạy Tempo tốc độ cao và cự ly dài.', 'img/run_3.jpg'),
('Giày Mizuno Wave Rider 27', 2900000, 4, 6, 'Sự kết hợp giữa đệm Mizuno Enerzy êm ái và tấm Wave Plate ổn định. Đôi giày bền bỉ, đáng tin cậy cho mọi cự ly marathon.', 'img/run_4.jpg'),
('Giày Adidas Adizero Boston 12', 3200000, 4, 2, 'Sử dụng thanh năng lượng EnergyRods 2.0 bằng sợi thủy tinh giúp tạo đà mạnh mẽ. Đế ngoài Continental bám đường cực tốt.', 'img/run_5.jpg'),
('Áo Singlet Chạy Bộ Nike', 450000, 4, 1, 'Áo ba lỗ siêu nhẹ (Ultra-lightweight), đục lỗ laser thoáng khí toàn thân, giảm thiểu ma sát lên da khi chạy đường dài.', 'img/run_6.jpg'),
('Quần Short Chạy Bộ Xẻ Tà', 250000, 4, 10, 'Thiết kế xẻ tà cao tối đa hóa phạm vi chuyển động của chân. Tích hợp quần lót tam giác bên trong và túi nhỏ đựng chìa khóa.', 'img/run_7.jpg'),
('Áo Khoác Gió Chạy Bộ', 350000, 4, 10, 'Chất liệu dù siêu mỏng nhẹ, trượt nước (Water Repellent) và chắn gió tốt. Có thể gấp gọn trong lòng bàn tay, phù hợp chạy sáng sớm.', 'img/run_8.jpg'),
('Belt Chạy Bộ Đựng Điện Thoại', 80000, 4, 10, 'Đai đeo hông ôm sát cơ thể, không rung lắc khi chạy. Đựng vừa điện thoại màn hình lớn, chìa khóa, gel năng lượng.', 'img/run_9.jpg'),
('Mũ Nửa Đầu Visor', 120000, 4, 10, 'Thiết kế hở đầu giúp thoát nhiệt đỉnh đầu nhanh chóng. Vành mũ rộng che nắng hiệu quả, đai thấm mồ hôi trán mềm mại.', 'img/run_10.jpg'),
('Tất Chạy Bộ Xỏ Ngón', 60000, 4, 10, 'Tách riêng 5 ngón chân giúp ngăn ngừa ma sát giữa các ngón, loại bỏ hoàn toàn nguy cơ phồng rộp (blister) khi chạy marathon.', 'img/run_11.jpg'),
('Bó Bắp Chân Calf Compress', 100000, 4, 10, 'Công nghệ nén ép (Compression) giúp tăng cường lưu thông máu, giảm rung lắc cơ bắp chân, hạn chế chuột rút và mỏi cơ.', 'img/run_12.jpg'),
('Giày Chạy Bộ Coolmate', 690000, 4, 10, 'Sản phẩm chạy bộ giá tốt cho người mới bắt đầu. Đế Phylon nhẹ và êm, thân giày vải dệt thoáng khí, thiết kế tối giản dễ phối đồ.', 'img/run_13.jpg'),
('Áo Thun Chạy Bộ Coolmate', 190000, 4, 10, 'Sử dụng công nghệ Excool độc quyền thấm hút mồ hôi và khô nhanh gấp 2 lần Cotton. Mềm mại, mát lạnh, chống tia UV.', 'img/run_14.jpg'),
('Quần Legging Chạy Bộ Nam', 250000, 4, 10, 'Giữ ấm cơ bắp khi chạy mùa đông. Có túi bên hông tiện lợi đựng điện thoại. Chất vải co giãn 4 chiều hỗ trợ vận động.', 'img/run_15.jpg'),

-- --- BÓNG RỔ (71-85) ---
('Giày Nike LeBron 21', 4500000, 5, 1, 'Giày thửa riêng cho "King James". Hệ thống dây cáp 360 độ giữ chân chắc chắn, đệm Zoom Turbo đàn hồi cực tốt cho những pha tiếp đất nặng.', 'img/bball_1.jpg'),
('Giày Nike KD 16', 3800000, 5, 1, 'Nhẹ hơn và thoáng hơn. Bộ đệm Air Zoom Strobel full-length mang lại cảm giác êm ái tức thì ngay khi xỏ chân vào.', 'img/bball_2.jpg'),
('Giày Under Armour Curry 11', 4200000, 5, 7, 'Sử dụng đế UA Flow loại bỏ hoàn toàn cao su, mang lại độ bám sàn "kinh khủng" và trọng lượng siêu nhẹ cho những cú ném 3 điểm.', 'img/bball_3.jpg'),
('Giày Adidas Harden Vol 7', 3600000, 5, 2, 'Thiết kế lấy cảm hứng từ áo khoác phao độc đáo. Đệm lai giữa Boost và Lightstrike vừa êm ái vừa phản hồi nhanh cho lối đánh Eurostep.', 'img/bball_4.jpg'),
('Giày Lining Way of Wade', 3200000, 5, 5, 'Dòng giày cao cấp nhất của Lining. Công nghệ Boom (Pebax) siêu nảy, tấm Carbon chống xoắn bàn chân, thiết kế cực kỳ hầm hố.', 'img/bball_5.jpg'),
('Bóng Rổ Molten BG4500', 1200000, 5, 9, 'Bóng thi đấu chính thức của FIBA. Da PU cao cấp cho độ bám dính tuyệt vời ngay cả khi tay ra mồ hôi, độ nảy chuẩn xác.', 'img/bball_6.jpg'),
('Bóng Rổ Spalding TF-250', 550000, 5, 9, 'Lựa chọn số 1 cho sân Outdoor (bê tông). Vỏ bóng Composite bền bỉ chịu mài mòn tốt, rãnh bóng sâu giúp kiểm soát bóng dễ dàng.', 'img/bball_7.jpg'),
('Áo Jersey Lakers Lebron', 350000, 5, 1, 'Áo đấu Swingman chất lượng cao, logo và tên số được ép nhiệt chắc chắn. Vải lưới Mesh thoáng khí, mang lại phong cách NBA chuyên nghiệp.', 'img/bball_8.jpg'),
('Áo Jersey Warriors Curry', 350000, 5, 7, 'Áo đấu của tay ném vĩ đại nhất lịch sử Stephen Curry. Màu xanh hoàng gia nổi bật, form áo rộng rãi thoải mái chuẩn bóng rổ.', 'img/bball_9.jpg'),
('Quần Bóng Rổ Form Rộng', 220000, 5, 10, 'Form quần dài quá gối (Over-knee) đặc trưng của văn hóa bóng rổ đường phố. Ống quần rộng rãi giúp thực hiện các pha Cross-over dễ dàng.', 'img/bball_10.jpg'),
('Tất Bóng Rổ Cổ Cao', 80000, 5, 1, 'Dày gấp đôi tất thường tại gót và mũi chân để giảm chấn. Cổ cao ôm mắt cá giúp hạn chế lật cổ chân nhẹ.', 'img/bball_11.jpg'),
('Băng Đầu Gối Hex McDavid', 250000, 5, 10, 'Công nghệ đệm lục giác Hex bảo vệ đầu gối khỏi va đập khi té ngã xuống sàn cứng. Được tin dùng bởi hầu hết các cầu thủ NBA.', 'img/bball_12.jpg'),
('Tay Ném Bóng Rổ (Sleeve)', 90000, 5, 10, 'Giữ ấm cánh tay ném, duy trì cảm giác bóng. Đồng thời bảo vệ da khỏi trầy xước và hỗ trợ lưu thông máu.', 'img/bball_13.jpg'),
('Balo Bóng Rổ Cỡ Lớn', 550000, 5, 1, 'Dung tích khủng, có ngăn lưới riêng biệt bên ngoài để đựng bóng size 7 hoặc giày bẩn, không làm bẩn đồ bên trong.', 'img/bball_14.jpg'),
('Lưới Bóng Rổ Phát Sáng', 120000, 5, 10, 'Lưới dạ quang hấp thụ ánh sáng và phát sáng trong đêm, giúp bạn chơi bóng rổ buổi tối thêm phần thú vị và chính xác.', 'img/bball_15.jpg'),

-- --- TENNIS (86-95) ---
('Vợt Wilson Pro Staff v14', 4800000, 6, 8, 'Huyền thoại trở lại với phiên bản v14. Khung vợt ổn định, mang lại cảm giác đánh bóng cổ điển và độ chính xác tuyệt đối như Roger Federer.', 'img/ten_1.jpg'),
('Vợt Babolat Pure Aero', 4500000, 6, 8, 'Cỗ máy tạo xoáy (Spin Machine) của Rafael Nadal. Khung vợt khí động học giúp tăng tốc độ đầu vợt, tạo ra những cú Topspin cắm sân.', 'img/ten_2.jpg'),
('Vợt Head Speed Pro', 4200000, 6, 8, 'Sự lựa chọn của Novak Djokovic. Cây vợt cân bằng hoàn hảo giữa tốc độ và khả năng kiểm soát, phù hợp với lối đánh đôi công cuối sân.', 'img/ten_3.jpg'),
('Giày Tennis Asics Gel-Resolution 9', 3200000, 6, 8, 'Đôi giày ổn định nhất thị trường. Công nghệ Dynawall giúp khóa chặt bàn chân trong các pha di chuyển ngang (Slide) cứu bóng.', 'img/ten_4.jpg'),
('Giày Tennis Adidas Barricade', 2800000, 6, 2, 'Biểu tượng của sự bền bỉ. Đế ngoài Adiwear siêu chống mài mòn, bảo hành đế 6 tháng. Hỗ trợ cổ chân tuyệt vời.', 'img/ten_5.jpg'),
('Bóng Tennis Wilson (Lon 4 quả)', 150000, 6, 8, 'Bóng thi đấu chính thức tại giải Australian Open. Lớp nỉ Optivis giúp bóng dễ nhìn hơn, độ nảy bền bỉ trên mặt sân cứng.', 'img/ten_6.jpg'),
('Áo Polo Tennis NikeCourt', 550000, 6, 1, 'Phong cách lịch lãm quý tộc. Cổ áo bẻ gập gọn gàng, đường may vai lùi về sau giúp vung vợt giao bóng thoải mái không bị kích.', 'img/ten_7.jpg'),
('Váy Tennis Adidas Club', 450000, 6, 2, 'Vải công nghệ AEROREADY thấm hút mồ hôi. Thiết kế cạp bản rộng tôn dáng, xếp ly xòe nhẹ tạo sự nữ tính trong từng bước chạy.', 'img/ten_8.jpg'),
('Mũ Lưỡi Trai Tennis', 250000, 6, 1, 'Mặt dưới lưỡi trai màu đen giúp chống lóa mắt khi nhìn lên trời giao bóng. Công nghệ Dri-FIT giữ đầu luôn khô thoáng.', 'img/ten_9.jpg'),
('Băng Chặn Mồ Hôi Cổ Tay', 80000, 6, 1, 'Phụ kiện nhỏ nhưng quan trọng, ngăn mồ hôi tay chảy xuống lòng bàn tay gây trơn cán vợt. Chất liệu Cotton dày dặn thấm hút tốt.', 'img/ten_10.jpg'),

-- --- PHỤ KIỆN CHUNG (96-100) ---
('Bình Nước Thể Thao 1.5L', 120000, 7, 10, 'Dung tích lớn 1.5L đảm bảo đủ nước cho cả buổi tập dài. Nhựa Tritan cao cấp chịu va đập, không chứa BPA gây hại sức khỏe.', 'img/acc_1.jpg'),
('Khăn Lạnh Thể Thao', 60000, 7, 10, 'Công nghệ làm mát tức thì: chỉ cần nhúng nước, vắt khô và phẩy nhẹ là khăn sẽ giảm nhiệt độ sâu, giúp hạ nhiệt cơ thể nhanh chóng.', 'img/acc_2.jpg'),
('Túi Rút Đựng Đồ', 50000, 7, 2, 'Nhỏ gọn, tiện lợi để đựng giày, quần áo bẩn hoặc đồ cá nhân lặt vặt. Dây rút chắc chắn, có thể đeo như balo nhẹ.', 'img/acc_3.jpg'),
('Xịt Giảm Đau Thể Thao', 180000, 7, 10, 'Dạng xịt lạnh giúp đóng băng cảm giác đau tức thì, giảm sưng tấy cho các chấn thương phần mềm như bong gân, bầm tím.', 'img/acc_4.jpg'),
('Kính Mát Thể Thao Chống UV', 250000, 7, 10, 'Thiết kế ôm sát khuôn mặt không bị rơi khi vận động mạnh. Tròng kính phân cực chống tia UV400 bảo vệ mắt khi chạy bộ dưới nắng gắt.', 'img/acc_5.jpg');


-- ============================================
-- PRODUCT DETAILS (Size và Màu sắc)
-- ===========================================
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
-- 1. BÓNG ĐÁ (ID 1-20)
-- =======================================================
-- Giày (1-5, 16)
(1, '39', 'Xanh Ngọc', 20), (1, '40', 'Xanh Ngọc', 30), (1, '41', 'Xanh Ngọc', 30), (1, '42', 'Xanh Ngọc', 20),
(1, '40', 'Trắng/Vàng', 25), (1, '41', 'Trắng/Vàng', 25),
(2, '39', 'Đen/Đỏ', 15), (2, '40', 'Đen/Đỏ', 25), (2, '41', 'Đen/Đỏ', 30), (2, '42', 'Đen/Đỏ', 20),
(3, '40', 'Cam Neon', 20), (3, '41', 'Cam Neon', 25), (3, '42', 'Cam Neon', 20),
(4, '40', 'Đen/Vàng', 15), (4, '41', 'Đen/Vàng', 20), (4, '42', 'Đen/Vàng', 15),
(5, '40', 'Tím/Đen', 20), (5, '41', 'Tím/Đen', 25), (5, '42', 'Tím/Đen', 20),
(16, '39', 'Đỏ', 20), (16, '40', 'Đỏ', 30), (16, '41', 'Đỏ', 30), (16, '42', 'Đỏ', 20),

-- Bóng (6, 7)
(6, 'Size 5', 'Trắng/Xanh', 50), (6, 'Size 5', 'Trắng/Đỏ', 30),
(7, 'Size 5', 'Sao Vàng', 40),

-- Quần Áo (8-11, 17-19)
(8, 'S', 'Đỏ', 30), (8, 'M', 'Đỏ', 50), (8, 'L', 'Đỏ', 50), (8, 'XL', 'Đỏ', 30),
(9, 'S', 'Trắng', 30), (9, 'M', 'Trắng', 50), (9, 'L', 'Trắng', 50), (9, 'XL', 'Trắng', 30),
(10, 'S', 'Đỏ', 40), (10, 'M', 'Đỏ', 60), (10, 'L', 'Đỏ', 60), (10, 'XL', 'Đỏ', 40),
(11, 'M', 'Xanh Dương', 50), (11, 'L', 'Xanh Dương', 50), (11, 'M', 'Vàng', 50), (11, 'L', 'Vàng', 50),
(17, 'Freesize', 'Vàng', 100), (17, 'Freesize', 'Cam', 100),
(18, 'M', 'Đen', 40), (18, 'L', 'Đen', 40), (18, 'XL', 'Đen', 20),
(19, 'M', 'Đen', 50), (19, 'L', 'Đen', 50),

-- Phụ kiện Bóng đá (12-15, 20)
(12, 'Size 8', 'Đỏ', 15), (12, 'Size 9', 'Đỏ', 15),
(13, 'Size 9', 'Trắng', 15), (13, 'Size 10', 'Trắng', 10),
(14, 'Freesize', 'Trắng', 100), (14, 'Freesize', 'Đen', 100),
(15, 'M', 'Đen', 30), (15, 'L', 'Đen', 30), -- Bó gối
(20, 'Freesize', 'Đen', 50), -- Túi giày

-- =======================================================
-- 2. CẦU LÔNG (ID 21-35)
-- =======================================================
-- Vợt (21-25)
(21, '4U', 'Vàng Shine', 20), (21, '3U', 'Vàng Shine', 15),
(22, '4U', 'Đen Nhám', 25), (22, '5U', 'Đen Nhám', 15),
(23, '4U', 'Trắng', 20), (23, '3U', 'Trắng', 10), -- Lining Axforce (Bổ sung)
(24, '4U', 'Xanh/Đen', 20), -- Halbertec (Bổ sung)
(25, '4U', 'Đen', 20), -- Mizuno Fortius (Bổ sung)

-- Giày (26-28)
(26, '40', 'Trắng/Hồng', 15), (26, '41', 'Trắng/Hồng', 20),
(27, '40', 'Xanh/Cam', 20), (27, '41', 'Xanh/Cam', 20),
(28, '40', 'Trắng/Xanh', 20), (28, '41', 'Trắng/Xanh', 20),

-- Quần Áo (29-31)
(29, 'M', 'Xanh Navy', 30), (29, 'L', 'Xanh Navy', 30),
(30, 'M', 'Đen', 40), (30, 'L', 'Đen', 40),
(31, 'S', 'Trắng', 20), (31, 'M', 'Trắng', 20),

-- Phụ kiện Cầu lông (32-35)
(32, 'Tốc độ 76', 'Trắng', 50), (32, 'Tốc độ 77', 'Trắng', 50),
(33, 'Speed 77', 'Trắng', 100),
(34, 'Freesize', 'Đủ màu', 200), -- Cuốn cán (Bổ sung)
(35, 'Freesize', 'Đen', 20), (35, 'Freesize', 'Đỏ', 20), -- Bao vợt (Bổ sung)

-- =======================================================
-- 3. GYM & FITNESS (ID 36-55)
-- =======================================================
-- Quần Áo Gym (36-42, 52, 53)
(36, 'L', 'Xám', 30), (36, 'XL', 'Xám', 20),
(37, 'M', 'Đen', 40), (37, 'L', 'Đen', 40),
(38, 'M', 'Đen', 30), (38, 'L', 'Đen', 30),
(39, 'M', 'Xám Camo', 30), (39, 'L', 'Xám Camo', 30),
(40, 'S', 'Đen', 20), (40, 'M', 'Đen', 30),
(41, 'S', 'Tím', 20), (41, 'M', 'Tím', 30),
(42, 'S', 'Xanh', 20), (42, 'M', 'Xanh', 20),
(52, 'L', 'Vàng', 20), (52, 'XL', 'Vàng', 20),
(53, 'S', 'Đen', 20), (53, 'M', 'Đen', 20),

-- Dụng cụ & Phụ kiện Gym (43-51, 54, 55)
(43, 'M', 'Đen', 50), (43, 'L', 'Đen', 50), -- Găng tay (Bổ sung)
(44, 'M', 'Đen', 20), (44, 'L', 'Đen', 20), -- Đai lưng (Bổ sung)
(45, '8mm', 'Tím', 30), (45, '8mm', 'Xanh', 30),
(46, '65cm', 'Tím', 20), (46, '75cm', 'Xám', 20), -- Bóng Yoga (Bổ sung)
(47, 'Bộ', 'Ngũ sắc', 50),
(48, '5kg', 'Đỏ', 20), (48, '5kg', 'Xanh', 20),
(49, 'Freesize', 'Đen', 30), -- Con lăn (Bổ sung)
(50, 'Freesize', 'Đen', 50), -- Dây nhảy (Bổ sung)
(51, '700ml', 'Xanh', 50), -- Bình lắc (Bổ sung)
(54, 'Freesize', 'Trắng', 50), -- Băng đô (Bổ sung)
(55, 'Freesize', 'Đen', 30), -- Túi trống (Bổ sung)

-- =======================================================
-- 4. CHẠY BỘ (ID 56-70)
-- =======================================================
-- Giày (56-60, 68)
(56, '40', 'Đen/Trắng', 25), (56, '41', 'Đen/Trắng', 30), (56, '42', 'Đen/Trắng', 25),
(57, '40', 'Trắng Full', 20), (57, '41', 'Trắng Full', 20),
(58, '41', 'Cam Lửa', 15), (58, '42', 'Cam Lửa', 15),
(59, '41', 'Xanh', 20), (59, '42', 'Xanh', 20), -- Mizuno Rider (Bổ sung)
(60, '41', 'Đen', 20), (60, '42', 'Đen', 20), -- Adidas Boston (Bổ sung)
(68, '40', 'Xám', 30), (68, '41', 'Xám', 40),

-- Quần Áo & Phụ kiện Running (61-67, 69, 70)
(61, 'M', 'Xanh Chuối', 20), (61, 'L', 'Xanh Chuối', 20),
(62, 'M', 'Đen', 40), (62, 'L', 'Đen', 40),
(63, 'L', 'Cam', 20), (63, 'XL', 'Cam', 15),
(64, 'Freesize', 'Đen', 50), -- Belt (Bổ sung)
(65, 'Freesize', 'Trắng', 50), -- Mũ (Bổ sung)
(66, 'Freesize', 'Xám', 50), -- Tất xỏ ngón (Bổ sung)
(67, 'M', 'Đen', 30), -- Bó bắp chân (Bổ sung)
(69, 'M', 'Đen', 50), (69, 'L', 'Đen', 50),
(70, 'M', 'Đen', 30), (70, 'L', 'Đen', 30),

-- =======================================================
-- 5. BÓNG RỔ (ID 71-85)
-- =======================================================
-- Giày (71-75)
(71, '41', 'Tím/Vàng', 15), (71, '42', 'Tím/Vàng', 20),
(72, '41', 'Đỏ/Đen', 15), (72, '42', 'Đỏ/Đen', 20),
(73, '41', 'Trắng/Xanh', 15), (73, '42', 'Trắng/Xanh', 20),
(74, '41', 'Đen', 15), (74, '42', 'Đen', 20), -- Harden Vol 7 (Bổ sung)
(75, '41', 'Hồng', 15), (75, '42', 'Hồng', 20), -- Lining Wade (Bổ sung)

-- Bóng & Quần Áo (76-80)
(76, 'Size 7', 'Nâu/Vàng', 60),
(77, 'Size 7', 'Cam', 60),
(78, 'L', 'Vàng Lakers', 30), (78, 'XL', 'Vàng Lakers', 20),
(79, 'L', 'Xanh Warriors', 30), (79, 'XL', 'Xanh Warriors', 20),
(80, 'L', 'Đen', 40), (80, 'XL', 'Đen', 30),

-- Phụ kiện Bóng rổ (81-85)
(81, 'Freesize', 'Trắng', 50), -- Tất (Bổ sung)
(82, 'M', 'Đen', 30), -- Băng gối (Bổ sung)
(83, 'M', 'Đen', 30), -- Tay ném (Bổ sung)
(84, 'Freesize', 'Đen', 20), -- Balo (Bổ sung)
(85, 'Freesize', 'Xanh', 20), -- Lưới (Bổ sung)

-- =======================================================
-- 6. TENNIS (ID 86-95)
-- =======================================================
-- Vợt (86-88)
(86, '315g', 'Đen', 10), (86, '290g', 'Đen', 10),
(87, '300g', 'Vàng/Đen', 15),
(88, '300g', 'Trắng/Đen', 15),
-- Giày (89, 90)
(89, '40', 'Xanh Dương', 15), (89, '41', 'Xanh Dương', 20),
(90, '40', 'Trắng/Đỏ', 20), (90, '41', 'Trắng/Đỏ', 20),
-- Phụ kiện Tennis (91-95)
(91, 'Lon', 'Vàng', 100),
(92, 'M', 'Trắng', 20), (92, 'L', 'Trắng', 20), -- Áo Polo (Bổ sung)
(93, 'S', 'Trắng', 20), (93, 'M', 'Trắng', 20), -- Váy (Bổ sung)
(94, 'Freesize', 'Trắng', 30), -- Mũ (Bổ sung)
(95, 'Freesize', 'Trắng', 50), -- Băng tay (Bổ sung)

-- =======================================================
-- 7. PHỤ KIỆN CHUNG (ID 96-100)
-- =======================================================
(96, '1.5L', 'Xanh', 50), (96, '1.5L', 'Hồng', 50),
(97, 'Freesize', 'Xanh', 80), (97, 'Freesize', 'Xám', 60),
(98, 'Freesize', 'Đen', 100),
(99, 'Chai', 'Trắng', 50),
(100, 'Freesize', 'Đen', 40), (100, 'Freesize', 'Trắng', 20);



-- thieu demo cua nhung cai khac