<!-- Modal Form -->
<div class="modal fade modal-lg" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorModalLabel">Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vendorForm">
                    @csrf
                    <!-- Hidden field for Vendor ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                        <!-- Vendor Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="vendorName">Vendor Name<span class="text-danger">*</span></label>
                                <input type="text" maxlength="100" id="vendorName" name="vendorName" class="form-control" placeholder="Enter Vendor Name" required>
                            </div>
                        </div>
                        
                        <!-- Vendor GST Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="gstNo">GST Number</label>
                                <input type="text" maxlength="15" id="gstNo" name="gstNo" class="form-control" placeholder="Enter GST Number">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="email">Email</label>
                                <input type="email" maxlength="100" id="email" name="email" class="form-control" placeholder="vendor@gmail.com" >
                            </div>
                        </div>
                        
                        <!-- Vendor Mobile Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="mobileNo">Mobile Number</label>
                                <input type="text" maxlength="15" id="mobileNo" name="mobileNo" class="form-control" placeholder="Enter Mobile Number"  onkeypress="return isNum(event);">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label" for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" placeholder="Enter Vendor Address" rows="3"></textarea>
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
            </div>
        </div>
    </div>
</div>
