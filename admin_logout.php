<?php
require_once 'config.php';
unset($_SESSION['admin']);
header('Location: admin_index.php');
exit;
