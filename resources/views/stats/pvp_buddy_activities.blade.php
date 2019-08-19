@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0 py-0 mb-3" vocab="https://schema.org/" typeof="BreadcrumbList">
        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/stats">
            <span property="name">Stats</span>
          </a>
          <meta property="position" content="1">
        </li>

        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/stats/pvp/buddy">
            <span property="name">PvP (Crucible) Buddies</span>
          </a>
          <meta property="position" content="2">
        </li>

        <li class="breadcrumb-item" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/stats/pvp/buddy/{{$member->id}}">
            <span property="name">{{ $member->display_name }}</span>
          </a>
          <meta property="position" content="3">
        </li>

        <li class="breadcrumb-item active" property="itemListElement" typeof="ListItem">
          <a property="item" typeof="WebPage" href="/stats/pvp/buddy/{{$member->id}}/{{$buddy_id}}">
            <span property="name" class="buddy_name">{{ $buddy_id }}</span>
          </a>
          <meta property="position" content="3">
        </li>
      </ol>
    </nav>

    <h1 class="text-yellow text-left">PvP (Crucible) Activities between {{ $member->display_name }} & <span class="buddy_name"></span></h1>
  </div>

  <div class="mt-3 text-left mt-4">
    <div><small>Data updated daily. Data includes any crucible activity regardless of completion status.</small></div>
  </div>

  <div class="stats-container mt-3"></div>
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
  var activity_instances = {!! $activity_instances->toJson() !!};
  var clan_members = {!! $clan_members->toJson() !!};
  var activity_definition = {!! $activity_definition->toJson() !!};
  var buddy_id = '{{ $buddy_id }}';

  getBuddyName();

  // console.log( member );
  // console.log( buddy_id );
  // console.log( activity_definition );

  /**********************
  // Table
  ***********************/

  var tableData = [];

  for(var i=0; i<activity_instances.length; i++) {

    var pgcr = JSON.parse( activity_instances[i].pgcr.pgcr );

    if( pgcr ) {

      console.log(pgcr);

      // pgcr data
      var your_stats = {
        'kill': 0,
        'death': 0,
        'kd': 0
      };

      var buddy_stats = {
        'kill': 0,
        'death': 0,
        'kd': 0
      };

      // your kda
      var memberData = pgcr.entries.filter(function(p){
        return p.player.destinyUserInfo.membershipId == member.id;
      });

      // Ignore multiple characters
      if( memberData.length > 0 ) {
        your_stats['kill'] = memberData[0].values.kills.basic.value;
        your_stats['death'] = memberData[0].values.deaths.basic.value;
        your_stats['kda'] = memberData[0].values.efficiency.basic.value;
      }

      // your buddy's kda
      var buddyData = pgcr.entries.filter(function(p){
        return p.player.destinyUserInfo.membershipId == buddy_id;
      });

      // Ignore multiple characters
      if( buddyData.length > 0 ) {
        buddy_stats['kill'] = buddyData[0].values.kills.basic.value;
        buddy_stats['death'] = buddyData[0].values.deaths.basic.value;
        buddy_stats['kda'] = buddyData[0].values.efficiency.basic.value;
      }

      tableData.push({
        id: activity_instances.activity_id,
        activity_name: activity_definition[ pgcr.activityDetails.directorActivityHash ].displayProperties.name,
        date: moment(pgcr.period).format('D MMM Y, h:mm A'),
        your_kda: Number(your_stats['kda'].toFixed(2)),
        buddy_kda: Number(buddy_stats['kda'].toFixed(2)),
        link: '<a class="text-dark" target="_blank" href="https://guardian.gg/2/pgcr/' + activity_instances[i].activity_id + '">Go</a>'
      });
    }
  }

  $('.stats-container').append('<div id="raid-stats-table"></div>');

  var format = {precision: 0};

  var table = new Tabulator("#raid-stats-table", {
    data:tableData, //assign data to table
    columns:[ //Define Table Columns
      {formatter:"rownum", width:40, headerSort:false},
      {title:"Activity ID", field:"id", visible: false, cssClass: 'activity_id'},
      {title:"Activity", field:"activity_name", cssClass: 'activity_name'},
      {title:"Date", field:"date", cssClass: 'text-center', sorter:"date", headerSort:false, sorterParams:{
        format:"D MMM Y, h:mm A"
      }},
      {title: member.display_name + " KDA", field:"your_kda", cssClass: 'text-center', headerSort:false},
      {title:"<span class='table_header_buddy_name'>"+buddy_id+"</span> KDA", field:"buddy_kda", cssClass: 'text-center', headerSort:false},
      {title:"Guardian.GG", field:"link", formatter:"html", cssClass: 'text-center', headerSort:false},
    ],
    initialSort: [
      {column:"date", dir:"desc"}
    ],
    layout:"fitDataFill",
    resizableColumns:false
  });

  function getBuddyName() {
    $.ajax({
      url: ccbNS.bungie_api_url+'/Destiny2/'+ccbNS.platform_id+'/Profile/'+buddy_id+'/?components=100',
      headers: {
        'X-API-Key': ccbNS.bungie_api
      }
    }).done(function(data){
      $('.buddy_name').text( data.Response.profile.data.userInfo.displayName + ' ❤️');
      $('.table_header_buddy_name').text( data.Response.profile.data.userInfo.displayName );
      $('nav .buddy_name').text( data.Response.profile.data.userInfo.displayName );
    });
  }

});
</script>
@endsection