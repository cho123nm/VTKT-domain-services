-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 06:05 AM
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
-- Database: `tenmien`
--

-- --------------------------------------------------------

--
-- Table structure for table `caidatchung`
--

CREATE TABLE `caidatchung` (
  `id` int(11) NOT NULL,
  `tieude` varchar(255) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `imagebanner` varchar(255) DEFAULT NULL,
  `sodienthoai` varchar(255) DEFAULT NULL,
  `banner` varchar(2555) DEFAULT NULL,
  `logo` varchar(2555) DEFAULT NULL,
  `webgach` varchar(2565) DEFAULT NULL,
  `apikey` varchar(2555) DEFAULT NULL,
  `callback` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caidatchung`
--

INSERT INTO `caidatchung` (`id`, `tieude`, `theme`, `keywords`, `mota`, `imagebanner`, `sodienthoai`, `banner`, `logo`, `webgach`, `apikey`, `callback`) VALUES
(1, 'THANHVU.NET V4 UY TÍN TIỆN LỢI', '2', 'WEB BÁN DOMAIN NỘI ĐỊA UY TÍN', 'DỊCH VỤ DOMAIN UY TÍN CHẤT LƯỢNG', '', '0856761038', '4b455JDIvR8', '', 'cardvip.vn', '15626594-8251-4D4A-90E4-16F55C855D90', '/Packages/Callback.php');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `amount` varchar(255) DEFAULT NULL,
  `requestid` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `time2` varchar(255) DEFAULT NULL,
  `time3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `ns1` varchar(255) DEFAULT NULL,
  `ns2` varchar(255) DEFAULT NULL,
  `hsd` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `mgd` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `timedns` varchar(255) DEFAULT NULL,
  `ahihi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `uid`, `domain`, `ns1`, `ns2`, `hsd`, `status`, `mgd`, `time`, `timedns`, `ahihi`) VALUES
(12, 6, 'thanhvu.net', 'thanhvu.nett', 'netthanhvu.net', 1, 1, '164835191', '08/10/2025 - 23:50:23', '0', NULL),
(13, 8, 'thnhs.com', 'hieuthu2.net', 'hieu2thu.net', 1, 0, '245972390', '09/10/2025 - 23:22:37', '0', 0),
(14, 8, 'chatgpt.store', 'hieu2thu', 'netthanhvu', 1, 2, '853435910', '10/10/2025 - 01:11:40', '0', 0),
(15, 8, 'cucudfsdf.com', '11111222', 'hieu2thu.net', 1, 1, '247305497', '11/10/2025 - 18:53:57', '0', NULL),
(16, 9, 'cucubububububuu.com', 'hieuthu2.neter', 'netthanhvu', 1, 1, '952109907', '15/10/2025 - 20:40:42', '0', 0),
(17, 11, 'systemadmin.info', 'sdfgsdgs234', 'sdfgsdgs', 1, 1, '958190992', '27/10/2025 - 01:53:43', '42/10/2025', 0),
(18, 11, 'awss12.org', 'awsfcj.org1', 'olderaws.org1', 1, 1, '131638644', '27/10/2025 - 16:08:37', '17/11/2025', 0),
(19, 11, 'uqwojf.com', 'awsfcj.org1', 'olderaws.org', 1, 1, '300107175', '29/10/2025 - 01:37:30', '17/11/2025', 0),
(20, 11, 'h.com', '1111122', 'olderaws.org', 1, 1, '787763391', '02/11/2025 - 21:19:22', '17/11/2025', 0),
(21, 11, 'bv.com', 'hieuthu2.ne', 'olderaws.org', 1, 1, '130347298', '02/11/2025 - 21:23:02', '17/11/2025', 0);

-- --------------------------------------------------------

--
-- Table structure for table `listdomain`
--

CREATE TABLE `listdomain` (
  `id` int(11) NOT NULL,
  `image` varchar(2655) DEFAULT NULL,
  `price` varchar(2555) DEFAULT NULL,
  `duoi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listdomain`
--

INSERT INTO `listdomain` (`id`, `image`, `price`, `duoi`) VALUES
(1, '/images/dot_com.svg', '66000', '.com'),
(2, '/images/net-d3afe36203d3.svg', '55000', '.net'),
(3, '/images/info-3a404a27668b.svg', '55000', '.info'),
(4, '/images/org-292f994350a0.svg', '70000', '.org'),
(5, 'https://wwws1.lcn.com/images/lcn/channels/domain-names/extensions/tech-9e40579214ad.svg', '99000', '.tech');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `taikhoan` varchar(255) DEFAULT NULL,
  `matkhau` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tien` int(11) DEFAULT 0,
  `chucvu` int(11) DEFAULT 0,
  `time` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `taikhoan`, `matkhau`, `email`, `tien`, `chucvu`, `time`) VALUES
(6, 'adminthanhvu', '1bbd886460827015e5d605ed44252251', 'chumlongchinhgiua@gmail.com', 2147483647, 0, '08/10/2025 - 23:45:20'),
(7, 'cuto', '1bbd886460827015e5d605ed44252251', 'chuml@gmail.com', 0, 0, '09/10/2025 - 18:12:05'),
(8, 'thanhvu1', 'c9279a3f6c684f5c7d5d7060fc4ac3b7', 'cuto123@gmail.com', 899867999, 0, '09/10/2025 - 23:20:31'),
(9, 'cho123nm123', '069951877d52417a5e5375deca971622', 'chumli@gmail.com', 999933999, 0, '15/10/2025 - 18:05:32'),
(10, 'thanhvu2', '24e460f92c036c0a7928905bb84eba0a', 'toiiulaptrinh@gmail.com', 0, 0, '15/10/2025 - 23:44:12'),
(11, 'admin', 'c9279a3f6c684f5c7d5d7060fc4ac3b7', 'ad@gmail.com', 2147175647, 0, '16/10/2025 - 01:26:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `caidatchung`
--
ALTER TABLE `caidatchung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_theme` (`theme`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requestid` (`requestid`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_domain` (`domain`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_mgd` (`mgd`);

--
-- Indexes for table `listdomain`
--
ALTER TABLE `listdomain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_duoi` (`duoi`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_taikhoan` (`taikhoan`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_chucvu` (`chucvu`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `caidatchung`
--
ALTER TABLE `caidatchung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `listdomain`
--
ALTER TABLE `listdomain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `fk_cards_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
