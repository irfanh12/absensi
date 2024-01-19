<script setup>
import { useAuth } from "@/stores/auth";
import { useTemplateStore } from "@/stores/template";

import BaseLayout from "@/layouts/BaseLayout.vue";
import BaseNavigation from "@/components/BaseNavigation.vue";

// Main store
const store = useTemplateStore();

// Auth store
const auth = useAuth();
const permissions = auth.permissions()

// Set default elements for this layout
store.setLayout({
  header: true,
  sidebar: true,
  sideOverlay: true,
  footer: true,
});

// Set various template options for this layout variation
store.headerStyle({ mode: "light" });
store.mainContent({ mode: "narrow" });
</script>

<template>
  <BaseLayout>
    <!-- Side Overlay Content -->
    <!-- Using the available v-slot, we can override the default Side Overlay content from layouts/partials/SideOvelay.vue -->
    <template #side-overlay-content>
      <div class="content-side">
        <p>Side Overlay content..</p>
      </div>
    </template>
    <!-- END Side Overlay Content -->

    <!-- Sidebar Content -->
    <!-- Using the available v-slot, we can override the default Sidebar content from layouts/partials/Sidebar.vue -->
    <template #sidebar-content>
      <div class="content-side">
        <BaseNavigation
          :nodes="[
            {
              name: 'Dashboard',
              to: 'dashboard',
              icon: 'si si-speedometer',
              parent: 'dashboard',
              permission: true,
            },
            {
              name: 'Menus',
              heading: true,
              permission: permissions.menus.includes(auth.position),
            },
            {
              name: 'Klien',
              to: 'klien-index',
              icon: 'fa fa-user-tie',
              permission: permissions.klien.includes(auth.position),
              parent: 'klien',
            },
            {
              name: 'Karyawan',
              to: 'karyawan-index',
              icon: 'fa fa-users',
              permission: permissions.karyawan.includes(auth.position),
              parent: 'karyawan',
            },
            {
              name: 'Timesheet',
              to: 'timesheet-index',
              icon: 'fa fa-sheet-plastic',
              permission: permissions.timesheet_data.includes(auth.position),
              parent: 'timesheet',
            },
            {
              name: 'Presensi',
              to: 'presensi-index',
              icon: 'si si-camera',
              permission: permissions.presensi_data.includes(auth.position),
              parent: 'presensi',
            },
            {
              name: 'Action',
              heading: true,
              permission: true,
            },
            {
              name: 'Log Out',
              to: 'logout',
              icon: 'si si-logout',
              permission: true,
            },
          ]"
        />
      </div>
    </template>
    <!-- END Sidebar Content -->

    <!-- Header Content Left -->
    <!-- Using the available v-slot, we can override the default Header content from layouts/partials/Header.vue -->
    <template #header-content-left>
      <!-- Toggle Sidebar -->
      <button
        type="button"
        class="btn btn-sm btn-alt-secondary me-2 d-lg-none"
        @click="store.sidebar({ mode: 'toggle' })"
      >
        <i class="fa fa-fw fa-bars"></i>
      </button>
      <!-- END Toggle Sidebar -->

      <!-- Toggle Mini Sidebar -->
      <!-- <button
        type="button"
        class="btn btn-sm btn-alt-secondary me-2 d-none d-lg-inline-block"
        @click="store.sidebarMini({ mode: 'toggle' })"
      >
        <i class="fa fa-fw fa-ellipsis-v"></i>
      </button> -->
      <!-- END Toggle Mini Sidebar -->
    </template>
    <!-- END Header Content Left -->

    <!-- Header Content Right -->
    <!-- Using the available v-slot, we can override the default Header content from layouts/partials/Header.vue -->
    <template #header-content-right>
      <!-- Toggle Side Overlay -->
      <button
        type="button"
        class="btn btn-sm btn-alt-secondary p-2 px-3">
        {{ auth.fullname }}
      </button>
      <!-- END Toggle Side Overlay -->
    </template>
    <!-- END Header Content Right -->

    <!-- Footer Content Left -->
    <!-- Using the available v-slot, we can override the default Footer content from layouts/partials/Footer.vue -->
    <template #footer-content-left>
      <strong>{{ store.app.name }}</strong>
      &copy; {{ store.app.copyright }}
    </template>
    <!-- END Footer Content Left -->
  </BaseLayout>
</template>
