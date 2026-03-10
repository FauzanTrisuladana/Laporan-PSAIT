<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <?php
    $id = $_GET['id_mhs'];
    // URL API VPS untuk GET data spesifik
    $api_url = 'http://10.33.35.96/sait_api/mahasiswa_api.php?id_mhs=' . $id;
    
    $json_data = @file_get_contents($api_url);
    $response = json_decode($json_data, true);
    
    // Ambil data dari key 'data' index ke-0
    $mhs = [];
    if(isset($response['data'][0])) { 
        $mhs = $response['data'][0]; 
    }
    ?>

    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Edit Data Mahasiswa</h2>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nama">Nama Lengkap</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="nama" type="text" name="nama" value="<?php echo isset($mhs['nama']) ? $mhs['nama'] : ''; ?>" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="alamat">Alamat</label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="alamat" name="alamat" required><?php echo isset($mhs['alamat']) ? $mhs['alamat'] : ''; ?></textarea>
            </div>
            
            <div class="flex items-center justify-between">
                <a href="index.php" class="text-gray-500 hover:text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Batal</a>
                <input type="submit" name="submit" value="Update Data" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-pointer">
            </div>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $data = array(
                'nama' => $_POST['nama'],
                'alamat' => $_POST['alamat']
            );

            // Perhatikan URL: Menggunakan POST tapi ID ditaruh di Query String (?id_mhs=)
            // Sesuai logika: case 'POST': if(!empty($_GET["id_mhs"])) { update... }
            $update_url = 'http://10.33.35.96/sait_api/mahasiswa_api.php?id_mhs=' . $id;

            $ch = curl_init($update_url); 
            curl_setopt($ch, CURLOPT_POST, true); // Tetap POST, bukan PUT
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            curl_close($ch);

            echo "<script>
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data mahasiswa berhasil diperbarui.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';
                    }
                });
            </script>";
        }
        ?>
    </div>
</body>
</html>