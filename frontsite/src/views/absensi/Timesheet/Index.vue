<script setup>
import { onMounted, reactive, ref } from "vue";
import * as TimesheetController from "@/controllers/Timesheet";
import { formatTimestamp } from "@/stores/utils"

import { useAuth } from "@/stores/auth"

const timesheet = ref(null)

const auth = useAuth()
const permissions = auth.permissions()

let table = reactive({
  lists: [],
  total: 0,
  page: 1,
  per_page: 15,
  last_page: 0
})

let canApproveStatus = '';

onMounted(() => {
  canApproveStatus = auth.position.includes('Klien') ? 'Pending' : 'Approved Client';
  loadDataAndUpdateTable(table, timesheet);
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
 * Loads data from the timesheet and updates the table.
 *
 * @param {string} table - The table to update.
 * @param {object} timesheet - The timesheet object.
 * @return {Promise} A promise that resolves when the data is loaded and the table is updated.
 */
function loadDataAndUpdateTable(table, timesheet) {
  return TimesheetController.loadData(table, timesheet)
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
  loadDataAndUpdateTable(table, timesheet);
}

function downloadReport() {
  
}

async function approveItem(timesheetId) {
  const shouldApprove = confirm("Are you sure you want to approve this timesheet?");
  
  if (!shouldApprove) {
    return;
  }

  try {
    const respData = await TimesheetController.approveTimesheet({ id: timesheetId }, timesheet);
    
    const { success } = respData;
    
    if (success) {
      loadDataAndUpdateTable(table, timesheet);
    }
  } catch (error) {
    console.error(error);
  }
}

async function rejectItem(timesheetId) {
  const shouldReject = confirm("Are you sure you want to reject this timesheet?");
  
  if (!shouldReject) {
    return;
  }

  let message = prompt("Please enter your message:");
  
  while (message === '') {
    alert("Message cannot be blank.");
    message = prompt("Please enter your message:");
  }
  
  if (message) {
    try {
      const respData = await TimesheetController.rejectTimesheet({
        id: timesheetId,
        remark_revision: message
      }, timesheet);
      
      const { success } = respData;
      
      if (success) {
        loadDataAndUpdateTable(table, timesheet);
      }
    } catch (error) {
      console.error(error);
    }
  }
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="timesheet" title="Timesheet" class="mb-0">
      <template #options>
        <button type="button" @click="downloadReport" class="btn btn-sm btn-success">
          <i class="fa fa-download"></i> Download Report
        </button>
      </template>
      
      <table class="table table-hover table-bordered" style="font-size: 14px !important;">
        <thead>
          <tr>
            <th class="text-center table-active" style="width: 10px;">#</th>
            <th class="table-active" style="width: 200px;">Nama Karyawan</th>
            <th class="table-active">Remark</th>
            <th class="table-active" style="width: 5%">Status</th>
            <th class="table-active" style="width: 15%">Created At</th>
            <th class="table-active" style="width: 15%">Updated At</th>
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
            <td>{{ item.remarks }}</td>
            <td><span class="badge " :class="item.status.class">{{ item.status.label }}</span></td>
            <td>{{ formatTimestamp(item.created_at) }}</td>
            <td>{{ item.updated_at ? formatTimestamp(item.updated_at) : '-' }}</td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center align-items-center" v-if="item.status.label.includes(canApproveStatus)">
                <button type="button" class="btn btn-sm btn-info" @click="approveItem(item.id)">
                  Approve
                </button>
  
                <button type="button" class="btn btn-sm btn-danger" @click="rejectItem(item.id)">
                  Reject
                </button>
              </div>
              <div v-else>
                -
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <paginate v-show="table.lists.length > 0" :page-count="table.last_page" :click-handler="paginateClick">
      </paginate>
    </BaseBlock>
  </div>
</template>




