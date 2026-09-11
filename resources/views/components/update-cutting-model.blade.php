<!-- Modal Form -->
<style>
    .modal-body {
        max-height: calc(100vh - 150px); /* Adjust height as needed */
        overflow-y: auto; /* Enable vertical scrolling */
    }
</style>
<div class="modal fade modal-lg" id="UpdateCuttingModel" tabindex="-1" aria-labelledby="UpdateCuttingModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UpdateCuttingModelLabel">Update Production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cuttingRoll">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="cuttingUpdate">Date <span class="text-danger">*</span></label>
                                <input type="date" name="cuttingUpdate" id="cuttingUpdate" class="form-control" max="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="cuttingUpdate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="rollNo">Roll No</label>
                                <input type="text" id="rollNo" name="rollNo" class="form-control">
                                <span class="error-text" id="rollNo-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for=""></label>
                                <input type="button" id="search" name="search" class="form-control btn btn-primary" value="Search" />
                            </div>
                        </div>
                    </div>
                    <hr>
                    <table class="table table-striped table-bordered table-responsive">
                        <thead>
                            <th>Roll No</th>
                            <th>Punches Date</th>
                            <th>Roll Size</th>
                            <th>Roll Color</th>
                            <th>Client Name</th>
                            <th>Printing Color</th>
                            <th>Weight After Cutting</th>
                            <th>Remove</th>
                        </thead>
                        <tbody id="rollTableBody">
                        </tbody>
                    </table>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let sl = 0;
    $(document).ready(function () {
        // Handle Search Button Click
        $("#search").on("click", function () {
            let rollNo = $("#rollNo").val();
            if (rollNo != "") {
                $.ajax({
                    url: "{{ route('roll.search.printing') }}",
                    type: "POST",
                    dataType: "json",
                    data: { "rollNo": rollNo },
                    beforeSend: function () {
                        $("#loadingDiv").show();
                    },
                    success: function (data) {
                        $("#loadingDiv").hide();
                        if (data.status && data.data) {
                            $("#rollNo").val("");
                            let roll = data.data;
                            const rowId = `row${sl}`;
                            const colors = roll.printing_color.split(",");
                            let row = `
                                <tr id='${rowId}'>
                                    <td>
                                        <input type='hidden' name='roll[${sl}][id]' value='${roll.id}' />${roll.roll_no}
                                    </td>
                                    <td>${roll.purchase_date}</td>
                                    <td>${roll.size}</td>
                                    <td>${roll.roll_color}</td>
                                    <td>${roll.client_name}</td>
                                    <td>${roll.printing_color}</td>
                                    <td>
                                        <input type='text' name='roll[${sl}][cuttingWeight]' id='cuttingWeight${rowId}' class='form-control dynamic-field' onkeypress="return isNumDot(event);" required />
                                    </td>
                                    <td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>
                                </tr>`;
                            $("#rollTableBody").append(row);
                            sl = sl+1;
                            applyValidationRules(rowId);
                        } else {
                            modelInfo(data.messages || "Invalid Roll No", "warning");
                        }
                    }
                });
            }
        });

        // Handle Form Validation
        $("#cuttingRoll").validate({
            ignore: [],
            rules: {
                id: { required: true },
                cuttingUpdate: { required: true },
            },
            submitHandler: function (form) {
                // If form is valid, submit it
                updateRollCutting();
            }
        });
    });

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

    function updateRollCutting() {
        $.ajax({
            type: "POST",
            url: "{{ route('roll.cutting.update') }}",
            dataType: "json",
            data: $("#cuttingRoll").serialize(),
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data.status) {
                    sl =0;
                    // Reset form and clear the table
                    $("#cuttingRoll")[0].reset();
                    $("#rollTableBody").empty();
                    $("#UpdateCuttingModel").modal('hide');
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

</script>
