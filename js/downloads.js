apollomin.local = {
    downloadTemplate: [
        function (data) {
            return '<td><h2 class="download_file">' + data['title'] + '</h2><a href=' + data['href'] +'>Download</a></td>'
        }
    ],
    loadDownloadFiles: function() {
        $.ajax({
            url     : "data/page/downloads.php",
            dataType: "json"
        }).done(function(response) {
            if (!response['success']) {
                console.error("Failed to get download files.", response['message']);
            } else {
                var downloads = response['data'];
                if (!downloads || downloads.lenght === 0) {
                    return;
                }
                var target = $('#downloads').find('> tbody');
                target.empty();
                var downloadElement = null;
                $.each(downloads, function(index, value) {
                    var template    = apollomin.toHtml(apollomin.local.downloadTemplate, [value]);
                    downloadElement = $('<tr class="download_files"></tr>');
                    downloadElement.append(template);
                    target.append(downloadElement);
                    target.append($('<hr>'));
                });
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