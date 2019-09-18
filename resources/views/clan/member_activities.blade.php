@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0 py-0 mb-3" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/clan">
            <span property="name">Clan</span>
          </a>
          <meta property="position" content="1">
        </li>

        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/clan/roster">
            <span property="name">Roster</span>
          </a>
          <meta property="position" content="2">
        </li>

        <li class="breadcrumb-item active" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="{{ $link }}">
            <span property="name">{{$activity_type}} Activities</span>
          </a>
          <meta property="position" content="3">
        </li>
      </ol>
    </nav>

    <h1 class="text-yellow text-left">{{ $activity_type }} Activities for {{ $member->display_name }}</h1>
  </div>

  <div class="loader"></div>
  <div class="loader-text">Fetching Data...</div>

  <div class="stats-container mt-3"></div>

  <div style="display: none;" class="pagination-container text-center mt-4">
    @if( !$activity_instances->onFirstPage() )
      <a href="{{ $activity_instances->previousPageUrl() }}" class="mr-2"> <i class="fas fa-angle-double-left"></i> Prev Page</a>
    @endif
    @if( $activity_instances->hasMorePages()  )
      <a href="{{ $activity_instances->nextPageUrl() }}" class="ml-2">Next Page <i class="fas fa-angle-double-right"></i></a>
    @endif
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
  var member = {!! $member->toJson() !!};
  var activity_type = '{{ $activity_type }}';
  var item_start_no = {{ $activity_instances->firstItem() ?? 0 }};
  var activity_instances = {!! $activity_instances->toJson() !!};
  var clan_members_ids = {!! $clan_members->pluck('id')->toJson() !!};
  var activity_definition = {!! $activity_definition->toJson() !!};

  console.log(activity_instances);

  if( activity_instances.data.length > 0 ) {

    /**********************
    // Table
    ***********************/

    var tableData = [];

    for(var i=0; i<activity_instances.data.length; i++) {

      var pgcr = JSON.parse( activity_instances.data[i].pgcr.pgcr );

      if( pgcr ) {

        // console.log(pgcr);

        // pgcr data
        var your_stats = {
          'completed': 0
        };

        // your kda
        var member_data = pgcr.entries.filter(function(p){
          return p.player.destinyUserInfo.membershipId == member.id;
        });

        // Ignore multiple characters
        if( member_data.length > 0 ) {
          your_stats['completed'] = member_data[0].values.completed.basic.value;
        }

        // Has clan mate check
        var activity_member_ids = pgcr.entries.filter(function(p){
          return p.player.destinyUserInfo.membershipId != member.id;
        }).map(function(p){
          return p.player.destinyUserInfo.membershipId;
        });

        var has_clan_mate = false;

        for(var j=0; j<activity_member_ids.length; j++) {
          if( clan_members_ids.includes(activity_member_ids[j]) ) {
            has_clan_mate = true;
            break;
          }
        }

        // Link
        if( activity_type == 'Gambit' ) {
          var link = '<a class="text-dark" target="_blank" href="https://destinytracker.com/d2/pgcr/' + activity_instances.data[i].activity_id + '">Go</a>';

          var link_name = 'D2 Tracker';
        }
        else if( activity_type == 'PvP' ) {
          var link = '<a class="text-dark" target="_blank" href="https://guardian.gg/2/pgcr/' + activity_instances.data[i].activity_id + '">Go</a>';

          var link_name = 'Guardian.GG';
        }
        else if( activity_type == 'Raid' ) {
          var link = '<a class="text-dark" target="_blank" href="https://raid.report/pgcr/' + activity_instances.data[i].activity_id + '">Go</a>';

          var link_name = 'Raid.Report';
        }
        else {
          var link = '<a class="text-dark" target="_blank" href="https://destinytracker.com/d2/pgcr/' + activity_instances.data[i].activity_id + '">Go</a>';

          var link_name = 'D2 Tracker';
        }

        tableData.push({
          no: item_start_no + i,
          id: activity_instances.data[i].activity_id,
          activity_name: activity_definition[ pgcr.activityDetails.directorActivityHash ].displayProperties.name,
          date: moment(pgcr.period).format('D MMM Y, h:mm A'),
          completed: your_stats['completed'] == 1 ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>',
          has_clan_mate: has_clan_mate == true ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>',
          link: link
        });
      }
    }

    $('.loader').hide();
    $('.loader-text').hide();
    $('.stats-container').append('<div id="raid-stats-table"></div>');
    $('.pagination-container').show();

    var format = {precision: 0};

    var table = new Tabulator("#raid-stats-table", {
      data:tableData, //assign data to table
      columns:[ //Define Table Columns
        {title:"No.", field:"no", headerSort:false},
        {title:"Activity ID", field:"id", visible: false, cssClass: 'activity_id'},
        {title:"Activity", field:"activity_name", cssClass: 'activity_name', headerSort:false},
        {title:"<div class='text-center'>Date</div>", field:"date", cssClass: 'text-right', sorter:"date", headerSort:false, sorterParams:{
          format:"D MMM Y, hh:mm A"
        }},
        {title: "Completed", field:"completed", formatter:"html", cssClass: 'text-center', headerSort:false},
        {title: "With Clan Mate", field:"has_clan_mate", formatter:"html", cssClass: 'text-center', headerSort:false},
        {title: link_name, field:"link", formatter:"html", cssClass: 'text-center', headerSort:false},
      ],
      initialSort: [
        {column:"date", dir:"desc"}
      ],
      layout:"fitDataFill",
      resizableColumns:false
    });

  }
  else {
    $('.loader').hide();
    $('.loader-text').hide();
    $('.stats-container').append('<div class="py-5 text-left">No '+activity_type.toLowerCase()+' activities data found :(</div>');
  }

});
</script>
@endsection