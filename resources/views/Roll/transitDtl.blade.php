@include("layout.header")
<!-- Main Component -->

<!-- DataTables SearchPanes -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>                    
                    <li class="breadcrumb-item fs-6"><a href="{{route('roll.transit')}}">Transit Dtl</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">List</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List  (<span style="color:aliceblue"> {{$vender->vendor_name??""}} <i style="font-size: small;">{{$purchase_date}}</i> <i style="font-size: small;">total Weight :</i> <i id="total_weight" style="font-size: small;"></i> </span>) </h5>
            <div class="panel-control">
                @if($addToRollInStock??false)
                <button id="addRoll" type="button" class="btn btn-primary fa fa-arrow-right" onclick="transferToRollStock()">
                    Add To Roll Stock <ion-icon name="add-circle-outline"></ion-icon>
                </button>
                @endif
            </div>            
        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered table-fixed" >
                    <thead>
                        <tr>
                            <!-- <th >#</th>                     -->
                            <th>Roll No</th>
                            <th>Quality</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Roll Color</th>
                            <th>Net Weight</th>
                            <th>Bag Size</th>
                            <th>Bag Type</th>
                            <th>Unit</th>
                            <th>Client Name</th>
                            <th>Purchase Date</th>
                            <th>Vendor Name</th>
                            <!-- <th>Hardness</th> -->
                            <th>Roll Type</th>
                            <th>Grade</th>
                            <th>Length</th>
                            <th>Gross Weight</th>
                            <th>GSM Variation</th>                        
                            <th>Action</th>
                            <th onclick="selectAllCheck()">Select</th>    
                        </tr>                    
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>    
    
    <x-pending-order-book />
</main>

<script>
    let isCheckBox = '<?=($addToRollInStock??false);?>';
    
    $(document).ready(function () {
        // Get vendor_id from the URL path
        let vendor_id = window.location.pathname.split('/').pop(); 
        let url = window.location.href; // Current URL with query parameters
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            // responsive: true,
            ajax: {
                url: url,
                data: function (d) {
                    let formData = $("#searchForm").serializeArray();
                    $.each(formData, function (i, field) {
                        d[field.name] = field.value;
                    });
                },
                dataSrc: function (json) {
                    // Get extra data from server
                    // $('#totalAmount').text(json?.totalAmount.toFixed(2)); 
                    $('#total_weight').text(json?.totalWeight); 
                    return json.data;
                },
                beforeSend: function () {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function () {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },
            columns: [
                // { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },                      
                { data: "roll_no", name: "roll_no" ,render:function(row,type,data){return (data.roll_no ? data.roll_no :"N/A")} },
                { data : "quality", name: "quality" ,render:function(row,type,data){return (data.quality ? data.quality+` (${data.hardness})` :"N/A")}},
                { data: "size", name: "size" ,render:function(row,type,data){return (data.size ? data.size :"N/A")}},
                { data: "gsm", name: "gsm",render:function(row,type,data){return (data.gsm ? data.gsm :"N/A")} },
                { data: "roll_color", name: "roll_color" ,render:function(row,type,data){return (data.roll_color ? data.roll_color :"N/A")}},
                { data: "net_weight", name: "net_weight" ,render:function(row,type,data){return (data.net_weight ? data.net_weight :"N/A")}},
                { data: "bag_size", name: "bag_size" ,render:function(row,type,data){return (data.bag_size ? "<pre>"+data.bag_size+"</pre>" :"N/A")}},
                { data: "bag_type", name: "bag_type" ,render:function(row,type,data){return (data.bag_type ? data.bag_type :"N/A")}},
                { data: "bag_unit", name: "bag_unit" ,render:function(row,type,data){return (data.bag_unit ? data.bag_unit :"N/A")}},
                { data: "client_name", name: "client_name" ,render:function(row,type,data){return (data.client_name ? data.client_name :"N/A")}},
                { data: "purchase_date", name: "purchase_date" ,render:function(row,type,data){return (data.purchase_date ? data.purchase_date :"N/A")}},
                { data: "vendor_name", name: "vendor_name",render:function(row,type,data){return (data.vendor_name ? data.vendor_name :"N/A")} },
                // { data: "hardness", name: "hardness" ,render:function(row,type,data){return (data.hardness ? data.hardness :"N/A")} },
                { data: "roll_type", name: "roll_type",render:function(row,type,data){return (data.roll_type ? data.roll_type :"N/A")} },
                { data : "grade", name: "grade" ,render:function(row,type,data){return (data.grade ? data.grade :"N/A")}},
                { data: "length", name: "length",render:function(row,type,data){return (data.length ? data.length :"N/A")} },
                { data: "gross_weight", name: "gross_weight" ,render:function(row,type,data){return (data.gross_weight ? data.gross_weight :"N/A")}},
                { data: "gsm_variation", name: "gsm_variation" ,render:function(row,type,data){return (data.gsm_variation ? data.gsm_variation :"N/A")}},
                { data: "action", name: "action", orderable: false, searchable: false,render:function(row,type,data){return (`<span style="display:flex;">${data?.action}</span>`)}  },
                { data: "check", name: "check", orderable: false, searchable: false },          
            ],
            dom: 'lBfrtip', // Updated dom configuration
            language: {
                lengthMenu: "Show _MENU_",
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"],
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
                    exportOptions: {
                        columns: [0, 2, 3,4,5,6,7,8,9,10]  // Export only Name, Position, and Age columns
                    }

                },
            ],
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(6); 
                td.attr("title", data?.gsm_json); 
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                }
            },
            initComplete: function () {
                hideColumn(table);
                addFilter('postsTable',[0,$('#postsTable thead tr:nth-child(1) th').length - 1]);
            },
        }); 
               

        if (!isCheckBox) {
            table.column(1).visible(false);
        }


    });
    
        
    function hideColumn(table){
        const columnsToHide = [10,11,12,13,14,15,16,17];
        columnsToHide.forEach(index => table.column(index).visible(false));
    }


    // Trigger table redraw on search
    function searchData() {
        $('#postsTable').DataTable().ajax.reload(null, false);
    }

    function openModelBookingModel(id) {
        if (id) {
            $("#rollBookingModal").on("show.bs.modal",function(){
                resetFormBook("myForm");
            })
            $("#id").val(id);
            $("#rollBookingModal").modal("show");

        }
        return;
    }

    function openCloseClientMode(){
        forClientId = $("#forClientId").val();
        if(forClientId!=""){
            $("div[client='client']").show();
        }
        else{
            $("div[client='client']").hide();
        }
    }

    let selectAll = false;

    function selectAllCheck(){
        if(selectAll)
        {
            $('input[name="transitId[]"]').prop("checked",false);             
            selectAll = false;
        }
        else
        {
            $('input[name="transitId[]"]').prop("checked",true);
            selectAll = true;
        }
    }
    function transferToRollStock(){
        var selectitem = [];
        $('input[name="transitId[]"]').each(function() { 
            if ($(this).is(':checked')) {
                selectitem.push($(this).val());
            }
        });
        
        console.log(selectitem);

        $.ajax({
            type: "POST",
            url: "{{route('roll.transit.rll.stock')}}",
            dataType: "json",
            data:{
                items: selectitem, 
            },
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    var table = $('#postsTable').DataTable();
                    table.ajax.reload(null, false);  
                    modelInfo(data.messages);
                } else if (data?.errors) {
                    let errors = data.errors;
                    for (let field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                } else {
                    modelInfo("Something Went Wrong!!");
                }
            },
        });
    }

    function removeBooking(id){
        $.ajax({
            url:"{{route('roll.order.remove.booking')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){                
                $("#loadingDiv").hide();
                if(data?.status){
                    $('#postsTable').DataTable().ajax.reload();
                }else{
                    modelInfo(data?.message,"warning");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error")
            }
        })
    }
</script>

@include("layout.footer")
