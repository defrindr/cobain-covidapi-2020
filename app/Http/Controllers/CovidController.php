<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CovidController extends Controller
{

    protected $urlStatistic = 'https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/Statistik_Perkembangan_COVID19_Indonesia/FeatureServer/0/query?f=json&where=1=1&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&outStatistics=[{"statisticType":"sum","onStatisticField":"Jumlah_Kasus_Baru_per_hari","outStatisticFieldName":"jumlah_kasus"},{"statisticType":"max","onStatisticField":"Jumlah_Pasien_Sembuh","outStatisticFieldName":"jumlah_sembuh"},{"statisticType":"max","onStatisticField":"Jumlah_Pasien_Meninggal","outStatisticFieldName":"jumlah_meninggal"}]&outSR=102100&cacheHint=true';


    protected $urlPerProv = "https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/COVID19_Indonesia_per_Provinsi/FeatureServer/0/query?f=json&where=(Provinsi+like+'%{template}%')&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=*&orderByFields=Kasus_Posi&resultOffset=0&resultRecordCount=34&cacheHint=true";


    protected $urlJatim = "https://services8.arcgis.com/yTQgcgZWR10MGhD2/ArcGIS/rest/services/Data_Covid_Jatim/FeatureServer/0/query?where={whereTemplate}&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&resultType=none&outFields=*&returnGeometry=false&orderByFields=POSITIF+DESC&quantizationParameters=&f=json";


    protected $urlWorld = "https://services1.arcgis.com/0MSEUqKaxRlEPj5g/arcgis/rest/services/ncov_cases/FeatureServer/2/query?f=json&where={worldTemplate}&returnGeometry=false&spatialRel=esriSpatialRelIntersects&outFields=Country_Region,Confirmed,Deaths,Recovered,Active,Last_Update&orderByFields=Active+DESC&quantizationParameters=&cacheHint=true";


    protected $urlRs = "https://services5.arcgis.com/VS6HdKS0VfIhv8Ct/arcgis/rest/services/RS_Rujukan_COVID19_Indonesia/FeatureServer/0/query?f=json&where={template}&spatialRel=esriSpatialRelIntersects&outFields=*";





    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * index
     * @param  Request $r 
     * @return [type]     ["jumlah_kasus" => 0, "jumlah_sembuh" => 0, "jumlah_meninggal"]
     */
    public function index(Request $r){
            $sources = $this->request($this->urlStatistic);
            if(is_array($sources)){
                if($sources["success"] == false) return response()->json($sources);
            }
            $stat = $sources->features[0]->attributes;
            $schema = [
                "success" => true,
                "message" => "data berhasil didapat",
                "data" => [$stat],
                "count" => count([$stat]),
            ];

            return response()->json($schema);
    }


    /**
     * [province description]
     * @param  [type] $province [description]
     * @return [type]           [description]
     */
    public function province($province){
        $location = preg_replace('/\s/','+',$province);
        $url = preg_replace("/\{template\}/", $location, $this->urlPerProv);
        $sources = $this->request($url);
        if(is_array($sources)){
            if($sources["success"] == false) return response()->json($sources);
        }
        $data = $sources->features;

        if(count($data) <= 0){
            return response()->json([
                "success" => false,
                "message" => "Data tidak ditemukan",
                "data" => [],
                "count" => 0,
            ]);
        }else{
            $finalData = [];
            foreach ($data as $row) {
                array_push($finalData, [
                    "provinsi" => $row->attributes->Provinsi,
                    "kasus_positif" => $row->attributes->Kasus_Posi,
                    "kasus_sembuh" => $row->attributes->Kasus_Semb,
                    "kasus_meninggal" => $row->attributes->Kasus_Meni,
                ]);
            }
            return response()->json([
                "success" => true,
                "message" => "Data berhasil didapatkan",
                "data" => $finalData,
                "count" => count($finalData),
            ]);
        }
    }



    // "Nama": "RS Aisyiyah Ponorogo",
    // "Provinsi": "Jawa Timur",
    // "Alamat": null,
    // "Telepon": "(0352) 461560",
    // "Jumlah_Tenaga_Medis": null,
    // "Jumlah_APD": null,
    // "Ruang_Isolasi_Biasa": null,
    // "Ruang_Isolasi_Tekanan": null,
    // "Ruang_Isolasi_ICU": null,
    // "Keterangan": "RS Rujukan Muhammadiyah & Aisyiyah",
    // "X": 111.471631,
    // "Y": -7.86843,
    public function getRs(Request $r){
        // ( Provinsi = 'Jawa Timur' ) AND ( (Nama like '%ponorogo%')  OR (Alamat like '%ponorogo%') )
        $query = [];
        if(
            ($r->get('province') == null) &&
            ($r->get('zone') == null)
        ){
            array_push($query,"1=1");
        }
        if(
            ($r->get('province') != null)
        ){
            array_push($query, "( Provinsi like '%". $r->get('province') ."%' )");
        }
        if(
            ($r->get('zone') != null)
        ){
            array_push($query, "( Nama like '%". $r->get('zone') ."%' ) OR ".
                                "( Alamat like '%". $r->get('zone') ."%' )" );
        }

        $query = join(" AND ",$query);
        $url = preg_replace("/\{template\}/", $query, $this->urlRs);
        // dd($this->rSpace($url));
        $sources = $this->request($this->rSpace($url));
        if(is_array($sources)){
            if($sources["success"] == false) return response()->json($sources);
        }
        $data = $sources->features;


        if(count($data) <= 0) {
            return response()->json([
                "success" => false,
                "message" => "Data tidak ditemukan.",
                "data" => [],
                "count" => 0
            ]);
        }else{
            $finalData = [];
            foreach ($data as $row) {
                array_push($finalData, [
                    "nama_rs" => $row->attributes->Nama,
                    "provinsi" => $row->attributes->Provinsi,
                    "alamat" => $row->attributes->Alamat,
                    "telepon" => $row->attributes->Telepon,
                    "jumlah_tenaga_medis" => $row->attributes->Jumlah_Tenaga_Medis,
                    "jumlah_apd" => $row->attributes->Jumlah_APD,
                    "ruang_Iisolasi_biasa" => $row->attributes->Ruang_Isolasi_Biasa,
                    "ruang_isolasi_tekanan" => $row->attributes->Ruang_Isolasi_Tekanan,
                    "ruang_isolasi_icu" => $row->attributes->Ruang_Isolasi_ICU,
                    "keterangan" => $row->attributes->Keterangan,
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Data berhasil didapatkan",
                "data" => $finalData,
                "count" => count($finalData)
            ]);
        }


    }




    /**
     * [world description]
     * @param  [type] $nation [description]
     * @return [type]         [description]
     */
    public function world($nation){
        $url = preg_replace("/\{worldTemplate\}/", "(Country_Region like '%$nation%')", $this->urlWorld);
        $sources = $this->request($this->rSpace($url));
        if(is_array($sources)){
            if($sources["success"] == false) return response()->json($sources);
        }
        $data = $sources->features;

        if(count($data) <= 0){
            return response()->json([
                "success" => false,
                "message" => "Data tidak ditemukan",
                "data" => [],
                "count" => 0,
            ]);
        }else{
            $finalData = [];
            foreach ($data as $row) {
                array_push($finalData,[
                    "nama_negara" => $row->attributes->Country_Region,
                    "jumlah_positif" => $row->attributes->Confirmed,
                    "jumlah_meninngal" => $row->attributes->Deaths,
                    "jumlah_sembuh" => $row->attributes->Recovered,
                    "jumlah_kasus_aktif" => $row->attributes->Active,
                    "update_terakhir" => substr((string)$row->attributes->Last_Update,0,-3),
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Data berhasil didapatkan",
                "data" => $finalData,
                "count" => count($finalData),
            ]);
        }
    }




    /**
     * jatim
     * @param  string $zone 
     * @return array       [success => true, "message" => "msg", "data" => [
            "zona"
            "jumlah_kasus"
            "jumlah_odp"
            "jumlah_pdp"
            "jumlah_positif"
            "jumlah_sembuh"
            "jumlah_meninggal"
     * ], "count" => 0]
     */
    public function jatimall(){
        $url = preg_replace("/\{whereTemplate\}/", "1=1", $this->urlJatim);

        $sources = $this->request($this->rSpace($url));
        if(is_array($sources)){
            if($sources["success"] == false) return response()->json($sources);
        }
        $sources = $sources->features;
        
        if(count($sources) <= 0){
            return response()->json([
                "success" => false,
                "message" => "Data tidak ditemukan",
                "data" => [],
                "count" => 0,
            ]);
        }else{
            $finalData = [];

            foreach ($sources as $row) {
                array_push($finalData,[
                    "zona" => $row->attributes->KAB_KOTA,
                    "jumlah_kasus" => $row->attributes->JML_KASUS,
                    "jumlah_odp" => $row->attributes->ODP,
                    "jumlah_pdp" => $row->attributes->PDP,
                    "jumlah_positif" => $row->attributes->POSITIF,
                    "jumlah_sembuh" => $row->attributes->SEMBUH,
                    "jumlah_meninggal" => $row->attributes->MENINGGAL,
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Data berhasil didapatkan",
                "data" => $finalData,
                "count" => count($finalData),
            ]);
        }

    }




    /**
     * jatim
     * @param  string $zone 
     * @return array       [success => true, "message" => "msg", "data" => [
            "zona"
            "jumlah_kasus"
            "jumlah_odp"
            "jumlah_pdp"
            "jumlah_positif"
            "jumlah_sembuh"
            "jumlah_meninggal"
     * ], "count" => 0]
     */
    public function jatim($zone){
        $url = preg_replace("/\{whereTemplate\}/", "(KAB_KOTA like '%" .$zone. "%')", $this->urlJatim);

        $sources = $this->request($this->rSpace($url));
        if(is_array($sources)){
            if($sources["success"] == false) return response()->json($sources);
        }
        $sources = $sources->features;
        
        if(count($sources) <= 0){
            return response()->json([
                "success" => false,
                "message" => "Data tidak ditemukan",
                "data" => [],
                "count" => 0,
            ]);
        }else{
            $finalData = [];

            foreach ($sources as $row) {
                array_push($finalData,[
                    "zona" => $row->attributes->KAB_KOTA,
                    "jumlah_kasus" => $row->attributes->JML_KASUS,
                    "jumlah_odp" => $row->attributes->ODP,
                    "jumlah_pdp" => $row->attributes->PDP,
                    "jumlah_positif" => $row->attributes->POSITIF,
                    "jumlah_sembuh" => $row->attributes->SEMBUH,
                    "jumlah_meninggal" => $row->attributes->MENINGGAL,
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "Data berhasil didapatkan",
                "data" => $finalData,
                "count" => count($finalData),
            ]);
        }

    }


    /**
     * [rSpace description]
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    protected function rSpace($str){
        return preg_replace("/\s/","+",$str);
    }


    /**
     * [request description]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    protected function request($url){
        try{
            $dat = json_decode(file_get_contents($url));
            if($dat == null){
                return [
                    "success" => false,
                    "message" => "Sumber daya tidak tersedia",
                    "data" => [],
                    "count" => 0,
                ];
            }

            return $dat;
        }catch(\Exception $e){
            return [
                "success" => false,
                "message" => "Sumber daya tidak tersedia",
                "data" => [],
                "count" => 0,
            ];
        }
    }

}
