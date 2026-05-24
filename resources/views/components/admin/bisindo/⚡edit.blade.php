<?php
use Livewire\Component;
use App\Models\BisindoMaterial;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public BisindoMaterial $material;

    #[Validate('required|string|min:3|max:120')]
    public $judul;

    #[Validate('required|string|min:2|max:60')]
    public $kategori = 'Umum';

    #[Validate('required|string|min:2|max:30')]
    public $tingkat = 'Pemula';

    #[Validate('nullable|string|max:255')]
    public $gambar_url;

    #[Validate('nullable|image|max:4096')]
    public $gambar_file;

    #[Validate('nullable|string|max:255')]
    public $video_url;

    #[Validate('nullable|mimes:mp4,webm,ogg|max:51200')]
    public $video_file;

    #[Validate('nullable|string|max:2000')]
    public $deskripsi;

    #[Validate('nullable|string|max:2000')]
    public $sumber_jurnal;

    public $is_active = true;

    public function mount($id)
    {
        $this->material = BisindoMaterial::findOrFail($id);
        $this->judul = $this->material->judul;
        $this->kategori = $this->material->kategori;
        $this->tingkat = $this->material->tingkat;
        $this->gambar_url = $this->material->gambar_url;
        $this->video_url = Schema::hasColumn('bisindo_materials', 'video_url') ? $this->material->video_url : null;
        $this->deskripsi = $this->material->deskripsi;
        $this->sumber_jurnal = Schema::hasColumn('bisindo_materials', 'sumber_jurnal') ? $this->material->sumber_jurnal : null;
        $this->is_active = (bool) $this->material->is_active;
    }

    public function save()
    {
        $this->validate();

        $gambarUrl = trim((string) $this->gambar_url);
        if (!$this->gambar_file && $gambarUrl === '') {
            $this->addError('gambar_file', 'Pilih file gambar atau isi URL gambar.');
            $this->addError('gambar_url', 'Pilih file gambar atau isi URL gambar.');
            return;
        }

        if ($this->gambar_file) {
            try {
                $dir = public_path('images/bisindo');
                File::ensureDirectoryExists($dir);

                $ext = strtolower($this->gambar_file->getClientOriginalExtension() ?: 'jpg');
                $filename = 'bisindo-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $ext;
                $source = $this->gambar_file->getRealPath();
                $target = $dir . DIRECTORY_SEPARATOR . $filename;

                if (!$source || !is_file($source) || !@copy($source, $target)) {
                    throw new RuntimeException('Gagal menyimpan file gambar. Pastikan folder public/images/bisindo dapat ditulis.');
                }

                $gambarUrl = '/images/bisindo/' . $filename;
            } catch (Throwable $e) {
                $this->addError('gambar_file', $e->getMessage());
                return;
            }
        }

        $videoUrl = trim((string) $this->video_url);
        if ($this->video_file) {
            try {
                $dir = public_path('videos/bisindo');
                File::ensureDirectoryExists($dir);

                $ext = strtolower($this->video_file->getClientOriginalExtension() ?: 'mp4');
                $filename = 'bisindo-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $ext;
                $source = $this->video_file->getRealPath();
                $target = $dir . DIRECTORY_SEPARATOR . $filename;

                if (!$source || !is_file($source) || !@copy($source, $target)) {
                    throw new RuntimeException('Gagal menyimpan file video. Pastikan folder public/videos/bisindo dapat ditulis.');
                }

                $videoUrl = '/videos/bisindo/' . $filename;
            } catch (Throwable $e) {
                $this->addError('video_file', $e->getMessage());
                return;
            }
        }

        $data = [
            'judul' => $this->judul,
            'kategori' => $this->kategori,
            'tingkat' => $this->tingkat,
            'gambar_url' => $gambarUrl,
            'video_url' => $videoUrl !== '' ? $videoUrl : null,
            'deskripsi' => $this->deskripsi,
            'is_active' => (bool) $this->is_active,
        ];

        if (Schema::hasColumn('bisindo_materials', 'sumber_jurnal')) {
            $data['sumber_jurnal'] = $this->sumber_jurnal;
        }

        if (!Schema::hasColumn('bisindo_materials', 'video_url')) {
            unset($data['video_url']);
        }

        $this->material->update($data);

        $this->dispatch('visual-feedback', message: 'Materi BISINDO berhasil diperbarui!');
        return redirect('/admin/bisindo');
    }
};
?>

<div class="max-w-3xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Edit Materi BISINDO</h1>
        <p class="text-slate-500 text-sm">Ubah poster/gambar materi untuk halaman belajar BISINDO.</p>
    </div>

    <form wire:submit="save" class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Judul</label>
            <input wire:model="judul" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('judul') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Kategori</label>
                <input wire:model="kategori" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                @error('kategori') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Tingkat</label>
                <select wire:model="tingkat" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
                    <option>Pemula</option>
                    <option>Menengah</option>
                    <option>Lanjutan</option>
                </select>
                @error('tingkat') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">URL Gambar/Poster</label>
            <input wire:model="gambar_url" type="text" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('gambar_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Upload Gambar Baru (File)</label>
            <input wire:model="gambar_file" type="file" accept="image/*" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('gambar_file') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror

            @if($gambar_file)
                <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="p-3 border-b border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Preview Upload</p>
                    </div>
                    <img src="{{ $gambar_file->temporaryUrl() }}" class="w-full h-auto" alt="Preview Gambar">
                </div>
            @elseif($gambar_url)
                <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gambar Saat Ini</p>
                        <a href="{{ $gambar_url }}" target="_blank" class="text-[10px] font-bold text-mdmc-700 uppercase tracking-widest">Buka</a>
                    </div>
                    <img src="{{ $gambar_url }}" class="w-full h-auto" alt="Gambar Saat Ini">
                </div>
            @endif
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Link Video (opsional)</label>
            <input wire:model="video_url" type="text" placeholder="https://youtube.com/... atau https://..." class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('video_url') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Upload Video Baru (File) (opsional)</label>
            <input wire:model="video_file" type="file" accept="video/mp4,video/webm,video/ogg" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all">
            @error('video_file') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror

            @if($video_file)
                <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="p-3 border-b border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Preview Upload</p>
                    </div>
                    <video class="w-full" controls>
                        <source src="{{ $video_file->temporaryUrl() }}">
                    </video>
                </div>
            @elseif($video_url)
                <div class="mt-3 bg-white border border-slate-100 rounded-2xl overflow-hidden">
                    <div class="p-3 border-b border-slate-100 flex items-center justify-between">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Video Saat Ini</p>
                        <a href="{{ $video_url }}" target="_blank" class="text-[10px] font-bold text-mdmc-700 uppercase tracking-widest">Buka</a>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Deskripsi (opsional)</label>
            <textarea wire:model="deskripsi" rows="4" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
            @error('deskripsi') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest ml-1">Sumber Jurnal / Referensi (opsional)</label>
            <textarea wire:model="sumber_jurnal" rows="3" placeholder="Contoh: Fauziyah 2022; Christianingsih et al. 2025" class="w-full px-5 py-4 rounded-2xl border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-red-500/10 focus:border-red-500 outline-none transition-all"></textarea>
            @error('sumber_jurnal') <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="bg-slate-50 border border-slate-100 rounded-3xl p-6 flex items-center justify-between">
            <div>
                <p class="font-bold text-slate-900">Status Materi</p>
                <p class="text-sm text-slate-500">Jika nonaktif, tidak tampil di publik.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                <div class="w-12 h-7 bg-slate-200 rounded-full peer peer-checked:bg-red-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:after:translate-x-5"></div>
            </label>
        </div>

        <div class="flex gap-3">
            <a href="/admin/bisindo" wire:navigate class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 py-4 rounded-2xl font-bold text-center border border-slate-100">Batal</a>
            <button type="submit" class="flex-1 bg-mdmc-700 hover:bg-mdmc-800 text-white py-4 rounded-2xl font-bold transition-all shadow-xl shadow-mdmc-100">Simpan</button>
        </div>
    </form>
</div>
