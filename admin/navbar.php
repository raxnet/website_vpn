<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }

    .custom-sidebar {
        width: 250px;
        height: 100vh;
        padding: 20px;
        position: fixed;
        top: 0;
        left: -290px; /* Memastikan tertutup penuh */
        background-color: #222;
        transition: left 0.3s ease-in-out;
        z-index: 1001;
        overflow-y: auto;
    }

    .custom-sidebar img {
        width: 120px;
        border-radius: 50%;
    }

    .custom-menu-section {
        margin-top: 20px;
    }

    .custom-menu-item {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        font-size: 15px;
        color: #ddd;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .custom-menu-item i {
        margin-right: 12px;
        font-size: 18px;
    }

    .custom-menu-item:hover {
        background-color: #444;
        color: #fff;
    }

    .custom-toggle-btn {
        position: fixed;
        top: 12px;
        left: 20px;
        background-color: #333;
        color: #fff;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        font-size: 18px;
        cursor: pointer;
        z-index: 1100;
        transition: left 0.3s ease-in-out;
    }

    .custom-toggle-btn:hover {
        background-color: #444;
    }

    .custom-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }
</style>

<div class="custom-overlay" id="custom-overlay" onclick="toggleSidebar()"></div>
<button class="custom-toggle-btn" id="custom-toggleBtn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<div class="custom-sidebar" id="custom-sidebar">
    <div class="custom-logo-section">
        <img alt="Logo" src="/layout/raxnet.png" />
    </div>
    <div class="custom-menu-section">
        <a class="custom-menu-item" href="/admin/dashboard">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a class="custom-menu-item" href="/admin/pengguna">
            <i class="fas fa-users"></i> Pengguna
        </a>
        <a class="custom-menu-item" href="/admin/account">
     <i class="fas fa-shield-alt"></i> akun layanan
        </a>
        <a class="custom-menu-item" href="/admin/server">
            <i class="fas fa-server"></i> Server
        </a>
        <a class="custom-menu-item" href="/admin/transaksi">
            <i class="fas fa-receipt"></i> Transaksi
        </a>
        <a class="custom-menu-item" href="/admin/pembayaran">
            <i class="fas fa-wallet"></i> Gateway Pembayaran
        </a>
        <a class="custom-menu-item" href="/admin/cloudflare">
            <i class="fas fa-cog"></i> captcha cloudflare 
        </a>
        <a class="custom-menu-item" href="/admin/kupon">
            <i class="fas fa-handshake"></i> buat kupon
        </a>
        
        <a class="custom-menu-item" href="/admin/affiliate">
            <i class="fas fa-handshake"></i> Afiliasi
        </a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("custom-sidebar");
        const overlay = document.getElementById("custom-overlay");
        const toggleBtn = document.getElementById("custom-toggleBtn");

        if (sidebar.style.left === "0px") {
            sidebar.style.left = "-290px"; // Memastikan sidebar benar-benar tertutup
            toggleBtn.style.left = "20px";
            overlay.style.display = "none";
        } else {
            sidebar.style.left = "0px";
            toggleBtn.style.left = "270px";
            overlay.style.display = "block";
        }
    }
</script>