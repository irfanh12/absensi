export const baseUrl = import.meta.env.VITE_APP_CORE_API_URL;

export function getCookieByName(name) {
  const cookies = document.cookie
    .split(';')
    .map(cookie => cookie.trim())
    .reduce((acc, cookie) => {    
      const [cookieName, cookieValue] = cookie.split('=');
      acc[cookieName] = cookieValue;
      return acc;
    }, {});
    console.log(cookies)

  return cookies[name];
}

export default async function checkSession(token) {
  
  try {
    const response = await axios.get('api/v1/auth/profile', {
      headers: {
        'ngrok-skip-browser-warning': '1',
        Authorization: `Bearer ${token}`
      }
    });

    // Assuming the API returns a boolean indicating session validity
    return response.data.success;
  } catch (error) {
    // Log or handle the error in a more informative way
    console.error('Error validating session:', error);
    return false;
  }
}