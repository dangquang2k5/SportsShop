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
INSERT INTO `Brand` (`BrandID`, `BrandName`, `BrandDescription`, `created_at`, `updated_at`) VALUES (1,'Nike','Thương hiệu thể thao hàng đầu thế giới, tiên phong trong đổi mới công nghệ và truyền cảm hứng cho mọi vận động viên.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(2,'Adidas','Biểu tượng thể thao toàn cầu đến từ Đức, nổi tiếng với công nghệ đế Boost êm ái và thiết kế ba sọc kinh điển.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(3,'Puma','Thương hiệu thể thao Đức năng động, kết hợp hoàn hảo giữa hiệu suất tốc độ và phong cách thời trang đường phố.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(4,'Yonex','Thương hiệu Nhật Bản thống trị thế giới cầu lông với công nghệ Carbon tiên tiến và độ chính xác tuyệt đối.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(5,'Lining','Thương hiệu thể thao quốc tế cao cấp, mang đến các sản phẩm chất lượng với thiết kế thời thượng và giá thành hợp lý.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(6,'Mizuno','Thương hiệu Nhật Bản lâu đời, cam kết chất lượng hoàn hảo và sự tỉ mỉ trong từng sản phẩm chạy bộ và bóng đá.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(7,'Under Armour','Tiên phong trong trang phục hiệu suất cao, nổi tiếng với công nghệ vải co giãn và thấm hút mồ hôi vượt trội.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(8,'Wilson','Nhà sản xuất dụng cụ thể thao hàng đầu của Mỹ, chuyên về vợt Tennis và các loại bóng thi đấu chuyên nghiệp.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(9,'Molten','Thương hiệu Nhật Bản chuyên cung cấp bóng thi đấu chính thức (Official Game Ball) cho các giải đấu quốc tế lớn như FIBA.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(10,'Coolmate','Thương hiệu thời trang nam Việt Nam ứng dụng công nghệ, mang lại sự thoải mái tối đa và trải nghiệm mua sắm tiện lợi.','2025-11-23 17:20:16','2025-11-23 17:20:16');

-- ============================================
-- 2. CATEGORIES
-- ============================================
INSERT INTO `Categories` (`CategoryID`, `CategoryName`, `CategoryDescription`, `created_at`, `updated_at`) VALUES (1,'Bóng Đá','Thỏa mãn đam mê túc cầu với đầy đủ trang thiết bị từ giày đá bóng sân cỏ nhân tạo/tự nhiên, quần áo thi đấu CLB mùa giải mới, găng tay thủ môn đến các phụ kiện hỗ trợ tập luyện.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(2,'Cầu Lông','Tổng hợp dụng cụ cầu lông chính hãng bao gồm vợt trợ lực công nghệ cao, giày chuyên dụng bám sân, cầu thi đấu tiêu chuẩn và các phụ kiện như bao vợt, quấn cán.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(3,'Gym & Fitness','Kiến tạo vóc dáng hoàn hảo với hệ thống dụng cụ tập thể hình đa dạng, thảm Yoga định tuyến, tạ tay các loại và trang phục tập luyện co giãn, thấm hút mồ hôi tối ưu.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(4,'Chạy Bộ (Running)','Đồng hành trên mọi cung đường với các dòng giày chạy bộ công nghệ đệm êm ái, trang phục thoáng khí siêu nhẹ và các phụ kiện bó cơ hỗ trợ tăng thành tích marathon.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(5,'Bóng Rổ','Thế giới của các Baller với những đôi giày bóng rổ hiệu suất cao bảo vệ cổ chân, bóng thi đấu tiêu chuẩn FIBA và những set đồ Jersey đậm chất văn hóa bóng rổ.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(6,'Quần Vợt (Tennis)','Cung cấp trang thiết bị Quần Vợt đẳng cấp bao gồm vợt trợ lực kiểm soát bóng, giày tennis đế bền chống mài mòn và các phụ kiện thi đấu chuyên nghiệp.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(7,'Phụ Kiện & Bảo Hộ','Tối ưu hóa hiệu suất và bảo vệ cơ thể với các phụ kiện thiết yếu như bình nước thể thao, túi xách đa năng, băng bó chấn thương và các sản phẩm hỗ trợ phục hồi.','2025-11-23 17:20:16','2025-11-23 17:20:16');

-- ============================================
-- 3. VOUCHERS
-- ============================================
INSERT INTO `Voucher` (`VoucherID`, `VoucherCode`, `DiscountValue`, `StartDate`, `EndDate`, `Quantity`, `MinOrderValue`, `created_at`) VALUES (1,'WELCOME2025',100000.00,'2025-01-01','2025-12-31',100,500000.00,'2025-11-23 17:20:16'),(2,'SUMMER50',50000.00,'2025-06-01','2025-08-31',200,300000.00,'2025-11-23 17:20:16'),(3,'NEWYEAR2025',200000.00,'2025-01-01','2025-01-31',50,1000000.00,'2025-11-23 17:20:16'),(4,'FLASH30',30000.00,'2025-01-01','2025-12-31',500,200000.00,'2025-11-23 17:20:16'),(5,'VIP100',100000.00,'2025-01-01','2025-12-31',150,800000.00,'2025-11-23 17:20:16'),(6,'SPORT20',20000.00,'2025-01-01','2025-12-31',300,150000.00,'2025-11-23 17:20:16'),(7,'MEGA200',200000.00,'2025-01-01','2025-12-31',30,1500000.00,'2025-11-23 17:20:16'),(8,'FREESHIP',30000.00,'2025-01-01','2025-12-31',999,0.00,'2025-11-23 17:20:16'),(9,'AUTUMN150',150000.00,'2025-09-01','2025-11-30',80,900000.00,'2025-11-23 17:20:16'),(10,'BLACKFRIDAY',500000.00,'2025-11-24','2025-11-30',20,2000000.00,'2025-11-23 17:20:16'),(11,'STUDENT15',15000.00,'2025-01-01','2025-12-31',400,100000.00,'2025-11-23 17:20:16'),(12,'MEMBER250',250000.00,'2025-01-01','2025-12-31',60,1200000.00,'2025-11-23 17:20:16'),(13,'WEEKEND50',50000.00,'2025-01-01','2025-12-31',250,400000.00,'2025-11-23 17:20:16');


-- ============================================
-- 4. PRODUCTS
-- ============================================
INSERT INTO `Product` (`ProductID`, `ProductName`, `Price`, `Status`, `MainImage`, `Description`, `RatingAvg`, `CategoryID`, `BrandID`, `created_at`, `updated_at`) VALUES (5,'Giày Nike Phantom GX Academy',1850000.00,'active','../assets/uploads/products/main_1764000343.jpg','Mở rộng vùng tiếp xúc bóng nhờ hệ thống dây lệch, kết hợp lớp phủ NikeSkin giúp thực hiện những cú sút xoáy hiểm hóc và kiểm soát trận đấu dễ dàng.',0.00,1,1,'2025-11-23 17:20:16','2025-11-24 16:05:43'),(6,'Bóng Động Lực UHV 2.07',550000.00,'active','../assets/uploads/products/main_1764000144.png','Bóng thi đấu chính thức tại V-League, đạt chuẩn FIFA Quality Pro. Vỏ bóng da PU cao cấp chống thấm nước, độ nảy ổn định và quỹ đạo bay chính xác.',0.00,1,5,'2025-11-23 17:20:16','2025-11-24 16:02:24'),(9,'Áo Đấu Real Madrid Home 2024',250000.00,'active','../assets/uploads/products/main_1763999854.jpg','Thiết kế hoàng gia với màu trắng kinh điển phối viền vàng sang trọng. Logo thêu sắc nét, chất liệu vải co giãn 4 chiều hỗ trợ vận động cường độ cao.',0.00,1,2,'2025-11-23 17:20:16','2025-11-24 15:57:34'),(12,'Găng Tay Thủ Môn Adidas Predator',1200000.00,'active','../assets/uploads/products/main_1763999783.png','Găng tay thủ môn chuyên nghiệp với mút URG 2.0 dính bóng như nam châm. Mặt lưng có gai cao su Demonskin hỗ trợ đấm bóng phá vây hiệu quả.',0.00,1,2,'2025-11-23 17:20:16','2025-11-24 15:56:23'),(14,'Tất Chống Trượt Aolikes',50000.00,'active','../assets/uploads/products/main_1764000473.png','Hệ thống hạt cao su dưới lòng bàn chân giúp tăng ma sát tuyệt đối với lót giày, loại bỏ tình trạng trượt chân trong giày, hạn chế phồng rộp.',0.00,1,10,'2025-11-23 17:20:16','2025-11-24 16:07:53'),(15,'Bó Gối Đá Bóng Dài',120000.00,'active','../assets/uploads/products/main_1764000566.png','Bảo vệ toàn diện khớp gối và dây chằng, giảm thiểu nguy cơ chấn thương khi va chạm. Chất liệu thun co giãn, thoáng khí, không gây hầm bí.',0.00,1,10,'2025-11-23 17:20:16','2025-11-24 16:09:26'),(21,'Vợt Yonex Astrox 77 Pro',3200000.00,'active','../assets/uploads/products/main_1764147176.png','Cây vợt thiên công mạnh mẽ được tin dùng bởi các VĐV hàng đầu. Hệ thống Rotational Generator System giúp vợt cân bằng, phục hồi nhanh sau mỗi cú đập.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:52:56'),(24,'Vợt Lining Halbertec 8000',3600000.00,'active','../assets/uploads/products/main_1764147291.png','Cây vợt cân bằng hoàn hảo, kiểm soát cầu tối ưu. Thân vợt dẻo dai giúp trợ lực tốt cho người chơi phong trào, phù hợp lối đánh điều cầu.',0.00,2,5,'2025-11-23 17:20:16','2025-11-26 08:54:51'),(26,'Giày Yonex Eclipsion Z3',2400000.00,'active','../assets/uploads/products/main_1764147353.png','Sở hữu đế Power Cushion+ độc quyền giúp hấp thụ chấn động và chuyển hóa thành năng lượng cho bước di chuyển tiếp theo. Ổn định cổ chân tuyệt vời.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:55:53'),(29,'Áo Cầu Lông Yonex Thi Đấu',450000.00,'active','../assets/uploads/products/main_1764147400.png','Công nghệ làm mát VeryCool Xylitol giúp giảm nhiệt độ cơ thể tới 3 độ C. Vải co giãn đa chiều hỗ trợ tối đa cho các động tác vung vợt.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:56:40'),(34,'Cuốn Cán Vợt Yonex (Hộp 3 cái)',120000.00,'active','../assets/uploads/products/main_1764147451.png','Chất liệu cao su non tổng hợp bám tay, thấm hút mồ hôi tốt. Giúp cầm vợt chắc chắn, tránh trơn trượt trong những pha đập cầu mạnh.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:57:32'),(35,'Bao Vợt Cầu Lông 2 Ngăn',450000.00,'active','../assets/uploads/products/main_1764147546.png','Có lớp lót cách nhiệt bảo vệ vợt khỏi nhiệt độ cao. Sức chứa lớn (4-6 vợt) cùng ngăn đựng giày và quần áo riêng biệt.',0.00,2,5,'2025-11-23 17:20:16','2025-11-26 08:59:06'),(36,'Áo Tanktop Nam Under Armour',450000.00,'active','../assets/uploads/products/main_1764168926.png','Thuộc bộ sưu tập Project Rock của The Rock. Thiết kế khoét nách sâu khoe trọn cơ bắp, chất vải Cotton-Poly mềm mại và thấm hút mồ hôi.',0.00,3,7,'2025-11-23 17:20:16','2025-11-26 14:55:26'),(46,'Bóng Yoga Chống Nổ',120000.00,'active','../assets/uploads/products/main_1764168648.png','Chịu lực lên đến 200kg, công nghệ chống nổ (xì hơi từ từ khi bị thủng). Hỗ trợ các bài tập thăng bằng, cơ bụng và phục hồi chức năng.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:50:48'),(50,'Dây Nhảy Thể Lực Cáp',50000.00,'active','../assets/uploads/products/main_1764168804.png','Dây cáp bọc nhựa siêu bền, trục bi xoay 360 độ giúp dây quay tốc độ cao mà không bị rối. Bài tập Cardio đốt mỡ hiệu quả nhất.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:53:24'),(51,'Bình Lắc Whey 700ml',80000.00,'active','../assets/uploads/products/main_1764168757.png','Nhựa PP an toàn sức khỏe (BPA Free). Có quả cầu lò xo inox giúp đánh tan bột Whey/Mass nhanh chóng mà không bị vón cục.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:52:37'),(54,'Băng Đô Headband',40000.00,'active','../assets/uploads/products/main_1763996959.png','Ngăn mồ hôi trán chảy xuống mắt gây cay mắt. Chất liệu Cotton thấm hút cực tốt, co giãn vừa vặn mọi kích cỡ đầu.',0.00,3,1,'2025-11-23 17:20:16','2025-11-24 15:09:19'),(55,'Túi Trống Gym Có Ngăn Giày',280000.00,'active','../assets/uploads/products/main_1763996905.png','Thiết kế hình trụ năng động, dung tích lớn 25L. Có ngăn đựng giày riêng biệt có lỗ thoáng khí, ngăn mùi khó chịu ám vào quần áo.',0.00,3,2,'2025-11-23 17:20:16','2025-11-24 15:08:25'),(61,'Áo Singlet Chạy Bộ Nike',450000.00,'active','../assets/uploads/products/main_1763996779.png','Áo ba lỗ siêu nhẹ (Ultra-lightweight), đục lỗ laser thoáng khí toàn thân, giảm thiểu ma sát lên da khi chạy đường dài.',0.00,4,1,'2025-11-23 17:20:16','2025-11-24 15:06:19'),(62,'Quần Short Chạy Bộ Xẻ Tà',250000.00,'active','../assets/uploads/products/main_1763995460.png','Thiết kế xẻ tà cao tối đa hóa phạm vi chuyển động của chân. Tích hợp quần lót tam giác bên trong và túi nhỏ đựng chìa khóa.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:44:20'),(63,'Áo Khoác Gió Chạy Bộ',350000.00,'active','../assets/uploads/products/main_1763971873.png','Chất liệu dù siêu mỏng nhẹ, trượt nước (Water Repellent) và chắn gió tốt. Có thể gấp gọn trong lòng bàn tay, phù hợp chạy sáng sớm.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 08:11:13'),(64,'Belt Chạy Bộ Đựng Điện Thoại',80000.00,'active','../assets/uploads/products/main_1763995205.png','Đai đeo hông ôm sát cơ thể, không rung lắc khi chạy. Đựng vừa điện thoại màn hình lớn, chìa khóa, gel năng lượng.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:40:05'),(65,'Mũ Nửa Đầu Visor',120000.00,'active','../assets/uploads/products/main_1763995129.png','Thiết kế hở đầu giúp thoát nhiệt đỉnh đầu nhanh chóng. Vành mũ rộng che nắng hiệu quả, đai thấm mồ hôi trán mềm mại.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:38:49'),(66,'Tất Chạy Bộ Xỏ Ngón',60000.00,'active','../assets/uploads/products/main_1763995035.png','Tách riêng 5 ngón chân giúp ngăn ngừa ma sát giữa các ngón, loại bỏ hoàn toàn nguy cơ phồng rộp (blister) khi chạy marathon.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:37:15'),(67,'Bó Bắp Chân Calf Compress',100000.00,'active','../assets/uploads/products/main_1763994991.png','Công nghệ nén ép (Compression) giúp tăng cường lưu thông máu, giảm rung lắc cơ bắp chân, hạn chế chuột rút và mỏi cơ.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:36:31'),(68,'Giày Chạy Bộ Coolmate',690000.00,'active','../assets/uploads/products/main_1763994932.png','Sản phẩm chạy bộ giá tốt cho người mới bắt đầu. Đế Phylon nhẹ và êm, thân giày vải dệt thoáng khí, thiết kế tối giản dễ phối đồ.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:35:32'),(69,'Áo Thun Chạy Bộ Coolmate',190000.00,'active','../assets/uploads/products/main_1763994853.png','Sử dụng công nghệ Excool độc quyền thấm hút mồ hôi và khô nhanh gấp 2 lần Cotton. Mềm mại, mát lạnh, chống tia UV.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:34:13'),(70,'Quần Legging Chạy Bộ Nam',250000.00,'active','../assets/uploads/products/main_1763994765.png','Giữ ấm cơ bắp khi chạy mùa đông. Có túi bên hông tiện lợi đựng điện thoại. Chất vải co giãn 4 chiều hỗ trợ vận động.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:32:45'),(71,'Giày Nike LeBron 21',4500000.00,'active','../assets/uploads/products/main_1763994589.png','Giày thửa riêng cho \"King James\". Hệ thống dây cáp 360 độ giữ chân chắc chắn, đệm Zoom Turbo đàn hồi cực tốt cho những pha tiếp đất nặng.',0.00,5,1,'2025-11-23 17:20:16','2025-11-24 14:29:49'),(72,'Giày Nike KD 16',3800000.00,'active','../assets/uploads/products/main_1763994454.jpg','Nhẹ hơn và thoáng hơn. Bộ đệm Air Zoom Strobel full-length mang lại cảm giác êm ái tức thì ngay khi xỏ chân vào.',0.00,5,1,'2025-11-23 17:20:16','2025-11-24 14:27:34'),(73,'Giày Under Armour Curry 11',4200000.00,'active','../assets/uploads/products/main_1763972480.png','Sử dụng đế UA Flow loại bỏ hoàn toàn cao su, mang lại độ bám sàn \"kinh khủng\" và trọng lượng siêu nhẹ cho những cú ném 3 điểm.',0.00,5,7,'2025-11-23 17:20:16','2025-11-24 08:21:20'),(74,'Giày Adidas Harden Vol 7',3600000.00,'active','../assets/uploads/products/main_1763972258.png','Thiết kế lấy cảm hứng từ áo khoác phao độc đáo. Đệm lai giữa Boost và Lightstrike vừa êm ái vừa phản hồi nhanh cho lối đánh Eurostep.',0.00,5,2,'2025-11-23 17:20:16','2025-11-24 08:17:38'),(75,'Giày Lining Way of Wade',3200000.00,'active','../assets/uploads/products/main_1763972080.png','Dòng giày cao cấp nhất của Lining. Công nghệ Boom (Pebax) siêu nảy, tấm Carbon chống xoắn bàn chân, thiết kế cực kỳ hầm hố.',0.00,5,5,'2025-11-23 17:20:16','2025-11-24 08:14:40'),(76,'Bóng Rổ Molten BG4500',1200000.00,'active','../assets/uploads/products/main_1763995270.png','Bóng thi đấu chính thức của FIBA. Da PU cao cấp cho độ bám dính tuyệt vời ngay cả khi tay ra mồ hôi, độ nảy chuẩn xác.',0.00,5,9,'2025-11-23 17:20:16','2025-11-24 14:41:10'),(86,'Vợt Wilson Pro Staff v14',4800000.00,'active','../assets/uploads/products/main_1764169247.png','Huyền thoại trở lại với phiên bản v14. Khung vợt ổn định, mang lại cảm giác đánh bóng cổ điển và độ chính xác tuyệt đối như Roger Federer.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:00:47'),(87,'Vợt Babolat Pure Aero',4500000.00,'active','../assets/uploads/products/main_1764169298.png','Cỗ máy tạo xoáy (Spin Machine) của Rafael Nadal. Khung vợt khí động học giúp tăng tốc độ đầu vợt, tạo ra những cú Topspin cắm sân.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:01:38'),(91,'Bóng Tennis Wilson (Lon 4 quả)',150000.00,'active','../assets/uploads/products/main_1764169357.png','Bóng thi đấu chính thức tại giải Australian Open. Lớp nỉ Optivis giúp bóng dễ nhìn hơn, độ nảy bền bỉ trên mặt sân cứng.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:02:37'),(92,'Áo Polo Tennis NikeCourt',550000.00,'active','../assets/uploads/products/main_1764169623.png','Phong cách lịch lãm quý tộc. Cổ áo bẻ gập gọn gàng, đường may vai lùi về sau giúp vung vợt giao bóng thoải mái không bị kích.',0.00,6,1,'2025-11-23 17:20:16','2025-11-26 15:07:03'),(93,'Váy Tennis Adidas Club',450000.00,'active','../assets/uploads/products/main_1764169591.png','Vải công nghệ AEROREADY thấm hút mồ hôi. Thiết kế cạp bản rộng tôn dáng, xếp ly xòe nhẹ tạo sự nữ tính trong từng bước chạy.',0.00,6,2,'2025-11-23 17:20:16','2025-11-26 15:06:31'),(94,'Mũ Lưỡi Trai Tennis',250000.00,'active','../assets/uploads/products/main_1764169408.png','Mặt dưới lưỡi trai màu đen giúp chống lóa mắt khi nhìn lên trời giao bóng. Công nghệ Dri-FIT giữ đầu luôn khô thoáng.',0.00,6,1,'2025-11-23 17:20:16','2025-11-26 15:03:28'),(96,'Bình Nước Thể Thao 1.5L',120000.00,'active','../assets/uploads/products/main_1764169167.png','Dung tích lớn 1.5L đảm bảo đủ nước cho cả buổi tập dài. Nhựa Tritan cao cấp chịu va đập, không chứa BPA gây hại sức khỏe.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:59:27'),(97,'Khăn Lạnh Thể Thao',60000.00,'active','../assets/uploads/products/main_1764169094.png','Công nghệ làm mát tức thì: chỉ cần nhúng nước, vắt khô và phẩy nhẹ là khăn sẽ giảm nhiệt độ sâu, giúp hạ nhiệt cơ thể nhanh chóng.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:58:14'),(98,'Túi Rút Đựng Đồ',50000.00,'active','../assets/uploads/products/main_1764169038.png','Nhỏ gọn, tiện lợi để đựng giày, quần áo bẩn hoặc đồ cá nhân lặt vặt. Dây rút chắc chắn, có thể đeo như balo nhẹ.',0.00,7,2,'2025-11-23 17:20:16','2025-11-26 14:57:18'),(99,'Xịt Giảm Đau Thể Thao',180000.00,'active','../assets/uploads/products/main_1764168990.png','Dạng xịt lạnh giúp đóng băng cảm giác đau tức thì, giảm sưng tấy cho các chấn thương phần mềm như bong gân, bầm tím.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:56:30'),(100,'Kính Mát Thể Thao Chống UV',250000.00,'active','../assets/uploads/products/main_1764000783.png','Thiết kế ôm sát khuôn mặt không bị rơi khi vận động mạnh. Tròng kính phân cực chống tia UV400 bảo vệ mắt khi chạy bộ dưới nắng gắt.',0.00,7,10,'2025-11-23 17:20:16','2025-11-24 16:13:03');


-- ============================================
-- PRODUCT DETAILS (Size và Màu sắc)
-- ===========================================
INSERT INTO `ProductDetail` (`ProductDetailID`, `ProductID`, `Size`, `Color`, `Quantity`, `Image`) VALUES (17,5,'40','Trắng-Xanh',20,''),(18,5,'41','Trắng-Xanh',25,''),(19,5,'42','Trắng-Xanh',20,''),(24,6,'Size 5','Trắng-Xanh',50,'../assets/uploads/products/product_6_Trắng-Xanh_1764000173.png'),(25,6,'Size 5','Trắng-Đỏ',30,''),(31,9,'S','Trắng',30,''),(32,9,'M','Trắng',50,''),(33,9,'L','Trắng',50,''),(34,9,'XL','Trắng',30,''),(50,12,'Size 8','Đỏ',15,''),(51,12,'Size 9','Đỏ',15,''),(54,14,'Freesize','Trắng',100,''),(55,14,'Freesize','Đen',100,'../assets/uploads/products/product_14_Đen_1764000473.png'),(56,15,'M','Đen-Xanh',30,''),(57,15,'L','Đen-Xanh',30,''),(59,21,'4U','Vàng Shine',20,''),(60,21,'3U','Vàng Shine',15,''),(65,24,'4U','',20,''),(67,26,'40','Xanh',15,''),(68,26,'41','Xanh',20,''),(73,29,'M','Xanh Navy',30,''),(74,29,'L','Xanh Navy',30,''),(82,34,'Freesize','Đủ màu',200,''),(83,35,'Freesize','Đen',20,'../assets/uploads/products/product_35_Đen_1764147546.png'),(84,35,'Freesize','Trắng',20,'../assets/uploads/products/product_35_Trắng_1764147546.png'),(85,36,'L','Đen',30,''),(86,36,'XL','Xám',20,''),(109,46,'65cm','Tím',20,'../assets/uploads/products/product_46_Tím_1764168648.png'),(110,46,'75cm','Xám',20,'../assets/uploads/products/product_46_Xám_1764168648.png'),(115,50,'Freesize','Tím',50,''),(116,51,'700ml','Trắng',50,'../assets/uploads/products/product_51_Trắng_1764168757.png'),(117,54,'Freesize','Trắng',50,''),(118,55,'Freesize','Đen',30,''),(130,68,'40','Xám',30,''),(131,68,'41','Xám',40,''),(132,61,'M','Đen',20,''),(133,61,'L','Đen',20,''),(134,62,'M','Xanh',40,''),(135,62,'L','Xanh',40,''),(136,63,'L','Cam',20,''),(137,63,'XL','Cam',8,''),(138,64,'Freesize','Đen',49,''),(139,65,'Freesize','Trắng',50,''),(140,66,'Freesize','Xám',50,''),(141,67,'M','Đen',30,''),(142,69,'M','Đen',50,''),(143,69,'L','Đen',50,''),(144,70,'M','Đen',30,''),(145,70,'L','Đen',30,''),(146,71,'41','Tím-Vàng',15,''),(147,71,'42','Tím-Vàng',20,''),(148,72,'41','Đỏ-Đen',15,''),(149,72,'42','Đỏ-Đen',20,''),(150,73,'41','Trắng-Xanh',15,''),(151,73,'42','Trắng-Xanh',20,''),(152,74,'41','Đen',15,''),(153,74,'42','Đen',20,''),(154,75,'41','Hồng',15,''),(155,75,'42','Hồng',20,''),(156,76,'Size 7','cam-trắng',60,''),(169,86,'315g','Cam',10,''),(170,86,'290g','Cam',10,''),(171,87,'300g','Xanh-Đen',15,''),(177,91,'Lon','Vàng',100,''),(178,92,'M','Tím than',20,''),(179,92,'L','Tím than',20,''),(180,93,'S','Trắng',20,''),(181,93,'M','Trắng',20,''),(182,94,'Freesize','Đen',30,''),(184,96,'1.5L','Xanh',50,''),(185,96,'1.5L','Hồng',50,''),(186,97,'Freesize','Xanh',80,''),(187,97,'Freesize','Xám',60,''),(188,98,'Freesize','Đen',100,''),(189,99,'Chai','',50,''),(190,100,'Freesize','Đen',40,''),(192,100,'Freesize','Trắng',35,'../assets/uploads/products/product_100_Trắng_1764000873.png'),(193,51,'700ml','Đen',43,'../assets/uploads/products/product_51_Đen_1764168757.png');


-- thieu demo cua nhung cai khac