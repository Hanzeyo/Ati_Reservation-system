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

// Global navigation handle
let goToStep = null;

// Global state for Venue / Facility Booking
const bookingState = {
  currentStep: 1,
  selectedFacility: {
    id: 'function-hall',
    name: 'Serrano Function Hall',
    rate: '₱5,000/day',
    capacity: '200 PAX',
    type: 'halls',
    setupName: 'Theater Setup',
    setupCap: '200 PAX',
    location: 'Main Administration Building • Ground Floor'
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
   1. Facility Selection & All 6 Hall Layouts (In Order)
   ========================================================================== */
const hallLayoutData = {
  'function-hall': {
    title: 'Serrano Function Hall',
    location: 'Main Administration Building • Ground Floor',
    desc: 'Flagship multi-purpose event auditorium with central aircon, stage lighting, high-power sound system, and VIP holding lounge.',
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
  'training-hall-b': {
    title: 'Training Hall B (Agri-Fisheries)',
    location: 'Agri-Fisheries Building • 1st Floor',
    desc: 'Specialized technical training venue equipped with demonstration tables, wet lab access, and technical teaching equipment.',
    rate: '₱3,000 / day',
    layouts: [
      { id: 'thb-workshop', name: 'Workshop Benches', cap: '50 PAX', available: true, desc: 'Hands-on practical workstations for agricultural trainees' },
      { id: 'thb-lecture', name: 'Technical Lecture', cap: '80 PAX', available: true, desc: 'Standard lecture seating facing dual laser display' },
      { id: 'thb-demo', name: 'Demonstration Lab', cap: '50 PAX', available: true, desc: 'Central demonstration platform with perimeter seating' },
      { id: 'thb-modular', name: 'Modular Group Pods', cap: '60 PAX', available: false, desc: 'Interactive workshop configuration (Book other date)' },
      { id: 'thb-av', name: 'AV Presentation', cap: '70 PAX', available: true, desc: 'High-definition multimedia training setup' },
      { id: 'thb-hybrid', name: 'Hybrid Training', cap: '45 PAX', available: true, desc: 'Webinar broadcast setup with multi-camera recording' }
    ]
  },
  'four-h-center': {
    title: '4-H Learning Center',
    location: '4-H Youth Building • 2nd Floor',
    desc: 'Configurable multi-purpose training hall for youth agricultural leadership, digital farmer programs, and group forums.',
    rate: '₱3,000 / day',
    layouts: [
      { id: '4h-workshop', name: 'Multi-Purpose Workshop', cap: '70 PAX', available: true, desc: 'Flexible modular tables with mobile whiteboards' },
      { id: '4h-youth', name: 'Youth Delegation Pods', cap: '60 PAX', available: true, desc: 'Collaborative team clusters for youth delegates' },
      { id: '4h-circle', name: 'Seminar Circle', cap: '50 PAX', available: false, desc: 'Town hall circular discussion layout (Book other date)' },
      { id: '4h-digital', name: 'Digital Agri Display', cap: '60 PAX', available: true, desc: 'Smart screen workstation layout for smart farming apps' },
      { id: '4h-theater', name: 'Forum Theater', cap: '80 PAX', available: true, desc: 'Auditorium style seating for youth assemblies' },
      { id: '4h-collab', name: 'Group Collaboration', cap: '60 PAX', available: true, desc: 'Brainstorming layout with interactive 4K display' }
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
  },
  'mess-hall': {
    title: 'ATI Mess Hall & Dining Pavilion',
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
  }
};

let currentModalFacilityId = null;
let modalTempSelectedItem = null;

function initFacilitySelection() {
  const cards = document.querySelectorAll('.facility-choice-card');

  cards.forEach(card => {
    const selectBtn = card.querySelector('.btn-select-facility');

    function selectCard(openModal = true) {
      cards.forEach(c => {
        c.classList.remove('selected');
        const btn = c.querySelector('.btn-select-facility');
        if (btn) {
          btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Setup &amp; View Floor Plan`;
        }
      });

      card.classList.add('selected');
      const hallId = card.dataset.id;
      const hallData = hallLayoutData[hallId] || hallLayoutData['function-hall'];
      const defaultLayout = hallData.layouts[0];

      bookingState.selectedFacility = {
        id: hallId,
        name: card.dataset.name || hallData.title,
        rate: card.dataset.rate || hallData.rate,
        capacity: defaultLayout.cap,
        type: 'halls',
        setupId: defaultLayout.id,
        setupName: defaultLayout.name,
        setupCap: defaultLayout.cap,
        location: hallData.location,
        setupRate: card.dataset.rate || hallData.rate,
        occupiedOnDefaultDate: false
      };

      if (openModal) {
        openFacilityModal(hallId);
      } else {
        updateReviewSummary();
      }
    }

    if (selectBtn) {
      selectBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        selectCard(true);
      });
    }

    card.addEventListener('click', () => {
      selectCard(true);
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

  // Pre-initialize default Function Hall setup badge
  const defaultHallCard = document.querySelector('.facility-choice-card.selected[data-id="function-hall"]');
  if (defaultHallCard) {
    const defaultLayout = hallLayoutData['function-hall'].layouts[0];
    bookingState.selectedFacility = {
      id: 'function-hall',
      name: 'Serrano Function Hall',
      rate: '₱5,000 / day',
      capacity: defaultLayout.cap,
      type: 'halls',
      setupId: defaultLayout.id,
      setupName: defaultLayout.name,
      setupCap: defaultLayout.cap,
      location: hallLayoutData['function-hall'].location,
      setupRate: '₱5,000 / day',
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

  // Pre-select facility from URL parameter if provided
  const urlParamFacility = new URLSearchParams(window.location.search).get('facility') || new URLSearchParams(window.location.search).get('hall');
  if (urlParamFacility && hallLayoutData[urlParamFacility]) {
    const targetCard = document.querySelector(`.facility-choice-card[data-id="${urlParamFacility}"]`);
    if (targetCard) {
      document.querySelectorAll('.facility-choice-card').forEach(c => {
        c.classList.remove('selected');
        const b = c.querySelector('.hall-selected-setup-badge');
        if (b) b.style.display = 'none';
        const bt = c.querySelector('.btn-select-facility');
        if (bt) bt.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg> Select Setup & View Floor Plan`;
      });
      targetCard.classList.add('selected');
      const hData = hallLayoutData[urlParamFacility];
      const dLayout = hData.layouts[0];
      bookingState.selectedFacility = {
        id: urlParamFacility,
        name: targetCard.dataset.name || hData.title,
        rate: targetCard.dataset.rate || hData.rate,
        capacity: dLayout.cap,
        type: 'halls',
        setupId: dLayout.id,
        setupName: dLayout.name,
        setupCap: dLayout.cap,
        location: hData.location,
        setupRate: targetCard.dataset.rate || hData.rate,
        occupiedOnDefaultDate: false
      };
      const badge = targetCard.querySelector('.hall-selected-setup-badge');
      const badgeText = targetCard.querySelector('.d-room-text');
      if (badge && badgeText) {
        badgeText.textContent = `✓ ${dLayout.name} Selected (${dLayout.cap})`;
        badge.style.display = 'flex';
      }
      const btn = targetCard.querySelector('.btn-select-facility');
      if (btn) {
        btn.innerHTML = `✓ ${dLayout.name} Selected (Click to change)`;
      }
      updateReviewSummary();
    }
  }
}

function openFacilityModal(facilityId) {
  const data = hallLayoutData[facilityId];
  if (!data) return;

  currentModalFacilityId = facilityId;
  modalTempSelectedItem = null;

  const modal = document.getElementById('roomSelectionModal');
  const catPill = document.getElementById('modalCategoryPill');
  const titleEl = document.getElementById('modalDormTitle');
  const floorEl = document.getElementById('modalDormFloor');
  const rateEl = document.getElementById('modalDormRate');
  const gridEl = document.getElementById('modalRoomsGrid');
  const feedbackBar = document.getElementById('modalRoomFeedback');
  const legendAvail = document.getElementById('modalLegendAvailText');
  const legendRes = document.getElementById('modalLegendResText');

  if (feedbackBar) feedbackBar.style.display = 'none';

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
      'thb-workshop': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
      'thb-lecture': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M12 16v4"/></svg>',
      'thb-demo': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>',
      'thb-modular': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>',
      'thb-av': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>',
      'thb-hybrid': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>',
      '4h-workshop': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
      '4h-youth': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      '4h-circle': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>',
      '4h-digital': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
      '4h-theater': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M7 18h10M3 21h18"/></svg>',
      '4h-collab': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
      'eb-board': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="10" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
      'eb-videoconf': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>',
      'eb-briefing': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>',
      'eb-hearing': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>',
      'eb-strategy': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
      'eb-delegation': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>',
      'mh-buffet': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>',
      'mh-banquet': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="7"/><circle cx="12" cy="12" r="3"/></svg>',
      'mh-cafeteria': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="18" height="6" rx="1"/><rect x="3" y="15" width="18" height="3" rx="1"/></svg>',
      'mh-patio': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
      'mh-mixer': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="6 2 18 2 12 11 12 22"/><line x1="8" y1="22" x2="16" y2="22"/></svg>',
      'mh-fellowship': '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polygon points="10 8 16 12 10 16 10 8"/></svg>',
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
        <div class="dorm-room-hint ${l.available ? 'ready' : 'alt-date'}">${l.available ? 'Ready Oct 5' : 'Book other date'}</div>
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
            if (fTitle) fTitle.textContent = `${l.name} Selected (Reserved on Oct 5)`;
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
        badgeText.textContent = `✓ ${layout.name} Selected (Reserved Oct 5 • Pick other date)`;
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

  closeFacilityModal();
  updateReviewSummary();

  const summaryCard = document.getElementById('step1SummaryCard');
  if (summaryCard) {
    summaryCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }
}
const confirmRoomSelection = confirmFacilitySelection;

/* ==========================================================================
   2. Filter & Category Selection (Facility Halls & Venues)
   ========================================================================== */
function initFacilityFilters() {
  const facilityCards = document.querySelectorAll('.facility-choice-card');
  const sectionHeading = document.getElementById('categorySectionHeading');
  const activeCategoryName = document.getElementById('activeCategoryName');

  if (activeCategoryName) {
    activeCategoryName.textContent = 'Halls & Venues (6 Available)';
  }

  if (sectionHeading) {
    sectionHeading.textContent = 'AVAILABLE FACILITY VENUES & HALLS (6):';
  }

  // Ensure all 6 hall cards remain visible in order
  facilityCards.forEach(card => {
    card.style.display = 'flex';
  });
}

/* ==========================================================================
   3. Stepper Wizard Flow (Dedicated to Facility Venue Booking)
   ========================================================================== */
function initStepperNavigation() {
  const stepButtons = document.querySelectorAll('.step-tab-btn');
  const stepViews = document.querySelectorAll('.wizard-step-view');
  const proceedBtn = document.getElementById('btnProceedStep');
  const backBtn = document.getElementById('btnStepBack');
  const bottomActions = document.getElementById('bookingBottomActions') || document.querySelector('.booking-bottom-actions');

  function updateStepperMode() {
    const stepTabEvent = document.getElementById('stepTabEventDetails');
    const stepTabDocs = document.getElementById('stepTabDocuments');
    const stepTabRev = document.getElementById('stepTabReview');
    const dotEvent = document.getElementById('mobileDotEventDetails');
    const dotDocs = document.getElementById('mobileDotDocuments');
    const dotRev = document.getElementById('mobileDotReview');

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

    // Step 3 Labels for Venue & Activity Booking
    const step3Title = document.getElementById('step3FormTitle');
    const step3Desc = document.getElementById('step3FormDesc');
    const eventTitleLabel = document.getElementById('eventTitleLabel');
    const eventTitleInput = document.getElementById('eventTitleInput');
    const eventPaxLabel = document.getElementById('eventPaxLabel');
    const eventPaxInput = document.getElementById('eventPaxInput');
    const specialNotesLabel = document.getElementById('specialNotesLabel');
    const specialNotes = document.getElementById('specialNotes');

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

  goToStep = function (stepNumber) {
    if (stepNumber < 1 || stepNumber > 5) return;

    // Requirement: User MUST select a venue layout setup before proceeding to Step 2
    if (stepNumber === 2 && bookingState.currentStep === 1) {
      if (!bookingState.selectedFacility?.setupName) {
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
          window.selectAlternateAvailableDateForRoom(bookingState.selectedFacility?.setupName);
        }
      } else {
        if (typeof window.selectCurrentDate === 'function') {
          window.selectCurrentDate();
        }
      }
    }

    bookingState.currentStep = stepNumber;
    updateStepperMode();

    // Update Desktop Stepper Cards
    stepButtons.forEach((btn) => {
      const stepIdx = parseInt(btn.dataset.step, 10);
      btn.classList.remove('active', 'completed');
      if (stepIdx === stepNumber) {
        btn.classList.add('active');
      } else if (stepIdx < stepNumber) {
        btn.classList.add('completed');
      }
    });

    // Update Mobile Compact Progress Bar (5 steps)
    const totalSteps = 5;
    const stepLabels = {
      1: 'Venue Selection',
      2: 'Date & Time Selection',
      3: 'Event & Activity Details',
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
    if (mobileName) mobileName.textContent = stepLabels[stepNumber] || 'Booking Details';
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

    if (bottomActions) {
      bottomActions.style.display = 'flex';
    }

    if (backBtn) {
      backBtn.style.display = stepNumber > 1 ? 'inline-flex' : 'none';
    }

    if (proceedBtn) {
      if (stepNumber === 1) {
        proceedBtn.style.display = 'inline-flex';
        const hallSetup = bookingState.selectedFacility?.setupName;
        const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
        if (hallSetup) {
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
        proceedBtn.innerHTML = `<span>Review &amp; Confirm Booking</span> <svg viewBox="0 0 24 24" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>`;
      } else if (stepNumber === 5) {
        proceedBtn.style.display = 'none';
      }
    }

    window.scrollTo({ top: 120, behavior: 'smooth' });
    updateReviewSummary();
  };

  window.goToStep = goToStep;

  // Proceed button click listener
  if (proceedBtn) {
    proceedBtn.addEventListener('click', () => {
      if (bookingState.currentStep === 1) {
        if (!bookingState.selectedFacility?.setupName) {
          alert('Please choose a venue layout & setup (e.g. Theater Setup, Classroom Setup, Banquet Dining) before proceeding to Date & Time Selection.');
          const card = document.querySelector('.facility-choice-card.selected');
          if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }
        goToStep(2);
      } else if (bookingState.currentStep === 2) {
        goToStep(3);
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
      const refNum = `ATI-BK-2026-${Math.floor(1000 + Math.random() * 9000)}`;

      // Persist to localStorage for live sync with Your Bookings (my_reservations.php)
      try {
        const stored = JSON.parse(localStorage.getItem('ati_facility_bookings') || '[]');
        const newBooking = {
          id: Date.now(),
          ref: refNum,
          type: 'facility',
          title: bookingState.eventTitle || `${bookingState.selectedFacility?.name || 'Facility'} Booking`,
          facility: bookingState.selectedFacility?.name || 'Facility',
          facilityKey: bookingState.selectedFacility?.id || 'function-hall',
          setupName: bookingState.selectedFacility?.setupName || 'Standard Layout',
          setupCap: bookingState.selectedFacility?.setupCap || '100 PAX',
          date: bookingState.dateRaw || '2026-10-20',
          time: bookingState.time || '08:00 AM - 05:00 PM',
          division: document.getElementById('divisionInput')?.value || 'Career Development Division (CDD)',
          attendees: bookingState.participants ? `${bookingState.participants} Attendees` : '120 Attendees',
          status: 'Pending Administrative Clearance',
          statusCode: 'pending',
          notes: document.getElementById('specialNotes')?.value || ''
        };
        stored.unshift(newBooking);
        localStorage.setItem('ati_facility_bookings', JSON.stringify(stored));

        // Also sync to global reservations list tagged as facility
        const sysRes = JSON.parse(localStorage.getItem('ati_system_reservations') || '[]');
        sysRes.unshift({
          id: Date.now(),
          ref: refNum,
          type: 'facility',
          title: newBooking.title,
          facility: newBooking.facility,
          facilityKey: newBooking.facilityKey,
          date: newBooking.date,
          time: newBooking.time,
          division: newBooking.division,
          attendees: newBooking.attendees,
          status: 'Pending Administrative Clearance'
        });
        localStorage.setItem('ati_system_reservations', JSON.stringify(sysRes));
      } catch (err) {
        console.warn('Could not save to localStorage:', err);
      }

      let summaryMsg = `Official Facility Booking Request Submitted Successfully!\n\nReference: ${refNum}\nVenue: ${bookingState.selectedFacility.name}\nLayout Setup: ${bookingState.selectedFacility.setupName} (${bookingState.selectedFacility.setupCap})`;
      if (bookingState.eventTitle) {
        summaryMsg += `\nActivity Title: ${bookingState.eventTitle}`;
      }
      summaryMsg += `\nSchedule: ${bookingState.date}\nTime Slot: ${bookingState.timeSlot}\nStatus: Pending Administrative Review\n\nNotification has been sent to your registered email.`;
      alert(summaryMsg);
      window.location.href = 'my_reservations.php';
    });
  }

  // Initial stepper setup
  updateStepperMode();
}

/* ==========================================================================
   4. Update Summary on Step 1 (Preview) and Step 5 (Official Summary)
   ========================================================================== */
function updateReviewSummary() {
  const hallSetup = bookingState.selectedFacility?.setupName;
  const hallCap = bookingState.selectedFacility?.setupCap;
  const facilityName = bookingState.selectedFacility?.name || 'Serrano Function Hall';
  const rateText = bookingState.selectedFacility?.setupRate || bookingState.selectedFacility?.rate || '₱5,000 / day';
  const capText = hallCap || bookingState.selectedFacility?.capacity || '150 - 200 PAX';
  const locationText = bookingState.selectedFacility?.location || hallLayoutData[bookingState.selectedFacility?.id]?.location || 'Main Administration Building • Ground Floor';

  // 1. Step 1 Summary Preview Card
  const prevBadge = document.getElementById('summaryPreviewBadge');
  const prevTitle = document.getElementById('summaryPreviewTitle');
  const prevRate = document.getElementById('summaryPreviewRate');
  const prevRoomWrap = document.getElementById('summaryPreviewRoomWrap');
  const prevRoom = document.getElementById('summaryPreviewRoom');
  const prevUnitLabel = document.getElementById('summaryPreviewUnitLabel');
  const prevCap = document.getElementById('summaryPreviewCap');

  if (prevBadge) prevBadge.textContent = 'SELECTED VENUE';
  if (prevTitle) prevTitle.textContent = facilityName;
  if (prevRate) prevRate.textContent = rateText;
  if (prevCap) prevCap.textContent = capText;

  if (prevRoomWrap && prevRoom) {
    if (hallSetup) {
      if (prevUnitLabel) prevUnitLabel.textContent = 'Assigned Setup & Layout:';
      prevRoomWrap.style.display = 'flex';
      const isOcc = bookingState.selectedFacility?.occupiedOnDefaultDate;
      if (isOcc) {
        prevRoom.innerHTML = `${hallSetup} (${hallCap}) <span style="display:inline-block; margin-left:6px; font-size:0.75rem; color:#dc2626; font-weight:700;">(Reserved Oct 5 • Pick alternate date in Step 2)</span>`;
      } else {
        prevRoom.textContent = `${hallSetup} (${hallCap})`;
      }
    } else {
      prevRoomWrap.style.display = 'none';
      prevRoom.textContent = 'None';
    }
  }

  // 2. Step 5 Official Review Summary Card
  const sumBadge = document.getElementById('summaryOfficialBadge') || document.getElementById('summaryFacilityBadge');
  const sumVenue = document.getElementById('summaryVenueName');
  const sumRate = document.getElementById('summaryVenueRate');
  const sumRoomWrap = document.getElementById('summaryRoomDetailWrap');
  const sumRoom = document.getElementById('summaryRoomDetail');
  const sumRoomLabel = document.getElementById('summaryRoomDetailLabel');
  const sumFacType = document.getElementById('summaryFacilityType');
  const sumCap = document.getElementById('summaryVenueCapacity');
  const sumDate = document.getElementById('summaryReservationDate');
  const sumTime = document.getElementById('summaryTimeSlot');

  if (sumBadge) sumBadge.textContent = 'OFFICIAL FACILITY BOOKING PARTICULARS';
  if (sumVenue) sumVenue.textContent = facilityName;
  if (sumRate) sumRate.textContent = rateText;
  if (sumFacType) sumFacType.textContent = locationText;
  if (sumCap) sumCap.textContent = capText;
  if (sumDate) sumDate.textContent = bookingState.date || '10/05/2026';
  if (sumTime) sumTime.textContent = bookingState.timeSlot || 'Whole Day (8:00 AM - 5:00 PM)';

  if (sumRoomWrap && sumRoom) {
    if (hallSetup) {
      if (sumRoomLabel) sumRoomLabel.textContent = 'Assigned Venue Setup:';
      sumRoomWrap.style.display = 'flex';
      sumRoom.textContent = `${hallSetup} (${hallCap})`;
    } else {
      sumRoomWrap.style.display = 'none';
      sumRoom.textContent = 'Standard Layout';
    }
  }

  // Step 5 Event Details Summary fields
  const eventTitleInput = document.getElementById('eventTitleInput');
  const eventPaxInput = document.getElementById('eventPaxInput');
  const sumEventTitle = document.getElementById('summaryEventTitle');
  const sumPaxValue = document.getElementById('summaryPaxValue');
  const sumEventLabel = document.getElementById('summaryEventLabel');
  const sumPaxLabel = document.getElementById('summaryPaxLabel');

  const curTitle = eventTitleInput?.value?.trim() || bookingState.eventTitle || 'Regional Agricultural Training Workshop';
  const curPax = eventPaxInput?.value?.trim() || bookingState.participants || (hallCap || '120 Attendees');

  if (sumEventLabel) sumEventLabel.textContent = 'Activity / Event Title:';
  if (sumEventTitle) sumEventTitle.textContent = curTitle;
  if (sumPaxLabel) sumPaxLabel.textContent = 'Participants / Attendees:';
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
          statusDesc.textContent = `Room ${roomNum} is confirmed available on ${formatDisplayDate(selectedStart)} (currently occupied on Oct 5–6).`;
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

    const isRoomOccupiedThisDate = Boolean(bookingState.selectedFacility?.occupiedOnDefaultDate) && (key === '2026-10-05' || key === '2026-10-06');
    if (btn.classList.contains('reserved') || RESERVED_DATES.has(key) || isRoomOccupiedThisDate) {
      if (isRoomOccupiedThisDate) {
        const roomNum = bookingState.selectedFacility?.roomNumber;
        alert(`Room ${roomNum} is occupied on ${formatDisplayDate(dateObj)}.\n\nPlease select an available green slot (such as Wednesday, October 7 onwards) to book this room.`);
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
          const isRoomOccupiedRangeDate = Boolean(bookingState.selectedFacility?.occupiedOnDefaultDate) && (cKey === '2026-10-05' || cKey === '2026-10-06');
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
      const isRoomOccupiedThisDate = Boolean(bookingState.selectedFacility?.occupiedOnDefaultDate) && (key === '2026-10-05' || key === '2026-10-06');
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
  window.selectCurrentDate = function () {
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

  // Helper when user chose an occupied room: pre-select earliest open date (October 7, 2026)
  window.selectAlternateAvailableDateForRoom = function (roomNumber) {
    viewYear = 2026;
    viewMonth = 9; // October 2026
    selectedStart = { year: 2026, month: 9, day: 7 };
    selectedEnd = { year: 2026, month: 9, day: 7 };
    mode = 'single';
    rangeWaitingForEnd = false;
    if (btnModeSingle) btnModeSingle.classList.add('active');
    if (btnModeRange) btnModeRange.classList.remove('active');
    if (endDateWrap) endDateWrap.classList.remove('active-focus');
    updateMonthHeader();
    renderCalendarGrid();
    renderSelectionDetails();

    if (statusTitle) {
      statusTitle.textContent = `Room ${roomNumber} Available (October 7, 2026)`;
    }
    if (statusDesc) {
      statusDesc.textContent = `Room ${roomNumber} is occupied on Oct 5–6, 2026. Wednesday, October 7, 2026 has been automatically selected as the earliest open date. You can choose any upcoming green slot on the calendar.`;
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

