<div class="form-group">
    <label>Judul</label>
    <input type="text" name="judul" class="form-control"
        value="{{ old('judul', $data->judul ?? '') }}" {{ $readonly ? 'readonly' : '' }}>
</div>

<div class="form-group">
    <label>Kategori</label>
    <input type="text" name="kategori" class="form-control"
        value="{{ old('kategori', $data->kategori ?? '') }}" {{ $readonly ? 'readonly' : '' }}>
</div>

<div class="form-group">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control" {{ $readonly ? 'readonly' : '' }}>
    {{ old('deskripsi', $data->deskripsi ?? '') }}
    </textarea>
</div>

<div class="form-group">
    <label>Gambar</label>
    @if (!$readonly)
        <input type="file" name="gambar" class="form-control">
    @endif

    @if (isset($data))
        <img src="{{ asset('upload/kolaborasi/' . $data->gambar) }}" width="120" class="mt-2">
    @endif
</div>
