package com.example.GinumApps.controller;

import com.example.GinumApps.dto.AppUserRequestDto;
import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.service.AppUserService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/users")
@RequiredArgsConstructor
public class AppUserController {

    private final AppUserService appUserService;

    @PostMapping("/companies/{companyId}")
    public ResponseEntity<?> createUser(@PathVariable Integer companyId,
                                        @Valid @RequestBody AppUserRequestDto request) {
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
    public ResponseEntity<List<AppUser>> getUsersByCompany(@PathVariable Integer companyId) {
        return ResponseEntity.ok(appUserService.getUsersByCompany(companyId));
    }

    @DeleteMapping("/{userId}")
    public ResponseEntity<?> deleteUser(@PathVariable Integer userId) {
        appUserService.deleteUser(userId);
        return ResponseEntity.ok(Map.of("message", "User deleted successfully"));
    }
}

