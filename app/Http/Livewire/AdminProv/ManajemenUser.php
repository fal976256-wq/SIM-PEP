<?php

namespace App\Http\Livewire\AdminProv;

use App\Models\MasterAnggotaDewan;
use App\Models\User;
use Livewire\Component;

class ManajemenUser extends Component
{
    public $showForm = false;
    public $editId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $role = 'UTUSAN_DEWAN';
    public $anggotaDewanId = '';
    public $noHp = '';
    public $search = '';

    public function getUserList()
    {
        $query = User::with('anggotaDewan');

        if ($this->search) {
            $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $this->search);
            $query->where(function ($q) use ($escaped) {
                $q->where('name', 'like', "%{$escaped}%", 'and')
                  ->orWhere('email', 'like', "%{$escaped}%", 'and')
                  ->orWhereHas('anggotaDewan', function ($q2) use ($escaped) {
                      $q2->where('nama', 'like', "%{$escaped}%", 'and')
                         ->orWhere('dapil', 'like', "%{$escaped}%", 'and');
                  });
            });
        }

        return $query->latest()->paginate(15);
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function updatedAnggotaDewanId(): void
    {
        if ($this->anggotaDewanId) {
            $dewan = MasterAnggotaDewan::find($this->anggotaDewanId);
            if ($dewan) {
                $this->name = $dewan->nama;
            }
        }
    }

    public function edit($id): void
    {
        $user = User::findOrFail($id);
        $this->editId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->anggotaDewanId = $user->anggota_dewan_id ?? '';
        $this->noHp = $user->no_hp ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        if (empty($this->name) || empty($this->email)) {
            session()->flash('error', 'Nama dan email wajib diisi!');
            return;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            session()->flash('error', 'Format email tidak valid!');
            return;
        }

        // Email uniqueness check
        $emailExists = User::where('email', $this->email)
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->exists();
        if ($emailExists) {
            session()->flash('error', 'Email sudah digunakan oleh user lain!');
            return;
        }

        if (!$this->editId && empty($this->password)) {
            session()->flash('error', 'Password wajib diisi untuk user baru!');
            return;
        }

        // Password validation: minimum 8 characters
        if (!empty($this->password) && strlen($this->password) < 8) {
            session()->flash('error', 'Password minimal 8 karakter!');
            return;
        }

        // Phone validation (if provided)
        if (!empty($this->noHp) && !preg_match('/^0[0-9]{9,12}$/', $this->noHp)) {
            session()->flash('error', 'Format nomor HP tidak valid! Gunakan format 08xxxxxxxxxx');
            return;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'anggota_dewan_id' => $this->anggotaDewanId ?: null,
            'no_hp' => $this->noHp ?: null,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->editId) {
            User::findOrFail($this->editId)->update($data);
            session()->flash('success', 'User berhasil diupdate!');
        } else {
            User::create($data);
            session()->flash('success', 'User berhasil ditambahkan!');
        }

        $this->resetForm();
    }

    public function toggleActive($id): void
    {
        abort_if($id === auth()->id(), 400, 'Tidak dapat menonaktifkan akun sendiri.');

        $user = User::findOrFail($id);
        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);
        $status = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('success', "User {$status}!");
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'UTUSAN_DEWAN';
        $this->anggotaDewanId = '';
        $this->noHp = '';
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.admin-prov.manajemen-user', [
            'userList' => $this->getUserList(),
            'anggotaList' => MasterAnggotaDewan::active()->orderBy('dapil')->orderBy('nama')->get()->groupBy('dapil'),
        ]);
    }
}
