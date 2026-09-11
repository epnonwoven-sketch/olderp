<?php

namespace App\Imports;

use App\Models\RollDetail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ImportRoll implements ToCollection,WithChunkReading
{
    /**
    * @param Collection $collection
    */
    private $counter = 0;
    public function collection(Collection $rows)
    {
        
        if($this->counter==0)
        $rows = $rows->skip(1);
        $_M_RollDetail = new RollDetail();
        foreach ($rows as $rowNo => $row) {           
            $row = collect($row->values());
            $request = new Request(array_combine($_M_RollDetail->getKey(), $row->toArray()));
            $_M_RollDetail->store($request);
        }
    }
    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows per chunk
    }
}
