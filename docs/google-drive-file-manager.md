# File Manager / Google Shared Drive setup

The admin sidebar links to `/admin/file-manager`. This feature stores files directly in one Google Shared Drive through Drive API v3. It does not copy files into public Laravel storage. All app administrators can access the configured drive through the service account; clients and healthcare workers cannot access these routes.

## Connect the drive

1. In Google Cloud, select a project and enable **Google Drive API**.
2. Create a service account and download its JSON key. No domain-wide delegation is needed when the service account can be added directly to the drive.
3. In Google Drive, open the target **Shared drive → Manage members** and add the service account's `client_email` as **Content manager**. Your Workspace administrator may need to permit this membership. Give the service account access only to the intended drive.
4. Store the JSON key **outside the repository and Apache document root**, readable by the PHP/Apache service user. Never place it in `public/`, commit it, or paste its contents into a browser form.
5. Add these values to the app's private `.env` file (forward slashes work on Windows):

   ```dotenv
   GOOGLE_DRIVE_SHARED_DRIVE_ID=your_shared_drive_id
   GOOGLE_DRIVE_CREDENTIALS_PATH="C:/secure/our-care/google-drive-service-account.json"
   GOOGLE_DRIVE_MAX_UPLOAD_MB=20
   ```

   Open the Shared Drive root in Google Drive: the value after `/folders/` in its URL is the drive ID. Use the **drive root**, not a folder inside it or a folder from My Drive.
6. Run `composer install` when deploying, then `php artisan config:clear` (or rebuild your production configuration cache).
7. Sign in as an admin and open **File Manager**. Confirm the drive name and folder listing appear. Create a test folder, upload and download a small file, rename and move it, then trash it and check that it can be restored in Google Drive.

The service account uses the `https://www.googleapis.com/auth/drive` scope to manage existing files as well as new uploads. Credentials and cached access tokens stay on the server. IDs submitted by the browser are checked against the configured drive before reads or writes. Root drive rename/trash and moving folders into themselves are blocked.

## Supported actions and limits

- Browse nested folders with breadcrumbs; search names within the current folder; paginate listings of more than 100 items.
- Create folders, upload one file at a time, rename, move with a folder picker, and move items to trash. Trashing a folder includes its contents. Restore items in Google Drive; there is no permanent-delete action in this app.
- Download uploaded files; export Google Docs, Slides, and Drawings as PDF and Sheets as XLSX. Other native Google file types and shortcuts are not downloaded here. Google export size limits apply.
- Uploads stream via a resumable upload session with one data transfer. Interrupted transfers are not automatically resumed; refresh the listing before retrying. The configured upload maximum is clamped between 1 and 100 MB. Empty files are rejected.
- PHP `upload_max_filesize`, `post_max_size`, web server request limits, and timeouts must permit the desired upload size. For a 20 MB app limit, use at least `upload_max_filesize=20M` and `post_max_size=25M` in the PHP configuration used by Apache, then restart Apache. Check `max_execution_time` for slower connections.
- App administrators share the service account's Drive identity. Google Drive's audit attribution is to that identity, not each individual app administrator.

## Troubleshooting

- **Connection needed:** Check both environment values and whether PHP can read the JSON file.
- **Authentication failed:** Verify it is a valid, active service-account key, the Drive API is enabled, and the server clock is correct.
- **Denied / unavailable:** Check the Shared Drive ID, membership, Workspace restrictions, and Content manager permissions. A Viewer can browse but cannot upload or manage files.
- **Upload too large:** Align PHP/Apache limits with the app limit described above.

## Verification

Run `php artisan test --filter=FileManagerTest`. Tests fake Google HTTP responses and never create or delete real Drive files. Live verification requires the actual Shared Drive ID and service-account key; automated tests alone do not verify Workspace permissions.

References: [Shared Drive API support](https://developers.google.com/workspace/drive/api/guides/enable-shareddrives), [service-account authentication](https://developers.google.com/identity/protocols/oauth2/service-account), [uploads](https://developers.google.com/workspace/drive/api/guides/manage-uploads).
