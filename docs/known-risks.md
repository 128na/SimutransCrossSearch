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
| A1 | 1 サイト/1URL の失敗で他サイト・他 URL の処理が止まらない | Preventive（ハンドラ毎の try/catch） | 1（`Extract/Japan/HandlerIsolationTest`） | 🟡 SPOF | テストを 2 本以上に（scrape 側 / 他サイトにも展開） | 2026-06-22 |
| A2 | HTTP 失敗時に RawPage を空/部分 HTML で上書きしない | Preventive（fetch 例外時は upsert に到達しない） | 1（`Scrape/Japan/HandlerFailureTest`） | 🟡 SPOF | 残課題: 非2xx でも body を書き込む経路がある。`->throw()` 等で非2xx も失敗扱いにするか検討 | 2026-06-22 |
| A3 | 同一 RawPage に extract を再実行しても Page 重複が出ない（冪等） | Preventive（`pages_url_unique` + HasOne） | 1（`Extract/UpdateOrCreatePageTest`） | 🟡 SPOF | テストを 2 本以上に | 2026-06-22 |
| A4 | 抽出失敗時にスクレイプ済データ（RawPage）を破壊しない | Preventive（`MarkExtractFailed`：削除せず `extract_failed_at` で隔離、成功時にクリア） | 4（`MarkExtractFailedTest`×3 + `HandlerIsolationTest`） | 🟢 OK | — | 2026-06-22 |
| A5 | 同一 URL の重複行を作らない | Preventive（DB 一意制約） | 2（`Models/UrlUniqueConstraintTest`） | 🟢 OK | — | 2026-06-22 |

## B. Notion 同期

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| B1 | 再実行で Notion ページを重複作成しない | Preventive（URL 突合で create/update 分岐） | 1（Strong） | 🟡 SPOF | テストを 2 本以上に増やす | 2026-06-22 |
| B2 | 1 件の Notion API エラーでバッチ全体が止まらない | Preventive（項目単位 try/catch + 継続 + 失敗件数を error ログ） | 1（`SyncActionTest::test_continues_syncing_when_one_item_fails`） | 🟡 SPOF | テストを 2 本以上に（delete 側の失敗継続も） | 2026-06-22 |
| B3 | 未変更項目を毎回 re-PUSH せず API クォータを浪費しない | None（`synced_at`/状態フラグ無し） | 0 | 🔴 Missing | **記録のみ**。API 呼び出し回数が問題化したら `synced_at` を導入 | 2026-06-22 |
| B4 | Notion シークレットを例外/ログ/Discord に流出させない | Preventive（`SecretScrubber` で送出前に伏字化。C3 と一体） | 4（C3 のテストと共有） | 🟢 OK | — | 2026-06-22 |

## C. シークレット / 資格情報の取扱い

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| C1 | デバッグページが config/スタックトレースを露出しない | Preventive（`.env.example`/`.ci`/`.deploy` すべて `APP_DEBUG=false`、コード既定も false） | 1（C2 の `ApiErrorResponseTest` で間接担保） | 🟡 SPOF | Web 側デバッグページ向けの直接テストは未追加 | 2026-06-22 |
| C2 | API 例外が生のトレースを返さない | Preventive（`APP_DEBUG=false` で汎用 JSON エラー） | 1（`Http/ApiErrorResponseTest`） | 🟡 SPOF | テストを 2 本以上に | 2026-06-22 |
| C3 | 例外レポート（log/Discord）に機密値を混入させない | Preventive（`SecretScrubber` + `RedactSecretsProcessor`/tap + `ConvertDiscord` で送出前に伏字化） | 4（`SecretScrubberTest`×3 + `RedactSecretsProcessorTest`） | 🟢 OK | — | 2026-06-22 |
| C4 | 実シークレットを git に混入させない | Preventive（`.gitignore` で `.env` 除外、committed な `.env.*` は空値） | N/A | 🟢 OK | — | 2026-06-22 |

## D. 公開検索 / Feed / API

| ID | 保護すべき挙動 / Expected Outcome | 制御 | テスト | Status | 是正条件 | 記録日 |
|----|----------------------------------|------|--------|--------|----------|--------|
| D1 | raw_pages（生 HTML）を公開経路に露出しない | Preventive（検索/Feed で rawPage を eager-load しない、Page に html 列無し） | 1（`PageResourceTest::test_search_does_not_eager_load_raw_page_relation`） | 🟡 SPOF | Feed 経路の assert も追加 | 2026-06-22 |
| D2 | 非公開コンテンツを検索/Feed に出さない | N/A（Page に公開状態の概念が無い） | — | — | 対象外（将来 status 列を足す場合は再評価） | 2026-06-22 |
| D3 | API/Feed 応答に内部フィールドを含めない | Preventive（PageResource 許可リスト） | 1（`PageResourceTest::test_resource_exposes_only_whitelisted_fields`） | 🟡 SPOF | テストを 2 本以上に | 2026-06-22 |
| D4 | 半分書かれた Page 行を検索に見せない（原子性） | None（`UpdateOrCreatePage` に transaction 無し） | 0 | 🟡 Structural Weakness | （低優先・データ品質）将来 transaction か status 列で是正 | 2026-06-22 |

<!--
運用メモ:
- この台帳は Assurance Audit（assurance-audit スキル）の採点結果を反映する。
- Coverage（量）ではなく Confidence（守られているか）を追跡する台帳。
- 改修完了行は Status を 🟢/🟡 に更新し、テスト欄に本数を反映する。
-->
