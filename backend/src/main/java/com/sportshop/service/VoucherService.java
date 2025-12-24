package com.sportshop.service;

import com.sportshop.entity.Voucher;
import com.sportshop.repository.VoucherRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class VoucherService {
    
    private final VoucherRepository voucherRepository;
    
    public List<Voucher> getAllVouchers() {
        return voucherRepository.findAll();
    }
    
    public Optional<Voucher> getVoucherById(Integer id) {
        return voucherRepository.findById(id);
    }
    
    public Optional<Voucher> getVoucherByCode(String code) {
        return voucherRepository.findByVoucherCode(code);
    }
    
    public List<Voucher> getActiveVouchers() {
        return voucherRepository.findActiveVouchers(LocalDate.now());
    }
    
    public Voucher createVoucher(Voucher voucher) {
        if (voucherRepository.existsByVoucherCode(voucher.getVoucherCode())) {
            throw new RuntimeException("Voucher code already exists");
        }
        return voucherRepository.save(voucher);
    }
    
    public Voucher updateVoucher(Integer id, Voucher voucherDetails) {
        Voucher voucher = voucherRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Voucher not found"));
        
        voucher.setVoucherCode(voucherDetails.getVoucherCode());
        voucher.setDiscountValue(voucherDetails.getDiscountValue());
        voucher.setStartDate(voucherDetails.getStartDate());
        voucher.setEndDate(voucherDetails.getEndDate());
        voucher.setQuantity(voucherDetails.getQuantity());
        voucher.setMinOrderValue(voucherDetails.getMinOrderValue());
        
        return voucherRepository.save(voucher);
    }
    
    public void deleteVoucher(Integer id) {
        Voucher voucher = voucherRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Voucher not found"));
        voucherRepository.delete(voucher);
    }
    
    public boolean validateVoucher(String code, java.math.BigDecimal orderAmount) {
        Optional<Voucher> voucherOpt = voucherRepository.findByVoucherCode(code);
        if (voucherOpt.isEmpty()) {
            return false;
        }
        
        Voucher voucher = voucherOpt.get();
        LocalDate today = LocalDate.now();
        
        return voucher.getQuantity() > 0 &&
               !today.isBefore(voucher.getStartDate()) &&
               !today.isAfter(voucher.getEndDate()) &&
               orderAmount.compareTo(voucher.getMinOrderValue()) >= 0;
    }
}
