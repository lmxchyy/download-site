<?php
session_start();
require_once '../../../config/database.php';
require_once '../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('../../login.php');
}

redirect('../../admin/');
