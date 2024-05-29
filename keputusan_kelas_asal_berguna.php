<?php
require 'config.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch data as previously described...

$students = [];
$subjects = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_tajuk = $_POST['id_tajuk'];
    $id_tingkatan = $_POST['id_tingkatan'];
    $id_kelas = $_POST['id_kelas'];

    // Dapatkan keterangan kelas
    $kelas_query = "SELECT keterangan FROM kelas_lengkap WHERE id = '$id_kelas'";
    $kelas_result = mysqli_query($conn, $kelas_query);
    $kelas = mysqli_fetch_assoc($kelas_result)['keterangan'];

    // Dapatkan senarai subjek untuk tajuk peperiksaan dan tingkatan yang dipilih
    $subjects_query = "SELECT s.subjek
                       FROM tb_subjek s
                       JOIN exam_markah em ON s.id = em.id_subjek
                       WHERE em.id_tajuk = '$id_tajuk' AND em.id_tingkatan = '$id_tingkatan'
                       GROUP BY s.subjek";
    $subjects_result = mysqli_query($conn, $subjects_query);
    while ($row = mysqli_fetch_assoc($subjects_result)) {
        $subjects[] = $row['subjek'];
    }

    // Dapatkan senarai pelajar dan markah mereka
    $query = "SELECT p.id, p.fullname, em.id_subjek, em.markah, s.subjek
              FROM pengguna p
              JOIN maklumat_murid mm ON p.id = mm.id_pengguna
              LEFT JOIN exam_markah em ON p.id = em.id_pengguna AND em.id_tajuk = '$id_tajuk'
              LEFT JOIN tb_subjek s ON em.id_subjek = s.id
              WHERE mm.id_kelasLengkap = '$id_kelas' 
              ORDER BY p.fullname ASC ";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        if (!isset($students[$row['id']])) {
            $students[$row['id']] = [
                'fullname' => $row['fullname'],
                'marks' => array_fill_keys($subjects, 0) // Isikan markah dengan 0 untuk setiap subjek
            ];
        }
        if ($row['subjek']) { 
            $students[$row['id']]['marks'][$row['subjek']] = $row['markah'];
        }
    }

    // Check if the download button was clicked
    if (isset($_POST['download'])) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header row
        $sheet->setCellValue('A1', 'No.');
        $sheet->setCellValue('B1', 'Nama Murid');
        $column = 'C';
        foreach ($subjects as $subjek) {
            $sheet->setCellValue($column . '1', $subjek);
            $column++;
        }
        $sheet->setCellValue($column . '1', 'Jumlah Markah');
        $column++;
        $sheet->setCellValue($column . '1', '% Markah');

        // Populate data rows
        $rowNumber = 2;
        $no = 1;
        foreach ($students as $id => $student) {
            $sheet->setCellValue('A' . $rowNumber, $no++);
            $sheet->setCellValue('B' . $rowNumber, $student['fullname']);
            $column = 'C';
            foreach ($subjects as $subjek) {
                $sheet->setCellValue($column . $rowNumber, isset($student['marks'][$subjek]) ? $student['marks'][$subjek] : '-');
                $column++;
            }
            $total_marks = array_sum($student['marks']);
            $percentage = count($subjects) > 0 ? ($total_marks / count($subjects)) * 100 : 0;
            $sheet->setCellValue($column . $rowNumber, $total_marks);
            $column++;
            $sheet->setCellValue($column . $rowNumber, number_format($percentage, 1) . '%');
            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'keputusan_kelas.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        $writer->save('php://output');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keputusan Kelas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Keputusan Kelas</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="id_tajuk">Tajuk Peperiksaan</label>
            <select class="form-control" id="id_tajuk" name="id_tajuk" required>
                <option value="" disabled selected>Sila Pilih Penilaian</option>
                <?php
                $exams = mysqli_query($conn, "SELECT * FROM exam_tajuk WHERE status = 1");
                while ($exam = mysqli_fetch_assoc($exams)) { ?>
                    <option value="<?php echo $exam['id']; ?>"><?php echo $exam['tajuk']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_tingkatan">Tingkatan</label>
            <select class="form-control" id="id_tingkatan" name="id_tingkatan" required>
                <option value="" disabled selected>Sila Pilih Tingkatan</option>
                <?php
                $tingkatans = mysqli_query($conn, "SELECT * FROM tingkatan WHERE id_status = 1");
                while ($tingkatan = mysqli_fetch_assoc($tingkatans)) { ?>
                    <option value="<?php echo $tingkatan['id']; ?>"><?php echo $tingkatan['nama_tingkatan']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_kelas">Kelas</label>
            <select class="form-control" id="id_kelas" name="id_kelas" required>
                <option value="" disabled selected>Sila Pilih Kelas</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Paparkan</button>
        <button type="submit" name="download" class="btn btn-success">Download Excel</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['download'])) { ?>
        <h3 class="mt-5">Kelas: <?php echo $kelas; ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Murid</th>
                    <?php foreach ($subjects as $subjek) { ?>
                        <th><?php echo $subjek; ?></th>
                    <?php } ?>
                    <th>Jumlah Markah</th>
                    <th>% Markah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($students as $id => $student) {
                    $total_marks = array_sum($student['marks']);
                    $percentage = count($subjects) > 0 ? ($total_marks / count($subjects)) * 100 : 0;
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $student['fullname']; ?></td>
                        <?php foreach ($subjects as $subjek) { ?>
                            <td><?php echo isset($student['marks'][$subjek]) ? $student['marks'][$subjek] : '-'; ?></td>
                        <?php } ?>
                        <td><?php echo $total_marks; ?></td>
                        <td><?php echo number_format($percentage, 1); ?>%</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-2">
        <p>&copy; 2024 Sistem Peperiksaan. All rights reserved.</p>
    </footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#id_tingkatan').change(function() {
        var id_tingkatan = $(this).val();
        $.ajax({
            url: 'get_classes.php',
            type: 'POST',
            data: {id_tingkatan: id_tingkatan},
            success: function(response) {
                $('#id_kelas').html(response);
            }
        });
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
