import { createRouter, createWebHashHistory } from "vue-router";
import checkSession from "@/stores/utils";
import NProgress from "nprogress/nprogress.js";

// AUTH
import auth from "@/router/menus/auth";
import dashboard from "@/router/menus/dashboard";

// Set all routes
const routes = [
  ...auth,
  ...dashboard,
]

// Create Router
const router = createRouter({
  history: createWebHashHistory(),
  linkActiveClass: "active",
  linkExactActiveClass: "active",
  scrollBehavior() {
    return { left: 0, top: 0 };
  },
  routes,
});

// NProgress
/*eslint-disable no-unused-vars*/
router.beforeEach(async (to, from, next) => {
  NProgress.start();
  let token = localStorage.getItem("token");

  if (to.name === from.name) {
    next(false);
    return;
  }

  if (token) {
    const isValidSession = await checkSession(token);

    if (isValidSession && to.name == 'login') next({ name: 'dashboard' });
    else if (!isValidSession && to.name !== 'login') next({ name: 'login' });
    else next();
  } else {
    if (to.name !== 'login') next({ name: 'login' });
    else {
      next();
    }
  }
});

router.afterEach((to, from) => {
  NProgress.done();
});
/*eslint-enable no-unused-vars*/

export default router;
