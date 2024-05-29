<?php
// Sambungan ke pangkalan data
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses untuk mengemaskini konfigurasi gred
    foreach ($_POST['grade_config'] as $id => $config) {
        $min_mark = $config['min_mark'];
        $max_mark = $config['max_mark'];
        $grade = $config['grade'];
        $query = "UPDATE grade_config SET min_mark = '$min_mark', max_mark = '$max_mark', grade = '$grade' WHERE id = '$id'";
        mysqli_query($conn, $query);
    }
}

// Dapatkan konfigurasi gred dari pangkalan data
$query = "SELECT * FROM grade_config";
$result = mysqli_query($conn, $query);

$grade_config = [];
while ($row = mysqli_fetch_assoc($result)) {
    $grade_config[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konfigurasi Gred</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Konfigurasi Gred</h2>
    <form method="post" action="admin_edit_grade_config.php">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Markah Min</th>
                    <th>Markah Max</th>
                    <th>Gred</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grade_config as $config) { ?>
                <tr>
                    <td><input type="number" name="grade_config[<?php echo $config['id']; ?>][min_mark]" value="<?php echo $config['min_mark']; ?>" class="form-control"></td>
                    <td><input type="number" name="grade_config[<?php echo $config['id']; ?>][max_mark]" value="<?php echo $config['max_mark']; ?>" class="form-control"></td>
                    <td><input type="text" name="grade_config[<?php echo $config['id']; ?>][grade]" value="<?php echo $config['grade']; ?>" class="form-control"></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
