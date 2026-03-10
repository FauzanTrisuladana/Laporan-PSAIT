<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hapus Data</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<?php
if(isset($_GET['id_mhs'])){
    $id = $_GET['id_mhs'];
    $api_url = 'http://10.33.35.96/sait_api/mahasiswa_api.php?id_mhs=' . $id;

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);

    echo "<script>
        Swal.fire({
            title: 'Terhapus!',
            text: 'Data mahasiswa berhasil dihapus.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php';
            }
        });
    </script>";
} else {
    header("Location: index.php");
}
?>

</body>
</html>