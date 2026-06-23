/**
 * ADEEEEE Frontend API Client
 * Connects all frontend pages to backend APIs
 */

const API_BASE = window.location.pathname.includes('/admin_dashboard/') ? '../api/' : '../api/';
const API_ADMIN = API_BASE + 'admin/';
const API_USER = API_BASE + 'user/';
const API_AUTH = API_BASE + 'auth.php';

// Auth functions
const Auth = {
    async login(email, password) {
        const res = await fetch(API_AUTH + '?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        return res.json();
    },
    
    async register(data) {
        const res = await fetch(API_AUTH + '?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    },
    
    async logout() {
        await fetch(API_AUTH + '?action=logout');
        window.location.href = 'signin.php';
    },
    
    async me() {
        const res = await fetch(API_AUTH + '?action=me');
        return res.json();
    }
};

// Admin APIs
const AdminAPI = {
    async getEmployees(params = {}) {
        const qs = new URLSearchParams(params).toString();
        const res = await fetch(API_ADMIN + 'employees.php?action=list&' + qs);
        return res.json();
    },
    
    async createEmployee(data) {
        const res = await fetch(API_ADMIN + 'employees.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    },
    
    async updateEmployee(id, data) {
        const res = await fetch(API_ADMIN + 'employees.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({...data, id})
        });
        return res.json();
    },
    
    async deleteEmployee(id) {
        const res = await fetch(API_ADMIN + 'employees.php?id=' + id, { method: 'DELETE' });
        return res.json();
    },
    
    async getLeaveRequests(params = {}) {
        const qs = new URLSearchParams(params).toString();
        const res = await fetch(API_ADMIN + 'leaves.php?action=list&' + qs);
        return res.json();
    },
    
    async approveLeave(id) {
        const res = await fetch(API_ADMIN + 'leaves.php?action=approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        return res.json();
    },
    
    async rejectLeave(id, reason) {
        const res = await fetch(API_ADMIN + 'leaves.php?action=reject', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, reason })
        });
        return res.json();
    },
    
    async getAttendance(date) {
        const res = await fetch(API_ADMIN + 'attendance.php?action=list&date=' + date);
        return res.json();
    },
    
    async getAttendanceSummary() {
        const res = await fetch(API_ADMIN + 'attendance.php?action=summary');
        return res.json();
    },
    
    async getPayrollPeriods() {
        const res = await fetch(API_ADMIN + 'payroll.php?action=periods');
        return res.json();
    },
    
    async getPayrollEntries(periodId) {
        const res = await fetch(API_ADMIN + 'payroll.php?action=entries&period_id=' + periodId);
        return res.json();
    }
};

// User/Employee APIs
const UserAPI = {
    async getDashboard() {
        const res = await fetch(API_USER + 'dashboard.php');
        return res.json();
    },
    
    async getLeaves() {
        const res = await fetch(API_USER + 'leaves.php?action=list');
        return res.json();
    },
    
    async getLeaveTypes() {
        const res = await fetch(API_USER + 'leaves.php?action=types');
        return res.json();
    },
    
    async getLeaveBalance() {
        const res = await fetch(API_USER + 'leaves.php?action=balance');
        return res.json();
    },
    
    async applyLeave(data) {
        const res = await fetch(API_USER + 'leaves.php?action=apply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    },
    
    async cancelLeave(id) {
        const res = await fetch(API_USER + 'leaves.php?action=cancel', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        return res.json();
    },
    
    async getTodayAttendance() {
        const res = await fetch(API_USER + 'attendance.php?action=today');
        return res.json();
    },
    
    async getAttendanceHistory(start, end) {
        const res = await fetch(API_USER + `attendance.php?action=history&start=${start}&end=${end}`);
        return res.json();
    },
    
    async clockIn(notes = '') {
        const res = await fetch(API_USER + 'attendance.php?action=clock_in', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ notes })
        });
        return res.json();
    },
    
    async clockOut() {
        const res = await fetch(API_USER + 'attendance.php?action=clock_out', {
            method: 'POST'
        });
        return res.json();
    },
    
    async startBreak() {
        const res = await fetch(API_USER + 'attendance.php?action=break_start', {
            method: 'POST'
        });
        return res.json();
    },
    
    async endBreak() {
        const res = await fetch(API_USER + 'attendance.php?action=break_end', {
            method: 'POST'
        });
        return res.json();
    },
    
    async getExpenses() {
        const res = await fetch(API_USER + 'expenses.php');
        return res.json();
    },
    
    async submitExpense(data) {
        const res = await fetch(API_USER + 'expenses.php?action=submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        return res.json();
    }
};

// Toast notification helper
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Form submission helper
async function submitForm(form, apiUrl, method = 'POST', onSuccess = null) {
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    
    try {
        const res = await fetch(apiUrl, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (result.success) {
            showToast(result.message || 'Operation successful', 'success');
            if (onSuccess) onSuccess(result);
        } else {
            showToast(result.message || 'Operation failed', 'error');
        }
        
        return result;
    } catch (err) {
        showToast('Network error: ' + err.message, 'error');
        return { success: false, message: err.message };
    }
}

// Table rendering helper
function renderTable(tableId, data, columns, actions = null) {
    const tbody = document.querySelector(`#${tableId} tbody`);
    if (!tbody) return;
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${columns.length + (actions ? 1 : 0)}" class="text-center">No records found</td></tr>`;
        return;
    }
    
    tbody.innerHTML = data.map(row => {
        let html = '<tr>';
        columns.forEach(col => {
            const val = row[col.key] ?? '-';
            html += `<td>${col.format ? col.format(val, row) : val}</td>`;
        });
        
        if (actions) {
            html += '<td>' + actions(row).join(' ') + '</td>';
        }
        
        html += '</tr>';
        return html;
    }).join('');
}

// Auto-initialize data attributes
// Add data-api attributes to elements to auto-load data
document.addEventListener('DOMContentLoaded', () => {
    // Auto-load tables with data-api attribute
    document.querySelectorAll('[data-api]').forEach(el => {
        const api = el.dataset.api;
        const render = el.dataset.render;
        // Implementation would load data and render based on render type
    });
});

// Export for use in other scripts
window.ADEEEEE = { Auth, AdminAPI, UserAPI, showToast, submitForm, renderTable };
