/**
 * sync-snippets.js
 * Universal FLOCSS Component Snippet Engine for WordPress
 * Dynamically scans any .php file in src/includes/components/
 * and generates accurate VS Code snippets for any c-* and l-* class.
 * Zero hardcoded file names, zero hardcoded class names.
 */

import { readFileSync, writeFileSync, existsSync, readdirSync, mkdirSync, statSync } from 'fs';
import { resolve, dirname, basename } from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);
const ROOT = resolve(__dirname, '..');
const COMPONENTS_DIR = resolve(ROOT, 'src/includes/components');
const SNIPPETS_FILE = resolve(ROOT, '.vscode/jline-components.code-snippets');

function toTitleCase(str) {
  if (!str) return '';
  return str.split(/[-_]+/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
}

const VOID_TAGS = new Set(['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr']);

/**
 * Accurately extracts full HTML block taking nested closing tags into account
 */
function extractBalancedTag(html, startPos) {
  const openTagMatch = html.slice(startPos).match(/^<([a-zA-Z0-9]+)\b((?:[^'">]|"[^"]*"|'[^']*')*)>/);
  if (!openTagMatch) return null;
  const tagName = openTagMatch[1].toLowerCase();
  
  // Return immediately for self-closing or void elements
  if (VOID_TAGS.has(tagName) || openTagMatch[0].endsWith('/>')) {
    return openTagMatch[0];
  }

  let depth = 0;
  const tagRegex = new RegExp(`<(/?)(${tagName})\\b(?:[^'">]|"[^"]*"|'[^']*')*>`, 'gi');
  tagRegex.lastIndex = startPos;
  let match;
  while ((match = tagRegex.exec(html)) !== null) {
    if (match[1] === '/') {
      depth--;
      if (depth === 0) {
        return html.slice(startPos, tagRegex.lastIndex);
      }
    } else {
      depth++;
    }
  }
  return null;
}

/**
 * Universal component extractor:
 * Finds all top-level FLOCSS components (c-* or l-*) in any HTML/PHP string
 */
function extractFlocssComponents(html, fileName) {
  const components = [];
  const tagRegex = /<([a-zA-Z0-9]+)\b((?:[^'">]|"[^"]*"|'[^']*')*)>/gi;
  let match;
  const extractedRanges = [];

  while ((match = tagRegex.exec(html)) !== null) {
    const startIndex = match.index;
    const tagName = match[1].toLowerCase();
    const attrString = match[2];

    // Skip if inside an already extracted component block
    if (extractedRanges.some(r => startIndex >= r.start && startIndex < r.end)) {
      continue;
    }

    const classMatch = attrString.match(/\bclass=["']([^"']+)["']/i);
    if (!classMatch) continue;

    const classStr = classMatch[1].trim();
    const classList = classStr.split(/\s+/).filter(Boolean);

    // FLOCSS Root tokens: starts with c- or l-, not wrapper classes or BEM elements
    const flocssClasses = classList.filter(c =>
      /^[cl]-[a-zA-Z0-9_-]+/.test(c) &&
      c !== 'l-container' &&
      !c.startsWith('p-component') &&
      !c.includes('__')
    );

    if (flocssClasses.length === 0) continue;

    const fullHtml = extractBalancedTag(html, startIndex);
    if (!fullHtml) continue;

    const endIndex = startIndex + fullHtml.length;
    extractedRanges.push({ start: startIndex, end: endIndex });

    // Look for HTML comment immediately preceding startIndex (or before its wrapper tag)
    const preceding = html.slice(0, startIndex);
    let commentTitle = null;
    const lastCommentEnd = preceding.lastIndexOf('-->');
    if (lastCommentEnd !== -1) {
      const afterComment = preceding.slice(lastCommentEnd + 3);
      if (/^\s*$/.test(afterComment) || /^\s*<[a-zA-Z0-9_-]+\b[^>]*>\s*$/.test(afterComment)) {
        const lastCommentStart = preceding.lastIndexOf('<!--', lastCommentEnd);
        if (lastCommentStart !== -1) {
          let text = preceding.slice(lastCommentStart + 4, lastCommentEnd).trim();
          text = text.replace(/^[\s*#-]+/, '').replace(/[\s*#-]+$/, '').trim();
          if (text && !text.startsWith('[') && !text.startsWith('vite') && !text.startsWith('webpack')) {
            commentTitle = text;
          }
        }
      }
    }

    components.push({
      tagName,
      classStr,
      classList,
      flocssClasses,
      mainClass: flocssClasses[0],
      rawHtml: fullHtml,
      commentTitle
    });
  }

  return components;
}

/**
 * Generate a clean, user-friendly snippet prefix from class list
 */
function formatSnippetPrefix(classList) {
  const flocss = classList.filter(c =>
    /^[cl]-[a-zA-Z0-9_-]+/.test(c) &&
    c !== 'l-container' &&
    !c.startsWith('p-component')
  );

  if (flocss.length === 0) return 'component';
  if (flocss.length === 1) return flocss[0];

  const base = flocss[0];
  const mods = flocss.slice(1).map(m => m.replace(new RegExp(`^${base}--?`), '').replace(/^--/, ''));
  return `${base}-${mods.join('-')}`;
}

/**
 * Format user-friendly title for snippet label
 */
function formatFriendlyTitle(prefix, classStr) {
  let clean = prefix.replace(/^[cl]-/, '');
  if (classStr.includes('--cl3')) return 'Grid (3 Columns)';
  if (classStr.includes('--cl4')) return 'Grid (4 Columns)';
  if (classStr.includes('--cl5')) return 'Grid (5 Columns)';
  if (classStr.includes('--middle') && classStr.includes('--reverse')) return 'Flex (Reverse + Middle)';
  if (classStr.includes('--reverse')) return classStr.includes('btn') ? 'Button Reverse' : 'Flex Reverse';
  if (classStr.includes('--arrow-between')) return 'Button Arrow Between';
  if (classStr.includes('--arrow-center')) return 'Button Arrow Center';
  if (classStr.includes('--arrow')) return 'Button with Arrow';
  if (classStr.includes('--blank')) return 'Link External (Blank)';
  if (classStr.includes('--line')) return 'Heading Line';
  if (classStr.includes('--dot')) return 'Heading Dot';
  return toTitleCase(clean);
}

/**
 * Normalize base indentation so root element starts at column 0
 * and all nested tags have balanced relative indentation
 */
function normalizeIndentation(html) {
  if (!html) return '';
  const trimmed = html.trim();
  const lines = trimmed.split(/\r?\n/);
  if (lines.length <= 1) return trimmed;

  const lastLine = lines[lines.length - 1];
  const lastMatch = lastLine.match(/^(\s+)</);
  let baseIndent = '';

  if (lastMatch) {
    baseIndent = lastMatch[1];
  } else {
    let minLen = Infinity;
    for (let i = 1; i < lines.length; i++) {
      const line = lines[i];
      if (line.trim().length > 0) {
        const m = line.match(/^(\s*)/);
        const len = m ? m[1].length : 0;
        if (len < minLen) {
          minLen = len;
          baseIndent = m[1];
        }
      }
    }
  }

  if (baseIndent.length > 0) {
    return lines.map((line, idx) => {
      if (idx === 0) return line.trimStart();
      if (line.startsWith(baseIndent)) {
        return line.slice(baseIndent.length);
      }
      return line.trimStart();
    }).join('\n');
  }

  return trimmed;
}

/**
 * Format HTML body into VS Code snippet lines with intelligent tabstops
 */
function formatSnippetBody(rawHtml, tagName, mainClass, classStr) {
  const normalizedHtml = normalizeIndentation(rawHtml);
  // Normalize indent to tabs
  let lines = normalizedHtml.split(/\r?\n/).map(line => line.replace(/^ +/g, match => '\t'.repeat(Math.round(match.length / 4))));

  // 1. Lists (ul/ol): keep 2 items with tabstops for fast authoring
  if (tagName === 'ul' || tagName === 'ol') {
    const liMatches = rawHtml.match(/<li\b[^>]*>[\s\S]*?<\/li>/gi) || [];
    if (liMatches.length > 2) {
      const sampleLi = liMatches[0];
      const itemClsMatch = sampleLi.match(/class=["']([^"']+)["']/i);
      const itemCls = itemClsMatch ? itemClsMatch[1] : `${mainClass}__item`;
      return [
        `<${tagName} class="${classStr}">`,
        `\t<li class="${itemCls}">\${1:リストテキスト}</li>`,
        `\t<li class="${itemCls}">\${2:リストテキスト}</li>`,
        `</${tagName}>`
      ];
    }
  }

  // 2. Links (<a>) & Buttons (<button>): keep href="" and ${1:...} for link text or button content
  if (tagName === 'a' || tagName === 'button') {
    let joined = lines.join('\n');
    joined = joined.replace(/\bhref=["'][^"']*["']/g, 'href=""');
    if (joined.includes('__content')) {
      joined = joined.replace(/(<span\b[^>]*class=["'][^"']*__content[^"']*["'][^>]*>)([^<\n]+)(<\/span>)/g, (m, openSpan, txt, closeSpan) => {
        return `${openSpan}\${1:${txt.trim()}}${closeSpan}`;
      });
    } else {
      joined = joined.replace(/>([^<\n]+)<\/(?:a|button)>/g, (m, txt) => {
        const clean = txt.trim();
        return `>\${1:${clean}}</${tagName}>`;
      });
    }
    return joined.split('\n');
  }

  // 3. Headings with single text child
  if (/^h[1-6]$/.test(tagName) && !rawHtml.includes('<span')) {
    let joined = lines.join('\n');
    joined = joined.replace(/>([^<\n]+)<\/h[1-6]>/g, (m, txt) => {
      const clean = txt.trim();
      return `>\${1:${clean}}</${tagName}>`;
    });
    return joined.split('\n');
  }

  // 4. General components: ensure hrefs default to href=""
  let joined = lines.join('\n');
  joined = joined.replace(/\bhref=["'][^"']*["']/g, 'href=""');
  return joined.split('\n');
}

function isLayoutFile(file, content) {
  const slug = basename(file, '.php').toLowerCase();
  const knownLayoutSlugs = ['flexs', 'grids', 'tbls', 'flex', 'grid', 'table', 'tables', 'layout', 'layouts'];
  if (knownLayoutSlugs.includes(slug) || slug.startsWith('l-')) return true;

  const hasLayoutClass = /\bclass=["'][^"']*\bl-(?!container\b)[a-zA-Z0-9_-]+/i.test(content);
  const hasComponentClass = /\bclass=["'][^"']*\bc-[a-zA-Z0-9_-]+/i.test(content);
  if (hasLayoutClass && !hasComponentClass) return true;
  if (hasLayoutClass && hasComponentClass) {
    const m = content.match(/\bclass=["'][^"']*\b([cl])-(?!container\b)[a-zA-Z0-9_-]+/i);
    if (m && m[1].toLowerCase() === 'l') return true;
  }
  return false;
}

/**
 * Generate Snippets dynamically from all src/includes/components/*.php
 */
export function syncSnippets(options = {}) {
  const { quiet = false } = options;

  if (!existsSync(COMPONENTS_DIR)) {
    if (!quiet) console.warn(`[snippets] Directory not found: ${COMPONENTS_DIR}`);
    return;
  }

  const generated = {};
  const stats = {};

  const rawFiles = readdirSync(COMPONENTS_DIR).filter(f => {
    if (!f.endsWith('.php') || f.startsWith('.') || f.startsWith('~')) return false;
    try {
      return statSync(resolve(COMPONENTS_DIR, f)).isFile();
    } catch {
      return false;
    }
  });

  const fileData = rawFiles.map(file => {
    const filePath = resolve(COMPONENTS_DIR, file);
    const content = readFileSync(filePath, 'utf8');
    const isLayout = isLayoutFile(file, content);
    return { file, filePath, content, isLayout };
  });

  // Xếp Components (c-*) TRÊN Layout & Structure (l-*)
  const compFiles = fileData.filter(f => !f.isLayout).sort((a, b) => a.file.localeCompare(b.file, undefined, { numeric: true, sensitivity: 'base' }));
  const layoutFiles = fileData.filter(f => f.isLayout).sort((a, b) => a.file.localeCompare(b.file, undefined, { numeric: true, sensitivity: 'base' }));
  const sortedFiles = [...compFiles, ...layoutFiles];

  for (const { file, filePath, content } of sortedFiles) {
    const compName = basename(file, '.php').replace(/^_/, '');
    stats[compName] = 0;

    // Extract all FLOCSS components generically
    const components = extractFlocssComponents(content, file);

    for (const comp of components) {
      const prefix = formatSnippetPrefix(comp.classList);
      // Ưu tiên lấy chú thích từ comment code; nếu không có thì lấy tên đặt của component đó để hiển thị
      const friendlyTitle = comp.commentTitle || formatFriendlyTitle(prefix, comp.classStr);
      const label = `${comp.classStr} (${friendlyTitle})`;

      // Skip duplicate entries with identical classes
      if (generated[label]) continue;

      const body = formatSnippetBody(comp.rawHtml, comp.tagName, comp.mainClass, comp.classStr);

      generated[label] = {
        prefix,
        body,
        description: comp.commentTitle
          ? `${comp.commentTitle} - FLOCSS (${comp.classStr})`
          : `FLOCSS (${comp.classStr}) from ${file}`
      };
      stats[compName]++;
    }

    // Enhancement: Multi-size heading picker for c-ttl
    const sizeMatches = Array.from(content.matchAll(/\bc-ttl(\d+)\b/g)).map(m => m[1]);
    const uniqueSizes = Array.from(new Set(sizeMatches)).sort((a, b) => Number(b) - Number(a));
    if (uniqueSizes.length > 1 && !generated['c-ttl (Heading with Selectable Size)']) {
      generated['c-ttl (Heading with Selectable Size)'] = {
        prefix: 'c-ttl',
        body: [
          `<h2 class="c-ttl\${1|${uniqueSizes.join(',')}|}">\${2:タイトルコンテンツ}</h2>`
        ],
        description: 'FLOCSS Heading with selectable font-size'
      };
      stats[compName]++;
    }

    // Enhancement: Multi-size text picker for c-txt
    const txtSizeMatches = Array.from(content.matchAll(/\bc-txt(\d+)\b/g)).map(m => m[1]);
    const uniqueTxtSizes = Array.from(new Set(txtSizeMatches)).sort((a, b) => Number(b) - Number(a));
    if (uniqueTxtSizes.length > 1 && !generated['c-txt (Text with Selectable Size)']) {
      generated['c-txt (Text with Selectable Size)'] = {
        prefix: 'c-txt',
        body: [
          `<p class="c-txt\${1|${uniqueTxtSizes.join(',')}|}">\${2:テキストコンテンツ}</p>`
        ],
        description: 'FLOCSS Text with selectable font-size'
      };
      stats[compName]++;
    }

    // Enhancement: Bilingual EN/JP Heading for c-title
    if (content.includes('c-title') && content.includes('c-title__en') && !generated['c-title (EN/JP Bilingual Heading)']) {
      generated['c-title (EN/JP Bilingual Heading)'] = {
        prefix: 'c-title',
        body: [
          '<h2 class="c-title">',
          '\t<span class="c-title__en">${1:ENGLISH TITLE}</span>',
          '\t<span class="c-title__jp">${2:日本語タイトル}</span>',
          '</h2>'
        ],
        description: 'FLOCSS Bilingual Title EN/JP'
      };
      stats[compName]++;
    }
  }

  // Ensure target folder exists
  const targetDir = dirname(SNIPPETS_FILE);
  if (!existsSync(targetDir)) {
    mkdirSync(targetDir, { recursive: true });
  }

  // Dirty-check: only write to disk if content has actually changed
  const newContent = JSON.stringify(generated, null, 2) + '\n';
  let oldContent = '';
  if (existsSync(SNIPPETS_FILE)) {
    try { oldContent = readFileSync(SNIPPETS_FILE, 'utf8'); } catch {}
  }

  const hasChanged = newContent !== oldContent;
  if (hasChanged) {
    writeFileSync(SNIPPETS_FILE, newContent, 'utf8');
  }

  const total = Object.keys(generated).length;
  if (!quiet) {
    const { compact = false } = options;
    if (compact) {
      if (hasChanged) {
        console.log(`[snippets] ✓ Auto-synced ${total} components -> .vscode/jline-components.code-snippets`);
      } else {
        console.log(`[snippets] ✓ Synced ${total} components (up to date)`);
      }
    } else {
      console.log(`\n✓ Snippets Rewritten & Synced (${total} items in .vscode/jline-components.code-snippets):`);
      for (const [name, count] of Object.entries(stats)) {
        if (count > 0) {
          console.log(`  - ${name}.php: ${count} snippet(s)`);
        }
      }
    }
  }

  return generated;
}

// CLI execution
if (process.argv[1] && resolve(process.argv[1]) === resolve(__filename)) {
  syncSnippets();
}
