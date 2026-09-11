<?php

return[
    "boraWeightInGram"=>300,
    "transportType"=>[
        "For Delivery"=>4,
        "For Godown"=>3,
        "For Factory"=>1,
    ],
    "godownBagStatus"=>[
        "1"=>2,
        "2"=>5
    ],
    "bagStatus"=>[
        1=>"in factory",
        2=>"in godown",
        3=>"in transport",
        4=>"dispatched",
    ],
    "transportationDropDownType"=>[
        "Factory To Godown"=>[
            "transport_init_status"=>[1],
            "transport_status"=>3,
            "type"=>"Factory To Godown",
        ],
        "Factory To Client"=>[
            "transport_init_status"=>[1],
            "transport_status"=>4,
            "type"=>"Factory To Client",
        ],        
        "Godown To Client"=>[
            "transport_init_status"=>[2,5],
            "transport_status"=>4,
            "type"=>"Godown To Client",
        ],
        "Godown To Factory"=>[
            "transport_init_status"=>[2,5],
            "transport_status"=>1,
            "type"=>"Godown To Factory",
        ],
        "Godown To Godown"=>[
            "transport_init_status"=>[2,5],
            "transport_status"=>3,
            "type"=>"Godown To Godown",
        ],
    ],
    "rollImportCsvHeader"=>['vendor_name',
                             "roll_no",
                            'vehicle_no',
                            "vendor_roll_no", 
                            'purchase_date',
                            "quality", 
                            'roll_size',
                            "roll_type",
                            "hardness",
                            "roll_gsm",
                            "bopp",
                            "roll_color",
                            "roll_length",
                            "net_weight",
                            "gross_weight"
    ],

    "orderImportCsvHeader"=>[
                        "order_no",
                        "order_date",
                        "client_name",
                        "estimate_delivery_date",
                        "bag_type",
                        "bag_quality",
                        "bag_gsm",
                        "units",
                        "total_units",
                        "rate_per_unit",
                        "bag_w",
                        "bag_l",
                        "bag_g",
                        "bag_loop_color",
                        "bag_color",
                        "booked_units",
                        "rate_type",
                        "fare_type",
                        "stereo_type",
                        "bag_printing_color",
                        "agent_name",
                        "alt_bag_color",
                        "alt_bag_gsm",
                        "is_delivered"      
    ],
    "bagTypeIdByShortName"=>[
        "D"=>1,
        "B"=>2,
        "U"=>3,
        "L"=>4,
        "LBB"=>5,
        "NO-D"=>6
    ],

    "orderRollMapCsvHeader"=>[
                        "order_no",
                        "roll_no",     
    ],

    "BagTypeIdealWeightFormula"=>[
        [
            "id"=>1,
            "RS"=>"(L X 2) + 6",
        ],
        [
            "id"=>2,
            "RS"=>"(L X 2) + 6",
        ],
        [
            "id"=>3,
            "RS"=>"(W X 2) + 2",
        ],
        [
            "id"=>4,
            "RS"=>"(L X 2) + 3",
        ],
        [
            "id"=>5,
            "RS"=>"(L X 2) + 3",
        ],
    ],
    "localCityIds"=>[
        333,
    ],
    "godownDtl"=>[
        "id"=>0,
        "client_name"=>"Godown",
        "mobile_no"=>"----------",
        "email"=>"------------",
        "address"=>"----------",
        "address"=>"----------",
        "city"=>"Ranchi",
        "state"=>"Jharkhand",
    ],

    "garbageVerificationRemarks"=>[
        "Fabric Quality",
        "Operator",
        "Helper",
        "Electrical Problem",
        "Machine Problem",
        "Other"
    ],
    "clientCsvHeader"=>[
        "client_name","sector","mobile_no","secondary_mobile_no","temporary_mobile_no","email","state","city","address","trade_name"
    ],
    "BagCsvHeader"=>[
        "bora_number","packing_date","client_name","bag_configuration","bag_type","gsm","w","l","g","bag_color","printing_color","bag_weight","bag_in_pieces","bag_status"
    ],

];