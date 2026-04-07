<?php
require_once "config.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$request_method = $_SERVER["REQUEST_METHOD"] ?? 'GET';
$resource = strtolower(trim($_GET['tabel'] ?? $_GET['table'] ?? $_GET['resource'] ?? $_GET['endpoint'] ?? ''));

if ($resource === '') {
    send_json([
        'status' => 1,
        'message' => 'Koperasi API aktif.',
        'database' => 'sait_new_a1_6',
        'cara_pakai' => [
            'Pilih resource pakai query: ?tabel=koperasi atau ?tabel=anggota',
            'ID pakai: id_koperasi / id_anggota',
            'Body bisa form-data atau JSON',
        ],
        'endpoints' => [
            'GET /a1/6/new/?tabel=koperasi',
            'GET /a1/6/new/?tabel=koperasi&id_koperasi=1',
            'POST /a1/6/new/?tabel=koperasi',
            'POST /a1/6/new/?tabel=koperasi&id_koperasi=1',
            'DELETE /a1/6/new/?tabel=koperasi&id_koperasi=1',
            'GET /a1/6/new/?tabel=anggota',
            'GET /a1/6/new/?tabel=anggota&id_anggota=1',
            'POST /a1/6/new/?tabel=anggota',
            'POST /a1/6/new/?tabel=anggota&id_anggota=1',
            'DELETE /a1/6/new/?tabel=anggota&id_anggota=1',
        ],
    ]);
}

switch ($resource) {
    case 'koperasi':
        handle_koperasi($request_method);
        break;
    case 'anggota':
        handle_anggota($request_method);
        break;
    default:
        send_json([
            'status' => 0,
            'message' => 'Endpoint tidak ditemukan. Gunakan ?tabel=koperasi atau ?tabel=anggota',
        ], 404);
}

function handle_koperasi(string $method): void
{
    $id = null;

    if (isset($_GET['id_koperasi'])) {
        $id = $_GET['id_koperasi'];
    } elseif (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    if ($id !== null) {
        if (!ctype_digit((string) $id) || (int) $id <= 0) {
            send_json([
                'status' => 0,
                'message' => 'id_koperasi harus berupa angka positif.',
            ], 400);
        }

        $id = (int) $id;
    }

    switch ($method) {
        case 'GET':
            if ($id === null) {
                get_koperasis();
            }

            get_koperasi($id);
            break;

        case 'POST':
            if ($id === null) {
                insert_koperasi();
            }

            update_koperasi($id);
            break;

        case 'PUT':
            if ($id === null) {
                send_json([
                    'status' => 0,
                    'message' => 'id_koperasi wajib untuk update (PUT).',
                ], 400);
            }

            update_koperasi($id);
            break;

        case 'DELETE':
            if ($id === null) {
                send_json([
                    'status' => 0,
                    'message' => 'id_koperasi wajib untuk delete.',
                ], 400);
            }

            delete_koperasi($id);
            break;

        default:
            send_json([
                'status' => 0,
                'message' => 'Method tidak didukung.',
            ], 405);
    }
}

function handle_anggota(string $method): void
{
    $id = null;

    if (isset($_GET['id_anggota'])) {
        $id = $_GET['id_anggota'];
    } elseif (isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    if ($id !== null) {
        if (!ctype_digit((string) $id) || (int) $id <= 0) {
            send_json([
                'status' => 0,
                'message' => 'id_anggota harus berupa angka positif.',
            ], 400);
        }

        $id = (int) $id;
    }

    switch ($method) {
        case 'GET':
            if ($id === null) {
                get_anggotas();
            }

            get_anggota($id);
            break;

        case 'POST':
            if ($id === null) {
                insert_anggota();
            }

            update_anggota($id);
            break;

        case 'PUT':
            if ($id === null) {
                send_json([
                    'status' => 0,
                    'message' => 'id_anggota wajib untuk update (PUT).',
                ], 400);
            }

            update_anggota($id);
            break;

        case 'DELETE':
            if ($id === null) {
                send_json([
                    'status' => 0,
                    'message' => 'id_anggota wajib untuk delete.',
                ], 400);
            }

            delete_anggota($id);
            break;

        default:
            send_json([
                'status' => 0,
                'message' => 'Method tidak didukung.',
            ], 405);
    }
}

function get_koperasis(): void
{
    global $mysqli;

    $query = "SELECT * FROM koperasi";
    $data = [];
    $result = $mysqli->query($query);

    if ($result === false) {
        send_json([
            'status' => 0,
            'message' => 'Gagal mengambil data koperasi.',
            'error' => $mysqli->error,
        ], 500);
    }

    while ($row = mysqli_fetch_object($result)) {
        $data[] = $row;
    }

    send_json([
        'status' => 1,
        'message' => 'Get List Koperasi Successfully.',
        'data' => $data,
    ]);
}

function get_koperasi(int $id): void
{
    global $mysqli;

    $query = "SELECT * FROM koperasi WHERE id_koperasi=" . $id . " LIMIT 1";
    $data = [];
    $result = $mysqli->query($query);

    if ($result === false) {
        send_json([
            'status' => 0,
            'message' => 'Gagal mengambil data koperasi.',
            'error' => $mysqli->error,
        ], 500);
    }

    while ($row = mysqli_fetch_object($result)) {
        $data[] = $row;
    }

    send_json([
        'status' => 1,
        'message' => 'Get Koperasi Successfully.',
        'data' => $data,
    ]);
}

function insert_koperasi(): void
{
    global $mysqli;

    $data = get_request_data();
    $arrcheckpost = ['nama_koperasi' => '', 'alamat' => ''];
    $hitung = count(array_intersect_key($data, $arrcheckpost));

    if ($hitung !== count($arrcheckpost)) {
        send_json([
            'status' => 0,
            'message' => 'Parameter Do Not Match',
        ], 400);
    }

    $nama = esc((string) $data['nama_koperasi']);
    $alamat = esc((string) $data['alamat']);
    $telp_sql = sql_nullable($data['telp'] ?? null);
    $tgl_sql = sql_nullable($data['tgl_berdiri'] ?? null);

    $result = mysqli_query(
        $mysqli,
        "INSERT INTO koperasi SET nama_koperasi='$nama', alamat='$alamat', telp=$telp_sql, tgl_berdiri=$tgl_sql"
    );

    if ($result) {
        send_json([
            'status' => 1,
            'message' => 'Koperasi Added Successfully.',
            'id_koperasi' => (int) $mysqli->insert_id,
        ], 201);
    }

    send_json([
        'status' => 0,
        'message' => 'Koperasi Addition Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function update_koperasi(int $id): void
{
    global $mysqli;

    $data = get_request_data();
    $arrcheckpost = ['nama_koperasi' => '', 'alamat' => ''];
    $hitung = count(array_intersect_key($data, $arrcheckpost));

    if ($hitung !== count($arrcheckpost)) {
        send_json([
            'status' => 0,
            'message' => 'Parameter Do Not Match',
        ], 400);
    }

    $nama = esc((string) $data['nama_koperasi']);
    $alamat = esc((string) $data['alamat']);
    $telp_sql = sql_nullable($data['telp'] ?? null);
    $tgl_sql = sql_nullable($data['tgl_berdiri'] ?? null);

    $result = mysqli_query(
        $mysqli,
        "UPDATE koperasi SET nama_koperasi='$nama', alamat='$alamat', telp=$telp_sql, tgl_berdiri=$tgl_sql WHERE id_koperasi='$id'"
    );

    if ($result) {
        send_json([
            'status' => 1,
            'message' => 'Koperasi Updated Successfully.',
        ]);
    }

    send_json([
        'status' => 0,
        'message' => 'Koperasi Updation Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function delete_koperasi(int $id): void
{
    global $mysqli;

    $query = "DELETE FROM koperasi WHERE id_koperasi=" . $id;

    if (mysqli_query($mysqli, $query)) {
        send_json([
            'status' => 1,
            'message' => 'Koperasi Deleted Successfully.',
        ]);
    }

    send_json([
        'status' => 0,
        'message' => 'Koperasi Deletion Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function get_anggotas(): void
{
    global $mysqli;

    $query = "SELECT a.*, k.nama_koperasi FROM anggota a LEFT JOIN koperasi k ON a.id_koperasi = k.id_koperasi";
    $data = [];
    $result = $mysqli->query($query);

    if ($result === false) {
        send_json([
            'status' => 0,
            'message' => 'Gagal mengambil data anggota.',
            'error' => $mysqli->error,
        ], 500);
    }

    while ($row = mysqli_fetch_object($result)) {
        $data[] = $row;
    }

    send_json([
        'status' => 1,
        'message' => 'Get List Anggota Successfully.',
        'data' => $data,
    ]);
}

function get_anggota(int $id): void
{
    global $mysqli;

    $query = "SELECT a.*, k.nama_koperasi FROM anggota a LEFT JOIN koperasi k ON a.id_koperasi = k.id_koperasi WHERE a.id_anggota=" . $id . " LIMIT 1";
    $data = [];
    $result = $mysqli->query($query);

    if ($result === false) {
        send_json([
            'status' => 0,
            'message' => 'Gagal mengambil data anggota.',
            'error' => $mysqli->error,
        ], 500);
    }

    while ($row = mysqli_fetch_object($result)) {
        $data[] = $row;
    }

    send_json([
        'status' => 1,
        'message' => 'Get Anggota Successfully.',
        'data' => $data,
    ]);
}

function insert_anggota(): void
{
    global $mysqli;

    $data = get_request_data();
    $arrcheckpost = ['id_koperasi' => '', 'nama' => '', 'alamat' => ''];
    $hitung = count(array_intersect_key($data, $arrcheckpost));

    if ($hitung !== count($arrcheckpost)) {
        send_json([
            'status' => 0,
            'message' => 'Parameter Do Not Match',
        ], 400);
    }

    $id_koperasi = $data['id_koperasi'];
    if (!ctype_digit((string) $id_koperasi) || (int) $id_koperasi <= 0) {
        send_json([
            'status' => 0,
            'message' => 'id_koperasi harus berupa angka positif.',
        ], 400);
    }

    $id_koperasi = (int) $id_koperasi;
    $nama = esc((string) $data['nama']);
    $alamat = esc((string) $data['alamat']);
    $no_hp_sql = sql_nullable($data['no_hp'] ?? null);
    $tgl_sql = sql_nullable($data['tanggal_gabung'] ?? null);

    $result = mysqli_query(
        $mysqli,
        "INSERT INTO anggota SET id_koperasi=$id_koperasi, nama='$nama', alamat='$alamat', no_hp=$no_hp_sql, tanggal_gabung=$tgl_sql"
    );

    if ($result) {
        send_json([
            'status' => 1,
            'message' => 'Anggota Added Successfully.',
            'id_anggota' => (int) $mysqli->insert_id,
        ], 201);
    }

    send_json([
        'status' => 0,
        'message' => 'Anggota Addition Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function update_anggota(int $id): void
{
    global $mysqli;

    $data = get_request_data();
    $arrcheckpost = ['id_koperasi' => '', 'nama' => '', 'alamat' => ''];
    $hitung = count(array_intersect_key($data, $arrcheckpost));

    if ($hitung !== count($arrcheckpost)) {
        send_json([
            'status' => 0,
            'message' => 'Parameter Do Not Match',
        ], 400);
    }

    $id_koperasi = $data['id_koperasi'];
    if (!ctype_digit((string) $id_koperasi) || (int) $id_koperasi <= 0) {
        send_json([
            'status' => 0,
            'message' => 'id_koperasi harus berupa angka positif.',
        ], 400);
    }

    $id_koperasi = (int) $id_koperasi;
    $nama = esc((string) $data['nama']);
    $alamat = esc((string) $data['alamat']);
    $no_hp_sql = sql_nullable($data['no_hp'] ?? null);
    $tgl_sql = sql_nullable($data['tanggal_gabung'] ?? null);

    $result = mysqli_query(
        $mysqli,
        "UPDATE anggota SET id_koperasi=$id_koperasi, nama='$nama', alamat='$alamat', no_hp=$no_hp_sql, tanggal_gabung=$tgl_sql WHERE id_anggota='$id'"
    );

    if ($result) {
        send_json([
            'status' => 1,
            'message' => 'Anggota Updated Successfully.',
        ]);
    }

    send_json([
        'status' => 0,
        'message' => 'Anggota Updation Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function delete_anggota(int $id): void
{
    global $mysqli;

    $query = "DELETE FROM anggota WHERE id_anggota=" . $id;

    if (mysqli_query($mysqli, $query)) {
        send_json([
            'status' => 1,
            'message' => 'Anggota Deleted Successfully.',
        ]);
    }

    send_json([
        'status' => 0,
        'message' => 'Anggota Deletion Failed.',
        'error' => $mysqli->error,
    ], 500);
}

function get_request_data(): array
{
    if (!empty($_POST) && is_array($_POST)) {
        return $_POST;
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $json = json_decode($raw, true);
    if (is_array($json)) {
        return $json;
    }

    $data = [];
    parse_str($raw, $data);

    return is_array($data) ? $data : [];
}

function send_json(array $payload, int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode($payload);
    exit;
}

function esc(string $value): string
{
    global $mysqli;

    return mysqli_real_escape_string($mysqli, $value);
}

function sql_nullable(mixed $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    $value = trim((string) $value);
    if ($value === '') {
        return 'NULL';
    }

    return "'" . esc($value) . "'";
}
