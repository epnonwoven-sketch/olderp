<!-- Modal Form -->
<div class="modal fade modal-xl" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientModalLabel">Add New Client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="clientForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="cityHidden"/>

                    <div class="row mb-3">
                        <!-- Client Name -->
                        <label class="control-label col-md-2" for="clientName">Client Name<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <input type="text" maxlength="100" id="clientName" name="clientName" class="form-control" placeholder="Enter Client Name" required>
                            <span class="error-text" id="clientName-error"></span>
                        </div>
                        <label class="control-label col-md-2" for="stateId">State<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <select name="stateId" id="stateId" class="form-select" required>
                                <option value="">select</option>
                                @foreach($stateList as $val)
                                <option value="{{$val->id}}">{{$val->state_name}}</option>
                                @endforeach
                            </select>
                            <span class="error-text" id="stateId-error"></span>
                        </div>
                    </div>

                    <div class="row mb-3"> 
                        <label class="control-label col-md-2" for="cityId">City<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <select name="cityId" id="cityId" class="form-select" required>
                                <option value="">select</option>
                            </select>
                            <span class="error-text" id="cityId-error"></span>
                        </div>
                        
                        <!-- Client Address -->
                        <label class="control-label col-md-2" for="address">Address<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <textarea id="address" name="address" class="form-control" placeholder="Enter Client Address" rows="3" required></textarea>
                            <span class="error-text" id="address-error"></span>
                        </div>

                        <label class="control-label col-md-3" for="hasAddressTwo">Has Multiple Address<span class="text-danger">*</span></label>
                        <div class="col-md-3 form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="hasAddressTwo" name="hasAddressTwo" />
                            <div class="">
                            </div>  
                            <span class="error-text" id="hasAddressTwo-error"></span>
                        </div>
                        
                    </div>

                    <div class="row mb-3">
                        <label class="control-label col-md-2" for="sectorId">Sector<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <select type="text" id="sectorId" name="sectorId" class="form-select" placeholder="Enter state" required >
                                <option value="">Select</option>
                                @foreach($sector as $val)
                                    <option value="{{$val->id}}">{{$val->sector}}</option>
                                @endforeach
                            </select>
                            <span class="error-text" id="sectorId-error"></span>
                        </div>
                        <!-- Mobile Number -->
                        <label class="control-label col-md-2" for="mobileNo">Mobile Number<span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <input type="text" maxlength="15" id="mobileNo" name="mobileNo" class="form-control" placeholder="Enter Mobile Number" required onkeypress="return isNum(event);">
                            <span class="error-text" id="mobileNo-error"></span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="control-label col-md-2" for="secondaryMobileNo">Mobile Number 2</label>
                        <div class="col-md-4">
                            <input type="text" maxlength="15" id="secondaryMobileNo" name="secondaryMobileNo" class="form-control" placeholder="Enter Secondary Mobile Number" onkeypress="return isNum(event);">
                            <span class="error-text" id="secondaryMobileNo-error"></span>
                        </div>
                        <label class="control-label col-md-2" for="temporaryMobileNo">Mobile Number 3</label>
                        <div class="col-md-4">
                            <input type="text" maxlength="15" id="temporaryMobileNo" name="temporaryMobileNo" class="form-control" placeholder="Enter Temporary Mobile Number" onkeypress="return isNum(event);">
                            <span class="error-text" id="temporaryMobileNo-error"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <!-- Client Email -->
                        <label class="control-label col-md-2" for="email">Email</label>
                        <div class="col-md-4">
                            <input type="email" maxlength="100" id="email" name="email" class="form-control" placeholder="client@example.com">
                            <span class="error-text" id="email-error"></span>
                        </div>
                        <label class="control-label col-md-2" for="tradeName">Trade Name</label>
                        <div class="col-md-4">
                            <input type="text" maxlength="100" id="tradeName" name="tradeName" class="form-control" placeholder="">
                            <span class="error-text" id="tradeName-error"></span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#stateId").select2({
            width:"100%",                    
            dropdownCssClass: 'form-control',            
            dropdownParent: $('#clientModal'),
        });
        $("#cityId").select2({
            width:"100%",                   
            dropdownCssClass: 'form-control',            
            dropdownParent: $('#clientModal'),
        });
        $("#stateId").on("change",function(event,cityId=null){
            stateId = event.target.value;
            oldCity = cityId?cityId:$("#cityHidden").val();
            $('#cityId').empty(); 
            $('#cityId').append('<option value="">Select</option>');
            if(stateId){
                $.ajax({
                    url:"{{route('master.city.by.state',':id')}}".replace(":id",stateId),
                    type:"get",
                    success:function(data){
                        if(data?.status){
                            $.each(data?.data, function (key, city) {
                            $('#cityId').append(
                                `<option value="${city.id}" ${city.id==oldCity?"selected":""}>${city.city_name}</option>`
                            );
                        });
                        }
                    }
                })
            }
        });
        $("#clientForm").validate({
            rules: {
                clientName: {
                    required: true,
                    minlength: 3
                },

                clientMobileNo: {
                    required: true,
                    number: true,
                    minlength:10,
                    minlength:10
                },
                clientAddress: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                addClint();
            }
        });
        addEventListenersToForm("clientForm");
    });

    function addClint(){
        $.ajax({
                type: "POST",
                'url':"{{route('client.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#clientForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#clientForm").get(0).reset();
                        $("#clientModal").modal('hide');
                        var newOptionValue = data?.data?.client?.id;
                        var clientName = data?.data?.client?.client_name;
                        if (newOptionValue !== "") {
                            // Check if the option already exists
                            if ($('#forClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#forClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                            if ($('#bookingForClientId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#bookingForClientId').append('<option value="' + newOptionValue + '">' + clientName + '</option>');                                
                            } 
                        } 
                        modelInfo(data.messages);
                    }else if (data?.errors) {
                        let errors = data?.errors;
                        modelInfo(data.message);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }
</script>
