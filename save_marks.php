<?php
include('config.php'); // fail sambungan ke database

$data = json_decode(file_get_contents('php://input'), true);
$success = true;

foreach ($data['marks'] as $mark) {
    $id_tajuk = $data['id_tajuk'];
    $id_tingkatan = $data['id_tingkatan'];
    $id_kelas = $data['id_kelas'];
    $id_subjek = $data['id_subjek'];
    $id_murid = $mark['id_murid'];
    $markah = $mark['markah'];
    $gred = $mark['gred'];

    $query = "INSERT INTO exam_markah (id_tajuk, id_tingkatan, id_kelas, id_subjek, id_pengguna, markah, gred) 
              VALUES ('$id_tajuk', '$id_tingkatan', '$id_kelas', '$id_subjek', '$id_murid', '$markah', '$gred')
              ON DUPLICATE KEY UPDATE markah = '$markah', gred = '$gred'";
    
    if (!mysqli_query($conn, $query)) {
        $success = false;
        break;
    }
}

echo json_encode(['success' => $success]);
?>
