-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 26, 2024 at 05:40 AM
-- Server version: 8.0.39-0ubuntu0.22.04.1
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ginum`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_tbl`
--

CREATE TABLE `account_tbl` (
  `account_id` int NOT NULL,
  `account_name` varchar(11) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_tbl`
--

INSERT INTO `account_tbl` (`account_id`, `account_name`) VALUES
(1, 'Assets'),
(2, 'Liabilities'),
(3, 'Equity'),
(4, 'Income'),
(5, 'Expenses');

-- --------------------------------------------------------

--
-- Table structure for table `bank_account_tbl`
--

CREATE TABLE `bank_account_tbl` (
  `bank_id` int NOT NULL,
  `bank_details_id` int NOT NULL,
  `company_id` int NOT NULL,
  `company_sub_account_balance_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_details_tbl`
--

CREATE TABLE `bank_details_tbl` (
  `bank_details_id` int NOT NULL,
  `bank_name` varchar(150) NOT NULL,
  `branch` varchar(150) NOT NULL,
  `account_name` varchar(70) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `company_id` int NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_statement_tbl`
--

CREATE TABLE `bank_statement_tbl` (
  `stmt_id` int NOT NULL,
  `transaction_date` date NOT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_description` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_account_id` int NOT NULL,
  `bank_id` int NOT NULL,
  `d_or_w` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `amount` float(18,2) NOT NULL,
  `balance` float(18,2) NOT NULL,
  `company_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_category_tbl`
--

CREATE TABLE `company_category_tbl` (
  `company_category_id` int NOT NULL,
  `category_name` varchar(35) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_category_tbl`
--

INSERT INTO `company_category_tbl` (`company_category_id`, `category_name`) VALUES
(1, 'Education and EdTech'),
(2, 'Creative and Design'),
(3, 'Real Estate and Property Management'),
(4, 'Construction and Engineering'),
(5, 'Hospitality and Tourism'),
(6, 'IT and Technology'),
(7, 'Marketing and E-commerce'),
(8, 'Manufacturing and Logistics'),
(9, 'Healthcare and Life Sciences'),
(10, 'Professional Services');

-- --------------------------------------------------------

--
-- Table structure for table `company_sub_account_balance`
--

CREATE TABLE `company_sub_account_balance` (
  `company_sub_account_balance_id` int NOT NULL,
  `recorded_timstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `company_id` int DEFAULT NULL,
  `sub_account_id` int DEFAULT NULL,
  `sub_account_type_id` int DEFAULT NULL,
  `balance` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_tbl`
--

CREATE TABLE `company_tbl` (
  `company_id` int NOT NULL,
  `company_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `company_category_id` int NOT NULL,
  `company_reg_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vat_no` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin_no` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company_registered_address` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `company_factory_address` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mobile_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `website_url` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_joined` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `password` varchar(65) COLLATE utf8mb4_general_ci NOT NULL,
  `img_path` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `br_report_path` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country_id` int DEFAULT NULL,
  `currency_id` int NOT NULL,
  `privilege_id` int NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_currency_tbl`
--

CREATE TABLE `country_currency_tbl` (
  `country_id` int NOT NULL,
  `currency_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country_currency_tbl`
--

INSERT INTO `country_currency_tbl` (`country_id`, `currency_id`) VALUES
(48, 48),
(51, 48),
(52, 48),
(56, 48),
(59, 48),
(73, 48),
(75, 48),
(87, 48),
(92, 48),
(93, 48),
(101, 48),
(117, 48),
(132, 48),
(146, 48),
(147, 48),
(152, 48),
(185, 48),
(186, 48),
(187, 48);

-- --------------------------------------------------------

--
-- Table structure for table `country_tbl`
--

CREATE TABLE `country_tbl` (
  `country_id` int NOT NULL,
  `country_code` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `country_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country_tbl`
--

INSERT INTO `country_tbl` (`country_id`, `country_code`, `country_name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'AO', 'Angola'),
(5, 'AR', 'Argentina'),
(6, 'AM', 'Armenia'),
(7, 'AW', 'Aruba'),
(8, 'AU', 'Australia'),
(9, 'AZ', 'Azerbaijan'),
(10, 'BS', 'Bahamas'),
(11, 'BH', 'Bahrain'),
(12, 'BD', 'Bangladesh'),
(13, 'BB', 'Barbados'),
(14, 'BY', 'Belarus'),
(15, 'BZ', 'Belize'),
(16, 'BM', 'Bermuda'),
(17, 'BT', 'Bhutan'),
(18, 'BO', 'Bolivia'),
(19, 'BA', 'Bosnia and Herzegovina'),
(20, 'BW', 'Botswana'),
(21, 'BR', 'Brazil'),
(22, 'BN', 'Brunei'),
(23, 'BG', 'Bulgaria'),
(24, 'BI', 'Burundi'),
(25, 'CV', 'Cabo Verde'),
(26, 'KH', 'Cambodia'),
(27, 'CA', 'Canada'),
(28, 'KY', 'Cayman Islands'),
(29, 'CF', 'Central African Republic'),
(30, 'CL', 'Chile'),
(31, 'CN', 'China'),
(32, 'CO', 'Colombia'),
(33, 'KM', 'Comoros'),
(34, 'CG', 'Congo'),
(35, 'CR', 'Costa Rica'),
(36, 'HR', 'Croatia'),
(37, 'CU', 'Cuba'),
(38, 'CZ', 'Czech Republic'),
(39, 'DK', 'Denmark'),
(40, 'DJ', 'Djibouti'),
(41, 'DO', 'Dominican Republic'),
(42, 'DM', 'Dominica'),
(43, 'EC', 'Ecuador'),
(44, 'EG', 'Egypt'),
(45, 'SV', 'El Salvador'),
(46, 'GQ', 'Equatorial Guinea'),
(47, 'ER', 'Eritrea'),
(48, 'EE', 'Estonia'),
(49, 'ET', 'Ethiopia'),
(50, 'FJ', 'Fiji'),
(51, 'FI', 'Finland'),
(52, 'FR', 'France'),
(53, 'GA', 'Gabon'),
(54, 'GM', 'Gambia'),
(55, 'GE', 'Georgia'),
(56, 'DE', 'Germany'),
(57, 'GH', 'Ghana'),
(58, 'GI', 'Gibraltar'),
(59, 'GR', 'Greece'),
(60, 'GD', 'Grenada'),
(61, 'GT', 'Guatemala'),
(62, 'GN', 'Guinea'),
(63, 'GY', 'Guyana'),
(64, 'HT', 'Haiti'),
(65, 'HN', 'Honduras'),
(66, 'HK', 'Hong Kong'),
(67, 'HU', 'Hungary'),
(68, 'IS', 'Iceland'),
(69, 'IN', 'India'),
(70, 'ID', 'Indonesia'),
(71, 'IR', 'Iran'),
(72, 'IQ', 'Iraq'),
(73, 'IE', 'Ireland'),
(74, 'IL', 'Israel'),
(75, 'IT', 'Italy'),
(76, 'JM', 'Jamaica'),
(77, 'JP', 'Japan'),
(78, 'JO', 'Jordan'),
(79, 'KZ', 'Kazakhstan'),
(80, 'KE', 'Kenya'),
(81, 'KI', 'Kiribati'),
(82, 'KP', 'North Korea'),
(83, 'KR', 'South Korea'),
(84, 'KW', 'Kuwait'),
(85, 'KG', 'Kyrgyzstan'),
(86, 'LA', 'Laos'),
(87, 'LV', 'Latvia'),
(88, 'LB', 'Lebanon'),
(89, 'LS', 'Lesotho'),
(90, 'LR', 'Liberia'),
(91, 'LY', 'Libya'),
(92, 'LT', 'Lithuania'),
(93, 'LU', 'Luxembourg'),
(94, 'MO', 'Macau'),
(95, 'MK', 'North Macedonia'),
(96, 'MG', 'Madagascar'),
(97, 'MW', 'Malawi'),
(98, 'MY', 'Malaysia'),
(99, 'MV', 'Maldives'),
(100, 'ML', 'Mali'),
(101, 'MT', 'Malta'),
(102, 'MH', 'Marshall Islands'),
(103, 'MR', 'Mauritania'),
(104, 'MU', 'Mauritius'),
(105, 'MX', 'Mexico'),
(106, 'FM', 'Micronesia'),
(107, 'MD', 'Moldova'),
(108, 'MC', 'Monaco'),
(109, 'MN', 'Mongolia'),
(110, 'ME', 'Montenegro'),
(111, 'MA', 'Morocco'),
(112, 'MZ', 'Mozambique'),
(113, 'MM', 'Myanmar'),
(114, 'NA', 'Namibia'),
(115, 'NR', 'Nauru'),
(116, 'NP', 'Nepal'),
(117, 'NL', 'Netherlands'),
(118, 'NZ', 'New Zealand'),
(119, 'NI', 'Nicaragua'),
(120, 'NE', 'Niger'),
(121, 'NG', 'Nigeria'),
(122, 'NO', 'Norway'),
(123, 'OM', 'Oman'),
(124, 'PK', 'Pakistan'),
(125, 'PW', 'Palau'),
(126, 'PA', 'Panama'),
(127, 'PG', 'Papua New Guinea'),
(128, 'PY', 'Paraguay'),
(129, 'PE', 'Peru'),
(130, 'PH', 'Philippines'),
(131, 'PL', 'Poland'),
(132, 'PT', 'Portugal'),
(133, 'QA', 'Qatar'),
(134, 'RO', 'Romania'),
(135, 'RU', 'Russia'),
(136, 'RW', 'Rwanda'),
(137, 'WS', 'Samoa'),
(138, 'SM', 'San Marino'),
(139, 'ST', 'Sao Tome and Principe'),
(140, 'SA', 'Saudi Arabia'),
(141, 'SN', 'Senegal'),
(142, 'RS', 'Serbia'),
(143, 'SC', 'Seychelles'),
(144, 'SL', 'Sierra Leone'),
(145, 'SG', 'Singapore'),
(146, 'SK', 'Slovakia'),
(147, 'SI', 'Slovenia'),
(148, 'SB', 'Solomon Islands'),
(149, 'SO', 'Somalia'),
(150, 'ZA', 'South Africa'),
(151, 'SS', 'South Sudan'),
(152, 'ES', 'Spain'),
(153, 'LK', 'Sri Lanka'),
(154, 'SD', 'Sudan'),
(155, 'SR', 'Suriname'),
(156, 'SZ', 'Eswatini'),
(157, 'SE', 'Sweden'),
(158, 'CH', 'Switzerland'),
(159, 'SY', 'Syria'),
(160, 'TW', 'Taiwan'),
(161, 'TJ', 'Tajikistan'),
(162, 'TZ', 'Tanzania'),
(163, 'TH', 'Thailand'),
(164, 'TL', 'Timor-Leste'),
(165, 'TG', 'Togo'),
(166, 'TO', 'Tonga'),
(167, 'TT', 'Trinidad and Tobago'),
(168, 'TN', 'Tunisia'),
(169, 'TR', 'Turkey'),
(170, 'TM', 'Turkmenistan'),
(171, 'TV', 'Tuvalu'),
(172, 'UG', 'Uganda'),
(173, 'UA', 'Ukraine'),
(174, 'AE', 'United Arab Emirates'),
(175, 'GB', 'United Kingdom'),
(176, 'US', 'United States'),
(177, 'UY', 'Uruguay'),
(178, 'UZ', 'Uzbekistan'),
(179, 'VU', 'Vanuatu'),
(180, 'VE', 'Venezuela'),
(181, 'VN', 'Vietnam'),
(182, 'YE', 'Yemen'),
(183, 'ZM', 'Zambia'),
(184, 'ZW', 'Zimbabwe'),
(185, 'AT', 'Austria'),
(186, 'BE', 'Belgium'),
(187, 'CY', 'Cyprus');

-- --------------------------------------------------------

--
-- Table structure for table `currency_tbl`
--

CREATE TABLE `currency_tbl` (
  `currency_id` int NOT NULL,
  `currency_code` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `currency_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `country_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `currency_tbl`
--

INSERT INTO `currency_tbl` (`currency_id`, `currency_code`, `currency_name`, `country_id`) VALUES
(1, 'AFN', 'Afghan Afghani', 1),
(2, 'ALL', 'Albanian Lek', 2),
(3, 'DZD', 'Algerian Dinar', 3),
(4, 'AOA', 'Angolan Kwanza', 4),
(5, 'ARS', 'Argentine Peso', 5),
(6, 'AMD', 'Armenian Dram', 6),
(7, 'AWG', 'Aruban Florin', 7),
(8, 'AUD', 'Australian Dollar', 8),
(9, 'AZN', 'Azerbaijani Manat', 9),
(10, 'BSD', 'Bahamian Dollar', 10),
(11, 'BHD', 'Bahraini Dinar', 11),
(12, 'BDT', 'Bangladeshi Taka', 12),
(13, 'BBD', 'Barbadian Dollar', 13),
(14, 'BYN', 'Belarusian Ruble', 14),
(15, 'BZD', 'Belize Dollar', 15),
(16, 'BMD', 'Bermudian Dollar', 16),
(17, 'BTN', 'Bhutanese Ngultrum', 17),
(18, 'BOB', 'Bolivian Boliviano', 18),
(19, 'BAM', 'Bosnia and Herzegovina Convertible Mark', 19),
(20, 'BWP', 'Botswana Pula', 20),
(21, 'BRL', 'Brazilian Real', 21),
(22, 'BND', 'Brunei Dollar', 22),
(23, 'BGN', 'Bulgarian Lev', 23),
(24, 'BIF', 'Burundian Franc', 24),
(25, 'CVE', 'Cabo Verdean Escudo', 25),
(26, 'KHR', 'Cambodian Riel', 26),
(27, 'CAD', 'Canadian Dollar', 27),
(28, 'KYD', 'Cayman Islands Dollar', 28),
(29, 'XAF', 'Central African CFA Franc', 29),
(30, 'CLP', 'Chilean Peso', 30),
(31, 'CNY', 'Chinese Yuan', 31),
(32, 'COP', 'Colombian Peso', 32),
(33, 'KMF', 'Comorian Franc', 33),
(34, 'CDF', 'Congolese Franc', 34),
(35, 'CRC', 'Costa Rican Colón', 35),
(36, 'HRK', 'Croatian Kuna', 36),
(37, 'CUP', 'Cuban Peso', 37),
(38, 'CZK', 'Czech Koruna', 38),
(39, 'DKK', 'Danish Krone', 39),
(40, 'DJF', 'Djiboutian Franc', 40),
(41, 'DOP', 'Dominican Peso', 41),
(42, 'XCD', 'East Caribbean Dollar', 42),
(43, 'EGP', 'Egyptian Pound', 44),
(44, 'SVC', 'Salvadoran Colón', 45),
(45, 'ERN', 'Eritrean Nakfa', 47),
(46, 'ETB', 'Ethiopian Birr', 49),
(47, 'FJD', 'Fijian Dollar', 50),
(48, 'EUR', 'Euro', NULL),
(49, 'GMD', 'Gambian Dalasi', 54),
(50, 'GEL', 'Georgian Lari', 55),
(51, 'GHS', 'Ghanaian Cedi', 57),
(52, 'GIP', 'Gibraltar Pound', 58),
(53, 'GTQ', 'Guatemalan Quetzal', 61),
(54, 'GNF', 'Guinean Franc', 62),
(55, 'GYD', 'Guyanese Dollar', 63),
(56, 'HTG', 'Haitian Gourde', 64),
(57, 'HNL', 'Honduran Lempira', 65),
(58, 'HKD', 'Hong Kong Dollar', 66),
(59, 'HUF', 'Hungarian Forint', 67),
(60, 'ISK', 'Icelandic Króna', 68),
(61, 'INR', 'Indian Rupee', 69),
(62, 'IDR', 'Indonesian Rupiah', 70),
(63, 'IRR', 'Iranian Rial', 71),
(64, 'IQD', 'Iraqi Dinar', 72),
(65, 'ILS', 'Israeli New Shekel', 74),
(66, 'JMD', 'Jamaican Dollar', 76),
(67, 'JPY', 'Japanese Yen', 77),
(68, 'JOD', 'Jordanian Dinar', 78),
(69, 'KZT', 'Kazakhstani Tenge', 79),
(70, 'KES', 'Kenyan Shilling', 80),
(71, 'KWD', 'Kuwaiti Dinar', 84),
(72, 'KGS', 'Kyrgyzstani Som', 85),
(73, 'LAK', 'Lao Kip', 86),
(74, 'LBP', 'Lebanese Pound', 88),
(75, 'LSL', 'Lesotho Loti', 89),
(76, 'LRD', 'Liberian Dollar', 90),
(77, 'LYD', 'Libyan Dinar', 91),
(78, 'MOP', 'Macanese Pataca', 94),
(79, 'MKD', 'Macedonian Denar', 95),
(80, 'MGA', 'Malagasy Ariary', 96),
(81, 'MWK', 'Malawian Kwacha', 97),
(82, 'MYR', 'Malaysian Ringgit', 98),
(83, 'MVR', 'Maldivian Rufiyaa', 99),
(84, 'MRO', 'Mauritanian Ouguiya', 103),
(85, 'MUR', 'Mauritian Rupee', 104),
(86, 'MXN', 'Mexican Peso', 105),
(87, 'MDL', 'Moldovan Leu', 107),
(88, 'MNT', 'Mongolian Tögrög', 109),
(89, 'MAD', 'Moroccan Dirham', 111),
(90, 'MZN', 'Mozambican Metical', 112),
(91, 'MMK', 'Myanmar Kyat', 113),
(92, 'NAD', 'Namibian Dollar', 114),
(93, 'NPR', 'Nepalese Rupee', 116),
(94, 'ANG', 'Netherlands Antillean Guilder', 117),
(95, 'NZD', 'New Zealand Dollar', 118),
(96, 'NIO', 'Nicaraguan Córdoba', 119),
(97, 'NGN', 'Nigerian Naira', 121),
(98, 'NOK', 'Norwegian Krone', 122),
(99, 'OMR', 'Omani Rial', 123),
(100, 'PKR', 'Pakistani Rupee', 124),
(101, 'PAB', 'Panamanian Balboa', 126),
(102, 'PGK', 'Papua New Guinean Kina', 127),
(103, 'PYG', 'Paraguayan Guaraní', 128),
(104, 'PEN', 'Peruvian Sol', 129),
(105, 'PHP', 'Philippine Peso', 130),
(106, 'PLN', 'Polish Złoty', 131),
(107, 'QAR', 'Qatari Riyal', 133),
(108, 'RON', 'Romanian Leu', 134),
(109, 'RUB', 'Russian Ruble', 135),
(110, 'RWF', 'Rwandan Franc', 136),
(111, 'SHP', 'Saint Helena Pound', NULL),
(112, 'WST', 'Samoan Tala', 137),
(113, 'SAR', 'Saudi Riyal', 140),
(114, 'RSD', 'Serbian Dinar', 142),
(115, 'SCR', 'Seychellois Rupee', 143),
(116, 'SLL', 'Sierra Leonean Leone', 144),
(117, 'SGD', 'Singapore Dollar', 145),
(118, 'SBD', 'Solomon Islands Dollar', 148),
(119, 'SOS', 'Somali Shilling', 149),
(120, 'ZAR', 'South African Rand', 150),
(121, 'SSP', 'South Sudanese Pound', 151),
(122, 'LKR', 'Sri Lankan Rupee', 153),
(123, 'SDG', 'Sudanese Pound', 154),
(124, 'SRD', 'Surinamese Dollar', 155),
(125, 'SZL', 'Eswatini Lilangeni', 156),
(126, 'SEK', 'Swedish Krona', 157),
(127, 'CHF', 'Swiss Franc', 158),
(128, 'SYP', 'Syrian Pound', 159),
(129, 'TWD', 'New Taiwan Dollar', 160),
(130, 'TJS', 'Tajikistani Somoni', 161),
(131, 'TZS', 'Tanzanian Shilling', 162),
(132, 'THB', 'Thai Baht', 163),
(133, 'TOP', 'Tongan Paʻanga', 166),
(134, 'TTD', 'Trinidad and Tobago Dollar', 167),
(135, 'TND', 'Tunisian Dinar', 168),
(136, 'TRY', 'Turkish Lira', 169),
(137, 'TMT', 'Turkmenistani Manat', 170),
(138, 'UGX', 'Ugandan Shilling', 172),
(139, 'UAH', 'Ukrainian Hryvnia', 173),
(140, 'AED', 'United Arab Emirates Dirham', 174),
(141, 'GBP', 'British Pound Sterling', 175),
(142, 'USD', 'United States Dollar', 176),
(143, 'UYU', 'Uruguayan Peso', 177),
(144, 'UZS', 'Uzbekistani Som', 178),
(145, 'VUV', 'Vanuatu Vatu', 179),
(146, 'VEF', 'Venezuelan Bolívar', 180),
(147, 'VND', 'Vietnamese Đồng', 181),
(148, 'YER', 'Yemeni Rial', 182),
(149, 'ZMW', 'Zambian Kwacha', 183),
(150, 'ZWL', 'Zimbabwean Dollar', 184);

-- --------------------------------------------------------

--
-- Table structure for table `customer_tbl`
--

CREATE TABLE `customer_tbl` (
  `customer_id` int NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `nic` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_type` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `vat_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `business_reg_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `company_id` int NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department_tbl`
--

CREATE TABLE `department_tbl` (
  `department_id` int NOT NULL,
  `company_id` int NOT NULL,
  `department_code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `department_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `depreciation_tbl`
--

CREATE TABLE `depreciation_tbl` (
  `depreciation_id` int NOT NULL,
  `company_id` int NOT NULL,
  `sub_account_type_id` int NOT NULL,
  `useful_years` int NOT NULL,
  `salvage_value` float(18,2) NOT NULL,
  `depreciation_rate` float(5,2) NOT NULL,
  `accumulated_depreciation` float(18,2) NOT NULL,
  `remaining_useful_years` int NOT NULL,
  `depreciation_method` varchar(20) NOT NULL,
  `note` text,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation_tbl`
--

CREATE TABLE `designation_tbl` (
  `designation_id` int NOT NULL,
  `designation_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `edit_requests`
--

CREATE TABLE `edit_requests` (
  `request_id` int NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `section` varchar(20) NOT NULL,
  `record_id` int NOT NULL,
  `sub_login_id` int NOT NULL,
  `company_id` int NOT NULL,
  `old_data` json NOT NULL,
  `new_data` json NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_tbl`
--

CREATE TABLE `employee_tbl` (
  `employee_id` int NOT NULL,
  `first_name` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `designation_id` int DEFAULT NULL,
  `company_id` int NOT NULL,
  `address` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `mobileNo` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `nic` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `epf_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(70) COLLATE utf8mb4_general_ci NOT NULL,
  `date_joined` date NOT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `img_path` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_tbl`
--

CREATE TABLE `expense_tbl` (
  `expense_id` int NOT NULL,
  `company_id` int NOT NULL,
  `sub_account_id` int NOT NULL,
  `amount` double(18,2) NOT NULL,
  `reference` varchar(30) DEFAULT NULL,
  `note` text NOT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `bill_img_path` varchar(150) DEFAULT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `extended_tax_tbl`
--

CREATE TABLE `extended_tax_tbl` (
  `ext_tax_id` int NOT NULL,
  `company_id` int NOT NULL,
  `starting_range` double(18,2) NOT NULL,
  `ending_range` double(18,2) DEFAULT NULL,
  `tax_rate` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `income_tbl`
--

CREATE TABLE `income_tbl` (
  `income_id` int NOT NULL,
  `company_id` int NOT NULL,
  `sub_account_id` int NOT NULL,
  `amount` double(18,2) NOT NULL,
  `reference` varchar(30) DEFAULT NULL,
  `note` text NOT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `invoice_img_path` varchar(150) DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_tbl`
--

CREATE TABLE `inventory_tbl` (
  `inventory_id` int NOT NULL,
  `item_id` int NOT NULL,
  `company_id` int NOT NULL,
  `date_added` date NOT NULL,
  `qty` int NOT NULL,
  `unit_price` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_category_tbl`
--

CREATE TABLE `item_category_tbl` (
  `item_category_id` int NOT NULL,
  `item_category_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `category_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_category_tbl`
--

INSERT INTO `item_category_tbl` (`item_category_id`, `item_category_name`, `category_status`) VALUES
(1, 'Raw Materials', 1),
(2, 'Components and Parts', 1),
(3, 'Office Supplies', 1),
(4, 'IT and Technology Products', 1),
(5, 'Furniture and Fixtures', 1),
(6, 'Safety and Personal Protective Equipment (PPE)', 1),
(7, 'Janitorial and Cleaning Supplies', 1),
(8, 'Maintenance and Repair Supplies', 1),
(9, 'Promotional and Marketing Materials', 1),
(10, 'Food and Beverage', 1),
(11, 'Transportation and Logistics Services', 1),
(12, 'Consulting and Professional Services', 1);

-- --------------------------------------------------------

--
-- Table structure for table `item_tbl`
--

CREATE TABLE `item_tbl` (
  `item_id` int NOT NULL,
  `company_id` int NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `unit` varchar(5) NOT NULL,
  `length_or_weight_or_vol` float(18,2) DEFAULT NULL,
  `bought_item_count` float(10,2) NOT NULL,
  `total_price` double(15,2) NOT NULL,
  `description` varchar(150) NOT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_tbl`
--

CREATE TABLE `payroll_tbl` (
  `payroll_id` int NOT NULL,
  `company_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `month` int NOT NULL,
  `year` year NOT NULL,
  `basic_salary` float(12,2) NOT NULL,
  `allowance` float(12,2) DEFAULT NULL,
  `ot_pay` float(12,2) DEFAULT NULL,
  `bonus` float(12,2) DEFAULT NULL,
  `appit` float(12,2) DEFAULT NULL,
  `loan` float(12,2) DEFAULT NULL,
  `other_deductions` float(12,2) DEFAULT NULL,
  `epf_8` float(12,2) NOT NULL,
  `epf_12` float(12,2) NOT NULL,
  `etf_3` float(12,2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `privilege_tbl`
--

CREATE TABLE `privilege_tbl` (
  `privilege_id` int NOT NULL,
  `privilege_name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privilege_tbl`
--

INSERT INTO `privilege_tbl` (`privilege_id`, `privilege_name`) VALUES
(1, 'Inventory'),
(2, 'Payroll'),
(3, 'All');

-- --------------------------------------------------------

--
-- Table structure for table `project_tbl`
--

CREATE TABLE `project_tbl` (
  `project_id` int NOT NULL,
  `project_code` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `department_id` int NOT NULL,
  `company_id` int NOT NULL,
  `billing_method` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `work_status` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `priority` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `budget` float(8,2) NOT NULL,
  `customer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_item_tbl`
--

CREATE TABLE `quotation_item_tbl` (
  `quotation_item_id` int NOT NULL,
  `quotation_id` int NOT NULL,
  `quotation_item_name` varchar(200) NOT NULL,
  `quotation_item_features` varchar(200) DEFAULT NULL,
  `quotation_item_quantity` int NOT NULL,
  `quotation_item_price` float(18,2) NOT NULL,
  `quotation_item_description` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `quotation_tbl`
--

CREATE TABLE `quotation_tbl` (
  `quotation_id` int NOT NULL,
  `date_created` date NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone_no` varchar(12) NOT NULL,
  `warranty_period` varchar(20) DEFAULT NULL,
  `delivery_date` date NOT NULL,
  `expiration_date` date NOT NULL,
  `tax_amount` double(18,2) DEFAULT NULL,
  `tax_rate` float(10,3) DEFAULT NULL,
  `discount_rate` float(10,3) DEFAULT NULL,
  `discount` double(18,2) DEFAULT NULL,
  `company_id` int NOT NULL,
  `date_updated` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sales_receipt_tbl`
--

CREATE TABLE `sales_receipt_tbl` (
  `receipt_id` int NOT NULL,
  `company_id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `date_made` date NOT NULL,
  `date_recorded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invoice_no` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `payment_method` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `note` text COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `qty` int NOT NULL,
  `tax` double(5,2) DEFAULT NULL,
  `discount` double(10,2) DEFAULT NULL,
  `sold_price` decimal(18,2) NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_account_tbl`
--

CREATE TABLE `sub_account_tbl` (
  `sub_account_id` int NOT NULL,
  `sub_account_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `account_id` int NOT NULL,
  `account_type` enum('Current','Non-Current') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_account_tbl`
--

INSERT INTO `sub_account_tbl` (`sub_account_id`, `sub_account_name`, `account_id`, `account_type`) VALUES
(1, 'Cash', 1, 'Current'),
(2, 'Accounts Receivable', 1, 'Current'),
(3, 'Inventory', 1, 'Current'),
(4, 'Prepaid Expenses', 1, 'Current'),
(5, 'Equipment', 1, 'Non-Current'),
(6, 'Buildings', 1, 'Non-Current'),
(7, 'Land', 1, 'Non-Current'),
(8, 'Investments', 1, 'Non-Current'),
(9, 'Current Assets', 1, 'Current'),
(10, 'Non-current Assets', 1, 'Non-Current'),
(11, 'Accounts Payable', 2, 'Current'),
(12, 'Notes Payable', 2, 'Current'),
(13, 'Wages Payable', 2, 'Current'),
(14, 'Interest Payable', 2, 'Current'),
(15, 'Unearned Revenue', 2, 'Current'),
(16, 'Accrued Liabilities', 2, 'Current'),
(17, 'Bonds Payable', 2, 'Non-Current'),
(18, 'Current Liabilities', 2, 'Current'),
(19, 'Non-current Liabilities', 2, 'Non-Current'),
(20, 'Common Stock', 3, NULL),
(21, 'Preferred Stock', 3, NULL),
(22, 'Additional Paid-in Capital', 3, NULL),
(23, 'Retained Earnings', 3, NULL),
(24, 'Treasury Stock', 3, NULL),
(26, 'Sales', 4, NULL),
(27, 'Service Revenue', 4, NULL),
(28, 'Interest Revenue', 4, NULL),
(29, 'Rental Revenue', 4, NULL),
(31, 'Cost of Goods Sold', 5, NULL),
(32, 'Wages Expense', 5, NULL),
(33, 'Rent Expense', 5, NULL),
(34, 'Utilities Expense', 5, NULL),
(35, 'Depreciation Expense', 5, NULL),
(36, 'Interest Expense', 5, NULL),
(37, 'Taxes Expense', 5, NULL),
(38, 'Insurance Expense', 5, NULL),
(39, 'Advertising Expense', 5, NULL),
(41, 'Goodwill', 1, NULL),
(42, 'Trademarks', 1, NULL),
(43, 'Patents', 1, NULL),
(44, 'Customer List', 1, NULL),
(45, 'Licenses', 1, NULL),
(46, 'Franchise Rights', 1, NULL),
(47, 'Software', 1, NULL),
(48, 'Land Improvements', 1, NULL),
(49, 'Vehicles', 1, NULL),
(50, 'Furniture and Fixtures', 1, NULL),
(51, 'Accrued Expenses', 2, 'Current'),
(52, 'Customer Deposits', 2, 'Current'),
(53, 'Income Taxes Payable', 2, 'Current'),
(54, 'Sales Taxes Payable', 2, 'Current'),
(55, 'Warranty Liability', 2, 'Current'),
(56, 'Pension Liabilities', 2, 'Current'),
(57, 'Deferred Tax Liabilities', 2, 'Non-Current'),
(58, 'Mortgages Payable', 2, 'Non-Current'),
(59, 'Lease Obligations', 2, 'Non-Current'),
(60, 'Deferred Revenue', 2, 'Non-Current'),
(61, 'Preferred Stock Dividends', 3, 'Current'),
(62, 'Additional Retained Earnings', 3, 'Current'),
(63, 'Dividends Payable', 3, NULL),
(64, 'Earnings Surplus', 3, NULL),
(65, 'Restricted Retained Earnings', 3, NULL),
(66, 'Interest Income', 4, NULL),
(67, 'Dividend Income', 4, NULL),
(68, 'Gains on Sale of Investments', 4, NULL),
(69, 'Royalty Income', 4, NULL),
(70, 'Consulting Revenue', 4, NULL),
(71, 'Depreciation and Amortization', 5, NULL),
(72, 'Repairs and Maintenance', 5, NULL),
(73, 'Professional Fees', 5, NULL),
(74, 'Travel and Entertainment', 5, NULL),
(75, 'Bad Debt Expense', 5, NULL),
(77, 'Saving Account', 1, NULL),
(78, 'Current Account', 1, NULL),
(79, 'Trade Creditors', 2, 'Current'),
(80, 'Salary', 5, 'Current'),
(81, 'Electricity', 5, 'Current'),
(82, 'Water', 5, 'Current'),
(83, 'Telephone', 5, 'Current'),
(84, 'Office Expense', 5, 'Current'),
(85, 'Fuel', 5, 'Current'),
(86, 'Repair and Maintenance', 5, 'Current'),
(87, 'ETF', 5, 'Current'),
(88, 'EPF', 5, 'Current'),
(89, 'Bonus', 5, 'Current'),
(90, 'Transport', 5, 'Current'),
(91, 'Trade debtors', 1, 'Current'),
(97, 'Bank Loan', 2, 'Non-Current'),
(98, 'Trade Receivable', 1, 'Current'),
(99, 'Related Party', 1, 'Current'),
(100, 'Stated Capital', 3, 'Current'),
(101, 'Bank Ovredraft', 2, 'Current'),
(102, 'Salary Payable', 2, 'Current'),
(103, 'ETF Payable', 2, 'Current'),
(104, 'EPF Payable', 2, 'Current'),
(105, 'APPIT', 5, 'Current'),
(106, 'APPIT Payable', 2, 'Current'),
(109, 'Finance Cost', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sub_account_type_tbl`
--

CREATE TABLE `sub_account_type_tbl` (
  `sub_account_type_id` int NOT NULL,
  `sub_account_id` int NOT NULL,
  `company_id` int NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `initial_value` float(18,2) NOT NULL,
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_logins_tbl`
--

CREATE TABLE `sub_logins_tbl` (
  `sub_login_id` int NOT NULL,
  `company_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `password` varchar(65) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `super_admin_tbl`
--

CREATE TABLE `super_admin_tbl` (
  `s_admin_id` int NOT NULL,
  `s_admin_name` varchar(50) NOT NULL,
  `s_admin_username` varchar(50) NOT NULL,
  `s_admin_password` varchar(65) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `super_admin_tbl`
--

INSERT INTO `super_admin_tbl` (`s_admin_id`, `s_admin_name`, `s_admin_username`, `s_admin_password`, `last_login`) VALUES
(1, 'Admin', 'admin@ginum.lk', '$2y$10$//fQ4JwZzOtJ04gjY11rKu3vdVKOwFjyXDx/xI4Ce4BT8TAx.03QO', '2024-09-25 23:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_tbl`
--

CREATE TABLE `supplier_tbl` (
  `supplier_id` int NOT NULL,
  `supplier_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `supplier_type` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `phone_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `vat_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `business_reg_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `br_report_path` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_category_id` int NOT NULL,
  `date_added` date NOT NULL,
  `date_updated` date DEFAULT NULL,
  `company_id` int NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_tbl`
--

CREATE TABLE `task_tbl` (
  `task_id` int NOT NULL,
  `project_id` int NOT NULL,
  `task_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `priority` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `created_date` date NOT NULL,
  `due_date` date NOT NULL,
  `description` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxes_tbl`
--

CREATE TABLE `taxes_tbl` (
  `tax_id` int NOT NULL,
  `company_id` int NOT NULL,
  `tax_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `tax_percentage` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log_tbl`
--

CREATE TABLE `transaction_log_tbl` (
  `transaction_id` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `transaction_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `debit_account` int DEFAULT NULL,
  `credit_account` int DEFAULT NULL,
  `transaction_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_tbl`
--
ALTER TABLE `account_tbl`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `bank_account_tbl`
--
ALTER TABLE `bank_account_tbl`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `bank_details_id` (`bank_details_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `company_sub_account_balance_id` (`company_sub_account_balance_id`);

--
-- Indexes for table `bank_details_tbl`
--
ALTER TABLE `bank_details_tbl`
  ADD PRIMARY KEY (`bank_details_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `bank_details_id` (`bank_details_id`);

--
-- Indexes for table `bank_statement_tbl`
--
ALTER TABLE `bank_statement_tbl`
  ADD PRIMARY KEY (`stmt_id`),
  ADD KEY `sub_account_id` (`sub_account_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `company_category_tbl`
--
ALTER TABLE `company_category_tbl`
  ADD PRIMARY KEY (`company_category_id`),
  ADD KEY `company_category_id` (`company_category_id`);

--
-- Indexes for table `company_sub_account_balance`
--
ALTER TABLE `company_sub_account_balance`
  ADD PRIMARY KEY (`company_sub_account_balance_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `sub_account_id` (`sub_account_id`),
  ADD KEY `company_sub_account_balance_id` (`company_sub_account_balance_id`),
  ADD KEY `sub_account_type_id` (`sub_account_type_id`);

--
-- Indexes for table `company_tbl`
--
ALTER TABLE `company_tbl`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `company_category_id` (`company_category_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `currency` (`currency_id`),
  ADD KEY `privilege_id` (`privilege_id`);

--
-- Indexes for table `country_currency_tbl`
--
ALTER TABLE `country_currency_tbl`
  ADD PRIMARY KEY (`country_id`,`currency_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `country_tbl`
--
ALTER TABLE `country_tbl`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `currency_tbl`
--
ALTER TABLE `currency_tbl`
  ADD PRIMARY KEY (`currency_id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `department_tbl`
--
ALTER TABLE `department_tbl`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `compnay_id` (`company_id`);

--
-- Indexes for table `depreciation_tbl`
--
ALTER TABLE `depreciation_tbl`
  ADD PRIMARY KEY (`depreciation_id`),
  ADD KEY `depreciation_id` (`depreciation_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `sub_account_type_id` (`sub_account_type_id`);

--
-- Indexes for table `designation_tbl`
--
ALTER TABLE `designation_tbl`
  ADD PRIMARY KEY (`designation_id`),
  ADD KEY `designation_id` (`designation_id`),
  ADD KEY `dpt` (`department_id`);

--
-- Indexes for table `edit_requests`
--
ALTER TABLE `edit_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `sub_login_id` (`sub_login_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `employee_tbl`
--
ALTER TABLE `employee_tbl`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `designation_id` (`designation_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `expense_tbl`
--
ALTER TABLE `expense_tbl`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `expense_id` (`expense_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `sub_account_id` (`sub_account_id`);

--
-- Indexes for table `extended_tax_tbl`
--
ALTER TABLE `extended_tax_tbl`
  ADD PRIMARY KEY (`ext_tax_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `income_tbl`
--
ALTER TABLE `income_tbl`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `company` (`company_id`),
  ADD KEY `sub_acc_id` (`sub_account_id`);

--
-- Indexes for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `item` (`item_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `item_category_tbl`
--
ALTER TABLE `item_category_tbl`
  ADD PRIMARY KEY (`item_category_id`),
  ADD KEY `item_category_id` (`item_category_id`);

--
-- Indexes for table `item_tbl`
--
ALTER TABLE `item_tbl`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `company` (`company_id`);

--
-- Indexes for table `payroll_tbl`
--
ALTER TABLE `payroll_tbl`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `privilege_tbl`
--
ALTER TABLE `privilege_tbl`
  ADD PRIMARY KEY (`privilege_id`),
  ADD KEY `privilege_id` (`privilege_id`);

--
-- Indexes for table `project_tbl`
--
ALTER TABLE `project_tbl`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `quotation_item_tbl`
--
ALTER TABLE `quotation_item_tbl`
  ADD PRIMARY KEY (`quotation_item_id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `quotation_tbl`
--
ALTER TABLE `quotation_tbl`
  ADD PRIMARY KEY (`quotation_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `quotation_id` (`quotation_id`);

--
-- Indexes for table `sales_receipt_tbl`
--
ALTER TABLE `sales_receipt_tbl`
  ADD PRIMARY KEY (`receipt_id`),
  ADD KEY `company` (`company_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `prjct` (`project_id`);

--
-- Indexes for table `sub_account_tbl`
--
ALTER TABLE `sub_account_tbl`
  ADD PRIMARY KEY (`sub_account_id`),
  ADD KEY `acc` (`account_id`);

--
-- Indexes for table `sub_account_type_tbl`
--
ALTER TABLE `sub_account_type_tbl`
  ADD PRIMARY KEY (`sub_account_type_id`),
  ADD KEY `sub_account_type_id` (`sub_account_type_id`),
  ADD KEY `sub_account_id` (`sub_account_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `sub_logins_tbl`
--
ALTER TABLE `sub_logins_tbl`
  ADD PRIMARY KEY (`sub_login_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `super_admin_tbl`
--
ALTER TABLE `super_admin_tbl`
  ADD PRIMARY KEY (`s_admin_id`);

--
-- Indexes for table `supplier_tbl`
--
ALTER TABLE `supplier_tbl`
  ADD PRIMARY KEY (`supplier_id`),
  ADD KEY `supplier_tbl` (`supplier_id`),
  ADD KEY `item_category_id` (`item_category_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `task_tbl`
--
ALTER TABLE `task_tbl`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `taxes_tbl`
--
ALTER TABLE `taxes_tbl`
  ADD PRIMARY KEY (`tax_id`),
  ADD KEY `tax_id` (`tax_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `transaction_log_tbl`
--
ALTER TABLE `transaction_log_tbl`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `debit_account` (`debit_account`),
  ADD KEY `credit_account` (`credit_account`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_tbl`
--
ALTER TABLE `account_tbl`
  MODIFY `account_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bank_account_tbl`
--
ALTER TABLE `bank_account_tbl`
  MODIFY `bank_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_details_tbl`
--
ALTER TABLE `bank_details_tbl`
  MODIFY `bank_details_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_statement_tbl`
--
ALTER TABLE `bank_statement_tbl`
  MODIFY `stmt_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_category_tbl`
--
ALTER TABLE `company_category_tbl`
  MODIFY `company_category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `company_sub_account_balance`
--
ALTER TABLE `company_sub_account_balance`
  MODIFY `company_sub_account_balance_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_tbl`
--
ALTER TABLE `company_tbl`
  MODIFY `company_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `country_tbl`
--
ALTER TABLE `country_tbl`
  MODIFY `country_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `currency_tbl`
--
ALTER TABLE `currency_tbl`
  MODIFY `currency_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_tbl`
--
ALTER TABLE `department_tbl`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `depreciation_tbl`
--
ALTER TABLE `depreciation_tbl`
  MODIFY `depreciation_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designation_tbl`
--
ALTER TABLE `designation_tbl`
  MODIFY `designation_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `edit_requests`
--
ALTER TABLE `edit_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_tbl`
--
ALTER TABLE `employee_tbl`
  MODIFY `employee_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_tbl`
--
ALTER TABLE `expense_tbl`
  MODIFY `expense_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extended_tax_tbl`
--
ALTER TABLE `extended_tax_tbl`
  MODIFY `ext_tax_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_tbl`
--
ALTER TABLE `income_tbl`
  MODIFY `income_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  MODIFY `inventory_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_category_tbl`
--
ALTER TABLE `item_category_tbl`
  MODIFY `item_category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item_tbl`
--
ALTER TABLE `item_tbl`
  MODIFY `item_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_tbl`
--
ALTER TABLE `payroll_tbl`
  MODIFY `payroll_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privilege_tbl`
--
ALTER TABLE `privilege_tbl`
  MODIFY `privilege_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_tbl`
--
ALTER TABLE `project_tbl`
  MODIFY `project_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_item_tbl`
--
ALTER TABLE `quotation_item_tbl`
  MODIFY `quotation_item_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotation_tbl`
--
ALTER TABLE `quotation_tbl`
  MODIFY `quotation_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_receipt_tbl`
--
ALTER TABLE `sales_receipt_tbl`
  MODIFY `receipt_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_account_tbl`
--
ALTER TABLE `sub_account_tbl`
  MODIFY `sub_account_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `sub_account_type_tbl`
--
ALTER TABLE `sub_account_type_tbl`
  MODIFY `sub_account_type_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_logins_tbl`
--
ALTER TABLE `sub_logins_tbl`
  MODIFY `sub_login_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `super_admin_tbl`
--
ALTER TABLE `super_admin_tbl`
  MODIFY `s_admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_tbl`
--
ALTER TABLE `supplier_tbl`
  MODIFY `supplier_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_tbl`
--
ALTER TABLE `task_tbl`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxes_tbl`
--
ALTER TABLE `taxes_tbl`
  MODIFY `tax_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_log_tbl`
--
ALTER TABLE `transaction_log_tbl`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank_account_tbl`
--
ALTER TABLE `bank_account_tbl`
  ADD CONSTRAINT `balance` FOREIGN KEY (`company_sub_account_balance_id`) REFERENCES `company_sub_account_balance` (`company_sub_account_balance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_account_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bankDetailsId` FOREIGN KEY (`bank_details_id`) REFERENCES `bank_details_tbl` (`bank_details_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bank_details_tbl`
--
ALTER TABLE `bank_details_tbl`
  ADD CONSTRAINT `bank_details_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bank_statement_tbl`
--
ALTER TABLE `bank_statement_tbl`
  ADD CONSTRAINT `bank` FOREIGN KEY (`bank_id`) REFERENCES `bank_account_tbl` (`bank_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_statement_tbl_ibfk_1` FOREIGN KEY (`sub_account_id`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_statement_tbl_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_sub_account_balance`
--
ALTER TABLE `company_sub_account_balance`
  ADD CONSTRAINT `company_sub_account_balance_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`),
  ADD CONSTRAINT `company_sub_account_balance_ibfk_2` FOREIGN KEY (`sub_account_id`) REFERENCES `sub_account_tbl` (`sub_account_id`),
  ADD CONSTRAINT `sb_acc_type` FOREIGN KEY (`sub_account_type_id`) REFERENCES `sub_account_type_tbl` (`sub_account_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `company_tbl`
--
ALTER TABLE `company_tbl`
  ADD CONSTRAINT `company_tbl_ibfk_1` FOREIGN KEY (`company_category_id`) REFERENCES `company_category_tbl` (`company_category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `country` FOREIGN KEY (`country_id`) REFERENCES `country_tbl` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `currency` FOREIGN KEY (`currency_id`) REFERENCES `currency_tbl` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `privileges` FOREIGN KEY (`privilege_id`) REFERENCES `privilege_tbl` (`privilege_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `country_currency_tbl`
--
ALTER TABLE `country_currency_tbl`
  ADD CONSTRAINT `country_currency_tbl_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country_tbl` (`country_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `country_currency_tbl_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currency_tbl` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `currency_tbl`
--
ALTER TABLE `currency_tbl`
  ADD CONSTRAINT `currency_tbl_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `country_tbl` (`country_id`);

--
-- Constraints for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  ADD CONSTRAINT `customer_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `department_tbl`
--
ALTER TABLE `department_tbl`
  ADD CONSTRAINT `com` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `depreciation_tbl`
--
ALTER TABLE `depreciation_tbl`
  ADD CONSTRAINT `cmpny_id` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_acc_type_id` FOREIGN KEY (`sub_account_type_id`) REFERENCES `sub_account_type_tbl` (`sub_account_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `designation_tbl`
--
ALTER TABLE `designation_tbl`
  ADD CONSTRAINT `dpt` FOREIGN KEY (`department_id`) REFERENCES `department_tbl` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `edit_requests`
--
ALTER TABLE `edit_requests`
  ADD CONSTRAINT `cmp` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `edit_requests_ibfk_1` FOREIGN KEY (`sub_login_id`) REFERENCES `sub_logins_tbl` (`sub_login_id`);

--
-- Constraints for table `employee_tbl`
--
ALTER TABLE `employee_tbl`
  ADD CONSTRAINT `companyId` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `designation` FOREIGN KEY (`designation_id`) REFERENCES `designation_tbl` (`designation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expense_tbl`
--
ALTER TABLE `expense_tbl`
  ADD CONSTRAINT `comapny` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_account` FOREIGN KEY (`sub_account_id`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `extended_tax_tbl`
--
ALTER TABLE `extended_tax_tbl`
  ADD CONSTRAINT `extended_tax_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `income_tbl`
--
ALTER TABLE `income_tbl`
  ADD CONSTRAINT `income_tbl_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_acc_id` FOREIGN KEY (`sub_account_id`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_tbl`
--
ALTER TABLE `inventory_tbl`
  ADD CONSTRAINT `inventory_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_tbl_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item_tbl` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `item_tbl`
--
ALTER TABLE `item_tbl`
  ADD CONSTRAINT `company` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payroll_tbl`
--
ALTER TABLE `payroll_tbl`
  ADD CONSTRAINT `employeeID` FOREIGN KEY (`employee_id`) REFERENCES `employee_tbl` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_tbl`
--
ALTER TABLE `project_tbl`
  ADD CONSTRAINT `company_id` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `customer` FOREIGN KEY (`customer_id`) REFERENCES `customer_tbl` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_tbl_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department_tbl` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quotation_item_tbl`
--
ALTER TABLE `quotation_item_tbl`
  ADD CONSTRAINT `quotation_id` FOREIGN KEY (`quotation_id`) REFERENCES `quotation_tbl` (`quotation_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_receipt_tbl`
--
ALTER TABLE `sales_receipt_tbl`
  ADD CONSTRAINT `item` FOREIGN KEY (`item_id`) REFERENCES `item_tbl` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prjct_id` FOREIGN KEY (`project_id`) REFERENCES `project_tbl` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_receipt_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_account_tbl`
--
ALTER TABLE `sub_account_tbl`
  ADD CONSTRAINT `acc` FOREIGN KEY (`account_id`) REFERENCES `account_tbl` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_account_type_tbl`
--
ALTER TABLE `sub_account_type_tbl`
  ADD CONSTRAINT `cmpny` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_acc` FOREIGN KEY (`sub_account_id`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_logins_tbl`
--
ALTER TABLE `sub_logins_tbl`
  ADD CONSTRAINT `company_id_key` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_id_key` FOREIGN KEY (`employee_id`) REFERENCES `employee_tbl` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_tbl`
--
ALTER TABLE `supplier_tbl`
  ADD CONSTRAINT `supplier_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_tbl_ibfk_2` FOREIGN KEY (`item_category_id`) REFERENCES `item_category_tbl` (`item_category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task_tbl`
--
ALTER TABLE `task_tbl`
  ADD CONSTRAINT `task` FOREIGN KEY (`project_id`) REFERENCES `project_tbl` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `taxes_tbl`
--
ALTER TABLE `taxes_tbl`
  ADD CONSTRAINT `taxes_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_log_tbl`
--
ALTER TABLE `transaction_log_tbl`
  ADD CONSTRAINT `crt_acc` FOREIGN KEY (`credit_account`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbt_acc` FOREIGN KEY (`debit_account`) REFERENCES `sub_account_tbl` (`sub_account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_log_tbl_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_tbl` (`company_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
