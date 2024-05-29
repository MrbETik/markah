<?php
include('config.php'); // fail sambungan ke database

$alertMessage = "";
$alertType = "";

if (isset($_POST['edit_exam'])) {
    $id = $_POST['id'];
    $tahun = $_POST['tahun'];
    $tajuk = $_POST['tajuk'];
    $status = $_POST['status'];

    // Semak jika peperiksaan sudah wujud selain daripada ID yang sedang diedit
    $checkQuery = "SELECT * FROM exam_tajuk WHERE tahun = '$tahun' AND tajuk = '$tajuk' AND id != '$id'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        $alertMessage = "Peperiksaan dengan tahun dan tajuk yang sama sudah wujud";
        $alertType = "error";
    } else {
        $query = "UPDATE exam_tajuk SET tahun = '$tahun', tajuk = '$tajuk', status = '$status' WHERE id = '$id'";
        mysqli_query($conn, $query);

        $alertMessage = "Peperiksaan berjaya dikemaskini";
        $alertType = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peperiksaan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<script>
<?php if ($alertMessage) { ?>
    Swal.fire({
        icon: '<?php echo $alertType; ?>',
        title: '<?php echo ($alertType == "success") ? "Berjaya" : "Gagal"; ?>',
        text: '<?php echo $alertMessage; ?>'
    }).then(function() {
        window.location.href = 'daftar_peperiksaan.php';
    });
<?php } ?>
</script>
</body>
</html>
