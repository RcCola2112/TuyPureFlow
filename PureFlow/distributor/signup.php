<?php
// signup.php
include '../includes/header.php'; // Adjusted path
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Distributor Signup</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const steps = document.querySelectorAll('.step');
      const nextBtns = document.querySelectorAll('.next-btn');
      const prevBtns = document.querySelectorAll('.prev-btn');
      let currentStep = 0;

      function showStep(index) {
        steps.forEach((step, i) => {
          step.classList.toggle('hidden', i !== index);
        });
        updateProgress(index);
      }

      function updateProgress(index) {
        document.querySelectorAll('.progress-step').forEach((el, i) => {
          el.classList.toggle('bg-blue-600', i <= index);
        });
      }

      nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          if (currentStep < steps.length - 1) currentStep++;
          showStep(currentStep);
        });
      });

      prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
          if (currentStep > 0) currentStep--;
          showStep(currentStep);
        });
      });

      showStep(currentStep);
    });
  </script>
</head>
<body class="bg-gray-100 py-10">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-center">Distributor Signup</h2>

    <!-- Progress Bar -->
    <div class="flex justify-between mb-6">
      <div class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
      <div class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
      <div class="progress-step w-1/3 h-2 bg-blue-300 rounded"></div>
    </div>

    <form action="submit_signup.php" method="POST" enctype="multipart/form-data">

      <!-- Step 1 -->
      <div class="step">
        <h3 class="text-lg font-semibold mb-2">Business Information</h3>
        <input type="text" name="business_name" placeholder="Business Name *" required class="w-full border p-2 mb-2">
        <input type="text" name="owner_name" placeholder="Owner's Name *" required class="w-full border p-2 mb-2">
        <input type="text" name="contact" placeholder="Contact Number *" required class="w-full border p-2 mb-2">
        <input type="email" name="email" placeholder="Email Address *" required class="w-full border p-2 mb-2">
        <input type="text" name="hours" placeholder="Operating Hours *" required class="w-full border p-2 mb-2">

        <h3 class="text-lg font-semibold mt-4 mb-2">Business Address</h3>
        <input type="text" name="street" placeholder="Street and House Number *" required class="w-full border p-2 mb-2">
        <select name="city" required class="w-full border p-2 mb-2">
          <option value="">Select City *</option>
          <option value="Tuy">Tuy</option>
        </select>
        <select name="region" required class="w-full border p-2 mb-2">
          <option value="">Select Region *</option>
          <option value="Region IV-A">Region IV-A</option>
        </select>
        <input type="text" name="map_location" placeholder="Choose Location Using Maps *" required class="w-full border p-2 mb-2">

        <button type="button" class="next-btn bg-blue-600 text-white px-4 py-2 rounded">Continue</button>
      </div>

      <!-- Step 2 -->
      <div class="step hidden">
        <h3 class="text-lg font-semibold mb-2">Account Credentials</h3>
        <input type="text" name="username" placeholder="Username" required class="w-full border p-2 mb-2">
        <input type="password" name="password" placeholder="Password *" required class="w-full border p-2 mb-2">
        <input type="password" name="confirm_password" placeholder="Confirm Password *" required class="w-full border p-2 mb-2">

        <div class="flex justify-between mt-4">
          <button type="button" class="prev-btn bg-gray-600 text-white px-4 py-2 rounded">Back</button>
          <button type="button" class="next-btn bg-blue-600 text-white px-4 py-2 rounded">Continue</button>
        </div>
      </div>

      <!-- Step 3 -->
      <div class="step hidden">
        <h3 class="text-lg font-semibold mb-2">Verification Documents</h3>
        <label class="block mb-2">Business Permit / License *</label>
        <input type="file" name="permit" required class="mb-4">

        <label class="block mb-2">Valid Government ID *</label>
        <input type="file" name="id" required class="mb-4">

        <label class="block mb-2">Proof of Address *</label>
        <input type="file" name="proof" required class="mb-4">

        <div class="mt-4">
          <label><input type="checkbox" name="terms" required> I agree to the Terms and Conditions *</label><br>
          <label><input type="checkbox" name="privacy" required> I agree to the Privacy Policy *</label>
        </div>

        <div class="flex justify-between mt-4">
          <button type="button" class="prev-btn bg-gray-600 text-white px-4 py-2 rounded">Back</button>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
        </div>
      </div>

    </form>
  </div>
</body>
</html>
