<?php
// Sambungan ke pangkalan data
include('config.php');

// Dapatkan konfigurasi gred dari pangkalan data
$query = "SELECT * FROM grade_config";
$result = mysqli_query($conn, $query);

$grade_config = [];
while ($row = mysqli_fetch_assoc($result)) {
    $grade_config[] = $row;
}

// Dapatkan data pelajar dan markah dari pangkalan data
// (Ini adalah contoh, anda perlu menyesuaikan dengan keperluan sebenar)
$students = [
    ['id' => 1, 'fullname' => 'Ahmad', 'markah' => 20, 'gred' => 'B'],
    ['id' => 2, 'fullname' => 'Abu', 'markah' => 'TH', 'gred' => 'TH']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Keputusan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Keputusan Pelajar</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Murid</th>
                <th>Markah</th>
                <th>Gred</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($students as $student) { ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $student['fullname']; ?></td>
                    <td><input type="text" class="form-control markah-input" name="markah[]" data-id="<?php echo $student['id']; ?>" value="<?php echo $student['markah']; ?>"></td>
                    <td class="grade"><?php echo $student['gred']; ?></td>
                    <td><button class="btn btn-warning editButton" data-id="<?php echo $student['id']; ?>">EDIT</button></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal untuk Edit Markah -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Markah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editMarkah">Markah</label>
                        <input type="text" class="form-control" id="editMarkah" name="markah" required>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveEdit">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const gradeConfig = <?php echo json_encode($grade_config); ?>;

document.querySelectorAll('input[name="markah[]"]').forEach(input => {
    input.addEventListener('input', function() {
        const value = this.value.toUpperCase();
        if (value !== 'TH' && (isNaN(value) || value < 0 || value > 100)) {
            this.setCustomValidity('Sila masukkan markah antara 0 dan 100 atau TH untuk tidak hadir');
        } else {
            this.setCustomValidity('');
        }

        let gred = 'G';
        if (value === 'TH') {
            gred = 'TH';
        } else {
            const markah = parseInt(value);
            if (!isNaN(markah)) {
                for (const config of gradeConfig) {
                    if (markah >= config.min_mark && markah <= config.max_mark) {
                        gred = config.grade;
                        break;
                    }
                }
            }
        }
        this.parentElement.nextElementSibling.innerText = gred;
    });
});

$('.editButton').click(function() {
    var id = $(this).data('id');
    var markah = $('input[data-id="' + id + '"]').val();
    $('#editId').val(id);
    $('#editMarkah').val(markah);
    $('#editModal').modal('show');
});

$('#saveEdit').click(function() {
    var id = $('#editId').val();
    var markah = $('#editMarkah').val().toUpperCase();
    
    if ((markah !== 'TH' && (isNaN(markah) || markah < 0 || markah > 100)) || (markah === 'TH' && markah.length > 2)) {
        alert('Sila masukkan markah antara 0 dan 100 atau TH untuk tidak hadir');
        return;
    }

    $('input[data-id="' + id + '"]').val(markah).trigger('input');
    $('#editModal').modal('hide');
});
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
