import { build } from 'vite';

console.log('Starting build...');
try {
  await build();
  console.log('Build completed successfully.');
} catch (e) {
  console.error('Build failed:', e);
  process.exit(1);
}
