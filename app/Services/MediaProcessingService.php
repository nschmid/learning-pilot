<?php

namespace App\Services;

use App\Enums\MaterialType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaProcessingService
{
    /**
     * Allowed extensions by material type.
     */
    protected array $allowedExtensions;

    /**
     * Maximum file sizes by type (in bytes).
     */
    protected array $maxFileSizes;

    public function __construct()
    {
        $this->allowedExtensions = config('lernpfad.materials.allowed_extensions', [
            'video' => ['mp4', 'webm', 'mov'],
            'audio' => ['mp3', 'wav', 'ogg'],
            'document' => ['pdf', 'doc', 'docx'],
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        ]);

        $this->maxFileSizes = [
            'video' => config('lernpfad.materials.max_file_size', 100 * 1024 * 1024),
            'audio' => 50 * 1024 * 1024,
            'document' => 20 * 1024 * 1024,
            'image' => 5 * 1024 * 1024,
        ];
    }

    /**
     * Validate an uploaded file.
     */
    public function validateFile(UploadedFile $file, ?MaterialType $type = null): array
    {
        $errors = [];
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Determine material type from extension if not provided
        if (! $type) {
            $type = $this->detectMaterialType($extension);
        }

        if (! $type) {
            $errors[] = __('Dateityp nicht unterstützt: :extension', ['extension' => $extension]);

            return $errors;
        }

        // Validate extension
        $allowedExtensions = $this->getAllowedExtensions($type);
        if (! in_array($extension, $allowedExtensions)) {
            $errors[] = __('Ungültige Dateierweiterung. Erlaubt: :extensions', [
                'extensions' => implode(', ', $allowedExtensions),
            ]);
        }

        // Validate file size
        $maxSize = $this->getMaxFileSize($type);
        if ($size > $maxSize) {
            $errors[] = __('Datei zu groß. Maximum: :size MB', [
                'size' => round($maxSize / 1024 / 1024, 1),
            ]);
        }

        // Validate MIME type
        if (! $this->validateMimeType($mimeType, $type)) {
            $errors[] = __('Ungültiger MIME-Typ: :mime', ['mime' => $mimeType]);
        }

        return $errors;
    }

    /**
     * Process and store an uploaded file.
     */
    public function processUpload(
        UploadedFile $file,
        string $collection,
        ?string $disk = null,
        array $options = []
    ): array {
        $disk = $disk ?? config('filesystems.default');
        $extension = strtolower($file->getClientOriginalExtension());
        $type = $this->detectMaterialType($extension);

        // Generate unique filename
        $filename = $this->generateFilename($file);

        // Determine storage path
        $path = $this->getStoragePath($collection, $type);

        // Store the file
        $storedPath = $file->storeAs($path, $filename, $disk);

        $result = [
            'path' => $storedPath,
            'disk' => $disk,
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'extension' => $extension,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $type?->value,
        ];

        // Process based on type
        if ($type === MaterialType::Image && ($options['generate_thumbnail'] ?? true)) {
            $result['thumbnail'] = $this->generateImageThumbnail($storedPath, $disk, $options);
        }

        if ($type === MaterialType::Video && ($options['extract_metadata'] ?? true)) {
            $result['duration'] = $this->extractVideoDuration($storedPath, $disk);
        }

        return $result;
    }

    /**
     * Generate a thumbnail for an image.
     */
    public function generateImageThumbnail(
        string $path,
        string $disk,
        array $options = []
    ): ?string {
        $width = $options['thumbnail_width'] ?? 400;
        $height = $options['thumbnail_height'] ?? 300;

        try {
            $fullPath = Storage::disk($disk)->path($path);

            $image = Image::read($fullPath);
            $image->cover($width, $height);

            // Generate thumbnail path
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_thumb.'.$pathInfo['extension'];

            $thumbnailFullPath = Storage::disk($disk)->path($thumbnailPath);
            $image->save($thumbnailFullPath);

            return $thumbnailPath;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Generate a thumbnail for a video (first frame).
     */
    public function generateVideoThumbnail(string $path, string $disk): ?string
    {
        // This would require FFmpeg - return null if not available
        // In production, you'd use a package like PHP-FFMpeg
        try {
            $fullPath = Storage::disk($disk)->path($path);
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_thumb.jpg';
            $thumbnailFullPath = Storage::disk($disk)->path($thumbnailPath);

            // FFmpeg command to extract first frame
            $command = sprintf(
                'ffmpeg -i %s -ss 00:00:01 -vframes 1 -vf scale=400:300 %s 2>&1',
                escapeshellarg($fullPath),
                escapeshellarg($thumbnailFullPath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($thumbnailFullPath)) {
                return $thumbnailPath;
            }

            return null;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Extract video duration in seconds.
     */
    public function extractVideoDuration(string $path, string $disk): ?int
    {
        try {
            $fullPath = Storage::disk($disk)->path($path);

            // Use FFprobe to get duration
            $command = sprintf(
                'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>&1',
                escapeshellarg($fullPath)
            );

            $duration = shell_exec($command);

            if ($duration && is_numeric(trim($duration))) {
                return (int) round((float) trim($duration));
            }

            return null;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * Optimize an image file.
     */
    public function optimizeImage(string $path, string $disk, int $quality = 85): bool
    {
        try {
            $fullPath = Storage::disk($disk)->path($path);

            $image = Image::read($fullPath);

            // Resize if too large
            $maxWidth = config('lernpfad.materials.max_image_width', 1920);
            $maxHeight = config('lernpfad.materials.max_image_height', 1080);

            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->scaleDown($maxWidth, $maxHeight);
            }

            $image->save($fullPath, $quality);

            return true;
        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Delete a file and its thumbnail.
     */
    public function deleteFile(string $path, string $disk, bool $deleteThumbnail = true): bool
    {
        $deleted = Storage::disk($disk)->delete($path);

        if ($deleteThumbnail) {
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_thumb.'.$pathInfo['extension'];

            if (Storage::disk($disk)->exists($thumbnailPath)) {
                Storage::disk($disk)->delete($thumbnailPath);
            }

            // Also try jpg thumbnail for videos
            $jpgThumbnailPath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_thumb.jpg';
            if (Storage::disk($disk)->exists($jpgThumbnailPath)) {
                Storage::disk($disk)->delete($jpgThumbnailPath);
            }
        }

        return $deleted;
    }

    /**
     * Get file URL for serving.
     */
    public function getFileUrl(string $path, string $disk, bool $temporary = false, int $expireMinutes = 60): string
    {
        if ($temporary) {
            return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($expireMinutes));
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Copy a media file.
     */
    public function copyFile(string $sourcePath, string $targetPath, string $disk): bool
    {
        return Storage::disk($disk)->copy($sourcePath, $targetPath);
    }

    /**
     * Get file metadata.
     */
    public function getFileMetadata(string $path, string $disk): array
    {
        if (! Storage::disk($disk)->exists($path)) {
            return [];
        }

        return [
            'path' => $path,
            'size' => Storage::disk($disk)->size($path),
            'last_modified' => Storage::disk($disk)->lastModified($path),
            'mime_type' => Storage::disk($disk)->mimeType($path),
            'url' => $this->getFileUrl($path, $disk),
        ];
    }

    /**
     * Detect material type from extension.
     */
    public function detectMaterialType(string $extension): ?MaterialType
    {
        $extension = strtolower($extension);

        foreach ($this->allowedExtensions as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return match ($type) {
                    'video' => MaterialType::Video,
                    'audio' => MaterialType::Audio,
                    'document' => MaterialType::Pdf,
                    'image' => MaterialType::Image,
                    default => null,
                };
            }
        }

        return null;
    }

    /**
     * Get allowed extensions for a material type.
     */
    public function getAllowedExtensions(MaterialType $type): array
    {
        return match ($type) {
            MaterialType::Video => $this->allowedExtensions['video'] ?? [],
            MaterialType::Audio => $this->allowedExtensions['audio'] ?? [],
            MaterialType::Pdf => $this->allowedExtensions['document'] ?? [],
            MaterialType::Image => $this->allowedExtensions['image'] ?? [],
            default => [],
        };
    }

    /**
     * Get maximum file size for a material type.
     */
    public function getMaxFileSize(MaterialType $type): int
    {
        return match ($type) {
            MaterialType::Video => $this->maxFileSizes['video'],
            MaterialType::Audio => $this->maxFileSizes['audio'],
            MaterialType::Pdf => $this->maxFileSizes['document'],
            MaterialType::Image => $this->maxFileSizes['image'],
            default => 10 * 1024 * 1024, // 10MB default
        };
    }

    /**
     * Validate MIME type matches expected material type.
     */
    protected function validateMimeType(string $mimeType, MaterialType $type): bool
    {
        $validMimes = match ($type) {
            MaterialType::Video => ['video/mp4', 'video/webm', 'video/quicktime'],
            MaterialType::Audio => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3'],
            MaterialType::Pdf => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            MaterialType::Image => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            default => [],
        };

        return in_array($mimeType, $validMimes);
    }

    /**
     * Generate a unique filename.
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        return $name.'-'.Str::random(8).'.'.$extension;
    }

    /**
     * Get storage path for a collection and type.
     */
    protected function getStoragePath(string $collection, ?MaterialType $type): string
    {
        $basePath = $collection;

        if ($type) {
            $basePath .= '/'.strtolower($type->value);
        }

        $basePath .= '/'.now()->format('Y/m');

        return $basePath;
    }

    /**
     * Process a Spatie Media Library media item.
     */
    public function processMediaLibraryItem(Media $media, array $options = []): void
    {
        $type = $this->detectMaterialType($media->extension);

        if ($type === MaterialType::Image) {
            // Generate conversions
            $this->optimizeImage($media->getPath(), 'local');
        }
    }

    /**
     * Get total storage used by a user or team.
     */
    public function getStorageUsed(string $path, string $disk = 'local'): int
    {
        $size = 0;

        $files = Storage::disk($disk)->allFiles($path);
        foreach ($files as $file) {
            $size += Storage::disk($disk)->size($file);
        }

        return $size;
    }

    /**
     * Format bytes to human readable.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
