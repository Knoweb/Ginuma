package com.example.GinumApps.model;

import jakarta.persistence.*;
import lombok.Data;

@Data
@Entity
@Table(name = "transactions")
public class Transaction {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Integer id;

    private String referenceNumber;
    private String date;
    private String description;
    private double totalDebit;
    private double totalCredit;

    @ManyToOne
    @JoinColumn(name = "company_id")
    private Company company;
}