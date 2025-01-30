<?php

namespace AhmetAksoy\CKFinder;

use GuzzleHttp\Client;
use ZipArchive;

class Installer
{
    private const CKFINDER_DOWNLOAD_URL = 'http://download.cksource.com/CKFinder/CKFinder%20for%20PHP/3.6.1/ckfinder_php_3.6.1.zip';
    private string $targetDir;
    private string $zipFile;

    public function __construct()
    {
        $this->targetDir = dirname(__DIR__) . '/CKFinder';
        $this->zipFile = dirname(__DIR__) . '/ckfinder.zip';
    }

    public function install(): void
    {
        if (file_exists($this->targetDir)) {
            echo "✅ CKFinder zaten yüklü.\n";
            return;
        }

        $this->download();
        $this->extract();
    }

    private function download(): void
    {
        $client = new Client();
        echo "📥 CKFinder indiriliyor...\n";

        $response = $client->get(self::CKFINDER_DOWNLOAD_URL, ['sink' => $this->zipFile]);

        if ($response->getStatusCode() === 200) {
            echo "✅ CKFinder indirildi.\n";
        } else {
            die("❌ CKFinder indirme başarısız.");
        }
    }

    private function extract(): void
    {
        $zip = new ZipArchive;

        if ($zip->open($this->zipFile) === TRUE) {
            $zip->extractTo($this->targetDir);
            $zip->close();
            echo "✅ CKFinder başarıyla açıldı: " . $this->targetDir . "\n";
        } else {
            die("❌ CKFinder ZIP açma işlemi başarısız.");
        }

        unlink($this->zipFile);
    }
}