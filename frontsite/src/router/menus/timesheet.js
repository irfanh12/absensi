// Route
import LayoutBackend from "@/layouts/variations/BackendStarter.vue";

const TimesheetIndex = () => import("@/views/absensi/Timesheet/Index.vue");
const TimesheetForm = () => import("@/views/absensi/Timesheet/Form.vue");

const timesheet = [
  {
    path: "/timesheet",
    component: LayoutBackend,
    children: [
      {
        path: "",
        name: "timesheet-index",
        component: TimesheetIndex,
      },
      {
        path: "form/:id?",
        name: "timesheet-form",
        component: TimesheetForm,
      },
    ],
  },
];

export default timesheet;
