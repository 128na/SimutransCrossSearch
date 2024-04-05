<div>
    <input type="text" wire:model.live.debounce.300ms="keyword" wire:click="$refresh">
    <button wire:click="clear">リセット</button>

    <div>
    @foreach (App\Enums\PakSlug::cases() as $pak)
        <label><input type="checkbox" wire:model="paks.{{$pak->value}}" wire:click="$refresh" />{{__($pak->value)}}</label>
    @endforeach
    </div>
    {{ $pages->links() }}

    @foreach ($pages as $page)
        <p>{{ $page->title}}</p>
    @endforeach

    {{ $pages->links() }}
</div>
