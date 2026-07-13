<div class="row border-bottom mb-3">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="judul" class="required">Judul</label>
            <input type="text" name="judul" id="judul" class="form-control border-dark"
                value="{{ old('judul', $data->judul ?? '') }}" {{ $readonly ? 'readonly' : '' }}>
            @error('judul')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <label for="deskripsi" class="required">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control border-dark" rows="10" {{ $readonly ? 'readonly' : '' }}>{{ old('deskripsi', $data->deskripsi ?? '') }}</textarea>
            @error('deskripsi')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <label for="keterangan" class="required">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control border-dark" rows="10">{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
            @error('keterangan')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
</div>
