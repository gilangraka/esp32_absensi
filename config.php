<?php
    class Database {
        var $host = "localhost";
        var $username = "root";
        var $password = "";
        var $database = "esp32_absen";
        var $koneksi = "";

        function __construct()
        {
            $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->database);
            if(mysqli_connect_errno()){
				echo "Koneksi database gagal : " .mysqli_connect_errno();
			} 
        }

        function resetAbsen()
        {
            $query = "UPDATE data_mahasiswa SET status_absen = '0'";
            mysqli_query($this->koneksi, $query);
        }

        function CountAbsen($id_mahasiswa)
        {
            $query = "SELECT * FROM data_absen WHERE id_mahasiswa = '$id_mahasiswa'";
            $result = mysqli_query($this->koneksi, $query);
            while($row = mysqli_fetch_array($result)) {
                $count_absen = $row['count_absen'];
                $count_absen_terbaru = $count_absen + 1;
            }
            return $count_absen_terbaru;
        }

        function SetAbsen($count_absen_terbaru, $id_mahasiswa)
        {
            $query = "UPDATE data_absen SET count_absen = '$count_absen_terbaru' WHERE id_mahasiswa = '$id_mahasiswa'";
            mysqli_query($this->koneksi, $query);
        }

        function SetTrue($id_mahasiswa)
        {
            $query = "UPDATE data_mahasiswa SET status_absen = 1 WHERE id_mahasiswa = '$id_mahasiswa'";
            mysqli_query($this->koneksi, $query);
        }

        function CekID($id_mahasiswa) 
        {
            $query = "SELECT * FROM data_mahasiswa WHERE id_mahasiswa = '$id_mahasiswa'";
            $result = mysqli_query($this->koneksi, $query);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_array($result)) {
                    $id_terdaftar = true;
                    $nama_mahasiswa = $row['nama_mahasiswa'];
                    $status_absen = $row['status_absen'];

                    if($status_absen == true) {
                        //LCD akan tampil : $nama_mahasiswa sudah absen hari ini!
                        $data [] = array(
                            'id_terdaftar' => $id_terdaftar,
                            'nama_mahasiswa' => $nama_mahasiswa,
                            'status_absen' => true,
                        );
                    } else {
                        //LCD akan tampil : $nama_mahasiswa berhasil absen!
                        // Set Status Absensi menjadi True
                        $this->SetTrue($id_mahasiswa);
                        // Set Count Absen
                        $this->SetAbsen($this->CountAbsen($id_mahasiswa), $id_mahasiswa);
                        $data [] = array(
                            'id_terdaftar' => $id_terdaftar,
                            'nama_mahasiswa' => $nama_mahasiswa,
                            'status_absen' => false,
                        );
                    }
                }
                $jsonfile = json_encode($data, JSON_PRETTY_PRINT);
                file_put_contents('validasi.json', $jsonfile);
            } 
            else {
                //LCD akan tampil : ID tidak terdaftar
                $id_terdaftar = false;
                $data [] = array(
                    'id_terdaftar' => $id_terdaftar,
                );
                $jsonfile = json_encode($data, JSON_PRETTY_PRINT);
                file_put_contents('validasi.json', $jsonfile);
            }
        }
    }
?>