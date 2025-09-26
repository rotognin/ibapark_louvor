<?php
require_once(__DIR__ . '/../../app/bootstrap.php');

// Apagar o cookie
setcookie('sistema_rotog_2025', '', 1);

global $authManager;
global $activeUser;
$activeUser = $authManager->enforce('default');

$activeUser->logout();
$response->redirect('/sgc/login.php');
