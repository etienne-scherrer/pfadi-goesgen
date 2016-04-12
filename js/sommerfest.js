apollomin.local = {
    sommerfestTemplate: [
        '<li><h1>data[title]</h1><h2>data[teaser]</h2>', apollomin.getImageDiv, '<div>data[text]</div><hr /></li>'
    ]
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function () {
    apollomin.loadText(110, $('#content > div > ul'), apollomin.local.sommerfestTemplate);
});