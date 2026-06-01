# Modul Praktikum Sistem Administrasi dan Informasi Terdistribusi 2026

### 4. Clone repository
Untuk melakukan cloning repository, Anda dapat menekan tombol code, dan klik copy URL seperti Gambar 11.4.23.

*Gambar 11.4.23 Copy URL Repository*

Buka terminal **di Laptop atau komputer** yang terkoneksi dengan jaringan internet yang sama dengan VPS dan jalankan kode seperti berikut Gambar 11.4.24. Jika berhasil maka folder php-jwt-example akan muncul.

```bash
[9:01:53] ~/gitea
$ git clone http://192.168.1.72:3000/praktikan/php-jwt-example.git
Cloning into 'php-jwt-example'...
remote: Enumerating objects: 48, done.
remote: Counting objects: 100% (48/48), done.
remote: Compressing objects: 100% (42/42), done.
remote: Total 48 (delta 0), reused 0 (delta 0), pack-reused 0 (from 0)
Receiving objects: 100% (48/48), 37.45 KiB | 7.49 MiB/s, done.
[9:02:04] ~/gitea
$ ls
php-jwt-example
[9:02:09] ~/gitea
$ 
```

*Gambar 11.4.24 Git clone*

Buka kode repository tersebut di Visual Studio Code seperti Gambar 11.4.25. Jika Anda ingin menjalankan aplikasi di Laptop atau komputer, Anda harus menambahkan .env karena di pengaturan yang kita lakukan, file .env tidak akan disimpan di repository. Namun, kita tidak akan menjalankan di lokal dan menganggap aplikasi telah berhasil jalan dan selesai dilakukan perubahan.

*Gambar 11.4.25 Membuka kode di VS code*

### 5. Implementasi Gitea runner untuk CICD
**Kita masih melanjutkan di VS Code yang ada di laptop atau kompter yang berada di jaringan internet yang sama dengan VPS**, kita akan menambahkan script workflow untuk CICD. Script ini serupa dengan Github Action, sehingga dapat juga digunakan di Github. Tambahkan file `deploys.yaml` di dalam folder `.gitea/workflows/` (jangan lupa tanda titik di depan) seperti Gambar 11.4.26. Di kode yang di highligh, terdapat change directory ke lokasi php-jwt-example kita di VPS. Anda perlu mengubahnya sesuai dengan kondisi masing-masing VPS! Pada contoh ini, lokasi php-jwt-example berada di home VPS, masuk ke folder psait, dan masuk lagi ke folder php-jwt-example. Jika diperhatikan dengan seksama, script yang ada di dalam workflows CICD ini mirip seperti apa yang kita lakukan secara manual. Namun, script ini akan dijalankan oleh robot sehingga kita tidak lagi mengupdate secara manual.

```yaml
name: Auto Deploy via SSH Password
run-name: ${{ gitea.actor }} sedang mendeploy aplikasi 🚀

# Trigger: Jalan setiap ada push ke branch master
on:
  push:
    branches:
      - main

jobs:
  deploy-ke-server:
    runs-on: ubuntu-latest
    steps:
      - name: Menghubungkan ke Server via SSH
        # Kita gunakan plugin siap pakai: appleboy/ssh-action
        uses: appleboy/ssh-action@v1.0.0
        with:
          # Ambil data rahasia dari Settings Gitea
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          password: ${{ secrets.PASSWORD }}
          port: 22

          # Script yang akan dijalankan di dalam server target
          script: |
            echo "🧹 PRE-CLEANUP: Membersihkan sampah sebelum mulai..."
            # Hapus image 'dangling' (bekas build sebelumnya) agar ada ruang untuk build baru
            docker image prune -f

            echo "🚀 Pulling code..."
            cd psait/php-jwt-example
            git pull origin main

            echo "🏗️ Build & Deploy..."
            # Matikan container dulu
            docker compose down

            # Build baru
            docker compose up -d --build

            echo "🧹 POST-CLEANUP: Bersih-bersih akhir..."
            # Hapus semua yang tidak terpakai (Network, Container mati, Cache build)
            docker system prune -f -a --volumes

            # Cek sisa disk (untuk log history)
            df -h | grep /dev/
```

*Gambar 11.4.26 Deploys YAML Workflows*

Di script Gambar 11.4.26, terdapat host, username, dan juga password. Kita perlu menambahkan secrets variable tersebut di website gitea yang telah kita buat. Buka repository Anda di gitea, dan klik setting (bagian kanan baris ketiga).

*Gambar 11.4.27 Tombol setting di kanan baris 3*

Pilih menu **Actions > Secrets** maka akan tertampil seperti Gambar 11.4.28. Di halaman ini Anda perlu menambahkan konfigurasi SSH server VPS kalian! Sesuaikan data VPS yang kalian miliki.

*Gambar 11.4.28 Halaman Secrets*

#### Tambah Server IP VPS
- **Name:** HOST
- **Value:** 192.168.1.72
- **Description:** IP dari VPS kalian!

*Gambar 11.4.29 Tambah Server IP VPS*

#### Tambah Username VPS
- **Name:** USERNAME
- **Value:** praktikan
- **Description:** Username VPS kalian!

*Gambar 11.4.30 Tambah Username VPS*

#### Tambah Password VPS
- **Name:** PASSWORD
- **Value:** praktikan
- **Description:** password VPS kalian!

*Gambar 11.4.31 Tambah Password VPS*

*Gambar 11.4.32 Tiga secrets*

Untuk mengetes CICD, kita akan mencoba membuat route endpoint baru bernama cicd. Tambahkan route tersebut di `index.php` yang pernah kita buat sebagai routes aplikasi seperti Gambar 11.4.33.

```php
case '/api/cicd':
    // Simple response to indicate Docker is working
    http_response_code(200);
    echo json_encode(array("message" => "CI/CD is working!"));
    break;
```

*Gambar 11.4.33 Route baru cicd*

Sebelum kita melakukan push, buka url cicd di IP VPS Anda saat ini, maka web akan menampilkan seperti Gambar 11.4.34. Route endpoint CICD belum ada di VPS. Kita akan mencoba melihat apakah sistem CICD berhasil melakukan update aplikasi secara otomatis yang ditandai dengan berfungsinya route endpoint cicd.

```json
{"message":"Endpoint not found.","debug_uri":"\/cicd"}
```

*Gambar 11.4.34 Tampilan url CICD awal*

### 6. Push aplikasi
Masukkan perubahan kode ke staging dengan `"git add ."`, (jangan lupa titik). Kemudian tambahkan commit message. Lakukan push dengan `git push`. Jika Anda mengalami kredensial error seperti Gambar 11.4.35 (karena di pembuatan modul ini, akun git belum ditambahkan), tambahkan akun gitea yang telah didaftarkan. Pada halaman website gitea ketika user melakukan login, Gitea akan meminta autorisasi, Klik **Authorize Application**.

*Gambar 11.4.35 Git Push dan Kredensial*

Setelah klik authorize, perubahan akan ditambahkan ke dalam gitea.

```bash
[9:50:06] ~/gitea/php-jwt-example
$ git push origin main
Enumerating objects: 8, done.
Counting objects: 100% (8/8), done.
Delta compression using up to 8 threads
Compressing objects: 100% (4/4), done.
Writing objects: 100% (6/6), 1.17 KiB | 1.17 MiB/s, done.
Total 6 (delta 2), reused 0 (delta 0), pack-reused 0
remote: . Processing 1 references
remote: Processed 1 references in total
To http://192.168.1.72:3000/praktikan/php-jwt-example.git
   c09e00b..aadd917  main -> main
```

*Gambar 11.4.36 Lanjutan terminal ketika push diizinkan*

Jika kita membuka menu jaction di repository aplikasi di gitea, maka kita akan melihat Actions CICD kita berjalan seperti Gambar 11.4.37.

*Gambar 11.4.37 Actions Berjalan di Gitea*

Anda bisa melihat detail bagaimana robot menjalankan kode CICD workflows yang dibuat seperti Gambar 11.4.38.
*Gambar 11.4.38 Detail Proses Robot Menjalankan Kode CI/CD*

Jika proses deploy sukses, maka akan muncul tanda centang hijau di samping commit message Anda, seperti yang ditunjukkan pada Gambar 11.4.39.

*Gambar 11.4.39 Status Deploy Sukses (Centang Hijau)*

Sekarang, mari kita uji kembali route endpoint cicd yang telah kita tambahkan dengan membuka kembali URL di browser Anda. Jika sistem CI/CD berhasil berjalan dengan baik, maka halaman web Anda akan menampilkan pesan sukses seperti pada Gambar 11.4.40.

```json
{"message":"CI\/CD is working!"}
```

*Gambar 11.4.40 Tampilan URL CICD Setelah Berhasil Deploy*

Selamat! Anda telah berhasil mengimplementasikan sistem otomasi CI/CD menggunakan Gitea dan Docker Runner di VPS Anda sendiri.

---

## 11.5 Tugas Praktikum

Selesaikanlah tugas praktikum berikut ini secara mandiri atau berkelompok sesuai instruksi dosen/asisten pengampu:

1. Buatlah sebuah repositori baru di Gitea lokal milik Anda dengan nama `tugas-cicd-nama-praktikan`.
2. Integrasikan proyek web app sederhana (boleh berbasis PHP, Node.js, atau Python) ke dalam repositori tersebut dan pastikan sudah berjalan di dalam Docker Container.
3. Buat skrip workflow otomasi CI/CD (`.gitea/workflows/deploy.yaml`) untuk melakukan auto-deploy ke server VPS target setiap kali terjadi aktivitas *push* ke branch `main`.
4. Tambahkan konfigurasi Environment Secrets (`HOST`, `USERNAME`, `PASSWORD`) di Gitea untuk menjaga keamanan data akses SSH ke VPS Anda.
5. Lakukan perubahan kode sederhana pada proyek Anda (misalnya menambahkan teks atau halaman baru), kemudian lakukan pengujian dengan melakukan perintah `git push`. Ambil tangkapan layar (*screenshot*) yang menunjukkan bahwa robot Runner berhasil mengeksekusi semua tahapan deployment hingga sukses (centang hijau) dan perubahan berhasil diterapkan di web browser Anda.
6. Susun laporan hasil praktikum dalam format PDF yang berisi langkah-langkah pengerjaan serta bukti-bukti tangkapan layar pengujian yang telah Anda lakukan.