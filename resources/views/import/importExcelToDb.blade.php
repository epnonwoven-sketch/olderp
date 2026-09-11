@include("layout.header")
    <main class="p-3">
        <div class="container-fluid">
            <div class="mb-3 text-left">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb fs-6">
                        <li class="breadcrumb-item fs-6"><a href="#">Import</a></li>
                        <li class="breadcrumb-item active fs-6" aria-current="page">Excel</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="container">            
            <div class="panel-body">
                <form id="importForm" method="post" enctype="multipart/form-data">
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

    </main>
    <script>
        $("#importForm").validate({
            rules: {
                csvFile: {
                    required: true,
                }
            },
        });

    </script>
@include("layout.footer")