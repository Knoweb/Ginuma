package com.example.GinumApps.controller;

import com.example.GinumApps.model.AppUser;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.repository.UserRepository;
import com.example.GinumApps.repository.CompanyRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.Optional;
import java.util.List;

@RestController
@RequestMapping("/api/users")
@CrossOrigin(origins = "*")
public class UserController {

    @Autowired
    private UserRepository userRepository;

    @Autowired
    private CompanyRepository companyRepository;

    @PostMapping("/companies/{companyId}")
    public ResponseEntity<?> assignUser(@PathVariable Integer companyId, @RequestBody AppUser user) {
        // 1. එම Email එකෙන් User කෙනෙක් සිටීදැයි පරීක්ෂා කරන්න
        Optional<AppUser> existingUser = userRepository.findByEmail(user.getEmail());

        if (existingUser.isPresent()) {
            // සිටී නම්: පැරණි User ගේ ID එක අරගෙන ඒ දත්ත Update කරන්න
            AppUser userToUpdate = existingUser.get();
            userToUpdate.setRole(user.getRole());
            // අවධානය: මුරපදය (password) වෙනස් කරන්නේ නම් පමණක් Encrypt කර Update කරන්න
            if (user.getPassword() != null && !user.getPassword().isEmpty()) {
                userToUpdate.setPassword(user.getPassword());
            }
            return ResponseEntity.ok(userRepository.save(userToUpdate));
        } else {
            // සිටින්නේ නැති නම්: අලුතින් Save කරන්න
            Company company = companyRepository.findById(companyId)
                    .orElseThrow(() -> new RuntimeException("Company not found"));
            user.setCompany(company);
            return ResponseEntity.ok(userRepository.save(user));
        }
    }

    @GetMapping("/companies/{companyId}")
    public ResponseEntity<List<AppUser>> getAllUsers(@PathVariable Integer companyId) {
        return ResponseEntity.ok(userRepository.findByCompany_CompanyId(companyId));
    }


}