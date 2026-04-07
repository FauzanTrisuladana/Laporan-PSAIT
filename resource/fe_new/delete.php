<?php
require_once __DIR__ . '/config.php';

$resource = normalize_resource($_GET['tabel'] ?? $_GET['table'] ?? $_GET['resource'] ?? '');
if ($resource === '') {
    header('Location: index.php');
    exit;
}

$idKey = resource_id_key($resource);
$idRaw = $_GET[$idKey] ?? ($_GET['id'] ?? '');

if (!ctype_digit((string) $idRaw) || (int) $idRaw <= 0) {
    header('Location: index.php');
    exit;
}

$id = (int) $idRaw;

$resp = api_request('DELETE', api_build_url($resource, $id));
$ok = (int)($resp['status'] ?? 0) === 1;

$title = $ok ? 'Terhapus!' : 'Gagal!';
$text = $ok
    ? ($resource === 'koperasi' ? 'Data koperasi berhasil dihapus.' : 'Data anggota berhasil dihapus.')
    : (string)($resp['message'] ?? 'Gagal menghapus data.');
$icon = $ok ? 'success' : 'error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hapus Data</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">

<script>
    Swal.fire({
        title: <?php echo json_encode($title); ?>,
        text: <?php echo json_encode($text); ?>,
        icon: <?php echo json_encode($icon); ?>,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.php';
        }
    });
</script>

</body>
</html>
