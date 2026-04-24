<div class="form-group">

    <label>Nama</label>
    <input type="text" name="nama" class="form-control"
        value="{{ old('nama', $data->nama ?? '') }}" {{ $readonly ? 'readonly' : '' }}>

    <label>Kategori</label>
    <select name="kategori" class="form-control" {{ $readonly ? 'disabled' : '' }}>
        <option value="Pusat">Pusat</option>
        <option value="Utara">Utara</option>
        <option value="Timur">Timur</option>
        <option value="Barat">Barat</option>
        <option value="Selatan">Selatan</option>
    </select>

    <label>Gambar</label>
    @if(!$readonly)
    <input type="file" name="gambar" class="form-control">
    @endif

    @if(isset($data->gambar))
    <br>
    <img src="{{ asset('upload/destinasi/' . $data->gambar) }}" width="120">
    @endif

</div>