
<form action="" id="myForm" class="row g-3" >
    @csrf
    <input type="hidden"  name="id" id="id" />
    <div class="row">
        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bookingForClientId">Book For Client</label>
                    <div class="col-md-12">
                        <select name="bookingForClientId" id="bookingForClientId" class="form-control" required >
                            <option value="">Select</option>
                            @foreach ($pendingOrder as $val)
                            <option value="{{ $val->id }}" id="or{{$val->id}}" data-item="{{htmlspecialchars(json_encode($val))}}" >{{ $val->client_name  }} ({{$val->bag_w.'x'.$val->bag_l.($val->bag_g?('x'.$val->bag_g):"")}})</option>
                            @endforeach
                        </select>
                        <br>
                        <span class="error-text" id="bookingForClientId-error"></span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="orderDate">Order Date</label>
                    <input type="date" name="orderDate" id="orderDate" class="form-control" readonly />
                    <span class="error-text" id="orderDate-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                    <input type="date" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" readonly />
                    <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bagQuality">Bag Quality </label>
                    <select name="bagQuality" id="bagQuality" class="form-select" onchange="showHidePrintingColorDiv()" disabled>
                        <option value="">Select</option>
                        <option value="NW">NW</option>
                        <option value="BOPP">BOPP</option>
                        <option value="LAM">LAM</option>
                    </select>
                    <span class="error-text" id="bagQuality-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="gradeId">Roll Grade</label>
                    <select name="gradeId" id="gradeId" class="form-select" disabled>
                        <option value="">Select</option>
                        @foreach($grade as $val)
                        <option value="{{$val->id}}">{{$val->grade}}</option>
                        @endforeach
                    </select>
                    <span class="error-text" id="bagQuality-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="ratePerUnit">Rate Per Unit</label>
                    <input type="text" name="ratePerUnit" id="ratePerUnit" class="form-control" onkeypress="return isNumDot(event);" readonly />
                    <span class="error-text" id="ratePerUnit-error"></span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="rateTypeId">Rate Type </label>
                    <select name="rateTypeId" id="rateTypeId" class="form-select" disabled>
                        <option value="">Select</option>
                        @foreach($rateType as $val)
                        <option value="{{$val->id}}">{{$val->rate_type}}</option>
                        @endforeach
                    </select>
                    <span class="error-text" id="rateTypeId-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="fareTypeId">Fare</label>
                    <select name="fareTypeId" id="fareTypeId" class="form-select" disabled>
                        <option value="">Select</option>
                        @foreach($fare as $val)
                        <option value="{{$val->id}}">{{$val->fare_type}}</option>
                        @endforeach
                    </select>
                    <span class="error-text" id="fareTypeId-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="stereoTypeId">Stereo</label>
                    <select name="stereoTypeId" id="stereoTypeId" class="form-select" disabled>
                        <option value="">Select</option>
                        @foreach($stereo as $val)
                        <option value="{{$val->id}}">{{$val->stereo_type}}</option>
                        @endforeach
                    </select>
                    <span class="error-text" id="stereoTypeId-error"></span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                    <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-select" onchange="showHideLoop()" disabled>
                        <option value="">Select</option>
                        @foreach ($bagType as $val)
                        <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                        @endforeach
                    </select>
                    <span class="error-text" id="bookingBagTypeId-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bookingBagColor">Bag Color </label>
                    <div class="col-md-12">
                        <select name="bookingBagColor" id="bookingBagColor" multiple="multiple" class="form-select" disabled>
                            <option value="">Select</option>
                            @foreach($rollColor as $val)
                            <option data-color="{{$val->color}}" value="{{$val->color}}">{{$val->color}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="error-text" id="bookingBagColor-error"></span>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                    <select name="bookingBagUnits" id="bookingBagUnits" class="form-select" onchange="emptyTable()" disabled>
                        <option value="">Select</option>
                        <option value="Kg">Kg</option>
                        <option value="Piece">Piece</option>
                    </select>
                    <span class="error-text" id="bookingBagUnits-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="totalUnits">QTY</label>
                    <input name="totalUnits" id="totalUnits" class="form-control" onkeypress="return isNumDot(event);" onchange="getBalance()" readonly/>
                    <span class="error-text" id="bookingBagUnits-error"></span>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group" id='singleGsm'>
                    <label class="form-label" for="bagGsm">GSM</label>
                    <!-- <input name="bagGsm" id="bagGsm" class="form-control" onkeypress="return isNumDot(event);" readonly/> -->
                    <select name="bagGsm[]" id="bagGsm" class="form-select" multiple="multiple" disabled>
                        <option value="">All</option>
                        @foreach($gsm as $val)
                        <option value="{{$val}}">{{$val}}</option>
                        @endforeach
                    </select> 
                    <span class="error-text" id="bagGsm-error"></span>
                </div>
                <div class="form-group" id='multipleGsm' style="display: none;">
                    <label class="form-label" for="bagGsmJson">GSM</label>
                    <input name="bagGsmJson" id="bagGsmJson" class="form-control" placeholder="gsm/lamination/boop" onkeypress="return gsmJson(event); " onkeyup="setGsm();" readonly />
                    <span class="error-text" id="bagGsmJson-error"></span>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="w">W</label>
                    <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" readonly />
                    <span class="error-text" id="w-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="l">L </label>
                    <input name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" readonly />
                    <span class="error-text" id="l-error"></span>
                </div>
            </div>
        </div>

        <div class="row mt-3" id="loopColorDiv">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="looColor">Loop Color</label>
                    <input name="looColor" id="looColor" class="form-control" readonly />
                    <span class="error-text" id="looColor-error"></span>
                </div>
            </div>
            <div class="col-sm-4" id="gussetDiv">
                <div class="form-group">
                    <label class="form-label" for="g">G </label>
                    <input name="g" id="g" class="form-control" onkeypress="return isNumDot(event);" readonly />
                    <span class="error-text" id="g-error"></span>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-4" id="bookingPrintingColorDiv">
                <div class="form-group">
                    <label class="form-label" for="bookingPrintingColor">Printing Color</label>
                    <div class="col-md-12">
                        <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-select" multiple="multiple" disabled>
                            <option value="">Select</option>
                            @foreach($color as $val)
                            <option data-color="{{$val->color}}" value="{{$val->color}}" style="background-color:{{$val->color}};">{{$val->color}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="error-text" id="bookingPrintingColor-error"></span>
                </div>
            </div>

        </div>

        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="altBagColor">Alternate Bag Color</label>
                    <div class="col-md-12">
                        <select name="altBagColor[]" id="altBagColor" class="form-select select22" multiple="multiple" disabled > 
                            <option value="">Select</option> 
                            @foreach($altRollColor as $val)
                            <option value="{{$val->color}}">{{$val->color}}</option>
                            @endforeach
                        </select>
                    </div>                                                                                                          
                    <span class="error-text" id="altBagColor-error"></span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label class="form-label" for="altBagGsm">Alternate Gsm</label>
                    <div class="col-md-12">
                        <select name="altBagGsm[]" id="altBagGsm" class="form-select select22" disabled multiple="multiple" > 
                            <option value="">Select</option>                                        
                            @foreach($altGsm as $val)
                            <option value="{{$val}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>                                                                                                          
                    <span class="error-text" id="altBagGsm-error"></span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label" for="balance">Balance</label>
                        <input type="text" id="balance" readonly>                   
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="form-label" for="possibleProduction">Possible Units</label>
                        <input type="text" id="possibleProduction" readonly>                   
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary" id="submit" disabled >Submit</button>
    </div>
</form>