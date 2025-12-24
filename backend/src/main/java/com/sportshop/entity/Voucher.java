package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

import java.math.BigDecimal;
import java.time.LocalDate;

@Entity
@Table(name = "Voucher")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Voucher {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "VoucherID")
    private Integer voucherId;
    
    @Column(name = "VoucherCode", unique = true, nullable = false, length = 50)
    private String voucherCode; // Thêm trường này
    
    @Column(name = "DiscountValue", nullable = false, precision = 10, scale = 2)
    private BigDecimal discountValue;
    
    @Column(name = "StartDate", nullable = false)
    private LocalDate startDate;
    
    @Column(name = "EndDate", nullable = false)
    private LocalDate endDate;
    
    @Column(name = "Quantity", nullable = false)
    private Integer quantity;
    
    @Column(name = "MinOrderValue", nullable = false, precision = 10, scale = 2)
    private BigDecimal minOrderValue;
}