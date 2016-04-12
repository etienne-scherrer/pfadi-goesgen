apollomin.local = {
  portraitTemplate : [
      '<li><h1>data[title]</h1><h2>data[teaser]</h2>',

      apollomin.getImageDiv,

      '  <div>data[text]</div><hr /></li>'
  ]
};

/* ---------------------------------------- */
// initialize
/* ---------------------------------------- */
$(function() {
  apollomin.loadText(103, $('#content > div > ul'), apollomin.local.portraitTemplate);
});