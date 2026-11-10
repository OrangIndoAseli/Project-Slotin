# SLOTIN – Smart Parking System

**SLOTIN** adalah sistem monitoring parkir berbasis Computer Vision yang dirancang untuk membantu pengelolaan dan pemantauan kapasitas area parkir secara real-time.

Sistem ini menggunakan algoritma **YOLO (You Only Look Once)** untuk mendeteksi kendaraan, khususnya sepeda motor, melalui kamera smartphone. Hasil deteksi diproses oleh backend Python menggunakan FastAPI, kemudian dikirim ke aplikasi Laravel untuk ditampilkan pada dashboard administrator dan disimpan ke database.

## Features

- **Real-Time Vehicle Detection** – Mendeteksi sepeda motor menggunakan YOLO.
- **Camera Station** – Menggunakan kamera smartphone sebagai sumber video.
- **Parking Zone Monitoring** – Memantau kondisi setiap zona parkir.
- **Automatic Parking Status** – Memperbarui status zona berdasarkan jumlah kendaraan.
- **Admin Dashboard** – Menampilkan statistik dan informasi kapasitas parkir.
- **Scan History** – Menyimpan riwayat hasil pemindaian.
- **Zone Management** – Mengelola data zona dan kapasitas parkir.

## Technologies

| Technology | Usage |
|---|---|
| PHP | Backend application |
| Laravel | Web application framework |
| Python | Computer Vision processing |
| YOLOv8 | Vehicle detection |
| FastAPI | AI inference API |
| OpenCV | Image and video processing |
| MySQL | Database management |
| Bootstrap 5 | Frontend interface |
| JavaScript | Camera streaming and interaction |
| ngrok | Secure public access to camera station |

## System Architecture

SLOTIN menggunakan arsitektur terpisah antara aplikasi web Laravel dan layanan Computer Vision berbasis Python.

```text
           SMARTPHONE CAMERA
                  |
                  | Video Frames
                  v
          NGROK HTTPS / WSS
                  |
                  v
          PYTHON FASTAPI
                  |
                  v
              YOLOv8
                  |
                  | Vehicle Detection
                  v
           DETECTION RESULT
                  |
                  | HTTP POST
                  v
          LARAVEL BACKEND
                  |
                  v
              MYSQL
                  |
                  v
          ADMIN DASHBOARD
```

### System Workflow

1. Administrator menjalankan aplikasi Laravel pada komputer.
2. Petugas membuka Camera Station melalui smartphone.
3. Smartphone mengirimkan frame kamera ke FastAPI melalui WebSocket.
4. YOLOv8 memproses frame untuk mendeteksi sepeda motor.
5. Hasil deteksi dikirim ke Laravel melalui HTTP API.
6. Laravel memperbarui jumlah kendaraan dan status zona parkir.
7. Hasil pemindaian disimpan ke database MySQL.
8. Administrator dapat memantau kondisi parkir melalui dashboard.

## Parking Zone Status

SLOTIN menggunakan empat status utama:

| Status | Description |
|---|---|
| KOSONG | Zona belum terisi kendaraan |
| TERSEDIA | Zona masih memiliki kapasitas |
| PENUH | Kapasitas zona telah terpenuhi |
| TIDAK TERPANTAU | Zona belum memiliki data pemantauan terbaru |

## Project Structure

```text
Project-Slotin/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│
├── ML/
│   ├── app.py
│   ├── requirements.txt
│   ├── models/
│   │   └── yolov8s.pt
│   └── venv/
│
├── database/
│   └── migrations/
│
├── resources/
│   └── views/
│       ├── layouts/
│       ├── dashboard/
│       ├── scan/
│       ├── zones/
│       └── history/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── public/
├── .env.example
└── README.md
```

## Requirements

Pastikan perangkat sudah memiliki:

- PHP dan Composer
- Laravel
- MySQL
- Python 3
- pip
- ngrok
- Browser dengan dukungan kamera dan WebSocket

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/Project-Slotin.git
```

Masuk ke direktori proyek:

```bash
cd Project-Slotin
```

### 2. Install Laravel Dependencies

```bash
composer install
```

Salin konfigurasi environment:

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 3. Database Configuration

Buat database MySQL dengan nama:

```sql
CREATE DATABASE slotin;
```

Sesuaikan konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=slotin
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi apabila seluruh struktur tabel sudah tersedia dalam migration:

```bash
php artisan migrate
```

Jika menggunakan database yang sudah diisi secara manual, pastikan struktur tabel sesuai dengan kebutuhan aplikasi sebelum menjalankan migrasi.

### 4. Install Python Dependencies

Masuk ke folder ML:

```bash
cd ML
```

Buat virtual environment:

```bash
python -m venv venv
```

Aktifkan virtual environment pada Windows:

```powershell
.\venv\Scripts\Activate.ps1
```

Install dependencies:

```bash
pip install -r requirements.txt
```

### 5. YOLO Model

Letakkan model YOLO pada direktori:

```text
ML/models/yolov8s.pt
```

Pastikan file model tersedia sebelum menjalankan FastAPI.

## Running the Application

SLOTIN membutuhkan tiga terminal untuk menjalankan seluruh layanan.

### Terminal 1 – Laravel

Dari direktori utama proyek:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Laravel dapat diakses melalui:

```text
http://127.0.0.1:8000
```

### Terminal 2 – FastAPI

Masuk ke folder ML:

```bash
cd ML
```

Aktifkan virtual environment:

```powershell
.\venv\Scripts\Activate.ps1
```

Jalankan FastAPI:

```bash
python -m uvicorn app:app --host 0.0.0.0 --port 8001 --reload
```

FastAPI berjalan pada:

```text
http://127.0.0.1:8001
```

### Terminal 3 – ngrok

Jalankan:

```bash
ngrok http 8001
```

Gunakan URL HTTPS yang diberikan ngrok untuk mengakses Camera Station melalui smartphone.

Contoh:

```text
https://your-ngrok-domain.ngrok-free.dev
```

Buka URL tersebut melalui browser smartphone dan izinkan akses kamera.

**Important:** Laravel tetap berjalan secara lokal pada komputer. ngrok hanya digunakan untuk mengekspos layanan FastAPI agar smartphone dapat mengakses Camera Station.

## API Endpoint

### Camera WebSocket

```text
/ws/camera?zone_id=1
```

Digunakan untuk menerima frame kamera dari smartphone dan menjalankan proses deteksi kendaraan.

### Zone Detection API

```http
POST /api/zone-detection
```

Digunakan oleh FastAPI untuk mengirimkan hasil deteksi kendaraan ke Laravel.

Contoh payload:

```json
{
    "zone_id": 1,
    "motor_count": 3,
    "processing_time_ms": 125.5
}
```

### Health Check

```http
GET /health
```

Digunakan untuk memeriksa status layanan FastAPI.

## Database

Database SLOTIN menggunakan MySQL dengan tabel utama:

| Table | Description |
|---|---|
| users | Data administrator |
| zones | Data zona dan kapasitas parkir |
| scan_histories | Riwayat hasil pemindaian |

## Project Information

**Project Name:** SLOTIN – Smart Parking System  
**Category:** Computer Vision / Smart Parking  
**Application Type:** Web-Based Application  
**Detection Model:** YOLOv8  
**Backend:** Laravel & FastAPI  
**Database:** MySQL

---

*Developed as an academic project in Informatics Engineering.*
