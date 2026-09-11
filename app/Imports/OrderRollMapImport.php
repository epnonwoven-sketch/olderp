<?php

namespace App\Imports;

use App\Models\BagTypeMaster;
use App\Models\LoopStock;
use App\Models\LoopUsageAccount;
use App\Models\OrderPunchDetail;
use App\Models\OrderRollBagType;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Traits\Formula;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrderRollMapImport implements ToCollection,WithChunkReading,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    use Formula;
    public function collection(Collection $rows)
    {
        $_M_OrderPunch = new OrderPunchDetail();
        $_M_RollDetail = new RollDetail();
        $_M_RollTransit = new RollTransit();
        $_M_OrderRollBagType = new OrderRollBagType();
        $_M_BagType = new BagTypeMaster();
        $_M_LoopStock = new LoopStock();
        $_M_LoopAccount = new LoopUsageAccount();
        foreach ($rows as $row) {
            $bookOrders = 0;
            $orderNew = $_M_OrderPunch->where("order_no",$row["order_no"])->first();
            $orderId = $orderNew->id;

            $roll = $_M_RollDetail->where("roll_no",$row["roll_no"])->first();
            if(!$roll){
                $roll = $_M_RollTransit->where("roll_no",$row["roll_no"])->first();
            }
        
            if($roll->client_detail_id){
                $orderRoll = $_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->first();
                if($orderRoll){
                    $order = $_M_OrderPunch->find($orderRoll->order_id);
                    $bag = $_M_BagType->find($order->bag_type_id);
                    $bestFind = "";
                    $bestFind2 ="";
                    if($order->units=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($order->units=="Piece"){
                        $bestFind = $bag->roll_find;
                        $bestFind2 = $bag->roll_find_as_weight;
                    }
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $order->units,
                        "formula" => $bestFind,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$order->bag_l,
                        "bagW"=>$order->bag_w,
                        "bagG"=>$order->bag_g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $qty = ((($result["result"]??0)+($result2["result"]??0))/2);

                    $newRequest->merge([
                        "formula" => $bag->roll_find,
                    ]);
                    $newRequest2->merge([
                        "formula" => $bag->roll_find_as_weight,
                    ]);
                    $pieces = $this->calculatePossibleProduction($newRequest);
                    $pieces2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                    $totalLoopWeight = (($totalPiece*3.4)/1000);
                    if(in_array($bag->id,[2,4])){
                        $loopStock = $_M_LoopStock->where("loop_color",$order->bag_loop_color)->first();

                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "roll_id"=>$roll->id,
                                "order_id"=>$order->id,
                                "description"=>"Roll Remove From Booking",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>0,
                                "debit"=>$totalLoopWeight,
                                "balance"=>$loopStock->balance +  $totalLoopWeight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $_M_LoopAccount->store($newLoopAccRequest);

                        $loopStock->balance = $loopStock->balance + $totalLoopWeight;
                        $loopStock->update();
                    }
                    $order->booked_units = $order->booked_units - $qty;
                    $orderRoll->lock_status=true;
                    $order->update();
                    $orderRoll->update();
                }                        
            }
            $roll->client_detail_id = $orderNew->client_detail_id;
            $roll->estimate_delivery_date = $orderNew->estimate_delivery_date;
            $roll->bag_type_id = $orderNew->bag_type_id;
            $roll->bag_unit = $orderNew->units;
            $roll->loop_color = $orderNew->bag_loop_color;
            $roll->w = $orderNew->bag_w;
            $roll->l = $orderNew->bag_l;
            $roll->g = $orderNew->bag_g;
            $roll->printing_color = $orderNew->bag_printing_color?json_decode($orderNew->bag_printing_color,true):null;
            $roll->update();
            $newRequest = new Request($roll->toArray());
            $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
            $_M_OrderRollBagType->store($newRequest);

            $bag = $_M_BagType->find($orderNew->bag_type_id);
            $formula = "";
            $formula2 = "";
            if($orderNew->units=="Kg"){
                $formula = "RW";
                $formula2 = "RW";
            }elseif($orderNew->units=="Piece"){
                $formula = $bag->roll_find;
                $formula2 = $bag->roll_find_as_weight;
            }
            $newRequest = new Request();
            $newRequest->merge(
                [
                "bookingBagUnits" => $orderNew->units,
                "formula" => $formula,
                "length" => $roll->length,
                "netWeight"=>$roll->net_weight,
                "size"=>$roll->size,
                "gsm"=>$roll->gsm,

                "bagL"=>$orderNew->bag_l,
                "bagW"=>$orderNew->bag_w,
                "bagG"=>$orderNew->bag_g
                ]
            );
            $newRequest2 = new Request($newRequest->all());
            $newRequest2->merge([
                "formula" => $formula2,
            ]); 
            $result = $this->calculatePossibleProduction($newRequest);
            $result2 = $this->calculatePossibleProduction($newRequest2);
            $bookOrders += ((($result["result"]??0)+($result2["result"]??0))/2); 

            $newRequest->merge([
                "formula" => $bag->roll_find,
            ]);
            $newRequest2->merge([
                "formula" => $bag->roll_find_as_weight,
            ]);
            $pieces = $this->calculatePossibleProduction($newRequest);
            $pieces2 = $this->calculatePossibleProduction($newRequest2);
            $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
            $totalLoopWeight = (($totalPiece*3.4)/1000);
            if(in_array($bag->id,[2,4])){
                $loopStock = $_M_LoopStock->where("loop_color",$orderNew->bag_loop_color)->first();

                $newLoopAccRequest = new Request(
                    [
                        "loop_stock_id"=>$loopStock->id,
                        "roll_id"=>$roll->id,
                        "order_id"=>$orderNew->id,
                        "description"=>"Roll Add To Booking",
                        "opening_balance"=>$loopStock->balance,
                        "credit"=>$totalLoopWeight,
                        "debit"=>0,
                        "balance"=>$loopStock->balance -  $totalLoopWeight,
                        "user_id"=>Auth()->user()->id
                    ]
                );
                $_M_LoopAccount->store($newLoopAccRequest);
                $loopStock->balance = $loopStock->balance - $totalLoopWeight;
                $loopStock->update();
            }
            
            $orderNew = $_M_OrderPunch->find($orderId);               
            $orderNew->booked_units = $orderNew->booked_units+$bookOrders;
            $orderNew->total_units = $orderNew->booked_units;
            $orderNew->update();
        }
    }

    public function chunkSize(): int
    {
        return 200; // Process 100 rows per chunk
    }
}
