<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="rollModal" tabindex="-1" aria-labelledby="rollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollModalLabel">Edit Roll [<span class="text-primary text-sm" id="rollNo"></span>]</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollEditForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="editRollId" name="editRollId" value="">
                    <input type="hidden" id="qualityHidden"/>
                    <input type="hidden" id="vendorIdHidden"/>
                    <div class="row">
                                                
                        <!-- Vendor Name -->
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="venderName">Vender Name <span class="text-danger">*</span></label>
                                <input name="venderName" id="venderName" class="form-control" readonly />                                    
                                <span class="error-text" id="venderName-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="purchaseDate">Purchase Date <span class="text-danger">*</span></label>
                                <input name="purchaseDate" id="purchaseDate" class="form-control" readonly />                                    
                                <span class="error-text" id="purchaseDate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="qualityId">Roll Quality<span class="text-danger">*</span></label>
                                <select id="qualityId" name="qualityId" class="form-select" required >
                                    <option value="">select</option>
                                </select>
                                <span class="error-text" id="qualityId-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="rollType">Roll Type<span class="text-danger">*</span></label>
                                <select id="rollType" name="rollType" class="form-select" required onchange="showHideGsmJsonDiv()">
                                    <option value="">select</option>
                                    <option value="NW">NW</option>
                                    <option value="BOPP">BOPP</option>
                                    <option value="LAM">LAM</option>
                                </select>
                                <span class="error-text" id="rollType-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="hardness">Hardness<span class="text-danger">*</span></label>
                                <input id="hardness" name="hardness" class="form-control" required />
                                <span class="error-text" id="rollType-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group" id="singleGsmEdit">
                                <label class="control-label" for="gsm">GSM<span class="text-danger">*</span></label>
                                <input id="gsm" name="gsm" class="form-control" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="gsm-error"></span>
                            </div>
                            <div class="form-group" id="multipleGsmEdit" style="display: none;">
                                <label class="control-label" for="gsmJson">GSM<span class="text-danger">*</span></label>
                                <input id="gsmJson" name="gsmJson" class="form-control" required placeholder="gsm/lamination/boop"  onkeypress="return gsmJsonValidate(event); "  onkeyup="setGsm();"/>
                                <span class="error-text" id="gsmJson-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="rollColor">Roll Color <span class="text-danger">*</span></label>
                                <input name="rollColor" id="rollColor" class="form-control" readonly />                                    
                                <span class="error-text" id="rollColor-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="length">Roll Length <span class="text-danger">*</span></label>
                                <input name="length" id="length" class="form-control" required onkeypress="return isNumDot(event);" />                                    
                                <span class="error-text" id="length-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="size">Roll Size <span class="text-danger">*</span></label>
                                <input name="size" id="size" class="form-control" required onkeypress="return isNumDot(event);" />                                    
                                <span class="error-text" id="size-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="netWeight">Net Weight<span class="text-danger">*</span></label>
                                <input name="netWeight" id="netWeight" class="form-control" required onkeypress="return isNumDot(event);" />                                    
                                <span class="error-text" id="netWeight-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="grossWeight">Gross Weight<span class="text-danger">*</span></label>
                                <input name="grossWeight" id="grossWeight" class="form-control" required onkeypress="return isNumDot(event);" />                                    
                                <span class="error-text" id="grossWeight-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="printingColor">Printing Color</label>
                                <select name="printingColor[]" id="printingColor" class="form-select select22" multiple="multiple"> 
                                    @foreach($color as $val)
                                    <option data-color="{{$val->color}}" value="{{$val->color}}" style="background-color:{{$val->color}};">{{$val->color}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="printingColor-error"></span>
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Edit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#printingColor').select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',
            dropdownParent: $('#rollModal'),            
            width:"100%",
            templateResult: formatOption,
            templateSelection: formatOption 
        });
        $("#rollEditForm").validate({
            rules: {
                id: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                showConfirmDialog('Are you sure you want to update roll?', updateRoll)
            }
        });

    });

    $('#rollModal').on('show.bs.modal', function (e) {
        $('#printingColor').select2({
                placeholder: "Select tags",
                allowClear: false,
                maximumSelectionLength: 4,
                dropdownCssClass: 'form-control',
                dropdownParent: $('#rollModal'),            
                width:"100%",
                templateResult: formatOption,
                templateSelection: formatOption 
            });
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }

    function editRoll(id){
        $.ajax({
            url:"{{route('roll.dtl.full',':id')}}".replace(":id",id),
            type:"get",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                var roll = response?.data;
                if(response?.status){
                    $("#loadingDiv").hide();
                    $("#rollModal").modal("show");
                    resetForm("rollEditForm");
                    setRollValue(roll);
                }else{
                    $("#loadingDiv").hide();
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
            }
        })
    }

    function updateRoll(){
        $.ajax({
            url:"{{route('roll.update.edit')}}",
            type:"post",
            data:$("#rollEditForm").serialize(0),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    resetForm("rollEditForm");
                    $('#postsTable').DataTable().draw();
                    $("#rollModal").modal("hide");
                    modelInfo(data.message);
                }
                else{
                    modelInfo("server error","error");
                }
            },
            error:function(errors){
                modelInfo("server error","error");
                $("#loadingDiv").hide();
            }
        })
    }

    function getRollQuality(){
        let venderId = $("#venderId").val();
        $.ajax({
            url:"{{route('master.quality.vender.map.list',':vendorId')}}".replace(':vendorId', venderId),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    $("#qualityId").empty();  
                    let option="<option value=''>select</option>";
                    data?.data.forEach((item)=>{
                        option+=`<option value="${item?.id}">${item?.quality}</option>`;
                    });
                    $("#qualityId").append(option);
                }
            },
            error: function (error) {
                $("#loadingDiv").hide();
                console.log(error);
            }

        });
    }

    function setGsm(){
        let gsmJson = $("#gsmJson").val();
        let gsm = 0;
        const parts = gsmJson.split(/\/+/);
        for (let part of parts) {
            gsm+= parseFloat(part);
        }
        if(gsmJson){
            $("#gsm").val(gsm);
        }
    }

    function showHideGsmJsonDiv(){
        if($("#rollType").val()=="BOPP"){
            $("#singleGsmEdit").hide();
            $("#multipleGsmEdit").show();
            $("#gsm").val("");
        }else{
            $("#multipleGsmEdit").hide();
            $("#singleGsmEdit").show();
            $("#gsmJson").val("");
        }
        setGsm();
    }

    function gsmJsonValidate(e) {
        var keyCode = e.which || e.keyCode;
        var value = e.target.value;
        
        // Ensure the input starts with a number (only on first character entry)
        if (value.length === 0 && (keyCode < 48 || keyCode > 57)) {
            popupAlert("Input must start with a number!");
            e.preventDefault();
            return false;
        }

        // Allow only numbers and slashes (no dots allowed)
        if ((keyCode < 48 || keyCode > 57) && keyCode !== 47) {
            popupAlert("Only numbers and '/' are allowed!");
            e.preventDefault();
            return false;
        }

        // Prevent consecutive slashes
        if (value.slice(-1) === '/' && keyCode === 47) {
            popupAlert("Consecutive '/' are not allowed!");
            e.preventDefault();
            return false;
        }

        // Count the number of slashes and restrict to a maximum of 3
        var slashCount = (value.match(/\//g) || []).length;
        if (slashCount >= 2 && keyCode === 47) {
            popupAlert("Only three '/' are allowed!");
            e.preventDefault();
            return false;
        }

        return true;
    }

    function setRollValue(roll){
        $("#editRollId").val(roll?.id);
        $("#rollNo").html(roll?.roll_no);
        $("#vendorIdHidden").val(roll?.vender_id);
        $("#venderName").val(roll?.vender_name);
        $("#purchaseDate").val(roll?.purchase_date);
        $("#qualityHidden").val(roll?.quality_id);
        $("#rollType").val(roll?.roll_type);
        $("#hardness").val(roll?.hardness);
        $("#gsm").val(roll?.gsm);
        $("#gsmJson").val(roll?.gsm_json ? JSON.parse(roll?.gsm_json).join("/"):"");
        $("#rollColor").val(roll?.roll_color);
        $("#length").val(roll?.length);
        $("#size").val(roll?.size);
        $("#netWeight").val(roll?.net_weight);
        $("#grossWeight").val(roll?.gross_weight);
        $("#printingColor").val(roll?.printing_color).trigger("change");
        setQualityDropDune(roll?.quality_id)
    }

    function setQualityDropDune(quality_id=null){
        let venderId = $("#vendorIdHidden").val();
        quality_id = quality_id  ? quality_id : $("#qualityHidden").val();
        console.log(quality_id);
        $.ajax({
            url:"{{route('master.quality.vender.map.list',':vendorId')}}".replace(':vendorId', venderId),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    $("#qualityId").empty();  
                    let option="<option value=''>select</option>";
                    data?.data.forEach((item)=>{
                        option+=`<option value="${item?.id}" ${item?.id==quality_id?"selected":""}>${item?.quality}</option>`;
                    });
                    $("#qualityId").append(option);
                }
            },
            error: function (error) {
                $("#loadingDiv").hide();
                console.log(error);
            }

        });
    }

    function resetForm(id){
        $("#"+id).get(0).reset();
        $('#'+id+' select').each(function() {
            if ($(this).data('select2')) {
                $(this).val(null).trigger('change');
            }
        });
    }
</script>