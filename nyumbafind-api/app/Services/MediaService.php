<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->cloudName = config('services.cloudinary.cloud_name');
        $this->apiKey    = config('services.cloudinary.api_key');
        $this->apiSecret = config('services.cloudinary.api_secret');
    }

    public function uploadPhoto(UploadedFile $file, int $listingId): array
    {
        return $this->upload($file, "nyumbafind/listings/{$listingId}/photos", 'image');
    }

    public function uploadVideo(UploadedFile $file, int $listingId): array
    {
        return $this->upload($file, "nyumbafind/listings/{$listingId}/videos", 'video');
    }

    private function upload(UploadedFile $file, string $folder, string $resourceType): array
    {
        $timestamp = time();
        $params    = "folder={$folder}&resource_type={$resourceType}&timestamp={$timestamp}";
        $signature = sha1($params . $this->apiSecret);

        $response = Http::attach('file', file_get_contents($file->path()), $file->getClientOriginalName())
            ->post("https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/upload", [
                'api_key'       => $this->apiKey,
                'timestamp'     => $timestamp,
                'signature'     => $signature,
                'folder'        => $folder,
                'resource_type' => $resourceType,
            ]);

        if ($response->failed()) {
            Log::error("Cloudinary upload failed: " . $response->body());
            throw new \RuntimeException("Media upload failed. Please try again.");
        }

        $data = $response->json();

        return [
            'url'       => $data['secure_url'],
            'public_id' => $data['public_id'],
            'thumbnail' => $data['eager'][0]['secure_url'] ?? null,
        ];
    }

    public function delete(string $publicId, string $resourceType = 'image'): bool
    {
        $timestamp = time();
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}" . $this->apiSecret);

        $response = Http::post("https://api.cloudinary.com/v1_1/{$this->cloudName}/{$resourceType}/destroy", [
            'public_id'  => $publicId,
            'api_key'    => $this->apiKey,
            'timestamp'  => $timestamp,
            'signature'  => $signature,
        ]);

        return $response->json('result') === 'ok';
    }
}
