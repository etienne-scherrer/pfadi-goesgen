apollomin.local = {
    loadDownloadFiles: function() {
        $.ajax({
            url     : "data/page/downloads.php",
            dataType: "json"
        }).done(function(response) {
            if (!response['success']) {
                console.error("Failed to get download files.", response['message']);
            }
            else {
                var downloads = response['data'];
                if (!downloads || downloads.lenght == 0) {
                    return;
                }
                var target = $('#downloads > tbody');
                target.empty();
                var trElement = null;
                $.each(downloads, function(index, value) {
                    trElement = $('<tr class="download_files"></tr>');
                    var tmp   = apollomin.toHtml(apollomin.local.galerienTemplate, [value]);
                    trElement.append(tmp + '<hr />');
                });
                if (trElement != null) {
                    target.append(trElement);
                }
            }
        }).fail(function(jqXHR, statusText, error) {
            console.error("Failed to get galerien: " + statusText, jqXHR);
        });
    }
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function() {
    apollomin.local.loadDownloadFiles();
});