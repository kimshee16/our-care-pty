@extends('layouts.dashboard')

@section('page-title', 'Document Signing')

@section('content')
<div class="dashboard-content">
    <div class="dashboard-header">
        <h1>Document Signing</h1>
    </div>

    <style>
        .signing-panel {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 22px;
            padding: 20px;
        }

        .signing-panel h2 {
            color: var(--text-primary);
            font-size: 20px;
            margin: 0 0 16px;
        }

        .signing-alert {
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 18px;
            padding: 14px 16px;
        }

        .signing-alert.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .signing-alert.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .signing-alert.warning {
            background: #fff4e5;
            border: 1px solid #ffddb3;
            color: #7a5a16;
        }

        .signing-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .signing-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .signing-field.full {
            grid-column: 1 / -1;
        }

        .signing-field.message-field {
            grid-column: 2;
            grid-row: 1 / span 3;
        }

        .signing-field label {
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 700;
        }

        .signing-field input,
        .signing-field select,
        .signing-field textarea {
            border: 1px solid #d9dee3;
            border-radius: 6px;
            color: var(--text-primary);
            font: inherit;
            min-height: 42px;
            padding: 10px 12px;
            width: 100%;
        }

        .signing-field textarea {
            flex: 1;
            min-height: 90px;
            resize: vertical;
        }

        .signature-placement {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            grid-column: 1 / -1;
            min-width: 0;
            padding: 14px;
        }

        .signature-placement h3 {
            color: var(--text-primary);
            font-size: 15px;
            margin: 0 0 10px;
        }

        .signature-placement-help {
            margin: 0 0 12px;
        }

        .signature-placement-table {
            min-width: 620px;
            table-layout: fixed;
        }

        .signature-placement-table .signature-file-column {
            width: 40%;
        }

        .signature-placement-table th,
        .signature-placement-table td {
            padding: 9px 10px;
        }

        .signature-placement-table .signature-file-name {
            display: block;
            overflow-wrap: anywhere;
        }

        .signature-placement-table .is-active,
        .signature-placement-table .is-active:hover {
            background: #f2edff;
        }

        .signature-placement-table .is-active th {
            box-shadow: inset 3px 0 var(--accent);
        }

        .signature-placement-table input {
            border: 1px solid #cfd6dd;
            border-radius: 5px;
            color: var(--text-primary);
            font: inherit;
            min-height: 34px;
            padding: 6px 8px;
            width: 100%;
            max-width: 90px;
        }

        .signature-page-count {
            display: block;
            margin-top: 3px;
        }

        .signature-preview {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-top: 12px;
            overflow: hidden;
        }

        .signature-document-header {
            align-items: center;
            background: #f8f9fa;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            padding: 10px 12px;
        }

        .signature-document-header strong {
            color: var(--text-primary);
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .signature-preview-title {
            min-width: 0;
        }

        .signature-preview-shell {
            background: #f3f4f6;
            padding: 16px;
        }

        .signature-page-stage {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.14);
            margin: 0 auto;
            position: relative;
            width: fit-content;
        }

        .signature-page-stage canvas {
            display: block;
        }

        .signature-marker {
            align-items: center;
            background: rgba(108, 79, 209, 0.10);
            border: 2px solid var(--accent);
            border-radius: 6px;
            color: var(--accent);
            cursor: grab;
            display: flex;
            font-size: 12px;
            font-weight: 800;
            height: 42px;
            justify-content: center;
            left: 0;
            position: absolute;
            top: 0;
            touch-action: none;
            width: 132px;
            z-index: 2;
        }

        .signature-marker:active {
            cursor: grabbing;
        }

        .signature-preview-error {
            color: #7a5a16;
            font-size: 13px;
            font-weight: 700;
            padding: 12px;
        }

        .field-error {
            color: #b42318;
            font-size: 12px;
            font-weight: 600;
        }

        .signing-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .signing-actions form {
            margin: 0;
        }

        .signing-btn {
            align-items: center;
            border: 0;
            border-radius: 6px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            gap: 7px;
            min-height: 34px;
            padding: 8px 11px;
            text-decoration: none;
            white-space: nowrap;
        }

        .signing-btn.primary {
            background: var(--accent);
        }

        .signing-btn.secondary {
            background: #0f7c90;
        }

        .signing-btn.warning {
            background: #d39e00;
        }

        .signing-btn.danger {
            background: #dc3545;
        }

        .signing-btn.neutral {
            background: #6c757d;
        }

        .signing-table-wrap {
            overflow-x: auto;
        }

        .signing-table {
            border-collapse: collapse;
            font-size: 14px;
            width: 100%;
        }

        .signing-table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .signing-table th,
        .signing-table td {
            padding: 12px;
            text-align: left;
            vertical-align: middle;
        }

        .signing-table tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        .signing-table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            border-radius: 999px;
            display: inline-block;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 10px;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-draft,
        .status-failed {
            background: #fff3cd;
            color: #856404;
        }

        .status-sent,
        .status-signing_ready {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-voided,
        .status-declined {
            background: #f8d7da;
            color: #721c24;
        }

        .muted {
            color: var(--text-secondary);
            font-size: 13px;
        }

        @media (max-width: 900px) {
            .signing-form-grid {
                grid-template-columns: 1fr;
            }

            .signing-field.message-field {
                grid-column: auto;
                grid-row: auto;
            }

            .signature-document-header {
                flex-wrap: wrap;
            }
        }
    </style>

    @if(session('success'))
        <div class="signing-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="signing-alert error">{{ session('error') }}</div>
    @endif

    @if($syncFailed)
        <div class="signing-alert warning" role="status">
            Some signing statuses could not be refreshed from DocuSign. Their last saved statuses are shown. Reload the page to try again.
        </div>
    @endif

    @if($configMissing)
        <div class="signing-alert warning">
            DocuSign API is not fully configured. Missing: {{ implode(', ', $configMissing) }}.
        </div>
    @endif

    @if($docusignConsentUrl)
        <div class="signing-alert warning">
            DocuSign needs one-time JWT consent for this integration.
            <a href="{{ $docusignConsentUrl }}" target="_blank" rel="noopener">Open DocuSign consent</a>,
            accept the signature and impersonation scopes, then retry sending the request.
        </div>
    @endif

    @if($isAdmin)
        <div class="signing-panel">
            <h2>Create Signing Request</h2>
            <form method="POST" action="{{ url('/admin/document-signing') }}" enctype="multipart/form-data">
                @csrf
                <div class="signing-form-grid">
                    <div class="signing-field">
                        <label for="title">Document Title</label>
                        <input id="title" type="text" name="title" value="{{ old('title') }}" required>
                        @error('title') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="signing-field">
                        <label for="documents">Upload Documents</label>
                        <input id="documents" type="file" name="documents[]" accept=".pdf,.doc,.docx" multiple required>
                        @error('documents') <span class="field-error">{{ $message }}</span> @enderror
                        @error('documents.*') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="signing-field">
                        <label for="client_id">Client</label>
                        <select id="client_id" name="client_id" required>
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                    {{ trim($client->first_name . ' ' . $client->last_name) }} - {{ $client->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="signing-field message-field">
                        <label for="message">Message</label>
                        <textarea id="message" name="message">{{ old('message') }}</textarea>
                        @error('message') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="signature-placement" id="signaturePlacement" hidden>
                        <h3>Signature Placement</h3>
                        <p class="muted signature-placement-help" id="signaturePlacementHelp">One signature per file. Preview a PDF to drag its signature, or edit the page and X / Y position (from the top-left) in its row. Word files use manual placement.</p>
                        <div class="signing-table-wrap">
                            <table class="signing-table signature-placement-table" aria-label="Signature positions by file" aria-describedby="signaturePlacementHelp">
                                <thead>
                                    <tr>
                                        <th scope="col" class="signature-file-column">File</th>
                                        <th scope="col">Page</th>
                                        <th scope="col">X</th>
                                        <th scope="col">Y</th>
                                        <th scope="col">Preview</th>
                                    </tr>
                                </thead>
                                <tbody id="signaturePlacementList"></tbody>
                            </table>
                        </div>
                        <div class="signature-preview" id="signaturePreview" hidden>
                            <div class="signature-document-header">
                                <div class="signature-preview-title">
                                    <strong id="signaturePreviewName"></strong>
                                    <div class="muted" id="signaturePreviewStatus" role="status"></div>
                                </div>
                                <button type="button" class="signing-btn neutral" id="signaturePreviewClose">Hide preview</button>
                            </div>
                            <div class="signature-preview-shell" id="signaturePreviewShell"></div>
                        </div>
                    </div>
                </div>

                <div class="signing-actions" style="margin-top: 16px;">
                    <button type="submit" class="signing-btn primary">
                        <i class="fas fa-plus"></i>
                        Create Request
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="signing-panel">
        <h2>{{ $isAdmin ? 'All Signing Requests' : 'Documents For Signature' }}</h2>
        <div class="signing-table-wrap">
            <table class="signing-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        @if($isAdmin)
                            <th>Client</th>
                        @endif
                        <th>Document</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Signed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        <tr>
                            <td><strong>{{ $document->displayReference() }}</strong></td>
                            @if($isAdmin)
                                <td>
                                    <strong>{{ trim(($document->client->first_name ?? '') . ' ' . ($document->client->last_name ?? '')) ?: 'Client #' . $document->client_id }}</strong><br>
                                    <span class="muted">{{ $document->client->email ?? 'No email' }}</span>
                                </td>
                            @endif
                            <td>
                                <strong>{{ $document->title }}</strong><br>
                                @forelse($document->documentDisplayNames() as $fileName)
                                    <span class="muted">{{ $fileName }}</span>@if(! $loop->last)<br>@endif
                                @empty
                                    <span class="muted">No file</span>
                                @endforelse
                            </td>
                            <td>
                                <span class="status-badge status-{{ $document->status }}">{{ str_replace('_', ' ', $document->status) }}</span>
                                @if($document->provider_status)
                                    <br><span class="muted">DocuSign: {{ $document->provider_status }}</span>
                                @endif
                                @if($document->docusign_envelope_id)
                                    <br><span class="muted">Envelope: {{ $document->docusign_envelope_id }}</span>
                                @endif
                            </td>
                            <td>{{ $document->sent_at ? $document->sent_at->format('M d, Y H:i') : 'Not sent' }}</td>
                            <td>{{ $document->signed_at ? $document->signed_at->format('M d, Y H:i') : 'Pending' }}</td>
                            <td>
                                <div class="signing-actions">
                                    @if($document->isComplete())
                                        <a class="signing-btn neutral" href="{{ url(($isAdmin ? '/admin' : '/client') . '/document-signing/' . $document->id . '/download') }}" title="Download signed document" aria-label="Download signed document">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @else
                                        @foreach($document->documentFiles() as $fileIndex => $file)
                                            <a class="signing-btn neutral" href="{{ url(($isAdmin ? '/admin' : '/client') . '/document-signing/' . $document->id . '/download/' . $fileIndex) }}" title="Download {{ $file['original_name'] ?? 'source document' }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endforeach
                                    @endif

                                    @if($isAdmin)
                                        @if($document->canBeSent())
                                            <form method="POST" action="{{ url('/admin/document-signing/' . $document->id . '/send') }}">
                                                @csrf
                                                <button type="submit" class="signing-btn primary" title="Send through DocuSign">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if(! in_array($document->status, ['completed', 'voided'], true))
                                            <form method="POST" action="{{ url('/admin/document-signing/' . $document->id . '/void') }}" onsubmit="return confirm('Void this signing request?');">
                                                @csrf
                                                <button type="submit" class="signing-btn danger" title="Void request">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        @if(in_array($document->status, ['sent', 'signing_ready'], true))
                                            <form method="POST" action="{{ url('/client/document-signing/' . $document->id . '/sign') }}">
                                                @csrf
                                                <button type="submit" class="signing-btn primary">
                                                    <i class="fas fa-file-signature"></i>
                                                    Sign
                                                </button>
                                            </form>
                                        @endif

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}" class="muted">No document signing requests yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const documentsInput = document.getElementById('documents');
        const placementPanel = document.getElementById('signaturePlacement');
        const placementList = document.getElementById('signaturePlacementList');
        const preview = document.getElementById('signaturePreview');
        const previewName = document.getElementById('signaturePreviewName');
        const previewStatus = document.getElementById('signaturePreviewStatus');
        const previewShell = document.getElementById('signaturePreviewShell');
        const closePreview = document.getElementById('signaturePreviewClose');
        let placements = [];
        let activePlacement = null;
        let renderVersion = 0;
        let activeMarker = null;

        if (!documentsInput || !placementPanel || !placementList) {
            return;
        }

        if (window.pdfjsLib) {
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        function makeNumberInput(name, value, min, label) {
            const input = document.createElement('input');
            input.type = 'number';
            input.name = name;
            input.value = value;
            input.min = min;
            input.step = '1';
            input.required = true;
            input.setAttribute('aria-label', label);
            return input;
        }

        function clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }

        function updateMarker() {
            if (!activeMarker) return;
            const { placement, marker, scale, width, height } = activeMarker;
            const x = clamp(parseInt(placement.xInput.value || '0', 10), 0, Math.max(0, Math.floor(width - 96)));
            const y = clamp(parseInt(placement.yInput.value || '0', 10), 0, Math.max(0, Math.floor(height - 30)));
            placement.xInput.value = x;
            placement.yInput.value = y;
            marker.style.left = `${x * scale}px`;
            marker.style.top = `${y * scale}px`;
        }

        function attachDrag(stage, canvas, placement, scale) {
            let dragging = false;
            function moveToEvent(event) {
                if (!activeMarker || activeMarker.placement !== placement) return;
                const rect = canvas.getBoundingClientRect();
                placement.xInput.value = Math.round((event.clientX - rect.left) / scale - 48);
                placement.yInput.value = Math.round((event.clientY - rect.top) / scale - 15);
                updateMarker();
            }
            stage.addEventListener('pointerdown', function (event) {
                if (event.button !== 0) return;
                dragging = true;
                stage.setPointerCapture?.(event.pointerId);
                moveToEvent(event);
            });
            stage.addEventListener('pointermove', function (event) {
                if (dragging) moveToEvent(event);
            });
            ['pointerup', 'pointercancel', 'lostpointercapture'].forEach(function (type) {
                stage.addEventListener(type, () => { dragging = false; });
            });
        }

        async function renderPreview(placement) {
            const version = ++renderVersion;
            activeMarker = null;
            previewShell.replaceChildren();
            previewStatus.textContent = 'Loading preview...';
            previewShell.setAttribute('aria-busy', 'true');

            try {
                if (!window.pdfjsLib) throw new Error('PDF preview is unavailable.');
                if (!placement.loadingTask) {
                    placement.objectUrl = URL.createObjectURL(placement.file);
                    placement.loadingTask = window.pdfjsLib.getDocument(placement.objectUrl);
                }
                const pdf = await placement.loadingTask.promise;
                if (version !== renderVersion) return;
                placement.pageInput.max = pdf.numPages;
                placement.pageCount.textContent = `of ${pdf.numPages}`;
                const pageNumber = clamp(parseInt(placement.pageInput.value || '1', 10), 1, pdf.numPages);
                placement.pageInput.value = pageNumber;
                const page = await pdf.getPage(pageNumber);
                if (version !== renderVersion) return;

                const baseViewport = page.getViewport({ scale: 1 });
                const availableWidth = Math.max(1, previewShell.clientWidth - 32);
                const headerHeight = preview.querySelector('.signature-document-header').offsetHeight;
                // Fit the whole page, leaving room for the toolbar, padding and screen margins.
                const availableHeight = Math.max(1, window.innerHeight - headerHeight - 96);
                const scale = Math.min(1.4, availableWidth / baseViewport.width, availableHeight / baseViewport.height);
                const viewport = page.getViewport({ scale });
                // Each render owns its canvas so rapid file/page changes cannot overlap.
                const stage = document.createElement('div');
                const canvas = document.createElement('canvas');
                const marker = document.createElement('div');
                stage.className = 'signature-page-stage';
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                stage.style.width = `${canvas.width}px`;
                stage.style.height = `${canvas.height}px`;
                marker.className = 'signature-marker';
                marker.textContent = 'Signature';
                marker.style.width = `${96 * scale}px`;
                marker.style.height = `${30 * scale}px`;
                marker.style.fontSize = `${9 * scale}px`;
                marker.style.boxSizing = 'border-box';
                await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                if (version !== renderVersion) return;

                stage.append(canvas, marker);
                previewShell.replaceChildren(stage);
                activeMarker = { placement, marker, scale, width: baseViewport.width, height: baseViewport.height };
                updateMarker();
                attachDrag(stage, canvas, placement, scale);
                previewStatus.textContent = `Page ${pageNumber} of ${pdf.numPages}. Full page view. Click or drag to place the signature.`;
            } catch (error) {
                if (version !== renderVersion) return;
                previewStatus.textContent = 'Preview unavailable. Enter the page and X / Y position in the file row.';
                const message = document.createElement('div');
                message.className = 'signature-preview-error';
                message.textContent = 'This PDF could not be previewed. You can still set its signature position manually.';
                previewShell.replaceChildren(message);
            } finally {
                if (version === renderVersion) previewShell.setAttribute('aria-busy', 'false');
            }
        }

        function selectPlacement(placement) {
            activePlacement = placement;
            placements.forEach(function (item) {
                const selected = item === placement;
                item.row.classList.toggle('is-active', selected);
                if (item.previewButton) {
                    item.previewButton.textContent = selected ? 'Viewing' : 'Preview';
                    item.previewButton.setAttribute('aria-expanded', String(selected));
                }
            });
            preview.hidden = !placement;
            if (placement) {
                previewName.textContent = `${placement.index + 1}. ${placement.file.name}`;
                renderPreview(placement);
            } else {
                ++renderVersion;
                activeMarker = null;
                previewShell.replaceChildren();
            }
        }

        function createPlacementRow(file, index) {
            const row = document.createElement('tr');
            const nameCell = document.createElement('th');
            const name = document.createElement('strong');
            const type = document.createElement('span');
            const pageCount = document.createElement('span');
            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
            const label = `File ${index + 1}: ${file.name}`;
            const pageInput = makeNumberInput(`signature_positions[${index}][page]`, '1', '1', `Signature page for ${label}`);
            const xInput = makeNumberInput(`signature_positions[${index}][x]`, '420', '0', `Signature X position for ${label}`);
            const yInput = makeNumberInput(`signature_positions[${index}][y]`, '650', '0', `Signature Y position for ${label}`);
            const placement = { file, index, row, pageInput, xInput, yInput, pageCount };
            nameCell.scope = 'row';
            name.className = 'signature-file-name';
            name.textContent = `${index + 1}. ${file.name}`;
            type.className = 'muted';
            type.textContent = isPdf ? 'PDF' : 'Word - Manual placement';
            nameCell.append(name, type);
            row.append(nameCell);
            pageCount.className = 'muted signature-page-count';
            [pageInput, xInput, yInput].forEach(function (input) {
                const cell = document.createElement('td');
                cell.append(input);
                if (input === pageInput) cell.append(pageCount);
                row.append(cell);
            });
            const action = document.createElement('td');
            if (isPdf) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'signing-btn primary';
                button.textContent = 'Preview';
                button.setAttribute('aria-label', `Preview ${label}`);
                button.setAttribute('aria-controls', 'signaturePreview');
                button.setAttribute('aria-expanded', 'false');
                button.addEventListener('click', () => selectPlacement(placement));
                placement.previewButton = button;
                action.append(button);
            } else {
                const message = document.createElement('span');
                message.className = 'muted';
                message.textContent = 'Not available';
                action.append(message);
            }
            row.append(action);
            pageInput.addEventListener('change', function () {
                if (activePlacement === placement) renderPreview(placement);
            });
            [xInput, yInput].forEach(function (input) {
                input.addEventListener('input', function () {
                    if (activePlacement === placement && input.value !== '') updateMarker();
                });
            });
            return placement;
        }

        closePreview.addEventListener('click', function () {
            const button = activePlacement?.previewButton;
            selectPlacement(null);
            button?.focus();
        });

        documentsInput.addEventListener('change', function () {
            selectPlacement(null);
            placements.forEach(function (placement) {
                if (placement.loadingTask) placement.loadingTask.destroy().catch(() => {});
                if (placement.objectUrl) URL.revokeObjectURL(placement.objectUrl);
            });
            placementList.replaceChildren();
            placements = Array.from(documentsInput.files).map(createPlacementRow);
            placementPanel.hidden = placements.length === 0;
            placements.forEach(placement => placementList.append(placement.row));
            const firstPdf = placements.find(placement => placement.previewButton);
            if (firstPdf) selectPlacement(firstPdf);
        });

        let resizeTimer;
        let previousWidth = placementPanel.clientWidth;
        function schedulePreviewResize() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                if (activePlacement) renderPreview(activePlacement);
            }, 150);
        }
        const resizeObserver = new ResizeObserver(function () {
            const width = placementPanel.clientWidth;
            if (width === previousWidth) return;
            previousWidth = width;
            schedulePreviewResize();
        });
        resizeObserver.observe(placementPanel);
        window.addEventListener('resize', schedulePreviewResize);

    });
</script>
@endsection
