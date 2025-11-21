package com.sportshop.repository;

import com.sportshop.entity.Order;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.time.LocalDateTime;
import java.util.List;

@Repository
public interface OrderRepository extends JpaRepository<Order, Integer> {
    
    List<Order> findByUserUserIdOrderByCreatedAtDesc(Integer userId);
    
    Page<Order> findByUserUserId(Integer userId, Pageable pageable);
    
    List<Order> findByStatus(String status);
    
    Page<Order> findByStatus(String status, Pageable pageable);
    
    // Fixed: Use Java field names (createdAt) not column names
    @Query("SELECT o FROM Order o WHERE DATE(o.createdAt) BETWEEN :startDate AND :endDate")
    List<Order> findOrdersByDateRange(@Param("startDate") LocalDate startDate, 
                                      @Param("endDate") LocalDate endDate);
    
    // Fixed: Use Java field names (status) not column names
    @Query("SELECT COUNT(o) FROM Order o WHERE o.status IN ('delivered', 'shipped')")
    Long countCompletedOrders();
    
    // Fixed: Use Java field names (totalAmount, status) not column names
    @Query("SELECT SUM(o.totalAmount) FROM Order o WHERE o.status IN ('delivered', 'shipped')")
    Double getTotalRevenue();
}