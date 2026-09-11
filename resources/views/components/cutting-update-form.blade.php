<div class="modal fade modal-lg" id="cuttingUpdateModal" tabindex="-1" aria-labelledby="cuttingUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cuttingUpdateModalLabel">cutting Update <span display_roll_no="roll_no_display" class="text-info"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cuttingUpdateModalForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="cuttingUpdateRollId" name="cuttingUpdateRollId" value="">

                    <div class="row">
                        <div class="row mt-3">
                            <!-- Roll Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="cuttingUpdateDate">cutting Date<span class="text-danger">*</span></label>
                                    <input type="date" max="{{date('Y-m-d')}}" id="cuttingUpdateDate" name="cuttingUpdateDate" class="form-control" value="{{date('Y-m-d')}}" required>
                                    <span class="error-text" id="cuttingUpdateDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="cuttingUpdateWeight">Roll Weight After cutting<span class="text-danger">*</span></label>
                                    <input  id="cuttingUpdateWeight" name="cuttingUpdateWeight" class="form-control" required onkeypress="return isNumDot(event);">
                                    <span class="error-text" id="cuttingUpdateWeight-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="cuttingUpdateMachineId">cutting Machine<span class="text-danger">*</span></label>
                                    <select  id="cuttingUpdateMachineId" name="cuttingUpdateMachineId" class="form-control" required>
                                        <option value="">Select</option>
                                        @foreach ($cuttingMachineList as $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="cuttingUpdateMachineId-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>