<x-app-layout>
    <div x-data="{
        conflicts: {{ json_encode($conflicts) }},
        activeIndex: 0,
        resolved: [],
        isProcessing: false,
        init() {
            this.resolved = new Array(this.conflicts.length).fill(null);
        },
        resolve(status) {
            if (this.resolved[this.activeIndex] !== null) return;
            this.isProcessing = true;

            this.resolved[this.activeIndex] = status;
            const label = status === 'incoming' ? 'Accept New' : 'Keep Existing';
            const remaining = this.resolved.filter(r => r === null).length;

            setTimeout(() => {
                this.isProcessing = false;

                if (remaining === 0) {
                    Swal.fire({
                        icon: 'success',
                        title: 'All Conflicts Resolved!',
                        text: 'Finalizing import now...',
                        timer: 1500,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        document.getElementById('finalizeForm').submit();
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: label,
                        text: remaining + ' conflict(s) remaining.',
                        timer: 1200,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        // Move to next unresolved conflict
                        for (let i = 0; i < this.conflicts.length; i++) {
                            if (this.resolved[i] === null) {
                                this.activeIndex = i;
                                break;
                            }
                        }
                    });
                }
            }, 400);
        }
    }" style="background: rgba(0,0,0,0.4); min-height: 100vh; padding: 40px; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;">

        {{-- Hidden form for auto-submit --}}
        <form id="finalizeForm" action="{{ route('suppliers.import.execute', $supplier) }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="strategy" value="manual">
            @foreach($conflicts as $index => $conflict)
                @foreach($conflict['layer_conflicts'] as $lc)
                    <input type="hidden" name="resolutions[{{ $conflict['layup_id'] }}_{{ $lc['layer_order'] }}]" :value="resolved[{{ $index }}]" x-bind:value="resolved[{{ $index }}]">
                @endforeach
            @endforeach
        </form>

        <div style="background: white; width: 100%; max-width: 1200px; height: 85vh; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); display: flex; flex-direction: column; overflow: hidden;">

            {{-- Header --}}
            <div style="padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: white;">
                <div>
                    <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 12px;">
                        Conflict Resolution: Import [supplier_data.json]
                        <span style="background: #fff7ed; color: #9a3412; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 12px; border: 1px solid #ffedd5; text-transform: uppercase;">Needs Review</span>
                    </h2>
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Please review discrepancies between incoming data and existing records.</p>
                </div>
                <a href="{{ route('suppliers.show', $supplier) }}" style="color: #94a3b8; text-decoration: none;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>

            <div style="flex: 1; display: flex; overflow: hidden;">

                {{-- Sidebar --}}
                <div style="width: 280px; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; background: #fafafa;">
                    <div style="padding: 24px; flex: 1; overflow-y: auto;">
                        <h3 style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between;">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 14px; height: 14px; color: #e11d48;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                Conflicting Layups (<span x-text="resolved.filter(r => r === null).length"></span>)
                            </span>
                        </h3>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <template x-for="(conflict, index) in conflicts" :key="'unresolved-'+index">
                                <div x-show="resolved[index] === null">
                                    <button @click="activeIndex = index"
                                        :style="activeIndex === index ? 'width:100%; text-align: left; padding: 12px 16px; background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 12px; position: relative; cursor: pointer;' : 'width:100%; text-align: left; padding: 12px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer;'">
                                        <div :style="activeIndex === index ? 'font-size: 14px; font-weight: 800; color: #064e3b;' : 'font-size: 14px; font-weight: 700; color: #1e293b;'" x-text="conflict.layup_name"></div>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;" x-text="conflict.layer_conflicts.length + ' discrepancies found'"></div>
                                        <template x-if="activeIndex === index">
                                            <div style="position: absolute; right: 12px; top: 12px; width: 6px; height: 6px; background: #e11d48; border-radius: 50%;"></div>
                                        </template>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <h3 style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; margin: 32px 0 16px 0;">Resolved</h3>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <template x-for="(conflict, index) in conflicts" :key="'resolved-'+index">
                                <div x-show="resolved[index] !== null">
                                    <div style="padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <div style="font-size: 13px; font-weight: 700; color: #64748b;" x-text="conflict.layup_name"></div>
                                            <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; margin-top: 2px;"
                                                :style="resolved[index] === 'incoming' ? 'color: #059669;' : 'color: #3b82f6;'"
                                                x-text="resolved[index] === 'incoming' ? 'Accepted New' : 'Kept Existing'"></div>
                                        </div>
                                        <svg style="width: 16px; height: 16px; color: #10b981;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div style="padding: 24px; border-top: 1px solid #f1f5f9;">
                        <p style="font-size: 11px; color: #94a3b8; margin-bottom: 12px; text-align: center;">Aborting will clear all analysis and keep database unchanged.</p>
                        <a href="{{ route('suppliers.show', $supplier) }}" style="display: block; width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; font-weight: 700; color: #475569; text-align: center; text-decoration: none;">Cancel Import</a>
                    </div>
                </div>

                {{-- Main Content --}}
                <div style="flex: 1; display: flex; flex-direction: column; background: #ffffff; position: relative;">

                    {{-- Processing Overlay --}}
                    <div x-show="isProcessing" x-cloak style="position: absolute; inset: 0; background: rgba(255,255,255,0.8); z-index: 50; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px);">
                        <div style="text-align: center;">
                            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #064e3b; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
                            <p style="font-size: 14px; font-weight: 700; color: #064e3b;">Choice Recorded. Advancing...</p>
                        </div>
                    </div>

                    <template x-if="conflicts[activeIndex]">
                    <div style="flex: 1; padding: 32px; overflow-y: auto;">
                        <style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h2 style="font-size: 20px; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 12px;">
                                <span x-text="conflicts[activeIndex].layup_name"></span> Comparison
                                <span style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;" x-text="conflicts[activeIndex].full_layers.length + ' LAYERS'"></span>
                            </h2>
                            <span style="font-size: 12px; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                <div style="width: 8px; height: 8px; background: #e11d48; border-radius: 50%;"></div>
                                Differences highlighted in <span style="color: #e11d48; font-weight: 800;">Red</span>
                            </span>
                        </div>

                        {{-- Already resolved banner --}}
                        <div x-show="resolved[activeIndex] !== null" style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
                            <svg style="width: 20px; height: 20px; color: #059669;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span style="font-size: 14px; font-weight: 700; color: #065f46;" x-text="resolved[activeIndex] === 'incoming' ? 'Resolved: Accepted New Version' : 'Resolved: Kept Existing Version'"></span>
                        </div>

                        {{-- Tables Grid --}}
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">

                            {{-- Existing Side --}}
                            <div :style="resolved[activeIndex] === 'existing' ? 'opacity: 1' : (resolved[activeIndex] === 'incoming' ? 'opacity: 0.5' : 'opacity: 0.8')">
                                <div style="border: 2px solid transparent; border-radius: 16px; overflow: hidden; transition: all 0.3s;"
                                    :style="resolved[activeIndex] === 'existing' ? 'border-color: #3b82f6;' : 'border-color: #e2e8f0;'">
                                    <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #fcfcfc; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <svg style="width: 18px; height: 18px; color: #334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.105 2.239 2 5 2s5-.895 5-2V7m-10 0c0-1.105 2.239-2 5-2s5 .895 5 2m-10 0c0 1.105 2.239 2 5 2s5-.895 5-2"/></svg>
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800; color: #0f172a;">Existing Version</div>
                                                <div style="font-size: 11px; color: #94a3b8;">Database Record</div>
                                            </div>
                                        </div>
                                        <div x-show="resolved[activeIndex] === 'existing'" style="background: #3b82f6; color: white; border-radius: 50%; padding: 4px;"><svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                        <thead>
                                            <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">ORDER</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">THICKNESS (MM)</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">WIDTH (MM)</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">ANGLE (°)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="layer in conflicts[activeIndex].full_layers">
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 14px 20px; color: #64748b; font-weight: 600;" x-text="layer.order"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.thickness !== layer.incoming.thickness ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.existing ? layer.existing.thickness : '-'"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.width !== layer.incoming.width ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.existing ? layer.existing.width : '-'"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.angle !== layer.incoming.angle ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.existing ? layer.existing.angle : '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <div style="padding: 24px; border-top: 1px solid #f1f5f9; background: #fafafa;">
                                        <button @click="resolve('existing')"
                                            :disabled="resolved[activeIndex] !== null"
                                            :style="resolved[activeIndex] !== null ? 'width: 100%; padding: 14px; background: #f1f5f9; border: 2px solid #e2e8f0; border-radius: 12px; color: #94a3b8; font-size: 14px; font-weight: 800; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 8px;' : 'width: 100%; padding: 14px; background: white; border: 2px solid #064e3b; border-radius: 12px; color: #064e3b; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;'">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Keep Existing
                                        </button>
                                        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin-top: 8px;">Database remain unchanged for this layup.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Incoming Side --}}
                            <div :style="resolved[activeIndex] === 'incoming' ? 'opacity: 1' : (resolved[activeIndex] === 'existing' ? 'opacity: 0.5' : 'opacity: 0.8')">
                                <div style="border: 2px solid transparent; border-radius: 16px; overflow: hidden; transition: all 0.3s;"
                                    :style="resolved[activeIndex] === 'incoming' ? 'border-color: #10b981;' : 'border-color: #e2e8f0;'">
                                    <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #fcfcfc; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <svg style="width: 18px; height: 18px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <div>
                                                <div style="font-size: 14px; font-weight: 800; color: #0f172a;">Importing Version</div>
                                                <div style="font-size: 11px; color: #94a3b8;">Incoming JSON File</div>
                                            </div>
                                        </div>
                                        <div x-show="resolved[activeIndex] === 'incoming'" style="background: #10b981; color: white; border-radius: 50%; padding: 4px;"><svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>
                                    </div>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                        <thead>
                                            <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">ORDER</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">THICKNESS (MM)</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">WIDTH (MM)</th>
                                                <th style="padding: 12px 20px; text-align: left; font-weight: 800; color: #64748b; text-transform: uppercase;">ANGLE (°)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="layer in conflicts[activeIndex].full_layers">
                                                <tr :style="layer.has_conflict ? 'background: #fff5f5; border-bottom: 1px solid #fed7d7;' : 'border-bottom: 1px solid #f1f5f9;'">
                                                    <td style="padding: 14px 20px; color: #64748b; font-weight: 600;" x-text="layer.order"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.thickness !== layer.incoming.thickness ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.incoming ? layer.incoming.thickness : '-'"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.width !== layer.incoming.width ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.incoming ? layer.incoming.width : '-'"></td>
                                                    <td style="padding: 14px 20px; font-weight: 700;"
                                                        :style="layer.existing && layer.incoming && layer.existing.angle !== layer.incoming.angle ? 'color: #e11d48;' : 'color: #334155;'"
                                                        x-text="layer.incoming ? layer.incoming.angle : '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                    <div style="padding: 24px; border-top: 1px solid #f1f5f9; background: #fafafa;">
                                        <button @click="resolve('incoming')"
                                            :disabled="resolved[activeIndex] !== null"
                                            :style="resolved[activeIndex] !== null ? 'width: 100%; padding: 14px; background: #f1f5f9; border: none; border-radius: 12px; color: #94a3b8; font-size: 14px; font-weight: 800; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 8px;' : 'width: 100%; padding: 14px; background: #064e3b; border: none; border-radius: 12px; color: white; font-size: 14px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 6px -1px rgba(6, 78, 59, 0.4);'">
                                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Accept New
                                        </button>
                                        <p style="font-size: 11px; color: #94a3b8; text-align: center; margin-top: 8px;">Database will be updated with these values.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="padding: 20px 32px; border-top: 1px solid #f1f5f9; background: white; display: flex; justify-content: space-between; align-items: center;">
                        <button @click="if(activeIndex > 0) activeIndex--" :disabled="activeIndex === 0" :style="activeIndex === 0 ? 'background: none; border: none; font-size: 14px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; cursor: not-allowed; opacity: 0.3;' : 'background: none; border: none; font-size: 14px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; cursor: pointer; opacity: 1;'">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Previous Conflict
                        </button>

                        <div style="font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">
                            <span x-text="activeIndex + 1"></span> of <span x-text="conflicts.length"></span> discrepancies
                        </div>

                        <button @click="if(activeIndex < conflicts.length - 1) activeIndex++" :disabled="activeIndex === conflicts.length - 1" :style="activeIndex === conflicts.length - 1 ? 'background: none; border: none; font-size: 14px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; cursor: not-allowed; opacity: 0.3;' : 'background: none; border: none; font-size: 14px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; cursor: pointer; opacity: 1;'">
                            Next Conflict
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                    </template>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
