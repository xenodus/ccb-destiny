@extends('layouts.template')

@section('footer')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />
<script type="text/javascript">
ccbNS.hideActivities = true;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function(){
  $('#event_datetime').datetimepicker({
    format: 'D MMM Y h:mm A',
  });
});
</script>
<style>
.picker-switch a {
  color: #000;
}
.fa-clock-o:before {
  content: "\f017";
}
</style>
@endsection

@section('body')
<section id="event-crud-section">
  <div class="container">
    <div class="d-flex align-items-center justify-content-center">
      <div class="w-100 my-5" style="max-width: 500px;">

        @if(session('status'))
        <div class="text-center">
          {{ session('status') }}
        </div>

        @else

          @if( $expired == false )

          <div>
            <h2>{{ isset($raid_event) ? 'Edit' : 'Create' }} Event {{ $raid_event->event_id ?? '' }}</h2>
          </div>

          <hr class="border-white"/>

          <form action="/alfred/{{$token}}" method="POST">
            @csrf

            <div class="form-group">
              <label for="event_name">Event Name</label>
              <input type="text" class="form-control" id="event_name" name="event_name" value="{{ isset($raid_event) ? $raid_event->getEventName() : '' }}" required>
              <small id="event_name_help" class="form-text text-muted">Ensure event name is clearly defined. E.g. Levi / EoW / SoS / CoS / Iron Banner</small>
              @if( $errors->has('event_name') )
                <div class="text-danger mt-1">Event name is required.</div>
              @endif
            </div>
            <div class="form-group">
              <label for="event_description">Event Description</label>
              <input type="text" class="form-control" id="event_description" name="event_description" value="{{ $raid_event->event_description ?? '' }}">
              <small id="event_description_help" class="form-text text-muted">Optional. E.g. Prestige teaching raid. Newbies welcome.</small>
              <div class="d-none event_description_error-msg error-msg text-danger mt-1"></div>
            </div>

            <div class="form-group">
              <label for="event_description">Event Date & Time</label>
              <div class="input-group date" id="event_datetime" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" name="event_datetime" data-target="#event_datetime" value="{{ isset($raid_event) ? Carbon\Carbon::parse( $raid_event->event_date )->format('j M Y g:i A') : '' }}"/>
                <div class="input-group-append" data-target="#event_datetime" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
              </div>

              @if( $errors->has('event_datetime') )
                <div class="text-danger mt-1">Event date time is required.</div>
              @endif
            </div>

            <button type="submit" class="btn btn-warning w-100 rounded-0 mb-2">Submit</button>
          </form>

          @else
          <div class="text-center">
            Link is expired
          </div>
          @endif

        @endif
      </div>
    </div>
  </div>
</section>
@endsection