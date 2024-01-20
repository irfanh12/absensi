import { getActivePinia } from "pinia"
export const baseUrl = import.meta.env.VITE_APP_CORE_API_URL;

let videoStream;
/**
 * Starts the camera and streams the video to a specified video element.
 *
 * @param {HTMLElement} videoElement - The HTML video element to stream the video to.
 * @return {void} This function does not return a value.
 */
export function startCamera(videoElement, formcontrol) {
  const constraints = {
    video: {
      facingMode: 'user', // use the front camera if available
      zoom: 2, // adjust the zoom level (try different values)
    },
  };

  formcontrol.value.statusLoading()

  navigator.mediaDevices.getUserMedia(constraints)
    .then((stream) => {
      videoStream = stream;
      videoElement.srcObject = stream;

      formcontrol.value.statusNormal()
    })
    .catch((error) => {
      console.error('Error accessing camera:', error);
    });
}

/**
 * Stops the camera stream and clears the video element.
 *
 * @param {none} none - No parameters.
 * @return {none} No return value.
 */
export function stopCamera(videoElement) {
  if (videoStream) {
    const tracks = videoStream.getTracks();

    tracks.forEach(track => track.stop());
    videoElement.srcObject = null;
    videoStream = null;
  }
}

/**
 * Capture a snapshot of a video element.
 *
 * @param {HTMLVideoElement} videoElement - The video element to capture the snapshot from.
 * @return {Object|Boolean} An object containing the URL and base64 image data of the captured snapshot, or `false` if the video stream is not available.
 */
export function captureSnapshot(videoElement) {
  if (videoStream) {
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    // Set the canvas dimensions to the video stream dimensions
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;

    // Draw the current video frame onto the canvas
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

    // Convert the canvas content to base64 data URL
    const base64image = canvas.toDataURL('image/png');

    // Convert base64 data URL to Blob
    const blob = dataURLtoBlob(base64image);
    return {
      url: URL.createObjectURL(blob),
      base64image: base64image
    }
  }

  return false;
}

/**
 * Converts a data URL to a Blob object.
 *
 * @param {string} dataUrl - The data URL to be converted.
 * @return {Blob} The Blob object representing the converted data URL.
 */
function dataURLtoBlob(dataUrl) {
  const parts = dataUrl.split(';base64,');
  const contentType = parts[0].split(':')[1];
  const raw = window.atob(parts[1]);
  const rawLength = raw.length;
  const uint8Array = new Uint8Array(rawLength);

  for (let i = 0; i < rawLength; ++i) {
    uint8Array[i] = raw.charCodeAt(i);
  }

  return new Blob([uint8Array], { type: contentType });
}

/**
 * Generates a date string representing the time difference between the input date and the current moment.
 *
 * @param {Date} date - The input date to compare to the current moment.
 * @return {string} - A string representation of the time difference between the input date and the current moment.
 */
export function justNowDate(date) {
  const now = moment();
  const diffInSeconds = now.diff(date, 'seconds');
  
  if (diffInSeconds < 5) {
    return 'just now';
  } else {
    return moment(date).fromNow();
  }
}

/**
 * Formats the given timestamp using the specified format.
 *
 * @param {number} timestamp - The timestamp to be formatted.
 * @param {string} [format='MMM D, YYYY'] - The format to use for formatting the timestamp. Defaults to 'MMM D, YYYY'.
 * @return {string} - The formatted timestamp.
 */
export function formatTimestamp(timestamp, format = 'MMM D, YYYY HH:mm') {  
  return moment.unix(timestamp).format(format);
}

/**
 * Retrieves the value of a cookie by its name.
 *
 * @param {string} name - The name of the cookie to retrieve.
 * @return {string|undefined} The value of the cookie, or undefined if the cookie does not exist.
 */
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

/**
 * Checks the session validity using the provided token.
 *
 * @param {string} token - The token used to authenticate the session.
 * @return {boolean} Returns a boolean indicating the session validity.
 */
export default async function checkSession(token) {
  
  try {
    const response = await axios.get('api/v1/auth/profile', {
      headers: {
        'ngrok-skip-browser-warning': '1',
        Authorization: `Bearer ${token}`
      }
    });

    // Assuming the API returns a boolean indicating session validity    
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`
    return response.data.success;
  } catch (error) {
    if(error.response) {
      localStorage.clear();
      // map through that list and use the **$reset** fn to reset the state
      getActivePinia()._s.forEach(store => store.$reset());
      alert(error.response.data.error.message);
    } else {
      alert(error);
    }

    // Log or handle the error in a more informative way
    console.error('Error validating session:', error);
    return false;
  }
}