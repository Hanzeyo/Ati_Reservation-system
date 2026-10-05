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
  initDormRoomSelection();
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
  const dormSection = document.getElementById('dormFloorPlansSection');
  const sbbBuildingTitle = document.getElementById('sbbBuildingTitle');
  const sbbRate = document.getElementById('sbbRate');

  cards.forEach(card => {
    const selectBtn = card.querySelector('.btn-select-facility');
    const isDorm = card.dataset.facilityType === 'dormitories';

    function selectCard() {
      cards.forEach(c => {
        c.classList.remove('selected');
        const btn = c.querySelector('.btn-select-facility');
        if (btn) btn.innerHTML = 'Select This Facility';
      });

      card.classList.add('selected');

      bookingState.selectedFacility = {
        id: card.dataset.id,
        name: card.dataset.name,
        rate: card.dataset.rate,
        capacity: card.dataset.capacity,
        type: card.dataset.facilityType,
        roomNumber: bookingState.selectedFacility?.roomNumber || null,
        floor: bookingState.selectedFacility?.floor || null
      };

      if (isDorm) {
        // Reveal Dormitory Floor Plans
        if (dormSection) {
          dormSection.style.display = 'flex';
          if (sbbBuildingTitle) sbbBuildingTitle.textContent = card.dataset.name;
          if (sbbRate) sbbRate.textContent = card.dataset.rate;

          if (selectBtn) {
            const currentRoom = bookingState.selectedFacility?.roomNumber;
            if (currentRoom) {
              selectBtn.innerHTML = `✓ Room ${currentRoom} Selected (Click to change)`;
            } else {
              selectBtn.innerHTML = `✓ Selected &mdash; Choose Room Below &darr;`;
            }
          }

          // Smoothly scroll down so user immediately sees the floor plans
          setTimeout(() => {
            let targetScrollEl = dormSection;
            if (card.dataset.id === 'executive-vip-suite') {
              const f4 = document.getElementById('dormFloor4');
              if (f4) targetScrollEl = f4;
            } else if (card.dataset.id === 'twin-deluxe') {
              const f3 = document.getElementById('dormFloor3');
              if (f3) targetScrollEl = f3;
            } else if (card.dataset.id === 'trainee-quad-quarters') {
              const f2 = document.getElementById('dormFloor2');
              if (f2) targetScrollEl = f2;
            }
            targetScrollEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }, 80);
        }
      } else {
        // Hall / Venue
        if (dormSection) {
          dormSection.style.display = 'none';
        }
        if (selectBtn) {
          selectBtn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Selected Venue
          `;
        }
        bookingState.selectedFacility.roomNumber = null;
        bookingState.selectedFacility.floor = null;
        const feedbackBar = document.getElementById('dormSelectedFeedbackBar');
        if (feedbackBar) feedbackBar.style.display = 'none';
        document.querySelectorAll('.dorm-room-box.selected').forEach(rb => rb.classList.remove('selected'));
      }

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
   2. Filter & Category Selection (Halls vs Dormitories)
   ========================================================================== */
function initFacilityFilters() {
  const categoryCards = document.querySelectorAll('.category-pick-card');
  const exploreButtons = document.querySelectorAll('.btn-explore-category');
  const filterPills = document.querySelectorAll('.filter-pill');
  const facilityCards = document.querySelectorAll('.facility-choice-card');
  const sectionHeading = document.getElementById('categorySectionHeading');
  const activeCategoryName = document.getElementById('activeCategoryName');
  const anchorSection = document.getElementById('facilitiesSectionAnchor');

  function switchCategory(catName, shouldScroll = false) {
    // 1. Update Category Cards Active State (if present)
    categoryCards.forEach(card => {
      const isTarget = card.dataset.category === catName;
      card.classList.toggle('active', isTarget);
      const btn = card.querySelector('.btn-explore-category span');
      if (btn) {
        if (isTarget) {
          btn.textContent = catName === 'halls' ? 'Selected: Halls ✓' : 'Selected: Dormitories ✓';
        } else {
          btn.textContent = card.dataset.category === 'halls' ? 'Explore Halls' : 'Explore Dormitories';
        }
      }
    });

    // 2. Update Filter Pills
    filterPills.forEach(pill => {
      pill.classList.toggle('active', pill.dataset.categoryFilter === catName);
    });

    // 3. Update Heading & Badge Indicator
    if (activeCategoryName) {
      if (catName === 'halls') {
        activeCategoryName.textContent = 'Halls (4 Available)';
      } else if (catName === 'dormitories') {
        activeCategoryName.textContent = 'Dormitories (4 Room Types)';
      } else {
        activeCategoryName.textContent = 'All Facilities (8 Total)';
      }
    }

    if (sectionHeading) {
      if (catName === 'halls') {
        sectionHeading.textContent = 'AVAILABLE HALLS & VENUES (4):';
      } else if (catName === 'dormitories') {
        sectionHeading.textContent = 'AVAILABLE DORMITORY ROOMS & SUITES (4):';
      } else {
        sectionHeading.textContent = 'ALL AVAILABLE FACILITIES & ROOMS (8):';
      }
    }

    // 4. Show/Hide Matching Facility Cards
    let firstVisibleCard = null;
    let currentSelectedVisible = false;

    facilityCards.forEach(card => {
      const type = card.dataset.facilityType;
      const isVisible = (catName === 'all' || type === catName);
      card.style.display = isVisible ? 'flex' : 'none';

      if (isVisible) {
        if (!firstVisibleCard) firstVisibleCard = card;
        if (card.classList.contains('selected')) {
          currentSelectedVisible = true;
        }
      }
    });

    // If current selected card is now hidden, select the first visible card!
    if (!currentSelectedVisible && firstVisibleCard) {
      firstVisibleCard.click();
    }

    // Toggle dormFloorPlansSection visibility based on category
    const dormSection = document.getElementById('dormFloorPlansSection');
    if (dormSection) {
      if (catName === 'halls') {
        dormSection.style.display = 'none';
      } else if (catName === 'dormitories') {
        dormSection.style.display = 'flex';
      }
    }

    // Smooth scroll down to facilities list if requested
    if (shouldScroll && anchorSection) {
      anchorSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  // Bind Category Cards & Explore Buttons (if rendered)
  categoryCards.forEach(card => {
    card.addEventListener('click', () => {
      const cat = card.dataset.category;
      switchCategory(cat, true);
    });
  });

  exploreButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const targetCat = btn.dataset.targetCategory;
      switchCategory(targetCat, true);
    });
  });

  // Bind Filter Pills
  filterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      const cat = pill.dataset.categoryFilter;
      switchCategory(cat, false);
    });
  });

  // Check URL query parameters for pre-selected category (e.g. ?category=dormitories)
  const urlParams = new URLSearchParams(window.location.search);
  const requestedCat = urlParams.get('category');
  if (requestedCat === 'dormitories' || requestedCat === 'halls' || requestedCat === 'all') {
    switchCategory(requestedCat, false);
  } else {
    // Default to halls
    switchCategory('halls', false);
  }
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

    // Requirement: When reserving a dormitory accommodation, user MUST select a room first before proceeding to Step 2
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      const isDorm = bookingState.selectedFacility?.type === 'dormitories' ||
                     ['dormitory-suites', 'executive-vip-suite', 'trainee-quad-quarters', 'twin-deluxe'].includes(bookingState.selectedFacility?.id);
      if (isDorm && !bookingState.selectedFacility?.roomNumber) {
        alert('Please choose an AVAILABLE room unit (marked in green, e.g. Room 402, 302, 201, 101) from the floor plan below before proceeding to Date & Time Selection.');
        const dormSection = document.getElementById('dormFloorPlansSection');
        if (dormSection) {
          dormSection.style.display = 'flex';
          dormSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        return;
      }
    }

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

    // Update Desktop Stepper Buttons
    stepButtons.forEach((btn, index) => {
      const stepIdx = index + 1;
      btn.classList.remove('active');
      if (stepIdx === stepNumber) {
        btn.classList.add('active');
      } else if (stepIdx < stepNumber) {
        btn.classList.add('completed');
      }
    });

    // Update Mobile Compact Progress Bar
    const stepLabels = [
      'Facility Selection',
      'Date & Time Selection',
      'Event & Activity Details',
      'Document Upload',
      'Review & Submit'
    ];

    const mobileBadge = document.getElementById('mobileStepBadge');
    const mobileName = document.getElementById('mobileStepName');
    const mobilePercent = document.getElementById('mobileStepPercent');
    const mobileFill = document.getElementById('mobileProgressFill');
    const mobileDots = document.querySelectorAll('.mobile-dot');

    const progressPercent = Math.round((stepNumber / 5) * 100);

    if (mobileBadge) mobileBadge.textContent = `Step ${stepNumber} of 5`;
    if (mobileName) mobileName.textContent = stepLabels[stepNumber - 1];
    if (mobilePercent) mobilePercent.textContent = `${progressPercent}%`;
    if (mobileFill) mobileFill.style.width = `${progressPercent}%`;

    if (mobileDots) {
      mobileDots.forEach((dot, index) => {
        const dotStep = index + 1;
        dot.classList.remove('active', 'completed');
        if (dotStep === stepNumber) {
          dot.classList.add('active');
        } else if (dotStep < stepNumber) {
          dot.classList.add('completed');
        }
      });
    }

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

  // Desktop step buttons click
  stepButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetStep = parseInt(btn.dataset.step, 10);
      goToStep(targetStep);
    });
  });

  // Mobile step dots click
  const mobileDots = document.querySelectorAll('.mobile-dot');
  if (mobileDots) {
    mobileDots.forEach(dot => {
      dot.addEventListener('click', () => {
        const targetStep = parseInt(dot.dataset.step, 10);
        goToStep(targetStep);
      });
    });
  }

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

  if (summaryVenue) {
    let name = bookingState.selectedFacility.name;
    if (bookingState.selectedFacility.roomNumber) {
      name += ` — Room ${bookingState.selectedFacility.roomNumber} (${bookingState.selectedFacility.floor || ''})`;
    }
    summaryVenue.textContent = name;
  }
  if (summaryCap) summaryCap.textContent = bookingState.selectedFacility.capacity;
  if (summaryRate) {
    const rate = bookingState.selectedFacility.roomRate || bookingState.selectedFacility.rate;
    summaryRate.textContent = rate;
  }
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

/* ==========================================================================
   6. Dormitory Room Interactive Selection (All 4 Floors)
   ========================================================================== */
function initDormRoomSelection() {
  const roomBoxes = document.querySelectorAll('.dorm-room-box');
  const feedbackBar = document.getElementById('dormSelectedFeedbackBar');
  const feedbackTitle = document.getElementById('dormSelectedFeedbackTitle');
  const feedbackSub = document.getElementById('dormSelectedFeedbackSub');
  const proceedBtn = document.getElementById('btnProceedStep');

  roomBoxes.forEach(box => {
    box.addEventListener('click', (e) => {
      e.stopPropagation();
      const roomNum = box.dataset.room;
      const floorName = box.dataset.floor;
      const isAvailable = box.classList.contains('available');

      if (!isAvailable) {
        alert(`Room ${roomNum} (${floorName}) is currently RESERVED for scheduled agricultural training delegates.\n\nPlease select one of the AVAILABLE rooms highlighted in green (e.g. Room 402, 406, 410, 302, 309, 310, 201, 212, 101, 102).`);
        return;
      }

      // Mark this room box as selected
      roomBoxes.forEach(b => b.classList.remove('selected'));
      box.classList.add('selected');

      // Update state
      if (!bookingState.selectedFacility) {
        bookingState.selectedFacility = {};
      }
      bookingState.selectedFacility.roomNumber = roomNum;
      bookingState.selectedFacility.floor = floorName;
      if (box.dataset.rate) {
        bookingState.selectedFacility.roomRate = box.dataset.rate;
      }

      // Display feedback banner
      if (feedbackBar) {
        feedbackBar.style.display = 'flex';
        if (feedbackTitle) {
          feedbackTitle.textContent = `Room ${roomNum} Selected`;
        }
        if (feedbackSub) {
          const rateText = box.dataset.rate ? ` (${box.dataset.rate})` : '';
          feedbackSub.textContent = `${floorName} — Standard Unit${rateText} • Assigned for your booking`;
        }
      }

      // Update selected facility card button
      const selectedCard = document.querySelector('.facility-choice-card.selected');
      if (selectedCard && selectedCard.dataset.facilityType === 'dormitories') {
        const btn = selectedCard.querySelector('.btn-select-facility');
        if (btn) {
          btn.innerHTML = `✓ Room ${roomNum} Selected (Click to change)`;
        }
      }

      // Update Step 1 proceed button text
      if (proceedBtn && bookingState.currentStep === 1) {
        proceedBtn.innerHTML = `<span>Proceed to Date & Time (Room ${roomNum})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      }

      updateReviewSummary();
    });
  });
}


