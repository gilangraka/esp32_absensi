<?php 
    require('config.php');
    $koneksi = new Database();

    $id_mahasiswa = $_GET['id_mahasiswa'];
    $koneksi->CekID($id_mahasiswa);
?>