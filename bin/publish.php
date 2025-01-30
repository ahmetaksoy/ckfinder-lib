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
    die("❌ HATA: --public parametresini belirtmelisiniz.\nÖrnek: composer run-script ckfinder:publish -- --public=/var/www/public\n");
}

// CKFinder kaynağı (vendor/ahmetaksoy/ckfinder/CKFinder)
$ckfinderExtractedPath = dirname(__DIR__) . '/CKFinder/ckfinder';

$projectRoot = dirname(__DIR__, 4);
$publicTarget = trim($publicTarget, '/');

// Vendor dışındaki hedef dizin (ana proje dizini içinde /ckfinder)
$finalPublicTarget = $publicTarget ? "$projectRoot/$publicTarget/ckfinder" : "$projectRoot/ckfinder";

// Eğer hedef dizin yoksa oluştur
if (!file_exists($finalPublicTarget) && !mkdir($finalPublicTarget, 0755, true) && !is_dir($finalPublicTarget)) {
    die("❌ HATA: Dizin oluşturulamadı: $finalPublicTarget\n");
}

// Kopyalanacak dosyalar
$filesToCopy = [
    'skins',
    'lang',
    'libs',
    'config.js',
    'ckfinder.js',
    'ckfinder.html'
];

// En son kopyalanan dosya
$lastCopiedFile = '';

function copyFiles(string $source, string $target, array $allowedFiles, &$lastCopiedFile): void
{
    foreach ($allowedFiles as $file) {
        $srcPath = $source . '/' . $file;
        $targetPath = $target . '/' . $file;

        if (!file_exists($srcPath)) {
            echo "⚠️ Uyarı: $srcPath bulunamadı, atlanıyor.\n";
            continue;
        }

        if (is_dir($srcPath)) {
            if (!file_exists($targetPath) && !mkdir($targetPath, 0755, true) && !is_dir($targetPath)) {
                echo "❌ HATA: $targetPath dizini oluşturulamadı.\n";
                continue;
            }

            foreach (scandir($srcPath) as $subFile) {
                if ($subFile !== '.' && $subFile !== '..') {
                    copyFiles($srcPath . '/' . $subFile, $targetPath . '/' . $subFile, [], $lastCopiedFile);
                }
            }
        } else {
            if (copy($srcPath, $targetPath)) {
                $lastCopiedFile = realpath($targetPath);
            } else {
                echo "❌ HATA: $srcPath -> $targetPath kopyalanamadı.\n";
            }
        }
    }
}

copyFiles($ckfinderExtractedPath, $finalPublicTarget, $filesToCopy, $lastCopiedFile);

if ($lastCopiedFile) {
    echo "✅ CKFinder gerekli dosyaları kopyalandı: $finalPublicTarget\n";
    echo "📌 En son kopyalanan dosya: $lastCopiedFile\n";
} else {
    echo "⚠️ Uyarı: Hiçbir dosya kopyalanmadı.\n";
}