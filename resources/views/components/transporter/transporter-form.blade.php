<!-- Modal Form -->
<div class="modal fade modal-lg" id="transporterModal" tabindex="-1" aria-labelledby="transporterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transporterModalLabel">Add Transporter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="transporterForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="transporterName">Transporter Name<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="transporterName" name="transporterName" class="form-control" placeholder="Enter Transporter Name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="mobileNo">Mobile No<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="mobileNo" name="mobileNo" class="form-control" placeholder="Enter Contact No" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="isBus" name="isBus" checked>
                            <label class="form-check-label" for="isBus">Is Bus</label>
                        </div>                        
                    </div>
                    <div class="mb-3" id="gstDiv">
                        <label class="form-label" for="gstNo">GST No<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="gstNo" name="gstNo" class="form-control" placeholder="Enter GST No" >
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