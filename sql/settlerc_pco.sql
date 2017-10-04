-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 04 okt 2017 om 08:30
-- Serverversie: 5.6.36
-- PHP-versie: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `settlerc_pco`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_ads`
--

CREATE TABLE `pco_ads` (
  `ID` int(11) NOT NULL,
  `ad_name` varchar(100) NOT NULL,
  `ad_code` varchar(1000) NOT NULL,
  `ad_pages` varchar(1000) NOT NULL,
  `ad_sort` int(11) NOT NULL COMMENT '0 = skycraper 1 = vierkant'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_backgrounds`
--

CREATE TABLE `pco_backgrounds` (
  `ID` int(11) NOT NULL,
  `background` varchar(2002) NOT NULL,
  `tijd` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_chats`
--

CREATE TABLE `pco_chats` (
  `ID` int(11) NOT NULL,
  `user1` longtext NOT NULL,
  `user2` longtext NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_chats_messages`
--

CREATE TABLE `pco_chats_messages` (
  `ID` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `sender` longtext NOT NULL,
  `message` longtext NOT NULL,
  `readed` int(11) NOT NULL DEFAULT '0',
  `sended` varchar(2002) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_help`
--

CREATE TABLE `pco_help` (
  `ID` int(11) NOT NULL,
  `title` varchar(2200) NOT NULL,
  `content` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_pageview`
--

CREATE TABLE `pco_pageview` (
  `ID` int(11) NOT NULL,
  `date` varchar(2000) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parkcraft_reaction`
--

CREATE TABLE `pco_parkcraft_reaction` (
  `ID` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `pc_id` int(11) NOT NULL,
  `uuid` longtext,
  `reaction` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parkrequest`
--

CREATE TABLE `pco_parkrequest` (
  `ID` int(11) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `ip` varchar(1000) NOT NULL,
  `twitter` varchar(1000) NOT NULL,
  `email` varchar(1000) NOT NULL,
  `requester` longtext NOT NULL,
  `rejected` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks`
--

CREATE TABLE `pco_parks` (
  `ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(2000) NOT NULL DEFAULT '',
  `ip` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(1000) NOT NULL DEFAULT '',
  `followers` longtext NOT NULL,
  `logo` varchar(2000) NOT NULL DEFAULT '',
  `header` varchar(2000) NOT NULL DEFAULT '',
  `owner` longtext NOT NULL,
  `background` varchar(4000) NOT NULL DEFAULT '',
  `APIKey` longtext NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `deleted` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks_events`
--

CREATE TABLE `pco_parks_events` (
  `ID` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `event_start` datetime NOT NULL,
  `event_end` datetime NOT NULL,
  `event_title` varchar(2202) NOT NULL,
  `event_description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks_jobs`
--

CREATE TABLE `pco_parks_jobs` (
  `ID` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `job_name` varchar(2002) NOT NULL,
  `job_description` longtext NOT NULL,
  `job_status` int(11) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `expires` varchar(2002) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks_jobs_candidates`
--

CREATE TABLE `pco_parks_jobs_candidates` (
  `ID` int(11) NOT NULL,
  `user` longtext NOT NULL,
  `job_id` int(11) NOT NULL,
  `name` varchar(2002) NOT NULL,
  `about` longtext NOT NULL,
  `email` varchar(2002) NOT NULL,
  `skype` varchar(2002) NOT NULL,
  `knowledge` longtext NOT NULL,
  `reason` longtext NOT NULL,
  `extra` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks_rides`
--

CREATE TABLE `pco_parks_rides` (
  `ID` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `park_id` longtext NOT NULL,
  `ride_name` varchar(2002) NOT NULL,
  `ride_code` varchar(2002) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `time` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_parks_staff`
--

CREATE TABLE `pco_parks_staff` (
  `ID` int(11) NOT NULL,
  `uuid` longtext NOT NULL,
  `park_id` int(100) NOT NULL,
  `prefix` varchar(100) NOT NULL DEFAULT 'medewerker',
  `can_write` int(2) NOT NULL DEFAULT '0',
  `can_edit_settings` int(2) NOT NULL DEFAULT '0',
  `can_manage_staff` int(11) NOT NULL DEFAULT '0',
  `can_manage_rides` int(11) NOT NULL DEFAULT '0',
  `can_manage_jobs` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_plugins`
--

CREATE TABLE `pco_plugins` (
  `ID` int(11) NOT NULL,
  `logo` varchar(1000) NOT NULL,
  `name` varchar(1000) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `author` varchar(1000) NOT NULL,
  `author_link` varchar(1000) NOT NULL,
  `url` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_posts`
--

CREATE TABLE `pco_posts` (
  `ID` int(11) NOT NULL,
  `park_id` int(11) NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_body` mediumtext NOT NULL,
  `post_header` longtext NOT NULL,
  `post_images` longtext NOT NULL,
  `post_poster` longtext NOT NULL,
  `post_likes` longtext NOT NULL,
  `posted_on` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `reviewed` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_posts_view`
--

CREATE TABLE `pco_posts_view` (
  `ID` int(11) NOT NULL,
  `article` int(11) NOT NULL,
  `views` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_pvdm`
--

CREATE TABLE `pco_pvdm` (
  `ID` int(11) NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_body` mediumtext NOT NULL,
  `post_header` longtext NOT NULL,
  `post_images` longtext NOT NULL,
  `post_poster` longtext NOT NULL,
  `post_likes` longtext NOT NULL,
  `posted_on` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_pvdw`
--

CREATE TABLE `pco_pvdw` (
  `ID` int(11) NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_body` mediumtext NOT NULL,
  `post_header` longtext NOT NULL,
  `post_images` longtext NOT NULL,
  `post_poster` longtext NOT NULL,
  `post_likes` longtext NOT NULL,
  `posted_on` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_ranks`
--

CREATE TABLE `pco_ranks` (
  `rank` int(10) NOT NULL,
  `prefix` varchar(2000) NOT NULL,
  `color` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_reaction`
--

CREATE TABLE `pco_reaction` (
  `ID` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `uuid` longtext,
  `reaction` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_settings`
--

CREATE TABLE `pco_settings` (
  `ID` int(11) NOT NULL,
  `variable` varchar(2002) NOT NULL,
  `data` varchar(2002) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_staff`
--

CREATE TABLE `pco_staff` (
  `ID` int(11) NOT NULL,
  `UUID` longtext NOT NULL,
  `rank` int(11) NOT NULL,
  `can_use_staffpanel` int(11) NOT NULL DEFAULT '0',
  `can_manage_parkrequests` int(11) NOT NULL DEFAULT '0',
  `can_manage_users` int(11) NOT NULL DEFAULT '0',
  `can_manage_parks` int(11) NOT NULL DEFAULT '0',
  `can_manage_comments` int(11) NOT NULL DEFAULT '0',
  `can_send_mail` int(11) NOT NULL DEFAULT '0',
  `can_manage_posts` int(11) NOT NULL DEFAULT '0',
  `can_write_tutorials` int(11) NOT NULL DEFAULT '0',
  `can_write_pvdw` int(11) NOT NULL DEFAULT '0',
  `can_write_pvdm` int(11) NOT NULL DEFAULT '0',
  `can_manage_applications` int(11) NOT NULL DEFAULT '0',
  `can_manage_chats` int(11) NOT NULL DEFAULT '0',
  `can_manage_advertisements` int(10) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_tutorials`
--

CREATE TABLE `pco_tutorials` (
  `ID` int(11) NOT NULL,
  `post_title` varchar(200) NOT NULL,
  `post_body` mediumtext NOT NULL,
  `post_images` longtext NOT NULL,
  `post_poster` longtext NOT NULL,
  `post_likes` longtext NOT NULL,
  `posted_on` varchar(100) NOT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `pco_users`
--

CREATE TABLE `pco_users` (
  `ID` int(11) NOT NULL,
  `UUID` varchar(2000) NOT NULL DEFAULT '',
  `name` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(2083) NOT NULL,
  `password` longtext NOT NULL,
  `changepassword` varchar(2000) NOT NULL DEFAULT '0',
  `rank` int(11) NOT NULL DEFAULT '0',
  `access` int(10) NOT NULL DEFAULT '1',
  `activated` varchar(2000) NOT NULL DEFAULT '',
  `sessionID` varchar(2000) NOT NULL DEFAULT '',
  `last_execution` varchar(2000) NOT NULL DEFAULT '',
  `news_email` int(11) NOT NULL DEFAULT '1',
  `reaction_mail` int(11) NOT NULL DEFAULT '1',
  `profile_picture` varchar(2000) NOT NULL DEFAULT '',
  `profile_about` text NOT NULL,
  `profile_mc` varchar(2002) NOT NULL DEFAULT '',
  `lang` varchar(10) NOT NULL DEFAULT 'NL'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `pco_ads`
--
ALTER TABLE `pco_ads`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_backgrounds`
--
ALTER TABLE `pco_backgrounds`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_chats`
--
ALTER TABLE `pco_chats`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_chats_messages`
--
ALTER TABLE `pco_chats_messages`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_help`
--
ALTER TABLE `pco_help`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_pageview`
--
ALTER TABLE `pco_pageview`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parkcraft_reaction`
--
ALTER TABLE `pco_parkcraft_reaction`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parkrequest`
--
ALTER TABLE `pco_parkrequest`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks`
--
ALTER TABLE `pco_parks`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks_events`
--
ALTER TABLE `pco_parks_events`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks_jobs`
--
ALTER TABLE `pco_parks_jobs`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks_jobs_candidates`
--
ALTER TABLE `pco_parks_jobs_candidates`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks_rides`
--
ALTER TABLE `pco_parks_rides`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_parks_staff`
--
ALTER TABLE `pco_parks_staff`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_plugins`
--
ALTER TABLE `pco_plugins`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_posts`
--
ALTER TABLE `pco_posts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_posts_view`
--
ALTER TABLE `pco_posts_view`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_pvdm`
--
ALTER TABLE `pco_pvdm`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_pvdw`
--
ALTER TABLE `pco_pvdw`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_ranks`
--
ALTER TABLE `pco_ranks`
  ADD PRIMARY KEY (`rank`);

--
-- Indexen voor tabel `pco_reaction`
--
ALTER TABLE `pco_reaction`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_settings`
--
ALTER TABLE `pco_settings`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_staff`
--
ALTER TABLE `pco_staff`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_tutorials`
--
ALTER TABLE `pco_tutorials`
  ADD PRIMARY KEY (`ID`);

--
-- Indexen voor tabel `pco_users`
--
ALTER TABLE `pco_users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `pco_ads`
--
ALTER TABLE `pco_ads`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT voor een tabel `pco_backgrounds`
--
ALTER TABLE `pco_backgrounds`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT voor een tabel `pco_chats`
--
ALTER TABLE `pco_chats`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;
--
-- AUTO_INCREMENT voor een tabel `pco_chats_messages`
--
ALTER TABLE `pco_chats_messages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=581;
--
-- AUTO_INCREMENT voor een tabel `pco_help`
--
ALTER TABLE `pco_help`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT voor een tabel `pco_pageview`
--
ALTER TABLE `pco_pageview`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=227;
--
-- AUTO_INCREMENT voor een tabel `pco_parkcraft_reaction`
--
ALTER TABLE `pco_parkcraft_reaction`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT voor een tabel `pco_parkrequest`
--
ALTER TABLE `pco_parkrequest`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;
--
-- AUTO_INCREMENT voor een tabel `pco_parks`
--
ALTER TABLE `pco_parks`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;
--
-- AUTO_INCREMENT voor een tabel `pco_parks_events`
--
ALTER TABLE `pco_parks_events`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT voor een tabel `pco_parks_jobs`
--
ALTER TABLE `pco_parks_jobs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=710;
--
-- AUTO_INCREMENT voor een tabel `pco_parks_jobs_candidates`
--
ALTER TABLE `pco_parks_jobs_candidates`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1171;
--
-- AUTO_INCREMENT voor een tabel `pco_parks_rides`
--
ALTER TABLE `pco_parks_rides`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1378;
--
-- AUTO_INCREMENT voor een tabel `pco_parks_staff`
--
ALTER TABLE `pco_parks_staff`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;
--
-- AUTO_INCREMENT voor een tabel `pco_plugins`
--
ALTER TABLE `pco_plugins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT voor een tabel `pco_posts`
--
ALTER TABLE `pco_posts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=563;
--
-- AUTO_INCREMENT voor een tabel `pco_posts_view`
--
ALTER TABLE `pco_posts_view`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=456;
--
-- AUTO_INCREMENT voor een tabel `pco_pvdm`
--
ALTER TABLE `pco_pvdm`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT voor een tabel `pco_pvdw`
--
ALTER TABLE `pco_pvdw`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT voor een tabel `pco_reaction`
--
ALTER TABLE `pco_reaction`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1241;
--
-- AUTO_INCREMENT voor een tabel `pco_settings`
--
ALTER TABLE `pco_settings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT voor een tabel `pco_staff`
--
ALTER TABLE `pco_staff`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT voor een tabel `pco_tutorials`
--
ALTER TABLE `pco_tutorials`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT voor een tabel `pco_users`
--
ALTER TABLE `pco_users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2021;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
