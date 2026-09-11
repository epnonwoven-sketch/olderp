<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="fileImportModal" tabindex="-1" aria-labelledby="fileImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileImportModalLabel">Import File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <!-- Hidden field for roll ID -->

                    <div class="row">
                        <div class="row mb-3">
                            <label for="csvFile" class="col-sm-4 col-form-label">CSV File.<span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                            <input type="file" id="csvFile" name="csvFile" class="form-control" required accept=".csv,.xlsx">
                            </div>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12" id="errorExcelLog" style="display: none;">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-client-form/>
<script>
    $(document).ready(function(){
    });
</script>