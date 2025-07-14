const axios = require('axios');

// Replace with your actual Semaphore API key
const API_KEY = 'YOUR_SEMAPHORE_API_KEY';

// Send SMS to one or more recipients
async function sendSMS(recipients, message) {
  try {
    const response = await axios.post('https://api.semaphore.co/api/v4/messages', {
      apikey: API_KEY,
      number: recipients, // Can be a string or an array of numbers
      message: message,
      sendername: 'PureFlow' // Optional: registered sender name
    });
    console.log('SMS sent:', response.data);
    return response.data;
  } catch (error) {
    console.error('Error sending SMS:', error.response ? error.response.data : error.message);
    throw error;
  }
}

// Example usage:
(async () => {
  // Example distributor numbers
  const distributors = ['09171234567', '09181234567', '09221234567'];
  await sendSMS(distributors, 'Hello distributor! This is a test message from PureFlow.');
})();

module.exports = { sendSMS };