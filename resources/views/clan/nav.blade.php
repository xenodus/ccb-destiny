<div id="clan-sub-menu" class="mt-4 mb-4">
  <ul class="nav justify-content-center">
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'lockouts') ? 'active' : ''  }}" href="/clan/lockouts">
        @if((isset($active_page) && $active_page == 'lockouts'))<h1>@endif
        Weekly Raid Lockouts
        @if((isset($active_page) && $active_page == 'lockouts'))</h1>@endif
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ (isset($active_page) && $active_page == 'seals') ? 'active' : ''  }}" href="/clan/seals">
        @if((isset($active_page) && $active_page == 'seals'))<h1>@endif
        Seal Completions
        @if((isset($active_page) && $active_page == 'seals'))</h1>@endif
      </a>
    </li>
  </ul>
</div>