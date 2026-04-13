import { defineConfig } from "vite";

export default defineConfig({
    base: '',
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern-compiler",
                silenceDeprecations: [
                    'import',
                    'global-builtin'
                ],
                quietDeps: true
            },
        },
    },
    build: {
        manifest: true,
        sourcemap: true,
        rollupOptions: {
            input: {
                style: "src/scss/public.scss",
                script: "src/js/main.js",
            },
        },
    },
    server: {
        hmr: false,
    },
});
