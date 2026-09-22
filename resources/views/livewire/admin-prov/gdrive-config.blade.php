<div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">
            <i class="fab fa-google-drive mr-2 text-green-600"></i>Konfigurasi Google Drive
        </h3>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">GAS Web App URL *</label>
                <input type="url" wire:model.live="gasUrl"
                       placeholder="https://script.google.com/macros/s/xxxxx/exec"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-400">
                    Deploy Code.gs ke Google Apps Script → Deploy as Web App → Copy URL ke sini.
                </p>
            </div>

            <div class="flex gap-2">
                <button wire:click="save"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">
                    <span wire:loading.remove wire:target="save"><i class="fas fa-save me-1"></i> Simpan</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...</span>
                </button>
                <button wire:click="testConnection"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    <span wire:loading.remove wire:target="testConnection"><i class="fas fa-plug me-1"></i> Test Koneksi</span>
                    <span wire:loading wire:target="testConnection"><i class="fas fa-spinner fa-spin me-1"></i> Menghubungi GAS...</span>
                </button>
            </div>

            {{-- Test Result --}}
            @if($testResult)
            <div class="p-4 rounded-lg {{ $testResult['success'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <p class="font-medium text-sm {{ $testResult['success'] ? 'text-green-800' : 'text-red-800' }}">
                    {{ $testResult['success'] ? 'Koneksi Berhasil' : 'Koneksi Gagal' }}
                </p>
                <p class="text-sm {{ $testResult['success'] ? 'text-green-600' : 'text-red-600' }}">
                    {{ $testResult['message'] }}
                </p>
                @if(isset($testResult['data']))
                <pre class="mt-2 text-xs text-gray-600 bg-white p-2 rounded">{{ json_encode($testResult['data'], JSON_PRETTY_PRINT) }}</pre>
                @endif
            </div>
            @endif

            {{-- Status --}}
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <strong>Status saat ini:</strong>
                    @if(config('gdrive.webapp_url'))
                        <span class="text-green-600 font-medium">Aktif</span>
                        <span class="text-xs text-gray-400 block mt-1 break-all">{{ config('gdrive.webapp_url') }}</span>
                    @else
                        <span class="text-gray-400">Belum dikonfigurasi</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
