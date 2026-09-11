<?php

namespace App\Imports;

use App\Models\LoopStock;
use App\Models\LoopTransit;
use App\Models\RollColorMaster;
use App\Models\RollDetail;
use App\Models\RollQualityMaster;
use App\Models\VendorDetailMaster;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RollDetailsImport implements ToCollection,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        $_M_RollDetail = new RollDetail();
        $_M_VendorDetail = new VendorDetailMaster();
        $_M_RollQualityMaster = new RollQualityMaster();
        
        $_M_RollColor = new RollColorMaster();        
        $_M_LoopStock = new LoopStock();
        $file = request()->file('csvFile'); // Assuming the file input name is 'file'
        foreach ($rows as $row) {
            if(strtolower($file->getClientOriginalExtension())=="xlsx")
            {
                $row["purchase_date"] =  is_int($row["purchase_date"])? getDateColumnAttribute($row['purchase_date']) : $row['purchase_date'];
            }
            $color = $_M_RollColor->where(DB::raw("UPPER(color)"),strtoupper(trim($row["roll_color"])))->first()->color??"";
            if($row["roll_size"]<=2){
                $color = $_M_LoopStock->where(DB::raw("UPPER(loop_color)"),strtoupper(trim($row["roll_color"])))->first()->loop_color??"";
            }
            $row["roll_color"] = $color;
            $vendor = $_M_VendorDetail->where(DB::raw("upper(vendor_name)"),trim(strtoupper($row["vendor_name"])))->first();
            $quality = $_M_RollQualityMaster->where("vendor_id",$vendor->id)->where(DB::raw("upper(quality)"),trim(strtoupper($row["quality"])))->first();
            $row["vender_id"] = $vendor->id;
            $row["quality_id"] = $quality->id;
            $row["size"] = $row["roll_size"];
            $row["gsm"] = $row["roll_gsm"];
            $row["gsm_json"] = $row["bopp"] ? explode("/",$row["bopp"]):null;
            $row["length"] = $row["roll_length"];
            $row['roll_type'] = $row['roll_type']? $row['roll_type']: "NW";
            $row["purchase_date"] = $row["purchase_date"]?Carbon::parse($row["purchase_date"])->format("Y-m-d"):null;
            $request = new Request($row->toArray());            
            $_M_RollDetail->store($request);
        }
    }

}
