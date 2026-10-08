<?php
$c = new mysqli('db', getenv('DB_USER'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
echo $c->connect_error ?: 'Koneksi DB berhasil';
