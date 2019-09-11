@extends('layouts.template')

@section('header')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('body')
<section id="application-section">
  <div class="container">
    <div class="standard-header mt-5 mb-5 text-yellow d-flex justify-content-start align-items-center">
      <div id="milestones-header-icon"></div>
      <h1>Apply to join the {{ env('SITE_NAME') }}</h1>
    </div>

    <div class="mb-3 text-yellow">
      <h4>Step 1 of 3: Apply</h4>
    </div>

    <div class="mb-5">
      <p>Apply to the clan at Bungie.net, <a href="{{ env('CLAN_LINK') }}" target="_blank">{{ env('CLAN_LINK') }}</a>.</p>
    </div>

    <div class="mt-5 mb-3 text-yellow">
      <h4>Step 2 of 3: Fill in the application form</h4>
    </div>

    <div style="max-width: 550px;">
      <form action="/" id="join_form" method="POST">
        <div class="form-group">
          <label for="ig_name">What is your in-game name (Full Bnet ID)?</label>
          <input type="text" class="form-control" id="ig_name" name="ig_name" aria-describedby="ig_name_help">
          <div class="d-none ig_name_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="nationality">Where are you from?</label>
          <input type="text" class="form-control" aria-describedby="nationality_help" id="nationality" name="nationality">
          <div class="d-none nationality_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="timezone">What timezone are you in?</label>
          <input type="text" class="form-control" aria-describedby="timezone_help" id="timezone" name="timezone">
          <div class="d-none timezone_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="age">What is your age?</label>
          <input type="text" class="form-control" aria-describedby="age_help" id="age" name="age">
          <div class="d-none age_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="expansion">What is your latest expansion owned?</label>
          <input type="text" class="form-control" id="expansion" name="expansion">
          <div class="d-none expansion_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="activity">What is your level of activity / average hours online in-game per week?</label>
          <input type="text" class="form-control" id="activity" name="activity">
          <div class="d-none activity_error-msg error-msg text-danger mt-1"></div>
        </div>
        <div class="form-group">
          <label for="experience">What is your raid experience & general interest in raiding?</label>
          <textarea class="form-control" id="experience" name="experience" rows="3"></textarea>
          <div class="d-none experience_error-msg error-msg text-danger mt-1"></div>
        </div>

        <div class="captcha mb-3">
          {!! NoCaptcha::display(['data-theme' => 'dark']) !!}
          <div class="d-none g-recaptcha-response_error-msg error-msg text-danger mt-1"></div>
        </div>

        <button type="submit" class="btn btn-warning w-100 rounded-0 mb-2">Submit</button>
      </form>
    </div>

    <div class="form_success d-none">Application submitted.</div>

    <div class="mt-5 mb-3 text-yellow">
      <h4>Step 3 of 3: Join our Discord</h4>
    </div>

    <div class="mb-5">
      <p>Join our Discord <a href="{{env('DISCORD_LINK')}}" target="_blank">here</a>. Message a Mod to expedite your application.
    </div>
  </div>
</section>
@endsection

@section('footer')
<script type="text/javascript">
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function(){
  $("#join_form").on('submit', function(e){
    e.preventDefault();

    $('.error-msg').addClass('d-none');

    $.ajax({
      method: 'POST',
      url: '/apply',
      data: $(this).serialize(),
      dataType: "json"
    })
    .done(function(res){
      $('form').fadeOut(function(){
        $('.form_success').removeClass('d-none');
      });
    })
    .fail(function(jqXHR, textStatus, errorThrown){

      grecaptcha.reset();

      if( jqXHR.responseJSON.errors ) {
        for(var key in jqXHR.responseJSON.errors) {
          $("." + key + "_error-msg").text( jqXHR.responseJSON.errors[key][0] );
          $("." + key + "_error-msg").removeClass('d-none');
        }
      }
    });
  });
});
</script>
<style>
.form-control,
.form-control:focus {
  background-color: rgba(255,255,255,0.05);
  color: #fff;
  border: none;
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #ffca00;
}
.form-control:focus {
  box-shadow: none;
  border-color: #ffca00;
}
form label {
  letter-spacing: 1px;
  margin-left: .1rem;
}
#application-section p {
  letter-spacing: 1px;
}
.error-msg {
  font-size: .8rem;
}
</style>
@endsection