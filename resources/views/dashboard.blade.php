<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Premium Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Suppliers Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="text-white/60 text-xs font-bold uppercase tracking-wider">Suppliers</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-white mb-1">{{ $stats['suppliers_count'] }}</h3>
                        <p class="text-emerald-100 text-sm font-medium">Active Partners</p>
                    </div>
                    <a href="{{ route('suppliers.index') }}" class="block px-6 py-3 bg-black/10 text-white/80 hover:text-white text-xs font-bold transition-colors">
                        View List &rarr;
                    </a>
                </div>

                <!-- Layups Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <span class="text-white/60 text-xs font-bold uppercase tracking-wider">Layups</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-white mb-1">{{ $stats['layups_count'] }}</h3>
                        <p class="text-indigo-100 text-sm font-medium">Catalog Items</p>
                    </div>
                    <a href="{{ route('layups.index') }}" class="block px-6 py-3 bg-black/10 text-white/80 hover:text-white text-xs font-bold transition-colors">
                        View Catalog &rarr;
                    </a>
                </div>

                <!-- Layers Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                            </div>
                            <span class="text-white/60 text-xs font-bold uppercase tracking-wider">Total Layers</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-white mb-1">{{ $stats['layers_count'] }}</h3>
                        <p class="text-amber-50 text-sm font-medium">Structure Elements</p>
                    </div>
                    <div class="px-6 py-3 bg-black/10 text-white/60 text-xs font-bold">
                        Combined Depth
                    </div>
                </div>

                <!-- Complexity Card -->
                <div class="relative overflow-hidden bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl shadow-lg transition-transform hover:scale-[1.02]">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <span class="text-white/60 text-xs font-bold uppercase tracking-wider">Complexity</span>
                        </div>
                        <h3 class="text-3xl font-extrabold text-white mb-1">{{ $stats['avg_layers'] }}</h3>
                        <p class="text-rose-100 text-sm font-medium">Avg. Layers / Layup</p>
                    </div>
                    <div class="px-6 py-3 bg-black/10 text-white/60 text-xs font-bold">
                        Assembly Intensity
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="px-8 py-6 border-bottom border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Recent Partners</h3>
                        <p class="text-sm text-gray-500 font-medium">Latest suppliers added to the ecosystem</p>
                    </div>
                    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
                        View all
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/30 dark:bg-gray-700/30">
                                <th class="px-8 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Supplier Details</th>
                                <th class="px-8 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Global ID</th>
                                <th class="px-8 py-4 text-center text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Layups</th>
                                <th class="px-8 py-4 text-right text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Onboarding</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($stats['recent_suppliers'] as $supplier)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-sm mr-4 shadow-inner">
                                            {{ substr($supplier->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-sm font-black text-gray-900 dark:text-white hover:text-emerald-600 transition-colors">{{ $supplier->name }}</a>
                                            <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">{{ $supplier->identifier ?: 'Verified Partner' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="inline-flex px-2.5 py-1 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-black font-mono tracking-tight">
                                        SUP-{{ $supplier->created_at->format('Y') }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-800">
                                        {{ $supplier->layups_count }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $supplier->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-400 font-medium">{{ $supplier->created_at->diffForHumans() }}</p>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
