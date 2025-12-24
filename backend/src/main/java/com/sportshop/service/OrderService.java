package com.sportshop.service;

import com.sportshop.entity.Order;
import com.sportshop.entity.OrderDetail;
import com.sportshop.repository.OrderRepository;
import com.sportshop.repository.OrderDetailRepository;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.LocalDate;
import java.util.List;
import java.util.Optional;

@Service
@RequiredArgsConstructor
@Transactional
public class OrderService {
    
    private final OrderRepository orderRepository;
    private final OrderDetailRepository orderDetailRepository;
    
    public Page<Order> getAllOrders(Pageable pageable) {
        return orderRepository.findAll(pageable);
    }
    
    public Optional<Order> getOrderById(Integer id) {
        return orderRepository.findById(id);
    }
    
    public List<Order> getOrdersByUser(Integer userId) {
        return orderRepository.findByUserUserIdOrderByCreatedAtDesc(userId);
    }
    
    public List<Order> getOrdersByStatus(String status) {
        return orderRepository.findByStatus(status);
    }
    
    public List<Order> getOrdersByDateRange(LocalDate startDate, LocalDate endDate) {
        return orderRepository.findOrdersByDateRange(startDate, endDate);
    }
    
    public Order createOrder(Order order) {
        order.setStatus("pending");
        return orderRepository.save(order);
    }
    
    public Order updateOrder(Integer id, Order orderDetails) {
        Order order = orderRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Order not found"));
        
        order.setAddress(orderDetails.getAddress());
        order.setStatus(orderDetails.getStatus());
        order.setNote(orderDetails.getNote());
        order.setTotalAmount(orderDetails.getTotalAmount());
        
        return orderRepository.save(order);
    }
    
    public Order updateOrderStatus(Integer id, String status) {
        Order order = orderRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Order not found"));
        
        order.setStatus(status);
        return orderRepository.save(order);
    }
    
    public void deleteOrder(Integer id) {
        Order order = orderRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Order not found"));
        orderRepository.delete(order);
    }
    
    public List<OrderDetail> getOrderDetails(Integer orderId) {
        return orderDetailRepository.findByOrderOrderId(orderId);
    }
}
