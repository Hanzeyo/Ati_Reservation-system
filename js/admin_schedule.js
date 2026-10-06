/**
 * Agriculture Training Institute - Master Schedule Admin Controller
 * Multi-Facility Operational Calendar with Dual-Stage Reservation Statuses
 */

// Calendar Database with real institutional reservation entries
let calendarEvents = [
  {
    ref: 'R-2026-0888',
    title: 'Q4 National Rice Assessment & Planning Workshop',
    facility: 'serrano',
    facilityName: 'Serrano Function Hall',
    startDate: '2026-10-06',
    endDate: '2026-10-07',
    time: '08:00 AM - 05:00 PM',
    applicant: 'Grace Valenzuela',
    agency: 'DA - National Rice Program',
    email: 'gvalenzuela@da.gov.ph',
    phone: '+63 928 333 7788',
    pax: 100,
    status: 'cleared',
    statusLabel: 'Stage 2 Cleared (Confirmed)',
    colorClass: 'status-cleared',
    stage1Reviewer: 'Recommending Officer (Endorsed)',
    stage2Reviewer: 'Clearance Authority (Cleared)'
  },
  {
    ref: 'R-2026-0893',
    title: 'Administrative Adjudication Hearing',
    facility: 'boardroom',
    facilityName: 'Executive Boardroom',
    startDate: '2026-10-08',
    endDate: '2026-10-08',
    time: '01:00 PM - 05:00 PM',
    applicant: 'Atty. Bernardo Castro',
    agency: 'DA - Legal Service Office',
    email: 'bcastro@da.gov.ph',
    phone: '+63 920 555 1212',
    pax: 18,
    status: 'pending_rec',
    statusLabel: 'Stage 1 Pending Recommendation',
    colorClass: 'status-pending-rec',
    stage1Reviewer: 'Recommending Officer (Under Review)',
    stage2Reviewer: 'Awaiting Stage 1 Recommendation'
  },
  {
    ref: 'MAINT-2026-02',
    title: 'Airconditioning Deep Cleaning & Fumigation',
    facility: 'dormitory',
    facilityName: 'Dormitory Suite B',
    startDate: '2026-10-08',
    endDate: '2026-10-09',
    time: 'Full Day Out of Service',
    applicant: 'General Services Unit',
    agency: 'Administrative Services Unit',
    email: 'gsu@ati.da.gov.ph',
    phone: 'Loc 104',
    pax: 0,
    status: 'blackout',
    statusLabel: 'Maintenance Hold',
    colorClass: 'status-blackout',
    stage1Reviewer: 'Approved Maintenance Hold',
    stage2Reviewer: 'GSU Work Order #44'
  },
  {
    ref: 'R-2026-0891',
    title: 'National TOT on Climate-Resilient Agriculture',
    facility: 'serrano',
    facilityName: 'Serrano Function Hall',
    startDate: '2026-10-12',
    endDate: '2026-10-14',
    time: '08:00 AM - 05:00 PM (3 Days)',
    applicant: 'Engr. Juan Dela Cruz',
    agency: 'ATI - Career Development Division (CDD)',
    email: 'juan.delacruz@ati.da.gov.ph',
    phone: '+63 917 123 4567',
    pax: 85,
    status: 'pending_rec',
    statusLabel: 'Stage 1 Pending Recommendation',
    colorClass: 'status-pending-rec',
    stage1Reviewer: 'Recommending Officer (Queue #1)',
    stage2Reviewer: 'Awaiting Stage 1 Recommendation'
  },
  {
    ref: 'R-2026-0892',
    title: 'Inter-Agency Seed Inspection Workshop',
    facility: 'dormitory',
    facilityName: 'Dormitory Suite A & B',
    startDate: '2026-10-15',
    endDate: '2026-10-18',
    time: 'Check-in: 02:00 PM (4 Days)',
    applicant: 'Dr. Maria Santos',
    agency: 'Bureau of Plant Industry (BPI)',
    email: 'maria.santos@bpi.gov.ph',
    phone: '+63 918 987 6543',
    pax: 32,
    status: 'pending_clear',
    statusLabel: 'Stage 2 Pending Clearance',
    colorClass: 'status-pending-clear',
    stage1Reviewer: 'Recommending Officer (Endorsed)',
    stage2Reviewer: 'Clearance Authority (Under Review)'
  },
  {
    ref: 'R-2026-0894',
    title: 'Digital Agriculture & Rice Crop Manager Training',
    facility: 'four_h',
    facilityName: '4-H Learning Center',
    startDate: '2026-10-20',
    endDate: '2026-10-22',
    time: '08:30 AM - 04:30 PM (3 Days)',
    applicant: 'Ramon Pascual',
    agency: 'PhilRice - Extension Division',
    email: 'rpascual@philrice.gov.ph',
    phone: '+63 922 444 3322',
    pax: 60,
    status: 'pending_clear',
    statusLabel: 'Stage 2 Pending Clearance',
    colorClass: 'status-pending-clear',
    stage1Reviewer: 'Recommending Officer (Endorsed)',
    stage2Reviewer: 'Clearance Authority (Under Review)'
  },
  {
    ref: 'HOLD-2026-01',
    title: 'National Agricultural Extension Summit Setup',
    facility: 'all',
    facilityName: 'All ATI Facilities (Blackout)',
    startDate: '2026-10-26',
    endDate: '2026-10-28',
    time: 'Full Institutional Blackout',
    applicant: 'Directorate Office',
    agency: 'Office of the Director IV',
    email: 'od@ati.da.gov.ph',
    phone: 'Loc 101',
    pax: 350,
    status: 'blackout',
    statusLabel: 'Institutional Blackout Hold',
    colorClass: 'status-blackout',
    stage1Reviewer: 'Memo Circular #2026-18',
    stage2Reviewer: 'Direct Executive Reservation'
  }
];

let currentYear = 2026;
let currentMonth = 9; // 0-indexed: 9 = October
let activeFacilityFilter = 'all';

document.addEventListener('DOMContentLoaded', function () {
  initDateDisplay();
  renderCalendar();

  // Restore desktop sidebar collapsed preference if previously saved
  if (window.innerWidth > 960 && localStorage.getItem('admin_sidebar_collapsed') === 'true') {
    const layout = document.querySelector('.admin-layout-container');
    if (layout) layout.classList.add('sidebar-collapsed');
  }
});

/* ==========================================================================
   DATE DISPLAY & SIDEBAR TOGGLE (RESPONSIVE & DESKTOP)
   ========================================================================== */
function initDateDisplay() {
  const dateEl = document.getElementById('currentDateDisplay');
  if (dateEl) {
    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
    const today = new Date();
    dateEl.textContent = today.toLocaleDateString('en-US', options);
  }
}

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
   CALENDAR ENGINE (MONTH, DAYS & EVENT RENDERING)
   ========================================================================== */
const monthNames = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

function renderCalendar() {
  const monthDisplay = document.getElementById('currentMonthDisplay');
  if (monthDisplay) {
    monthDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
  }

  const grid = document.getElementById('boardDaysGrid');
  if (!grid) return;
  grid.innerHTML = '';

  const firstDay = new Date(currentYear, currentMonth, 1).getDay(); // Day of week (0-6)
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

  const today = new Date();
  const isCurrentMonthActual = (today.getFullYear() === currentYear && today.getMonth() === currentMonth);

  // 1. Previous Month Spillover Days
  for (let i = firstDay - 1; i >= 0; i--) {
    const prevDayNum = daysInPrevMonth - i;
    const cell = document.createElement('div');
    cell.className = 'calendar-day-cell inactive-month';
    cell.innerHTML = `
      <div class="day-cell-top">
        <span class="day-number">${prevDayNum}</span>
      </div>
      <div class="day-events-container"></div>
    `;
    grid.appendChild(cell);
  }

  // 2. Current Month Active Days
  for (let day = 1; day <= daysInMonth; day++) {
    const cell = document.createElement('div');
    const isToday = isCurrentMonthActual && (today.getDate() === day);
    cell.className = `calendar-day-cell ${isToday ? 'today-cell' : ''}`;

    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    // Get matching events
    const matchingEvents = calendarEvents.filter(ev => {
      // Facility Filter
      if (activeFacilityFilter !== 'all' && ev.facility !== 'all' && ev.facility !== activeFacilityFilter) {
        return false;
      }

      // Date range check
      return dateStr >= ev.startDate && dateStr <= ev.endDate;
    });

    let eventsHtml = '';
    matchingEvents.forEach(ev => {
      let venueCode = 'HALL';
      if (ev.facility === 'serrano') venueCode = 'SERRANO';
      else if (ev.facility === 'four_h') venueCode = '4-H';
      else if (ev.facility === 'boardroom') venueCode = 'BOARD';
      else if (ev.facility === 'dormitory') venueCode = 'DORM';
      else if (ev.facility === 'mess_hall') venueCode = 'MESS';
      else if (ev.facility === 'all') venueCode = 'BLACKOUT';

      eventsHtml += `
        <div class="calendar-event-chip ${ev.colorClass}" onclick="openAdminEventDetailsModal('${ev.ref}')" title="${ev.ref}: ${ev.title} (${ev.statusLabel})">
          <span class="chip-venue-tag">${venueCode} &bull; ${ev.time.split(' ')[0]}</span>
          <span class="chip-title-text">${ev.title}</span>
        </div>
      `;
    });

    cell.innerHTML = `
      <div class="day-cell-top">
        <span class="day-number">${day}</span>
        <span class="day-badge-quick-add" onclick="openDateBlockModal('${dateStr}')" title="Hold / Block this Date">+ Hold</span>
      </div>
      <div class="day-events-container">
        ${eventsHtml}
      </div>
    `;
    grid.appendChild(cell);
  }

  // 3. Next Month Spillover Days (Total cells should fill complete 7-day rows)
  const totalCells = firstDay + daysInMonth;
  const remainingCells = (7 - (totalCells % 7)) % 7;
  for (let nextDay = 1; nextDay <= remainingCells; nextDay++) {
    const cell = document.createElement('div');
    cell.className = 'calendar-day-cell inactive-month';
    cell.innerHTML = `
      <div class="day-cell-top">
        <span class="day-number">${nextDay}</span>
      </div>
      <div class="day-events-container"></div>
    `;
    grid.appendChild(cell);
  }
}

/* ==========================================================================
   MONTH & FACILITY FILTER CONTROLS
   ========================================================================== */
function changeMonth(direction) {
  currentMonth += direction;
  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }
  renderCalendar();
}

function goToToday() {
  const today = new Date();
  currentYear = today.getFullYear();
  currentMonth = today.getMonth();
  renderCalendar();
}

function filterCalendarByFacility(facilityKey, btnEl) {
  if (btnEl) {
    document.querySelectorAll('.fac-pill').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
  }
  activeFacilityFilter = facilityKey;
  renderCalendar();
}

/* ==========================================================================
   EVENT DETAILS MODAL (ADMIN REVIEW DIRECT ACCESS / LIFT BLOCK)
   ========================================================================== */
let currentActiveViewedRef = null;

function openAdminEventDetailsModal(refId) {
  const ev = calendarEvents.find(e => e.ref === refId);
  if (!ev) return;

  currentActiveViewedRef = refId;
  const modal = document.getElementById('adminEventDetailsModal');
  if (!modal) return;

  document.getElementById('modalEventRef').textContent = ev.ref;
  document.getElementById('modalEventFacility').textContent = ev.facilityName;
  document.getElementById('modalEventTitle').textContent = ev.title;
  document.getElementById('modalEventDate').textContent = (ev.startDate === ev.endDate) ? ev.startDate : `${ev.startDate} to ${ev.endDate}`;
  document.getElementById('modalEventTime').textContent = ev.time;
  document.getElementById('modalEventAgency').textContent = `${ev.applicant} (${ev.agency})`;
  document.getElementById('modalEventAttendees').textContent = ev.pax > 0 ? `${ev.pax} Participants` : 'Staff / GSU';
  document.getElementById('modalEventStatus').textContent = ev.statusLabel;
  document.getElementById('modalEventReviewers').textContent = `Stage 1: ${ev.stage1Reviewer} | Stage 2: ${ev.stage2Reviewer}`;

  const avatarCircle = document.getElementById('modalEventAvatarCircle');
  const btnDesk = document.getElementById('btnModalOpenDesk');
  const btnLift = document.getElementById('btnModalLiftBlock');

  if (ev.status === 'blackout') {
    if (avatarCircle) {
      avatarCircle.style.background = '#dc2626';
      avatarCircle.textContent = 'HLD';
    }
    if (btnDesk) btnDesk.style.display = 'none';
    if (btnLift) btnLift.style.display = 'inline-flex';
  } else {
    if (avatarCircle) {
      avatarCircle.style.background = '#174d2f';
      const initials = ev.applicant.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
      avatarCircle.textContent = initials || 'EV';
    }
    if (btnDesk) {
      btnDesk.style.display = 'inline-flex';
      btnDesk.href = `admin_dashboard.php#reservationsSection`;
    }
    if (btnLift) btnLift.style.display = 'none';
  }

  modal.style.display = 'flex';
}

function closeAdminEventDetailsModal() {
  const modal = document.getElementById('adminEventDetailsModal');
  if (modal) modal.style.display = 'none';
  currentActiveViewedRef = null;
}

function confirmLiftCurrentBlock() {
  if (!currentActiveViewedRef) return;
  liftHoldByRef(currentActiveViewedRef);
  closeAdminEventDetailsModal();
}

function liftHoldByRef(refId) {
  const targetIndex = calendarEvents.findIndex(e => e.ref === refId);
  if (targetIndex === -1) return;

  const holdTitle = calendarEvents[targetIndex].title;
  calendarEvents.splice(targetIndex, 1);

  renderCalendar();
  updateHoldsKPICounter();

  // If manage holds modal is open, refresh its list
  renderActiveHoldsList();

  showAdminToast(`Administrative block "${holdTitle}" lifted. Dates are now open.`, 'success');
}

/* ==========================================================================
   MANAGE ACTIVE HOLDS MODAL
   ========================================================================== */
function openManageHoldsModal() {
  renderActiveHoldsList();
  const modal = document.getElementById('manageHoldsModal');
  if (modal) modal.style.display = 'flex';
}

function closeManageHoldsModal() {
  const modal = document.getElementById('manageHoldsModal');
  if (modal) modal.style.display = 'none';
}

function renderActiveHoldsList() {
  const container = document.getElementById('activeHoldsListContainer');
  if (!container) return;

  const holds = calendarEvents.filter(e => e.status === 'blackout');

  if (holds.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 2rem 1rem; background: #fbfdfc; border: 1.5px dashed #cfe0d5; border-radius: 10px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" style="margin-bottom: 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <p style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #163824;">No Active Administrative Blocks</p>
        <p style="margin: 0.25rem 0 0; font-size: 0.8rem; color: #557262;">All facility dates on the master calendar are fully available for standard reservations.</p>
      </div>
    `;
    return;
  }

  let html = '';
  holds.forEach(h => {
    html += `
      <div style="background: #fff8f8; border: 1.2px solid #fecaca; border-radius: 10px; padding: 0.85rem 1.1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 240px;">
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
            <span style="background: #dc2626; color: #fff; font-size: 0.68rem; font-weight: 800; padding: 2px 6px; border-radius: 4px;">${h.ref}</span>
            <strong style="color: #991b1b; font-size: 0.92rem;">${h.title}</strong>
          </div>
          <div style="font-size: 0.78rem; color: #5d7567; display: flex; gap: 0.85rem; flex-wrap: wrap;">
            <span><strong>Venue:</strong> ${h.facilityName}</span>
            <span><strong>Dates:</strong> ${h.startDate} &rarr; ${h.endDate}</span>
          </div>
          <div style="font-size: 0.74rem; color: #7f1d1d; margin-top: 0.2rem;">${h.stage1Reviewer}</div>
        </div>
        <div>
          <button type="button" class="btn-confirm-decline" style="font-size: 0.76rem; padding: 0.42rem 0.85rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.35rem;" onclick="liftHoldByRef('${h.ref}')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            <span>Lift Block</span>
          </button>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
}

function updateHoldsKPICounter() {
  const holdsCount = calendarEvents.filter(e => e.status === 'blackout').length;
  const kpiEl = document.getElementById('kpiActiveHoldsCount');
  if (kpiEl) {
    kpiEl.textContent = `${holdsCount} Hold${holdsCount === 1 ? '' : 's'}`;
  }
}

/* ==========================================================================
   DATE BLOCKING / BLACKOUT CONTROLLER
   ========================================================================== */
function openDateBlockModal(defaultDate = '') {
  const form = document.getElementById('dateBlockForm');
  if (form) form.reset();

  if (defaultDate) {
    const startInput = document.getElementById('blockStartDate');
    const endInput = document.getElementById('blockEndDate');
    if (startInput) startInput.value = defaultDate;
    if (endInput) endInput.value = defaultDate;
  }

  const modal = document.getElementById('adminDateBlockModal');
  if (modal) modal.style.display = 'flex';
}

function closeDateBlockModal() {
  const modal = document.getElementById('adminDateBlockModal');
  if (modal) modal.style.display = 'none';
}

function handleSaveDateBlock(e) {
  if (e) e.preventDefault();

  const title = document.getElementById('blockTitle').value.trim();
  const facility = document.getElementById('blockFacilitySelect').value;
  const startDate = document.getElementById('blockStartDate').value;
  const endDate = document.getElementById('blockEndDate').value || startDate;
  const reason = document.getElementById('blockReasonSelect').value;
  const memoRef = document.getElementById('blockMemoRef').value.trim();

  if (!title || !startDate) return;

  const newRef = 'HOLD-' + Math.floor(1000 + Math.random() * 9000);

  let facName = 'All ATI Facilities (Blackout)';
  if (facility === 'serrano') facName = 'Serrano Function Hall';
  else if (facility === 'four_h') facName = '4-H Learning Center';
  else if (facility === 'boardroom') facName = 'Executive Boardroom';
  else if (facility === 'mess_hall') facName = 'ATI Mess Hall & Dining Area';
  else if (facility === 'dormitory') facName = 'Dormitory Suites (All Wings)';

  const newBlockItem = {
    ref: newRef,
    title: title,
    facility: facility,
    facilityName: facName,
    startDate: startDate,
    endDate: endDate,
    time: 'Full Day Administrative Hold',
    applicant: 'Executive Directorate',
    agency: 'Office of the Director / Admin Services',
    email: 'admin@ati.da.gov.ph',
    phone: 'Loc 101',
    pax: 0,
    status: 'blackout',
    statusLabel: `Administrative Hold (${reason})`,
    colorClass: 'status-blackout',
    stage1Reviewer: memoRef ? `Memo: ${memoRef}` : 'Executive Administrative Order',
    stage2Reviewer: 'Official Calendar Blackout'
  };

  calendarEvents.unshift(newBlockItem);
  renderCalendar();
  updateHoldsKPICounter();
  closeDateBlockModal();
  showAdminToast(`Administrative blackout hold applied for ${title}.`, 'decline');
}

/* ==========================================================================
   CSV EXPORT GENERATOR
   ========================================================================== */
function exportMasterScheduleCSV() {
  const rows = [
    ['Reference ID', 'Event Title', 'Venue', 'Start Date', 'End Date', 'Time Window', 'Requesting Agency / Division', 'PAX', 'Operational Status']
  ];

  calendarEvents.forEach(ev => {
    rows.push([
      ev.ref,
      `"${ev.title}"`,
      `"${ev.facilityName}"`,
      ev.startDate,
      ev.endDate,
      `"${ev.time}"`,
      `"${ev.applicant} - ${ev.agency}"`,
      ev.pax,
      `"${ev.statusLabel}"`
    ]);
  });

  const csvContent = 'data:text/csv;charset=utf-8,' + rows.map(e => e.join(',')).join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `ATI_Official_Master_Schedule_${monthNames[currentMonth]}_${currentYear}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  showAdminToast('Official Master Schedule exported successfully as CSV.', 'success');
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

let currentBlockRef = null;

function confirmLiftCurrentBlock() {
  if (!currentBlockRef) return;
  if (confirm('Are you sure you want to lift this blackout hold? This will immediately make these dates available for reservation.')) {
    liftHoldByRef(currentBlockRef);
    closeAdminEventDetailsModal();
  }
}

// Hook into openAdminEventDetailsModal to capture current block ref
const originalOpenModal = openAdminEventDetailsModal;
openAdminEventDetailsModal = function(refId) {
  const actualEv = calendarEvents.find(e => e.ref === refId);
  const liftBtn = document.getElementById('btnModalLiftBlock');
  const deskBtn = document.getElementById('btnModalOpenDesk');
  
  if (actualEv && actualEv.status === 'blackout') {
    currentBlockRef = actualEv.ref;
    if(liftBtn) liftBtn.style.display = 'inline-flex';
    if(deskBtn) deskBtn.style.display = 'none';
  } else {
    currentBlockRef = null;
    if(liftBtn) liftBtn.style.display = 'none';
    if(deskBtn) deskBtn.style.display = 'inline-flex';
  }
  originalOpenModal(refId);
};

