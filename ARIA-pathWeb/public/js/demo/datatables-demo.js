// Call the dataTables jQuery plugin
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        "scrollX": true,
        "order": [[ 14, "desc" ],[ 13, "desc" ]]
    });

});
