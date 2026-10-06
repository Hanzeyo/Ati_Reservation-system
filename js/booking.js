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

// Global navigation handle
let goToStep = null;

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
   1. Facility Selection & Dormitory Room Floor Data (Modal Popup)
   ========================================================================== */
const dormFloorData = {
  'dorm-floor-1': {
    title: '1st Floor: Sampaguita Dormitory',
    floor: '1st Floor (Sampaguita)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '101', available: true },
      { num: '102', available: true },
      { num: '103', available: false },
      { num: '104', available: true },
      { num: '105', available: true },
      { num: '106', available: true },
      { num: '107', available: false },
      { num: '108', available: true },
      { num: '109', available: true },
      { num: '110', available: true },
      { num: '111', available: false },
      { num: '112', available: true }
    ]
  },
  'dorm-floor-2': {
    title: '2nd Floor: Ilang-Ilang Dormitory',
    floor: '2nd Floor (Ilang-Ilang)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '201', available: true },
      { num: '202', available: false },
      { num: '203', available: false },
      { num: '204', available: false },
      { num: '205', available: false },
      { num: '206', available: true },
      { num: '207', available: false },
      { num: '208', available: false },
      { num: '209', available: false },
      { num: '210', available: false },
      { num: '211', available: false },
      { num: '212', available: true }
    ]
  },
  'dorm-floor-3': {
    title: '3rd Floor: Gumamela Dormitory',
    floor: '3rd Floor (Gumamela)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '301', available: false },
      { num: '302', available: true },
      { num: '303', available: false },
      { num: '304', available: false },
      { num: '305', available: true },
      { num: '306', available: false },
      { num: '307', available: false },
      { num: '308', available: false },
      { num: '309', available: true },
      { num: '310', available: true },
      { num: '311', available: false },
      { num: '312', available: false }
    ]
  },
  'dorm-floor-4': {
    title: '4th Floor: Rosal Dormitory',
    floor: '4th Floor (Rosal)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '401', available: false },
      { num: '402', available: false },
      { num: '403', available: true },
      { num: '404', available: false },
      { num: '405', available: false },
      { num: '406', available: false },
      { num: '407', available: true },
      { num: '408', available: false },
      { num: '409', available: false },
      { num: '410', available: false },
      { num: '411', available: true },
      { num: '412', available: false }
    ]
  },
  'dorm-floor-5': {
    title: '5th Floor: Waling-Waling Dormitory',
    floor: '5th Floor (Waling-Waling)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '501', available: false },
      { num: '502', available: true },
      { num: '503', available: false },
      { num: '504', available: false },
      { num: '505', available: false },
      { num: '506', available: true },
      { num: '507', available: false },
      { num: '508', available: false },
      { num: '509', available: false },
      { num: '510', available: true },
      { num: '511', available: false },
      { num: '512', available: false }
    ]
  },
  'dorm-floor-6': {
    title: '6th Floor: Tayabak Dormitory',
    floor: '6th Floor (Tayabak)',
    desc: 'Standard trainee dormitory floor with 12 air-conditioned rooms, individual lockers, and study desks.',
    rate: '₱500 / night',
    rooms: [
      { num: '601', available: true },
      { num: '602', available: false },
      { num: '603', available: false },
      { num: '604', available: true },
      { num: '605', available: false },
      { num: '606', available: false },
      { num: '607', available: false },
      { num: '608', available: true },
      { num: '609', available: false },
      { num: '610', available: false },
      { num: '611', available: false },
      { num: '612', available: true }
    ]
  }
};
let currentModalDormId = null;
let modalTempSelectedRoom = null;

function initFacilitySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    const selectBtn = card.querySelector('.btn-select-facility');
    const isDorm = card.dataset.facilityType === 'dormitories' || card.dataset.id.startsWith('dorm-floor-');

    function selectCard(openModalIfDorm = true) {
      cards.forEach(c => {
        c.classList.remove('selected');
        const btn = c.querySelector('.btn-select-facility');
        if (btn) {
          const isCDorm = c.dataset.facilityType === 'dormitories' || c.dataset.id.startsWith('dorm-floor-');
          if (isCDorm) {
            btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Room &amp; View Floor Plan`;
          } else {
            btn.innerHTML = 'Select This Facility';
          }
        }
      });

      card.classList.add('selected');

      bookingState.selectedFacility = {
        id: card.dataset.id,
        name: card.dataset.name,
        rate: card.dataset.rate,
        capacity: card.dataset.capacity,
        type: card.dataset.facilityType,
        roomNumber: bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomNumber || null) : null,
        floor: bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.floor || null) : null,
        roomRate: bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomRate || null) : null
      };

      if (isDorm) {
        if (openModalIfDorm) {
          openRoomModal(card.dataset.id);
        }
      } else {
        // Hall / Venue
        if (selectBtn) {
          selectBtn.innerHTML = `
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Selected Venue ✓
          `;
        }
        bookingState.selectedFacility.roomNumber = null;
        bookingState.selectedFacility.floor = null;

        updateReviewSummary();

        const proceedBtn = document.getElementById('btnProceedStep');
        if (proceedBtn) {
          proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time Selection</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        }
      }

      updateReviewSummary();
    }

    if (selectBtn) {
      selectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        selectCard(true);
      });
    }

    card.addEventListener('click', () => {
      selectCard(isDorm);
    });
  });

  // Modal event bindings
  const modalOverlay = document.getElementById('roomSelectionModal');
  const btnClose = document.getElementById('btnModalClose');
  const btnCancel = document.getElementById('btnModalCancel');
  const btnConfirm = document.getElementById('btnModalConfirm');

  if (btnClose) btnClose.addEventListener('click', closeRoomModal);
  if (btnCancel) btnCancel.addEventListener('click', closeRoomModal);
  if (btnConfirm) btnConfirm.addEventListener('click', confirmRoomSelection);

  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeRoomModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('roomSelectionModal');
      if (modal && modal.style.display !== 'none') {
        closeRoomModal();
      }
    }
  });
}

function openRoomModal(dormId) {
  const data = dormFloorData[dormId];
  if (!data) return;

  currentModalDormId = dormId;
  modalTempSelectedRoom = null;

  const modal = document.getElementById('roomSelectionModal');
  const titleEl = document.getElementById('modalDormTitle');
  const floorEl = document.getElementById('modalDormFloor');
  const rateEl = document.getElementById('modalDormRate');
  const gridEl = document.getElementById('modalRoomsGrid');
  const feedbackBar = document.getElementById('modalRoomFeedback');
  const confirmBtn = document.getElementById('btnModalConfirm');

  if (titleEl) titleEl.textContent = data.title;
  if (floorEl) floorEl.textContent = `${data.floor} • ${data.desc}`;
  if (rateEl) rateEl.textContent = data.rate;

  if (feedbackBar) feedbackBar.style.display = 'none';
  if (confirmBtn) {
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = `<span>Confirm Room &amp; Proceed to Date Selection</span> <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
  }

  // Populate 12 room boxes
  if (gridEl) {
    gridEl.innerHTML = '';
    const bedIconSvg = `
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 7v11M21 11v7M3 15h18M3 11h14a4 4 0 0 1 4 4v0M7 11V8a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3"/>
      </svg>
    `;

    data.rooms.forEach(r => {
      const box = document.createElement('div');
      box.className = `dorm-room-box ${r.available ? 'available' : 'reserved'}`;
      box.dataset.room = r.num;
      box.dataset.available = r.available ? 'true' : 'false';

      box.innerHTML = `
        <div class="dorm-bed-icon">${bedIconSvg}</div>
        <div class="dorm-room-num">${r.num}</div>
        <div class="dorm-room-status">${r.available ? 'AVAILABLE' : 'RESERVED'}</div>
      `;

      // If previously selected, highlight it
      if (bookingState.selectedFacility?.id === dormId && String(bookingState.selectedFacility?.roomNumber) === String(r.num)) {
        box.classList.add('selected');
        modalTempSelectedRoom = r.num;
        if (feedbackBar) {
          feedbackBar.style.display = 'flex';
          const fTitle = document.getElementById('modalFeedbackTitle');
          const fSub = document.getElementById('modalFeedbackSub');
          if (fTitle) fTitle.textContent = `Room ${r.num} Selected`;
          if (fSub) fSub.textContent = `${data.floor} • ${data.title} (${data.rate})`;
        }
        if (confirmBtn) {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = `<span>Confirm Room ${r.num} &amp; Proceed &rarr;</span>`;
        }
      }

      box.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!r.available) {
          alert(`Room ${r.num} (${data.floor}) is currently RESERVED for scheduled agricultural training delegates.\n\nPlease select one of the AVAILABLE rooms highlighted in green.`);
          return;
        }

        gridEl.querySelectorAll('.dorm-room-box.selected').forEach(b => b.classList.remove('selected'));
        box.classList.add('selected');
        modalTempSelectedRoom = r.num;

        if (feedbackBar) {
          feedbackBar.style.display = 'flex';
          const fTitle = document.getElementById('modalFeedbackTitle');
          const fSub = document.getElementById('modalFeedbackSub');
          const badgeOk = feedbackBar.querySelector('.badge-assigned-ok');
          if (fTitle) fTitle.textContent = `Room ${r.num} Selected ✓`;
          if (fSub) fSub.textContent = `${data.floor} • ${data.title} (${data.rate}) • Room Assigned`;
          if (badgeOk) badgeOk.textContent = '✓ Room Confirmed';
        }

        if (confirmBtn) {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = `<span>Confirm Room ${r.num}</span>`;
        }

        // Close modal and let user review reservation summary in Step 1
        setTimeout(() => {
          confirmRoomSelection();
        }, 400);
      });

      gridEl.appendChild(box);
    });
  }

  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function closeRoomModal() {
  const modal = document.getElementById('roomSelectionModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

function confirmRoomSelection() {
  if (!modalTempSelectedRoom || !currentModalDormId) return;

  const data = dormFloorData[currentModalDormId];
  if (!data) return;

  // Set bookingState
  bookingState.selectedFacility = {
    id: currentModalDormId,
    name: data.title,
    rate: data.rate,
    capacity: document.querySelector(`.facility-choice-card[data-id="${currentModalDormId}"]`)?.dataset.capacity || '40 GUESTS',
    type: 'dormitories',
    roomNumber: modalTempSelectedRoom,
    floor: data.floor,
    roomRate: data.rate
  };

  // Update card on page
  const card = document.querySelector(`.facility-choice-card[data-id="${currentModalDormId}"]`);
  if (card) {
    document.querySelectorAll('.facility-choice-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');

    const badge = card.querySelector('.dorm-card-selected-room-badge');
    const badgeText = card.querySelector('.d-room-text');
    if (badge && badgeText) {
      badgeText.textContent = `✓ Room ${modalTempSelectedRoom} Assigned (${data.floor})`;
      badge.style.display = 'flex';
    }

    const btn = card.querySelector('.btn-select-facility');
    if (btn) {
      btn.innerHTML = `✓ Room ${modalTempSelectedRoom} Selected (Click to change)`;
    }
  }

  closeRoomModal();
  updateReviewSummary();

  // Update Proceed button text so user can review summary then proceed when ready
  const proceedBtn = document.getElementById('btnProceedStep');
  if (proceedBtn) {
    proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (Room ${modalTempSelectedRoom})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
    proceedBtn.style.display = 'inline-flex';
  }

  // Smooth scroll down to the reservation summary preview card before proceeding
  const summaryCard = document.getElementById('step1SummaryCard');
  if (summaryCard) {
    summaryCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}

/* ==========================================================================
   2. Filter & Category Selection (Halls vs Dormitories)
   ========================================================================== */
function initFacilityFilters() {
  const categoryCards = document.querySelectorAll('.category-pick-card, .portal-cat-card');
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
        activeCategoryName.textContent = 'Dormitories (6 Floors)';
      } else {
        activeCategoryName.textContent = 'All Facilities (10 Total)';
      }
    }

    if (sectionHeading) {
      if (catName === 'halls') {
        sectionHeading.textContent = 'AVAILABLE HALLS & VENUES (4):';
      } else if (catName === 'dormitories') {
        sectionHeading.textContent = 'AVAILABLE DORMITORY FLOORS (6):';
      } else {
        sectionHeading.textContent = 'ALL AVAILABLE FACILITIES & ROOMS (10):';
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
    const activePill = document.querySelector('.filter-pill.active');
    const defaultCat = activePill ? activePill.dataset.categoryFilter : 'dormitories';
    switchCategory(defaultCat, false);
  }
}

/* ==========================================================================
   3. Stepper Wizard Flow
   ========================================================================== */
function initStepperNavigation() {
  const stepButtons = document.querySelectorAll('.step-tab-btn');
  const stepViews = document.querySelectorAll('.wizard-step-view');
  const proceedBtn = document.getElementById('btnProceedStep');
  const backBtn = document.getElementById('btnStepBack');
  const bottomActions = document.getElementById('bookingBottomActions') || document.querySelector('.booking-bottom-actions');

  function isDormitorySelected() {
    return bookingState.selectedFacility?.type === 'dormitories' ||
           String(bookingState.selectedFacility?.id).startsWith('dorm-');
  }

  function updateStepperMode() {
    const isDorm = isDormitorySelected();
    const stepTabEvent = document.getElementById('stepTabEventDetails');
    const stepTabDocs = document.getElementById('stepTabDocuments');
    const stepTabRev = document.getElementById('stepTabReview');
    const dotEvent = document.getElementById('mobileDotEventDetails');
    const dotDocs = document.getElementById('mobileDotDocuments');
    const dotRev = document.getElementById('mobileDotReview');

    // Both Halls and Dormitories always show all 5 steps (including Event Details)!
    if (stepTabEvent) stepTabEvent.style.display = '';
    if (dotEvent) dotEvent.style.display = '';
    if (stepTabDocs) {
      const n = stepTabDocs.querySelector('.step-number');
      if (n) n.textContent = '4';
    }
    if (stepTabRev) {
      const n = stepTabRev.querySelector('.step-number');
      if (n) n.textContent = '5';
    }
    if (dotDocs) dotDocs.textContent = '4';
    if (dotRev) dotRev.textContent = '5';

    // Contextualize Step 3 Form labels & placeholders for Dormitory vs Hall
    const step3Title = document.getElementById('step3FormTitle');
    const step3Desc = document.getElementById('step3FormDesc');
    const eventTitleLabel = document.getElementById('eventTitleLabel');
    const eventTitleInput = document.getElementById('eventTitleInput');
    const eventPaxLabel = document.getElementById('eventPaxLabel');
    const eventPaxInput = document.getElementById('eventPaxInput');
    const specialNotesLabel = document.getElementById('specialNotesLabel');
    const specialNotes = document.getElementById('specialNotes');

    if (isDorm) {
      if (step3Title) step3Title.textContent = 'Step 3: Event & Accommodation Details';
      if (step3Desc) step3Desc.textContent = 'Provide details regarding your training activity or event, trainees/guests lodging, and specific accommodation requirements.';
      if (eventTitleLabel) eventTitleLabel.textContent = 'Training Activity / Event Purpose';
      if (eventTitleInput && !eventTitleInput.value) {
        eventTitleInput.placeholder = 'e.g. Regional Agricultural Extension Training Delegates Lodging';
      }
      if (eventPaxLabel) eventPaxLabel.textContent = 'Number of Trainees / Guests Staying';
      if (eventPaxInput && !eventPaxInput.value) {
        eventPaxInput.placeholder = 'e.g. 12';
      }
      if (specialNotesLabel) specialNotesLabel.textContent = 'Accommodation Requests & Room Notes (Optional)';
      if (specialNotes && !specialNotes.value) {
        specialNotes.placeholder = 'e.g. Late check-in after 7:00 PM; separate male/female quarters; extra linens requested.';
      }
    } else {
      if (step3Title) step3Title.textContent = 'Step 3: Event & Activity Information';
      if (step3Desc) step3Desc.textContent = 'Provide details regarding the nature of your activity, participants, and specific requirements.';
      if (eventTitleLabel) eventTitleLabel.textContent = 'Activity / Event Title';
      if (eventTitleInput && !eventTitleInput.value) {
        eventTitleInput.placeholder = 'e.g. Regional Agricultural Extension Coordinators Training 2026';
      }
      if (eventPaxLabel) eventPaxLabel.textContent = 'Estimated Number of Attendees';
      if (eventPaxInput && !eventPaxInput.value) {
        eventPaxInput.placeholder = 'e.g. 120';
      }
      if (specialNotesLabel) specialNotesLabel.textContent = 'Special Equipment / Setup Notes (Optional)';
      if (specialNotes && !specialNotes.value) {
        specialNotes.placeholder = 'e.g. Needs 4 wireless microphones, podium banner stand, and registration tables.';
      }
    }
  }

  goToStep = function(stepNumber) {
    if (stepNumber < 1 || stepNumber > 5) return;

    const isDorm = isDormitorySelected();

    // Requirement: When reserving a dormitory accommodation, user MUST select a room first before proceeding to Step 2
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      if (isDorm && !bookingState.selectedFacility?.roomNumber) {
        alert('Please choose an AVAILABLE room unit (highlighted in green, e.g. Room 101, 201, 302) inside your chosen dormitory floor before proceeding to Date & Time Selection.');
        const selectedCard = document.querySelector('.facility-choice-card.selected');
        if (selectedCard) {
          selectedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
    updateStepperMode();

    // Update Desktop Stepper Cards (strictly visual progress indicator cards)
    stepButtons.forEach((btn) => {
      const stepIdx = parseInt(btn.dataset.step, 10);
      btn.classList.remove('active', 'completed');
      if (stepIdx === stepNumber) {
        btn.classList.add('active');
      } else if (stepIdx < stepNumber) {
        btn.classList.add('completed');
      }
    });

    // Update Mobile Compact Progress Bar (always 5 steps)
    const totalSteps = 5;
    const stepLabels = {
      1: 'Facility Selection',
      2: 'Date & Time Selection',
      3: isDorm ? 'Accommodation & Event Details' : 'Event & Activity Details',
      4: 'Document Upload',
      5: 'Review & Submit'
    };

    const mobileBadge = document.getElementById('mobileStepBadge');
    const mobileName = document.getElementById('mobileStepName');
    const mobilePercent = document.getElementById('mobileStepPercent');
    const mobileFill = document.getElementById('mobileProgressFill');
    const mobileDots = document.querySelectorAll('.mobile-dot');

    const progressPercent = Math.round((stepNumber / totalSteps) * 100);

    if (mobileBadge) mobileBadge.textContent = `Step ${stepNumber} of ${totalSteps}`;
    if (mobileName) mobileName.textContent = stepLabels[stepNumber] || 'Reservation Details';
    if (mobilePercent) mobilePercent.textContent = `${progressPercent}%`;
    if (mobileFill) mobileFill.style.width = `${progressPercent}%`;

    if (mobileDots) {
      mobileDots.forEach((dot) => {
        const dotStep = parseInt(dot.dataset.step, 10);
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

    // Bottom Navigation Bar is ALWAYS visible across all steps
    if (bottomActions) {
      bottomActions.style.display = 'flex';
    }

    // Back button behavior
    if (backBtn) {
      backBtn.style.display = stepNumber > 1 ? 'inline-flex' : 'none';
    }

    // Proceed button behavior across all 5 steps
    if (proceedBtn) {
      if (stepNumber === 1) {
        proceedBtn.style.display = 'inline-flex';
        const dormRoom = bookingState.selectedFacility?.roomNumber;
        if (isDorm && dormRoom) {
          proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (Room ${dormRoom})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        } else {
          proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time Selection</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
        }
      } else if (stepNumber === 2) {
        proceedBtn.style.display = 'inline-flex';
        proceedBtn.innerHTML = `<span>Proceed to Event Details</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else if (stepNumber === 3) {
        proceedBtn.style.display = 'inline-flex';
        proceedBtn.innerHTML = `<span>Proceed to Document Upload</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else if (stepNumber === 4) {
        proceedBtn.style.display = 'inline-flex';
        proceedBtn.innerHTML = `<span>Review &amp; Confirm Details</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else if (stepNumber === 5) {
        // Step 5 has its dedicated submit button inside the official review summary card
        proceedBtn.style.display = 'none';
      }
    }

    window.scrollTo({ top: 120, behavior: 'smooth' });
    updateReviewSummary();
  };

  window.goToStep = goToStep;

  // IMPORTANT: Stepper cards & mobile dots are NON-CLICKABLE progress step cards!
  // Navigation is driven exclusively by the bottom Back & Proceed buttons.

  // Proceed button click listener
  if (proceedBtn) {
    proceedBtn.addEventListener('click', () => {
      const isDorm = isDormitorySelected();
      if (bookingState.currentStep === 1) {
        if (isDorm && !bookingState.selectedFacility?.roomNumber) {
          alert('Please choose an AVAILABLE room unit (highlighted in green, e.g. Room 101, 201, 302) inside your chosen dormitory floor before proceeding to Date & Time Selection.');
          const card = document.querySelector('.facility-choice-card.selected');
          if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }
        goToStep(2);
      } else if (bookingState.currentStep === 2) {
        goToStep(3); // Proceeds to Event Details for BOTH dormitories and halls!
      } else if (bookingState.currentStep === 3) {
        goToStep(4);
      } else if (bookingState.currentStep === 4) {
        goToStep(5);
      }
    });
  }

  // Back button click listener
  if (backBtn) {
    backBtn.addEventListener('click', () => {
      goToStep(bookingState.currentStep - 1);
    });
  }

  // Step 3 live input bindings
  const eventTitleInput = document.getElementById('eventTitleInput');
  const eventPaxInput = document.getElementById('eventPaxInput');
  if (eventTitleInput) {
    eventTitleInput.addEventListener('input', () => {
      bookingState.eventTitle = eventTitleInput.value;
      updateReviewSummary();
    });
  }
  if (eventPaxInput) {
    eventPaxInput.addEventListener('input', () => {
      bookingState.participants = eventPaxInput.value;
      updateReviewSummary();
    });
  }

  // Final Submit listener on Step 5
  const finalSubmitBtn = document.getElementById('btnSubmitFinalReservation');
  if (finalSubmitBtn) {
    finalSubmitBtn.addEventListener('click', () => {
      const refNum = `ATI-RES-2026-${Math.floor(1000 + Math.random() * 9000)}`;
      let summaryMsg = `Reservation Request Submitted Successfully!\n\nReference: ${refNum}\nFacility: ${bookingState.selectedFacility.name}`;
      if (bookingState.selectedFacility.roomNumber) {
        summaryMsg += `\nAssigned Room: Room ${bookingState.selectedFacility.roomNumber} (${bookingState.selectedFacility.floor || ''})`;
      }
      if (bookingState.eventTitle) {
        summaryMsg += `\nEvent / Purpose: ${bookingState.eventTitle}`;
      }
      summaryMsg += `\nSchedule: ${bookingState.date}\nStatus: Pending Administrative Review\n\nNotification has been sent to your registered email.`;
      alert(summaryMsg);
      window.location.href = 'booking.php';
    });
  }

  // Initial stepper setup
  updateStepperMode();
}

/* ==========================================================================
   4. Update Summary on Step 1 (Preview) and Step 5 (Official Summary)
   ========================================================================== */
function updateReviewSummary() {
  const isDorm = bookingState.selectedFacility?.type === 'dormitories' ||
                 String(bookingState.selectedFacility?.id).startsWith('dorm-');
  const roomNum = bookingState.selectedFacility?.roomNumber;
  const floorName = bookingState.selectedFacility?.floor || '';
  const facilityName = bookingState.selectedFacility?.name || 'Function Hall';
  const rateText = bookingState.selectedFacility?.roomRate || bookingState.selectedFacility?.rate || '₱5,000/day';
  const capText = bookingState.selectedFacility?.capacity || '150 - 200 PAX';

  // 1. Step 1 Summary Preview Card
  const prevBadge = document.getElementById('summaryPreviewBadge');
  const prevTitle = document.getElementById('summaryPreviewTitle');
  const prevRate = document.getElementById('summaryPreviewRate');
  const prevRoomWrap = document.getElementById('summaryPreviewRoomWrap');
  const prevRoom = document.getElementById('summaryPreviewRoom');
  const prevCap = document.getElementById('summaryPreviewCap');

  if (prevBadge) prevBadge.textContent = isDorm ? 'DORMITORY ACCOMMODATION' : 'SELECTED VENUE';
  if (prevTitle) prevTitle.textContent = facilityName;
  if (prevRate) prevRate.textContent = rateText;
  if (prevCap) prevCap.textContent = capText;

  if (prevRoomWrap && prevRoom) {
    if (isDorm && roomNum) {
      prevRoomWrap.style.display = 'flex';
      prevRoom.textContent = `Room ${roomNum} (${floorName})`;
    } else {
      prevRoomWrap.style.display = 'none';
      prevRoom.textContent = 'None';
    }
  }

  // 2. Step 5 Official Review Summary Card
  const sumBadge = document.getElementById('summaryFacilityBadge');
  const sumVenue = document.getElementById('summaryVenueName');
  const sumRate = document.getElementById('summaryVenueRate');
  const sumRoomWrap = document.getElementById('summaryRoomDetailWrap');
  const sumRoom = document.getElementById('summaryRoomDetail');
  const sumFacType = document.getElementById('summaryFacilityType');
  const sumCap = document.getElementById('summaryVenueCapacity');
  const sumDate = document.getElementById('summaryReservationDate');
  const sumTime = document.getElementById('summaryTimeSlot');

  if (sumBadge) sumBadge.textContent = isDorm ? 'DORMITORY ACCOMMODATION' : 'OFFICIAL VENUE RESERVATION';
  if (sumVenue) sumVenue.textContent = facilityName;
  if (sumRate) sumRate.textContent = rateText;
  if (sumFacType) sumFacType.textContent = isDorm ? 'Trainee Dormitory Lodging' : 'Conference & Training Venue';
  if (sumCap) sumCap.textContent = capText;
  if (sumDate) sumDate.textContent = bookingState.date || '10/05/2026';
  if (sumTime) sumTime.textContent = isDorm ? 'Overnight Lodging Stay' : (bookingState.timeSlot || 'Whole Day (8:00 AM - 5:00 PM)');

  if (sumRoomWrap && sumRoom) {
    if (isDorm && roomNum) {
      sumRoomWrap.style.display = 'flex';
      sumRoom.textContent = `Room ${roomNum} (${floorName})`;
    } else {
      sumRoomWrap.style.display = 'none';
      sumRoom.textContent = 'None';
    }
  }

  // Step 5 Event Details Summary fields
  const eventTitleInput = document.getElementById('eventTitleInput');
  const eventPaxInput = document.getElementById('eventPaxInput');
  const sumEventTitle = document.getElementById('summaryEventTitle');
  const sumPaxValue = document.getElementById('summaryPaxValue');
  const sumEventLabel = document.getElementById('summaryEventLabel');
  const sumPaxLabel = document.getElementById('summaryPaxLabel');

  const curTitle = eventTitleInput?.value?.trim() || bookingState.eventTitle || (isDorm ? 'Agricultural Training Delegates Lodging' : 'Regional Agricultural Training Workshop');
  const curPax = eventPaxInput?.value?.trim() || bookingState.participants || (isDorm ? '12 Trainees' : '120 Attendees');

  if (sumEventLabel) sumEventLabel.textContent = isDorm ? 'Training / Stay Purpose:' : 'Activity / Event Title:';
  if (sumEventTitle) sumEventTitle.textContent = curTitle;
  if (sumPaxLabel) sumPaxLabel.textContent = isDorm ? 'Trainees / Lodgers:' : 'Participants / Attendees:';
  if (sumPaxValue) sumPaxValue.textContent = curPax;
}


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
  // Room selection modal is dynamically controlled
}
