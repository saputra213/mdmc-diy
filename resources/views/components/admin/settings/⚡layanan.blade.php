<?php
use Livewire\Component;
use App\Models\Setting;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|min:3|max:80')]
    public $services_title;

    #[Validate('required|string|min:10|max:240')]
    public $services_excerpt;

    public $services_cards = [];

    public function mount()
    {
        $this->services_title = Setting::get('guest.services_title', 'Layanan Informasi');
        $this->services_excerpt = Setting::get('guest.services_excerpt', 'Ringkasan layanan untuk kebutuhan tanggap darurat.');

        $cards = json_decode(Setting::get('guest.services_cards', ''), true);
        $this->services_cards = is_array($cards) && count($cards) ? $cards : [
            ['label' => 'Info Logistik', 'title' => 'Ketersediaan Stok', 'desc' => 'Pantau ringkas ketersediaan barang dan prioritas kebutuhan.'],
            ['label' => 'Mitigasi', 'title' => 'Panduan Siaga', 'desc' => 'Video dan ringkasan langkah cepat untuk berbagai skenario bencana.'],
            ['label' => 'Kontak', 'title' => 'Koordinasi Relawan', 'desc' => 'Informasi koordinasi dan komunikasi untuk mitra lapangan.'],
        ];
    }

    public function addCard()
    {
        $this->services_cards[] = ['label' => 'Layanan', 'title' => '', 'desc' => ''];
    }

    public function removeCard($index)
    {
        unset($this->services_cards[$index]);
        $this->services_cards = array_values($this->services_cards);
    }

    public function save()
    {
        $this->validate();

        $cards = [];
        foreach ($this->services_cards as $c) {
            $label = trim((string) ($c['label'] ?? ''));
            $title = trim((string) ($c['title'] ?? ''));
            $desc = trim((string) ($c['desc'] ?? ''));
            if ($title === '' && $desc === '') {
                continue;
            }
            $cards[] = [
                'label' => $label !== '' ? $label : 'Layanan',
                'title' => $title !== '' ? $title : '-',
                'desc' => $desc,
            ];
        }

        Setting::set('guest.services_title', $this->services_title, 'string');
        Setting::set('guest.services_excerpt', $this->services_excerpt, 'text');
        Setting::set('guest.services_cards', json_encode($cards, JSON_UNESCAPED_UNICODE), 'json');

        $this->dispatch('visual-feedback', message: 'Pengaturan Layanan berhasil disimpan!');
    }
};
?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Layanan (Guest)</h1>
            <p class="text-slate-500 text-sm">Atur judul, ringkasan, dan kartu layanan.</p>
        </div>
        <a href="/layanan" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Layanan
        </a>
    </div>

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul Layanan</label>
                    <input wire:model="services_title" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    @error('services_title') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Ringkasan</label>
                    <textarea wire:model="services_excerpt" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                    @error('services_excerpt') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900">Kartu Layanan</p>
                        <p class="text-sm text-slate-500">Tampil di halaman Layanan dan highlight di Beranda.</p>
                    </div>
                    <button type="button" wire:click="addCard" class="bg-slate-900 hover:bg-slate-800 text-white px-3 py-2 rounded-xl font-bold text-xs">Tambah</button>
                </div>

                <div class="space-y-4">
                    @foreach($services_cards as $i => $card)
                        <div class="bg-white rounded-3xl border border-slate-200 p-5 space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Item {{ $i + 1 }}</p>
                                <button type="button" wire:click="removeCard({{ $i }})" class="text-red-600 font-bold">Hapus</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Label</label>
                                    <input wire:model="services_cards.{{ $i }}.label" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
                                    <input wire:model="services_cards.{{ $i }}.title" type="text" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                                <textarea wire:model="services_cards.{{ $i }}.desc" rows="2" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Simpan Pengaturan
        </button>
    </form>
</div>
