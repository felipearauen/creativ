<?php
/**
 * Cierra sesión y vuelve al login.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$_SESSION = [];
session_destroy();
redirect('login.php');
