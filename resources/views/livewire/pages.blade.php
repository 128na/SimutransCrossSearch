<section class="px-4 lg:px-6 py-2.5 my-2.5">
    <div class="mx-auto max-w-screen-md">
        <div class="mb-2">
        @foreach (App\Enums\PakSlug::cases() as $pak)
            <label
                class="leading-6 text-gray-900 pr-3 font-medium"
            >
            <input
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                wire:model="paks.{{$pak->value}}"
                wire:click="$refresh"
            />
            {{__('misc.'.$pak->value)}}</label>
        @endforeach

        @foreach (App\Enums\SiteName::cases() as $site)
            <label
                class="leading-6 text-gray-900 pr-3 font-medium"
            >
            <input
                type="checkbox"
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                wire:model="sites.{{$site->value}}"
                wire:click="$refresh"
            />{{__('misc.'.$site->value)}}</label>
        @endforeach
        </div>
        <div class="mb-2">
            <input
                type="text"
                id="keyword"
                class="rounded-md p-2 text-gray-900 ring-1 ring-inset ring-gray-300"
                placeholder="キーワード"
                wire:model.live.debounce.300ms="keyword"
                wire:click="$refresh"
            />
            <button
                type="button"
                class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700"
                wire:click="clear"
            >リセット</button>
        </div>

        <div class="my-4">
            {{ $pages->links() }}
        </div>

        <ul>
            @foreach ($pages as $page)
                <li class="my-2">
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
                        class="text-xs font-medium text-gray-600"
                    >{{ $page->last_modified->toDateTimeString() }}</span>

                </li>
            @endforeach
        </ul>
        <div class="my-4">
            {{ $pages->links() }}
        </div>
    </div>
</section>
