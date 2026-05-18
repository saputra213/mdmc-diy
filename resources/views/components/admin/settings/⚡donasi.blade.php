<?php

use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|url|max:2048')]
    public $baznas_url;

    #[Validate('required|string|min:8|max:24')]
    public $mdmc_wa_number;

    #[Validate('required|string|min:10|max:500')]
    public $wa_template;

    public function mount()
    {
        $this->baznas_url = Setting::get('guest.donation_baznas_url', 'https://baznas.go.id/');
        $this->mdmc_wa_number = Setting::get('guest.donation_wa_number', '6280000000000');
        $this->wa_template = Setting::get('guest.donation_wa_template', 'Halo MDMC DIY, saya ingin donasi barang untuk event {event}. Saya tertarik membantu kebutuhan berikut:\n{items}\n\nMohon info lokasi drop-off dan prosedur selanjutnya.');
    }

    public function save()
    {
        $this->validate();

        Setting::set('guest.donation_baznas_url', trim((string) $this->baznas_url), 'url');
        Setting::set('guest.donation_wa_number', preg_replace('/\s+/', '', (string) $this->mdmc_wa_number), 'string');
        Setting::set('guest.donation_wa_template', (string) $this->wa_template, 'text');

        $this->dispatch('visual-feedback', message: 'Pengaturan Donasi berhasil disimpan!');
    }
};

?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Donasi (Guest)</h1>
            <p class="text-slate-500 text-sm">Atur tautan donasi tunai dan nomor WhatsApp MDMC untuk donasi barang.</p>
        </div>
        <a href="/donasi" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Donasi
        </a>
    </div>

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">URL Donasi Tunai (BAZNAS)</label>
                <input wire:model="baznas_url" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('baznas_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Contoh: https://baznas.go.id/ atau tautan kampanye BAZNAS daerah.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp MDMC</label>
                <input wire:model="mdmc_wa_number" type="text" placeholder="62812xxxxxxx" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('mdmc_wa_number') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Gunakan format internasional tanpa +, contoh 628123456789.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Template Pesan WhatsApp</label>
                <textarea wire:model="wa_template" rows="10" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all font-mono text-sm"></textarea>
                @error('wa_template') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                <p class="text-[11px] text-slate-400">Placeholder: {event} dan {items}.</p>
            </div>

            <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

