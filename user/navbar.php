<?php
?><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Tema Gelap */
    .dark-theme {
        background-color: #333;
        color: #fff;
    }

    /* Tema Terang */
    .light-theme {
        background-color: #fff;
        color: #000;
    }

    /* Sidebar Styles */
    .custom-sidebar {
        width: 250px;
        height: 100vh;
        padding: 20px;
        position: fixed;
        top: 0;
        left: -250px;
        opacity: 0;
        transform: scale(0.9);
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.6);
        transition: left 0.3s ease-out, opacity 0.3s ease-out, transform 0.5s ease-out, box-shadow 0.5s ease-out;
        z-index: 1001;
        background-color: #222; /* Pastikan sidebar gelap memiliki latar belakang solid */
    }

    .custom-sidebar.light-theme {
        background-color: #f5f5f5; /* Sidebar terang memiliki latar belakang terang */
    }

    .custom-logo-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .custom-sidebar img {
        width: 150px;
        height: auto;
        vertical-align: middle;
        border-radius: 50%;
        filter: drop-shadow(0px 6px 10px rgba(0, 0, 0, 0.6));
        transition: filter 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .custom-sidebar img:hover {
        transform: scale(1.1);
        filter: brightness(1.2);
    }

    .custom-menu-section {
        background-color: transparent !important;
        margin-top: 30px;
    }

    .custom-menu-item {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease;
    }

    .custom-menu-item i {
        margin-right: 10px;
    }

    .custom-menu-item:hover {
        background-color: #444;
        color: #fff;
        transform: scale(1.05);
    }

    .custom-menu-section h2 {
        font-size: 12px;
        color: #888;
        margin-bottom: 10px;
    }

    .custom-menu-item.light-theme {
        color: #000;
    }

    /* Tombol Toggle Sidebar */
    .custom-toggle-btn {
        position: fixed;
        top: 12px;
        left: 20px;
        background-color: #333; /* Warna latar belakang tombol toggle gelap */
        color: #fff;
        width: 35px;
        height: 35px;
        border: 1px solid #fff;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        z-index: 1100;
        transition: transform 0.3s ease-out, color 0.3s ease-out, filter 0.3s ease-out, left 0.3s ease-out;
    }

    .custom-toggle-btn:hover {
        background-color: #444; /* Warna latar belakang saat hover */
        color: #fff;
        filter: brightness(1.3);
    }

    .custom-toggle-btn:active {
        transform: scale(0.9);
    }

    .custom-toggle-btn.dark-theme {
        background-color: #fff;
        color: #333;
        border: 1px solid #333;
    }

    .custom-toggle-btn.light-theme {
        background-color: #333;
        color: #fff;
        border: 1px solid #fff;
    }

    @media (max-width: 768px) {
        .custom-sidebar {
            width: 200px;
        }

        .custom-menu-section h2 {
            font-size: 14px;
        }

        .custom-menu-item {
            font-size: 14px;
        }
    }

    /* New Custom Classes for elements that didn't have one before */
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
        <h2>UTAMA</h2>
        <a class="custom-menu-item" href="/user/dashboard">
            <i class="fas fa-home"></i> Dasbor
        </a>
        <a class="custom-menu-item" href="/user/pengaturan">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
        <a class="custom-menu-item" href="/user/management">
            <i class="fas fa-bullhorn"></i> Management Layanan
        </a>
        <a class="custom-menu-item" href="/user/dompet">
            <i class="fas fa-user-plus"></i> dompet
        </a>
        <a class="custom-menu-item" href="/user/server">
            <i class="fas fa-server"></i> Server
        </a>
    </div>
    <div class="custom-menu-section">
        <h2>LAYANAN</h2>
        <a class="custom-menu-item" href="/user/vmess">
            <i class="fas fa-shopping-cart"></i> VMess
        </a>
        <a class="custom-menu-item" href="/user/vless">
            <i class="fas fa-lock"></i> VLess
        </a>
        <a class="custom-menu-item" href="/user/trojan">
            <i class="fas fa-lock"></i> Trojan
        </a>
    </div>
    <div class="custom-menu-section">
        <h2>DUKUNGAN</h2>
        <a class="custom-menu-item" href="/user/pengetahuan">
            <i class="fas fa-book"></i> Basis Pengetahuan
        </a>
        <a class="custom-menu-item" href="https://t.me/ANDRAXnett" target="_blank">
            <i class="fab fa-telegram-plane"></i> Tautan Grup Telegram
        </a>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('custom-sidebar');
        const overlay = document.getElementById('custom-overlay');
        const toggleBtn = document.getElementById('custom-toggleBtn');

        if (sidebar.style.left === "0px") {
            sidebar.style.left = "-250px";
            sidebar.style.opacity = "0";
            sidebar.style.transform = "scale(0.9)";
            overlay.style.display = "none";
            toggleBtn.style.left = "20px";
        } else {
            sidebar.style.left = "0px";
            sidebar.style.opacity = "1";
            sidebar.style.transform = "scale(1)";
            overlay.style.display = "block";
            toggleBtn.style.left = "250px";
        }
    }

   
</script>