<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'folderUploadPdf' => '../../../../data',
    'mailConfig' => array(
        'host' => '10.132.162.156',
        'port' => 25,
        'basepath' => 'https://www.sr-navi.metro.tokyo.jp',
        'timeout' => 30,
        'smtp' => null,
    ),
    'configIni' => '/../../../data/conf.ini'
);