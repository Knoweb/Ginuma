// CompanyRegistrationController.java
package com.example.GinumApps.controller;

import com.example.GinumApps.dto.CompanyRegistrationDto;
import com.example.GinumApps.dto.CompanyResponseDto;
import com.example.GinumApps.exception.DuplicateEntityException;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.service.CompanyService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.util.Collections;
import java.util.List;
import java.util.Map;
import java.util.Optional;

@RestController
@RequestMapping("/api/companies")
@RequiredArgsConstructor
public class CompanyRegistrationController {

    private final CompanyService companyService;

    @PostMapping(consumes = MediaType.MULTIPART_FORM_DATA_VALUE)
    public ResponseEntity<Map<String, String>> registerCompany(
            @ModelAttribute CompanyRegistrationDto formDto,
            @RequestPart(value = "company", required = false) CompanyRegistrationDto jsonDto,
            @RequestParam(value = "companyLogo", required = false) MultipartFile companyLogo,
            @RequestParam(value = "brReport", required = false) MultipartFile brReport
    ) throws IOException {

        CompanyRegistrationDto dto = (jsonDto != null && jsonDto.getEmail() != null) ? jsonDto : formDto;

        if (dto.getCompanyName() == null || dto.getCompanyName().trim().isEmpty()) {
            return ResponseEntity.badRequest().body(Map.of("error", "Company Name is required"));
        }
        if (dto.getEmail() == null || dto.getEmail().trim().isEmpty()) {
            return ResponseEntity.badRequest().body(Map.of("error", "Email is required"));
        }
        if (dto.getPassword() == null || dto.getPassword().trim().isEmpty()) {
            return ResponseEntity.badRequest().body(Map.of("error", "Password is required"));
        }
        if (dto.getCountryId() == null) {
            return ResponseEntity.badRequest().body(Map.of("error", "Country is required"));
        }
        if (dto.getCurrencyId() == null) {
            return ResponseEntity.badRequest().body(Map.of("error", "Currency is required"));
        }

        long maxFileSize = 5 * 1024 * 1024; // 5 MB

        if (companyLogo != null && !companyLogo.isEmpty()) {
            if (companyLogo.getSize() > maxFileSize) {
                return ResponseEntity.badRequest().body(Map.of("error", "Company logo exceeds maximum size of 5MB"));
            }
            dto.setCompanyLogo(companyLogo);
        }
        if (brReport != null && !brReport.isEmpty()) {
            if (brReport.getSize() > maxFileSize) {
                return ResponseEntity.badRequest().body(Map.of("error", "BR Report exceeds maximum size of 5MB"));
            }
            dto.setBrReport(brReport);
        }

        try {
            companyService.registerCompany(dto);
        } catch (DuplicateEntityException e) {
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage()));
        } catch (Exception e) {
            org.slf4j.LoggerFactory.getLogger(CompanyRegistrationController.class).error("Registration error", e);
            return ResponseEntity.badRequest().body(Map.of("error", e.getMessage() != null ? e.getMessage() : "Registration failed"));
        }

        return ResponseEntity
                .status(HttpStatus.CREATED)
                .body(Collections.singletonMap("message", "Company created successfully"));
    }

    @GetMapping("/{id}")
    public ResponseEntity<?> getCompanyById(@PathVariable Integer id) {
        Optional<Company> optionalCompany = companyService.getCompanyById(id);
        if (optionalCompany.isEmpty()) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body("Company not found");
        }
        CompanyResponseDto dto = companyService.convertToDto(optionalCompany.get());
        return ResponseEntity.ok(dto);
    }

    @GetMapping("/{id}/brReport")
    public ResponseEntity<?> getBrReport(@PathVariable Integer id) {
        Optional<Company> optionalCompany = companyService.getCompanyById(id);
        if (optionalCompany.isEmpty() || optionalCompany.get().getBrReport() == null) {
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body("BR Report not found");
        }
        byte[] brReport = optionalCompany.get().getBrReport();
        return ResponseEntity.ok()
                .contentType(MediaType.APPLICATION_OCTET_STREAM)
                .header(HttpHeaders.CONTENT_DISPOSITION, "attachment; filename=br_report_" + id + ".pdf")
                .body(brReport);
    }

}