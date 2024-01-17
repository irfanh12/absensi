<script setup>
import { onMounted, reactive, ref } from "vue";
import * as KlienController from "@/controllers/Klien";

const klien = ref(null)

let table = reactive({
  lists: [],
  total: 0,
  page: 1,
  per_page: 15,
  last_page: 0,
  type_id: 2
})

onMounted(() => {
  loadDataAndUpdateTable(table, klien);
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
 * Loads data from the klien and updates the table.
 *
 * @param {string} table - The table to update.
 * @param {object} klien - The klien object.
 * @return {Promise} A promise that resolves when the data is loaded and the table is updated.
 */
function loadDataAndUpdateTable(table, klien) {
  return KlienController.loadData(table, klien)
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
  loadDataAndUpdateTable(table, klien);
}
</script>

<template>
  <div class="content">
    <BaseBlock ref="klien" title="Klien" class="mb-0">
      <template #options>
        <RouterLink :to="{ name: 'klien-form' }" class="btn btn-sm btn-primary">
          <i class="fa fa-user-plus"></i> Create Klien
        </RouterLink>
      </template>
      
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th class="table-active" style="width: 200px;">Klien Name</th>
            <th class="table-active">Email</th>
            <th class="table-active">Address</th>
            <th class="table-active">Code</th>
            <th class="text-center table-active" style="width: 20%">
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
          <tr v-for="item in table.lists" :key="item.id">
            <td>{{ item.nama }}</td>
            <td>{{ item.user.email }}</td>
            <td>{{ item.address }}</td>
            <td>{{ item.code }}</td>
            <td class="text-center">
              <div class="d-flex gap-2 justify-content-center align-items-center">
                <router-link :to="{ name: 'klien-form', params: { id: item.id } }">
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




