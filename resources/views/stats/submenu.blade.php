<div id="stats-sub-menu" class="mt-4 mb-4">
  <ul class="nav justify-content-center">
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'stats') ? 'active' : ''  }}" href="/stats/raid">Raid Completions</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'weapons') ? 'active' : ''  }}" href="/stats/weapons">Weapon Kills</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'pve') ? 'active' : ''  }}" href="/stats/pve">PvE</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'pvp') ? 'active' : ''  }}" href="/stats/pvp">PvP</a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'gambit') ? 'active' : ''  }}" href="/stats/gambit">Gambit</a>
    </li>
  </ul>
</div>