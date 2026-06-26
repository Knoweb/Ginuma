package com.example.GinumApps.controller;

import com.example.GinumApps.dto.ProjectRequestDto;
import com.example.GinumApps.dto.ProjectResponseDto;
import com.example.GinumApps.service.ProjectService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/companies/{companyId}/projects")
@RequiredArgsConstructor
public class ProjectController {

    private final ProjectService projectService;

    @PostMapping
    public ResponseEntity<ProjectResponseDto> createProject(
            @PathVariable Integer companyId,
            @Valid @RequestBody ProjectRequestDto request
    ) {
        ProjectResponseDto savedProject = projectService.createProject(companyId, request);
        return ResponseEntity.status(HttpStatus.CREATED).body(savedProject);
    }

    @GetMapping
    public ResponseEntity<List<ProjectResponseDto>> getProjectsByCompany(
            @PathVariable Integer companyId
    ) {
        List<ProjectResponseDto> projects = projectService.getProjectsByCompany(companyId);
        return ResponseEntity.ok(projects);
    }
}