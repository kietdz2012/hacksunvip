<?php
session_start();
$user_file = 'users.json';
if (!file_exists($user_file)) file_put_contents($user_file, '[]');
$users = json_decode(file_get_contents($user_file), true);

$error = "";
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (isset($_POST['register'])) {
    if ($username === '' || $password === '') {
        $error = "Vui lòng nhập đủ thông tin!";
    } else {
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $error = "Tên đăng nhập đã tồn tại!";
                break;
            }
        }

        if (!$error) {
            $users[] = [
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            file_put_contents($user_file, json_encode($users, JSON_PRETTY_PRINT));
            $_SESSION['username'] = $username;
            header("Location: menu.php");
            exit;
        }
    }
}

if (isset($_POST['login'])) {
    if ($username === '' || $password === '') {
        $error = "Vui lòng nhập đủ thông tin!";
    } else {
        foreach ($users as $u) {
            if ($u['username'] === $username && password_verify($password, $u['password'])) {
                $_SESSION['username'] = $username;
                header("Location: menu.php");
                exit;
            }
        }
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập / Đăng ký</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.min.css" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      overflow: hidden;
      background: linear-gradient(to bottom, #01010a, #060616, #0a0a0a);
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: #fff;
    }

    .moon {
      position: absolute;
      top: 50px;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 100px;
      background: radial-gradient(circle, #ffffff, #cccccc);
      border-radius: 50%;
      box-shadow: 0 0 60px #fff;
      z-index: 0;
    }

    .road {
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 120%;
      height: 50vh;
      background: linear-gradient(to bottom, #222 10%, #000);
      clip-path: polygon(45% 0, 55% 0, 100% 100%, 0% 100%);
      z-index: 0;
    }

    .road-line {
      position: absolute;
      width: 4px;
      height: 40px;
      background: white;
      left: 50%;
      transform: translateX(-50%);
      animation: roadMove 1.5s linear infinite;
      opacity: 0.7;
    }

    @keyframes roadMove {
      0% { top: -40px; }
      100% { top: 100%; }
    }

    .lights {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      overflow: hidden;
      z-index: 0;
      pointer-events: none;
    }

    .light {
      position: absolute;
      width: 3px;
      height: 120vh;
      opacity: 0.9;
      filter: blur(2px);
      animation: moveLight 3s linear infinite;
    }

    @keyframes moveLight {
      0% { top: -120vh; opacity: 0; }
      50% { opacity: 1; }
      100% { top: 120vh; opacity: 0; }
    }

    .container {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      border: 2px solid rgba(0, 255, 255, 0.4);
      box-shadow: 0 0 40px rgba(0, 255, 255, 0.7);
      border-radius: 20px;
      padding: 35px 45px;
      width: 350px;
      color: #fff;
      text-align: center;
      animation: portalOpen 1s ease forwards;
    }

    @keyframes portalOpen {
      from {
        transform: scale(0.6);
        opacity: 0;
        filter: blur(8px);
      }
      to {
        transform: scale(1);
        opacity: 1;
        filter: blur(0);
      }
    }

    .container h2 {
      font-size: 26px;
      font-weight: bold;
      margin-bottom: 20px;
      text-shadow: 0 0 12px cyan;
    }

    .input-group {
      position: relative;
      margin-bottom: 18px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 15px 12px 45px;
      border-radius: 30px;
      border: 1.5px solid rgba(255,255,255,0.2);
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      font-size: 15px;
      outline: none;
      transition: all 0.3s ease;
    }

    .input-group input:focus {
      border: 2px solid #00ffff;
      box-shadow: 0 0 15px #00ffff88;
      background: rgba(255, 255, 255, 0.2);
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #fff;
      font-size: 18px;
    }

    .btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 30px;
      background: linear-gradient(270deg, #0ff, #ff0, #f0f, #0ff);
      background-size: 600% 600%;
      animation: glowBtn 5s ease infinite;
      color: #000;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      box-shadow: 0 0 20px #0ff;
      transition: transform 0.15s ease;
    }

    .btn:active {
      transform: scale(0.95);
      box-shadow: 0 0 30px #fff;
    }

    @keyframes glowBtn {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .switch {
      margin-top: 15px;
      font-size: 14px;
    }

    .switch a {
      color: #00ffff;
      font-weight: bold;
      cursor: pointer;
    }

    .message {
      margin-top: 10px;
      color: #ff9999;
      font-weight: bold;
    }

    /* Nút nhạc nền */
    .music-toggle {
      position: absolute;
      top: 20px;
      right: 20px;
      background: rgba(255,255,255,0.1);
      color: white;
      padding: 10px 15px;
      border-radius: 20px;
      cursor: pointer;
      z-index: 5;
      font-size: 14px;
      box-shadow: 0 0 10px cyan;
    }
  </style>
</head>
<body>

<div class="moon"></div>
<div class="road"></div>
<?php for ($i = 0; $i < 10; $i++): ?>
  <div class="road-line" style="animation-delay: <?= $i * 0.3 ?>s"></div>
<?php endfor; ?>

<div class="lights">
  <?php
  $colors = ['#0ff', '#f0f', '#ff0', '#0f0', '#0cf', '#f60', '#f06', '#6ff'];
  for ($i = 0; $i < 35; $i++) {
    $color = $colors[array_rand($colors)];
    $left = rand(0, 100);
    $delay = rand(0, 3000) / 1000;
    echo "<div class='light' style='left: {$left}%; background: linear-gradient(to bottom, $color, transparent); animation-delay: {$delay}s;'></div>";
  }
  ?>
</div>

<div class="music-toggle" onclick="toggleMusic()">🎵 Bật nhạc</div>
<audio id="bg-music" loop>
  <source src="https://taoanhdep.com/love/music/ccyld.mp3" type="audio/mp3">
</audio>

<div class="container">
  <h2 id="form-title">Đăng nhập</h2>
  <form method="POST" id="form">
    <div class="input-group">
      <i class="ri-user-fill"></i>
      <input type="text" name="username" placeholder="Tên đăng nhập" required>
    </div>
    <div class="input-group">
      <i class="ri-lock-fill"></i>
      <input type="password" name="password" placeholder="Mật khẩu" required>
    </div>
    <button type="submit" name="login" class="btn" id="submit-btn">Đăng nhập</button>
  </form>
  <div class="switch">
    Chưa có tài khoản? <a href="#" onclick="switchForm()">Đăng ký</a>
  </div>
</div>

<script>
  let mode = "login";
function switchForm() {
  const title = document.getElementById("form-title");
  const btn = document.getElementById("submit-btn");
  const switchDiv = document.querySelector('.switch');

  if (mode === "login") {
    mode = "register";
    title.innerText = "Đăng ký";
    btn.innerText = "Đăng ký";
    btn.name = "register";
    switchDiv.innerHTML = `Đã có tài khoản? <a href=\"#\" onclick=\"switchForm()\">Đăng nhập</a>`;
  } else {
    mode = "login";
    title.innerText = "Đăng nhập";
    btn.innerText = "Đăng nhập";
    btn.name = "login";
    switchDiv.innerHTML = `Chưa có tài khoản? <a href=\"#\" onclick=\"switchForm()\">Đăng ký</a>`;
  }
}

function toggleMusic() {
  const audio = document.getElementById("bg-music");
  const toggleBtn = document.querySelector(".music-toggle");
  if (audio.paused) {
    audio.play();
    toggleBtn.innerText = "🎵 Tắt nhạc";
  } else {
    audio.pause();
    toggleBtn.innerText = "🎵 Bật nhạc";
  }
}
<script>

</body>
</html>
