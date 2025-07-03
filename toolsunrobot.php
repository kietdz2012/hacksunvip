<?php
session_start();
$keyFile = 'keys.json';

// ✅ Hàm kiểm tra key hợp lệ
function isKeyValid($userKey, $keyFile) {
    if (!file_exists($keyFile)) return false;
    $data = json_decode(file_get_contents($keyFile), true);
    foreach ($data as $item) {
        if ($item['key'] === $userKey) {
            return ($item['expire'] == 0 || time() < $item['expire']);
        }
    }
    return false;
}

// ✅ Nếu là AJAX gọi dữ liệu JSON
if (
    isset($_SERVER['HTTP_ACCEPT']) &&
    strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
) {
    // ✅ Kiểm tra quyền truy cập bằng session
    if (!isset($_SESSION['key_valid']) || $_SESSION['key_valid'] !== true || !isKeyValid($_SESSION['input_key'], $keyFile)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Bạn không có quyền truy cập']);
        exit;
    }

    // ✅ Gọi API thật tại đây (ẩn key trong PHP)
    $apiKey = '28102012';
    $apiUrl = 'https://chayseversunwin-production.up.railway.app/api/sunwin?key=' . urlencode($apiKey);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // ✅ Trả về dữ liệu hoặc lỗi
    header('Content-Type: application/json');
    echo $response ?: json_encode(['error' => 'Lỗi kết nối API: ' . $error]);
    exit;
}

// ✅ Nếu không phải JSON (người dùng truy cập trực tiếp) thì bắt buộc đăng nhập key
if (!isset($_SESSION['key_valid']) || $_SESSION['key_valid'] !== true || !isKeyValid($_SESSION['input_key'], $keyFile)) {
    header("Location: nhapkey.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Robot AI - Tài Xỉu</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <style>
    body { margin: 0; background: #000; font-family: 'Poppins', sans-serif; overflow: hidden; }
    iframe { position: absolute; top: 0; left: 0; width: 100vw; height: 100vh; border: none; z-index: 0; }
    #robotContainer {
      position: absolute; top: 100px; left: 50px; z-index: 9999; cursor: move; user-select: none;
      display: flex; align-items: flex-start; gap: 15px;
    }
    .robotIconWrapper { width: 100px; height: 100px; }
    #robotIcon { width: 100%; height: 100%; object-fit: contain; }
    #robotText {
      background: rgba(40, 167, 69, 0.15); border: 2px solid #28a745; border-radius: 15px;
      padding: 10px 15px; color: #28a745; font-size: 14px; backdrop-filter: blur(8px);
      box-shadow: 0 0 8px #28a745aa; max-width: 280px; margin-top: -10px; text-align: left;
    }
  </style>
</head>
<body>

<script>
// 🚫 Chống mở DevTools
(function detectDevTools() {
  const threshold = 100;
  let checkInterval = setInterval(() => {
    const start = performance.now();
    debugger;
    const time = performance.now() - start;
    if (time > threshold) {
      document.body.innerHTML = `
        <div style="color: red; font-size: 22px; text-align: center; padding-top: 100px; font-weight: bold;">
          🚫 Phát hiện Developer Tools đang bật!<br>
          Hệ thống đã ngừng hoạt động để bảo vệ API.<br>
          Vui lòng tắt DevTools và tải lại trang.
        </div>
      `;
      let highest = setInterval(() => {}, 9999);
      for (let i = 0; i <= highest; i++) clearInterval(i);
      document.querySelectorAll("*").forEach(el => el.remove());
      clearInterval(checkInterval);
      throw new Error("DevTools detected");
    }
  }, 1000);
})();
</script>

<iframe id="gameFrame" src="https://web.sun.win/" allow="clipboard-write *; autoplay *" style="pointer-events: auto;"></iframe>

<div id="robotContainer">
  <div class="robotIconWrapper">
    <img id="robotIcon" src="robot.png" alt="Robot AI" />
  </div>
  <div id="robotText">
    <div>🤖 Dự đoán: <span id="du_doan">Đang tải...</span></div>
    <div>🎯 Độ tin cậy: <span id="do_tin_cay">--%</span></div>
    <div>🎲 Kết quả: <span id="ket_qua">---</span> [<span id="dice">--</span>]</div>
    <div>📈 Cầu: <span id="cau">--</span></div>
    <div>🆔 Phiên: <span id="phien">--</span></div>
    <div>🕒 Thời gian: <span id="thoigian">--</span></div>
  </div>
</div>

<script>
const apiUrl = location.href; // Chính file này sẽ trả về JSON

let lastResult = null;
let isAnalyzing = false;

async function fetchData() {
  try {
    const res = await fetch(apiUrl, {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();

    if (data.error) {
      document.getElementById('du_doan').textContent = '❌ ' + data.error;
      return;
    }

    if (data.ket_qua !== lastResult && !isAnalyzing) {
      isAnalyzing = true;
      document.getElementById('du_doan').textContent = '⏳ Đang phân tích dữ liệu...';
      setTimeout(() => {
        updateUI(data);
        lastResult = data.ket_qua;
        isAnalyzing = false;
      }, 5000);
    } else if (!isAnalyzing) {
      updateUI(data);
    }
  } catch (e) {
    document.getElementById('du_doan').textContent = '⚠️ Không kết nối được máy chủ';
    console.error('Lỗi:', e);
  }
}

function updateUI(data) {
  document.getElementById('du_doan').textContent = data.du_doan || 'Đang cập nhật';
  document.getElementById('do_tin_cay').textContent = data.do_tin_cay || '--%';
  document.getElementById('ket_qua').textContent = data.ket_qua || '---';
  document.getElementById('dice').textContent = (data.Dice || []).join(', ') || '--';
  document.getElementById('phien').textContent = data.phien_hien_tai || '--';
  document.getElementById('cau').textContent = data.cau || '--';
  document.getElementById('thoigian').textContent = data.ngay || '--';
}

fetchData();
setInterval(fetchData, 20000);

// Kéo thả
const robot = document.getElementById("robotContainer");
let isDragging = false;
let offsetX, offsetY;

robot.addEventListener("mousedown", function (e) {
  isDragging = true;
  offsetX = e.clientX - robot.offsetLeft;
  offsetY = e.clientY - robot.offsetTop;
  document.getElementById("gameFrame").style.pointerEvents = "none";
});
document.addEventListener("mousemove", function (e) {
  if (isDragging) {
    robot.style.left = (e.clientX - offsetX) + "px";
    robot.style.top = (e.clientY - offsetY) + "px";
  }
});
document.addEventListener("mouseup", function () {
  isDragging = false;
  document.getElementById("gameFrame").style.pointerEvents = "auto";
});
</script>
</body>
</html>