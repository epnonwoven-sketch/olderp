<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait Formula{
    public function calculatePossibleProduction($request){

        $variables = [
            "RL"=>$request->length,
            "RW"=>$request->netWeight,
            "RS"=>$request->size,
            "GSM"=>$request->gsm,
            "X"=>"*",
            "*"=>"*",
            "x"=>"*",
            "/"=>"/",
            "+"=>"+",
            "-"=>"-",
            "L"=>$request->bagL,
            "W"=>$request->bagW,
            "G"=>$request->bagG??0,
        ];  

        $formula = $request->formula;
        foreach ($variables as $key => $value) {                                               
            $formula = str_replace($key, $value, $formula);                        
        }
        $result = round(eval(" return ".$formula." ;"));
        return collect([
            "result" => $result,
            "unit" => $result ." ".$request->bookingBagUnits,
            "variables"=>$variables,
            "formula"=>$request->formula,
        ]);
    }

    public function gsmVariation($roll){
        $oldGsm = $roll->gsm_variation;
        $roll->gsm_variation = (((($roll->net_weight * 39.37 * 1000) / $roll->size)/$roll->length)-$roll->gsm)/$roll->gsm;
    }

    public function getCylinderSize($request){

        $variables = [
            "RL"=>$request->length,
            "RW"=>$request->netWeight,
            "RS"=>$request->size,
            "GSM"=>$request->gsm,
            "X"=>"*",
            "*"=>"*",
            "x"=>"*",
            "/"=>"/",
            "+"=>"+",
            "-"=>"-",
            "L"=>$request->l,
            "W"=>$request->w,
            "G"=>$request->g??0,
        ];  

        $formula = $request->formula;
        foreach ($variables as $key => $value) {                                               
            $formula = str_replace($key, $value, $formula);                        
        }
        $result = round(eval(" return ".$formula." ;"));
        return collect([
            "result" => $result,
            "unit" => $result ." ".$request->bookingBagUnits,
            "variables"=>$variables,
            "formula"=>$request->formula,
        ]);
    }

    public function getChalaneSequence(int $transPortStatus)
    {
        $sequence = "sequence_" . str_replace('-',"_",getFY()) . "_" . $transPortStatus;

        

        // ✅ Execute the query
        
        DB::statement("CREATE SEQUENCE IF NOT EXISTS $sequence");

        // ✅ Fetch the next sequence value properly
        return DB::selectOne("SELECT nextval('$sequence') AS next_value")->next_value;
    }


}