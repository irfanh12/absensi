<script setup>
import { onMounted, reactive, ref } from "vue";
import * as PresensiController from "@/controllers/Presensi";
import { formatTimestamp } from "@/stores/utils"

const presensi = ref(null)
const modalDetails = ref(null)

let table = reactive({
  lists: [],
  total: 0,
  page: 1,
  per_page: 15,
  last_page: 0,
  type_id: 2,
  date: moment().format("YYYYMMDD"),
})

let detail = reactive({
  fullname: '',
  direction: {
    lat: null,
    lng: null,
  },
  photoUrl: '',
  date: '',
  time: '',
})

onMounted(() => {
  loadDataAndUpdateTable(table, presensi);

  const myModalDetail = document.getElementById('modal-block-details')
  myModalDetail.addEventListener('hidden.bs.modal', () => {
    detail.fullname = ''
    detail.direction.lat = ''
    detail.direction.lng = ''
    detail.photoUrl = ''
    detail.date = ''
    detail.time = ''
  })
});

/**
 * Update the items in the table with the provided data.
 *
 * @param {Object} table - The table object.
 * @param {Object} data - The data object containing the updated items.
 * @return {void}
 */
function updateTableItems(table, data) {
  table.lists = data.data;
  table.total = data.total;
  table.page = data.current_page;
  table.per_page = data.per_page;
  table.last_page = data.last_page;
}

/**
 * Loads data from the presensi and updates the table.
 *
 * @param {string} table - The table to update.
 * @param {object} presensi - The presensi object.
 * @return {Promise} A promise that resolves when the data is loaded and the table is updated.
 */
function loadDataAndUpdateTable(table, presensi) {
  return PresensiController.loadData(table, presensi)
    .then(respData => {
      const { success, data } = respData;
      
      if (success) {
        updateTableItems(table, data);
      }
    }).catch(error => {
      console.error(error);
    });
}


/**
 * A function that handles pagination click events.
 *
 * @param {number} pageNumber - The page number that was clicked.
 * @return {undefined} This function does not return a value.
 */
function paginateClick(pageNumber) {
  console.log("Pagination Click clicked");

  table.page = pageNumber;
  loadDataAndUpdateTable(table, presensi);
}

function lookItem(item) {
  const direction = JSON.parse(item.map_direction);
  modalDetails.value.statusLoading()

  detail.fullname = `${item.karyawan.first_name} ${item.karyawan.last_name}`
  detail.direction.lat = direction.lat
  detail.direction.lng = direction.lng
  detail.photoUrl = item.photo
  detail.date = formatTimestamp(item.created_at, 'ddd, DD MMM YYYY')
  detail.time = item.time

  new google.maps.Map(document.getElementById("mapResult"), {
    center: { lat: direction.lat, lng: direction.lng },
    draggable: false,
    zoomControl: false,
    scrollwheel: false,
    disableDoubleClickZoom: true,
    fullscreenControl: false,
    zoom: 15,
  })

  setTimeout(() => {
    modalDetails.value.statusNormal()
    document.querySelector('#mapResult > div:last-child').style.display = 'none';
  }, 1000)
}
</script>

<style lang="scss" scoped>
.result {
  position: relative;

  .card {
    border-color: transparent;
  }
}

.marker {
  top: 0;
  left: 0;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>

<template>
  <div class="content">
    <BaseBlock ref="presensi" title="Presensi" class="mb-0">
      <template #options>
        <button type="button" class="btn btn-sm btn-success">
          <i class="fa fa-download"></i> Download Report
        </button>
      </template>
      
      <table class="table table-hover table-bordered table-responsive">
        <thead>
          <tr>
            <th class="text-center table-active" style="width: 10px;">#</th>
            <th class="table-active" style="width: 200px;">Nama Karyawan</th>
            <th class="table-active">Hari</th>
            <th class="table-active">Status</th>
            <th class="table-active">Time At</th>
            <th class="table-active" style="width: 25%">Created At</th>
            <th class="text-center table-active" style="width: 15%">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-show="table.lists.length === 0">
            <td class="text-center" colspan="5">
              No records found.
            </td>
          </tr>
          <tr v-for="(item, key) in table.lists" :key="item.id" class="align-middle">
            <td>{{ key+1 }}</td>
            <td>{{ `${item.karyawan.first_name} ${item.karyawan.last_name}` }}</td>
            <td>{{ item.jamkerja.hari }}</td>
            <td class="text-end">{{ item.status }}</td>
            <td style="font-family: 'Courier New', Courier, monospace;" class="text-end">{{ item.time }}</td>
            <td style="font-family: 'Courier New', Courier, monospace;" class="text-end">{{ formatTimestamp(item.created_at) }}</td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center align-items-center">
                <button type="button" class="btn btn-sm btn-primary" @click="lookItem(item)" data-bs-toggle="modal" data-bs-target="#modal-block-details">
                  <i class="si si-files"></i> See Details
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <paginate v-show="table.lists.length > 0" :page-count="table.last_page" :click-handler="paginateClick">
      </paginate>
    </BaseBlock>

    <!-- Pop Out Block Modal -->
    <div
      class="modal fade"
      id="modal-block-details"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modal-block-details"
      aria-hidden="true"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="modalDetails" transparent class="mb-0">

            <template #content>
              <div class="result block-content fs-sm">
                <div class="card">
                  <div class="position-relative">
                    <div id="mapResult" style="height: 250px" class="card-img-top"></div>
                    <div class="marker position-absolute">
                      <img src="@/assets/images/location-pin.png" alt="marker" style="width: 10%; opacity: .8;" />
                    </div>
                  </div>
                  <div class="card-body">
                    <p class="card-text">
                      <div class="d-flex flex-column">
                        <div class="d-flex flex-column justify-content-center align-items-center mb-2">
                          <h3 class="fw-bold mb-0">{{ detail.fullname }}</h3>
                          <p class="mb-0">{{ detail.date }}, <span class="fw-bold text-primary">{{ detail.time }}</span></p>
                        </div>
                        <div class="d-flex flex-column justify-content-center align-items-center mb-2">
                          <img :src="detail.photoUrl" alt="Result Attendance Photo" class="img-thumbnail mb-2" style="width: 250px" />
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
                  data-bs-dismiss="modal">
                  Close
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




