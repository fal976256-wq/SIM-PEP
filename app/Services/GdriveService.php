<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GdriveService
{
    private string $webAppUrl;

    public function __construct()
    {
        $this->webAppUrl = config('gdrive.webapp_url', '');
    }

    /**
     * Check if GDrive integration is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->webAppUrl);
    }

    /**
     * Upload a file to Google Drive via GAS
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $metadata — tahunAnggaran, jenisBantuan, usulanId, jenisDokumen
     * @return array{success: bool, file_id: ?string, link: ?string, error: ?string}
     */
    public function upload($file, array $metadata): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'file_id' => null, 'link' => null, 'error' => 'GDrive not configured'];
        }

        try {
            $payload = [
                'action' => 'upload',
                'file' => [
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'content' => base64_encode(file_get_contents($file->getRealPath())),
                ],
                'metadata' => [
                    'tahunAnggaran' => $metadata['tahunAnggaran'] ?? config('app.tahun_anggaran'),
                    'jenisBantuan'  => $metadata['jenisBantuan'] ?? 'KUBE',
                    'usulanId'      => $metadata['usulanId'] ?? 0,
                    'jenisDokumen'  => $metadata['jenisDokumen'] ?? 'Dokumen',
                ],
            ];

            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->webAppUrl, $payload);

            if ($response->successful()) {
                $body = $response->json();
                if ($body['success'] ?? false) {
                    $data = $body['data'] ?? [];
                    return [
                        'success' => true,
                        'file_id' => $data['fileId'] ?? null,
                        'link'    => $data['webViewLink'] ?? null,
                        'error'   => null,
                    ];
                }
                return ['success' => false, 'file_id' => null, 'link' => null, 'error' => $body['message'] ?? 'Unknown error'];
            }

            return ['success' => false, 'file_id' => null, 'link' => null, 'error' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            Log::error('GDrive upload failed: ' . $e->getMessage());
            return ['success' => false, 'file_id' => null, 'link' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Delete a file from Google Drive via GAS
     */
    public function delete(string $fileId): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->post($this->webAppUrl, [
                    'action' => 'delete',
                    'fileId' => $fileId,
                ]);

            return $response->successful() && ($response->json('success') ?? false);
        } catch (\Exception $e) {
            Log::error('GDrive delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test connection to GAS web app
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'GAS Web App URL belum dikonfigurasi'];
        }

        try {
            $response = Http::timeout(10)
                ->post($this->webAppUrl, ['action' => 'test']);

            if ($response->successful()) {
                $body = $response->json();
                if ($body['success'] ?? false) {
                    return [
                        'success' => true,
                        'message' => 'Koneksi berhasil',
                        'data' => $body['data'] ?? [],
                    ];
                }
                return ['success' => false, 'message' => $body['message'] ?? 'GAS error'];
            }

            return ['success' => false, 'message' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal koneksi: ' . $e->getMessage()];
        }
    }
}
