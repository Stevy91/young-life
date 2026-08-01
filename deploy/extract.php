<?php
// Upload this file MANUALLY once, as "extract.php" at the true FTP/document
// root (jennviyounglife.org's "/" in FileZilla) — NOT inside laravel_app/.
// It is not part of the app deploy; CI never touches it after this.
//
// Why it exists: this host has no SSH, and plain FTP is far too slow for
// vendor/'s thousands of small files/folders (one network round-trip per
// folder). Instead, CI zips the whole build, FTP-uploads just that one
// file (fast), and hits this script to unzip it server-side — a single
// local disk operation instead of thousands of network calls.
//
// Guarded by the same secret as routes/web.php's /deploy/{token} route.
// The token itself lives in a SEPARATE sibling file — deploy-token.txt,
// containing nothing but the token, uploaded once by hand next to this
// script — never in this PHP file, since this repo is public on GitHub.
// Delete deploy-token.txt on the server (or empty it) to disable both
// this script and the /deploy/{token} route.

$tokenFile = __DIR__.'/deploy-token.txt';
$expectedToken = is_file($tokenFile) ? trim((string) file_get_contents($tokenFile)) : '';

$token = $_GET['token'] ?? '';

if ($expectedToken === '' || ! hash_equals($expectedToken, $token)) {
    http_response_code(403);
    exit('Forbidden');
}

$zipPath = __DIR__.'/deploy.zip';
$targetDir = __DIR__.'/laravel_app';

if (! file_exists($zipPath)) {
    http_response_code(404);
    exit('deploy.zip not found next to extract.php');
}

$zip = new ZipArchive;

if ($zip->open($zipPath) !== true) {
    http_response_code(500);
    exit('Could not open deploy.zip');
}

// Wipe any previous deploy first — local disk operations, so this is fast
// even though it may be thousands of files. Otherwise files removed from
// the repo would linger on the server forever, and this is also what
// clears out folders left behind by an interrupted/cancelled deploy.
//
// laravel_app/storage/ and laravel_app/public/storage/ are deliberately
// skipped: real uploaded content (CMS images) lives under one of these
// (see PUBLIC_DISK_NO_SYMLINK in config/filesystems.php) and is excluded
// from deploy.zip on purpose specifically so it's never touched by a
// redeploy — wiping either wholesale would delete it right before
// extraction, since a wiped-then-not-reprovided folder is gone for good.
function rrmdir(string $dir): void
{
    // is_link() must be checked before is_dir(): is_dir() follows symlinks
    // and returns true for a symlink pointing at a directory, which
    // previously made this function recurse straight through a storage
    // symlink and delete the real files behind it instead of just
    // unlinking the pointer.
    if (is_link($dir)) {
        unlink($dir);

        return;
    }

    if (! is_dir($dir)) {
        unlink($dir);

        return;
    }

    foreach (scandir($dir) as $item) {
        if ($item !== '.' && $item !== '..') {
            rrmdir($dir.'/'.$item);
        }
    }

    rmdir($dir);
}

// Empties $dir's contents except any child named in $except, without
// removing $dir itself.
function wipeContentsExcept(string $dir, array $except): void
{
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);

        return;
    }

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $except, true)) {
            continue;
        }

        rrmdir($dir.'/'.$item);
    }
}

// public/ must be preserved as a directory here too (not just its storage/
// child) — otherwise this top-level wipe would delete the whole folder,
// storage/ included, before the second call below ever gets a chance to
// selectively protect just that one subfolder. .env is manually uploaded
// here (excluded from deploy.zip on purpose, real secrets) — without this
// exception every extraction deletes it and the app can't boot at all.
wipeContentsExcept($targetDir, ['storage', 'public', '.env']);
wipeContentsExcept($targetDir.'/public', ['storage']);

$zip->extractTo($targetDir);
$zip->close();
unlink($zipPath);

header('Content-Type: text/plain');
echo "OK: laravel_app/ wiped and deploy.zip extracted into it.\n";
