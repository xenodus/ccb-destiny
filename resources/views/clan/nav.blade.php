<div id="clan-sub-menu" class="mt-4 mb-4">
  <ul class="nav justify-content-center">
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'roster') ? 'active' : ''  }}" href="/clan/roster">
        @if((isset($active_page) && $active_page == 'roster'))<h1>@endif
        Roster
        @if((isset($active_page) && $active_page == 'roster'))</h1>@endif
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'lockouts') ? 'active' : ''  }}" href="/clan/lockouts">
        @if((isset($active_page) && $active_page == 'lockouts'))<h1>@endif
        Weekly Raid Lockouts
        @if((isset($active_page) && $active_page == 'lockouts'))</h1>@endif
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ ( isset($active_page) && in_array($active_page, ['seals', 'seals_breakdown']) ) ? 'active' : ''  }}" href="/clan/seals">
        @if((isset($active_page) && in_array($active_page, ['seals', 'seals_breakdown'])))<h1>@endif
        Seal Completions
        @if((isset($active_page) && in_array($active_page, ['seals', 'seals_breakdown'])))</h1>@endif
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ ( isset($active_page) && in_array($active_page, ['clan_exotic']) ) ? 'active' : ''  }}" href="/clan/exotics">
        @if((isset($active_page) && in_array($active_page, ['clan_exotic'])))<h1>@endif
        Uncollected Exotics
        @if((isset($active_page) && in_array($active_page, ['clan_exotic'])))</h1>@endif
      </a>
    </li>
  </ul>
</div>