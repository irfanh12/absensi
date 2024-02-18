<script setup>
import _ from "lodash";
import { onMounted, reactive, ref, watch } from "vue";
import * as PresensiController from "@/controllers/Presensi";
import { formatTimestamp } from "@/stores/utils"

/* PLUGINS */
// Vue Select, for more info and examples you can check out https://github.com/sagalbot/vue-select
import VueSelect from "vue-select";

// Auth Store
import { useAuth } from "@/stores/auth"
const auth = useAuth()
const permissions = auth.permissions()

let selected = ref(null);
const paging = ref(null)
const presensi = ref(null)
const modalDetails = ref(null)
const filterDate = ref(new Date())

let karyawan = reactive({
	loaded: false,
  lists: [],
	selected: null,
  presensis: [],
  filterDate: moment().format("YYYYMM01")
})

let table = reactive({
  lists: [],
  total: 0,
  page: 1,
  per_page: 15,
  last_page: 0,
})

let detail = reactive({
  direction: [],
  photos: [],
  times: [],
  date: '',
})

onMounted(() => {
  if(permissions.hasKaryawan()) {
		karyawan.selected = JSON.parse(localStorage.getItem('user'));
    loadDataPresensis()
  } else {
    loadDataKaryawan()
  }
  
  const myModalDetail = document.getElementById('modal-block-details')
  myModalDetail.addEventListener('hidden.bs.modal', () => {
    detail.directions = []
    detail.photos = []
    detail.times = []
    detail.date = ''
  })
});

watch(karyawan, async (newItem) => {
  if(!newItem.selected) {
    karyawan.selected = null
    karyawan.loaded = false
  }
})

watch(selected, async (newItem) => {
  if (newItem) {
    karyawan.selected = newItem
    loadDataPresensis()
  } else {
    karyawan.timesheets = []
    karyawan.canApproveReject = null
    karyawan.klien = null
    karyawan.selected = null
    karyawan.loaded = false
  }
})

/**
 * Performs a search for data based on a given keyword.
 *
 * @param {string} keyword - The keyword to search for.
 * @param {Function} loading - The loading function to update the loading state.
 * @return {void}
 */
const searchData = _.debounce((keyword, loading) => {
	if(keyword.length) {
    karyawan.lists = []
    karyawan.selected = null
    karyawan.loaded = false

    loading(true)
    presensi.value.statusLoading()

    PresensiController.searchData(keyword)
      .then((response) => {
        const { success, data } = response
        if(success) {
          karyawan.lists = data
          loading(false)
          presensi.value.statusNormal()
        }
      })
  }
}, 500)

async function loadDataKaryawan() {
  presensi.value.statusLoading()
  const { success, data } = await PresensiController.searchData(4);
  karyawan.lists = []
  if (success) {
    karyawan.lists = data
    presensi.value.statusNormal()
  }
}

/**
 * Debounces the filterPresensis function to delay its execution.
 *
 * @param {Date} date - The date to filter the presensis.
 * @return {void}
 */
const filterPresensis = _.debounce((date) => {
  karyawan.filterDate = moment(date).format("YYYYMM01")
  loadDataPresensis()
}, 500)

/**
 * Asynchronously loads presensis data from PresensiController
 *
 * @return {Object} Object with success and data properties
 */
async function loadDataPresensis() {
  karyawan.loaded = false
  presensi.value.statusLoading()
  
	const { success, data } = await PresensiController.listPresensi(karyawan.selected, karyawan.filterDate, table)
  if(success) {
    karyawan.loaded = true
    table.lists = data.data
    table.total = data.total
    table.page = data.current_page
    table.per_page = data.per_page
    table.last_page = data.last_page
    // table = data
    presensi.value.statusNormal()
  }
}

/**
 * Takes an item and performs various operations to display its details and location on a map.
 *
 * @param {Object} item - The item to be displayed
 * @return {void} 
 */
function lookItem(item) {
	console.log(item)
	detail.directions = item.directions
	detail.photos = item.photos
	detail.times = item.time
	// detail.date = moment(item.created_at).format('ddd, DD MMM YYYY')
	detail.date = formatTimestamp(item.created_at, 'ddd, DD MMM YYYY')

  modalDetails.value.statusLoading()

	if(detail.directions[0]) {
		new google.maps.Map(document.getElementById("mapResultStart"), {
			center: { lat: detail.directions[0]?.lat, lng: detail.directions[0]?.lng },
			draggable: false,
			zoomControl: false,
			scrollwheel: false,
			disableDoubleClickZoom: true,
			fullscreenControl: false,
			zoom: 15,
		})
	}

	if(detail.directions[1]) {
		new google.maps.Map(document.getElementById("mapResultEnd"), {
			center: { lat: detail.directions[1]?.lat, lng: detail.directions[1]?.lng },
			draggable: false,
			zoomControl: false,
			scrollwheel: false,
			disableDoubleClickZoom: true,
			fullscreenControl: false,
			zoom: 15,
		})
	}


  setTimeout(() => {
    modalDetails.value.statusNormal()
    document.querySelector('#mapResultStart > div:last-child').style.display = 'none';

		if(detail.directions[1]) {
    	document.querySelector('#mapResultEnd > div:last-child').style.display = 'none';
		}
  }, 1000)
}

/**
 * Asynchronously reports presensis by fetching presensi data for the selected 
 * karyawan and filterDate, then updates the UI accordingly. 
 *
 * @return {Promise} A Promise that resolves when the presensi data is 
 * fetched and the UI is updated.
 */
async function reportPresensis() {
  karyawan.loaded = false
  presensi.value.statusLoading()
  
	const { success, data } = await PresensiController.reportPresensi(karyawan.selected, karyawan.filterDate)
  if(success) {
    window.open(data.url_download, '_blank');

    karyawan.loaded = true
    presensi.value.statusNormal()
  }
}

function paginateClick(pageNumber) {
  console.log("Pagination Click clicked", pageNumber);

  paging.value.innerValue = pageNumber
  table.page = pageNumber;
  loadDataPresensis();
}
</script>

<style lang="scss">
  @import '@vuepic/vue-datepicker/dist/main.css';

	// Vue Select + Custom overrides
	@import "vue-select/dist/vue-select.css";
	@import "@/assets/scss/vendor/vue-select";
</style>

<style lang="scss" scoped>
.result {
  position: relative;

  .card {
    border-color: transparent;
		width: 50%;
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
    <BaseBlock ref="presensi" title="Card Presensi">
      <div class="mb-4" v-if="permissions.presensi_data.includes(auth.position)">
        <VueSelect
          v-model="selected"
          :options="karyawan.lists"
          label="fullname"
          placeholder="Cari Karyawan.." />
      </div>

      <div class="mb-4" v-if="karyawan.loaded">
        <h4 class="mb-2">Data Karyawan</h4>
        <ul class="list-group w-50">
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent">
            <span class="fw-bold">Nama Karyawan</span>
            <span>{{ karyawan.selected.fullname }}</span>
          </li>
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent">
            <span class="fw-bold">Status</span>
            <span>{{ karyawan.selected.user_type.type }}</span>
          </li>
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent">
            <span class="fw-bold">Posisi</span>
            <span>{{ karyawan.selected.position }}</span>
          </li>
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent"
            v-if="karyawan.selected.user_type.id === 5">
            <span class="fw-bold">Perusahaan</span>
            <span>{{ karyawan.selected.perusahaan.nama_perusahaan }}</span>
          </li>
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent">
            <span class="fw-bold">Alamat Email</span>
            <span>{{ karyawan.selected.email }}</span>
          </li>
          <li class="border-0 px-0 p-1 list-group-item d-flex justify-content-between align-items-center bg-transparent">
            <span class="fw-bold">No Telepon</span>
            <span>{{ karyawan.selected.phone_number }}</span>
          </li>
        </ul>
      </div>

      <div class="mb-4" v-if="karyawan.loaded">
        <div class="mb-2 d-flex align-items-center">
					<div class="me-auto">
						<h4 class="mb-0 me-auto">Tabel Presensi</h4>
					</div>
					<div>
						<div class="input-group flex-nowrap">
							<Datepicker v-model="filterDate" @update:model-value="filterPresensis" month-picker auto-apply />
							<button @click="reportPresensis" class="btn btn-success btn-sm" style="width: 70%;">
								<i class="fa fa-download"></i> Unduh Presensi
							</button>
						</div>
					</div>
        </div>
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th class="table-active text-center" style="width: 5%;">#</th>
              <th class="table-active" style="width: 20%;">Tanggal</th>
              <th class="table-active" style="width: 30%;">Status</th>
              <th class="table-active text-center" style="width: 10%;">Masuk</th>
              <th class="table-active text-center" style="width: 10%;">Pulang</th>
              <th class="table-active text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="table.lists.length === 0">
              <td colspan="6" class="text-center">No one record</td>
            </tr>
            <tr v-for="(presensi, key) in table.lists" :key="key+1">
              <td class="text-center">{{ key+1 }}</td>
              <td>{{ presensi.date }}</td>
              <td>
                <div class="d-flex gap-1 flex-wrap">
                  <span class="badge" v-for="(stats, key) in presensi.status_label" :key="key" :class="stats.class">{{ stats.label }}</span>
                </div>
              </td>
              <td>{{ presensi.time[0] ?? '-' }}</td>
              <td>{{ presensi.time[1] ?? '-' }}</td>
              <td>
                <div class="d-flex gap-2 justify-content-center align-items-center">
                  <button type="button" class="btn btn-sm btn-primary" @click="lookItem(presensi)" data-bs-toggle="modal" data-bs-target="#modal-block-details">
                    <i class="fa fa-eye"></i> Lihat
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <paginate ref="paging" v-model="table.page" :page-count="table.last_page" :click-handler="paginateClick" />
      </div>
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
      <div class="modal-dialog modal-lg modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="modalDetails" transparent >
            <template #title>
							<h4 class="mb-0">
								{{ karyawan.selected?.fullname }}<br>
								<small>{{ karyawan.selected?.position }}</small>
							</h4>
						</template>
						
            <template #options>
							<button
                type="button"
                class="btn-block-option"
                data-bs-dismiss="modal"
                aria-label="Close">
                <i class="fa fa-fw fa-times"></i>
              </button>
						</template>

            <template #content>
              <div class="result block-content fs-sm">
								<div class="d-flex gap-2 w-100">
									<div class="card">
										<div class="position-relative">
											<div id="mapResultStart" style="height: 250px" class="card-img-top"></div>
											<div class="marker position-absolute">
												<img src="@/assets/images/location-pin.png" alt="marker" style="width: 10%; opacity: .8;" />
											</div>
										</div>
										<div class="card-body">
											<p class="card-text">
												<div class="d-flex flex-column">
													<div class="d-flex flex-column justify-content-center align-items-center mb-2">
														<p >{{ detail.date }}, <span class="fw-bold text-success">{{ detail.times ? detail.times[0] : '-' }}</span></p>
													</div>
													<div class="d-flex flex-column justify-content-center align-items-center mb-2">
														<img :src="detail.photos[0]" alt="Result Attendance Photo" class="img-thumbnail mb-2" style="width: 250px" />
														<small>Attendance Photo</small>
													</div>
													
												</div>  
											</p>
										</div>
									</div>
									<div class="card" v-show="detail.times[1]">
										<div class="position-relative">
											<div id="mapResultEnd" style="height: 250px" class="card-img-top"></div>
											<div class="marker position-absolute">
												<img src="@/assets/images/location-pin.png" alt="marker" style="width: 10%; opacity: .8;" />
											</div>
										</div>
										<div class="card-body">
											<p class="card-text">
												<div class="d-flex flex-column">
													<div class="d-flex flex-column justify-content-center align-items-center mb-2">
														<p >{{ detail.date }}, <span class="fw-bold text-danger">{{ detail.times ? detail.times[1] : '-' }}</span></p>
													</div>
													<div class="d-flex flex-column justify-content-center align-items-center mb-2">
														<img :src="detail.photos[1]" alt="Result Attendance Photo" class="img-thumbnail mb-2" style="width: 250px" />
														<small>Attendance Photo</small>
													</div>
													
												</div>  
											</p>
										</div>
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




