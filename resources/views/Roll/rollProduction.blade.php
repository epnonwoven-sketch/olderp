@include("layout.header")
<!-- Main Component -->



<!-- Modal Form -->
<style>
    .modal-body {
        max-height: calc(100vh - 150px); /* Adjust height as needed */
        overflow-y: auto; /* Enable vertical scrolling */
    }
</style>

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}}</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">       
        <div class="panel-body">
            <form id="printingRoll">
                @csrf
                <!-- Hidden field for Client ID -->
                <input type="hidden" id="id" name="id" value="">

                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="printingUpdate">Date <span class="text-danger">*</span></label>
                                <input type="date" name="printingUpdate" id="printingUpdate" class="form-control" max="{{date('Y-m-d')}}" value="{{$privDate??''}}" required />
                                <span class="error-text" id="printingUpdate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="shift">Shift <span class="text-danger">*</span></label>
                                <select type="shift" name="shift" id="shift" class="form-select" required>
                                    <option value="">select</option>
                                    <option value="Day">Day</option>
                                    <option value="Night">Night</option>
                                </select>
                                <span class="error-text" id="shift-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="operatorId">Operator <span class="text-danger">*</span></label>
                                <select name="operatorId" id="operatorId" class="form-select" required>
                                    <option value="">Select</option>
                                    @foreach($operator as $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="operatorId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="helperId">Helper <span class="text-danger">*</span></label>
                                <select type="date" name="helperId" id="helperId" class="form-select"  required >
                                    <option value="">Select</option>
                                        @foreach($helper as $val)
                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                        @endforeach
                                </select>
                                <span class="error-text" id="helperId-error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="rollNo">Roll No</label>
                                <!-- <input type="text" id="rollNo" name="rollNo" class="form-control" onkeyup="getUnprintedRollNo()"> -->
                                <select name="rollNo" id="rollNo" class="form-control" onchange="setRollNo(event)">
                                
                                </select>
                                <div class="col-md-12" id="rollSuggestionList"></div>
                                <span class="error-text" id="rollNo-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end" >
                            <button type="button" id="search" class="btn btn-primary w-100" style="display: none;">Search</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><br></div>
                    </div>
                
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <th>Roll No</th>
                                    <!-- <th>Punches Date</th> -->
                                    <th>Roll Size</th>
                                    <th>Roll Color</th>
                                    <th>Client Name</th>
                                    <th>Bag Size</th>
                                    <th>Bag Type</th>
                                    <th>Printing Color</th>
                                    <th>Weight After Printing</th>
                                    <th>Color Ratio</th>
                                    <th>Remove</th>
                                </thead>
                                <tbody id="rollTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-sm-12 text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
</main>

<script>
    let sl = 0;
    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function () {
        $("#rollNo").select2({
            placeholder: "Search and select...",
            allowClear: true,
            width: "100%",
            ajax: {
                url: "{{ route('roll.search.printing.list') }}",
                type: "POST",
                dataType: "json",
                delay: 250, // Delay to reduce requests
                data: function (params) {
                    let rollId = [];
                    $("input[type='hidden'][id^='roll_id_']").each(function () {
                        rollId.push($(this).val());
                    });
                    return {
                        rollNo: params.term, // Send search term to the server
                        machineId:machineId,
                        rollId:rollId,
                    };
                },
                processResults: function (response) {
                    if (response.status && response.data) {
                        return {
                            results: response.data.map(item => ({
                                id: item.roll_no, // Unique ID for each option
                                text: item.roll_no // Show roll_no as option text
                            }))
                        };
                    } else {
                        return { results: [] }; // Return empty if no data
                    }
                },
                cache: true
            },
            minimumInputLength: 1 // Fetch results after typing at least 1 character
        });
        
        $("#id").val(machineId);
        // Handle Search Button Click
        $("#search").on("click", function () {
            searchPrinting();
        });

        // Handle Form Validation
        $("#printingRoll").validate({
            ignore: [],
            rules: {
                id: { required: true },
                printingUpdate: { required: true },
            },
            submitHandler: function (form) {
                // If form is valid, submit it
                if(testRatio()){
                    updateRollPrinting();
                }
            }
        });
    });

    function testRatio(){
        let isValid = true;
        $("#printingRoll table tr").each(function () {
            let totalPercent = 100;
            let dataId = $(this).attr("data-id");
            console.log(dataId);
            let id = $(this).attr("id");
            if (!dataId) {
                return; // Continue to the next iteration
            }

            $(`#${id} input[data-id='${dataId}']`).each(function () {
                totalPercent -= ($(this).val() ? parseFloat($(this).val()) : 0);
            });

            if (totalPercent !== 0) {
                alert($(this).attr("data-item") + " has no valid color ratio");
                isValid = false;
                return false; // Break `.each()` loop
            }
        });
        return isValid;
    }

    function searchPrinting(){
        let rollNo = $("#rollNo").val();
        let machineId = $("#id").val();
        if (rollNo != "") {
            $.ajax({
                url: "{{ route('roll.search.printing') }}",
                type: "POST",
                dataType: "json",
                data: { "rollNo": rollNo,"machineId":machineId },
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    if (data.status && data.data) {
                        $("#rollNo").empty().trigger("change").select2("open");
                        setTimeout(() => {
                            let searchField = document.querySelector(".select2-container--open .select2-search__field");
                            if (searchField) {
                                searchField.focus(); // Ensure the input gets focus
                                searchField.click(); // Simulate a click to activate it
                            }
                        }, 100);
                        let roll = data.data;
                        const rowId = `row${sl}`;
                        const colors = roll.printing_color.split(",");
                        const colorInputs = colors.map((color, index) => {
                            return `<input data-id="${sl}" value="${colors.length==1?100:''}" ${colors.length==1?'readonly':''} type='text' name='roll[${sl}][color][${index}]' id='color${index}_${rowId}' class='form-control dynamic-field' required onkeypress="return isNumDot(event);" onchange="calculatePercentNext(event)" placeholder='${color}' /> <input type='hidden' name='roll[${sl}][colorName][${index}]' value='${color}' />`;
                        }).join("");

                        let row = `
                            <tr id='${rowId}' data-id="${sl}" data-item="${roll.roll_no}">
                                <td>
                                    <input type='hidden' name='roll[${sl}][id]' id="roll_id_${sl}" value='${roll.id}' />${roll.roll_no}
                                </td>
                                
                                <td>${roll.size}</td>
                                <td>${roll.roll_color}</td>
                                <td>${roll.client_name}</td>
                                <td>${roll.bag_size}</td>
                                <td>${roll.bag_type}</td>
                                <td>${roll.printing_color}</td>
                                <td>
                                    <input type='text' name='roll[${sl}][printingWeight]' id='printingWeight${rowId}' class='form-control dynamic-field' min='${roll.net_weight}' onkeypress="return isNumDot(event);" required />
                                </td>
                                <td>${colorInputs}</td>
                                <td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>
                            </tr>`;
                        $("#rollTableBody").append(row);
                        sl = sl+1;
                        applyValidationRules(rowId);
                    } else if(data.status){
                        modelInfo(data.message || "Invalid Roll No", "warning");
                    }
                    else {
                        modelInfo(data.messages || "Invalid Roll No", "warning");
                    }
                }
            });
        }
    }

    // Apply validation rules to dynamically added fields
    function applyValidationRules(rowId) {
        $(`#${rowId} input.dynamic-field`).each(function () {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "This field is required.",
                },
            });
        });
    }

    // Remove Row from Table
    function removeTr(element) {
        $(element).closest("tr").remove();
    }

    // Update Roll Printing (Submit form via AJAX)

    function updateRollPrinting() {
        $.ajax({
            type: "POST",
            url: "{{ route('roll.printing.update') }}",
            dataType: "json",
            data: $("#printingRoll").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    sl =0;
                    // Reset form and clear the table
                    $("#printingRoll")[0].reset();
                    $("#rollTableBody").empty();
                    $("#UpdatePrintingModel").modal('hide');
                    modelInfo(data.messages || "Update Successful!");
                } else if (data.errors) {
                    let errors = data.errors;
                    for (field in errors) {
                        $(`#${field}-error`).text(errors[field][0]);
                    }
                    modelInfo(data.messages || "Validation Error", "error");
                } else {
                    modelInfo("Something Went Wrong!!", "error");
                }
            },            
        });
    }

    function getUnprintedRollNo(){
        let rollNo = $("#rollNo").val();
        let machineId = $("#id").val();
        if(rollNo && rollNo.length>1){
            $.ajax({
                url: "{{ route('roll.search.printing.list') }}",
                type: "POST",
                dataType: "json",
                data: { "rollNo": rollNo,"machineId":machineId },
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    if (data.status && data.data.length>0) {
                        let rollNos = data.data;
                        let options = data?.data?.map((item)=>{
                            return`<option value="${item?.roll_no}">${item?.roll_no}</option>`;
                        });
                        let selectElement=`<select class="form-control" onchange="setRollNo(event)"><option>select</option>${options}</select>`;
                        $("#rollSuggestionList").html(selectElement);
                    } else if(data.status && data.data.length==0){
                        modelInfo("Invalid Roll No", "warning");
                    }
                    else {
                        modelInfo(data.messages || "Invalid Roll No", "warning");
                    }
                },
                error:function(error){
                    $("#loadingDiv").hide();
                }
            });
        }
    }

    function setRollNo(event){
        $("#rollNo").val(event.target.value);
        searchPrinting();
        $("#rollSuggestionList").html('');
        $("#rollNo").focus();
    }

    function calculatePercentNext(event) {
        let dataId = event.target.getAttribute("data-id");
        let inputVal = event.target.value;
        let totalPercent = 100;
        let emptyInput = [];

        $(`input[data-id='${dataId}']`).each(function () {
            let values = $(this).val();
            if (values === "") {
                emptyInput.push($(this).attr("id"));
            } else {
                totalPercent -= parseFloat(values);
            }
        });

        // if (totalPercent < 0) {
        //     event.target.value = inputVal.slice(0, -1); // Remove last character if total is negative
        //     return;
        // }

        if (emptyInput.length === 1) {
            $("#" + emptyInput[0]).val(totalPercent>=0?totalPercent:0);
        }
    }


</script>
@include("layout.footer")