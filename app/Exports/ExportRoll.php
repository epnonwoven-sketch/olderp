<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportRoll implements FromCollection,WithHeadings, WithTitle

{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data)->map(function ($row) {
            return [
                'roll_no' => $row->roll_no,
                'purchase_date' => $row->purchase_date,
                'vendor_name' => $row->vendor_name,
                'roll_size' => $row->roll_size,
                'roll_gsm' => $row->roll_gsm,
                'roll_color' => $row->roll_color,
                'roll_length' => $row->roll_length,
                'net_weight' => $row->net_weight,
                'gross_weight' => $row->gross_weight,
                'client_name' => $row->client_name,
                'print_color' => collect($row->getPrintingColor()->get())->implode("color",","),
                'bag_type' => $row->bag_type,
                'bag_units' => $row->bag_units,
                'printing_date' => $row->printing_date,
                'weight_after_printing' => $row->weight_after_printing,
            ];
        });
    }
    public function headings(): array
    {
        return [
            'Roll No',            // Heading for roll_no
            'Purchase Date',      // Heading for purchase_date
            'Vendor Name',        // Heading for vendor_name
            'Roll Size',          // Heading for roll_size
            'Roll GSM',           // Heading for roll_gsm
            'Roll Color',         // Heading for roll_color
            'Roll Length',        // Heading for roll_length
            'Net Weight',         // Heading for net_weight
            'Gross Weight',       // Heading for gross_weight
            'Client Name',        // Heading for client_name
            'Print Color',        // Heading for print_color
            'Bag Type',           // Heading for bag_type
            'Bag Units',          // Heading for bag_units
            'Printing Date',      // Heading for printing_date
            'Weight After Printing', // Heading for weight_after_printing
        ];
    }
    public function title(): string
    {
        return 'Roll Data Export'; // Add a custom title for the sheet
    }


}
