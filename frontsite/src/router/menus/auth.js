// Route
import LayoutSimple from "@/layouts/variations/Simple.vue";

const AuthLogin = () => import("@/views/absensi/Auth/SignInView.vue");
const AuthLogout = () => import("@/views/absensi/Auth/LogoutView.vue");

const auth = [
  {
    path: "/",
    component: LayoutSimple,
    children: [
      {
        path: "",
        name: "login",
        component: AuthLogin,
      },
      {
        path: "",
        name: "logout",
        component: AuthLogout,
      },
    ],
  },
];

export default auth;
