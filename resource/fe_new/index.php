<?php
require_once __DIR__ . '/config.php';

$koperasiResponse = api_get_json(api_build_url('koperasi'));
$koperasiList = [];
$koperasiError = '';

if ((int)($koperasiResponse['status'] ?? 0) === 1 && isset($koperasiResponse['data']) && is_array($koperasiResponse['data'])) {
    $koperasiList = $koperasiResponse['data'];
} else {
    $koperasiError = (string)($koperasiResponse['message'] ?? 'Gagal mengambil data koperasi.');
}

$anggotaResponse = api_get_json(api_build_url('anggota'));
$anggotaList = [];
$anggotaError = '';

if ((int)($anggotaResponse['status'] ?? 0) === 1 && isset($anggotaResponse['data']) && is_array($anggotaResponse['data'])) {
    $anggotaList = $anggotaResponse['data'];
} else {
    $anggotaError = (string)($anggotaResponse['message'] ?? 'Gagal mengambil data anggota.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koperasi & Anggota</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-5">

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Koperasi</h2>
                <a href="insert.php?tabel=koperasi" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                    + Tambah Data
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama</th>
                            <th class="py-3 px-6 text-left">Alamat</th>
                            <th class="py-3 px-6 text-left">Telp</th>
                            <th class="py-3 px-6 text-left">Tgl Berdiri</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php if (!empty($koperasiList)) { ?>
                            <?php foreach ($koperasiList as $k) { ?>
                                <?php
                                    $id = (int)($k['id_koperasi'] ?? 0);
                                    $telp = trim((string)($k['telp'] ?? ''));
                                    $tgl = trim((string)($k['tgl_berdiri'] ?? ''));
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-medium"><?php echo h($id); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($k['nama_koperasi'] ?? ''); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($k['alamat'] ?? ''); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($telp !== '' ? $telp : '-'); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($tgl !== '' ? $tgl : '-'); ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center">
                                            <a href="update.php?tabel=koperasi&id_koperasi=<?php echo h($id); ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete('koperasi', <?php echo h($id); ?>)" class="w-4 transform hover:text-red-500 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-red-500 font-bold">
                                    <?php echo h($koperasiError !== '' ? $koperasiError : 'Data koperasi kosong.'); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Anggota</h2>
                <a href="insert.php?tabel=anggota" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                    + Tambah Data
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Nama</th>
                            <th class="py-3 px-6 text-left">Koperasi</th>
                            <th class="py-3 px-6 text-left">Alamat</th>
                            <th class="py-3 px-6 text-left">No HP</th>
                            <th class="py-3 px-6 text-left">Tgl Gabung</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php if (!empty($anggotaList)) { ?>
                            <?php foreach ($anggotaList as $a) { ?>
                                <?php
                                    $id = (int)($a['id_anggota'] ?? 0);
                                    $idKop = trim((string)($a['id_koperasi'] ?? ''));
                                    $namaKop = trim((string)($a['nama_koperasi'] ?? ''));
                                    $koperasiLabel = $idKop;
                                    if ($namaKop !== '') {
                                        $koperasiLabel = $koperasiLabel !== '' ? ($koperasiLabel . ' - ' . $namaKop) : $namaKop;
                                    }

                                    $noHp = trim((string)($a['no_hp'] ?? ''));
                                    $tgl = trim((string)($a['tanggal_gabung'] ?? ''));
                                ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left whitespace-nowrap font-medium"><?php echo h($id); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($a['nama'] ?? ''); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($koperasiLabel !== '' ? $koperasiLabel : '-'); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($a['alamat'] ?? ''); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($noHp !== '' ? $noHp : '-'); ?></td>
                                    <td class="py-3 px-6 text-left"><?php echo h($tgl !== '' ? $tgl : '-'); ?></td>
                                    <td class="py-3 px-6 text-center">
                                        <div class="flex item-center justify-center">
                                            <a href="update.php?tabel=anggota&id_anggota=<?php echo h($id); ?>" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <a href="javascript:void(0);" onclick="confirmDelete('anggota', <?php echo h($id); ?>)" class="w-4 transform hover:text-red-500 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-red-500 font-bold">
                                    <?php echo h($anggotaError !== '' ? $anggotaError : 'Data anggota kosong.'); ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function confirmDelete(resource, id) {
            const idKey = resource === 'koperasi' ? 'id_koperasi' : 'id_anggota';

            Swal.fire({
                title: 'Apakah anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete.php?tabel=' + encodeURIComponent(resource) + '&' + idKey + '=' + encodeURIComponent(id);
                }
            });
        }
    </script>
</body>
</html>
