$(document).ready(function(){
  if( typeof news_listing_category_slug !== 'undefined' ) {
    var category_map = {
      'magic-the-gathering': 'magic',
      'the-division-2': 'division',
      'destiny': 'destiny',
    };

    if( category_map[news_listing_category_slug] ) {
      $.get('/api/news/get/' + category_map[news_listing_category_slug], function(res){

        $("#news-listing-"+news_listing_category_slug+" .loader").hide();

        if( res.length ) {
          for(var i=0; i<res.length; i++) {
            $("#news-listing-" + news_listing_category_slug).append("<div class='mb-2'>"+(i+1)+". <a href='"+res[i].url+"' target='_blank'>"+res[i].title+"</a> | "+res[i].source+"</div>");
          }
        }
        else {
          $("#news-widget-{{$top_category->term->slug}}").remove();
        }
      });
    }
  }
});

document.addEventListener('DOMContentLoaded', function () {
  tooltips = new mtgTooltip({
    'basiclands': 'zzz',
    'shocklands': 'new',
    'fetchlands': 'new',
    'painlands': 'new',
    'mobile': false,
    'lazyload': false
  });
});