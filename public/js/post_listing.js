$(document).ready(function(){
  $('.grid').masonry({
    itemSelector: '.grid-item',
    gutter: 0,
    columnWidth: '.grid-sizer',
    gutter: '.gutter-sizer',
    percentPosition: true
  });
});