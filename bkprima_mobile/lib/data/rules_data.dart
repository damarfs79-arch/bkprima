class RuleItem {
  final String code;
  final String category;
  final String description;
  final int points;
  final String defaultPenalty;

  const RuleItem({
    required this.code,
    required this.category,
    required this.description,
    required this.points,
    required this.defaultPenalty,
  });
}

class RulesData {
  static const List<RuleItem> items = [
    RuleItem(
      code: 'DIS-01',
      category: 'Kedisiplinan & Waktu',
      description: 'Terlambat masuk sekolah jam ke-1 (pukul 07.00)',
      points: 5,
      defaultPenalty: 'Piket Gerbang & Pembinaan Lisan',
    ),
    RuleItem(
      code: 'DIS-02',
      category: 'Kedisiplinan & Waktu',
      description: 'Keluar lingkungan sekolah tanpa surat izin piket',
      points: 10,
      defaultPenalty: 'Tugas Kebersihan Lingkungan Sekolah',
    ),
    RuleItem(
      code: 'DIS-03',
      category: 'Kedisiplinan & Waktu',
      description: 'Tidak masuk sekolah tanpa keterangan (Alpa)',
      points: 10,
      defaultPenalty: 'Konfirmasi Wali Murid & Pembinaan Wali Kelas',
    ),
    RuleItem(
      code: 'DIS-04',
      category: 'Kedisiplinan & Waktu',
      description: 'Membolos saat jam pergantian pelajaran',
      points: 15,
      defaultPenalty: 'Panggilan Guru BK & Tugas Akademik Tambahan',
    ),
    RuleItem(
      code: 'ATR-01',
      category: 'Kerapian & Atribut',
      description: 'Atribut seragam tidak lengkap (badge/lokasi/dasi)',
      points: 5,
      defaultPenalty: 'Peringatan & Melengkapi Atribut',
    ),
    RuleItem(
      code: 'ATR-02',
      category: 'Kerapian & Atribut',
      description: 'Seragam tidak sesuai jadwal atau dicoret-coret',
      points: 10,
      defaultPenalty: 'Teguran Tertulis & Mengganti Seragam',
    ),
    RuleItem(
      code: 'ATR-03',
      category: 'Kerapian & Atribut',
      description: 'Sepatu tidak sesuai ketentuan (non-hitam polos)',
      points: 5,
      defaultPenalty: 'Peringatan Lisan',
    ),
    RuleItem(
      code: 'ATR-04',
      category: 'Kerapian & Atribut',
      description: 'Rambut tidak rapi / diwarnai (siswa putra)',
      points: 10,
      defaultPenalty: 'Tenggat Waktu Cukur Rapi 2 Hari',
    ),
    RuleItem(
      code: 'TIB-01',
      category: 'Ketertiban Lingkungan',
      description: 'Membuang sampah tidak pada tempatnya',
      points: 5,
      defaultPenalty: 'Operasi Semut Area Sekolah',
    ),
    RuleItem(
      code: 'TIB-02',
      category: 'Ketertiban Lingkungan',
      description: 'Memainkan gawai/HP saat pembelajaran tanpa izin guru',
      points: 10,
      defaultPenalty: 'Penahanan HP Sementara sampai Akhir KBM',
    ),
    RuleItem(
      code: 'BRT-01',
      category: 'Pelanggaran Khusus / Berat',
      description: 'Membawa, menghisap rokok / rokok elektrik (vape)',
      points: 25,
      defaultPenalty: 'Panggilan Orang Tua & Surat Peringatan (SP-1)',
    ),
    RuleItem(
      code: 'BRT-02',
      category: 'Pelanggaran Khusus / Berat',
      description: 'Terlibat perkelahian atau tawuran antarpelajar',
      points: 50,
      defaultPenalty: 'Panggilan Orang Tua & SP-2',
    ),
    RuleItem(
      code: 'BRT-03',
      category: 'Pelanggaran Khusus / Berat',
      description: 'Tindakan perundungan (bullying) fisik atau verbal',
      points: 50,
      defaultPenalty: 'Konseling Khusus BK & Surat Peringatan',
    ),
    RuleItem(
      code: 'BRT-04',
      category: 'Pelanggaran Khusus / Berat',
      description: 'Membawa senjata tajam atau konten asusila',
      points: 100,
      defaultPenalty: 'Skorsing & Konferensi Kasus (SP-3)',
    ),
  ];
}
