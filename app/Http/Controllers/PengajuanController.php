<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengajuanController extends Controller
{
    /**
     * Helper to get unread notification count for sidebar badge.
     */
    protected function getUnreadNotifications($userId)
    {
        $unread = DB::table('notifikasi')
            ->where('id_penerima', $userId)
            ->where('sudah_dibaca', 0)
            ->count();

        if ($unread === 0) {
            $unread = DB::table('notifikasi')->where('sudah_dibaca', 0)->count();
        }

        return $unread;
    }

    /**
     * Helper to parse existing kemasan_berat_bersih string into (jenis, angka, satuan).
     */
    protected function parseKemasan($kemasanStr)
    {
        $kemasanJenis = '';
        $kemasanAngka = '';
        $kemasanSatuan = 'g';

        if (!empty($kemasanStr)) {
            // Contoh format: "Standing pouch / 100 gram", "Botol / 250 ml", "100 g"
            if (preg_match('/^(.*?)(?:\s*\/\s*)?(\d+(?:[.,]\d+)?)\s*(g|kg|ml|L|pcs|gram|liter)?$/i', trim($kemasanStr), $matches)) {
                $kemasanJenis = trim($matches[1]);
                $kemasanAngka = $matches[2];
                $satuanRaw = strtolower($matches[3] ?? 'g');
                if ($satuanRaw === 'gram') {
                    $kemasanSatuan = 'g';
                } elseif ($satuanRaw === 'liter') {
                    $kemasanSatuan = 'L';
                } elseif (in_array($matches[3] ?? '', ['g', 'kg', 'ml', 'L', 'pcs'])) {
                    $kemasanSatuan = $matches[3];
                }
            } else {
                $kemasanJenis = $kemasanStr;
            }
        }

        return [$kemasanJenis, $kemasanAngka, $kemasanSatuan];
    }

    /**
     * 1. Daftar Pengajuan:
     * Pencarian (nomor pengajuan atau nama usaha), filter status, pagination.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $unreadNotifications = $this->getUnreadNotifications($user->id_user);

        $search = trim($request->query('search', ''));
        $status = trim($request->query('status', ''));

        $query = DB::table('pengajuan')
            ->join('pelaku_usaha', 'pengajuan.id_pelaku_usaha', '=', 'pelaku_usaha.id_pelaku_usaha')
            ->leftJoin('produk', 'pengajuan.id_pengajuan', '=', 'produk.id_pengajuan')
            ->select(
                'pengajuan.id_pengajuan',
                'pengajuan.nomor_pengajuan',
                'pengajuan.tanggal_pengajuan',
                'pengajuan.status_pengajuan',
                'pengajuan.sumber_data',
                'pelaku_usaha.nama_usaha',
                'produk.nama_produk'
            );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('pengajuan.nomor_pengajuan', 'LIKE', '%' . $search . '%')
                  ->orWhere('pelaku_usaha.nama_usaha', 'LIKE', '%' . $search . '%');
            });
        }

        if (!empty($status) && $status !== 'Semua status') {
            $query->where('pengajuan.status_pengajuan', $status);
        }

        // Urutkan pengajuan terbaru
        $pengajuanList = $query
            ->orderBy('pengajuan.tanggal_pengajuan', 'desc')
            ->orderBy('pengajuan.id_pengajuan', 'desc')
            ->paginate(10)
            ->withQueryString();

        $totalCount = DB::table('pengajuan')->count();

        return view('admin.pengajuan', [
            'viewMode' => 'index',
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'pengajuanList' => $pengajuanList,
            'search' => $search,
            'status' => $status,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * 2. Detail Pengajuan dengan 3 tab (04.1 Pelaku Usaha, 04.2 Produk, 04.3 Bahan).
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $unreadNotifications = $this->getUnreadNotifications($user->id_user);

        $pengajuan = DB::table('pengajuan')->where('id_pengajuan', $id)->first();
        if (!$pengajuan) {
            return redirect()->route('admin.pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $pelaku = DB::table('pelaku_usaha')->where('id_pelaku_usaha', $pengajuan->id_pelaku_usaha)->first();
        $produk = DB::table('produk')->where('id_pengajuan', $pengajuan->id_pengajuan)->first();

        $bahanList = collect();
        if ($produk) {
            $bahanList = DB::table('bahan')->where('id_produk', $produk->id_produk)->get();
        }

        $activeTab = $request->query('tab', 'pelaku_usaha');
        if (!in_array($activeTab, ['pelaku_usaha', 'produk', 'bahan'])) {
            $activeTab = 'pelaku_usaha';
        }

        return view('admin.pengajuan', [
            'viewMode' => 'show',
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'pengajuan' => $pengajuan,
            'pelaku' => $pelaku,
            'produk' => $produk,
            'bahanList' => $bahanList,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Form Tambah Data Manual (04.5).
     * sumber_data = 'Manual', nomor_pengajuan format REG-2026-NNNNN.
     */
    public function create()
    {
        $user = Auth::user();
        $unreadNotifications = $this->getUnreadNotifications($user->id_user);

        // Cari nomor pengajuan terbesar yang ada
        $latestNomor = DB::table('pengajuan')
            ->where('nomor_pengajuan', 'LIKE', 'REG-2026-%')
            ->orderBy('nomor_pengajuan', 'desc')
            ->value('nomor_pengajuan');

        $nextNumber = 1286;
        if ($latestNomor && preg_match('/REG-2026-(\d+)/', $latestNomor, $m)) {
            $nextNumber = ((int)$m[1]) + 1;
        }
        $nomorPengajuan = sprintf('REG-2026-%05d', $nextNumber);

        return view('admin.pengajuan', [
            'viewMode' => 'form',
            'isEdit' => false,
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'nomorPengajuan' => $nomorPengajuan,
            'pengajuan' => null,
            'pelaku' => null,
            'produk' => null,
            'bahan' => null,
            'kemasanJenis' => '',
            'kemasanAngka' => '',
            'kemasanSatuan' => 'g',
        ]);
    }

    /**
     * Simpan Data Pengajuan Manual baru dalam 1 transaksi database.
     */
    public function store(Request $request)
    {
        // 1. Definisikan aturan validasi dasar
        $rules = [
            // Pelaku Usaha
            'nama_usaha' => ['required', 'string', 'max:150'],
            'nama_penanggung_jawab' => ['required', 'string', 'max:100'],
            'nib' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'skala_usaha' => ['nullable', Rule::in(config('pilihan.skala_usaha', []))],
            'email' => ['nullable', 'email', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_pengajuan' => ['required', 'date'],

            // Produk
            'nama_produk' => ['required', 'string', 'max:150'],
            'merek_dagang' => ['nullable', 'string', 'max:100'],
            'varian_produk' => ['nullable', 'string', 'max:100'],
            'kemasan_angka' => ['nullable', 'numeric', 'min:0'],
            'kemasan_satuan' => ['nullable', Rule::in(config('pilihan.satuan_kemasan', []))],
            'kemasan_jenis' => ['nullable', 'string', 'max:100'],
            'lokasi_produksi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],

            // Bahan
            'nama_bahan' => ['required', 'string', 'max:150'],
            'merek_produsen' => ['nullable', 'string', 'max:150'],
            'keterangan_bahan' => ['nullable', 'string'],
        ];

        // Validasi kota_provinsi: jika 'Lainnya' dipilih, wajib isi kota_provinsi_custom; jika bukan, Rule::in
        if ($request->input('kota_provinsi') === 'Lainnya') {
            $rules['kota_provinsi_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['kota_provinsi'] = ['nullable', Rule::in(config('pilihan.kota_provinsi', []))];
        }

        // Validasi jenis_usaha: jika 'Lainnya' dipilih, wajib isi jenis_usaha_custom; jika bukan, Rule::in
        if ($request->input('jenis_usaha') === 'Lainnya') {
            $rules['jenis_usaha_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_usaha'] = ['nullable', Rule::in(config('pilihan.jenis_usaha', []))];
        }

        // Validasi jenis_produk: jika 'Lainnya' dipilih, wajib isi jenis_produk_custom; jika bukan, Rule::in
        if ($request->input('jenis_produk') === 'Lainnya') {
            $rules['jenis_produk_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_produk'] = ['required', Rule::in(config('pilihan.jenis_produk', []))];
        }

        // Validasi jenis_bahan: jika 'Lainnya' dipilih, wajib isi jenis_bahan_custom; jika bukan, Rule::in
        if ($request->input('jenis_bahan') === 'Lainnya') {
            $rules['jenis_bahan_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_bahan'] = ['required', Rule::in(config('pilihan.jenis_bahan', []))];
        }

        $messages = [
            'nama_usaha.required' => 'Nama Usaha wajib diisi.',
            'nama_penanggung_jawab.required' => 'Nama Penanggung Jawab wajib diisi.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'jenis_produk.required' => 'Jenis Produk wajib dipilih.',
            'jenis_produk_custom.required' => 'Jenis Produk lainnya wajib diisi.',
            'nama_bahan.required' => 'Nama Bahan wajib diisi.',
            'jenis_bahan.required' => 'Jenis Bahan wajib dipilih.',
            'jenis_bahan_custom.required' => 'Jenis Bahan lainnya wajib diisi.',
            'kota_provinsi_custom.required' => 'Kabupaten/Kota lainnya wajib diisi.',
            'jenis_usaha_custom.required' => 'Jenis Usaha lainnya wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berupa angka.',
            'nib.max' => 'NIB maksimal 20 digit.',
            'email.email' => 'Format email usaha tidak valid.',
            'kemasan_angka.numeric' => 'Isian angka kemasan/berat harus berupa angka.',
            'kemasan_angka.min' => 'Isian angka kemasan/berat tidak boleh negatif.',
        ];

        $request->validate($rules, $messages);

        // 2. Olah nilai input pilihan (termasuk 'Lainnya')
        $finalKotaProvinsi = ($request->input('kota_provinsi') === 'Lainnya')
            ? $request->input('kota_provinsi_custom')
            : $request->input('kota_provinsi');

        $finalJenisUsaha = ($request->input('jenis_usaha') === 'Lainnya')
            ? $request->input('jenis_usaha_custom')
            : $request->input('jenis_usaha');

        $finalJenisProduk = ($request->input('jenis_produk') === 'Lainnya')
            ? $request->input('jenis_produk_custom')
            : $request->input('jenis_produk');

        // Untuk jenis_bahan: jika 'Lainnya', simpan custom text ke keterangan jika di luar ENUM database
        $keteranganBahan = $request->input('keterangan_bahan');
        $validEnumsBahan = ['Bahan Baku', 'Bahan Penolong', 'Bahan Tambahan'];
        if ($request->input('jenis_bahan') === 'Lainnya') {
            $customBahan = $request->input('jenis_bahan_custom');
            if (in_array($customBahan, $validEnumsBahan)) {
                $finalJenisBahan = $customBahan;
            } else {
                $finalJenisBahan = null; // Sesuai kolom jenis_bahan ENUM NULL
                $keteranganBahan = trim(($customBahan ? "[Jenis: {$customBahan}] " : '') . ($keteranganBahan ?? ''));
            }
        } else {
            $finalJenisBahan = $request->input('jenis_bahan');
        }

        // Susun string kemasan_berat_bersih
        $finalKemasan = null;
        if ($request->filled('kemasan_angka')) {
            $satuan = $request->input('kemasan_satuan', 'g');
            $berat = $request->input('kemasan_angka') . ' ' . $satuan;
            $finalKemasan = $request->filled('kemasan_jenis')
                ? ($request->input('kemasan_jenis') . ' / ' . $berat)
                : $berat;
        } elseif ($request->filled('kemasan_jenis')) {
            $finalKemasan = $request->input('kemasan_jenis');
        }

        // 3. Simpan dalam satu database transaction
        $idPengajuan = DB::transaction(function () use ($request, $finalKotaProvinsi, $finalJenisUsaha, $finalJenisProduk, $finalJenisBahan, $keteranganBahan, $finalKemasan) {
            // Hitung nomor pengajuan unik terbesar
            $latestNomor = DB::table('pengajuan')
                ->where('nomor_pengajuan', 'LIKE', 'REG-2026-%')
                ->lockForUpdate()
                ->orderBy('nomor_pengajuan', 'desc')
                ->value('nomor_pengajuan');

            $nextNumber = 1286;
            if ($latestNomor && preg_match('/REG-2026-(\d+)/', $latestNomor, $m)) {
                $nextNumber = ((int)$m[1]) + 1;
            }
            $nomorPengajuan = sprintf('REG-2026-%05d', $nextNumber);

            // a. Simpan tabel pelaku_usaha
            $idPelaku = DB::table('pelaku_usaha')->insertGetId([
                'nama_usaha' => $request->nama_usaha,
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab,
                'nib' => $request->nib,
                'skala_usaha' => $request->skala_usaha,
                'alamat' => $request->alamat,
                'kota_provinsi' => $finalKotaProvinsi,
                'no_telepon' => $request->no_telepon,
                'email' => $request->email,
                'jenis_usaha' => $finalJenisUsaha,
                'created_at' => now(),
            ]);

            // b. Simpan tabel pengajuan
            $idPengajuan = DB::table('pengajuan')->insertGetId([
                'nomor_pengajuan' => $nomorPengajuan,
                'id_pelaku_usaha' => $idPelaku,
                'id_sinkronisasi' => null,
                'jenis_pengajuan' => 'Sertifikasi Halal Reguler',
                'tanggal_pengajuan' => $request->filled('tanggal_pengajuan') ? $request->tanggal_pengajuan : now()->toDateString(),
                'status_pengajuan' => 'Menunggu Verifikasi',
                'sumber_data' => 'Manual',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // c. Simpan tabel produk
            $idProduk = DB::table('produk')->insertGetId([
                'id_pengajuan' => $idPengajuan,
                'nama_produk' => $request->nama_produk,
                'merek_dagang' => $request->merek_dagang,
                'jenis_produk' => $finalJenisProduk,
                'varian_produk' => $request->varian_produk,
                'kemasan_berat_bersih' => $finalKemasan,
                'lokasi_produksi' => $request->lokasi_produksi,
                'deskripsi' => $request->deskripsi,
            ]);

            // d. Simpan tabel bahan
            DB::table('bahan')->insert([
                'id_produk' => $idProduk,
                'nama_bahan' => $request->nama_bahan,
                'merek_produsen' => $request->merek_produsen,
                'jenis_bahan' => $finalJenisBahan,
                'keterangan' => $keteranganBahan,
            ]);

            return $idPengajuan;
        });

        return redirect()->route('admin.pengajuan.success', $idPengajuan)
            ->with('status_saved', 'created');
    }

    /**
     * Form Edit Data Pengajuan (04.4).
     */
    public function edit($id)
    {
        $user = Auth::user();
        $unreadNotifications = $this->getUnreadNotifications($user->id_user);

        $pengajuan = DB::table('pengajuan')->where('id_pengajuan', $id)->first();
        if (!$pengajuan) {
            return redirect()->route('admin.pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $pelaku = DB::table('pelaku_usaha')->where('id_pelaku_usaha', $pengajuan->id_pelaku_usaha)->first();
        $produk = DB::table('produk')->where('id_pengajuan', $pengajuan->id_pengajuan)->first();

        $bahan = null;
        if ($produk) {
            $bahan = DB::table('bahan')->where('id_produk', $produk->id_produk)->first();
        }

        [$kemasanJenis, $kemasanAngka, $kemasanSatuan] = $this->parseKemasan($produk->kemasan_berat_bersih ?? '');

        return view('admin.pengajuan', [
            'viewMode' => 'form',
            'isEdit' => true,
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'nomorPengajuan' => $pengajuan->nomor_pengajuan,
            'pengajuan' => $pengajuan,
            'pelaku' => $pelaku,
            'produk' => $produk,
            'bahan' => $bahan,
            'kemasanJenis' => $kemasanJenis,
            'kemasanAngka' => $kemasanAngka,
            'kemasanSatuan' => $kemasanSatuan,
        ]);
    }

    /**
     * Update Data Pengajuan dalam 1 transaksi database.
     */
    public function update(Request $request, $id)
    {
        $pengajuan = DB::table('pengajuan')->where('id_pengajuan', $id)->first();
        if (!$pengajuan) {
            return redirect()->route('admin.pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $pelaku = DB::table('pelaku_usaha')->where('id_pelaku_usaha', $pengajuan->id_pelaku_usaha)->first();
        $produk = DB::table('produk')->where('id_pengajuan', $pengajuan->id_pengajuan)->first();
        $bahan = $produk ? DB::table('bahan')->where('id_produk', $produk->id_produk)->first() : null;

        // Daftar nilai yang diperbolehkan termasuk nilai lama dari database agar data tidak hilang (Requirement 7 & 8)
        $allowedKota = array_filter(array_merge(config('pilihan.kota_provinsi', []), [$pelaku->kota_provinsi ?? '']));
        $allowedJenisUsaha = array_filter(array_merge(config('pilihan.jenis_usaha', []), [$pelaku->jenis_usaha ?? '']));
        $allowedJenisProduk = array_filter(array_merge(config('pilihan.jenis_produk', []), [$produk->jenis_produk ?? '']));
        $allowedJenisBahan = array_filter(array_merge(config('pilihan.jenis_bahan', []), [$bahan->jenis_bahan ?? '']));

        $rules = [
            // Pelaku Usaha
            'nama_usaha' => ['required', 'string', 'max:150'],
            'nama_penanggung_jawab' => ['required', 'string', 'max:100'],
            'nib' => ['nullable', 'regex:/^[0-9]+$/', 'max:20'],
            'skala_usaha' => ['nullable', Rule::in(array_filter(array_merge(config('pilihan.skala_usaha', []), [$pelaku->skala_usaha ?? ''])))],
            'email' => ['nullable', 'email', 'max:100'],
            'no_telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'tanggal_pengajuan' => ['required', 'date'],

            // Produk
            'nama_produk' => ['required', 'string', 'max:150'],
            'merek_dagang' => ['nullable', 'string', 'max:100'],
            'varian_produk' => ['nullable', 'string', 'max:100'],
            'kemasan_angka' => ['nullable', 'numeric', 'min:0'],
            'kemasan_satuan' => ['nullable', Rule::in(config('pilihan.satuan_kemasan', []))],
            'kemasan_jenis' => ['nullable', 'string', 'max:100'],
            'lokasi_produksi' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],

            // Bahan
            'nama_bahan' => ['required', 'string', 'max:150'],
            'merek_produsen' => ['nullable', 'string', 'max:150'],
            'keterangan_bahan' => ['nullable', 'string'],

            // Status (opsional)
            'status_pengajuan' => ['nullable', Rule::in(config('pilihan.status_pengajuan', []))],
        ];

        // Validasi kota_provinsi
        if ($request->input('kota_provinsi') === 'Lainnya') {
            $rules['kota_provinsi_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['kota_provinsi'] = ['nullable', Rule::in($allowedKota)];
        }

        // Validasi jenis_usaha
        if ($request->input('jenis_usaha') === 'Lainnya') {
            $rules['jenis_usaha_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_usaha'] = ['nullable', Rule::in($allowedJenisUsaha)];
        }

        // Validasi jenis_produk
        if ($request->input('jenis_produk') === 'Lainnya') {
            $rules['jenis_produk_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_produk'] = ['required', Rule::in($allowedJenisProduk)];
        }

        // Validasi jenis_bahan
        if ($request->input('jenis_bahan') === 'Lainnya') {
            $rules['jenis_bahan_custom'] = ['required', 'string', 'max:100'];
        } else {
            $rules['jenis_bahan'] = ['required', Rule::in($allowedJenisBahan)];
        }

        $messages = [
            'nama_usaha.required' => 'Nama Usaha wajib diisi.',
            'nama_penanggung_jawab.required' => 'Nama Penanggung Jawab wajib diisi.',
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'jenis_produk.required' => 'Jenis Produk wajib dipilih.',
            'jenis_produk_custom.required' => 'Jenis Produk lainnya wajib diisi.',
            'nama_bahan.required' => 'Nama Bahan wajib diisi.',
            'jenis_bahan.required' => 'Jenis Bahan wajib dipilih.',
            'jenis_bahan_custom.required' => 'Jenis Bahan lainnya wajib diisi.',
            'kota_provinsi_custom.required' => 'Kabupaten/Kota lainnya wajib diisi.',
            'jenis_usaha_custom.required' => 'Jenis Usaha lainnya wajib diisi.',
            'nib.regex' => 'NIB hanya boleh berupa angka.',
            'nib.max' => 'NIB maksimal 20 digit.',
            'email.email' => 'Format email usaha tidak valid.',
            'kemasan_angka.numeric' => 'Isian angka kemasan/berat harus berupa angka.',
            'kemasan_angka.min' => 'Isian angka kemasan/berat tidak boleh negatif.',
        ];

        $request->validate($rules, $messages);

        // Olah nilai input pilihan (termasuk 'Lainnya')
        $finalKotaProvinsi = ($request->input('kota_provinsi') === 'Lainnya')
            ? $request->input('kota_provinsi_custom')
            : $request->input('kota_provinsi');

        $finalJenisUsaha = ($request->input('jenis_usaha') === 'Lainnya')
            ? $request->input('jenis_usaha_custom')
            : $request->input('jenis_usaha');

        $finalJenisProduk = ($request->input('jenis_produk') === 'Lainnya')
            ? $request->input('jenis_produk_custom')
            : $request->input('jenis_produk');

        $keteranganBahan = $request->input('keterangan_bahan');
        $validEnumsBahan = ['Bahan Baku', 'Bahan Penolong', 'Bahan Tambahan'];
        if ($request->input('jenis_bahan') === 'Lainnya') {
            $customBahan = $request->input('jenis_bahan_custom');
            if (in_array($customBahan, $validEnumsBahan)) {
                $finalJenisBahan = $customBahan;
            } else {
                $finalJenisBahan = null;
                $keteranganBahan = trim(($customBahan ? "[Jenis: {$customBahan}] " : '') . ($keteranganBahan ?? ''));
            }
        } else {
            $finalJenisBahan = $request->input('jenis_bahan');
        }

        $finalKemasan = null;
        if ($request->filled('kemasan_angka')) {
            $satuan = $request->input('kemasan_satuan', 'g');
            $berat = $request->input('kemasan_angka') . ' ' . $satuan;
            $finalKemasan = $request->filled('kemasan_jenis')
                ? ($request->input('kemasan_jenis') . ' / ' . $berat)
                : $berat;
        } elseif ($request->filled('kemasan_jenis')) {
            $finalKemasan = $request->input('kemasan_jenis');
        }

        DB::transaction(function () use ($request, $pengajuan, $finalKotaProvinsi, $finalJenisUsaha, $finalJenisProduk, $finalJenisBahan, $keteranganBahan, $finalKemasan) {
            // 1. Update pelaku_usaha
            DB::table('pelaku_usaha')->where('id_pelaku_usaha', $pengajuan->id_pelaku_usaha)->update([
                'nama_usaha' => $request->nama_usaha,
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab,
                'nib' => $request->nib,
                'skala_usaha' => $request->skala_usaha,
                'alamat' => $request->alamat,
                'kota_provinsi' => $finalKotaProvinsi,
                'no_telepon' => $request->no_telepon,
                'email' => $request->email,
                'jenis_usaha' => $finalJenisUsaha,
            ]);

            // 2. Update pengajuan
            $pengajuanUpdate = [
                'updated_at' => now(),
            ];
            if ($request->filled('tanggal_pengajuan')) {
                $pengajuanUpdate['tanggal_pengajuan'] = $request->tanggal_pengajuan;
            }
            if ($request->filled('status_pengajuan')) {
                $pengajuanUpdate['status_pengajuan'] = $request->status_pengajuan;
            }
            DB::table('pengajuan')->where('id_pengajuan', $pengajuan->id_pengajuan)->update($pengajuanUpdate);

            // 3. Update atau insert produk
            $produk = DB::table('produk')->where('id_pengajuan', $pengajuan->id_pengajuan)->first();
            $produkData = [
                'nama_produk' => $request->nama_produk,
                'merek_dagang' => $request->merek_dagang,
                'jenis_produk' => $finalJenisProduk,
                'varian_produk' => $request->varian_produk,
                'kemasan_berat_bersih' => $finalKemasan,
                'lokasi_produksi' => $request->lokasi_produksi,
                'deskripsi' => $request->deskripsi,
            ];

            if ($produk) {
                DB::table('produk')->where('id_produk', $produk->id_produk)->update($produkData);
                $idProduk = $produk->id_produk;
            } else {
                $produkData['id_pengajuan'] = $pengajuan->id_pengajuan;
                $idProduk = DB::table('produk')->insertGetId($produkData);
            }

            // 4. Update atau insert bahan
            $bahan = DB::table('bahan')->where('id_produk', $idProduk)->first();
            $bahanData = [
                'nama_bahan' => $request->nama_bahan,
                'merek_produsen' => $request->merek_produsen,
                'jenis_bahan' => $finalJenisBahan,
                'keterangan' => $keteranganBahan,
            ];

            if ($bahan) {
                DB::table('bahan')->where('id_bahan', $bahan->id_bahan)->update($bahanData);
            } else {
                $bahanData['id_produk'] = $idProduk;
                DB::table('bahan')->insert($bahanData);
            }
        });

        return redirect()->route('admin.pengajuan.success', $id)
            ->with('status_saved', 'updated');
    }

    /**
     * 4. Tampilan 04.6: Konfirmasi Data Berhasil Disimpan.
     */
    public function success($id)
    {
        $user = Auth::user();
        $unreadNotifications = $this->getUnreadNotifications($user->id_user);

        $pengajuan = DB::table('pengajuan')->where('id_pengajuan', $id)->first();
        if (!$pengajuan) {
            return redirect()->route('admin.pengajuan')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $pelaku = DB::table('pelaku_usaha')->where('id_pelaku_usaha', $pengajuan->id_pelaku_usaha)->first();
        $produk = DB::table('produk')->where('id_pengajuan', $pengajuan->id_pengajuan)->first();

        return view('admin.pengajuan', [
            'viewMode' => 'success',
            'user' => $user,
            'unreadNotifications' => $unreadNotifications,
            'pengajuan' => $pengajuan,
            'pelaku' => $pelaku,
            'produk' => $produk,
        ]);
    }
}
