<?php
session_start();

$id = isset($_GET['id']) ? $_GET['id'] : '0';

// Generowanie kodu captcha
function generateCaptchaCode($length = 5) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

$code = generateCaptchaCode();
$_SESSION['captcha_' . $id] = $code;

// Sprawdź czy GD jest dostępne
if (extension_loaded('gd')) {
    // Tworzenie obrazka z GD
    $width = 130;
    $height = 45;
    $image = imagecreatetruecolor($width, $height);
    
    // Kolory
    $bg = imagecolorallocate($image, 40, 40, 60);
    $textColor = imagecolorallocate($image, 0, 212, 255);
    $noiseColor = imagecolorallocate($image, 100, 100, 120);
    
    // Wypełnienie tła
    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    
    // Dodanie szumu (linie)
    for ($i = 0; $i < 6; $i++) {
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noiseColor);
    }
    
    // Dodanie szumu (kropki)
    for ($i = 0; $i < 100; $i++) {
        imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
    }
    
    // Dodanie tekstu - każda litera osobno z lekkim przesunięciem
    $font = 5;
    $charWidth = imagefontwidth($font);
    $charHeight = imagefontheight($font);
    $startX = ($width - (strlen($code) * ($charWidth + 3))) / 2;
    
    for ($i = 0; $i < strlen($code); $i++) {
        $x = $startX + ($i * ($charWidth + 3));
        $y = ($height - $charHeight) / 2 + rand(-3, 3);
        imagestring($image, $font, $x, $y, $code[$i], $textColor);
    }
    
    // Nagłówki
    header('Content-Type: image/png');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    imagepng($image);
    imagedestroy($image);
} else {
    // Fallback - SVG captcha (nie wymaga GD)
    header('Content-Type: image/svg+xml');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    $width = 130;
    $height = 45;
    
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    ?>
<svg xmlns="http://www.w3.org/2000/svg" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
    <rect width="100%" height="100%" fill="#28283c"/>
    <?php
    // Linie szumu
    for ($i = 0; $i < 5; $i++) {
        $x1 = rand(0, $width);
        $y1 = rand(0, $height);
        $x2 = rand(0, $width);
        $y2 = rand(0, $height);
        echo "<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' stroke='#555577' stroke-width='1'/>";
    }
    
    // Tekst captcha
    $startX = 15;
    for ($i = 0; $i < strlen($code); $i++) {
        $x = $startX + ($i * 22);
        $y = 30 + rand(-3, 3);
        $rotate = rand(-15, 15);
        $char = $code[$i];
        echo "<text x='$x' y='$y' fill='#00d4ff' font-family='monospace' font-size='20' font-weight='bold' transform='rotate($rotate $x $y)'>$char</text>";
    }
    ?>
</svg>
<?php
}
?>
