$(document).ready(function(){


  if( typeof news_listing_category_slug !== 'undefined' ) {
    var category_map = {
      'magic-the-gathering': 'magic',
      'the-division-2': 'division',
      'destiny': 'destiny',
    };

    if( category_map[news_listing_category_slug] ) {
      $.get('/api/news/get/'+category_map[news_listing_category_slug], function(res){

        $("#news-listing-"+news_listing_category_slug+" .loader").hide();

        if( Object.keys(res).length ) {

          var i = 0;

          for(const item in res) {
            i++;
            $("#news-listing-" + news_listing_category_slug).append("<div class='mb-2'>"+i+". <a href='"+res[item].url+"' target='_blank'>"+res[item].title+"</a> | "+res[item].source+"</div>");
          }
        }
        else {
          $("#news-widget-"+news_listing_category_slug).remove();
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