<script setup>
// Vue Packages
import { onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";

import SimpleBar from "simplebar";
import { useAuth } from "@/stores/auth";

// Router
const router = useRouter();

// Utils
import { startCamera, stopCamera, captureSnapshot, justNowDate } from "@/stores/utils";

// Refs
const formcontrolCamera = ref(null);
const formcontrolMaps = ref(null);
const formcontrolResult = ref(null);

const presensiEmployee = ref(null);
const timesheetEmployee = ref(null);

const toast2 = ref(null);

// Auth store
const auth = useAuth();
const permissions = auth.permissions();

// Now Date
const nowDate = moment().format("ddd, DD MMM YYYY");
const nowDateWithOutYear = moment().format("ddd, DD MMM");

// Differential Date
const currentDate = new Date();
const dayOfWeek = currentDate.getDay();

let shift = {
  worktime: "-",
  label: "Shift OFF"
};

if (dayOfWeek >= 1 && dayOfWeek <= 5) {
  shift.label = "Office Hour";
  shift.worktime = "09:00 - 18:00";
}

// User Details
let user = localStorage.getItem("user");
user = JSON.parse(user);

// Data Reactive
let data = reactive({
  photo: {
    url: "",
    base64image: "",
  },
  map_direction: {
    lat: null,
    lng: null,
  },
  time: "",
});

let timesheet = reactive({
  lists: [],
  data: {
    remarks: "",
  }
})

// toastMessage Reactive
let toastNotif = reactive({
  date: moment(),
  message: '',
})

onMounted(() => {
  // Load Data

  let videoElement = document.getElementById('cameraPreview');
  // SimpleBar
  new SimpleBar(document.getElementById("timesheet"));
  
  // Modals
  const myModal = document.getElementById('modal-block-popout');
  const myModalDirection = document.getElementById('modal-block-direction');
  const myModalResult = document.getElementById('modal-block-result');

  // Event Listener
  myModal.addEventListener('shown.bs.modal', () => {
    startCamera(videoElement, formcontrolCamera)
  })

  myModal.addEventListener('hidden.bs.modal', () => {
    stopCamera(videoElement, formcontrolMaps)

    // Open New Modal
    const theModal = new bootstrap.Modal(myModalDirection)
    theModal.show();
  })


  myModalDirection.addEventListener('shown.bs.modal', async () => {
    try {
      const pos = await getCurrentPosition();
      const { latitude, longitude } = pos.coords;

      formcontrolMaps.value.statusLoading();

      data.map_direction.lat = latitude;
      data.map_direction.lng = longitude;
      
      new google.maps.Map(document.getElementById("map"), {
        center: { lat: latitude, lng: longitude },
        zoom: 12,
      });

      setTimeout(() => {
        formcontrolMaps.value.statusNormal();
      }, 1000);
    } catch (error) {
      // Handle any errors that occur
      console.error(error);
    }
  })

  myModalDirection.addEventListener('hidden.bs.modal', () => {
    data.time = moment().format("HH:mm")

    // Open New Modal
    const theModal = new bootstrap.Modal(myModalResult)
    theModal.show();
  })

  myModalResult.addEventListener('shown.bs.modal', () => {
    formcontrolResult.value.statusLoading();

    new google.maps.Map(document.getElementById("mapResult"), {
      center: { lat: data.map_direction.lat, lng: data.map_direction.lng },
      zoom: 12,
    });

    setTimeout(() => {
      formcontrolResult.value.statusNormal();
    }, 500);
  })

  myModalResult.addEventListener('hidden.bs.modal', () => {
    // Destroy Map
    document.getElementById('mapResult').innerHTML
  })

  const toastElement = document.getElementById("toast-example-2");
  toast2.value = new window.bootstrap.Toast(toastElement);

  getPresensiEmployee();
  getTimesheetEmployee();
})

function takeaction() {
  let videoElement = document.getElementById('cameraPreview');
  data.photo = captureSnapshot(videoElement);
}

function takedirection() {
  document.getElementById('map').innerHTML
}

function getCurrentPosition() {
  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });
}

async function getTimesheetEmployee() {
  timesheetEmployee.value.statusLoading()

  try {
    const dateNowTrim = moment().format("YYYYMMDD")
    const response = await axios.get(`api/v1/timesheet/employee`);
    const respData = response.data;

    if(respData.success) {
      timesheetEmployee.value.statusNormal()
  
      auth.setTimesheet(respData.data);
    }
  } catch (error) {
    console.error(error);
  }
}

async function getPresensiEmployee() {
  presensiEmployee.value.statusLoading()

  try {
    const dateNowTrim = moment().format("YYYYMMDD")
    const response = await axios.get(`api/v1/shift/presensi/employee/${dateNowTrim}`);
    const respData = response.data;

    if(respData.success) {
      presensiEmployee.value.statusNormal()
  
      const { start_time, end_time } = respData.data;
      auth.setPresensi(start_time, end_time);
    }
  } catch (error) {
    console.error(error);
  }
}

async function storePresensiEmployee() {
  try {    
    const dateNowTrim = moment().format("YYYYMMDD")
    const response = await axios.post(`api/v1/shift/presensi/employee/${dateNowTrim}`, data);
    const respData = response.data

    toastNotif.message = respData.message

    data = {
      photo: {
        url: "",
        base64image: "",
      },
      map_direction: {
        lat: null,
        lng: null,
      },
      time: "",
    }

    // Open Toast
    const toastElement = document.getElementById("toast-example-2");
    const toastInstance = bootstrap.Toast.getOrCreateInstance(toastElement);
    toastInstance.show();

    // Load Data Presensi Again
    getPresensiEmployee()
  } catch (error) {
    // Handle any errors that occur
    console.error(error);
  }
}

async function storeTimesheet() {
  try {
    const response = await axios.post(`api/v1/timesheet/store`, timesheet.data);
    const respData = response.data;

    toastNotif.message = respData.message;

    const modalElement = document.getElementById("modal-block-timesheet");
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    modalInstance.hide()

    if (!modalInstance._isShown) {
      timesheet.data.remarks = "";

      const toastElement = document.getElementById("toast-example-2");
      const toastInstance = bootstrap.Toast.getOrCreateInstance(toastElement);
      toastInstance.show();
    }
  } catch (error) {
    console.error(error);
  }
}
</script>

<style lang="scss" scoped>
.content.content-boxed {
  margin: 1.875rem;
}

.capture {
  position: relative; 
  display: flex;
  justify-content: center;
  padding: 1.25rem 1.25rem;

  #cameraPreview {
    width: 100%;
    max-height: 50%;
  }

  .face {
    position: absolute;
    width: 100%;
    padding: 0 20px;
  }
}

.result {
  position: relative;

  .card {
    border-color: transparent;
  }
}
</style>

<template>
  <div>
    <!-- Hero -->
      <BaseBackground
      image="/assets/media/photos/photo12@2x.jpg"
      inner-class="bg-black-50"
    >
      <div class="content content-full text-center">
        <div class="my-3">
          <img
            class="img-avatar img-avatar-thumb"
            src="/assets/media/avatars/avatar13.jpg"
            alt="Avatar"
          />
        </div>
        <h1 class="h2 text-white mb-0">{{ auth.fullname }}</h1>
        <span class="text-white-75">{{ auth.position }}</span>
      </div>
    </BaseBackground>
    <!-- END Hero -->

    <!-- Stats -->
    <!-- <div class="bg-body-extra-light">
      <div class="content content-boxed">
        <div class="row items-push text-center">
          <div class="col-6 col-md-3">
            <div class="fs-sm fw-semibold text-muted text-uppercase">Sales</div>
            <a class="link-fx fs-3" href="javascript:void(0)">17980</a>
          </div>
          <div class="col-6 col-md-3">
            <div class="fs-sm fw-semibold text-muted text-uppercase">
              Products
            </div>
            <a class="link-fx fs-3" href="javascript:void(0)">27</a>
          </div>
          <div class="col-6 col-md-3">
            <div class="fs-sm fw-semibold text-muted text-uppercase">
              Followers
            </div>
            <a class="link-fx fs-3" href="javascript:void(0)">1360</a>
          </div>
          <div class="col-6 col-md-3">
            <div class="fs-sm fw-semibold text-muted text-uppercase mb-2">
              739 Ratings
            </div>
            <span class="text-warning">
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star"></i>
              <i class="fa fa-star-half"></i>
            </span>
            <span class="fs-sm text-muted">(4.9)</span>
          </div>
        </div>
      </div>
    </div> -->
    <!-- END Stats -->

    <!-- Page Content -->
    <div class="content content-boxed">
      <div class="row">
        <div class="col-md-7 col-xl-8">
          <!-- Updates -->
          <BaseBlock id="timesheet" class="timesheet js-sidebar-scroll max-screen">
            <ul class="timeline timeline-alt py-0">
              <li class="timeline-event">
                <div class="timeline-event-icon bg-dark">
                  <i class="fa fa-cog"></i>
                </div>
                <BaseBlock title="System" class="timeline-event-block">
                  <template #options>
                    <div class="timeline-event-time block-options-item fs-sm">
                      Just Now
                    </div>
                  </template>
  
                  <p>You must wait for approval of the timesheet from the client.</p>
                </BaseBlock>
              </li>
            </ul>
          </BaseBlock>
          <!-- END Updates -->
        </div>
        <div class="col-md-5 col-xl-4">
          <!-- Products -->
          <BaseBlock ref="presensiEmployee">
            <template #title>
              <i class="fa fa-briefcase text-muted me-1"></i> Attendance
            </template>

            <div class="d-flex align-items-center push">
              <div class="flex-grow-1">
                <div class="fw-semibold">Today ({{ nowDate }})</div>
                <div class="fs-sm">Shift: {{ shift.label }} [ {{ shift.worktime }} ]</div>
              </div>
            </div>
            <div class="d-flex align-items-center push">
              <div class="flex-grow-0 me-4">
                <div class="fw-semibold">Start Time</div>
                <div class="fs-sm fw-bold text-success">{{ auth.presensi.start_time }}</div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold">End Time</div>
                <div class="fs-sm fw-bold text-danger">{{ auth.presensi.end_time }}</div>
              </div>
            </div>
            <div class="text-center push w-100">
              <button type="button" class="btn btn-sm btn-alt-primary w-100" data-bs-toggle="modal" data-bs-target="#modal-block-popout">
                Record Time
              </button>
            </div>
          </BaseBlock>
          <!-- END Products -->

          <!-- Ratings -->
          <BaseBlock ref="timesheetEmployee" v-show="permissions.timesheet.includes(auth.position)">
            <template #title>
              <i class="si si-info text-muted me-1"></i> Timesheet
            </template>

            <div class="fs-sm push" v-for="timesheet in auth.timesheets" :key="timesheet">
              <div class="d-flex justify-content-between mb-2">
                <div class="space-x-1 w-100 d-flex justify-content-between align-items-center">
                  <span class="fw-semibold text-gray-lighter">{{ nowDateWithOutYear }}</span>
                  <span class="badge rounded-pill text-bg-primary" :class="timesheet.status.class">{{ timesheet.status.label }}</span>
                </div>
                <!-- <div class="text-warning">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div> -->
              </div>
              <p class="mb-0 text-gray">{{ timesheet.remarks }}</p>
            </div>
            <div class="text-center push w-100">
              <button type="button" class="btn btn-sm btn-info w-100" data-bs-toggle="modal" data-bs-target="#modal-block-timesheet">
                Add Timesheet
              </button>
            </div>
          </BaseBlock>
          <!-- END Ratings -->

          <!-- Ratings -->
          <BaseBlock>
            <template #title>
              <i class="si si-info text-muted me-1"></i> Notifications
            </template>

            <!-- <div class="fs-sm push">
              <div class="d-flex justify-content-between mb-2">
                <div class="space-x-1">
                  <a class="fw-semibold" href="">Alice Moore</a>
                  <span class="text-muted">(5/5)</span>
                </div>
                <div class="text-warning">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div>
              </div>
              <p class="mb-0">
                Flawless design execution! I'm really impressed with the product,
                it really helped me build my app so fast! Thank you!
              </p>
            </div>
            <div class="fs-sm push">
              <div class="d-flex justify-content-between mb-2">
                <div class="space-x-1">
                  <a class="fw-semibold" href="">Danielle Jones</a>
                  <span class="text-muted">(5/5)</span>
                </div>
                <div class="text-warning">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div>
              </div>
              <p class="mb-0">
                Great value for money and awesome support! Would buy again and
                again! Thanks!
              </p>
            </div>
            <div class="fs-sm push">
              <div class="d-flex justify-content-between mb-2">
                <div class="space-x-1">
                  <a class="fw-semibold" href="">Ryan Flores</a>
                  <span class="text-muted">(5/5)</span>
                </div>
                <div class="text-warning">
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                  <i class="fa fa-star"></i>
                </div>
              </div>
              <p class="mb-0">
                Working great in all my devices, quality and quantity in a great
                package! Thank you!
              </p>
            </div> -->
            <div class="text-center push">
              <button type="button" class="btn btn-sm btn-alt-secondary">
                TBD
              </button>
            </div>
          </BaseBlock>
          <!-- END Ratings -->

          <!-- Followers -->
          <BaseBlock>
            <template #title>
              <i class="fa fa-share-alt text-muted me-1"></i> Feature 2
            </template>

            <!-- <ul class="nav-items fs-sm">
              <li>
                <a class="d-flex py-2" href="javascript:void(0)">
                  <div
                    class="flex-shrink-0 me-3 ms-2 overlay-container overlay-bottom"
                  >
                    <img
                      class="img-avatar img-avatar48"
                      src="/assets/media/avatars/avatar6.jpg"
                      alt=""
                    />
                    <span
                      class="overlay-item item item-tiny item-circle border border-2 border-white bg-success"
                    ></span>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-semibold">Laura Carr</div>
                    <div class="fw-normal text-muted">Copywriter</div>
                  </div>
                </a>
              </li>
              <li>
                <a class="d-flex py-2" href="javascript:void(0)">
                  <div
                    class="flex-shrink-0 me-3 ms-2 overlay-container overlay-bottom"
                  >
                    <img
                      class="img-avatar img-avatar48"
                      src="/assets/media/avatars/avatar11.jpg"
                      alt=""
                    />
                    <span
                      class="overlay-item item item-tiny item-circle border border-2 border-white bg-success"
                    ></span>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-semibold">Ryan Flores</div>
                    <div class="fw-normal text-muted">Web Developer</div>
                  </div>
                </a>
              </li>
              <li>
                <a class="d-flex py-2" href="javascript:void(0)">
                  <div
                    class="flex-shrink-0 me-3 ms-2 overlay-container overlay-bottom"
                  >
                    <img
                      class="img-avatar img-avatar48"
                      src="/assets/media/avatars/avatar3.jpg"
                      alt=""
                    />
                    <span
                      class="overlay-item item item-tiny item-circle border border-2 border-white bg-warning"
                    ></span>
                  </div>
                  <div class="flex-grow-1">
                    <div class="fw-semibold">Marie Duncan</div>
                    <div class="fw-normal text-muted">Web Designer</div>
                  </div>
                </a>
              </li>
            </ul> -->
            <div class="text-center push">
              <button type="button" class="btn btn-sm btn-alt-secondary">
                TBD
              </button>
            </div>
          </BaseBlock>
          <!-- END Followers -->
        </div>
      </div>
    </div>
    <!-- END Page Content -->

    <!-- Pop Out Block Modal -->
    <div
      class="modal fade"
      id="modal-block-popout"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modal-block-popout"
      aria-hidden="true"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="formcontrolCamera" transparent class="mb-0">
            <!-- <template #options>
              <button
                type="button"
                class="btn-block-option"
                data-bs-dismiss="modal"
                aria-label="Close"
              >
                <i class="fa fa-fw fa-times"></i>
              </button>
            </template> -->

            <template #content>
              <div class="capture block-content fs-sm">
                <video id="cameraPreview" autoplay playsinline></video>
              </div>
              <div class="block-content block-content-full text-end bg-body d-flex justify-content-center">
                <button
                  type="button"
                  class="btn btn-sm btn-primary w-100"
                  data-bs-dismiss="modal"
                  @click="takeaction()">
                  Take A Snapshot
                </button>
              </div>
            </template>
          </BaseBlock>
        </div>
      </div>
    </div>
    <!-- END Pop Out Block Modal -->
    
    <!-- Pop Out Block Modal -->
    <div
      class="modal fade"
      id="modal-block-direction"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modal-block-direction"
      aria-hidden="true"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="formcontrolMaps" transparent class="mb-0">

            <template #content>
              <div class="block-content fs-sm">
                <div id="map" style="height: 400px;"></div>
              </div>
              <div class="block-content block-content-full text-end bg-body d-flex justify-content-center">
                <button
                  type="button"
                  class="btn btn-sm btn-primary w-100"
                  data-bs-dismiss="modal"
                  @click="takedirection()">
                  Take A Direction
                </button>
              </div>
            </template>
          </BaseBlock>
        </div>
      </div>
    </div>
    <!-- END Pop Out Block Modal -->

    <!-- Pop Out Block Modal -->
    <div
      class="modal fade"
      id="modal-block-result"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modal-block-result"
      aria-hidden="true"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="formcontrolResult" transparent class="mb-0">

            <template #content>
              <div class="result block-content fs-sm">
                <div class="card">
                  <div id="mapResult" style="height: 250px;" class="card-img-top"></div>
                  <div class="card-body">
                    <p class="card-text">
                      <div class="d-flex flex-column">
                        <div class="d-flex flex-column justify-content-center align-items-center mb-2">
                          <h3 class="fw-bold mb-0">{{ auth.fullname }}</h3>
                          <p class="mb-0">{{ nowDate }}, <span class="fw-bold text-primary">{{ data.time }}</span></p>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center mb-2">
                          <img :src="data.photo.url" alt="Result Attendance Photo" class="img-thumbnail mb-2" style="width: 250px" />
                          <small>Attendance Photo</small>
                        </div>
                        
                      </div>  
                    </p>
                  </div>
                </div>
              </div>
              <div class="block-content block-content-full text-end bg-body d-flex justify-content-center">
                <button
                  type="button"
                  class="btn btn-sm btn-primary w-100"
                  data-bs-dismiss="modal"
                  @click="storePresensiEmployee()">
                  Save Attendance
                </button>
              </div>
            </template>
          </BaseBlock>
        </div>
      </div>
    </div>
    <!-- END Pop Out Block Modal -->

    <!-- Pop Out Block Modal -->
    <div
      class="modal fade"
      id="modal-block-timesheet"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modal-block-timesheet"
      aria-hidden="true"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock title="Add Timesheet" transparent class="mb-0">

            <template #content>
              <form @submit.prevent="storeTimesheet()">
                <div class="result block-content fs-sm p-0">
                  <div class="card">
                    <div class="card-body">
                      <label class="form-label" for="example-textarea-input">Remarks</label>
                      <textarea
                        class="form-control"
                        id="example-textarea-input"
                        name="example-textarea-input"
                        rows="7"
                        placeholder="Remarks content.."
                        v-model="timesheet.data.remarks"
                      ></textarea>
                    </div>
                  </div>
                </div>
                <div class="block-content block-content-full text-end bg-body d-flex justify-content-center">
                  <button
                    type="submit"
                    class="btn btn-sm btn-primary w-100">
                    Save Timesheet
                  </button>
                </div>
              </form>
            </template>
          </BaseBlock>
        </div>
      </div>
    </div>
    <!-- END Pop Out Block Modal -->

    <div class="position-fixed bottom-0 end-0 p-3 space-y-3" style="z-index: 9999">
      <!-- Toast Example 2 -->
      <div
        id="toast-example-2"
        class="toast fade hide"
        data-delay="4000"
        role="alert"
        aria-live="assertive"
        aria-atomic="true">
        <div class="toast-header">
          <i class="si si-wrench text-dark me-2"></i>
          <strong class="me-auto">System</strong>
          <small class="text-muted">{{ justNowDate(toastNotif.date) }}</small>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="toast"
            aria-label="Close"
          ></button>
        </div>
        <div class="toast-body">
          {{ toastNotif.message }}
        </div>
      </div>
      <!-- END Toast Example 2 -->
    </div>
  </div>
</template>
