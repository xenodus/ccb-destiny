<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kweh! - A Final Fantasy XIV (FFXIV) Discord Tracker Bot</title>
    <meta name="author" content="name">
    <meta name="description" content="A Final Fantasy XIV (FFXIV) Discord Bot to view character profiles, look up items, marketboard prices, receive lodestone news and fashion report results">
    <meta name="keywords" content="ffxiv, discord, final fantasy, kweh, bot, square enix">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:image:width" content="1190">
    <meta property="og:image:height" content="623">
    <meta property="og:description" content="A Final Fantasy XIV (FFXIV) Discord Bot to view character profiles, look up items, marketboard prices, receive lodestone news and fashion report results">
    <meta property="og:title" content="Kweh! - A Final Fantasy XIV (FFXIV) Discord Tracker Bot">
    <meta property="og:url" content="https://kwehbot.xyz/">
    <meta property="og:image" content="https://kwehbot.xyz/img/fb-og/og-image.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css?<?=time()?>"/>
  </head>
  <body class="bg-grey text-white">
    <main>
      <div class="container" style="margin-top: 25px; max-width: 920px;">
        <div class="mb-3">
          <header>
            <div class="d-flex align-items-end justify-content-between">
              <div class="site-title">
                <h1>Kweh! Discord Bot</h1>
              </div>
              <div class="header-img">
                <img src="/files/images/FFXIV_Alpha_Render_03.png"/>
              </div>
            </div>
          </header>
        </div>
        <div class="d-flex">
          <div class="p-3 ff-style-container" style="min-height: 200px; width: 200px;">
            <div class="menu-header">Menu</div>
            <div class="side-menu">
              <nav>
                <ul>
                  <li><a href="">Home</a></li>
                  <li><a href="">Commands</a></li>
                  <li><a href="">Contact</a></li>
                </ul>
              </nav>
            </div>
          </div>
          <div class="p-3 ml-3 ff-style-container" style="width: 100%;">
            <p><strong><em>Kweh!</em></strong> is yet another Final Fantasy 14 (FFXIV) Discord Bot that fetches data from the official <a href="https://na.finalfantasyxiv.com/lodestone/" target="_blank">Lodestone</a> and various community websites.</p>

            <p>Features includes:</p>

            <div>
              <ul>
                <li>Lodestone profiles</li>
                <li>Marketboard prices via <a href="https://universalis.app/" target="_blank">Universalis</a></li>
                <li><a href="https://www.fflogs.com/" target="_blank">FFLogs</a></li>
                <li>Item and recipe information via <a href="https://ffxivteamcraft.com/" target="_blank">Teamcraft</a> & <a href="https://xivapi.com/" target="_blank">XIVAPI</a></li>
                <li>Lodestone news via <a href="https://github.com/mattantonelli/lodestone/wiki" target="_blank">Raelys' Lodestone API</a></li>
                <li>Fashion report results by <a href="https://twitter.com/KaiyokoStar" target="_blank">Miss Kaiyoko Star</a></li>
              </ul>
            </div>

            <p>There's full support for English and partial support for French, German and Japanese.</p>

            <p><strong><em>Kweh!</em></strong> requires minimal permissions to operate. It does not need administrator rights.</p>

            <p>To invite Kweh! to your server, click on the button below:</p>

            <div class="add-bot-btn">
              <a href="https://discordapp.com/oauth2/authorize?&amp;client_id=725551243551834112&amp;scope=bot&amp;permissions=59392" class="btn" role="button">Add Me To Your Server <i class="fas fa-external-link-alt"></i></a>
              <div class="cursor-hint animated pulse slower infinite delay-0.5s">
                <img src="/files/images/ff_cursor_sm2.png">
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>
    <footer>
      <div id="footer" class="text-center mt-2 mb-2 pl-3 pr-3 text-secondary">
        <div>&copy; 2020 kwehbot.xyz</div>
        <div><small>ALL FINAL FANTASY XIV CONTENT IS PROPERTY OF SQUARE ENIX CO., LTD</small></div>
      </div>
      <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i" rel="stylesheet">
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
      <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </footer>
  </body>
</html>
