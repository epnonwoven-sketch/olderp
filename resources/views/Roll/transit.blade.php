@include("layout.header")
<!-- Main Component -->

<!-- DataTables SearchPanes -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transit Dtl</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                <!-- <button id="addRoll" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#rollModal">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button> -->
                <a href="{{ route('download.roll.import.template') }}" class="btn btn-info">Template</a>
                <button id="addRollImport" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#fileImportModal">
                    Import Roll<ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table id="example" class="table table-striped table-bordered" >
                    <thead>
                        <tr>
                            <th>Vendor Name</th>
                            <th>Purchase Date</th>
                            <th>Vehicle No.</th>
                            <th>Total Roll</th>
                            <th>Total Loop</th>
                            <th>Booked Roll</th>
                            <th>Deviation Roll</th>
                            <th>Total Roll Weight</th>
                            <th>Action</th>
                            @if(in_array($user_type,[1,2]))
                            <th>Delete</th>
                            @endif
                        </tr>
                        
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->vendor_name }}</td>
                            <td>{{ $item->purchase_date }}</td>
                            <td>{{ $item->vehicle_no }}</td>
                            <td>{{ $item->total_count}}</td>
                            <td>{{ $item->total_loop}}</td>
                            <td class="text-info">{{ $item->total_book }}</td>
                            <td>{{ $item->total_deviation }}</td>
                            <td>{{ $item->total_net_weight }}</td>
                            <td> <a  class="btn btn-sm btn-primary" href="{{ url('roll/transit/dtl/' . $item->vender_id.'?purchase_date='.$item->purchase_date.'&vehicle_no='.$item->vehicle_no) }}">View</a></td>
                            @if(in_array($user_type,[1,2]))
                            <td> <button type="button" class="btn btn-sm btn-danger" onclick="{{$item->deletesAction}}">Delete</button></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-roll-form />
    <x-import-file />
</main>
<script>
    $(document).ready(function(){
        $("#rollForm").validate({
            rules: {
                vendorId: {
                    required: true,
                },
                purchaseDate: {
                    required: true,
                },
                rollSize: {
                    required: true,
                },
                rollGsm: {
                    required: true,
                },
                rollColor: {
                    required: true,
                },
                netWeight: {
                    required: true,
                },
                grossWeight: {
                    required: true,
                },
                estimatedDespatchDate:{
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                bagUnits: {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                bagTypeId: {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                },
                "printingColor[]": {
                    required: (element) => {
                        return $("#forClientId").val() != "";
                    },
                }
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addRoll();
            }
        });
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
    });

    function addRoll() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.transit.add')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollForm").get(0).reset();
                        $("#rollModal").modal('hide');
                        modelInfo(data.messages);
                        window.location.reload();
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        )
    }

    function importFile() {
        var formData = new FormData($("#importForm")[0]);
        $.ajax({
                type: "POST",
                'url': "{{route('roll.import')}}",
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
                        $("#fileImportModal").modal('hide');
                        modelInfo(data.messages);
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
                                table+=`
                                    <tr>
                                        <td> ${field}</td>
                                        <td> ${messages[0]}</td>
                                    </tr>
                                `;
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

    function deleteTransit(vendorId,purchaseDate,vehicleNo){
        $.ajax({
            url:"{{route('roll.delete.transit')}}",
            type:"post",
            data:{"vendorId":vendorId,"purchaseDate":purchaseDate,"vehicleNo":vehicleNo},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                if(response?.status){
                    modelInfo(response?.message);
                    window.location.reload();
                }else{
                    modelInfo("something went wrong","waring");                    
                    $("#loadingDiv").hide();
                }
            },
            error:function(error){
                console.log(error);
                modelInfo("server error","error");
                $("#loadingDiv").hide();
            }
        })
    }
</script>
@include("layout.footer")
