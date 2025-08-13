<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <th>s.no</th>
          <th>
            <input v-model="newEmployee.first_name" class="custom_nameInput" type="text" placeholder="First Name" />
          </th>
          <th>
            <input v-model="newEmployee.last_name" class="custom_nameInput" type="text" placeholder="Last Name" />
          </th>
          <th>
            <select v-model="newEmployee.email" class="custom_dropdown">
              <option value="">Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.department_id" class="custom_dropdown">
              <option value="">Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.designation_id" class="custom_dropdown">
              <option value="">Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </th>
          <th>
            <button class="btn btn-primary w-100 add-button" @click="addEmployee" :disabled="isAddDisabled"><i class="fas fa-plus"></i>Add</button>
          </th>
        </tr>
      </thead>

      <tbody>
        <!-- Loop through the employees array and display employee data dynamically -->
        <tr v-for="(employee, index) in employees" :key="employee.id">
          <td>{{ index + 1 }}.</td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.first_name }}</span>
            <input v-else v-model="employee.first_name" class="custom_input" type="text" placeholder="First Name" />
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.last_name }}</span>
            <input v-else v-model="employee.last_name" class="custom_input" type="text" placeholder="Last Name" />
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.email }}</span>
            <select v-else v-model="employee.email" class="custom_dropdown">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </td>
         <td>
  <span v-if="!employee.isEditing">{{ employee.department?.name || 'No Department' }}</span>
  <select v-else v-model="employee.department_id" class="custom_dropdown">
    <option value="">Select Department</option>
    <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
  </select>
</td>

<!-- Designation -->
<td>
  <span v-if="!employee.isEditing">{{ employee.designation?.name || 'No Designation' }}</span>
  <select v-else v-model="employee.designation_id" class="custom_dropdown">
    <option value="">Select Designation</option>
    <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
  </select>
</td>
          <td>
            <div class="button-group">
              <!-- Edit Button -->
              <button v-if="!employee.isEditing" class="btn edit-button" @click="editEmployee(employee)">
                <i class="fas fa-edit"></i>
              </button>
              <!-- Save Button -->
              <button v-if="employee.isEditing" class="btn btn-success save-button" @click="saveEmployee(employee, index)">
                <i class="fas fa-save"></i>
              </button>
              <!-- Delete Button -->
              <button @click="deleteEmployee(index, employee.id)" class="delete-button">
  <i class="fas fa-trash"></i>
</button>

            </div>
          </td>
        </tr>
      </tbody>

    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const employees = ref([]);  // Dynamically populated employees list
const departments = ref([]);  // List of departments
const designations = ref([]);  // List of designations
const employeeEmails = ref([]);  // List of employee emails for dropdown
const tasks = ref([]);  // Store tasks
const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  first_name: '',
  last_name: '',
  task_id: ''  // Add task_id for assigning task to new employee
});

const isAddDisabled = computed(() => {
  // Disable the Add button if any field is missing
  return !newEmployee.value.email || 
         !newEmployee.value.department_id || 
         !newEmployee.value.designation_id || 
         !newEmployee.value.first_name || 
         !newEmployee.value.last_name;
});

// On mounted, fetch departments, designations, and tasks
onMounted(async () => {
  try {
    const response = await fetch('/api/department-designation-data', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`  // Include token for authentication
      }
    });

    const data = await response.json();
    departments.value = data.departments;
    designations.value = data.designations;
    employeeEmails.value = data.employees.map(employee => employee.email);

    // Fetch tasks data
    const taskResponse = await axios.get('/api/tasks', {  // Make sure your API has a route for fetching tasks
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}` 
      }
    });
    tasks.value = taskResponse.data;

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Add employee to the list dynamically
const addEmployee = async () => {
  // Check if the form fields are valid
  if (isAddDisabled.value) {
    return;
  }

  // Prepare the employee data to be sent to the API
  const employeeData = {
    email: newEmployee.value.email,
    department_id: newEmployee.value.department_id,
    designation_id: newEmployee.value.designation_id,
    first_name: newEmployee.value.first_name,
    last_name: newEmployee.value.last_name,
    task_id: newEmployee.value.task_id,  // Include task_id here
  };

  try {
    // Send the data to the API to add the employee
    const response = await axios.post('/api/employee/add-employee', employeeData, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}` // Include token for authentication
      }
    });

    // Log the response to check if the employee was added successfully
    console.log(response.data.employee);  // This logs the new employee object from the API response

    // Dynamically add the new employee to the employees array
    employees.value.push(response.data.employee);  // Add the employee to the employees array

    // Reset the form fields for the next entry
    resetForm();

  } catch (error) {
    console.error('Error adding employee:', error);
    alert('Failed to add employee.');
  }
};

// Edit employee details
const editEmployee = (employee) => {
  employee.isEditing = true;
};

// Save the edited employee details
const saveEmployee = (employee, index) => {
  // Mark the employee as not editing
  employee.isEditing = false;

  // Find the updated department and designation names
  const updatedDepartment = departments.value.find(department => department.id === employee.department_id);
  const updatedDesignation = designations.value.find(designation => designation.id === employee.designation_id);

  // Update the employee object with the new department and designation names
  const updatedEmployee = {
    ...employee,  // Spread the existing employee data
    department: updatedDepartment, // Assign the full department object
    designation: updatedDesignation, // Assign the full designation object
  };

  // Replace the old employee data in the array with the updated one
  employees.value.splice(index, 1, updatedEmployee);

  // Optionally reset the form (if you want)
  resetForm();
};

const deleteEmployee = async (index, employeeId) => {
  try {
    // Send DELETE request to Laravel API
    const response = await axios.delete(`/api/employee/delete-employee/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}` // Include token for authentication
      }
    });

    // On successful deletion, remove the employee from the employees array
    if (response.status === 200) {
      employees.value.splice(index, 1);
      // alert('Employee deleted successfully!');
    }
  } catch (error) {
    console.error('Error deleting employee:', error);
    alert('Failed to delete employee.');
  }
};



// Reset the form fields
const resetForm = () => {
  newEmployee.value = {
    email: '',
    department_id: '',
    designation_id: '',
    first_name: '',
    last_name: '',
    task_id: '',  // Reset task_id when form is cleared
  };
};
</script>

<style scoped>
.title {
  border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #2c2a2a;
  font-size: 1.5em;
  font-weight: 600;
  font-family: serif;
}

.container {
  width: 100%;
  max-width: 1040px;
  background-color: #f8f9faa1;
  padding: 40px;
  border-radius: 4px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(118, 165, 209);
  margin-left: 137px;
  margin-top: 100px;
}

table {
  width: 100%;
  border-collapse: collapse;
  table-layout: fixed;
}

th {
  padding: 6px;
  border: 2px solid #ddd;
  background: linear-gradient(to bottom, #275b8f, #b4cfe7);
  color: white;
  width: 14%;
}

th:first-child,
td:first-child {
  width: 4.4%;
}

th:nth-child(2),
td:nth-child(2) {
  width: 11%;
  padding-left: 15px;
}

th:nth-child(3),
td:nth-child(3),
th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5) {
  width: 11%;
}

th:nth-child(6),
td:nth-child(6) {
  width: 13%;
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7),
td:nth-child(7) {
  width: 8.5%;
}

th:nth-child(8),
td:nth-child(8) {
  width: 9%;
}

th:first-child,
td:first-child,
th:nth-child(2),
td:nth-child(2),
th:nth-child(3),
td:nth-child(3),
th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5),
th:nth-child(6),
td:nth-child(6),
th:nth-child(8),
td:nth-child(8) {
  padding: 10px;
  border: 2px solid #cacaca;
  overflow: hidden;
  word-wrap: break-word;
  white-space: normal;
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
  color: rgb(43, 41, 41);
  font-size: 14px;
}

select,
input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #ffffff;
  font-size: 16px;
  font-family: 'Arial', sans-serif;
  transition: border-color 0.3s ease;
}

select:focus,
input:focus {
  border-color: #2c87f0;
  outline: none;
}
.custom_input 
.add-button {
  width: 20%;
  padding: 5px;
  border-color: #4396f5;
  color: rgb(48, 45, 45);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}
.custom_nameInput{

height:35px;
}
.add-button:hover {
  background-color: #1675e2;
  color: rgb(247, 246, 246);
}

.delete-button {
  padding: 8px 12px;
  background-color: white;
  color: white;
  border-color: #d34a14;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.edit-button {
  padding: 8px 12px;
  background-color: white;
  color: white;
  border-color: #347537;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.save-button {
  padding: 8px 12px;
  background-color: white;
  color: white;
  border-color: #717271;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.delete-button:hover {
  background-color: rgb(245, 38, 38);
  color: #f9f9f9
}

.edit-button:hover {
  background-color: #319131;
  color: #f9f9f9
}

.save-button:hover {
  background-color: #4e644e;
  color: #f9f9f9
}

.edit-button:hover i {
  color: #f9f9f9;
}

.delete-button:hover i {
  color: #f9f9f9;
}

.save-button:hover i {
  color: #f9f9f9;
}

.delete-button i,
.edit-button i,
.save-button i {
  font-size: 15px;
  color: #333;
}

button:disabled {
  cursor: not-allowed;
  background-color: #ddd;
}

.button-group {
  display: flex;
  gap: 10px;
  justify-content: flex-start;
}

.button-group button {
  flex: 1 1 auto;
}

button:hover {
  background-color: #f4f4f4;
  color: black;
}
</style>