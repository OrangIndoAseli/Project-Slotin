
import os
import json
import string
from datetime import datetime
from ultralytics import YOLO


class SLOTIN_AI:

    def __init__(self, model_path, config_path):
        self.model = YOLO(model_path)
        self.config_path = config_path

        with open(config_path, "r") as f:
            self.config = json.load(f)

        self.tracker = self.config.get("tracker", "bytetrack.yaml")
        self.conf = 0.15
        self.iou = 0.60
        self.imgsz = 1280
        self.kapasitas = 4

        if "zones" not in self.config:
            self.config["zones"] = []

    def nama_zona(self, urutan):
        huruf = string.ascii_uppercase
        hasil = ""

        while urutan >= 0:
            hasil = huruf[urutan % 26] + hasil
            urutan = urutan // 26 - 1

        return hasil

    def zona_berikutnya(self):
        nomor = 0
        tersimpan = {z["name"] for z in self.config["zones"]}

        while self.nama_zona(nomor) in tersimpan:
            nomor += 1

        return self.nama_zona(nomor)

    def buat_zona(self, nama_zona=None):
        if nama_zona is None:
            nama_zona = self.zona_berikutnya()

        for zona in self.config["zones"]:
            if zona["name"] == nama_zona:
                return zona

        data = {
            "name": nama_zona,
            "capacity": self.kapasitas,
            "motor_count": None,
            "status": "TIDAK TERPANTAU",
            "scanned": False,
            "track_ids": [],
            "last_scanned": None
        }

        self.simpan_zona(data)
        return data

    def status_zona(self, jumlah):
        if jumlah is None:
            return "TIDAK TERPANTAU"
        elif jumlah == 0:
            return "KOSONG"
        elif jumlah < self.kapasitas:
            return "TERSEDIA"
        return "PENUH"

    def simpan_zona(self, data):
        zones = self.config["zones"]

        for i, zona in enumerate(zones):
            if zona["name"] == data["name"]:
                zones[i] = data
                break
        else:
            zones.append(data)

        # Urutkan zona berdasarkan nama A, B, ..., Z, AA, AB
        zones.sort(
            key=lambda z: (
                len(z["name"]),
                z["name"]
            )
        )

        self.config["zones"] = zones

        with open(self.config_path, "w") as f:
            json.dump(self.config, f, indent=4)

    def scan_zona_frames(
        self,
        frames,
        nama_zona,
        batas_kiri,
        batas_kanan,
        batas_atas=0,
        batas_bawah=None
    ):
        if nama_zona not in ["A", "B", "C", "D"]:
            raise ValueError(
                "Pemindaian kamera SLOTIN versi ini hanya mendukung zona A-D."
            )

        if not frames:
            raise ValueError("Frame scan kosong!")

        id_motor_unik = set()
        gambar_terakhir = None

        for nomor_frame, frame in enumerate(frames):
            hasil = self.model.track(
                frame,
                persist=(nomor_frame > 0),
                tracker=self.tracker,
                classes=[3],
                conf=self.conf,
                iou=self.iou,
                imgsz=self.imgsz,
                verbose=False
            )[0]

            gambar_terakhir = hasil.plot()

            if hasil.boxes.id is None:
                continue

            for box, track_id in zip(
                hasil.boxes,
                hasil.boxes.id.cpu().tolist()
            ):
                x1, y1, x2, y2 = box.xyxy[0].cpu().tolist()

                tengah_x = (x1 + x2) / 2
                tengah_y = (y1 + y2) / 2

                masuk_zona = (
                    batas_kiri <= tengah_x < batas_kanan
                    and batas_atas <= tengah_y
                    and (
                        batas_bawah is None
                        or tengah_y <= batas_bawah
                    )
                )

                if masuk_zona:
                    id_motor_unik.add(int(track_id))

        jumlah = len(id_motor_unik)

        data = {
            "name": nama_zona,
            "capacity": self.kapasitas,
            "motor_count": jumlah,
            "status": self.status_zona(jumlah),
            "scanned": True,
            "track_ids": sorted(id_motor_unik),
            "last_scanned": datetime.now().isoformat()
        }

        self.simpan_zona(data)

        return data, gambar_terakhir

    def tandai_tidak_terpantau(self, nama_zona):
        data = {
            "name": nama_zona,
            "capacity": self.kapasitas,
            "motor_count": None,
            "status": "TIDAK TERPANTAU",
            "scanned": False,
            "track_ids": [],
            "last_scanned": None
        }

        self.simpan_zona(data)
        return data

    def daftar_zona(self):
        return self.config["zones"]

    def zona_dashboard(self):
        return [
            zona for zona in self.config["zones"]
            if zona["name"] in ["A", "B", "C", "D"]
        ]

    def semua_hasil_zona(self):
        return self.config["zones"]
