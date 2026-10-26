<?php
require_once __DIR__ . '/includes/init.php';
/**
 * Agriculture Training Institute - Facility and Dormitory Reservation System
 * Authentication & Account Registration Portal
 */
$initialTab = isset($_GET['tab']) && $_GET['tab'] === 'login' ? 'login' : 'register';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Registration & Login - Agricultural Training Institute</title>
  <meta name="description"
    content="Register for official ATI facility reservation authorization or log in to manage your bookings.">
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="assets/images/ATI_Logo.png">
</head>

<body class="auth-page-wrapper">

  <!-- Return Link -->
  <a href="index.php" class="auth-nav-back">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
      stroke-linecap="round" stroke-linejoin="round">
      <line x1="19" y1="12" x2="5" y2="12"></line>
      <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    <span>Return to Home</span>
  </a>

  <!-- Official ATI Brand Header -->
  <div class="auth-brand-header">
    <div class="auth-brand-logo-box">
      <img src="assets/images/ATI_Logo.png" alt="Agriculture Training Institute Logo" class="auth-brand-logo">
    </div>
    <div class="auth-brand-text">
      <h1>Agricultural Training Institute</h1>
      <p>Facility and Dormitory Reservation System</p>
    </div>
  </div>

  <!-- Central Authentication Card -->
  <div class="auth-card" id="authCard">
    <!-- Navigation Tabs -->
    <div class="auth-tabs" role="tablist">
      <button type="button" class="auth-tab-btn <?= $initialTab === 'login' ? 'active' : '' ?>" id="tabBtnSignIn"
        role="tab" aria-selected="<?= $initialTab === 'login' ? 'true' : 'false' ?>">
        <!-- Sign In Icon -->
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
          <polyline points="10 17 15 12 10 7"></polyline>
          <line x1="15" y1="12" x2="3" y2="12"></line>
        </svg>
        <span>Sign In</span>
      </button>

      <button type="button" class="auth-tab-btn <?= $initialTab === 'register' ? 'active' : '' ?>" id="tabBtnRegister"
        role="tab" aria-selected="<?= $initialTab === 'register' ? 'true' : 'false' ?>">
        <!-- User Plus Icon -->
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="8.5" cy="7" r="4"></circle>
          <line x1="20" y1="8" x2="20" y2="14"></line>
          <line x1="23" y1="11" x2="17" y2="11"></line>
        </svg>
        <span>Create Account</span>
      </button>
    </div>

    <div class="auth-panel-body">
      <!-- ==========================================
           TAB PANEL 1: CREATE ACCOUNT (REGISTRATION)
           ========================================== -->
      <div class="auth-tab-panel <?= $initialTab === 'register' ? 'active' : '' ?>" id="panelRegister" role="tabpanel">
        <div class="auth-panel-header">
          <h2 class="auth-heading">Create New Account</h2>
          <p class="auth-subheading">Register for official facility reservation authorization.</p>
        </div>

        <form id="registrationForm" onsubmit="handleRegistrationSubmit(event)">
          <!-- Inline Alert Feedback -->
          <div class="auth-alert-banner auth-alert-error" id="registerAlert" role="alert" style="display: none;">
            <svg class="auth-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span class="auth-alert-text" id="registerAlertText"></span>
          </div>

          <!-- Account Category Selector -->
          <div class="category-group">
            <label class="category-group-label">Account Category</label>
            <div class="category-options">
              <!-- Category 1: ATI Personnel -->
              <div class="category-card active" data-category="ati" id="catAti">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <circle cx="19" cy="11" r="2"></circle>
                  <path d="M19 8v1M19 13v1M16 11h1M21 11h1"></path>
                </svg>
                <span class="category-card-name">ATI Personnel</span>
              </div>

              <!-- Category 2: Gov Agency -->
              <div class="category-card" data-category="gov" id="catGov">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M3 21h18M3 10h18M5 10v11M19 10v11M9 10v11M15 10v11M12 2l9 5H3l9-5z"></path>
                </svg>
                <span class="category-card-name">Gov Agency</span>
              </div>

              <!-- Category 3: Private / Guest -->
              <div class="category-card" data-category="private" id="catPrivate">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span class="category-card-name">Private / Guest</span>
              </div>
            </div>

            <!-- Dynamic Category Helper Hint -->
            <div class="category-helper-hint" id="categoryHint">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
              <span id="categoryHintText">For ATI Central Office & Regional Training Centers (RTC) staff members.</span>
            </div>
          </div>

          <!-- Full Name & Office / Agency (2-Column Grid) -->
          <div class="auth-form-row">
            <!-- Full Name -->
            <div class="auth-field-group">
              <label class="auth-field-label" for="regFullName">Full Name</label>
              <div class="auth-input-wrapper">
                <input type="text" id="regFullName" class="auth-input" placeholder="Juan Dela Cruz" required>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
              </div>
            </div>

            <!-- Dynamic Office / Division / Agency Field -->
            <div class="auth-field-group">
              <label class="auth-field-label" for="regOffice" id="labelOffice">Office / Division Name</label>
              <div class="auth-input-wrapper custom-select-container" id="officeSelectContainer">
                <input type="text" id="regOffice" class="auth-input custom-select-input" placeholder="Select your Division" required readonly>
                <svg class="custom-select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
                <!-- Organization / Branch Icon -->
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="3" width="7" height="7"></rect>
                  <rect x="14" y="3" width="7" height="7"></rect>
                  <rect x="14" y="14" width="7" height="7"></rect>
                  <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                
                <div class="custom-options-panel" id="officeOptionsPanel">
                  <!-- Options are dynamically generated by js/auth.js based on selected category -->
                </div>
              </div>
            </div>
          </div>

          <!-- Email Address -->
          <div class="auth-field-group">
            <label class="auth-field-label" for="regEmail">Email Address</label>
            <div class="auth-input-wrapper">
              <input type="email" id="regEmail" class="auth-input" placeholder="juan.delacruz@ati.da.gov.ph" required>
              <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </div>
          </div>

          <!-- Password & Confirm Password (2-Column Grid) -->
          <div class="auth-form-row">
            <!-- Password -->
            <div class="auth-field-group">
              <label class="auth-field-label" for="regPassword">Password</label>
              <div class="auth-input-wrapper">
                <input type="password" id="regPassword" class="auth-input" placeholder="••••••••" required
                  minlength="8">
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <button type="button" class="auth-toggle-pwd" data-target="regPassword"
                  aria-label="Toggle password visibility">
                  <svg viewBox="0 0 24 24" fill="none">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              <!-- Strength Bar -->
              <div class="pwd-strength-container">
                <div class="pwd-strength-bar">
                  <div class="pwd-strength-fill" id="pwdStrengthFill"></div>
                </div>
                <span class="pwd-strength-label" id="pwdStrengthLabel"></span>
              </div>
            </div>

            <!-- Confirm Password -->
            <div class="auth-field-group">
              <label class="auth-field-label" for="regConfirmPassword">Confirm Password</label>
              <div class="auth-input-wrapper">
                <input type="password" id="regConfirmPassword" class="auth-input" placeholder="••••••••" required
                  minlength="8">
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <button type="button" class="auth-toggle-pwd" data-target="regConfirmPassword"
                  aria-label="Toggle confirm password visibility">
                  <svg viewBox="0 0 24 24" fill="none">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </button>
              </div>
              <div class="pwd-match-indicator" id="pwdMatchIndicator"></div>
            </div>
          </div>

          <!-- Terms & Privacy Agreement -->
          <div class="terms-agreement-row">
            <input type="checkbox" id="regTerms" class="terms-checkbox" required>
            <label for="regTerms">
              I agree to the <a href="javascript:void(0)" class="js-open-terms">Terms of Reservation</a> and <a
                href="javascript:void(0)" class="js-open-privacy">Data Privacy Policy</a> of ATI.
            </label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-auth-primary" id="btnRegisterSubmit">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="8.5" cy="7" r="4"></circle>
              <line x1="20" y1="8" x2="20" y2="14"></line>
              <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <span>Register Account</span>
          </button>
        </form>

        <div class="auth-switch-footer">
          <span>Already have an account?</span>
          <button type="button" class="auth-switch-link js-switch-to-signin">Sign In</button>
        </div>
      </div>

      <!-- ==========================================
           TAB PANEL 2: SIGN IN (LOGIN)
           ========================================== -->
      <div class="auth-tab-panel <?= $initialTab === 'login' ? 'active' : '' ?>" id="panelSignIn" role="tabpanel">
        <div class="auth-panel-header">
          <h2 class="auth-heading">Welcome Back</h2>
          <p class="auth-subheading">Sign in to access your reservation requests and facility bookings.</p>
        </div>

        <form id="signInForm" onsubmit="handleLoginSubmit(event)">
          <!-- Inline Alert / Error Feedback -->
          <div class="auth-alert-banner auth-alert-error" id="loginAlert" role="alert" style="display: none;">
            <svg class="auth-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span class="auth-alert-text" id="loginAlertText"></span>
          </div>

          <!-- Email / Username -->
          <div class="auth-field-group">
            <label class="auth-field-label" for="loginEmailInput">Email or Authorized Username</label>
            <div class="auth-input-wrapper">
              <input type="text" id="loginEmailInput" class="auth-input" placeholder="e.g. employee@ati.da.gov.ph"
                required>
              <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </div>
          </div>

          <!-- Password -->
          <div class="auth-field-group">
            <label class="auth-field-label" for="loginPwdInput">Password</label>
            <div class="auth-input-wrapper">
              <input type="password" id="loginPwdInput" class="auth-input" placeholder="Enter your password" required>
              <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
              <button type="button" class="auth-toggle-pwd" data-target="loginPwdInput"
                aria-label="Toggle password visibility">
                <svg viewBox="0 0 24 24" fill="none">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          <!-- Options -->
          <div class="auth-remember-row">
            <label class="remember-label">
              <input type="checkbox" checked class="remember-checkbox">
              <span>Remember this device</span>
            </label>
            <a href="javascript:void(0)"
              class="forgot-link js-open-forgot" id="forgotPasswordLink">Forgot password?</a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-auth-primary">
            <svg viewBox="0 0 24 24" fill="none">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
              <polyline points="10 17 15 12 10 7"></polyline>
              <line x1="15" y1="12" x2="3" y2="12"></line>
            </svg>
            <span>Sign In to Account</span>
          </button>
        </form>

        <div class="auth-switch-footer">
          <span>Don't have an account?</span>
          <button type="button" class="auth-switch-link js-switch-to-register">Create Account</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Forgot Password Modal -->
  <div class="modal-overlay" id="forgotPasswordModal">
    <div class="modal-card" style="max-width: 480px;">
      <button class="modal-close-btn js-close-modal" aria-label="Close modal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="modal-header-banner">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,0.18); margin-bottom: 0.65rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </div>
        <h3 style="margin: 0; font-size: 1.25rem;">Account Password Assistance</h3>
        <p style="margin: 0.25rem 0 0 0; opacity: 0.9; font-size: 0.85rem;">Agricultural Training Institute &bull; Central Office</p>
      </div>

      <div class="modal-body" style="padding: 1.5rem; font-size: 0.9rem; color: #3b5044;">
        <!-- Alert Banner inside modal -->
        <div id="forgotAlert" class="auth-alert-banner" style="display: none; margin-bottom: 1.15rem;">
          <svg class="auth-alert-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <span class="auth-alert-text" id="forgotAlertText"></span>
        </div>

        <p style="margin: 0 0 1.15rem 0; font-size: 0.86rem; color: #4b6354; line-height: 1.55;">
          Pursuant to institutional IT security guidelines, credentials are reset and issued by the <strong>ATI ICT Support / Administrative Division</strong> or authorized Division Officers.
        </p>

        <form id="forgotPasswordForm" onsubmit="handleForgotPasswordSubmit(event)">
          <div class="auth-field-group" style="margin-bottom: 1.25rem;">
            <label class="auth-field-label" for="forgotEmailInput">Enter Your Registered Official Email</label>
            <div class="auth-input-wrapper">
              <input type="email" id="forgotEmailInput" class="auth-input" placeholder="e.g. employee@ati.da.gov.ph" required>
              <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
            </div>
            <span style="display: block; font-size: 0.76rem; color: #6e8879; margin-top: 0.35rem;">
              We will verify your record and log your reset assistance ticket.
            </span>
          </div>

          <button type="submit" class="btn-auth-primary" id="btnForgotSubmit" style="width: 100%; height: 44px; margin-bottom: 1.25rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 2L11 13"></path>
              <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
            <span>Submit Reset Request</span>
          </button>
        </form>

        <!-- Helpdesk Direct Contacts -->
        <div style="background: #f4f8f5; border: 1.5px solid #d8e6dc; border-radius: 12px; padding: 0.95rem 1.1rem;">
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.3">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <strong style="font-size: 0.84rem; color: #143823;">Need Immediate Assistance?</strong>
          </div>
          <div style="font-size: 0.8rem; color: #476352; display: flex; flex-direction: column; gap: 0.35rem; line-height: 1.45;">
            <div>🏛️ <strong>Office:</strong> Administrative & Finance Unit, ATI Central Office</div>
            <div>✉️ <strong>Support Email:</strong> <a href="mailto:support@ati.da.gov.ph" style="color: #15803d; font-weight: 600; text-decoration: underline;">support@ati.da.gov.ph</a></div>
            <div>📞 <strong>Trunkline:</strong> (02) 8929-8541 to 49 (Loc. 250 / 251)</div>
          </div>
        </div>

        <button type="button" class="btn-system-secondary js-close-modal" style="width: 100%; margin-top: 1.25rem; height: 40px; font-size: 0.85rem; font-weight: 700; border-radius: 10px; cursor: pointer; border: 1.5px solid #d0ded4; background: #ffffff; color: #2a4c37;">
          Back to Sign In
        </button>
      </div>
    </div>
  </div>

  <!-- Terms Modal -->
  <div class="modal-overlay" id="termsModal">
    <div class="modal-card">
      <button class="modal-close-btn js-close-modal" aria-label="Close modal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div class="modal-header-banner">
        <h3>Terms of Reservation</h3>
        <p>Agricultural Training Institute</p>
      </div>
      <div class="modal-body" style="font-size: 0.9rem; color: #3b5044; line-height: 1.6;">
        <p style="margin-bottom: 0.8rem;">1. Facility reservations must be submitted at least <strong>5 business
            days</strong> prior to the requested schedule.</p>
        <p style="margin-bottom: 0.8rem;">2. Official Department of Agriculture activities and institutional extension
          workshops take precedence in scheduling.</p>
        <p style="margin-bottom: 0.8rem;">3. Cancellations or schedule modifications must be reported to the
          Administrative Services Unit.</p>
        <p>4. Users are accountable for preserving the facility cleanliness and audiovisual equipment condition.</p>
        <button type="button" class="btn-auth-primary js-close-modal" style="margin-top: 1.5rem;">I Understand</button>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div class="modal-overlay" id="privacyModal">
    <div class="modal-card">
      <button class="modal-close-btn js-close-modal" aria-label="Close modal">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <div class="modal-header-banner">
        <h3>Data Privacy Policy</h3>
        <p>Republic Act No. 10173 Compliance</p>
      </div>
      <div class="modal-body" style="font-size: 0.9rem; color: #3b5044; line-height: 1.6;">
        <p style="margin-bottom: 0.8rem;">The <strong>Agricultural Training Institute (ATI)</strong> is committed to
          safeguarding personal information collected for reservation records.</p>
        <p style="margin-bottom: 0.8rem;">Your name, email address, agency affiliation, and contact details will only be
          used for communication, authorization, and facility management purposes.</p>
        <p>We do not share your private data with unauthorized external entities.</p>
        <button type="button" class="btn-auth-primary js-close-modal" style="margin-top: 1.5rem;">Accept Policy</button>
      </div>
    </div>
  </div>

  <script src="js/auth.js?v=<?php echo time(); ?>"></script>
</body>

</html>