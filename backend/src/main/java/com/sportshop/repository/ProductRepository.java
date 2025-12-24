package com.sportshop.repository;

import com.sportshop.entity.Product;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.math.BigDecimal;
import java.util.List;

@Repository
public interface ProductRepository extends JpaRepository<Product, Integer> {
    
    // SỬA: Sửa tên cột 'Status' (và giả định bạn đã sửa Entity Product để Status là String)
    Page<Product> findByStatus(String status, Pageable pageable);
    
    Page<Product> findByCategoryCategoryId(Integer categoryId, Pageable pageable);
    
    Page<Product> findByBrandBrandId(Integer brandId, Pageable pageable);
    
    // Fixed: Use Java field names (productName, description) not column names
    @Query("SELECT p FROM Product p WHERE " +
           "LOWER(p.productName) LIKE LOWER(CONCAT('%', :keyword, '%')) OR " +
           "LOWER(p.description) LIKE LOWER(CONCAT('%', :keyword, '%'))")
    Page<Product> searchProducts(@Param("keyword") String keyword, Pageable pageable);
    
    // Fixed: Use Java field names (price, status) not column names
    @Query("SELECT p FROM Product p WHERE " +
           "p.price BETWEEN :minPrice AND :maxPrice AND " +
           "p.status = 'active'")
    Page<Product> findByPriceRange(@Param("minPrice") BigDecimal minPrice, 
                                    @Param("maxPrice") BigDecimal maxPrice, 
                                    Pageable pageable);
    
    // Fixed: Use Java field names (status, ratingAvg) not column names
    List<Product> findTop10ByStatusOrderByRatingAvgDesc(String status);
}