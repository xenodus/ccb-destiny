@extends('layouts.template')

@section('body')
<section id="outbreak-solution" class="text-center container-fluid mt-4 mb-4">
  <div class="p-1">
    <h1 class="text-yellow mb-1">Raid Events</h1>
    <div class="text-left">Create / sign up for raids in our Discord's #raid-noticeboard / #lfg-noticeboard channels.</div>

    <div class="mt-3">
      @if($raid_events->count())
      <div class="table-responsive">
        <table class="table text-white">
          <thead class="text-white border-0" style="background: #000; letter-spacing: 1px;">
            <tr>
              <th class="border-0" scope="col">Event</th>
              <th class="border-0" scope="col">Date</th>
              <th class="border-0" scope="col">Lead</th>
              <th class="border-0" scope="col">Confirmed</th>
              <th class="border-0" scope="col">Reserve</th>
            </tr>
          </thead>
          <tbody style="font-size:.9rem; background: rgba(255,255,255,.05);">
          @foreach($raid_events as $event)
            <tr>
              <td class="w-50 border-dark">
                <div>
                  <div>
                    <a href="https://discordapp.com/channels/{{$event->server_id}}/{{$event->channel_id}}/{{$event->message_id}}" target="_blank">
                      {{ $event->getEventName() }}</div>
                    </a>
                  <div class="mt-1" style="line-height: 1rem;">
                    <small>{{ $event->event_description }}</small>
                  </div>
                </div>
              </td>
              <td class="w-25 border-dark">
                <div>{{ Carbon\Carbon::parse($event->event_date)->format('l') }}</div>
                <div>{{ Carbon\Carbon::parse($event->event_date)->format('j M Y') }}</div>
                <div>{{ Carbon\Carbon::parse($event->event_date)->format('g:i A') }}</div>
              </td>
              <td class="border-dark">
                {{ $event->created_by_username }}
              </td>
              <td class="border-dark">
                @if( $event->signups->where('type', 'confirmed')->count() > 0 )
                <div style="cursor:pointer;" data-toggle="tooltip" data-html="true" title="<div class='p-1'>{{ implode('<br/>', $event->getConfirmed()) }}</div>">
                {{ $event->signups->where('type', 'confirmed')->count() }}
                </div>
                @else
                  0
                @endif
              </td>
              <td class="border-dark">
                @if( $event->signups->where('type', 'reserve')->count() > 0 )
                <div style="cursor:pointer;" data-toggle="tooltip" data-html="true" title="<div class='p-1'>{{ implode('<br/>', $event->getReserve()) }}</div>">
                {{ $event->signups->where('type', 'reserve')->count() }}
                </div>
                @else
                  0
                @endif
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
      @else
        <div class="text-left">No raid events have been scheduled yet. Go create one!</div>
      @endif
    </div>
  </div>
</section>
@endsection

@section('footer')
<script type="text/javascript">
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection