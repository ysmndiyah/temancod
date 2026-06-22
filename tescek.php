<?php
$password_baru = 'Erika123.';
$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
echo $hash_baru;
?>