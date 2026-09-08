package com.example.GinumApps.controller;

import com.example.GinumApps.dto.AuthRequest;
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

    @CrossOrigin
    @PostMapping("/login")
    public ResponseEntity<?> login(@Valid @RequestBody AuthRequest authRequest) {
        try {
            java.util.Optional<AppUser> userOpt = userRepository.findByEmail(authRequest.getEmail());
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
                            authRequest.getEmail(),
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
            } else {
                java.util.Optional<Company> companyOpt = companyRepository.findByEmail(authRequest.getEmail());
                if (companyOpt.isPresent()) {
                    mfaEnabled = companyOpt.get().isMfaEnabled();
                    mfaSecret = companyOpt.get().getMfaSecret();
                }
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

    private LoginResponse buildLoginResponse(String email, String role, String token) {
        LoginResponse response = new LoginResponse();
        response.setToken(token);
        response.setRole(role);
        response.setEmail(email);

        java.util.Optional<AppUser> appUserOpt = userRepository.findByEmail(email);
        if (appUserOpt.isPresent()) {
            AppUser appUser = appUserOpt.get();
            response.setUserId(appUser.getId());
            if (appUser.getCompany() != null) {
                response.setCompanyId(appUser.getCompany().getCompanyId());
                response.setCompanyName(appUser.getCompany().getCompanyName());
            }
            return response;
        }

        java.util.Optional<Company> companyOpt = companyRepository.findByEmail(email);
        if (companyOpt.isPresent()) {
            Company company = companyOpt.get();
            response.setCompanyId(company.getCompanyId());
            response.setCompanyName(company.getCompanyName());
            return response;
        }

        java.util.Optional<Admin> adminOpt = adminRepository.findByEmail(email);
        if (adminOpt.isPresent()) {
            Admin admin = adminOpt.get();
            response.setUserId(admin.getId());
            return response;
        }

        throw new RuntimeException("Missing company mapping for email: " + email);
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
}