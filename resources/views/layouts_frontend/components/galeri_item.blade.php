<div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
    <div class="gallery-item h-100">
        <img src="{{ asset('travela-1.0.0/img/'.$item->gambar) }}"
            class="img-fluid w-100 h-100 rounded">

        <div class="gallery-content">
            <div class="gallery-info">
                <h5 class="text-white">{{ $item->judul }}</h5>
            </div>
        </div>

        <div class="gallery-plus-icon">
            <a href="{{ asset('travela-1.0.0/img/'.$item->gambar) }}">
                <i class="fas fa-plus fa-2x text-white"></i>
            </a>
        </div>
    </div>
</div>