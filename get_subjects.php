<?php
include('config.php');

if (isset($_POST['id_tingkatan'])) {
    $id_tingkatan = $_POST['id_tingkatan'];
    $subjects = mysqli_query($conn, "SELECT * FROM tb_subjek WHERE id_tingkatan = '$id_tingkatan'");

    while ($subjek = mysqli_fetch_assoc($subjects)) {
        echo '<option value="' . $subjek['id'] . '">' . $subjek['subjek'] . '</option>';
    }
}
?>
