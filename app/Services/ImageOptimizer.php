<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ImageOptimizer
{
    public function store(
        UploadedFile $file,
        string $directory,
        int $maxWidth,
        int $maxHeight,
    ): string {
        $sourcePath = $file->getRealPath();
        $info = getimagesize($sourcePath);

        if ($info === false) {
            throw ValidationException::withMessages(['image' => 'فایل تصویر معتبر نیست.']);
        }

        [$width, $height] = $info;
        if ($width * $height > config('images.max_pixels')) {
            throw ValidationException::withMessages(['image' => 'ابعاد تصویر بیش از حد مجاز است.']);
        }

        $source = $this->createImage($sourcePath, $info['mime']);
        if ($info['mime'] === 'image/jpeg') {
            $source = $this->applyOrientation($source, $sourcePath);
            $width = imagesx($source);
            $height = imagesy($source);
        }

        $scale = min($maxWidth / $width, $maxHeight / $height, 1);
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height,
        );

        ob_start();
        $encoded = imagewebp($target, null, (int) config('images.quality', 82));
        $contents = ob_get_clean();
        imagedestroy($source);
        imagedestroy($target);

        if (! $encoded || ! is_string($contents)) {
            throw new RuntimeException('Image optimization failed.');
        }

        $path = trim($directory, '/').'/'.Str::uuid().'.webp';
        if (! Storage::disk('public')->put($path, $contents)) {
            throw new RuntimeException('Optimized image could not be stored.');
        }

        return $path;
    }

    private function createImage(string $path, string $mime): \GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default => false,
        };

        if (! $image instanceof \GdImage) {
            throw ValidationException::withMessages(['image' => 'فرمت تصویر پشتیبانی نمی‌شود.']);
        }

        return $image;
    }

    private function applyOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = @exif_read_data($path)['Orientation'] ?? 1;
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        if ($rotated !== $image) {
            imagedestroy($image);
        }

        return $rotated;
    }
}
