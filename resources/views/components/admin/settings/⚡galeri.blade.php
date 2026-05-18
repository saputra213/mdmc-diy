<?php
use Livewire\Component;
use App\Models\DisasterEvent;
use App\Models\DisasterEventPhoto;
use App\Models\Setting;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    #[Validate('required|string|min:3|max:80')]
    public $gallery_title;

    #[Validate('required|string|min:10|max:240')]
    public $gallery_excerpt;

    public $selected_event_id;

    #[Validate('nullable|url|max:2048')]
    public $photo_url;

    #[Validate('nullable|string|max:1000')]
    public $photo_description;

    #[Validate('nullable|image|max:4096')]
    public $photo_upload;

    #[Validate('nullable|string|max:1000')]
    public $upload_description;

    public $descriptions = [];

    public function mount()
    {
        $this->gallery_title = Setting::get('guest.gallery_title', 'Galeri');
        $this->gallery_excerpt = Setting::get('guest.gallery_excerpt', 'Dokumentasi kegiatan koordinasi dan distribusi.');

        $this->selected_event_id = (int) (session('admin_disaster_event_id') ?: DisasterEvent::orderByDesc('updated_at')->value('id'));
        $this->hydrateDescriptions();
    }

    public function updatedSelectedEventId($value): void
    {
        $this->selected_event_id = $value ? (int) $value : null;
        $this->hydrateDescriptions();
    }

    private function hydrateDescriptions(): void
    {
        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        if (!$eventId) {
            $this->descriptions = [];
            return;
        }

        $this->descriptions = DisasterEventPhoto::query()
            ->where('disaster_event_id', $eventId)
            ->pluck('description', 'id')
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        Setting::set('guest.gallery_title', $this->gallery_title, 'string');
        Setting::set('guest.gallery_excerpt', $this->gallery_excerpt, 'text');

        $this->dispatch('visual-feedback', message: 'Pengaturan Galeri berhasil disimpan!');
    }

    public function addFromUrl(): void
    {
        $this->validateOnly('photo_url');

        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        if (!$eventId) {
            session()->flash('error', 'Pilih event terlebih dahulu.');
            return;
        }

        $url = trim((string) $this->photo_url);
        if ($url === '') {
            session()->flash('error', 'URL foto wajib diisi.');
            return;
        }

        DisasterEventPhoto::create([
            'disaster_event_id' => $eventId,
            'image_path' => $url,
            'description' => $this->photo_description ? trim((string) $this->photo_description) : null,
        ]);

        $this->reset(['photo_url', 'photo_description']);
        $this->hydrateDescriptions();
        $this->dispatch('visual-feedback', message: 'Foto berhasil ditambahkan.');
    }

    public function uploadPhoto(): void
    {
        $this->validateOnly('photo_upload');

        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        if (!$eventId) {
            session()->flash('error', 'Pilih event terlebih dahulu.');
            return;
        }

        if (!$this->photo_upload) {
            session()->flash('error', 'File foto wajib dipilih.');
            return;
        }

        $dir = public_path('images/gallery/event-' . $eventId);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext = strtolower($this->photo_upload->getClientOriginalExtension() ?: 'jpg');
        $filename = 'photo-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $ext;
        $this->photo_upload->move($dir, $filename);

        DisasterEventPhoto::create([
            'disaster_event_id' => $eventId,
            'image_path' => 'images/gallery/event-' . $eventId . '/' . $filename,
            'description' => $this->upload_description ? trim((string) $this->upload_description) : null,
        ]);

        $this->reset(['photo_upload', 'upload_description']);
        $this->hydrateDescriptions();
        $this->dispatch('visual-feedback', message: 'Foto berhasil diunggah.');
    }

    public function saveDescriptions(): void
    {
        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        if (!$eventId) {
            session()->flash('error', 'Pilih event terlebih dahulu.');
            return;
        }

        foreach ($this->descriptions as $id => $desc) {
            $photo = DisasterEventPhoto::query()
                ->whereKey((int) $id)
                ->where('disaster_event_id', $eventId)
                ->first();

            if (!$photo) {
                continue;
            }

            $desc = trim((string) $desc);
            $photo->update([
                'description' => $desc === '' ? null : $desc,
            ]);
        }

        $this->dispatch('visual-feedback', message: 'Deskripsi foto berhasil disimpan.');
    }

    public function deletePhoto(int $id): void
    {
        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        if (!$eventId) {
            return;
        }

        $photo = DisasterEventPhoto::query()
            ->whereKey($id)
            ->where('disaster_event_id', $eventId)
            ->first();

        if (!$photo) {
            return;
        }

        $path = (string) $photo->image_path;
        $photo->delete();

        if (!str_starts_with($path, 'http://') && !str_starts_with($path, 'https://')) {
            $full = public_path($path);
            if (is_file($full)) {
                @unlink($full);
            }
        }

        unset($this->descriptions[$id]);
        $this->descriptions = $this->descriptions;

        $this->dispatch('visual-feedback', message: 'Foto berhasil dihapus.');
    }

    public function with(): array
    {
        $events = DisasterEvent::orderByRaw("case when status = 'active' then 0 else 1 end")
            ->orderByDesc('updated_at')
            ->get();

        $eventId = $this->selected_event_id ? (int) $this->selected_event_id : null;
        $photos = collect();
        $selectedEvent = null;
        if ($eventId) {
            $selectedEvent = $events->firstWhere('id', $eventId) ?: DisasterEvent::find($eventId);
            $photos = DisasterEventPhoto::query()
                ->where('disaster_event_id', $eventId)
                ->orderByDesc('id')
                ->get();
        }

        return [
            'events' => $events,
            'selectedEvent' => $selectedEvent,
            'photos' => $photos,
        ];
    }
};
?>

<div class="space-y-6">
    <div class="flex items-start justify-between gap-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pengaturan Galeri (Guest)</h1>
            <p class="text-slate-500 text-sm">Galeri dibuat dalam folder per event bencana. Setiap foto bisa diberi deskripsi.</p>
        </div>
        <a href="/galeri" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg shadow-slate-200 text-sm">
            Preview Galeri
        </a>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul Galeri</label>
                <input wire:model="gallery_title" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                @error('gallery_title') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Ringkasan</label>
                <textarea wire:model="gallery_excerpt" rows="3" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all"></textarea>
                @error('gallery_excerpt') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100 flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Judul & Ringkasan
            </button>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-slate-900">Folder Event</p>
                        <p class="text-sm text-slate-500">Pilih event untuk mengelola foto-fotonya.</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Pilih Event</label>
                    <select wire:model="selected_event_id" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                        <option value="">Pilih event...</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev->id }}">{{ $ev->status === 'active' ? '● ' : '' }}{{ $ev->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 space-y-8">
        <div class="flex items-start justify-between gap-6">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Foto Event</h2>
                <p class="text-slate-500 text-sm">Tambah foto lewat link atau unggah. Klik foto di publik untuk melihat deskripsi.</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Event terpilih</p>
                <p class="text-sm font-extrabold text-slate-900">{{ $selectedEvent?->name ?: '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <p class="font-extrabold text-slate-900">Tambah via URL</p>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">URL Foto</label>
                    <input wire:model="photo_url" type="text" placeholder="https://..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                    @error('photo_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                    <textarea wire:model="photo_description" rows="3" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all"></textarea>
                </div>
                <button type="button" wire:click="addFromUrl" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 rounded-2xl font-extrabold transition-all">
                    Tambah Foto (URL)
                </button>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 space-y-4">
                <p class="font-extrabold text-slate-900">Unggah Foto</p>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">File Foto</label>
                    <input wire:model="photo_upload" type="file" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all">
                    @error('photo_upload') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi</label>
                    <textarea wire:model="upload_description" rows="3" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:ring-4 focus:ring-mdmc-600/10 focus:border-mdmc-600 outline-none transition-all"></textarea>
                </div>
                <button type="button" wire:click="uploadPhoto" class="w-full bg-mdmc-700 hover:bg-mdmc-800 text-white py-3 rounded-2xl font-extrabold transition-all">
                    Unggah Foto
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between gap-6">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Daftar Foto</p>
                <p class="text-sm text-slate-500">Edit deskripsi lalu simpan.</p>
            </div>
            <button type="button" wire:click="saveDescriptions" class="bg-mdmc-700 hover:bg-mdmc-800 text-white px-4 py-2 rounded-xl font-extrabold text-sm shadow-sm shadow-mdmc-100">
                Simpan Deskripsi
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @forelse($photos as $photo)
                @php
                    $src = str_starts_with($photo->image_path, 'http://') || str_starts_with($photo->image_path, 'https://')
                        ? $photo->image_path
                        : asset($photo->image_path);
                @endphp
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                    <div class="aspect-square bg-slate-100">
                        <img src="{{ $src }}" class="w-full h-full object-cover" alt="">
                    </div>
                    <div class="p-3 space-y-2">
                        <textarea wire:model="descriptions.{{ $photo->id }}" rows="3" placeholder="Deskripsi..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-mdmc-600/20 focus:border-mdmc-600 outline-none"></textarea>
                        <button type="button" wire:click="deletePhoto({{ $photo->id }})" wire:confirm="Yakin ingin menghapus foto ini?" class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2 rounded-xl font-extrabold text-xs">
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-slate-50 border border-slate-100 rounded-3xl p-10 text-center text-slate-500 font-semibold">
                    Belum ada foto untuk event ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
