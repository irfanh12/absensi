// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const PresensiIndex = () => import("@/views/absensi/Presensi/Index.vue");
const PresensiForm = () => import("@/views/absensi/Presensi/Form.vue");

const presensi = [
  {
    path: "/presensi",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "presensi-index",
        component: PresensiIndex,
      },
      {
        path: "form/:id?",
        name: "presensi-form",
        component: PresensiForm,
      },
    ],
  },
];

export default presensi;
