$(document).ready(function(){

  $('#guides-item-container').hide();

  $.get('/guides/get/latest', function(res){
    if(res.length) {

      for(var i=0; i<res.length; i++) {

        var guideItemHtml = '<a href="'+res[i].url+'" title="'+res[i].title+'">' +
                              '<div class="guides-posts-item mb-3">' +
                                '<!--div class="text-center post-category" style="background: '+res[i].color_code+';">'+res[i].category+'</div-->' +
                                '<div class="guides-posts-tn d-flex flex-column justify-content-center lazy" style="background: url('+(res[i].thumbnail?res[i].thumbnail:'https://ccb-destiny.com/images/og-banner-ccb-7-nov.jpg')+') no-repeat center 20%/cover;" data-bg="url('+(res[i].thumbnail_large?res[i].thumbnail_large:'https://ccb-destiny.com/images/og-banner-ccb-7-nov.jpg')+')"></div>' +
                                '<div class="guides-posts-title bg-white text-dark" style="border-top: 5px solid '+res[i].color_code+'!important;">' +
                                  '<div class="p-3">'+
                                    '<small>'+res[i].title+'</small>'+
                                  '</div>' +
                                '</div>' +
                              '</div>' +
                            '</a>';

        $('#guides-item-container').append('<div class="col-md-4">'+guideItemHtml+'</div>');
      }

      // Lazy load
      if (lazyLoadInstance) {
        lazyLoadInstance.update();
      }

      $('section#guides > div.loader, section#guides > div.loader-text').hide();
      $('#guides-item-container').fadeIn();
    }
    else {
      $('section#guides > div.loader, section#guides > div.loader-text').hide();
      $("#guides-item-container").append('<div class="col-md-12 text-center p-2">Unable to retrieve guides :(</div>');
      $('#guides-item-container').fadeIn();
    }
  })
  .fail(function(){
    $('section#guides > div.loader, section#guides > div.loader-text').hide();
    $("#guides-item-container").append('<div class="col-md-12 text-center p-2">Unable to retrieve guides :(</div>');
    $('#guides-item-container').fadeIn();
  });
})