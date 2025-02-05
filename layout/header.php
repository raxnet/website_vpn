<?php

?>
<!-- Masukkan CDN Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    /* Header Section */
    .custom-header {
        width: 100%;
        background-color: #003f5c;/* Warna abu-abu terang */
        padding: 10px 0;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        text-align: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5); /* Shadow ringan */
    }

    .custom-header img {
        max-width: 130px;
        height: auto;
        display: inline-block;
    }

    /* Container untuk Dark Mode dan Profil */
    .menu-container {
        position: fixed;
        top: 10px;
        right: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1100;
    }

    /* Tombol Dark Mode */
    .dark-mode-toggle {
        font-size: 20px;
        cursor: pointer;
        padding: 6px;
        border-radius: 50%;
        background: #; /* Warna netral yang lebih terang */
        transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        color: #fff;
    }

    .dark-mode-toggle:hover {
        background-color: #3498db; /* Biru terang */
        transform: scale(1.1);
        box-shadow: 0px 4px 12px rgba(52, 152, 219, 0.5);
    }

    /* Profile Menu */
    .profile-menu {
        position: relative;
        cursor: pointer;
    }

    .profile-menu i {
        font-size: 22px;
        color: #fff;
        border-radius: 50%;
        padding: 6px;
        background: #; /* Warna netral terang */
        transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-menu i:hover {
        background-color: #3498db; /* Biru terang */
        transform: scale(1.1);
        box-shadow: 0px 4px 12px rgba(52, 152, 219, 0.5);
    }

    /* Lampu hijau untuk status online */
    .online-indicator {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        background-color: #2ecc71; /* Warna hijau untuk status online */
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0px 0px 5px rgba(46, 204, 113, 0.8);
        animation: pulse 2s infinite ease-in-out;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Dropdown Menu */
    .dropdown {
        position: absolute;
        top: 40px;
        right: 0;
        background: #2c3e50; /* Warna abu-abu gelap */
        padding: 8px;
        border-radius: 6px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.3);
        z-index: 1200;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px);
        transition: opacity 0.4s ease, transform 0.4s ease, visibility 0.4s;
    }

    .dropdown.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 8px;
        border-radius: 5px;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        background: #3b4b5a; /* Warna biru gelap */
        margin: 4px 0;
        transition: color 0.3s ease, background-color 0.3s ease, transform 0.2s ease;
    }

    .dropdown a:hover {
        color: #fff;
        background-color: #f39c12; /* Warna oranye terang */
        transform: translateX(4px);
    }

    /* Dark Mode */
    body.dark-mode {
        background-color: #1e1e1e; /* Gelap untuk mode gelap */
        color: white;
    }

    body.dark-mode .custom-header {
        background-color: #121212; /* Latar belakang gelap */
    }

    body.dark-mode .dropdown {
        background: #2c3e50;
    }

    body.dark-mode .profile-menu i,
    body.dark-mode .dark-mode-toggle {
        background: #121212;
    }

    body.dark-mode .profile-menu i:hover,
    body.dark-mode .dark-mode-toggle:hover {
        background: #f39c12; /* Oranye terang */
    }

</style>

<!-- Header Section -->
<div class="custom-header">
<img src="/layout/raxnet.png" alt="Logo" />
</div>

<!-- Profile Menu & Dark Mode Toggle -->
<div class="menu-container">
    <!-- Tombol Dark Mode -->
    <div class="dark-mode-toggle" onclick="toggleDarkMode()">
        <i class="fas fa-moon"></i>
    </div>

    <!-- Profile Menu -->
    <div class="profile-menu" onclick="toggleDropdown()">
        <i class="fas fa-user-circle"></i>
        <div class="online-indicator"></div>
    </div>
</div>

<!-- Dropdown Menu -->
<div id="profile-dropdown" class="dropdown">
    <a href="#">Profile</a>
    <a href="../user/pengaturan.php">Settings</a>
    <a href="./logout.php">Logout</a>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profile-dropdown");
        dropdown.classList.toggle("show");
    }

    document.addEventListener("click", function(event) {
        const profileMenu = document.querySelector(".profile-menu");
        const dropdown = document.getElementById("profile-dropdown");

        if (!profileMenu.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove("show");
        }
    });

    function toggleDarkMode() {
        document.body.classList.toggle("dark-mode");
        const icon = document.querySelector(".dark-mode-toggle i");
        if (document.body.classList.contains("dark-mode")) {
            icon.classList.remove("fa-moon");
            icon.classList.add("fa-sun");
        } else {
            icon.classList.remove("fa-sun");
            icon.classList.add("fa-moon");
        }
    }
</script>