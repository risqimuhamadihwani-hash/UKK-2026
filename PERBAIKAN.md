PERBAIKAN UKK-2026

Perubahan:
1. Memperbaiki nama link menu Catatan Pelanggaran agar sesuai dengan nama file (case-sensitive).
2. Memperbaiki urutan tipe data bind_param() pada penyimpanan catatan pelanggaran.
3. Menambahkan filter Tahun Ajaran pada Laporan dan Rekap Poin, dengan tahun aktif sebagai pilihan awal.
4. Memastikan rekap poin hanya menghitung catatan pada tahun ajaran yang dipilih dan menghindari join penempatan kelas lintas tahun.
5. Memperbaiki cakupan laporan guru: wali kelas melihat siswa kelas yang diampu; guru biasa melihat catatan yang dibuatnya.
6. Menyesuaikan join kelas laporan agar mengikuti tahun ajaran catatan.

Catatan:
- Impor database db_ukk_2026.sql seperti biasa bila database belum tersedia.
- Sesuaikan kredensial database di config/koneksi.php untuk server Anda.
- Salin isi folder proyek ini ke htdocs/server web Anda.
- Pengujian yang dilakukan di sini adalah pemeriksaan sintaks PHP (php -l); koneksi ke database dan uji login langsung perlu dilakukan di lingkungan server Anda.
