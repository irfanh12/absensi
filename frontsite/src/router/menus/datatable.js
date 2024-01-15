// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const KlienIndex = () => import("@/views/absensi/Klien/Index.vue");
const KlienForm = () => import("@/views/absensi/Klien/Form.vue");

const KaryawanIndex = () => import("@/views/absensi/Karyawan/Index.vue");
const KaryawanForm = () => import("@/views/absensi/Karyawan/Form.vue");

const TimesheetIndex = () => import("@/views/absensi/Timesheet/Index.vue");
const TimesheetForm = () => import("@/views/absensi/Timesheet/Form.vue");

const datatable = [
  {
    path: "/datatable",
    redirect: "/datatable/klien",
    component: LayoutBackend,
    children: [
      {
        path: "klien",
        children: [
          {
            path: "",
            name: "datatable-klien-index",
            component: KlienIndex,
          },
          {
            path: "/form/:id?",
            name: "datatable-klien-form",
            component: KlienForm,
          },
        ]
      },
      {
        path: "karyawan",
        children: [
          {
            path: "",
            name: "datatable-karyawan-index",
            component: KaryawanIndex,
          },
          {
            path: "/form/:id?",
            name: "datatable-karyawan-form",
            component: KaryawanForm,
          },
        ]
      },
      {
        path: "timesheet",
        children: [
          {
            path: "",
            name: "datatable-timesheet-index",
            component: TimesheetIndex,
          },
          {
            path: "/form/:id?",
            name: "datatable-timesheet-form",
            component: TimesheetForm,
          },
        ]
      },
    ],
  },
];

export default datatable;
