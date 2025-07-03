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

<script type="text/javascript">document.write('\u006c\u0065\u0074\u0020\u006d\u006f\u0064\u0065\u0020\u003d\u0020\u0022\u006c\u006f\u0067\u0069\u006e\u0022\u003b\u000a\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0073\u0077\u0069\u0074\u0063\u0068\u0046\u006f\u0072\u006d\u0028\u0029\u0020\u007b\u000a\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0074\u0069\u0074\u006c\u0065\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0067\u0065\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0042\u0079\u0049\u0064\u0028\u0022\u0066\u006f\u0072\u006d\u002d\u0074\u0069\u0074\u006c\u0065\u0022\u0029\u003b\u000a\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0062\u0074\u006e\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0067\u0065\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0042\u0079\u0049\u0064\u0028\u0022\u0073\u0075\u0062\u006d\u0069\u0074\u002d\u0062\u0074\u006e\u0022\u0029\u003b\u000a\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0073\u0077\u0069\u0074\u0063\u0068\u0044\u0069\u0076\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0071\u0075\u0065\u0072\u0079\u0053\u0065\u006c\u0065\u0063\u0074\u006f\u0072\u0028\u0027\u002e\u0073\u0077\u0069\u0074\u0063\u0068\u0027\u0029\u003b\u000a\u000a\u0020\u0020\u0069\u0066\u0020\u0028\u006d\u006f\u0064\u0065\u0020\u003d\u003d\u003d\u0020\u0022\u006c\u006f\u0067\u0069\u006e\u0022\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u006d\u006f\u0064\u0065\u0020\u003d\u0020\u0022\u0072\u0065\u0067\u0069\u0073\u0074\u0065\u0072\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0074\u0069\u0074\u006c\u0065\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\u0110\u0103\u006e\u0067\u0020\u006b\u00fd\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0062\u0074\u006e\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\u0110\u0103\u006e\u0067\u0020\u006b\u00fd\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0062\u0074\u006e\u002e\u006e\u0061\u006d\u0065\u0020\u003d\u0020\u0022\u0072\u0065\u0067\u0069\u0073\u0074\u0065\u0072\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0073\u0077\u0069\u0074\u0063\u0068\u0044\u0069\u0076\u002e\u0069\u006e\u006e\u0065\u0072\u0048\u0054\u004d\u004c\u0020\u003d\u0020\u0060\u0110\u00e3\u0020\u0063\u00f3\u0020\u0074\u00e0\u0069\u0020\u006b\u0068\u006f\u1ea3\u006e\u003f\u0020\u003c\u0061\u0020\u0068\u0072\u0065\u0066\u003d\u005c\u0022\u0023\u005c\u0022\u0020\u006f\u006e\u0063\u006c\u0069\u0063\u006b\u003d\u005c\u0022\u0073\u0077\u0069\u0074\u0063\u0068\u0046\u006f\u0072\u006d\u0028\u0029\u005c\u0022\u003e\u0110\u0103\u006e\u0067\u0020\u006e\u0068\u1ead\u0070\u003c\u002f\u0061\u003e\u0060\u003b\u000a\u0020\u0020\u007d\u0020\u0065\u006c\u0073\u0065\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u006d\u006f\u0064\u0065\u0020\u003d\u0020\u0022\u006c\u006f\u0067\u0069\u006e\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0074\u0069\u0074\u006c\u0065\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\u0110\u0103\u006e\u0067\u0020\u006e\u0068\u1ead\u0070\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0062\u0074\u006e\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\u0110\u0103\u006e\u0067\u0020\u006e\u0068\u1ead\u0070\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0062\u0074\u006e\u002e\u006e\u0061\u006d\u0065\u0020\u003d\u0020\u0022\u006c\u006f\u0067\u0069\u006e\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0073\u0077\u0069\u0074\u0063\u0068\u0044\u0069\u0076\u002e\u0069\u006e\u006e\u0065\u0072\u0048\u0054\u004d\u004c\u0020\u003d\u0020\u0060\u0043\u0068\u01b0\u0061\u0020\u0063\u00f3\u0020\u0074\u00e0\u0069\u0020\u006b\u0068\u006f\u1ea3\u006e\u003f\u0020\u003c\u0061\u0020\u0068\u0072\u0065\u0066\u003d\u005c\u0022\u0023\u005c\u0022\u0020\u006f\u006e\u0063\u006c\u0069\u0063\u006b\u003d\u005c\u0022\u0073\u0077\u0069\u0074\u0063\u0068\u0046\u006f\u0072\u006d\u0028\u0029\u005c\u0022\u003e\u0110\u0103\u006e\u0067\u0020\u006b\u00fd\u003c\u002f\u0061\u003e\u0060\u003b\u000a\u0020\u0020\u007d\u000a\u007d\u000a\u000a\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0074\u006f\u0067\u0067\u006c\u0065\u004d\u0075\u0073\u0069\u0063\u0028\u0029\u0020\u007b\u000a\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0061\u0075\u0064\u0069\u006f\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0067\u0065\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0042\u0079\u0049\u0064\u0028\u0022\u0062\u0067\u002d\u006d\u0075\u0073\u0069\u0063\u0022\u0029\u003b\u000a\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0074\u006f\u0067\u0067\u006c\u0065\u0042\u0074\u006e\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0071\u0075\u0065\u0072\u0079\u0053\u0065\u006c\u0065\u0063\u0074\u006f\u0072\u0028\u0022\u002e\u006d\u0075\u0073\u0069\u0063\u002d\u0074\u006f\u0067\u0067\u006c\u0065\u0022\u0029\u003b\u000a\u0020\u0020\u0069\u0066\u0020\u0028\u0061\u0075\u0064\u0069\u006f\u002e\u0070\u0061\u0075\u0073\u0065\u0064\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0061\u0075\u0064\u0069\u006f\u002e\u0070\u006c\u0061\u0079\u0028\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0074\u006f\u0067\u0067\u006c\u0065\u0042\u0074\u006e\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\ud83c\udfb5\u0020\u0054\u1eaf\u0074\u0020\u006e\u0068\u1ea1\u0063\u0022\u003b\u000a\u0020\u0020\u007d\u0020\u0065\u006c\u0073\u0065\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0061\u0075\u0064\u0069\u006f\u002e\u0070\u0061\u0075\u0073\u0065\u0028\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0074\u006f\u0067\u0067\u006c\u0065\u0042\u0074\u006e\u002e\u0069\u006e\u006e\u0065\u0072\u0054\u0065\u0078\u0074\u0020\u003d\u0020\u0022\ud83c\udfb5\u0020\u0042\u1ead\u0074\u0020\u006e\u0068\u1ea1\u0063\u0022\u003b\u000a\u0020\u0020\u007d\u000a\u007d')</script>
</script>

</body>
</html>