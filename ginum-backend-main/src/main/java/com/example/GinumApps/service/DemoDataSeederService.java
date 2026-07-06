package com.example.GinumApps.service;

import com.example.GinumApps.dto.*;
import com.example.GinumApps.enums.*;
import com.example.GinumApps.model.*;
import com.example.GinumApps.repository.*;
import jakarta.transaction.Transactional;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.*;

@Service
@RequiredArgsConstructor
public class DemoDataSeederService {

    private final CompanyRepository companyRepository;
    private final AppUserRepository appUserRepository;
    private final AppUserService appUserService;
    private final AccountService accountService;
    private final AccountRepository accountRepository;
    private final JournalEntryService journalEntryService;
    private final SupplierService supplierService;
    private final CustomerService customerService;
    private final ItemService itemService;
    private final PurchaseOrderService purchaseOrderService;
    private final SalesOrderService salesOrderService;
    private final TransactionService transactionService;

    // Removed @Transactional to prevent one big transaction failure
    public Map<String, Object> seedCompanyData(Integer companyId) throws Exception {
        Map<String, Object> summary = new LinkedHashMap<>();

        Company company = companyRepository.findById(companyId)
                .orElseThrow(() -> new RuntimeException("Company not found"));

        summary.put("userStatus", seedUser(company));
        summary.put("accountsStatus", seedAccounts(company));
        summary.put("journalEntriesStatus", seedJournalEntries(company));
        summary.put("suppliersStatus", seedSuppliers(company));
        summary.put("customersStatus", seedCustomers(company));
        summary.put("itemsStatus", seedItems(company));
        summary.put("purchaseOrdersStatus", seedPurchaseOrders(company));
        summary.put("salesOrdersStatus", seedSalesOrders(company));
        summary.put("paymentsStatus", seedPayments(company));
        summary.put("receiptsStatus", seedReceipts(company));

        summary.put("message", "Demo data seeded successfully.");
        return summary;
    }

    private String seedUser(Company company) {
        String email = "madam.demo@ginuma.com";
        if (appUserRepository.findByEmail(email).isPresent()) {
            return "userAlreadyExists";
        }
        AppUserRequestDto userDto = new AppUserRequestDto();
        userDto.setEmail(email);
        userDto.setPassword("Demo@2026");
        userDto.setRole("ROLE_ADMIN");
        try {
            appUserService.createUser(company.getCompanyId(), userDto);
            return "userCreated";
        } catch (Exception e) {
            return "error: " + e.getMessage();
        }
    }

    private String seedAccounts(Company company) {
        int count = 0;
        count += createAccountIfNotExists(company, "Cash in Hand", AccountType.ASSET_BANK);
        count += createAccountIfNotExists(company, "Bank Account", AccountType.ASSET_BANK);
        count += createAccountIfNotExists(company, "Accounts Receivable", AccountType.ASSET_ACCOUNT_RECEIVABLE);
        count += createAccountIfNotExists(company, "Raw Material Inventory", AccountType.ASSET_OTHER_CURRENT_ASSET);
        count += createAccountIfNotExists(company, "Finished Goods Inventory", AccountType.ASSET_OTHER_CURRENT_ASSET);
        count += createAccountIfNotExists(company, "Land", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Factory Building", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Machinery", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Furniture & Equipment", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Accumulated Depreciation - Building", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Accumulated Depreciation - Machinery", AccountType.ASSET_FIXED_ASSET);
        count += createAccountIfNotExists(company, "Accumulated Depreciation - Furniture", AccountType.ASSET_FIXED_ASSET);

        count += createAccountIfNotExists(company, "Accounts Payable", AccountType.LIABILITY_ACCOUNTS_PAYABLE);
        count += createAccountIfNotExists(company, "Bank Loan", AccountType.LIABILITY_LONG_TERM_LIABILITY);
        count += createAccountIfNotExists(company, "VAT Payable", AccountType.LIABILITY_OTHER_CURRENT_LIABILITY);

        count += createAccountIfNotExists(company, "Share Capital", AccountType.EQUITY);
        count += createAccountIfNotExists(company, "Retained Earnings", AccountType.EQUITY);

        count += createAccountIfNotExists(company, "Sales Revenue", AccountType.INCOME);

        count += createAccountIfNotExists(company, "Cost of Goods Sold", AccountType.COST_OF_SALES);
        count += createAccountIfNotExists(company, "Administrative Expenses", AccountType.EXPENSE);
        count += createAccountIfNotExists(company, "Selling Expenses", AccountType.EXPENSE);
        count += createAccountIfNotExists(company, "Salary Expense", AccountType.EXPENSE);
        count += createAccountIfNotExists(company, "Depreciation Expense", AccountType.EXPENSE);
        count += createAccountIfNotExists(company, "Interest Expense", AccountType.EXPENSE);

        return "accountsCreated: " + count;
    }

    private int createAccountIfNotExists(Company company, String name, AccountType type) {
        String normalized = name.replaceAll("\\s+", "").toUpperCase();
        if (accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream().anyMatch(a -> a.getNormalizedName().equals(normalized) && (a.getNormalizedSubAccount() == null || a.getNormalizedSubAccount().isEmpty()))) {
            return 0;
        }
        AccountRequestDto dto = new AccountRequestDto();
        dto.setAccountName(name);
        dto.setAccountType(type);
        dto.setCurrentBalance(BigDecimal.ZERO);
        accountService.createAccount(company.getCompanyId(), dto);
        return 1;
    }

    private String seedJournalEntries(Company company) {
        String refNo = "OB-2026-001";
        try {
            JournalEntryDto dto = new JournalEntryDto();
            dto.setCompanyId(company.getCompanyId());
            dto.setEntryDate(LocalDate.of(2026, 1, 1));
            dto.setReferenceNo(refNo);
            dto.setJournalTitle("Opening Balances");
            dto.setDescription("Opening balances for demo testing");
            dto.setEntryType(JournalEntryType.MANUAL);
            dto.setAuthorId(1); // just a dummy author id

            List<JournalEntryLineDto> lines = new ArrayList<>();
            lines.add(createJELine(company, "Cash in Hand", new BigDecimal("50000"), true));
            lines.add(createJELine(company, "Bank Account", new BigDecimal("2000000"), true));
            lines.add(createJELine(company, "Accounts Receivable", new BigDecimal("1200000"), true));
            lines.add(createJELine(company, "Raw Material Inventory", new BigDecimal("800000"), true));
            lines.add(createJELine(company, "Finished Goods Inventory", new BigDecimal("1500000"), true));
            lines.add(createJELine(company, "Land", new BigDecimal("5000000"), true));
            lines.add(createJELine(company, "Factory Building", new BigDecimal("8000000"), true));
            lines.add(createJELine(company, "Machinery", new BigDecimal("6000000"), true));
            lines.add(createJELine(company, "Furniture & Equipment", new BigDecimal("500000"), true));

            lines.add(createJELine(company, "Accounts Payable", new BigDecimal("1100000"), false));
            lines.add(createJELine(company, "Bank Loan", new BigDecimal("4000000"), false));
            lines.add(createJELine(company, "VAT Payable", new BigDecimal("100000"), false));
            lines.add(createJELine(company, "Share Capital", new BigDecimal("15000000"), false));
            lines.add(createJELine(company, "Retained Earnings", new BigDecimal("2750000"), false));
            lines.add(createJELine(company, "Accumulated Depreciation - Building", new BigDecimal("1000000"), false));
            lines.add(createJELine(company, "Accumulated Depreciation - Machinery", new BigDecimal("1000000"), false));
            lines.add(createJELine(company, "Accumulated Depreciation - Furniture", new BigDecimal("100000"), false));

            dto.setLines(lines);

            journalEntryService.createJournalEntry(dto);
            return "journalEntriesCreated: 1";
        } catch (Exception e) {
            return "journalEntriesSkipped or error: " + e.getMessage();
        }
    }

    private JournalEntryLineDto createJELine(Company company, String accountName, BigDecimal amount, boolean isDebit) {
        String normalized = accountName.replaceAll("\\s+", "").toUpperCase();
        Account account = accountRepository.findByCompany_CompanyId(company.getCompanyId())
                .stream().filter(a -> a.getNormalizedName().equals(normalized) && (a.getNormalizedSubAccount() == null || a.getNormalizedSubAccount().isEmpty()))
                .findFirst().orElseThrow(() -> new RuntimeException("Account not found: " + accountName));
        JournalEntryLineDto line = new JournalEntryLineDto(account.getAccountCode(), amount, isDebit, "Opening balance");
        return line;
    }

    private String seedSuppliers(Company company) {
        int count = 0;
        try {
            List<SupplierSummaryDto> existing = supplierService.getSuppliersByCompanyId(company.getCompanyId());
            count += createSupplierIfNotExists(existing, company, "Main Raw Material Supplier", "supplier@example.com", SupplierType.SUPPLIER, ItemCategory.FURNITURE);
            count += createSupplierIfNotExists(existing, company, "Packaging Material Supplier", "packaging@example.com", SupplierType.SUPPLIER, ItemCategory.ELECTRONICS);
        } catch (Exception e) {
            return "suppliersSkipped or error: " + e.getMessage();
        }
        return "suppliersCreated: " + count;
    }

    private int createSupplierIfNotExists(List<SupplierSummaryDto> existing, Company company, String name, String email, SupplierType type, ItemCategory category) {
        if (existing.stream().anyMatch(s -> s.getSupplierName().equalsIgnoreCase(name))) {
            return 0;
        }
        try {
            SupplierDto dto = new SupplierDto();
            dto.setSupplierName(name);
            dto.setEmail(email);
            dto.setMobileNo("+94770000001");
            dto.setAddress("123 Supplier Street, Colombo");
            dto.setSupplierType(type);
            dto.setTax(TaxType.INCLUSIVE);
            dto.setCurrencyId(company.getCountry().getDefaultCurrency().getId());
            dto.setItemCategory(category);
            dto.setDiscountPercentage(0.0);
            supplierService.createSupplier(dto, company.getCompanyId());
            return 1;
        } catch (Exception e) {
            e.printStackTrace();
            return 0;
        }
    }

    private String seedCustomers(Company company) {
        int count = 0;
        try {
            List<CustomerSummaryDto> existing = customerService.getCustomersByCompanyId(company.getCompanyId());
            count += createCustomerIfNotExists(existing, company, "Main Credit Customer", "customer1@example.com", com.example.GinumApps.enums.CustomerType.CORPORATE);
            count += createCustomerIfNotExists(existing, company, "Cash Customer", "cash@example.com", com.example.GinumApps.enums.CustomerType.INDIVIDUAL);
        } catch (Exception e) {
            return "customersSkipped or error: " + e.getMessage();
        }
        return "customersCreated: " + count;
    }

    private int createCustomerIfNotExists(List<CustomerSummaryDto> existing, Company company, String name, String email, com.example.GinumApps.enums.CustomerType type) {
        if (existing.stream().anyMatch(c -> c.getCustomerName().equalsIgnoreCase(name))) {
            return 0;
        }
        try {
            CustomerDto dto = new CustomerDto();
            dto.setName(name);
            dto.setEmail(email);
            dto.setPhoneNo("+94770000002");
            dto.setDeliveryAddress("456 Customer Ave, Colombo");
            dto.setBillingAddress("456 Customer Ave, Colombo");
            dto.setCustomerType(type);
            dto.setTax(TaxType.INCLUSIVE);
            dto.setCurrencyId(company.getCountry().getDefaultCurrency().getId());
            dto.setDiscountPercentage(0.0);
            dto.setCompanyId(company.getCompanyId());
            customerService.createCustomer(dto);
            return 1;
        } catch (Exception e) {
            e.printStackTrace();
            return 0;
        }
    }

    private String seedItems(Company company) {
        int count = 0;
        try {
            List<ItemDto> existing = itemService.getItemsByCompany(company.getCompanyId());
            count += createItemIfNotExists(existing, company, "Raw Material", "Raw Material", ItemType.RAW_MATERIAL, new BigDecimal("3900"), new BigDecimal("0.01"), new BigDecimal("205"), 10);
            count += createItemIfNotExists(existing, company, "Chair", "Finished Goods", ItemType.SALES_ITEM, new BigDecimal("6500"), new BigDecimal("13000"), new BigDecimal("230"), 5);
        } catch (Exception e) {
            return "itemsSkipped or error: " + e.getMessage();
        }
        return "itemsCreated: " + count;
    }

    private int createItemIfNotExists(List<ItemDto> existing, Company company, String name, String category, ItemType type, BigDecimal purchasePrice, BigDecimal sellingPrice, BigDecimal stock, int reorder) {
        if (existing.stream().anyMatch(i -> i.getName().equalsIgnoreCase(name))) {
            return 0;
        }
        try {
            ItemDto dto = new ItemDto();
            dto.setName(name);
            dto.setCategory(category);
            dto.setItemType(type);
            dto.setPurchasePrice(purchasePrice);
            dto.setUnitPrice(sellingPrice);
            dto.setCurrentStock(stock);
            dto.setReorderLevel(reorder);
            dto.setUnit("PCS");
            dto.setActive(true);
            dto.setDescription(name + " for testing");
            itemService.createItem(company.getCompanyId(), dto);
            return 1;
        } catch (Exception e) {
            e.printStackTrace();
            return 0;
        }
    }

    private String seedPurchaseOrders(Company company) {
        int count = 0;
        try {
            List<PurchaseOrderResponseDto> existing = purchaseOrderService.getPurchaseOrdersByCompany(company.getCompanyId());
            SupplierSummaryDto rawSupplier = supplierService.getSuppliersByCompanyId(company.getCompanyId()).stream()
                    .filter(s -> s.getSupplierName().equals("Main Raw Material Supplier")).findFirst().orElse(null);
            SupplierSummaryDto packSupplier = supplierService.getSuppliersByCompanyId(company.getCompanyId()).stream()
                    .filter(s -> s.getSupplierName().equals("Packaging Material Supplier")).findFirst().orElse(null);
            ItemDto rawItem = itemService.getItemsByCompany(company.getCompanyId()).stream()
                    .filter(i -> i.getName().equals("Raw Material")).findFirst().orElse(null);

            if (rawSupplier != null && rawItem != null) {
                count += createPOIfNotExists(existing, company, rawSupplier.getId(), "SUP-001", "PO-DEMO-1", LocalDate.of(2026, 1, 8), rawItem.getItemId(), 10, new BigDecimal("120000"));
                count += createPOIfNotExists(existing, company, rawSupplier.getId(), "SUP-002", "PO-DEMO-2", LocalDate.of(2026, 1, 15), rawItem.getItemId(), 3, new BigDecimal("100000"));
            }
            if (packSupplier != null && rawItem != null) {
                count += createPOIfNotExists(existing, company, packSupplier.getId(), "SUP-003", "PO-DEMO-3", LocalDate.of(2026, 1, 23), rawItem.getItemId(), 2, new BigDecimal("100000"));
            }
        } catch (Exception e) {
            return "purchaseOrdersSkipped or error: " + e.getMessage();
        }
        return "purchaseOrdersCreated: " + count;
    }

    private int createPOIfNotExists(List<PurchaseOrderResponseDto> existing, Company company, Long supplierId, String supInvNo, String poNo, LocalDate date, Long itemId, int qty, BigDecimal unitPrice) {
        if (existing.stream().anyMatch(po -> poNo.equals(po.getPurchaseOrderNumber()))) {
            return 0;
        }
        try {
            PurchaseOrderRequestDto dto = new PurchaseOrderRequestDto();
            dto.setSupplierId(supplierId);
            dto.setSupplierInvoiceNumber(supInvNo);
            dto.setPoNumber(poNo);
            dto.setIssueDate(date);
            dto.setDueDate(date.plusDays(30));

            PurchaseOrderItemRequestDto itemDto = new PurchaseOrderItemRequestDto();
            itemDto.setItemId(itemId);
            itemDto.setQuantity(qty);
            itemDto.setUnitPrice(unitPrice);
            itemDto.setDiscount(BigDecimal.ZERO);
            itemDto.setAmount(unitPrice.multiply(BigDecimal.valueOf(qty)));
            
            Account account = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                .filter(a -> a.getNormalizedName().equals("RAWMATERIALINVENTORY"))
                .findFirst().orElse(null);
            if (account == null) {
                System.err.println("Purchase Order Seeding failed: RAWMATERIALINVENTORY account is missing for company " + company.getCompanyId());
                return 0;
            }
            itemDto.setAccountCode(account.getAccountCode());

            dto.setItems(List.of(itemDto));
            purchaseOrderService.createPurchaseOrder(dto, company.getCompanyId());
            return 1;
        } catch (Exception e) {
            e.printStackTrace();
            return 0;
        }
    }

    private String seedSalesOrders(Company company) {
        int count = 0;
        try {
            List<SalesOrderResponseDto> existing = salesOrderService.getSalesOrdersByCompany(company.getCompanyId());
            CustomerSummaryDto crCustomer = customerService.getCustomersByCompanyId(company.getCompanyId()).stream()
                    .filter(c -> c.getCustomerName().equals("Main Credit Customer")).findFirst().orElse(null);
            CustomerSummaryDto cashCustomer = customerService.getCustomersByCompanyId(company.getCompanyId()).stream()
                    .filter(c -> c.getCustomerName().equals("Cash Customer")).findFirst().orElse(null);
            ItemDto chair = itemService.getItemsByCompany(company.getCompanyId()).stream()
                    .filter(i -> i.getName().equals("Chair")).findFirst().orElse(null);

            if (crCustomer != null && chair != null) {
                count += createSOIfNotExists(existing, company, crCustomer.getCustomerId(), "SO-DEMO-1", LocalDate.of(2026, 1, 13), chair.getItemId(), 115, new BigDecimal("13043.48"));
                count += createSOIfNotExists(existing, company, crCustomer.getCustomerId(), "SO-DEMO-3", LocalDate.of(2026, 1, 30), chair.getItemId(), 153, new BigDecimal("13071.90"));
            }
            if (cashCustomer != null && chair != null) {
                count += createSOIfNotExists(existing, company, cashCustomer.getCustomerId(), "SO-DEMO-2", LocalDate.of(2026, 1, 21), chair.getItemId(), 61, new BigDecimal("13114.75"));
            }
        } catch (Exception e) {
            return "salesOrdersSkipped or error: " + e.getMessage();
        }
        return "salesOrdersCreated: " + count;
    }

    private int createSOIfNotExists(List<SalesOrderResponseDto> existing, Company company, Long customerId, String soNo, LocalDate date, Long itemId, int qty, BigDecimal unitPrice) {
        if (existing.stream().anyMatch(so -> soNo.equals(so.getSoNumber()))) {
            return 0;
        }
        try {
            SalesOrderRequestDto dto = new SalesOrderRequestDto();
            dto.setCustomerId(customerId);
            dto.setSoNumber(soNo);
            dto.setIssueDate(date);
            
            SalesOrderItemRequestDto itemDto = new SalesOrderItemRequestDto();
            itemDto.setItemId(itemId);
            itemDto.setQuantity(qty);
            itemDto.setUnitPrice(unitPrice);
            itemDto.setDiscountPercent(BigDecimal.ZERO);
            itemDto.setDescription("Chair for testing");
            Account account = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                .filter(a -> a.getNormalizedName().equals("SALESREVENUE"))
                .findFirst().orElse(null);
            if (account == null) {
                System.err.println("Sales Order Seeding failed: SALESREVENUE account is missing for company " + company.getCompanyId());
                return 0;
            }
            itemDto.setAccountCode(account.getAccountCode());

            dto.setItems(List.of(itemDto));
            salesOrderService.createSalesOrder(dto, company.getCompanyId());
            return 1;
        } catch (Exception e) {
            e.printStackTrace();
            return 0;
        }
    }

    private String seedPayments(Company company) {
        int count = 0;
        try {
            Account bank = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                .filter(a -> a.getNormalizedName().equals("BANKACCOUNT")).findFirst().orElse(null);
            
            if (bank != null) {
                List<PurchaseOrderResponseDto> pos = purchaseOrderService.getPurchaseOrdersByCompany(company.getCompanyId());
                for (PurchaseOrderResponseDto po : pos) {
                    if ("PO-DEMO-1".equals(po.getPurchaseOrderNumber()) && po.getAmountPaid().compareTo(BigDecimal.ZERO) == 0) {
                        PurchasePaymentRequestDto req = new PurchasePaymentRequestDto();
                        req.setAmount(new BigDecimal("800000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setCompanyId(company.getCompanyId());
                        req.setPaymentNote("Advance payment");
                        purchaseOrderService.payPurchaseOrder(po.getId(), req);
                        count++;
                    }
                    if ("PO-DEMO-2".equals(po.getPurchaseOrderNumber()) && po.getAmountPaid().compareTo(BigDecimal.ZERO) == 0) {
                        PurchasePaymentRequestDto req = new PurchasePaymentRequestDto();
                        req.setAmount(new BigDecimal("100000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setCompanyId(company.getCompanyId());
                        req.setPaymentNote("Full payment");
                        purchaseOrderService.payPurchaseOrder(po.getId(), req);
                        count++;
                    }
                }

                Account salaryExp = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                    .filter(a -> a.getNormalizedName().equals("SALARYEXPENSE")).findFirst().orElse(null);
                Account adminExp = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                    .filter(a -> a.getNormalizedName().equals("ADMINISTRATIVEEXPENSES")).findFirst().orElse(null);

                if (transactionService.getAllTransactions(company.getCompanyId()).stream().noneMatch(t -> "EXP-001".equals(t.getReferenceNumber()))) {
                    if (salaryExp != null) {
                        DirectPaymentRequestDto req = new DirectPaymentRequestDto();
                        req.setPayeeId(1);
                        req.setPayeeType("OTHER");
                        req.setAmount(new BigDecimal("900000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setExpenseAccountCode(salaryExp.getAccountCode());
                        req.setPaymentCategory("Salary Expense");
                        req.setPaymentMethod("Bank Transfer");
                        req.setPaymentNote("Factory wages Jan");
                        req.setReferenceNumber("EXP-001");
                        transactionService.processDirectPayment(company.getCompanyId(), req);
                        count++;
                    }
                    if (adminExp != null) {
                        DirectPaymentRequestDto req = new DirectPaymentRequestDto();
                        req.setPayeeId(1);
                        req.setPayeeType("OTHER");
                        req.setAmount(new BigDecimal("150000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setExpenseAccountCode(adminExp.getAccountCode());
                        req.setPaymentCategory("Other");
                        req.setPaymentMethod("Bank Transfer");
                        req.setPaymentNote("Electricity bill");
                        req.setReferenceNumber("EXP-002");
                        transactionService.processDirectPayment(company.getCompanyId(), req);
                        count++;
                    }
                }
            }
        } catch (Exception e) {
            return "paymentsSkipped or error: " + e.getMessage();
        }
        return "paymentsCreated: " + count;
    }

    private String seedReceipts(Company company) {
        int count = 0;
        try {
            Account bank = accountRepository.findByCompany_CompanyId(company.getCompanyId()).stream()
                .filter(a -> a.getNormalizedName().equals("BANKACCOUNT")).findFirst().orElse(null);
            
            if (bank != null) {
                List<SalesOrderResponseDto> sos = salesOrderService.getSalesOrdersByCompany(company.getCompanyId());
                for (SalesOrderResponseDto so : sos) {
                    if ("SO-DEMO-1".equals(so.getSoNumber()) && so.getAmountPaid().compareTo(BigDecimal.ZERO) == 0) {
                        SalesPaymentRequestDto req = new SalesPaymentRequestDto();
                        req.setAmount(new BigDecimal("1500000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setCompanyId(company.getCompanyId());
                        salesOrderService.paySalesOrder(so.getId(), req);
                        count++;
                    }
                    if ("SO-DEMO-2".equals(so.getSoNumber()) && so.getAmountPaid().compareTo(BigDecimal.ZERO) == 0) {
                        SalesPaymentRequestDto req = new SalesPaymentRequestDto();
                        req.setAmount(new BigDecimal("800000"));
                        req.setPaymentAccountCode(bank.getAccountCode());
                        req.setCompanyId(company.getCompanyId());
                        salesOrderService.paySalesOrder(so.getId(), req);
                        count++;
                    }
                }
            }
        } catch (Exception e) {
            return "receiptsSkipped or error: " + e.getMessage();
        }
        return "receiptsCreated: " + count;
    }
}
