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
      background-color: #007bff; /* changed to blue */
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
  <div id="viewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true" style="display:none;">
    <div id="viewModalContent" class="modal-dialog modal-lg" role="document" style="background-color: white; padding: 20px; border-radius: 5px; margin: 10% auto; max-height: 80vh; overflow-y: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.3); position: relative;">
      <button class="close-btn btn btn-secondary" id="closeViewModal" style="position: absolute; right: 20px; top: 20px;">Close</button>
      <h2 id="viewModalLabel" class="text-center mb-4">Form Details</h2>
      <div id="formDetailsContainer" class="container-fluid px-3">
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
        console.log('Fetching form details for id:', id, 'formType:', formType);
        const response = await fetch(apiUrl + '/' + id + '?form_type=' + formType);
        if (!response.ok) throw new Error('Failed to fetch form data');
        const formData = await response.json();
        console.log('Form data received:', formData);

        let htmlContent = '';

        if (formType === 'clearance') {
          htmlContent = `
            <form id="viewClearanceForm" class="needs-validation" novalidate>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label>First Name</label>
                  <input type="text" class="form-control" value="${formData.first_name || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Middle Name</label>
                  <input type="text" class="form-control" value="${formData.middle_name || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Last Name</label>
                  <input type="text" class="form-control" value="${formData.last_name || ''}" readonly>
                </div>
                <div class="form-group col-md-12">
                  <label>Complete Address</label>
                  <input type="text" class="form-control" value="${formData.address || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Birth Date</label>
                  <input type="date" class="form-control" value="${formData.birth_date || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Age</label>
                  <input type="number" class="form-control" value="${formData.age || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Civil Status</label>
                  <input type="text" class="form-control" value="${formData.civil_status || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Mobile Number</label>
                  <input type="tel" class="form-control" value="${formData.mobile_number || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Years of Stay</label>
                  <input type="number" class="form-control" value="${formData.years_of_stay || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Purpose</label>
                  <input type="text" class="form-control" value="${formData.purpose || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Name of Student / Patient</label>
                  <input type="text" class="form-control" value="${formData.student_patient_name || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Address</label>
                  <input type="text" class="form-control" value="${formData.student_patient_address || ''}" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label>Relationship</label>
                  <input type="text" class="form-control" value="${formData.relationship || ''}" readonly>
                </div>
                <div class="form-group col-md-6">
                  <label>Email</label>
                  <input type="email" class="form-control" value="${formData.email || ''}" readonly>
                </div>
                <div class="form-group col-md-6">
                  <label>Shipping Method</label>
                  <input type="text" class="form-control" value="${formData.shipping_method || ''}" readonly>
                </div>
              </div>
            </form>
          `;
        } else {
          // Default display for other form types
          for (const [key, value] of Object.entries(formData)) {
            if (key === 'id') continue; // skip id field
            const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            htmlContent += createDisplayField(label, value);
          }
        }

        formDetailsContainer.innerHTML = htmlContent;
        viewModal.style.display = "block";
      } catch (error) {
        alert('Error loading form data: ' + error.message);
      }
    }

    // Update form status (Approve/Reject)
    async function updateStatus(id, formType, newStatus) {
      const form = forms.find(f => f.id === id && f.form_type.toLowerCase() === formType);
      if (!form) return;
      const updatedForm = { ...form, status: newStatus };
      try {
        const response = await fetch(apiUrl + '/' + id + '?form_type=' + formType, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updatedForm)
        });
        if (!response.ok) throw new Error('Failed to update status');
        const result = await response.json();
        if (result.updated) {
          form.status = newStatus;
          renderForms();
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
        const url = apiUrl + '/' + id + '?form_type=' + encodeURIComponent(formType.toLowerCase());
        console.log('DELETE URL:', url);
        const response = await fetch(url, {
          method: 'DELETE'
        });
        if (!response.ok) throw new Error('Failed to delete form');
        const result = await response.json();
        if (result.deleted) {
          forms = forms.filter(f => !(f.id === id && f.form_type.toLowerCase() === formType.toLowerCase()));
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

    // Close modal when clicking outside the modal content
    viewModal.addEventListener("click", (event) => {
      if (event.target === viewModal) {
        viewModal.style.display = "none";
        formDetailsContainer.innerHTML = "";
      }
    });

    // Initial fetch and render of forms
    fetchForms();
  </script>
</body>
</html>
