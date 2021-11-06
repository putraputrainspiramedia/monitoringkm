<?php
$conn = mysqli_connect("localhost","root","root","kmtelkom") or die("Gagal koneksi");
mysqli_select_db($conn,"kmtelkom");

global $conn;

?>