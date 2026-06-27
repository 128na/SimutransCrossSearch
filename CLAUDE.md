# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Run all checks (dump-autoload + ide-helper + rector + phpstan + pint)
composer run all

composer run pint        # Laravel Pint (format)
composer run pint:check  # Pint dry-run
composer run stan        # PHPStan
composer run rector      # Rector (automated refactoring, dry-run by default)
php artisan test         # PHPUnit
```

## グローバル規約

`~/.claude/CLAUDE.md` のポリシーに従う。パッケージ更新は `/repo-maintenance`、Dependabot PR 整理は `/dependabot-maintenance`、複数エージェント作業は `/orchestrate` スキルを使う。
