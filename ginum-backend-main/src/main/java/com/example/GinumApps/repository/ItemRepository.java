package com.example.GinumApps.repository;

import com.example.GinumApps.model.Item;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.Optional;

public interface ItemRepository extends JpaRepository<Item, Long> {

    List<Item> findByCompany_CompanyId(Integer companyId);

    Optional<Item> findByItemIdAndCompany_CompanyId(Long itemId, Integer companyId);

    boolean existsByItemCodeIgnoreCaseAndCompany_CompanyId(String itemCode, Integer companyId);

    boolean existsByItemCodeIgnoreCaseAndCompany_CompanyIdAndItemIdNot(
            String itemCode,
            Integer companyId,
            Long itemId
    );
}