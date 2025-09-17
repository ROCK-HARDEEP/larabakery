@if ($paginator->hasPages())
    <nav class="pagination-wrapper" role="navigation" aria-label="Pagination Navigation">
        <div class="pagination-info">
            <span class="pagination-text">
                Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
            </span>
        </div>
        
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link">
                        <i class="fas fa-chevron-left"></i>
                        <span class="sr-only">Previous</span>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left"></i>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            @endif

            {{-- First Page --}}
            @if($paginator->currentPage() > 3)
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($paginator->currentPage() > 4)
                    <li class="pagination-item">
                        <span class="pagination-ellipsis">...</span>
                    </li>
                @endif
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item">
                        <span class="pagination-ellipsis">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active">
                                <span class="pagination-link current">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Last Page --}}
            @if($paginator->currentPage() < $paginator->lastPage() - 2)
                @if($paginator->currentPage() < $paginator->lastPage() - 3)
                    <li class="pagination-item">
                        <span class="pagination-ellipsis">...</span>
                    </li>
                @endif
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fas fa-chevron-right"></i>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            @else
                <li class="pagination-item disabled">
                    <span class="pagination-link">
                        <i class="fas fa-chevron-right"></i>
                        <span class="sr-only">Next</span>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Page Size Selector --}}
        <div class="pagination-page-size">
            <label for="pageSize">Show:</label>
            <select id="pageSize" onchange="changePageSize(this.value)">
                <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
                <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
            </select>
        </div>
    </nav>

    <style>
        .pagination-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .pagination-info {
            color: var(--gray-600);
            font-size: 0.9rem;
        }
        
        .pagination-list {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .pagination-item {
            display: flex;
        }
        
        .pagination-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.75rem;
            background: var(--white);
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-fast);
            position: relative;
        }
        
        .pagination-link:hover {
            background: var(--bakery-cream);
            border-color: var(--bakery-primary);
            color: var(--bakery-primary);
            transform: translateY(-1px);
        }
        
        .pagination-item.active .pagination-link,
        .pagination-link.current {
            background: linear-gradient(135deg, var(--bakery-primary) 0%, var(--bakery-primary-light) 100%);
            border-color: var(--bakery-primary-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        
        .pagination-item.disabled .pagination-link {
            background: var(--gray-100);
            border-color: var(--gray-200);
            color: var(--gray-400);
            cursor: not-allowed;
        }
        
        .pagination-item.disabled .pagination-link:hover {
            transform: none;
            background: var(--gray-100);
            border-color: var(--gray-200);
            color: var(--gray-400);
        }
        
        .pagination-ellipsis {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            color: var(--gray-400);
            font-weight: 500;
        }
        
        .pagination-page-size {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--gray-600);
        }
        
        .pagination-page-size select {
            padding: 0.5rem;
            border: 2px solid var(--gray-200);
            border-radius: 6px;
            background: var(--white);
            color: var(--gray-700);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-fast);
        }
        
        .pagination-page-size select:focus {
            outline: none;
            border-color: var(--bakery-primary);
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        @media (max-width: 768px) {
            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
            }
            
            .pagination-list {
                order: 2;
            }
            
            .pagination-info {
                order: 1;
            }
            
            .pagination-page-size {
                order: 3;
            }
            
            .pagination-link {
                min-width: 36px;
                height: 36px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .pagination-list {
                gap: 0.125rem;
            }
            
            .pagination-link {
                min-width: 32px;
                height: 32px;
                padding: 0 0.5rem;
                font-size: 0.85rem;
            }
        }
    </style>

    <script>
        function changePageSize(perPage) {
            const url = new URL(window.location);
            url.searchParams.set('per_page', perPage);
            url.searchParams.delete('page'); // Reset to page 1
            window.location.href = url.toString();
        }
    </script>
@endif