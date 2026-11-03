import { execSync } from 'child_process';
import { existsSync, mkdirSync, copyFileSync, writeFileSync, readdirSync, rmSync } from 'fs';
import { resolve, dirname, join } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '../..');
const BACKUP_ROOT = resolve(ROOT, '.git', 'jline-backups');
const MAX_BACKUPS = 10;

function runBackup() {
  try {
    const output = execSync('git diff --cached --name-only', { encoding: 'utf-8', cwd: ROOT }).trim();
    if (!output) return;

    const stagedFiles = output.split('\n').map(f => f.trim()).filter(Boolean);
    const criticalFiles = stagedFiles.filter(f => f.startsWith('src/') || f.startsWith('workbench/'));

    if (criticalFiles.length === 0) return;

    let branch = 'unknown';
    try {
      branch = execSync('git rev-parse --abbrev-ref HEAD', { encoding: 'utf-8', cwd: ROOT })
        .trim()
        .replace(/[^a-zA-Z0-9_-]/g, '_');
    } catch { /* ignore */ }

    const now = new Date();
    const pad = (n) => String(n).padStart(2, '0');
    const timestamp = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}_${pad(now.getHours())}-${pad(now.getMinutes())}-${pad(now.getSeconds())}`;
    const snapshotDir = join(BACKUP_ROOT, `${timestamp}_${branch}`);

    mkdirSync(snapshotDir, { recursive: true });

    let backedUpCount = 0;
    for (const relPath of criticalFiles) {
      const fullSrc = resolve(ROOT, relPath);
      if (existsSync(fullSrc)) {
        const fullDest = resolve(snapshotDir, relPath);
        mkdirSync(dirname(fullDest), { recursive: true });
        copyFileSync(fullSrc, fullDest);
        backedUpCount++;
      } else {
        try {
          const headContent = execSync(`git show HEAD:"${relPath}"`, { cwd: ROOT, stdio: ['pipe', 'pipe', 'ignore'] });
          const fullDest = resolve(snapshotDir, relPath);
          mkdirSync(dirname(fullDest), { recursive: true });
          writeFileSync(fullDest, headContent);
          backedUpCount++;
        } catch { /* New file that doesn't exist in HEAD */ }
      }
    }

    if (existsSync(BACKUP_ROOT)) {
      const entries = readdirSync(BACKUP_ROOT, { withFileTypes: true })
        .filter(d => d.isDirectory())
        .map(d => d.name)
        .sort()
        .reverse();

      if (entries.length > MAX_BACKUPS) {
        for (const oldDir of entries.slice(MAX_BACKUPS)) {
          rmSync(join(BACKUP_ROOT, oldDir), { recursive: true, force: true });
        }
      }
    }

    if (backedUpCount > 0) {
      console.log(`🛡️ [Auto-Backup] Snapshotted ${backedUpCount} file(s) to .git/jline-backups/${timestamp}_${branch}`);
    }
  } catch {
    // Non-blocking backup helper
  }
}

runBackup();

