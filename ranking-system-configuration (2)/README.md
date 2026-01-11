# System Rankingowy - PHP + MySQL

System umożliwiający głosowanie na elementy (obrazki lub teksty) z zabezpieczeniem CAPTCHA i blokadą IP.

## 📁 Struktura plików

```
htdocs/
├── config.php      # Konfiguracja bazy danych i funkcje pomocnicze
├── index.php       # Strona główna z głosowaniem
├── a1.php          # Panel administratora
├── vote.php        # Obsługa głosowania
├── captcha.php     # Generator obrazków CAPTCHA
├── style.css       # Style CSS
├── images/         # Folder na obrazki
└── README.md       # Ten plik
```

## 🚀 Instalacja

### 1. Skopiuj pliki
Skopiuj wszystkie pliki do folderu `htdocs` (XAMPP) lub odpowiedniego katalogu serwera WWW.

### 2. Skonfiguruj bazę danych
Otwórz plik `config.php` i dostosuj dane połączenia z MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Twoje hasło MySQL
define('DB_NAME', 'ranking_system');
```

### 3. Uruchom XAMPP
- Uruchom Apache
- Uruchom MySQL

### 4. Otwórz w przeglądarce
- Strona główna: `http://localhost/index.php`
- Panel admina: `http://localhost/a1.php`

Baza danych i tabele zostaną utworzone automatycznie przy pierwszym uruchomieniu!

## 🔐 Logowanie do panelu admina

**Domyślne dane logowania:**
- Login: `admin`
- Hasło: `admin123`

⚠️ **Ważne:** Zmień hasło po pierwszym zalogowaniu!

## ⚙️ Funkcje systemu

### Strona główna (index.php)
- Wyświetla od 2 do 10 elementów (konfigurowalne)
- Elementy sortowane według liczby głosów
- Głosowanie wymaga przepisania kodu CAPTCHA
- Blokada IP na określony czas (np. 30 minut)
- Medale dla TOP 3 (złoty, srebrny, brązowy)

### Panel administratora (a1.php)
- **Statystyki:** liczba elementów, aktywnych, głosów
- **Ustawienia:**
  - Liczba wyświetlanych elementów (2-10)
  - Czas blokady głosowania (w minutach)
  - Folder ze zdjęciami
- **Zarządzanie elementami:**
  - Dodawanie tekstów lub obrazków
  - Włączanie/wyłączanie elementów
  - Reset głosów (pojedynczy lub wszystkie)
  - Usuwanie elementów
- **Zmiana hasła administratora**

## 🖼️ Dodawanie obrazków

1. Wgraj obrazki do folderu `images/` (lub innego skonfigurowanego)
2. W panelu admina wybierz typ "Obrazek"
3. Wpisz nazwę pliku, np. `photo1.jpg`

**Obsługiwane formaty:** JPG, PNG, GIF, WebP

## 📊 Struktura bazy danych

### Tabela `config`
- Przechowuje ustawienia systemu

### Tabela `elements`
- id, type (image/text), content, votes, active, created_at

### Tabela `votes`
- id, element_id, ip_address, voted_at

### Tabela `admins`
- id, username, password (MD5)

## ⚠️ Uwagi

- System używa przestarzałej biblioteki `mysql_*` (PHP < 7.0)
- Dla PHP 7+ należy przerobić na `mysqli_*` lub PDO
- CAPTCHA generowana jest przez GD Library (musi być włączona w PHP)
- Hasła przechowywane jako MD5 (dla produkcji użyj `password_hash()`)

## 🔧 Rozwiązywanie problemów

### Błąd "Call to undefined function mysql_connect"
PHP 7+ nie obsługuje mysql_*. Uruchom na PHP 5.6 lub przebuduj na mysqli.

### Obrazki się nie wyświetlają
- Sprawdź czy folder `images/` istnieje
- Sprawdź prawa dostępu (chmod 755)
- Sprawdź czy nazwa pliku jest poprawna

### CAPTCHA nie działa
- Włącz rozszerzenie GD w php.ini: `extension=gd`
- Zrestartuj Apache

## 📝 Licencja

Projekt edukacyjny - dowolne użycie.
