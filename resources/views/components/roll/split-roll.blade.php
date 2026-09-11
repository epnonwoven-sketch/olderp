<div class="modal fade" id="rollSplitModal" tabindex="-1" aria-labelledby="rollSplitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> <div class="modal-content shadow border-0">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="rollSplitModalLabel">
                    <i class="bi bi-scissors me-2"></i>Split Roll
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="rollSplitModalForm">
                    @csrf
                    <input type="hidden" id="rollSplitId" name="rollSplitId" value="">

                    <div class="row g-3"> 
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="rollSplitNetWight">Net Weight <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="rollSplitNetWight" id="rollSplitNetWight" class="form-control" placeholder="0.00" required>
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-danger error-text" id="rollSplitNetWight-error"></small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="rollSplitGrossWeight">Gross Weight <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="rollSplitGrossWeight" id="rollSplitGrossWeight" class="form-control" placeholder="0.00" required>
                                <span class="input-group-text">kg</span>
                            </div>
                            <small class="text-danger error-text" id="rollSplitGrossWeight-error"></small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="rollSplitSize">Size <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="rollSplitSize" id="rollSplitSize" class="form-control" placeholder="0" required>
                                <span class="input-group-text">m</span>
                            </div>
                            <small class="text-danger error-text" id="rollSplitSize-error"></small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold" for="rollSplitLength">Length <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="rollSplitLength" id="rollSplitLength" class="form-control" placeholder="0" required>
                                <span class="input-group-text">m</span>
                            </div>
                            <small class="text-danger error-text" id="rollSplitLength-error"></small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4" id="rollSplit">
                                <i class="bi bi-check2-circle me-1"></i> Confirm Split
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const $form = $("#rollSplitModalForm");
        const $modal = $('#rollSplitModal');

        // Reset form when modal opens
        $modal.on('shown.bs.modal', function () {
            $form[0].reset();
            $(".error-text").text(""); // Clear old errors
        });

        // Validation
        $form.validate({
            rules: {
                rollSplitId: "required",
                rollSplitNetWight: { required: true, number: true },
                rollSplitGrossWeight: { required: true, number: true },
                rollSplitSize: {required:true,number:true},
                rollSplitLength: { required: true, number: true }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('text-danger small');
                element.closest('.col-md-4').append(error);
            },
            submitHandler: function(form) {
                showConfirmDialog("Are you sure you want to split this roll?", splitRollSubmit);
            }
        });

        function splitRollSubmit() {
            const $btn = $("#rollSplit");
            
            $.ajax({
                url: "{{ route('roll.split') }}",
                type: "POST",
                data: $form.serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
                },
                success: function(response) {
                    if (response.status) {
                        $modal.modal("hide");
                        searchData();
                        modelInfo("Roll split successfully!", "success");
                    } else {
                        handleErrors(response);
                    }
                },
                error: function() {
                    modelInfo("A server error occurred.", "error");
                },
                complete: function() {
                    $("#loadingDiv").hide();
                    $btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Confirm Split');
                }
            });
        }

        function handleErrors(response) {
            if (response.errors) {
                modelInfo(response.message || "Validation Error", "warning");
                Object.keys(response.errors).forEach(field => {
                    const cleanField = field.replace(/\./g, '\\.');
                    $(`#${cleanField}-error`).text(response.errors[field][0]);
                });
            } else {
                modelInfo("Operation failed.", "error");
            }
        }
    });
</script>