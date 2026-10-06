/**
 * Agriculture Training Institute - Super Admin Dashboard Script
 * Unified ("Terno") Interactive Controller
 */

// In-memory data store for sample records
const reservationsData = {
  'R-2026-0891': {
    ref: 'R-2026-0891',
    applicant: 'Engr. Juan Dela Cruz',
    agency: 'ATI - Career Development Division (CDD)',
    email: 'juan.delacruz@ati.da.gov.ph',
    phone: '+63 917 123 4567',
    venue: 'Serrano Function Hall',
    schedule: 'October 12 – 14, 2026 (3 Days)',
    pax: '85 Participants',
    eventTitle: 'National TOT on Climate-Resilient Agriculture',
    equipment: 'Projector & Screen, 4 Wireless Microphones, Stage Rostrum, Classroom Type Seating',
    status: 'pending'
  },
  'R-2026-0892': {
    ref: 'R-2026-0892',
    applicant: 'Dr. Maria Santos',
    agency: 'Bureau of Plant Industry (BPI)',
    email: 'maria.santos@bpi.gov.ph',
    phone: '+63 918 987 6543',
    venue: 'Dormitory Suite A & B',
    schedule: 'October 15 – 18, 2026 (4 Days)',
    pax: '32 Delegates',
    eventTitle: 'Inter-Agency Seed Inspection Workshop',
    equipment: '32 Single Beds, Linen Sets, Common Lounge Access, Hot Water Showers',
    status: 'pending'
  },
  'R-2026-0893': {
    ref: 'R-2026-0893',
    applicant: 'Atty. Bernardo Castro',
    agency: 'DA - Legal Service Office',
    email: 'bcastro@da.gov.ph',
    phone: '+63 920 555 1212',
    venue: 'Executive Boardroom',
    schedule: 'October 08, 2026 (1:00 PM – 5:00 PM)',
    pax: '18 PAX',
    eventTitle: 'Administrative Adjudication Hearing',
    equipment: 'Video Conference System, Audio Recording System, Smart Display Board',
    status: 'pending'
  },
  'R-2026-0894': {
    ref: 'R-2026-0894',
    applicant: 'Ramon Pascual',
    agency: 'PhilRice - Extension Division',
    email: 'rpascual@philrice.gov.ph',
    phone: '+63 922 444 3322',
    venue: '4-H Learning Center',
    schedule: 'October 20 – 22, 2026 (3 Days)',
    pax: '60 PAX',
    eventTitle: 'Digital Agriculture & Rice Crop Manager Training',
    equipment: 'High-speed WiFi Hub, Dual Projectors, 60 Ergonomic Chairs & Tables',
    status: 'pending'
  },
  'R-2026-0895': {
    ref: 'R-2026-0895',
    applicant: 'Carmela Lim',
    agency: 'ATI - Information Services (ISD)',
    email: 'carmela.lim@ati.da.gov.ph',
    phone: '+63 917 888 9900',
    venue: 'Dormitory Executive Suite',
    schedule: 'October 25 – 27, 2026 (3 Days)',
    pax: '14 Delegates',
    eventTitle: 'Knowledge Management Technical Working Group Accommodations',
    equipment: 'Executive Suite Rooms, WiFi Access, Working Desks, Welcome Refreshments',
    status: 'pending'
  },
  'R-2026-0888': {
    ref: 'R-2026-0888',
    applicant: 'Grace Valenzuela',
    agency: 'DA - National Rice Program',
    email: 'gvalenzuela@da.gov.ph',
    phone: '+63 928 333 7788',
    venue: 'Serrano Hall',
    schedule: 'October 06 – 07, 2026 (2 Days)',
    pax: '100 PAX',
    eventTitle: 'Q4 National Rice Assessment & Planning Workshop',
    equipment: 'Full Audio-Visual Setup, Live Stream Equipment, VIP Holding Area',
    status: 'approved'
  }
};

let currentDeclineTargetRef = null;
let currentModalActiveRef = null;

document.addEventListener('DOMContentLoaded', function () {
  initDateDisplay();
  initSmoothSidebarScrolling();
  updateKPIBadges();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }
});

function initSmoothSidebarScrolling() {
  document.querySelectorAll('.sidebar-menu-item[href^="#"]').forEach(link => {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href').substring(1);
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        e.preventDefault();
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (window.innerWidth <= 960) {
          toggleSidebar(false);
        }
      }
    });
  });
}

/* ==========================================================================
   SIDEBAR TOGGLE (RESPONSIVE & DESKTOP)
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

function initDateDisplay() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (dateEl) {
    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString('en-US', options);
  }
}

/* ==========================================================================
   APPROVE RESERVATION ACTION
   ========================================================================== */
function approveReservation(refId, applicantName, venueName) {
  const row = document.querySelector(`tr[data-ref="${refId}"]`);
  if (!row) return;

  // Update record
  if (reservationsData[refId]) {
    reservationsData[refId].status = 'approved';
  }

  // Update row status
  row.setAttribute('data-status', 'approved');
  const statusCell = row.querySelector('td:nth-child(6)');
  if (statusCell) {
    statusCell.innerHTML = `
      <span class="status-pill status-approved">
        <span class="status-dot"></span>
        Confirmed & Approved
      </span>
    `;
  }

  // Update actions cell
  const actionsCell = row.querySelector('td:nth-child(7)');
  if (actionsCell) {
    actionsCell.innerHTML = `
      <div class="action-buttons-wrap">
        <span class="badge-approved-note">Approved by Director</span>
        <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')" title="View Booking Form">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
      </div>
    `;
  }

  // Update counters
  updateKPIBadges();

  // Audit Log Entry
  addAuditLogEntry(`Approved: Reservation <span class="audit-ref">${refId}</span> for ${venueName} was approved by the Clearance Authority.`, 'green');

  // Toast
  showAdminToast(`Reservation ${refId} officially approved! Applicant ${applicantName} notified.`, 'success');
}

/* ==========================================================================
   DECLINE MODAL & ACTION
   ========================================================================== */
function openDeclineModal(refId, applicantName) {
  currentDeclineTargetRef = refId;
  const modal = document.getElementById('declineModal');
  const modalRef = document.getElementById('declineModalRef');
  const modalApplicant = document.getElementById('declineModalApplicant');
  const remarks = document.getElementById('declineRemarks');

  if (modalRef) modalRef.textContent = refId;
  if (modalApplicant) modalApplicant.textContent = applicantName;
  if (remarks) remarks.value = '';

  if (modal) modal.style.display = 'flex';
}

function closeDeclineModal() {
  const modal = document.getElementById('declineModal');
  if (modal) modal.style.display = 'none';
  currentDeclineTargetRef = null;
}

function submitDeclineAction() {
  if (!currentDeclineTargetRef) return;
  const refId = currentDeclineTargetRef;
  const reasonSelect = document.getElementById('declineReasonSelect');
  const reason = reasonSelect ? reasonSelect.value : 'Administrative Grounds';

  const row = document.querySelector(`tr[data-ref="${refId}"]`);
  if (row) {
    row.setAttribute('data-status', 'rejected');
    const statusCell = row.querySelector('td:nth-child(6)');
    if (statusCell) {
      statusCell.innerHTML = `
        <span class="status-pill status-cancelled">
          <span class="status-dot"></span>
          Declined
        </span>
      `;
    }

    const actionsCell = row.querySelector('td:nth-child(7)');
    if (actionsCell) {
      actionsCell.innerHTML = `
        <div class="action-buttons-wrap">
          <span style="font-size: 0.78rem; font-weight: 700; color: #b91c1c; background: #fee2e2; padding: 0.35rem 0.65rem; border-radius: 9999px;">Declined</span>
          <button type="button" class="btn-table-action view" onclick="viewReservationDetails('${refId}')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </button>
        </div>
      `;
    }
  }

  if (reservationsData[refId]) {
    reservationsData[refId].status = 'rejected';
  }

  closeDeclineModal();
  updateKPIBadges();

  addAuditLogEntry(`Declined: Reservation <span class="audit-ref">${refId}</span> was declined (${reason}).`, 'amber');
  showAdminToast(`Reservation ${refId} has been declined. Official notice sent.`, 'decline');
}

/* ==========================================================================
   VIEW RESERVATION DETAILS MODAL
   ========================================================================== */
function viewReservationDetails(refId) {
  const data = reservationsData[refId];
  if (!data) return;

  currentModalActiveRef = refId;

  document.getElementById('modalRefId').textContent = data.ref;
  document.getElementById('modalApplicantName').textContent = data.applicant;
  document.getElementById('modalApplicantOrg').textContent = data.agency;
  document.getElementById('modalApplicantContact').textContent = `${data.email} • ${data.phone}`;
  document.getElementById('modalVenueName').textContent = data.venue;
  document.getElementById('modalSchedule').textContent = data.schedule;
  document.getElementById('modalPax').textContent = data.pax;
  document.getElementById('modalEventTitle').textContent = data.eventTitle;
  document.getElementById('modalEquipment').textContent = data.equipment;

  // Avatar initials
  const initials = data.applicant.split(' ').map(n => n[0]).slice(0, 2).join('');
  document.getElementById('modalAvatar').textContent = initials;

  // Button state depending on status
  const btnApprove = document.getElementById('btnModalApprove');
  const btnDecline = document.getElementById('btnModalDecline');

  if (data.status === 'approved') {
    btnApprove.style.display = 'none';
    btnDecline.style.display = 'none';
  } else if (data.status === 'rejected') {
    btnApprove.style.display = 'none';
    btnDecline.style.display = 'none';
  } else {
    btnApprove.style.display = 'inline-flex';
    btnDecline.style.display = 'inline-flex';
  }

  const modal = document.getElementById('reservationDetailsModal');
  if (modal) modal.style.display = 'flex';
}

function closeReservationModal() {
  const modal = document.getElementById('reservationDetailsModal');
  if (modal) modal.style.display = 'none';
  currentModalActiveRef = null;
}

function modalTriggerApprove() {
  if (currentModalActiveRef && reservationsData[currentModalActiveRef]) {
    const item = reservationsData[currentModalActiveRef];
    closeReservationModal();
    approveReservation(item.ref, item.applicant, item.venue);
  }
}

function modalTriggerDecline() {
  if (currentModalActiveRef && reservationsData[currentModalActiveRef]) {
    const item = reservationsData[currentModalActiveRef];
    closeReservationModal();
    openDeclineModal(item.ref, item.applicant);
  }
}

/* ==========================================================================
   FILTER TABS, VENUE SELECT & SYNCHRONIZED SEARCH
   ========================================================================== */
const adminFilterState = {
  tab: 'all',
  venue: 'all',
  query: ''
};

function applyTabFilter(filter) {
  adminFilterState.tab = filter;

  // Update tab buttons
  document.querySelectorAll('.filter-tab-btn').forEach(btn => {
    if (btn.getAttribute('data-filter') === filter) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  applyCombinedAdminFilters();
}

function handleAdminSearch(query) {
  const cleanVal = query || '';
  adminFilterState.query = cleanVal.toLowerCase().trim();

  // Synchronize topbar and toolbar search inputs
  const topInput = document.getElementById('topbarSearchInput');
  const barInput = document.getElementById('adminSearchInput');
  if (topInput && topInput.value !== cleanVal) topInput.value = cleanVal;
  if (barInput && barInput.value !== cleanVal) barInput.value = cleanVal;

  // Toggle clear buttons
  const topClear = document.getElementById('topbarSearchClear');
  const barClear = document.getElementById('adminSearchClear');
  const isFilled = cleanVal.length > 0;
  if (topClear) topClear.style.display = isFilled ? 'flex' : 'none';
  if (barClear) barClear.style.display = isFilled ? 'flex' : 'none';

  applyCombinedAdminFilters();
}

function clearAdminSearch() {
  handleAdminSearch('');
  const barInput = document.getElementById('adminSearchInput');
  if (barInput) barInput.focus();
}

function handleVenueSelectFilter(venue) {
  adminFilterState.venue = venue;
  applyCombinedAdminFilters();
}

function resetAllAdminFilters() {
  adminFilterState.tab = 'all';
  adminFilterState.venue = 'all';
  adminFilterState.query = '';

  const topInput = document.getElementById('topbarSearchInput');
  const barInput = document.getElementById('adminSearchInput');
  if (topInput) topInput.value = '';
  if (barInput) barInput.value = '';

  const topClear = document.getElementById('topbarSearchClear');
  const barClear = document.getElementById('adminSearchClear');
  if (topClear) topClear.style.display = 'none';
  if (barClear) barClear.style.display = 'none';

  const venueSelect = document.getElementById('venueFilterSelect');
  if (venueSelect) venueSelect.value = 'all';

  document.querySelectorAll('.filter-tab-btn').forEach(btn => {
    if (btn.getAttribute('data-filter') === 'all') {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  applyCombinedAdminFilters();
}

function applyCombinedAdminFilters() {
  const rows = document.querySelectorAll('#adminTableBody tr[data-status]');
  const noResultsRow = document.getElementById('adminNoResultsRow');
  let visibleCount = 0;

  rows.forEach(row => {
    const status = row.getAttribute('data-status') || '';
    const category = row.getAttribute('data-category') || '';
    const rowVenue = (row.getAttribute('data-venue') || '').toLowerCase();
    const rowText = row.innerText.toLowerCase();

    // 1. Check Tab Filter
    let matchesTab = false;
    if (adminFilterState.tab === 'all') {
      matchesTab = true;
    } else if (adminFilterState.tab === 'pending') {
      matchesTab = (status === 'pending');
    } else if (adminFilterState.tab === 'approved') {
      matchesTab = (status === 'approved');
    } else if (adminFilterState.tab === 'halls') {
      matchesTab = (category === 'halls');
    } else if (adminFilterState.tab === 'dorms') {
      matchesTab = (category === 'dorms');
    }

    // 2. Check Venue Dropdown Filter
    let matchesVenue = false;
    if (adminFilterState.venue === 'all') {
      matchesVenue = true;
    } else if (rowVenue.includes(adminFilterState.venue.toLowerCase())) {
      matchesVenue = true;
    }

    // 3. Check Unified Search Query
    let matchesQuery = false;
    if (!adminFilterState.query || rowText.includes(adminFilterState.query)) {
      matchesQuery = true;
    }

    if (matchesTab && matchesVenue && matchesQuery) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });

  if (noResultsRow) {
    noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
  }
}

// Global keyboard shortcut: Ctrl+K or Cmd+K to jump to search
document.addEventListener('keydown', (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    const searchInput = document.getElementById('adminSearchInput') || document.getElementById('topbarSearchInput');
    if (searchInput) {
      searchInput.focus();
      searchInput.select();
    }
  }
});

/* ==========================================================================
   FACILITY MAINTENANCE TOGGLE
   ========================================================================== */
function toggleFacilityMaintenance(venueId, venueName, isMaintenance) {
  const badge = document.getElementById(`badge-${venueId}`);
  if (!badge) return;

  if (isMaintenance) {
    badge.className = 'facility-state-badge state-maintenance';
    badge.textContent = 'Maintenance Hold';
    addAuditLogEntry(`Maintenance Mode activated for <strong>${venueName}</strong> by Super Admin.`, 'amber');
    showAdminToast(`${venueName} placed on Maintenance Hold.`, 'decline');
  } else {
    badge.className = 'facility-state-badge state-available';
    badge.textContent = 'Available';
    addAuditLogEntry(`<strong>${venueName}</strong> restored to Active & Available status.`, 'green');
    showAdminToast(`${venueName} is now Available for bookings.`, 'success');
  }
}

/* ==========================================================================
   AUDIT LOG STREAM & COUNTERS
   ========================================================================== */
function addAuditLogEntry(messageHtml, dotColor = 'green') {
  const stream = document.getElementById('auditStream');
  if (!stream) return;

  const item = document.createElement('div');
  item.className = 'audit-item';
  item.innerHTML = `
    <div class="audit-dot ${dotColor}"></div>
    <div class="audit-content">
      <p>${messageHtml}</p>
      <span class="audit-time">Just now &bull; Executed by Super Admin</span>
    </div>
  `;
  stream.insertBefore(item, stream.firstChild);

  // Smooth scroll to the top of the stream to see the newest entry
  stream.scrollTo({ top: 0, behavior: 'smooth' });

  // Limit stream to 15 entries max to prevent excessive DOM growth
  while (stream.children.length > 15) {
    stream.removeChild(stream.lastChild);
  }
}

function updateKPIBadges() {
  const rows = document.querySelectorAll('#adminTableBody tr');
  let pendingCount = 0;
  let approvedCount = 0;

  rows.forEach(row => {
    const st = row.getAttribute('data-status');
    if (st === 'pending') pendingCount++;
    if (st === 'approved') approvedCount++;
  });

  const kpiPending = document.getElementById('kpiPending');
  const tabPending = document.getElementById('tabCountPending');
  const navBadge = document.getElementById('topNavPendingBadge');
  const sidebarBadge = document.getElementById('sidebarPendingCount');

  if (kpiPending) kpiPending.textContent = pendingCount;
  if (tabPending) tabPending.textContent = pendingCount;
  if (navBadge) {
    navBadge.textContent = pendingCount;
    navBadge.style.display = pendingCount > 0 ? 'inline-block' : 'none';
  }
  if (sidebarBadge) {
    sidebarBadge.textContent = pendingCount;
    sidebarBadge.style.display = pendingCount > 0 ? 'inline-block' : 'none';
  }

  const tabApproved = document.getElementById('tabCountApproved');
  if (tabApproved) tabApproved.textContent = 113 + approvedCount;
}

/* ==========================================================================
   EXPORT REPORT (CSV GENERATOR)
   ========================================================================== */
function exportReservationData() {
  const rows = [
    ['Reference ID', 'Applicant', 'Agency', 'Venue', 'Schedule', 'PAX', 'Status'],
    ['R-2026-0891', 'Engr. Juan Dela Cruz', 'ATI - CDD', 'Serrano Hall', 'Oct 12-14, 2026', '85', 'Pending'],
    ['R-2026-0892', 'Dr. Maria Santos', 'Bureau of Plant Industry', 'Dormitory Suite A & B', 'Oct 15-18, 2026', '32', 'Pending'],
    ['R-2026-0893', 'Atty. Bernardo Castro', 'DA - Legal Service', 'Executive Boardroom', 'Oct 08, 2026', '18', 'Pending'],
    ['R-2026-0894', 'Ramon Pascual', 'PhilRice Extension', '4-H Learning Center', 'Oct 20-22, 2026', '60', 'Pending'],
    ['R-2026-0895', 'Carmela Lim', 'ATI - ISD', 'Dormitory Executive Suite', 'Oct 25-27, 2026', '14', 'Pending'],
    ['R-2026-0888', 'Grace Valenzuela', 'DA - National Rice Program', 'Serrano Hall', 'Oct 06-07, 2026', '100', 'Approved']
  ];

  let csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', 'ATI_Official_Reservations_Report_Oct2026.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showAdminToast('Official Reservations Report downloaded as CSV.', 'success');
}

/* ==========================================================================
   TOAST HELPER
   ========================================================================== */
function showAdminToast(message, type = 'success') {
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

function openSystemSettings() {
  showAdminToast('System Settings module active. Administrative policies up to date.');
}

/* ==========================================================================
   USER MANAGEMENT & ROLE-BASED ACCESS CONTROL (RBAC) CONTROLLERS
   ========================================================================== */

let activeUserEditId = null;

function applyUserFilter(filter, btn) {
  if (btn) {
    document.querySelectorAll('.user-filter-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
  }

  const rows = document.querySelectorAll('#userTableBody tr');
  rows.forEach(row => {
    const role = row.getAttribute('data-user-role');
    if (filter === 'all' || role === filter) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function handleUserSearch(query) {
  const q = (query || '').toLowerCase().trim();
  const rows = document.querySelectorAll('#userTableBody tr');
  rows.forEach(row => {
    const searchMeta = (row.getAttribute('data-search') || '') + ' ' + row.innerText.toLowerCase();
    if (!q || searchMeta.includes(q)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function openEditRoleModal(userId, name, email, role, dept) {
  activeUserEditId = userId;
  const modal = document.getElementById('editRoleModal');
  if (!modal) return;

  const refBadge = document.getElementById('editRoleUserId');
  const nameEl = document.getElementById('editRoleUserName');
  const emailEl = document.getElementById('editRoleUserEmail');
  const deptEl = document.getElementById('editRoleUserDept');
  const avatarEl = document.getElementById('editRoleAvatar');
  const roleDropdown = document.getElementById('roleSelectDropdown');

  if (refBadge) refBadge.textContent = userId;
  if (nameEl) nameEl.textContent = name;
  if (emailEl) emailEl.textContent = email;
  if (deptEl) deptEl.textContent = dept;

  if (avatarEl) {
    const parts = name.replace(/(Engr\.|Atty\.|Dr\.|Ms\.|Mr\.)/g, '').trim().split(' ');
    const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();
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
  const permUserMgmt = document.getElementById('permUserMgmt');

  if (permInspect) permInspect.checked = (role === 'rec' || role === 'clear');
  if (permRecommend) permRecommend.checked = (role === 'rec');
  if (permClearance) permClearance.checked = (role === 'clear');
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
      privCell.innerHTML = `<span class="privilege-summary-tag">Schedule Verification & Administrative Recommendation</span>`;
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

  addAuditLogEntry(`Institutional role for <strong>${userName}</strong> reconfigured to <em>${newRole.toUpperCase()}</em>.`, 'green');
  showAdminToast(`Permissions updated successfully for ${userName}.`, 'success');
  closeEditRoleModal();
}

function openAddUserModal() {
  const form = document.getElementById('addUserForm');
  if (form) form.reset();
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'none';
}

function handleAddNewUser(e) {
  if (e) e.preventDefault();

  const nameInput = document.getElementById('addUserName');
  const emailInput = document.getElementById('addUserEmail');
  const deptInput = document.getElementById('addUserDept');
  const roleInput = document.getElementById('addUserRole');

  if (!nameInput || !emailInput) return;

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const dept = deptInput ? deptInput.value : 'Administrative Services Unit';
  const role = roleInput ? roleInput.value : 'staff';

  const newId = 'U-' + (100 + Math.floor(Math.random() * 899));
  const parts = name.replace(/(Engr\.|Atty\.|Dr\.|Ms\.|Mr\.)/g, '').trim().split(' ');
  const initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase();

  let roleHtml = '';
  let privHtml = '';
  if (role === 'clear') {
    roleHtml = `<span class="user-role-badge status-role-clearance"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg> Stage 2: Final Clearance Authority</span>`;
    privHtml = `<span class="privilege-summary-tag">Final Approval & Gate Pass Authorization</span>`;
  } else if (role === 'rec') {
    roleHtml = `<span class="user-role-badge status-role-recommend"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Stage 1: Recommending Officer</span>`;
    privHtml = `<span class="privilege-summary-tag">Schedule Verification & Administrative Recommendation</span>`;
  } else {
    roleHtml = `<span class="user-role-badge status-role-staff">Division Personnel / Requestor</span>`;
    privHtml = `<span class="privilege-summary-tag">Submit Reservations & Track Endorsements</span>`;
  }

  const tbody = document.getElementById('userTableBody');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.setAttribute('data-user-id', newId);
    tr.setAttribute('data-user-role', role);
    tr.setAttribute('data-search', `${name} ${email} ${dept} ${role}`.toLowerCase());
    tr.innerHTML = `
      <td>
        <div class="table-applicant-cell">
          <div class="user-avatar-circle" style="background: #246a42; color: #fff;">${initials}</div>
          <div class="applicant-meta-text">
            <strong>${name}</strong>
            <span>${email}</span>
          </div>
        </div>
      </td>
      <td>
        <span class="user-dept-text">${dept}</span>
      </td>
      <td>${roleHtml}</td>
      <td>${privHtml}</td>
      <td>
        <span class="status-badge-chip chip-confirmed">Active</span>
      </td>
      <td style="text-align: right;">
        <button type="button" class="btn-table-action edit-role" onclick="openEditRoleModal('${newId}', '${name.replace(/'/g, "\\'")}', '${email}', '${role}', '${dept.replace(/'/g, "\\'")}')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          Configure Role
        </button>
      </td>
    `;
    tbody.insertBefore(tr, tbody.firstChild);
  }

  addAuditLogEntry(`New institutional account created for <strong>${name}</strong> (${dept}).`, 'green');
  showAdminToast(`Account successfully registered for ${name}.`, 'success');
  closeAddUserModal();
}

