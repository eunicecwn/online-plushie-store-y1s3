-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2025 at 07:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_shopping`
--
CREATE DATABASE IF NOT EXISTS `online_shopping` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `online_shopping`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `adminName` varchar(255) NOT NULL,
  `adminEmail` varchar(255) NOT NULL,
  `adminPassword` varchar(255) NOT NULL,
  `profilePhoto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `adminName`, `adminEmail`, `adminPassword`, `profilePhoto`) VALUES
(10001, 'KawaiiAdmin', 'kawaii@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '1745387654_logo1.png');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `categoryID` int(5) NOT NULL,
  `categoryName` varchar(20) NOT NULL,
  `categoryType` enum('Animals','Food & Drinks','Personalised','Flowers','Best for Gift','Ocean & Sea Life') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`categoryID`, `categoryName`, `categoryType`, `status`) VALUES
(10001, 'AMPHIBIANS & REPTILE', 'Animals', 'active'),
(10002, 'ARCTIC & ANTARCTIC', 'Animals', 'active'),
(10003, 'BEARS', 'Animals', 'active'),
(10004, 'BIRDS', 'Animals', 'active'),
(10005, 'BUGS & INSECTS', 'Animals', 'active'),
(10006, 'BUNNIES', 'Animals', 'active'),
(10007, 'CATS & KITTENS', 'Animals', 'active'),
(10008, 'DOGS & PUPPIES', 'Animals', 'active'),
(10009, 'DRAGONS & DINOSAURS', 'Animals', 'active'),
(10010, 'FARMYARD', 'Animals', 'active'),
(10011, 'JUNGLE & SAFARI', 'Animals', 'active'),
(10012, 'MYTHICAL CREATURES', 'Animals', 'active'),
(10013, 'WOODLAND ANIMALS', 'Animals', 'active'),
(10014, 'CLASSIC GIFTS', 'Best for Gift', 'active'),
(10015, 'BIRTHDAY GIFTS', 'Best for Gift', 'active'),
(10016, 'NEW BABY & BABY SHOW', 'Best for Gift', 'active'),
(10017, 'BOUQUET', 'Flowers', 'active'),
(10018, 'FLOWER POT', 'Flowers', 'active'),
(10019, 'CARNIFLORE', 'Flowers', 'active'),
(10020, 'PLANT', 'Flowers', 'active'),
(10021, 'FRUIT', 'Food & Drinks', 'active'),
(10022, 'VEGETABLE', 'Food & Drinks', 'active'),
(10023, 'PASTRY', 'Food & Drinks', 'active'),
(10024, 'DESSERT', 'Food & Drinks', 'active'),
(10025, 'COFFEE', 'Food & Drinks', 'active'),
(10026, 'LOBSTER & CRUSTACEAN', 'Ocean & Sea Life', 'active'),
(10027, 'WHALES & MARINE MAMM', 'Ocean & Sea Life', 'active'),
(10028, 'OCEAN FISH', 'Ocean & Sea Life', 'active'),
(10029, 'SEA INVERTEBRATES', 'Ocean & Sea Life', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `delivery`
--

CREATE TABLE `delivery` (
  `deliveryID` int(11) NOT NULL,
  `orderID` varchar(20) NOT NULL,
  `trackingNumber` varchar(50) NOT NULL,
  `shippingAddress` text NOT NULL,
  `deliveryStatus` enum('Processing','Shipped','Out for Delivery','Delivered','Failed') NOT NULL,
  `estimatedDate` date DEFAULT NULL,
  `deliveryDate` date DEFAULT NULL,
  `courierService` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(100) DEFAULT NULL,
  `recipient_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery`
--

INSERT INTO `delivery` (`deliveryID`, `orderID`, `trackingNumber`, `shippingAddress`, `deliveryStatus`, `estimatedDate`, `deliveryDate`, `courierService`, `recipient_name`, `recipient_phone`) VALUES
(19, 'ORD-2504272231720', 'TRK-680E3FCE595B6', 'PV16 13A-07 Jalan Danau Kota, 53000, Setapak', 'Processing', '2025-05-02', NULL, NULL, 'Eunice', '0103830298'),
(20, 'ORD-2504272236696', 'TRK-680E410791A31', '88, Jalan Desa Bakti, Taman Desa, 58100 Kuala Lumpur, Wilayah Persekutuan', 'Processing', '2025-05-02', NULL, NULL, 'Lucas Tan', '017-9012345'),
(21, 'ORD-2504272238999', 'TRK-680E414D8336F', '45, Lorong Merpati 3, Taman Merpati, 11600 Georgetown, Pulau Pinang', 'Processing', '2025-05-02', NULL, NULL, 'Chloe Lim', '016-4567890'),
(22, 'ORD-2504272239099', 'TRK-680E41A010D9F', 'No. 17, Jalan Bukit Bintang, 55100 Kuala Lumpur, Wilayah Persekutuan', 'Processing', '2025-05-02', NULL, NULL, 'Enna Alouette', '017-2345678'),
(23, 'ORD-2504272240017', 'TRK-680E41F43954F', '31, Lorong Bunga, Taman Bunga, 93300 Kuching, Sarawak', 'Processing', '2025-05-02', NULL, NULL, 'Grace Wong', '013-1122334'),
(24, 'ORD-2504272242529', 'TRK-680E4245BA9F0', 'No. 12, Jalan Ampang, 50450 Kuala Lumpur, Wilayah Persekutuan', 'Processing', '2025-05-02', NULL, NULL, 'Daniel Lee', '012-1234567'),
(25, 'ORD-2504272242187', 'TRK-680E427056006', '20, Taman Cempaka, 31400 Ipoh, Perak', 'Processing', '2025-05-02', NULL, NULL, 'Giselle', '011-6789012'),
(26, 'ORD-2504272243286', 'TRK-680E42A6E1F36', '75, Jalan Mahkota, Bandar Indera Mahkota, 25200 Kuantan, Pahang', 'Processing', '2025-05-02', NULL, NULL, 'Aiden Low', '016-2233445'),
(27, 'ORD-2504272245192', 'TRK-680E42FEDC206', '29, Jalan Tun Perak, 50050 Kuala Lumpur, Wilayah Persekutuan', 'Processing', '2025-05-02', NULL, NULL, 'Benjamin Chew', '018-4455667'),
(28, 'ORD-2504272246362', 'TRK-680E434497EB8', '14, Taman Sejati, 08000 Sungai Petani, Kedah', 'Processing', '2025-05-02', NULL, NULL, 'Mia Chua', '012-5566778'),
(29, 'ORD-2504272247004', 'TRK-680E43735B0DE', '67, Jalan Bukit Katil, 75450 Melaka', 'Processing', '2025-05-02', NULL, NULL, 'Liz', '019-6677889'),
(30, 'ORD-2504272247183', 'TRK-680E439F5D89C', '23, Jalan Putra 1, Taman Putra, 81200 Johor Bahru, Johor', 'Processing', '2025-05-02', NULL, NULL, 'Pochacco', '012-7766554'),
(31, 'ORD-2504272248413', 'TRK-680E43D8BE463', '101, Lorong Anggerik 5, Taman Anggerik, 50000 Kuala Lumpur', 'Processing', '2025-05-02', NULL, NULL, 'Liam Chan', '017-8899221'),
(32, 'ORD-2504272249549', 'TRK-680E440D7D1F7', '58, Jalan Kempas 2, Taman Kempas, 70400 Seremban, Negeri Sembilan', 'Processing', '2025-05-02', NULL, NULL, 'Zoe Low', '013-9988776'),
(33, 'ORD-2504272250264', 'TRK-680E443C9770C', '21, Jalan Air Putih, 25300 Kuantan, Pahang', 'Processing', '2025-05-02', NULL, NULL, 'Hello Kitty', '017-7788990');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `imageID` int(11) NOT NULL,
  `productID` varchar(20) NOT NULL,
  `imageName` varchar(255) NOT NULL,
  `is_cover` tinyint(1) DEFAULT 0 COMMENT '1 for cover image, 0 for others'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`imageID`, `productID`, `imageName`, `is_cover`) VALUES
(105, 'PROD-BD31127A', '6807d4c28a1bf.jpg', 0),
(106, 'PROD-BD31127A', '6807d4d1b4910.jpg', 1),
(107, 'PROD-BD31127A', '6807d5c9ebc6b.jpg', 0),
(108, 'PROD-218EA46B', '680986c9161cc.jpg', 1),
(109, 'PROD-218EA46B', '680986e2ac75e.jpg', 0),
(110, 'PROD-218EA46B', '680987091ea4c.jpg', 0),
(111, 'PROD-51D34E49', '680988286d153.jpg', 1),
(115, 'PROD-51D34E49', '68098b4e5fc18.jpg', 0),
(116, 'PROD-51D34E49', '68098b75a128c.jpg', 0),
(120, 'PROD-78DADB77', '680dce81bb802.jpg', 1),
(121, 'PROD-78DADB77', '680dce81df64a.jpg', 0),
(122, 'PROD-78DADB77', '680dce820a1f6.jpg', 0),
(123, 'PROD-0C4EA322', '680dcf359eaba.jpg', 1),
(124, 'PROD-0C4EA322', '680dcf35c4465.jpg', 0),
(125, 'PROD-0C4EA322', '680dcf35e0b47.jpg', 0),
(126, 'PROD-E0CE579E', '680dd4a43ee85.jpg', 1),
(127, 'PROD-E0CE579E', '680dd4a47929b.jpg', 0),
(128, 'PROD-E0CE579E', '680dd4a4b6ba4.jpg', 0),
(129, 'PROD-C4DA8D7E', '680dd50fc84a6.jpg', 1),
(130, 'PROD-C4DA8D7E', '680dd5102dad8.jpg', 0),
(131, 'PROD-C4DA8D7E', '680dd51061e62.jpg', 0),
(133, 'PROD-763E1CF1', '680dd57e05dc1.jpg', 0),
(134, 'PROD-763E1CF1', '680dd57e627c7.jpg', 0),
(135, 'PROD-C5588A16', '680dd5d7cdab8.jpg', 1),
(136, 'PROD-C5588A16', '680dd5d816455.jpg', 0),
(137, 'PROD-C5588A16', '680dd5d851221.jpg', 0),
(138, 'PROD-B0F9F741', '680dd67bed2d1.jpg', 1),
(139, 'PROD-B0F9F741', '680dd67c31b94.jpg', 0),
(140, 'PROD-B0F9F741', '680dd67c5dc27.jpg', 0),
(141, 'PROD-0DC4EFE7', '680dd7237d3bd.jpg', 1),
(142, 'PROD-0DC4EFE7', '680dd723baa4b.jpg', 0),
(143, 'PROD-0DC4EFE7', '680dd72404776.jpg', 0),
(144, 'PROD-54BD7142', '680dd7d903acd.jpg', 1),
(145, 'PROD-54BD7142', '680dd7d951453.jpg', 0),
(146, 'PROD-54BD7142', '680dd7d98d746.jpg', 0),
(147, 'PROD-58EA438C', '680dd8598c0f5.jpg', 1),
(148, 'PROD-58EA438C', '680dd859db2b2.jpg', 0),
(149, 'PROD-58EA438C', '680dd85a30834.jpg', 0),
(150, 'PROD-1EC0AF09', '680dd8b5542d0.jpg', 1),
(151, 'PROD-1EC0AF09', '680dd8b590017.jpg', 0),
(152, 'PROD-1EC0AF09', '680dd8b5d0187.jpg', 0),
(153, 'PROD-9905552A', '680dd9155993d.jpg', 1),
(154, 'PROD-9905552A', '680dd91597586.jpg', 0),
(155, 'PROD-9905552A', '680dd915cf66a.jpg', 0),
(156, 'PROD-CB685797', '680dd97609bfa.jpg', 1),
(157, 'PROD-CB685797', '680dd9764603e.jpg', 0),
(158, 'PROD-CB685797', '680dd97681dc5.jpg', 0),
(159, 'PROD-F9B11314', '680dd9e006c09.jpg', 1),
(160, 'PROD-F9B11314', '680dd9e047a20.jpg', 0),
(161, 'PROD-F9B11314', '680dd9e09eae5.jpg', 0),
(162, 'PROD-9289EC7B', '680dda55cadd2.jpg', 1),
(163, 'PROD-9289EC7B', '680dda560ce97.jpg', 0),
(164, 'PROD-9289EC7B', '680dda5653f34.jpg', 0),
(165, 'PROD-9289EC7B', '680dda5689f68.jpg', 0),
(166, 'PROD-FC537985', '680ddafca03a1.jpg', 1),
(167, 'PROD-FC537985', '680ddafcd84d0.jpg', 0),
(168, 'PROD-FC537985', '680ddafd22818.jpg', 0),
(169, 'PROD-C265E4DD', '680ddbb898823.jpg', 1),
(170, 'PROD-C265E4DD', '680ddbb8e4131.jpg', 0),
(171, 'PROD-C265E4DD', '680ddbb938065.jpg', 0),
(172, 'PROD-648DB616', '680ddc0bf1888.jpg', 1),
(173, 'PROD-648DB616', '680ddc0c4121d.jpg', 0),
(174, 'PROD-648DB616', '680ddc0c8829a.jpg', 0),
(175, 'PROD-A09E1CE9', '680ddd0652c22.jpg', 1),
(176, 'PROD-A09E1CE9', '680ddd0671d95.jpg', 0),
(177, 'PROD-A09E1CE9', '680ddd068e2ad.jpg', 0),
(178, 'PROD-861A92C1', '680de04a63736.jpg', 1),
(179, 'PROD-861A92C1', '680de04aa4fa7.jpg', 0),
(180, 'PROD-861A92C1', '680de04aed19d.jpg', 0),
(181, 'PROD-C7728EF0', '680de0b0ea16a.jpg', 1),
(182, 'PROD-C7728EF0', '680de0b13576d.jpg', 0),
(183, 'PROD-C7728EF0', '680de0b171fc8.jpg', 0),
(184, 'PROD-1459575F', '680de10cd8214.jpg', 1),
(185, 'PROD-1459575F', '680de10d24176.jpg', 0),
(186, 'PROD-1459575F', '680de10d6d177.jpg', 0),
(187, 'PROD-D4343FB6', '680de174adfa2.jpg', 1),
(188, 'PROD-D4343FB6', '680de17501abe.jpg', 0),
(189, 'PROD-D4343FB6', '680de17533fb5.jpg', 0),
(190, 'PROD-893A10E1', '680de1d2e298e.jpg', 1),
(191, 'PROD-893A10E1', '680de1d338561.jpg', 0),
(192, 'PROD-893A10E1', '680de1d38daf8.jpg', 0),
(193, 'PROD-D0E58172', '680de23514050.jpg', 1),
(194, 'PROD-D0E58172', '680de23560026.jpg', 0),
(195, 'PROD-D0E58172', '680de23590a54.jpg', 0),
(196, 'PROD-735F18C2', '680de28559ff4.jpg', 1),
(197, 'PROD-735F18C2', '680de28594b7d.jpg', 0),
(198, 'PROD-735F18C2', '680de285d2452.jpg', 0),
(199, 'PROD-931D1FE2', '680de2eed01de.jpg', 1),
(200, 'PROD-931D1FE2', '680de2ef1806b.jpg', 0),
(201, 'PROD-931D1FE2', '680de2ef66e12.jpg', 0),
(202, 'PROD-98E07A8D', '680de34927361.jpg', 1),
(203, 'PROD-98E07A8D', '680de349609f6.jpg', 0),
(204, 'PROD-98E07A8D', '680de349a11a9.jpg', 0),
(205, 'PROD-421497E1', '680de3ed717e4.jpg', 1),
(206, 'PROD-421497E1', '680de3edbbaac.jpg', 0),
(207, 'PROD-421497E1', '680de3ee12183.jpg', 0),
(208, 'PROD-636DB7A1', '680de47d28d3a.jpg', 1),
(209, 'PROD-636DB7A1', '680de47d69829.jpg', 0),
(210, 'PROD-636DB7A1', '680de47da1108.jpg', 0),
(211, 'PROD-97EE4151', '680de56fa8a24.jpg', 1),
(212, 'PROD-97EE4151', '680de56feb23d.jpg', 0),
(213, 'PROD-97EE4151', '680de570367f9.jpg', 0),
(214, 'PROD-B90F5DEB', '680de5d27ccd6.jpg', 1),
(215, 'PROD-B90F5DEB', '680de5d2ea90c.jpg', 0),
(216, 'PROD-B90F5DEB', '680de5d33f091.jpg', 0),
(217, 'PROD-3434CC2E', '680de65ea41c8.jpg', 1),
(218, 'PROD-3434CC2E', '680de65ee092d.jpg', 0),
(219, 'PROD-3434CC2E', '680de65f1f5dd.jpg', 0),
(220, 'PROD-B59114F7', '680de6e7e0204.jpg', 1),
(221, 'PROD-B59114F7', '680de6e817a5a.jpg', 0),
(222, 'PROD-B59114F7', '680de6e850fad.jpg', 0),
(223, 'PROD-B9F7693B', '680de7447a051.jpg', 1),
(224, 'PROD-B9F7693B', '680de744c27f0.jpg', 0),
(225, 'PROD-B9F7693B', '680de74501d52.jpg', 0),
(226, 'PROD-A4FF3771', '680de8bfbb928.jpg', 1),
(227, 'PROD-A4FF3771', '680de8bff3345.jpg', 0),
(228, 'PROD-A4FF3771', '680de8c04196f.jpg', 0),
(229, 'PROD-D50962ED', '680de928d5ffb.jpg', 1),
(230, 'PROD-D50962ED', '680de92926734.jpg', 0),
(231, 'PROD-D50962ED', '680de9296d2a9.jpg', 0),
(232, 'PROD-294CD6DD', '680de9a260ecf.jpg', 1),
(233, 'PROD-294CD6DD', '680de9a2a5e2a.jpg', 0),
(234, 'PROD-294CD6DD', '680de9a2d2cf1.jpg', 0),
(235, 'PROD-15AF5FD5', '680deadf98c01.jpg', 1),
(236, 'PROD-15AF5FD5', '680deadfd3df7.jpg', 0),
(237, 'PROD-15AF5FD5', '680deae01e274.jpg', 0),
(238, 'PROD-BD1E8C77', '680deb5a69c2f.jpg', 1),
(239, 'PROD-BD1E8C77', '680deb5aa40f3.jpg', 0),
(240, 'PROD-BD1E8C77', '680deb5ad0afa.jpg', 0),
(241, 'PROD-F0736E17', '680deba21e5e7.jpg', 1),
(242, 'PROD-F0736E17', '680deba24c3e7.jpg', 0),
(243, 'PROD-F0736E17', '680deba278e46.jpg', 0),
(244, 'PROD-2E9244EA', '680dec11919e0.jpg', 1),
(245, 'PROD-2E9244EA', '680dec11c1c65.jpg', 0),
(246, 'PROD-2E9244EA', '680dec11e9a1d.jpg', 0),
(247, 'PROD-620B2BE0', '680dee83bb873.jpg', 1),
(248, 'PROD-620B2BE0', '680dee83d3fd8.jpg', 0),
(249, 'PROD-620B2BE0', '680dee83e989b.jpg', 0),
(250, 'PROD-9C5D3BF4', '680deed8520bb.jpg', 1),
(251, 'PROD-9C5D3BF4', '680deed86765b.jpg', 0),
(252, 'PROD-9C5D3BF4', '680deed87a810.jpg', 0),
(253, 'PROD-AF66F954', '680def674591f.jpg', 1),
(254, 'PROD-AF66F954', '680def675ca18.jpg', 0),
(255, 'PROD-AF66F954', '680def676f9b6.jpg', 0),
(256, 'PROD-910A89EC', '680defb7cb7a0.jpg', 1),
(257, 'PROD-910A89EC', '680defb7e21ff.jpg', 0),
(258, 'PROD-910A89EC', '680defb800b52.jpg', 0),
(259, 'PROD-8B7FE5FB', '680df00e5001a.jpg', 1),
(260, 'PROD-8B7FE5FB', '680df00e683ef.jpg', 0),
(261, 'PROD-8B7FE5FB', '680df00e7b008.jpg', 0),
(262, 'PROD-BA817D57', '680df05d33c54.jpg', 1),
(263, 'PROD-BA817D57', '680df05d496f1.jpg', 0),
(264, 'PROD-BA817D57', '680df05d5d089.jpg', 0),
(265, 'PROD-6F7F23DA', '680df0f435a8d.jpg', 1),
(266, 'PROD-6F7F23DA', '680df0f44de86.jpg', 0),
(267, 'PROD-6F7F23DA', '680df0f46041a.jpg', 0),
(268, 'PROD-AAE1C227', '680df16301df1.jpg', 1),
(269, 'PROD-AAE1C227', '680df1631710f.jpg', 0),
(270, 'PROD-AAE1C227', '680df16329973.jpg', 0),
(271, 'PROD-A4C3315D', '680df1bef3b3d.jpg', 1),
(272, 'PROD-A4C3315D', '680df1bf16341.jpg', 0),
(273, 'PROD-A4C3315D', '680df1bf296f8.jpg', 0),
(274, 'PROD-FB52E5F6', '680df26fb21d4.jpg', 1),
(275, 'PROD-FB52E5F6', '680df26fcdb60.jpg', 0),
(276, 'PROD-FB52E5F6', '680df26fe1780.jpg', 0),
(277, 'PROD-F61B157B', '680df2bb798e4.jpg', 1),
(278, 'PROD-F61B157B', '680df2bb8e102.jpg', 0),
(279, 'PROD-F61B157B', '680df2bba267f.jpg', 0),
(280, 'PROD-02AA72BC', '680df4cdbab2e.jpg', 1),
(281, 'PROD-02AA72BC', '680df4cdd103d.jpg', 0),
(282, 'PROD-02AA72BC', '680df4cde6851.jpg', 0),
(283, 'PROD-575727EE', '680df55a0c2b6.jpg', 1),
(284, 'PROD-575727EE', '680df55a23101.jpg', 0),
(285, 'PROD-575727EE', '680df55a3929f.jpg', 0),
(286, 'PROD-BA0BBA7E', '680df5f40c857.jpg', 1),
(287, 'PROD-BA0BBA7E', '680df5f422254.jpg', 0),
(288, 'PROD-BA0BBA7E', '680df5f4367f2.jpg', 0),
(289, 'PROD-497AF1B0', '680df64381bbd.jpg', 1),
(290, 'PROD-497AF1B0', '680df6439902d.jpg', 0),
(291, 'PROD-497AF1B0', '680df643ad3bb.jpg', 0),
(292, 'PROD-A21AEE8E', '680df6ad0c1c7.jpg', 1),
(293, 'PROD-A21AEE8E', '680df6ad24a74.jpg', 0),
(294, 'PROD-A21AEE8E', '680df6ad39483.jpg', 0),
(295, 'PROD-742F476B', '680df74b4839f.jpg', 1),
(296, 'PROD-742F476B', '680df74b5b15a.jpg', 0),
(297, 'PROD-742F476B', '680df74b6eaa0.jpg', 0),
(298, 'PROD-4F55DE3A', '680df79f9b97e.jpg', 1),
(299, 'PROD-4F55DE3A', '680df79fb17de.jpg', 0),
(300, 'PROD-4F55DE3A', '680df79fc564a.jpg', 0),
(301, 'PROD-D0F72B09', '680df8b7767f9.jpg', 0),
(302, 'PROD-8CD47893', '680df92a9742d.jpg', 1),
(303, 'PROD-8CD47893', '680df92aad070.jpg', 0),
(304, 'PROD-8CD47893', '680df92ac1bc1.jpg', 0),
(305, 'PROD-215A006E', '680df9f6ad389.jpg', 1),
(306, 'PROD-215A006E', '680df9f6ca258.jpg', 0),
(307, 'PROD-215A006E', '680df9f6e00a9.jpg', 0),
(308, 'PROD-A532593C', '680e030c838f5.jpg', 1),
(309, 'PROD-A532593C', '680e030c99254.jpg', 0),
(310, 'PROD-A532593C', '680e030cacda9.jpg', 0),
(311, 'PROD-AD9739C1', '680e035fef61f.jpg', 1),
(312, 'PROD-AD9739C1', '680e036010cf3.jpg', 0),
(313, 'PROD-AD9739C1', '680e0360249f4.jpg', 0),
(314, 'PROD-4B67F981', '680e03e18c782.jpg', 1),
(315, 'PROD-4B67F981', '680e03e1a16a7.jpg', 0),
(316, 'PROD-4B67F981', '680e03e1b5a85.jpg', 0),
(317, 'PROD-EC04C67C', '680e044d2cc59.jpg', 1),
(318, 'PROD-EC04C67C', '680e044d42962.jpg', 0),
(319, 'PROD-EC04C67C', '680e044d56645.jpg', 0),
(320, 'PROD-D68E5245', '680e04a335517.jpg', 1),
(321, 'PROD-D68E5245', '680e04a34d213.jpg', 0),
(322, 'PROD-D68E5245', '680e04a360bae.jpg', 0),
(323, 'PROD-85F886D6', '680e053cd3a30.jpg', 1),
(324, 'PROD-85F886D6', '680e053ce7409.jpg', 0),
(325, 'PROD-85F886D6', '680e053d0728a.jpg', 0),
(326, 'PROD-70F87C62', '680e05bf589fc.jpg', 1),
(327, 'PROD-70F87C62', '680e05bf70e67.jpg', 0),
(328, 'PROD-70F87C62', '680e05bf83933.jpg', 0),
(329, 'PROD-309ABD72', '680e063243c5b.jpg', 1),
(330, 'PROD-309ABD72', '680e0632595dc.jpg', 0),
(331, 'PROD-309ABD72', '680e06326e853.jpg', 0),
(332, 'PROD-D0F72B09', '680e07648485e.jpg', 0),
(333, 'PROD-D0F72B09', '680e078174de9.jpg', 1),
(338, 'PROD-763E1CF1', '680e48df3b02b.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `memberID` int(11) NOT NULL,
  `memberName` varchar(255) NOT NULL,
  `memberEmail` varchar(255) NOT NULL,
  `memberPassword` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `memberAddress` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `profilePhoto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`memberID`, `memberName`, `memberEmail`, `memberPassword`, `phoneNumber`, `memberAddress`, `status`, `profilePhoto`) VALUES
(1, 'IU', 'ggbond@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '3232323', '', 'active', '1745387698_iu-1.jpg'),
(16, 'Arabella', 'tongjlai1109@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '1788888888', 'The zizz damansara damai', 'active', '1745738924_WhatsApp Image 2025-03-01 at 10.49.59 AM.jpeg'),
(1041, 'Enna Alouette', 'enna@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '017-2345678', 'No. 17, Jalan Bukit Bintang, 55100 Kuala Lumpur, Wilayah Persekutuan', 'active', '1745741923_f6372afb-50bc-4bcf-822a-6c3f1f6d6b46.jpg'),
(1042, 'Chloe Lim', 'chloelim@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '016-4567890', '45, Lorong Merpati 3, Taman Merpati, 11600 Georgetown, Pulau Pinang', 'active', '1745742026_ੈ✩‧₊˚.jpg'),
(1043, 'Grace Wong', 'gracewong@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '013-1122334', '31, Lorong Bunga, Taman Bunga, 93300 Kuching, Sarawak', 'active', '1745742090_36e0156b-1416-4283-938c-7d081f702b21.jpg'),
(1044, 'Lucas Tan', 'lucastan@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '017-9012345', '88, Jalan Desa Bakti, Taman Desa, 58100 Kuala Lumpur, Wilayah Persekutuan', 'active', '1745742169_8fa71a80-6bed-4369-8039-7e17d4206b3d.jpg'),
(1045, 'Daniel Lee', 'daniellee@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '012-1234567', 'No. 12, Jalan Ampang, 50450 Kuala Lumpur, Wilayah Persekutuan', 'active', '1745742409_2025.jpg'),
(1046, 'Giselle', 'giselle@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '011-6789012', '20, Taman Cempaka, 31400 Ipoh, Perak', 'active', '1745742471_giselle 2014 core pfp.jpg'),
(1047, 'Aiden Low', 'aidenlow@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '016-2233445', '75, Jalan Mahkota, Bandar Indera Mahkota, 25200 Kuantan, Pahang', 'active', '1745742603_30918e7e-21e0-46bf-9b76-58782b5a0199.jpg'),
(1048, 'Benjamin Chew', 'benjamin@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '018-4455667', '29, Jalan Tun Perak, 50050 Kuala Lumpur, Wilayah Persekutuan', 'active', '1745742691_☆.jpg'),
(1049, 'Mia Chua', 'miachua@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '012-5566778', '14, Taman Sejati, 08000 Sungai Petani, Kedah', 'active', '1745742794_‧₊˚☆˚₊‧.jpg'),
(1050, 'Liz', 'liz@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '019-6677889', '67, Jalan Bukit Katil, 75450 Melaka', 'active', '1745742834_ೀ.jpg'),
(1051, 'Pochacco', 'pochacco@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '012-7766554', '23, Jalan Putra 1, Taman Putra, 81200 Johor Bahru, Johor', 'active', '1745742965_☆ (1).jpg'),
(1052, 'Liam Chan', 'laimchan@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '017-8899221', '101, Lorong Anggerik 5, Taman Anggerik, 50000 Kuala Lumpur', 'active', '1745743160_soobin icon.jpg'),
(1053, 'Zoe Low', 'zoelow@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '013-9988776', '58, Jalan Kempas 2, Taman Kempas, 70400 Seremban, Negeri Sembilan', 'active', '1745743233_where are the girls that look like this__!.jpg'),
(1054, 'Hello Kitty', 'hellokitty@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '017-7788990', '21, Jalan Air Putih, 25300 Kuantan, Pahang', 'active', '1745743343_hello kitty☆♡.jpg'),
(1055, 'Eunice', 'cwn2308@gmail.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '0103830298', 'PV16 13A-07 Jalan Danau Kota, 53000, Setapak', 'active', '1745764087_8186c853-5315-4d60-b78e-a65b9d1d57a2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `member_carts`
--

CREATE TABLE `member_carts` (
  `memberID` int(11) NOT NULL,
  `productID` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_wishlist`
--

CREATE TABLE `member_wishlist` (
  `memberID` int(11) NOT NULL,
  `productID` varchar(255) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ordereditem`
--

CREATE TABLE `ordereditem` (
  `orderItemID` int(36) NOT NULL,
  `orderID` varchar(20) NOT NULL,
  `productID` varchar(20) NOT NULL,
  `orderItemQuantity` int(11) NOT NULL CHECK (`orderItemQuantity` > 0),
  `unitPrice` decimal(10,2) NOT NULL CHECK (`unitPrice` > 0),
  `totalPrice` decimal(10,2) NOT NULL CHECK (`totalPrice` > 0),
  `discountAmount` decimal(10,2) DEFAULT 0.00,
  `finalPrice` decimal(10,2) GENERATED ALWAYS AS (`totalPrice` - `discountAmount`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordereditem`
--

INSERT INTO `ordereditem` (`orderItemID`, `orderID`, `productID`, `orderItemQuantity`, `unitPrice`, `totalPrice`, `discountAmount`) VALUES
(44, 'ORD-2504272231720', 'PROD-A4FF3771', 1, 225.00, 225.00, 0.00),
(45, 'ORD-2504272231720', 'PROD-D68E5245', 1, 200.00, 200.00, 0.00),
(46, 'ORD-2504272236696', 'PROD-B90F5DEB', 1, 160.00, 160.00, 0.00),
(47, 'ORD-2504272236696', 'PROD-CB685797', 3, 210.00, 630.00, 0.00),
(48, 'ORD-2504272238999', 'PROD-9C5D3BF4', 1, 250.00, 250.00, 0.00),
(49, 'ORD-2504272239099', 'PROD-4F55DE3A', 4, 80.00, 320.00, 20.00),
(50, 'ORD-2504272240017', 'PROD-B0F9F741', 1, 190.00, 190.00, 10.00),
(51, 'ORD-2504272240017', 'PROD-C4DA8D7E', 1, 150.00, 150.00, 10.00),
(52, 'ORD-2504272242529', 'PROD-735F18C2', 1, 170.00, 170.00, 2.86),
(53, 'ORD-2504272242529', 'PROD-763E1CF1', 1, 190.00, 190.00, 2.86),
(54, 'ORD-2504272242529', 'PROD-85F886D6', 1, 230.00, 230.00, 2.86),
(55, 'ORD-2504272242529', 'PROD-97EE4151', 1, 160.00, 160.00, 2.86),
(56, 'ORD-2504272242529', 'PROD-A532593C', 1, 280.00, 280.00, 2.86),
(57, 'ORD-2504272242529', 'PROD-AAE1C227', 1, 400.00, 400.00, 2.86),
(58, 'ORD-2504272242529', 'PROD-D50962ED', 1, 150.00, 150.00, 2.86),
(59, 'ORD-2504272242187', 'PROD-98E07A8D', 1, 160.00, 160.00, 0.00),
(60, 'ORD-2504272243286', 'PROD-497AF1B0', 1, 50.00, 50.00, 0.00),
(61, 'ORD-2504272243286', 'PROD-8CD47893', 1, 130.00, 130.00, 0.00),
(62, 'ORD-2504272245192', 'PROD-9905552A', 2, 150.00, 300.00, 0.00),
(63, 'ORD-2504272246362', 'PROD-620B2BE0', 1, 250.00, 250.00, 1.82),
(64, 'ORD-2504272246362', 'PROD-6F7F23DA', 1, 230.00, 230.00, 1.82),
(65, 'ORD-2504272246362', 'PROD-8B7FE5FB', 1, 240.00, 240.00, 1.82),
(66, 'ORD-2504272246362', 'PROD-910A89EC', 1, 240.00, 240.00, 1.82),
(67, 'ORD-2504272246362', 'PROD-9C5D3BF4', 1, 250.00, 250.00, 1.82),
(68, 'ORD-2504272246362', 'PROD-A4C3315D', 1, 200.00, 200.00, 1.82),
(69, 'ORD-2504272246362', 'PROD-AAE1C227', 1, 400.00, 400.00, 1.82),
(70, 'ORD-2504272246362', 'PROD-AF66F954', 1, 250.00, 250.00, 1.82),
(71, 'ORD-2504272246362', 'PROD-BA817D57', 2, 240.00, 480.00, 1.82),
(72, 'ORD-2504272246362', 'PROD-F61B157B', 1, 200.00, 200.00, 1.82),
(73, 'ORD-2504272246362', 'PROD-FB52E5F6', 1, 200.00, 200.00, 1.82),
(74, 'ORD-2504272247004', 'PROD-97EE4151', 1, 160.00, 160.00, 0.00),
(75, 'ORD-2504272247183', 'PROD-309ABD72', 1, 100.00, 100.00, 0.00),
(76, 'ORD-2504272247183', 'PROD-AD9739C1', 1, 190.00, 190.00, 0.00),
(77, 'ORD-2504272248413', 'PROD-2E9244EA', 1, 250.00, 250.00, 0.00),
(78, 'ORD-2504272248413', 'PROD-648DB616', 1, 150.00, 150.00, 0.00),
(79, 'ORD-2504272248413', 'PROD-F0736E17', 1, 200.00, 200.00, 0.00),
(80, 'ORD-2504272249549', 'PROD-1EC0AF09', 1, 140.00, 140.00, 0.00),
(81, 'ORD-2504272249549', 'PROD-3434CC2E', 1, 225.00, 225.00, 0.00),
(82, 'ORD-2504272249549', 'PROD-931D1FE2', 1, 300.00, 300.00, 0.00),
(83, 'ORD-2504272250264', 'PROD-D0E58172', 1, 225.00, 225.00, 0.00),
(84, 'ORD-2504272250264', 'PROD-E0CE579E', 1, 125.00, 125.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` varchar(20) NOT NULL,
  `orderStatus` enum('Pending','Shipped','Delivered','Cancelled','Completed') NOT NULL,
  `orderDate` datetime DEFAULT current_timestamp(),
  `totalAmount` decimal(10,2) NOT NULL,
  `memberID` int(11) NOT NULL,
  `discountAmount` decimal(10,2) DEFAULT 0.00,
  `voucherID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderID`, `orderStatus`, `orderDate`, `totalAmount`, `memberID`, `discountAmount`, `voucherID`) VALUES
('ORD-2504272231720', 'Delivered', '2025-04-01 22:31:42', 425.00, 1055, 0.00, NULL),
('ORD-2504272236696', 'Completed', '2025-04-27 22:36:55', 790.00, 1044, 0.00, NULL),
('ORD-2504272238999', 'Completed', '2025-04-02 22:38:05', 250.00, 1042, 0.00, NULL),
('ORD-2504272239099', 'Completed', '2025-04-27 22:39:28', 300.00, 1041, 20.00, 15),
('ORD-2504272240017', 'Shipped', '2025-04-27 22:40:52', 320.00, 1043, 20.00, 16),
('ORD-2504272242187', 'Pending', '2025-04-27 22:42:56', 160.00, 1046, 0.00, NULL),
('ORD-2504272242529', 'Shipped', '2025-03-12 22:42:13', 1560.00, 1045, 20.00, 16),
('ORD-2504272243286', 'Pending', '2025-04-27 22:43:50', 180.00, 1047, 0.00, NULL),
('ORD-2504272245192', 'Shipped', '2025-04-27 22:45:18', 300.00, 1048, 0.00, NULL),
('ORD-2504272246362', 'Pending', '2025-04-19 22:46:28', 2920.00, 1049, 20.00, 16),
('ORD-2504272247004', 'Delivered', '2025-04-26 22:47:15', 160.00, 1050, 0.00, NULL),
('ORD-2504272247183', 'Pending', '2025-04-15 22:47:59', 290.00, 1051, 0.00, NULL),
('ORD-2504272248413', 'Shipped', '2025-04-22 22:48:56', 600.00, 1052, 0.00, NULL),
('ORD-2504272249549', 'Shipped', '2025-04-25 22:49:49', 665.00, 1053, 0.00, NULL),
('ORD-2504272250264', 'Pending', '2025-04-27 22:50:36', 350.00, 1054, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(11) NOT NULL,
  `paymentStatus` enum('Pending','Completed','Failed','Refunded') NOT NULL,
  `orderID` varchar(20) NOT NULL,
  `memberID` int(11) NOT NULL,
  `paymentDate` datetime DEFAULT current_timestamp(),
  `paymentMethod` enum('Credit Card','Debit Card','PayPal','Bank Transfer','Cash on Delivery') NOT NULL,
  `amountPay` decimal(10,2) NOT NULL CHECK (`amountPay` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productID` varchar(20) NOT NULL,
  `productName` varchar(255) NOT NULL,
  `stockQuantity` int(11) NOT NULL CHECK (`stockQuantity` >= 0),
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` > 0),
  `weight` decimal(10,2) NOT NULL,
  `length` decimal(10,2) NOT NULL,
  `width` decimal(10,2) NOT NULL,
  `height` decimal(10,2) NOT NULL,
  `adminID` int(11) DEFAULT NULL,
  `status` enum('available','delisted') NOT NULL DEFAULT 'available',
  `tags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productID`, `productName`, `stockQuantity`, `description`, `price`, `weight`, `length`, `width`, `height`, `adminID`, `status`, `tags`) VALUES
('PROD-02AA72BC', 'Amuseables Lemon', 33, 'Amuseables Lemon is sweet, not sour, and full of lemon-grove groove! A squishy citrus with tussly fur in gentle yellow tufts, this loveable lemon is dressed with zest. You\'ve got to love those brown cordy boots, that nubbly stalk and snazzy cordy leaf!', 150.00, 1.00, 20.00, 25.00, 13.00, NULL, 'available', 'Lemon,Sour,Cute'),
('PROD-0C4EA322', 'Timmy Turtle', 15, 'Timmy Turtle needs a friend to cheer him up – or someone to have a good grumble with! This soft, sturdy guy has a green textured shell, embroidered fudge fur and squeezable feet. Don\'t be fooled by the face – Timmy gives surprisingly good hugs!', 240.00, 1.00, 28.00, 20.00, 16.00, NULL, 'available', 'Timmy,Turtle'),
('PROD-0DC4EFE7', 'Ricky Rain Frog Headphones', 42, 'Sometimes life can be a lot to deal with. Ricky Rain Frog Headphones gets it. Our iconic green frog is chilling out, wearing his chunky black-and-grey headphones. With a flexible stiffened band, black disc details and chunky cord pads, these phones help Ricky focus. A relatable pal for chaotic days.', 240.00, 1.00, 15.00, 16.00, 18.00, NULL, 'available', 'Frog,Headphone,Chunky'),
('PROD-1459575F', 'Little Pup Bag Charm', 35, 'Little Pup Bag Charm is adorably tiny, and has a silver chain for a lead! This nougat pup has chocolate patches, perky ears, and a waggly tail, and proudly wears a Jellycat disc. Clip them onto your rucksack, tote or doggy bag!', 125.00, 0.50, 17.00, 6.00, 8.00, NULL, 'available', 'Charm,Tiny,Puppy'),
('PROD-15AF5FD5', 'Amuseables Sports Skateboarding', 47, 'Land tricks and cuddles with Amuseables Sports Skateboarding. In blue suedette with beige edging and a bold orange back, this board can rest on soft charcoal wheels and grey trucks. This smiling skater boasts sweet Jellycat graffiti detail, stitched screws, and fine cord legs with textured trainers.', 250.00, 1.00, 15.00, 34.00, 8.00, NULL, 'available', 'Skateboard,Cute'),
('PROD-1EC0AF09', 'Perry Polar Bear', 2, 'For the cosiest cuddles in the whole North Pole, skate on over to Perry Polar Bear! Unbelievably snuggly, this cloudy cream cub is as soft as freshly fallen snow. He sits so neatly on his beany bottom, and has the cutest bobble nose and nubbly tail. Hold him close and turn chilly days into silly days!', 140.00, 1.00, 20.00, 11.00, 9.00, NULL, 'available', 'Bear,Polar,Perry'),
('PROD-215A006E', 'Amuseables Brie', 45, 'Sacre bleu! Amuseables Brie is a chipper chunk of cheer! With a fine cordy rind, golden centre, kickity boots and big cheesy grin, this winning wedge is just the gift for your favourite foodie ami! Mwah - c\'est parfait!', 132.00, 1.00, 9.00, 22.00, 13.00, NULL, 'available', 'Cheese,Cake,Brie'),
('PROD-218EA46B', 'Horticus Hare', 10, 'Horticus Hare will never miss a call to cuddle with those ears! Soft and sweet in biscuit fur with oatmeal tummy and paws, Horticus is sat back on wide haunches, showing those big back feet and flat cream tail. The perfect companion with the sweetest face.\r\n\r\nMain Materials: Polyester\r\nInner Filling: Polyester Fibres, PE Beans\r\nHard Eye', 190.00, 0.50, 15.00, 17.00, 24.00, NULL, 'available', 'Horticus,Bunnies,Hare'),
('PROD-294CD6DD', 'Amuseables Ice Cream Cone', 55, 'Keep cool all year with Amuseables Ice Cream Cone. A soft scoop of textured cream fur sits in a short beige waffle cone with fluffy chocolate edge. With a big, stitched smile, this frozen friend has waffle cone arms wide for the sweetest hugs!', 50.00, 0.50, 14.00, 9.00, 5.00, NULL, 'available', 'Cone,Waffle'),
('PROD-2E9244EA', 'Bashful Bunny \'Peony\'', 33, 'Bashful Bunny ‘Peony’ is a sweet spring softie. Our iconic Bashful Cream Bunny is a lop-eared lovey with a pink suedette nose. Holding the stem of a pink suedette peony, this Bashful Bunny is a gorgeous gift to share love and prosperity.', 250.00, 1.00, 18.00, 8.00, 9.00, NULL, 'available', 'Bunny,Flower,Sweet,Romantic'),
('PROD-309ABD72', 'Wavelly Whale', 55, 'Wavelly Wave Inky is huggably chunky in charcoal fur, and we love that swooping back. With a snowy belly, roundy head, sturdy-soft tail and kindly eyes, this wonderful whale is surf-stunning. A beautiful gift for ocean fans, and any aspiring merfolk!', 100.00, 1.00, 8.00, 6.00, 13.00, NULL, 'available', 'Whale,Ocean,Fur'),
('PROD-3434CC2E', 'Rolie Polie Giraffe', 57, 'Rolie Polie Giraffe is ready for some serious cuddles! Beautifully tousled in custard fur, with soft toffee horns and a tiny tail, this gentle calf has trumpet-shaped hooves and waggly ears, truly a baby-friendly favourite.', 225.00, 1.00, 34.00, 11.00, 9.00, NULL, 'available', 'Fluffy,Soft,Giraffe'),
('PROD-421497E1', 'Barnabus Pig', 43, 'As scrummy and pink as strawberry pudding, Barnabus Pig is a rumply wonder. Candyfloss-soft with a big tubby tum, foldy ears and a curly tail, this suedey-snoot sweetheart is irresistibly cuddly. An affable piglet with squashy trotters and so many snuffly snuggles to share.', 150.00, 1.00, 26.00, 12.00, 8.00, NULL, 'available', 'Pig,Soft,Pink'),
('PROD-497AF1B0', 'Amuseables Pretzel', 55, 'Freckly, friendly and fresh from the bakery, Amuseables Pretzel is knotty but nice! This squishy silly is golden brown, with cocoa cord boots and stitchy salt speckles! Huggy and hearty - a brilliant breakfast buddy!', 50.00, 1.00, 18.00, 18.00, 6.00, NULL, 'available', 'Pastry,Pretzel,Salt'),
('PROD-4B67F981', 'Ollivander the Orca', 45, 'Ollivander the Orca is truly extraordinary! A black-and-cream boss in bold inky blots, with a fine dorsal fin and a rumpled cord tum, Ollivander wanders all over the ocean! With a striking white patch and a swishy split tail, this whale is ready to ride the waves!', 450.00, 2.00, 22.00, 20.00, 64.00, NULL, 'available', 'Whale,Fin'),
('PROD-4F55DE3A', 'Fabulous Fruit Strawberry', 56, 'Bursting with scrumptious stitchy seeds, it\'s Fabulous Fruit Strawberry! Squishy and stretchy in rich red fur, this merry berry has a mop of soft green leaves! Pick this poppet for sweet sundae snuggles.', 80.00, 1.00, 8.00, 7.00, 6.00, NULL, 'available', 'Strawberry,Sweet,Red'),
('PROD-51D34E49', 'Smudge Rabbit', 7, 'Smudge Rabbit is such a sleepy sweetie - always sprawling out, lazing on the bed or rug! Scrumptiously soft in oatmeal fur, this lop-eared lazer just captures the heart. An incredibly lopsy and loving companion, Smudge is a great gift for any bouncing baby. Bonnets and bobtails - we like it!\r\n\r\nMain Materials: Polyester\r\nInner Filling: Polyester Fibres, PE Beans\r\nHard Eye', 190.00, 1.00, 24.00, 11.00, 13.00, NULL, 'available', 'kawaii,sittingrabbit,baby'),
('PROD-54BD7142', 'Bashful Luxe Bunny Curly', 50, 'Bashful Luxe Bunny Curly is a curly-twirly bundle of joy! A muddle of honey curls, Curly is soft and sweet with a fluffy cream bobtail. With lop ears and a pink suedette nose, Curly is the perfect cuddling companion for comfort and adventure alike!', 200.00, 1.00, 31.00, 12.00, 9.00, NULL, 'available', 'Bunny,Curly,Fluffy'),
('PROD-575727EE', 'Amuseables Clementine', 33, 'Jolly, juicy and perfectly plump, Amuseables Clementine is good for everyone! Ostentatiously orange with textured fur peel and very fine cordy leaves and booties, this smiley citrus has a zing in its step!', 150.00, 1.00, 11.00, 13.00, 13.00, NULL, 'available', 'Juicy,Orange,Clementine'),
('PROD-58EA438C', 'Maurice Macaroni Penguin', 20, 'Maurice Macaroni is a blue-grey penguin, gorgeously squat with a round cream bib and custard-yellow tufts. With waggly flippers, stitch suedette feet and a down turned piped orange bill, Maurice is suitably serious!', 125.00, 1.00, 20.00, 13.00, 17.00, NULL, 'available', 'Penguin,Grey'),
('PROD-620B2BE0', 'Amuseables Bouquet of Flowers', 24, 'The Amuseables Bouquet of Flowers is the perfect way to say Thank You or Congratulations, or simply to cheer up a friend. A stunning bundle of fluffy blooms, with stitch centres, suedette leaves, petals in pink, cream, purple and yellow, embroidered stems and a hessian wrap, this bunch is blooming gorgeous.', 250.00, 1.00, 31.00, 23.00, 5.00, NULL, 'available', 'Bouquet,flower,propose,Valentine'),
('PROD-636DB7A1', 'Huddles Sheep', 60, 'Huddles Sheep is so, so proud, taking very good care of their teeny, tiny lamb! This gentle sheep has foldy fudge ears, a heart-shaped face and matching soft feet. Parent and baby have matching fur in scrumbly tumbled vanilla. The perfect present for any toddler welcoming a little sibling.', 250.00, 1.00, 26.00, 14.00, 10.00, NULL, 'available', 'Sheep,Lamb,Little'),
('PROD-648DB616', 'Bashful Red Love Heart Bunny', 75, 'A classic gift to share love and joy with iconic Bashful Bunny. This cloud-soft bunny has cream fur, flopsy ears and a pink suedette nose. Holding a sumptuous cranberry heart, this Bashful Bunny gives incredible hugs, from Valentine\'s Day to a special anniversary.', 150.00, 1.00, 31.00, 12.00, 9.00, NULL, 'available', 'Anniversary,Gift,Valentine,Bunny'),
('PROD-6F7F23DA', 'Amuseables Daffodil Pot', 67, 'Ring in the springtime all year round with the Amuseables Daffodil Pot. This soft beige pot has cordy boots and a friendly smile. With furry soil, long green leaves and suedette daffodils with sunny trumpets, this quirky gift is a pot of gold.', 230.00, 1.00, 29.00, 11.00, 11.00, NULL, 'available', 'Flower,Pot,Yellow'),
('PROD-70F87C62', 'Celebration Crustacean Shrimp', 44, 'A dynamo dancer in perky pink, it\'s Celebration Crustacean Shrimp! Shimmying along in a mustard pompom hat, this rumply rascal has waves of sea style! Smiley and scrumbly, with a funky fluted tail, beany bottom and and squidgy segments, this shrimp is a tickly trouper!', 200.00, 1.00, 14.00, 14.00, 11.00, NULL, 'available', 'Shrimp,Prawn,Pink'),
('PROD-735F18C2', 'Backpack Puppy', 55, 'Backpack Puppy is one intrepid dog, and they\'ve packed a rucksack that looks just like them! This oatmeal pup has a cocoa patch tummy, flopsy brown ears and a button nose. Their backpack unfastens with a cotton stripe lining – the perfect place to keep a treasure map!', 170.00, 1.00, 22.00, 9.00, 8.00, NULL, 'available', 'Puppy,backpack,cute'),
('PROD-742F476B', 'Pretty Patisserie Éclair', 5, 'Pretty Patisserie Éclair brings out everyone\'s sweet tooth! This artisan ami is scrumptiously snuggly, with puffy-soft choux pastry fur, a frilled hat of rich chocolate icing and a squidgy filling of rumpled cream! Carry this cutie wherever you go for a little piece of Paris.', 85.00, 1.00, 7.00, 12.00, 6.00, NULL, 'available', 'Dessert,Sweet,Patisserie'),
('PROD-763E1CF1', 'Alice Axolotl', 3, 'Loveably luminous, Alice Axolotl is a whole lot of kooky in hot pink floof! This ambling amphibian is bubblegum-bright, with squidgy-soft feet, a goofy grin and a supercool long fuzzy tail! Alice waggles her tufty gills as she trots along the sea bed!', 190.00, 1.20, 31.00, 10.00, 27.00, NULL, 'available', 'Axolotl,Fuzzy'),
('PROD-78DADB77', 'Ricky Rain Frog', 5, 'Ricky Rain-frog likes to sit and puzzle out the world. It’s a tricky business! He’s a ponderous, chunky, pea-green chap, and his podginess means he can sing like an opera tenor. He’s squishy, huggable – and very thoughtful indeed, especially in the mornings!', 150.00, 0.80, 15.00, 16.00, 18.00, NULL, 'available', 'Frog,Chunky,Squishy'),
('PROD-85F886D6', 'Love-Me Lobster', 55, 'Love-Me Lobster is delightfully dorky and full of crustacean cuddles! This joyful chum has suedey heart claws and a matching tail in plum-purple fur. With squashy segments, waggly legs and perky antennae, there\'s a whole lot to love. Get you a lobster that greets you like this!', 230.00, 1.00, 15.00, 7.00, 5.00, NULL, 'available', 'Lobster,Crustacean'),
('PROD-861A92C1', 'Little Kitten', 52, 'Little Kitten may be diddy, but this squat sweetie is mighty! Lavender-blue and utterly loveable, this tufty scamp likes to hide in your pocket! With a long fuzzy tail and snowy snoot, Little Kitten is the cat\'s whiskers!', 120.00, 0.50, 18.00, 10.00, 8.00, NULL, 'available', 'Little,Kitten,Cute,Fluffy'),
('PROD-893A10E1', 'Tilly Golden Retriever', 27, 'Tilly Golden Retriever is a soft biscuit pup with firm beanie paws and sits well on golden haunches. Floppy-eared with big brown eyes and a liquorice suedette nose, this retriever is coming home!', 190.00, 1.00, 27.00, 16.00, 22.00, NULL, 'available', 'Golden,Dog'),
('PROD-8B7FE5FB', 'Amuseables Tulip Pot', 66, 'Amuseables Tulip Pot is cute, cheery, and very easy to care for! This gorgeous workmate has a beige felt pot, fudge cord boots and soft suedette leaves. With hot-pink and cherry-red textured tulips, this plant brightens any desk.', 240.00, 1.00, 30.00, 11.00, 11.00, NULL, 'available', 'Tulip,Pot,Plant'),
('PROD-8CD47893', 'Amuseables Coffee Cup', 67, 'Amuseables Coffee Cup is a classic blend of rich textures and delicious colours, with notes of quirky charm! This charcoal cup perks up any desk, filled with a suedette ombre latte. With corduroy boots, a cheery smile and a Jellycat stencil embroidered on top, this cup is full of beans!', 130.00, 1.00, 14.00, 10.00, 9.00, NULL, 'available', 'Coffee,Roasted,Bean,Cup'),
('PROD-910A89EC', 'Amuseables Daisy', 55, 'Freshen up your space with Amuseables Daisy! This trio of flowers have golden fur faces, suedette fold petals and deep green stalks. Sat in a beautiful yellow linen pot with mocha soil, these peaceful flowers make chill companions.', 240.00, 1.00, 34.00, 11.00, 11.00, NULL, 'available', 'Daisy,Flower,Pot'),
('PROD-9289EC7B', 'Featherful Swan', 54, 'Featherful Swan is a very proud parent, protecting twin cygnets with their fluffy cream wings! This elegant swan has rich vanilla fur, set off by orange suedette feet and a matching beak with piping detail. The babies snuggled on their snowy back can pop out to explore by themselves!', 400.00, 2.00, 39.00, 14.00, 18.00, NULL, 'available', 'Swan,Fluffy,Feather'),
('PROD-931D1FE2', 'Persimmon Dragon', 24, 'Persimmon Dragon is stretching out so we can admire that vibrant fur! This warm coral dragon is carefully detailed with delicate stitching all over. With indigo wings, ears, spines and tail dart, flopsy paws and a long, weighted tail, Persimmon brightens up any nursery.', 300.00, 2.00, 12.00, 14.00, 50.00, NULL, 'available', 'Dragon,Orange,Fluffy'),
('PROD-97EE4151', 'Delia Duck', 56, 'Head down to the pond to find Delia Duck. Super soft in cream fur, Delia has a matching tuft of long hair and short, wide wings. With bright orange feet and a matching sad beak, this grumpy duck prefers cuddles over bread.', 160.00, 1.00, 23.00, 14.00, 16.00, NULL, 'available', 'Duck,Orange,Yellow'),
('PROD-98E07A8D', 'Bashful Dragon', 30, 'Bashful Dragon is a fierce little friend, with plenty of attitude! With supersoft fur in pale sage green, suedey horns and a long, squidgy tail, this brilliant beastie is pretty fantastic. We love those neat little contrast wings, chunky feet and fine flappy ears - what a legend!', 160.00, 1.00, 31.00, 12.00, 9.00, NULL, 'available', 'Dragon,Green,Fur'),
('PROD-9905552A', 'Bartholomew Bear', 23, 'Tawny-tousled and full of softness, Bartholomew Bear is the perfect bedtime buddy. Read him a story, sing him a teddy bear lullaby, or maybe just rest on his fluffy toffee tummy - he’s adorably podgy!', 150.00, 1.00, 26.00, 12.00, 8.00, NULL, 'available', 'Soft,Bear,Teddy'),
('PROD-9C5D3BF4', 'Amuseables Rose Bouquet', 54, 'Amuseables Rose Bouquet is the floral gift that keeps on giving. A trio of soft red roses in full bloom with green suedette leaves and stems with stitched detail are wrapped up in beige linen. Tied with a ribbon and wearing an embroidered smile, this wrap has fine cord arms wide for thorn-free cuddles.', 250.00, 1.00, 30.00, 17.00, 6.00, NULL, 'available', 'Rose,Valentine,Propose,Floral'),
('PROD-A09E1CE9', 'Napping Nipper Cat', 25, 'If you lift Napping Nipper Cat up, be very careful not to wake them! This gentle kitten has cloudy-soft fur in soothing pebble grey, with a cream-splash snoot and matching perky ears. All curled up in a cordy blue bed with beautiful buttercream lining, someone\'s dreaming of scruffles and salmon.', 140.00, 1.00, 10.00, 15.00, 12.00, NULL, 'available', 'Cat,Nap,Soft'),
('PROD-A21AEE8E', 'Vivacious Vegetable Bok Choy', 55, 'Vivacious Vegetable Bok Choy is a sturdy scamp in stretchy fur! Chunky, cheery and huggably hearty, with a mighty crown of stitchy leaves, this veggie has a cool ombre stalk fading from green to cream. Pop this stir-fry silly on the worktop and cook along together!', 110.00, 1.00, 18.00, 7.00, 6.00, NULL, 'available', 'Vege,Leave,Veggie'),
('PROD-A4C3315D', 'Amuseables Bird of Paradise', 33, 'Amuseables Bird of Paradise is an elegant plant with a teal suedette pot filled with fluffy cocoa soil. With sturdy green stalks, fluted leaves, and red, green, and purple flowers with fuzzy orange petals, this plant makes any space divine!', 200.00, 2.00, 38.00, 17.00, 14.00, NULL, 'available', 'Plant,Pot,Colourful'),
('PROD-A4FF3771', 'Bashful Bunny Musical Pull', 33, 'An enchanting newborn gift, let babies dream sweetly with a Bashful Silver Bunny Musical Pull. Our most loved Bashful Bunny, in soft, silver-grey fur made from recycled fibres can be gently pulled to hear an original Jellycat lullaby. The cream star shaped musical pull with contrast trim can be attached to a crib or pram with a Velcro fastening.', 225.00, 1.00, 30.00, 20.00, 6.00, NULL, 'available', 'Newborn,Gift,Bunny,Star'),
('PROD-A532593C', 'Odyssey Octopus', 55, 'Dive to the depths for high adventure with wonderful Odyssey Octopus! Squishably soft in sea-moss green, with eight super-squiggly springy arms, Odyssey loves to give cordy cuddles. A splendid friend for any merfolk!', 280.00, 1.50, 47.00, 19.00, 19.00, NULL, 'available', 'Octopus,sea,ocean'),
('PROD-AAE1C227', 'Amuseables Monstera Plant', 22, 'Make an office statement with the Amuseables Monstera Plant! This kooky plant has a suedette pot in warm terracotta, latte soil and gorgeous stitched leaves with suedette stems. With corduroy boots and a merry smile, this Monstera is rooting for you!', 400.00, 2.00, 43.00, 15.00, 14.00, NULL, 'available', 'Plant,Green,Pot'),
('PROD-AD9739C1', 'Spindleshanks Crab', 55, 'Ahh, it\'s so nice to flop on the beach. Spindleshanks Crab has popped out of the rockpool to doze and laze on the soft, warm sand. This rust-red rascal has a nuzzly shell and lots of fuzzy two-tone legs. Spindleshanks may have two chunky claws, but they\'re only for holding ice-creams!', 190.00, 1.00, 10.00, 40.00, 36.00, NULL, 'available', 'Crab,beach,Shell'),
('PROD-AF66F954', 'Carniflore Tammie', 32, 'Carniflore Tammie is a pot of delight, with a tangerine grin, soft stitch fangs, green stitched leaves and a shock of orange hair. Kooky Tammie sits in her chocolate pot, waving her tendrils at all who walk by.', 250.00, 1.00, 27.00, 8.00, 8.00, NULL, 'available', 'Plant,Cute,Carniflore'),
('PROD-B0F9F741', 'Forest Fauna Frog', 3, 'Forest Fauna Frog was excited to find such a fine scarlet toadstool! With a sturdy suedey stalk and squishy cranberry cap, it\'s just the spot to curl up for a snooze. This stretch mossy sweetie bounced out of the pond to claim this speckled nook all for themselves!', 190.00, 0.80, 20.00, 11.00, 8.00, NULL, 'available', 'Frog,Forest,Mushroom'),
('PROD-B59114F7', 'Isadora Unicorn', 34, 'A dreamy sight in clotted-cream fur, Isadora Unicorn is enchanting. With a plume mane and tail, silky feather and a pink twist horn stitched with silver, she trots through the forest on blush suedette hooves!', 350.00, 2.00, 32.00, 11.00, 32.00, NULL, 'available', 'Unicorn,Pink,Dreamy'),
('PROD-B90F5DEB', 'Bashful Snow Tiger', 52, 'Isn\'t Bashful Snow Tiger Medium majestic? Stalking through deep forests in grey and creamy stripes, this fierce-but-friendly wanderer isn\'t scared of anything! Nuzzle up to those big squishy paw, a silky, tufty ruff and pebbly nose – who needs a guard dog?', 160.00, 1.00, 31.00, 12.00, 9.00, NULL, 'available', 'Snow,Tiger,Grey,Stripes'),
('PROD-B9F7693B', 'Snowy Owling', 33, 'Snowy Owling is a scrumptious bundle of vanilla fluff. This blue-eyed baby is dreamily soft, with the tiniest wings and grey suedette feet. With a tiny pip beak and a sweet textured face, this cloud of an owl is adorable.', 60.00, 0.50, 11.00, 7.00, 6.00, NULL, 'available', 'Owl,Snow,Tiny,Cute'),
('PROD-BA0BBA7E', 'Vivacious Vegetable Aubergine', 33, 'Vivacious Vegetable Aubergine just arrived in a very special veg box! Stretchy-soft in blush plum fur, with a sweet friendly smile and a green stalk hat, this one is scrumptiously squishable! Plump and perfect with a beany bottom, this aubergine is so serene!', 90.00, 1.00, 16.00, 9.00, 9.00, NULL, 'available', 'Brinjal,Eggplant,Vege'),
('PROD-BA817D57', 'Carniflore Priscilla', 33, 'Forgetful with plants? Carniflore Priscilla is your girl. This stunning green diva has a grey suedette pot, fluffy brown soil and scalloped stitch leaves. With a plump bulb head, neon tuft hairdo and pink lips embroidered with fangs, Priscilla\'s looking fly!', 240.00, 1.00, 29.00, 8.00, 8.00, NULL, 'available', 'Carniflore,Plant,Pot'),
('PROD-BD1E8C77', 'Amuseables Jellina Birthday Cake', 55, 'Amuseables Jellina Birthday Cake is baked to perfection. With layers of fluffy sponge in gradient orange shades, a pair of brown cordy boots and a stitched smile, Jellina has been iced in fluffy cream fur, and decorated with stitched rainbow sprinkles and a striped, lit candle. The perfect celebration gift for Jellycat friends and fans alike!', 200.00, 1.00, 13.00, 15.00, 15.00, NULL, 'available', 'Cake,Birthday,Colourful'),
('PROD-BD31127A', 'Timmy Turtle \'Garden Gnome\'', 12, 'Timmy Turtle is a garden gem. Our favourite grumpy turtle wears a deep blue tunic and brown belt tucked into his green shell. With a red hat and fluffy cream beard, Timmy is the grumpiest gnome you’ll ever meet!\r\nMain Materials: Polyester\r\nInner Filling: Polyester Fibres, PE Beans\r\nHard Eye', 275.00, 1.00, 20.00, 16.00, 28.00, NULL, 'available', 'Turtle,Timmy'),
('PROD-C265E4DD', 'Bashful Beige Bunny \'Birthday\'', 55, 'Invite Bashful Beige Bunny ‘Birthday’ along for the most joyful celebrations. Soft and sweet in beige fur, our iconic Bashful Bunny is holding a tiny, textured Amuseables Birthday Cake, topped with soft cream icing, velveteen strawberries, and a striped candle already lit with a suedette flame.', 250.00, 1.00, 31.00, 12.00, 9.00, NULL, 'available', 'Birthday,Cake,Bunny,Gift'),
('PROD-C4DA8D7E', 'Flumpie Frog', 5, 'Flumpie Frog is the grinningest scalliwag you ever did see! What\'s tickled this tousled chum? We don\'t know for sure, but those boggly bright eyes, diddy arms and legs, tufty fern coat and merry face are bound to win anyone around! Just try resisting it!', 150.00, 1.00, 20.00, 15.00, 11.00, NULL, 'available', 'Frog,Flumpie'),
('PROD-C5588A16', 'Little Frog', 25, 'Here\'s Little Frog, lining up for the first gentle hop of the day! This mossy moppet is just so tufty and scrumbly-soft, with such a small, secret smile – like a tiny tussock of joy. Look at those teensy arms and legs too! Count to one, two, three, and let this frog jump into your hands!', 125.00, 0.60, 18.00, 10.00, 8.00, NULL, 'available', 'Little,Frog,Cute'),
('PROD-C7728EF0', 'Spookipaws Cat', 12, 'Spookipaws Cat looks somewhat surprised. This spooked kitten has a slinky arched back, a sleek curly tail and bright emerald eyes, with bobbly paws and soft suedette claws! A quirky, stylish and spooky companion for someone a little bit different.', 160.00, 1.00, 20.00, 7.00, 25.00, NULL, 'available', 'Cat,Black,Fluffy'),
('PROD-CB685797', 'Bartholomew Bear \'Bumblebee\'', 13, 'Bartholomew Bear ‘Bumblebee’ is all snuggle, no sting. Our most-loved toffee bear is spreading springtime joy in a stripey bee costume, which can be removed. With a fixed antennae headband, this is a bear you can snuggle for all seasons.', 210.00, 1.50, 26.00, 14.00, 14.00, NULL, 'available', 'Bear,Bee'),
('PROD-D0E58172', 'Munro Scottie Dog \'Love You\'', 55, 'Munro Scottie Dog has a special delivery! Our shaggy cream pup has a black suedette nose, and is holding on tight to a red tartan rose, with a soft green stem. Munro promises to love you until the last petal falls!', 225.00, 1.00, 25.00, 12.00, 23.00, NULL, 'available', 'Scottie,Dog,Flower'),
('PROD-D0F72B09', 'Amuseables Espresso Cup', 66, 'Amuseables Espresso Cup is mini but mighty, with a huge personality! Sparky and soft, this creamy cup holds a scrummy serving of rich, fuzzy coffee. With adorable chocolatey cordy feet this espresso is always on the go!', 100.00, 1.00, 10.00, 5.00, 5.00, NULL, 'available', 'Coffee,Espresso'),
('PROD-D4343FB6', 'Otto Sausage Dog', 30, 'Tiny legs and a great big heart - that\'s Otto Sausage Dog! This intrepid pup is divinely dinky in dark chocolate fur with caramel patches. With waggly ears, a perky tail, golden eyebrows and sassy spirit, bright-eyed Otto\'s a playful little pal!', 165.00, 1.00, 19.00, 9.00, 29.00, NULL, 'available', 'Sausage,Dog,Cute'),
('PROD-D50962ED', 'Amuseables Cloud Soother', 44, 'The Amuseables Cloud Soother brings smiles whatever the weather, rolled up and presented in a Baby Jellycat grosgrain ribbon. Both cream cloud and blue soother are made with the softest recycled fibres. With cheery embroidered eyes and smile to brighten every day, it makes for a perfect newborn gift.', 150.00, 1.00, 9.00, 34.00, 34.00, NULL, 'available', 'Blanket,Soft,Cloud'),
('PROD-D68E5245', 'Alexis Anglerfish', 55, 'Alexis Anglerfish is gorgeously podgy in terracotta fur. Alexis comes from the deepest ocean – that\'s why they carry a cool suedette torch! With huge bobble eyes, stitched nose and mouth, and glorious golden segmented fins, this fish is a light in the dark.', 200.00, 1.00, 14.00, 12.00, 21.00, NULL, 'available', 'Fist,Angry,Ocean'),
('PROD-E0CE579E', 'Little Snake', 10, 'Little Snake is inquisitive, and likes to chat to the people they meet. This bright-eyed snake has the cutest stitch smile and a wriggly weighted tummy, and sits up proudly in green and cream curls, waving their tail in greeting.', 125.00, 0.50, 16.00, 8.00, 21.00, NULL, 'available', 'Snake,Cute'),
('PROD-EC04C67C', 'Pacey Pufferfish', 55, 'Eep – looks like Pacey Pufferfish has had a big surprise! This dizzy dumpling is perfectly podgy, with a marshmallow tummy and seagrass fur. With stitchy fins, nuzzly knobbles, squidgy lips and boggly eyes, this funny fish is a bubble of cuddles! Chill out Pacey – go with the flow!', 200.00, 1.00, 16.00, 12.00, 23.00, NULL, 'available', 'Fish,Cute'),
('PROD-F0736E17', 'Amuseables Birthday Cake', 55, 'Amuseables Birthday Cake is snuggly-scrumptious, with tussly fur so beautifully baked. This party poppet wears a squashy hat of heavenly splodgy buttercream, topped with velvety strawberries and a striped jersey candle. Kick up those cordy boots and let\'s celebrate!', 200.00, 1.00, 16.00, 12.00, 12.00, NULL, 'available', 'Birthday,Cake,Candle'),
('PROD-F61B157B', 'Amuseables Pink Orchid', 33, 'Amuseables Pink Orchid brightens up any room! Bold pink flowers with pale orange columns sit on sturdy, suedette stalks with matching leaves, all nestled in a pot of fuzzy cocoa soil. In a trendy, two-tone pot, this sweet orchid has charcoal fine cord legs, and a big, stitched smile to bring joy wherever it sits.', 200.00, 1.00, 25.00, 10.00, 11.00, NULL, 'available', 'Orchid,Pink,Pot,Flower'),
('PROD-F9B11314', 'Little Panda', 25, 'Little Panda may have diddy paws, but this bitsy bear has captured our hearts. A black-and-cream bobbin of tussly softness, with scruffly ears and a podgy paunch, this panda\'s both mini and mighty! Pop a bear in your bag, on your desk or in your arms.', 120.00, 0.50, 18.00, 10.00, 8.00, NULL, 'available', 'Panda,Fluffy,Cute,Little'),
('PROD-FB52E5F6', 'Amuseables Sunflower', 34, 'Add a little sunshine to your day with Amuseables Sunflower. This trio of flowers have brown fur faces, bright yellow petals and deep green stalks. Sat in a beautiful brown linen pot with mocha soil, these bright flowers make every day a little sunnier.', 200.00, 1.00, 35.00, 11.00, 11.00, NULL, 'available', 'Sunflower,Yellow,Pot'),
('PROD-FC537985', 'Spindleshanks Spider', 5, 'Beautifully fluffy and not at all scary, it\'s elegant Spindleshanks Spider! This cheery arachnid has a beany thorax and curved cocoa legs in smooth and tufty furs. Spindleshanks loves to sit dreaming in their web, their bright eyes watching the world spin on.', 170.00, 1.00, 7.00, 35.00, 17.00, NULL, 'available', 'Spider,fluffy');

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `productID` varchar(20) NOT NULL,
  `categoryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`productID`, `categoryID`) VALUES
('PROD-02AA72BC', 10021),
('PROD-0C4EA322', 10001),
('PROD-0DC4EFE7', 10001),
('PROD-1459575F', 10008),
('PROD-15AF5FD5', 10015),
('PROD-1EC0AF09', 10002),
('PROD-215A006E', 10024),
('PROD-218EA46B', 10001),
('PROD-218EA46B', 10002),
('PROD-218EA46B', 10006),
('PROD-294CD6DD', 10014),
('PROD-2E9244EA', 10014),
('PROD-309ABD72', 10027),
('PROD-3434CC2E', 10011),
('PROD-421497E1', 10010),
('PROD-497AF1B0', 10023),
('PROD-4B67F981', 10027),
('PROD-4F55DE3A', 10021),
('PROD-51D34E49', 10001),
('PROD-51D34E49', 10002),
('PROD-51D34E49', 10006),
('PROD-54BD7142', 10006),
('PROD-575727EE', 10021),
('PROD-58EA438C', 10002),
('PROD-620B2BE0', 10017),
('PROD-636DB7A1', 10010),
('PROD-648DB616', 10014),
('PROD-6F7F23DA', 10018),
('PROD-70F87C62', 10026),
('PROD-735F18C2', 10008),
('PROD-742F476B', 10024),
('PROD-763E1CF1', 10001),
('PROD-78DADB77', 10001),
('PROD-85F886D6', 10026),
('PROD-861A92C1', 10007),
('PROD-893A10E1', 10008),
('PROD-8B7FE5FB', 10018),
('PROD-8CD47893', 10025),
('PROD-910A89EC', 10018),
('PROD-9289EC7B', 10004),
('PROD-931D1FE2', 10009),
('PROD-97EE4151', 10010),
('PROD-98E07A8D', 10009),
('PROD-9905552A', 10003),
('PROD-9C5D3BF4', 10017),
('PROD-A09E1CE9', 10007),
('PROD-A21AEE8E', 10022),
('PROD-A4C3315D', 10020),
('PROD-A4FF3771', 10016),
('PROD-A532593C', 10029),
('PROD-AAE1C227', 10020),
('PROD-AD9739C1', 10029),
('PROD-AF66F954', 10019),
('PROD-B0F9F741', 10001),
('PROD-B59114F7', 10012),
('PROD-B90F5DEB', 10011),
('PROD-B9F7693B', 10013),
('PROD-BA0BBA7E', 10022),
('PROD-BA817D57', 10019),
('PROD-BD1E8C77', 10014),
('PROD-BD1E8C77', 10015),
('PROD-BD31127A', 10014),
('PROD-C265E4DD', 10015),
('PROD-C4DA8D7E', 10001),
('PROD-C5588A16', 10001),
('PROD-C7728EF0', 10007),
('PROD-CB685797', 10003),
('PROD-D0E58172', 10008),
('PROD-D0F72B09', 10025),
('PROD-D4343FB6', 10008),
('PROD-D50962ED', 10016),
('PROD-D68E5245', 10028),
('PROD-E0CE579E', 10001),
('PROD-EC04C67C', 10028),
('PROD-F0736E17', 10015),
('PROD-F61B157B', 10018),
('PROD-F9B11314', 10003),
('PROD-FB52E5F6', 10018),
('PROD-FC537985', 10005);

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receiptNo` varchar(20) NOT NULL,
  `paymentID` int(11) DEFAULT NULL,
  `orderID` varchar(20) NOT NULL,
  `amountPay` decimal(10,2) NOT NULL,
  `receiptStatus` enum('Generated','Sent','Cancelled') DEFAULT 'Generated',
  `receiptDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `staffName` varchar(255) NOT NULL,
  `staffEmail` varchar(255) NOT NULL,
  `staffPassword` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staffID`, `staffName`, `staffEmail`, `staffPassword`, `phoneNumber`, `status`) VALUES
(101, 'Julie', 'julie@kawaii.com', '88ea39439e74fa27c09a4fc0bc8ebe6d00978392', '01123445566', 'active'),
(103, 'Muhammad Hakim bin Ahmad', 'muhammad.hakim@kawaii.com', '$2y$10$j3D/n5Wdgw2uIEnkjtIT.egdySehRcbaX2J0wRG/YvvCG8pmBebK6', '0121122334', 'active'),
(104, 'Muhammad Amirul bin Hafiz', 'muhammad.amirul@kawaii.com', '$2y$10$cNuziGoOBTQjwPwLl0PPRe5yJFHSjgPXIU5879Pd7xz9HJTcj/ki6', '0176566678', 'active'),
(105, 'Yasmin Hanani binti Ismail', 'yasmin.hanani@kawaii.com', '$2y$10$d/fnILBBmjdrhH1eloX.kuXda0N8fWXTZvBpy9t9CDNz7roSPBzQ2', '0195455567', 'active'),
(106, 'Tan Jia Wen', 'tan.jia.wen@kawaii.com', '$2y$10$f9ojlTo1eqGlBANf/XHyLOlfWd8JL1GTdZAosj1nT7zfHYb3GMRW6', '0124344456', 'active'),
(107, 'Rajesh Kumar a/l Maniam', 'rajesh.kumar@kawaii.com', '$2y$10$/R6.6DlhqheucHl3ybieieXAPLoTNz/MKc1.PDrs72IMcBKWgrI9u', '0113233345', 'active'),
(108, 'Goh Jia Hui', 'goh.jia.hui@kawaii.com', '$2y$10$ln9pQFjXxB2PCxmGSpEQUOKkjcFSatAn4LyXNE2jpDHv3ellDM5KC', '0162122234', 'active'),
(109, 'Nur Syafiqah binti Rahman', 'nur.syafiqah@kawaii.com', '$2y$10$x5GzY0w/Bd7uKFdqjFTf.eStB5CEWm67Wqx1rquWkIVfHrllKrlUS', '0131011123', 'active'),
(110, 'Aiman Hakimi bin Zainal', 'aiman.hakimi@kawaii.com', '$2y$10$pjjyjGLMyqIcgPi52WXaAOUiHkmF49mX22GdwsVugqOAuPt08Nhdu', '0179900112', 'active'),
(111, 'Chong Li Xuan', 'chong.li.xuan@kawaii.com', '$2y$10$TOky4ye6aJ6AFh1iMAoK1.crQWCcUb8GtH5wc8Fsk2jjwxLP/77Ku', '0198899001', 'active'),
(112, 'Nurul Aina binti Zulkifli', 'nurul.aina@kawaii.com', '$2y$10$39cbCSJ5bBDaZ8anSD0B7.tpGjDS/5HQD4A9H/wZtEVWmF8MzmbCq', '0172233445', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` varchar(255) NOT NULL,
  `expire` datetime NOT NULL,
  `memberID` int(11) NOT NULL,
  `type` enum('reset_password','account_activation') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `voucherID` int(11) NOT NULL,
  `voucherCode` varchar(50) NOT NULL,
  `discountAmount` decimal(10,2) NOT NULL,
  `usageLimit` int(11) NOT NULL DEFAULT 1,
  `timesUsed` int(11) NOT NULL DEFAULT 0,
  `expiryDate` datetime DEFAULT NULL,
  `createdDate` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Expired','Disabled') NOT NULL DEFAULT 'Active'
) ;

--
-- Dumping data for table `voucher`
--

INSERT INTO `voucher` (`voucherID`, `voucherCode`, `discountAmount`, `usageLimit`, `timesUsed`, `expiryDate`, `createdDate`, `status`) VALUES
(5, 'DDFDF', 20.00, 1, 0, NULL, '2025-04-21 23:19:29', 'Disabled'),
(6, '20222', 10.00, 1, 0, '2025-04-21 23:24:00', '2025-04-21 23:23:16', 'Expired'),
(7, 'DDDDD', 111.00, 30, 0, '2025-04-22 23:41:00', '2025-04-21 23:41:42', 'Expired'),
(8, '11111', 111.00, 111, 0, '2025-04-22 12:42:00', '2025-04-21 23:42:13', 'Expired'),
(9, '12121', 11.00, 1111, 0, '2025-04-22 23:42:00', '2025-04-21 23:42:37', 'Expired'),
(10, '23232', 11.00, 1111, 4, '2025-04-26 23:43:00', '2025-04-21 23:43:16', 'Expired'),
(11, '12123', 12.00, 111, 0, NULL, '2025-04-21 23:43:37', 'Disabled'),
(12, '111DD', 111.00, 1, 0, NULL, '2025-04-21 23:43:55', 'Disabled'),
(13, '88888', 80.00, 1, 0, '2025-04-22 00:08:00', '2025-04-21 23:44:07', 'Expired'),
(14, '12112', 124.00, 1, 0, NULL, '2025-04-21 23:44:23', 'Disabled'),
(15, 'ABC12', 20.00, 10, 2, NULL, '2025-04-24 10:25:34', 'Active'),
(16, 'KAWAI', 10.00, 100000, 6, NULL, '2025-04-27 17:43:22', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`),
  ADD UNIQUE KEY `adminEmail` (`adminEmail`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`categoryID`),
  ADD UNIQUE KEY `categoryName` (`categoryName`);

--
-- Indexes for table `delivery`
--
ALTER TABLE `delivery`
  ADD PRIMARY KEY (`deliveryID`),
  ADD UNIQUE KEY `orderID` (`orderID`),
  ADD UNIQUE KEY `trackingNumber` (`trackingNumber`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`imageID`),
  ADD KEY `gallery_ibfk_1` (`productID`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`memberID`),
  ADD UNIQUE KEY `memberEmail` (`memberEmail`);

--
-- Indexes for table `member_carts`
--
ALTER TABLE `member_carts`
  ADD PRIMARY KEY (`memberID`,`productID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `member_wishlist`
--
ALTER TABLE `member_wishlist`
  ADD PRIMARY KEY (`memberID`,`productID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `ordereditem`
--
ALTER TABLE `ordereditem`
  ADD PRIMARY KEY (`orderItemID`),
  ADD KEY `ordereditem_ibfk_2` (`productID`),
  ADD KEY `ordereditem_ibfk_1` (`orderID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `memberID` (`memberID`),
  ADD KEY `voucherID` (`voucherID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`),
  ADD UNIQUE KEY `orderID` (`orderID`),
  ADD KEY `memberID` (`memberID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `adminID` (`adminID`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`productID`,`categoryID`),
  ADD KEY `categoryID` (`categoryID`);

--
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receiptNo`),
  ADD UNIQUE KEY `orderID` (`orderID`),
  ADD UNIQUE KEY `paymentID` (`paymentID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`memberID`);

--
-- Indexes for table `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`voucherID`),
  ADD UNIQUE KEY `voucherCode` (`voucherCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10002;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `categoryID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23233;

--
-- AUTO_INCREMENT for table `delivery`
--
ALTER TABLE `delivery`
  MODIFY `deliveryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `imageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `memberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1056;

--
-- AUTO_INCREMENT for table `ordereditem`
--
ALTER TABLE `ordereditem`
  MODIFY `orderItemID` int(36) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `voucher`
--
ALTER TABLE `voucher`
  MODIFY `voucherID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `delivery_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON DELETE CASCADE;

--
-- Constraints for table `member_carts`
--
ALTER TABLE `member_carts`
  ADD CONSTRAINT `member_carts_ibfk_1` FOREIGN KEY (`memberID`) REFERENCES `member` (`memberID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `member_carts_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `member_wishlist`
--
ALTER TABLE `member_wishlist`
  ADD CONSTRAINT `member_wishlist_ibfk_1` FOREIGN KEY (`memberID`) REFERENCES `member` (`memberID`),
  ADD CONSTRAINT `member_wishlist_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`);

--
-- Constraints for table `ordereditem`
--
ALTER TABLE `ordereditem`
  ADD CONSTRAINT `ordereditem_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordereditem_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`memberID`) REFERENCES `member` (`memberID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`voucherID`) REFERENCES `voucher` (`voucherID`) ON DELETE SET NULL;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`memberID`) REFERENCES `member` (`memberID`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`adminID`) REFERENCES `admin` (`adminID`) ON DELETE CASCADE;

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`productID`) REFERENCES `product` (`productID`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `category` (`categoryID`) ON DELETE CASCADE;

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`paymentID`) REFERENCES `payment` (`paymentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `receipt_ibfk_2` FOREIGN KEY (`orderID`) REFERENCES `orders` (`orderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`memberID`) REFERENCES `member` (`memberID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
