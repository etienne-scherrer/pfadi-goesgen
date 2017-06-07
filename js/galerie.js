apollomin.local = {

    galerienTemplate: [
        '<td><h2 class="galerie_name">data[name]</h2><div class="vorschau_bild">', function(data) {
            var imagePath = data['head_img'];
            if (imagePath && imagePath !== '') {
                return '<a class="galerie" href="' + imagePath + '" data-path="'+ data['path'] +'"><img src="' + apollomin.getThumbnailPath(imagePath) + '" alt="" /></a>';
            }
            return '';
        }, '</div><p class="anzahl_bilder">data[count] Bilder</p></td>'
    ],

    loadGalerien: function() {
        $.ajax({
            url     : "data/page/galerie.php",
            dataType: "json"
        }).done(function(response) {
            if (!response['success']) {
                console.error("Failed to get gallery.", response['message']);
            }
            else {
                var galerien = response['data'];
                if (!galerien || galerien.lenght === 0) {
                    return;
                }
                var target = $('#galerien > tbody');
                target.empty();
                var length    = galerien.length;
                var trElement = null;
                for (var i = 0; i < length; i++) {
                    if (i % 2 === 0) {
                        if (trElement !== null) {
                            target.append(trElement);
                            target.append('<tr class="galerie_abstand"><td colspan="2"><hr /></td></tr>');
                        }
                        trElement = $('<tr class="galerie_uebersicht"></tr>');
                    }
                    var tmp = apollomin.toHtml(apollomin.local.galerienTemplate, [
                        galerien[i]
                    ]);
                    trElement.append(tmp);
                }
                if (trElement !== null) {
                    target.append(trElement);
                }

                apollomin.local.installGalerieHandler();
            }
        }).fail(function(jqXHR, statusText, error) {
            console.error("Failed to get galerien: " + statusText, jqXHR);
        });
    },

    installGalerieHandler: function() {
        //install handler for fancybox / galerien
        $(".galerie").click(function() {
            var path = $(this).attr('data-path');
            $.ajax({
                url     : "data/page/galerie.php",
                data    : {
                    'path': path
                },
                dataType: "json"
            }).done(function(response) {
                if (!response['success']) {
                    console.error("Failed to get gallery.", response['message']);
                }
                else {
                    $.fancybox.open(response['data'], {
                        nextEffect: 'none',
                        prevEffect: 'none',
                        padding   : 0,
                        afterClose: function () {
                            window.setTimeout(function () {
                                apollomin.setBackground()
                            }, 50);
                        }
                    });
                }
            }).fail(function(jqXHR, statusText, error) {
                console.error("Failed to get gallery: " + statusText, jqXHR);
            });
            return false;
        });
    }
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function() {
    apollomin.local.loadGalerien();
});