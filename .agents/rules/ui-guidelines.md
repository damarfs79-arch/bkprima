# Aturan Pengembangan & Styling UI (Berlaku untuk Seluruh Halaman Web)

## 1. Pertahankan Struktur Layout
- Jangan mengubah tata letak (layout), posisi komponen, maupun fitur yang sudah ada.
- Fokus hanya pada perbaikan estetika (styling), kerapian visual, dan kualitas antarmuka (UI).

## 2. Hilangkan Elemen Khas AI
- Hapus semua komponen "Pill Badge / Badge Melayang" berlatar bundar lonjong yang berada di atas judul/heading.
- Jika teks kategori/sub-judul tersebut tetap diperlukan, ubah menjadi 'Eyebrow Text' biasa (teks kecil biasa tanpa background melengkung/border, contoh: `<p class="text-xs font-bold uppercase tracking-wider text-blue-700 mb-2">...</p>`).

## 3. Penggunaan Ikon & Emoji
- **DILARANG KERAS MENGGUNAKAN EMOJI BAWAAN** (seperti 🚀, 💡, 📌, ✨, ✓, ✕, ⚠, dll.).
- Ganti seluruh ikon/emoji menggunakan kode SVG murni (atau pustaka Heroicons / Lucide) yang bersih, minimalis, dan berukuran proporsional.

## 4. Gaya Visual & Bahasa (Anti-AI)
- Gunakan skema warna yang lebih subtle/halus dan profesional (hindari kontras warna biru pekat atau gradien yang berlebihan).
- Gunakan warna latar netral yang terang/bersih (`#F8FAFC` / `bg-slate-50`).
- Pastikan copywriting/teks menggunakan istilah baku situs web resmi di Indonesia, singkat, padat, dan tidak dramatis.
