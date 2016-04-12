apollomin.local = {
  aktuell : 'start',

  beschreibung : {
    start : 'Willkommen im Pfadiheim Niedergösgen.<br /> Klicke auf einen Raum im Grundriss um Bilder und mehr Infos zu erfahren. ',
    pfadiraum1 : 'Dieser Bereich ist der Pfadi Gösgen vorbehalten.',
    pfadiraum2 : 'Dieser Bereich ist der Pfadi Gösgen vorbehalten.',
    aufenthaltsraum : 'Das ist der Aufenthaltsraum',
    schlafraum : '3 mal Schlafräume',
    kueche : 'Die Küche',
    duschraum : 'Dusch und Waschraum',
    wc : 'Toiletten',
    eingang_mieter : 'Eingang Mieter',
    eingang_pfadi : 'Eingang Pfadi Gösgen'
  },

  imageslocation : {
    aufenthaltsraum : [
        {
          src : "bilder/pfadiheim/aufenthaltsraum/pfadiheim_aufenthaltsr0007.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/aufenthaltsraum/pfadiheim_aufenthaltsr0008.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/aufenthaltsraum/pfadiheim_aufenthaltsr0009.jpg",
          width : 233
        }, {
          src : "bilder/pfadiheim/aufenthaltsraum/pfadiheim_aufenthaltsr0010.jpg",
          width : 526
        }
    ],
    duschraum : [
        {
          src : "bilder/pfadiheim/waschraum/pfadiheim_wasch_43.jpg",
          width : 233
        }, {
          src : "bilder/pfadiheim/waschraum/pfadiheim_wasch_44.jpg",
          width : 233
        }, {
          src : "bilder/pfadiheim/waschraum/pfadiheim_wasch_45.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/waschraum/pfadiheim_wasch_47.jpg",
          width : 526
        }
    ],
    eingang_mieter : [
      {
        src : "bilder/pfadiheim/pfadiheim/vorne.jpg",
        width : 3341
      }
    ],
    eingang_pfadi : [
      {
        src : "bilder/pfadiheim/pfadiheim/vorne.jpg",
        width : 3341
      }
    ],
    kueche : [
        {
          src : "bilder/pfadiheim/kueche/pfadiheim_kueche_1.jpg",
          width : 459
        }, {
          src : "bilder/pfadiheim/kueche/pfadiheim_kueche_2.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/kueche/pfadiheim_kueche_4.jpg",
          width : 526
        }
    ],
    start : [
      {
        src : "bilder/pfadiheim/pfadiheim/vorne.jpg",
        width : 3341
      }
    ],
    pfadiraum1 : [
      {
        src : "bilder/pfadiheim/pfadiheim/vorne.jpg",
        width : 3341
      }
    ],
    pfadiraum2 : [
      {
        src : "bilder/pfadiheim/pfadiheim/vorne.jpg",
        width : 3341
      }
    ],
    schlafraum : [
        {
          src : "bilder/pfadiheim/schlafraum/pfadiheim_schlafraum_1.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/schlafraum/pfadiheim_schlafraum_2.jpg",
          width : 526
        }
    ],
    wc : [
        {
          src : "bilder/pfadiheim/wc/pfadiheim_wc_1.jpg",
          width : 526
        }, {
          src : "bilder/pfadiheim/wc/pfadiheim_wc_2.jpg",
          width : 233
        }
    ]
  },

  loadScrollPane : function() {
    var imageslocs = apollomin.local.imageslocation[apollomin.local.aktuell];
    for ( var imageloc in imageslocs) {
      $('#galerie_post_loaded_content').append("<img src=" + imageslocs[imageloc]['src'] + " alt='' height='350' width='" + imageslocs[imageloc]['width'] + "'/>");
    }
    $("#galerie_post_loaded_content").smoothDivScroll({
      hotSpotScrolling : true,
      touchScrolling : true,
    });
  },

  reloadScrollPane : function() {
    $('#galerie_post_loaded_content').smoothDivScroll("destroy");
    $('#galerie_post_loaded_content').empty();
    apollomin.local.loadScrollPane();
    $('#galerie_post_loaded_content').smoothDivScroll("recalculateScrollableArea");
  },

  showInfo : function(e) {
    var name = e.id;
    name = name.replace(/grundriss-/, "");
    $('#content_beschreibung').empty().append(apollomin.local.beschreibung[name]);
  },

  showImages : function(e) {
    $(".active").removeClass("active");
    $(e).addClass("active");

    var name = e.id;
    name = name.replace(/grundriss-/, "");
    apollomin.local.aktuell = name;
    apollomin.local.reloadScrollPane();
  },

  out : function() {
    $('#content_beschreibung').empty().append(apollomin.local.beschreibung[apollomin.local.aktuell]);
  }
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function() {
  apollomin.local.loadScrollPane();
  apollomin.local.out();
});
