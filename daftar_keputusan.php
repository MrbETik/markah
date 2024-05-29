<?php
include('config.php'); // fail sambungan ke database

// Dapatkan data peperiksaan, tingkatan, kelas, dan subjek
$exams = mysqli_query($conn, "SELECT * FROM exam_tajuk WHERE status = 1");
$tingkatans = mysqli_query($conn, "SELECT * FROM tingkatan WHERE id_status = 1");
$classes = mysqli_query($conn, "SELECT * FROM kelas_lengkap WHERE id_tingkatan = 1");
$subjects = mysqli_query($conn, "SELECT * FROM tb_subjek WHERE id_tingkatan = 1");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Keputusan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .thead-primary {
            background-color: #0e9e8b; /* Bootstrap primary color */
            color: white;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Daftar Keputusan</h2>
    <form id="selectForm" class="mb-4">
        <div class="form-group">
            <label for="id_tajuk">Tajuk Peperiksaan</label>
            <select class="form-control" id="id_tajuk" name="id_tajuk" required>
                <option value="" disabled selected>Sila Pilih Penilaian</option>
                <?php while ($exam = mysqli_fetch_assoc($exams)) { ?>
                    <option value="<?php echo $exam['id']; ?>"><?php echo $exam['tajuk']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_tingkatan">Tingkatan</label>
            <select class="form-control" id="id_tingkatan" name="id_tingkatan" required>
                <option value="" disabled selected>Sila Pilih Tingkatan</option>
                <?php while ($tingkatan = mysqli_fetch_assoc($tingkatans)) { ?>
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
        <div class="form-group">
            <label for="id_subjek">Subjek</label>
            <select class="form-control" id="id_subjek" name="id_subjek" required>
                <option value="" disabled selected>Sila Pilih Subjek</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" id="getStudents">Papar Murid</button>
    </form>

    <form id="studentsForm" method="post" action="save_marks.php">
        <div id="studentsTable"></div>
        <button type="submit" class="btn btn-primary" id="saveMarks">Simpan Markah</button>
    </form>
</div>

<!-- Footer -->
<footer class="text-center mt-5 py-2">
    <p>&copy; 2024 Sistem Peperiksaan. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
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

    $.ajax({
        url: 'get_subjects.php',
        type: 'POST',
        data: {id_tingkatan: id_tingkatan},
        success: function(response) {
            $('#id_subjek').html(response);
        }
    });
});

$('#getStudents').click(function() {
    var id_tajuk = $('#id_tajuk').val();
    var id_tingkatan = $('#id_tingkatan').val();
    var id_kelas = $('#id_kelas').val();
    var id_subjek = $('#id_subjek').val();

    if (id_tajuk && id_tingkatan && id_kelas && id_subjek) {
        $.ajax({
            url: 'get_students.php',
            type: 'POST',
            data: {
                id_tajuk: id_tajuk,
                id_tingkatan: id_tingkatan,
                id_kelas: id_kelas,
                id_subjek: id_subjek
            },
            success: function(response) {
                $('#studentsTable').html(response);
                document.querySelectorAll('input[name="markah[]"]').forEach(input => {
                    input.addEventListener('input', function() {
                        const value = this.value.toUpperCase();
                        if (value !== 'TH' && (isNaN(value) || value < 0 || value > 100)) {
                            this.setCustomValidity('Sila masukkan markah antara 0 dan 100 atau TH untuk tidak hadir');
                        } else {
                            this.setCustomValidity('');
                        }
                    });
                });
            }
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Ralat',
            text: 'Sila pilih semua pilihan yang diperlukan sebelum memaparkan murid.'
        });
    }
});
</script>

</body>
</html>
