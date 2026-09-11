@include("layout.header")
    <main class="p-3">
        <div class="container-fluid">
            <div class="mb-3 text-left">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb fs-6">
                        <li class="breadcrumb-item fs-6"><a href="#">Order</a></li>
                        <li class="breadcrumb-item active fs-6" aria-current="page">Import</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="container">            
            <div class="panel-body">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <!-- Hidden field for roll ID -->

                    <div class="row">
                        <div class="row mb-3">
                            <label for="csvFile" class="col-sm-4 col-form-label">CSV File.<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                            <input type="file" id="csvFile" name="csvFile" class="form-control" required accept=".csv,.xlsx">
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12" id="errorExcelLog" style="display: none;">

                    </div>
                </div>
            </div>
        </div>

    </main>
    <script>
        $("#importForm").validate({
            rules: {
                csvFile: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                importFile();
                return false;
            }
        });

        function importFile() {
            var formData = new FormData($("#importForm")[0]);
            $.ajax({
                    type: "POST",
                    'url': "{{route('import.order')}}",
                    "deferRender": true,
                    processData: false, // Do not process data (let FormData handle it)
                    contentType: false, // Do not set content type (let the browser handle it)
                    dataType: "json",

                    'data': formData,
                    beforeSend: function() {
                        $("#errorExcelLog").html("");
                        $("#errorExcelLog").hide();
                        $("#loadingDiv").show();
                    },
                    success: function(data) {
                        $("#loadingDiv").hide();
                        if (data.status) {
                            document.getElementById("importForm").reset();
                            modelInfo(data.messages);
                            $("#errorExcelLog").html("");
                            window.location.reload();
                        } else if (!data.status && data?.data) {
                            let errors = data?.data;
                            // console.log(data?.data?.rollNo[0]);
                            modelInfo(data.message,"error");                        
                            if (errors && typeof errors === 'object') {
                                let table=`<table class="table table-bordered responsive" style="font-size:xx-small; text-align:center; color:red;">
                                            <thead>
                                                <tr>
                                                    <th>Row No</th>
                                                    <th>Error</th>
                                                </tr>
                                            </thead>
                                            <tbody>                                            
                                `;
                                for (const [field, messages] of Object.entries(errors)) {
                                    messages.forEach((error)=>{

                                        table+=`
                                            <tr>
                                                <td> ${field}</td>
                                                <td> ${error}</td>
                                            </tr>
                                        `;
                                    });
                                    console.log(`Row No. ${field}:`, messages[0]);
                                    $(`#${field}-error`).text(messages[0]);
                                }
                                table+=`
                                        </tbody>
                                    </table>
                                `;
                                $("#errorExcelLog").html(table);
                                $("#errorExcelLog").show();
                            }else{
                                for (field in errors) {
                                    console.log(field);
                                    $(`#${field}-error`).text(errors[field][0]);
                                }

                            }
                        } else if(!data.status){
                            modelInfo(data.message,"error");  
                        } 
                        else {
                            modelInfo("Something Went Wrong!!","error");
                        }
                    },
                    error: function(error) {
                        $("#loadingDiv").hide();
                        console.log(error);
                    }
                }

            )
        }
    </script>
@include("layout.footer")