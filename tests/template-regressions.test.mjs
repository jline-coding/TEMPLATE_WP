import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import http from 'node:http';
import { spawn, spawnSync } from 'node:child_process';
import { fileURLToPath, pathToFileURL } from 'node:url';
import sharp from 'sharp';
import AdmZip from 'adm-zip';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const php = process.env.PHP_BINARY || 'php';
const hasPhp = spawnSync(php, ['-n', '-v'], { encoding: 'utf8' }).status === 0;
sharp.cache(false); // Release image handles before watch tests overwrite files on Windows.

function fixture(t) {
  const dir = fs.mkdtempSync(path.join(root, '.template-test-'));
  const cleanup = [];
  t.after(async () => {
    for (const fn of cleanup.reverse()) await fn();
    assert.equal(path.dirname(dir), root);
    assert(path.basename(dir).startsWith('.template-test-'));
    fs.rmSync(dir, { recursive: true, force: true, maxRetries: 3, retryDelay: 100 });
  });
  fs.cpSync(path.join(root, 'scripts'), path.join(dir, 'scripts'), { recursive: true });
  fs.mkdirSync(path.join(dir, 'src/assets/scss'), { recursive: true });
  fs.mkdirSync(path.join(dir, 'public'), { recursive: true });
  fs.writeFileSync(path.join(dir, 'package.json'), '{"type":"module"}');
  const config = { source_folder: 'public', theme_name: 'test-theme', project_dir: 'test-project', folder_wp: false };
  const configure = (values = {}) => {
    Object.assign(config, values);
    fs.writeFileSync(path.join(dir, 'deploy-config.json'), JSON.stringify(config));
  };
  configure();
  const run = (file, args = []) => spawnSync(process.execPath, [path.join(dir, 'scripts', file), ...args], {
    cwd: dir, encoding: 'utf8', timeout: 30000,
    env: { ...process.env, DEPLOY_ENV: '' },
    maxBuffer: 8 * 1024 * 1024,
  });
  return { dir, cleanup, configure, run, theme: path.join(dir, 'public/wp-content/themes/test-theme') };
}

test('production builds valid SCSS, fails invalid SCSS, and clean preserves WordPress', (t) => {
  const f = fixture(t);
  const scss = path.join(f.dir, 'src/assets/scss/probe.scss');
  fs.writeFileSync(scss, '.probe { color: red; }');
  fs.writeFileSync(path.join(f.dir, 'public/wp-core-sentinel.php'), '<?php // keep');
  let result = f.run('build.js');
  assert.equal(result.status, 0, result.stdout + result.stderr);
  assert.match(fs.readFileSync(path.join(f.theme, 'assets/css/probe.css'), 'utf8'), /\.probe/);
  fs.writeFileSync(scss, '.broken { color: ; broken @@@ }');
  result = f.run('build.js');
  assert.equal(result.status, 1, result.stdout + result.stderr);
  assert.match(result.stderr, /Error compiling/);
  assert.doesNotMatch(result.stdout, /Build complete/);
  assert.equal(f.run('clean.js').status, 0);
  assert(!fs.existsSync(f.theme));
  assert(fs.existsSync(path.join(f.dir, 'public/wp-core-sentinel.php')));
});

test('link preserves real directories and files, and safely creates/replaces links', (t) => {
  const f = fixture(t);
  const www = path.join(f.dir, 'www');
  const link = path.join(www, 'test-project');
  fs.mkdirSync(link, { recursive: true });
  fs.writeFileSync(path.join(link, 'keep.txt'), 'user data');
  const args = ['--www=' + www];
  assert.equal(f.run('link.js', args).status, 1);
  assert.equal(fs.readFileSync(path.join(link, 'keep.txt'), 'utf8'), 'user data');
  fs.unlinkSync(path.join(link, 'keep.txt'));
  fs.rmdirSync(link);
  fs.writeFileSync(link, 'user file');
  assert.equal(f.run('link.js', args).status, 1);
  assert.equal(fs.readFileSync(link, 'utf8'), 'user file');
  fs.unlinkSync(link);
  assert.equal(f.run('link.js', args).status, 0);
  assert.equal(fs.realpathSync(link), fs.realpathSync(path.join(f.dir, 'public')));
  assert.equal(f.run('link.js', args).status, 0, 'rerunning an existing correct link must succeed');
  fs.unlinkSync(link);
  const oldTarget = path.join(f.dir, 'old-target');
  fs.mkdirSync(oldTarget);
  fs.writeFileSync(path.join(oldTarget, 'keep.txt'), 'keep old target');
  fs.symlinkSync(oldTarget, link, process.platform === 'win32' ? 'junction' : 'dir');
  assert.equal(f.run('link.js', args).status, 0);
  assert.equal(fs.readFileSync(path.join(oldTarget, 'keep.txt'), 'utf8'), 'keep old target');
  fs.unlinkSync(link);
  fs.symlinkSync(path.join(f.dir, 'missing-target'), link, process.platform === 'win32' ? 'junction' : 'dir');
  assert.equal(f.run('link.js', args).status, 0, 'dangling links may be replaced');
  f.configure({ project_dir: '../outside' });
  assert.equal(f.run('link.js', args).status, 1);
});

test('Dropbox accepts generated keys and rejects traversal, arrays and symlink escapes', { skip: !hasPhp }, (t) => {
  const f = fixture(t);
  const endpointDir = path.join(f.dir, 'a/b/c/mailformpro');
  const tempDir = path.join(endpointDir, 'data/temp');
  fs.mkdirSync(tempDir, { recursive: true });
  fs.copyFileSync(path.join(root, 'src/contact/mailformpro/dropbox.php'), path.join(endpointDir, 'dropbox.php'));
  fs.writeFileSync(path.join(f.dir, 'outside.php'), "<?php echo 'OUTSIDE_EXECUTED'; exit;");
  const driver = path.join(endpointDir, 'driver.php');
  fs.writeFileSync(driver, "<?php $_GET=json_decode($argv[1],true); register_shutdown_function(function(){echo '\\nSTATUS='.http_response_code(); if(isset($GLOBALS['uploads'])) echo '\\nUPLOADS='.json_encode($GLOBALS['uploads']);}); include __DIR__.'/dropbox.php';");
  const request = (params) => spawnSync(php, ['-n', driver, JSON.stringify(params)], { cwd: endpointDir, encoding: 'utf8' });
  for (const params of [{}, { key: '' }, { key: [] }, { key: '../../../outside' }, { key: '..\\outside' }, { key: 'a'.repeat(23) }]) {
    const response = request(params);
    assert.equal(response.status, 0, response.stderr);
    assert.match(response.stdout, /STATUS=400/);
    assert.doesNotMatch(response.stdout, /OUTSIDE_EXECUTED/);
  }
  const key = 'Ab_-'.repeat(5) + 'xY';
  assert.match(request({ key }).stdout, /STATUS=404/);
  fs.writeFileSync(path.join(tempDir, key + '.php'), "<?php echo 'VALID_JOB'; exit;");
  assert.match(request({ key }).stdout, /VALID_JOB/);
  fs.unlinkSync(path.join(tempDir, key + '.php'));
  // Exercise normal upload, temporary-job removal, and both file-clear settings
  // with a local SDK stub; no Dropbox account or network request is involved.
  fs.mkdirSync(path.join(f.dir, 'vendor'));
  fs.writeFileSync(path.join(f.dir, 'vendor/autoload.php'), `<?php namespace Kunnu\\Dropbox;
class DropboxApp { public function __construct(...$args) {} }
class DropboxFile { public $path; public function __construct($path) { $this->path=$path; } }
class Dropbox { public function __construct($app) {} public function simpleUpload($file,$name,$options) { $GLOBALS['uploads'][]=array('name'=>$name,'exists'=>is_file($file->path),'autorename'=>$options['autorename']); } }`);
  fs.mkdirSync(path.join(endpointDir, 'uploads'));
  for (const clear of [true, false]) {
    fs.writeFileSync(path.join(endpointDir, 'uploads/invoice.txt'), 'upload fixture');
    fs.writeFileSync(path.join(tempDir, key + '.php'), `<?php $AppKey='app';$SecretKey='secret';$AccessToken='token';$UploadFiles=array('/uploads/invoice.txt');$UploadFileNames=array('/invoice.txt');$file_clear=${clear ? 'true' : 'false'};$thanks_url='/thanks/';`);
    const response = request({ key });
    assert.equal(response.status, 0, response.stderr);
    assert.match(response.stdout, /"exists":true,"autorename":true/);
    assert(!fs.existsSync(path.join(tempDir, key + '.php')), 'completed job must be removed');
    assert.equal(fs.existsSync(path.join(endpointDir, 'uploads/invoice.txt')), !clear);
  }
  try {
    fs.symlinkSync(path.join(f.dir, 'outside.php'), path.join(tempDir, key + '.php'), 'file');
  } catch (err) {
    if (err.code !== 'EPERM') throw err;
    t.diagnostic('File symlink case unavailable without Windows symlink permission');
    return;
  }
  assert.match(request({ key }).stdout, /STATUS=404/);
});

test('CF7 locates source in root/subfolder/custom layouts and rejects unrelated paths', { skip: !hasPhp }, (t) => {
  const f = fixture(t);
  const source = path.join(f.dir, 'src/page-contactform7.php');
  fs.writeFileSync(source, '<?php // source');
  const driver = path.join(f.dir, 'source-test.php');
  const importer = path.join(root, 'src/includes/import_form_cf7.php').replaceAll('\\', '/');
  fs.writeFileSync(driver, `<?php function add_filter(){} function add_action(){} require '${importer}'; echo json_encode(cf7_find_source_template_file($argv[1],$argv[2]));`);
  const locate = (theme, file = 'page-contactform7.php') => {
    const result = spawnSync(php, ['-n', driver, theme, file], { encoding: 'utf8' });
    assert.equal(result.status, 0, result.stderr);
    return JSON.parse(result.stdout);
  };
  for (const [output, wpFolder] of [['public', ''], ['public', 'wp-test'], ['output/deep', 'wordpress']]) {
    f.configure({ source_folder: output });
    const theme = path.join(f.dir, output, wpFolder, 'wp-content/themes/test-theme');
    fs.mkdirSync(theme, { recursive: true });
    assert.equal(locate(theme), fs.realpathSync(source));
    assert.equal(locate(theme, '../../package.json'), '');
  }
  const standalone = path.join(f.dir, 'standalone-theme');
  fs.mkdirSync(standalone);
  assert.equal(locate(standalone), '');
});

test('WordPress download extracts, cleans up, skips existing installs and migrates to a subfolder', (t) => {
  const f = fixture(t);
  const zip = new AdmZip();
  zip.addFile('wordpress/wp-config-sample.php', Buffer.from('<?php // fixture'));
  zip.addFile('wordpress/wp-blog-header.php', Buffer.from('<?php // fixture'));
  zip.addFile('wordpress/wp-content/themes/twenty-fixture/style.css', Buffer.from('fixture'));
  zip.addFile('wordpress/wp-content/preserve.txt', Buffer.from('preserve content'));
  const preload = path.join(f.dir, 'download-preload.mjs');
  fs.writeFileSync(preload, `import https from 'node:https';import {syncBuiltinESMExports} from 'node:module';import {Readable} from 'node:stream';import {EventEmitter} from 'node:events';
https.get=(opts,callback)=>{const req=new EventEmitter();queueMicrotask(()=>{const res=Readable.from([Buffer.from('${zip.toBuffer().toString('base64')}','base64')]);res.statusCode=200;res.headers={};callback(res);});return req;};syncBuiltinESMExports();`);
  const run = () => spawnSync(process.execPath, ['--import', pathToFileURL(preload).href, path.join(f.dir, 'scripts/download-wp.js')], {
    cwd: f.dir, encoding: 'utf8', timeout: 30000, env: { ...process.env, DEPLOY_ENV: '' },
  });
  let result = run();
  assert.equal(result.status, 0, result.stdout + result.stderr);
  assert(fs.existsSync(path.join(f.dir, 'public/wp-config-sample.php')));
  assert(!fs.existsSync(path.join(f.dir, 'public/wp-content/themes/twenty-fixture')));
  assert(!fs.existsSync(path.join(f.dir, '.wp_temp')));
  assert(!fs.existsSync(path.join(f.dir, 'latest-ja.zip')));
  result = run();
  assert.equal(result.status, 0, result.stderr);
  assert.match(result.stdout, /Skipping download/);
  f.configure({ folder_wp: 'wp-test' });
  result = run();
  assert.equal(result.status, 0, result.stdout + result.stderr);
  assert.equal(fs.readFileSync(path.join(f.dir, 'public/wp-test/wp-content/preserve.txt'), 'utf8'), 'preserve content');
  assert.match(fs.readFileSync(path.join(f.dir, 'public/index.php'), 'utf8'), /wp-test\/wp-blog-header\.php/);
  assert(fs.existsSync(path.join(f.dir, 'public/.htaccess')));
});

test('watch updates assets/plugins and recovers after SCSS and image errors', { timeout: 45000 }, async (t) => {
  const f = fixture(t);
  const scss = path.join(f.dir, 'src/assets/scss/probe.scss');
  fs.writeFileSync(scss, '.probe {color:red;}');
  fs.mkdirSync(path.join(f.dir, 'src/assets/images'), { recursive: true });
  fs.mkdirSync(path.join(f.dir, 'dev_plugins/demo'), { recursive: true });
  const server = http.createServer((req, res) => res.end('template proxy check'));
  await new Promise((resolve, reject) => { server.once('error', reject); server.listen(0, '127.0.0.1', resolve); });
  f.cleanup.push(() => new Promise((resolve) => { server.closeAllConnections(); server.close(resolve); }));
  const preload = path.join(f.dir, 'watch-preload.mjs');
  fs.writeFileSync(preload, `import bs from 'browser-sync';
const create=bs.create.bind(bs);
bs.create=(...args)=>{const instance=create(...args);const init=instance.init.bind(instance);
instance.init=(opts)=>init({...opts,open:false,online:false,listen:'127.0.0.1'},()=>console.log('TEST_READY='+opts.port));return instance;};`);
  const child = spawn(process.execPath, ['--import', pathToFileURL(preload).href, path.join(f.dir, 'scripts/build.js'), '--watch', '--proxy=http://127.0.0.1:' + server.address().port], {
    cwd: f.dir, stdio: ['ignore', 'pipe', 'pipe'], windowsHide: true, env: { ...process.env, DEPLOY_ENV: '' },
  });
  let stdout = '', stderr = '';
  child.stdout.on('data', b => { stdout += b; });
  child.stderr.on('data', b => { stderr += b; });
  f.cleanup.push(async () => {
    if (child.exitCode === null && child.signalCode === null) {
      const exited = new Promise(resolve => child.once('exit', resolve)); child.kill(); await exited;
    }
  });
  async function until(fn, label) {
    const end = Date.now() + 12000;
    while (Date.now() < end) {
      if (fn()) return;
      assert.equal(child.exitCode, null, stdout + stderr);
      await new Promise(resolve => setTimeout(resolve, 100));
    }
    assert.fail('Timeout: ' + label + '\n' + stdout.slice(-1800) + stderr.slice(-1800));
  }
  const read = p => fs.existsSync(p) ? fs.readFileSync(p, 'utf8') : '';
  await until(() => /TEST_READY=\d+/.test(stdout), 'BrowserSync startup');
  const port = stdout.match(/TEST_READY=(\d+)/)[1];
  assert.equal(await (await fetch('http://127.0.0.1:' + port)).text(), 'template proxy check');
  const css = path.join(f.theme, 'assets/css/probe.css');
  fs.writeFileSync(scss, '.bad { broken @@@ }');
  await until(() => stderr.includes('Error compiling'), 'SCSS error');
  fs.writeFileSync(scss, '.recovered {color:blue;}');
  await until(() => read(css).includes('.recovered'), 'SCSS recovery');
  const phpSource = path.join(f.dir, 'src/probe.php'), phpOutput = path.join(f.theme, 'probe.php');
  fs.writeFileSync(phpSource, '<?php // first'); await until(() => fs.existsSync(phpOutput), 'PHP add');
  fs.writeFileSync(phpSource, '<?php // updated'); await until(() => read(phpOutput).includes('updated'), 'PHP update');
  fs.unlinkSync(phpSource); await until(() => !fs.existsSync(phpOutput), 'PHP delete');
  const image = path.join(f.dir, 'src/assets/images/probe.png'), webp = path.join(f.theme, 'assets/images/probe.webp');
  const png = await sharp({ create: { width: 2, height: 2, channels: 3, background: 'red' } }).png().toBuffer();
  fs.writeFileSync(image, png); await until(() => fs.existsSync(webp), 'image add');
  assert.equal((await sharp(webp).metadata()).format, 'webp');
  fs.writeFileSync(image, 'invalid image'); await until(() => stderr.includes('[watch] Error processing'), 'image error');
  const prior = fs.statSync(webp).mtimeMs;
  fs.writeFileSync(image, png); await until(() => fs.statSync(webp).mtimeMs > prior, 'image recovery');
  fs.unlinkSync(image); await until(() => !fs.existsSync(webp), 'image delete');
  const plugin = path.join(f.dir, 'dev_plugins/demo/probe.php'), deployed = path.join(f.dir, 'public/wp-content/plugins/demo/probe.php');
  fs.writeFileSync(plugin, '<?php // first'); await until(() => fs.existsSync(deployed), 'plugin add');
  fs.writeFileSync(plugin, '<?php // updated'); await until(() => read(deployed).includes('updated'), 'plugin update');
  fs.unlinkSync(plugin); await until(() => !fs.existsSync(deployed), 'plugin delete');
  fs.unlinkSync(scss); await until(() => !fs.existsSync(css), 'SCSS delete');
  assert.equal(child.exitCode, null);
});
