<!-- Modal Form -->
<div class="modal fade modal-lg" id="autoModal" tabindex="-1" aria-labelledby="autoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="autoModalLabel">Add Auto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="autoForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="autoName">Auto Name<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="autoName" name="autoName" class="form-control" placeholder="Enter Auto Name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="MobileNo">Mobile No<span class="text-danger">*</span></label>
                        <input type="text" maxlength="15" id="MobileNo" name="MobileNo" class="form-control" placeholder="Enter Mobile No" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="autoNo">Auto No<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="autoNo" name="autoNo" class="form-control" placeholder="Enter Auto No" required>
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