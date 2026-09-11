@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Production</a></li>
                    <li class="breadcrumb-item fs-6"><a href="#">Register</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}} Register</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$fromDate??''}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}"  value="{{$uptoDate??''}}" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-3">
                        <!-- Search Button -->
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered table-fixed table-nowrap">
                    <thead>
                        <tr>
                            <th>Print Date</th>
                            <th>Roll No</th>
                            <th>Vendor Name</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Roll Color</th>
                            <th>Net Weight</th>
                            <th>Weight After Print</th>
                            <th>Bag Size</th>
                            <th>Bag Type</th>
                            <th>Client Name</th>
                            <th>Printing Color</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-printing-update-form />
    <x-cutting-update-form />

    <div class="modal fade modal-md" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Printing Update <span display_roll_no="roll_no_display" class="text-info"></span> </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editModalForm">
                        @csrf
                        <!-- Hidden field for roll ID -->
                        <input type="hidden" id="rollId" name="rollId" value="">

                        <div class="row">
                            <div class="row mt-3">
                                <!-- Roll Name -->                                
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label" for="printingWeight">Printing Weight<span class="text-danger">*</span></label>
                                        <input type="text"  id="printingWeight" name="printingWeight" class="form-control" required onkeypress="return isNumDot(event);" />                                            
                                        <span class="error-text" id="printingWeight-error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>
<script>
    

    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function() {  

        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                url: "{{route('roll.register.printing',':machineId')}}".replace(':machineId', machineId), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
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
                { data: "printing_date", name: "printing_date" },
                { data: "roll_no", name: "roll_no" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "roll_color", name: "roll_color" },
                { data: "net_weight", name: "net_weight" },
                { data : "weight_after_print", name: "weight_after_print" },
                { data: "bag_size", name: "bag_size" },
                { data : "bag_type", name: "bag_type" },
                { data : "client_name", name: "client_name" },
                { data : "print_color", name: "print_color" },
                { data : "action", name: "action" },
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
                    exportOptions: {
                        columns: [0, 1,2, 3,4,5,6,7,8,9,10,11]  // Export only Name, Position, and Age columns
                    }

                },
            ],       
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(4); 
                td.attr("title", data?.gsm_json);
            },     
            initComplete: function () {
                addFilter('postsTable',[$("#postsTable thead tr:nth-child(1) th").length - 1]);
            },
        });

        $("#editModalForm").validate({
            ignore: [],
            rules: {
                rollId: { required: true },
                printingWeight: { required: true },
            },
            submitHandler: function (form) {
                // If form is valid, submit it
                showConfirmDialog("Are sure to Edit??",function (){editFormSubmit()});
            }
        })

    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }

    function deletePrintingConform(rollId){
        showConfirmDialog("Are You Sure Delete From Printing??",function(){ 
            deletePrinting(rollId);
        });
    }
    function deletePrinting(rollId){
        $.ajax({
            url:"{{route('roll.printing.delete')}}",
            type:"post",
            dataType:"json",
            data:{id:rollId},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response?.message);
                    searchData();
                }
                else{
                    modelInfo(response?.message||"Server Error","Warning");
                }
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        })
    }

    function editPrinting(roleId){
        try{
            $("#loadingDiv").show();
            $.ajax({
                url:"{{route('roll.dtl.full',':id')}}".replace(':id', roleId),
                type:"get",
                success:function(response){
                    if(response.status){
                        $("#editModal").modal("show");
                        $("#rollId").val(response?.data?.id);
                        $("#rollId").val(response?.data?.id);
                        $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html(response?.data?.roll_no || '');
                        });
                        $("#printingWeight").val(response?.data?.weight_after_print).attr("min",response?.data?.net_weight);
                    }else{
                        $("#editModal").modal("hide");
                    }
                }
            })

        }catch(error){
            console.log("error:",error);
        }finally{
            $("#loadingDiv").hide();
        }
    }

    function editFormSubmit(){
        $.ajax({
            type: "POST",
            url: "{{ route('roll.production.edit.printing.weight') }}",
            dataType: "json",
            data: $("#editModalForm").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
                $("#editModal").modal("hide");
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response?.message);                    
                    $("#editModalForm").get(0).reset();
                    searchData();
                }else{
                    modelInfo(response?.message,"error");
                }
            }
        });
    }
</script>
@include("layout.footer")