// Build 4K masters for a background set from one high-res source
// Usage:
// node tools/make-bg-masters.mjs --src assets/originals/register-road.jpg --base register-bg --out public/images/register-bg --focus attention

import fs from 'node:fs';
import path from 'node:path';
import url from 'node:url';
import sharp from 'sharp';

const __dirname = path.dirname(url.fileURLToPath(import.meta.url));

function arg(name, def = undefined) {
  const i = process.argv.findIndex(a => a === `--${name}`);
  return i !== -1 ? (process.argv[i + 1] ?? true) : def;
}

const SRC   = arg('src');                              // e.g. assets/originals/register-road.jpg
const BASE  = arg('base', 'register-bg');             // e.g. register-bg
const OUT   = path.resolve(arg('out', `public/images/${BASE}`));
const FOCUS = (arg('focus', 'attention') || '').toLowerCase(); // attention|entropy|center|north|south|east|west|...
const FORMAT   = (arg('format', 'webp') || 'webp').toLowerCase();
const QUALITY  = Number(arg('quality', 90));
const DESKTOP  = { w: 3840, h: 2160 };  // 16:9
const MOBILE   = { w: 2160, h: 3840 };  // 9:16
const FIT = 'cover';

if (!SRC || !fs.existsSync(SRC)) {
  console.error('❌ --src is required and must exist (high-res photo).');
  process.exit(1);
}

fs.mkdirSync(OUT, { recursive: true });

function resolvePosition(focus) {
  switch (focus) {
    case 'attention': return sharp.strategy.attention;
    case 'entropy':   return sharp.strategy.entropy;
    // sharp also accepts strings like 'center','north','south', etc.
    default:          return focus || 'center';
  }
}

async function out(src, w, h, name) {
  const pipeline = sharp(src)
    .resize(w, h, { fit: FIT, position: resolvePosition(FOCUS) });

  if (FORMAT === 'avif') pipeline.avif({ quality: QUALITY });
  else if (FORMAT === 'jpg' || FORMAT === 'jpeg') pipeline.jpeg({ quality: QUALITY, mozjpeg: true });
  else pipeline.webp({ quality: QUALITY, effort: 4 });

  const file = path.join(OUT, `${BASE}-${name}-master.${FORMAT}`);
  await pipeline.toFile(file);
  console.log('✔', path.basename(file));
}

(async () => {
  console.log(`→ Making masters
  src    : ${SRC}
  base   : ${BASE}
  out    : ${OUT}
  focus  : ${FOCUS}
  format : ${FORMAT}
  `);

  await out(SRC, DESKTOP.w, DESKTOP.h, 'desktop'); // e.g. register-bg-desktop-master.webp
  await out(SRC, MOBILE.w,  MOBILE.h,  'mobile');  // e.g. register-bg-mobile-master.webp

  console.log('✅ Masters ready.');
})();