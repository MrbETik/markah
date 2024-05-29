<?php
include('config.php'); // fail sambungan ke database

if (isset($_POST['id_tajuk']) && isset($_POST['id_tingkatan']) && isset($_POST['id_kelas']) && isset($_POST['id_subjek'])) {
    $id_tajuk = $_POST['id_tajuk'];
    $id_tingkatan = $_POST['id_tingkatan'];
    $id_kelas = $_POST['id_kelas'];
    $id_subjek = $_POST['id_subjek'];

    $query = "SELECT a.fullname, b.id AS id_murid, b.id_pengguna
              FROM pengguna a
              INNER JOIN maklumat_murid b ON a.id = b.id_pengguna
              INNER JOIN kelas_lengkap c ON c.id = b.id_kelasLengkap
              WHERE b.id_kelasLengkap = '$id_kelas' AND a.role = 3 AND a.status = 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $subjek_query = "SELECT subjek FROM tb_subjek WHERE id = '$id_subjek'";
        $subjek_result = mysqli_fetch_assoc(mysqli_query($conn, $subjek_query));
        $subjek = $subjek_result['subjek'];

        $kelas_query = "SELECT keterangan FROM kelas_lengkap WHERE id = '$id_kelas'";
        $kelas_result = mysqli_fetch_assoc(mysqli_query($conn, $kelas_query));
        $kelas = $kelas_result['keterangan'];
        ?>
        <h3>SUBJEK : <?php echo $subjek; ?> | KELAS : <?php echo $kelas; ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Murid</th>
                    <th>Markah</th>
                    <th>Gred</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['fullname']; ?></td>
                        <td><input type="text" class="form-control" name="markah[]" data-id-murid="<?php echo $row['id_murid']; ?>" required></td>
                        <td class="grade"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button class="btn btn-primary" id="saveMarks">Simpan Markah</button>

        <script>
            document.querySelectorAll('input[name="markah[]"]').forEach(input => {
                input.addEventListener('input', function() {
                    const value = this.value.toUpperCase();
                    if (value !== 'TH' && (isNaN(value) || value < 0 || value > 100)) {
                        this.setCustomValidity('Sila masukkan markah antara 0 dan 100 atau TH untuk tidak hadir');
                    } else {
                        this.setCustomValidity('');
                    }

                    let gred = '';
                    const markah = parseInt(value);
                    if (value === 'TH') {
                        gred = 'TH';
                    } else if (!isNaN(markah)) {
                        if (markah >= 90) {
                            gred = 'A+';
                        } else if (markah >= 80) {
                            gred = 'A';
                        } else if (markah >= 60) {
                            gred = 'B';
                        } else if (markah >= 40) {
                            gred = 'C';
                        } else if (markah >= 30) {
                            gred = 'D';
                        } else if (markah >= 20) {
                            gred = 'F';
                        } else {
                            gred = 'G';
                        }
                    }
                    this.parentElement.nextElementSibling.innerText = gred;
                });
            });

            document.getElementById('saveMarks').addEventListener('click', function() {
                const marks = [];
                document.querySelectorAll('input[name="markah[]"]').forEach(input => {
                    marks.push({
                        id_murid: input.getAttribute('data-id-murid'),
                        markah: input.value,
                        gred: input.parentElement.nextElementSibling.innerText
                    });
                });

                fetch('save_marks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        marks,
                        id_tajuk: <?php echo $id_tajuk; ?>,
                        id_tingkatan: <?php echo $id_tingkatan; ?>,
                        id_kelas: <?php echo $id_kelas; ?>,
                        id_subjek: <?php echo $id_subjek; ?>
                    })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire({
                              icon: 'success',
                              title: 'Berjaya',
                              text: 'Markah berjaya disimpan!'
                          }).then((result) => {
                              if (result.isConfirmed) {
                                  Swal.fire({
                                      title: 'Anda Ingin Memasukkan Markah Lagi?',
                                      showDenyButton: true,
                                      confirmButtonText: 'YA',
                                      denyButtonText: 'TIDAK',
                                  }).then((result) => {
                                      if (result.isConfirmed) {
                                          window.location.href = 'daftar_keputusan.php';
                                      } else if (result.isDenied) {
                                          window.location.href = 'index.php';
                                      }
                                  });
                              }
                          });
                      } else {
                          Swal.fire({
                              icon: 'error',
                              title: 'Gagal',
                              text: 'Terdapat ralat semasa menyimpan markah!'
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
