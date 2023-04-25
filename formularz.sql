-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 25 Kwi 2023, 10:07
-- Wersja serwera: 10.4.22-MariaDB
-- Wersja PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `formularz`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `formularz`
--

CREATE TABLE `formularz` (
  `id` int(25) NOT NULL,
  `login` varchar(25) NOT NULL,
  `hasło` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `data` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `formularz`
--

INSERT INTO `formularz` (`id`, `login`, `hasło`, `email`, `data`) VALUES
(1, 'fadfas', 'asda', 'asfas@fdf', '2023-04-06'),
(2, '22rse', 'sdfsdfsdfsdf', 'asfas@fdfez', '2023-04-07');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `formularz`
--
ALTER TABLE `formularz`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `formularz`
--
ALTER TABLE `formularz`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
