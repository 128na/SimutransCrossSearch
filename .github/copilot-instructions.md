# GitHub Copilot Instructions

このファイルは GitHub Copilot エージェントがこのプロジェクトを理解するためのコンテキスト情報です。

## プロジェクト概要

### 目的

Simutrans アドオン横断検索 - 複数の Simutrans 関連サイトからアドオン・Mod 情報を収集し、統合検索を提供する Web アプリケーション

### 対象サイト

-   **japan**: 日本語 wiki
-   **twitrans**: 実験室
-   **portal**: ポータルサイト

### URL

https://cross-search.128-bit.net

## 技術スタック

-   **言語**: PHP 8.3+
-   **フレームワーク**: Laravel 12.x
-   **フロントエンド**: Livewire 3.x, Tailwind CSS
-   **データベース**: MySQL 8.0+
-   **外部 API**: Notion API, Discord Logger
-   **HTTP クライアント**: Guzzle
-   **その他**: Spatie Laravel Feed

## アーキテクチャ

### データフロー

```
1. Scrape (app:scrape)
   → 外部サイトからHTMLを取得
   → raw_pagesテーブルに保存

2. Extract (app:extract)
   → raw_pagesからデータを抽出・整形
   → pagesテーブルに保存
   → paksテーブルと関連付け

3. Search (Livewire)
   → ユーザーがキーワード・Pakで検索
   → pagesから結果を取得・表示

4. Sync (app:notion-sync)
   → pagesの最新情報をNotionに同期
```

### ディレクトリ構成

```
app/
├── Actions/              # ビジネスロジック（単一責任の原則）
│   ├── Scrape/          # スクレイピング処理
│   ├── Extract/         # データ抽出処理
│   ├── SearchPage/      # 検索機能
│   ├── SyncNotion/      # Notion連携
│   └── Logging/         # ログ処理
├── Console/Commands/     # Artisanコマンド
│   ├── Pages/
│   │   ├── ScrapeCommand.php   # HTMLスクレイピング
│   │   └── ExtractCommand.php  # データ抽出
│   └── Notion/
│       └── SyncCommand.php     # Notion同期
├── Enums/                # Enum定義
│   ├── SiteName.php     # japan, twitrans, portal
│   ├── PakSlug.php      # pak64, pak128, pak128.japan等
│   ├── Encoding.php     # 文字エンコーディング
│   └── Rss.php          # RSS設定
├── Http/
│   ├── Controllers/
│   │   ├── PageController.php        # メインページ
│   │   └── Api/PageController.php    # API
│   └── Resources/       # APIリソース
├── Livewire/
│   └── Pages.php        # 検索UI（ページネーション付き）
└── Models/
    ├── Page.php         # 検索インデックス（抽出済みデータ）
    ├── RawPage.php      # 生HTML
    ├── Pak.php          # Pakset情報
    └── Portal/          # ポータルサイト関連モデル
        ├── Article.php
        ├── Category.php
        ├── FileInfo.php
        └── Tag.php
```

## データベーススキーマ

### 主要テーブル

#### raw_pages

スクレイピングした生 HTML を保存

-   `site_name`: japan/twitrans/portal
-   `url`: スクレイピング元 URL
-   `html`: 生 HTML

#### pages

抽出・整形されたページ情報

-   `raw_page_id`: 元の raw_page
-   `site_name`: サイト識別子
-   `url`: ページ URL
-   `text`: 検索用テキスト
-   `title`: ページタイトル
-   `last_modified`: 元記事の最終更新日時

#### paks

Pakset 情報（64、128、128.japan 等）

-   `name`: Pak 名
-   `slug`: URL スラッグ

#### page_pak

pages と paks の多対多リレーション

#### articles

ポータルサイトの記事（別 DB 参照の可能性）

## 主要な Artisan コマンド

### スクレイピング

```bash
php artisan app:scrape [name]
```

-   引数なし: 全サイトをスクレイピング
-   引数あり: 指定サイトのみ（japan/twitrans/portal）
-   実行: 毎日 1:00（自動）

### データ抽出

```bash
php artisan app:extract [name]
```

-   raw_pages からデータ抽出して pages に保存
-   実行: 毎日 3:00（自動）

### Notion 同期

```bash
php artisan app:notion-sync
```

-   入門サイトの新着 DB と同期
-   実行: 毎日 7:00（自動）

## コーディング規約

### 厳格な型定義

-   すべてのファイルに `declare(strict_types=1);`
-   戻り値の型を明示
-   プロパティの型を明示

### コードスタイル

-   Laravel Pint 使用（自動フォーマット）
-   PHPStan Level 9（静的解析）
-   Rector 使用（自動リファクタリング）

### 実行コマンド

```bash
composer pint        # フォーマット
composer stan        # 静的解析
composer rector      # リファクタリング
composer all         # 全チェック実行
```

## 環境変数

### 必須

-   `APP_KEY`: Laravel アプリケーションキー
-   `DB_DATABASE`: データベース名（デフォルト: cs）
-   `DB_USERNAME`: データベースユーザー
-   `DB_PASSWORD`: データベースパスワード

### オプション

-   `PORTAL_DB_DATABASE`: ポータルサイト用 DB
-   `NOTION_SECRET`: Notion API Secret
-   `NOTION_DATABASE_ID`: Notion Database ID
-   `LOG_DISCORD_WEBHOOK_URL`: Discord 通知用 Webhook

## Livewire コンポーネント

### Pages (app/Livewire/Pages.php)

検索 UI コンポーネント

-   **プロパティ**:
    -   `keyword`: 検索キーワード
    -   `paks`: Pak フィルター（配列）
    -   `siteNames`: サイトフィルター（配列）
    -   `page`: 現在のページ
-   **機能**:
    -   リアルタイム検索
    -   Pak フィルタリング
    -   サイトフィルタリング
    -   ページネーション

## 外部連携

### Notion API

-   Notion SDK を使用: `mariosimao/notion-sdk-php`
-   用途: 入門サイトの新着情報を同期
-   設定: `config/services.php`

### Discord Logger

-   パッケージ: `marvinlabs/laravel-discord-logger`
-   用途: エラー通知、処理結果の通知
-   設定: `config/discord-logger.php`

### RSS フィード

-   パッケージ: `spatie/laravel-feed`
-   エンドポイント: `/feed`（自動生成）
-   設定: `config/feed.php`

## テスト

### 実行

```bash
php artisan test
# または
vendor/bin/phpunit
```

### 構成

-   **Unit**: 単体テスト（`tests/Unit/`）
-   **Feature**: 機能テスト（`tests/Feature/`）

## デバッグ・開発ツール

### IDE Helper

```bash
php artisan ide-helper:generate       # ヘルパー生成
php artisan ide-helper:models -WR     # モデル定義生成
```

### ログ

-   場所: `storage/logs/laravel.log`
-   Discord 通知も利用可能

## エラーハンドリング

### 共通パターン

```php
try {
    // 処理
    return self::SUCCESS;
} catch (\Throwable $throwable) {
    report($throwable);  // ログ出力＆Discord通知
    $this->error($throwable->getMessage());
    return self::FAILURE;
}
```

## パフォーマンス考慮事項

-   スクレイピング時は適切なタイムアウト設定
-   データ抽出はバッチ処理で効率化
-   Livewire 検索はページネーション必須
-   キャッシュ活用（`last_crawl`, `last_extract`）

## コード変更時の注意点

### モデル変更時

1. マイグレーションファイル作成・実行
2. `database/schema/mysql-schema.sql`を更新
3. IDE Helper を再生成

### Enum 追加時

-   SiteName, PakSlug に追加する場合は関連ロジックも更新

### Action 追加時

-   単一責任の原則を守る
-   必要に応じて Logging を追加

### Livewire コンポーネント変更時

-   プロパティ変更は URL パラメータにも影響
-   ページネーション状態に注意

## よくある実装パターン

### スクレイピング

```php
// Guzzleでページ取得
$response = $client->get($url);
$html = $response->getBody()->getContents();

// RawPageに保存
RawPage::updateOrCreate(['url' => $url], ['html' => $html]);
```

### データ抽出

```php
// RawPageから取得
$rawPage = RawPage::where('site_name', $siteName)->get();

// DOMパーサーで解析
$crawler = new Crawler($rawPage->html);
$title = $crawler->filter('h1')->text();

// Pageに保存
Page::updateOrCreate(['url' => $rawPage->url], [
    'title' => $title,
    'text' => $text,
]);
```

### 検索実装

```php
// Livewire内で
$query = Page::query()
    ->when($this->keyword, fn($q) => $q->where('text', 'like', "%{$this->keyword}%"))
    ->when($this->paks, fn($q) => $q->whereHas('paks', fn($q) => $q->whereIn('id', $this->paks)));

return $query->paginate(20);
```

## セキュリティ

-   SQL インジェクション対策: Eloquent ORM 使用
-   XSS 対策: Blade 自動エスケープ
-   CSRF 対策: Laravel 標準機能
-   環境変数: `.env`で管理（Git には含めない）

## デプロイ

1. 依存関係インストール: `composer install --no-dev`
2. 設定最適化: `php artisan config:cache`
3. ルートキャッシュ: `php artisan route:cache`
4. ビューキャッシュ: `php artisan view:cache`
5. スケジューラー設定: crontab に追加

## トラブルシューティング

### スクレイピング失敗

-   タイムアウト設定確認
-   対象サイトの可用性確認
-   ログで詳細確認

### データ抽出エラー

-   HTML 構造変更の可能性
-   Crawler のセレクタ確認
-   エンコーディング確認

### 検索結果が表示されない

-   pages テーブルにデータが存在するか確認
-   Livewire のフィルタ条件確認

## 参考リソース

-   Laravel 公式: https://laravel.com/docs
-   Livewire 公式: https://livewire.laravel.com
-   Notion API: https://developers.notion.com
