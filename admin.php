<?php
session_start();

define("ADMIN_PASSWORD", "123456");
define("KEYS_FILE", "keys.json");
define("NAP_FILE", "don_nap.json");
define("TB_FILE", "thongbao.json");

// Hàm đọc file json
function save_keys($data) {
    save_file(KEYS_FILE, $data);
}
function read_file($file) {
    if (!file_exists($file)) file_put_contents($file, json_encode([]));
    $content = file_get_contents($file);
    $json = json_decode($content, true);
    return is_array($json) ? $json : [];
}
// Hàm lưu file json
function save_file($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Hàm đọc keys và chuyển expire sang dạng ngày tháng
function read_keys() {
    return read_file(KEYS_FILE);
}
// Hàm đọc keys với expire chuyển thành chuỗi ngày tháng dễ đọc
function read_keys_readable() {
    $data = read_file(KEYS_FILE);
    foreach ($data as &$item) {
        if (isset($item['expire'])) {
            if ($item['expire'] == 0) {
                $item['expire_readable'] = 'Vĩnh viễn';
            } else {
                $item['expire_readable'] = date('Y-m-d H:i:s', $item['expire']);
            }
        } else {
            $item['expire_readable'] = 'Không xác định';
        }
    }
    return $data;
}

// ... phần xử lý xóa đơn nạp, duyệt/refuse đơn nạp, đăng nhập admin (giữ nguyên) ...

// Xử lý xóa đơn nạp (truy cập qua ?action=delete&id=...)
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = intval($_GET['id']);
    $list = read_file(NAP_FILE);
    if (isset($list[$id])) {
        unset($list[$id]);
        $list = array_values($list); // Reindex lại mảng
        save_file(NAP_FILE, $list);
    }
    header("Location: admin.php");
    exit;
}

// Xử lý duyệt hoặc từ chối đơn nạp (approve hoặc refuse)
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['approve', 'refuse'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    $naps = read_file(NAP_FILE);
    if (isset($naps[$id])) {
        $msg = $_POST['ghichu'] ?? '';
        $naps[$id]['trangthai'] = $action === 'approve' ? 'Đã duyệt' : 'Từ chối';
        $naps[$id]['ghichu'] = $msg;
        save_file(NAP_FILE, $naps);

        $tb = read_file(TB_FILE);
        $user = $naps[$id]['user'] ?? 'unknown';
        $tb[] = [
            "user" => $user,
            "noidung" => "Admin đã $action đơn nạp: $msg",
            "thoigian" => date("d/m/Y H:i")
        ];
        save_file(TB_FILE, $tb);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Đăng nhập admin
if (!isset($_SESSION['login'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['password'] ?? '') === ADMIN_PASSWORD) {
        $_SESSION['login'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
    echo '<style>body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:linear-gradient(135deg,#00c6ff,#0072ff);color:white;}form{background:white;color:black;padding:30px;border-radius:10px;box-shadow:0 0 15px rgba(0,0,0,0.2);}input,button{padding:10px;margin:10px 0;width:100%;border:none;border-radius:5px;}button{background:#0072ff;color:white;font-weight:bold;}</style><form method="POST"><h2>🔐 Đăng nhập Admin</h2><input type="password" name="password" placeholder="Nhập mật khẩu" required><button type="submit">Đăng nhập</button></form>';
    exit;
}

// Thêm key mới
if (isset($_POST['add_key'])) {
    $key = trim($_POST['key']);
    $duration = $_POST['duration'] ?? '';
    $now = (new DateTime("now", new DateTimeZone("Asia/Ho_Chi_Minh")))->getTimestamp();

    $durations = [
        "30p" => 1800,
        "1 ngày" => 86400,
        "3 ngày" => 86400 * 3,
        "1 tuần" => 86400 * 7,
        "30 ngày" => 86400 * 30,
        "1 tháng" => 86400 * 30,
        "2 tháng" => 86400 * 60,
        "1 năm" => 86400 * 365
    ];

    $lower_duration = strtolower(trim($duration));
    if ($lower_duration === "vĩnh viễn") {
        $expire = 0;
    } else {
        $expire = $durations[$duration] ?? 0;
        $expire = $expire > 0 ? $now + $expire : 0;
    }

    $data = read_keys();
    $data[] = [
        "key" => $key,
        "expire" => $expire
    ];
    save_keys($data);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Xóa key (truy cập ?delete=INDEX)
if (isset($_GET['delete'])) {
    $index = intval($_GET['delete']);
    $data = read_keys();
    if (isset($data[$index])) {
        unset($data[$index]);
        $data = array_values($data);
        save_keys($data);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ví dụ: Hiển thị danh sách key với expire đọc được (bạn có thể dùng ở đâu cần)
if (isset($_SESSION['login'])) {
    $keys_with_readable = read_keys_readable();
    echo '<h3>Danh sách key hiện tại:</h3><ul>';
    foreach ($keys_with_readable as $i => $item) {
        echo '<li>';
        echo 'Key: ' . htmlspecialchars($item['key']) . ' - Hết hạn: ' . htmlspecialchars($item['expire_readable']);
        echo ' <a href="?delete=' . $i . '" onclick="return confirm(\'Bạn chắc chắn muốn xóa key này?\')">[Xóa]</a>';
        echo '</li>';
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Key Tool</title>
<style>
body{font-family:'Segoe UI',sans-serif;margin:0;padding:20px;background:linear-gradient(135deg,#00c6ff,#0072ff);color:white;}
h2{margin-top:0;}
.box{background:rgba(255,255,255,0.1);padding:20px;border-radius:12px;margin-bottom:20px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
input,select,button{width:100%;padding:10px;margin:5px 0 10px 0;border-radius:5px;border:none;}
button{background:#004d99;color:white;font-weight:bold;cursor:pointer;}
table{width:100%;background:white;color:black;border-collapse:collapse;border-radius:8px;overflow:hidden;}
th,td{padding:10px;border-bottom:1px solid #ddd;}
tr:hover{background-color:#f2f2f2;}
.delete-btn{color:red;font-weight:bold;text-decoration:none;}
</style>
</head>
<body>

<div class="box">
<h2>➕ Thêm Key Mới</h2>
<form method="POST">
<input type="text" name="key" placeholder="Nhập key" required>
<select name="duration">
<option value="30p">30 phút</option>
<option value="1 ngày">1 ngày</option>
<option value="3 ngày">3 ngày</option>
<option value="1 tuần">1 tuần</option>
<option value="30 ngày">30 ngày</option>
<option value="1 tháng">1 tháng</option>
<option value="2 tháng">2 tháng</option>
<option value="1 năm">1 năm</option>
<option value="vĩnh viễn">Vĩnh viễn</option>
</select>
<button type="submit" name="add_key">Thêm Key</button>
</form>
</div>

<div class="box">
<h2>📋 Danh sách Key</h2>
<table>
<tr><th>#</th><th>Key</th><th>Hạn dùng</th><th>Hành động</th></tr>
<?php
$keys = read_keys();
$now = time();
$valid_keys = [];
foreach ($keys as $item) {
    if ($item['expire'] == 0 || $item['expire'] > $now) $valid_keys[] = $item;
}
if (count($valid_keys) !== count($keys)) save_keys($valid_keys);
foreach ($valid_keys as $i => $item) {
    $expire = $item['expire'] == 0 ? 'Vĩnh viễn' : date("d/m/Y H:i", $item['expire']);
    echo "<tr><td>$i</td><td>{$item['key']}</td><td>$expire</td><td><a class='delete-btn' href='?delete=$i' onclick='return confirm(\"Bạn muốn xoá key này?\")'>Xoá</a></td></tr>";
}
?>
</table>
</div>

<div class="box">
<h2>📬 Đơn nạp chờ duyệt</h2>
<table>
<tr>
  <th>#</th><th>Tài khoản</th><th>Thời gian</th><th>Loại thẻ</th><th>Mệnh giá</th>
  <th>Serial</th><th>Mã thẻ</th><th>Trạng thái</th><th>Ghi chú</th><th>Hành động</th>
</tr>
<?php
$list = read_file(NAP_FILE);
foreach ($list as $i => $d) {
    $user = $d['user'] ?? 'N/A';
    $trangthai = $d['trangthai'] ?? 'Chờ duyệt';
    $color = $trangthai === 'Đã duyệt' ? 'green' : ($trangthai === 'Từ chối' ? 'red' : 'orange');
    $ghichu = $d['ghichu'] ?? '';
    echo "<tr>
        <td>$i</td>
        <td>$user</td>
        <td>{$d['thoigian']}</td>
        <td>{$d['loaithe']}</td>
        <td>" . number_format($d['menhgia'], 0, ',', '.') . "đ</td>
        <td>{$d['seri']}</td>
        <td>{$d['mathe']}</td>
        <td style='color:$color;font-weight:bold;'>$trangthai</td>
        <td>$ghichu</td>
        <td>
            <form method='POST' action='?action=approve&id=$i' style='display:inline; margin-bottom:5px;'>
                <input name='ghichu' placeholder='Nội dung gửi' required>
                <button onclick=\"return confirm('Duyệt đơn này?')\">✅</button>
            </form><br>
            <form method='POST' action='?action=reject&id=$i' style='display:inline; margin-bottom:5px;'>
                <input name='ghichu' placeholder='Lý do từ chối' required>
                <button onclick=\"return confirm('Từ chối đơn này?')\">❌</button>
            </form><br>
            <form method='GET' action='' style='display:inline;'>
                <input type='hidden' name='action' value='delete'>
                <input type='hidden' name='id' value='$i'>
                <button onclick=\"return confirm('Xoá đơn này?')\">🗑</button>
            </form>
        </td>
    </tr>";
}
?>
</table>
</div>

</body>
</html>