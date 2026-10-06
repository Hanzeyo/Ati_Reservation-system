/**
 * ATI Facility Reservation System - Master Schedule Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initMasterCalendar();
  initFacilityFilters();
  initSystemStatusCards();
  initMonthlyEventsModal();
  initProfileDropdown();
  initMobileDrawer();
});

// Master Schedule Database
const masterEventsDatabase = [
  {
    id: 1,
    title: 'Regional Rice Specialists Briefing',
    facility: 'Function Hall',
    facilityKey: 'function-hall',
    date: '2026-10-02',
    time: '08:00 AM - 05:00 PM',
    division: 'Partnerships and Accreditation Division (PAD)',
    attendees: '180 Participants',
    status: 'Approved & Confirmed'
  },
  {
    id: 2,
    title: 'Urban Agriculture Workshop',
    facility: 'Training Hall A',
    facilityKey: 'training-hall',
    date: '2026-10-06',
    time: '09:00 AM - 04:00 PM',
    division: 'Information Services Division (ISD)',
    attendees: '65 Participants',
    status: 'Approved & Confirmed'
  },
  {
    id: 3,
    title: 'Management Committee Meeting',
    facility: 'Executive Boardroom',
    facilityKey: 'boardroom',
    date: '2026-10-08',
    time: '01:00 PM - 05:00 PM',
    division: 'Office of the Director (OD)',
    attendees: '22 Officials',
    status: 'Approved & Confirmed'
  },
  {
    id: 4,
    title: 'Farmers Summit Delegate Luncheon',
    facility: 'Mess Hall & Dining Area',
    facilityKey: 'mess-hall',
    date: '2026-10-12',
    time: '11:00 AM - 02:00 PM',
    division: 'General Services Unit (GSU)',
    attendees: '120 Guests',
    status: 'Approved & Confirmed'
  },
  {
    id: 5,
    title: 'National Rice Extension Congress',
    facility: 'Function Hall',
    facilityKey: 'function-hall',
    date: '2026-10-14',
    time: '08:00 AM - 05:00 PM',
    division: 'Career Development Division (CDD)',
    attendees: '200 Delegates',
    status: 'Approved & Confirmed'
  },
  {
    id: 6,
    title: 'National Rice Extension Congress (Day 2)',
    facility: 'Function Hall',
    facilityKey: 'function-hall',
    date: '2026-10-15',
    time: '08:00 AM - 05:00 PM',
    division: 'Career Development Division (CDD)',
    attendees: '200 Delegates',
    status: 'Approved & Confirmed'
  },
  {
    id: 7,
    title: 'National Rice Extension Congress (Closing)',
    facility: 'Function Hall',
    facilityKey: 'function-hall',
    date: '2026-10-16',
    time: '08:00 AM - 12:00 PM',
    division: 'Career Development Division (CDD)',
    attendees: '200 Delegates',
    status: 'Approved & Confirmed'
  },
  {
    id: 8,
    title: 'Congress Delegates Lodging (10 Rooms)',
    facility: 'Dormitory Suites',
    facilityKey: 'dormitory',
    date: '2026-10-14',
    time: 'Check-in: 02:00 PM',
    division: 'Administrative Services Unit',
    attendees: '40 Guests',
    status: 'Approved & Confirmed'
  },
  {
    id: 9,
    title: 'Organic Fertilizer Masterclass',
    facility: 'Training Hall A',
    facilityKey: 'training-hall',
    date: '2026-10-19',
    time: '08:30 AM - 04:30 PM',
    division: 'Agricultural Training Council',
    attendees: '70 Participants',
    status: 'Approved & Confirmed'
  },
  {
    id: 10,
    title: 'Inter-Agency Coordination Session',
    facility: 'Executive Boardroom',
    facilityKey: 'boardroom',
    date: '2026-10-21',
    time: '09:00 AM - 12:00 PM',
    division: 'DA Central Office Liaison',
    attendees: '18 Officials',
    status: 'Approved & Confirmed'
  },
  {
    id: 11,
    title: 'Beneficiaries Regional Assembly',
    facility: 'Function Hall',
    facilityKey: 'function-hall',
    date: '2026-10-27',
    time: '08:00 AM - 05:00 PM',
    division: 'Extension Support Services',
    attendees: '190 Delegates',
    status: 'Approved & Confirmed'
  }
];

const FACILITY_DISPLAY_NAMES = {
  'all': 'All Facilities',
  'function-hall': 'Function Hall',
  'training-hall': 'Training Hall A',
  'mess-hall': 'Mess Hall & Dining Area',
  'boardroom': 'Executive Boardroom',
  'dormitory': 'Dormitory Suites'
};

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

let currentYear = 2026;
let currentMonth = 9; // 9 = October (0-indexed)
let activeFilter = 'all';

// Interactive Modes driven by Stat Cards
let isHeatmapActive = false;
let isAvailableFocusActive = false;
let renderCalendarGlobal = null;

/**
 * Retrieve all events combining static database and live user reservations in localStorage
 */
function getAllEvents() {
  const allEvents = [...masterEventsDatabase];
  try {
    const local = JSON.parse(localStorage.getItem('ati_system_reservations') || '[]');
    if (Array.isArray(local) && local.length > 0) {
      local.forEach(l => {
        if (!allEvents.some(e => e.id === l.id || (e.ref && e.ref === l.ref))) {
          allEvents.push(l);
        }
      });
    }
  } catch (err) {
    console.warn('Error reading localStorage reservations:', err);
  }
  return allEvents;
}

/**
 * Calculate actual system status metrics for the active month, year, and filter
 */
function calculateSystemStatus() {
  const allEvents = getAllEvents();
  const prefix = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;
  const totalDaysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

  // Events in current viewed month & active filter
  const monthEvents = allEvents.filter(ev => {
    const inMonth = ev.date && ev.date.startsWith(prefix);
    const inFilter = activeFilter === 'all' || ev.facilityKey === activeFilter;
    return inMonth && inFilter;
  });

  const totalEventsCount = monthEvents.length;

  // Highest demand venue in this month
  const facilityCounts = {};
  allEvents
    .filter(ev => ev.date && ev.date.startsWith(prefix))
    .forEach(ev => {
      const fKey = ev.facilityKey || 'function-hall';
      facilityCounts[fKey] = (facilityCounts[fKey] || 0) + 1;
    });

  let topFacilityKey = 'function-hall';
  let topFacilityCount = 0;
  for (const [key, count] of Object.entries(facilityCounts)) {
    if (count > topFacilityCount) {
      topFacilityCount = count;
      topFacilityKey = key;
    }
  }

  const topFacilityName = FACILITY_DISPLAY_NAMES[topFacilityKey] || 'Function Hall';

  // Occupancy: unique days with at least 1 event
  const uniqueBookedDays = new Set(monthEvents.map(ev => ev.date)).size;
  const occupancyPercent = totalDaysInMonth > 0 ? Math.round((uniqueBookedDays / totalDaysInMonth) * 100) : 0;

  // Available days: days with < 2 events (still having open slots)
  const dayEventCounts = {};
  monthEvents.forEach(ev => {
    dayEventCounts[ev.date] = (dayEventCounts[ev.date] || 0) + 1;
  });

  let fullyBookedDaysCount = 0;
  for (let d = 1; d <= totalDaysInMonth; d++) {
    const dateStr = `${prefix}-${String(d).padStart(2, '0')}`;
    if ((dayEventCounts[dateStr] || 0) >= 2) {
      fullyBookedDaysCount++;
    }
  }
  const daysWithAvailableSlots = Math.max(0, totalDaysInMonth - fullyBookedDaysCount);

  return {
    totalEventsCount,
    topFacilityName,
    topFacilityKey,
    topFacilityCount,
    occupancyPercent,
    uniqueBookedDays,
    totalDaysInMonth,
    daysWithAvailableSlots,
    monthName: MONTH_NAMES[currentMonth],
    monthEvents,
    facilityCounts
  };
}

/**
 * Update the 4 Stat Metric Cards on the page with dynamically computed actual status
 * Simple and clean: value + label inside card
 */
function updateSystemStatusMetrics() {
  const status = calculateSystemStatus();

  // Card 1: Total Events Scheduled
  const elValEvents = document.getElementById('statValueEvents');
  const elLblEvents = document.getElementById('statLabelEvents');
  if (elValEvents) elValEvents.textContent = `${status.totalEventsCount} Event${status.totalEventsCount === 1 ? '' : 's'}`;
  if (elLblEvents) elLblEvents.textContent = 'Scheduled this Month';

  // Card 2: Highest Demand Venue
  const elValDemand = document.getElementById('statValueDemand');
  const elLblDemand = document.getElementById('statLabelDemand');
  if (elValDemand) {
    elValDemand.textContent = status.topFacilityCount > 0 ? status.topFacilityName : 'Function Hall';
  }
  if (elLblDemand) elLblDemand.textContent = 'Highest Demand Venue';

  // Card 3: Overall Monthly Occupancy
  const elValOccupancy = document.getElementById('statValueOccupancy');
  const elLblOccupancy = document.getElementById('statLabelOccupancy');
  if (elValOccupancy) elValOccupancy.textContent = `${status.occupancyPercent}%`;
  if (elLblOccupancy) elLblOccupancy.textContent = 'Overall Monthly Occupancy';

  // Card 4: Days with Available Slots
  const elValAvailable = document.getElementById('statValueAvailableDays');
  const elLblAvailable = document.getElementById('statLabelAvailableDays');
  if (elValAvailable) elValAvailable.textContent = `${status.daysWithAvailableSlots} Day${status.daysWithAvailableSlots === 1 ? '' : 's'}`;
  if (elLblAvailable) elLblAvailable.textContent = 'With Available Slots';
}

/* ==========================================================================
   1. Master Calendar Grid Controller
   ========================================================================== */
function initMasterCalendar() {
  const monthDisplay = document.getElementById('currentMonthDisplay');
  const daysContainer = document.getElementById('boardDaysGrid');
  const prevBtn = document.getElementById('btnPrevMonth');
  const nextBtn = document.getElementById('btnNextMonth');
  const todayBtn = document.getElementById('btnToday');

  function renderCalendar() {
    if (monthDisplay) {
      monthDisplay.textContent = `${MONTH_NAMES[currentMonth]} ${currentYear}`;
    }

    if (!daysContainer) return;
    daysContainer.innerHTML = '';

    const allEvents = getAllEvents();
    const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
    const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonthTotalDays = new Date(currentYear, currentMonth, 0).getDate();

    // Previous month padding days
    for (let i = firstDayIndex - 1; i >= 0; i--) {
      const prevDay = prevMonthTotalDays - i;
      const box = document.createElement('div');
      box.className = 'board-day-box outside-month';
      box.innerHTML = `<div class="day-header-line"><span class="day-num">${prevDay}</span></div>`;
      daysContainer.appendChild(box);
    }

    // Current month days
    for (let day = 1; day <= totalDays; day++) {
      const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const dayEvents = allEvents.filter(ev => {
        const matchesDate = ev.date === dateStr;
        const matchesFilter = activeFilter === 'all' || ev.facilityKey === activeFilter;
        return matchesDate && matchesFilter;
      });

      const isToday = (day === 15 && currentMonth === 9 && currentYear === 2026);
      const isFull = dayEvents.length >= 2;
      const isOpen = dayEvents.length < 2;

      const dayBox = document.createElement('div');
      dayBox.dataset.date = dateStr;

      let extraClasses = '';
      if (isToday) extraClasses += ' today';

      // Interactive Stat Card Modes
      if (isHeatmapActive) {
        if (dayEvents.length === 0) extraClasses += ' heatmap-free';
        else if (dayEvents.length === 1) extraClasses += ' heatmap-moderate';
        else extraClasses += ' heatmap-busy';
      }

      if (isAvailableFocusActive && isOpen) {
        extraClasses += ' available-highlight';
      }

      dayBox.className = `board-day-box${extraClasses}`;

      let statusBadge = '';
      if (dayEvents.length > 0) {
        statusBadge = isFull
          ? `<span class="day-status-indicator full">Full</span>`
          : `<span class="day-status-indicator open">${dayEvents.length} Booked</span>`;
      } else if (isAvailableFocusActive) {
        statusBadge = `<span class="day-status-indicator open" style="background:#dcfce7; color:#166534;">Open</span>`;
      }

      let eventsHtml = '';
      if (dayEvents.length > 0) {
        eventsHtml = `<div class="day-event-chips-list">` +
          dayEvents.map(ev => `
            <div class="event-chip ${ev.facilityKey}" data-id="${ev.id}" title="${ev.facility}: ${ev.title}">
              <span>${ev.title}</span>
            </div>
          `).join('') +
          `</div>`;
      }

      dayBox.innerHTML = `
        <div class="day-header-line">
          <span class="day-num">${day}</span>
          ${statusBadge}
        </div>
        ${eventsHtml}
      `;

      daysContainer.appendChild(dayBox);
    }

    // Attach click listeners to chips
    document.querySelectorAll('.event-chip').forEach(chip => {
      chip.addEventListener('click', (e) => {
        e.stopPropagation();
        const eventId = parseInt(chip.dataset.id, 10);
        openEventDetails(eventId);
      });
    });

    // Update the 4 Stat Metric Cards on every calendar render
    updateSystemStatusMetrics();
  }

  renderCalendarGlobal = renderCalendar;

  if (prevBtn) {
    prevBtn.addEventListener('click', () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      renderCalendar();
      if (activeStatMode && typeof populateStatPanelGlobal === 'function') {
        populateStatPanelGlobal(activeStatMode);
      }
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      renderCalendar();
      if (activeStatMode && typeof populateStatPanelGlobal === 'function') {
        populateStatPanelGlobal(activeStatMode);
      }
    });
  }

  if (todayBtn) {
    todayBtn.addEventListener('click', () => {
      currentYear = 2026;
      currentMonth = 9;
      renderCalendar();
      if (activeStatMode && typeof populateStatPanelGlobal === 'function') {
        populateStatPanelGlobal(activeStatMode);
      }
    });
  }

  renderCalendar();
}

/* ==========================================================================
   2. Filter by Facility
   ========================================================================== */
function initFacilityFilters() {
  const pills = document.querySelectorAll('.fac-pill');

  pills.forEach(pill => {
    pill.addEventListener('click', () => {
      pills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');

      activeFilter = pill.dataset.facility;
      if (typeof renderCalendarGlobal === 'function') {
        renderCalendarGlobal();
      }
    });
  });
}

/* ==========================================================================
   3. Interactive System Status Cards Controller (Details on Bottom)
   ========================================================================== */
let activeStatMode = null; // 'events' | 'demand' | 'occupancy' | 'available' | null
let populateStatPanelGlobal = null;

function initSystemStatusCards() {
  const cardEvents = document.getElementById('statCardEvents');
  const cardDemand = document.getElementById('statCardDemand');
  const cardOccupancy = document.getElementById('statCardOccupancy');
  const cardAvailable = document.getElementById('statCardAvailableDays');
  const panel = document.getElementById('statDetailPanel');
  const closeBtn = document.getElementById('statDetailCloseBtn');
  const actionBtn = document.getElementById('statDetailActionBtn');

  function populatePanel(mode) {
    if (!panel) return;
    const status = calculateSystemStatus();
    const iconWrap = document.getElementById('statDetailIconWrap');
    const titleEl = document.getElementById('statDetailTitle');
    const badgeEl = document.getElementById('statDetailBadge');
    const descEl = document.getElementById('statDetailDesc');
    const chipsEl = document.getElementById('statDetailChips');

    panel.className = `stat-detail-panel theme-${mode === 'events' ? 'green' : mode === 'demand' ? 'blue' : mode === 'occupancy' ? 'amber' : 'purple'}`;

    if (mode === 'events') {
      if (iconWrap) {
        iconWrap.className = 'stat-detail-icon-wrap green';
        iconWrap.innerHTML = `
          <svg viewBox="0 0 24 24" fill="none">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
          </svg>`;
      }
      if (titleEl) titleEl.textContent = `Scheduled Events Summary • ${status.monthName} ${currentYear}`;
      if (badgeEl) {
        badgeEl.className = 'stat-detail-badge green';
        badgeEl.textContent = `${status.totalEventsCount} Total Events`;
      }
      if (descEl) {
        descEl.textContent = `A total of ${status.totalEventsCount} confirmed reservation events are scheduled across ATI facilities in ${status.monthName} ${currentYear}. Click on any event chip or button below to inspect full event agendas.`;
      }
      if (chipsEl) {
        const counts = status.facilityCounts || {};
        const items = [
          { name: 'Function Hall', count: counts['function-hall'] || 0, icon: '🏛️' },
          { name: 'Training Hall', count: counts['training-hall'] || 0, icon: '🏫' },
          { name: 'Mess Hall', count: counts['mess-hall'] || 0, icon: '🍽️' },
          { name: 'Boardroom', count: counts['boardroom'] || 0, icon: '💼' },
          { name: 'Dormitory', count: counts['dormitory'] || 0, icon: '🛏️' }
        ];
        chipsEl.innerHTML = items
          .filter(item => item.count > 0)
          .map(item => `<span class="stat-chip">${item.icon} <strong>${item.name}:</strong> ${item.count} event${item.count === 1 ? '' : 's'}</span>`)
          .join('') || '<span class="stat-chip">✨ All Facilities Open</span>';
      }
      if (actionBtn) {
        actionBtn.style.display = 'inline-flex';
        actionBtn.textContent = '📋 View Full Event List';
        actionBtn.onclick = () => openMonthlyEventsListModal();
      }
    } else if (mode === 'demand') {
      if (iconWrap) {
        iconWrap.className = 'stat-detail-icon-wrap blue';
        iconWrap.innerHTML = `
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>`;
      }
      if (titleEl) titleEl.textContent = `Highest Demand Facility • ${status.topFacilityName}`;
      if (badgeEl) {
        badgeEl.className = 'stat-detail-badge blue';
        badgeEl.textContent = `Top Venue (${status.topFacilityCount} Bookings)`;
      }
      const pct = status.totalEventsCount > 0 ? Math.round((status.topFacilityCount / status.totalEventsCount) * 100) : 0;
      if (descEl) {
        descEl.textContent = `"${status.topFacilityName}" is currently the most requested facility this month, accounting for ${pct}% of all scheduled events. The calendar grid below has been filtered to highlight only this facility.`;
      }
      if (chipsEl) {
        chipsEl.innerHTML = `
          <span class="stat-chip">🔥 <strong>${status.topFacilityCount}</strong> Reservations</span>
          <span class="stat-chip">📊 <strong>${pct}%</strong> of Total Bookings</span>
          <span class="stat-chip">🔍 Filter Applied to Calendar</span>
        `;
      }
      if (actionBtn) {
        actionBtn.style.display = 'inline-flex';
        actionBtn.textContent = '✕ Reset Facility Filter';
        actionBtn.onclick = () => {
          activeFilter = 'all';
          document.querySelectorAll('.fac-pill').forEach(p => {
            p.classList.toggle('active', p.dataset.facility === 'all');
          });
          closeStatDetails();
        };
      }
    } else if (mode === 'occupancy') {
      if (iconWrap) {
        iconWrap.className = 'stat-detail-icon-wrap amber';
        iconWrap.innerHTML = `
          <svg viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>`;
      }
      if (titleEl) titleEl.textContent = `Monthly Facility Occupancy • ${status.occupancyPercent}%`;
      if (badgeEl) {
        badgeEl.className = 'stat-detail-badge amber';
        badgeEl.textContent = status.occupancyPercent >= 75 ? 'Peak Demand' : status.occupancyPercent >= 40 ? 'Optimal Utilization' : 'Light Utilization';
      }
      const freeDays = status.totalDaysInMonth - status.uniqueBookedDays;
      if (descEl) {
        descEl.textContent = `During ${status.monthName} ${currentYear}, facilities are booked across ${status.uniqueBookedDays} of ${status.totalDaysInMonth} calendar days (${status.occupancyPercent}% occupancy rate). Heatmap view is now active on the calendar grid below.`;
      }
      if (chipsEl) {
        chipsEl.innerHTML = `
          <span class="stat-chip">📅 <strong>${status.uniqueBookedDays}</strong> Booked Days</span>
          <span class="stat-chip">✨ <strong>${freeDays}</strong> Free Days</span>
          <span class="stat-chip">🌡️ Heatmap: 🟢 Free • 🟡 Moderate • 🔴 Busy</span>
        `;
      }
      if (actionBtn) {
        actionBtn.style.display = 'inline-flex';
        actionBtn.textContent = '✕ Turn Off Heatmap';
        actionBtn.onclick = () => closeStatDetails();
      }
    } else if (mode === 'available') {
      if (iconWrap) {
        iconWrap.className = 'stat-detail-icon-wrap purple';
        iconWrap.innerHTML = `
          <svg viewBox="0 0 24 24" fill="none">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>`;
      }
      if (titleEl) titleEl.textContent = `Available Days for Reservation • ${status.daysWithAvailableSlots} Days`;
      if (badgeEl) {
        badgeEl.className = 'stat-detail-badge purple';
        badgeEl.textContent = 'Open for Booking';
      }
      if (descEl) {
        descEl.textContent = `There are ${status.daysWithAvailableSlots} days with open slots available for reservation in ${status.monthName} ${currentYear}. Dates with available slots are highlighted in bright green on the calendar below.`;
      }
      if (chipsEl) {
        chipsEl.innerHTML = `
          <span class="stat-chip">🟢 <strong>${status.daysWithAvailableSlots}</strong> Open Days</span>
          <span class="stat-chip">⚡ Quick Booking Ready</span>
          <span class="stat-chip">🎯 Available Slots Highlighted</span>
        `;
      }
      if (actionBtn) {
        actionBtn.style.display = 'inline-flex';
        actionBtn.textContent = '➕ Reserve a Facility';
        actionBtn.onclick = () => {
          window.location.href = 'booking.php';
        };
      }
    }
  }

  function openStatDetails(mode) {
    if (!panel) return;

    // Toggle off immediately if same card clicked
    if (activeStatMode === mode) {
      closeStatDetails();
      return;
    }

    activeStatMode = mode;

    // Instant card highlight updates
    if (cardEvents) cardEvents.classList.toggle('active-mode', mode === 'events');
    if (cardDemand) cardDemand.classList.toggle('active-mode', mode === 'demand');
    if (cardOccupancy) cardOccupancy.classList.toggle('active-mode', mode === 'occupancy');
    if (cardAvailable) cardAvailable.classList.toggle('active-mode', mode === 'available');

    // Instant display and fill with zero delay
    panel.style.display = 'flex';
    populatePanel(mode);

    // Apply calendar state only if needed
    const status = calculateSystemStatus();
    let needCalendarRerender = false;

    if (mode === 'events') {
      if (isHeatmapActive || isAvailableFocusActive || activeFilter !== 'all') {
        isHeatmapActive = false;
        isAvailableFocusActive = false;
        activeFilter = 'all';
        document.querySelectorAll('.fac-pill').forEach(p => p.classList.toggle('active', p.dataset.facility === 'all'));
        needCalendarRerender = true;
      }
    } else if (mode === 'demand') {
      if (activeFilter !== status.topFacilityKey || isHeatmapActive || isAvailableFocusActive) {
        activeFilter = status.topFacilityKey;
        isHeatmapActive = false;
        isAvailableFocusActive = false;
        document.querySelectorAll('.fac-pill').forEach(p => p.classList.toggle('active', p.dataset.facility === status.topFacilityKey));
        needCalendarRerender = true;
      }
    } else if (mode === 'occupancy') {
      if (!isHeatmapActive || isAvailableFocusActive) {
        isHeatmapActive = true;
        isAvailableFocusActive = false;
        needCalendarRerender = true;
      }
    } else if (mode === 'available') {
      if (!isAvailableFocusActive || isHeatmapActive) {
        isAvailableFocusActive = true;
        isHeatmapActive = false;
        needCalendarRerender = true;
      }
    }

    if (needCalendarRerender && typeof renderCalendarGlobal === 'function') {
      renderCalendarGlobal();
    }
  }

  function closeStatDetails() {
    activeStatMode = null;
    [cardEvents, cardDemand, cardOccupancy, cardAvailable].forEach(c => {
      if (c) c.classList.remove('active-mode');
    });

    if (panel) panel.style.display = 'none';

    // Reset visual modes
    let needCalendarRerender = isHeatmapActive || isAvailableFocusActive || activeFilter !== 'all';
    isHeatmapActive = false;
    isAvailableFocusActive = false;

    if (activeFilter !== 'all') {
      activeFilter = 'all';
      document.querySelectorAll('.fac-pill').forEach(p => {
        p.classList.toggle('active', p.dataset.facility === 'all');
      });
    }

    if (needCalendarRerender && typeof renderCalendarGlobal === 'function') {
      renderCalendarGlobal();
    }
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', closeStatDetails);
  }

  // Card click event listeners
  if (cardEvents) {
    cardEvents.addEventListener('click', () => openStatDetails('events'));
    cardEvents.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openStatDetails('events'); }
    });
  }

  if (cardDemand) {
    cardDemand.addEventListener('click', () => openStatDetails('demand'));
    cardDemand.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openStatDetails('demand'); }
    });
  }

  if (cardOccupancy) {
    cardOccupancy.addEventListener('click', () => openStatDetails('occupancy'));
    cardOccupancy.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openStatDetails('occupancy'); }
    });
  }

  if (cardAvailable) {
    cardAvailable.addEventListener('click', () => openStatDetails('available'));
    cardAvailable.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openStatDetails('available'); }
    });
  }

  populateStatPanelGlobal = populatePanel;
}

/* ==========================================================================
   4. Monthly Events Breakdown Modal Controller (Activated by Card 1)
   ========================================================================== */
function initMonthlyEventsModal() {
  const searchInput = document.getElementById('eventsModalSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const query = searchInput.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.monthly-event-row');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? 'flex' : 'none';
      });
    });
  }
}

function openMonthlyEventsListModal() {
  const modal = document.getElementById('monthlyEventsModal');
  const titleEl = document.getElementById('monthlyEventsModalTitle');
  const countEl = document.getElementById('monthlyEventsModalCount');
  const container = document.getElementById('monthlyEventsListContainer');
  const searchInput = document.getElementById('eventsModalSearchInput');

  if (!modal || !container) return;

  const status = calculateSystemStatus();

  if (titleEl) {
    titleEl.textContent = `Scheduled Activities & Events: ${status.monthName} ${currentYear}`;
  }
  if (countEl) {
    const filterTxt = activeFilter !== 'all' ? ` for ${FACILITY_DISPLAY_NAMES[activeFilter]}` : '';
    countEl.textContent = `Showing ${status.totalEventsCount} confirmed event${status.totalEventsCount === 1 ? '' : 's'}${filterTxt}`;
  }
  if (searchInput) {
    searchInput.value = '';
  }

  container.innerHTML = '';

  if (status.monthEvents.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 2.5rem 1rem; color: #526f5e;">
        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📅</div>
        <h4 style="font-size: 1.1rem; color: #175432; margin-bottom: 0.35rem;">No Events Scheduled for this Period</h4>
        <p style="font-size: 0.85rem;">All facilities are open and available for reservation during ${status.monthName} ${currentYear}.</p>
      </div>
    `;
  } else {
    // Sort events chronologically by date
    const sorted = [...status.monthEvents].sort((a, b) => a.date.localeCompare(b.date));

    sorted.forEach(ev => {
      const dateParts = ev.date.split('-');
      const dayNum = parseInt(dateParts[2], 10);
      const row = document.createElement('div');
      row.className = 'monthly-event-row';

      row.innerHTML = `
        <div class="monthly-event-date-badge">
          <div style="font-size: 0.62rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85;">${MONTH_NAMES[currentMonth].slice(0, 3)}</div>
          <div style="font-size: 1.15rem; font-weight: 900; line-height: 1;">${dayNum}</div>
        </div>
        <div class="monthly-event-info">
          <div class="monthly-event-title">${ev.title}</div>
          <div class="monthly-event-meta">
            <span style="background: ${getFacilityColor(ev.facilityKey)}; color: #ffffff; padding: 2px 8px; border-radius: 9999px; font-weight: 700; font-size: 0.72rem;">${ev.facility}</span>
            <span>🕒 ${ev.time}</span>
            <span>🏛️ ${ev.division}</span>
            <span>👥 ${ev.attendees}</span>
          </div>
        </div>
        <button type="button" class="monthly-event-locate-btn" data-date="${ev.date}" data-event-id="${ev.id}">
          Locate on Calendar &rarr;
        </button>
      `;

      const locateBtn = row.querySelector('.monthly-event-locate-btn');
      if (locateBtn) {
        locateBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          modal.classList.remove('active');
          locateEventOnCalendar(ev.date, ev.id);
        });
      }

      container.appendChild(row);
    });
  }

  modal.classList.add('active');
}

/**
 * Locate and highlight an event date on the calendar grid
 */
function locateEventOnCalendar(dateStr, eventId) {
  const targetBox = document.querySelector(`.board-day-box[data-date="${dateStr}"]`);
  if (targetBox) {
    targetBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    targetBox.style.transition = 'all 0.3s ease';
    targetBox.style.boxShadow = '0 0 0 4px #175432, 0 8px 24px rgba(23, 84, 50, 0.25)';
    targetBox.style.transform = 'scale(1.03)';
    setTimeout(() => {
      targetBox.style.boxShadow = '';
      targetBox.style.transform = '';
      if (eventId) {
        openEventDetails(eventId);
      }
    }, 1200);
  }
}

/* ==========================================================================
   5. Event Details Modal
   ========================================================================== */
function openEventDetails(eventId) {
  const allEvents = getAllEvents();
  const event = allEvents.find(ev => ev.id === eventId);
  if (!event) return;

  const modal = document.getElementById('scheduleEventModal');
  if (!modal) return;

  document.getElementById('modalEventTitle').textContent = event.title;
  document.getElementById('modalEventFacility').textContent = event.facility;
  document.getElementById('modalEventDate').textContent = event.date;
  document.getElementById('modalEventTime').textContent = event.time;
  document.getElementById('modalEventDivision').textContent = event.division;
  document.getElementById('modalEventAttendees').textContent = event.attendees;
  document.getElementById('modalEventStatus').textContent = event.status;

  const banner = document.getElementById('modalEventBanner');
  if (banner) {
    banner.style.backgroundColor = getFacilityColor(event.facilityKey);
  }

  modal.classList.add('active');
}

function getFacilityColor(key) {
  switch (key) {
    case 'function-hall': return '#107545';
    case 'training-hall': return '#2563eb';
    case 'mess-hall': return '#d97706';
    case 'boardroom': return '#7c3aed';
    case 'dormitory': return '#0d9488';
    default: return '#175432';
  }
}

// Close Modal Handler with smooth click detection on svg & button
document.addEventListener('click', (e) => {
  if (e.target.closest('.js-close-modal') || e.target.classList.contains('modal-overlay')) {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
  }
});

/* ==========================================================================
   4. User Profile Dropdown
   ========================================================================== */
function initProfileDropdown() {
  const badge = document.getElementById('userProfileBadge');
  const dropdown = document.getElementById('profileDropdown');

  if (!badge || !dropdown) return;

  badge.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    dropdown.classList.remove('show');
  });
}

/* ==========================================================================
   5. Mobile Navigation Drawer Controller
   ========================================================================== */
function initMobileDrawer() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const overlay = document.getElementById('mobileDrawerOverlay');
  const closeBtn = document.getElementById('mobileDrawerClose');

  if (!toggleBtn || !overlay) return;

  function openDrawer() {
    overlay.style.display = 'block';
    requestAnimationFrame(() => {
      overlay.classList.add('show');
    });
    toggleBtn.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    overlay.classList.remove('show');
    toggleBtn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
    setTimeout(() => {
      if (!overlay.classList.contains('show')) {
        overlay.style.display = 'none';
      }
    }, 280);
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    openDrawer();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      closeDrawer();
    });
  }

  // Close when tapping on backdrop outside drawer
  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      closeDrawer();
    }
  });

  // Close on Escape key press
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('show')) {
      closeDrawer();
    }
  });

  // Close automatically if user resizes back to desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 860 && overlay.classList.contains('show')) {
      closeDrawer();
    }
  });
}
