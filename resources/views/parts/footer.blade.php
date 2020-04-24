<div class="container text-center">
    <p class="text-muted text-center">
        <span>created by <a href="{{ config('app.twitter.url') }}" target="_blank" rel="noopener noreferrer">{{ config('app.twitter.name') }}</a>.</span> /
        <span><a href="{{ config('app.github.url') }}" target="_blank" rel="noopener noreferrer">Github</a></span>
        <small class="ml-2">Logs:
            <span><a href="{{ route('logs.search') }}" >Search</a></span> /
            <span><a href="{{ route('logs.schedule') }}" >Schdule</a></span>
        </small>
    </p>
</div>
