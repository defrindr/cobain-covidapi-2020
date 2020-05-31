# API covid-19

## information
Menyajikan data terkait covid-19 ter-khusus wilayah Jawa Timur

## Sample Request

* Mendapatkan data pasien covid di Indonesia

Request
```
site.com/
```
Response
```
{
    "success": true,
    "message": "data berhasil didapat",
    "data": [
    {
        "jumlah_kasus": 4557,
        "jumlah_sembuh": 380,
        "jumlah_meninggal": 399
    }],
    "count": 1
}
```



* Mendapatkan data pasien covid setiap provinsi di Indonesia

Request
```
site.com/province
```
Response
```
{
    "success": true,
    "message": "Data berhasil didapatkan",
    "data": [
    ...
    {
        "provinsi": "Jawa Tengah",
        "kasus_positif": 203,
        "kasus_sembuh": 19,
        "kasus_meninggal": 25
    }],
    "count": 34
}

```

Request
```
site.com/province/{province}
```
Response
```
{
    "success": true,
    "message": "Data berhasil didapatkan",
    "data": [
    {
        "provinsi": "Jawa Tengah",
        "kasus_positif": 203,
        "kasus_sembuh": 19,
        "kasus_meninggal": 25
    }],
    "count": 1
}

```


* Mendapatkan data pasien covid setiap wilayah di Jawa Timur

Request
```
site.com/jatim/{zone}
```
Response
```
{
    "success": true,
    "message": "Data berhasil didapatkan",
    "data": [
    {
        "zona": "Kab. Ponorogo",
        "jumlah_kasus": 367,
        "jumlah_odp": 341,
        "jumlah_pdp": 20,
        "jumlah_positif": 6,
        "jumlah_sembuh": 0,
        "jumlah_meninggal": 0
    }],
    "count": 1
}
```




* Mendapatkan data RS penanganan covid berdasarkan wilayah

Request
```
site.com/hospital?province={province}&zone={zone}
```
Response
```
{
    "success": true,
    "message": "Data berhasil didapatkan",
    "data": [
    {
        "nama_rs": "RS Aisyiyah Ponorogo",
        "provinsi": "Jawa Timur",
        "alamat": null,
        "telepon": "(0352) 461560",
        "jumlah_tenaga_medis": null,
        "jumlah_apd": null,
        "ruang_Iisolasi_biasa": null,
        "ruang_isolasi_tekanan": null,
        "ruang_isolasi_icu": null,
        "keterangan": "RS Rujukan Muhammadiyah & Aisyiyah"
    }],
    "count": 1
}
```
