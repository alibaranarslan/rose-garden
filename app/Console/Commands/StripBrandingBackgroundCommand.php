<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Removes solid outer background from branding PNGs via edge flood-fill.
 * Does not invent new artwork; only makes pixels similar to image corners transparent.
 */
class StripBrandingBackgroundCommand extends Command
{
    protected $signature = 'branding:strip-background
                            {--tol=12 : Default max per-channel distance from corner reference (light-on-black logo)}
                            {--tol-dark=0 : For dark-on-black PNG: max channel delta vs corner colour; use 0 for exact black only}
                            {--dry-run : Report only, do not write files}';

    protected $description = 'Strip outer solid background from rg-logo PNGs (transparent alpha)';

    public function handle(): int
    {
        if (! extension_loaded('gd')) {
            $this->error('PHP GD extension is required.');

            return self::FAILURE;
        }

        $tolLight = max(0, min(80, (int) $this->option('tol')));
        $tolDark = max(0, min(80, (int) $this->option('tol-dark')));
        $dry = (bool) $this->option('dry-run');
        $dir = public_path('images/branding');

        $pairs = [
            'rg-logo-dark.png' => ['lockup' => 'rg-lockup-dark.png', 'tol' => $tolDark],
            'rg-logo-light.png' => ['lockup' => 'rg-lockup-light.png', 'tol' => $tolLight],
        ];

        foreach ($pairs as $source => $meta) {
            $srcPath = $dir.DIRECTORY_SEPARATOR.$source;
            $lockupCopy = $meta['lockup'];
            $tol = $meta['tol'];
            if (! is_file($srcPath)) {
                $this->warn("Missing {$source}, skip.");

                continue;
            }

            $tmp = $this->processPng($srcPath, $tol);
            if ($tmp === null) {
                $this->error("Failed to process {$source}");

                return self::FAILURE;
            }

            if ($source === 'rg-logo-dark.png') {
                $ratio = $this->opaquePixelRatio($tmp);
                if ($ratio < 0.02) {
                    imagedestroy($tmp);
                    $this->warn('rg-logo-dark.png: kenar flood-fill neredeyse tüm görüntüyü siliyor (arka plan ile yazı aynı siyah tonunda birleşik). Orijinal dosya korundu; şeffaf versiyon için tasarımdan gerçek alfa kanallı export gerekir.');
                    if (! $dry) {
                        File::copy($srcPath, $dir.DIRECTORY_SEPARATOR.$lockupCopy);
                    }

                    continue;
                }
            }

            if ($dry) {
                $this->info("[dry-run] Would write {$source} and {$lockupCopy}");

                continue;
            }

            $written = imagepng($tmp, $srcPath, 6);
            imagedestroy($tmp);
            if (! $written) {
                $this->error("Could not write {$source}");

                return self::FAILURE;
            }

            $lockPath = $dir.DIRECTORY_SEPARATOR.$lockupCopy;
            File::copy($srcPath, $lockPath);
            $this->info("Updated {$source} and synced {$lockupCopy}");
        }

        return self::SUCCESS;
    }

    private function processPng(string $path, int $tol): ?\GdImage
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            return null;
        }

        $im = @imagecreatefromstring($data);
        if (! $im instanceof \GdImage) {
            return null;
        }

        if (! imageistruecolor($im)) {
            imagepalettetotruecolor($im);
        }

        imagesavealpha($im, true);
        imagealphablending($im, false);

        $w = imagesx($im);
        $h = imagesy($im);
        if ($w < 2 || $h < 2) {
            return $im;
        }

        $ref = $this->averageCornerRgb($im, $w, $h);
        [$br, $bg, $bb] = $ref;

        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);

        $visited = array_fill(0, $w * $h, false);
        $queue = [];

        $enqueue = function (int $x, int $y) use (&$queue, &$visited, $w, $h, $im, $br, $bg, $bb, $tol): void {
            if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) {
                return;
            }
            $i = $y * $w + $x;
            if ($visited[$i]) {
                return;
            }
            $visited[$i] = true;
            $rgb = imagecolorat($im, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            if (! $this->matchesRef($r, $g, $b, $br, $bg, $bb, $tol)) {
                return;
            }
            $queue[] = [$x, $y];
        };

        for ($x = 0; $x < $w; $x++) {
            $enqueue($x, 0);
            $enqueue($x, $h - 1);
        }
        for ($y = 0; $y < $h; $y++) {
            $enqueue(0, $y);
            $enqueue($w - 1, $y);
        }

        while ($queue !== []) {
            [$x, $y] = array_pop($queue);
            imagesetpixel($im, $x, $y, $transparent);
            $enqueue($x + 1, $y);
            $enqueue($x - 1, $y);
            $enqueue($x, $y + 1);
            $enqueue($x, $y - 1);
        }

        $this->floodEdgeMatchingColour($im, $w, $h, 255, 255, 255, 22, $transparent);

        return $im;
    }

    /**
     * Removes opaque pixels matching a reference colour (e.g. white matte) reachable from any image edge.
     */
    private function floodEdgeMatchingColour(
        \GdImage $im,
        int $w,
        int $h,
        int $br,
        int $bg,
        int $bb,
        int $tol,
        int $transparent
    ): void {
        $visited = array_fill(0, $w * $h, false);
        $queue = [];

        $enqueue = function (int $x, int $y) use (&$queue, &$visited, $w, $h, $im, $br, $bg, $bb, $tol): void {
            if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) {
                return;
            }
            $i = $y * $w + $x;
            if ($visited[$i]) {
                return;
            }
            $visited[$i] = true;
            $rgba = imagecolorat($im, $x, $y);
            if ((($rgba >> 24) & 0xFF) === 127) {
                return;
            }
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;
            if (! $this->matchesRef($r, $g, $b, $br, $bg, $bb, $tol)) {
                return;
            }
            $queue[] = [$x, $y];
        };

        for ($x = 0; $x < $w; $x++) {
            $enqueue($x, 0);
            $enqueue($x, $h - 1);
        }
        for ($y = 0; $y < $h; $y++) {
            $enqueue(0, $y);
            $enqueue($w - 1, $y);
        }

        while ($queue !== []) {
            [$x, $y] = array_pop($queue);
            imagesetpixel($im, $x, $y, $transparent);
            $enqueue($x + 1, $y);
            $enqueue($x - 1, $y);
            $enqueue($x, $y + 1);
            $enqueue($x, $y - 1);
        }
    }

    /**
     * @return array{0:int,1:int,2:int}
     */
    private function averageCornerRgb(\GdImage $im, int $w, int $h): array
    {
        $coords = [
            [0, 0],
            [$w - 1, 0],
            [0, $h - 1],
            [$w - 1, $h - 1],
        ];
        $tr = 0;
        $tg = 0;
        $tb = 0;
        $n = 0;
        foreach ($coords as [$x, $y]) {
            $rgb = imagecolorat($im, $x, $y);
            $tr += ($rgb >> 16) & 0xFF;
            $tg += ($rgb >> 8) & 0xFF;
            $tb += $rgb & 0xFF;
            $n++;
        }

        return [(int) round($tr / $n), (int) round($tg / $n), (int) round($tb / $n)];
    }

    private function matchesRef(int $r, int $g, int $b, int $br, int $bg, int $bb, int $tol): bool
    {
        return abs($r - $br) <= $tol
            && abs($g - $bg) <= $tol
            && abs($b - $bb) <= $tol;
    }

    private function opaquePixelRatio(\GdImage $im): float
    {
        $w = imagesx($im);
        $h = imagesy($im);
        if ($w < 1 || $h < 1) {
            return 0.0;
        }
        $opaque = 0;
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $a = (imagecolorat($im, $x, $y) >> 24) & 0xFF;
                if ($a !== 127) {
                    $opaque++;
                }
            }
        }

        return $opaque / ($w * $h);
    }
}
