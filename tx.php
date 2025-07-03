<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Prediction Menu</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            overflow-x: hidden;
            touch-action: none;
        }
        
        #game-iframe {
            width: 100%;
            height: 100vh;
            border: none;
            display: block;
        }
        
        #floating-menu {
            position: fixed;
            top: 50px;
            right: 20px;
            width: 300px;
            background: rgba(40, 40, 40, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            overflow: hidden;
            color: white;
            user-select: none;
            -webkit-user-select: none;
        }
        
        #menu-header {
            background: #2a2a2a;
            padding: 10px;
            cursor: move;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        #menu-title {
            font-weight: bold;
        }
        
        #toggle-menu {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            padding: 5px;
        }
        
        #menu-content {
            padding: 15px;
            transition: all 0.3s;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #444;
            margin-bottom: 10px;
        }
        
        .tab-btn {
            flex: 1;
            padding: 8px;
            background: #333;
            border: none;
            color: white;
            cursor: pointer;
            text-align: center;
            border-right: 1px solid #444;
        }
        
        .tab-btn:last-child {
            border-right: none;
        }
        
        .tab-btn.active {
            background: #555;
        }
        
        .input-group {
            margin-bottom: 15px;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .input-group input, .input-group textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #555;
            background: #333;
            color: white;
            box-sizing: border-box;
        }
        
        .input-group textarea {
            min-height: 60px;
            resize: vertical;
        }
        
        .predict-btn {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .result-box {
            padding: 10px;
            background: #333;
            border-radius: 4px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            min-height: 20px;
        }
        
        .collapsed {
            height: 40px !important;
        }
        
        .collapsed #menu-content {
            display: none;
        }
        
        @media (max-width: 768px) {
            #floating-menu {
                width: 280px;
                right: 10px;
            }
        }
    </style>
</head>
<body>
    <iframe id="game-iframe" src="https://play.sun.win" allowfullscreen="false"></iframe>
    
    <div id="floating-menu">
        <div id="menu-header">
            <span id="menu-title">Game Prediction Menu</span>
            <button id="toggle-menu">−</button>
        </div>
        <div id="menu-content">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="tx-normal">TX Thường</button>
                <button class="tab-btn" data-tab="sicbo">Sicbo</button>
                <button class="tab-btn" data-tab="tx-livestream">TX Livestream</button>
            </div>
            
            <!-- TX Thường Tab -->
            <div id="tx-normal" class="tab-content active">
                <div class="input-group">
                    <label for="tx-input">Nhập chuỗi tài/xỉu (ví dụ: TXTXXT):</label>
                    <textarea id="tx-input" placeholder="Nhập chuỗi tài (T) xỉu (X)"></textarea>
                </div>
                <button class="predict-btn" id="predict-tx">Dự đoán phiên tiếp theo</button>
                <div class="result-box" id="tx-result"></div>
            </div>
            
            <!-- Sicbo Tab -->
            <div id="sicbo" class="tab-content">
                <div class="input-group">
                    <label for="sicbo-md5">Nhập mã MD5:</label>
                    <input type="text" id="sicbo-md5" placeholder="Nhập mã MD5">
                </div>
                <button class="predict-btn" id="predict-sicbo">Phân tích phiên tiếp theo</button>
                <div class="result-box" id="sicbo-result"></div>
            </div>
            
            <!-- TX Livestream Tab -->
            <div id="tx-livestream" class="tab-content">
                <div class="input-group">
                    <label for="dice-input">Nhập 3 xúc xắc phiên trước (ví dụ: 1,3,5):</label>
                    <input type="text" id="dice-input" placeholder="Nhập 3 số, cách nhau bằng dấu phẩy">
                </div>
                <button class="predict-btn" id="predict-dice">Dự đoán phiên tiếp theo</button>
                <div class="result-box" id="dice-result"></div>
            </div>
        </div>
    </div>
<a href="index.php" style="position: fixed; top: 10px; left: 10px; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-family: Arial, sans-serif;">← Quay về</a>
    <script>
        // Prevent iframe from going fullscreen
        document.getElementById('game-iframe').allowFullscreen = false;
        
        // Menu functionality
        const menu = document.getElementById('floating-menu');
        const menuHeader = document.getElementById('menu-header');
        const toggleBtn = document.getElementById('toggle-menu');
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        // Make menu draggable
        let isDragging = false;
        let offsetX, offsetY;
        
        menuHeader.addEventListener('mousedown', startDrag);
        menuHeader.addEventListener('touchstart', startDrag);
        
        function startDrag(e) {
            isDragging = true;
            if (e.type === 'mousedown') {
                offsetX = e.clientX - menu.getBoundingClientRect().left;
                offsetY = e.clientY - menu.getBoundingClientRect().top;
            } else {
                const touch = e.touches[0];
                offsetX = touch.clientX - menu.getBoundingClientRect().left;
                offsetY = touch.clientY - menu.getBoundingClientRect().top;
            }
            
            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag);
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchend', stopDrag);
        }
        
        function drag(e) {
            if (!isDragging) return;
            
            e.preventDefault();
            
            let clientX, clientY;
            if (e.type === 'mousemove') {
                clientX = e.clientX;
                clientY = e.clientY;
            } else {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            }
            
            const x = clientX - offsetX;
            const y = clientY - offsetY;
            
            // Boundary checks
            const maxX = window.innerWidth - menu.offsetWidth;
            const maxY = window.innerHeight - menu.offsetHeight;
            
            menu.style.left = `${Math.min(Math.max(0, x), maxX)}px`;
            menu.style.top = `${Math.min(Math.max(0, y), maxY)}px`;
            menu.style.right = 'auto';
        }
        
        function stopDrag() {
            isDragging = false;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('touchmove', drag);
        }
        
        // Toggle menu collapse/expand
        toggleBtn.addEventListener('click', () => {
            menu.classList.toggle('collapsed');
            toggleBtn.textContent = menu.classList.contains('collapsed') ? '+' : '−';
        });
        
        // Tab switching
        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.getAttribute('data-tab');
                
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                btn.classList.add('active');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Prediction functions
        document.getElementById('predict-tx').addEventListener('click', () => {
            const input = document.getElementById('tx-input').value.trim().toUpperCase();
            if (!input) {
                document.getElementById('tx-result').textContent = 'Vui lòng nhập chuỗi tài/xỉu';
                return;
            }
            
            // Simple prediction algorithm (replace with your actual algorithm)
            let tCount = 0, xCount = 0;
            for (let char of input) {
                if (char === 'T') tCount++;
                else if (char === 'X') xCount++;
            }
            
            const prediction = tCount > xCount ? 'XỈU (X)' : 
                              xCount > tCount ? 'TÀI (T)' : 
                              Math.random() > 0.5 ? 'TÀI (T)' : 'XỈU (X)';
            
            document.getElementById('tx-result').textContent = `Dự đoán: ${prediction}`;
        });
        
        document.getElementById('predict-sicbo').addEventListener('click', () => {
            const md5 = document.getElementById('sicbo-md5').value.trim();
            if (!md5) {
                document.getElementById('sicbo-result').textContent = 'Vui lòng nhập mã MD5';
                return;
            }
            
            // Simple prediction based on MD5 (replace with your actual algorithm)
            const hashValue = parseInt(md5.replace(/[^0-9]/g, '').slice(0, 8)) || 0;
            const prediction = hashValue % 2 === 0 ? 'TÀI' : 'XỈU';
            
            document.getElementById('sicbo-result').textContent = `Phân tích: ${prediction}`;
        });
        
        document.getElementById('predict-dice').addEventListener('click', () => {
            const diceInput = document.getElementById('dice-input').value.trim();
            if (!diceInput) {
                document.getElementById('dice-result').textContent = 'Vui lòng nhập 3 xúc xắc';
                return;
            }
            
            const dice = diceInput.split(',').map(num => parseInt(num.trim()));
            if (dice.length !== 3 || dice.some(isNaN)) {
                document.getElementById('dice-result').textContent = 'Vui lòng nhập 3 số hợp lệ';
                return;
            }
            
            // Simulate 8 random rolls and analyze
            let taiCount = 0, xiuCount = 0, evenCount = 0, oddCount = 0;
            
            for (let i = 0; i < 8; i++) {
                const roll1 = Math.floor(Math.random() * 6) + 1;
                const roll2 = Math.floor(Math.random() * 6) + 1;
                const roll3 = Math.floor(Math.random() * 6) + 1;
                
                const sum = roll1 + roll2 + roll3;
                
                if (sum >= 11) taiCount++;
                else xiuCount++;
                
                if (sum % 2 === 0) evenCount++;
                else oddCount++;
            }
            
            const taiPred = taiCount > xiuCount ? 'TÀI' : xiuCount > taiCount ? 'XỈU' : 'NGANG';
            const evenPred = evenCount > oddCount ? 'CHẴN' : oddCount > evenCount ? 'LẺ' : 'NGANG';
            
            document.getElementById('dice-result').textContent = 
                `Dự đoán: ${taiPred} / ${evenPred} (Tài:${taiCount}, Xỉu:${xiuCount}, Chẵn:${evenCount}, Lẻ:${oddCount})`;
        });
    </script>
</body>
</html>