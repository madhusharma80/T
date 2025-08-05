<template>
  <div class="container">
    <h1 class="title">EMPLOYEE TASK MANAGEMENT - TO-DO LIST</h1>
    <table class="table">
      <thead>
        <tr>
          <th>s.no</th>
          <th>
            <select v-model="newEmployee.email" class="custom-dropdown">
              <option value="">Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </th>

          <th>
            <select v-model="newEmployee.department_id" class="custom-dropdown">
              <option value="">Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </th>

          <th>
            <select v-model="newEmployee.designation_id" class="custom-dropdown">
              <option value="">Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </th>
          <th>
            <select v-model="newEmployee.assigned_to" class="custom-dropdown">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned.id" :value="assigned.id">{{ assigned.name }}</option>
            </select>
          </th>
          <th>
            <input v-model="newEmployee.description" class="custom-input" type="text" placeholder="Enter Description" />
          </th>
          <!-- Status -->
          <th>
            <select v-model="newEmployee.status" class="custom-dropdown">
              <option value="">Status</option>
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Complete">Complete</option>
            </select>
          </th>

          <th>
            <button class="btn btn-primary w-100 add-button" @click="addEmployee" :disabled="isAddDisabled"><i class="fas fa-plus"></i>Add</button>
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(employee, index) in employees" :key="index">
          <td>{{ index + 1 }}.</td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.email }}</span>
            <select v-else v-model="employee.email" class="custom-dropdown">
              <option value="">Select Email</option>
              <option v-for="email in employeeEmails" :key="email" :value="email">{{ email }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.department.name }}</span>
            <select v-else v-model="employee.department_id" class="custom-dropdown">
              <option value="">Select Department</option>
              <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.designation.name }}</span>
            <select v-else v-model="employee.designation_id" class="custom-dropdown">
              <option value="">Select Designation</option>
              <option v-for="designation in designations" :key="designation.id" :value="designation.id">{{ designation.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ getAssignedToName(employee.assigned_to) }}</span>
            <select v-else v-model="employee.assigned_to" class="custom-dropdown">
              <option value="">Assigned To</option>
              <option v-for="assigned in assignedTo" :key="assigned.id" :value="assigned.id">{{ assigned.name }}</option>
            </select>
          </td>

          <td>
            <span v-if="!employee.isEditing">{{ employee.description }}</span>
            <input v-else v-model="employee.description" class="custom-input" type="text" placeholder="Enter Description" />
          </td>

          <!-- Task Status with background color -->
          <td :class="statusClass(employee.status)">
            <span v-if="!employee.isEditing">{{ employee.status }}</span>
            <select v-else v-model="employee.status" class="custom-dropdown">
              <option value="">Select Status</option>
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Complete">Complete</option>
            </select>
          </td>

          <td>
            <div class="button-group">
              <button v-if="!employee.isEditing" class="btn  edit-button " @click="editEmployee(employee)"><i class="fas fa-edit"></i></button>
              <button v-if="employee.isEditing" class="btn btn-success save-button" @click="saveEmployee(employee, index)"><i class="fas fa-save"></i></button>
              <button class="btn delete-button" @click="deleteEmployee(index)"><i class="fas fa-trash"></i></button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';

const employees = ref([]);
const departments = ref([]);
const designations = ref([]);
const employeeEmails = ref([]);
const assignedTo = ref([]);
const newEmployee = ref({
  email: '',
  department_id: '',
  designation_id: '',
  assigned_to: '',
  description: '',
  status: ''
});

const isAddDisabled = computed(() => {
  return !newEmployee.value.email || !newEmployee.value.department_id || !newEmployee.value.designation_id || !newEmployee.value.assigned_to || !newEmployee.value.description || !newEmployee.value.status;
});

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
    assignedTo.value = data.employees.map(employee => ({
      id: employee.id,
      name: employee.email
    }));

    const storedEmployees = localStorage.getItem('employees');
    if (storedEmployees) {
      employees.value = JSON.parse(storedEmployees);
    }

  } catch (error) {
    console.error('Error fetching data:', error);
  }
});

const addEmployee = () => {
  if (isAddDisabled.value) {
    return;
  }

  const newEmployeeData = {
    email: newEmployee.value.email,
    department_id: newEmployee.value.department_id,
    designation_id: newEmployee.value.designation_id,
    assigned_to: newEmployee.value.assigned_to,
    description: newEmployee.value.description,
    status: newEmployee.value.status,  // Include status
    department: departments.value.find(department => department.id === newEmployee.value.department_id),
    designation: designations.value.find(designation => designation.id === newEmployee.value.designation_id),
    assigned_to_name: assignedTo.value.find(assigned => assigned.id === newEmployee.value.assigned_to).name,
    isEditing: false,
  };

  employees.value.push(newEmployeeData);
  localStorage.setItem('employees', JSON.stringify(employees.value));
  resetForm();
};
//name of the assigned employee based on their id.
const getAssignedToName = (assignedToId) => {
  const assignedEmployee = assignedTo.value.find(assigned => assigned.id === assignedToId);
  return assignedEmployee ? assignedEmployee.name : '';
};
//Edit button function 
const editEmployee = (employee) => {
  employee.isEditing = true;
  newEmployee.value = {
    email: employee.email,
    department_id: employee.department_id,
    designation_id: employee.designation_id,
    assigned_to: employee.assigned_to,
    description: employee.description,
    status: employee.status
  };
};
//Save button function 
const saveEmployee = (employee, index) => {
  employee.isEditing = false;
  employees.value[index] = { 
    ...employee,
    department: departments.value.find(department => department.id === employee.department_id),
    designation: designations.value.find(designation => designation.id === employee.designation_id),
    assigned_to_name: assignedTo.value.find(assigned => assigned.id === employee.assigned_to)?.name
  };

  localStorage.setItem('employees', JSON.stringify(employees.value));
  resetForm();
};
//Deletes a task from the employees array and updates localStorage.
const deleteEmployee = (index) => {
  employees.value.splice(index, 1);
  if (employees.value.length === 0) {
    localStorage.removeItem('employees');
  } else {
    localStorage.setItem('employees', JSON.stringify(employees.value));
  }
};
//clearing the form fields.Resets the newEmployee
const resetForm = () => {
  newEmployee.value = {
    email: '',
    department_id: '',
    designation_id: '',
    assigned_to: '',
    description: '',
    status: ''
  };
};

// Method to determine the background color class based on the status
const statusClass = (status) => {
  if (status === 'Pending') return 'status-pending';
  if (status === 'In Progress') return 'status-in-progress';
  if (status === 'Complete') return 'status-complete';
  return '';
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
  background:  linear-gradient(to bottom, #275b8f, #b4cfe7);
  color: white;
  width: 14%;
}

th:first-child, td:first-child {
  width: 4.4%;
}

th:nth-child(2), td:nth-child(2) {
  width: 11%;
  padding-left: 15px;
}

th:nth-child(3), td:nth-child(3),
th:nth-child(4), td:nth-child(4),
th:nth-child(5), td:nth-child(5){
  width: 11%;
}

th:nth-child(6), td:nth-child(6) {
  width: 13%;
  text-align: left;
  white-space: normal;
  word-wrap: break-word;
}

th:nth-child(7), td:nth-child(7) {
  width: 8.5%;

}
th:nth-child(8), td:nth-child(8) {
  width:9%;
}
th:first-child, td:first-child,
th:nth-child(2), td:nth-child(2),
th:nth-child(3), td:nth-child(3),
th:nth-child(4), td:nth-child(4),
th:nth-child(5), td:nth-child(5),
th:nth-child(6),td:nth-child(6),
th:nth-child(8) ,td:nth-child(8){
  padding: 10px;
  border: 2px solid #cacaca;
  overflow: hidden;
  word-wrap: break-word;
  white-space: normal;
  box-shadow: inset 2px 4px   19px rgba(85, 86, 87, 0.5);
  color: rgb(43, 41, 41);
  font-size: 14px;
}

th:nth-child(7) {
  padding: 10px;
  overflow: hidden;
  word-wrap: break-word;
  white-space: normal;
  font-size: 14px;
}
select, input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #ffffff;
  font-size: 14px;
  font-family: 'Arial', sans-serif;
  transition: border-color 0.3s ease;
}

select:focus, input:focus {
  border-color: #2c87f0;
  outline: none;
}

.add-button {
  width: 20%;
  padding: 8px;
  border-color: #4396f5;
  color: rgb(48, 45, 45);
  border-radius: 5px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
}
.add-button:hover {
  background-color: #1675e2;
  color: rgb(247, 246, 246);
}

.delete-button {
  padding: 8px 12px;
   background-color:white ;
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
  background-color:white ;
  color: white;
  border-color:  #347537;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  justify-content: center;
  align-items: center;
}

.save-button{
 padding: 8px 12px;
  background-color:white ;
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

.status-pending {
  background-color: #fadadd;
  color: #721c24;
  border: 1px solid #ec2748;
  font-size: 14px;
}

.status-in-progress {
  background-color: #fff5d4; 
  color: #856404;
  border: 1px solid  #daac24;
  font-size: 14px;
}

.status-complete {
  background-color: #d2ffdd; 
  color: #155724;
  border: 1px solid #2ac54e;
  font-size: 14px;
}
.custom-dropdown {
  width: 100%;
  padding: 8px 12px;
  border-radius: 4px;
  background-color: #fff;
  border: 1px solid #59b3e7;
}

.custom-dropdown:focus {
  border-color: #4e9bec;
}

.custom-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
}
</style>