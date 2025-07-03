<?php
session_start();

$keyFile = 'keys.json'; // Tên file chứa danh sách key
$message = ''; // Biến chứa thông báo

// Hàm kiểm tra key có tồn tại và còn hạn hay không
function checkKey($inputKey, $keyFile) {
    if (!file_exists($keyFile)) return false;
    $data = json_decode(file_get_contents($keyFile), true);

    foreach ($data as $item) {
        if ($item['key'] === $inputKey) {
            return ($item['expire'] == 0 || time() < $item['expire']);
        }
    }
    return false;
}

// Xử lý khi người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputKey = trim($_POST['key']);

    if (checkKey($inputKey, $keyFile)) {
        $_SESSION['key_valid'] = true;
        $_SESSION['input_key'] = $inputKey;
        header("Location: toolsunrobot.php");
        exit;
    } else {
        $message = "❌ Key không hợp lệ hoặc đã hết hạn.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Nhập Key</title>
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      height: 100vh;
      background: radial-gradient(ellipse at bottom, #000, #050b15);
      font-family: 'Segoe UI', sans-serif;
      overflow: hidden;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
    }

    /* Hiệu ứng portal xoay */
    body::before {
      content: '';
      position: absolute;
      width: 200vmax;
      height: 200vmax;
      background: conic-gradient(from 0deg, #0ff, #f0f, #ff0, #0ff);
      animation: spin 12s linear infinite;
      filter: blur(100px);
      opacity: 0.15;
      z-index: 0;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    form {
      position: relative;
      background: rgba(255, 255, 255, 0.05);
      padding: 40px 50px;
      border-radius: 20px;
      border: 2px solid rgba(0, 255, 255, 0.4);
      backdrop-filter: blur(15px);
      box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
      width: 360px;
      z-index: 2;
      text-align: center;
    }

    h2 {
      color: #00ffff;
      margin-bottom: 25px;
      font-size: 2rem;
      text-shadow: 0 0 12px cyan;
    }

    input[type="text"] {
      width: 100%;
      padding: 14px 18px;
      margin-bottom: 20px;
      border-radius: 30px;
      border: 1.5px solid rgba(255,255,255,0.15);
      background: rgba(255,255,255,0.1);
      color: #fff;
      font-size: 1.1rem;
      outline: none;
      transition: 0.3s;
      text-align: center;
    }

    input[type="text"]:focus {
      background: rgba(255,255,255,0.2);
      border-color: #0ff;
      box-shadow: 0 0 15px #0ff88a;
    }

    input[type="submit"] {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 30px;
      background: linear-gradient(270deg, #0ff, #f0f, #ff0, #0ff);
      background-size: 400% 400%;
      animation: glowBtn 6s ease infinite;
      color: #000;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 0 20px #0ff;
      transition: transform 0.15s ease;
    }

    input[type="submit"]:active {
      transform: scale(0.95);
    }

    @keyframes glowBtn {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .message {
      margin-top: 15px;
      font-weight: bold;
      font-size: 1rem;
      text-shadow: 0 0 5px #000;
    }

    .success { color: #00ff99; }
    .error { color: #ff4444; }
  </style>
</head>
<body>

<form method="post">
  <h2>🔑 Nhập Key Truy Cập</h2>
  <input type="text" name="key" placeholder="Nhập key của bạn" required>
  <input type="submit" value="Xác nhận">
  <?php if ($message !== ''): ?>
    <div class="message <?= (strpos($message, 'hợp lệ') !== false) ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>
</form>

</body>
</html>