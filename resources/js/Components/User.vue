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
            <input v-model="newEmployee.email" class="custom_nameInput" type="email" placeholder="Email" />
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
            <button class="btn btn-primary w-100 add-button" @click="addEmployee" :disabled="isAddDisabled">
              <i class="fas fa-plus"></i>Add
            </button>
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(employee, index) in paginatedEmployees.data" :key="employee.id">
          <td>{{ index + 1 + ((paginatedEmployees.current_page - 1) * paginatedEmployees.per_page) }}.</td>
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
            <input v-else v-model="employee.email" class="custom_input" type="email" placeholder="Email" />
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.department?.name || 'No Department' }}</span>
            <select v-else v-model="employee.department_id" class="custom_dropdown">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </td>
          <td>
            <span v-if="!employee.isEditing">{{ employee.designation?.name || 'No Designation' }}</span>
            <select v-else v-model="employee.designation_id" class="custom_dropdown">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </td>
          <td>
            <div class="button-group">
              <!-- View Detail Button -->
              <button class="btn btn-info view-button" @click="viewEmployee(employee)">
                <i class="fas fa-eye"></i>
              </button>
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

    <!-- Pagination Controls -->
    <div class="pagination">
      <button @click="changePage(paginatedEmployees.current_page - 1)" :disabled="paginatedEmployees.current_page === 1">
        <i class="fas fa-chevron-left"></i>
      </button>

      <button v-for="page in paginatedEmployees.last_page" :key="page" @click="changePage(page)" :class="{ active: paginatedEmployees.current_page === page }">
        {{ page }}
      </button>

      <button @click="changePage(paginatedEmployees.current_page + 1)" :disabled="paginatedEmployees.current_page === paginatedEmployees.last_page">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <!-- Employee Detail Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal-content">
        <h2>Employee Details</h2>
        <p><strong>First Name:</strong> {{ selectedEmployee.first_name }}</p>
        <p><strong>Last Name:</strong> {{ selectedEmployee.last_name }}</p>
        <p><strong>Email:</strong> {{ selectedEmployee.email }}</p>
        <p><strong>Department:</strong> {{ selectedEmployee.department?.name || 'N/A' }}</p>
        <p><strong>Designation:</strong> {{ selectedEmployee.designation?.name || 'N/A' }}</p>
        <button class="btn btn-secondary" @click="closeModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const employees = ref([]);
const departments = ref([]);
const designations = ref([]);
const employeeEmails = ref([]);
const tasks = ref([]);
const paginatedEmployees = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  per_page: 3,
});

const showModal = ref(false);
const selectedEmployee = ref({});

const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  first_name: '',
  last_name: '',
  task_id: ''
});

const isAddDisabled = computed(() => {
  return !newEmployee.value.email ||
    !newEmployee.value.department_id ||
    !newEmployee.value.designation_id ||
    !newEmployee.value.first_name ||
    !newEmployee.value.last_name;
});

onMounted(async () => {
  try {
    const response = await fetch('/api/department-designation-data', {
      method: 'GET',
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    const data = await response.json();
    departments.value = data.departments;
    designations.value = data.designations;
    employeeEmails.value = data.employees.map(employee => employee.email);

    const taskResponse = await axios.get('/api/tasks', {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });
    tasks.value = taskResponse.data;

    fetchEmployees(1);
  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const fetchEmployees = async (page = 1) => {
  try {
    const response = await axios.get(`/api/employees?page=${page}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    paginatedEmployees.value = response.data;
    paginatedEmployees.value.data.forEach(emp => emp.isEditing = false); // initialize editing state
  } catch (error) {
    console.error('Error fetching employees:', error);
  }
};

const addEmployee = async () => {
  if (isAddDisabled.value) return;

  const employeeData = { ...newEmployee.value };

  try {
    await axios.post('/api/employee/add-employee', employeeData, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    fetchEmployees(paginatedEmployees.value.current_page);
    resetForm();
  } catch (error) {
    console.error('Error adding employee:', error);
    alert('Failed to add employee.');
  }
};

const editEmployee = (employee) => {
  employee.isEditing = true;
};

const saveEmployee = async (employee, index) => {
  try {
    const updatedEmployee = {
      first_name: employee.first_name,
      last_name: employee.last_name,
      email: employee.email,
      department_id: employee.department_id,
      designation_id: employee.designation_id,
      task_id: employee.task_id,
    };

    await axios.put(`/api/employee/update-employee/${employee.id}`, updatedEmployee, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    employee.isEditing = false; 
    fetchEmployees(paginatedEmployees.value.current_page);
  } catch (error) {
    console.error('Error updating employee:', error);
    alert('Failed to save employee.');
  }
};

const deleteEmployee = async (index, employeeId) => {
  try {
    const response = await axios.delete(`/api/employee/delete-employee/${employeeId}`, {
      headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
    });

    if (response.status === 200) fetchEmployees(paginatedEmployees.value.current_page);
  } catch (error) {
    console.error('Error deleting employee:', error);
    alert('Failed to delete employee.');
  }
};

const changePage = (page) => {
  if (page > 0 && page <= paginatedEmployees.value.last_page) {
    fetchEmployees(page);
  }
};

const resetForm = () => {
  newEmployee.value = {
    email: '',
    department_id: '',
    designation_id: '',
    first_name: '',
    last_name: '',
    task_id: '',
  };
};

const viewEmployee = (employee) => {
  selectedEmployee.value = employee;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
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
  max-width: 950px;
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
  width: 4.3%;
}

/* Buttons */
.view-button {
  padding: 6px 10px;
  background: #fbfcfd;
  border: none;
  border-radius: 5px;
  color: rgb(26, 25, 25);
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.2s ease-in-out;
}
.view-button:hover {
  background: #0d8ba1;
  color:white;
}

.delete-button,
.edit-button,
.save-button {
  padding: 6px 10px;
  border-radius: 4px;
  cursor: pointer;
  border: none;
  transition: 0.2s;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
  width: 400px;
}

.task-table {
  width: 100%;
  margin-top: 10px;
  border-collapse: collapse;
}
.task-table th,
.task-table td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: left;
}

.close-btn {
  margin-top: 15px;
  padding: 8px 15px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.close-btn:hover {
  background: #c0392b;
}

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
  max-width: 1150px;
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
  width: 4.3%;
}

th:nth-child(2),
td:nth-child(2) {
  width: 10%;
  padding-left: 15px;
}

th:nth-child(3),
td:nth-child(3),
th:nth-child(4),
td:nth-child(4),
th:nth-child(5),
td:nth-child(5) {
  width: 10%;
}

th:nth-child(6),
td:nth-child(6) {
  width: 10%;
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7),
td:nth-child(7) {
  box-shadow: inset 2px 4px 11px rgba(134, 187, 240, 0.5);
  width: 8.8%;
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
  font-size: 15px;
}

select,
input {
  width: 100%;
  padding: 8px 12px;
  border-color: #7bb0ec;
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

.custom_input {
  padding: 5px;
 border-color: #7bb0ec;
  color: rgb(48, 45, 45);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}
.add-button {

  border:1px solid white ;
}

.custom_nameInput {
  height: 35px;
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
.pagination {
  display: flex;
  justify-content:left;
  gap: 5px;
  margin-top: 20px;
}

.pagination button {
  padding: 4px 9px;
  border: 1px solid #ddd;
  border-radius: 6px;
  background-color: #f9f9f9;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pagination button:hover {
  background-color: #1675e2;
  color: white;
}

.pagination button.active {
  background-color: #275b8f;
  color: white;

}

.pagination button:disabled {
  background-color: #ddd;
  cursor: not-allowed;
}

</style>