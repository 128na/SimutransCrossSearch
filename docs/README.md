# ドキュメント規約

このリポジトリのドキュメントは「実装とテストが SSOT（唯一の真実）」を前提に、
md として残すものを次の3種類に限定する。この規約は [docs-lint](https://github.com/128na/docs-lint) で機械検証される。

## 原則

1. コードを読めば分かることは書かない。
2. 残すのは「意思決定の理由」（ADR）と「過去時点の記録」（`docs/records/`）だけ。どちらも書いたら変更しない。
3. 変化する事実（ステータス・日付・採番）は台帳に集約し、md 本文には書かない。
4. 手動維持の索引は持たない。一覧は `ls docs/records/`（日付順に並ぶ）で得る。

## 「実装が SSOT」を削除の言い訳にする前に

「コードを読めば分かる」は思い込みで成立していないことがある。実在するクラス名を
含みながら内部構造・メソッドシグネチャが実装と全く異なる「一見実装がありそうで
実は架空」のケースが、他リポジトリへの実地移行で複数見つかっている。現在形の
spec 相当文書を削除する前に:

1. **対応する実装が実在するか確認する**。存在しなければ削除しない。`docs/records/` へ
   日付付きで凍結する。
2. **内容が実装と一致するか確認する**。クラス名が実在するだけでは不十分。文書が挙げる
   具体例のうち最低1つは実際のソースファイルを開き、クラス構成・メソッドシグネチャ・
   データ構造まで突き合わせる。
3. **テストが仕様の主張を実際に検証しているか確認する**。記述量に対してテストケース数が
   見合っているか照合する。名ばかりのテストしかない場合は、テスト拡充が先か、当面は
   生きた文書として残すかを判断する。
4. **原理的にテストが検出できない契約でないか確認する**。外部フォーマット/外部APIの
   reverse-engineering 文書や、自動検証のない人手同期の契約は、生きた文書 allowlist の
   正当な例外として認める。

## 分類と置き場所

### 不変記録 — docs/records/

- 命名: `YYYY-MM-DD_slug.md`（例: `2026-06-22_assurance-audit.md`）
- 対象: 調査メモ / 作業ログ / postmortem / 実験結果
- 作成後は変更しない。内容を更新したくなったら**新しい日付で新規作成**し、
  旧ファイル冒頭に `> Superseded by:` + 新記録への相対リンク、の1行だけを追記する。
  もう1つ許可される編集は、参照先ファイルが移動・削除された際の**リンクパスのみの追従**
  （主張・内容は変えず、リンク先を現在の場所や後継 ADR に向け直すだけ）。内容の書き換えは
  一切許可しない。

### 意思決定記録 — docs/adr/

- 命名: `NNNN-slug.md`（連番4桁、例: `0001-use-sqlite.md`）
- 「なぜ」だけを書く。「どうなっているか」は実装を参照させる。
- ステータス行 `> ステータス: Accepted (YYYY-MM-DD)` が必須（lint 検査対象）。
  覆すときは新 ADR を書き、旧 ADR のステータスを `Superseded by ADR-NNNN` に変える。

### 台帳 — docs/dependency-debt.md, docs/known-risks.md

- 変化する事実の SSOT。行の追加・更新・削除が正規の運用。
- [dependency-debt.md](dependency-debt.md) のスキーマは変更禁止（`/dependabot-maintenance` スキル互換）。
- [known-risks.md](known-risks.md) は解消しても行を削除せず、Status とテスト欄を更新して
  履歴として残す運用（dependency-debt.md の delete-on-resolve とは異なる）。初回診断の根拠は
  [records/2026-06-22_assurance-audit.md](records/2026-06-22_assurance-audit.md) に凍結済み。
- 新しい台帳を作るには `tools/docs-policy.json` の `ledgers` への登録が必要。

### 生きた文書 — allowlist 制

- `tools/docs-policy.json` の `livingDocs` に列挙されたファイルのみ許可。現在の一覧:
  `README.md` / `CLAUDE.md` / `docs/README.md`
- allowlist へ追加する場合は、追加理由を ADR として残すこと。
- 生きた文書は「常に現在を反映する義務」を負う。義務を果たせない文書は
  records 化（日付を付けて凍結）するか削除する。

## 禁止事項（lint がエラーにする）

- allowlist 外の「現在形 md」を docs/ やルートに置くこと
- `temp` / `tmp` / `draft` / `wip` という名前のディレクトリ（下書きは PR 説明・issue・セッションの scratchpad へ）
- `INDEX.md` / `index.md` / `TODO.md` というファイル名（索引は持たない、タスクは台帳か issue へ）
- リポジトリ外への絶対パスリンク（`~/`、ドライブレター、`file://`）
- 壊れた相対リンク

## 検証

```bash
npx --yes github:128na/docs-lint#v1.0.0
```

CI（`.github/workflows/docs-lint.yml`）でも同じものが走る。`composer run docs`（`composer run all` にも
含まれる）からも実行できる。実体は [docs-lint](https://github.com/128na/docs-lint)
パッケージ（複数リポジトリで共有、タグ `v1.0.0` 固定）。設定ファイルは引き続き
`tools/docs-policy.json`。
