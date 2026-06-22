# Known Risks（保護すべき挙動の台帳）

「壊れてはいけない挙動」の台帳。1 行＝1 保護挙動。Assurance Audit の採点結果を記録し、
今後の棚卸し・監査の対象にする（`docs/dependency-debt.md` と同じ運用思想）。

- **制御**: None（無し）/ Detective（事後に検知・通知のみ）/ Preventive（事前に阻止）
- **Status**: 🔴 Missing/Stale/Weak ・ 🟡 Structural Weakness/SPOF ・ 🟢 OK
- 是正が完了したら Status とテスト欄を更新する（行は削除せず履歴として残す）。
- 初回診断の根拠は [assurance-audit-2026-06-22.md](assurance-audit-2026-06-22.md) を参照。

## A. Scrape / Extract パイプライン

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| A1 | 1 サイト/1URL の失敗で他サイト・他 URL の処理が止まらない | Preventive（ハンドラ毎の try/catch） | 2（`Extract/Japan/HandlerIsolationTest` + `Scrape/Japan/HandlerIsolationTest`） | 🟢 OK | — | 2026-06-22 |
| A2 | HTTP 失敗時に RawPage を空/部分 HTML で上書きしない | Preventive（`FetchHtml` で `Http::get()->throw()`。非2xx も例外として扱い `retry()` 経由で失敗、upsert に到達しない） | 3（`FetchHtmlTest::test_throws_on_non_2xx...` + `Scrape/Japan/HandlerFailureTest`×2） | 🟢 OK | — | 2026-06-22 |
| A3 | 同一 RawPage に extract を再実行しても Page 重複が出ない（冪等） | Preventive（`pages_url_unique` + HasOne） | 2（`Extract/UpdateOrCreatePageTest`） | 🟢 OK | — | 2026-06-22 |
| A4 | 抽出失敗時にスクレイプ済データ（RawPage）を破壊しない | Preventive（`MarkExtractFailed`：削除せず `extract_failed_at` で隔離、成功時にクリア。`extract_failed_at` に index 追加済み） | 4（`MarkExtractFailedTest`×3 + `HandlerIsolationTest`） | 🟢 OK | — | 2026-06-22 |
| A5 | 同一 URL の重複行を作らない | Preventive（DB 一意制約） | 2（`Models/UrlUniqueConstraintTest`） | 🟢 OK | — | 2026-06-22 |

## B. Notion 同期

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| B1 | 再実行で Notion ページを重複作成しない | Preventive（URL 突合で create/update 分岐） | 2（`test_sync_action_creates_updates_and_deletes_pages` + `test_does_not_create_duplicate_when_page_already_exists_in_notion`） | 🟢 OK | — | 2026-06-22 |
| B2 | 1 件の Notion API エラーでバッチ全体が止まらない | Preventive（delete/add 両方とも項目単位 try/catch + 継続 + 失敗件数を error ログ。`addNewNotionPages` は `keyNotionPagesByUrl()` で URL 解析失敗も個別隔離。URL が無い Notion ページは削除対象から除外） | 3（`test_continues_syncing_when_one_item_fails` + `test_continues_deleting_when_one_delete_fails` + `test_does_not_delete_notion_page_without_url`） | 🟢 OK | — | 2026-06-22 |
| B3 | 未変更項目を毎回 re-PUSH せず API クォータを浪費しない | None（`synced_at`/状態フラグ無し） | 0 | 🔴 Missing | **記録のみ**。API 呼び出し回数が問題化したら `synced_at` を導入 | 2026-06-22 |
| B4 | Notion シークレットを例外/ログ/Discord に流出させない | Preventive（`SecretScrubber` で送出前に伏字化。C3 と一体） | 7（C3 のテストと共有） | 🟢 OK | — | 2026-06-22 |

## C. シークレット / 資格情報の取扱い

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| C1 | デバッグページが config/スタックトレースを露出しない | Preventive（`.env.example`/`.ci`/`.deploy` すべて `APP_DEBUG=false`、コード既定も false） | 2（`Http/ApiErrorResponseTest` + `Http/WebErrorResponseTest`） | 🟢 OK | — | 2026-06-22 |
| C2 | API 例外が生のトレースを返さない | Preventive（`APP_DEBUG=false` で汎用 JSON エラー） | 2（`Http/ApiErrorResponseTest`×2） | 🟢 OK | — | 2026-06-22 |
| C3 | 例外レポート（log/Discord）に機密値を混入させない | Preventive（`SecretScrubber`（伏字化対象は5文字以上の値のみ。"root"等の短い開発用パスワードでの誤爆を回避）+ `RedactSecretsProcessor`/tap + `ConvertDiscord`（親クラスの `addMessageStacktrace` を no-op 化し、未伏字化の生トレースでの上書きも防止） で送出前に伏字化） | 7（`SecretScrubberTest`×4 + `RedactSecretsProcessorTest` + `ConvertDiscordTest`×2） | 🟢 OK | — | 2026-06-22 |
| C4 | 実シークレットを git に混入させない | Preventive（`.gitignore` で `.env` 除外、committed な `.env.*` は空値） | N/A | 🟢 OK | — | 2026-06-22 |

## D. 公開検索 / Feed / API

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| D1 | raw_pages（生 HTML）を公開経路に露出しない | Preventive（検索/Feed で rawPage を eager-load しない、Page に html 列無し） | 2（`PageResourceTest::test_search_does_not_eager_load_raw_page_relation` + `SearchPage/FeedActionTest`） | 🟢 OK | — | 2026-06-22 |
| D2 | 非公開コンテンツを検索/Feed に出さない | N/A（Page に公開状態の概念が無い） | — | — | 対象外（将来 status 列を足す場合は再評価） | 2026-06-22 |
| D3 | API/Feed 応答に内部フィールドを含めない | Preventive（PageResource 許可リスト + `Page::toFeedItem()`） | 2（`PageResourceTest::test_resource_exposes_only_whitelisted_fields` + `test_feed_item_does_not_expose_internal_fields_or_raw_html`） | 🟢 OK | — | 2026-06-22 |
| D4 | 半分書かれた Page 行を検索に見せない（原子性） | Preventive（`UpdateOrCreatePageWithPaks` が Page 更新 + Pak 同期を `DB::transaction()` で原子化。3 Handler すべてこれを使用） | 2（`Extract/UpdateOrCreatePageWithPaksTest`） | 🟢 OK | — | 2026-06-22 |

<!--
運用メモ:
- この台帳は Assurance Audit（assurance-audit スキル）の採点結果を反映する。
- Coverage（量）ではなく Confidence（守られているか）を追跡する台帳。
- 改修完了行は Status を 🟢/🟡 に更新し、テスト欄に本数を反映する。
-->
