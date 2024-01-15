// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const KlienIndex = () => import("@/views/absensi/Klien/Index.vue");
const KlienForm = () => import("@/views/absensi/Klien/Form.vue");

const klien = [
  {
    path: "/klien",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "klien-index",
        component: KlienIndex,
      },
      {
        path: "form/:id?",
        name: "klien-form",
        component: KlienForm,
      },
    ],
  },
];

export default klien;
