<?php
include('config.php');

if (isset($_POST['id_tingkatan'])) {
    $id_tingkatan = $_POST['id_tingkatan'];

    $query = "SELECT id, keterangan FROM kelas_lengkap WHERE id_tingkatan = '$id_tingkatan'";
    $result = mysqli_query($conn, $query);

    echo '<option value="" disabled selected>Sila Pilih Kelas</option>';
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<option value="' . $row['id'] . '">' . $row['keterangan'] . '</option>';
    }
}
?>
