<div>
    <div class="mb-2">
    @foreach (App\Enums\PakSlug::cases() as $pak)
        <label
            class="leading-6 text-gray-900 pr-3 font-medium dark:text-white"
        >
        <input
            type="checkbox"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            wire:model="paks.{{$pak->value}}"
        />
        {{__('misc.'.$pak->value)}}</label>
    @endforeach

    @foreach (App\Enums\SiteName::cases() as $site)
        <label
            class="leading-6 text-gray-900 pr-3 font-medium dark:text-white"
        >
        <input
            type="checkbox"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            wire:model="sites.{{$site->value}}"
        />{{__('misc.'.$site->value)}}</label>
    @endforeach
    </div>
    <div class="inline-flex rounded-md shadow-sm" role="group">
        <input
            type="text"
            id="keyword"
            class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700"
            placeholder="キーワード"
            wire:model="keyword"
        />
        <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700"
            wire:click="onConditionUpdate"
        >検索</button>
        <button
            type="button"
            class="px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700"
            wire:click="clear"
        >リセット</button>
    </div>

    <div class="my-4">
        {{ $pages->onEachSide(1)->links('tailwind_custom') }}
    </div>

    <ul>
        @forelse ($pages as $page)
            <li class="my-2">
                <div>
                    @foreach ($page->paks as $pak)
                        <span
                            class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                        >{{ __('misc.'.$pak->slug->value) }}</span>
                    @endforeach

                    <span
                        class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10"
                    >{{ __('misc.'.$page->site_name->value) }}</span>

                    <a
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-blue-600 dark:text-blue-500 hover:underline"
                        href="{{ $page->url }}"
                    >{{ $page->title }}</a>

                    <span
                        class="text-xs font-medium text-gray-600 dark:text-gray-300"
                    >{{ $page->last_modified->toDateTimeString() }}</span>
                </div>
                <div
                    class="text-gray-900 text-sm px-5 py-2.5 dark:text-white"
                >{{ $page->getSummary(300) }}</div>
            </li>
        @empty
            <li class="my-2">該当なし</li>
        @endforelse
    </ul>
    <div class="my-4">
        {{ $pages->onEachSide(1)->links('tailwind_custom') }}
    </div>
</div>
