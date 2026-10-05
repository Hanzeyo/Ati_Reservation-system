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
  date: '2026-10-15',
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

  if (summaryVenue) summaryVenue.textContent = bookingState.selectedFacility.name;
  if (summaryCap) summaryCap.textContent = bookingState.selectedFacility.capacity;
  if (summaryRate) summaryRate.textContent = bookingState.selectedFacility.rate;
}

/* ==========================================================================
   5. Profile Dropdown Toggle
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
   6. Interactive Date & Slot Selection (Step 2)
   ========================================================================== */
function initDateSlotInteractions() {
  const dayButtons = document.querySelectorAll('.slot-day-btn:not(.empty)');
  const startInput = document.getElementById('startDateInput');
  const endInput = document.getElementById('endDateInput');
  const endDateWrap = document.getElementById('endDateWrap');
  const statusTitle = document.getElementById('slotStatusTitle');
  const statusDesc = document.getElementById('slotStatusDesc');
  const btnModeSingle = document.getElementById('btnModeSingle');
  const btnModeRange = document.getElementById('btnModeRange');

  let mode = 'single'; // 'single' or 'range'
  let selectedStart = 14;
  let selectedEnd = 14;
  let rangeWaitingForEnd = false;

  function renderSelection() {
    dayButtons.forEach(btn => {
      const day = parseInt(btn.dataset.day, 10);
      if (btn.classList.contains('reserved') || btn.classList.contains('suspended')) return;

      if (day >= selectedStart && day <= selectedEnd) {
        btn.classList.add('selected');
        btn.classList.remove('available');
      } else {
        btn.classList.remove('selected');
        btn.classList.add('available');
      }
    });

    const formattedStart = `10/${String(selectedStart).padStart(2, '0')}/2026`;
    const formattedEnd = `10/${String(selectedEnd).padStart(2, '0')}/2026`;

    if (startInput) startInput.value = formattedStart;
    if (endInput) endInput.value = formattedEnd;

    const durationDays = (selectedEnd - selectedStart) + 1;
    if (statusTitle) statusTitle.textContent = 'Selected Slot Available!';
    if (statusDesc) {
      if (durationDays === 1) {
        statusDesc.textContent = `No venue conflicts detected for 1 day duration (October ${selectedStart}, 2026).`;
      } else {
        statusDesc.textContent = `No venue conflicts detected for ${durationDays} days duration (Oct ${selectedStart} – Oct ${selectedEnd}, 2026).`;
      }
    }

    bookingState.date = durationDays === 1 ? formattedStart : `${formattedStart} to ${formattedEnd}`;
  }

  // Mode Toggle listeners
  if (btnModeSingle && btnModeRange) {
    btnModeSingle.addEventListener('click', () => {
      mode = 'single';
      rangeWaitingForEnd = false;
      btnModeSingle.classList.add('active');
      btnModeRange.classList.remove('active');
      if (endDateWrap) endDateWrap.classList.remove('active-focus');
      // Collapse to single start date
      selectedEnd = selectedStart;
      renderSelection();
    });

    btnModeRange.addEventListener('click', () => {
      mode = 'range';
      rangeWaitingForEnd = true;
      btnModeRange.classList.add('active');
      btnModeSingle.classList.remove('active');
      if (endDateWrap) endDateWrap.classList.add('active-focus');
      if (statusDesc) {
        statusDesc.textContent = `Start date is Oct ${selectedStart}. Click another date on the calendar to set your End Date.`;
      }
    });
  }

  // If user clicks directly on END DATE input box, switch to range end selection
  if (endInput && endDateWrap) {
    endInput.addEventListener('click', () => {
      mode = 'range';
      rangeWaitingForEnd = true;
      if (btnModeRange) btnModeRange.classList.add('active');
      if (btnModeSingle) btnModeSingle.classList.remove('active');
      endDateWrap.classList.add('active-focus');
      if (statusDesc) {
        statusDesc.textContent = `End Date picker active: Click an end date after October ${selectedStart} on the calendar.`;
      }
    });
  }

  // Handle Calendar Day Clicks
  dayButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const day = parseInt(btn.dataset.day, 10);

      // 1. Guard Suspended
      if (btn.classList.contains('suspended')) {
        alert(`October ${day}, 2026 is SUSPENDED for scheduled facility maintenance / administrative sanitation. Booking is not available on this date.`);
        return;
      }

      // 2. Guard Reserved
      if (btn.classList.contains('reserved')) {
        alert(`October ${day}, 2026 is currently reserved for another official event. Please select an available green slot.`);
        return;
      }

      // 3. Single Day Mode: 
      // ALWAYS select ONLY this single date! 
      // Guarantees zero multiplying / spreading bug when clicking dates!
      if (mode === 'single') {
        selectedStart = day;
        selectedEnd = day;
        renderSelection();
        return;
      }

      // 4. Multi-Day Range Mode:
      if (mode === 'range') {
        if (!rangeWaitingForEnd) {
          // First click in range mode: pick new start date
          selectedStart = day;
          selectedEnd = day;
          rangeWaitingForEnd = true;
          renderSelection();
          if (endDateWrap) endDateWrap.classList.add('active-focus');
          if (statusDesc) {
            statusDesc.textContent = `Start date set to Oct ${day}. Now click an end date on the calendar.`;
          }
        } else {
          // Second click: user is picking the end date
          if (day < selectedStart) {
            // Clicked a date before start -> make it the new start date instead
            selectedStart = day;
            selectedEnd = day;
            rangeWaitingForEnd = true;
            renderSelection();
            if (statusDesc) {
              statusDesc.textContent = `Start date changed to Oct ${day}. Click an end date after Oct ${day}.`;
            }
            return;
          }

          if (day === selectedStart) {
            // Selected same day
            selectedEnd = day;
            rangeWaitingForEnd = false;
            if (endDateWrap) endDateWrap.classList.remove('active-focus');
            renderSelection();
            return;
          }

          // Check if range spans over any reserved or suspended dates!
          let conflict = null;
          for (let d = selectedStart + 1; d <= day; d++) {
            const checkBtn = document.querySelector(`.slot-day-btn[data-day="${d}"]`);
            if (checkBtn) {
              if (checkBtn.classList.contains('reserved')) {
                conflict = `October ${d} is Reserved for another event`;
                break;
              }
              if (checkBtn.classList.contains('suspended')) {
                conflict = `October ${d} is Suspended for facility maintenance`;
                break;
              }
            }
          }

          if (conflict) {
            alert(`Cannot select range: ${conflict}. Please select consecutive available dates without conflicts.`);
            // Reset to just the clicked day to avoid stuck state
            selectedStart = day;
            selectedEnd = day;
            rangeWaitingForEnd = true;
            renderSelection();
            return;
          }

          // Valid range!
          selectedEnd = day;
          rangeWaitingForEnd = false;
          if (endDateWrap) endDateWrap.classList.remove('active-focus');
          renderSelection();
        }
      }
    });
  });

  // Initial render
  renderSelection();
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

