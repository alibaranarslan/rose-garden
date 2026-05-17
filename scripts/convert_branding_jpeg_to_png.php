<?php

/**
 * Cursor bazen sohbet görsellerini .png adıyla JPEG olarak kaydeder.
 * Bu betik JPEG içeriğini geçerli PNG dosyasına çevirir (şeffaflık eklemez).
 */
$dir = dirname(__DIR__).'/public/images/branding';
foreach (['rg-logo-dark.png', 'rg-logo-light.png', 'rg-lockup-dark.png', 'rg-lockup-light.png'] as $name) {
    $path = $dir.'/'.$name;
    if (! is_file($path)) {
        continue;
    }
    $bin = file_get_contents($path);
    if ($bin === false || strlen($bin) < 4) {
        fwrite(STDERR, "skip empty $name\n");

        continue;
    }
    $sig = substr($bin, 0, 2);
    if ($sig !== "\xFF\xD8") {
        echo "unchanged (not JPEG): $name\n";

        continue;
    }
    $im = @imagecreatefromstring($bin);
    if (! $im instanceof GdImage) {
        fwrite(STDERR, "failed decode $name\n");
        exit(1);
    }
    if (! imageistruecolor($im)) {
        imagepalettetotruecolor($im);
    }
    imagesavealpha($im, true);
    imagealphablending($im, false);
    if (! imagepng($im, $path, 6)) {
        fwrite(STDERR, "failed write $name\n");
        exit(1);
    }
    imagedestroy($im);
    echo "converted JPEG→PNG: $name\n";
}
