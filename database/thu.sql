SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0; -- Tắt kiểm tra khóa ngoại để Truncate không lỗi

-- =======================================================
-- 1. XÓA DỮ LIỆU CŨ & TẠO CATEGORIES - BRAND MỚI
-- =======================================================
TRUNCATE TABLE `OrderDetails`;
TRUNCATE TABLE `CartItems`;
TRUNCATE TABLE `InventoryLog`;
TRUNCATE TABLE `Reviews`;
TRUNCATE TABLE `ProductDetail`;
TRUNCATE TABLE `Product`;
TRUNCATE TABLE `Brand`;
TRUNCATE TABLE `Categories`;

-- Insert Categories (7 Nhóm chuẩn)
INSERT INTO `Categories` (CategoryID, CategoryName, CategoryDescription) VALUES 
(1, 'Bóng Đá', 'Thỏa mãn đam mê túc cầu với đầy đủ trang thiết bị từ giày đá bóng sân cỏ nhân tạo/tự nhiên, quần áo thi đấu CLB mùa giải mới, găng tay thủ môn đến các phụ kiện hỗ trợ tập luyện.'), 
(2, 'Cầu Lông', 'Tổng hợp dụng cụ cầu lông chính hãng bao gồm vợt trợ lực công nghệ cao, giày chuyên dụng bám sân, cầu thi đấu tiêu chuẩn và các phụ kiện như bao vợt, quấn cán.'), 
(3, 'Gym & Fitness', 'Kiến tạo vóc dáng hoàn hảo với hệ thống dụng cụ tập thể hình đa dạng, thảm Yoga định tuyến, tạ tay các loại và trang phục tập luyện co giãn, thấm hút mồ hôi tối ưu.'), 
(4, 'Chạy Bộ (Running)', 'Đồng hành trên mọi cung đường với các dòng giày chạy bộ công nghệ đệm êm ái, trang phục thoáng khí siêu nhẹ và các phụ kiện bó cơ hỗ trợ tăng thành tích marathon.'), 
(5, 'Bóng Rổ', 'Thế giới của các Baller với những đôi giày bóng rổ hiệu suất cao bảo vệ cổ chân, bóng thi đấu tiêu chuẩn FIBA và những set đồ Jersey đậm chất văn hóa bóng rổ.'), 
(6, 'Quần Vợt (Tennis)', 'Cung cấp trang thiết bị Quần Vợt đẳng cấp bao gồm vợt trợ lực kiểm soát bóng, giày tennis đế bền chống mài mòn và các phụ kiện thi đấu chuyên nghiệp.'), 
(7, 'Phụ Kiện & Bảo Hộ', 'Tối ưu hóa hiệu suất và bảo vệ cơ thể với các phụ kiện thiết yếu như bình nước thể thao, túi xách đa năng, băng bó chấn thương và các sản phẩm hỗ trợ phục hồi.');

-- Insert Brand (10 Thương hiệu lớn)
INSERT INTO `Brand` (BrandID, BrandName, BrandDescription) VALUES 
(1, 'Nike', 'Thương hiệu thể thao hàng đầu thế giới, tiên phong trong đổi mới công nghệ và truyền cảm hứng cho mọi vận động viên.'), 
(2, 'Adidas', 'Biểu tượng thể thao toàn cầu đến từ Đức, nổi tiếng với công nghệ đế Boost êm ái và thiết kế ba sọc kinh điển.'), 
(3, 'Puma', 'Thương hiệu thể thao Đức năng động, kết hợp hoàn hảo giữa hiệu suất tốc độ và phong cách thời trang đường phố.'), 
(4, 'Yonex', 'Thương hiệu Nhật Bản thống trị thế giới cầu lông với công nghệ Carbon tiên tiến và độ chính xác tuyệt đối.'), 
(5, 'Lining', 'Thương hiệu thể thao quốc tế cao cấp, mang đến các sản phẩm chất lượng với thiết kế thời thượng và giá thành hợp lý.'), 
(6, 'Mizuno', 'Thương hiệu Nhật Bản lâu đời, cam kết chất lượng hoàn hảo và sự tỉ mỉ trong từng sản phẩm chạy bộ và bóng đá.'), 
(7, 'Under Armour', 'Tiên phong trong trang phục hiệu suất cao, nổi tiếng với công nghệ vải co giãn và thấm hút mồ hôi vượt trội.'), 
(8, 'Wilson', 'Nhà sản xuất dụng cụ thể thao hàng đầu của Mỹ, chuyên về vợt Tennis và các loại bóng thi đấu chuyên nghiệp.'), 
(9, 'Molten', 'Thương hiệu Nhật Bản chuyên cung cấp bóng thi đấu chính thức (Official Game Ball) cho các giải đấu quốc tế lớn như FIBA.'), 
(10, 'Coolmate', 'Thương hiệu thời trang nam Việt Nam ứng dụng công nghệ, mang lại sự thoải mái tối đa và trải nghiệm mua sắm tiện lợi.');

-- =======================================================
-- 2. INSERT 100 SẢN PHẨM (Đúng cột trong schema của bạn)
-- =======================================================
INSERT INTO Product (ProductName, Price, CategoryID, BrandID, Description, MainImage) VALUES
-- --- BÓNG ĐÁ (1-20) ---
('Giày Nike Mercurial Vapor 15 Elite', 2800000, 1, 1, 'Giày đá bóng sân cỏ nhân tạo, thiên hướng tốc độ.', 'img/foot_1.jpg'),
('Giày Adidas Predator Accuracy .3', 1900000, 1, 2, 'Kiểm soát bóng tối ưu, vân 3D bám dính.', 'img/foot_2.jpg'),
('Giày Puma Future Ultimate', 2200000, 1, 3, 'Công nghệ Fuzionfit ôm chân, linh hoạt.', 'img/foot_3.jpg'),
('Giày Mizuno Morelia Neo 3 Pro', 2600000, 1, 6, 'Da Kangaroo siêu mềm, cảm giác bóng thật chân.', 'img/foot_4.jpg'),
('Giày Nike Phantom GX Academy', 1850000, 1, 1, 'Hỗ trợ sút xoáy, kiểm soát bóng.', 'img/foot_5.jpg'),
('Bóng Động Lực UHV 2.07', 550000, 1, 5, 'Bóng thi đấu V-League chính hãng.', 'img/foot_6.jpg'),
('Bóng Adidas Champions League', 850000, 1, 2, 'Bóng tiêu chuẩn FIFA Quality Pro.', 'img/foot_7.jpg'),
('Áo Đấu Man Utd Home 2024', 250000, 1, 2, 'Áo cổ động viên, vải mè thái.', 'img/foot_8.jpg'),
('Áo Đấu Real Madrid Home 2024', 250000, 1, 2, 'Màu trắng hoàng gia, logo thêu.', 'img/foot_9.jpg'),
('Áo Đấu Tuyển Việt Nam 2024', 350000, 1, 5, 'Hàng chính hãng Grand Sport.', 'img/foot_10.jpg'),
('Bộ Quần Áo Bóng Đá Không Logo CP', 150000, 1, 10, 'Thun lạnh co giãn 4 chiều.', 'img/foot_11.jpg'),
('Găng Tay Thủ Môn Adidas Predator', 1200000, 1, 2, 'Mút dính URG 2.0, có xương bảo vệ.', 'img/foot_12.jpg'),
('Găng Tay Nike Vapor Grip 3', 1500000, 1, 1, 'Công nghệ ACC chơi mọi thời tiết.', 'img/foot_13.jpg'),
('Tất Chống Trượt Aolikes', 50000, 1, 10, 'Có hạt cao su dưới đế.', 'img/foot_14.jpg'),
('Bó Gối Đá Bóng Dài', 120000, 1, 10, 'Bảo vệ đầu gối và dây chằng.', 'img/foot_15.jpg'),
('Giày Kamito QH19', 650000, 1, 5, 'Giày Quang Hải đại diện.', 'img/foot_16.jpg'),
('Áo Bib Tập Luyện', 30000, 1, 10, 'Áo lưới chia đội.', 'img/foot_17.jpg'),
('Quần Short Đá Banh Nike', 350000, 1, 1, 'Vải dù nhẹ, có túi khóa kéo.', 'img/foot_18.jpg'),
('Áo Giữ Nhiệt Body Đá Bóng', 150000, 1, 10, 'Mặc lót trong giữ ấm.', 'img/foot_19.jpg'),
('Túi Đựng Giày 2 Ngăn', 120000, 1, 2, 'Đựng vừa giày và quần áo.', 'img/foot_20.jpg'),

-- --- CẦU LÔNG (21-35) ---
('Vợt Yonex Astrox 77 Pro', 3200000, 2, 4, 'Thiên công, đập cầu cắm.', 'img/bad_1.jpg'),
('Vợt Yonex Nanoflare 800', 3500000, 2, 4, 'Phản tạt nhanh, khung vợt mỏng.', 'img/bad_2.jpg'),
('Vợt Lining Axforce 80', 3100000, 2, 5, 'Vợt cân bằng, công thủ toàn diện.', 'img/bad_3.jpg'),
('Vợt Lining Halbertec 8000', 3600000, 2, 5, 'Kiểm soát cầu tốt.', 'img/bad_4.jpg'),
('Vợt Mizuno Fortius 10', 2800000, 2, 6, 'Vợt cứng, tốc độ cao.', 'img/bad_5.jpg'),
('Giày Yonex Eclipsion Z3', 2400000, 2, 4, 'Đế Power Cushion+ giảm chấn.', 'img/bad_6.jpg'),
('Giày Lining Saga Pro', 1800000, 2, 5, 'Bền bỉ, bám sân.', 'img/bad_7.jpg'),
('Giày Mizuno Wave Claw 2', 2100000, 2, 6, 'Siêu nhẹ, linh hoạt.', 'img/bad_8.jpg'),
('Áo Cầu Lông Yonex Thi Đấu', 450000, 2, 4, 'Công nghệ làm mát VeryCool.', 'img/bad_9.jpg'),
('Quần Cầu Lông Yonex', 320000, 2, 4, 'Vải thun mè thoáng khí.', 'img/bad_10.jpg'),
('Váy Cầu Lông Nữ Xếp Ly', 280000, 2, 5, 'Có quần lót bảo hộ bên trong.', 'img/bad_11.jpg'),
('Ống Cầu Thành Công', 180000, 2, 10, 'Cầu bền, bay đầm.', 'img/bad_12.jpg'),
('Ống Cầu Yonex AS40', 650000, 2, 4, 'Cầu thi đấu quốc tế.', 'img/bad_13.jpg'),
('Cuốn Cán Vợt Yonex (Hộp 3 cái)', 120000, 2, 4, 'Cao su bám tay.', 'img/bad_14.jpg'),
('Bao Vợt Cầu Lông 2 Ngăn', 450000, 2, 5, 'Cách nhiệt, đựng 4-6 vợt.', 'img/bad_15.jpg'),

-- --- GYM & FITNESS (36-55) ---
('Áo Tanktop Nam Under Armour', 450000, 3, 7, 'Dòng Project Rock, khoe cơ bắp.', 'img/gym_1.jpg'),
('Áo Thun Gym Shark Body', 350000, 3, 10, 'Ôm sát body, tôn dáng.', 'img/gym_2.jpg'),
('Quần Jogger Tập Gym Nam', 320000, 3, 10, 'Vải nỉ da cá, co giãn.', 'img/gym_3.jpg'),
('Quần Short Gym 2 Lớp', 250000, 3, 1, 'Có lớp lót bó cơ bên trong.', 'img/gym_4.jpg'),
('Áo Bra Nike Swoosh', 650000, 3, 1, 'Nâng đỡ tốt, đệm mút rời.', 'img/gym_5.jpg'),
('Quần Legging Lululemon (Rep)', 450000, 3, 10, 'Lưng cao, gen bụng.', 'img/gym_6.jpg'),
('Áo Croptop Tập Gym Nữ', 220000, 3, 10, 'Thấm hút mồ hôi.', 'img/gym_7.jpg'),
('Găng Tay Tập Gym Có Cuốn', 150000, 3, 10, 'Bảo vệ cổ tay khi đẩy tạ.', 'img/gym_8.jpg'),
('Đai Lưng Cứng Valeo', 250000, 3, 10, 'Hỗ trợ lưng khi Squat/Deadlift.', 'img/gym_9.jpg'),
('Thảm Tập Yoga TPE 8mm', 180000, 3, 10, 'Chống trượt, định tuyến.', 'img/gym_10.jpg'),
('Bóng Yoga Chống Nổ', 120000, 3, 10, 'Tập thăng bằng và Core.', 'img/gym_11.jpg'),
('Bộ Dây Kháng Lực Miniband', 80000, 3, 10, '5 mức độ kháng lực.', 'img/gym_12.jpg'),
('Tạ Tay Bọc Nhựa 5kg', 60000, 3, 10, 'Tạ tay tập tại nhà.', 'img/gym_13.jpg'),
('Con Lăn Tập Bụng', 150000, 3, 10, 'Kèm thảm lót gối.', 'img/gym_14.jpg'),
('Dây Nhảy Thể Lực Cáp', 50000, 3, 10, 'Dây cáp kim loại, quay nhanh.', 'img/gym_15.jpg'),
('Bình Lắc Whey 700ml', 80000, 3, 10, 'Có lò xo đánh tan bột.', 'img/gym_16.jpg'),
('Áo Stringer Gold Gym', 150000, 3, 10, 'Áo 3 lỗ dây nhỏ.', 'img/gym_17.jpg'),
('Quần Biker Short Nữ', 200000, 3, 10, 'Quần ngắn bó sát đùi.', 'img/gym_18.jpg'),
('Băng Đô Headband', 40000, 3, 1, 'Ngăn mồ hôi trán.', 'img/gym_19.jpg'),
('Túi Trống Gym Có Ngăn Giày', 280000, 3, 2, 'Vải dù chống nước.', 'img/gym_20.jpg'),

-- --- CHẠY BỘ (56-70) ---
('Giày Nike Air Zoom Pegasus 40', 3100000, 4, 1, 'Giày chạy quốc dân, đa năng.', 'img/run_1.jpg'),
('Giày Adidas Ultraboost Light', 3800000, 4, 2, 'Đế Boost êm ái, hoàn trả năng lượng.', 'img/run_2.jpg'),
('Giày Nike Zoom Fly 5', 3500000, 4, 1, 'Có đĩa carbon hỗ trợ tốc độ.', 'img/run_3.jpg'),
('Giày Mizuno Wave Rider 27', 2900000, 4, 6, 'Bền bỉ, ổn định.', 'img/run_4.jpg'),
('Giày Adidas Adizero Boston 12', 3200000, 4, 2, 'Giày tập luyện tốc độ cao.', 'img/run_5.jpg'),
('Áo Singlet Chạy Bộ Nike', 450000, 4, 1, 'Áo ba lỗ siêu nhẹ, thoáng khí.', 'img/run_6.jpg'),
('Quần Short Chạy Bộ Xẻ Tà', 250000, 4, 10, 'Xẻ tà cao, có quần lót trong.', 'img/run_7.jpg'),
('Áo Khoác Gió Chạy Bộ', 350000, 4, 10, 'Chống nắng, trượt nước nhẹ.', 'img/run_8.jpg'),
('Belt Chạy Bộ Đựng Điện Thoại', 80000, 4, 10, 'Đeo hông, giữ điện thoại không rung lắc.', 'img/run_9.jpg'),
('Mũ Nửa Đầu Visor', 120000, 4, 10, 'Che nắng, thoáng đầu.', 'img/run_10.jpg'),
('Tất Chạy Bộ Xỏ Ngón', 60000, 4, 10, 'Chống phồng rộp kẽ ngón chân.', 'img/run_11.jpg'),
('Bó Bắp Chân Calf Compress', 100000, 4, 10, 'Giảm rung cơ bắp chân.', 'img/run_12.jpg'),
('Giày Chạy Bộ Coolmate', 690000, 4, 10, 'Giá rẻ cho người mới bắt đầu.', 'img/run_13.jpg'),
('Áo Thun Chạy Bộ Coolmate', 190000, 4, 10, 'Công nghệ Excool khô nhanh.', 'img/run_14.jpg'),
('Quần Legging Chạy Bộ Nam', 250000, 4, 10, 'Có túi đựng điện thoại bên đùi.', 'img/run_15.jpg'),

-- --- BÓNG RỔ (71-85) ---
('Giày Nike LeBron 21', 4500000, 5, 1, 'Giày signature LeBron James.', 'img/bball_1.jpg'),
('Giày Nike KD 16', 3800000, 5, 1, 'Giày Kevin Durant, đệm Zoom êm.', 'img/bball_2.jpg'),
('Giày Under Armour Curry 11', 4200000, 5, 7, 'Đế Flow bám sàn cực tốt.', 'img/bball_3.jpg'),
('Giày Adidas Harden Vol 7', 3600000, 5, 2, 'Thiết kế cổ điển pha lẫn tương lai.', 'img/bball_4.jpg'),
('Giày Lining Way of Wade', 3200000, 5, 5, 'Giày bóng rổ cao cấp Lining.', 'img/bball_5.jpg'),
('Bóng Rổ Molten BG4500', 1200000, 5, 9, 'Bóng thi đấu da PU cao cấp.', 'img/bball_6.jpg'),
('Bóng Rổ Spalding TF-250', 550000, 5, 9, 'Bóng cao su tập luyện outdoor.', 'img/bball_7.jpg'),
('Áo Jersey Lakers Lebron', 350000, 5, 1, 'Áo đấu Los Angeles Lakers.', 'img/bball_8.jpg'),
('Áo Jersey Warriors Curry', 350000, 5, 7, 'Áo đấu Golden State Warriors.', 'img/bball_9.jpg'),
('Quần Bóng Rổ Form Rộng', 220000, 5, 10, 'Quần dài quá gối, ống rộng.', 'img/bball_10.jpg'),
('Tất Bóng Rổ Cổ Cao', 80000, 5, 1, 'Dày dặn, bảo vệ mắt cá.', 'img/bball_11.jpg'),
('Băng Đầu Gối Hex McDavid', 250000, 5, 10, 'Đệm lục giác bảo vệ gối.', 'img/bball_12.jpg'),
('Tay Ném Bóng Rổ (Sleeve)', 90000, 5, 10, 'Giữ ấm tay ném.', 'img/bball_13.jpg'),
('Balo Bóng Rổ Cỡ Lớn', 550000, 5, 1, 'Đựng vừa bóng size 7 và giày.', 'img/bball_14.jpg'),
('Lưới Bóng Rổ Phát Sáng', 120000, 5, 10, 'Phát quang trong đêm.', 'img/bball_15.jpg'),

-- --- TENNIS (86-95) ---
('Vợt Wilson Pro Staff v14', 4800000, 6, 8, 'Vợt của Roger Federer.', 'img/ten_1.jpg'),
('Vợt Babolat Pure Aero', 4500000, 6, 8, 'Vợt của Rafael Nadal, tạo xoáy.', 'img/ten_2.jpg'),
('Vợt Head Speed Pro', 4200000, 6, 8, 'Vợt của Novak Djokovic.', 'img/ten_3.jpg'),
('Giày Tennis Asics Gel-Resolution 9', 3200000, 6, 8, 'Bền bỉ, hỗ trợ di chuyển ngang.', 'img/ten_4.jpg'),
('Giày Tennis Adidas Barricade', 2800000, 6, 2, 'Ông vua độ bền sân cứng.', 'img/ten_5.jpg'),
('Bóng Tennis Wilson (Lon 4 quả)', 150000, 6, 8, 'Bóng thi đấu Australian Open.', 'img/ten_6.jpg'),
('Áo Polo Tennis NikeCourt', 550000, 6, 1, 'Cổ bẻ lịch sự, thoáng mát.', 'img/ten_7.jpg'),
('Váy Tennis Adidas Club', 450000, 6, 2, 'Thiết kế xếp ly năng động.', 'img/ten_8.jpg'),
('Mũ Lưỡi Trai Tennis', 250000, 6, 1, 'Công nghệ Dri-fit thấm hút.', 'img/ten_9.jpg'),
('Băng Chặn Mồ Hôi Cổ Tay', 80000, 6, 1, 'Set 2 cái, cotton dày.', 'img/ten_10.jpg'),

-- --- PHỤ KIỆN CHUNG (96-100) ---
('Bình Nước Thể Thao 1.5L', 120000, 7, 10, 'Nhựa an toàn BPA Free.', 'img/acc_1.jpg'),
('Khăn Lạnh Thể Thao', 60000, 7, 10, 'Nhúng nước vắt khô là mát.', 'img/acc_2.jpg'),
('Túi Rút Đựng Đồ', 50000, 7, 2, 'Nhỏ gọn tiện lợi.', 'img/acc_3.jpg'),
('Xịt Giảm Đau Thể Thao', 180000, 7, 10, 'Giảm đau tức thời chấn thương.', 'img/acc_4.jpg'),
('Kính Mát Thể Thao Chống UV', 250000, 7, 10, 'Chống bụi và nắng khi chạy bộ.', 'img/acc_5.jpg');

-- =======================================================
-- 3. INSERT PRODUCT DETAIL (SIÊU NHIỀU - ĐA DẠNG)
-- =======================================================
INSERT INTO ProductDetail (ProductID, Size, Color, Quantity) VALUES
-- --- NHÓM GIÀY BÓNG ĐÁ (1-5, 16) ---
-- Mercurial (ID 1): Đa dạng size/màu
(1, '39', 'Xanh Ngọc', 20), (1, '40', 'Xanh Ngọc', 30), (1, '41', 'Xanh Ngọc', 30), (1, '42', 'Xanh Ngọc', 20), (1, '43', 'Xanh Ngọc', 10),
(1, '39', 'Trắng/Vàng', 15), (1, '40', 'Trắng/Vàng', 25), (1, '41', 'Trắng/Vàng', 25), (1, '42', 'Trắng/Vàng', 15),
(1, '40', 'Đen Full', 20), (1, '41', 'Đen Full', 20), (1, '42', 'Đen Full', 20),

-- Predator (ID 2)
(2, '39', 'Đỏ/Đen', 15), (2, '40', 'Đỏ/Đen', 25), (2, '41', 'Đỏ/Đen', 30), (2, '42', 'Đỏ/Đen', 20),
(2, '40', 'Trắng/Hồng', 20), (2, '41', 'Trắng/Hồng', 20), (2, '42', 'Trắng/Hồng', 15),

-- Puma Future (ID 3)
(3, '40', 'Cam Neon', 20), (3, '41', 'Cam Neon', 25), (3, '42', 'Cam Neon', 20),
(3, '40', 'Xanh Biển', 15), (3, '41', 'Xanh Biển', 15),

-- Mizuno (ID 4) & Phantom (ID 5)
(4, '40', 'Đen/Vàng', 15), (4, '41', 'Đen/Vàng', 20), (4, '42', 'Đen/Vàng', 15),
(5, '40', 'Tím/Đen', 20), (5, '41', 'Tím/Đen', 25), (5, '42', 'Tím/Đen', 20),

-- Kamito (ID 16)
(16, '39', 'Đỏ', 20), (16, '40', 'Đỏ', 30), (16, '41', 'Đỏ', 30), (16, '42', 'Đỏ', 20),

-- --- NHÓM QUẦN ÁO BÓNG ĐÁ (8-11, 17-19) ---
-- Áo MU (ID 8)
(8, 'S', 'Đỏ', 30), (8, 'M', 'Đỏ', 50), (8, 'L', 'Đỏ', 50), (8, 'XL', 'Đỏ', 30), (8, 'XXL', 'Đỏ', 10),
-- Áo Real (ID 9)
(9, 'S', 'Trắng', 30), (9, 'M', 'Trắng', 50), (9, 'L', 'Trắng', 50), (9, 'XL', 'Trắng', 30),
-- Áo VN (ID 10)
(10, 'S', 'Đỏ', 40), (10, 'M', 'Đỏ', 60), (10, 'L', 'Đỏ', 60), (10, 'XL', 'Đỏ', 40),
-- Bộ Không Logo (ID 11) - Rất nhiều màu
(11, 'M', 'Xanh Dương', 50), (11, 'L', 'Xanh Dương', 50),
(11, 'M', 'Vàng Chanh', 50), (11, 'L', 'Vàng Chanh', 50),
(11, 'M', 'Hồng', 30), (11, 'L', 'Hồng', 30),
(11, 'M', 'Xám', 40), (11, 'L', 'Xám', 40),

-- --- NHÓM CẦU LÔNG (21-35) ---
-- Vợt Yonex (ID 21, 22) - Size là trọng lượng (U) và Cán (G)
(21, '4U-G5', 'Vàng Shine', 20), (21, '3U-G5', 'Vàng Shine', 15),
(21, '4U-G5', 'Đỏ High', 20),
(22, '4U-G5', 'Đen Nhám', 25), (22, '5U-G5', 'Đen Nhám', 15),
-- Giày Cầu Lông (ID 26-28)
(26, '40', 'Trắng/Hồng', 15), (26, '41', 'Trắng/Hồng', 20), (26, '42', 'Trắng/Hồng', 15),
(27, '40', 'Xanh/Cam', 20), (27, '41', 'Xanh/Cam', 20),
(28, '40', 'Trắng/Xanh', 20), (28, '41', 'Trắng/Xanh', 20),
-- Quần Áo Cầu Lông (ID 29-31)
(29, 'M', 'Xanh Navy', 30), (29, 'L', 'Xanh Navy', 30),
(30, 'M', 'Đen', 40), (30, 'L', 'Đen', 40),
(31, 'S', 'Trắng', 20), (31, 'M', 'Trắng', 20), (31, 'S', 'Đen', 20),

-- --- NHÓM GYM (36-55) ---
-- Tanktop & Áo Thun (ID 36, 37, 52)
(36, 'M', 'Xám', 30), (36, 'L', 'Xám', 30), (36, 'XL', 'Xám', 20),
(37, 'M', 'Đen', 40), (37, 'L', 'Đen', 40), (37, 'M', 'Xanh Rêu', 30),
(52, 'L', 'Vàng', 20), (52, 'XL', 'Vàng', 20),
-- Quần Gym (ID 38, 39, 41, 53)
(38, 'M', 'Đen', 30), (38, 'L', 'Đen', 30), (38, 'XL', 'Đen', 20),
(39, 'M', 'Xám Camo', 30), (39, 'L', 'Xám Camo', 30),
(41, 'S', 'Tím Pastel', 20), (41, 'M', 'Tím Pastel', 30), (41, 'S', 'Đen', 20),
(53, 'S', 'Đen', 20), (53, 'M', 'Đen', 20),
-- Bra & Croptop (ID 40, 42)
(40, 'S', 'Đen', 20), (40, 'M', 'Đen', 30), (40, 'S', 'Hồng', 20),
(42, 'S', 'Xanh', 20), (42, 'M', 'Xanh', 20),
-- Dụng Cụ (Size = Thông số)
(45, '8mm', 'Tím', 30), (45, '8mm', 'Hồng', 30), (45, '6mm', 'Xanh', 30),
(48, '5kg', 'Đỏ', 20), (48, '5kg', 'Xanh', 20),
(47, 'Bộ 5 dây', 'Ngũ sắc', 50),

-- --- NHÓM CHẠY BỘ (56-70) ---
-- Giày (ID 56-60, 68)
(56, '40', 'Đen/Trắng', 25), (56, '41', 'Đen/Trắng', 30), (56, '42', 'Đen/Trắng', 25),
(56, '40', 'Xanh Neon', 20), (56, '41', 'Xanh Neon', 20),
(57, '40', 'Trắng Full', 20), (57, '41', 'Trắng Full', 20),
(58, '41', 'Cam Lửa', 15), (58, '42', 'Cam Lửa', 15),
(68, '40', 'Xám', 30), (68, '41', 'Xám', 40), (68, '42', 'Xám', 30),
-- Quần Áo (ID 61, 62, 63, 69, 70)
(61, 'M', 'Xanh Chuối', 20), (61, 'L', 'Xanh Chuối', 20),
(62, 'M', 'Đen', 40), (62, 'L', 'Đen', 40),
(63, 'L', 'Cam', 20), (63, 'XL', 'Cam', 15),
(69, 'M', 'Đen', 50), (69, 'L', 'Đen', 50),
(70, 'M', 'Đen', 30), (70, 'L', 'Đen', 30),

-- --- NHÓM BÓNG RỔ (71-85) ---
-- Giày (ID 71-75)
(71, '41', 'Tím/Vàng', 15), (71, '42', 'Tím/Vàng', 20), (71, '43', 'Tím/Vàng', 15),
(72, '41', 'Đỏ/Đen', 15), (72, '42', 'Đỏ/Đen', 20),
(73, '41', 'Trắng/Xanh', 15), (73, '42', 'Trắng/Xanh', 20),
-- Quần Áo (ID 78-80)
(78, 'L', 'Vàng Lakers', 30), (78, 'XL', 'Vàng Lakers', 20),
(79, 'L', 'Xanh Warriors', 30), (79, 'XL', 'Xanh Warriors', 20),
(80, 'L', 'Đen', 40), (80, 'XL', 'Đen', 30), (80, 'XXL', 'Đen', 20),

-- --- NHÓM TENNIS (86-95) ---
-- Vợt (ID 86-88) - Size là trọng lượng
(86, '315g', 'Đen', 10), (86, '290g', 'Đen', 10),
(87, '300g', 'Vàng/Đen', 15), (87, '285g', 'Vàng/Đen', 10),
(88, '300g', 'Trắng/Đen', 15),
-- Giày (ID 89, 90)
(89, '40', 'Xanh Dương', 15), (89, '41', 'Xanh Dương', 20),
(90, '40', 'Trắng/Đỏ', 20), (90, '41', 'Trắng/Đỏ', 20),

-- --- DỤNG CỤ & PHỤ KIỆN CHUNG (Còn lại) ---
(6, 'Size 5', 'Trắng/Xanh', 50), (6, 'Size 5', 'Trắng/Đỏ', 30), -- Bóng Động Lực
(7, 'Size 5', 'Sao Vàng', 40), -- Bóng Adidas
(12, 'Size 8', 'Đỏ', 15), (12, 'Size 9', 'Đỏ', 15), -- Găng Adidas
(13, 'Size 9', 'Trắng', 15), -- Găng Nike
(14, 'Freesize', 'Trắng', 100), (14, 'Freesize', 'Đen', 100), -- Tất Aolikes
(76, 'Size 7', 'Nâu/Vàng', 60), -- Bóng rổ Molten
(77, 'Size 7', 'Cam', 60), -- Bóng rổ Spalding
(91, 'Lon', 'Vàng', 100), -- Bóng Tennis
(96, '1.5L', 'Xanh', 50), (96, '1.5L', 'Hồng', 50), -- Bình nước
(97, 'Freesize', 'Xanh', 80), (97, 'Freesize', 'Xám', 60), -- Khăn lạnh
(98, 'Freesize', 'Đen', 100), -- Túi rút
(99, 'Chai', 'Trắng', 50), -- Xịt giảm đau
(100, 'Freesize', 'Đen', 40), (100, 'Freesize', 'Trắng', 20); -- Kính mát

SET FOREIGN_KEY_CHECKS = 1; -- Bật lại kiểm tra khóa ngoại