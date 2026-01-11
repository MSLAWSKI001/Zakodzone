<?php
require_once 'config.php';

$message = '';
$messageType = '';

// Obsługa wylogowania
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged']);
    header('Location: a1.php');
    exit;
}

// Obsługa logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $result = mysqli_query($conn, "SELECT id FROM admins WHERE username = '$username' AND password = '$password'");
    
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['admin_logged'] = true;
        header('Location: a1.php');
        exit;
    } else {
        $message = 'Nieprawidłowa nazwa użytkownika lub hasło.';
        $messageType = 'error';
    }
}

// Sprawdzenie czy zalogowany
$isLogged = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;

// Pobranie aktualnej konfiguracji
$currentElementsCount = getConfig('elements_count');
$currentVoteCooldown = getConfig('vote_cooldown');
$currentImagesFolder = getConfig('images_folder');

// Tworzenie folderu na obrazki jeśli nie istnieje
if (!file_exists($currentImagesFolder)) {
    mkdir($currentImagesFolder, 0755, true);
}

// Operacje tylko dla zalogowanych
if ($isLogged) {
    
    // Zapisanie ustawień
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
        $elementsCount = max(2, min(10, (int)$_POST['elements_count']));
        $voteCooldown = max(1, (int)$_POST['vote_cooldown']);
        $imagesFolder = mysqli_real_escape_string($conn, $_POST['images_folder']);
        
        setConfig('elements_count', $elementsCount);
        setConfig('vote_cooldown', $voteCooldown);
        setConfig('images_folder', $imagesFolder);
        
        // Tworzenie nowego folderu
        if (!file_exists($imagesFolder)) {
            mkdir($imagesFolder, 0755, true);
        }
        
        $currentImagesFolder = $imagesFolder;
        $message = 'Ustawienia zostały zapisane.';
        $messageType = 'success';
    }
    
    // Dodanie nowego elementu - TEKST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_text'])) {
        $content = mysqli_real_escape_string($conn, $_POST['text_content']);
        
        if (!empty($content)) {
            mysqli_query($conn, "INSERT INTO elements (type, content) VALUES ('text', '$content')");
            $message = 'Element tekstowy został dodany.';
            $messageType = 'success';
        } else {
            $message = 'Treść tekstu nie może być pusta.';
            $messageType = 'error';
        }
    }
    
    // Dodanie nowego elementu - OBRAZEK (upload)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_image'])) {
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image_file'];
            $fileName = $file['name'];
            $fileTmp = $file['tmp_name'];
            $fileSize = $file['size'];
            
            // Sprawdzenie rozszerzenia
            $allowedExt = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowedExt)) {
                $message = 'Niedozwolony format pliku. Dozwolone: JPG, PNG, GIF, WebP.';
                $messageType = 'error';
            } elseif ($fileSize > 5 * 1024 * 1024) {
                $message = 'Plik jest za duży. Maksymalny rozmiar: 5MB.';
                $messageType = 'error';
            } else {
                // Unikalna nazwa pliku
                $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
                $targetPath = $currentImagesFolder . '/' . $newFileName;
                
                if (move_uploaded_file($fileTmp, $targetPath)) {
                    $newFileName = mysqli_real_escape_string($conn, $newFileName);
                    mysqli_query($conn, "INSERT INTO elements (type, content) VALUES ('image', '$newFileName')");
                    $message = 'Obrazek został dodany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd podczas zapisywania pliku.';
                    $messageType = 'error';
                }
            }
        } else {
            $message = 'Nie wybrano pliku lub wystąpił błąd podczas przesyłania.';
            $messageType = 'error';
        }
    }
    
    // Usunięcie elementu
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $deleteId = (int)$_GET['delete'];
        
        // Pobierz informacje o elemencie przed usunięciem
        $elementResult = mysqli_query($conn, "SELECT type, content FROM elements WHERE id = $deleteId");
        if ($elementRow = mysqli_fetch_assoc($elementResult)) {
            // Usuń plik obrazka jeśli to obrazek
            if ($elementRow['type'] === 'image') {
                $imagePath = $currentImagesFolder . '/' . $elementRow['content'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        mysqli_query($conn, "DELETE FROM elements WHERE id = $deleteId");
        mysqli_query($conn, "DELETE FROM votes WHERE element_id = $deleteId");
        $message = 'Element został usunięty.';
        $messageType = 'success';
    }
    
    // Zmiana statusu elementu
    if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
        $toggleId = (int)$_GET['toggle'];
        mysqli_query($conn, "UPDATE elements SET active = IF(active = 1, 0, 1) WHERE id = $toggleId");
        $message = 'Status elementu został zmieniony.';
        $messageType = 'success';
    }
    
    // Reset głosów
    if (isset($_GET['reset']) && is_numeric($_GET['reset'])) {
        $resetId = (int)$_GET['reset'];
        mysqli_query($conn, "UPDATE elements SET votes = 0 WHERE id = $resetId");
        mysqli_query($conn, "DELETE FROM votes WHERE element_id = $resetId");
        $message = 'Głosy zostały zresetowane.';
        $messageType = 'success';
    }
    
    // Reset wszystkich głosów
    if (isset($_GET['reset_all'])) {
        mysqli_query($conn, "UPDATE elements SET votes = 0");
        mysqli_query($conn, "TRUNCATE TABLE votes");
        $message = 'Wszystkie głosy zostały zresetowane.';
        $messageType = 'success';
    }
    
    // Zmiana hasła admina
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (strlen($newPassword) < 5) {
            $message = 'Hasło musi mieć co najmniej 5 znaków.';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'Hasła nie są identyczne.';
            $messageType = 'error';
        } else {
            $hashedPassword = md5($newPassword);
            mysqli_query($conn, "UPDATE admins SET password = '$hashedPassword' WHERE username = 'admin'");
            $message = 'Hasło zostało zmienione.';
            $messageType = 'success';
        }
    }
}

// Pobranie listy elementów
$elementsResult = mysqli_query($conn, "SELECT * FROM elements ORDER BY votes DESC");
$allElements = array();
while ($row = mysqli_fetch_assoc($elementsResult)) {
    $allElements[] = $row;
}

// Pobranie statystyk
$totalVotes = 0;
$statsResult = mysqli_query($conn, "SELECT SUM(votes) as total FROM elements");
$statsRow = mysqli_fetch_assoc($statsResult);
if ($statsRow) {
    $totalVotes = (int)$statsRow['total'];
}

$totalElements = count($allElements);
$activeElements = 0;
foreach ($allElements as $el) {
    if ($el['active']) $activeElements++;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - System Rankingowy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (!$isLogged): ?>
        <!-- Formularz logowania -->
        <div class="login-box">
            <h2> Panel Administratora</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nazwa użytkownika</label>
                    <input type="text" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Hasło</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="login" class="btn" style="width: 100%;">Zaloguj się</button>
            </form>
            
            <p style="text-align: center; margin-top: 20px; color: #666; font-size: 0.85rem;">
                Domyślne dane: admin / admin123
            </p>
        </div>
    <?php else: ?>
        <!-- Panel admina -->
        <div class="admin-container">
            <div class="admin-header">
                <h1> Panel Konfiguracji</h1>
                <div class="admin-nav">
                    <a href="index.php" class="btn btn-small">Zobacz stronę</a>
                    <a href="a1.php?logout=1" class="btn btn-small btn-danger">Wyloguj</a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Statystyki -->
            <div class="admin-section">
                <h2> Statystyki</h2>
                <div class="settings-row">
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5rem; color: #00d4ff; font-weight: bold;"><?php echo $totalElements; ?></div>
                        <div style="color: #888;">Wszystkich elementów</div>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5rem; color: #69f0ae; font-weight: bold;"><?php echo $activeElements; ?></div>
                        <div style="color: #888;">Aktywnych elementów</div>
                    </div>
                    <div style="text-align: center; padding: 20px;">
                        <div style="font-size: 2.5rem; color: #ffd54f; font-weight: bold;"><?php echo $totalVotes; ?></div>
                        <div style="color: #888;">Wszystkich głosów</div>
                    </div>
                </div>
            </div>

            <!-- Ustawienia główne -->
            <div class="admin-section">
                <h2> Ustawienia Główne</h2>
                <form method="POST">
                    <div class="settings-row">
                        <div class="form-group">
                            <label>Liczba wyświetlanych elementów (2-10)</label>
                            <input type="number" name="elements_count" min="2" max="10" 
                                   value="<?php echo htmlspecialchars($currentElementsCount); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Czas blokady głosowania (minuty)</label>
                            <input type="number" name="vote_cooldown" min="1" 
                                   value="<?php echo htmlspecialchars($currentVoteCooldown); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Folder ze zdjęciami</label>
                            <input type="text" name="images_folder" 
                                   value="<?php echo htmlspecialchars($currentImagesFolder); ?>" required>
                        </div>
                    </div>
                    <button type="submit" name="save_settings" class="btn btn-success">Zapisz ustawienia</button>
                </form>
            </div>

            <!-- Dodawanie obrazka -->
            <div class="admin-section">
                <h2> Dodaj Obrazek</h2>
                <form method="POST" enctype="multipart/form-data" id="image-upload-form">
                    
                    <!-- Strefa drag & drop -->
                    <div class="drop-zone" id="drop-zone">
                        <div class="drop-zone-content">
                            <div class="drop-zone-icon"></div>
                            <p class="drop-zone-text">Przeciągnij i upuść obrazek tutaj</p>
                            <p class="drop-zone-hint">lub</p>
                            <label class="drop-zone-btn">
                                Wybierz plik z dysku
                                <input type="file" name="image_file" id="image-file-input" 
                                       accept="image/jpeg,image/png,image/gif,image/webp" hidden>
                            </label>
                            <p class="drop-zone-formats">Obsługiwane formaty: JPG, PNG, GIF, WebP (max 5MB)</p>
                        </div>
                        
                        <!-- Podgląd wybranego pliku -->
                        <div class="file-preview" id="file-preview" style="display: none;">
                            <img id="preview-image" src="" alt="Podgląd">
                            <div class="file-info">
                                <span class="file-name" id="file-name"></span>
                                <span class="file-size" id="file-size"></span>
                            </div>
                            <button type="button" class="remove-file-btn" id="remove-file">✕ Usuń</button>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_image" class="btn btn-success" id="upload-btn" disabled>
                         Prześlij obrazek
                    </button>
                </form>
            </div>

            <!-- Dodawanie tekstu -->
            <div class="admin-section">
                <h2> Dodaj Tekst</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Treść tekstu</label>
                        <textarea name="text_content" placeholder="Wpisz treść akapitu..." required rows="4"></textarea>
                    </div>
                    <button type="submit" name="add_text" class="btn btn-success">➕ Dodaj tekst</button>
                </form>
            </div>

            <!-- Lista elementów -->
            <div class="admin-section">
                <h2> Lista Elementów</h2>
                <div style="margin-bottom: 15px;">
                    <a href="a1.php?reset_all=1" class="btn btn-small btn-danger" 
                       onclick="return confirm('Czy na pewno chcesz zresetować WSZYSTKIE głosy?');">
                       Resetuj wszystkie głosy
                    </a>
                </div>
                
                <?php if (empty($allElements)): ?>
                    <p style="text-align: center; color: #888; padding: 30px;">
                        Brak elementów. Dodaj pierwszy element powyżej.
                    </p>
                <?php else: ?>
                    <table class="elements-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Typ</th>
                                <th>Treść/Podgląd</th>
                                <th>Głosy</th>
                                <th>Status</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allElements as $element): ?>
                                <tr>
                                    <td><?php echo $element['id']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $element['type']; ?>">
                                            <?php echo $element['type'] === 'image' ? '🖼️ Obrazek' : '📝 Tekst'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($element['type'] === 'image'): ?>
                                            <?php 
                                            $imgPath = $currentImagesFolder . '/' . $element['content'];
                                            if (file_exists($imgPath)): 
                                            ?>
                                                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Podgląd">
                                            <?php else: ?>
                                                <span style="color: #ff8a80;">❌ <?php echo htmlspecialchars($element['content']); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars(substr($element['content'], 0, 50)) . (strlen($element['content']) > 50 ? '...' : ''); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: bold; color: #00d4ff;"><?php echo $element['votes']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $element['active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $element['active'] ? '✓ Aktywny' : '✗ Nieaktywny'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="a1.php?toggle=<?php echo $element['id']; ?>" 
                                               class="btn btn-small">
                                               <?php echo $element['active'] ? 'Wyłącz' : 'Włącz'; ?>
                                            </a>
                                            <a href="a1.php?reset=<?php echo $element['id']; ?>" 
                                               class="btn btn-small"
                                               onclick="return confirm('Zresetować głosy tego elementu?');">
                                               Reset
                                            </a>
                                            <a href="a1.php?delete=<?php echo $element['id']; ?>" 
                                               class="btn btn-small btn-danger"
                                               onclick="return confirm('Czy na pewno chcesz usunąć ten element?');">
                                               Usuń
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Zmiana hasła -->
            <div class="admin-section">
                <h2> Zmiana Hasła</h2>
                <form method="POST">
                    <div class="settings-row">
                        <div class="form-group">
                            <label>Nowe hasło</label>
                            <input type="password" name="new_password" required minlength="5">
                        </div>
                        <div class="form-group">
                            <label>Potwierdź hasło</label>
                            <input type="password" name="confirm_password" required minlength="5">
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn">Zmień hasło</button>
                </form>
            </div>

            <footer>
                <p>System Rankingowy &copy; <?php echo date('Y'); ?> | <a href="index.php">Powrót do strony głównej</a></p>
            </footer>
        </div>

        <script>
            // ============ DRAG & DROP UPLOAD ============
            var dropZone = document.getElementById('drop-zone');
            var fileInput = document.getElementById('image-file-input');
            var filePreview = document.getElementById('file-preview');
            var previewImage = document.getElementById('preview-image');
            var fileName = document.getElementById('file-name');
            var fileSize = document.getElementById('file-size');
            var removeBtn = document.getElementById('remove-file');
            var uploadBtn = document.getElementById('upload-btn');
            var dropZoneContent = document.querySelector('.drop-zone-content');

            // Formatowanie rozmiaru pliku
            function formatFileSize(bytes) {
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            }

            // Wyświetlenie podglądu pliku
            function showPreview(file) {
                if (!file || !file.type.startsWith('image/')) {
                    alert('Proszę wybrać plik graficzny (JPG, PNG, GIF, WebP)');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Plik jest za duży. Maksymalny rozmiar to 5MB.');
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    
                    dropZoneContent.style.display = 'none';
                    filePreview.style.display = 'flex';
                    uploadBtn.disabled = false;
                    dropZone.classList.add('has-file');
                };
                reader.readAsDataURL(file);
            }

            // Usunięcie pliku
            function removeFile() {
                fileInput.value = '';
                previewImage.src = '';
                fileName.textContent = '';
                fileSize.textContent = '';
                
                dropZoneContent.style.display = 'block';
                filePreview.style.display = 'none';
                uploadBtn.disabled = true;
                dropZone.classList.remove('has-file');
            }

            // Event: Wybór pliku przez input
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    showPreview(this.files[0]);
                }
            });

            // Event: Usunięcie pliku
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                removeFile();
            });

            // ============ DRAG & DROP EVENTS ============
            
            // Prevent default behavior
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Highlight on drag
            ['dragenter', 'dragover'].forEach(function(eventName) {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.add('drag-over');
                });
            });

            // Remove highlight
            ['dragleave', 'drop'].forEach(function(eventName) {
                dropZone.addEventListener(eventName, function() {
                    dropZone.classList.remove('drag-over');
                });
            });

            // Handle drop
            dropZone.addEventListener('drop', function(e) {
                var files = e.dataTransfer.files;
                if (files && files[0]) {
                    // Przypisz plik do inputa
                    var dataTransfer = new DataTransfer();
                    dataTransfer.items.add(files[0]);
                    fileInput.files = dataTransfer.files;
                    
                    showPreview(files[0]);
                }
            });

            // Click na dropzone (poza przyciskiem) nie otwiera okna wyboru
            dropZone.addEventListener('click', function(e) {
                if (e.target === dropZone || e.target.classList.contains('drop-zone-content')) {
                    // Nie rób nic - niech klikają tylko w przycisk
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
