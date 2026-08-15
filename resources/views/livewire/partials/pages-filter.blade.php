<div class="mb-2">
    @foreach (App\Enums\PakSlug::cases() as $pak)
    <label class="leading-6 text-gray-900 pr-3 font-medium dark:text-white">
        <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" wire:model="paks.{{$pak->value}}" />
        {{__('misc.'.$pak->value)}}</label>
    @endforeach

    @foreach (App\Enums\SiteName::cases() as $site)
    <label class="leading-6 text-gray-900 pr-3 font-medium dark:text-white">
        <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" wire:model="sites.{{$site->value}}" />{{__('misc.'.$site->value)}}</label>
    @endforeach
</div>
<div class="inline-flex rounded-md shadow-sm" role="group">
    <input type="text" id="keyword" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700" placeholder="キーワード" wire:model="keyword" wire:keydown.enter="onConditionUpdate" />
    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700" wire:click="onConditionUpdate">検索</button>
    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700" wire:click="clear">リセット</button>
</div>
@error('keyword')
    <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
@enderror
