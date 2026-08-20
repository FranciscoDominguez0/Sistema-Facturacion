<div class="w-full">
    @section('breadcrumbs')
        <x-breadcrumbs :links="[
            ['title' => 'Gastos']
        ]" />
    @endsection

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-bold text-slate-800 whitespace-nowrap">Gastos</h2>

        <div class="flex flex-col sm:flex-row items-center gap-4 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input wire:model.live.debounce.300ms="search" class="w-full bg-white border border-slate-200 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-800 focus:outline-none focus:border-sovereign-blue focus:ring-1 focus:ring-sovereign-blue transition-colors" placeholder="Buscar por concepto o categoría..." type="text"/>
            </div>

            <!-- Main Action -->
            <a href="{{ route('gastos.crear') }}" wire:navigate class="w-full sm:w-auto bg-sovereign-blue text-white px-6 py-2 rounded-lg text-sm font-medium border border-sovereign-blue hover:bg-opacity-90 transition-colors shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Nuevo Gasto
            </a>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Concepto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Categoría</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Monto</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Fecha</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Registrado por</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Comprobante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($gastos as $gasto)
                    <tr class="hover:bg-slate-50 transition-colors" wire:key="{{ $gasto->id }}">
                        <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $gasto->concepto }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $gasto->categoria }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-800">{{ $gasto->monto_formateado }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $gasto->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $gasto->registradoPor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $gasto->comprobante ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined text-4xl mb-3 text-slate-300">receipt</span>
                                <p class="text-base font-medium text-slate-800 mb-1">No hay gastos registrados</p>
                                <p class="text-sm mb-4">Comienza registrando un nuevo gasto.</p>
                                <a href="{{ route('gastos.crear') }}" wire:navigate class="text-sm text-sovereign-blue font-medium hover:underline">
                                    + Nuevo Gasto
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($gastos->hasPages())
        <div class="bg-slate-50 px-6 py-3 border-t border-slate-200">
            {{ $gastos->links() }}
        </div>
        @endif
    </div>
</div>
