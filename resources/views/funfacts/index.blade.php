@extends('layouts.template')

@section('footer')
<script type="text/javascript">
ccbNS.hideActivities = true;
ccbNS.token = "{{ $token }}";

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function doAlert(message, type='primary') {

  $('#alert-container').empty();

  var alertMarkup = `
  <div class="alert alert-`+type+` alert-dismissible fade show" role="alert">
    %ALERT_MSG%
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>`;

  $('#alert-container').append( alertMarkup.replace('%ALERT_MSG%', message) );
}

$(document).ready(function(){

  // Enable FF
  $(".enable-ff-btn").on('click', function(){
     var ff_id = $(this).data('ffid');
     var fun_fact = $('.ffid_' + ff_id + '_displayed').text();

     var url = '/funfacts/'+ccbNS.token+'/edit';
     var payload = {
      fun_fact: fun_fact,
      ff_id: ff_id,
      ff_status: 'active'
    };

    $.post(url, payload, function(data){
      if( data.status == 0 ) {
        location.reload();
      }
      else
        doAlert(data.message, 'danger');
    })
    .fail(function(data){
      if( data.status === 422 ) {
        console.log(data);
      }
    });
  });

  // Disable FF
  $(".disable-ff-btn").on('click', function(){
     var ff_id = $(this).data('ffid');
     var fun_fact = $('.ffid_' + ff_id + '_displayed').text();

     var url = '/funfacts/'+ccbNS.token+'/edit';
     var payload = {
      fun_fact: fun_fact,
      ff_id: ff_id,
      ff_status: 'disabled'
    };

    $.post(url, payload, function(data){
      if( data.status == 0 ) {
        location.reload();
      }
      else
        doAlert(data.message, 'danger');
    })
    .fail(function(data){
      if( data.status === 422 ) {
        console.log(data);
      }
    });
  });

  // Edit FF
  $(".edit-ff-btn").on('click', function(){
    var ff_id = $(this).data('ffid');

    $("#editFFModal").data('ffid', ff_id);
    $('#edit_fun_fact').val( $('.ffid_' + ff_id + '_displayed').text() );
    $('#editFFModal').modal('show');
  });

  // Edit FF Submit
  $(".edit-fun-fact-btn").on('click', function(){

     var ff_id = $("#editFFModal").data('ffid');
     var edit_fun_fact = $('#edit_fun_fact').val().trim();

     var url = '/funfacts/'+ccbNS.token+'/edit';
     var payload = {
      fun_fact: edit_fun_fact,
      ff_id: ff_id
    };

    $.post(url, payload, function(data){
      $('#editFFModal').modal('hide');
      $('#edit_fun_fact').val('');

      if( data.status == 0 ) {
        $('.ffid_' + ff_id + '_displayed').text( edit_fun_fact );
        doAlert(data.message, 'success');
      }
      else
        doAlert(data.message, 'danger');
    })
    .fail(function(data){
      if( data.status === 422 ) {
        console.log(data);
      }
    });
  });

  // New FF Submit
  $(".new-fun-fact-btn").on('click', function(){
    var new_fun_fact = $('#new_fun_fact').val().trim();

    if( new_fun_fact ) {
      $.post('/funfacts/'+ccbNS.token+'/create', {fun_fact: new_fun_fact}, function(data){
        $('#createFFModal').modal('hide');
        $('#new_fun_fact').val('');

        if( data.status == 0 ) {
          location.reload();
        }
        else
          doAlert(data.message, 'danger')
      })
      .fail(function(data){
        if( data.status === 422 ) {
          console.log(data);
        }
      });
    }
  });
});
</script>

<style>
.non-active {
  opacity: 30%;
}
</style>
@endsection

@section('body')
<section id="event-crud-section">
  <div class="container">
    <div class="d-flex align-items-center justify-content-center">
      <div class="w-100 my-5">

        @if( $expired == false )

          <div id="alert-container"></div>

          <div class="mb-3">Logged in as {{ $ff_token->discord_nickname }} ({{ $ff_token->discord_id }})</div>

          <div class="mb-3">
            <a class="btn btn-success <?=($type=='active'?'active':'non-active')?>" href="{{ route('funfact_web_admin', [$token, 'active']) }}" role="button">Active ({{$active_count}})</a>
            <a class="btn btn-danger <?=($type=='disabled'?'active':'non-active')?>" href="{{ route('funfact_web_admin', [$token, 'disabled']) }}" role="button">Disabled ({{$deleted_count}})</a>

            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createFFModal">
              Create <i class="fas fa-plus fa-sm"></i>
            </button>
          </div>

          <!-- Create Fun Fact Modal -->
          <div class="modal fade" id="createFFModal" tabindex="-1" role="dialog" aria-labelledby="createFFModalLabel" aria-hidden="true">
            <div class="modal-dialog text-dark" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="createFFModalLabel">New CCB Fun Fact</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" class="form-control" id="new_fun_fact" name="new_fun_fact" value="">
                    <small id="new_fun_fact_help" class="form-text text-muted">Your CCB fun fact</small>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary new-fun-fact-btn">Save</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Fun Fact Modal -->
          <div class="modal fade" id="editFFModal" data-ff-if="0" tabindex="-1" role="dialog" aria-labelledby="editFFModalLabel" aria-hidden="true">
            <div class="modal-dialog text-dark" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editFFModalLabel">Edit CCB Fun Fact</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input type="text" class="form-control" id="edit_fun_fact" name="edit_fun_fact" value="">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary edit-fun-fact-btn">Save</button>
                </div>
              </div>
            </div>
          </div>

          @if($fun_facts->count())
          <div class="table-responsive">
            <table class="table text-white">
              <thead class="text-white border-0" style="background: #000; letter-spacing: 1px;">
                <tr>
                  <th class="border-0" scope="col">#</th>
                  <th class="border-0" scope="col">Fun Fact</th>
                  <th class="border-0" scope="col">Added By</th>
                  <th class="border-0" scope="col">Updated By</th>
                  <th class="border-0" scope="col">Last Modified</th>
                  <th class="border-0" scope="col"></th>
                </tr>
              </thead>
              <tbody style="font-size:.9rem; background: rgba(255,255,255,.05);">
              @php
                $start_count = $fun_facts->firstItem();
              @endphp

              @foreach($fun_facts as $fun_fact)
                <tr>
                  <td class="border-dark">{{ $start_count++ }}</td>
                  <td class="border-dark ffid_{{$fun_fact->id}}_displayed">
                    {{ $fun_fact->fact }}
                  </td>
                  <td class="border-dark">{{ $fun_fact->created_by_discord_nickname }}</td>
                  <td class="border-dark">{{ $fun_fact->updated_by_discord_nickname }}</td>
                  <td class="border-dark">{{ Carbon\Carbon::parse($fun_fact->date_added)->format('j M Y g:i A') }}</td>
                  <td class="border-dark">
                    @if($type=='active')
                    <button type="button" class="btn btn-warning btn-sm edit-ff-btn" data-ffid="{{ $fun_fact->id }}">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm ml-lg-1 mt-lg-0 disable-ff-btn" data-ffid="{{ $fun_fact->id }}">Disable</button>
                    @else
                    <button type="button" class="btn btn-success btn-sm ml-1 enable-ff-btn" data-ffid="{{ $fun_fact->id }}">Enable</button>
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>

          <div class="pagination-container text-center mt-2">
            @if( !$fun_facts->onFirstPage() )
              <a href="{{ $fun_facts->previousPageUrl() }}" class="mr-2"> <i class="fas fa-angle-double-left"></i> Prev Page</a>
            @endif
            @if( $fun_facts->hasMorePages()  )
              <a href="{{ $fun_facts->nextPageUrl() }}" class="ml-2">Next Page <i class="fas fa-angle-double-right"></i></a>
            @endif
          </div>
          @else
            @if($type=='active')
              <div class="text-left">No fun facts have been created yet. Go create one!</div>
            @else
              <div class="text-left">No results!</div>
            @endif
          @endif

        @else
        <div class="text-center">
          Invalid token or session has expired. Please request a new session through CCB Bot.
        </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection