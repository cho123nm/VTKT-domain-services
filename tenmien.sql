-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2025 at 12:14 PM
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
  `callback` varchar(255) DEFAULT NULL,
  `facebook_link` varchar(500) DEFAULT NULL,
  `zalo_phone` varchar(50) DEFAULT NULL,
  `telegram_bot_token` varchar(255) DEFAULT NULL COMMENT 'Telegram Bot Token',
  `telegram_admin_chat_id` varchar(255) DEFAULT NULL COMMENT 'Telegram Admin Chat ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caidatchung`
--

INSERT INTO `caidatchung` (`id`, `tieude`, `theme`, `keywords`, `mota`, `imagebanner`, `sodienthoai`, `banner`, `logo`, `webgach`, `apikey`, `callback`, `facebook_link`, `zalo_phone`, `telegram_bot_token`, `telegram_admin_chat_id`) VALUES
(1, 'THANHVU.NET V4 UY TÍN TIỆN LỢI', '2', 'WEB BÁN DOMAIN NỘI ĐỊA UY TÍN', 'DỊCH VỤ DOMAIN UY TÍN CHẤT LƯỢNG', '', '0856761038', '4b455JDIvR8', '', 'cardvip.vn', '15626594-8251-4D4A-90E4-16F55C855D90', '/Packages/Callback.php', 'https://www.facebook.com/thanh.vu.826734', '0856761038', '8546022568:AAHq8cNiXZRa34pODa2Cfigx_fqbu9Wtalk', '7358984141');

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

--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (`id`, `uid`, `pin`, `serial`, `type`, `amount`, `requestid`, `status`, `time`, `time2`, `time3`) VALUES
(6, 6, '123456', 'ABCD1234', 'Viettel', '100000', 'REQ123', 1, '2025-11-13 22:10:00', '2025-11-13 22:10:00', '2025-11-14 0:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL COMMENT 'ID người dùng gửi phản hồi',
  `username` varchar(255) DEFAULT NULL COMMENT 'Tên người dùng',
  `email` varchar(255) DEFAULT NULL COMMENT 'Email người dùng',
  `message` text DEFAULT NULL COMMENT 'Nội dung phản hồi/lỗi',
  `admin_reply` text DEFAULT NULL COMMENT 'Phản hồi từ admin',
  `status` int(11) DEFAULT 0 COMMENT '0: Chờ xử lý, 1: Đã trả lời, 2: Đã đọc',
  `telegram_chat_id` varchar(255) DEFAULT NULL COMMENT 'Chat ID Telegram của user',
  `time` varchar(255) DEFAULT NULL COMMENT 'Thời gian gửi',
  `reply_time` varchar(255) DEFAULT NULL COMMENT 'Thời gian admin trả lời'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `uid`, `username`, `email`, `message`, `admin_reply`, `status`, `telegram_chat_id`, `time`, `reply_time`) VALUES
(1, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'WEB NHƯ CAK', 'CUT', 2, NULL, '29/11/2025 - 00:03:39', '29/11/2025 - 00:04:36'),
(2, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'con cak admin', 'cmm', 2, NULL, '29/11/2025 - 00:19:09', '29/11/2025 - 00:19:41'),
(3, 11, 'admin', 'chumlongchinhgiua@gmail.com', 'hi', 'hi bạn', 2, NULL, '01/12/2025 - 22:37:52', '02/12/2025 - 00:41:49');

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
(12, 6, 'thanhvu.net', 'thanhvu.nett', 'netthanhvu.net', 1, 2, '164835191', '08/10/2025 - 23:50:23', '0', NULL),
(13, 8, 'thnhs.com', 'hieuthu2.net', 'hieu2thu.net', 1, 1, '245972390', '09/10/2025 - 23:22:37', '0', 0),
(14, 8, 'chatgpt.store', 'hieu2thu', 'netthanhvu', 1, 1, '853435910', '10/10/2025 - 01:11:40', '0', 0),
(15, 8, 'cucudfsdf.com', '11111222', 'hieu2thu.net', 1, 1, '247305497', '11/10/2025 - 18:53:57', '0', NULL),
(16, 9, 'cucubububububuu.com', 'hieuthu2.neter', 'netthanhvu', 1, 1, '952109907', '15/10/2025 - 20:40:42', '0', 0),
(17, 11, 'systemadmin.info', 'sdfgsdgs234', 'sdfgsdgs', 1, 1, '958190992', '27/10/2025 - 01:53:43', '42/10/2025', 0),
(18, 11, 'awss12.org', 'awsfcj.org1', 'olderaws.org1', 1, 1, '131638644', '27/10/2025 - 16:08:37', '0', 0),
(19, 11, 'uqwojf.com', 'awsfcj.org1', 'olderaws.org', 1, 1, '300107175', '29/10/2025 - 01:37:30', '0', 0),
(20, 11, 'h.com', '1111122', 'olderaws.org', 1, 1, '787763391', '02/11/2025 - 21:19:22', '0', 0),
(21, 11, 'bv.com', 'hieuthu2.ne', 'olderaws.org', 1, 1, '130347298', '02/11/2025 - 21:23:02', '0', 0),
(22, 11, 'tst.com', 'q123', 'q', 1, 4, '200912899', '12/11/2025 - 16:56:44', '27/11/2025', 0),
(23, 11, 'minhnghia.info', 'minhnghia.sexy.inf', 'minhnghia.vn.info', 1, 1, '771515495', '19/11/2025 - 01:06:32', '17/12/2025', 0),
(24, 11, 'cucu.com', '111112', 'hieu2thu.net', 1, 0, '810047792', '02/12/2025 - 00:11:43', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hostinghistory`
--

CREATE TABLE `hostinghistory` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `hosting_id` int(11) NOT NULL,
  `period` varchar(20) NOT NULL,
  `mgd` varchar(100) NOT NULL,
  `time` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '/domain/images/dot_com.svg', '66000', '.com'),
(2, '/domain/images/net-d3afe36203d3.svg', '55000', '.net'),
(3, '/domain/images/info-3a404a27668b.svg', '55000', '.info'),
(4, '/domain/images/org-292f994350a0.svg', '70000', '.org'),
(5, '/domain/images/tech-9e40579214ad.svg', '99000', '.tech');

-- --------------------------------------------------------

--
-- Table structure for table `listhosting`
--

CREATE TABLE `listhosting` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_month` int(11) NOT NULL,
  `price_year` int(11) NOT NULL,
  `specs` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listsourcecode`
--

CREATE TABLE `listsourcecode` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `download_link` varchar(500) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `listsourcecode`
--

INSERT INTO `listsourcecode` (`id`, `name`, `description`, `price`, `file_path`, `download_link`, `image`, `category`, `time`) VALUES
(6, 'source app note', '', 1200, '/domain/uploads/source-code/1766945504_glfw-3.4.bin.WIN64.zip', '', '/domain/images/thanhvu.jpg', 'php', '29/12/2025 - 01:11:44');

-- --------------------------------------------------------

--
-- Table structure for table `listvps`
--

CREATE TABLE `listvps` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_month` int(11) NOT NULL,
  `price_year` int(11) NOT NULL,
  `specs` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `time` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sourcecodehistory`
--

CREATE TABLE `sourcecodehistory` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `source_code_id` int(11) NOT NULL,
  `mgd` varchar(100) NOT NULL,
  `time` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sourcecodehistory`
--

INSERT INTO `sourcecodehistory` (`id`, `uid`, `source_code_id`, `mgd`, `time`, `status`) VALUES
(3, 12, 6, '17672764972360', '2026-01-01 21:08:17', 1),
(4, 12, 6, '17672772327725', '2026-01-01 21:20:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(6, 'adminthanhvu', '1bbd886460827015e5d605ed44252251', 'chumlongchinhgiua@gmail.com', 1231312313, 0, '08/10/2025 - 23:45:20'),
(7, 'cuto', '1bbd886460827015e5d605ed44252251', 'chuml@gmail.com', 99999, 0, '09/10/2025 - 18:12:05'),
(8, 'thanhvu1', 'c9279a3f6c684f5c7d5d7060fc4ac3b7', 'cuto123@gmail.com', 899867999, 0, '09/10/2025 - 23:20:31'),
(9, 'cho123nm123', '069951877d52417a5e5375deca971622', 'chumli@gmail.com', 999933999, 0, '15/10/2025 - 18:05:32'),
(10, 'thanhvu2', '24e460f92c036c0a7928905bb84eba0a', 'toiiulaptrinh@gmail.com', 0, 0, '15/10/2025 - 23:44:12'),
(11, 'adminvu', 'c9279a3f6c684f5c7d5d7060fc4ac3b7', 'adc@gmail.com', 2146986247, 1, '16/10/2025 - 01:26:11'),
(12, 'vu123', 'b6baae2f2f7fb9765830655781ee0313', 'thanhvuaws@gmail.com', 99997600, 0, '01/01/2026 - 20:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `vpshistory`
--

CREATE TABLE `vpshistory` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `vps_id` int(11) NOT NULL,
  `period` varchar(20) NOT NULL,
  `mgd` varchar(100) NOT NULL,
  `time` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uid` (`uid`),
  ADD KEY `idx_status` (`status`);

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
-- Indexes for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `hosting_id` (`hosting_id`),
  ADD KEY `mgd` (`mgd`);

--
-- Indexes for table `listdomain`
--
ALTER TABLE `listdomain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_duoi` (`duoi`);

--
-- Indexes for table `listhosting`
--
ALTER TABLE `listhosting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listsourcecode`
--
ALTER TABLE `listsourcecode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `listvps`
--
ALTER TABLE `listvps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `email` (`email`);

--
-- Indexes for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `source_code_id` (`source_code_id`),
  ADD KEY `mgd` (`mgd`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_taikhoan` (`taikhoan`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_chucvu` (`chucvu`);

--
-- Indexes for table `vpshistory`
--
ALTER TABLE `vpshistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `vps_id` (`vps_id`),
  ADD KEY `mgd` (`mgd`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listdomain`
--
ALTER TABLE `listdomain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `listhosting`
--
ALTER TABLE `listhosting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `listsourcecode`
--
ALTER TABLE `listsourcecode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `listvps`
--
ALTER TABLE `listvps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vpshistory`
--
ALTER TABLE `vpshistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `fk_cards_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_history_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hostinghistory`
--
ALTER TABLE `hostinghistory`
  ADD CONSTRAINT `fk_hostinghistory_hosting` FOREIGN KEY (`hosting_id`) REFERENCES `listhosting` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hostinghistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sourcecodehistory`
--
ALTER TABLE `sourcecodehistory`
  ADD CONSTRAINT `fk_sourcecodehistory_sourcecode` FOREIGN KEY (`source_code_id`) REFERENCES `listsourcecode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sourcecodehistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vpshistory`
--
ALTER TABLE `vpshistory`
  ADD CONSTRAINT `fk_vpshistory_users` FOREIGN KEY (`uid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vpshistory_vps` FOREIGN KEY (`vps_id`) REFERENCES `listvps` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
