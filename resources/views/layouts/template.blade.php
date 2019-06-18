<!DOCTYPE html>
<html>
  <head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-35918300-6"></script>
    <meta name="google-site-verification" content="D9-BceHdaxycglc0RlAFxr_nlEh5GGiNgdK8pT7Y1PY" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ isset($site_title) ? $site_title : env('SITE_NAME') }}</title>

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <meta name="author" content="name">
    <meta name="description" content="{{ $site_description ?? env('SITE_DESCRIPTION') }}">
    <meta name="keywords" content="{{ $site_keywords ?? env('SITE_KEYWORDS') }}">

    <meta property="og:site_name" content="{{ env('SITE_NAME') }}">
    <meta property="og:title" content="{{ $site_title ?? env('SITE_NAME') }}"/>
    <meta property="og:type" content="{{ $site_type ?? 'website' }}"/>
    <meta property="og:url" content="{{ $site_url ?? Illuminate\Support\Facades\URL::current() }}"/>
    <meta property="og:image" content="{{ $site_image ?? secure_url('/images/og-banner-ccb.jpg') }}"/>
    <meta property="og:description" content="{{ $site_description ?? env('SITE_DESCRIPTION') }}"/>

    <meta name="twitter:title" content="{{ $site_title ?? env('SITE_NAME') }}">
    <meta name="twitter:description" content="{{ $site_description ?? env('SITE_DESCRIPTION') }}">
    <meta name="twitter:image" content="{{ $site_image ?? secure_url('/images/og-banner-ccb.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="shortcut icon" href="/favicon.ico" type="image/vnd.microsoft.icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/compiled/common.css') }}"/>
    @if( isset($_GET['light']) )
    <link rel="stylesheet" href="/css/light-mode.css?<?=time()?>"/>
    @endif
    @yield('header')
  </head>
  <body class="bg-dark text-white" data-scroll="1">
    <header class="border-bottom border-dark">
      <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent">
          <button class="navbar-toggler mt-1 mb-1" type="button" data-toggle="collapse" data-target="#headerNav" aria-controls="headerNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse justify-content-md-center" id="headerNav">
            <ul class="navbar-nav">
              <li class="nav-item {{ (isset($active_page) && $active_page == 'home') ? 'active' : ''  }}">
                <a class="nav-link text-md-center" href="/">
                  <i class="fas fa-igloo animated pulse slower infinite delay-0.5s"></i>
                  <div>Home</div>
                </a>
              </li>
              <li class="nav-item {{ (isset($active_page) && in_array($active_page, ['clan', 'lockouts', 'seals', 'seals_breakdown']) ) ? 'active' : ''  }}">
                <a class="nav-link text-md-center" href="/clan/lockouts">
                  <i class="ra ra-double-team"></i>
                  <div>Clan</div>
                </a>
              </li>
              <li class="nav-item {{ (isset($active_page) && in_array($active_page, ['stats', 'weapons', 'pve', 'pvp', 'gambit']) ) ? 'active' : ''  }}">
                <a class="nav-link text-md-center" href="/stats">
                  <i class="far fa-chart-bar animated pulse slower infinite delay-0.5s"></i>
                  <div>Stats</div>
                </a>
              </li>
              <li class="nav-item {{ (isset($active_page) && $active_page == 'guides') ? 'active' : ''  }}">
                <a class="nav-link text-md-center" href="/guides">
                  <i class="fas fa-book"></i>
                  <div>Guides</div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-md-center" href="{{ env('DISCORD_LINK') }}" target="_blank">
                  <i class="fab fa-discord animated pulse slower infinite delay-0.5s"></i>
                  <div>Discord</div>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-md-center" href="https://www.bungie.net/en-us/ClanV2?groupid=3717919" target="_blank">
                  <i class="fas fa-door-open animated pulse slower infinite delay-0.5s"></i>
                  <div>Join Us</div>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </header>

    <main class="d-flex flex-column">
    @yield('body')
    </main>

    <section id="members-online" class="w-100 text-center" data-show="0">
      <div class="pt-2 pb-2 pl-2 pr-2" id="members-online-text">
        <i class="fas fa-circle fa-sm text-success mr-1" style="font-size: 0.6rem;position: relative; bottom: 1px;"></i> <span id="member-count">2 members</span> online
        <small id="members-online-toggle-icon" data-status="up" class="pr-3"><i class="fas fa-chevron-down fa-lg animated rotateIn delay-0.5s"></i></small>
      </div>
      <div id="members-online-table" class="p-2 border-top border-dark"></div>
    </section>

    <footer class="border-top border-dark">
      <div id="footer" class="text-center pt-4 pb-4 pl-3 pr-3">
        <div>
          <small class="text-white">&copy; 2019 ccboys.xyz</small>
        </div>
        <div>
          <small class="text-white">Developed by <a href="https://www.bungie.net/en/Profile/4/4611686018474971535" target="_blank">xenodus</a></small>
        </div>
      </div>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

      <script src="{{ mix('/js/compiled/common.js') }}"></script>
      @yield('footer')
    </footer>
  </body>
</html>