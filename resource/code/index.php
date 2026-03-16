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
                        // URL API LOKAL (Pastikan API Ubuntu sudah jalan)
                        $api_url = "http://103.139.192.46/a1/6/sait_api/mahasiswa_api.php";
                        
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
                <h2 class="text-2xl font-bold text-gray-800">Top Anime (MyAnimeList via Jikan)</h2>
                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Live Data</span>
            </div>

            <?php
            // API publik tanpa API key: Jikan (Unofficial MyAnimeList API)
            // Docs: https://docs.api.jikan.moe/
            $anime_url = "https://api.jikan.moe/v4/top/anime?limit=12";

            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10,
                    'header' => "User-Agent: sait_frontend/1.0\r\nAccept: application/json\r\n",
                ]
            ]);

            $anime_json = @file_get_contents($anime_url, false, $context);
            $anime_data = $anime_json ? json_decode($anime_json, true) : null;

            $anime_list = [];
            if (is_array($anime_data) && isset($anime_data['data']) && is_array($anime_data['data'])) {
                $anime_list = $anime_data['data'];
            }

            if (!empty($anime_list)) {
                $has_images = isset($anime_list[0]['images']) && is_array($anime_list[0]['images']);

                if ($has_images) {
                    echo "<div class='grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4'>";

                    foreach ($anime_list as $anime) {
                        $title = htmlspecialchars($anime['title'] ?? 'Untitled');
                        $rank = htmlspecialchars((string)($anime['rank'] ?? '-'));
                        $score = htmlspecialchars((string)($anime['score'] ?? '-'));
                        $type = htmlspecialchars($anime['type'] ?? '-');
                        $episodes = htmlspecialchars((string)($anime['episodes'] ?? '-'));
                        $year = htmlspecialchars((string)($anime['year'] ?? '-'));
                        $url = htmlspecialchars($anime['url'] ?? '#');
                        $img = $anime['images']['jpg']['image_url'] ?? '';
                        $img = htmlspecialchars($img);

                        echo "<a href='{$url}' target='_blank' rel='noopener noreferrer' class='block bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition'>";
                        if (!empty($img)) {
                            echo "<img src='{$img}' alt='{$title}' class='w-full h-56 object-cover' loading='lazy' />";
                        }
                        echo "<div class='p-4'>";
                        echo "<div class='flex items-start justify-between gap-3'>";
                        echo "<h3 class='font-bold text-gray-800 leading-snug'>{$title}</h3>";
                        echo "<span class='shrink-0 bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-1 rounded'>#{$rank}</span>";
                        echo "</div>";
                        echo "<div class='mt-2 text-sm text-gray-600'>";
                        echo "Skor: <span class='font-semibold text-gray-800'>{$score}</span> · {$type} · Ep: {$episodes} · {$year}";
                        echo "</div>";
                        echo "</div>";
                        echo "</a>";
                    }

                    echo "</div>";
                } else {
                    echo "<div class='overflow-x-auto'>";
                    echo "<table class='min-w-full bg-white border border-gray-300'>";
                    echo "<thead class='bg-gray-200'>";
                    echo "<tr class='text-gray-600 uppercase text-sm leading-normal'>";
                    echo "<th class='py-3 px-6 text-left'>Rank</th>";
                    echo "<th class='py-3 px-6 text-left'>Judul</th>";
                    echo "<th class='py-3 px-6 text-left'>Skor</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody class='text-gray-600 text-sm font-light'>";

                    foreach ($anime_list as $anime) {
                        $title = htmlspecialchars($anime['title'] ?? 'Untitled');
                        $rank = htmlspecialchars((string)($anime['rank'] ?? '-'));
                        $score = htmlspecialchars((string)($anime['score'] ?? '-'));
                        echo "<tr class='border-b border-gray-200 hover:bg-gray-100'>";
                        echo "<td class='py-3 px-6 text-left font-medium'>#{$rank}</td>";
                        echo "<td class='py-3 px-6 text-left'>{$title}</td>";
                        echo "<td class='py-3 px-6 text-left'>{$score}</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table></div>";
                }
            } else {
                echo "<div class='text-center py-4 text-red-500 font-bold'>Gagal mengambil data anime. Cek koneksi internet / limit API Jikan.</div>";
            }
            ?>

            <p class="text-xs text-gray-500 mt-3">* Sumber data: Jikan API (Top Anime, limit 12).</p>
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