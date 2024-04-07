<footer>
    <nav class="px-4 lg:px-6 py-2.5 mt-2.5 dark:bg-gray-800 border-solid border-t" >
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-md">
            <span class="self-center text-sm dark:text-white">最終更新 crawl : {{ \Illuminate\Support\Facades\Cache::get('last_crawl', '-') }}, extract : {{ \Illuminate\Support\Facades\Cache::get('last_extract', '-') }}</span>
        </div>
    </nav>
</footer>
