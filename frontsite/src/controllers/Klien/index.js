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
    // Log or handle the error in a more informative way
    console.error('Error validating :', error);
    return false;
  }
}

export async function loadEmployee(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get(`api/v1/employee/edit/${reactive.uuid}`);

    ref.value.statusNormal()
    return response.data;
  } catch (error) {
    // Log or handle the error in a more informative way
    console.error('Error validating :', error);
    return false;
  }
}

export async function onSubmit(reactive) {

  try {
    const response = await axios.post(`api/v1/auth/register`, {
      email: reactive.email,
      password: reactive.password,
      user_type_id: reactive.user_type_id,
    });
    
    return response.data;
  } catch (error) {
    // Log or handle the error in a more informative way
    console.error('Error validating :', error);
    return false;
  }
}

export async function storeOwner(reactive) {
  try {
    const response = await axios.post(`api/v1/employee/store`, {
      uuid: reactive.uuid,
      nama: reactive.nama,
      address: reactive.address,
      code: reactive.code,
    });
    
    return response.data;
  } catch (error) {
    // Log or handle the error in a more informative way
    console.error('Error validating :', error);
    return false;
  }
}
