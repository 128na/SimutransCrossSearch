<?php

namespace App\Console\Commands\Articles;

use App\Models\Article;
use App\Services\ArticleSearchService;
use App\Services\SummaryImageService;
use App\Services\TweetService;
use Illuminate\Console\Command;

class TweetSummaryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tweet:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tweet Summary';

    /**
     * @var ArticleSearchService
     */
    private $article_service;
    /**
     * @var SummaryImageService
     */
    private $image_service;
    /**
     * @var TweetService
     */
    private $tweet_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ArticleSearchService $article_service,
        SummaryImageService $image_service,
        TweetService $tweet_service
    ) {
        parent::__construct();
        $this->article_service = $article_service;
        $this->image_service = $image_service;
        $this->tweet_service = $tweet_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $rows = 5;
        $cols = 5;
        $width = 320;
        $height = 180;

        $articles = $this->article_service->latest([], $rows * $cols);

        $image = $this->image_service->make($articles, $rows, $cols, $width, $height);

        $path = storage_path('summary.jpg');
        $image->save($path);

        $date = now()->modify('-1 day')->format('m月d日');
        $this->tweet_service->postMedia([$path], "昨日（{$date}）のしむとら");
    }
}
