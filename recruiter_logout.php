<?php
require_once 'config.php';
unset($_SESSION['recruiter']);
header('Location: recruiter_index.php');
exit;
