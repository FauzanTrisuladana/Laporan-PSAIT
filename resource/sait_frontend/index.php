<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa & Cuaca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <div class="container mx-auto mt-10 p-5">
        
        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Daftar Mahasiswa</h2>
                <a href="insert.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
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
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php
                        // URL REST API pada VPS yang sama dengan endpoint pengujian Postman
                        $api_url = 'http://10.33.35.96/sait_api/mahasiswa_api.php';
                        
                        $json_data = @file_get_contents($api_url);
                        $mahasiswa = [];
                        
                        if($json_data) {
                            $response = json_decode($json_data, true);
                            if(isset($response['status']) && $response['status'] == 1 && isset($response['data'])) {
                                $mahasiswa = $response['data'];
                            }
                        }

                        if(!empty($mahasiswa)) {
                            foreach ($mahasiswa as $mhs) {
                                echo "<tr class='border-b border-gray-200 hover:bg-gray-100'>";
                                echo "<td class='py-3 px-6 text-left whitespace-nowrap font-medium'>" . $mhs['id_mhs'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $mhs['nama'] . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $mhs['alamat'] . "</td>";
                                echo "<td class='py-3 px-6 text-center'>
                                        <div class='flex item-center justify-center'>
                                            <a href='update.php?id_mhs=" . $mhs['id_mhs'] . "' class='w-4 mr-2 transform hover:text-purple-500 hover:scale-110'>
                                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z' />
                                                </svg>
                                            </a>
                                            <a href='javascript:void(0);' onclick='confirmDelete(" . $mhs['id_mhs'] . ")' class='w-4 transform hover:text-red-500 hover:scale-110'>
                                                <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                                                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' />
                                                </svg>
                                            </a>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-4 text-red-500 font-bold'>Gagal mengambil data mahasiswa. Pastikan Server API aktif.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Prakiraan Cuaca (Berlin)</h2>
                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Live Data</span>
            </div>

            <div class="overflow-x-auto h-64"> <table class="min-w-full bg-white border border-gray-300">
                    <thead class="sticky top-0 bg-gray-200">
                        <tr class="text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Waktu (Jam)</th>
                            <th class="py-3 px-6 text-left">Temperatur (&deg;C)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <?php
                        // URL API OPEN-METEO
                        $weather_url = "https://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41&hourly=temperature_2m";
                        
                        // Mengambil data cuaca
                        $weather_json = @file_get_contents($weather_url);
                        $weather_data = json_decode($weather_json, true);

                        // Cek apakah data berhasil diambil
                        if ($weather_data && isset($weather_data['hourly'])) {
                            // API ini memisahkan array 'time' dan 'temperature_2m'
                            // Kita ambil array waktu dan array suhu
                            $times = $weather_data['hourly']['time'];
                            $temps = $weather_data['hourly']['temperature_2m'];

                            // Loop sebanyak data yang ada (disini saya limit 24 jam pertama agar tidak terlalu panjang)
                            // Hapus 'array_slice' jika ingin menampilkan semua data 7 hari
                            $limit = 24; 
                            
                            foreach (array_slice($times, 0, $limit) as $index => $time) {
                                // Format Waktu agar lebih enak dibaca (Mengubah 2024-01-01T00:00 menjadi format tanggal biasa)
                                $date_formatted = date("d F Y, H:i", strtotime($time));
                                
                                echo "<tr class='border-b border-gray-200 hover:bg-gray-100'>";
                                echo "<td class='py-3 px-6 text-left font-medium'>" . $date_formatted . "</td>";
                                echo "<td class='py-3 px-6 text-left'>" . $temps[$index] . " &deg;C</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' class='text-center py-4 text-red-500'>Gagal mengambil data cuaca. Cek koneksi internet.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-500 mt-2">* Menampilkan 24 data pertama dari API.</p>
        </div>

    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete.php?id_mhs=' + id;
                }
            })
        }
    </script>
</body>
</html>