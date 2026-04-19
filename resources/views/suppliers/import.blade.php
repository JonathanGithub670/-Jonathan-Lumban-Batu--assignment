<x-app-layout>
    {{-- Pop-up Simulator Backdrop --}}
    <div style="background: rgba(15, 23, 42, 0.7); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        
        {{-- The "Pop-up" Card --}}
        <div style="background: white; width: 100%; max-width: 580px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); position: relative; overflow: hidden;">
            
            {{-- Header --}}
            <div style="padding: 24px 32px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.01em;">Import Layup Data</h2>
                <a href="{{ route('suppliers.show', $supplier) }}" style="color: #94a3b8; text-decoration: none;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>

            <div style="padding: 32px;" x-data="{ fileName: '', hasFile: false }">
                
                {{-- Error Alerts --}}
                @if(session('error') || $errors->any())
                <div style="background: #fff1f2; border: 1px solid #fecaca; border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; gap: 12px; align-items: flex-start;">
                    <div style="color: #e11d48; margin-top: 2px;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 style="font-size: 14px; font-weight: 800; color: #9f1239; margin: 0 0 4px 0;">Import Error</h4>
                        <p style="font-size: 13px; color: #e11d48; margin: 0;">{{ session('error') ?? $errors->first() }}</p>
                    </div>
                </div>
                @endif
                
                <form method="POST" action="{{ route('suppliers.import.analyze', $supplier) }}" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Dropzone Area --}}
                    <div style="margin-bottom: 24px;">
                        <label for="file" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 160px; border: 2px dashed #cbd5e1; border-radius: 12px; cursor: pointer; background: #f8fafc; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.borderColor='#94a3b8'" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'">
                            <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                                <div style="background: white; padding: 10px; border-radius: 50%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 12px;">
                                    <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <p style="font-size: 14px; margin: 0; color: #64748b;">
                                    <span style="font-weight: 700; color: #059669;">Click to upload</span> or drag and drop
                                </p>
                                <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">CSV or JSON up to 10MB</p>
                            </div>
                            <input id="file" name="file" type="file" accept=".json" style="display: none;" @change="fileName = $event.target.files[0].name; hasFile = true" required />
                        </label>
                        <template x-if="hasFile">
                            <div style="margin-top: 12px; font-size: 13px; color: #059669; font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Selected: <span x-text="fileName"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Conflict Resolution Strategy --}}
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">Conflict Resolution Strategy</label>
                        <div style="position: relative;">
                            <select name="strategy" style="width: 100%; padding: 12px 16px; border: 1px solid #e2e8f0; border-radius: 8px; appearance: none; font-size: 14px; color: #1e293b; background: white;">
                                <option value="skip">Skip conflicts (Default)</option>
                                <option value="overwrite">Overwrite existing</option>
                                <option value="duplicate">Duplicate layup</option>
                                <option value="reject">Reject entire import</option>
                                <option value="manual">Manual resolution</option>
                            </select>
                            <div style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #94a3b8;">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Dry Run Checkbox --}}
                    <div style="background: #f8fafc; border-radius: 12px; padding: 16px; display: flex; align-items: flex-start; gap: 12px; margin-bottom: 24px;">
                        <input type="checkbox" name="dry_run" id="dry_run" style="width: 18px; height: 18px; border-radius: 4px; border: 2px solid #cbd5e1; margin-top: 2px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label for="dry_run" style="font-size: 14px; font-weight: 700; color: #1e293b; cursor: pointer;">Run as Dry Run</label>
                                <svg style="width: 16px; height: 16px; color: #475569;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <p style="font-size: 12px; color: #64748b; margin: 4px 0 0 0;">Simulate the import process without saving changes to the database.</p>
                        </div>
                    </div>

                    {{-- Potential Conflicts Alert Section --}}
                    @if(session('conflicts_detected'))
                    </form>
                    <div style="background: #fff5f5; border: 1px solid #feb2b2; border-radius: 12px; padding: 20px; display: flex; gap: 16px; margin-bottom: 24px;">
                        <div style="color: #c53030; margin-top: 2px;">
                            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <h4 style="font-size: 15px; font-weight: 900; color: #9b2c2c; margin: 0 0 6px 0; letter-spacing: -0.01em;">Potential Conflicts Detected</h4>
                            @php $analysis = session('import_analysis'); @endphp
                            <p style="font-size: 13px; color: #c53030; margin: 0; line-height: 1.6; font-weight: 500;">
                                {{ count($analysis['conflicts']) }} Layups differ significantly from current suppliers in the database.
                                <a href="{{ route('suppliers.import.review', $supplier) }}" style="color: #9b2c2c; font-weight: 900; text-decoration: underline; margin-left: 4px;">View details</a>
                            </p>
                        </div>
                    </div>

                    {{-- Separate confirm form — no file upload needed --}}
                    <form method="POST" action="{{ route('suppliers.import.analyze', $supplier) }}">
                        @csrf
                        <input type="hidden" name="confirmed" value="1">
                        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
                            <a href="{{ route('suppliers.show', $supplier) }}" style="padding: 12px 24px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #475569; text-decoration: none; text-align: center;">Cancel</a>
                            <button type="submit" style="padding: 12px 24px; background: #064e3b; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; color: white; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Confirm Import
                            </button>
                        </div>
                    </form>
                    @else

                    {{-- Footer Buttons --}}
                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;">
                        <a href="{{ route('suppliers.show', $supplier) }}" style="padding: 12px 24px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 14px; font-weight: 700; color: #475569; text-decoration: none; text-align: center;">Cancel</a>
                        <button type="submit" style="padding: 12px 24px; background: #064e3b; border: none; border-radius: 10px; font-size: 14px; font-weight: 700; color: white; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Analyze & Import
                        </button>
                    </div>

                </form>
                    @endif
            </div>

        </div>
    </div>
</x-app-layout>
