<?php

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\OrganisasiMahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Enums\UserRole;
// use App\Enums\OrganisasiStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads;

    // Properti Global
    public string $role = '';
    public bool $syarat_ketentuan = false;

    // Properti Mahasiswa
    public string $nama_mahasiswa = '';
    public string $nim = '';
    public $prodi_id_mahasiswa = '';
    public string $email_mahasiswa = '';
    public string $password_mahasiswa = '';

    // Properti Organisasi
    public string $nama_organisasi = '';
    public string $no_organisasi = '';
    public string $email_organisasi = '';
    public string $password_organisasi = '';
    public string $tingkat_organisasi = '';
    public $fakultas_id_organisasi = '';
    public $prodi_id_organisasi = '';
    public string $ig_url = '';
    public string $linkedin_url = '';
    public string $deskripsi = '';
    public string $visi = '';
    public string $misi = '';
    
    // File Uploads
    public $logo_url;
    public $ad_art;
    public $sk;

    public function with(): array
    {
        return [
            'daftar_fakultas' => Fakultas::all(), 
            'daftar_prodi' => Prodi::all(), 
        ];
    }

    public function register()
    {
        $this->validate([
            'role' => ['required', 'in:mahasiswa,organisasi'],
            'syarat_ketentuan' => ['accepted']
        ]);

        if ($this->role === 'mahasiswa') {
            $this->validate([
                'nama_mahasiswa' => ['required', 'string', 'max:255'],
                'nim' => ['required', 'string', 'max:50', 'unique:mahasiswa,nim'],
                'prodi_id_mahasiswa' => ['required', 'integer'],
                'email_mahasiswa' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'password_mahasiswa' => ['required', Rules\Password::defaults()],
            ]);
        } else {
            $this->validate([
                'nama_organisasi' => ['required', 'string', 'max:255'],
                'no_organisasi' => ['required', 'string', 'max:255'],
                'email_organisasi' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'password_organisasi' => ['required', Rules\Password::defaults()],
                'tingkat_organisasi' => ['required', 'string'],
                'fakultas_id_organisasi' => ['nullable', 'integer'],
                'prodi_id_organisasi' => ['nullable', 'integer'],
                'ig_url' => ['nullable', 'string', 'max:255'],
                'linkedin_url' => ['nullable', 'string', 'max:255'],
                'deskripsi' => ['required', 'string'],
                'visi' => ['required', 'string'],
                'misi' => ['required', 'string'],
                'logo_url' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                'ad_art' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                'sk' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            ]);
        }

        $user = DB::transaction(function () {
            
            if ($this->role === 'mahasiswa') {
                $user = User::create([
                    'name' => $this->nama_mahasiswa,
                    'email' => $this->email_mahasiswa,
                    'password' => Hash::make($this->password_mahasiswa),
                    'role' => 'mahasiswa', 
                ]);

                $user->assignRole('mahasiswa');

                Mahasiswa::create([
                    'user_id' => $user->id,
                    'nama' => $this->nama_mahasiswa,
                    'nim' => $this->nim,
                    'prodi_id' => $this->prodi_id_mahasiswa,
                ]);

            } else {
                $user = User::create([
                    'name' => $this->nama_organisasi,
                    'email' => $this->email_organisasi,
                    'password' => Hash::make($this->password_organisasi),
                    'role' => 'organisasi', 
                ]);

                $user->assignRole('organisasi');

                $logoPath = $this->logo_url ? $this->logo_url->store('logo', 'public') : null;
                $adArtPath = $this->ad_art ? $this->ad_art->store('dokumen/ad-art', 'public') : null;
                $skPath = $this->sk ? $this->sk->store('dokumen/sk', 'public') : null;

                OrganisasiMahasiswa::create([
                    'user_id' => $user->id,
                    'nama_organisasi' => $this->nama_organisasi,
                    'no_organisasi' => $this->no_organisasi,
                    'ig_url' => $this->ig_url,
                    'linkedin_url' => $this->linkedin_url,
                    'status' => 'pending', 
                    'tingkat_organisasi' => $this->tingkat_organisasi,
                    'fakultas_id' => $this->fakultas_id_organisasi ?: null,
                    'prodi_id' => $this->prodi_id_organisasi ?: null,
                    'logo_url' => $logoPath,
                    'deskripsi' => $this->deskripsi,
                    'visi' => $this->visi,
                    'misi' => $this->misi,
                    'ad_art' => $adArtPath,
                    'sk' => $skPath,
                ]);
            }

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        if ($this->role === 'mahasiswa') {
            return $this->redirect(route('mahasiswa.dashboard', absolute: false), navigate: true);
        } else {
            return $this->redirect(route('organisasi.dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div x-data="{ role: @entangle('role'), showPass: false, tingkat: @entangle('tingkat_organisasi') }" class="min-h-screen flex flex-col bg-surface-container-lowest">
    
    <header class="w-full border-b-[3px] border-primary py-5 bg-surface-container-lowest text-center shadow-sm">
        <a href="/" wire:navigate class="inline-flex items-center gap-2 text-2xl font-extrabold text-primary tracking-tight">
            <i class="fa-solid fa-graduation-cap"></i> Eventoria
        </a>
    </header>

    <main class="flex-grow w-full max-w-5xl mx-auto px-6 py-10">
        
        <div x-show="role === ''" x-transition.opacity.duration.500ms class="text-center py-10">
            <h1 class="text-headline-lg font-extrabold text-primary mb-3">Pilih Jenis Akun</h1>
            <p class="text-on-surface-variant text-body-lg mb-10 max-w-lg mx-auto">Silakan pilih jenis akun yang ingin Anda daftarkan untuk bergabung dengan ekosistem Eventoria.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                <div @click="role = 'mahasiswa'" class="group cursor-pointer rounded-xl border-2 border-outline-variant bg-surface-container-lowest p-8 hover:border-primary hover:shadow-card transition-all text-center">
                    <div class="w-16 h-16 mx-auto bg-primary-fixed text-primary rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <h3 class="text-title-lg font-bold text-primary mb-2">Mahasiswa</h3>
                    <p class="text-sm text-on-surface-variant">Daftar untuk mencari, mendaftar, dan mengikuti berbagai event kampus.</p>
                </div>

                <div @click="role = 'organisasi'" class="group cursor-pointer rounded-xl border-2 border-outline-variant bg-surface-container-lowest p-8 hover:border-primary hover:shadow-card transition-all text-center">
                    <div class="w-16 h-16 mx-auto bg-primary-fixed text-primary rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h3 class="text-title-lg font-bold text-primary mb-2">Organisasi</h3>
                    <p class="text-sm text-on-surface-variant">Daftar untuk membuat, mengelola, dan mempublikasikan event kampus Anda.</p>
                </div>
            </div>
        </div>

        <div x-show="role !== ''" style="display: none;" x-transition.opacity.duration.500ms>
            
            <button @click="role = ''" type="button" class="text-sm font-semibold text-outline hover:text-primary mb-6 flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali pilih peran
            </button>

            <div class="text-center mb-8 border-b border-outline-variant pb-8">
                <h1 class="text-headline-md font-extrabold text-primary mb-2">
                    <span x-show="role === 'organisasi'">Registrasi Organisasi</span>
                    <span x-show="role === 'mahasiswa'">Registrasi Mahasiswa</span>
                </h1>
                <p class="text-sm text-on-surface-variant">
                    <span x-show="role === 'organisasi'">Bergabunglah dengan ekosistem akademik Eventoria untuk mengelola kegiatan kampus Anda.</span>
                    <span x-show="role === 'mahasiswa'">Lengkapi data diri Anda untuk mulai mengeksplorasi kegiatan kampus.</span>
                </p>
            </div>

            <form wire:submit="register">
                            
                <div x-show="role === 'organisasi'" class="bg-[#e8f0fe] border border-[#d2e3fc] rounded-lg p-4 mb-8 flex gap-3 text-[13.5px] text-[#1967d2]">
                    <i class="fa-solid fa-circle-info mt-0.5"></i>
                    <p>Akun organisasi akan diperiksa dan menunggu persetujuan Admin Kampus. Lengkapi seluruh dokumen legalitas (AD/ART & SK) untuk mempercepat proses verifikasi.</p>
                </div>

                {{-- ========================================== --}}
                {{-- BLOK MAHASISWA                             --}}
                {{-- ========================================== --}}
                <div x-show="role === 'mahasiswa'" class="flex flex-col gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Nama Lengkap</label>
                            <input wire:model="nama_mahasiswa" type="text" placeholder="Masukkan nama lengkap Anda" :required="role === 'mahasiswa'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">NIM (Nomor Induk Mahasiswa)</label>
                            <input wire:model="nim" type="text" placeholder="Contoh: 2105551000" :required="role === 'mahasiswa'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Program Studi</label>
                            <select wire:model="prodi_id_mahasiswa" :required="role === 'mahasiswa'" class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm bg-transparent">
                                <option value="" disabled selected>Pilih Program Studi</option>
                                @foreach($daftar_prodi as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Email Kampus</label>
                            <input wire:model="email_mahasiswa" type="email" placeholder="mahasiswa@student.univ.ac.id" :required="role === 'mahasiswa'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Kata Sandi</label>
                            <div class="relative">
                                <input wire:model="password_mahasiswa" :type="showPass ? 'text' : 'password'" placeholder="••••••••" :required="role === 'mahasiswa'"
                                    class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 pr-10 transition-colors shadow-sm" />
                                <i @click="showPass = !showPass" :class="showPass ? 'fa-eye-slash' : 'fa-eye'" class="fa-regular absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer text-outline hover:text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- BLOK ORGANISASI                            --}}
                {{-- ========================================== --}}
                <div x-show="role === 'organisasi'" class="flex flex-col gap-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Nama Organisasi</label>
                            <input wire:model="nama_organisasi" type="text" placeholder="Contoh: BEM Fakultas Teknik" :required="role === 'organisasi'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Nomor Organisasi</label>
                            <input wire:model="no_organisasi" type="text" placeholder="Contoh: 08123456789" :required="role === 'organisasi'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Email Organisasi</label>
                            <input wire:model="email_organisasi" type="email" placeholder="organisasi@univ.ac.id" :required="role === 'organisasi'"
                                class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm" />
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Kata Sandi</label>
                            <div class="relative">
                                <input wire:model="password_organisasi" :type="showPass ? 'text' : 'password'" placeholder="••••••••" :required="role === 'organisasi'"
                                    class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 pr-10 transition-colors shadow-sm" />
                                <i @click="showPass = !showPass" :class="showPass ? 'fa-eye-slash' : 'fa-eye'" class="fa-regular absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer text-outline hover:text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Tingkat Organisasi</label>
                            <select x-model="tingkat" wire:model="tingkat_organisasi" class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 bg-transparent">
                                <option value="" disabled selected>Pilih Tingkat</option>
                                <option value="universitas">Universitas</option>
                                <option value="fakultas">Fakultas</option>
                                <option value="program_studi">Program Studi</option>
                            </select>
                        </div>

                        <!-- Conditional Fakultas / Prodi -->
                        <div x-show="tingkat === 'fakultas'" x-transition style="display: none;">
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Fakultas</label>
                            <select wire:model="fakultas_id_organisasi" class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 bg-transparent">
                                <option value="" selected>Pilih Fakultas</option>
                                @foreach($daftar_fakultas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="tingkat === 'program_studi'" x-transition style="display: none;">
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Program Studi</label>
                            <select wire:model="prodi_id_organisasi" class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 bg-transparent">
                                <option value="" selected>Pilih Program Studi</option>
                                @foreach($daftar_prodi as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Bagian 2: Dokumen Legalitas & Sosial Media -->
                    <div class="border-t border-outline-variant pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-6">
                            <div>
                                <label class="block text-[13px] font-bold text-on-surface mb-1.5">Logo Organisasi</label>
                                <label for="logo_upload" class="flex flex-col items-center justify-center w-full py-4 border-2 border-outline-variant border-dashed rounded-md cursor-pointer bg-surface-container-lowest hover:bg-surface-container-low transition-all">
                                    <i class="fa-solid fa-cloud-arrow-up text-outline text-xl mb-1"></i>
                                    <p class="text-[12px] text-on-surface font-semibold">{{ $logo_url ? $logo_url->getClientOriginalName() : 'Unggah Logo' }}</p>
                                    <p class="text-[11px] text-outline">PNG, JPG (Max 2MB)</p>
                                    <input wire:model="logo_url" id="logo_upload" type="file" class="hidden" accept="image/png, image/jpeg" />
                                </label>
                                @error('logo_url') <span class="text-[11px] text-error mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[13px] font-bold text-on-surface mb-1.5">Dokumen AD/ART</label>
                                    <label for="ad_art_upload" class="flex flex-col items-center justify-center w-full py-3 border border-outline-variant rounded-md cursor-pointer bg-surface-container-lowest hover:bg-surface-container-low transition-all">
                                        <i class="fa-solid fa-file-pdf text-outline text-lg mb-1"></i>
                                        <p class="text-[11px] text-on-surface font-medium truncate px-2 text-center w-full">{{ $ad_art ? $ad_art->getClientOriginalName() : 'Unggah File PDF' }}</p>
                                        <input wire:model="ad_art" id="ad_art_upload" type="file" class="hidden" accept="application/pdf" />
                                    </label>
                                    @error('ad_art') <span class="text-[11px] text-error mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-on-surface mb-1.5">Dokumen SK</label>
                                    <label for="sk_upload" class="flex flex-col items-center justify-center w-full py-3 border border-outline-variant rounded-md cursor-pointer bg-surface-container-lowest hover:bg-surface-container-low transition-all">
                                        <i class="fa-solid fa-file-pdf text-outline text-lg mb-1"></i>
                                        <p class="text-[11px] text-on-surface font-medium truncate px-2 text-center w-full">{{ $sk ? $sk->getClientOriginalName() : 'Unggah File PDF' }}</p>
                                        <input wire:model="sk" id="sk_upload" type="file" class="hidden" accept="application/pdf" />
                                    </label>
                                    @error('sk') <span class="text-[11px] text-error mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-6">
                            <div>
                                <label class="block text-[13px] font-bold text-on-surface mb-1.5">Link Instagram</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-outline-variant bg-surface-container text-outline text-[14px] font-medium">ig/</span>
                                    <input wire:model="ig_url" type="text" placeholder="username"
                                        class="flex-1 block w-full rounded-none rounded-r-md text-[14px] border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-on-surface mb-1.5">Link LinkedIn</label>
                                <div class="flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-outline-variant bg-surface-container text-outline text-[14px] font-medium">in/</span>
                                    <input wire:model="linkedin_url" type="text" placeholder="company"
                                        class="flex-1 block w-full rounded-none rounded-r-md text-[14px] border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian 3: Profil Organisasi -->
                    <div class="flex flex-col gap-5 border-t border-outline-variant pt-6">
                        <h3 class="text-title-lg font-bold text-primary mb-1">Informasi Profil Organisasi</h3>
                        <div>
                            <label class="block text-[13px] font-bold text-on-surface mb-1.5">Deskripsi Singkat Organisasi</label>
                            <textarea wire:model="deskripsi" rows="3" placeholder="Tuliskan gambaran umum organisasi Anda..." :required="role === 'organisasi'"
                                    class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm resize-y"></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[13px] font-bold text-on-surface mb-1.5">Visi</label>
                                <textarea wire:model="visi" rows="4" placeholder="Tuliskan visi organisasi..." :required="role === 'organisasi'"
                                        class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-[13px] font-bold text-on-surface mb-1.5">Misi</label>
                                <textarea wire:model="misi" rows="4" placeholder="Tuliskan misi organisasi..." :required="role === 'organisasi'"
                                        class="w-full text-[14px] rounded-md border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary py-2.5 px-3 transition-colors shadow-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- TOMBOL SUBMIT & ERRORS                     --}}
                {{-- ========================================== --}}
                <div class="mt-8 pt-6 border-t border-outline-variant flex flex-col sm:flex-row items-center justify-between gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="syarat_ketentuan" type="checkbox" required class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary shadow-sm cursor-pointer">
                        <span class="text-[13px] font-medium text-on-surface">Saya menyetujui syarat dan ketentuan layanan.</span>
                    </label>

                    <button type="submit" class="w-full sm:w-auto bg-primary text-on-primary font-bold text-[13.5px] px-8 py-2.5 rounded-md hover:bg-primary-container transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="register">
                            Daftar Sekarang
                        </span>
                        <span wire:loading wire:target="register">
                            Memproses...
                        </span>
                    </button>
                </div>
                
                @if($errors->any())
                    <div class="mt-4 p-3 bg-error-container text-on-error-container text-[12px] rounded-md">
                        Terjadi kesalahan. Pastikan semua field wajib terisi dan format file (PDF/Image) sesuai.
                        <ul class="list-disc pl-5 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </form>
        </div>

    </main>

    <footer class="w-full bg-surface-container-low py-6 text-center border-t border-outline-variant mt-auto">
        <p class="text-[13.5px] text-on-surface-variant font-medium">
            Sudah memiliki akun? <a wire:navigate href="{{ route('login') }}" class="font-bold text-primary hover:text-primary-container transition-colors">Masuk di sini</a>
        </p>
    </footer>

</div>