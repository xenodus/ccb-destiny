@extends('layouts.template')

@section('body')
<section class="stats text-center container-fluid mb-4">

  <div class="mt-4">
    <h1 class="text-yellow text-left">MHW: Weakness Table</h1>
  </div>

  <div class="loader"></div>
  <div class="loader-text">Loading...</div>

  <div class="filter-container">
    <div class="form-group text-left mt-4">
      <div>
        <label for="nameFilter" class="d-md-inline-block">Filter by Name</label>
        <input type="text" class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1 typeahead" id="nameFilter" style="max-width: 360px;" autocomplete="off">
      </div>

      <div>
        <label for="nameFilter" class="d-md-inline-block">Filter by Type</label>
        <select class="form-control form-control-sm d-md-inline-block ml-0 ml-md-1" id="typeFilter" style="max-width: 360px;">
          <option value=""></option>
          @foreach($monster_types as $type)
          <option value="{{ $type }}">{{ $type }}</option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="stats-container mt-1"></div>
</section>
@endsection

@section('header')
<link rel="stylesheet" href="{{ mix('/css/compiled/stats.css') }}"/>
<style type="text/css">
.tabulator .tabulator-header .tabulator-col.weak-points,
.tabulator .tabulator-header .tabulator-col.hunter-log {
  min-width: 115px!important;
}
.tabulator .tabulator-header .tabulator-col.weakness {
  min-width: 90px!important;
}
.filter-container {
  display: none;
}
.filter-container label {
  width: 110px;
}
.weakness_img {
  height: 80px;
  width: auto;
}
.col-wrapper-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  height: 100%;
}
.col-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  min-height: 70px;
}
.col-wrapper a,
.col-wrapper a:hover,
.col-wrapper a:focus,
.col-wrapper a:active {
  min-width: 100px;
  color: #000;
  cursor: pointer;
}
</style>
@endsection

@section('footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<script>
$(document).ready(function(){
  var tableData = [];
  var monsterData = @json($monsters);
  var monsterNames = monsterData.map(m => m.name);

  $(".typeahead").typeahead({
    source: monsterNames,
    minLength: 2
  });

  for(var i=0; i<monsterData.length; i++) {

    tableData.push({
      id: monsterData[i].id,
      name: '<div class="col-wrapper-left pr-3 pl-2">' + monsterData[i].name + '<br/><small class="text-primary">'+monsterData[i].type+'</small></div>',
      type: monsterData[i].type,
      fire: getStars( monsterData[i].fire ),
      water: getStars( monsterData[i].water ),
      thunder: getStars( monsterData[i].thunder ),
      ice: getStars( monsterData[i].ice ),
      dragon: getStars( monsterData[i].dragon ),
      poison: getStars( monsterData[i].poison ),
      sleep: getStars( monsterData[i].sleep ),
      paralysis: getStars( monsterData[i].paralysis ),
      blast: getStars( monsterData[i].blast ),
      stun: getStars( monsterData[i].stun ),
      weak_points: '<div class="col-wrapper-left pl-2">' + monsterData[i].weak_points.map(w => w.weak_point).join(',<br/>') + '</div>',
      weak_points_img: getLightbox( monsterData[i] ),
      wiki_link: '<div class="col-wrapper"><a data-sort-name="'+monsterData[i].name+'" href="'+monsterData[i].wiki_link+'" target="_blank">Go</a></div>'
    });
  }

  $('.loader-text').text('Generating Table...');
  $('.stats-container').append('<div id="monster-table"></div>');
  $('.loader').hide();
  $('.loader-text').hide();
  $('.filter-container').show();

  var table = new Tabulator("#monster-table", {
    data:tableData,
    layout:"fitColumns",
    columns:[
      {title:"ID", field:"id", visible: false},
      {title:"Type", field:"type", visible: false},
      {title:"Name", field:"name", cssClass: 'px-1', formatter:"html"},
      {title:"Weak Points", field:"weak_points", formatter:"html", cssClass: 'px-1 weak-points', headerSort:false},

      {title:"Fire", field:"fire", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Water", field:"water", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Thunder", field:"thunder", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Ice", field:"ice", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Dragon", field:"dragon", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},

      {title:"Poison", field:"poison", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Sleep", field:"sleep", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Paralysis", field:"paralysis", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Blast", field:"blast", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},
      {title:"Stun", field:"stun", formatter:"html", cssClass: 'text-center px-1 weakness', headerSort:false},

      {title:"Hunter's Log", field:"weak_points_img", formatter:"html", cssClass: 'px-1 hunter-log', headerSort:false},

      {title:"Wiki", field:"wiki_link", formatter:"html", cssClass: 'text-center', headerSort:false},
    ],
    initialSort: [
      {column:"id", dir:"asc"},
    ],
    layout:"fitDataFill",
    height:"600px",
    resizableRows:false,
    resizableColumns:false,
  });

  $("#nameFilter, #typeFilter").on("input change keyup", function(){

    var typeFilter = $("#typeFilter").val();
    var nameFilter = $("#nameFilter").val()
    var filters = [];

    if( typeFilter == '' ) {
      filters.push( {field: "type", type: "like", value: typeFilter} );
    }
    else {
      filters.push( {field: "type", type: "=", value: typeFilter} );
    }

    filters.push( {field: "name", type: "like", value: nameFilter} );

    if( filters.length > 0 )
      table.setFilter( filters );
  });
});

function getLightbox(m) {
  return `
  <a href="`+m.weakness_img_link+`" data-lightbox="`+m.name+`'s Weakness">
    <img src="`+m.weakness_img_link+`" class="img-fluid border border-white weakness_img">
  </a>
  `;
}

function getStars(n) {

  if(n==0)
    return '';

  if( n < 0 )
    return '<div class="col-wrapper"><i class="fas fa-times text-danger fa-lg"></i></div>';

  var stars = '<div class="col-wrapper">';
  var style_color = 'text-secondary';

  if( n == 2 )
    style_color = 'text-info';
  else if( n == 3 )
    style_color = 'text-success';

  for(var i=0; i<n; i++) {
    stars += '<i class="fas fa-star fa-sm px-1 '+style_color+'"></i>';
  }

  stars += '</div>';

  return stars;
}
</script>
@endsection