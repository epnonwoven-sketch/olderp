@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">State</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#stateModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>State</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    
    <!-- Modal Form -->
    <div class="modal fade modal-lg" id="stateModal" tabindex="-1" aria-labelledby="stateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="stateModalLabel">Add State</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="stateForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <input type="hidden" id="id" name="id" value="">

                        <!-- Client Name -->
                        <div class="mb-3">
                            <label class="form-label" for="stateName">State<span class="text-danger">*</span></label>
                            <input type="text" maxlength="100" id="stateName" name="stateName" class="form-control" placeholder="Enter State Type" required>
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
            serverSide: true,
            ajax: "{{route('master.state.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "state_name",
                    name: "state_name"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
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
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i>',
                    className: 'btn btn-success',
                    action: function () {
                        let dt = $('#postsTable').DataTable();
                        let ajaxUrl = dt.ajax.url(); 
                        let params = dt.ajax.params();

                        let columns = [];
                        let headings = [];


                        dt.columns().every(function () {
                            const col = this;
                            const settings = col.settings()[0].aoColumns[col.index()];
                            const colData = settings.data;

                            if (col.visible() && colData && colData !== 'action' && colData !== 'DT_RowIndex') {
                                columns.push(colData);
                                const thText = $(col.header()).text().trim();
                                headings.push(thText);

                            }
                        });

                        params.export = 'excel';
                        params.export_columns = JSON.stringify(columns);
                        params.export_headings = JSON.stringify(headings); 

                        // Now trigger an AJAX call to export and handle download
                        $.ajax({
                            url: ajaxUrl,
                            method: 'GET',
                            data: params,
                            xhrFields: {
                                responseType: 'blob' // Important: receive binary
                            },
                            success: function (blob, status, xhr) {
                                const filename = xhr.getResponseHeader('Content-Disposition')
                                    ?.split('filename=')[1]
                                    ?.replace(/['"]/g, '') || 'auto-list.xlsx';

                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(url);
                            },
                            error: function (xhr) {
                                alert('Export failed!');
                            }
                        });
                    }
                }
            ],
        });
        $("#stateForm").validate({
            rules: {
                stateName: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addState();
            }
        });
    });
    function addState(){
        $.ajax({
                type: "POST",
                'url':"{{route('master.state.add.edit')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#stateForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        resetModelForm();
                        $("#stateModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }   

    function openModelEdit(id){
        $.ajax({
            type:"get",
            url: "{{ route('master.state.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#stateName").val(bagDtl?.state_name);
                    $("#stateModal").modal("show");
                    $("#submit").html("Edit");
                    $("#stateModalLabel").html("Edit State");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(){
        $("#stateForm").get(0).reset();
        $("#id").val("");
        $("#submit").html("Add");
        $("#stateModalLabel").html("Add State");
    }
    
    function deactivate(id){
        $.ajax({
            type:"post",
            url: "{{ route('master.state.deactivate', ':id') }}".replace(':id', id),
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
    function activate(id){
        $.ajax({
            type:"post",
            url: "{{ route('master.state.deactivate', ':id') }}".replace(':id', id),
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

</script>

@include("layout.footer")