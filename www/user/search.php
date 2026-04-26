<?php
require_once '../../config/database.php';
require_once '../includes/functions.php';

$query = trim((string)($_GET['q'] ?? ''));
$target = '../search.php?q=' . rawurlencode($query);
redirect($target);
