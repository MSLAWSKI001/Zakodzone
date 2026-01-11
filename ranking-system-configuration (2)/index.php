<?php
require_once 'config.php';

$message = '';
$messageType = '';

// Sprawdzenie czy są jakieś komunikaty w sesji
if (isset($_SESSION['vote_message'])) {
    $message = $_SESSION['vote_message'];
    $messageType = $_SESSION['vote_type'];
    unset($_SESSION['vote_message']);
    unset($_SESSION['vote_type']);
}

// Pobranie konfiguracji
$elementsCount = (int)getConfig('elements_count');
$voteCooldown = (int)getConfig('vote_cooldown');
$imagesFolder = getConfig('images_folder');

// Pobranie aktywnych elementów
$result = mysqli_query($conn, "SELECT * FROM elements WHERE active = 1 ORDER BY votes DESC LIMIT $elementsCount");
$elements = array();
while ($row = mysqli_fetch_assoc($result)) {
    $elements[] = $row;
}

// Sprawdzenie IP dla GLOBALNEGO cooldownu (jeden głos na wszystko)
$userIP = $_SERVER['REMOTE_ADDR'];
$userIPEscaped = mysqli_real_escape_string($conn, $userIP);

// Sprawdź ostatni głos użytkownika (na DOWOLNY element)
$globalCooldown = 0;
$lastVoteResult = mysqli_query($conn, "SELECT voted_at FROM votes WHERE ip_address = '$userIPEscaped' ORDER BY voted_at DESC LIMIT 1");
if ($lastVoteRow = mysqli_fetch_assoc($lastVoteResult)) {
    $lastVotedTime = strtotime($lastVoteRow['voted_at']);
    $unlockTime = $lastVotedTime + ($voteCooldown * 60);
    $remaining = $unlockTime - time();
    if ($remaining > 0) {
        $globalCooldown = $remaining;
    }
}

$canVote = ($globalCooldown <= 0);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Rankingowy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1> System Rankingowy</h1>
            <p>Oddaj swój głos na ulubiony element! Kliknij przycisk, aby zagłosować.</p>
        </header>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>" id="flash-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($elements)): ?>
            <div class="message info">
                Brak elementów do wyświetlenia. Administrator musi dodać elementy w panelu konfiguracji.
            </div>
        <?php else: ?>
            <div class="elements-grid">
                <?php 
                $rank = 1;
                foreach ($elements as $element): 
                    $elementId = (int)$element['id'];
                    
                    // Klasa dla medali
                    $rankClass = '';
                    if ($rank == 1) $rankClass = '';
                    elseif ($rank == 2) $rankClass = 'silver';
                    elseif ($rank == 3) $rankClass = 'bronze';
                ?>
                    <div class="element-card">
                        <div class="rank-badge <?php echo $rankClass; ?>"><?php echo $rank; ?></div>
                        
                        <?php if ($element['type'] == 'image'): ?>
                            <?php 
                            $imagePath = $imagesFolder . '/' . $element['content'];
                            if (file_exists($imagePath)): 
                            ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Element <?php echo $element['id']; ?>">
                            <?php else: ?>
                                <div class="text-content"> Brak obrazka: <?php echo htmlspecialchars($element['content']); ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-content">
                                <?php echo nl2br(htmlspecialchars($element['content'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="element-info">
                            <span class="vote-count" id="votes-<?php echo $elementId; ?>"><?php echo $element['votes']; ?></span>
                            <span class="vote-label">głosów</span>
                        </div>

                        <div class="vote-form">
                            <?php if ($canVote): ?>
                                <button type="button" class="vote-btn" id="vote-btn-<?php echo $elementId; ?>" onclick="openVoteModal(<?php echo $elementId; ?>)">
                                     Zagłosuj
                                </button>
                            <?php else: ?>
                                <button class="vote-btn cooldown-btn" disabled id="vote-btn-<?php echo $elementId; ?>">
                                     <span class="global-timer"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php 
                    $rank++;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>

        <footer>
            <p>System Rankingowy &copy; <?php echo date('Y'); ?> | <a href="a1.php">Panel Administratora</a></p>
        </footer>
    </div>

    <!-- Modal do głosowania -->
    <div id="vote-modal" class="modal-overlay">
        <div class="modal-content">
            <h3> Potwierdzenie głosu</h3>
            <p class="modal-subtitle">Przepisz kod z obrazka, aby oddać głos.</p>
            
            <!-- Timer info -->
            <div id="modal-timer-box" class="timer-box" style="display: none;">
                <span class="timer-icon"></span>
                <span>Następny głos za: <strong id="modal-timer-display">0:00</strong></span>
            </div>
            
            <!-- Canvas CAPTCHA -->
            <div class="captcha-wrapper">
                <canvas id="captcha-canvas" width="200" height="60"></canvas>
            </div>
            
            <button type="button" class="refresh-captcha-btn" onclick="captchaController.refresh()">
                 Odśwież obrazek
            </button>
            
            <input type="text" 
                   id="captcha-input" 
                   class="captcha-text-input" 
                   placeholder="Wpisz kod" 
                   maxlength="5"
                   autocomplete="off">
            
            <p id="captcha-error" class="error-text" style="display: none;">Nieprawidłowy kod.</p>
            
            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeVoteModal()">Anuluj</button>
                <button type="button" class="btn-vote" onclick="submitVote()">Głosuj</button>
            </div>
            
            <form id="vote-form" action="vote.php" method="POST" style="display: none;">
                <input type="hidden" name="element_id" id="form-element-id" value="">
                <input type="hidden" name="captcha" id="form-captcha" value="">
            </form>
        </div>
    </div>

    <script>
        // Dane z PHP - GLOBALNY cooldown (jeden dla wszystkich elementów)
        var globalCooldown = <?php echo $globalCooldown; ?>;
        var cooldownMinutes = <?php echo $voteCooldown; ?>;
        var currentElementId = null;
        var currentCaptchaCode = '';

        // ============ CAPTCHA CONTROLLER ============
        var captchaController = {
            canvas: null,
            ctx: null,
            
            init: function() {
                this.canvas = document.getElementById('captcha-canvas');
                this.ctx = this.canvas.getContext('2d');
            },
            
            generateCode: function() {
                var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                var code = '';
                for (var i = 0; i < 5; i++) {
                    code += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return code;
            },
            
            draw: function(code) {
                var ctx = this.ctx;
                var width = this.canvas.width;
                var height = this.canvas.height;
                
                // Tło
                ctx.fillStyle = '#1a1a2e';
                ctx.fillRect(0, 0, width, height);
                
                // Linie szumu
                for (var i = 0; i < 5; i++) {
                    ctx.beginPath();
                    ctx.moveTo(Math.random() * width, Math.random() * height);
                    ctx.lineTo(Math.random() * width, Math.random() * height);
                    ctx.strokeStyle = 'rgba(100, 100, 150, 0.5)';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
                
                // Kropki szumu
                for (var i = 0; i < 50; i++) {
                    ctx.beginPath();
                    ctx.arc(Math.random() * width, Math.random() * height, 1, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(100, 100, 150, 0.5)';
                    ctx.fill();
                }
                
                // Tekst
                ctx.font = 'bold 32px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                var startX = 30;
                for (var i = 0; i < code.length; i++) {
                    var x = startX + i * 35;
                    var y = height / 2 + (Math.random() * 10 - 5);
                    
                    // Gradient kolorów
                    var hue = 180 + Math.random() * 40;
                    ctx.fillStyle = 'hsl(' + hue + ', 100%, 60%)';
                    
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.rotate((Math.random() - 0.5) * 0.4);
                    ctx.fillText(code[i], 0, 0);
                    ctx.restore();
                }
            },
            
            refresh: function() {
                currentCaptchaCode = this.generateCode();
                this.draw(currentCaptchaCode);
                document.getElementById('captcha-input').value = '';
                document.getElementById('captcha-error').style.display = 'none';
            }
        };

        // ============ TIMER FUNCTIONS ============
        function formatTime(seconds) {
            if (seconds <= 0) return '0:00';
            var mins = Math.floor(seconds / 60);
            var secs = seconds % 60;
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }

        function updateGlobalTimer() {
            // Aktualizuj wszystkie timery na stronie
            var timerElements = document.querySelectorAll('.global-timer');
            var allVoteButtons = document.querySelectorAll('[id^="vote-btn-"]');
            
            if (globalCooldown > 0) {
                globalCooldown--;
                
                // Aktualizuj tekst timerów
                timerElements.forEach(function(el) {
                    el.textContent = formatTime(globalCooldown);
                });
                
                // Aktualizuj timer w modalu
                var modalTimerDisplay = document.getElementById('modal-timer-display');
                if (modalTimerDisplay) {
                    modalTimerDisplay.textContent = formatTime(globalCooldown);
                }
                
                // Gdy czas się skończy - odblokuj wszystkie przyciski
                if (globalCooldown <= 0) {
                    allVoteButtons.forEach(function(btn) {
                        var elemId = btn.id.replace('vote-btn-', '');
                        btn.disabled = false;
                        btn.className = 'vote-btn';
                        btn.innerHTML = ' Zagłosuj';
                        btn.onclick = function() { 
                            openVoteModal(parseInt(elemId)); 
                        };
                    });
                }
            }
        }

        // Inicjalizacja timerów przy starcie
        function initTimers() {
            var timerElements = document.querySelectorAll('.global-timer');
            if (globalCooldown > 0) {
                timerElements.forEach(function(el) {
                    el.textContent = formatTime(globalCooldown);
                });
            }
        }

        setInterval(updateGlobalTimer, 1000);
        initTimers();

        // ============ MODAL FUNCTIONS ============
        function openVoteModal(elementId) {
            currentElementId = elementId;
            document.getElementById('form-element-id').value = elementId;
            
            // Init captcha
            captchaController.init();
            captchaController.refresh();
            
            // Timer - pokazuj globalny cooldown
            var timerBox = document.getElementById('modal-timer-box');
            if (globalCooldown > 0) {
                timerBox.style.display = 'flex';
                document.getElementById('modal-timer-display').textContent = formatTime(globalCooldown);
            } else {
                timerBox.style.display = 'none';
            }
            
            // Show modal
            document.getElementById('vote-modal').classList.add('active');
            
            // Focus input
            setTimeout(function() {
                document.getElementById('captcha-input').focus();
            }, 100);
        }

        function closeVoteModal() {
            document.getElementById('vote-modal').classList.remove('active');
            currentElementId = null;
            document.getElementById('captcha-error').style.display = 'none';
        }

        function submitVote() {
            var input = document.getElementById('captcha-input').value.toUpperCase().trim();
            var errorEl = document.getElementById('captcha-error');
            
            if (input === currentCaptchaCode) {
                document.getElementById('form-captcha').value = input;
                document.getElementById('vote-form').submit();
            } else {
                errorEl.style.display = 'block';
                captchaController.refresh();
            }
        }

        // Enter key submit
        document.getElementById('captcha-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                submitVote();
            }
        });

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVoteModal();
            }
        });

        // Close on backdrop click
        document.getElementById('vote-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVoteModal();
            }
        });

        // Flash message auto-hide
        var flashMsg = document.getElementById('flash-message');
        if (flashMsg) {
            setTimeout(function() {
                flashMsg.style.opacity = '0';
                setTimeout(function() {
                    flashMsg.style.display = 'none';
                }, 500);
            }, 5000);
        }
    </script>
</body>
</html>
