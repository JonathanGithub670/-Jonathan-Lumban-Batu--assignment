<x-app-layout>
    {{-- Main Container --}}
    <div style="background-color: #f3f4f6; min-height: 100vh; padding: 24px 0;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Breadcrumb & Top Actions --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <x-breadcrumbs :items="[
                    ['label' => 'Suppliers', 'url' => route('suppliers.index')],
                    ['label' => $supplier->name, 'url' => route('suppliers.show', $supplier)],
                    ['label' => 'Layup: ' . $layup->name]
                ]" />
                <div style="display: flex; gap: 12px;">
                    <form action="{{ route('suppliers.layups.duplicate', [$supplier, $layup]) }}" method="POST">
                        @csrf
                        <button type="submit" style="display: inline-flex; align-items: center; padding: 8px 16px; background: white; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; font-weight: 600; color: #374151; cursor: pointer;">
                            <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h2"/>
                            </svg>
                            Duplicate
                        </button>
                    </form>
                    <button type="submit" form="layup-save-form" style="display: inline-flex; align-items: center; padding: 8px 16px; background: #065f46; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; color: white; cursor: pointer;">
                         <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>

            {{-- Main Spec Card --}}
            <form id="layup-save-form" action="{{ route('suppliers.layups.update', [$supplier, $layup]) }}" method="POST">
                @csrf @method('PUT')
                <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; overflow: hidden;">
                    <div style="padding: 24px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 2;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                                <input type="text" name="name" value="{{ old('name', $layup->name) }}" style="font-size: 24px; font-weight: 700; color: #111827; border: 1px solid transparent; background: transparent; padding: 4px; border-radius: 4px; width: 100%;" onfocus="this.style.borderColor='#e2e8f0'; this.style.background='#f9fafb'" onblur="this.style.borderColor='transparent'; this.style.background='transparent'">
                                <span style="background: #ecfdf5; color: #065f46; font-size: 12px; font-weight: 600; padding: 2px 10px; border-radius: 20px; border: 1px solid #10b981; white-space: nowrap;">Active</span>
                            </div>
                            <textarea name="description" rows="1" style="color: #6b7280; font-size: 14px; border: 1px solid transparent; background: transparent; padding: 4px; border-radius: 4px; width: 100%; resize: none;" onfocus="this.style.borderColor='#e2e8f0'; this.style.background='#f9fafb'" onblur="this.style.borderColor='transparent'; this.style.background='transparent'">{{ old('description', $layup->description ?? 'Standard ' . $layup->layers->count() . '-layer panel for residential structural walls.') }}</textarea>
                        </div>
                    <div style="flex: 1; border-left: 1px solid #f3f4f6; padding-left: 24px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 4px;">Created By</span>
                        <span style="font-size: 15px; font-weight: 700; color: #374151;">Eng. Dept A</span>
                    </div>
                    <div style="flex: 1; border-left: 1px solid #f3f4f6; padding-left: 24px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 4px;">Last Modified</span>
                        <span style="font-size: 15px; font-weight: 700; color: #374151;">{{ now()->format('M d, Y') }}</span>
                    </div>
                    <div style="flex: 1; border-left: 1px solid #f3f4f6; padding-left: 24px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 4px;">Total Thickness</span>
                        <span style="font-size: 18px; font-weight: 800; color: #065f46;">{{ $layup->layers->sum('thickness') }}mm</span>
                    </div>
                    <div style="flex: 1; border-left: 1px solid #f3f4f6; padding-left: 24px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #9ca3af; letter-spacing: 0.05em; margin-bottom: 4px;">Total Layers</span>
                        <span style="font-size: 18px; font-weight: 800; color: #065f46;">{{ $layup->layers->count() }} Layers</span>
                    </div>
                </div>
            </div>

            {{-- 2-Column Desktop Grid --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                
                {{-- Left Column: Composition Table --}}
                <div>
                   <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Layer Composition</h2>
                        <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" style="color: #065f46; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;">
                            <span style="font-size: 18px; margin-right: 4px;">+</span> Add Layer
                        </a>
                   </div>

                   <div style="background: white; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Order</th>
                                    <th style="padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Thickness</th>
                                    <th style="padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Width</th>
                                    <th style="padding: 12px 16px; text-align: center; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Angle</th>
                                    <th style="padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Grade</th>
                                    <th style="padding: 12px 16px; text-align: right; font-size: 11px; text-transform: uppercase; color: #6b7280; font-weight: 700;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($layup->layers->sortBy('layer_order') as $layer)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 16px; display: flex; align-items: center; gap: 12px;">
                                        <div style="color: #d1d5db; display: flex; flex-direction: column; gap: 2px;">
                                            <div style="width: 3px; height: 3px; border-radius: 50%; background: currentColor;"></div>
                                            <div style="width: 3px; height: 3px; border-radius: 50%; background: currentColor;"></div>
                                            <div style="width: 3px; height: 3px; border-radius: 50%; background: currentColor;"></div>
                                        </div>
                                    </td>
                                    <td style="padding: 14px 16px; font-weight: 600; color: #111827;">{{ $layer->thickness }}mm</td>
                                    <td style="padding: 14px 16px; color: #4b5563;">{{ $layer->width }}mm</td>
                                    <td style="padding: 14px 16px; text-align: center;">
                                        <div style="display: inline-flex; align-items: center; background: #f3f4f6; padding: 4px 8px; border-radius: 40px; font-size: 12px; font-weight: 600; color: #374151; border: 1px solid #e5e7eb;">
                                            @if($layer->angle == 0)
                                                <svg style="width: 14px; height: 14px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                                0&deg;
                                            @else
                                                <svg style="width: 14px; height: 14px; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                90&deg;
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 14px 16px;">
                                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #374151;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $layer->angle == 0 ? '#10b981' : '#b45309' }};"></div>
                                            C24
                                        </div>
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right;">
                                        <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" style="color: #6b7280; margin-right: 12px; display: inline-block;">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <form action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}" method="POST" style="display: inline-block;">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background: none; border: none; padding: 0; color: #ef4444; cursor: pointer;">
                                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div style="padding: 12px 16px; background: #f9fafb; display: flex; justify-content: space-between; font-size: 12px; color: #6b7280; font-weight: 500;">
                            <span>Showing {{ $layup->layers->count() }} layers</span>
                            <span>Calculated Sum: {{ $layup->layers->sum('thickness') }}.00 mm</span>
                        </div>
                   </div>

                   {{-- Engineering Note --}}
                   <div style="margin-top: 24px; background: #fffcf0; border: 1px solid #fde68a; border-radius: 8px; padding: 16px; display: flex; gap: 16px;">
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: #fde68a; display: flex; align-items: center; justify-content: center; color: #b45309; flex-shrink: 0;">
                            <span style="font-weight: 800; font-size: 14px;">i</span>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 14px; font-weight: 700; color: #92400e; margin: 0 0 4px 0;">Engineering Note</h4>
                            <textarea name="engineering_note" rows="2" style="font-size: 13px; color: #b45309; border: 1px solid transparent; background: transparent; padding: 4px; border-radius: 4px; width: 100%; resize: vertical; line-height: 1.5;" onfocus="this.style.borderColor='#fde68a'; this.style.background='#fffde7'" onblur="this.style.borderColor='transparent'; this.style.background='transparent'">{{ old('engineering_note', $layup->engineering_note ?? 'Ensure bonding pressure is adjusted for varying layer grades (C24/C16 mix). Verify alignment of 90° transverse layers.') }}</textarea>
                        </div>
                   </div>
                </div>
            </form>

                {{-- Right Column: Structure Visualizer --}}
                <div>
                   <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h2 style="font-size: 18px; font-weight: 700; color: #111827; margin: 0;">Structure Visualizer</h2>
                        <div style="display: flex; gap: 16px;">
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #6b7280;">
                                <div style="width: 12px; height: 12px; background: #ead3b3; border-radius: 2px;"></div> Longitudinal (0&deg;)
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #6b7280;">
                                <div style="width: 12px; height: 12px; background: #cd9b6e; border-radius: 2px;"></div> Transverse (90&deg;)
                            </div>
                        </div>
                   </div>

                   <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 48px; position: relative; min-height: 600px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        
                        {{-- Labels --}}
                        <div style="position: absolute; left: 40px; top: 80px; text-align: left;">
                            <span style="display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Top</span>
                            <span style="display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">(Outside)</span>
                            <div style="width: 1px; height: 400px; background: #e5e7eb; position: absolute; left: 16px; top: 30px; border-left: 1px dashed #ced4da;"></div>
                        </div>
                        <div style="position: absolute; left: 40px; bottom: 80px; text-align: left;">
                            <span style="display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">Bottom</span>
                            <span style="display: block; font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">(Inside)</span>
                        </div>

                        {{-- The Assembly --}}
                        <div style="background: white; padding: 40px; border: 1px solid #f3f4f6; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); width: 320px; display: flex; flex-direction: column-reverse; gap: 4px;">
                            @foreach($layup->layers->sortBy('layer_order') as $layer)
                            <div style="
                                height: {{ max(40, $layer->thickness * 1.5) }}px; 
                                background: {{ $layer->angle == 0 ? '#ead3b3' : '#cd9b6e' }}; 
                                border-radius: 4px; 
                                display: flex; 
                                align-items: center; 
                                justify-content: center; 
                                position: relative; 
                                box-shadow: inset 0 1px 1px rgba(255,255,255,0.4), 0 1px 2px rgba(0,0,0,0.1);
                                transition: all 0.3s;
                                cursor: pointer;
                            ">
                                <span style="font-size: 12px; font-weight: 800; color: rgba(0,0,0,0.6);">L{{ $layer->layer_order }} ({{ $layer->thickness }}mm)</span>
                                
                                {{-- Grain Indicator --}}
                                <div style="position: absolute; right: 20px; color: rgba(0,0,0,0.3);">
                                    @if($layer->angle == 0)
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    @else
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div style="margin-top: 40px; text-align: center;">
                            <p style="font-size: 13px; font-weight: 700; color: #64748b; margin: 0 0 4px 0;">Cross-Laminated Structural Assembly</p>
                            <p style="font-size: 10px; color: #94a3b8; font-style: italic; max-width: 300px;">Note: 3D orientation is for schematic purposes. All layers bonded with industrial-grade adhesives.</p>
                        </div>
                   </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
