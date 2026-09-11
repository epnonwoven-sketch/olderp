<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;
    protected $headings;
    protected $columns;

    public function __construct($data, $headings,$columns)
    {
        $this->data = collect($data);
        $this->headings = $headings;
        $this->columns = $columns;
    }

    public function array(): array
    {
        return $this->data->map(function ($item) {
            // Use only selected columns
            return collect($this->columns)->map(function ($col) use ($item) {
                return data_get($item, $col); // handles dot notation (e.g. driver.name)
            })->toArray();
        })->toArray();
    }


    /**
     * Return the column headings.
     */
    public function headings(): array
    {
        return $this->headings;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
