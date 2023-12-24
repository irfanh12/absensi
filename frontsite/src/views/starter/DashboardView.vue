<script setup>
// Vue Packages
import { onMounted, reactive } from "vue";
import { useRouter } from "vue-router";

import { useTemplateStore } from "@/stores/template";

// Main store
const store = useTemplateStore();

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
  getCurrentPosition()
    .then((pos) => {
      // Handle the position data here
      maps.lat = pos.coords.latitude;
      maps.lng = pos.coords.longitude;
      new google.maps.Map(document.getElementById("map"), {
        center: { lat: maps.lat, lng: maps.lng }, // Example location (San Francisco)
        zoom: 12, // Adjust the zoom level as needed
      });
    })
    .catch((error) => {
      // Handle any errors that occur
      console.error(error);
    });
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
        <h1 class="h2 text-white mb-0">{{ store.app.fullname }}</h1>
        <span class="text-white-75">{{ store.app.position }}</span>
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
          <ul class="timeline timeline-alt py-0">
            <li class="timeline-event">
              <div class="timeline-event-icon bg-default">
                <i class="fab fa-facebook-f"></i>
              </div>
              <BaseBlock title="Facebook" class="timeline-event-block">
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    just now
                  </div>
                </template>

                <p class="fw-semibold mb-2">+ 290 Page Likes</p>
                <p>This is great, keep it up!</p>
              </BaseBlock>
            </li>
            <li class="timeline-event">
              <div class="timeline-event-icon bg-modern">
                <i class="fa fa-briefcase"></i>
              </div>
              <BaseBlock title="Products" class="timeline-event-block">
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    4 hrs ago
                  </div>
                </template>

                <p class="fw-semibold mb-2">3 New Products were added!</p>
                <div class="d-flex push">
                  <a
                    class="item item-rounded bg-info me-2"
                    href="javascript:void(0)"
                  >
                    <i class="si si-rocket fa-2x text-white-75"></i>
                  </a>
                  <a
                    class="item item-rounded bg-amethyst me-2"
                    href="javascript:void(0)"
                  >
                    <i class="si si-calendar fa-2x text-white-75"></i>
                  </a>
                  <a
                    class="item item-rounded bg-city me-2"
                    href="javascript:void(0)"
                  >
                    <i class="si si-speedometer fa-2x text-white-75"></i>
                  </a>
                </div>
              </BaseBlock>
            </li>
            <li class="timeline-event">
              <div class="timeline-event-icon bg-info">
                <i class="fab fa-twitter"></i>
              </div>
              <BaseBlock title="Twitter" class="timeline-event-block">
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    12 hrs ago
                  </div>
                </template>

                <p class="fw-semibold mb-2">+ 1150 Followers</p>
                <p>You’re getting more and more followers, keep it up!</p>
              </BaseBlock>
            </li>
            <li class="timeline-event">
              <div class="timeline-event-icon bg-smooth">
                <i class="fa fa-database"></i>
              </div>
              <BaseBlock title="Backup" class="timeline-event-block">
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    1 day ago
                  </div>
                </template>

                <p class="fw-semibold mb-2">Database backup completed!</p>
                <p>
                  Download the <a href="javascript:void(0)">latest backup</a>.
                </p>
              </BaseBlock>
            </li>
            <li class="timeline-event">
              <div class="timeline-event-icon bg-dark">
                <i class="fa fa-cog"></i>
              </div>
              <BaseBlock title="System" class="timeline-event-block">
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    1 week ago
                  </div>
                </template>

                <p class="fw-semibold mb-2">App updated to v2.02</p>
                <p>
                  Check the complete changelog at the
                  <a href="javascript:void(0)">activity page</a>.
                </p>
              </BaseBlock>
            </li>
            <li class="timeline-event">
              <div class="timeline-event-icon bg-modern">
                <i class="fa fa-briefcase"></i>
              </div>
              <BaseBlock
                title="Products"
                class="timeline-event-block"
                content-full
              >
                <template #options>
                  <div class="timeline-event-time block-options-item fs-sm">
                    2 months ago
                  </div>
                </template>

                <p class="fw-semibold mb-2">1 New Product was added!</p>
                <a class="item item-rounded bg-muted" href="javascript:void(0)">
                  <i class="si si-wallet fa-2x text-white-75"></i>
                </a>
              </BaseBlock>
            </li>
          </ul>
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
              <button type="button" class="btn btn-sm btn-alt-primary w-100">
                Record Time
              </button>
            </div>
          </BaseBlock>
          <!-- END Products -->

          <!-- Ratings -->
          <BaseBlock>
            <template #title>
              <i class="fa fa-pencil-alt text-muted me-1"></i> Feature 1
            </template>

            <div id="map" style="height: 400px;"></div>

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
  </div>
</template>
