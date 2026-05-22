<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tracker Studio</title>
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

  <div class="max-w-5xl mx-auto px-4 py-8">
    <header class="mb-8 text-center">
      <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Finance Tracker</h1>
      <p class="text-gray-500 mt-2">Loan & Expense Monitoring System</p>
    </header>

    <!-- Tab Navigation -->
    <div class="flex justify-center space-x-4 mb-8">
      <button id="loanTabBtn" onclick="switchTab('loan')" class="px-6 py-2 rounded-lg font-medium bg-blue-600 text-white shadow">Loan Monitoring</button>
      <button id="expenseTabBtn" onclick="switchTab('expense')" class="px-6 py-2 rounded-lg font-medium bg-white text-gray-600 border hover:bg-gray-50">Expense Monitoring</button>
    </div>

    <!-- LOAN SECTION -->
    <section id="loanSection" class="block space-y-6">
      <!-- Loan Form -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Loan Record</h2>
        <form id="loanForm" onsubmit="submitLoan(event)" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Type of Loan</label>
            <input type="text" id="loan_type" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
            <select id="status" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
              <option value="Active">Active</option>
              <option value="Fully Paid">Fully Paid</option>
              <option value="Pending">Pending</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
            <input type="number" id="amount" step="0.01" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Term (Months)</label>
            <input type="number" id="term" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Date Approved</label>
            <input type="date" id="date_approved" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Monthly Amortization</label>
            <input type="number" id="monthly_amortization" step="0.01" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div class="md:col-span-3 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">Save Loan</button>
          </div>
        </form>
      </div>
    </section>

    <!-- EXPENSE SECTION -->
    <section id="expenseSection" class="hidden space-y-6">
      <!-- Expense Form -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Expense</h2>
        <form id="expenseForm" onsubmit="submitExpense(event)" class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Year</label>
            <input type="number" id="year" value="2026" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Month</label>
            <select id="month" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
              <option value="January">January</option>
              <option value="February">February</option>
              <option value="March">March</option>
              <option value="April">April</option>
              <option value="May" selected>May</option>
              <option value="June">June</option>
              <option value="July">July</option>
              <option value="August">August</option>
              <option value="September">September</option>
              <option value="October">October</option>
              <option value="November">November</option>
              <option value="December">December</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Type of Expense</label>
            <input type="text" id="expense_type" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Amount</label>
            <input type="number" id="expense_amount" step="0.01" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
          </div>
          <div class="md:col-span-4 text-right">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-medium">Save Expense</button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <script>
    // REPLACE THIS WITH YOUR STEIN OR SHEETY API ENDPOINT URL
   const SCRIPT_URL = "https://script.google.com/macros/s/AKfycbxaxrC2tv7nlbiX_pQbkx0kmvqrm17kvhVV5AIClKHWYinpHLU5pwq9Ou1sGQx6IyIt/exec";

// Submit Loan Logic
async function submitLoan(e) {
  e.preventDefault();
  
  // Create payload mapping to your exact column order in the Google Sheet
  const payload = {
    sheetName: "loans",
    values: [
      document.getElementById('loan_type').value,
      document.getElementById('status').value,
      document.getElementById('amount').value,
      document.getElementById('term').value,
      document.getElementById('date_approved').value,
      document.getElementById('monthly_amortization').value
    ]
  };

  try {
    // Notice: No headers object! This is the CORS bypass trick.
    const response = await fetch(SCRIPT_URL, {
      method: 'POST',
      body: JSON.stringify(payload) 
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      alert('Loan recorded successfully!');
      document.getElementById('loanForm').reset();
    } else {
      console.error('Backend error:', result.message);
      alert('Failed to save: ' + result.message);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

// Submit Expense Logic
async function submitExpense(e) {
  e.preventDefault();
  
  const payload = {
    sheetName: "expenses",
    values: [
      document.getElementById('year').value,
      document.getElementById('month').value,
      document.getElementById('expense_type').value,
      document.getElementById('expense_amount').value
    ]
  };

  try {
    const response = await fetch(SCRIPT_URL, {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    
    const result = await response.json();
    
    if (result.status === "success") {
      alert('Expense recorded successfully!');
      document.getElementById('expenseForm').reset();
    } else {
      console.error('Backend error:', result.message);
      alert('Failed to save: ' + result.message);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}
  </script>
</body>
</html>
