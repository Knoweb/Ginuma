package com.example.GinumApps.controller;

import com.example.GinumApps.dto.AppUserRequestDto;
import com.example.GinumApps.dto.ChangePasswordRequest;
import com.example.GinumApps.dto.UserProfileDto;
import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.service.AppUserService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/users")
@CrossOrigin(origins = "*")
@RequiredArgsConstructor
public class UserController {

    private final AppUserService appUserService;

    @PostMapping("/companies/{companyId}")
    public ResponseEntity<?> createUser(
            @PathVariable Integer companyId,
            @Valid @RequestBody AppUserRequestDto request
    ) {
        try {
            AppUser createdUser = appUserService.createUser(companyId, request);
            return ResponseEntity.status(HttpStatus.CREATED).body(createdUser);
        } catch (RuntimeException e) {
            if (e.getMessage() != null && e.getMessage().contains("already registered")) {
                try {
                    AppUser userToAssign = new AppUser();
                    userToAssign.setEmail(request.getEmail());
                    userToAssign.setPassword(request.getPassword());
                    userToAssign.setRole(request.getRole());
                    AppUser updatedUser = appUserService.assignUser(companyId, userToAssign);
                    return ResponseEntity.ok(updatedUser);
                } catch (Exception ex) {
                    return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(Map.of("error", ex.getMessage()));
                }
            }
            return ResponseEntity
                    .status(HttpStatus.BAD_REQUEST)
                    .body(Map.of("error", e.getMessage()));
        }
    }

    @GetMapping("/companies/{companyId}")
    public ResponseEntity<List<AppUser>> getAllUsers(
            @PathVariable Integer companyId
    ) {
        List<AppUser> users = appUserService.getUsersByCompany(companyId);
        return ResponseEntity.ok(users);
    }

    @DeleteMapping("/{userId}")
    public ResponseEntity<?> deleteUser(
            @PathVariable Integer userId
    ) {
        appUserService.deleteUser(userId);
        return ResponseEntity.ok(Map.of("message", "User deleted successfully"));
    }

    @GetMapping("/profile")
    public ResponseEntity<UserProfileDto> getProfile(Authentication authentication) {
        UserProfileDto profile = appUserService.getUserProfile(authentication.getName());
        return ResponseEntity.ok(profile);
    }

    @PutMapping("/profile")
    public ResponseEntity<UserProfileDto> updateProfile(
            Authentication authentication,
            @RequestBody UserProfileDto profileDto
    ) {
        UserProfileDto updated = appUserService.updateUserProfile(authentication.getName(), profileDto);
        return ResponseEntity.ok(updated);
    }

    @PostMapping("/profile/change-password")
    public ResponseEntity<String> changePassword(
            Authentication authentication,
            @RequestBody ChangePasswordRequest passwordRequest
    ) {
        try {
            appUserService.changePassword(authentication.getName(), passwordRequest);
            return ResponseEntity.ok("{\"message\": \"Password changed successfully!\"}");
        } catch (IllegalArgumentException ex) {
            return ResponseEntity.badRequest().body("{\"message\": \"" + ex.getMessage() + "\"}");
        }
    }
}