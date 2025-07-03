<?php
session_start();
if (!isset($_SESSION['username'])) {
  die("Bạn chưa đăng nhập. <a href='index.php'>Đăng nhập</a>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $loaithe = htmlspecialchars($_POST['loaithe']);
  $menhgia = (int)$_POST['menhgia'];
  $seri = htmlspecialchars($_POST['seri']);
  $mathe = htmlspecialchars($_POST['mathe']);
  $thoigian = date("d/m/Y H:i");
  $trangthai = "Chờ duyệt";

  $don_file = 'don_nap.json';
  if (!file_exists($don_file)) file_put_contents($don_file, '[]');
  $list = json_decode(file_get_contents($don_file), true);
  $list[] = [
    'user' => $_SESSION['username'],
    'thoigian' => $thoigian,
    'loaithe' => $loaithe,
    'menhgia' => $menhgia,
    'seri' => $seri,
    'mathe' => $mathe,
    'trangthai' => $trangthai,
    'ghichu' => ''
  ];
  file_put_contents($don_file, json_encode($list, JSON_PRETTY_PRINT));
}
?>
<!-- phần HTML giữ nguyên sau đây -->
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nạp thẻ cào</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      margin: 0;
      background: #121212;
      color: #fff;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .main {
      margin-left: 0;
      padding: 20px 30px 60px;
      flex: 1;
      overflow-y: auto;
      background: #181818;
      display: flex;
      flex-direction: column;
      gap: 25px;
      transition: margin-left 0.3s ease;
      min-height: calc(100vh - 60px);
    }
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #f4b41a;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .header .logo {
      font-size: 28px;
      font-weight: 700;
      color: #f4b41a;
      letter-spacing: 3px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      color: #f4b41a;
    }
    input, select {
      width: 100%;
      padding: 10px;
      background: #2d2d2d;
      border: 1px solid #444;
      color: #fff;
      border-radius: 4px;
    }
    button {
      background: #f4b41a;
      color: #121212;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    .alert {
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
    }
    .alert-success {
      background: #4CAF50;
    }
    .alert-error {
      background: #F44336;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #444;
    }
    th {
      background-color: #f4b41a;
      color: #121212;
    }
    .status-pending {
      color: #FFC107;
    }
    .status-approved {
      color: #4CAF50;
    }
    .status-rejected {
      color: #F44336;
    }
  </style>
</head>
<body>
  <style>
/* CSS cho sidebar */
.sidebar {
  width: 220px;
  background: #1e1e1e;
  padding: 20px;
  position: fixed;
  height: 100%;
  box-shadow: 2px 0 8px rgba(0,0,0,0.7);
  display: flex;
  flex-direction: column;
  gap: 20px;
  transition: transform 0.3s ease;
  z-index: 1000;
  transform: translateX(-100%);
}
.sidebar.visible {
  transform: translateX(0);
}
.sidebar h2 {
  margin: 0 0 30px 0;
  font-size: 24px;
  color: #f4b41a;
  text-align: center;
  letter-spacing: 2px;
}
.sidebar a {
  color: #ccc;
  text-decoration: none;
  font-size: 18px;
  padding: 10px 15px;
  border-radius: 6px;
  transition: background 0.3s, color 0.3s;
  display: flex;
  align-items: center;
  gap: 10px;
}
.sidebar a:hover {
  background: #f4b41a;
  color: #121212;
  cursor: pointer;
}
</style>

<!-- HTML của sidebar -->
<nav class="sidebar" id="sidebar">
  <h2>🎲 GIẢI TRÍ</h2>
  <a href="index.php">🏠 Trang chủ</a>
  <a href="tx.php">📢 Dự đoán Sun Free</a>
  <p>Premium</p>  
  <a href="banggia.php">⚡ Bảng giá</a>
  <a href="napthe.php">⚡ Nạp mua tool</a>
  <a href="sunwin.php">🎲 Tool Sunwin</a>
  <a href="">🎲 Tool 789</a>
  <a href="md5.php">🎲 Tool MD5</a>
</nav>

<script>
// JavaScript cho sidebar
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const main = document.getElementById('main');
  const menuToggle = document.getElementById('menu-toggle');

  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      if (sidebar.classList.contains('visible')) {
        sidebar.classList.remove('visible');
        main.classList.remove('shifted');
      } else {
        sidebar.classList.add('visible');
        main.classList.add('shifted');
      }
    });
  }
});
</script>
  <main class="main" id="main">
    <header class="header">
      <div class="menu-toggle" id="menu-toggle" title="Mở/đóng menu">☰</div>
      <div class="logo">NẠP THẺ CÀO</div>
    </header>

    <div class="content">
            
      <form method="POST">
        <div class="form-group">
          <label for="loaithe">Loại thẻ</label>
          <select id="loaithe" name="loaithe" required>
            <option value="">-- Chọn loại thẻ --</option>
            <option value="Viettel">Viettel</option>
            <option value="Mobifone">Mobifone</option>
            <option value="Vinaphone">Vinaphone</option>
            <option value="Zing">Zing</option>
            <option value="Garena">Garena</option>
          </select>
        </div>

        <div class="form-group">
          <label for="menhgia">Mệnh giá</label>
          <select id="menhgia" name="menhgia" required>
            <option value="">-- Chọn mệnh giá --</option>
            <option value="10000">10,000đ</option>
            <option value="20000">20,000đ</option>
            <option value="50000">50,000đ</option>
            <option value="100000">100,000đ</option>
            <option value="200000">200,000đ</option>
            <option value="500000">500,000đ</option>
          </select>
        </div>

        <div class="form-group">
          <label for="seri">Số seri</label>
          <input type="text" id="seri" name="seri" required placeholder="Nhập số seri thẻ">
        </div>

        <div class="form-group">
          <label for="mathe">Mã thẻ</label>
          <input type="text" id="mathe" name="mathe" required placeholder="Nhập mã thẻ">
        </div>

        <button type="submit">Nạp thẻ</button>
      </form>

      <h3>Lịch sử nạp thẻ</h3>
              <table>
          <thead>
            <tr>
              <th>Thời gian</th>
              <th>Loại thẻ</th>
              <th>Mệnh giá</th>
              <th>Trạng thái</th>
              <th>Ghi chú</th>
  <?php
$don_file = 'don_nap.json';
if (!file_exists($don_file)) file_put_contents($don_file, '[]');
$list = json_decode(file_get_contents($don_file), true);
$username = $_SESSION['username'];

foreach ($list as $don) {
  if ($don['user'] !== $username) continue;

  $color = match($don['trangthai']) {
    'Đã duyệt' => 'green',
    'Từ chối' => 'red',
    default => 'orange'
  };
  echo "<tr>
    <td>{$don['thoigian']}</td>
    <td>{$don['loaithe']}</td>
    <td>" . number_format($don['menhgia'], 0, ',', '.') . "đ</td>
    <td style='color:$color;font-weight:bold;'>{$don['trangthai']}</td>
    <td>{$don['ghichu']}</td>
  </tr>";
}
?>
  </main>

  <footer>
    © 2025 Hệ thống nạp thẻ | Bản quyền thuộc về bạn
  </footer>

  <script>
    // Lưu trạng thái sidebar vào localStorage
    document.getElementById('menu-toggle').addEventListener('click', function() {
      const main = document.getElementById('main');
      main.classList.toggle('shifted');
      localStorage.setItem('sidebarOpen', main.classList.contains('shifted'));
    });

    // Khôi phục trạng thái sidebar khi tải trang
    window.addEventListener('DOMContentLoaded', function() {
      const sidebarOpen = localStorage.getItem('sidebarOpen') === 'true';
      if (sidebarOpen) {
        document.getElementById('main').classList.add('shifted');
      }
    });
  </script>
</body>
</html>