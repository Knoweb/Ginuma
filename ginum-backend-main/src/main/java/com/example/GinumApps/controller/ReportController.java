package com.example.GinumApps.controller;

import com.example.GinumApps.dto.BalanceSheetResponseDto;
import com.example.GinumApps.service.ReportService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/companies/{companyId}/reports")
@RequiredArgsConstructor
@CrossOrigin(origins = "*")
public class ReportController {

    private final ReportService reportService;

    @GetMapping("/balance-sheet")
    public ResponseEntity<BalanceSheetResponseDto> getBalanceSheet(@PathVariable Integer companyId) {
        BalanceSheetResponseDto report = reportService.getBalanceSheet(companyId);
        return ResponseEntity.ok(report);
    }
}
