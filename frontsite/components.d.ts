// components.d.ts
declare module "@vue/runtime-core" {
  export interface GlobalComponents {
    RouterLink: typeof import("vue-router")["RouterLink"];
    RouterView: typeof import("vue-router")["RouterView"];
    BaseBlock: typeof import("./src/components/BaseBlock.vue");
    BaseBackground: typeof import("./src/components/BaseBackground.vue");
    BasePageHeading: typeof import("./src/components/BasePageHeading.vue");
  }
}

export {};
