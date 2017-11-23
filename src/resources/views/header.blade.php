<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"data-toggle="collapse"data-target="#navbarEexample8">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ route('index') }}">
        {{ config('const.app.name') }}
      </a>
    </div>
    <div class="collapse navbar-collapse" id="navbarEexample8">

      <form class="navbar-form navbar-right" role="search" action="{{ route('search') }}" method="get">
        <div class="form-group">
          <a href="{{ route('sites') }}">関連サイト更新情報</a>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" placeholder="アドオン名" name="word" value="{{ $word ?? null }}">
        </div>
        <div class="form-group">
          <label class="sr-only" for="pak">選択</label>
          <select class="form-control" id="pak" name="pak">
@foreach(array_keys(config('const.pak')) as $name)
            <option value="{{ $name }}" {{ (($pak ?? null) == $name) ? 'selected' : '' }}>pak{{ $name }}</option>
@endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-default">検索</button>
      </form>
    </div>
  </div>
</nav>
