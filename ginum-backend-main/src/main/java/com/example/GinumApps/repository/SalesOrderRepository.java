package com.example.GinumApps.repository;

import com.example.GinumApps.model.SalesOrder;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface SalesOrderRepository extends JpaRepository<SalesOrder, Long> {
    // You can add custom query methods here
}