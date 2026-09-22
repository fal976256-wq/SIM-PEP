<?php

namespace App\Http\Livewire\AdminProv;

use App\Services\GdriveService;
use Livewire\Component;

class GdriveConfig extends Component
{
    public $gasUrl = '';
    public $testResult = null;

    public function mount(): void
    {
        $this->gasUrl = config('gdrive.webapp_url', '');
    }

    public function save(): void
    {
        if (empty($this->gasUrl)) {
            session()->flash('error', 'URL GAS Web App wajib diisi!');
            return;
        }

        // Validate URL format
        if (!filter_var($this->gasUrl, FILTER_VALIDATE_URL)) {
            session()->flash('error', 'Format URL tidak valid!');
            return;
        }

        // Save to .env — we'll update the config file instead
        $envPath = base_path('.env');
        $currentContent = file_get_contents($envPath);

        if (str_contains($currentContent, 'GAS_WEBAPP_URL=')) {
            $newContent = preg_replace(
                '/GAS_WEBAPP_URL=.*/',
                'GAS_WEBAPP_URL="' . $this->gasUrl . '"',
                $currentContent
            );
        } else {
            $newContent = $currentContent . "\nGAS_WEBAPP_URL=\"" . $this->gasUrl . "\"\n";
        }

        file_put_contents($envPath, $newContent);

        session()->flash('success', 'GAS Web App URL berhasil disimpan! Jalankan `php artisan config:clear` untuk menerapkan.');
    }

    public function testConnection(): void
    {
        // Temporarily set config for testing
        config(['gdrive.webapp_url' => $this->gasUrl]);

        $service = new GdriveService();
        $this->testResult = $service->testConnection();
    }

    public function render()
    {
        return view('livewire.admin-prov.gdrive-config');
    }
}
