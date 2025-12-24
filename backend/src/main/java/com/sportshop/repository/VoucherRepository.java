package com.sportshop.repository;

import com.sportshop.entity.Voucher;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

@Repository
public interface VoucherRepository extends JpaRepository<Voucher, Integer> {
    
    Optional<Voucher> findByVoucherCode(String voucherCode);
    
    boolean existsByVoucherCode(String voucherCode);
    
    @Query("SELECT v FROM Voucher v WHERE " +
           "v.startDate <= :currentDate AND " +
           "v.endDate >= :currentDate AND " +
           "v.quantity > 0 " +
           "ORDER BY v.discountValue DESC")
    List<Voucher> findActiveVouchers(@Param("currentDate") LocalDate currentDate);
}
