// tools/build-images.mjs
import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';
import sharp from 'sharp';

const __dirname = path.dirname(url.fileURLToPath(import.meta.url));

// ---- Profiles ----
// You can add more size sets later. Keys become CLI choices for --profile
const PROFILES = {
  // Backgrounds with desktop + mobile masters
  bg: {
    desktop: [3840, 2560, 1920],
    mobile:  [2160, 1080, 720],
    format:  'webp',
    quality: 78,
    fit:     'cover',
  },
  // One master -> multiple sizes (no desktop/mobile split)
  hero: { sizes: [2560, 1920, 1440], format: 'webp', quality: 80, fit: 'cover' },
  card: { sizes: [1200, 800, 600],    format: 'webp', quality: 80, fit: 'inside' },
  thumb:{ sizes: [400,  300,  200],   format: 'webp', quality: 80, fit: 'inside' },
};

// ---- CLI args (minimal) ----
function arg(name, def = undefined) {
  const idx = process.argv.findIndex(a => a === `--${name}`);
  return idx !== -1 ? (process.argv[idx + 1] ?? true) : def;
}
/**
 * Flags:
 * --profile bg|hero|card|thumb
 * --base <string>            e.g. "register-bg", "login-bg"
 * --out  <dir>               e.g. "public/images/register-bg"
 * --masters <dir>            where masters live (default: same as out)
 * --format webp|avif|jpg     (override profile)
 * --quality <number>         (override profile)
 * --concurrency <number>     default 4
 */
const PROFILE = arg('profile', 'bg');
const BASE    = arg('base');           // required
const OUTDIR  = path.resolve(arg('out') || `public/images/${BASE}`);
const MASTERS = path.resolve(arg('masters') || OUTDIR);
const OVR_FORMAT = arg('format', null);
const OVR_QUALITY = arg('quality', null);
const CONCURRENCY = Number(arg('concurrency', 4));

if (!BASE) {
  console.error('❌ Missing --base. Example: --base register-bg');
  process.exit(1);
}
if (!PROFILES[PROFILE]) {
  console.error(`❌ Unknown --profile "${PROFILE}". Available: ${Object.keys(PROFILES).join(', ')}`);
  process.exit(1);
}
fs.mkdirSync(OUTDIR, { recursive: true });

function firstExisting(...candidates) {
  for (const p of candidates) if (fs.existsSync(p)) return p;
  return null;
}

function outNameBg(kind, width, fmt) {
  // e.g. register-bg-desktop-3840.webp
  return `${BASE}-${kind}-${width}.${fmt}`;
}

function outNameSingle(width, fmt) {
  // e.g. hero-monte-carlo-1920.webp
  return `${BASE}-${width}.${fmt}`;
}

async function pipelineResize(src, width, fmt, quality, fit) {
  const s = sharp(src).resize({ width, fit });
  switch (fmt) {
    case 'avif': return s.avif({ quality }).toBuffer();
    case 'jpg':
    case 'jpeg': return s.jpeg({ quality, mozjpeg: true }).toBuffer();
    default:     return s.webp({ quality, effort: 4 }).toBuffer();
  }
}

async function runBg() {
  // Master names: <BASE>-desktop-master.(webp|png|jpg|jpeg), <BASE>-mobile-master.*
  const desktopMaster = firstExisting(
    path.join(MASTERS, `${BASE}-desktop-master.webp`),
    path.join(MASTERS, `${BASE}-desktop-master.png`),
    path.join(MASTERS, `${BASE}-desktop-master.jpg`),
    path.join(MASTERS, `${BASE}-desktop-master.jpeg`),
  );
  const mobileMaster = firstExisting(
    path.join(MASTERS, `${BASE}-mobile-master.webp`),
    path.join(MASTERS, `${BASE}-mobile-master.png`),
    path.join(MASTERS, `${BASE}-mobile-master.jpg`),
    path.join(MASTERS, `${BASE}-mobile-master.jpeg`),
  );
  if (!desktopMaster || !mobileMaster) {
    console.error(`❌ Missing masters for bg profile:
  Expected: ${BASE}-desktop-master.(webp|png|jpg|jpeg) and ${BASE}-mobile-master.(webp|png|jpg|jpeg) in ${MASTERS}`);
    process.exit(1);
  }

  const { desktop, mobile, format, quality, fit } = PROFILES.bg;
  const fmt = (OVR_FORMAT || format).toLowerCase();
  const q   = OVR_QUALITY ? Number(OVR_QUALITY) : quality;

  const tasks = [];
  for (const w of desktop) {
    tasks.push({ src: desktopMaster, width: w, out: path.join(OUTDIR, outNameBg('desktop', w, fmt)), fmt, q, fit });
  }
  for (const w of mobile) {
    tasks.push({ src: mobileMaster, width: w, out: path.join(OUTDIR, outNameBg('mobile', w, fmt)), fmt, q, fit });
  }
  await runBatched(tasks);
}

async function runSingle(profileKey) {
  const prof = PROFILES[profileKey];
  const fmt = (OVR_FORMAT || prof.format).toLowerCase();
  const q   = OVR_QUALITY ? Number(OVR_QUALITY) : prof.quality;

  // Master name: <BASE>-master.(webp|png|jpg|jpeg)
  const master = firstExisting(
    path.join(MASTERS, `${BASE}-master.webp`),
    path.join(MASTERS, `${BASE}-master.png`),
    path.join(MASTERS, `${BASE}-master.jpg`),
    path.join(MASTERS, `${BASE}-master.jpeg`),
  );
  if (!master) {
    console.error(`❌ Missing master for profile "${profileKey}":
  Expected: ${BASE}-master.(webp|png|jpg|jpeg) in ${MASTERS}`);
    process.exit(1);
  }

  const tasks = [];
  for (const w of prof.sizes) {
    tasks.push({ src: master, width: w, out: path.join(OUTDIR, outNameSingle(w, fmt)), fmt, q: prof.quality, fit: prof.fit });
  }
  await runBatched(tasks);
}

async function runBatched(items) {
  let i = 0;
  const total = items.length;
  const queue = Array.from({ length: Math.min(CONCURRENCY, total) }, () => worker());
  await Promise.all(queue);

  async function worker() {
    while (i < total) {
      const idx = i++;
      const it = items[idx];
      try {
        const buf = await pipelineResize(it.src, it.width, it.fmt, it.q, it.fit);
        await fs.promises.writeFile(it.out, buf);
        console.log(`✔ ${path.basename(it.out)}`);
      } catch (e) {
        console.error(`✖ Failed ${path.basename(it.out)}:`, e.message);
        process.exitCode = 1;
      }
    }
  }
}

// ---- Run ----
(async () => {
  console.log(`→ Building images
  profile  : ${PROFILE}
  base     : ${BASE}
  masters  : ${MASTERS}
  out      : ${OUTDIR}
  format   : ${OVR_FORMAT || '(profile default)'}
  quality  : ${OVR_QUALITY || '(profile default)'}
  `);

  if (PROFILE === 'bg') await runBg();
  else await runSingle(PROFILE);

  if (process.exitCode) {
    console.error('❌ Completed with errors.');
    process.exit(process.exitCode);
  } else {
    console.log('✅ Done.');
  }
})();