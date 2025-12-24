package com.sportshop.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

@Entity
@Table(name = "Brand") // Sửa 'Brands'
@Data
@NoArgsConstructor
@AllArgsConstructor
public class Brand {
    
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "BrandID")
    private Integer brandId;
    
    @Column(name = "BrandName", unique = true, nullable = false, length = 100)
    private String brandName;
    
    @Column(name = "BrandDescription", columnDefinition = "TEXT")
    private String brandDescription;
}