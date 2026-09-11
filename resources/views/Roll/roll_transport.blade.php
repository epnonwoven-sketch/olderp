@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transport</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-body">
            <form action="" id="searchForm" >
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="fromDate">From Date<span class="text-danger">*</span></label>
                                <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="fromDate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="uptoDate">Upto Date<span class="text-danger">*</span></label>
                                <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="uptoDate-error"></span>
                            </div>
                        </div>
                                         
                    </div>                    
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-success" id="btn_search" onclick="searchData()">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table class="table table-bordered table-responsive table-fixed" id="postTable">
                    <thead>
                        <tr>
                            <th>Dispatch Date</th>
                            <th>Client / Vendor Name</th>
                            <th>Purpose</th>
                            <th>Roll No</th>
                            <th>Role Type</th>
                            <th>Quality</th>
                            <th>Color</th>
                            <th>GSM</th>
                            <th>Size</th>
                            <th>Net Weight</th>
                            <th>Gross Weight</th>
                            <th>Length</th>
                            <th>Grade</th>
                            <th>Auto /Transporter Name</th>
                            <th>Chalan No</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- model chalan -->
    <div class="modal fade modal-lg" id="chalanModal" tabindex="-1" aria-labelledby="chalanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chalanModalLabel">Chalan Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfPreview" style="width: 100%; height: 500px; display: none;"></iframe>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-primary" id="downloadChalan">Download</button>
            </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function(){
        $("#transportTypeId").select2();
        const table = $('#postTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            searching:true,
            ajax: {
                url: "{{route('roll.sell.register')}}", // The route where you're getting data from  
                data: function(d) {
                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        if (d[field.name]) {
                            if (!Array.isArray(d[field.name])) {
                                d[field.name] = [d[field.name]];
                            }
                            d[field.name].push(field.value);
                        } else {
                            d[field.name] = field.value;
                        }
                    });

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
                { data: "transport_date", name: "transport_date" },
                { data: "client_name", name: "client_name" },
                { data: "purpose", name: "purpose",render:function(row,type,data){return (data.purpose ? (Array.isArray(data.purpose) ? data.purpose : JSON.parse(data.purpose)).join(" , ") :"")} },
                { data: "roll_no", name: "roll_no" },
                { data: "roll_type", name: "roll_type" },
                { data: "quality", name: "quality", render:function(row,type,data){return (`${data.quality} (${data?.hardness})` )}},                
                { data: "roll_color", name: "roll_color" },                
                { data: "gsm", name: "gsm" },
                { data: "size", name: "size" },
                { data: "net_weight", name: "net_weight" },
                { data: "gross_weight", name: "gross_weight" },
                { data: "length", name: "length" },
                { data: "grade", name: "grade" },
                { data: "auto_name", name: "auto_name" },
                { data: "invoice_no", name: "invoice_no" },
                { data: "action", name: "action", orderable: false, searchable: false },
                
            ],
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
                addFilter('postTable',[$('#postTable thead tr:nth-child(1) th').length - 1]);
            },   
        });
    });

    function searchData(){
        $('#postTable').DataTable().ajax.reload(function(){
            addFilter('postTable',[$('#postTable thead tr:nth-child(1) th').length - 1]);
        },false);
    }

    function base64ToBlob(base64, mimeType) {
        let byteCharacters = atob(base64);
        let byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);
        return new Blob([byteArray], { type: mimeType });
    }

    function openPreviewChalanModel(unique_id){
        $.ajax({
            url: "{{ route('packing.view.chalan', ['unique_id' => ':unique_id']) }}".replace(':unique_id', unique_id),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    let chalanNo = response.data.chalan_no;
                    let pdfBase64 = response.data.pdf_base64;
                    let isDownload = false;
                    let pdfDataUri = "data:application/pdf;base64," + pdfBase64;
                    $("#pdfPreview").attr("src", pdfDataUri).show();

                    $("#downloadChalan").show().off("click").on("click", function () {
                        isDownload= true;
                        let pdfBlob = base64ToBlob(pdfBase64, "application/pdf");
                        let link = document.createElement("a");
                        link.href = URL.createObjectURL(pdfBlob);
                        link.download = chalanNo+".pdf";
                        link.click();
                    });

                    $("#chalanModal").modal("show");
                }
            },
            error: function (xhr, status, error) {
                $("#loadingDiv").hide();
                console.error("AJAX error:", error);
                popupAlert("An error occurred while generating the PDF.");
            }
        })
    }

    function sellRollBak(id){
        $.ajax({
            url:"{{route('roll.sell.return', ['id' => ':id']) }}".replace(':id', id),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                modelInfo(response.message);
                if(response.status){
                    searchData();
                }
            },
            error: function (xhr, status, error) {
                $("#loadingDiv").hide();
                console.error("AJAX error:", error);
                modelInfo("server error");
            }
        });
    }
</script>
@include("layout.footer")