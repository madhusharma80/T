<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <th>S.No.</th>
          <!-- Employee Email -->
          <th>
            <select v-model="newEmployee.email">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </th>

          <!-- Department -->
          <th>
            <select v-model="newEmployee.department_id">
              <option value=""> Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </th>

          <!-- Designation -->
          <th>
            <select v-model="newEmployee.designation_id">
              <option value=""> Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </th>

          <!-- Assigned To -->
          <th>
            <select v-model="newEmployee.assigned_to">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned.id" :value="assigned.id">{{ assigned.name }}</option>
            </select>
          </th>

          <!-- Description -->
          <th>
            <input v-model="newEmployee.description" type="text" placeholder="Enter Description" />
          </th>

          <!-- Add Button -->
          <th>
            <button class="btn btn-primary add_btn w-100" @click="addEmployee" :disabled="isAddDisabled"><i class="fas fa-plus"></i>Add</button>
          </th>
        </tr>
      </thead>

      <tbody>
        <!-- Employee Table Rows -->
        <tr v-for="(employee, index) in employees" :key="index">
          <!-- Serial Number -->
          <td>{{ index + 1 }}</td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.email }}</span>
            <select v-else v-model="employee.email">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.department.name }}</span>
            <select v-else v-model="employee.department_id">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.designation.name }}</span>
            <select v-else v-model="employee.designation_id">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ getAssignedToName(employee.assigned_to) }}</span>
            <select v-else v-model="employee.assigned_to">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned.id" :value="assigned.id">{{ assigned.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.description }}</span>
            <input v-else v-model="employee.description" type="text" placeholder="Enter Description" />
          </td>

          <td>
            <div class="button-group">
              <button v-if="!employee.isEditing" class="btn btn-secondary" @click="editEmployee(employee)"><i class="fas fa-edit"></i></button>
              <button v-if="employee.isEditing" class="btn btn-success" @click="saveEmployee(employee, index)"><i class="fas fa-save"></i></button>
              <button class="btn btn-danger" @click="deleteEmployee(index)">  <i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

// Define the reactive data
const employees = ref([]); // Only users' added employees
const departments = ref([]);
const designations = ref([]);
const employeeEmails = ref([]);
const assignedTo = ref([]);
const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  assigned_to: '',
  description: ''
});

// Define the state for disabling the Add button
const isAddDisabled = computed(() => {
  return !newEmployee.value.email || !newEmployee.value.department_id || !newEmployee.value.designation_id || !newEmployee.value.assigned_to || !newEmployee.value.description;
});

// Fetch data when component is mounted
onMounted(async () => {
  try {
    const response = await fetch('/api/department-designation-data', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`  // Include token for authentication
      }
    });

    const data = await response.json();

    // Set departments and designations (These are the only pre-loaded values)
    departments.value = data.departments;
    designations.value = data.designations;

    // Populate missing properties for employee dropdowns (we won't load employees yet)
    employeeEmails.value = data.employees.map(employee => employee.email);

    // Populate the assignedTo field with employee names and ids (not showing pre-loaded employees)
    assignedTo.value = data.employees.map(employee => ({
      id: employee.id,
      name: employee.email // Or use employee.name if you want that
    }));

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

// Add a new employee to the employees array
const addEmployee = () => {
  if (isAddDisabled.value) {
    return; // Don't add if fields are incomplete
  }

  // Push the new employee to the list with default "isEditing" as false
  employees.value.push({
    email: newEmployee.value.email,
    department_id: newEmployee.value.department_id,
    designation_id: newEmployee.value.designation_id,
    assigned_to: newEmployee.value.assigned_to,
    description: newEmployee.value.description,
    department: departments.value.find(department => department.id === newEmployee.value.department_id),
    designation: designations.value.find(designation => designation.id === newEmployee.value.designation_id),
    assigned_to_name: assignedTo.value.find(assigned => assigned.id === newEmployee.value.assigned_to).name, // Added name for display
    isEditing: false, // New employee is not in editing mode
  });

  // Reset the form fields after adding the employee
  resetForm();
};

// Get the assigned "name" from the assignedTo array using the ID
const getAssignedToName = (assignedToId) => {
  const assignedEmployee = assignedTo.value.find(assigned => assigned.id === assignedToId);
  return assignedEmployee ? assignedEmployee.name : '';
};

// Edit employee - set `isEditing` to true to enable editing mode
const editEmployee = (employee) => {
  employee.isEditing = true;

  // Populate form with the employee's current values
  newEmployee.value = {
    email: employee.email,
    department_id: employee.department_id,
    designation_id: employee.designation_id,
    assigned_to: employee.assigned_to,
    description: employee.description,
  };
};

// Save employee after editing
const saveEmployee = (employee, index) => {
  employee.isEditing = false;

  // Update the employee data in the list with the newly edited values
  employees.value[index] = { 
    ...employee,
    department: departments.value.find(department => department.id === employee.department_id),
    designation: designations.value.find(designation => designation.id === employee.designation_id),
    assigned_to_name: assignedTo.value.find(assigned => assigned.id === employee.assigned_to)?.name
  };

  resetForm();
};

const deleteEmployee = (index) => {
  employees.value.splice(index, 1);
};

const resetForm = () => {
  newEmployee.value = {
    email: '',
    department_id: '',
    designation_id: '',
    assigned_to: '',
    description: ''
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
  width:100%;
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
  padding: 8px;
  border: 2px solid #ddd;
  background: linear-gradient(to bottom, #3a699b, #8daeca);
  color: white;
  width: 14%; 
}

th:first-child, td:first-child {
  width: 3.5%; 
  padding:12px 11px;
  
}

th:nth-child(2), td:nth-child(2) {
  width: 11%;
  padding-left: 15px;
}

th:nth-child(3), td:nth-child(3),
th:nth-child(4), td:nth-child(4),
th:nth-child(5), td:nth-child(5),
th:nth-child(6), td:nth-child(6) {
width: 10%; 

}

th:nth-child(6), td:nth-child(6) {
  width: 11%; 
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7), td:nth-child(7) {
  width:10%;
}
td {
  padding: 12px;
  border: 2px solid #ddd;
  overflow: hidden;
  word-wrap: break-word;
  white-space: normal;
  background: linear-gradient(to bottom, #3a699b, #8daeca);
  color:white;
  font-size: 15.4px;
}

select, input {
  width: 100%;
  padding: 5px;
  border: 1px solid #ddd;
  margin-top: 5px;
  box-sizing: border-box; 
}

button {
  padding: 5px 10px;
  cursor: pointer;
  margin-top: 5px;
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

.add_btn {
  width: 100%;
  background-color: #244a92;
}

button:hover {
  background-color: #f4f4f4;
  color: black;
}
</style>
