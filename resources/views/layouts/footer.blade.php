<div id="kt_app_footer" class="app-footer align-items-center justify-content-between">
    <div class="text-dark order-2 order-md-1">
        <span class="text-muted fw-semibold me-1">2026&copy;</span>
        <a href="https://www.facebook.com/thanh.vu.826734" target="_blank" class="text-gray-800 text-hover-primary">CUNG CẤP BỞI <b class="text-danger">THANHVU.NET V4</b></a>
    </div>
    <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
        <li class="menu-item">
            <a href="{{ route('home') }}" target="_blank" class="menu-link px-2">About</a>
        </li>
        <li class="menu-item">
            <a href="https://www.facebook.com/thanh.vu.826734" target="_blank" class="menu-link px-2">Support</a>
        </li>
        <li class="menu-item">
            <a href="/domain/" target="_blank" class="menu-link px-2">Purchase</a>
        </li>
    </ul>
</div>

<!-- Enhanced Music Player Pro -->
<div class="music-player">
    <div class="music-header" id="musicHeader">
        <div class="music-title">🎵 Music Player Pro</div>
        <div class="drag-indicator" title="Kéo để di chuyển">⋮⋮⋮</div>
        <div class="music-controls-header">
            <button class="minimize-btn" onclick="toggleMinimize()" id="minimizeBtn" title="Thu nhỏ player">➖</button>
            <button class="music-toggle-btn" onclick="toggleMusicPlayer()" id="musicToggleBtn">🎶</button>
        </div>
    </div>
    
    <div class="music-input-section" id="musicInputSection">
        <div class="input-group">
            <label class="input-label">Embed Code hoặc Link</label>
            <textarea class="music-input" id="musicInput" placeholder="Paste Spotify/YouTube/SoundCloud embed iframe code hoặc link..."></textarea>
        </div>
        <div class="input-buttons">
            <button class="music-btn" onclick="loadMusic()">🚀 Load Music</button>
            <button class="music-btn secondary" onclick="clearMusic()">🗑️ Clear</button>
        </div>
    </div>
    
    <div class="music-status" id="musicStatus" style="display: none;">Click 🎶 để mở music player</div>
    <div id="musicContainer"></div>
</div>

<script src="/assets/plugins/global/plugins.bundle.js"></script>
<script src="/assets/js/fix-search-error.js"></script>
<script src="/assets/js/scripts.bundle.js"></script>

