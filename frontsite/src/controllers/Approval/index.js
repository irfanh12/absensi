export async function loadData(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get('api/v1/owner/lists', {
      params: {
        page: reactive.items.page,
        per_page: reactive.items.per_page,
        keyword: reactive.items.keyword,
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

export async function loadOwner(reactive, ref) {
  ref.value.statusLoading()

  try {
    const response = await axios.get(`api/v1/owner/edit/${reactive.uuid}`);

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
    const response = await axios.post(`api/v1/owner/store`, {
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
