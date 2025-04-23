<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Barangay Forms Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background-color: #f4f4f4;
    }
    h1 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #fff;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #007bff;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    button {
      margin-right: 5px;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: white;
      font-size: 14px;
    }
    .view-btn {
      background-color: #17a2b8;
    }
    .approve-btn {
      background-color: #28a745;
    }
    .reject-btn {
      background-color: #dc3545;
    }
    .remove-btn {
      background-color: #6c757d;
    }
    #viewModal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }
    #viewModalContent {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 5px;
      width: 600px;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      position: relative;
    }
    #viewModalContent h2 {
      margin-top: 0;
    }
    #viewModalContent label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
    }
    #viewModalContent p {
      margin: 5px 0 10px 0;
      padding: 8px;
      background-color: #f1f1f1;
      border-radius: 4px;
      white-space: pre-wrap;
    }
    #viewModalContent button.close-btn {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 15px;
      position: absolute;
      right: 20px;
      top: 20px;
    }
    .status-approved {
      color: green;
      font-weight: bold;
    }
    .status-rejected {
      color: red;
      font-weight: bold;
    }
    .status-pending {
      color: orange;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>Barangay Forms Admin Panel</h1>
  <table id="formsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Resident Name</th>
        <th>Form Type</th>
        <th>Details</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <!-- Form entries will be populated here -->
    </tbody>
  </table>

  <!-- Modal for viewing form details -->
  <div id="viewModal">
    <div id="viewModalContent">
      <button class="close-btn" id="closeViewModal">Close</button>
      <h2>Form Details</h2>
      <div id="formDetailsContainer">
        <!-- Form details will be displayed here -->
      </div>
    </div>
  </div>

  <script>
    const apiUrl = 'api.php';

    const formsTableBody = document.querySelector("#formsTable tbody");
    const viewModal = document.getElementById("viewModal");
    const formDetailsContainer = document.getElementById("formDetailsContainer");
    const closeViewModalBtn = document.getElementById("closeViewModal");

    let forms = [];

    // Helper function to create a label and value display
    function createDisplayField(label, value) {
      return '<label>' + label + '</label><p>' + (value || '') + '</p>';
    }

    // Fetch all forms from the backend API
    async function fetchForms() {
      try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error('Failed to fetch forms');
        forms = await response.json();
        renderForms();
      } catch (error) {
        alert('Error fetching forms: ' + error.message);
      }
    }

    // Render the forms in the table
    function renderForms() {
      formsTableBody.innerHTML = "";
      forms.forEach(form => {
        const statusClass = form.status === "Approved" ? "status-approved" :
                            form.status === "Rejected" ? "status-rejected" : "status-pending";

        const residentName = form.first_name || '';

        const tr = document.createElement("tr");

        // Create table cells
        const idTd = document.createElement("td");
        idTd.textContent = form.id;

        const nameTd = document.createElement("td");
        nameTd.textContent = residentName;

        const typeTd = document.createElement("td");
        typeTd.textContent = form.form_type;

        const detailsTd = document.createElement("td");
        detailsTd.textContent = form.details || '';

        const statusTd = document.createElement("td");
        statusTd.textContent = form.status || 'Pending';
        statusTd.className = statusClass;

        const actionsTd = document.createElement("td");

        // Create buttons
        const viewBtn = document.createElement("button");
        viewBtn.className = "view-btn";
        viewBtn.textContent = "View";
        viewBtn.addEventListener("click", () => viewFormDetails(form.id, form.form_type.toLowerCase()));

        const approveBtn = document.createElement("button");
        approveBtn.className = "approve-btn";
        approveBtn.textContent = "Approve";
        approveBtn.addEventListener("click", () => updateStatus(form.id, form.form_type.toLowerCase(), "Approved"));

        const rejectBtn = document.createElement("button");
        rejectBtn.className = "reject-btn";
        rejectBtn.textContent = "Reject";
        rejectBtn.addEventListener("click", () => updateStatus(form.id, form.form_type.toLowerCase(), "Rejected"));

        const removeBtn = document.createElement("button");
        removeBtn.className = "remove-btn";
        removeBtn.textContent = "Remove";
        removeBtn.addEventListener("click", () => removeForm(form.id, form.form_type.toLowerCase()));


        // Append buttons to actions cell
        actionsTd.appendChild(viewBtn);
        actionsTd.appendChild(approveBtn);
        actionsTd.appendChild(rejectBtn);
        actionsTd.appendChild(removeBtn);

        // Append all cells to row
        tr.appendChild(idTd);
        tr.appendChild(nameTd);
        tr.appendChild(typeTd);
        tr.appendChild(detailsTd);
        tr.appendChild(statusTd);
        tr.appendChild(actionsTd);

        formsTableBody.appendChild(tr);
      });
    }

    // View form details in modal
    async function viewFormDetails(id, formType) {
      try {
        const response = await fetch(apiUrl + '/' + id + '?form_type=' + formType);
        if (!response.ok) throw new Error('Failed to fetch form data');
        const formData = await response.json();

        let htmlContent = '';
        for (const [key, value] of Object.entries(formData)) {
          if (key === 'id') continue; // skip id field
          const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
          htmlContent += createDisplayField(label, value);
        }
        formDetailsContainer.innerHTML = htmlContent;
        viewModal.style.display = "block";
      } catch (error) {
        alert('Error loading form data: ' + error.message);
      }
    }

    // Update form status (Approve/Reject)
    async function updateStatus(id, formType, newStatus) {
      try {
        const response = await fetch(apiUrl + '/' + id + '?form_type=' + formType, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ status: newStatus, form_type: formType })
        });
        if (!response.ok) throw new Error('Failed to update status');
        const result = await response.json();
        if (result.updated) {
          const form = forms.find(f => f.id === id && f.form_type.toLowerCase() === formType);
          if (form) {
            form.status = newStatus;
            renderForms();
          }
        } else {
          alert('Update failed');
        }
      } catch (error) {
        alert('Error updating status: ' + error.message);
      }
    }

    // Remove form
    async function removeForm(id, formType) {
      if (!confirm("Are you sure you want to remove this form?")) return;
      try {
        const response = await fetch(apiUrl + '/' + id + '?form_type=' + formType, {
          method: 'DELETE'
        });
        if (!response.ok) throw new Error('Failed to delete form');
        const result = await response.json();
        if (result.deleted) {
          forms = forms.filter(f => !(f.id === id && f.form_type.toLowerCase() === formType));
          renderForms();
        } else {
          alert('Delete failed');
        }
      } catch (error) {
        alert('Error deleting form: ' + error.message);
      }
    }

    // Close the view modal
    closeViewModalBtn.addEventListener("click", () => {
      viewModal.style.display = "none";
      formDetailsContainer.innerHTML = "";
    });

    // Initial fetch and render of forms
    fetchForms();
  </script>
</body>
</html>
