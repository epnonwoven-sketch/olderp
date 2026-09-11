<div class="modal fade modal-lg" id="cuttingScheduleModal" tabindex="-1" aria-labelledby="cuttingScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cuttingScheduleModalLabel">Set cutting Schedule <span display_roll_no="roll_no_display" class="text-info"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cuttingScheduleModalForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="cuttingScheduleRollId" name="cuttingScheduleRollId" value="">

                    <div class="row">
                        <div class="row mt-3">
                            <!-- Roll Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="cuttingScheduleDate">Schedule Date<span class="text-danger">*</span></label>
                                    <input type="date" min="{{date('Y-m-d')}}" id="cuttingScheduleDate" name="cuttingScheduleDate" class="form-control" required>
                                    <span class="error-text" id="cuttingScheduleDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="cuttingMachineId">Select Machine<span class="text-danger">*</span></label>
                                    <select id="cuttingMachineId" name="cuttingMachineId" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($cuttingMachine as $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="cuttingMachineId-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Schedule</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>