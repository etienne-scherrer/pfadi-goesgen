apollomin.local = {

};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function() {
  $("#info_kasten_pfadi").draggable({
    opacity : 0.45,
    cursor : "hand"
  });
  $("#info_kasten_woelfe").draggable({
    opacity : 0.45,
    cursor : "hand"
  });
  $("#info_kasten_news").draggable({
    opacity : 0.45,
    cursor : "hand"
  });
  apollomin.loadAnschlag(104, '#woelfe');
  apollomin.loadAnschlag(105, '#pfadi');

  var element = $('#news > ul');
  element = element.empty();
  apollomin.loadTextHandler(102, function(data) {
    if (data) {
      var datalength = data.length;
      if (datalength > 0) {
        //erste news detailierter ausgeben
        element.append(apollomin.toHtml([
            '<li class="pointer"> ',

            '    <div class="datum">',

            function(data) {
              return '      <p>' + apollomin.formatDate(data['evt_create']) + '</p>';
            },

            '    </div>',

            '<div class="anschlag"><a href="news.html"><h2>data[title]</h2></a><p class="teaser">data[teaser]</p></div></li>'
        ], [
          data[0]
        ]));
      }
      if (datalength > 1) {
        for ( var i = 1; i < datalength; i++) {
          //zusaetzliche news nur titel ausgeben
          element.append(apollomin.toHtml([
            '<li><a href="news.html">data[title]</a></li>'
          ], [
            data[i]
          ]));
        }
      }
    }

  });
});