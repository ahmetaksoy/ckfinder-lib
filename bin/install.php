#!/usr/bin/env php
<?php

require __DIR__ . '/../../../autoload.php';
require __DIR__ . '/../src/Installer.php';

use AhmetAksoy\CKFinder\Installer;

$installer = new Installer();
$installer->install();