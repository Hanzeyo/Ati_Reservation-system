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
    monthEvents
  };
}

/**
 * Update the 4 Stat Metric Cards on the page with dynamically computed actual status
 */
function updateSystemStatusMetrics() {
  const status = calculateSystemStatus();

  // Card 1: Total Events Scheduled
  const elValEvents = document.getElementById('statValueEvents');
  const elLblEvents = document.getElementById('statLabelEvents');
  const elPillEvents = document.getElementById('statPillEvents');
  if (elValEvents) elValEvents.textContent = `${status.totalEventsCount} Event${status.totalEventsCount === 1 ? '' : 's'}`;
  if (elLblEvents) {
    if (activeFilter !== 'all') {
      elLblEvents.textContent = `${FACILITY_DISPLAY_NAMES[activeFilter] || 'Facility'} in ${status.monthName}`;
    } else {
      elLblEvents.textContent = `Scheduled in ${status.monthName}`;
    }
  }
  if (elPillEvents) {
    elPillEvents.textContent = status.totalEventsCount >= 15 ? '↗ High' : status.totalEventsCount >= 8 ? '↗ Active' : '↘ Light';
  }

  // Card 2: Highest Demand Venue
  const elValDemand = document.getElementById('statValueDemand');
  const elLblDemand = document.getElementById('statLabelDemand');
  const elPillDemand = document.getElementById('statPillDemand');
  if (elValDemand) {
    elValDemand.textContent = status.topFacilityCount > 0 ? status.topFacilityName : 'All Venues Open';
  }
  if (elLblDemand) {
    if (status.topFacilityCount > 0) {
      elLblDemand.textContent = `${status.topFacilityCount} Booking${status.topFacilityCount === 1 ? '' : 's'} in ${status.monthName}`;
    } else {
      elLblDemand.textContent = `No bookings in ${status.monthName}`;
    }
  }
  if (elPillDemand) {
    elPillDemand.textContent = status.topFacilityCount > 0 ? '🔥 Top' : '● Open';
  }

  // Card 3: Overall Monthly Occupancy
  const elValOccupancy = document.getElementById('statValueOccupancy');
  const elLblOccupancy = document.getElementById('statLabelOccupancy');
  const elPillOccupancy = document.getElementById('statPillOccupancy');
  if (elValOccupancy) elValOccupancy.textContent = `${status.occupancyPercent}%`;
  if (elLblOccupancy) {
    elLblOccupancy.textContent = `${status.uniqueBookedDays} of ${status.totalDaysInMonth} Days Occupied`;
  }
  if (elPillOccupancy) {
    elPillOccupancy.textContent = status.occupancyPercent >= 75 ? '🔥 Peak' : status.occupancyPercent >= 40 ? '● Optimal' : '○ Low';
  }

  // Card 4: Days with Available Slots
  const elValAvailable = document.getElementById('statValueAvailableDays');
  const elLblAvailable = document.getElementById('statLabelAvailableDays');
  const elPillAvailable = document.getElementById('statPillAvailable');
  if (elValAvailable) elValAvailable.textContent = `${status.daysWithAvailableSlots} Day${status.daysWithAvailableSlots === 1 ? '' : 's'}`;
  if (elLblAvailable) {
    elLblAvailable.textContent = `With Available Slots in ${status.monthName}`;
  }
  if (elPillAvailable) {
    elPillAvailable.textContent = `${status.daysWithAvailableSlots} Open`;
  }
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
    });
  }

  if (todayBtn) {
    todayBtn.addEventListener('click', () => {
      currentYear = 2026;
      currentMonth = 9;
      renderCalendar();
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
   3. Interactive System Status Cards Controller
   ========================================================================== */
function initSystemStatusCards() {
  const cardEvents = document.getElementById('statCardEvents');
  const cardDemand = document.getElementById('statCardDemand');
  const cardOccupancy = document.getElementById('statCardOccupancy');
  const cardAvailable = document.getElementById('statCardAvailableDays');
  const banner = document.getElementById('scheduleStatusBanner');
  const bannerText = document.getElementById('statusBannerText');
  const bannerIcon = document.getElementById('statusBannerIcon');
  const btnReset = document.getElementById('btnResetStatusMode');

  function showStatusBanner(icon, text) {
    if (!banner) return;
    if (bannerIcon) bannerIcon.textContent = icon;
    if (bannerText) bannerText.textContent = text;
    banner.style.display = 'flex';
  }

  function hideStatusBanner() {
    if (!banner) return;
    banner.style.display = 'none';
  }

  function resetAllModes() {
    isHeatmapActive = false;
    isAvailableFocusActive = false;

    if (cardOccupancy) cardOccupancy.classList.remove('active-mode');
    if (cardAvailable) cardAvailable.classList.remove('active-mode');
    if (cardDemand) cardDemand.classList.remove('active-mode');
    if (cardEvents) cardEvents.classList.remove('active-mode');

    if (activeFilter !== 'all') {
      activeFilter = 'all';
      document.querySelectorAll('.fac-pill').forEach(p => {
        p.classList.toggle('active', p.dataset.facility === 'all');
      });
    }

    hideStatusBanner();
    if (typeof renderCalendarGlobal === 'function') {
      renderCalendarGlobal();
    }
  }

  if (btnReset) {
    btnReset.addEventListener('click', resetAllModes);
  }

  // 1. Card 1 (Events Scheduled): Opens Monthly Events List Modal Breakdown
  if (cardEvents) {
    cardEvents.addEventListener('click', () => {
      openMonthlyEventsListModal();
    });
    cardEvents.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openMonthlyEventsListModal();
      }
    });
  }

  // 2. Card 2 (Highest Demand Venue): Filter directly to that venue
  if (cardDemand) {
    cardDemand.addEventListener('click', () => {
      const status = calculateSystemStatus();
      const targetFacility = status.topFacilityKey;

      if (activeFilter === targetFacility) {
        activeFilter = 'all';
        cardDemand.classList.remove('active-mode');
        hideStatusBanner();
      } else {
        activeFilter = targetFacility;
        isHeatmapActive = false;
        isAvailableFocusActive = false;
        if (cardOccupancy) cardOccupancy.classList.remove('active-mode');
        if (cardAvailable) cardAvailable.classList.remove('active-mode');
        cardDemand.classList.add('active-mode');

        document.querySelectorAll('.fac-pill').forEach(p => {
          p.classList.toggle('active', p.dataset.facility === targetFacility);
        });

        showStatusBanner(
          '🔥',
          `Calendar Filtered to Top Facility: Showing only "${status.topFacilityName}" schedules (${status.topFacilityCount} bookings in ${status.monthName}).`
        );
      }

      if (typeof renderCalendarGlobal === 'function') {
        renderCalendarGlobal();
      }
    });

    cardDemand.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        cardDemand.click();
      }
    });
  }

  // 3. Card 3 (Occupancy): Toggle Heatmap View
  if (cardOccupancy) {
    cardOccupancy.addEventListener('click', () => {
      isHeatmapActive = !isHeatmapActive;
      isAvailableFocusActive = false;
      if (cardAvailable) cardAvailable.classList.remove('active-mode');

      cardOccupancy.classList.toggle('active-mode', isHeatmapActive);

      if (isHeatmapActive) {
        const status = calculateSystemStatus();
        showStatusBanner(
          '🔥',
          `Occupancy Heatmap Active (${status.occupancyPercent}% monthly rate): 🟢 Free Day (0 events) • 🟡 Moderate (1 event) • 🔴 High / Busy (2+ events). Click card again to disable.`
        );
      } else {
        hideStatusBanner();
      }

      if (typeof renderCalendarGlobal === 'function') {
        renderCalendarGlobal();
      }
    });

    cardOccupancy.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        cardOccupancy.click();
      }
    });
  }

  // 4. Card 4 (Available Slots): Highlight all open days with available slots
  if (cardAvailable) {
    cardAvailable.addEventListener('click', () => {
      isAvailableFocusActive = !isAvailableFocusActive;
      isHeatmapActive = false;
      if (cardOccupancy) cardOccupancy.classList.remove('active-mode');

      cardAvailable.classList.toggle('active-mode', isAvailableFocusActive);

      if (isAvailableFocusActive) {
        const status = calculateSystemStatus();
        showStatusBanner(
          '📅',
          `Available Slots Highlighted: Showing ${status.daysWithAvailableSlots} open days with available reservation slots in ${status.monthName}. Click any green slot to reserve.`
        );
      } else {
        hideStatusBanner();
      }

      if (typeof renderCalendarGlobal === 'function') {
        renderCalendarGlobal();
      }
    });

    cardAvailable.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        cardAvailable.click();
      }
    });
  }
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
