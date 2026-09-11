<style>
    .info-table tr td:nth-child(1) {
        background-color: lightblue;
        color: rgb(226 2 31 / 70%);
        font-weight: bold;
    }
</style>
<!-- Modal Form -->
<div class="modal fade modal-lg" id="bagModal" tabindex="-1" aria-labelledby="bagModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bagModalLabel">Add New Bag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bagForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Client Name -->
                    <div class="mb-3">
                        <label class="form-label" for="bagType">Bag Type<span class="text-danger">*</span></label>
                        <input type="text" maxlength="100" id="bagType" name="bagType" class="form-control" placeholder="Enter Client Name" required>
                    </div>
                    <div class="mb-3">
                        <div class="row" style="font-size: smaller !important;">
                            <div class="col-sm-4">
                                <h6 class="text-info">Bag Related</h6>
                                <table class="table table-border table-striped info-table" >
                                    <tr>
                                        <td>L</td>
                                        <td>Bag Length</td>
                                    </tr>
                                    <tr>
                                        <td>W</td>
                                        <td>Bag Width</td>
                                    </tr>
                                    <tr>
                                        <td>G</td>
                                        <td>Bag Gusset</td>
                                    </tr>                                    
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <h6 class="text-info">Roll Related</h6>
                                <table class="table table-border table-striped info-table">
                                    <tr>
                                        <td>RL</td>
                                        <td>Roll Length</td>
                                    </tr>
                                    <tr>
                                        <td>RW</td>
                                        <td>Roll Weight</td>
                                    </tr>
                                    <tr>
                                        <td>RS</td>
                                        <td>Roll Size</td>
                                    </tr>
                                    <tr>
                                        <td>GSM</td>
                                        <td>Roll GSM (thickness)</td> 
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-sm-4">
                                <h6 class="text-info">Operator</h6>
                                <table class="table table-border table-striped info-table">
                                    <tr>
                                        <td>X</td>
                                        <td>Multiplication</td>
                                    </tr>
                                    <tr>
                                        <td>*</td>
                                        <td>Multiplication</td>
                                    </tr>
                                    <tr>
                                        <td>/</td>
                                        <td>Division</td>
                                    </tr>
                                    <tr>
                                        <td>+</td>
                                        <td>Addition</td>
                                    </tr>
                                    <tr>
                                        <td>-</td>
                                        <td>Subtraction</td> 
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Client Email -->
                    <div class="mb-3">
                        <label class="form-label" for="gsmVariation">Formula To GSM Variation<span class="text-danger">*</span></label>
                        <textarea id="gsmVariation" name="gsmVariation" class="form-control" placeholder="" required></textarea>

                    </div>

                    <div class="mb-3">
                        <label class="control-label" for="rollFind">Formula To Finding Roll<span class="text-danger">*</span></label>
                        <textarea id="rollFind" name="rollFind" class="form-control" placeholder="" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="control-label" for="rollFindAsWeight">Formula To Finding Roll By Weight <span class="text-danger">*</span></label>
                        <textarea id="rollFindAsWeight" name="rollFindAsWeight" class="form-control" placeholder="" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="control-label" for="rollSizeFind">Formula To Finding Roll size By Bag <span class="text-danger">*</span></label>
                        <textarea id="rollSizeFind" name="rollSizeFind" class="form-control" placeholder="" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="control-label" for="weightOfBagPerPiece">Formula To Finding Roll size By Bag <span class="text-danger">*</span></label>
                        <textarea id="weightOfBagPerPiece" name="weightOfBagPerPiece" class="form-control" placeholder="" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="control-label" for="cylinderSize">Formula To Finding Cylinder size <span class="text-danger">*</span></label>
                        <textarea id="cylinderSize" name="cylinderSize" class="form-control" placeholder="" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add Bag</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>