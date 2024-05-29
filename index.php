<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peperiksaan</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 100px;
        }
        .btn {
            margin: 10px;
            padding: 20px;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Selamat Datang ke Sistem Peperiksaan</h1>
        <p>Sila pilih tindakan yang ingin anda lakukan:</p>
        <div class="row">
            <div class="col-md-4">
                <a href="daftar_peperiksaan.php" class="btn btn-primary btn-block">Daftar Peperiksaan</a>
            </div>
            <div class="col-md-4">
                <a href="daftar_keputusan.php" class="btn btn-success btn-block">Daftar Keputusan</a>
            </div>
            <div class="col-md-4">
                <a href="edit_keputusan.php" class="btn btn-info btn-block">Kemaskini Keputusan</a>
            </div>
            <div class="col-md-4">
                <a href="keputusan_kelas.php" class="btn btn-warning btn-block">Keputusan Kelas</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5 py-2">
        <p>&copy; 2024 Sistem Peperiksaan. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
