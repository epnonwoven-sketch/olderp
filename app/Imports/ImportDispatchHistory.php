<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;


class ImportDispatchHistory implements ToCollection,WithChunkReading,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {   
        $file = request()->file('csvFile');
        foreach ($rows as $row) {
            if(Str::lower($file->getClientOriginalExtension())=="xlsx")
            {
                $row["date"] = is_int($row["date"])? getDateColumnAttribute($row['date']) : $row['date'];
                $row["packing_date"] = is_int($row["packing_date"])? getDateColumnAttribute($row['packing_date']) : $row['packing_date'];
            }
            $row["party_name"] = trim($row["party_name"]);         
            $row["date"] = $row["date"]?Carbon::parse($row["date"])->format("Y-m-d"):null;
            $row["packing_date"] = $row["packing_date"]?Carbon::parse($row["packing_date"])->format("Y-m-d"):null;
           
            DB::table("old_dispatch_history")->insert($row->toArray());
        }
    }
    public function chunkSize(): int
    {
        return 200; // Process 100 rows per chunk
    }
}
