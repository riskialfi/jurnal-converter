📄 Deskripsi Website
Website ini merupakan alat konversi jurnal berbasis web yang dirancang untuk memudahkan pengguna dalam mengubah format jurnal akademik sesuai dengan template atau panduan tertentu. Sistem ini menyediakan fitur unggahan ganda, pemrosesan konversi multi-tahap, serta dukungan berbagai format file.

🚀 Fitur Utama
1. Upload Ganda
Unggah Dokumen Jurnal: Mendukung format PDF dan DOCX.

Unggah Template Referensi: Template format tujuan dalam LaTeX.

2. Proses Konversi Multi-Tahap
Ekstraksi Konten: Menggunakan PHP untuk membaca dan mengurai isi dokumen.

Reformatting Dokumen: Menggunakan Python untuk menyesuaikan struktur dan gaya dokumen.

Kompilasi Akhir: Menggunakan LaTeX (via pdflatex atau xelatex) untuk menghasilkan dokumen akhir yang terstruktur dengan baik.

3. Output yang Dihasilkan
Format akhir: DOCX yang telah diformat sesuai template.

📦 Dukungan Format
Input: PDF, DOCX, LaTeX

Output: DOCX

🧰 Teknologi yang Digunakan
Komponen	Teknologi
Frontend	HTML5, JavaScript (jQuery)
Backend	PHP 8+
Processing	Python 3 (PyPDF2, python-docx)
Typesetting	LaTeX (pdflatex / xelatex)
Server	Apache / Nginx
