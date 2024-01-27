import _ from "lodash";

export async function searchData (keyword) {
  try {
    const response = await axios.get('api/v1/employee/search', {
      params: {
        keyword: keyword
      }
    });
    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}

export async function listPresensi(reactive, dateDay) {
  try {
    const response = await axios.get(`api/v1/presensi/lists/${dateDay}`, {
      params: { ...reactive }
    });

    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}

export async function exportData(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get('api/v1/timesheet/lists', {
      params: {
        page: reactive.page,
        per_page: reactive.per_page,
        keyword: reactive.keyword,
      }
    });

    ref.value.statusNormal()
    return response.data;
  } catch (error) {
    ref.value.statusNormal()
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}