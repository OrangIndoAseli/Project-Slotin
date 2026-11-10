# Dokumentasi Parameter Model SLOTIN

| Parameter | Nilai |
|---|---|
| Model | YOLOv8s pretrained COCO |
| Bobot | models/yolov8s.pt |
| Kelas motorcycle | 3 |
| Confidence | 0.15 |
| IoU | 0.60 |
| Image size | 1280 |
| Tracker | bytetrack.yaml |
| Kapasitas zona | 4 motor |

## Mekanisme
1. Frame diterima dari sumber video/kamera.
2. YOLO mendeteksi objek motorcycle.
3. ByteTrack membantu menghubungkan deteksi antar-frame.
4. Posisi objek diperiksa terhadap ROI zona.
5. Sistem menghitung ID tracking unik yang masuk ROI.
6. Jumlah digunakan untuk menentukan status okupansi.

## Batasan
- Tracking ID dapat berubah karena occlusion atau perpindahan kamera.
- ROI harus disesuaikan dengan sudut kamera.
- Penghitungan berdasarkan ID tracking perlu validasi pada kondisi nyata.
- Modul saat ini memproses kumpulan frame/video, belum streaming kamera langsung.
