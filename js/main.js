/**
 * ATI Facility Reservation System - Main Interactive Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  initHeroSlider();
  initCalendar();
  initModals();
  initMobileMenu();
  initNavigationScrollSpy();
});

/* ==========================================================================
   1. Hero Showcase Slider
   ========================================================================== */
function initHeroSlider() {
  const slides = document.querySelectorAll('.showcase-slide');
  const dots = document.querySelectorAll('.showcase-dot');
  const pillBadge = document.getElementById('heroPillBadge');

  if (!slides.length) return;

  const hallNames = [
    'Function Hall',
    'Training Hall',
    'Mess Hall'
  ];

  let currentIndex = 0;
  let intervalTimer = null;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });

    dots.forEach((dot, i) => {
      dot.classList.toggle('active', i === index);
    });

    if (pillBadge && hallNames[index]) {
      pillBadge.textContent = hallNames[index];
    }

    currentIndex = index;
  }

  function startAutoCycle() {
    stopAutoCycle();
    intervalTimer = setInterval(() => {
      let nextIndex = (currentIndex + 1) % slides.length;
      showSlide(nextIndex);
    }, 4500);
  }

  function stopAutoCycle() {
    if (intervalTimer) clearInterval(intervalTimer);
  }

  dots.forEach(dot => {
    dot.addEventListener('click', (e) => {
      const targetIndex = parseInt(e.target.dataset.index, 10);
      showSlide(targetIndex);
      startAutoCycle();
    });
  });

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
   2. Interactive Calendar & Availability Details
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

  // Base state defaults to October 2026 (matching design reference)
  let currentDate = new Date(2026, 9, 1); // 9 = October (0-indexed)
  let selectedDate = 15; // default selected on Oct 15

  // Mock reservation schedules for display
  const mockSchedules = {
    '2026-09-02': { status: 'reserved', functionHall: 'Reserved (8:00 AM - 5:00 PM)', trainingHall: 'Reserved', messHall: 'Available' },
    '2026-09-05': { status: 'available', functionHall: 'Available All Day', trainingHall: 'Available All Day', messHall: 'Available All Day' },
    '2026-09-08': { status: 'pending', functionHall: 'Pending Approval (DA Staff)', trainingHall: 'Available', messHall: 'Available' },
    '2026-09-12': { status: 'reserved', functionHall: 'Reserved (Training Group A)', trainingHall: 'Reserved', messHall: 'Reserved' },
    '2026-09-15': { status: 'available', functionHall: 'Available All Day', trainingHall: 'Available All Day', messHall: 'Available All Day' },
    '2026-09-21': { status: 'pending', functionHall: 'Available', trainingHall: 'Pending Review', messHall: 'Available' },
    '2026-09-24': { status: 'reserved', functionHall: 'Annual Regional Assembly', trainingHall: 'Reserved', messHall: 'Reserved' },
    '2026-09-29': { status: 'suspended', functionHall: 'Maintenance / Closed', trainingHall: 'Maintenance', messHall: 'Maintenance' },
  };

  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ];

  const weekdayNames = [
    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
  ];

  function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

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

      const dateKey = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const schedule = mockSchedules[dateKey];

      if (schedule) {
        dayBtn.classList.add('has-indicator', `status-${schedule.status}`);
      }

      if (day === selectedDate && month === 9 && year === 2026) {
        dayBtn.classList.add('selected');
        updateDetailsPanel(day, month, year, schedule);
      }

      dayBtn.addEventListener('click', () => {
        document.querySelectorAll('.cal-day').forEach(el => el.classList.remove('selected'));
        dayBtn.classList.add('selected');
        selectedDate = day;
        updateDetailsPanel(day, month, year, schedule);
      });

      daysGrid.appendChild(dayBtn);
    }
  }

  function updateDetailsPanel(day, month, year, schedule) {
    if (!emptyState || !activeState) return;

    emptyState.style.display = 'none';
    activeState.style.display = 'block';

    const dateObj = new Date(year, month, day);
    const dayName = weekdayNames[dateObj.getDay()];
    const formattedDate = `${monthNames[month]} ${day}, ${year}`;

    if (activeDateTitle) activeDateTitle.textContent = formattedDate;
    if (activeDateDay) activeDateDay.textContent = `Schedule overview for ${dayName}`;

    // Generate schedule overview items
    const fStatus = schedule?.functionHall || 'Available All Day';
    const tStatus = schedule?.trainingHall || 'Available All Day';
    const mStatus = schedule?.messHall || 'Available All Day';

    const getPillClass = (statusStr) => {
      if (statusStr.includes('Reserved')) return 'reserved';
      if (statusStr.includes('Pending')) return 'pending';
      if (statusStr.includes('Maintenance')) return 'suspended';
      return 'available';
    };

    if (slotList) {
      slotList.innerHTML = `
        <div class="hall-slot-item">
          <div>
            <div class="slot-name">Function Hall</div>
            <small style="color: #64748b;">${fStatus}</small>
          </div>
          <span class="slot-status-pill ${getPillClass(fStatus)}">
            ${getPillClass(fStatus).toUpperCase()}
          </span>
        </div>
        <div class="hall-slot-item">
          <div>
            <div class="slot-name">Training Hall</div>
            <small style="color: #64748b;">${tStatus}</small>
          </div>
          <span class="slot-status-pill ${getPillClass(tStatus)}">
            ${getPillClass(tStatus).toUpperCase()}
          </span>
        </div>
        <div class="hall-slot-item">
          <div>
            <div class="slot-name">Mess Hall</div>
            <small style="color: #64748b;">${mStatus}</small>
          </div>
          <span class="slot-status-pill ${getPillClass(mStatus)}">
            ${getPillClass(mStatus).toUpperCase()}
          </span>
        </div>
      `;
    }
  }

  if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() - 1);
      selectedDate = null;
      renderCalendar();
    });
  }

  if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', () => {
      currentDate.setMonth(currentDate.getMonth() + 1);
      selectedDate = null;
      renderCalendar();
    });
  }

  renderCalendar();
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
    'function-hall': {
      title: 'Function Hall',
      image: 'assets/images/function_hall.jpg',
      desc: 'Our premier venue for broad audience events, symposiums, official regional congresses, and stakeholder meetings. Equipped with high-definition presentation screens and dedicated acoustics.',
      capacity: 'Up to 250 Persons',
      features: ['Dual HD Laser Projectors', 'Surround Sound & Lapel Microphones', 'Full Air-conditioning', 'VIP Stage & Podium']
    },
    'training-hall': {
      title: 'Training Hall',
      image: 'assets/images/training_hall.jpg',
      desc: 'Purpose-built for comprehensive hands-on training sessions, agricultural extension modules, interactive workshops, and group breakout discussions.',
      capacity: 'Up to 80 Persons',
      features: ['Modular Work Tables & Chairs', 'Dual Presentation Monitors', 'Dedicated High-Speed WiFi', 'Whiteboards & Training Kits']
    },
    'mess-hall': {
      title: 'Mess Hall',
      image: 'assets/images/mess_hall.jpg',
      desc: 'A welcoming, naturally lit dining facility designed for delegate meals, networking breaks, and catered agricultural community banquets.',
      capacity: 'Up to 150 Persons',
      features: ['Buffet & Food Service Counters', 'Sanitation & Handwash Stations', 'Direct Access to Kitchen Area', 'Comfortable Solid Wood Seating']
    }
  };

  facilityDetailButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
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

      openModal(facilityModal);
    });
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

/* ==========================================================================
   4. Mobile Navigation Drawer (Upper Left Hamburger & Off-Canvas)
   ========================================================================== */
function initMobileMenu() {
  const toggleBtn = document.getElementById('mobileMenuToggle');
  const overlay = document.getElementById('mobileDrawerOverlay');
  const closeBtn = document.getElementById('mobileDrawerClose');
  const drawerLinks = document.querySelectorAll('.js-drawer-link');

  if (!toggleBtn || !overlay) return;

  function toggleDrawer(open) {
    if (open) {
      overlay.style.display = 'block';
      void overlay.offsetHeight; // force reflow for smooth slide-in
      overlay.classList.add('show');
      toggleBtn.setAttribute('aria-expanded', 'true');
      document.body.style.overflow = 'hidden';
    } else {
      overlay.classList.remove('show');
      toggleBtn.setAttribute('aria-expanded', 'false');
      document.body.style.overflow = '';
      setTimeout(() => {
        if (!overlay.classList.contains('show')) {
          overlay.style.display = 'none';
        }
      }, 280);
    }
  }

  toggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleDrawer(true);
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      toggleDrawer(false);
    });
  }

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) {
      toggleDrawer(false);
    }
  });

  drawerLinks.forEach(link => {
    link.addEventListener('click', () => {
      toggleDrawer(false);
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
  const sections = document.querySelectorAll('section[id], footer[id]');

  function setActiveLink(activeId) {
    navLinks.forEach(link => {
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
      }
    });
  });

  // 2. ScrollSpy: automatically track section as user scrolls
  let isThrottled = false;
  window.addEventListener('scroll', () => {
    if (isThrottled) return;
    isThrottled = true;

    setTimeout(() => {
      isThrottled = false;
      const scrollPos = window.scrollY + 85; // offset for sticky navbar

      // Check if at the bottom of the page
      if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
        setActiveLink('about');
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
    }, 80);
  });
}

