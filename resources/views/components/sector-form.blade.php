<!-- Modal Form -->
<div class="modal fade modal-lg" id="sectorModal" tabindex="-1" aria-labelledby="sectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectorModalLabel">Add New Sector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sectorForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                        <!-- Client Name -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="sector">Sector<span class="text-danger">*</span></label>
                                <input type="text" maxlength="100" id="sector" name="sector" class="form-control" placeholder="Enter Client Name" required>
                                <span class="error-text" id="sector-error"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success" id="submit">Add Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
