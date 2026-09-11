
</div>
</div>    
    <script>
        $(document).ready(function() {
            $("#loadingDiv").hide();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });            
            @if($result = flashToast('message')) {
                modelInfo('<?= $result; ?>');
            }
            @endif
        });
    </script>
</body>
<script src="{{ asset('js/script.js')}}"></script>
</html>