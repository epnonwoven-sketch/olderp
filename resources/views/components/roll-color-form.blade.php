<!-- Modal Form -->
<div class="modal fade modal-lg" id="rollColorModal" tabindex="-1" aria-labelledby="rollColorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollColorModalLabel">Add Color</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollColorForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="color">Color Name<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="color" name="color" class="form-control" placeholder="Enter Color Name" required>
                    </div>

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