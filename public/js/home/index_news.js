$(document).ready(function(){

  $('#news-item-container').hide();

  $.get('/api/news/get', function(res){
    if(res.length) {
      var destiny_news = [];
      var apex_news = [];
      var mhw_news = [];

      for(var i=0; i<res.length; i++) {
        switch(res[i].category) {
          case "destiny":
            destiny_news.push(res[i]);
            break;
          case "apex":
            apex_news.push(res[i]);
            break;
          case "mhw":
            mhw_news.push(res[i]);
            break;
          default:
        }
      }

      if( destiny_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="destiny-news-container" class="news-container mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Destiny 2</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<destiny_news.length; i++) {
          $("#destiny-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+destiny_news[i].url+"' target='_blank'>"+destiny_news[i].title+"</a> | "+destiny_news[i].source+"</div>");
        }
      }

      if( mhw_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="mhw-news-container" class="news-container mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Monster Hunter World</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<mhw_news.length; i++) {
          $("#mhw-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+mhw_news[i].url+"' target='_blank'>"+mhw_news[i].title+"</a> | "+mhw_news[i].source+"</div>");
        }
      }

      if( apex_news.length > 0 ) {
        $("#news-item-container").append('<div class="col-md-4"><div id="apex-news-container" class="news-container mb-3 text-left border-warning border"><h2 class="text-center p-2 border-warning border-bottom">Apex Legends</h2><div class="news-listing-container pl-3 pr-3 pt-1 pb-1"></div></div></div>');

        for(var i=0; i<apex_news.length; i++) {
          $("#apex-news-container .news-listing-container").append("<div class='mb-2'>"+(i+1)+". <a href='"+apex_news[i].url+"' target='_blank'>"+apex_news[i].title+"</a> | "+apex_news[i].source+"</div>");
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