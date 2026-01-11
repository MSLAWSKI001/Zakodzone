<?php
// Konfiguracja bazy danych MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'test');

// Połączenie z bazą danych - mysqli
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$conn) {
    die('Błąd połączenia z bazą danych: ' . mysqli_connect_error());
}

// Tworzenie bazy danych jeśli nie istnieje
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS " . DB_NAME);
mysqli_select_db($conn, DB_NAME);

// Tworzenie tabel
$sql_config = "CREATE TABLE IF NOT EXISTS config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    param_name VARCHAR(100) NOT NULL,
    param_value TEXT,
    UNIQUE KEY (param_name)
)";
mysqli_query($conn, $sql_config);

$sql_elements = "CREATE TABLE IF NOT EXISTS elements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('image', 'text') NOT NULL,
    content TEXT NOT NULL,
    votes INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql_elements);

$sql_votes = "CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    element_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql_votes);

$sql_admins = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    UNIQUE KEY (username)
)";
mysqli_query($conn, $sql_admins);

// Sprawdź czy są domyślne ustawienia
$check = mysqli_query($conn, "SELECT * FROM config WHERE param_name = 'elements_count'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO config (param_name, param_value) VALUES ('elements_count', '4')");
    mysqli_query($conn, "INSERT INTO config (param_name, param_value) VALUES ('vote_cooldown', '30')");
    mysqli_query($conn, "INSERT INTO config (param_name, param_value) VALUES ('images_folder', 'images')");
}

// Sprawdź czy jest admin
$checkAdmin = mysqli_query($conn, "SELECT * FROM admins WHERE username = 'admin'");
if (mysqli_num_rows($checkAdmin) == 0) {
    $default_pass = md5('admin123');
    mysqli_query($conn, "INSERT INTO admins (username, password) VALUES ('admin', '$default_pass')");
}

mysqli_set_charset($conn, 'utf8');

// Funkcja pomocnicza do pobierania konfiguracji
function getConfig($name) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $name);
    $result = mysqli_query($conn, "SELECT param_value FROM config WHERE param_name = '$name'");
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['param_value'];
    }
    return null;
}

// Funkcja do ustawiania konfiguracji
function setConfig($name, $value) {
    global $conn;
    $name = mysqli_real_escape_string($conn, $name);
    $value = mysqli_real_escape_string($conn, $value);
    mysqli_query($conn, "INSERT INTO config (param_name, param_value) VALUES ('$name', '$value') ON DUPLICATE KEY UPDATE param_value = '$value'");
}

session_start();
?>
