-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 11 Gru 2017, 14:31
-- Wersja serwera: 10.1.22-MariaDB
-- Wersja PHP: 7.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `cms_instalka`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `aktualnosci`
--

CREATE TABLE `aktualnosci` (
  `id` int(11) UNSIGNED NOT NULL,
  `photo` varchar(1022) NOT NULL,
  `date_add` datetime NOT NULL,
  `count` int(11) NOT NULL,
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `auth` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `comments` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `aktualnosci_description`
--

CREATE TABLE `aktualnosci_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_keywords` varchar(255) NOT NULL,
  `page_description` varchar(255) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `title_url` varchar(1022) NOT NULL,
  `content` mediumtext NOT NULL,
  `content_short` varchar(1022) NOT NULL,
  `tagi` varchar(1022) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `name_url` varchar(255) NOT NULL DEFAULT '',
  `path_id` varchar(255) NOT NULL,
  `order` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `depth` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` varchar(16382) NOT NULL,
  `date_add` datetime NOT NULL,
  `author` varchar(255) NOT NULL,
  `group` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `config`
--

CREATE TABLE `config` (
  `name` varchar(255) NOT NULL,
  `value` varchar(16382) NOT NULL,
  `description` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `config`
--

INSERT INTO `config` (`name`, `value`, `description`, `active`) VALUES
('firm_name', 'T.CMS', 'Nazwa firmy', 1),
('alt_img', 'T.CMS', 'Alt obrazków na stronie', 1),
('google_analytics_universal', '', 'Kod statystyk Google Analytics wersja uniwersalna', 0),
('verify_v1', '', 'Meta tag Google verify-1 [proszę podać cały kod]', 1),
('show_tagi', '1', 'Opcja określa czy pokazać tagi na stronie (1 = tak, 0 = nie)', 1),
('comments_active', '1', 'Czy komentarze sa aktywne w systemie (1 = tak, 0 = nie)', 0),
('comments_not_logged', '1', 'Opcja określa czy komentarze mają być widoczne dla niezalogowanych użytkowników (1 = tak, 0 = nie)', 0),
('comments_not_logged_post', '1', 'Opcja określa czy niezalogowani użytkownicy mogą pisać komentarze (1 = tak, 0 = nie)', 0),
('limit_admin', '20', 'Ilość elementów wyświetlanych w panelu admina', 0),
('limit_home', '2', 'Ilość nagłówków wiadomości na stronie głównej', 1),
('limit_page', '5', 'Ilość nagłówków wiadomości na podstronach', 1),
('limit_rss', '10', 'Ilość nagłówków wiadomości w kanale RSS', 1),
('limit_rejestr', '50', 'Ilość pozycji w rejestrze zmian', 1),
('op_page_title', '3', 'Domyślne ustawienia SEO dla TITLE', 0),
('op_page_keywords', '3', 'Domyślne ustawienia SEO dla KEYWORDS', 0),
('op_page_description', '5', 'Domyślne ustawienia SEO dla DESCRIPTION', 0),
('default_template', 'default', 'Domyślny szablon stron', 0),
('thumb_width_default', '125', 'Domyślna szerokość miniaturki w galerii', 0),
('thumb_height_default', '125', 'Domyślna wysokość miniaturki w galerii', 0),
('cache_on', '0', 'Aktywność cache  (1 = tak, 0 = nie)', 0),
('cache_lifetime', '60', 'Długość życia cache', 0),
('cache_logged_on', '0', 'Aktywność cache dla zalogowanych [1 = tak]', 0),
('aktualizacja', '2013-07-22', 'Data ostatniej aktualizacji', 0),
('katalog_baza', 'baza-444ee92c578af3e2e1f99a28e21bcaf0', 'Zakodowana nazwa katalogu bazy danych tego serwisu', 0),
('ver', 'T.CMS 4.5 SEO', 'Wersja systemu T.CMS', 0),
('files_types', 'pdf, doc, docx, txt, rtf, mpg, avi, zip, rar, jpg, png, gif, bmp', 'Dozwolone rozszerzenia plików dołączanych do podstron', 0),
('files_max_size', '20000', 'Maksymalna wielkość plików dołączanych do podstron podana w KB', 0),
('lang_selection_method', 'old', 'Metoda wyboru języka (old - stary CMS, subdomain - poprzez subdomenę, domain - poprzez domenę)', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `config_extra`
--

CREATE TABLE `config_extra` (
  `name` varchar(255) NOT NULL,
  `value` varchar(16382) NOT NULL,
  `description` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `config_extra`
--

INSERT INTO `config_extra` (`name`, `value`, `description`) VALUES
('newsletter_info', '<p>informacja o biuletynie</p>', 'Informacja o biuletynie'),
('newsletter_rules', '<p>regulamin biuletynu</p>', 'Regulamin subskrypcji e-biuletynu'),
('main_page', '<p>zawartość strony gł&oacute;wnej</p>', 'Zawartość strony głównej'),
('mail_add_user', '<table style=\"margin: 0px; border-collapse: collapse; font-family: Arial; font-size: 11px; padding: 0px; width: 100%;\" border=\"0\" align=\"left\">\r\n<tbody>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #3a6007; height: 2px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #83b42c; height: 14px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; background-color: #f0f0f0; padding-left: 40px; padding-right: 40px; height: 45px; padding-top: 10px;\"><strong>Witaj #IMIE#! </strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>Otrzymaliśmy następujące dane:<br /> Login: <strong>#LOGIN#</strong><br /> Hasło: <strong>#HASLO#</strong><br /> Typ konta: <strong>#TYP_KONTA#</strong><br /> Nazwa firmy: <strong>#NAZWA_FIRMY#</strong><br /> NIP: <strong>#NIP# <br /> <br /> </strong></p>\r\n<hr width=\"100%\" size=\"2\" /></td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>Imię: <strong>#IMIE#</strong><br /> Nazwisko: <strong>#NAZWISKO#</strong><br /> E-mail: <strong>#EMAIL#</strong><br /> Adres: <strong>#STREET# #NRBUD#/#NRLOK#, #KOD_POCZTOWY#&nbsp; #MIEJSCOWOSC#</strong><br /> Telefon: <strong>#TELEFON# </strong></p>\r\n<p>&nbsp;</p>\r\n<p><br /> Jeżeli chcesz potwierdzić rejestrację w naszym serwisie, kliknij proszę na poniższy link:</p>\r\n<p style=\"color: #3a6007;\"><br /> #CONFIRM_URL#</p>\r\n<br /><hr width=\"100%\" size=\"2\" /></td>\r\n</tr>\r\n<tr align=\"left\">\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>&nbsp;Jeśli chcesz usunąć swoje dane z naszego systemu, użyj opcji:</p>\r\n<p style=\"color: #3a6007;\"><br /> #DELETE_URL#</p>\r\n<p>&nbsp;</p>\r\n<p>Dziękujemy za rejestrację!</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>', '<b>Prośba o potwierdzenie rejestracji w serwisie</b>\r\n\r\nZmienne, które można użyć w liście: \r\n<b>#LOGIN#</b> - login nowego użytkownika \r\n<b>#HASLO#</b> - hasło podane podczas rejestracji \r\n<b>#TYP_KONTA#</b> - firma lub osoba fizyczna \r\n<b>#NAZWA_FIRMY#</b> - podana podczas rejestracji \r\n<b>#NIP#</b> - podany podczas rejestracji \r\n<b>#IMIE#</b> - imię podane podczas rejestracji \r\n<b>#NAZWISKO#</b> \r\n<b>#EMAIL#</b> - e-mail użytkownika\r\n<b>#STREET# #NRBUD#/#NRLOK#</b> - ulica, numer budynku/lokalu \r\n<b>#KOD_POCZTOWY#</b> \r\n<b>#MIEJSCOWOSC#</b> \r\n<b>#TELEFON#</b> \r\n<b>#CONFIRM_URL#</b> - link, na jaki musi kliknąć użytkownik aby potwierdzić rejestrację \r\n<b>#DELETE_URL#</b> - link, na jaki musi kliknąć użytkownik, aby usunąć swoje konto z systemu'),
('mail_add_user_admin', '<table style=\"margin: 0px; border-collapse: collapse; font-family: Arial; font-size: 11px; padding: 0px; width: 100%;\" border=\"0\" align=\"left\">\r\n<tbody>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #3a6007; height: 2px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #83b42c; height: 14px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; background-color: #f0f0f0; padding-left: 40px; padding-right: 40px; height: 45px; padding-top: 10px;\"><strong>Witaj! </strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>Dnia #DATE# w serwisie <strong>#SERWER#</strong> został zarejestrowany użytkownik <strong>#LOGIN#</strong>&nbsp;</p>\r\n<p>&nbsp;</p>\r\nMożesz przejrzeć jego dane w panelu administracyjnym używając opcji:\r\n<p style=\"color: #3a6007;\"><br /> #EDIT_URL#</p>\r\n<br /><hr width=\"100%\" size=\"2\" /></td>\r\n</tr>\r\n</tbody>\r\n</table>', '<b>Informacja dla administratora o nowym użytkowniku</b>\r\n\r\nZmienne, które można użyć w liście: \r\n<b>#DATE#</b> - aktualna data i czas\r\n<b>#SERWER#</b> - adres serwera\r\n<b>#LOGIN#</b> - login nowego użytkownika \r\n<b>#EDIT_URL#</b> - link, edycja danych w panelu admina'),
('mail_reminder_pass', '<table style=\"margin: 0px; border-collapse: collapse; font-family: Arial; font-size: 11px; padding: 0px; width: 100%;\" border=\"0\" align=\"left\">\r\n<tbody>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #3a6007; height: 2px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #83b42c; height: 14px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; background-color: #f0f0f0; padding-left: 40px; padding-right: 40px; height: 45px; padding-top: 10px;\"><strong>Witaj! </strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>Ktoś (prawdopodobnie Ty) użył opcji przypomnienie hasła dla Twojego konta.<br /> System wygenerował nowe hasło:</p>\r\n<p><strong>#NEW_PASS#</strong></p>\r\n<p>Możesz się teraz zalogować używając nowego hasła. Hasło można zmienić w panelu użytkownika po zalogowaniu.</p>\r\n<p style=\"color: #3a6007;\"><br /> #LOGIN_URL#</p>\r\n<br /><hr width=\"100%\" size=\"2\" /></td>\r\n</tr>\r\n</tbody>\r\n</table>', '<b>Informacja o zmianie hasła dla użytkownika</b>\r\n\r\nZmienne, które można użyć w liście: \r\n<b>#NEW_PASS#</b> - wyswitla nowo wygenerowane haslo\r\n<b>#LOGIN_URL#</b> - link, logowanie do serwisu'),
('mail_add_oferta', '<table style=\"margin: 0px; border-collapse: collapse; font-family: Arial; font-size: 11px; padding: 0px; width: 100%;\" border=\"0\" align=\"left\">\r\n<tbody>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #3a6007; height: 2px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"line-height: 1px; background-color: #83b42c; height: 14px;\">&nbsp;</td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; background-color: #f0f0f0; padding-left: 40px; padding-right: 40px; height: 45px; padding-top: 10px;\"><strong>Witaj! </strong></td>\r\n</tr>\r\n<tr>\r\n<td style=\"padding-bottom: 10px; padding-left: 40px; padding-right: 40px; color: #6c6c6c; padding-top: 10px;\">\r\n<p>Dnia #DATE# w serwisie <strong>#SERWER#</strong> dodano nową ofertę przez użytkownika <strong>#LOGIN#</strong>&nbsp;</p>\r\n<p>&nbsp;</p>\r\nMożesz przejrzeć ofertę używając opcji:\r\n<p style=\"color: #3a6007;\"><br /> #SHOW_URL#</p>\r\n<br /><hr width=\"100%\" size=\"2\" /></td>\r\n</tr>\r\n</tbody>\r\n</table>', '<b>Informacja o dodaniu nowej oferty w serwisie</b>\n\nZmienne, które można użyć w liście: \n<b>#DATE#</b> - aktualna data i czas\n<b>#SERWER#</b> - adres serwera\n<b>#LOGIN#</b> - login nowego użytkownika \n<b>#SHOW_URL#</b> - link, podgląd oferty'),
('auth_email', '---tutaj-wpisz-nazwe-konta-email---', '<b>Adres e-mail do autoryzacji SMTP</b>'),
('auth_pass', '---a-tutaj-haslo---', '<b>Hasło dla e-mail do autoryzacji SMTP</b>'),
('auth_smtp', '---wysyłanie przez smtp------', '<b>Ustawienie funkcji wysyłania na smtp</b>'),
('admin_email', '---adres administratora serwisu------', '<b>Adres e-mail administratora systemu</b>'),
('biuro_email', '---adres z jakiego klienci dostaną e-mail------', '<b>Adres e-mail z jakiego klienci dostaną e-mail</b>'),
('auth_port', '---port serwera mailowego---', '<b>Port serwera mailowego</b>'),
('auth_host', '---host serwera mailowego---', '<b>Host serwera mailowego</b>'),
('auth_auth', '1', '<b>Włączenie/wyłączenie autoryzacji</b>');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `config_lang`
--

CREATE TABLE `config_lang` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `config_lang`
--

INSERT INTO `config_lang` (`id`, `name`) VALUES
(1, 'newsletter_info'),
(2, 'newsletter_rules'),
(3, 'main_page'),
(4, 'kontakt'),
(5, 'page_title'),
(6, 'page_title_prefix'),
(7, 'page_title_suffix'),
(8, 'page_keywords'),
(9, 'page_description');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `config_lang_description`
--

CREATE TABLE `config_lang_description` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `value` varchar(16382) NOT NULL,
  `description` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `config_lang_description`
--

INSERT INTO `config_lang_description` (`id`, `parent_id`, `language_id`, `value`, `description`) VALUES
(1, 1, 1, '<p>Proin nec mi massa. Integer laoreet mi vulputate eros interdum sodales. Donec varius eros sed eros commodo bibendum. Curabitur ac rhoncus lorem. Nulla quis augue eu nisi varius pulvinar. Ut sagittis ligula eu mauris rutrum convallis. Quisque hendrerit euismod neque sit amet facilisis. Aenean porta luctus lectus. Donec vitae nisl sed ipsum scelerisque pulvinar quis eget nibh.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</strong></p>\r\n<p>&nbsp;</p>\r\n<p>Nunc nisl lectus, condimentum eget molestie id, pulvinar nec <a href=\"#\">risus</a>. Integer laoreet accumsan mauris vitae fermentum. Integer purus tellus, ornare eu eleifend sit amet, faucibus id lorem. Praesent sagittis tempor posuere. Maecenas eget nunc ut justo dictum commodo. Donec elit elit, molestie non mattis vitae, tristique eget leo.</p>\r\n<ul>\r\n<li>nulla vitae diam arcu,</li>\r\n<li>non imperdiet sapien.</li>\r\n</ul>\r\n<p>Donec et neque in elit euismod lacinia in eget dolor. Quisque faucibus neque nec mi vehicula eu condimentum magna viverra. Aenean id libero sapien. Pellentesque vel elit dui, sit amet euismod orci. Donec vitae lorem vel nibh dapibus varius. Curabitur placerat congue ligula, ut mattis neque iaculis ut. Sed tristique dui at neque tempor sit amet porttitor quam iaculis. Maecenas quis nulla quis erat auctor egestas eget quis orci.</p>', ''),
(2, 1, 2, '<p>informacja o biuletynie en</p>', ''),
(3, 1, 3, '<p>informacja o biuletynie de</p>', ''),
(4, 1, 4, '<p>informacja o biuletynie ru</p>', ''),
(5, 2, 1, '<p>regulamin biuletynu pl</p>', ''),
(6, 2, 2, '<p>regulamin biuletynu en</p>', ''),
(7, 2, 3, '<p>regulamin biuletynu de</p>', ''),
(8, 2, 4, '<p>regulamin biuletynu ru</p>', ''),
(9, 3, 1, '<p>Suspendisse nulla libero, blandit et rutrum ut, hendrerit ac dolor. Etiam pellentesque euismod metus sit amet aliquam. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Cras id enim lectus. Mauris at felis eget est porttitor porta. Fusce porttitor augue non diam <a href=\"#\">ullamcorper</a> accumsan. Nunc tristique mattis semper. Vestibulum luctus, diam nec condimentum tincidunt, elit nisi porta elit, nec fermentum orci quam a odio.</p>\r\n<p>&nbsp;</p>\r\n<p><strong>Aliquam sodales lorem eget magna bibendum pharetra.</strong> Quisque molestie imperdiet ornare. Curabitur at ante ut odio luctus tristique ut accumsan sapien. Donec eu purus a ligula feugiat rutrum.</p>\r\n<ul>\r\n<li>Curabitur arcu odio,</li>\r\n<li>molestie aliquet ullamcorper molestie,</li>\r\n<li>ultricies in nisl.</li>\r\n</ul>\r\n<p>Aliquam mattis porttitor commodo. Praesent accumsan gravida odio, et tristique velit viverra ut. Proin erat ipsum, rhoncus vitae faucibus non, ultricies et erat. Curabitur nunc augue, euismod eget placerat tristique, lacinia ac odio. Maecenas a tempor libero. Vivamus convallis ultrices erat, ac porttitor dui ornare et. Suspendisse quis elementum libero. In auctor nulla id augue tempus a venenatis purus aliquam. Cras malesuada convallis elit porta vulputate. Vivamus tristique eleifend ligula, ornare pellentesque ligula blandit sed.</p>', ''),
(10, 3, 2, '<p>zawartość strony głównej en</p>', ''),
(11, 3, 3, '<p>zawartość strony głównej</p>', ''),
(12, 3, 4, '<p>zawartość strony głównej ru</p>', ''),
(13, 4, 1, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet massa nisi. Integer ut tortor ipsum, et vestibulum massa. Vestibulum pretium nisl at orci laoreet tempor. Suspendisse non magna nec diam pulvinar porttitor. Suspendisse et velit porta erat luctus lacinia. Nunc sed turpis sit amet metus laoreet rhoncus. Morbi nec orci eu ligula venenatis consequat. Curabitur eros lacus, aliquam in malesuada id, luctus nec dolor. Proin malesuada sodales suscipit. Nullam adipiscing rutrum augue, sed varius augue venenatis a. Aenean sed eleifend metus. Vestibulum a ultricies nisl. Maecenas sodales mi at est adipiscing ut gravida ante interdum. Donec eu dapibus lorem. Ut at dolor quis orci volutpat gravida vel vitae lorem. In nec consectetur sem.</p>\r\n<p>&nbsp;</p>\r\n<p>Mauris vitae felis id justo ornare convallis. Ut eget porta erat. Vestibulum lobortis tincidunt iaculis. Proin justo eros, auctor nec rhoncus at, ultrices tincidunt libero. Cras id dui ligula, a volutpat libero. Nullam nulla mauris, ornare cursus pretium sit amet, rhoncus egestas nulla. Curabitur facilisis leo sed nunc egestas tincidunt. Sed et nunc sem, ac tincidunt turpis. In ac nibh a orci facilisis venenatis in blandit nibh. Praesent tortor felis, consectetur non molestie sit amet, semper et augue. Donec ipsum ante, ullamcorper id suscipit et, malesuada in erat. Praesent in rhoncus augue. Mauris lacinia est et dui sodales sodales.</p>\r\n<p>&nbsp;</p>\r\n<p>Nullam in lorem neque, at laoreet quam. In ac luctus est. Aenean commodo purus eu nunc adipiscing fringilla. Vestibulum quis sapien eu ante aliquet vulputate ut sed leo. Nulla molestie iaculis sollicitudin. In laoreet velit at lacus congue tincidunt. Nulla ullamcorper lorem at libero imperdiet eu mollis ligula accumsan. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed varius nunc vel metus ultricies hendrerit. Suspendisse at metus ligula.</p>', ''),
(14, 4, 2, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet massa nisi. Integer ut tortor ipsum, et vestibulum massa. Vestibulum pretium nisl at orci laoreet tempor. Suspendisse non magna nec diam pulvinar porttitor. Suspendisse et velit porta erat luctus lacinia. Nunc sed turpis sit amet metus laoreet rhoncus. Morbi nec orci eu ligula venenatis consequat. Curabitur eros lacus, aliquam in malesuada id, luctus nec dolor. Proin malesuada sodales suscipit. Nullam adipiscing rutrum augue, sed varius augue venenatis a. Aenean sed eleifend metus. Vestibulum a ultricies nisl. Maecenas sodales mi at est adipiscing ut gravida ante interdum. Donec eu dapibus lorem. Ut at dolor quis orci volutpat gravida vel vitae lorem. In nec consectetur sem.</p>\r\n<p>&nbsp;</p>\r\n<p>Mauris vitae felis id justo ornare convallis. Ut eget porta erat. Vestibulum lobortis tincidunt iaculis. Proin justo eros, auctor nec rhoncus at, ultrices tincidunt libero. Cras id dui ligula, a volutpat libero. Nullam nulla mauris, ornare cursus pretium sit amet, rhoncus egestas nulla. Curabitur facilisis leo sed nunc egestas tincidunt. Sed et nunc sem, ac tincidunt turpis. In ac nibh a orci facilisis venenatis in blandit nibh. Praesent tortor felis, consectetur non molestie sit amet, semper et augue. Donec ipsum ante, ullamcorper id suscipit et, malesuada in erat. Praesent in rhoncus augue. Mauris lacinia est et dui sodales sodales.</p>\r\n<p>&nbsp;</p>\r\n<p>Nullam in lorem neque, at laoreet quam. In ac luctus est. Aenean commodo purus eu nunc adipiscing fringilla. Vestibulum quis sapien eu ante aliquet vulputate ut sed leo. Nulla molestie iaculis sollicitudin. In laoreet velit at lacus congue tincidunt. Nulla ullamcorper lorem at libero imperdiet eu mollis ligula accumsan. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed varius nunc vel metus ultricies hendrerit. Suspendisse at metus ligula.</p>', ''),
(15, 4, 3, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet massa nisi. Integer ut tortor ipsum, et vestibulum massa. Vestibulum pretium nisl at orci laoreet tempor. Suspendisse non magna nec diam pulvinar porttitor. Suspendisse et velit porta erat luctus lacinia. Nunc sed turpis sit amet metus laoreet rhoncus. Morbi nec orci eu ligula venenatis consequat. Curabitur eros lacus, aliquam in malesuada id, luctus nec dolor. Proin malesuada sodales suscipit. Nullam adipiscing rutrum augue, sed varius augue venenatis a. Aenean sed eleifend metus. Vestibulum a ultricies nisl. Maecenas sodales mi at est adipiscing ut gravida ante interdum. Donec eu dapibus lorem. Ut at dolor quis orci volutpat gravida vel vitae lorem. In nec consectetur sem.</p>\r\n<p>&nbsp;</p>\r\n<p>Mauris vitae felis id justo ornare convallis. Ut eget porta erat. Vestibulum lobortis tincidunt iaculis. Proin justo eros, auctor nec rhoncus at, ultrices tincidunt libero. Cras id dui ligula, a volutpat libero. Nullam nulla mauris, ornare cursus pretium sit amet, rhoncus egestas nulla. Curabitur facilisis leo sed nunc egestas tincidunt. Sed et nunc sem, ac tincidunt turpis. In ac nibh a orci facilisis venenatis in blandit nibh. Praesent tortor felis, consectetur non molestie sit amet, semper et augue. Donec ipsum ante, ullamcorper id suscipit et, malesuada in erat. Praesent in rhoncus augue. Mauris lacinia est et dui sodales sodales.</p>\r\n<p>&nbsp;</p>\r\n<p>Nullam in lorem neque, at laoreet quam. In ac luctus est. Aenean commodo purus eu nunc adipiscing fringilla. Vestibulum quis sapien eu ante aliquet vulputate ut sed leo. Nulla molestie iaculis sollicitudin. In laoreet velit at lacus congue tincidunt. Nulla ullamcorper lorem at libero imperdiet eu mollis ligula accumsan. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed varius nunc vel metus ultricies hendrerit. Suspendisse at metus ligula.</p>', ''),
(16, 4, 4, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec sit amet massa nisi. Integer ut tortor ipsum, et vestibulum massa. Vestibulum pretium nisl at orci laoreet tempor. Suspendisse non magna nec diam pulvinar porttitor. Suspendisse et velit porta erat luctus lacinia. Nunc sed turpis sit amet metus laoreet rhoncus. Morbi nec orci eu ligula venenatis consequat. Curabitur eros lacus, aliquam in malesuada id, luctus nec dolor. Proin malesuada sodales suscipit. Nullam adipiscing rutrum augue, sed varius augue venenatis a. Aenean sed eleifend metus. Vestibulum a ultricies nisl. Maecenas sodales mi at est adipiscing ut gravida ante interdum. Donec eu dapibus lorem. Ut at dolor quis orci volutpat gravida vel vitae lorem. In nec consectetur sem.</p>\r\n<p>&nbsp;</p>\r\n<p>Mauris vitae felis id justo ornare convallis. Ut eget porta erat. Vestibulum lobortis tincidunt iaculis. Proin justo eros, auctor nec rhoncus at, ultrices tincidunt libero. Cras id dui ligula, a volutpat libero. Nullam nulla mauris, ornare cursus pretium sit amet, rhoncus egestas nulla. Curabitur facilisis leo sed nunc egestas tincidunt. Sed et nunc sem, ac tincidunt turpis. In ac nibh a orci facilisis venenatis in blandit nibh. Praesent tortor felis, consectetur non molestie sit amet, semper et augue. Donec ipsum ante, ullamcorper id suscipit et, malesuada in erat. Praesent in rhoncus augue. Mauris lacinia est et dui sodales sodales.</p>\r\n<p>&nbsp;</p>\r\n<p>Nullam in lorem neque, at laoreet quam. In ac luctus est. Aenean commodo purus eu nunc adipiscing fringilla. Vestibulum quis sapien eu ante aliquet vulputate ut sed leo. Nulla molestie iaculis sollicitudin. In laoreet velit at lacus congue tincidunt. Nulla ullamcorper lorem at libero imperdiet eu mollis ligula accumsan. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed varius nunc vel metus ultricies hendrerit. Suspendisse at metus ligula.</p>', ''),
(17, 5, 1, 'T.CMS', ''),
(18, 5, 2, 'T.CMS', ''),
(19, 5, 3, 'T.CMS', ''),
(20, 5, 4, 'T.CMS', ''),
(21, 6, 1, '', ''),
(22, 6, 2, '', ''),
(23, 6, 3, '', ''),
(24, 6, 4, '', ''),
(25, 7, 1, '', ''),
(26, 7, 2, '', ''),
(27, 7, 3, '', ''),
(28, 7, 4, '', ''),
(29, 8, 1, 'page_keywords', ''),
(30, 8, 2, 'page_keywords', ''),
(31, 8, 3, 'page_keywords', ''),
(32, 8, 4, 'page_keywords', ''),
(33, 9, 1, 'page_description', ''),
(34, 9, 2, 'page_description', ''),
(35, 9, 3, 'page_description', ''),
(36, 9, 4, 'page_description', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `faq`
--

CREATE TABLE `faq` (
  `id` int(11) UNSIGNED NOT NULL,
  `photo` varchar(1022) NOT NULL,
  `date_add` datetime NOT NULL,
  `count` int(11) NOT NULL,
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `auth` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `comments` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `faq_description`
--

CREATE TABLE `faq_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_keywords` varchar(255) NOT NULL,
  `page_description` varchar(255) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `title_url` varchar(1022) NOT NULL,
  `content` mediumtext NOT NULL,
  `content_short` varchar(1022) NOT NULL,
  `tagi` varchar(1022) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `parent_type` int(11) NOT NULL,
  `filename` varchar(2048) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `files_description`
--

CREATE TABLE `files_description` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(2048) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) UNSIGNED NOT NULL,
  `date_add` datetime NOT NULL,
  `count` int(11) NOT NULL,
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `watermark` tinyint(1) NOT NULL DEFAULT '0',
  `watermark_file` varchar(1022) NOT NULL,
  `watermark_x` int(11) NOT NULL DEFAULT '0',
  `watermark_y` int(11) NOT NULL DEFAULT '0',
  `watermark_position` int(11) NOT NULL DEFAULT '1',
  `auth` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `show_list` tinyint(1) NOT NULL,
  `show_page` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `comments` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gallery_description`
--

CREATE TABLE `gallery_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_keywords` varchar(255) NOT NULL,
  `page_description` varchar(255) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `title_url` varchar(1022) NOT NULL,
  `content` varchar(16382) NOT NULL,
  `content_short` varchar(1022) NOT NULL,
  `tagi` varchar(1022) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gallery_photo`
--

CREATE TABLE `gallery_photo` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(1022) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `gallery_photo_description`
--

CREATE TABLE `gallery_photo_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `content` varchar(2046) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `groups`
--

CREATE TABLE `groups` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `privileges` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `groups`
--

INSERT INTO `groups` (`id`, `name`, `privileges`) VALUES
(1, 'administratorzy', '1|2|3|4|5|6|7|8|9|10|11|12|13|14|16|'),
(2, 'użytkownicy', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `directory` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `main` tinyint(1) NOT NULL,
  `gen_title` tinyint(1) NOT NULL,
  `domain` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `directory`, `order`, `active`, `main`, `gen_title`, `domain`) VALUES
(1, 'Polski', 'pl', 'pl', 1, 1, 1, 1, 'localhost/cms_instalka'),
(2, 'English', 'en', 'en', 2, 1, 0, 1, 'localhost/cms_instalka'),
(3, 'Deutsch', 'de', 'de', 3, 1, 0, 1, 'localhost/cms_instalka'),
(4, 'Russian', 'ru', 'ru', 4, 1, 0, 0, 'localhost/cms_instalka');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `menu`
--

CREATE TABLE `menu` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `blank` tinyint(1) NOT NULL,
  `nofollow` tinyint(1) NOT NULL,
  `has_submenu` tinyint(1) NOT NULL,
  `submenu_type` int(11) NOT NULL,
  `submenu_source` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `menu`
--

INSERT INTO `menu` (`id`, `parent_id`, `order`, `type`, `group`, `blank`, `nofollow`, `has_submenu`, `submenu_type`, `submenu_source`) VALUES
(1, 0, 1, 2, 0, 0, 0, 0, 0, 0),
(2, 0, 2, 2, 0, 0, 0, 0, 0, 0),
(3, 0, 3, 2, 0, 0, 0, 0, 0, 0),
(4, 0, 4, 2, 0, 0, 0, 0, 0, 0),
(5, 0, 5, 2, 0, 0, 0, 0, 0, 0),
(6, 0, 1, 2, 2, 0, 0, 0, 0, 0),
(7, 0, 2, 2, 2, 0, 0, 0, 0, 0),
(8, 0, 3, 2, 2, 0, 0, 0, 0, 0),
(9, 0, 1, 1, 1, 0, 0, 0, 0, 0),
(10, 0, 4, 2, 2, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `menu_description`
--

CREATE TABLE `menu_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_id` int(11) NOT NULL,
  `url` varchar(1022) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `menu_description`
--

INSERT INTO `menu_description` (`id`, `parent_id`, `language_id`, `name`, `target_id`, `url`, `active`) VALUES
(1, 1, 1, 'Strona główna', 12, 'http://', 1),
(2, 1, 2, 'Strona główna', 12, 'http://', 1),
(3, 1, 3, 'Strona główna', 12, 'http://', 1),
(4, 1, 4, 'Strona główna', 12, 'http://', 1),
(5, 2, 1, 'Aktualności', 1, 'http://', 1),
(6, 2, 2, 'Aktualności', 1, 'http://', 1),
(7, 2, 3, 'Aktualności', 1, 'http://', 1),
(8, 2, 4, 'Aktualności', 1, 'http://', 1),
(9, 3, 1, 'Galerie', 5, 'http://', 1),
(10, 3, 2, 'Galerie', 5, 'http://', 1),
(11, 3, 3, 'Galerie', 5, 'http://', 1),
(12, 3, 4, 'Galerie', 5, 'http://', 1),
(13, 4, 1, 'Kontakt', 4, 'http://', 1),
(14, 4, 2, 'Kontakt', 4, 'http://', 1),
(15, 4, 3, 'Kontakt', 4, 'http://', 1),
(16, 4, 4, 'Kontakt', 4, 'http://', 1),
(17, 5, 1, 'Rss', 11, 'http://', 1),
(18, 5, 2, 'Rss', 11, 'http://', 1),
(19, 5, 3, 'Rss', 11, 'http://', 1),
(20, 5, 4, 'Rss', 11, 'http://', 1),
(21, 6, 1, 'Logowanie', 6, 'http://', 1),
(22, 6, 2, 'Logowanie', 6, 'http://', 1),
(23, 6, 3, 'Logowanie', 6, 'http://', 1),
(24, 6, 4, 'Logowanie', 6, 'http://', 1),
(25, 7, 1, 'Rejestracja', 10, 'http://', 1),
(26, 7, 2, 'Rejestracja', 10, 'http://', 1),
(27, 7, 3, 'Rejestracja', 10, 'http://', 1),
(28, 7, 4, 'Rejestracja', 10, 'http://', 1),
(29, 8, 1, 'Mapa strony', 7, 'http://', 1),
(30, 8, 2, 'Mapa strony', 7, 'http://', 1),
(31, 8, 3, 'Mapa strony', 7, 'http://', 1),
(32, 8, 4, 'Mapa strony', 7, 'http://', 1),
(33, 9, 1, 'O firmie', 1, 'http://', 1),
(34, 9, 2, 'O firmie', 1, 'http://', 1),
(35, 9, 3, 'O firmie', 1, 'http://', 1),
(36, 9, 4, 'O firmie', 1, 'http://', 1),
(37, 10, 1, 'Faq', 3, 'http://', 1),
(38, 10, 2, 'Faq', 3, 'http://', 1),
(39, 10, 3, 'Faq', 3, 'http://', 1),
(40, 10, 4, 'Faq', 3, 'http://', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `modules`
--

CREATE TABLE `modules` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_url` varchar(255) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `class_file` varchar(255) NOT NULL,
  `templates_dir` varchar(255) NOT NULL,
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `frontend` tinyint(1) NOT NULL,
  `backend` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `auth` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `modules`
--

INSERT INTO `modules` (`id`, `title`, `title_url`, `table_name`, `class_name`, `class_file`, `templates_dir`, `op_page_title`, `op_page_keywords`, `op_page_description`, `frontend`, `backend`, `active`, `auth`) VALUES
(1, 'Aktualności', 'aktualnosci', 'aktualnosci', 'Aktualnosci', 'aktualnosci.class.php', 'aktualnosci', 1, 1, 1, 1, 1, 1, 0),
(2, 'Dane użytkownika', 'konto/dane', '', '', '', '', 1, 1, 1, 1, 0, 1, 1),
(3, 'Faq', 'faq', '', 'Faq', 'faq.class.php', 'faq', 1, 1, 1, 1, 1, 1, 0),
(4, 'Formularz kontaktowy', 'kontakt', '', '', '', 'kontakt', 1, 1, 1, 1, 0, 1, 0),
(5, 'Galeria', 'galeria', '', 'Gallery', 'gallery.class.php', 'galeria', 1, 1, 1, 1, 1, 1, 0),
(6, 'Logowanie', 'logowanie', '', '', '', '', 1, 1, 1, 1, 0, 1, 0),
(7, 'Mapa strony', 'mapa-strony', '', '', '', '', 1, 1, 1, 1, 0, 1, 0),
(8, 'Newsletter', 'newsletter', '', '', '', '', 1, 1, 1, 1, 0, 1, 1),
(9, 'Przypomnienie hasła', 'przypomnienie-hasla', '', '', '', '', 1, 1, 1, 1, 0, 1, 1),
(10, 'Rejestracja', 'rejestracja', '', '', '', '', 1, 1, 1, 1, 0, 1, 0),
(11, 'Rss', 'rss', '', '', '', '', 1, 1, 1, 1, 0, 1, 0),
(12, 'Strona główna', 'main', '', '', '', '', 1, 1, 1, 1, 0, 1, 0),
(13, 'Szukaj', 'szukaj', '', '', '', '', 1, 1, 1, 1, 0, 1, 1),
(14, 'Zmiana hasła', 'konto/haslo', '', '', '', '', 1, 1, 1, 1, 0, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `modules_description`
--

CREATE TABLE `modules_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_keywords` varchar(255) NOT NULL,
  `page_description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `modules_description`
--

INSERT INTO `modules_description` (`id`, `parent_id`, `language_id`, `page_title`, `page_keywords`, `page_description`) VALUES
(1, 1, 1, '', '', ''),
(2, 1, 2, '', '', ''),
(3, 1, 3, '', '', ''),
(4, 1, 4, '', '', ''),
(5, 2, 1, '', '', ''),
(6, 2, 2, '', '', ''),
(7, 2, 3, '', '', ''),
(8, 2, 4, '', '', ''),
(9, 3, 1, '', '', ''),
(10, 3, 2, '', '', ''),
(11, 3, 3, '', '', ''),
(12, 3, 4, '', '', ''),
(13, 4, 1, '', '', ''),
(14, 4, 2, '', '', ''),
(15, 4, 3, '', '', ''),
(16, 4, 4, '', '', ''),
(17, 5, 1, '', '', ''),
(18, 5, 2, '', '', ''),
(19, 5, 3, '', '', ''),
(20, 5, 4, '', '', ''),
(21, 6, 1, '', '', ''),
(22, 6, 2, '', '', ''),
(23, 6, 3, '', '', ''),
(24, 6, 4, '', '', ''),
(25, 7, 1, '', '', ''),
(26, 7, 2, '', '', ''),
(27, 7, 3, '', '', ''),
(28, 7, 4, '', '', ''),
(29, 8, 1, '', '', ''),
(30, 8, 2, '', '', ''),
(31, 8, 3, '', '', ''),
(32, 8, 4, '', '', ''),
(33, 9, 1, '', '', ''),
(34, 9, 2, '', '', ''),
(35, 9, 3, '', '', ''),
(36, 9, 4, '', '', ''),
(37, 10, 1, '', '', ''),
(38, 10, 2, '', '', ''),
(39, 10, 3, '', '', ''),
(40, 10, 4, '', '', ''),
(41, 11, 1, '', '', ''),
(42, 11, 2, '', '', ''),
(43, 11, 3, '', '', ''),
(44, 11, 4, '', '', ''),
(45, 12, 1, '', '', ''),
(46, 12, 2, '', '', ''),
(47, 12, 3, '', '', ''),
(48, 12, 4, '', '', ''),
(49, 13, 1, '', '', ''),
(50, 13, 2, '', '', ''),
(51, 13, 3, '', '', ''),
(52, 13, 4, '', '', ''),
(53, 14, 1, '', '', ''),
(54, 14, 2, '', '', ''),
(55, 14, 3, '', '', ''),
(56, 14, 4, '', '', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `grupa` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `newsletter`
--

INSERT INTO `newsletter` (`id`, `first_name`, `last_name`, `email`, `active`, `grupa`) VALUES
(1, '', '', 'lukasz.solecki@technetium.pl', 1, 1),
(2, '', '', 'lukasz.solecki@technetium.pl', 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `newsletter_do_wyslania`
--

CREATE TABLE `newsletter_do_wyslania` (
  `id` int(11) UNSIGNED NOT NULL,
  `szablon_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `newsletter_template`
--

CREATE TABLE `newsletter_template` (
  `id` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `description` varchar(1022) NOT NULL,
  `send` int(11) NOT NULL,
  `odebrano` int(11) NOT NULL,
  `clik` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pages`
--

CREATE TABLE `pages` (
  `id` int(11) UNSIGNED NOT NULL,
  `date_add` datetime NOT NULL,
  `count` int(11) NOT NULL,
  `op_page_title` tinyint(1) NOT NULL,
  `op_page_keywords` tinyint(1) NOT NULL,
  `op_page_description` tinyint(1) NOT NULL,
  `gallery_id` int(11) NOT NULL,
  `auth` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `show_title` tinyint(1) NOT NULL,
  `comments` tinyint(1) NOT NULL,
  `typ` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pages_description`
--

CREATE TABLE `pages_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_keywords` varchar(255) NOT NULL,
  `page_description` varchar(255) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `title_url` varchar(1022) NOT NULL,
  `content` mediumtext NOT NULL,
  `content_short` varchar(1022) NOT NULL,
  `tagi` varchar(1022) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `privilege`
--

CREATE TABLE `privilege` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `privilege`
--

INSERT INTO `privilege` (`id`, `name`, `description`) VALUES
(1, 'page_config', 'Zarządzanie konfiguracją serwisu'),
(2, 'menu_administration', 'Zarządzanie menu'),
(3, 'strony_administration', 'Zarządzanie stronami'),
(4, 'aktualnosci_administration', 'Zarządzanie aktualnościami'),
(5, 'galeria_administration', 'Zarządzanie galeriami zdjęć'),
(6, 'biuletyn_administration', 'Edycja i wysyłanie biuletynu'),
(7, 'users_administration', 'Zarządzanie użytkownikami'),
(8, 'stat_administration', 'Podgląd statystyk'),
(9, 'komentarze_administration', 'Moderacja komentarzy'),
(10, 'forum_moderation', 'Moderacja forum'),
(11, 'faq_administration', 'Zarządzanie faq'),
(12, 'oferta_administration', 'Zarządzanie ofertą'),
(13, 'forum_administration', 'Zarządzanie forum'),
(14, 'zmieniarka_administration', 'Zarządzanie zmieniarką'),
(16, 'modules_administration', 'Zarządzanie modułami');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ranking`
--

CREATE TABLE `ranking` (
  `id` int(11) NOT NULL,
  `anchor` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ranking_pozycja`
--

CREATE TABLE `ranking_pozycja` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `pozycja` int(11) NOT NULL,
  `date_add` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `redirects`
--

CREATE TABLE `redirects` (
  `id` int(11) NOT NULL,
  `active` int(11) NOT NULL,
  `src_url` varchar(500) NOT NULL,
  `dst_url` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rejestr`
--

CREATE TABLE `rejestr` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(1022) NOT NULL,
  `date_add` datetime NOT NULL,
  `action` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `rejestr`
--

INSERT INTO `rejestr` (`id`, `title`, `url`, `date_add`, `action`, `type`, `login`) VALUES
(1, 'Robots', '', '2016-07-04 12:07:20', 'zmieniono', 'robots', 'kamil@technetium.pl'),
(2, 'Robots', '', '2016-07-04 12:07:36', 'zmieniono', 'robots', 'kamil@technetium.pl'),
(3, 'Htaccess', '', '2016-07-04 12:13:56', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(4, 'Htaccess', '', '2016-07-04 12:14:04', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(5, 'Htaccess', '', '2016-07-04 12:14:28', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(6, 'Htaccess', '', '2016-07-04 12:16:46', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(7, 'Htaccess', '', '2016-07-04 12:16:54', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(8, 'Htaccess', '', '2016-07-04 12:17:00', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(9, 'Htaccess', '', '2016-07-04 12:17:13', 'zmieniono', 'htaccess', 'kamil@technetium.pl'),
(10, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:31:22', 'dodano', 'gallery', 'tcjuras'),
(11, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:36:22', 'zmieniono', 'galeria', 'tcjuras'),
(12, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:38:49', 'zmieniono', 'galeria', 'tcjuras'),
(13, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:39:09', 'zmieniono', 'galeria', 'tcjuras'),
(14, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:39:10', 'zmieniono', 'galeria', 'tcjuras'),
(15, '', '/galeria/galeria-do-testow.html', '2017-08-02 12:43:03', 'zmieniono', 'galeria', 'tcjuras');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `search`
--

CREATE TABLE `search` (
  `id` int(11) UNSIGNED NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `ilosc` int(11) NOT NULL,
  `count` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `slownik`
--

CREATE TABLE `slownik` (
  `id` int(11) NOT NULL,
  `label` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `slownik`
--

INSERT INTO `slownik` (`id`, `label`) VALUES
(1, 'CZYTAJ_WIECEJ'),
(2, 'LANG'),
(3, 'LANG_DE'),
(4, 'LANG_EN'),
(5, 'LANG_PL'),
(6, 'LANG_RU'),
(7, 'MENU'),
(8, 'MIESIAC_1'),
(9, 'MIESIAC_10'),
(10, 'MIESIAC_11'),
(11, 'MIESIAC_12'),
(12, 'MIESIAC_2'),
(13, 'MIESIAC_3'),
(14, 'MIESIAC_4'),
(15, 'MIESIAC_5'),
(16, 'MIESIAC_6'),
(17, 'MIESIAC_7'),
(18, 'MIESIAC_8'),
(19, 'MIESIAC_9'),
(20, 'WYBIERZ_JEZYK'),
(21, '_ADMIN_CREATE_ERROR'),
(22, '_ADMIN_CREATE_SUCCESS'),
(23, '_ADMIN_DELETE_ERROR'),
(24, '_ADMIN_DELETE_SUCCESS'),
(25, '_ADMIN_MOVE_DOWN'),
(26, '_ADMIN_MOVE_DOWN_MANY'),
(27, '_ADMIN_MOVE_UP'),
(28, '_ADMIN_MOVE_UP_MANY'),
(29, '_ADMIN_PHOTOS_DELETE'),
(30, '_ADMIN_PHOTOS_DETACHED'),
(31, '_ADMIN_PHOTO_ATTACHED'),
(32, '_ADMIN_PHOTO_DELETE'),
(33, '_ADMIN_PHOTO_ERROR'),
(34, '_ADMIN_PHOTO_SUCCESS'),
(35, '_ADMIN_SET_ACTIVE_ERROR'),
(36, '_ADMIN_SET_ACTIVE_SUCCESS'),
(37, '_ADMIN_TITLE_URL_EXIST'),
(38, '_ADMIN_UPDATE_ERROR'),
(39, '_ADMIN_UPDATE_SUCCESS'),
(40, '_COPYRIGHT'),
(41, '_FILES_HEADER'),
(42, '_FILE_CREATE_ERROR'),
(43, '_FILE_CREATE_SUCCESS'),
(44, '_FILE_DELETE_ERROR'),
(45, '_FILE_DELETE_MANY'),
(46, '_FILE_DELETE_SUCCESS'),
(47, '_FILE_MOVE_DOWN'),
(48, '_FILE_MOVE_DOWN_MANY'),
(49, '_FILE_MOVE_UP'),
(50, '_FILE_MOVE_UP_MANY'),
(51, '_FILE_TO_BIG'),
(52, '_FILE_UPDATE_ERROR'),
(53, '_FILE_UPDATE_SUCCESS'),
(54, '_FILE_WRONG_EXTENSION'),
(55, '_FOOTER'),
(56, '_GALLERY_HEADER'),
(57, '_HOMEPAGE'),
(58, '_KONTAKT_WYPELNIJ'),
(59, '_LABEL_BACK'),
(60, '_LABEL_CITY'),
(61, '_LABEL_EMAIL'),
(62, '_LABEL_FIRSTNAME'),
(63, '_LABEL_IMIE_NAZWISKO'),
(64, '_LABEL_LASTNAME'),
(65, '_LABEL_LINK_UP'),
(66, '_LABEL_LOGIN'),
(67, '_LABEL_MORE'),
(68, '_LABEL_NR_BUD'),
(69, '_LABEL_PAGE_NEXT'),
(70, '_LABEL_PAGE_PREV'),
(71, '_LABEL_PASS'),
(72, '_LABEL_PASS_NEW'),
(73, '_LABEL_PASS_NEW_REPEAT'),
(74, '_LABEL_PASS_OLD'),
(75, '_LABEL_PASS_REPEAT'),
(76, '_LABEL_PHONE'),
(77, '_LABEL_POSTCODE'),
(78, '_LABEL_REPEAT_EMAIL'),
(79, '_LABEL_STREET'),
(80, '_LABEL_TOKEN'),
(81, '_LABEL_TRESC'),
(82, '_LABEL_WYSLIJ'),
(83, '_NEWSLETTER'),
(84, '_NEWSLETTER_ACCEPT'),
(85, '_NEWSLETTER_ACCEPT2'),
(86, '_NEWSLETTER_ACTIVE'),
(87, '_NEWSLETTER_CONFIRM'),
(88, '_NEWSLETTER_DEL'),
(89, '_NEWSLETTER_DELETE'),
(90, '_NEWSLETTER_DEL_EMAIL'),
(91, '_NEWSLETTER_DEL_EMAIL2'),
(92, '_NEWSLETTER_INFO'),
(93, '_NEWSLETTER_INFO2'),
(94, '_NEWSLETTER_NO_ACTIVE'),
(95, '_NEWSLETTER_NO_EMAIL'),
(96, '_NEWSLETTER_NO_SEND'),
(97, '_NEWSLETTER_REGISTER'),
(98, '_NEWSLETTER_RESIGNATION'),
(99, '_NEWSLETTER_RULES'),
(100, '_NEWSLETTER_SAVE'),
(101, '_NEWSLETTER_SUBMIT'),
(102, '_NEWSLETTER_SUBMIT2'),
(103, '_NEWSLETTER_UNSUBSCRIBE'),
(104, '_PAGE_ADDED'),
(105, '_PAGE_AUTH'),
(106, '_PAGE_CHANGE_PASS'),
(107, '_PAGE_CHECK_TOKEN'),
(108, '_PAGE_COMMENT_ADD'),
(109, '_PAGE_COMMENT_CHANGE'),
(110, '_PAGE_COMMENT_DELETE'),
(111, '_PAGE_EMAIL_HTML'),
(112, '_PAGE_EMPTY_FORM'),
(113, '_PAGE_ERROR'),
(114, '_PAGE_EXIST'),
(115, '_PAGE_FAQ'),
(116, '_PAGE_GALLERY'),
(117, '_PAGE_GALLERY_NO_PHOTO'),
(118, '_PAGE_GO_MAIN'),
(119, '_PAGE_HERE'),
(120, '_PAGE_HOMEPAGE'),
(121, '_PAGE_ILE_AKT'),
(122, '_PAGE_ILE_FAQ'),
(123, '_PAGE_ILE_GAL'),
(124, '_PAGE_ILE_POD'),
(125, '_PAGE_INFO'),
(126, '_PAGE_I_REGISTER'),
(127, '_PAGE_JUST_LOGIN'),
(128, '_PAGE_KONTAKT'),
(129, '_PAGE_LINK'),
(130, '_PAGE_LOGGED_IN'),
(131, '_PAGE_LOGGED_INFO'),
(132, '_PAGE_LOGGED_OUT'),
(133, '_PAGE_LOGGED_OUT_INFO'),
(134, '_PAGE_LOGIN_USER'),
(135, '_PAGE_LOGOUT'),
(136, '_PAGE_LOG_IN'),
(137, '_PAGE_LOG_OUT'),
(138, '_PAGE_NEWS'),
(139, '_PAGE_NEWSLETTER'),
(140, '_PAGE_NEWSLETTER_DEL'),
(141, '_PAGE_NEW_ACCOUNT'),
(142, '_PAGE_NEW_REMINDER'),
(143, '_PAGE_NEW_USER'),
(144, '_PAGE_NOT'),
(145, '_PAGE_NOT_EXIST'),
(146, '_PAGE_NO_FAQ'),
(147, '_PAGE_NO_GALLERY'),
(148, '_PAGE_NO_NEWS'),
(149, '_PAGE_NO_SEND'),
(150, '_PAGE_OPERATION_CORECT'),
(151, '_PAGE_PAGES'),
(152, '_PAGE_PASS'),
(153, '_PAGE_PODAJ_EMAIL'),
(154, '_PAGE_POSTCODE_TRUE'),
(155, '_PAGE_PRINT'),
(156, '_PAGE_REDIRECT'),
(157, '_PAGE_REG'),
(158, '_PAGE_REGEDIT'),
(159, '_PAGE_REGISTER'),
(160, '_PAGE_REGISTER_INFO'),
(161, '_PAGE_REMINDER'),
(162, '_PAGE_REMINDER_INFO'),
(163, '_PAGE_SAVE_CHANGE'),
(164, '_PAGE_SEND'),
(165, '_PAGE_SITEMAP'),
(166, '_PAGE_SZUKAJ'),
(167, '_PAGE_SZUKAJ_BRAK'),
(168, '_PAGE_SZUKAJ_KEYWORDS'),
(169, '_PAGE_SZUKAJ_WYNIKI'),
(170, '_PAGE_UNSUBSCRIBE'),
(171, '_PAGE_UPDATING'),
(172, '_PAGE_USER'),
(173, '_PAGE_WELCOME'),
(174, '_PAGE_WPISZ'),
(175, '_PAGE_WYSZUKAJ'),
(176, '_PAGE_WYSZ_ZAWANS'),
(177, '_PAGE_ZOBACZ_ROWNIEZ'),
(178, '_USER_ACTIVE'),
(179, '_USER_ACTIVE_PAGE'),
(180, '_USER_BAD_ACCOUNT'),
(181, '_USER_BAD_BOTH_PASS'),
(182, '_USER_BAD_EMAIL'),
(183, '_USER_BAD_GEN_PASS'),
(184, '_USER_BAD_ID_ACTIVE'),
(185, '_USER_BAD_ID_DEL'),
(186, '_USER_BAD_LOGIN'),
(187, '_USER_BAD_OLD_PASS'),
(188, '_USER_BAD_PASS'),
(189, '_USER_BOTH_EMAIL'),
(190, '_USER_BOTH_PASS'),
(191, '_USER_CHANGE_DATA'),
(192, '_USER_CHANGE_NEW_PASS'),
(193, '_USER_CHANGE_PASS'),
(194, '_USER_CHANGE_SAVE'),
(195, '_USER_CHECK_TRUE'),
(196, '_USER_CONFIRMATION'),
(197, '_USER_DELETE'),
(198, '_USER_EMAIL_REGISTER'),
(199, '_USER_EMPTY'),
(200, '_USER_EMPTY_CITY'),
(201, '_USER_EMPTY_FIRM_NAME'),
(202, '_USER_EMPTY_LASTNAME'),
(203, '_USER_EMPTY_NAME'),
(204, '_USER_EMPTY_NIP'),
(205, '_USER_EMPTY_NR_BUD'),
(206, '_USER_EMPTY_POSTCODE'),
(207, '_USER_EMPTY_STREET'),
(208, '_USER_FIRM'),
(209, '_USER_FIRM_NAME'),
(210, '_USER_GEN_NEW_PASS'),
(211, '_USER_GROUP_ADD'),
(212, '_USER_GROUP_ADD_PRIV'),
(213, '_USER_GROUP_DEL'),
(214, '_USER_GROUP_DEL_USERS'),
(215, '_USER_GROUP_EXIST'),
(216, '_USER_GROUP_NO_DEL'),
(217, '_USER_LOGIN'),
(218, '_USER_MAKE_ACCOUNT'),
(219, '_USER_MAKE_USER'),
(220, '_USER_MIN_SING'),
(221, '_USER_NAME_ACCOUNT'),
(222, '_USER_NEW_PASS'),
(223, '_USER_NIP'),
(224, '_USER_NOT_ACTIVE'),
(225, '_USER_NOT_PRIVILAGE'),
(226, '_USER_NOT_REGISTER'),
(227, '_USER_NOT_SAVED'),
(228, '_USER_NO_ACTIVE'),
(229, '_USER_NO_LOGIN'),
(230, '_USER_NO_SEE_PAGE'),
(231, '_USER_NO_USER'),
(232, '_USER_PERSON'),
(233, '_USER_PRIV_ADD'),
(234, '_USER_PRIV_DEL'),
(235, '_USER_PRIV_DEL_GROUP'),
(236, '_USER_PRIV_EXIST'),
(237, '_USER_PRIV_NO_ADD'),
(238, '_USER_PRIV_NO_DEL'),
(239, '_USER_REGISTER'),
(240, '_USER_REGISTER_INFO'),
(241, '_USER_REMINDER_PASS'),
(242, '_USER_RESIGNATION'),
(243, '_USER_SHORT_PASS'),
(244, '_USER_STAR'),
(245, '_USER_TYPE_ACCOUNT'),
(246, '_USER_VAT'),
(247, '_USER_NOT_REGISTER_ALTERNATE_LOGIN');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `slownik_admin`
--

CREATE TABLE `slownik_admin` (
  `id` int(11) NOT NULL,
  `label` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `slownik_admin`
--

INSERT INTO `slownik_admin` (`id`, `label`) VALUES
(1, 'WYLOGUJ'),
(2, 'WITAJ'),
(3, 'OPCJE_GLOWNE'),
(5, 'PODGLAD_STRONY_ON_LINE'),
(4, 'STATYSTYKI_STRONY'),
(6, 'MENU'),
(7, 'ZARZADZANIE_MENU'),
(8, 'ZARZADZANIE_STRONAMI'),
(9, 'ZAWARTOSC_STRONY_GLOWNEJ'),
(10, 'DODAJ_NOWA_PODSTRONE'),
(11, 'ZARZADZANIE_PODSTRONAMI'),
(12, 'AKTUALNOSCI'),
(13, 'DODAJ_NOWA_AKTUALNOSC'),
(14, 'ZARZADZANIE_AKTUALNOSCIAMI'),
(15, 'GALERIA'),
(16, 'DODAJ_NOWA_GALERIE'),
(17, 'ZARZADZANIE_GALERIAMI_ZDJEC'),
(18, 'ZMIENIARKA'),
(19, 'DODAJ_NOWA_ZMIENIARKE'),
(20, 'ZARZADZANIE_ZMIENIARKAMI'),
(21, 'BIULETYN'),
(22, 'INFORMACJA_O_BIULETYNIE'),
(23, 'EDYCJA_REGULAMINU'),
(24, 'UZYTKOWNICY_BIULETYNU'),
(25, 'EDYCJA_SZABLONOW_MAILINGU'),
(26, 'WYSYLANIE_BIULETYNU'),
(27, 'UZYTKOWNICY'),
(28, 'DODAJ_UZYTKOWNIKA'),
(29, 'ZARZADZANIE_UZYTKOWNIKAMI'),
(30, 'ZARZADZANIE_GRUPAMI'),
(31, 'ZARZADZANIE_UPRAWNIENIAMI'),
(32, 'STATYSTYKI'),
(33, 'LOGOWANIA_DO_PANELU_ADMIN'),
(34, 'NIEUDANE_LOGOWANIA_DO_PANELU'),
(35, 'ODWIEDZALNOSC_PODSTRON'),
(36, 'ODWIEDZALNOSC_AKTUALNOSCI'),
(37, 'ODWIEDZALNOSC_FAQ'),
(38, 'ODWIEDZALNOSC_GALERII'),
(39, 'WYNIKI_WYSZUKIWARKI'),
(40, 'STATYSTYKI_WYSYLANIA_BIULETYNU'),
(41, 'REJESTR_ZMIAN_W_SERWISIE'),
(42, 'KOMENTARZE'),
(43, 'KONFIGURACJA_KOMENTARZY'),
(44, 'MODERACJA_KOMENTARZY'),
(45, 'FAQ'),
(46, 'DODAJ_NOWE_FAQ'),
(47, 'ZARZADZANIE_FAQ'),
(48, 'KONFIGURACJA_PODSTAWOWA'),
(49, 'MODULY'),
(50, 'KONTAKT'),
(51, 'SLOWNIK'),
(52, 'KONFIGURACJA_STRONY'),
(53, 'RANKING_W_GOOGLE'),
(54, 'KONFIGURACJA_DOMYSLNA_SEO'),
(55, 'WYBOR_SZABLONU'),
(56, 'SZABLONY_EMAILI_DO_UZYTKOWNIKOW'),
(57, 'ZMIANA_HASLA'),
(58, 'OPERACJE_NA_BAZIE_DANYCH'),
(60, '_ERROR_IFRAME'),
(61, '_MENU_WYBIERZ_MENU'),
(62, '_MENU_WYBIERZ_INFO'),
(63, 'DODAJ_NOWY_ELEMENT'),
(64, 'AKTUALNA_SCIEZKA'),
(65, 'NAZWA'),
(66, 'WSKAZUJE_NA'),
(67, 'PODMENU'),
(68, 'OPCJE'),
(69, 'NOWY_EMEMENT'),
(70, 'POKAZ_DRZEWO_MENU'),
(71, 'POKAZ_OPCJE'),
(72, 'COFNIJ_SIE_O_POZIOM_W_GORE'),
(73, 'ADRES_ZEWNETRZNY'),
(74, 'PODSTRONA'),
(75, 'MODUL'),
(76, 'ELEMENT_BEZ_LINKU'),
(77, 'KASUJ'),
(78, 'EDYTUJ'),
(79, 'PRZENIES_W_DOL'),
(80, 'PRZENIES_W_GORE'),
(81, 'CONFIRM_DELETE_MULTI'),
(83, '_MENU_EMPTY'),
(84, 'DODAJ_ELEMENT_MENU'),
(85, 'ELEMENT_AKTYWNY_W_WERSJACH_JEZYKOWYCH'),
(86, 'NAZWA_ELEMENTU'),
(87, 'TYP_PODMENU'),
(88, 'ELEMENT_POSIADA_PODMENU'),
(89, 'ZARZADZALNE'),
(90, 'GENEROWANE_AUTOMATYCZNIE'),
(91, '_MENU_WYBIERZ_MODUL'),
(92, 'WYBIERZ_MODUL'),
(93, 'ELEMENT_WSKAZUJE_NA'),
(94, 'PODSTRONE_NA_SERWERZE'),
(95, 'MODUL_NA_SERWERZE'),
(96, '_MENU_PODAJ_ZEWNETRZNY_URL'),
(97, 'WYSWIETLAJ_STRONE_W_NOWYM_OKNIE'),
(98, 'LINK_Z_ATRYBUTEM'),
(99, '_MENU_WYBIERZ_STRONE'),
(100, 'WYBIERZ_STRONE'),
(101, '_MENU_WYBIERZ_MODUL_DOCELOWY'),
(102, 'EDYCJA_ELEMENTU_MENU'),
(103, 'NOWA_PODSTRONA'),
(104, 'WERSJE_JEZYKOWE'),
(105, 'TRESC'),
(106, 'AKTYWNA_W_TEJ_WERSJI_JEZYKOWEJ'),
(107, 'TYTUL_STRONY'),
(108, 'OPCJE_DODATKOWE'),
(109, 'POKAZUJ_TYTUL'),
(110, 'POKAZUJ_PODSTRONE_W_SERWISIE'),
(111, 'UZYTKOWNICY_MOGA_KOMENTOWAC'),
(112, 'DOSTEPNA_TYLKO_DLA_ZALOGOWANYCH'),
(113, 'BRAK'),
(114, '_GALERIA_DOLACZONA_DO_ARTYKULU'),
(115, '_SEO_WYBOR_TITLE'),
(116, '_SEO_KONF_GLOWNA'),
(117, '_SEO_KONF_AUTOMATYCZNA'),
(118, '_SEO_KONF_AUTOMATYCZNA_GLOWNA'),
(119, '_SEO_KONF_RECZNA'),
(120, '_SEO_KONF_RECZNA_GLOWNA'),
(121, '_SEO_WYBOR_KEYWORDS'),
(122, '_SEO_KONF_RECZNA_FRAZY'),
(123, '_SEO_KONF_RECZNA_FRAZY_GLOWNA'),
(124, '_SEO_WYBOR_DESCRIPTION'),
(125, '_SEO_KONF_AUTOMATYCZNA_TRESC'),
(126, '_SEO_KONF_AUTOMATYCZNA_TRESC_GLOWNA'),
(127, 'KROTKI_OPIS'),
(128, 'AUTOMATYCZNY_Z_TRESCI_250'),
(129, '_SEO_OPIS_RECZNY'),
(130, 'TAGI'),
(131, '_SEO_TAGI_ODZIELONE_ZNAKIEM'),
(132, '_SEO_WSTAWIANE_POD_ARTYKULEM_JAKO_LINKI'),
(133, 'EDYCJA_PODSTRONY'),
(134, 'TYTUL_I_KROTKA_TRESC'),
(135, 'DATA_DODANIA'),
(136, 'WIDOCZNA'),
(137, '_BRAK_ARTYKULOW_W_BAZIE'),
(138, 'NOWA_AKTUALNOSC'),
(139, 'EDYCJA_AKTUALNOSCI'),
(140, 'SEO'),
(141, 'PLIKI'),
(142, 'TYTUL_AKTUALNOSCI'),
(143, 'ZDJECIE'),
(144, 'NIE_WIEKSZE_NIZ'),
(145, 'URL_STRONY'),
(146, '_SEO_URL'),
(147, 'PLIKI_DO_POBRANIA_WYSWIETLANE_NA_STRONIE'),
(148, 'LP'),
(149, 'PLIK'),
(150, 'BRAK_PLIKOW_W_BAZIE'),
(151, 'FORMULARZ_DODAWANIA_PLIKU'),
(152, 'NAZWA_WYSWIETLANA'),
(153, 'UCZYN_NIEWIDOCZNA'),
(154, 'UCZYN_WIDOCZNA'),
(155, 'NIEWIDOCZNA'),
(156, 'ZABEZPIECZONA_HASLEM'),
(157, 'DOLACZONA_GALERIA'),
(158, 'BRAK_GALERII'),
(159, 'PODGLAD'),
(160, 'NOWY_ZASTAPI_STARY'),
(161, '_GALERIA_INFO'),
(162, 'TYTUL_GALERII'),
(163, 'SZEROKOSC_MINIATURKI'),
(164, 'WYSOKOSC_MINIATURKI'),
(165, 'ZNAK_WODNY'),
(166, 'PLIK_ZNAKU_WODNEGO'),
(167, 'POZYCJA_X_ZNAKU_WODNEGO'),
(168, 'POZYCJA_Y_ZNAKU_WODNEGO'),
(169, 'PUNKT_STARTOWY_ZNAKU_WODNEGO'),
(170, 'LEWA_GORA'),
(171, 'PRAWA_GORA'),
(172, 'LEWA_DOL'),
(173, 'PRAWA_DOL'),
(174, 'POKAZUJ_GALERIE_W_SERWISIE'),
(175, 'GALERIA_WYSWIETLANA_NA_LISCIE_GALERII'),
(176, 'GALERIA_MOZLIWA_DO_DOLACZENIA_W_PODSTRONACH'),
(177, 'NOWA_GALERIA_ZDJEC'),
(178, 'EDYCJA_GALERII_ZDJEC'),
(179, 'ZARZADZANIE_GALERIA_ZDJEC'),
(180, '_MINIATURA_ABY_WYKADROWAC'),
(181, 'NOWE_ZASTAPI_JUZ_ISTNIEJACE'),
(182, 'KADROWANIE'),
(183, 'ZDJECIE_NORMALNE'),
(184, 'USUN_Z_SERWERA'),
(185, 'MINIATURA_NA_STRONE_GLOWNA'),
(186, 'UTWORZ_MINIATURE'),
(187, 'MINIATURA_NA_LISTE'),
(188, 'MINIATURA_DO_SZCZEGOLOW'),
(189, 'DODAJ_AKTUALNOSC'),
(190, 'PORZUC_ZMIANY'),
(191, 'ZAPISZ'),
(192, 'ZAPISZ_I_KONTYNUUJ_EDYCJE'),
(193, 'TRWA_PRZETWARZANIE_DANYCH'),
(194, 'INFORMACJA'),
(195, 'INFORMACJA_O_BLEDZIE'),
(196, 'CONFIRM_DELETE'),
(197, 'CONFIRM_SUBMIT'),
(198, 'DODAJ_ZDJECIE'),
(199, 'DODAJ_PLIK'),
(200, 'ZAPISZ_ZMIANY'),
(201, 'DODAJ_PODSTRONE'),
(202, 'NOWA_GALERIA'),
(203, 'ZDJECIA'),
(204, 'DODAWALNA'),
(205, 'TYTUL'),
(206, 'DODAJ_ZDJECIA'),
(207, 'DOLACZANA_W_PODSTRONACH'),
(208, 'NIEWIDOCZNA_W_PODSTRONACH'),
(209, 'NIE_UTWORZONO_JESZCZE_GALERII'),
(210, '_ZDJECIE_ABY_WYKADROWAC'),
(211, 'POWROT_DO_SPISU_GALERII'),
(212, 'EDYTUJ_GALERIE'),
(213, 'USUN_GALERIE'),
(214, 'ZDJECIA_W_GALERII'),
(215, 'BRAK_ZDJECIA'),
(216, 'EDYTUJ_OPIS'),
(217, 'AKTUALNIE_BRAK_ZDJEC_W_GALERII'),
(218, '_GALERIA_WYBIERZ_ZDJECIA'),
(219, 'ZDJECIA_NA_SERWERZE'),
(220, 'DODAJ_ZAZNACZONE_DO_GALERII'),
(221, 'USUN_ZAZNACZONE_Z_SERWERA'),
(222, 'USUN_ZAZNACZONE_Z_GALERII'),
(223, 'WYBIERZ_ZDJECIA_DO_GALERII'),
(224, 'EDYCJA_OPISU_DLA_ZDJECIA_NR'),
(225, 'W_GALERII'),
(226, 'OPIS'),
(227, 'NIE_UTWORZONO_JESZCZE_ZMIENIARKI'),
(228, 'USUN'),
(229, 'ETYKIETA_ZMIENIARKI'),
(230, 'TEKST_PO_KTORYM_ZMIENIARKA_BEDZIE_IDENTYFIKOWANA'),
(231, 'ZMIENIARKA_WIDOCZNA_W_SERWISIE'),
(232, 'POWROT_DO_LISTY_ZMIENIAREK'),
(233, 'KONFIGURUJ_ZMIENIARKE'),
(234, 'USUN_ZMIENIARKE'),
(235, 'AKTUALNIE_BRAK_ZDJEC_W_ZMIENIARCE'),
(236, '_ZMIENIARKA_WYBIERZ_ZDJECIA_DO_GALERII'),
(237, 'DODAJ_ZAZNACZONE_DO_ZMIENIARKI'),
(238, 'USUN_ZAZNACZONE_ZE_ZMIENIARKI'),
(239, 'W_ZMIENIARCE'),
(240, 'ZAWARTOSC_ATRYBUTU_ALT'),
(241, 'ZARZADZANIE_ZMIENIARKA'),
(242, 'DODAJ_ZMIENIARKE'),
(243, '_BIULETYN_INFO'),
(244, 'REGULAMIN_BIULETYNU'),
(245, 'WYSYLANIE_NEWSLETTERA'),
(246, 'ZARZADZANIE_UZYTKOWNIKAMI_BIULETYNU'),
(247, 'EDYCJA_UZYTKOWNIKA_BIULETYNU'),
(248, '_BIULETYN_INFO_ZMIANY_W_REGULAMINIE'),
(249, '_BIULETYN_INFO_ZMIANY_W_INFORMACJI'),
(250, '_BIULETYN_ERROR_REGULAMIN'),
(251, '_BIULETYN_ERROR_INFORMACJA'),
(252, 'BIULETYN_WYSLANO_DO'),
(253, 'ODBIORCOW'),
(254, '_BIULETYN_ERROR_SEND'),
(255, '_BIULETYN_INFO_USER'),
(256, 'FILTR'),
(257, 'IMIE'),
(258, 'NAZWISKO'),
(259, 'E_MAIL'),
(260, 'POKAZ_KONTA'),
(261, 'WSZYSTKIE'),
(262, 'ID_UZYTKOWNIKA'),
(263, 'NAZWISKA'),
(264, 'SORTOWANIE'),
(265, 'ILOSC'),
(266, 'ILOSC_UZYTKOWNIKOW_NA_STRONE'),
(267, 'SORTUJ_WEDLUG'),
(268, 'WYBIERZ'),
(269, 'ADRESU_E_MAIL'),
(270, 'NIEAKTYWNE'),
(271, 'AKTYWNE'),
(272, 'ROSNACO'),
(273, 'MALEJACO'),
(274, 'ZASTOSUJ'),
(275, 'POKAZ_WSZYSTKIE_KONTA'),
(276, 'DODAJ_ADRES_EMAIL'),
(277, 'BRAK_UZYTKOWNIKOW_O_PODANYCH_KRYTERIACH_W_BAZIE'),
(278, 'POTWIERDZENIE_WYSYLANIA_BIULETYNU'),
(279, 'POWROT'),
(280, 'BIULETYN_WYSYLANY_NA_ADRES'),
(281, 'BIULETYN_WYSYLANY_NA'),
(282, 'AKTYWNYCH_ADRESOW_E_MAIL'),
(283, 'CZY_NA_PEWNO_CHCESZ_WYSLAC_BIULETYN'),
(284, 'TYTUL_BIULETYNU'),
(285, 'WYSLIJ_BIULETYN'),
(286, 'WYBIERZ_SZABLON_KTORY_CHCESZ_EDYTOWAC'),
(287, 'DODAJ_NOWY_SZABLON'),
(288, 'BRAK_SZABLONOW_W_BAZIE'),
(289, 'DODAJ_SZABLON'),
(290, 'POWROT_DO_SPISU_UZYTKOWNIKOW'),
(291, 'STATUS_KONTA'),
(292, 'NAZWA_SZABLONU'),
(293, 'DODAJ'),
(294, '_BIULETYN_INFO_MAILING'),
(295, 'AKTUALNIE_W_BAZIE_JEST'),
(296, 'ADRESOW_Z_CZEGO'),
(297, 'AKTYWNYCH'),
(298, 'WYBIERZ_SZABLON'),
(299, 'WCZYTAJ'),
(300, 'WYSLIJ_BIULETYN_DO'),
(301, 'WSZYSTKICH_ZAPISANYCH'),
(302, 'NA_PODANY_ADRES'),
(303, 'PODAJ_ADRES_NA_JAKI_WYSLAC_BIULETYN'),
(304, 'WYBIERZ_GRUPE_DO_KTOREJ_CHCESZ_WYSLAC_BIULETYN'),
(305, 'LOGIN'),
(306, 'BEZ_DOSTEPU_DO_PANELU_ADMINA'),
(307, 'Z_DOSTEPEM_DO_PANELU_ADMINA'),
(308, 'LOGINU'),
(309, 'NAZWY_FIRMY'),
(310, 'DODAJ_NOWEGO_UZYTKOWNIKA'),
(311, 'NAZWA_GRUPY'),
(312, 'ZARZADZAJ'),
(313, 'ZMIEN_NAZWE'),
(314, 'BRAK_ZDEFINIOWANYCH_GRUP_UZYTKOWNIKOW'),
(315, 'NOWA_GRUPA'),
(316, 'DODAJ_GRUPE'),
(317, 'DODAJ_NOWA_GRUPE'),
(318, 'UPRAWNIENIA_DLA_NOWEJ_GRUPY'),
(319, 'BRAK_UPRAWNIEN_W_BAZIE'),
(320, 'ZAZNACZENIE'),
(321, 'ODZNACZENIE_WSZYSTKICH'),
(322, 'POWROT_DO_WSZYSTKICH_GRUP'),
(323, 'UPRAWNIENIA_DLA_GRUPY'),
(324, 'DODAJ_NOWE_UPRAWNIENIE'),
(325, 'OK'),
(326, 'NAZWA_KONTA_UZYTKOWNIKA'),
(327, 'ZMIEN_HASLO'),
(328, 'LOGOWANIE_DO_PANELU_ADMINISTRATORA'),
(329, 'NIE'),
(330, 'TAK'),
(331, 'GRUPA'),
(332, 'NIE_WYBRANO_GRUPY'),
(333, 'TYP_KONTA'),
(334, 'PROSZE_ZAZNACZYC_WLASCIWE'),
(335, 'OSOBA_FIZYCZNA'),
(336, 'FIRMA'),
(337, 'NAZWA_FIRMY'),
(338, 'NUMER_NIP'),
(339, 'ULICA'),
(340, 'NR_BUDYNKU_LOKALU'),
(341, 'KOD_POCZTOWY'),
(342, 'POPRAWNY_KOD_POCZTOWY'),
(343, 'MIEJSCOWOSC'),
(344, 'TELEFON'),
(345, 'HASLO'),
(346, 'POWTORZ_HASLO'),
(347, 'POWTORZ_E_MAIL'),
(348, 'DODAWANIE_NOWEGO_UZYTKOWNIKA'),
(349, 'EDYCJA_UZYTKOWNIKA'),
(350, 'ZARZADZANIE_UPRAWNIENIAMI_UZTYTKOWNIKOW'),
(351, 'ZMIANY_ZOSTALY_ZAPISANE'),
(352, 'ZARZADZANIE_SZABLONAMI_BIULETYNU'),
(353, 'EDYCJA_SZABLONU_BIULETYNU'),
(354, 'ZARZADZANIE_GRUPAMI_UZYTKOWNIKOW'),
(355, 'LOGOWANIA_DO_PANELU'),
(356, 'DATA'),
(357, 'HOST'),
(358, 'BRAK_WPISOW_W_BAZIE'),
(359, 'NIEUDANE_LOGOWANIA'),
(360, 'POWOD_ODRZUCENIA'),
(361, 'ODWIEDZALNOSC'),
(362, 'DATA_UTWORZENIA'),
(363, 'ODWIEDZINY'),
(364, 'FRAZY_WPISANE_W_WYSZUKIWARCE'),
(365, 'WPISYWANA_FRAZA'),
(366, 'ZNALEZIONYCH_WYNIKOW'),
(367, 'ILOSC_WYSWIETLEN'),
(368, 'REJESTR_ZMIAN'),
(369, 'STATYSTYKA_WYSYLANIA_BIULETYNU'),
(370, 'WYSLANE'),
(371, 'ODEBRANE'),
(372, 'KLIKNIETE_LINKI'),
(373, 'AKCJA'),
(374, 'DOTYCZY'),
(375, 'WYKONANA_PRZEZ'),
(376, 'ZMIANY_W_KONFIGURACJI_ZOSTALY_ZAPISANE'),
(377, 'WYSTAPIL_BLAD_PODCZAS_ZSPISYWANIA_ZMIAN_W_KONFIGURACJI'),
(378, 'KOMENTARZE_WIDOCZNE_DLA_NIEZALOGOWANYCH'),
(379, 'CZY_NIEZALOGOWANI_UZYTKOWNICY_MOGA_PISAC_KOMENTARZE'),
(380, '_KOMENTARZE_MODERACJA_INFO'),
(381, 'AUTOR'),
(382, 'ID'),
(383, 'EDYCJA_KOMENTARZA'),
(384, 'POKAZ_KOMENTARZE'),
(385, 'NOWE_PYTANIE'),
(386, 'EDYCJA_PYTANIA'),
(387, 'ZARZADZANIE_PYTANIAMI'),
(388, 'DODAJ_NOWE_PYTANIE'),
(390, 'PODGLAD_ONLINE'),
(391, 'BRAK_PYTAN_W_BAZIE'),
(392, 'TYTUL_PYTANIA'),
(393, 'DODAJ_PYTANIE'),
(395, 'MINIATURA'),
(396, 'NOWY_MODUL'),
(397, 'EDYCJA_MODULU'),
(398, 'ZARZADZANIE_MODULAMI'),
(399, 'DODAJ_NOWY_MODUL'),
(400, 'ETYKIETA'),
(401, 'FRONTEND'),
(402, 'BACKEND'),
(403, 'TYLKO_DLA_ZALOGOWANYCH'),
(404, 'AKTYWNY'),
(405, '_BRAK_MODULOW_W_BAZIE'),
(406, '_MODULY_INFO'),
(408, '_NAZWA_WYSWIETLANA_JAKO_URL'),
(409, 'MODUL_FRONTENDU'),
(410, 'MODUL_PANELU_ADMINISTRACYJNEGO'),
(411, '_MODUL_NAZWA_TABELI'),
(412, '_MODUL_GLOWNA_KLASA_PHP'),
(413, 'NAZWA_PLIKU_ZAWIERAJACEGO_DEFINICJE_KLASY'),
(414, 'NAZWA_KATALOGU_ZAWIERAJACEGO_SZABLONY_HTML'),
(415, 'DODAJ_MODUL'),
(416, 'ZAWARTOSC_STRONY_KONTAKTOWEJ'),
(417, 'ZARZADZANIE_SLOWNIKIEM'),
(418, 'IMPORT_SLOWNIKA'),
(419, '_SLOWNIK_INFO'),
(420, 'DODAJ_NOWY_WYRAZ'),
(421, 'EXPORTUJ_DO_EXCELA'),
(422, 'IMPORTUJ_Z_EXCELA'),
(423, 'SZUKAJ'),
(424, 'POWROT_DO_SLOWNIKA'),
(425, 'TEKST'),
(426, '_SLOWNIK_IMPORT_INFO'),
(427, 'IMPORTUJ'),
(428, 'PIERWSZA'),
(429, 'POPRZEDNIA'),
(430, 'NASTEPNA'),
(431, 'OSTATNIA'),
(433, 'OPCJA_ZOSTALA_SKASOWANA'),
(434, 'WYSTAPIL_BLAD_PODCZAS_KASOWANIA_OPCJI'),
(435, 'NOWA_OPCJA_ZOSTALA_DODANA'),
(436, 'WYSTAPIL_BLAD_PODCZAS_DODAWANIA_NOWEJ_OPCJI'),
(437, 'RANKING_W_GOOGLE'),
(438, '_RANKING_INFO'),
(439, 'AKTUALNIE_AUTOMAT_SPRAWDZAJACY_JEST'),
(440, 'WLACZONY'),
(441, 'WYLACZONY'),
(442, 'DLA_DOMENY'),
(443, 'FRAZA'),
(444, 'POZYCJA'),
(445, 'POKAZ_WYNIKI'),
(446, 'DODAWANIE_FRAZY'),
(447, 'ANCHOR'),
(448, 'WYKORZYSTALES_JUZ_LICZBE_DOPUSZCZALNYCH_FRAZ'),
(449, 'DATA_SPRAWDZANIA'),
(450, 'POWROT_DO_SPISU_FRAZ'),
(452, '_SEO_KONFIGURACJA_INFO'),
(453, '_SEO_KONFIGURACJA_TITLE'),
(454, 'KONFIGURACJA_CMS'),
(455, 'Z_TYTULU'),
(456, 'Z_TYTULU_CMS'),
(457, 'RECZNA'),
(458, 'RECZNA_CMS'),
(459, 'ZAWARTOSC_TAGU_TITLE'),
(460, 'PREFIKS'),
(461, 'SUFFIKS'),
(462, '_ZAWARTOSC_TAGU_KEYWORDS'),
(463, '_ZAWARTOSC_TAGU_DESCRIPTION'),
(464, 'Z_TRESCI'),
(465, 'Z_TRESCI_CMS'),
(466, 'WYSTAPIL_BLAD_PODCZAS_ZAPISYWANIA_ZMIAN'),
(467, 'WYBOR_DOMYSLNEGO_SZABLONU'),
(468, '_SZABLONY_INFO'),
(469, '_SZABLONY_INFO2'),
(470, '_SZABLONY_INFO3'),
(471, 'WYBIERZ_DOMYSLNY_SZABLON'),
(472, 'KATALOG_KOMPILACJI'),
(473, 'KATALOG_CACHE'),
(474, '_SZABLONY_BLAD_UPRAWNIEN'),
(476, '_HASLO_INFO'),
(477, 'STARE_HASLO'),
(478, 'NOWE_HASLO'),
(479, 'POWTORZ_NOWE_HASLO'),
(481, 'EXPORT_BAZY_DANYCH_MYSQL'),
(482, '_BAZA_MYSQL_INFO'),
(483, '_BAZA_MYSQL_INFO2'),
(484, 'EXPORTUJ'),
(485, 'WROC_NA_LISTE'),
(486, 'CONFIRM_DELETE_2'),
(487, 'ZARZADZAJ_UPRAWNIENIAMI'),
(488, 'ELEMENT'),
(489, '_ADMIN_SLOWNIK_PANEL'),
(490, 'ADMIN_BRAK_OPCJI'),
(491, 'ADMIN_DODAJ_NOWA_OPCJE'),
(492, 'ADMIN_NAZWA_OPCJI'),
(493, 'ADMIN_WARTOSC_OPCJI'),
(494, 'ADMIN_KROTKI_OPIS_OPCJI'),
(495, 'ADMIN_PANEL_ADMINISTRACYJNY'),
(496, 'ADMIN_ZARZADZANIE_SLOWNIKIEM_PANELU'),
(497, 'ADMIN_IMPORT_SLOWNIKA_PANELU'),
(498, 'ADMIN_NOWY_OBIEKT'),
(499, 'ADMIN_EDYCJA_OBIEKTU'),
(500, 'ADMIN_ZARZADZANIE_OBIEKTAMI'),
(501, 'ADMIN_SET_MARKER'),
(502, 'ADMIN_OBIEKT_ROZBICIE'),
(503, 'ADMIN_KATEGORIA'),
(504, 'OBIEKTY_ADRES'),
(505, 'ADMIN_OBIEKTY_ADRES_INFO'),
(506, 'OBIEKTY_LOKALIZACJA'),
(507, 'OBIEKTY_DANE_KONTAKTOWE'),
(508, 'OBIEKTY_ATRYBUTY'),
(509, 'OBIEKTY_ATRYBUTY_INFO'),
(510, 'OBIEKTY_DODAJ_OBIEKT'),
(511, 'OBIEKTY_NOWY_OBIEKT'),
(512, 'OBIEKTY_DODAJ_NOWY'),
(513, 'KONFIGURACJA_MAILERA'),
(514, 'AUTH_EMAIL_LABEL'),
(515, 'AUTH_PASS_LABEL'),
(516, 'AUTH_SMTP_LABEL'),
(517, 'ADMIN_EMAIL_LABEL'),
(518, 'BIURO_EMAIL_LABEL'),
(519, 'AUTH_PORT_LABEL'),
(520, 'AUTH_HOST_LABEL'),
(521, 'AUTH_AUTH_LABEL');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `slownik_admin_description`
--

CREATE TABLE `slownik_admin_description` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `value` varchar(2046) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `slownik_admin_description`
--

INSERT INTO `slownik_admin_description` (`id`, `parent_id`, `language_id`, `value`) VALUES
(1, 1, 1, 'Wyloguj'),
(2, 1, 2, ''),
(3, 1, 3, ''),
(4, 1, 4, ''),
(5, 2, 1, 'Witaj'),
(6, 2, 2, ''),
(7, 2, 3, ''),
(8, 2, 4, ''),
(9, 3, 1, 'OPCJE GŁÓWNE'),
(10, 3, 2, ''),
(11, 3, 3, ''),
(12, 3, 4, ''),
(13, 4, 1, 'Statystyki strony'),
(14, 4, 2, ''),
(15, 4, 3, ''),
(16, 4, 4, ''),
(17, 5, 1, 'Podgląd strony on-line'),
(18, 5, 2, ''),
(19, 5, 3, ''),
(20, 5, 4, ''),
(21, 6, 1, 'MENU'),
(22, 6, 2, ''),
(23, 6, 3, ''),
(24, 6, 4, ''),
(25, 7, 1, 'Zarządzanie menu'),
(26, 7, 2, ''),
(27, 7, 3, ''),
(28, 7, 4, ''),
(29, 8, 1, 'ZARZĄDZANIE STRONAMI'),
(30, 8, 2, ''),
(31, 8, 3, ''),
(32, 8, 4, ''),
(33, 9, 1, 'Zawartość strony głównej'),
(34, 9, 2, ''),
(35, 9, 3, ''),
(36, 9, 4, ''),
(37, 10, 1, 'Dodaj nową podstronę'),
(38, 10, 2, ''),
(39, 10, 3, ''),
(40, 10, 4, ''),
(41, 11, 1, 'Zarządzanie podstronami'),
(42, 11, 2, ''),
(43, 11, 3, ''),
(44, 11, 4, ''),
(45, 12, 1, 'AKTUALNOŚCI'),
(46, 12, 2, ''),
(47, 12, 3, ''),
(48, 12, 4, ''),
(49, 13, 1, 'Dodaj nową aktualność'),
(50, 13, 2, ''),
(51, 13, 3, ''),
(52, 13, 4, ''),
(53, 14, 1, 'Zarządzanie aktualnościami'),
(54, 14, 2, ''),
(55, 14, 3, ''),
(56, 14, 4, ''),
(57, 15, 1, 'Galeria'),
(58, 15, 2, ''),
(59, 15, 3, ''),
(60, 15, 4, ''),
(61, 16, 1, 'Dodaj nową galerię'),
(62, 16, 2, ''),
(63, 16, 3, ''),
(64, 16, 4, ''),
(65, 17, 1, 'Zarządzanie galeriami zdjęć'),
(66, 17, 2, ''),
(67, 17, 3, ''),
(68, 17, 4, ''),
(69, 18, 1, 'ZMIENIARKA'),
(70, 18, 2, ''),
(71, 18, 3, ''),
(72, 18, 4, ''),
(73, 19, 1, 'Dodaj nową zmieniarkę'),
(74, 19, 2, ''),
(75, 19, 3, ''),
(76, 19, 4, ''),
(77, 20, 1, 'Zarządzanie zmieniarkami'),
(78, 20, 2, ''),
(79, 20, 3, ''),
(80, 20, 4, ''),
(81, 21, 1, 'BIULETYN'),
(82, 21, 2, ''),
(83, 21, 3, ''),
(84, 21, 4, ''),
(85, 22, 1, 'Informacja o biuletynie'),
(86, 22, 2, ''),
(87, 22, 3, ''),
(88, 22, 4, ''),
(89, 23, 1, 'Edycja regulaminu'),
(90, 23, 2, ''),
(91, 23, 3, ''),
(92, 23, 4, ''),
(93, 24, 1, 'Użytkownicy biuletynu'),
(94, 24, 2, ''),
(95, 24, 3, ''),
(96, 24, 4, ''),
(97, 25, 1, 'Edycja szablonów mailingu'),
(98, 25, 2, ''),
(99, 25, 3, ''),
(100, 25, 4, ''),
(101, 26, 1, 'Wysyłanie biuletynu'),
(102, 26, 2, ''),
(103, 26, 3, ''),
(104, 26, 4, ''),
(105, 27, 1, 'UŻYTKOWNICY'),
(106, 27, 2, ''),
(107, 27, 3, ''),
(108, 27, 4, ''),
(109, 28, 1, 'Dodaj użytkownika'),
(110, 28, 2, ''),
(111, 28, 3, ''),
(112, 28, 4, ''),
(113, 29, 1, 'Zarządzanie użytkownikami'),
(114, 29, 2, ''),
(115, 29, 3, ''),
(116, 29, 4, ''),
(117, 30, 1, 'Zarządzanie grupami'),
(118, 30, 2, ''),
(119, 30, 3, ''),
(120, 30, 4, ''),
(121, 31, 1, 'Zarządzanie uprawnieniami'),
(122, 31, 2, ''),
(123, 31, 3, ''),
(124, 31, 4, ''),
(125, 32, 1, 'STATYSTYKI'),
(126, 32, 2, ''),
(127, 32, 3, ''),
(128, 32, 4, ''),
(129, 33, 1, 'Logowania do panelu admin'),
(130, 33, 2, ''),
(131, 33, 3, ''),
(132, 33, 4, ''),
(133, 34, 1, 'Nieudane logowania do panelu'),
(134, 34, 2, ''),
(135, 34, 3, ''),
(136, 34, 4, ''),
(137, 35, 1, 'Odwiedzalność podstron'),
(138, 35, 2, ''),
(139, 35, 3, ''),
(140, 35, 4, ''),
(141, 36, 1, 'Odwiedzalność aktualności'),
(142, 36, 2, ''),
(143, 36, 3, ''),
(144, 36, 4, ''),
(145, 37, 1, 'Odwiedzalność FAQ'),
(146, 37, 2, ''),
(147, 37, 3, ''),
(148, 37, 4, ''),
(149, 38, 1, 'Odwiedzalność galerii'),
(150, 38, 2, ''),
(151, 38, 3, ''),
(152, 38, 4, ''),
(153, 39, 1, 'Wyniki wyszukiwarki'),
(154, 39, 2, ''),
(155, 39, 3, ''),
(156, 39, 4, ''),
(157, 40, 1, 'Statystyki wysyłania biuletynu'),
(158, 40, 2, ''),
(159, 40, 3, ''),
(160, 40, 4, ''),
(161, 41, 1, 'Rejestr zmian w serwisie'),
(162, 41, 2, ''),
(163, 41, 3, ''),
(164, 41, 4, ''),
(165, 42, 1, 'KOMENTARZE'),
(166, 42, 2, ''),
(167, 42, 3, ''),
(168, 42, 4, ''),
(169, 43, 1, 'Konfiguracja komentarzy'),
(170, 43, 2, ''),
(171, 43, 3, ''),
(172, 43, 4, ''),
(173, 44, 1, 'Moderacja komentarzy'),
(174, 44, 2, ''),
(175, 44, 3, ''),
(176, 44, 4, ''),
(177, 45, 1, 'FAQ'),
(178, 45, 2, ''),
(179, 45, 3, ''),
(180, 45, 4, ''),
(181, 46, 1, 'Dodaj nowe faq'),
(182, 46, 2, ''),
(183, 46, 3, ''),
(184, 46, 4, ''),
(185, 47, 1, 'Zarządzanie faq'),
(186, 47, 2, ''),
(187, 47, 3, ''),
(188, 47, 4, ''),
(189, 48, 1, 'KONFIGURACJA PODSTAWOWA'),
(190, 48, 2, ''),
(191, 48, 3, ''),
(192, 48, 4, ''),
(193, 49, 1, 'Moduły'),
(194, 49, 2, ''),
(195, 49, 3, ''),
(196, 49, 4, ''),
(197, 50, 1, 'Kontakt'),
(198, 50, 2, ''),
(199, 50, 3, ''),
(200, 50, 4, ''),
(201, 51, 1, 'Słownik'),
(202, 51, 2, ''),
(203, 51, 3, ''),
(204, 51, 4, ''),
(205, 52, 1, 'Konfiguracja strony'),
(206, 52, 2, ''),
(207, 52, 3, ''),
(208, 52, 4, ''),
(209, 53, 1, 'Ranking w Google'),
(210, 53, 2, ''),
(211, 53, 3, ''),
(212, 53, 4, ''),
(213, 54, 1, 'Konfiguracja domyślna SEO'),
(214, 54, 2, ''),
(215, 54, 3, ''),
(216, 54, 4, ''),
(217, 55, 1, 'Wybór szablonu'),
(218, 55, 2, ''),
(219, 55, 3, ''),
(220, 55, 4, ''),
(221, 56, 1, 'Szablony e-maili do użytkowników'),
(222, 56, 2, ''),
(223, 56, 3, ''),
(224, 56, 4, ''),
(225, 57, 1, 'Zmiana hasła'),
(226, 57, 2, ''),
(227, 57, 3, ''),
(228, 57, 4, ''),
(229, 58, 1, 'Operacje na bazie danych MYSQL'),
(230, 58, 2, ''),
(231, 58, 3, ''),
(232, 58, 4, ''),
(237, 60, 1, 'Ta opcja nie działa poprawnie. Niestety Twoja przeglądarka nie obsługuje ramek typu iFrame.'),
(238, 60, 2, ''),
(239, 60, 3, ''),
(240, 60, 4, ''),
(241, 61, 1, 'Wybierz menu którym chcesz zarządzać'),
(242, 61, 2, ''),
(243, 61, 3, ''),
(244, 61, 4, ''),
(245, 62, 1, 'Aby dodać element menu kliknij przycisk powyżej. Możesz też wybrać dowolony element i stworzyć podmenu podpięte do niego. Jeśli element posiada zaznaczoną opcje \"Element posiada podmenu\" będzisz mógł w niego kliknąć i na następnej stronie dodać elementy podmenu.'),
(246, 62, 2, ''),
(247, 62, 3, ''),
(248, 62, 4, ''),
(249, 63, 1, 'Dodaj nowy element'),
(250, 63, 2, ''),
(251, 63, 3, ''),
(252, 63, 4, ''),
(253, 64, 1, 'Aktualna ścieżka'),
(254, 64, 2, ''),
(255, 64, 3, ''),
(256, 64, 4, ''),
(257, 65, 1, 'Nazwa'),
(258, 65, 2, ''),
(259, 65, 3, ''),
(260, 65, 4, ''),
(261, 66, 1, 'Wskazuje na'),
(262, 66, 2, ''),
(263, 66, 3, ''),
(264, 66, 4, ''),
(265, 67, 1, 'Podmenu'),
(266, 67, 2, ''),
(267, 67, 3, ''),
(268, 67, 4, ''),
(269, 68, 1, 'Opcje'),
(270, 68, 2, ''),
(271, 68, 3, ''),
(272, 68, 4, ''),
(273, 69, 1, 'Nowy element'),
(274, 69, 2, ''),
(275, 69, 3, ''),
(276, 69, 4, ''),
(277, 70, 1, 'Pokaż drzewo menu'),
(278, 70, 2, ''),
(279, 70, 3, ''),
(280, 70, 4, ''),
(281, 71, 1, 'Pokaż opcje'),
(282, 71, 2, ''),
(283, 71, 3, ''),
(284, 71, 4, ''),
(285, 72, 1, 'Cofnij się o poziom w górę'),
(286, 72, 2, ''),
(287, 72, 3, ''),
(288, 72, 4, ''),
(289, 73, 1, 'Adres zewnętrzny'),
(290, 73, 2, ''),
(291, 73, 3, ''),
(292, 73, 4, ''),
(293, 74, 1, 'Podstrona'),
(294, 74, 2, ''),
(295, 74, 3, ''),
(296, 74, 4, ''),
(297, 75, 1, 'Moduł'),
(298, 75, 2, ''),
(299, 75, 3, ''),
(300, 75, 4, ''),
(301, 76, 1, 'Element bez linku'),
(302, 76, 2, ''),
(303, 76, 3, ''),
(304, 76, 4, ''),
(305, 77, 1, 'Kasuj'),
(306, 77, 2, ''),
(307, 77, 3, ''),
(308, 77, 4, ''),
(309, 78, 1, 'Edytuj'),
(310, 78, 2, ''),
(311, 78, 3, ''),
(312, 78, 4, ''),
(313, 79, 1, 'Przenieś w dół'),
(314, 79, 2, ''),
(315, 79, 3, ''),
(316, 79, 4, ''),
(317, 80, 1, 'Przenieś w górę'),
(318, 80, 2, ''),
(319, 80, 3, ''),
(320, 80, 4, ''),
(321, 81, 1, 'Usunięcie tego elementu usunie również jego elementy potomne.\nCzy jesteś pewien, że chcesz skasować wybrany element?'),
(322, 81, 2, ''),
(323, 81, 3, ''),
(324, 81, 4, ''),
(329, 83, 1, 'Menu jest puste. Aby dodać tutaj nowy element kliknij w przycisk \"Dodaj nowy element\".'),
(330, 83, 2, ''),
(331, 83, 3, ''),
(332, 83, 4, ''),
(333, 84, 1, 'Dodaj element menu'),
(334, 84, 2, ''),
(335, 84, 3, ''),
(336, 84, 4, ''),
(337, 85, 1, 'Element aktywny w wersjach językowych'),
(338, 85, 2, ''),
(339, 85, 3, ''),
(340, 85, 4, ''),
(341, 86, 1, 'Nazwa elementu'),
(342, 86, 2, ''),
(343, 86, 3, ''),
(344, 86, 4, ''),
(345, 87, 1, 'Typ podmenu'),
(346, 87, 2, ''),
(347, 87, 3, ''),
(348, 87, 4, ''),
(349, 88, 1, 'Element posiada podmenu'),
(350, 88, 2, ''),
(351, 88, 3, ''),
(352, 88, 4, ''),
(353, 89, 1, 'zarządzalne'),
(354, 89, 2, ''),
(355, 89, 3, ''),
(356, 89, 4, ''),
(357, 90, 1, 'generowane automatycznie'),
(358, 90, 2, ''),
(359, 90, 3, ''),
(360, 90, 4, ''),
(361, 91, 1, 'Wybierz moduł, z którego pobrane mają być elementy podmenu'),
(362, 91, 2, ''),
(363, 91, 3, ''),
(364, 91, 4, ''),
(365, 92, 1, 'wybierz moduł'),
(366, 92, 2, ''),
(367, 92, 3, ''),
(368, 92, 4, ''),
(369, 93, 1, 'Element wskazuje na'),
(370, 93, 2, ''),
(371, 93, 3, ''),
(372, 93, 4, ''),
(373, 94, 1, 'podstronę na serwerze'),
(374, 94, 2, ''),
(375, 94, 3, ''),
(376, 94, 4, ''),
(377, 95, 1, 'moduł na serwerze'),
(378, 95, 2, ''),
(379, 95, 3, ''),
(380, 95, 4, ''),
(381, 96, 1, 'Podaj zewnętrzny URL, rozpocznij od http://'),
(382, 96, 2, ''),
(383, 96, 3, ''),
(384, 96, 4, ''),
(385, 97, 1, 'wyświetlaj stronę w nowym oknie'),
(386, 97, 2, ''),
(387, 97, 3, ''),
(388, 97, 4, ''),
(389, 98, 1, 'link z atrybutem'),
(390, 98, 2, ''),
(391, 98, 3, ''),
(392, 98, 4, ''),
(393, 99, 1, 'Wybierz stronę na którą ma odsyłać element'),
(394, 99, 2, ''),
(395, 99, 3, ''),
(396, 99, 4, ''),
(397, 100, 1, 'wybierz stronę'),
(398, 100, 2, ''),
(399, 100, 3, ''),
(400, 100, 4, ''),
(401, 101, 1, 'Wybierz moduł, do którego ma odsyłać element'),
(402, 101, 2, ''),
(403, 101, 3, ''),
(404, 101, 4, ''),
(405, 102, 1, 'Edycja elementu menu'),
(406, 102, 2, ''),
(407, 102, 3, ''),
(408, 102, 4, ''),
(409, 103, 1, 'Nowa podstrona'),
(410, 103, 2, ''),
(411, 103, 3, ''),
(412, 103, 4, ''),
(413, 104, 1, 'Wersje językowe'),
(414, 104, 2, ''),
(415, 104, 3, ''),
(416, 104, 4, ''),
(417, 105, 1, 'Treść'),
(418, 105, 2, ''),
(419, 105, 3, ''),
(420, 105, 4, ''),
(421, 106, 1, 'aktywna w tej wersji językowej'),
(422, 106, 2, ''),
(423, 106, 3, ''),
(424, 106, 4, ''),
(425, 107, 1, 'Tytuł strony'),
(426, 107, 2, ''),
(427, 107, 3, ''),
(428, 107, 4, ''),
(429, 108, 1, 'Opcje dodatkowe'),
(430, 108, 2, ''),
(431, 108, 3, ''),
(432, 108, 4, ''),
(433, 109, 1, 'pokazuj tytuł'),
(434, 109, 2, ''),
(435, 109, 3, ''),
(436, 109, 4, ''),
(437, 110, 1, 'pokazuj podstronę w serwisie'),
(438, 110, 2, ''),
(439, 110, 3, ''),
(440, 110, 4, ''),
(441, 111, 1, 'użytkownicy mogą komentować'),
(442, 111, 2, ''),
(443, 111, 3, ''),
(444, 111, 4, ''),
(445, 112, 1, 'dostępna tylko dla zalogowanych'),
(446, 112, 2, ''),
(447, 112, 3, ''),
(448, 112, 4, ''),
(449, 113, 1, 'brak'),
(450, 113, 2, ''),
(451, 113, 3, ''),
(452, 113, 4, ''),
(453, 114, 1, 'wybrana galeria będzie dołączona do artykułu'),
(454, 114, 2, ''),
(455, 114, 3, ''),
(456, 114, 4, ''),
(457, 115, 1, 'możliwość wyboru odpowiedniego TITLE w nagłówku strony'),
(458, 115, 2, ''),
(459, 115, 3, ''),
(460, 115, 4, ''),
(461, 116, 1, 'Konfiguracja główna CMS'),
(462, 116, 2, ''),
(463, 116, 3, ''),
(464, 116, 4, ''),
(465, 117, 1, 'Automatyczna z tytułu strony'),
(466, 117, 2, ''),
(467, 117, 3, ''),
(468, 117, 4, ''),
(469, 118, 1, 'Automatyczna z tytułu strony + Konfiguracja główna CMS'),
(470, 118, 2, ''),
(471, 118, 3, ''),
(472, 118, 4, ''),
(473, 119, 1, 'Ręczna [proszę niżej wpisać tekst]'),
(474, 119, 2, ''),
(475, 119, 3, ''),
(476, 119, 4, ''),
(477, 120, 1, 'Ręczna [proszę niżej wpisać tekst] + Konfiguracja główna CMS'),
(478, 120, 2, ''),
(479, 120, 3, ''),
(480, 120, 4, ''),
(481, 121, 1, 'możliwość wyboru odpowiedniego KEYWORDS w nagłówku strony'),
(482, 121, 2, ''),
(483, 121, 3, ''),
(484, 121, 4, ''),
(485, 122, 1, 'Ręczna [proszę niżej wpisać tekst, frazy odzielone przecinkami]'),
(486, 122, 2, ''),
(487, 122, 3, ''),
(488, 122, 4, ''),
(489, 123, 1, 'Ręczna [proszę niżej wpisać tekst, frazy odzielone przecinkami] + Konfiguracja główna CMS'),
(490, 123, 2, ''),
(491, 123, 3, ''),
(492, 123, 4, ''),
(493, 124, 1, 'możliwość wyboru odpowiedniego DESCRIPTION w nagłówku strony'),
(494, 124, 2, ''),
(495, 124, 3, ''),
(496, 124, 4, ''),
(497, 125, 1, 'Automatyczna z treści strony'),
(498, 125, 2, ''),
(499, 125, 3, ''),
(500, 125, 4, ''),
(501, 126, 1, 'Automatyczna z treści strony + Konfiguracja główna CMS'),
(502, 126, 2, ''),
(503, 126, 3, ''),
(504, 126, 4, ''),
(505, 127, 1, 'Krótki opis'),
(506, 127, 2, ''),
(507, 127, 3, ''),
(508, 127, 4, ''),
(509, 128, 1, 'Automatyczny z treści strony [pierwsze 250 znaków]'),
(510, 128, 2, ''),
(511, 128, 3, ''),
(512, 128, 4, ''),
(513, 129, 1, 'Ręczny [proszę niżej wpisać tekst]'),
(514, 129, 2, ''),
(515, 129, 3, ''),
(516, 129, 4, ''),
(517, 130, 1, 'Tagi'),
(518, 130, 2, ''),
(519, 130, 3, ''),
(520, 130, 4, ''),
(521, 131, 1, 'tagi odzielone znakiem'),
(522, 131, 2, ''),
(523, 131, 3, ''),
(524, 131, 4, ''),
(525, 132, 1, 'wstawiane pod artykułem jako linki'),
(526, 132, 2, ''),
(527, 132, 3, ''),
(528, 132, 4, ''),
(529, 133, 1, 'Edycja podstrony'),
(530, 133, 2, ''),
(531, 133, 3, ''),
(532, 133, 4, ''),
(533, 134, 1, 'Tytuł i krótka treść'),
(534, 134, 2, ''),
(535, 134, 3, ''),
(536, 134, 4, ''),
(537, 135, 1, 'Data dodania'),
(538, 135, 2, ''),
(539, 135, 3, ''),
(540, 135, 4, ''),
(541, 136, 1, 'Widoczna'),
(542, 136, 2, ''),
(543, 136, 3, ''),
(544, 136, 4, ''),
(545, 137, 1, 'Brak artykułów w bazie. Aby stworzyć nową artykuł użyj opcji powyżej.'),
(546, 137, 2, ''),
(547, 137, 3, ''),
(548, 137, 4, ''),
(549, 138, 1, 'Nowa aktualność'),
(550, 138, 2, ''),
(551, 138, 3, ''),
(552, 138, 4, ''),
(553, 139, 1, 'Edycja aktualności'),
(554, 139, 2, ''),
(555, 139, 3, ''),
(556, 139, 4, ''),
(557, 140, 1, 'SEO'),
(558, 140, 2, ''),
(559, 140, 3, ''),
(560, 140, 4, ''),
(561, 141, 1, 'Pliki'),
(562, 141, 2, ''),
(563, 141, 3, ''),
(564, 141, 4, ''),
(565, 142, 1, 'Tytuł aktualności'),
(566, 142, 2, ''),
(567, 142, 3, ''),
(568, 142, 4, ''),
(569, 143, 1, 'Zdjęcie'),
(570, 143, 2, ''),
(571, 143, 3, ''),
(572, 143, 4, ''),
(573, 144, 1, 'nie większe niż'),
(574, 144, 2, ''),
(575, 144, 3, ''),
(576, 144, 4, ''),
(577, 145, 1, 'URL strony'),
(578, 145, 2, ''),
(579, 145, 3, ''),
(580, 145, 4, ''),
(581, 146, 1, 'Ciąg znaków wykorzystywany przy tworzeniu linku.'),
(582, 146, 2, ''),
(583, 146, 3, ''),
(584, 146, 4, ''),
(585, 147, 1, 'Pliki do pobrania wyświetlane na stronie.'),
(586, 147, 2, ''),
(587, 147, 3, ''),
(588, 147, 4, ''),
(589, 148, 1, 'Lp.'),
(590, 148, 2, ''),
(591, 148, 3, ''),
(592, 148, 4, ''),
(593, 149, 1, 'Plik'),
(594, 149, 2, ''),
(595, 149, 3, ''),
(596, 149, 4, ''),
(597, 150, 1, 'Brak plików w bazie. Aby dodać plik użyj formularza poniżej.'),
(598, 150, 2, ''),
(599, 150, 3, ''),
(600, 150, 4, ''),
(601, 151, 1, 'Formularz dodawania pliku.'),
(602, 151, 2, ''),
(603, 151, 3, ''),
(604, 151, 4, ''),
(605, 152, 1, 'Nazwa wyświetlana'),
(606, 152, 2, ''),
(607, 152, 3, ''),
(608, 152, 4, ''),
(609, 153, 1, 'Uczyń niewidoczną'),
(610, 153, 2, ''),
(611, 153, 3, ''),
(612, 153, 4, ''),
(613, 154, 1, 'Uczyń widoczną'),
(614, 154, 2, ''),
(615, 154, 3, ''),
(616, 154, 4, ''),
(617, 155, 1, 'Niewidoczna'),
(618, 155, 2, ''),
(619, 155, 3, ''),
(620, 155, 4, ''),
(621, 156, 1, 'Zabezpieczona hasłem'),
(622, 156, 2, ''),
(623, 156, 3, ''),
(624, 156, 4, ''),
(625, 157, 1, 'Dołączona galeria'),
(626, 157, 2, ''),
(627, 157, 3, ''),
(628, 157, 4, ''),
(629, 158, 1, 'Brak galerii'),
(630, 158, 2, ''),
(631, 158, 3, ''),
(632, 158, 4, ''),
(633, 159, 1, 'Podgląd'),
(634, 159, 2, ''),
(635, 159, 3, ''),
(636, 159, 4, ''),
(637, 160, 1, 'nowy zastąpi stary'),
(638, 160, 2, ''),
(639, 160, 3, ''),
(640, 160, 4, ''),
(641, 161, 1, 'Aby dodać nową galerię zdjęć proszę wypełnić poniższy formularz. Następnie należy przejść do nowo utworzonej galerii i tam dodawać zdjęcia.'),
(642, 161, 2, ''),
(643, 161, 3, ''),
(644, 161, 4, ''),
(645, 162, 1, 'Tytuł galerii'),
(646, 162, 2, ''),
(647, 162, 3, ''),
(648, 162, 4, ''),
(649, 163, 1, 'Szerokość miniaturki'),
(650, 163, 2, ''),
(651, 163, 3, ''),
(652, 163, 4, ''),
(653, 164, 1, 'Wysokość miniaturki'),
(654, 164, 2, ''),
(655, 164, 3, ''),
(656, 164, 4, ''),
(657, 165, 1, 'Znak wodny'),
(658, 165, 2, ''),
(659, 165, 3, ''),
(660, 165, 4, ''),
(661, 166, 1, 'Plik znaku wodnego'),
(662, 166, 2, ''),
(663, 166, 3, ''),
(664, 166, 4, ''),
(665, 167, 1, 'Pozycja X znaku wodnego'),
(666, 167, 2, ''),
(667, 167, 3, ''),
(668, 167, 4, ''),
(669, 168, 1, 'Pozycja Y znaku wodnego'),
(670, 168, 2, ''),
(671, 168, 3, ''),
(672, 168, 4, ''),
(673, 169, 1, 'Punkt startowy znaku wodnego'),
(674, 169, 2, ''),
(675, 169, 3, ''),
(676, 169, 4, ''),
(677, 170, 1, 'lewa góra'),
(678, 170, 2, ''),
(679, 170, 3, ''),
(680, 170, 4, ''),
(681, 171, 1, 'prawa góra'),
(682, 171, 2, ''),
(683, 171, 3, ''),
(684, 171, 4, ''),
(685, 172, 1, 'lewa dół'),
(686, 172, 2, ''),
(687, 172, 3, ''),
(688, 172, 4, ''),
(689, 173, 1, 'prawa dół'),
(690, 173, 2, ''),
(691, 173, 3, ''),
(692, 173, 4, ''),
(693, 174, 1, 'pokazuj galerię w serwisie'),
(694, 174, 2, ''),
(695, 174, 3, ''),
(696, 174, 4, ''),
(697, 175, 1, 'galeria wyświetlana na liście galerii'),
(698, 175, 2, ''),
(699, 175, 3, ''),
(700, 175, 4, ''),
(701, 176, 1, 'galeria możliwa do dołączenia w podstronach'),
(702, 176, 2, ''),
(703, 176, 3, ''),
(704, 176, 4, ''),
(705, 177, 1, 'Nowa galeria zdjęć'),
(706, 177, 2, ''),
(707, 177, 3, ''),
(708, 177, 4, ''),
(709, 178, 1, 'Edycja galerii zdjęć'),
(710, 178, 2, ''),
(711, 178, 3, ''),
(712, 178, 4, ''),
(713, 179, 1, 'Zarządzanie galerią zdjęć'),
(714, 179, 2, ''),
(715, 179, 3, ''),
(716, 179, 4, ''),
(717, 180, 1, 'Aby wykadrować miniaturkę kliknij na nią. Otworzy to formularz kadrowania miniatury.'),
(718, 180, 2, ''),
(719, 180, 3, ''),
(720, 180, 4, ''),
(721, 181, 1, 'nowe zastąpi już istniejące'),
(722, 181, 2, ''),
(723, 181, 3, ''),
(724, 181, 4, ''),
(725, 182, 1, 'Kadrowanie'),
(726, 182, 2, ''),
(727, 182, 3, ''),
(728, 182, 4, ''),
(729, 183, 1, 'Zdjęcie normalne'),
(730, 183, 2, ''),
(731, 183, 3, ''),
(732, 183, 4, ''),
(733, 184, 1, 'Usuń z serwera'),
(734, 184, 2, ''),
(735, 184, 3, ''),
(736, 184, 4, ''),
(737, 185, 1, 'Miniatura na stronę główną'),
(738, 185, 2, ''),
(739, 185, 3, ''),
(740, 185, 4, ''),
(741, 186, 1, 'Utwórz miniaturę'),
(742, 186, 2, ''),
(743, 186, 3, ''),
(744, 186, 4, ''),
(745, 187, 1, 'Miniatura na listę'),
(746, 187, 2, ''),
(747, 187, 3, ''),
(748, 187, 4, ''),
(749, 188, 1, 'Miniatura do szczegółów'),
(750, 188, 2, ''),
(751, 188, 3, ''),
(752, 188, 4, ''),
(753, 189, 1, 'Dodaj aktualność'),
(754, 189, 2, ''),
(755, 189, 3, ''),
(756, 189, 4, ''),
(757, 190, 1, 'Porzuć zmiany'),
(758, 190, 2, ''),
(759, 190, 3, ''),
(760, 190, 4, ''),
(761, 191, 1, 'Zapisz'),
(762, 191, 2, ''),
(763, 191, 3, ''),
(764, 191, 4, ''),
(765, 192, 1, 'Zapisz i kontynuuj edycję'),
(766, 192, 2, ''),
(767, 192, 3, ''),
(768, 192, 4, ''),
(769, 193, 1, 'trwa przetwarzanie danych...'),
(770, 193, 2, ''),
(771, 193, 3, ''),
(772, 193, 4, ''),
(773, 194, 1, 'Informacja'),
(774, 194, 2, ''),
(775, 194, 3, ''),
(776, 194, 4, ''),
(777, 195, 1, 'Informacja o błędzie'),
(778, 195, 2, ''),
(779, 195, 3, ''),
(780, 195, 4, ''),
(781, 196, 1, 'Czy jesteś pewien, że chcesz skasować wybrany element?'),
(782, 196, 2, ''),
(783, 196, 3, ''),
(784, 196, 4, ''),
(785, 197, 1, 'Wprowadzone zmiany będą nieodwracalne. Czy na pewno wykonać?'),
(786, 197, 2, ''),
(787, 197, 3, ''),
(788, 197, 4, ''),
(789, 198, 1, 'Dodaj zdjęcie'),
(790, 198, 2, ''),
(791, 198, 3, ''),
(792, 198, 4, ''),
(793, 199, 1, 'Dodaj plik'),
(794, 199, 2, ''),
(795, 199, 3, ''),
(796, 199, 4, ''),
(797, 200, 1, 'Zapisz zmiany'),
(798, 200, 2, ''),
(799, 200, 3, ''),
(800, 200, 4, ''),
(801, 201, 1, 'Dodaj podstronę'),
(802, 201, 2, ''),
(803, 201, 3, ''),
(804, 201, 4, ''),
(805, 202, 1, 'Nowa galeria'),
(806, 202, 2, ''),
(807, 202, 3, ''),
(808, 202, 4, ''),
(809, 203, 1, 'Zdjęcia'),
(810, 203, 2, ''),
(811, 203, 3, ''),
(812, 203, 4, ''),
(813, 204, 1, 'Dodawalna'),
(814, 204, 2, ''),
(815, 204, 3, ''),
(816, 204, 4, ''),
(817, 205, 1, 'Tytuł'),
(818, 205, 2, ''),
(819, 205, 3, ''),
(820, 205, 4, ''),
(821, 206, 1, 'Dodaj zdjęcia'),
(822, 206, 2, ''),
(823, 206, 3, ''),
(824, 206, 4, ''),
(825, 207, 1, 'Dołączana w podstronach'),
(826, 207, 2, ''),
(827, 207, 3, ''),
(828, 207, 4, ''),
(829, 208, 1, 'Niewidoczna w podstronach'),
(830, 208, 2, ''),
(831, 208, 3, ''),
(832, 208, 4, ''),
(833, 209, 1, 'Nie utworzono jeszcze galerii.'),
(834, 209, 2, ''),
(835, 209, 3, ''),
(836, 209, 4, ''),
(837, 210, 1, 'Aby wykadrować zdjęcie kliknij na nie. Otworzy to okno kadrowania.'),
(838, 210, 2, ''),
(839, 210, 3, ''),
(840, 210, 4, ''),
(841, 211, 1, 'Powrót do spisu galerii'),
(842, 211, 2, ''),
(843, 211, 3, ''),
(844, 211, 4, ''),
(845, 212, 1, 'Edytuj galerię'),
(846, 212, 2, ''),
(847, 212, 3, ''),
(848, 212, 4, ''),
(849, 213, 1, 'Usuń galerię'),
(850, 213, 2, ''),
(851, 213, 3, ''),
(852, 213, 4, ''),
(853, 214, 1, 'Zdjęcia w galerii'),
(854, 214, 2, ''),
(855, 214, 3, ''),
(856, 214, 4, ''),
(857, 215, 1, 'Brak zdjęcia!'),
(858, 215, 2, ''),
(859, 215, 3, ''),
(860, 215, 4, ''),
(861, 216, 1, 'Edytuj opis'),
(862, 216, 2, ''),
(863, 216, 3, ''),
(864, 216, 4, ''),
(865, 217, 1, 'Aktualnie brak zdjęć w galerii.'),
(866, 217, 2, ''),
(867, 217, 3, ''),
(868, 217, 4, ''),
(869, 218, 1, 'Wybierz zdjęcia i załaduj je na serwer [można wybrać większą liczbe zdjęć]:'),
(870, 218, 2, ''),
(871, 218, 3, ''),
(872, 218, 4, ''),
(873, 219, 1, 'Zdjęcia na serwerze'),
(874, 219, 2, ''),
(875, 219, 3, ''),
(876, 219, 4, ''),
(877, 220, 1, 'Dodaj zaznaczone do galerii'),
(878, 220, 2, ''),
(879, 220, 3, ''),
(880, 220, 4, ''),
(881, 221, 1, 'Usuń zaznaczone z serwera'),
(882, 221, 2, ''),
(883, 221, 3, ''),
(884, 221, 4, ''),
(885, 222, 1, 'Usuń zaznaczone z galerii'),
(886, 222, 2, ''),
(887, 222, 3, ''),
(888, 222, 4, ''),
(889, 223, 1, 'Wybierz zdjęcia do galerii:'),
(890, 223, 2, ''),
(891, 223, 3, ''),
(892, 223, 4, ''),
(893, 224, 1, 'Edycja opisu dla zdjecia nr'),
(894, 224, 2, ''),
(895, 224, 3, ''),
(896, 224, 4, ''),
(897, 225, 1, 'w galerii'),
(898, 225, 2, ''),
(899, 225, 3, ''),
(900, 225, 4, ''),
(901, 226, 1, 'Opis'),
(902, 226, 2, ''),
(903, 226, 3, ''),
(904, 226, 4, ''),
(905, 227, 1, 'Nie utworzono jeszcze zmieniarki.'),
(906, 227, 2, ''),
(907, 227, 3, ''),
(908, 227, 4, ''),
(909, 228, 1, 'Usuń'),
(910, 228, 2, ''),
(911, 228, 3, ''),
(912, 228, 4, ''),
(913, 229, 1, 'Etykieta zmieniarki'),
(914, 229, 2, ''),
(915, 229, 3, ''),
(916, 229, 4, ''),
(917, 230, 1, 'tekst po którym zmieniarka będzie identyfikowana'),
(918, 230, 2, ''),
(919, 230, 3, ''),
(920, 230, 4, ''),
(921, 231, 1, 'zmieniarka widoczna w serwisie'),
(922, 231, 2, ''),
(923, 231, 3, ''),
(924, 231, 4, ''),
(925, 232, 1, 'Powrót do listy zmieniarek'),
(926, 232, 2, ''),
(927, 232, 3, ''),
(928, 232, 4, ''),
(929, 233, 1, 'Konfiguruj zmieniarkę'),
(930, 233, 2, ''),
(931, 233, 3, ''),
(932, 233, 4, ''),
(933, 234, 1, 'Usuń zmieniarkę'),
(934, 234, 2, ''),
(935, 234, 3, ''),
(936, 234, 4, ''),
(937, 235, 1, 'Aktualnie brak zdjęć w zmieniarce.'),
(938, 235, 2, ''),
(939, 235, 3, ''),
(940, 235, 4, ''),
(941, 236, 1, 'Wybierz zdjęcia do galerii [po kliknięciu na zdjęcie można ustawić obszar kadrowania, jeśli dana galeria ma włączoną taką opcje]:'),
(942, 236, 2, ''),
(943, 236, 3, ''),
(944, 236, 4, ''),
(945, 237, 1, 'Dodaj zaznaczone do zmieniarki'),
(946, 237, 2, ''),
(947, 237, 3, ''),
(948, 237, 4, ''),
(949, 238, 1, 'Usuń zaznaczone ze zmieniarki'),
(950, 238, 2, ''),
(951, 238, 3, ''),
(952, 238, 4, ''),
(953, 239, 1, 'w zmieniarce'),
(954, 239, 2, ''),
(955, 239, 3, ''),
(956, 239, 4, ''),
(957, 240, 1, 'Zawartość atrybutu \"alt\":'),
(958, 240, 2, ''),
(959, 240, 3, ''),
(960, 240, 4, ''),
(961, 241, 1, 'Zarządzanie zmieniarką'),
(962, 241, 2, ''),
(963, 241, 3, ''),
(964, 241, 4, ''),
(965, 242, 1, 'Dodaj zmieniarkę'),
(966, 242, 2, ''),
(967, 242, 3, ''),
(968, 242, 4, ''),
(969, 243, 1, 'Informacja o biuletynie to tekst wyświetlany nad formularzem rejestracji.'),
(970, 243, 2, ''),
(971, 243, 3, ''),
(972, 243, 4, ''),
(973, 244, 1, 'Regulamin biuletynu'),
(974, 244, 2, ''),
(975, 244, 3, ''),
(976, 244, 4, ''),
(977, 245, 1, 'Wysyłąnie newslettera'),
(978, 245, 2, ''),
(979, 245, 3, ''),
(980, 245, 4, ''),
(981, 246, 1, 'Zarządzanie użytkownikami biuletynu'),
(982, 246, 2, ''),
(983, 246, 3, ''),
(984, 246, 4, ''),
(985, 247, 1, 'Edycja użytkownika biuletynu'),
(986, 247, 2, ''),
(987, 247, 3, ''),
(988, 247, 4, ''),
(989, 248, 1, 'Zmiany w regulaminie biuletynu zostały zapisane!'),
(990, 248, 2, ''),
(991, 248, 3, ''),
(992, 248, 4, ''),
(993, 249, 1, 'Zmiany w informacji o biuletynie zostały zapisane!'),
(994, 249, 2, ''),
(995, 249, 3, ''),
(996, 249, 4, ''),
(997, 250, 1, 'Wystąpił błąd podczas zapisywania zmian w regulaminie biuletynu!'),
(998, 250, 2, ''),
(999, 250, 3, ''),
(1000, 250, 4, ''),
(1001, 251, 1, 'Wystąpił błąd podczas zapisywania zmian w informacji o biuletynie!'),
(1002, 251, 2, ''),
(1003, 251, 3, ''),
(1004, 251, 4, ''),
(1005, 252, 1, 'Biuletyn wysłano do'),
(1006, 252, 2, ''),
(1007, 252, 3, ''),
(1008, 252, 4, ''),
(1009, 253, 1, 'odbiorców'),
(1010, 253, 2, ''),
(1011, 253, 3, ''),
(1012, 253, 4, ''),
(1013, 254, 1, 'Biuletyn nie został wysłany do nikogo! Prawdopodobnie brak aktywnych adresów e-mail w bazie!'),
(1014, 254, 2, ''),
(1015, 254, 3, ''),
(1016, 254, 4, ''),
(1017, 255, 1, '<p>W polach tekstowych można stosować \"maski\": % zastępuje dowolny ciąg, _ pojedynczy znak.</p><p>Na przykład: wpisanie \"P%\" w pole imię spowoduje wyświetlenie wszystkich użytkowników o imieniu zaczynającym się na literę P.</p>'),
(1018, 255, 2, ''),
(1019, 255, 3, ''),
(1020, 255, 4, ''),
(1021, 256, 1, 'Filtr'),
(1022, 256, 2, ''),
(1023, 256, 3, ''),
(1024, 256, 4, ''),
(1025, 257, 1, 'imię'),
(1026, 257, 2, ''),
(1027, 257, 3, ''),
(1028, 257, 4, ''),
(1029, 258, 1, 'nazwisko'),
(1030, 258, 2, ''),
(1031, 258, 3, ''),
(1032, 258, 4, ''),
(1033, 259, 1, 'E-mail'),
(1034, 259, 2, ''),
(1035, 259, 3, ''),
(1036, 259, 4, ''),
(1037, 260, 1, 'Pokaż konta'),
(1038, 260, 2, ''),
(1039, 260, 3, ''),
(1040, 260, 4, ''),
(1041, 261, 1, 'wszystkie'),
(1042, 261, 2, ''),
(1043, 261, 3, ''),
(1044, 261, 4, ''),
(1045, 262, 1, 'id użytkownika'),
(1046, 262, 2, ''),
(1047, 262, 3, ''),
(1048, 262, 4, ''),
(1049, 263, 1, 'nazwiska'),
(1050, 263, 2, ''),
(1051, 263, 3, ''),
(1052, 263, 4, ''),
(1053, 264, 1, 'Sortowanie'),
(1054, 264, 2, ''),
(1055, 264, 3, ''),
(1056, 264, 4, ''),
(1057, 265, 1, 'Ilość'),
(1058, 265, 2, ''),
(1059, 265, 3, ''),
(1060, 265, 4, ''),
(1061, 266, 1, 'Ilość użytkowników na stronę'),
(1062, 266, 2, ''),
(1063, 266, 3, ''),
(1064, 266, 4, ''),
(1065, 267, 1, 'Sortuj według'),
(1066, 267, 2, ''),
(1067, 267, 3, ''),
(1068, 267, 4, ''),
(1069, 268, 1, 'wybierz'),
(1070, 268, 2, ''),
(1071, 268, 3, ''),
(1072, 268, 4, ''),
(1073, 269, 1, 'adresu e-mail'),
(1074, 269, 2, ''),
(1075, 269, 3, ''),
(1076, 269, 4, ''),
(1077, 270, 1, 'nieaktywne'),
(1078, 270, 2, ''),
(1079, 270, 3, ''),
(1080, 270, 4, ''),
(1081, 271, 1, 'aktywne'),
(1082, 271, 2, ''),
(1083, 271, 3, ''),
(1084, 271, 4, ''),
(1085, 272, 1, 'rosnąco'),
(1086, 272, 2, ''),
(1087, 272, 3, ''),
(1088, 272, 4, ''),
(1089, 273, 1, 'malejąco'),
(1090, 273, 2, ''),
(1091, 273, 3, ''),
(1092, 273, 4, ''),
(1093, 274, 1, 'Zastosuj'),
(1094, 274, 2, ''),
(1095, 274, 3, ''),
(1096, 274, 4, ''),
(1097, 275, 1, 'Pokaż wszystkie konta'),
(1098, 275, 2, ''),
(1099, 275, 3, ''),
(1100, 275, 4, ''),
(1101, 276, 1, 'Dodaj adres email'),
(1102, 276, 2, ''),
(1103, 276, 3, ''),
(1104, 276, 4, ''),
(1105, 277, 1, 'Brak użytkowników o podanych kryteriach w bazie.'),
(1106, 277, 2, ''),
(1107, 277, 3, ''),
(1108, 277, 4, ''),
(1109, 278, 1, 'potwierdzenie wysyłania biuletynu'),
(1110, 278, 2, ''),
(1111, 278, 3, ''),
(1112, 278, 4, ''),
(1113, 279, 1, 'Powrót'),
(1114, 279, 2, ''),
(1115, 279, 3, ''),
(1116, 279, 4, ''),
(1117, 280, 1, 'Biuletyn wysyłany na adres'),
(1118, 280, 2, ''),
(1119, 280, 3, ''),
(1120, 280, 4, ''),
(1121, 281, 1, 'Biuletyn wysyłany na'),
(1122, 281, 2, ''),
(1123, 281, 3, ''),
(1124, 281, 4, ''),
(1125, 282, 1, 'aktywnych adresów e-mail.'),
(1126, 282, 2, ''),
(1127, 282, 3, ''),
(1128, 282, 4, ''),
(1129, 283, 1, 'Czy na pewno chcesz wysłać biuletyn?'),
(1130, 283, 2, ''),
(1131, 283, 3, ''),
(1132, 283, 4, ''),
(1133, 284, 1, 'Tytuł biuletynu'),
(1134, 284, 2, ''),
(1135, 284, 3, ''),
(1136, 284, 4, ''),
(1137, 285, 1, 'Wyślij biuletyn'),
(1138, 285, 2, ''),
(1139, 285, 3, ''),
(1140, 285, 4, ''),
(1141, 286, 1, 'Wybierz szablon, który chcesz edytować'),
(1142, 286, 2, ''),
(1143, 286, 3, ''),
(1144, 286, 4, ''),
(1145, 287, 1, 'Dodaj nowy szablon'),
(1146, 287, 2, ''),
(1147, 287, 3, ''),
(1148, 287, 4, ''),
(1149, 288, 1, 'Brak szablonów w bazie!'),
(1150, 288, 2, ''),
(1151, 288, 3, ''),
(1152, 288, 4, ''),
(1153, 289, 1, 'Dodaj szablon'),
(1154, 289, 2, ''),
(1155, 289, 3, ''),
(1156, 289, 4, ''),
(1157, 290, 1, 'Powrót do spisu użytkowników'),
(1158, 290, 2, ''),
(1159, 290, 3, ''),
(1160, 290, 4, ''),
(1161, 291, 1, 'Status konta'),
(1162, 291, 2, ''),
(1163, 291, 3, ''),
(1164, 291, 4, ''),
(1165, 292, 1, 'Nazwa szablonu'),
(1166, 292, 2, ''),
(1167, 292, 3, ''),
(1168, 292, 4, ''),
(1169, 293, 1, 'Dodaj'),
(1170, 293, 2, ''),
(1171, 293, 3, ''),
(1172, 293, 4, ''),
(1173, 294, 1, '<p>Aby wysłać biuletyn do wszystkich użytkowników proszę wypełnić poniższy formularz. Przycisk \"Wyślij biuletyn\" należy kliknąć tylko raz, w przeciwnym wypadku użytkownicy mogą dostać kilka kopii tego samego listu.</p><p>W treści biuletynu można zastosować zmienne. Tekst <b>#IMIE#</b> zostanie zastąpiony imieniem podanym podczas rejestracji, natomiast <b>#NAZWISKO#</b> zostanie zamienione na nazwisko.</p>'),
(1174, 294, 2, ''),
(1175, 294, 3, ''),
(1176, 294, 4, ''),
(1177, 295, 1, 'Aktualnie w bazie jest'),
(1178, 295, 2, ''),
(1179, 295, 3, ''),
(1180, 295, 4, ''),
(1181, 296, 1, 'adresów, z czego'),
(1182, 296, 2, ''),
(1183, 296, 3, ''),
(1184, 296, 4, ''),
(1185, 297, 1, 'aktywnych'),
(1186, 297, 2, ''),
(1187, 297, 3, ''),
(1188, 297, 4, ''),
(1189, 298, 1, 'Wybierz szablon'),
(1190, 298, 2, ''),
(1191, 298, 3, ''),
(1192, 298, 4, ''),
(1193, 299, 1, 'Wczytaj'),
(1194, 299, 2, ''),
(1195, 299, 3, ''),
(1196, 299, 4, ''),
(1197, 300, 1, 'Wyślij biuletyn do'),
(1198, 300, 2, ''),
(1199, 300, 3, ''),
(1200, 300, 4, ''),
(1201, 301, 1, 'wszystkich zapisanych'),
(1202, 301, 2, ''),
(1203, 301, 3, ''),
(1204, 301, 4, ''),
(1205, 302, 1, 'na podany adres'),
(1206, 302, 2, ''),
(1207, 302, 3, ''),
(1208, 302, 4, ''),
(1209, 303, 1, 'podaj adres, na jaki wysłać biuletyn'),
(1210, 303, 2, ''),
(1211, 303, 3, ''),
(1212, 303, 4, ''),
(1213, 304, 1, 'wybierz grupę, do której chcesz wysłać biuletyn'),
(1214, 304, 2, ''),
(1215, 304, 3, ''),
(1216, 304, 4, ''),
(1217, 305, 1, 'Login'),
(1218, 305, 2, ''),
(1219, 305, 3, ''),
(1220, 305, 4, ''),
(1221, 306, 1, 'bez dostępu do panelu admina'),
(1222, 306, 2, ''),
(1223, 306, 3, ''),
(1224, 306, 4, ''),
(1225, 307, 1, 'z dostępem do panelu admina'),
(1226, 307, 2, ''),
(1227, 307, 3, ''),
(1228, 307, 4, ''),
(1229, 308, 1, 'loginu'),
(1230, 308, 2, ''),
(1231, 308, 3, ''),
(1232, 308, 4, ''),
(1233, 309, 1, 'nazwy firmy'),
(1234, 309, 2, ''),
(1235, 309, 3, ''),
(1236, 309, 4, ''),
(1237, 310, 1, 'Dodaj nowego użytkownika'),
(1238, 310, 2, ''),
(1239, 310, 3, ''),
(1240, 310, 4, ''),
(1241, 311, 1, 'Nazwa grupy'),
(1242, 311, 2, ''),
(1243, 311, 3, ''),
(1244, 311, 4, ''),
(1245, 312, 1, 'Zarządzaj'),
(1246, 312, 2, ''),
(1247, 312, 3, ''),
(1248, 312, 4, ''),
(1249, 313, 1, 'Zmień nazwę'),
(1250, 313, 2, ''),
(1251, 313, 3, ''),
(1252, 313, 4, ''),
(1253, 314, 1, 'Brak zdefiniowanych grup użytkowników!'),
(1254, 314, 2, ''),
(1255, 314, 3, ''),
(1256, 314, 4, ''),
(1257, 315, 1, 'Nowa grupa'),
(1258, 315, 2, ''),
(1259, 315, 3, ''),
(1260, 315, 4, ''),
(1261, 316, 1, 'Dodaj grupę'),
(1262, 316, 2, ''),
(1263, 316, 3, ''),
(1264, 316, 4, ''),
(1265, 317, 1, 'Dodaj nową grupę'),
(1266, 317, 2, ''),
(1267, 317, 3, ''),
(1268, 317, 4, ''),
(1269, 318, 1, 'Uprawnienia dla nowej grupy'),
(1270, 318, 2, ''),
(1271, 318, 3, ''),
(1272, 318, 4, ''),
(1273, 319, 1, 'Brak uprawnień w bazie!'),
(1274, 319, 2, ''),
(1275, 319, 3, ''),
(1276, 319, 4, ''),
(1277, 320, 1, 'Zaznaczenie'),
(1278, 320, 2, ''),
(1279, 320, 3, ''),
(1280, 320, 4, ''),
(1281, 321, 1, 'Odznaczenie wszystkich'),
(1282, 321, 2, ''),
(1283, 321, 3, ''),
(1284, 321, 4, ''),
(1285, 322, 1, 'Powrót do wszystkich grup'),
(1286, 322, 2, ''),
(1287, 322, 3, ''),
(1288, 322, 4, ''),
(1289, 323, 1, 'Uprawnienia dla grupy'),
(1290, 323, 2, ''),
(1291, 323, 3, ''),
(1292, 323, 4, ''),
(1293, 324, 1, 'Dodaj nowe uprawnienie'),
(1294, 324, 2, ''),
(1295, 324, 3, ''),
(1296, 324, 4, ''),
(1297, 325, 1, 'Ok'),
(1298, 325, 2, ''),
(1299, 325, 3, ''),
(1300, 325, 4, ''),
(1301, 326, 1, 'Nazwa konta / użytkownika'),
(1302, 326, 2, ''),
(1303, 326, 3, ''),
(1304, 326, 4, ''),
(1305, 327, 1, 'Zmień hasło'),
(1306, 327, 2, ''),
(1307, 327, 3, ''),
(1308, 327, 4, ''),
(1309, 328, 1, 'Logowanie do panelu administratora'),
(1310, 328, 2, ''),
(1311, 328, 3, ''),
(1312, 328, 4, ''),
(1313, 329, 1, 'NIE'),
(1314, 329, 2, ''),
(1315, 329, 3, ''),
(1316, 329, 4, ''),
(1317, 330, 1, 'TAK'),
(1318, 330, 2, ''),
(1319, 330, 3, ''),
(1320, 330, 4, ''),
(1321, 331, 1, 'Grupa'),
(1322, 331, 2, ''),
(1323, 331, 3, ''),
(1324, 331, 4, ''),
(1325, 332, 1, 'nie wybrano grupy'),
(1326, 332, 2, ''),
(1327, 332, 3, ''),
(1328, 332, 4, ''),
(1329, 333, 1, 'Typ konta'),
(1330, 333, 2, ''),
(1331, 333, 3, ''),
(1332, 333, 4, ''),
(1333, 334, 1, 'Proszę zaznaczyć właściwe'),
(1334, 334, 2, ''),
(1335, 334, 3, ''),
(1336, 334, 4, ''),
(1337, 335, 1, 'osoba fizyczna'),
(1338, 335, 2, ''),
(1339, 335, 3, ''),
(1340, 335, 4, ''),
(1341, 336, 1, 'firma'),
(1342, 336, 2, ''),
(1343, 336, 3, ''),
(1344, 336, 4, ''),
(1345, 337, 1, 'Nazwa firmy'),
(1346, 337, 2, ''),
(1347, 337, 3, ''),
(1348, 337, 4, ''),
(1349, 338, 1, 'Numer NIP'),
(1350, 338, 2, ''),
(1351, 338, 3, ''),
(1352, 338, 4, ''),
(1353, 339, 1, 'Ulica'),
(1354, 339, 2, ''),
(1355, 339, 3, ''),
(1356, 339, 4, ''),
(1357, 340, 1, 'Nr budynku/lokalu'),
(1358, 340, 2, ''),
(1359, 340, 3, ''),
(1360, 340, 4, ''),
(1361, 341, 1, 'Kod pocztowy'),
(1362, 341, 2, ''),
(1363, 341, 3, ''),
(1364, 341, 4, ''),
(1365, 342, 1, 'Poprawny kod pocztowy: xx-xxx'),
(1366, 342, 2, ''),
(1367, 342, 3, ''),
(1368, 342, 4, ''),
(1369, 343, 1, 'Miejscowość'),
(1370, 343, 2, ''),
(1371, 343, 3, ''),
(1372, 343, 4, ''),
(1373, 344, 1, 'Telefon'),
(1374, 344, 2, ''),
(1375, 344, 3, ''),
(1376, 344, 4, ''),
(1377, 345, 1, 'Hasło'),
(1378, 345, 2, ''),
(1379, 345, 3, ''),
(1380, 345, 4, ''),
(1381, 346, 1, 'Powtórz hasło'),
(1382, 346, 2, ''),
(1383, 346, 3, ''),
(1384, 346, 4, ''),
(1385, 347, 1, 'Powtórz e-mail'),
(1386, 347, 2, ''),
(1387, 347, 3, ''),
(1388, 347, 4, ''),
(1389, 348, 1, 'Dodawanie nowego użytkownika'),
(1390, 348, 2, ''),
(1391, 348, 3, ''),
(1392, 348, 4, ''),
(1393, 349, 1, 'Edycja użytkownika'),
(1394, 349, 2, ''),
(1395, 349, 3, ''),
(1396, 349, 4, ''),
(1397, 350, 1, 'Zarządzanie uprawnieniami użytkowników'),
(1398, 350, 2, ''),
(1399, 350, 3, ''),
(1400, 350, 4, ''),
(1401, 351, 1, 'Zmiany zostały zapisane!'),
(1402, 351, 2, ''),
(1403, 351, 3, ''),
(1404, 351, 4, ''),
(1405, 352, 1, 'Zarządzanie szablonami biuletynu'),
(1406, 352, 2, ''),
(1407, 352, 3, ''),
(1408, 352, 4, ''),
(1409, 353, 1, 'Edycja szablonu biuletynu'),
(1410, 353, 2, ''),
(1411, 353, 3, ''),
(1412, 353, 4, ''),
(1413, 354, 1, 'Zarządzanie grupami użytkowników'),
(1414, 354, 2, ''),
(1415, 354, 3, ''),
(1416, 354, 4, ''),
(1417, 355, 1, 'Logowania do panelu'),
(1418, 355, 2, ''),
(1419, 355, 3, ''),
(1420, 355, 4, ''),
(1421, 356, 1, 'Data'),
(1422, 356, 2, ''),
(1423, 356, 3, ''),
(1424, 356, 4, ''),
(1425, 357, 1, 'Host'),
(1426, 357, 2, ''),
(1427, 357, 3, ''),
(1428, 357, 4, ''),
(1429, 358, 1, 'Brak wpisów w bazie.'),
(1430, 358, 2, ''),
(1431, 358, 3, ''),
(1432, 358, 4, ''),
(1433, 359, 1, 'Nieudane logowania'),
(1434, 359, 2, ''),
(1435, 359, 3, ''),
(1436, 359, 4, ''),
(1437, 360, 1, 'Powód odrzucenia'),
(1438, 360, 2, ''),
(1439, 360, 3, ''),
(1440, 360, 4, ''),
(1441, 361, 1, 'Odwiedzalność'),
(1442, 361, 2, ''),
(1443, 361, 3, ''),
(1444, 361, 4, ''),
(1445, 362, 1, 'Data utworzenia'),
(1446, 362, 2, ''),
(1447, 362, 3, ''),
(1448, 362, 4, ''),
(1449, 363, 1, 'Odwiedziny'),
(1450, 363, 2, ''),
(1451, 363, 3, ''),
(1452, 363, 4, ''),
(1453, 364, 1, 'Frazy wpisane w wyszukiwarce'),
(1454, 364, 2, ''),
(1455, 364, 3, ''),
(1456, 364, 4, ''),
(1457, 365, 1, 'Wpisywana fraza'),
(1458, 365, 2, ''),
(1459, 365, 3, ''),
(1460, 365, 4, ''),
(1461, 366, 1, 'Znalezionych wyników'),
(1462, 366, 2, ''),
(1463, 366, 3, ''),
(1464, 366, 4, ''),
(1465, 367, 1, 'Ilość wyświetleń'),
(1466, 367, 2, ''),
(1467, 367, 3, ''),
(1468, 367, 4, ''),
(1469, 368, 1, 'Rejestr zmian'),
(1470, 368, 2, ''),
(1471, 368, 3, ''),
(1472, 368, 4, ''),
(1473, 369, 1, 'Statystyka wysyłania biuletynu'),
(1474, 369, 2, ''),
(1475, 369, 3, ''),
(1476, 369, 4, ''),
(1477, 370, 1, 'Wysłane'),
(1478, 370, 2, ''),
(1479, 370, 3, ''),
(1480, 370, 4, ''),
(1481, 371, 1, 'Odebrane'),
(1482, 371, 2, ''),
(1483, 371, 3, ''),
(1484, 371, 4, ''),
(1485, 372, 1, 'Kliknięte linki'),
(1486, 372, 2, ''),
(1487, 372, 3, ''),
(1488, 372, 4, ''),
(1489, 373, 1, 'Akcja'),
(1490, 373, 2, ''),
(1491, 373, 3, ''),
(1492, 373, 4, ''),
(1493, 374, 1, 'Dotyczy'),
(1494, 374, 2, ''),
(1495, 374, 3, ''),
(1496, 374, 4, ''),
(1497, 375, 1, 'Wykonana przez'),
(1498, 375, 2, ''),
(1499, 375, 3, ''),
(1500, 375, 4, ''),
(1501, 376, 1, 'Zmiany w konfiguracji zostały zapisane!'),
(1502, 376, 2, ''),
(1503, 376, 3, ''),
(1504, 376, 4, ''),
(1505, 377, 1, 'Wystąpił błąd podczas zapisywania zmian w konfiguracji!'),
(1506, 377, 2, ''),
(1507, 377, 3, ''),
(1508, 377, 4, ''),
(1509, 378, 1, 'Komentarze widoczne dla <b>niezalogowanych</b>?'),
(1510, 378, 2, ''),
(1511, 378, 3, ''),
(1512, 378, 4, ''),
(1513, 379, 1, 'Czy <b>niezalogowani</b> użytkownicy mogą pisać komentarze?'),
(1514, 379, 2, ''),
(1515, 379, 3, ''),
(1516, 379, 4, ''),
(1517, 380, 1, '<p>Aby edytować i kasować komentarze należy odnaleźć żądany komentarz i użyć odpowiedniej opcji.</p><p>Wybierz odpowiednią grupe komentarzy.</p>'),
(1518, 380, 2, ''),
(1519, 380, 3, ''),
(1520, 380, 4, ''),
(1521, 381, 1, 'Autor'),
(1522, 381, 2, ''),
(1523, 381, 3, ''),
(1524, 381, 4, ''),
(1525, 382, 1, 'Id.'),
(1526, 382, 2, ''),
(1527, 382, 3, ''),
(1528, 382, 4, ''),
(1529, 383, 1, 'Edycja komentarza'),
(1530, 383, 2, ''),
(1531, 383, 3, ''),
(1532, 383, 4, ''),
(1533, 384, 1, 'pokaż komentarze'),
(1534, 384, 2, ''),
(1535, 384, 3, ''),
(1536, 384, 4, ''),
(1537, 385, 1, 'Nowe pytanie'),
(1538, 385, 2, ''),
(1539, 385, 3, ''),
(1540, 385, 4, ''),
(1541, 386, 1, 'Edycja pytania'),
(1542, 386, 2, ''),
(1543, 386, 3, ''),
(1544, 386, 4, ''),
(1545, 387, 1, 'Zarządzanie pytaniami'),
(1546, 387, 2, ''),
(1547, 387, 3, ''),
(1548, 387, 4, ''),
(1549, 388, 1, 'Dodaj nowe pytanie'),
(1550, 388, 2, ''),
(1551, 388, 3, ''),
(1552, 388, 4, ''),
(1557, 390, 1, 'Podgląd online'),
(1558, 390, 2, ''),
(1559, 390, 3, ''),
(1560, 390, 4, ''),
(1561, 391, 1, 'Brak pytań w bazie. Aby stworzyć nowe pytanie użyj opcji powyżej.'),
(1562, 391, 2, ''),
(1563, 391, 3, ''),
(1564, 391, 4, ''),
(1565, 392, 1, 'Tytuł pytania'),
(1566, 392, 2, ''),
(1567, 392, 3, ''),
(1568, 392, 4, ''),
(1569, 393, 1, 'Dodaj pytanie'),
(1570, 393, 2, ''),
(1571, 393, 3, ''),
(1572, 393, 4, ''),
(1577, 395, 1, 'Miniatura'),
(1578, 395, 2, ''),
(1579, 395, 3, ''),
(1580, 395, 4, ''),
(1581, 396, 1, 'Nowy moduł'),
(1582, 396, 2, ''),
(1583, 396, 3, ''),
(1584, 396, 4, ''),
(1585, 397, 1, 'Edycja modułu'),
(1586, 397, 2, ''),
(1587, 397, 3, ''),
(1588, 397, 4, ''),
(1589, 398, 1, 'Zarządzanie modułami'),
(1590, 398, 2, ''),
(1591, 398, 3, ''),
(1592, 398, 4, ''),
(1593, 399, 1, 'Dodaj nowy moduł'),
(1594, 399, 2, ''),
(1595, 399, 3, ''),
(1596, 399, 4, ''),
(1597, 400, 1, 'Etykieta'),
(1598, 400, 2, ''),
(1599, 400, 3, ''),
(1600, 400, 4, ''),
(1601, 401, 1, 'Frontend'),
(1602, 401, 2, ''),
(1603, 401, 3, ''),
(1604, 401, 4, ''),
(1605, 402, 1, 'Backend'),
(1606, 402, 2, ''),
(1607, 402, 3, ''),
(1608, 402, 4, ''),
(1609, 403, 1, 'Tylko dla zalogowanych'),
(1610, 403, 2, ''),
(1611, 403, 3, ''),
(1612, 403, 4, ''),
(1613, 404, 1, 'Aktywny'),
(1614, 404, 2, ''),
(1615, 404, 3, ''),
(1616, 404, 4, ''),
(1617, 405, 1, 'Brak modułów w bazie. Aby dodać nowy moduł użyj opcji powyżej.'),
(1618, 405, 2, ''),
(1619, 405, 3, ''),
(1620, 405, 4, ''),
(1621, 406, 1, '<p>Upewnij się, że zmiany, które wprowadzasz są poprawne. Mogą one prowadzić do nieprawidłowego działania systemu.</p><p>Generowanie nowych plików dla modułów zostanie uruchomione <b>tylko</b> w przypadku gdy <b>te pliki nie istnieją</b>.</p>'),
(1622, 406, 2, ''),
(1623, 406, 3, ''),
(1624, 406, 4, ''),
(1625, 408, 1, 'Nazwa wyświetlana jako adres URL <small>[bez .html]</small>'),
(1626, 408, 2, ''),
(1627, 408, 3, ''),
(1628, 408, 4, ''),
(1629, 409, 1, 'moduł frontend-u'),
(1630, 409, 2, ''),
(1631, 409, 3, ''),
(1632, 409, 4, ''),
(1633, 410, 1, 'moduł panelu administracyjnego'),
(1634, 410, 2, ''),
(1635, 410, 3, ''),
(1636, 410, 4, ''),
(1637, 411, 1, 'Nazwa tabeli w bazie danych: <small>[nazwa tabeli dla wersji jezykowych generowana automatycznie]</small>'),
(1638, 411, 2, ''),
(1639, 411, 3, ''),
(1640, 411, 4, ''),
(1641, 412, 1, 'Główna klasa PHP: <small>[nazwa klasy panelu administracyjnego generowana automatycznie]</small>'),
(1642, 412, 2, ''),
(1643, 412, 3, ''),
(1644, 412, 4, ''),
(1645, 413, 1, 'Nazwa pliku zawierającego definicję klasy'),
(1646, 413, 2, ''),
(1647, 413, 3, ''),
(1648, 413, 4, ''),
(1649, 414, 1, 'Nazwa katalogu zawierającego szablony html'),
(1650, 414, 2, ''),
(1651, 414, 3, ''),
(1652, 414, 4, ''),
(1653, 415, 1, 'Dodaj moduł'),
(1654, 415, 2, ''),
(1655, 415, 3, ''),
(1656, 415, 4, ''),
(1657, 416, 1, 'Zawartość strony kontaktowej'),
(1658, 416, 2, ''),
(1659, 416, 3, ''),
(1660, 416, 4, ''),
(1661, 417, 1, 'Zarządzanie słownikiem'),
(1662, 417, 2, ''),
(1663, 417, 3, ''),
(1664, 417, 4, ''),
(1665, 418, 1, 'Import słownika'),
(1666, 418, 2, ''),
(1667, 418, 3, ''),
(1668, 418, 4, ''),
(1669, 419, 1, 'W słowniku przechowywane są edytowalne teksty i komunikaty wykorzystywane w serwisie. Aby znaleźć potrzebny tekst należy w polu szukaj wpisać jego etykietę lub fragment treści (może to być jeden wyraz)'),
(1670, 419, 2, ''),
(1671, 419, 3, ''),
(1672, 419, 4, ''),
(1673, 420, 1, 'Dodaj nowy wyraz'),
(1674, 420, 2, ''),
(1675, 420, 3, ''),
(1676, 420, 4, ''),
(1677, 421, 1, 'Exportuj do Excel-a'),
(1678, 421, 2, ''),
(1679, 421, 3, ''),
(1680, 421, 4, ''),
(1681, 422, 1, 'Importuj z Excel-a'),
(1682, 422, 2, ''),
(1683, 422, 3, ''),
(1684, 422, 4, ''),
(1685, 423, 1, 'Szukaj'),
(1686, 423, 2, ''),
(1687, 423, 3, ''),
(1688, 423, 4, ''),
(1689, 424, 1, 'Powrót do słownika'),
(1690, 424, 2, ''),
(1691, 424, 3, ''),
(1692, 424, 4, ''),
(1693, 425, 1, 'Tekst'),
(1694, 425, 2, ''),
(1695, 425, 3, ''),
(1696, 425, 4, ''),
(1697, 426, 1, 'Zaimportować można tylko specjalnie przygotowane pliki Excela. Aby otrzymać taki plik można skorzystać z opcji exportu słownika do pliki Excel, która taki plik utworzy wraz z całą zawartością słownika.'),
(1698, 426, 2, ''),
(1699, 426, 3, ''),
(1700, 426, 4, ''),
(1701, 427, 1, 'Importuj'),
(1702, 427, 2, ''),
(1703, 427, 3, ''),
(1704, 427, 4, ''),
(1705, 428, 1, 'Pierwsza'),
(1706, 428, 2, ''),
(1707, 428, 3, ''),
(1708, 428, 4, ''),
(1709, 429, 1, 'Poprzednia'),
(1710, 429, 2, ''),
(1711, 429, 3, ''),
(1712, 429, 4, ''),
(1713, 430, 1, 'Następna'),
(1714, 430, 2, ''),
(1715, 430, 3, ''),
(1716, 430, 4, ''),
(1717, 431, 1, 'Ostatnia'),
(1718, 431, 2, ''),
(1719, 431, 3, ''),
(1720, 431, 4, ''),
(1725, 433, 1, 'Opcja została skasowana!'),
(1726, 433, 2, ''),
(1727, 433, 3, ''),
(1728, 433, 4, ''),
(1729, 434, 1, 'Wystąpił błąd podczas kasowania opcji!'),
(1730, 434, 2, ''),
(1731, 434, 3, ''),
(1732, 434, 4, ''),
(1733, 435, 1, 'Nowa opcja została dodana!'),
(1734, 435, 2, ''),
(1735, 435, 3, ''),
(1736, 435, 4, ''),
(1737, 436, 1, 'Wystąpił błąd podczas dodawania nowej opcji!'),
(1738, 436, 2, ''),
(1739, 436, 3, ''),
(1740, 436, 4, ''),
(1741, 437, 1, 'Ranking w Google'),
(1742, 437, 2, ''),
(1743, 437, 3, ''),
(1744, 437, 4, ''),
(1745, 438, 1, '<p>Ranking pozycji w Google na podane frazy (słowa kluczowe), max można dodać 10 fraz.</p><p>System automatycznie sprawdza pozycję w wyszukiwarce google i zapisuje dane do systemu.</p>'),
(1746, 438, 2, ''),
(1747, 438, 3, ''),
(1748, 438, 4, ''),
(1749, 439, 1, 'Aktualnie automat sprawdzający jest'),
(1750, 439, 2, ''),
(1751, 439, 3, ''),
(1752, 439, 4, ''),
(1753, 440, 1, 'włączony'),
(1754, 440, 2, ''),
(1755, 440, 3, ''),
(1756, 440, 4, ''),
(1757, 441, 1, 'wyłączony'),
(1758, 441, 2, ''),
(1759, 441, 3, ''),
(1760, 441, 4, ''),
(1761, 442, 1, 'dla domeny'),
(1762, 442, 2, ''),
(1763, 442, 3, ''),
(1764, 442, 4, ''),
(1765, 443, 1, 'Fraza'),
(1766, 443, 2, ''),
(1767, 443, 3, ''),
(1768, 443, 4, ''),
(1769, 444, 1, 'Pozycja'),
(1770, 444, 2, ''),
(1771, 444, 3, ''),
(1772, 444, 4, ''),
(1773, 445, 1, 'Pokaż wyniki'),
(1774, 445, 2, ''),
(1775, 445, 3, ''),
(1776, 445, 4, ''),
(1777, 446, 1, 'Dodawanie frazy'),
(1778, 446, 2, ''),
(1779, 446, 3, ''),
(1780, 446, 4, ''),
(1781, 447, 1, 'Anchor'),
(1782, 447, 2, ''),
(1783, 447, 3, ''),
(1784, 447, 4, ''),
(1785, 448, 1, 'Wykorzystałeś już liczbę dopuszczalnych fraz.'),
(1786, 448, 2, ''),
(1787, 448, 3, ''),
(1788, 448, 4, ''),
(1789, 449, 1, 'Data sprawdzania'),
(1790, 449, 2, ''),
(1791, 449, 3, ''),
(1792, 449, 4, ''),
(1793, 450, 1, 'Powrót do spisu fraz'),
(1794, 450, 2, ''),
(1795, 450, 3, ''),
(1796, 450, 4, ''),
(1801, 452, 1, '<div>Ustawienie opcji SEO przy dodawaniu nowych podstron. Dzięki tej konfiguracji mozna dostosować stronę pod wyszukiwarki internetowe lub pozostawić domyślne ustawienia strony. Jeżeli nie jesteś pewnien jakie opcje wybrać pozostaw wszędzie \"konfiguracja CMS\".</div><div>WAŻNE ! pamiętaj aby nie zmieniać często tych opcji.</div>'),
(1802, 452, 2, ''),
(1803, 452, 3, ''),
(1804, 452, 4, ''),
(1805, 453, 1, '<div>Parametr <b>title</b> odnosi się do tytułu strony wyświetlanego na górnym pasku przeglądarki.</div><div>Jeżeli chcesz maksymalnie zoptymalizować tą opcję wybierz parametr \"z tytułu + CMS\".</div>'),
(1806, 453, 2, ''),
(1807, 453, 3, ''),
(1808, 453, 4, ''),
(1809, 454, 1, 'konfiguracja CMS'),
(1810, 454, 2, ''),
(1811, 454, 3, ''),
(1812, 454, 4, ''),
(1813, 455, 1, 'z tytułu'),
(1814, 455, 2, ''),
(1815, 455, 3, ''),
(1816, 455, 4, ''),
(1817, 456, 1, 'z tytułu + CMS'),
(1818, 456, 2, ''),
(1819, 456, 3, ''),
(1820, 456, 4, ''),
(1821, 457, 1, 'ręczna'),
(1822, 457, 2, ''),
(1823, 457, 3, ''),
(1824, 457, 4, ''),
(1825, 458, 1, 'ręczna + CMS'),
(1826, 458, 2, ''),
(1827, 458, 3, ''),
(1828, 458, 4, ''),
(1829, 459, 1, 'Zawartość tagu &lt;title&gt;'),
(1830, 459, 2, ''),
(1831, 459, 3, ''),
(1832, 459, 4, ''),
(1833, 460, 1, 'Prefiks'),
(1834, 460, 2, ''),
(1835, 460, 3, ''),
(1836, 460, 4, ''),
(1837, 461, 1, 'Suffiks'),
(1838, 461, 2, ''),
(1839, 461, 3, ''),
(1840, 461, 4, ''),
(1841, 462, 1, '<div>Parameter <b>keywords</b> odnosi się do słów kluczowych strony / podstorny.</div><div>Jeżeli chcesz maksymanie zoptymalizować tą opcję wybierz parametr \"z tytułu + CMS\".</div>'),
(1842, 462, 2, ''),
(1843, 462, 3, ''),
(1844, 462, 4, ''),
(1845, 463, 1, '<div>Parametr <b>description</b> odnosi się do opisu strony/podstrony dla wyszukiwarek</div><div>Jeżeli chcesz maksymanie zoptymalizować tą opcję wybierz parametr \"z tytułu + CMS\".</div>'),
(1846, 463, 2, ''),
(1847, 463, 3, ''),
(1848, 463, 4, ''),
(1849, 464, 1, 'z treści'),
(1850, 464, 2, ''),
(1851, 464, 3, ''),
(1852, 464, 4, ''),
(1853, 465, 1, 'z treści + CMS'),
(1854, 465, 2, ''),
(1855, 465, 3, ''),
(1856, 465, 4, ''),
(1857, 466, 1, 'Wystąpił błąd podczas zapisywania zmian!'),
(1858, 466, 2, ''),
(1859, 466, 3, ''),
(1860, 466, 4, ''),
(1861, 467, 1, 'Wybór domyślnego szablonu'),
(1862, 467, 2, ''),
(1863, 467, 3, ''),
(1864, 467, 4, ''),
(1865, 468, 1, 'Posługując się poniższym formularzem możesz wybrać domyślny szablon wyświetlany zwykłemu użytkownikowi. <br />\r\n        W tabeli przedstawiono dostępne szablony oraz ich status. Jeżeli w którymkolwiek miejscu pojawi się błąd, oznacza to \r\n        iż szablon nie może zostać poprawnie wyświetlony. Aby wybór szablonu stał się możliwy należy utworzyć odpowiednie katalogi oraz nadać im uprawnienia do zapisu (chmod 777).<br /><br />\r\n        Jeżeli powstanie błąd dla <b>katalogu kompilacji</b>, wówczas należy w katalogu {$ROOT_PATH}/templates/<b>_compile/</b> utworzyć katalog o nazwie takiej jak nazwa szablonu.<br />\r\n        Jeśli natomiast system wygeneruje błąd dla <b>katalogu cache</b>, wówczas w katalogu {$ROOT_PATH}/templates/<b>_cache/</b> należy utworzyć katalog o nazwie takiej jak nazwa szablonu.'),
(1866, 468, 2, ''),
(1867, 468, 3, ''),
(1868, 468, 4, ''),
(1869, 469, 1, 'Aby <b>zainstalować nowy szablon</b> należy utworzyć katalog (będzie to jednocześnie nazwa szablonu) w katalogu'),
(1870, 469, 2, ''),
(1871, 469, 3, ''),
(1872, 469, 4, ''),
(1873, 470, 1, 'a następnie wgrać do niego uprzednio przygotowane pliki.'),
(1874, 470, 2, ''),
(1875, 470, 3, ''),
(1876, 470, 4, ''),
(1877, 471, 1, 'Wybierz domyślny szablon'),
(1878, 471, 2, ''),
(1879, 471, 3, ''),
(1880, 471, 4, ''),
(1881, 472, 1, 'Katalog kompilacji'),
(1882, 472, 2, ''),
(1883, 472, 3, ''),
(1884, 472, 4, ''),
(1885, 473, 1, 'Katalog cache'),
(1886, 473, 2, ''),
(1887, 473, 3, ''),
(1888, 473, 4, ''),
(1889, 474, 1, '<b>BŁĄD!</b><br />Nadaj uprawnienia do zapisu (chmod 777) lub stwórz katalog '),
(1890, 474, 2, ''),
(1891, 474, 3, ''),
(1892, 474, 4, ''),
(1897, 476, 1, 'Aby zmienić hasło należy wypełnić wszystkie pola poniższego formularza'),
(1898, 476, 2, ''),
(1899, 476, 3, ''),
(1900, 476, 4, ''),
(1901, 477, 1, 'Stare hasło'),
(1902, 477, 2, ''),
(1903, 477, 3, ''),
(1904, 477, 4, ''),
(1905, 478, 1, 'Nowe hasło'),
(1906, 478, 2, ''),
(1907, 478, 3, ''),
(1908, 478, 4, ''),
(1909, 479, 1, 'Powtórz nowe hasło'),
(1910, 479, 2, ''),
(1911, 479, 3, ''),
(1912, 479, 4, ''),
(1917, 481, 1, 'Export bazy danych MYSQL'),
(1918, 481, 2, ''),
(1919, 481, 3, ''),
(1920, 481, 4, ''),
(1921, 482, 1, 'Automatyczny zrzut bazy danych wykonywany jest raz w miesiącu. Aby exportować bazę danych ręcznie do pliku należy użyć funkcji poniżej EXPORT.<br />Dostępne pliki można również pobrać.'),
(1922, 482, 2, ''),
(1923, 482, 3, ''),
(1924, 482, 4, ''),
(1925, 483, 1, 'Lista plików zawierających zrzuty bazy danych. Nazwa pliku określa datę utworzenie kopii bazy danych.'),
(1926, 483, 2, ''),
(1927, 483, 3, ''),
(1928, 483, 4, ''),
(1929, 484, 1, 'Exportuj'),
(1930, 484, 2, ''),
(1931, 484, 3, ''),
(1932, 484, 4, ''),
(1933, 485, 1, 'ExWróć na listę'),
(1934, 485, 2, ''),
(1935, 485, 3, ''),
(1936, 485, 4, ''),
(1937, 486, 1, 'Czy jesteś pewien, że chcesz skasować wybrane elementy?'),
(1938, 486, 2, ''),
(1939, 486, 3, ''),
(1940, 486, 4, ''),
(1941, 487, 1, 'Zarządzaj uprawnieniami'),
(1942, 487, 2, ''),
(1943, 487, 3, ''),
(1944, 487, 4, ''),
(1945, 488, 1, 'Element'),
(1946, 488, 2, ''),
(1947, 488, 3, ''),
(1948, 488, 4, ''),
(1949, 489, 1, 'Słownik panelu'),
(1950, 489, 2, 'Słownik panelu'),
(1951, 489, 3, 'Słownik panelu'),
(1952, 489, 4, 'Słownik panelu'),
(1953, 490, 1, 'Brak opcji konfiguracyjnych!'),
(1954, 490, 2, 'Brak opcji konfiguracyjnych!'),
(1955, 490, 3, 'Brak opcji konfiguracyjnych!'),
(1956, 490, 4, 'Brak opcji konfiguracyjnych!'),
(1957, 491, 1, 'Dodaj nową opcję'),
(1958, 491, 2, 'Dodaj nową opcję'),
(1959, 491, 3, 'Dodaj nową opcję'),
(1960, 491, 4, 'Dodaj nową opcję'),
(1961, 492, 1, 'Nazwa opcji'),
(1962, 492, 2, 'Nazwa opcji'),
(1963, 492, 3, 'Nazwa opcji'),
(1964, 492, 4, 'Nazwa opcji'),
(1965, 493, 1, 'Wartość opcji'),
(1966, 493, 2, 'Wartość opcji'),
(1967, 493, 3, 'Wartość opcji'),
(1968, 493, 4, 'Wartość opcji'),
(1969, 494, 1, 'Krótki opis opcji'),
(1970, 494, 2, ''),
(1971, 494, 3, ''),
(1972, 494, 4, ''),
(1973, 495, 1, 'Panel administracyjny'),
(1974, 495, 2, ''),
(1975, 495, 3, ''),
(1976, 495, 4, ''),
(1977, 496, 1, 'Zarządzanie słownikiem panelu'),
(1978, 496, 2, ''),
(1979, 496, 3, ''),
(1980, 496, 4, ''),
(1981, 497, 1, 'Import słownika panelu'),
(1982, 497, 2, ''),
(1983, 497, 3, ''),
(1984, 497, 4, ''),
(1985, 498, 1, 'Nowy obiekt'),
(1986, 498, 2, ''),
(1987, 498, 3, ''),
(1988, 498, 4, ''),
(1989, 499, 1, 'Edycja obiektu'),
(1990, 499, 2, ''),
(1991, 499, 3, ''),
(1992, 499, 4, ''),
(1993, 500, 1, 'Zarządzanie obiektami'),
(1994, 500, 2, ''),
(1995, 500, 3, ''),
(1996, 500, 4, ''),
(1997, 501, 1, 'Przeciągnij w odpowiednie miejsce'),
(1998, 501, 2, ''),
(1999, 501, 3, ''),
(2000, 501, 4, ''),
(2001, 502, 1, '(znak | rozbija tytuł na wiersze)'),
(2002, 502, 2, ''),
(2003, 502, 3, ''),
(2004, 502, 4, ''),
(2005, 503, 1, 'Kategoria'),
(2006, 503, 2, ''),
(2007, 503, 3, ''),
(2008, 503, 4, ''),
(2009, 504, 1, 'Pełny adres'),
(2010, 504, 2, ''),
(2011, 504, 3, ''),
(2012, 504, 4, ''),
(2013, 505, 1, '(kliknij poza obszarem tego pola, by zaktualizować mapę)'),
(2014, 505, 2, ''),
(2015, 505, 3, ''),
(2016, 505, 4, ''),
(2017, 506, 1, 'Lokalizacja'),
(2018, 506, 2, ''),
(2019, 506, 3, ''),
(2020, 506, 4, ''),
(2021, 507, 1, 'Dane kontaktowe'),
(2022, 507, 2, ''),
(2023, 507, 3, ''),
(2024, 507, 4, ''),
(2025, 508, 1, 'Atrybuty'),
(2026, 508, 2, ''),
(2027, 508, 3, ''),
(2028, 508, 4, ''),
(2029, 509, 1, 'możesz zaznaczyć kilka z użyciem przycisków SHIFT i CTRL'),
(2030, 509, 2, ''),
(2031, 509, 3, ''),
(2032, 509, 4, ''),
(2033, 510, 1, 'Dodaj obiekt'),
(2034, 510, 2, ''),
(2035, 510, 3, ''),
(2036, 510, 4, ''),
(2037, 511, 1, 'Nowy obiekt'),
(2038, 511, 2, ''),
(2039, 511, 3, ''),
(2040, 511, 4, ''),
(2041, 512, 1, 'Dodaj nowy obiekt'),
(2042, 512, 2, ''),
(2043, 512, 3, ''),
(2044, 512, 4, ''),
(2045, 513, 1, 'Konfiguracja mailera'),
(2046, 513, 2, ''),
(2047, 513, 3, ''),
(2048, 513, 4, ''),
(2049, 514, 1, 'Adres e-mail do autoryzacji SMTP'),
(2050, 514, 2, ''),
(2051, 514, 3, ''),
(2052, 514, 4, '');
INSERT INTO `slownik_admin_description` (`id`, `parent_id`, `language_id`, `value`) VALUES
(2053, 515, 1, 'Hasło dla e-mail do autoryzacji SMTP'),
(2054, 515, 2, ''),
(2055, 515, 3, ''),
(2056, 515, 4, ''),
(2057, 516, 1, 'Włączenie 1/wyłączenie 0 SMTP'),
(2058, 516, 2, ''),
(2059, 516, 3, ''),
(2060, 516, 4, ''),
(2061, 517, 1, 'Adres e-mail administratora systemu'),
(2062, 517, 2, ''),
(2063, 517, 3, ''),
(2064, 517, 4, ''),
(2065, 518, 1, 'Adres e-mail z jakiego klienci dostaną e-mail'),
(2066, 518, 2, ''),
(2067, 518, 3, ''),
(2068, 518, 4, ''),
(2069, 519, 1, 'Port mailera'),
(2070, 519, 2, ''),
(2071, 519, 3, ''),
(2072, 519, 4, ''),
(2073, 520, 1, 'Host mailera'),
(2074, 520, 2, ''),
(2075, 520, 3, ''),
(2076, 520, 4, ''),
(2077, 521, 1, 'Włączenie 1 / wyłączenie 0 autoryzacji'),
(2078, 521, 2, ''),
(2079, 521, 3, ''),
(2080, 521, 4, '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `slownik_description`
--

CREATE TABLE `slownik_description` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `value` varchar(2046) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `slownik_description`
--

INSERT INTO `slownik_description` (`id`, `parent_id`, `language_id`, `value`) VALUES
(1, 1, 1, 'czytaj więcej'),
(2, 1, 2, 'Read more'),
(3, 1, 3, 'Mehr lesen'),
(4, 1, 4, 'читать подробнее'),
(5, 2, 1, 'pl'),
(6, 2, 2, 'en'),
(7, 2, 3, 'de'),
(8, 2, 4, 'ru'),
(9, 3, 1, 'Deutsch'),
(10, 3, 2, 'Deutsch'),
(11, 3, 3, 'Deutsch'),
(12, 3, 4, 'Deutsch'),
(13, 4, 1, 'English'),
(14, 4, 2, 'English'),
(15, 4, 3, 'English'),
(16, 4, 4, 'English'),
(17, 5, 1, 'Polski'),
(18, 5, 2, 'Polski'),
(19, 5, 3, 'Polski'),
(20, 5, 4, 'Polski'),
(21, 6, 1, 'Россию'),
(22, 6, 2, 'Россию'),
(23, 6, 3, 'Россию'),
(24, 6, 4, 'Россию'),
(25, 7, 1, 'Menu'),
(26, 7, 2, 'Menu'),
(27, 7, 3, 'Menu'),
(28, 7, 4, 'меню'),
(29, 8, 1, 'Styczeń'),
(30, 8, 2, 'January'),
(31, 8, 3, 'Januar'),
(32, 8, 4, 'Январь'),
(33, 9, 1, 'Październik'),
(34, 9, 2, 'October'),
(35, 9, 3, 'Oktober'),
(36, 9, 4, 'Октябрь'),
(37, 10, 1, 'Listopad'),
(38, 10, 2, 'November'),
(39, 10, 3, 'November'),
(40, 10, 4, 'Ноябрь'),
(41, 11, 1, 'Grudzień'),
(42, 11, 2, 'December'),
(43, 11, 3, 'Dezember'),
(44, 11, 4, 'Декабрь'),
(45, 12, 1, 'Luty'),
(46, 12, 2, 'February'),
(47, 12, 3, 'Februar'),
(48, 12, 4, 'Февраль'),
(49, 13, 1, 'Marzec'),
(50, 13, 2, 'March'),
(51, 13, 3, 'März'),
(52, 13, 4, 'Март'),
(53, 14, 1, 'Kwiecień'),
(54, 14, 2, 'April'),
(55, 14, 3, 'April'),
(56, 14, 4, 'Апрель'),
(57, 15, 1, 'Maj'),
(58, 15, 2, 'May'),
(59, 15, 3, 'Mai'),
(60, 15, 4, 'Май'),
(61, 16, 1, 'Czerwiec'),
(62, 16, 2, 'June'),
(63, 16, 3, 'Juni'),
(64, 16, 4, 'Июнь'),
(65, 17, 1, 'Lipiec'),
(66, 17, 2, 'July'),
(67, 17, 3, 'Juli'),
(68, 17, 4, 'Июль'),
(69, 18, 1, 'Sierpień'),
(70, 18, 2, 'August'),
(71, 18, 3, 'August'),
(72, 18, 4, 'Август'),
(73, 19, 1, 'Wrzesień'),
(74, 19, 2, 'September'),
(75, 19, 3, 'September'),
(76, 19, 4, 'Сентябрь'),
(77, 20, 1, 'Wybierz język'),
(78, 20, 2, 'Choose language'),
(79, 20, 3, 'Sprache wählen'),
(80, 20, 4, 'выберите язык'),
(81, 21, 1, 'Wystąpił błąd podczas tworzenia elementu!'),
(82, 21, 2, 'An error has occurred during item creation!'),
(83, 21, 3, 'Beim Hinzufügen des Elements ist ein Fehler aufgetreten!'),
(84, 21, 4, 'При создании элемента произошла ошибка!'),
(85, 22, 1, 'Element został pomyślnie utworzony.'),
(86, 22, 2, 'Item has been successfully created.'),
(87, 22, 3, 'Element wurde erfolgreich hinzugefügt.'),
(88, 22, 4, 'Элемент создан успешно'),
(89, 23, 1, 'Wystąpił błąd podczas usuwania elementu!'),
(90, 23, 2, 'An Error has occurred during item deletion!'),
(91, 23, 3, 'Beim Löschen des Elements ist ein Fehler aufgetreten!'),
(92, 23, 4, 'Во время удаления элемента произошла ошибка!'),
(93, 24, 1, 'Element został skasowany.'),
(94, 24, 2, 'Item has been deleted successfully.'),
(95, 24, 3, 'Element wurde erfolgreich gelöscht.'),
(96, 24, 4, 'Элемент успешно удален.'),
(97, 25, 1, 'Element przeniesiony o jeden poziom w dół.'),
(98, 25, 2, 'Item moved one level down.'),
(99, 25, 3, 'Element eine Ebene nach unten bewegt.'),
(100, 25, 4, 'Элемент перемещается на один уровень вниз.'),
(101, 26, 1, 'Element przeniesiony w dół.'),
(102, 26, 2, 'Item moved down.'),
(103, 26, 3, 'Element nach unten bewegt.'),
(104, 26, 4, 'Элемент перемещается вниз.'),
(105, 27, 1, 'Element przeniesiony o jeden poziom w górę.'),
(106, 27, 2, 'Item moved one level up.'),
(107, 27, 3, 'Element eine Ebene nach oben verschoben.'),
(108, 27, 4, 'Элемент перемещается на один уровень вверх.'),
(109, 28, 1, 'Element przeniesiony w górę.'),
(110, 28, 2, 'Item moved up.'),
(111, 28, 3, 'Element nach oben bewegt.'),
(112, 28, 4, 'Элемент перемещается вверх.'),
(113, 29, 1, 'Usunięto #PHOTOS zdjęć.'),
(114, 29, 2, 'Removed #PHOTOS pictures.'),
(115, 29, 3, 'Entfernt #PHOTOS fotos.'),
(116, 29, 4, 'Удалены #PHOTOS фотографии.'),
(117, 30, 1, 'Usunięto zdjęcia.'),
(118, 30, 2, 'Pictures deleted.'),
(119, 30, 3, 'Bilder gelöscht.'),
(120, 30, 4, 'Удаленные снимки.'),
(121, 31, 1, 'Zdjęcie jest przypisane do galerii i nie można go usunąć.'),
(122, 31, 2, 'The picture is attached to the gallery and you can not delete it.'),
(123, 31, 3, 'Das Bild ist auf der Galerie angebracht und man kann ihn nicht löschen.'),
(124, 31, 4, 'Картина прилагается в галерею, и вы не можете удалить его.'),
(125, 32, 1, 'Wybrane zdjęcie zostało usunięte!'),
(126, 32, 2, 'The selected image was deleted!'),
(127, 32, 3, 'Das gewählte Bild wurde gelöscht!'),
(128, 32, 4, 'Выбранная фотография удалена!'),
(129, 33, 1, 'Wystąpił błąd podczas dodawania zdjęcia!'),
(130, 33, 2, 'An error occurred while adding photo!'),
(131, 33, 3, 'Ein Fehler ist aufgetreten beim Hinzufügen der Bilder!'),
(132, 33, 4, 'Ошибка при добавлении фотографий!'),
(133, 34, 1, 'Zdjęcie zostało dodane.'),
(134, 34, 2, 'Photo was added.'),
(135, 34, 3, 'Foto aufgenommen.'),
(136, 34, 4, 'Фотография была добавлена.'),
(137, 35, 1, 'Wystąpił błąd podczas zmiany widoczności elementu!'),
(138, 35, 2, 'An error occurred while changing the visibility of the item!'),
(139, 35, 3, 'Fehler beim Ändern der Sichtbarkeit des Elements!'),
(140, 35, 4, 'Произошла ошибка при изменении видимости деталь!'),
(141, 36, 1, 'Zmiana widoczności zakończona sukcesem.'),
(142, 36, 2, 'Changing visibility accomplished.'),
(143, 36, 3, 'Ändern Sichtbarkeit erreicht.'),
(144, 36, 4, 'Изменение видимости выполнена.'),
(145, 37, 1, 'Element o podanym URL już istnieje w systemie. Proszę wybrać inny.'),
(146, 37, 2, 'Item at the specified URL already exists in the system. Please choose another one.'),
(147, 37, 3, 'Element an der angegebenen URL bereits im System vorhanden ist. Bitte wählen Sie einen anderen.'),
(148, 37, 4, 'Элемент с указанным URL уже существует в системе. Пожалуйста, выберите другой.'),
(149, 38, 1, 'Wystąpił błąd podczas aktualizowania elementu!'),
(150, 38, 2, 'An error has occurred during item update!'),
(151, 38, 3, 'Beim Bearbeiten des Elements ist ein Fehler aufgetreten!'),
(152, 38, 4, 'Во время обновления элемента произошла ошибка!'),
(153, 39, 1, 'Element został pomyślnie zaktualizowany.'),
(154, 39, 2, 'Item has been successfully updated.'),
(155, 39, 3, 'Element wurde erfolgreich bearbeitet.'),
(156, 39, 4, 'Элемент успешно обновлен.'),
(157, 40, 1, '© 2013 Copyright by Technetium'),
(158, 40, 2, '© 2013 Copyright by Technetium'),
(159, 40, 3, '© 2013 Copyright by Technetium'),
(160, 40, 4, '© 2013 Copyright by Technetium'),
(161, 41, 1, 'Pliki do pobrania'),
(162, 41, 2, 'Downloads'),
(163, 41, 3, 'Downloads'),
(164, 41, 4, 'Загрузки'),
(165, 42, 1, 'Wystąpił błąd podczas dodawania pliku!'),
(166, 42, 2, 'An error occurred while adding the file!'),
(167, 42, 3, 'Ein Fehler ist aufgetreten die Datei!'),
(168, 42, 4, 'Ошибка при добавлении файла!'),
(169, 43, 1, 'Plik został dodany.'),
(170, 43, 2, 'The file has been added.'),
(171, 43, 3, 'Die Datei wurde hinzugefügt.'),
(172, 43, 4, 'Файл был добавлен.'),
(173, 44, 1, 'Wystąpił błąd podczas usuwania pliku!'),
(174, 44, 2, 'An error occurred while deleting a file!'),
(175, 44, 3, 'Fehler beim Löschen einer Datei!'),
(176, 44, 4, 'Произошла ошибка при удалении файла!'),
(177, 45, 1, 'Usunięto #FILES plików.'),
(178, 45, 2, 'Removed #FILES files.'),
(179, 45, 3, '#FILES Dateien entfernt.'),
(180, 45, 4, 'Удалены #FILES Файлы.'),
(181, 46, 1, 'Plik usunięto pomyślnie.'),
(182, 46, 2, 'File was removed successfully.'),
(183, 46, 3, 'Datei wurde erfolgreich entfernt.'),
(184, 46, 4, 'Файл был успешно удален.'),
(185, 47, 1, 'Plik przeniesiony o jeden poziom w dół.'),
(186, 47, 2, 'The file moved one level down.'),
(187, 47, 3, 'Die Datei eine Ebene nach unten bewegt.'),
(188, 47, 4, 'Файл перемещается на один уровень вниз.'),
(189, 48, 1, 'Plik przeniesiony w dół.'),
(190, 48, 2, 'The file moved down.'),
(191, 48, 3, 'Die Datei nach unten bewegt.'),
(192, 48, 4, 'Файл перемещается вниз.'),
(193, 49, 1, 'Plik przeniesiony o jeden poziom w górę.'),
(194, 49, 2, 'The file moved one level up.'),
(195, 49, 3, 'Die Datei eine Ebene nach oben verschoben.'),
(196, 49, 4, 'Файл перемещается на один уровень вверх.'),
(197, 50, 1, 'Plik przeniesiony w górę.'),
(198, 50, 2, 'The file moved up.'),
(199, 50, 3, 'Die Datei nach oben bewegt.'),
(200, 50, 4, 'Файл перемещается вверх.'),
(201, 51, 1, 'Plik jest za duży.'),
(202, 51, 2, 'The file is too large.'),
(203, 51, 3, 'Die Datei ist zu groß.'),
(204, 51, 4, 'Файл слишком большой.'),
(205, 52, 1, 'Wystąpił błąd podczas aktualizowania pliku!'),
(206, 52, 2, 'An error occurred while updating the file!'),
(207, 52, 3, 'Fehler beim Aktualisieren der Datei!'),
(208, 52, 4, 'Произошла ошибка при обновлении файла!'),
(209, 53, 1, 'Plik został zaktualizowany.'),
(210, 53, 2, 'The file has been updated.'),
(211, 53, 3, 'Die Datei wurde aktualisiert.'),
(212, 53, 4, 'Файл был обновлен.'),
(213, 54, 1, 'Niepoprawne rozszerzenie pliku.'),
(214, 54, 2, 'Wrong file extension.'),
(215, 54, 3, 'Falsche Dateiendung.'),
(216, 54, 4, 'Неправильный расширение файла.'),
(217, 55, 1, 'Realizacja <a href=\"http://www.technetium.pl\" target=\"_blank\" title=\"profesjonalne strony www\">strony www</a>: Technetium.pl'),
(218, 55, 2, 'Realization: <a href=\"http://www.technetium.pl\" target=\"_blank\" title=\"profesjonalne strony www\">Technetium</a> [Tc]'),
(219, 55, 3, 'Abwicklung: <a href=\"http://www.technetium.pl\" target=\"_blank\" title=\"profesjonalne strony www\">Technetium</a> [Tc]'),
(220, 55, 4, 'Разработка: <a href=\"http://www.technetium.pl\" target=\"_blank\" title=\"profesjonalne strony www\">Technetium</a> [Tc]'),
(221, 56, 1, 'Galeria zdjęć'),
(222, 56, 2, 'Photo Gallery'),
(223, 56, 3, 'Photo Gallery'),
(224, 56, 4, 'Фотогалерея'),
(225, 57, 1, 'Strona główna'),
(226, 57, 2, 'Main page'),
(227, 57, 3, 'Homepage'),
(228, 57, 4, 'Главная'),
(229, 58, 1, 'Wypełnij wszytkie pola'),
(230, 58, 2, 'Fill in all fields'),
(231, 58, 3, 'alle Felder ausfüllen'),
(232, 58, 4, 'заполните все поля'),
(233, 59, 1, 'powrót'),
(234, 59, 2, 'back'),
(235, 59, 3, 'zurück'),
(236, 59, 4, 'назад'),
(237, 60, 1, 'Miejscowość:'),
(238, 60, 2, 'City:'),
(239, 60, 3, 'Der Ort:'),
(240, 60, 4, 'Город (населенный пункт):'),
(241, 61, 1, 'Adres e-mail'),
(242, 61, 2, 'Email'),
(243, 61, 3, 'E-Mail'),
(244, 61, 4, 'e-mail'),
(245, 62, 1, 'Imię:'),
(246, 62, 2, 'First name:'),
(247, 62, 3, 'Vorname:'),
(248, 62, 4, 'Имя:'),
(249, 63, 1, 'Imię i nazwisko / Firma'),
(250, 63, 2, 'Your name'),
(251, 63, 3, 'Vor- und Nachname'),
(252, 63, 4, 'фамилия и имя'),
(253, 64, 1, 'Nazwisko:'),
(254, 64, 2, 'Last name:'),
(255, 64, 3, 'Nachname:'),
(256, 64, 4, 'Фамилия:'),
(257, 65, 1, 'do góry'),
(258, 65, 2, 'up'),
(259, 65, 3, 'up'),
(260, 65, 4, 'вверх'),
(261, 66, 1, 'Login:'),
(262, 66, 2, 'Login:'),
(263, 66, 3, 'Benutzername:'),
(264, 66, 4, 'Логин:'),
(265, 67, 1, 'więcej'),
(266, 67, 2, 'more'),
(267, 67, 3, 'mehr'),
(268, 67, 4, 'подробнее'),
(269, 68, 1, 'Nr budynku/lokalu:'),
(270, 68, 2, 'No building/premises:'),
(271, 68, 3, 'Hausnummer/Wohnungsnummer:'),
(272, 68, 4, 'Номер дома:'),
(273, 69, 1, 'Następna'),
(274, 69, 2, 'Next'),
(275, 69, 3, 'Nächste'),
(276, 69, 4, 'Следующая'),
(277, 70, 1, 'Poprzednia'),
(278, 70, 2, 'Prev'),
(279, 70, 3, 'Vorherige'),
(280, 70, 4, 'Предыдущая'),
(281, 71, 1, 'Hasło:'),
(282, 71, 2, 'Password:'),
(283, 71, 3, 'Kennwort:'),
(284, 71, 4, 'Пароль:'),
(285, 72, 1, 'Nowe hasło:'),
(286, 72, 2, 'New password:'),
(287, 72, 3, 'Neues Kennwort:'),
(288, 72, 4, 'Новый пароль:'),
(289, 73, 1, 'Powtórz nowe hasło:'),
(290, 73, 2, 'Powtórz nowe hasło:'),
(291, 73, 3, 'Neues Kennwort bestätigen:'),
(292, 73, 4, 'Повторить новый пароль:'),
(293, 74, 1, 'Stare hasło:'),
(294, 74, 2, 'Old password:'),
(295, 74, 3, 'Altes Kennwort:'),
(296, 74, 4, 'Старый пароль:'),
(297, 75, 1, 'Powtórz hasło:'),
(298, 75, 2, 'Confirm password:'),
(299, 75, 3, 'Kennwort wiederholen:'),
(300, 75, 4, 'Повторить пароль:'),
(301, 76, 1, 'Telefon:'),
(302, 76, 2, 'Phone:'),
(303, 76, 3, 'Telefonnummer:'),
(304, 76, 4, 'Телефон:'),
(305, 77, 1, 'Kod pocztowy:'),
(306, 77, 2, 'Postcode:'),
(307, 77, 3, 'PLZ:'),
(308, 77, 4, 'Почтовый индекс:'),
(309, 78, 1, 'Powtórz e-mail:'),
(310, 78, 2, 'Repeat e-mail:'),
(311, 78, 3, 'Postleitzahl wiederholen:'),
(312, 78, 4, 'Повторите e-mail:'),
(313, 79, 1, 'Ulica:'),
(314, 79, 2, 'Street:'),
(315, 79, 3, 'Straße:'),
(316, 79, 4, 'Улица:'),
(317, 80, 1, 'Kod z obrazka'),
(318, 80, 2, 'Correct code'),
(319, 80, 3, 'geben Sie korrekte PLZ ein'),
(320, 80, 4, 'введите правильный код'),
(321, 81, 1, 'Treść zapytania'),
(322, 81, 2, 'Your message'),
(323, 81, 3, 'Inhalt der Nachricht'),
(324, 81, 4, 'текст сообщения'),
(325, 82, 1, 'Wyślij'),
(326, 82, 2, 'Send'),
(327, 82, 3, 'Senden'),
(328, 82, 4, 'Отправить'),
(329, 83, 1, '<b>Witaj!</b><br /><br />Otrzymaliśmy następujące zgłoszenie do biuletynu:<br /><br />'),
(330, 83, 2, '<b>Hello:</b><br /><br />received follow-up for the newsletter:<br /><br />'),
(331, 84, 1, 'Rozumiem i akceptuję warunki oraz zasady zawarte w'),
(332, 84, 2, 'I understand and accept the conditions and principles contained in'),
(333, 85, 1, 'regulaminie e-biuletynu'),
(334, 85, 2, 'Rules of the e-newsletter'),
(335, 85, 4, 'регламенте электронного бюллетеня'),
(336, 86, 1, 'Na podany adres e-mail została wysłana prośba o aktywację konta!'),
(337, 86, 2, 'at the e-mail has been sent a request to activate your account!'),
(338, 87, 1, 'Jeżeli chcesz otrzymywać e-maile na podany adres, kliknij proszę na poniższy link, aby potwierdzić rejestrację:'),
(339, 87, 2, 'If you want to receive e-mails to your address, please click on the link below to confirm the registration of:'),
(340, 87, 4, 'Если Вы хотите получать e-mail на указанный адрес, нажмите, пожалуйста, на ссылку для подтверждения регистрации:'),
(341, 88, 1, 'Jeśli chcesz usunąć swoje dane z naszego systemu, użyj opcji:'),
(342, 88, 2, 'If you want to remove your details from our system, use:'),
(343, 89, 1, 'Usuń'),
(344, 89, 2, 'Delete'),
(345, 90, 1, 'Podany adres e-mail został usunięty z naszego systemu!'),
(346, 90, 2, 'The email address has been removed from our system!'),
(347, 91, 1, 'Proszę podać adres e-mail, który ma być usunięty z bazy:'),
(348, 91, 2, 'Please enter your e-mail, which is to be removed from the database:'),
(349, 92, 1, 'Na podany adres został wysłany e-mail z instrukcją jak aktywować subskrypcję biuletynu. Dziękujemy!'),
(350, 92, 2, 'On the address was sent an email with instructions on how to activate your subscription newsletter. Thank you!'),
(351, 93, 1, 'Na podany adres e-mail została wysłana prośba o potwierdzenie rezygnacji z biuletynu.'),
(352, 93, 2, 'at the e-mail has been sent to confirm the withdrawal of the newsletter.'),
(353, 94, 1, 'Wystąpił błąd podczas wysyłania potwierdzenia rejestracji! Konto nie jest aktywne!'),
(354, 94, 2, 'An error occurred when sending the registration confirmation! The account is not active!'),
(355, 95, 1, 'W naszej bazie nie ma podanego adresu'),
(356, 95, 2, 'We have not given an address'),
(357, 96, 1, 'Jeśli nie chcesz otrzymywać biuletynu, użyj opcji:'),
(358, 96, 2, 'If you do not want to receive the newsletter, use the option:'),
(359, 97, 1, 'Rejestracja została potwierdzona, od tej pory na podany adres e-mail będzie wysyłany nasz biuletyn. Dziękujemy!'),
(360, 97, 2, 'registration has been confirmed, from now on to the email address you will receive our newsletter. Thank you!'),
(361, 98, 1, '<b>Witaj!</b><br /><br />Jeśli chcesz usunąć swoje dane z naszego systemu, proszę potwierdzić rezygnację z biuletynu, klikając na poniższy link:<br />'),
(362, 98, 2, 'I want to cancel your subscription'),
(363, 98, 1, 'Chcę zrezygnować z subskrypcji'),
(364, 98, 2, 'I want to cancel your subscription'),
(365, 99, 1, 'Proszę zaakceptować regulamin biuletynu!'),
(366, 99, 2, 'Please accept the rules of the newsletter!'),
(367, 100, 1, 'Zapisz się'),
(368, 100, 2, 'Subscribe'),
(369, 101, 1, 'Aktywacja biuletynu'),
(370, 101, 2, 'Activation newsletter'),
(371, 102, 1, 'Rezygnacja z biuletynu'),
(372, 102, 2, 'The abandonment of the newsletter'),
(373, 103, 1, 'Chcę zrezygnować z subskrypcji'),
(374, 103, 2, 'I want to cancel your subscription'),
(375, 104, 1, 'Dodano:'),
(376, 104, 2, 'Added:'),
(377, 104, 3, 'Hinzugefügt:'),
(378, 104, 4, 'Добавлено:'),
(379, 105, 1, 'Artykuł, który chcesz przeczytać dostępny jest jedynie dla zalogowanych użytkowników.<br /><b><a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">Kliknij tutaj</a></b> aby się zalogować lub <b><a href=\"BASE_URL/rejestracja.html\" title=\"rejestracja\">zarejestruj się</a></b> jeżeli nie masz jeszcze konta.'),
(380, 105, 2, 'Article which you want to read is only available to logged in users.<br /><b><a href=\"BASE_URL/logowanie.html\" title=\"click here\">click here</a></b> to log in or <b><a href=\"BASE_URL/rejestracja.html\" title=\"register\">register</a></b> if you do not have yet.'),
(381, 105, 3, 'Der Artikel, den Sie lesen möchten, ist nur für angemeldete Benutzer verfügbar.<br /><b><a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">Klicken Sie hier</a></b> um sich anzumelden oder <b><a href=\"BASE_URL/rejestracja.html\" title=\"rejestracja\">registrieren Sie sich</a></b> falls Sie kein Konto haben.'),
(382, 105, 4, 'Статья, которую Вы хотите прочесть, доступна только после входа на сайт <br /><b><a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">Нажмите здесь</a></b> для входа на сайт или здесь <b><a href=\"BASE_URL/rejestracja.html\" title=\"rejestracja\"> регистрация </a></b> для регистрации.'),
(383, 106, 1, 'Zmień hasło'),
(384, 106, 2, 'Change password'),
(385, 106, 3, 'Kennwort ändern'),
(386, 106, 4, 'Изменить пароль'),
(387, 107, 1, 'Wpisany kod jest nieprawidłowy! Proszę wpisać poprawnie kod.'),
(388, 107, 2, 'Typing the code is invalid! Please enter the correct code.'),
(389, 107, 3, 'Der eingegebene Code ist ungültig! Geben Sie bitte einen korrekten Code ein.'),
(390, 107, 4, 'Введен неправильный код! Пожалуйста, введите правильный код.'),
(391, 108, 1, 'Dziękujemy! Twój komentarz został dodany'),
(392, 108, 2, 'Thank you! Your comment has been added'),
(393, 108, 3, 'Vielen Dank! Ihr Kommentar wurde hinzugefügt, wird jedoch erst nach Akzeptierung durch die Administration angezeigt'),
(394, 109, 1, 'Zmiany w komentarzu zostały zapisane!'),
(395, 109, 2, 'Changes have been saved in a comment!'),
(396, 109, 3, 'Die Änderungen im Kommentar wurden gespeichert!'),
(397, 110, 1, 'Wybrany komentarz został usunięty!'),
(398, 110, 2, 'The selected comment has been deleted!'),
(399, 110, 3, 'Der gewählte Kommentar wurde gelöscht!'),
(400, 111, 1, 'Mail w formacie HTML! Twój program nie obsługuje wiadomości w formacie HTML!'),
(401, 111, 2, 'Mail in HTML format! Your program does not support messages in HTML format!'),
(402, 111, 3, 'Die E-Mail im HTML-Format! Ihre Software bedient die HTML-Nachrichten nicht!'),
(403, 112, 1, 'Proszę wypełnić wszystkie pola formularza!'),
(404, 112, 2, 'Please complete all fields!'),
(405, 112, 3, 'Bitte füllen Sie alle Formularfelder aus!'),
(406, 112, 4, 'Заполните, пожалуйста, все поля формуляра!'),
(407, 113, 1, 'Informacja o błędzie'),
(408, 113, 2, 'Information about an error'),
(409, 113, 3, 'Fehlerinfo'),
(410, 113, 4, 'Информация об ошибке'),
(411, 114, 1, 'Strona o podanym tytule już istnieje, proszę wybrać inny tytuł.'),
(412, 114, 2, 'Page for the given title already exists, please choose a different title.'),
(413, 114, 3, 'Der Seitentitel existiert bereits, bitte einen neuen Titel auswählen.'),
(414, 115, 1, 'Faq'),
(415, 115, 2, 'Faq'),
(416, 115, 3, 'Faq'),
(417, 115, 4, 'Faq'),
(418, 116, 1, 'Galeria'),
(419, 116, 2, 'Gallery'),
(420, 116, 3, 'Galerie'),
(421, 116, 4, 'Галерея'),
(422, 117, 1, 'Przepraszamy, ale aktualnie brak zdjęć w galerii.'),
(423, 117, 2, 'Sorry, but there are currently no images in the gallery.'),
(424, 117, 3, 'Es tut uns leid, aber es gibt noch keine Bilder in der Galerie.'),
(425, 117, 4, 'Извините, в настоящее время нет изображений в галерее.'),
(426, 118, 1, 'Przejdź do <a href=\"BASE_URL/\" title=\"strona główna\">strony głównej</a>.'),
(427, 118, 2, 'Go to <a href=\"BASE_URL/\" title=\"home page\">home page</a>.'),
(428, 118, 3, 'Zur <a href=\"BASE_URL/\" title=\"strona g3ówna\">Homepage</a>.'),
(429, 118, 4, 'Перейти на <a href=\"BASE_URL/\" title=\"strona główna\">главную</a>.'),
(430, 119, 1, 'Jesteś w:'),
(431, 119, 2, 'You are in:'),
(432, 119, 3, 'Sie sind in:'),
(433, 119, 4, 'Вы находитесь:'),
(434, 120, 1, 'Strona główna'),
(435, 120, 2, 'Home page'),
(436, 120, 3, 'Homepage'),
(437, 120, 4, 'Главная'),
(438, 121, 1, 'Ilość aktualności odpowiadających wyszukiwanej frazie:'),
(439, 121, 2, 'Number of news matching search phrase:'),
(440, 121, 3, 'Die News, die der gesuchten Phrase entsprechen:'),
(441, 121, 4, 'Количество новостей, соответствующих искомой фразе:'),
(442, 122, 1, 'Ilość porad odpowiadających wyszukiwanej frazie:'),
(443, 122, 2, 'Ilość porad odpowiadających wyszukiwanej frazie:'),
(444, 122, 3, 'Ilość porad odpowiadających wyszukiwanej frazie:'),
(445, 122, 4, 'Ilość porad odpowiadających wyszukiwanej frazie:'),
(446, 123, 1, 'Ilość galerii odpowiadających wyszukiwanej frazie:'),
(447, 123, 2, 'Number of gallery corresponding search phrase:'),
(448, 123, 3, 'Die Galerien, die der gesuchten Phrase entsprechen:'),
(449, 123, 4, 'Количество галерей, соответствующих искомой фразе:'),
(450, 124, 1, 'Ilość podstron odpowiadających wyszukiwanej frazie:'),
(451, 124, 2, 'Number of pages corresponding search phrase:'),
(452, 124, 3, 'Die Unterseiten, die der gesuchten Phrase entsprechen:'),
(453, 124, 4, 'Количество страниц, соответствующих искомой фразе:'),
(454, 125, 1, 'Informacja'),
(455, 125, 2, 'Information'),
(456, 125, 3, 'Information'),
(457, 125, 4, 'Информация'),
(458, 126, 1, 'Jestem już zarejestrowany'),
(459, 126, 2, 'I am already registered'),
(460, 126, 3, 'Ich bin bereits registriert'),
(461, 126, 4, 'Я уже зарегистрирован'),
(462, 127, 1, 'Podaj login do swojego konta'),
(463, 127, 2, 'Enter the login to your account'),
(464, 127, 3, 'Benutzername für das Konto angeben'),
(465, 127, 4, 'Введите логин своего аккаунта'),
(466, 128, 1, 'Kontakt'),
(467, 128, 2, 'Contact'),
(468, 128, 3, 'Kontakt'),
(469, 128, 4, 'Контакты'),
(470, 129, 1, 'Kliknij tutaj'),
(471, 129, 2, 'Click here'),
(472, 129, 3, 'Klicken Sie hier'),
(473, 129, 4, 'Нажмите здесь'),
(474, 130, 1, 'Zalogowany'),
(475, 130, 2, 'Logged in'),
(476, 130, 3, 'Angemeldet'),
(477, 130, 4, 'Пользователь online'),
(478, 131, 1, 'Zostałeś zalogowany do naszego systemu. Możesz przejść na <a href=\"BASE_URL/\" title=\"strona główna\">stronę główną</a> lub powrócić do poprzednio przeglądanej strony klikając'),
(479, 131, 2, 'You have been logged into our system. You can go to <a href=\"BASE_URL/\" title=\"home page\">home page</a> or return to the previous page by clicking the'),
(480, 131, 3, 'Sie wurden in unserem System angemeldet. Sie können zur <a href=\"BASE_URL/\" title=\"strona g3ówna\">Homepage</a> übergehen oder zur vorherigen Seite zurückgehen durchs Klicken auf'),
(481, 131, 4, 'Вы вошли в систему. Можете перейти на <a href=\"BASE_URL/\" title=\"strona główna\">главную</a> или на <a href=\"BASE_URL/salony-zarzadzaj.html\">управление своим салоном </a>.'),
(482, 132, 1, 'Wylogowany'),
(483, 132, 2, 'Logged out'),
(484, 132, 3, 'Abgemeldet'),
(485, 132, 4, 'Пользователь offline'),
(486, 133, 1, 'Zostałeś wylogowany z sytemu. Możesz przejść na <a href=\"BASE_URL/\" title=\"strona główna\">stronę główną</a> lub do <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">strony logowania</a>.'),
(487, 133, 2, 'You have been signed out of the system. You can go to <a href=\"BASE_URL/\" title=\"home page\">home page</a> or <a href=\"BASE_URL/logowanie.html\" title=\"login page\">login page</a>.'),
(488, 133, 3, 'Sie wurden abgemeldet. Sie können zur <a href=\"BASE_URL/\" title=\"strona g3ówna\">Homepage</a> übergehen oder zur <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">Anmeldeseite </a> zurückkommen.'),
(489, 133, 4, 'Вы Вышли из системы. Можете перейти на <a href=\"BASE_URL/\" title=\"strona główna\">главную</a> или на <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">вход в систему </a>.'),
(490, 134, 1, 'Logowanie użytkownika'),
(491, 134, 2, 'User Login'),
(492, 134, 3, 'Benutzer anmelden'),
(493, 134, 4, 'Вход пользователя'),
(494, 135, 1, 'Użytkownik wylogowany'),
(495, 135, 2, 'The user logged out'),
(496, 135, 3, 'Benutzer abgemeldet'),
(497, 135, 4, 'Пользователь покинул сайт'),
(498, 136, 1, 'Zaloguj'),
(499, 136, 2, 'Login'),
(500, 136, 3, 'Anmelden'),
(501, 136, 4, 'Войти на сайт'),
(502, 137, 1, 'Wyloguj'),
(503, 137, 2, 'Logout'),
(504, 137, 3, 'Abmelden'),
(505, 137, 4, 'Выйти'),
(506, 138, 1, 'Aktualności'),
(507, 138, 2, 'News'),
(508, 138, 3, 'News'),
(509, 138, 4, 'Новости'),
(510, 139, 1, 'Biuletyn informacyjny'),
(511, 139, 2, 'Newsletter'),
(512, 139, 3, 'Newsletter'),
(513, 140, 1, 'Jeśli chcesz zrezygnować z subskrypcji kliknij proszę na poniższy link:'),
(514, 140, 2, 'Jeśli chcesz zrezygnować z subskrypcji kliknij proszę na poniższy link:'),
(515, 140, 3, 'Jeśli chcesz zrezygnować z subskrypcji kliknij proszę na poniższy link:'),
(516, 140, 4, 'Jeśli chcesz zrezygnować z subskrypcji kliknij proszę na poniższy link:'),
(517, 141, 1, 'Nowe konto'),
(518, 141, 2, 'New Account'),
(519, 141, 3, 'Neues Konto'),
(520, 142, 1, 'Przypomnienie hasła'),
(521, 142, 2, 'Password reminder'),
(522, 142, 3, 'Kennworterinnerung'),
(523, 142, 4, 'Восстановление пароля'),
(524, 143, 1, 'Nowy użytkownik'),
(525, 143, 2, 'New user'),
(526, 143, 3, 'Neuer Benutzer'),
(527, 144, 1, 'Brak podstron w bazie!'),
(528, 144, 2, 'No sub in the database!'),
(529, 144, 3, 'Keine Unterseiten in der Datenbank!'),
(530, 145, 1, 'Przepraszamy, ale strona o podanej nazwie nie istnieje.'),
(531, 145, 2, 'Sorry, the page with the given name does not exist.'),
(532, 145, 3, 'Entschuldigung, die angegebene Seite existiert nicht.'),
(533, 145, 4, 'Извините, данная страница отсутствует.'),
(534, 146, 1, 'Brak porad w serwisie.'),
(535, 146, 2, 'No faq on the site.'),
(536, 146, 3, 'Keine Faq auf der Website.'),
(537, 146, 4, 'Нет советов на сайте.'),
(538, 147, 1, 'Nie utworzono jeszcze galerii.'),
(539, 147, 2, 'No galleries have been created.'),
(540, 147, 3, 'Die Galerie wurde noch nicht erstellt.'),
(541, 147, 4, 'Галерея еще не создана.'),
(542, 148, 1, 'Brak aktualności w serwisie.'),
(543, 148, 2, 'No news on the site.'),
(544, 148, 3, 'Keine News in dem Service.'),
(545, 148, 4, 'Новости на сервере отсутствуют.'),
(546, 149, 1, 'Wystąpił błąd podczas wysyłania wiadomości!'),
(547, 149, 2, 'An error occurred while sending a message!'),
(548, 149, 3, 'Es ist ein Fehler beim Senden der Nachricht aufgetreten!'),
(549, 149, 4, 'Ошибка при отправке сообщения!'),
(550, 150, 1, 'Operacja wykonana prawidłowo.'),
(551, 150, 2, 'The operation performed correctly.'),
(552, 150, 3, 'Die Operation korrekt durchgeführt.'),
(553, 150, 4, 'Операция выполнена правильно.'),
(554, 151, 1, 'Strony'),
(555, 151, 2, 'Pages'),
(556, 151, 3, 'Webseiten'),
(557, 151, 4, 'Страницы'),
(558, 152, 1, 'Zmiana hasła'),
(559, 152, 2, 'Change password'),
(560, 152, 3, 'Ändern eines Passwortes'),
(561, 152, 4, 'Изменить пароль'),
(562, 153, 1, 'podaj swój e-mail'),
(563, 153, 2, 'Enter your e-mail'),
(564, 153, 3, 'geben Sie Ihre E-Mail an'),
(565, 153, 4, 'введите свой  e-mail'),
(566, 154, 1, 'Poprawny kod pocztowy: xx-xxx'),
(567, 154, 2, 'The correct zip code: xx-xxx'),
(568, 154, 3, 'Korrekte Postleitzahl: xx-xxx'),
(569, 154, 4, 'Правильный почтовый индекс: хх-ххх'),
(570, 155, 1, 'Drukuj'),
(571, 155, 2, 'Print'),
(572, 155, 3, 'Ausdrucken'),
(573, 155, 4, 'Распечатать'),
(574, 156, 1, 'Nie mogę przekierować URL - nie podano adresu!'),
(575, 156, 2, 'I can not redirect the URL - the address is not given!'),
(576, 156, 3, 'Ich kann den URL nicht umleiten - keine Adresse angegeben!'),
(577, 157, 1, 'Rejestracja'),
(578, 157, 2, 'Registration'),
(579, 157, 3, 'Registrieren'),
(580, 158, 1, 'Rejestr zmian'),
(581, 158, 2, 'ChangeLog'),
(582, 158, 3, 'Änderungsregister'),
(583, 159, 1, 'Rejestracja nowego użytkownika'),
(584, 159, 2, 'Register new user'),
(585, 159, 3, 'Neuen Benutzer registrieren'),
(586, 159, 4, 'Регистрация нового пользователя'),
(587, 160, 1, 'Rejestracja w systemie daje dostęp do niektórych zabezpieczonych stron.'),
(588, 160, 2, 'Register in the system gives access to some secured sites.'),
(589, 160, 3, 'Registrierung in unserem System erlaubt den Zugang zu manchen geschützten Seiten.'),
(590, 161, 1, 'Przypomnienie hasła'),
(591, 161, 2, 'password reminder'),
(592, 161, 3, 'Kennworterinnerung'),
(593, 161, 4, 'Восстановление пароля'),
(594, 162, 1, 'Aby zmienić hasło należy wypełnić wszystkie pola poniższego formularza'),
(595, 162, 2, 'To change your password by filling out the form below, all fields'),
(596, 162, 3, 'Um Kennwort zu ändern, müssen alle Felder der untenstehenden Formulars ausgefüllt werden'),
(597, 162, 4, 'Для изменения пароля заполните все поля нижеследующего формуляра'),
(598, 163, 1, 'Zapisz zmiany'),
(599, 163, 2, 'Save Changes'),
(600, 163, 3, 'Änderungen speichern'),
(601, 163, 4, 'Сохранить изменения'),
(602, 164, 1, 'Dziękujemy! Twoja wiadomość została wysłana.'),
(603, 164, 2, 'Thank you! Your message has been sent.'),
(604, 164, 3, 'Vielen Dank! Ihre Nachricht wurde gesendet.'),
(605, 164, 4, 'Спасибо! Ваше сообщение отправлено.'),
(606, 165, 1, 'Mapa strony'),
(607, 165, 2, 'Site map'),
(608, 165, 3, 'Sitemap'),
(609, 165, 4, 'Карта сайта'),
(610, 166, 1, 'Szukaj'),
(611, 166, 2, 'Search'),
(612, 166, 3, 'Suchen'),
(613, 166, 4, 'Поиск'),
(614, 167, 1, 'Brak wyników wyszukiwania!'),
(615, 167, 2, 'Brak wyników wyszukiwania!'),
(616, 167, 3, 'Keine Suchergebnisse vorhanden!'),
(617, 167, 4, 'Результаты поиска отсутствуют!'),
(618, 168, 1, 'Słowo wyszukiwane:'),
(619, 168, 2, 'Słowo wyszukiwane:'),
(620, 168, 3, 'Gesuchtes Wort:'),
(621, 168, 4, 'Искомое слово:'),
(622, 169, 1, 'Wyniki wyszukiwania'),
(623, 169, 2, 'Search Results'),
(624, 169, 3, 'Suchergebnisse:'),
(625, 169, 4, 'Результаты поиска'),
(626, 170, 1, 'Rezygnacja z biuletynu'),
(627, 170, 2, 'The abandonment of the newsletter'),
(628, 170, 3, 'Auf Newsletter verzichten'),
(629, 171, 1, 'Przepraszamy. Trwa aktualizacja danych.'),
(630, 171, 2, 'Sorry. Updating the data.'),
(631, 171, 3, 'Entschuldigung. Daten werden aktualisiert.'),
(632, 171, 4, 'Извините, идет обновление данных.'),
(633, 172, 1, 'Dane użytkownika'),
(634, 172, 2, 'User'),
(635, 172, 3, 'Benutzerdaten'),
(636, 172, 4, 'Данные пользователя'),
(637, 173, 1, 'Witaj'),
(638, 173, 2, 'Welcome'),
(639, 173, 3, 'Wilkommen'),
(640, 173, 4, 'Мы приветствуем Вас'),
(641, 174, 1, 'wpisz szukaną frazę...'),
(642, 174, 2, 'please enter a search phrase ...'),
(643, 174, 3, 'gesuchte Phrase eingeben...'),
(644, 174, 4, 'введите искомую фразу...'),
(645, 175, 1, 'Wyszukaj'),
(646, 175, 2, 'search it out in service'),
(647, 175, 3, 'Suchen'),
(648, 175, 4, 'Поиск'),
(649, 176, 1, 'wyszukiwanie zawansowane ...'),
(650, 176, 2, 'searching out advanced ...'),
(651, 176, 3, 'erweiterte Suche ...'),
(652, 176, 4, 'расширенный поиск...'),
(653, 177, 1, 'Zobacz również:'),
(654, 177, 2, 'See also:'),
(655, 177, 3, 'Siehe auch:'),
(656, 177, 4, 'Смотрите также:'),
(657, 178, 1, 'Konto zostało aktywowane! Dziękujemy!'),
(658, 178, 2, 'The account has been activated! Thank you!'),
(659, 178, 3, 'Das Konto wurde aktiviert! Danke!'),
(660, 179, 1, 'Aktywacja konta na stronie'),
(661, 179, 2, 'Activating your account'),
(662, 179, 3, 'Kontoaktivierung auf der Webseite'),
(663, 180, 1, 'Wystąpił błąd podczas zakładania konta.'),
(664, 180, 2, 'There was an error in the account.'),
(665, 180, 3, 'Bei der Erstellung des Kontos ist ein Fehler aufgetreten.'),
(666, 181, 1, 'Podane hasło w obu polach musi być identyczne!'),
(667, 181, 2, 'Given password must be identical in both fields!'),
(668, 181, 3, 'Das in beiden Feldern angegebenen Kennwörter müssen identisch sein!'),
(669, 182, 1, 'Podany adres e-mail jest nieprawidłowy!'),
(670, 182, 2, 'E-mail is incorrect!'),
(671, 182, 3, 'Die angegebene E-Mail-Adresse ist ungültig!'),
(672, 183, 1, 'Wystąpił błąd podczas generowania hasła, wypełnij ponownie formularz.'),
(673, 183, 2, 'During password generating error has occured, please fill in the form again.'),
(674, 183, 3, 'Bei der Generierung des neuen Kennwortes ist ein Fehler aufgetreten, füllen Sie erneut das Formular aus.'),
(675, 184, 1, 'Podany identyfikator jest nieprawidłowy! Konto nie zostało aktywowane!'),
(676, 184, 2, 'Given identifier is incorrect! Account has not been activated!'),
(677, 184, 3, 'Das angegebene ID ist ungültig! Das Konto wurde nicht aktiviert!'),
(678, 184, 4, 'Введен неправильный идентификатор! Аккаунт не активирован!'),
(679, 185, 1, 'Podany identyfikator jest nieprawidłowy! Konto nie zostało usunięte!'),
(680, 185, 2, 'Given identifier is incorrect! Account has not been removed.'),
(681, 185, 3, 'Die angegebene ID ist ungültig! Das Konto wurde nicht gelöscht!'),
(682, 185, 4, 'Введен неправильный идентификатор! Аккаунт не удален!'),
(683, 186, 1, 'Podany login jest niepoprawny. Prawidłowy login może składać się z liter lub cyfr oraz podkreślenia \"_\", powinien mieć długość conajmniej 3 znaków.'),
(684, 186, 2, 'The given login name is incorrect. The correct login name can consist of letters or digits and the underline \"_\", should have the length of at least 3 signs.'),
(685, 186, 3, 'Der angegebene Benutzername ist ungültig. Ein gültiger Benutzername soll aus Buchstaben und einem Unterstrich bestehen\"_\", soll mindestens 3 Zeichen enthalten.'),
(686, 187, 1, 'Stare hasło jest nieprawidłowe!'),
(687, 187, 2, 'Old password is incorrect!'),
(688, 187, 3, 'Das alte Kennwort ist ungültig!'),
(689, 188, 1, 'Niepoprawne hasło.'),
(690, 188, 2, 'Incorrect password.'),
(691, 188, 3, 'Kennwort ungültig.'),
(692, 188, 4, 'Неправильный пароль.'),
(693, 189, 1, 'Podany adres e-mail w obu polach musi być identyczny!'),
(694, 189, 2, 'Given email must be identical in both fields!'),
(695, 189, 3, 'Die in beiden Feldern angegebene E-Mail-Adresse muss identisch sein!'),
(696, 190, 1, 'Przepraszamy, ale podane login i hasło nie pasują do siebie! Jeżeli zapomniałeś hasła możesz użyć opcji <a class=\"blue\" href=\"BASE_URL/konto/przypomnienie-hasla.html\" title=\"przypomnienie hasła\">przypomnienie hasła</a>.'),
(697, 190, 2, 'Sorry, but given username and password do not match! If you have forgotten your password, you can use the <a class=\"blue\" href=\"BASE_URL/konto/przypomnienie-hasla.html\" title=\"password reminder\">password reminder</a>.'),
(698, 190, 3, 'Entschuldigung, der angegebene Benutzername und Kennwort passen nicht zueinander! Falls Sie Ihr Kennwort vergessen haben, benutzen Sie die Option <a class=\"blue\" href=\"BASE_URL/konto/przypomnienie-hasla.html\" title=\"przypomnienie has3a\">Kennworterinnerung</a>.'),
(699, 190, 4, 'Логин или пароль указаны неверно! Если Вы забыли пароль, воспользуйтесь услугой <a class=\"blue\" href=\"BASE_URL/konto/przypomnienie-hasla.html\" title=\"przypomnienie hasła\">восстановления пароля</a>.'),
(700, 191, 1, 'Poniżej można przejrzeć i/lub zmienić dane adresowe, które znajdują się w naszej bazie.'),
(701, 191, 2, 'Here you can view and/or change your address, which can be found in our database.'),
(702, 191, 3, 'Unten können Sie die in unserer Datenbank enthaltenen Adressdaten einsehen oder ändern.'),
(703, 192, 1, 'Możesz się teraz zalogować używając nowego hasła. Hasło można zmienić w panelu użytkownika po zalogowaniu.'),
(704, 192, 2, 'You can now log in by using new password. Password can be changed in user’s panel after logging in.'),
(705, 192, 3, 'Sie können sich jetzt mit dem neuen Kennwort anmelden. Das Kennwort kann im Benutzerpanel nach der Anmeldung geändert werden.'),
(706, 193, 1, 'Hasło do konta zostało zmienione!'),
(707, 193, 2, 'Password to the account has been changed.'),
(708, 193, 3, 'Das Kennwort für das Konto wurde geändert!'),
(709, 194, 1, 'Zmiany zostały zapisane.'),
(710, 194, 2, 'Changes in address data have been recorded.'),
(711, 194, 3, 'Die Änderungen wurden gespeichert.'),
(712, 194, 4, 'Изменения сохранены.'),
(713, 195, 1, 'Proszę zaznaczyć właściwe'),
(714, 195, 2, 'Type of the account appropriate'),
(715, 195, 3, 'Bitte korrekt markieren'),
(716, 196, 1, 'potwierdzenie rejestracji'),
(717, 196, 2, 'registration confirmation'),
(718, 196, 3, 'Bestätigung der Anmeldung'),
(719, 197, 1, 'Wybrane konto zostało usunięte!'),
(720, 197, 2, 'Your account has been removed!'),
(721, 197, 3, 'Das gewählte Konto wurde gelöscht!'),
(722, 197, 4, 'Выбранный аккаунт удален!'),
(723, 198, 1, 'Podany adres e-mail jest już zarejestrowany!'),
(724, 198, 2, 'Given e-mail has already been registered!'),
(725, 198, 3, 'Die angegebene E-Mail-Adresse wurde bereits registriert!'),
(726, 199, 1, 'Proszę wypełnić wszystkie pola!'),
(727, 199, 2, 'Please fill in all fields!'),
(728, 199, 3, 'Bitte füllen Sie alle Felder aus!'),
(729, 199, 4, 'Заполните, пожалуйста, все поля!'),
(730, 200, 1, 'Pole MIEJSCOWOŚĆ nie może być puste!'),
(731, 200, 2, 'Please give city.'),
(732, 200, 3, 'Das Feld ORT kann nicht leer sein!'),
(733, 201, 1, 'Proszę podać nazwę firmy.'),
(734, 201, 2, 'Please give firm name.'),
(735, 201, 3, 'Geben Sie bitte den Firmennamen ein.'),
(736, 202, 1, 'Pole NAZWISKO nie może być puste!'),
(737, 202, 2, 'Please give last name.'),
(738, 202, 3, 'Das Feld NAME kann nicht leer sein!'),
(739, 203, 1, 'Pole IMIĘ nie może być puste!'),
(740, 203, 2, 'Please give first name.'),
(741, 203, 3, 'Das Feld VORNAME kann nicht leer sein!'),
(742, 204, 1, 'Jeżeli chcesz założyć konto firmowe musisz podać poprawny numer NIP!'),
(743, 204, 2, 'If you want to sign up for your company, you must enter a valid VAT number!'),
(744, 204, 3, 'Falls Sie ein Firmenkonto erstellen möchten, müssen Sie eine korrekte USt-IdNr. angeben!'),
(745, 205, 1, 'Pole NR BUDYNKU nie może być puste!'),
(746, 205, 2, 'Please give no home!'),
(747, 205, 3, 'Das Feld HAUSNUMMER kann nicht leer sein!'),
(748, 206, 1, 'Podany kod pocztowy jest nieprawidłowy!'),
(749, 206, 2, 'The postal code is invalid!'),
(750, 206, 3, 'Die angegebene Postleitzahl ist ungültig!'),
(751, 207, 1, 'Pole ULICA nie może być puste!'),
(752, 207, 2, 'Please give street!'),
(753, 207, 3, 'Das Feld STRAßE kann nicht leer sein!'),
(754, 208, 1, 'firma'),
(755, 208, 2, 'company'),
(756, 208, 3, 'Firma'),
(757, 209, 1, 'Nazwa firmy:'),
(758, 209, 2, 'Firm name:'),
(759, 209, 3, 'Firmenname:'),
(760, 210, 1, 'System wygenerował nowe hasło. Potwierdzenie i instrukcje jak je aktywować wysłano na adres:'),
(761, 210, 2, 'System has generated new password. E-amil acknowledge and informations on activate have been sent:'),
(762, 210, 3, 'Das System hat ein neues Kennwort generiert. Die Bestätigung und Aktivierungsanleitung wurden auf folgende E-Mail-Adresse verschickt:'),
(763, 211, 1, 'Nowa grupa została dodana!'),
(764, 211, 2, 'The new group has been added!'),
(765, 211, 3, 'Neue Gruppe wurde hinzugefügt!'),
(766, 212, 1, 'Wystąpił błąd podczas dodawania uprawnień dla nowej grupy!'),
(767, 212, 2, 'An error occurred while adding power to a new group!'),
(768, 212, 3, 'Es ist ein Fehler beim Hinzufügen der Berechtigungen für neue Gruppe aufgetreten!'),
(769, 213, 1, 'Wybrana grupa została skasowana!'),
(770, 213, 2, 'The selected group is deleted!'),
(771, 213, 3, 'Die gewählte Gruppe wurde gelöscht!'),
(772, 214, 1, 'Grupa została skasowana, ale nie zaktualizowano tabeli użytkowników!'),
(773, 214, 2, 'The group was deleted but not updated the table of users!'),
(774, 214, 3, 'Die Gruppe wurde gelöscht, aber die Benutzertabelle wurde nicht aktualisiert!'),
(775, 215, 1, 'Grupa o podanej nazwie już istnieje!'),
(776, 215, 2, 'The group with the given name already exists!'),
(777, 215, 3, 'Der angegebene Gruppenname existiert bereits!'),
(778, 216, 1, 'Wystąpił błąd podczas kasowania grupy!'),
(779, 216, 2, 'An error occurred while deleting the group!'),
(780, 216, 3, 'Es ist ein Fehler beim Löschen der Gruppe aufgetreten!'),
(781, 217, 1, 'Aby się zalogować należy podać login i hasło!'),
(782, 217, 2, 'To log on give the login name and the password!'),
(783, 217, 3, 'Um sich anzumelden, muss man Benutzernamen und Kennwort eingeben!'),
(784, 217, 4, 'Для входа в систему введите логин и пароль!'),
(785, 218, 1, 'Konto zostało pomyślnie utworzone, pozostaje jednak nieaktywne. Na podany adres e-mail wysłano instrukcje dotyczące aktywacji konta. Dopiero po aktywacji będzie można się zalogować.'),
(786, 218, 2, 'Instructions for account activity have been sent to the given e-mail. Only after making account activity you will be able to log in.'),
(787, 218, 3, 'Das Konto wurde erfolgreich erstellt, bleibt jedoch inaktiv. Auf die angegebene E-Mail-Adresse wurde die Anleitung der Kontoaktivierung verschickt. Erst nach der Aktivierung können Sie sich anmelden.'),
(788, 219, 1, 'Konto zostało pomyślnie utworzone.'),
(789, 219, 2, 'Your account has been successfully created.'),
(790, 219, 3, 'Das Konto wurde erfolgreich erstellt.'),
(791, 220, 1, 'Minimum 5 znaków'),
(792, 220, 2, 'Minimum 5 symbols'),
(793, 220, 3, 'Mindestens 5 Zeichen'),
(794, 221, 1, 'Nazwa konta / użytkownika'),
(795, 221, 2, 'Account Name / users'),
(796, 221, 3, 'Kontoname / Benutzername'),
(797, 222, 1, '<b>Witaj!</b><br />Ktoś (prawdopodobnie Ty) użył opcji przypomnienie hasła dla Twojego konta.<br />System wygenerował nowe hasło:'),
(798, 222, 2, '<b>Welcome!</b><br />Someone (probably you) has used password reminder option to your account. System has generated new password:'),
(799, 222, 3, '<b>Herzlich wilkommen!</b><br />Jemand (höchstwahrscheinlich Sie) haben die Option der Kennworterinnerung für Ihr Konto benutzt.<br />Das System hat ein neues Kennwort generiert:'),
(800, 222, 4, '<b>Здравствуйте!</b><br />Кто-то (скорей всего Вы) воспользовался услугой восстановления пароля Вашего аккаунта.<br /> Система создала новый пароль:'),
(801, 223, 1, 'Numer NIP:'),
(802, 223, 2, 'Number NIP:'),
(803, 223, 3, 'NIP-Nummer (USt-IdNr.):'),
(804, 224, 1, 'Twoje konto nie jest jeszcze aktywne. Aby potwierdzić rejestrację, a tym samym aktywować konto postępuj zgodnie z instrukcjami wysłanymi na podany podczas rejestracji adres e-mail.<br />Jeżeli w ciągu godziny nie dostarczono na Twój e-mail instrukcji skontaktuj się z <a href=\"mailto:ADMIN_EMAIL\">administratorem</a>.'),
(805, 224, 2, 'Your account has not been active yet.<br />In order to confirm registration, and in the same way activate your account, follow the instructions which have been sent to your e-mail during logging.<br />If within an hour instructions are not delivered to your e-mail, please contact the <a href=\"mailto:ADMIN_EMAIL\">admin</a>.'),
(806, 224, 3, 'Ihr Konto ist noch nicht aktiv. Um die Registrierung zu bestätigen und das Konto zu aktivieren, gehen Sie gemäß Anleitungen vor.<br />Falls innerhalb von einer Stunde keine Anleitung auf Ihre E-Mail-Adresse zugeschickt wird, kontaktieren Sie den <a href=\"mailto:ADMIN_EMAIL\">Administrator</a>.'),
(807, 225, 1, 'Brak uprawnień aby logować się do panelu administracyjnego!'),
(808, 225, 2, 'There are no powers to sign in to the administration panel!'),
(809, 225, 3, 'Sie haben keine Berechtigungen, um sich im Admin-Panel anzumelden!'),
(810, 225, 4, 'У Вас нет доступа в панель администратора!'),
(811, 226, 1, 'Użytkownik o podanym loginie nie jest zarejestrowany w naszej bazie! Aby założyć nowe konto możesz użyć opcji <a class=\"blue\" href=\"BASE_URL/konto/rejestracja.html\" title=\"rejestracja\">rejestracja</a>.'),
(812, 226, 2, 'A user with the login is not registered in our database! To create a new account, you can use the <a class=\"blue\" href=\"BASE_URL/konto/rejestracja.html\" title=\"new account\">new account</a>.'),
(813, 226, 3, 'Der angegebene Benutzername existiert bereits in unserer Datenbank! Um ein neues Benutzerkonto zu erstellen, verwende die Option <a class=\"blue\" href=\"BASE_URL/konto/rejestracja.html\" title=\"rejestracja\">Registrierung</a>.'),
(814, 226, 4, 'Пользователь с таким логином не зарегистрирован в нашей системе! Чтобы стать пользователем, воспользуйтесь услугой <a class=\"blue\" href=\"BASE_URL/konto/rejestracja.html\" title=\"rejestracja\">регистрация</a>.'),
(815, 227, 1, 'Wystąpił błąd podczas zapisywania zmian!'),
(816, 227, 2, 'An error occurred while saving the changes!'),
(817, 227, 3, 'Es ist ein Fehler beim Speichern der Änderungen aufgetreten!'),
(818, 228, 1, 'Konto nieaktywne.'),
(819, 228, 2, 'Account inactive.'),
(820, 228, 3, 'Konto inaktiv.'),
(821, 228, 4, 'Аккаунт неактивен.'),
(822, 229, 1, 'Przepraszamy, ale nie jesteś zalogowany w naszym systemie. Aby się zalogować proszę skorzystać z <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">formularza logowania</a>. Jeżeli jeszcze nie masz konta możesz je łatwo założyć używając opcji <a href=\"BASE_URL/rejestracja.html\" title=\"rejestracja\">nowe konto</a>.<br /><br />'),
(823, 229, 2, 'We are sorry, but you are not logged in our system. In order to log in please use <a href=\"BASE_URL/konto/logowanie.html\">login form</a>.<br />If you have not the account yet, you can make it easily by using <a href=\"BASE_URL/konto/rejestracja.html\">new account</a> option.<br /><br />'),
(824, 229, 3, 'Entschuldigung, Sie sind in unserem System nicht angemeldet. Um sich anzumelden, nutzen Sie das <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">Anmeldeformular</a>. Falls Sie kein Konto haben, können sie es leicht mittels der Option <a href=\"BASE_URL/rejestracja.html\" title=\"Registrierung\">neues Konto</a>.<br /><br /> erstellen.'),
(825, 229, 4, 'Извините, но Вы не вошли в систему, чтобы войти воспользуйтесь услугой <a href=\"BASE_URL/logowanie.html\" title=\"logowanie\">входа в систему</a>. Если Вы еще не зарегистрированы, пожалуйста, пройдите регистрацию <a href=\"BASE_URL/rejestracja.html\" title=\"rejestracja\">регистрация</a>.<br /><br />.'),
(826, 230, 1, 'Nie masz uprawnień do oglądania tej strony!'),
(827, 230, 2, 'You do not have permission to view this page!'),
(828, 230, 3, 'Sie haben nicht die Berechtigung, diese Seite zu betreten!'),
(829, 230, 4, 'У Вас нет прав просматривать эту страницу!'),
(830, 231, 1, 'Brak użytkownika w bazie.'),
(831, 231, 2, 'There are no user in database.'),
(832, 231, 3, 'Kein Benutzer in der Datenbank.'),
(833, 231, 4, 'Пользователь отсутствует в системе.'),
(834, 232, 1, 'osoba fizyczna'),
(835, 232, 2, 'person'),
(836, 232, 3, 'natürliche Person'),
(837, 233, 1, 'Nowe uprawnienie zostało dodane!'),
(838, 233, 2, 'The new rating is added!'),
(839, 233, 3, 'Neue Berechtigung wurde hinzugefügt!'),
(840, 234, 1, 'Uprawnienie zostało usunięte z bazy!'),
(841, 234, 2, 'The rating was removed from the database!'),
(842, 234, 3, 'Die Berechtigung wurde von der Datenbank gelöscht!'),
(843, 235, 1, 'Uprawnienie zostało skasowane, jednak wystąpił błąd podczas cofania uprawnienia dla grup!'),
(844, 235, 2, 'The power was gone, but there is an error when revoking permissions for groups!'),
(845, 235, 3, 'Die Berechtigung wurde gelöscht, es ist jedoch ein Fehler beim Zurücksetzen der Berechtigung für die Gruppen aufgetreten!'),
(846, 236, 1, 'Uprawnienie o podanej nazwie już istnieje!'),
(847, 236, 2, 'The power with the given name already exists!'),
(848, 236, 3, 'Der angegebene Berechtigungsname existiert bereits!'),
(849, 237, 1, 'Wystąpił błąd podczas zapisywania uprawnienia w bazie!'),
(850, 237, 2, 'An error occurred while saving power in the database!'),
(851, 237, 3, 'Es ist ein Fehler beim Speichern der Berechtigung in der Datenbank aufgetreten!'),
(852, 238, 1, 'Wystąpił błąd podczas kasowania uprawnienia!'),
(853, 238, 2, 'An error occurred while erasing the power!'),
(854, 238, 3, 'Es ist ein Fehler beim Löschen der Berechtigung aufgetreten!'),
(855, 239, 1, 'Użytkownik o podanym loginie już jest zarejestrowany!'),
(856, 239, 2, 'User with the given login has already been registered!'),
(857, 239, 3, 'Ein Benutzer mit dem angegebenen Benutzernamen ist bereits registriert!'),
(858, 240, 1, 'Po poprawnym wypełnieniu formularza powinna pojawić się informacja o utworzeniu konta użytkownika i wysłaniu na maila kodu aktywacyjnego do tego konta. W przypadku braku informacji potwierdzającej poprawne utworzenie konta, prosimy ponownie dokładnie wypełnić formularz.'),
(859, 240, 2, 'After the correct filling in the form the information about creating the users account should appear and activation code should be sent to this account. In case of the lack of confirming information, please fill in the form precisely.'),
(860, 240, 3, 'Nach einem korrekten Ausfüllen des Formulars soll Info über die Erstellung des Benutzerkontos und Verschicken des Aktivierungscodes für dieses Konto auf die E-Mail-Adresse- erscheinen. Beim Fehlen einer Bestätigung der korrekten Kontoerstellung füllen Sie bitte das Formular genau noch einmal.'),
(861, 241, 1, 'Przypomnienie hasła'),
(862, 241, 2, 'Password reminder'),
(863, 241, 3, 'Kennworterinnerung'),
(864, 242, 1, 'rezygnacja z konta'),
(865, 242, 2, 'resignation from the account'),
(866, 242, 3, 'Verzicht auf das Konto'),
(867, 243, 1, 'Podane nowe hasło jest za krótkie! Hasło powinno mieć conajmniej 5 znaków.'),
(868, 243, 2, 'Given password is too short. The password should have at least 5 marks.'),
(869, 243, 3, 'Das neue Kennwort ist zu kurz! Das Kennwort muss mindestens 5 Zeichen haben.'),
(870, 244, 1, 'Pola oznaczone czerwoną gwiazdką  <span class=\"important\">*</span> są obowiązkowe.'),
(871, 244, 2, 'Fields with red star <span class=\"important\">*</span> are obligatory.'),
(872, 244, 3, 'Die mit rotem Stern gekennzeichneten Felder <span class=\"important\">*</span> sind Pflichtfelder.'),
(873, 244, 4, 'Поля, отмеченные красной звездочкой <span class=\"important\">*</span>, обязательные для заполнения.'),
(874, 245, 1, 'Typ konta'),
(875, 245, 2, 'Type acount'),
(876, 245, 3, 'Kontotyp'),
(877, 246, 1, 'Wymagany do wystawienia faktury VAT!'),
(878, 246, 2, 'VAT required for issuing an invoice!'),
(879, 246, 3, 'Wird für die Ausstellung der MwSt.-Rechnung benötigt!'),
(880, 247, 1, 'Użytkownik o podanym loginie nie jest zarejestrowany w naszej bazie! Czy chodziło Ci o:'),
(881, 247, 2, ''),
(882, 247, 3, ''),
(883, 247, 4, '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tc_stats`
--

CREATE TABLE `tc_stats` (
  `id` int(20) NOT NULL,
  `data_add` int(10) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `hostname` varchar(150) NOT NULL,
  `city` varchar(30) NOT NULL,
  `region` varchar(50) NOT NULL,
  `loc` varchar(50) NOT NULL,
  `org` varchar(255) NOT NULL,
  `url_link` varchar(255) NOT NULL,
  `http_user_agent` varchar(255) NOT NULL,
  `typ_urzadzenia` varchar(255) NOT NULL,
  `count_unit` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `tc_stats`
--

INSERT INTO `tc_stats` (`id`, `data_add`, `ip`, `hostname`, `city`, `region`, `loc`, `org`, `url_link`, `http_user_agent`, `typ_urzadzenia`, `count_unit`) VALUES
(1, 1467625932, '::1', '', '', '', '', '', 'http://localhost/cms5/', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36', 'computer', 15),
(2, 1501669855, '91.189.216.27', '', '', '', '', '', 'http://www.cms-test.technetium.com.pl/', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36', 'computer', 8),
(3, 1501741124, '91.189.216.27', '', '', '', '', '', 'http://www.cms-test.technetium.com.pl/', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36', 'computer', 0),
(4, 1505462361, '::1', '', '', '', '', '', 'http://localhost/cms_instalka/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36', 'computer', 0),
(5, 1511178236, '::1', '', '', '', '', '', 'http://localhost/cms_instalka/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36', 'computer', 23);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `business` tinyint(1) NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `nr_bud` varchar(10) NOT NULL,
  `nr_lok` varchar(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `post_code` varchar(6) NOT NULL,
  `nip` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `admin_login` tinyint(1) NOT NULL,
  `group_id` int(11) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `alternate_login` varchar(1024) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `business`, `firm_name`, `first_name`, `last_name`, `street`, `nr_bud`, `nr_lok`, `city`, `post_code`, `nip`, `email`, `phone`, `active`, `admin_login`, `group_id`, `discount`, `admin`, `alternate_login`) VALUES
(1, 'mateusz@technetium.pl', 'sha1:64000:18:Fpyay2dlW4F3mwzsEL9NEAFeFWvED0Nn:ZAHDDsES0Vs4hXVYaGyW3Rei', 1, 'Technetium', 'Mateusz', 'Wiktor', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'mateusz@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(2, 'biuro@technetium.pl', 'sha1:64000:18:FlytdcGpFCtZKPbn6wUMdg85GlEJ8LaY:xab5Tg2pitjPVZISnXPsWeDZ', 1, 'Technetium', 'Ania', 'Wiktor', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'biuro@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(3, 'kamil@technetium.pl', 'sha1:64000:18:ll3GYdeSKR+LdxrdDLhjN7+njXkJ+sOV:cHTTENfDr2j/x73r66fGkaWd', 0, 'Technetium', 'Kamil', 'Zawada', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'kamil@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(7, 'anna.malec@technetium.pl', 'sha1:64000:18:NMqYBMLYj0thS+8SP/q5cDMzs4UEOT2N:fAhKaNIx8QPNrl6IePwmsriR', 0, 'Technetium', 'Anna', 'Malec', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'anna.malec@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(18, 'nikol.wojdacz@technetium.pl', 'sha1:64000:18:GgfBLHxD+Z3IZgvsd39Gf6Y81niScICE:D4oODVSImJLKblkR5yfCZcg5', 0, 'Technetium', 'Nikol', 'Wojdacz', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'nikol.wojdacz@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(17, 'karolina.ochmanek@technetium.pl', 'sha1:64000:18:JaVFkyRoN91s3jJhSnJ5QEH0OZBAff9H:rAYkhWqyVmRdSLI8pSiAfKVZ', 0, 'Technetium', 'Karolina', 'Ochmanek', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'karolina.ochmanek@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(16, 'lukasz.solecki@technetium.pl', 'sha1:64000:18:rdpD0xT5D+8amxDRRfEv+f0ggQfzZOFA:Lo/Rrcp56dv314mSUiHZGX1c', 0, 'Technetium', 'Łukasz', 'Solecki', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'lukasz.solecki@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(19, 'mariia.mykoliv@technetium.pl', 'sha1:64000:18:RhWRWDbfZeS9TkLXHTW70gnZJXnaw1Uv:ZAtMI2zLPRCmsN1EKZRGp/S8', 0, 'Technetium', 'Mariia', 'Mykoliv', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'mariia.mykoliv@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(20, 'bohdan.masliannykov@technetium.pl', 'sha1:64000:18:DFbIjZIBvhAs6OFwifd+G3CZI8UVWcMI:ikjswgb3o5Ty5dQ2eNyQL2Hx', 0, 'Technetium', 'Bohdan', 'Masliannykov', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'bohdan.masliannykov@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(21, 'wojciech.mastej@technetium.pl', 'sha1:64000:18:xWgPGqd99EIoQ5JDvCkaVi/UzigUsvET:jfBwZjenZyT+w+x1nvBmSTeb', 0, 'Technetium', 'Wojciech', 'Mastej', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'wojciech.mastej@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(22, 'piotr.bieszczad@technetium.pl', 'sha1:64000:18:hQBYvCYQu9Ob2fMeErFUf+H3yGsHAoVJ:ziqjt8aJZDDP6UcM9gDTwYI3', 0, 'Technetium', 'Piotr', 'Bieszczad', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'piotr.bieszczad@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL),
(23, 'patrycja.baran@technetium.pl', 'sha1:64000:18:u4OXoNZ/0CI28KyGh1u3DRg7kdEwtu93:SknjuiJPcaDfkijiwXr4x+m3', 0, 'Technetium', 'Patrycja', 'Baran', 'Goździkowa', '14A', '', 'Rzeszów', '35-604', '', 'patrycja.baran@technetium.pl', '', 1, 1, 1, '0.00', 1, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users_log`
--

CREATE TABLE `users_log` (
  `id` int(11) UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `date_add` datetime NOT NULL,
  `reason` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users_log`
--

INSERT INTO `users_log` (`id`, `login`, `pass`, `date_add`, `reason`, `host`) VALUES
(1, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:08:42', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(2, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:08:50', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(3, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:16:36', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(4, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:18:21', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(5, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:24:29', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(6, 'lukasz.solecki@technetium.pl', '******', '2017-11-20 14:24:42', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1'),
(7, 'lukasz.solecki@technetium.pl', '******', '2017-11-21 12:11:14', 'Konto nieaktywne.', 'DESKTOP-KGTCD0B IP:::1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users_zalogowani`
--

CREATE TABLE `users_zalogowani` (
  `id` int(11) UNSIGNED NOT NULL,
  `login` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `host` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `users_zalogowani`
--

INSERT INTO `users_zalogowani` (`id`, `login`, `first_name`, `last_name`, `user_id`, `group_id`, `date_add`, `host`) VALUES
(1, 'kamil@technetium.pl', 'Kamil', 'Zawada', 3, 1, '2016-07-04 11:52:38', 'TC-prog-TC-prog IP:::1'),
(2, 'tcjuras', 'Karol', 'Jurusik', 13, 1, '2017-08-02 12:31:05', 'ip-91.189.216.27.skyware.pl IP:91.189.216.27'),
(3, 'tcjuras', 'Karol', 'Jurusik', 13, 1, '2017-08-03 08:24:27', 'ip-91.189.216.27.skyware.pl IP:91.189.216.27'),
(4, 'mateusz@technetium.pl', 'Mateusz', 'Wiktor', 1, 1, '2017-11-20 13:22:36', 'DESKTOP-KGTCD0B IP:::1'),
(5, 'lukasz.solecki@technetium.pl', '', '', 15, 1, '2017-11-20 13:33:05', 'DESKTOP-KGTCD0B IP:::1'),
(6, 'lukasz.solecki@technetium.pl', 'Łukasz', 'Solecki', 16, 1, '2017-11-21 12:14:03', 'DESKTOP-KGTCD0B IP:::1'),
(7, 'lukasz.solecki@technetium.pl', 'Łukasz', 'Solecki', 16, 1, '2017-11-21 12:15:05', 'DESKTOP-KGTCD0B IP:::1'),
(8, 'lukasz.solecki@technetium.pl', 'Łukasz', 'Solecki', 16, 1, '2017-11-21 12:15:15', 'DESKTOP-KGTCD0B IP:::1'),
(9, 'patrycja.baran@technetium.pl', 'Patrycja', 'Baran', 23, 1, '2017-12-11 14:26:41', 'DESKTOP-KGTCD0B IP:::1'),
(10, 'patrycja.baran@technetium.pl', 'Patrycja', 'Baran', 23, 1, '2017-12-11 14:27:36', 'DESKTOP-KGTCD0B IP:::1'),
(11, 'patrycja.baran@technetium.pl', 'Patrycja', 'Baran', 23, 1, '2017-12-11 14:27:52', 'DESKTOP-KGTCD0B IP:::1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zmieniarka`
--

CREATE TABLE `zmieniarka` (
  `id` int(11) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `watermark` tinyint(1) NOT NULL DEFAULT '0',
  `watermark_file` varchar(2046) NOT NULL,
  `watermark_x` int(11) NOT NULL DEFAULT '0',
  `watermark_y` int(11) NOT NULL DEFAULT '0',
  `watermark_position` int(11) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `zmieniarka`
--

INSERT INTO `zmieniarka` (`id`, `label`, `width`, `height`, `watermark`, `watermark_file`, `watermark_x`, `watermark_y`, `watermark_position`, `active`) VALUES
(1, 'glowna', 800, 400, 0, '', 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zmieniarka_photo`
--

CREATE TABLE `zmieniarka_photo` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(1022) NOT NULL,
  `zmieniarka_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `zmieniarka_photo`
--

INSERT INTO `zmieniarka_photo` (`id`, `name`, `zmieniarka_id`, `order`) VALUES
(1, 'lilie-wodnejpg9649.jpg', 1, 1),
(2, 'niebieskie-goryjpg7701.jpg', 1, 2),
(3, 'zachod-sloncajpg8969.jpg', 1, 3),
(4, 'zimajpg4281.jpg', 1, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zmieniarka_photo_description`
--

CREATE TABLE `zmieniarka_photo_description` (
  `id` int(11) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(1022) NOT NULL,
  `content` varchar(2046) NOT NULL,
  `alt` varchar(1022) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `zmieniarka_photo_description`
--

INSERT INTO `zmieniarka_photo_description` (`id`, `parent_id`, `language_id`, `title`, `content`, `alt`) VALUES
(1, 1, 1, '', '', ''),
(2, 1, 2, '', '', ''),
(3, 1, 3, '', '', ''),
(4, 1, 4, '', '', ''),
(5, 2, 1, '', '', ''),
(6, 2, 2, '', '', ''),
(7, 2, 3, '', '', ''),
(8, 2, 4, '', '', ''),
(9, 3, 1, '', '', ''),
(10, 3, 2, '', '', ''),
(11, 3, 3, '', '', ''),
(12, 3, 4, '', '', ''),
(13, 4, 1, '', '', ''),
(14, 4, 2, '', '', ''),
(15, 4, 3, '', '', ''),
(16, 4, 4, '', '', '');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `aktualnosci`
--
ALTER TABLE `aktualnosci`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aktualnosci_description`
--
ALTER TABLE `aktualnosci_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `config_extra`
--
ALTER TABLE `config_extra`
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `config_lang`
--
ALTER TABLE `config_lang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config_lang_description`
--
ALTER TABLE `config_lang_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq_description`
--
ALTER TABLE `faq_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files_description`
--
ALTER TABLE `files_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_description`
--
ALTER TABLE `gallery_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_photo`
--
ALTER TABLE `gallery_photo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_photo_description`
--
ALTER TABLE `gallery_photo_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_LANGUAGES_NAME` (`name`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_description`
--
ALTER TABLE `menu_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules_description`
--
ALTER TABLE `modules_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_do_wyslania`
--
ALTER TABLE `newsletter_do_wyslania`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_template`
--
ALTER TABLE `newsletter_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages_description`
--
ALTER TABLE `pages_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ranking`
--
ALTER TABLE `ranking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ranking_pozycja`
--
ALTER TABLE `ranking_pozycja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redirects`
--
ALTER TABLE `redirects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `src_url` (`src_url`(333)),
  ADD KEY `active` (`active`);

--
-- Indexes for table `rejestr`
--
ALTER TABLE `rejestr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `search`
--
ALTER TABLE `search`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slownik`
--
ALTER TABLE `slownik`
  ADD PRIMARY KEY (`id`),
  ADD KEY `label` (`label`(333));

--
-- Indexes for table `slownik_admin`
--
ALTER TABLE `slownik_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `label` (`label`(333));

--
-- Indexes for table `slownik_admin_description`
--
ALTER TABLE `slownik_admin_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slownik_description`
--
ALTER TABLE `slownik_description`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tc_stats`
--
ALTER TABLE `tc_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_log`
--
ALTER TABLE `users_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_zalogowani`
--
ALTER TABLE `users_zalogowani`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zmieniarka`
--
ALTER TABLE `zmieniarka`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zmieniarka_photo`
--
ALTER TABLE `zmieniarka_photo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zmieniarka_photo_description`
--
ALTER TABLE `zmieniarka_photo_description`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `aktualnosci`
--
ALTER TABLE `aktualnosci`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `aktualnosci_description`
--
ALTER TABLE `aktualnosci_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `config_lang`
--
ALTER TABLE `config_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT dla tabeli `config_lang_description`
--
ALTER TABLE `config_lang_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT dla tabeli `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `faq_description`
--
ALTER TABLE `faq_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `files_description`
--
ALTER TABLE `files_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `gallery_description`
--
ALTER TABLE `gallery_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `gallery_photo`
--
ALTER TABLE `gallery_photo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT dla tabeli `gallery_photo_description`
--
ALTER TABLE `gallery_photo_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;
--
-- AUTO_INCREMENT dla tabeli `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT dla tabeli `menu_description`
--
ALTER TABLE `menu_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT dla tabeli `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT dla tabeli `modules_description`
--
ALTER TABLE `modules_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT dla tabeli `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `newsletter_do_wyslania`
--
ALTER TABLE `newsletter_do_wyslania`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `newsletter_template`
--
ALTER TABLE `newsletter_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `pages_description`
--
ALTER TABLE `pages_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT dla tabeli `ranking`
--
ALTER TABLE `ranking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `ranking_pozycja`
--
ALTER TABLE `ranking_pozycja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `redirects`
--
ALTER TABLE `redirects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;
--
-- AUTO_INCREMENT dla tabeli `rejestr`
--
ALTER TABLE `rejestr`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT dla tabeli `search`
--
ALTER TABLE `search`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `slownik`
--
ALTER TABLE `slownik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;
--
-- AUTO_INCREMENT dla tabeli `slownik_admin`
--
ALTER TABLE `slownik_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=522;
--
-- AUTO_INCREMENT dla tabeli `slownik_admin_description`
--
ALTER TABLE `slownik_admin_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2081;
--
-- AUTO_INCREMENT dla tabeli `slownik_description`
--
ALTER TABLE `slownik_description`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=884;
--
-- AUTO_INCREMENT dla tabeli `tc_stats`
--
ALTER TABLE `tc_stats`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT dla tabeli `users_log`
--
ALTER TABLE `users_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `users_zalogowani`
--
ALTER TABLE `users_zalogowani`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT dla tabeli `zmieniarka`
--
ALTER TABLE `zmieniarka`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `zmieniarka_photo`
--
ALTER TABLE `zmieniarka_photo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `zmieniarka_photo_description`
--
ALTER TABLE `zmieniarka_photo_description`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
