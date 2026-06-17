# Dependency Debt

上げられないメジャー依存の台帳。dependabot / auto-package-update が再提案しても、ここに記録済みのものは「既知の負債」として扱う。
解除条件を満たしたら対応し、この表から削除する。「未マージPR数」ではなくこの表を棚卸し・監査の対象にする。

| Package | Current | Target | Blocker | Type | Revisit condition | Recorded |
|---------|---------|--------|---------|------|-------------------|----------|
| laravel/framework | 12.61 | 13.x | prod が PHP 8.3。Laravel 13.3+ は Symfony 8 を引き込み PHP 8.4.1+ が必須 | infra | prod を PHP 8.4+ に更新する | 2026-06-17 |

<!--
運用メモ:
- 本リポジトリの composer 更新は dependabot ではなく auto-package-update.yml が担当。
  composer.json が laravel/framework を ^12 に固定しているため 13 のPRは生成されない（これが実質の歯止め）。
- dependabot は github-actions のみを対象としているため、composer 側の ignore 追加は不要。
- prod PHP を 8.4+ にできたら、composer.json の制約を ^13 に上げて Laravel 13 移行を実施する。
-->
