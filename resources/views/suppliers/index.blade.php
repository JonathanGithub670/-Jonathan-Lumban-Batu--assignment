<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumbs :items="[['label' => 'Suppliers']]" />
            
            {{-- Header Section --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; text-align: left;">
                <div style="text-align: left;">
                    <h1 style="font-size: 30px; font-weight: 700; color: #111827; margin: 0;">Suppliers</h1>
                    <p style="font-size: 14px; color: #6b7280; margin-top: 4px;">Manage timber suppliers and material sourcing.</p>
                </div>
                <a href="{{ route('suppliers.create') }}" style="display: inline-flex; align-items: center; padding: 10px 16px; background-color: #15803d; color: white; font-size: 14px; font-weight: 600; border-radius: 6px; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background-color 0.2s;">
                    <svg style="width: 20px; height: 20px; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Supplier
                </a>
            </div>

            {{-- Toolbar Section --}}
            <div class="flex items-center gap-3 mb-6" x-data="{ search: '{{ $search ?? '' }}' }">
                <div style="width: 400px; max-width: 100%;">
                    <!-- Search Form with Alpine.js -->
                    <form x-ref="searchForm" action="{{ route('suppliers.index') }}" method="GET">
                        <div style="position: relative; display: flex; align-items: center;">
                            <input 
                                type="text" 
                                name="search" 
                                x-model="search"
                                @input.debounce.500ms="$refs.searchForm.submit()"
                                placeholder="Search suppliers..." 
                                class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all shadow-sm"
                            >
                            
                            <!-- Clear Button (x) - Forced Inside with Absolute Position -->
                            <button 
                                type="button" 
                                x-show="search.length > 0" 
                                @click="search = ''; $nextTick(() => $refs.searchForm.submit())"
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); padding: 0; border: none; background: transparent; color: #9ca3af; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                class="hover:text-gray-600 transition-colors"
                            >
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors h-[38px]">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors h-[38px]">
                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
            </div>

            {{-- Table Section --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Layups</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created At</th>
                                <th scope="col" colspan="3" class="px-3 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-l border-gray-100">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($suppliers as $index => $supplier)
                                @php
                                    $bgColors = ['bg-blue-50', 'bg-green-50', 'bg-orange-50', 'bg-purple-50', 'bg-teal-50'];
                                    $textColors = ['text-blue-600', 'text-green-600', 'text-orange-600', 'text-purple-600', 'text-teal-600'];
                                    $colorIdx = $index % count($bgColors);
                                    
                                    // Generate Initials
                                    $words = explode(' ', $supplier->name);
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($supplier->name, 0, 2));
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div style="display: flex; align-items: center;">
                                            @php
                                                $styles = [
                                                    ['bg' => '#eff6ff', 'text' => '#1e40af'], // Blue
                                                    ['bg' => '#f0fdf4', 'text' => '#166534'], // Green
                                                    ['bg' => '#fff7ed', 'text' => '#9a3412'], // Orange
                                                    ['bg' => '#faf5ff', 'text' => '#6b21a8'], // Purple
                                                    ['bg' => '#f0fdfa', 'text' => '#115e59'], // Teal
                                                ];
                                                // Use supplier ID for consistent color mapping
                                                $style = $styles[$supplier->id % count($styles)];
                                                
                                                $words = explode(' ', $supplier->name);
                                                $initials = '';
                                                if (count($words) >= 2) {
                                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                                } else {
                                                    $initials = strtoupper(substr($supplier->name, 0, 2));
                                                }
                                            @endphp
                                            <!-- Avatar Initials - Fixed with Inline Styles -->
                                            <div style="width: 40px; height: 40px; border-radius: 9999px; background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.05);">
                                                {{ $initials }}
                                            </div>
                                            <!-- Name and ID -->
                                            <div style="margin-left: 16px; text-align: left;">
                                                <div style="font-size: 14px; font-weight: 700; color: #111827; line-height: 1.25;">{{ $supplier->name }}</div>
                                                <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">ID: SUP-{{ $supplier->created_at->format('Y') }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $supplier->layups_count ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $supplier->created_at->format('M d, Y') }}
                                    </td>
                                    <!-- View Action -->
                                    <td class="px-2 py-4 whitespace-nowrap text-center border-l border-gray-50" style="width: 40px;">
                                        <a href="{{ route('suppliers.show', $supplier) }}" style="color: #3b82f6; display: inline-block;" title="View">
                                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </td>
                                    <!-- Edit Action -->
                                    <td class="px-2 py-4 whitespace-nowrap text-center" style="width: 40px;">
                                        <a href="{{ route('suppliers.edit', $supplier) }}" style="color: #f59e0b; display: inline-block;" title="Edit">
                                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                    </td>
                                    <!-- Delete Action -->
                                    <td class="px-2 py-4 whitespace-nowrap text-center" style="width: 40px;">
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #ef4444; background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center;" title="Delete">
                                                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Area -->
                <div class="px-6 py-4 border-t border-gray-100 bg-white flex items-center justify-between">
                    <div class="text-xs text-gray-500">
                        Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} results
                    </div>
                    <div class="flex gap-2">
                        @if ($suppliers->onFirstPage())
                            <span class="p-1.5 rounded border border-gray-200 text-gray-300 bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $suppliers->previousPageUrl() }}" class="p-1.5 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        @if ($suppliers->hasMorePages())
                            <a href="{{ $suppliers->nextPageUrl() }}" class="p-1.5 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="p-1.5 rounded border border-gray-200 text-gray-300 bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
