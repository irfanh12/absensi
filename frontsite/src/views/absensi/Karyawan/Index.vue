<script setup>
import { onMounted, reactive, ref } from "vue";
import * as KaryawanController from "@/controllers/Karyawan";

import { useAuth } from "@/stores/auth"
const auth = useAuth()
const permissions = auth.permissions()

const karyawan = ref(null)

let table = reactive({
  lists: [],
  total: 0,
  page: 1,
  per_page: 15,
  last_page: 0,
  type_id: ['Human Resource', 'Administrator'].includes(auth.position) ? [4,5] : 5
})

onMounted(() => {
  loadDataAndUpdateTable(table, karyawan);
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
 * Loads data from the karyawan and updates the table.
 *
 * @param {string} table - The table to update.
 * @param {object} karyawan - The karyawan object.
 * @return {Promise} A promise that resolves when the data is loaded and the table is updated.
 */
function loadDataAndUpdateTable(table, karyawan) {
  return KaryawanController.loadData(table, karyawan)
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
  loadDataAndUpdateTable(table, karyawan);
}

/**
 * Deletes an item with the given ID.
 *
 * @param {string} id - The ID of the item to delete.
 * @return {Promise} A promise that resolves with the result of the deletion.
 */
function deleteItem(id) {
  const confirmMessage = 'Are you sure you want to delete this?';
  const confirmed = confirm(confirmMessage);
  if (!confirmed) {
    return;
  }
   
  return KaryawanController.deleteItem(id)
    .then(({ success, data }) => {
      if (success) {
        loadDataAndUpdateTable(table, karyawan);
      }
    }).catch(error => {
      console.error(error);
    });
}

async function reportKaryawan() {
  const { success, data } = await KaryawanController.reportKaryawan(karyawan)
  if (success) {
    window.open(data.url_download, '_blank');
  }
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="karyawan" title="Karyawan" >
      <template #options>
        <button class="btn btn-sm btn-success" @click="reportKaryawan">
          Download Report Karyawan
        </button>

        <RouterLink :to="{ name: 'karyawan-form' }" class="btn btn-sm btn-primary">
          <i class="fa fa-user-plus"></i> Create Karyawan
        </RouterLink>
      </template>
      
      <table class="table table-hover table-bordered" style="font-size: 14px !important;">
        <thead>
          <tr>
            <th class="table-active" style="width: 25%;">Fullname</th>
            <th class="table-active">Email</th>
            <th class="table-active">Position</th>
            <th class="table-active">Phone Number</th>
            <th class="text-center table-active" style="width: 10%">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-show="table.lists.length === 0">
            <td class="text-center" colspan="7">
              No records found.
            </td>
          </tr>
          <tr v-for="item in table.lists" :key="item.id">
            <td>{{ `${item.karyawan.first_name} ${item.karyawan.last_name}` }}</td>
            <td>{{ item.email }}</td>
            <td>{{ item.karyawan.position }}</td>
            <td>{{ item.karyawan.phone_number }}</td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center align-items-center">
                <router-link :to="{ name: 'karyawan-form', params: { id: item.id } }">
                  <button type="button" class="btn btn-sm btn-primary">
                    Edit
                  </button>
                </router-link>
  
                <button type="button" class="btn btn-sm btn-danger"
                  @click="deleteItem(item.id)">
                  Delete
                </button>
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




