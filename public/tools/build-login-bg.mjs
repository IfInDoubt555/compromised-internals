import fs from 'node:fs';
import path from 'node:path';
import sharp from 'sharp';

const outDir = path.resolve('public/images/login-bg');
fs.mkdirSync(outDir, { recursive: true });

// Helper: resolve a master file, try .webp first, then .png
function resolveMaster(baseName) {
  const webp = path.resolve(`public/images/login-bg/${baseName}.webp`);
  const png  = path.resolve(`public/images/login-bg/${baseName}.png`);
  if (fs.existsSync(webp)) return webp;
  if (fs.existsSync(png)) return png;
  throw new Error(`Missing master for ${baseName} (.webp or .png)`);
}

const DESKTOP_SRC = resolveMaster('login-bg-desktop-4k-master');
const MOBILE_SRC  = resolveMaster('login-bg-mobile-4k-master');

async function makeDesktop() {
  const targets = [
    { w: 3840, name: 'login-bg-desktop-4k.webp' },
    { w: 2560, name: 'login-bg-desktop-2560.webp' },
    { w: 1920, name: 'login-bg-desktop-1920.webp' },
  ];
  for (const t of targets) {
    await sharp(DESKTOP_SRC)
      .resize({ width: t.w })
      .webp({ quality: 78, effort: 4 })
      .toFile(path.join(outDir, t.name));
  }
}

async function makeMobile() {
  const targets = [
    { w: 2160, name: 'login-bg-mobile-4k.webp' },
    { w: 1080, name: 'login-bg-mobile-1080.webp' },
    { w: 720,  name: 'login-bg-mobile-720.webp' },
  ];
  for (const t of targets) {
    await sharp(MOBILE_SRC)
      .resize({ width: t.w })
      .webp({ quality: 78, effort: 4 })
      .toFile(path.join(outDir, t.name));
  }
}

try {
  await makeDesktop();
  await makeMobile();
  console.log('✔ Login backgrounds built.');
} catch (e) {
  console.error('❌ Error building login backgrounds:', e);
  process.exit(1);
}