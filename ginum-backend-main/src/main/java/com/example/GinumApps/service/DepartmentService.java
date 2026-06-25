package com.example.GinumApps.service;


import com.example.GinumApps.dto.DepartmentDto;
import com.example.GinumApps.model.Company;
import com.example.GinumApps.model.Department;
import com.example.GinumApps.repository.CompanyRepository;
import com.example.GinumApps.repository.DepartmentRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;

import java.util.List;


@Service
@RequiredArgsConstructor
public class DepartmentService {

    private final CompanyRepository companyRepository;
    private final DepartmentRepository departmentRepository;

    public Department createDepartment(Integer companyId, DepartmentDto departmentDto) {
        Company company = companyRepository.findById(companyId)
                .orElseThrow(() -> new RuntimeException("Company not found"));

        Department department = new Department();
        department.setName(departmentDto.getName());
        department.setCode(departmentDto.getCode());
        department.setCompany(company);
        return departmentRepository.save(department);
    }

    public List<Department> getDepartmentsByCompanyId(Integer companyId) {
        return departmentRepository.findByCompany_CompanyId(companyId);
    }
}
