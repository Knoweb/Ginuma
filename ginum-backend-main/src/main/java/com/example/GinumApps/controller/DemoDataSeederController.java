package com.example.GinumApps.controller;

import com.example.GinumApps.service.DemoDataSeederService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/api/demo")
@RequiredArgsConstructor
public class DemoDataSeederController {

    private final DemoDataSeederService demoDataSeederService;

    @Value("${DEMO_SEED_TOKEN:change-me-demo-seed-token}")
    private String requiredToken;

    @PostMapping("/seed/company/{companyId}")
    public ResponseEntity<?> seedDemoData(
            @PathVariable Integer companyId,
            @RequestHeader(value = "X-DEMO-SEED-TOKEN", required = false) String providedToken) {

        if (providedToken == null || !providedToken.equals(requiredToken)) {
            return ResponseEntity.status(HttpStatus.FORBIDDEN)
                    .body(Map.of("error", "Invalid demo seed token."));
        }

        try {
            Map<String, Object> result = demoDataSeederService.seedCompanyData(companyId);
            return ResponseEntity.ok(result);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(Map.of("error", "Error seeding demo data", "details", e.getMessage()));
        }
    }

    @PostMapping("/reconcile/company/{companyId}")
    public ResponseEntity<?> reconcileDemoData(
            @PathVariable Integer companyId,
            @RequestHeader(value = "X-DEMO-SEED-TOKEN", required = false) String token) {
        
        if (token == null || !token.equals(requiredToken)) {
            return ResponseEntity.status(HttpStatus.FORBIDDEN)
                    .body(Map.of("error", "Invalid demo seed token."));
        }

        try {
            Map<String, Object> result = demoDataSeederService.reconcileDemoOpeningBalances(companyId);
            return ResponseEntity.ok(result);
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body(Map.of("error", "Error reconciling demo data", "details", e.getMessage()));
        }
    }
}
