@extends('layouts.template')

@section('body')
<section id="glory-cheese" class="text-center container-fluid mt-4 mb-4">
  <h3 class="text-yellow mb-5">Glory Team Balancer</h3>

  <div class="mt-3">
    <div class="d-flex justify-content-between row">
      <div class="col-md-5">
        <h6 class="text-left text-yellow">
          1. Enter Players' Name & Current Glory Points
        </h6>

        <form id="form1" class="mt-3">
          @for ($i = 0; $i < 8; $i++)
          <div class="row mb-2 player-points">
            <div class="col-md-6 mb-md-0 mb-2">
              <input type="text" class="form-control form-control-sm player-name typeahead" name="player{{$i+1}}-name" value="{{ $glory_names[$i] ?? 'Player ' . ($i+1) }}" autocomplete="off">
            </div>
            <div class="col-md-6">
              <input type="number" min="0" class="form-control form-control-sm player-point" name="player{{$i+1}}-point" value="{{ $glory_points[$i] ?? 0 }}">
            </div>
          </div>
          @endfor

          <div class="row mt-3">
            <div class="col-md-12 text-left">
              <button type="button" class="btn btn-danger btn-sm" id="reset"><i class="fas fa-sync-alt"></i> Reset</button>
            </div>
            <div class="col-md-12 mt-2 text-left">
              <button type="button" class="btn btn-danger btn-sm" id="fetchGlory"><i class="fas fa-retweet"></i> Fetch Members' Glory From Bungie API</button>
              <div class="fetch-status">
                <small></small>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="col-md-6 text-white text-left">
        <h6 class="text-left text-yellow">
          2. Setup Fireteams Accordingly
        </h6>

        <div id="status" class="mt-3" style="display: none;">
          Oi, bodoh! Fill in all the fields in step 1 first!
        </div>

        <div id="network-status" class="mt-3" style="display: none;">
          Error. Too many requests.
        </div>

        <form id="result-form" class="mt-3" style="display: none;">
          @for ($i = 0; $i < 8; $i++)

          @if($i==0)
          <div class="row">
            <div class="col-md-12"><u>Fireteam 1</u></div>
          </div>
          @endif

          @if($i==4)
          <div class="row">
            <div class="col-md-12 mt-3"><u>Fireteam 2</u></div>
          </div>
          @endif

          <div class="row">
            <div class="col-md-6 result-player{{$i+1}}-name"></div>
            <div class="col-md-6 result-player{{$i+1}}-point"></div>
          </div>

          @endfor

          <div class="row mt-3">
            <div class="col-md-6">Fireteam 1 Total</div>
            <div class="col-md-6 result-team1-total">0</div>
          </div>

          <div class="row">
            <div class="col-md-6">Fireteam 2 Total</div>
            <div class="col-md-6 result-team2-total">0</div>
          </div>

          <div class="row mt-3">
            <div class="col-md-6">Glory Difference</div>
            <div class="col-md-6 result-diff">0</div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="text-left mt-5">
    <div>Resources</div>
    <ul>
      <li>
        <a href="https://www.reddit.com/r/destiny2/comments/a1euwi/comp_exploit_everyone_should_know_this/" target="_blank">Queueing Guide</a>
      </li>
      <li>
        <a href="https://www.wireshark.org/#download" target="_blank">Wireshark</a>
      </li>
    </ul>
  </div>
</section>
@endsection

@section('footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script>
<script type="text/javascript">
ccbNS.clanMembers = {!! $members !!};
ccbNS.hideActivities = true;
</script>
<script src="{{ mix('/js/compiled/glory_cheese.js') }}"></script>
@endsection