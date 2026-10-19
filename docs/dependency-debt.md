# Dependency Debt

上げられないメジャー依存の台帳。dependabot / auto-package-update が再提案しても、ここに記録済みのものは「既知の負債」として扱う。
解除条件を満たしたら対応し、この表から削除する。「未マージPR数」ではなくこの表を棚卸し・監査の対象にする。

| Package | Current | Target | Blocker | Type | Revisit condition | Recorded |
|---------|---------|--------|---------|------|-------------------|----------|

<!--
運用メモ:
- 本リポジトリの composer 更新は dependabot ではなく auto-package-update.yml が担当。
  Laravel 13 への依存制約更新済み。以後のメジャー更新は別途検証する。
- dependabot は github-actions のみを対象としているため、composer 側の ignore 追加は不要。
- PHP 8.3.30 の platform 制約下でも Laravel 13.37.0 と Symfony 7.4 系で依存解決可能（2026-10-19 dry-run 確認）。旧 PHP 要件による infra ブロックは解消済み。
- メジャー更新ではアップグレードガイド・実動作を検証し、依存解決成功だけで自動更新しない。
-->
