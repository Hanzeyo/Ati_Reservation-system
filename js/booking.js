/**
 * ATI Facility Reservation System - Booking Portal Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initFacilitySelection();
  initFacilityFilters();
  initStepperNavigation();
  initDateSlotInteractions();
  initProfileDropdown();
  initMobileDrawer();
});

// Global state
const bookingState = {
  currentStep: 1,
  selectedFacility: {
    id: 'function-hall',
    name: 'Function Hall',
    rate: '₱5,000/day',
    capacity: '150 - 200 PAX'
  },
  selectedStartDate: { year: 2026, month: 9, day: 5 },
  selectedEndDate: { year: 2026, month: 9, day: 5 },
  selectedStartDay: 5,
  selectedEndDay: 5,
  date: '10/05/2026',
  timeSlot: 'Whole Day (8:00 AM - 5:00 PM)',
  eventTitle: '',
  participants: '',
  eventType: 'Official Training'
};

/* ==========================================================================
   1. Facility Selection
   ========================================================================== */
function initFacilitySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    const selectBtn = card.querySelector('.btn-select-facility');

    function selectCard() {
      cards.forEach(c => {
        c.classList.remove('selected');
        const btn = c.querySelector('.btn-select-facility');
        if (btn) btn.innerHTML = 'Select This Facility';
      });

      card.classList.add('selected');
      if (selectBtn) {
        selectBtn.innerHTML = `
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          Selected Venue
        `;
      }

      bookingState.selectedFacility = {
        id: card.dataset.id,
        name: card.dataset.name,
        rate: card.dataset.rate,
        capacity: card.dataset.capacity
      };

      updateReviewSummary();
    }

    if (selectBtn) {
      selectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        selectCard();
      });
    }

    card.addEventListener('click', selectCard);
  });
}

/* ==========================================================================
   2. Filter Available Facilities
   ========================================================================== */
function initFacilityFilters() {
  const filterPills = document.querySelectorAll('.filter-pill');
  const cards = document.querySelectorAll('.facility-choice-card');

  filterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      filterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');

      const filterValue = pill.dataset.filter;

      cards.forEach(card => {
        const category = card.dataset.category;
        if (filterValue === 'all' || category === filterValue) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

/* ==========================================================================
   3. Stepper Wizard Flow
   ========================================================================== */
function initStepperNavigation() {
  const stepButtons = document.querySelectorAll('.step-tab-btn');
  const stepViews = document.querySelectorAll('.wizard-step-view');
  const proceedBtn = document.getElementById('btnProceedStep');

  function goToStep(stepNumber) {
    if (stepNumber < 1 || stepNumber > 5) return;

    // Restriction: Cannot advance past Step 2 if a past date is selected
    if (stepNumber > 2 && bookingState.currentStep === 2) {
      if (bookingState.selectedStartDate) {
        const s = bookingState.selectedStartDate;
        if (s.year < 2026 || (s.year === 2026 && s.month < 9) || (s.year === 2026 && s.month === 9 && s.day < 5)) {
          alert('Cannot proceed with a date that has already passed. Please select today or an upcoming available date.');
          return;
        }
      }
    }

    // When advancing to Step 2 (Date & Time Selection), automatically select current date (October 5, 2026)
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      if (typeof window.selectCurrentDate === 'function') {
        window.selectCurrentDate();
      }
    }

    bookingState.currentStep = stepNumber;

    stepButtons.forEach((btn, index) => {
      const stepIdx = index + 1;
      btn.classList.remove('active');
      if (stepIdx === stepNumber) {
        btn.classList.add('active');
      } else if (stepIdx < stepNumber) {
        btn.classList.add('completed');
      }
    });

    stepViews.forEach(view => {
      view.classList.toggle('active', parseInt(view.dataset.step, 10) === stepNumber);
    });

    // Update Proceed Button and Back Button based on step
    const backBtn = document.getElementById('btnStepBack');
    if (backBtn) {
      backBtn.style.display = stepNumber > 1 ? 'inline-flex' : 'none';
    }

    if (proceedBtn) {
      if (stepNumber === 1) {
        proceedBtn.innerHTML = `<span>Proceed to Date & Time Selection</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        proceedBtn.style.display = 'inline-flex';
      } else if (stepNumber === 2) {
        proceedBtn.innerHTML = `<span>Proceed to Event Details</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        proceedBtn.style.display = 'inline-flex';
      } else if (stepNumber === 3) {
        proceedBtn.innerHTML = `<span>Proceed to Document Upload</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        proceedBtn.style.display = 'inline-flex';
      } else if (stepNumber === 4) {
        proceedBtn.innerHTML = `<span>Review & Confirm Details</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        proceedBtn.style.display = 'inline-flex';
      } else if (stepNumber === 5) {
        proceedBtn.style.display = 'none'; // Step 5 has its own submit button
      }
    }

    window.scrollTo({ top: 120, behavior: 'smooth' });
    updateReviewSummary();
  }

  stepButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetStep = parseInt(btn.dataset.step, 10);
      goToStep(targetStep);
    });
  });

  if (proceedBtn) {
    proceedBtn.addEventListener('click', () => {
      goToStep(bookingState.currentStep + 1);
    });
  }

  const backBtn = document.getElementById('btnStepBack');
  if (backBtn) {
    backBtn.addEventListener('click', () => {
      goToStep(bookingState.currentStep - 1);
    });
  }

  // Final Submit
  const finalSubmitBtn = document.getElementById('btnSubmitFinalReservation');
  if (finalSubmitBtn) {
    finalSubmitBtn.addEventListener('click', () => {
      alert(`Reservation Request Submitted Successfully!\n\nReference: ATI-RES-2026-${Math.floor(1000 + Math.random() * 9000)}\nFacility: ${bookingState.selectedFacility.name}\nDate: ${bookingState.date}\nStatus: Pending Administrative Review\n\nNotification has been sent to your registered email.`);
      window.location.href = 'booking.php';
    });
  }
}

/* ==========================================================================
   4. Update Summary on Step 5
   ========================================================================== */
function updateReviewSummary() {
  const summaryVenue = document.getElementById('summaryVenueName');
  const summaryCap = document.getElementById('summaryVenueCapacity');
  const summaryRate = document.getElementById('summaryVenueRate');
  const summaryDate = document.getElementById('summaryReservationDate');

  if (summaryVenue) summaryVenue.textContent = bookingState.selectedFacility.name;
  if (summaryCap) summaryCap.textContent = bookingState.selectedFacility.capacity;
  if (summaryRate) summaryRate.textContent = bookingState.selectedFacility.rate;
  if (summaryDate) summaryDate.textContent = bookingState.date;
}

/* ==========================================================================
   5. Interactive Date & Slot Selection (Step 2)
   ========================================================================== */
function initDateSlotInteractions() {
  const startInput = document.getElementById('startDateInput');
  const endInput = document.getElementById('endDateInput');
  const endDateWrap = document.getElementById('endDateWrap');
  const statusTitle = document.getElementById('slotStatusTitle');
  const statusDesc = document.getElementById('slotStatusDesc');
  const btnModeSingle = document.getElementById('btnModeSingle');
  const btnModeRange = document.getElementById('btnModeRange');
  const btnPrevMonth = document.getElementById('btnPrevMonth');
  const btnNextMonth = document.getElementById('btnNextMonth');
  const monthDisplay = document.getElementById('slotMonthDisplay');
  const grid = document.getElementById('slotDaysGrid');

  const TODAY = { year: 2026, month: 9, day: 5 }; // October 5, 2026 (0-indexed month)
  let viewYear = 2026;
  let viewMonth = 9; // October

  let selectedStart = { year: 2026, month: 9, day: 5 };
  let selectedEnd = { year: 2026, month: 9, day: 5 };
  let mode = 'single'; // 'single' or 'range'
  let rangeWaitingForEnd = false;

  const MONTH_NAMES = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  // Reserved & maintenance dates database for realistic simulation
  const RESERVED_DATES = new Set([
    '2026-10-06', '2026-10-12', '2026-10-19', '2026-10-27',
    '2026-11-04', '2026-11-10', '2026-11-17', '2026-11-25',
    '2026-12-08', '2026-12-15', '2026-12-22', '2026-12-29'
  ]);
  const SUSPENDED_DATES = new Set([
    '2026-10-29', '2026-11-13', '2026-12-24', '2026-12-25'
  ]);

  function dateToKey(y, m, d) {
    return `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
  }

  function compareDates(d1, d2) {
    if (d1.year !== d2.year) return d1.year - d2.year;
    if (d1.month !== d2.month) return d1.month - d2.month;
    return d1.day - d2.day;
  }

  function isPastDate(y, m, d) {
    return compareDates({ year: y, month: m, day: d }, TODAY) < 0;
  }

  function isTodayDate(y, m, d) {
    return compareDates({ year: y, month: m, day: d }, TODAY) === 0;
  }

  function isDateInRange(target, start, end) {
    return compareDates(target, start) >= 0 && compareDates(target, end) <= 0;
  }

  function formatInputDate(d) {
    return `${String(d.month + 1).padStart(2, '0')}/${String(d.day).padStart(2, '0')}/${d.year}`;
  }

  function formatDisplayDate(d) {
    return `${MONTH_NAMES[d.month]} ${d.day}, ${d.year}`;
  }

  function updateMonthHeader() {
    if (monthDisplay) {
      monthDisplay.textContent = `${MONTH_NAMES[viewMonth]} ${viewYear}`;
    }

    if (btnPrevMonth) {
      // Disable previous month button if we are on or before the current month (October 2026)
      const isCurrentMonthOrEarlier = (viewYear < TODAY.year) || (viewYear === TODAY.year && viewMonth <= TODAY.month);
      btnPrevMonth.disabled = isCurrentMonthOrEarlier;
    }
  }

  function renderSelectionDetails() {
    const formattedStart = formatInputDate(selectedStart);
    const formattedEnd = formatInputDate(selectedEnd);

    if (startInput) startInput.value = formattedStart;
    if (endInput) endInput.value = formattedEnd;

    const startDateObj = new Date(selectedStart.year, selectedStart.month, selectedStart.day);
    const endDateObj = new Date(selectedEnd.year, selectedEnd.month, selectedEnd.day);
    const diffTime = Math.abs(endDateObj - startDateObj);
    const durationDays = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;

    bookingState.selectedStartDate = selectedStart;
    bookingState.selectedEndDate = selectedEnd;
    bookingState.selectedStartDay = selectedStart.day;
    bookingState.selectedEndDay = selectedEnd.day;

    if (statusTitle) statusTitle.textContent = 'Selected Slot Available!';
    if (statusDesc) {
      if (durationDays === 1) {
        statusDesc.textContent = `No venue conflicts detected for 1 day duration (${formatDisplayDate(selectedStart)}).`;
      } else {
        statusDesc.textContent = `No venue conflicts detected for ${durationDays} days duration (${formatDisplayDate(selectedStart)} – ${formatDisplayDate(selectedEnd)}).`;
      }
    }

    bookingState.date = durationDays === 1 ? formattedStart : `${formattedStart} to ${formattedEnd}`;
  }

  function updateGridHighlights() {
    const buttons = document.querySelectorAll('.slot-day-btn:not(.empty)');
    buttons.forEach(b => {
      const y = parseInt(b.dataset.year, 10);
      const m = parseInt(b.dataset.month, 10);
      const d = parseInt(b.dataset.day, 10);
      if (b.classList.contains('reserved') || b.classList.contains('suspended') || b.classList.contains('past-date')) return;

      const inRange = isDateInRange({ year: y, month: m, day: d }, selectedStart, selectedEnd);
      if (inRange) {
        b.classList.add('selected');
        b.classList.remove('available');
      } else {
        b.classList.remove('selected');
        b.classList.add('available');
      }
    });
  }

  function handleDayClick(dateObj, btn) {
    const key = dateToKey(dateObj.year, dateObj.month, dateObj.day);

    if (btn.classList.contains('past-date') || isPastDate(dateObj.year, dateObj.month, dateObj.day)) {
      return;
    }

    if (btn.classList.contains('suspended') || SUSPENDED_DATES.has(key)) {
      alert(`${formatDisplayDate(dateObj)} is currently suspended for scheduled facility maintenance.`);
      return;
    }

    if (btn.classList.contains('reserved') || RESERVED_DATES.has(key)) {
      alert(`${formatDisplayDate(dateObj)} is already reserved for another official event. Please select an available green slot.`);
      return;
    }

    // Single Day Mode
    if (mode === 'single') {
      selectedStart = { ...dateObj };
      selectedEnd = { ...dateObj };
      renderSelectionDetails();
      updateGridHighlights();
      return;
    }

    // Multi-Day Range Mode
    if (mode === 'range') {
      if (!rangeWaitingForEnd) {
        selectedStart = { ...dateObj };
        selectedEnd = { ...dateObj };
        rangeWaitingForEnd = true;
        if (endDateWrap) endDateWrap.classList.add('active-focus');
        if (statusDesc) {
          statusDesc.textContent = `Start date set to ${formatDisplayDate(dateObj)}. Now click an end date on the calendar.`;
        }
        renderSelectionDetails();
        updateGridHighlights();
      } else {
        if (compareDates(dateObj, selectedStart) < 0) {
          selectedStart = { ...dateObj };
          selectedEnd = { ...dateObj };
          rangeWaitingForEnd = true;
          if (statusDesc) {
            statusDesc.textContent = `Start date changed to ${formatDisplayDate(dateObj)}. Click an end date on or after this date.`;
          }
          renderSelectionDetails();
          updateGridHighlights();
          return;
        }

        if (compareDates(dateObj, selectedStart) === 0) {
          selectedEnd = { ...dateObj };
          rangeWaitingForEnd = false;
          if (endDateWrap) endDateWrap.classList.remove('active-focus');
          renderSelectionDetails();
          updateGridHighlights();
          return;
        }

        // Validate range conflicts
        let conflict = null;
        let cur = new Date(selectedStart.year, selectedStart.month, selectedStart.day);
        const endD = new Date(dateObj.year, dateObj.month, dateObj.day);

        while (cur <= endD) {
          const cY = cur.getFullYear();
          const cM = cur.getMonth();
          const cD = cur.getDate();
          const cKey = dateToKey(cY, cM, cD);
          if (isPastDate(cY, cM, cD)) {
            conflict = `${formatDisplayDate({year: cY, month: cM, day: cD})} has already passed`;
            break;
          }
          if (RESERVED_DATES.has(cKey)) {
            conflict = `${formatDisplayDate({year: cY, month: cM, day: cD})} is reserved for another event`;
            break;
          }
          if (SUSPENDED_DATES.has(cKey)) {
            conflict = `${formatDisplayDate({year: cY, month: cM, day: cD})} is suspended for facility maintenance`;
            break;
          }
          cur.setDate(cur.getDate() + 1);
        }

        if (conflict) {
          alert(`Cannot select date range: ${conflict}. Please select consecutive available dates.`);
          selectedStart = { ...dateObj };
          selectedEnd = { ...dateObj };
          rangeWaitingForEnd = true;
          renderSelectionDetails();
          updateGridHighlights();
          return;
        }

        selectedEnd = { ...dateObj };
        rangeWaitingForEnd = false;
        if (endDateWrap) endDateWrap.classList.remove('active-focus');
        renderSelectionDetails();
        updateGridHighlights();
      }
    }
  }

  function renderCalendarGrid() {
    if (!grid) return;
    grid.innerHTML = '';

    const firstDayOfWeek = new Date(viewYear, viewMonth, 1).getDay();
    const totalDaysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

    // Leading spacer slots
    for (let i = 0; i < firstDayOfWeek; i++) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'slot-day-btn empty';
      grid.appendChild(emptyDiv);
    }

    // Month day buttons
    for (let day = 1; day <= totalDaysInMonth; day++) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.dataset.year = viewYear;
      btn.dataset.month = viewMonth;
      btn.dataset.day = day;
      btn.textContent = day;

      const dateObj = { year: viewYear, month: viewMonth, day: day };
      const key = dateToKey(viewYear, viewMonth, day);
      const isPast = isPastDate(viewYear, viewMonth, day);
      const isToday = isTodayDate(viewYear, viewMonth, day);
      const isSuspended = SUSPENDED_DATES.has(key);
      const isReserved = RESERVED_DATES.has(key);
      const isSelected = isDateInRange(dateObj, selectedStart, selectedEnd);

      if (isPast) {
        btn.className = 'slot-day-btn past-date';
        btn.disabled = true;
        btn.title = 'Unavailable';
      } else if (isSuspended) {
        btn.className = 'slot-day-btn suspended';
        btn.title = 'Suspended: Scheduled Facility Maintenance';
      } else if (isReserved) {
        btn.className = 'slot-day-btn reserved';
        btn.title = 'Reserved for Official Event';
      } else {
        if (isSelected) {
          btn.className = 'slot-day-btn selected';
        } else {
          btn.className = 'slot-day-btn available';
        }
      }

      if (isToday) {
        btn.classList.add('today');
        if (!btn.title) btn.title = `Today: ${formatDisplayDate(dateObj)}`;
      }

      btn.addEventListener('click', () => handleDayClick(dateObj, btn));

      grid.appendChild(btn);
    }
  }

  // Month navigation button handlers
  if (btnNextMonth) {
    btnNextMonth.addEventListener('click', () => {
      viewMonth++;
      if (viewMonth > 11) {
        viewMonth = 0;
        viewYear++;
      }
      updateMonthHeader();
      renderCalendarGrid();
    });
  }

  if (btnPrevMonth) {
    btnPrevMonth.addEventListener('click', () => {
      const isCurrentMonthOrEarlier = (viewYear < TODAY.year) || (viewYear === TODAY.year && viewMonth <= TODAY.month);
      if (isCurrentMonthOrEarlier) return;
      viewMonth--;
      if (viewMonth < 0) {
        viewMonth = 11;
        viewYear--;
      }
      updateMonthHeader();
      renderCalendarGrid();
    });
  }

  // Explicit helper to select current date (October 5, 2026)
  window.selectCurrentDate = function() {
    viewYear = 2026;
    viewMonth = 9;
    selectedStart = { year: 2026, month: 9, day: 5 };
    selectedEnd = { year: 2026, month: 9, day: 5 };
    mode = 'single';
    rangeWaitingForEnd = false;
    if (btnModeSingle) btnModeSingle.classList.add('active');
    if (btnModeRange) btnModeRange.classList.remove('active');
    if (endDateWrap) endDateWrap.classList.remove('active-focus');
    updateMonthHeader();
    renderCalendarGrid();
    renderSelectionDetails();
  };

  // Mode Toggle listeners
  if (btnModeSingle && btnModeRange) {
    btnModeSingle.addEventListener('click', () => {
      mode = 'single';
      rangeWaitingForEnd = false;
      btnModeSingle.classList.add('active');
      btnModeRange.classList.remove('active');
      if (endDateWrap) endDateWrap.classList.remove('active-focus');
      selectedEnd = { ...selectedStart };
      renderSelectionDetails();
      updateGridHighlights();
    });

    btnModeRange.addEventListener('click', () => {
      mode = 'range';
      rangeWaitingForEnd = true;
      btnModeRange.classList.add('active');
      btnModeSingle.classList.remove('active');
      if (endDateWrap) endDateWrap.classList.add('active-focus');
      if (statusDesc) {
        statusDesc.textContent = `Start date is ${formatDisplayDate(selectedStart)}. Click another date on the calendar to set your End Date.`;
      }
    });
  }

  // Click on END DATE input box opens range mode
  if (endInput && endDateWrap) {
    endInput.addEventListener('click', () => {
      mode = 'range';
      rangeWaitingForEnd = true;
      if (btnModeRange) btnModeRange.classList.add('active');
      if (btnModeSingle) btnModeSingle.classList.remove('active');
      endDateWrap.classList.add('active-focus');
      if (statusDesc) {
        statusDesc.textContent = `End Date picker active: Click an end date on or after ${formatDisplayDate(selectedStart)} on the calendar.`;
      }
    });
  }

  // Input change validation
  if (startInput) {
    startInput.addEventListener('change', () => {
      const parts = startInput.value.split('/');
      if (parts.length === 3) {
        const m = parseInt(parts[0], 10) - 1;
        const d = parseInt(parts[1], 10);
        const y = parseInt(parts[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
          if (isPastDate(y, m, d)) {
            alert('Cannot enter a date in the past. Reverting to today.');
            window.selectCurrentDate();
          } else {
            selectedStart = { year: y, month: m, day: d };
            if (compareDates(selectedEnd, selectedStart) < 0) {
              selectedEnd = { ...selectedStart };
            }
            viewYear = y;
            viewMonth = m;
            updateMonthHeader();
            renderCalendarGrid();
            renderSelectionDetails();
          }
        }
      }
    });
  }

  if (endInput) {
    endInput.addEventListener('change', () => {
      const parts = endInput.value.split('/');
      if (parts.length === 3) {
        const m = parseInt(parts[0], 10) - 1;
        const d = parseInt(parts[1], 10);
        const y = parseInt(parts[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
          const newEnd = { year: y, month: m, day: d };
          if (compareDates(newEnd, selectedStart) < 0) {
            alert('End date must be on or after the start date.');
            selectedEnd = { ...selectedStart };
            renderSelectionDetails();
            updateGridHighlights();
          } else {
            selectedEnd = newEnd;
            renderSelectionDetails();
            updateGridHighlights();
          }
        }
      }
    });
  }

  // Initial Calendar Setup
  updateMonthHeader();
  renderCalendarGrid();
  renderSelectionDetails();
}

/* ==========================================================================
   5. Profile Dropdown & Mobile Drawer Handlers
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

