@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">
    @include('stats.breadcrumbs', ['nav_link' => '/stats/raid/buddy', 'nav_name' => 'Raid Buddies'])
    <h1 class="text-yellow text-left">Raid Buddies of {{ $member->display_name }} <i class="fas fa-user-friends"></i></h1>
  </div>

  <div class="mt-3 p-0 col-md-12 col-xs-12 text-center">
    <canvas id="buddiesChart" style="background: rgba(255,255,255,.05);"></canvas>
  </div>

  <div class="stats-container mt-1"></div>

  <div class="mt-3 text-left">
    <div><small>* Not clan mate</small></div>
    <div><small>** Data updated weekly</small></div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
@endsection

@section('footer')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" integrity="sha256-aa0xaJgmK/X74WM224KMQeNQC2xYKwlAt08oZqjeF0E=" crossorigin="anonymous" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>

<script type="text/javascript">
$(document).ready(function(){
  var member = JSON.parse('{!! $member->toJson() !!}');
  var clan_members = JSON.parse('{!! $clan_members->toJson() !!}');

  var chartLimit = 10;
  var id2query = [];
  var buddiesValues = [];
  var buddiesIDs = [];

  for(var i=0; i<member.raid_buddies.length; i++) {

    buddiesValues.push(member.raid_buddies[i].activity_count);

    var match = clan_members.filter(function(m){
      return m.id == member.raid_buddies[i].buddy_id;
    });

    if( match.length > 0 ) {
      buddiesIDs.push(match[0].display_name);
    }
    else {
      id2query.push(member.raid_buddies[i].buddy_id);
      buddiesIDs.push(member.raid_buddies[i].buddy_id);
    }

    if(i==(chartLimit-1))
      break;
  }

  // console.log( top10BuddiesIDs );
  // console.log( top10BuddiesValues );

  var colorOptions = ['#4285F4', '#DB4437', '#F4B400', '#0F9D58'];
  var colors = [];
  var j = 0;

  for(var i=0; i<buddiesIDs.length; i++) {
    if(j>=colorOptions.length)
      j = 0;

    colors.push( colorOptions[j] );
    j++;
  }

  var buddiesChartCanvas = document.getElementById('buddiesChart').getContext('2d');
  var buddiesChart = new Chart(buddiesChartCanvas, {
    type: 'horizontalBar',
    data: {
      datasets: [{
        data: buddiesValues,
        backgroundColor: colors,
      }],
      labels: buddiesIDs
    },
    options: {
      title: {
        display: true,
        text: 'Top '+chartLimit+' Raid Buddies',
        fontColor: "#ffffff"
      },
      legend: {
        display: false
      },
      layout: {
        padding: {
          left: 10,
          right: 80,
          top: 10,
          bottom: 10
        }
      },
      scales: {
        yAxes: [{
          ticks: {
            fontSize: 12,
            fontColor: "#ffffff"
          },
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'Guardian',
            padding: 15,
            fontColor: "#ffffff"
          }
        }],
        xAxes: [{
          ticks: {
            fontSize: 12,
            fontColor: "#ffffff",
            precision: 0
          },
          display: true,
          scaleLabel: {
            display: true,
            labelString: 'No. of Raids Played Together',
            padding: 15,
            fontColor: "#ffffff"
          }
        }],
      }
    }
  });

  // Update unknown IDs in Chart
  if( id2query.length > 0 ) {

    console.log( id2query );

    for(var i=0; i<id2query.length; i++) {
      queryUpdateIDs(id2query[i]);
    }
  }

  function queryUpdateIDs(id) {
    $.ajax({
      url: ccbNS.bungie_api_url+'/Destiny2/'+ccbNS.platform_id+'/Profile/'+id+'/?components=100',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    }).done(function(data){
      for(var i=0; i<buddiesIDs.length; i++) {
        if(buddiesIDs[i] == id) {
          buddiesIDs[i] = data.Response.profile.data.userInfo.displayName;
        }
      }

      buddiesChart.data.labels = buddiesIDs;
      buddiesChart.update();
    });
  }

  /**********************
  // Table
  ***********************/

  var tableLimit = 50;
  var tableData = [];

  var tableIDs2query = [];
  var tableBuddiesValues = [];
  var tableBuddiesIDs = [];

  for(var i=0; i<member.raid_buddies.length; i++) {

    var match = clan_members.filter(function(m){
      return m.id == member.raid_buddies[i].buddy_id;
    });

    var buddy_name = '';

    if( match.length > 0 ) {
      buddy_name = match[0].display_name;
    }
    else {
      buddy_name = member.raid_buddies[i].buddy_id;
      tableIDs2query.push(member.raid_buddies[i].buddy_id);
    }

    tableData.push({
      id: member.raid_buddies[i].buddy_id,
      buddy_name: buddy_name,
      buddy_id: member.raid_buddies[i].buddy_id,
      activity_count: member.raid_buddies[i].activity_count
    });

    if(i==(tableLimit-1))
      break;
  }

  $('.stats-container').append('<div id="raid-stats-table"></div>');

  var format = {precision: 0};

  var table = new Tabulator("#raid-stats-table", {
    data:tableData, //assign data to table
    layout:"fitColumns", //fit columns to width of table (optional)
    columns:[ //Define Table Columns
      {formatter:"rownum", width:40, headerSort:false},
      {title:"Member ID", field:"id", visible: false, cssClass: 'member_id'},
      {title:"Buddy ID", field:"buddy_id", visible: false, cssClass: 'buddy_id'},
      {title:"Buddy", field:"buddy_name", cssClass: 'buddy_name', headerSort:false},
      {title:"No. of Raids Together", field:"activity_count", cssClass: 'activity_count text-center', headerSort:false},
    ],
    initialSort: [
      {column:"activity_count", dir:"desc"}
    ],
    layout:"fitColumns",
    resizableColumns:false,
    tableBuilt:function() {
      if( tableIDs2query.length > 0 ) {
        for(var i=0; i<tableIDs2query.length; i++) {
          queryUpdateTableIDs(tableIDs2query[i]);
        }
      }
    }
  });

  // Add ❤️
  table.updateData([{
    id: tableData[0].id,
    buddy_name: tableData[0].buddy_name + " ❤️",
  }]);

  function queryUpdateTableIDs(id) {
    $.ajax({
      url: ccbNS.bungie_api_url+'/Destiny2/'+ccbNS.platform_id+'/Profile/'+id+'/?components=100',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    }).done(function(data){

      var matches = tableData.filter(function(m){
        return m.buddy_id == id;
      });

      for(var i=0; i<matches.length; i++) {
        table.updateData([{
          id: matches[i].id,
          buddy_name: data.Response.profile.data.userInfo.displayName + "*",
        }]);
      }
    });
  }
});
</script>
@endsection