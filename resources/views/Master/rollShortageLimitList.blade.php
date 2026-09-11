@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Roll Shortage Limit</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <!-- <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#gradeModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button> -->
            </div>
        </div>
    </div>
    <div class="container">
        <div class="tableStickyDiv">
            <table id="postsTable" class="table table-striped table-bordered table-fixed">
                <thead>
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2">Size</th>
                        <th rowspan="2">Roll Color</th>
                        <th rowspan="2">GSM</th>
                        <th rowspan="2">Quality</th>
                        <th rowspan="2">Limit</th>
                        <th colspan="3">Stock</th>
                        <th colspan="3">In Transit</th>
                        <th rowspan="2">Action</th>
                    </tr>
                    <tr>
                        <th>Net Total Weight</th>
                        <th>Total Roll</th>
                        <th>Total Length</th>
                        <th>Net Total Weight</th>
                        <th>Total Roll</th>
                        <th>Total Length</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    
    <!-- Modal Form -->
    <div class="modal fade modal-lg" id="rollMinLimitModal" tabindex="-1" aria-labelledby="rollMinLimitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rollMinLimitModalLabel">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rollMinLimitForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <input type="hidden" id="qualityTypeId" name="qualityTypeId" value="">

                        <!-- Client Name -->
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="rollSize">Size<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="100" id="rollSize" name="rollSize" class="form-control" placeholder="Enter Grade" required readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="rollColor">Roll Color<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="100" id="rollColor" name="rollColor" class="form-control" placeholder="Enter Grade" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="rollGsm">GSM<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="100" id="rollGsm" name="rollGsm" class="form-control" placeholder="Enter Grade" required readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="qualityType">Quality<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="100" id="qualityType" name="qualityType" class="form-control" placeholder="Enter Grade" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="minLimit">Min Limit<span class="text-danger">*</span></label>
                                    <input type="text" maxlength="100" id="minLimit" name="minLimit" class="form-control" placeholder="Enter Grade" required onkeypress="return isNum(event);">
                                </div>                                
                            </div>
                         </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="submit" class="btn btn-success" id="submit">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: "{{route('master.roll.shortage.list')}}",
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "size", name: "size" },
                { data: "roll_color", name: "roll_color" },
                { data: "gsm", name: "gsm" },
                { data: "quality", name: "quality" },
                { data: "min_limit", name: "min_limit" },
                { data: "total_net_weight", name: "total_net_weight" },
                { data: "total_roll", name: "total_roll" },
                { data: "total_length", name: "total_length" },
                { data: "transit_total_net_weight", name: "transit_total_net_weight" },
                { data: "transit_total_roll", name: "transit_total_roll" },
                { data: "transit_total_length", name: "transit_total_length" },
                { data: null, name: "action", orderable: false, searchable: false,
                    render:function(row,type,data){
                        return `
                            ${!data?.lock_status ? `<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="identifyFyeRowAndAction(${data?.DT_RowIndex},openModelEdit)" ></i>` :""}
                            
                            ${data?.id && !data?.lock_status ? `<i class="bi bi-lock-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to lock this item?', function() { identifyFyeRowAndAction(${data?.DT_RowIndex},deactivate); })" ></i>`:""}
                                    
                            ${(data?.lock_status) ? `<i class="bi bi-unlock-fill btn btn-sm" style ="color:rgb(37, 229, 37)" onclick="showConfirmDialog('Are you sure you want to unlock this item?', function() { identifyFyeRowAndAction(${data?.DT_RowIndex},activate); })" ></i>`: ""}
                        `;
                        (data.purchase_date ? data.purchase_date :"N/A")
                    }
                },
            ],
            createdRow: function(row, data, dataIndex) {
                $(row).attr('data-id', data.DT_RowIndex);
                $(row).attr('data-item', JSON.stringify(data));
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
            ],
        });
        $("#rollMinLimitForm").validate({
            rules: {
                minLimit: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addLimit();
            }
        });
    });

    function identifyFyeRowAndAction(id,callback=openModelEdit){
        const row = $(`tr[data-id='${id}']`);
        const dataItem = JSON.parse(row.attr("data-item")); // Get the data-id attribute
        console.log("Row data-id:", id);
        console.log("callback:", callback);
        // callback(id);
        console.log(typeof callback);
        if (typeof callback === "function") {
            callback(id); // Pass both ID and dataItem
        } else {
            console.warn("Callback is not a function. Received:", callback);
        }


    }

    function addLimit(){
        $.ajax({
                type: "POST",
                'url':"{{route('master.roll.shortage.add.edit')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#rollMinLimitForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        resetModelForm();
                        $("#rollMinLimitModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.message);
                    }else{
                        modelInfo("Something Went Wrong!!","warning");
                    }
                },
                error:function(errors){
                    $("#loadingDiv").hide();
                    modelInfo("Server Error!!","error");
                }
            }

        ) 
    }   

    function openModelEdit(id){
        const row = $(`tr[data-id='${id}']`);
        const dataItem = JSON.parse(row.attr("data-item"));
        if(dataItem){
            $("#rollSize").val(dataItem?.size);
            $("#rollColor").val(dataItem?.roll_color);
            $("#rollGsm").val(dataItem?.gsm);
            $("#qualityTypeId").val(dataItem?.quality_id);
            $("#qualityType").val(dataItem?.quality);
            $("#minLimit").val(dataItem?.min_limit);
            $("#rollMinLimitModalLabel").html("Edit Limit");
            $("#submit").html("Edit");
            $("#rollMinLimitModal").modal("show");
        }
    }

    function resetModelForm(){
        $("#rollMinLimitForm").get(0).reset();
        $("#id").val("");
        $("#submit").html("Add");
        $("#rollMinLimitModalLabel").html("Add");
    }

    function deactivate(id){
        const row = $(`tr[data-id='${id}']`);
        const dataItem = JSON.parse(row.attr("data-item"));
        if(dataItem){
            const limitId = dataItem?.id;
            $.ajax({
                type:"post",
                url: "{{ route('master.roll.shortage.deactivate', ':id') }}".replace(':id', limitId),
                dataType: "json",
                data:{lock_status:true},
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success:function(data){
                    if(data.status==true) {
                        $('#postsTable').DataTable().draw();
                        modelInfo(data?.message,"success");
                    } 
                    $("#loadingDiv").hide();
                },
                error:function(error){
                    $("#loadingDiv").hide();
                }
            });
        }
    }

    function activate(id){
        const row = $(`tr[data-id='${id}']`);
        const dataItem = JSON.parse(row.attr("data-item"));
        if(dataItem){
            const limitId = dataItem?.id;
            $.ajax({
                type:"post",
                url: "{{ route('master.roll.shortage.deactivate', ':id') }}".replace(':id', limitId),
                dataType: "json",
                data:{lock_status:false},
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success:function(data){
                    if(data.status==true) {
                        $('#postsTable').DataTable().draw();
                        modelInfo(data?.message,"success");
                    } 
                    $("#loadingDiv").hide();
                },
                error:function(error){
                    $("#loadingDiv").hide();
                }
            });
        }
    }

</script>

@include("layout.footer")