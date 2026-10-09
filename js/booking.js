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
  initDocumentUpload();
});

function initDocumentUpload() {
  const dropZone = document.getElementById('documentUploadZone');
  const fileInput = document.getElementById('documentUploadInput');
  const fileLabel = document.getElementById('documentUploadLabel');
  const fileDesc = document.getElementById('documentUploadDesc');

  if (!dropZone || !fileInput) return;

  dropZone.addEventListener('click', () => fileInput.click());

  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.background = '#eaf2ec';
    dropZone.style.borderColor = '#2e7d32';
  });

  dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fbfdfc';
    dropZone.style.borderColor = '#b8ccbe';
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fbfdfc';
    dropZone.style.borderColor = '#b8ccbe';

    if (e.dataTransfer.files.length) {
      fileInput.files = e.dataTransfer.files;
      handleFileSelection();
    }
  });

  fileInput.addEventListener('change', handleFileSelection);

  function handleFileSelection() {
    if (fileInput.files.length > 0) {
      const file = fileInput.files[0];
      const maxSize = 15 * 1024 * 1024; // 15MB

      if (file.size > maxSize) {
        alert('File size exceeds the 15MB limit. Please upload a smaller file.');
        fileInput.value = '';
        fileLabel.textContent = 'Click or Drag & Drop File Here';
        fileDesc.textContent = 'PDF, DOCX, or PNG formats up to 15MB';
        return;
      }

      fileLabel.textContent = file.name;
      fileDesc.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';

      // Keep reference globally
      bookingState.documentFile = file;
    }
  }
}

// Global navigation handle
let goToStep = null;

// Dynamic Today Date Helper
const _todayDate = new Date();
const _initYear = _todayDate.getFullYear();
const _initMonth = _todayDate.getMonth();
const _initDay = _todayDate.getDate();
const _padZero = (n) => String(n).padStart(2, '0');
const _formattedTodayDate = `${_padZero(_initMonth + 1)}/${_padZero(_initDay)}/${_initYear}`;

// Global state
const bookingState = {
  currentStep: 1,
  selectedFacility: {
    id: 'function-hall',
    name: 'Function Hall',
    rate: '₱5,000/day',
    capacity: '200 PAX',
    type: 'halls',
    setupName: 'Theater Setup',
    setupCap: '200 PAX',
    location: 'Main Administration Building • Ground Floor'
  },
  selectedStartDate: { year: _initYear, month: _initMonth, day: _initDay },
  selectedEndDate: { year: _initYear, month: _initMonth, day: _initDay },
  selectedStartDay: _initDay,
  selectedEndDay: _initDay,
  date: _formattedTodayDate,
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
/* ==========================================================================
   1. Facility Selection & Hall Layout / Dorm Room Data (Interactive Modal)
   ========================================================================== */
const hallLayoutData = {
  'function-hall': {
    title: 'Function Hall',
    location: 'Main Administration Building • Ground Floor',
    desc: 'Flagship multi-purpose event auditorium with central aircon, stage lighting, sound system, and VIP holding lounge.',
    rate: '₱5,000 / day',
    layouts: [
      { id: 'fh-theater', name: 'Theater Setup', cap: '200 PAX', available: true, desc: 'Row seating facing main presentation stage' },
      { id: 'fh-classroom', name: 'Classroom Setup', cap: '150 PAX', available: true, desc: 'Tables and chairs facing presentation screen' },
      { id: 'fh-banquet', name: 'Banquet Dining', cap: '120 PAX', available: true, desc: 'Round tables for official catering & gatherings' },
      { id: 'fh-ushape', name: 'U-Shape Workshop', cap: '80 PAX', available: false, desc: 'Interactive workshop configuration (Book other date)' },
      { id: 'fh-conference', name: 'Conference Setup', cap: '100 PAX', available: true, desc: 'Central aisle with wide projection clearance' },
      { id: 'fh-exhibition', name: 'Open Exhibition', cap: '200 PAX', available: true, desc: 'Open booth space for agricultural displays' }
    ]
  },
  'training-hall-a': {
    title: 'Training Hall A',
    location: 'Training Center • 2nd Floor',
    desc: 'Interactive audio-visual training room tailored for workshops, seminars, and technical capacity-building.',
    rate: '₱3,000 / day',
    layouts: [
      { id: 'tha-pods', name: 'Modular Pods', cap: '60 PAX', available: true, desc: 'Collaborative small group table clusters' },
      { id: 'tha-lecture', name: 'Classroom Lecture', cap: '80 PAX', available: true, desc: 'Standard training desks with smart display focus' },
      { id: 'tha-circle', name: 'Workshop Circle', cap: '50 PAX', available: false, desc: 'Circular discussion layout (Book other date)' },
      { id: 'tha-computer', name: 'Computer Lab Work', cap: '45 PAX', available: true, desc: 'Power hubs and high-speed LAN connectivity' },
      { id: 'tha-seminar', name: 'Seminar Theater', cap: '80 PAX', available: true, desc: 'Tiered audio-visual lecture configuration' },
      { id: 'tha-breakout', name: 'Breakout Stations', cap: '50 PAX', available: true, desc: 'Individual station whiteboards & display corners' }
    ]
  },
  'mess-hall': {
    title: 'Mess Hall & Dining Area',
    location: 'Hostel & Dining Complex • Ground Floor',
    desc: 'Institutional dining facility equipped with commercial buffet counters, beverage stations, and patio deck.',
    rate: '₱3,500 / day',
    layouts: [
      { id: 'mh-buffet', name: 'Full Buffet Dining', cap: '100 PAX', available: true, desc: 'Dual-line self-service buffet and dining tables' },
      { id: 'mh-banquet', name: 'Formal Plated Service', cap: '80 PAX', available: true, desc: 'Head table VIP service with course runners' },
      { id: 'mh-cafeteria', name: 'Cafeteria Standard', cap: '100 PAX', available: true, desc: 'Long-table communal dining arrangement' },
      { id: 'mh-patio', name: 'Patio & Deck Combo', cap: '60 PAX', available: false, desc: 'Indoor-outdoor dining layout (Book other date)' },
      { id: 'mh-mixer', name: 'Cocktail & Social', cap: '100 PAX', available: true, desc: 'High-top cocktail tables and appetizer station' },
      { id: 'mh-fellowship', name: 'Fellowship Night', cap: '90 PAX', available: true, desc: 'Dinner tables with acoustic music stage setup' }
    ]
  },
  'executive-boardroom': {
    title: 'Executive Boardroom',
    location: 'Executive Wing • 3rd Floor',
    desc: 'High-level conference suite with ergonomic leather executive seating, 4K video conference bar, and acoustic walls.',
    rate: '₱2,500 / day',
    layouts: [
      { id: 'eb-board', name: 'Executive Board Table', cap: '25 PAX', available: true, desc: 'Central solid mahogany executive conference table' },
      { id: 'eb-videoconf', name: 'Hybrid Video-Conf', cap: '20 PAX', available: true, desc: 'Dual camera auto-framing & boundary mics' },
      { id: 'eb-briefing', name: 'Executive Briefing', cap: '30 PAX', available: false, desc: 'Board table with gallery seating (Book other date)' },
      { id: 'eb-hearing', name: 'Committee Hearing', cap: '24 PAX', available: true, desc: 'Presiding panel facing witness/delegate tables' },
      { id: 'eb-strategy', name: 'Closed Strategy Session', cap: '18 PAX', available: true, desc: 'Private soundproof layout with document displays' },
      { id: 'eb-delegation', name: 'Diplomatic Delegation', cap: '20 PAX', available: true, desc: 'Formal protocol seating with desk flags & mics' }
    ]
  }
};

let currentModalFacilityId = null;
let modalTempSelectedItem = null;

function selectFacilityCard(card, openModal = true) {
  if (!card) return;
  const cards = document.querySelectorAll('.facility-choice-card');
  const isDorm = card.dataset.facilityType === 'dormitories' || (card.dataset.id && card.dataset.id.startsWith('dorm-floor-'));

  cards.forEach(c => {
    c.classList.remove('selected');
    const btn = c.querySelector('.btn-select-facility');
    if (btn) {
      const isCDorm = c.dataset.facilityType === 'dormitories' || (c.dataset.id && c.dataset.id.startsWith('dorm-floor-'));
      if (isCDorm) {
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Room &amp; View Floor Plan`;
      } else {
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Setup &amp; View Floor Plan`;
      }
    }
  });

  card.classList.add('selected');

  let setupName = null;
  let setupCap = null;
  let setupId = null;
  let setupRate = null;
  let location = null;

  if (!isDorm) {
    location = hallLayoutData[card.dataset.id]?.location || null;
    setupRate = card.dataset.rate || null;
    if (bookingState.selectedFacility?.id === card.dataset.id && bookingState.selectedFacility?.setupName) {
      setupName = bookingState.selectedFacility.setupName;
      setupCap = bookingState.selectedFacility.setupCap;
      setupId = bookingState.selectedFacility.setupId;
    } else if (hallLayoutData[card.dataset.id]?.layouts?.[0]) {
      const defLayout = hallLayoutData[card.dataset.id].layouts[0];
      setupId = defLayout.id;
      setupName = defLayout.name;
      setupCap = defLayout.cap;
    }
  }

  bookingState.selectedFacility = {
    id: card.dataset.id,
    name: card.dataset.name,
    rate: card.dataset.rate,
    capacity: card.dataset.capacity,
    type: card.dataset.facilityType,
    roomNumber: isDorm ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomNumber || null) : null) : null,
    floor: isDorm ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.floor || null) : null) : null,
    roomRate: isDorm ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomRate || null) : null) : null,
    setupId: setupId,
    setupName: setupName,
    setupCap: setupCap,
    location: location,
    setupRate: setupRate,
    occupiedOnDefaultDate: bookingState.selectedFacility?.id === card.dataset.id ? Boolean(bookingState.selectedFacility?.occupiedOnDefaultDate) : false
  };

  if (!isDorm && setupName) {
    const badge = card.querySelector('.hall-selected-setup-badge');
    const badgeText = card.querySelector('.d-room-text');
    if (badge && badgeText) {
      badgeText.textContent = `✓ ${setupName} Selected (${setupCap || ''})`;
      badge.style.display = 'flex';
    }
    const btn = card.querySelector('.btn-select-facility');
    if (btn) {
      btn.innerHTML = `✓ ${setupName} Selected (Click to change)`;
    }
  }

  if (openModal) {
    openFacilityModal(card.dataset.id);
  } else {
    updateReviewSummary();
  }
}

function initFacilitySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    const selectBtn = card.querySelector('.btn-select-facility');

    if (selectBtn) {
      selectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        selectFacilityCard(card, true);
      });
    }

    card.addEventListener('click', () => {
      selectFacilityCard(card, true);
    });
  });

  // Modal event bindings
  const modalOverlay = document.getElementById('roomSelectionModal');
  const btnClose = document.getElementById('btnModalClose');
  const btnCancel = document.getElementById('btnModalCancel');
  const btnConfirm = document.getElementById('btnModalConfirm');

  if (btnClose) btnClose.addEventListener('click', closeFacilityModal);
  if (btnCancel) btnCancel.addEventListener('click', closeFacilityModal);
  if (btnConfirm) btnConfirm.addEventListener('click', () => confirmFacilitySelection(false));

  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeFacilityModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('roomSelectionModal');
      if (modal && modal.style.display !== 'none') {
        closeFacilityModal();
      }
    }
  });

  // Pre-initialize default Function Hall setup badge if Function Hall is selected
  const defaultHallCard = document.querySelector('.facility-choice-card.selected[data-id="function-hall"]');
  if (defaultHallCard) {
    const defaultLayout = hallLayoutData['function-hall'].layouts[0];
    bookingState.selectedFacility = {
      id: 'function-hall',
      name: 'Function Hall',
      rate: '₱5,000/day',
      capacity: defaultLayout.cap,
      type: 'halls',
      setupId: defaultLayout.id,
      setupName: defaultLayout.name,
      setupCap: defaultLayout.cap,
      location: hallLayoutData['function-hall'].location,
      setupRate: '₱5,000/day',
      occupiedOnDefaultDate: false
    };
    const badge = defaultHallCard.querySelector('.hall-selected-setup-badge');
    const badgeText = defaultHallCard.querySelector('.d-room-text');
    if (badge && badgeText) {
      badgeText.textContent = `✓ ${defaultLayout.name} Selected (${defaultLayout.cap})`;
      badge.style.display = 'flex';
    }
    const btn = defaultHallCard.querySelector('.btn-select-facility');
    if (btn) {
      btn.innerHTML = `✓ ${defaultLayout.name} Selected (Click to change)`;
    }
    updateReviewSummary();
  }

  // Pre-initialize default Dormitory selection if on dormitory page or dorm card is pre-selected
  const isDormPageInit = window.location.pathname.includes('dormitory_booking');
  const defaultDormCard = document.querySelector('.facility-choice-card.selected[data-facility-type="dormitories"]');
  if (defaultDormCard && (!defaultHallCard || isDormPageInit)) {
    const dormId = defaultDormCard.dataset.id;
    const dormData = dormFloorData[dormId];
    bookingState.selectedFacility = {
      id: dormId,
      name: defaultDormCard.dataset.name,
      rate: defaultDormCard.dataset.rate,
      capacity: defaultDormCard.dataset.capacity,
      type: 'dormitories',
      roomNumber: null,
      floor: dormData?.floor || defaultDormCard.dataset.name,
      roomRate: defaultDormCard.dataset.rate,
      occupiedOnDefaultDate: false
    };
    updateReviewSummary();
  }
}

function openFacilityModal(facilityId) {
  const isDorm = Boolean(dormFloorData[facilityId]);
  const isHall = Boolean(hallLayoutData[facilityId]);

  if (!isDorm && !isHall) return;

  currentModalFacilityId = facilityId;
  modalTempSelectedItem = null;

  const modal = document.getElementById('roomSelectionModal');
  const catPill = document.getElementById('modalCategoryPill');
  const titleEl = document.getElementById('modalDormTitle');
  const floorEl = document.getElementById('modalDormFloor');
  const rateEl = document.getElementById('modalDormRate');
  const gridEl = document.getElementById('modalRoomsGrid');
  const feedbackBar = document.getElementById('modalRoomFeedback');
  const confirmBtn = document.getElementById('btnModalConfirm');
  const legendAvail = document.getElementById('modalLegendAvailText');
  const legendRes = document.getElementById('modalLegendResText');

  if (feedbackBar) feedbackBar.style.display = 'none';

  if (isDorm) {
    const data = dormFloorData[facilityId];
    if (catPill) catPill.textContent = 'DORMITORY FLOOR PLAN & ROOM SELECTION';
    if (titleEl) titleEl.textContent = data.title;
    if (floorEl) floorEl.textContent = `${data.floor} • ${data.desc}`;
    if (rateEl) rateEl.textContent = data.rate;
    if (legendAvail) legendAvail.innerHTML = '<strong>Available:</strong> Click to assign for your stay';
    if (legendRes) legendRes.innerHTML = '<strong>Reserved:</strong> Occupied by scheduled delegates';

    if (gridEl) {
      gridEl.className = 'modal-rooms-grid';
      gridEl.innerHTML = '';
      const bedIconSvg = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 7v11M21 11v7M3 15h18M3 11h14a4 4 0 0 1 4 4v0M7 11V8a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3"/>
        </svg>
      `;

      data.rooms.forEach(r => {
        const box = document.createElement('div');
        box.className = `dorm-room-box ${r.available ? 'available' : 'reserved'}`;
        box.dataset.item = r.num;
        box.dataset.available = r.available ? 'true' : 'false';

        box.innerHTML = `
          <div class="dorm-bed-icon">${bedIconSvg}</div>
          <div class="dorm-room-num">${r.num}</div>
          <div class="dorm-room-status">${r.available ? 'AVAILABLE' : 'RESERVED'}</div>
          <div class="dorm-room-hint ${r.available ? 'ready' : 'alt-date'}">${r.available ? 'Ready Today' : 'Book other date'}</div>
        `;

        if (bookingState.selectedFacility?.id === facilityId && String(bookingState.selectedFacility?.roomNumber) === String(r.num)) {
          box.classList.add('selected');
          modalTempSelectedItem = r.num;
        }

        box.addEventListener('click', (e) => {
          e.stopPropagation();
          const isOcc = !r.available;
          gridEl.querySelectorAll('.dorm-room-box.selected').forEach(b => b.classList.remove('selected'));
          box.classList.add('selected');
          modalTempSelectedItem = r.num;

          if (feedbackBar) {
            feedbackBar.style.display = 'flex';
            feedbackBar.classList.toggle('alternate-date', isOcc);
            const fTitle = document.getElementById('modalFeedbackTitle');
            const fSub = document.getElementById('modalFeedbackSub');
            const badgeOk = feedbackBar.querySelector('.badge-assigned-ok');

            if (isOcc) {
              if (fTitle) fTitle.textContent = `Room ${r.num} Selected (Occupied Today)`;
              if (fSub) fSub.textContent = `${data.floor} • Proceed to Step 2 to choose an available alternate date for this room.`;
              if (badgeOk) badgeOk.textContent = '📅 Pick Alternate Date';
            } else {
              if (fTitle) fTitle.textContent = `Room ${r.num} Selected ✓`;
              if (fSub) fSub.textContent = `${data.floor} • ${data.title} (${data.rate}) • Room Assigned`;
              if (badgeOk) badgeOk.textContent = '✓ Room Confirmed';
            }
          }

          setTimeout(() => {
            confirmFacilitySelection(isOcc);
          }, 450);
        });

        gridEl.appendChild(box);
      });
    }
  } else if (isHall) {
    const data = hallLayoutData[facilityId];
    if (catPill) catPill.textContent = 'VENUE SETUP & FLOOR PLAN SELECTION';
    if (titleEl) titleEl.textContent = data.title;
    if (floorEl) floorEl.textContent = `${data.location} • ${data.desc}`;
    if (rateEl) rateEl.textContent = data.rate;
    if (legendAvail) legendAvail.innerHTML = '<strong>Available:</strong> Click to assign layout setup';
    if (legendRes) legendRes.innerHTML = '<strong>Reserved:</strong> Setup reserved for existing booking';

    if (gridEl) {
      gridEl.className = 'modal-rooms-grid hall-layout-mode';
      gridEl.innerHTML = '';

      const hallIcons = {
        'fh-theater': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M7 18h10M3 21h18"/></svg>',
        'fh-classroom': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg>',
        'fh-banquet': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/></svg>',
        'fh-ushape': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 4v11a5 5 0 0 0 10 0V4"/><rect x="3" y="3" width="4" height="3"/><rect x="13" y="3" width="4" height="3"/></svg>',
        'fh-conference': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="6" width="16" height="12" rx="2"/><circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/></svg>',
        'fh-exhibition': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>',
        'tha-pods': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
        'tha-lecture': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg>',
        'tha-circle': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>',
        'tha-computer': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'tha-seminar': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M7 18h10M3 21h18"/></svg>',
        'tha-breakout': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>',
        'mh-buffet': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
        'mh-banquet': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/></svg>',
        'mh-cafeteria': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="18" height="6" rx="1"/><rect x="3" y="15" width="18" height="3" rx="1"/></svg>',
        'mh-patio': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
        'mh-mixer': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="6 2 18 2 12 11 12 22"/><line x1="8" y1="22" x2="16" y2="22"/></svg>',
        'mh-fellowship': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polygon points="10 8 16 12 10 16 10 8"/></svg>',
        'eb-board': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="10" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'eb-videoconf': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>',
        'eb-briefing': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>',
        'eb-hearing': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>',
        'eb-strategy': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
        'eb-delegation': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>',
        'default': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>'
      };

      data.layouts.forEach(l => {
        const box = document.createElement('div');
        box.className = `dorm-room-box hall-layout-box ${l.available ? 'available' : 'reserved'}`;
        box.dataset.item = l.id;
        box.dataset.available = l.available ? 'true' : 'false';

        const iconSvg = hallIcons[l.id] || hallIcons['default'];

        box.innerHTML = `
          <div class="dorm-bed-icon">${iconSvg}</div>
          <div class="dorm-room-num" style="font-size: 1.05rem;">${l.name}</div>
          <span class="layout-cap-badge">${l.cap}</span>
          <div class="dorm-room-status">${l.available ? 'AVAILABLE' : 'RESERVED'}</div>
          <div class="dorm-room-hint ${l.available ? 'ready' : 'alt-date'}">${l.available ? 'Ready Today' : 'Book other date'}</div>
          <div class="layout-desc-text">${l.desc}</div>
        `;

        if (bookingState.selectedFacility?.id === facilityId && (bookingState.selectedFacility?.setupName === l.name || bookingState.selectedFacility?.setupId === l.id)) {
          box.classList.add('selected');
          modalTempSelectedItem = l.id;
        }

        box.addEventListener('click', (e) => {
          e.stopPropagation();
          const isOcc = !l.available;
          gridEl.querySelectorAll('.dorm-room-box.selected').forEach(b => b.classList.remove('selected'));
          box.classList.add('selected');
          modalTempSelectedItem = l.id;

          if (feedbackBar) {
            feedbackBar.style.display = 'flex';
            feedbackBar.classList.toggle('alternate-date', isOcc);
            const fTitle = document.getElementById('modalFeedbackTitle');
            const fSub = document.getElementById('modalFeedbackSub');
            const badgeOk = feedbackBar.querySelector('.badge-assigned-ok');

            if (isOcc) {
              if (fTitle) fTitle.textContent = `${l.name} Selected (Reserved Today)`;
              if (fSub) fSub.textContent = `${data.title} • Proceed to Step 2 to choose an available alternate date for this setup.`;
              if (badgeOk) badgeOk.textContent = '📅 Pick Alternate Date';
            } else {
              if (fTitle) fTitle.textContent = `${l.name} Selected ✓ (${l.cap})`;
              if (fSub) fSub.textContent = `${data.title} • ${data.location} (${data.rate}) • Layout Confirmed`;
              if (badgeOk) badgeOk.textContent = '✓ Setup Confirmed';
            }
          }

          setTimeout(() => {
            confirmFacilitySelection(isOcc);
          }, 450);
        });

        gridEl.appendChild(box);
      });
    }
  }

  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function closeFacilityModal() {
  const modal = document.getElementById('roomSelectionModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}
const closeRoomModal = closeFacilityModal;
const openRoomModal = openFacilityModal;

function confirmFacilitySelection(isOccupiedOnDefaultDate = false) {
  if (!modalTempSelectedItem || !currentModalFacilityId) return;

  const isDorm = Boolean(dormFloorData[currentModalFacilityId]);
  const isHall = Boolean(hallLayoutData[currentModalFacilityId]);

  if (isDorm) {
    const data = dormFloorData[currentModalFacilityId];
    if (!data) return;

    bookingState.selectedFacility = {
      id: currentModalFacilityId,
      name: data.title,
      rate: data.rate,
      capacity: document.querySelector(`.facility-choice-card[data-id="${currentModalFacilityId}"]`)?.dataset.capacity || '48 BEDS',
      type: 'dormitories',
      roomNumber: modalTempSelectedItem,
      floor: data.floor,
      roomRate: data.rate,
      occupiedOnDefaultDate: Boolean(isOccupiedOnDefaultDate)
    };

    const card = document.querySelector(`.facility-choice-card[data-id="${currentModalFacilityId}"]`);
    if (card) {
      document.querySelectorAll('.facility-choice-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');

      const badge = card.querySelector('.dorm-card-selected-room-badge:not(.hall-selected-setup-badge)');
      const badgeText = badge?.querySelector('.d-room-text');
      if (badge && badgeText) {
        if (isOccupiedOnDefaultDate) {
          badgeText.textContent = `✓ Room ${modalTempSelectedItem} Selected (Occupied Today • Pick other date)`;
        } else {
          badgeText.textContent = `✓ Room ${modalTempSelectedItem} Assigned (${data.floor})`;
        }
        badge.style.display = 'flex';
      }

      const btn = card.querySelector('.btn-select-facility');
      if (btn) {
        btn.innerHTML = `✓ Room ${modalTempSelectedItem} Selected (Click to change)`;
      }
    }

    const proceedBtn = document.getElementById('btnProceedStep');
    if (proceedBtn) {
      if (isOccupiedOnDefaultDate) {
        proceedBtn.innerHTML = `<span>Proceed to Choose Date (Room ${modalTempSelectedItem})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else {
        proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (Room ${modalTempSelectedItem})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      }
      proceedBtn.style.display = 'inline-flex';
    }
  } else if (isHall) {
    const data = hallLayoutData[currentModalFacilityId];
    if (!data) return;

    const layout = data.layouts.find(l => l.id === modalTempSelectedItem) || data.layouts[0];

    bookingState.selectedFacility = {
      id: currentModalFacilityId,
      name: data.title,
      rate: data.rate,
      capacity: layout.cap,
      type: 'halls',
      setupId: layout.id,
      setupName: layout.name,
      setupCap: layout.cap,
      location: data.location,
      setupRate: data.rate,
      occupiedOnDefaultDate: Boolean(isOccupiedOnDefaultDate)
    };

    const card = document.querySelector(`.facility-choice-card[data-id="${currentModalFacilityId}"]`);
    if (card) {
      document.querySelectorAll('.facility-choice-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');

      const badge = card.querySelector('.hall-selected-setup-badge');
      const badgeText = badge?.querySelector('.d-room-text');
      if (badge && badgeText) {
        if (isOccupiedOnDefaultDate) {
          badgeText.textContent = `✓ ${layout.name} Selected (Reserved Today • Pick other date)`;
        } else {
          badgeText.textContent = `✓ ${layout.name} Selected (${layout.cap})`;
        }
        badge.style.display = 'flex';
      }

      const btn = card.querySelector('.btn-select-facility');
      if (btn) {
        btn.innerHTML = `✓ ${layout.name} Selected (Click to change)`;
      }
    }

    const proceedBtn = document.getElementById('btnProceedStep');
    if (proceedBtn) {
      if (isOccupiedOnDefaultDate) {
        proceedBtn.innerHTML = `<span>Proceed to Choose Date (${layout.name})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else {
        proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (${layout.name})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      }
      proceedBtn.style.display = 'inline-flex';
    }
  }

  closeFacilityModal();
  updateReviewSummary();

  const summaryCard = document.getElementById('step1SummaryCard');
  if (summaryCard) {
    summaryCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}
const confirmRoomSelection = confirmFacilitySelection;

/* ==========================================================================
   2. Filter & Category Selection (Halls vs Dormitories)
   ========================================================================== */
function initFacilityFilters() {
  const isDormPage = window.location.pathname.includes('dormitory_booking');
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
        activeCategoryName.textContent = isDormPage ? 'Dormitories (6 Floors Available)' : 'Dormitories (6 Floors)';
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
      const isVisible = (catName === 'all' || type === catName || isDormPage);
      card.style.display = isVisible ? 'flex' : 'none';

      if (isVisible) {
        if (!firstVisibleCard) firstVisibleCard = card;
        if (card.classList.contains('selected')) {
          currentSelectedVisible = true;
        }
      }
    });

    // If current selected card is now hidden, select the first visible card quietly without opening the modal!
    if (!currentSelectedVisible && firstVisibleCard) {
      selectFacilityCard(firstVisibleCard, false);
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
  } else if (isDormPage) {
    switchCategory('dormitories', false);
  } else {
    const activePill = document.querySelector('.filter-pill.active');
    const defaultCat = activePill ? activePill.dataset.categoryFilter : 'halls';
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

  goToStep = function (stepNumber) {
    if (stepNumber < 1 || stepNumber > 5) return;

    const isDorm = isDormitorySelected();

    // Requirement: When reserving, user MUST select a room or hall setup unit before proceeding to Step 2
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      if (isDorm && !bookingState.selectedFacility?.roomNumber) {
        alert('Please choose a room unit (e.g. Room 101, 102, 103) inside your chosen dormitory floor before proceeding to Date & Time Selection.');
        const selectedCard = document.querySelector('.facility-choice-card.selected');
        if (selectedCard) {
          selectedCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return;
      }
      if (!isDorm && !bookingState.selectedFacility?.setupName) {
        alert('Please choose a venue layout & setup (e.g. Theater Setup, Classroom Setup, Banquet Dining) before proceeding to Date & Time Selection.');
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

    // When advancing to Step 2 (Date & Time Selection)
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      if (bookingState.selectedFacility?.occupiedOnDefaultDate) {
        if (typeof window.selectAlternateAvailableDateForRoom === 'function') {
          const itemLabel = isDorm ? `Room ${bookingState.selectedFacility?.roomNumber}` : bookingState.selectedFacility?.setupName;
          window.selectAlternateAvailableDateForRoom(itemLabel);
        }
      } else {
        if (typeof window.selectCurrentDate === 'function') {
          window.selectCurrentDate();
        }
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
        const hallSetup = bookingState.selectedFacility?.setupName;
        const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
        if (isDorm && dormRoom) {
          if (isOcc) {
            proceedBtn.innerHTML = `<span>Proceed to Choose Date (Room ${dormRoom})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
          } else {
            proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (Room ${dormRoom})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
          }
        } else if (!isDorm && hallSetup) {
          if (isOcc) {
            proceedBtn.innerHTML = `<span>Proceed to Choose Date (${hallSetup})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
          } else {
            proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (${hallSetup})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
          }
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
          alert('Please choose a room unit (e.g. Room 101, 102, 103) inside your chosen dormitory floor before proceeding to Date & Time Selection.');
          const card = document.querySelector('.facility-choice-card.selected');
          if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }
        if (!isDorm && !bookingState.selectedFacility?.setupName) {
          alert('Please choose a venue layout & setup (e.g. Theater Setup, Classroom Setup, Banquet Dining) before proceeding to Date & Time Selection.');
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
      const refNum = `ATI-RES-${new Date().getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
      const isDorm = isDormitorySelected();

      const formData = new FormData();
      formData.append('reference_no', refNum);
      formData.append('facility_name', bookingState.selectedFacility?.name || 'Function Hall');
      formData.append('facility_type', isDorm ? 'dormitories' : 'halls');
      formData.append('rate', bookingState.selectedFacility?.rate || 'N/A');
      formData.append('capacity', bookingState.selectedFacility?.capacity || 'N/A');
      if (bookingState.selectedFacility?.roomNumber) {
        formData.append('room_number', bookingState.selectedFacility.roomNumber);
      }
      formData.append('event_title', bookingState.eventTitle || '');
      formData.append('pax_count', bookingState.participants || '0');

      const sDate = bookingState.selectedStartDate;
      const eDate = bookingState.selectedEndDate;
      formData.append('start_date', `${sDate.year}-${String(sDate.month + 1).padStart(2, '0')}-${String(sDate.day).padStart(2, '0')}`);
      formData.append('end_date', `${eDate.year}-${String(eDate.month + 1).padStart(2, '0')}-${String(eDate.day).padStart(2, '0')}`);

      const timeSelect = document.getElementById('startTimeSelect');
      const endTimeSelect = document.getElementById('endTimeSelect');
      const timeSlot = (timeSelect ? timeSelect.value : '') + ' - ' + (endTimeSelect ? endTimeSelect.value : '');
      formData.append('time_slot', timeSlot);

      const specialNotes = document.getElementById('specialNotes') ? document.getElementById('specialNotes').value : '';
      formData.append('special_notes', specialNotes);

      if (bookingState.documentFile) {
        formData.append('document', bookingState.documentFile);
      }

      // Disable button UI state
      const origText = finalSubmitBtn.innerHTML;
      finalSubmitBtn.innerHTML = '<span>Submitting Please Wait...</span>';
      finalSubmitBtn.disabled = true;

      fetch('ajax/submit_booking.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            let summaryMsg = `Reservation Request Submitted Successfully!\n\nReference: ${refNum}\nFacility: ${bookingState.selectedFacility.name}`;
            if (bookingState.selectedFacility.roomNumber) {
              summaryMsg += `\nAssigned Room: Room ${bookingState.selectedFacility.roomNumber}`;
            }
            if (bookingState.eventTitle) {
              summaryMsg += `\nEvent / Purpose: ${bookingState.eventTitle}`;
            }
            summaryMsg += `\nStatus: Pending Administrative Review\n\nNotification has been sent to your registered email.`;
            alert(summaryMsg);
            window.location.href = 'my_reservations.php';
          } else {
            alert('Error: ' + data.message);
            finalSubmitBtn.innerHTML = origText;
            finalSubmitBtn.disabled = false;
          }
        })
        .catch(err => {
          console.error(err);
          alert('An unexpected error occurred while submitting.');
          finalSubmitBtn.innerHTML = origText;
          finalSubmitBtn.disabled = false;
        });
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
  const hallSetup = bookingState.selectedFacility?.setupName;
  const hallCap = bookingState.selectedFacility?.setupCap;
  const facilityName = bookingState.selectedFacility?.name || 'Function Hall';
  const rateText = bookingState.selectedFacility?.roomRate || bookingState.selectedFacility?.setupRate || bookingState.selectedFacility?.rate || '₱5,000/day';
  const capText = isDorm ? (bookingState.selectedFacility?.capacity || '48 BEDS') : (hallCap || bookingState.selectedFacility?.capacity || '150 - 200 PAX');

  // 1. Step 1 Summary Preview Card
  const prevBadge = document.getElementById('summaryPreviewBadge');
  const prevTitle = document.getElementById('summaryPreviewTitle');
  const prevRate = document.getElementById('summaryPreviewRate');
  const prevRoomWrap = document.getElementById('summaryPreviewRoomWrap');
  const prevRoom = document.getElementById('summaryPreviewRoom');
  const prevUnitLabel = document.getElementById('summaryPreviewUnitLabel');
  const prevCap = document.getElementById('summaryPreviewCap');

  if (prevBadge) prevBadge.textContent = isDorm ? 'DORMITORY ACCOMMODATION' : 'SELECTED VENUE';
  if (prevTitle) prevTitle.textContent = facilityName;
  if (prevRate) prevRate.textContent = rateText;
  if (prevCap) prevCap.textContent = capText;

  if (prevRoomWrap && prevRoom) {
    if (isDorm && roomNum) {
      if (prevUnitLabel) prevUnitLabel.textContent = 'Assigned Room Unit:';
      prevRoomWrap.style.display = 'flex';
      const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
      if (isOcc) {
        prevRoom.innerHTML = `Room ${roomNum} (${floorName}) <span style="display:inline-block; margin-left:6px; font-size:0.75rem; color:#dc2626; font-weight:700;">(Occupied Today • Pick alternate date in Step 2)</span>`;
      } else {
        prevRoom.textContent = `Room ${roomNum} (${floorName})`;
      }
    } else if (!isDorm && hallSetup) {
      if (prevUnitLabel) prevUnitLabel.textContent = 'Assigned Setup & Layout:';
      prevRoomWrap.style.display = 'flex';
      const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
      if (isOcc) {
        prevRoom.innerHTML = `${hallSetup} (${hallCap}) <span style="display:inline-block; margin-left:6px; font-size:0.75rem; color:#dc2626; font-weight:700;">(Reserved Today • Pick alternate date in Step 2)</span>`;
      } else {
        prevRoom.textContent = `${hallSetup} (${hallCap})`;
      }
    } else {
      prevRoomWrap.style.display = 'none';
      prevRoom.textContent = 'None';
    }
  }

  // 2. Step 5 Official Review Summary Card
  const sumBadge = document.getElementById('summaryFacilityBadge') || document.getElementById('summaryOfficialBadge');
  const sumVenue = document.getElementById('summaryVenueName');
  const sumRate = document.getElementById('summaryVenueRate');
  const sumRoomWrap = document.getElementById('summaryRoomDetailWrap');
  const sumRoom = document.getElementById('summaryRoomDetail');
  const sumRoomLabel = document.getElementById('summaryRoomDetailLabel');
  const sumFacType = document.getElementById('summaryFacilityType');
  const sumCap = document.getElementById('summaryVenueCapacity');
  const sumDate = document.getElementById('summaryReservationDate');
  const sumTime = document.getElementById('summaryTimeSlot');

  if (sumBadge) sumBadge.textContent = isDorm ? 'DORMITORY ACCOMMODATION PARTICULARS' : 'OFFICIAL VENUE RESERVATION PARTICULARS';
  if (sumVenue) sumVenue.textContent = facilityName;
  if (sumRate) sumRate.textContent = rateText;
  if (sumFacType) sumFacType.textContent = isDorm ? 'Trainee Dormitory Lodging' : 'Conference & Training Venue';
  if (sumCap) sumCap.textContent = capText;
  if (sumDate) sumDate.textContent = bookingState.date || _formattedTodayDate;
  if (sumTime) sumTime.textContent = isDorm ? 'Overnight Lodging Stay' : (bookingState.timeSlot || 'Whole Day (8:00 AM - 5:00 PM)');

  if (sumRoomWrap && sumRoom) {
    if (isDorm && roomNum) {
      if (sumRoomLabel) sumRoomLabel.textContent = 'Assigned Room Unit:';
      sumRoomWrap.style.display = 'flex';
      sumRoom.textContent = `Room ${roomNum} (${floorName})`;
    } else if (!isDorm && hallSetup) {
      if (sumRoomLabel) sumRoomLabel.textContent = 'Assigned Setup & Layout:';
      sumRoomWrap.style.display = 'flex';
      sumRoom.textContent = `${hallSetup} (${hallCap})`;
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
  const curPax = eventPaxInput?.value?.trim() || bookingState.participants || (isDorm ? '12 Trainees' : (hallCap || '120 Attendees'));

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

  const curDate = new Date();
  const TODAY = {
    year: curDate.getFullYear(),
    month: curDate.getMonth(),
    day: curDate.getDate()
  };
  let viewYear = TODAY.year;
  let viewMonth = TODAY.month;

  let selectedStart = { ...TODAY };
  let selectedEnd = { ...TODAY };
  let mode = 'single'; // 'single' or 'range'
  let rangeWaitingForEnd = false;

  const MONTH_NAMES = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  // Helper to determine if date is occupied for selected room (today & tomorrow)
  function isOccupiedRoomDate(d) {
    if (!bookingState.selectedFacility?.occupiedOnDefaultDate) return false;
    const target = new Date(d.year, d.month, d.day).getTime();
    const todayMs = new Date(TODAY.year, TODAY.month, TODAY.day).getTime();
    const dayDiff = Math.round((target - todayMs) / (1000 * 60 * 60 * 24));
    return dayDiff === 0 || dayDiff === 1;
  }

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

  // Ensure current date is never accidentally marked as reserved or suspended
  RESERVED_DATES.delete(dateToKey(TODAY.year, TODAY.month, TODAY.day));
  SUSPENDED_DATES.delete(dateToKey(TODAY.year, TODAY.month, TODAY.day));

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

    if (statusTitle) {
      if (bookingState.selectedFacility?.occupiedOnDefaultDate) {
        statusTitle.textContent = `Room ${bookingState.selectedFacility?.roomNumber || ''} Available on Selected Date!`;
      } else {
        statusTitle.textContent = 'Selected Slot Available!';
      }
    }
    if (statusDesc) {
      const roomNum = bookingState.selectedFacility?.roomNumber;
      const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
      if (isOcc && roomNum) {
        if (durationDays === 1) {
          statusDesc.textContent = `Room ${roomNum} is confirmed available on ${formatDisplayDate(selectedStart)} (occupied today and tomorrow).`;
        } else {
          statusDesc.textContent = `Room ${roomNum} is confirmed available for ${durationDays} days duration (${formatDisplayDate(selectedStart)} – ${formatDisplayDate(selectedEnd)}).`;
        }
      } else {
        if (durationDays === 1) {
          statusDesc.textContent = `No venue conflicts detected for 1 day duration (${formatDisplayDate(selectedStart)}).`;
        } else {
          statusDesc.textContent = `No venue conflicts detected for ${durationDays} days duration (${formatDisplayDate(selectedStart)} – ${formatDisplayDate(selectedEnd)}).`;
        }
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

    const isRoomOccupiedThisDate = isOccupiedRoomDate(dateObj);
    if (btn.classList.contains('reserved') || RESERVED_DATES.has(key) || isRoomOccupiedThisDate) {
      if (isRoomOccupiedThisDate) {
        const roomNum = bookingState.selectedFacility?.roomNumber;
        alert(`Room ${roomNum} is occupied on ${formatDisplayDate(dateObj)}.\n\nPlease select an available green slot to book this room.`);
      } else {
        alert(`${formatDisplayDate(dateObj)} is already reserved for another official event. Please select an available green slot.`);
      }
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
            conflict = `${formatDisplayDate({ year: cY, month: cM, day: cD })} has already passed`;
            break;
          }
          const isRoomOccupiedRangeDate = isOccupiedRoomDate({ year: cY, month: cM, day: cD });
          if (RESERVED_DATES.has(cKey) || isRoomOccupiedRangeDate) {
            conflict = isRoomOccupiedRangeDate
              ? `Room ${bookingState.selectedFacility?.roomNumber || ''} is occupied on ${formatDisplayDate({ year: cY, month: cM, day: cD })}`
              : `${formatDisplayDate({ year: cY, month: cM, day: cD })} is reserved for another event`;
            break;
          }
          if (SUSPENDED_DATES.has(cKey)) {
            conflict = `${formatDisplayDate({ year: cY, month: cM, day: cD })} is suspended for facility maintenance`;
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
      const isRoomOccupiedThisDate = isOccupiedRoomDate(dateObj);
      const isReserved = RESERVED_DATES.has(key) || isRoomOccupiedThisDate;
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
        if (isRoomOccupiedThisDate) {
          btn.title = `Room ${bookingState.selectedFacility?.roomNumber || ''} is occupied on this date`;
        } else {
          btn.title = 'Reserved for Official Event';
        }
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

    // Trailing spacer slots
    const totalRendered = firstDayOfWeek + totalDaysInMonth;
    const remainingDays = (7 - (totalRendered % 7)) % 7;
    for (let i = 0; i < remainingDays; i++) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'slot-day-btn empty';
      grid.appendChild(emptyDiv);
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

  // Explicit helper to select current date (Today)
  window.selectCurrentDate = function () {
    const freshNow = new Date();
    TODAY.year = freshNow.getFullYear();
    TODAY.month = freshNow.getMonth();
    TODAY.day = freshNow.getDate();

    viewYear = TODAY.year;
    viewMonth = TODAY.month;
    selectedStart = { ...TODAY };
    selectedEnd = { ...TODAY };
    mode = 'single';
    rangeWaitingForEnd = false;
    if (btnModeSingle) btnModeSingle.classList.add('active');
    if (btnModeRange) btnModeRange.classList.remove('active');
    if (endDateWrap) endDateWrap.classList.remove('active-focus');
    updateMonthHeader();
    renderCalendarGrid();
    renderSelectionDetails();
  };

  // Helper when user chose an occupied room: pre-select earliest open date (Today + 2 days)
  window.selectAlternateAvailableDateForRoom = function (roomNumber) {
    const cur = new Date();
    const target = new Date(cur.getFullYear(), cur.getMonth(), cur.getDate() + 2);
    viewYear = target.getFullYear();
    viewMonth = target.getMonth();
    selectedStart = { year: viewYear, month: viewMonth, day: target.getDate() };
    selectedEnd = { ...selectedStart };
    mode = 'single';
    rangeWaitingForEnd = false;
    if (btnModeSingle) btnModeSingle.classList.add('active');
    if (btnModeRange) btnModeRange.classList.remove('active');
    if (endDateWrap) endDateWrap.classList.remove('active-focus');
    updateMonthHeader();
    renderCalendarGrid();
    renderSelectionDetails();

    if (statusTitle) {
      statusTitle.textContent = `Room ${roomNumber} Available (${formatDisplayDate(selectedStart)})`;
    }
    if (statusDesc) {
      statusDesc.textContent = `Room ${roomNumber} is occupied today and tomorrow. ${formatDisplayDate(selectedStart)} has been automatically selected as the earliest open date. You can choose any upcoming green slot on the calendar.`;
    }
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

  syncGlobalProfileHeader();

  if (!badge || !dropdown) return;

  badge.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.classList.toggle('show');
  });

  document.addEventListener('click', () => {
    dropdown.classList.remove('show');
  });
}

function syncGlobalProfileHeader() {
  const avatar = localStorage.getItem('ati_user_avatar');
  const profile = localStorage.getItem('ati_user_profile');
  if (avatar) {
    document.querySelectorAll('.user-avatar-circle, .drawer-avatar').forEach(c => {
      c.innerHTML = `<img src="${avatar}" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
    });
  }
  if (profile) {
    try {
      const p = JSON.parse(profile);
      if (p.fullName) {
        document.querySelectorAll('.user-name, .dropdown-user-name, .drawer-profile-info h5').forEach(el => el.textContent = p.fullName);
      }
    } catch (e) { }
  }
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
