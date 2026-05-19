@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de paginación" class="flex flex-col sm:flex-row gap-4 items-center justify-between w-full">
        
        {{-- Resumen de resultados (Izquierda en desktop, arriba en móvil) --}}
        <div class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">
            Mostrando 
            @if ($paginator->firstItem())
                <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            de
            <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $paginator->total() }}</span>
            resultados
        </div>

        {{-- Botones de página (Derecha en desktop, abajo en móvil) --}}
        <div>
            <span class="flex items-center gap-2">

                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Anterior">
                        <span class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-medium text-slate-300 dark:text-slate-700 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800/80 cursor-not-allowed shadow-sm" aria-hidden="true">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm hover:-translate-y-0.5 duration-200" aria-label="Anterior">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-medium text-slate-400 dark:text-slate-600 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 cursor-default shadow-sm">{{ $element }}</span>
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-extrabold text-white bg-blue-600 dark:bg-blue-600 border border-blue-600 dark:border-blue-600 shadow-md shadow-blue-500/25 cursor-default">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-bold text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm hover:-translate-y-0.5 duration-200" aria-label="Ir a página {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-medium text-slate-600 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm hover:-translate-y-0.5 duration-200" aria-label="Siguiente">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Siguiente">
                        <span class="w-10 h-10 rounded-xl inline-flex items-center justify-center text-sm font-medium text-slate-300 dark:text-slate-700 bg-slate-50 dark:bg-slate-950/40 border border-slate-200 dark:border-slate-800/80 cursor-not-allowed shadow-sm" aria-hidden="true">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
