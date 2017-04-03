--
-- Database: `id1089148_beer`
--
CREATE DATABASE IF NOT EXISTS `id1089148_beer` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `id1089148_beer`;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `id` bigint(20) NOT NULL,
  `turn` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `code` varchar(4) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` bigint(20) NOT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(4000) COLLATE utf8_unicode_ci NOT NULL,
  `turnorder` int(11) NOT NULL,
  `game_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;