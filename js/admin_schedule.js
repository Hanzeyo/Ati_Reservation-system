/**
 * Agriculture Training Institute - Master Schedule Admin Controller
 * Multi-Facility Operational Calendar with Dual-Stage Reservation Statuses
 */

// Calendar Database initialized from database or fallback
let calendarEvents = (typeof initialDbCalendarEvents !== 'undefined' && Array.isArray(initialDbCalendarEvents))
  ? initialDbCalendarEvents
  : [];

const _adminNow = new Date();
let currentYear = _adminNow.getFullYear();
let currentMonth = _adminNow.getMonth(); // 0-indexed
let activeFacilityFilter = 'all';
let activeStatusFilter = 'all';
let isOpenBookingHighlighted = false;

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

/**
 * Checks whether a specific date (YYYY-MM-DD) is available for reservation
 * strictly excluding past days and confirmed/blackout dates.
 */
function isDateAvailableForBooking(dateStr) {
  const today = new Date();
  const todayDateStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

  // Past dates cannot be booked
  if (dateStr < todayDateStr) {
    return false;
  }

  if (activeFacilityFilter !== 'all') {
    // Selected venue: unavailable if confirmed or administrative hold is on this facility
    const isBooked = calendarEvents.some(ev => {
      if (ev.status !== 'cleared' && ev.status !== 'blackout') return false;
      const facStr = String(ev.facility).toLowerCase();
      const activeStr = String(activeFacilityFilter).toLowerCase();
      const match = (facStr === 'all' || facStr === activeStr || (ev.facilityName && ev.facilityName.toLowerCase().includes(activeStr)));
      if (!match) return false;
      return dateStr >= ev.startDate && dateStr <= ev.endDate;
    });
    return !isBooked;
  } else {
    // All facilities view: unavailable if institution-wide blackout is in place
    const hasGlobalHold = calendarEvents.some(ev => {
      return ev.status === 'blackout' && String(ev.facility) === 'all' && dateStr >= ev.startDate && dateStr <= ev.endDate;
    });
    if (hasGlobalHold) return false;
    return true;
  }
}

/**
 * Applies or removes glowing highlights on all calendar day cells
 */
function applyOpenBookingHighlights() {
  const cells = document.querySelectorAll('.calendar-day-cell:not(.inactive-month)');
  cells.forEach(cell => {
    cell.classList.remove('open-date-highlight');
    if (!isOpenBookingHighlighted) return;

    const dateStr = cell.getAttribute('data-date');
    if (dateStr && isDateAvailableForBooking(dateStr)) {
      cell.classList.add('open-date-highlight');
    }
  });
}

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

  // Update KPI counters dynamically for current month
  updateMonthKPIs(daysInMonth);

  const today = new Date();
  const todayDateStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

  // 1. Previous Month Spillover Days
  for (let i = firstDay - 1; i >= 0; i--) {
    const prevDayNum = daysInPrevMonth - i;
    const cell = document.createElement('div');
    cell.className = 'calendar-day-cell inactive-month past-day-cell';
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
    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const isPast = (dateStr < todayDateStr);
    const isToday = (dateStr === todayDateStr);
    const isAvailable = (!isPast && isDateAvailableForBooking(dateStr));

    cell.className = `calendar-day-cell ${isToday ? 'today-cell' : ''} ${isPast ? 'past-day-cell' : ''}`;
    cell.setAttribute('data-date', dateStr);

    // Get matching events
    const matchingEvents = calendarEvents.filter(ev => {
      // Status Filter
      if (activeStatusFilter === 'pending') {
        if (ev.status !== 'pending_rec' && ev.status !== 'pending_clear') return false;
      } else if (activeStatusFilter === 'cleared') {
        if (ev.status !== 'cleared') return false;
      }

      // Facility Filter (supports both ID and key match)
      if (activeFacilityFilter !== 'all' && ev.facility !== 'all') {
        const facStr = String(ev.facility).toLowerCase();
        const activeStr = String(activeFacilityFilter).toLowerCase();
        if (facStr !== activeStr && !(ev.facilityName && ev.facilityName.toLowerCase().includes(activeStr))) {
          return false;
        }
      }

      // Date range check
      return dateStr >= ev.startDate && dateStr <= ev.endDate;
    });

    let eventsHtml = '';
    matchingEvents.forEach(ev => {
      let venueCode = 'VENUE';
      if (ev.status === 'blackout' || ev.facility === 'all') {
        venueCode = 'HOLD';
      } else if (ev.facilityName) {
        const parts = ev.facilityName.trim().split(/\s+/);
        venueCode = parts[0].toUpperCase();
      }

      const timeSnippet = ev.time ? ev.time.split(' ')[0] : 'ALL-DAY';

      eventsHtml += `
        <div class="calendar-event-chip ${ev.colorClass}" onclick="openAdminEventDetailsModal('${ev.ref}')" title="${ev.ref}: ${ev.title} (${ev.statusLabel})">
          <span class="chip-venue-tag">${venueCode} &bull; ${timeSnippet}</span>
          <span class="chip-title-text">${ev.title}</span>
        </div>
      `;
    });

    cell.innerHTML = `
      <div class="day-cell-top">
        <span class="day-number">${day}</span>
        ${isAvailable ? '<span class="open-date-indicator">Open</span>' : ''}
        ${!isPast ? `<span class="day-badge-quick-add" onclick="openDateBlockModal('${dateStr}')" title="Hold / Block this Date">+ Hold</span>` : ''}
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

  // Apply open booking highlights if toggled on
  if (isOpenBookingHighlighted) {
    applyOpenBookingHighlights();
  }
}

function updateMonthKPIs(daysInMonth) {
  const monthStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;
  const monthStart = `${monthStr}-01`;
  const monthEnd = `${monthStr}-${String(daysInMonth).padStart(2, '0')}`;

  const today = new Date();
  const todayDateStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

  // Filter events in current month respecting facility filter
  const monthEvents = calendarEvents.filter(ev => {
    if (activeFacilityFilter !== 'all' && ev.facility !== 'all') {
      const facStr = String(ev.facility).toLowerCase();
      const activeStr = String(activeFacilityFilter).toLowerCase();
      if (facStr !== activeStr && !(ev.facilityName && ev.facilityName.toLowerCase().includes(activeStr))) {
        return false;
      }
    }
    return ev.startDate <= monthEnd && ev.endDate >= monthStart;
  });

  const confirmed = monthEvents.filter(e => e.status === 'cleared').length;
  const pending = monthEvents.filter(e => e.status === 'pending_rec' || e.status === 'pending_clear').length;
  const holds = monthEvents.filter(e => e.status === 'blackout').length;

  // Calculate booked days for occupancy
  const bookedDays = new Set();
  monthEvents.filter(e => e.status === 'cleared' || e.status === 'blackout').forEach(e => {
    const s = e.startDate > monthStart ? e.startDate : monthStart;
    const end = e.endDate < monthEnd ? e.endDate : monthEnd;
    const startParts = s.split('-').map(Number);
    const endParts = end.split('-').map(Number);
    const cur = new Date(startParts[0], startParts[1] - 1, startParts[2]);
    const last = new Date(endParts[0], endParts[1] - 1, endParts[2]);
    while (cur <= last) {
      const curStr = `${cur.getFullYear()}-${String(cur.getMonth() + 1).padStart(2, '0')}-${String(cur.getDate()).padStart(2, '0')}`;
      bookedDays.add(curStr);
      cur.setDate(cur.getDate() + 1);
    }
  });

  const occupancy = daysInMonth > 0 ? Math.round((bookedDays.size / daysInMonth) * 100) : 0;

  // Accurately count available bookable dates (excluding past and booked/held)
  let openDaysCount = 0;
  for (let d = 1; d <= daysInMonth; d++) {
    const dStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    if (isDateAvailableForBooking(dStr)) {
      openDaysCount++;
    }
  }

  const kpiConf = document.getElementById('kpiConfirmedMonth');
  if (kpiConf) kpiConf.textContent = `${confirmed} Event${confirmed === 1 ? '' : 's'}`;

  const kpiPend = document.getElementById('kpiPendingMonth');
  if (kpiPend) kpiPend.textContent = `${pending} Pending`;

  const kpiOcc = document.getElementById('kpiOccupancyRate');
  if (kpiOcc) kpiOcc.textContent = `${occupancy}%`;

  const kpiOpen = document.getElementById('kpiOpenDays');
  if (kpiOpen) kpiOpen.textContent = `${openDaysCount} Day${openDaysCount === 1 ? '' : 's'}`;

  const kpiOpenTrend = document.getElementById('kpiOpenTrend');
  const isPastMonth = (currentYear < today.getFullYear()) || (currentYear === today.getFullYear() && currentMonth < today.getMonth());
  if (kpiOpenTrend) {
    if (isPastMonth) {
      kpiOpenTrend.textContent = 'Past Month';
      kpiOpenTrend.className = 'stat-trend neutral';
    } else if (openDaysCount === 0) {
      kpiOpenTrend.textContent = 'Fully Booked';
      kpiOpenTrend.className = 'stat-trend negative';
    } else {
      kpiOpenTrend.textContent = 'Available';
      kpiOpenTrend.className = 'stat-trend positive';
    }
  }

  const kpiHolds = document.getElementById('kpiActiveHoldsCount');
  if (kpiHolds) kpiHolds.textContent = `${holds} Hold${holds === 1 ? '' : 's'}`;
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

/**
 * Filter calendar chips by dual-stage status
 */
function filterCalendarByStatus(statusKey, cardEl) {
  // If clicking currently active filter, toggle back to 'all'
  if (activeStatusFilter === statusKey && statusKey !== 'all') {
    activeStatusFilter = 'all';
  } else {
    activeStatusFilter = statusKey;
  }

  // Clear open booking highlights
  isOpenBookingHighlighted = false;
  document.querySelectorAll('.calendar-day-cell').forEach(c => c.classList.remove('open-date-highlight'));

  document.querySelectorAll('.admin-kpi-grid .my-res-stat-card').forEach(c => c.classList.remove('active-filter'));

  if (activeStatusFilter === 'all') {
    const allCard = document.querySelector(".admin-kpi-grid .my-res-stat-card[onclick*=\"'all'\"]");
    if (allCard) allCard.classList.add('active-filter');
    showAdminToast('Showing all calendar reservations & holds.', 'info');
  } else {
    if (cardEl) cardEl.classList.add('active-filter');
    if (activeStatusFilter === 'pending') {
      showAdminToast('Filtered calendar: Showing pending reservation holds only.', 'info');
    } else if (activeStatusFilter === 'cleared') {
      showAdminToast('Filtered calendar: Showing confirmed & cleared bookings only.', 'info');
    }
  }

  renderCalendar();
}

/**
 * Toggle open booking dates highlight mode
 */
function highlightOpenBookingDates(cardEl) {
  isOpenBookingHighlighted = !isOpenBookingHighlighted;

  document.querySelectorAll('.admin-kpi-grid .my-res-stat-card').forEach(c => c.classList.remove('active-filter'));

  if (isOpenBookingHighlighted) {
    const card = cardEl || document.getElementById('cardOpenBookingDates');
    if (card) card.classList.add('active-filter');

    activeStatusFilter = 'all';
    renderCalendar();

    const highlightedCells = document.querySelectorAll('.calendar-day-cell.open-date-highlight');
    if (highlightedCells.length > 0) {
      highlightedCells[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      showAdminToast(`Found ${highlightedCells.length} open booking date(s). Highlighted in green on the calendar.`, 'info');
    } else {
      showAdminToast('No open booking dates available for this facility in the selected month.', 'decline');
    }
  } else {
    renderCalendar();
    const allCard = document.querySelector(".admin-kpi-grid .my-res-stat-card[onclick*=\"'all'\"]");
    if (allCard) allCard.classList.add('active-filter');
    showAdminToast('Open booking dates highlight cleared.', 'info');
  }
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

  const formData = new FormData();
  formData.append('action', 'lift_block');
  formData.append('ref', refId);

  fetch('ajax/schedule_actions.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      calendarEvents.splice(targetIndex, 1);
      renderCalendar();
      updateHoldsKPICounter();
      renderActiveHoldsList();
      showAdminToast(`Administrative block "${holdTitle}" lifted. Dates are now open.`, 'success');
    } else {
      showAdminToast(data.message || 'Failed to lift block.', 'decline');
    }
  })
  .catch(err => {
    console.error(err);
    showAdminToast('Network error while lifting block.', 'decline');
  });
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

  const todayObj = new Date();
  const todayStr = `${todayObj.getFullYear()}-${String(todayObj.getMonth() + 1).padStart(2, '0')}-${String(todayObj.getDate()).padStart(2, '0')}`;

  const startInput = document.getElementById('blockStartDate');
  const endInput = document.getElementById('blockEndDate');
  if (startInput) startInput.min = todayStr;
  if (endInput) endInput.min = todayStr;

  if (defaultDate) {
    if (defaultDate < todayStr) defaultDate = todayStr;
    if (startInput) startInput.value = defaultDate;
    if (endInput) endInput.value = defaultDate;
  } else {
    if (startInput) startInput.value = todayStr;
    if (endInput) endInput.value = todayStr;
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

  const formData = new FormData();
  formData.append('action', 'save_block');
  formData.append('title', title);
  formData.append('facility', facility);
  formData.append('start_date', startDate);
  formData.append('end_date', endDate);
  formData.append('reason', reason);
  formData.append('memo_ref', memoRef);

  fetch('ajax/schedule_actions.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success && data.block) {
      calendarEvents.unshift(data.block);
      renderCalendar();
      updateHoldsKPICounter();
      closeDateBlockModal();
      showAdminToast(data.message || `Administrative blackout hold applied for ${title}.`, 'decline');
    } else {
      showAdminToast(data.message || 'Failed to save date block.', 'decline');
    }
  })
  .catch(err => {
    console.error(err);
    showAdminToast('Network error while saving date block.', 'decline');
  });
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

