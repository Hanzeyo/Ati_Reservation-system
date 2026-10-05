/**
 * ATI Facility Reservation System - Master Schedule Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initMasterCalendar();
  initFacilityFilters();
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

let currentYear = 2026;
let currentMonth = 9; // 9 = October (0-indexed)
let activeFilter = 'all';

function initMasterCalendar() {
  const monthDisplay = document.getElementById('currentMonthDisplay');
  const daysContainer = document.getElementById('boardDaysGrid');
  const prevBtn = document.getElementById('btnPrevMonth');
  const nextBtn = document.getElementById('btnNextMonth');
  const todayBtn = document.getElementById('btnToday');

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  function renderCalendar() {
    if (monthDisplay) {
      monthDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    }

    if (!daysContainer) return;
    daysContainer.innerHTML = '';

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
      const dayEvents = masterEventsDatabase.filter(ev => {
        const matchesDate = ev.date === dateStr;
        const matchesFilter = activeFilter === 'all' || ev.facilityKey === activeFilter;
        return matchesDate && matchesFilter;
      });

      const isToday = (day === 15 && currentMonth === 9 && currentYear === 2026);
      const isFull = dayEvents.length >= 2;

      const dayBox = document.createElement('div');
      dayBox.className = `board-day-box ${isToday ? 'today' : ''}`;

      let statusBadge = '';
      if (dayEvents.length > 0) {
        statusBadge = isFull 
          ? `<span class="day-status-indicator full">Full</span>`
          : `<span class="day-status-indicator open">${dayEvents.length} Booked</span>`;
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
  }

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
      initMasterCalendar();
    });
  });
}

/* ==========================================================================
   3. Event Details Modal
   ========================================================================== */
function openEventDetails(eventId) {
  const event = masterEventsDatabase.find(ev => ev.id === eventId);
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

// Close Modal Handler
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('js-close-modal') || e.target.classList.contains('modal-overlay')) {
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
