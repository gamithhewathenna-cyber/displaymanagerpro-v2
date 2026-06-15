<?php
/**
 * Storage Service
 * Provides a unified interface for Local, S3, and Cloudflare R2 storage.
 * R2 is S3-compatible, so the same AWS SDK logic works for both.
 */
class StorageService
{
    private string $driver;

    public function __construct(?string $driver = null)
    {
        $this->driver = $driver ?? Settings::storageDriver();
    }

    /**
     * Store an uploaded file and return media metadata
     */
    public function store(array $file, int $userId): array
    {
        $ext        = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $storedName = uniqid('img_', true) . '_' . $userId . '.' . $ext;

        // Validate
        if (!in_array($file['type'], ALLOWED_MIME_TYPES)) {
            throw new Exception('File type not allowed. Only JPG, PNG, WEBP accepted.');
        }
        if ($file['size'] > MAX_UPLOAD_BYTES) {
            throw new Exception('File exceeds 500KB maximum size.');
        }

        // Get image dimensions
        $dimensions = @getimagesize($file['tmp_name']);
        $width      = $dimensions[0] ?? null;
        $height     = $dimensions[1] ?? null;

        // Optimize/resize if needed
        $tmpPath = $this->optimizeImage($file['tmp_name'], $file['type']);

        switch ($this->driver) {
            case 's3':
                return $this->storeOnS3($tmpPath, $storedName, $file, $userId, $width, $height, false);
            case 'r2':
                return $this->storeOnS3($tmpPath, $storedName, $file, $userId, $width, $height, true);
            default:
                return $this->storeLocally($tmpPath, $storedName, $file, $userId, $width, $height);
        }
    }

    private function storeLocally(string $tmpPath, string $storedName, array $file, int $userId, ?int $w, ?int $h): array
    {
        $dir = UPLOAD_PATH . '/' . $userId . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $destPath = $dir . $storedName;
        if (!move_uploaded_file($tmpPath, $destPath)) {
            // For optimized temp files
            if (!rename($tmpPath, $destPath) && !copy($tmpPath, $destPath)) {
                throw new Exception('Failed to save uploaded file.');
            }
        }

        $publicUrl  = Helpers::baseUrl('uploads/' . $userId . '/' . $storedName);
        $storagePath = 'uploads/' . $userId . '/' . $storedName;
        $fileSize   = filesize($destPath);

        return [
            'original_name' => $file['name'],
            'stored_name'   => $storedName,
            'storage_path'  => $storagePath,
            'storage_driver'=> 'local',
            'mime_type'     => $file['type'],
            'file_size'     => $fileSize,
            'width'         => $w,
            'height'        => $h,
            'public_url'    => $publicUrl,
        ];
    }

    private function storeOnS3(string $tmpPath, string $storedName, array $file, int $userId, ?int $w, ?int $h, bool $isR2): array
    {
        if ($isR2) {
            $bucket    = Settings::get('r2_bucket');
            $accessKey = Settings::get('r2_access_key');
            $secretKey = Settings::get('r2_secret_key');
            $endpoint  = 'https://' . Settings::get('r2_account_id') . '.r2.cloudflarestorage.com';
            $publicUrl = rtrim(Settings::get('r2_url'), '/') . '/' . $userId . '/' . $storedName;
        } else {
            $bucket    = Settings::get('s3_bucket');
            $accessKey = Settings::get('s3_access_key');
            $secretKey = Settings::get('s3_secret_key');
            $region    = Settings::get('s3_region', 'us-east-1');
            $endpoint  = 'https://s3.' . $region . '.amazonaws.com';
            $publicUrl = rtrim(Settings::get('s3_url', ''), '/') ?: "https://$bucket.s3.$region.amazonaws.com";
            $publicUrl .= '/' . $userId . '/' . $storedName;
        }

        $key        = $userId . '/' . $storedName;
        $content    = file_get_contents($tmpPath);
        $contentLen = strlen($content);
        $date       = gmdate('D, d M Y H:i:s') . ' GMT';
        $contentType= $file['type'];

        // Build AWS Signature V4 (simplified for PUT)
        $region  = $isR2 ? 'auto' : ($region ?? 'us-east-1');
        $service = 's3';
        $host    = parse_url($endpoint, PHP_URL_HOST) . '/' . $bucket;
        $uri     = '/' . $key;

        $amzDate  = gmdate('Ymd\THis\Z');
        $dateStamp= gmdate('Ymd');

        $canonHeaders = "content-type:$contentType\nhost:$host\nx-amz-content-sha256:" . hash('sha256', $content) . "\nx-amz-date:$amzDate\n";
        $signedHeaders = 'content-type;host;x-amz-content-sha256;x-amz-date';
        $payloadHash   = hash('sha256', $content);
        $canonRequest  = "PUT\n$uri\n\n$canonHeaders\n$signedHeaders\n$payloadHash";

        $credScope = "$dateStamp/$region/$service/aws4_request";
        $strToSign = "AWS4-HMAC-SHA256\n$amzDate\n$credScope\n" . hash('sha256', $canonRequest);

        $sigKey  = hash_hmac('sha256', 'aws4_request',
                    hash_hmac('sha256', $service,
                    hash_hmac('sha256', $region,
                    hash_hmac('sha256', $dateStamp, 'AWS4' . $secretKey, true), true), true), true);
        $sig     = hash_hmac('sha256', $strToSign, $sigKey);
        $authHeader = "AWS4-HMAC-SHA256 Credential=$accessKey/$credScope, SignedHeaders=$signedHeaders, Signature=$sig";

        $ch = curl_init("$endpoint/$bucket/$key");
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $content,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_HTTPHEADER     => [
                "Authorization: $authHeader",
                "Content-Type: $contentType",
                "Content-Length: $contentLen",
                "x-amz-content-sha256: $payloadHash",
                "x-amz-date: $amzDate",
                "x-amz-acl: public-read",
            ],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Failed to upload to cloud storage. HTTP $httpCode");
        }

        @unlink($tmpPath);

        return [
            'original_name' => $file['name'],
            'stored_name'   => $storedName,
            'storage_path'  => $key,
            'storage_driver'=> $isR2 ? 'r2' : 's3',
            'mime_type'     => $file['type'],
            'file_size'     => $contentLen,
            'width'         => $w,
            'height'        => $h,
            'public_url'    => $publicUrl,
        ];
    }

    private function optimizeImage(string $tmpPath, string $mimeType): string
    {
        if (!extension_loaded('gd')) return $tmpPath;

        $img = match($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($tmpPath),
            'image/png'  => @imagecreatefrompng($tmpPath),
            'image/webp' => @imagecreatefromwebp($tmpPath),
            default      => null,
        };
        if (!$img) return $tmpPath;

        // Auto-orient based on EXIF
        if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
            $exif = @exif_read_data($tmpPath);
            $orientation = $exif['Orientation'] ?? 1;
            $img = $this->rotateByExif($img, $orientation);
        }

        $outPath = sys_get_temp_dir() . '/' . uniqid('sc_opt_') . '.jpg';
        imagejpeg($img, $outPath, 82);
        imagedestroy($img);

        return $outPath;
    }

    private function rotateByExif($img, int $orientation)
    {
        return match($orientation) {
            3 => imagerotate($img, 180, 0),
            6 => imagerotate($img, -90, 0),
            8 => imagerotate($img, 90, 0),
            default => $img,
        };
    }

    public function delete(array $media): bool
    {
        switch ($media['storage_driver']) {
            case 'local':
                $path = PUBLIC_PATH . '/' . $media['storage_path'];
                return file_exists($path) ? unlink($path) : true;
            case 's3':
            case 'r2':
                // Cloud delete (simplified)
                return true;
        }
        return true;
    }

    public function getUrl(array $media): string
    {
        if ($media['public_url']) return $media['public_url'];
        if ($media['storage_driver'] === 'local') {
            return Helpers::baseUrl($media['storage_path']);
        }
        return '';
    }
}
