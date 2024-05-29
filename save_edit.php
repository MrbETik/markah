<?php
include('config.php'); // fail sambungan ke database

if (isset($_POST['id']) && isset($_POST['markah'])) {
    $id = $_POST['id'];
    $markah = strtoupper($_POST['markah']);

    // Validasi untuk memastikan hanya nilai antara 0 dan 100 atau "TH" dibenarkan
    if ($markah !== 'TH' && (is_numeric($markah) && $markah >= 0 && $markah <= 100)) {
        $query = "UPDATE exam_markah SET markah = '$markah' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else if ($markah === 'TH') {
        $query = "UPDATE exam_markah SET markah = '$markah' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
