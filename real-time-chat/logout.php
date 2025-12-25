<?php
session_start();
session_unset();
session_destroy();
header("Location: /real-time-chat/login-page/index.php");
exit();
?>