<?php
/**
 * ATI Reservation System - Inclement Weather Suspension Directive & Disaster Risk Reduction Control Center
 * Built for Super Administrators, Recommending Officers & General Services Directorate
 * Republic of the Philippines • Executive Order No. 66 (s. 2012) & Civil Service Commission Directives
 */
?>
<!-- Inclement Weather Suspension Directive Modal -->
<style>
.ws-grid-2col {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 0.85rem;
  margin-bottom: 0.85rem;
}
.ws-date-grid-2col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
}
@media (max-width: 640px) {
  .ws-grid-2col,
  .ws-date-grid-2col {
    grid-template-columns: 1fr !important;
    gap: 0.65rem !important;
  }
  .ws-tabs-container {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    width: 100% !important;
    scrollbar-width: none !important;
    padding-bottom: 0.25rem !important;
  }
  .ws-tabs-container::-webkit-scrollbar {
    display: none !important;
  }
  .ws-modal-header-inner {
    flex-wrap: wrap !important;
    gap: 0.65rem !important;
  }
}
</style>
<div class="admin-modal-overlay" id="adminWeatherSuspensionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.78); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; overflow-y: auto;">
  <div class="admin-modal-card" style="background: #ffffff; width: 100%; max-width: 740px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45); overflow: hidden; border: 1px solid #cbd5e1; animation: modalPop 0.22s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <!-- Modal Header -->
    <div class="ws-modal-header-inner" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.15rem 1.4rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #ea580c; position: relative;">
      <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
        <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(234, 88, 12, 0.2); border: 1.5px solid rgba(249, 115, 22, 0.6); display: flex; align-items: center; justify-content: center; color: #fb923c; flex-shrink: 0; box-shadow: 0 0 14px rgba(234, 88, 12, 0.25);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
            <polyline points="13 11 9 17 15 17 11 23"></polyline>
          </svg>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <h3 style="margin: 0; font-size: 1.08rem; font-weight: 800; color: #ffffff; letter-spacing: -0.01em;">Inclement Weather Suspension Directive</h3>
            <span style="font-size: 0.62rem; font-weight: 800; letter-spacing: 0.05em; background: rgba(234, 88, 12, 0.25); color: #fed7aa; border: 1px solid rgba(249, 115, 22, 0.45); padding: 0.12rem 0.45rem; border-radius: 999px; text-transform: uppercase;">SAFETY PROTOCOL</span>
          </div>
          <p style="margin: 0.2rem 0 0; font-size: 0.74rem; color: #94a3b8;">Republic of the Philippines • Civil Service & EO 66 Disaster Risk Reduction</p>
        </div>
      </div>
      <button type="button" onclick="closeWeatherSuspensionModal()" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.12); color: #cbd5e1; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; margin-left: auto;" aria-label="Close modal">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Navigation / Mode Pills -->
    <div style="background: #f1f5f9; border-bottom: 1px solid #e2e8f0; padding: 0.5rem 1.4rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
      <div class="ws-tabs-container" style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
        <button type="button" class="tab-ws-btn active" id="tabBtnForm" onclick="switchWeatherTab('form')" style="font-size: 0.75rem; font-weight: 700; padding: 0.32rem 0.75rem; border-radius: 6px; border: none; background: #ffffff; color: #0f172a; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
          ⚡ Suspension Broadcast
        </button>
        <button type="button" class="tab-ws-btn" id="tabBtnChecklist" onclick="switchWeatherTab('checklist')" style="font-size: 0.75rem; font-weight: 700; padding: 0.32rem 0.75rem; border-radius: 6px; border: none; background: transparent; color: #64748b; cursor: pointer;">
          📋 Facility Safety Checklist
        </button>
        <button type="button" class="tab-ws-btn" id="tabBtnHotlines" onclick="switchWeatherTab('hotlines')" style="font-size: 0.75rem; font-weight: 700; padding: 0.32rem 0.75rem; border-radius: 6px; border: none; background: transparent; color: #64748b; cursor: pointer;">
          ☎️ Emergency Hotlines
        </button>
      </div>
      <div style="font-size: 0.7rem; color: #475569; display: flex; align-items: center; gap: 0.35rem;">
        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #22c55e;"></span>
        <span>Target: <strong>ATI Central Office, Diliman, QC</strong></span>
      </div>
    </div>

    <!-- Modal Body -->
    <div style="padding: 1.25rem 1.4rem; max-height: calc(85vh - 150px); overflow-y: auto;">
      
      <!-- Current Status Indicator Box -->
      <div id="weatherStatusBox" style="background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 0.85rem 1.15rem; margin-bottom: 1.15rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; transition: all 0.25s ease;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <span id="weatherStatusIndicatorDot" style="width: 12px; height: 12px; border-radius: 50%; background: #22c55e; display: inline-block; box-shadow: 0 0 10px rgba(34, 197, 94, 0.7); flex-shrink: 0;"></span>
          <div>
            <strong id="weatherStatusTitle" style="font-size: 0.88rem; color: #1e293b; display: block; font-weight: 800;">Standard Office Operations Active</strong>
            <span id="weatherStatusSubtitle" style="font-size: 0.75rem; color: #64748b; line-height: 1.4; display: block;">No active weather suspension declared. Regular physical counter hours and Master Schedule bookings apply.</span>
          </div>
        </div>
        <span id="weatherStatusBadge" style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; padding: 0.25rem 0.75rem; border-radius: 999px; background: rgba(34, 197, 94, 0.12); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3); white-space: nowrap;">NORMAL</span>
      </div>

      <!-- TAB 1: SUSPENSION CONFIGURATION FORM -->
      <div id="tabContentForm">
        
        <!-- Quick Preset Buttons (1-Click Emergency Scenarios) -->
        <div style="margin-bottom: 1.15rem;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.45rem;">
            <label style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #334155;">
              Emergency Scenarios (1-Click Fill)
            </label>
            <span style="font-size: 0.68rem; color: #64748b;">Aligned with DOST-PAGASA TCWS, NDRRMC & EO 66 Directives</span>
          </div>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.45rem;">
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('signal2')" style="background: #fff7ed; border: 1px solid #fdba74; color: #c2410c; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🌪️ TCWS No. 2 (Auto EO 66)
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('signal3')" style="background: #fff1f2; border: 1px solid #fda4af; color: #be123c; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🌀 Signal No. 3 / Severe Typhoon
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('signal4')" style="background: #fdf2f8; border: 1px solid #f472b6; color: #9d174d; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🚨 Signal No. 4/5 Super Typhoon
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('heavyrain')" style="background: #eff6ff; border: 1px solid #93c5fd; color: #1d4ed8; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🌧️ Heavy Rainfall Red Warning
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('monsoon')" style="background: #ecfeff; border: 1px solid #a5f3fc; color: #0e7490; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🌊 Habagat / Flash Flood
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('malacanang')" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🏛️ Malacañang Directive (OP)
            </button>
            <button type="button" class="btn-weather-preset" onclick="applyWeatherPreset('heat')" style="background: #fefce8; border: 1px solid #fde047; color: #854d0e; padding: 0.45rem 0.6rem; border-radius: 8px; font-size: 0.73rem; font-weight: 700; cursor: pointer; text-align: left; transition: all 0.15s ease;">
              🔥 Heat Danger (>42°C CSC)
            </button>
          </div>
        </div>

        <!-- Live Master Schedule & Attendee Safety Impact Banner -->
        <div id="weatherImpactBanner" style="background: #fffbeb; border: 1.5px solid #fef08a; border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.15rem; display: flex; align-items: flex-start; gap: 0.7rem;">
          <div style="color: #d97706; margin-top: 2px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3">
              <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
              <line x1="12" y1="9" x2="12" y2="13"></line>
              <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
          </div>
          <div style="flex: 1;">
            <strong style="font-size: 0.8rem; color: #92400e; display: block;" id="impactBannerTitle">
              Master Schedule Safety Impact Analysis
            </strong>
            <p style="margin: 0.15rem 0 0; font-size: 0.74rem; color: #b45309;" id="impactBannerDesc">
              Checking scheduled events, facility bookings, and attendee counts for the selected dates...
            </p>
          </div>
          <button type="button" onclick="toggleImpactDetails()" id="btnToggleImpact" style="background: rgba(217, 119, 6, 0.15); border: 1px solid rgba(217, 119, 6, 0.35); color: #92400e; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.68rem; font-weight: 700; cursor: pointer; white-space: nowrap;">
            View Bookings
          </button>
        </div>

        <!-- Hidden Expandable Bookings List -->
        <div id="impactDetailsBox" style="display: none; background: #ffffff; border: 1px solid #fde68a; border-radius: 8px; padding: 0.75rem; margin-top: -0.65rem; margin-bottom: 1.15rem; max-height: 180px; overflow-y: auto;">
          <div id="impactBookingsList" style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.73rem;">
            <!-- Injected via JavaScript -->
          </div>
        </div>

        <!-- Suspension Configuration Form -->
        <form id="weatherSuspensionForm" onsubmit="handleSaveWeatherSuspension(event)">
          
          <div class="ws-grid-2col">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                Tropical Cyclone / Disturbance Name
              </label>
              <input type="text" id="wsTyphoonName" placeholder="e.g. Typhoon Pepito / Habagat" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                Warning Level / Category
              </label>
              <select id="wsSignalLevel" onchange="onSignalLevelChanged()" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit; background: #ffffff;">
                <option value="TCWS Signal No. 2">TCWS Signal No. 2 (62–88 km/h • Auto EO 66)</option>
                <option value="TCWS Signal No. 3">TCWS Signal No. 3 (89–117 km/h • Severe Typhoon)</option>
                <option value="TCWS Signal No. 4">TCWS Signal No. 4 (118–184 km/h • Typhoon Force)</option>
                <option value="TCWS Signal No. 5">TCWS Signal No. 5 (>185 km/h • Super Typhoon)</option>
                <option value="TCWS Signal No. 1">TCWS Signal No. 1 (39–61 km/h • Monitor Advisory)</option>
                <option value="Heavy Rainfall Red Warning">Heavy Rainfall Red Warning (>30 mm/h Torrential)</option>
                <option value="Heavy Rainfall Orange Warning">Heavy Rainfall Orange Warning (15–30 mm/h)</option>
                <option value="Southwest Monsoon Flooding">Severe Habagat / Monsoon Inundation</option>
                <option value="Executive Work Suspension">Presidential Work Suspension Order (OP)</option>
                <option value="Extreme Heat Index">Extreme Heat Danger (>42°C CSC MC 17)</option>
              </select>
            </div>
          </div>

          <!-- Date Range with Quick Chips -->
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.85rem; margin-bottom: 0.85rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.45rem; flex-wrap: wrap; gap: 0.4rem;">
              <span style="font-size: 0.76rem; font-weight: 700; color: #334155;">Effective Dates</span>
              <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                <button type="button" class="btn-date-chip" onclick="setQuickDates('today')" style="background: #e2e8f0; border: none; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.68rem; font-weight: 700; cursor: pointer;">Today</button>
                <button type="button" class="btn-date-chip" onclick="setQuickDates('today_tomorrow')" style="background: #e2e8f0; border: none; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.68rem; font-weight: 700; cursor: pointer;">2 Days (48h)</button>
                <button type="button" class="btn-date-chip" onclick="setQuickDates('3days')" style="background: #e2e8f0; border: none; padding: 0.15rem 0.45rem; border-radius: 4px; font-size: 0.68rem; font-weight: 700; cursor: pointer;">3 Days (72h)</button>
              </div>
            </div>
            <div class="ws-date-grid-2col">
              <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">
                  Effective Start Date
                </label>
                <input type="date" id="wsStartDate" value="<?= date('Y-m-d') ?>" required onchange="onDatesChanged()" style="width: 100%; padding: 0.55rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
              </div>
              <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem;">
                  Effective End Date (Inclusive)
                </label>
                <input type="date" id="wsEndDate" value="<?= date('Y-m-d') ?>" required onchange="onDatesChanged()" style="width: 100%; padding: 0.55rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
              </div>
            </div>
          </div>

          <div class="ws-grid-2col">
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                Official Directive / Memo Reference
              </label>
              <input type="text" id="wsMemoRef" placeholder="e.g. Executive Order No. 66 / NDRRMC Memo #14" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
                Advisory Badge Tag
              </label>
              <input type="text" id="wsBadgeText" value="WEATHER SUSPENSION" required style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
            </div>
          </div>

          <div style="margin-bottom: 0.85rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Public Advisory Announcement Description
            </label>
            <textarea id="wsDescription" rows="3" style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem; font-family: inherit; line-height: 1.45;" placeholder="Enter details to be displayed on public landing page cards and top announcement banner..."></textarea>
          </div>

          <div style="margin-bottom: 0.85rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">
              Resumption Timeline Note
            </label>
            <input type="text" id="wsResumptionNote" placeholder="e.g. Regular on-site counter operations resume next business day at 8:00 AM (PST)." style="width: 100%; padding: 0.6rem 0.75rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.86rem; font-family: inherit;">
          </div>

          <!-- Master Schedule Safety Lock Toggle -->
          <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 0.75rem 0.9rem; margin-bottom: 1.15rem; display: flex; align-items: flex-start; gap: 0.65rem;">
            <input type="checkbox" id="wsAutoLockMaster" checked style="width: 17px; height: 17px; margin-top: 2px; accent-color: #ea580c; cursor: pointer;">
            <div>
              <label for="wsAutoLockMaster" style="font-size: 0.8rem; font-weight: 800; color: #1e3a8a; cursor: pointer; display: block;">
                Auto-Lock Master Schedule & Reserve Calender
              </label>
              <span style="font-size: 0.73rem; color: #1d4ed8; line-height: 1.35; display: block;">
                Automatically applies an Institution-Wide Safety Hold on the Master Schedule for these dates. Blocks public booking slots and notifies on-site desks.
              </span>
            </div>
          </div>

          <!-- Live Preview Box -->
          <div style="background: #0f172a; border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
              <span style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #ea580c;">Live Portal Card Preview</span>
              <span style="font-size: 0.65rem; color: #94a3b8;">Instant view on index.html</span>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
              <!-- Left Card Preview -->
              <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 8px; padding: 0.65rem; font-size: 0.72rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                  <span style="color: #a7f3d0; font-weight: 800;">OFFICE HOURS</span>
                  <span style="background: #ea580c; color: #fff; font-size: 0.62rem; font-weight: 900; padding: 0.15rem 0.45rem; border-radius: 999px;">SUSPENDED</span>
                </div>
                <strong id="previewLeftTitle" style="color: #ffffff; display: block; font-size: 0.75rem;">Inclement Weather Suspension</strong>
                <span id="previewLeftDesc" style="color: rgba(255, 255, 255, 0.85); display: block; font-size: 0.7rem;">Suspended per Weather Advisory</span>
              </div>
              <!-- Right Card Preview -->
              <div style="background: rgba(234, 88, 12, 0.14); border: 1px solid rgba(249, 115, 22, 0.5); border-radius: 8px; padding: 0.65rem; font-size: 0.72rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                  <span style="color: #fbbf24; font-weight: 800;">PHILIPPINES</span>
                  <span id="previewRightBadge" style="background: rgba(234, 88, 12, 0.3); color: #fed7aa; font-size: 0.62rem; font-weight: 800; padding: 0.15rem 0.45rem; border-radius: 999px;">WEATHER SUSPENSION</span>
                </div>
                <strong id="previewRightTitle" style="color: #fb923c; display: block; font-size: 0.75rem;">Work Suspended: Weather Advisory</strong>
                <span style="color: rgba(255, 255, 255, 0.78); font-size: 0.66rem;">Online requests remain 24/7 active</span>
              </div>
            </div>
          </div>

          <!-- Modal Footer Actions -->
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.65rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
            <button type="button" onclick="handleLiftWeatherSuspension()" id="btnLiftSuspension" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.2s ease;">
              Lift Suspension (Restore Regular Hours)
            </button>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
              <button type="button" onclick="closeWeatherSuspensionModal()" style="background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer;">
                Cancel
              </button>
              <button type="submit" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff; border: 1px solid #9a3412; padding: 0.55rem 1.35rem; border-radius: 8px; font-weight: 800; font-size: 0.84rem; cursor: pointer; box-shadow: 0 4px 14px rgba(234, 88, 12, 0.38); display: inline-flex; align-items: center; gap: 0.45rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9"></path>
                  <polyline points="13 11 9 17 15 17 11 23"></polyline>
                </svg>
                <span>Broadcast Weather Suspension & Secure Facilities</span>
              </button>
            </div>
          </div>

        </form>
      </div>

      <!-- TAB 2: FACILITY SAFETY & LOCKDOWN CHECKLIST -->
      <div id="tabContentChecklist" style="display: none;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; margin-bottom: 1rem;">
          <h4 style="margin: 0 0 0.4rem; color: #0f172a; font-size: 0.88rem; font-weight: 800;">
            🏢 General Services Directorate • Campus Safety Protocols
          </h4>
          <p style="margin: 0 0 0.85rem; font-size: 0.75rem; color: #64748b; line-height: 1.45;">
            Follow these essential emergency risk reduction steps at the ATI Central Office compound (Diliman, Quezon City) prior to storm landfall:
          </p>
          <div style="display: flex; flex-direction: column; gap: 0.55rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.55rem; font-size: 0.77rem; color: #334155; cursor: pointer;">
              <input type="checkbox" checked style="margin-top: 2px; accent-color: #ea580c;">
              <span><strong>Drainage & Sump Pumps:</strong> Inspect and clear perimeter storm drains around Function Hall and basement areas. Ensure automated submersible sump pumps are functional.</span>
            </label>
            <label style="display: flex; align-items: flex-start; gap: 0.55rem; font-size: 0.77rem; color: #334155; cursor: pointer;">
              <input type="checkbox" checked style="margin-top: 2px; accent-color: #ea580c;">
              <span><strong>Backup Generators & Fuel:</strong> Verify 100% fuel capacity for the central standby diesel generator to ensure continuous power for security, servers, and cold storage.</span>
            </label>
            <label style="display: flex; align-items: flex-start; gap: 0.55rem; font-size: 0.77rem; color: #334155; cursor: pointer;">
              <input type="checkbox" checked style="margin-top: 2px; accent-color: #ea580c;">
              <span><strong>Electrical Power Downs:</strong> De-energize outdoor garden lights, marquee signage, and non-essential sub-panels in open-air training tents and courtyards.</span>
            </label>
            <label style="display: flex; align-items: flex-start; gap: 0.55rem; font-size: 0.77rem; color: #334155; cursor: pointer;">
              <input type="checkbox" checked style="margin-top: 2px; accent-color: #ea580c;">
              <span><strong>Dormitory & Guest Guidance:</strong> Account for all currently checked-in dormitory and guesthouse occupants. Provide emergency lanterns, potable water, and emergency evacuation briefing.</span>
            </label>
            <label style="display: flex; align-items: flex-start; gap: 0.55rem; font-size: 0.77rem; color: #334155; cursor: pointer;">
              <input type="checkbox" checked style="margin-top: 2px; accent-color: #ea580c;">
              <span><strong>Campus Vehicle Fleet:</strong> Relocate official ATI vehicles from underneath large narra and acacia trees to elevated concrete bays near the Main Administration building.</span>
            </label>
          </div>
        </div>
      </div>

      <!-- TAB 3: EMERGENCY HOTLINES -->
      <div id="tabContentHotlines" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;" id="emergencyHotlinesGrid">
          <!-- Populated via JavaScript -->
        </div>
        <div style="background: #f1f5f9; border-radius: 8px; padding: 0.75rem; font-size: 0.74rem; color: #475569;">
          <strong>Direct Line to DOST-PAGASA Diliman Forecaster Desk:</strong> (02) 8284-0800 Loc 4801 • Severe Weather Bulletins updated every 3 hours (5 AM, 8 AM, 11 AM, 2 PM, 5 PM, 8 PM, 11 PM).
        </div>
      </div>

    </div>
  </div>
</div>

<script>
// ==========================================================================
// ADVANCED INCLEMENT WEATHER SUSPENSION DIRECTIVE CONTROLLER
// ==========================================================================

let activeWeatherStatus = null;

function openWeatherSuspensionModal() {
  const modal = document.getElementById('adminWeatherSuspensionModal');
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.classList.add('admin-modal-open');

  // Load current status from backend
  fetch('ajax/weather_suspension_actions.php?action=get_status')
    .then(res => res.json())
    .then(res => {
      if (res && res.success && res.data) {
        activeWeatherStatus = res.data;
        populateWeatherForm(res.data);
        if (res.impact) {
          renderImpactData(res.impact);
        }
        if (res.emergency_hotlines) {
          renderHotlines(res.emergency_hotlines);
        }
      }
    })
    .catch(err => {
      console.warn('Failed loading weather status:', err);
    });
}

function closeWeatherSuspensionModal() {
  const modal = document.getElementById('adminWeatherSuspensionModal');
  if (modal) modal.style.display = 'none';
  document.body.classList.remove('admin-modal-open');
}

function switchWeatherTab(tabKey) {
  const tabForm = document.getElementById('tabContentForm');
  const tabChecklist = document.getElementById('tabContentChecklist');
  const tabHotlines = document.getElementById('tabContentHotlines');

  const btnForm = document.getElementById('tabBtnForm');
  const btnChecklist = document.getElementById('tabBtnChecklist');
  const btnHotlines = document.getElementById('tabBtnHotlines');

  const tabs = [
    { key: 'form', content: tabForm, btn: btnForm },
    { key: 'checklist', content: tabChecklist, btn: btnChecklist },
    { key: 'hotlines', content: tabHotlines, btn: btnHotlines }
  ];

  tabs.forEach(t => {
    if (!t.content || !t.btn) return;
    if (t.key === tabKey) {
      t.content.style.display = 'block';
      t.btn.style.background = '#ffffff';
      t.btn.style.color = '#0f172a';
      t.btn.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
    } else {
      t.content.style.display = 'none';
      t.btn.style.background = 'transparent';
      t.btn.style.color = '#64748b';
      t.btn.style.boxShadow = 'none';
    }
  });
}

function renderHotlines(hotlines) {
  const grid = document.getElementById('emergencyHotlinesGrid');
  if (!grid) return;
  grid.innerHTML = hotlines.map(h => `
    <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 0.85rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
      <strong style="font-size: 0.82rem; color: #0f172a; display: block;">${h.agency}</strong>
      <div style="font-size: 0.95rem; font-weight: 800; color: #ea580c; margin: 0.25rem 0;">${h.phone}</div>
      <span style="font-size: 0.72rem; color: #64748b; display: block;">${h.desc}</span>
    </div>
  `).join('');
}

function renderImpactData(impact) {
  const title = document.getElementById('impactBannerTitle');
  const desc = document.getElementById('impactBannerDesc');
  const list = document.getElementById('impactBookingsList');

  if (impact.count > 0) {
    if (title) title.textContent = `⚠️ Safety Risk: ${impact.count} Confirmed Event${impact.count > 1 ? 's' : ''} (${impact.total_pax} Estimated Attendees)`;
    if (desc) desc.textContent = `Official events and facility reservations are booked during this weather window. Activating suspension will automatically secure these bookings.`;
    if (list && impact.bookings) {
      list.innerHTML = impact.bookings.map(b => `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #fef08a; padding-bottom: 0.35rem;">
          <div>
            <strong>${b.event_title || 'Reserved Event'}</strong> (${b.facility_name})
            <div style="color: #64748b; font-size: 0.68rem;">${b.start_date} to ${b.end_date || b.start_date} • ${b.applicant_name} (${b.applicant_phone || 'No phone'})</div>
          </div>
          <span style="background: #fef08a; color: #854d0e; padding: 0.12rem 0.4rem; border-radius: 4px; font-weight: 700;">${b.pax_count || 0} Pax</span>
        </div>
      `).join('');
    }
  } else {
    if (title) title.textContent = '🛡️ Zero Scheduled Events at Risk';
    if (desc) desc.textContent = 'No confirmed reservations or facility bookings found on the Master Schedule for these dates.';
    if (list) list.innerHTML = '<span style="color: #64748b;">No conflicting reservations found.</span>';
  }
}

function toggleImpactDetails() {
  const box = document.getElementById('impactDetailsBox');
  const btn = document.getElementById('btnToggleImpact');
  if (!box) return;
  if (box.style.display === 'none') {
    box.style.display = 'block';
    if (btn) btn.textContent = 'Hide Details';
  } else {
    box.style.display = 'none';
    if (btn) btn.textContent = 'View Bookings';
  }
}

function onDatesChanged() {
  const sDate = document.getElementById('wsStartDate')?.value;
  const eDate = document.getElementById('wsEndDate')?.value || sDate;
  if (!sDate) return;

  fetch(`ajax/weather_suspension_actions.php?action=check_affected&start_date=${sDate}&end_date=${eDate}`)
    .then(res => res.json())
    .then(data => {
      if (data && data.success) {
        renderImpactData(data);
      }
    })
    .catch(() => {});
  
  updateWeatherPreview();
}

function setQuickDates(mode) {
  const now = new Date();
  const format = d => d.toISOString().split('T')[0];

  const sInput = document.getElementById('wsStartDate');
  const eInput = document.getElementById('wsEndDate');

  if (mode === 'today') {
    sInput.value = format(now);
    eInput.value = format(now);
  } else if (mode === 'today_tomorrow') {
    sInput.value = format(now);
    const tom = new Date(now.getTime() + 86400000);
    eInput.value = format(tom);
  } else if (mode === '3days') {
    sInput.value = format(now);
    const d3 = new Date(now.getTime() + 86400000 * 2);
    eInput.value = format(d3);
  }

  onDatesChanged();
}

function populateWeatherForm(data) {
  const isActive = !!data.active;
  const dot = document.getElementById('weatherStatusIndicatorDot');
  const title = document.getElementById('weatherStatusTitle');
  const subtitle = document.getElementById('weatherStatusSubtitle');
  const badge = document.getElementById('weatherStatusBadge');
  const btnLift = document.getElementById('btnLiftSuspension');

  if (isActive) {
    if (dot) dot.style.background = '#ea580c';
    if (title) title.textContent = 'Inclement Weather Suspension Currently Active';
    if (subtitle) subtitle.textContent = `Office work suspended: ${data.typhoon_name || 'Severe Weather'} (${data.signal_level || 'Weather Advisory'}). Broadcasted on public portal.`;
    if (badge) {
      badge.textContent = 'SUSPENDED';
      badge.style.background = 'rgba(234, 88, 12, 0.15)';
      badge.style.color = '#ea580c';
      badge.style.borderColor = 'rgba(234, 88, 12, 0.4)';
    }
    if (btnLift) btnLift.style.display = 'inline-block';
  } else {
    if (dot) dot.style.background = '#22c55e';
    if (title) title.textContent = 'Standard Office Operations Active';
    if (subtitle) subtitle.textContent = 'No active weather suspension declared. Regular physical counter hours apply.';
    if (badge) {
      badge.textContent = 'NORMAL';
      badge.style.background = 'rgba(34, 197, 94, 0.12)';
      badge.style.color = '#16a34a';
      badge.style.borderColor = 'rgba(34, 197, 94, 0.3)';
    }
    if (btnLift) btnLift.style.display = 'none';
  }

  // Populate inputs
  if (data.typhoon_name) document.getElementById('wsTyphoonName').value = data.typhoon_name;
  if (data.signal_level) document.getElementById('wsSignalLevel').value = data.signal_level;
  if (data.start_date) document.getElementById('wsStartDate').value = data.start_date;
  if (data.end_date) document.getElementById('wsEndDate').value = data.end_date;
  if (data.memo_ref) document.getElementById('wsMemoRef').value = data.memo_ref;
  if (data.badge_text) document.getElementById('wsBadgeText').value = data.badge_text;
  if (data.description) document.getElementById('wsDescription').value = data.description;
  if (data.resumption_note) document.getElementById('wsResumptionNote').value = data.resumption_note;
  if (data.auto_lock_master_schedule !== undefined) {
    document.getElementById('wsAutoLockMaster').checked = !!data.auto_lock_master_schedule;
  }

  updateWeatherPreview();
}

function onSignalLevelChanged() {
  const signal = document.getElementById('wsSignalLevel')?.value || '';
  const badgeInput = document.getElementById('wsBadgeText');
  const memoInput = document.getElementById('wsMemoRef');

  if (signal.includes('Signal No. 3')) {
    if (badgeInput) badgeInput.value = 'TYPHOON ALERT';
    if (memoInput && !memoInput.value) memoInput.value = 'NDRRMC / DOST-PAGASA Bulletin / EO 66';
  } else if (signal.includes('Signal No. 4') || signal.includes('Signal No. 5')) {
    if (badgeInput) badgeInput.value = 'SUPER TYPHOON';
    if (memoInput && !memoInput.value) memoInput.value = 'Presidential Directive / NDRRMC Red Alert';
  } else if (signal.includes('Heavy Rainfall')) {
    if (badgeInput) badgeInput.value = 'RAIN SUSPENSION';
    if (memoInput && !memoInput.value) memoInput.value = 'DOST-PAGASA / NDRRMC Rainfall Warning';
  } else if (signal.includes('Heat')) {
    if (badgeInput) badgeInput.value = 'HEAT DANGER';
    if (memoInput && !memoInput.value) memoInput.value = 'CSC Memorandum Circular No. 17 (s. 2022)';
  } else {
    if (badgeInput) badgeInput.value = 'WEATHER SUSPENSION';
    if (memoInput && !memoInput.value) memoInput.value = 'Executive Order No. 66 (s. 2012) / NDRRMC';
  }

  updateWeatherPreview();
}

function applyWeatherPreset(presetKey) {
  const nameInput = document.getElementById('wsTyphoonName');
  const levelSelect = document.getElementById('wsSignalLevel');
  const memoInput = document.getElementById('wsMemoRef');
  const descArea = document.getElementById('wsDescription');
  const badgeInput = document.getElementById('wsBadgeText');
  const resumptionInput = document.getElementById('wsResumptionNote');

  if (presetKey === 'signal2') {
    if (nameInput) nameInput.value = 'Severe Tropical Storm';
    if (levelSelect) levelSelect.value = 'TCWS Signal No. 2';
    if (memoInput) memoInput.value = 'Executive Order No. 66 (s. 2012) / NDRRMC Memo';
    if (badgeInput) badgeInput.value = 'WEATHER SUSPENSION';
    if (descArea) descArea.value = 'Pursuant to NDRRMC / DOST-PAGASA Tropical Cyclone Wind Signal (TCWS) No. 2 and Executive Order No. 66, work in government offices at ATI Central Office is officially suspended today for public safety. Online reservation requests remain active 24/7.';
    if (resumptionInput) resumptionInput.value = 'Regular on-site counter operations resume next business day at 8:00 AM (PST).';
  } else if (presetKey === 'signal3') {
    if (nameInput) nameInput.value = 'Typhoon';
    if (levelSelect) levelSelect.value = 'TCWS Signal No. 3';
    if (memoInput) memoInput.value = 'Executive Order No. 66 / NDRRMC / DOST-PAGASA Severe Bulletin';
    if (badgeInput) badgeInput.value = 'TYPHOON ALERT';
    if (descArea) descArea.value = 'NDRRMC / DOST-PAGASA has raised TCWS Signal No. 3 over Metro Manila. Physical operations and counter services at ATI Central Office are completely suspended for public safety. Online facility inquiries remain active.';
    if (resumptionInput) resumptionInput.value = 'Operations will resume upon lifting of NDRRMC / DOST-PAGASA storm signals.';
  } else if (presetKey === 'signal4') {
    if (nameInput) nameInput.value = 'Super Typhoon';
    if (levelSelect) levelSelect.value = 'TCWS Signal No. 5';
    if (memoInput) memoInput.value = 'NDRRMC Critical Emergency Directive / Malacañang Order';
    if (badgeInput) badgeInput.value = 'SUPER TYPHOON';
    if (descArea) descArea.value = 'CRITICAL WEATHER EMERGENCY: Super Typhoon warning in effect over Metro Manila. ATI Central Office is in complete lockdown. All events are cancelled and personnel are sheltered in place.';
    if (resumptionInput) resumptionInput.value = 'Operations will resume after official NDRRMC safety clearance.';
  } else if (presetKey === 'heavyrain') {
    if (nameInput) nameInput.value = 'Heavy Monsoon Rainfall';
    if (levelSelect) levelSelect.value = 'Heavy Rainfall Red Warning';
    if (memoInput) memoInput.value = 'DOST-PAGASA / NDRRMC / QC-DRRMO Advisory';
    if (badgeInput) badgeInput.value = 'RAIN SUSPENSION';
    if (descArea) descArea.value = 'Due to torrential rains and widespread flooding threat under DOST-PAGASA / NDRRMC Red Rainfall Warning, ATI Central Office operations are suspended. Online bookings remain active.';
    if (resumptionInput) resumptionInput.value = 'On-site services resume tomorrow at 8:00 AM (PST).';
  } else if (presetKey === 'monsoon') {
    if (nameInput) nameInput.value = 'Southwest Monsoon (Habagat)';
    if (levelSelect) levelSelect.value = 'Southwest Monsoon Flooding';
    if (memoInput) memoInput.value = 'MMDA & QC-DRRMC Flood Advisory / OP Directive';
    if (badgeInput) badgeInput.value = 'FLOOD ADVISORY';
    if (descArea) descArea.value = 'Enhanced Southwest Monsoon (Habagat) has caused severe road inundation and flood hazards across Metro Manila. On-site desk operations at ATI Central Office are suspended.';
    if (resumptionInput) resumptionInput.value = 'Regular operations resume when floodwaters subside.';
  } else if (presetKey === 'malacanang') {
    if (nameInput) nameInput.value = 'Inclement Weather & Flooding';
    if (levelSelect) levelSelect.value = 'Executive Work Suspension';
    if (memoInput) memoInput.value = 'Office of the President / Malacañang Circular';
    if (badgeInput) badgeInput.value = 'GOV SUSPENSION';
    if (descArea) descArea.value = 'Pursuant to Memorandum Circular from the Office of the President, work in government offices is suspended due to inclement weather. Digital reservation desk remains operational.';
    if (resumptionInput) resumptionInput.value = 'Regular operations resume on the next official working day.';
  } else if (presetKey === 'heat') {
    if (nameInput) nameInput.value = 'Extreme Danger Heat Index';
    if (levelSelect) levelSelect.value = 'Extreme Heat Index';
    if (memoInput) memoInput.value = 'Civil Service Commission MC No. 17 (s. 2022)';
    if (badgeInput) badgeInput.value = 'HEAT DANGER';
    if (descArea) descArea.value = 'DOST-PAGASA / NDRRMC forecasts a hazardous Heat Index exceeding 42°C in Metro Manila. Under Civil Service Commission guidelines, flexible and remote work arrangements are activated for personnel and client safety.';
    if (resumptionInput) resumptionInput.value = 'Regular counter hours resume next business day.';
  }

  updateWeatherPreview();
}

function updateWeatherPreview() {
  const typhoonName = document.getElementById('wsTyphoonName')?.value || 'Tropical Cyclone';
  const signal = document.getElementById('wsSignalLevel')?.value || 'TCWS Signal No. 2';
  const badgeText = document.getElementById('wsBadgeText')?.value || 'WEATHER SUSPENSION';

  const previewLeftDesc = document.getElementById('previewLeftDesc');
  const previewRightBadge = document.getElementById('previewRightBadge');
  const previewRightTitle = document.getElementById('previewRightTitle');

  if (previewLeftDesc) previewLeftDesc.textContent = `${typhoonName} (${signal})`;
  if (previewRightBadge) previewRightBadge.textContent = badgeText;
  if (previewRightTitle) previewRightTitle.textContent = `Work Suspended: ${typhoonName}`;
}

// Live typing preview
document.getElementById('wsTyphoonName')?.addEventListener('input', updateWeatherPreview);
document.getElementById('wsBadgeText')?.addEventListener('input', updateWeatherPreview);

function handleSaveWeatherSuspension(e) {
  if (e) e.preventDefault();

  const typhoonName = document.getElementById('wsTyphoonName').value.trim();
  const signalLevel = document.getElementById('wsSignalLevel').value;
  const startDate = document.getElementById('wsStartDate').value;
  const endDate = document.getElementById('wsEndDate').value || startDate;
  const memoRef = document.getElementById('wsMemoRef').value.trim();
  const badgeText = document.getElementById('wsBadgeText').value.trim();
  const description = document.getElementById('wsDescription').value.trim();
  const resumptionNote = document.getElementById('wsResumptionNote').value.trim();
  const autoLockMaster = document.getElementById('wsAutoLockMaster')?.checked ? '1' : '0';

  const formData = new FormData();
  formData.append('action', 'save_suspension');
  formData.append('active', '1');
  formData.append('typhoon_name', typhoonName);
  formData.append('signal_level', signalLevel);
  formData.append('start_date', startDate);
  formData.append('end_date', endDate);
  formData.append('memo_ref', memoRef);
  formData.append('badge_text', badgeText);
  formData.append('description', description);
  formData.append('resumption_note', resumptionNote);
  formData.append('auto_lock_master_schedule', autoLockMaster);

  fetch('ajax/weather_suspension_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      if (res && res.success) {
        if (typeof showAdminToast === 'function') {
          showAdminToast('Inclement Weather Suspension successfully broadcasted & Master Schedule locked!', 'confirm');
        } else {
          alert('Inclement Weather Suspension successfully broadcasted and synchronized with the Master Schedule!');
        }
        closeWeatherSuspensionModal();
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(res.message || 'Failed to save weather suspension.');
      }
    })
    .catch(err => {
      alert('Network error while saving weather suspension.');
    });
}

function handleLiftWeatherSuspension() {
  if (!confirm('Are you sure you want to lift the weather suspension and restore normal office operations and Master Schedule access?')) {
    return;
  }

  const formData = new FormData();
  formData.append('action', 'lift_suspension');

  fetch('ajax/weather_suspension_actions.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(res => {
      if (res && res.success) {
        if (typeof showAdminToast === 'function') {
          showAdminToast('Weather suspension lifted. Regular office hours and Master Schedule restored.', 'confirm');
        } else {
          alert('Weather suspension lifted. Regular office hours and Master Schedule restored.');
        }
        closeWeatherSuspensionModal();
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(res.message || 'Failed to lift suspension.');
      }
    })
    .catch(err => {
      alert('Network error while lifting weather suspension.');
    });
}
</script>
