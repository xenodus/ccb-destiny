$(document).ready(function(){

  $('#news-item-container').hide();

  $.get('/api/news/get', function(res){
    if(res.length) {
      var destiny_news = [];
      var division_news = [];
      var magic_news = [];

      for(var i=0; i<res.length; i++) {
        switch(res[i].category) {
          case "destiny":
            destiny_news.push(res[i]);
            break;
          case "division":
            division_news.push(res[i]);
            break;
          case "magic":
            magic_news.push(res[i]);
            break;
          default:
        }
      }

      if( destiny_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="destiny-news-container" class="mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Destiny 2</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<destiny_news.length; i++) {
          $("#destiny-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+destiny_news[i].url+"' target='_blank'>"+destiny_news[i].title+"</a> | "+destiny_news[i].source+"</div>");
        }
      }

      if( magic_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="magic-news-container" class="mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Magic: The Gathering Arena</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<magic_news.length; i++) {
          $("#magic-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+magic_news[i].url+"' target='_blank'>"+magic_news[i].title+"</a> | "+magic_news[i].source+"</div>");
        }
      }

      if( division_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="division-news-container" class="mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Tom Clancy\'s The Division 2</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<division_news.length; i++) {
          $("#division-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+division_news[i].url+"' target='_blank'>"+division_news[i].title+"</a> | "+division_news[i].source+"</div>");
        }
      }

      $('section#news > div.loader, section#news > div.loader-text').hide();
      $('#news-item-container').fadeIn();
    }
    else {
      $('section#news > div.loader, section#news > div.loader-text').hide();
      $("#news-item-container").append('<div class="col-md-12 text-center p-2">Unable to retrieve news :(</div>');
      $('#news-item-container').fadeIn();
    }
  })
  .fail(function(){
    $('section#news > div.loader, section#news > div.loader-text').hide();
    $("#news-item-container").append('<div class="col-md-12 text-center p-4 mb-2">Unable to retrieve news :(</div>');
    $('#news-item-container').fadeIn();
  });
})