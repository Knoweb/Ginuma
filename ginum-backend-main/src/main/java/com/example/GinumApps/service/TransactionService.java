package com.example.GinumApps.service;

import com.example.GinumApps.model.Transaction;
import com.example.GinumApps.dto.TransactionDto;
import com.example.GinumApps.repository.TransactionRepository;
import org.springframework.stereotype.Service;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;
import java.util.List;


@Service
public class TransactionService {

    @Autowired
    private TransactionRepository transactionRepository;

    public List<Transaction> getAllTransactions(Integer companyId) {
        return transactionRepository.findAll();
    }

    public Transaction saveTransaction(Integer companyId, TransactionDto dto) {
        Transaction transaction = new Transaction();
        transaction.setReferenceNumber(dto.getReferenceNumber());
        transaction.setDate(dto.getDate());
        transaction.setDescription(dto.getDescription());
        transaction.setTotalDebit(dto.getTotalDebit());
        transaction.setTotalCredit(dto.getTotalCredit());


        return transactionRepository.save(transaction);
    }
}
