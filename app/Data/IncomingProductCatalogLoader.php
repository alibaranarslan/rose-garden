<?php

namespace App\Data;

use InvalidArgumentException;
use JsonException;

final class IncomingProductCatalogLoader
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function load(?string $explicitPath = null): array
    {
        $path = self::resolvePath($explicitPath);

        if ($path === null) {
            return ProductIncomingDefinitions::catalog();
        }

        if (! is_file($path)) {
            throw new InvalidArgumentException('Katalog dosyası bulunamadı: '.$path);
        }

        try {
            $decoded = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException('Katalog JSON okunamadı: '.$e->getMessage(), 0, $e);
        }

        if (! is_array($decoded)) {
            throw new InvalidArgumentException('Katalog kökü bir nesne (dosya adı → tanım) olmalıdır.');
        }

        return $decoded;
    }

    private static function resolvePath(?string $explicitPath): ?string
    {
        if ($explicitPath !== null && $explicitPath !== '') {
            return $explicitPath;
        }

        $default = storage_path('app/product-import/catalog.json');

        return is_file($default) ? $default : null;
    }
}
