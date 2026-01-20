import fs from 'node:fs';
import path from 'node:path';

const rootDir = path.resolve();
const buildDir = path.join(rootDir, 'public', 'build');
const manifestPath = path.join(buildDir, 'manifest.json');

if (!fs.existsSync(manifestPath)) {
  console.error('Missing Vite manifest. Run `npm run build` first.');
  process.exit(1);
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
const seen = new Set();
const files = [];

const addFile = (file) => {
  if (!file || seen.has(file)) {
    return;
  }

  const filePath = path.join(buildDir, file);
  if (!fs.existsSync(filePath)) {
    return;
  }

  const size = fs.statSync(filePath).size;
  seen.add(file);
  files.push({ file, bytes: size });
};

Object.values(manifest).forEach((entry) => {
  addFile(entry.file);
  (entry.css || []).forEach(addFile);
  (entry.assets || []).forEach(addFile);
});

const totals = files.reduce(
  (acc, file) => {
    acc.total += file.bytes;
    if (file.file.endsWith('.js')) {
      acc.js += file.bytes;
    } else if (file.file.endsWith('.css')) {
      acc.css += file.bytes;
    } else {
      acc.other += file.bytes;
    }
    return acc;
  },
  { total: 0, js: 0, css: 0, other: 0 },
);

const report = {
  generated_at: new Date().toISOString(),
  totals,
  files: files.sort((a, b) => b.bytes - a.bytes),
};

const outputDir = path.join(rootDir, 'storage', 'app', 'performance');
fs.mkdirSync(outputDir, { recursive: true });
fs.writeFileSync(
  path.join(outputDir, 'bundle-report.json'),
  JSON.stringify(report, null, 2),
);

console.log(`Bundle report written to ${path.join(outputDir, 'bundle-report.json')}`);
