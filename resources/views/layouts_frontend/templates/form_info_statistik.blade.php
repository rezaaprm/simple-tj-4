<div class="row border-bottom mb-3">
    <div class="col-sm-12">
        <div class="form-group">

            <label>Jenis Data</label>
            <input type="text" name="jenis_data" class="form-control border-dark"
                value="{{ old('jenis_data', $data->jenis_data ?? '') }}" {{ $readonly ? 'readonly' : '' }}>
            @error('jenis_data')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <label>Jumlah</label>
            <input type="text" name="jumlah" class="form-control border-dark"
                value="{{ old('jumlah', $data->jumlah ?? '') }}" {{ $readonly ? 'readonly' : '' }}>
            @error('jumlah')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control border-dark" rows="5" {{ $readonly ? 'readonly' : '' }}>{{ old('keterangan', $data->keterangan ?? '') }}</textarea>
            @error('keterangan')
                <small class="text-danger">{{ $message }}</small>
            @enderror

        </div>
    </div>
</div>
