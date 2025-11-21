package com.sportshop.controller;

import com.sportshop.entity.Product;
import com.sportshop.entity.ProductDetail; // <-- THÊM IMPORT NÀY
import com.sportshop.repository.ProductRepository;
import com.sportshop.repository.ProductDetailRepository; // <-- THÊM IMPORT NÀY
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.data.domain.Sort;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.math.BigDecimal;
import java.util.List;

@RestController
@RequestMapping("/api/products")
@RequiredArgsConstructor
@CrossOrigin(origins = "*") // (Cho phép frontend gọi)
public class ProductController {
    
    private final ProductRepository productRepository;
    private final ProductDetailRepository productDetailRepository; // <-- SỬA: THÊM REPO MỚI

    @GetMapping
    public ResponseEntity<Page<Product>> getAllProducts(
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "12") int size,
            @RequestParam(defaultValue = "productId") String sortBy,
            @RequestParam(defaultValue = "DESC") String sortDir) {
        
        Sort sort = sortDir.equalsIgnoreCase("ASC") 
            ? Sort.by(sortBy).ascending() 
            : Sort.by(sortBy).descending();
        
        Pageable pageable = PageRequest.of(page, size, sort);
        
        // SỬA: Dùng String "active" (giả định Entity Product đã sửa 'status' thành String)
        Page<Product> products = productRepository.findByStatus("active", pageable);
        
        return ResponseEntity.ok(products);
    }
    
    @GetMapping("/{id}")
    public ResponseEntity<Product> getProductById(@PathVariable Integer id) {
        return productRepository.findById(id)
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.notFound().build());
    }
    
    @GetMapping("/search")
    public ResponseEntity<Page<Product>> searchProducts(
            @RequestParam String keyword,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "12") int size) {
        
        Pageable pageable = PageRequest.of(page, size);
        Page<Product> products = productRepository.searchProducts(keyword, pageable);
        
        return ResponseEntity.ok(products);
    }
    
    @GetMapping("/category/{categoryId}")
    public ResponseEntity<Page<Product>> getProductsByCategory(
            @PathVariable Integer categoryId,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "12") int size) {
        
        Pageable pageable = PageRequest.of(page, size);
        Page<Product> products = productRepository.findByCategoryCategoryId(categoryId, pageable);
        
        return ResponseEntity.ok(products);
    }
    
    @GetMapping("/brand/{brandId}")
    public ResponseEntity<Page<Product>> getProductsByBrand(
            @PathVariable Integer brandId,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "12") int size) {
        
        Pageable pageable = PageRequest.of(page, size);
        Page<Product> products = productRepository.findByBrandBrandId(brandId, pageable);
        
        return ResponseEntity.ok(products);
    }
    
    @GetMapping("/price-range")
    public ResponseEntity<Page<Product>> getProductsByPriceRange(
            @RequestParam BigDecimal minPrice,
            @RequestParam BigDecimal maxPrice,
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "12") int size) {
        
        Pageable pageable = PageRequest.of(page, size);
        Page<Product> products = productRepository.findByPriceRange(minPrice, maxPrice, pageable);
        
        return ResponseEntity.ok(products);
    }
    
    @GetMapping("/top-rated")
    public ResponseEntity<List<Product>> getTopRatedProducts() {
        // SỬA: Dùng String "active"
        List<Product> products = productRepository.findTop10ByStatusOrderByRatingAvgDesc("active");
        return ResponseEntity.ok(products);
    }
    
    // --- (API MỚI 100%) ---
    /**
     * API này được gọi bởi product_detail.php
     * để lấy danh sách Size/Màu cho một sản phẩm.
     */
    @GetMapping("/{id}/variants")
    public ResponseEntity<List<ProductDetail>> getProductVariants(@PathVariable("id") Integer productId) {
        // Gọi hàm mới mà chúng ta đã tạo trong ProductDetailRepository
        List<ProductDetail> variants = productDetailRepository.findByProductProductId(productId);
        return ResponseEntity.ok(variants);
    }
    // --- KẾT THÚC API MỚI ---
    
    @PostMapping
    public ResponseEntity<Product> createProduct(@RequestBody Product product) {
        Product savedProduct = productRepository.save(product);
        return ResponseEntity.ok(savedProduct);
    }
    
    @PutMapping("/{id}")
    public ResponseEntity<Product> updateProduct(@PathVariable Integer id, @RequestBody Product product) {
        return productRepository.findById(id)
                .map(existingProduct -> {
                    product.setProductId(id); // Đảm bảo ID được giữ nguyên
                    Product updatedProduct = productRepository.save(product);
                    return ResponseEntity.ok(updatedProduct);
                })
                .orElse(ResponseEntity.notFound().build());
    }
    
    @DeleteMapping("/{id}")
    public ResponseEntity<Void> deleteProduct(@PathVariable Integer id) {
        return productRepository.findById(id)
                .map(product -> {
                    productRepository.delete(product);
                    return ResponseEntity.ok().<Void>build();
                })
                .orElse(ResponseEntity.notFound().build());
    }
}