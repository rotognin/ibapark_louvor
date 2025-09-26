<?php

use App\MOVIMENTACOES\DAO\Anexos;
use App\MOVIMENTACOES\DAO\Estudos;

$anexosDAO = new Anexos();
$estudosDAO = new Estudos();

global $session;
$usuario = $session->get('credentials.default');

$arquivo = $request->file('arquivo');
$bib_id = $request->post('bib_id');

$parts = explode('.', $arquivo['name']);
$ext = array_pop($parts);
if (!in_array(strtolower($ext), ['pdf', 'jpg', 'jpeg', 'bmp', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'])) {
    echo 'erro';
    exit();
}

$dir = __DIR__ . '/../../../' . $config->get('folders.upload') . '/anexos_estudos/' . $bib_id;
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
}

// Renomear o arquivo para jogar na pasta
$dataHora = date('Ymd-His');
$nomeCompleto = $dataHora . '-' . $arquivo['name'];
$arquivoCompleto = $dir . '/' . $nomeCompleto;

move_uploaded_file($arquivo['tmp_name'], $arquivoCompleto);

$record = [
    'anx_entidade' => $estudosDAO->tipo_anexo,
    'anx_entidade_id' => $bib_id,
    'anx_tipo' => $anexosDAO->encontrarTipo($ext),
    'anx_nome_arquivo' => $nomeCompleto,
    'anx_fisico_arquivo' => $arquivo['name'],
    'anx_caminho' => $dir,
    'anx_descricao' => '',
    'anx_data_hora' => date('Y-m-d H:i:s'),
    'anx_usuario' => $usuario
];

$anx_id = $anexosDAO->insert($record);

$record['anx_id'] = $anx_id;

echo json_encode($record);
