-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: SportsStoreDB
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `Brand`
--

LOCK TABLES `Brand` WRITE;
/*!40000 ALTER TABLE `Brand` DISABLE KEYS */;
INSERT INTO `Brand` (`BrandID`, `BrandName`, `BrandDescription`, `created_at`, `updated_at`) VALUES (1,'Nike','Thương hiệu thể thao hàng đầu thế giới, tiên phong trong đổi mới công nghệ và truyền cảm hứng cho mọi vận động viên.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(2,'Adidas','Biểu tượng thể thao toàn cầu đến từ Đức, nổi tiếng với công nghệ đế Boost êm ái và thiết kế ba sọc kinh điển.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(3,'Puma','Thương hiệu thể thao Đức năng động, kết hợp hoàn hảo giữa hiệu suất tốc độ và phong cách thời trang đường phố.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(4,'Yonex','Thương hiệu Nhật Bản thống trị thế giới cầu lông với công nghệ Carbon tiên tiến và độ chính xác tuyệt đối.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(5,'Lining','Thương hiệu thể thao quốc tế cao cấp, mang đến các sản phẩm chất lượng với thiết kế thời thượng và giá thành hợp lý.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(6,'Mizuno','Thương hiệu Nhật Bản lâu đời, cam kết chất lượng hoàn hảo và sự tỉ mỉ trong từng sản phẩm chạy bộ và bóng đá.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(7,'Under Armour','Tiên phong trong trang phục hiệu suất cao, nổi tiếng với công nghệ vải co giãn và thấm hút mồ hôi vượt trội.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(8,'Wilson','Nhà sản xuất dụng cụ thể thao hàng đầu của Mỹ, chuyên về vợt Tennis và các loại bóng thi đấu chuyên nghiệp.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(9,'Molten','Thương hiệu Nhật Bản chuyên cung cấp bóng thi đấu chính thức (Official Game Ball) cho các giải đấu quốc tế lớn như FIBA.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(10,'Coolmate','Thương hiệu thời trang nam Việt Nam ứng dụng công nghệ, mang lại sự thoải mái tối đa và trải nghiệm mua sắm tiện lợi.','2025-11-23 17:20:16','2025-11-23 17:20:16');
/*!40000 ALTER TABLE `Brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Cart`
--

LOCK TABLES `Cart` WRITE;
/*!40000 ALTER TABLE `Cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `Cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `CartItems`
--

LOCK TABLES `CartItems` WRITE;
/*!40000 ALTER TABLE `CartItems` DISABLE KEYS */;
/*!40000 ALTER TABLE `CartItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Categories`
--

LOCK TABLES `Categories` WRITE;
/*!40000 ALTER TABLE `Categories` DISABLE KEYS */;
INSERT INTO `Categories` (`CategoryID`, `CategoryName`, `CategoryDescription`, `created_at`, `updated_at`) VALUES (1,'Bóng Đá','Thỏa mãn đam mê túc cầu với đầy đủ trang thiết bị từ giày đá bóng sân cỏ nhân tạo/tự nhiên, quần áo thi đấu CLB mùa giải mới, găng tay thủ môn đến các phụ kiện hỗ trợ tập luyện.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(2,'Cầu Lông','Tổng hợp dụng cụ cầu lông chính hãng bao gồm vợt trợ lực công nghệ cao, giày chuyên dụng bám sân, cầu thi đấu tiêu chuẩn và các phụ kiện như bao vợt, quấn cán.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(3,'Gym & Fitness','Kiến tạo vóc dáng hoàn hảo với hệ thống dụng cụ tập thể hình đa dạng, thảm Yoga định tuyến, tạ tay các loại và trang phục tập luyện co giãn, thấm hút mồ hôi tối ưu.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(4,'Chạy Bộ (Running)','Đồng hành trên mọi cung đường với các dòng giày chạy bộ công nghệ đệm êm ái, trang phục thoáng khí siêu nhẹ và các phụ kiện bó cơ hỗ trợ tăng thành tích marathon.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(5,'Bóng Rổ','Thế giới của các Baller với những đôi giày bóng rổ hiệu suất cao bảo vệ cổ chân, bóng thi đấu tiêu chuẩn FIBA và những set đồ Jersey đậm chất văn hóa bóng rổ.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(6,'Quần Vợt (Tennis)','Cung cấp trang thiết bị Quần Vợt đẳng cấp bao gồm vợt trợ lực kiểm soát bóng, giày tennis đế bền chống mài mòn và các phụ kiện thi đấu chuyên nghiệp.','2025-11-23 17:20:16','2025-11-23 17:20:16'),(7,'Phụ Kiện & Bảo Hộ','Tối ưu hóa hiệu suất và bảo vệ cơ thể với các phụ kiện thiết yếu như bình nước thể thao, túi xách đa năng, băng bó chấn thương và các sản phẩm hỗ trợ phục hồi.','2025-11-23 17:20:16','2025-11-23 17:20:16');
/*!40000 ALTER TABLE `Categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `InventoryLog`
--

LOCK TABLES `InventoryLog` WRITE;
/*!40000 ALTER TABLE `InventoryLog` DISABLE KEYS */;
INSERT INTO `InventoryLog` (`LogID`, `ProductDetailID`, `QuantityIn`, `QuantityOut`, `Remaining`, `Reason`, `created_at`) VALUES (1,138,0,1,49,'Bán đơn hàng #1','2025-11-23 17:25:21'),(2,137,0,7,8,'Bán đơn hàng #2','2025-11-24 07:56:46');
/*!40000 ALTER TABLE `InventoryLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `OrderDetails`
--

LOCK TABLES `OrderDetails` WRITE;
/*!40000 ALTER TABLE `OrderDetails` DISABLE KEYS */;
INSERT INTO `OrderDetails` (`OrderDetailID`, `OrderID`, `ProductDetailID`, `Quantity`, `Price`) VALUES (1,1,138,1,80000.00),(2,2,137,7,350000.00);
/*!40000 ALTER TABLE `OrderDetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Orders`
--

LOCK TABLES `Orders` WRITE;
/*!40000 ALTER TABLE `Orders` DISABLE KEYS */;
INSERT INTO `Orders` (`OrderID`, `UserID`, `GuestName`, `GuestEmail`, `GuestPhone`, `TotalAmount`, `Address`, `Status`, `VoucherID`, `Note`, `created_at`, `updated_at`) VALUES (1,1,NULL,NULL,NULL,80000.00,'123 Admin Street, Hanoi, TP. Hồ Chí Minh','delivered',8,'','2025-11-23 17:25:21','2025-11-24 07:57:35'),(2,1,NULL,NULL,NULL,2450000.00,'123 Admin Street, Hanoi, Hà Nội','delivered',NULL,'','2025-11-24 07:56:46','2025-11-24 07:57:21');
/*!40000 ALTER TABLE `Orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Product`
--

LOCK TABLES `Product` WRITE;
/*!40000 ALTER TABLE `Product` DISABLE KEYS */;
INSERT INTO `Product` (`ProductID`, `ProductName`, `Price`, `Status`, `MainImage`, `Description`, `RatingAvg`, `CategoryID`, `BrandID`, `created_at`, `updated_at`) VALUES (5,'Giày Nike Phantom GX Academy',1850000.00,'active','../assets/uploads/products/main_1764000343.jpg','Mở rộng vùng tiếp xúc bóng nhờ hệ thống dây lệch, kết hợp lớp phủ NikeSkin giúp thực hiện những cú sút xoáy hiểm hóc và kiểm soát trận đấu dễ dàng.',0.00,1,1,'2025-11-23 17:20:16','2025-11-24 16:05:43'),(6,'Bóng Động Lực UHV 2.07',550000.00,'active','../assets/uploads/products/main_1764000144.png','Bóng thi đấu chính thức tại V-League, đạt chuẩn FIFA Quality Pro. Vỏ bóng da PU cao cấp chống thấm nước, độ nảy ổn định và quỹ đạo bay chính xác.',0.00,1,5,'2025-11-23 17:20:16','2025-11-24 16:02:24'),(9,'Áo Đấu Real Madrid Home 2024',250000.00,'active','../assets/uploads/products/main_1763999854.jpg','Thiết kế hoàng gia với màu trắng kinh điển phối viền vàng sang trọng. Logo thêu sắc nét, chất liệu vải co giãn 4 chiều hỗ trợ vận động cường độ cao.',0.00,1,2,'2025-11-23 17:20:16','2025-11-24 15:57:34'),(12,'Găng Tay Thủ Môn Adidas Predator',1200000.00,'active','../assets/uploads/products/main_1763999783.png','Găng tay thủ môn chuyên nghiệp với mút URG 2.0 dính bóng như nam châm. Mặt lưng có gai cao su Demonskin hỗ trợ đấm bóng phá vây hiệu quả.',0.00,1,2,'2025-11-23 17:20:16','2025-11-24 15:56:23'),(14,'Tất Chống Trượt Aolikes',50000.00,'active','../assets/uploads/products/main_1764000473.png','Hệ thống hạt cao su dưới lòng bàn chân giúp tăng ma sát tuyệt đối với lót giày, loại bỏ tình trạng trượt chân trong giày, hạn chế phồng rộp.',0.00,1,10,'2025-11-23 17:20:16','2025-11-24 16:07:53'),(15,'Bó Gối Đá Bóng Dài',120000.00,'active','../assets/uploads/products/main_1764000566.png','Bảo vệ toàn diện khớp gối và dây chằng, giảm thiểu nguy cơ chấn thương khi va chạm. Chất liệu thun co giãn, thoáng khí, không gây hầm bí.',0.00,1,10,'2025-11-23 17:20:16','2025-11-24 16:09:26'),(21,'Vợt Yonex Astrox 77 Pro',3200000.00,'active','../assets/uploads/products/main_1764147176.png','Cây vợt thiên công mạnh mẽ được tin dùng bởi các VĐV hàng đầu. Hệ thống Rotational Generator System giúp vợt cân bằng, phục hồi nhanh sau mỗi cú đập.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:52:56'),(24,'Vợt Lining Halbertec 8000',3600000.00,'active','../assets/uploads/products/main_1764147291.png','Cây vợt cân bằng hoàn hảo, kiểm soát cầu tối ưu. Thân vợt dẻo dai giúp trợ lực tốt cho người chơi phong trào, phù hợp lối đánh điều cầu.',0.00,2,5,'2025-11-23 17:20:16','2025-11-26 08:54:51'),(26,'Giày Yonex Eclipsion Z3',2400000.00,'active','../assets/uploads/products/main_1764147353.png','Sở hữu đế Power Cushion+ độc quyền giúp hấp thụ chấn động và chuyển hóa thành năng lượng cho bước di chuyển tiếp theo. Ổn định cổ chân tuyệt vời.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:55:53'),(29,'Áo Cầu Lông Yonex Thi Đấu',450000.00,'active','../assets/uploads/products/main_1764147400.png','Công nghệ làm mát VeryCool Xylitol giúp giảm nhiệt độ cơ thể tới 3 độ C. Vải co giãn đa chiều hỗ trợ tối đa cho các động tác vung vợt.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:56:40'),(34,'Cuốn Cán Vợt Yonex (Hộp 3 cái)',120000.00,'active','../assets/uploads/products/main_1764147451.png','Chất liệu cao su non tổng hợp bám tay, thấm hút mồ hôi tốt. Giúp cầm vợt chắc chắn, tránh trơn trượt trong những pha đập cầu mạnh.',0.00,2,4,'2025-11-23 17:20:16','2025-11-26 08:57:32'),(35,'Bao Vợt Cầu Lông 2 Ngăn',450000.00,'active','../assets/uploads/products/main_1764147546.png','Có lớp lót cách nhiệt bảo vệ vợt khỏi nhiệt độ cao. Sức chứa lớn (4-6 vợt) cùng ngăn đựng giày và quần áo riêng biệt.',0.00,2,5,'2025-11-23 17:20:16','2025-11-26 08:59:06'),(36,'Áo Tanktop Nam Under Armour',450000.00,'active','../assets/uploads/products/main_1764168926.png','Thuộc bộ sưu tập Project Rock của The Rock. Thiết kế khoét nách sâu khoe trọn cơ bắp, chất vải Cotton-Poly mềm mại và thấm hút mồ hôi.',0.00,3,7,'2025-11-23 17:20:16','2025-11-26 14:55:26'),(46,'Bóng Yoga Chống Nổ',120000.00,'active','../assets/uploads/products/main_1764168648.png','Chịu lực lên đến 200kg, công nghệ chống nổ (xì hơi từ từ khi bị thủng). Hỗ trợ các bài tập thăng bằng, cơ bụng và phục hồi chức năng.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:50:48'),(50,'Dây Nhảy Thể Lực Cáp',50000.00,'active','../assets/uploads/products/main_1764168804.png','Dây cáp bọc nhựa siêu bền, trục bi xoay 360 độ giúp dây quay tốc độ cao mà không bị rối. Bài tập Cardio đốt mỡ hiệu quả nhất.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:53:24'),(51,'Bình Lắc Whey 700ml',80000.00,'active','../assets/uploads/products/main_1764168757.png','Nhựa PP an toàn sức khỏe (BPA Free). Có quả cầu lò xo inox giúp đánh tan bột Whey/Mass nhanh chóng mà không bị vón cục.',0.00,3,10,'2025-11-23 17:20:16','2025-11-26 14:52:37'),(54,'Băng Đô Headband',40000.00,'active','../assets/uploads/products/main_1763996959.png','Ngăn mồ hôi trán chảy xuống mắt gây cay mắt. Chất liệu Cotton thấm hút cực tốt, co giãn vừa vặn mọi kích cỡ đầu.',0.00,3,1,'2025-11-23 17:20:16','2025-11-24 15:09:19'),(55,'Túi Trống Gym Có Ngăn Giày',280000.00,'active','../assets/uploads/products/main_1763996905.png','Thiết kế hình trụ năng động, dung tích lớn 25L. Có ngăn đựng giày riêng biệt có lỗ thoáng khí, ngăn mùi khó chịu ám vào quần áo.',0.00,3,2,'2025-11-23 17:20:16','2025-11-24 15:08:25'),(61,'Áo Singlet Chạy Bộ Nike',450000.00,'active','../assets/uploads/products/main_1763996779.png','Áo ba lỗ siêu nhẹ (Ultra-lightweight), đục lỗ laser thoáng khí toàn thân, giảm thiểu ma sát lên da khi chạy đường dài.',0.00,4,1,'2025-11-23 17:20:16','2025-11-24 15:06:19'),(62,'Quần Short Chạy Bộ Xẻ Tà',250000.00,'active','../assets/uploads/products/main_1763995460.png','Thiết kế xẻ tà cao tối đa hóa phạm vi chuyển động của chân. Tích hợp quần lót tam giác bên trong và túi nhỏ đựng chìa khóa.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:44:20'),(63,'Áo Khoác Gió Chạy Bộ',350000.00,'active','../assets/uploads/products/main_1763971873.png','Chất liệu dù siêu mỏng nhẹ, trượt nước (Water Repellent) và chắn gió tốt. Có thể gấp gọn trong lòng bàn tay, phù hợp chạy sáng sớm.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 08:11:13'),(64,'Belt Chạy Bộ Đựng Điện Thoại',80000.00,'active','../assets/uploads/products/main_1763995205.png','Đai đeo hông ôm sát cơ thể, không rung lắc khi chạy. Đựng vừa điện thoại màn hình lớn, chìa khóa, gel năng lượng.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:40:05'),(65,'Mũ Nửa Đầu Visor',120000.00,'active','../assets/uploads/products/main_1763995129.png','Thiết kế hở đầu giúp thoát nhiệt đỉnh đầu nhanh chóng. Vành mũ rộng che nắng hiệu quả, đai thấm mồ hôi trán mềm mại.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:38:49'),(66,'Tất Chạy Bộ Xỏ Ngón',60000.00,'active','../assets/uploads/products/main_1763995035.png','Tách riêng 5 ngón chân giúp ngăn ngừa ma sát giữa các ngón, loại bỏ hoàn toàn nguy cơ phồng rộp (blister) khi chạy marathon.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:37:15'),(67,'Bó Bắp Chân Calf Compress',100000.00,'active','../assets/uploads/products/main_1763994991.png','Công nghệ nén ép (Compression) giúp tăng cường lưu thông máu, giảm rung lắc cơ bắp chân, hạn chế chuột rút và mỏi cơ.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:36:31'),(68,'Giày Chạy Bộ Coolmate',690000.00,'active','../assets/uploads/products/main_1763994932.png','Sản phẩm chạy bộ giá tốt cho người mới bắt đầu. Đế Phylon nhẹ và êm, thân giày vải dệt thoáng khí, thiết kế tối giản dễ phối đồ.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:35:32'),(69,'Áo Thun Chạy Bộ Coolmate',190000.00,'active','../assets/uploads/products/main_1763994853.png','Sử dụng công nghệ Excool độc quyền thấm hút mồ hôi và khô nhanh gấp 2 lần Cotton. Mềm mại, mát lạnh, chống tia UV.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:34:13'),(70,'Quần Legging Chạy Bộ Nam',250000.00,'active','../assets/uploads/products/main_1763994765.png','Giữ ấm cơ bắp khi chạy mùa đông. Có túi bên hông tiện lợi đựng điện thoại. Chất vải co giãn 4 chiều hỗ trợ vận động.',0.00,4,10,'2025-11-23 17:20:16','2025-11-24 14:32:45'),(71,'Giày Nike LeBron 21',4500000.00,'active','../assets/uploads/products/main_1763994589.png','Giày thửa riêng cho \"King James\". Hệ thống dây cáp 360 độ giữ chân chắc chắn, đệm Zoom Turbo đàn hồi cực tốt cho những pha tiếp đất nặng.',0.00,5,1,'2025-11-23 17:20:16','2025-11-24 14:29:49'),(72,'Giày Nike KD 16',3800000.00,'active','../assets/uploads/products/main_1763994454.jpg','Nhẹ hơn và thoáng hơn. Bộ đệm Air Zoom Strobel full-length mang lại cảm giác êm ái tức thì ngay khi xỏ chân vào.',0.00,5,1,'2025-11-23 17:20:16','2025-11-24 14:27:34'),(73,'Giày Under Armour Curry 11',4200000.00,'active','../assets/uploads/products/main_1763972480.png','Sử dụng đế UA Flow loại bỏ hoàn toàn cao su, mang lại độ bám sàn \"kinh khủng\" và trọng lượng siêu nhẹ cho những cú ném 3 điểm.',0.00,5,7,'2025-11-23 17:20:16','2025-11-24 08:21:20'),(74,'Giày Adidas Harden Vol 7',3600000.00,'active','../assets/uploads/products/main_1763972258.png','Thiết kế lấy cảm hứng từ áo khoác phao độc đáo. Đệm lai giữa Boost và Lightstrike vừa êm ái vừa phản hồi nhanh cho lối đánh Eurostep.',0.00,5,2,'2025-11-23 17:20:16','2025-11-24 08:17:38'),(75,'Giày Lining Way of Wade',3200000.00,'active','../assets/uploads/products/main_1763972080.png','Dòng giày cao cấp nhất của Lining. Công nghệ Boom (Pebax) siêu nảy, tấm Carbon chống xoắn bàn chân, thiết kế cực kỳ hầm hố.',0.00,5,5,'2025-11-23 17:20:16','2025-11-24 08:14:40'),(76,'Bóng Rổ Molten BG4500',1200000.00,'active','../assets/uploads/products/main_1763995270.png','Bóng thi đấu chính thức của FIBA. Da PU cao cấp cho độ bám dính tuyệt vời ngay cả khi tay ra mồ hôi, độ nảy chuẩn xác.',0.00,5,9,'2025-11-23 17:20:16','2025-11-24 14:41:10'),(86,'Vợt Wilson Pro Staff v14',4800000.00,'active','../assets/uploads/products/main_1764169247.png','Huyền thoại trở lại với phiên bản v14. Khung vợt ổn định, mang lại cảm giác đánh bóng cổ điển và độ chính xác tuyệt đối như Roger Federer.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:00:47'),(87,'Vợt Babolat Pure Aero',4500000.00,'active','../assets/uploads/products/main_1764169298.png','Cỗ máy tạo xoáy (Spin Machine) của Rafael Nadal. Khung vợt khí động học giúp tăng tốc độ đầu vợt, tạo ra những cú Topspin cắm sân.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:01:38'),(91,'Bóng Tennis Wilson (Lon 4 quả)',150000.00,'active','../assets/uploads/products/main_1764169357.png','Bóng thi đấu chính thức tại giải Australian Open. Lớp nỉ Optivis giúp bóng dễ nhìn hơn, độ nảy bền bỉ trên mặt sân cứng.',0.00,6,8,'2025-11-23 17:20:16','2025-11-26 15:02:37'),(92,'Áo Polo Tennis NikeCourt',550000.00,'active','../assets/uploads/products/main_1764169623.png','Phong cách lịch lãm quý tộc. Cổ áo bẻ gập gọn gàng, đường may vai lùi về sau giúp vung vợt giao bóng thoải mái không bị kích.',0.00,6,1,'2025-11-23 17:20:16','2025-11-26 15:07:03'),(93,'Váy Tennis Adidas Club',450000.00,'active','../assets/uploads/products/main_1764169591.png','Vải công nghệ AEROREADY thấm hút mồ hôi. Thiết kế cạp bản rộng tôn dáng, xếp ly xòe nhẹ tạo sự nữ tính trong từng bước chạy.',0.00,6,2,'2025-11-23 17:20:16','2025-11-26 15:06:31'),(94,'Mũ Lưỡi Trai Tennis',250000.00,'active','../assets/uploads/products/main_1764169408.png','Mặt dưới lưỡi trai màu đen giúp chống lóa mắt khi nhìn lên trời giao bóng. Công nghệ Dri-FIT giữ đầu luôn khô thoáng.',0.00,6,1,'2025-11-23 17:20:16','2025-11-26 15:03:28'),(96,'Bình Nước Thể Thao 1.5L',120000.00,'active','../assets/uploads/products/main_1764169167.png','Dung tích lớn 1.5L đảm bảo đủ nước cho cả buổi tập dài. Nhựa Tritan cao cấp chịu va đập, không chứa BPA gây hại sức khỏe.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:59:27'),(97,'Khăn Lạnh Thể Thao',60000.00,'active','../assets/uploads/products/main_1764169094.png','Công nghệ làm mát tức thì: chỉ cần nhúng nước, vắt khô và phẩy nhẹ là khăn sẽ giảm nhiệt độ sâu, giúp hạ nhiệt cơ thể nhanh chóng.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:58:14'),(98,'Túi Rút Đựng Đồ',50000.00,'active','../assets/uploads/products/main_1764169038.png','Nhỏ gọn, tiện lợi để đựng giày, quần áo bẩn hoặc đồ cá nhân lặt vặt. Dây rút chắc chắn, có thể đeo như balo nhẹ.',0.00,7,2,'2025-11-23 17:20:16','2025-11-26 14:57:18'),(99,'Xịt Giảm Đau Thể Thao',180000.00,'active','../assets/uploads/products/main_1764168990.png','Dạng xịt lạnh giúp đóng băng cảm giác đau tức thì, giảm sưng tấy cho các chấn thương phần mềm như bong gân, bầm tím.',0.00,7,10,'2025-11-23 17:20:16','2025-11-26 14:56:30'),(100,'Kính Mát Thể Thao Chống UV',250000.00,'active','../assets/uploads/products/main_1764000783.png','Thiết kế ôm sát khuôn mặt không bị rơi khi vận động mạnh. Tròng kính phân cực chống tia UV400 bảo vệ mắt khi chạy bộ dưới nắng gắt.',0.00,7,10,'2025-11-23 17:20:16','2025-11-24 16:13:03');
/*!40000 ALTER TABLE `Product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `ProductDetail`
--

LOCK TABLES `ProductDetail` WRITE;
/*!40000 ALTER TABLE `ProductDetail` DISABLE KEYS */;
INSERT INTO `ProductDetail` (`ProductDetailID`, `ProductID`, `Size`, `Color`, `Quantity`, `Image`) VALUES (17,5,'40','Trắng-Xanh',20,''),(18,5,'41','Trắng-Xanh',25,''),(19,5,'42','Trắng-Xanh',20,''),(24,6,'Size 5','Trắng-Xanh',50,'../assets/uploads/products/product_6_Trắng-Xanh_1764000173.png'),(25,6,'Size 5','Trắng-Đỏ',30,''),(31,9,'S','Trắng',30,''),(32,9,'M','Trắng',50,''),(33,9,'L','Trắng',50,''),(34,9,'XL','Trắng',30,''),(50,12,'Size 8','Đỏ',15,''),(51,12,'Size 9','Đỏ',15,''),(54,14,'Freesize','Trắng',100,''),(55,14,'Freesize','Đen',100,'../assets/uploads/products/product_14_Đen_1764000473.png'),(56,15,'M','Đen-Xanh',30,''),(57,15,'L','Đen-Xanh',30,''),(59,21,'4U','Vàng Shine',20,''),(60,21,'3U','Vàng Shine',15,''),(65,24,'4U','',20,''),(67,26,'40','Xanh',15,''),(68,26,'41','Xanh',20,''),(73,29,'M','Xanh Navy',30,''),(74,29,'L','Xanh Navy',30,''),(82,34,'Freesize','Đủ màu',200,''),(83,35,'Freesize','Đen',20,'../assets/uploads/products/product_35_Đen_1764147546.png'),(84,35,'Freesize','Trắng',20,'../assets/uploads/products/product_35_Trắng_1764147546.png'),(85,36,'L','Đen',30,''),(86,36,'XL','Xám',20,''),(109,46,'65cm','Tím',20,'../assets/uploads/products/product_46_Tím_1764168648.png'),(110,46,'75cm','Xám',20,'../assets/uploads/products/product_46_Xám_1764168648.png'),(115,50,'Freesize','Tím',50,''),(116,51,'700ml','Trắng',50,'../assets/uploads/products/product_51_Trắng_1764168757.png'),(117,54,'Freesize','Trắng',50,''),(118,55,'Freesize','Đen',30,''),(130,68,'40','Xám',30,''),(131,68,'41','Xám',40,''),(132,61,'M','Đen',20,''),(133,61,'L','Đen',20,''),(134,62,'M','Xanh',40,''),(135,62,'L','Xanh',40,''),(136,63,'L','Cam',20,''),(137,63,'XL','Cam',8,''),(138,64,'Freesize','Đen',49,''),(139,65,'Freesize','Trắng',50,''),(140,66,'Freesize','Xám',50,''),(141,67,'M','Đen',30,''),(142,69,'M','Đen',50,''),(143,69,'L','Đen',50,''),(144,70,'M','Đen',30,''),(145,70,'L','Đen',30,''),(146,71,'41','Tím-Vàng',15,''),(147,71,'42','Tím-Vàng',20,''),(148,72,'41','Đỏ-Đen',15,''),(149,72,'42','Đỏ-Đen',20,''),(150,73,'41','Trắng-Xanh',15,''),(151,73,'42','Trắng-Xanh',20,''),(152,74,'41','Đen',15,''),(153,74,'42','Đen',20,''),(154,75,'41','Hồng',15,''),(155,75,'42','Hồng',20,''),(156,76,'Size 7','cam-trắng',60,''),(169,86,'315g','Cam',10,''),(170,86,'290g','Cam',10,''),(171,87,'300g','Xanh-Đen',15,''),(177,91,'Lon','Vàng',100,''),(178,92,'M','Tím than',20,''),(179,92,'L','Tím than',20,''),(180,93,'S','Trắng',20,''),(181,93,'M','Trắng',20,''),(182,94,'Freesize','Đen',30,''),(184,96,'1.5L','Xanh',50,''),(185,96,'1.5L','Hồng',50,''),(186,97,'Freesize','Xanh',80,''),(187,97,'Freesize','Xám',60,''),(188,98,'Freesize','Đen',100,''),(189,99,'Chai','',50,''),(190,100,'Freesize','Đen',40,''),(192,100,'Freesize','Trắng',35,'../assets/uploads/products/product_100_Trắng_1764000873.png'),(193,51,'700ml','Đen',43,'../assets/uploads/products/product_51_Đen_1764168757.png');
/*!40000 ALTER TABLE `ProductDetail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Reviews`
--

LOCK TABLES `Reviews` WRITE;
/*!40000 ALTER TABLE `Reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Sessions`
--

LOCK TABLES `Sessions` WRITE;
/*!40000 ALTER TABLE `Sessions` DISABLE KEYS */;
INSERT INTO `Sessions` (`session_id`, `user_id`, `session_data`, `last_activity`) VALUES ('6bad943b9bd89de41091b2469f438613',NULL,'user_id|i:1;full_name|s:17:\"Admin Sport Store\";role|s:5:\"admin\";','2025-11-26 15:35:30');
/*!40000 ALTER TABLE `Sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` (`UserID`, `FirstName`, `LastName`, `Email`, `Phone`, `Password`, `Address`, `Status`, `Role`, `created_at`, `updated_at`) VALUES (1,'Admin','Sport Store','admin@sportstore.com','0123456789','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','123 Admin Street, Hanoi',1,'admin','2025-11-23 17:20:16','2025-11-23 17:20:16'),(2,'Nguyen Van','An','nguyenvanan@gmail.com','0987654321','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','456 Customer Street, HCMC',1,'customer','2025-11-23 17:20:16','2025-11-23 17:20:16');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `Voucher`
--

LOCK TABLES `Voucher` WRITE;
/*!40000 ALTER TABLE `Voucher` DISABLE KEYS */;
INSERT INTO `Voucher` (`VoucherID`, `VoucherCode`, `DiscountValue`, `StartDate`, `EndDate`, `Quantity`, `MinOrderValue`, `created_at`) VALUES (1,'WELCOME2025',100000.00,'2025-01-01','2025-12-31',100,500000.00,'2025-11-23 17:20:16'),(2,'SUMMER50',50000.00,'2025-06-01','2025-08-31',200,300000.00,'2025-11-23 17:20:16'),(3,'NEWYEAR2025',200000.00,'2025-01-01','2025-01-31',50,1000000.00,'2025-11-23 17:20:16'),(4,'FLASH30',30000.00,'2025-01-01','2025-12-31',500,200000.00,'2025-11-23 17:20:16'),(5,'VIP100',100000.00,'2025-01-01','2025-12-31',150,800000.00,'2025-11-23 17:20:16'),(6,'SPORT20',20000.00,'2025-01-01','2025-12-31',300,150000.00,'2025-11-23 17:20:16'),(7,'MEGA200',200000.00,'2025-01-01','2025-12-31',30,1500000.00,'2025-11-23 17:20:16'),(8,'FREESHIP',30000.00,'2025-01-01','2025-12-31',999,0.00,'2025-11-23 17:20:16'),(9,'AUTUMN150',150000.00,'2025-09-01','2025-11-30',80,900000.00,'2025-11-23 17:20:16'),(10,'BLACKFRIDAY',500000.00,'2025-11-24','2025-11-30',20,2000000.00,'2025-11-23 17:20:16'),(11,'STUDENT15',15000.00,'2025-01-01','2025-12-31',400,100000.00,'2025-11-23 17:20:16'),(12,'MEMBER250',250000.00,'2025-01-01','2025-12-31',60,1200000.00,'2025-11-23 17:20:16'),(13,'WEEKEND50',50000.00,'2025-01-01','2025-12-31',250,400000.00,'2025-11-23 17:20:16');
/*!40000 ALTER TABLE `Voucher` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-26 15:36:49
