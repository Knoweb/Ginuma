package com.example.GinumApps.controller;

import com.example.GinumApps.dto.DesignationDto;
import com.example.GinumApps.model.Department;
import com.example.GinumApps.model.Designation;
import com.example.GinumApps.service.DesignationService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@RequestMapping("/api/{companyId}/designations")
@RequiredArgsConstructor
public class DesignationController {
    private final DesignationService designationService;

    @PostMapping
    public ResponseEntity<Designation> createDesignation(
            @PathVariable Integer companyId,
            @RequestBody DesignationDto designationDto) {
        return ResponseEntity.ok(designationService.createDesignation(companyId, designationDto));
    }

    @GetMapping("/by-department/{departmentCode}")
    public ResponseEntity<List<Designation>> getDesignationsByDepartmentCode(
            @PathVariable Integer companyId,
            @PathVariable String departmentCode) {

        List<Designation> designations = designationService.getDesignationsByDepartmentCode(companyId, departmentCode);
        if (designations == null) {
            return ResponseEntity.notFound().build();
        }
        if (designations.isEmpty()) {
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.ok(designations);
    }

}
