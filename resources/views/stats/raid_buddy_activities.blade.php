@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent pl-0 py-0 mb-3" itemscope itemtype="https://schema.org/BreadcrumbList">>
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats">
            <span itemprop="name">Stats</span>
          </a>
          <meta itemprop="position" content="1">
        </li>

        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/raid/buddy">
            <span itemprop="name">Raid Buddies</span>
          </a>
          <meta itemprop="position" content="2">
        </li>

        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/raid/buddy/{{$member->id}}">
            <span itemprop="name">{{ $member->display_name }}</span>
          </a>
          <meta itemprop="position" content="3">
        </li>

        <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
          <a itemprop="item" itemtype="https://schema.org/WebPage" href="/stats/raid/buddy/{{$member->id}}/{{$buddy_id}}">
            <span itemprop="name" class="buddy_name">{{ $buddy_id }}</span>
          </a>
          <meta itemprop="position" content="3">
        </li>
      </ol>
    </nav>

    <h1 class="text-yellow text-left">Raid Activities between {{ $member->display_name }} & <span class="buddy_name"></span></h1>
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
  var member = @json($member);
  var activity_instances = @json($activity_instances);
  var clan_members = @json($clan_members);
  var activity_definition = @json($activity_definition);
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

      // console.log(pgcr);

      // pgcr data
      var your_stats = {
        'kill': 0,
        'death': 0,
        'kd': 0,
        'completed': 0
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
        your_stats['kd'] = memberData[0].values.killsDeathsRatio.basic.value;
        your_stats['completed'] = memberData[0].values.completed.basic.value;
      }

      // your buddy's kda
      var buddyData = pgcr.entries.filter(function(p){
        return p.player.destinyUserInfo.membershipId == buddy_id;
      });

      // Ignore multiple characters
      if( buddyData.length > 0 ) {
        buddy_stats['kill'] = buddyData[0].values.kills.basic.value;
        buddy_stats['death'] = buddyData[0].values.deaths.basic.value;
        buddy_stats['kd'] = buddyData[0].values.killsDeathsRatio.basic.value;
      }

      tableData.push({
        id: activity_instances[i].activity_id,
        activity_name: activity_definition[ pgcr.activityDetails.directorActivityHash ].displayProperties.name,
        date: moment(pgcr.period).format('D MMM Y, h:mm A'),
        completed: your_stats['completed'] == 1 ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>',
        your_kd: Number(your_stats['kd'].toFixed(2)),
        buddy_kd: Number(buddy_stats['kd'].toFixed(2)),
        link: '<a class="text-dark" target="_blank" href="https://raid.report/pgcr/' + activity_instances[i].activity_id + '">Go</a>'
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
      {title:"<div class='text-center'>Date</div>", field:"date", cssClass: 'text-right', sorter:"date", headerSort:false, sorterParams:{
        format:"D MMM Y, hh:mm A"
      }},
      {title: "Completed", field:"completed", formatter:"html", cssClass: 'text-center', headerSort:false},
      {title: "<div class='mx-3'>KD</div><small>" + member.display_name + "</small>", field:"your_kd", cssClass: 'text-center', headerSort:false},
      {title:"<div class='mx-3'>KD</div><small><span class='table_header_buddy_name'>"+buddy_id+"</span></small>", field:"buddy_kd", cssClass: 'text-center', headerSort:false},
      {title:"Raid.Report", field:"link", formatter:"html", cssClass: 'text-center', headerSort:false},
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