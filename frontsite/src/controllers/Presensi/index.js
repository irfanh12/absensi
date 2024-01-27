/**
 * Retrieves search data based on the provided keyword.
 *
 * @param {string} keyword - The keyword to search for
 * @return {Promise} The data retrieved from the search
 */
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

/**
 * Async function to list presensi.
 *
 * @param {boolean} reactive - whether the request is reactive
 * @param {string} dateDay - the date for presensi list
 * @return {Promise} the presensi list data
 */
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

/**
 * Asynchronously reports presensi using the given reactive data and dateDay.
 *
 * @param {any} reactive - The reactive data to be used in the report.
 * @param {any} dateDay - The date for which the presensi report is requested.
 * @return {any} The data returned from the presensi report.
 */
export async function reportPresensi(reactive, dateDay) {
  try {
    const response = await axios.post(`api/v1/presensi/report/${dateDay}`, { ...reactive });

    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}
