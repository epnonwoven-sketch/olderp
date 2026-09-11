<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true" style="z-index: 1000000000000000;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="confirmMessage">Are you sure?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmAction">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script>
    function showConfirmDialog(message, callback = "") {
        // Set the confirmation message
        document.getElementById('confirmMessage').textContent = message;

        // Set the action to be executed when the "Confirm" button is clicked
        const confirmButton = document.getElementById('confirmAction');
        confirmButton.onclick = function () {
          if (callback !== "") {
            callback(); // Call the provided callback function if it's not an empty string
          } else {
            // If callback is an empty string, return true (successful confirmation)
            console.log('Confirmation accepted, no action taken.');
            return true;
          }

            const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            confirmModal.hide(); // Hide the modal after confirming
        };

        // Show the modal
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
    }

    $(document).ready(function(){
      let modelInfo = {};
      let modalZIndex = 1050; // Define the z-index value here or dynamically

      $('#confirmModal').on('show.bs.modal', function() {
          $('.modal').each(function() {
              let modal = $(this);
              if (modal.attr('id') !== 'confirmModal') {
                  // Store the z-index of each modal except 'confirmModal'
                  let zIndex = modal.css('z-index');
                  modelInfo[modal.attr('id')] = zIndex;
                  modal.css('z-index', parseInt(zIndex)-10); // Set a lower z-index to all other modals
              }
          });
      });

      $('#confirmModal').on('hidden.bs.modal', function() {
          $('.modal').each(function() {
              let modal = $(this);
              let zIndex = modelInfo[modal.attr('id')];
              if (zIndex) {
                  // Restore the original z-index from modelInfo
                  modal.css('z-index', zIndex);
              }
          });
      });
    });

</script>