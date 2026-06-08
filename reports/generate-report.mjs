import { spawn, spawnSync } from 'node:child_process';
import { existsSync, mkdirSync, openSync, rmSync, writeFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath, pathToFileURL } from 'node:url';

const rootDir = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const reportsDir = join(rootDir, 'reports');
const screenshotsDir = join(reportsDir, 'screenshots');
const chromeProfileDir = join(reportsDir, `.chrome-profile-${Date.now()}`);
const serverPort = 8000;
const debugPort = 9223;
const baseUrl = `http://127.0.0.1:${serverPort}`;
const chromePath = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const reportHtmlPath = join(reportsDir, 'laporan-screenshot.html');
const reportPdfPath = join(reportsDir, 'laporan-screenshot.pdf');
const dbPath = join(rootDir, 'database', 'report.sqlite');
const githubUrl = 'https://github.com/BintangAkmalKurniawan/UTS-WEB2.git';

mkdirSync(screenshotsDir, { recursive: true });
mkdirSync(chromeProfileDir, { recursive: true });

const children = [];

function spawnLogged(command, args, logName, options = {}) {
  const log = openSync(join(reportsDir, logName), 'a');
  const child = spawn(command, args, {
    cwd: rootDir,
    env: process.env,
    stdio: ['ignore', log, log],
    ...options,
  });

  children.push(child);
  return child;
}

function cleanup() {
  for (const child of children) {
    if (!child.killed) {
      child.kill();
    }
  }

  const resolvedProfile = resolve(chromeProfileDir);
  const resolvedReports = resolve(reportsDir);
  if (resolvedProfile.startsWith(resolvedReports) && existsSync(resolvedProfile)) {
    try {
      rmSync(resolvedProfile, {
        recursive: true,
        force: true,
        maxRetries: 5,
        retryDelay: 200,
      });
    } catch {
      // Chrome can keep profile files locked briefly on Windows; leaving this
      // temporary folder is better than failing a completed report run.
    }
  }
}

process.on('exit', cleanup);
process.on('SIGINT', () => {
  cleanup();
  process.exit(130);
});

function sleep(ms) {
  return new Promise((resolveSleep) => setTimeout(resolveSleep, ms));
}

async function waitForHttp(url, timeoutMs = 30000) {
  const start = Date.now();
  let lastError = null;

  while (Date.now() - start < timeoutMs) {
    try {
      const response = await fetch(url);
      if (response.status < 500) {
        return;
      }
      lastError = new Error(`HTTP ${response.status}`);
    } catch (error) {
      lastError = error;
    }
    await sleep(500);
  }

  throw new Error(`Timed out waiting for ${url}: ${lastError?.message || 'no response'}`);
}

async function getJson(url, options = {}) {
  const response = await fetch(url, options);
  if (!response.ok) {
    throw new Error(`${url} returned HTTP ${response.status}`);
  }
  return response.json();
}

class CdpClient {
  constructor(wsUrl) {
    this.wsUrl = wsUrl;
    this.id = 0;
    this.pending = new Map();
    this.listeners = [];
  }

  async connect() {
    this.ws = new WebSocket(this.wsUrl);
    await new Promise((resolveConnect, rejectConnect) => {
      const timer = setTimeout(() => rejectConnect(new Error('Timed out connecting to Chrome DevTools')), 10000);
      this.ws.addEventListener('open', () => {
        clearTimeout(timer);
        resolveConnect();
      }, { once: true });
      this.ws.addEventListener('error', (event) => {
        clearTimeout(timer);
        rejectConnect(new Error(`DevTools WebSocket error: ${event.message || 'unknown error'}`));
      }, { once: true });
    });

    this.ws.addEventListener('message', (event) => {
      const message = JSON.parse(event.data);
      if (message.id && this.pending.has(message.id)) {
        const { resolveSend, rejectSend } = this.pending.get(message.id);
        this.pending.delete(message.id);
        if (message.error) {
          rejectSend(new Error(`${message.error.message}: ${JSON.stringify(message.error.data || {})}`));
        } else {
          resolveSend(message.result || {});
        }
        return;
      }

      for (const listener of [...this.listeners]) {
        listener(message);
      }
    });
  }

  send(method, params = {}, timeoutMs = 30000) {
    const id = ++this.id;
    this.ws.send(JSON.stringify({ id, method, params }));
    return new Promise((resolveSend, rejectSend) => {
      this.pending.set(id, { resolveSend, rejectSend });
      setTimeout(() => {
        if (this.pending.has(id)) {
          this.pending.delete(id);
          rejectSend(new Error(`Timed out calling ${method}`));
        }
      }, timeoutMs);
    });
  }

  waitForEvent(method, timeoutMs = 15000) {
    return new Promise((resolveEvent, rejectEvent) => {
      const timer = setTimeout(() => {
        this.listeners = this.listeners.filter((listener) => listener !== onMessage);
        rejectEvent(new Error(`Timed out waiting for ${method}`));
      }, timeoutMs);

      const onMessage = (message) => {
        if (message.method === method) {
          clearTimeout(timer);
          this.listeners = this.listeners.filter((listener) => listener !== onMessage);
          resolveEvent(message.params || {});
        }
      };

      this.listeners.push(onMessage);
    });
  }

  close() {
    this.ws?.close();
  }
}

async function waitForExpression(cdp, expression, timeoutMs = 15000) {
  const start = Date.now();
  while (Date.now() - start < timeoutMs) {
    const result = await cdp.send('Runtime.evaluate', {
      expression,
      awaitPromise: true,
      returnByValue: true,
    });

    if (result.result?.value) {
      return;
    }
    await sleep(250);
  }

  throw new Error(`Timed out waiting for expression: ${expression}`);
}

async function navigate(cdp, url, settleMs = 1000) {
  const loaded = cdp.waitForEvent('Page.loadEventFired', 20000).catch(() => null);
  await cdp.send('Page.navigate', { url });
  await loaded;
  await waitForExpression(cdp, 'document.readyState === "complete" || document.readyState === "interactive"', 10000);
  await sleep(settleMs);
}

async function capturePage(cdp, page) {
  await navigate(cdp, page.url, page.settleMs || 1200);
  await cdp.send('Runtime.evaluate', {
    expression: 'window.scrollTo(0, 0)',
    returnByValue: true,
  });
  await sleep(300);

  const metrics = await cdp.send('Page.getLayoutMetrics');
  const size = metrics.cssContentSize || metrics.contentSize || { width: 1440, height: 1000 };
  const width = Math.max(1440, Math.ceil(size.width));
  const height = Math.min(page.maxHeight || 1400, Math.max(1000, Math.ceil(size.height)));

  const image = await cdp.send('Page.captureScreenshot', {
    format: 'png',
    fromSurface: true,
    captureBeyondViewport: true,
    clip: { x: 0, y: 0, width, height, scale: 1 },
  });

  const imagePath = join(screenshotsDir, page.file);
  writeFileSync(imagePath, Buffer.from(image.data, 'base64'));
  return { ...page, imagePath };
}

function createReportHtml(pages) {
  const dateText = new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  }).format(new Date());

  const figures = pages.map((page, index) => `
    <section class="figure">
      <div class="caption">
        <span>${String(index + 1).padStart(2, '0')}</span>
        <div>
          <h2>${page.title}</h2>
          <p>${page.description}</p>
        </div>
      </div>
      <img src="${pathToFileURL(page.imagePath).href}" alt="${page.title}">
    </section>
  `).join('\n');

  return `<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Screenshot MyCampus</title>
  <style>
    @page {
      size: A4;
      margin: 14mm;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      color: #121826;
      font-family: Arial, Helvetica, sans-serif;
      background: #ffffff;
    }

    .cover {
      min-height: 267mm;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-top: 9px solid #00113a;
    }

    .eyebrow {
      color: #5266b1;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.18em;
      text-transform: uppercase;
    }

    h1 {
      margin: 16px 0 20px;
      color: #00113a;
      font-size: 40px;
      line-height: 1.1;
    }

    .meta {
      margin: 0;
      color: #4b5563;
      font-size: 14px;
      line-height: 1.7;
    }

    .github-link {
      color: #00113a;
      font-weight: 700;
      text-decoration: underline;
      text-underline-offset: 3px;
    }

    .list {
      margin-top: 34px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
    }

    .item {
      border: 1px solid #d6dae5;
      border-radius: 8px;
      padding: 13px 15px;
      font-size: 13px;
      font-weight: 700;
      color: #1f2937;
      background: #f7f9fb;
    }

    .figure {
      break-before: page;
      page-break-before: always;
    }

    .caption {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 10px;
    }

    .caption span {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #00113a;
      color: #ffffff;
      font-size: 12px;
      font-weight: 700;
    }

    h2 {
      margin: 0;
      color: #00113a;
      font-size: 21px;
    }

    .caption p {
      margin: 3px 0 0;
      color: #5b6472;
      font-size: 12px;
    }

    img {
      display: block;
      width: 100%;
      max-height: 247mm;
      object-fit: contain;
      border: 1px solid #d6dae5;
      border-radius: 8px;
      background: #f7f9fb;
    }
  </style>
</head>
<body>
  <section class="cover">
    <p class="eyebrow">Laporan UTS Web 2</p>
    <h1>Laporan Screenshot<br>Aplikasi MyCampus</h1>
    <p class="meta">Tanggal pembuatan: ${dateText}<br>Berisi dokumentasi halaman login, dashboard, dan fitur CRUD mahasiswa, jurusan, serta mata kuliah.<br>Link GitHub: <a class="github-link" href="${githubUrl}">${githubUrl}</a></p>
    <div class="list">
      <div class="item">Halaman Login</div>
      <div class="item">Dashboard Admin</div>
      <div class="item">Data Mahasiswa</div>
      <div class="item">Tambah Mahasiswa</div>
      <div class="item">CRUD Jurusan</div>
      <div class="item">CRUD Mata Kuliah</div>
    </div>
  </section>
  ${figures}
</body>
</html>`;
}

async function main() {
  if (!existsSync(dbPath)) {
    throw new Error(`Database sementara tidak ditemukan: ${dbPath}`);
  }

  const phpEnv = {
    ...process.env,
    APP_URL: baseUrl,
    DB_CONNECTION: 'sqlite',
    DB_DATABASE: dbPath,
    SESSION_DRIVER: 'file',
    CACHE_STORE: 'file',
    QUEUE_CONNECTION: 'sync',
  };

  spawnLogged('php', ['artisan', 'serve', '--host=127.0.0.1', `--port=${serverPort}`], 'report-server.log', {
    env: phpEnv,
  });
  await waitForHttp(baseUrl);

  const chrome = spawnLogged(chromePath, [
    '--headless=new',
    '--disable-gpu',
    '--no-first-run',
    '--no-default-browser-check',
    `--remote-debugging-port=${debugPort}`,
    `--user-data-dir=${chromeProfileDir}`,
    '--window-size=1440,1000',
    'about:blank',
  ], 'report-chrome.log');
  await waitForHttp(`http://127.0.0.1:${debugPort}/json/version`);

  const target = await getJson(`http://127.0.0.1:${debugPort}/json/new?about:blank`, { method: 'PUT' });
  const cdp = new CdpClient(target.webSocketDebuggerUrl);
  await cdp.connect();

  await cdp.send('Page.enable');
  await cdp.send('Runtime.enable');
  await cdp.send('Network.enable');
  await cdp.send('Emulation.setDeviceMetricsOverride', {
    width: 1440,
    height: 1000,
    deviceScaleFactor: 1,
    mobile: false,
  });

  const captured = [];

  captured.push(await capturePage(cdp, {
    title: 'Halaman Login',
    description: 'Form login admin aplikasi MyCampus.',
    url: `${baseUrl}/`,
    file: '01-login.png',
    maxHeight: 1000,
    settleMs: 1200,
  }));

  await cdp.send('Runtime.evaluate', {
    awaitPromise: true,
    expression: `new Promise((resolve) => {
      const setNativeValue = (element, value) => {
        const setter = Object.getOwnPropertyDescriptor(element.constructor.prototype, 'value')?.set;
        setter.call(element, value);
        element.dispatchEvent(new Event('input', { bubbles: true }));
        element.dispatchEvent(new Event('change', { bubbles: true }));
      };

      setNativeValue(document.querySelector('#email'), 'akmal@gmail.com');
      setNativeValue(document.querySelector('#password'), 'password');
      setTimeout(() => {
        document.querySelector('form button[type="submit"], form button:not([type])').click();
        resolve(true);
      }, 250);
    })`,
  });
  await waitForExpression(cdp, 'location.pathname === "/admin/dashboard"', 20000);
  await sleep(1200);

  const pages = [
    {
      title: 'Dashboard Admin',
      description: 'Ringkasan jumlah mahasiswa, jurusan, dan chart distribusi jurusan.',
      url: `${baseUrl}/admin/dashboard`,
      file: '02-dashboard.png',
      maxHeight: 1350,
      settleMs: 1800,
    },
    {
      title: 'CRUD Mahasiswa - Data Mahasiswa',
      description: 'Tabel data mahasiswa beserta filter, pagination, edit, dan hapus.',
      url: `${baseUrl}/admin/mahasiswa`,
      file: '03-crud-mahasiswa-data.png',
      maxHeight: 1450,
      settleMs: 1200,
    },
    {
      title: 'CRUD Mahasiswa - Tambah Mahasiswa',
      description: 'Form tambah data mahasiswa baru.',
      url: `${baseUrl}/admin/tambah-mahasiswa`,
      file: '04-crud-mahasiswa-tambah.png',
      maxHeight: 1200,
      settleMs: 1200,
    },
    {
      title: 'CRUD Jurusan',
      description: 'Form tambah/edit jurusan dan tabel daftar jurusan.',
      url: `${baseUrl}/admin/jurusan`,
      file: '05-crud-jurusan.png',
      maxHeight: 1600,
      settleMs: 1200,
    },
    {
      title: 'CRUD Mata Kuliah',
      description: 'Form tambah/edit mata kuliah dan tabel daftar mata kuliah.',
      url: `${baseUrl}/admin/matakuliah`,
      file: '06-crud-matakuliah.png',
      maxHeight: 1600,
      settleMs: 1200,
    },
  ];

  for (const page of pages) {
    captured.push(await capturePage(cdp, page));
  }

  writeFileSync(reportHtmlPath, createReportHtml(captured), 'utf8');

  const pdfBuild = spawnSync('php', ['reports/build-pdf.php'], {
    cwd: rootDir,
    encoding: 'utf8',
  });

  if (pdfBuild.status !== 0) {
    throw new Error(pdfBuild.stderr || pdfBuild.stdout || 'Gagal membuat PDF.');
  }

  cdp.close();
  cleanup();
  console.log(`PDF dibuat: ${reportPdfPath}`);
}

main().catch((error) => {
  cleanup();
  console.error(error);
  process.exit(1);
});
