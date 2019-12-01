@extends('layouts.template')

@section('body')
<section id="roster" class="container-fluid text-center mb-4">
  <div class="my-4">
    @include('clan.breadcrumbs', ['nav_link' => '/clan/roster', 'nav_name' => 'Roster'])
    <h1 class="text-yellow text-left">Members' Activity History</h1>
  </div>
  <div class="loader"></div>
  <div class="loader-text mb-4">Fetching Members...</div>

  <div class="form-group filter-container text-left mt-4" style="display: none;">
    <label for="nameFilter" class="d-md-inline-block">Filter by name</label>
    <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="nameFilter" style="max-width: 360px;">
  </div>

  <div>
    <div class="row">
      <div class="col-md-12">
        <div id="roster-container"></div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/clan.css') }}"/>
@endsection

@section('footer')
<script type="text/javascript">
$(document).ready(function(){
  $.get('/bungie/roster/get', function(members){

    if( members.length > 0 ) {

      var tableData = [];

      for(var i=0; i<members.length; i++) {
        tableData.push({
          name: members[i].display_name,
          raid_activities: '<a href="/clan/activities/raid/'+members[i].id +'">View</a>',
          pvp_activities: '<a href="/clan/activities/pvp/'+members[i].id +'">View</a>',
          gambit_activities: '<a href="/clan/activities/gambit/'+members[i].id +'">View</a>',
        });
      }

      $('.loader').hide();
      $('.loader-text').hide();
      $('.filter-container').show();

      var table = new Tabulator("#roster-container", {
        data:tableData, //assign data to table
        layout:"fitColumns", //fit columns to width of table (optional)
        columns:[ //Define Table Columns
          {formatter:"rownum", width:40, headerSort:false},
          {title:"Name", field:"name", headerSort:false},
          {title:"<div class='mx-3'>Raid</div>", field:"raid_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>PvP</div>", field:"pvp_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
          {title:"<div class='mx-3'>Gambit</div>", field:"gambit_activities", formatter: "html", cssClass: 'text-center', headerSort:false},
        ],
        initialSort: [
          {column:"name", dir:"asc"},
          {column:"last_online", dir:"desc"}
        ],
        layout:"fitDataFill",
        height:"500px",
        resizableColumns:false,
      });

      $("#nameFilter").on("input", function(){
        table.setFilter("name", "like", $(this).val());
      });
    }
    else {
      $('.loader-text').html('Unable to retrieve members 😟 <br/>Bungie API might be under maintenance');
    }
  });
});
</script>
@endsection