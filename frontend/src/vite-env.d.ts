/// <reference types="vite/client" />

/**
 * Iran-Daily — Environment Type Declarations
 * @author THE SAZ <https://github.com/THE-SAZ>
 */

interface ImportMetaEnv {
  readonly VITE_API_BASE: string;
  readonly VITE_SSE_ENABLED: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
