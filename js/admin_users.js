/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * User & Role Management (RBAC) Client Controller - Governance Workflow
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

  // Keyboard shortcut Ctrl+K to jump to quick search
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

  // Attach dynamic listener to role dropdown inside Edit Role modal
  const roleDropdown = document.getElementById('roleSelectDropdown');
  if (roleDropdown) {
    roleDropdown.addEventListener('change', function () {
      syncRolePermissionsCheckboxes(this.value);
    });
  }
});

/* ==========================================================================
   FILTERING & LIVE SEARCH
   ========================================================================== */
function filterUsersByRole(role) {
  rbacFilterState.role = role;

  // Update pills
  document.querySelectorAll('.user-filter-pill').forEach(pill => {
    if (pill.getAttribute('data-role') === role) {
      pill.classList.add('active');
    } else {
      pill.classList.remove('active');
    }
  });

  // Update stat cards
  document.querySelectorAll('.admin-kpi-grid .my-res-stat-card').forEach(card => {
    const onclickStr = card.getAttribute('onclick') || '';
    if (onclickStr.includes(`'${role}'`)) {
      card.classList.add('active-filter');
    } else {
      card.classList.remove('active-filter');
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

  // Sync with topbar search input if triggered from elsewhere
  const topbarInput = document.getElementById('topbarSearchInput');
  const mainInput = document.getElementById('userSearchInput');
  if (topbarInput && topbarInput.value !== query && document.activeElement !== topbarInput) {
    topbarInput.value = query;
  }
  if (mainInput && mainInput.value !== query && document.activeElement !== mainInput) {
    mainInput.value = query;
  }

  applyUserFilters();
}

function clearUserSearch() {
  const mainInput = document.getElementById('userSearchInput');
  const topbarInput = document.getElementById('topbarSearchInput');
  if (mainInput) mainInput.value = '';
  if (topbarInput) topbarInput.value = '';
  handleUserSearch('');
  if (mainInput) mainInput.focus();
}

function handleStatusFilter(status) {
  rbacFilterState.status = status;
  applyUserFilters();
}

function resetAllUserFilters() {
  rbacFilterState.role = 'all';
  rbacFilterState.status = 'all';
  rbacFilterState.query = '';

  const mainInput = document.getElementById('userSearchInput');
  const topbarInput = document.getElementById('topbarSearchInput');
  if (mainInput) mainInput.value = '';
  if (topbarInput) topbarInput.value = '';

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

  document.querySelectorAll('.admin-kpi-grid .my-res-stat-card').forEach(card => {
    const onclickStr = card.getAttribute('onclick') || '';
    if (onclickStr.includes("'all'")) {
      card.classList.add('active-filter');
    } else {
      card.classList.remove('active-filter');
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
    const isArchived = (row.getAttribute('data-user-archived') === '1');
    const text = row.innerText.toLowerCase();

    // Archive isolation: if viewing 'archived', show only archived. Otherwise, show only non-archived.
    if (rbacFilterState.role === 'archived') {
      if (!isArchived) {
        row.style.display = 'none';
        return;
      }
    } else {
      if (isArchived) {
        row.style.display = 'none';
        return;
      }
    }

    // 1. Role Filter Match
    let matchesRole = false;
    if (rbacFilterState.role === 'all' || rbacFilterState.role === 'archived') {
      matchesRole = true;
    } else if (rbacFilterState.role === 'endorsement') {
      matchesRole = (role === 'endorsement' || role === 'endorser' || role === 'division_chief');
    } else if (rbacFilterState.role === 'recommendation') {
      matchesRole = (role === 'recommendation' || role === 'recommender' || role === 'admin');
    } else if (rbacFilterState.role === 'clearance') {
      matchesRole = (role === 'clearance' || role === 'director');
    } else if (rbacFilterState.role === 'super_admin') {
      matchesRole = (role === 'super_admin');
    } else if (rbacFilterState.role === 'user') {
      matchesRole = (role === 'user' || role === 'staff' || role === 'external');
    }

    // 2. Status Filter Match
    let matchesStatus = false;
    if (rbacFilterState.status === 'all' || rbacFilterState.role === 'archived') {
      matchesStatus = true;
    } else if (rbacFilterState.status === 'active') {
      matchesStatus = (status === 'active');
    } else if (rbacFilterState.status === 'suspended') {
      matchesStatus = (status === 'suspended' || status === 'inactive');
    } else if (rbacFilterState.status === 'pending') {
      matchesStatus = (status === 'pending');
    }

    // 3. Search Query Match
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
  let totalActive = 0;
  let active = 0;
  let endorsement = 0;
  let recommendation = 0;
  let clearance = 0;
  let superAdmin = 0;
  let requestors = 0;
  let archived = 0;

  rows.forEach(row => {
    const isArchived = (row.getAttribute('data-user-archived') === '1');
    if (isArchived) {
      archived++;
      return;
    }

    totalActive++;
    const role = (row.getAttribute('data-user-role') || '').toLowerCase();
    const status = (row.getAttribute('data-user-status') || '').toLowerCase();

    if (status === 'active') active++;

    if (role === 'endorsement' || role === 'endorser' || role === 'division_chief') {
      endorsement++;
    } else if (role === 'recommendation' || role === 'recommender' || role === 'admin') {
      recommendation++;
    } else if (role === 'clearance' || role === 'director') {
      clearance++;
    } else if (role === 'super_admin') {
      superAdmin++;
    } else {
      requestors++;
    }
  });

  // KPI Stat Cards
  const kpiTotal = document.getElementById('kpiTotalUsers');
  const kpiAct = document.getElementById('kpiActiveUsers');
  const kpiEndorse = document.getElementById('kpiEndorsementCount');
  const kpiRec = document.getElementById('kpiRecommendingCount');
  const kpiClear = document.getElementById('kpiClearanceCount');
  const kpiStaff = document.getElementById('kpiStaffCount');

  if (kpiTotal) kpiTotal.textContent = totalActive;
  if (kpiAct) kpiAct.textContent = `${active} Active`;
  if (kpiEndorse) kpiEndorse.textContent = endorsement;
  if (kpiRec) kpiRec.textContent = recommendation;
  if (kpiClear) kpiClear.textContent = clearance;
  if (kpiStaff) kpiStaff.textContent = requestors + superAdmin;

  // Filter Tabs
  const pillTotal = document.getElementById('pillCountAll');
  const pillEndorse = document.getElementById('pillCountEndorse');
  const pillRec = document.getElementById('pillCountRec');
  const pillClear = document.getElementById('pillCountClear');
  const pillSuper = document.getElementById('pillCountSuper');
  const pillStaff = document.getElementById('pillCountStaff');
  const pillArchived = document.getElementById('pillCountArchived');

  if (pillTotal) pillTotal.textContent = totalActive;
  if (pillEndorse) pillEndorse.textContent = endorsement;
  if (pillRec) pillRec.textContent = recommendation;
  if (pillClear) pillClear.textContent = clearance;
  if (pillSuper) pillSuper.textContent = superAdmin;
  if (pillStaff) pillStaff.textContent = requestors;
  if (pillArchived) pillArchived.textContent = archived;

  // Sidebar count badge
  const sidebarCount = document.getElementById('sidebarUsersCount');
  if (sidebarCount) sidebarCount.textContent = totalActive;
}

/* ==========================================================================
   ROLE CONFIGURATION MODAL (GOVERNANCE)
   ========================================================================== */
function openEditRoleModal(userId, name, email, role, dept) {
  activeUserEditId = userId;
  const modal = document.getElementById('editRoleModal');
  if (!modal) return;

  const idInput = document.getElementById('editRoleUserId');
  const nameEl = document.getElementById('editRoleUserName');
  const emailEl = document.getElementById('editRoleUserEmail');
  const deptEl = document.getElementById('editRoleUserDept');
  const deptInput = document.getElementById('editRoleUserDeptInput');
  const avatarEl = document.getElementById('editRoleUserAvatar');
  const roleDropdown = document.getElementById('roleSelectDropdown');

  if (idInput) idInput.value = userId;
  if (nameEl) nameEl.textContent = name;
  if (emailEl) emailEl.textContent = email;
  if (deptEl) deptEl.textContent = dept || 'ATI Central Office';
  if (deptInput) deptInput.value = dept || '';

  if (avatarEl) {
    const parts = name.trim().split(/\s+/);
    const initials = parts.length > 1
      ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
      : name.slice(0, 2).toUpperCase();
    avatarEl.textContent = initials;
  }

  // Normalize role
  let selectedRole = role.toLowerCase();
  if (selectedRole === 'endorser' || selectedRole === 'division_chief') selectedRole = 'endorsement';
  if (selectedRole === 'recommender' || selectedRole === 'admin') selectedRole = 'recommendation';
  if (selectedRole === 'director') selectedRole = 'clearance';
  if (selectedRole === 'staff' || selectedRole === 'external') selectedRole = 'user';

  if (roleDropdown) {
    roleDropdown.value = selectedRole;
    syncRolePermissionsCheckboxes(selectedRole);
  }

  modal.style.display = 'flex';
}

function syncRolePermissionsCheckboxes(role) {
  const permInspect = document.getElementById('permInspect');
  const permEndorse = document.getElementById('permEndorse');
  const permRecommend = document.getElementById('permRecommend');
  const permClearance = document.getElementById('permClearance');
  const permUserMgmt = document.getElementById('permUserMgmt');

  const r = role.toLowerCase();
  const isSuper = (r === 'super_admin');
  const isClear = (r === 'clearance' || isSuper);
  const isRec = (r === 'recommendation' || isSuper);
  const isEndorse = (r === 'endorsement' || isSuper);

  if (permInspect) permInspect.checked = (isEndorse || isRec || isClear);
  if (permEndorse) permEndorse.checked = isEndorse;
  if (permRecommend) permRecommend.checked = isRec;
  if (permClearance) permClearance.checked = isClear;
  if (permUserMgmt) permUserMgmt.checked = isSuper;
}

function closeEditRoleModal() {
  const modal = document.getElementById('editRoleModal');
  if (modal) modal.style.display = 'none';
  activeUserEditId = null;
}

async function saveRoleChanges(e) {
  if (e) e.preventDefault();
  if (!activeUserEditId) return;

  const roleDropdown = document.getElementById('roleSelectDropdown');
  const deptInput = document.getElementById('editRoleUserDeptInput');
  const newRole = roleDropdown ? roleDropdown.value : 'user';
  const newDept = deptInput ? deptInput.value.trim() : '';

  const submitBtn = e ? e.target.querySelector('button[type="submit"]') : null;
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';
  }

  try {
    const formData = new FormData();
    formData.append('action', 'update_role');
    formData.append('user_id', activeUserEditId);
    formData.append('role', newRole);
    formData.append('office_agency', newDept);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-user-id="${activeUserEditId}"]`);
      if (row) {
        row.setAttribute('data-user-role', newRole);

        // Update Dept
        const deptText = row.querySelector('.user-dept-text');
        if (deptText) deptText.textContent = newDept || 'ATI Central Office';

        // Update Role Badge and Privilege Tag
        const roleCell = row.children[2];
        const privCell = row.children[3];
        const avatarBadge = row.querySelector('.user-avatar-badge');

        if (newRole === 'endorsement') {
          if (avatarBadge) avatarBadge.className = 'user-avatar-badge endorsement';
          if (roleCell) {
            roleCell.innerHTML = `
              <span class="user-role-badge status-role-endorse">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                Endorsement Officer
              </span>
            `;
          }
          if (privCell) privCell.innerHTML = `<span class="privilege-summary-tag">Division Endorsement, Facilities & Venue Setups</span>`;
        } else if (newRole === 'recommendation') {
          if (avatarBadge) avatarBadge.className = 'user-avatar-badge recommending';
          if (roleCell) {
            roleCell.innerHTML = `
              <span class="user-role-badge status-role-recommend">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Recommending Officer
              </span>
            `;
          }
          if (privCell) privCell.innerHTML = `<span class="privilege-summary-tag">Schedule Verification & Logistics Recommendation</span>`;
        } else if (newRole === 'clearance') {
          if (avatarBadge) avatarBadge.className = 'user-avatar-badge director';
          if (roleCell) {
            roleCell.innerHTML = `
              <span class="user-role-badge status-role-clearance">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                Clearance Authority
              </span>
            `;
          }
          if (privCell) privCell.innerHTML = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
        } else if (newRole === 'super_admin') {
          if (avatarBadge) avatarBadge.className = 'user-avatar-badge super_admin';
          if (roleCell) {
            roleCell.innerHTML = `
              <span class="user-role-badge status-role-super">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                Super Administrator
              </span>
            `;
          }
          if (privCell) privCell.innerHTML = `<span class="privilege-summary-tag">Master Executive Access across All Modules</span>`;
        } else {
          if (avatarBadge) avatarBadge.className = 'user-avatar-badge staff';
          if (roleCell) {
            roleCell.innerHTML = `
              <span class="user-role-badge status-role-staff">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Authorized Requestor
              </span>
            `;
          }
          if (privCell) privCell.innerHTML = `<span class="privilege-summary-tag">Submit Reservations & Track Clearance Status</span>`;
        }
      }

      updateUserCounts();
      showUserToast(result.message || 'Governance role saved successfully.', 'success');
      closeEditRoleModal();
    } else {
      showUserToast(result.message || 'Failed to update role.', 'decline');
    }
  } catch (err) {
    showUserToast('Server connection error. Please try again.', 'decline');
  } finally {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  }
}

/* ==========================================================================
   STATUS TOGGLE (ACTIVE / SUSPENDED) VIA REAL AJAX
   ========================================================================== */
async function toggleUserStatus(userId) {
  const row = document.querySelector(`tr[data-user-id="${userId}"]`);
  if (!row) return;

  const currentStatus = row.getAttribute('data-user-status');
  const newStatus = (currentStatus === 'active') ? 'suspended' : 'active';
  const name = row.querySelector('.user-name-title')?.textContent || 'User';

  try {
    const formData = new FormData();
    formData.append('action', 'toggle_status');
    formData.append('user_id', userId);
    formData.append('status', newStatus);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      row.setAttribute('data-user-status', newStatus);
      const statusCell = row.children[4];
      const toggleBtn = row.querySelector('.btn-user-action.status-toggle');
      const dot = row.querySelector('.user-online-dot');

      if (newStatus === 'suspended') {
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-badge-chip suspended">
              <span class="chip-status-dot"></span>
              Suspended
            </span>
          `;
        }
        if (toggleBtn) {
          toggleBtn.innerHTML = `
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Activate</span>
          `;
          toggleBtn.classList.add('activate');
          toggleBtn.title = 'Reactivate access';
        }
        if (dot) dot.classList.add('offline');
        showUserToast(`Access suspended for ${name}.`, 'decline');
      } else {
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-badge-chip active">
              <span class="chip-status-dot"></span>
              Active
            </span>
          `;
        }
        if (toggleBtn) {
          toggleBtn.innerHTML = `
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
            <span>Suspend</span>
          `;
          toggleBtn.classList.remove('activate');
          toggleBtn.title = 'Suspend access';
        }
        if (dot) dot.classList.remove('offline');
        showUserToast(`Account restored to Active status for ${name}.`, 'success');
      }

      updateUserCounts();
      applyUserFilters();
    } else {
      showUserToast(result.message || 'Failed to change user status.', 'decline');
    }
  } catch (err) {
    showUserToast('Server error toggling status.', 'decline');
  }
}

/* ==========================================================================
   SOFT DELETE USER ACCOUNT (MOVE TO TRASH)
   ========================================================================== */
async function confirmDeleteUser(userId, name) {
  if (!confirm(`Are you sure you want to archive user "${name}" and move this account to Trash? The user's historical records will be preserved.`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'soft_delete_user');
    formData.append('user_id', userId);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-user-id="${userId}"]`);
      if (row) {
        row.setAttribute('data-user-archived', '1');
        row.setAttribute('data-user-status', 'inactive');
        row.classList.add('row-user-archived');

        // Update actions cell
        const actionCell = row.querySelector('.user-action-btns');
        if (actionCell) {
          actionCell.innerHTML = `
            <button type="button" class="btn-user-action restore" onclick="confirmRestoreUser(${userId}, '${name.replace(/'/g, "\\'")}')" title="Restore user to Active list">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"></polyline><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path></svg>
              <span>Restore</span>
            </button>
            <button type="button" class="btn-user-action purge" onclick="confirmPermanentDeleteUser(${userId}, '${name.replace(/'/g, "\\'")}')" title="Permanently delete from database">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Purge</span>
            </button>
          `;
        }

        // Update status badge
        const statusCell = row.cells[4];
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-badge-chip archived" style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;">
              <span class="chip-status-dot" style="background: #dc2626;"></span>
              Archived
            </span>
          `;
        }

        // Animate row transition
        row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(20px)';
        setTimeout(() => {
          row.style.display = 'none';
          row.style.opacity = '1';
          row.style.transform = '';
          updateUserCounts();
          applyUserFilters();
        }, 260);
      }
      showUserToast(result.message || `User ${name} moved to Trash.`, 'decline');
    } else {
      showUserToast(result.message || 'Failed to archive user.', 'decline');
    }
  } catch (err) {
    showUserToast('Server error while archiving user.', 'decline');
  }
}

/* ==========================================================================
   RESTORE USER ACCOUNT FROM TRASH
   ========================================================================== */
async function confirmRestoreUser(userId, name) {
  if (!confirm(`Restore user "${name}" back to the active personnel directory?`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'restore_user');
    formData.append('user_id', userId);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-user-id="${userId}"]`);
      if (row) {
        row.setAttribute('data-user-archived', '0');
        row.setAttribute('data-user-status', 'active');
        row.classList.remove('row-user-archived');

        // Restore action cell
        const actionCell = row.querySelector('.user-action-btns');
        const role = row.getAttribute('data-user-role') || 'user';
        if (actionCell) {
          actionCell.innerHTML = `
            <button type="button" class="btn-user-action edit" onclick="openEditRoleModal(${userId}, '${name.replace(/'/g, "\\'")}', '', '${role}', '')" title="Configure clearance role">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
              <span>Role</span>
            </button>
            <button type="button" class="btn-user-action status-toggle" onclick="toggleUserStatus(${userId})" title="Suspend access">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
              <span>Suspend</span>
            </button>
            <button type="button" class="btn-user-action purge" onclick="confirmDeleteUser(${userId}, '${name.replace(/'/g, "\\'")}')" title="Delete user account (Move to Trash)">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              <span>Delete</span>
            </button>
          `;
        }

        // Update status badge
        const statusCell = row.cells[4];
        if (statusCell) {
          statusCell.innerHTML = `
            <span class="status-badge-chip active">
              <span class="chip-status-dot"></span>
              Active
            </span>
          `;
        }

        // Animate row transition
        row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
          row.style.display = 'none';
          row.style.opacity = '1';
          row.style.transform = '';
          updateUserCounts();
          applyUserFilters();
        }, 260);
      }
      showUserToast(result.message || `User ${name} has been restored.`, 'success');
    } else {
      showUserToast(result.message || 'Failed to restore user.', 'decline');
    }
  } catch (err) {
    showUserToast('Server error while restoring user.', 'decline');
  }
}

/* ==========================================================================
   PERMANENTLY PURGE USER ACCOUNT (HARD DELETE)
   ========================================================================== */
async function confirmPermanentDeleteUser(userId, name) {
  if (!confirm(`CAUTION: Are you sure you want to PERMANENTLY purge "${name}" from the MySQL database?\n\nThis will completely delete this record. This cannot be undone.`)) {
    return;
  }

  try {
    const formData = new FormData();
    formData.append('action', 'permanent_delete_user');
    formData.append('user_id', userId);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      const row = document.querySelector(`tr[data-user-id="${userId}"]`);
      if (row) {
        row.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
        row.style.opacity = '0';
        row.style.transform = 'scale(0.9)';
        setTimeout(() => {
          row.remove();
          updateUserCounts();
          applyUserFilters();
        }, 260);
      }
      showUserToast(result.message || `User ${name} permanently purged.`, 'decline');
    } else {
      showUserToast(result.message || 'Failed to purge user.', 'decline');
    }
  } catch (err) {
    showUserToast('Server error while purging user.', 'decline');
  }
}

/* ==========================================================================
   ADD USER MODAL & AJAX SUBMIT
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

async function submitNewUser(e) {
  if (e) e.preventDefault();

  const nameInput = document.getElementById('newUserName');
  const emailInput = document.getElementById('newUserEmail');
  const phoneInput = document.getElementById('newUserPhone');
  const deptInput = document.getElementById('newUserDept');
  const catInput = document.getElementById('newUserCategory');
  const passInput = document.getElementById('newUserPassword');
  const roleInput = document.getElementById('newUserRole');

  const name = nameInput ? nameInput.value.trim() : '';
  const email = emailInput ? emailInput.value.trim() : '';
  const phone = phoneInput ? phoneInput.value.trim() : '';
  const dept = deptInput ? deptInput.value.trim() : '';
  const cat = catInput ? catInput.value : 'ATI Personnel';
  const pass = passInput ? passInput.value : '';
  const role = roleInput ? roleInput.value : 'user';

  if (!name || !email) {
    showUserToast('Please provide both full name and email.', 'decline');
    return;
  }

  const submitBtn = e ? e.target.querySelector('button[type="submit"]') : null;
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Registering...';
  }

  try {
    const formData = new FormData();
    formData.append('action', 'add_user');
    formData.append('full_name', name);
    formData.append('email', email);
    formData.append('contact_number', phone);
    formData.append('office_agency', dept);
    formData.append('category', cat);
    formData.append('password', pass);
    formData.append('role', role);

    const response = await fetch('ajax/user_actions.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (result.success) {
      showUserToast(result.message || `Personnel ${name} registered successfully.`, 'success');
      closeAddUserModal();

      // Smooth page refresh to synchronize server state
      setTimeout(() => {
        window.location.reload();
      }, 700);
    } else {
      showUserToast(result.message || 'Failed to register user.', 'decline');
    }
  } catch (err) {
    showUserToast('Server error while registering user.', 'decline');
  } finally {
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  }
}

/* ==========================================================================
   TOAST NOTIFICATION ENGINE
   ========================================================================== */
function showUserToast(message, type = 'success') {
  const container = document.getElementById('adminToastContainer');
  if (!container) return;

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

// Sidebar toggle helper
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
