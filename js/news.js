apollomin.local = {
    newsTemplate: [
        '<li><div><h1>data[title]</h1>',

        apollomin.getImageDiv,

        '<div class="news_text"><h2>data[teaser]</h2><div>data[text]</div><hr class="hr_quadrat" /></div></div></li>'
    ]
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function () {
    apollomin.loadAnschlag(104, '#woelfe');
    apollomin.loadAnschlag(105, '#pfadi');
    apollomin.loadAnschlag(106, '#biber');
    apollomin.loadText(102, $('#content > div > ul'), apollomin.local.newsTemplate);
    $("#info_kasten_pfadi").draggable({
        opacity: 0.45,
        cursor: "hand"
    });
    $("#info_kasten_woelfe").draggable({
        opacity: 0.45,
        cursor: "hand"
    });
});