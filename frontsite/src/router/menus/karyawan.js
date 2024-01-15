// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const KaryawanIndex = () => import("@/views/absensi/Karyawan/Index.vue");
const KaryawanForm = () => import("@/views/absensi/Karyawan/Form.vue");

const karyawan = [
  {
    path: "/karyawan",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "karyawan-index",
        component: KaryawanIndex,
      },
      {
        path: "form/:id?",
        name: "karyawan-form",
        component: KaryawanForm,
      },
    ],
  },
];

export default karyawan;
