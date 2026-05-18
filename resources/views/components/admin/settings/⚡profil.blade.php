<?php
use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:80')]
    public $profile_title;

    #[Validate('required|string|min:10|max:240')]
    public $profile_excerpt;

    #[Validate('required|string|min:10|max:2000')]
    public $profile_body;

    public $profile_focus = [];

    public function mount()
    {
        $this->profile_title = Setting::get('guest.profile_title', 'Profil MDMC DIY');
        $this->profile_excerpt = Setting::get('guest.profile_excerpt', 'Mengenal peran, fokus, dan cara kerja MDMC DIY dalam kesiapsiagaan dan respons bencana.');
        $this->profile_body = Setting::get('guest.profile_body', 'MDMC DIY berfokus pada kesiapsiagaan, respons, dan pemulihan bencana melalui koordinasi relawan, pengelolaan logistik, serta edukasi mitigasi untuk masyarakat.');

        $focus = json_decode(Setting::get('guest.profile_focus', ''), true);
        $this->profile_focus = is_array($focus) && count($focus) ? $focus : [
            'Manajemen logistik tanggap darurat yang transparan dan terukur.',
            'Edukasi mitigasi berbasis komunitas untuk meningkatkan kesiapsiagaan.',
            'Koordinasi cepat lintas mitra dalam situasi darurat.',
        ];
    }

    public function addFocus()
    {
        $this->profile_focus[] = '';
    }

    public function removeFocus($index)
    {
        unset($this->profile_focus[$index]);
        $this->profile_focus = array_values($this->profile_focus);
    }

    public function save()
    {
        $this->validate();

        $focus = array_values(array_filter(array_map(fn ($v) => trim((string) $v), $this->profile_focus), fn ($v) => $v !== ''));

        Setting::set('guest.profile_title', $this->profile_title, 'string');
        Setting::set('guest.profile_excerpt', $this->profile_excerpt, 'text');
        Setting::set('guest.profile_body', $this->profile_body, 'text');
        Setting::set('guest.profile_focus', json_encode($focus, JSON_UNESCAPED_UNICODE), 'json');

        $this->dispatch('visual-feedback', message: 'Pengaturan Profil berhasil disimpan!');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Profil (Guest)</h1>
            <p class="text-slate-500 text-sm">Atur judul, ringkasan, deskripsi, dan fokus profil.</p>
        </div>
        <a href="/profil" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Profil
        </a>
    </div>

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul Profil</label>
                <input wire:model="profile_title" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('profile_title') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Ringkasan Profil</label>
                <textarea wire:model="profile_excerpt" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                @error('profile_excerpt') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi Profil</label>
                <textarea wire:model="profile_body" rows="6" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                @error('profile_body') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900">Fokus Profil</p>
                        <p class="text-sm text-slate-500">Daftar poin fokus yang tampil di halaman Profil.</p>
                    </div>
                    <button type="button" wire:click="addFocus" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-2 rounded-xl font-bold text-xs">Tambah</button>
                </div>

                <div class="space-y-3">
                    @foreach($profile_focus as $i => $item)
                        <div class="flex items-start gap-2">
                            <textarea wire:model="profile_focus.{{ $i }}" rows="2" class="flex-1 px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                            <button type="button" wire:click="removeFocus({{ $i }})" class="px-3 py-3 rounded-2xl bg-red-50 text-red-600 font-bold hover:bg-red-100">×</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
