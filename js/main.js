/**
 * ATI Facility Reservation System - Main Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initHeroSlider();
  initCalendar();
  initModals();
  initMobileMenu();
  initNavigationScrollSpy();
  initRealtimeOfficeHours();
  initVerticalFacilitiesScroller();
  initGmailSmartLink();
  initPhoneHotlineDirectDial();
});

/* ==========================================================================
   1. Hero Showcase Slider
   ========================================================================== */
function initHeroSlider() {
  const slides = document.querySelectorAll('.showcase-slide');
  const dots = document.querySelectorAll('.showcase-dot');
  const pillBadge = document.getElementById('heroPillBadge');
  const statusBadge = document.getElementById('heroStatusBadge') || document.querySelector('.showcase-status-badge');
  const statusText = document.getElementById('showcaseStatusText') || (statusBadge ? statusBadge.querySelector('span:last-child') : null);

  if (!slides.length) return;

  const showcaseItems = [
    { name: 'Carandang Hall', type: 'Featured Facility' },
    { name: 'Orosa Hall', type: 'Featured Facility' },
    { name: 'RDEC Training Hall 1', type: 'Featured Facility' },
    { name: 'RDEC Training Hall 2', type: 'Featured Facility' },
    { name: 'Function Hall', type: 'Featured Facility' },
    { name: 'Serrano Hall', type: 'Featured Facility' },
    { name: 'VIP Suite', type: 'Featured Dormitories' },
    { name: 'Dormitory Suite 1', type: 'Featured Dormitories' },
    { name: 'Dormitory Suite 2', type: 'Featured Dormitories' }
  ];

  let currentIndex = 0;
  let intervalTimer = null;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });

    if (dots && dots.length) {
      dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
      });
    }

    const currentItem = showcaseItems[index];
    if (currentItem) {
      if (pillBadge) {
        pillBadge.textContent = currentItem.name;
      }
      if (statusText) {
        statusText.textContent = currentItem.type;
      }
      if (statusBadge) {
        const isDorm = currentItem.type.includes('Dorm');
        statusBadge.classList.toggle('is-dorm', isDorm);
      }
    }

    currentIndex = index;
  }

  function startAutoCycle() {
    stopAutoCycle();
    intervalTimer = setInterval(() => {
      let nextIndex = (currentIndex + 1) % slides.length;
      showSlide(nextIndex);
    }, 2000);
  }

  function stopAutoCycle() {
    if (intervalTimer) clearInterval(intervalTimer);
  }

  if (dots && dots.length) {
    dots.forEach(dot => {
      dot.addEventListener('click', (e) => {
        const targetIndex = parseInt(e.target.dataset.index, 10);
        showSlide(targetIndex);
        startAutoCycle();
      });
    });
  }

  const heroCard = document.querySelector('.hero-card');
  if (heroCard) {
    heroCard.addEventListener('mouseenter', stopAutoCycle);
    heroCard.addEventListener('mouseleave', startAutoCycle);
  }

  // Initial state
  showSlide(0);
  startAutoCycle();
}

/* ==========================================================================
   2. Interactive Calendar & Real-Time Availability Engine
   ========================================================================== */
function initCalendar() {
  const daysGrid = document.getElementById('calendarDaysGrid');
  const currentMonthLabel = document.getElementById('calendarCurrentMonth');
  const prevMonthBtn = document.getElementById('prevMonthBtn');
  const nextMonthBtn = document.getElementById('nextMonthBtn');

  const emptyState = document.getElementById('previewEmptyState');
  const activeState = document.getElementById('previewActiveState');
  const activeDateTitle = document.getElementById('previewDateTitle');
  const activeDateDay = document.getElementById('previewDateDay');
  const slotList = document.getElementById('previewSlotList');

  // Base state defaults to current date
  const _today = new Date();
  let currentDate = new Date(_today.getFullYear(), _today.getMonth(), 1);
  let selectedDate = _today.getDate();

  // In-memory cache for real-time live availability from MySQL
  let liveAvailabilityCache = {};
  let isFetching = false;

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const weekdayNames = [
    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
  ];

  // Default facility list while API is loading
  const defaultHalls = [
    { facility_id: 12, facility_name: 'Function Hall', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' },
    { facility_id: 8, facility_name: 'Carandang Room', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' },
    { facility_id: 9, facility_name: 'Orosa Hall', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' },
    { facility_id: 10, facility_name: 'RDEC Training Hall 1', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' },
    { facility_id: 11, facility_name: 'RDEC Training Hall 2', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' },
    { facility_id: 13, facility_name: 'Serrano Hall', status: 'available', status_label: 'AVAILABLE', details: 'Available All Day' }
  ];

  function fetchLiveAvailability(year, month, shouldRerender = true) {
    const cacheKey = `${year}-${month + 1}`;
    isFetching = true;

    fetch(`ajax/get_realtime_availability.php?year=${year}&month=${month + 1}`)
      .then(res => res.json())
      .then(data => {
        isFetching = false;
        if (data && data.status === 'success') {
          liveAvailabilityCache[cacheKey] = data;
          if (shouldRerender) {
            renderCalendar();
          }
        }
      })
      .catch(err => {
        isFetching = false;
        console.warn('Real-time availability fetch notice:', err);
      });
  }

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const cacheKey = `${year}-${month + 1}`;
    const liveData = liveAvailabilityCache[cacheKey];

    if (currentMonthLabel) {
      currentMonthLabel.textContent = `${monthNames[month]} ${year}`;
    }

    daysGrid.innerHTML = '';

    const firstDayIndex = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    // Fill blank days before 1st day of month
    for (let i = 0; i < firstDayIndex; i++) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'cal-day empty';
      daysGrid.appendChild(emptyDiv);
    }

    // Populate actual days
    for (let day = 1; day <= totalDays; day++) {
      const dayBtn = document.createElement('button');
      dayBtn.type = 'button';
      dayBtn.className = 'cal-day';
      dayBtn.textContent = day;

      const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const daySchedule = liveData && liveData.days ? liveData.days[dateKey] : null;

      // Real-time status indicator dot from MySQL database
      if (daySchedule && daySchedule.overall_status) {
        dayBtn.classList.add('has-indicator', `status-${daySchedule.overall_status}`);
      }

      // Selected date state
      if (selectedDate === day) {
        dayBtn.classList.add('selected');
        updateDetailsPanel(day, month, year, daySchedule);
      }

      dayBtn.addEventListener('click', () => {
        document.querySelectorAll('.cal-day').forEach(el => el.classList.remove('selected'));
        dayBtn.classList.add('selected');
        selectedDate = day;
        updateDetailsPanel(day, month, year, daySchedule);
      });

      daysGrid.appendChild(dayBtn);
    }

    // Trailing spacer slots
    const totalRendered = firstDayIndex + totalDays;
    const remainingDays = (7 - (totalRendered % 7)) % 7;
    for (let i = 0; i < remainingDays; i++) {
      const emptyDiv = document.createElement('div');
      emptyDiv.className = 'cal-day empty';
      daysGrid.appendChild(emptyDiv);
    }
  }

  function updateDetailsPanel(day, month, year, daySchedule) {
    if (!emptyState || !activeState) return;

    emptyState.style.display = 'none';
    activeState.style.display = 'block';

    const dateObj = new Date(year, month, day);
    const dayName = weekdayNames[dateObj.getDay()];
    const formattedDate = `${monthNames[month]} ${day}, ${year}`;

    if (activeDateTitle) activeDateTitle.textContent = formattedDate;
    if (activeDateDay) activeDateDay.textContent = `Schedule overview for ${dayName}`;

    if (slotList) {
      let facilitiesToRender = [];

      if (daySchedule && Array.isArray(daySchedule.facilities) && daySchedule.facilities.length) {
        // Prioritize official institutional halls
        const halls = daySchedule.facilities.filter(f => f.facility_type === 'halls');
        facilitiesToRender = halls.length ? halls : daySchedule.facilities;
      } else {
        facilitiesToRender = defaultHalls;
      }

      slotList.innerHTML = facilitiesToRender.map(f => {
        const pillClass = f.status || 'available';
        const pillLabel = (f.status_label || pillClass).toUpperCase();
        const details = f.details || 'Available All Day';

        return `
          <div class="hall-slot-item">
            <div>
              <div class="slot-name">${f.facility_name}</div>
              <small style="color: #64748b;">${details}</small>
            </div>
            <span class="slot-status-pill ${pillClass}">
              ${pillLabel}
            </span>
          </div>
        `;
      }).join('');
    }
  }

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      selectedDate = 1;
      renderCalendar();
      fetchLiveAvailability(currentDate.getFullYear(), currentDate.getMonth());
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      selectedDate = 1;
      renderCalendar();
      fetchLiveAvailability(currentDate.getFullYear(), currentDate.getMonth());
    });
  }

  // Initial render with defaults, then seamlessly stream live availability
  renderCalendar();
  fetchLiveAvailability(currentDate.getFullYear(), currentDate.getMonth());

  // Real-time synchronization: poll every 25 seconds for real-time changes
  setInterval(() => {
    fetchLiveAvailability(currentDate.getFullYear(), currentDate.getMonth(), true);
  }, 25000);
}

/* ==========================================================================
   3. Modals Management (Login & Facility Info)
   ========================================================================== */
function initModals() {
  const loginModal = document.getElementById('loginModal');
  const facilityModal = document.getElementById('facilityModal');

  const openLoginButtons = document.querySelectorAll('.js-open-login');
  const closeButtons = document.querySelectorAll('.js-close-modal');

  openLoginButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      openModal(loginModal);
    });
  });

  closeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      closeAllModals();
    });
  });

  // Facility detail buttons
  const facilityDetailButtons = document.querySelectorAll('.js-view-facility');
  const facilityData = {
    'carandang-hall': {
      title: 'Carandang Hall',
      image: 'assets/images/carandang_room.jpg',
      desc: 'Executive boardroom venue equipped for high-level management conferences, diplomatic delegations, hybrid video conferences, and strategic planning assemblies.',
      capacity: 'Up to 28 Persons',
      features: ['Executive Solid Table & Ergonomic Seating', '4K Hybrid Video-Conferencing Bar', 'Dedicated Digital Presentation Screen', 'High-Speed Secure Wi-Fi']
    },
    'orosa-hall': {
      title: 'Orosa Hall',
      image: 'assets/images/orosa_hall.jpg',
      desc: 'Versatile banquet and seminar hall ideal for specialized workshops, institutional assemblies, and technical presentations.',
      capacity: 'Up to 80 Persons',
      features: ['Dual Laser Projectors & Screens', 'Acoustic Wall Paneling & PA Audio', 'Flexible Classroom or Banquet Seating', 'High-Speed Presentation Wi-Fi']
    },
    'rdec-training-1': {
      title: 'RDEC Training Hall 1',
      image: 'assets/images/rdec_training_1.jpg',
      desc: 'Comprehensive training auditorium with dual display presentation systems, stage podium, and modular classroom layout.',
      capacity: 'Up to 100 Persons',
      features: ['Dual Presentation Monitors', 'Modular Workstations & Power Outlets', 'Front Stage & Lectern Mic', 'Full Air-conditioning']
    },
    'rdec-training-2': {
      title: 'RDEC Training Hall 2',
      image: 'assets/images/rdec_training_2.jpg',
      desc: 'Interactive training facility designed for hands-on technical sessions, collaborative workshops, and agricultural technology demonstrations.',
      capacity: 'Up to 100 Persons',
      features: ['Interactive Display Board', 'Modular Breakout Workstations', 'Surround Sound Audio System', 'Dedicated High-Speed LAN & Wi-Fi']
    },
    'function-hall': {
      title: 'Function Hall',
      image: 'assets/images/function_hall.jpg',
      desc: 'Our premier venue for broad audience events, symposiums, official regional congresses, and stakeholder meetings. Equipped with high-definition presentation screens and dedicated acoustics.',
      capacity: 'Up to 200 Persons',
      features: ['Dual HD Laser Projectors', 'Surround Sound & Lapel Microphones', 'Full Air-conditioning', 'VIP Stage & Elevated Rostrum']
    },
    'serrano-hall': {
      title: 'Serrano Hall',
      image: 'assets/images/serrano_hall.jpg',
      desc: 'Multi-purpose auditorium setup providing flexible configurations for educational seminars, general assemblies, and institutional events.',
      capacity: 'Up to 80 Persons',
      features: ['Full Stage & Lighting Setup', 'Surround Sound PA System', 'Ergonomic Auditorium Seating', 'High-Definition Laser Projection']
    },
    'dorm-vip-suite': {
      title: 'VIP Suite',
      image: 'assets/images/dorm_vip_suite.jpg',
      desc: 'Executive VIP Suite located on the 4th floor equipped with premium wood panel finishes, Queen bed, personal refrigerator, smart HDTV, study workstation, and private ensuite restroom.',
      capacity: 'Executive Private Suite • ₱1,500 / night',
      features: ['Full Air-Conditioning & Smart HDTV', 'Private Ensuite Restroom & Hot Shower', 'Mini Refrigerator & Beverage Station', 'Executive Study Workstation & Wi-Fi']
    },
        'dorm-suite-101': {
      title: 'Executive Suite 101',
      image: 'assets/images/dorm_vip_suite.jpg',
      desc: 'Deluxe executive accommodation with a premium Queen-size bed, private study desk, climate control, ensuite hot shower, and complimentary hospitality amenities.',
      capacity: 'Executive Private Suite • ₱1,500 / night',
      features: ['Queen-Size Deluxe Bed', 'Private Ensuite Restroom & Hot Shower', 'Executive Study Desk & Wi-Fi', 'Full Air-Conditioning & Smart TV']
    },
    'dorm-suite-102': {
      title: 'Premier Suite 102',
      image: 'assets/images/dorm_vip_suite.jpg',
      desc: 'Quiet executive twin suite tailored for visiting specialists, equipped with twin beds, workstations, smart TV, climate control, and private ensuite bath.',
      capacity: 'Twin Executive Suite • ₱1,500 / night',
      features: ['Twin Executive Beds', 'Private Ensuite Restroom & Hot Shower', 'Dedicated Study Workstations', 'Climate Control & High-Speed Wi-Fi']
    },
'dorm-suite-1': {
      title: 'Dormitory Suite 1',
      image: 'assets/images/dorm_suite_1.png',
      desc: 'Comfortable trainee dormitory suite located on the 2nd floor, designed with individual secured storage lockers, study workstations, and comprehensive floor amenities.',
      capacity: 'Air-Conditioned Trainee Suite • ₱500 / night',
      features: ['Full Air-Conditioning System', 'Individual Secured Steel Lockers', 'Personal Study Desks with Power Outlets', 'Shared Restroom & Common Lounge Access']
    },
    'dorm-suite-2': {
      title: 'Dormitory Suite 2',
      image: 'assets/images/dorm_suite_2.png',
      desc: 'Modern trainee dormitory suite located on the 3rd floor offering quiet, air-conditioned accommodations with dedicated study desks and individual secured closets.',
      capacity: 'Air-Conditioned Trainee Suite • ₱500 / night',
      features: ['Full Air-Conditioning System', 'Individual Secured Steel Lockers', 'Study Workstations & Reading Lamps', 'High-Speed Wi-Fi & Lounge Access']
    },
    'dorm-suite-3': {
      title: 'Dormitory Suite 3',
      image: 'assets/images/dormitory.jpg',
      desc: 'Executive trainee quarters located on the 4th floor equipped with individual study carrels, secured personal storage, and high-speed Wi-Fi.',
      capacity: 'Air-Conditioned Trainee Suite • ₱500 / night',
      features: ['Full Air-Conditioning System', 'Individual Secured Steel Lockers', 'Personal Study Desks with Power Outlets', 'Shared Restroom & Common Lounge Access']
    },
    'dorm-male': {
      title: 'Male Trainee Dormitory',
      image: 'assets/images/dorm_suite_1.png',
      desc: 'Dedicated multi-occupancy trainee wing on the ground floor with individual lockers, study stations, and immediate access to training halls.',
      capacity: 'Air-Conditioned Trainee Wing • ₱400 / night',
      features: ['Air-Conditioned Trainee Hall', 'Individual Personal Lockers', 'Study Desks & High-Speed Wi-Fi', 'Spacious Common Washrooms']
    },
    'dorm-female': {
      title: 'Female Trainee Dormitory',
      image: 'assets/images/dorm_suite_2.png',
      desc: 'Quiet, secure trainee accommodation wing on the 2nd floor featuring ergonomic study desks, individual lockers, and full air-conditioning.',
      capacity: 'Air-Conditioned Trainee Wing • ₱400 / night',
      features: ['Air-Conditioned Trainee Hall', 'Individual Personal Lockers', 'Study Desks & High-Speed Wi-Fi', 'Spacious Common Washrooms']
    },
    'guesthouse-1': {
      title: 'Guesthouse 1 (Presidential Villa)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Premier executive guesthouse villa featuring an elegant master bedroom, private ensuite bathroom with hot shower, workstation desk, and direct views of the lush compound gardens.',
      capacity: 'Executive Villa • ₱2,000 / night',
      features: ['Full Air-Conditioning & Smart HDTV', 'Private Ensuite Restroom & Hot Shower', 'Mini Refrigerator & Beverage Bar', 'Executive Workstation & Fast Wi-Fi']
    },
    'guesthouse-2': {
      title: 'Guesthouse 2 (Director’s Lodge)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Spacious residential lodge tailored for senior institutional officials and international delegations, equipped with comfortable seating, study desk, and private veranda.',
      capacity: 'Executive Lodge • ₱2,000 / night',
      features: ['Full Climate Control & Smart TV', 'Ensuite Restroom & Amenities Kit', 'Private Balcony Veranda Access', 'Executive Workstation & Wi-Fi']
    },
    'guesthouse-3': {
      title: 'Guesthouse 3 (Senior Cottage)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Peaceful garden cottage equipped with plush bedding, dining nook, hot shower, high-speed Wi-Fi, and climate control in a private grove.',
      capacity: 'Private Cottage • ₱1,800 / night',
      features: ['Full Air-Conditioning', 'Private Restroom with Water Heater', 'Dining Nook & Refrigerator', 'Peaceful Botanical Garden View']
    },
    'guesthouse-4': {
      title: 'Guesthouse 4 (Resource Residence)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Quiet, secluded accommodation ideal for training resource speakers, guest professors, and visiting technical specialists.',
      capacity: 'Guest Residence • ₱1,800 / night',
      features: ['Air Conditioning & Study Workstation', 'Private Restroom & Hot Shower', 'Smart Television & Wi-Fi', 'Quiet Courtyard Location']
    },
    'guesthouse-5': {
      title: 'Guesthouse 5 (Delegates Home)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Comfortable multi-guest residential unit featuring two private bedrooms, living hall, personal refrigerator, and smart TV.',
      capacity: 'Residential Unit • ₱1,800 / night',
      features: ['Two Private Bedrooms', 'Shared Lounge & Smart TV', 'Private Bathroom & Hot Shower', 'High-Speed Wi-Fi Access']
    },
    'guesthouse-6': {
      title: 'Guesthouse 6 (Fellows Lodge)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Cozy modern lodge featuring twin beds, private bath, dedicated writing desks, and relaxing views of the botanical landscape.',
      capacity: 'Fellows Lodge • ₱1,500 / night',
      features: ['Twin Bed Accommodation', 'Air-Conditioned Comfort', 'Private Restroom & Shower', 'Dedicated Writing Desks']
    },
    'guesthouse-7': {
      title: 'Guesthouse 7 (Lecturer Villa)',
      image: 'assets/images/guesthouse.jpg',
      desc: 'Executive guest villa with serene garden surroundings, Queen bed, modern work desk, and full hospitality amenities.',
      capacity: 'Lecturer Villa • ₱1,500 / night',
      features: ['Executive Queen Bed', 'Full Air-Conditioning', 'Ensuite Restroom & Hot Water', 'Workstation & Wi-Fi Access']
    }
  };

  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-view-facility');
    if (!btn) return;
    e.preventDefault();
    const facilityKey = btn.dataset.facility;
    const data = facilityData[facilityKey];
    if (!data) return;

    const titleEl = document.getElementById('modalFacilityTitle');
    const imgEl = document.getElementById('modalFacilityImg');
    const descEl = document.getElementById('modalFacilityDesc');
    const capEl = document.getElementById('modalFacilityCap');
    const amenitiesEl = document.getElementById('modalFacilityAmenities');

    if (titleEl) titleEl.textContent = data.title;
    if (imgEl) imgEl.src = data.image;
    if (descEl) descEl.textContent = data.desc;
    if (capEl) capEl.textContent = data.capacity;

    if (amenitiesEl) {
      amenitiesEl.innerHTML = data.features.map(f => `
        <div class="amenity-item">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          <span>${f}</span>
        </div>
      `).join('');
    }

    const bookBtn = facilityModal ? facilityModal.querySelector('.btn-reserve-slot') : null;
    if (bookBtn) {
      const isGuesthouse = facilityKey.startsWith('guesthouse');
      const isDorm = facilityKey.startsWith('dorm');
      bookBtn.textContent = isGuesthouse ? 'Book this Guesthouse' : (isDorm ? 'Book this Dormitory' : 'Book this Facility');
    }

    openModal(facilityModal);
  });

  // Close modal when clicking outside of modal card
  [loginModal, facilityModal].forEach(modal => {
    if (!modal) return;
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeAllModals();
      }
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllModals();
  });

  function openModal(modal) {
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeAllModals() {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
    document.body.style.overflow = '';
  }
}

let userSelectedDrawerId = null;

/* ==========================================================================
   4. Mobile Navigation Drawer (Upper Left Hamburger & Off-Canvas)
   ========================================================================== */
function initMobileMenu() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const overlay = document.getElementById('mobileDrawerOverlay');
  const drawer = document.getElementById('mobileNavDrawer');
  const closeBtn = document.getElementById('mobileDrawerClose');
  const drawerLinks = document.querySelectorAll('.js-drawer-link');

  if (!toggleBtn || !overlay) return;

  function toggleDrawer(open) {
    if (open) {
      overlay.style.display = 'block';
      void overlay.offsetHeight; // force reflow for smooth slide-in
      overlay.classList.add('show');
      toggleBtn.setAttribute('aria-expanded', 'true');
    } else {
      overlay.classList.remove('show');
      toggleBtn.setAttribute('aria-expanded', 'false');
      userSelectedDrawerId = null;
      setTimeout(() => {
        if (!overlay.classList.contains('show')) {
          overlay.style.display = 'none';
        }
      }, 280);
    }
  }

  // Prevent background drag scrolling through dark overlay
  overlay.addEventListener('touchmove', (e) => {
    if (e.target === overlay) {
      e.preventDefault();
    }
  }, { passive: false });

  // Tapping dark backdrop does NOT close drawer (strictly only X button exits)
  overlay.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  if (drawer) {
    drawer.addEventListener('click', (e) => {
      e.stopPropagation();
    });
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleDrawer(true);
  });

  // ONLY exit the drawer when user presses the 'X' button
  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      userSelectedDrawerId = null;
      toggleDrawer(false);
    });
  }

  // Tapping a section: green tint STAYS, page scrolls behind drawer, drawer does NOT exit
  drawerLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();

      const targetId = this.getAttribute('href');
      userSelectedDrawerId = targetId;

      // 1. Permanently maintain active green tint on the selected drawer section
      drawerLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');

      // 2. Smoothly scroll page to target section in the background
      if (targetId && targetId.startsWith('#')) {
        const targetSection = document.querySelector(targetId);
        if (targetSection) {
          const navOffset = window.innerWidth <= 768 ? 68 : 75;
          const elementPosition = targetSection.getBoundingClientRect().top;
          const offsetPosition = elementPosition + window.pageYOffset - navOffset;

          window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
          });
        }

        // Also synchronize desktop navigation links
        document.querySelectorAll('.nav-link').forEach(nl => {
          if (nl.getAttribute('href') === targetId) {
            nl.classList.add('active');
          } else {
            nl.classList.remove('active');
          }
        });
      }

      // Drawer does NOT exit. It remains open until the user presses the 'X' button.
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && overlay.classList.contains('show')) {
      toggleDrawer(false);
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 991 && overlay.classList.contains('show')) {
      toggleDrawer(false);
    }
  });
}

/* ==========================================================================
   5. Active Navigation & ScrollSpy
   ========================================================================== */
function initNavigationScrollSpy() {
  const navLinks = document.querySelectorAll('.nav-link');
  const drawerNavLinks = document.querySelectorAll('.drawer-nav-link');
  const overlay = document.getElementById('mobileDrawerOverlay');
  const sections = document.querySelectorAll('section[id], footer[id]');

  function setActiveLink(activeId) {
    // 1. Always update desktop nav links
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href === `#${activeId}`) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });

    // 2. When drawer is open and user tapped a section, keep that green tint locked!
    if (overlay && overlay.classList.contains('show') && userSelectedDrawerId) {
      drawerNavLinks.forEach(link => {
        if (link.getAttribute('href') === userSelectedDrawerId) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
      return;
    }

    // 3. Otherwise update drawer links according to scroll position
    drawerNavLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href === `#${activeId}`) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  // 1. Click Listener: instantly shift underline when clicked
  navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId && targetId.startsWith('#')) {
        navLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');

        drawerNavLinks.forEach(dl => {
          if (dl.getAttribute('href') === targetId) {
            dl.classList.add('active');
          } else {
            dl.classList.remove('active');
          }
        });
      }
    });
  });

  // 2. ScrollSpy: synchronized with requestAnimationFrame & passive scroll for buttery 120fps scrolling
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (!ticking) {
      window.requestAnimationFrame(() => {
        const scrollPos = window.scrollY + 85;

        // Check if at the bottom of the page
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
          setActiveLink('about');
          ticking = false;
          return;
        }

        sections.forEach(section => {
          const top = section.offsetTop;
          const height = section.offsetHeight;
          const id = section.getAttribute('id');

          if (scrollPos >= top && scrollPos < top + height) {
            setActiveLink(id);
          }
        });
        ticking = false;
      });
      ticking = true;
    }
  }, { passive: true });
}

/* ==========================================================================
   6. Real-time Office Hours Status (Auto-updates OPEN / CLOSED)
   ========================================================================== */
function initRealtimeOfficeHours() {
  const statusEl = document.getElementById('officeStatusIndicator');
  if (!statusEl) return;

  function getPhilippineTime() {
    try {
      const now = new Date();
      const dtf = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Asia/Manila',
        weekday: 'short',
        hour: 'numeric',
        minute: 'numeric',
        hour12: false
      });
      const parts = dtf.formatToParts(now);
      const partMap = {};
      parts.forEach(p => partMap[p.type] = p.value);
      return {
        weekday: partMap.weekday,
        hour: parseInt(partMap.hour, 10),
        minute: parseInt(partMap.minute, 10)
      };
    } catch (e) {
      const now = new Date();
      const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
      const pht = new Date(utc + (3600000 * 8)); // UTC+8
      const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      return {
        weekday: days[pht.getDay()],
        hour: pht.getHours(),
        minute: pht.getMinutes()
      };
    }
  }

  function updateOfficeStatus() {
    const { weekday, hour, minute } = getPhilippineTime();
    const isWeekday = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'].includes(weekday);
    const totalMinutes = hour * 60 + minute;

    // Regular ATI office hours: Monday to Friday, 8:00 AM (480 mins) to 5:00 PM (1020 mins)
    const isOpen = isWeekday && (totalMinutes >= 480 && totalMinutes < 1020);

    if (isOpen) {
      statusEl.textContent = 'OPEN';
      statusEl.className = 'office-status-tag open';
      statusEl.title = 'ATI Office is currently OPEN (Regular hours: Mon-Fri 8:00 AM - 5:00 PM PST)';
    } else {
      statusEl.textContent = 'CLOSED';
      statusEl.className = 'office-status-tag closed';
      statusEl.title = 'ATI Office is currently CLOSED (Regular hours: Mon-Fri 8:00 AM - 5:00 PM PST)';
    }
  }

  // Initial check
  updateOfficeStatus();

  // Real-time interval check every 15 seconds
  setInterval(updateOfficeStatus, 15000);
}

/* ==========================================================================
   7. Vertical Animated Facilities & Guesthouses Scroller (Silky Smooth Loop)
   ========================================================================== */
function createVerticalShowcaseScroller({ wrapperId, viewportId, trackId, interval = 3500 }) {
  const wrapper = document.getElementById(wrapperId);
  const viewport = document.getElementById(viewportId);
  const track = document.getElementById(trackId);
  if (!wrapper || !viewport || !track) return;
  const originalRows = Array.from(track.querySelectorAll('.facility-row'));
  if (originalRows.length < 2) return;

  // Build seamless loop track with full preview buffer:
  // [LastRowCloneBefore, Row 0, ... Row N-1, FirstRowCloneAfter, SecondRowCloneAfter]
  // Having both Row 0 and Row 1 clones at the end ensures the bottom frosted blur always
  // displays the next preview row during loop reset with 100% pixel-perfect continuity.
  const lastRowCloneBefore = originalRows[originalRows.length - 1].cloneNode(true);
  lastRowCloneBefore.classList.remove('active');
  lastRowCloneBefore.setAttribute('aria-hidden', 'true');
  track.insertBefore(lastRowCloneBefore, originalRows[0]);

  const firstRowCloneAfter = originalRows[0].cloneNode(true);
  firstRowCloneAfter.classList.remove('active');
  firstRowCloneAfter.setAttribute('aria-hidden', 'true');
  track.appendChild(firstRowCloneAfter);

  const secondRowCloneAfter = originalRows[1].cloneNode(true);
  secondRowCloneAfter.classList.remove('active');
  secondRowCloneAfter.setAttribute('aria-hidden', 'true');
  track.appendChild(secondRowCloneAfter);

  const allRows = Array.from(track.querySelectorAll('.facility-row'));
  const allCards = Array.from(track.querySelectorAll('.facility-card-item'));

  const prependedCardCount = lastRowCloneBefore.querySelectorAll('.facility-card-item').length;
  const totalOriginalCards = originalRows.reduce((sum, r) => sum + r.querySelectorAll('.facility-card-item').length, 0);

  const firstOriginalCardIndex = prependedCardCount;
  const lastOriginalCardIndex = prependedCardCount + totalOriginalCards - 1;
  const cloneAfterFirstCardIndex = prependedCardCount + totalOriginalCards;

  const firstOriginalRowIndex = 1;
  const lastOriginalRowIndex = originalRows.length;
  const cloneAfterRowIndex = originalRows.length + 1;

  const isMobile = () => window.innerWidth <= 768;
  let wasMobile = isMobile();

  let currentIndex = isMobile() ? firstOriginalCardIndex : firstOriginalRowIndex;
  let isAnimating = false;
  let autoTimer = null;
  let isHovered = false;

  // Pre-calculate and cache all Y offsets to completely eliminate layout thrashing during animation
  let cachedOffsets = [];
  function updateOffsets() {
    cachedOffsets = [];
    if (isMobile()) {
      allCards.forEach(card => {
        let top = 0;
        let curr = card;
        while (curr && curr !== track) {
          top += curr.offsetTop;
          curr = curr.offsetParent;
        }
        // Align card high on mobile to reveal preview card & deep frosted blur
        cachedOffsets.push(Math.round(0 - top));
      });
    } else {
      allRows.forEach(row => {
        let top = 0;
        let curr = row;
        while (curr && curr !== track) {
          top += curr.offsetTop;
          curr = curr.offsetParent;
        }
        // Align active row high on desktop to reveal full preview card image & badge
        cachedOffsets.push(Math.round(0 - top));
      });
    }
  }

  function getTargetY(index) {
    return cachedOffsets[index] !== undefined ? cachedOffsets[index] : 0;
  }

  function goTo(index, animate = true) {
    currentIndex = index;
    const targetY = getTargetY(currentIndex);

    if (animate) {
      isAnimating = true;
      track.style.transition = 'transform 0.88s cubic-bezier(0.16, 1, 0.3, 1)';
    } else {
      track.style.transition = 'none';
    }

    // Force hardware-accelerated 3D composition with subpixel smoothing
    track.style.transform = `translate3d(0, ${targetY}px, 0)`;

    if (!animate) {
      // Synchronously flush styles to ensure instant, glitch-free loop reset
      void track.offsetHeight;
    }
  }

  // Seamless instant jump when reaching clones at the boundaries
  track.addEventListener('transitionend', (e) => {
    if (e.target !== track || e.propertyName !== 'transform') return;
    isAnimating = false;

    if (isMobile()) {
      if (currentIndex === cloneAfterFirstCardIndex) {
        goTo(firstOriginalCardIndex, false);
      } else if (currentIndex === firstOriginalCardIndex - 1) {
        goTo(lastOriginalCardIndex, false);
      }
    } else {
      if (currentIndex === cloneAfterRowIndex) {
        goTo(firstOriginalRowIndex, false);
      } else if (currentIndex === 0) {
        goTo(lastOriginalRowIndex, false);
      }
    }
  });

  function next() {
    if (isAnimating) return;
    goTo(currentIndex + 1, true);
  }

  let isVisible = false;
  function startAuto() {
    stopAuto();
    if (!isVisible) return;
    autoTimer = setInterval(() => {
      if (!isHovered && !isAnimating && isVisible) {
        next();
      }
    }, interval);
  }

  function stopAuto() {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  }

  // IntersectionObserver: automatically pauses carousel when offscreen to eliminate all lag
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        isVisible = entry.isIntersecting;
        if (isVisible) {
          startAuto();
        } else {
          stopAuto();
        }
      });
    }, { threshold: 0.08 });
    observer.observe(wrapper);
  } else {
    isVisible = true;
  }

  // Smooth pause when mouse hovers, and fluid touch swipe on mobile web
  wrapper.addEventListener('mouseenter', () => { isHovered = true; }, { passive: true });
  wrapper.addEventListener('mouseleave', () => { isHovered = false; }, { passive: true });

  let touchStartY = 0;
  let touchDiffY = 0;

  wrapper.addEventListener('touchstart', (e) => {
    isHovered = true;
    if (e.touches && e.touches.length === 1) {
      touchStartY = e.touches[0].clientY;
      touchDiffY = 0;
    }
  }, { passive: true });

  wrapper.addEventListener('touchmove', (e) => {
    if (e.touches && e.touches.length === 1) {
      touchDiffY = e.touches[0].clientY - touchStartY;
    }
  }, { passive: true });

  wrapper.addEventListener('touchend', () => {
    isHovered = false;
    if (Math.abs(touchDiffY) > 42 && !isAnimating) {
      if (touchDiffY < 0) {
        next();
      } else {
        goTo(currentIndex - 1, true);
      }
    }
    touchDiffY = 0;
  }, { passive: true });

  // Update layout offsets after fonts/images load
  function refreshLayout() {
    updateOffsets();
    goTo(currentIndex, false);
  }

  const trackImgs = track.querySelectorAll('img');
  trackImgs.forEach((img) => {
    if (!img.complete) {
      img.addEventListener('load', refreshLayout, { once: true });
    }
  });

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(refreshLayout);
  }

  requestAnimationFrame(refreshLayout);
  setTimeout(refreshLayout, 150);
  setTimeout(refreshLayout, 450);

  window.addEventListener('load', refreshLayout);
  window.addEventListener('resize', () => {
    const nowMobile = isMobile();
    if (nowMobile !== wasMobile) {
      wasMobile = nowMobile;
      currentIndex = nowMobile ? firstOriginalCardIndex : firstOriginalRowIndex;
    }
    refreshLayout();
  });

  startAuto();
}

function initVerticalFacilitiesScroller() {
  createVerticalShowcaseScroller({
    wrapperId: 'facilitiesShowcaseWrapper',
    viewportId: 'facilitiesViewport',
    trackId: 'facilitiesTrack',
    interval: 3800
  });

  createVerticalShowcaseScroller({
    wrapperId: 'dormitoriesShowcaseWrapper',
    viewportId: 'dormitoriesViewport',
    trackId: 'dormitoriesTrack',
    interval: 4200
  });

  createVerticalShowcaseScroller({
    wrapperId: 'guesthousesShowcaseWrapper',
    viewportId: 'guesthousesViewport',
    trackId: 'guesthousesTrack',
    interval: 4600
  });
}

/* ==========================================================================
   10. Smart Gmail Link (Direct to Compose for Web on PC & App on Mobile)
   ========================================================================== */
function initGmailSmartLink() {
  const emailLink = document.getElementById('footerEmailLink');
  if (!emailLink) return;

  emailLink.addEventListener('click', (e) => {
    e.preventDefault();
    const email = 'dormitory.co@ati.da.gov.ph';
    const webComposeUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}`;
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

    if (isMobile) {
      const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
      if (isIOS) {
        // Deep-link directly into iOS Gmail App compose view
        const appUrl = `googlegmail:///co?to=${encodeURIComponent(email)}`;
        window.location.href = appUrl;
        setTimeout(() => {
          window.location.href = webComposeUrl;
        }, 1200);
      } else {
        // Deep-link directly into Android Gmail App compose view (bypasses Outlook)
        const androidIntent = `intent://co?to=${encodeURIComponent(email)}#Intent;scheme=googlegmail;package=com.google.android.gm;end`;
        window.location.href = androidIntent;
        setTimeout(() => {
          window.location.href = webComposeUrl;
        }, 1200);
      }
    } else {
      // Desktop / PC: Open Gmail Web Compose in a new tab (never Outlook)
      window.open(webComposeUrl, '_blank', 'noopener,noreferrer');
    }
  });
}

/* ==========================================================================
   11. Official Phone Hotline (Direct One-Tap Phone Dialing on Mobile)
   ========================================================================== */
function initPhoneHotlineDirectDial() {
  const hotlineLinks = document.querySelectorAll('.footer-contact-phone-link, a[href^="tel:"]');
  hotlineLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      // In mobile web version, direct this number straight into the user's phone dialer
      const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) || window.innerWidth <= 991;
      if (isMobile) {
        window.location.href = 'tel:09992200064';
      }
    });
  });
}


