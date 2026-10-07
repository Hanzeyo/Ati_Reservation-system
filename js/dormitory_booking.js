/**
 * Agriculture Training Institute - Dormitory Reservation System
 * Dedicated Controller for Official Dormitory Lodging & Room Allotments
 * Separated cleanly from Facility Venue Booking logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initDormitorySelection();
  initDormitoryDatesCalculation();
  initDormitoryStepper();
  initDormitoryRoomModal();
  initDormitorySubmit();
  initProfileDropdown();
  initMobileDrawer();
});

// State Store for Dormitory Reservation
const dormBookingState = {
  currentStep: 1,
  totalSteps: 5,
  selectedFloor: {
    id: 'dorm-floor-1',
    name: '1st Floor: Sampaguita Dormitory',
    shortName: 'Sampaguita',
    rate: 500,
    rateText: '₱500 / night',
    capacity: '10 ROOMS (40 BEDS)',
    selectedRooms: ['Room 101']
  },
  checkInDate: '2026-10-15',
  checkOutDate: '2026-10-18',
  nights: 3,
  purpose: 'Regional Agricultural Extension Trainees Accommodation',
  guestCount: 16,
  division: 'Career Development Division (CDD)',
  maleCount: 8,
  femaleCount: 8,
  specialRequests: 'Linens & towels requested. Gender-segregated rooms on 1st Floor.',
  referenceNo: 'ATI-DORM-' + Math.floor(100000 + Math.random() * 900000)
};

// 6 Floors Inventory Data
const DORM_FLOORS_DATA = {
  'dorm-floor-1': {
    id: 'dorm-floor-1',
    name: '1st Floor: Sampaguita Dormitory',
    shortName: '1st Floor (Sampaguita)',
    desc: 'Ground level trainee delegation quarters with 10 air-conditioned rooms, wooden bunk beds, and individual lockers.',
    rate: 500,
    rateText: '₱500 / night',
    capText: '4 Beds per Room',
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
      { num: '110', available: true }
    ]
  },
  'dorm-floor-2': {
    id: 'dorm-floor-2',
    name: '2nd Floor: Ilang-Ilang Dormitory',
    shortName: '2nd Floor (Ilang-Ilang)',
    desc: 'Standard trainee dormitory floor with 10 air-conditioned rooms, wooden bunk beds, and individual lockers.',
    rate: 500,
    rateText: '₱500 / night',
    capText: '4 Beds per Room',
    rooms: [
      { num: '201', available: true },
      { num: '202', available: false },
      { num: '203', available: true },
      { num: '204', available: false },
      { num: '205', available: true },
      { num: '206', available: true },
      { num: '207', available: false },
      { num: '208', available: true },
      { num: '209', available: false },
      { num: '210', available: true }
    ]
  },
  'dorm-floor-3': {
    id: 'dorm-floor-3',
    name: '3rd Floor: Gumamela Dormitory',
    shortName: '3rd Floor (Gumamela)',
    desc: 'Standard trainee delegation floor with 10 air-conditioned rooms, wooden bunk beds, and study lounges.',
    rate: 500,
    rateText: '₱500 / night',
    capText: '4 Beds per Room',
    rooms: [
      { num: '301', available: true },
      { num: '302', available: true },
      { num: '303', available: false },
      { num: '304', available: true },
      { num: '305', available: true },
      { num: '306', available: false },
      { num: '307', available: true },
      { num: '308', available: false },
      { num: '309', available: true },
      { num: '310', available: true }
    ]
  },
  'dorm-floor-4': {
    id: 'dorm-floor-4',
    name: '4th Floor: Rosal Dormitory',
    shortName: '4th Floor (Rosal)',
    desc: 'Standard trainee delegation quarters with 10 air-conditioned rooms, wooden bunk beds, and delegation lounge.',
    rate: 500,
    rateText: '₱500 / night',
    capText: '4 Beds per Room',
    rooms: [
      { num: '401', available: true },
      { num: '402', available: true },
      { num: '403', available: true },
      { num: '404', available: false },
      { num: '405', available: true },
      { num: '406', available: true },
      { num: '407', available: false },
      { num: '408', available: true },
      { num: '409', available: true },
      { num: '410', available: false }
    ]
  },
  'dorm-floor-5': {
    id: 'dorm-floor-5',
    name: '5th Floor: Waling-Waling Dormitory',
    shortName: '5th Floor (Waling-Waling VIP)',
    desc: 'VIP guest suite floor with 10 air-conditioned executive rooms, private ensuite bath, executive desk, and personal fridge.',
    rate: 800,
    rateText: '₱800 / night',
    capText: '2 Beds per Executive Suite',
    rooms: [
      { num: '501', available: true },
      { num: '502', available: true },
      { num: '503', available: false },
      { num: '504', available: true },
      { num: '505', available: true },
      { num: '506', available: false },
      { num: '507', available: true },
      { num: '508', available: true },
      { num: '509', available: true },
      { num: '510', available: false }
    ]
  },
  'dorm-floor-6': {
    id: 'dorm-floor-6',
    name: '6th Floor: Dama de Noche Dormitory',
    shortName: '6th Floor (Dama de Noche VIP)',
    desc: 'Penthouse executive suite floor with 10 air-conditioned VIP suites, ensuite bath, hot shower, executive desk, and city views.',
    rate: 800,
    rateText: '₱800 / night',
    capText: '2 Beds per Penthouse Suite',
    rooms: [
      { num: '601', available: true },
      { num: '602', available: false },
      { num: '603', available: true },
      { num: '604', available: true },
      { num: '605', available: false },
      { num: '606', available: true },
      { num: '607', available: true },
      { num: '608', available: false },
      { num: '609', available: true },
      { num: '610', available: false }
    ]
  }
};

/* ==========================================================================
   1. Dormitory Floor Selection
   ========================================================================== */
function initDormitorySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    card.addEventListener('click', (e) => {
      // Don't auto-open modal if user clicked the specific view button
      if (e.target.closest('.btn-select-facility')) {
        openDormRoomModal(card.dataset.id);
        return;
      }
      selectDormFloor(card.dataset.id);
    });

    const triggerBtn = card.querySelector('.btn-select-facility');
    if (triggerBtn) {
      triggerBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        selectDormFloor(card.dataset.id);
        openDormRoomModal(card.dataset.id);
      });
    }
  });

  // Select initial floor
  selectDormFloor('dorm-floor-1');
}

function selectDormFloor(floorId) {
  const floorData = DORM_FLOORS_DATA[floorId];
  if (!floorData) return;

  dormBookingState.selectedFloor.id = floorId;
  dormBookingState.selectedFloor.name = floorData.name;
  dormBookingState.selectedFloor.shortName = floorData.shortName;
  dormBookingState.selectedFloor.rate = floorData.rate;
  dormBookingState.selectedFloor.rateText = floorData.rateText;
  dormBookingState.selectedFloor.capacity = floorData.capText;

  // Visual selection of card
  document.querySelectorAll('.facility-choice-card').forEach(c => {
    const isSelected = c.dataset.id === floorId;
    c.classList.toggle('selected', isSelected);
  });

  // Update summary card preview on Step 1
  const titleEl = document.getElementById('summaryPreviewTitle');
  const rateEl = document.getElementById('summaryPreviewRate');
  const capEl = document.getElementById('summaryPreviewCap');
  const roomEl = document.getElementById('summaryPreviewRoom');

  if (titleEl) titleEl.textContent = floorData.name;
  if (rateEl) rateEl.textContent = floorData.rateText;
  if (capEl) capEl.textContent = floorData.capText;
  if (roomEl) roomEl.textContent = dormBookingState.selectedFloor.selectedRooms.join(', ') || 'Room 101 Selected';

  // Recalculate rate for stay dates
  calculateDormStayRate();
}

/* ==========================================================================
   2. Stay Dates & Nights Calculation (Step 2)
   ========================================================================== */
function initDormitoryDatesCalculation() {
  const inInput = document.getElementById('dormCheckInDate');
  const outInput = document.getElementById('dormCheckOutDate');

  if (inInput) {
    inInput.addEventListener('change', () => {
      dormBookingState.checkInDate = inInput.value;
      if (outInput && outInput.value <= inInput.value) {
        const nextDay = new Date(inInput.value);
        nextDay.setDate(nextDay.getDate() + 1);
        outInput.value = nextDay.toISOString().split('T')[0];
      }
      calculateDormStayRate();
    });
  }

  if (outInput) {
    outInput.addEventListener('change', () => {
      dormBookingState.checkOutDate = outInput.value;
      calculateDormStayRate();
    });
  }

  calculateDormStayRate();
}

function calculateDormStayRate() {
  const inInput = document.getElementById('dormCheckInDate');
  const outInput = document.getElementById('dormCheckOutDate');
  const nightsDisplay = document.getElementById('dormNightsCountDisplay');
  const estRateDisplay = document.getElementById('dormEstimatedRateDisplay');

  if (!inInput || !outInput) return;

  const dIn = new Date(inInput.value);
  const dOut = new Date(outInput.value);

  let diffTime = dOut.getTime() - dIn.getTime();
  let nights = Math.max(1, Math.round(diffTime / (1000 * 3600 * 24)));
  if (isNaN(nights) || nights < 1) nights = 1;

  dormBookingState.nights = nights;
  const ratePerNight = dormBookingState.selectedFloor.rate || 500;
  const totalEst = nights * ratePerNight;

  if (nightsDisplay) {
    nightsDisplay.textContent = `${nights} Night${nights > 1 ? 's' : ''} Accommodation`;
  }
  if (estRateDisplay) {
    estRateDisplay.textContent = `₱${totalEst.toLocaleString()}`;
  }
}

/* ==========================================================================
   3. Stepper Controller (5 Steps)
   ========================================================================== */
function initDormitoryStepper() {
  const tabBtns = document.querySelectorAll('.step-tab-btn');
  const btnProceed = document.getElementById('btnProceedStep');
  const btnBack = document.getElementById('btnStepBack');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const step = parseInt(btn.dataset.step);
      goToDormStep(step);
    });
  });

  if (btnProceed) {
    btnProceed.addEventListener('click', () => {
      goToDormStep(dormBookingState.currentStep + 1);
    });
  }

  if (btnBack) {
    btnBack.addEventListener('click', () => {
      goToDormStep(dormBookingState.currentStep - 1);
    });
  }
}

function goToDormStep(step) {
  if (step < 1 || step > dormBookingState.totalSteps) return;
  dormBookingState.currentStep = step;

  // Toggle active view
  document.querySelectorAll('.wizard-step-view').forEach(view => {
    view.classList.toggle('active', parseInt(view.dataset.step) === step);
  });

  // Desktop Stepper Tabs
  document.querySelectorAll('.step-tab-btn').forEach(tab => {
    tab.classList.toggle('active', parseInt(tab.dataset.step) === step);
  });

  // Mobile Dots
  document.querySelectorAll('.mobile-dot').forEach(dot => {
    dot.classList.toggle('active', parseInt(dot.dataset.step) === step);
  });

  // Mobile Header Indicators
  const badge = document.getElementById('mobileStepBadge');
  const stepName = document.getElementById('mobileStepName');
  const percent = document.getElementById('mobileStepPercent');
  const fill = document.getElementById('mobileProgressFill');
  const btnBack = document.getElementById('btnStepBack');
  const btnProceed = document.getElementById('btnProceedStep');

  const names = ['Dormitory Floor', 'Stay Dates', 'Lodging Details', 'Roster Documents', 'Review & Submit'];
  if (badge) badge.textContent = `Step ${step} of 5`;
  if (stepName) stepName.textContent = names[step - 1];
  const pct = (step / 5) * 100;
  if (percent) percent.textContent = `${pct}%`;
  if (fill) fill.style.width = `${pct}%`;

  if (btnBack) btnBack.style.display = step > 1 ? 'inline-flex' : 'none';
  if (btnProceed) {
    if (step === 5) {
      btnProceed.style.display = 'none';
    } else {
      btnProceed.style.display = 'inline-flex';
      const nextNames = ['Stay Dates', 'Lodging Details', 'Roster Documents', 'Review Summary'];
      btnProceed.querySelector('span').textContent = `Proceed to ${nextNames[step - 1]}`;
    }
  }

  // Populate Step 5 Review screen if reaching step 5
  if (step === 5) {
    populateDormReviewScreen();
  }

  window.scrollTo({ top: 120, behavior: 'smooth' });
}

function populateDormReviewScreen() {
  const purposeInput = document.getElementById('dormLodgingPurpose');
  const guestCountInput = document.getElementById('dormGuestCount');
  const maleCountInput = document.getElementById('dormMaleCount');
  const femaleCountInput = document.getElementById('dormFemaleCount');

  const purpose = purposeInput ? purposeInput.value : dormBookingState.purpose;
  const guests = guestCountInput ? guestCountInput.value : dormBookingState.guestCount;
  const male = maleCountInput ? maleCountInput.value : dormBookingState.maleCount;
  const female = femaleCountInput ? femaleCountInput.value : dormBookingState.femaleCount;

  const revTitle = document.getElementById('dormReviewTitle');
  const revFloor = document.getElementById('dormReviewFloor');
  const revStay = document.getElementById('dormReviewStay');
  const revPax = document.getElementById('dormReviewPax');
  const revRef = document.getElementById('dormRefPreview');

  if (revTitle) revTitle.textContent = purpose;
  if (revFloor) revFloor.textContent = `${dormBookingState.selectedFloor.name} (${dormBookingState.selectedFloor.selectedRooms.join(', ') || 'Room 101'})`;
  if (revStay) revStay.textContent = `${dormBookingState.checkInDate} to ${dormBookingState.checkOutDate} (${dormBookingState.nights} Nights)`;
  if (revPax) revPax.textContent = `${guests} Delegates (${male}M / ${female}F)`;
  if (revRef) revRef.textContent = dormBookingState.referenceNo;
}

/* ==========================================================================
   4. Room Allocation Modal (Interactive Bed / Room Picker)
   ========================================================================== */
let activeModalFloorId = null;

function initDormitoryRoomModal() {
  const closeBtn = document.getElementById('btnModalClose');
  const overlay = document.getElementById('roomSelectionModal');

  if (closeBtn) closeBtn.addEventListener('click', closeDormRoomModal);
  if (overlay) {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeDormRoomModal();
    });
  }
}

function openDormRoomModal(floorId) {
  activeModalFloorId = floorId || dormBookingState.selectedFloor.id;
  const floorData = DORM_FLOORS_DATA[activeModalFloorId];
  if (!floorData) return;

  const modal = document.getElementById('roomSelectionModal');
  const modalTitle = document.getElementById('modalDormTitle');
  const modalGrid = document.getElementById('modalRoomsGrid');

  if (modalTitle) modalTitle.textContent = floorData.name;

  if (modalGrid) {
    modalGrid.innerHTML = '';
    floorData.rooms.forEach(room => {
      const isSelected = dormBookingState.selectedFloor.id === activeModalFloorId &&
                         dormBookingState.selectedFloor.selectedRooms.includes(`Room ${room.num}`);

      const roomCard = document.createElement('div');
      roomCard.className = `room-box ${room.available ? 'available' : 'occupied'} ${isSelected ? 'selected' : ''}`;
      roomCard.innerHTML = `
        <div class="room-number">Room ${room.num}</div>
        <div class="room-status-badge">${room.available ? (isSelected ? '✓ Selected' : 'Available') : 'Reserved'}</div>
        <div class="room-meta-sub">${floorData.rate >= 800 ? '2 Twin Beds • VIP' : '4 Bunk Beds • Trainee'}</div>
      `;

      if (room.available) {
        roomCard.style.cursor = 'pointer';
        roomCard.addEventListener('click', () => {
          modalGrid.querySelectorAll('.room-box').forEach(b => b.classList.remove('selected'));
          roomCard.classList.add('selected');
          
          dormBookingState.selectedFloor.selectedRooms = [`Room ${room.num}`];
          
          // Update badge in card
          const card = document.querySelector(`.facility-choice-card[data-id="${activeModalFloorId}"]`);
          if (card) {
            const badge = card.querySelector('.dorm-card-selected-room-badge');
            if (badge) {
              badge.style.display = 'block';
              badge.querySelector('.d-room-text').textContent = `✓ Room ${room.num} Selected`;
            }
          }

          // Update summary preview
          const roomPreview = document.getElementById('summaryPreviewRoom');
          if (roomPreview) roomPreview.textContent = `Room ${room.num} Selected`;

          setTimeout(() => {
            closeDormRoomModal();
          }, 240);
        });
      }

      modalGrid.appendChild(roomCard);
    });
  }

  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function closeDormRoomModal() {
  const modal = document.getElementById('roomSelectionModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

/* ==========================================================================
   5. Submit Official Dormitory Request
   ========================================================================== */
function initDormitorySubmit() {
  const submitBtn = document.getElementById('btnSubmitDormitoryBooking');
  if (!submitBtn) return;

  submitBtn.addEventListener('click', () => {
    const payload = {
      id: dormBookingState.referenceNo,
      type: 'dormitory',
      floorName: dormBookingState.selectedFloor.name,
      rooms: dormBookingState.selectedFloor.selectedRooms,
      checkIn: dormBookingState.checkInDate,
      checkOut: dormBookingState.checkOutDate,
      nights: dormBookingState.nights,
      purpose: document.getElementById('dormLodgingPurpose')?.value || dormBookingState.purpose,
      guestCount: document.getElementById('dormGuestCount')?.value || dormBookingState.guestCount,
      division: document.getElementById('dormDivisionUnit')?.value || dormBookingState.division,
      status: 'pending_custodian',
      timestamp: new Date().toISOString()
    };

    // Save to localStorage so booking_history.php immediately reflects this official reservation
    try {
      const existing = JSON.parse(localStorage.getItem('ati_dormitory_reservations') || '[]');
      existing.unshift(payload);
      localStorage.setItem('ati_dormitory_reservations', JSON.stringify(existing));
    } catch (e) {
      console.warn('Could not persist to localStorage', e);
    }

    alert(`Official Dormitory Reservation Request Submitted Successfully!\n\nReference: ${dormBookingState.referenceNo}\nFloor: ${dormBookingState.selectedFloor.name}\nAssigned Room: ${dormBookingState.selectedFloor.selectedRooms.join(', ')}\n\nYour request has been routed to the Dormitory Custodian for Bed Assignment.`);
    window.location.href = 'booking_history.php';
  });
}

/* ==========================================================================
   6. Profile Dropdown & Mobile Drawer Handlers
   ========================================================================== */
function initProfileDropdown() {
  const badge = document.getElementById('userProfileBadge');
  const dropdown = document.getElementById('profileDropdown');

  if (badge && dropdown) {
    badge.addEventListener('click', (e) => {
      e.stopPropagation();
      dropdown.classList.toggle('show');
      dropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
      if (!badge.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
        dropdown.classList.remove('active');
      }
    });
  }
}

function initMobileDrawer() {
  window.toggleMobileDrawer = function(open) {
    const overlay = document.getElementById('mobileDrawerOverlay');
    const toggleBtn = document.getElementById('mobileMenuToggle');
    if (!overlay) return;
    if (open) {
      overlay.style.display = 'block';
      requestAnimationFrame(() => overlay.classList.add('show'));
      document.body.style.overflow = 'hidden';
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'true');
    } else {
      overlay.classList.remove('show');
      document.body.style.overflow = '';
      setTimeout(() => { overlay.style.display = 'none'; }, 260);
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }
  };
}
