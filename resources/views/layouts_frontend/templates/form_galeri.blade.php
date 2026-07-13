<div class="form-group">

    <label>Judul</label>
    <input type="text" name="judul" class="form-control"
        value="{{ old('judul', $data->judul ?? '') }}"
        {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('judul')
        <small class="text-danger">{{ $message }}</small>
    @enderror

    <label>Kategori</label>
    <input type="text" name="kategori" class="form-control"
        value="{{ old('kategori', $data->kategori ?? '') }}"
        {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('kategori')
        <small class="text-danger">{{ $message }}</small>
    @enderror

    <label>Gambar</label>
    <input type="file" name="gambar" class="form-control"
        {{ $readonly ?? false ? 'disabled' : '' }}>
    @error('gambar')
        <small class="text-danger">{{ $message }}</small>
    @enderror

    @if (isset($data->gambar))
        <br>
        <img src="{{ asset('travela-1.0.0/img/' . $data->gambar) }}" width="120">
    @endif

</div>
