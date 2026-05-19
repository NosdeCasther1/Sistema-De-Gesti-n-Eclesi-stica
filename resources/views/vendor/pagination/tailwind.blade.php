@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de paginación" class="flex flex-col sm:flex-row gap-4 items-center justify-between w-full">
        
        {{-- Resumen de resultados (Izquierda en desktop, arriba en móvil) --}}
        <div class="text-sm text-slate-500 dark:text-slate-400 font-medium">
            Mostrando 
            @if ($paginator->firstItem())
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            de
            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $paginator->total() }}</span>
            resultados
        </div>

        {{-- Botones de página (Derecha en desktop, abajo en móvil) --}}
        <div>
            <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">

                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Anterior">
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-300 dark:text-slate-700 bg-slate-50 dark:bg-slate-950 cursor-not-allowed border-r border-slate-200 dark:border-slate-800" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" aria-label="Anterior">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 dark:text-slate-600 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 cursor-default">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-blue-600 border-r border-slate-200 dark:border-slate-800 cursor-default">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" aria-label="Ir a página {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white bg-white dark:bg-slate-900 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" aria-label="Siguiente">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Siguiente">
                        <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-slate-300 dark:text-slate-700 bg-slate-50 dark:bg-slate-950 cursor-not-allowed" aria-hidden="true">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
