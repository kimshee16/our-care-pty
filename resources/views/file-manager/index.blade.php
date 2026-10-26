@extends('layouts.dashboard')
@section('page-title', 'File Manager')
@section('content')
<style>
    .fm [hidden] { display:none !important; }
    .fm { padding-top: 28px; } .fm h1 { font-size: 30px; margin-bottom: 6px; }
    .fm p { color: #6c757d; line-height: 1.6; } .fm-heading, .fm-toolbar, .fm-actions, .fm-crumbs { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .fm-heading { justify-content:space-between; margin-bottom:24px; } .fm-badge { border-radius:20px; padding:8px 14px; background:#eee8fb; color:#6244a5; font-size:13px; }
    .fm-panel { background:white; border:1px solid #e5e7eb; border-radius:12px; margin-bottom:20px; overflow:hidden; }
    .fm-toolbar { justify-content:space-between; padding:20px; border-bottom:1px solid #e5e7eb; }
    .fm button, .fm input, .fm dialog { font:inherit; } .fm button, .fm-link { cursor:pointer; border:1px solid #ddd6ec; background:white; color:#674cbf; padding:9px 13px; border-radius:7px; text-decoration:none; font-size:14px; display:inline-flex; gap:7px; align-items:center; }
    .fm button.primary { background:#674cbf; color:white; border-color:#674cbf; } .fm button.danger { color:#b42318; } .fm button:disabled { opacity:.5; cursor:not-allowed; }
    .fm input { padding:10px 12px; border:1px solid #d8dce3; border-radius:7px; max-width:100%; } .fm input[type=text] { width:280px; }
    .fm-crumbs { padding:16px 20px; background:#fcfbfe; font-size:14px; } .fm-crumbs a { color:#674cbf; text-decoration:none; } .fm-crumbs span { overflow-wrap:anywhere; }
    .fm-scroll { overflow-x:auto; } .fm table { width:100%; border-collapse:collapse; text-align:left; } .fm th { padding:14px 20px; font-size:12px; text-transform:uppercase; color:#727986; background:#f9fafb; letter-spacing:.04em; } .fm td { padding:16px 20px; border-top:1px solid #eef0f3; font-size:14px; } .fm td:first-child { min-width:240px; max-width:420px; overflow-wrap:anywhere; }
    .fm-file { display:flex; align-items:center; gap:12px; } .fm-file i { font-size:24px; color:#a88bde; } .fm-file a { color:#42375c; font-weight:600; text-decoration:none; } .fm-row-actions { display:flex; gap:6px; } .fm-row-actions button, .fm-row-actions a { font-size:12px; padding:7px 9px; white-space:nowrap; }
    .fm-empty { text-align:center; padding:60px 20px; } .fm-empty i { font-size:42px; color:#b9a5dd; margin-bottom:18px; } .fm-empty h2 { font-size:20px; margin-bottom:8px; }
    .fm-alert { padding:14px 18px; border-radius:8px; background:#fff3e8; color:#854b20; margin-bottom:18px; } .fm-alert.success { background:#eaf7ee; color:#256d3b; } .fm-alert ul { margin-left:20px; }
    .fm dialog { margin:auto; width:min(520px, calc(100% - 32px)); padding:26px; border:0; border-radius:12px; color:#2c3e50; box-shadow:0 20px 60px #0003; } .fm dialog::backdrop { background:#20203480; } .fm dialog h2 { font-size:22px; margin-bottom:18px; } .fm dialog label { display:block; margin:16px 0 8px; } .fm dialog input[type=text] { width:100%; } .fm dialog .fm-actions { justify-content:flex-end; margin-top:24px; } .fm-picker { max-height:260px; overflow:auto; display:grid; gap:6px; margin-top:12px; } .fm-picker button { text-align:left; overflow-wrap:anywhere; } .fm small { color:#6c757d; } .fm-page { padding:18px 20px; display:flex; justify-content:flex-end; }
    @media(max-width:768px) { .fm-toolbar form { width:100%; } .fm-toolbar input[type=search] { width:65%; } .fm-heading { align-items:flex-start; } }
</style>
<div class="dashboard-content fm">
    <div class="fm-heading"><div><h1>File Manager</h1><p>Keep your team’s folders and files together in Google Shared Drive.</p></div><span class="fm-badge"><i class="fab fa-google-drive" aria-hidden="true"></i> {{ $configured && !$error ? 'Google Shared Drive' : 'Connection needed' }}</span></div>
    @if(session('success'))<div class="fm-alert success" role="status">{{ session('success') }}</div>@endif
    @if($error || session('drive_error'))<div class="fm-alert" role="alert">{{ $error ?? session('drive_error') }} <a href="{{ route('file-manager.index') }}">Return to shared drive</a></div>@endif
    @if($errors->any())<div class="fm-alert" role="alert"><ul>@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
    @if(!$configured)
        <div class="fm-panel fm-empty"><i class="fab fa-google-drive" aria-hidden="true"></i><h2>Connect your Google Shared Drive</h2><p>Your administrator needs to configure the Shared Drive and service account.<br>Once connected, your folders and files will appear here.</p></div>
    @elseif(!$error)
        <div class="fm-panel">
            <div class="fm-toolbar">
                <form method="GET" action="{{ route('file-manager.index') }}" class="fm-actions"><input type="hidden" name="folder" value="{{ $folderId }}"><input aria-label="Search this folder" type="search" name="search" value="{{ $search }}" placeholder="Search this folder…" maxlength="200"><button type="submit"><i class="fas fa-search" aria-hidden="true"></i> Search</button>@if($search !== '')<a class="fm-link" href="{{ route('file-manager.index', ['folder' => $folderId]) }}">Clear</a>@endif</form>
                <div class="fm-actions"><button type="button" data-dialog="fm-new" @disabled(!($folder['capabilities']['canAddChildren'] ?? false))><i class="fas fa-folder-plus" aria-hidden="true"></i> New folder</button><button type="button" class="primary" data-dialog="fm-upload" @disabled(!($folder['capabilities']['canAddChildren'] ?? false))><i class="fas fa-upload" aria-hidden="true"></i> Upload file</button></div>
            </div>
            <nav class="fm-crumbs" aria-label="Folder path">@foreach($breadcrumbs as $crumb)@unless($loop->first)<span aria-hidden="true">/</span>@endunless @if($loop->last)<span aria-current="page">{{ $crumb['name'] }}</span>@else<a href="{{ route('file-manager.index', ['folder' => $crumb['id']]) }}">{{ $crumb['name'] }}</a>@endif @endforeach</nav>
            @if(count($listing['files']))
                <div class="fm-scroll"><table><thead><tr><th>Name</th><th>Size</th><th>Modified</th><th>Actions</th></tr></thead><tbody>
                @foreach($listing['files'] as $file)
                    @php
                        $isFolder = $file['mimeType'] === \App\Services\GoogleDriveService::FOLDER;
                        $caps = $file['capabilities'] ?? [];
                        $native = str_starts_with($file['mimeType'], 'application/vnd.google-apps.');
                        $downloadable = !$native || in_array($file['mimeType'], ['application/vnd.google-apps.document', 'application/vnd.google-apps.spreadsheet', 'application/vnd.google-apps.presentation', 'application/vnd.google-apps.drawing']);
                    @endphp
                    <tr><td><div class="fm-file"><i class="fas {{ $isFolder ? 'fa-folder' : 'fa-file-alt' }}" aria-hidden="true"></i>@if($isFolder)<a href="{{ route('file-manager.index', ['folder' => $file['id']]) }}">{{ $file['name'] }}</a>@else<span>{{ $file['name'] }}</span>@endif</div></td>
                    <td>{{ isset($file['size']) ? \Illuminate\Support\Number::fileSize((int) $file['size']) : '—' }}</td><td>{{ isset($file['modifiedTime']) ? \Carbon\Carbon::parse($file['modifiedTime'])->format('d M Y, H:i') : '—' }}</td>
                    <td><div class="fm-row-actions">
                        @if($downloadable && ($caps['canDownload'] ?? false))<a class="fm-link" href="{{ route('file-manager.download', $file['id']) }}" aria-label="Download {{ $file['name'] }}">Download</a>@endif
                        @if($caps['canEdit'] ?? false)<button type="button" data-rename="{{ route('file-manager.update', $file['id']) }}" data-name="{{ $file['name'] }}">Rename</button>@endif
                        @if(($caps['canMoveItemWithinDrive'] ?? false) && ($caps['canEdit'] ?? false))<button type="button" data-move="{{ route('file-manager.update', $file['id']) }}" data-id="{{ $file['id'] }}" data-name="{{ $file['name'] }}">Move</button>@endif
                        @if($caps['canTrash'] ?? false)<button type="button" class="danger" data-trash="{{ route('file-manager.trash', $file['id']) }}" data-name="{{ $file['name'] }}" data-folder="{{ $isFolder ? '1' : '0' }}">Trash</button>@endif
                    </div></td></tr>
                @endforeach
                </tbody></table></div>
            @else
                <div class="fm-empty"><i class="fas {{ $search !== '' ? 'fa-search' : 'fa-folder-open' }}" aria-hidden="true"></i><h2>{{ $search !== '' ? 'No matching files' : 'This folder is empty' }}</h2><p>{{ $search !== '' ? 'Try a different name or clear your search.' : 'Create a folder or upload your first file to get started.' }}</p></div>
            @endif
            @if(!empty($listing['nextPageToken']))<div class="fm-page"><a class="fm-link" href="{{ route('file-manager.index', ['folder' => $folderId, 'search' => $search, 'page' => $listing['nextPageToken']]) }}">Next page →</a></div>@endif
        </div>
        <small>Files are stored in Google Shared Drive. Google documents download as PDF; spreadsheets download as Excel. Modified times: {{ config('app.timezone') }}.</small>
    @endif

    <dialog id="fm-new"><h2>New folder</h2><form method="POST" action="{{ route('file-manager.folders') }}">@csrf<input type="hidden" name="folder" value="{{ $folderId }}"><label for="fm-folder-name">Folder name</label><input id="fm-folder-name" type="text" name="name" maxlength="255" required><div class="fm-actions"><button type="button" data-close>Cancel</button><button class="primary">Create folder</button></div></form></dialog>
    <dialog id="fm-upload"><h2>Upload file</h2><form method="POST" enctype="multipart/form-data" action="{{ route('file-manager.upload') }}">@csrf<input type="hidden" name="folder" value="{{ $folderId }}"><label for="fm-file">Choose a file</label><input id="fm-file" type="file" name="file" required><p><small>Up to {{ $maxUploadMb }} MB per file. Server upload limits may also apply.</small></p><p id="fm-upload-status" role="status"></p><div class="fm-actions"><button type="button" data-close>Cancel</button><button class="primary">Upload to Drive</button></div></form></dialog>
    <dialog id="fm-rename"><h2>Rename</h2><form method="POST">@csrf @method('PATCH')<input type="hidden" name="folder" value="{{ $folderId }}"><label for="fm-rename-name">Name</label><input id="fm-rename-name" type="text" name="name" maxlength="255" required><div class="fm-actions"><button type="button" data-close>Cancel</button><button class="primary">Save name</button></div></form></dialog>
    <dialog id="fm-trash"><h2>Move to trash?</h2><p id="fm-trash-description"></p><p>You can restore this item from Google Drive.</p><form method="POST">@csrf @method('DELETE')<input type="hidden" name="folder" value="{{ $folderId }}"><div class="fm-actions"><button type="button" data-close>Cancel</button><button class="danger">Move to trash</button></div></form></dialog>
    <dialog id="fm-move"><h2>Move <span id="fm-move-name"></span></h2><p>Choose a destination folder.</p><nav id="fm-picker-crumbs" class="fm-crumbs" aria-label="Destination folder path"></nav><p id="fm-picker-status" role="status"></p><div id="fm-picker" class="fm-picker"></div><button type="button" id="fm-picker-next" hidden>More folders</button><form method="POST">@csrf @method('PATCH')<input type="hidden" name="folder" value="{{ $folderId }}"><input type="hidden" name="destination" id="fm-destination"><div class="fm-actions"><button type="button" data-close>Cancel</button><button class="primary" id="fm-move-submit" disabled>Move here</button></div></form></dialog>
</div>
<script>
(() => {
    const root = document.querySelector('.fm');
    const modal = id => document.getElementById(id);
    root.querySelectorAll('[data-dialog]').forEach(button => button.addEventListener('click', () => modal(button.dataset.dialog).showModal()));
    root.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => button.closest('dialog').close()));
    root.querySelectorAll('[data-rename]').forEach(button => button.addEventListener('click', () => {
        modal('fm-rename').querySelector('form').action = button.dataset.rename;
        modal('fm-rename-name').value = button.dataset.name;
        modal('fm-rename').showModal();
    }));
    root.querySelectorAll('[data-trash]').forEach(button => button.addEventListener('click', () => {
        modal('fm-trash').querySelector('form').action = button.dataset.trash;
        modal('fm-trash-description').textContent = button.dataset.folder === '1' ? `“${button.dataset.name}” and all its contents will be moved to trash.` : `“${button.dataset.name}” will be moved to trash.`;
        modal('fm-trash').showModal();
    }));
    let movingId, pickerFolder, nextPage, pickerRequest = 0;
    const pickerButton = (name, id) => {
        const button = document.createElement('button'); button.type = 'button'; button.textContent = name;
        button.addEventListener('click', () => loadPicker(id)); return button;
    };
    async function loadPicker(folder, page = null) {
        const version = ++pickerRequest;
        modal('fm-move-submit').disabled = true;
        modal('fm-picker-next').hidden = true;
        modal('fm-picker-status').textContent = 'Loading folders…';
        if (!page) modal('fm-picker').replaceChildren();
        const url = new URL(@json(route('file-manager.index'))); url.searchParams.set('picker', '1');
        if (folder) url.searchParams.set('folder', folder); if (page) url.searchParams.set('page', page);
        try {
            const response = await fetch(url, {headers: {'Accept': 'application/json'}});
            const data = await response.json(); if (version !== pickerRequest) return;
            if (!response.ok) throw new Error(data.error || data.message || 'Could not load folders.');
            pickerFolder = data.folder.id; nextPage = data.nextPageToken;
            modal('fm-destination').value = pickerFolder;
            modal('fm-picker-crumbs').replaceChildren(...data.breadcrumbs.map(crumb => pickerButton(crumb.name, crumb.id)));
            data.files.filter(file => file.id !== movingId).forEach(file => modal('fm-picker').append(pickerButton('📁 ' + file.name, file.id)));
            modal('fm-picker-status').textContent = modal('fm-picker').children.length ? '' : 'No subfolders. You can select this folder.';
            modal('fm-move-submit').disabled = !data.folder.capabilities?.canAddChildren || data.breadcrumbs.some(crumb => crumb.id === movingId);
            modal('fm-picker-next').hidden = !nextPage;
        } catch (error) { if (version === pickerRequest) modal('fm-picker-status').textContent = error.message; }
    }
    modal('fm-picker-next').addEventListener('click', () => loadPicker(pickerFolder, nextPage));
    root.querySelectorAll('[data-move]').forEach(button => button.addEventListener('click', () => {
        movingId = button.dataset.id;
        modal('fm-move-name').textContent = button.dataset.name;
        modal('fm-move').querySelector('form').action = button.dataset.move;
        modal('fm-move').showModal(); loadPicker(null);
    }));
    root.querySelectorAll('dialog form').forEach(form => form.addEventListener('submit', event => {
        if (form.closest('dialog').id === 'fm-upload') {
            const file = modal('fm-file').files[0];
            if (file && file.size > @json($maxUploadMb * 1024 * 1024)) {
                event.preventDefault(); modal('fm-upload-status').textContent = 'This file exceeds the {{ $maxUploadMb }} MB upload limit.'; return;
            }
            modal('fm-upload-status').textContent = 'Uploading to Google Drive. Keep this page open…';
        }
        form.querySelectorAll('button').forEach(button => button.disabled = true);
    }));
})();
</script>
@endsection
