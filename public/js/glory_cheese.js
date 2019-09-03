$(document).ready(function(){

  $(".typeahead").typeahead({
    source: ccbNS.clanMembers,
    minLength: 0
  });

  getCheese();

  $(".player-point").focus(function(){
    if($(this).val() == 0)
      $(this).val('');
  });

  $(".player-point").focusout(function(){
    if($(this).val() == 0 || $(this).val() == '') {
      $(this).val(0);
    }
  });

  $("input").on("change input", function(){
    getCheese();
  });

  $("#fetchGlory").on("click", function(){

    $(".fetch-status > small").text('Fetching . . .').show();
    $(".player-point").val(0);

    var playerPoints = [];

    $('.player-points').each(function(){
      var name = $(this).find('.player-name').val();
      var point = $(this).find('.player-point').val();

      if( name && point ) {
        playerPoints.push({
          'name': name,
          'point': point,
        });
      }
    });

    var names = playerPoints.map(function(player){ return player.name; });

    $.post('/api/pvp/update_glory_from_db', {
      names: names.join(','),
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, function(data){
      if(data.length > 0) {
        for(var i=0; i<data.length; i++) {
          $('input.player-name').filter(function() { return this.value == data[i].name }).parent().siblings().find('.player-point').val( data[i].glory );
        }

        $(".fetch-status > small").text('Done!').fadeOut('1000');

        getCheese();
      }
    });
  });

  $("#reset").on("click", function(){
    $('.player-name').each(function(index){
      $(this).val('Player ' + (index+1));
    })
    $('.player-point').val(0);

    getCheese();
  });

  function getCheese() {
    var playerPoints = [];

    $('.player-points').each(function(){
      var name = $(this).find('.player-name').val();
      var point = $(this).find('.player-point').val();

      if( name && point ) {
        playerPoints.push({
          'name': name,
          'point': point,
        });
      }
    });

    if( playerPoints.length == 8 ) {
      var points = playerPoints.map(function(player){ return player.point; });
      var names = playerPoints.map(function(player){ return player.name; });

      $.post('/api/pvp/closest_glory', {

        points: points.join(','),
        names: names.join(','),
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

      }, function(data){

        var k = 1;

        for(var i=0; i<data.team1.length; i++) {
          for(var j=0; j<playerPoints.length; j++) {
            if( data.team1[i] == playerPoints[j].point ) {
              $('.result-player'+k+'-name').html( playerPoints[j].name );
              $('.result-player'+k+'-point').html( playerPoints[j].point );

              playerPoints[j].point = '';
              k++;
              break;
            }
          }
        }

        var k = 5;

        for(var i=0; i<data.team2.length; i++) {
          for(var j=0; j<playerPoints.length; j++) {
            if( data.team2[i] == playerPoints[j].point ) {
              $('.result-player'+k+'-name').html( playerPoints[j].name );
              $('.result-player'+k+'-point').html( playerPoints[j].point );

              playerPoints[j].point = '';
              k++;
              break;
            }
          }
        }

        $(".result-team1-total").html(data.team1_total);
        $(".result-team2-total").html(data.team2_total);
        $(".result-diff").html(data.diff);
      })
      .fail(function() {
        $("#network-status").show();
        $("#result-form").hide();
      });

      $("#network-status").hide();
      $("#result-form").fadeIn();
      $("#status").hide();
    }
    else {
      $("#result-form").hide();
      $("#status").show();
    }
  }
});