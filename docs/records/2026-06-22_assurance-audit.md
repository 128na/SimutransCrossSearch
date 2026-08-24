# Assurance Audit スナップショット（2026-06-22）

これは初回の Assurance Audit（assurance-audit スキル）の診断記録。
「現在の状態」は [known-risks.md](known-risks.md) を参照。本ファイルは**初回診断の根拠**を残すための凍結記録。

採点モデル: `Intent → Behavior → Control → Evidence`。
Coverage（量）ではなく **Confidence（守られているか）** を採点する。
スコープは認証/課金/取引のない公開スクレイピング＋検索アプリとして、ユーザー選択の 4 領域。

---

## Step -1 所見（最優先）

**脅威モデル・既知リスク・runbook が一切存在しない。** `docs/dependency-debt.md` は依存負債のみ。
「何を守るべきか」の台帳が無いこと自体が最大の欠陥。本監査を機に [known-risks.md](known-risks.md) を新設した。

## New Candidate Risks（脅威探索で発見、既存台帳に無かった項目）

1. **🟡 抽出失敗時に RawPage を削除している (A4)** — `app/Actions/Extract/{Twitrans,Japan,Portal}/Handler.php`
   の catch で `$rawPage->delete()`。一過性バグでも唯一のスクレイプ結果が恒久消失。
2. **🟡 例外 → Discord/ログへのスタックトレース素通し (B4/C3)** — `bootstrap/app.php` の空ハンドラ、
   `config/discord-logger.php` の `'stacktrace' => 'smart'`、`ConvertDiscord` が trace/context を無加工送出。
   機密値（Notion secret/webhook/DB パスワード）混入経路が開いている。
3. **🔴 `.env.example` が `APP_DEBUG=true` 既定 (C1)** — redaction 無し、API も独自ハンドリング無し。
4. **Notion 同期の再同期スロットリング無し (B3)** — 毎回最新 100 件を re-PUSH。データ破損ではなく API クォータ浪費。

---

## 全挙動マトリクス（初回値）

| # | 挙動 → Expected Outcome | 制御 | テスト | Status |
|---|---|---|---|---|
| A1 | 1 サイト失敗で他が止まらない | Preventive（try/catch） | 0 | 🔴 Untested |
| A2 | HTTP 失敗時に空 HTML 上書きしない | Preventive（成功時のみ upsert） | 0 | 🔴 Untested |
| A3 | extract 再実行で Page 重複なし | Preventive（unique + HasOne） | 0 | 🔴 Untested |
| A4 | 抽出失敗時にデータ破壊しない | 誤り（delete で消去） | 0 | 🟡 Structural Weakness |
| A5 | 同一 URL 重複行なし | Preventive（DB 一意制約） | 0 | 🔴 Untested |
| B1 | 再実行で Notion 重複作成なし | Preventive（URL 突合） | 1 Strong | 🟡 SPOF |
| B2 | 1 件 API エラーで全体停止しない | None | 0 | 🔴 Missing |
| B3 | 未変更項目を re-PUSH しない | None | 0 | 🔴 Missing |
| B4 | 機密値を例外出力に混入させない | Detective のみ | 0 | 🟡 Structural Weakness |
| C1 | デバッグページが trace を露出しない | None（DEBUG=true 既定） | 0 | 🟡 Structural Weakness |
| C2 | API 例外が生 trace を返さない | None | 0 | 🔴 Missing |
| C3 | log/Discord に機密値を混入させない | Detective のみ | 0 | 🟡 Structural Weakness |
| C4 | git に実シークレット混入なし | Preventive | N/A | 🟢 OK |
| D1 | raw_pages を公開経路に露出しない | Preventive（eager-load せず） | 0 | 🔴 Untested |
| D2 | 非公開コンテンツを出さない | N/A（公開状態の概念なし） | — | 対象外 |
| D3 | 応答に内部フィールドを含めない | Preventive（Resource 許可リスト） | 0 | 🔴 Untested |
| D4 | 半書き Page を検索に見せない | None（transaction なし） | 0 | 🟡 Structural Weakness |

---

## 所見

- 🟢 は C4（git シークレット混入チェック）のみ。
- 「Untested」行（A1/A2/A3/A5/D1/D3）は機構自体は健全。回帰検知テストが無いことが欠陥。
- 最優先の構造的弱点: **A4**（証跡破壊）と **C1+C3/B4**（機密漏洩経路、1 つの修正で複数行を閉じる）。
- **B2** は最もクリアな Missing Control（scrape/extract と違い項目単位のフォールト分離が皆無）。
- 機密値が「過去に実際に漏れた痕跡」は発見していない。確認されたのは**開いた経路**であり、過去のインシデントではない。
