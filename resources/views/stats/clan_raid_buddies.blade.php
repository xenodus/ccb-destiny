@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">
    @include('stats.breadcrumbs', ['nav_link' => '/stats/raid/buddy', 'nav_name' => 'Raid Buddies'])
    <h1 class="text-yellow text-left">Who's Your Raid Buddy? <i class="fas fa-user-friends"></i></h1>
  </div>

  <div class="loader"></div>
  <div class="loader-text">Fetching Members...</div>

  <div class="mt-3 text-left mt-4">
    <div><small>If your name is not listed here, go to your Bungie privacy settings and ensure "Show my Destiny game Activity Feed on Bungie.net" is checked. Come back in 24 hours.</small></div>
    <div><small>Data updated daily. Data includes any crucible activity regardless of completion status.</small></div>
    <div><small>* Not clan mate</small></div>
  </div>

  <div class="form-group filter-container text-left mt-4" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>

  <div class="stats-container mt-1"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
@endsection

@section('footer')
<script>
$(document).ready(function(){
  var tableData = [];
  var memberData = JSON.parse('{!! $members->toJson() !!}');
  var id2query = [];

  for(var i=0; i<memberData.length; i++) {

      if( memberData[i].raid_buddies[0] ) {

        var buddy = memberData.filter(function(m){
          return m.id == memberData[i].raid_buddies[0].buddy_id;
        });

        var buddy_name = '';

        if( buddy.length > 0 ) {
          buddy_name = buddy[0].display_name;
        }
        else {
          id2query.push( memberData[i].raid_buddies[0].buddy_id );
        }

      tableData.push({
        id: memberData[i].id,
        name: memberData[i].display_name,
        buddy_name: buddy_name,
        buddy_id: memberData[i].raid_buddies[0] ? memberData[i].raid_buddies[0].buddy_id : '',
        activity_count: memberData[i].raid_buddies[0] ? memberData[i].raid_buddies[0].activity_count : '',
        link: '<a class="text-dark" href="/stats/raid/buddy/'+memberData[i].id+'">Go</a>'
      });
    }
  }

  $('.loader-text').text('Generating Table...');
  $('.stats-container').append('<div id="raid-stats-table"></div>');
  $('.loader').hide();
  $('.loader-text').hide();
  $('.filter-container').show();

  var format = {precision: 0};

  var table = new Tabulator("#raid-stats-table", {
    data:tableData, //assign data to table
    layout:"fitColumns", //fit columns to width of table (optional)
    columns:[ //Define Table Columns
      {title:"Name", field:"name", formatter:"html", frozen:true, cssClass: 'member_name'},
      {title:"Member ID", field:"id", visible: false, cssClass: 'member_id'},
      {title:"Buddy ID", field:"buddy_id", visible: false, cssClass: 'buddy_id'},
      {title:"No. 1 Buddy", field:"buddy_name", cssClass: 'buddy_name'},
      {title:"No. of Raids Together", field:"activity_count", cssClass: 'activity_count text-center'},
      {title:"More Details", field:"link", formatter:"html", cssClass: 'view_more text-center', headerSort:false},
    ],
    initialSort: [
      {column:"activity_count", dir:"desc"}
    ],
    layout:"fitDataFill",
    height:"500px",
    resizableColumns:false,
    tableBuilt:function() {
      if( id2query.length > 0 ) {
        for(var i=0; i<id2query.length; i++) {
          queryUpdateIDs(id2query[i]);
        }
      }
    }
  });

  $("#nameFilter").on("input", function(){
    table.setFilter("name", "like", $(this).val());
  });

  function queryUpdateIDs(id) {
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