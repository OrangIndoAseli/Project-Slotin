# SLOTIN - Machine Learning Module

## Deskripsi
Modul Machine Learning SLOTIN digunakan untuk mendeteksi sepeda motor
dan menghitung okupansi zona parkir menggunakan YOLOv8s dan ByteTrack.

## Struktur
- models/yolov8s.pt : bobot model utama
- src/slotin_ai.py : modul deteksi dan penghitungan zona
- config/zones_config.json : konfigurasi zona
- results/ : hasil pengujian
- docs/ : dokumentasi teknis

## Model
- Model: YOLOv8s pretrained COCO
- Kelas motorcycle: 3
- Confidence: 0.15
- IoU: 0.60
- Image size: 1280
- Tracker: ByteTrack

## Aturan Okupansi
- 0 motor: KOSONG
- 1-3 motor: TERSEDIA
- 4 atau lebih: PENUH
- Belum dipindai/tidak terlihat: TIDAK TERPANTAU

Kapasitas standar: 4 motor per zona.

## Instalasi
Jalankan dari folder ML:

    pip install -r requirements.txt

## Penggunaan Modul
Contoh pemanggilan:

    from src.slotin_ai import SLOTIN_AI

    ai = SLOTIN_AI(
        model_path="models/yolov8s.pt",
        config_path="config/zones_config.json"
    )

Catatan: sesuaikan path dengan direktori kerja aplikasi.

## Integrasi Backend
Backend FastAPI menggunakan modul SLOTIN_AI untuk menjalankan
deteksi motor, tracking, penghitungan ROI, dan penyimpanan status zona.

Integrasi kamera real-time melalui WebSocket atau streaming frame
belum termasuk dalam modul prototype ini.

## Catatan
Tracking ID bukan identitas permanen kendaraan.
ROI harus dikalibrasi sesuai sudut dan posisi kamera.
Hasil pengujian prototype tidak menjamin akurasi pada semua kondisi.
