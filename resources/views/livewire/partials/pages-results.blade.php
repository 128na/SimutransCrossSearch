<ul>
    @forelse ($this->pages as $page)
    <li class="my-2">
        <div>
            @foreach ($page->paks as $pak)
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('misc.'.$pak->slug->value) }}</span>
            @endforeach

            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ __('misc.'.$page->site_name->value) }}</span>

            <a target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-500 hover:underline" href="{{ $page->url }}">{{ $page->title }}</a>

            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ $page->last_modified->toDateTimeString() }}</span>
        </div>
        <div class="text-gray-900 text-sm px-5 py-2.5 dark:text-white">{{ $page->getSummary(300) }}</div>
    </li>
    @empty
    <li class="my-2">該当なし</li>
    @endforelse
</ul>
