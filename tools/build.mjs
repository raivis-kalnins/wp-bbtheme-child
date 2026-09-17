import fs from 'node:fs';
import path from 'node:path';
import crypto from 'node:crypto';

const root = process.cwd();
const dist = path.join(root, 'dist');
const assetsDir = path.join(dist, 'assets');
const styleEntry = 'src/scss/public.scss';
const scriptEntry = 'src/js/main.js';

function read(rel) { return fs.readFileSync(path.join(root, rel), 'utf8'); }
function hash(content) { return crypto.createHash('sha256').update(content).digest('hex').slice(0, 10); }

function parseTokens() {
  const input = read('src/scss/_tokens.scss');
  const tokens = {};
  for (const line of input.split(/\r?\n/)) {
    const match = line.match(/^\s*\$([\w-]+)\s*:\s*(.+);\s*$/);
    if (match) tokens[match[1]] = match[2].trim();
  }
  const resolve = (name, stack = []) => {
    if (!(name in tokens)) return `$${name}`;
    if (stack.includes(name)) throw new Error(`Circular Sass token: ${[...stack, name].join(' -> ')}`);
    return tokens[name].replace(/\$([\w-]+)/g, (_, child) => resolve(child, [...stack, name]));
  };
  return Object.fromEntries(Object.keys(tokens).map((name) => [name, resolve(name)]));
}

function rgbaHex(hex, alpha) {
  let value = hex.trim();
  if (!/^#[0-9a-f]{3,8}$/i.test(value)) return `rgba(${value},${alpha})`;
  value = value.slice(1);
  if (value.length === 3) value = value.split('').map((char) => char + char).join('');
  const rgb = [0, 2, 4].map((offset) => parseInt(value.slice(offset, offset + 2), 16));
  return `rgba(${rgb.join(',')},${alpha.trim()})`;
}

function pxNumber(value) {
  const match = String(value).trim().match(/^(-?[0-9.]+)(px|rem|em)?$/);
  if (!match) throw new Error(`fluid-font supports px/rem/em values; received ${value}`);
  const number = Number(match[1]);
  const unit = match[2] || 'px';
  return unit === 'rem' || unit === 'em' ? number * 16 : number;
}

function viewportPx(value) {
  const match = String(value).trim().match(/^([0-9.]+)px$/);
  if (!match) throw new Error(`fluid-font viewport values must be px; received ${value}`);
  return Number(match[1]);
}

function expandFluidMixins(css) {
  const media = [];
  // All fluid-font calls live in flat selector blocks by design. Expand them to
  // a px + vw equation plus explicit min/max media guards (no clamp()).
  css = css.replace(/([^{}]+)\{([^{}]*?)@include\s+fluid-font\(([^)]+)\);?([^{}]*?)\}/g, (full, selector, before, argsText, after) => {
    const args = argsText.split(',').map((value) => value.trim());
    const minText = args[0];
    const maxText = args[1];
    const minVwText = args[2] || '360px';
    const maxVwText = args[3] || '1720px';
    const fallback = args[4] && args[4] !== 'false' ? args[4] : maxText;
    const min = pxNumber(minText);
    const max = pxNumber(maxText);
    const minVw = viewportPx(minVwText);
    const maxVw = viewportPx(maxVwText);
    const slope = ((max - min) * 100) / (maxVw - minVw);
    const intercept = min - (slope * minVw / 100);
    const declaration = `font-size:${fallback};font-size:calc(${intercept.toFixed(6)}px + ${slope.toFixed(6)}vw);`;
    media.push(`@media(max-width:${minVwText}){${selector.trim()}{font-size:${minText}}}`);
    media.push(`@media(min-width:${maxVwText}){${selector.trim()}{font-size:${maxText}}}`);
    return `${selector}{${before}${declaration}${after}}`;
  });
  if (/@include\s+fluid-font/.test(css)) throw new Error('Unexpanded fluid-font mixin remains. Keep fluid-font calls in flat selector blocks.');
  return `${css}\n${media.join('\n')}`;
}

function compileScssFallback() {
  const order = ['base','header','footer','components','swiper','motion','forms','blog','quality','sector','responsive','features','functional-ui','design-system','premium-ui'];
  const tokens = parseTokens();
  let css = order.map((name) => read(`src/scss/_${name}.scss`)).join('\n');
  css = css.replace(/^\s*@use\s+['"](?:tokens|tools)['"](?:\s+as\s+\*)?\s*;\s*$/gm, '');
  css = css.replace(/#\{\$([\w-]+)\}/g, (_, name) => tokens[name] ?? `$${name}`);
  css = css.replace(/rgba\(\s*\$([\w-]+)\s*,\s*([^)]+)\)/g, (_, name, alpha) => rgbaHex(tokens[name] ?? `$${name}`, alpha));
  css = css.replace(/\$([\w-]+)/g, (_, name) => tokens[name] ?? `$${name}`);
  if (/\$[\w-]+/.test(css)) throw new Error('Unresolved Sass variable remains in compiled CSS.');
  css = expandFluidMixins(css);
  if (/clamp\(/.test(css)) throw new Error('clamp() is not allowed in current-suite CSS.');
  if (/!important/.test(css)) throw new Error('!important is not allowed in current-suite CSS.');
  css = css.replace(/\/\*[\s\S]*?\*\//g, '');
  css = css.replace(/\s+/g, ' ').replace(/\s*([{}:;,>])\s*/g, '$1').replace(/;}/g, '}').trim();
  return `${css}\n`;
}

async function compileScss() {
  try {
    const sass = await import('sass');
    const result = sass.compile(path.join(root, styleEntry), { style: 'compressed', loadPaths: [path.join(root, 'src/scss')] });
    const css = result.css;
    if (/clamp\(/.test(css)) throw new Error('clamp() is not allowed in current-suite CSS.');
    if (/!important/.test(css)) throw new Error('!important is not allowed in current-suite CSS.');
    return `${css}\n`;
  } catch (error) {
    if (error && (error.code === 'ERR_MODULE_NOT_FOUND' || String(error.message || '').includes("Cannot find package 'sass'"))) {
      return compileScssFallback();
    }
    throw error;
  }
}

function bundleJs() {
  const header = read('src/js/components/header.js').replace(/^export\s+/gm, '');
  const motion = read('src/js/components/motion.js').replace(/^export\s+/gm, '');
  const blog = read('src/js/components/blog.js').replace(/^export\s+/gm, '');
  const main = read('src/js/main.js').replace(/^import[^;]+;\s*$/gm, '');
  return `${header}\n${motion}\n${blog}\n${main}\n`;
}

fs.rmSync(dist, { recursive: true, force: true });
fs.mkdirSync(path.join(dist, '.vite'), { recursive: true });
fs.mkdirSync(assetsDir, { recursive: true });
const css = await compileScss();
const js = bundleJs();
const cssFile = `assets/theme-${hash(css)}.css`;
const jsFile = `assets/theme-${hash(js)}.js`;
fs.writeFileSync(path.join(dist, cssFile), css);
fs.writeFileSync(path.join(dist, jsFile), js);
fs.writeFileSync(path.join(dist, '.vite', 'manifest.json'), JSON.stringify({
  [styleEntry]: { file: cssFile, src: styleEntry, isEntry: true },
  [scriptEntry]: { file: jsFile, src: scriptEntry, isEntry: true },
}, null, 2) + '\n');
console.log(`Built ${cssFile} and ${jsFile}`);
