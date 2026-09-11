@include("layout.header")

<!-- Add Custom Styles -->
<style>
    .card {
        /* background: #fff; */
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);  */
        /* Optional shadow for design */
        border: none; /* Remove border */
        border-radius: 10px; /* Keep rounded corners */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        padding: 15px; /* Adjust padding */
        text-align: center;
        /* overflow: hidden;  */
        /* Prevent content overflow */
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        /* overflow-y: auto;  */
        /* Add scrolling for overflowing content */
    }
    .card-body-title {
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        padding: 5px 0;
        /* background-color: #007BFF;
        color: white; */
        margin: 0;
    }


    .row.g-3 {
        gap: 1.5rem; /* Adjust the gap between rows */
    }

    /* Scrollbar hidden but allow scrolling */
    body::-webkit-scrollbar {
        display: none;
    }
</style>

<!-- Main Component -->
<main class="p-3">
    <div class="container">
        <div class="panel-body" style="text-align: center;">
            <div class="row">
                <div class="card text-center col-md-4" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                    <h5 class="card-header">Loop Stock</h5>
                    <div class="card-body">
                        <p class="card-text">
                            <x-dashboard.loop-stock-status />
                        </p>
                    </div>
                </div>
                <div class="card text-center col-md-8" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                    <h5 class="card-header">Nearest Dispatched Order</h5>
                    <div class="card-body">
                        <p class="card-text">
                            @livewireStyles
                            @livewire('dashboard.nearest-dispatched-order')
                            @livewireScripts
                            @stack('scripts') 
                            <!-- Include the custom scripts -->
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="card text-center col-md-12" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                    <h5 class="card-header">Roll Stock</h5>
                    <div class="card-body">
                        <x-dashboard.roll-stock-status />
                        <!-- <p class="card-text">
                        </p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@include("layout.footer")
