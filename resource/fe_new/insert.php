<?php
require_once __DIR__ . '/config.php';

$resource = normalize_resource($_GET['tabel'] ?? $_GET['table'] ?? $_GET['resource'] ?? '');
if ($resource === '') {
    header('Location: index.php');
    exit;
}

$pageTitle = $resource === 'koperasi' ? 'Tambah Koperasi' : 'Tambah Anggota';

$koperasiOptions = [];
if ($resource === 'anggota') {
    $kopRes = api_get_json(api_build_url('koperasi'));
    if ((int)($kopRes['status'] ?? 0) === 1 && isset($kopRes['data']) && is_array($kopRes['data'])) {
        $koperasiOptions = $kopRes['data'];
    }
}

if (isset($_POST['submit'])) {
    if ($resource === 'koperasi') {
        $payload = [
            'nama_koperasi' => trim((string)($_POST['nama_koperasi'] ?? '')),
            'alamat' => trim((string)($_POST['alamat'] ?? '')),
        ];

        $telp = trim((string)($_POST['telp'] ?? ''));
        $tgl = trim((string)($_POST['tgl_berdiri'] ?? ''));

        if ($telp !== '') {
            $payload['telp'] = $telp;
        }
        if ($tgl !== '') {
            $payload['tgl_berdiri'] = $tgl;
        }

        $resp = api_request('POST', api_build_url('koperasi'), $payload);
        $ok = (int)($resp['status'] ?? 0) === 1;

        if ($ok) {
            swal_and_redirect('Berhasil!', 'Data koperasi berhasil ditambahkan.', 'success', 'index.php');
        } else {
            $msg = (string)($resp['message'] ?? 'Gagal menambahkan koperasi.');
            echo "<script>Swal.fire({title:" . json_encode('Gagal!') . ", text:" . json_encode($msg) . ", icon:'error', confirmButtonText:'OK'});</script>";
        }
    } else {
        $payload = [
            'id_koperasi' => trim((string)($_POST['id_koperasi'] ?? '')),
            'nama' => trim((string)($_POST['nama'] ?? '')),
            'alamat' => trim((string)($_POST['alamat'] ?? '')),
        ];

        $noHp = trim((string)($_POST['no_hp'] ?? ''));
        $tgl = trim((string)($_POST['tanggal_gabung'] ?? ''));

        if ($noHp !== '') {
            $payload['no_hp'] = $noHp;
        }
        if ($tgl !== '') {
            $payload['tanggal_gabung'] = $tgl;
        }

        $resp = api_request('POST', api_build_url('anggota'), $payload);
        $ok = (int)($resp['status'] ?? 0) === 1;

        if ($ok) {
            swal_and_redirect('Berhasil!', 'Data anggota berhasil ditambahkan.', 'success', 'index.php');
        } else {
            $msg = (string)($resp['message'] ?? 'Gagal menambahkan anggota.');
            echo "<script>Swal.fire({title:" . json_encode('Gagal!') . ", text:" . json_encode($msg) . ", icon:'error', confirmButtonText:'OK'});</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($pageTitle); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center"><?php echo h($pageTitle); ?></h2>

        <form method="POST" action="">
            <?php if ($resource === 'koperasi') { ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama_koperasi">Nama Koperasi</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama_koperasi" type="text" name="nama_koperasi" required placeholder="Nama Koperasi" value="<?php echo h($_POST['nama_koperasi'] ?? ''); ?>">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="alamat" name="alamat" required placeholder="Alamat Koperasi"><?php echo h($_POST['alamat'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="telp">Telp (opsional)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="telp" type="text" name="telp" placeholder="08xxxxxxxxxx" value="<?php echo h($_POST['telp'] ?? ''); ?>">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tgl_berdiri">Tanggal Berdiri (opsional)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="tgl_berdiri" type="date" name="tgl_berdiri" value="<?php echo h($_POST['tgl_berdiri'] ?? ''); ?>">
                </div>
            <?php } else { ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id_koperasi">Koperasi</label>

                    <?php if (!empty($koperasiOptions)) { ?>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="id_koperasi" name="id_koperasi" required>
                            <option value="">-- Pilih Koperasi --</option>
                            <?php foreach ($koperasiOptions as $k) { ?>
                                <?php
                                    $idKop = (string)($k['id_koperasi'] ?? '');
                                    $label = trim((string)($k['nama_koperasi'] ?? ''));
                                    $selected = ((string)($_POST['id_koperasi'] ?? '') === $idKop) ? 'selected' : '';
                                ?>
                                <option value="<?php echo h($idKop); ?>" <?php echo $selected; ?>><?php echo h($idKop . ' - ' . $label); ?></option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="id_koperasi" type="number" min="1" name="id_koperasi" required placeholder="ID Koperasi" value="<?php echo h($_POST['id_koperasi'] ?? ''); ?>">
                        <p class="text-xs text-gray-500 mt-1">* Jika dropdown kosong, cek API koperasi di index.</p>
                    <?php } ?>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama Anggota</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama" type="text" name="nama" required placeholder="Nama" value="<?php echo h($_POST['nama'] ?? ''); ?>">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="alamat" name="alamat" required placeholder="Alamat"><?php echo h($_POST['alamat'] ?? ''); ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="no_hp">No HP (opsional)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="no_hp" type="text" name="no_hp" placeholder="08xxxxxxxxxx" value="<?php echo h($_POST['no_hp'] ?? ''); ?>">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="tanggal_gabung">Tanggal Gabung (opsional)</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="tanggal_gabung" type="date" name="tanggal_gabung" value="<?php echo h($_POST['tanggal_gabung'] ?? ''); ?>">
                </div>
            <?php } ?>

            <div class="flex items-center justify-between">
                <a href="index.php" class="text-gray-500 hover:text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Batal</a>
                <input type="submit" name="submit" value="Simpan Data" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-pointer">
            </div>
        </form>
    </div>
</body>
</html>
