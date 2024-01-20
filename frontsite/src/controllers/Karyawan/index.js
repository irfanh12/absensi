export async function loadData(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get('api/v1/employee/lists', {
      params: {
        page: reactive.page,
        per_page: reactive.per_page,
        keyword: reactive.keyword,
        type_id: reactive.type_id,
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

export async function loadKaryawan(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get(`api/v1/employee/edit/${reactive.id}`);

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

export async function storeKaryawan(reactive, ref) {
  try {
    const payload = { ...reactive };
		const id = payload.id;

		const url = id ? `api/v1/employee/${id}/update` : 'api/v1/employee/store';
		const method = id? 'put' : 'post';
		const request = { method, url, data: payload };
		const response = await axios(request);
    
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

export async function deleteItem(id) {
	try {
		const response = await axios.delete(`api/v1/employee/${id}/destroy`);
		
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


export async function getListClients() {
  try {
    const response = await axios.get(`api/v1/employee/get-list-clients`);

    return response.data;
  } catch (error) {
    // Log or handle the error in a more informative way
    console.error('Error validating :', error);
    return false;
  }
}