/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * User & Role Management (RBAC) Client Logic
 */

let activeUserEditId = null;

const rbacFilterState = {
  role: 'all',
  status: 'all',
  query: ''
};

document.addEventListener('DOMContentLoaded', () => {
  updateUserCounts();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }

  // Keyboard shortcut Ctrl+K to jump to search
  document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      const input = document.getElementById('userSearchInput');
      if (input) {
        input.focus();
        input.select();
      }
    }
  });
});

/* ==========================================================================
   FILTERING & SEARCH
   ========================================================================== */
function filterUsersByRole(role) {
  rbacFilterState.role = role;

  document.querySelectorAll('.user-filter-pill').forEach(pill => {
    if (pill.getAttribute('data-role') === role) {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  applyUserFilters();
}

function handleUserSearch(query) {
  rbacFilterState.query = (query || '').toLowerCase().trim();

  const clearBtn = document.getElementById('userSearchClear');
  if (clearBtn) {
    clearBtn.style.display = rbacFilterState.query ? 'flex' : 'none';
  }

  applyUserFilters();
}

function clearUserSearch() {
  const input = document.getElementById('userSearchInput');
  if (input) {
    input.value = '';
    input.focus();
  }
  handleUserSearch('');
}

function handleStatusFilter(status) {
  rbacFilterState.status = status;
  applyUserFilters();
}

function resetAllUserFilters() {
  rbacFilterState.role = 'all';
  rbacFilterState.status = 'all';
  rbacFilterState.query = '';

  const input = document.getElementById('userSearchInput');
  if (input) input.value = '';

  const clearBtn = document.getElementById('userSearchClear');
  if (clearBtn) clearBtn.style.display = 'none';

  const statusSelect = document.getElementById('userStatusFilterSelect');
  if (statusSelect) statusSelect.value = 'all';

  document.querySelectorAll('.user-filter-pill').forEach(pill => {
    if (pill.getAttribute('data-role') === 'all') {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  applyUserFilters();
}

function applyUserFilters() {
  const rows = document.querySelectorAll('#usersTableBody tr[data-user-id]');
  const emptyRow = document.getElementById('userNoResultsRow');
  let visibleCount = 0;

  rows.forEach(row => {
    const role = (row.getAttribute('data-user-role') || '').toLowerCase();
    const status = (row.getAttribute('data-user-status') || '').toLowerCase();
    const text = row.innerText.toLowerCase();

    // 1. Role Filter
    let matchesRole = false;
    if (rbacFilterState.role === 'all') {
      matchesRole = true;
    } else if (rbacFilterState.role === role) {
      matchesRole = true;
    }

    // 2. Status Filter
    let matchesStatus = false;
    if (rbacFilterState.status === 'all') {
      matchesStatus = true;
    } else if (rbacFilterState.status === status) {
      matchesStatus = true;
    }

    // 3. Search Query
    let matchesQuery = false;
    if (!rbacFilterState.query || text.includes(rbacFilterState.query)) {
      matchesQuery = true;
    }

    if (matchesRole && matchesStatus && matchesQuery) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  if (emptyRow) {
    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
  }
}

function updateUserCounts() {
  const rows = document.querySelectorAll('#usersTableBody tr[data-user-id]');
  let total = rows.length;
  let director = 0;
  let recommending = 0;
  let staff = 0;
  let external = 0;

  rows.forEach(row => {
    const role = row.getAttribute('data-user-role');
    if (role === 'clear') director++;
    else if (role === 'rec') recommending++;
    else if (role === 'staff' || role === 'gsu') staff++;
    else if (role === 'external') external++;
  });

  const countTotal = document.getElementById('kpiTotalUsers');
  const countDir = document.getElementById('kpiDirectorCount');
  const countRec = document.getElementById('kpiRecommendingCount');
  const countStaff = document.getElementById('kpiStaffCount');

  if (countTotal) countTotal.textContent = total;
  if (countDir) countDir.textContent = director;
  if (countRec) countRec.textContent = recommending;
  if (countStaff) countStaff.textContent = staff + external;

  const pillTotal = document.getElementById('pillCountAll');
  const pillDir = document.getElementById('pillCountClear');
  const pillRec = document.getElementById('pillCountRec');
  const pillStaff = document.getElementById('pillCountStaff');
  const pillExt = document.getElementById('pillCountExt');

  if (pillTotal) pillTotal.textContent = total;
  if (pillDir) pillDir.textContent = director;
  if (pillRec) pillRec.textContent = recommending;
  if (pillStaff) pillStaff.textContent = staff;
  if (pillExt) pillExt.textContent = external;
}

/* ==========================================================================
   ROLE CONFIGURATION MODAL
   ========================================================================== */
function openEditRoleModal(userId, name, email, role, dept) {
  activeUserEditId = userId;
  const modal = document.getElementById('editRoleModal');
  if (!modal) return;

  const nameEl = document.getElementById('editRoleUserName');
  const emailEl = document.getElementById('editRoleUserEmail');
  const deptEl = document.getElementById('editRoleUserDept');
  const avatarEl = document.getElementById('editRoleUserAvatar');
  const roleDropdown = document.getElementById('roleSelectDropdown');

  if (nameEl) nameEl.textContent = name;
  if (emailEl) emailEl.textContent = email;
  if (deptEl) deptEl.textContent = dept;

  if (avatarEl) {
    const parts = name.split(' ');
    const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : name.slice(0, 2).toUpperCase();
    avatarEl.textContent = initials;
  }

  if (roleDropdown) {
    roleDropdown.value = role;
    syncRolePermissionsCheckboxes(role);
    roleDropdown.onchange = function () {
      syncRolePermissionsCheckboxes(this.value);
    };
  }

  modal.style.display = 'flex';
}

function syncRolePermissionsCheckboxes(role) {
  const permInspect = document.getElementById('permInspect');
  const permRecommend = document.getElementById('permRecommend');
  const permClearance = document.getElementById('permClearance');
  const permMaintenance = document.getElementById('permMaintenance');
  const permUserMgmt = document.getElementById('permUserMgmt');

  if (permInspect) permInspect.checked = (role === 'rec' || role === 'clear' || role === 'gsu');
  if (permRecommend) permRecommend.checked = (role === 'rec' || role === 'clear');
  if (permClearance) permClearance.checked = (role === 'clear');
  if (permMaintenance) permMaintenance.checked = (role === 'clear' || role === 'gsu');
  if (permUserMgmt) permUserMgmt.checked = (role === 'clear');
}

function closeEditRoleModal() {
  const modal = document.getElementById('editRoleModal');
  if (modal) modal.style.display = 'none';
  activeUserEditId = null;
}

function saveRoleChanges(e) {
  if (e) e.preventDefault();
  if (!activeUserEditId) return;

  const roleDropdown = document.getElementById('roleSelectDropdown');
  const newRole = roleDropdown ? roleDropdown.value : 'staff';
  const row = document.querySelector(`tr[data-user-id="${activeUserEditId}"]`);
  const userNameEl = document.getElementById('editRoleUserName');
  const userName = userNameEl ? userNameEl.textContent : 'Personnel';

  if (row) {
    row.setAttribute('data-user-role', newRole);

    const roleCell = row.children[2];
    const privCell = row.children[3];
    const avatarBadge = row.querySelector('.user-avatar-badge');

    if (avatarBadge) {
      avatarBadge.className = 'user-avatar-badge ' + (newRole === 'clear' ? 'director' : newRole === 'rec' ? 'recommending' : newRole === 'gsu' ? 'gsu' : newRole === 'external' ? 'external' : 'staff');
    }

    if (newRole === 'clear') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-clearance">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
          Stage 2: Final Clearance Authority
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
    } else if (newRole === 'rec') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-recommend">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Stage 1: Recommending Officer
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Schedule Verification & Recommending Endorsement</span>`;
    } else if (newRole === 'gsu') {
      roleCell.innerHTML = `
        <span class="user-role-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
          Facility & Maintenance Officer
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Venue Maintenance Hold & Physical Inspection</span>`;
    } else if (newRole === 'staff') {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-staff">
          Division Personnel / Requestor
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>`;
    } else {
      roleCell.innerHTML = `
        <span class="user-role-badge status-role-external">
          External Agency Partner
        </span>
      `;
      privCell.innerHTML = `<span class="privilege-summary-tag">External Facility Booking Applicant</span>`;
    }
  }

  updateUserCounts();
  showUserToast(`Role updated for ${userName}. Clearance credentials refreshed.`, 'success');
  closeEditRoleModal();
}

/* ==========================================================================
   STATUS TOGGLE (ACTIVE / SUSPENDED)
   ========================================================================== */
function toggleUserStatus(userId) {
  const row = document.querySelector(`tr[data-user-id="${userId}"]`);
  if (!row) return;

  const currentStatus = row.getAttribute('data-user-status');
  const statusCell = row.children[4];
  const toggleBtn = row.querySelector('.btn-user-action.status-toggle');
  const name = row.querySelector('.user-name-title')?.textContent || 'User';

  if (currentStatus === 'active') {
    row.setAttribute('data-user-status', 'suspended');
    statusCell.innerHTML = `
      <span class="status-badge-chip suspended">
        <span class="chip-status-dot"></span>
        Suspended
      </span>
    `;
    if (toggleBtn) {
      toggleBtn.innerHTML = `
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
        <span>Activate</span>
      `;
      toggleBtn.classList.add('activate');
      toggleBtn.title = "Reactivate user account";
    }
    showUserToast(`Access suspended for ${name}.`, 'decline');
  } else {
    row.setAttribute('data-user-status', 'active');
    statusCell.innerHTML = `
      <span class="status-badge-chip active">
        <span class="chip-status-dot"></span>
        Active
      </span>
    `;
    if (toggleBtn) {
      toggleBtn.innerHTML = `
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
        <span>Suspend</span>
      `;
      toggleBtn.classList.remove('activate');
      toggleBtn.title = "Suspend user access";
    }
    showUserToast(`Account restored to Active status for ${name}.`, 'success');
  }

  applyUserFilters();
}

/* ==========================================================================
   ADD USER MODAL
   ========================================================================== */
function openAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'none';
  const form = document.getElementById('addUserForm');
  if (form) form.reset();
}

function submitNewUser(e) {
  if (e) e.preventDefault();

  const nameInput = document.getElementById('newUserName');
  const emailInput = document.getElementById('newUserEmail');
  const deptInput = document.getElementById('newUserDept');
  const roleSelect = document.getElementById('newUserRole');

  if (!nameInput || !emailInput || !deptInput || !roleSelect) return;

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const dept = deptInput.value.trim();
  const role = roleSelect.value;

  if (!name || !email || !dept) {
    alert('Please fill out all required fields.');
    return;
  }

  const newId = 'USR-' + Math.floor(1000 + Math.random() * 9000);
  const parts = name.split(' ');
  const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : name.slice(0, 2).toUpperCase();

  let roleHtml = '';
  let privHtml = '';
  let badgeClass = 'staff';

  if (role === 'clear') {
    badgeClass = 'director';
    roleHtml = `<span class="user-role-badge status-role-clearance"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>Stage 2: Final Clearance Authority</span>`;
    privHtml = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
  } else if (role === 'rec') {
    badgeClass = 'recommending';
    roleHtml = `<span class="user-role-badge status-role-recommend"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>Stage 1: Recommending Officer</span>`;
    privHtml = `<span class="privilege-summary-tag">Schedule Verification & Recommending Endorsement</span>`;
  } else if (role === 'gsu') {
    badgeClass = 'gsu';
    roleHtml = `<span class="user-role-badge" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Facility & Maintenance Officer</span>`;
    privHtml = `<span class="privilege-summary-tag">Venue Maintenance Hold & Physical Inspection</span>`;
  } else if (role === 'staff') {
    badgeClass = 'staff';
    roleHtml = `<span class="user-role-badge status-role-staff">Division Personnel / Requestor</span>`;
    privHtml = `<span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>`;
  } else {
    badgeClass = 'external';
    roleHtml = `<span class="user-role-badge status-role-external">External Agency Partner</span>`;
    privHtml = `<span class="privilege-summary-tag">External Facility Booking Applicant</span>`;
  }

  const tbody = document.getElementById('usersTableBody');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.setAttribute('data-user-id', newId);
    tr.setAttribute('data-user-role', role);
    tr.setAttribute('data-user-status', 'active');

    tr.innerHTML = `
      <td>
        <div class="user-avatar-cell">
          <div class="user-avatar-badge ${badgeClass}">
            ${initials}
            <span class="user-online-dot"></span>
          </div>
          <div>
            <div class="user-name-title">${name}</div>
            <span class="user-email-text">${email}</span>
          </div>
        </div>
      </td>
      <td>
        <span class="user-dept-text">${dept}</span>
      </td>
      <td>${roleHtml}</td>
      <td>${privHtml}</td>
      <td>
        <span class="status-badge-chip active">
          <span class="chip-status-dot"></span>
          Active
        </span>
      </td>
      <td style="text-align: right;">
        <div class="user-action-btns">
          <button type="button" class="btn-user-action edit" onclick="openEditRoleModal('${newId}', '${name.replace(/'/g, "\\'")}', '${email}', '${role}', '${dept.replace(/'/g, "\\'")}')" title="Configure clearance and roles">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
            <span>Role</span>
          </button>
          <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus('${newId}')" title="Suspend user access">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
            <span>Suspend</span>
          </button>
        </div>
      </td>
    `;

    tbody.insertBefore(tr, tbody.firstChild);
  }

  updateUserCounts();
  showUserToast(`Official account registered for ${name} (${newId}).`, 'success');
  closeAddUserModal();
}

/* ==========================================================================
   SIDEBAR TOGGLE
   ========================================================================== */
function toggleSidebar(forceState) {
  const layout = document.querySelector('.admin-layout-container');
  const sidebar = document.getElementById('adminSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (!sidebar) return;

  const isMobile = window.innerWidth <= 960;

  if (isMobile) {
    const willOpen = typeof forceState === 'boolean' ? forceState : !sidebar.classList.contains('open');
    if (willOpen) {
      sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('show');
    } else {
      sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('show');
    }
  } else {
    // Desktop: toggle collapsed state
    const isCollapsed = layout ? layout.classList.contains('sidebar-collapsed') : false;
    const willCollapse = typeof forceState === 'boolean' ? !forceState : !isCollapsed;

    if (layout) {
      if (willCollapse) {
        layout.classList.add('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', 'true');
      } else {
        layout.classList.remove('sidebar-collapsed');
        localStorage.setItem('admin_sidebar_collapsed', 'false');
      }
    }
  }
}

/* ==========================================================================
   TOAST HELPER
   ========================================================================== */
function showUserToast(message, type = 'success') {
  let container = document.getElementById('adminToastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'adminToastContainer';
    container.className = 'admin-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `admin-toast ${type === 'decline' ? 'decline' : ''}`;
  toast.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
      ${type === 'decline'
      ? '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>'
      : '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'}
    </svg>
    <span>${message}</span>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    setTimeout(() => toast.remove(), 320);
  }, 4000);
}
