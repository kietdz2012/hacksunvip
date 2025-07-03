<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bảng Giá Dịch Vụ</title>
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
    /* Main content */
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
    .main.shifted {
      margin-left: 220px;
    }
    /* Header */
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
    /* Menu toggle button */
    .menu-toggle {
      font-size: 28px;
      color: #f4b41a;
      cursor: pointer;
      user-select: none;
      padding: 5px 10px;
      border-radius: 6px;
      transition: background 0.3s;
    }
    .menu-toggle:hover {
      background: #f4b41a33;
    }
    /* Footer */
    footer {
      background: #1e1e1e;
      color: #f4b41a;
      text-align: center;
      padding: 12px 20px;
      font-size: 14px;
      letter-spacing: 1.5px;
      box-shadow: 0 -2px 8px rgba(244, 180, 26, 0.5);
      user-select: none;
    }
    
    /* Bảng giá */
    .pricing-section {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }
    
    .pricing-card {
      background: #1e1e1e;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      border-left: 4px solid #f4b41a;
    }
    
    .pricing-title {
      font-size: 24px;
      color: #f4b41a;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .pricing-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .pricing-table th {
      background: #f4b41a;
      color: #121212;
      padding: 12px;
      text-align: left;
    }
    
    .pricing-table td {
      padding: 12px;
      border-bottom: 1px solid #333;
    }
    
    .pricing-table tr:last-child td {
      border-bottom: none;
    }
    
    .pricing-table tr:hover {
      background: #252525;
    }
    
    .highlight {
      font-weight: bold;
      color: #f4b41a;
    }
    
    .contact-info {
      margin-top: 40px;
      background: #1e1e1e;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      border-top: 2px solid #f4b41a;
    }
    
    .contact-info h3 {
      color: #f4b41a;
      margin-bottom: 15px;
    }
    
    .contact-links {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    
    .contact-link {
      background: #f4b41a;
      color: #121212;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: transform 0.3s;
    }
    
    .contact-link:hover {
      transform: translateY(-3px);
    }
    
    .emoji {
      font-size: 20px;
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
      <div class="logo">BẢNG GIÁ DỊCH VỤ</div>
    </header>

    <div class="pricing-section">
      <!-- Tool Sunwin -->
      <div class="pricing-card">
        <h2 class="pricing-title">
          <span class="emoji">🔧</span> TOOL SUNWIN <span class="emoji">🎮</span>
        </h2>
        <table class="pricing-table">
          <thead>
            <tr>
              <th>Thời gian</th>
              <th>Giá tiền</th>
              <th>...</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="emoji">⏳</span> 1 ngày</td>
              <td class="highlight">30.000đ</td>
              <td><span class="emoji">🎁</span> Hỗ trợ 24/7</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 3 ngày</td>
              <td class="highlight">60.000đ</td>
              <td><span class="emoji">🎁</span> Giảm còn 20/ngày</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tuần</td>
              <td class="highlight">120.000đ</td>
              <td><span class="emoji">🎁</span>Giảm hơn 80k so vs giá gốc</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tháng</td>
              <td class="highlight">200.000đ</td>
              <td><span class="emoji">🎁</span> Giảm 40%</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 2 tháng</td>
              <td class="highlight">250.000đ</td>
              <td><span class="emoji">🎁</span> Giảm 80%</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Tool 789 -->
      <div class="pricing-card">
        <h2 class="pricing-title">
          <span class="emoji">🔧</span> TOOL 789 <span class="emoji">🎲</span>
        </h2>
        <table class="pricing-table">
          <thead>
            <tr>
              <th>Thời gian</th>
              <th>Giá tiền</th>
              <th>Khuyến mãi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="emoji">⏳</span> 1 ngày</td>
              <td class="highlight">Mở bán</td>
              <td><span class="emoji">🎁</span> Hỗ trợ 24/7</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 3 ngày</td>
              <td class="highlight">Mở bán</td>
              <td><span class="emoji">🎁</span> Giảm 17%</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tuần</td>
              <td class="highlight">80,000đ</td>
              <td><span class="emoji">🎁</span> Giảm 25%</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tháng</td>
              <td class="highlight">Mở bán</td>
              <td><span class="emoji">🎁</span> Giảm 42%</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Tool MD5 -->
      <div class="pricing-card">
        <h2 class="pricing-title">
          <span class="emoji">🔧</span> TOOL MD5 <span class="emoji">🔐</span>
        </h2>
        <table class="pricing-table">
          <thead>
            <tr>
              <th>Thời gian</th>
              <th>Giá tiền</th>
              <th>Khuyến mãi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="emoji">⏳</span> 1 ngày</td>
              <td class="highlight">20.000đ</td>
              <td><span class="emoji">🎁</span> Hỗ trợ 24/7</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 3 ngày</td>
              <td class="highlight">40.000đ</td>
              <td><span class="emoji">🎁</span> Giảm 11%</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tuần</td>
              <td class="highlight">80.000đ</td>
              <td><span class="emoji">🎁</span> Giảm 29%</td>
            </tr>
            <tr>
              <td><span class="emoji">⏳</span> 1 tháng</td>
              <td class="highlight">120.000đ</td>
              <td><span class="emoji">🎁</span> Giảm 38%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <div class="contact-info">
      <h3><span class="emoji">📩</span> LIÊN HỆ MUA TOOL <span class="emoji">💌</span></h3>
      <div class="contact-links">
        <a href="https://t.me/sharetooltxfreevn123" class="contact-link" target="_blank">
          <span class="emoji">📨</span> Telegram
        </a>
        <a href="https://www.facebook.com/nguyen.tuan.kiet.128560" class="contact-link" target="_blank">
          <span class="emoji">👤</span> Facebook
        </a>
      </div>
      <p style="margin-top: 15px; color: #aaa;"><span class="emoji">🛡️</span> Uy tín - Chất lượng - Bảo mật <span class="emoji">🔒</span></p>
    </div>
  </main>

  <footer>
    © 2025 Bảng Giá Dịch Vụ <span class="emoji">🤖</span>
  </footer>
</body>
</html>