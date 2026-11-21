<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Skala Usaha (sesuai ENUM tabel pelaku_usaha di SQL)
    |--------------------------------------------------------------------------
    */
    'skala_usaha' => [
        'Usaha Mikro',
        'Usaha Kecil',
        'Usaha Menengah',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Pengajuan (sesuai ENUM tabel pengajuan di SQL)
    |--------------------------------------------------------------------------
    */
    'status_pengajuan' => [
        'Menunggu Verifikasi',
        'Ditugaskan',
        'Sedang Diperiksa',
        'Perlu Perbaikan',
        'Selesai',
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenis Pengajuan (sesuai tabel pengajuan di SQL)
    |--------------------------------------------------------------------------
    */
    'jenis_pengajuan' => [
        'Sertifikasi Halal Reguler',
        'Sertifikasi Halal Self Declare',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kota / Kabupaten Jawa Timur (format: "Nama Kota/Kabupaten, Jawa Timur") + "Lainnya"
    |--------------------------------------------------------------------------
    */
    'kota_provinsi' => [
        'Surabaya, Jawa Timur',
        'Sidoarjo, Jawa Timur',
        'Gresik, Jawa Timur',
        'Mojokerto, Jawa Timur',
        'Kota Mojokerto, Jawa Timur',
        'Pasuruan, Jawa Timur',
        'Kota Pasuruan, Jawa Timur',
        'Malang, Jawa Timur',
        'Kota Malang, Jawa Timur',
        'Kota Batu, Jawa Timur',
        'Jombang, Jawa Timur',
        'Lamongan, Jawa Timur',
        'Tuban, Jawa Timur',
        'Bojonegoro, Jawa Timur',
        'Madiun, Jawa Timur',
        'Kota Madiun, Jawa Timur',
        'Magetan, Jawa Timur',
        'Ngawi, Jawa Timur',
        'Ponorogo, Jawa Timur',
        'Pacitan, Jawa Timur',
        'Kediri, Jawa Timur',
        'Kota Kediri, Jawa Timur',
        'Blitar, Jawa Timur',
        'Kota Blitar, Jawa Timur',
        'Tulungagung, Jawa Timur',
        'Trenggalek, Jawa Timur',
        'Nganjuk, Jawa Timur',
        'Probolinggo, Jawa Timur',
        'Kota Probolinggo, Jawa Timur',
        'Lumajang, Jawa Timur',
        'Jember, Jawa Timur',
        'Bondowoso, Jawa Timur',
        'Situbondo, Jawa Timur',
        'Banyuwangi, Jawa Timur',
        'Bangkalan, Jawa Timur',
        'Sampang, Jawa Timur',
        'Pamekasan, Jawa Timur',
        'Sumenep, Jawa Timur',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenis Usaha
    | (Makanan Olahan, Minuman, Bakery dan Roti, Katering, Bumbu dan Saus, Daging Olahan, Lainnya)
    |--------------------------------------------------------------------------
    */
    'jenis_usaha' => [
        'Makanan Olahan',
        'Minuman',
        'Bakery dan Roti',
        'Katering',
        'Bumbu dan Saus',
        'Daging Olahan',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenis Produk (Kategori yang masuk akal + "Lainnya")
    |--------------------------------------------------------------------------
    */
    'jenis_produk' => [
        'Makanan Ringan',
        'Makanan Olahan',
        'Minuman Olahan',
        'Bakery, Roti & Kue',
        'Bumbu, Rempah & Saus',
        'Daging & Olahan Daging',
        'Susu & Olahan Susu',
        'Katering & Makanan Siap Saji',
        'Minyak, Lemak & Olahannya',
        'Perikanan & Olahan Ikan',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenis Bahan (sesuai ENUM tabel bahan di SQL + "Lainnya")
    |--------------------------------------------------------------------------
    */
    'jenis_bahan' => [
        'Bahan Baku',
        'Bahan Penolong',
        'Bahan Tambahan',
        'Lainnya',
    ],

    /*
    |--------------------------------------------------------------------------
    | Satuan Kemasan
    |--------------------------------------------------------------------------
    */
    'satuan_kemasan' => [
        'g',
        'kg',
        'ml',
        'L',
        'pcs',
    ],
];
