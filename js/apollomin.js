//namespace
var apollomin = {

    /**
     * @param date a string representing a date
     * @returns A timestring in the format 'hh:mm', e.g. '19:32'
     */
    formatTime: function(date) {
        if (!date) {
            return '';
        }
        var d = new Date(Date.parse(date));
        return apollomin.padTime(d.getHours()) + ':' + apollomin.padTime(d.getMinutes());
    },

    padTime: function(i) {
        var result = i.toString();
        if (result.length < 2) {
            result = '0' + result;
        }
        return result;
    },

    dayNames  : [
        'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'
    ],
    monthNames: [
        'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'
    ],

    /**
     * @param date a string representing a date
     * @returns A datestring in the format 'Day &nbsp; dd. Month YYYY', e.g. 'Samstag &nbsp; 28. Januar 2012'
     */
    formatDate: function(date) {
        if (!date) {
            return '';
        }
        var d = new Date(Date.parse(date));
        return apollomin.dayNames[d.getDay()] + ' &nbsp; ' + d.getDate() + '. ' + apollomin.monthNames[d.getMonth()] + ' ' + d.getFullYear();
    },

    getThumbnailPath: function(fullImagePath) {
        return fullImagePath.substr(0, fullImagePath.lastIndexOf("/") + 1) + 'thumb/' + fullImagePath.substr(fullImagePath.lastIndexOf("/") + 1);
    },

    getImageDiv: function(data) {
        var imagePath = data['image_path'];
        if (imagePath && imagePath != '') {
            return '<div class="news_foto"><a class="fancybox" href="' + imagePath + '"><img src="' + apollomin.getThumbnailPath(imagePath) + '" class="news_bild"/></a></div>';
        }
        return '';
    },

    sampleData: [
        {
            event_nr       : 123,
            text_nr        : 122,
            start_location : 'Feuerwehrmagazin',
            end_location   : 'Pfadiheim',
            evt_start      : new Date().toISOString(),
            evt_end        : new Date().toISOString(),
            user_nr        : 100,
            type_uid       : 104,
            title          : 'Ferien',
            teaser         : 'Wir wünschen Euch tolle Sportferien im Schnee oder zuhause! :-) kommt unfallfrei zurück!',
            text           : 'Die Lagerdaten sind nun definitv für das SoLa!! Es wird in der ersten Sommerferienwoche stattfinden.',
            additional_text: 'Z Vieri, Z Trinke',
            evt_create     : new Date().toISOString()
        }
    ],

    anschlagHtmlTemplate: [
        '<ul>',
        '  <li class="pointer">',
        '    <div class="datum">',
        function(data) {
            return '      <p>' + apollomin.formatDate(data['evt_start']) + '</p>';
        },
        '    </div>',
        '    <div class="anschlag">',
        '      <h2>data[title]</h2>',
        '      <p class="teaser">data[teaser]</p>',
        '      <hr />',
        '      <table>',
        '        <tr class="antreten">',
        '          <th>Sammlung:</th>',
        '          <td>data[start_location]</td>',
        function(data) {
            return '          <td>' + apollomin.formatTime(data['evt_start']) + '</td>';
        },
        '        </tr>',
        '        <tr class="besammeln">',
        '          <th>Abtreten:</th>',
        '          <td>data[end_location]</td>',
        function(data) {
            return '          <td>' + apollomin.formatTime(data['evt_end']) + '</td>';
        },
        '        </tr>',
        '        <tr class="besammeln">',
        '          <th>Mitnehmen:</th>',
        '          <td colspan="2">data[additional_text]</td>',
        '        </tr>',
        '      </table>',
        '      <hr />',
        '      <div class="text">data[text]</div>',
        '    </div>',
        '  </li>',
        '</ul>'
    ],

    /**
     * @param typeUid e.g. 104 fuer die Woelfe und 105 fuer die Pfader
     * @param divId e.g. '#woelfe'
     */
    loadAnschlag: function(typeUid, divId) {
        var div = $(divId).empty();

        $.ajax({
            url     : "data/page/events.php",
            data    : {
                'typeUid': typeUid
            },
            method: 'POST',
            dataType: "json"
        }).done(function(response) {
            if (!response['success']) {
                console.error("Failed to get next event.", response['message']);
            }
            else {
                div.append(apollomin.toHtml(apollomin.anschlagHtmlTemplate, response['data']));
                //init fancybox for images
                $('.fancybox').fancybox();
            }
        }).fail(function(jqXHR, statusText, error) {
            console.error("Failed to get next event: " + statusText, jqXHR);
        });
    },

    /**
     * @param typeUid e.g. 102 fuer news und 103 fuer portrait
     * @param element a jquery element
     */
    loadText: function(typeUid, element, htmlTemplate) {
        element = element.empty();
        apollomin.loadTextHandler(typeUid, function(data) {
            element.append(apollomin.toHtml(htmlTemplate, data));
            //init fancybox for images
            $('.fancybox').fancybox();
        });
    },

    /**
     * @param typeUid e.g. 102 fuer news und 103 fuer portrait
     * @param element a jquery element
     */
    loadTextHandler: function(typeUid, handler) {
        $.ajax({
            url     : "data/page/texts.php",
            data    : {
                'typeUid': typeUid
            },
            method: 'POST',
            dataType: "json"
        }).done(function(response) {
            if (!response['success']) {
                console.error("Failed to get text.", response['message']);
            }
            else {
                handler(response['data']);
            }
        }).fail(function(jqXHR, statusText, error) {
            console.error("Failed to get text: " + statusText, jqXHR);
        });
    },

    toHtml: function(template, data) {
        var result = '';
        if (!data || data.lenght == 0) {
            return result;
        }
        var datalength = data.length;
        var length     = template.length;
        var item, index, key;
        for (var i = 0; i < datalength; i++) {
            for (var j = 0; j < length; j++) {
                item = template[j];
                if ($.isFunction(item)) {
                    result = result + item(data[i]);
                }
                else {
                    while (item.indexOf('data[') >= 0) {
                        index = item.indexOf('data[');
                        key   = item.substr(index + 5, item.indexOf(']', index) - index - 5);
                        item  = item.replace('data[' + key + ']', data[i][key]);
                    }
                    result = result + item + '\n';
                }
            }
        }
        return result;
    },

    setBackground: function () {
        //set random background
        var numRand = Math.floor(Math.random() * 15) + 1;
        $('body').css({
            "background": "url(bilder/background/bg-" + numRand + ".jpg) no-repeat fixed left top / cover",
            "min-width" : '100%'
        });
    }
};

/* ---------------------------------------- */
//initialize (run on each site)
/* ---------------------------------------- */
$(function() {
   apollomin.setBackground();
});

//appcache: if there was an update, refresh page (without confirm)
if (window.applicationCache) {
    window.applicationCache.addEventListener('updateready', function() {
        if (window.applicationCache.status === window.applicationCache.UPDATEREADY) {
            window.applicationCache.swapCache();
            console.log("appcache updated");
            window.location.reload();
        }
    }, false);
    window.applicationCache.addEventListener('error', function(msg) {
        console.log("appcache error " + msg);
    }, false);
}