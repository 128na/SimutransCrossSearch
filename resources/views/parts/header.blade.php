<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{config('app.url')}}">{{ config('app.name') }}</a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
              <li class="nav-item {{ request()->route()->named(['pages.index', 'pages.search']) ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('pages.index') }}">Addon</a>
              </li>
              <li class="nav-item {{ request()->route()->named('articles.index') ? 'active' : ''}}">
                <a class="nav-link" href="{{ route('articles.index') }}">Media</a>
              </li>
            </ul>
        </div>
    </div>
</nav>
