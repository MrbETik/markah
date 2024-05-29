<?php
include('config.php'); // fail sambungan ke database

if (isset($_POST['id_tajuk']) && isset($_POST['id_tingkatan']) && isset($_POST['id_kelas']) && isset($_POST['id_subjek'])) {
    $id_tajuk = $_POST['id_tajuk'];
    $id_tingkatan = $_POST['id_tingkatan'];
    $id_kelas = $_POST['id_kelas'];
    $id_subjek = $_POST['id_subjek'];

    $query = "SELECT em.id, p.fullname, em.markah
              FROM exam_markah em
              INNER JOIN pengguna p ON em.id_pengguna = p.id
              WHERE em.id_tajuk = '$id_tajuk' AND em.id_tingkatan = '$id_tingkatan' AND em.id_kelas = '$id_kelas' AND em.id_subjek = '$id_subjek'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Murid</th>
                    <th>Markah</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><?php echo $row['markah']; ?></td>
                        <td><button class="btn btn-warning editButton" data-id="<?php echo $row['id']; ?>" data-markah="<?php echo $row['markah']; ?>">EDIT</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

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
        $('.editButton').click(function() {
            var id = $(this).data('id');
            var markah = $(this).data('markah');
            $('#editId').val(id);
            $('#editMarkah').val(markah);
            $('#editModal').modal('show');
        });

        $('#saveEdit').click(function() {
            var id = $('#editId').val();
            var markah = $('#editMarkah').val().toUpperCase();
            
            // Validasi untuk memastikan hanya nilai antara 0 dan 100 atau "TH" dibenarkan
            if ((markah !== 'TH' && (isNaN(markah) || markah < 0 || markah > 100)) || (markah === 'TH' && markah.length > 2)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Sila masukkan markah antara 0 dan 100 atau TH untuk tidak hadir!'
                });
                return;
            }

            $.ajax({
                url: 'save_edit.php',
                type: 'POST',
                data: { id: id, markah: markah },
                success: function(response) {
                    $('#editModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berjaya',
                        text: 'Markah berjaya dikemaskini!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Anda Ingin Mengubah Markah Lagi?',
                                showDenyButton: true,
                                confirmButtonText: 'YA',
                                denyButtonText: 'TIDAK',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                } else if (result.isDenied) {
                                    window.location.href = 'index.php';
                                }
                            });
                        }
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terdapat ralat semasa mengemaskini markah!'
                    });
                }
            });
        });
        </script>
        <?php
    } else {
        echo "Tiada murid ditemui dalam kelas ini.";
    }
}
?>
