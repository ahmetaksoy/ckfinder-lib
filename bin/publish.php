<?php

function getArgument(string $name, ?string $default = null): ?string
{
    global $argv;

    foreach ($argv as $arg) {
        if (strpos($arg, "--$name=") === 0) {
            return substr($arg, strlen("--$name="));
        }
    }

    return $default;
}

$publicTarget = getArgument('public');

if (!$publicTarget) {
    die("❌ HATA: --public parametresini belirtmelisiniz.\nÖrnek: composer ckfinder:publish -- --public=/var/www/public\n");
}

$ckfinderExtractedPath = dirname(__DIR__) . '/CKFinder/ckfinder';

$finalPublicTarget = rtrim($publicTarget, '/') . '/ckfinder';

if (!file_exists($finalPublicTarget)) {
    mkdir($finalPublicTarget, 0755, true);
}

$filesToCopy = [
    'skins',
    'lang',
    'libs',
    'config.js',
    'ckfinder.js',
    'ckfinder.html'
];

function copyFiles(string $source, string $target, array $allowedFiles): void
{
    foreach ($allowedFiles as $file) {
        $srcPath = $source . '/' . $file;
        $targetPath = $target . '/' . $file;

        if (!file_exists($srcPath)) {
            echo "⚠️ Uyarı: $srcPath bulunamadı, atlanıyor.\n";
            continue;
        }

        if (is_dir($srcPath)) {
            mkdir($targetPath, 0755, true);
            foreach (scandir($srcPath) as $subFile) {
                if ($subFile !== '.' && $subFile !== '..') {
                    copyFiles($srcPath . '/' . $subFile, $targetPath . '/' . $subFile, []);
                }
            }
        } else {
            copy($srcPath, $targetPath);
        }
    }
}

copyFiles($ckfinderExtractedPath, $finalPublicTarget, $filesToCopy);

echo "✅ CKFinder gerekli dosyaları kopyalandı: $finalPublicTarget\n";