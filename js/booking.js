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
  initCustomDeptDropdown();
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

      // Clear any validation error styling
      dropZone.style.borderColor = '#2e7d32';
      dropZone.style.backgroundColor = '#f0fdf4';
      const docErr = document.getElementById('documentUploadError');
      if (docErr) docErr.style.display = 'none';
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
  selectedFacility: null,
  selectedStartDate: { year: _initYear, month: _initMonth, day: _initDay },
  selectedEndDate: { year: _initYear, month: _initMonth, day: _initDay },
  selectedStartDay: _initDay,
  selectedEndDay: _initDay,
  date: _formattedTodayDate,
  timeSlot: 'Whole Day (8:00 AM - 5:00 PM)',
  eventTitle: '',
  participants: '',
  eventType: 'Official Training',
  division: 'Career Development Division (CDD)'
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
    title: 'Suite 1',
    floor: '2nd Floor',
    desc: 'Trainee dormitory suite located on the 2nd floor with 3 single beds, air conditioning, lockers, and study desk.',
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
    title: 'Suite 2',
    floor: '3rd Floor',
    desc: 'Trainee dormitory suite located on the 3rd floor with 4 single beds, air conditioning, lockers, and study desk.',
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
    title: 'VIP Suite',
    floor: '4th Floor',
    desc: 'Executive VIP Suite located on the 4th floor with Queen bed, wood panel accents, mini refrigerator, smart TV, and executive amenities.',
    rate: '₱1,500 / night',
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

function scrollScreenAllTheWayDown() {
  const scrollToAbsoluteBottom = () => {
    const maxScroll = Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight,
      document.body.offsetHeight,
      document.documentElement.offsetHeight
    );
    window.scrollTo({
      top: maxScroll,
      behavior: 'smooth'
    });
  };

  // Immediate scroll call plus layout reflow follow-ups
  setTimeout(scrollToAbsoluteBottom, 60);
  setTimeout(scrollToAbsoluteBottom, 200);
  setTimeout(scrollToAbsoluteBottom, 400);
}

function selectFacilityCard(card, openModal = true) {
  if (!card) return;
  if (card.dataset.status === 'maintenance' || card.classList.contains('under-maintenance')) {
    if (typeof showToast === 'function') {
      showToast('This facility is currently under maintenance and cannot be selected.', 'warning');
    }
    return;
  }
  const cards = document.querySelectorAll('.facility-choice-card');
  const isDorm = card.dataset.facilityType === 'dormitories' || (card.dataset.id && card.dataset.id.startsWith('dorm-floor-'));
  const isGuesthouse = card.dataset.facilityType === 'guesthouses' || (card.dataset.id && card.dataset.id.startsWith('guesthouse-'));

  cards.forEach(c => {
    c.classList.remove('selected');
    const badge = c.querySelector('.dorm-card-selected-room-badge');
    if (badge) badge.style.display = 'none';
    const btn = c.querySelector('.btn-select-facility');
    if (btn) {
      const isCDorm = c.dataset.facilityType === 'dormitories' || (c.dataset.id && c.dataset.id.startsWith('dorm-floor-'));
      const isCGuest = c.dataset.facilityType === 'guesthouses' || (c.dataset.id && c.dataset.id.startsWith('guesthouse-'));
      if (isCDorm) {
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Room &amp; View Floor Plan`;
      } else if (isCGuest) {
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Villa &amp; View Layout`;
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

  if (!isDorm && !isGuesthouse) {
    location = hallLayoutData[card.dataset.id]?.location || 'ATI Institutional Complex';
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
    } else {
      setupId = 'standard';
      setupName = 'Standard Configuration';
      setupCap = card.dataset.capacity || 'Standard Capacity';
    }
  }

  bookingState.selectedFacility = {
    id: card.dataset.id,
    dbId: card.dataset.facilityDbId || (card.dataset.id ? String(card.dataset.id).replace(/\D/g, '') : card.dataset.id),
    name: card.dataset.name,
    rate: card.dataset.rate,
    capacity: card.dataset.capacity,
    type: card.dataset.facilityType,
    roomNumber: (isDorm || isGuesthouse) ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomNumber || null) : null) : null,
    floor: (isDorm || isGuesthouse) ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.floor || null) : null) : null,
    roomRate: (isDorm || isGuesthouse) ? (bookingState.selectedFacility?.id === card.dataset.id ? (bookingState.selectedFacility?.roomRate || null) : null) : null,
    setupId: setupId,
    setupName: setupName,
    setupCap: setupCap,
    location: location,
    setupRate: setupRate,
    occupiedOnDefaultDate: false
  };

  if (!isDorm && !isGuesthouse && setupName) {
    const btn = card.querySelector('.btn-select-facility');
    if (btn) {
      btn.innerHTML = `✓ ${setupName} Selected (Click to change)`;
    }
  }

  if (openModal) {
    openFacilityModal(card.dataset.id);
  } else {
    updateReviewSummary();
    if (typeof window.refreshLiveAvailabilityForCurrentFacility === 'function') {
      window.refreshLiveAvailabilityForCurrentFacility();
    }
    scrollScreenAllTheWayDown();
  }
}

function initFacilitySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    const isMaint = card.dataset.status === 'maintenance' || card.classList.contains('under-maintenance');
    const selectBtn = card.querySelector('.btn-select-facility');

    if (selectBtn) {
      selectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (isMaint) {
          if (typeof showToast === 'function') {
            showToast('This facility is currently under maintenance and cannot be selected.', 'warning');
          }
          return;
        }
        openFacilityModal(card.dataset.id);
      });
    }

    card.addEventListener('click', () => {
      if (isMaint) {
        if (typeof showToast === 'function') {
          showToast('This facility is currently under maintenance and cannot be selected.', 'warning');
        }
        return;
      }
      openFacilityModal(card.dataset.id);
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

  // Dynamically initialize only if a card was explicitly marked as selected in HTML
  const initialCard = document.querySelector('.facility-choice-card.selected:not(.under-maintenance):not([data-status="maintenance"])');
  if (initialCard) {
    selectFacilityCard(initialCard, false);
  } else {
    const step1Summary = document.getElementById('step1SummaryCard');
    const proceedBtn = document.getElementById('btnProceedStep');
    if (step1Summary) step1Summary.style.display = 'none';
    if (proceedBtn) proceedBtn.style.display = 'none';
  }
}

const guesthouseDetailsMap = {
  17: { badge: 'Presidential Villa', specs: 'Master Suite & Deluxe Quarters • Hot & Cold Shower • Garden Compound' },
  18: { badge: "Director's Lodge", specs: 'Senior Executive Veranda & Suite • Study Desk • VIP Delegation Wing' },
  19: { badge: 'Senior Cottage', specs: 'Garden Grove Cottage • Climate Control • Private Dining Nook' },
  20: { badge: 'Resource Residence', specs: 'Visiting Specialist Accommodations • Quiet Secluded Enclave' },
  21: { badge: 'Delegates Home', specs: 'Dual Private Bedrooms • Living Hall & Kitchenette • Airconditioned' },
  22: { badge: 'Fellows Lodge', specs: 'Twin Bed Quarters • Writing Desks • Botanical Landscape Views' },
  23: { badge: 'Lecturer Villa', specs: 'Queen Executive Suite • Garden Surroundings • Hospitality Amenities' }
};

function getGuesthouseInfo(facilityId, title, rate) {
  const m = String(facilityId).match(/\d+/);
  let id = m ? parseInt(m[0], 10) : 1;
  if (id >= 1 && id <= 7) id = 16 + id;
  return guesthouseDetailsMap[id] || {
    badge: `${title || 'ATI Guesthouse'} Villa`,
    specs: `Private Executive Residence • ${rate || 'Standard Rate'} • Scenic Compound Grounds`
  };
}

function renderDormRoomsInModal(facilityId, data, roomsList) {
  const gridEl = document.getElementById('modalRoomsGrid');
  const feedbackBar = document.getElementById('modalRoomFeedback');
  if (!gridEl) return;

  const isVilla = Boolean(data.isGuesthouse || String(facilityId).startsWith('guesthouse-'));
  gridEl.className = isVilla ? 'modal-rooms-grid guesthouse-mode' : 'modal-rooms-grid';
  gridEl.innerHTML = '';

  if (!roomsList || roomsList.length === 0) {
    gridEl.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem 1rem; background: #fbfdfc; border: 1.5px dashed #cde2d5; border-radius: 12px; color: #637f6f;">
        <p style="margin: 0 0 0.4rem 0; font-weight: 700; color: #174d2f;">No rooms currently registered for this ${isVilla ? 'guesthouse villa' : 'dormitory'}.</p>
        <p style="margin: 0; font-size: 0.82rem;">An administrator can configure rooms in the Facilities Master Registry.</p>
      </div>
    `;
    return;
  }

  const bedIconSvg = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 7v11M21 11v7M3 15h18M3 11h14a4 4 0 0 1 4 4v0M7 11V8a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3"/>
    </svg>
  `;

  const villaIconSvg = `
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
      <polyline points="9 22 9 12 15 12 15 22"/>
    </svg>
  `;

  roomsList.forEach(r => {
    const isMaint = (r.status === 'maintenance') || (r.is_maintenance === true);
    const isAvail = !isMaint && (r.available === true || r.status === 'available');
    const isOcc = !isMaint && !isAvail;

    const box = document.createElement('div');
    const stateClass = isMaint ? 'maintenance' : (isAvail ? 'available' : 'reserved');
    box.className = `dorm-room-box ${isVilla ? 'guesthouse-unit-box' : ''} ${stateClass}`;
    box.dataset.item = r.num;
    box.dataset.available = isAvail ? 'true' : 'false';
    box.dataset.maintenance = isMaint ? 'true' : 'false';

    let statusText = 'AVAILABLE';
    let hintText = 'Ready Today';
    let hintClass = 'ready';

    if (isMaint) {
      statusText = 'MAINTENANCE';
      hintText = 'Under Maintenance';
      hintClass = 'maintenance-hold';
    } else if (isOcc) {
      statusText = 'RESERVED';
      hintText = 'Book other date';
      hintClass = 'alt-date';
    }

    let displayTitle = r.num;
    let subtitleTag = '';
    if (isVilla) {
      const parenMatch = r.num.match(/^(.*?)\s*\((.*?)\)$/);
      if (parenMatch) {
        displayTitle = parenMatch[1];
        subtitleTag = parenMatch[2];
      } else if (r.num.includes('Full Villa') || r.num.includes('Full Lodge') || r.num.includes('Full Cottage') || r.num.includes('Full Residence')) {
        const parts = r.num.split('-');
        displayTitle = parts[parts.length - 1];
        subtitleTag = 'Entire Property';
      }
    }

    box.innerHTML = `
      <div class="dorm-bed-icon">${isVilla ? villaIconSvg : bedIconSvg}</div>
      <div class="dorm-room-num">${displayTitle}</div>
      ${subtitleTag ? `<div class="guesthouse-suite-tag">${subtitleTag}</div>` : ''}
      ${isVilla && r.floor ? `<div class="guesthouse-wing-desc">${r.floor}</div>` : ''}
      <div class="dorm-room-status">${statusText}</div>
      <div class="dorm-room-hint ${hintClass}">${hintText}</div>
    `;

    if (bookingState.selectedFacility?.id === facilityId && String(bookingState.selectedFacility?.roomNumber) === String(r.num)) {
      box.classList.add('selected');
      modalTempSelectedItem = r.num;
    }

    box.addEventListener('click', (e) => {
      e.stopPropagation();

      // BLOCK IF ROOM IS UNDER MAINTENANCE
      if (isMaint) {
        if (feedbackBar) {
          feedbackBar.style.display = 'flex';
          feedbackBar.className = 'modal-room-feedback-bar alternate-date';
          const fTitle = document.getElementById('modalFeedbackTitle');
          const fSub = document.getElementById('modalFeedbackSub');
          const badgeOk = feedbackBar.querySelector('.badge-assigned-ok');
          if (fTitle) fTitle.textContent = `${isVilla ? 'Villa Unit' : 'Room'} ${displayTitle} is Under Maintenance`;
          if (fSub) fSub.textContent = `This ${isVilla ? 'villa unit' : 'room'} is temporarily on maintenance hold. Please choose another available unit.`;
          if (badgeOk) badgeOk.textContent = '⛔ Unavailable';
        }
        return;
      }

      gridEl.querySelectorAll('.dorm-room-box.selected').forEach(b => b.classList.remove('selected'));
      box.classList.add('selected');
      modalTempSelectedItem = r.num;

      if (feedbackBar) {
        feedbackBar.style.display = 'flex';
        feedbackBar.classList.toggle('alternate-date', isOcc);
        const fTitle = document.getElementById('modalFeedbackTitle');
        const fSub = document.getElementById('modalFeedbackSub');
        const badgeOk = feedbackBar.querySelector('.badge-assigned-ok');

        const unitLabel = isVilla ? `${displayTitle}${subtitleTag ? ' (' + subtitleTag + ')' : ''}` : `Room ${r.num}`;
        if (isOcc) {
          if (fTitle) fTitle.textContent = `${unitLabel} Selected (Occupied Today)`;
          if (fSub) fSub.textContent = `${r.floor || data.floor} • Proceed to Step 2 to choose an available alternate date for this selection.`;
          if (badgeOk) badgeOk.textContent = '📅 Pick Alternate Date';
        } else {
          if (fTitle) fTitle.textContent = `${unitLabel} Selected ✓`;
          if (fSub) fSub.textContent = `${r.floor || data.floor} • ${data.title} (${data.rate}) • Unit Assigned`;
          if (badgeOk) badgeOk.textContent = isVilla ? '✓ Villa Stay Confirmed' : '✓ Room Confirmed';
        }
      }

      setTimeout(() => {
        confirmFacilitySelection(isOcc);
      }, 450);
    });

    gridEl.appendChild(box);
  });
}

function openFacilityModal(facilityId) {
  const card = document.querySelector(`.facility-choice-card[data-id="${facilityId}"]`);
  const dbFacilityId = card?.dataset.facilityDbId || String(facilityId).replace(/\D/g, '') || facilityId;

  // Load admin-configured custom layouts or auto-generate for custom/newly registered facilities
  if (card && card.dataset.facilityType !== 'dormitories' && card.dataset.facilityType !== 'guesthouses' && card.dataset.layouts) {
    try {
      const parsed = JSON.parse(card.dataset.layouts);
      if (Array.isArray(parsed) && parsed.length > 0) {
        hallLayoutData[facilityId] = {
          title: card.dataset.name || 'Institutional Hall',
          location: 'ATI Campus Complex',
          desc: `Institutional facility equipped for official events, seminars, and meetings. Capacity: ${card.dataset.capacity || '50 PAX'}.`,
          rate: card.dataset.rate || '₱3,000 / day',
          layouts: parsed
        };
      }
    } catch (e) { }
  }

  if (!dormFloorData[facilityId] && !hallLayoutData[facilityId] && card) {
    const isDormType = card.dataset.facilityType === 'dormitories';
    const isGuesthouseType = card.dataset.facilityType === 'guesthouses' || String(facilityId).startsWith('guesthouse-');
    if (isDormType) {
      const dormName = card.dataset.name || 'Dormitory Suite';
      dormFloorData[facilityId] = {
        title: dormName,
        floor: 'Institutional Dormitory Wing',
        desc: 'Air-conditioned rooms with single beds, personal lockers, and comfortable amenities.',
        rate: card.dataset.rate || '₱500 / night',
        rooms: [],
        isDbLoaded: false,
        isGuesthouse: false
      };
    } else if (isGuesthouseType) {
      const villaName = card.dataset.name || 'Guesthouse Villa';
      const capVal = card.dataset.capacity || '4 PAX';
      const rateVal = card.dataset.rate || '₱2,000 / night';
      dormFloorData[facilityId] = {
        title: villaName,
        floor: 'ATI Residential Compound • Guesthouse Villa',
        desc: `Private executive residential villa with master bedroom, hot & cold bath, workstation, and serene garden compound views. Capacity: ${capVal}.`,
        rate: rateVal,
        rooms: [],
        isDbLoaded: false,
        isGuesthouse: true
      };
    } else {
      const hallName = card.dataset.name || 'Institutional Hall';
      const capVal = card.dataset.capacity || '100 PAX';
      hallLayoutData[facilityId] = {
        title: hallName,
        location: 'ATI Campus Complex',
        desc: `Institutional facility equipped for official events, seminars, and meetings. Capacity: ${capVal}.`,
        rate: card.dataset.rate || '₱3,000 / day',
        layouts: [
          { id: `${facilityId}-theater`, name: 'Theater Setup', cap: capVal, available: true, desc: 'Row seating facing main presentation screen' },
          { id: `${facilityId}-classroom`, name: 'Classroom Setup', cap: capVal, available: true, desc: 'Desks and chairs configuration for workshops' },
          { id: `${facilityId}-workshop`, name: 'Workshop Pods', cap: capVal, available: true, desc: 'Collaborative group table arrangements' },
          { id: `${facilityId}-banquet`, name: 'Banquet Dining', cap: capVal, available: true, desc: 'Round dining tables for official gatherings' }
        ]
      };
    }
  }

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

  const heroCard = document.getElementById('modalGuesthouseHeroCard');
  const heroImg = document.getElementById('modalGuesthouseHeroImg');
  const heroBadge = document.getElementById('modalGuesthouseBadgeText');
  const heroSpecs = document.getElementById('modalGuesthouseSpecs');

  if (feedbackBar) feedbackBar.style.display = 'none';

  if (isDorm) {
    const data = dormFloorData[facilityId];
    const isVilla = Boolean(data.isGuesthouse || (card && card.dataset.facilityType === 'guesthouses') || String(facilityId).startsWith('guesthouse-'));

    if (isVilla) {
      if (heroCard) heroCard.style.display = 'block';
      if (heroImg) heroImg.src = 'assets/images/guesthouse.jpg';
      const ghInfo = getGuesthouseInfo(facilityId, data.title, data.rate);
      if (heroBadge) heroBadge.textContent = ghInfo.badge;
      if (heroSpecs) heroSpecs.textContent = ghInfo.specs;

      if (catPill) catPill.textContent = 'GUESTHOUSE VILLA ALLOCATION & ROOM SELECTION';
      if (titleEl) titleEl.textContent = data.title;
      if (floorEl) floorEl.textContent = `${data.floor} • ${data.desc}`;
      if (rateEl) rateEl.textContent = data.rate;
      if (legendAvail) legendAvail.innerHTML = '<strong>Available:</strong> Click to assign villa room/suite';
      if (legendRes) legendRes.innerHTML = '<strong>Reserved:</strong> Occupied by scheduled guests';
    } else {
      if (heroCard) heroCard.style.display = 'none';
      if (catPill) catPill.textContent = 'DORMITORY FLOOR PLAN & ROOM SELECTION';
      if (titleEl) titleEl.textContent = data.title;
      if (floorEl) floorEl.textContent = `${data.floor} • ${data.desc}`;
      if (rateEl) rateEl.textContent = data.rate;
      if (legendAvail) legendAvail.innerHTML = '<strong>Available:</strong> Click to assign for your stay';
      if (legendRes) legendRes.innerHTML = '<strong>Reserved:</strong> Occupied by scheduled delegates';
    }

    // Show loading skeleton while fetching real room inventory from DB
    if (gridEl) {
      gridEl.className = isVilla ? 'modal-rooms-grid guesthouse-mode' : 'modal-rooms-grid';
      gridEl.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem; color: #637f6f;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-bottom: 0.5rem;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg>
          <div style="font-size: 0.9rem; font-weight: 600;">Checking ${isVilla ? 'villa room/suite' : 'room'} availability...</div>
        </div>
      `;
    }

    // Fetch dynamic live rooms from MySQL Database
    const dateParam = bookingState.date ? `&date=${encodeURIComponent(bookingState.date)}` : '';
    fetch(`ajax/get_facility_rooms.php?facility_id=${encodeURIComponent(dbFacilityId)}${dateParam}`)
      .then(res => res.json())
      .then(resData => {
        if (resData.success && Array.isArray(resData.rooms)) {
          data.rooms = resData.rooms;
          data.isDbLoaded = true;
          if (resData.facility?.name) {
            data.title = resData.facility.name;
            if (titleEl) titleEl.textContent = data.title;
          }
          if (currentModalFacilityId === facilityId) {
            renderDormRoomsInModal(facilityId, data, resData.rooms);
          }
        } else {
          if (gridEl) {
            gridEl.innerHTML = `
              <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
                ${resData.message || 'Unable to retrieve room availability.'}
              </div>
            `;
          }
        }
      })
      .catch(e => {
        console.warn('Live room fetch notice:', e);
        if (gridEl) {
          gridEl.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 2rem 1rem; color: #dc2626;">
              A network connection error occurred. Please try again.
            </div>
          `;
        }
      });
  } else if (isHall) {
    if (heroCard) heroCard.style.display = 'none';
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
  currentModalFacilityId = null;
  modalTempSelectedItem = null;
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

    const card = document.querySelector(`.facility-choice-card[data-id="${currentModalFacilityId}"]`);
    const dbFacilityId = card?.dataset.facilityDbId || (currentModalFacilityId ? String(currentModalFacilityId).replace(/\D/g, '') : currentModalFacilityId);
    const isGuesthouse = Boolean(data.isGuesthouse || card?.dataset.facilityType === 'guesthouses' || String(currentModalFacilityId).startsWith('guesthouse-'));

    bookingState.selectedFacility = {
      id: currentModalFacilityId,
      dbId: dbFacilityId,
      name: data.title,
      rate: data.rate,
      capacity: card?.dataset.capacity || (isGuesthouse ? '4 PAX' : '48 BEDS'),
      type: isGuesthouse ? 'guesthouses' : 'dormitories',
      roomNumber: modalTempSelectedItem,
      floor: data.floor,
      roomRate: data.rate,
      occupiedOnDefaultDate: Boolean(isOccupiedOnDefaultDate)
    };

    if (card) {
      document.querySelectorAll('.facility-choice-card').forEach(c => {
        c.classList.remove('selected');
        const b = c.querySelector('.dorm-card-selected-room-badge');
        if (b) b.style.display = 'none';
        const selectBtn = c.querySelector('.btn-select-facility');
        if (selectBtn) {
          const isCDorm = c.dataset.facilityType === 'dormitories' || (c.dataset.id && c.dataset.id.startsWith('dorm-floor-'));
          const isCGuest = c.dataset.facilityType === 'guesthouses' || (c.dataset.id && c.dataset.id.startsWith('guesthouse-'));
          if (isCDorm) {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Room &amp; View Floor Plan`;
          } else if (isCGuest) {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Villa &amp; View Layout`;
          } else {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Setup &amp; View Floor Plan`;
          }
        }
      });
      card.classList.add('selected');

      const badge = card.querySelector('.dorm-card-selected-room-badge');
      const badgeText = badge?.querySelector('.d-room-text');
      if (badge && badgeText) {
        if (isOccupiedOnDefaultDate) {
          badgeText.textContent = `✓ ${modalTempSelectedItem} Selected (Occupied Today • Pick other date)`;
        } else {
          badgeText.textContent = `✓ ${modalTempSelectedItem} Assigned (${data.floor})`;
        }
        badge.style.display = 'flex';
      }

      const btn = card.querySelector('.btn-select-facility');
      if (btn) {
        btn.innerHTML = `✓ ${modalTempSelectedItem} Selected (Click to change)`;
      }
    }

    const proceedBtn = document.getElementById('btnProceedStep');
    if (proceedBtn) {
      if (isOccupiedOnDefaultDate) {
        proceedBtn.innerHTML = `<span>Proceed to Choose Date (${modalTempSelectedItem})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else {
        proceedBtn.innerHTML = `<span>Proceed to Date &amp; Time (${modalTempSelectedItem})</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      }
      proceedBtn.style.display = 'inline-flex';
    }
  } else if (isHall) {
    const data = hallLayoutData[currentModalFacilityId];
    if (!data) return;

    const layout = data.layouts.find(l => l.id === modalTempSelectedItem) || data.layouts[0];
    const card = document.querySelector(`.facility-choice-card[data-id="${currentModalFacilityId}"]`);
    const dbFacilityId = card?.dataset.facilityDbId || (currentModalFacilityId ? String(currentModalFacilityId).replace(/\D/g, '') : currentModalFacilityId);

    bookingState.selectedFacility = {
      id: currentModalFacilityId,
      dbId: dbFacilityId,
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

    if (card) {
      document.querySelectorAll('.facility-choice-card').forEach(c => {
        c.classList.remove('selected');
        const b = c.querySelector('.dorm-card-selected-room-badge');
        if (b) b.style.display = 'none';
        const selectBtn = c.querySelector('.btn-select-facility');
        if (selectBtn) {
          const isCDorm = c.dataset.facilityType === 'dormitories' || (c.dataset.id && c.dataset.id.startsWith('dorm-floor-'));
          const isCGuest = c.dataset.facilityType === 'guesthouses' || (c.dataset.id && c.dataset.id.startsWith('guesthouse-'));
          if (isCDorm) {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Room &amp; View Floor Plan`;
          } else if (isCGuest) {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Villa &amp; View Layout`;
          } else {
            selectBtn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Setup &amp; View Floor Plan`;
          }
        }
      });
      card.classList.add('selected');

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
  if (typeof window.refreshLiveAvailabilityForCurrentFacility === 'function') {
    window.refreshLiveAvailabilityForCurrentFacility();
  }

  scrollScreenAllTheWayDown();
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

    // 2. Update Filter Pills and Category Switcher Tabs
    filterPills.forEach(pill => {
      pill.classList.toggle('active', pill.dataset.categoryFilter === catName);
    });
    document.querySelectorAll('.category-pill-tab').forEach(tab => {
      const tabCat = tab.dataset.category || (tab.classList.contains('tab-guesthouses') ? 'guesthouses' : (tab.classList.contains('tab-dorms') ? 'dormitories' : 'halls'));
      const isTarget = (tabCat === catName);
      tab.classList.toggle('active', isTarget);
      tab.setAttribute('aria-selected', isTarget ? 'true' : 'false');
    });

    // 3. Show/Hide Matching Facility Cards and count visible
    let visibleCount = 0;
    let firstVisibleCard = null;
    let currentSelectedVisible = false;

    facilityCards.forEach(card => {
      const type = card.dataset.facilityType;
      const isVisible = (catName === 'all' || type === catName);
      card.style.display = isVisible ? 'flex' : 'none';

      if (isVisible) {
        visibleCount++;
        if (!firstVisibleCard) firstVisibleCard = card;
        if (card.classList.contains('selected')) {
          currentSelectedVisible = true;
        }
      }
    });

    // 4. Update Heading & Badge Indicator (Dynamic counts)
    if (activeCategoryName) {
      if (catName === 'halls') {
        activeCategoryName.textContent = `Halls (${visibleCount} Available)`;
      } else if (catName === 'dormitories') {
        activeCategoryName.textContent = `Dormitories (${visibleCount} Suites Available)`;
      } else if (catName === 'guesthouses') {
        activeCategoryName.textContent = `Guesthouses (${visibleCount} Available)`;
      } else {
        activeCategoryName.textContent = `All Facilities (${visibleCount} Total)`;
      }
    }

    if (sectionHeading) {
      if (catName === 'halls') {
        sectionHeading.textContent = `AVAILABLE HALLS & VENUES (${visibleCount}):`;
      } else if (catName === 'guesthouses') {
        sectionHeading.textContent = `AVAILABLE GUESTHOUSES (${visibleCount}):`;
      } else if (catName === 'dormitories') {
        sectionHeading.textContent = `AVAILABLE DORMITORY SUITES (${visibleCount}):`;
      } else {
        sectionHeading.textContent = `ALL AVAILABLE FACILITIES & ROOMS (${visibleCount}):`;
      }
    }

    const emptyState = document.getElementById('bookingEmptyState');
    const emptyTitle = document.getElementById('bookingEmptyTitle');
    const emptyDesc = document.getElementById('bookingEmptyDesc');
    const step1Summary = document.getElementById('step1SummaryCard');
    const proceedBtn = document.getElementById('btnProceedStep');

    if (emptyState) {
      if (visibleCount === 0) {
        emptyState.style.display = 'block';
        if (emptyTitle) {
          if (catName === 'halls') {
            emptyTitle.textContent = 'No Halls or Venues Available';
          } else if (catName === 'guesthouses') {
            emptyTitle.textContent = 'No Guesthouses Available';
          } else if (catName === 'dormitories') {
            emptyTitle.textContent = 'No Dormitory Suites Available';
          } else {
            emptyTitle.textContent = 'No Facilities Available';
          }
        }
        if (emptyDesc) {
          if (catName === 'halls') {
            emptyDesc.textContent = 'There are currently no training halls or function venues available under this category. Try switching to Dormitories or Guesthouses.';
          } else if (catName === 'guesthouses') {
            emptyDesc.textContent = 'There are currently no executive guesthouses available under this category. Try switching to Halls or Dormitories.';
          } else if (catName === 'dormitories') {
            emptyDesc.textContent = 'There are currently no dormitory suites or rooms available under this category. Try switching to Halls or Guesthouses.';
          } else {
            emptyDesc.textContent = 'There are currently no active facilities or rooms listed in the system. Please check back later or contact administration.';
          }
        }
        if (step1Summary) step1Summary.style.display = 'none';
        if (proceedBtn) proceedBtn.style.display = 'none';
      } else {
        emptyState.style.display = 'none';
        const hasSelected = Boolean(bookingState.selectedFacility && bookingState.selectedFacility.id && document.querySelector('.facility-choice-card.selected'));
        if (step1Summary) step1Summary.style.display = hasSelected ? 'block' : 'none';
        if (proceedBtn) proceedBtn.style.display = hasSelected ? 'inline-flex' : 'none';
      }
    }

    // If current selected card is now hidden, select the first visible card only if a facility was already selected
    if (!currentSelectedVisible && firstVisibleCard && bookingState.selectedFacility && bookingState.selectedFacility.id && document.querySelector('.facility-choice-card.selected')) {
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

  // Bind Empty State Reset Button
  const resetCategoryBtn = document.getElementById('btnEmptyResetCategory');
  if (resetCategoryBtn) {
    resetCategoryBtn.addEventListener('click', () => {
      const allPill = document.querySelector('.filter-pill[data-category-filter="all"]');
      if (allPill) {
        allPill.click();
      } else {
        switchCategory('all', true);
      }
    });
  }

  // Bind Category Pill Tabs (Halls, Dormitories, Guesthouses)
  const categoryPillTabs = document.querySelectorAll('.category-pill-tab');
  categoryPillTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      e.preventDefault();
      const cat = tab.dataset.category || (tab.classList.contains('tab-guesthouses') ? 'guesthouses' : (tab.classList.contains('tab-dorms') ? 'dormitories' : 'halls'));
      switchCategory(cat, false);
      if (window.history && window.history.pushState) {
        window.history.pushState({ category: cat }, '', `booking.php?category=${cat}`);
      }
    });
  });

  // Handle browser Back/Forward navigation without requiring reload
  window.addEventListener('popstate', (e) => {
    const urlParams = new URLSearchParams(window.location.search);
    const cat = urlParams.get('category') || (e.state && e.state.category) || 'halls';
    switchCategory(cat, false);
  });

  // Check URL query parameters for pre-selected category (e.g. ?category=guesthouses)
  const urlParams = new URLSearchParams(window.location.search);
  const requestedCat = urlParams.get('category');
  if (requestedCat === 'dormitories' || requestedCat === 'halls' || requestedCat === 'guesthouses' || requestedCat === 'all') {
    switchCategory(requestedCat, false);
  } else if (isDormPage) {
    switchCategory('dormitories', false);
  } else {
    const activeTab = document.querySelector('.category-pill-tab.active');
    const activePill = document.querySelector('.filter-pill.active');
    let defaultCat = 'halls';
    if (activeTab && activeTab.dataset.category) {
      defaultCat = activeTab.dataset.category;
    } else if (activeTab && activeTab.classList.contains('tab-guesthouses')) {
      defaultCat = 'guesthouses';
    } else if (activePill && activePill.dataset.categoryFilter) {
      defaultCat = activePill.dataset.categoryFilter;
    }
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
      if (typeof window.refreshLiveAvailabilityForCurrentFacility === 'function') {
        window.refreshLiveAvailabilityForCurrentFacility();
      }
      if (bookingState.selectedFacility?.occupiedOnDefaultDate) {
        if (typeof window.selectAlternateAvailableDateForRoom === 'function') {
          const itemLabel = isDorm ? `Room ${bookingState.selectedFacility?.roomNumber}` : bookingState.selectedFacility?.setupName;
          window.selectAlternateAvailableDateForRoom(itemLabel);
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
        proceedBtn.innerHTML = `<span>Proceed to ${isDorm ? 'Lodging Details' : 'Event Details'}</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else if (stepNumber === 3) {
        proceedBtn.style.display = 'inline-flex';
        proceedBtn.innerHTML = `<span>Proceed to ${isDorm ? 'Roster &amp; Authority' : 'Document Upload'}</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
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
  window.setDormStep = goToStep;

  // Allow clicking on stepper tabs
  stepButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetStep = parseInt(btn.dataset.step, 10);
      if (targetStep) goToStep(targetStep);
    });
  });

  // Validation helper for Step 3 (Event & Lodging Details)
  function validateStep3Inputs() {
    let isValid = true;
    let firstInvalidInput = null;

    const titleInput = document.getElementById('eventTitleInput') || document.getElementById('dormLodgingPurpose');
    const titleError = document.getElementById('eventTitleError') || document.getElementById('dormLodgingPurposeError');
    const paxInput = document.getElementById('eventPaxInput') || document.getElementById('dormGuestCount');
    const paxError = document.getElementById('eventPaxError') || document.getElementById('dormGuestCountError');
    const divSelect = document.getElementById('divisionSelect') || document.getElementById('dormDivisionUnit');
    const divOtherInput = document.getElementById('divisionOtherInput') || document.getElementById('dormDivisionOtherInput');
    const divOtherError = document.getElementById('divisionOtherError');

    // 1. Validate Event Title
    if (titleInput) {
      const val = titleInput.value.trim();
      if (!val || val.length < 3) {
        isValid = false;
        titleInput.style.borderColor = '#dc2626';
        titleInput.style.backgroundColor = '#fff5f5';
        if (titleError) titleError.style.display = 'block';
        if (!firstInvalidInput) firstInvalidInput = titleInput;
      } else {
        titleInput.style.borderColor = '';
        titleInput.style.backgroundColor = '';
        if (titleError) titleError.style.display = 'none';
      }
    }

    // 2. Validate Attendees Count (PAX)
    if (paxInput) {
      const num = parseInt(paxInput.value, 10);
      if (isNaN(num) || num <= 0) {
        isValid = false;
        paxInput.style.borderColor = '#dc2626';
        paxInput.style.backgroundColor = '#fff5f5';
        if (paxError) paxError.style.display = 'block';
        if (!firstInvalidInput) firstInvalidInput = paxInput;
      } else {
        paxInput.style.borderColor = '';
        paxInput.style.backgroundColor = '';
        if (paxError) paxError.style.display = 'none';
      }
    }

    // 3. Validate Division if "Other" is selected
    if (divSelect && divSelect.value === 'Other' && divOtherInput) {
      const otherVal = divOtherInput.value.trim();
      if (!otherVal || otherVal.length < 2) {
        isValid = false;
        divOtherInput.style.borderColor = '#dc2626';
        divOtherInput.style.backgroundColor = '#fff5f5';
        if (divOtherError) divOtherError.style.display = 'block';
        if (!firstInvalidInput) firstInvalidInput = divOtherInput;
      } else {
        divOtherInput.style.borderColor = '';
        divOtherInput.style.backgroundColor = '';
        if (divOtherError) divOtherError.style.display = 'none';
      }
    }

    if (!isValid && firstInvalidInput) {
      firstInvalidInput.focus();
      firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return isValid;
  }

  // Custom Clean Popup Modal for Validation Alerts
  function showValidationModal(title, message, onDismiss) {
    const modal = document.getElementById('validationWarningModal');
    const titleEl = document.getElementById('valModalTitle');
    const msgEl = document.getElementById('valModalMessage');
    const btnDismiss = document.getElementById('btnDismissValModal');

    if (!modal) {
      alert(`${title}\n\n${message}`);
      if (typeof onDismiss === 'function') onDismiss();
      return;
    }

    if (titleEl) titleEl.textContent = title;
    if (msgEl) msgEl.textContent = message;

    modal.style.display = 'flex';

    function closeModal() {
      modal.style.display = 'none';
      btnDismiss.removeEventListener('click', closeModal);
      modal.removeEventListener('click', handleBackdrop);
      if (typeof onDismiss === 'function') onDismiss();
    }

    function handleBackdrop(e) {
      if (e.target === modal) {
        closeModal();
      }
    }

    btnDismiss.addEventListener('click', closeModal);
    modal.addEventListener('click', handleBackdrop);
  }

  // Proceed button click listener
  if (proceedBtn) {
    proceedBtn.addEventListener('click', () => {
      const isDorm = isDormitorySelected();
      if (bookingState.currentStep === 1) {
        if (isDorm && !bookingState.selectedFacility?.roomNumber) {
          showValidationModal(
            'Room Selection Required',
            'Please choose a room unit (e.g. Room 101, 102, 103) inside your chosen dormitory floor before proceeding to Date & Time Selection.',
            () => {
              const card = document.querySelector('.facility-choice-card.selected');
              if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          );
          return;
        }
        if (!isDorm && !bookingState.selectedFacility?.setupName) {
          showValidationModal(
            'Venue Layout Required',
            'Please choose a venue layout & setup (e.g. Theater Setup, Classroom Setup, Banquet Dining) before proceeding to Date & Time Selection.',
            () => {
              const card = document.querySelector('.facility-choice-card.selected');
              if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          );
          return;
        }
        goToStep(2);
      } else if (bookingState.currentStep === 2) {
        goToStep(3); // Proceeds to Event Details for BOTH dormitories and halls!
      } else if (bookingState.currentStep === 3) {
        if (!validateStep3Inputs()) {
          showValidationModal(
            'Event Details Incomplete',
            'Please fill out all required fields marked with an asterisk (*) — Activity Title, Estimated Attendees, and Division/Unit.'
          );
          return; // Block advancement if required fields are missing
        }
        goToStep(4);
      } else if (bookingState.currentStep === 4) {
        // Step 4 Validation: Require supporting document upload
        const docInput = document.getElementById('documentUploadInput') || document.getElementById('dormDocUpload');
        const hasFile = (docInput && docInput.files && docInput.files.length > 0) || bookingState.documentFile;

        if (!hasFile) {
          const dropZone = document.getElementById('documentUploadZone');
          const docErr = document.getElementById('documentUploadError');
          if (dropZone) {
            dropZone.style.borderColor = '#dc2626';
            dropZone.style.backgroundColor = '#fff5f5';
            dropZone.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          if (docErr) {
            docErr.style.display = 'block';
          }
          const isDormBooking = bookingState.selectedFacility?.type === 'dormitories' || Boolean(document.getElementById('dormLodgingPurpose'));
          showValidationModal(
            'Supporting Document Required',
            isDormBooking
              ? 'Please upload your signed Reservation Form, Travel Order, Special Order, or Participant List before proceeding to Review & Confirmation.'
              : 'Please upload your approved Activity Design, Special Order, or Official Request Memo before proceeding to Review & Confirmation.'
          );
          return;
        }
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
  const eventTitleInput = document.getElementById('eventTitleInput') || document.getElementById('dormLodgingPurpose');
  const eventPaxInput = document.getElementById('eventPaxInput') || document.getElementById('dormGuestCount');
  if (eventTitleInput) {
    eventTitleInput.addEventListener('input', () => {
      bookingState.eventTitle = eventTitleInput.value;
      updateReviewSummary();
    });
  }
  if (eventPaxInput) {
    eventPaxInput.addEventListener('input', () => {
      eventPaxInput.dataset.manual = 'true';
      bookingState.participants = eventPaxInput.value;
      updateReviewSummary();
    });
  }

  const dormMaleInput = document.getElementById('dormMaleCount');
  const dormFemaleInput = document.getElementById('dormFemaleCount');
  if (dormMaleInput) {
    dormMaleInput.addEventListener('input', () => {
      const m = parseInt(dormMaleInput.value || 0, 10) || 0;
      const f = parseInt(dormFemaleInput?.value || 0, 10) || 0;
      if (eventPaxInput && (!eventPaxInput.value || !eventPaxInput.dataset.manual)) {
        if (m + f > 0) eventPaxInput.value = m + f;
        bookingState.participants = String(m + f);
      }
      updateReviewSummary();
    });
  }
  if (dormFemaleInput) {
    dormFemaleInput.addEventListener('input', () => {
      const m = parseInt(dormMaleInput?.value || 0, 10) || 0;
      const f = parseInt(dormFemaleInput.value || 0, 10) || 0;
      if (eventPaxInput && (!eventPaxInput.value || !eventPaxInput.dataset.manual)) {
        if (m + f > 0) eventPaxInput.value = m + f;
        bookingState.participants = String(m + f);
      }
      updateReviewSummary();
    });
  }

  // Requesting Division / Unit dropdown + conditional "Other" manual input
  const divisionSelect = document.getElementById('divisionSelect') || document.getElementById('dormDivisionUnit');
  const divisionOtherWrap = document.getElementById('divisionOtherWrap') || document.getElementById('dormDivisionOtherWrap');
  const divisionOtherInput = document.getElementById('divisionOtherInput') || document.getElementById('dormDivisionOtherInput');

  function getEffectiveDivision() {
    if (divisionSelect) {
      if (divisionSelect.value === 'Other') {
        const customVal = divisionOtherInput?.value?.trim();
        return customVal || 'Other (Unspecified Department)';
      }
      return divisionSelect.value;
    }
    return bookingState.division || 'Career Development Division (CDD)';
  }

  function handleDivisionChange() {
    const val = divisionSelect ? divisionSelect.value : '';
    if (val === 'Other') {
      if (divisionOtherWrap) divisionOtherWrap.style.display = 'block';
      if (divisionOtherInput) divisionOtherInput.focus();
    } else {
      if (divisionOtherWrap) divisionOtherWrap.style.display = 'none';
    }
    bookingState.division = getEffectiveDivision();
    const hiddenDivInput = document.getElementById('divisionInput');
    if (hiddenDivInput) hiddenDivInput.value = bookingState.division;
    updateReviewSummary();
  }

  if (divisionSelect) {
    divisionSelect.addEventListener('change', handleDivisionChange);
  }
  if (divisionOtherInput) {
    divisionOtherInput.addEventListener('input', () => {
      bookingState.division = getEffectiveDivision();
      const hiddenDivInput = document.getElementById('divisionInput');
      if (hiddenDivInput) hiddenDivInput.value = bookingState.division;
      updateReviewSummary();
    });
  }

  // Dormitory submit button listener (dormitory_booking.php)
  const btnSubmitDormitory = document.getElementById('btnSubmitDormitoryBooking');
  if (btnSubmitDormitory) {
    btnSubmitDormitory.addEventListener('click', () => {
      if (!validateStep3Inputs()) {
        showValidationModal('Lodging Details Incomplete', 'Please complete all required fields (Lodging Purpose, Guests Count, Endorsing Division) before submitting.', () => {
          goToStep(3);
        });
        return;
      }

      const refNum = `ATI-BK-${new Date().getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
      const refEl = document.getElementById('dormRefPreview');
      if (refEl) refEl.textContent = refNum;

      const curTitle = document.getElementById('dormLodgingPurpose')?.value?.trim() || bookingState.eventTitle || '';
      const curPax = document.getElementById('dormGuestCount')?.value?.trim() || bookingState.participants || '1';
      const curDivision = getEffectiveDivision();
      const facId = bookingState.selectedFacility?.dbId || bookingState.selectedFacility?.facilityDbId || (bookingState.selectedFacility?.id ? String(bookingState.selectedFacility.id).replace(/\D/g, '') : '');

      const formData = new FormData();
      formData.append('reference_no', refNum);
      formData.append('facility_id', facId);
      formData.append('facility_name', bookingState.selectedFacility?.name || 'Suite 1');
      formData.append('facility_type', 'dormitories');
      formData.append('rate', bookingState.selectedFacility?.rate || '₱500/night');
      formData.append('capacity', bookingState.selectedFacility?.capacity || '12 ROOMS (48 BEDS)');
      if (bookingState.selectedFacility?.roomNumber) {
        formData.append('room_number', bookingState.selectedFacility.roomNumber);
      }
      formData.append('event_title', curTitle);
      formData.append('pax_count', curPax);
      formData.append('requesting_division', curDivision);

      const sDate = bookingState.selectedStartDate;
      const eDate = bookingState.selectedEndDate;
      if (sDate && eDate) {
        formData.append('start_date', `${sDate.year}-${String(sDate.month + 1).padStart(2, '0')}-${String(sDate.day).padStart(2, '0')}`);
        formData.append('end_date', `${eDate.year}-${String(eDate.month + 1).padStart(2, '0')}-${String(eDate.day).padStart(2, '0')}`);
      }

      const timeSelect = document.getElementById('startTimeSelect');
      const endTimeSelect = document.getElementById('endTimeSelect');
      let timeSlot = (timeSelect && timeSelect.value ? timeSelect.value : '') + (endTimeSelect && endTimeSelect.value ? ' - ' + endTimeSelect.value : '');
      if (!timeSlot || timeSlot.trim() === '-' || timeSlot.trim() === '') {
        timeSlot = '02:00 PM Check-in - 12:00 PM Check-out';
      }
      formData.append('time_slot', timeSlot);

      const specialNotes = document.getElementById('dormSpecialRequests')?.value || '';
      formData.append('special_notes', specialNotes);

      const docInput = document.getElementById('documentUploadInput') || document.getElementById('dormDocUpload');
      if (docInput?.files?.[0]) {
        formData.append('document', docInput.files[0]);
      } else if (bookingState.documentFile) {
        formData.append('document', bookingState.documentFile);
      }

      const origText = btnSubmitDormitory.innerHTML;
      btnSubmitDormitory.disabled = true;
      btnSubmitDormitory.innerHTML = '<span>Submitting Lodging Request...</span>';

      const dormFloorText = document.getElementById('dormReviewFloor')?.textContent?.trim() || (bookingState.selectedFacility?.name || 'Suite 1');
      const dormStayText = document.getElementById('dormReviewStay')?.textContent?.trim() || bookingState.date || 'Selected Dates';

      const currentUserName = window.currentUserFullName || 'Authorized Staff';
      const submitData = {
        isDorm: true,
        refNum: refNum,
        facilityName: dormFloorText,
        setup: bookingState.selectedFacility?.roomNumber ? `Room ${bookingState.selectedFacility.roomNumber}` : 'Standard Dorm Bed Assignment',
        rate: bookingState.selectedFacility?.rate || '₱500 / night',
        schedule: dormStayText,
        time: timeSlot,
        pax: curPax,
        purpose: curTitle,
        requestedBy: `${currentUserName} (${curDivision})`
      };

      fetch('ajax/submit_booking.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btnSubmitDormitory.innerHTML = origText;
        btnSubmitDormitory.disabled = false;
        if (data && data.conflict) {
          showValidationModal('Reservation Conflict Detected', data.message);
          return;
        }
        if (data && !data.success && data.message) {
          showValidationModal('Submission Error', data.message);
          return;
        }
        window.showReservationSuccessModal(submitData);
      })
      .catch(err => {
        console.error(err);
        btnSubmitDormitory.innerHTML = origText;
        btnSubmitDormitory.disabled = false;
        showValidationModal('Submission Error', 'A network or transmission error occurred while processing your lodging request. Please check your connection and try again.');
      });
    });
  }

  // Final Submit listener on Step 5 (booking.php)
  const finalSubmitBtn = document.getElementById('btnSubmitFinalReservation');
  if (finalSubmitBtn) {
    finalSubmitBtn.addEventListener('click', () => {
      if (!validateStep3Inputs()) {
        showValidationModal('Event Details Incomplete', 'Please complete all required fields (Event Title, Attendees, Division) before submitting.', () => {
          goToStep(3);
        });
        return;
      }
      const refNum = `ATI-RES-${new Date().getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
      const isDorm = isDormitorySelected();
      const curDivision = getEffectiveDivision();
      const facId = bookingState.selectedFacility?.dbId || bookingState.selectedFacility?.facilityDbId || (bookingState.selectedFacility?.id ? String(bookingState.selectedFacility.id).replace(/\D/g, '') : '');

      const formData = new FormData();
      formData.append('reference_no', refNum);
      formData.append('facility_id', facId);
      formData.append('facility_name', bookingState.selectedFacility?.name || 'Function Hall');
      formData.append('facility_type', isDorm ? 'dormitories' : 'halls');
      formData.append('rate', bookingState.selectedFacility?.rate || 'N/A');
      formData.append('capacity', bookingState.selectedFacility?.capacity || 'N/A');
      if (bookingState.selectedFacility?.roomNumber) {
        formData.append('room_number', bookingState.selectedFacility.roomNumber);
      }
      formData.append('event_title', bookingState.eventTitle || '');
      formData.append('pax_count', bookingState.participants || '0');
      formData.append('requesting_division', curDivision);

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

      const currentUserName = window.currentUserFullName || 'Authorized Staff';
      const submitData = {
        isDorm: isDorm,
        refNum: refNum,
        facilityName: bookingState.selectedFacility?.name || (isDorm ? 'Suite 1' : 'Function Hall'),
        setup: isDorm 
          ? (bookingState.selectedFacility?.roomNumber ? `Room ${bookingState.selectedFacility.roomNumber}` : 'Standard Dormitory Beds')
          : (bookingState.selectedFacility?.setupName || 'Theater Setup (200 PAX)'),
        rate: bookingState.selectedFacility?.rate || '₱5,000 / day',
        schedule: bookingState.date || 'Selected Schedule',
        time: timeSlot || 'Whole Day (8:00 AM - 5:00 PM)',
        pax: bookingState.participants || '200',
        purpose: bookingState.eventTitle || 'Regional Agricultural Extension Activity',
        requestedBy: `${currentUserName} (${curDivision})`
      };

      fetch('ajax/submit_booking.php', {
        method: 'POST',
        body: formData
      })
        .then(res => res.json())
        .then(data => {
          finalSubmitBtn.innerHTML = origText;
          finalSubmitBtn.disabled = false;
          if (data && data.conflict) {
            showValidationModal('Reservation Conflict Detected', data.message);
            return;
          }
          if (data && !data.success && data.message) {
            showValidationModal('Submission Error', data.message);
            return;
          }
          window.showReservationSuccessModal(submitData);
        })
        .catch(err => {
          console.error(err);
          finalSubmitBtn.innerHTML = origText;
          finalSubmitBtn.disabled = false;
          showValidationModal('Submission Error', 'A network or transmission error occurred while processing your booking request. Please check your connection and try again.');
        });
    });
  }

  // Initial stepper setup
  updateStepperMode();
}

/**
 * Display Official Reservation Success Confirmation Modal
 */
window.showReservationSuccessModal = function (submitData) {
  const modal = document.getElementById('reservationSuccessModal');
  if (!modal) {
    alert(`✓ Reservation Submitted Successfully!\n\nReference No.: ${submitData.refNum}\nFacility: ${submitData.facilityName}\nStatus: Pending Administrative Approval`);
    window.location.href = 'my_reservations.php';
    return;
  }

  // Populate receipt details in modal
  const refEl = document.getElementById('succModalRef');
  const facEl = document.getElementById('succModalFacility');
  const schedEl = document.getElementById('succModalSchedule');
  const timeEl = document.getElementById('succModalTime');

  if (refEl) refEl.textContent = submitData.refNum || 'ATI-RES-PENDING';
  if (facEl) facEl.textContent = `${submitData.facilityName} (${submitData.setup || 'Standard'})`;
  if (schedEl) schedEl.textContent = submitData.schedule || 'Scheduled Dates';
  if (timeEl) timeEl.textContent = submitData.time || 'Regular Hours';

  // Save to localStorage for quick client sync
  try {
    const list = JSON.parse(localStorage.getItem('ati_system_reservations') || '[]');
    list.unshift({
      id: Date.now(),
      ref: submitData.refNum,
      title: submitData.purpose,
      facility: submitData.facilityName,
      facilityKey: submitData.isDorm ? 'dormitory' : 'function-hall',
      date: typeof bookingState !== 'undefined' && bookingState.selectedStartDate ? `${bookingState.selectedStartDate.year}-${String(bookingState.selectedStartDate.month + 1).padStart(2, '0')}-${String(bookingState.selectedStartDate.day).padStart(2, '0')}` : new Date().toISOString().split('T')[0],
      time: submitData.time,
      division: submitData.requestedBy,
      attendees: `${submitData.pax} Participants`,
      status: 'Pending'
    });
    localStorage.setItem('ati_system_reservations', JSON.stringify(list));
  } catch (e) {
    console.warn('LocalStorage save error:', e);
  }

  modal.style.display = 'flex';
};

/* ==========================================================================
   4. Update Summary on Step 1 (Preview) and Step 5 (Official Summary)
   ========================================================================== */
function updateReviewSummary() {
  const isDorm = bookingState.selectedFacility?.type === 'dormitories' ||
    String(bookingState.selectedFacility?.id).startsWith('dorm-');
  const isGuesthouse = bookingState.selectedFacility?.type === 'guesthouses' ||
    String(bookingState.selectedFacility?.id).startsWith('guesthouse-');
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

  if (prevBadge) prevBadge.textContent = isGuesthouse ? 'GUESTHOUSE VILLA' : (isDorm ? 'DORMITORY ACCOMMODATION' : 'SELECTED VENUE');
  if (prevTitle) prevTitle.textContent = facilityName;
  if (prevRate) prevRate.textContent = rateText;
  if (prevCap) prevCap.textContent = capText;

  if (prevRoomWrap && prevRoom) {
    if ((isDorm || isGuesthouse) && roomNum) {
      if (prevUnitLabel) prevUnitLabel.textContent = isGuesthouse ? 'Assigned Villa Unit / Suite:' : 'Assigned Room Unit:';
      prevRoomWrap.style.display = 'flex';
      const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
      const displayUnit = isGuesthouse ? roomNum : `Room ${roomNum}`;
      if (isOcc) {
        prevRoom.innerHTML = `${displayUnit} (${floorName}) <span style="display:inline-block; margin-left:6px; font-size:0.75rem; color:#dc2626; font-weight:700;">(Occupied Today • Pick alternate date in Step 2)</span>`;
      } else {
        prevRoom.textContent = `${displayUnit} (${floorName})`;
      }
    } else if (!isDorm && !isGuesthouse && hallSetup) {
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

  if (sumBadge) sumBadge.textContent = isDorm ? 'DORMITORY ACCOMMODATION PARTICULARS' : (isGuesthouse ? 'GUESTHOUSE VILLA RESERVATION PARTICULARS' : 'OFFICIAL VENUE RESERVATION PARTICULARS');
  if (sumVenue) sumVenue.textContent = facilityName;
  if (sumRate) sumRate.textContent = rateText;
  if (sumFacType) sumFacType.textContent = isDorm ? 'Trainee Dormitory Lodging' : (isGuesthouse ? 'Private Guesthouse Villa' : 'Conference & Training Venue');
  if (sumCap) sumCap.textContent = capText;
  if (sumDate) sumDate.textContent = bookingState.date || _formattedTodayDate;
  if (sumTime) sumTime.textContent = (isDorm || isGuesthouse) ? 'Overnight Lodging Stay' : (bookingState.timeSlot || 'Whole Day (8:00 AM - 5:00 PM)');

  if (sumRoomWrap && sumRoom) {
    if ((isDorm || isGuesthouse) && roomNum) {
      if (sumRoomLabel) sumRoomLabel.textContent = isGuesthouse ? 'Assigned Villa Unit / Suite:' : 'Assigned Room Unit:';
      sumRoomWrap.style.display = 'flex';
      const displayUnit = isGuesthouse ? roomNum : `Room ${roomNum}`;
      sumRoom.textContent = `${displayUnit} (${floorName})`;
    } else if (!isDorm && !isGuesthouse && hallSetup) {
      if (sumRoomLabel) sumRoomLabel.textContent = 'Assigned Setup & Layout:';
      sumRoomWrap.style.display = 'flex';
      sumRoom.textContent = `${hallSetup} (${hallCap})`;
    } else {
      sumRoomWrap.style.display = 'none';
      sumRoom.textContent = 'None';
    }
  }

  // Step 5 Event Details Summary fields
  const eventTitleInput = document.getElementById('eventTitleInput') || document.getElementById('dormLodgingPurpose');
  const eventPaxInput = document.getElementById('eventPaxInput') || document.getElementById('dormGuestCount');
  const sumEventTitle = document.getElementById('summaryEventTitle');
  const sumPaxValue = document.getElementById('summaryPaxValue');
  const sumEventLabel = document.getElementById('summaryEventLabel');
  const sumPaxLabel = document.getElementById('summaryPaxLabel');

  const curTitle = eventTitleInput?.value?.trim() || bookingState.eventTitle || '';
  const curPax = eventPaxInput?.value?.trim() || bookingState.participants || '';

  if (sumEventLabel) sumEventLabel.textContent = isDorm ? 'Training / Stay Purpose:' : 'Activity / Event Title:';
  if (sumEventTitle) sumEventTitle.textContent = curTitle || '—';
  if (sumPaxLabel) sumPaxLabel.textContent = isDorm ? 'Trainees / Lodgers:' : 'Participants / Attendees:';
  if (sumPaxValue) sumPaxValue.textContent = curPax || '—';

  // Support for dormitory-specific review cards (dormitory_booking.php)
  const dormReviewTitle = document.getElementById('dormReviewTitle');
  const dormReviewFloor = document.getElementById('dormReviewFloor');
  const dormReviewStay = document.getElementById('dormReviewStay');
  const dormReviewPax = document.getElementById('dormReviewPax');

  if (dormReviewTitle) dormReviewTitle.textContent = curTitle || 'Official Lodging Accommodation';
  if (dormReviewFloor) {
    dormReviewFloor.textContent = roomNum ? `${facilityName} (Room ${roomNum})` : facilityName;
  }
  if (dormReviewStay) {
    const s = bookingState.selectedStartDate;
    const e = bookingState.selectedEndDate;
    if (s && e) {
      const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      const sObj = new Date(s.year, s.month, s.day);
      const eObj = new Date(e.year, e.month, e.day);
      const diff = Math.round(Math.abs(eObj - sObj) / (1000 * 60 * 60 * 24)) + 1;
      const sStr = `${MONTH_SHORT[s.month]} ${s.day}`;
      const eStr = `${MONTH_SHORT[e.month]} ${e.day}, ${e.year}`;
      dormReviewStay.textContent = diff === 1 ? `${sStr}, ${s.year} (1 Day / Overnight)` : `${sStr} - ${eStr} (${diff - 1} Nights)`;
    }
  }
  if (dormReviewPax) {
    const maleVal = parseInt(document.getElementById('dormMaleCount')?.value || 0, 10) || 0;
    const femaleVal = parseInt(document.getElementById('dormFemaleCount')?.value || 0, 10) || 0;
    let breakdown = '';
    if (maleVal > 0 || femaleVal > 0) {
      breakdown = ` (${maleVal}M / ${femaleVal}F)`;
    }
    if (curPax) {
      const baseLabel = curPax.includes('Delegates') || curPax.includes('Trainees') || curPax.includes('PAX') || curPax.includes('Guests') ? curPax : `${curPax} Guests`;
      dormReviewPax.textContent = `${baseLabel}${breakdown}`;
    } else if (maleVal + femaleVal > 0) {
      dormReviewPax.textContent = `${maleVal + femaleVal} Guests${breakdown}`;
    } else {
      dormReviewPax.textContent = '—';
    }
  }

  // Update requested division on Step 5 review cards
  const curDivision = bookingState.division || 'Career Development Division (CDD)';
  const sumReqDiv = document.getElementById('summaryRequestedDivision');
  if (sumReqDiv) sumReqDiv.textContent = curDivision;
  const dormReviewDiv = document.getElementById('dormReviewDivision');
  if (dormReviewDiv) dormReviewDiv.textContent = curDivision;
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

  // Helper to determine if date is occupied for selected room
  function isOccupiedRoomDate(d) {
    return false;
  }

  // Live booked and suspended dates strictly driven by MySQL database
  const RESERVED_DATES = new Set();
  const SUSPENDED_DATES = new Set();

  // Live booked, pending, and suspended dates from MySQL database
  const DB_BOOKED_DATES = new Set();
  const DB_BOOKED_DETAILS = {};
  const DB_PENDING_DATES = new Set();
  const DB_PENDING_DETAILS = {};
  const DB_SUSPENDED_DATES = new Set();
  const DB_SUSPENDED_DETAILS = {};

  function fetchLiveAvailabilityForViewMonth() {
    const card = document.querySelector(`.facility-choice-card.selected`);
    const facId = bookingState.selectedFacility?.dbId || bookingState.selectedFacility?.facilityDbId || card?.dataset.facilityDbId || (bookingState.selectedFacility?.id ? String(bookingState.selectedFacility.id).replace(/\D/g, '') : '');
    const facName = bookingState.selectedFacility?.name || card?.dataset.name || '';
    const roomNum = bookingState.selectedFacility?.roomNumber || '';
    const url = `ajax/check_availability.php?facility_id=${encodeURIComponent(facId)}&facility_name=${encodeURIComponent(facName)}&room_number=${encodeURIComponent(roomNum)}&year=${viewYear}&month=${viewMonth + 1}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (data && data.status === 'success') {
          DB_BOOKED_DATES.clear();
          (data.booked_dates || []).forEach(d => DB_BOOKED_DATES.add(d));
          for (let k in DB_BOOKED_DETAILS) delete DB_BOOKED_DETAILS[k];
          Object.assign(DB_BOOKED_DETAILS, data.booked_details || {});

          DB_PENDING_DATES.clear();
          (data.pending_dates || []).forEach(d => DB_PENDING_DATES.add(d));
          for (let k in DB_PENDING_DETAILS) delete DB_PENDING_DETAILS[k];
          Object.assign(DB_PENDING_DETAILS, data.pending_details || {});

          DB_SUSPENDED_DATES.clear();
          (data.suspended_dates || []).forEach(d => DB_SUSPENDED_DATES.add(d));
          for (let k in DB_SUSPENDED_DETAILS) delete DB_SUSPENDED_DETAILS[k];
          Object.assign(DB_SUSPENDED_DETAILS, data.suspended_details || {});

          // If current selected start date falls on a suspended or booked date, advance to first available date
          const curStartKey = dateToKey(selectedStart.year, selectedStart.month, selectedStart.day);
          if (DB_SUSPENDED_DATES.has(curStartKey) || DB_BOOKED_DATES.has(curStartKey)) {
            const totalDaysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            let nextAvail = null;
            for (let day = TODAY.day; day <= totalDaysInMonth; day++) {
              const testKey = dateToKey(viewYear, viewMonth, day);
              if (!DB_SUSPENDED_DATES.has(testKey) && !DB_BOOKED_DATES.has(testKey)) {
                nextAvail = day;
                break;
              }
            }
            if (nextAvail) {
              selectedStart = { year: viewYear, month: viewMonth, day: nextAvail };
              selectedEnd = { ...selectedStart };
            }
          }

          renderCalendarGrid();
          updateGridHighlights();
          renderSelectionDetails();
        }
      })
      .catch(err => console.warn('Availability check fallback:', err));
  }

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

    // Check if the selected date range intersects any pending requests
    let hasPendingInRange = false;
    let pendingFirstTitle = '';
    let curP = new Date(selectedStart.year, selectedStart.month, selectedStart.day);
    const endP = new Date(selectedEnd.year, selectedEnd.month, selectedEnd.day);
    while (curP <= endP) {
      const pKey = dateToKey(curP.getFullYear(), curP.getMonth(), curP.getDate());
      if (DB_PENDING_DATES.has(pKey)) {
        hasPendingInRange = true;
        const pArr = DB_PENDING_DETAILS[pKey] || [];
        if (pArr.length > 0 && !pendingFirstTitle) pendingFirstTitle = pArr[0].title;
      }
      curP.setDate(curP.getDate() + 1);
    }

    if (hasPendingInRange) {
      if (statusTitle) {
        statusTitle.innerHTML = `<span style="color: #b45309; display: inline-flex; align-items: center; gap: 0.4rem;">⚠️ Pending Request (Under Review)</span>`;
      }
      if (statusDesc) {
        const titleNote = pendingFirstTitle ? ` ("${pendingFirstTitle}")` : '';
        statusDesc.innerHTML = `<strong style="color: #92400e;">Notice:</strong> An earlier reservation request${titleNote} has been submitted for the selected date and is currently under administrative review. You may still proceed with your submission, subject to official evaluation and approval by the Administrator.`;
      }
    } else {
      if (statusTitle) {
        statusTitle.textContent = 'Selected Slot Available!';
      }
      if (statusDesc) {
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
      const key = dateToKey(y, m, d);
      const isPending = DB_PENDING_DATES.has(key);

      if (inRange) {
        b.classList.add('selected');
        b.classList.remove('available');
      } else {
        b.classList.remove('selected');
        if (isPending) {
          b.classList.add('has-pending');
          b.classList.remove('available');
        } else {
          b.classList.remove('has-pending');
          b.classList.add('available');
        }
      }
    });
  }

  function handleDayClick(dateObj, btn) {
    const key = dateToKey(dateObj.year, dateObj.month, dateObj.day);

    if (btn.classList.contains('past-date') || isPastDate(dateObj.year, dateObj.month, dateObj.day)) {
      return;
    }

    const isSuspendedDate = btn.classList.contains('suspended') || DB_SUSPENDED_DATES.has(key) || SUSPENDED_DATES.has(key);
    if (isSuspendedDate) {
      const suspInfo = DB_SUSPENDED_DETAILS[key];
      const suspReason = suspInfo?.reason || suspInfo?.title || 'Scheduled Facility Maintenance';
      alert(`${formatDisplayDate(dateObj)} is currently suspended (${suspReason}).\n\nPlease select an available green slot.`);
      return;
    }

    const isRoomOccupiedThisDate = isOccupiedRoomDate(dateObj);
    const isDbBooked = DB_BOOKED_DATES.has(key);
    if (btn.classList.contains('reserved') || RESERVED_DATES.has(key) || isRoomOccupiedThisDate || isDbBooked) {
      if (isRoomOccupiedThisDate) {
        const roomNum = bookingState.selectedFacility?.roomNumber;
        alert(`Room ${roomNum} is occupied on ${formatDisplayDate(dateObj)}.\n\nPlease select an available green slot to book this room.`);
      } else if (isDbBooked) {
        const details = DB_BOOKED_DETAILS[key] || [];
        const evTitle = details.length > 0 ? ` (${details[0].title})` : '';
        alert(`${formatDisplayDate(dateObj)} is already booked and approved${evTitle}.\n\nPlease select an available slot.`);
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
          if (DB_BOOKED_DATES.has(cKey) || RESERVED_DATES.has(cKey)) {
            const details = DB_BOOKED_DETAILS[cKey] || [];
            const evTitle = details.length > 0 ? ` (${details[0].title})` : '';
            conflict = `${formatDisplayDate({ year: cY, month: cM, day: cD })} is already reserved${evTitle}`;
            break;
          }
          if (DB_SUSPENDED_DATES.has(cKey) || SUSPENDED_DATES.has(cKey)) {
            const suspInfo = DB_SUSPENDED_DETAILS[cKey];
            const suspReason = suspInfo?.reason || suspInfo?.title || 'facility maintenance';
            conflict = `${formatDisplayDate({ year: cY, month: cM, day: cD })} is suspended (${suspReason})`;
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
      const isSuspended = DB_SUSPENDED_DATES.has(key) || SUSPENDED_DATES.has(key);
      const isRoomOccupiedThisDate = isOccupiedRoomDate(dateObj);
      const isDbBooked = DB_BOOKED_DATES.has(key);
      const isReserved = RESERVED_DATES.has(key) || isRoomOccupiedThisDate || isDbBooked;
      const isPending = !isPast && !isSuspended && !isReserved && DB_PENDING_DATES.has(key);
      const isSelected = isDateInRange(dateObj, selectedStart, selectedEnd);

      if (isPast) {
        btn.className = 'slot-day-btn past-date';
        btn.disabled = true;
        btn.title = 'Unavailable';
      } else if (isSuspended) {
        btn.className = 'slot-day-btn suspended';
        const suspInfo = DB_SUSPENDED_DETAILS[key];
        const suspReason = suspInfo?.reason || suspInfo?.title || 'Scheduled Facility Maintenance';
        btn.title = `Suspended: ${suspReason}`;
      } else if (isReserved) {
        btn.className = 'slot-day-btn reserved';
        if (isRoomOccupiedThisDate) {
          btn.title = `Room ${bookingState.selectedFacility?.roomNumber || ''} is occupied on this date`;
        } else if (isDbBooked) {
          const details = DB_BOOKED_DETAILS[key] || [];
          btn.title = details.length > 0 ? `Booked: ${details[0].title}` : 'Reserved for Official Event';
        } else {
          btn.title = 'Reserved for Official Event';
        }
      } else if (isPending) {
        const pDetails = DB_PENDING_DETAILS[key] || [];
        const pTitle = pDetails.length > 0 ? ` ("${pDetails[0].title}")` : '';
        btn.title = `Pending Request: A reservation request${pTitle} is currently awaiting administrative review for this date`;
        if (isSelected) {
          btn.className = 'slot-day-btn selected has-pending';
        } else {
          btn.className = 'slot-day-btn has-pending';
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
      fetchLiveAvailabilityForViewMonth();
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
      fetchLiveAvailabilityForViewMonth();
    });
  }

  // Hook into facility changes to refresh slot availability for the newly selected venue
  window.refreshLiveAvailabilityForCurrentFacility = function () {
    fetchLiveAvailabilityForViewMonth();
  };

  // Single Day / Multi-Day Range Mode Handlers
  if (btnModeSingle) {
    btnModeSingle.addEventListener('click', () => {
      mode = 'single';
      rangeWaitingForEnd = false;
      btnModeSingle.classList.add('active');
      if (btnModeRange) btnModeRange.classList.remove('active');
      if (endDateWrap) {
        endDateWrap.classList.remove('active-focus');
        endDateWrap.style.boxShadow = '';
        endDateWrap.style.borderColor = '';
      }
      selectedEnd = { ...selectedStart };
      renderSelectionDetails();
      updateGridHighlights();
    });
  }

  if (btnModeRange) {
    btnModeRange.addEventListener('click', () => {
      mode = 'range';
      rangeWaitingForEnd = true;
      btnModeRange.classList.add('active');
      if (btnModeSingle) btnModeSingle.classList.remove('active');
      if (endDateWrap) {
        endDateWrap.classList.add('active-focus');
        endDateWrap.style.boxShadow = '0 0 0 3px rgba(23, 84, 50, 0.18)';
        endDateWrap.style.borderColor = '#175432';
      }
      if (statusDesc) {
        statusDesc.textContent = `Range Mode: Start Date is ${formatDisplayDate(selectedStart)}. Please click your desired End Date on the calendar.`;
      }
      renderSelectionDetails();
      updateGridHighlights();
    });
  }

  if (endDateWrap || endInput) {
    (endDateWrap || endInput).addEventListener('click', () => {
      if (mode !== 'range' && btnModeRange) {
        btnModeRange.click();
      } else {
        rangeWaitingForEnd = true;
        if (endDateWrap) {
          endDateWrap.classList.add('active-focus');
          endDateWrap.style.boxShadow = '0 0 0 3px rgba(23, 84, 50, 0.18)';
          endDateWrap.style.borderColor = '#175432';
        }
        if (statusDesc) {
          statusDesc.textContent = `Click your desired End Date on the calendar.`;
        }
      }
    });
  }

  if (startInput) {
    startInput.addEventListener('click', () => {
      if (mode === 'range') {
        rangeWaitingForEnd = false;
        if (endDateWrap) {
          endDateWrap.classList.remove('active-focus');
          endDateWrap.style.boxShadow = '';
          endDateWrap.style.borderColor = '';
        }
        if (statusDesc) {
          statusDesc.textContent = `Click a new Start Date on the calendar.`;
        }
      }
    });
  }

  // Initial Calendar Setup
  updateMonthHeader();
  renderCalendarGrid();
  renderSelectionDetails();
  fetchLiveAvailabilityForViewMonth();
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

/* ==========================================================================
   7. Compact Themed Department / Unit Dropdown Selector
   ========================================================================== */
function initCustomDeptDropdown() {
  const configs = [
    {
      wrap: document.getElementById('hallDeptSelectWrap'),
      trigger: document.getElementById('deptSelectTrigger'),
      triggerText: document.getElementById('deptTriggerText'),
      search: document.getElementById('deptSearchInput'),
      scroll: document.getElementById('deptOptionsScroll'),
      hiddenInput: document.getElementById('divisionSelect'),
      otherWrap: document.getElementById('divisionOtherWrap'),
      otherInput: document.getElementById('divisionOtherInput')
    },
    {
      wrap: document.getElementById('dormDeptSelectWrap'),
      trigger: document.getElementById('dormDeptSelectTrigger'),
      triggerText: document.getElementById('dormDeptTriggerText'),
      search: document.getElementById('dormDeptSearchInput'),
      scroll: document.getElementById('dormDeptOptionsScroll'),
      hiddenInput: document.getElementById('dormDivisionUnit'),
      otherWrap: document.getElementById('dormDivisionOtherWrap'),
      otherInput: document.getElementById('dormDivisionOtherInput')
    }
  ];

  configs.forEach(cfg => {
    if (!cfg.wrap || !cfg.trigger) return;

    // Toggle dropdown open state
    cfg.trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = cfg.wrap.classList.contains('open');

      // Close all custom dept dropdowns first
      document.querySelectorAll('.dept-select-wrap.open').forEach(w => {
        w.classList.remove('open');
        const trig = w.querySelector('.dept-select-trigger');
        if (trig) trig.setAttribute('aria-expanded', 'false');
      });

      if (!isOpen) {
        cfg.wrap.classList.add('open');
        cfg.trigger.setAttribute('aria-expanded', 'true');
        if (cfg.search) {
          cfg.search.value = '';
          filterItems(cfg, '');
          setTimeout(() => cfg.search.focus(), 60);
        }
      }
    });

    // Real-time live search filtering
    if (cfg.search) {
      cfg.search.addEventListener('input', (e) => {
        filterItems(cfg, e.target.value.toLowerCase().trim());
      });
      cfg.search.addEventListener('click', (e) => e.stopPropagation());
    }

    // Option item click handling
    if (cfg.scroll) {
      cfg.scroll.addEventListener('click', (e) => {
        const item = e.target.closest('.dept-option-item');
        if (!item) return;

        const val = item.getAttribute('data-value');
        cfg.scroll.querySelectorAll('.dept-option-item').forEach(it => it.classList.remove('selected'));
        item.classList.add('selected');

        if (cfg.triggerText) {
          cfg.triggerText.textContent = val === 'Other' ? 'Other (Manual Entry)' : val;
        }

        if (cfg.hiddenInput) {
          cfg.hiddenInput.value = val;
          // Trigger change event for any legacy listeners
          cfg.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Show/hide manual "Other" input
        if (val === 'Other') {
          if (cfg.otherWrap) cfg.otherWrap.style.display = 'block';
          if (cfg.otherInput) cfg.otherInput.focus();
        } else {
          if (cfg.otherWrap) cfg.otherWrap.style.display = 'none';
        }

        cfg.wrap.classList.remove('open');
        cfg.trigger.setAttribute('aria-expanded', 'false');

        // Update booking state & live review cards
        const effectiveDiv = val === 'Other' 
          ? (cfg.otherInput?.value?.trim() || 'Other (Unspecified Department)')
          : val;
        bookingState.division = effectiveDiv;
        const mainDivInput = document.getElementById('divisionInput');
        if (mainDivInput) mainDivInput.value = effectiveDiv;

        if (typeof updateReviewSummary === 'function') {
          updateReviewSummary();
        }
      });
    }

    // If other input is typed into, keep bookingState & summaries synced
    if (cfg.otherInput) {
      cfg.otherInput.addEventListener('input', () => {
        const customVal = cfg.otherInput.value.trim() || 'Other (Unspecified Department)';
        bookingState.division = customVal;
        const mainDivInput = document.getElementById('divisionInput');
        if (mainDivInput) mainDivInput.value = customVal;
        if (typeof updateReviewSummary === 'function') {
          updateReviewSummary();
        }
      });
    }
  });

  // Filter helper
  function filterItems(cfg, term) {
    if (!cfg.scroll) return;
    const items = cfg.scroll.querySelectorAll('.dept-option-item');
    const groupTitles = cfg.scroll.querySelectorAll('.dept-group-title');

    items.forEach(it => {
      const txt = it.textContent.toLowerCase();
      if (!term || txt.includes(term)) {
        it.classList.remove('hidden');
      } else {
        it.classList.add('hidden');
      }
    });

    // Hide group headers if all children below are hidden
    groupTitles.forEach(header => {
      let nextEl = header.nextElementSibling;
      let hasVisibleChild = false;
      while (nextEl && !nextEl.classList.contains('dept-group-title')) {
        if (nextEl.classList.contains('dept-option-item') && !nextEl.classList.contains('hidden')) {
          hasVisibleChild = true;
          break;
        }
        nextEl = nextEl.nextElementSibling;
      }
      header.style.display = (!term || hasVisibleChild) ? '' : 'none';
    });
  }

  // Close dropdowns on click outside or Escape
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.dept-select-wrap')) {
      document.querySelectorAll('.dept-select-wrap.open').forEach(w => {
        w.classList.remove('open');
        const trig = w.querySelector('.dept-select-trigger');
        if (trig) trig.setAttribute('aria-expanded', 'false');
      });
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.dept-select-wrap.open').forEach(w => {
        w.classList.remove('open');
        const trig = w.querySelector('.dept-select-trigger');
        if (trig) trig.setAttribute('aria-expanded', 'false');
      });
    }
  });
}
