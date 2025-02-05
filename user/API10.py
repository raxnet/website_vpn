from flask import Flask, request, jsonify
import json
import os
import logging
from datetime import datetime
import psutil
import subprocess
import platform
import time

app = Flask(__name__)

# Path ke file konfigurasi Xray
CONFIG_FILE = "/etc/xray/config.json"

# Set up logging
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)

# --- Fungsi Utilitas ---

def load_config():
    """Membaca file konfigurasi Xray."""
    try:
        with open(CONFIG_FILE, "r") as f:
            return json.load(f)
    except Exception as e:
        logger.error("Gagal membaca file konfigurasi: %s", e)
        raise

def save_config(config):
    """Menyimpan perubahan ke file konfigurasi Xray."""
    try:
        with open(CONFIG_FILE, "w") as f:
            json.dump(config, f, indent=4)
    except Exception as e:
        logger.error("Gagal menyimpan file konfigurasi: %s", e)
        raise

def restart_xray():
    """Merestart layanan Xray."""
    os.system("systemctl restart xray")

def check_service(service_name):
    """Memeriksa status layanan sistem."""
    try:
        result = subprocess.run(['systemctl', 'is-active', service_name], stdout=subprocess.PIPE, text=True)
        return "running" if result.stdout.strip() == "active" else "stopped"
    except Exception as e:
        return f"error: {str(e)}"

def get_uptime():
    """Mendapatkan uptime server."""
    try:
        uptime_seconds = time.time() - psutil.boot_time()
        uptime_string = time.strftime("%d days, %H:%M:%S", time.gmtime(uptime_seconds))
        return uptime_string
    except Exception as e:
        return f"error: {str(e)}"

# --- Endpoint Monitoring ---

@app.route('/monitor', methods=['GET'])
def monitor_server():
    """Endpoint untuk memonitor status server."""
    try:
        os_name = platform.system()
        os_version = platform.version()
        hostname = platform.node()
        cpu_usage = psutil.cpu_percent(interval=1)
        memory = psutil.virtual_memory()
        disk = psutil.disk_usage('/')
        uptime = get_uptime()

        # Status layanan
        xray_status = check_service('xray')
        ssh_status = check_service('ssh')
        dropbear_status = check_service('dropbear')

        data = {
            "system": {
                "os_name": os_name,
                "os_version": os_version,
                "hostname": hostname,
                "uptime": uptime
            },
            "cpu": {
                "usage": f"{cpu_usage}%",
            },
            "memory": {
                "total": f"{memory.total // (1024 ** 2)} MB",
                "used": f"{memory.used // (1024 ** 2)} MB",
                "percent": f"{memory.percent}%",
            },
            "disk": {
                "total": f"{disk.total // (1024 ** 3)} GB",
                "used": f"{disk.used // (1024 ** 3)} GB",
                "percent": f"{disk.percent}%",
            },
            "services": {
                "xray": xray_status,
                "ssh": ssh_status,
                "dropbear": dropbear_status,
            }
        }
        return jsonify(data)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# --- Endpoint Penggunaan Data ---

@app.route('/penggunaan_data/<uuid>', methods=['GET'])
def penggunaan_data(uuid):
    """Endpoint untuk mendapatkan penggunaan data server berdasarkan UUID."""
    try:
        # Load konfigurasi Xray
        config = load_config()

        # Mengambil informasi penggunaan CPU, Memori, Disk, dan Jaringan
        cpu_usage = psutil.cpu_percent(interval=1)
        memory = psutil.virtual_memory()
        disk = psutil.disk_usage('/')
        net_io = psutil.net_io_counters()

        # Mencari data yang sesuai dengan UUID
        account_data = None
        for inbound in config["inbounds"]:
            if inbound.get("protocol") in ["vmess", "vless", "trojan"]:
                clients = inbound["settings"].get("clients", [])
                for client in clients:
                    if client.get("id") == uuid or client.get("password") == uuid:
                        account_data = client
                        break

        if not account_data:
            return jsonify({"error": "Akun dengan UUID tersebut tidak ditemukan"}), 404

        # Menyusun respons dengan data penggunaan dan informasi akun
        data = {
            "uuid": uuid,
            "account_info": account_data,
            "cpu": {
                "usage": f"{cpu_usage}%",
            },
            "memory": {
                "total": f"{memory.total // (1024 ** 2)} MB",
                "used": f"{memory.used // (1024 ** 2)} MB",
                "percent": f"{memory.percent}%",
            },
            "disk": {
                "total": f"{disk.total // (1024 ** 3)} GB",
                "used": f"{disk.used // (1024 ** 3)} GB",
                "percent": f"{disk.percent}%",
            },
            "network": {
                "bytes_sent": f"{net_io.bytes_sent} bytes",
                "bytes_recv": f"{net_io.bytes_recv} bytes",
                "packets_sent": f"{net_io.packets_sent} packets",
                "packets_recv": f"{net_io.packets_recv} packets",
            }
        }
        return jsonify(data)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# --- Endpoint Xray Account ---

@app.route('/create-account', methods=['POST'])
def create_account():
    """Membuat atau memperbarui akun di Xray."""
    data = request.get_json()

    # Validasi input
    required_fields = ["protocol", "username", "expiration_date"]
    for field in required_fields:
        if field not in data:
            return jsonify({"error": f"Field '{field}' diperlukan"}), 400

    protocol = data["protocol"]
    username = data["username"]
    expiration_date = data["expiration_date"]

    # Validasi format expiration_date
    try:
        expiration_date = datetime.strptime(expiration_date, '%Y-%m-%d %H:%M:%S')
    except ValueError:
        return jsonify({"error": "Format 'expiration_date' tidak valid. Gunakan format 'YYYY-MM-DD HH:MM:SS'"}), 400

    # Untuk protokol Trojan, gunakan password. Untuk Vmess/Vless, gunakan UUID.
    identifier_field = "password" if protocol == "trojan" else "id"
    identifier_value = data.get("uuid") if protocol in ["vmess", "vless"] else data.get("password")

    if not identifier_value:
        field_name = "uuid" if protocol in ["vmess", "vless"] else "password"
        return jsonify({"error": f"Field '{field_name}' diperlukan untuk protokol {protocol}"}), 400

    try:
        config = load_config()
        updated = False
        inbound_found = False

        for inbound in config["inbounds"]:
            if inbound.get("protocol") == protocol:
                inbound_found = True
                clients = inbound["settings"].get("clients", [])

                # Cari UUID/password yang sama di dalam daftar clients
                for client in clients:
                    if client.get(identifier_field) == identifier_value:
                        # Jika ditemukan, perbarui expiration_date
                        client["expiration_date"] = expiration_date.strftime('%Y-%m-%d %H:%M:%S')
                        updated = True
                        break

                if not updated:
                    # Jika tidak ditemukan, tambahkan data baru
                    clients.append({
                        identifier_field: identifier_value,
                        "email": f"{username}@example.com",
                        "expiration_date": expiration_date.strftime('%Y-%m-%d %H:%M:%S')
                    })

        if not inbound_found:
            return jsonify({"error": f"Tidak ada inbound untuk protokol {protocol} ditemukan"}), 404

        save_config(config)
        restart_xray()

        action = "diperbarui" if updated else "ditambahkan"
        return jsonify({
            "message": f"Akun {protocol} berhasil {action}",
            "details": {
                "protocol": protocol,
                "identifier_field": identifier_field,
                "identifier_value": identifier_value,
                "username": username,
                "expiration_date": expiration_date.strftime('%Y-%m-%d %H:%M:%S')
            }
        }), 200

    except Exception as e:
        logger.error("Gagal membuat atau memperbarui akun: %s", e)
        return jsonify({"error": "Terjadi kesalahan internal"}), 500


# --- Endpoint Hapus Akun ---

@app.route('/delete-account', methods=['POST'])
def delete_account():
    """Menghapus akun berdasarkan UUID dan protocol dari konfigurasi Xray."""
    try:
        # Ambil data JSON dari request
        data = request.get_json()
        
        # Validasi input
        required_fields = ["protocol", "uuid"]
        for field in required_fields:
            if field not in data:
                return jsonify({"error": f"Field '{field}' diperlukan"}), 400

        protocol = data["protocol"]
        uuid = data["uuid"]
        username = data.get("username")
        expiration_date = data.get("expiration_date")

        # Validasi format expiration_date jika diberikan
        if expiration_date:
            try:
                expiration_date = datetime.strptime(expiration_date, '%Y-%m-%d %H:%M:%S')
            except ValueError:
                return jsonify({"error": "Format 'expiration_date' tidak valid. Gunakan format 'YYYY-MM-DD HH:MM:SS'"}), 400

        # Load konfigurasi Xray
        config = load_config()

        # Flag untuk menentukan apakah akun ditemukan
        account_found = False

        # Loop untuk mencari akun berdasarkan UUID dan protocol, lalu menghapusnya
        for inbound in config["inbounds"]:
            if inbound.get("protocol") == protocol:
                clients = inbound["settings"].get("clients", [])
                for client in clients[:]:
                    # Cek UUID dan jika ada expiration_date, juga periksa tanggalnya
                    if client.get("id") == uuid or client.get("password") == uuid:
                        # Jika expiration_date diberikan, pastikan tanggalnya sesuai sebelum menghapus
                        if expiration_date:
                            client_expiration = datetime.strptime(client.get("expiration_date"), '%Y-%m-%d %H:%M:%S')
                            if client_expiration != expiration_date:
                                continue  # Lewatkan akun ini jika expiration_date tidak sesuai
                        # Jika tidak ada perbedaan expiration_date atau tidak diberikan, hapus akun
                        clients.remove(client)
                        account_found = True
                        break

        if not account_found:
            return jsonify({"error": "Akun dengan UUID tersebut tidak ditemukan"}), 404

        # Simpan konfigurasi yang sudah diubah
        save_config(config)

        # Restart layanan Xray untuk menerapkan perubahan
        restart_xray()

        return jsonify({"message": "Akun berhasil dihapus"}), 200

    except Exception as e:
        logger.error("Gagal menghapus akun: %s", e)
        return jsonify({"error": "Terjadi kesalahan internal"}), 500


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)