import { defineConfig } from "vite";

export default defineConfig({
  server: {
    host: "127.0.0.1",
    port: 5173,
    strictPort: true,
    cors: true,
    hmr: {
      host: "127.0.0.1",
      port: 5173,
    },
  },
  plugins: [
    {
      name: "reload-php",
      handleHotUpdate({ file, server }) {
        if (file.endsWith(".php")) {
          server.ws.send({ type: "full-reload" });
          return [];
        }
      },
    },
  ],
});
