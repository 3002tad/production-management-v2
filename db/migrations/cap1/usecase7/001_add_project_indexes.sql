-- =====================================================
-- UC7: Order Management - Performance Indexes
-- Purpose: Optimize order list, JOIN, and statistics
-- Date: 2025-11-27
-- Author: Cấp 1 - Danh
-- =====================================================

USE db_production;

-- =====================================================
-- PROJECT TABLE INDEXES (Order Management)
-- =====================================================

-- Index cho JOIN với customer (FK optimization)
-- Tối ưu: OrderModel::getAllOrders() JOIN với customer
CREATE INDEX IF NOT EXISTS idx_project_customer 
ON project(id_cust, pr_status, created_at);

-- Index cho JOIN với product (FK optimization)
-- Tối ưu: OrderModel::getAllOrders() JOIN với product
CREATE INDEX IF NOT EXISTS idx_project_product 
ON project(id_product, pr_status, created_at);

-- Index cho status filter
-- Tối ưu: Filter theo pr_status (1=Mới, 2=Đang SX, 3=Hoàn thành, 4=Hủy)
CREATE INDEX IF NOT EXISTS idx_project_status 
ON project(pr_status, created_at DESC);

-- Composite index cho thống kê
-- Tối ưu: COUNT, SUM, GROUP BY
CREATE INDEX IF NOT EXISTS idx_project_stats 
ON project(id_cust, id_product, pr_status, qty_request);

-- =====================================================
-- CUSTOMER TABLE INDEXES (for Order JOIN)
-- =====================================================

-- Index cho JOIN optimization
-- Đã tạo trong UC1: idx_customer_active

-- =====================================================
-- PRODUCT TABLE INDEXES (for Order JOIN)
-- =====================================================

-- Index cho JOIN optimization
-- Đã tạo trong UC2: idx_product_active

-- =====================================================
-- VERIFY INDEXES
-- =====================================================

-- Kiểm tra indexes đã tạo
SHOW INDEX FROM project WHERE Key_name LIKE 'idx_project%';

-- =====================================================
-- ANALYZE TABLE (Update statistics)
-- =====================================================

ANALYZE TABLE project;
ANALYZE TABLE customer;
ANALYZE TABLE product;

-- =====================================================
-- EXPECTED IMPROVEMENTS (UC7):
-- =====================================================
-- 1. Order list: 60% faster (1000ms → 400ms)
-- 2. JOIN với customer: 40% faster
-- 3. JOIN với product: 40% faster
-- 4. Statistics (COUNT/SUM): 50% faster
-- 5. Filter by status: 30% faster
-- =====================================================
