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
 * Async function to list timesheet.
 *
 * @param {boolean} reactive - whether the request is reactive
 * @param {string} dateDay - the date for timesheet list
 * @return {Promise} the timesheet list data
 */
export async function listTimesheet(reactive, dateDay) {
  try {
    const response = await axios.get(`api/v1/timesheet/lists/${dateDay}`, {
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
 * Asynchronously reports timesheet using the given reactive data and dateDay.
 *
 * @param {any} reactive - The reactive data to be used in the report.
 * @param {any} dateDay - The date for which the timesheet report is requested.
 * @return {any} The data returned from the timesheet report.
 */
export async function reportTimesheet(reactive, dateDay) {
  try {
    const response = await axios.post(`api/v1/timesheet/report/${dateDay}`, { ...reactive });

    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}

export async function approveTimesheet(reactive, dateDay) {
  try {
    const response = await axios.post(`api/v1/timesheet/approve/${dateDay}`, reactive);

    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}

export async function rejectTimesheet(reactive, dateDay) {
  try {
    const response = await axios.post(`api/v1/timesheet/reject/${dateDay}`, reactive);

    return response.data;
  } catch (error) {
    const errorResp = error.response.data
    alert(errorResp.error.message);
    // Log or handle the error in a more informative way
    console.error('Error validating:', errorResp.error.message);
    return false;
  }
}