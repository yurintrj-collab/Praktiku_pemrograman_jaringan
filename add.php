<?php

$password = "jesly123";//variabel yang menampung password sebagai text deskripsi

$hash = password_hash($password, PASSWORD_DEFAULT);//proses enkripsi password

echo $hash;//menampilkan hasil enkripsi dihalaman browser

?>