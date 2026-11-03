import assert from 'node:assert/strict';
import { existsSync, mkdirSync, mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { join, resolve } from 'node:path';
import test from 'node:test';
import { fileURLToPath } from 'node:url';

import { WORKBENCH_OUT_DIR } from '../scripts/tools/config.js';
import { buildWorkbench, cleanWorkbench } from '../scripts/builders/workbench.js';
import { syncSnippets } from '../scripts/sync-snippets.js';
import {
  getRegistry,
  installComponent,
  installVariant,
  isVariantInstalled,
  removeComponent,
  saveComponent,
  deleteWorkbenchVariant,
  deleteAllWorkbenchComponents,
  listSnapshotBackups,
  sortComponentRegistry
} from '../scripts/tools/component-service.js';
import {
  isEmptyComponentMarkup,
  parseComponentHtml,
  extractTagBlockFromFile,
  exportSelectionToWorkbench
} from '../scripts/tools/selection-exporter.js';
import { createApiMiddleware } from '../scripts/tools/api-middleware.js';
import { getDevSessionToken } from '../scripts/tools/safety.js';

const root = fileURLToPath(new URL('..', import.meta.url));

function createSandboxPaths() {
  const tempRoot = mkdtempSync(join(root, '.wb-test-'));
  const paths = {
    root: tempRoot,
    wbDir: join(tempRoot, 'workbench'),
    wbComponentsDir: join(tempRoot, 'workbench/components'),
    wbScssDir: join(tempRoot, 'workbench/scss/component'),
    wbLayoutDir: join(tempRoot, 'workbench/scss/layout'),
    wbJsDir: join(tempRoot, 'workbench/js'),
    clientComponentsDir: join(tempRoot, 'src/includes/components'),
    clientScssDir: join(tempRoot, 'src/assets/scss/component'),
    clientLayoutDir: join(tempRoot, 'src/assets/scss/layout'),
    clientJsDir: join(tempRoot, 'src/assets/js/component')
  };

  for (const dir of Object.values(paths)) {
    mkdirSync(dir, { recursive: true });
  }
  mkdirSync(join(tempRoot, 'src/assets/js'), { recursive: true });
  writeFileSync(join(paths.wbScssDir, '_index.scss'), '@forward "../layout";\n', 'utf8');
  writeFileSync(join(paths.wbLayoutDir, '_index.scss'), '@forward "container";\n', 'utf8');
  writeFileSync(join(paths.clientScssDir, '_index.scss'), '', 'utf8');
  writeFileSync(join(paths.clientLayoutDir, '_index.scss'), '@use "container";\n@use "girds";\n', 'utf8');
  writeFileSync(join(tempRoot, 'src/assets/js/common.js'), '// Base common.js\n', 'utf8');

  return {
    tempRoot,
    paths,
    cleanup() {
      rmSync(tempRoot, { recursive: true, force: true });
    }
  };
}

test('Workbench builds Showroom HTML, SCSS, Inview Showroom, and cleans up for production', async (t) => {
  t.after(() => {
    cleanWorkbench();
  });

  cleanWorkbench();
  assert.equal(existsSync(WORKBENCH_OUT_DIR), false);

  await buildWorkbench({ force: true, port: 6868 });

  const indexHtmlPath = resolve(WORKBENCH_OUT_DIR, 'index.html');
  const inviewHtmlPath = resolve(WORKBENCH_OUT_DIR, 'inview.html');
  const wbCssPath = resolve(WORKBENCH_OUT_DIR, 'workbench.css');
  const inviewCssPath = resolve(WORKBENCH_OUT_DIR, 'inview.css');
  const catalogCssPath = resolve(WORKBENCH_OUT_DIR, 'catalog.css');
  const catalogJsPath = resolve(WORKBENCH_OUT_DIR, 'catalog.js');

  assert.equal(existsSync(indexHtmlPath), true, 'index.html should be generated');
  assert.equal(existsSync(inviewHtmlPath), true, 'inview.html should be generated');
  assert.equal(existsSync(wbCssPath), true, 'workbench.css should be compiled');
  assert.equal(existsSync(inviewCssPath), true, 'inview.css should be compiled');
  assert.equal(existsSync(catalogCssPath), true, 'catalog.css should be copied');
  assert.equal(existsSync(catalogJsPath), true, 'catalog.js should be copied');

  const html = readFileSync(indexHtmlPath, 'utf8');
  const wbCss = readFileSync(wbCssPath, 'utf8');
  const inviewCss = readFileSync(inviewCssPath, 'utf8');

  assert.match(html, /<!DOCTYPE html>/i);
  assert.match(html, /class="cs-sidebar"/);
  assert.match(html, /id="cs-filter-input"/);
  assert.match(html, /fonts\.googleapis\.com/, 'Should inherit Google Fonts from src/header.php');
  assert.match(wbCss, /\.l-container/);
  assert.match(inviewCss, /\.c-inview/);

  const snippets = syncSnippets({ quiet: true });
  assert.equal(typeof snippets, 'object');
  if (getRegistry().length === 0) {
    assert.equal(Object.keys(snippets).length, 0, 'When workbench/components is empty, jline-components.code-snippets must be empty {}');
  } else {
    assert.ok(Object.keys(snippets).length > 0);
  }

  cleanWorkbench();
  assert.equal(existsSync(WORKBENCH_OUT_DIR), false, 'cleanWorkbench() must remove __workbench completely');
});

test('Component Engine installs, slices variants, detects conflicts, and removes components in WP structure', () => {
  const { tempRoot, paths, cleanup } = createSandboxPaths();
  try {
    writeFileSync(
      join(paths.wbComponentsDir, '_btn.ejs'),
      `<div class="p-component__item">\n    <!-- Primary Button -->\n    <a href="" class="c-btn">Click</a>\n</div>\n<div class="p-component__item">\n    <!-- Arrow Button -->\n    <a href="" class="c-btn c-btn--arrow">More</a>\n</div>\n`,
      'utf8'
    );
    writeFileSync(
      join(paths.wbScssDir, '_btn.scss'),
      `@use "../global" as *;\n.c-btn {\n  display: inline-flex;\n  color: #fff;\n  &--arrow {\n    padding-right: 20px;\n  }\n  &--reverse {\n    background: #000;\n  }\n}\n`,
      'utf8'
    );
    writeFileSync(
      join(paths.wbJsDir, 'common.js'),
      `/* ==========================================================================\n   [Component: btn]\n   ========================================================================== */\nconsole.log('btn-init');\n`,
      'utf8'
    );

    const regBefore = getRegistry(paths);
    assert.equal(regBefore.length, 1);
    assert.equal(regBefore[0].name, 'btn');
    assert.equal(regBefore[0].isInstalled, false);

    // 1. Install single variant (c-btn c-btn--arrow)
    const varRes = installVariant(
      'btn',
      { classStr: 'c-btn c-btn--arrow', targetJsFile: 'common.js' },
      paths
    );
    assert.equal(varRes.success, true);

    const clientBtnScss = readFileSync(join(paths.clientScssDir, '_btn.scss'), 'utf8');
    assert.match(clientBtnScss, /\.c-btn/);
    assert.match(clientBtnScss, /&--arrow/);
    assert.doesNotMatch(clientBtnScss, /&--reverse/, 'Unselected modifier should not be installed by variant import');

    assert.equal(isVariantInstalled('btn', 'c-btn c-btn--arrow', paths), true);
    assert.equal(isVariantInstalled('btn', 'c-btn c-btn--reverse', paths), false);

    // 2. Full component install into WP paths (src/assets/scss/component & src/assets/js/common.js)
    const fullRes = installComponent('btn', { force: true, targetJsFile: 'common.js' }, paths);
    assert.equal(fullRes.success, true);
    assert.ok(fullRes.files.includes('src/assets/scss/component/_btn.scss'));
    assert.ok(fullRes.files.includes('src/assets/js/common.js'));

    const fullClientBtnScss = readFileSync(join(paths.clientScssDir, '_btn.scss'), 'utf8');
    assert.match(fullClientBtnScss, /&--reverse/);

    const clientCommonJs = readFileSync(join(tempRoot, 'src/assets/js/common.js'), 'utf8');
    assert.match(clientCommonJs, /\[Component: btn\]/);
    assert.match(clientCommonJs, /btn-init/);

    // 3. Conflict detection when developer customizes client SCSS
    writeFileSync(
      join(paths.clientScssDir, '_btn.scss'),
      fullClientBtnScss.replace('color: #fff;', 'color: #123456;'),
      'utf8'
    );
    const conflictInstall = installComponent('btn', { force: false }, paths);
    assert.equal(conflictInstall.success, false);
    assert.equal(conflictInstall.conflict, true);

    // 4. Delete a single variant card from Workbench
    const delVarRes = deleteWorkbenchVariant({ component: 'btn', cardIndex: 1 }, paths);
    assert.equal(delVarRes.success, true);
    const remainingWbEjs = readFileSync(join(paths.wbComponentsDir, '_btn.ejs'), 'utf8');
    assert.doesNotMatch(remainingWbEjs, /c-btn--arrow/);
    assert.match(remainingWbEjs, /Primary Button/);

    // 5. Remove component from client site (with force since we customized it)
    const rmRes = removeComponent('btn', { force: true }, paths);
    assert.equal(rmRes.success, true);
    assert.equal(existsSync(join(paths.clientScssDir, '_btn.scss')), false);

    // 6. Backups & Semantic Ordering & Clear Workbench
    const backups = listSnapshotBackups('btn', paths);
    assert.ok(backups.length > 0, 'Snapshots should be recorded in workbench/.backup');

    const sorted = sortComponentRegistry([
      { name: 'footer', category: 'footer' },
      { name: 'btn', category: 'component' },
      { name: 'header', category: 'header' }
    ]);
    assert.deepEqual(sorted.map((s) => s.name), ['header', 'btn', 'footer']);

    const clearRes = deleteAllWorkbenchComponents({}, paths);
    assert.equal(clearRes.success, true);
    assert.equal(getRegistry(paths).length, 0);
  } finally {
    cleanup();
  }
});

test('Selection Exporter & Snippets reject empty components and only sync non-empty Workbench components', async () => {
  const phpHtml = `<div class="l-grid l-grid--cl3">\n  <div class="l-grid__item">\n    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/img_dummy.webp" alt="">\n  </div>\n</div>`;
  const parsed = parseComponentHtml(phpHtml);
  assert.ok(parsed);
  assert.equal(parsed.compName, 'grids');
  assert.equal(parsed.category, 'layout');
  assert.ok(parsed.allClasses.includes('l-grid'));
  assert.ok(parsed.allClasses.includes('l-grid--cl3'));

  // Empty component shells must be detected and rejected
  assert.equal(isEmptyComponentMarkup('<header class="c-header"></header>'), true);
  assert.equal(isEmptyComponentMarkup('<div class="c-btn">   <!-- empty -->   </div>'), true);
  assert.equal(isEmptyComponentMarkup('<a href="" class="c-btn">Click</a>'), false);
  assert.equal(parseComponentHtml('<header class="c-header"></header>'), null);
  assert.equal(parseComponentHtml('<div class="c-btn"></div>'), null);

  const emptyExportRes = await exportSelectionToWorkbench({
    html: '<header class="c-header">\n</header>'
  });
  assert.equal(emptyExportRes.success, false, 'Empty component must not be added to Workbench');

  // Sandbox snippet & save test with empty vs non-empty components
  const { tempRoot, paths, cleanup } = createSandboxPaths();
  try {
    const tempSnippetsFile = join(tempRoot, 'jline-components.code-snippets');

    // 1. Empty wbComponentsDir -> {}
    const emptySnippets = syncSnippets({
      quiet: true,
      wbComponentsDir: paths.wbComponentsDir,
      snippetsFile: tempSnippetsFile
    });
    assert.deepEqual(emptySnippets, {});
    assert.equal(readFileSync(tempSnippetsFile, 'utf8').trim(), '{}');

    // 2. Empty component file in wbComponentsDir -> ignored by both getRegistry and syncSnippets
    writeFileSync(
      join(paths.wbComponentsDir, '_header.ejs'),
      `<div class="p-component__item">\n  <header class="c-header"></header>\n</div>\n`,
      'utf8'
    );
    assert.equal(getRegistry(paths).length, 0, 'Empty component in wbComponentsDir must not appear in registry');
    const afterEmptyFileSnippets = syncSnippets({
      quiet: true,
      wbComponentsDir: paths.wbComponentsDir,
      snippetsFile: tempSnippetsFile
    });
    assert.deepEqual(afterEmptyFileSnippets, {});

    // 3. Valid component in wbComponentsDir -> added to snippets, while empty one is still skipped
    writeFileSync(
      join(paths.wbComponentsDir, '_btn.ejs'),
      `<div class="p-component__item">\n  <a href="" class="c-btn"> Button </a>\n  <div class="c-empty"></div>\n</div>\n`,
      'utf8'
    );
    const validSnippets = syncSnippets({
      quiet: true,
      wbComponentsDir: paths.wbComponentsDir,
      snippetsFile: tempSnippetsFile
    });
    assert.equal(Object.keys(validSnippets).length, 1);
    assert.ok(Object.keys(validSnippets)[0].startsWith('c-btn '));

    // 4. saveComponent rejects empty client component
    writeFileSync(join(paths.clientComponentsDir, 'empty.php'), '<div class="c-empty"></div>', 'utf8');
    const saveEmptyRes = saveComponent('empty', {}, paths);
    assert.equal(saveEmptyRes.success, false, 'saveComponent must reject empty component');
  } finally {
    cleanup();
  }

  const extractedHeader = extractTagBlockFromFile('src/header.php', 36);
  assert.ok(extractedHeader, 'Should extract <header class="c-header"> from src/header.php');
  assert.match(extractedHeader, /^<header class="c-header">/);
  assert.match(extractedHeader, /<\/header>$/);
});

test('API Middleware enforces localhost, CSRF token, and serves registry & js-files', async () => {
  const middleware = createApiMiddleware();

  function invoke(method, url, headers = {}, remoteAddress = '127.0.0.1') {
    return new Promise((resolvePromise) => {
      const req = {
        method,
        url,
        headers: { host: 'localhost:6868', ...headers },
        socket: { remoteAddress },
        on(event, cb) {
          if (event === 'end') cb();
        }
      };
      const res = {
        statusCode: 200,
        headers: {},
        writeHead(status, hdrs = {}) {
          this.statusCode = status;
          Object.assign(this.headers, hdrs);
        },
        setHeader(k, v) {
          this.headers[k.toLowerCase()] = v;
        },
        end(body) {
          resolvePromise({
            statusCode: this.statusCode,
            body: body ? JSON.parse(body) : null
          });
        }
      };
      middleware(req, res, () => resolvePromise({ next: true }));
    });
  }

  // 1. Non-localhost Host/Origin should be blocked (403)
  const externalRes = await invoke('GET', '/__api/registry', { host: 'evil.example.com' }, '192.168.1.50');
  assert.equal(externalRes.statusCode, 403);

  // 2. GET /__api/registry should succeed (200)
  const regRes = await invoke('GET', '/__api/registry');
  assert.equal(regRes.statusCode, 200);
  assert.equal(regRes.body.success, true);
  assert.ok(Array.isArray(regRes.body.components));

  // 3. GET /__api/js-files should return JS files from src/assets/js/
  const jsRes = await invoke('GET', '/__api/js-files');
  assert.equal(jsRes.statusCode, 200);
  assert.equal(jsRes.body.success, true);
  assert.ok(jsRes.body.files.some((f) => f.name === 'common.js'));

  // 4. POST without X-Workbench-Token should be blocked (401)
  const unauthPost = await invoke('POST', '/__api/import');
  assert.equal(unauthPost.statusCode, 401);

  // 5. POST with valid token but invalid traversal component name should be blocked (400)
  const token = getDevSessionToken();
  const badNamePost = await invoke(
    'POST',
    '/__api/import?component=../../etc/passwd',
    { 'x-workbench-token': token }
  );
  assert.equal(badNamePost.statusCode, 400);
});
