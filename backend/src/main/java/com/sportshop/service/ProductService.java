package com.sportshop.service;

import com.sportshop.entity.Product;
import com.sportshop.entity.ProductDetail;
import com.sportshop.repository.ProductRepository;
import com.sportshop.repository.ProductDetailRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class ProductService {
    
    private final ProductRepository productRepository;
    private final ProductDetailRepository productDetailRepository;
    
    public Page<Product> getAllProducts(Pageable pageable) {
        return productRepository.findAll(pageable);
    }
    
    public Page<Product> getActiveProducts(Pageable pageable) {
        return productRepository.findByStatus("active", pageable);
    }
    
    public Optional<Product> getProductById(Integer id) {
        return productRepository.findById(id);
    }
    
    public Page<Product> searchProducts(String keyword, Pageable pageable) {
        return productRepository.searchProducts(keyword, pageable);
    }
    
    public Page<Product> getProductsByCategory(Integer categoryId, Pageable pageable) {
        return productRepository.findByCategoryCategoryId(categoryId, pageable);
    }
    
    public Page<Product> getProductsByBrand(Integer brandId, Pageable pageable) {
        return productRepository.findByBrandBrandId(brandId, pageable);
    }
    
    public Page<Product> getProductsByPriceRange(BigDecimal minPrice, BigDecimal maxPrice, Pageable pageable) {
        return productRepository.findByPriceRange(minPrice, maxPrice, pageable);
    }
    
    public List<Product> getTopRatedProducts() {
        return productRepository.findTop10ByStatusOrderByRatingAvgDesc("active");
    }
    
    public List<ProductDetail> getProductVariants(Integer productId) {
        return productDetailRepository.findByProductProductId(productId);
    }
    
    public Product createProduct(Product product) {
        return productRepository.save(product);
    }
    
    public Product updateProduct(Integer id, Product productDetails) {
        Product product = productRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Product not found"));
        
        product.setProductName(productDetails.getProductName());
        product.setDescription(productDetails.getDescription());
        product.setPrice(productDetails.getPrice());
        product.setMainImage(productDetails.getMainImage());
        product.setStatus(productDetails.getStatus());
        product.setCategory(productDetails.getCategory());
        product.setBrand(productDetails.getBrand());
        
        return productRepository.save(product);
    }
    
    public void deleteProduct(Integer id) {
        Product product = productRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Product not found"));
        productRepository.delete(product);
    }
    
    // ProductDetail methods
    public ProductDetail createProductDetail(ProductDetail productDetail) {
        return productDetailRepository.save(productDetail);
    }
    
    public ProductDetail updateProductDetail(Integer id, ProductDetail detailsUpdate) {
        ProductDetail detail = productDetailRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Product detail not found"));
        
        detail.setSize(detailsUpdate.getSize());
        detail.setColor(detailsUpdate.getColor());
        detail.setQuantity(detailsUpdate.getQuantity());
        detail.setImage(detailsUpdate.getImage());
        
        return productDetailRepository.save(detail);
    }
    
    public void deleteProductDetail(Integer id) {
        ProductDetail detail = productDetailRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Product detail not found"));
        productDetailRepository.delete(detail);
    }
}
