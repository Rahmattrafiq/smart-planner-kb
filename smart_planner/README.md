# Smart Planner Pengembangan Karier Mahasiswa (Goal-Based AI)

Tugas Akhir Mata Kuliah Kecerdasan Buatan — Kelompok Dream Career (Kelas IK24AB)

Sistem web yang membantu mahasiswa menyusun roadmap pengembangan karier
per semester menggunakan metode **Planning berbasis Goal-Based AI**
(forward-chaining, mirip STRIPS).

## Fitur

- Halaman Beranda (landing page)
- Form input data mahasiswa (skill, semester, target karier)
- Generator roadmap otomatis (Goal-Based AI Planner) — skill yang sudah
  dimiliki mahasiswa otomatis dilewati (skill-gap aware)
- Timeline roadmap per semester
- Detail semester dengan tab (Ringkasan, Skill, Sertifikasi, Portofolio, Magang)
- Checklist progres yang tersimpan ke database
- Unduh roadmap sebagai file teks
- Halaman Profil, Panduan, dan Tentang

## Struktur Proyek

```
smart_planner/
├── app.py                  # Routing Flask & controller
├── planner.py               # Goal-Based AI Planner (forward-chaining)
├── database.py               # Lapisan database SQLite
├── data/
│   └── career_data.py       # Knowledge base: aksi, prasyarat, goal per karier
├── templates/                # Halaman HTML (Jinja2)
├── static/
│   ├── css/style.css
│   └── js/main.js
├── requirements.txt
└── smart_planner.db          # Dibuat otomatis saat pertama dijalankan
```

## Cara Menjalankan

1. Pastikan Python 3.9+ sudah terpasang.
2. Buka terminal di folder `smart_planner/`, lalu buat virtual environment (opsional tapi disarankan):

   ```bash
   python -m venv venv
   # Windows
   venv\Scripts\activate
   # Mac/Linux
   source venv/bin/activate
   ```

3. Install dependency:

   ```bash
   pip install -r requirements.txt
   ```

4. Jalankan aplikasi:

   ```bash
   python app.py
   ```

5. Buka browser ke: **http://127.0.0.1:5000**

Database SQLite (`smart_planner.db`) akan dibuat otomatis saat aplikasi
pertama kali dijalankan — tidak perlu setup database manual.

## Cara Kerja Goal-Based AI Planner

Lihat `planner.py` dan `data/career_data.py`:

1. **Initial state** — skill yang dicentang mahasiswa di form diubah menjadi
   himpunan fakta awal (`build_initial_state`).
2. **Goal state** — setiap karier (`software_developer`, `data_analyst`,
   `uiux_designer`) punya daftar fakta tujuan (`goal`) di `career_data.py`.
3. **Aksi (actions)** — tiap aksi punya `pre` (prasyarat) dan menghasilkan
   1 fakta baru (`id` aksi itu sendiri) jika dijalankan.
4. **Forward-chaining** (`forward_chaining_plan`) — mengeksekusi semua aksi
   yang prasyaratnya sudah terpenuhi, berulang, sampai goal tercapai.
   Skill yang sudah dimiliki mahasiswa membuat aksi terkait otomatis
   terlewati (tidak masuk rencana).
5. Hasil rencana (`plan`) dikelompokkan ke 4 fase (Fondasi & Dasar,
   Pengembangan Skill, Portofolio & Sertifikasi, Magang & Persiapan
   Karier) dan dipetakan ke semester berjalan mahasiswa
   (`build_roadmap`).

## Menambah Karier Baru

Tambahkan entri baru ke dictionary `CAREERS` di `data/career_data.py`
dengan format yang sama (actions + goal). Sistem akan otomatis
menampilkannya sebagai opsi di form input.

## Catatan

Ini adalah versi awal/prototipe untuk keperluan Tugas Akhir. Beberapa
pengembangan lanjutan yang bisa dilakukan:
- Autentikasi multi-pengguna (saat ini 1 sesi browser = 1 mahasiswa aktif)
- Basis pengetahuan karier yang lebih luas
- Ekspor roadmap ke PDF
