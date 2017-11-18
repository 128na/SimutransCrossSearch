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
          <input type="text" class="form-control" placeholder="アドオン名" name="word" value="{{ $word ?? null }}">
        </div>
        <div class="form-group">
          <label class="sr-only" for="pak">選択</label>
          <select class="form-control" id="pak" name="pak">
            <option value="pak64" {{ (($pak ?? null) === 'pak64') ? 'selected' : '' }}>pak64</option>
            <option value="pak128" {{ (($pak ?? null) === 'pak128') ? 'selected' : '' }}>pak128</option>
            <option value="pak128.japan" {{ (($pak ?? null) === 'pak128.japan') ? 'selected' : '' }}>pak128.Japan</option>
          </select>
        </div>
        <button type="submit" class="btn btn-default">検索</button>
      </form>
    </div>
  </div>
</nav>
