<x-app-layout>
    {{-- Main Wrapper with light slate background --}}
    <div style="background-color: #f8fafc; min-height: 100vh; padding: 40px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb: Professional & Clean --}}
            <x-breadcrumbs :items="[
                ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                ['label' => $supplier->name]
            ]" />

            {{-- Main Header Card: High Shadow & Rounded --}}
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05); overflow: hidden; margin-bottom: 32px;">
                <div style="padding: 32px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">{{ $supplier->name }}</h1>
                            <span style="background: #064e3b; color: white; font-size: 10px; font-weight: 800; text-transform: uppercase; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.05em;">
                                Active Partner
                            </span>
                        </div>
                        <p style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em; margin: 0;">ID: SUP-{{ $supplier->created_at->format('Y') }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <a href="{{ route('suppliers.edit', $supplier) }}" style="display: inline-flex; align-items: center; padding: 10px 18px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; color: #334155; font-size: 14px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;">
                        <svg style="width: 16px; height: 16px; margin-right: 8px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit Supplier
                    </a>
                </div>

                {{-- INFO GRID: Forced Horizontal with Flexbox --}}
                <div style="display: flex; border-top: 1px solid #f1f5f9; background: #ffffff;">
                    {{-- Primary Contact --}}
                    <div style="flex: 1; padding: 24px 32px; border-right: 1px solid #f1f5f9;">
                        <label style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 12px;">Primary Contact</label>
                        <div style="display: flex; align-items: center; font-size: 14px; font-weight: 700; color: #334155;">
                            <svg style="width: 18px; height: 18px; margin-right: 10px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            engineering@nordic.ca
                        </div>
                    </div>
                    {{-- Location --}}
                    <div style="flex: 1; padding: 24px 32px; border-right: 1px solid #f1f5f9;">
                        <label style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 12px;">Location</label>
                        <div style="display: flex; align-items: center; font-size: 14px; font-weight: 700; color: #334155;">
                            <svg style="width: 18px; height: 18px; margin-right: 10px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Montreal, QC, Canada
                        </div>
                    </div>
                    {{-- Certifications --}}
                    <div style="flex: 1; padding: 24px 32px; border-right: 1px solid #f1f5f9;">
                        <label style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 12px;">Certifications</label>
                        <div style="display: flex; align-items: center; font-size: 14px; font-weight: 700; color: #334155;">
                            <svg style="width: 18px; height: 18px; margin-right: 10px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z"/>
                            </svg>
                            SPF No. 1/2, D. Fir-L
                        </div>
                    </div>
                    {{-- Audit --}}
                    <div style="flex: 1; padding: 24px 32px;">
                        <label style="display: block; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 12px;">Last Audit Date</label>
                        <div style="display: flex; align-items: center; font-size: 14px; font-weight: 700; color: #334155;">
                            <svg style="width: 18px; height: 18px; margin-right: 10px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Oct 16, 2025
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Header & Actions --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Associated Layups</h2>
                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('suppliers.import', $supplier) }}" style="display: inline-flex; align-items: center; padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; color: #475569; font-size: 14px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <svg style="width: 16px; height: 16px; margin-right: 8px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Import
                    </a>
                    <a href="{{ route('suppliers.export', $supplier) }}" style="display: inline-flex; align-items: center; padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; color: #475569; font-size: 14px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <svg style="width: 16px; height: 16px; margin-right: 8px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export
                    </a>
                    <a href="{{ route('suppliers.layups.create', $supplier) }}" style="display: inline-flex; align-items: center; padding: 10px 20px; background: #064e3b; color: white; border-radius: 8px; font-size: 14px; font-weight: 700; text-decoration: none; box-shadow: 0 4px 6px -1px rgba(6, 78, 59, 0.2);">
                        <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Layup
                    </a>
                </div>
            </div>

            {{-- Main Table Section --}}
            <div style="background: white; border-radius: 12px; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="overflow-x-auto">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                            <tr>
                                <th style="padding: 16px 24px; text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Layup ID</th>
                                <th style="padding: 16px 24px; text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Name</th>
                                <th style="padding: 16px 24px; text-align: center; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Thickness</th>
                                <th style="padding: 16px 24px; text-align: center; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Ply Count</th>
                                <th style="padding: 16px 24px; text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Species/Grade</th>
                                <th style="padding: 16px 24px; text-align: left; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Revision</th>
                                <th style="padding: 16px 24px; text-align: center; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Status</th>
                                <th style="padding: 16px 24px; text-align: right; font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody style="background: white;">
                            @forelse($supplier->layups as $layup)
                                @php
                                    $thickness = $layup->layers->sum('thickness');
                                    $plyCount = $layup->layers->count();
                                @endphp
                                <tr style="border-bottom: 1px solid #f8fafc;" class="group hover:bg-gray-50/50 transition-colors">
                                    <td style="padding: 20px 24px; font-size: 13px; font-weight: 700; color: #94a3b8;">L-{{ $supplier->id }}-{{ chr(65 + $loop->index) }}</td>
                                    <td style="padding: 20px 24px; font-size: 14px; font-weight: 700; color: #0f172a;">
                                        <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="hover:text-emerald-700 transition-colors">
                                            {{ $layup->name }}
                                        </a>
                                    </td>
                                    <td style="padding: 20px 24px; text-align: center; font-size: 14px; font-weight: 700; color: #334155;">{{ number_format($thickness, 0) }}mm</td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <span style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">{{ $plyCount }}</span>
                                    </td>
                                    <td style="padding: 20px 24px; font-size: 14px; font-weight: 700; color: #475569;">Spruce / No. 2</td>
                                    <td style="padding: 20px 24px; font-size: 12px; font-weight: 700; color: #94a3b8; font-style: italic;">Rev {{ $loop->iteration }} ({{ now()->subWeeks($loop->iteration)->format('M d') }})</td>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <div style="display: inline-flex; align-items: center; border: 1px solid #d1fae5; background: #f0fdf4; color: #065f46; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px;">
                                            <div style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; margin-right: 8px;"></div>
                                            Active
                                        </div>
                                    </td>
                                    <td style="padding: 20px 24px; text-align: right;">
                                        <div style="display: inline-flex; gap: 12px; align-items: center;">
                                            {{-- View Action --}}
                                            <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" style="color: #3b82f6;" title="View Layers">
                                                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            {{-- Edit Action --}}
                                            <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" style="color: #f59e0b;" title="Edit Layup">
                                                <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </a>
                                            {{-- Delete Action --}}
                                            <form action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" method="POST" onsubmit="return confirm('Delete this layup?')" style="display: inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" style="color: #ef4444; border: none; background: transparent; cursor: pointer; padding: 0;">
                                                    <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 48px; text-align: center; font-size: 14px; color: #94a3b8;">No layups found for this supplier.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Custom Legend/Footer --}}
                <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <div style="font-size: 10px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.15em;">
                        Showing {{ $supplier->layups->count() }} of {{ $supplier->layups->count() }} layups
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
