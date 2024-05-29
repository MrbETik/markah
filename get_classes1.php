<?php
include('config.php');

if (isset($_POST['id_tingkatan'])) {
    $id_tingkatan = $_POST['id_tingkatan'];
    $classes = mysqli_query($conn, "SELECT * FROM kelas_lengkap WHERE id_tingkatan = '$id_tingkatan'");

    while ($kelas = mysqli_fetch_assoc($classes)) {
        echo '<option value="' . $kelas['id'] . '">' . $kelas['keterangan'] . '</option>';
    }
}
?>
