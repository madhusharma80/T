<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <!-- Employee ID -->
          <th>
            <select v-model="newEmployee.id">
              <option value="">Select Id</option>
              <option v-for="id in employeeIds" :key="id" :value="id">{{ id }}</option>
            </select>
          </th>

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
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </th>

          <!-- Designation -->
          <th>
            <select v-model="newEmployee.designation_id">
              <option value="">Select Designation</option>
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

          <!-- Add Button -->
          <th>
            <button class="btn btn-primary add_btn" @click="addEmployee" :disabled="isAddDisabled">Add</button>
          </th>
        </tr>
      </thead>

      <tbody>
        <!-- Employee Table Rows -->
        <tr v-for="(employee, index) in employees" :key="employee.id">
          <td>
            <span v-if="!employee.isEditing">{{ employee.id }}</span>
            <select v-else v-model="employee.id">
              <option value="">Select Id</option>
              <option v-for="id in employeeIds" :key="id" :value="id">{{ id }}</option>
            </select>
          </td>

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
            <span v-if="!employee.isEditing">{{ employee.assigned_to.name }}</span>
            <select v-else v-model="employee.assigned_to">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned.id" :value="assigned.id">{{ assigned.name }}</option>
            </select>
          </td>

          <td>
            <div class="button-group">
              <button v-if="!employee.isEditing" class="btn btn-secondary" @click="editEmployee(employee)">Edit</button>
              <button v-if="employee.isEditing" class="btn btn-success" @click="saveEmployee(employee, index)">Save</button>
              <button class="btn btn-danger" @click="deleteEmployee(index)">Delete</button>
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
const employeeIds = ref([]);
const employeeEmails = ref([]);
const assignedTo = ref([]);
const newEmployee = ref({
  id: '',
  email: '',
  department_id: '',
  designation_id: '',
  assigned_to: ''
});

// Define the state for disabling the Add button
const isAddDisabled = computed(() => {
  return !newEmployee.value.id || !newEmployee.value.email || !newEmployee.value.department_id || !newEmployee.value.designation_id || !newEmployee.value.assigned_to;
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
    employeeIds.value = data.employees.map(employee => employee.id);
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
    id: newEmployee.value.id,
    email: newEmployee.value.email,
    department_id: newEmployee.value.department_id,
    designation_id: newEmployee.value.designation_id,
    assigned_to: newEmployee.value.assigned_to,  // Ensure the correct value is added
    department: departments.value.find(department => department.id === newEmployee.value.department_id),
    designation: designations.value.find(designation => designation.id === newEmployee.value.designation_id),
    assigned_to_name: assignedTo.value.find(assigned => assigned.id === newEmployee.value.assigned_to).name,
    isEditing: false, // New employee is not in editing mode
  });

  // Reset the form fields after adding the employee
  resetForm();
};

// Edit employee - set `isEditing` to true to enable editing mode
const editEmployee = (employee) => {
  employee.isEditing = true;

  // Populate form with the employee's current values
  newEmployee.value = {
    id: employee.id,
    email: employee.email,
    department_id: employee.department_id,
    designation_id: employee.designation_id,
    assigned_to: employee.assigned_to,
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

  // Reset form after saving the edited employee
  resetForm();
};

// Delete an employee from the list
const deleteEmployee = (index) => {
  employees.value.splice(index, 1); // Remove employee at the specified index
};

// Reset the form fields
const resetForm = () => {
  newEmployee.value = {
    id: '',
    email: '',
    department_id: '',
    designation_id: '',
    assigned_to: ''
  };
};
</script>

<style scoped>
.title {
  border-bottom: 1px solid #679fd8;
  text-align: center;
  color: #333;
  font-size: 1.7em;
  font-weight: 600;
  font-family: serif;
}
.container {
  max-width: 1000px;
  margin: 100px auto;
  background-color: #f8f9fa;
  padding: 80px;
  border-radius: 8px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border: 1px solid rgb(118, 165, 209);
}
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  padding: 12px;
  border: 2px solid #ddd;
  background: linear-gradient(to bottom, #3a699b, #8daeca);
  color:white;
}
select {
  width: 100%;
  padding: 5px;
  border: 1px solid #ddd;
  margin-top: 5px;
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
  background-color: #ddd;
}
button:hover {
  background-color: #f4f4f4;
  color:black;
}
input {
  padding: 5px;
  width: 100%;
}
</style>
