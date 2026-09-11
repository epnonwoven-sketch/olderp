<?php

namespace App\Imports;

use App\Models\BagTypeMaster;
use App\Models\ClientDetailMaster;
use App\Models\FareDetail;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
use App\Models\StereoDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderImport implements ToCollection,WithChunkReading,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    
    public function collection(Collection $rows)
    {
        $_M_OrderPunch = new OrderPunchDetail();
        $_M_RateTypeMaster = new RateTypeMaster();
        $_M_FareDetail = new FareDetail();
        $_M_StereoDetail = new StereoDetail();
        $_M_ClientDetail = new ClientDetailMaster();
        $_M_BagType = new BagTypeMaster();
        $clientList = $_M_ClientDetail->all();
        $bagTypeList = $_M_BagType->all();
        $broker = new OrderBroker();
        
        $file = request()->file('csvFile'); // Assuming the file input name is 'file'
        
        foreach ($rows as $row) {
            if(strtolower($file->getClientOriginalExtension())=="xlsx")
            {
                $row["order_date"] = is_int($row["order_date"])? getDateColumnAttribute($row['order_date']) : $row['order_date'];
                $row["estimate_delivery_date"] = is_int($row["estimate_delivery_date"])? getDateColumnAttribute($row['estimate_delivery_date']) : $row['estimate_delivery_date'];
            }
            if($row["bag_quality"]=="BOPP" || $row["bag_quality"]=="LAM"){
                $row["bag_gsm_json"] = explode("+",$row["bag_gsm"]);
                $row["bag_gsm"] = array_sum(explode("+",$row["bag_gsm"]));
            }
            if($row["bag_type"]!="B"){
                $row["bag_g"]=null;
            }
            if($row["agent_name"]){
                $row["broker_id"] = $broker->where("broker_name",$row["agent_name"])->first()->id??null;
            }

            $row["client_detail_id"] = $clientList->where("client_name",$row["client_name"])->first()->id;
            $row["is_delivered"] = trim($row["is_delivered"]) ? TRUE:FALSE;
            $row["booked_units"] = $row["booked_units"]? $row["booked_units"] : 0;
            $row["bag_type_id"] = Config::get("customConfig.bagTypeIdByShortName.".$row["bag_type"]);
            $row["bag_gsm"] = $row['bag_gsm'] ? explode(",",$row['bag_gsm']):null;
            $row["bag_printing_color"] = $row['bag_printing_color'] ? explode(",",$row['bag_printing_color']):null;    
            $row["bag_color"] = $row['bag_color'] ? explode(",",$row['bag_color']):null;           
            $row["order_date"] = $row["order_date"]?Carbon::parse($row["order_date"])->format("Y-m-d"):null;
            $row["estimate_delivery_date"] = $row["estimate_delivery_date"]?Carbon::parse($row["estimate_delivery_date"])->format("Y-m-d"):null;
            $request = new Request($row->toArray());     
            $_M_OrderPunch->store($request);   
            
        }
    }

    public function chunkSize(): int
    {
        return 200; // Process 100 rows per chunk
    }
}
