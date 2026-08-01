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
// storage/ is deliberately skipped: storage/app/public/ holds real
// uploaded content (CMS images) that is excluded from deploy.zip on
// purpose (see .github/workflows/deploy.yml) specifically so it's never
// touched by a redeploy. Wiping laravel_app/ wholesale would delete it
// right before extraction, since a wiped-then-not-reprovided folder is
// gone for good.
function rrmdir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $dir.'/'.$item;

        is_dir($path) ? rrmdir($path) : unlink($path);
    }

    rmdir($dir);
}

if (is_dir($targetDir)) {
    foreach (scandir($targetDir) as $item) {
        if ($item === '.' || $item === '..' || $item === 'storage') {
            continue;
        }

        $path = $targetDir.'/'.$item;

        is_dir($path) ? rrmdir($path) : unlink($path);
    }
} else {
    mkdir($targetDir, 0755, true);
}

$zip->extractTo($targetDir);
$zip->close();
unlink($zipPath);

header('Content-Type: text/plain');
echo "OK: laravel_app/ wiped and deploy.zip extracted into it.\n";
