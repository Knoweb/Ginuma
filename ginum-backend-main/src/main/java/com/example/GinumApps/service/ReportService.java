package com.example.GinumApps.service;

import com.example.GinumApps.dto.BalanceSheetResponseDto;
import com.example.GinumApps.dto.BalanceSheetResponseDto.BalanceSheetLineDto;
import com.example.GinumApps.enums.AccountType;
import com.example.GinumApps.model.Account;
import com.example.GinumApps.repository.AccountRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.List;

@Service
@RequiredArgsConstructor
public class ReportService {

    private final AccountRepository accountRepository;

    public BalanceSheetResponseDto getBalanceSheet(Integer companyId) {
        List<Account> accounts = accountRepository.findByCompany_CompanyId(companyId);

        List<BalanceSheetLineDto> currentAssets = new ArrayList<>();
        List<BalanceSheetLineDto> nonCurrentAssets = new ArrayList<>();
        List<BalanceSheetLineDto> currentLiabilities = new ArrayList<>();
        List<BalanceSheetLineDto> nonCurrentLiabilities = new ArrayList<>();
        List<BalanceSheetLineDto> equity = new ArrayList<>();

        BigDecimal totalCurrentAssets = BigDecimal.ZERO;
        BigDecimal totalNonCurrentAssets = BigDecimal.ZERO;
        BigDecimal totalCurrentLiabilities = BigDecimal.ZERO;
        BigDecimal totalNonCurrentLiabilities = BigDecimal.ZERO;
        BigDecimal totalEquityAccounts = BigDecimal.ZERO;

        BigDecimal totalIncome = BigDecimal.ZERO;
        BigDecimal totalExpenses = BigDecimal.ZERO;

        for (Account account : accounts) {
            AccountType type = account.getAccountType();
            BigDecimal balance = account.getCurrentBalance() != null ? account.getCurrentBalance() : BigDecimal.ZERO;

            switch (type) {
                // Assets
                case ASSET_BANK:
                case ASSET_ACCOUNT_RECEIVABLE:
                case ASSET_OTHER_CURRENT_ASSET:
                    currentAssets.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                    totalCurrentAssets = totalCurrentAssets.add(balance);
                    break;
                case ASSET_FIXED_ASSET:
                case ASSET_OTHER_ASSET:
                    nonCurrentAssets.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                    totalNonCurrentAssets = totalNonCurrentAssets.add(balance);
                    break;

                // Liabilities
                case LIABILITY_CREDIT_CARD:
                case LIABILITY_ACCOUNTS_PAYABLE:
                case LIABILITY_OTHER_CURRENT_LIABILITY:
                    currentLiabilities.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                    totalCurrentLiabilities = totalCurrentLiabilities.add(balance);
                    break;
                case LIABILITY_LONG_TERM_LIABILITY:
                case LIABILITY_OTHER_LIABILITY:
                    nonCurrentLiabilities.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                    totalNonCurrentLiabilities = totalNonCurrentLiabilities.add(balance);
                    break;

                // Equity
                case EQUITY:
                    equity.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                    totalEquityAccounts = totalEquityAccounts.add(balance);
                    break;

                // Income
                case INCOME:
                case OTHER_INCOME:
                    totalIncome = totalIncome.add(balance);
                    break;

                // Expenses
                case EXPENSE:
                case COST_OF_SALES:
                case OTHER_EXPENSE:
                    totalExpenses = totalExpenses.add(balance);
                    break;

                default:
                    // If any type is missed, fall back based on main category
                    String mainCat = type.getMainCategory();
                    if ("Asset".equalsIgnoreCase(mainCat)) {
                        currentAssets.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                        totalCurrentAssets = totalCurrentAssets.add(balance);
                    } else if ("Liability".equalsIgnoreCase(mainCat)) {
                        currentLiabilities.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                        totalCurrentLiabilities = totalCurrentLiabilities.add(balance);
                    } else if ("Equity".equalsIgnoreCase(mainCat)) {
                        equity.add(new BalanceSheetLineDto(account.getAccountName(), account.getAccountCode(), balance));
                        totalEquityAccounts = totalEquityAccounts.add(balance);
                    } else if ("Income".equalsIgnoreCase(mainCat)) {
                        totalIncome = totalIncome.add(balance);
                    } else if ("Expense".equalsIgnoreCase(mainCat) || "Cost of Sales".equalsIgnoreCase(mainCat)) {
                        totalExpenses = totalExpenses.add(balance);
                    }
                    break;
            }
        }

        // Net Profit/Loss = Revenue - Expenses
        BigDecimal netProfitLoss = totalIncome.subtract(totalExpenses);

        // Add Net Profit/Loss dynamically as a line in Equity
        equity.add(new BalanceSheetLineDto("Current Period Profit/Loss", "NET_INCOME", netProfitLoss));

        BigDecimal totalAssets = totalCurrentAssets.add(totalNonCurrentAssets);
        BigDecimal totalLiabilities = totalCurrentLiabilities.add(totalNonCurrentLiabilities);
        BigDecimal totalEquity = totalEquityAccounts.add(netProfitLoss);

        BigDecimal diff = totalAssets.subtract(totalLiabilities.add(totalEquity)).abs();
        boolean balanced = diff.compareTo(new BigDecimal("0.01")) < 0;

        BalanceSheetResponseDto response = new BalanceSheetResponseDto();
        response.setCurrentAssets(currentAssets);
        response.setNonCurrentAssets(nonCurrentAssets);
        response.setCurrentLiabilities(currentLiabilities);
        response.setNonCurrentLiabilities(nonCurrentLiabilities);
        response.setEquity(equity);
        response.setNetProfitLoss(netProfitLoss);
        response.setTotalAssets(totalAssets);
        response.setTotalLiabilities(totalLiabilities);
        response.setTotalEquity(totalEquity);
        response.setBalanced(balanced);

        return response;
    }
}
