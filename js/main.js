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
    'Carandang Hall',
    'Orosa Hall',
    'RDEC Training Hall 1',
    'RDEC Training Hall 2',
    'Function Hall',
    'Serrano Hall'
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
   7. Vertical Animated Facilities Scroller (Infinite One-Way Loop with Blur)
   ========================================================================== */
function initVerticalFacilitiesScroller() {
  const wrapper = document.getElementById('facilitiesShowcaseWrapper');
  const viewport = document.getElementById('facilitiesViewport');
  const track = document.getElementById('facilitiesTrack');
  const originalRows = Array.from(track.querySelectorAll('.facility-row'));

  if (!wrapper || !viewport || !track || originalRows.length < 2) return;

  // Build infinite loop track: [Row1CloneBefore, Row0, Row1, Row0CloneAfter]
  const row1CloneBefore = originalRows[1].cloneNode(true);
  row1CloneBefore.classList.remove('active');
  row1CloneBefore.setAttribute('aria-hidden', 'true');
  track.insertBefore(row1CloneBefore, originalRows[0]);

  const row0CloneAfter = originalRows[0].cloneNode(true);
  row0CloneAfter.classList.remove('active');
  row0CloneAfter.setAttribute('aria-hidden', 'true');
  track.appendChild(row0CloneAfter);

  const allRows = Array.from(track.querySelectorAll('.facility-row'));
  const allCards = Array.from(track.querySelectorAll('.facility-card-item'));

  const isMobile = () => window.innerWidth <= 768;
  let wasMobile = isMobile();

  // Desktop indexes: 1 = Row 0, 2 = Row 1 (Loop: 1 -> 2 -> 3 (jump to 1))
  // Mobile indexes: 3 = Carandang, 4 = Orosa, 5 = RDEC 1, 6 = RDEC 2, 7 = Function, 8 = Serrano (Loop: 3..8 -> 9 (jump to 3))
  let currentIndex = isMobile() ? 3 : 1;
  let isAnimating = false;
  let autoTimer = null;
  let isHovered = false;

  function getElementTrackOffset(element) {
    let top = 0;
    let curr = element;
    while (curr && curr !== track) {
      top += curr.offsetTop;
      curr = curr.offsetParent;
    }
    return top;
  }

  function getTargetY(index) {
    if (isMobile()) {
      const card = allCards[index];
      if (!card) return 0;
      const topPadding = 12;
      return Math.round(topPadding - getElementTrackOffset(card));
    } else {
      const row = allRows[index];
      if (!row) return 0;
      const topPadding = 16;
      return Math.round(topPadding - getElementTrackOffset(row));
    }
  }

  function setClasses(activeIndex) {
    if (isMobile()) {
      allCards.forEach((card, idx) => {
        const isActive = idx === activeIndex;
        card.classList.toggle('active', isActive);
        card.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });
      allRows.forEach((row) => {
        row.classList.add('active');
        row.removeAttribute('aria-hidden');
      });
    } else {
      allRows.forEach((row, idx) => {
        const isActive = idx === activeIndex;
        row.classList.toggle('active', isActive);
        row.setAttribute('aria-hidden', isActive ? 'false' : 'true');
      });
    }
  }

  function goTo(index, animate = true) {
    currentIndex = index;
    setClasses(currentIndex);

    if (animate) {
      isAnimating = true;
      track.style.transition = 'transform 0.8s cubic-bezier(0.25, 1, 0.35, 1)';
    } else {
      track.style.transition = 'none';
    }

    track.style.transform = `translateY(${getTargetY(currentIndex)}px)`;

    if (!animate) {
      void track.offsetHeight; // Force reflow
    }
  }

  // Seamless jump when reaching clones at the boundaries
  track.addEventListener('transitionend', (e) => {
    if (e.target !== track) return;
    isAnimating = false;

    if (isMobile()) {
      // Reached clone after (index 9 -> identical to original Carandang at index 3)
      if (currentIndex === 9) {
        goTo(3, false);
      }
      // Reached clone before (index 2 -> identical to original Serrano at index 8)
      else if (currentIndex === 2) {
        goTo(8, false);
      }
    } else {
      // Reached clone after (index 3 -> identical to original Row 0 at index 1)
      if (currentIndex === 3) {
        goTo(1, false);
      }
      // Reached clone before (index 0 -> identical to original Row 1 at index 2)
      else if (currentIndex === 0) {
        goTo(2, false);
      }
    }
  });

  // Always scrolls forward in a continuous, one-directional loop
  function next() {
    if (isAnimating) return;
    goTo(currentIndex + 1, true);
  }

  function prev() {
    if (isAnimating) return;
    goTo(currentIndex - 1, true);
  }

  // Auto-play scrolling (forward one-way loop every 2.2 seconds)
  function startAuto() {
    stopAuto();
    autoTimer = setInterval(() => {
      if (!isHovered && !isAnimating) {
        next();
      }
    }, 2200);
  }

  function stopAuto() {
    if (autoTimer) {
      clearInterval(autoTimer);
      autoTimer = null;
    }
  }

  wrapper.addEventListener('mouseenter', () => { isHovered = true; });
  wrapper.addEventListener('mouseleave', () => { isHovered = false; });
  wrapper.addEventListener('touchstart', () => { isHovered = true; }, { passive: true });
  wrapper.addEventListener('touchend', () => { isHovered = false; }, { passive: true });

  // Layout calculations & dynamic image loading handlers
  const trackImgs = track.querySelectorAll('img');
  trackImgs.forEach((img) => {
    if (!img.complete) {
      img.addEventListener('load', () => {
        goTo(currentIndex, false);
      }, { once: true });
    }
  });

  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(() => {
      goTo(currentIndex, false);
    });
  }

  requestAnimationFrame(() => {
    goTo(currentIndex, false);
  });
  setTimeout(() => { goTo(currentIndex, false); }, 150);
  setTimeout(() => { goTo(currentIndex, false); }, 500);

  window.addEventListener('load', () => {
    goTo(currentIndex, false);
  });
  window.addEventListener('resize', () => {
    const nowMobile = isMobile();
    if (nowMobile !== wasMobile) {
      wasMobile = nowMobile;
      currentIndex = nowMobile ? 3 : 1;
    }
    goTo(currentIndex, false);
  });

  startAuto();
}


