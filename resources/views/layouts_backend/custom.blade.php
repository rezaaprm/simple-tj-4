@if ($paginator->hasPages())
    <nav>
        <ul class="pagination pagination-sm justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&rsaquo;</span>
                </li>
            @endif

            {{-- Lompat Halaman --}}
            <li class="page-item">
                <div class="d-flex align-items-center" style="margin-left: 10px;">
                    <input type="number"
                        id="page-input-{{ uniqid() }}"
                        class="form-control form-control-sm page-jump-input"
                        style="width: 70px; text-align: center;"
                        min="1"
                        max="{{ $paginator->lastPage() }}"
                        placeholder="Hal"
                        value="{{ $paginator->currentPage() }}">
                    <button class="btn btn-sm btn-outline-primary ms-1 page-jump-btn"
                        data-last-page="{{ $paginator->lastPage() }}">
                        <i class="fas fa-angle-double-right"></i>
                    </button>
                </div>
            </li>
        </ul>
    </nav>

    <script>
        // Event Delegation untuk memastikan tombol berfungsi
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk lompat halaman
            function goToPage(inputElement, lastPage) {
                let page = parseInt(inputElement.value);

                if (isNaN(page) || page < 1) {
                    page = 1;
                }
                if (page > lastPage) {
                    page = lastPage;
                }

                // Update URL dengan parameter page
                let url = new URL(window.location.href);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            }

            // Event listener untuk semua tombol lompat halaman
            document.querySelectorAll('.page-jump-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    let input = this.parentElement.querySelector('.page-jump-input');
                    let lastPage = parseInt(this.getAttribute('data-last-page'));
                    goToPage(input, lastPage);
                });
            });

            // Event listener untuk Enter key pada input
            document.querySelectorAll('.page-jump-input').forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        let btn = this.parentElement.querySelector('.page-jump-btn');
                        let lastPage = parseInt(btn.getAttribute('data-last-page'));
                        goToPage(this, lastPage);
                    }
                });
            });
        });
    </script>
@endif
