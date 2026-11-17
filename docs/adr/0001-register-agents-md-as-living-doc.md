# ADR: AGENTS.md を生きた文書に登録し、CLAUDE.md を参照の1行にする

> ステータス: Accepted (2026-11-18)

## 背景と問題

AI エージェント向けの作業指示は `CLAUDE.md` だけに書いていた。そのため Claude Code 以外の
エージェント（Codex など）は同じ指示を読めなかった。

2026-11 に、手元の全リポジトリで「`AGENTS.md` を正本にし、`CLAUDE.md` は `@AGENTS.md` の1行」
という形に揃えた。Claude Code は `@` の取り込みで `AGENTS.md` を読み、Codex は `AGENTS.md` を
直接読む。本リポジトリでは PR #206 で `CLAUDE.md` の中身を `AGENTS.md` へ移し、
`tools/docs-policy.json` の `livingDocs` に `AGENTS.md` を足した。[docs/README.md](../README.md) は
allowlist への追加理由を ADR に残すよう定めているので、ここで理由を残す。

## 決定

- `AGENTS.md` を生きた文書に登録する。プロジェクトの現状を常に反映する義務は `AGENTS.md` が負う。
- `CLAUDE.md` は `@AGENTS.md` の1行だけを置く。Claude Code にだけ要る指示が出てきた場合に限り、
  この1行の後に足す。`CLAUDE.md` は引き続き allowlist に残す。

## 検討した代替案と却下理由

- 案A（`CLAUDE.md` を正本のままにする）: Codex などが指示を読めないままになる。
- 案B（`CLAUDE.md` と `AGENTS.md` に同じ内容を置く）: 片方だけ直されて食い違う。手元の別の
  リポジトリで実際に差が出ていた。

## 結果

`livingDocs` は `README.md` / `AGENTS.md` / `CLAUDE.md` / `docs/README.md` の4つになる。
