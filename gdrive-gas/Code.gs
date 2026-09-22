/**
 * SIPOKIR — Google Drive Integration via Google Apps Script
 *
 * Deploy as Web App:
 *   1. Open https://script.google.com → New Project → paste this file
 *   2. Set GAS_WEBAPP_URL in Laravel .env to the deployed URL
 *   3. Deploy → New Deployment → Web App → Execute as Me → Anyone can access
 *   4. Copy the deployment URL to .env
 */

const ROOT_FOLDER_NAME = 'SIM-PEP';

// ─────────────────────────────────────────────
// HTTP Entry Points
// ─────────────────────────────────────────────

/**
 * Health check
 */
function doGet(e) {
  return ContentService
    .createTextOutput(JSON.stringify({ status: 'ok', timestamp: new Date().toISOString() }))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Main router — upload, delete, deleteFolder, test
 */
function doPost(e) {
  try {
    const body = JSON.parse(e.postData.contents);
    const action = body.action;

    switch (action) {
      case 'upload':   return handleUpload(body);
      case 'delete':   return handleDelete(body);
      case 'deleteFolder': return handleDeleteFolder(body);
      case 'test':     return handleTest(body);
      default:         return respond(false, 'Unknown action: ' + action);
    }
  } catch (err) {
    return respond(false, 'Error: ' + err.message);
  }
}

// ─────────────────────────────────────────────
// Upload
// ─────────────────────────────────────────────

/**
 * body.payload:
 *   file: { name, mime, content } — base64-encoded file
 *   metadata: { tahunAnggaran, jenisBantuan, usulanId, jenisDokumen }
 */
function handleUpload(body) {
  const { file, metadata } = body;

  if (!file || !file.content || !file.name) {
    return respond(false, 'Missing file data');
  }
  if (!metadata || !metadata.tahunAnggaran || !metadata.usulanId || !metadata.jenisDokumen) {
    return respond(false, 'Missing metadata (tahunAnggaran, usulanId, jenisDokumen)');
  }

  // Decode base64 → blob
  const raw = Utilities.base64Decode(file.content);
  const blob = Utilities.newBlob(raw, file.mime || 'application/octet-stream', file.name);

  // Get or create folder: SIM-PEP/{tahun}/{KUBE|UEP}/{usulan_id}/
  const jenis = (metadata.jenisBantuan || 'KUBE').toUpperCase();
  const folderPath = [ROOT_FOLDER_NAME, String(metadata.tahunAnggaran), jenis, 'USULAN_' + metadata.usulanId];
  const folder = getOrCreateFolder_(folderPath);

  // Upload
  const driveFile = folder.createFile(blob);
  driveFile.setDescription(JSON.stringify({
    usulan_id: metadata.usulanId,
    jenis_dokumen: metadata.jenisDokumen,
    uploaded_at: new Date().toISOString(),
  }));

  // Generate web view link
  let webViewLink = '';
  try {
    driveFile.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
    webViewLink = driveFile.getUrl();
  } catch (sharingErr) {
    // Sharing might fail for organizational accounts — still usable
    webViewLink = driveFile.getUrl();
  }

  return respond(true, 'File uploaded', {
    fileId: driveFile.getId(),
    fileName: driveFile.getName(),
    webViewLink: webViewLink,
    mimeType: driveFile.getMimeType(),
    size: driveFile.getSize(),
  });
}

// ─────────────────────────────────────────────
// Delete
// ─────────────────────────────────────────────

function handleDelete(body) {
  const { fileId } = body;
  if (!fileId) {
    return respond(false, 'Missing fileId');
  }

  try {
    const file = DriveApp.getFileById(fileId);
    file.setTrashed(true);
    return respond(true, 'File trashed', { fileId: fileId });
  } catch (err) {
    return respond(false, 'File not found or already deleted: ' + fileId);
  }
}

// ─────────────────────────────────────────────
// Delete Folder
// ─────────────────────────────────────────────

function handleDeleteFolder(body) {
  const { folderId } = body;
  if (!folderId) {
    return respond(false, 'Missing folderId');
  }

  try {
    const folder = DriveApp.getFolderById(folderId);
    folder.setTrashed(true);
    return respond(true, 'Folder trashed', { folderId: folderId });
  } catch (err) {
    return respond(false, 'Folder not found: ' + folderId);
  }
}

// ─────────────────────────────────────────────
// Test
// ─────────────────────────────────────────────

function handleTest(body) {
  const root = getOrCreateFolder_([ROOT_FOLDER_NAME]);
  const stats = {
    status: 'ok',
    rootFolderId: root.getId(),
    rootFolderName: root.getName(),
    storageUsed: DriveApp.getStorageUsed(),
    storageLimit: DriveApp.getStorageLimit(),
  };
  return respond(true, 'Connection OK', stats);
}

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────

/**
 * Recursively create/get folder by path segments.
 * e.g. getOrCreateFolder_(['SIM-PEP', '2027', 'KUBE', 'USULAN_12'])
 */
function getOrCreateFolder_(segments) {
  let current = DriveApp.getRootFolder();

  for (let i = 0; i < segments.length; i++) {
    const name = segments[i];
    const childIterator = current.getFoldersByName(name);

    if (childIterator.hasNext()) {
      current = childIterator.next();
    } else {
      current = current.createFolder(name);
    }
  }

  return current;
}

/**
 * Standard JSON response wrapper
 */
function respond(success, message, data) {
  const result = {
    success: success,
    message: message || '',
    data: data || null,
    timestamp: new Date().toISOString(),
  };
  return ContentService
    .createTextOutput(JSON.stringify(result))
    .setMimeType(ContentService.MimeType.JSON);
}
