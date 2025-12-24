package com.sportshop.service;

import com.sportshop.entity.Review;
import com.sportshop.repository.ReviewRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class ReviewService {
    
    private final ReviewRepository reviewRepository;
    
    public List<Review> getAllReviews() {
        return reviewRepository.findAll();
    }
    
    public Optional<Review> getReviewById(Integer id) {
        return reviewRepository.findById(id);
    }
    
    public List<Review> getReviewsByProduct(Integer productId) {
        return reviewRepository.findByProductProductId(productId);
    }
    
    public List<Review> getApprovedReviewsByProduct(Integer productId) {
        return reviewRepository.findByProductIdAndStatus(productId, "approved");
    }
    
    public List<Review> getReviewsByUser(Integer userId) {
        return reviewRepository.findByUserUserId(userId);
    }
    
    public List<Review> getReviewsByStatus(String status) {
        return reviewRepository.findByStatus(status);
    }
    
    public Review createReview(Review review) {
        review.setStatus("pending");
        return reviewRepository.save(review);
    }
    
    public Review updateReview(Integer id, Review reviewDetails) {
        Review review = reviewRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Review not found"));
        
        review.setContent(reviewDetails.getContent());
        review.setRating(reviewDetails.getRating());
        review.setStatus(reviewDetails.getStatus());
        
        return reviewRepository.save(review);
    }
    
    public Review updateReviewStatus(Integer id, String status) {
        Review review = reviewRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Review not found"));
        
        review.setStatus(status);
        return reviewRepository.save(review);
    }
    
    public void deleteReview(Integer id) {
        Review review = reviewRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Review not found"));
        reviewRepository.delete(review);
    }
}
