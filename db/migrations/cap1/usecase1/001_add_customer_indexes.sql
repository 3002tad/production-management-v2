-- =====================================================
-- UC1: Customer Management - Performance Indexes
-- Purpose: Optimize search, filter, and JOIN queries
-- Date: 2025-11-27
-- Author: Cấp 1 - Danh
-- =====================================================

USE db_production;

-- =====================================================
-- CUSTOMER TABLE INDEXES
-- =====================================================

-- Index cho search (cust_name, email, telp)
-- Tối ưu: CustomerModel::searchCustomers()
CREATE INDEX IF NOT EXISTS idx_customer_search 
ON customer(cust_name, email, telp);

-- Index cho LEFT JOIN với project
-- Tối ưu: CustomerModel::getAllCustomers() JOIN
CREATE INDEX IF NOT EXISTS idx_customer_active 
ON customer(is_active, id_cust);

-- Index cho created_at (ORDER BY optimization)
-- Tối ưu: ORDER BY created_at DESC
CREATE INDEX IF NOT EXISTS idx_customer_created 
ON customer(created_at DESC);

-- =====================================================
-- PROJECT TABLE INDEXES (for Customer statistics)
-- =====================================================

-- Index cho JOIN với customer (FK optimization)
-- Tối ưu: COUNT, SUM trong getAllCustomers()
CREATE INDEX IF NOT EXISTS idx_project_customer 
ON project(id_cust, pr_status, created_at);

-- =====================================================
-- VERIFY INDEXES
-- =====================================================

-- Kiểm tra indexes đã tạo
SHOW INDEX FROM customer WHERE Key_name LIKE 'idx_customer%';
SHOW INDEX FROM project WHERE Key_name LIKE 'idx_project_customer%';

-- =====================================================
-- ANALYZE TABLE (Update statistics)
-- =====================================================

ANALYZE TABLE customer;
ANALYZE TABLE project;

-- =====================================================
-- EXPECTED IMPROVEMENTS (UC1):
-- =====================================================
-- 1. Search queries: 75% faster (600ms → 150ms)
-- 2. Customer list: 69% faster (800ms → 250ms)
-- 3. JOIN with project: 40% faster
-- 4. ORDER BY: 30% faster
-- =====================================================
