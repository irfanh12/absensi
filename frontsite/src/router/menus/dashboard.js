// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const Dashboard = () => import("@/views/starter/DashboardView.vue");

const dashboard = [
  {
    path: "/dashboard",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "dashboard",
        component: Dashboard,
      },
    ],
  },
];

export default dashboard;
