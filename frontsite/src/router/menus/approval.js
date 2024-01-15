// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const Approval = () => import("@/views/absensi/Klien/Index.vue");

const approval = [
  {
    path: "/approval",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "approval",
        component: Approval,
      },
    ],
  },
];

export default approval;
