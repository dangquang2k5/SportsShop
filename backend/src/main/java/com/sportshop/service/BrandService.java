package com.sportshop.service;

import com.sportshop.entity.Brand;
import com.sportshop.repository.BrandRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class BrandService {
    
    private final BrandRepository brandRepository;
    
    public List<Brand> getAllBrands() {
        return brandRepository.findAll();
    }
    
    public Optional<Brand> getBrandById(Integer id) {
        return brandRepository.findById(id);
    }
    
    public Optional<Brand> getBrandByName(String name) {
        return brandRepository.findByBrandName(name);
    }
    
    public Brand createBrand(Brand brand) {
        if (brandRepository.existsByBrandName(brand.getBrandName())) {
            throw new RuntimeException("Brand name already exists");
        }
        return brandRepository.save(brand);
    }
    
    public Brand updateBrand(Integer id, Brand brandDetails) {
        Brand brand = brandRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Brand not found"));
        
        brand.setBrandName(brandDetails.getBrandName());
        brand.setBrandDescription(brandDetails.getBrandDescription());
        
        return brandRepository.save(brand);
    }
    
    public void deleteBrand(Integer id) {
        Brand brand = brandRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Brand not found"));
        brandRepository.delete(brand);
    }
}
