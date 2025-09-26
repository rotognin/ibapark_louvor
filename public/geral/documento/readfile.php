<?php

require_once(__DIR__ . '/../../../app/bootstrap.php');

global $config;
$caminho = array();

$upload = $config->get('folders.upload');
$arquivo = __DIR__ . '/../../../..' . $request->get('file');

$arquivo = str_replace('&#039;', "'", $arquivo);

$fileParts = pathinfo($arquivo);

$mime = mime_content_type($arquivo);
header("Content-Type: $mime");
readfile($arquivo);
