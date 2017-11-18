<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Validator;
use App\Rules\ExistsSiteName;
use App\Models\SiteFactory;
use App\Models\PageFactory;
use App;

class Scrape extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'scrape:site {site_name}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Scrape Website';

  // 取得間隔
  const WAIT_SEC = 5;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
      parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    // 入力値バリデーション
    $validator = Validator::make($this->argument(), [
      'site_name' => ['required', new ExistsSiteName],
    ]);
    if ($validator->fails()) {
      exit($validator->errors()->first('site_name'));
    }

    $site_name = $this->argument('site_name');
    static::logger("start {$site_name}");

    $site = SiteFactory::forge($site_name);
    try {
      $urls = $site->scrape()->getUrls();
    } catch(\Exception $e) {
        static::logger("リスト取得失敗 {$e->getMessage()}");
        exit();
    }

    foreach ($urls as $url) {
      $page = PageFactory::forge($site_name, $url);
      try {
        $page->scrape()->save();
        static::logger("save {$page->getTitle()}");
      } catch(\Exception $e) {
        static::logger("ページ取得失敗 {$e->getMessage()}");
      }
      // テスト環境は1件のみ
      if (App::environment('local', 'development')) {
        break;
      }
      sleep(static::WAIT_SEC);
    }

    static::logger("end {$site_name}");
  }

  public static function logger($message)
  {
    echo sprintf("[%s] %s\n", now(), $message);
    logger($message);
  }
}
