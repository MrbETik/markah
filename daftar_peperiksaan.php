<?php
include('config.php'); // fail sambungan ke database

$alertMessage = "";
$alertType = "";

// Tambah peperiksaan baru
if (isset($_POST['add_exam'])) {
    $tahun = $_POST['tahun'];
    $tajuk = $_POST['tajuk'];
    $status = $_POST['status'];

    // Semak jika peperiksaan sudah wujud
    $checkQuery = "SELECT * FROM exam_tajuk WHERE tahun = '$tahun' AND tajuk = '$tajuk'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        $alertMessage = "Peperiksaan dengan tahun dan tajuk yang sama sudah wujud";
        $alertType = "error";
    } else {
        $query = "INSERT INTO exam_tajuk (tahun, tajuk, status) VALUES ('$tahun', '$tajuk', '$status')";
        mysqli_query($conn, $query);

        $alertMessage = "Peperiksaan berjaya didaftarkan";
        $alertType = "success";
    }
}

// Kemas kini status peperiksaan
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = ($_GET['action'] == 'activate') ? 1 : 0;

    $query = "UPDATE exam_tajuk SET status = '$status' WHERE id = '$id'";
    mysqli_query($conn, $query);

    header('Location: daftar_peperiksaan.php');
}

// Dapatkan data peperiksaan
$exams = mysqli_query($conn, "SELECT * FROM exam_tajuk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peperiksaan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Daftar Peperiksaan</h2>
    <form action="daftar_peperiksaan.php" method="POST" class="mb-4">
        <div class="form-group">
            <label for="tahun">Tahun</label>
            <input type="text" class="form-control" id="tahun" name="tahun" maxlength="4" required>
        </div>
        <div class="form-group">
            <label for="tajuk">Tajuk Peperiksaan</label>
            <input type="text" class="form-control" id="tajuk" name="tajuk" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
        </div>
        <button type="submit" name="add_exam" class="btn btn-primary">Daftar Peperiksaan</button>
    </form>

    <hr>

    <h3>Senarai Peperiksaan</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tahun</th>
                <th>Tajuk Peperiksaan</th>
                <th>Status</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while ($row = mysqli_fetch_assoc($exams)) { ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row['tahun']; ?></td>
                    <td><?php echo $row['tajuk']; ?></td>
                    <td><?php echo ($row['status'] == 1) ? 'Aktif' : 'Tidak Aktif'; ?></td>
                    <td>
                        <a href="daftar_peperiksaan.php?action=<?php echo ($row['status'] == 1) ? 'deactivate' : 'activate'; ?>&id=<?php echo $row['id']; ?>" class="btn <?php echo ($row['status'] == 1) ? 'btn-success' : 'btn-danger'; ?>">
                            <?php echo ($row['status'] == 1) ? 'Sedang Aktif' : 'Tidak Aktif'; ?>
                        </a>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Peperiksaan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="edit_exam.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <div class="form-group">
                                        <label for="tahun">Tahun</label>
                                        <input type="text" class="form-control" id="tahun" name="tahun" value="<?php echo $row['tahun']; ?>" maxlength="4" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="tajuk">Tajuk Peperiksaan</label>
                                        <input type="text" class="form-control" id="tajuk" name="tajuk" value="<?php echo $row['tajuk']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>Aktif</option>
                                            <option value="0" <?php echo ($row['status'] == 0) ? 'selected' : ''; ?>>Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="edit_exam" class="btn btn-primary">Kemas Kini</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Edit Modal -->
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Footer -->
<footer class="text-center mt-5 py-2">
    <p>&copy; 2024 Sistem Peperiksaan. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
<?php if ($alertMessage) { ?>
    Swal.fire({
        icon: '<?php echo $alertType; ?>',
        title: '<?php echo ($alertType == "success") ? "Berjaya" : "Gagal"; ?>',
        text: '<?php echo $alertMessage; ?>'
    });
<?php } ?>
</script>

</body>
</html>
