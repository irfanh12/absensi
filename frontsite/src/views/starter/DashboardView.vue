<script setup>
// Vue Packages
import { onMounted, reactive } from "vue";
import { useRouter } from "vue-router";

import SimpleBar from "simplebar";
import { useAuth } from "@/stores/auth";

// Utils
import { startCamera, captureSnapshot } from "@/stores/utils";

// Auth store
const auth = useAuth();

// Now Date
const nowDate = moment().format("ddd, DD MMM YYYY");

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


// Current Location
let maps = reactive({
  lat: null,
  lng: null,
});

onMounted(() => {
  // SimpleBar
  new SimpleBar(document.getElementById("timesheet"));
  
  // Modals
  const myModal = document.getElementById('modal-block-popout');
  myModal.addEventListener('shown.bs.modal', () => {
    startCamera()
  })
  // getCurrentPosition()
  //   .then((pos) => {
  //     // Handle the position data here
  //     maps.lat = pos.coords.latitude;
  //     maps.lng = pos.coords.longitude;
  //     new google.maps.Map(document.getElementById("map"), {
  //       center: { lat: maps.lat, lng: maps.lng }, // Example location (San Francisco)
  //       zoom: 12, // Adjust the zoom level as needed
  //     });
  //   })
  //   .catch((error) => {
  //     // Handle any errors that occur
  //     console.error(error);
  //   });
})

function getCurrentPosition() {
  return new Promise((resolve, reject) => {
    navigator.geolocation.getCurrentPosition(resolve, reject);
  });
}
</script>

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
          <BaseBlock>
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
                <div class="fs-sm text-success">--:--</div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold">End Time</div>
                <div class="fs-sm text-danger">--:--</div>
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
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock title="Modal Title" transparent class="mb-0">
            <template #options>
              <button
                type="button"
                class="btn-block-option"
                data-bs-dismiss="modal"
                aria-label="Close"
              >
                <i class="fa fa-fw fa-times"></i>
              </button>
            </template>

            <template #content>
              <div class="block-content fs-sm">
                <button onclick="captureSnapshot()">Capture Snapshot</button>
                <video id="cameraPreview" autoplay playsinline></video>
                <img src="@/assets/images/siluet.svg" class="face ng-star-inserted">
              </div>
              <div class="block-content block-content-full text-end bg-body">
                <button
                  type="button"
                  class="btn btn-sm btn-alt-secondary me-1"
                  data-bs-dismiss="modal"
                >
                  Close
                </button>
                <button
                  type="button"
                  class="btn btn-sm btn-primary"
                  data-bs-dismiss="modal"
                >
                  Perfect
                </button>
              </div>
            </template>
          </BaseBlock>
        </div>
      </div>
    </div>
    <!-- END Pop Out Block Modal -->
  </div>
</template>
