<!-- resources/views/layouts_backend/poi/form_poi.blade.php -->
<div class="form-group">
    <label>Nama POI <span class="text-danger">*</span></label>
    <input type="text" name="name" id="poi_name" class="form-control"
        value="{{ old('name', $data->name ?? '') }}" required {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    <small class="text-muted">Minimal 3 karakter, tanpa karakter khusus.</small>
</div>

<div class="form-group">
    <label>Kategori <span class="text-danger">*</span></label>
    <input type="text" name="category" id="poi_category" class="form-control"
        value="{{ old('category', $data->category ?? '') }}" required {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('category') <small class="text-danger">{{ $message }}</small> @enderror
    <small class="text-muted">Contoh: hospital, school, cafe, bank, mall, park, dll.</small>
</div>

<div class="form-group">
    <label>Latitude <span class="text-danger">*</span></label>
    <input type="number" step="any" name="lat" id="poi_lat" class="form-control"
        value="{{ old('lat', $data->lat ?? '') }}" required {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('lat') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label>Longitude <span class="text-danger">*</span></label>
    <input type="number" step="any" name="lng" id="poi_lng" class="form-control"
        value="{{ old('lng', $data->lng ?? '') }}" required {{ $readonly ?? false ? 'readonly' : '' }}>
    @error('lng') <small class="text-danger">{{ $message }}</small> @enderror
</div>

@if(isset($data) && !empty($data->osm_id))
<div class="form-group">
    <label>OSM ID (otomatis)</label>
    <input type="text" class="form-control" value="{{ $data->osm_id }}" readonly disabled>
</div>
@endif

@push('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        let name = document.getElementById('poi_name');
        let category = document.getElementById('poi_category');
        let lat = document.getElementById('poi_lat');
        let lng = document.getElementById('poi_lng');
        let errors = [];

        if (!name.value.trim()) errors.push('Nama POI tidak boleh kosong');
        else if (name.value.trim().length < 3) errors.push('Nama POI minimal 3 karakter');

        if (!category.value.trim()) errors.push('Kategori tidak boleh kosong');

        let latVal = parseFloat(lat.value);
        let lngVal = parseFloat(lng.value);
        if (isNaN(latVal) || latVal < -90 || latVal > 90) errors.push('Latitude tidak valid (rentang -90 s.d 90)');
        if (isNaN(lngVal) || lngVal < -180 || lngVal > 180) errors.push('Longitude tidak valid (rentang -180 s.d 180)');

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
</script>
@endpush