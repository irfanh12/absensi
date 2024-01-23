export async function loadData(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get('api/v1/presensi/lists', {
      params: {
        page: reactive.page,
        per_page: reactive.per_page,
        date: reactive.date,
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