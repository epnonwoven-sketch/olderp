@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Report</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Roll Stock Status</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-heading">
            <h5 class="panel-title">Search</h5>
        </div>
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3 mt-3">
                    <!-- Upto Date -->
                    <div class="col-auto">
                        <label for="uptoDate" class="col-form-label">Upto Date</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$uptoDate??date('Y-m-d')}}"/>
                    </div>                    
                    <!-- Search Button -->
                    <div class="col-auto">
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>
        </div>           
        <div class="panel-body"> 
            <div class="panel-control justify-content-end">
                <span class="fw-bold">Total Weight: </span><span id="total_weight">0</span>
            </div>
            <div class="tableStickyDiv">
                <table id="postTable" class="table table-responsive table-border table-fixed">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Quality</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Roll Color</th>                            
                            <th>Net Weight</th>
                            <th>Bag Size</th>
                            <th>Bag Type</th>
                            <th>Client Name</th>
                            <th>Unit</th>
                            <th>Roll Type</th>
                            <th>Grade</th>
                            <th>Length</th>
                            <th>Gross Weight</th>
                            <th>GSM Variation</th>
                        </tr> 
                    </thead>
                    <tbody>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $("#postTable").DataTable({
            processing: true,
            serverSide: false,
            searching:true,
            ajax: {
                url: "{{route('report.roll.status')}}", // The route where you're getting data from  
                data: function(d) {
                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },  
                dataSrc: function (json) {
                    $('#total_weight').text(json?.totalWeight); 
                    return json.data;
                },            
                beforeSend: function() {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function() {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },

            columns: [
                { data: "roll_no", name: "roll_no" ,render:function(row,type,data){return (data.roll_no ? data.roll_no :"N/A")} },
                
                { data : "quality", name: "quality" ,render:function(row,type,data){return (data.quality ? data.quality :"N/A")}},
                { data: "size", name: "size" ,render:function(row,type,data){return (data.size ? data.size :"N/A")}},
                { data: "gsm", name: "gsm",render:function(row,type,data){return (data.gsm ? data.gsm :"N/A")} },
                { data: "roll_color", name: "roll_color" ,render:function(row,type,data){return (data.roll_color ? data.roll_color :"N/A")}},
                
                { data: "net_weight", name: "net_weight" ,render:function(row,type,data){return (data.net_weight ? data.net_weight :"N/A")}},
                { data: "bag_size", name: "bag_size" ,render:function(row,type,data){return (data.bag_size ? "<pre>"+data.bag_size+"</pre>" :"N/A")}},
                { data: "bag_type", name: "bag_type" ,render:function(row,type,data){return (data.bag_type ? data.bag_type :"N/A")}},
                { data: "client_name", name: "client_name" ,render:function(row,type,data){return (data.client_name ? data.client_name :"N/A")}},
                { data: "bag_unit", name: "bag_unit" ,render:function(row,type,data){return (data.bag_unit ? data.bag_unit :"N/A")}},
                { data: "roll_type", name: "roll_type",render:function(row,type,data){return (data.roll_type ? data.roll_type :"N/A")} },
                { data : "grade", name: "grade" ,render:function(row,type,data){return (data.grade ? data.grade :"N/A")}},
                { data: "length", name: "length",render:function(row,type,data){return (data.length ? data.length :"N/A")} },
                { data: "gross_weight", name: "gross_weight" ,render:function(row,type,data){return (data.gross_weight ? data.gross_weight :"N/A")}},
                { data: "gsm_variation", name: "gsm_variation" ,render:function(row,type,data){return (data.gsm_variation ? data.gsm_variation :"N/A")}},
                
            ],
            createdRow: function(row, data, dataIndex) {
                // Apply the custom class to the row
                let td = $('td', row).eq(7); 
                td.attr("title", data?.gsm_json);
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                    if(data.row_color=="tr-client"){
                        $(row).attr("title", "book for client");
                    }else if(data.row_color=="tr-client-printed"){
                        $(row).attr("title", "roll have booked and printed");
                    }else if(data.row_color=="tr-printed"){
                        $(row).attr("title", "roll is printed");
                    }else if(data.row_color=="tr-primary-print"){
                        $(row).attr("title", "this roll will be delivering soon");
                    }else if(data.row_color=="tr-expiry-print blink"){
                        $(row).attr("title", "this roll  delivery has been expired");
                    }else if(data.row_color=="tr-argent-print"){
                        $(row).attr("title", "this roll  delivery is urgent");
                    }
                }
            },
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i> ',
                    className: 'btn btn-success',
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill text-danger"></i>',
                    title: 'Data Export',
                    orientation: 'portrait',
                    pageSize: 'A4',

                },
            ],  
            initComplete: function () {
                addFilter('postTable',[]);
            },
        });
    });

    function searchData(){
        $('#postTable').DataTable().ajax.reload(function(){
            addFilter('postTable',[]);
        }, false);
    }
</script>

@include("layout.footer")