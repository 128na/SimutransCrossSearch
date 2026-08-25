#!/usr/bin/env node
// docs-lint: ドキュメント規約（docs/README.md）の機械検証。依存ゼロ・Node 標準モジュールのみ。
// 使い方: node tools/docs-lint.mjs   （リポジトリルートで実行。exit 0 = green / 1 = error あり）
import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";

const toolsDir = path.dirname(fileURLToPath(import.meta.url));
const repoRoot = path.dirname(toolsDir);
const policy = JSON.parse(fs.readFileSync(path.join(toolsDir, "docs-policy.json"), "utf8"));

const errors = [];
const warns = [];
const rel = (p) => path.relative(repoRoot, p).replaceAll("\\", "/");
const error = (file, msg) => errors.push(`ERROR ${rel(file)}: ${msg}`);
const warn = (file, msg) => warns.push(`WARN  ${rel(file)}: ${msg}`);

// ---- 走査 -------------------------------------------------------------
const SKIP_DIRS = new Set([".git", "node_modules", ".github", ".claude", ".idea", ".vscode"]);
const mdFiles = []; // { abs, relPath, kind }

function walk(dir) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const abs = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      if (SKIP_DIRS.has(entry.name)) continue;
      if (policy.forbiddenDirNames.includes(entry.name.toLowerCase())) {
        error(abs, `禁止されたディレクトリ名です（${entry.name}）。下書きは PR 説明や scratchpad へ、残す価値があるなら docs/records/ に日付付きで置く`);
      }
      walk(abs);
    } else if (entry.name.endsWith(".md")) {
      mdFiles.push({ abs, relPath: rel(abs) });
    }
  }
}
for (const root of policy.scanRoots.recursive) {
  const abs = path.join(repoRoot, root);
  if (fs.existsSync(abs)) walk(abs);
}
for (const root of policy.scanRoots.flat) {
  const abs = path.join(repoRoot, root);
  for (const entry of fs.readdirSync(abs, { withFileTypes: true })) {
    if (entry.isFile() && entry.name.endsWith(".md")) {
      mdFiles.push({ abs: path.join(abs, entry.name), relPath: rel(path.join(abs, entry.name)) });
    }
  }
}

// ---- 分類（allowlist 検査） ------------------------------------------
const inDir = (relPath, dir) => relPath.startsWith(dir.replaceAll("\\", "/") + "/");
for (const f of mdFiles) {
  if (policy.forbiddenFileNames.includes(path.basename(f.relPath))) {
    error(f.abs, `禁止されたファイル名です。手動索引や TODO.md は持たない（一覧は ls docs/records/、タスクは台帳か issue へ）`);
    f.kind = "forbidden";
  } else if (policy.livingDocs.includes(f.relPath)) f.kind = "living";
  else if (policy.ledgers.includes(f.relPath)) f.kind = "ledger";
  else if (policy.recordDirs.some((d) => inDir(f.relPath, d))) f.kind = "record";
  else if (inDir(f.relPath, policy.adrDir)) f.kind = "adr";
  else if (inDir(f.relPath, policy.templateDir)) f.kind = "template";
  else {
    f.kind = "unclassified";
    error(f.abs, `分類できない md です。records/ADR/台帳のいずれかに置くか、生きた文書として tools/docs-policy.json の livingDocs に登録（+理由を ADR 化）する`);
  }
}

// ---- records 命名 -----------------------------------------------------
const RECORD_NAME = /^(\d{4})-(\d{2})-(\d{2})_[a-z0-9-]+\.md$/;
const now = new Date();
const today = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}`;
// ローカル日付で判定する（UTCだとJST等UTC+では日付が変わる前0〜9時台に today 扱いの記録が「未来」誤判定される）
for (const f of mdFiles.filter((x) => x.kind === "record")) {
  const m = path.basename(f.relPath).match(RECORD_NAME);
  if (!m) {
    error(f.abs, "records の命名は YYYY-MM-DD_slug.md（slug は小文字英数とハイフン）");
    continue;
  }
  const [, y, mo, d] = m;
  const dt = new Date(`${y}-${mo}-${d}T00:00:00Z`);
  if (Number.isNaN(dt.getTime()) || dt.toISOString().slice(0, 10) !== `${y}-${mo}-${d}`) {
    error(f.abs, `実在しない日付です（${y}-${mo}-${d}）`);
  } else if (`${y}-${mo}-${d}` > today) {
    error(f.abs, `未来の日付です（${y}-${mo}-${d}）`);
  }
}

// ---- ADR 命名 + ステータス行 -----------------------------------------
const ADR_NAME = /^(\d{4})-[a-z0-9-]+\.md$/;
const adrNumbers = new Map();
for (const f of mdFiles.filter((x) => x.kind === "adr")) {
  const m = path.basename(f.relPath).match(ADR_NAME);
  if (!m) {
    error(f.abs, "ADR の命名は NNNN-slug.md（連番4桁 + 小文字英数とハイフン）");
    continue;
  }
  if (adrNumbers.has(m[1])) {
    error(f.abs, `ADR 番号 ${m[1]} が重複しています（${adrNumbers.get(m[1])}）`);
  } else {
    adrNumbers.set(m[1], f.relPath);
  }
  const body = fs.readFileSync(f.abs, "utf8");
  if (!/^> ステータス: /m.test(body)) {
    error(f.abs, "「> ステータス: Accepted (YYYY-MM-DD)」形式のステータス行が必要です");
  }
}

// ---- リンク検査 -------------------------------------------------------
const LINK = /\[[^\]]*\]\(([^)\s]+)(?:\s+"[^"]*")?\)/g;
for (const f of mdFiles) {
  if (f.kind === "template" || f.kind === "forbidden") continue;
  const body = fs.readFileSync(f.abs, "utf8");
  for (const m of body.matchAll(LINK)) {
    const target = m[1];
    if (/^(https?|mailto):/.test(target) || target.startsWith("#")) continue;
    if (target.startsWith("~") || /^[A-Za-z]:[\\/]/.test(target) || target.startsWith("file://") || target.startsWith("/")) {
      error(f.abs, `リポジトリ外への絶対パスリンクは禁止（${target}）。リポジトリ内の相対リンクにするか、経緯なら records に書く`);
      continue;
    }
    const resolved = path.resolve(path.dirname(f.abs), target.split("#")[0]);
    if (!fs.existsSync(resolved)) {
      error(f.abs, `リンク切れ: ${target}`);
    }
  }
}

// ---- 台帳スキーマ -----------------------------------------------------
const depDebt = path.join(repoRoot, "docs", "dependency-debt.md");
if (fs.existsSync(depDebt)) {
  const lines = fs.readFileSync(depDebt, "utf8").split(/\r?\n/);
  const headerIdx = lines.findIndex((l) => l.trim() === policy.dependencyDebtHeader);
  if (headerIdx === -1) {
    error(depDebt, `ヘッダ行が規定と一致しません。/dependabot-maintenance 互換のため次を維持: ${policy.dependencyDebtHeader}`);
  } else {
    for (let i = headerIdx + 2; i < lines.length; i++) {
      const line = lines[i].trim();
      if (!line.startsWith("|")) break;
      const cells = line.split("|").map((c) => c.trim());
      const type = cells[5];
      if (type && !policy.dependencyDebtTypes.includes(type)) {
        error(depDebt, `L${i + 1}: Type「${type}」は不正。許可値: ${policy.dependencyDebtTypes.join(" / ")}`);
      }
    }
  }
}
const consDebt = path.join(repoRoot, "docs", "consistency-debt.md");
if (fs.existsSync(consDebt)) {
  const seen = new Map();
  const lines = fs.readFileSync(consDebt, "utf8").split(/\r?\n/);
  lines.forEach((line, i) => {
    const m = line.match(/^\|\s*(CD-\d+)\s*\|/);
    if (!m) return;
    if (seen.has(m[1])) error(consDebt, `L${i + 1}: ${m[1]} が重複しています（初出 L${seen.get(m[1])}）`);
    else seen.set(m[1], i + 1);
  });
}

// ---- 生きた文書のパス参照検査（warn） --------------------------------
const PATHLIKE = /`((?:docs|tools|src|scripts|tests)\/[^`\s]+)`/g;
for (const f of mdFiles.filter((x) => x.kind === "living")) {
  const body = fs.readFileSync(f.abs, "utf8");
  for (const m of body.matchAll(PATHLIKE)) {
    const token = m[1];
    if (/[*{}<>()?$]|YYYY|NNNN|\.\.\./.test(token)) continue; // プレースホルダはスキップ
    if (!fs.existsSync(path.join(repoRoot, token))) {
      warn(f.abs, `参照パスが見つかりません: ${token}（リネーム未追随の可能性）`);
    }
  }
}

// ---- テンプレマーカー残存（warn） ------------------------------------
for (const f of mdFiles) {
  if (f.kind === "template") continue;
  const body = fs.readFileSync(f.abs, "utf8");
  const count = (body.match(/TODO\(template\):/g) || []).length;
  if (count > 0) warn(f.abs, `TODO(template) マーカーが ${count} 件残っています`);
}

// ---- 出力 -------------------------------------------------------------
for (const line of errors) console.error(line);
for (const line of warns) console.log(line);
console.log(`docs-lint: ${mdFiles.length} files scanned, ${errors.length} error(s), ${warns.length} warning(s)`);
process.exit(errors.length > 0 ? 1 : 0);
