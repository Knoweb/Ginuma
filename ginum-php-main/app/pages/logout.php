<?php
$msg = $_GET['msg'];
session_start();
session_unset();
session_destroy();

header("Location: ../login.php?success=$msg");
exit;

?>