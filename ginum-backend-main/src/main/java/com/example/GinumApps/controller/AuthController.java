package com.example.GinumApps.controller;

import com.example.GinumApps.dto.AuthRequest;
import com.example.GinumApps.dto.ForgotPasswordRequest;
import com.example.GinumApps.dto.ResetPasswordRequest;
import com.example.GinumApps.dto.LoginResponse;
import com.example.GinumApps.model.Admin;
import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.repository.AdminRepository;
import com.example.GinumApps.repository.AppUserRepository;
import com.example.GinumApps.repository.CompanyRepository;
import com.example.GinumApps.util.JwtUtil;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.BadCredentialsException;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.web.bind.annotation.*;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import java.time.LocalDateTime;
import com.example.GinumApps.model.AppUser;
import org.springframework.http.ResponseCookie;
import org.springframework.http.HttpHeaders;
import jakarta.servlet.http.HttpServletResponse;

// AuthController.java
@RestController
@RequestMapping("/api/auth")
@RequiredArgsConstructor
public class AuthController {

    private static final Logger logger = LoggerFactory.getLogger(AuthController.class);

    private static final int MAX_FAILED_ATTEMPTS = 5;
    private static final int LOCK_TIME_DURATION_MINUTES = 15;

    private final AuthenticationManager authenticationManager;
    private final JwtUtil jwtUtil;
    private final AdminRepository adminRepository;
    private final CompanyRepository companyRepository;
    private final AppUserRepository userRepository;
    private final com.example.GinumApps.service.MfaService mfaService;
    private final com.example.GinumApps.service.EmailService emailService;
    private final com.example.GinumApps.service.RoleService roleService;
    private final org.springframework.security.crypto.password.PasswordEncoder passwordEncoder;

    private static final java.util.concurrent.ConcurrentHashMap<String, String> otpStorage = new java.util.concurrent.ConcurrentHashMap<>();

    @CrossOrigin
    @PostMapping("/login")
    public ResponseEntity<?> login(@Valid @RequestBody AuthRequest authRequest) {
        try {
            String cleanEmail = authRequest.getEmail() != null ? authRequest.getEmail().trim() : "";

            java.util.Optional<AppUser> userOpt = userRepository.findByEmailIgnoreCase(cleanEmail);
            java.util.Optional<Company> checkCompanyOpt = companyRepository.findByEmailIgnoreCase(cleanEmail);

            if (userOpt.isEmpty() && checkCompanyOpt.isPresent()) {
                Company company = checkCompanyOpt.get();
                if (!company.isEmailVerified()) {
                    return ResponseEntity.status(HttpStatus.FORBIDDEN)
                            .body(java.util.Map.of("error", "Email not verified. Please check your inbox and click the verification link before logging in.", "emailUnverified", true));
                }
            }

            if (userOpt.isPresent()) {
                AppUser user = userOpt.get();
                if (user.getLockTime() != null) {
                    if (user.getLockTime().isAfter(LocalDateTime.now())) {
                        return ResponseEntity.status(HttpStatus.LOCKED).body(java.util.Map.of("error", "Your account has been locked due to multiple failed login attempts. Please try again later."));
                    } else {
                        // Lock expired, unlock account
                        user.setLockTime(null);
                        user.setFailedAttempts(0);
                        userRepository.save(user);
                    }
                }
            }

            Authentication authentication = authenticationManager.authenticate(
                    new UsernamePasswordAuthenticationToken(
                            cleanEmail,
                            authRequest.getPassword()
                    )
            );

            UserDetails userDetails = (UserDetails) authentication.getPrincipal();
            String role = authentication.getAuthorities().iterator().next().getAuthority();

            boolean mfaEnabled = false;
            String mfaSecret = null;
            if (userOpt.isPresent()) {
                mfaEnabled = userOpt.get().isMfaEnabled();
                mfaSecret = userOpt.get().getMfaSecret();
            } else if (checkCompanyOpt.isPresent()) {
                mfaEnabled = checkCompanyOpt.get().isMfaEnabled();
                mfaSecret = checkCompanyOpt.get().getMfaSecret();
            }

            if (mfaEnabled) {
                if (authRequest.getMfaCode() == null || authRequest.getMfaCode().isEmpty()) {
                    LoginResponse loginResponse = new LoginResponse();
                    loginResponse.setMfaRequired(true);
                    return ResponseEntity.ok(loginResponse);
                } else {
                    if (!mfaService.verifyTotp(authRequest.getMfaCode(), mfaSecret)) {
                        return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(java.util.Map.of("error", "Invalid MFA Code"));
                    }
                }
            }

            // Per-role OTP requirement check
            Long compId = null;
            if (userOpt.isPresent() && userOpt.get().getCompany() != null) {
                compId = userOpt.get().getCompany().getCompanyId().longValue();
            } else if (checkCompanyOpt.isPresent()) {
                compId = checkCompanyOpt.get().getCompanyId().longValue();
            }

            if (compId != null) {
                String cleanRoleName = role.replace("ROLE_", "");
                java.util.Optional<com.example.GinumApps.model.Role> roleEntityOpt = roleService.findByCompanyAndName(compId, cleanRoleName);
                if (roleEntityOpt.isPresent() && Boolean.TRUE.equals(roleEntityOpt.get().getOtpRequired())) {
                    String providedOtp = authRequest.getLoginOtp();
                    if (providedOtp == null || providedOtp.trim().isEmpty()) {
                        // Generate & send OTP
                        String generatedOtp = String.format("%06d", new java.util.Random().nextInt(900000) + 100000);
                        otpStorage.put(authRequest.getEmail().toLowerCase(), generatedOtp);
                        try {
                            emailService.sendOtpEmail(authRequest.getEmail(), generatedOtp);
                        } catch (Exception ex) {
                            logger.error("Failed to send login OTP to " + authRequest.getEmail(), ex);
                        }
                        return ResponseEntity.ok(java.util.Map.of(
                                "otpRequired", true,
                                "email", authRequest.getEmail(),
                                "message", "OTP required for your role. A 6-digit verification code has been sent to your email."
                        ));
                    } else {
                        String storedOtp = otpStorage.get(authRequest.getEmail().toLowerCase());
                        if (storedOtp == null || !storedOtp.equals(providedOtp.trim())) {
                            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(java.util.Map.of("error", "Invalid or expired OTP code"));
                        }
                        otpStorage.remove(authRequest.getEmail().toLowerCase());
                    }
                }
            }

            String token = jwtUtil.generateToken(userDetails.getUsername(), role);

            logger.info("Successful login for user: {}, role: {}", userDetails.getUsername(), role);

            // Reset failed attempts on success
            if (userOpt.isPresent()) {
                AppUser user = userOpt.get();
                if (user.getFailedAttempts() > 0 || user.getLockTime() != null) {
                    user.setFailedAttempts(0);
                    user.setLockTime(null);
                    userRepository.save(user);
                }
            }

            ResponseCookie jwtCookie = ResponseCookie.from("jwt", token)
                    .httpOnly(true)
                    .secure(false) // Assuming cross-origin HTTP without HTTPS
                    .path("/")
                    .maxAge(10 * 60 * 60)
                    .sameSite("Lax")
                    .build();

            ResponseCookie loggedInCookie = ResponseCookie.from("isLoggedIn", "true")
                    .httpOnly(false)
                    .secure(false)
                    .path("/")
                    .maxAge(10 * 60 * 60)
                    .sameSite("Lax")
                    .build();

            return ResponseEntity.ok()
                    .header(HttpHeaders.SET_COOKIE, jwtCookie.toString())
                    .header(HttpHeaders.SET_COOKIE, loggedInCookie.toString())
                    .body(buildLoginResponse(userDetails.getUsername(), role, token)); // Keeping token in body for backwards compatibility with mobile app

        } catch (BadCredentialsException e) {
            logger.warn("Failed login attempt for email: {}", authRequest.getEmail());
            
            java.util.Optional<AppUser> userOpt = userRepository.findByEmail(authRequest.getEmail());
            if (userOpt.isPresent()) {
                AppUser user = userOpt.get();
                int newFailures = user.getFailedAttempts() + 1;
                user.setFailedAttempts(newFailures);
                if (newFailures >= MAX_FAILED_ATTEMPTS) {
                    user.setLockTime(LocalDateTime.now().plusMinutes(LOCK_TIME_DURATION_MINUTES));
                    logger.warn("Account locked for email: {}", authRequest.getEmail());
                }
                userRepository.save(user);
            }
            
            return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(java.util.Map.of("error", "Invalid credentials"));
        } catch (RuntimeException e) {
            logger.error("Error during login for email: {}", authRequest.getEmail(), e);
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(java.util.Map.of("error", e.getMessage()));
        }
    }

    @CrossOrigin
    @PostMapping("/logout")
    public ResponseEntity<?> logout() {
        ResponseCookie jwtCookie = ResponseCookie.from("jwt", "")
                .httpOnly(true)
                .secure(false)
                .path("/")
                .maxAge(0)
                .sameSite("Lax")
                .build();

        ResponseCookie loggedInCookie = ResponseCookie.from("isLoggedIn", "")
                .httpOnly(false)
                .secure(false)
                .path("/")
                .maxAge(0)
                .sameSite("Lax")
                .build();

        return ResponseEntity.ok()
                .header(HttpHeaders.SET_COOKIE, jwtCookie.toString())
                .header(HttpHeaders.SET_COOKIE, loggedInCookie.toString())
                .body(java.util.Map.of("message", "Logged out successfully"));
    }

    @PostMapping("/forgot-password")
    public ResponseEntity<?> forgotPassword(@RequestBody com.example.GinumApps.dto.ForgotPasswordRequest request) {
        String email = request.getEmail();
        if (email == null || email.trim().isEmpty()) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "Email is required"));
        }
        
        String cleanEmail = email.trim();
        String otp = String.format("%06d", new java.util.Random().nextInt(999999));
        LocalDateTime expiry = LocalDateTime.now().plusMinutes(15);
        boolean userFound = false;

        java.util.Optional<AppUser> userOpt = userRepository.findByEmailIgnoreCase(cleanEmail);
        java.util.Optional<Company> companyOpt = companyRepository.findByEmailIgnoreCase(cleanEmail);
        java.util.Optional<Admin> adminOpt = adminRepository.findByEmail(cleanEmail);

        if (userOpt.isPresent()) {
            AppUser user = userOpt.get();
            user.setResetOtp(otp);
            user.setResetOtpExpiry(expiry);
            userRepository.save(user);
            userFound = true;
        } else if (companyOpt.isPresent()) {
            Company company = companyOpt.get();
            company.setResetOtp(otp);
            company.setResetOtpExpiry(expiry);
            companyRepository.save(company);
            userFound = true;
        } else if (adminOpt.isPresent()) {
            Admin admin = adminOpt.get();
            admin.setResetOtp(otp);
            admin.setResetOtpExpiry(expiry);
            adminRepository.save(admin);
            userFound = true;
        }

        if (!userFound) {
            // For security, always return success so we don't leak which emails exist
            return ResponseEntity.ok(java.util.Map.of("message", "If your email is registered, you will receive a reset code shortly."));
        }
        
        try {
            emailService.sendResetPasswordEmail(cleanEmail, otp);
            return ResponseEntity.ok(java.util.Map.of("message", "If your email is registered, you will receive a reset code shortly."));
        } catch (Exception e) {
            logger.error("Failed to send reset email to " + email, e);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(java.util.Map.of("error", "Failed to send reset email"));
        }
    }

    @PostMapping("/reset-password")
    public ResponseEntity<?> resetPassword(@RequestBody com.example.GinumApps.dto.ResetPasswordRequest request) {
        String email = request.getEmail();
        String otp = request.getOtp();
        String newPassword = request.getNewPassword();

        if (email == null || otp == null || newPassword == null) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "All fields are required"));
        }
        
        String cleanEmail = email.trim();

        java.util.Optional<AppUser> userOpt = userRepository.findByEmailIgnoreCase(cleanEmail);
        java.util.Optional<Company> companyOpt = companyRepository.findByEmailIgnoreCase(cleanEmail);
        java.util.Optional<Admin> adminOpt = adminRepository.findByEmail(cleanEmail);

        if (userOpt.isPresent()) {
            AppUser user = userOpt.get();
            if (isValidOtp(user.getResetOtp(), user.getResetOtpExpiry(), otp)) {
                user.setPassword(passwordEncoder.encode(newPassword));
                user.setResetOtp(null);
                user.setResetOtpExpiry(null);
                user.setFailedAttempts(0);
                user.setLockTime(null);
                userRepository.save(user);
                return ResponseEntity.ok(java.util.Map.of("message", "Password has been successfully reset."));
            }
        } else if (companyOpt.isPresent()) {
            Company company = companyOpt.get();
            if (isValidOtp(company.getResetOtp(), company.getResetOtpExpiry(), otp)) {
                company.setPassword(passwordEncoder.encode(newPassword));
                company.setResetOtp(null);
                company.setResetOtpExpiry(null);
                companyRepository.save(company);
                return ResponseEntity.ok(java.util.Map.of("message", "Password has been successfully reset."));
            }
        } else if (adminOpt.isPresent()) {
            Admin admin = adminOpt.get();
            if (isValidOtp(admin.getResetOtp(), admin.getResetOtpExpiry(), otp)) {
                admin.setPassword(passwordEncoder.encode(newPassword));
                admin.setResetOtp(null);
                admin.setResetOtpExpiry(null);
                adminRepository.save(admin);
                return ResponseEntity.ok(java.util.Map.of("message", "Password has been successfully reset."));
            }
        }

        return ResponseEntity.badRequest().body(java.util.Map.of("error", "Invalid or expired reset code."));
    }

    private boolean isValidOtp(String storedOtp, LocalDateTime expiry, String requestOtp) {
        if (storedOtp == null || !storedOtp.equals(requestOtp)) return false;
        if (expiry == null || expiry.isBefore(LocalDateTime.now())) return false;
        return true;
    }

    private LoginResponse buildLoginResponse(String email, String role, String token) {
        LoginResponse response = new LoginResponse();
        response.setToken(token);
        response.setRole(role);
        response.setEmail(email);

        java.util.Optional<AppUser> appUserOpt = userRepository.findByEmailIgnoreCase(email);
        if (appUserOpt.isPresent()) {
            AppUser appUser = appUserOpt.get();
            response.setUserId(appUser.getId());
            Long compId = null;
            if (appUser.getCompany() != null) {
                compId = appUser.getCompany().getCompanyId().longValue();
                response.setCompanyId(appUser.getCompany().getCompanyId());
                response.setCompanyName(appUser.getCompany().getCompanyName());
            }

            // Assign permissions
            if ("ROLE_COMPANY".equals(role) || "ROLE_SUPER_ADMIN".equals(role)) {
                response.setPermissions(java.util.List.of("*"));
            } else if (compId != null) {
                String cleanRoleName = role.replace("ROLE_", "");
                java.util.Optional<com.example.GinumApps.model.Role> customRoleOpt = roleService.findByCompanyAndName(compId, cleanRoleName);
                if (customRoleOpt.isPresent() && customRoleOpt.get().getPermissions() != null && !customRoleOpt.get().getPermissions().isEmpty()) {
                    String[] permsArray = customRoleOpt.get().getPermissions().split(",");
                    response.setPermissions(java.util.Arrays.asList(permsArray));
                } else {
                    response.setPermissions(java.util.List.of());
                }
            } else {
                response.setPermissions(java.util.List.of());
            }

            return response;
        }

        java.util.Optional<Company> companyOpt = companyRepository.findByEmailIgnoreCase(email);
        if (companyOpt.isPresent()) {
            Company company = companyOpt.get();
            response.setCompanyId(company.getCompanyId());
            response.setCompanyName(company.getCompanyName());
            response.setPermissions(java.util.List.of("*"));
            return response;
        }

        java.util.Optional<Admin> adminOpt = adminRepository.findByEmail(email);
        if (adminOpt.isPresent()) {
            Admin admin = adminOpt.get();
            response.setUserId(admin.getId().intValue());
            response.setPermissions(java.util.List.of("*"));
            return response;
        }

        response.setPermissions(java.util.List.of());
        return response;
    }

    @GetMapping("/mfa/setup")
    public ResponseEntity<?> setupMfa(Authentication authentication) {
        try {
            String email = authentication.getName();
            String secret = mfaService.generateSecretKey();
            
            // Save secret temporarily (or directly to DB, but not enabled yet)
            java.util.Optional<AppUser> userOpt = userRepository.findByEmail(email);
            if (userOpt.isPresent()) {
                AppUser user = userOpt.get();
                user.setMfaSecret(secret);
                userRepository.save(user);
            } else {
                java.util.Optional<Company> companyOpt = companyRepository.findByEmail(email);
                if (companyOpt.isPresent()) {
                    Company company = companyOpt.get();
                    company.setMfaSecret(secret);
                    companyRepository.save(company);
                }
            }

            String qrCodeUri = mfaService.getQrCodeImageUri(secret, email);
            return ResponseEntity.ok(java.util.Map.of("qrCodeUri", qrCodeUri));
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(java.util.Map.of("error", e.getMessage()));
        }
    }

    @PostMapping("/mfa/enable")
    public ResponseEntity<?> enableMfa(@RequestBody java.util.Map<String, String> body, Authentication authentication) {
        String email = authentication.getName();
        String code = body.get("code");
        
        String secret = null;
        java.util.Optional<AppUser> userOpt = userRepository.findByEmail(email);
        if (userOpt.isPresent()) {
            secret = userOpt.get().getMfaSecret();
        } else {
            java.util.Optional<Company> companyOpt = companyRepository.findByEmail(email);
            if (companyOpt.isPresent()) {
                secret = companyOpt.get().getMfaSecret();
            }
        }

        if (secret == null) {
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(java.util.Map.of("error", "MFA Setup not initiated"));
        }

        if (mfaService.verifyTotp(code, secret)) {
            if (userOpt.isPresent()) {
                AppUser user = userOpt.get();
                user.setMfaEnabled(true);
                userRepository.save(user);
            } else {
                java.util.Optional<Company> companyOpt = companyRepository.findByEmail(email);
                if (companyOpt.isPresent()) {
                    Company company = companyOpt.get();
                    company.setMfaEnabled(true);
                    companyRepository.save(company);
                }
            }
            return ResponseEntity.ok(java.util.Map.of("message", "MFA Enabled successfully"));
        } else {
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(java.util.Map.of("error", "Invalid MFA Code"));
        }
    }

    @GetMapping("/verify-email")
    public ResponseEntity<?> verifyEmail(@RequestParam("token") String token) {
        if (token == null || token.trim().isEmpty()) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "Verification token is required"));
        }

        java.util.Optional<Company> companyOpt = companyRepository.findByVerificationToken(token);
        if (companyOpt.isEmpty()) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "Invalid or expired verification token"));
        }

        Company company = companyOpt.get();
        company.setEmailVerified(true);
        company.setVerificationToken(null);
        companyRepository.save(company);

        logger.info("Email verified successfully for company: {}", company.getEmail());

        return ResponseEntity.ok(java.util.Map.of("message", "Email verified successfully! You may now log in to your account."));
    }

    @PostMapping("/resend-verification")
    public ResponseEntity<?> resendVerification(@RequestBody java.util.Map<String, String> body) {
        String email = body.get("email");
        if (email == null || email.trim().isEmpty()) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "Email is required"));
        }

        java.util.Optional<Company> companyOpt = companyRepository.findByEmail(email);
        if (companyOpt.isEmpty()) {
            // For security, return success even if email not found
            return ResponseEntity.ok(java.util.Map.of("message", "If an account exists with this email, a verification link has been sent."));
        }

        Company company = companyOpt.get();
        if (company.isEmailVerified()) {
            return ResponseEntity.badRequest().body(java.util.Map.of("error", "Email is already verified. You can log in directly."));
        }

        String newToken = java.util.UUID.randomUUID().toString();
        company.setVerificationToken(newToken);
        companyRepository.save(company);

        try {
            emailService.sendVerificationEmail(company.getEmail(), company.getCompanyName(), newToken);
            return ResponseEntity.ok(java.util.Map.of("message", "Verification email sent! Please check your inbox."));
        } catch (Exception e) {
            logger.error("Failed to resend verification email to " + email, e);
            String errMsg = e.getMessage() != null ? e.getMessage() : "Failed to send email";
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(java.util.Map.of("error", errMsg));
        }
    }
}