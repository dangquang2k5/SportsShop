package com.sportshop.entity;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.jpa.domain.support.AuditingEntityListener;

import java.time.LocalDateTime;

@Entity
@Table(name = "Messages")
@Data
@NoArgsConstructor
@AllArgsConstructor
@EntityListeners(AuditingEntityListener.class)
public class Message {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "MessageID")
    private Integer id;

    @ManyToOne(fetch = FetchType.EAGER)
    @JoinColumn(name = "UserID")
    // Quan trọng: Tránh lỗi serialize User kéo theo đống data thừa
    @JsonIgnoreProperties({"password", "messages", "hibernateLazyInitializer", "handler"}) 
    private User user;

    @Column(name = "Content", columnDefinition = "TEXT")
    private String content;

    // Backend là adminFlag thì JSON trả về sẽ là "adminFlag"
    @Column(name = "AdminFlag")
    private Boolean adminFlag = false; 

    @CreatedDate
    @Column(name = "SentAt", updatable = false)
    private LocalDateTime sentAt;
}