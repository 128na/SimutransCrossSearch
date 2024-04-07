<header>
    <nav class="px-4 lg:px-6 py-2.5 mb-2.5 dark:bg-gray-800 border-solid border-b" >
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-md">
            <a href="/" class="flex items-center">
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">{{ $title ?? config('app.name') }}</span>
            </a>
            <div class=" justify-between items-center w-full md:flex md:w-auto md:order-1">
                <ul class="flex flex-col mt-4 font-medium md:flex-row md:space-x-8 md:mt-0">
                    <li>
                        <a
                            href="/feed"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block p-2 text-gray-700 dark:text-white"
                        >Feed</a>
                    </li>
                    <li>
                        <a
                            href="https://www.notion.so/simutrans-intro/API-2cbb6813417b4b2f80c27392b4d6b3d2?pvs=4#3a08260b0bdd4f9ab6f887123704332e"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block p-2 text-gray-700 dark:text-white"
                        >API</a>
                    </li>
                    <li>
                        <a
                            href="https://github.com/128na/SimutransCrossSearch"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block p-2 text-gray-700 dark:text-white"
                        >GitHub</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
