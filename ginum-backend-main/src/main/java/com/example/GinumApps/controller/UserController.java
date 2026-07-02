package com.example.GinumApps.controller;

import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.service.AppUserService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/users")
@CrossOrigin(origins = "*")
@RequiredArgsConstructor
public class UserController {

    private final AppUserService appUserService;

    @PostMapping("/companies/{companyId}")
    public ResponseEntity<AppUser> assignUser(
            @PathVariable Integer companyId,
            @RequestBody AppUser user
    ) {
        AppUser savedUser = appUserService.assignUser(companyId, user);
        return ResponseEntity.ok(savedUser);
    }

    @GetMapping("/companies/{companyId}")
    public ResponseEntity<List<AppUser>> getAllUsers(
            @PathVariable Integer companyId
    ) {
        List<AppUser> users = appUserService.getUsersByCompany(companyId);
        return ResponseEntity.ok(users);
    }

    @DeleteMapping("/{userId}")
    public ResponseEntity<Void> deleteUser(
            @PathVariable Integer userId
    ) {
        appUserService.deleteUser(userId);
        return ResponseEntity.noContent().build();
    }
}