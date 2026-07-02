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
        Optional<AppUser> existingUser = userRepository.findByEmail(user.getEmail());

        if (existingUser.isPresent()) {
            AppUser userToUpdate = existingUser.get();
            userToUpdate.setRole(user.getRole());
            if (user.getPassword() != null && !user.getPassword().isEmpty()) {
                userToUpdate.setPassword(user.getPassword());
            }
            return ResponseEntity.ok(userRepository.save(userToUpdate));
        } else {
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

    @DeleteMapping("/{userId}")
    public ResponseEntity<Void> deleteUser(@PathVariable Integer userId) {
        userRepository.deleteById(userId);
        return ResponseEntity.ok().build();
    }
}