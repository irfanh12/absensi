export const baseUrl = import.meta.env.VITE_APP_CORE_API_URL;

let videoStream;
let snapshotElement = document.getElementById('snapshot');

export function startCamera(videoElement) {
  const constraints = {
    video: {
      facingMode: 'user', // use the front camera if available
      zoom: 2, // adjust the zoom level (try different values)
    },
  };

  navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
      console.log(stream, videoElement)
      videoStream = stream;
      videoElement.srcObject = stream;
    })
    .catch((error) => {
      console.error('Error accessing camera:', error);
    });
}

export function captureSnapshot() {
  if (videoStream) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    // Set the canvas dimensions to the video stream dimensions
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;

    // Draw the current video frame onto the canvas
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

    // Convert the canvas content to base64 data URL
    const base64DataUrl = canvas.toDataURL('image/png');

    // Display the snapshot
    snapshotElement.src = base64DataUrl;
  }
}

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
    if(error.response) {
      localStorage.clear();
      alert(error.response.data.error.message);
    } else {
      alert(error);
    }

    // Log or handle the error in a more informative way
    console.error('Error validating session:', error);
    return false;
  }
}