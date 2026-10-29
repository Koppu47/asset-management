@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <li><span class="pagination-link is-disabled" aria-disabled="true">ก่อนหน้า</span></li>
            @else
                <li><a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">ก่อนหน้า</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-link is-ellipsis" aria-hidden="true">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page === $paginator->currentPage())
                            <li><span class="pagination-link is-current" aria-current="page">{{ $page }}</span></li>
                        @else
                            <li><a class="pagination-link" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">ถัดไป</a></li>
            @else
                <li><span class="pagination-link is-disabled" aria-disabled="true">ถัดไป</span></li>
            @endif
        </ul>
    </nav>
@endif
