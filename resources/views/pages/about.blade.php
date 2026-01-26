<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu - SHOP VTKT | Future Edition</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Outfit:wght@300;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* --- KHAI BÁO BIẾN MÀU (THEME CONFIG) --- */
        :root {
            --bg-dark: #030712;
            --primary: #6366f1;
            --accent: #06b6d4;
            --highlight: #ffd700;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* --- BACKGROUND EFFECTS --- */
        .bg-grid {
            position: fixed;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background-image: 
                linear-gradient(rgba(99, 102, 241, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            transform: perspective(500px) rotateX(60deg);
            animation: grid-move 20s linear infinite;
            z-index: -2;
            pointer-events: none;
        }

        @keyframes grid-move {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px); }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
            opacity: 0.6;
            animation: orb-float 10s ease-in-out infinite alternate;
        }
        .orb-1 { width: 400px; height: 400px; background: var(--primary); top: -100px; left: -100px; }
        .orb-2 { width: 300px; height: 300px; background: var(--accent); bottom: 10%; right: -50px; animation-delay: -5s; }
        .orb-3 { width: 200px; height: 200px; background: var(--highlight); top: 40%; left: 40%; opacity: 0.3; }

        @keyframes orb-float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 50px); }
        }

        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px;
            position: relative;
            z-index: 10;
        }

        /* --- HERO SECTION --- */
        .hero {
            text-align: center;
            margin-bottom: 80px;
            position: relative;
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            line-height: 1.1;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 20px;
            letter-spacing: 2px;
            background: linear-gradient(180deg, #ffffff 0%, #94a3b8 50%, #475569 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            filter: drop-shadow(0 0 15px rgba(99, 102, 241, 0.6));
            transition: transform 0.3s ease;
        }

        .hero h1:hover {
            transform: scale(1.05);
            filter: drop-shadow(0 0 25px rgba(6, 182, 212, 0.8));
        }

        .hero p.tagline {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent);
            font-size: 1.2rem;
            background: rgba(6, 182, 212, 0.1);
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            border: 1px solid rgba(6, 182, 212, 0.3);
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
        }

        /* --- GITHUB BUTTON --- */
        .github-btn {
            margin-top: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 18px 40px;
            background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.2rem;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }

        .github-btn::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .github-btn:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.4);
        }
        
        .github-btn:hover::before { left: 100%; }

        /* --- TECH SCROLL (HUD STYLE) --- */
        .tech-container {
            position: fixed;
            width: 100%;
            z-index: 50;
            background: rgba(3, 7, 18, 0.95);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            padding: 12px 0;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        .tech-container.top { top: 0; }
        .tech-container.bottom { bottom: 0; }
        
        .tech-container::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 20px; height: 100%;
            background: var(--accent);
            opacity: 0.5;
            z-index: 2;
        }

        .tech-scroll {
            display: flex;
            gap: 50px;
            width: max-content;
            padding-right: 50px; 
            animation: scroll 60s linear infinite;
        }
        .tech-container.bottom .tech-scroll { animation-direction: reverse; }

        @keyframes scroll { 
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); } 
        }

        .tech-icon {
            width: 50px;
            height: 50px;
            filter: grayscale(100%) brightness(200%);
            transition: 0.3s;
            opacity: 0.7;
            flex-shrink: 0;
        }
        .tech-icon:hover {
            filter: grayscale(0%) brightness(100%);
            opacity: 1;
            transform: scale(1.2);
        }

        /* --- TEAM CARDS --- */
        .team-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            perspective: 1000px;
        }

        .card {
            background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            transition: all 0.5s ease;
            position: relative;
            transform-style: preserve-3d;
            overflow: hidden;
        }

        .card.leader {
            border-color: rgba(255, 215, 0, 0.5);
            background: linear-gradient(145deg, rgba(255, 215, 0, 0.05) 0%, rgba(0,0,0,0) 100%);
            grid-column: 1 / -1;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .card:hover {
            transform: translateY(-10px) rotateX(5deg);
            border-color: var(--primary);
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .card.leader:hover {
            border-color: var(--highlight);
            box-shadow: 0 0 40px rgba(255, 215, 0, 0.2);
        }

        .avatar-placeholder {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(45deg, #333, #111);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            border: 2px solid var(--glass-border);
            color: var(--text-muted);
        }

        .card.leader .avatar-placeholder {
            border-color: var(--highlight);
            color: var(--highlight);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: 700;
            color: #fff;
        }
        .card.leader h3 { color: var(--highlight); font-size: 2rem; }

        .card p {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent);
            font-size: 0.9rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .tech-stack-mini {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            padding: 0 10px;
        }
        
        .tech-stack-mini i {
            font-size: 1.8rem;
            color: var(--highlight);
            transition: all 0.3s;
            cursor: pointer;
            opacity: 0.8;
        }

        .tech-stack-mini i:hover {
            transform: translateY(-5px) scale(1.2);
            color: #fff;
            opacity: 1;
            text-shadow: 0 0 15px var(--highlight);
        }

        /* --- MUSIC PLAYER UI --- */
        .music-toggle {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            border: 2px solid var(--accent);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
            box-shadow: 0 0 20px rgba(6, 182, 212, 0.2);
        }
        .music-toggle:hover {
            transform: scale(1.1);
            background: var(--accent);
            color: #000;
        }

        .music-panel {
            position: fixed;
            bottom: 170px;
            right: 30px;
            width: 320px;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 20px;
            z-index: 99;
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            box-shadow: -10px 10px 30px rgba(0,0,0,0.5);
        }
        .music-panel.active { transform: translateX(0); }

        .song-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px; }
        .track-btn {
            background: rgba(255,255,255,0.05);
            border: none;
            color: #fff;
            padding: 10px;
            text-align: left;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.2s;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            display: flex;
            justify-content: space-between;
        }
        .track-btn:hover, .track-btn.active { background: var(--accent); color: #000; }
        
        .controls { display: flex; gap: 10px; }
        .controls button {
            flex: 1; padding: 8px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.2s;
        }
        .btn-stop { background: #ef4444; color: white; }
        .btn-home { background: #3b82f6; color: white; }

        /* --- RESPONSIVE DESIGN --- */
        
        /* Tablet (768px - 1024px) */
        @media (max-width: 1024px) and (min-width: 769px) {
            .wrapper {
                padding: 80px 20px;
            }
            
            .hero h1 {
                font-size: clamp(2.5rem, 6vw, 4.5rem);
            }
            
            .team-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
            
            .card.leader {
                grid-column: 1 / -1;
            }
            
            .tech-icon {
                width: 45px;
                height: 45px;
            }
            
            .github-btn {
                padding: 16px 35px;
                font-size: 1.1rem;
            }
        }
        
        /* Mobile & Small Tablet (max-width: 768px) */
        @media (max-width: 768px) {
            .wrapper {
                padding: 100px 15px 40px;
            }
            
            .hero {
                margin-bottom: 50px;
            }
            
            .hero h1 {
                font-size: clamp(2rem, 8vw, 3rem);
                letter-spacing: 1px;
                margin-bottom: 15px;
            }
            
            .hero p.tagline {
                font-size: 1rem;
                padding: 6px 16px;
            }
            
            .hero p {
                font-size: 0.95rem;
                padding: 0 10px;
            }
            
            .github-btn {
                padding: 14px 28px;
                font-size: 1rem;
                gap: 10px;
            }
            
            .team-section {
                grid-template-columns: 1fr;
                gap: 25px;
            }
            
            .card {
                padding: 30px 20px;
            }
            
            .card.leader {
                padding: 35px 25px;
            }
            
            .card h3 {
                font-size: 1.3rem;
            }
            
            .card.leader h3 {
                font-size: 1.6rem;
            }
            
            .avatar-placeholder {
                width: 80px;
                height: 80px;
                font-size: 1.5rem;
                margin-bottom: 15px;
            }
            
            .tech-stack-mini {
                gap: 12px;
                margin-top: 20px;
            }
            
            .tech-stack-mini i {
                font-size: 1.5rem;
            }
            
            .tech-container {
                padding: 8px 0;
            }
            
            .tech-scroll {
                gap: 30px;
            }
            
            .tech-icon {
                width: 35px;
                height: 35px;
            }
            
            .music-toggle {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 80px;
                right: 20px;
            }
            
            .music-panel {
                width: calc(100% - 40px);
                right: 20px;
                bottom: 140px;
                max-width: 400px;
            }
            
            .track-btn {
                font-size: 0.8rem;
                padding: 8px;
            }
            
            .controls button {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
        
        /* Small Mobile (max-width: 480px) */
        @media (max-width: 480px) {
            .wrapper {
                padding: 90px 10px 30px;
            }
            
            .hero h1 {
                font-size: clamp(1.8rem, 10vw, 2.5rem);
                letter-spacing: 0.5px;
            }
            
            .hero p.tagline {
                font-size: 0.9rem;
                padding: 5px 12px;
            }
            
            .github-btn {
                padding: 12px 24px;
                font-size: 0.95rem;
                width: 100%;
                max-width: 300px;
            }
            
            .card {
                padding: 25px 15px;
            }
            
            .card.leader {
                padding: 30px 20px;
            }
            
            .card h3 {
                font-size: 1.2rem;
            }
            
            .card.leader h3 {
                font-size: 1.4rem;
            }
            
            .avatar-placeholder {
                width: 70px;
                height: 70px;
                font-size: 1.3rem;
            }
            
            .tech-icon {
                width: 30px;
                height: 30px;
            }
            
            .tech-scroll {
                gap: 25px;
            }
            
            .music-toggle {
                width: 45px;
                height: 45px;
                font-size: 18px;
                bottom: 70px;
                right: 15px;
            }
            
            .music-panel {
                width: calc(100% - 30px);
                right: 15px;
                bottom: 125px;
                padding: 15px;
            }
            
            .tech-stack-mini {
                gap: 10px;
            }
            
            .tech-stack-mini i {
                font-size: 1.3rem;
            }
        }
        
        /* Landscape Mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .wrapper {
                padding: 60px 15px 30px;
            }
            
            .hero {
                margin-bottom: 30px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .team-section {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
            
            .card.leader {
                grid-column: 1 / -1;
            }
        }
    </style>
</head>
<body>

    <div class="bg-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- TECH SCROLL TOP -->
    <div class="tech-container top">
        <div class="tech-scroll">
            <!-- GROUP 1 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" title="Laravel">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" title="PHP">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg" title="MySQL">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" title="Docker">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" title="Tailwind CSS">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" title="JavaScript">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery">
            
            <!-- GROUP 2 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" title="Laravel">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" title="PHP">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg" title="MySQL">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" title="Docker">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" title="Tailwind CSS">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" title="JavaScript">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery">

             <!-- GROUP 3 -->
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" title="Laravel">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" title="PHP">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg" title="MySQL">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" title="Docker">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" title="Tailwind CSS">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" title="JavaScript">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery">

             <!-- GROUP 4 -->
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" title="Laravel">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" title="PHP">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original.svg" title="MySQL">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" title="Docker">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/tailwindcss/tailwindcss-original.svg" title="Tailwind CSS">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" title="JavaScript">
             <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery">
        </div>
    </div>

    <div class="wrapper">
        <div class="hero">
            <h1 data-text="SHOP VTKT">SHOP VTKT</h1>
            <br>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                Hệ sinh thái dịch vụ số tối ưu, được xây dựng trên nền tảng công nghệ hiện đại nhất.
            </p>

            <a href="https://github.com/cho123nm/VTKT-domain-services" target="_blank" class="github-btn">
                <i class="fab fa-github"></i> Repository
            </a>
            <p style="margin-top: 15px; font-size: 0.9rem; color: var(--text-muted); opacity: 0.7;">
                <i class="fas fa-star" style="color: var(--highlight);"></i> Star us if you like it!
            </p>
        </div>

        <div class="team-section">
            
            <!-- Leader -->
            <div class="card leader">
                <div class="avatar-placeholder"><i class="fas fa-crown"></i></div>
                <h3>ĐÀM THANH VŨ</h3>
                <p>LEAD DEVELOPER</p>
                
                <div class="tech-stack-mini">
                    <i class="fab fa-laravel" title="Laravel Framework"></i>
                    <i class="fab fa-php" title="PHP"></i>
                    <i class="fas fa-database" title="MySQL Database"></i>
                    <i class="fab fa-docker" title="Docker"></i>
                    <i class="fab fa-linux" title="Linux Server"></i>
                    <i class="fab fa-bootstrap" title="Bootstrap"></i>
                    <i class="fab fa-js" title="JavaScript/jQuery/Ajax"></i>
                    <i class="fab fa-html5" title="HTML5"></i>
                    <i class="fab fa-css3-alt" title="CSS3"></i>
                </div>
            </div>

            <!-- Các thành viên khác: Đổi thành TEAM MEMBER (Tiếng Anh) -->
            <div class="card">
                <div class="avatar-placeholder"><i class="fas fa-user-astronaut"></i></div>
                <h3>Hoàng Thủy Tiên</h3>
                <p>TEAM MEMBER</p>
            </div>

            <div class="card">
                <div class="avatar-placeholder"><i class="fas fa-code"></i></div>
                <h3>Nguyễn Kỳ Đàn</h3>
                <p>TEAM MEMBER</p>
            </div>

            <div class="card">
                <div class="avatar-placeholder"><i class="fas fa-laptop-code"></i></div>
                <h3>Trương Ngọc Tiến</h3>
                <p>TEAM MEMBER</p>
            </div>
        </div>
    </div>

    <!-- TECH SCROLL BOTTOM -->
    <div class="tech-container bottom">
        <div class="tech-scroll">
            <!-- GROUP 1 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/vscode/vscode-original.svg" title="VS Code">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg" title="Linux">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bootstrap/bootstrap-original.svg" title="Bootstrap">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" title="HTML5">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" title="CSS3">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery/Ajax">
            
            <!-- GROUP 2 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/vscode/vscode-original.svg" title="VS Code">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg" title="Linux">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bootstrap/bootstrap-original.svg" title="Bootstrap">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" title="HTML5">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" title="CSS3">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery/Ajax">

            <!-- GROUP 3 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/vscode/vscode-original.svg" title="VS Code">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg" title="Linux">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bootstrap/bootstrap-original.svg" title="Bootstrap">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" title="HTML5">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" title="CSS3">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery/Ajax">

            <!-- GROUP 4 -->
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/vscode/vscode-original.svg" title="VS Code">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/linux/linux-original.svg" title="Linux">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/bootstrap/bootstrap-original.svg" title="Bootstrap">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original.svg" title="HTML5">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original.svg" title="CSS3">
            <img class="tech-icon" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/jquery/jquery-original.svg" title="jQuery/Ajax">
        </div>
    </div>

    <div class="music-toggle" onclick="toggleMusic()">
        <i class="fas fa-music"></i>
    </div>

    <div class="music-panel" id="musicPanel">
        <h4 style="margin-bottom: 15px; color: var(--accent); border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 5px;">
            <i class="fas fa-compact-disc"></i> PLAYLIST
        </h4>
        <div class="song-list">
            <button class="track-btn" onclick="playTrack(1)" id="track1">
                <span>🎵 Lofi Chill 1</span>
            </button>
            <button class="track-btn" onclick="playTrack(2)" id="track2">
                <span>🔥 EDM Mix (Default)</span>
            </button>
        </div>
        
        <!-- YouTube Player - Ẩn nhưng vẫn có kích thước để API hoạt động -->
        <div id="musicFrame" style="width: 1px; height: 1px; position: absolute; left: -9999px; opacity: 0;"></div>

        <div class="controls">
            <button class="btn-stop" onclick="stopMusic()"><i class="fas fa-stop"></i> STOP</button>
            <button class="btn-home" onclick="goHome()"><i class="fas fa-home"></i> HOME</button>
        </div>
    </div>

    <!-- YouTube IFrame API -->
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        const tracks = {
            1: "V8ZQ3ga6xEY",
            2: "vXxfOGjicGQ"
        };
        let activeTrackId = 2;
        let isPlaying = false;
        let player = null;

        // YouTube API ready callback
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('musicFrame', {
                videoId: tracks[activeTrackId],
                playerVars: {
                    'autoplay': 1,
                    'loop': 1,
                    'playlist': tracks[activeTrackId],
                    'controls': 0,
                    'modestbranding': 1,
                    'rel': 0,
                    'showinfo': 0
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        function onPlayerReady(event) {
            // Tự động phát nhạc khi player sẵn sàng
            event.target.playVideo();
            isPlaying = true;
            document.querySelector('.music-toggle').classList.add('active');
            document.getElementById('track' + activeTrackId).classList.add('active');
        }

        function onPlayerStateChange(event) {
            // Khi nhạc hết (state = 0), tự động phát lại
            if (event.data === YT.PlayerState.ENDED) {
                event.target.playVideo(); // Tự động phát lại
            }
        }

        // Fallback nếu YouTube API không load được
        window.addEventListener('DOMContentLoaded', () => {
            // Nếu sau 2 giây mà API chưa load, dùng iframe thông thường
            setTimeout(() => {
                if (!player) {
                    const frame = document.getElementById('musicFrame');
                    frame.src = `https://www.youtube.com/embed/${tracks[activeTrackId]}?autoplay=1&loop=1&playlist=${tracks[activeTrackId]}&enablejsapi=1`;
                    isPlaying = true;
                    document.querySelector('.music-toggle').classList.add('active');
                    document.getElementById('track' + activeTrackId).classList.add('active');
                }
            }, 2000);
        });

        function toggleMusic() {
            const panel = document.getElementById('musicPanel');
            panel.classList.toggle('active');
        }

        function playTrack(id) {
            activeTrackId = id;
            isPlaying = true;
            
            if (player) {
                // Dùng YouTube API nếu có
                player.loadVideoById({
                    videoId: tracks[id],
                    startSeconds: 0
                });
                player.setLoop(true);
            } else {
                // Fallback: dùng iframe thông thường
                const frame = document.getElementById('musicFrame');
                frame.src = "";
                setTimeout(() => {
                    frame.src = `https://www.youtube.com/embed/${tracks[id]}?autoplay=1&loop=1&playlist=${tracks[id]}&enablejsapi=1`;
                }, 100);
            }
            
            document.querySelectorAll('.track-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('track' + id).classList.add('active');
        }

        function stopMusic() {
            isPlaying = false;
            if (player) {
                player.stopVideo();
            } else {
                document.getElementById('musicFrame').src = "";
            }
            document.querySelectorAll('.track-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelector('.music-toggle').classList.remove('active');
        }

        function goHome() {
            try { window.location.href = "{{ route('home') }}"; } catch(e) {}
        }

        document.addEventListener('click', function(event) {
            const panel = document.getElementById('musicPanel');
            const toggle = document.querySelector('.music-toggle');
            if (!panel.contains(event.target) && !toggle.contains(event.target) && panel.classList.contains('active')) {
                panel.classList.remove('active');
            }
        });
    </script>
</body>
</html>