package com.example.GinumApps.service;

import com.example.GinumApps.model.Transaction;
import com.example.GinumApps.dto.TransactionDto;
import com.example.GinumApps.repository.TransactionRepository;
import org.springframework.stereotype.Service;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import java.util.List;


import com.example.GinumApps.model.Company;
import com.example.GinumApps.repository.CompanyRepository;
import com.example.GinumApps.exception.ResourceNotFoundException;

@Service
public class TransactionService {

    @Autowired
    private TransactionRepository transactionRepository;

    @Autowired
    private CompanyRepository companyRepository;

    public List<Transaction> getAllTransactions(Integer companyId) {
        return transactionRepository.findByCompanyId(companyId);
    }

    public Transaction saveTransaction(Integer companyId, TransactionDto dto) {
        Company company = companyRepository.findById(companyId)
                .orElseThrow(() -> new ResourceNotFoundException("Company not found"));

        Transaction transaction = new Transaction();
        transaction.setReferenceNumber(dto.getReferenceNumber());
        transaction.setDate(dto.getDate());
        transaction.setDescription(dto.getDescription());
        transaction.setTotalDebit(dto.getTotalDebit());
        transaction.setTotalCredit(dto.getTotalCredit());
        transaction.setCompany(company);

        return transactionRepository.save(transaction);
    }
}
