var currentEventIndex = 0;
var mtgEvents = null;

$(document).ready(function(){
  $.get('/api/mtg/top_decks', function(res){

    $("#top-decks .loader").hide();

    if( res.length && res[0]['decks'].length ) {
      currentEventIndex = 0;
      mtgEvents = res;
      renderEvent( res[0] );
    }
    else {
      $("#top-decks").append("<div>Unable to retrieve data.</div>");
    }
  });

  function renderEvent(event) {
    $("#top-decks").empty();

    $("#top-decks").append('<div><small>Decks</small></div>');

    for(var i=0; i<event['decks'].length; i++) {
      $("#top-decks").append('<div><a href="'+event['decks'][i]['link']+'" target="_blank"><small>'+event['decks'][i]['name']+"</small></a></div>");
    }

    $("#top-decks").prepend('<div class="mb-3" style="width: 90%; margin: 0 auto;"><small>Event '+(currentEventIndex+1)+' of '+mtgEvents.length+'<br/><a href="'+event['event_link']+'" target="_blank">'+event['event_name']+'</a></small></div>');

    if( mtgEvents.length > 1 ) {

      var nav = '';

      nav += '<button data-index="'+(currentEventIndex-1)+'" type="button" class="prevMTGEventBtn btn btn-sm btn-link" '+(currentEventIndex==0?'disabled':'')+'><i class="fas fa-angle-double-left"></i> Prev Event</button>';
      nav += '<button data-index="'+(currentEventIndex+1)+'" type="button" class="nextMTGEventBtn btn btn-sm btn-link" '+(currentEventIndex == (mtgEvents.length-1)?'disabled':'')+'>Next Event <i class="fas fa-angle-double-right"></i></button>';

      $("#top-decks").append('<div class="mt-3">'+nav+'</div>');
    }
  }

  $("body").on("click", ".nextMTGEventBtn, .prevMTGEventBtn", function(){
    if( mtgEvents ) {
      currentEventIndex = $(this).data('index');
      event = mtgEvents[currentEventIndex];
      renderEvent(event);
    }
  })
});