<script setup>
import _ from "lodash";
import { onMounted, reactive, ref, watch } from "vue";
import * as TimesheetController from "@/controllers/Timesheet";
import { formatTimestamp } from "@/stores/utils"

/* PLUGINS */
// Vue Select, for more info and examples you can check out https://github.com/sagalbot/vue-select
import VueSelect from "vue-select";

// Auth Store
import { useAuth } from "@/stores/auth"
const auth = useAuth()
const permissions = auth.permissions()

let selected = ref(null);
const paging = ref(null);
const timesheet = ref(null)
const modalDetails = ref(null)
const filterDate = ref(new Date())

let karyawan = reactive({
  loaded: false,
  canApproveReject: false,
  lists: [],
  selected: null,
  klien: null,
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
  store_timesheet: false,
  data: {
    remarks: "",
  }
})

onMounted(() => {
  loadDataKaryawan();
  if (permissions.hasKaryawan()) {
    karyawan.selected = JSON.parse(localStorage.getItem('user'));
    loadDataTimesheets()
  }
});

watch(karyawan, async (newItem) => {
  if (!newItem.selected) {
    karyawan.canApproveReject = null
    karyawan.klien = null
    karyawan.selected = null
    karyawan.loaded = false
  }
})

watch(selected, async (newItem) => {
  if (newItem) {
    karyawan.selected = newItem
    loadDataTimesheets()
  } else {
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
  if (keyword.length) {
    karyawan.lists = []
    karyawan.canApproveReject = null
    karyawan.klien = null
    karyawan.loaded = false

    loading(true)
    timesheet.value.statusLoading()

    TimesheetController.searchData(keyword)
      .then((response) => {
        const { success, data } = response
        if (success) {
          karyawan.lists = data
          loading(false)
          timesheet.value.statusNormal()
        }
      })
  }
}, 500)

/**
 * Debounces the filterTimesheets function to delay its execution.
 *
 * @param {Date} date - The date to filter the timesheets.
 * @return {void}
 */
const filterTimesheets = _.debounce((date) => {
  karyawan.filterDate = moment(date).format("YYYYMM01")
  loadDataTimesheets()
}, 500)

async function loadDataKaryawan() {
  timesheet.value.statusLoading()
  const { success, data } = await TimesheetController.searchData(null);
  if (success) {
    karyawan.lists = data
    timesheet.value.statusNormal()
  }
}

/**
 * Asynchronously loads timesheets data from TimesheetController
 *
 * @return {Object} Object with success and data properties
 */
async function loadDataTimesheets() {
  karyawan.loaded = false
  timesheet.value.statusLoading()

  const { success, data } = await TimesheetController.listTimesheet(karyawan.selected, karyawan.filterDate, table)
  if (success) {
    karyawan.loaded = true
    karyawan.klien = data.klien
    karyawan.canApproveReject = data.canApproveReject
    updateTableItems(table, data);
    timesheet.value.statusNormal()
  }
}

function updateTableItems(table, data) {
  table.lists = data.data;
  table.total = data.total;
  table.page = data.current_page;
  table.per_page = data.per_page;
  table.last_page = data.last_page;
}

/**
 * Asynchronously approves the item after confirming with the user.
 *
 * @return {void} 
 */
async function approveItem() {
  const shouldApprove = confirm("Are you sure you want to approve this timesheet?");

  if (!shouldApprove) {
    return;
  }

  try {
    const response = await TimesheetController.approveTimesheet(karyawan.selected, karyawan.filterDate);

    const { success } = response;

    if (success) {
      loadDataTimesheets();
    }
  } catch (error) {
    console.error(error);
  }
}

/**
 * Asynchronously rejects an item after confirming with the user and handling
 * message input validation. If the rejection is successful, it triggers a
 * reload of the timesheets data.
 *
 * @return {Promise<void>} A promise that resolves when the rejection process
 * is completed.
 */
async function rejectItem() {
  const shouldReject = confirm("Apakah Anda yakin ingin menolak timesheet ini?");

  if (!shouldReject) {
    return;
  }

  let message = prompt("Silakan masukkan pesan Anda:");

  while (message === '') {
    alert("Pesan tidak boleh kosong.");
    message = prompt("Silakan masukkan pesan Anda:");
  }

  if (message) {
    try {
      const respData = await TimesheetController.rejectTimesheet(karyawan.selected, karyawan.filterDate);

      const { success } = respData;

      if (success) {
        loadDataTimesheets()
      }
    } catch (error) {
      console.error(error);
    }
  }
}

/**
 * Asynchronously reports timesheets by fetching timesheet data for the selected 
 * karyawan and filterDate, then updates the UI accordingly. 
 *
 * @return {Promise} A Promise that resolves when the timesheet data is 
 * fetched and the UI is updated.
 */
async function reportTimesheets() {
  karyawan.loaded = false
  timesheet.value.statusLoading()

  const { success, data } = await TimesheetController.reportTimesheet(karyawan.selected, karyawan.filterDate)
  if (success) {
    window.open(data.url_download, '_blank');

    karyawan.loaded = true
    timesheet.value.statusNormal()
  }
}

function paginateClick(pageNumber) {
  console.log("Pagination Click clicked", pageNumber);

  paging.value.innerValue = pageNumber
  table.page = pageNumber;
  loadDataTimesheets();
}

async function storeTimesheet() {
  try {
    modalDetails.value.statusLoading()
    const response = await axios.post(`api/v1/timesheet/store`, {
      id: detail.data.id ? detail.data.id : null,
      remarks: detail.data.remarks,
    })

    const { success, message } = response.data

    const modalElement = document.getElementById("modal-block-details")
    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement)
    modalInstance.hide()

    if (success) {
      detail.data.remarks = ""
      modalDetails.value.statusNormal()
      loadDataTimesheets();
    }
  } catch (error) {
    console.error(error)
  }
}

function lookItem(item) {
  detail.data = {
    id: item.id,
    remarks: item.remarks,
    revision: item.revision,
  }
}

</script>

<style lang="scss">
@import '@vuepic/vue-datepicker/dist/main.css';

// Vue Select + Custom overrides
@import "vue-select/dist/vue-select.css";
@import "@/assets/scss/vendor/vue-select";
</style>

<template>
  <div class="content">
    <BaseBlock ref="timesheet" title="Card Timesheet">
      <div class="mb-4" v-if="permissions.timesheet_data.includes(auth.position)">
        <VueSelect
          v-model="selected"
          :options="karyawan.lists"
          label="fullname"
          placeholder="Cari Karyawan.." />
      </div>

      <div class="mb-4 d-flex gap-4" v-if="karyawan.loaded">

        <div class="data-karyawan w-50">
          <h4 class="mb-2">Data Karyawan</h4>
          <ul class="list-group">
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

        <div class="data-revision w-50">
          <h4 class="mb-2">Revisi</h4>
          <!-- <figure v-if="timesheet.data.revision">
            <label class="form-label" for="example-textarea-input">Revision</label>
            <blockquote class="blockquote">
              <p><em>{{ timesheet.data.revision.remark_revision }}</em></p>
            </blockquote>
            <figcaption class="blockquote-footer">
              {{ `${timesheet.data.revision.karyawan.first_name} ${timesheet.data.revision.karyawan.last_name}` }}
            </figcaption>
          </figure> -->
        </div>

      </div>

      <div class="mb-4" v-if="karyawan.loaded">
        <div class="mb-2 d-flex align-items-center gap-2">
          <div class="me-auto">
            <h4 class="mb-0 me-auto">Tabel Timesheet</h4>
          </div>
          
          <div v-show="!permissions.hasKaryawan()" v-if="karyawan.canApproveReject">
            <div class="input-group flex-nowrap">
              <button @click="approveItem" class="btn btn-success">
                <i class="fa fa-file-lines"></i> Terima
              </button>
              <button @click="rejectItem" class="btn btn-danger">
                <i class="fa fa-xmark"></i> Tolak
              </button>
            </div>
          </div>

          <div>
            <div class="input-group flex-nowrap">
              <Datepicker v-model="filterDate" @update:model-value="filterTimesheets" month-picker auto-apply />
              <button @click="reportTimesheets" class="btn btn-success btn-sm" style="width: 70%;">
                <i class="fa fa-download"></i> Unduh Timesheet
              </button>
            </div>
          </div>
        </div>
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th class="table-active text-center" style="width: 5%;">#</th>
              <th class="table-active" style="width: 20%;">Tanggal</th>
              <th class="table-active" style="width: 15%;">Klien</th>
              <th class="table-active text-center" style="width: 10%;">Status</th>
              <th class="table-active">Kegiatan</th>
              <th class="table-active text-center" style="width: 20%;" v-show="permissions.hasKaryawan()">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="table.lists.length === 0">
              <td :colspan="permissions.hasKaryawan() ? 6 : 4" class="text-center">No one record</td>
            </tr>
            <tr v-for="(sheet, key) in table.lists" :key="key + 1">
              <td class="text-center">{{ key + 1 }}</td>
              <td>{{ formatTimestamp(sheet.created_at, 'DD MMM YYYY') }}</td>
              <td>{{ karyawan.klien.fullname }}</td>
              <td class="text-center">
                <div class="d-flex gap-1 flex-wrap">
                  <span class="badge" :class="sheet.status.class">{{ sheet.status.label }}</span>
                </div>
              </td>
              <td>{{ sheet.remarks }}</td>
              <td v-show="permissions.hasKaryawan()">
                <div class="d-flex gap-2 justify-content-center align-items-center" v-if="sheet.status.label.includes('Pending')">
                  <button type="button" class="btn btn-sm btn-primary" @click="lookItem(sheet)" data-bs-toggle="modal" data-bs-target="#modal-block-details">
                    <i class="fa fa-pencil"></i> Sunting
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
      <div class="modal-dialog modal-dialog-popout modal-dialog-centered" role="document">
        <div class="modal-content">
          <BaseBlock ref="modalDetails" transparent class="mb-0">
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
              <form @submit.prevent="storeTimesheet()">
                <div class="result block-content fs-sm p-0">
                  <div class="card">
                    <div class="card-body">
                      <!-- <figure v-if="detail.data.revision">
                        <label class="form-label" for="example-textarea-input">Revision</label>
                        <blockquote class="blockquote">
                          <p><em>{{ detail.data.revision.remark_revision }}</em></p>
                        </blockquote>
                        <figcaption class="blockquote-footer">
                          {{ `${detail.data.revision.karyawan.first_name} ${detail.data.revision.karyawan.last_name}` }}
                        </figcaption>
                      </figure> -->
                      <label class="form-label" for="example-textarea-input">Remarks</label>
                      <textarea
                        class="form-control"
                        id="example-textarea-input"
                        name="example-textarea-input"
                        rows="7"
                        placeholder="Remarks content.."
                        v-model="detail.data.remarks"
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
  </div>
</template>




