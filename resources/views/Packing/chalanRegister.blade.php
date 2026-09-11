@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Chalane</li>
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
                    
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="transportTypeId">Transportation Type</label>
                                <select type="text" id="transportTypeId" name="transportTypeId[]" class="form-select" multiple>
                                    @foreach($transportType as $val)
                                        <option value="{{$val->id}}" {{in_array($val->id,['Factory To Client1','Godown To Client1'])?"selected":""}}>{{$val->type}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="transporterId-error"></span>
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
            <table class="table table-bordered  table-responsive table-fixed" id="postsTable">
                <thead>
                    <tr>
                        <th>Transport Date</th>
                        <th>Chalan No.</th>
                        <th>Auto Name</th>
                        <th>Transporter Name</th>
                        <th>Total Bag</th>
                        <th>Return Bag</th>
                        <th>Transportation Type</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
    <!-- model transport dtl modal  -->
    <div class="modal fade modal-lg" id="dtlModal" tabindex="-1" aria-labelledby="dtlModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dtlModalLabel">Dtl Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function(){
        $("#transportTypeId").select2();
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                url: "{{route('packing.chalan.register')}}",// The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },
                dataSrc: function (json) {
                    // $('#total_weight').text(json?.totalWeight); 
                    // $('#intTransPort').text(json?.intTransPort); 
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
                { data:"transport_date",name:"transport_date" },
                { data: "invoice_no", name: "invoice_no" },
                { data: "auto_name", name: "auto_name" },
                { data: "transporter_name", name: "transporter_name" },
                { data: "total_bags", name: "total_bags" },
                { data: "total_return_bags", name: "total_return_bags" },
                { data: "transition_type", name: "transition_type" },
                
            ],
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(1); 
                let originalText = td.text().trim(); // Get the plain text content
                td.html(`
                    <i onclick="openPreviewChalanModel('${data.chalan_unique_id}')" style="cursor:pointer; color:blue; text-decoration:underline;">
                        ${originalText}
                    </i>
                    &nbsp;
                    <i class="bi bi-info-circle-fill" data-placement="bottom" data-toggle="tooltip" title="View detail" style="cursor:pointer;" onclick="showDtlModel(${data.id})"></i>
                `);

            },
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [{
                extend: 'csv',
                text: 'Export to Excel',
                className: 'btn btn-success',

            }],            
            initComplete: function () {
                addFilter('postsTable',[]);
            },
        });
    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[]);
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

    function showDtlModel(chalaneId){
        $.ajax({
            url:"{{route('packing.transport.dtlhtml', ['id' => ':id'])}}".replace(":id",chalaneId),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){                    
                    $("#dtlModal").modal("show");
                    $("#dtlModal .modal-body").html(response?.data);
                }else{

                }
            },
            error: function (xhr, status, error) {
                $("#loadingDiv").hide();
                console.error("AJAX error:", error);
                popupAlert("An error occurred while generating the PDF.");
            }
        })
    }
</script>
@include("layout.footer")