<div class="modal fade modal-lg" id="printingScheduleModal" tabindex="-1" aria-labelledby="printingScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printingScheduleModalLabel">Set Printing Schedule <span id="roll_no_display" class="text-info"></span> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="printingScheduleModalForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="printingScheduleRollId" name="printingScheduleRollId" value="">

                    <div class="row">
                        <div class="row mt-3">
                            <!-- Roll Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingScheduleDate">Schedule Date<span class="text-danger">*</span></label>
                                    <input type="date" min="{{date('Y-m-d')}}" id="printingScheduleDate" name="printingScheduleDate" class="form-control" required>
                                    <span class="error-text" id="printingScheduleDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="printingMachineId">Select Machine<span class="text-danger">*</span></label>
                                    <select id="printingMachineId" name="printingMachineId" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach($printingMachine as $val)
                                        <option value="{{$val->id}}">{{$val->name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="printingMachineId-error"></span>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="sl">Print Sl<span class="text-danger">*</span></label>
                                    <input id="sl" name="sl" class="form-control" required onkeypress="return isNum(event);"/>
                                    <span class="error-text" id="sl-error"></span>
                                </div>
                            </div> -->
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