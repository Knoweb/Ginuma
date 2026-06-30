package com.example.GinumApps.controller;

import com.example.GinumApps.dto.ItemDto;
import com.example.GinumApps.dto.StockUpdateDto;
import com.example.GinumApps.service.ItemService;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/companies/{companyId}/items")
@RequiredArgsConstructor
public class ItemController {

    private final ItemService itemService;

    @PostMapping
    public ResponseEntity<ItemDto> createItem(
            @PathVariable Integer companyId,
            @RequestBody @Valid ItemDto itemDto
    ) {
        ItemDto savedItem = itemService.createItem(companyId, itemDto);
        return ResponseEntity.status(HttpStatus.CREATED).body(savedItem);
    }

    @GetMapping
    public ResponseEntity<List<ItemDto>> getItems(
            @PathVariable Integer companyId
    ) {
        return ResponseEntity.ok(itemService.getItemsByCompany(companyId));
    }

    @PutMapping("/{itemId}")
    public ResponseEntity<ItemDto> updateItem(
            @PathVariable Integer companyId,
            @PathVariable Long itemId,
            @RequestBody @Valid ItemDto itemDto
    ) {
        ItemDto updatedItem = itemService.updateItem(companyId, itemId, itemDto);
        return ResponseEntity.ok(updatedItem);
    }

    @DeleteMapping("/{itemId}")
    public ResponseEntity<Void> deleteItem(
            @PathVariable Integer companyId,
            @PathVariable Long itemId
    ) {
        itemService.deleteItem(companyId, itemId);
        return ResponseEntity.noContent().build();
    }

    @PostMapping("/{itemId}/stock/add")
    public ResponseEntity<ItemDto> addStock(
            @PathVariable Integer companyId,
            @PathVariable Long itemId,
            @RequestBody @Valid StockUpdateDto dto
    ) {
        ItemDto updatedItem = itemService.addStock(companyId, itemId, dto);
        return ResponseEntity.ok(updatedItem);
    }

    @PostMapping("/{itemId}/stock/reduce")
    public ResponseEntity<ItemDto> reduceStock(
            @PathVariable Integer companyId,
            @PathVariable Long itemId,
            @RequestBody @Valid StockUpdateDto dto
    ) {
        ItemDto updatedItem = itemService.reduceStock(companyId, itemId, dto);
        return ResponseEntity.ok(updatedItem);
    }
}