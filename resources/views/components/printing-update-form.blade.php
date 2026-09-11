<div class="modal fade modal-lg" id="printingUpdateModal" tabindex="-1" aria-labelledby="printingUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printingUpdateModalLabel">Printing Update <span display_roll_no="roll_no_display" class="text-info"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="printingUpdateModalForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="printingUpdateRollId" name="printingUpdateRollId" value="">

                    <div class="row">
                        <div class="row mt-3">
                            <!-- Roll Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingUpdateDate">Printing Date<span class="text-danger">*</span></label>
                                    <input type="date" max="{{date('Y-m-d')}}" id="printingUpdateDate" name="printingUpdateDate" class="form-control" value="{{date('Y-m-d')}}" required>
                                    <span class="error-text" id="printingUpdateDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingUpdateWeight">Roll Weight After Print<span class="text-danger">*</span></label>
                                    <input  id="printingUpdateWeight" name="printingUpdateWeight" class="form-control" required onkeypress="return isNumDot(event);">
                                    <span class="error-text" id="printingUpdateWeight-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingUpdateMachineId">Printing Machine<span class="text-danger">*</span></label>
                                    <select  id="printingUpdateMachineId" name="printingUpdateMachineId" class="form-control" required>
                                        <option value="">Select</option>
                                        @foreach ($printingMachineList as $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="printingUpdateMachineId-error"></span>
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