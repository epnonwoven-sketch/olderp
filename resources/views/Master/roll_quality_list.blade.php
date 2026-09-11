@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Quality</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <!-- <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rollQualityModal" onclick="resetModelForm()">
                    Add <i class="btn btn-sm bi bi-plus-circle-fill"></i>
                </button> -->
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vendor Name</th>
                    <th>Qualities</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    
    <!-- Modal Form -->
    <div class="modal fade modal-lg" id="rollQualityModal" tabindex="-1" aria-labelledby="rollQualityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rollQualityModalLabel">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rollQualityForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <input type="hidden" id="id" name="id" value="">

                        <!-- Client Name -->
                        <table id="quality_tbl" class="table table-sm table-bordered " style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Quality</th>
                                    <th>Grade</th>
                                    <th><i class="btn btn-sm bi bi-plus-circle-fill" style="color: #4f85dc;" onclick="addQualityTr()"></i></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

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
    let sl=0;
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('master.roll.quality.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "vendor_name",
                    name: "vendor_name"
                },
                {
                    data: "roll_quality",
                    name: "roll_quality"
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
        $("#rollQualityForm").validate({
            rules: {
                rollQuality: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addFare();
            }
        });
    });
    function addFare(){
        $.ajax({
                type: "POST",
                'url':"{{route('master.roll.quality.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#rollQualityForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        resetModelForm();
                        $("#rollQualityModal").modal('hide');
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
            url: "{{ route('master.roll.quality.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#quality_tbl tbody").empty();
                    sl = 0;
                    bagDtl?.roll_quality.forEach((item)=>{
                        addQualityTr(item?.quality,item?.grade_id);
                    });
                    $("#rollQualityModal").modal("show");
                    $("#submit").html("Edit");
                    $("#rollQualityModalLabel").html("Edit Grade");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(){
        sl = 0;
        $("#rollQualityForm").get(0).reset();
        $("#id").val("");
        $("#submit").html("Add");
        $("#rollQualityModalLabel").html("Add Grade");
    }

    function addQualityTr(item="",grade=""){
        $.ajax({
            type:"get",
            url: "{{ route('master.grade.map.list') }}",
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    let select = `<select class="form-select" id="grade_${sl}" name="quality[${sl}][gradeId]" required>
                                     <option value="">select</option>
                                    `;
                    bagDtl.forEach((item)=>{
                        select+=`<option value="${item?.id}" ${grade && grade==item?.id ? "selected" : ""}>${item?.grade}</option>`
                    });
                    select+=`</select>`;
                    let tr = `<tr>
                                <td>
                                    <input type="text" class="form-control" id="quality_${sl}" name="quality[${sl}][quality]" value="${item}" required />
                                </td>    
                                <td> ${select} </td>                         
                                <td>
                                    <i class=" btn btn-sm bi bi-trash-fill color-danger" style="color: red;" onclick='removeTr(this)'></i>
                                </td>
                            </tr>
                            `;
                    $("#quality_tbl tbody").append(tr);
                    sl = sl+1;
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
        
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
        if ($("input[type='hidden'][name^='packing']").length === 0){
            $("#transport").hide();            
            $("#packingStatus").attr("disabled",false);
        }
    }

</script>

@include("layout.footer")