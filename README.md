# Simutrans アドオン横断検索

https://cross-search.128-bit.net

## 概要

Simutrans のアドオンや Mod 情報を複数のサイトから収集し、横断検索を可能にする Laravel アプリケーションです。

## 機能

-   デイリーで日本語 wiki、実験室、ポータルのアドオンページを取得して検索インデックスを作成
-   更新一覧の表示、検索（Livewire 使用）
-   デイリーで入門サイトの新着一覧へ同期（Notion API）
-   RSS フィード配信

## 技術スタック

-   **PHP**: 8.3+
-   **フレームワーク**: Laravel 12.x
-   **フロントエンド**: Livewire 3.x, Tailwind CSS
-   **データベース**: MySQL
-   **外部連携**: Notion API, Discord Logger
-   **その他**: Guzzle HTTP Client, Spatie Laravel Feed

## 必要要件

-   PHP 8.3 以上
-   Composer
-   MySQL 8.0 以上
-   Node.js & npm（フロントエンド開発時）

## セットアップ

### 1. リポジトリのクローン

```bash
git clone https://github.com/128na/SimutransCrossSearch.git
cd SimutransCrossSearch
```

### 2. 依存関係のインストール

```bash
composer install
```

### 3. 環境設定ファイルの作成

```bash
cp .env.example .env
```

`.env`ファイルを編集して以下の項目を設定：

```env
APP_NAME=Simutransアドオン横断検索
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

# データベース設定
DB_DATABASE=cs
DB_USERNAME=root
DB_PASSWORD=your_password

# ポータルDB設定（別途ポータルサイトのDBが必要な場合）
PORTAL_DB_DATABASE=po

# Notion連携（オプション）
NOTION_SECRET=your_notion_secret
NOTION_DATABASE_ID=your_database_id

# Discord通知（オプション）
LOG_DISCORD_WEBHOOK_URL=your_discord_webhook
```

### 4. アプリケーションキーの生成

```bash
php artisan key:generate
```

### 5. データベースの準備

MySQL データベースを作成：

```sql
CREATE DATABASE cs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

データベーススキーマをインポート：

```bash
mysql -u root -p cs < database/schema/mysql-schema.sql
```

### 6. キャッシュディレクトリの権限設定

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 起動方法

### 開発サーバーの起動

```bash
php artisan serve
```

ブラウザで http://localhost:8000 にアクセス

## 主要なコマンド

### スクレイピング関連

```bash
# 全サイトからHTMLを取得してRawPageを更新
php artisan app:scrape

# 特定サイトのみスクレイピング
php artisan app:scrape japan    # 日本語wiki
php artisan app:scrape twitrans # 実験室
php artisan app:scrape portal   # ポータル
```

```bash
# RawPageから情報を抽出してPageを更新
php artisan app:extract

# 特定サイトのみ抽出
php artisan app:extract japan
```

### Notion 同期

```bash
# 入門サイトの新着DBと同期
php artisan app:notion-sync
```

## スケジュール実行

`routes/console.php`で定義されている自動実行タスク：

-   **毎日 1:00**: `app:scrape` - サイトから HTML を取得
-   **毎日 3:00**: `app:extract` - HTML から情報を抽出
-   **毎日 7:00**: `app:notion-sync` - Notion と同期

スケジューラーを有効にするには、crontab に以下を追加：

```cron
* * * * * cd /path/to/SimutransCrossSearch && php artisan schedule:run >> /dev/null 2>&1
```

## データベース構造

### 主要テーブル

-   **raw_pages**: スクレイピングした生 HTML
-   **pages**: 抽出・整形されたページ情報
-   **paks**: Pakset 情報（64、128、128.japan 等）
-   **page_pak**: ページと Pak の関連テーブル
-   **articles**: ポータルサイトの記事（別 DB 参照の可能性あり）
-   **cache**: Laravel キャッシュ

## 開発

### コーディングスタンダード

```bash
# Laravel Pintでコードフォーマット
composer pint

# PHPStanで静的解析
composer stan

# Rectorでコードリファクタリング
composer rector

# 全チェック実行
composer all
```

### テスト実行

```bash
# PHPUnit実行
php artisan test

# または
vendor/bin/phpunit
```

### IDE Helper 生成

```bash
php artisan ide-helper:generate
php artisan ide-helper:models -WR
```

## プロジェクト構成

```
app/
  Actions/          # ビジネスロジック
    Scrape/         # スクレイピング処理
    Extract/        # データ抽出処理
    SearchPage/     # 検索機能
    SyncNotion/     # Notion同期
  Console/Commands/ # Artisanコマンド
  Enums/            # Enum定義（SiteName, PakSlug等）
  Http/
    Controllers/    # コントローラー
  Livewire/         # Livewireコンポーネント
  Models/           # Eloquentモデル
config/             # 設定ファイル
database/
  migrations/       # マイグレーションファイル
  schema/           # DBスキーマ定義
  seeders/          # シーダー
resources/
  views/            # Bladeテンプレート
routes/
  web.php           # Webルート
  console.php       # スケジュール定義
```

## トラブルシューティング

### データベース接続エラー

-   `.env`の DB 設定を確認
-   MySQL サービスが起動しているか確認
-   データベースが作成されているか確認

### スクレイピングエラー

-   対象サイトへのアクセスが可能か確認
-   タイムアウト設定を調整
-   ログファイル（`storage/logs/laravel.log`）を確認

### キャッシュ関連の問題

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## ライセンス

MIT License

## 作者

128na (https://github.com/128na)
