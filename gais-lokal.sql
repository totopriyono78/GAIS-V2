--
-- PostgreSQL database dump
--

\restrict TscsQJZ9UC7Z59nOJwe6bPZdvv88foFAurhIjgrnwLWx5bolG4H0HxjXZRMqlzJ

-- Dumped from database version 15.16
-- Dumped by pg_dump version 15.16

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_technician_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_service_request_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_reported_by_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_maintenance_visit_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_maintenance_schedule_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_order_attachments DROP CONSTRAINT IF EXISTS work_order_attachments_work_order_id_foreign;
ALTER TABLE IF EXISTS ONLY public.work_order_attachments DROP CONSTRAINT IF EXISTS work_order_attachments_uploaded_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_supply_purchase_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_paid_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_approved_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bill_lines DROP CONSTRAINT IF EXISTS vendor_bill_lines_vendor_bill_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bill_lines DROP CONSTRAINT IF EXISTS vendor_bill_lines_expense_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vendor_bill_lines DROP CONSTRAINT IF EXISTS vendor_bill_lines_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicles DROP CONSTRAINT IF EXISTS vehicles_default_driver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicles DROP CONSTRAINT IF EXISTS vehicles_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_trips DROP CONSTRAINT IF EXISTS vehicle_trips_vehicle_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_trips DROP CONSTRAINT IF EXISTS vehicle_trips_vehicle_booking_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_trips DROP CONSTRAINT IF EXISTS vehicle_trips_driver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_trips DROP CONSTRAINT IF EXISTS vehicle_trips_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_refuelings DROP CONSTRAINT IF EXISTS vehicle_refuelings_vehicle_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_refuelings DROP CONSTRAINT IF EXISTS vehicle_refuelings_driver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_refuelings DROP CONSTRAINT IF EXISTS vehicle_refuelings_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_photos DROP CONSTRAINT IF EXISTS vehicle_photos_vehicle_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_photos DROP CONSTRAINT IF EXISTS vehicle_photos_uploaded_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_documents DROP CONSTRAINT IF EXISTS vehicle_documents_vehicle_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_documents DROP CONSTRAINT IF EXISTS vehicle_documents_uploaded_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_vehicle_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_requester_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_driver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_assigned_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_approver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_supply_item_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_issued_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_approver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_request_lines DROP CONSTRAINT IF EXISTS supply_request_lines_supply_transaction_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_request_lines DROP CONSTRAINT IF EXISTS supply_request_lines_supply_request_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_request_lines DROP CONSTRAINT IF EXISTS supply_request_lines_supply_item_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_receipts DROP CONSTRAINT IF EXISTS supply_receipts_supply_purchase_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_receipts DROP CONSTRAINT IF EXISTS supply_receipts_received_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_receipt_lines DROP CONSTRAINT IF EXISTS supply_receipt_lines_supply_transaction_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_receipt_lines DROP CONSTRAINT IF EXISTS supply_receipt_lines_supply_receipt_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_receipt_lines DROP CONSTRAINT IF EXISTS supply_receipt_lines_supply_purchase_line_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_purchases DROP CONSTRAINT IF EXISTS supply_purchases_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_purchases DROP CONSTRAINT IF EXISTS supply_purchases_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_purchases DROP CONSTRAINT IF EXISTS supply_purchases_approved_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_purchase_lines DROP CONSTRAINT IF EXISTS supply_purchase_lines_supply_purchase_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_purchase_lines DROP CONSTRAINT IF EXISTS supply_purchase_lines_supply_item_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opnames DROP CONSTRAINT IF EXISTS supply_opnames_scope_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opnames DROP CONSTRAINT IF EXISTS supply_opnames_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opnames DROP CONSTRAINT IF EXISTS supply_opnames_adjusted_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opname_lines DROP CONSTRAINT IF EXISTS supply_opname_lines_supply_transaction_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opname_lines DROP CONSTRAINT IF EXISTS supply_opname_lines_supply_opname_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_opname_lines DROP CONSTRAINT IF EXISTS supply_opname_lines_supply_item_id_foreign;
ALTER TABLE IF EXISTS ONLY public.supply_items DROP CONSTRAINT IF EXISTS supply_items_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_started_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_scope_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_scope_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_scope_asset_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_finished_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_adjusted_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_stock_opname_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_found_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_expected_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_checked_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_staff DROP CONSTRAINT IF EXISTS service_staff_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_staff DROP CONSTRAINT IF EXISTS service_staff_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_work_order_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_service_request_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_requester_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_approver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_accepted_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_request_attachments DROP CONSTRAINT IF EXISTS service_request_attachments_uploaded_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_request_attachments DROP CONSTRAINT IF EXISTS service_request_attachments_service_request_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_areas DROP CONSTRAINT IF EXISTS service_areas_service_staff_id_foreign;
ALTER TABLE IF EXISTS ONLY public.service_areas DROP CONSTRAINT IF EXISTS service_areas_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_service_staff_id_foreign;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_replacement_staff_id_foreign;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.role_user DROP CONSTRAINT IF EXISTS role_user_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.role_user DROP CONSTRAINT IF EXISTS role_user_role_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_verified_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_paid_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_approver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursement_lines DROP CONSTRAINT IF EXISTS reimbursement_lines_reimbursement_id_foreign;
ALTER TABLE IF EXISTS ONLY public.reimbursement_lines DROP CONSTRAINT IF EXISTS reimbursement_lines_expense_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_module_id_foreign;
ALTER TABLE IF EXISTS ONLY public.permission_user DROP CONSTRAINT IF EXISTS permission_user_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.permission_user DROP CONSTRAINT IF EXISTS permission_user_permission_id_foreign;
ALTER TABLE IF EXISTS ONLY public.permission_role DROP CONSTRAINT IF EXISTS permission_role_role_id_foreign;
ALTER TABLE IF EXISTS ONLY public.permission_role DROP CONSTRAINT IF EXISTS permission_role_permission_id_foreign;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_requester_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.number_sequence_periods DROP CONSTRAINT IF EXISTS number_sequence_periods_number_sequence_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_technician_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_maintenance_schedule_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_closed_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_schedules DROP CONSTRAINT IF EXISTS maintenance_schedules_vendor_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_schedules DROP CONSTRAINT IF EXISTS maintenance_schedules_technician_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.maintenance_schedules DROP CONSTRAINT IF EXISTS maintenance_schedules_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.locations DROP CONSTRAINT IF EXISTS locations_parent_id_foreign;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_signer_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_handed_over_to_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_assigned_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_service_request_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_security_shift_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_reported_by_staff_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_closed_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.depreciation_periods DROP CONSTRAINT IF EXISTS depreciation_periods_closed_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.depreciation_entries DROP CONSTRAINT IF EXISTS depreciation_entries_depreciation_period_id_foreign;
ALTER TABLE IF EXISTS ONLY public.depreciation_entries DROP CONSTRAINT IF EXISTS depreciation_entries_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.departments DROP CONSTRAINT IF EXISTS departments_parent_id_foreign;
ALTER TABLE IF EXISTS ONLY public.departments DROP CONSTRAINT IF EXISTS departments_head_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspections DROP CONSTRAINT IF EXISTS cleaning_inspections_scope_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspections DROP CONSTRAINT IF EXISTS cleaning_inspections_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspection_lines DROP CONSTRAINT IF EXISTS cleaning_inspection_lines_service_area_id_foreign;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspection_lines DROP CONSTRAINT IF EXISTS cleaning_inspection_lines_cleaning_inspection_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_settled_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_approver_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_advance_paid_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trip_participants DROP CONSTRAINT IF EXISTS business_trip_participants_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trip_participants DROP CONSTRAINT IF EXISTS business_trip_participants_business_trip_id_foreign;
ALTER TABLE IF EXISTS ONLY public.business_trip_expenses DROP CONSTRAINT IF EXISTS business_trip_expenses_business_trip_id_foreign;
ALTER TABLE IF EXISTS ONLY public.budgets DROP CONSTRAINT IF EXISTS budgets_expense_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.budgets DROP CONSTRAINT IF EXISTS budgets_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.budgets DROP CONSTRAINT IF EXISTS budgets_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.audit_logs DROP CONSTRAINT IF EXISTS audit_logs_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_custodian_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_asset_category_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_to_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_to_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_to_custodian_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_received_by_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_handed_over_by_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_from_location_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_from_department_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_from_custodian_employee_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_documents DROP CONSTRAINT IF EXISTS asset_documents_uploaded_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_documents DROP CONSTRAINT IF EXISTS asset_documents_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_created_by_user_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_asset_id_foreign;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_approved_by_employee_id_foreign;
DROP INDEX IF EXISTS public.work_orders_type_status_index;
DROP INDEX IF EXISTS public.work_orders_status_priority_index;
DROP INDEX IF EXISTS public.work_orders_asset_id_reported_date_index;
DROP INDEX IF EXISTS public.work_order_attachments_work_order_id_type_index;
DROP INDEX IF EXISTS public.vendors_is_active_name_index;
DROP INDEX IF EXISTS public.vendor_bills_vendor_id_invoice_date_index;
DROP INDEX IF EXISTS public.vendor_bills_status_invoice_date_index;
DROP INDEX IF EXISTS public.vendor_bills_due_date_index;
DROP INDEX IF EXISTS public.vendor_bill_lines_vendor_bill_id_index;
DROP INDEX IF EXISTS public.vendor_bill_lines_expense_category_id_department_id_index;
DROP INDEX IF EXISTS public.vehicles_vehicle_type_index;
DROP INDEX IF EXISTS public.vehicles_usage_mode_is_active_index;
DROP INDEX IF EXISTS public.vehicle_trips_vehicle_id_departed_at_index;
DROP INDEX IF EXISTS public.vehicle_trips_vehicle_booking_id_index;
DROP INDEX IF EXISTS public.vehicle_refuelings_vehicle_id_odometer_km_index;
DROP INDEX IF EXISTS public.vehicle_refuelings_vehicle_id_filled_at_index;
DROP INDEX IF EXISTS public.vehicle_photos_vehicle_id_type_index;
DROP INDEX IF EXISTS public.vehicle_documents_vehicle_id_type_expires_at_index;
DROP INDEX IF EXISTS public.vehicle_documents_expires_at_index;
DROP INDEX IF EXISTS public.vehicle_bookings_vehicle_id_start_at_end_at_index;
DROP INDEX IF EXISTS public.vehicle_bookings_status_start_at_index;
DROP INDEX IF EXISTS public.vehicle_bookings_requester_employee_id_status_index;
DROP INDEX IF EXISTS public.supply_transactions_type_index;
DROP INDEX IF EXISTS public.supply_transactions_supply_item_id_transaction_date_index;
DROP INDEX IF EXISTS public.supply_requests_status_submitted_at_index;
DROP INDEX IF EXISTS public.supply_requests_employee_id_status_index;
DROP INDEX IF EXISTS public.supply_requests_department_id_status_index;
DROP INDEX IF EXISTS public.supply_request_lines_supply_item_id_index;
DROP INDEX IF EXISTS public.supply_receipts_supply_purchase_id_receipt_date_index;
DROP INDEX IF EXISTS public.supply_receipt_lines_supply_purchase_line_id_index;
DROP INDEX IF EXISTS public.supply_purchases_vendor_id_index;
DROP INDEX IF EXISTS public.supply_purchases_status_order_date_index;
DROP INDEX IF EXISTS public.supply_purchases_kind_status_index;
DROP INDEX IF EXISTS public.supply_purchase_lines_supply_item_id_index;
DROP INDEX IF EXISTS public.supply_opnames_status_created_at_index;
DROP INDEX IF EXISTS public.supply_opname_lines_supply_opname_id_checked_index;
DROP INDEX IF EXISTS public.supply_items_is_active_index;
DROP INDEX IF EXISTS public.supply_items_category_index;
DROP INDEX IF EXISTS public.stock_opnames_status_index;
DROP INDEX IF EXISTS public.stock_opname_lines_stock_opname_id_checked_index;
DROP INDEX IF EXISTS public.stock_opname_lines_asset_code_index;
DROP INDEX IF EXISTS public.sessions_user_id_index;
DROP INDEX IF EXISTS public.sessions_last_activity_index;
DROP INDEX IF EXISTS public.service_staff_kind_is_active_index;
DROP INDEX IF EXISTS public.service_requests_status_priority_index;
DROP INDEX IF EXISTS public.service_requests_requester_employee_id_status_index;
DROP INDEX IF EXISTS public.service_requests_department_id_status_index;
DROP INDEX IF EXISTS public.service_request_categories_is_active_code_index;
DROP INDEX IF EXISTS public.service_request_attachments_service_request_id_index;
DROP INDEX IF EXISTS public.service_areas_frequency_is_active_index;
DROP INDEX IF EXISTS public.service_areas_category_is_active_index;
DROP INDEX IF EXISTS public.security_shifts_shift_date_shift_index;
DROP INDEX IF EXISTS public.security_shifts_attendance_shift_date_index;
DROP INDEX IF EXISTS public.reimbursements_status_submitted_at_index;
DROP INDEX IF EXISTS public.reimbursements_employee_id_status_index;
DROP INDEX IF EXISTS public.reimbursements_department_id_index;
DROP INDEX IF EXISTS public.reimbursement_lines_reimbursement_id_index;
DROP INDEX IF EXISTS public.reimbursement_lines_expense_category_id_expense_date_index;
DROP INDEX IF EXISTS public.parcel_shipments_status_request_date_index;
DROP INDEX IF EXISTS public.parcel_shipments_shipped_date_index;
DROP INDEX IF EXISTS public.modules_group_sort_index;
DROP INDEX IF EXISTS public.maintenance_visits_status_due_date_index;
DROP INDEX IF EXISTS public.maintenance_visits_maintenance_schedule_id_due_date_index;
DROP INDEX IF EXISTS public.maintenance_visits_asset_id_due_date_index;
DROP INDEX IF EXISTS public.maintenance_schedules_is_active_next_due_date_index;
DROP INDEX IF EXISTS public.maintenance_schedules_asset_id_is_active_index;
DROP INDEX IF EXISTS public.locations_type_name_index;
DROP INDEX IF EXISTS public.letters_direction_logged_date_index;
DROP INDEX IF EXISTS public.letters_category_logged_date_index;
DROP INDEX IF EXISTS public.jobs_queue_index;
DROP INDEX IF EXISTS public.incident_reports_occurred_at_index;
DROP INDEX IF EXISTS public.incident_reports_category_severity_index;
DROP INDEX IF EXISTS public.expense_categories_is_active_code_index;
DROP INDEX IF EXISTS public.employees_full_name_index;
DROP INDEX IF EXISTS public.depreciation_entries_period_index;
DROP INDEX IF EXISTS public.depreciation_entries_depreciation_period_id_asset_id_index;
DROP INDEX IF EXISTS public.departments_name_index;
DROP INDEX IF EXISTS public.cleaning_inspections_status_inspection_date_index;
DROP INDEX IF EXISTS public.cleaning_inspection_lines_cleaning_inspection_id_checked_index;
DROP INDEX IF EXISTS public.business_trips_status_start_date_index;
DROP INDEX IF EXISTS public.business_trips_employee_id_start_date_index;
DROP INDEX IF EXISTS public.business_trip_expenses_business_trip_id_expense_date_index;
DROP INDEX IF EXISTS public.budgets_fiscal_year_department_id_index;
DROP INDEX IF EXISTS public.audit_logs_event_created_at_index;
DROP INDEX IF EXISTS public.audit_logs_auditable_type_auditable_id_index;
DROP INDEX IF EXISTS public.assets_warranty_until_index;
DROP INDEX IF EXISTS public.assets_status_condition_index;
DROP INDEX IF EXISTS public.assets_serial_number_index;
DROP INDEX IF EXISTS public.assets_ownership_type_index;
DROP INDEX IF EXISTS public.assets_name_index;
DROP INDEX IF EXISTS public.assets_lease_end_date_index;
DROP INDEX IF EXISTS public.assets_asset_category_id_location_id_index;
DROP INDEX IF EXISTS public.asset_transfers_transfer_date_index;
DROP INDEX IF EXISTS public.asset_transfers_asset_id_transfer_date_index;
DROP INDEX IF EXISTS public.asset_documents_asset_id_type_index;
DROP INDEX IF EXISTS public.asset_disposals_method_index;
DROP INDEX IF EXISTS public.asset_disposals_disposal_date_index;
DROP INDEX IF EXISTS public.asset_categories_name_index;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_pkey;
ALTER TABLE IF EXISTS ONLY public.work_orders DROP CONSTRAINT IF EXISTS work_orders_code_unique;
ALTER TABLE IF EXISTS ONLY public.work_order_attachments DROP CONSTRAINT IF EXISTS work_order_attachments_pkey;
ALTER TABLE IF EXISTS ONLY public.vendors DROP CONSTRAINT IF EXISTS vendors_pkey;
ALTER TABLE IF EXISTS ONLY public.vendors DROP CONSTRAINT IF EXISTS vendors_code_unique;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_pkey;
ALTER TABLE IF EXISTS ONLY public.vendor_bills DROP CONSTRAINT IF EXISTS vendor_bills_code_unique;
ALTER TABLE IF EXISTS ONLY public.vendor_bill_lines DROP CONSTRAINT IF EXISTS vendor_bill_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicles DROP CONSTRAINT IF EXISTS vehicles_plate_number_unique;
ALTER TABLE IF EXISTS ONLY public.vehicles DROP CONSTRAINT IF EXISTS vehicles_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicles DROP CONSTRAINT IF EXISTS vehicles_asset_id_unique;
ALTER TABLE IF EXISTS ONLY public.vehicle_trips DROP CONSTRAINT IF EXISTS vehicle_trips_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicle_refuelings DROP CONSTRAINT IF EXISTS vehicle_refuelings_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicle_photos DROP CONSTRAINT IF EXISTS vehicle_photos_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicle_documents DROP CONSTRAINT IF EXISTS vehicle_documents_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_pkey;
ALTER TABLE IF EXISTS ONLY public.vehicle_bookings DROP CONSTRAINT IF EXISTS vehicle_bookings_code_unique;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_pkey;
ALTER TABLE IF EXISTS ONLY public.users DROP CONSTRAINT IF EXISTS users_email_unique;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_transactions DROP CONSTRAINT IF EXISTS supply_transactions_code_unique;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_requests DROP CONSTRAINT IF EXISTS supply_requests_code_unique;
ALTER TABLE IF EXISTS ONLY public.supply_request_lines DROP CONSTRAINT IF EXISTS supply_request_lines_supply_request_id_supply_item_id_unique;
ALTER TABLE IF EXISTS ONLY public.supply_request_lines DROP CONSTRAINT IF EXISTS supply_request_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_receipts DROP CONSTRAINT IF EXISTS supply_receipts_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_receipts DROP CONSTRAINT IF EXISTS supply_receipts_code_unique;
ALTER TABLE IF EXISTS ONLY public.supply_receipt_lines DROP CONSTRAINT IF EXISTS supply_receipt_lines_supply_receipt_id_supply_purchase_line_id_;
ALTER TABLE IF EXISTS ONLY public.supply_receipt_lines DROP CONSTRAINT IF EXISTS supply_receipt_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_purchases DROP CONSTRAINT IF EXISTS supply_purchases_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_purchases DROP CONSTRAINT IF EXISTS supply_purchases_code_unique;
ALTER TABLE IF EXISTS ONLY public.supply_purchase_lines DROP CONSTRAINT IF EXISTS supply_purchase_lines_supply_purchase_id_supply_item_id_unique;
ALTER TABLE IF EXISTS ONLY public.supply_purchase_lines DROP CONSTRAINT IF EXISTS supply_purchase_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_opnames DROP CONSTRAINT IF EXISTS supply_opnames_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_opnames DROP CONSTRAINT IF EXISTS supply_opnames_code_unique;
ALTER TABLE IF EXISTS ONLY public.supply_opname_lines DROP CONSTRAINT IF EXISTS supply_opname_lines_supply_opname_id_supply_item_id_unique;
ALTER TABLE IF EXISTS ONLY public.supply_opname_lines DROP CONSTRAINT IF EXISTS supply_opname_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_items DROP CONSTRAINT IF EXISTS supply_items_pkey;
ALTER TABLE IF EXISTS ONLY public.supply_items DROP CONSTRAINT IF EXISTS supply_items_code_unique;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_pkey;
ALTER TABLE IF EXISTS ONLY public.stock_opnames DROP CONSTRAINT IF EXISTS stock_opnames_code_unique;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_stock_opname_id_asset_id_unique;
ALTER TABLE IF EXISTS ONLY public.stock_opname_lines DROP CONSTRAINT IF EXISTS stock_opname_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.settings DROP CONSTRAINT IF EXISTS settings_pkey;
ALTER TABLE IF EXISTS ONLY public.settings DROP CONSTRAINT IF EXISTS settings_key_unique;
ALTER TABLE IF EXISTS ONLY public.sessions DROP CONSTRAINT IF EXISTS sessions_pkey;
ALTER TABLE IF EXISTS ONLY public.service_staff DROP CONSTRAINT IF EXISTS service_staff_pkey;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_pkey;
ALTER TABLE IF EXISTS ONLY public.service_requests DROP CONSTRAINT IF EXISTS service_requests_code_unique;
ALTER TABLE IF EXISTS ONLY public.service_request_categories DROP CONSTRAINT IF EXISTS service_request_categories_pkey;
ALTER TABLE IF EXISTS ONLY public.service_request_categories DROP CONSTRAINT IF EXISTS service_request_categories_code_unique;
ALTER TABLE IF EXISTS ONLY public.service_request_attachments DROP CONSTRAINT IF EXISTS service_request_attachments_pkey;
ALTER TABLE IF EXISTS ONLY public.service_areas DROP CONSTRAINT IF EXISTS service_areas_pkey;
ALTER TABLE IF EXISTS ONLY public.service_areas DROP CONSTRAINT IF EXISTS service_areas_code_unique;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_unik;
ALTER TABLE IF EXISTS ONLY public.security_shifts DROP CONSTRAINT IF EXISTS security_shifts_pkey;
ALTER TABLE IF EXISTS ONLY public.roles DROP CONSTRAINT IF EXISTS roles_pkey;
ALTER TABLE IF EXISTS ONLY public.roles DROP CONSTRAINT IF EXISTS roles_code_unique;
ALTER TABLE IF EXISTS ONLY public.role_user DROP CONSTRAINT IF EXISTS role_user_pkey;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_pkey;
ALTER TABLE IF EXISTS ONLY public.reimbursements DROP CONSTRAINT IF EXISTS reimbursements_code_unique;
ALTER TABLE IF EXISTS ONLY public.reimbursement_lines DROP CONSTRAINT IF EXISTS reimbursement_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_pkey;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_module_id_action_unique;
ALTER TABLE IF EXISTS ONLY public.permissions DROP CONSTRAINT IF EXISTS permissions_key_unique;
ALTER TABLE IF EXISTS ONLY public.permission_user DROP CONSTRAINT IF EXISTS permission_user_pkey;
ALTER TABLE IF EXISTS ONLY public.permission_role DROP CONSTRAINT IF EXISTS permission_role_pkey;
ALTER TABLE IF EXISTS ONLY public.password_reset_tokens DROP CONSTRAINT IF EXISTS password_reset_tokens_pkey;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_pkey;
ALTER TABLE IF EXISTS ONLY public.parcel_shipments DROP CONSTRAINT IF EXISTS parcel_shipments_code_unique;
ALTER TABLE IF EXISTS ONLY public.number_sequences DROP CONSTRAINT IF EXISTS number_sequences_pkey;
ALTER TABLE IF EXISTS ONLY public.number_sequences DROP CONSTRAINT IF EXISTS number_sequences_code_unique;
ALTER TABLE IF EXISTS ONLY public.number_sequence_periods DROP CONSTRAINT IF EXISTS number_sequence_periods_pkey;
ALTER TABLE IF EXISTS ONLY public.number_sequence_periods DROP CONSTRAINT IF EXISTS number_sequence_periods_number_sequence_id_period_unique;
ALTER TABLE IF EXISTS ONLY public.modules DROP CONSTRAINT IF EXISTS modules_pkey;
ALTER TABLE IF EXISTS ONLY public.modules DROP CONSTRAINT IF EXISTS modules_code_unique;
ALTER TABLE IF EXISTS ONLY public.migrations DROP CONSTRAINT IF EXISTS migrations_pkey;
ALTER TABLE IF EXISTS ONLY public.maintenance_visits DROP CONSTRAINT IF EXISTS maintenance_visits_pkey;
ALTER TABLE IF EXISTS ONLY public.maintenance_schedules DROP CONSTRAINT IF EXISTS maintenance_schedules_pkey;
ALTER TABLE IF EXISTS ONLY public.locations DROP CONSTRAINT IF EXISTS locations_pkey;
ALTER TABLE IF EXISTS ONLY public.locations DROP CONSTRAINT IF EXISTS locations_code_unique;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_pkey;
ALTER TABLE IF EXISTS ONLY public.letters DROP CONSTRAINT IF EXISTS letters_code_unique;
ALTER TABLE IF EXISTS ONLY public.jobs DROP CONSTRAINT IF EXISTS jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.job_batches DROP CONSTRAINT IF EXISTS job_batches_pkey;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_pkey;
ALTER TABLE IF EXISTS ONLY public.incident_reports DROP CONSTRAINT IF EXISTS incident_reports_code_unique;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_uuid_unique;
ALTER TABLE IF EXISTS ONLY public.failed_jobs DROP CONSTRAINT IF EXISTS failed_jobs_pkey;
ALTER TABLE IF EXISTS ONLY public.expense_categories DROP CONSTRAINT IF EXISTS expense_categories_pkey;
ALTER TABLE IF EXISTS ONLY public.expense_categories DROP CONSTRAINT IF EXISTS expense_categories_code_unique;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_user_id_unique;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_pkey;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_nip_unique;
ALTER TABLE IF EXISTS ONLY public.employees DROP CONSTRAINT IF EXISTS employees_email_unique;
ALTER TABLE IF EXISTS ONLY public.depreciation_periods DROP CONSTRAINT IF EXISTS depreciation_periods_pkey;
ALTER TABLE IF EXISTS ONLY public.depreciation_periods DROP CONSTRAINT IF EXISTS depreciation_periods_period_unique;
ALTER TABLE IF EXISTS ONLY public.depreciation_entries DROP CONSTRAINT IF EXISTS depreciation_entries_pkey;
ALTER TABLE IF EXISTS ONLY public.depreciation_entries DROP CONSTRAINT IF EXISTS depreciation_entries_asset_id_period_unique;
ALTER TABLE IF EXISTS ONLY public.departments DROP CONSTRAINT IF EXISTS departments_pkey;
ALTER TABLE IF EXISTS ONLY public.departments DROP CONSTRAINT IF EXISTS departments_code_unique;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspections DROP CONSTRAINT IF EXISTS cleaning_inspections_pkey;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspections DROP CONSTRAINT IF EXISTS cleaning_inspections_code_unique;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspection_lines DROP CONSTRAINT IF EXISTS cleaning_inspection_lines_pkey;
ALTER TABLE IF EXISTS ONLY public.cleaning_inspection_lines DROP CONSTRAINT IF EXISTS cleaning_inspection_lines_cleaning_inspection_id_service_area_i;
ALTER TABLE IF EXISTS ONLY public.cache DROP CONSTRAINT IF EXISTS cache_pkey;
ALTER TABLE IF EXISTS ONLY public.cache_locks DROP CONSTRAINT IF EXISTS cache_locks_pkey;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_pkey;
ALTER TABLE IF EXISTS ONLY public.business_trips DROP CONSTRAINT IF EXISTS business_trips_code_unique;
ALTER TABLE IF EXISTS ONLY public.business_trip_participants DROP CONSTRAINT IF EXISTS business_trip_participants_unik;
ALTER TABLE IF EXISTS ONLY public.business_trip_participants DROP CONSTRAINT IF EXISTS business_trip_participants_pkey;
ALTER TABLE IF EXISTS ONLY public.business_trip_expenses DROP CONSTRAINT IF EXISTS business_trip_expenses_pkey;
ALTER TABLE IF EXISTS ONLY public.budgets DROP CONSTRAINT IF EXISTS budgets_unik;
ALTER TABLE IF EXISTS ONLY public.budgets DROP CONSTRAINT IF EXISTS budgets_pkey;
ALTER TABLE IF EXISTS ONLY public.audit_logs DROP CONSTRAINT IF EXISTS audit_logs_pkey;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_pkey;
ALTER TABLE IF EXISTS ONLY public.assets DROP CONSTRAINT IF EXISTS assets_code_unique;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_pkey;
ALTER TABLE IF EXISTS ONLY public.asset_transfers DROP CONSTRAINT IF EXISTS asset_transfers_code_unique;
ALTER TABLE IF EXISTS ONLY public.asset_documents DROP CONSTRAINT IF EXISTS asset_documents_pkey;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_pkey;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_code_unique;
ALTER TABLE IF EXISTS ONLY public.asset_disposals DROP CONSTRAINT IF EXISTS asset_disposals_asset_id_unique;
ALTER TABLE IF EXISTS ONLY public.asset_categories DROP CONSTRAINT IF EXISTS asset_categories_pkey;
ALTER TABLE IF EXISTS ONLY public.asset_categories DROP CONSTRAINT IF EXISTS asset_categories_code_unique;
ALTER TABLE IF EXISTS public.work_orders ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.work_order_attachments ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vendors ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vendor_bills ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vendor_bill_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicles ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicle_trips ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicle_refuelings ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicle_photos ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicle_documents ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.vehicle_bookings ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_transactions ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_requests ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_request_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_receipts ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_receipt_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_purchases ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_purchase_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_opnames ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_opname_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.supply_items ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.stock_opnames ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.stock_opname_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.settings ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.service_staff ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.service_requests ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.service_request_categories ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.service_request_attachments ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.service_areas ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.security_shifts ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.roles ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.reimbursements ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.reimbursement_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.permissions ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.parcel_shipments ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.number_sequences ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.number_sequence_periods ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.modules ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.maintenance_visits ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.maintenance_schedules ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.locations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.letters ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.incident_reports ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.failed_jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.expense_categories ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.employees ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.depreciation_periods ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.depreciation_entries ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.departments ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.cleaning_inspections ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.cleaning_inspection_lines ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.business_trips ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.business_trip_participants ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.business_trip_expenses ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.budgets ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.audit_logs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.assets ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.asset_transfers ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.asset_documents ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.asset_disposals ALTER COLUMN id DROP DEFAULT;
ALTER TABLE IF EXISTS public.asset_categories ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE IF EXISTS public.work_orders_id_seq;
DROP TABLE IF EXISTS public.work_orders;
DROP SEQUENCE IF EXISTS public.work_order_attachments_id_seq;
DROP TABLE IF EXISTS public.work_order_attachments;
DROP SEQUENCE IF EXISTS public.vendors_id_seq;
DROP TABLE IF EXISTS public.vendors;
DROP SEQUENCE IF EXISTS public.vendor_bills_id_seq;
DROP TABLE IF EXISTS public.vendor_bills;
DROP SEQUENCE IF EXISTS public.vendor_bill_lines_id_seq;
DROP TABLE IF EXISTS public.vendor_bill_lines;
DROP SEQUENCE IF EXISTS public.vehicles_id_seq;
DROP TABLE IF EXISTS public.vehicles;
DROP SEQUENCE IF EXISTS public.vehicle_trips_id_seq;
DROP TABLE IF EXISTS public.vehicle_trips;
DROP SEQUENCE IF EXISTS public.vehicle_refuelings_id_seq;
DROP TABLE IF EXISTS public.vehicle_refuelings;
DROP SEQUENCE IF EXISTS public.vehicle_photos_id_seq;
DROP TABLE IF EXISTS public.vehicle_photos;
DROP SEQUENCE IF EXISTS public.vehicle_documents_id_seq;
DROP TABLE IF EXISTS public.vehicle_documents;
DROP SEQUENCE IF EXISTS public.vehicle_bookings_id_seq;
DROP TABLE IF EXISTS public.vehicle_bookings;
DROP SEQUENCE IF EXISTS public.users_id_seq;
DROP TABLE IF EXISTS public.users;
DROP SEQUENCE IF EXISTS public.supply_transactions_id_seq;
DROP TABLE IF EXISTS public.supply_transactions;
DROP SEQUENCE IF EXISTS public.supply_requests_id_seq;
DROP TABLE IF EXISTS public.supply_requests;
DROP SEQUENCE IF EXISTS public.supply_request_lines_id_seq;
DROP TABLE IF EXISTS public.supply_request_lines;
DROP SEQUENCE IF EXISTS public.supply_receipts_id_seq;
DROP TABLE IF EXISTS public.supply_receipts;
DROP SEQUENCE IF EXISTS public.supply_receipt_lines_id_seq;
DROP TABLE IF EXISTS public.supply_receipt_lines;
DROP SEQUENCE IF EXISTS public.supply_purchases_id_seq;
DROP TABLE IF EXISTS public.supply_purchases;
DROP SEQUENCE IF EXISTS public.supply_purchase_lines_id_seq;
DROP TABLE IF EXISTS public.supply_purchase_lines;
DROP SEQUENCE IF EXISTS public.supply_opnames_id_seq;
DROP TABLE IF EXISTS public.supply_opnames;
DROP SEQUENCE IF EXISTS public.supply_opname_lines_id_seq;
DROP TABLE IF EXISTS public.supply_opname_lines;
DROP SEQUENCE IF EXISTS public.supply_items_id_seq;
DROP TABLE IF EXISTS public.supply_items;
DROP SEQUENCE IF EXISTS public.stock_opnames_id_seq;
DROP TABLE IF EXISTS public.stock_opnames;
DROP SEQUENCE IF EXISTS public.stock_opname_lines_id_seq;
DROP TABLE IF EXISTS public.stock_opname_lines;
DROP SEQUENCE IF EXISTS public.settings_id_seq;
DROP TABLE IF EXISTS public.settings;
DROP TABLE IF EXISTS public.sessions;
DROP SEQUENCE IF EXISTS public.service_staff_id_seq;
DROP TABLE IF EXISTS public.service_staff;
DROP SEQUENCE IF EXISTS public.service_requests_id_seq;
DROP TABLE IF EXISTS public.service_requests;
DROP SEQUENCE IF EXISTS public.service_request_categories_id_seq;
DROP TABLE IF EXISTS public.service_request_categories;
DROP SEQUENCE IF EXISTS public.service_request_attachments_id_seq;
DROP TABLE IF EXISTS public.service_request_attachments;
DROP SEQUENCE IF EXISTS public.service_areas_id_seq;
DROP TABLE IF EXISTS public.service_areas;
DROP SEQUENCE IF EXISTS public.security_shifts_id_seq;
DROP TABLE IF EXISTS public.security_shifts;
DROP SEQUENCE IF EXISTS public.roles_id_seq;
DROP TABLE IF EXISTS public.roles;
DROP TABLE IF EXISTS public.role_user;
DROP SEQUENCE IF EXISTS public.reimbursements_id_seq;
DROP TABLE IF EXISTS public.reimbursements;
DROP SEQUENCE IF EXISTS public.reimbursement_lines_id_seq;
DROP TABLE IF EXISTS public.reimbursement_lines;
DROP SEQUENCE IF EXISTS public.permissions_id_seq;
DROP TABLE IF EXISTS public.permissions;
DROP TABLE IF EXISTS public.permission_user;
DROP TABLE IF EXISTS public.permission_role;
DROP TABLE IF EXISTS public.password_reset_tokens;
DROP SEQUENCE IF EXISTS public.parcel_shipments_id_seq;
DROP TABLE IF EXISTS public.parcel_shipments;
DROP SEQUENCE IF EXISTS public.number_sequences_id_seq;
DROP TABLE IF EXISTS public.number_sequences;
DROP SEQUENCE IF EXISTS public.number_sequence_periods_id_seq;
DROP TABLE IF EXISTS public.number_sequence_periods;
DROP SEQUENCE IF EXISTS public.modules_id_seq;
DROP TABLE IF EXISTS public.modules;
DROP SEQUENCE IF EXISTS public.migrations_id_seq;
DROP TABLE IF EXISTS public.migrations;
DROP SEQUENCE IF EXISTS public.maintenance_visits_id_seq;
DROP TABLE IF EXISTS public.maintenance_visits;
DROP SEQUENCE IF EXISTS public.maintenance_schedules_id_seq;
DROP TABLE IF EXISTS public.maintenance_schedules;
DROP SEQUENCE IF EXISTS public.locations_id_seq;
DROP TABLE IF EXISTS public.locations;
DROP SEQUENCE IF EXISTS public.letters_id_seq;
DROP TABLE IF EXISTS public.letters;
DROP SEQUENCE IF EXISTS public.jobs_id_seq;
DROP TABLE IF EXISTS public.jobs;
DROP TABLE IF EXISTS public.job_batches;
DROP SEQUENCE IF EXISTS public.incident_reports_id_seq;
DROP TABLE IF EXISTS public.incident_reports;
DROP SEQUENCE IF EXISTS public.failed_jobs_id_seq;
DROP TABLE IF EXISTS public.failed_jobs;
DROP SEQUENCE IF EXISTS public.expense_categories_id_seq;
DROP TABLE IF EXISTS public.expense_categories;
DROP SEQUENCE IF EXISTS public.employees_id_seq;
DROP TABLE IF EXISTS public.employees;
DROP SEQUENCE IF EXISTS public.depreciation_periods_id_seq;
DROP TABLE IF EXISTS public.depreciation_periods;
DROP SEQUENCE IF EXISTS public.depreciation_entries_id_seq;
DROP TABLE IF EXISTS public.depreciation_entries;
DROP SEQUENCE IF EXISTS public.departments_id_seq;
DROP TABLE IF EXISTS public.departments;
DROP SEQUENCE IF EXISTS public.cleaning_inspections_id_seq;
DROP TABLE IF EXISTS public.cleaning_inspections;
DROP SEQUENCE IF EXISTS public.cleaning_inspection_lines_id_seq;
DROP TABLE IF EXISTS public.cleaning_inspection_lines;
DROP TABLE IF EXISTS public.cache_locks;
DROP TABLE IF EXISTS public.cache;
DROP SEQUENCE IF EXISTS public.business_trips_id_seq;
DROP TABLE IF EXISTS public.business_trips;
DROP SEQUENCE IF EXISTS public.business_trip_participants_id_seq;
DROP TABLE IF EXISTS public.business_trip_participants;
DROP SEQUENCE IF EXISTS public.business_trip_expenses_id_seq;
DROP TABLE IF EXISTS public.business_trip_expenses;
DROP SEQUENCE IF EXISTS public.budgets_id_seq;
DROP TABLE IF EXISTS public.budgets;
DROP SEQUENCE IF EXISTS public.audit_logs_id_seq;
DROP TABLE IF EXISTS public.audit_logs;
DROP SEQUENCE IF EXISTS public.assets_id_seq;
DROP TABLE IF EXISTS public.assets;
DROP SEQUENCE IF EXISTS public.asset_transfers_id_seq;
DROP TABLE IF EXISTS public.asset_transfers;
DROP SEQUENCE IF EXISTS public.asset_documents_id_seq;
DROP TABLE IF EXISTS public.asset_documents;
DROP SEQUENCE IF EXISTS public.asset_disposals_id_seq;
DROP TABLE IF EXISTS public.asset_disposals;
DROP SEQUENCE IF EXISTS public.asset_categories_id_seq;
DROP TABLE IF EXISTS public.asset_categories;
SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: asset_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.asset_categories (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    description character varying(255),
    useful_life_months integer,
    depreciation_method character varying(30) DEFAULT 'garis_lurus'::character varying NOT NULL,
    residual_percent numeric(5,2) DEFAULT '0'::numeric NOT NULL,
    account_asset character varying(30),
    account_accumulated character varying(30),
    account_expense character varying(30),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tax_group character varying(30),
    requires_certificate boolean DEFAULT false NOT NULL
);


--
-- Name: asset_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.asset_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: asset_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.asset_categories_id_seq OWNED BY public.asset_categories.id;


--
-- Name: asset_disposals; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.asset_disposals (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    asset_id bigint NOT NULL,
    disposal_date date NOT NULL,
    method character varying(30) NOT NULL,
    proceeds numeric(18,2),
    counterparty character varying(150),
    reference character varying(100),
    previous_status character varying(20),
    approved_by_employee_id bigint,
    document_path character varying(255),
    reason text,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: asset_disposals_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.asset_disposals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: asset_disposals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.asset_disposals_id_seq OWNED BY public.asset_disposals.id;


--
-- Name: asset_documents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.asset_documents (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    type character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    file_path character varying(255) NOT NULL,
    original_name character varying(255),
    size_bytes bigint,
    notes text,
    uploaded_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: asset_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.asset_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: asset_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.asset_documents_id_seq OWNED BY public.asset_documents.id;


--
-- Name: asset_transfers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.asset_transfers (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    asset_id bigint NOT NULL,
    transfer_date date NOT NULL,
    reason character varying(30) NOT NULL,
    from_location_id bigint,
    from_custodian_employee_id bigint,
    from_department_id bigint,
    to_location_id bigint,
    to_custodian_employee_id bigint,
    to_department_id bigint,
    handed_over_by_employee_id bigint,
    received_by_employee_id bigint,
    reference character varying(100),
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: asset_transfers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.asset_transfers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: asset_transfers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.asset_transfers_id_seq OWNED BY public.asset_transfers.id;


--
-- Name: assets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.assets (
    id bigint NOT NULL,
    code character varying(80) NOT NULL,
    name character varying(200) NOT NULL,
    asset_category_id bigint NOT NULL,
    location_id bigint,
    custodian_employee_id bigint,
    department_id bigint,
    asset_type character varying(20) DEFAULT 'bergerak'::character varying NOT NULL,
    brand character varying(100),
    model character varying(100),
    serial_number character varying(100),
    acquisition_date date,
    acquisition_source character varying(30) DEFAULT 'pembelian'::character varying NOT NULL,
    acquisition_cost numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    residual_value numeric(18,2),
    useful_life_months integer,
    depreciation_method character varying(30),
    status character varying(20) DEFAULT 'aktif'::character varying NOT NULL,
    condition character varying(20) DEFAULT 'baik'::character varying NOT NULL,
    warranty_until date,
    notes text,
    photo_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    ownership_type character varying(20) DEFAULT 'milik'::character varying NOT NULL,
    lease_start_date date,
    lease_end_date date,
    lessor character varying(150),
    lease_contract_number character varying(100),
    acquisition_condition character varying(20),
    project_name character varying(150),
    has_warranty boolean DEFAULT false NOT NULL,
    warranty_from date,
    land_certificate_number character varying(100),
    building_certificate_number character varying(100),
    opening_accumulated_depreciation numeric(18,2),
    opening_depreciation_note text
);


--
-- Name: assets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.assets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: assets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.assets_id_seq OWNED BY public.assets.id;


--
-- Name: audit_logs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.audit_logs (
    id bigint NOT NULL,
    user_id bigint,
    user_name character varying(150),
    event character varying(30) NOT NULL,
    auditable_type character varying(150),
    auditable_id bigint,
    auditable_label character varying(200),
    module_code character varying(50),
    old_values json,
    new_values json,
    url character varying(255),
    ip_address character varying(45),
    user_agent character varying(255),
    created_at timestamp(0) without time zone
);


--
-- Name: audit_logs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.audit_logs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: audit_logs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.audit_logs_id_seq OWNED BY public.audit_logs.id;


--
-- Name: budgets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.budgets (
    id bigint NOT NULL,
    department_id bigint NOT NULL,
    expense_category_id bigint NOT NULL,
    fiscal_year smallint NOT NULL,
    amount numeric(15,2) NOT NULL,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: budgets_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.budgets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: budgets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.budgets_id_seq OWNED BY public.budgets.id;


--
-- Name: business_trip_expenses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.business_trip_expenses (
    id bigint NOT NULL,
    business_trip_id bigint NOT NULL,
    expense_date date NOT NULL,
    category character varying(20) DEFAULT 'lainnya'::character varying NOT NULL,
    description character varying(255) NOT NULL,
    amount numeric(15,2) NOT NULL,
    file_path character varying(255),
    original_name character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: business_trip_expenses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.business_trip_expenses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: business_trip_expenses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.business_trip_expenses_id_seq OWNED BY public.business_trip_expenses.id;


--
-- Name: business_trip_participants; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.business_trip_participants (
    id bigint NOT NULL,
    business_trip_id bigint NOT NULL,
    employee_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: business_trip_participants_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.business_trip_participants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: business_trip_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.business_trip_participants_id_seq OWNED BY public.business_trip_participants.id;


--
-- Name: business_trips; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.business_trips (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    employee_id bigint NOT NULL,
    department_id bigint NOT NULL,
    destination character varying(200) NOT NULL,
    purpose text NOT NULL,
    start_date date NOT NULL,
    end_date date NOT NULL,
    transport_mode character varying(20) DEFAULT 'darat_umum'::character varying NOT NULL,
    estimated_cost numeric(15,2),
    status character varying(30) DEFAULT 'diajukan'::character varying NOT NULL,
    submitted_at timestamp(0) without time zone,
    approver_employee_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason text,
    rejection_reason text,
    advance_amount numeric(15,2),
    advance_paid_at timestamp(0) without time zone,
    advance_paid_by_user_id bigint,
    advance_note character varying(255),
    reported_at timestamp(0) without time zone,
    settled_at timestamp(0) without time zone,
    settled_by_user_id bigint,
    settlement_note text,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: business_trips_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.business_trips_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: business_trips_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.business_trips_id_seq OWNED BY public.business_trips.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cleaning_inspection_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cleaning_inspection_lines (
    id bigint NOT NULL,
    cleaning_inspection_id bigint NOT NULL,
    service_area_id bigint,
    area_code character varying(30) NOT NULL,
    area_name character varying(150) NOT NULL,
    staff_name character varying(150),
    result character varying(20),
    checked boolean DEFAULT false NOT NULL,
    notes text,
    file_path character varying(255),
    original_name character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: cleaning_inspection_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cleaning_inspection_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cleaning_inspection_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cleaning_inspection_lines_id_seq OWNED BY public.cleaning_inspection_lines.id;


--
-- Name: cleaning_inspections; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cleaning_inspections (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    inspection_date date NOT NULL,
    scope_category character varying(30),
    scope_frequency character varying(20),
    scope_location_id bigint,
    status character varying(20) DEFAULT 'berjalan'::character varying NOT NULL,
    finished_at timestamp(0) without time zone,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: cleaning_inspections_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cleaning_inspections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cleaning_inspections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cleaning_inspections_id_seq OWNED BY public.cleaning_inspections.id;


--
-- Name: departments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.departments (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    cost_center character varying(50),
    parent_id bigint,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    head_employee_id bigint
);


--
-- Name: departments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.departments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: departments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.departments_id_seq OWNED BY public.departments.id;


--
-- Name: depreciation_entries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.depreciation_entries (
    id bigint NOT NULL,
    depreciation_period_id bigint NOT NULL,
    asset_id bigint NOT NULL,
    period character(7) NOT NULL,
    expense numeric(18,2) NOT NULL,
    accumulated_after numeric(18,2) NOT NULL,
    book_value_after numeric(18,2) NOT NULL,
    method character varying(30) NOT NULL,
    useful_life_months integer NOT NULL,
    acquisition_cost numeric(18,2) NOT NULL,
    residual_value numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    months_covered integer DEFAULT 1 NOT NULL,
    covers_from character(7),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: depreciation_entries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.depreciation_entries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: depreciation_entries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.depreciation_entries_id_seq OWNED BY public.depreciation_entries.id;


--
-- Name: depreciation_periods; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.depreciation_periods (
    id bigint NOT NULL,
    period character(7) NOT NULL,
    closed_at timestamp(0) without time zone NOT NULL,
    closed_by_user_id bigint,
    total_expense numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    asset_count integer DEFAULT 0 NOT NULL,
    catch_up_count integer DEFAULT 0 NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: depreciation_periods_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.depreciation_periods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: depreciation_periods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.depreciation_periods_id_seq OWNED BY public.depreciation_periods.id;


--
-- Name: employees; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.employees (
    id bigint NOT NULL,
    nip character varying(30) NOT NULL,
    full_name character varying(150) NOT NULL,
    email character varying(150),
    phone character varying(30),
    department_id bigint,
    "position" character varying(100),
    employment_status character varying(20) DEFAULT 'tetap'::character varying NOT NULL,
    join_date date,
    user_id bigint,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: employees_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.employees_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: employees_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.employees_id_seq OWNED BY public.employees.id;


--
-- Name: expense_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.expense_categories (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    source character varying(30) DEFAULT 'manual'::character varying NOT NULL,
    account_code character varying(30),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: expense_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.expense_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: expense_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.expense_categories_id_seq OWNED BY public.expense_categories.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: incident_reports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.incident_reports (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    occurred_at timestamp(0) without time zone NOT NULL,
    category character varying(30) NOT NULL,
    severity character varying(20) DEFAULT 'sedang'::character varying NOT NULL,
    location_id bigint,
    security_shift_id bigint,
    reported_by_staff_id bigint,
    description text NOT NULL,
    service_request_id bigint,
    closed_at timestamp(0) without time zone,
    closing_note text,
    closed_by_user_id bigint,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: incident_reports_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.incident_reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: incident_reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.incident_reports_id_seq OWNED BY public.incident_reports.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: letters; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.letters (
    id bigint NOT NULL,
    direction character varying(10) NOT NULL,
    code character varying(40) NOT NULL,
    letter_number character varying(100),
    letter_date date NOT NULL,
    logged_date date NOT NULL,
    category character varying(30) DEFAULT 'lainnya'::character varying NOT NULL,
    counterparty character varying(200) NOT NULL,
    subject character varying(255) NOT NULL,
    notes text,
    assigned_employee_id bigint,
    handed_over_at timestamp(0) without time zone,
    handed_over_to_employee_id bigint,
    handover_note text,
    signer_employee_id bigint,
    file_path character varying(255),
    original_name character varying(255),
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: letters_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.letters_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: letters_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.letters_id_seq OWNED BY public.letters.id;


--
-- Name: locations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.locations (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    type character varying(20) DEFAULT 'ruangan'::character varying NOT NULL,
    parent_id bigint,
    description character varying(255),
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: locations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: locations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.locations_id_seq OWNED BY public.locations.id;


--
-- Name: maintenance_schedules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.maintenance_schedules (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    name character varying(200) NOT NULL,
    tasks text,
    interval_months integer NOT NULL,
    last_done_date date,
    next_due_date date,
    vendor_id bigint,
    technician_employee_id bigint,
    estimated_cost numeric(18,2),
    is_active boolean DEFAULT true NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.maintenance_schedules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.maintenance_schedules_id_seq OWNED BY public.maintenance_schedules.id;


--
-- Name: maintenance_visits; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.maintenance_visits (
    id bigint NOT NULL,
    maintenance_schedule_id bigint NOT NULL,
    asset_id bigint NOT NULL,
    sequence integer DEFAULT 1 NOT NULL,
    due_date date NOT NULL,
    status character varying(20) DEFAULT 'dijadwalkan'::character varying NOT NULL,
    completed_date date,
    vendor_id bigint,
    technician_employee_id bigint,
    result text,
    cost numeric(18,2),
    skip_reason text,
    closed_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: maintenance_visits_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.maintenance_visits_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: maintenance_visits_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.maintenance_visits_id_seq OWNED BY public.maintenance_visits.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: modules; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.modules (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    name character varying(100) NOT NULL,
    description character varying(255),
    "group" character varying(50),
    icon character varying(80),
    sort integer DEFAULT 0 NOT NULL,
    available_actions json NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: modules_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.modules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: modules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.modules_id_seq OWNED BY public.modules.id;


--
-- Name: number_sequence_periods; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.number_sequence_periods (
    id bigint NOT NULL,
    number_sequence_id bigint NOT NULL,
    period character varying(10) DEFAULT ''::character varying NOT NULL,
    next_number integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: number_sequence_periods_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.number_sequence_periods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: number_sequence_periods_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.number_sequence_periods_id_seq OWNED BY public.number_sequence_periods.id;


--
-- Name: number_sequences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.number_sequences (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    name character varying(120) NOT NULL,
    prefix character varying(20) DEFAULT ''::character varying NOT NULL,
    period_format character varying(10) DEFAULT 'Ym'::character varying NOT NULL,
    padding smallint DEFAULT '4'::smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    separator character varying(3) DEFAULT '/'::character varying NOT NULL
);


--
-- Name: number_sequences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.number_sequences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: number_sequences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.number_sequences_id_seq OWNED BY public.number_sequences.id;


--
-- Name: parcel_shipments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.parcel_shipments (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    request_date date NOT NULL,
    requester_employee_id bigint,
    department_id bigint NOT NULL,
    recipient_name character varying(200) NOT NULL,
    recipient_address text NOT NULL,
    recipient_phone character varying(30),
    contents character varying(255) NOT NULL,
    weight_kg numeric(8,2),
    estimated_cost numeric(15,2),
    status character varying(20) DEFAULT 'diminta'::character varying NOT NULL,
    vendor_id bigint,
    tracking_number character varying(100),
    shipped_date date,
    shipping_cost numeric(15,2),
    insurance_cost numeric(15,2),
    packing_cost numeric(15,2),
    discount_amount numeric(15,2),
    tax_amount numeric(15,2),
    notes text,
    cancel_reason text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: parcel_shipments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.parcel_shipments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: parcel_shipments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.parcel_shipments_id_seq OWNED BY public.parcel_shipments.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: permission_role; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permission_role (
    role_id bigint NOT NULL,
    permission_id bigint NOT NULL
);


--
-- Name: permission_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permission_user (
    user_id bigint NOT NULL,
    permission_id bigint NOT NULL,
    granted boolean DEFAULT true NOT NULL
);


--
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    module_id bigint NOT NULL,
    action character varying(30) NOT NULL,
    key character varying(90) NOT NULL,
    name character varying(120) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: reimbursement_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reimbursement_lines (
    id bigint NOT NULL,
    reimbursement_id bigint NOT NULL,
    expense_category_id bigint NOT NULL,
    expense_date date NOT NULL,
    description character varying(200) NOT NULL,
    amount numeric(15,2) NOT NULL,
    file_path character varying(255),
    original_name character varying(255),
    size_bytes bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: reimbursement_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reimbursement_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reimbursement_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reimbursement_lines_id_seq OWNED BY public.reimbursement_lines.id;


--
-- Name: reimbursements; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reimbursements (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    employee_id bigint NOT NULL,
    department_id bigint,
    title character varying(200) NOT NULL,
    notes text,
    status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    submitted_at timestamp(0) without time zone,
    approver_employee_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason text,
    verified_by_user_id bigint,
    verified_at timestamp(0) without time zone,
    rejection_reason text,
    rejected_stage character varying(20),
    paid_date date,
    payment_reference character varying(80),
    paid_by_user_id bigint,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: reimbursements_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reimbursements_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reimbursements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reimbursements_id_seq OWNED BY public.reimbursements.id;


--
-- Name: role_user; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_user (
    role_id bigint NOT NULL,
    user_id bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    name character varying(100) NOT NULL,
    description character varying(255),
    is_system boolean DEFAULT false NOT NULL,
    data_scope character varying(20) DEFAULT 'all'::character varying NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: security_shifts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.security_shifts (
    id bigint NOT NULL,
    shift_date date NOT NULL,
    shift character varying(20) NOT NULL,
    service_staff_id bigint NOT NULL,
    location_id bigint,
    attendance character varying(20) DEFAULT 'belum'::character varying NOT NULL,
    replacement_staff_id bigint,
    checked_in_at timestamp(0) without time zone,
    checked_out_at timestamp(0) without time zone,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: security_shifts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.security_shifts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: security_shifts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.security_shifts_id_seq OWNED BY public.security_shifts.id;


--
-- Name: service_areas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_areas (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    category character varying(30) DEFAULT 'lainnya'::character varying NOT NULL,
    location_id bigint,
    frequency character varying(20) DEFAULT 'harian'::character varying NOT NULL,
    service_staff_id bigint,
    notes text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_areas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_areas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_areas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_areas_id_seq OWNED BY public.service_areas.id;


--
-- Name: service_request_attachments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_request_attachments (
    id bigint NOT NULL,
    service_request_id bigint NOT NULL,
    name character varying(150) NOT NULL,
    file_path character varying(255) NOT NULL,
    original_name character varying(255),
    size_bytes bigint,
    uploaded_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_request_attachments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_request_attachments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_request_attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_request_attachments_id_seq OWNED BY public.service_request_attachments.id;


--
-- Name: service_request_categories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_request_categories (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    description text,
    default_priority character varying(20) DEFAULT 'normal'::character varying NOT NULL,
    sla_hours integer,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_request_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_request_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_request_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_request_categories_id_seq OWNED BY public.service_request_categories.id;


--
-- Name: service_requests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_requests (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    requester_employee_id bigint NOT NULL,
    department_id bigint,
    service_request_category_id bigint,
    location_id bigint,
    asset_id bigint,
    title character varying(200) NOT NULL,
    description text NOT NULL,
    priority character varying(20) DEFAULT 'normal'::character varying NOT NULL,
    status character varying(20) DEFAULT 'diajukan'::character varying NOT NULL,
    submitted_at timestamp(0) without time zone,
    approver_employee_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason character varying(200),
    accepted_by_user_id bigint,
    accepted_at timestamp(0) without time zone,
    sla_due_at timestamp(0) without time zone,
    work_order_id bigint,
    closed_at timestamp(0) without time zone,
    rejection_reason text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_requests_id_seq OWNED BY public.service_requests.id;


--
-- Name: service_staff; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.service_staff (
    id bigint NOT NULL,
    employee_id bigint,
    name character varying(150),
    vendor_id bigint,
    kind character varying(20) DEFAULT 'kebersihan'::character varying NOT NULL,
    phone character varying(30),
    start_date date,
    notes text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: service_staff_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.service_staff_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: service_staff_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.service_staff_id_seq OWNED BY public.service_staff.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: settings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.settings (
    id bigint NOT NULL,
    "group" character varying(50) DEFAULT 'umum'::character varying NOT NULL,
    key character varying(100) NOT NULL,
    value text,
    type character varying(20) DEFAULT 'text'::character varying NOT NULL,
    label character varying(150) NOT NULL,
    description text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: settings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.settings_id_seq OWNED BY public.settings.id;


--
-- Name: stock_opname_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_opname_lines (
    id bigint NOT NULL,
    stock_opname_id bigint NOT NULL,
    asset_id bigint NOT NULL,
    asset_code character varying(80) NOT NULL,
    asset_name character varying(200) NOT NULL,
    expected_location_id bigint,
    expected_condition character varying(20) NOT NULL,
    expected_status character varying(20) NOT NULL,
    checked boolean DEFAULT false NOT NULL,
    found boolean,
    found_location_id bigint,
    found_condition character varying(20),
    notes character varying(255),
    checked_at timestamp(0) without time zone,
    checked_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stock_opname_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_opname_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_opname_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_opname_lines_id_seq OWNED BY public.stock_opname_lines.id;


--
-- Name: stock_opnames; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.stock_opnames (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    name character varying(150) NOT NULL,
    scope_location_id bigint,
    scope_department_id bigint,
    scope_asset_category_id bigint,
    status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    started_at timestamp(0) without time zone,
    started_by_user_id bigint,
    finished_at timestamp(0) without time zone,
    finished_by_user_id bigint,
    adjusted_at timestamp(0) without time zone,
    adjusted_by_user_id bigint,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: stock_opnames_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.stock_opnames_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: stock_opnames_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.stock_opnames_id_seq OWNED BY public.stock_opnames.id;


--
-- Name: supply_items; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_items (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    category character varying(30) NOT NULL,
    unit character varying(20) NOT NULL,
    minimum_stock integer DEFAULT 0 NOT NULL,
    location_id bigint,
    last_price numeric(15,2),
    account_expense character varying(30),
    is_active boolean DEFAULT true NOT NULL,
    notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_items_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_items_id_seq OWNED BY public.supply_items.id;


--
-- Name: supply_opname_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_opname_lines (
    id bigint NOT NULL,
    supply_opname_id bigint NOT NULL,
    supply_item_id bigint NOT NULL,
    item_code character varying(50) NOT NULL,
    item_name character varying(150) NOT NULL,
    unit character varying(20) NOT NULL,
    system_quantity integer NOT NULL,
    counted_quantity integer,
    checked boolean DEFAULT false NOT NULL,
    notes character varying(200),
    supply_transaction_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_opname_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_opname_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_opname_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_opname_lines_id_seq OWNED BY public.supply_opname_lines.id;


--
-- Name: supply_opnames; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_opnames (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    name character varying(150) NOT NULL,
    scope_category character varying(30),
    scope_location_id bigint,
    status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    started_at timestamp(0) without time zone,
    finished_at timestamp(0) without time zone,
    adjusted_at timestamp(0) without time zone,
    adjusted_by_user_id bigint,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_opnames_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_opnames_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_opnames_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_opnames_id_seq OWNED BY public.supply_opnames.id;


--
-- Name: supply_purchase_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_purchase_lines (
    id bigint NOT NULL,
    supply_purchase_id bigint NOT NULL,
    supply_item_id bigint NOT NULL,
    quantity_ordered integer NOT NULL,
    unit_price numeric(15,2) NOT NULL,
    notes character varying(200),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_purchase_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_purchase_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_purchase_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_purchase_lines_id_seq OWNED BY public.supply_purchase_lines.id;


--
-- Name: supply_purchases; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_purchases (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    kind character varying(20) DEFAULT 'pesanan'::character varying NOT NULL,
    vendor_id bigint,
    supplier_name character varying(150),
    order_date date NOT NULL,
    expected_date date,
    description character varying(200) NOT NULL,
    notes text,
    status character varying(25) DEFAULT 'draft'::character varying NOT NULL,
    submitted_at timestamp(0) without time zone,
    approved_by_user_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason text,
    rejection_reason text,
    closed_at timestamp(0) without time zone,
    closing_reason text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_purchases_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_purchases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_purchases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_purchases_id_seq OWNED BY public.supply_purchases.id;


--
-- Name: supply_receipt_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_receipt_lines (
    id bigint NOT NULL,
    supply_receipt_id bigint NOT NULL,
    supply_purchase_line_id bigint NOT NULL,
    quantity integer NOT NULL,
    supply_transaction_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_receipt_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_receipt_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_receipt_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_receipt_lines_id_seq OWNED BY public.supply_receipt_lines.id;


--
-- Name: supply_receipts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_receipts (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    supply_purchase_id bigint NOT NULL,
    receipt_date date NOT NULL,
    delivery_note_number character varying(80),
    notes text,
    received_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_receipts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_receipts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_receipts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_receipts_id_seq OWNED BY public.supply_receipts.id;


--
-- Name: supply_request_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_request_lines (
    id bigint NOT NULL,
    supply_request_id bigint NOT NULL,
    supply_item_id bigint NOT NULL,
    quantity_requested integer NOT NULL,
    quantity_issued integer,
    notes character varying(200),
    supply_transaction_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_request_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_request_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_request_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_request_lines_id_seq OWNED BY public.supply_request_lines.id;


--
-- Name: supply_requests; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_requests (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    employee_id bigint NOT NULL,
    department_id bigint NOT NULL,
    purpose character varying(200) NOT NULL,
    needed_date date,
    notes text,
    status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    submitted_at timestamp(0) without time zone,
    approver_employee_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason text,
    issued_by_user_id bigint,
    issued_at timestamp(0) without time zone,
    issue_note text,
    rejection_reason text,
    rejected_stage character varying(20),
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_requests_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_requests_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_requests_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_requests_id_seq OWNED BY public.supply_requests.id;


--
-- Name: supply_transactions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.supply_transactions (
    id bigint NOT NULL,
    code character varying(50) NOT NULL,
    supply_item_id bigint NOT NULL,
    type character varying(20) NOT NULL,
    quantity integer NOT NULL,
    unit_price numeric(15,2),
    transaction_date date NOT NULL,
    department_id bigint,
    employee_id bigint,
    supplier character varying(150),
    reference character varying(100),
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: supply_transactions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.supply_transactions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: supply_transactions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.supply_transactions_id_seq OWNED BY public.supply_transactions.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_super_admin boolean DEFAULT false NOT NULL,
    is_active boolean DEFAULT true NOT NULL,
    last_login_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: vehicle_bookings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicle_bookings (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    requester_employee_id bigint NOT NULL,
    department_id bigint,
    destination character varying(200) NOT NULL,
    purpose text NOT NULL,
    passenger_count smallint,
    needs_driver boolean DEFAULT false NOT NULL,
    start_at timestamp(0) without time zone NOT NULL,
    end_at timestamp(0) without time zone NOT NULL,
    status character varying(20) DEFAULT 'diajukan'::character varying NOT NULL,
    vehicle_id bigint,
    driver_employee_id bigint,
    assigned_by_user_id bigint,
    assigned_at timestamp(0) without time zone,
    approver_employee_id bigint,
    approved_at timestamp(0) without time zone,
    approval_note text,
    approval_skipped_reason character varying(200),
    rejection_reason text,
    closed_at timestamp(0) without time zone,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicle_bookings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicle_bookings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicle_bookings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicle_bookings_id_seq OWNED BY public.vehicle_bookings.id;


--
-- Name: vehicle_documents; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicle_documents (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    type character varying(30) NOT NULL,
    document_number character varying(60),
    issued_date date,
    expires_at date NOT NULL,
    issuer character varying(120),
    cost numeric(15,2),
    file_path character varying(255),
    original_name character varying(255),
    size_bytes bigint,
    notes text,
    uploaded_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicle_documents_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicle_documents_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicle_documents_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicle_documents_id_seq OWNED BY public.vehicle_documents.id;


--
-- Name: vehicle_photos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicle_photos (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    type character varying(30) DEFAULT 'lainnya'::character varying NOT NULL,
    name character varying(150) NOT NULL,
    file_path character varying(255) NOT NULL,
    original_name character varying(255),
    size_bytes bigint,
    taken_date date,
    odometer_km integer,
    notes text,
    uploaded_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicle_photos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicle_photos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicle_photos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicle_photos_id_seq OWNED BY public.vehicle_photos.id;


--
-- Name: vehicle_refuelings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicle_refuelings (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    filled_at date NOT NULL,
    odometer_km integer NOT NULL,
    liters numeric(8,2) NOT NULL,
    cost numeric(15,2),
    station character varying(120),
    fuel_type character varying(20),
    is_full_tank boolean DEFAULT true NOT NULL,
    driver_employee_id bigint,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicle_refuelings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicle_refuelings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicle_refuelings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicle_refuelings_id_seq OWNED BY public.vehicle_refuelings.id;


--
-- Name: vehicle_trips; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicle_trips (
    id bigint NOT NULL,
    vehicle_id bigint NOT NULL,
    vehicle_booking_id bigint,
    driver_employee_id bigint,
    departed_at timestamp(0) without time zone NOT NULL,
    returned_at timestamp(0) without time zone,
    start_odometer_km integer NOT NULL,
    end_odometer_km integer,
    destination character varying(200) NOT NULL,
    purpose text,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicle_trips_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicle_trips_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicle_trips_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicle_trips_id_seq OWNED BY public.vehicle_trips.id;


--
-- Name: vehicles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vehicles (
    id bigint NOT NULL,
    asset_id bigint NOT NULL,
    plate_number character varying(20) NOT NULL,
    vehicle_type character varying(30) NOT NULL,
    usage_mode character varying(20) DEFAULT 'pool'::character varying NOT NULL,
    chassis_number character varying(50),
    engine_number character varying(50),
    color character varying(40),
    production_year smallint,
    fuel_type character varying(20),
    transmission character varying(20),
    seat_capacity smallint,
    payload_kg integer,
    default_driver_employee_id bigint,
    last_odometer_km integer,
    last_odometer_date date,
    notes text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vehicles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vehicles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vehicles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vehicles_id_seq OWNED BY public.vehicles.id;


--
-- Name: vendor_bill_lines; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor_bill_lines (
    id bigint NOT NULL,
    vendor_bill_id bigint NOT NULL,
    expense_category_id bigint NOT NULL,
    department_id bigint,
    description character varying(200),
    amount numeric(15,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vendor_bill_lines_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vendor_bill_lines_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vendor_bill_lines_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vendor_bill_lines_id_seq OWNED BY public.vendor_bill_lines.id;


--
-- Name: vendor_bills; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendor_bills (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    vendor_id bigint NOT NULL,
    invoice_number character varying(80),
    invoice_date date NOT NULL,
    due_date date,
    description text NOT NULL,
    status character varying(20) DEFAULT 'draft'::character varying NOT NULL,
    approved_by_user_id bigint,
    approved_at timestamp(0) without time zone,
    rejection_reason text,
    paid_date date,
    payment_reference character varying(80),
    paid_by_user_id bigint,
    file_path character varying(255),
    original_name character varying(255),
    size_bytes bigint,
    notes text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    supply_purchase_id bigint
);


--
-- Name: vendor_bills_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vendor_bills_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vendor_bills_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vendor_bills_id_seq OWNED BY public.vendor_bills.id;


--
-- Name: vendors; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.vendors (
    id bigint NOT NULL,
    code character varying(30) NOT NULL,
    name character varying(200) NOT NULL,
    type character varying(20) DEFAULT 'jasa'::character varying NOT NULL,
    specialization character varying(200),
    contact_person character varying(150),
    phone character varying(50),
    email character varying(150),
    address text,
    tax_number character varying(40),
    notes text,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: vendors_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.vendors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: vendors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.vendors_id_seq OWNED BY public.vendors.id;


--
-- Name: work_order_attachments; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.work_order_attachments (
    id bigint NOT NULL,
    work_order_id bigint NOT NULL,
    type character varying(30) NOT NULL,
    name character varying(150) NOT NULL,
    file_path character varying(255) NOT NULL,
    original_name character varying(255),
    size_bytes bigint,
    notes text,
    uploaded_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: work_order_attachments_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.work_order_attachments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: work_order_attachments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.work_order_attachments_id_seq OWNED BY public.work_order_attachments.id;


--
-- Name: work_orders; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.work_orders (
    id bigint NOT NULL,
    code character varying(40) NOT NULL,
    asset_id bigint,
    type character varying(20) DEFAULT 'korektif'::character varying NOT NULL,
    maintenance_schedule_id bigint,
    priority character varying(20) DEFAULT 'normal'::character varying NOT NULL,
    status character varying(20) DEFAULT 'dibuka'::character varying NOT NULL,
    reported_date date NOT NULL,
    reported_by_employee_id bigint,
    problem text NOT NULL,
    scheduled_date date,
    vendor_id bigint,
    technician_employee_id bigint,
    completed_date date,
    work_done text,
    cost numeric(18,2),
    condition_after character varying(30),
    cancel_reason text,
    created_by_user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    maintenance_visit_id bigint,
    service_request_id bigint
);


--
-- Name: work_orders_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.work_orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: work_orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.work_orders_id_seq OWNED BY public.work_orders.id;


--
-- Name: asset_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_categories ALTER COLUMN id SET DEFAULT nextval('public.asset_categories_id_seq'::regclass);


--
-- Name: asset_disposals id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals ALTER COLUMN id SET DEFAULT nextval('public.asset_disposals_id_seq'::regclass);


--
-- Name: asset_documents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_documents ALTER COLUMN id SET DEFAULT nextval('public.asset_documents_id_seq'::regclass);


--
-- Name: asset_transfers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers ALTER COLUMN id SET DEFAULT nextval('public.asset_transfers_id_seq'::regclass);


--
-- Name: assets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets ALTER COLUMN id SET DEFAULT nextval('public.assets_id_seq'::regclass);


--
-- Name: audit_logs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.audit_logs ALTER COLUMN id SET DEFAULT nextval('public.audit_logs_id_seq'::regclass);


--
-- Name: budgets id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets ALTER COLUMN id SET DEFAULT nextval('public.budgets_id_seq'::regclass);


--
-- Name: business_trip_expenses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_expenses ALTER COLUMN id SET DEFAULT nextval('public.business_trip_expenses_id_seq'::regclass);


--
-- Name: business_trip_participants id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_participants ALTER COLUMN id SET DEFAULT nextval('public.business_trip_participants_id_seq'::regclass);


--
-- Name: business_trips id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips ALTER COLUMN id SET DEFAULT nextval('public.business_trips_id_seq'::regclass);


--
-- Name: cleaning_inspection_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspection_lines ALTER COLUMN id SET DEFAULT nextval('public.cleaning_inspection_lines_id_seq'::regclass);


--
-- Name: cleaning_inspections id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspections ALTER COLUMN id SET DEFAULT nextval('public.cleaning_inspections_id_seq'::regclass);


--
-- Name: departments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments ALTER COLUMN id SET DEFAULT nextval('public.departments_id_seq'::regclass);


--
-- Name: depreciation_entries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_entries ALTER COLUMN id SET DEFAULT nextval('public.depreciation_entries_id_seq'::regclass);


--
-- Name: depreciation_periods id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_periods ALTER COLUMN id SET DEFAULT nextval('public.depreciation_periods_id_seq'::regclass);


--
-- Name: employees id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees ALTER COLUMN id SET DEFAULT nextval('public.employees_id_seq'::regclass);


--
-- Name: expense_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expense_categories ALTER COLUMN id SET DEFAULT nextval('public.expense_categories_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: incident_reports id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports ALTER COLUMN id SET DEFAULT nextval('public.incident_reports_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: letters id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters ALTER COLUMN id SET DEFAULT nextval('public.letters_id_seq'::regclass);


--
-- Name: locations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations ALTER COLUMN id SET DEFAULT nextval('public.locations_id_seq'::regclass);


--
-- Name: maintenance_schedules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_schedules ALTER COLUMN id SET DEFAULT nextval('public.maintenance_schedules_id_seq'::regclass);


--
-- Name: maintenance_visits id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits ALTER COLUMN id SET DEFAULT nextval('public.maintenance_visits_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: modules id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modules ALTER COLUMN id SET DEFAULT nextval('public.modules_id_seq'::regclass);


--
-- Name: number_sequence_periods id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequence_periods ALTER COLUMN id SET DEFAULT nextval('public.number_sequence_periods_id_seq'::regclass);


--
-- Name: number_sequences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequences ALTER COLUMN id SET DEFAULT nextval('public.number_sequences_id_seq'::regclass);


--
-- Name: parcel_shipments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments ALTER COLUMN id SET DEFAULT nextval('public.parcel_shipments_id_seq'::regclass);


--
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: reimbursement_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursement_lines ALTER COLUMN id SET DEFAULT nextval('public.reimbursement_lines_id_seq'::regclass);


--
-- Name: reimbursements id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements ALTER COLUMN id SET DEFAULT nextval('public.reimbursements_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: security_shifts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts ALTER COLUMN id SET DEFAULT nextval('public.security_shifts_id_seq'::regclass);


--
-- Name: service_areas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_areas ALTER COLUMN id SET DEFAULT nextval('public.service_areas_id_seq'::regclass);


--
-- Name: service_request_attachments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_attachments ALTER COLUMN id SET DEFAULT nextval('public.service_request_attachments_id_seq'::regclass);


--
-- Name: service_request_categories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_categories ALTER COLUMN id SET DEFAULT nextval('public.service_request_categories_id_seq'::regclass);


--
-- Name: service_requests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests ALTER COLUMN id SET DEFAULT nextval('public.service_requests_id_seq'::regclass);


--
-- Name: service_staff id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_staff ALTER COLUMN id SET DEFAULT nextval('public.service_staff_id_seq'::regclass);


--
-- Name: settings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings ALTER COLUMN id SET DEFAULT nextval('public.settings_id_seq'::regclass);


--
-- Name: stock_opname_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines ALTER COLUMN id SET DEFAULT nextval('public.stock_opname_lines_id_seq'::regclass);


--
-- Name: stock_opnames id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames ALTER COLUMN id SET DEFAULT nextval('public.stock_opnames_id_seq'::regclass);


--
-- Name: supply_items id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_items ALTER COLUMN id SET DEFAULT nextval('public.supply_items_id_seq'::regclass);


--
-- Name: supply_opname_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines ALTER COLUMN id SET DEFAULT nextval('public.supply_opname_lines_id_seq'::regclass);


--
-- Name: supply_opnames id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames ALTER COLUMN id SET DEFAULT nextval('public.supply_opnames_id_seq'::regclass);


--
-- Name: supply_purchase_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchase_lines ALTER COLUMN id SET DEFAULT nextval('public.supply_purchase_lines_id_seq'::regclass);


--
-- Name: supply_purchases id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases ALTER COLUMN id SET DEFAULT nextval('public.supply_purchases_id_seq'::regclass);


--
-- Name: supply_receipt_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines ALTER COLUMN id SET DEFAULT nextval('public.supply_receipt_lines_id_seq'::regclass);


--
-- Name: supply_receipts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipts ALTER COLUMN id SET DEFAULT nextval('public.supply_receipts_id_seq'::regclass);


--
-- Name: supply_request_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines ALTER COLUMN id SET DEFAULT nextval('public.supply_request_lines_id_seq'::regclass);


--
-- Name: supply_requests id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests ALTER COLUMN id SET DEFAULT nextval('public.supply_requests_id_seq'::regclass);


--
-- Name: supply_transactions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions ALTER COLUMN id SET DEFAULT nextval('public.supply_transactions_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: vehicle_bookings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings ALTER COLUMN id SET DEFAULT nextval('public.vehicle_bookings_id_seq'::regclass);


--
-- Name: vehicle_documents id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_documents ALTER COLUMN id SET DEFAULT nextval('public.vehicle_documents_id_seq'::regclass);


--
-- Name: vehicle_photos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_photos ALTER COLUMN id SET DEFAULT nextval('public.vehicle_photos_id_seq'::regclass);


--
-- Name: vehicle_refuelings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_refuelings ALTER COLUMN id SET DEFAULT nextval('public.vehicle_refuelings_id_seq'::regclass);


--
-- Name: vehicle_trips id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips ALTER COLUMN id SET DEFAULT nextval('public.vehicle_trips_id_seq'::regclass);


--
-- Name: vehicles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles ALTER COLUMN id SET DEFAULT nextval('public.vehicles_id_seq'::regclass);


--
-- Name: vendor_bill_lines id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bill_lines ALTER COLUMN id SET DEFAULT nextval('public.vendor_bill_lines_id_seq'::regclass);


--
-- Name: vendor_bills id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills ALTER COLUMN id SET DEFAULT nextval('public.vendor_bills_id_seq'::regclass);


--
-- Name: vendors id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors ALTER COLUMN id SET DEFAULT nextval('public.vendors_id_seq'::regclass);


--
-- Name: work_order_attachments id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_order_attachments ALTER COLUMN id SET DEFAULT nextval('public.work_order_attachments_id_seq'::regclass);


--
-- Name: work_orders id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders ALTER COLUMN id SET DEFAULT nextval('public.work_orders_id_seq'::regclass);


--
-- Data for Name: asset_categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.asset_categories (id, code, name, description, useful_life_months, depreciation_method, residual_percent, account_asset, account_accumulated, account_expense, is_active, created_at, updated_at, tax_group, requires_certificate) FROM stdin;
4	PRS	Prasarana dan instalasi	Instalasi listrik, air, jaringan, dan pekerjaan renovasi yang melekat pada bangunan. [COA CONTOH]	96	garis_lurus	0.00	1204	1304	6204	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_2	f
5	KR4	Kendaraan roda empat	Mobil operasional, bus, dan truk. [COA CONTOH]	96	garis_lurus	0.00	1205	1305	6205	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_2	f
6	KR2	Kendaraan roda dua	Sepeda motor operasional. [COA CONTOH]	48	garis_lurus	0.00	1206	1306	6206	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_1	f
8	PLG	Perabot kantor logam	Meja, kursi, lemari, dan filing cabinet berbahan logam yang bukan bagian bangunan. [COA CONTOH]	96	garis_lurus	0.00	1208	1308	6208	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_2	f
9	KOM	Komputer dan perangkat jaringan	Komputer, laptop, printer, pemindai, server, dan perangkat jaringan. [COA CONTOH]	48	garis_lurus	0.00	1209	1309	6209	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_1	f
10	MSK	Mesin dan peralatan kantor	Mesin fotokopi, mesin penghancur kertas, proyektor, dan peralatan kantor sejenis. [COA CONTOH]	48	garis_lurus	0.00	1210	1310	6210	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_1	f
11	ALK	Alat komunikasi	Pesawat telepon, faksimile, telepon seluler, dan radio komunikasi. [COA CONTOH]	48	garis_lurus	0.00	1211	1311	6211	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_1	f
12	PGU	Peralatan pengatur udara	AC, kipas angin, dan peralatan pengatur udara lainnya. [COA CONTOH]	96	garis_lurus	0.00	1212	1312	6212	t	2026-09-06 13:30:01	2026-09-06 13:34:03	kelompok_2	f
13	ADP	Aset dalam penyelesaian	Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan . [COA CONTOH]	\N	tidak_disusutkan	0.00	1213	\N	\N	t	2026-09-06 13:30:01	2026-09-06 13:44:28	tidak_disusutkan	f
7	PKY	Perabot kantor kayu	Meja, kursi, lemari, dan perabot berbahan kayu a. [COA CONTOH]	48	garis_lurus	0.00	1207	1307	6207	t	2026-09-06 13:30:01	2026-09-06 13:44:47	kelompok_1	f
1	TNH	Tanah	Tanah milik perusahaan. Tidak disusutkan. [COA CONTOH]	\N	tidak_disusutkan	0.00	1201	\N	\N	t	2026-09-06 13:30:01	2026-09-06 13:34:01	tidak_disusutkan	t
2	BGN	Bangunan permanen	Gedung kantor dan bangunan permanen lainnya. [COA CONTOH]	240	garis_lurus	0.00	1202	1302	6202	t	2026-09-06 13:30:01	2026-09-06 13:34:03	bangunan_permanen	t
3	BGT	Bangunan tidak permanen	Bangunan sementara, misalnya gudang semi permanen. [COA CONTOH]	120	garis_lurus	0.00	1203	1303	6203	t	2026-09-06 13:30:01	2026-09-06 13:34:03	bangunan_tidak_permanen	t
\.


--
-- Data for Name: asset_disposals; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.asset_disposals (id, code, asset_id, disposal_date, method, proceeds, counterparty, reference, previous_status, approved_by_employee_id, document_path, reason, notes, created_by_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: asset_documents; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.asset_documents (id, asset_id, type, name, file_path, original_name, size_bytes, notes, uploaded_by_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: asset_transfers; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.asset_transfers (id, code, asset_id, transfer_date, reason, from_location_id, from_custodian_employee_id, from_department_id, to_location_id, to_custodian_employee_id, to_department_id, handed_over_by_employee_id, received_by_employee_id, reference, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	MA/2026/09/0001	17	2026-09-06	mutasi_ruangan	16	\N	8	18	\N	\N	\N	1	BA/UJI/001	\N	2	2026-09-06 23:06:13	2026-09-06 23:06:13
\.


--
-- Data for Name: assets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.assets (id, code, name, asset_category_id, location_id, custodian_employee_id, department_id, asset_type, brand, model, serial_number, acquisition_date, acquisition_source, acquisition_cost, residual_value, useful_life_months, depreciation_method, status, condition, warranty_until, notes, photo_path, created_at, updated_at, ownership_type, lease_start_date, lease_end_date, lessor, lease_contract_number, acquisition_condition, project_name, has_warranty, warranty_from, land_certificate_number, building_certificate_number, opening_accumulated_depreciation, opening_depreciation_note) FROM stdin;
1	GA-1201-2017-0001	Tanah kantor pusat	1	4	\N	4	tetap	\N	\N	\N	2017-09-22	pembelian	12000000000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
31	FIN-1211-2019-0001	Mesin faksimile	11	6	9	5	bergerak	Polycom	Tipe 271	ALK-694647	2019-07-24	pembelian	6496000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
61	HRD-1211-2023-0001	Pesawat telepon meja	11	17	5	7	bergerak	Panasonic	Tipe 723	ALK-706164	2023-06-27	pembelian	4985000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
148	FIN-1205-2018-0001	Kendaraan dinas	5	21	11	5	bergerak	Toyota Avanza	Tipe 266	KR4-107175	2018-06-29	pembelian	187478000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
2	GA-1202-2012-0001	Gedung Head Office	2	4	\N	4	tetap	\N	\N	\N	2012-09-20	pembelian	24500000000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
3	OPS-1203-2018-0001	Gudang semi permanen belakang	3	4	\N	8	tetap	\N	\N	\N	2018-05-25	pembelian	640000000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
4	GA-1213-2014-0001	Renovasi ruang rapat lantai 3	13	4	\N	4	tetap	\N	\N	\N	2014-06-10	pembelian	185000000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
5	HRD-1212-2021-0001	AC standing 3 PK	12	9	13	7	bergerak	LG	Tipe 561	PGU-827629	2021-09-17	pembelian	6957000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
6	IT-1204-2022-0001	Pompa air gedung	4	10	\N	6	tetap	Panasonic	Tipe 237	PRS-684314	2022-03-12	pembelian	68789000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
7	OPS-1208-2024-0001	Lemari besi arsip	8	7	5	8	bergerak	Daichiban	Tipe 430	PLG-426261	2024-08-07	pembelian	19892000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
8	FIN-1212-2018-0001	Kipas angin dinding	12	12	7	5	bergerak	Panasonic	Tipe 382	PGU-223492	2018-06-07	pembelian	3036000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
9	OPS-1207-2020-0001	Kursi tamu kayu	7	12	3	8	bergerak	Activ	Tipe 692	PKY-330001	2020-04-24	pembelian	13642000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
10	GA-1207-2019-0001	Kursi tamu kayu	7	6	8	4	bergerak	Pro Design	Tipe 116	PKY-624518	2019-05-02	pembelian	3275000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
11	FIN-1210-2021-0001	Laminator	10	13	11	5	bergerak	GBC	Tipe 641	MSK-982430	2021-08-20	pembelian	27638000.00	\N	\N	\N	tidak_dipakai	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
12	HRD-1208-2022-0001	Rak arsip besi	8	12	8	7	bergerak	Daichiban	Tipe 439	PLG-722138	2022-06-16	pembelian	5422000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
13	GA-1208-2026-0001	Loker karyawan	8	9	7	4	bergerak	Daichiban	Tipe 961	PLG-525898	2026-10-22	pembelian	9866000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
149	HRD-1212-2024-0002	Kipas angin dinding	12	18	\N	7	bergerak	Sharp	Tipe 894	PGU-237925	2024-09-07	pembelian	25765000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
150	GA-1208-2026-0002	Loker karyawan	8	14	10	4	bergerak	Elite	Tipe 802	PLG-128629	2026-06-19	pembelian	13852000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
151	OPS-1206-2025-0001	Sepeda motor operasional	6	21	13	8	bergerak	Yamaha NMAX	Tipe 603	KR2-256462	2025-09-09	pembelian	31268000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
152	FIN-1211-2018-0002	Radio genggam	11	14	10	5	bergerak	Panasonic	Tipe 865	ALK-867609	2018-10-11	pembelian	4452000.00	\N	\N	\N	tidak_dipakai	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
153	OPS-1211-2019-0001	Pesawat telepon meja	11	9	6	8	bergerak	Yealink	Tipe 139	ALK-686785	2019-02-05	hibah	1104000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
14	HRD-1208-2018-0001	Lemari besi arsip	8	19	3	7	bergerak	Brother	Tipe 849	PLG-792089	2018-09-15	pembelian	10816000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
15	HRD-1209-2018-0001	Pemindai dokumen	9	8	5	7	bergerak	Cisco	Tipe 175	KOM-963255	2018-04-01	pembelian	27481000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
16	FIN-1206-2022-0001	Sepeda motor operasional	6	21	4	5	bergerak	Honda PCX	Tipe 993	KR2-175840	2022-02-20	pembelian	27623000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
18	IT-1206-2025-0001	Sepeda motor operasional	6	21	\N	6	bergerak	Honda PCX	Tipe 852	KR2-610152	2025-11-22	pembelian	19791000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
19	FIN-1208-2019-0001	Filing cabinet 4 laci	8	14	3	5	bergerak	Krisbow	Tipe 242	PLG-991496	2019-06-20	pembelian	8555000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
20	IT-1209-2024-0001	Printer laser	9	11	10	6	bergerak	Dell	Tipe 267	KOM-513380	2024-05-26	pembelian	23390000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
21	HRD-1212-2024-0001	AC split 2 PK	12	18	5	7	bergerak	Panasonic	Tipe 388	PGU-234000	2024-09-08	pembelian	2023000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
22	FIN-1209-2023-0001	PC desktop	9	9	7	5	bergerak	Lenovo	Tipe 235	KOM-544873	2023-03-13	pembelian	17570000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
23	GA-1209-2023-0001	PC desktop	9	19	12	4	bergerak	Acer	Tipe 144	KOM-421073	2023-09-06	pembelian	26641000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
24	OPS-1205-2019-0001	Kendaraan dinas	5	21	2	8	bergerak	Isuzu Elf	Tipe 548	KR4-767295	2019-06-28	pembelian	319339000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
25	HRD-1207-2023-0001	Meja kerja kayu	7	7	12	7	bergerak	Activ	Tipe 127	PKY-801625	2023-07-18	pembelian	8585000.00	\N	\N	\N	perbaikan	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
26	FIN-1212-2023-0001	AC split 1 PK	12	11	8	5	bergerak	Daikin	Tipe 808	PGU-427049	2023-08-09	pembelian	17949000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
27	IT-1210-2025-0001	Mesin fotokopi	10	12	12	6	bergerak	Fuji Xerox	Tipe 647	MSK-688346	2025-07-31	pembelian	52281000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
28	GA-1206-2018-0001	Sepeda motor operasional	6	21	11	4	bergerak	Honda PCX	Tipe 356	KR2-830764	2018-10-27	pembelian	30821000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
29	HRD-1207-2019-0001	Meja kerja kayu	7	17	3	7	bergerak	Olympic	Tipe 425	PKY-325720	2019-03-12	pembelian	17415000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
30	OPS-1210-2026-0001	Proyektor ruang rapat	10	19	3	8	bergerak	GBC	Tipe 883	MSK-930859	2026-06-28	pembelian	3185000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
33	HRD-1210-2024-0001	Mesin absensi sidik jari	10	11	4	7	bergerak	Epson	Tipe 695	MSK-440110	2024-05-28	pembelian	36526000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
34	FIN-1209-2018-0001	Laptop kerja	9	8	\N	5	bergerak	Cisco	Tipe 885	KOM-522986	2018-10-23	pembelian	15185000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
35	OPS-1205-2022-0001	Kendaraan dinas	5	21	6	8	bergerak	Isuzu Elf	Tipe 254	KR4-148050	2022-07-18	pembelian	387811000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 14:04:04	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
36	FIN-1206-2023-0001	Sepeda motor kurir	6	21	12	5	bergerak	Honda Vario	Tipe 718	KR2-800223	2023-11-17	pembelian	26437000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
37	OPS-1205-2022-0002	Mobil boks pengiriman	5	21	8	8	bergerak	Toyota Innova	Tipe 425	KR4-459351	2022-03-19	pembelian	292096000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
38	GA-1204-2022-0001	Pompa air gedung	4	5	\N	4	tetap	Appron	Tipe 888	PRS-943739	2022-05-28	pembelian	236797000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
39	OPS-1209-2021-0001	Access point	9	18	8	8	bergerak	Dell	Tipe 698	KOM-783047	2021-10-07	pembelian	8861000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
40	OPS-1206-2021-0001	Sepeda motor kurir	6	21	12	8	bergerak	Honda PCX	Tipe 788	KR2-348851	2021-07-19	pembelian	22225000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
41	FIN-1211-2018-0001	Pesawat telepon meja	11	13	13	5	bergerak	Yealink	Tipe 266	ALK-132893	2018-09-07	pembelian	2758000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
42	OPS-1204-2025-0001	Sistem pemadam kebakaran	4	5	\N	8	tetap	Grundfos	Tipe 227	PRS-887972	2025-01-06	pembelian	245625000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
43	OPS-1206-2023-0001	Sepeda motor operasional	6	21	6	8	bergerak	Honda Beat	Tipe 523	KR2-834076	2023-01-23	pembelian	27734000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
44	HRD-1211-2025-0001	Telepon konferensi	11	16	2	7	bergerak	Yealink	Tipe 380	ALK-293432	2025-02-19	pembelian	5683000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
45	GA-1207-2025-0001	Meja rapat kayu	7	16	10	4	bergerak	Ligna	Tipe 134	PKY-498278	2025-08-15	pembelian	12448000.00	\N	\N	\N	tidak_dipakai	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
46	OPS-1206-2020-0001	Sepeda motor kurir	6	21	4	8	bergerak	Honda PCX	Tipe 845	KR2-462373	2020-07-22	pembelian	20699000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
47	OPS-1212-2024-0001	AC standing 3 PK	12	11	\N	8	bergerak	LG	Tipe 619	PGU-670204	2024-11-13	pembelian	23418000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
48	HRD-1206-2023-0001	Sepeda motor kurir	6	21	8	7	bergerak	Honda Vario	Tipe 268	KR2-557701	2023-10-06	pembelian	21764000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
49	HRD-1205-2026-0001	Kendaraan operasional	5	21	10	7	bergerak	Isuzu Elf	Tipe 452	KR4-398925	2026-11-22	pembelian	446673000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
50	GA-1207-2026-0001	Meja rapat kayu	7	19	\N	4	bergerak	Activ	Tipe 108	PKY-767166	2026-08-20	hibah	3535000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
51	FIN-1211-2025-0001	Telepon konferensi	11	13	2	5	bergerak	Motorola	Tipe 732	ALK-170551	2025-03-23	pembelian	3776000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
52	FIN-1207-2026-0001	Rak buku kayu	7	6	10	5	bergerak	Pro Design	Tipe 565	PKY-650785	2026-04-05	pembelian	8540000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
53	FIN-1209-2026-0001	Access point	9	7	13	5	bergerak	TP-Link	Tipe 390	KOM-167194	2026-02-16	pembelian	7485000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
54	FIN-1212-2024-0001	Kipas angin dinding	12	7	5	5	bergerak	Mitsubishi	Tipe 598	PGU-930591	2024-01-14	pembelian	17403000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
55	FIN-1212-2024-0002	Exhaust fan	12	19	10	5	bergerak	LG	Tipe 494	PGU-632265	2024-06-13	pembelian	25802000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
56	FIN-1208-2018-0001	Lemari besi arsip	8	9	9	5	bergerak	Krisbow	Tipe 995	PLG-828408	2018-11-11	pembelian	5231000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
57	IT-1209-2021-0001	Pemindai dokumen	9	\N	\N	6	bergerak	Cisco	Tipe 345	KOM-158798	2021-06-24	pembelian	41454000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
58	HRD-1209-2026-0001	Monitor 24 inci	9	14	6	7	bergerak	Cisco	Tipe 610	KOM-928090	2026-05-11	pembelian	26774000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
59	HRD-1210-2025-0001	Mesin penghancur kertas	10	7	11	7	bergerak	Canon	Tipe 958	MSK-358713	2025-05-11	pembelian	38225000.00	\N	\N	\N	dipinjam	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
62	OPS-1209-2019-0001	Pemindai dokumen	9	9	10	8	bergerak	Lenovo	Tipe 372	KOM-530888	2019-02-27	pembelian	35038000.00	\N	\N	\N	perbaikan	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
63	FIN-1212-2021-0001	AC split 2 PK	12	8	4	5	bergerak	LG	Tipe 959	PGU-310633	2021-01-04	pembelian	20209000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
64	IT-1206-2021-0001	Sepeda motor operasional	6	21	2	6	bergerak	Honda PCX	Tipe 711	KR2-834965	2021-09-20	pembelian	24541000.00	\N	\N	\N	tidak_dipakai	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
65	OPS-1207-2025-0001	Meja rapat kayu	7	14	2	8	bergerak	Olympic	Tipe 872	PKY-316476	2025-07-17	pembelian	13003000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
66	OPS-1205-2025-0001	Kendaraan operasional	5	21	4	8	bergerak	Daihatsu Gran Max	Tipe 667	KR4-660854	2025-09-30	pembelian	376818000.00	\N	\N	\N	tidak_dipakai	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
67	IT-1205-2023-0001	Kendaraan operasional	5	21	5	6	bergerak	Toyota Avanza	Tipe 860	KR4-156097	2023-08-08	pembelian	282204000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
68	OPS-1208-2024-0002	Brankas	8	12	6	8	bergerak	Elite	Tipe 567	PLG-252336	2024-05-23	pembelian	11019000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
69	IT-1207-2021-0001	Kursi tamu kayu	7	9	10	6	bergerak	Pro Design	Tipe 940	PKY-937062	2021-06-19	pembelian	15456000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
70	HRD-1205-2018-0001	Mobil boks pengiriman	5	21	6	7	bergerak	Toyota Innova	Tipe 172	KR4-191112	2018-02-27	pembelian	503247000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
71	FIN-1210-2022-0001	Mesin absensi sidik jari	10	7	12	5	bergerak	Fuji Xerox	Tipe 937	MSK-651849	2022-09-11	pembelian	12468000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
73	GA-1211-2020-0001	Pesawat telepon meja	11	11	5	4	bergerak	Polycom	Tipe 703	ALK-561762	2020-11-17	pembelian	5490000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
74	GA-1207-2023-0001	Lemari arsip kayu	7	12	13	4	bergerak	Olympic	Tipe 762	PKY-137580	2023-05-02	pembelian	9702000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
75	GA-1209-2025-0001	Switch 24 port	9	13	\N	4	bergerak	Acer	Tipe 780	KOM-419837	2025-10-22	pembelian	32238000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
76	HRD-1207-2023-0002	Lemari arsip kayu	7	16	11	7	bergerak	Ligna	Tipe 929	PKY-965451	2023-06-09	pembelian	4699000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
77	HRD-1205-2026-0002	Kendaraan dinas	5	21	12	7	bergerak	Isuzu Elf	Tipe 372	KR4-319485	2026-06-16	pembelian	472518000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
78	FIN-1212-2021-0002	Kipas angin dinding	12	7	9	5	bergerak	Sharp	Tipe 933	PGU-887969	2021-04-28	pembelian	7123000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
79	FIN-1209-2018-0002	PC desktop	9	16	13	5	bergerak	Acer	Tipe 712	KOM-718969	2018-10-04	pembelian	27586000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
80	OPS-1207-2018-0001	Meja rapat kayu	7	12	9	8	bergerak	Activ	Tipe 153	PKY-891824	2018-09-26	pembelian	15810000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
81	GA-1209-2024-0001	Server rak	9	7	13	4	bergerak	Acer	Tipe 379	KOM-111109	2024-01-06	pembelian	16089000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
82	FIN-1210-2020-0001	Mesin fotokopi	10	11	2	5	bergerak	Kyocera	Tipe 119	MSK-857820	2020-07-21	pembelian	43978000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
83	OPS-1208-2018-0001	Loker karyawan	8	16	7	8	bergerak	Krisbow	Tipe 462	PLG-413843	2018-09-20	hibah	20011000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
84	FIN-1206-2021-0001	Sepeda motor kurir	6	21	3	5	bergerak	Honda Beat	Tipe 469	KR2-294603	2021-10-16	pembelian	24951000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
85	HRD-1207-2018-0001	Meja kerja kayu	7	7	3	7	bergerak	Olympic	Tipe 112	PKY-267850	2018-11-10	pembelian	15726000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
86	HRD-1209-2022-0001	Monitor 24 inci	9	12	3	7	bergerak	HP	Tipe 886	KOM-897682	2022-03-08	pembelian	16691000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
87	FIN-1207-2018-0001	Meja rapat kayu	7	12	7	5	bergerak	Olympic	Tipe 836	PKY-117415	2018-01-27	pembelian	12086000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
88	GA-1212-2026-0001	AC split 1 PK	12	14	9	4	bergerak	LG	Tipe 659	PGU-876104	2026-01-26	pembelian	18024000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
72	FIN-1209-2020-0001	Pemindai dokumen	9	17	2	5	bergerak	Acer	Tipe 490	KOM-990880	2020-08-23	pembelian	36753000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 15:52:50	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
89	OPS-1212-2025-0001	AC standing 3 PK	12	16	8	8	bergerak	Panasonic	Tipe 444	PGU-537274	2025-03-25	pembelian	15735000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
90	OPS-1206-2018-0001	Sepeda motor kurir	6	21	8	8	bergerak	Honda Vario	Tipe 535	KR2-184913	2018-06-13	pembelian	22759000.00	\N	\N	\N	dipinjam	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
91	GA-1206-2020-0001	Sepeda motor kurir	6	21	11	4	bergerak	Yamaha NMAX	Tipe 332	KR2-833744	2020-11-19	pembelian	33392000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
92	HRD-1208-2023-0001	Lemari besi arsip	8	19	10	7	bergerak	Elite	Tipe 416	PLG-241406	2023-06-24	pembelian	18854000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
93	OPS-1206-2023-0002	Sepeda motor kurir	6	21	12	8	bergerak	Yamaha NMAX	Tipe 983	KR2-768967	2023-08-09	hibah	25150000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
94	IT-1207-2022-0001	Meja rapat kayu	7	14	7	6	bergerak	Activ	Tipe 250	PKY-400314	2022-03-14	pembelian	11420000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
95	HRD-1208-2018-0002	Loker karyawan	8	\N	\N	7	bergerak	Daichiban	Tipe 567	PLG-734374	2018-06-02	pembelian	15045000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
96	FIN-1206-2023-0002	Sepeda motor kurir	6	21	8	5	bergerak	Honda Beat	Tipe 249	KR2-562739	2023-04-01	pembelian	20365000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
97	IT-1204-2021-0001	Instalasi listrik lantai	4	15	\N	6	tetap	Perkins	Tipe 482	PRS-497527	2021-09-22	pembelian	196345000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
98	HRD-1205-2018-0002	Kendaraan dinas	5	21	5	7	bergerak	Toyota Innova	Tipe 169	KR4-352102	2018-03-05	pembelian	222934000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
99	OPS-1207-2026-0001	Kursi tamu kayu	7	11	10	8	bergerak	Pro Design	Tipe 467	PKY-984682	2026-02-09	pembelian	9144000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
100	HRD-1212-2025-0001	Exhaust fan	12	16	\N	7	bergerak	Panasonic	Tipe 785	PGU-619877	2025-11-21	pembelian	9738000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
101	GA-1208-2021-0001	Lemari besi arsip	8	13	6	4	bergerak	Elite	Tipe 377	PLG-141414	2021-06-06	pembelian	3136000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
102	HRD-1206-2021-0001	Sepeda motor operasional	6	21	8	7	bergerak	Honda Vario	Tipe 445	KR2-490615	2021-05-03	pembelian	28638000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
103	OPS-1205-2023-0001	Kendaraan dinas	5	21	11	8	bergerak	Mitsubishi L300	Tipe 617	KR4-717224	2023-01-07	hibah	402814000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
104	HRD-1209-2021-0001	Laptop kerja	9	18	5	7	bergerak	Dell	Tipe 283	KOM-813131	2021-09-21	pembelian	18730000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
105	OPS-1211-2020-0001	Telepon konferensi	11	7	4	8	bergerak	Yealink	Tipe 613	ALK-873989	2020-07-04	pembelian	6586000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
106	IT-1207-2026-0001	Kursi tamu kayu	7	7	11	6	bergerak	Pro Design	Tipe 273	PKY-719670	2026-03-26	pembelian	9826000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
107	GA-1206-2021-0001	Sepeda motor operasional	6	\N	4	4	bergerak	Honda Vario	Tipe 764	KR2-710975	2021-09-10	pembelian	27260000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
108	HRD-1211-2020-0001	Mesin faksimile	11	12	\N	7	bergerak	Motorola	Tipe 892	ALK-762370	2020-08-17	pembelian	7173000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
109	OPS-1209-2020-0001	PC desktop	9	14	4	8	bergerak	Asus	Tipe 411	KOM-244761	2020-11-22	pembelian	5711000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
110	HRD-1206-2024-0001	Sepeda motor operasional	6	21	8	7	bergerak	Honda Vario	Tipe 359	KR2-601307	2024-04-05	pembelian	34385000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
111	IT-1211-2019-0001	Telepon konferensi	11	16	11	6	bergerak	Panasonic	Tipe 424	ALK-195922	2019-01-21	pembelian	3237000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
112	GA-1211-2018-0001	Pesawat telepon meja	11	17	6	4	bergerak	Motorola	Tipe 155	ALK-252726	2018-06-26	pembelian	2924000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
113	HRD-1206-2020-0001	Sepeda motor operasional	6	21	3	7	bergerak	Yamaha NMAX	Tipe 269	KR2-238035	2020-02-02	pembelian	29645000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
114	HRD-1212-2019-0001	AC split 1 PK	12	7	7	7	bergerak	LG	Tipe 806	PGU-576421	2019-01-14	pembelian	7810000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
115	GA-1208-2021-0002	Loker karyawan	8	7	6	4	bergerak	Daichiban	Tipe 809	PLG-869068	2021-03-21	pembelian	9659000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:05	2026-09-06 14:04:05	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
116	OPS-1209-2026-0001	Switch 24 port	9	7	8	8	bergerak	Asus	Tipe 503	KOM-948110	2026-07-08	pembelian	33213000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
117	GA-1207-2018-0001	Lemari arsip kayu	7	19	11	4	bergerak	Ligna	Tipe 645	PKY-432289	2018-11-14	pembelian	1044000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
118	HRD-1210-2018-0001	Mesin fotokopi	10	8	\N	7	bergerak	Kyocera	Tipe 625	MSK-922759	2018-08-24	pembelian	3986000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
119	GA-1206-2022-0001	Sepeda motor kurir	6	\N	12	4	bergerak	Yamaha NMAX	Tipe 636	KR2-818500	2022-08-01	pembelian	20672000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
120	IT-1207-2018-0001	Meja kerja kayu	7	\N	7	6	bergerak	Ligna	Tipe 143	PKY-764977	2018-03-10	pembelian	15575000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
121	GA-1209-2020-0001	Pemindai dokumen	9	16	11	4	bergerak	Asus	Tipe 716	KOM-854659	2020-06-28	pembelian	32116000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
122	GA-1205-2020-0001	Mobil boks pengiriman	5	21	4	4	bergerak	Mitsubishi L300	Tipe 784	KR4-719561	2020-08-29	pembelian	260015000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
123	OPS-1207-2023-0001	Rak buku kayu	7	17	3	8	bergerak	Activ	Tipe 267	PKY-888494	2023-03-12	pembelian	10317000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
124	IT-1204-2020-0001	Instalasi listrik lantai	4	15	\N	6	tetap	Schneider	Tipe 823	PRS-458938	2020-04-04	pembelian	465295000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
125	IT-1207-2026-0002	Kursi tamu kayu	7	\N	9	6	bergerak	Ligna	Tipe 695	PKY-993297	2026-09-04	pembelian	13150000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
126	OPS-1204-2023-0001	Sistem pemadam kebakaran	4	10	\N	8	tetap	Schneider	Tipe 813	PRS-277106	2023-01-18	pembelian	195257000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
127	GA-1209-2026-0001	Access point	9	16	3	4	bergerak	Acer	Tipe 982	KOM-944868	2026-04-10	pembelian	24819000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
128	FIN-1212-2024-0003	Kipas angin dinding	12	18	11	5	bergerak	Mitsubishi	Tipe 344	PGU-345963	2024-03-29	hibah	21406000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
129	FIN-1208-2020-0001	Rak arsip besi	8	6	5	5	bergerak	Elite	Tipe 318	PLG-656042	2020-08-29	pembelian	5606000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
130	FIN-1206-2020-0001	Sepeda motor kurir	6	21	2	5	bergerak	Honda PCX	Tipe 647	KR2-689535	2020-01-26	hibah	32948000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
131	GA-1208-2025-0001	Filing cabinet 4 laci	8	11	11	4	bergerak	Brother	Tipe 745	PLG-539789	2025-08-17	pembelian	11037000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
132	IT-1211-2026-0001	Pesawat telepon meja	11	12	11	6	bergerak	Yealink	Tipe 238	ALK-436159	2026-09-02	pembelian	7893000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
133	GA-1206-2020-0002	Sepeda motor kurir	6	21	7	4	bergerak	Honda Vario	Tipe 844	KR2-924619	2020-04-16	pembelian	19951000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
134	HRD-1211-2022-0001	Pesawat telepon meja	11	11	6	7	bergerak	Panasonic	Tipe 189	ALK-623926	2022-08-17	pembelian	6980000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
135	GA-1205-2023-0001	Kendaraan operasional	5	21	2	4	bergerak	Daihatsu Gran Max	Tipe 660	KR4-791067	2023-03-26	hibah	293113000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
136	OPS-1206-2023-0003	Sepeda motor operasional	6	21	10	8	bergerak	Yamaha NMAX	Tipe 597	KR2-920300	2023-11-09	hibah	21380000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
137	OPS-1205-2022-0003	Mobil boks pengiriman	5	21	2	8	bergerak	Toyota Avanza	Tipe 917	KR4-319178	2022-11-05	hibah	440885000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
138	FIN-1211-2024-0001	Mesin faksimile	11	8	9	5	bergerak	Yealink	Tipe 104	ALK-573404	2024-08-24	hibah	3583000.00	\N	\N	\N	aktif	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
139	FIN-1212-2018-0002	Kipas angin dinding	12	14	3	5	bergerak	LG	Tipe 431	PGU-492088	2018-08-25	pembelian	16554000.00	\N	\N	\N	tidak_dipakai	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
140	HRD-1206-2024-0002	Sepeda motor kurir	6	21	6	7	bergerak	Honda Beat	Tipe 102	KR2-312991	2024-04-29	pembelian	29320000.00	\N	\N	\N	tidak_dipakai	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
141	FIN-1207-2022-0001	Kursi tamu kayu	7	13	10	5	bergerak	Olympic	Tipe 339	PKY-295363	2022-01-23	pembelian	9996000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
142	HRD-1209-2020-0001	Laptop kerja	9	19	10	7	bergerak	Asus	Tipe 815	KOM-180894	2020-07-07	hibah	5797000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
143	HRD-1205-2024-0001	Mobil boks pengiriman	5	\N	5	7	bergerak	Toyota Innova	Tipe 199	KR4-197257	2024-07-04	pembelian	516763000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
144	HRD-1205-2020-0001	Kendaraan operasional	5	21	2	7	bergerak	Daihatsu Gran Max	Tipe 963	KR4-534523	2020-05-05	pembelian	242968000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
145	HRD-1210-2023-0001	Mesin penghancur kertas	10	18	11	7	bergerak	GBC	Tipe 298	MSK-852908	2023-07-06	hibah	37807000.00	\N	\N	\N	dipinjam	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
146	HRD-1210-2018-0002	Mesin absensi sidik jari	10	14	12	7	bergerak	Fuji Xerox	Tipe 205	MSK-993891	2018-08-26	pembelian	3824000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
154	OPS-1207-2025-0002	Kursi tamu kayu	7	13	5	8	bergerak	Ligna	Tipe 974	PKY-109546	2025-10-17	pembelian	6075000.00	\N	\N	\N	tidak_dipakai	rusak	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:06	2026-09-06 14:04:06	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
60	FIN-1204-2022-0001	Pompa air gedung	4	15	\N	5	tetap	Schneider	Tipe 412	PRS-471714	2022-01-16	pembelian	277695000.00	\N	\N	\N	perbaikan	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	foto-aset/01M1VK3P4ABW0VF3DYCAD0FRP6.jpeg	2026-09-06 14:04:05	2026-09-06 14:49:10	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
147	FIN-1204-2023-0001	Genset cadangan	4	10	\N	5	tetap	Appron	Tipe 285	PRS-780300	2023-02-16	pembelian	183052000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	foto-aset/01M1VKYK2PP5Z1GMBBMMHG3QYG.jpeg	2026-09-06 14:04:06	2026-09-06 15:03:52	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
17	OPS-1211-2025-0001	Telepon konferensi	11	18	\N	8	bergerak	Motorola	Tipe 184	ALK-134653	2025-03-21	pembelian	2694000.00	\N	\N	\N	aktif	baik	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 23:09:59	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
32	OPS-1211-2022-0001	Mesin faksimile	11	6	13	8	bergerak	Panasonic	Tipe 451	ALK-438704	2022-08-17	hibah	1140000.00	\N	\N	\N	aktif	perlu_perbaikan	\N	[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.	\N	2026-09-06 14:04:04	2026-09-06 23:13:52	milik	\N	\N	\N	\N	\N	\N	f	\N	\N	\N	\N	\N
\.


--
-- Data for Name: audit_logs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.audit_logs (id, user_id, user_name, event, auditable_type, auditable_id, auditable_label, module_code, old_values, new_values, url, ip_address, user_agent, created_at) FROM stdin;
1	\N	\N	created	App\\Models\\Module	1	Role	\N	\N	{"code":"roles","name":"Role","description":"Membuat role dan menentukan modul serta aksi yang boleh diaksesnya.","group":"Pengaturan Akses","icon":"heroicon-o-shield-check","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":1}	console	\N	\N	2026-09-06 09:04:36
2	\N	\N	created	App\\Models\\Module	2	Pengguna	\N	\N	{"code":"users","name":"Pengguna","description":"Akun yang bisa masuk ke aplikasi beserta role dan izin khususnya.","group":"Pengaturan Akses","icon":"heroicon-o-user-circle","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":2}	console	\N	\N	2026-09-06 09:04:36
3	\N	\N	created	App\\Models\\Module	3	Modul	\N	\N	{"code":"modules","name":"Modul","description":"Registri modul yang menjadi sumber daftar izin.","group":"Pengaturan Akses","icon":"heroicon-o-squares-2x2","sort":30,"available_actions":"[\\"read\\",\\"update\\"]","is_active":true,"id":3}	console	\N	\N	2026-09-06 09:04:36
4	\N	\N	created	App\\Models\\Module	4	Departemen	\N	\N	{"code":"departments","name":"Departemen","description":"Struktur departemen dan cost center untuk pembebanan biaya.","group":"Data Induk","icon":"heroicon-o-building-office-2","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":4}	console	\N	\N	2026-09-06 09:04:36
5	\N	\N	created	App\\Models\\Module	5	Lokasi	\N	\N	{"code":"locations","name":"Lokasi","description":"Gedung, lantai, ruangan, dan area di Head Office.","group":"Data Induk","icon":"heroicon-o-map-pin","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":5}	console	\N	\N	2026-09-06 09:04:36
6	\N	\N	created	App\\Models\\Module	6	Karyawan	\N	\N	{"code":"employees","name":"Karyawan","description":"Data karyawan yang dipakai sebagai penanggung jawab dan pengaju permintaan.","group":"Data Induk","icon":"heroicon-o-identification","sort":30,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":6}	console	\N	\N	2026-09-06 09:04:36
7	\N	\N	created	App\\Models\\Module	7	Pengaturan	\N	\N	{"code":"settings","name":"Pengaturan","description":"Identitas perusahaan dan pengaturan sistem lainnya.","group":"Sistem","icon":"heroicon-o-adjustments-horizontal","sort":10,"available_actions":"[\\"read\\",\\"update\\"]","is_active":true,"id":7}	console	\N	\N	2026-09-06 09:04:36
8	\N	\N	created	App\\Models\\Module	8	Jejak audit	\N	\N	{"code":"audit_logs","name":"Jejak audit","description":"Riwayat penambahan, perubahan, dan penghapusan data.","group":"Sistem","icon":"heroicon-o-clipboard-document-list","sort":20,"available_actions":"[\\"read\\"]","is_active":true,"id":8}	console	\N	\N	2026-09-06 09:04:36
9	\N	\N	created	App\\Models\\Role	1	Administrator Sistem	\N	\N	{"code":"administrator","name":"Administrator Sistem","description":"Mengelola pengguna, role, data induk, dan pengaturan sistem.","is_system":true,"data_scope":"all","is_active":true,"id":1}	console	\N	\N	2026-09-06 09:04:36
10	\N	\N	created	App\\Models\\Role	2	Manajer GA	\N	\N	{"code":"manajer-ga","name":"Manajer GA","description":"Melihat seluruh data GA dan mengubah data induk, tanpa mengelola akses pengguna.","is_system":false,"data_scope":"all","is_active":true,"id":2}	console	\N	\N	2026-09-06 09:04:36
11	\N	\N	created	App\\Models\\Role	3	Staf GA	\N	\N	{"code":"staf-ga","name":"Staf GA","description":"Mengelola data induk harian: karyawan, departemen, dan lokasi.","is_system":false,"data_scope":"all","is_active":true,"id":3}	console	\N	\N	2026-09-06 09:04:36
12	\N	\N	created	App\\Models\\Role	4	Karyawan	\N	\N	{"code":"karyawan","name":"Karyawan","description":"Hanya melihat direktori karyawan dan daftar lokasi.","is_system":false,"data_scope":"own","is_active":true,"id":4}	console	\N	\N	2026-09-06 09:04:36
13	\N	\N	created	App\\Models\\Setting	1	Nama perusahaan	\N	\N	{"key":"perusahaan.nama","group":"perusahaan","label":"Nama perusahaan","description":"Dipakai pada kop laporan dan dokumen cetak.","type":"text","value":null,"id":1}	console	\N	\N	2026-09-06 09:04:37
14	\N	\N	created	App\\Models\\Setting	2	Nama singkat	\N	\N	{"key":"perusahaan.nama_singkat","group":"perusahaan","label":"Nama singkat","description":"Muncul di sudut kiri atas aplikasi.","type":"text","value":"GAIS","id":2}	console	\N	\N	2026-09-06 09:04:37
15	\N	\N	created	App\\Models\\Setting	3	Alamat Head Office	\N	\N	{"key":"perusahaan.alamat","group":"perusahaan","label":"Alamat Head Office","description":"Alamat lengkap kantor pusat.","type":"textarea","value":null,"id":3}	console	\N	\N	2026-09-06 09:04:37
16	\N	\N	created	App\\Models\\Setting	4	Telepon kantor	\N	\N	{"key":"perusahaan.telepon","group":"perusahaan","label":"Telepon kantor","description":null,"type":"text","value":null,"id":4}	console	\N	\N	2026-09-06 09:04:37
17	\N	\N	created	App\\Models\\Setting	5	Email GA	\N	\N	{"key":"perusahaan.email","group":"perusahaan","label":"Email GA","description":"Alamat email tim GA untuk pemberitahuan.","type":"text","value":null,"id":5}	console	\N	\N	2026-09-06 09:04:37
18	\N	\N	created	App\\Models\\Setting	6	Berkas logo	\N	\N	{"key":"perusahaan.logo","group":"perusahaan","label":"Berkas logo","description":"Belum ada berkas logo. Isi setelah file logo perusahaan tersedia.","type":"text","value":"[LOGO]","id":6}	console	\N	\N	2026-09-06 09:04:37
19	\N	\N	created	App\\Models\\User	1	Administrator	\N	\N	{"name":"Administrator","email":"admin@gais.test","is_super_admin":true,"is_active":true,"id":1}	console	\N	\N	2026-09-06 09:04:37
20	\N	\N	created	App\\Models\\Department	1	CONTOH-GA CONTOH General Affair	\N	\N	{"code":"CONTOH-GA","name":"CONTOH General Affair","cost_center":"CC-GA","id":1}	console	\N	\N	2026-09-06 11:55:29
21	\N	\N	created	App\\Models\\Department	2	CONTOH-FIN CONTOH Finance	\N	\N	{"code":"CONTOH-FIN","name":"CONTOH Finance","cost_center":"CC-FIN","id":2}	console	\N	\N	2026-09-06 11:55:29
22	\N	\N	created	App\\Models\\Department	3	CONTOH-IT CONTOH Information Technology	\N	\N	{"code":"CONTOH-IT","name":"CONTOH Information Technology","cost_center":"CC-IT","id":3}	console	\N	\N	2026-09-06 11:55:29
23	\N	\N	created	App\\Models\\Location	1	CONTOH-HO CONTOH Head Office	\N	\N	{"code":"CONTOH-HO","name":"CONTOH Head Office","type":"gedung","parent_id":null,"id":1}	console	\N	\N	2026-09-06 11:55:29
24	\N	\N	created	App\\Models\\Location	2	CONTOH-HO-L1 CONTOH Lantai 1	\N	\N	{"code":"CONTOH-HO-L1","name":"CONTOH Lantai 1","type":"lantai","parent_id":1,"id":2}	console	\N	\N	2026-09-06 11:55:29
25	\N	\N	created	App\\Models\\Location	3	CONTOH-HO-L1-R01 CONTOH Ruang Rapat	\N	\N	{"code":"CONTOH-HO-L1-R01","name":"CONTOH Ruang Rapat","type":"ruangan","parent_id":2,"id":3}	console	\N	\N	2026-09-06 11:55:29
26	\N	\N	created	App\\Models\\Employee	1	CONTOH-0001 CONTOH Staf GA	\N	\N	{"nip":"CONTOH-0001","full_name":"CONTOH Staf GA","department_id":1,"position":"Staf General Affair","employment_status":"tetap","is_active":true,"id":1}	console	\N	\N	2026-09-06 11:55:29
27	1	Administrator	login	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 12:00:36
28	\N	\N	created	App\\Models\\Module	9	Kategori aset	\N	\N	{"code":"asset_categories","name":"Kategori aset","description":"Kelompok aset beserta awalan kode, umur ekonomis, dan metode penyusutannya.","group":"Aset","icon":"heroicon-o-rectangle-group","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":9}	console	\N	\N	2026-09-06 12:53:24
29	\N	\N	created	App\\Models\\Module	10	Daftar aset	\N	\N	{"code":"assets","name":"Daftar aset","description":"Pendataan aset tetap dan aset bergerak, lokasi, penanggung jawab, status, dan kondisinya.","group":"Aset","icon":"heroicon-o-cube","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\",\\"print\\"]","is_active":true,"id":10}	console	\N	\N	2026-09-06 12:53:24
30	\N	\N	created	App\\Models\\Setting	7	Lebar label (mm)	\N	\N	{"key":"label.lebar_mm","group":"label","label":"Lebar label (mm)","description":"Ukuran satu stiker. Bawaannya 70, sesuai lembar stiker A4 isi 24.","type":"number","value":"70","id":7}	console	\N	\N	2026-09-06 12:53:36
31	\N	\N	created	App\\Models\\Setting	8	Tinggi label (mm)	\N	\N	{"key":"label.tinggi_mm","group":"label","label":"Tinggi label (mm)","description":"Bawaannya 37, sesuai lembar stiker A4 isi 24.","type":"number","value":"37","id":8}	console	\N	\N	2026-09-06 12:53:36
32	\N	\N	created	App\\Models\\Setting	9	Jumlah label per baris	\N	\N	{"key":"label.kolom","group":"label","label":"Jumlah label per baris","description":"Bawaannya 3 kolom.","type":"number","value":"3","id":9}	console	\N	\N	2026-09-06 12:53:36
33	\N	\N	created	App\\Models\\Setting	10	Margin atas halaman (mm)	\N	\N	{"key":"label.margin_atas_mm","group":"label","label":"Margin atas halaman (mm)","description":"Jarak dari tepi atas kertas ke label pertama. Sesuaikan kalau cetakan bergeser.","type":"number","value":"8","id":10}	console	\N	\N	2026-09-06 12:53:36
34	\N	\N	created	App\\Models\\Setting	11	Margin kiri halaman (mm)	\N	\N	{"key":"label.margin_kiri_mm","group":"label","label":"Margin kiri halaman (mm)","description":"Jarak dari tepi kiri kertas ke kolom pertama.","type":"number","value":"5","id":11}	console	\N	\N	2026-09-06 12:53:36
35	\N	\N	created	App\\Models\\AssetCategory	1	TNH Tanah	\N	\N	{"code":"TNH","name":"Tanah","description":"Tanah milik perusahaan. Tidak disusutkan.","tax_group":"tidak_disusutkan","depreciation_method":"tidak_disusutkan","is_active":true,"useful_life_months":null,"id":1}	console	\N	\N	2026-09-06 13:30:01
36	\N	\N	created	App\\Models\\AssetCategory	2	BGN Bangunan permanen	\N	\N	{"code":"BGN","name":"Bangunan permanen","description":"Gedung kantor dan bangunan permanen lainnya.","tax_group":"bangunan_permanen","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":240,"id":2}	console	\N	\N	2026-09-06 13:30:01
37	\N	\N	created	App\\Models\\AssetCategory	3	BGT Bangunan tidak permanen	\N	\N	{"code":"BGT","name":"Bangunan tidak permanen","description":"Bangunan sementara, misalnya gudang semi permanen.","tax_group":"bangunan_tidak_permanen","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":120,"id":3}	console	\N	\N	2026-09-06 13:30:01
38	\N	\N	created	App\\Models\\AssetCategory	4	PRS Prasarana dan instalasi	\N	\N	{"code":"PRS","name":"Prasarana dan instalasi","description":"Instalasi listrik, air, jaringan, dan pekerjaan renovasi yang melekat pada bangunan.","tax_group":"kelompok_2","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":96,"id":4}	console	\N	\N	2026-09-06 13:30:01
39	\N	\N	created	App\\Models\\AssetCategory	5	KR4 Kendaraan roda empat	\N	\N	{"code":"KR4","name":"Kendaraan roda empat","description":"Mobil operasional, bus, dan truk.","tax_group":"kelompok_2","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":96,"id":5}	console	\N	\N	2026-09-06 13:30:01
40	\N	\N	created	App\\Models\\AssetCategory	6	KR2 Kendaraan roda dua	\N	\N	{"code":"KR2","name":"Kendaraan roda dua","description":"Sepeda motor operasional.","tax_group":"kelompok_1","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":48,"id":6}	console	\N	\N	2026-09-06 13:30:01
41	\N	\N	created	App\\Models\\AssetCategory	7	PKY Perabot kantor kayu	\N	\N	{"code":"PKY","name":"Perabot kantor kayu","description":"Meja, kursi, lemari, dan perabot berbahan kayu atau rotan yang bukan bagian bangunan.","tax_group":"kelompok_1","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":48,"id":7}	console	\N	\N	2026-09-06 13:30:01
42	\N	\N	created	App\\Models\\AssetCategory	8	PLG Perabot kantor logam	\N	\N	{"code":"PLG","name":"Perabot kantor logam","description":"Meja, kursi, lemari, dan filing cabinet berbahan logam yang bukan bagian bangunan.","tax_group":"kelompok_2","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":96,"id":8}	console	\N	\N	2026-09-06 13:30:01
43	\N	\N	created	App\\Models\\AssetCategory	9	KOM Komputer dan perangkat jaringan	\N	\N	{"code":"KOM","name":"Komputer dan perangkat jaringan","description":"Komputer, laptop, printer, pemindai, server, dan perangkat jaringan.","tax_group":"kelompok_1","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":48,"id":9}	console	\N	\N	2026-09-06 13:30:01
44	\N	\N	created	App\\Models\\AssetCategory	10	MSK Mesin dan peralatan kantor	\N	\N	{"code":"MSK","name":"Mesin dan peralatan kantor","description":"Mesin fotokopi, mesin penghancur kertas, proyektor, dan peralatan kantor sejenis.","tax_group":"kelompok_1","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":48,"id":10}	console	\N	\N	2026-09-06 13:30:01
45	\N	\N	created	App\\Models\\AssetCategory	11	ALK Alat komunikasi	\N	\N	{"code":"ALK","name":"Alat komunikasi","description":"Pesawat telepon, faksimile, telepon seluler, dan radio komunikasi.","tax_group":"kelompok_1","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":48,"id":11}	console	\N	\N	2026-09-06 13:30:01
46	\N	\N	created	App\\Models\\AssetCategory	12	PGU Peralatan pengatur udara	\N	\N	{"code":"PGU","name":"Peralatan pengatur udara","description":"AC, kipas angin, dan peralatan pengatur udara lainnya.","tax_group":"kelompok_2","depreciation_method":"garis_lurus","is_active":true,"useful_life_months":96,"id":12}	console	\N	\N	2026-09-06 13:30:01
47	\N	\N	created	App\\Models\\AssetCategory	13	ADP Aset dalam penyelesaian	\N	\N	{"code":"ADP","name":"Aset dalam penyelesaian","description":"Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan sampai dipindahkan ke kelas yang sesuai.","tax_group":"tidak_disusutkan","depreciation_method":"tidak_disusutkan","is_active":true,"useful_life_months":null,"id":13}	console	\N	\N	2026-09-06 13:30:01
48	\N	\N	updated	App\\Models\\AssetCategory	1	TNH Tanah	\N	{"description":"Tanah milik perusahaan. Tidak disusutkan.","account_asset":null}	{"description":"Tanah milik perusahaan. Tidak disusutkan. [COA CONTOH]","account_asset":"1201"}	console	\N	\N	2026-09-06 13:34:02
49	\N	\N	updated	App\\Models\\AssetCategory	2	BGN Bangunan permanen	\N	{"description":"Gedung kantor dan bangunan permanen lainnya.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Gedung kantor dan bangunan permanen lainnya. [COA CONTOH]","account_asset":"1202","account_accumulated":"1302","account_expense":"6202"}	console	\N	\N	2026-09-06 13:34:03
50	\N	\N	updated	App\\Models\\AssetCategory	3	BGT Bangunan tidak permanen	\N	{"description":"Bangunan sementara, misalnya gudang semi permanen.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Bangunan sementara, misalnya gudang semi permanen. [COA CONTOH]","account_asset":"1203","account_accumulated":"1303","account_expense":"6203"}	console	\N	\N	2026-09-06 13:34:03
51	\N	\N	updated	App\\Models\\AssetCategory	4	PRS Prasarana dan instalasi	\N	{"description":"Instalasi listrik, air, jaringan, dan pekerjaan renovasi yang melekat pada bangunan.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Instalasi listrik, air, jaringan, dan pekerjaan renovasi yang melekat pada bangunan. [COA CONTOH]","account_asset":"1204","account_accumulated":"1304","account_expense":"6204"}	console	\N	\N	2026-09-06 13:34:03
52	\N	\N	updated	App\\Models\\AssetCategory	5	KR4 Kendaraan roda empat	\N	{"description":"Mobil operasional, bus, dan truk.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Mobil operasional, bus, dan truk. [COA CONTOH]","account_asset":"1205","account_accumulated":"1305","account_expense":"6205"}	console	\N	\N	2026-09-06 13:34:03
53	\N	\N	updated	App\\Models\\AssetCategory	6	KR2 Kendaraan roda dua	\N	{"description":"Sepeda motor operasional.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Sepeda motor operasional. [COA CONTOH]","account_asset":"1206","account_accumulated":"1306","account_expense":"6206"}	console	\N	\N	2026-09-06 13:34:03
54	\N	\N	updated	App\\Models\\AssetCategory	7	PKY Perabot kantor kayu	\N	{"description":"Meja, kursi, lemari, dan perabot berbahan kayu atau rotan yang bukan bagian bangunan.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Meja, kursi, lemari, dan perabot berbahan kayu atau rotan yang bukan bagian bangunan. [COA CONTOH]","account_asset":"1207","account_accumulated":"1307","account_expense":"6207"}	console	\N	\N	2026-09-06 13:34:03
55	\N	\N	updated	App\\Models\\AssetCategory	8	PLG Perabot kantor logam	\N	{"description":"Meja, kursi, lemari, dan filing cabinet berbahan logam yang bukan bagian bangunan.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Meja, kursi, lemari, dan filing cabinet berbahan logam yang bukan bagian bangunan. [COA CONTOH]","account_asset":"1208","account_accumulated":"1308","account_expense":"6208"}	console	\N	\N	2026-09-06 13:34:03
56	\N	\N	updated	App\\Models\\AssetCategory	9	KOM Komputer dan perangkat jaringan	\N	{"description":"Komputer, laptop, printer, pemindai, server, dan perangkat jaringan.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Komputer, laptop, printer, pemindai, server, dan perangkat jaringan. [COA CONTOH]","account_asset":"1209","account_accumulated":"1309","account_expense":"6209"}	console	\N	\N	2026-09-06 13:34:03
57	\N	\N	updated	App\\Models\\AssetCategory	10	MSK Mesin dan peralatan kantor	\N	{"description":"Mesin fotokopi, mesin penghancur kertas, proyektor, dan peralatan kantor sejenis.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Mesin fotokopi, mesin penghancur kertas, proyektor, dan peralatan kantor sejenis. [COA CONTOH]","account_asset":"1210","account_accumulated":"1310","account_expense":"6210"}	console	\N	\N	2026-09-06 13:34:03
58	\N	\N	updated	App\\Models\\AssetCategory	11	ALK Alat komunikasi	\N	{"description":"Pesawat telepon, faksimile, telepon seluler, dan radio komunikasi.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"Pesawat telepon, faksimile, telepon seluler, dan radio komunikasi. [COA CONTOH]","account_asset":"1211","account_accumulated":"1311","account_expense":"6211"}	console	\N	\N	2026-09-06 13:34:03
59	\N	\N	updated	App\\Models\\AssetCategory	12	PGU Peralatan pengatur udara	\N	{"description":"AC, kipas angin, dan peralatan pengatur udara lainnya.","account_asset":null,"account_accumulated":null,"account_expense":null}	{"description":"AC, kipas angin, dan peralatan pengatur udara lainnya. [COA CONTOH]","account_asset":"1212","account_accumulated":"1312","account_expense":"6212"}	console	\N	\N	2026-09-06 13:34:03
60	\N	\N	updated	App\\Models\\AssetCategory	13	ADP Aset dalam penyelesaian	\N	{"description":"Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan sampai dipindahkan ke kelas yang sesuai.","account_asset":null}	{"description":"Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan sampai dipindahkan ke kelas yang sesuai. [COA CONTOH]","account_asset":"1213"}	console	\N	\N	2026-09-06 13:34:03
61	1	Administrator	updated	App\\Models\\AssetCategory	13	ADP Aset dalam penyelesaian	\N	{"description":"Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan sampai dipindahkan ke kelas yang sesuai. [COA CONTOH]"}	{"description":"Pekerjaan yang belum selesai dan belum siap dipakai. Belum disusutkan . [COA CONTOH]"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 13:44:28
62	1	Administrator	updated	App\\Models\\AssetCategory	7	PKY Perabot kantor kayu	\N	{"description":"Meja, kursi, lemari, dan perabot berbahan kayu atau rotan yang bukan bagian bangunan. [COA CONTOH]"}	{"description":"Meja, kursi, lemari, dan perabot berbahan kayu a. [COA CONTOH]"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 13:44:47
63	\N	\N	created	App\\Models\\Department	4	GA General Affair	\N	\N	{"code":"GA","name":"General Affair","cost_center":"CC-GA","is_active":true,"id":4}	console	\N	\N	2026-09-06 14:04:04
64	\N	\N	created	App\\Models\\Department	5	FIN Finance	\N	\N	{"code":"FIN","name":"Finance","cost_center":"CC-FIN","is_active":true,"id":5}	console	\N	\N	2026-09-06 14:04:04
65	\N	\N	created	App\\Models\\Department	6	IT Information Technology	\N	\N	{"code":"IT","name":"Information Technology","cost_center":"CC-IT","is_active":true,"id":6}	console	\N	\N	2026-09-06 14:04:04
66	\N	\N	created	App\\Models\\Department	7	HRD Human Resources	\N	\N	{"code":"HRD","name":"Human Resources","cost_center":"CC-HRD","is_active":true,"id":7}	console	\N	\N	2026-09-06 14:04:04
67	\N	\N	created	App\\Models\\Department	8	OPS Operations	\N	\N	{"code":"OPS","name":"Operations","cost_center":"CC-OPS","is_active":true,"id":8}	console	\N	\N	2026-09-06 14:04:04
68	\N	\N	created	App\\Models\\Location	4	HO Head Office	\N	\N	{"code":"HO","name":"Head Office","type":"gedung","parent_id":null,"description":"[DATA DEMO]","is_active":true,"id":4}	console	\N	\N	2026-09-06 14:04:04
69	\N	\N	created	App\\Models\\Location	5	HO-L1 Lantai 1	\N	\N	{"code":"HO-L1","name":"Lantai 1","type":"lantai","parent_id":4,"description":"[DATA DEMO]","is_active":true,"id":5}	console	\N	\N	2026-09-06 14:04:04
70	\N	\N	created	App\\Models\\Location	6	HO-L1-R01 Lobi utama	\N	\N	{"code":"HO-L1-R01","name":"Lobi utama","type":"ruangan","parent_id":5,"description":"[DATA DEMO]","is_active":true,"id":6}	console	\N	\N	2026-09-06 14:04:04
71	\N	\N	created	App\\Models\\Location	7	HO-L1-R02 Ruang resepsionis	\N	\N	{"code":"HO-L1-R02","name":"Ruang resepsionis","type":"ruangan","parent_id":5,"description":"[DATA DEMO]","is_active":true,"id":7}	console	\N	\N	2026-09-06 14:04:04
72	\N	\N	created	App\\Models\\Location	8	HO-L1-R03 Ruang tunggu tamu	\N	\N	{"code":"HO-L1-R03","name":"Ruang tunggu tamu","type":"ruangan","parent_id":5,"description":"[DATA DEMO]","is_active":true,"id":8}	console	\N	\N	2026-09-06 14:04:04
73	\N	\N	created	App\\Models\\Location	9	HO-L1-R04 Ruang arsip	\N	\N	{"code":"HO-L1-R04","name":"Ruang arsip","type":"ruangan","parent_id":5,"description":"[DATA DEMO]","is_active":true,"id":9}	console	\N	\N	2026-09-06 14:04:04
74	\N	\N	created	App\\Models\\Location	10	HO-L2 Lantai 2	\N	\N	{"code":"HO-L2","name":"Lantai 2","type":"lantai","parent_id":4,"description":"[DATA DEMO]","is_active":true,"id":10}	console	\N	\N	2026-09-06 14:04:04
75	\N	\N	created	App\\Models\\Location	11	HO-L2-R01 Ruang Finance	\N	\N	{"code":"HO-L2-R01","name":"Ruang Finance","type":"ruangan","parent_id":10,"description":"[DATA DEMO]","is_active":true,"id":11}	console	\N	\N	2026-09-06 14:04:04
76	\N	\N	created	App\\Models\\Location	12	HO-L2-R02 Ruang HRD	\N	\N	{"code":"HO-L2-R02","name":"Ruang HRD","type":"ruangan","parent_id":10,"description":"[DATA DEMO]","is_active":true,"id":12}	console	\N	\N	2026-09-06 14:04:04
77	\N	\N	created	App\\Models\\Location	13	HO-L2-R03 Ruang rapat kecil	\N	\N	{"code":"HO-L2-R03","name":"Ruang rapat kecil","type":"ruangan","parent_id":10,"description":"[DATA DEMO]","is_active":true,"id":13}	console	\N	\N	2026-09-06 14:04:04
78	\N	\N	created	App\\Models\\Location	14	HO-L2-R04 Pantry lantai 2	\N	\N	{"code":"HO-L2-R04","name":"Pantry lantai 2","type":"ruangan","parent_id":10,"description":"[DATA DEMO]","is_active":true,"id":14}	console	\N	\N	2026-09-06 14:04:04
79	\N	\N	created	App\\Models\\Location	15	HO-L3 Lantai 3	\N	\N	{"code":"HO-L3","name":"Lantai 3","type":"lantai","parent_id":4,"description":"[DATA DEMO]","is_active":true,"id":15}	console	\N	\N	2026-09-06 14:04:04
80	\N	\N	created	App\\Models\\Location	16	HO-L3-R01 Ruang IT	\N	\N	{"code":"HO-L3-R01","name":"Ruang IT","type":"ruangan","parent_id":15,"description":"[DATA DEMO]","is_active":true,"id":16}	console	\N	\N	2026-09-06 14:04:04
81	\N	\N	created	App\\Models\\Location	17	HO-L3-R02 Ruang server	\N	\N	{"code":"HO-L3-R02","name":"Ruang server","type":"ruangan","parent_id":15,"description":"[DATA DEMO]","is_active":true,"id":17}	console	\N	\N	2026-09-06 14:04:04
82	\N	\N	created	App\\Models\\Location	18	HO-L3-R03 Ruang rapat besar	\N	\N	{"code":"HO-L3-R03","name":"Ruang rapat besar","type":"ruangan","parent_id":15,"description":"[DATA DEMO]","is_active":true,"id":18}	console	\N	\N	2026-09-06 14:04:04
83	\N	\N	created	App\\Models\\Location	19	HO-L3-R04 Ruang General Affair	\N	\N	{"code":"HO-L3-R04","name":"Ruang General Affair","type":"ruangan","parent_id":15,"description":"[DATA DEMO]","is_active":true,"id":19}	console	\N	\N	2026-09-06 14:04:04
84	\N	\N	created	App\\Models\\Location	20	HO-GDG Gudang belakang	\N	\N	{"code":"HO-GDG","name":"Gudang belakang","type":"area","parent_id":4,"description":"[DATA DEMO]","is_active":true,"id":20}	console	\N	\N	2026-09-06 14:04:04
85	\N	\N	created	App\\Models\\Location	21	HO-PRK Area parkir kendaraan dinas	\N	\N	{"code":"HO-PRK","name":"Area parkir kendaraan dinas","type":"area","parent_id":4,"description":"[DATA DEMO]","is_active":true,"id":21}	console	\N	\N	2026-09-06 14:04:04
86	\N	\N	created	App\\Models\\Employee	2	DEMO-001 Andi Prasetyo	\N	\N	{"nip":"DEMO-001","full_name":"Andi Prasetyo","department_id":4,"position":"Staf General Affair","employment_status":"tetap","join_date":"2018-07-30 00:00:00","is_active":true,"id":2}	console	\N	\N	2026-09-06 14:04:04
87	\N	\N	created	App\\Models\\Employee	3	DEMO-002 Siti Rahmawati	\N	\N	{"nip":"DEMO-002","full_name":"Siti Rahmawati","department_id":4,"position":"Admin Aset","employment_status":"tetap","join_date":"2016-08-08 00:00:00","is_active":true,"id":3}	console	\N	\N	2026-09-06 14:04:04
88	\N	\N	created	App\\Models\\Employee	4	DEMO-003 Budi Santoso	\N	\N	{"nip":"DEMO-003","full_name":"Budi Santoso","department_id":4,"position":"Manajer General Affair","employment_status":"tetap","join_date":"2020-05-27 00:00:00","is_active":true,"id":4}	console	\N	\N	2026-09-06 14:04:04
89	\N	\N	created	App\\Models\\Employee	5	DEMO-004 Dewi Anggraini	\N	\N	{"nip":"DEMO-004","full_name":"Dewi Anggraini","department_id":5,"position":"Staf Akuntansi","employment_status":"tetap","join_date":"2014-03-25 00:00:00","is_active":true,"id":5}	console	\N	\N	2026-09-06 14:04:04
90	\N	\N	created	App\\Models\\Employee	6	DEMO-005 Rizal Hakim	\N	\N	{"nip":"DEMO-005","full_name":"Rizal Hakim","department_id":5,"position":"Manajer Keuangan","employment_status":"tetap","join_date":"2020-06-04 00:00:00","is_active":true,"id":6}	console	\N	\N	2026-09-06 14:04:04
91	\N	\N	created	App\\Models\\Employee	7	DEMO-006 Putri Maharani	\N	\N	{"nip":"DEMO-006","full_name":"Putri Maharani","department_id":6,"position":"Staf Dukungan Teknis","employment_status":"tetap","join_date":"2019-03-06 00:00:00","is_active":true,"id":7}	console	\N	\N	2026-09-06 14:04:04
92	\N	\N	created	App\\Models\\Employee	8	DEMO-007 Agus Setiawan	\N	\N	{"nip":"DEMO-007","full_name":"Agus Setiawan","department_id":6,"position":"Administrator Jaringan","employment_status":"tetap","join_date":"2019-07-06 00:00:00","is_active":true,"id":8}	console	\N	\N	2026-09-06 14:04:04
93	\N	\N	created	App\\Models\\Employee	9	DEMO-008 Lestari Ningsih	\N	\N	{"nip":"DEMO-008","full_name":"Lestari Ningsih","department_id":7,"position":"Staf Kepegawaian","employment_status":"tetap","join_date":"2018-01-24 00:00:00","is_active":true,"id":9}	console	\N	\N	2026-09-06 14:04:04
94	\N	\N	created	App\\Models\\Employee	10	DEMO-009 Hendra Wijaya	\N	\N	{"nip":"DEMO-009","full_name":"Hendra Wijaya","department_id":7,"position":"Manajer SDM","employment_status":"tetap","join_date":"2022-03-16 00:00:00","is_active":true,"id":10}	console	\N	\N	2026-09-06 14:04:04
95	\N	\N	created	App\\Models\\Employee	11	DEMO-010 Maya Kusuma	\N	\N	{"nip":"DEMO-010","full_name":"Maya Kusuma","department_id":8,"position":"Staf Operasional","employment_status":"tetap","join_date":"2021-07-07 00:00:00","is_active":true,"id":11}	console	\N	\N	2026-09-06 14:04:04
96	\N	\N	created	App\\Models\\Employee	12	DEMO-011 Fajar Nugroho	\N	\N	{"nip":"DEMO-011","full_name":"Fajar Nugroho","department_id":8,"position":"Pengemudi","employment_status":"tetap","join_date":"2017-12-31 00:00:00","is_active":true,"id":12}	console	\N	\N	2026-09-06 14:04:04
97	\N	\N	created	App\\Models\\Employee	13	DEMO-012 Ratna Sari	\N	\N	{"nip":"DEMO-012","full_name":"Ratna Sari","department_id":8,"position":"Staf Umum","employment_status":"tetap","join_date":"2022-07-23 00:00:00","is_active":true,"id":13}	console	\N	\N	2026-09-06 14:04:04
98	\N	\N	created	App\\Models\\NumberSequence	1	asset.GA.1201 Kode aset GA akun 1201	\N	\N	{"code":"asset.GA.1201","name":"Kode aset GA akun 1201","prefix":"GA-1201","separator":"-","period_format":"Y","padding":4,"id":1}	console	\N	\N	2026-09-06 14:04:04
99	\N	\N	created	App\\Models\\Asset	1	GA-1201-2017-0001 Tanah kantor pusat	\N	\N	{"name":"Tanah kantor pusat","asset_category_id":1,"department_id":4,"location_id":4,"asset_type":"tetap","brand":null,"model":null,"acquisition_date":"2017-09-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":12000000000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1201-2017-0001","id":1}	console	\N	\N	2026-09-06 14:04:04
100	\N	\N	created	App\\Models\\NumberSequence	2	asset.GA.1202 Kode aset GA akun 1202	\N	\N	{"code":"asset.GA.1202","name":"Kode aset GA akun 1202","prefix":"GA-1202","separator":"-","period_format":"Y","padding":4,"id":2}	console	\N	\N	2026-09-06 14:04:04
101	\N	\N	created	App\\Models\\Asset	2	GA-1202-2012-0001 Gedung Head Office	\N	\N	{"name":"Gedung Head Office","asset_category_id":2,"department_id":4,"location_id":4,"asset_type":"tetap","brand":null,"model":null,"acquisition_date":"2012-09-20 00:00:00","acquisition_source":"pembelian","acquisition_cost":24500000000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1202-2012-0001","id":2}	console	\N	\N	2026-09-06 14:04:04
102	\N	\N	created	App\\Models\\NumberSequence	3	asset.OPS.1203 Kode aset OPS akun 1203	\N	\N	{"code":"asset.OPS.1203","name":"Kode aset OPS akun 1203","prefix":"OPS-1203","separator":"-","period_format":"Y","padding":4,"id":3}	console	\N	\N	2026-09-06 14:04:04
103	\N	\N	created	App\\Models\\Asset	3	OPS-1203-2018-0001 Gudang semi permanen belakang	\N	\N	{"name":"Gudang semi permanen belakang","asset_category_id":3,"department_id":8,"location_id":4,"asset_type":"tetap","brand":null,"model":null,"acquisition_date":"2018-05-25 00:00:00","acquisition_source":"pembelian","acquisition_cost":640000000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1203-2018-0001","id":3}	console	\N	\N	2026-09-06 14:04:04
104	\N	\N	created	App\\Models\\NumberSequence	4	asset.GA.1213 Kode aset GA akun 1213	\N	\N	{"code":"asset.GA.1213","name":"Kode aset GA akun 1213","prefix":"GA-1213","separator":"-","period_format":"Y","padding":4,"id":4}	console	\N	\N	2026-09-06 14:04:04
105	\N	\N	created	App\\Models\\Asset	4	GA-1213-2014-0001 Renovasi ruang rapat lantai 3	\N	\N	{"name":"Renovasi ruang rapat lantai 3","asset_category_id":13,"department_id":4,"location_id":4,"asset_type":"tetap","brand":null,"model":null,"acquisition_date":"2014-06-10 00:00:00","acquisition_source":"pembelian","acquisition_cost":185000000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1213-2014-0001","id":4}	console	\N	\N	2026-09-06 14:04:04
106	\N	\N	created	App\\Models\\NumberSequence	5	asset.HRD.1212 Kode aset HRD akun 1212	\N	\N	{"code":"asset.HRD.1212","name":"Kode aset HRD akun 1212","prefix":"HRD-1212","separator":"-","period_format":"Y","padding":4,"id":5}	console	\N	\N	2026-09-06 14:04:04
107	\N	\N	created	App\\Models\\Asset	5	HRD-1212-2021-0001 AC standing 3 PK	\N	\N	{"name":"AC standing 3 PK","asset_category_id":12,"department_id":7,"location_id":9,"custodian_employee_id":13,"asset_type":"bergerak","brand":"LG","model":"Tipe 561","serial_number":"PGU-827629","acquisition_date":"2021-09-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":6957000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1212-2021-0001","id":5}	console	\N	\N	2026-09-06 14:04:04
108	\N	\N	created	App\\Models\\NumberSequence	6	asset.IT.1204 Kode aset IT akun 1204	\N	\N	{"code":"asset.IT.1204","name":"Kode aset IT akun 1204","prefix":"IT-1204","separator":"-","period_format":"Y","padding":4,"id":6}	console	\N	\N	2026-09-06 14:04:04
109	\N	\N	created	App\\Models\\Asset	6	IT-1204-2022-0001 Pompa air gedung	\N	\N	{"name":"Pompa air gedung","asset_category_id":4,"department_id":6,"location_id":10,"custodian_employee_id":null,"asset_type":"tetap","brand":"Panasonic","model":"Tipe 237","serial_number":"PRS-684314","acquisition_date":"2022-03-12 00:00:00","acquisition_source":"pembelian","acquisition_cost":68789000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1204-2022-0001","id":6}	console	\N	\N	2026-09-06 14:04:04
110	\N	\N	created	App\\Models\\NumberSequence	7	asset.OPS.1208 Kode aset OPS akun 1208	\N	\N	{"code":"asset.OPS.1208","name":"Kode aset OPS akun 1208","prefix":"OPS-1208","separator":"-","period_format":"Y","padding":4,"id":7}	console	\N	\N	2026-09-06 14:04:04
111	\N	\N	created	App\\Models\\Asset	7	OPS-1208-2024-0001 Lemari besi arsip	\N	\N	{"name":"Lemari besi arsip","asset_category_id":8,"department_id":8,"location_id":7,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Daichiban","model":"Tipe 430","serial_number":"PLG-426261","acquisition_date":"2024-08-07 00:00:00","acquisition_source":"pembelian","acquisition_cost":19892000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1208-2024-0001","id":7}	console	\N	\N	2026-09-06 14:04:04
112	\N	\N	created	App\\Models\\NumberSequence	8	asset.FIN.1212 Kode aset FIN akun 1212	\N	\N	{"code":"asset.FIN.1212","name":"Kode aset FIN akun 1212","prefix":"FIN-1212","separator":"-","period_format":"Y","padding":4,"id":8}	console	\N	\N	2026-09-06 14:04:04
113	\N	\N	created	App\\Models\\Asset	8	FIN-1212-2018-0001 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":5,"location_id":12,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 382","serial_number":"PGU-223492","acquisition_date":"2018-06-07 00:00:00","acquisition_source":"pembelian","acquisition_cost":3036000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2018-0001","id":8}	console	\N	\N	2026-09-06 14:04:04
114	\N	\N	created	App\\Models\\NumberSequence	9	asset.OPS.1207 Kode aset OPS akun 1207	\N	\N	{"code":"asset.OPS.1207","name":"Kode aset OPS akun 1207","prefix":"OPS-1207","separator":"-","period_format":"Y","padding":4,"id":9}	console	\N	\N	2026-09-06 14:04:04
115	\N	\N	created	App\\Models\\Asset	9	OPS-1207-2020-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":8,"location_id":12,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Activ","model":"Tipe 692","serial_number":"PKY-330001","acquisition_date":"2020-04-24 00:00:00","acquisition_source":"pembelian","acquisition_cost":13642000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2020-0001","id":9}	console	\N	\N	2026-09-06 14:04:04
116	\N	\N	created	App\\Models\\NumberSequence	10	asset.GA.1207 Kode aset GA akun 1207	\N	\N	{"code":"asset.GA.1207","name":"Kode aset GA akun 1207","prefix":"GA-1207","separator":"-","period_format":"Y","padding":4,"id":10}	console	\N	\N	2026-09-06 14:04:04
167	\N	\N	created	App\\Models\\NumberSequence	31	asset.OPS.1206 Kode aset OPS akun 1206	\N	\N	{"code":"asset.OPS.1206","name":"Kode aset OPS akun 1206","prefix":"OPS-1206","separator":"-","period_format":"Y","padding":4,"id":31}	console	\N	\N	2026-09-06 14:04:05
117	\N	\N	created	App\\Models\\Asset	10	GA-1207-2019-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":4,"location_id":6,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Pro Design","model":"Tipe 116","serial_number":"PKY-624518","acquisition_date":"2019-05-02 00:00:00","acquisition_source":"pembelian","acquisition_cost":3275000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1207-2019-0001","id":10}	console	\N	\N	2026-09-06 14:04:04
118	\N	\N	created	App\\Models\\NumberSequence	11	asset.FIN.1210 Kode aset FIN akun 1210	\N	\N	{"code":"asset.FIN.1210","name":"Kode aset FIN akun 1210","prefix":"FIN-1210","separator":"-","period_format":"Y","padding":4,"id":11}	console	\N	\N	2026-09-06 14:04:04
119	\N	\N	created	App\\Models\\Asset	11	FIN-1210-2021-0001 Laminator	\N	\N	{"name":"Laminator","asset_category_id":10,"department_id":5,"location_id":13,"custodian_employee_id":11,"asset_type":"bergerak","brand":"GBC","model":"Tipe 641","serial_number":"MSK-982430","acquisition_date":"2021-08-20 00:00:00","acquisition_source":"pembelian","acquisition_cost":27638000,"status":"tidak_dipakai","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1210-2021-0001","id":11}	console	\N	\N	2026-09-06 14:04:04
120	\N	\N	created	App\\Models\\NumberSequence	12	asset.HRD.1208 Kode aset HRD akun 1208	\N	\N	{"code":"asset.HRD.1208","name":"Kode aset HRD akun 1208","prefix":"HRD-1208","separator":"-","period_format":"Y","padding":4,"id":12}	console	\N	\N	2026-09-06 14:04:04
121	\N	\N	created	App\\Models\\Asset	12	HRD-1208-2022-0001 Rak arsip besi	\N	\N	{"name":"Rak arsip besi","asset_category_id":8,"department_id":7,"location_id":12,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Daichiban","model":"Tipe 439","serial_number":"PLG-722138","acquisition_date":"2022-06-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":5422000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1208-2022-0001","id":12}	console	\N	\N	2026-09-06 14:04:04
122	\N	\N	created	App\\Models\\NumberSequence	13	asset.GA.1208 Kode aset GA akun 1208	\N	\N	{"code":"asset.GA.1208","name":"Kode aset GA akun 1208","prefix":"GA-1208","separator":"-","period_format":"Y","padding":4,"id":13}	console	\N	\N	2026-09-06 14:04:04
123	\N	\N	created	App\\Models\\Asset	13	GA-1208-2026-0001 Loker karyawan	\N	\N	{"name":"Loker karyawan","asset_category_id":8,"department_id":4,"location_id":9,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Daichiban","model":"Tipe 961","serial_number":"PLG-525898","acquisition_date":"2026-10-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":9866000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1208-2026-0001","id":13}	console	\N	\N	2026-09-06 14:04:04
124	\N	\N	created	App\\Models\\Asset	14	HRD-1208-2018-0001 Lemari besi arsip	\N	\N	{"name":"Lemari besi arsip","asset_category_id":8,"department_id":7,"location_id":19,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Brother","model":"Tipe 849","serial_number":"PLG-792089","acquisition_date":"2018-09-15 00:00:00","acquisition_source":"pembelian","acquisition_cost":10816000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1208-2018-0001","id":14}	console	\N	\N	2026-09-06 14:04:04
125	\N	\N	created	App\\Models\\NumberSequence	14	asset.HRD.1209 Kode aset HRD akun 1209	\N	\N	{"code":"asset.HRD.1209","name":"Kode aset HRD akun 1209","prefix":"HRD-1209","separator":"-","period_format":"Y","padding":4,"id":14}	console	\N	\N	2026-09-06 14:04:04
126	\N	\N	created	App\\Models\\Asset	15	HRD-1209-2018-0001 Pemindai dokumen	\N	\N	{"name":"Pemindai dokumen","asset_category_id":9,"department_id":7,"location_id":8,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Cisco","model":"Tipe 175","serial_number":"KOM-963255","acquisition_date":"2018-04-01 00:00:00","acquisition_source":"pembelian","acquisition_cost":27481000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1209-2018-0001","id":15}	console	\N	\N	2026-09-06 14:04:04
127	\N	\N	created	App\\Models\\NumberSequence	15	asset.FIN.1206 Kode aset FIN akun 1206	\N	\N	{"code":"asset.FIN.1206","name":"Kode aset FIN akun 1206","prefix":"FIN-1206","separator":"-","period_format":"Y","padding":4,"id":15}	console	\N	\N	2026-09-06 14:04:04
128	\N	\N	created	App\\Models\\Asset	16	FIN-1206-2022-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":5,"location_id":21,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 993","serial_number":"KR2-175840","acquisition_date":"2022-02-20 00:00:00","acquisition_source":"pembelian","acquisition_cost":27623000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1206-2022-0001","id":16}	console	\N	\N	2026-09-06 14:04:04
129	\N	\N	created	App\\Models\\NumberSequence	16	asset.OPS.1211 Kode aset OPS akun 1211	\N	\N	{"code":"asset.OPS.1211","name":"Kode aset OPS akun 1211","prefix":"OPS-1211","separator":"-","period_format":"Y","padding":4,"id":16}	console	\N	\N	2026-09-06 14:04:04
130	\N	\N	created	App\\Models\\Asset	17	OPS-1211-2025-0001 Telepon konferensi	\N	\N	{"name":"Telepon konferensi","asset_category_id":11,"department_id":8,"location_id":16,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Motorola","model":"Tipe 184","serial_number":"ALK-134653","acquisition_date":"2025-03-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":2694000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1211-2025-0001","id":17}	console	\N	\N	2026-09-06 14:04:04
131	\N	\N	created	App\\Models\\NumberSequence	17	asset.IT.1206 Kode aset IT akun 1206	\N	\N	{"code":"asset.IT.1206","name":"Kode aset IT akun 1206","prefix":"IT-1206","separator":"-","period_format":"Y","padding":4,"id":17}	console	\N	\N	2026-09-06 14:04:04
132	\N	\N	created	App\\Models\\Asset	18	IT-1206-2025-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":6,"location_id":21,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 852","serial_number":"KR2-610152","acquisition_date":"2025-11-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":19791000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1206-2025-0001","id":18}	console	\N	\N	2026-09-06 14:04:04
133	\N	\N	created	App\\Models\\NumberSequence	18	asset.FIN.1208 Kode aset FIN akun 1208	\N	\N	{"code":"asset.FIN.1208","name":"Kode aset FIN akun 1208","prefix":"FIN-1208","separator":"-","period_format":"Y","padding":4,"id":18}	console	\N	\N	2026-09-06 14:04:04
332	\N	\N	created	App\\Models\\SupplyItem	3	ATK-0003 Amplop kabinet coklat	\N	\N	{"name":"Amplop kabinet coklat","category":"kertas","unit":"pak","minimum_stock":10,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0003","id":3}	console	\N	\N	2026-09-06 21:23:53
134	\N	\N	created	App\\Models\\Asset	19	FIN-1208-2019-0001 Filing cabinet 4 laci	\N	\N	{"name":"Filing cabinet 4 laci","asset_category_id":8,"department_id":5,"location_id":14,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Krisbow","model":"Tipe 242","serial_number":"PLG-991496","acquisition_date":"2019-06-20 00:00:00","acquisition_source":"pembelian","acquisition_cost":8555000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1208-2019-0001","id":19}	console	\N	\N	2026-09-06 14:04:04
135	\N	\N	created	App\\Models\\NumberSequence	19	asset.IT.1209 Kode aset IT akun 1209	\N	\N	{"code":"asset.IT.1209","name":"Kode aset IT akun 1209","prefix":"IT-1209","separator":"-","period_format":"Y","padding":4,"id":19}	console	\N	\N	2026-09-06 14:04:04
136	\N	\N	created	App\\Models\\Asset	20	IT-1209-2024-0001 Printer laser	\N	\N	{"name":"Printer laser","asset_category_id":9,"department_id":6,"location_id":11,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Dell","model":"Tipe 267","serial_number":"KOM-513380","acquisition_date":"2024-05-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":23390000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1209-2024-0001","id":20}	console	\N	\N	2026-09-06 14:04:04
137	\N	\N	created	App\\Models\\Asset	21	HRD-1212-2024-0001 AC split 2 PK	\N	\N	{"name":"AC split 2 PK","asset_category_id":12,"department_id":7,"location_id":18,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 388","serial_number":"PGU-234000","acquisition_date":"2024-09-08 00:00:00","acquisition_source":"pembelian","acquisition_cost":2023000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1212-2024-0001","id":21}	console	\N	\N	2026-09-06 14:04:04
138	\N	\N	created	App\\Models\\NumberSequence	20	asset.FIN.1209 Kode aset FIN akun 1209	\N	\N	{"code":"asset.FIN.1209","name":"Kode aset FIN akun 1209","prefix":"FIN-1209","separator":"-","period_format":"Y","padding":4,"id":20}	console	\N	\N	2026-09-06 14:04:04
139	\N	\N	created	App\\Models\\Asset	22	FIN-1209-2023-0001 PC desktop	\N	\N	{"name":"PC desktop","asset_category_id":9,"department_id":5,"location_id":9,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Lenovo","model":"Tipe 235","serial_number":"KOM-544873","acquisition_date":"2023-03-13 00:00:00","acquisition_source":"pembelian","acquisition_cost":17570000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1209-2023-0001","id":22}	console	\N	\N	2026-09-06 14:04:04
140	\N	\N	created	App\\Models\\NumberSequence	21	asset.GA.1209 Kode aset GA akun 1209	\N	\N	{"code":"asset.GA.1209","name":"Kode aset GA akun 1209","prefix":"GA-1209","separator":"-","period_format":"Y","padding":4,"id":21}	console	\N	\N	2026-09-06 14:04:04
141	\N	\N	created	App\\Models\\Asset	23	GA-1209-2023-0001 PC desktop	\N	\N	{"name":"PC desktop","asset_category_id":9,"department_id":4,"location_id":19,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Acer","model":"Tipe 144","serial_number":"KOM-421073","acquisition_date":"2023-09-06 00:00:00","acquisition_source":"pembelian","acquisition_cost":26641000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1209-2023-0001","id":23}	console	\N	\N	2026-09-06 14:04:04
142	\N	\N	created	App\\Models\\NumberSequence	22	asset.OPS.1205 Kode aset OPS akun 1205	\N	\N	{"code":"asset.OPS.1205","name":"Kode aset OPS akun 1205","prefix":"OPS-1205","separator":"-","period_format":"Y","padding":4,"id":22}	console	\N	\N	2026-09-06 14:04:04
143	\N	\N	created	App\\Models\\Asset	24	OPS-1205-2019-0001 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Isuzu Elf","model":"Tipe 548","serial_number":"KR4-767295","acquisition_date":"2019-06-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":319339000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2019-0001","id":24}	console	\N	\N	2026-09-06 14:04:04
144	\N	\N	created	App\\Models\\NumberSequence	23	asset.HRD.1207 Kode aset HRD akun 1207	\N	\N	{"code":"asset.HRD.1207","name":"Kode aset HRD akun 1207","prefix":"HRD-1207","separator":"-","period_format":"Y","padding":4,"id":23}	console	\N	\N	2026-09-06 14:04:04
145	\N	\N	created	App\\Models\\Asset	25	HRD-1207-2023-0001 Meja kerja kayu	\N	\N	{"name":"Meja kerja kayu","asset_category_id":7,"department_id":7,"location_id":7,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Activ","model":"Tipe 127","serial_number":"PKY-801625","acquisition_date":"2023-07-18 00:00:00","acquisition_source":"pembelian","acquisition_cost":8585000,"status":"perbaikan","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1207-2023-0001","id":25}	console	\N	\N	2026-09-06 14:04:04
146	\N	\N	created	App\\Models\\Asset	26	FIN-1212-2023-0001 AC split 1 PK	\N	\N	{"name":"AC split 1 PK","asset_category_id":12,"department_id":5,"location_id":11,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Daikin","model":"Tipe 808","serial_number":"PGU-427049","acquisition_date":"2023-08-09 00:00:00","acquisition_source":"pembelian","acquisition_cost":17949000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2023-0001","id":26}	console	\N	\N	2026-09-06 14:04:04
147	\N	\N	created	App\\Models\\NumberSequence	24	asset.IT.1210 Kode aset IT akun 1210	\N	\N	{"code":"asset.IT.1210","name":"Kode aset IT akun 1210","prefix":"IT-1210","separator":"-","period_format":"Y","padding":4,"id":24}	console	\N	\N	2026-09-06 14:04:04
148	\N	\N	created	App\\Models\\Asset	27	IT-1210-2025-0001 Mesin fotokopi	\N	\N	{"name":"Mesin fotokopi","asset_category_id":10,"department_id":6,"location_id":12,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Fuji Xerox","model":"Tipe 647","serial_number":"MSK-688346","acquisition_date":"2025-07-31 00:00:00","acquisition_source":"pembelian","acquisition_cost":52281000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1210-2025-0001","id":27}	console	\N	\N	2026-09-06 14:04:04
149	\N	\N	created	App\\Models\\NumberSequence	25	asset.GA.1206 Kode aset GA akun 1206	\N	\N	{"code":"asset.GA.1206","name":"Kode aset GA akun 1206","prefix":"GA-1206","separator":"-","period_format":"Y","padding":4,"id":25}	console	\N	\N	2026-09-06 14:04:04
150	\N	\N	created	App\\Models\\Asset	28	GA-1206-2018-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":4,"location_id":21,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 356","serial_number":"KR2-830764","acquisition_date":"2018-10-27 00:00:00","acquisition_source":"pembelian","acquisition_cost":30821000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1206-2018-0001","id":28}	console	\N	\N	2026-09-06 14:04:04
151	\N	\N	created	App\\Models\\Asset	29	HRD-1207-2019-0001 Meja kerja kayu	\N	\N	{"name":"Meja kerja kayu","asset_category_id":7,"department_id":7,"location_id":17,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 425","serial_number":"PKY-325720","acquisition_date":"2019-03-12 00:00:00","acquisition_source":"pembelian","acquisition_cost":17415000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1207-2019-0001","id":29}	console	\N	\N	2026-09-06 14:04:04
152	\N	\N	created	App\\Models\\NumberSequence	26	asset.OPS.1210 Kode aset OPS akun 1210	\N	\N	{"code":"asset.OPS.1210","name":"Kode aset OPS akun 1210","prefix":"OPS-1210","separator":"-","period_format":"Y","padding":4,"id":26}	console	\N	\N	2026-09-06 14:04:04
153	\N	\N	created	App\\Models\\Asset	30	OPS-1210-2026-0001 Proyektor ruang rapat	\N	\N	{"name":"Proyektor ruang rapat","asset_category_id":10,"department_id":8,"location_id":19,"custodian_employee_id":3,"asset_type":"bergerak","brand":"GBC","model":"Tipe 883","serial_number":"MSK-930859","acquisition_date":"2026-06-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":3185000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1210-2026-0001","id":30}	console	\N	\N	2026-09-06 14:04:04
154	\N	\N	created	App\\Models\\NumberSequence	27	asset.FIN.1211 Kode aset FIN akun 1211	\N	\N	{"code":"asset.FIN.1211","name":"Kode aset FIN akun 1211","prefix":"FIN-1211","separator":"-","period_format":"Y","padding":4,"id":27}	console	\N	\N	2026-09-06 14:04:04
155	\N	\N	created	App\\Models\\Asset	31	FIN-1211-2019-0001 Mesin faksimile	\N	\N	{"name":"Mesin faksimile","asset_category_id":11,"department_id":5,"location_id":6,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Polycom","model":"Tipe 271","serial_number":"ALK-694647","acquisition_date":"2019-07-24 00:00:00","acquisition_source":"pembelian","acquisition_cost":6496000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1211-2019-0001","id":31}	console	\N	\N	2026-09-06 14:04:04
156	\N	\N	created	App\\Models\\Asset	32	OPS-1211-2022-0001 Mesin faksimile	\N	\N	{"name":"Mesin faksimile","asset_category_id":11,"department_id":8,"location_id":6,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 451","serial_number":"ALK-438704","acquisition_date":"2022-08-17 00:00:00","acquisition_source":"hibah","acquisition_cost":1140000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1211-2022-0001","id":32}	console	\N	\N	2026-09-06 14:04:04
157	\N	\N	created	App\\Models\\NumberSequence	28	asset.HRD.1210 Kode aset HRD akun 1210	\N	\N	{"code":"asset.HRD.1210","name":"Kode aset HRD akun 1210","prefix":"HRD-1210","separator":"-","period_format":"Y","padding":4,"id":28}	console	\N	\N	2026-09-06 14:04:04
158	\N	\N	created	App\\Models\\Asset	33	HRD-1210-2024-0001 Mesin absensi sidik jari	\N	\N	{"name":"Mesin absensi sidik jari","asset_category_id":10,"department_id":7,"location_id":11,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Epson","model":"Tipe 695","serial_number":"MSK-440110","acquisition_date":"2024-05-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":36526000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1210-2024-0001","id":33}	console	\N	\N	2026-09-06 14:04:04
159	\N	\N	created	App\\Models\\Asset	34	FIN-1209-2018-0001 Laptop kerja	\N	\N	{"name":"Laptop kerja","asset_category_id":9,"department_id":5,"location_id":8,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Cisco","model":"Tipe 885","serial_number":"KOM-522986","acquisition_date":"2018-10-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":15185000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1209-2018-0001","id":34}	console	\N	\N	2026-09-06 14:04:04
160	\N	\N	created	App\\Models\\Asset	35	OPS-1205-2022-0001 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Isuzu Elf","model":"Tipe 254","serial_number":"KR4-148050","acquisition_date":"2022-07-18 00:00:00","acquisition_source":"pembelian","acquisition_cost":387811000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2022-0001","id":35}	console	\N	\N	2026-09-06 14:04:04
161	\N	\N	created	App\\Models\\Asset	36	FIN-1206-2023-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":5,"location_id":21,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 718","serial_number":"KR2-800223","acquisition_date":"2023-11-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":26437000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1206-2023-0001","id":36}	console	\N	\N	2026-09-06 14:04:05
162	\N	\N	created	App\\Models\\Asset	37	OPS-1205-2022-0002 Mobil boks pengiriman	\N	\N	{"name":"Mobil boks pengiriman","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Toyota Innova","model":"Tipe 425","serial_number":"KR4-459351","acquisition_date":"2022-03-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":292096000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2022-0002","id":37}	console	\N	\N	2026-09-06 14:04:05
163	\N	\N	created	App\\Models\\NumberSequence	29	asset.GA.1204 Kode aset GA akun 1204	\N	\N	{"code":"asset.GA.1204","name":"Kode aset GA akun 1204","prefix":"GA-1204","separator":"-","period_format":"Y","padding":4,"id":29}	console	\N	\N	2026-09-06 14:04:05
164	\N	\N	created	App\\Models\\Asset	38	GA-1204-2022-0001 Pompa air gedung	\N	\N	{"name":"Pompa air gedung","asset_category_id":4,"department_id":4,"location_id":5,"custodian_employee_id":null,"asset_type":"tetap","brand":"Appron","model":"Tipe 888","serial_number":"PRS-943739","acquisition_date":"2022-05-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":236797000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1204-2022-0001","id":38}	console	\N	\N	2026-09-06 14:04:05
165	\N	\N	created	App\\Models\\NumberSequence	30	asset.OPS.1209 Kode aset OPS akun 1209	\N	\N	{"code":"asset.OPS.1209","name":"Kode aset OPS akun 1209","prefix":"OPS-1209","separator":"-","period_format":"Y","padding":4,"id":30}	console	\N	\N	2026-09-06 14:04:05
166	\N	\N	created	App\\Models\\Asset	39	OPS-1209-2021-0001 Access point	\N	\N	{"name":"Access point","asset_category_id":9,"department_id":8,"location_id":18,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Dell","model":"Tipe 698","serial_number":"KOM-783047","acquisition_date":"2021-10-07 00:00:00","acquisition_source":"pembelian","acquisition_cost":8861000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1209-2021-0001","id":39}	console	\N	\N	2026-09-06 14:04:05
168	\N	\N	created	App\\Models\\Asset	40	OPS-1206-2021-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 788","serial_number":"KR2-348851","acquisition_date":"2021-07-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":22225000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2021-0001","id":40}	console	\N	\N	2026-09-06 14:04:05
169	\N	\N	created	App\\Models\\Asset	41	FIN-1211-2018-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":5,"location_id":13,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 266","serial_number":"ALK-132893","acquisition_date":"2018-09-07 00:00:00","acquisition_source":"pembelian","acquisition_cost":2758000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1211-2018-0001","id":41}	console	\N	\N	2026-09-06 14:04:05
170	\N	\N	created	App\\Models\\NumberSequence	32	asset.OPS.1204 Kode aset OPS akun 1204	\N	\N	{"code":"asset.OPS.1204","name":"Kode aset OPS akun 1204","prefix":"OPS-1204","separator":"-","period_format":"Y","padding":4,"id":32}	console	\N	\N	2026-09-06 14:04:05
171	\N	\N	created	App\\Models\\Asset	42	OPS-1204-2025-0001 Sistem pemadam kebakaran	\N	\N	{"name":"Sistem pemadam kebakaran","asset_category_id":4,"department_id":8,"location_id":5,"custodian_employee_id":null,"asset_type":"tetap","brand":"Grundfos","model":"Tipe 227","serial_number":"PRS-887972","acquisition_date":"2025-01-06 00:00:00","acquisition_source":"pembelian","acquisition_cost":245625000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1204-2025-0001","id":42}	console	\N	\N	2026-09-06 14:04:05
172	\N	\N	created	App\\Models\\Asset	43	OPS-1206-2023-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Honda Beat","model":"Tipe 523","serial_number":"KR2-834076","acquisition_date":"2023-01-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":27734000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2023-0001","id":43}	console	\N	\N	2026-09-06 14:04:05
173	\N	\N	created	App\\Models\\NumberSequence	33	asset.HRD.1211 Kode aset HRD akun 1211	\N	\N	{"code":"asset.HRD.1211","name":"Kode aset HRD akun 1211","prefix":"HRD-1211","separator":"-","period_format":"Y","padding":4,"id":33}	console	\N	\N	2026-09-06 14:04:05
174	\N	\N	created	App\\Models\\Asset	44	HRD-1211-2025-0001 Telepon konferensi	\N	\N	{"name":"Telepon konferensi","asset_category_id":11,"department_id":7,"location_id":16,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 380","serial_number":"ALK-293432","acquisition_date":"2025-02-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":5683000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1211-2025-0001","id":44}	console	\N	\N	2026-09-06 14:04:05
175	\N	\N	created	App\\Models\\Asset	45	GA-1207-2025-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":4,"location_id":16,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 134","serial_number":"PKY-498278","acquisition_date":"2025-08-15 00:00:00","acquisition_source":"pembelian","acquisition_cost":12448000,"status":"tidak_dipakai","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1207-2025-0001","id":45}	console	\N	\N	2026-09-06 14:04:05
176	\N	\N	created	App\\Models\\Asset	46	OPS-1206-2020-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 845","serial_number":"KR2-462373","acquisition_date":"2020-07-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":20699000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2020-0001","id":46}	console	\N	\N	2026-09-06 14:04:05
177	\N	\N	created	App\\Models\\NumberSequence	34	asset.OPS.1212 Kode aset OPS akun 1212	\N	\N	{"code":"asset.OPS.1212","name":"Kode aset OPS akun 1212","prefix":"OPS-1212","separator":"-","period_format":"Y","padding":4,"id":34}	console	\N	\N	2026-09-06 14:04:05
178	\N	\N	created	App\\Models\\Asset	47	OPS-1212-2024-0001 AC standing 3 PK	\N	\N	{"name":"AC standing 3 PK","asset_category_id":12,"department_id":8,"location_id":11,"custodian_employee_id":null,"asset_type":"bergerak","brand":"LG","model":"Tipe 619","serial_number":"PGU-670204","acquisition_date":"2024-11-13 00:00:00","acquisition_source":"pembelian","acquisition_cost":23418000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1212-2024-0001","id":47}	console	\N	\N	2026-09-06 14:04:05
179	\N	\N	created	App\\Models\\NumberSequence	35	asset.HRD.1206 Kode aset HRD akun 1206	\N	\N	{"code":"asset.HRD.1206","name":"Kode aset HRD akun 1206","prefix":"HRD-1206","separator":"-","period_format":"Y","padding":4,"id":35}	console	\N	\N	2026-09-06 14:04:05
180	\N	\N	created	App\\Models\\Asset	48	HRD-1206-2023-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":7,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 268","serial_number":"KR2-557701","acquisition_date":"2023-10-06 00:00:00","acquisition_source":"pembelian","acquisition_cost":21764000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1206-2023-0001","id":48}	console	\N	\N	2026-09-06 14:04:05
181	\N	\N	created	App\\Models\\NumberSequence	36	asset.HRD.1205 Kode aset HRD akun 1205	\N	\N	{"code":"asset.HRD.1205","name":"Kode aset HRD akun 1205","prefix":"HRD-1205","separator":"-","period_format":"Y","padding":4,"id":36}	console	\N	\N	2026-09-06 14:04:05
182	\N	\N	created	App\\Models\\Asset	49	HRD-1205-2026-0001 Kendaraan operasional	\N	\N	{"name":"Kendaraan operasional","asset_category_id":5,"department_id":7,"location_id":21,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Isuzu Elf","model":"Tipe 452","serial_number":"KR4-398925","acquisition_date":"2026-11-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":446673000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2026-0001","id":49}	console	\N	\N	2026-09-06 14:04:05
183	\N	\N	created	App\\Models\\Asset	50	GA-1207-2026-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":4,"location_id":19,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Activ","model":"Tipe 108","serial_number":"PKY-767166","acquisition_date":"2026-08-20 00:00:00","acquisition_source":"hibah","acquisition_cost":3535000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1207-2026-0001","id":50}	console	\N	\N	2026-09-06 14:04:05
184	\N	\N	created	App\\Models\\Asset	51	FIN-1211-2025-0001 Telepon konferensi	\N	\N	{"name":"Telepon konferensi","asset_category_id":11,"department_id":5,"location_id":13,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Motorola","model":"Tipe 732","serial_number":"ALK-170551","acquisition_date":"2025-03-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":3776000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1211-2025-0001","id":51}	console	\N	\N	2026-09-06 14:04:05
185	\N	\N	created	App\\Models\\NumberSequence	37	asset.FIN.1207 Kode aset FIN akun 1207	\N	\N	{"code":"asset.FIN.1207","name":"Kode aset FIN akun 1207","prefix":"FIN-1207","separator":"-","period_format":"Y","padding":4,"id":37}	console	\N	\N	2026-09-06 14:04:05
186	\N	\N	created	App\\Models\\Asset	52	FIN-1207-2026-0001 Rak buku kayu	\N	\N	{"name":"Rak buku kayu","asset_category_id":7,"department_id":5,"location_id":6,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Pro Design","model":"Tipe 565","serial_number":"PKY-650785","acquisition_date":"2026-04-05 00:00:00","acquisition_source":"pembelian","acquisition_cost":8540000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1207-2026-0001","id":52}	console	\N	\N	2026-09-06 14:04:05
187	\N	\N	created	App\\Models\\Asset	53	FIN-1209-2026-0001 Access point	\N	\N	{"name":"Access point","asset_category_id":9,"department_id":5,"location_id":7,"custodian_employee_id":13,"asset_type":"bergerak","brand":"TP-Link","model":"Tipe 390","serial_number":"KOM-167194","acquisition_date":"2026-02-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":7485000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1209-2026-0001","id":53}	console	\N	\N	2026-09-06 14:04:05
188	\N	\N	created	App\\Models\\Asset	54	FIN-1212-2024-0001 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":5,"location_id":7,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Mitsubishi","model":"Tipe 598","serial_number":"PGU-930591","acquisition_date":"2024-01-14 00:00:00","acquisition_source":"pembelian","acquisition_cost":17403000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2024-0001","id":54}	console	\N	\N	2026-09-06 14:04:05
189	\N	\N	created	App\\Models\\Asset	55	FIN-1212-2024-0002 Exhaust fan	\N	\N	{"name":"Exhaust fan","asset_category_id":12,"department_id":5,"location_id":19,"custodian_employee_id":10,"asset_type":"bergerak","brand":"LG","model":"Tipe 494","serial_number":"PGU-632265","acquisition_date":"2024-06-13 00:00:00","acquisition_source":"pembelian","acquisition_cost":25802000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2024-0002","id":55}	console	\N	\N	2026-09-06 14:04:05
190	\N	\N	created	App\\Models\\Asset	56	FIN-1208-2018-0001 Lemari besi arsip	\N	\N	{"name":"Lemari besi arsip","asset_category_id":8,"department_id":5,"location_id":9,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Krisbow","model":"Tipe 995","serial_number":"PLG-828408","acquisition_date":"2018-11-11 00:00:00","acquisition_source":"pembelian","acquisition_cost":5231000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1208-2018-0001","id":56}	console	\N	\N	2026-09-06 14:04:05
191	\N	\N	created	App\\Models\\Asset	57	IT-1209-2021-0001 Pemindai dokumen	\N	\N	{"name":"Pemindai dokumen","asset_category_id":9,"department_id":6,"location_id":null,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Cisco","model":"Tipe 345","serial_number":"KOM-158798","acquisition_date":"2021-06-24 00:00:00","acquisition_source":"pembelian","acquisition_cost":41454000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1209-2021-0001","id":57}	console	\N	\N	2026-09-06 14:04:05
192	\N	\N	created	App\\Models\\Asset	58	HRD-1209-2026-0001 Monitor 24 inci	\N	\N	{"name":"Monitor 24 inci","asset_category_id":9,"department_id":7,"location_id":14,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Cisco","model":"Tipe 610","serial_number":"KOM-928090","acquisition_date":"2026-05-11 00:00:00","acquisition_source":"pembelian","acquisition_cost":26774000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1209-2026-0001","id":58}	console	\N	\N	2026-09-06 14:04:05
193	\N	\N	created	App\\Models\\Asset	59	HRD-1210-2025-0001 Mesin penghancur kertas	\N	\N	{"name":"Mesin penghancur kertas","asset_category_id":10,"department_id":7,"location_id":7,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Canon","model":"Tipe 958","serial_number":"MSK-358713","acquisition_date":"2025-05-11 00:00:00","acquisition_source":"pembelian","acquisition_cost":38225000,"status":"dipinjam","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1210-2025-0001","id":59}	console	\N	\N	2026-09-06 14:04:05
194	\N	\N	created	App\\Models\\NumberSequence	38	asset.FIN.1204 Kode aset FIN akun 1204	\N	\N	{"code":"asset.FIN.1204","name":"Kode aset FIN akun 1204","prefix":"FIN-1204","separator":"-","period_format":"Y","padding":4,"id":38}	console	\N	\N	2026-09-06 14:04:05
195	\N	\N	created	App\\Models\\Asset	60	FIN-1204-2022-0001 Pompa air gedung	\N	\N	{"name":"Pompa air gedung","asset_category_id":4,"department_id":5,"location_id":15,"custodian_employee_id":null,"asset_type":"tetap","brand":"Schneider","model":"Tipe 412","serial_number":"PRS-471714","acquisition_date":"2022-01-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":277695000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1204-2022-0001","id":60}	console	\N	\N	2026-09-06 14:04:05
196	\N	\N	created	App\\Models\\Asset	61	HRD-1211-2023-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":7,"location_id":17,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 723","serial_number":"ALK-706164","acquisition_date":"2023-06-27 00:00:00","acquisition_source":"pembelian","acquisition_cost":4985000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1211-2023-0001","id":61}	console	\N	\N	2026-09-06 14:04:05
197	\N	\N	created	App\\Models\\Asset	62	OPS-1209-2019-0001 Pemindai dokumen	\N	\N	{"name":"Pemindai dokumen","asset_category_id":9,"department_id":8,"location_id":9,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Lenovo","model":"Tipe 372","serial_number":"KOM-530888","acquisition_date":"2019-02-27 00:00:00","acquisition_source":"pembelian","acquisition_cost":35038000,"status":"perbaikan","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1209-2019-0001","id":62}	console	\N	\N	2026-09-06 14:04:05
373	\N	\N	created	App\\Models\\SupplyTransaction	46	MP/2026/09/0046 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-5,"transaction_date":"2026-08-15 00:00:00","department_id":6,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0046","created_by_user_id":null,"id":46}	console	\N	\N	2026-09-06 21:23:55
198	\N	\N	created	App\\Models\\Asset	63	FIN-1212-2021-0001 AC split 2 PK	\N	\N	{"name":"AC split 2 PK","asset_category_id":12,"department_id":5,"location_id":8,"custodian_employee_id":4,"asset_type":"bergerak","brand":"LG","model":"Tipe 959","serial_number":"PGU-310633","acquisition_date":"2021-01-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":20209000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2021-0001","id":63}	console	\N	\N	2026-09-06 14:04:05
199	\N	\N	created	App\\Models\\Asset	64	IT-1206-2021-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":6,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 711","serial_number":"KR2-834965","acquisition_date":"2021-09-20 00:00:00","acquisition_source":"pembelian","acquisition_cost":24541000,"status":"tidak_dipakai","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1206-2021-0001","id":64}	console	\N	\N	2026-09-06 14:04:05
200	\N	\N	created	App\\Models\\Asset	65	OPS-1207-2025-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":8,"location_id":14,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 872","serial_number":"PKY-316476","acquisition_date":"2025-07-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":13003000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2025-0001","id":65}	console	\N	\N	2026-09-06 14:04:05
201	\N	\N	created	App\\Models\\Asset	66	OPS-1205-2025-0001 Kendaraan operasional	\N	\N	{"name":"Kendaraan operasional","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Daihatsu Gran Max","model":"Tipe 667","serial_number":"KR4-660854","acquisition_date":"2025-09-30 00:00:00","acquisition_source":"pembelian","acquisition_cost":376818000,"status":"tidak_dipakai","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2025-0001","id":66}	console	\N	\N	2026-09-06 14:04:05
202	\N	\N	created	App\\Models\\NumberSequence	39	asset.IT.1205 Kode aset IT akun 1205	\N	\N	{"code":"asset.IT.1205","name":"Kode aset IT akun 1205","prefix":"IT-1205","separator":"-","period_format":"Y","padding":4,"id":39}	console	\N	\N	2026-09-06 14:04:05
203	\N	\N	created	App\\Models\\Asset	67	IT-1205-2023-0001 Kendaraan operasional	\N	\N	{"name":"Kendaraan operasional","asset_category_id":5,"department_id":6,"location_id":21,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Toyota Avanza","model":"Tipe 860","serial_number":"KR4-156097","acquisition_date":"2023-08-08 00:00:00","acquisition_source":"pembelian","acquisition_cost":282204000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1205-2023-0001","id":67}	console	\N	\N	2026-09-06 14:04:05
204	\N	\N	created	App\\Models\\Asset	68	OPS-1208-2024-0002 Brankas	\N	\N	{"name":"Brankas","asset_category_id":8,"department_id":8,"location_id":12,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Elite","model":"Tipe 567","serial_number":"PLG-252336","acquisition_date":"2024-05-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":11019000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1208-2024-0002","id":68}	console	\N	\N	2026-09-06 14:04:05
205	\N	\N	created	App\\Models\\NumberSequence	40	asset.IT.1207 Kode aset IT akun 1207	\N	\N	{"code":"asset.IT.1207","name":"Kode aset IT akun 1207","prefix":"IT-1207","separator":"-","period_format":"Y","padding":4,"id":40}	console	\N	\N	2026-09-06 14:04:05
206	\N	\N	created	App\\Models\\Asset	69	IT-1207-2021-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":6,"location_id":9,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Pro Design","model":"Tipe 940","serial_number":"PKY-937062","acquisition_date":"2021-06-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":15456000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1207-2021-0001","id":69}	console	\N	\N	2026-09-06 14:04:05
207	\N	\N	created	App\\Models\\Asset	70	HRD-1205-2018-0001 Mobil boks pengiriman	\N	\N	{"name":"Mobil boks pengiriman","asset_category_id":5,"department_id":7,"location_id":21,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Toyota Innova","model":"Tipe 172","serial_number":"KR4-191112","acquisition_date":"2018-02-27 00:00:00","acquisition_source":"pembelian","acquisition_cost":503247000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2018-0001","id":70}	console	\N	\N	2026-09-06 14:04:05
208	\N	\N	created	App\\Models\\Asset	71	FIN-1210-2022-0001 Mesin absensi sidik jari	\N	\N	{"name":"Mesin absensi sidik jari","asset_category_id":10,"department_id":5,"location_id":7,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Fuji Xerox","model":"Tipe 937","serial_number":"MSK-651849","acquisition_date":"2022-09-11 00:00:00","acquisition_source":"pembelian","acquisition_cost":12468000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1210-2022-0001","id":71}	console	\N	\N	2026-09-06 14:04:05
209	\N	\N	created	App\\Models\\Asset	72	FIN-1209-2020-0001 Pemindai dokumen	\N	\N	{"name":"Pemindai dokumen","asset_category_id":9,"department_id":5,"location_id":19,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Acer","model":"Tipe 490","serial_number":"KOM-990880","acquisition_date":"2020-08-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":36753000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1209-2020-0001","id":72}	console	\N	\N	2026-09-06 14:04:05
210	\N	\N	created	App\\Models\\NumberSequence	41	asset.GA.1211 Kode aset GA akun 1211	\N	\N	{"code":"asset.GA.1211","name":"Kode aset GA akun 1211","prefix":"GA-1211","separator":"-","period_format":"Y","padding":4,"id":41}	console	\N	\N	2026-09-06 14:04:05
211	\N	\N	created	App\\Models\\Asset	73	GA-1211-2020-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":4,"location_id":11,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Polycom","model":"Tipe 703","serial_number":"ALK-561762","acquisition_date":"2020-11-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":5490000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1211-2020-0001","id":73}	console	\N	\N	2026-09-06 14:04:05
212	\N	\N	created	App\\Models\\Asset	74	GA-1207-2023-0001 Lemari arsip kayu	\N	\N	{"name":"Lemari arsip kayu","asset_category_id":7,"department_id":4,"location_id":12,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 762","serial_number":"PKY-137580","acquisition_date":"2023-05-02 00:00:00","acquisition_source":"pembelian","acquisition_cost":9702000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1207-2023-0001","id":74}	console	\N	\N	2026-09-06 14:04:05
213	\N	\N	created	App\\Models\\Asset	75	GA-1209-2025-0001 Switch 24 port	\N	\N	{"name":"Switch 24 port","asset_category_id":9,"department_id":4,"location_id":13,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Acer","model":"Tipe 780","serial_number":"KOM-419837","acquisition_date":"2025-10-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":32238000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1209-2025-0001","id":75}	console	\N	\N	2026-09-06 14:04:05
214	\N	\N	created	App\\Models\\Asset	76	HRD-1207-2023-0002 Lemari arsip kayu	\N	\N	{"name":"Lemari arsip kayu","asset_category_id":7,"department_id":7,"location_id":16,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 929","serial_number":"PKY-965451","acquisition_date":"2023-06-09 00:00:00","acquisition_source":"pembelian","acquisition_cost":4699000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1207-2023-0002","id":76}	console	\N	\N	2026-09-06 14:04:05
215	\N	\N	created	App\\Models\\Asset	77	HRD-1205-2026-0002 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":7,"location_id":21,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Isuzu Elf","model":"Tipe 372","serial_number":"KR4-319485","acquisition_date":"2026-06-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":472518000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2026-0002","id":77}	console	\N	\N	2026-09-06 14:04:05
216	\N	\N	created	App\\Models\\Asset	78	FIN-1212-2021-0002 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":5,"location_id":7,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Sharp","model":"Tipe 933","serial_number":"PGU-887969","acquisition_date":"2021-04-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":7123000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2021-0002","id":78}	console	\N	\N	2026-09-06 14:04:05
217	\N	\N	created	App\\Models\\Asset	79	FIN-1209-2018-0002 PC desktop	\N	\N	{"name":"PC desktop","asset_category_id":9,"department_id":5,"location_id":16,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Acer","model":"Tipe 712","serial_number":"KOM-718969","acquisition_date":"2018-10-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":27586000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1209-2018-0002","id":79}	console	\N	\N	2026-09-06 14:04:05
218	\N	\N	created	App\\Models\\Asset	80	OPS-1207-2018-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":8,"location_id":12,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Activ","model":"Tipe 153","serial_number":"PKY-891824","acquisition_date":"2018-09-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":15810000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2018-0001","id":80}	console	\N	\N	2026-09-06 14:04:05
219	\N	\N	created	App\\Models\\Asset	81	GA-1209-2024-0001 Server rak	\N	\N	{"name":"Server rak","asset_category_id":9,"department_id":4,"location_id":7,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Acer","model":"Tipe 379","serial_number":"KOM-111109","acquisition_date":"2024-01-06 00:00:00","acquisition_source":"pembelian","acquisition_cost":16089000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1209-2024-0001","id":81}	console	\N	\N	2026-09-06 14:04:05
220	\N	\N	created	App\\Models\\Asset	82	FIN-1210-2020-0001 Mesin fotokopi	\N	\N	{"name":"Mesin fotokopi","asset_category_id":10,"department_id":5,"location_id":11,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Kyocera","model":"Tipe 119","serial_number":"MSK-857820","acquisition_date":"2020-07-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":43978000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1210-2020-0001","id":82}	console	\N	\N	2026-09-06 14:04:05
221	\N	\N	created	App\\Models\\Asset	83	OPS-1208-2018-0001 Loker karyawan	\N	\N	{"name":"Loker karyawan","asset_category_id":8,"department_id":8,"location_id":16,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Krisbow","model":"Tipe 462","serial_number":"PLG-413843","acquisition_date":"2018-09-20 00:00:00","acquisition_source":"hibah","acquisition_cost":20011000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1208-2018-0001","id":83}	console	\N	\N	2026-09-06 14:04:05
222	\N	\N	created	App\\Models\\Asset	84	FIN-1206-2021-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":5,"location_id":21,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Honda Beat","model":"Tipe 469","serial_number":"KR2-294603","acquisition_date":"2021-10-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":24951000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1206-2021-0001","id":84}	console	\N	\N	2026-09-06 14:04:05
223	\N	\N	created	App\\Models\\Asset	85	HRD-1207-2018-0001 Meja kerja kayu	\N	\N	{"name":"Meja kerja kayu","asset_category_id":7,"department_id":7,"location_id":7,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 112","serial_number":"PKY-267850","acquisition_date":"2018-11-10 00:00:00","acquisition_source":"pembelian","acquisition_cost":15726000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1207-2018-0001","id":85}	console	\N	\N	2026-09-06 14:04:05
224	\N	\N	created	App\\Models\\Asset	86	HRD-1209-2022-0001 Monitor 24 inci	\N	\N	{"name":"Monitor 24 inci","asset_category_id":9,"department_id":7,"location_id":12,"custodian_employee_id":3,"asset_type":"bergerak","brand":"HP","model":"Tipe 886","serial_number":"KOM-897682","acquisition_date":"2022-03-08 00:00:00","acquisition_source":"pembelian","acquisition_cost":16691000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1209-2022-0001","id":86}	console	\N	\N	2026-09-06 14:04:05
225	\N	\N	created	App\\Models\\Asset	87	FIN-1207-2018-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":5,"location_id":12,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 836","serial_number":"PKY-117415","acquisition_date":"2018-01-27 00:00:00","acquisition_source":"pembelian","acquisition_cost":12086000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1207-2018-0001","id":87}	console	\N	\N	2026-09-06 14:04:05
226	\N	\N	created	App\\Models\\NumberSequence	42	asset.GA.1212 Kode aset GA akun 1212	\N	\N	{"code":"asset.GA.1212","name":"Kode aset GA akun 1212","prefix":"GA-1212","separator":"-","period_format":"Y","padding":4,"id":42}	console	\N	\N	2026-09-06 14:04:05
227	\N	\N	created	App\\Models\\Asset	88	GA-1212-2026-0001 AC split 1 PK	\N	\N	{"name":"AC split 1 PK","asset_category_id":12,"department_id":4,"location_id":14,"custodian_employee_id":9,"asset_type":"bergerak","brand":"LG","model":"Tipe 659","serial_number":"PGU-876104","acquisition_date":"2026-01-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":18024000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1212-2026-0001","id":88}	console	\N	\N	2026-09-06 14:04:05
228	\N	\N	created	App\\Models\\Asset	89	OPS-1212-2025-0001 AC standing 3 PK	\N	\N	{"name":"AC standing 3 PK","asset_category_id":12,"department_id":8,"location_id":16,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 444","serial_number":"PGU-537274","acquisition_date":"2025-03-25 00:00:00","acquisition_source":"pembelian","acquisition_cost":15735000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1212-2025-0001","id":89}	console	\N	\N	2026-09-06 14:04:05
229	\N	\N	created	App\\Models\\Asset	90	OPS-1206-2018-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 535","serial_number":"KR2-184913","acquisition_date":"2018-06-13 00:00:00","acquisition_source":"pembelian","acquisition_cost":22759000,"status":"dipinjam","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2018-0001","id":90}	console	\N	\N	2026-09-06 14:04:05
230	\N	\N	created	App\\Models\\Asset	91	GA-1206-2020-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":4,"location_id":21,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 332","serial_number":"KR2-833744","acquisition_date":"2020-11-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":33392000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1206-2020-0001","id":91}	console	\N	\N	2026-09-06 14:04:05
231	\N	\N	created	App\\Models\\Asset	92	HRD-1208-2023-0001 Lemari besi arsip	\N	\N	{"name":"Lemari besi arsip","asset_category_id":8,"department_id":7,"location_id":19,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Elite","model":"Tipe 416","serial_number":"PLG-241406","acquisition_date":"2023-06-24 00:00:00","acquisition_source":"pembelian","acquisition_cost":18854000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1208-2023-0001","id":92}	console	\N	\N	2026-09-06 14:04:05
232	\N	\N	created	App\\Models\\Asset	93	OPS-1206-2023-0002 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 983","serial_number":"KR2-768967","acquisition_date":"2023-08-09 00:00:00","acquisition_source":"hibah","acquisition_cost":25150000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2023-0002","id":93}	console	\N	\N	2026-09-06 14:04:05
233	\N	\N	created	App\\Models\\Asset	94	IT-1207-2022-0001 Meja rapat kayu	\N	\N	{"name":"Meja rapat kayu","asset_category_id":7,"department_id":6,"location_id":14,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Activ","model":"Tipe 250","serial_number":"PKY-400314","acquisition_date":"2022-03-14 00:00:00","acquisition_source":"pembelian","acquisition_cost":11420000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1207-2022-0001","id":94}	console	\N	\N	2026-09-06 14:04:05
234	\N	\N	created	App\\Models\\Asset	95	HRD-1208-2018-0002 Loker karyawan	\N	\N	{"name":"Loker karyawan","asset_category_id":8,"department_id":7,"location_id":null,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Daichiban","model":"Tipe 567","serial_number":"PLG-734374","acquisition_date":"2018-06-02 00:00:00","acquisition_source":"pembelian","acquisition_cost":15045000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1208-2018-0002","id":95}	console	\N	\N	2026-09-06 14:04:05
235	\N	\N	created	App\\Models\\Asset	96	FIN-1206-2023-0002 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":5,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Honda Beat","model":"Tipe 249","serial_number":"KR2-562739","acquisition_date":"2023-04-01 00:00:00","acquisition_source":"pembelian","acquisition_cost":20365000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1206-2023-0002","id":96}	console	\N	\N	2026-09-06 14:04:05
236	\N	\N	created	App\\Models\\Asset	97	IT-1204-2021-0001 Instalasi listrik lantai	\N	\N	{"name":"Instalasi listrik lantai","asset_category_id":4,"department_id":6,"location_id":15,"custodian_employee_id":null,"asset_type":"tetap","brand":"Perkins","model":"Tipe 482","serial_number":"PRS-497527","acquisition_date":"2021-09-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":196345000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1204-2021-0001","id":97}	console	\N	\N	2026-09-06 14:04:05
237	\N	\N	created	App\\Models\\Asset	98	HRD-1205-2018-0002 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":7,"location_id":21,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Toyota Innova","model":"Tipe 169","serial_number":"KR4-352102","acquisition_date":"2018-03-05 00:00:00","acquisition_source":"pembelian","acquisition_cost":222934000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2018-0002","id":98}	console	\N	\N	2026-09-06 14:04:05
238	\N	\N	created	App\\Models\\Asset	99	OPS-1207-2026-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":8,"location_id":11,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Pro Design","model":"Tipe 467","serial_number":"PKY-984682","acquisition_date":"2026-02-09 00:00:00","acquisition_source":"pembelian","acquisition_cost":9144000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2026-0001","id":99}	console	\N	\N	2026-09-06 14:04:05
239	\N	\N	created	App\\Models\\Asset	100	HRD-1212-2025-0001 Exhaust fan	\N	\N	{"name":"Exhaust fan","asset_category_id":12,"department_id":7,"location_id":16,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 785","serial_number":"PGU-619877","acquisition_date":"2025-11-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":9738000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1212-2025-0001","id":100}	console	\N	\N	2026-09-06 14:04:05
374	\N	\N	created	App\\Models\\SupplyItem	9	ATK-0009 Stapler ukuran sedang	\N	\N	{"name":"Stapler ukuran sedang","category":"alat_tulis","unit":"pcs","minimum_stock":5,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0009","id":9}	console	\N	\N	2026-09-06 21:23:55
240	\N	\N	created	App\\Models\\Asset	101	GA-1208-2021-0001 Lemari besi arsip	\N	\N	{"name":"Lemari besi arsip","asset_category_id":8,"department_id":4,"location_id":13,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Elite","model":"Tipe 377","serial_number":"PLG-141414","acquisition_date":"2021-06-06 00:00:00","acquisition_source":"pembelian","acquisition_cost":3136000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1208-2021-0001","id":101}	console	\N	\N	2026-09-06 14:04:05
241	\N	\N	created	App\\Models\\Asset	102	HRD-1206-2021-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":7,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 445","serial_number":"KR2-490615","acquisition_date":"2021-05-03 00:00:00","acquisition_source":"pembelian","acquisition_cost":28638000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1206-2021-0001","id":102}	console	\N	\N	2026-09-06 14:04:05
242	\N	\N	created	App\\Models\\Asset	103	OPS-1205-2023-0001 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Mitsubishi L300","model":"Tipe 617","serial_number":"KR4-717224","acquisition_date":"2023-01-07 00:00:00","acquisition_source":"hibah","acquisition_cost":402814000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2023-0001","id":103}	console	\N	\N	2026-09-06 14:04:05
243	\N	\N	created	App\\Models\\Asset	104	HRD-1209-2021-0001 Laptop kerja	\N	\N	{"name":"Laptop kerja","asset_category_id":9,"department_id":7,"location_id":18,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Dell","model":"Tipe 283","serial_number":"KOM-813131","acquisition_date":"2021-09-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":18730000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1209-2021-0001","id":104}	console	\N	\N	2026-09-06 14:04:05
244	\N	\N	created	App\\Models\\Asset	105	OPS-1211-2020-0001 Telepon konferensi	\N	\N	{"name":"Telepon konferensi","asset_category_id":11,"department_id":8,"location_id":7,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 613","serial_number":"ALK-873989","acquisition_date":"2020-07-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":6586000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1211-2020-0001","id":105}	console	\N	\N	2026-09-06 14:04:05
245	\N	\N	created	App\\Models\\Asset	106	IT-1207-2026-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":6,"location_id":7,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Pro Design","model":"Tipe 273","serial_number":"PKY-719670","acquisition_date":"2026-03-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":9826000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1207-2026-0001","id":106}	console	\N	\N	2026-09-06 14:04:05
246	\N	\N	created	App\\Models\\Asset	107	GA-1206-2021-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":4,"location_id":null,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 764","serial_number":"KR2-710975","acquisition_date":"2021-09-10 00:00:00","acquisition_source":"pembelian","acquisition_cost":27260000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1206-2021-0001","id":107}	console	\N	\N	2026-09-06 14:04:05
247	\N	\N	created	App\\Models\\Asset	108	HRD-1211-2020-0001 Mesin faksimile	\N	\N	{"name":"Mesin faksimile","asset_category_id":11,"department_id":7,"location_id":12,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Motorola","model":"Tipe 892","serial_number":"ALK-762370","acquisition_date":"2020-08-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":7173000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1211-2020-0001","id":108}	console	\N	\N	2026-09-06 14:04:05
248	\N	\N	created	App\\Models\\Asset	109	OPS-1209-2020-0001 PC desktop	\N	\N	{"name":"PC desktop","asset_category_id":9,"department_id":8,"location_id":14,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Asus","model":"Tipe 411","serial_number":"KOM-244761","acquisition_date":"2020-11-22 00:00:00","acquisition_source":"pembelian","acquisition_cost":5711000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1209-2020-0001","id":109}	console	\N	\N	2026-09-06 14:04:05
249	\N	\N	created	App\\Models\\Asset	110	HRD-1206-2024-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":7,"location_id":21,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 359","serial_number":"KR2-601307","acquisition_date":"2024-04-05 00:00:00","acquisition_source":"pembelian","acquisition_cost":34385000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1206-2024-0001","id":110}	console	\N	\N	2026-09-06 14:04:05
250	\N	\N	created	App\\Models\\NumberSequence	43	asset.IT.1211 Kode aset IT akun 1211	\N	\N	{"code":"asset.IT.1211","name":"Kode aset IT akun 1211","prefix":"IT-1211","separator":"-","period_format":"Y","padding":4,"id":43}	console	\N	\N	2026-09-06 14:04:05
251	\N	\N	created	App\\Models\\Asset	111	IT-1211-2019-0001 Telepon konferensi	\N	\N	{"name":"Telepon konferensi","asset_category_id":11,"department_id":6,"location_id":16,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 424","serial_number":"ALK-195922","acquisition_date":"2019-01-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":3237000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1211-2019-0001","id":111}	console	\N	\N	2026-09-06 14:04:05
252	\N	\N	created	App\\Models\\Asset	112	GA-1211-2018-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":4,"location_id":17,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Motorola","model":"Tipe 155","serial_number":"ALK-252726","acquisition_date":"2018-06-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":2924000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1211-2018-0001","id":112}	console	\N	\N	2026-09-06 14:04:05
253	\N	\N	created	App\\Models\\Asset	113	HRD-1206-2020-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":7,"location_id":21,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 269","serial_number":"KR2-238035","acquisition_date":"2020-02-02 00:00:00","acquisition_source":"pembelian","acquisition_cost":29645000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1206-2020-0001","id":113}	console	\N	\N	2026-09-06 14:04:05
254	\N	\N	created	App\\Models\\Asset	114	HRD-1212-2019-0001 AC split 1 PK	\N	\N	{"name":"AC split 1 PK","asset_category_id":12,"department_id":7,"location_id":7,"custodian_employee_id":7,"asset_type":"bergerak","brand":"LG","model":"Tipe 806","serial_number":"PGU-576421","acquisition_date":"2019-01-14 00:00:00","acquisition_source":"pembelian","acquisition_cost":7810000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1212-2019-0001","id":114}	console	\N	\N	2026-09-06 14:04:05
255	\N	\N	created	App\\Models\\Asset	115	GA-1208-2021-0002 Loker karyawan	\N	\N	{"name":"Loker karyawan","asset_category_id":8,"department_id":4,"location_id":7,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Daichiban","model":"Tipe 809","serial_number":"PLG-869068","acquisition_date":"2021-03-21 00:00:00","acquisition_source":"pembelian","acquisition_cost":9659000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1208-2021-0002","id":115}	console	\N	\N	2026-09-06 14:04:05
256	\N	\N	created	App\\Models\\Asset	116	OPS-1209-2026-0001 Switch 24 port	\N	\N	{"name":"Switch 24 port","asset_category_id":9,"department_id":8,"location_id":7,"custodian_employee_id":8,"asset_type":"bergerak","brand":"Asus","model":"Tipe 503","serial_number":"KOM-948110","acquisition_date":"2026-07-08 00:00:00","acquisition_source":"pembelian","acquisition_cost":33213000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1209-2026-0001","id":116}	console	\N	\N	2026-09-06 14:04:06
257	\N	\N	created	App\\Models\\Asset	117	GA-1207-2018-0001 Lemari arsip kayu	\N	\N	{"name":"Lemari arsip kayu","asset_category_id":7,"department_id":4,"location_id":19,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 645","serial_number":"PKY-432289","acquisition_date":"2018-11-14 00:00:00","acquisition_source":"pembelian","acquisition_cost":1044000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1207-2018-0001","id":117}	console	\N	\N	2026-09-06 14:04:06
258	\N	\N	created	App\\Models\\Asset	118	HRD-1210-2018-0001 Mesin fotokopi	\N	\N	{"name":"Mesin fotokopi","asset_category_id":10,"department_id":7,"location_id":8,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Kyocera","model":"Tipe 625","serial_number":"MSK-922759","acquisition_date":"2018-08-24 00:00:00","acquisition_source":"pembelian","acquisition_cost":3986000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1210-2018-0001","id":118}	console	\N	\N	2026-09-06 14:04:06
259	\N	\N	created	App\\Models\\Asset	119	GA-1206-2022-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":4,"location_id":null,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 636","serial_number":"KR2-818500","acquisition_date":"2022-08-01 00:00:00","acquisition_source":"pembelian","acquisition_cost":20672000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1206-2022-0001","id":119}	console	\N	\N	2026-09-06 14:04:06
260	\N	\N	created	App\\Models\\Asset	120	IT-1207-2018-0001 Meja kerja kayu	\N	\N	{"name":"Meja kerja kayu","asset_category_id":7,"department_id":6,"location_id":null,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 143","serial_number":"PKY-764977","acquisition_date":"2018-03-10 00:00:00","acquisition_source":"pembelian","acquisition_cost":15575000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1207-2018-0001","id":120}	console	\N	\N	2026-09-06 14:04:06
261	\N	\N	created	App\\Models\\Asset	121	GA-1209-2020-0001 Pemindai dokumen	\N	\N	{"name":"Pemindai dokumen","asset_category_id":9,"department_id":4,"location_id":16,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Asus","model":"Tipe 716","serial_number":"KOM-854659","acquisition_date":"2020-06-28 00:00:00","acquisition_source":"pembelian","acquisition_cost":32116000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1209-2020-0001","id":121}	console	\N	\N	2026-09-06 14:04:06
262	\N	\N	created	App\\Models\\NumberSequence	44	asset.GA.1205 Kode aset GA akun 1205	\N	\N	{"code":"asset.GA.1205","name":"Kode aset GA akun 1205","prefix":"GA-1205","separator":"-","period_format":"Y","padding":4,"id":44}	console	\N	\N	2026-09-06 14:04:06
263	\N	\N	created	App\\Models\\Asset	122	GA-1205-2020-0001 Mobil boks pengiriman	\N	\N	{"name":"Mobil boks pengiriman","asset_category_id":5,"department_id":4,"location_id":21,"custodian_employee_id":4,"asset_type":"bergerak","brand":"Mitsubishi L300","model":"Tipe 784","serial_number":"KR4-719561","acquisition_date":"2020-08-29 00:00:00","acquisition_source":"pembelian","acquisition_cost":260015000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1205-2020-0001","id":122}	console	\N	\N	2026-09-06 14:04:06
264	\N	\N	created	App\\Models\\Asset	123	OPS-1207-2023-0001 Rak buku kayu	\N	\N	{"name":"Rak buku kayu","asset_category_id":7,"department_id":8,"location_id":17,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Activ","model":"Tipe 267","serial_number":"PKY-888494","acquisition_date":"2023-03-12 00:00:00","acquisition_source":"pembelian","acquisition_cost":10317000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2023-0001","id":123}	console	\N	\N	2026-09-06 14:04:06
265	\N	\N	created	App\\Models\\Asset	124	IT-1204-2020-0001 Instalasi listrik lantai	\N	\N	{"name":"Instalasi listrik lantai","asset_category_id":4,"department_id":6,"location_id":15,"custodian_employee_id":null,"asset_type":"tetap","brand":"Schneider","model":"Tipe 823","serial_number":"PRS-458938","acquisition_date":"2020-04-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":465295000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1204-2020-0001","id":124}	console	\N	\N	2026-09-06 14:04:06
266	\N	\N	created	App\\Models\\Asset	125	IT-1207-2026-0002 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":6,"location_id":null,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 695","serial_number":"PKY-993297","acquisition_date":"2026-09-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":13150000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1207-2026-0002","id":125}	console	\N	\N	2026-09-06 14:04:06
267	\N	\N	created	App\\Models\\Asset	126	OPS-1204-2023-0001 Sistem pemadam kebakaran	\N	\N	{"name":"Sistem pemadam kebakaran","asset_category_id":4,"department_id":8,"location_id":10,"custodian_employee_id":null,"asset_type":"tetap","brand":"Schneider","model":"Tipe 813","serial_number":"PRS-277106","acquisition_date":"2023-01-18 00:00:00","acquisition_source":"pembelian","acquisition_cost":195257000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1204-2023-0001","id":126}	console	\N	\N	2026-09-06 14:04:06
268	\N	\N	created	App\\Models\\Asset	127	GA-1209-2026-0001 Access point	\N	\N	{"name":"Access point","asset_category_id":9,"department_id":4,"location_id":16,"custodian_employee_id":3,"asset_type":"bergerak","brand":"Acer","model":"Tipe 982","serial_number":"KOM-944868","acquisition_date":"2026-04-10 00:00:00","acquisition_source":"pembelian","acquisition_cost":24819000,"status":"perbaikan","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1209-2026-0001","id":127}	console	\N	\N	2026-09-06 14:04:06
269	\N	\N	created	App\\Models\\Asset	128	FIN-1212-2024-0003 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":5,"location_id":18,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Mitsubishi","model":"Tipe 344","serial_number":"PGU-345963","acquisition_date":"2024-03-29 00:00:00","acquisition_source":"hibah","acquisition_cost":21406000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2024-0003","id":128}	console	\N	\N	2026-09-06 14:04:06
270	\N	\N	created	App\\Models\\Asset	129	FIN-1208-2020-0001 Rak arsip besi	\N	\N	{"name":"Rak arsip besi","asset_category_id":8,"department_id":5,"location_id":6,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Elite","model":"Tipe 318","serial_number":"PLG-656042","acquisition_date":"2020-08-29 00:00:00","acquisition_source":"pembelian","acquisition_cost":5606000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1208-2020-0001","id":129}	console	\N	\N	2026-09-06 14:04:06
271	\N	\N	created	App\\Models\\Asset	130	FIN-1206-2020-0001 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":5,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Honda PCX","model":"Tipe 647","serial_number":"KR2-689535","acquisition_date":"2020-01-26 00:00:00","acquisition_source":"hibah","acquisition_cost":32948000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1206-2020-0001","id":130}	console	\N	\N	2026-09-06 14:04:06
272	\N	\N	created	App\\Models\\Asset	131	GA-1208-2025-0001 Filing cabinet 4 laci	\N	\N	{"name":"Filing cabinet 4 laci","asset_category_id":8,"department_id":4,"location_id":11,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Brother","model":"Tipe 745","serial_number":"PLG-539789","acquisition_date":"2025-08-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":11037000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1208-2025-0001","id":131}	console	\N	\N	2026-09-06 14:04:06
273	\N	\N	created	App\\Models\\Asset	132	IT-1211-2026-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":6,"location_id":12,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 238","serial_number":"ALK-436159","acquisition_date":"2026-09-02 00:00:00","acquisition_source":"pembelian","acquisition_cost":7893000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"IT-1211-2026-0001","id":132}	console	\N	\N	2026-09-06 14:04:06
274	\N	\N	created	App\\Models\\Asset	133	GA-1206-2020-0002 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":4,"location_id":21,"custodian_employee_id":7,"asset_type":"bergerak","brand":"Honda Vario","model":"Tipe 844","serial_number":"KR2-924619","acquisition_date":"2020-04-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":19951000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1206-2020-0002","id":133}	console	\N	\N	2026-09-06 14:04:06
275	\N	\N	created	App\\Models\\Asset	134	HRD-1211-2022-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":7,"location_id":11,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 189","serial_number":"ALK-623926","acquisition_date":"2022-08-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":6980000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1211-2022-0001","id":134}	console	\N	\N	2026-09-06 14:04:06
276	\N	\N	created	App\\Models\\Asset	135	GA-1205-2023-0001 Kendaraan operasional	\N	\N	{"name":"Kendaraan operasional","asset_category_id":5,"department_id":4,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Daihatsu Gran Max","model":"Tipe 660","serial_number":"KR4-791067","acquisition_date":"2023-03-26 00:00:00","acquisition_source":"hibah","acquisition_cost":293113000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1205-2023-0001","id":135}	console	\N	\N	2026-09-06 14:04:06
277	\N	\N	created	App\\Models\\Asset	136	OPS-1206-2023-0003 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 597","serial_number":"KR2-920300","acquisition_date":"2023-11-09 00:00:00","acquisition_source":"hibah","acquisition_cost":21380000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2023-0003","id":136}	console	\N	\N	2026-09-06 14:04:06
278	\N	\N	created	App\\Models\\Asset	137	OPS-1205-2022-0003 Mobil boks pengiriman	\N	\N	{"name":"Mobil boks pengiriman","asset_category_id":5,"department_id":8,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Toyota Avanza","model":"Tipe 917","serial_number":"KR4-319178","acquisition_date":"2022-11-05 00:00:00","acquisition_source":"hibah","acquisition_cost":440885000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1205-2022-0003","id":137}	console	\N	\N	2026-09-06 14:04:06
279	\N	\N	created	App\\Models\\Asset	138	FIN-1211-2024-0001 Mesin faksimile	\N	\N	{"name":"Mesin faksimile","asset_category_id":11,"department_id":5,"location_id":8,"custodian_employee_id":9,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 104","serial_number":"ALK-573404","acquisition_date":"2024-08-24 00:00:00","acquisition_source":"hibah","acquisition_cost":3583000,"status":"aktif","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1211-2024-0001","id":138}	console	\N	\N	2026-09-06 14:04:06
280	\N	\N	created	App\\Models\\Asset	139	FIN-1212-2018-0002 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":5,"location_id":14,"custodian_employee_id":3,"asset_type":"bergerak","brand":"LG","model":"Tipe 431","serial_number":"PGU-492088","acquisition_date":"2018-08-25 00:00:00","acquisition_source":"pembelian","acquisition_cost":16554000,"status":"tidak_dipakai","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1212-2018-0002","id":139}	console	\N	\N	2026-09-06 14:04:06
281	\N	\N	created	App\\Models\\Asset	140	HRD-1206-2024-0002 Sepeda motor kurir	\N	\N	{"name":"Sepeda motor kurir","asset_category_id":6,"department_id":7,"location_id":21,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Honda Beat","model":"Tipe 102","serial_number":"KR2-312991","acquisition_date":"2024-04-29 00:00:00","acquisition_source":"pembelian","acquisition_cost":29320000,"status":"tidak_dipakai","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1206-2024-0002","id":140}	console	\N	\N	2026-09-06 14:04:06
282	\N	\N	created	App\\Models\\Asset	141	FIN-1207-2022-0001 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":5,"location_id":13,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Olympic","model":"Tipe 339","serial_number":"PKY-295363","acquisition_date":"2022-01-23 00:00:00","acquisition_source":"pembelian","acquisition_cost":9996000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1207-2022-0001","id":141}	console	\N	\N	2026-09-06 14:04:06
283	\N	\N	created	App\\Models\\Asset	142	HRD-1209-2020-0001 Laptop kerja	\N	\N	{"name":"Laptop kerja","asset_category_id":9,"department_id":7,"location_id":19,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Asus","model":"Tipe 815","serial_number":"KOM-180894","acquisition_date":"2020-07-07 00:00:00","acquisition_source":"hibah","acquisition_cost":5797000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1209-2020-0001","id":142}	console	\N	\N	2026-09-06 14:04:06
284	\N	\N	created	App\\Models\\Asset	143	HRD-1205-2024-0001 Mobil boks pengiriman	\N	\N	{"name":"Mobil boks pengiriman","asset_category_id":5,"department_id":7,"location_id":null,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Toyota Innova","model":"Tipe 199","serial_number":"KR4-197257","acquisition_date":"2024-07-04 00:00:00","acquisition_source":"pembelian","acquisition_cost":516763000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2024-0001","id":143}	console	\N	\N	2026-09-06 14:04:06
285	\N	\N	created	App\\Models\\Asset	144	HRD-1205-2020-0001 Kendaraan operasional	\N	\N	{"name":"Kendaraan operasional","asset_category_id":5,"department_id":7,"location_id":21,"custodian_employee_id":2,"asset_type":"bergerak","brand":"Daihatsu Gran Max","model":"Tipe 963","serial_number":"KR4-534523","acquisition_date":"2020-05-05 00:00:00","acquisition_source":"pembelian","acquisition_cost":242968000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1205-2020-0001","id":144}	console	\N	\N	2026-09-06 14:04:06
286	\N	\N	created	App\\Models\\Asset	145	HRD-1210-2023-0001 Mesin penghancur kertas	\N	\N	{"name":"Mesin penghancur kertas","asset_category_id":10,"department_id":7,"location_id":18,"custodian_employee_id":11,"asset_type":"bergerak","brand":"GBC","model":"Tipe 298","serial_number":"MSK-852908","acquisition_date":"2023-07-06 00:00:00","acquisition_source":"hibah","acquisition_cost":37807000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1210-2023-0001","id":145}	console	\N	\N	2026-09-06 14:04:06
287	\N	\N	created	App\\Models\\Asset	146	HRD-1210-2018-0002 Mesin absensi sidik jari	\N	\N	{"name":"Mesin absensi sidik jari","asset_category_id":10,"department_id":7,"location_id":14,"custodian_employee_id":12,"asset_type":"bergerak","brand":"Fuji Xerox","model":"Tipe 205","serial_number":"MSK-993891","acquisition_date":"2018-08-26 00:00:00","acquisition_source":"pembelian","acquisition_cost":3824000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1210-2018-0002","id":146}	console	\N	\N	2026-09-06 14:04:06
288	\N	\N	created	App\\Models\\Asset	147	FIN-1204-2023-0001 Genset cadangan	\N	\N	{"name":"Genset cadangan","asset_category_id":4,"department_id":5,"location_id":10,"custodian_employee_id":null,"asset_type":"tetap","brand":"Appron","model":"Tipe 285","serial_number":"PRS-780300","acquisition_date":"2023-02-16 00:00:00","acquisition_source":"pembelian","acquisition_cost":183052000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1204-2023-0001","id":147}	console	\N	\N	2026-09-06 14:04:06
289	\N	\N	created	App\\Models\\NumberSequence	45	asset.FIN.1205 Kode aset FIN akun 1205	\N	\N	{"code":"asset.FIN.1205","name":"Kode aset FIN akun 1205","prefix":"FIN-1205","separator":"-","period_format":"Y","padding":4,"id":45}	console	\N	\N	2026-09-06 14:04:06
290	\N	\N	created	App\\Models\\Asset	148	FIN-1205-2018-0001 Kendaraan dinas	\N	\N	{"name":"Kendaraan dinas","asset_category_id":5,"department_id":5,"location_id":21,"custodian_employee_id":11,"asset_type":"bergerak","brand":"Toyota Avanza","model":"Tipe 266","serial_number":"KR4-107175","acquisition_date":"2018-06-29 00:00:00","acquisition_source":"pembelian","acquisition_cost":187478000,"status":"aktif","condition":"perlu_perbaikan","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1205-2018-0001","id":148}	console	\N	\N	2026-09-06 14:04:06
291	\N	\N	created	App\\Models\\Asset	149	HRD-1212-2024-0002 Kipas angin dinding	\N	\N	{"name":"Kipas angin dinding","asset_category_id":12,"department_id":7,"location_id":18,"custodian_employee_id":null,"asset_type":"bergerak","brand":"Sharp","model":"Tipe 894","serial_number":"PGU-237925","acquisition_date":"2024-09-07 00:00:00","acquisition_source":"pembelian","acquisition_cost":25765000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"HRD-1212-2024-0002","id":149}	console	\N	\N	2026-09-06 14:04:06
292	\N	\N	created	App\\Models\\Asset	150	GA-1208-2026-0002 Loker karyawan	\N	\N	{"name":"Loker karyawan","asset_category_id":8,"department_id":4,"location_id":14,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Elite","model":"Tipe 802","serial_number":"PLG-128629","acquisition_date":"2026-06-19 00:00:00","acquisition_source":"pembelian","acquisition_cost":13852000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"GA-1208-2026-0002","id":150}	console	\N	\N	2026-09-06 14:04:06
293	\N	\N	created	App\\Models\\Asset	151	OPS-1206-2025-0001 Sepeda motor operasional	\N	\N	{"name":"Sepeda motor operasional","asset_category_id":6,"department_id":8,"location_id":21,"custodian_employee_id":13,"asset_type":"bergerak","brand":"Yamaha NMAX","model":"Tipe 603","serial_number":"KR2-256462","acquisition_date":"2025-09-09 00:00:00","acquisition_source":"pembelian","acquisition_cost":31268000,"status":"aktif","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1206-2025-0001","id":151}	console	\N	\N	2026-09-06 14:04:06
294	\N	\N	created	App\\Models\\Asset	152	FIN-1211-2018-0002 Radio genggam	\N	\N	{"name":"Radio genggam","asset_category_id":11,"department_id":5,"location_id":14,"custodian_employee_id":10,"asset_type":"bergerak","brand":"Panasonic","model":"Tipe 865","serial_number":"ALK-867609","acquisition_date":"2018-10-11 00:00:00","acquisition_source":"pembelian","acquisition_cost":4452000,"status":"tidak_dipakai","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"FIN-1211-2018-0002","id":152}	console	\N	\N	2026-09-06 14:04:06
295	\N	\N	created	App\\Models\\Asset	153	OPS-1211-2019-0001 Pesawat telepon meja	\N	\N	{"name":"Pesawat telepon meja","asset_category_id":11,"department_id":8,"location_id":9,"custodian_employee_id":6,"asset_type":"bergerak","brand":"Yealink","model":"Tipe 139","serial_number":"ALK-686785","acquisition_date":"2019-02-05 00:00:00","acquisition_source":"hibah","acquisition_cost":1104000,"status":"dipinjam","condition":"baik","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1211-2019-0001","id":153}	console	\N	\N	2026-09-06 14:04:06
296	\N	\N	created	App\\Models\\Asset	154	OPS-1207-2025-0002 Kursi tamu kayu	\N	\N	{"name":"Kursi tamu kayu","asset_category_id":7,"department_id":8,"location_id":13,"custodian_employee_id":5,"asset_type":"bergerak","brand":"Ligna","model":"Tipe 974","serial_number":"PKY-109546","acquisition_date":"2025-10-17 00:00:00","acquisition_source":"pembelian","acquisition_cost":6075000,"status":"tidak_dipakai","condition":"rusak","notes":"[DATA DEMO] Data karangan untuk peragaan, bukan aset sungguhan.","code":"OPS-1207-2025-0002","id":154}	console	\N	\N	2026-09-06 14:04:06
297	1	Administrator	updated	App\\Models\\Asset	60	FIN-1204-2022-0001 Pompa air gedung	\N	{"photo_path":null}	{"photo_path":"foto-aset\\/01M1VK3P4ABW0VF3DYCAD0FRP6.jpeg"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 14:49:10
298	1	Administrator	updated	App\\Models\\Asset	147	FIN-1204-2023-0001 Genset cadangan	\N	{"photo_path":null}	{"photo_path":"foto-aset\\/01M1VKYK2PP5Z1GMBBMMHG3QYG.jpeg"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 15:03:52
299	\N	\N	created	App\\Models\\Module	11	Stock opname	\N	\N	{"code":"stock_opnames","name":"Stock opname","description":"Sesi pemeriksaan fisik aset, pencatatan temuan, dan penyesuaian data.","group":"Aset","icon":"heroicon-o-clipboard-document-check","sort":30,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\"]","is_active":true,"id":11}	console	\N	\N	2026-09-06 15:17:20
300	\N	\N	created	App\\Models\\NumberSequence	46	stock_opname Nomor sesi stock opname	\N	\N	{"code":"stock_opname","name":"Nomor sesi stock opname","prefix":"SO","separator":"\\/","period_format":"Y\\/m","padding":4,"id":46}	console	\N	\N	2026-09-06 15:17:28
301	1	Administrator	created	App\\Models\\User	2	tester	\N	\N	{"name":"tester","email":"test@gais.test","is_active":true,"is_super_admin":false,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 15:26:03
302	1	Administrator	logout	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/admin/logout	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 15:26:13
303	2	tester	login	App\\Models\\User	2	tester	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-06 15:26:20
304	2	tester	login	App\\Models\\User	2	tester	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:29:09
305	2	tester	created	App\\Models\\StockOpname	1	SO/2026/09/0001 Opname lantai 3 uji coba	\N	\N	{"name":"Opname lantai 3 uji coba","notes":null,"scope_location_id":15,"scope_department_id":null,"scope_asset_category_id":null,"code":"SO\\/2026\\/09\\/0001","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:34:42
306	2	tester	updated	App\\Models\\Asset	32	OPS-1211-2022-0001 Mesin faksimile	\N	{"status":"aktif"}	{"status":"dilepas"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:43:17
307	2	tester	created	App\\Models\\StockOpname	2	SO/2026/09/0002 Opname menyeluruh uji coba	\N	\N	{"name":"Opname menyeluruh uji coba","notes":null,"scope_location_id":null,"scope_department_id":null,"scope_asset_category_id":null,"code":"SO\\/2026\\/09\\/0002","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:43:41
308	2	tester	updated	App\\Models\\StockOpname	1	SO/2026/09/0001 Opname lantai 3 uji coba	\N	{"status":"draft","started_at":null,"started_by_user_id":null}	{"status":"berjalan","started_at":"2026-09-06 15:44:34","started_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:44:34
309	2	tester	updated	App\\Models\\StockOpname	1	SO/2026/09/0001 Opname lantai 3 uji coba	\N	{"status":"berjalan","finished_at":null,"finished_by_user_id":null}	{"status":"selesai","finished_at":"2026-09-06 15:52:35","finished_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:52:35
310	2	tester	updated	App\\Models\\Asset	72	FIN-1209-2020-0001 Pemindai dokumen	\N	{"location_id":19}	{"location_id":17}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:52:50
311	2	tester	updated	App\\Models\\StockOpname	1	SO/2026/09/0001 Opname lantai 3 uji coba	\N	{"adjusted_at":null,"adjusted_by_user_id":null}	{"adjusted_at":"2026-09-06 15:52:50","adjusted_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 15:52:50
312	2	tester	created	App\\Models\\StockOpname	3	SO/2026/09/0003 Uji muat ulang daftar target	\N	\N	{"name":"Uji muat ulang daftar target","notes":null,"scope_location_id":null,"scope_department_id":null,"scope_asset_category_id":null,"code":"SO\\/2026\\/09\\/0003","id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 16:04:24
529	1	Administrator	login	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 04:52:54
313	2	tester	deleted	App\\Models\\StockOpname	3	SO/2026/09/0003 Uji muat ulang daftar target	\N	{"id":3,"code":"SO\\/2026\\/09\\/0003","name":"Uji muat ulang daftar target","scope_location_id":null,"scope_department_id":null,"scope_asset_category_id":null,"status":"draft","started_at":null,"started_by_user_id":null,"finished_at":null,"finished_by_user_id":null,"adjusted_at":null,"adjusted_by_user_id":null,"notes":null}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 16:05:25
314	2	tester	deleted	App\\Models\\StockOpname	2	SO/2026/09/0002 Opname menyeluruh uji coba	\N	{"id":2,"code":"SO\\/2026\\/09\\/0002","name":"Opname menyeluruh uji coba","scope_location_id":null,"scope_department_id":null,"scope_asset_category_id":null,"status":"draft","started_at":null,"started_by_user_id":null,"finished_at":null,"finished_by_user_id":null,"adjusted_at":null,"adjusted_by_user_id":null,"notes":null}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 16:05:42
315	2	tester	updated	App\\Models\\Asset	32	OPS-1211-2022-0001 Mesin faksimile	\N	{"status":"dilepas"}	{"status":"aktif"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 16:06:14
316	\N	\N	created	App\\Models\\Module	12	Barang habis pakai	\N	\N	{"code":"supply_items","name":"Barang habis pakai","description":"Daftar ATK dan perlengkapan habis pakai beserta stok dan batas pemesanan ulangnya.","group":"Persediaan","icon":"heroicon-o-archive-box","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":12}	console	\N	\N	2026-09-06 21:19:20
317	\N	\N	created	App\\Models\\Module	13	Mutasi barang	\N	\N	{"code":"supply_transactions","name":"Mutasi barang","description":"Barang masuk, barang keluar, dan koreksi stok. Daftar ini yang menjadi dasar perhitungan stok.","group":"Persediaan","icon":"heroicon-o-arrows-right-left","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":13}	console	\N	\N	2026-09-06 21:19:20
318	\N	\N	created	App\\Models\\NumberSequence	49	supply_item Kode barang habis pakai	\N	\N	{"code":"supply_item","name":"Kode barang habis pakai","prefix":"ATK","separator":"-","period_format":"","padding":4,"id":49}	console	\N	\N	2026-09-06 21:23:18
319	\N	\N	created	App\\Models\\NumberSequence	50	supply_transaction Nomor mutasi persediaan	\N	\N	{"code":"supply_transaction","name":"Nomor mutasi persediaan","prefix":"MP","separator":"\\/","period_format":"Y\\/m","padding":4,"id":50}	console	\N	\N	2026-09-06 21:23:20
320	\N	\N	created	App\\Models\\SupplyItem	1	ATK-0001 Kertas HVS A4 80 gram	\N	\N	{"name":"Kertas HVS A4 80 gram","category":"kertas","unit":"rim","minimum_stock":20,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0001","id":1}	console	\N	\N	2026-09-06 21:23:53
321	\N	\N	created	App\\Models\\SupplyTransaction	1	MP/2026/09/0001 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"masuk","quantity":65,"unit_price":62000,"transaction_date":"2026-05-26 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-62648","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0001","created_by_user_id":null,"id":1}	console	\N	\N	2026-09-06 21:23:53
322	\N	\N	created	App\\Models\\SupplyTransaction	2	MP/2026/09/0002 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"keluar","quantity":-6,"transaction_date":"2026-06-06 00:00:00","department_id":1,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0002","created_by_user_id":null,"id":2}	console	\N	\N	2026-09-06 21:23:53
323	\N	\N	created	App\\Models\\SupplyTransaction	3	MP/2026/09/0003 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"keluar","quantity":-9,"transaction_date":"2026-06-24 00:00:00","department_id":8,"employee_id":4,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0003","created_by_user_id":null,"id":3}	console	\N	\N	2026-09-06 21:23:53
324	\N	\N	created	App\\Models\\SupplyTransaction	4	MP/2026/09/0004 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"keluar","quantity":-2,"transaction_date":"2026-07-20 00:00:00","department_id":1,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0004","created_by_user_id":null,"id":4}	console	\N	\N	2026-09-06 21:23:53
325	\N	\N	created	App\\Models\\SupplyItem	2	ATK-0002 Kertas HVS F4 70 gram	\N	\N	{"name":"Kertas HVS F4 70 gram","category":"kertas","unit":"rim","minimum_stock":10,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0002","id":2}	console	\N	\N	2026-09-06 21:23:53
326	\N	\N	created	App\\Models\\SupplyTransaction	5	MP/2026/09/0005 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"masuk","quantity":28,"unit_price":58000,"transaction_date":"2026-05-22 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-95993","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0005","created_by_user_id":null,"id":5}	console	\N	\N	2026-09-06 21:23:53
327	\N	\N	created	App\\Models\\SupplyTransaction	6	MP/2026/09/0006 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-5,"transaction_date":"2026-06-06 00:00:00","department_id":1,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0006","created_by_user_id":null,"id":6}	console	\N	\N	2026-09-06 21:23:53
328	\N	\N	created	App\\Models\\SupplyTransaction	7	MP/2026/09/0007 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-2,"transaction_date":"2026-06-17 00:00:00","department_id":4,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0007","created_by_user_id":null,"id":7}	console	\N	\N	2026-09-06 21:23:53
329	\N	\N	created	App\\Models\\SupplyTransaction	8	MP/2026/09/0008 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-6,"transaction_date":"2026-06-28 00:00:00","department_id":2,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0008","created_by_user_id":null,"id":8}	console	\N	\N	2026-09-06 21:23:53
330	\N	\N	created	App\\Models\\SupplyTransaction	9	MP/2026/09/0009 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-3,"transaction_date":"2026-07-15 00:00:00","department_id":3,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0009","created_by_user_id":null,"id":9}	console	\N	\N	2026-09-06 21:23:53
331	\N	\N	created	App\\Models\\SupplyTransaction	10	MP/2026/09/0010 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-4,"transaction_date":"2026-08-21 00:00:00","department_id":3,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0010","created_by_user_id":null,"id":10}	console	\N	\N	2026-09-06 21:23:53
689	\N	\N	updated	App\\Models\\Module	8	Audit Log	\N	{"name":"Jejak audit","group":"Sistem"}	{"name":"Audit Log","group":"System"}	console	\N	\N	2026-09-08 05:48:06
333	\N	\N	created	App\\Models\\SupplyTransaction	11	MP/2026/09/0011 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"masuk","quantity":51,"unit_price":34000,"transaction_date":"2026-05-10 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-97394","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0011","created_by_user_id":null,"id":11}	console	\N	\N	2026-09-06 21:23:54
334	\N	\N	created	App\\Models\\SupplyTransaction	12	MP/2026/09/0012 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"keluar","quantity":-6,"transaction_date":"2026-06-06 00:00:00","department_id":4,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0012","created_by_user_id":null,"id":12}	console	\N	\N	2026-09-06 21:23:54
335	\N	\N	created	App\\Models\\SupplyTransaction	13	MP/2026/09/0013 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"keluar","quantity":-3,"transaction_date":"2026-06-24 00:00:00","department_id":2,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0013","created_by_user_id":null,"id":13}	console	\N	\N	2026-09-06 21:23:54
336	\N	\N	created	App\\Models\\SupplyTransaction	14	MP/2026/09/0014 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"keluar","quantity":-3,"transaction_date":"2026-07-08 00:00:00","department_id":5,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0014","created_by_user_id":null,"id":14}	console	\N	\N	2026-09-06 21:23:54
337	\N	\N	created	App\\Models\\SupplyTransaction	15	MP/2026/09/0015 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"keluar","quantity":-4,"transaction_date":"2026-07-24 00:00:00","department_id":8,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0015","created_by_user_id":null,"id":15}	console	\N	\N	2026-09-06 21:23:54
338	\N	\N	created	App\\Models\\SupplyTransaction	16	MP/2026/09/0016 Amplop kabinet coklat	\N	\N	{"supply_item_id":3,"type":"keluar","quantity":-4,"transaction_date":"2026-08-01 00:00:00","department_id":3,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0016","created_by_user_id":null,"id":16}	console	\N	\N	2026-09-06 21:23:54
339	\N	\N	created	App\\Models\\SupplyItem	4	ATK-0004 Kertas struk kasir	\N	\N	{"name":"Kertas struk kasir","category":"kertas","unit":"roll","minimum_stock":12,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0004","id":4}	console	\N	\N	2026-09-06 21:23:54
340	\N	\N	created	App\\Models\\SupplyTransaction	17	MP/2026/09/0017 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"masuk","quantity":30,"unit_price":6500,"transaction_date":"2026-05-17 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-33871","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0017","created_by_user_id":null,"id":17}	console	\N	\N	2026-09-06 21:23:54
341	\N	\N	created	App\\Models\\SupplyTransaction	18	MP/2026/09/0018 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":1,"employee_id":4,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0018","created_by_user_id":null,"id":18}	console	\N	\N	2026-09-06 21:23:54
342	\N	\N	created	App\\Models\\SupplyTransaction	19	MP/2026/09/0019 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-5,"transaction_date":"2026-06-16 00:00:00","department_id":6,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0019","created_by_user_id":null,"id":19}	console	\N	\N	2026-09-06 21:23:54
343	\N	\N	created	App\\Models\\SupplyTransaction	20	MP/2026/09/0020 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-1,"transaction_date":"2026-07-18 00:00:00","department_id":4,"employee_id":3,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0020","created_by_user_id":null,"id":20}	console	\N	\N	2026-09-06 21:23:54
344	\N	\N	created	App\\Models\\SupplyTransaction	21	MP/2026/09/0021 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-6,"transaction_date":"2026-07-06 00:00:00","department_id":8,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0021","created_by_user_id":null,"id":21}	console	\N	\N	2026-09-06 21:23:54
345	\N	\N	created	App\\Models\\SupplyTransaction	22	MP/2026/09/0022 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-5,"transaction_date":"2026-07-16 00:00:00","department_id":6,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0022","created_by_user_id":null,"id":22}	console	\N	\N	2026-09-06 21:23:54
346	\N	\N	created	App\\Models\\SupplyItem	5	ATK-0005 Pulpen tinta biru 0,5 mm	\N	\N	{"name":"Pulpen tinta biru 0,5 mm","category":"alat_tulis","unit":"pcs","minimum_stock":50,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0005","id":5}	console	\N	\N	2026-09-06 21:23:54
347	\N	\N	created	App\\Models\\SupplyTransaction	23	MP/2026/09/0023 Pulpen tinta biru 0,5 mm	\N	\N	{"supply_item_id":5,"type":"masuk","quantity":241,"unit_price":4200,"transaction_date":"2026-05-16 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-67979","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0023","created_by_user_id":null,"id":23}	console	\N	\N	2026-09-06 21:23:54
348	\N	\N	created	App\\Models\\SupplyTransaction	24	MP/2026/09/0024 Pulpen tinta biru 0,5 mm	\N	\N	{"supply_item_id":5,"type":"keluar","quantity":-19,"transaction_date":"2026-06-06 00:00:00","department_id":7,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0024","created_by_user_id":null,"id":24}	console	\N	\N	2026-09-06 21:23:54
349	\N	\N	created	App\\Models\\SupplyTransaction	25	MP/2026/09/0025 Pulpen tinta biru 0,5 mm	\N	\N	{"supply_item_id":5,"type":"keluar","quantity":-23,"transaction_date":"2026-06-16 00:00:00","department_id":6,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0025","created_by_user_id":null,"id":25}	console	\N	\N	2026-09-06 21:23:54
350	\N	\N	created	App\\Models\\SupplyTransaction	26	MP/2026/09/0026 Pulpen tinta biru 0,5 mm	\N	\N	{"supply_item_id":5,"type":"keluar","quantity":-18,"transaction_date":"2026-07-20 00:00:00","department_id":2,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0026","created_by_user_id":null,"id":26}	console	\N	\N	2026-09-06 21:23:54
351	\N	\N	created	App\\Models\\SupplyTransaction	27	MP/2026/09/0027 Pulpen tinta biru 0,5 mm	\N	\N	{"supply_item_id":5,"type":"keluar","quantity":-1,"transaction_date":"2026-07-24 00:00:00","department_id":2,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0027","created_by_user_id":null,"id":27}	console	\N	\N	2026-09-06 21:23:54
352	\N	\N	created	App\\Models\\SupplyItem	6	ATK-0006 Pulpen tinta hitam 0,5 mm	\N	\N	{"name":"Pulpen tinta hitam 0,5 mm","category":"alat_tulis","unit":"pcs","minimum_stock":50,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0006","id":6}	console	\N	\N	2026-09-06 21:23:54
353	\N	\N	created	App\\Models\\SupplyTransaction	28	MP/2026/09/0028 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"masuk","quantity":117,"unit_price":4200,"transaction_date":"2026-05-12 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-62418","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0028","created_by_user_id":null,"id":28}	console	\N	\N	2026-09-06 21:23:54
354	\N	\N	created	App\\Models\\SupplyTransaction	29	MP/2026/09/0029 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"keluar","quantity":-23,"transaction_date":"2026-06-06 00:00:00","department_id":6,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0029","created_by_user_id":null,"id":29}	console	\N	\N	2026-09-06 21:23:54
355	\N	\N	created	App\\Models\\SupplyTransaction	30	MP/2026/09/0030 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"keluar","quantity":-1,"transaction_date":"2026-06-19 00:00:00","department_id":6,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0030","created_by_user_id":null,"id":30}	console	\N	\N	2026-09-06 21:23:54
356	\N	\N	created	App\\Models\\SupplyTransaction	31	MP/2026/09/0031 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"keluar","quantity":-24,"transaction_date":"2026-07-12 00:00:00","department_id":6,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0031","created_by_user_id":null,"id":31}	console	\N	\N	2026-09-06 21:23:55
357	\N	\N	created	App\\Models\\SupplyTransaction	32	MP/2026/09/0032 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"keluar","quantity":-26,"transaction_date":"2026-07-18 00:00:00","department_id":3,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0032","created_by_user_id":null,"id":32}	console	\N	\N	2026-09-06 21:23:55
358	\N	\N	created	App\\Models\\SupplyTransaction	33	MP/2026/09/0033 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"keluar","quantity":-13,"transaction_date":"2026-08-21 00:00:00","department_id":5,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0033","created_by_user_id":null,"id":33}	console	\N	\N	2026-09-06 21:23:55
359	\N	\N	created	App\\Models\\SupplyItem	7	ATK-0007 Pensil kayu 2B	\N	\N	{"name":"Pensil kayu 2B","category":"alat_tulis","unit":"pcs","minimum_stock":30,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0007","id":7}	console	\N	\N	2026-09-06 21:23:55
360	\N	\N	created	App\\Models\\SupplyTransaction	34	MP/2026/09/0034 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"masuk","quantity":122,"unit_price":3100,"transaction_date":"2026-05-15 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-46617","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0034","created_by_user_id":null,"id":34}	console	\N	\N	2026-09-06 21:23:55
361	\N	\N	created	App\\Models\\SupplyTransaction	35	MP/2026/09/0035 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"keluar","quantity":-13,"transaction_date":"2026-06-06 00:00:00","department_id":8,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0035","created_by_user_id":null,"id":35}	console	\N	\N	2026-09-06 21:23:55
362	\N	\N	created	App\\Models\\SupplyTransaction	36	MP/2026/09/0036 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"keluar","quantity":-13,"transaction_date":"2026-06-27 00:00:00","department_id":8,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0036","created_by_user_id":null,"id":36}	console	\N	\N	2026-09-06 21:23:55
363	\N	\N	created	App\\Models\\SupplyTransaction	37	MP/2026/09/0037 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"keluar","quantity":-14,"transaction_date":"2026-06-26 00:00:00","department_id":1,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0037","created_by_user_id":null,"id":37}	console	\N	\N	2026-09-06 21:23:55
364	\N	\N	created	App\\Models\\SupplyTransaction	38	MP/2026/09/0038 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"keluar","quantity":-14,"transaction_date":"2026-07-24 00:00:00","department_id":7,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0038","created_by_user_id":null,"id":38}	console	\N	\N	2026-09-06 21:23:55
365	\N	\N	created	App\\Models\\SupplyTransaction	39	MP/2026/09/0039 Pensil kayu 2B	\N	\N	{"supply_item_id":7,"type":"keluar","quantity":-2,"transaction_date":"2026-08-09 00:00:00","department_id":5,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0039","created_by_user_id":null,"id":39}	console	\N	\N	2026-09-06 21:23:55
366	\N	\N	created	App\\Models\\SupplyItem	8	ATK-0008 Spidol papan tulis hitam	\N	\N	{"name":"Spidol papan tulis hitam","category":"alat_tulis","unit":"pcs","minimum_stock":12,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0008","id":8}	console	\N	\N	2026-09-06 21:23:55
367	\N	\N	created	App\\Models\\SupplyTransaction	40	MP/2026/09/0040 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"masuk","quantity":22,"unit_price":11500,"transaction_date":"2026-05-23 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-36455","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0040","created_by_user_id":null,"id":40}	console	\N	\N	2026-09-06 21:23:55
368	\N	\N	created	App\\Models\\SupplyTransaction	41	MP/2026/09/0041 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-2,"transaction_date":"2026-06-06 00:00:00","department_id":5,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0041","created_by_user_id":null,"id":41}	console	\N	\N	2026-09-06 21:23:55
369	\N	\N	created	App\\Models\\SupplyTransaction	42	MP/2026/09/0042 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-5,"transaction_date":"2026-06-21 00:00:00","department_id":2,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0042","created_by_user_id":null,"id":42}	console	\N	\N	2026-09-06 21:23:55
370	\N	\N	created	App\\Models\\SupplyTransaction	43	MP/2026/09/0043 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-1,"transaction_date":"2026-07-10 00:00:00","department_id":4,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0043","created_by_user_id":null,"id":43}	console	\N	\N	2026-09-06 21:23:55
371	\N	\N	created	App\\Models\\SupplyTransaction	44	MP/2026/09/0044 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-3,"transaction_date":"2026-08-05 00:00:00","department_id":2,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0044","created_by_user_id":null,"id":44}	console	\N	\N	2026-09-06 21:23:55
372	\N	\N	created	App\\Models\\SupplyTransaction	45	MP/2026/09/0045 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"keluar","quantity":-6,"transaction_date":"2026-08-05 00:00:00","department_id":2,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0045","created_by_user_id":null,"id":45}	console	\N	\N	2026-09-06 21:23:55
375	\N	\N	created	App\\Models\\SupplyTransaction	47	MP/2026/09/0047 Stapler ukuran sedang	\N	\N	{"supply_item_id":9,"type":"masuk","quantity":24,"unit_price":38000,"transaction_date":"2026-05-12 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-44887","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0047","created_by_user_id":null,"id":47}	console	\N	\N	2026-09-06 21:23:55
376	\N	\N	created	App\\Models\\SupplyTransaction	48	MP/2026/09/0048 Stapler ukuran sedang	\N	\N	{"supply_item_id":9,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0048","created_by_user_id":null,"id":48}	console	\N	\N	2026-09-06 21:23:55
377	\N	\N	created	App\\Models\\SupplyTransaction	49	MP/2026/09/0049 Stapler ukuran sedang	\N	\N	{"supply_item_id":9,"type":"keluar","quantity":-1,"transaction_date":"2026-06-19 00:00:00","department_id":5,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0049","created_by_user_id":null,"id":49}	console	\N	\N	2026-09-06 21:23:55
378	\N	\N	created	App\\Models\\SupplyTransaction	50	MP/2026/09/0050 Stapler ukuran sedang	\N	\N	{"supply_item_id":9,"type":"keluar","quantity":-4,"transaction_date":"2026-06-28 00:00:00","department_id":7,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0050","created_by_user_id":null,"id":50}	console	\N	\N	2026-09-06 21:23:55
379	\N	\N	created	App\\Models\\SupplyTransaction	51	MP/2026/09/0051 Stapler ukuran sedang	\N	\N	{"supply_item_id":9,"type":"keluar","quantity":-4,"transaction_date":"2026-08-11 00:00:00","department_id":1,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0051","created_by_user_id":null,"id":51}	console	\N	\N	2026-09-06 21:23:55
380	\N	\N	created	App\\Models\\SupplyItem	10	ATK-0010 Isi stapler nomor 10	\N	\N	{"name":"Isi stapler nomor 10","category":"alat_tulis","unit":"box","minimum_stock":20,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0010","id":10}	console	\N	\N	2026-09-06 21:23:55
381	\N	\N	created	App\\Models\\SupplyTransaction	52	MP/2026/09/0052 Isi stapler nomor 10	\N	\N	{"supply_item_id":10,"type":"masuk","quantity":53,"unit_price":3800,"transaction_date":"2026-05-11 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-39229","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0052","created_by_user_id":null,"id":52}	console	\N	\N	2026-09-06 21:23:55
382	\N	\N	created	App\\Models\\SupplyTransaction	53	MP/2026/09/0053 Isi stapler nomor 10	\N	\N	{"supply_item_id":10,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0053","created_by_user_id":null,"id":53}	console	\N	\N	2026-09-06 21:23:55
383	\N	\N	created	App\\Models\\SupplyTransaction	54	MP/2026/09/0054 Isi stapler nomor 10	\N	\N	{"supply_item_id":10,"type":"keluar","quantity":-2,"transaction_date":"2026-06-20 00:00:00","department_id":5,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0054","created_by_user_id":null,"id":54}	console	\N	\N	2026-09-06 21:23:56
384	\N	\N	created	App\\Models\\SupplyTransaction	55	MP/2026/09/0055 Isi stapler nomor 10	\N	\N	{"supply_item_id":10,"type":"keluar","quantity":-9,"transaction_date":"2026-06-28 00:00:00","department_id":7,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0055","created_by_user_id":null,"id":55}	console	\N	\N	2026-09-06 21:23:56
385	\N	\N	created	App\\Models\\SupplyTransaction	56	MP/2026/09/0056 Isi stapler nomor 10	\N	\N	{"supply_item_id":10,"type":"keluar","quantity":-3,"transaction_date":"2026-08-11 00:00:00","department_id":8,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0056","created_by_user_id":null,"id":56}	console	\N	\N	2026-09-06 21:23:56
386	\N	\N	created	App\\Models\\SupplyItem	11	ATK-0011 Map plastik berkancing	\N	\N	{"name":"Map plastik berkancing","category":"alat_tulis","unit":"pcs","minimum_stock":40,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0011","id":11}	console	\N	\N	2026-09-06 21:23:56
387	\N	\N	created	App\\Models\\SupplyTransaction	57	MP/2026/09/0057 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"masuk","quantity":193,"unit_price":5400,"transaction_date":"2026-05-22 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-60577","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0057","created_by_user_id":null,"id":57}	console	\N	\N	2026-09-06 21:23:56
388	\N	\N	created	App\\Models\\SupplyTransaction	58	MP/2026/09/0058 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-17,"transaction_date":"2026-06-06 00:00:00","department_id":8,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0058","created_by_user_id":null,"id":58}	console	\N	\N	2026-09-06 21:23:56
389	\N	\N	created	App\\Models\\SupplyTransaction	59	MP/2026/09/0059 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-19,"transaction_date":"2026-06-19 00:00:00","department_id":7,"employee_id":11,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0059","created_by_user_id":null,"id":59}	console	\N	\N	2026-09-06 21:23:56
390	\N	\N	created	App\\Models\\SupplyTransaction	60	MP/2026/09/0060 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-3,"transaction_date":"2026-07-02 00:00:00","department_id":7,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0060","created_by_user_id":null,"id":60}	console	\N	\N	2026-09-06 21:23:56
391	\N	\N	created	App\\Models\\SupplyTransaction	61	MP/2026/09/0061 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-19,"transaction_date":"2026-08-05 00:00:00","department_id":4,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0061","created_by_user_id":null,"id":61}	console	\N	\N	2026-09-06 21:23:56
392	\N	\N	created	App\\Models\\SupplyTransaction	62	MP/2026/09/0062 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-17,"transaction_date":"2026-08-09 00:00:00","department_id":7,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0062","created_by_user_id":null,"id":62}	console	\N	\N	2026-09-06 21:23:56
393	\N	\N	created	App\\Models\\SupplyTransaction	63	MP/2026/09/0063 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"keluar","quantity":-18,"transaction_date":"2026-09-06 00:00:00","department_id":7,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0063","created_by_user_id":null,"id":63}	console	\N	\N	2026-09-06 21:23:56
394	\N	\N	created	App\\Models\\SupplyItem	12	ATK-0012 Ordner arsip folio	\N	\N	{"name":"Ordner arsip folio","category":"alat_tulis","unit":"pcs","minimum_stock":15,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0012","id":12}	console	\N	\N	2026-09-06 21:23:56
395	\N	\N	created	App\\Models\\SupplyTransaction	64	MP/2026/09/0064 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"masuk","quantity":50,"unit_price":27000,"transaction_date":"2026-05-09 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-30121","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0064","created_by_user_id":null,"id":64}	console	\N	\N	2026-09-06 21:23:56
396	\N	\N	created	App\\Models\\SupplyTransaction	65	MP/2026/09/0065 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-6,"transaction_date":"2026-06-06 00:00:00","department_id":2,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0065","created_by_user_id":null,"id":65}	console	\N	\N	2026-09-06 21:23:56
397	\N	\N	created	App\\Models\\SupplyTransaction	66	MP/2026/09/0066 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-1,"transaction_date":"2026-06-23 00:00:00","department_id":6,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0066","created_by_user_id":null,"id":66}	console	\N	\N	2026-09-06 21:23:56
398	\N	\N	created	App\\Models\\SupplyTransaction	67	MP/2026/09/0067 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-6,"transaction_date":"2026-07-16 00:00:00","department_id":8,"employee_id":4,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0067","created_by_user_id":null,"id":67}	console	\N	\N	2026-09-06 21:23:56
399	\N	\N	created	App\\Models\\SupplyTransaction	68	MP/2026/09/0068 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-1,"transaction_date":"2026-07-03 00:00:00","department_id":7,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0068","created_by_user_id":null,"id":68}	console	\N	\N	2026-09-06 21:23:56
400	\N	\N	created	App\\Models\\SupplyTransaction	69	MP/2026/09/0069 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-8,"transaction_date":"2026-08-09 00:00:00","department_id":2,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0069","created_by_user_id":null,"id":69}	console	\N	\N	2026-09-06 21:23:56
401	\N	\N	created	App\\Models\\SupplyTransaction	70	MP/2026/09/0070 Ordner arsip folio	\N	\N	{"supply_item_id":12,"type":"keluar","quantity":-4,"transaction_date":"2026-09-06 00:00:00","department_id":3,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0070","created_by_user_id":null,"id":70}	console	\N	\N	2026-09-06 21:23:56
402	\N	\N	created	App\\Models\\SupplyItem	13	ATK-0013 Lakban bening 2 inci	\N	\N	{"name":"Lakban bening 2 inci","category":"alat_tulis","unit":"roll","minimum_stock":12,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0013","id":13}	console	\N	\N	2026-09-06 21:23:56
403	\N	\N	created	App\\Models\\SupplyTransaction	71	MP/2026/09/0071 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"masuk","quantity":57,"unit_price":12000,"transaction_date":"2026-05-06 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-10285","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0071","created_by_user_id":null,"id":71}	console	\N	\N	2026-09-06 21:23:56
404	\N	\N	created	App\\Models\\SupplyTransaction	72	MP/2026/09/0072 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-4,"transaction_date":"2026-06-06 00:00:00","department_id":6,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0072","created_by_user_id":null,"id":72}	console	\N	\N	2026-09-06 21:23:56
405	\N	\N	created	App\\Models\\SupplyTransaction	73	MP/2026/09/0073 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-3,"transaction_date":"2026-06-21 00:00:00","department_id":6,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0073","created_by_user_id":null,"id":73}	console	\N	\N	2026-09-06 21:23:56
406	\N	\N	created	App\\Models\\SupplyTransaction	74	MP/2026/09/0074 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-6,"transaction_date":"2026-07-18 00:00:00","department_id":4,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0074","created_by_user_id":null,"id":74}	console	\N	\N	2026-09-06 21:23:56
407	\N	\N	created	App\\Models\\SupplyTransaction	75	MP/2026/09/0075 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-3,"transaction_date":"2026-07-21 00:00:00","department_id":5,"employee_id":13,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0075","created_by_user_id":null,"id":75}	console	\N	\N	2026-09-06 21:23:56
408	\N	\N	created	App\\Models\\SupplyTransaction	76	MP/2026/09/0076 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-3,"transaction_date":"2026-08-21 00:00:00","department_id":7,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0076","created_by_user_id":null,"id":76}	console	\N	\N	2026-09-06 21:23:56
409	\N	\N	created	App\\Models\\SupplyTransaction	77	MP/2026/09/0077 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"keluar","quantity":-2,"transaction_date":"2026-08-20 00:00:00","department_id":1,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0077","created_by_user_id":null,"id":77}	console	\N	\N	2026-09-06 21:23:57
410	\N	\N	created	App\\Models\\SupplyItem	14	ATK-0014 Tinta printer hitam	\N	\N	{"name":"Tinta printer hitam","category":"tinta","unit":"botol","minimum_stock":6,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0014","id":14}	console	\N	\N	2026-09-06 21:23:57
411	\N	\N	created	App\\Models\\SupplyTransaction	78	MP/2026/09/0078 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"masuk","quantity":23,"unit_price":92000,"transaction_date":"2026-05-26 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-82851","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0078","created_by_user_id":null,"id":78}	console	\N	\N	2026-09-06 21:23:57
412	\N	\N	created	App\\Models\\SupplyTransaction	79	MP/2026/09/0079 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"keluar","quantity":-2,"transaction_date":"2026-06-06 00:00:00","department_id":7,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0079","created_by_user_id":null,"id":79}	console	\N	\N	2026-09-06 21:23:57
413	\N	\N	created	App\\Models\\SupplyTransaction	80	MP/2026/09/0080 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"keluar","quantity":-2,"transaction_date":"2026-06-25 00:00:00","department_id":8,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0080","created_by_user_id":null,"id":80}	console	\N	\N	2026-09-06 21:23:57
414	\N	\N	created	App\\Models\\SupplyTransaction	81	MP/2026/09/0081 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"keluar","quantity":-1,"transaction_date":"2026-06-28 00:00:00","department_id":2,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0081","created_by_user_id":null,"id":81}	console	\N	\N	2026-09-06 21:23:57
415	\N	\N	created	App\\Models\\SupplyTransaction	82	MP/2026/09/0082 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"keluar","quantity":-3,"transaction_date":"2026-08-08 00:00:00","department_id":4,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0082","created_by_user_id":null,"id":82}	console	\N	\N	2026-09-06 21:23:57
416	\N	\N	created	App\\Models\\SupplyTransaction	83	MP/2026/09/0083 Tinta printer hitam	\N	\N	{"supply_item_id":14,"type":"keluar","quantity":-3,"transaction_date":"2026-08-25 00:00:00","department_id":5,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0083","created_by_user_id":null,"id":83}	console	\N	\N	2026-09-06 21:23:57
417	\N	\N	created	App\\Models\\SupplyItem	15	ATK-0015 Tinta printer warna	\N	\N	{"name":"Tinta printer warna","category":"tinta","unit":"botol","minimum_stock":6,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0015","id":15}	console	\N	\N	2026-09-06 21:23:57
418	\N	\N	created	App\\Models\\SupplyTransaction	84	MP/2026/09/0084 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"masuk","quantity":15,"unit_price":98000,"transaction_date":"2026-05-17 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-51284","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0084","created_by_user_id":null,"id":84}	console	\N	\N	2026-09-06 21:23:57
419	\N	\N	created	App\\Models\\SupplyTransaction	85	MP/2026/09/0085 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":2,"employee_id":11,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0085","created_by_user_id":null,"id":85}	console	\N	\N	2026-09-06 21:23:57
420	\N	\N	created	App\\Models\\SupplyTransaction	86	MP/2026/09/0086 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"keluar","quantity":-2,"transaction_date":"2026-06-17 00:00:00","department_id":7,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0086","created_by_user_id":null,"id":86}	console	\N	\N	2026-09-06 21:23:57
421	\N	\N	created	App\\Models\\SupplyTransaction	87	MP/2026/09/0087 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"keluar","quantity":-1,"transaction_date":"2026-07-06 00:00:00","department_id":8,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0087","created_by_user_id":null,"id":87}	console	\N	\N	2026-09-06 21:23:57
422	\N	\N	created	App\\Models\\SupplyTransaction	88	MP/2026/09/0088 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"keluar","quantity":-3,"transaction_date":"2026-08-08 00:00:00","department_id":2,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0088","created_by_user_id":null,"id":88}	console	\N	\N	2026-09-06 21:23:57
423	\N	\N	created	App\\Models\\SupplyTransaction	89	MP/2026/09/0089 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"keluar","quantity":-4,"transaction_date":"2026-08-21 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0089","created_by_user_id":null,"id":89}	console	\N	\N	2026-09-06 21:23:57
424	\N	\N	created	App\\Models\\SupplyItem	16	ATK-0016 Toner printer laser	\N	\N	{"name":"Toner printer laser","category":"tinta","unit":"pcs","minimum_stock":2,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0016","id":16}	console	\N	\N	2026-09-06 21:23:57
425	\N	\N	created	App\\Models\\SupplyTransaction	90	MP/2026/09/0090 Toner printer laser	\N	\N	{"supply_item_id":16,"type":"masuk","quantity":12,"unit_price":780000,"transaction_date":"2026-05-19 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-44357","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0090","created_by_user_id":null,"id":90}	console	\N	\N	2026-09-06 21:23:57
426	\N	\N	created	App\\Models\\SupplyTransaction	91	MP/2026/09/0091 Toner printer laser	\N	\N	{"supply_item_id":16,"type":"keluar","quantity":-2,"transaction_date":"2026-06-06 00:00:00","department_id":1,"employee_id":7,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0091","created_by_user_id":null,"id":91}	console	\N	\N	2026-09-06 21:23:57
427	\N	\N	created	App\\Models\\SupplyTransaction	92	MP/2026/09/0092 Toner printer laser	\N	\N	{"supply_item_id":16,"type":"keluar","quantity":-1,"transaction_date":"2026-06-16 00:00:00","department_id":4,"employee_id":13,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0092","created_by_user_id":null,"id":92}	console	\N	\N	2026-09-06 21:23:57
428	\N	\N	created	App\\Models\\SupplyTransaction	93	MP/2026/09/0093 Toner printer laser	\N	\N	{"supply_item_id":16,"type":"keluar","quantity":-2,"transaction_date":"2026-07-14 00:00:00","department_id":5,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0093","created_by_user_id":null,"id":93}	console	\N	\N	2026-09-06 21:23:57
429	\N	\N	created	App\\Models\\SupplyTransaction	94	MP/2026/09/0094 Toner printer laser	\N	\N	{"supply_item_id":16,"type":"keluar","quantity":-1,"transaction_date":"2026-08-05 00:00:00","department_id":6,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0094","created_by_user_id":null,"id":94}	console	\N	\N	2026-09-06 21:23:57
430	\N	\N	created	App\\Models\\SupplyItem	17	ATK-0017 Sabun cuci tangan isi ulang	\N	\N	{"name":"Sabun cuci tangan isi ulang","category":"kebersihan","unit":"botol","minimum_stock":8,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0017","id":17}	console	\N	\N	2026-09-06 21:23:57
431	\N	\N	created	App\\Models\\SupplyTransaction	95	MP/2026/09/0095 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"masuk","quantity":40,"unit_price":26000,"transaction_date":"2026-05-26 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-53124","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0095","created_by_user_id":null,"id":95}	console	\N	\N	2026-09-06 21:23:57
432	\N	\N	created	App\\Models\\SupplyTransaction	96	MP/2026/09/0096 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-4,"transaction_date":"2026-06-06 00:00:00","department_id":2,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0096","created_by_user_id":null,"id":96}	console	\N	\N	2026-09-06 21:23:57
433	\N	\N	created	App\\Models\\SupplyTransaction	97	MP/2026/09/0097 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-1,"transaction_date":"2026-06-17 00:00:00","department_id":5,"employee_id":3,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0097","created_by_user_id":null,"id":97}	console	\N	\N	2026-09-06 21:23:57
434	\N	\N	created	App\\Models\\SupplyTransaction	98	MP/2026/09/0098 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-4,"transaction_date":"2026-07-04 00:00:00","department_id":4,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0098","created_by_user_id":null,"id":98}	console	\N	\N	2026-09-06 21:23:57
435	\N	\N	created	App\\Models\\SupplyTransaction	99	MP/2026/09/0099 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-4,"transaction_date":"2026-07-12 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0099","created_by_user_id":null,"id":99}	console	\N	\N	2026-09-06 21:23:57
436	\N	\N	created	App\\Models\\SupplyTransaction	100	MP/2026/09/0100 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-4,"transaction_date":"2026-07-20 00:00:00","department_id":6,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0100","created_by_user_id":null,"id":100}	console	\N	\N	2026-09-06 21:23:57
437	\N	\N	created	App\\Models\\SupplyTransaction	101	MP/2026/09/0101 Sabun cuci tangan isi ulang	\N	\N	{"supply_item_id":17,"type":"keluar","quantity":-2,"transaction_date":"2026-08-20 00:00:00","department_id":4,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0101","created_by_user_id":null,"id":101}	console	\N	\N	2026-09-06 21:23:57
438	\N	\N	created	App\\Models\\SupplyItem	18	ATK-0018 Tisu gulung	\N	\N	{"name":"Tisu gulung","category":"kebersihan","unit":"roll","minimum_stock":24,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0018","id":18}	console	\N	\N	2026-09-06 21:23:57
439	\N	\N	created	App\\Models\\SupplyTransaction	102	MP/2026/09/0102 Tisu gulung	\N	\N	{"supply_item_id":18,"type":"masuk","quantity":66,"unit_price":8500,"transaction_date":"2026-05-16 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-61044","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0102","created_by_user_id":null,"id":102}	console	\N	\N	2026-09-06 21:23:57
440	\N	\N	created	App\\Models\\SupplyTransaction	103	MP/2026/09/0103 Tisu gulung	\N	\N	{"supply_item_id":18,"type":"keluar","quantity":-5,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0103","created_by_user_id":null,"id":103}	console	\N	\N	2026-09-06 21:23:57
441	\N	\N	created	App\\Models\\SupplyTransaction	104	MP/2026/09/0104 Tisu gulung	\N	\N	{"supply_item_id":18,"type":"keluar","quantity":-9,"transaction_date":"2026-06-16 00:00:00","department_id":1,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0104","created_by_user_id":null,"id":104}	console	\N	\N	2026-09-06 21:23:57
442	\N	\N	created	App\\Models\\SupplyTransaction	105	MP/2026/09/0105 Tisu gulung	\N	\N	{"supply_item_id":18,"type":"keluar","quantity":-9,"transaction_date":"2026-07-04 00:00:00","department_id":1,"employee_id":13,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0105","created_by_user_id":null,"id":105}	console	\N	\N	2026-09-06 21:23:57
443	\N	\N	created	App\\Models\\SupplyItem	19	ATK-0019 Pembersih lantai	\N	\N	{"name":"Pembersih lantai","category":"kebersihan","unit":"botol","minimum_stock":6,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0019","id":19}	console	\N	\N	2026-09-06 21:23:57
444	\N	\N	created	App\\Models\\SupplyTransaction	106	MP/2026/09/0106 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"masuk","quantity":18,"unit_price":21000,"transaction_date":"2026-05-20 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-81853","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0106","created_by_user_id":null,"id":106}	console	\N	\N	2026-09-06 21:23:57
445	\N	\N	created	App\\Models\\SupplyTransaction	107	MP/2026/09/0107 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-2,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0107","created_by_user_id":null,"id":107}	console	\N	\N	2026-09-06 21:23:57
446	\N	\N	created	App\\Models\\SupplyTransaction	108	MP/2026/09/0108 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-1,"transaction_date":"2026-06-28 00:00:00","department_id":8,"employee_id":1,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0108","created_by_user_id":null,"id":108}	console	\N	\N	2026-09-06 21:23:57
447	\N	\N	created	App\\Models\\SupplyTransaction	109	MP/2026/09/0109 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-3,"transaction_date":"2026-07-16 00:00:00","department_id":6,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0109","created_by_user_id":null,"id":109}	console	\N	\N	2026-09-06 21:23:57
448	\N	\N	created	App\\Models\\SupplyTransaction	110	MP/2026/09/0110 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-1,"transaction_date":"2026-07-03 00:00:00","department_id":6,"employee_id":3,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0110","created_by_user_id":null,"id":110}	console	\N	\N	2026-09-06 21:23:57
449	\N	\N	created	App\\Models\\SupplyTransaction	111	MP/2026/09/0111 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-4,"transaction_date":"2026-07-24 00:00:00","department_id":8,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0111","created_by_user_id":null,"id":111}	console	\N	\N	2026-09-06 21:23:57
450	\N	\N	created	App\\Models\\SupplyTransaction	112	MP/2026/09/0112 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"keluar","quantity":-4,"transaction_date":"2026-07-26 00:00:00","department_id":7,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0112","created_by_user_id":null,"id":112}	console	\N	\N	2026-09-06 21:23:57
451	\N	\N	created	App\\Models\\SupplyItem	20	ATK-0020 Kantong sampah ukuran besar	\N	\N	{"name":"Kantong sampah ukuran besar","category":"kebersihan","unit":"pak","minimum_stock":10,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0020","id":20}	console	\N	\N	2026-09-06 21:23:57
452	\N	\N	created	App\\Models\\SupplyTransaction	113	MP/2026/09/0113 Kantong sampah ukuran besar	\N	\N	{"supply_item_id":20,"type":"masuk","quantity":31,"unit_price":19500,"transaction_date":"2026-05-19 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-62636","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0113","created_by_user_id":null,"id":113}	console	\N	\N	2026-09-06 21:23:57
453	\N	\N	created	App\\Models\\SupplyTransaction	114	MP/2026/09/0114 Kantong sampah ukuran besar	\N	\N	{"supply_item_id":20,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":8,"employee_id":13,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0114","created_by_user_id":null,"id":114}	console	\N	\N	2026-09-06 21:23:58
454	\N	\N	created	App\\Models\\SupplyTransaction	115	MP/2026/09/0115 Kantong sampah ukuran besar	\N	\N	{"supply_item_id":20,"type":"keluar","quantity":-2,"transaction_date":"2026-06-27 00:00:00","department_id":6,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0115","created_by_user_id":null,"id":115}	console	\N	\N	2026-09-06 21:23:58
455	\N	\N	created	App\\Models\\SupplyTransaction	116	MP/2026/09/0116 Kantong sampah ukuran besar	\N	\N	{"supply_item_id":20,"type":"keluar","quantity":-5,"transaction_date":"2026-07-08 00:00:00","department_id":5,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0116","created_by_user_id":null,"id":116}	console	\N	\N	2026-09-06 21:23:58
456	\N	\N	created	App\\Models\\SupplyItem	21	ATK-0021 Gula pasir kemasan 1 kilogram	\N	\N	{"name":"Gula pasir kemasan 1 kilogram","category":"pantry","unit":"kg","minimum_stock":5,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0021","id":21}	console	\N	\N	2026-09-06 21:23:58
457	\N	\N	created	App\\Models\\SupplyTransaction	117	MP/2026/09/0117 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"masuk","quantity":24,"unit_price":16500,"transaction_date":"2026-05-08 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-77168","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0117","created_by_user_id":null,"id":117}	console	\N	\N	2026-09-06 21:23:58
458	\N	\N	created	App\\Models\\SupplyTransaction	118	MP/2026/09/0118 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0118","created_by_user_id":null,"id":118}	console	\N	\N	2026-09-06 21:23:58
459	\N	\N	created	App\\Models\\SupplyTransaction	119	MP/2026/09/0119 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-4,"transaction_date":"2026-06-20 00:00:00","department_id":6,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0119","created_by_user_id":null,"id":119}	console	\N	\N	2026-09-06 21:23:58
460	\N	\N	created	App\\Models\\SupplyTransaction	120	MP/2026/09/0120 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-4,"transaction_date":"2026-07-20 00:00:00","department_id":2,"employee_id":13,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0120","created_by_user_id":null,"id":120}	console	\N	\N	2026-09-06 21:23:58
461	\N	\N	created	App\\Models\\SupplyTransaction	121	MP/2026/09/0121 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-4,"transaction_date":"2026-07-06 00:00:00","department_id":5,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0121","created_by_user_id":null,"id":121}	console	\N	\N	2026-09-06 21:23:58
462	\N	\N	created	App\\Models\\SupplyTransaction	122	MP/2026/09/0122 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-1,"transaction_date":"2026-07-28 00:00:00","department_id":3,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0122","created_by_user_id":null,"id":122}	console	\N	\N	2026-09-06 21:23:58
463	\N	\N	created	App\\Models\\SupplyItem	22	ATK-0022 Kopi bubuk kemasan	\N	\N	{"name":"Kopi bubuk kemasan","category":"pantry","unit":"pak","minimum_stock":8,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0022","id":22}	console	\N	\N	2026-09-06 21:23:58
464	\N	\N	created	App\\Models\\SupplyTransaction	123	MP/2026/09/0123 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"masuk","quantity":22,"unit_price":24000,"transaction_date":"2026-05-17 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-88850","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0123","created_by_user_id":null,"id":123}	console	\N	\N	2026-09-06 21:23:58
465	\N	\N	created	App\\Models\\SupplyTransaction	124	MP/2026/09/0124 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"keluar","quantity":-4,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0124","created_by_user_id":null,"id":124}	console	\N	\N	2026-09-06 21:23:58
466	\N	\N	created	App\\Models\\SupplyTransaction	125	MP/2026/09/0125 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"keluar","quantity":-1,"transaction_date":"2026-06-15 00:00:00","department_id":2,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0125","created_by_user_id":null,"id":125}	console	\N	\N	2026-09-06 21:23:58
467	\N	\N	created	App\\Models\\SupplyTransaction	126	MP/2026/09/0126 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"keluar","quantity":-3,"transaction_date":"2026-07-04 00:00:00","department_id":1,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0126","created_by_user_id":null,"id":126}	console	\N	\N	2026-09-06 21:23:58
468	\N	\N	created	App\\Models\\SupplyTransaction	127	MP/2026/09/0127 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"keluar","quantity":-1,"transaction_date":"2026-07-03 00:00:00","department_id":7,"employee_id":3,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0127","created_by_user_id":null,"id":127}	console	\N	\N	2026-09-06 21:23:58
469	\N	\N	created	App\\Models\\SupplyTransaction	128	MP/2026/09/0128 Kopi bubuk kemasan	\N	\N	{"supply_item_id":22,"type":"keluar","quantity":-2,"transaction_date":"2026-08-09 00:00:00","department_id":7,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0128","created_by_user_id":null,"id":128}	console	\N	\N	2026-09-06 21:23:58
470	\N	\N	created	App\\Models\\SupplyItem	23	ATK-0023 Teh celup kotak isi 25	\N	\N	{"name":"Teh celup kotak isi 25","category":"pantry","unit":"box","minimum_stock":6,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0023","id":23}	console	\N	\N	2026-09-06 21:23:58
471	\N	\N	created	App\\Models\\SupplyTransaction	129	MP/2026/09/0129 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"masuk","quantity":21,"unit_price":12500,"transaction_date":"2026-05-18 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-19782","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0129","created_by_user_id":null,"id":129}	console	\N	\N	2026-09-06 21:23:58
472	\N	\N	created	App\\Models\\SupplyTransaction	130	MP/2026/09/0130 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-4,"transaction_date":"2026-06-06 00:00:00","department_id":8,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0130","created_by_user_id":null,"id":130}	console	\N	\N	2026-09-06 21:23:58
473	\N	\N	created	App\\Models\\SupplyTransaction	131	MP/2026/09/0131 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-4,"transaction_date":"2026-06-26 00:00:00","department_id":4,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0131","created_by_user_id":null,"id":131}	console	\N	\N	2026-09-06 21:23:58
474	\N	\N	created	App\\Models\\SupplyTransaction	132	MP/2026/09/0132 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-1,"transaction_date":"2026-07-10 00:00:00","department_id":4,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0132","created_by_user_id":null,"id":132}	console	\N	\N	2026-09-06 21:23:58
475	\N	\N	created	App\\Models\\SupplyTransaction	133	MP/2026/09/0133 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-4,"transaction_date":"2026-07-30 00:00:00","department_id":6,"employee_id":5,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0133","created_by_user_id":null,"id":133}	console	\N	\N	2026-09-06 21:23:58
476	\N	\N	created	App\\Models\\SupplyTransaction	134	MP/2026/09/0134 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-4,"transaction_date":"2026-08-13 00:00:00","department_id":5,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0134","created_by_user_id":null,"id":134}	console	\N	\N	2026-09-06 21:23:58
477	\N	\N	created	App\\Models\\SupplyTransaction	135	MP/2026/09/0135 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"keluar","quantity":-4,"transaction_date":"2026-09-06 00:00:00","department_id":5,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0135","created_by_user_id":null,"id":135}	console	\N	\N	2026-09-06 21:23:58
478	\N	\N	created	App\\Models\\SupplyItem	24	ATK-0024 Air minum galon 19 liter	\N	\N	{"name":"Air minum galon 19 liter","category":"pantry","unit":"pcs","minimum_stock":10,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0024","id":24}	console	\N	\N	2026-09-06 21:23:58
479	\N	\N	created	App\\Models\\SupplyTransaction	136	MP/2026/09/0136 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"masuk","quantity":43,"unit_price":21000,"transaction_date":"2026-05-07 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-62735","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0136","created_by_user_id":null,"id":136}	console	\N	\N	2026-09-06 21:23:58
480	\N	\N	created	App\\Models\\SupplyTransaction	137	MP/2026/09/0137 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"keluar","quantity":-1,"transaction_date":"2026-06-06 00:00:00","department_id":1,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0137","created_by_user_id":null,"id":137}	console	\N	\N	2026-09-06 21:23:58
481	\N	\N	created	App\\Models\\SupplyTransaction	138	MP/2026/09/0138 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"keluar","quantity":-4,"transaction_date":"2026-06-18 00:00:00","department_id":1,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0138","created_by_user_id":null,"id":138}	console	\N	\N	2026-09-06 21:23:58
482	\N	\N	created	App\\Models\\SupplyTransaction	139	MP/2026/09/0139 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"keluar","quantity":-4,"transaction_date":"2026-06-24 00:00:00","department_id":8,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0139","created_by_user_id":null,"id":139}	console	\N	\N	2026-09-06 21:23:58
483	\N	\N	created	App\\Models\\SupplyTransaction	140	MP/2026/09/0140 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"keluar","quantity":-1,"transaction_date":"2026-08-05 00:00:00","department_id":1,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0140","created_by_user_id":null,"id":140}	console	\N	\N	2026-09-06 21:23:58
484	\N	\N	created	App\\Models\\SupplyTransaction	141	MP/2026/09/0141 Air minum galon 19 liter	\N	\N	{"supply_item_id":24,"type":"keluar","quantity":-6,"transaction_date":"2026-08-05 00:00:00","department_id":8,"employee_id":4,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0141","created_by_user_id":null,"id":141}	console	\N	\N	2026-09-06 21:23:58
485	\N	\N	created	App\\Models\\SupplyItem	25	ATK-0025 Lampu LED 12 watt	\N	\N	{"name":"Lampu LED 12 watt","category":"listrik","unit":"pcs","minimum_stock":12,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0025","id":25}	console	\N	\N	2026-09-06 21:23:58
486	\N	\N	created	App\\Models\\SupplyTransaction	142	MP/2026/09/0142 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"masuk","quantity":58,"unit_price":34000,"transaction_date":"2026-05-11 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-99306","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0142","created_by_user_id":null,"id":142}	console	\N	\N	2026-09-06 21:23:58
487	\N	\N	created	App\\Models\\SupplyTransaction	143	MP/2026/09/0143 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-3,"transaction_date":"2026-06-06 00:00:00","department_id":4,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0143","created_by_user_id":null,"id":143}	console	\N	\N	2026-09-06 21:23:58
488	\N	\N	created	App\\Models\\SupplyTransaction	144	MP/2026/09/0144 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-4,"transaction_date":"2026-06-15 00:00:00","department_id":2,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0144","created_by_user_id":null,"id":144}	console	\N	\N	2026-09-06 21:23:58
489	\N	\N	created	App\\Models\\SupplyTransaction	145	MP/2026/09/0145 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-5,"transaction_date":"2026-06-24 00:00:00","department_id":1,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0145","created_by_user_id":null,"id":145}	console	\N	\N	2026-09-06 21:23:58
490	\N	\N	created	App\\Models\\SupplyTransaction	146	MP/2026/09/0146 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-2,"transaction_date":"2026-07-15 00:00:00","department_id":7,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0146","created_by_user_id":null,"id":146}	console	\N	\N	2026-09-06 21:23:58
491	\N	\N	created	App\\Models\\SupplyTransaction	147	MP/2026/09/0147 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-2,"transaction_date":"2026-08-25 00:00:00","department_id":4,"employee_id":9,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0147","created_by_user_id":null,"id":147}	console	\N	\N	2026-09-06 21:23:58
492	\N	\N	created	App\\Models\\SupplyTransaction	148	MP/2026/09/0148 Lampu LED 12 watt	\N	\N	{"supply_item_id":25,"type":"keluar","quantity":-4,"transaction_date":"2026-09-06 00:00:00","department_id":8,"employee_id":8,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0148","created_by_user_id":null,"id":148}	console	\N	\N	2026-09-06 21:23:58
493	\N	\N	created	App\\Models\\SupplyItem	26	ATK-0026 Baterai AA isi 4	\N	\N	{"name":"Baterai AA isi 4","category":"listrik","unit":"pak","minimum_stock":6,"location_id":20,"is_active":true,"notes":"[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.","code":"ATK-0026","id":26}	console	\N	\N	2026-09-06 21:23:58
494	\N	\N	created	App\\Models\\SupplyTransaction	149	MP/2026/09/0149 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"masuk","quantity":21,"unit_price":27500,"transaction_date":"2026-05-12 00:00:00","supplier":"Toko contoh untuk peragaan","reference":"FKT-74472","notes":"[DATA DEMO] Pembelian awal untuk peragaan.","code":"MP\\/2026\\/09\\/0149","created_by_user_id":null,"id":149}	console	\N	\N	2026-09-06 21:23:59
495	\N	\N	created	App\\Models\\SupplyTransaction	150	MP/2026/09/0150 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-2,"transaction_date":"2026-06-06 00:00:00","department_id":3,"employee_id":null,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0150","created_by_user_id":null,"id":150}	console	\N	\N	2026-09-06 21:23:59
496	\N	\N	created	App\\Models\\SupplyTransaction	151	MP/2026/09/0151 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-2,"transaction_date":"2026-06-16 00:00:00","department_id":3,"employee_id":6,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0151","created_by_user_id":null,"id":151}	console	\N	\N	2026-09-06 21:23:59
497	\N	\N	created	App\\Models\\SupplyTransaction	152	MP/2026/09/0152 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-1,"transaction_date":"2026-07-10 00:00:00","department_id":2,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0152","created_by_user_id":null,"id":152}	console	\N	\N	2026-09-06 21:23:59
498	\N	\N	created	App\\Models\\SupplyTransaction	153	MP/2026/09/0153 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-4,"transaction_date":"2026-08-02 00:00:00","department_id":4,"employee_id":2,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0153","created_by_user_id":null,"id":153}	console	\N	\N	2026-09-06 21:23:59
499	\N	\N	created	App\\Models\\SupplyTransaction	154	MP/2026/09/0154 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-4,"transaction_date":"2026-08-29 00:00:00","department_id":1,"employee_id":10,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0154","created_by_user_id":null,"id":154}	console	\N	\N	2026-09-06 21:23:59
500	\N	\N	created	App\\Models\\SupplyTransaction	155	MP/2026/09/0155 Baterai AA isi 4	\N	\N	{"supply_item_id":26,"type":"keluar","quantity":-1,"transaction_date":"2026-07-21 00:00:00","department_id":2,"employee_id":12,"notes":"[DATA DEMO] Pengambilan untuk peragaan.","code":"MP\\/2026\\/09\\/0155","created_by_user_id":null,"id":155}	console	\N	\N	2026-09-06 21:23:59
501	2	tester	created	App\\Models\\SupplyTransaction	156	MP/2026/09/0156 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"keluar","quantity":-3,"unit_price":null,"transaction_date":"2026-09-06 00:00:00","department_id":1,"employee_id":null,"notes":"Uji verifikasi kiriman C","code":"MP\\/2026\\/09\\/0156","created_by_user_id":2,"id":156}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 21:48:01
502	2	tester	created	App\\Models\\SupplyTransaction	157	MP/2026/09/0157 Kertas HVS F4 70 gram	\N	\N	{"supply_item_id":2,"type":"masuk","quantity":10,"unit_price":61500,"transaction_date":"2026-09-06 00:00:00","department_id":null,"employee_id":null,"notes":null,"code":"MP\\/2026\\/09\\/0157","created_by_user_id":2,"id":157}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 21:48:44
503	2	tester	created	App\\Models\\SupplyItem	27	ATK-0027 Uji verifikasi kiriman C	\N	\N	{"name":"Uji verifikasi kiriman C","category":"lainnya","unit":"pcs","location_id":null,"minimum_stock":5,"account_expense":null,"is_active":true,"notes":null,"code":"ATK-0027","id":27}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 22:02:28
504	2	tester	deleted	App\\Models\\SupplyItem	27	ATK-0027 Uji verifikasi kiriman C	\N	{"id":27,"code":"ATK-0027","name":"Uji verifikasi kiriman C","category":"lainnya","unit":"pcs","minimum_stock":5,"location_id":null,"last_price":null,"account_expense":null,"is_active":true,"notes":null}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 22:02:50
505	\N	\N	created	App\\Models\\Module	14	Serah terima aset	\N	\N	{"code":"asset_transfers","name":"Serah terima aset","description":"Dokumen perpindahan aset antar ruangan, penanggung jawab, dan departemen.","group":"Aset","icon":"heroicon-o-arrow-right-start-on-rectangle","sort":40,"available_actions":"[\\"read\\",\\"create\\",\\"delete\\"]","is_active":true,"id":14}	console	\N	\N	2026-09-06 23:01:27
506	\N	\N	created	App\\Models\\Module	15	Pelepasan aset	\N	\N	{"code":"asset_disposals","name":"Pelepasan aset","description":"Dokumen penjualan, hibah, pemusnahan, dan kehilangan aset beserta dasarnya.","group":"Aset","icon":"heroicon-o-archive-box-x-mark","sort":50,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":15}	console	\N	\N	2026-09-06 23:01:27
507	\N	\N	created	App\\Models\\NumberSequence	51	asset_transfer Nomor serah terima aset	\N	\N	{"code":"asset_transfer","name":"Nomor serah terima aset","prefix":"MA","separator":"\\/","period_format":"Y\\/m","padding":4,"id":51}	console	\N	\N	2026-09-06 23:01:35
508	\N	\N	created	App\\Models\\NumberSequence	52	asset_disposal Nomor pelepasan aset	\N	\N	{"code":"asset_disposal","name":"Nomor pelepasan aset","prefix":"PA","separator":"\\/","period_format":"Y\\/m","padding":4,"id":52}	console	\N	\N	2026-09-06 23:01:35
509	2	tester	created	App\\Models\\AssetTransfer	1	MA/2026/09/0001 OPS-1211-2025-0001	\N	\N	{"asset_id":17,"to_location_id":18,"to_custodian_employee_id":null,"to_department_id":null,"reason":"mutasi_ruangan","transfer_date":"2026-09-06 00:00:00","reference":"BA\\/UJI\\/001","handed_over_by_employee_id":null,"received_by_employee_id":1,"notes":null,"code":"MA\\/2026\\/09\\/0001","created_by_user_id":2,"from_location_id":16,"from_custodian_employee_id":null,"from_department_id":8,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:06:13
510	2	tester	updated	App\\Models\\Asset	17	OPS-1211-2025-0001 Telepon konferensi	\N	{"location_id":16}	{"location_id":18}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:06:13
511	2	tester	created	App\\Models\\AssetTransfer	2	MA/2026/09/0002 OPS-1211-2025-0001	\N	\N	{"asset_id":17,"to_location_id":null,"to_custodian_employee_id":1,"to_department_id":null,"reason":"ganti_pemegang","transfer_date":"2026-09-06 00:00:00","reference":null,"handed_over_by_employee_id":null,"received_by_employee_id":null,"notes":null,"code":"MA\\/2026\\/09\\/0002","created_by_user_id":2,"from_location_id":18,"from_custodian_employee_id":null,"from_department_id":8,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:09:06
512	2	tester	updated	App\\Models\\Asset	17	OPS-1211-2025-0001 Telepon konferensi	\N	{"custodian_employee_id":null}	{"custodian_employee_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:09:06
513	2	tester	updated	App\\Models\\Asset	17	OPS-1211-2025-0001 Telepon konferensi	\N	{"custodian_employee_id":1}	{"custodian_employee_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:09:59
514	2	tester	deleted	App\\Models\\AssetTransfer	2	MA/2026/09/0002 OPS-1211-2025-0001	\N	{"id":2,"code":"MA\\/2026\\/09\\/0002","asset_id":17,"transfer_date":"2026-09-06T00:00:00.000000Z","reason":"ganti_pemegang","from_location_id":18,"from_custodian_employee_id":null,"from_department_id":8,"to_location_id":null,"to_custodian_employee_id":1,"to_department_id":null,"handed_over_by_employee_id":null,"received_by_employee_id":null,"reference":null,"notes":null,"created_by_user_id":2}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:09:59
515	2	tester	created	App\\Models\\AssetDisposal	1	PA/2026/09/0001 OPS-1211-2022-0001	\N	\N	{"asset_id":32,"method":"dijual","disposal_date":"2026-09-06 00:00:00","reference":"BA\\/LEPAS\\/001","proceeds":250000,"counterparty":"Toko contoh untuk peragaan","approved_by_employee_id":null,"document_path":null,"reason":"Rusak berat, biaya perbaikan melebihi harga barang pengganti.","notes":null,"code":"PA\\/2026\\/09\\/0001","created_by_user_id":2,"previous_status":"aktif","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:11:36
516	2	tester	updated	App\\Models\\Asset	32	OPS-1211-2022-0001 Mesin faksimile	\N	{"status":"aktif"}	{"status":"dilepas"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:11:36
517	2	tester	deleted	App\\Models\\AssetDisposal	1	PA/2026/09/0001 OPS-1211-2022-0001	\N	{"id":1,"code":"PA\\/2026\\/09\\/0001","asset_id":32,"disposal_date":"2026-09-06T00:00:00.000000Z","method":"dijual","proceeds":"250000.00","counterparty":"Toko contoh untuk peragaan","reference":"BA\\/LEPAS\\/001","previous_status":"aktif","approved_by_employee_id":null,"document_path":null,"reason":"Rusak berat, biaya perbaikan melebihi harga barang pengganti.","notes":null,"created_by_user_id":2}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:13:52
518	2	tester	updated	App\\Models\\Asset	32	OPS-1211-2022-0001 Mesin faksimile	\N	{"status":"dilepas"}	{"status":"aktif"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-06 23:13:52
519	\N	\N	created	App\\Models\\Setting	12	Kapan penyusutan mulai dihitung	\N	\N	{"key":"penyusutan.mulai","group":"penyusutan","label":"Kapan penyusutan mulai dihitung","description":"Pajak Indonesia menghitung penyusutan sejak bulan pengeluaran dilakukan, sebulan penuh, tanpa memandang tanggalnya. Pilihan kedua menunda ke bulan berikutnya, yang lebih umum dipakai pembukuan komersial.","type":"pilihan","value":"bulan_perolehan","id":12}	console	\N	\N	2026-09-07 02:33:09
520	\N	\N	created	App\\Models\\Setting	13	Nilai sisa bawaan	\N	\N	{"key":"penyusutan.nilai_sisa","group":"penyusutan","label":"Nilai sisa bawaan","description":"Penyusutan fiskal menyusutkan seluruh nilai perolehan sampai habis, jadi bawaannya nol. Pilihan kedua memakai persen nilai sisa yang diatur per kategori aset. Nilai sisa yang diisi langsung pada satu aset selalu menang atas keduanya.","type":"pilihan","value":"nol","id":13}	console	\N	\N	2026-09-07 02:33:09
521	\N	\N	created	App\\Models\\Setting	14	Saldo menurun di tahun terakhir	\N	\N	{"key":"penyusutan.saldo_menurun_akhir","group":"penyusutan","label":"Saldo menurun di tahun terakhir","description":"Saldo menurun murni tidak pernah benar benar habis, karena tiap tahun hanya mengambil sebagian dari sisanya. Kebiasaan pajak menghabiskan sisanya di tahun terakhir. Hanya berlaku untuk aset bermetode saldo menurun ganda.","type":"pilihan","value":"habiskan","id":14}	console	\N	\N	2026-09-07 02:33:09
522	\N	\N	created	App\\Models\\Setting	15	Periode pertama yang dihitung GAIS	\N	\N	{"key":"penyusutan.periode_mulai","group":"penyusutan","label":"Periode pertama yang dihitung GAIS","description":"Bulan pertama yang penyusutannya dicatat di aplikasi ini, ditulis YYYY-MM, contohnya 2026-09. Bulan bulan sebelum ini dianggap sudah dicatat di tempat lain, dan angkanya masuk sebagai akumulasi awal tiap aset. Kosongkan untuk memakai bulan berjalan saat periode pertama ditutup.","type":"text","value":null,"id":15}	console	\N	\N	2026-09-07 02:35:10
523	\N	\N	created	App\\Models\\Module	16	Penyusutan aset	\N	\N	{"code":"depreciation_periods","name":"Penyusutan aset","description":"Penutupan penyusutan bulanan dan beban per aset.","group":"Aset","icon":"heroicon-o-arrow-trending-down","sort":60,"available_actions":"[\\"read\\",\\"close\\",\\"reopen\\"]","is_active":true,"id":16}	console	\N	\N	2026-09-07 02:35:17
524	\N	\N	created	App\\Models\\Module	17	Rekanan	\N	\N	{"code":"vendors","name":"Rekanan","description":"Tukang servis, bengkel, dan pemasok yang mengerjakan pemeliharaan.","group":"Data Induk","icon":"heroicon-o-building-storefront","sort":40,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":17}	console	\N	\N	2026-09-07 03:19:20
525	\N	\N	created	App\\Models\\Module	18	Jadwal pemeliharaan	\N	\N	{"code":"maintenance_schedules","name":"Jadwal pemeliharaan","description":"Pekerjaan preventif yang berulang beserta jatuh temponya.","group":"Pemeliharaan","icon":"heroicon-o-calendar-days","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":18}	console	\N	\N	2026-09-07 03:19:20
526	\N	\N	created	App\\Models\\Module	19	Perintah kerja	\N	\N	{"code":"work_orders","name":"Perintah kerja","description":"Pekerjaan pemeliharaan preventif dan korektif beserta biayanya.","group":"Pemeliharaan","icon":"heroicon-o-wrench-screwdriver","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":19}	console	\N	\N	2026-09-07 03:19:20
527	\N	\N	created	App\\Models\\NumberSequence	53	work_order Nomor perintah kerja	\N	\N	{"code":"work_order","name":"Nomor perintah kerja","prefix":"WO","separator":"\\/","period_format":"Y\\/m","padding":4,"id":53}	console	\N	\N	2026-09-07 03:19:29
528	2	tester	logout	App\\Models\\User	2	tester	\N	\N	\N	http://127.0.0.1:8000/admin/logout	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 04:52:18
530	2	tester	created	App\\Models\\DepreciationPeriod	1	Penyusutan September 2026	\N	\N	{"period":"2026-09","closed_at":"2026-09-07 05:07:03","closed_by_user_id":2,"total_expense":191070562.52,"asset_count":89,"catch_up_count":0,"notes":"Penutupan pertama, diuji lewat peramban.","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:07:03
531	2	tester	deleted	App\\Models\\DepreciationPeriod	1	Penyusutan September 2026	\N	{"id":1,"period":"2026-09","closed_at":"2026-09-07T05:07:03.000000Z","closed_by_user_id":2,"total_expense":"191070562.52","asset_count":89,"catch_up_count":0,"notes":"Penutupan pertama, diuji lewat peramban."}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:08:41
532	2	tester	created	App\\Models\\DepreciationPeriod	2	Penyusutan September 2026	\N	\N	{"period":"2026-09","closed_at":"2026-09-07 05:10:13","closed_by_user_id":2,"total_expense":191070562.52,"asset_count":89,"catch_up_count":0,"notes":"Ditutup ulang setelah uji buka kembali.","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:10:13
533	2	tester	deleted	App\\Models\\DepreciationPeriod	2	Penyusutan September 2026	\N	{"id":2,"period":"2026-09","closed_at":"2026-09-07T05:10:13.000000Z","closed_by_user_id":2,"total_expense":"191070562.52","asset_count":89,"catch_up_count":0,"notes":"Ditutup ulang setelah uji buka kembali."}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:10:52
534	2	tester	created	App\\Models\\DepreciationPeriod	3	Penyusutan September 2026	\N	\N	{"period":"2026-09","closed_at":"2026-09-07 05:11:20","closed_by_user_id":2,"total_expense":191070562.52,"asset_count":89,"catch_up_count":0,"notes":"Penutupan September 2026, diverifikasi lewat peramban.","id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:11:20
535	2	tester	created	App\\Models\\Vendor	1	VND-AC-01 [DATA UJI] Sejuk Abadi Teknik	\N	\N	{"code":"VND-AC-01","name":"[DATA UJI] Sejuk Abadi Teknik","type":"jasa","specialization":"AC dan pendingin ruangan","contact_person":"Bagian layanan","phone":null,"email":null,"tax_number":null,"address":null,"notes":null,"is_active":true,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:11:52
536	2	tester	created	App\\Models\\MaintenanceSchedule	1	[DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	\N	{"asset_id":2,"name":"[DATA UJI] Servis rutin dan cuci AC","tasks":"Cuci filter dan evaporator\\nPeriksa tekanan freon","interval_months":3,"last_done_date":"2026-06-10 00:00:00","is_active":true,"vendor_id":1,"technician_employee_id":null,"estimated_cost":null,"notes":null,"next_due_date":"2026-09-10 00:00:00","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:12:22
537	2	tester	created	App\\Models\\MaintenanceVisit	1	Kunjungan 1 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	\N	{"asset_id":2,"sequence":1,"due_date":"2026-09-10 00:00:00","status":"dijadwalkan","vendor_id":1,"technician_employee_id":null,"maintenance_schedule_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:12:22
538	2	tester	updated	App\\Models\\MaintenanceVisit	1	Kunjungan 1 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"status":"dijadwalkan","completed_date":null,"result":null,"cost":null,"closed_by_user_id":null}	{"status":"dikerjakan","completed_date":"2026-09-05 00:00:00","result":"Filter dan evaporator dicuci, freon ditambah 0,3 kg, tidak ada kebocoran.","cost":850000,"closed_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:06
539	2	tester	updated	App\\Models\\MaintenanceSchedule	1	[DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"last_done_date":"2026-06-10T00:00:00.000000Z","next_due_date":"2026-09-10T00:00:00.000000Z"}	{"last_done_date":"2026-09-05 00:00:00","next_due_date":"2026-12-05 00:00:00"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:06
540	2	tester	created	App\\Models\\MaintenanceVisit	2	Kunjungan 2 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	\N	{"asset_id":2,"sequence":2,"due_date":"2026-12-05 00:00:00","status":"dijadwalkan","vendor_id":1,"technician_employee_id":null,"maintenance_schedule_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:06
541	2	tester	updated	App\\Models\\MaintenanceVisit	2	Kunjungan 2 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"status":"dijadwalkan","skip_reason":null,"closed_by_user_id":null}	{"status":"dilewati","skip_reason":"Unit sedang tidak dipakai karena ruangannya direnovasi.","closed_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:27
542	2	tester	updated	App\\Models\\MaintenanceSchedule	1	[DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"next_due_date":"2026-12-05T00:00:00.000000Z"}	{"next_due_date":"2027-03-05 00:00:00"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:27
543	2	tester	created	App\\Models\\MaintenanceVisit	3	Kunjungan 3 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	\N	{"asset_id":2,"sequence":3,"due_date":"2027-03-05 00:00:00","status":"dijadwalkan","vendor_id":1,"technician_employee_id":null,"maintenance_schedule_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:13:27
544	2	tester	created	App\\Models\\WorkOrder	1	WO/2026/09/0001 GA-1202-2012-0001	\N	\N	{"asset_id":2,"type":"preventif","maintenance_schedule_id":1,"maintenance_visit_id":3,"priority":"normal","status":"dibuka","reported_date":"2026-09-07 00:00:00","problem":"[DATA UJI] Servis rutin dan cuci AC\\n\\nCuci filter dan evaporator\\nPeriksa tekanan freon","scheduled_date":"2027-03-05 00:00:00","vendor_id":1,"technician_employee_id":null,"code":"WO\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:15:04
545	2	tester	updated	App\\Models\\WorkOrder	1	WO/2026/09/0001 GA-1202-2012-0001	\N	{"status":"dibuka","completed_date":null,"work_done":null,"cost":null}	{"status":"selesai","completed_date":"2026-09-07 00:00:00","work_done":"Servis lengkap, filter diganti, kompresor diperiksa.","cost":1250000}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:16:55
546	2	tester	updated	App\\Models\\MaintenanceVisit	3	Kunjungan 3 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"status":"dijadwalkan","completed_date":null,"result":null,"cost":null,"closed_by_user_id":null}	{"status":"dikerjakan","completed_date":"2026-09-07 00:00:00","result":"Servis lengkap, filter diganti, kompresor diperiksa.","cost":"1250000.00","closed_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:16:55
547	2	tester	updated	App\\Models\\MaintenanceSchedule	1	[DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	{"last_done_date":"2026-09-05T00:00:00.000000Z","next_due_date":"2027-03-05T00:00:00.000000Z"}	{"last_done_date":"2026-09-07 00:00:00","next_due_date":"2026-12-07 00:00:00"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:16:55
548	2	tester	created	App\\Models\\MaintenanceVisit	4	Kunjungan 4 [DATA UJI] Servis rutin dan cuci AC (GA-1202-2012-0001)	\N	\N	{"asset_id":2,"sequence":4,"due_date":"2026-12-07 00:00:00","status":"dijadwalkan","vendor_id":1,"technician_employee_id":null,"maintenance_schedule_id":1,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 05:16:55
549	1	Administrator	logout	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/admin/logout	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 05:27:33
550	1	Administrator	login	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 05:27:49
551	1	Administrator	created	App\\Models\\WorkOrder	2	WO/2026/09/0002 FIN-1209-2018-0001	\N	\N	{"asset_id":34,"type":"korektif","priority":"normal","reported_date":"2026-09-07 00:00:00","reported_by_employee_id":2,"problem":"layar blank","scheduled_date":"2026-09-18 00:00:00","vendor_id":1,"technician_employee_id":8,"code":"WO\\/2026\\/09\\/0002","created_by_user_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 05:29:12
552	\N	\N	created	App\\Models\\Module	20	Permintaan perbaikan	\N	\N	{"code":"service_requests","name":"Permintaan perbaikan","description":"Tiket kerusakan dari karyawan, persetujuan atasannya, dan penerimaannya oleh tim GA.","group":"Pemeliharaan","icon":"heroicon-o-lifebuoy","sort":5,"available_actions":"[\\"read\\",\\"read_all\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"accept\\"]","is_active":true,"id":20}	console	\N	\N	2026-09-07 05:52:52
553	\N	\N	created	App\\Models\\Module	21	Jenis permintaan	\N	\N	{"code":"service_request_categories","name":"Jenis permintaan","description":"Kelompok permintaan perbaikan beserta prioritas bawaan dan target waktu penyelesaiannya.","group":"Data Induk","icon":"heroicon-o-tag","sort":50,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":21}	console	\N	\N	2026-09-07 05:52:52
554	\N	\N	created	App\\Models\\Setting	16	Permintaan mendesak	\N	\N	{"key":"layanan.mendesak_lewati_persetujuan","group":"layanan","label":"Permintaan mendesak","description":"Kebocoran air, listrik mati, dan lift berhenti tidak bisa menunggu atasan membuka aplikasi. Bawaannya, permintaan berprioritas mendesak langsung masuk antrean tim GA, dan alasan lompatan itu tertulis di tiketnya sehingga tetap terbaca siapa pun yang membukanya. Ubah ke pilihan kedua kalau perusahaan menghendaki semua permintaan lewat persetujuan tanpa kecuali.","type":"pilihan","value":"ya","id":16}	console	\N	\N	2026-09-07 05:53:03
555	\N	\N	created	App\\Models\\NumberSequence	54	service_request Nomor permintaan perbaikan	\N	\N	{"code":"service_request","name":"Nomor permintaan perbaikan","prefix":"PB","separator":"\\/","period_format":"Y\\/m","padding":4,"id":54}	console	\N	\N	2026-09-07 05:53:04
556	\N	\N	created	App\\Models\\ServiceRequestCategory	1	LST Listrik dan penerangan	\N	\N	{"code":"LST","name":"Listrik dan penerangan","default_priority":"tinggi","description":"Lampu mati, stopkontak tidak berfungsi, MCB turun berulang.","is_active":true,"id":1}	console	\N	\N	2026-09-07 05:53:05
557	\N	\N	created	App\\Models\\ServiceRequestCategory	2	AC Pendingin ruangan	\N	\N	{"code":"AC","name":"Pendingin ruangan","default_priority":"normal","description":"AC tidak dingin, bocor, atau berisik.","is_active":true,"id":2}	console	\N	\N	2026-09-07 05:53:05
558	\N	\N	created	App\\Models\\ServiceRequestCategory	3	AIR Air dan sanitasi	\N	\N	{"code":"AIR","name":"Air dan sanitasi","default_priority":"tinggi","description":"Kebocoran pipa, keran rusak, toilet mampet.","is_active":true,"id":3}	console	\N	\N	2026-09-07 05:53:05
559	\N	\N	created	App\\Models\\ServiceRequestCategory	4	MBL Meja, kursi, dan lemari	\N	\N	{"code":"MBL","name":"Meja, kursi, dan lemari","default_priority":"rendah","description":"Perabot kantor yang rusak, goyang, atau perlu dipindahkan.","is_active":true,"id":4}	console	\N	\N	2026-09-07 05:53:05
560	\N	\N	created	App\\Models\\ServiceRequestCategory	5	PTU Pintu, kunci, dan jendela	\N	\N	{"code":"PTU","name":"Pintu, kunci, dan jendela","default_priority":"normal","description":"Kunci macet, engsel lepas, kaca retak.","is_active":true,"id":5}	console	\N	\N	2026-09-07 05:53:05
561	\N	\N	created	App\\Models\\ServiceRequestCategory	6	KBR Kebersihan	\N	\N	{"code":"KBR","name":"Kebersihan","default_priority":"normal","description":"Permintaan pembersihan di luar jadwal rutin.","is_active":true,"id":6}	console	\N	\N	2026-09-07 05:53:05
562	\N	\N	created	App\\Models\\ServiceRequestCategory	7	JRG Jaringan dan kelistrikan data	\N	\N	{"code":"JRG","name":"Jaringan dan kelistrikan data","default_priority":"tinggi","description":"Titik LAN mati, kabel putus, rak jaringan. Perangkatnya sendiri urusan tim IT.","is_active":true,"id":7}	console	\N	\N	2026-09-07 05:53:05
563	\N	\N	created	App\\Models\\ServiceRequestCategory	8	LFT Lift dan mesin gedung	\N	\N	{"code":"LFT","name":"Lift dan mesin gedung","default_priority":"mendesak","description":"Lift berhenti, genset, pompa, atau mesin gedung lain yang berhenti bekerja.","is_active":true,"id":8}	console	\N	\N	2026-09-07 05:53:05
564	\N	\N	created	App\\Models\\ServiceRequestCategory	9	LAIN Lainnya	\N	\N	{"code":"LAIN","name":"Lainnya","default_priority":"normal","description":"Permintaan yang belum masuk jenis mana pun. Tim GA memindahkannya saat menerima.","is_active":true,"id":9}	console	\N	\N	2026-09-07 05:53:05
565	1	Administrator	created	App\\Models\\ServiceRequest	1	PB/2026/09/0001 AC berasap	\N	\N	{"service_request_category_id":2,"priority":"tinggi","title":"AC berasap","description":"Per pagi ini ac mulai berasap...","location_id":4,"asset_id":null,"requester_employee_id":8,"code":"PB\\/2026\\/09\\/0001","created_by_user_id":1,"submitted_at":"2026-09-07 05:55:51","department_id":6,"status":"disetujui","approved_at":"2026-09-07 05:55:51","approval_skipped_reason":"Departemen pemohon belum punya kepala departemen","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 05:55:51
566	1	Administrator	created	App\\Models\\WorkOrder	5	WO/2026/09/0005 	\N	\N	{"asset_id":null,"service_request_id":1,"type":"korektif","priority":"tinggi","status":"dibuka","reported_date":"2026-09-07 00:00:00","reported_by_employee_id":8,"problem":"AC berasap\\n\\nPer pagi ini ac mulai berasap...\\n\\nLokasi: Head Office","scheduled_date":"2026-09-10 00:00:00","vendor_id":1,"technician_employee_id":8,"code":"WO\\/2026\\/09\\/0005","created_by_user_id":1,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 06:37:28
567	1	Administrator	updated	App\\Models\\ServiceRequest	1	PB/2026/09/0001 AC berasap	\N	{"status":"disetujui","accepted_by_user_id":null,"accepted_at":null,"work_order_id":null}	{"status":"diterima","accepted_by_user_id":1,"accepted_at":"2026-09-07 06:37:28","work_order_id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 06:37:28
568	1	Administrator	created	App\\Models\\WorkOrderAttachment	1	foto (WO/2026/09/0005)	\N	\N	{"type":"foto_kerusakan","name":"foto","file_path":"lampiran-perintah-kerja\\/01M1X9E4N58FEV0EGRAPPKAGYE.jpeg","notes":"rusak","original_name":"genset.jpeg","work_order_id":5,"size_bytes":53041,"uploaded_by_user_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 06:38:36
569	1	Administrator	created	App\\Models\\Vendor	2	VND-001 PT. Abadi Jaya	\N	\N	{"code":"VND-001","name":"PT. Abadi Jaya","type":"jasa","specialization":"Konsultan","contact_person":"Toto  Priyono","phone":"08112502883","email":"toto.priyono@gmail.com","tax_number":"123452345","address":"Sidorejo selomartani kalasan sleman yk","notes":"ok","is_active":true,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 06:56:03
570	\N	\N	created	App\\Models\\Module	22	Kendaraan dinas	\N	\N	{"code":"vehicles","name":"Kendaraan dinas","description":"Data kendaraan yang menempel pada aset, beserta pajak, STNK, KIR, dan asuransinya.","group":"Kendaraan","icon":"heroicon-o-truck","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":22}	console	\N	\N	2026-09-07 10:24:00
571	2	tester	created	App\\Models\\Vehicle	1	B 1234 UJI Toyota Avanza Tipe 266 2018	\N	\N	{"asset_id":148,"plate_number":"B 1234 UJI","vehicle_type":"mobil_penumpang","usage_mode":"pool","default_driver_employee_id":null,"is_active":true,"chassis_number":"MHKA1234567890123","engine_number":null,"production_year":2018,"color":null,"fuel_type":"bensin","transmission":null,"seat_capacity":null,"last_odometer_km":84210,"last_odometer_date":null,"notes":"[DATA UJI] dibuat untuk pengujian kiriman H","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:30:24
572	2	tester	created	App\\Models\\VehicleDocument	1	Pajak tahunan dan pengesahan STNK B 1234 UJI	\N	\N	{"type":"pajak_tahunan","document_number":"SKPD-2025-0001","issued_date":"2025-08-20 00:00:00","expires_at":"2026-08-20 00:00:00","issuer":"Samsat Jakarta Selatan","cost":3450000,"file_path":null,"notes":null,"original_name":null,"vehicle_id":1,"size_bytes":null,"uploaded_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:38:20
573	2	tester	created	App\\Models\\VehicleDocument	2	Uji berkala KIR B 1234 UJI	\N	\N	{"type":"kir","document_number":"KIR-778","issued_date":"2026-04-01 00:00:00","expires_at":"2026-10-01 00:00:00","issuer":"Dishub DKI Jakarta","cost":275000,"file_path":null,"notes":null,"original_name":null,"vehicle_id":1,"size_bytes":null,"uploaded_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:38:41
574	2	tester	created	App\\Models\\VehicleDocument	3	Pajak tahunan dan pengesahan STNK B 1234 UJI	\N	\N	{"issued_date":"2026-09-07 00:00:00","expires_at":"2027-08-20 00:00:00","document_number":"SKPD-2025-0001","issuer":"Samsat Jakarta Selatan","cost":3720000,"file_path":null,"original_name":null,"type":"pajak_tahunan","vehicle_id":1,"size_bytes":null,"uploaded_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:39:26
588	2	tester	created	App\\Models\\VehicleBooking	3	PK/2026/09/0003 ke [DATA UJI] Bandara Soekarno Hatta	\N	\N	{"destination":"[DATA UJI] Bandara Soekarno Hatta","purpose":"[DATA UJI] Menjemput tamu kantor pusat.","start_at":"2026-09-10 15:00:00","end_at":"2026-09-10 20:00:00","passenger_count":2,"needs_driver":false,"requester_employee_id":1,"notes":null,"code":"PK\\/2026\\/09\\/0003","created_by_user_id":2,"department_id":1,"status":"disetujui","approved_at":"2026-09-07 11:30:46","approval_skipped_reason":"Departemen pemohon belum punya kepala departemen","id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:30:46
575	2	tester	created	App\\Models\\ServiceRequest	2	PB/2026/09/0002 [DATA UJI] Keran pantry lantai 2 menetes	\N	\N	{"service_request_category_id":2,"priority":"normal","title":"[DATA UJI] Keran pantry lantai 2 menetes","description":"[DATA UJI] Dibuat untuk menguji unggah foto pada permintaan perbaikan.","location_id":1,"asset_id":null,"requester_employee_id":1,"code":"PB\\/2026\\/09\\/0002","created_by_user_id":2,"submitted_at":"2026-09-07 10:41:13","department_id":1,"status":"disetujui","approved_at":"2026-09-07 10:41:13","approval_skipped_reason":"Departemen pemohon belum punya kepala departemen","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:41:13
576	2	tester	created	App\\Models\\VehiclePhoto	1	[DATA UJI] Tampak depan saat pendataan (B 1234 UJI)	\N	\N	{"type":"depan","name":"[DATA UJI] Tampak depan saat pendataan","taken_date":"2026-09-07 00:00:00","odometer_km":84210,"file_path":"foto-kendaraan\\/01M1XQZ5F4XN7XWB7SC5JYKXKV.png","notes":null,"original_name":"data-uji-tampak-depan.png","vehicle_id":1,"size_bytes":1810,"uploaded_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 10:52:34
577	\N	\N	created	App\\Models\\Module	23	Pemesanan kendaraan	\N	\N	{"code":"vehicle_bookings","name":"Pemesanan kendaraan","description":"Pemesanan pool car, persetujuan atasan, dan penugasan kendaraan beserta sopirnya.","group":"Kendaraan","icon":"heroicon-o-calendar-days","sort":20,"available_actions":"[\\"read\\",\\"read_all\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"assign\\"]","is_active":true,"id":23}	console	\N	\N	2026-09-07 11:20:58
578	\N	\N	created	App\\Models\\NumberSequence	55	vehicle_booking Nomor pemesanan kendaraan	\N	\N	{"code":"vehicle_booking","name":"Nomor pemesanan kendaraan","prefix":"PK","separator":"\\/","period_format":"Y\\/m","padding":4,"id":55}	console	\N	\N	2026-09-07 11:20:59
579	2	tester	created	App\\Models\\VehicleBooking	1	PK/2026/09/0001 ke [DATA UJI] Gudang Bekasi	\N	\N	{"destination":"[DATA UJI] Gudang Bekasi","purpose":"[DATA UJI] Mengantar sampel barang dan menjemput dokumen.","start_at":"2026-09-10 08:00:00","end_at":"2026-09-10 16:00:00","passenger_count":3,"needs_driver":true,"requester_employee_id":1,"notes":null,"code":"PK\\/2026\\/09\\/0001","created_by_user_id":2,"department_id":1,"status":"disetujui","approved_at":"2026-09-07 11:22:43","approval_skipped_reason":"Departemen pemohon belum punya kepala departemen","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:22:43
580	2	tester	created	App\\Models\\VehicleBooking	2	PK/2026/09/0002 ke [DATA UJI] Kantor pajak Jaksel	\N	\N	{"destination":"[DATA UJI] Kantor pajak Jaksel","purpose":"[DATA UJI] Menyerahkan berkas pajak tahunan.","start_at":"2026-09-10 13:00:00","end_at":"2026-09-10 18:00:00","passenger_count":2,"needs_driver":false,"requester_employee_id":1,"notes":null,"code":"PK\\/2026\\/09\\/0002","created_by_user_id":2,"department_id":1,"status":"disetujui","approved_at":"2026-09-07 11:23:13","approval_skipped_reason":"Departemen pemohon belum punya kepala departemen","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:23:13
581	2	tester	updated	App\\Models\\VehicleBooking	1	PK/2026/09/0001 ke [DATA UJI] Gudang Bekasi	\N	{"status":"disetujui","vehicle_id":null,"driver_employee_id":null,"assigned_by_user_id":null,"assigned_at":null}	{"status":"ditugaskan","vehicle_id":1,"driver_employee_id":2,"assigned_by_user_id":2,"assigned_at":"2026-09-07 11:23:41"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:23:41
582	2	tester	created	App\\Models\\VehicleTrip	1	B 1234 UJI ke [DATA UJI] Gudang Bekasi	\N	\N	{"departed_at":"2026-09-10 08:05:00","start_odometer_km":84210,"destination":"[DATA UJI] Gudang Bekasi","driver_employee_id":2,"vehicle_booking_id":1,"purpose":"[DATA UJI] Mengantar sampel barang.","vehicle_id":1,"created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:24:57
583	2	tester	updated	App\\Models\\VehicleTrip	1	B 1234 UJI ke [DATA UJI] Gudang Bekasi	\N	{"returned_at":null,"end_odometer_km":null}	{"returned_at":"2026-09-10 16:20:00","end_odometer_km":84356}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:25:34
584	2	tester	updated	App\\Models\\Vehicle	1	B 1234 UJI Toyota Avanza Tipe 266 2018	\N	{"last_odometer_km":84210,"last_odometer_date":null}	{"last_odometer_km":84356,"last_odometer_date":"2026-09-10 00:00:00"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:25:34
585	2	tester	updated	App\\Models\\VehicleBooking	1	PK/2026/09/0001 ke [DATA UJI] Gudang Bekasi	\N	{"status":"ditugaskan","closed_at":null}	{"status":"selesai","closed_at":"2026-09-07 11:25:34"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:25:34
586	2	tester	created	App\\Models\\VehicleRefueling	1	B 1234 UJI 31,00 liter	\N	\N	{"filled_at":"2026-08-14 00:00:00","odometer_km":83500,"liters":31,"cost":465000,"is_full_tank":true,"station":"[DATA UJI] SPBU Cakung","driver_employee_id":null,"fuel_type":"bensin","notes":null,"vehicle_id":1,"created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:29:23
587	2	tester	created	App\\Models\\VehicleRefueling	2	B 1234 UJI 52,00 liter	\N	\N	{"filled_at":"2026-09-04 00:00:00","odometer_km":84210,"liters":52,"cost":780000,"is_full_tank":true,"station":"[DATA UJI] SPBU Bekasi","driver_employee_id":null,"fuel_type":"bensin","notes":null,"vehicle_id":1,"created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:29:41
609	\N	\N	updated	App\\Models\\ExpenseCategory	5	LSTR Listrik, air, dan telepon	\N	{"description":"Tagihan utilitas bulanan. Menunggu modul tagihan."}	{"description":"Tagihan utilitas bulanan, dijumlahkan dari faktur rekanan yang sudah disetujui."}	console	\N	\N	2026-09-07 12:33:34
589	2	tester	updated	App\\Models\\VehicleBooking	2	PK/2026/09/0002 ke [DATA UJI] Kantor pajak Jaksel	\N	{"status":"disetujui","vehicle_id":null,"assigned_by_user_id":null,"assigned_at":null}	{"status":"ditugaskan","vehicle_id":1,"assigned_by_user_id":2,"assigned_at":"2026-09-07 11:30:57"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:30:57
590	\N	\N	created	App\\Models\\Module	24	Kategori biaya	\N	\N	{"code":"expense_categories","name":"Kategori biaya","description":"Kelompok biaya GA beserta sumber realisasinya dan pemetaan ke akun perusahaan.","group":"Data Induk","icon":"heroicon-o-banknotes","sort":60,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":24}	console	\N	\N	2026-09-07 11:54:38
591	\N	\N	created	App\\Models\\Module	25	Anggaran dan realisasi	\N	\N	{"code":"budgets","name":"Anggaran dan realisasi","description":"Pagu per departemen per kategori per tahun, dibandingkan dengan realisasi yang dijumlahkan sendiri dari catatan yang sudah ada.","group":"Anggaran","icon":"heroicon-o-calculator","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":25}	console	\N	\N	2026-09-07 11:54:38
592	\N	\N	created	App\\Models\\ExpenseCategory	1	PMLH Pemeliharaan gedung dan peralatan	\N	\N	{"code":"PMLH","name":"Pemeliharaan gedung dan peralatan","source":"pemeliharaan","description":"Servis, suku cadang, dan jasa tukang. Dijumlahkan dari perintah kerja dan kunjungan pemeliharaan yang sudah selesai.","is_active":true,"id":1}	console	\N	\N	2026-09-07 11:54:39
593	\N	\N	created	App\\Models\\ExpenseCategory	2	BBM Bahan bakar kendaraan	\N	\N	{"code":"BBM","name":"Bahan bakar kendaraan","source":"bbm","description":"Dijumlahkan dari pengisian BBM yang dicatat pada tiap kendaraan.","is_active":true,"id":2}	console	\N	\N	2026-09-07 11:54:39
594	\N	\N	created	App\\Models\\ExpenseCategory	3	DOKKEN Pajak dan dokumen kendaraan	\N	\N	{"code":"DOKKEN","name":"Pajak dan dokumen kendaraan","source":"dokumen_kendaraan","description":"Pajak tahunan, perpanjangan STNK, KIR, dan asuransi. Dibebankan menurut tanggal terbit dokumennya.","is_active":true,"id":3}	console	\N	\N	2026-09-07 11:54:39
595	\N	\N	created	App\\Models\\ExpenseCategory	4	ATK Alat tulis dan barang habis pakai	\N	\N	{"code":"ATK","name":"Alat tulis dan barang habis pakai","source":"persediaan","description":"Dijumlahkan dari barang yang keluar gudang, dinilai dengan harga pada mutasinya atau harga pembelian terakhir.","is_active":true,"id":4}	console	\N	\N	2026-09-07 11:54:39
596	\N	\N	created	App\\Models\\ExpenseCategory	5	LSTR Listrik, air, dan telepon	\N	\N	{"code":"LSTR","name":"Listrik, air, dan telepon","source":"manual","description":"Tagihan utilitas bulanan. Menunggu modul tagihan.","is_active":true,"id":5}	console	\N	\N	2026-09-07 11:54:39
597	\N	\N	created	App\\Models\\ExpenseCategory	6	KBRS Kebersihan dan keamanan	\N	\N	{"code":"KBRS","name":"Kebersihan dan keamanan","source":"manual","description":"Jasa cleaning service dan satpam. Menunggu modul tagihan.","is_active":true,"id":6}	console	\N	\N	2026-09-07 11:54:39
598	\N	\N	created	App\\Models\\ExpenseCategory	7	SEWA Sewa gedung dan peralatan	\N	\N	{"code":"SEWA","name":"Sewa gedung dan peralatan","source":"manual","description":"Sewa ruang, mesin fotokopi, dan peralatan lain. Menunggu modul tagihan.","is_active":true,"id":7}	console	\N	\N	2026-09-07 11:54:39
599	\N	\N	created	App\\Models\\ExpenseCategory	8	RMTG Rumah tangga kantor	\N	\N	{"code":"RMTG","name":"Rumah tangga kantor","source":"manual","description":"Konsumsi rapat, galon, dan keperluan harian kantor. Menunggu modul tagihan.","is_active":true,"id":8}	console	\N	\N	2026-09-07 11:54:39
600	\N	\N	created	App\\Models\\ExpenseCategory	9	TRNS Transportasi dan perjalanan	\N	\N	{"code":"TRNS","name":"Transportasi dan perjalanan","source":"manual","description":"Transportasi daring, taksi, dan tol. Menunggu modul tagihan dan reimbursement.","is_active":true,"id":9}	console	\N	\N	2026-09-07 11:54:39
601	\N	\N	created	App\\Models\\ExpenseCategory	10	LAIN Biaya GA lainnya	\N	\N	{"code":"LAIN","name":"Biaya GA lainnya","source":"manual","description":"Biaya yang belum masuk kategori mana pun.","is_active":true,"id":10}	console	\N	\N	2026-09-07 11:54:39
602	2	tester	created	App\\Models\\Budget	1	Finance Bahan bakar kendaraan 2026	\N	\N	{"department_id":5,"expense_category_id":2,"fiscal_year":2026,"amount":2000000,"notes":"[DATA UJI] pagu BBM","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:57:40
603	2	tester	created	App\\Models\\Budget	2	Finance Pajak dan dokumen kendaraan 2026	\N	\N	{"department_id":5,"expense_category_id":3,"fiscal_year":2026,"amount":3000000,"notes":"[DATA UJI] pagu dokumen kendaraan","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:59:24
604	2	tester	created	App\\Models\\Budget	3	Finance Sewa gedung dan peralatan 2026	\N	\N	{"department_id":5,"expense_category_id":7,"fiscal_year":2026,"amount":50000000,"notes":"[DATA UJI] pagu pemeliharaan","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 11:59:42
605	2	tester	created	App\\Models\\Budget	4	Finance Pemeliharaan gedung dan peralatan 2026	\N	\N	{"department_id":5,"expense_category_id":1,"fiscal_year":2026,"amount":20000000,"notes":"[DATA UJI] pagu pemeliharaan","created_by_user_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:00:51
606	2	tester	created	App\\Models\\Budget	5	General Affair Pemeliharaan gedung dan peralatan 2026	\N	\N	{"department_id":4,"expense_category_id":1,"fiscal_year":2026,"amount":10000000,"notes":"[DATA UJI] pagu pemeliharaan GA","created_by_user_id":2,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:03:46
607	\N	\N	created	App\\Models\\Module	26	Tagihan rekanan	\N	\N	{"code":"vendor_bills","name":"Tagihan rekanan","description":"Faktur dari rekanan, pembebanannya ke departemen dan kategori biaya, persetujuan, dan penandaan pembayaran.","group":"Anggaran","icon":"heroicon-o-document-currency-dollar","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"pay\\"]","is_active":true,"id":26}	console	\N	\N	2026-09-07 12:33:30
608	\N	\N	created	App\\Models\\NumberSequence	56	vendor_bill Nomor tagihan rekanan	\N	\N	{"code":"vendor_bill","name":"Nomor tagihan rekanan","prefix":"TG","separator":"\\/","period_format":"Y\\/m","padding":4,"id":56}	console	\N	\N	2026-09-07 12:33:33
610	\N	\N	updated	App\\Models\\ExpenseCategory	6	KBRS Kebersihan dan keamanan	\N	{"description":"Jasa cleaning service dan satpam. Menunggu modul tagihan."}	{"description":"Jasa cleaning service dan satpam, dijumlahkan dari faktur rekanan yang sudah disetujui."}	console	\N	\N	2026-09-07 12:33:34
611	\N	\N	updated	App\\Models\\ExpenseCategory	7	SEWA Sewa gedung dan peralatan	\N	{"description":"Sewa ruang, mesin fotokopi, dan peralatan lain. Menunggu modul tagihan."}	{"description":"Sewa ruang, mesin fotokopi, dan peralatan lain, dijumlahkan dari faktur rekanan yang sudah disetujui."}	console	\N	\N	2026-09-07 12:33:35
612	\N	\N	updated	App\\Models\\ExpenseCategory	8	RMTG Rumah tangga kantor	\N	{"description":"Konsumsi rapat, galon, dan keperluan harian kantor. Menunggu modul tagihan."}	{"description":"Konsumsi rapat, galon, dan keperluan harian kantor, dijumlahkan dari faktur rekanan yang sudah disetujui."}	console	\N	\N	2026-09-07 12:33:35
613	\N	\N	updated	App\\Models\\ExpenseCategory	9	TRNS Transportasi dan perjalanan	\N	{"description":"Transportasi daring, taksi, dan tol. Menunggu modul tagihan dan reimbursement."}	{"description":"Transportasi daring, taksi, dan tol. Dijumlahkan dari faktur rekanan, dan nanti juga dari penggantian biaya karyawan."}	console	\N	\N	2026-09-07 12:33:35
614	2	tester	created	App\\Models\\VendorBill	1	TG/2026/09/0001 [DATA UJI] Sejuk Abadi Teknik	\N	\N	{"vendor_id":1,"invoice_number":"INV-SAT-2608","invoice_date":"2026-08-31 00:00:00","due_date":"2026-09-15 00:00:00","description":"[DATA UJI] Jasa kebersihan dan keamanan gedung Head Office periode Agustus 2026.","file_path":null,"notes":null,"original_name":null,"code":"TG\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:37:59
615	2	tester	created	App\\Models\\VendorBillLine	1	Kebersihan dan keamanan Rp 4.500.000	\N	\N	{"expense_category_id":6,"department_id":5,"amount":4500000,"description":"Porsi lantai 2 dan 3","vendor_bill_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:39:01
616	2	tester	created	App\\Models\\VendorBillLine	2	Kebersihan dan keamanan Rp 3.200.000	\N	\N	{"expense_category_id":6,"department_id":4,"amount":3200000,"description":"Porsi lantai 1 dan lobi","vendor_bill_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:39:28
617	2	tester	created	App\\Models\\VendorBillLine	3	Kebersihan dan keamanan Rp 1.300.000	\N	\N	{"expense_category_id":6,"department_id":null,"amount":1300000,"description":"Koridor dan area parkir bersama","vendor_bill_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:39:40
618	2	tester	updated	App\\Models\\VendorBill	1	TG/2026/09/0001 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"draft"}	{"status":"diajukan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:41:24
619	2	tester	created	App\\Models\\Budget	6	Finance Kebersihan dan keamanan 2026	\N	\N	{"department_id":5,"expense_category_id":6,"fiscal_year":2026,"amount":10000000,"notes":"[DATA UJI] Pagu kebersihan dan keamanan Finance 2026.","created_by_user_id":2,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:44:05
620	2	tester	updated	App\\Models\\VendorBill	1	TG/2026/09/0001 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"diajukan","approved_by_user_id":null,"approved_at":null}	{"status":"disetujui","approved_by_user_id":2,"approved_at":"2026-09-07 12:45:33"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:45:33
621	2	tester	updated	App\\Models\\VendorBill	1	TG/2026/09/0001 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"disetujui","paid_date":null,"payment_reference":null,"paid_by_user_id":null}	{"status":"dibayar","paid_date":"2026-09-07 00:00:00","payment_reference":"TRF-BCA-20260907-118","paid_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:48:58
622	2	tester	created	App\\Models\\VendorBill	2	TG/2026/09/0002 [DATA UJI] Sejuk Abadi Teknik	\N	\N	{"vendor_id":1,"invoice_number":"INV-PLN-0926","invoice_date":"2026-09-01 00:00:00","due_date":"2026-08-25 00:00:00","description":"[DATA UJI] Tagihan uji untuk memeriksa jalur penolakan dan perbaikan.","file_path":null,"notes":null,"original_name":null,"code":"TG\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:49:33
623	2	tester	created	App\\Models\\VendorBillLine	4	Listrik, air, dan telepon Rp 2.750.000	\N	\N	{"expense_category_id":5,"department_id":4,"amount":2750000,"description":"Pemakaian gedung utama","vendor_bill_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:50:02
624	2	tester	updated	App\\Models\\VendorBill	2	TG/2026/09/0002 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"draft"}	{"status":"diajukan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:50:15
625	2	tester	updated	App\\Models\\VendorBill	2	TG/2026/09/0002 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"diajukan","rejection_reason":null}	{"status":"ditolak","rejection_reason":"Pembebanan seharusnya dibagi dengan departemen Operations, bukan seluruhnya ke General Affair."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:50:33
626	2	tester	updated	App\\Models\\VendorBill	2	TG/2026/09/0002 [DATA UJI] Sejuk Abadi Teknik	\N	{"status":"ditolak"}	{"status":"draft"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 12:50:50
627	\N	\N	created	App\\Models\\Module	27	Penggantian biaya	\N	\N	{"code":"reimbursements","name":"Penggantian biaya","description":"Pengajuan penggantian biaya karyawan beserta struknya, persetujuan atasan, pemeriksaan tim GA, dan penandaan transfer.","group":"Anggaran","icon":"heroicon-o-receipt-percent","sort":30,"available_actions":"[\\"read\\",\\"read_all\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"verify\\",\\"pay\\"]","is_active":true,"id":27}	console	\N	\N	2026-09-07 13:33:11
628	\N	\N	created	App\\Models\\NumberSequence	57	reimbursement Nomor penggantian biaya	\N	\N	{"code":"reimbursement","name":"Nomor penggantian biaya","prefix":"PG","separator":"\\/","period_format":"Y\\/m","padding":4,"id":57}	console	\N	\N	2026-09-07 13:33:13
629	\N	\N	updated	App\\Models\\ExpenseCategory	9	TRNS Transportasi dan perjalanan	\N	{"description":"Transportasi daring, taksi, dan tol. Dijumlahkan dari faktur rekanan, dan nanti juga dari penggantian biaya karyawan."}	{"description":"Transportasi daring, taksi, dan tol. Dijumlahkan dari faktur rekanan dan dari struk penggantian biaya karyawan yang sudah disetujui."}	console	\N	\N	2026-09-07 13:33:14
630	2	tester	created	App\\Models\\Reimbursement	1	PG/2026/09/0001 [DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026	\N	\N	{"title":"[DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026","employee_id":6,"department_id":5,"notes":"[DATA UJI] Pengajuan uji untuk memeriksa jalur lewat persetujuan atasan.","code":"PG\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:35:24
631	2	tester	created	App\\Models\\ReimbursementLine	1	Taksi kantor ke Samsat Jakarta Selatan Rp 185.000	\N	\N	{"expense_date":"2026-09-02 00:00:00","expense_category_id":9,"description":"Taksi kantor ke Samsat Jakarta Selatan","amount":185000,"file_path":null,"original_name":null,"reimbursement_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:35:55
632	2	tester	created	App\\Models\\ReimbursementLine	2	Parkir dan tol Rp 40.000	\N	\N	{"expense_date":"2026-09-02 00:00:00","expense_category_id":9,"description":"Parkir dan tol","amount":40000,"file_path":null,"original_name":null,"reimbursement_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:36:08
633	2	tester	created	App\\Models\\ReimbursementLine	3	Konsumsi rapat dengan petugas Rp 275.000	\N	\N	{"expense_date":"2026-09-03 00:00:00","expense_category_id":8,"description":"Konsumsi rapat dengan petugas","amount":275000,"file_path":null,"original_name":null,"reimbursement_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:36:21
634	2	tester	updated	App\\Models\\Reimbursement	1	PG/2026/09/0001 [DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026	\N	{"status":"draft","submitted_at":null,"approved_at":null,"approval_skipped_reason":null}	{"status":"diperiksa","submitted_at":"2026-09-07 13:36:56","approved_at":"2026-09-07 13:36:56","approval_skipped_reason":"Departemen yang dibebani belum punya kepala departemen"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:36:56
635	2	tester	created	App\\Models\\Budget	7	Finance Transportasi dan perjalanan 2026	\N	\N	{"department_id":5,"expense_category_id":9,"fiscal_year":2026,"amount":2000000,"notes":"[DATA UJI] Pagu transportasi Finance 2026.","created_by_user_id":2,"id":7}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:37:44
636	2	tester	updated	App\\Models\\Reimbursement	1	PG/2026/09/0001 [DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026	\N	{"status":"diperiksa","verified_by_user_id":null,"verified_at":null}	{"status":"disetujui","verified_by_user_id":2,"verified_at":"2026-09-07 13:38:25"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:38:25
637	2	tester	updated	App\\Models\\Department	5	FIN Finance	\N	{"head_employee_id":null}	{"head_employee_id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:40:23
638	2	tester	created	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	\N	{"title":"[DATA UJI] Konsumsi rapat vendor, September 2026","employee_id":6,"department_id":5,"notes":null,"code":"PG\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:41:04
639	1	Administrator	created	App\\Models\\Reimbursement	3	PG/2026/09/0003 Operasional pekerjaan	\N	\N	{"title":"Operasional pekerjaan","employee_id":8,"department_id":8,"notes":"untuk layanan operasional","code":"PG\\/2026\\/09\\/0003","created_by_user_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:41:21
640	2	tester	created	App\\Models\\ReimbursementLine	4	Makan siang rapat dengan rekanan Rp 420.000	\N	\N	{"expense_date":"2026-09-04 00:00:00","expense_category_id":8,"description":"Makan siang rapat dengan rekanan","amount":420000,"file_path":null,"original_name":null,"reimbursement_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:41:25
641	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"draft","submitted_at":null,"approver_employee_id":null}	{"status":"diajukan","submitted_at":"2026-09-07 13:41:40","approver_employee_id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:41:40
690	\N	\N	created	App\\Models\\NumberSequence	58	supply_request Nomor permintaan barang	\N	\N	{"code":"supply_request","name":"Nomor permintaan barang","prefix":"PB","separator":"\\/","period_format":"Y\\/m","padding":4,"id":58}	console	\N	\N	2026-09-08 05:48:10
642	1	Administrator	updated	App\\Models\\Reimbursement	3	PG/2026/09/0003 Operasional pekerjaan	\N	{"status":"draft"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:41:49
643	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"diajukan","approved_at":null,"approval_note":null}	{"status":"diperiksa","approved_at":"2026-09-07 13:42:01","approval_note":"Sudah saya cek, memang rapat dengan rekanan minggu lalu."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:42:01
644	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"diperiksa","rejection_reason":null,"rejected_stage":null}	{"status":"ditolak","rejection_reason":"Foto struknya belum ada. Mohon difoto dan diajukan ulang.","rejected_stage":"ga"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:42:23
645	1	Administrator	created	App\\Models\\Reimbursement	4	PG/2026/09/0004 Operasional pekerjaan	\N	\N	{"title":"Operasional pekerjaan","employee_id":8,"department_id":6,"notes":"easte","code":"PG\\/2026\\/09\\/0004","created_by_user_id":1,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:42:25
646	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"ditolak","submitted_at":"2026-09-07T13:41:40.000000Z","approved_at":"2026-09-07T13:42:01.000000Z","approval_note":"Sudah saya cek, memang rapat dengan rekanan minggu lalu."}	{"status":"draft","submitted_at":null,"approved_at":null,"approval_note":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:42:50
647	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"draft","submitted_at":null,"rejection_reason":"Foto struknya belum ada. Mohon difoto dan diajukan ulang.","rejected_stage":"ga"}	{"status":"diajukan","submitted_at":"2026-09-07 13:43:00","rejection_reason":null,"rejected_stage":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:43:00
648	2	tester	updated	App\\Models\\Reimbursement	1	PG/2026/09/0001 [DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026	\N	{"status":"disetujui","paid_date":null,"payment_reference":null,"paid_by_user_id":null}	{"status":"dibayar","paid_date":"2026-09-07 00:00:00","payment_reference":"TRF-BCA-20260907-402","paid_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:43:30
649	1	Administrator	created	App\\Models\\ReimbursementLine	5	operasional Rp 250.000	\N	\N	{"expense_date":"2026-09-07 00:00:00","expense_category_id":10,"description":"operasional","amount":250000,"file_path":"struk-penggantian\\/01M1Y1RKRMZT57W6BEA2QFVTSF.pdf","original_name":"Test dokumen 1.pdf","reimbursement_id":4,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:43:45
650	2	tester	updated	App\\Models\\Department	5	FIN Finance	\N	{"head_employee_id":5}	{"head_employee_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:44:08
651	1	Administrator	created	App\\Models\\ReimbursementLine	6	operasional Rp 125.000	\N	\N	{"expense_date":"2026-09-07 00:00:00","expense_category_id":6,"description":"operasional","amount":125000,"file_path":"struk-penggantian\\/01M1Y1T806DDVWXMN3TQS9C6MM.pdf","original_name":"Test dokumen 2.pdf","reimbursement_id":4,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:44:38
652	1	Administrator	updated	App\\Models\\Reimbursement	4	PG/2026/09/0004 Operasional pekerjaan	\N	{"status":"draft","submitted_at":null,"approved_at":null,"approval_skipped_reason":null}	{"status":"diperiksa","submitted_at":"2026-09-07 13:45:02","approved_at":"2026-09-07 13:45:02","approval_skipped_reason":"Departemen yang dibebani belum punya kepala departemen"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 13:45:02
653	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"diajukan","rejection_reason":null,"rejected_stage":null}	{"status":"ditolak","rejection_reason":"Mohon lampirkan foto struknya.","rejected_stage":"atasan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:45:05
654	2	tester	updated	App\\Models\\Reimbursement	2	PG/2026/09/0002 [DATA UJI] Konsumsi rapat vendor, September 2026	\N	{"status":"ditolak","submitted_at":"2026-09-07T13:43:00.000000Z"}	{"status":"draft","submitted_at":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-07 13:45:15
655	\N	\N	created	App\\Models\\Setting	17	Pemegang hak cipta aplikasi	\N	\N	{"key":"aplikasi.hak_cipta","group":"aplikasi","label":"Pemegang hak cipta aplikasi","description":"Muncul di kaki setiap halaman, didahului tahun berjalan. Dikosongkan berarti baris hak cipta tidak ditampilkan sama sekali.","type":"text","value":"PT. Gamatechno Indonesia","id":17}	console	\N	\N	2026-09-07 14:34:51
656	1	Administrator	created	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	\N	{"name":"SO awal bulan Sep 2026","notes":"SO keseluruhan","scope_location_id":4,"scope_department_id":null,"scope_asset_category_id":null,"code":"SO\\/2026\\/09\\/0004","id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:34:22
657	1	Administrator	updated	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	{"scope_location_id":4}	{"scope_location_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:35:14
658	1	Administrator	updated	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	{"scope_location_id":1}	{"scope_location_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:35:36
659	1	Administrator	updated	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	{"scope_location_id":2}	{"scope_location_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:36:18
660	1	Administrator	updated	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	{"status":"draft","started_at":null,"started_by_user_id":null}	{"status":"berjalan","started_at":"2026-09-07 16:36:54","started_by_user_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:36:54
661	1	Administrator	updated	App\\Models\\StockOpname	4	SO/2026/09/0004 SO awal bulan Sep 2026	\N	{"status":"berjalan","finished_at":null,"finished_by_user_id":null}	{"status":"selesai","finished_at":"2026-09-07 16:38:37","finished_by_user_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-07 16:38:37
662	\N	\N	updated	App\\Models\\Module	1	Roles	\N	{"name":"Role","group":"Pengaturan Akses"}	{"name":"Roles","group":"Access Control"}	console	\N	\N	2026-09-08 05:48:04
663	\N	\N	updated	App\\Models\\Module	2	Users	\N	{"name":"Pengguna","group":"Pengaturan Akses"}	{"name":"Users","group":"Access Control"}	console	\N	\N	2026-09-08 05:48:04
664	\N	\N	updated	App\\Models\\Module	3	Modules	\N	{"name":"Modul","group":"Pengaturan Akses"}	{"name":"Modules","group":"Access Control"}	console	\N	\N	2026-09-08 05:48:04
665	\N	\N	updated	App\\Models\\Module	9	Asset Categories	\N	{"name":"Kategori aset","group":"Aset"}	{"name":"Asset Categories","group":"Assets"}	console	\N	\N	2026-09-08 05:48:04
666	\N	\N	updated	App\\Models\\Module	10	Assets	\N	{"name":"Daftar aset","group":"Aset"}	{"name":"Assets","group":"Assets"}	console	\N	\N	2026-09-08 05:48:05
667	\N	\N	updated	App\\Models\\Module	11	Stock Opname	\N	{"name":"Stock opname","group":"Aset"}	{"name":"Stock Opname","group":"Assets"}	console	\N	\N	2026-09-08 05:48:05
668	\N	\N	updated	App\\Models\\Module	14	Asset Transfers	\N	{"name":"Serah terima aset","group":"Aset"}	{"name":"Asset Transfers","group":"Assets"}	console	\N	\N	2026-09-08 05:48:05
669	\N	\N	updated	App\\Models\\Module	15	Asset Disposals	\N	{"name":"Pelepasan aset","group":"Aset"}	{"name":"Asset Disposals","group":"Assets"}	console	\N	\N	2026-09-08 05:48:05
670	\N	\N	updated	App\\Models\\Module	12	Supply Items	\N	{"name":"Barang habis pakai","group":"Persediaan"}	{"name":"Supply Items","group":"Office Supplies"}	console	\N	\N	2026-09-08 05:48:05
671	\N	\N	updated	App\\Models\\Module	13	Supply Movements	\N	{"name":"Mutasi barang","group":"Persediaan"}	{"name":"Supply Movements","group":"Office Supplies"}	console	\N	\N	2026-09-08 05:48:05
672	\N	\N	created	App\\Models\\Module	28	Supply Requests	\N	\N	{"code":"supply_requests","name":"Supply Requests","description":"Permintaan pemakaian ATK oleh karyawan, persetujuan atasan, dan penyerahan barang oleh tim GA. Penyerahannya yang melahirkan mutasi barang keluar.","group":"Office Supplies","icon":"heroicon-o-clipboard-document-list","sort":30,"available_actions":"[\\"read\\",\\"read_all\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"issue\\",\\"request_for_others\\"]","is_active":true,"id":28}	console	\N	\N	2026-09-08 05:48:05
673	\N	\N	updated	App\\Models\\Module	4	Departments	\N	{"name":"Departemen","group":"Data Induk"}	{"name":"Departments","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:05
674	\N	\N	updated	App\\Models\\Module	5	Locations	\N	{"name":"Lokasi","group":"Data Induk"}	{"name":"Locations","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:05
675	\N	\N	updated	App\\Models\\Module	6	Employees	\N	{"name":"Karyawan","group":"Data Induk"}	{"name":"Employees","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:05
676	\N	\N	updated	App\\Models\\Module	16	Depreciation	\N	{"name":"Penyusutan aset","group":"Aset"}	{"name":"Depreciation","group":"Assets"}	console	\N	\N	2026-09-08 05:48:05
677	\N	\N	updated	App\\Models\\Module	17	Vendors	\N	{"name":"Rekanan","group":"Data Induk"}	{"name":"Vendors","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:05
678	\N	\N	updated	App\\Models\\Module	20	Service Requests	\N	{"name":"Permintaan perbaikan","group":"Pemeliharaan"}	{"name":"Service Requests","group":"Maintenance"}	console	\N	\N	2026-09-08 05:48:05
679	\N	\N	updated	App\\Models\\Module	21	Request Types	\N	{"name":"Jenis permintaan","group":"Data Induk"}	{"name":"Request Types","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:05
680	\N	\N	updated	App\\Models\\Module	18	Maintenance Schedules	\N	{"name":"Jadwal pemeliharaan","group":"Pemeliharaan"}	{"name":"Maintenance Schedules","group":"Maintenance"}	console	\N	\N	2026-09-08 05:48:05
681	\N	\N	updated	App\\Models\\Module	19	Work Orders	\N	{"name":"Perintah kerja","group":"Pemeliharaan"}	{"name":"Work Orders","group":"Maintenance"}	console	\N	\N	2026-09-08 05:48:05
682	\N	\N	updated	App\\Models\\Module	22	Vehicles	\N	{"name":"Kendaraan dinas","group":"Kendaraan"}	{"name":"Vehicles","group":"Vehicles"}	console	\N	\N	2026-09-08 05:48:06
683	\N	\N	updated	App\\Models\\Module	23	Vehicle Bookings	\N	{"name":"Pemesanan kendaraan","group":"Kendaraan"}	{"name":"Vehicle Bookings","group":"Vehicles"}	console	\N	\N	2026-09-08 05:48:06
684	\N	\N	updated	App\\Models\\Module	24	Expense Categories	\N	{"name":"Kategori biaya","group":"Data Induk"}	{"name":"Expense Categories","group":"Master Data"}	console	\N	\N	2026-09-08 05:48:06
685	\N	\N	updated	App\\Models\\Module	25	Budgets	\N	{"name":"Anggaran dan realisasi","group":"Anggaran"}	{"name":"Budgets","group":"Budget & Expenses"}	console	\N	\N	2026-09-08 05:48:06
686	\N	\N	updated	App\\Models\\Module	26	Vendor Bills	\N	{"name":"Tagihan rekanan","group":"Anggaran"}	{"name":"Vendor Bills","group":"Budget & Expenses"}	console	\N	\N	2026-09-08 05:48:06
687	\N	\N	updated	App\\Models\\Module	27	Reimbursements	\N	{"name":"Penggantian biaya","group":"Anggaran"}	{"name":"Reimbursements","group":"Budget & Expenses"}	console	\N	\N	2026-09-08 05:48:06
688	\N	\N	updated	App\\Models\\Module	7	Settings	\N	{"name":"Pengaturan","group":"Sistem"}	{"name":"Settings","group":"System"}	console	\N	\N	2026-09-08 05:48:06
691	1	Administrator	login	App\\Models\\User	1	Administrator	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 05:50:04
692	2	tester	login	App\\Models\\User	2	tester	\N	\N	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:53:07
693	2	tester	created	App\\Models\\SupplyRequest	1	PB/2026/09/0001 Perlengkapan tulis dan pantry rapat tutup buku September	\N	\N	{"purpose":"Perlengkapan tulis dan pantry rapat tutup buku September","employee_id":5,"department_id":5,"needed_date":"2026-09-11 00:00:00","notes":null,"code":"PB\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:56:00
694	2	tester	created	App\\Models\\SupplyRequestLine	1	PB/2026/09/0001 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"quantity_requested":5,"notes":"Untuk cetak berkas rapat","supply_request_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:57:27
695	2	tester	created	App\\Models\\SupplyRequestLine	2	PB/2026/09/0001 Kertas struk kasir	\N	\N	{"supply_item_id":4,"quantity_requested":4,"notes":null,"supply_request_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:57:47
696	2	tester	created	App\\Models\\SupplyRequestLine	3	PB/2026/09/0001 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"quantity_requested":6,"notes":"Untuk papan tulis ruang rapat","supply_request_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:58:06
697	2	tester	updated	App\\Models\\SupplyRequest	1	PB/2026/09/0001 Perlengkapan tulis dan pantry rapat tutup buku September	\N	{"status":"draft","submitted_at":null,"approved_at":null,"approval_skipped_reason":null}	{"status":"disetujui","submitted_at":"2026-09-08 05:59:00","approved_at":"2026-09-08 05:59:00","approval_skipped_reason":"Departemen yang dibebani belum punya kepala departemen"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 05:59:00
698	2	tester	updated	App\\Models\\SupplyRequestLine	3	PB/2026/09/0001 Spidol papan tulis hitam	\N	{"quantity_issued":null}	{"quantity_issued":0}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:01:50
699	2	tester	created	App\\Models\\SupplyTransaction	158	MP/2026/09/0158 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"keluar","quantity":-5,"transaction_date":"2026-09-08 00:00:00","department_id":5,"employee_id":5,"reference":"PB\\/2026\\/09\\/0001","notes":"Penyerahan permintaan PB\\/2026\\/09\\/0001. Perlengkapan tulis dan pantry rapat tutup buku September","code":"MP\\/2026\\/09\\/0158","created_by_user_id":2,"id":158}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:02:15
700	2	tester	updated	App\\Models\\SupplyRequestLine	1	PB/2026/09/0001 Kertas HVS A4 80 gram	\N	{"quantity_issued":null,"supply_transaction_id":null}	{"quantity_issued":5,"supply_transaction_id":158}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:02:15
701	2	tester	created	App\\Models\\SupplyTransaction	159	MP/2026/09/0159 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"keluar","quantity":-4,"transaction_date":"2026-09-08 00:00:00","department_id":5,"employee_id":5,"reference":"PB\\/2026\\/09\\/0001","notes":"Penyerahan permintaan PB\\/2026\\/09\\/0001. Perlengkapan tulis dan pantry rapat tutup buku September","code":"MP\\/2026\\/09\\/0159","created_by_user_id":2,"id":159}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:02:15
702	2	tester	updated	App\\Models\\SupplyRequestLine	2	PB/2026/09/0001 Kertas struk kasir	\N	{"quantity_issued":null,"supply_transaction_id":null}	{"quantity_issued":4,"supply_transaction_id":159}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:02:15
703	2	tester	updated	App\\Models\\SupplyRequest	1	PB/2026/09/0001 Perlengkapan tulis dan pantry rapat tutup buku September	\N	{"status":"disetujui","issued_by_user_id":null,"issued_at":null,"issue_note":null}	{"status":"diserahkan","issued_by_user_id":2,"issued_at":"2026-09-08 06:02:15","issue_note":"Spidol kosong, diserahkan menyusul setelah pembelian"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:02:15
704	2	tester	updated	App\\Models\\Department	5	FIN Finance	\N	{"head_employee_id":null}	{"head_employee_id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:07:42
705	2	tester	created	App\\Models\\Budget	8	Finance Alat tulis dan barang habis pakai 2026	\N	\N	{"department_id":5,"expense_category_id":4,"fiscal_year":2026,"amount":5000000,"notes":"[DATA UJI] pagu sementara untuk verifikasi kiriman M, dihapus setelah selesai","created_by_user_id":2,"id":8}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:09:07
706	2	tester	created	App\\Models\\SupplyRequest	2	PB/2026/09/0002 Kertas cetak laporan keuangan kuartal tiga	\N	\N	{"purpose":"Kertas cetak laporan keuangan kuartal tiga","employee_id":5,"department_id":5,"needed_date":null,"notes":null,"code":"PB\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:09:48
707	2	tester	created	App\\Models\\SupplyRequestLine	4	PB/2026/09/0002 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"quantity_requested":3,"notes":null,"supply_request_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:10:21
708	2	tester	updated	App\\Models\\SupplyRequest	2	PB/2026/09/0002 Kertas cetak laporan keuangan kuartal tiga	\N	{"status":"draft","submitted_at":null,"approver_employee_id":null}	{"status":"diajukan","submitted_at":"2026-09-08 06:10:37","approver_employee_id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:10:37
709	2	tester	updated	App\\Models\\SupplyRequest	2	PB/2026/09/0002 Kertas cetak laporan keuangan kuartal tiga	\N	{"status":"diajukan","approved_at":null,"approval_note":null}	{"status":"disetujui","approved_at":"2026-09-08 06:11:08","approval_note":"Setuju, kertas memang habis di ruang keuangan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:11:08
710	2	tester	created	App\\Models\\SupplyTransaction	160	MP/2026/09/0160 Kertas HVS A4 80 gram	\N	\N	{"supply_item_id":1,"type":"keluar","quantity":-3,"transaction_date":"2026-09-08 00:00:00","department_id":5,"employee_id":5,"reference":"PB\\/2026\\/09\\/0002","notes":"Penyerahan permintaan PB\\/2026\\/09\\/0002. Kertas cetak laporan keuangan kuartal tiga","code":"MP\\/2026\\/09\\/0160","created_by_user_id":2,"id":160}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:11:32
711	2	tester	updated	App\\Models\\SupplyRequestLine	4	PB/2026/09/0002 Kertas HVS A4 80 gram	\N	{"quantity_issued":null,"supply_transaction_id":null}	{"quantity_issued":3,"supply_transaction_id":160}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:11:32
712	2	tester	updated	App\\Models\\SupplyRequest	2	PB/2026/09/0002 Kertas cetak laporan keuangan kuartal tiga	\N	{"status":"disetujui","issued_by_user_id":null,"issued_at":null}	{"status":"diserahkan","issued_by_user_id":2,"issued_at":"2026-09-08 06:11:32"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:11:32
713	2	tester	created	App\\Models\\SupplyRequest	3	PB/2026/09/0003 Tinta printer dan spidol untuk ruang keuangan	\N	\N	{"purpose":"Tinta printer dan spidol untuk ruang keuangan","employee_id":5,"department_id":5,"needed_date":null,"notes":null,"code":"PB\\/2026\\/09\\/0003","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:12:47
714	2	tester	created	App\\Models\\SupplyRequestLine	5	PB/2026/09/0003 Tinta printer warna	\N	\N	{"supply_item_id":15,"quantity_requested":2,"notes":null,"supply_request_id":3,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:13:14
715	2	tester	created	App\\Models\\SupplyRequestLine	6	PB/2026/09/0003 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"quantity_requested":4,"notes":null,"supply_request_id":3,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:13:53
716	2	tester	updated	App\\Models\\SupplyRequest	3	PB/2026/09/0003 Tinta printer dan spidol untuk ruang keuangan	\N	{"status":"draft","submitted_at":null,"approver_employee_id":null}	{"status":"diajukan","submitted_at":"2026-09-08 06:14:07","approver_employee_id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:14:07
717	2	tester	updated	App\\Models\\SupplyRequest	3	PB/2026/09/0003 Tinta printer dan spidol untuk ruang keuangan	\N	{"status":"diajukan","approved_at":null}	{"status":"disetujui","approved_at":"2026-09-08 06:14:55"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:14:55
718	2	tester	updated	App\\Models\\SupplyRequest	3	PB/2026/09/0003 Tinta printer dan spidol untuk ruang keuangan	\N	{"status":"disetujui"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:16:14
719	2	tester	deleted	App\\Models\\Budget	8	Finance Alat tulis dan barang habis pakai 2026	\N	{"id":8,"department_id":5,"expense_category_id":4,"fiscal_year":2026,"amount":"5000000.00","notes":"[DATA UJI] pagu sementara untuk verifikasi kiriman M, dihapus setelah selesai","created_by_user_id":2}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:16:56
720	2	tester	updated	App\\Models\\Department	5	FIN Finance	\N	{"head_employee_id":6}	{"head_employee_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 06:17:30
721	\N	\N	created	App\\Models\\Module	29	Supply Purchases	\N	\N	{"code":"supply_purchases","name":"Supply Purchases","description":"Pesanan pembelian ATK ke pemasok dan pembelian langsung, beserta penerimaan barangnya. Penerimaannya yang melahirkan mutasi barang masuk.","group":"Office Supplies","icon":"heroicon-o-shopping-cart","sort":40,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"receive\\"]","is_active":true,"id":29}	console	\N	\N	2026-09-08 10:02:59
722	\N	\N	created	App\\Models\\NumberSequence	59	supply_purchase Nomor pesanan pembelian barang	\N	\N	{"code":"supply_purchase","name":"Nomor pesanan pembelian barang","prefix":"PP","separator":"\\/","period_format":"Y\\/m","padding":4,"id":59}	console	\N	\N	2026-09-08 10:03:00
723	\N	\N	created	App\\Models\\NumberSequence	60	supply_receipt Nomor penerimaan barang	\N	\N	{"code":"supply_receipt","name":"Nomor penerimaan barang","prefix":"PN","separator":"\\/","period_format":"Y\\/m","padding":4,"id":60}	console	\N	\N	2026-09-08 10:03:00
724	2	tester	created	App\\Models\\SupplyPurchase	1	PP/2026/09/0001 Pengadaan ATK yang stoknya habis, September	\N	\N	{"kind":"pesanan","description":"Pengadaan ATK yang stoknya habis, September","vendor_id":2,"order_date":"2026-09-08 00:00:00","expected_date":"2026-09-15 00:00:00","notes":null,"supplier_name":null,"code":"PP\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:04:46
725	2	tester	created	App\\Models\\SupplyPurchaseLine	1	PP/2026/09/0001 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"quantity_ordered":12,"unit_price":8500,"notes":null,"supply_purchase_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:05:18
726	2	tester	created	App\\Models\\SupplyPurchaseLine	2	PP/2026/09/0001 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"quantity_ordered":10,"unit_price":22000,"notes":null,"supply_purchase_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:05:41
727	2	tester	created	App\\Models\\SupplyPurchaseLine	3	PP/2026/09/0001 Kertas struk kasir	\N	\N	{"supply_item_id":4,"quantity_ordered":12,"unit_price":6500,"notes":null,"supply_purchase_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:05:55
728	2	tester	updated	App\\Models\\SupplyPurchase	1	PP/2026/09/0001 Pengadaan ATK yang stoknya habis, September	\N	{"status":"draft","submitted_at":null}	{"status":"diajukan","submitted_at":"2026-09-08 10:06:19"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:06:19
729	2	tester	updated	App\\Models\\SupplyPurchase	1	PP/2026/09/0001 Pengadaan ATK yang stoknya habis, September	\N	{"status":"diajukan","approved_by_user_id":null,"approved_at":null,"approval_note":null}	{"status":"disetujui","approved_by_user_id":2,"approved_at":"2026-09-08 10:06:46","approval_note":"Setuju, harga sesuai penawaran"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:06:46
730	2	tester	created	App\\Models\\SupplyReceipt	1	PN/2026/09/0001 atas PP/2026/09/0001	\N	\N	{"receipt_date":"2026-09-08 00:00:00","delivery_note_number":"SJ-4471","notes":"Teh baru datang 4 box, sisanya menyusul minggu depan","received_by_user_id":2,"supply_purchase_id":1,"code":"PN\\/2026\\/09\\/0001","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
731	2	tester	created	App\\Models\\SupplyTransaction	161	MP/2026/09/0161 Kertas struk kasir	\N	\N	{"supply_item_id":4,"type":"masuk","quantity":12,"unit_price":"6500.00","transaction_date":"2026-09-08 00:00:00","supplier":"PT. Abadi Jaya","reference":"PN\\/2026\\/09\\/0001","notes":"Penerimaan PN\\/2026\\/09\\/0001 atas pembelian PP\\/2026\\/09\\/0001. Pengadaan ATK yang stoknya habis, September","code":"MP\\/2026\\/09\\/0161","created_by_user_id":2,"id":161}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
732	2	tester	created	App\\Models\\SupplyReceiptLine	1	PN/2026/09/0001 Kertas struk kasir	\N	\N	{"supply_purchase_line_id":3,"quantity":12,"supply_transaction_id":161,"supply_receipt_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
733	2	tester	created	App\\Models\\SupplyTransaction	162	MP/2026/09/0162 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"masuk","quantity":12,"unit_price":"8500.00","transaction_date":"2026-09-08 00:00:00","supplier":"PT. Abadi Jaya","reference":"PN\\/2026\\/09\\/0001","notes":"Penerimaan PN\\/2026\\/09\\/0001 atas pembelian PP\\/2026\\/09\\/0001. Pengadaan ATK yang stoknya habis, September","code":"MP\\/2026\\/09\\/0162","created_by_user_id":2,"id":162}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
734	2	tester	created	App\\Models\\SupplyReceiptLine	2	PN/2026/09/0001 Spidol papan tulis hitam	\N	\N	{"supply_purchase_line_id":1,"quantity":12,"supply_transaction_id":162,"supply_receipt_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
735	2	tester	created	App\\Models\\SupplyTransaction	163	MP/2026/09/0163 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"masuk","quantity":4,"unit_price":"22000.00","transaction_date":"2026-09-08 00:00:00","supplier":"PT. Abadi Jaya","reference":"PN\\/2026\\/09\\/0001","notes":"Penerimaan PN\\/2026\\/09\\/0001 atas pembelian PP\\/2026\\/09\\/0001. Pengadaan ATK yang stoknya habis, September","code":"MP\\/2026\\/09\\/0163","created_by_user_id":2,"id":163}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
736	2	tester	created	App\\Models\\SupplyReceiptLine	3	PN/2026/09/0001 Teh celup kotak isi 25	\N	\N	{"supply_purchase_line_id":2,"quantity":4,"supply_transaction_id":163,"supply_receipt_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
737	2	tester	updated	App\\Models\\SupplyPurchase	1	PP/2026/09/0001 Pengadaan ATK yang stoknya habis, September	\N	{"status":"disetujui"}	{"status":"diterima_sebagian"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:09:58
770	2	tester	updated	App\\Models\\SupplyOpnameLine	1	OP/2026/09/0001 Isi stapler nomor 10	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":38,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:30:14
738	2	tester	created	App\\Models\\SupplyPurchase	2	PP/2026/09/0002 Belanja mendadak barang kebersihan dan tinta printer	\N	\N	{"kind":"langsung","description":"Belanja mendadak barang kebersihan dan tinta printer","supplier_name":"Toko Sinar Jaya, Jalan Kaliurang","order_date":"2026-09-08 00:00:00","notes":null,"vendor_id":null,"expected_date":null,"code":"PP\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:11:41
739	2	tester	created	App\\Models\\SupplyPurchaseLine	4	PP/2026/09/0002 Pembersih lantai	\N	\N	{"supply_item_id":19,"quantity_ordered":6,"unit_price":18000,"notes":null,"supply_purchase_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:06
740	2	tester	created	App\\Models\\SupplyPurchaseLine	5	PP/2026/09/0002 Tinta printer warna	\N	\N	{"supply_item_id":15,"quantity_ordered":4,"unit_price":95000,"notes":null,"supply_purchase_id":2,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:29
741	2	tester	updated	App\\Models\\SupplyPurchase	2	PP/2026/09/0002 Belanja mendadak barang kebersihan dan tinta printer	\N	{"status":"draft","submitted_at":null,"approved_at":null,"approval_skipped_reason":null}	{"status":"disetujui","submitted_at":"2026-09-08 10:12:46","approved_at":"2026-09-08 10:12:46","approval_skipped_reason":"Pembelian langsung, barangnya sudah di tangan saat dicatat"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
742	2	tester	created	App\\Models\\SupplyReceipt	2	PN/2026/09/0002 atas PP/2026/09/0002	\N	\N	{"receipt_date":"2026-09-08 00:00:00","delivery_note_number":"NOTA-88123","notes":null,"received_by_user_id":2,"supply_purchase_id":2,"code":"PN\\/2026\\/09\\/0002","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
743	2	tester	created	App\\Models\\SupplyTransaction	164	MP/2026/09/0164 Tinta printer warna	\N	\N	{"supply_item_id":15,"type":"masuk","quantity":4,"unit_price":"95000.00","transaction_date":"2026-09-08 00:00:00","supplier":"Toko Sinar Jaya, Jalan Kaliurang","reference":"PN\\/2026\\/09\\/0002","notes":"Penerimaan PN\\/2026\\/09\\/0002 atas pembelian PP\\/2026\\/09\\/0002. Belanja mendadak barang kebersihan dan tinta printer","code":"MP\\/2026\\/09\\/0164","created_by_user_id":2,"id":164}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
744	2	tester	created	App\\Models\\SupplyReceiptLine	4	PN/2026/09/0002 Tinta printer warna	\N	\N	{"supply_purchase_line_id":5,"quantity":4,"supply_transaction_id":164,"supply_receipt_id":2,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
745	2	tester	created	App\\Models\\SupplyTransaction	165	MP/2026/09/0165 Pembersih lantai	\N	\N	{"supply_item_id":19,"type":"masuk","quantity":6,"unit_price":"18000.00","transaction_date":"2026-09-08 00:00:00","supplier":"Toko Sinar Jaya, Jalan Kaliurang","reference":"PN\\/2026\\/09\\/0002","notes":"Penerimaan PN\\/2026\\/09\\/0002 atas pembelian PP\\/2026\\/09\\/0002. Belanja mendadak barang kebersihan dan tinta printer","code":"MP\\/2026\\/09\\/0165","created_by_user_id":2,"id":165}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
746	2	tester	created	App\\Models\\SupplyReceiptLine	5	PN/2026/09/0002 Pembersih lantai	\N	\N	{"supply_purchase_line_id":4,"quantity":6,"supply_transaction_id":165,"supply_receipt_id":2,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
747	2	tester	updated	App\\Models\\SupplyPurchase	2	PP/2026/09/0002 Belanja mendadak barang kebersihan dan tinta printer	\N	{"status":"disetujui"}	{"status":"selesai"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:12:46
748	2	tester	created	App\\Models\\VendorBill	3	TG/2026/09/0003 PT. Abadi Jaya	\N	\N	{"vendor_id":2,"supply_purchase_id":1,"invoice_number":"INV-2026-4471","invoice_date":"2026-09-08 00:00:00","due_date":null,"description":"Faktur pengadaan ATK September, seluruh pesanan ditagih di muka","file_path":null,"notes":null,"original_name":null,"code":"TG\\/2026\\/09\\/0003","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:14:06
749	2	tester	created	App\\Models\\VendorBillLine	5	Biaya GA lainnya Rp 400.000	\N	\N	{"expense_category_id":10,"department_id":4,"amount":400000,"description":"Pengadaan ATK September sesuai PP\\/2026\\/09\\/0001","vendor_bill_id":3,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:15:36
750	2	tester	updated	App\\Models\\VendorBillLine	5	Biaya GA lainnya Rp 268.000	\N	{"amount":"400000.00"}	{"amount":268000}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:18:21
751	2	tester	updated	App\\Models\\VendorBillLine	5	Biaya GA lainnya Rp 400.000	\N	{"amount":"268000.00"}	{"amount":400000}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:20:10
752	2	tester	created	App\\Models\\SupplyReceipt	3	PN/2026/09/0003 atas PP/2026/09/0001	\N	\N	{"receipt_date":"2026-09-08 00:00:00","delivery_note_number":"SJ-4488","notes":null,"received_by_user_id":2,"supply_purchase_id":1,"code":"PN\\/2026\\/09\\/0003","id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:20:59
753	2	tester	created	App\\Models\\SupplyTransaction	166	MP/2026/09/0166 Teh celup kotak isi 25	\N	\N	{"supply_item_id":23,"type":"masuk","quantity":6,"unit_price":"22000.00","transaction_date":"2026-09-08 00:00:00","supplier":"PT. Abadi Jaya","reference":"PN\\/2026\\/09\\/0003","notes":"Penerimaan PN\\/2026\\/09\\/0003 atas pembelian PP\\/2026\\/09\\/0001. Pengadaan ATK yang stoknya habis, September","code":"MP\\/2026\\/09\\/0166","created_by_user_id":2,"id":166}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:20:59
754	2	tester	created	App\\Models\\SupplyReceiptLine	6	PN/2026/09/0003 Teh celup kotak isi 25	\N	\N	{"supply_purchase_line_id":2,"quantity":6,"supply_transaction_id":166,"supply_receipt_id":3,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:20:59
755	2	tester	updated	App\\Models\\SupplyPurchase	1	PP/2026/09/0001 Pengadaan ATK yang stoknya habis, September	\N	{"status":"diterima_sebagian"}	{"status":"selesai"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:20:59
756	2	tester	updated	App\\Models\\VendorBillLine	5	Biaya GA lainnya Rp 450.000	\N	{"amount":"400000.00"}	{"amount":450000}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:21:31
757	2	tester	updated	App\\Models\\VendorBillLine	5	Biaya GA lainnya Rp 400.000	\N	{"amount":"450000.00"}	{"amount":400000}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:21:55
758	2	tester	updated	App\\Models\\VendorBill	3	TG/2026/09/0003 PT. Abadi Jaya	\N	{"status":"draft"}	{"status":"diajukan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:22:04
759	2	tester	updated	App\\Models\\VendorBill	3	TG/2026/09/0003 PT. Abadi Jaya	\N	{"status":"diajukan"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:22:48
760	2	tester	created	App\\Models\\SupplyPurchase	3	PP/2026/09/0003 Pesanan uji penutupan, sisa dibatalkan rekanan	\N	\N	{"kind":"pesanan","description":"Pesanan uji penutupan, sisa dibatalkan rekanan","vendor_id":2,"order_date":"2026-09-08 00:00:00","expected_date":null,"notes":null,"supplier_name":null,"code":"PP\\/2026\\/09\\/0003","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:23:16
761	2	tester	created	App\\Models\\SupplyPurchaseLine	6	PP/2026/09/0003 Ordner arsip folio	\N	\N	{"supply_item_id":12,"quantity_ordered":5,"unit_price":35000,"notes":null,"supply_purchase_id":3,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:23:42
762	2	tester	updated	App\\Models\\SupplyPurchase	3	PP/2026/09/0003 Pesanan uji penutupan, sisa dibatalkan rekanan	\N	{"status":"draft","submitted_at":null}	{"status":"diajukan","submitted_at":"2026-09-08 10:23:58"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:23:58
763	2	tester	updated	App\\Models\\SupplyPurchase	3	PP/2026/09/0003 Pesanan uji penutupan, sisa dibatalkan rekanan	\N	{"status":"diajukan","approved_by_user_id":null,"approved_at":null}	{"status":"disetujui","approved_by_user_id":2,"approved_at":"2026-09-08 10:24:14"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:24:14
764	2	tester	updated	App\\Models\\SupplyPurchase	3	PP/2026/09/0003 Pesanan uji penutupan, sisa dibatalkan rekanan	\N	{"status":"disetujui","closed_at":null,"closing_reason":null}	{"status":"selesai","closed_at":"2026-09-08 10:24:40","closing_reason":"Rekanan membatalkan, ordner kosong sampai akhir tahun"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 10:24:40
765	\N	\N	created	App\\Models\\Module	30	Supply Opname	\N	\N	{"code":"supply_opnames","name":"Supply Opname","description":"Penghitungan fisik barang habis pakai dan penyesuaian stok dari hasilnya. Tiap selisih lahir sebagai mutasi koreksi, bukan mengubah angka diam diam.","group":"Office Supplies","icon":"heroicon-o-clipboard-document-check","sort":50,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\",\\"adjust\\"]","is_active":true,"id":30}	console	\N	\N	2026-09-08 11:27:33
766	\N	\N	updated	App\\Models\\NumberSequence	58	supply_request Nomor permintaan barang	\N	{"prefix":"PB"}	{"prefix":"PM"}	console	\N	\N	2026-09-08 11:27:34
767	\N	\N	created	App\\Models\\NumberSequence	61	supply_opname Nomor opname barang habis pakai	\N	\N	{"code":"supply_opname","name":"Nomor opname barang habis pakai","prefix":"OP","separator":"\\/","period_format":"Y\\/m","padding":4,"id":61}	console	\N	\N	2026-09-08 11:27:34
768	2	tester	created	App\\Models\\SupplyOpname	1	OP/2026/09/0001 Opname alat tulis akhir September 2026	\N	\N	{"name":"Opname alat tulis akhir September 2026","scope_category":"alat_tulis","scope_location_id":null,"notes":null,"code":"OP\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:28:41
769	2	tester	updated	App\\Models\\SupplyOpname	1	OP/2026/09/0001 Opname alat tulis akhir September 2026	\N	{"status":"draft","started_at":null}	{"status":"berjalan","started_at":"2026-09-08 11:29:45"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:29:45
771	2	tester	updated	App\\Models\\SupplyOpnameLine	2	OP/2026/09/0001 Lakban bening 2 inci	\N	{"counted_quantity":null,"checked":false,"notes":null}	{"counted_quantity":34,"checked":true,"notes":"Dua roll terpakai untuk kemasan kiriman, tidak sempat dicatat"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:30:33
772	2	tester	updated	App\\Models\\SupplyOpnameLine	3	OP/2026/09/0001 Map plastik berkancing	\N	{"counted_quantity":null,"checked":false,"notes":null}	{"counted_quantity":103,"checked":true,"notes":"Tiga pcs ketemu di laci meja resepsionis"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:30:44
773	2	tester	updated	App\\Models\\SupplyOpnameLine	4	OP/2026/09/0001 Ordner arsip folio	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":24,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:30:53
774	2	tester	updated	App\\Models\\SupplyOpnameLine	6	OP/2026/09/0001 Pulpen tinta biru 0,5 mm	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":180,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:31:10
775	2	tester	updated	App\\Models\\SupplyOpnameLine	7	OP/2026/09/0001 Pulpen tinta hitam 0,5 mm	\N	{"counted_quantity":null,"checked":false,"notes":null}	{"counted_quantity":28,"checked":true,"notes":"Dua batang kering, dibuang"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:31:21
776	2	tester	updated	App\\Models\\SupplyOpnameLine	8	OP/2026/09/0001 Spidol papan tulis hitam	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":12,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:31:30
777	2	tester	updated	App\\Models\\SupplyOpnameLine	9	OP/2026/09/0001 Stapler ukuran sedang	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":14,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:31:39
778	2	tester	created	App\\Models\\SupplyTransaction	167	MP/2026/09/0167 Spidol papan tulis hitam	\N	\N	{"supply_item_id":8,"type":"koreksi_kurang","quantity":-4,"transaction_date":"2026-09-08 00:00:00","notes":"Empat spidol kering dan dibuang setelah lembar hitungan opname diisi","code":"MP\\/2026\\/09\\/0167","created_by_user_id":2,"id":167}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:32:50
779	2	tester	updated	App\\Models\\SupplyOpnameLine	8	OP/2026/09/0001 Spidol papan tulis hitam	\N	{"counted_quantity":12,"notes":null}	{"counted_quantity":8,"notes":"Dihitung ulang setelah empat spidol kering dibuang"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:01
780	2	tester	updated	App\\Models\\SupplyOpname	1	OP/2026/09/0001 Opname alat tulis akhir September 2026	\N	{"status":"berjalan","finished_at":null}	{"status":"selesai","finished_at":"2026-09-08 11:34:23"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:23
781	2	tester	created	App\\Models\\SupplyTransaction	168	MP/2026/09/0168 Lakban bening 2 inci	\N	\N	{"supply_item_id":13,"type":"koreksi_kurang","quantity":-2,"transaction_date":"2026-09-08 00:00:00","reference":"OP\\/2026\\/09\\/0001","notes":"Hasil opname OP\\/2026\\/09\\/0001. Hitungan fisik 34 roll, catatan 36 roll. Dua roll terpakai untuk kemasan kiriman, tidak sempat dicatat","code":"MP\\/2026\\/09\\/0168","created_by_user_id":2,"id":168}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
782	2	tester	updated	App\\Models\\SupplyOpnameLine	2	OP/2026/09/0001 Lakban bening 2 inci	\N	{"supply_transaction_id":null}	{"supply_transaction_id":168}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
783	2	tester	created	App\\Models\\SupplyTransaction	169	MP/2026/09/0169 Map plastik berkancing	\N	\N	{"supply_item_id":11,"type":"koreksi_tambah","quantity":3,"transaction_date":"2026-09-08 00:00:00","reference":"OP\\/2026\\/09\\/0001","notes":"Hasil opname OP\\/2026\\/09\\/0001. Hitungan fisik 103 pcs, catatan 100 pcs. Tiga pcs ketemu di laci meja resepsionis","code":"MP\\/2026\\/09\\/0169","created_by_user_id":2,"id":169}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
784	2	tester	updated	App\\Models\\SupplyOpnameLine	3	OP/2026/09/0001 Map plastik berkancing	\N	{"supply_transaction_id":null}	{"supply_transaction_id":169}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
785	2	tester	created	App\\Models\\SupplyTransaction	170	MP/2026/09/0170 Pulpen tinta hitam 0,5 mm	\N	\N	{"supply_item_id":6,"type":"koreksi_kurang","quantity":-2,"transaction_date":"2026-09-08 00:00:00","reference":"OP\\/2026\\/09\\/0001","notes":"Hasil opname OP\\/2026\\/09\\/0001. Hitungan fisik 28 pcs, catatan 30 pcs. Dua batang kering, dibuang","code":"MP\\/2026\\/09\\/0170","created_by_user_id":2,"id":170}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
786	2	tester	updated	App\\Models\\SupplyOpnameLine	7	OP/2026/09/0001 Pulpen tinta hitam 0,5 mm	\N	{"supply_transaction_id":null}	{"supply_transaction_id":170}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
787	2	tester	updated	App\\Models\\SupplyOpname	1	OP/2026/09/0001 Opname alat tulis akhir September 2026	\N	{"adjusted_at":null,"adjusted_by_user_id":null}	{"adjusted_at":"2026-09-08 11:34:47","adjusted_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:34:47
788	2	tester	created	App\\Models\\SupplyOpname	2	OP/2026/09/0002 [DATA UJI] Uji peringatan pergerakan stok	\N	\N	{"name":"[DATA UJI] Uji peringatan pergerakan stok","scope_category":"pantry","scope_location_id":null,"notes":null,"code":"OP\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:44:09
789	2	tester	updated	App\\Models\\SupplyOpname	2	OP/2026/09/0002 [DATA UJI] Uji peringatan pergerakan stok	\N	{"status":"draft","started_at":null}	{"status":"berjalan","started_at":"2026-09-08 11:44:59"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:44:59
790	2	tester	created	App\\Models\\SupplyTransaction	171	MP/2026/09/0171 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"keluar","quantity":-2,"transaction_date":"2026-09-08 00:00:00","department_id":1,"employee_id":null,"notes":"[DATA UJI] Dipakai pantry saat opname OP\\/2026\\/09\\/0002 sedang berjalan.","code":"MP\\/2026\\/09\\/0171","created_by_user_id":2,"id":171}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:47:08
791	2	tester	updated	App\\Models\\SupplyOpname	2	OP/2026/09/0002 [DATA UJI] Uji peringatan pergerakan stok	\N	{"status":"berjalan"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:47:49
792	2	tester	created	App\\Models\\SupplyOpname	3	OP/2026/09/0003 [DATA UJI] Uji ulang peringatan pergerakan stok	\N	\N	{"name":"[DATA UJI] Uji ulang peringatan pergerakan stok","scope_category":"pantry","scope_location_id":null,"notes":null,"code":"OP\\/2026\\/09\\/0003","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:49:16
793	2	tester	created	App\\Models\\SupplyTransaction	172	MP/2026/09/0172 Gula pasir kemasan 1 kilogram	\N	\N	{"supply_item_id":21,"type":"koreksi_tambah","quantity":2,"transaction_date":"2026-09-08 00:00:00","notes":"[DATA UJI] Mengembalikan stok gula pasir setelah mutasi uji 2 kg pada pemeriksaan kiriman O. Bukan koreksi hasil hitungan fisik.","code":"MP\\/2026\\/09\\/0172","created_by_user_id":2,"id":172}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:49:57
794	2	tester	updated	App\\Models\\SupplyOpname	3	OP/2026/09/0003 [DATA UJI] Uji ulang peringatan pergerakan stok	\N	{"status":"draft","started_at":null}	{"status":"berjalan","started_at":"2026-09-08 11:50:18"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:50:18
795	2	tester	updated	App\\Models\\SupplyOpname	3	OP/2026/09/0003 [DATA UJI] Uji ulang peringatan pergerakan stok	\N	{"status":"berjalan"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 11:50:34
796	1	Administrator	created	App\\Models\\SupplyOpname	4	OP/2026/09/0004 Opname ATK Sep 2026	\N	\N	{"name":"Opname ATK Sep 2026","scope_category":null,"scope_location_id":null,"notes":"Opname Sep ATK 2026","code":"OP\\/2026\\/09\\/0004","created_by_user_id":1,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:22:52
797	1	Administrator	updated	App\\Models\\SupplyOpname	4	OP/2026/09/0004 Opname ATK Sep 2026	\N	{"status":"draft","started_at":null}	{"status":"berjalan","started_at":"2026-09-08 12:23:19"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:23:19
798	1	Administrator	updated	App\\Models\\SupplyOpnameLine	18	OP/2026/09/0004 Air minum galon 19 liter	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":27,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:23:50
799	1	Administrator	updated	App\\Models\\SupplyOpnameLine	19	OP/2026/09/0004 Amplop kabinet coklat	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":31,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:23:55
800	1	Administrator	updated	App\\Models\\SupplyOpnameLine	20	OP/2026/09/0004 Baterai AA isi 4	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":7,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:23:59
801	1	Administrator	updated	App\\Models\\SupplyOpnameLine	21	OP/2026/09/0004 Gula pasir kemasan 1 kilogram	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":10,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:04
802	1	Administrator	updated	App\\Models\\SupplyOpnameLine	22	OP/2026/09/0004 Isi stapler nomor 10	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":37,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:11
803	1	Administrator	updated	App\\Models\\SupplyOpnameLine	23	OP/2026/09/0004 Kantong sampah ukuran besar	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":23,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:18
804	1	Administrator	updated	App\\Models\\SupplyOpnameLine	24	OP/2026/09/0004 Kertas HVS A4 80 gram	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":39,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:25
805	1	Administrator	updated	App\\Models\\SupplyOpnameLine	25	OP/2026/09/0004 Kertas HVS F4 70 gram	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":15,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:30
806	1	Administrator	updated	App\\Models\\SupplyOpnameLine	26	OP/2026/09/0004 Kertas struk kasir	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":20,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:42
807	1	Administrator	updated	App\\Models\\SupplyOpnameLine	27	OP/2026/09/0004 Kopi bubuk kemasan	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":11,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:46
808	1	Administrator	updated	App\\Models\\SupplyOpnameLine	28	OP/2026/09/0004 Lakban bening 2 inci	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":34,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:52
809	1	Administrator	updated	App\\Models\\SupplyOpnameLine	29	OP/2026/09/0004 Lampu LED 12 watt	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":38,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:24:57
810	1	Administrator	updated	App\\Models\\SupplyOpnameLine	30	OP/2026/09/0004 Map plastik berkancing	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":100,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:06
811	1	Administrator	updated	App\\Models\\SupplyOpnameLine	43	OP/2026/09/0004 Toner printer laser	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":6,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:12
812	1	Administrator	updated	App\\Models\\SupplyOpnameLine	42	OP/2026/09/0004 Tisu gulung	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":43,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:17
813	1	Administrator	updated	App\\Models\\SupplyOpnameLine	41	OP/2026/09/0004 Tinta printer warna	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":8,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:25
814	1	Administrator	updated	App\\Models\\SupplyOpnameLine	40	OP/2026/09/0004 Tinta printer hitam	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":12,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:36
815	1	Administrator	updated	App\\Models\\SupplyOpnameLine	39	OP/2026/09/0004 Teh celup kotak isi 25	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":10,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:40
816	1	Administrator	updated	App\\Models\\SupplyOpnameLine	38	OP/2026/09/0004 Stapler ukuran sedang	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":14,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:44
817	1	Administrator	updated	App\\Models\\SupplyOpnameLine	37	OP/2026/09/0004 Spidol papan tulis hitam	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":8,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:48
818	1	Administrator	updated	App\\Models\\SupplyOpnameLine	36	OP/2026/09/0004 Sabun cuci tangan isi ulang	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":21,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:52
819	1	Administrator	updated	App\\Models\\SupplyOpnameLine	35	OP/2026/09/0004 Pulpen tinta hitam 0,5 mm	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":28,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:25:56
820	1	Administrator	updated	App\\Models\\SupplyOpnameLine	31	OP/2026/09/0004 Ordner arsip folio	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":24,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:26:01
821	1	Administrator	updated	App\\Models\\SupplyOpnameLine	32	OP/2026/09/0004 Pembersih lantai	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":8,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:26:06
822	1	Administrator	updated	App\\Models\\SupplyOpnameLine	33	OP/2026/09/0004 Pensil kayu 2B	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":65,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:26:10
823	1	Administrator	updated	App\\Models\\SupplyOpnameLine	34	OP/2026/09/0004 Pulpen tinta biru 0,5 mm	\N	{"counted_quantity":null,"checked":false}	{"counted_quantity":180,"checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:26:16
824	1	Administrator	updated	App\\Models\\SupplyOpname	4	OP/2026/09/0004 Opname ATK Sep 2026	\N	{"status":"berjalan","finished_at":null}	{"status":"selesai","finished_at":"2026-09-08 12:26:26"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 12:26:26
825	\N	\N	created	App\\Models\\Module	31	Service Staff	\N	\N	{"code":"service_staff","name":"Service Staff","description":"Petugas kebersihan dan keamanan, karyawan perusahaan maupun tenaga dari rekanan penyedia.","group":"Master Data","icon":"heroicon-o-identification","sort":70,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":31}	console	\N	\N	2026-09-08 12:28:01
826	\N	\N	created	App\\Models\\Module	32	Service Areas	\N	\N	{"code":"service_areas","name":"Service Areas","description":"Area yang dibersihkan dan diperiksa, beserta seberapa sering dan siapa penanggung jawabnya.","group":"Master Data","icon":"heroicon-o-sparkles","sort":80,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":32}	console	\N	\N	2026-09-08 12:28:01
827	\N	\N	created	App\\Models\\Module	33	Cleaning Inspections	\N	\N	{"code":"cleaning_inspections","name":"Cleaning Inspections","description":"Putaran pemeriksaan kebersihan oleh pengawas GA. Yang tercatat adalah hasil pemeriksaan, bukan laporan petugas.","group":"Facility Services","icon":"heroicon-o-clipboard-document-list","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":33}	console	\N	\N	2026-09-08 12:28:01
828	\N	\N	created	App\\Models\\NumberSequence	62	cleaning_inspection Nomor pemeriksaan kebersihan	\N	\N	{"code":"cleaning_inspection","name":"Nomor pemeriksaan kebersihan","prefix":"KB","separator":"\\/","period_format":"Y\\/m","padding":4,"id":62}	console	\N	\N	2026-09-08 12:28:05
829	2	tester	created	App\\Models\\ServiceStaff	1	Sumarno	\N	\N	{"kind":"kebersihan","name":"Sumarno","vendor_id":null,"phone":"081234567801","start_date":null,"notes":null,"is_active":true,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:30:51
830	2	tester	created	App\\Models\\ServiceStaff	2	Agus Setiawan	\N	\N	{"kind":"keamanan","employee_id":8,"phone":null,"start_date":null,"notes":null,"is_active":true,"name":null,"vendor_id":null,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:31:54
831	2	tester	created	App\\Models\\ServiceStaff	3	Wati Suryani	\N	\N	{"kind":"kebersihan","name":"Wati Suryani","vendor_id":2,"phone":null,"start_date":null,"notes":"[DATA UJI] Dibuat saat pemeriksaan kiriman P.","is_active":true,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:33:22
832	2	tester	updated	App\\Models\\ServiceStaff	3	Lestari Ningsih	\N	{"employee_id":null,"name":"Wati Suryani","vendor_id":2}	{"employee_id":9,"name":null,"vendor_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:34:03
833	2	tester	updated	App\\Models\\ServiceStaff	3	Wati Suryani	\N	{"employee_id":9,"name":null}	{"employee_id":null,"name":"Wati Suryani"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:40:44
834	2	tester	updated	App\\Models\\ServiceStaff	3	Wati Suryani	\N	{"vendor_id":null}	{"vendor_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:41:11
835	2	tester	updated	App\\Models\\ServiceStaff	3	Lestari Ningsih	\N	{"employee_id":null,"name":"Wati Suryani","vendor_id":2}	{"employee_id":9,"name":null,"vendor_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:41:33
836	2	tester	updated	App\\Models\\ServiceStaff	3	Wati Suryani	\N	{"employee_id":9,"name":null,"vendor_id":null}	{"employee_id":null,"name":"Wati Suryani","vendor_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:42:04
837	2	tester	created	App\\Models\\ServiceArea	1	L1-LOBI Lobi dan meja resepsionis	\N	\N	{"code":"L1-LOBI","name":"Lobi dan meja resepsionis","category":"lobi","frequency":"harian","location_id":null,"service_staff_id":1,"notes":null,"is_active":true,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:42:48
838	2	tester	created	App\\Models\\ServiceArea	2	L2-TOILET-PRIA Toilet pria lantai 2	\N	\N	{"code":"L2-TOILET-PRIA","name":"Toilet pria lantai 2","category":"toilet","frequency":"harian","location_id":null,"service_staff_id":3,"notes":null,"is_active":true,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:43:10
839	2	tester	created	App\\Models\\ServiceArea	3	L2-PANTRY Pantry lantai 2	\N	\N	{"code":"L2-PANTRY","name":"Pantry lantai 2","category":"pantry","frequency":"harian","location_id":null,"service_staff_id":3,"notes":null,"is_active":true,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:43:41
840	2	tester	created	App\\Models\\ServiceArea	4	L3-RAPAT-A Ruang rapat Anggrek	\N	\N	{"code":"L3-RAPAT-A","name":"Ruang rapat Anggrek","category":"ruang_rapat","frequency":"mingguan","location_id":null,"service_staff_id":null,"notes":null,"is_active":true,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:44:41
841	2	tester	created	App\\Models\\CleaningInspection	1	KB/2026/09/0001	\N	\N	{"inspection_date":"2026-09-08 00:00:00","scope_frequency":"harian","scope_category":null,"scope_location_id":null,"notes":null,"code":"KB\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:45:04
842	2	tester	updated	App\\Models\\CleaningInspectionLine	1	KB/2026/09/0001 Lobi dan meja resepsionis	\N	{"result":null,"checked":false}	{"result":"bersih","checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:45:28
843	2	tester	updated	App\\Models\\CleaningInspectionLine	2	KB/2026/09/0001 Pantry lantai 2	\N	{"result":null,"checked":false,"notes":null}	{"result":"kurang","checked":true,"notes":"Meja sudah dilap, tetapi tempat sampah di bawah wastafel masih penuh."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:45:48
844	2	tester	updated	App\\Models\\CleaningInspectionLine	3	KB/2026/09/0001 Toilet pria lantai 2	\N	{"result":null,"checked":false,"notes":null}	{"result":"kotor","checked":true,"notes":"Lantai dekat urinoir tergenang dan tempat sampah meluber. Belum disentuh sejak kemarin sore."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:50:58
845	2	tester	updated	App\\Models\\CleaningInspection	1	KB/2026/09/0001	\N	{"status":"berjalan","finished_at":null}	{"status":"selesai","finished_at":"2026-09-08 12:51:16"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 12:51:16
846	2	tester	created	App\\Models\\CleaningInspection	2	KB/2026/09/0002	\N	\N	{"inspection_date":"2026-09-08 00:00:00","scope_frequency":null,"scope_category":"toilet","scope_location_id":null,"notes":"[DATA UJI] Putaran kedua untuk menguji unggah foto.","code":"KB\\/2026\\/09\\/0002","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:02:19
847	2	tester	updated	App\\Models\\CleaningInspection	2	KB/2026/09/0002	\N	{"status":"berjalan"}	{"status":"dibatalkan"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:06:52
848	\N	\N	created	App\\Models\\Module	34	Security Shifts	\N	\N	{"code":"security_shifts","name":"Security Shifts","description":"Jadwal jaga keamanan beserta kehadirannya. Rencana dan kenyataan disimpan berdampingan, tidak saling menimpa.","group":"Facility Services","icon":"heroicon-o-shield-exclamation","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":34}	console	\N	\N	2026-09-08 13:33:14
849	\N	\N	created	App\\Models\\Module	35	Incident Reports	\N	\N	{"code":"incident_reports","name":"Incident Reports","description":"Buku kejadian keamanan. Insiden yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan.","group":"Facility Services","icon":"heroicon-o-exclamation-triangle","sort":30,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":35}	console	\N	\N	2026-09-08 13:33:14
850	\N	\N	created	App\\Models\\NumberSequence	63	incident_report Nomor laporan insiden keamanan	\N	\N	{"code":"incident_report","name":"Nomor laporan insiden keamanan","prefix":"LI","separator":"\\/","period_format":"Y\\/m","padding":4,"id":63}	console	\N	\N	2026-09-08 13:33:20
851	\N	\N	updated	App\\Models\\Module	20	Corrective Maintenance	\N	{"name":"Service Requests"}	{"name":"Corrective Maintenance"}	console	\N	\N	2026-09-08 13:44:05
852	\N	\N	updated	App\\Models\\Module	18	Preventive Maintenance	\N	{"name":"Maintenance Schedules"}	{"name":"Preventive Maintenance"}	console	\N	\N	2026-09-08 13:44:05
853	2	tester	updated	App\\Models\\ServiceStaff	1	Sumarno	\N	{"kind":"kebersihan"}	{"kind":"keduanya"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:49:00
854	2	tester	created	App\\Models\\SecurityShift	1	08 Sep 2026 Pagi Agus Setiawan	\N	\N	{"shift_date":"2026-09-08 00:00:00","shift":"pagi","service_staff_id":2,"location_id":null,"notes":null,"replacement_staff_id":null,"created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:49:52
855	2	tester	created	App\\Models\\SecurityShift	3	08 Sep 2026 Malam Agus Setiawan	\N	\N	{"shift_date":"2026-09-08 00:00:00","shift":"malam","service_staff_id":2,"location_id":null,"notes":null,"replacement_staff_id":null,"created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:52:18
856	2	tester	updated	App\\Models\\SecurityShift	1	08 Sep 2026 Pagi Agus Setiawan	\N	{"attendance":"belum","replacement_staff_id":null,"notes":null}	{"attendance":"digantikan","replacement_staff_id":1,"notes":"Agus izin ke dokter, digantikan Sumarno mulai pukul 07.00."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:52:56
857	2	tester	updated	App\\Models\\SecurityShift	3	08 Sep 2026 Malam Agus Setiawan	\N	{"attendance":"belum","replacement_staff_id":null}	{"attendance":"digantikan","replacement_staff_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:53:56
858	2	tester	updated	App\\Models\\SecurityShift	3	08 Sep 2026 Malam Agus Setiawan	\N	{"attendance":"digantikan","replacement_staff_id":1}	{"attendance":"terlambat","replacement_staff_id":null}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:54:15
859	2	tester	created	App\\Models\\IncidentReport	1	LI/2026/09/0001	\N	\N	{"occurred_at":"2026-09-08 13:54:00","category":"kerusakan","severity":"tinggi","location_id":21,"reported_by_staff_id":2,"security_shift_id":1,"description":"Pagar besi sisi timur bengkok dan gemboknya patah. Diduga ditabrak kendaraan saat parkir mundur. Area sudah dipasangi garis pembatas sementara.","code":"LI\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:55:51
860	2	tester	updated	App\\Models\\Employee	1	CONTOH-0001 CONTOH Staf GA	\N	{"user_id":null}	{"user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:57:28
861	2	tester	created	App\\Models\\ServiceRequest	3	PB/2026/09/0003 Kerusakan atau perusakan di Area parkir kendaraan dinas, LI/2026/09/0001	\N	\N	{"requester_employee_id":1,"service_request_category_id":5,"location_id":21,"title":"Kerusakan atau perusakan di Area parkir kendaraan dinas, LI\\/2026\\/09\\/0001","description":"Pagar besi sisi timur bengkok dan gemboknya patah. Diduga ditabrak kendaraan saat parkir mundur. Area sudah dipasangi garis pembatas sementara.","priority":"mendesak","code":"PB\\/2026\\/09\\/0003","created_by_user_id":2,"submitted_at":"2026-09-08 13:58:00","department_id":1,"status":"disetujui","approved_at":"2026-09-08 13:58:00","approval_skipped_reason":"Prioritas mendesak, langsung diteruskan ke tim GA","id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:58:00
862	2	tester	updated	App\\Models\\IncidentReport	1	LI/2026/09/0001	\N	{"service_request_id":null}	{"service_request_id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:58:00
863	2	tester	updated	App\\Models\\IncidentReport	1	LI/2026/09/0001	\N	{"closed_at":null,"closing_note":null,"closed_by_user_id":null}	{"closed_at":"2026-09-08 13:59:33","closing_note":"Pagar sudah dipagari sementara dan perbaikannya berjalan lewat PB\\/2026\\/09\\/0003. Rekaman kamera parkir diperiksa, tidak ada nomor polisi yang terbaca jelas.","closed_by_user_id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 13:59:33
864	\N	\N	created	App\\Models\\Module	36	Letters	\N	\N	{"code":"letters","name":"Letters","description":"Buku agenda surat masuk dan surat keluar, beserta serah terima surat masuk ke orang yang dituju.","group":"Correspondence","icon":"heroicon-o-envelope","sort":10,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":36}	console	\N	\N	2026-09-08 14:48:32
865	\N	\N	created	App\\Models\\Module	37	Parcel Shipments	\N	\N	{"code":"parcel_shipments","name":"Parcel Shipments","description":"Permintaan kirim paket keluar dan biaya sebenarnya dari resi ekspedisi. Biayanya memotong pagu anggaran departemen yang dibebani.","group":"Correspondence","icon":"heroicon-o-inbox-stack","sort":20,"available_actions":"[\\"read\\",\\"create\\",\\"update\\",\\"delete\\"]","is_active":true,"id":37}	console	\N	\N	2026-09-08 14:48:32
866	\N	\N	created	App\\Models\\Module	38	Business Trips	\N	\N	{"code":"business_trips","name":"Business Trips","description":"Perjalanan dinas beserta uang muka dan pertanggungjawabannya. Biaya yang sudah ditutup memotong pagu anggaran departemen yang dibebani.","group":"Vehicles","icon":"heroicon-o-map","sort":30,"available_actions":"[\\"read\\",\\"read_all\\",\\"create\\",\\"update\\",\\"delete\\",\\"approve\\",\\"pay\\",\\"verify\\"]","is_active":true,"id":38}	console	\N	\N	2026-09-08 14:48:32
867	\N	\N	created	App\\Models\\NumberSequence	64	business_trip Nomor surat perjalanan dinas	\N	\N	{"code":"business_trip","name":"Nomor surat perjalanan dinas","prefix":"SPD","separator":"\\/","period_format":"Y\\/m","padding":4,"id":64}	console	\N	\N	2026-09-08 14:48:36
868	\N	\N	created	App\\Models\\NumberSequence	65	parcel_shipment Nomor pengiriman paket	\N	\N	{"code":"parcel_shipment","name":"Nomor pengiriman paket","prefix":"KP","separator":"\\/","period_format":"Y\\/m","padding":4,"id":65}	console	\N	\N	2026-09-08 14:48:36
869	\N	\N	created	App\\Models\\NumberSequence	66	letter_in Nomor agenda surat masuk	\N	\N	{"code":"letter_in","name":"Nomor agenda surat masuk","prefix":"AM","separator":"\\/","period_format":"Y\\/m","padding":4,"id":66}	console	\N	\N	2026-09-08 14:48:36
870	\N	\N	created	App\\Models\\NumberSequence	67	letter_out Nomor agenda surat keluar	\N	\N	{"code":"letter_out","name":"Nomor agenda surat keluar","prefix":"AK","separator":"\\/","period_format":"Y\\/m","padding":4,"id":67}	console	\N	\N	2026-09-08 14:48:36
871	\N	\N	created	App\\Models\\ExpenseCategory	11	KRIM Pengiriman surat dan paket	\N	\N	{"code":"KRIM","name":"Pengiriman surat dan paket","source":"kiriman","description":"Ongkos kirim, asuransi, dan pengemasan paket keluar. Dijumlahkan dari pengiriman yang paketnya sudah berangkat, menurut tanggal kirimnya.","is_active":true,"id":11}	console	\N	\N	2026-09-08 14:48:38
872	\N	\N	created	App\\Models\\ExpenseCategory	12	SPPD Perjalanan dinas	\N	\N	{"code":"SPPD","name":"Perjalanan dinas","source":"perjalanan_dinas","description":"Transportasi, penginapan, uang harian, dan konsumsi selama perjalanan dinas. Dijumlahkan dari rincian pertanggungjawaban yang sudah ditutup, menurut tanggal tiap pengeluarannya.","is_active":true,"id":12}	console	\N	\N	2026-09-08 14:48:38
873	1	Administrator	created	App\\Models\\BusinessTrip	1	SPD/2026/09/0001 Agus Setiawan ke Meeting di Bali	\N	\N	{"employee_id":8,"transport_mode":"pesawat","destination":"Meeting di Bali","estimated_cost":3000000,"start_date":"2026-09-08 00:00:00","end_date":"2026-09-09 00:00:00","purpose":"Rapat koordinasi\\nkonsolidasi\\npenyelesaian pekerjaan","notes":"Lokasi meeting di Kantor Cabang Bali","code":"SPD\\/2026\\/09\\/0001","created_by_user_id":1,"submitted_at":"2026-09-08 14:58:10","department_id":6,"status":"disetujui","approval_skipped_reason":"Departemen Information Technology belum punya kepala departemen, jadi tidak ada yang bisa menyetujui.","approved_at":"2026-09-08 14:58:10","id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 14:58:10
874	1	Administrator	updated	App\\Models\\BusinessTrip	1	SPD/2026/09/0001 Agus Setiawan ke Meeting di Bali	\N	{"advance_amount":null,"advance_paid_at":null,"advance_paid_by_user_id":null}	{"advance_amount":2000000,"advance_paid_at":"2026-09-08 14:58:00","advance_paid_by_user_id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 14:58:46
875	1	Administrator	created	App\\Models\\BusinessTripExpense	1	SPD/2026/09/0001 Taxi rumah bandara	\N	\N	{"expense_date":"2026-09-08 00:00:00","category":"transport","description":"Taxi rumah bandara","amount":100000,"file_path":"bukti-perjalanan\\/01M20RGTTMJXBP6W75SZZB1919.pdf","original_name":"Test dokumen 1.pdf","business_trip_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 14:59:56
876	1	Administrator	created	App\\Models\\BusinessTripExpense	2	SPD/2026/09/0001 Pesawat Jogja Bali	\N	\N	{"expense_date":"2026-09-08 00:00:00","category":"transport","description":"Pesawat Jogja Bali","amount":600000,"file_path":"bukti-perjalanan\\/01M20RHTJSEJHK7NQN3YSH74GZ.pdf","original_name":"Test dokumen 2.pdf","business_trip_id":1,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:00:28
877	1	Administrator	created	App\\Models\\BusinessTripExpense	3	SPD/2026/09/0001 Taxi  Bandara ke Kantor Bali	\N	\N	{"expense_date":"2026-09-08 00:00:00","category":"transport","description":"Taxi  Bandara ke Kantor Bali","amount":150000,"file_path":"bukti-perjalanan\\/01M20RJW4T8Q4SWMA6ND6BAK8B.pdf","original_name":"Test dokumen 2.pdf","business_trip_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:01:03
878	1	Administrator	created	App\\Models\\BusinessTripExpense	4	SPD/2026/09/0001 Hotel Abadi Lima	\N	\N	{"expense_date":"2026-09-09 00:00:00","category":"penginapan","description":"Hotel Abadi Lima","amount":500000,"file_path":"bukti-perjalanan\\/01M20RM4TN3Y85HJFN0S7NMSPX.pdf","original_name":"Test dokumen 1.pdf","business_trip_id":1,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:01:44
879	1	Administrator	created	App\\Models\\BusinessTripExpense	5	SPD/2026/09/0001 Uang makan dan uang saku	\N	\N	{"expense_date":"2026-09-08 00:00:00","category":"uang_harian","description":"Uang makan dan uang saku","amount":500000,"file_path":null,"original_name":null,"business_trip_id":1,"id":5}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:02:19
880	1	Administrator	created	App\\Models\\BusinessTripExpense	6	SPD/2026/09/0001 Pesawat Bali Jogja	\N	\N	{"expense_date":"2026-09-09 00:00:00","category":"transport","description":"Pesawat Bali Jogja","amount":1000000,"file_path":"bukti-perjalanan\\/01M20RPAKRBXNHQ750R5HAFWYZ.pdf","original_name":"Test dokumen 2.pdf","business_trip_id":1,"id":6}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:02:58
881	1	Administrator	created	App\\Models\\BusinessTripExpense	7	SPD/2026/09/0001 Taxi bandara -RUmah	\N	\N	{"expense_date":"2026-09-08 00:00:00","category":"transport","description":"Taxi bandara -RUmah","amount":250000,"file_path":"bukti-perjalanan\\/01M20RQ925XGSR4NCY3JE56N2B.pdf","original_name":"Test dokumen 1.pdf","business_trip_id":1,"id":7}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:03:27
882	1	Administrator	updated	App\\Models\\BusinessTrip	1	SPD/2026/09/0001 Agus Setiawan ke Meeting di Bali	\N	{"status":"disetujui","reported_at":null}	{"status":"dipertanggungjawabkan","reported_at":"2026-09-08 15:03:39"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:03:39
883	1	Administrator	updated	App\\Models\\BusinessTrip	1	SPD/2026/09/0001 Agus Setiawan ke Meeting di Bali	\N	{"status":"dipertanggungjawabkan","settled_at":null,"settled_by_user_id":null,"settlement_note":null}	{"status":"selesai","settled_at":"2026-09-08 15:04:37","settled_by_user_id":1,"settlement_note":"selesai"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:04:37
884	1	Administrator	created	App\\Models\\CleaningInspection	3	KB/2026/09/0003	\N	\N	{"inspection_date":"2026-09-08 00:00:00","scope_frequency":"harian","scope_category":"toilet","scope_location_id":null,"notes":"test","code":"KB\\/2026\\/09\\/0003","created_by_user_id":1,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:06:19
885	1	Administrator	updated	App\\Models\\CleaningInspectionLine	5	KB/2026/09/0003 Toilet pria lantai 2	\N	{"result":null,"checked":false}	{"result":"bersih","checked":true}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:06:34
886	1	Administrator	updated	App\\Models\\CleaningInspection	3	KB/2026/09/0003	\N	{"status":"berjalan","finished_at":null}	{"status":"selesai","finished_at":"2026-09-08 15:07:03"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:07:03
887	1	Administrator	created	App\\Models\\SecurityShift	4	09 Sep 2026 Pagi Agus Setiawan	\N	\N	{"shift_date":"2026-09-09 00:00:00","shift":"pagi","service_staff_id":2,"location_id":1,"notes":null,"replacement_staff_id":null,"created_by_user_id":1,"id":4}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:07:43
888	1	Administrator	updated	App\\Models\\SecurityShift	4	09 Sep 2026 Pagi Agus Setiawan	\N	{"attendance":"belum","checked_in_at":null,"checked_out_at":null}	{"attendance":"hadir","checked_in_at":"2026-09-08 09:00:00","checked_out_at":"2026-09-09 00:00:00"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:08:55
889	1	Administrator	created	App\\Models\\ParcelShipment	1	KP/2026/09/0001 ke Joko Widyawan	\N	\N	{"request_date":"2026-09-08 00:00:00","requester_employee_id":4,"department_id":2,"estimated_cost":15000,"recipient_name":"Joko Widyawan","recipient_phone":"09876789987","recipient_address":"Parcel pernikahan","contents":"barang pecah belah","weight_kg":2,"notes":null,"code":"KP\\/2026\\/09\\/0001","created_by_user_id":1,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	2026-09-08 15:10:58
890	2	tester	created	App\\Models\\BusinessTrip	2	SPD/2026/09/0002 Andi Prasetyo ke Surabaya	\N	\N	{"employee_id":2,"transport_mode":"kereta","destination":"Surabaya","estimated_cost":12000000,"start_date":"2026-09-14 00:00:00","end_date":"2026-09-16 00:00:00","purpose":"Audit fasilitas kantor cabang Surabaya bersama tim gabungan.","notes":null,"code":"SPD\\/2026\\/09\\/0002","created_by_user_id":2,"submitted_at":"2026-09-08 15:24:31","department_id":4,"status":"disetujui","approval_skipped_reason":"Departemen General Affair belum punya kepala departemen, jadi tidak ada yang bisa menyetujui.","approved_at":"2026-09-08 15:24:31","id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:24:31
891	2	tester	updated	App\\Models\\BusinessTrip	2	SPD/2026/09/0002 Andi Prasetyo ke Surabaya	\N	{"advance_amount":null,"advance_paid_at":null,"advance_paid_by_user_id":null,"advance_note":null}	{"advance_amount":10000000,"advance_paid_at":"2026-09-08 15:24:00","advance_paid_by_user_id":2,"advance_note":"Transfer bank ke rekening Andi Prasetyo"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:25:01
892	2	tester	created	App\\Models\\BusinessTripExpense	8	SPD/2026/09/0002 Tiket kereta Yogyakarta ke Surabaya pulang pergi untuk 5 orang	\N	\N	{"expense_date":"2026-09-14 00:00:00","category":"transport","description":"Tiket kereta Yogyakarta ke Surabaya pulang pergi untuk 5 orang","amount":3250000,"file_path":null,"original_name":null,"business_trip_id":2,"id":8}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:27:01
893	2	tester	created	App\\Models\\BusinessTripExpense	9	SPD/2026/09/0002 Hotel 3 kamar untuk 2 malam	\N	\N	{"expense_date":"2026-09-14 00:00:00","category":"penginapan","description":"Hotel 3 kamar untuk 2 malam","amount":4800000,"file_path":null,"original_name":null,"business_trip_id":2,"id":9}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:27:20
894	2	tester	created	App\\Models\\BusinessTripExpense	10	SPD/2026/09/0002 Uang harian 5 orang selama 3 hari	\N	\N	{"expense_date":"2026-09-15 00:00:00","category":"uang_harian","description":"Uang harian 5 orang selama 3 hari","amount":2250000,"file_path":null,"original_name":null,"business_trip_id":2,"id":10}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:27:30
895	2	tester	created	App\\Models\\BusinessTripExpense	11	SPD/2026/09/0002 Makan bersama tim cabang Surabaya	\N	\N	{"expense_date":"2026-09-16 00:00:00","category":"konsumsi","description":"Makan bersama tim cabang Surabaya","amount":875000,"file_path":null,"original_name":null,"business_trip_id":2,"id":11}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:27:45
896	2	tester	updated	App\\Models\\BusinessTrip	2	SPD/2026/09/0002 Andi Prasetyo ke Surabaya	\N	{"status":"disetujui","reported_at":null}	{"status":"dipertanggungjawabkan","reported_at":"2026-09-08 15:28:33"}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:28:33
897	2	tester	updated	App\\Models\\BusinessTrip	2	SPD/2026/09/0002 Andi Prasetyo ke Surabaya	\N	{"status":"dipertanggungjawabkan","settled_at":null,"settled_by_user_id":null,"settlement_note":null}	{"status":"selesai","settled_at":"2026-09-08 15:28:53","settled_by_user_id":2,"settlement_note":"Kekurangan Rp 1.175.000 diproses lewat penggantian biaya atas nama Andi Prasetyo."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:28:53
898	2	tester	created	App\\Models\\Budget	9	General Affair Perjalanan dinas 2026	\N	\N	{"department_id":4,"expense_category_id":12,"fiscal_year":2026,"amount":20000000,"notes":"[DATA UJI] Pagu contoh untuk memeriksa realisasi perjalanan dinas.","created_by_user_id":2,"id":9}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:30:00
899	2	tester	created	App\\Models\\Letter	1	AM/2026/09/0001 Undangan sosialisasi wajib lapor ketenagakerjaan	\N	\N	{"direction":"masuk","category":"lainnya","counterparty":"Dinas Tenaga Kerja Kota Yogyakarta","letter_number":"800\\/1245\\/DISNAKER\\/IX\\/2026","letter_date":"2026-09-03 00:00:00","logged_date":"2026-09-08 00:00:00","subject":"Undangan sosialisasi wajib lapor ketenagakerjaan","assigned_employee_id":9,"notes":null,"file_path":null,"original_name":null,"signer_employee_id":null,"code":"AM\\/2026\\/09\\/0001","created_by_user_id":2,"id":1}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:30:57
900	2	tester	updated	App\\Models\\Letter	1	AM/2026/09/0001 Undangan sosialisasi wajib lapor ketenagakerjaan	\N	{"handed_over_at":null,"handed_over_to_employee_id":null,"handover_note":null}	{"handed_over_at":"2026-09-08 15:31:00","handed_over_to_employee_id":10,"handover_note":"Diterima Hendra Wijaya karena Lestari Ningsih sedang cuti."}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:31:26
901	2	tester	created	App\\Models\\Letter	2	AK/2026/09/0001 Permintaan penawaran jasa kebersihan tahun 2027	\N	\N	{"direction":"keluar","category":"lainnya","counterparty":"PT Sinar Abadi Sentosa","letter_number":"012\\/GA-GTI\\/IX\\/2026","letter_date":"2026-09-08 00:00:00","logged_date":"2026-09-08 00:00:00","subject":"Permintaan penawaran jasa kebersihan tahun 2027","signer_employee_id":2,"notes":null,"file_path":null,"original_name":null,"assigned_employee_id":null,"handed_over_at":null,"handed_over_to_employee_id":null,"handover_note":null,"code":"AK\\/2026\\/09\\/0001","created_by_user_id":2,"id":2}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:32:25
902	2	tester	created	App\\Models\\Letter	3	AM/2026/09/0002 Permintaan penawaran jasa kebersihan tahun 2027	\N	\N	{"direction":"masuk","category":"lainnya","counterparty":"PT Karya Bersih Mandiri","letter_number":"012\\/GA-GTI\\/IX\\/2026","letter_date":"2026-09-08 00:00:00","logged_date":"2026-09-08 00:00:00","subject":"Permintaan penawaran jasa kebersihan tahun 2027","assigned_employee_id":2,"notes":null,"file_path":null,"original_name":null,"signer_employee_id":null,"code":"AM\\/2026\\/09\\/0002","created_by_user_id":2,"id":3}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:33:02
903	2	tester	deleted	App\\Models\\Letter	3	AM/2026/09/0002 Permintaan penawaran jasa kebersihan tahun 2027	\N	{"id":3,"direction":"masuk","code":"AM\\/2026\\/09\\/0002","letter_number":"012\\/GA-GTI\\/IX\\/2026","letter_date":"2026-09-08T00:00:00.000000Z","logged_date":"2026-09-08T00:00:00.000000Z","category":"lainnya","counterparty":"PT Karya Bersih Mandiri","subject":"Permintaan penawaran jasa kebersihan tahun 2027","notes":null,"assigned_employee_id":2,"handed_over_at":null,"handed_over_to_employee_id":null,"handover_note":null,"signer_employee_id":null,"file_path":null,"original_name":null,"created_by_user_id":2}	\N	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:33:18
904	2	tester	updated	App\\Models\\ParcelShipment	1	KP/2026/09/0001 ke Joko Widyawan	\N	{"status":"diminta","vendor_id":null,"tracking_number":null,"shipped_date":null,"shipping_cost":null,"insurance_cost":null,"packing_cost":null,"discount_amount":null,"tax_amount":null}	{"status":"dikirim","vendor_id":2,"tracking_number":"AJ0098771265","shipped_date":"2026-09-08 00:00:00","shipping_cost":185000,"insurance_cost":12500,"packing_cost":25000,"discount_amount":20000,"tax_amount":22550}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:34:04
905	2	tester	created	App\\Models\\Budget	10	CONTOH Finance Pengiriman surat dan paket 2026	\N	\N	{"department_id":2,"expense_category_id":11,"fiscal_year":2026,"amount":1000000,"notes":"[DATA UJI] Pagu contoh untuk memeriksa realisasi biaya kiriman.","created_by_user_id":2,"id":10}	http://127.0.0.1:8000/livewire-5e9bb489/update	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	2026-09-08 15:34:31
\.


--
-- Data for Name: budgets; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.budgets (id, department_id, expense_category_id, fiscal_year, amount, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	5	2	2026	2000000.00	[DATA UJI] pagu BBM	2	2026-09-07 11:57:40	2026-09-07 11:57:40
2	5	3	2026	3000000.00	[DATA UJI] pagu dokumen kendaraan	2	2026-09-07 11:59:24	2026-09-07 11:59:24
3	5	7	2026	50000000.00	[DATA UJI] pagu pemeliharaan	2	2026-09-07 11:59:42	2026-09-07 11:59:42
4	5	1	2026	20000000.00	[DATA UJI] pagu pemeliharaan	2	2026-09-07 12:00:51	2026-09-07 12:00:51
5	4	1	2026	10000000.00	[DATA UJI] pagu pemeliharaan GA	2	2026-09-07 12:03:46	2026-09-07 12:03:46
6	5	6	2026	10000000.00	[DATA UJI] Pagu kebersihan dan keamanan Finance 2026.	2	2026-09-07 12:44:05	2026-09-07 12:44:05
7	5	9	2026	2000000.00	[DATA UJI] Pagu transportasi Finance 2026.	2	2026-09-07 13:37:44	2026-09-07 13:37:44
9	4	12	2026	20000000.00	[DATA UJI] Pagu contoh untuk memeriksa realisasi perjalanan dinas.	2	2026-09-08 15:30:00	2026-09-08 15:30:00
10	2	11	2026	1000000.00	[DATA UJI] Pagu contoh untuk memeriksa realisasi biaya kiriman.	2	2026-09-08 15:34:31	2026-09-08 15:34:31
\.


--
-- Data for Name: business_trip_expenses; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.business_trip_expenses (id, business_trip_id, expense_date, category, description, amount, file_path, original_name, created_at, updated_at) FROM stdin;
1	1	2026-09-08	transport	Taxi rumah bandara	100000.00	bukti-perjalanan/01M20RGTTMJXBP6W75SZZB1919.pdf	Test dokumen 1.pdf	2026-09-08 14:59:56	2026-09-08 14:59:56
2	1	2026-09-08	transport	Pesawat Jogja Bali	600000.00	bukti-perjalanan/01M20RHTJSEJHK7NQN3YSH74GZ.pdf	Test dokumen 2.pdf	2026-09-08 15:00:28	2026-09-08 15:00:28
3	1	2026-09-08	transport	Taxi  Bandara ke Kantor Bali	150000.00	bukti-perjalanan/01M20RJW4T8Q4SWMA6ND6BAK8B.pdf	Test dokumen 2.pdf	2026-09-08 15:01:03	2026-09-08 15:01:03
4	1	2026-09-09	penginapan	Hotel Abadi Lima	500000.00	bukti-perjalanan/01M20RM4TN3Y85HJFN0S7NMSPX.pdf	Test dokumen 1.pdf	2026-09-08 15:01:44	2026-09-08 15:01:44
5	1	2026-09-08	uang_harian	Uang makan dan uang saku	500000.00	\N	\N	2026-09-08 15:02:19	2026-09-08 15:02:19
6	1	2026-09-09	transport	Pesawat Bali Jogja	1000000.00	bukti-perjalanan/01M20RPAKRBXNHQ750R5HAFWYZ.pdf	Test dokumen 2.pdf	2026-09-08 15:02:58	2026-09-08 15:02:58
7	1	2026-09-08	transport	Taxi bandara -RUmah	250000.00	bukti-perjalanan/01M20RQ925XGSR4NCY3JE56N2B.pdf	Test dokumen 1.pdf	2026-09-08 15:03:27	2026-09-08 15:03:27
8	2	2026-09-14	transport	Tiket kereta Yogyakarta ke Surabaya pulang pergi untuk 5 orang	3250000.00	\N	\N	2026-09-08 15:27:01	2026-09-08 15:27:01
9	2	2026-09-14	penginapan	Hotel 3 kamar untuk 2 malam	4800000.00	\N	\N	2026-09-08 15:27:20	2026-09-08 15:27:20
10	2	2026-09-15	uang_harian	Uang harian 5 orang selama 3 hari	2250000.00	\N	\N	2026-09-08 15:27:30	2026-09-08 15:27:30
11	2	2026-09-16	konsumsi	Makan bersama tim cabang Surabaya	875000.00	\N	\N	2026-09-08 15:27:45	2026-09-08 15:27:45
\.


--
-- Data for Name: business_trip_participants; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.business_trip_participants (id, business_trip_id, employee_id, created_at, updated_at) FROM stdin;
1	2	4	2026-09-08 15:24:31	2026-09-08 15:24:31
2	2	3	2026-09-08 15:24:31	2026-09-08 15:24:31
3	2	5	2026-09-08 15:24:31	2026-09-08 15:24:31
4	2	7	2026-09-08 15:24:31	2026-09-08 15:24:31
\.


--
-- Data for Name: business_trips; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.business_trips (id, code, employee_id, department_id, destination, purpose, start_date, end_date, transport_mode, estimated_cost, status, submitted_at, approver_employee_id, approved_at, approval_note, approval_skipped_reason, rejection_reason, advance_amount, advance_paid_at, advance_paid_by_user_id, advance_note, reported_at, settled_at, settled_by_user_id, settlement_note, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	SPD/2026/09/0001	8	6	Meeting di Bali	Rapat koordinasi\nkonsolidasi\npenyelesaian pekerjaan	2026-09-08	2026-09-09	pesawat	3000000.00	selesai	2026-09-08 14:58:10	\N	2026-09-08 14:58:10	\N	Departemen Information Technology belum punya kepala departemen, jadi tidak ada yang bisa menyetujui.	\N	2000000.00	2026-09-08 14:58:00	1	\N	2026-09-08 15:03:39	2026-09-08 15:04:37	1	selesai	Lokasi meeting di Kantor Cabang Bali	1	2026-09-08 14:58:10	2026-09-08 15:04:37
2	SPD/2026/09/0002	2	4	Surabaya	Audit fasilitas kantor cabang Surabaya bersama tim gabungan.	2026-09-14	2026-09-16	kereta	12000000.00	selesai	2026-09-08 15:24:31	\N	2026-09-08 15:24:31	\N	Departemen General Affair belum punya kepala departemen, jadi tidak ada yang bisa menyetujui.	\N	10000000.00	2026-09-08 15:24:00	2	Transfer bank ke rekening Andi Prasetyo	2026-09-08 15:28:33	2026-09-08 15:28:53	2	Kekurangan Rp 1.175.000 diproses lewat penggantian biaya atas nama Andi Prasetyo.	\N	2	2026-09-08 15:24:31	2026-09-08 15:28:53
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache (key, value, expiration) FROM stdin;
gais_cache_gais.settings	a:17:{s:15:"perusahaan.nama";N;s:23:"perusahaan.nama_singkat";s:4:"GAIS";s:17:"perusahaan.alamat";N;s:18:"perusahaan.telepon";N;s:16:"perusahaan.email";N;s:15:"perusahaan.logo";s:6:"[LOGO]";s:14:"label.lebar_mm";s:2:"70";s:15:"label.tinggi_mm";s:2:"37";s:11:"label.kolom";s:1:"3";s:20:"label.margin_atas_mm";s:1:"8";s:20:"label.margin_kiri_mm";s:1:"5";s:16:"penyusutan.mulai";s:15:"bulan_perolehan";s:21:"penyusutan.nilai_sisa";s:3:"nol";s:30:"penyusutan.saldo_menurun_akhir";s:8:"habiskan";s:24:"penyusutan.periode_mulai";N;s:35:"layanan.mendesak_lewati_persetujuan";s:2:"ya";s:18:"aplikasi.hak_cipta";s:24:"PT. Gamatechno Indonesia";}	2104243485
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cleaning_inspection_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cleaning_inspection_lines (id, cleaning_inspection_id, service_area_id, area_code, area_name, staff_name, result, checked, notes, file_path, original_name, created_at, updated_at) FROM stdin;
1	1	1	L1-LOBI	Lobi dan meja resepsionis	Sumarno	bersih	t	\N	\N	\N	2026-09-08 12:45:04	2026-09-08 12:45:28
2	1	3	L2-PANTRY	Pantry lantai 2	Wati Suryani	kurang	t	Meja sudah dilap, tetapi tempat sampah di bawah wastafel masih penuh.	\N	\N	2026-09-08 12:45:04	2026-09-08 12:45:48
3	1	2	L2-TOILET-PRIA	Toilet pria lantai 2	Wati Suryani	kotor	t	Lantai dekat urinoir tergenang dan tempat sampah meluber. Belum disentuh sejak kemarin sore.	\N	\N	2026-09-08 12:45:04	2026-09-08 12:50:58
4	2	2	L2-TOILET-PRIA	Toilet pria lantai 2	Wati Suryani	\N	f	\N	\N	\N	2026-09-08 13:02:19	2026-09-08 13:02:19
5	3	2	L2-TOILET-PRIA	Toilet pria lantai 2	Wati Suryani	bersih	t	\N	\N	\N	2026-09-08 15:06:19	2026-09-08 15:06:34
\.


--
-- Data for Name: cleaning_inspections; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.cleaning_inspections (id, code, inspection_date, scope_category, scope_frequency, scope_location_id, status, finished_at, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	KB/2026/09/0001	2026-09-08	\N	harian	\N	selesai	2026-09-08 12:51:16	\N	2	2026-09-08 12:45:04	2026-09-08 12:51:16
2	KB/2026/09/0002	2026-09-08	toilet	\N	\N	dibatalkan	\N	[DATA UJI] Putaran kedua untuk menguji unggah foto.	2	2026-09-08 13:02:19	2026-09-08 13:06:52
3	KB/2026/09/0003	2026-09-08	toilet	harian	\N	selesai	2026-09-08 15:07:03	test	1	2026-09-08 15:06:19	2026-09-08 15:07:03
\.


--
-- Data for Name: departments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.departments (id, code, name, cost_center, parent_id, is_active, created_at, updated_at, head_employee_id) FROM stdin;
1	CONTOH-GA	CONTOH General Affair	CC-GA	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29	\N
2	CONTOH-FIN	CONTOH Finance	CC-FIN	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29	\N
3	CONTOH-IT	CONTOH Information Technology	CC-IT	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29	\N
4	GA	General Affair	CC-GA	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04	\N
6	IT	Information Technology	CC-IT	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04	\N
7	HRD	Human Resources	CC-HRD	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04	\N
8	OPS	Operations	CC-OPS	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04	\N
5	FIN	Finance	CC-FIN	\N	t	2026-09-06 14:04:04	2026-09-08 06:17:30	\N
\.


--
-- Data for Name: depreciation_entries; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.depreciation_entries (id, depreciation_period_id, asset_id, period, expense, accumulated_after, book_value_after, method, useful_life_months, acquisition_cost, residual_value, months_covered, covers_from, created_at, updated_at) FROM stdin;
179	3	60	2026-09	2892656.25	164881406.25	112813593.75	garis_lurus	96	277695000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
180	3	147	2026-09	1906791.67	83898833.48	99153166.52	garis_lurus	96	183052000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
181	3	36	2026-09	550770.83	19276979.05	7160020.95	garis_lurus	48	26437000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
182	3	96	2026-09	424270.83	17819374.86	2545625.14	garis_lurus	48	20365000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
183	3	52	2026-09	177916.67	1067500.02	7472499.98	garis_lurus	48	8540000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
184	3	56	2026-09	54489.58	5176510.10	54489.90	garis_lurus	96	5231000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
185	3	19	2026-09	89114.58	7842083.04	712916.96	garis_lurus	96	8555000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
186	3	129	2026-09	58395.83	4321291.42	1284708.58	garis_lurus	96	5606000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
187	3	22	2026-09	366041.67	15739791.81	1830208.19	garis_lurus	48	17570000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
188	3	53	2026-09	155937.50	1247500.00	6237500.00	garis_lurus	48	7485000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
189	3	138	2026-09	74645.83	1940791.58	1642208.42	garis_lurus	48	3583000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
190	3	51	2026-09	78666.67	1494666.73	2281333.27	garis_lurus	48	3776000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
191	3	63	2026-09	210510.42	14525218.98	5683781.02	garis_lurus	96	20209000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
192	3	78	2026-09	74197.92	4897062.72	2225937.28	garis_lurus	96	7123000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
193	3	26	2026-09	186968.75	7104812.50	10844187.50	garis_lurus	96	17949000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
194	3	54	2026-09	181281.25	5982281.25	11420718.75	garis_lurus	96	17403000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
195	3	55	2026-09	268770.83	7525583.24	18276416.76	garis_lurus	96	25802000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
196	3	128	2026-09	222979.17	6912354.27	14493645.73	garis_lurus	96	21406000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
197	3	2	2026-09	102083333.33	17252083332.77	7247916667.23	garis_lurus	240	24500000000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
198	3	38	2026-09	2466635.42	130731677.26	106065322.74	garis_lurus	96	236797000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
199	3	122	2026-09	2708489.58	200428228.92	59586771.08	garis_lurus	96	260015000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
200	3	135	2026-09	3053260.42	131290198.06	161822801.94	garis_lurus	96	293113000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
201	3	74	2026-09	202125.00	8287125.00	1414875.00	garis_lurus	48	9702000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
202	3	45	2026-09	259333.33	3630666.62	8817333.38	garis_lurus	48	12448000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
203	3	50	2026-09	73645.83	147291.66	3387708.34	garis_lurus	48	3535000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
204	3	101	2026-09	32666.67	2090666.88	1045333.12	garis_lurus	96	3136000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
205	3	115	2026-09	100614.58	6741176.86	2917823.14	garis_lurus	96	9659000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
206	3	131	2026-09	114968.75	1609562.50	9427437.50	garis_lurus	96	11037000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
207	3	150	2026-09	144291.67	577166.68	13274833.32	garis_lurus	96	13852000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
208	3	23	2026-09	555020.83	20535770.71	6105229.29	garis_lurus	48	26641000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
209	3	81	2026-09	335187.50	11061187.50	5027812.50	garis_lurus	48	16089000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
210	3	75	2026-09	671625.00	8059500.00	24178500.00	garis_lurus	48	32238000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
211	3	127	2026-09	517062.50	3102375.00	21716625.00	garis_lurus	48	24819000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
212	3	88	2026-09	187750.00	1689750.00	16334250.00	garis_lurus	96	18024000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
213	3	144	2026-09	2530916.67	194880583.59	48087416.41	garis_lurus	96	242968000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
214	3	143	2026-09	5382947.92	145339593.84	371423406.16	garis_lurus	96	516763000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
215	3	77	2026-09	4922062.50	19688250.00	452829750.00	garis_lurus	96	472518000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
216	3	48	2026-09	453416.67	16323000.12	5440999.88	garis_lurus	48	21764000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
217	3	110	2026-09	716354.17	21490625.10	12894374.90	garis_lurus	48	34385000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
218	3	140	2026-09	610833.33	18324999.90	10995000.10	garis_lurus	48	29320000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
219	3	25	2026-09	178854.17	6975312.63	1609687.37	garis_lurus	48	8585000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
220	3	76	2026-09	97895.83	3915833.20	783166.80	garis_lurus	48	4699000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
221	3	12	2026-09	56479.17	2936916.84	2485083.16	garis_lurus	96	5422000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
222	3	92	2026-09	196395.83	7855833.20	10998166.80	garis_lurus	96	18854000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
223	3	58	2026-09	557791.67	2788958.35	23985041.65	garis_lurus	48	26774000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
224	3	145	2026-09	787645.83	30718187.37	7088812.63	garis_lurus	48	37807000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
225	3	33	2026-09	760958.33	22067791.57	14458208.43	garis_lurus	48	36526000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
226	3	59	2026-09	796354.17	13538020.89	24686979.11	garis_lurus	48	38225000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
227	3	61	2026-09	103854.17	4154166.80	830833.20	garis_lurus	48	4985000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
228	3	44	2026-09	118395.83	2367916.60	3315083.40	garis_lurus	48	5683000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
229	3	114	2026-09	81354.17	7565937.81	244062.19	garis_lurus	96	7810000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
230	3	5	2026-09	72468.75	4420593.75	2536406.25	garis_lurus	96	6957000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
231	3	21	2026-09	21072.92	526823.00	1496177.00	garis_lurus	96	2023000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
232	3	149	2026-09	268385.42	6709635.50	19055364.50	garis_lurus	96	25765000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
233	3	100	2026-09	101437.50	1115812.50	8622187.50	garis_lurus	96	9738000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
234	3	124	2026-09	4846822.92	378052187.76	87242812.24	garis_lurus	96	465295000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
235	3	97	2026-09	2045260.42	124760885.62	71584114.38	garis_lurus	96	196345000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
236	3	6	2026-09	716552.08	39410364.40	29378635.60	garis_lurus	96	68789000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
237	3	67	2026-09	2939625.00	111705750.00	170498250.00	garis_lurus	96	282204000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
238	3	18	2026-09	412312.50	4535437.50	15255562.50	garis_lurus	48	19791000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
239	3	106	2026-09	204708.33	1432958.31	8393041.69	garis_lurus	48	9826000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
240	3	125	2026-09	273958.33	273958.33	12876041.67	garis_lurus	48	13150000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
241	3	20	2026-09	487291.67	14131458.43	9258541.57	garis_lurus	48	23390000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
242	3	27	2026-09	1089187.50	16337812.50	35943187.50	garis_lurus	48	52281000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
243	3	132	2026-09	164437.50	164437.50	7728562.50	garis_lurus	48	7893000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
244	3	3	2026-09	5333333.33	538666666.33	101333333.67	garis_lurus	120	640000000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
245	3	126	2026-09	2033927.08	91526718.60	103730281.40	garis_lurus	96	195257000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
246	3	42	2026-09	2558593.75	53730468.75	191894531.25	garis_lurus	96	245625000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
247	3	24	2026-09	3326447.92	292727416.96	26611583.04	garis_lurus	96	319339000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
248	3	35	2026-09	4039697.92	206024593.92	181786406.08	garis_lurus	96	387811000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
249	3	37	2026-09	3042666.67	167346666.85	124749333.15	garis_lurus	96	292096000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
250	3	137	2026-09	4592552.08	215849947.76	225035052.24	garis_lurus	96	440885000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
251	3	103	2026-09	4195979.17	188819062.65	213994937.35	garis_lurus	96	402814000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
252	3	66	2026-09	3925187.50	51027437.50	325790562.50	garis_lurus	96	376818000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
253	3	43	2026-09	577791.67	26000625.15	1733374.85	garis_lurus	48	27734000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
254	3	93	2026-09	523958.33	19910416.54	5239583.46	garis_lurus	48	25150000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
255	3	136	2026-09	445416.67	15589583.45	5790416.55	garis_lurus	48	21380000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
256	3	151	2026-09	651416.67	8468416.71	22799583.29	garis_lurus	48	31268000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
257	3	123	2026-09	214937.50	9242312.50	1074687.50	garis_lurus	48	10317000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
258	3	65	2026-09	270895.83	4063437.45	8939562.55	garis_lurus	48	13003000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
259	3	154	2026-09	126562.50	1518750.00	4556250.00	garis_lurus	48	6075000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
260	3	99	2026-09	190500.00	1524000.00	7620000.00	garis_lurus	48	9144000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
261	3	7	2026-09	207208.33	5387416.58	14504583.42	garis_lurus	96	19892000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
262	3	68	2026-09	114781.25	3328656.25	7690343.75	garis_lurus	96	11019000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
263	3	116	2026-09	691937.50	2075812.50	31137187.50	garis_lurus	48	33213000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
264	3	30	2026-09	66354.17	265416.68	2919583.32	garis_lurus	48	3185000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
265	3	17	2026-09	56125.00	1066375.00	1627625.00	garis_lurus	48	2694000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
266	3	47	2026-09	243937.50	5610562.50	17807437.50	garis_lurus	96	23418000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
267	3	89	2026-09	163906.25	3114218.75	12620781.25	garis_lurus	96	15735000.00	0.00	1	\N	2026-09-07 05:11:20	2026-09-07 05:11:20
\.


--
-- Data for Name: depreciation_periods; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.depreciation_periods (id, period, closed_at, closed_by_user_id, total_expense, asset_count, catch_up_count, notes, created_at, updated_at) FROM stdin;
3	2026-09	2026-09-07 05:11:20	2	191070562.52	89	0	Penutupan September 2026, diverifikasi lewat peramban.	2026-09-07 05:11:20	2026-09-07 05:11:20
\.


--
-- Data for Name: employees; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.employees (id, nip, full_name, email, phone, department_id, "position", employment_status, join_date, user_id, is_active, created_at, updated_at) FROM stdin;
2	DEMO-001	Andi Prasetyo	\N	\N	4	Staf General Affair	tetap	2018-07-30	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
3	DEMO-002	Siti Rahmawati	\N	\N	4	Admin Aset	tetap	2016-08-08	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
4	DEMO-003	Budi Santoso	\N	\N	4	Manajer General Affair	tetap	2020-05-27	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
5	DEMO-004	Dewi Anggraini	\N	\N	5	Staf Akuntansi	tetap	2014-03-25	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
6	DEMO-005	Rizal Hakim	\N	\N	5	Manajer Keuangan	tetap	2020-06-04	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
7	DEMO-006	Putri Maharani	\N	\N	6	Staf Dukungan Teknis	tetap	2019-03-06	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
8	DEMO-007	Agus Setiawan	\N	\N	6	Administrator Jaringan	tetap	2019-07-06	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
9	DEMO-008	Lestari Ningsih	\N	\N	7	Staf Kepegawaian	tetap	2018-01-24	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
10	DEMO-009	Hendra Wijaya	\N	\N	7	Manajer SDM	tetap	2022-03-16	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
11	DEMO-010	Maya Kusuma	\N	\N	8	Staf Operasional	tetap	2021-07-07	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
12	DEMO-011	Fajar Nugroho	\N	\N	8	Pengemudi	tetap	2017-12-31	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
13	DEMO-012	Ratna Sari	\N	\N	8	Staf Umum	tetap	2022-07-23	\N	t	2026-09-06 14:04:04	2026-09-06 14:04:04
1	CONTOH-0001	CONTOH Staf GA	\N	\N	1	Staf General Affair	tetap	\N	2	t	2026-09-06 11:55:29	2026-09-08 13:57:28
\.


--
-- Data for Name: expense_categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.expense_categories (id, code, name, description, source, account_code, is_active, created_at, updated_at) FROM stdin;
1	PMLH	Pemeliharaan gedung dan peralatan	Servis, suku cadang, dan jasa tukang. Dijumlahkan dari perintah kerja dan kunjungan pemeliharaan yang sudah selesai.	pemeliharaan	\N	t	2026-09-07 11:54:39	2026-09-07 11:54:39
2	BBM	Bahan bakar kendaraan	Dijumlahkan dari pengisian BBM yang dicatat pada tiap kendaraan.	bbm	\N	t	2026-09-07 11:54:39	2026-09-07 11:54:39
3	DOKKEN	Pajak dan dokumen kendaraan	Pajak tahunan, perpanjangan STNK, KIR, dan asuransi. Dibebankan menurut tanggal terbit dokumennya.	dokumen_kendaraan	\N	t	2026-09-07 11:54:39	2026-09-07 11:54:39
4	ATK	Alat tulis dan barang habis pakai	Dijumlahkan dari barang yang keluar gudang, dinilai dengan harga pada mutasinya atau harga pembelian terakhir.	persediaan	\N	t	2026-09-07 11:54:39	2026-09-07 11:54:39
10	LAIN	Biaya GA lainnya	Biaya yang belum masuk kategori mana pun.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 11:54:39
5	LSTR	Listrik, air, dan telepon	Tagihan utilitas bulanan, dijumlahkan dari faktur rekanan yang sudah disetujui.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 12:33:34
6	KBRS	Kebersihan dan keamanan	Jasa cleaning service dan satpam, dijumlahkan dari faktur rekanan yang sudah disetujui.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 12:33:34
7	SEWA	Sewa gedung dan peralatan	Sewa ruang, mesin fotokopi, dan peralatan lain, dijumlahkan dari faktur rekanan yang sudah disetujui.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 12:33:35
8	RMTG	Rumah tangga kantor	Konsumsi rapat, galon, dan keperluan harian kantor, dijumlahkan dari faktur rekanan yang sudah disetujui.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 12:33:35
9	TRNS	Transportasi dan perjalanan	Transportasi daring, taksi, dan tol. Dijumlahkan dari faktur rekanan dan dari struk penggantian biaya karyawan yang sudah disetujui.	tagihan	\N	t	2026-09-07 11:54:39	2026-09-07 13:33:14
11	KRIM	Pengiriman surat dan paket	Ongkos kirim, asuransi, dan pengemasan paket keluar. Dijumlahkan dari pengiriman yang paketnya sudah berangkat, menurut tanggal kirimnya.	kiriman	\N	t	2026-09-08 14:48:38	2026-09-08 14:48:38
12	SPPD	Perjalanan dinas	Transportasi, penginapan, uang harian, dan konsumsi selama perjalanan dinas. Dijumlahkan dari rincian pertanggungjawaban yang sudah ditutup, menurut tanggal tiap pengeluarannya.	perjalanan_dinas	\N	t	2026-09-08 14:48:38	2026-09-08 14:48:38
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: incident_reports; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.incident_reports (id, code, occurred_at, category, severity, location_id, security_shift_id, reported_by_staff_id, description, service_request_id, closed_at, closing_note, closed_by_user_id, created_by_user_id, created_at, updated_at) FROM stdin;
1	LI/2026/09/0001	2026-09-08 13:54:00	kerusakan	tinggi	21	1	2	Pagar besi sisi timur bengkok dan gemboknya patah. Diduga ditabrak kendaraan saat parkir mundur. Area sudah dipasangi garis pembatas sementara.	3	2026-09-08 13:59:33	Pagar sudah dipagari sementara dan perbaikannya berjalan lewat PB/2026/09/0003. Rekaman kamera parkir diperiksa, tidak ada nomor polisi yang terbaca jelas.	2	2	2026-09-08 13:55:51	2026-09-08 13:59:33
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: letters; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.letters (id, direction, code, letter_number, letter_date, logged_date, category, counterparty, subject, notes, assigned_employee_id, handed_over_at, handed_over_to_employee_id, handover_note, signer_employee_id, file_path, original_name, created_by_user_id, created_at, updated_at) FROM stdin;
1	masuk	AM/2026/09/0001	800/1245/DISNAKER/IX/2026	2026-09-03	2026-09-08	lainnya	Dinas Tenaga Kerja Kota Yogyakarta	Undangan sosialisasi wajib lapor ketenagakerjaan	\N	9	2026-09-08 15:31:00	10	Diterima Hendra Wijaya karena Lestari Ningsih sedang cuti.	\N	\N	\N	2	2026-09-08 15:30:57	2026-09-08 15:31:26
2	keluar	AK/2026/09/0001	012/GA-GTI/IX/2026	2026-09-08	2026-09-08	lainnya	PT Sinar Abadi Sentosa	Permintaan penawaran jasa kebersihan tahun 2027	\N	\N	\N	\N	\N	2	\N	\N	2	2026-09-08 15:32:25	2026-09-08 15:32:25
\.


--
-- Data for Name: locations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.locations (id, code, name, type, parent_id, description, is_active, created_at, updated_at) FROM stdin;
1	CONTOH-HO	CONTOH Head Office	gedung	\N	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29
2	CONTOH-HO-L1	CONTOH Lantai 1	lantai	1	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29
3	CONTOH-HO-L1-R01	CONTOH Ruang Rapat	ruangan	2	\N	t	2026-09-06 11:55:29	2026-09-06 11:55:29
4	HO	Head Office	gedung	\N	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
5	HO-L1	Lantai 1	lantai	4	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
6	HO-L1-R01	Lobi utama	ruangan	5	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
7	HO-L1-R02	Ruang resepsionis	ruangan	5	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
8	HO-L1-R03	Ruang tunggu tamu	ruangan	5	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
9	HO-L1-R04	Ruang arsip	ruangan	5	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
10	HO-L2	Lantai 2	lantai	4	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
11	HO-L2-R01	Ruang Finance	ruangan	10	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
12	HO-L2-R02	Ruang HRD	ruangan	10	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
13	HO-L2-R03	Ruang rapat kecil	ruangan	10	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
14	HO-L2-R04	Pantry lantai 2	ruangan	10	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
15	HO-L3	Lantai 3	lantai	4	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
16	HO-L3-R01	Ruang IT	ruangan	15	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
17	HO-L3-R02	Ruang server	ruangan	15	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
18	HO-L3-R03	Ruang rapat besar	ruangan	15	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
19	HO-L3-R04	Ruang General Affair	ruangan	15	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
20	HO-GDG	Gudang belakang	area	4	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
21	HO-PRK	Area parkir kendaraan dinas	area	4	[DATA DEMO]	t	2026-09-06 14:04:04	2026-09-06 14:04:04
\.


--
-- Data for Name: maintenance_schedules; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.maintenance_schedules (id, asset_id, name, tasks, interval_months, last_done_date, next_due_date, vendor_id, technician_employee_id, estimated_cost, is_active, notes, created_at, updated_at) FROM stdin;
1	2	[DATA UJI] Servis rutin dan cuci AC	Cuci filter dan evaporator\nPeriksa tekanan freon	3	2026-09-07	2026-12-07	1	\N	\N	t	\N	2026-09-07 05:12:22	2026-09-07 05:16:55
\.


--
-- Data for Name: maintenance_visits; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.maintenance_visits (id, maintenance_schedule_id, asset_id, sequence, due_date, status, completed_date, vendor_id, technician_employee_id, result, cost, skip_reason, closed_by_user_id, created_at, updated_at) FROM stdin;
1	1	2	1	2026-09-10	dikerjakan	2026-09-05	1	\N	Filter dan evaporator dicuci, freon ditambah 0,3 kg, tidak ada kebocoran.	850000.00	\N	2	2026-09-07 05:12:22	2026-09-07 05:13:06
2	1	2	2	2026-12-05	dilewati	\N	1	\N	\N	\N	Unit sedang tidak dipakai karena ruangannya direnovasi.	2	2026-09-07 05:13:06	2026-09-07 05:13:27
3	1	2	3	2027-03-05	dikerjakan	2026-09-07	1	\N	Servis lengkap, filter diganti, kompresor diperiksa.	1250000.00	\N	2	2026-09-07 05:13:27	2026-09-07 05:16:55
4	1	2	4	2026-12-07	dijadwalkan	\N	1	\N	\N	\N	\N	\N	2026-09-07 05:16:55	2026-09-07 05:16:55
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_09_06_100000_add_gais_columns_to_users_table	1
5	2026_09_06_100100_create_departments_table	1
6	2026_09_06_100200_create_locations_table	1
7	2026_09_06_100300_create_employees_table	1
8	2026_09_06_100400_add_head_employee_to_departments_table	1
9	2026_09_06_100500_create_modules_table	1
10	2026_09_06_100600_create_permissions_table	1
11	2026_09_06_100700_create_roles_table	1
12	2026_09_06_100800_create_permission_role_table	1
13	2026_09_06_100900_create_role_user_table	1
14	2026_09_06_101000_create_permission_user_table	1
15	2026_09_06_101100_create_audit_logs_table	1
16	2026_09_06_101200_create_settings_table	1
17	2026_09_06_101300_create_number_sequences_table	1
18	2026_09_06_200000_restructure_number_sequences_table	2
19	2026_09_06_200100_create_asset_categories_table	2
20	2026_09_06_200200_create_assets_table	2
21	2026_09_06_210000_ubah_format_kode_aset	3
22	2026_09_06_220000_create_stock_opnames_table	4
23	2026_09_06_220100_create_stock_opname_lines_table	4
24	2026_09_06_230000_create_supply_items_table	5
25	2026_09_06_230100_create_supply_transactions_table	5
26	2026_09_06_240000_create_asset_transfers_table	6
27	2026_09_06_240100_create_asset_disposals_table	6
28	2026_09_07_100000_add_ownership_and_warranty_to_assets_table	7
29	2026_09_07_100100_add_requires_certificate_to_asset_categories_table	7
30	2026_09_07_100200_create_asset_documents_table	7
31	2026_09_07_200000_create_depreciation_periods_table	8
32	2026_09_07_200100_create_depreciation_entries_table	8
33	2026_09_07_200200_add_opening_depreciation_to_assets_table	9
34	2026_09_07_200300_widen_settings_description	10
35	2026_09_07_210000_create_vendors_table	11
36	2026_09_07_210100_create_maintenance_schedules_table	11
37	2026_09_07_210200_create_work_orders_table	11
38	2026_09_07_210300_create_work_order_attachments_table	11
39	2026_09_07_220000_create_maintenance_visits_table	12
40	2026_09_07_220100_seed_first_maintenance_visits	12
41	2026_09_07_230000_create_service_request_categories_table	13
42	2026_09_07_230100_create_service_requests_table	13
43	2026_09_07_230200_create_service_request_attachments_table	13
44	2026_09_07_230300_make_work_order_asset_nullable	14
45	2026_09_07_240000_create_vehicles_table	15
46	2026_09_07_240100_create_vehicle_documents_table	15
47	2026_09_07_240200_create_vehicle_photos_table	16
48	2026_09_07_250000_create_vehicle_bookings_table	17
49	2026_09_07_250100_create_vehicle_trips_table	17
50	2026_09_07_250200_create_vehicle_refuelings_table	17
51	2026_09_07_260000_create_expense_categories_table	18
52	2026_09_07_260100_create_budgets_table	18
53	2026_09_07_270000_create_vendor_bills_table	19
54	2026_09_07_270100_create_vendor_bill_lines_table	19
55	2026_09_07_270200_rename_manual_expense_category_source	19
56	2026_09_07_280000_create_reimbursements_table	20
57	2026_09_07_280100_create_reimbursement_lines_table	20
58	2026_09_08_290000_create_supply_requests_table	21
59	2026_09_08_290100_create_supply_request_lines_table	21
60	2026_09_08_300000_create_supply_purchases_table	22
61	2026_09_08_300100_create_supply_purchase_lines_table	22
62	2026_09_08_300200_create_supply_receipts_table	22
63	2026_09_08_300300_create_supply_receipt_lines_table	22
64	2026_09_08_300400_add_supply_purchase_to_vendor_bills	22
65	2026_09_08_310000_create_supply_opnames_table	23
66	2026_09_08_310100_create_supply_opname_lines_table	23
67	2026_09_08_320000_create_service_staff_table	24
68	2026_09_08_320100_create_service_areas_table	24
69	2026_09_08_320200_create_cleaning_inspections_table	24
70	2026_09_08_320300_create_cleaning_inspection_lines_table	24
71	2026_09_08_330000_create_security_shifts_table	25
72	2026_09_08_330100_create_incident_reports_table	25
73	2026_09_08_340000_create_letters_table	26
74	2026_09_08_350000_create_parcel_shipments_table	26
75	2026_09_08_360000_create_business_trips_table	26
76	2026_09_08_360100_create_business_trip_expenses_table	26
77	2026_09_08_360200_create_business_trip_participants_table	27
\.


--
-- Data for Name: modules; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.modules (id, code, name, description, "group", icon, sort, available_actions, is_active, created_at, updated_at) FROM stdin;
1	roles	Roles	Membuat role dan menentukan modul serta aksi yang boleh diaksesnya.	Access Control	heroicon-o-shield-check	10	["read","create","update","delete"]	t	2026-09-06 09:04:36	2026-09-08 05:48:04
2	users	Users	Akun yang bisa masuk ke aplikasi beserta role dan izin khususnya.	Access Control	heroicon-o-user-circle	20	["read","create","update","delete"]	t	2026-09-06 09:04:36	2026-09-08 05:48:04
3	modules	Modules	Registri modul yang menjadi sumber daftar izin.	Access Control	heroicon-o-squares-2x2	30	["read","update"]	t	2026-09-06 09:04:36	2026-09-08 05:48:04
9	asset_categories	Asset Categories	Kelompok aset beserta awalan kode, umur ekonomis, dan metode penyusutannya.	Assets	heroicon-o-rectangle-group	10	["read","create","update","delete"]	t	2026-09-06 12:53:24	2026-09-08 05:48:04
10	assets	Assets	Pendataan aset tetap dan aset bergerak, lokasi, penanggung jawab, status, dan kondisinya.	Assets	heroicon-o-cube	20	["read","create","update","delete","print"]	t	2026-09-06 12:53:24	2026-09-08 05:48:05
11	stock_opnames	Stock Opname	Sesi pemeriksaan fisik aset, pencatatan temuan, dan penyesuaian data.	Assets	heroicon-o-clipboard-document-check	30	["read","create","update","delete","approve"]	t	2026-09-06 15:17:20	2026-09-08 05:48:05
14	asset_transfers	Asset Transfers	Dokumen perpindahan aset antar ruangan, penanggung jawab, dan departemen.	Assets	heroicon-o-arrow-right-start-on-rectangle	40	["read","create","delete"]	t	2026-09-06 23:01:27	2026-09-08 05:48:05
15	asset_disposals	Asset Disposals	Dokumen penjualan, hibah, pemusnahan, dan kehilangan aset beserta dasarnya.	Assets	heroicon-o-archive-box-x-mark	50	["read","create","update","delete"]	t	2026-09-06 23:01:27	2026-09-08 05:48:05
12	supply_items	Supply Items	Daftar ATK dan perlengkapan habis pakai beserta stok dan batas pemesanan ulangnya.	Office Supplies	heroicon-o-archive-box	10	["read","create","update","delete"]	t	2026-09-06 21:19:20	2026-09-08 05:48:05
13	supply_transactions	Supply Movements	Barang masuk, barang keluar, dan koreksi stok. Daftar ini yang menjadi dasar perhitungan stok.	Office Supplies	heroicon-o-arrows-right-left	20	["read","create","update","delete"]	t	2026-09-06 21:19:20	2026-09-08 05:48:05
28	supply_requests	Supply Requests	Permintaan pemakaian ATK oleh karyawan, persetujuan atasan, dan penyerahan barang oleh tim GA. Penyerahannya yang melahirkan mutasi barang keluar.	Office Supplies	heroicon-o-clipboard-document-list	30	["read","read_all","create","update","delete","approve","issue","request_for_others"]	t	2026-09-08 05:48:05	2026-09-08 05:48:05
4	departments	Departments	Struktur departemen dan cost center untuk pembebanan biaya.	Master Data	heroicon-o-building-office-2	10	["read","create","update","delete"]	t	2026-09-06 09:04:36	2026-09-08 05:48:05
5	locations	Locations	Gedung, lantai, ruangan, dan area di Head Office.	Master Data	heroicon-o-map-pin	20	["read","create","update","delete"]	t	2026-09-06 09:04:36	2026-09-08 05:48:05
6	employees	Employees	Data karyawan yang dipakai sebagai penanggung jawab dan pengaju permintaan.	Master Data	heroicon-o-identification	30	["read","create","update","delete"]	t	2026-09-06 09:04:36	2026-09-08 05:48:05
16	depreciation_periods	Depreciation	Penutupan penyusutan bulanan dan beban per aset.	Assets	heroicon-o-arrow-trending-down	60	["read","close","reopen"]	t	2026-09-07 02:35:17	2026-09-08 05:48:05
17	vendors	Vendors	Tukang servis, bengkel, dan pemasok yang mengerjakan pemeliharaan.	Master Data	heroicon-o-building-storefront	40	["read","create","update","delete"]	t	2026-09-07 03:19:20	2026-09-08 05:48:05
21	service_request_categories	Request Types	Kelompok permintaan perbaikan beserta prioritas bawaan dan target waktu penyelesaiannya.	Master Data	heroicon-o-tag	50	["read","create","update","delete"]	t	2026-09-07 05:52:52	2026-09-08 05:48:05
19	work_orders	Work Orders	Pekerjaan pemeliharaan preventif dan korektif beserta biayanya.	Maintenance	heroicon-o-wrench-screwdriver	20	["read","create","update","delete"]	t	2026-09-07 03:19:20	2026-09-08 05:48:05
22	vehicles	Vehicles	Data kendaraan yang menempel pada aset, beserta pajak, STNK, KIR, dan asuransinya.	Vehicles	heroicon-o-truck	10	["read","create","update","delete"]	t	2026-09-07 10:24:00	2026-09-08 05:48:06
23	vehicle_bookings	Vehicle Bookings	Pemesanan pool car, persetujuan atasan, dan penugasan kendaraan beserta sopirnya.	Vehicles	heroicon-o-calendar-days	20	["read","read_all","create","update","delete","approve","assign"]	t	2026-09-07 11:20:58	2026-09-08 05:48:06
24	expense_categories	Expense Categories	Kelompok biaya GA beserta sumber realisasinya dan pemetaan ke akun perusahaan.	Master Data	heroicon-o-banknotes	60	["read","create","update","delete"]	t	2026-09-07 11:54:38	2026-09-08 05:48:06
25	budgets	Budgets	Pagu per departemen per kategori per tahun, dibandingkan dengan realisasi yang dijumlahkan sendiri dari catatan yang sudah ada.	Budget & Expenses	heroicon-o-calculator	10	["read","create","update","delete"]	t	2026-09-07 11:54:38	2026-09-08 05:48:06
26	vendor_bills	Vendor Bills	Faktur dari rekanan, pembebanannya ke departemen dan kategori biaya, persetujuan, dan penandaan pembayaran.	Budget & Expenses	heroicon-o-document-currency-dollar	20	["read","create","update","delete","approve","pay"]	t	2026-09-07 12:33:30	2026-09-08 05:48:06
27	reimbursements	Reimbursements	Pengajuan penggantian biaya karyawan beserta struknya, persetujuan atasan, pemeriksaan tim GA, dan penandaan transfer.	Budget & Expenses	heroicon-o-receipt-percent	30	["read","read_all","create","update","delete","approve","verify","pay"]	t	2026-09-07 13:33:11	2026-09-08 05:48:06
7	settings	Settings	Identitas perusahaan dan pengaturan sistem lainnya.	System	heroicon-o-adjustments-horizontal	10	["read","update"]	t	2026-09-06 09:04:36	2026-09-08 05:48:06
8	audit_logs	Audit Log	Riwayat penambahan, perubahan, dan penghapusan data.	System	heroicon-o-clipboard-document-list	20	["read"]	t	2026-09-06 09:04:36	2026-09-08 05:48:06
29	supply_purchases	Supply Purchases	Pesanan pembelian ATK ke pemasok dan pembelian langsung, beserta penerimaan barangnya. Penerimaannya yang melahirkan mutasi barang masuk.	Office Supplies	heroicon-o-shopping-cart	40	["read","create","update","delete","approve","receive"]	t	2026-09-08 10:02:59	2026-09-08 10:02:59
30	supply_opnames	Supply Opname	Penghitungan fisik barang habis pakai dan penyesuaian stok dari hasilnya. Tiap selisih lahir sebagai mutasi koreksi, bukan mengubah angka diam diam.	Office Supplies	heroicon-o-clipboard-document-check	50	["read","create","update","delete","adjust"]	t	2026-09-08 11:27:33	2026-09-08 11:27:33
31	service_staff	Service Staff	Petugas kebersihan dan keamanan, karyawan perusahaan maupun tenaga dari rekanan penyedia.	Master Data	heroicon-o-identification	70	["read","create","update","delete"]	t	2026-09-08 12:28:01	2026-09-08 12:28:01
18	maintenance_schedules	Preventive Maintenance	Pekerjaan preventif yang berulang beserta jatuh temponya.	Maintenance	heroicon-o-calendar-days	10	["read","create","update","delete"]	t	2026-09-07 03:19:20	2026-09-08 13:44:05
32	service_areas	Service Areas	Area yang dibersihkan dan diperiksa, beserta seberapa sering dan siapa penanggung jawabnya.	Master Data	heroicon-o-sparkles	80	["read","create","update","delete"]	t	2026-09-08 12:28:01	2026-09-08 12:28:01
33	cleaning_inspections	Cleaning Inspections	Putaran pemeriksaan kebersihan oleh pengawas GA. Yang tercatat adalah hasil pemeriksaan, bukan laporan petugas.	Facility Services	heroicon-o-clipboard-document-list	10	["read","create","update","delete"]	t	2026-09-08 12:28:01	2026-09-08 12:28:01
34	security_shifts	Security Shifts	Jadwal jaga keamanan beserta kehadirannya. Rencana dan kenyataan disimpan berdampingan, tidak saling menimpa.	Facility Services	heroicon-o-shield-exclamation	20	["read","create","update","delete"]	t	2026-09-08 13:33:14	2026-09-08 13:33:14
35	incident_reports	Incident Reports	Buku kejadian keamanan. Insiden yang butuh perbaikan fisik diteruskan menjadi tiket perbaikan.	Facility Services	heroicon-o-exclamation-triangle	30	["read","create","update","delete"]	t	2026-09-08 13:33:14	2026-09-08 13:33:14
20	service_requests	Corrective Maintenance	Tiket kerusakan dari karyawan, persetujuan atasannya, dan penerimaannya oleh tim GA.	Maintenance	heroicon-o-lifebuoy	5	["read","read_all","create","update","delete","approve","accept"]	t	2026-09-07 05:52:52	2026-09-08 13:44:05
36	letters	Letters	Buku agenda surat masuk dan surat keluar, beserta serah terima surat masuk ke orang yang dituju.	Correspondence	heroicon-o-envelope	10	["read","create","update","delete"]	t	2026-09-08 14:48:32	2026-09-08 14:48:32
37	parcel_shipments	Parcel Shipments	Permintaan kirim paket keluar dan biaya sebenarnya dari resi ekspedisi. Biayanya memotong pagu anggaran departemen yang dibebani.	Correspondence	heroicon-o-inbox-stack	20	["read","create","update","delete"]	t	2026-09-08 14:48:32	2026-09-08 14:48:32
38	business_trips	Business Trips	Perjalanan dinas beserta uang muka dan pertanggungjawabannya. Biaya yang sudah ditutup memotong pagu anggaran departemen yang dibebani.	Vehicles	heroicon-o-map	30	["read","read_all","create","update","delete","approve","pay","verify"]	t	2026-09-08 14:48:32	2026-09-08 14:48:32
\.


--
-- Data for Name: number_sequence_periods; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.number_sequence_periods (id, number_sequence_id, period, next_number, created_at, updated_at) FROM stdin;
1	1	2017	2	2026-09-06 14:04:04	2026-09-06 14:04:04
2	2	2012	2	2026-09-06 14:04:04	2026-09-06 14:04:04
3	3	2018	2	2026-09-06 14:04:04	2026-09-06 14:04:04
4	4	2014	2	2026-09-06 14:04:04	2026-09-06 14:04:04
5	5	2021	2	2026-09-06 14:04:04	2026-09-06 14:04:04
6	6	2022	2	2026-09-06 14:04:04	2026-09-06 14:04:04
74	10	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
110	35	2024	3	2026-09-06 14:04:05	2026-09-06 14:04:06
9	9	2020	2	2026-09-06 14:04:04	2026-09-06 14:04:04
10	10	2019	2	2026-09-06 14:04:04	2026-09-06 14:04:04
11	11	2021	2	2026-09-06 14:04:04	2026-09-06 14:04:04
12	12	2022	2	2026-09-06 14:04:04	2026-09-06 14:04:04
41	27	2018	3	2026-09-06 14:04:05	2026-09-06 14:04:06
99	9	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
15	14	2018	2	2026-09-06 14:04:04	2026-09-06 14:04:04
16	15	2022	2	2026-09-06 14:04:04	2026-09-06 14:04:04
17	16	2025	2	2026-09-06 14:04:04	2026-09-06 14:04:04
18	17	2025	2	2026-09-06 14:04:04	2026-09-06 14:04:04
19	18	2019	2	2026-09-06 14:04:04	2026-09-06 14:04:04
20	19	2024	2	2026-09-06 14:04:04	2026-09-06 14:04:04
13	13	2026	3	2026-09-06 14:04:04	2026-09-06 14:04:06
22	20	2023	2	2026-09-06 14:04:04	2026-09-06 14:04:04
23	21	2023	2	2026-09-06 14:04:04	2026-09-06 14:04:04
24	22	2019	2	2026-09-06 14:04:04	2026-09-06 14:04:04
87	37	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
26	8	2023	2	2026-09-06 14:04:04	2026-09-06 14:04:04
27	24	2025	2	2026-09-06 14:04:04	2026-09-06 14:04:04
28	25	2018	2	2026-09-06 14:04:04	2026-09-06 14:04:04
29	23	2019	2	2026-09-06 14:04:04	2026-09-06 14:04:04
30	26	2026	2	2026-09-06 14:04:04	2026-09-06 14:04:04
31	27	2019	2	2026-09-06 14:04:04	2026-09-06 14:04:04
32	16	2022	2	2026-09-06 14:04:04	2026-09-06 14:04:04
33	28	2024	2	2026-09-06 14:04:04	2026-09-06 14:04:04
50	10	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
8	8	2018	3	2026-09-06 14:04:04	2026-09-06 14:04:06
38	29	2022	2	2026-09-06 14:04:05	2026-09-06 14:04:05
39	30	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
40	31	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
65	9	2025	3	2026-09-06 14:04:05	2026-09-06 14:04:06
42	32	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
94	40	2022	2	2026-09-06 14:04:05	2026-09-06 14:04:05
44	33	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
45	10	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
46	31	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
47	34	2024	2	2026-09-06 14:04:05	2026-09-06 14:04:05
48	35	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
88	42	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
51	27	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
52	37	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
53	20	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
91	25	2020	3	2026-09-06 14:04:05	2026-09-06 14:04:06
56	18	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
57	19	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
58	14	2026	2	2026-09-06 14:04:05	2026-09-06 14:04:05
59	28	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
60	38	2022	2	2026-09-06 14:04:05	2026-09-06 14:04:05
61	33	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
62	30	2019	2	2026-09-06 14:04:05	2026-09-06 14:04:05
64	17	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
66	22	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
67	39	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
7	7	2024	3	2026-09-06 14:04:04	2026-09-06 14:04:05
69	40	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
100	5	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
71	11	2022	2	2026-09-06 14:04:05	2026-09-06 14:04:05
72	20	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
73	41	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
75	21	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
25	23	2023	3	2026-09-06 14:04:04	2026-09-06 14:04:05
49	36	2026	3	2026-09-06 14:04:05	2026-09-06 14:04:05
63	8	2021	3	2026-09-06 14:04:05	2026-09-06 14:04:05
34	20	2018	3	2026-09-06 14:04:04	2026-09-06 14:04:05
80	9	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
81	21	2024	2	2026-09-06 14:04:05	2026-09-06 14:04:05
82	11	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
83	7	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
84	15	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
85	23	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
86	14	2022	2	2026-09-06 14:04:05	2026-09-06 14:04:05
89	34	2025	2	2026-09-06 14:04:05	2026-09-06 14:04:05
90	31	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
43	31	2023	4	2026-09-06 14:04:05	2026-09-06 14:04:06
92	12	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
35	22	2022	4	2026-09-06 14:04:04	2026-09-06 14:04:06
14	12	2018	3	2026-09-06 14:04:04	2026-09-06 14:04:05
36	15	2023	3	2026-09-06 14:04:04	2026-09-06 14:04:05
97	6	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
70	36	2018	3	2026-09-06 14:04:05	2026-09-06 14:04:05
106	40	2026	3	2026-09-06 14:04:05	2026-09-06 14:04:06
102	35	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
103	22	2023	2	2026-09-06 14:04:05	2026-09-06 14:04:05
104	14	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
105	16	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
54	8	2024	4	2026-09-06 14:04:05	2026-09-06 14:04:06
107	25	2021	2	2026-09-06 14:04:05	2026-09-06 14:04:05
108	33	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
109	30	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
21	5	2024	3	2026-09-06 14:04:04	2026-09-06 14:04:06
111	43	2019	2	2026-09-06 14:04:05	2026-09-06 14:04:05
112	41	2018	2	2026-09-06 14:04:05	2026-09-06 14:04:05
101	13	2021	3	2026-09-06 14:04:05	2026-09-06 14:04:05
113	35	2020	2	2026-09-06 14:04:05	2026-09-06 14:04:05
114	5	2019	2	2026-09-06 14:04:05	2026-09-06 14:04:05
116	30	2026	2	2026-09-06 14:04:06	2026-09-06 14:04:06
117	10	2018	2	2026-09-06 14:04:06	2026-09-06 14:04:06
119	25	2022	2	2026-09-06 14:04:06	2026-09-06 14:04:06
120	40	2018	2	2026-09-06 14:04:06	2026-09-06 14:04:06
121	21	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
122	44	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
123	9	2023	2	2026-09-06 14:04:06	2026-09-06 14:04:06
124	6	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
126	32	2023	2	2026-09-06 14:04:06	2026-09-06 14:04:06
127	21	2026	2	2026-09-06 14:04:06	2026-09-06 14:04:06
129	18	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
130	15	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
131	13	2025	2	2026-09-06 14:04:06	2026-09-06 14:04:06
132	43	2026	2	2026-09-06 14:04:06	2026-09-06 14:04:06
134	33	2022	2	2026-09-06 14:04:06	2026-09-06 14:04:06
135	44	2023	2	2026-09-06 14:04:06	2026-09-06 14:04:06
138	27	2024	2	2026-09-06 14:04:06	2026-09-06 14:04:06
141	37	2022	2	2026-09-06 14:04:06	2026-09-06 14:04:06
142	14	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
143	36	2024	2	2026-09-06 14:04:06	2026-09-06 14:04:06
144	36	2020	2	2026-09-06 14:04:06	2026-09-06 14:04:06
145	28	2023	2	2026-09-06 14:04:06	2026-09-06 14:04:06
118	28	2018	3	2026-09-06 14:04:06	2026-09-06 14:04:06
147	38	2023	2	2026-09-06 14:04:06	2026-09-06 14:04:06
148	45	2018	2	2026-09-06 14:04:06	2026-09-06 14:04:06
151	31	2025	2	2026-09-06 14:04:06	2026-09-06 14:04:06
153	16	2019	2	2026-09-06 14:04:06	2026-09-06 14:04:06
355	56	2026/09	4	2026-09-07 12:37:59	2026-09-08 10:14:06
369	60	2026/09	4	2026-09-08 10:09:58	2026-09-08 10:20:59
368	59	2026/09	4	2026-09-08 10:04:46	2026-09-08 10:23:16
159	50	2026/09	173	2026-09-06 21:23:53	2026-09-08 11:49:57
381	61	2026/09	5	2026-09-08 11:28:41	2026-09-08 12:22:52
158	49		28	2026-09-06 21:23:53	2026-09-06 22:02:28
393	63	2026/09	2	2026-09-08 13:55:51	2026-09-08 13:55:51
342	51	2026/09	3	2026-09-06 23:06:13	2026-09-06 23:09:06
344	52	2026/09	2	2026-09-06 23:11:36	2026-09-06 23:11:36
347	54	2026/09	4	2026-09-07 05:55:51	2026-09-08 13:58:00
391	62	2026/09	4	2026-09-08 12:45:04	2026-09-08 15:06:19
345	53	2026/09	6	2026-09-07 05:15:04	2026-09-07 06:37:28
397	65	2026/09	2	2026-09-08 15:10:58	2026-09-08 15:10:58
395	64	2026/09	3	2026-09-08 14:58:10	2026-09-08 15:24:31
352	55	2026/09	4	2026-09-07 11:22:43	2026-09-07 11:30:46
400	67	2026/09	2	2026-09-08 15:32:25	2026-09-08 15:32:25
357	57	2026/09	5	2026-09-07 13:35:24	2026-09-07 13:42:25
155	46	2026/09	5	2026-09-06 15:34:42	2026-09-07 16:34:22
399	66	2026/09	3	2026-09-08 15:30:57	2026-09-08 15:33:02
362	58	2026/09	4	2026-09-08 05:56:00	2026-09-08 06:12:47
\.


--
-- Data for Name: number_sequences; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.number_sequences (id, code, name, prefix, period_format, padding, created_at, updated_at, separator) FROM stdin;
1	asset.GA.1201	Kode aset GA akun 1201	GA-1201	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
2	asset.GA.1202	Kode aset GA akun 1202	GA-1202	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
3	asset.OPS.1203	Kode aset OPS akun 1203	OPS-1203	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
4	asset.GA.1213	Kode aset GA akun 1213	GA-1213	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
5	asset.HRD.1212	Kode aset HRD akun 1212	HRD-1212	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
6	asset.IT.1204	Kode aset IT akun 1204	IT-1204	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
7	asset.OPS.1208	Kode aset OPS akun 1208	OPS-1208	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
8	asset.FIN.1212	Kode aset FIN akun 1212	FIN-1212	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
9	asset.OPS.1207	Kode aset OPS akun 1207	OPS-1207	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
10	asset.GA.1207	Kode aset GA akun 1207	GA-1207	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
11	asset.FIN.1210	Kode aset FIN akun 1210	FIN-1210	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
12	asset.HRD.1208	Kode aset HRD akun 1208	HRD-1208	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
13	asset.GA.1208	Kode aset GA akun 1208	GA-1208	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
14	asset.HRD.1209	Kode aset HRD akun 1209	HRD-1209	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
15	asset.FIN.1206	Kode aset FIN akun 1206	FIN-1206	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
16	asset.OPS.1211	Kode aset OPS akun 1211	OPS-1211	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
17	asset.IT.1206	Kode aset IT akun 1206	IT-1206	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
18	asset.FIN.1208	Kode aset FIN akun 1208	FIN-1208	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
19	asset.IT.1209	Kode aset IT akun 1209	IT-1209	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
20	asset.FIN.1209	Kode aset FIN akun 1209	FIN-1209	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
21	asset.GA.1209	Kode aset GA akun 1209	GA-1209	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
22	asset.OPS.1205	Kode aset OPS akun 1205	OPS-1205	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
23	asset.HRD.1207	Kode aset HRD akun 1207	HRD-1207	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
24	asset.IT.1210	Kode aset IT akun 1210	IT-1210	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
25	asset.GA.1206	Kode aset GA akun 1206	GA-1206	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
26	asset.OPS.1210	Kode aset OPS akun 1210	OPS-1210	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
27	asset.FIN.1211	Kode aset FIN akun 1211	FIN-1211	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
28	asset.HRD.1210	Kode aset HRD akun 1210	HRD-1210	Y	4	2026-09-06 14:04:04	2026-09-06 14:04:04	-
29	asset.GA.1204	Kode aset GA akun 1204	GA-1204	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
30	asset.OPS.1209	Kode aset OPS akun 1209	OPS-1209	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
31	asset.OPS.1206	Kode aset OPS akun 1206	OPS-1206	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
32	asset.OPS.1204	Kode aset OPS akun 1204	OPS-1204	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
33	asset.HRD.1211	Kode aset HRD akun 1211	HRD-1211	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
34	asset.OPS.1212	Kode aset OPS akun 1212	OPS-1212	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
35	asset.HRD.1206	Kode aset HRD akun 1206	HRD-1206	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
36	asset.HRD.1205	Kode aset HRD akun 1205	HRD-1205	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
37	asset.FIN.1207	Kode aset FIN akun 1207	FIN-1207	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
38	asset.FIN.1204	Kode aset FIN akun 1204	FIN-1204	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
39	asset.IT.1205	Kode aset IT akun 1205	IT-1205	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
40	asset.IT.1207	Kode aset IT akun 1207	IT-1207	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
41	asset.GA.1211	Kode aset GA akun 1211	GA-1211	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
42	asset.GA.1212	Kode aset GA akun 1212	GA-1212	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
43	asset.IT.1211	Kode aset IT akun 1211	IT-1211	Y	4	2026-09-06 14:04:05	2026-09-06 14:04:05	-
44	asset.GA.1205	Kode aset GA akun 1205	GA-1205	Y	4	2026-09-06 14:04:06	2026-09-06 14:04:06	-
45	asset.FIN.1205	Kode aset FIN akun 1205	FIN-1205	Y	4	2026-09-06 14:04:06	2026-09-06 14:04:06	-
46	stock_opname	Nomor sesi stock opname	SO	Y/m	4	2026-09-06 15:17:28	2026-09-06 15:17:28	/
49	supply_item	Kode barang habis pakai	ATK		4	2026-09-06 21:23:18	2026-09-06 21:23:18	-
50	supply_transaction	Nomor mutasi persediaan	MP	Y/m	4	2026-09-06 21:23:20	2026-09-06 21:23:20	/
51	asset_transfer	Nomor serah terima aset	MA	Y/m	4	2026-09-06 23:01:35	2026-09-06 23:01:35	/
52	asset_disposal	Nomor pelepasan aset	PA	Y/m	4	2026-09-06 23:01:35	2026-09-06 23:01:35	/
53	work_order	Nomor perintah kerja	WO	Y/m	4	2026-09-07 03:19:29	2026-09-07 03:19:29	/
54	service_request	Nomor permintaan perbaikan	PB	Y/m	4	2026-09-07 05:53:04	2026-09-07 05:53:04	/
55	vehicle_booking	Nomor pemesanan kendaraan	PK	Y/m	4	2026-09-07 11:20:59	2026-09-07 11:20:59	/
56	vendor_bill	Nomor tagihan rekanan	TG	Y/m	4	2026-09-07 12:33:33	2026-09-07 12:33:33	/
57	reimbursement	Nomor penggantian biaya	PG	Y/m	4	2026-09-07 13:33:13	2026-09-07 13:33:13	/
59	supply_purchase	Nomor pesanan pembelian barang	PP	Y/m	4	2026-09-08 10:03:00	2026-09-08 10:03:00	/
60	supply_receipt	Nomor penerimaan barang	PN	Y/m	4	2026-09-08 10:03:00	2026-09-08 10:03:00	/
58	supply_request	Nomor permintaan barang	PM	Y/m	4	2026-09-08 05:48:09	2026-09-08 11:27:34	/
61	supply_opname	Nomor opname barang habis pakai	OP	Y/m	4	2026-09-08 11:27:34	2026-09-08 11:27:34	/
62	cleaning_inspection	Nomor pemeriksaan kebersihan	KB	Y/m	4	2026-09-08 12:28:05	2026-09-08 12:28:05	/
63	incident_report	Nomor laporan insiden keamanan	LI	Y/m	4	2026-09-08 13:33:20	2026-09-08 13:33:20	/
64	business_trip	Nomor surat perjalanan dinas	SPD	Y/m	4	2026-09-08 14:48:36	2026-09-08 14:48:36	/
65	parcel_shipment	Nomor pengiriman paket	KP	Y/m	4	2026-09-08 14:48:36	2026-09-08 14:48:36	/
66	letter_in	Nomor agenda surat masuk	AM	Y/m	4	2026-09-08 14:48:36	2026-09-08 14:48:36	/
67	letter_out	Nomor agenda surat keluar	AK	Y/m	4	2026-09-08 14:48:36	2026-09-08 14:48:36	/
\.


--
-- Data for Name: parcel_shipments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.parcel_shipments (id, code, request_date, requester_employee_id, department_id, recipient_name, recipient_address, recipient_phone, contents, weight_kg, estimated_cost, status, vendor_id, tracking_number, shipped_date, shipping_cost, insurance_cost, packing_cost, discount_amount, tax_amount, notes, cancel_reason, created_by_user_id, created_at, updated_at) FROM stdin;
1	KP/2026/09/0001	2026-09-08	4	2	Joko Widyawan	Parcel pernikahan	09876789987	barang pecah belah	2.00	15000.00	dikirim	2	AJ0098771265	2026-09-08	185000.00	12500.00	25000.00	20000.00	22550.00	\N	\N	1	2026-09-08 15:10:58	2026-09-08 15:34:04
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: permission_role; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permission_role (role_id, permission_id) FROM stdin;
1	1
1	2
1	3
1	4
1	5
1	6
1	7
1	8
1	9
1	10
1	11
1	12
1	13
1	14
1	15
1	16
1	17
1	18
1	19
1	20
1	21
1	22
1	23
1	24
1	25
2	11
2	12
2	13
2	15
2	16
2	17
2	19
2	20
2	21
2	23
2	25
3	11
3	15
3	16
3	17
3	19
3	20
3	21
4	15
4	19
1	26
1	27
1	28
1	29
1	30
1	31
1	32
1	33
1	34
2	26
2	27
2	28
2	30
2	31
2	32
2	34
3	26
3	30
3	31
3	32
3	34
4	30
1	35
1	36
1	37
1	38
1	39
2	35
2	36
2	37
2	39
3	35
3	36
3	37
1	40
1	41
1	42
1	43
1	44
1	45
1	46
1	47
2	40
2	41
2	42
2	44
2	45
2	46
2	47
3	40
3	41
3	42
3	44
3	45
3	46
1	48
1	49
1	50
1	51
1	52
1	53
1	54
2	48
2	49
2	50
2	51
2	52
2	53
2	54
3	48
3	49
3	51
1	55
1	56
1	57
1	58
1	59
1	60
1	61
1	62
1	63
1	64
1	65
1	66
1	67
1	68
1	69
1	70
1	71
1	72
1	73
1	74
1	75
1	76
1	77
1	78
1	79
1	80
2	55
2	58
2	59
2	60
2	62
2	63
2	64
2	65
2	66
2	67
2	68
2	69
2	70
2	71
2	72
2	73
2	75
2	76
2	77
2	78
2	79
3	58
3	62
3	63
3	64
3	66
3	67
3	68
3	70
3	71
3	72
3	73
3	76
3	77
4	70
4	72
1	81
1	82
1	83
1	84
2	81
2	82
2	83
2	84
3	81
3	82
3	83
1	85
1	86
1	87
1	88
1	89
1	90
1	91
2	85
2	86
2	87
2	88
2	89
2	90
2	91
3	85
3	86
3	87
3	88
3	91
4	85
4	87
1	92
1	93
1	94
1	95
1	96
1	97
1	98
1	99
2	92
2	93
2	94
2	96
2	97
2	98
2	99
3	92
3	96
1	100
1	101
1	102
1	103
1	104
1	105
2	100
2	101
2	102
2	103
2	104
2	105
3	100
3	101
3	102
1	106
1	107
1	108
1	109
1	110
1	111
1	112
1	113
2	106
2	107
2	108
2	109
2	110
2	111
2	112
2	113
3	106
3	107
3	108
3	109
3	112
4	106
4	108
4	109
1	114
1	115
1	116
1	117
1	118
1	119
1	120
1	121
2	114
2	115
2	116
2	117
2	118
2	119
2	120
2	121
3	114
3	115
3	116
3	117
3	120
3	121
4	114
4	116
4	117
1	122
1	123
1	124
1	125
1	126
1	127
2	122
2	123
2	124
2	125
2	126
2	127
3	122
3	123
3	124
3	127
1	128
1	129
1	130
1	131
1	132
2	128
2	129
2	130
2	131
2	132
3	128
3	129
3	130
1	133
1	134
1	135
1	136
1	137
1	138
1	139
1	140
1	141
1	142
1	143
1	144
2	133
2	134
2	135
2	136
2	137
2	138
2	139
2	140
2	141
2	142
2	143
2	144
3	133
3	137
3	141
3	142
3	143
1	145
1	146
1	147
1	148
1	149
1	150
1	151
1	152
2	145
2	146
2	147
2	148
2	149
2	150
2	151
2	152
3	145
3	147
3	149
3	150
3	151
1	153
1	154
1	155
1	156
1	157
1	158
1	159
1	160
1	161
1	162
1	163
1	164
1	165
1	166
1	167
1	168
2	153
2	154
2	155
2	156
2	157
2	158
2	159
2	160
2	161
2	162
2	163
2	164
2	165
2	166
2	167
2	168
3	153
3	154
3	155
3	157
3	158
3	159
3	161
3	162
3	163
3	164
3	168
4	161
4	163
4	164
\.


--
-- Data for Name: permission_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permission_user (user_id, permission_id, granted) FROM stdin;
\.


--
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.permissions (id, module_id, action, key, name, created_at, updated_at) FROM stdin;
2	1	create	roles.create	Tambah Roles	2026-09-06 09:04:36	2026-09-08 05:48:04
3	1	update	roles.update	Ubah Roles	2026-09-06 09:04:36	2026-09-08 05:48:04
4	1	delete	roles.delete	Hapus Roles	2026-09-06 09:04:36	2026-09-08 05:48:04
6	2	create	users.create	Tambah Users	2026-09-06 09:04:36	2026-09-08 05:48:04
7	2	update	users.update	Ubah Users	2026-09-06 09:04:36	2026-09-08 05:48:04
8	2	delete	users.delete	Hapus Users	2026-09-06 09:04:36	2026-09-08 05:48:04
9	3	read	modules.read	Lihat Modules	2026-09-06 09:04:36	2026-09-08 05:48:04
10	3	update	modules.update	Ubah Modules	2026-09-06 09:04:36	2026-09-08 05:48:04
26	9	read	asset_categories.read	Lihat Asset Categories	2026-09-06 12:53:24	2026-09-08 05:48:04
27	9	create	asset_categories.create	Tambah Asset Categories	2026-09-06 12:53:24	2026-09-08 05:48:05
28	9	update	asset_categories.update	Ubah Asset Categories	2026-09-06 12:53:24	2026-09-08 05:48:05
29	9	delete	asset_categories.delete	Hapus Asset Categories	2026-09-06 12:53:24	2026-09-08 05:48:05
30	10	read	assets.read	Lihat Assets	2026-09-06 12:53:24	2026-09-08 05:48:05
31	10	create	assets.create	Tambah Assets	2026-09-06 12:53:24	2026-09-08 05:48:05
32	10	update	assets.update	Ubah Assets	2026-09-06 12:53:24	2026-09-08 05:48:05
33	10	delete	assets.delete	Hapus Assets	2026-09-06 12:53:24	2026-09-08 05:48:05
34	10	print	assets.print	Cetak Assets	2026-09-06 12:53:24	2026-09-08 05:48:05
35	11	read	stock_opnames.read	Lihat Stock Opname	2026-09-06 15:17:20	2026-09-08 05:48:05
36	11	create	stock_opnames.create	Tambah Stock Opname	2026-09-06 15:17:20	2026-09-08 05:48:05
37	11	update	stock_opnames.update	Ubah Stock Opname	2026-09-06 15:17:20	2026-09-08 05:48:05
38	11	delete	stock_opnames.delete	Hapus Stock Opname	2026-09-06 15:17:20	2026-09-08 05:48:05
39	11	approve	stock_opnames.approve	Setujui Stock Opname	2026-09-06 15:17:20	2026-09-08 05:48:05
48	14	read	asset_transfers.read	Lihat Asset Transfers	2026-09-06 23:01:27	2026-09-08 05:48:05
49	14	create	asset_transfers.create	Tambah Asset Transfers	2026-09-06 23:01:27	2026-09-08 05:48:05
50	14	delete	asset_transfers.delete	Hapus Asset Transfers	2026-09-06 23:01:27	2026-09-08 05:48:05
51	15	read	asset_disposals.read	Lihat Asset Disposals	2026-09-06 23:01:27	2026-09-08 05:48:05
52	15	create	asset_disposals.create	Tambah Asset Disposals	2026-09-06 23:01:27	2026-09-08 05:48:05
54	15	delete	asset_disposals.delete	Hapus Asset Disposals	2026-09-06 23:01:27	2026-09-08 05:48:05
40	12	read	supply_items.read	Lihat Supply Items	2026-09-06 21:19:20	2026-09-08 05:48:05
41	12	create	supply_items.create	Tambah Supply Items	2026-09-06 21:19:20	2026-09-08 05:48:05
42	12	update	supply_items.update	Ubah Supply Items	2026-09-06 21:19:20	2026-09-08 05:48:05
43	12	delete	supply_items.delete	Hapus Supply Items	2026-09-06 21:19:20	2026-09-08 05:48:05
44	13	read	supply_transactions.read	Lihat Supply Movements	2026-09-06 21:19:20	2026-09-08 05:48:05
45	13	create	supply_transactions.create	Tambah Supply Movements	2026-09-06 21:19:20	2026-09-08 05:48:05
46	13	update	supply_transactions.update	Ubah Supply Movements	2026-09-06 21:19:20	2026-09-08 05:48:05
47	13	delete	supply_transactions.delete	Hapus Supply Movements	2026-09-06 21:19:20	2026-09-08 05:48:05
11	4	read	departments.read	Lihat Departments	2026-09-06 09:04:36	2026-09-08 05:48:05
12	4	create	departments.create	Tambah Departments	2026-09-06 09:04:36	2026-09-08 05:48:05
13	4	update	departments.update	Ubah Departments	2026-09-06 09:04:36	2026-09-08 05:48:05
14	4	delete	departments.delete	Hapus Departments	2026-09-06 09:04:36	2026-09-08 05:48:05
15	5	read	locations.read	Lihat Locations	2026-09-06 09:04:36	2026-09-08 05:48:05
16	5	create	locations.create	Tambah Locations	2026-09-06 09:04:36	2026-09-08 05:48:05
17	5	update	locations.update	Ubah Locations	2026-09-06 09:04:36	2026-09-08 05:48:05
18	5	delete	locations.delete	Hapus Locations	2026-09-06 09:04:36	2026-09-08 05:48:05
19	6	read	employees.read	Lihat Employees	2026-09-06 09:04:36	2026-09-08 05:48:05
20	6	create	employees.create	Tambah Employees	2026-09-06 09:04:36	2026-09-08 05:48:05
21	6	update	employees.update	Ubah Employees	2026-09-06 09:04:36	2026-09-08 05:48:05
22	6	delete	employees.delete	Hapus Employees	2026-09-06 09:04:36	2026-09-08 05:48:05
55	16	read	depreciation_periods.read	Lihat Depreciation	2026-09-07 02:35:17	2026-09-08 05:48:05
56	16	close	depreciation_periods.close	Tutup periode Depreciation	2026-09-07 02:35:17	2026-09-08 05:48:05
58	17	read	vendors.read	Lihat Vendors	2026-09-07 03:19:20	2026-09-08 05:48:05
59	17	create	vendors.create	Tambah Vendors	2026-09-07 03:19:20	2026-09-08 05:48:05
60	17	update	vendors.update	Ubah Vendors	2026-09-07 03:19:20	2026-09-08 05:48:05
61	17	delete	vendors.delete	Hapus Vendors	2026-09-07 03:19:20	2026-09-08 05:48:05
71	20	read_all	service_requests.read_all	Lihat milik semua orang Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
72	20	create	service_requests.create	Tambah Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
73	20	update	service_requests.update	Ubah Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
74	20	delete	service_requests.delete	Hapus Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
75	20	approve	service_requests.approve	Setujui Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
62	18	read	maintenance_schedules.read	Lihat Preventive Maintenance	2026-09-07 03:19:20	2026-09-08 13:44:05
63	18	create	maintenance_schedules.create	Tambah Preventive Maintenance	2026-09-07 03:19:20	2026-09-08 13:44:05
64	18	update	maintenance_schedules.update	Ubah Preventive Maintenance	2026-09-07 03:19:20	2026-09-08 13:44:05
65	18	delete	maintenance_schedules.delete	Hapus Preventive Maintenance	2026-09-07 03:19:20	2026-09-08 13:44:05
66	19	read	work_orders.read	Lihat Work Orders	2026-09-07 03:19:20	2026-09-08 05:48:05
67	19	create	work_orders.create	Tambah Work Orders	2026-09-07 03:19:20	2026-09-08 05:48:05
68	19	update	work_orders.update	Ubah Work Orders	2026-09-07 03:19:20	2026-09-08 05:48:05
69	19	delete	work_orders.delete	Hapus Work Orders	2026-09-07 03:19:20	2026-09-08 05:48:06
23	7	read	settings.read	Lihat Settings	2026-09-06 09:04:36	2026-09-08 05:48:06
24	7	update	settings.update	Ubah Settings	2026-09-06 09:04:36	2026-09-08 05:48:06
25	8	read	audit_logs.read	Lihat Audit Log	2026-09-06 09:04:36	2026-09-08 05:48:06
70	20	read	service_requests.read	Lihat Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
1	1	read	roles.read	Lihat Roles	2026-09-06 09:04:36	2026-09-08 05:48:04
5	2	read	users.read	Lihat Users	2026-09-06 09:04:36	2026-09-08 05:48:04
53	15	update	asset_disposals.update	Ubah Asset Disposals	2026-09-06 23:01:27	2026-09-08 05:48:05
114	28	read	supply_requests.read	Lihat Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
115	28	read_all	supply_requests.read_all	Lihat milik semua orang Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
116	28	create	supply_requests.create	Tambah Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
117	28	update	supply_requests.update	Ubah Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
118	28	delete	supply_requests.delete	Hapus Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
119	28	approve	supply_requests.approve	Setujui Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
120	28	issue	supply_requests.issue	Serahkan barang Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
121	28	request_for_others	supply_requests.request_for_others	Ajukan atas nama orang lain Supply Requests	2026-09-08 05:48:05	2026-09-08 05:48:05
57	16	reopen	depreciation_periods.reopen	Buka kembali periode Depreciation	2026-09-07 02:35:17	2026-09-08 05:48:05
77	21	read	service_request_categories.read	Lihat Request Types	2026-09-07 05:52:52	2026-09-08 05:48:05
78	21	create	service_request_categories.create	Tambah Request Types	2026-09-07 05:52:52	2026-09-08 05:48:05
79	21	update	service_request_categories.update	Ubah Request Types	2026-09-07 05:52:52	2026-09-08 05:48:05
80	21	delete	service_request_categories.delete	Hapus Request Types	2026-09-07 05:52:52	2026-09-08 05:48:05
81	22	read	vehicles.read	Lihat Vehicles	2026-09-07 10:24:00	2026-09-08 05:48:06
82	22	create	vehicles.create	Tambah Vehicles	2026-09-07 10:24:00	2026-09-08 05:48:06
83	22	update	vehicles.update	Ubah Vehicles	2026-09-07 10:24:00	2026-09-08 05:48:06
84	22	delete	vehicles.delete	Hapus Vehicles	2026-09-07 10:24:00	2026-09-08 05:48:06
85	23	read	vehicle_bookings.read	Lihat Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
86	23	read_all	vehicle_bookings.read_all	Lihat milik semua orang Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
87	23	create	vehicle_bookings.create	Tambah Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
88	23	update	vehicle_bookings.update	Ubah Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
89	23	delete	vehicle_bookings.delete	Hapus Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
90	23	approve	vehicle_bookings.approve	Setujui Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
91	23	assign	vehicle_bookings.assign	Tentukan pelaksana Vehicle Bookings	2026-09-07 11:20:58	2026-09-08 05:48:06
92	24	read	expense_categories.read	Lihat Expense Categories	2026-09-07 11:54:38	2026-09-08 05:48:06
93	24	create	expense_categories.create	Tambah Expense Categories	2026-09-07 11:54:38	2026-09-08 05:48:06
94	24	update	expense_categories.update	Ubah Expense Categories	2026-09-07 11:54:38	2026-09-08 05:48:06
95	24	delete	expense_categories.delete	Hapus Expense Categories	2026-09-07 11:54:38	2026-09-08 05:48:06
96	25	read	budgets.read	Lihat Budgets	2026-09-07 11:54:38	2026-09-08 05:48:06
97	25	create	budgets.create	Tambah Budgets	2026-09-07 11:54:38	2026-09-08 05:48:06
98	25	update	budgets.update	Ubah Budgets	2026-09-07 11:54:38	2026-09-08 05:48:06
99	25	delete	budgets.delete	Hapus Budgets	2026-09-07 11:54:38	2026-09-08 05:48:06
100	26	read	vendor_bills.read	Lihat Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
101	26	create	vendor_bills.create	Tambah Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
102	26	update	vendor_bills.update	Ubah Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
103	26	delete	vendor_bills.delete	Hapus Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
104	26	approve	vendor_bills.approve	Setujui Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
105	26	pay	vendor_bills.pay	Tandai sudah dibayar Vendor Bills	2026-09-07 12:33:30	2026-09-08 05:48:06
106	27	read	reimbursements.read	Lihat Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
107	27	read_all	reimbursements.read_all	Lihat milik semua orang Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
108	27	create	reimbursements.create	Tambah Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
109	27	update	reimbursements.update	Ubah Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
110	27	delete	reimbursements.delete	Hapus Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
111	27	approve	reimbursements.approve	Setujui Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
112	27	verify	reimbursements.verify	Periksa bukti Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
113	27	pay	reimbursements.pay	Tandai sudah dibayar Reimbursements	2026-09-07 13:33:11	2026-09-08 05:48:06
122	29	read	supply_purchases.read	Lihat Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
123	29	create	supply_purchases.create	Tambah Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
124	29	update	supply_purchases.update	Ubah Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
125	29	delete	supply_purchases.delete	Hapus Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
126	29	approve	supply_purchases.approve	Setujui Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
127	29	receive	supply_purchases.receive	Terima barang datang Supply Purchases	2026-09-08 10:02:59	2026-09-08 10:02:59
128	30	read	supply_opnames.read	Lihat Supply Opname	2026-09-08 11:27:33	2026-09-08 11:27:33
129	30	create	supply_opnames.create	Tambah Supply Opname	2026-09-08 11:27:33	2026-09-08 11:27:33
130	30	update	supply_opnames.update	Ubah Supply Opname	2026-09-08 11:27:33	2026-09-08 11:27:33
131	30	delete	supply_opnames.delete	Hapus Supply Opname	2026-09-08 11:27:33	2026-09-08 11:27:33
132	30	adjust	supply_opnames.adjust	Terapkan penyesuaian stok Supply Opname	2026-09-08 11:27:33	2026-09-08 11:27:33
133	31	read	service_staff.read	Lihat Service Staff	2026-09-08 12:28:01	2026-09-08 12:28:01
134	31	create	service_staff.create	Tambah Service Staff	2026-09-08 12:28:01	2026-09-08 12:28:01
135	31	update	service_staff.update	Ubah Service Staff	2026-09-08 12:28:01	2026-09-08 12:28:01
136	31	delete	service_staff.delete	Hapus Service Staff	2026-09-08 12:28:01	2026-09-08 12:28:01
137	32	read	service_areas.read	Lihat Service Areas	2026-09-08 12:28:01	2026-09-08 12:28:01
138	32	create	service_areas.create	Tambah Service Areas	2026-09-08 12:28:01	2026-09-08 12:28:01
139	32	update	service_areas.update	Ubah Service Areas	2026-09-08 12:28:01	2026-09-08 12:28:01
140	32	delete	service_areas.delete	Hapus Service Areas	2026-09-08 12:28:01	2026-09-08 12:28:01
141	33	read	cleaning_inspections.read	Lihat Cleaning Inspections	2026-09-08 12:28:01	2026-09-08 12:28:01
142	33	create	cleaning_inspections.create	Tambah Cleaning Inspections	2026-09-08 12:28:01	2026-09-08 12:28:01
143	33	update	cleaning_inspections.update	Ubah Cleaning Inspections	2026-09-08 12:28:01	2026-09-08 12:28:01
144	33	delete	cleaning_inspections.delete	Hapus Cleaning Inspections	2026-09-08 12:28:01	2026-09-08 12:28:01
145	34	read	security_shifts.read	Lihat Security Shifts	2026-09-08 13:33:14	2026-09-08 13:33:14
146	34	create	security_shifts.create	Tambah Security Shifts	2026-09-08 13:33:14	2026-09-08 13:33:14
147	34	update	security_shifts.update	Ubah Security Shifts	2026-09-08 13:33:14	2026-09-08 13:33:14
148	34	delete	security_shifts.delete	Hapus Security Shifts	2026-09-08 13:33:14	2026-09-08 13:33:14
149	35	read	incident_reports.read	Lihat Incident Reports	2026-09-08 13:33:14	2026-09-08 13:33:14
150	35	create	incident_reports.create	Tambah Incident Reports	2026-09-08 13:33:14	2026-09-08 13:33:14
151	35	update	incident_reports.update	Ubah Incident Reports	2026-09-08 13:33:14	2026-09-08 13:33:14
152	35	delete	incident_reports.delete	Hapus Incident Reports	2026-09-08 13:33:14	2026-09-08 13:33:14
76	20	accept	service_requests.accept	Terima dan tugaskan Corrective Maintenance	2026-09-07 05:52:52	2026-09-08 13:44:05
153	36	read	letters.read	Lihat Letters	2026-09-08 14:48:32	2026-09-08 14:48:32
154	36	create	letters.create	Tambah Letters	2026-09-08 14:48:32	2026-09-08 14:48:32
155	36	update	letters.update	Ubah Letters	2026-09-08 14:48:32	2026-09-08 14:48:32
156	36	delete	letters.delete	Hapus Letters	2026-09-08 14:48:32	2026-09-08 14:48:32
157	37	read	parcel_shipments.read	Lihat Parcel Shipments	2026-09-08 14:48:32	2026-09-08 14:48:32
158	37	create	parcel_shipments.create	Tambah Parcel Shipments	2026-09-08 14:48:32	2026-09-08 14:48:32
159	37	update	parcel_shipments.update	Ubah Parcel Shipments	2026-09-08 14:48:32	2026-09-08 14:48:32
160	37	delete	parcel_shipments.delete	Hapus Parcel Shipments	2026-09-08 14:48:32	2026-09-08 14:48:32
161	38	read	business_trips.read	Lihat Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
162	38	read_all	business_trips.read_all	Lihat milik semua orang Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
163	38	create	business_trips.create	Tambah Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
164	38	update	business_trips.update	Ubah Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
165	38	delete	business_trips.delete	Hapus Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
166	38	approve	business_trips.approve	Setujui Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
167	38	pay	business_trips.pay	Tandai sudah dibayar Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
168	38	verify	business_trips.verify	Periksa bukti Business Trips	2026-09-08 14:48:32	2026-09-08 14:48:32
\.


--
-- Data for Name: reimbursement_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.reimbursement_lines (id, reimbursement_id, expense_category_id, expense_date, description, amount, file_path, original_name, size_bytes, created_at, updated_at) FROM stdin;
1	1	9	2026-09-02	Taksi kantor ke Samsat Jakarta Selatan	185000.00	\N	\N	\N	2026-09-07 13:35:55	2026-09-07 13:35:55
2	1	9	2026-09-02	Parkir dan tol	40000.00	\N	\N	\N	2026-09-07 13:36:08	2026-09-07 13:36:08
3	1	8	2026-09-03	Konsumsi rapat dengan petugas	275000.00	\N	\N	\N	2026-09-07 13:36:21	2026-09-07 13:36:21
4	2	8	2026-09-04	Makan siang rapat dengan rekanan	420000.00	\N	\N	\N	2026-09-07 13:41:25	2026-09-07 13:41:25
5	4	10	2026-09-07	operasional	250000.00	struk-penggantian/01M1Y1RKRMZT57W6BEA2QFVTSF.pdf	Test dokumen 1.pdf	\N	2026-09-07 13:43:45	2026-09-07 13:43:45
6	4	6	2026-09-07	operasional	125000.00	struk-penggantian/01M1Y1T806DDVWXMN3TQS9C6MM.pdf	Test dokumen 2.pdf	\N	2026-09-07 13:44:38	2026-09-07 13:44:38
\.


--
-- Data for Name: reimbursements; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.reimbursements (id, code, employee_id, department_id, title, notes, status, submitted_at, approver_employee_id, approved_at, approval_note, approval_skipped_reason, verified_by_user_id, verified_at, rejection_reason, rejected_stage, paid_date, payment_reference, paid_by_user_id, created_by_user_id, created_at, updated_at) FROM stdin;
3	PG/2026/09/0003	8	8	Operasional pekerjaan	untuk layanan operasional	dibatalkan	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	1	2026-09-07 13:41:21	2026-09-07 13:41:49
1	PG/2026/09/0001	6	5	[DATA UJI] Transportasi dan parkir kunjungan kantor pajak, September 2026	[DATA UJI] Pengajuan uji untuk memeriksa jalur lewat persetujuan atasan.	dibayar	2026-09-07 13:36:56	\N	2026-09-07 13:36:56	\N	Departemen yang dibebani belum punya kepala departemen	2	2026-09-07 13:38:25	\N	\N	2026-09-07	TRF-BCA-20260907-402	2	2	2026-09-07 13:35:24	2026-09-07 13:43:30
4	PG/2026/09/0004	8	6	Operasional pekerjaan	easte	diperiksa	2026-09-07 13:45:02	\N	2026-09-07 13:45:02	\N	Departemen yang dibebani belum punya kepala departemen	\N	\N	\N	\N	\N	\N	\N	1	2026-09-07 13:42:25	2026-09-07 13:45:02
2	PG/2026/09/0002	6	5	[DATA UJI] Konsumsi rapat vendor, September 2026	\N	draft	\N	5	\N	\N	\N	\N	\N	Mohon lampirkan foto struknya.	atasan	\N	\N	\N	2	2026-09-07 13:41:04	2026-09-07 13:45:15
\.


--
-- Data for Name: role_user; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.role_user (role_id, user_id) FROM stdin;
1	1
1	2
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles (id, code, name, description, is_system, data_scope, is_active, created_at, updated_at) FROM stdin;
1	administrator	Administrator Sistem	Mengelola pengguna, role, data induk, dan pengaturan sistem.	t	all	t	2026-09-06 09:04:36	2026-09-06 09:04:36
2	manajer-ga	Manajer GA	Melihat seluruh data GA dan mengubah data induk, tanpa mengelola akses pengguna.	f	all	t	2026-09-06 09:04:36	2026-09-06 09:04:36
3	staf-ga	Staf GA	Mengelola data induk harian: karyawan, departemen, dan lokasi.	f	all	t	2026-09-06 09:04:36	2026-09-06 09:04:36
4	karyawan	Karyawan	Hanya melihat direktori karyawan dan daftar lokasi.	f	own	t	2026-09-06 09:04:36	2026-09-06 09:04:36
\.


--
-- Data for Name: security_shifts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.security_shifts (id, shift_date, shift, service_staff_id, location_id, attendance, replacement_staff_id, checked_in_at, checked_out_at, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	2026-09-08	pagi	2	\N	digantikan	1	\N	\N	Agus izin ke dokter, digantikan Sumarno mulai pukul 07.00.	2	2026-09-08 13:49:52	2026-09-08 13:52:56
3	2026-09-08	malam	2	\N	terlambat	\N	\N	\N	\N	2	2026-09-08 13:52:18	2026-09-08 13:54:15
4	2026-09-09	pagi	2	1	hadir	\N	2026-09-08 09:00:00	2026-09-09 00:00:00	\N	1	2026-09-08 15:07:43	2026-09-08 15:08:55
\.


--
-- Data for Name: service_areas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.service_areas (id, code, name, category, location_id, frequency, service_staff_id, notes, is_active, created_at, updated_at) FROM stdin;
1	L1-LOBI	Lobi dan meja resepsionis	lobi	\N	harian	1	\N	t	2026-09-08 12:42:48	2026-09-08 12:42:48
2	L2-TOILET-PRIA	Toilet pria lantai 2	toilet	\N	harian	3	\N	t	2026-09-08 12:43:10	2026-09-08 12:43:10
3	L2-PANTRY	Pantry lantai 2	pantry	\N	harian	3	\N	t	2026-09-08 12:43:41	2026-09-08 12:43:41
4	L3-RAPAT-A	Ruang rapat Anggrek	ruang_rapat	\N	mingguan	\N	\N	t	2026-09-08 12:44:41	2026-09-08 12:44:41
\.


--
-- Data for Name: service_request_attachments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.service_request_attachments (id, service_request_id, name, file_path, original_name, size_bytes, uploaded_by_user_id, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: service_request_categories; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.service_request_categories (id, code, name, description, default_priority, sla_hours, is_active, created_at, updated_at) FROM stdin;
1	LST	Listrik dan penerangan	Lampu mati, stopkontak tidak berfungsi, MCB turun berulang.	tinggi	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
2	AC	Pendingin ruangan	AC tidak dingin, bocor, atau berisik.	normal	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
3	AIR	Air dan sanitasi	Kebocoran pipa, keran rusak, toilet mampet.	tinggi	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
4	MBL	Meja, kursi, dan lemari	Perabot kantor yang rusak, goyang, atau perlu dipindahkan.	rendah	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
5	PTU	Pintu, kunci, dan jendela	Kunci macet, engsel lepas, kaca retak.	normal	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
6	KBR	Kebersihan	Permintaan pembersihan di luar jadwal rutin.	normal	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
7	JRG	Jaringan dan kelistrikan data	Titik LAN mati, kabel putus, rak jaringan. Perangkatnya sendiri urusan tim IT.	tinggi	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
8	LFT	Lift dan mesin gedung	Lift berhenti, genset, pompa, atau mesin gedung lain yang berhenti bekerja.	mendesak	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
9	LAIN	Lainnya	Permintaan yang belum masuk jenis mana pun. Tim GA memindahkannya saat menerima.	normal	\N	t	2026-09-07 05:53:05	2026-09-07 05:53:05
\.


--
-- Data for Name: service_requests; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.service_requests (id, code, requester_employee_id, department_id, service_request_category_id, location_id, asset_id, title, description, priority, status, submitted_at, approver_employee_id, approved_at, approval_note, approval_skipped_reason, accepted_by_user_id, accepted_at, sla_due_at, work_order_id, closed_at, rejection_reason, created_by_user_id, created_at, updated_at) FROM stdin;
1	PB/2026/09/0001	8	6	2	4	\N	AC berasap	Per pagi ini ac mulai berasap...	tinggi	diterima	2026-09-07 05:55:51	\N	2026-09-07 05:55:51	\N	Departemen pemohon belum punya kepala departemen	1	2026-09-07 06:37:28	\N	5	\N	\N	1	2026-09-07 05:55:51	2026-09-07 06:37:28
2	PB/2026/09/0002	1	1	2	1	\N	[DATA UJI] Keran pantry lantai 2 menetes	[DATA UJI] Dibuat untuk menguji unggah foto pada permintaan perbaikan.	normal	disetujui	2026-09-07 10:41:13	\N	2026-09-07 10:41:13	\N	Departemen pemohon belum punya kepala departemen	\N	\N	\N	\N	\N	\N	2	2026-09-07 10:41:13	2026-09-07 10:41:13
3	PB/2026/09/0003	1	1	5	21	\N	Kerusakan atau perusakan di Area parkir kendaraan dinas, LI/2026/09/0001	Pagar besi sisi timur bengkok dan gemboknya patah. Diduga ditabrak kendaraan saat parkir mundur. Area sudah dipasangi garis pembatas sementara.	mendesak	disetujui	2026-09-08 13:58:00	\N	2026-09-08 13:58:00	\N	Prioritas mendesak, langsung diteruskan ke tim GA	\N	\N	\N	\N	\N	\N	2	2026-09-08 13:58:00	2026-09-08 13:58:00
\.


--
-- Data for Name: service_staff; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.service_staff (id, employee_id, name, vendor_id, kind, phone, start_date, notes, is_active, created_at, updated_at) FROM stdin;
2	8	\N	\N	keamanan	\N	\N	\N	t	2026-09-08 12:31:54	2026-09-08 12:31:54
3	\N	Wati Suryani	2	kebersihan	\N	\N	[DATA UJI] Dibuat saat pemeriksaan kiriman P.	t	2026-09-08 12:33:22	2026-09-08 12:42:04
1	\N	Sumarno	\N	keduanya	081234567801	\N	\N	t	2026-09-08 12:30:51	2026-09-08 13:49:00
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
VDO5hL8XsODdGXcoDo0tfa2q5T1oIeogLfX8CvmV	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36 Edg/152.0.0.0	YTo4OntzOjY6Il90b2tlbiI7czo0MDoiOFAyNktKbVBqTm1YeVJpWU1zOWR6T1ZzS2tQQnp0bXlPc05hVWZhTSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vc3VwcGx5LXRyYW5zYWN0aW9ucyI7czo1OiJyb3V0ZSI7czo1MDoiZmlsYW1lbnQuYWRtaW4ucmVzb3VyY2VzLnN1cHBseS10cmFuc2FjdGlvbnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI5YWE3OTYyZmExMjMzMjlhM2Q2ZmJkY2VmZGQxZmRhY2QwYWY5NDUxY2MzYTdjMjc2NmU3ZDhiNWI1MzA3MmNhIjtzOjY6InRhYmxlcyI7YTo1OTp7czo0MDoiZTI4YTYwMjY0YTI4YTBmYzU5YzdkYzg2YmZmZDgyNDhfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NToibGFiZWwiO3M6MTA6IlBlbmdhdHVyYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjM6ImtleSI7czo1OiJsYWJlbCI7czo1OiJLdW5jaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ2YWx1ZSI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToiZ3JvdXAiO3M6NToibGFiZWwiO3M6ODoiS2Vsb21wb2siO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjFjMTQ4NDZjNzI1OTUzOWM1MTkzOTY0MmJlZjYxZTA1X2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6OToiS29kZSBhc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IkFzZXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJsb2NhdGlvbi5jb2RlIjtzOjU6ImxhYmVsIjtzOjY6Ikxva2FzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImN1c3RvZGlhbi5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTY6IlBlbmFuZ2d1bmcgamF3YWIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJ3YXJyYW50eV91bnRpbCI7czo1OiJsYWJlbCI7czoxNDoiR2FyYW5zaSBzYW1wYWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6InNpc2EiO3M6NToibGFiZWwiO3M6NDoiU2lzYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiOTcyMGI5NTAyYmEyYzdhMTczYjhjNTlkOTE2YjA2OGZfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImV4cGlyZXNfYXQiO3M6NToibGFiZWwiO3M6MTQ6IkJlcmxha3Ugc2FtcGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMDoidmVoaWNsZS5wbGF0ZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTI6Ik5vbW9yIHBvbGlzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidHlwZSI7czo1OiJsYWJlbCI7czo3OiJEb2t1bWVuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJpc3N1ZXIiO3M6NToibGFiZWwiO3M6MTY6IkRpdGVyYml0a2FuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjQzNzI1M2RmNDcwMzZmNmM3NjRhODdhYzI0NDYzZGQxX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJlbXBsb3llZS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6NzoiUGVtb2hvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToidGl0bGUiO3M6NToibGFiZWwiO3M6OToiVW50dWsgYXBhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoibGluZXNfc3VtX2Ftb3VudCI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjg6Ik1lbnVuZ2d1IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3VibWl0dGVkX2F0IjtzOjU6ImxhYmVsIjtzOjU6IlNlamFrIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiIxOGM0ZWViMjNhOGUyMjg4MDI4NWIzYWVhYTYwZDdiN19jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiZW1wbG95ZWUuZnVsbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjc6IlBlbW9ob24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6InB1cnBvc2UiO3M6NToibGFiZWwiO3M6MTk6IlVudHVrIGtlcGVybHVhbiBhcGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJsaW5lc19jb3VudCI7czo1OiJsYWJlbCI7czozOiJJc2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo4OiJNZW51bmdndSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoic3RvayI7czo1OiJsYWJlbCI7czoxMzoiS2VzaWFwYW4gc3RvayI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZmY2MWMyN2MzNmE3Mjc0NDE1ZGM4NGU1MTM1YzIyYWNfY29sdW1ucyI7YTo2OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJLb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjY6IkJhcmFuZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoic3RvayI7czo1OiJsYWJlbCI7czo0OiJTdG9rIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoibWluaW11bV9zdG9jayI7czo1OiJsYWJlbCI7czo3OiJNaW5pbXVtIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJrdXJhbmciO3M6NToibGFiZWwiO3M6NjoiS3VyYW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJrZWFkYWFuIjtzOjU6ImxhYmVsIjtzOjc6IktlYWRhYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6ImVhZmU2MjcxNDVjOGM3NmRhNTJiYTUyYTk3NmZlNmVmX2NvbHVtbnMiO2E6MTU6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoicGhvdG9fcGF0aCI7czo1OiJsYWJlbCI7czo0OiJGb3RvIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6OToiS29kZSBhc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJjYXRlZ29yeS5uYW1lIjtzOjU6ImxhYmVsIjtzOjg6IkthdGVnb3JpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJkZXBhcnRtZW50LmNvZGUiO3M6NToibGFiZWwiO3M6MTA6IkRlcGFydGVtZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImxvY2F0aW9uLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiTG9rYXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToiY3VzdG9kaWFuLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czoxNjoiUGVuYW5nZ3VuZyBqYXdhYiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJjb25kaXRpb24iO3M6NToibGFiZWwiO3M6NzoiS29uZGlzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6ImFjcXVpc2l0aW9uX2Nvc3QiO3M6NToibGFiZWwiO3M6MTU6Ik5pbGFpIHBlcm9sZWhhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MTA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6ImFjcXVpc2l0aW9uX2RhdGUiO3M6NToibGFiZWwiO3M6MTc6IlRhbmdnYWwgcGVyb2xlaGFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxMTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoib3duZXJzaGlwX3R5cGUiO3M6NToibGFiZWwiO3M6MTE6IktlcGVtaWxpa2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxMjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoid2FycmFudHlfdW50aWwiO3M6NToibGFiZWwiO3M6MTQ6IkdhcmFuc2kgc2FtcGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxMzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoibGVhc2VfZW5kX2RhdGUiO3M6NToibGFiZWwiO3M6MTE6IlNld2Egc2FtcGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxNDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToiZG9jdW1lbnRzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjc6IkRva3VtZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZWFmZTYyNzE0NWM4Yzc2ZGE1MmJhNTJhOTc2ZmU2ZWZfZmlsdGVycyI7YToxMjp7czoxNzoiYXNzZXRfY2F0ZWdvcnlfaWQiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjExOiJsb2NhdGlvbl9pZCI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTM6ImRlcGFydG1lbnRfaWQiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjY6InN0YXR1cyI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6OToiY29uZGl0aW9uIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxMDoiYXNzZXRfdHlwZSI7YToxOntzOjU6InZhbHVlIjtOO31zOjE0OiJvd25lcnNoaXBfdHlwZSI7YToxOntzOjU6InZhbHVlIjtOO31zOjIwOiJnYXJhbnNpX3NlZ2VyYV9oYWJpcyI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTk6ImdhcmFuc2lfc3VkYWhfaGFiaXMiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjE3OiJzZXdhX3NlZ2VyYV9oYWJpcyI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTI6InRhbnBhX2xva2FzaSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MjI6InRhbnBhX3BlbmFuZ2d1bmdfamF3YWIiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czozNzoiZWFmZTYyNzE0NWM4Yzc2ZGE1MmJhNTJhOTc2ZmU2ZWZfc29ydCI7TjtzOjQwOiIxYWI4YTQ4NTg4NGFlNDNjNmUyYjhhNzdmY2U3NjdjNl9jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJ0eXBlIjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJOYW1hIGRva3VtZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJvcmlnaW5hbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IkJlcmthcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic2l6ZV9ieXRlcyI7czo1OiJsYWJlbCI7czo2OiJVa3VyYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6ODoiRGl1bmdnYWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJ1cGxvYWRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJkZmJmM2I5OGU1Y2FlZGQzNzlkMWZkY2EyZDRhNTY0Nl9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IlNlc2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJsaW5lc19jb3VudCI7czo1OiJsYWJlbCI7czo2OiJUYXJnZXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImRpcGVyaWtzYSI7czo1OiJsYWJlbCI7czo5OiJEaXBlcmlrc2EiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6InNlbGlzaWgiO3M6NToibGFiZWwiO3M6NzoiU2VsaXNpaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImZpbmlzaGVkX2F0IjtzOjU6ImxhYmVsIjtzOjc6IlNlbGVzYWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NjoiRGlidWF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6Ijc5NGI4NDZkYjQ2MTBkY2JkNTQ4NmY1NGNmOTM2ZjIzX2NvbHVtbnMiO2E6MTA6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoidHJhbnNmZXJfZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJUYW5nZ2FsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJhc3NldC5uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IkFzZXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InJlYXNvbiI7czo1OiJsYWJlbCI7czo2OiJBbGFzYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InBlcnViYWhhbiI7czo1OiJsYWJlbCI7czoxMjoiWWFuZyBiZXJ1YmFoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMDoicmVjZWl2ZWRCeS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTM6IkRpdGVyaW1hIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIyOiJoYW5kZWRPdmVyQnkuZnVsbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjE1OiJEaXNlcmFoa2FuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicmVmZXJlbmNlIjtzOjU6ImxhYmVsIjtzOjEyOiJCZXJpdGEgYWNhcmEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaWNhdGF0IG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiNzk0Yjg0NmRiNDYxMGRjYmQ1NDg2ZjU0Y2Y5MzZmMjNfZmlsdGVycyI7YTozOntzOjY6InJlYXNvbiI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTQ6InRvX2xvY2F0aW9uX2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTU6InJlbnRhbmdfdGFuZ2dhbCI7YToyOntzOjQ6ImRhcmkiO047czo2OiJzYW1wYWkiO047fX1zOjQwOiI1YzNmYzZiYzNhNGE3M2YzODYzMDc0YWMxYTc5ZWM1NF9jb2x1bW5zIjthOjEyOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImRpc3Bvc2FsX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiVGFuZ2dhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiYXNzZXQubmFtZSI7czo1OiJsYWJlbCI7czo0OiJBc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJtZXRob2QiO3M6NToibGFiZWwiO3M6NDoiQ2FyYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoicHJvY2VlZHMiO3M6NToibGFiZWwiO3M6NToiSGFzaWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImxhYmFfcnVnaSI7czo1OiJsYWJlbCI7czoxNDoiTGFiYSBhdGF1IHJ1Z2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJuaWxhaV9idWt1IjtzOjU6ImxhYmVsIjtzOjIzOiJOaWxhaSBidWt1IHNhYXQgZGlsZXBhcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToibmlsYWlfcGVyb2xlaGFuIjtzOjU6ImxhYmVsIjtzOjE1OiJOaWxhaSBwZXJvbGVoYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImNvdW50ZXJwYXJ0eSI7czo1OiJsYWJlbCI7czoxMzoiUGloYWsgdGVya2FpdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMDoiYXBwcm92ZWRCeS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTQ6IkRpc2V0dWp1aSBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxMDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJyZWZlcmVuY2UiO3M6NToibGFiZWwiO3M6MTI6IkJlcml0YSBhY2FyYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MTE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NjoicmVhc29uIjtzOjU6ImxhYmVsIjtzOjY6IkFsYXNhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI1YzNmYzZiYzNhNGE3M2YzODYzMDc0YWMxYTc5ZWM1NF9maWx0ZXJzIjthOjI6e3M6NjoibWV0aG9kIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxNToicmVudGFuZ190YW5nZ2FsIjthOjI6e3M6NDoiZGFyaSI7TjtzOjY6InNhbXBhaSI7Tjt9fXM6NDA6ImQ5MzljMmMxZGNmZWQ2ZjlkMWVkYmRiMWJkN2JjZTRiX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InBlcmlvZCI7czo1OiJsYWJlbCI7czo3OiJQZXJpb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoidG90YWxfZXhwZW5zZSI7czo1OiJsYWJlbCI7czoxMToiVG90YWwgYmViYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJhc3NldF9jb3VudCI7czo1OiJsYWJlbCI7czo0OiJBc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoiY2F0Y2hfdXBfY291bnQiO3M6NToibGFiZWwiO3M6MTM6IkJlYmFuIHN1c3VsYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiY2xvc2VkX2F0IjtzOjU6ImxhYmVsIjtzOjc6IkRpdHV0dXAiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE3OiJjbG9zZWRCeVVzZXIubmFtZSI7czo1OiJsYWJlbCI7czo0OiJPbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjkwOGE1YzA1YmZiMDMwODgxOTExN2QxYzliNDY4NzU3X2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJuZXh0X2R1ZV9kYXRlIjtzOjU6ImxhYmVsIjtzOjExOiJKYXR1aCB0ZW1wbyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImFzc2V0LmNvZGUiO3M6NToibGFiZWwiO3M6OToiS29kZSBhc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjk6IlBla2VyamFhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImludGVydmFsX21vbnRocyI7czo1OiJsYWJlbCI7czoxMDoiUGVydWxhbmdhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6Imxhc3RfZG9uZV9kYXRlIjtzOjU6ImxhYmVsIjtzOjg6IlRlcmFraGlyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJwZWxha3NhbmEiO3M6NToibGFiZWwiO3M6OToiUGVsYWtzYW5hIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJhc3NldC5sb2NhdGlvbi5uYW1lIjtzOjU6ImxhYmVsIjtzOjY6Ikxva2FzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoiZXN0aW1hdGVkX2Nvc3QiO3M6NToibGFiZWwiO3M6MTU6IlBlcmtpcmFhbiBiaWF5YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI5MDhhNWMwNWJmYjAzMDg4MTkxMTdkMWM5YjQ2ODc1N19maWx0ZXJzIjthOjU6e3M6ODoidGVybGV3YXQiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjk6ImJ1bGFuX2luaSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6OToidmVuZG9yX2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTM6ImthdGVnb3JpX2FzZXQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czo5OiJpc19hY3RpdmUiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9fXM6Mzc6IjkwOGE1YzA1YmZiMDMwODgxOTExN2QxYzliNDY4NzU3X3NvcnQiO047czo0MDoiNjU3ZjZiNTUwNGIwYTU5MGI4MTlmNjllNmNhNDgzZDJfY29sdW1ucyI7YTo4OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoic2VxdWVuY2UiO3M6NToibGFiZWwiO3M6MjoiS2UiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImR1ZV9kYXRlIjtzOjU6ImxhYmVsIjtzOjExOiJKYXR1aCB0ZW1wbyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjc6IktlYWRhYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJjb21wbGV0ZWRfZGF0ZSI7czo1OiJsYWJlbCI7czoxMDoiRGlrZXJqYWthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicGVsYWtzYW5hIjtzOjU6ImxhYmVsIjtzOjE1OiJEaWtlcmpha2FuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InJlc3VsdCI7czo1OiJsYWJlbCI7czoxNzoiSGFzaWwgYXRhdSBhbGFzYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvc3QiO3M6NToibGFiZWwiO3M6NToiQmlheWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6ImNsb3NlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaXR1dHVwIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiMGMxMGZjZmY2ZmIyZDk4NjM1YzRmZmI5ZjhhNzBkOWZfY29sdW1ucyI7YToxMDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRpdGxlIjtzOjU6ImxhYmVsIjtzOjEwOiJQZXJtaW50YWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToicmVxdWVzdGVyLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czo3OiJQZW1vaG9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoibG9jYXRpb24ubmFtZSI7czo1OiJsYWJlbCI7czo2OiJMb2thc2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoicHJpb3JpdHkiO3M6NToibGFiZWwiO3M6OToiUHJpb3JpdGFzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic2xhX2R1ZV9hdCI7czo1OiJsYWJlbCI7czoxMToiQmF0YXMgd2FrdHUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6IndvcmtPcmRlci5jb2RlIjtzOjU6ImxhYmVsIjtzOjE0OiJQZXJpbnRhaCBrZXJqYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3VibWl0dGVkX2F0IjtzOjU6ImxhYmVsIjtzOjg6IkRpYWp1a2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE3OiJhdHRhY2htZW50c19jb3VudCI7czo1OiJsYWJlbCI7czo0OiJGb3RvIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjBjMTBmY2ZmNmZiMmQ5ODYzNWM0ZmZiOWY4YTcwZDlmX2ZpbHRlcnMiO2E6ODp7czo3OiJ0ZXJidWthIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoyMDoibWVudW5nZ3VfcGVyc2V0dWp1YW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjExOiJtZW51bmdndV9nYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6OToibGV3YXRfc2xhIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czo2OiJzdGF0dXMiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjg6InByaW9yaXR5IjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoyNzoic2VydmljZV9yZXF1ZXN0X2NhdGVnb3J5X2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTE6ImxvY2F0aW9uX2lkIjthOjE6e3M6NToidmFsdWUiO047fX1zOjM3OiIwYzEwZmNmZjZmYjJkOTg2MzVjNGZmYjlmOGE3MGQ5Zl9zb3J0IjtOO3M6NDA6IjgxM2NiNTgyZTczYmMzYTA1ZjU2NDkyNTdiNjliZjcxX2NvbHVtbnMiO2E6MTI6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiYXNzZXQuY29kZSI7czo1OiJsYWJlbCI7czo0OiJBc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJwcm9ibGVtIjtzOjU6ImxhYmVsIjtzOjc6Ik1hc2FsYWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidHlwZSI7czo1OiJsYWJlbCI7czo1OiJKZW5pcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJwcmlvcml0eSI7czo1OiJsYWJlbCI7czo5OiJQcmlvcml0YXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InBlbGFrc2FuYSI7czo1OiJsYWJlbCI7czo5OiJQZWxha3NhbmEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJyZXBvcnRlZF9kYXRlIjtzOjU6ImxhYmVsIjtzOjEwOiJEaWxhcG9ya2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJjb21wbGV0ZWRfZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJTZWxlc2FpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvc3QiO3M6NToibGFiZWwiO3M6NToiQmlheWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjEwO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJzZXJ2aWNlUmVxdWVzdC5jb2RlIjtzOjU6ImxhYmVsIjtzOjE1OiJEYXJpIHBlcm1pbnRhYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjExO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE3OiJhdHRhY2htZW50c19jb3VudCI7czo1OiJsYWJlbCI7czo4OiJMYW1waXJhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI4MTNjYjU4MmU3M2JjM2EwNWY1NjQ5MjU3YjY5YmY3MV9maWx0ZXJzIjthOjU6e3M6NzoidGVyYnVrYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6Njoic3RhdHVzIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czo4OiJwcmlvcml0eSI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6NDoidHlwZSI7YToxOntzOjU6InZhbHVlIjtOO31zOjk6InZlbmRvcl9pZCI7YToxOntzOjU6InZhbHVlIjtOO319czozNzoiODEzY2I1ODJlNzNiYzNhMDVmNTY0OTI1N2I2OWJmNzFfc29ydCI7TjtzOjQwOiJiODQyOWZlMmY1MmZkYjhiNzQzYTRjZTgyZmNiZjBkZV9jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJ0eXBlIjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzaXplX2J5dGVzIjtzOjU6ImxhYmVsIjtzOjY6IlVrdXJhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo4OiJEaXVuZ2dhaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6InVwbG9hZGVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6NDoiT2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJub3RlcyI7czo1OiJsYWJlbCI7czo3OiJDYXRhdGFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImE5ZGNlYmQxNDM0ZWNiYzVhOGJlOGU3OTk4Yjc2YmJlX2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJwbGF0ZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTI6Ik5vbW9yIHBvbGlzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoia2VhZGFhbiI7czo1OiJsYWJlbCI7czoxNjoiRG9rdW1lbiB0ZXJkZWthdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InZlaGljbGVfdHlwZSI7czo1OiJsYWJlbCI7czo1OiJKZW5pcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVzYWdlX21vZGUiO3M6NToibGFiZWwiO3M6OToiUGVtYWthaWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyNToiYXNzZXQuY3VzdG9kaWFuLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czoxNjoiUGVuYW5nZ3VuZyBqYXdhYiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoyMzoiZGVmYXVsdERyaXZlci5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTE6IlNvcGlyIHRldGFwIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE2OiJsYXN0X29kb21ldGVyX2ttIjtzOjU6ImxhYmVsIjtzOjg6Ik9kb21ldGVyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJkb2N1bWVudHNfY291bnQiO3M6NToibGFiZWwiO3M6NzoiRG9rdW1lbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJhOWRjZWJkMTQzNGVjYmM1YThiZThlNzk5OGI3NmJiZV9maWx0ZXJzIjthOjU6e3M6MTE6ImphdHVoX3RlbXBvIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czo5OiJ0ZXJsYW1iYXQiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjEyOiJ2ZWhpY2xlX3R5cGUiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjEwOiJ1c2FnZV9tb2RlIjthOjE6e3M6NToidmFsdWUiO047fXM6OToiaXNfYWN0aXZlIjthOjE6e3M6NToidmFsdWUiO047fX1zOjQwOiIyNWI5OTc3MmYwNTU2ZDE3NzA5MTg5ZTcxY2NlZDhiMV9jb2x1bW5zIjthOjk6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjk6Ik5hbWEgc2VzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoia2VtYWp1YW4iO3M6NToibGFiZWwiO3M6ODoiS2VtYWp1YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6InNlbGlzaWgiO3M6NToibGFiZWwiO3M6NzoiU2VsaXNpaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NjoiRGlidWF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRlZF9hdCI7czo1OiJsYWJlbCI7czoxNDoiTXVsYWkgZGloaXR1bmciO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImFkanVzdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjE2OiJTdG9rIGRpc2VzdWFpa2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE5OiJhZGp1c3RlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjE2OiJEaXNlc3VhaWthbiBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjI1Yjk5NzcyZjA1NTZkMTc3MDkxODllNzFjY2VkOGIxX2ZpbHRlcnMiO2E6NDp7czo3OiJ0ZXJidWthIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoyMDoibWVudW5nZ3VfcGVueWVzdWFpYW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjY6InN0YXR1cyI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTQ6InNjb3BlX2NhdGVnb3J5IjthOjE6e3M6NToidmFsdWUiO047fX1zOjQwOiJhNWFhYTcwYzExZjBmNjJlNTgyZmYzZjA5Yzg1OTM5Y19jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpdGVtX25hbWUiO3M6NToibGFiZWwiO3M6NjoiQmFyYW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJjYXRhdGFuIjtzOjU6ImxhYmVsIjtzOjE1OiJNZW51cnV0IGNhdGF0YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE2OiJjb3VudGVkX3F1YW50aXR5IjtzOjU6ImxhYmVsIjtzOjE0OiJIaXR1bmdhbiBmaXNpayI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoic2VsaXNpaCI7czo1OiJsYWJlbCI7czo3OiJTZWxpc2loIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJub3RlcyI7czo1OiJsYWJlbCI7czoxMDoiS2V0ZXJhbmdhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InRyYW5zYWN0aW9uLmNvZGUiO3M6NToibGFiZWwiO3M6MTQ6Ik11dGFzaSBrb3Jla3NpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImE1YWFhNzBjMTFmMGY2MmU1ODJmZjNmMDljODU5MzljX2ZpbHRlcnMiO2E6Mjp7czoxNDoiYmVsdW1fZGloaXR1bmciO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjc6InNlbGlzaWgiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MToiYTVhYWE3MGMxMWYwZjYyZTU4MmZmM2YwOWM4NTkzOWNfcGVyX3BhZ2UiO3M6MjoiNTAiO3M6NDA6ImIyMGM5Yjk4OGRkNWI2NGYzYjM3NTRmYTIzOWJjZGY4X2NvbHVtbnMiO2E6Nzp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJmaXNjYWxfeWVhciI7czo1OiJsYWJlbCI7czo1OiJUYWh1biI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImRlcGFydG1lbnQubmFtZSI7czo1OiJsYWJlbCI7czoxMDoiRGVwYXJ0ZW1lbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImNhdGVnb3J5Lm5hbWUiO3M6NToibGFiZWwiO3M6ODoiS2F0ZWdvcmkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImFtb3VudCI7czo1OiJsYWJlbCI7czo0OiJQYWd1IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJyZWFsaXNhc2kiO3M6NToibGFiZWwiO3M6OToiUmVhbGlzYXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJrZWFkYWFuIjtzOjU6ImxhYmVsIjtzOjg6IlRlcnBha2FpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJub3RlcyI7czo1OiJsYWJlbCI7czo3OiJDYXRhdGFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImIyMGM5Yjk4OGRkNWI2NGYzYjM3NTRmYTIzOWJjZGY4X2ZpbHRlcnMiO2E6NDp7czoxMToiZmlzY2FsX3llYXIiO2E6MTp7czo1OiJ2YWx1ZSI7czo0OiIyMDI2Ijt9czoxMzoiZGVwYXJ0bWVudF9pZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjE5OiJleHBlbnNlX2NhdGVnb3J5X2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTA6Imxld2F0X3BhZ3UiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiOWVhZTg3NGU3N2ZkZjZjMjllNzcwODdmOTYyNWI4MTJfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InZlbmRvci5uYW1lIjtzOjU6ImxhYmVsIjtzOjc6IlJla2FuYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJpbnZvaWNlX2RhdGUiO3M6NToibGFiZWwiO3M6MTQ6IlRhbmdnYWwgZmFrdHVyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJkdWVfZGF0ZSI7czo1OiJsYWJlbCI7czoxMToiSmF0dWggdGVtcG8iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRvdGFsIjtzOjU6ImxhYmVsIjtzOjU6Ik5pbGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJwYWlkX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiRGliYXlhciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoicGF5bWVudF9yZWZlcmVuY2UiO3M6NToibGFiZWwiO3M6MTY6IkJ1a3RpIHBlbWJheWFyYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NzoiRGljYXRhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI5ZWFlODc0ZTc3ZmRmNmMyOWU3NzA4N2Y5NjI1YjgxMl9maWx0ZXJzIjthOjY6e3M6NzoidGVyYnVrYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MjA6Im1lbnVuZ2d1X3BlcnNldHVqdWFuIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoxOToibWVudW5nZ3VfcGVtYmF5YXJhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6OToidGVybGFtYmF0IjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czo2OiJzdGF0dXMiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjk6InZlbmRvcl9pZCI7YToxOntzOjU6InZhbHVlIjtOO319czo0MDoiZWU3NGI2ZmI3MWZmOTU1NzM5YTMwYjZiMTIxYmE1YWFfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImVtcGxveWVlLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czo3OiJQZW1vaG9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0aXRsZSI7czo1OiJsYWJlbCI7czo5OiJVbnR1ayBhcGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRvdGFsIjtzOjU6ImxhYmVsIjtzOjU6Ik5pbGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3VibWl0dGVkX2F0IjtzOjU6ImxhYmVsIjtzOjg6IkRpYWp1a2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJwYWlkX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiRGlnYW50aSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoicGF5bWVudF9yZWZlcmVuY2UiO3M6NToibGFiZWwiO3M6MTQ6IkJ1a3RpIHRyYW5zZmVyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjY6IkRpYnVhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJlZTc0YjZmYjcxZmY5NTU3MzlhMzBiNmIxMjFiYTVhYV9maWx0ZXJzIjthOjY6e3M6NzoidGVyYnVrYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTU6Im1lbnVuZ2d1X2F0YXNhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTE6Im1lbnVuZ2d1X2dhIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoxOToibWVudW5nZ3VfcGVtYmF5YXJhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6Njoic3RhdHVzIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxMzoiZGVwYXJ0bWVudF9pZCI7YToxOntzOjU6InZhbHVlIjtOO319czo0MDoiZTMyMjNkZGY2MjJhOTg4NzA1YWUyMWViNzBjZTE5NzhfY29sdW1ucyI7YToxMDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJlbXBsb3llZS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTY6IlBlbmFuZ2d1bmcgamF3YWIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJkZXN0aW5hdGlvbiI7czo1OiJsYWJlbCI7czo2OiJUdWp1YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InVhbmdfbXVrYSI7czo1OiJsYWJlbCI7czo5OiJVYW5nIG11a2EiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJwZW55ZWxlc2FpYW4iO3M6NToibGFiZWwiO3M6MTI6IlBlbnllbGVzYWlhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicmVhbGlzYXNpIjtzOjU6ImxhYmVsIjtzOjE2OiJCaWF5YSBzZWJlbmFybnlhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InJvbWJvbmdhbiI7czo1OiJsYWJlbCI7czoxNDoiWWFuZyBiZXJhbmdrYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToidHJhbnNwb3J0IjtzOjU6ImxhYmVsIjtzOjE0OiJDYXJhIGJlcmFuZ2thdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfZGF0ZSI7czo1OiJsYWJlbCI7czo5OiJCZXJhbmdrYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZTMyMjNkZGY2MjJhOTg4NzA1YWUyMWViNzBjZTE5NzhfZmlsdGVycyI7YTozOntzOjY6InN0YXR1cyI7YToxOntzOjU6InZhbHVlIjtOO31zOjEzOiJkZXBhcnRtZW50X2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTg6Im1lbnVuZ2d1X3VhbmdfbXVrYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiJlYTA3NmU4NTA4MTU2YjRlZWY2ZTc3ZGM3NjY1YTQ4Zl9jb2x1bW5zIjthOjU6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoiZXhwZW5zZV9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IlRhbmdnYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNhdGVnb3J5IjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZGVzY3JpcHRpb24iO3M6NToibGFiZWwiO3M6MTA6IktldGVyYW5nYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6ImJ1a3RpIjtzOjU6ImxhYmVsIjtzOjU6IkJ1a3RpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6NToiTmlsYWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6ImVhMDc2ZTg1MDgxNTZiNGVlZjZlNzdkYzc2NjVhNDhmX2ZpbHRlcnMiO2E6MTp7czo4OiJjYXRlZ29yeSI7YToxOntzOjU6InZhbHVlIjtOO319czo0MDoiOTQyNWZiZDRhM2RlZTI1NzQ4NjBkMzY0YmQ1YjI4OWJfY29sdW1ucyI7YTo4OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6Imluc3BlY3Rpb25fZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJUYW5nZ2FsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJrZW1hanVhbiI7czo1OiJsYWJlbCI7czo4OiJLZW1hanVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NjoidGVtdWFuIjtzOjU6ImxhYmVsIjtzOjY6IlRlbXVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NjoiRGlidWF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZmluaXNoZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IkRpc2VsZXNhaWthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjExOiJEaWJ1YXQgb2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI5NDI1ZmJkNGEzZGVlMjU3NDg2MGQzNjRiZDViMjg5Yl9maWx0ZXJzIjthOjI6e3M6Njoic3RhdHVzIjthOjE6e3M6NToidmFsdWUiO047fXM6MTA6ImFkYV90ZW11YW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiNzUzOTE5ZjA0ZjRlMWRmMjgxOGEyYjg3ZDZmMDViNzNfY29sdW1ucyI7YTo1OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiYXJlYV9uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IkFyZWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFmZl9uYW1lIjtzOjU6ImxhYmVsIjtzOjE2OiJQZW5hbmdndW5nIGphd2FiIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJyZXN1bHQiO3M6NToibGFiZWwiO3M6NToiSGFzaWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5vdGVzIjtzOjU6ImxhYmVsIjtzOjc6IkNhdGF0YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImZpbGVfcGF0aCI7czo1OiJsYWJlbCI7czo0OiJGb3RvIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiI3NTM5MTlmMDRmNGUxZGYyODE4YTJiODdkNmYwNWI3M19maWx0ZXJzIjthOjI6e3M6MTU6ImJlbHVtX2RpcGVyaWtzYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6NjoidGVtdWFuIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6IjNkMjNjMGVkYjk0ZjUyYTM2MGU1MTUyMDNlOGUxZmM4X2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzaGlmdF9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IlRhbmdnYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InNoaWZ0IjtzOjU6ImxhYmVsIjtzOjU6IlNoaWZ0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJwZXR1Z2FzIjtzOjU6ImxhYmVsIjtzOjExOiJEaWphZHdhbGthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImF0dGVuZGFuY2UiO3M6NToibGFiZWwiO3M6OToiS2VoYWRpcmFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czozOiJqYW0iO3M6NToibGFiZWwiO3M6ODoiSmFtIGphZ2EiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJpbmNpZGVudHNfY291bnQiO3M6NToibGFiZWwiO3M6NzoiSW5zaWRlbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaXN1c3VuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiM2QyM2MwZWRiOTRmNTJhMzYwZTUxNTIwM2U4ZTFmYzhfZmlsdGVycyI7YTozOntzOjU6InNoaWZ0IjthOjE6e3M6NToidmFsdWUiO047fXM6MTA6ImF0dGVuZGFuY2UiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxMDoidGVydGluZ2dhbCI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiI1Y2EzNzRhOTQ5MjU1NDAyOGUwMGI4OTE3ZTAzNTg3NF9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToib2NjdXJyZWRfYXQiO3M6NToibGFiZWwiO3M6MTQ6Ildha3R1IGtlamFkaWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJjYXRlZ29yeSI7czo1OiJsYWJlbCI7czo1OiJKZW5pcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoic2V2ZXJpdHkiO3M6NToibGFiZWwiO3M6NToiQmVyYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJ0aW5kYWtfbGFuanV0IjtzOjU6ImxhYmVsIjtzOjEzOiJUaW5kYWsgbGFuanV0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJwZWxhcG9yIjtzOjU6ImxhYmVsIjtzOjEwOiJEaWxhcG9ya2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZGVzY3JpcHRpb24iO3M6NToibGFiZWwiO3M6NjoiVXJhaWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJjcmVhdGVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IkRpY2F0YXQgb2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI1Y2EzNzRhOTQ5MjU1NDAyOGUwMGI4OTE3ZTAzNTg3NF9maWx0ZXJzIjthOjM6e3M6ODoiY2F0ZWdvcnkiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czo4OiJzZXZlcml0eSI7YToxOntzOjU6InZhbHVlIjtOO31zOjg6Im1lbnVuZ2d1IjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6IjVmNDhjMTFmMGE2MWIxZGMzYjQ3ZDUzZjMxYjRlYWIzX2NvbHVtbnMiO2E6MTA6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjEyOiJOb21vciBhZ2VuZGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImRpcmVjdGlvbiI7czo1OiJsYWJlbCI7czo0OiJBcmFoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJzdWJqZWN0IjtzOjU6ImxhYmVsIjtzOjc6IlBlcmloYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJsb2dnZWRfZGF0ZSI7czo1OiJsYWJlbCI7czoyMToiRGl0ZXJpbWEgYXRhdSBkaWtpcmltIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJrZWFkYWFuIjtzOjU6ImxhYmVsIjtzOjc6IktlYWRhYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InR1anVhbiI7czo1OiJsYWJlbCI7czoxMToiVW50dWsgc2lhcGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNhdGVnb3J5IjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJsZXR0ZXJfZGF0ZSI7czo1OiJsYWJlbCI7czoxMzoiVGFuZ2dhbCBzdXJhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJub3RlcyI7czo1OiJsYWJlbCI7czo3OiJDYXRhdGFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJjcmVhdGVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IkRpY2F0YXQgb2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI1ZjQ4YzExZjBhNjFiMWRjM2I0N2Q1M2YzMWI0ZWFiM19maWx0ZXJzIjthOjM6e3M6OToiZGlyZWN0aW9uIjthOjE6e3M6NToidmFsdWUiO047fXM6ODoiY2F0ZWdvcnkiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxOToibWVudW5nZ3VfZGlzZXJhaGthbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiJiZGNjOGNlODBkZjFkNWE4YWQyZTQ5NDBhMDZkM2Q1NF9jb2x1bW5zIjthOjEwOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InJlY2lwaWVudF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IlR1anVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImRlcGFydG1lbnQubmFtZSI7czo1OiJsYWJlbCI7czo4OiJEaWJlYmFuaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjc6IktlYWRhYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRvdGFsIjtzOjU6ImxhYmVsIjtzOjU6IkJpYXlhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoicmVxdWVzdF9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IkRpbWludGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJzaGlwcGVkX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiRGlraXJpbSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToicmVxdWVzdGVyLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czoxMjoiWWFuZyBtZW1pbnRhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo4O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6ImJlcmF0IjtzOjU6ImxhYmVsIjtzOjU6IkJlcmF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InBlcmtpcmFhbiI7czo1OiJsYWJlbCI7czoxNToiUGVya2lyYWFuIGJpYXlhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImJkY2M4Y2U4MGRmMWQ1YThhZDJlNDk0MGEwNmQzZDU0X2ZpbHRlcnMiO2E6Mzp7czo2OiJzdGF0dXMiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxMzoiZGVwYXJ0bWVudF9pZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjg6Im1lbnVuZ2d1IjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6ImQ4NmNjZGQ4Njc2YzBjMjk5NWVlMjQwZmZiZGRlNTgwX2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiS29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo4OiJLYXRlZ29yaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImFjY291bnRfYXNzZXQiO3M6NToibGFiZWwiO3M6MTU6IkFrdW4gYXNldCB0ZXRhcCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjEwOiJLZXRlcmFuZ2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJhc3NldHNfY291bnQiO3M6NToibGFiZWwiO3M6MTE6Ikp1bWxhaCBhc2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJ0YXhfZ3JvdXAiO3M6NToibGFiZWwiO3M6MTQ6IktlbG9tcG9rIHBhamFrIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJ1c2VmdWxfbGlmZV9tb250aHMiO3M6NToibGFiZWwiO3M6MTI6Ik1hc2EgbWFuZmFhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NzoiRGlwYWthaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZjNjYzU4OWE1M2E3YTdkYjljM2UzMjNmODczMmE1NDdfY29sdW1ucyI7YToxMTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE2OiJ0cmFuc2FjdGlvbl9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IlRhbmdnYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiaXRlbS5uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IkJhcmFuZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidHlwZSI7czo1OiJsYWJlbCI7czo1OiJKZW5pcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoicXVhbnRpdHkiO3M6NToibGFiZWwiO3M6NjoiSnVtbGFoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToiZGVwYXJ0bWVudC5uYW1lIjtzOjU6ImxhYmVsIjtzOjEwOiJEZXBhcnRlbWVuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiZW1wbG95ZWUuZnVsbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjEzOiJEaXRlcmltYSBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6InN1cHBsaWVyIjtzOjU6ImxhYmVsIjtzOjc6IlBlbWFzb2siO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicmVmZXJlbmNlIjtzOjU6ImxhYmVsIjtzOjEyOiJEb2t1bWVuIGFzYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVuaXRfcHJpY2UiO3M6NToibGFiZWwiO3M6MTI6IkhhcmdhIHNhdHVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MTA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImNyZWF0ZWRCeVVzZXIubmFtZSI7czo1OiJsYWJlbCI7czoxMjoiRGljYXRhdCBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImYzY2M1ODlhNTNhN2E3ZGI5YzNlMzIzZjg3MzJhNTQ3X2ZpbHRlcnMiO2E6NDp7czo0OiJ0eXBlIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxNDoic3VwcGx5X2l0ZW1faWQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxMzoiZGVwYXJ0bWVudF9pZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjE1OiJyZW50YW5nX3RhbmdnYWwiO2E6Mjp7czo0OiJkYXJpIjtOO3M6Njoic2FtcGFpIjtOO319fXM6ODoiZmlsYW1lbnQiO2E6MDp7fX0=	1788884386
IlKODThHTl6BvQRfCFNq0TVU4zruOY24yT7y38Fr	2	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Claude/1.46388.4 Chrome/148.0.7778.280 Safari/537.36 MSIX	YTo4OntzOjY6Il90b2tlbiI7czo0MDoiYWdRQmlJWkVIcmpKVWlCbjJuazlMVjJMbXJiMGlnamJodzlKUlJiZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ0OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vYnVzaW5lc3MtdHJpcHMvMiI7czo1OiJyb3V0ZSI7czo0NDoiZmlsYW1lbnQuYWRtaW4ucmVzb3VyY2VzLmJ1c2luZXNzLXRyaXBzLnZpZXciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI3NTFkMWY0ZGNhZmMwNzNjMTlmMjA4ODE3NmMzMTA3MzNlNjczMTgwMDJlZWU3MmFmZDYzNzllNGQ2OTU0MWUxIjtzOjY6InRhYmxlcyI7YTo1Mjp7czo0MDoiNmQ5MjY3NzI5YTI0ZDA5ZTc2M2EyZTMxNGY3YmYzYzJfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImVtcGxveWVlLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czo3OiJQZW1vaG9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJwdXJwb3NlIjtzOjU6ImxhYmVsIjtzOjE5OiJVbnR1ayBrZXBlcmx1YW4gYXBhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToibGluZXNfY291bnQiO3M6NToibGFiZWwiO3M6MzoiSXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic3VibWl0dGVkX2F0IjtzOjU6ImxhYmVsIjtzOjg6IkRpYWp1a2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc3N1ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkRpc2VyYWhrYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6Imlzc3VlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjE1OiJEaXNlcmFoa2FuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6NjoiRGlidWF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjZkOTI2NzcyOWEyNGQwOWU3NjNhMmUzMTRmN2JmM2MyX2ZpbHRlcnMiO2E6NTp7czo3OiJ0ZXJidWthIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoxNToibWVudW5nZ3VfYXRhc2FuIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czoxOToibWVudW5nZ3VfcGVueWVyYWhhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6Njoic3RhdHVzIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxMzoiZGVwYXJ0bWVudF9pZCI7YToxOntzOjU6InZhbHVlIjtOO319czo0MDoiY2FmODNiZTc4YmE4ZmMyNWJmMzQ4M2VjMGNkZThkZDNfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJLb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjY6IkJhcmFuZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiY2F0ZWdvcnkiO3M6NToibGFiZWwiO3M6ODoiS2F0ZWdvcmkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI1OiJ0cmFuc2FjdGlvbnNfc3VtX3F1YW50aXR5IjtzOjU6ImxhYmVsIjtzOjQ6IlN0b2siO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJtaW5pbXVtX3N0b2NrIjtzOjU6ImxhYmVsIjtzOjc6Ik1pbmltdW0iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6ImtlYWRhYW4iO3M6NToibGFiZWwiO3M6NzoiS2VhZGFhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImxvY2F0aW9uLmNvZGUiO3M6NToibGFiZWwiO3M6MTM6IlRlbXBhdCBzaW1wYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6Imxhc3RfcHJpY2UiO3M6NToibGFiZWwiO3M6MjE6IkhhcmdhIHNhdHVhbiB0ZXJha2hpciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NzoiRGlwYWthaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJjYWY4M2JlNzhiYThmYzI1YmYzNDgzZWMwY2RlOGRkM19maWx0ZXJzIjthOjQ6e3M6ODoiY2F0ZWdvcnkiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjEzOiJwZXJsdV9kaXBlc2FuIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9czo1OiJoYWJpcyI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTM6InRpZGFrX2RpcGFrYWkiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiZTBkMGEzMWI0ZDhhZjE2YWFmMmYwMTY3NmZkOTEwYTFfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJLb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjEwOiJEZXBhcnRlbWVuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToicGFyZW50Lm5hbWUiO3M6NToibGFiZWwiO3M6NToiSW5kdWsiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6ImhlYWQuZnVsbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IktlcGFsYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImVtcGxveWVlc19jb3VudCI7czo1OiJsYWJlbCI7czo4OiJLYXJ5YXdhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImNvc3RfY2VudGVyIjtzOjU6ImxhYmVsIjtzOjExOiJDb3N0IGNlbnRlciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NToiQWt0aWYiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjRjMTY4ZmFkYjZhYWJhNGU0NGE0M2Q5MmVkZDk4OWFjX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6Iml0ZW0ubmFtZSI7czo1OiJsYWJlbCI7czo2OiJCYXJhbmciO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJxdWFudGl0eV9yZXF1ZXN0ZWQiO3M6NToibGFiZWwiO3M6NzoiRGltaW50YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6InF1YW50aXR5X2lzc3VlZCI7czo1OiJsYWJlbCI7czoxMDoiRGlzZXJhaGthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoic3RvayI7czo1OiJsYWJlbCI7czoxMzoiU3RvayBzZWthcmFuZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6MTA6IktldGVyYW5nYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE2OiJ0cmFuc2FjdGlvbi5jb2RlIjtzOjU6ImxhYmVsIjtzOjExOiJNdXRhc2kgc3RvayI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJmM2NjNTg5YTUzYTdhN2RiOWMzZTMyM2Y4NzMyYTU0N19jb2x1bW5zIjthOjExOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InRyYW5zYWN0aW9uX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiVGFuZ2dhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpdGVtLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQmFyYW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJ0eXBlIjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJxdWFudGl0eSI7czo1OiJsYWJlbCI7czo2OiJKdW1sYWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJkZXBhcnRtZW50Lm5hbWUiO3M6NToibGFiZWwiO3M6MTA6IkRlcGFydGVtZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJlbXBsb3llZS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTM6IkRpdGVyaW1hIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoic3VwcGxpZXIiO3M6NToibGFiZWwiO3M6NzoiUGVtYXNvayI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJyZWZlcmVuY2UiO3M6NToibGFiZWwiO3M6MTI6IkRva3VtZW4gYXNhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidW5pdF9wcmljZSI7czo1OiJsYWJlbCI7czoxMjoiSGFyZ2Egc2F0dWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aToxMDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaWNhdGF0IG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZjNjYzU4OWE1M2E3YTdkYjljM2UzMjNmODczMmE1NDdfZmlsdGVycyI7YTo0OntzOjQ6InR5cGUiO2E6MTp7czo2OiJ2YWx1ZXMiO2E6MDp7fX1zOjE0OiJzdXBwbHlfaXRlbV9pZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjEzOiJkZXBhcnRtZW50X2lkIjthOjE6e3M6NToidmFsdWUiO047fXM6MTU6InJlbnRhbmdfdGFuZ2dhbCI7YToyOntzOjQ6ImRhcmkiO047czo2OiJzYW1wYWkiO047fX1zOjQwOiJiMjBjOWI5ODhkZDViNjRmM2IzNzU0ZmEyMzliY2RmOF9jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZmlzY2FsX3llYXIiO3M6NToibGFiZWwiO3M6NToiVGFodW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJkZXBhcnRtZW50Lm5hbWUiO3M6NToibGFiZWwiO3M6MTA6IkRlcGFydGVtZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJjYXRlZ29yeS5uYW1lIjtzOjU6ImxhYmVsIjtzOjg6IkthdGVnb3JpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6NDoiUGFndSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicmVhbGlzYXNpIjtzOjU6ImxhYmVsIjtzOjk6IlJlYWxpc2FzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoia2VhZGFhbiI7czo1OiJsYWJlbCI7czo4OiJUZXJwYWthaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJiMjBjOWI5ODhkZDViNjRmM2IzNzU0ZmEyMzliY2RmOF9maWx0ZXJzIjthOjQ6e3M6MTE6ImZpc2NhbF95ZWFyIjthOjE6e3M6NToidmFsdWUiO3M6NDoiMjAyNiI7fXM6MTM6ImRlcGFydG1lbnRfaWQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxOToiZXhwZW5zZV9jYXRlZ29yeV9pZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjEwOiJsZXdhdF9wYWd1IjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6ImY4MWU3MTI4OWQ5ODI2YTc4NmM4YWRiZWUxZDFhNjU2X2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NDoiS29kZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo4OiJLYXRlZ29yaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic291cmNlIjtzOjU6ImxhYmVsIjtzOjE2OiJTdW1iZXIgcmVhbGlzYXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoiYWNjb3VudF9jb2RlIjtzOjU6ImxhYmVsIjtzOjEwOiJOb21vciBha3VuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiYnVkZ2V0c19jb3VudCI7czo1OiJsYWJlbCI7czo0OiJQYWd1IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NzoiRGlwYWthaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZjgxZTcxMjg5ZDk4MjZhNzg2YzhhZGJlZTFkMWE2NTZfZmlsdGVycyI7YTozOntzOjY6InNvdXJjZSI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTU6ImJlbHVtX2RpcGV0YWthbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6OToiaXNfYWN0aXZlIjthOjE6e3M6NToidmFsdWUiO047fX1zOjQwOiIxMDhjY2I3MmRkYzVlNzA5M2QwZDFiODY1MDM2MTM2M19jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czozOiJuaXAiO3M6NToibGFiZWwiO3M6MzoiTklQIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJmdWxsX25hbWUiO3M6NToibGFiZWwiO3M6NDoiTmFtYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImRlcGFydG1lbnQubmFtZSI7czo1OiJsYWJlbCI7czoxMDoiRGVwYXJ0ZW1lbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6ImVtcGxveW1lbnRfc3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVzZXIuZW1haWwiO3M6NToibGFiZWwiO3M6NDoiQWt1biI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NToiQWt0aWYiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDE6IjEwOGNjYjcyZGRjNWU3MDkzZDBkMWI4NjUwMzYxMzYzX3Blcl9wYWdlIjtpOjI1O3M6NDA6IjE4YzRlZWIyM2E4ZTIyODgwMjg1YjNhZWFhNjBkN2I3X2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJlbXBsb3llZS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6NzoiUGVtb2hvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoicHVycG9zZSI7czo1OiJsYWJlbCI7czoxOToiVW50dWsga2VwZXJsdWFuIGFwYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImxpbmVzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjM6IklzaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjg6Ik1lbnVuZ2d1IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJzdG9rIjtzOjU6ImxhYmVsIjtzOjEzOiJLZXNpYXBhbiBzdG9rIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiJmZjYxYzI3YzM2YTcyNzQ0MTVkYzg0ZTUxMzVjMjJhY19jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjQ6IktvZGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NjoiQmFyYW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJzdG9rIjtzOjU6ImxhYmVsIjtzOjQ6IlN0b2siO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJtaW5pbXVtX3N0b2NrIjtzOjU6ImxhYmVsIjtzOjc6Ik1pbmltdW0iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6Imt1cmFuZyI7czo1OiJsYWJlbCI7czo2OiJLdXJhbmciO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6ImtlYWRhYW4iO3M6NToibGFiZWwiO3M6NzoiS2VhZGFhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZjA1MGQzYzVlYWE2NWVlZjNkMDllODE3NWU1NTM3MjhfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoicGVtYXNvayI7czo1OiJsYWJlbCI7czo3OiJQZW1hc29rIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiZGVzY3JpcHRpb24iO3M6NToibGFiZWwiO3M6MTk6IlVudHVrIGtlcGVybHVhbiBhcGEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5pbGFpIjtzOjU6ImxhYmVsIjtzOjU6Ik5pbGFpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoib3JkZXJfZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJUYW5nZ2FsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJkaXRlcmltYSI7czo1OiJsYWJlbCI7czoxNDoiU3VkYWggZGl0ZXJpbWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InZlbmRvci5uYW1lIjtzOjU6ImxhYmVsIjtzOjE3OiJSZWthbmFuIHRlcmRhZnRhciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo2OiJEaWJ1YXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZjA1MGQzYzVlYWE2NWVlZjNkMDllODE3NWU1NTM3MjhfZmlsdGVycyI7YTo2OntzOjc6InRlcmJ1a2EiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjIwOiJtZW51bmdndV9wZXJzZXR1anVhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTU6Im1lbnVuZ2d1X2JhcmFuZyI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6NDoia2luZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjY6InN0YXR1cyI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6OToidmVuZG9yX2lkIjthOjE6e3M6NToidmFsdWUiO047fX1zOjQwOiI0YWY0MjJlZTczYjBlOTExNmQ1MzVhMjIyZWE3NWQ2NF9jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpdGVtLm5hbWUiO3M6NToibGFiZWwiO3M6NjoiQmFyYW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoicXVhbnRpdHlfb3JkZXJlZCI7czo1OiJsYWJlbCI7czo3OiJEaXBlc2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJkaXRlcmltYSI7czo1OiJsYWJlbCI7czoxMjoiU3VkYWggZGF0YW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJzaXNhIjtzOjU6ImxhYmVsIjtzOjQ6IlNpc2EiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6InN1YnRvdGFsIjtzOjU6ImxhYmVsIjtzOjY6Ikp1bWxhaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6MTA6IktldGVyYW5nYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiNWQ5NTY2MDMwNzk5ZmYxZGQ3ZDU0YmFkM2VhN2JjZDdfY29sdW1ucyI7YTo2OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InJlY2VpcHRfZGF0ZSI7czo1OiJsYWJlbCI7czoxNDoiVGFuZ2dhbCBkYXRhbmciO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjM6ImlzaSI7czo1OiJsYWJlbCI7czoxNToiQXBhIHlhbmcgZGF0YW5nIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJuaWxhaSI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjA6ImRlbGl2ZXJ5X25vdGVfbnVtYmVyIjtzOjU6ImxhYmVsIjtzOjExOiJTdXJhdCBqYWxhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6InJlY2VpdmVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTM6IkRpdGVyaW1hIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjJjMDY0YzU3ZWRhZjQ4Yzg3OGFmNjQ4MGRiOWE5MTEwX2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJjYXRlZ29yeS5uYW1lIjtzOjU6ImxhYmVsIjtzOjE0OiJLYXRlZ29yaSBiaWF5YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImRlcGFydG1lbnQubmFtZSI7czo1OiJsYWJlbCI7czoxMDoiRGVwYXJ0ZW1lbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjEwOiJLZXRlcmFuZ2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6NToiTmlsYWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjI1Yjk5NzcyZjA1NTZkMTc3MDkxODllNzFjY2VkOGIxX2NvbHVtbnMiO2E6OTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6OToiTmFtYSBzZXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJrZW1hanVhbiI7czo1OiJsYWJlbCI7czo4OiJLZW1hanVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoic2VsaXNpaCI7czo1OiJsYWJlbCI7czo3OiJTZWxpc2loIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo2OiJEaWJ1YXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFydGVkX2F0IjtzOjU6ImxhYmVsIjtzOjE0OiJNdWxhaSBkaWhpdHVuZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiYWRqdXN0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTY6IlN0b2sgZGlzZXN1YWlrYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6ImFkanVzdGVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTY6IkRpc2VzdWFpa2FuIG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiMjViOTk3NzJmMDU1NmQxNzcwOTE4OWU3MWNjZWQ4YjFfZmlsdGVycyI7YTo0OntzOjc6InRlcmJ1a2EiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjIwOiJtZW51bmdndV9wZW55ZXN1YWlhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6Njoic3RhdHVzIjthOjE6e3M6NjoidmFsdWVzIjthOjA6e319czoxNDoic2NvcGVfY2F0ZWdvcnkiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9fXM6NDA6ImE1YWFhNzBjMTFmMGY2MmU1ODJmZjNmMDljODU5MzljX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6Iml0ZW1fbmFtZSI7czo1OiJsYWJlbCI7czo2OiJCYXJhbmciO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjc6ImNhdGF0YW4iO3M6NToibGFiZWwiO3M6MTU6Ik1lbnVydXQgY2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6ImNvdW50ZWRfcXVhbnRpdHkiO3M6NToibGFiZWwiO3M6MTQ6IkhpdHVuZ2FuIGZpc2lrIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJzZWxpc2loIjtzOjU6ImxhYmVsIjtzOjc6IlNlbGlzaWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5vdGVzIjtzOjU6ImxhYmVsIjtzOjEwOiJLZXRlcmFuZ2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoidHJhbnNhY3Rpb24uY29kZSI7czo1OiJsYWJlbCI7czoxNDoiTXV0YXNpIGtvcmVrc2kiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiYTVhYWE3MGMxMWYwZjYyZTU4MmZmM2YwOWM4NTkzOWNfZmlsdGVycyI7YToyOntzOjE0OiJiZWx1bV9kaWhpdHVuZyI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6Nzoic2VsaXNpaCI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiIwNjNiMmRkM2NjYTdmYzIwNjdlZDY4NzQxN2U3YTFjZV9jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1hIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImtpbmQiO3M6NToibGFiZWwiO3M6MTE6IkJlcnR1Z2FzIGRpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiYXJlYXNfY291bnQiO3M6NToibGFiZWwiO3M6NDoiQXJlYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoidGVsZXBvbiI7czo1OiJsYWJlbCI7czo3OiJUZWxlcG9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfZGF0ZSI7czo1OiJsYWJlbCI7czoxNDoiTXVsYWkgYmVydHVnYXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImlzX2FjdGl2ZSI7czo1OiJsYWJlbCI7czo4OiJCZXJ0dWdhcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiIwNjNiMmRkM2NjYTdmYzIwNjdlZDY4NzQxN2U3YTFjZV9maWx0ZXJzIjthOjI6e3M6NDoia2luZCI7YToxOntzOjU6InZhbHVlIjtOO31zOjk6ImlzX2FjdGl2ZSI7YToxOntzOjU6InZhbHVlIjtOO319czo0MDoiYWM4NjM3MzdmZTkzMzg0YzJkZjliNDExMWQ2M2UxNzdfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo0OiJLb2RlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IkFyZWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNhdGVnb3J5IjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJmcmVxdWVuY3kiO3M6NToibGFiZWwiO3M6MTE6IkRpYmVyc2loa2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoicGVuYW5nZ3VuZ19qYXdhYiI7czo1OiJsYWJlbCI7czoxNjoiUGVuYW5nZ3VuZyBqYXdhYiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiaXNfYWN0aXZlIjtzOjU6ImxhYmVsIjtzOjc6IkRpcGFrYWkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5vdGVzIjtzOjU6ImxhYmVsIjtzOjc6IkNhdGF0YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiYWM4NjM3MzdmZTkzMzg0YzJkZjliNDExMWQ2M2UxNzdfZmlsdGVycyI7YTozOntzOjg6ImNhdGVnb3J5IjthOjE6e3M6NToidmFsdWUiO047fXM6OToiZnJlcXVlbmN5IjthOjE6e3M6NToidmFsdWUiO047fXM6MjI6InRhbnBhX3BlbmFuZ2d1bmdfamF3YWIiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiNzUzOTE5ZjA0ZjRlMWRmMjgxOGEyYjg3ZDZmMDViNzNfY29sdW1ucyI7YTo1OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiYXJlYV9uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IkFyZWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFmZl9uYW1lIjtzOjU6ImxhYmVsIjtzOjE2OiJQZW5hbmdndW5nIGphd2FiIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJyZXN1bHQiO3M6NToibGFiZWwiO3M6NToiSGFzaWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5vdGVzIjtzOjU6ImxhYmVsIjtzOjc6IkNhdGF0YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImZpbGVfcGF0aCI7czo1OiJsYWJlbCI7czo0OiJGb3RvIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiI3NTM5MTlmMDRmNGUxZGYyODE4YTJiODdkNmYwNWI3M19maWx0ZXJzIjthOjI6e3M6MTU6ImJlbHVtX2RpcGVyaWtzYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6NjoidGVtdWFuIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6ImVlNzRiNmZiNzFmZjk1NTczOWEzMGI2YjEyMWJhNWFhX2NvbHVtbnMiO2E6OTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJlbXBsb3llZS5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6NzoiUGVtb2hvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToidGl0bGUiO3M6NToibGFiZWwiO3M6OToiVW50dWsgYXBhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0b3RhbCI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InN1Ym1pdHRlZF9hdCI7czo1OiJsYWJlbCI7czo4OiJEaWFqdWthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicGFpZF9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IkRpZ2FudGkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6InBheW1lbnRfcmVmZXJlbmNlIjtzOjU6ImxhYmVsIjtzOjE0OiJCdWt0aSB0cmFuc2ZlciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo2OiJEaWJ1YXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiZWU3NGI2ZmI3MWZmOTU1NzM5YTMwYjZiMTIxYmE1YWFfZmlsdGVycyI7YTo2OntzOjc6InRlcmJ1a2EiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjE1OiJtZW51bmdndV9hdGFzYW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjExOiJtZW51bmdndV9nYSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTk6Im1lbnVuZ2d1X3BlbWJheWFyYW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjY6InN0YXR1cyI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTM6ImRlcGFydG1lbnRfaWQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9fXM6NDA6IjFmZmUyZTAxNWFhODY1MWJmOTEzMzBiMmQ5ZjA1MzAyX2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJleHBlbnNlX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiVGFuZ2dhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjEwOiJLZXRlcmFuZ2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJmaWxlX3BhdGgiO3M6NToibGFiZWwiO3M6NToiQnVrdGkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImFtb3VudCI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiYTlkY2ViZDE0MzRlY2JjNWE4YmU4ZTc5OThiNzZiYmVfY29sdW1ucyI7YTo4OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InBsYXRlX251bWJlciI7czo1OiJsYWJlbCI7czoxMjoiTm9tb3IgcG9saXNpIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJrZWFkYWFuIjtzOjU6ImxhYmVsIjtzOjE2OiJEb2t1bWVuIHRlcmRla2F0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoidmVoaWNsZV90eXBlIjtzOjU6ImxhYmVsIjtzOjU6IkplbmlzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidXNhZ2VfbW9kZSI7czo1OiJsYWJlbCI7czo5OiJQZW1ha2FpYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI1OiJhc3NldC5jdXN0b2RpYW4uZnVsbF9uYW1lIjtzOjU6ImxhYmVsIjtzOjE2OiJQZW5hbmdndW5nIGphd2FiIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjIzOiJkZWZhdWx0RHJpdmVyLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czoxMToiU29waXIgdGV0YXAiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6Imxhc3Rfb2RvbWV0ZXJfa20iO3M6NToibGFiZWwiO3M6ODoiT2RvbWV0ZXIiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6ImRvY3VtZW50c19jb3VudCI7czo1OiJsYWJlbCI7czo3OiJEb2t1bWVuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6ImE5ZGNlYmQxNDM0ZWNiYzVhOGJlOGU3OTk4Yjc2YmJlX2ZpbHRlcnMiO2E6NTp7czoxMToiamF0dWhfdGVtcG8iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO31zOjk6InRlcmxhbWJhdCI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fXM6MTI6InZlaGljbGVfdHlwZSI7YToxOntzOjY6InZhbHVlcyI7YTowOnt9fXM6MTA6InVzYWdlX21vZGUiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czo5OiJpc19hY3RpdmUiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9fXM6NDA6IjNlMzIzYzM4ZjVlYjU3ZDJmZjQ2NzM5YjExNTYyYzJmX2NvbHVtbnMiO2E6Nzp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6InR5cGUiO3M6NToibGFiZWwiO3M6NToiSmVuaXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJleHBpcmVzX2F0IjtzOjU6ImxhYmVsIjtzOjE0OiJCZXJsYWt1IHNhbXBhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoiYmVybGFrdSI7czo1OiJsYWJlbCI7czo3OiJLZWFkYWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiaXNzdWVkX2RhdGUiO3M6NToibGFiZWwiO3M6NjoiVGVyYml0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6Imlzc3VlciI7czo1OiJsYWJlbCI7czoxNjoiRGl0ZXJiaXRrYW4gb2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb3N0IjtzOjU6ImxhYmVsIjtzOjU6IkJpYXlhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic2l6ZV9ieXRlcyI7czo1OiJsYWJlbCI7czo4OiJQaW5kYWlhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI5NDI1ZmJkNGEzZGVlMjU3NDg2MGQzNjRiZDViMjg5Yl9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJjb2RlIjtzOjU6ImxhYmVsIjtzOjU6Ik5vbW9yIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToiaW5zcGVjdGlvbl9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IlRhbmdnYWwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImtlbWFqdWFuIjtzOjU6ImxhYmVsIjtzOjg6IktlbWFqdWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJ0ZW11YW4iO3M6NToibGFiZWwiO3M6NjoiVGVtdWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo2OiJEaWJ1YXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJmaW5pc2hlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoiRGlzZWxlc2Fpa2FuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE4OiJjcmVhdGVkQnlVc2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTE6IkRpYnVhdCBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6Ijk0MjVmYmQ0YTNkZWUyNTc0ODYwZDM2NGJkNWIyODliX2ZpbHRlcnMiO2E6Mjp7czo2OiJzdGF0dXMiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxMDoiYWRhX3RlbXVhbiI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiIzZDIzYzBlZGI5NGY1MmEzNjBlNTE1MjAzZThlMWZjOF9jb2x1bW5zIjthOjg6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic2hpZnRfZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJUYW5nZ2FsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJzaGlmdCI7czo1OiJsYWJlbCI7czo1OiJTaGlmdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoicGV0dWdhcyI7czo1OiJsYWJlbCI7czoxMToiRGlqYWR3YWxrYW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJhdHRlbmRhbmNlIjtzOjU6ImxhYmVsIjtzOjk6IktlaGFkaXJhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MzoiamFtIjtzOjU6ImxhYmVsIjtzOjg6IkphbSBqYWdhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNToiaW5jaWRlbnRzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjc6Ikluc2lkZW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6Im5vdGVzIjtzOjU6ImxhYmVsIjtzOjc6IkNhdGF0YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImNyZWF0ZWRCeVVzZXIubmFtZSI7czo1OiJsYWJlbCI7czoxMjoiRGlzdXN1biBvbGVoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjNkMjNjMGVkYjk0ZjUyYTM2MGU1MTUyMDNlOGUxZmM4X2ZpbHRlcnMiO2E6Mzp7czo1OiJzaGlmdCI7YToxOntzOjU6InZhbHVlIjtOO31zOjEwOiJhdHRlbmRhbmNlIjthOjE6e3M6NToidmFsdWUiO047fXM6MTA6InRlcnRpbmdnYWwiO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiNWQ3N2UyNGVlZDMyOGViMDY4Y2I4NzRhODA1MjUzY2ZfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czoxMDoiS2V0ZXJhbmdhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InNpemVfYnl0ZXMiO3M6NToibGFiZWwiO3M6NjoiVWt1cmFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToidXBsb2FkZWRCeVVzZXIubmFtZSI7czo1OiJsYWJlbCI7czoxMzoiRGl1bmdnYWggb2xlaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo4OiJEaXVuZ2dhaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiNWNhMzc0YTk0OTI1NTQwMjhlMDBiODkxN2UwMzU4NzRfY29sdW1ucyI7YTo4OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6Im9jY3VycmVkX2F0IjtzOjU6ImxhYmVsIjtzOjE0OiJXYWt0dSBrZWphZGlhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiY2F0ZWdvcnkiO3M6NToibGFiZWwiO3M6NToiSmVuaXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6InNldmVyaXR5IjtzOjU6ImxhYmVsIjtzOjU6IkJlcmF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoidGluZGFrX2xhbmp1dCI7czo1OiJsYWJlbCI7czoxMzoiVGluZGFrIGxhbmp1dCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoicGVsYXBvciI7czo1OiJsYWJlbCI7czoxMDoiRGlsYXBvcmthbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjY6IlVyYWlhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaWNhdGF0IG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiNWNhMzc0YTk0OTI1NTQwMjhlMDBiODkxN2UwMzU4NzRfZmlsdGVycyI7YTozOntzOjg6ImNhdGVnb3J5IjthOjE6e3M6NToidmFsdWUiO047fXM6ODoic2V2ZXJpdHkiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czo4OiJtZW51bmdndSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX1zOjQwOiJlMzIyM2RkZjYyMmE5ODg3MDVhZTIxZWI3MGNlMTk3OF9jb2x1bW5zIjthOjEwOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czo1OiJOb21vciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6ImVtcGxveWVlLmZ1bGxfbmFtZSI7czo1OiJsYWJlbCI7czoxNjoiUGVuYW5nZ3VuZyBqYXdhYiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc3RpbmF0aW9uIjtzOjU6ImxhYmVsIjtzOjY6IlR1anVhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToidWFuZ19tdWthIjtzOjU6ImxhYmVsIjtzOjk6IlVhbmcgbXVrYSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InBlbnllbGVzYWlhbiI7czo1OiJsYWJlbCI7czoxMjoiUGVueWVsZXNhaWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJyZWFsaXNhc2kiO3M6NToibGFiZWwiO3M6MTY6IkJpYXlhIHNlYmVuYXJueWEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicm9tYm9uZ2FuIjtzOjU6ImxhYmVsIjtzOjE0OiJZYW5nIGJlcmFuZ2thdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJ0cmFuc3BvcnQiO3M6NToibGFiZWwiO3M6MTQ6IkNhcmEgYmVyYW5na2F0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFydF9kYXRlIjtzOjU6ImxhYmVsIjtzOjk6IkJlcmFuZ2thdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJlMzIyM2RkZjYyMmE5ODg3MDVhZTIxZWI3MGNlMTk3OF9maWx0ZXJzIjthOjM6e3M6Njoic3RhdHVzIjthOjE6e3M6NToidmFsdWUiO047fXM6MTM6ImRlcGFydG1lbnRfaWQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czoxODoibWVudW5nZ3VfdWFuZ19tdWthIjthOjE6e3M6ODoiaXNBY3RpdmUiO2I6MDt9fXM6NDA6ImVhMDc2ZTg1MDgxNTZiNGVlZjZlNzdkYzc2NjVhNDhmX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJleHBlbnNlX2RhdGUiO3M6NToibGFiZWwiO3M6NzoiVGFuZ2dhbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiY2F0ZWdvcnkiO3M6NToibGFiZWwiO3M6NToiSmVuaXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJkZXNjcmlwdGlvbiI7czo1OiJsYWJlbCI7czoxMDoiS2V0ZXJhbmdhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToiYnVrdGkiO3M6NToibGFiZWwiO3M6NToiQnVrdGkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImFtb3VudCI7czo1OiJsYWJlbCI7czo1OiJOaWxhaSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZWEwNzZlODUwODE1NmI0ZWVmNmU3N2RjNzY2NWE0OGZfZmlsdGVycyI7YToxOntzOjg6ImNhdGVnb3J5IjthOjE6e3M6NToidmFsdWUiO047fX1zOjQwOiI1ZjQ4YzExZjBhNjFiMWRjM2I0N2Q1M2YzMWI0ZWFiM19jb2x1bW5zIjthOjEwOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoiY29kZSI7czo1OiJsYWJlbCI7czoxMjoiTm9tb3IgYWdlbmRhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJkaXJlY3Rpb24iO3M6NToibGFiZWwiO3M6NDoiQXJhaCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoic3ViamVjdCI7czo1OiJsYWJlbCI7czo3OiJQZXJpaGFsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToibG9nZ2VkX2RhdGUiO3M6NToibGFiZWwiO3M6MjE6IkRpdGVyaW1hIGF0YXUgZGlraXJpbSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Nzoia2VhZGFhbiI7czo1OiJsYWJlbCI7czo3OiJLZWFkYWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJ0dWp1YW4iO3M6NToibGFiZWwiO3M6MTE6IlVudHVrIHNpYXBhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJjYXRlZ29yeSI7czo1OiJsYWJlbCI7czo1OiJKZW5pcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToibGV0dGVyX2RhdGUiO3M6NToibGFiZWwiO3M6MTM6IlRhbmdnYWwgc3VyYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToibm90ZXMiO3M6NToibGFiZWwiO3M6NzoiQ2F0YXRhbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxODoiY3JlYXRlZEJ5VXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJEaWNhdGF0IG9sZWgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiNWY0OGMxMWYwYTYxYjFkYzNiNDdkNTNmMzFiNGVhYjNfZmlsdGVycyI7YTozOntzOjk6ImRpcmVjdGlvbiI7YToxOntzOjU6InZhbHVlIjtOO31zOjg6ImNhdGVnb3J5IjthOjE6e3M6NToidmFsdWUiO047fXM6MTk6Im1lbnVuZ2d1X2Rpc2VyYWhrYW4iO2E6MTp7czo4OiJpc0FjdGl2ZSI7YjowO319czo0MDoiYmRjYzhjZTgwZGYxZDVhOGFkMmU0OTQwYTA2ZDNkNTRfY29sdW1ucyI7YToxMDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImNvZGUiO3M6NToibGFiZWwiO3M6NToiTm9tb3IiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJyZWNpcGllbnRfbmFtZSI7czo1OiJsYWJlbCI7czo2OiJUdWp1YW4iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJkZXBhcnRtZW50Lm5hbWUiO3M6NToibGFiZWwiO3M6ODoiRGliZWJhbmkiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo3OiJLZWFkYWFuIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0b3RhbCI7czo1OiJsYWJlbCI7czo1OiJCaWF5YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InJlcXVlc3RfZGF0ZSI7czo1OiJsYWJlbCI7czo3OiJEaW1pbnRhIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoic2hpcHBlZF9kYXRlIjtzOjU6ImxhYmVsIjtzOjc6IkRpa2lyaW0iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTk6InJlcXVlc3Rlci5mdWxsX25hbWUiO3M6NToibGFiZWwiO3M6MTI6IllhbmcgbWVtaW50YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJiZXJhdCI7czo1OiJsYWJlbCI7czo1OiJCZXJhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJwZXJraXJhYW4iO3M6NToibGFiZWwiO3M6MTU6IlBlcmtpcmFhbiBiaWF5YSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJiZGNjOGNlODBkZjFkNWE4YWQyZTQ5NDBhMDZkM2Q1NF9maWx0ZXJzIjthOjM6e3M6Njoic3RhdHVzIjthOjE6e3M6NToidmFsdWUiO047fXM6MTM6ImRlcGFydG1lbnRfaWQiO2E6MTp7czo1OiJ2YWx1ZSI7Tjt9czo4OiJtZW51bmdndSI7YToxOntzOjg6ImlzQWN0aXZlIjtiOjA7fX19czo4OiJmaWxhbWVudCI7YTowOnt9fQ==	1788881787
\.


--
-- Data for Name: settings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.settings (id, "group", key, value, type, label, description, created_at, updated_at) FROM stdin;
1	perusahaan	perusahaan.nama	\N	text	Nama perusahaan	Dipakai pada kop laporan dan dokumen cetak.	2026-09-06 09:04:37	2026-09-06 09:04:37
2	perusahaan	perusahaan.nama_singkat	GAIS	text	Nama singkat	Muncul di sudut kiri atas aplikasi.	2026-09-06 09:04:37	2026-09-06 09:04:37
3	perusahaan	perusahaan.alamat	\N	textarea	Alamat Head Office	Alamat lengkap kantor pusat.	2026-09-06 09:04:37	2026-09-06 09:04:37
4	perusahaan	perusahaan.telepon	\N	text	Telepon kantor	\N	2026-09-06 09:04:37	2026-09-06 09:04:37
5	perusahaan	perusahaan.email	\N	text	Email GA	Alamat email tim GA untuk pemberitahuan.	2026-09-06 09:04:37	2026-09-06 09:04:37
6	perusahaan	perusahaan.logo	[LOGO]	text	Berkas logo	Belum ada berkas logo. Isi setelah file logo perusahaan tersedia.	2026-09-06 09:04:37	2026-09-06 09:04:37
7	label	label.lebar_mm	70	number	Lebar label (mm)	Ukuran satu stiker. Bawaannya 70, sesuai lembar stiker A4 isi 24.	2026-09-06 12:53:36	2026-09-06 12:53:36
8	label	label.tinggi_mm	37	number	Tinggi label (mm)	Bawaannya 37, sesuai lembar stiker A4 isi 24.	2026-09-06 12:53:36	2026-09-06 12:53:36
9	label	label.kolom	3	number	Jumlah label per baris	Bawaannya 3 kolom.	2026-09-06 12:53:36	2026-09-06 12:53:36
10	label	label.margin_atas_mm	8	number	Margin atas halaman (mm)	Jarak dari tepi atas kertas ke label pertama. Sesuaikan kalau cetakan bergeser.	2026-09-06 12:53:36	2026-09-06 12:53:36
11	label	label.margin_kiri_mm	5	number	Margin kiri halaman (mm)	Jarak dari tepi kiri kertas ke kolom pertama.	2026-09-06 12:53:36	2026-09-06 12:53:36
12	penyusutan	penyusutan.mulai	bulan_perolehan	pilihan	Kapan penyusutan mulai dihitung	Pajak Indonesia menghitung penyusutan sejak bulan pengeluaran dilakukan, sebulan penuh, tanpa memandang tanggalnya. Pilihan kedua menunda ke bulan berikutnya, yang lebih umum dipakai pembukuan komersial.	2026-09-07 02:33:09	2026-09-07 02:33:09
13	penyusutan	penyusutan.nilai_sisa	nol	pilihan	Nilai sisa bawaan	Penyusutan fiskal menyusutkan seluruh nilai perolehan sampai habis, jadi bawaannya nol. Pilihan kedua memakai persen nilai sisa yang diatur per kategori aset. Nilai sisa yang diisi langsung pada satu aset selalu menang atas keduanya.	2026-09-07 02:33:09	2026-09-07 02:33:09
14	penyusutan	penyusutan.saldo_menurun_akhir	habiskan	pilihan	Saldo menurun di tahun terakhir	Saldo menurun murni tidak pernah benar benar habis, karena tiap tahun hanya mengambil sebagian dari sisanya. Kebiasaan pajak menghabiskan sisanya di tahun terakhir. Hanya berlaku untuk aset bermetode saldo menurun ganda.	2026-09-07 02:33:09	2026-09-07 02:33:09
15	penyusutan	penyusutan.periode_mulai	\N	text	Periode pertama yang dihitung GAIS	Bulan pertama yang penyusutannya dicatat di aplikasi ini, ditulis YYYY-MM, contohnya 2026-09. Bulan bulan sebelum ini dianggap sudah dicatat di tempat lain, dan angkanya masuk sebagai akumulasi awal tiap aset. Kosongkan untuk memakai bulan berjalan saat periode pertama ditutup.	2026-09-07 02:35:10	2026-09-07 02:35:10
16	layanan	layanan.mendesak_lewati_persetujuan	ya	pilihan	Permintaan mendesak	Kebocoran air, listrik mati, dan lift berhenti tidak bisa menunggu atasan membuka aplikasi. Bawaannya, permintaan berprioritas mendesak langsung masuk antrean tim GA, dan alasan lompatan itu tertulis di tiketnya sehingga tetap terbaca siapa pun yang membukanya. Ubah ke pilihan kedua kalau perusahaan menghendaki semua permintaan lewat persetujuan tanpa kecuali.	2026-09-07 05:53:03	2026-09-07 05:53:03
17	aplikasi	aplikasi.hak_cipta	PT. Gamatechno Indonesia	text	Pemegang hak cipta aplikasi	Muncul di kaki setiap halaman, didahului tahun berjalan. Dikosongkan berarti baris hak cipta tidak ditampilkan sama sekali.	2026-09-07 14:34:51	2026-09-07 14:34:51
\.


--
-- Data for Name: stock_opname_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.stock_opname_lines (id, stock_opname_id, asset_id, asset_code, asset_name, expected_location_id, expected_condition, expected_status, checked, found, found_location_id, found_condition, notes, checked_at, checked_by_user_id, created_at, updated_at) FROM stdin;
346	4	36	FIN-1206-2023-0001	Sepeda motor kurir	21	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
347	4	96	FIN-1206-2023-0002	Sepeda motor kurir	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
9	1	121	GA-1209-2020-0001	Pemindai dokumen	16	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
10	1	23	GA-1209-2023-0001	PC desktop	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
11	1	127	GA-1209-2026-0001	Access point	16	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
12	1	112	GA-1211-2018-0001	Pesawat telepon meja	17	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
13	1	29	HRD-1207-2019-0001	Meja kerja kayu	17	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
14	1	76	HRD-1207-2023-0002	Lemari arsip kayu	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
15	1	14	HRD-1208-2018-0001	Lemari besi arsip	19	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
16	1	92	HRD-1208-2023-0001	Lemari besi arsip	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
17	1	142	HRD-1209-2020-0001	Laptop kerja	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
18	1	104	HRD-1209-2021-0001	Laptop kerja	18	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
19	1	145	HRD-1210-2023-0001	Mesin penghancur kertas	18	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
20	1	61	HRD-1211-2023-0001	Pesawat telepon meja	17	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
21	1	44	HRD-1211-2025-0001	Telepon konferensi	16	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
22	1	21	HRD-1212-2024-0001	AC split 2 PK	18	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
23	1	149	HRD-1212-2024-0002	Kipas angin dinding	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
24	1	100	HRD-1212-2025-0001	Exhaust fan	16	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
25	1	124	IT-1204-2020-0001	Instalasi listrik lantai	15	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
26	1	97	IT-1204-2021-0001	Instalasi listrik lantai	15	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
27	1	111	IT-1211-2019-0001	Telepon konferensi	16	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
28	1	123	OPS-1207-2023-0001	Rak buku kayu	17	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
29	1	83	OPS-1208-2018-0001	Loker karyawan	16	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
30	1	39	OPS-1209-2021-0001	Access point	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
31	1	30	OPS-1210-2026-0001	Proyektor ruang rapat	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
32	1	17	OPS-1211-2025-0001	Telepon konferensi	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
33	1	89	OPS-1212-2025-0001	AC standing 3 PK	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-06 15:37:40	2026-09-06 15:37:40
348	4	87	FIN-1207-2018-0001	Meja rapat kayu	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
349	4	141	FIN-1207-2022-0001	Kursi tamu kayu	13	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
350	4	52	FIN-1207-2026-0001	Rak buku kayu	6	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
351	4	56	FIN-1208-2018-0001	Lemari besi arsip	9	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
352	4	19	FIN-1208-2019-0001	Filing cabinet 4 laci	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
353	4	129	FIN-1208-2020-0001	Rak arsip besi	6	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
354	4	34	FIN-1209-2018-0001	Laptop kerja	8	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
355	4	79	FIN-1209-2018-0002	PC desktop	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
356	4	72	FIN-1209-2020-0001	Pemindai dokumen	17	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
357	4	22	FIN-1209-2023-0001	PC desktop	9	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
358	4	53	FIN-1209-2026-0001	Access point	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
359	4	82	FIN-1210-2020-0001	Mesin fotokopi	11	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
360	4	11	FIN-1210-2021-0001	Laminator	13	perlu_perbaikan	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
361	4	71	FIN-1210-2022-0001	Mesin absensi sidik jari	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
362	4	41	FIN-1211-2018-0001	Pesawat telepon meja	13	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
363	4	152	FIN-1211-2018-0002	Radio genggam	14	rusak	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
364	4	31	FIN-1211-2019-0001	Mesin faksimile	6	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
365	4	138	FIN-1211-2024-0001	Mesin faksimile	8	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
343	4	130	FIN-1206-2020-0001	Sepeda motor kurir	21	rusak	aktif	t	t	21	rusak	\N	2026-09-07 16:38:15	1	2026-09-07 16:36:32	2026-09-07 16:38:15
341	4	147	FIN-1204-2023-0001	Genset cadangan	10	baik	aktif	t	t	10	baik	\N	2026-09-07 16:38:18	1	2026-09-07 16:36:32	2026-09-07 16:38:18
344	4	84	FIN-1206-2021-0001	Sepeda motor kurir	21	baik	aktif	t	t	21	baik	\N	2026-09-07 16:38:26	1	2026-09-07 16:36:32	2026-09-07 16:38:26
345	4	16	FIN-1206-2022-0001	Sepeda motor operasional	21	baik	aktif	t	t	21	baik	\N	2026-09-07 16:38:29	1	2026-09-07 16:36:32	2026-09-07 16:38:29
3	1	72	FIN-1209-2020-0001	Pemindai dokumen	19	baik	aktif	t	t	17	baik	\N	2026-09-06 15:51:00	2	2026-09-06 15:37:40	2026-09-06 15:51:00
4	1	55	FIN-1212-2024-0002	Exhaust fan	19	baik	dipinjam	t	t	19	baik	\N	2026-09-06 15:51:31	2	2026-09-06 15:37:40	2026-09-06 15:51:31
5	1	128	FIN-1212-2024-0003	Kipas angin dinding	18	baik	aktif	t	t	18	baik	\N	2026-09-06 15:51:31	2	2026-09-06 15:37:40	2026-09-06 15:51:31
7	1	45	GA-1207-2025-0001	Meja rapat kayu	16	perlu_perbaikan	tidak_dipakai	t	t	16	perlu_perbaikan	\N	2026-09-06 15:51:31	2	2026-09-06 15:37:40	2026-09-06 15:51:31
8	1	50	GA-1207-2026-0001	Meja rapat kayu	19	perlu_perbaikan	aktif	t	t	19	perlu_perbaikan	\N	2026-09-06 15:51:31	2	2026-09-06 15:37:40	2026-09-06 15:51:31
366	4	51	FIN-1211-2025-0001	Telepon konferensi	13	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
367	4	8	FIN-1212-2018-0001	Kipas angin dinding	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
368	4	139	FIN-1212-2018-0002	Kipas angin dinding	14	perlu_perbaikan	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
369	4	63	FIN-1212-2021-0001	AC split 2 PK	8	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
370	4	78	FIN-1212-2021-0002	Kipas angin dinding	7	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
371	4	26	FIN-1212-2023-0001	AC split 1 PK	11	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
372	4	54	FIN-1212-2024-0001	Kipas angin dinding	7	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
373	4	55	FIN-1212-2024-0002	Exhaust fan	19	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
374	4	128	FIN-1212-2024-0003	Kipas angin dinding	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
375	4	1	GA-1201-2017-0001	Tanah kantor pusat	4	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
376	4	2	GA-1202-2012-0001	Gedung Head Office	4	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
377	4	38	GA-1204-2022-0001	Pompa air gedung	5	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
378	4	122	GA-1205-2020-0001	Mobil boks pengiriman	21	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
379	4	135	GA-1205-2023-0001	Kendaraan operasional	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
380	4	28	GA-1206-2018-0001	Sepeda motor operasional	21	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
381	4	91	GA-1206-2020-0001	Sepeda motor kurir	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
382	4	133	GA-1206-2020-0002	Sepeda motor kurir	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
383	4	107	GA-1206-2021-0001	Sepeda motor operasional	\N	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
384	4	119	GA-1206-2022-0001	Sepeda motor kurir	\N	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
385	4	117	GA-1207-2018-0001	Lemari arsip kayu	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
386	4	10	GA-1207-2019-0001	Kursi tamu kayu	6	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
387	4	74	GA-1207-2023-0001	Lemari arsip kayu	12	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
388	4	45	GA-1207-2025-0001	Meja rapat kayu	16	perlu_perbaikan	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
389	4	50	GA-1207-2026-0001	Meja rapat kayu	19	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
390	4	101	GA-1208-2021-0001	Lemari besi arsip	13	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
391	4	115	GA-1208-2021-0002	Loker karyawan	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
392	4	131	GA-1208-2025-0001	Filing cabinet 4 laci	11	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
393	4	13	GA-1208-2026-0001	Loker karyawan	9	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
394	4	150	GA-1208-2026-0002	Loker karyawan	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
395	4	121	GA-1209-2020-0001	Pemindai dokumen	16	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
396	4	23	GA-1209-2023-0001	PC desktop	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
397	4	81	GA-1209-2024-0001	Server rak	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
398	4	75	GA-1209-2025-0001	Switch 24 port	13	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
399	4	127	GA-1209-2026-0001	Access point	16	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
400	4	112	GA-1211-2018-0001	Pesawat telepon meja	17	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
401	4	73	GA-1211-2020-0001	Pesawat telepon meja	11	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
402	4	88	GA-1212-2026-0001	AC split 1 PK	14	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
403	4	4	GA-1213-2014-0001	Renovasi ruang rapat lantai 3	4	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
404	4	70	HRD-1205-2018-0001	Mobil boks pengiriman	21	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
405	4	98	HRD-1205-2018-0002	Kendaraan dinas	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
406	4	144	HRD-1205-2020-0001	Kendaraan operasional	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
407	4	143	HRD-1205-2024-0001	Mobil boks pengiriman	\N	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
408	4	49	HRD-1205-2026-0001	Kendaraan operasional	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
409	4	77	HRD-1205-2026-0002	Kendaraan dinas	21	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
410	4	113	HRD-1206-2020-0001	Sepeda motor operasional	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
411	4	102	HRD-1206-2021-0001	Sepeda motor operasional	21	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
412	4	48	HRD-1206-2023-0001	Sepeda motor kurir	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
413	4	110	HRD-1206-2024-0001	Sepeda motor operasional	21	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
414	4	140	HRD-1206-2024-0002	Sepeda motor kurir	21	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
415	4	85	HRD-1207-2018-0001	Meja kerja kayu	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
416	4	29	HRD-1207-2019-0001	Meja kerja kayu	17	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
417	4	25	HRD-1207-2023-0001	Meja kerja kayu	7	perlu_perbaikan	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
418	4	76	HRD-1207-2023-0002	Lemari arsip kayu	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
419	4	14	HRD-1208-2018-0001	Lemari besi arsip	19	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
420	4	95	HRD-1208-2018-0002	Loker karyawan	\N	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
421	4	12	HRD-1208-2022-0001	Rak arsip besi	12	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
422	4	92	HRD-1208-2023-0001	Lemari besi arsip	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
423	4	15	HRD-1209-2018-0001	Pemindai dokumen	8	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
424	4	142	HRD-1209-2020-0001	Laptop kerja	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
425	4	104	HRD-1209-2021-0001	Laptop kerja	18	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
426	4	86	HRD-1209-2022-0001	Monitor 24 inci	12	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
427	4	58	HRD-1209-2026-0001	Monitor 24 inci	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
428	4	118	HRD-1210-2018-0001	Mesin fotokopi	8	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
429	4	146	HRD-1210-2018-0002	Mesin absensi sidik jari	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
430	4	145	HRD-1210-2023-0001	Mesin penghancur kertas	18	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
431	4	33	HRD-1210-2024-0001	Mesin absensi sidik jari	11	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
432	4	59	HRD-1210-2025-0001	Mesin penghancur kertas	7	perlu_perbaikan	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
433	4	108	HRD-1211-2020-0001	Mesin faksimile	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
434	4	134	HRD-1211-2022-0001	Pesawat telepon meja	11	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
435	4	61	HRD-1211-2023-0001	Pesawat telepon meja	17	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
436	4	44	HRD-1211-2025-0001	Telepon konferensi	16	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
437	4	114	HRD-1212-2019-0001	AC split 1 PK	7	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
438	4	5	HRD-1212-2021-0001	AC standing 3 PK	9	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
439	4	21	HRD-1212-2024-0001	AC split 2 PK	18	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
440	4	149	HRD-1212-2024-0002	Kipas angin dinding	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
441	4	100	HRD-1212-2025-0001	Exhaust fan	16	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
442	4	124	IT-1204-2020-0001	Instalasi listrik lantai	15	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
443	4	97	IT-1204-2021-0001	Instalasi listrik lantai	15	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
444	4	6	IT-1204-2022-0001	Pompa air gedung	10	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
445	4	67	IT-1205-2023-0001	Kendaraan operasional	21	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
446	4	64	IT-1206-2021-0001	Sepeda motor operasional	21	perlu_perbaikan	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
447	4	18	IT-1206-2025-0001	Sepeda motor operasional	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
448	4	120	IT-1207-2018-0001	Meja kerja kayu	\N	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
449	4	69	IT-1207-2021-0001	Kursi tamu kayu	9	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
450	4	94	IT-1207-2022-0001	Meja rapat kayu	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
451	4	106	IT-1207-2026-0001	Kursi tamu kayu	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
452	4	125	IT-1207-2026-0002	Kursi tamu kayu	\N	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
453	4	57	IT-1209-2021-0001	Pemindai dokumen	\N	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
454	4	20	IT-1209-2024-0001	Printer laser	11	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
455	4	27	IT-1210-2025-0001	Mesin fotokopi	12	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
456	4	111	IT-1211-2019-0001	Telepon konferensi	16	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
457	4	132	IT-1211-2026-0001	Pesawat telepon meja	12	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
458	4	3	OPS-1203-2018-0001	Gudang semi permanen belakang	4	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
459	4	126	OPS-1204-2023-0001	Sistem pemadam kebakaran	10	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
460	4	42	OPS-1204-2025-0001	Sistem pemadam kebakaran	5	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
461	4	24	OPS-1205-2019-0001	Kendaraan dinas	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
462	4	35	OPS-1205-2022-0001	Kendaraan dinas	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
463	4	37	OPS-1205-2022-0002	Mobil boks pengiriman	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
464	4	137	OPS-1205-2022-0003	Mobil boks pengiriman	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
465	4	103	OPS-1205-2023-0001	Kendaraan dinas	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
466	4	66	OPS-1205-2025-0001	Kendaraan operasional	21	perlu_perbaikan	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
467	4	90	OPS-1206-2018-0001	Sepeda motor kurir	21	rusak	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
468	4	46	OPS-1206-2020-0001	Sepeda motor kurir	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
469	4	40	OPS-1206-2021-0001	Sepeda motor kurir	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
470	4	43	OPS-1206-2023-0001	Sepeda motor operasional	21	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
471	4	93	OPS-1206-2023-0002	Sepeda motor kurir	21	baik	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
472	4	136	OPS-1206-2023-0003	Sepeda motor operasional	21	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
473	4	151	OPS-1206-2025-0001	Sepeda motor operasional	21	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
474	4	80	OPS-1207-2018-0001	Meja rapat kayu	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
475	4	9	OPS-1207-2020-0001	Kursi tamu kayu	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
476	4	123	OPS-1207-2023-0001	Rak buku kayu	17	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
477	4	65	OPS-1207-2025-0001	Meja rapat kayu	14	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
478	4	154	OPS-1207-2025-0002	Kursi tamu kayu	13	rusak	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
479	4	99	OPS-1207-2026-0001	Kursi tamu kayu	11	baik	tidak_dipakai	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
480	4	83	OPS-1208-2018-0001	Loker karyawan	16	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
481	4	7	OPS-1208-2024-0001	Lemari besi arsip	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
482	4	68	OPS-1208-2024-0002	Brankas	12	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
483	4	62	OPS-1209-2019-0001	Pemindai dokumen	9	perlu_perbaikan	perbaikan	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
484	4	109	OPS-1209-2020-0001	PC desktop	14	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
485	4	39	OPS-1209-2021-0001	Access point	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
486	4	116	OPS-1209-2026-0001	Switch 24 port	7	rusak	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
487	4	30	OPS-1210-2026-0001	Proyektor ruang rapat	19	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
488	4	153	OPS-1211-2019-0001	Pesawat telepon meja	9	baik	dipinjam	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
489	4	105	OPS-1211-2020-0001	Telepon konferensi	7	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
490	4	32	OPS-1211-2022-0001	Mesin faksimile	6	perlu_perbaikan	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
491	4	17	OPS-1211-2025-0001	Telepon konferensi	18	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
492	4	47	OPS-1212-2024-0001	AC standing 3 PK	11	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
493	4	89	OPS-1212-2025-0001	AC standing 3 PK	16	baik	aktif	f	\N	\N	\N	\N	\N	\N	2026-09-07 16:36:32	2026-09-07 16:36:32
1	1	60	FIN-1204-2022-0001	Pompa air gedung	15	baik	perbaikan	t	t	15	baik	\N	2026-09-06 15:45:51	2	2026-09-06 15:37:40	2026-09-06 15:45:51
2	1	79	FIN-1209-2018-0002	PC desktop	16	baik	aktif	t	f	\N	\N	\N	2026-09-06 15:49:05	2	2026-09-06 15:37:40	2026-09-06 15:49:05
6	1	117	GA-1207-2018-0001	Lemari arsip kayu	19	baik	aktif	t	t	19	baik	\N	2026-09-06 15:51:31	2	2026-09-06 15:37:40	2026-09-06 15:51:31
340	4	60	FIN-1204-2022-0001	Pompa air gedung	15	baik	perbaikan	t	t	15	baik	\N	2026-09-07 16:37:01	1	2026-09-07 16:36:32	2026-09-07 16:37:01
342	4	148	FIN-1205-2018-0001	Kendaraan dinas	21	perlu_perbaikan	aktif	t	t	21	baik	\N	2026-09-07 16:37:29	1	2026-09-07 16:36:32	2026-09-07 16:37:29
\.


--
-- Data for Name: stock_opnames; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.stock_opnames (id, code, name, scope_location_id, scope_department_id, scope_asset_category_id, status, started_at, started_by_user_id, finished_at, finished_by_user_id, adjusted_at, adjusted_by_user_id, notes, created_at, updated_at) FROM stdin;
1	SO/2026/09/0001	Opname lantai 3 uji coba	15	\N	\N	selesai	2026-09-06 15:44:34	2	2026-09-06 15:52:35	2	2026-09-06 15:52:50	2	\N	2026-09-06 15:34:42	2026-09-06 15:52:50
4	SO/2026/09/0004	SO awal bulan Sep 2026	\N	\N	\N	selesai	2026-09-07 16:36:54	1	2026-09-07 16:38:37	1	\N	\N	SO keseluruhan	2026-09-07 16:34:22	2026-09-07 16:38:37
\.


--
-- Data for Name: supply_items; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_items (id, code, name, category, unit, minimum_stock, location_id, last_price, account_expense, is_active, notes, created_at, updated_at) FROM stdin;
1	ATK-0001	Kertas HVS A4 80 gram	kertas	rim	20	20	62000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:53	2026-09-06 21:23:53
23	ATK-0023	Teh celup kotak isi 25	pantry	box	6	20	22000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-08 10:09:58
3	ATK-0003	Amplop kabinet coklat	kertas	pak	10	20	34000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:53	2026-09-06 21:23:54
4	ATK-0004	Kertas struk kasir	kertas	roll	12	20	6500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:54	2026-09-06 21:23:54
5	ATK-0005	Pulpen tinta biru 0,5 mm	alat_tulis	pcs	50	20	4200.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:54	2026-09-06 21:23:54
6	ATK-0006	Pulpen tinta hitam 0,5 mm	alat_tulis	pcs	50	20	4200.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:54	2026-09-06 21:23:54
7	ATK-0007	Pensil kayu 2B	alat_tulis	pcs	30	20	3100.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:55	2026-09-06 21:23:55
9	ATK-0009	Stapler ukuran sedang	alat_tulis	pcs	5	20	38000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:55	2026-09-06 21:23:55
10	ATK-0010	Isi stapler nomor 10	alat_tulis	box	20	20	3800.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:55	2026-09-06 21:23:55
11	ATK-0011	Map plastik berkancing	alat_tulis	pcs	40	20	5400.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:56	2026-09-06 21:23:56
12	ATK-0012	Ordner arsip folio	alat_tulis	pcs	15	20	27000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:56	2026-09-06 21:23:56
13	ATK-0013	Lakban bening 2 inci	alat_tulis	roll	12	20	12000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:56	2026-09-06 21:23:56
14	ATK-0014	Tinta printer hitam	tinta	botol	6	20	92000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-06 21:23:57
16	ATK-0016	Toner printer laser	tinta	pcs	2	20	780000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-06 21:23:57
17	ATK-0017	Sabun cuci tangan isi ulang	kebersihan	botol	8	20	26000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-06 21:23:57
18	ATK-0018	Tisu gulung	kebersihan	roll	24	20	8500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-06 21:23:57
20	ATK-0020	Kantong sampah ukuran besar	kebersihan	pak	10	20	19500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-06 21:23:57
21	ATK-0021	Gula pasir kemasan 1 kilogram	pantry	kg	5	20	16500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-06 21:23:58
22	ATK-0022	Kopi bubuk kemasan	pantry	pak	8	20	24000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-06 21:23:58
24	ATK-0024	Air minum galon 19 liter	pantry	pcs	10	20	21000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-06 21:23:58
25	ATK-0025	Lampu LED 12 watt	listrik	pcs	12	20	34000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-06 21:23:58
26	ATK-0026	Baterai AA isi 4	listrik	pak	6	20	27500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:58	2026-09-06 21:23:59
2	ATK-0002	Kertas HVS F4 70 gram	kertas	rim	10	20	61500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:53	2026-09-06 21:48:44
8	ATK-0008	Spidol papan tulis hitam	alat_tulis	pcs	12	20	8500.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:55	2026-09-08 10:09:58
15	ATK-0015	Tinta printer warna	tinta	botol	6	20	95000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-08 10:12:46
19	ATK-0019	Pembersih lantai	kebersihan	botol	6	20	18000.00	\N	t	[DATA DEMO] Barang karangan untuk peragaan, bukan persediaan sungguhan.	2026-09-06 21:23:57	2026-09-08 10:12:46
\.


--
-- Data for Name: supply_opname_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_opname_lines (id, supply_opname_id, supply_item_id, item_code, item_name, unit, system_quantity, counted_quantity, checked, notes, supply_transaction_id, created_at, updated_at) FROM stdin;
5	1	7	ATK-0007	Pensil kayu 2B	pcs	66	\N	f	\N	\N	2026-09-08 11:29:04	2026-09-08 11:29:04
1	1	10	ATK-0010	Isi stapler nomor 10	box	38	38	t	\N	\N	2026-09-08 11:29:04	2026-09-08 11:30:14
4	1	12	ATK-0012	Ordner arsip folio	pcs	24	24	t	\N	\N	2026-09-08 11:29:04	2026-09-08 11:30:53
6	1	5	ATK-0005	Pulpen tinta biru 0,5 mm	pcs	180	180	t	\N	\N	2026-09-08 11:29:04	2026-09-08 11:31:10
9	1	9	ATK-0009	Stapler ukuran sedang	pcs	14	14	t	\N	\N	2026-09-08 11:29:04	2026-09-08 11:31:39
8	1	8	ATK-0008	Spidol papan tulis hitam	pcs	12	8	t	Dihitung ulang setelah empat spidol kering dibuang	\N	2026-09-08 11:29:04	2026-09-08 11:34:01
2	1	13	ATK-0013	Lakban bening 2 inci	roll	36	34	t	Dua roll terpakai untuk kemasan kiriman, tidak sempat dicatat	168	2026-09-08 11:29:04	2026-09-08 11:34:47
3	1	11	ATK-0011	Map plastik berkancing	pcs	100	103	t	Tiga pcs ketemu di laci meja resepsionis	169	2026-09-08 11:29:04	2026-09-08 11:34:47
7	1	6	ATK-0006	Pulpen tinta hitam 0,5 mm	pcs	30	28	t	Dua batang kering, dibuang	170	2026-09-08 11:29:04	2026-09-08 11:34:47
10	2	24	ATK-0024	Air minum galon 19 liter	pcs	27	\N	f	\N	\N	2026-09-08 11:44:40	2026-09-08 11:44:40
11	2	21	ATK-0021	Gula pasir kemasan 1 kilogram	kg	10	\N	f	\N	\N	2026-09-08 11:44:40	2026-09-08 11:44:40
12	2	22	ATK-0022	Kopi bubuk kemasan	pak	11	\N	f	\N	\N	2026-09-08 11:44:40	2026-09-08 11:44:40
13	2	23	ATK-0023	Teh celup kotak isi 25	box	10	\N	f	\N	\N	2026-09-08 11:44:40	2026-09-08 11:44:40
14	3	24	ATK-0024	Air minum galon 19 liter	pcs	27	\N	f	\N	\N	2026-09-08 11:49:26	2026-09-08 11:49:26
15	3	21	ATK-0021	Gula pasir kemasan 1 kilogram	kg	8	\N	f	\N	\N	2026-09-08 11:49:26	2026-09-08 11:49:26
16	3	22	ATK-0022	Kopi bubuk kemasan	pak	11	\N	f	\N	\N	2026-09-08 11:49:26	2026-09-08 11:49:26
17	3	23	ATK-0023	Teh celup kotak isi 25	box	10	\N	f	\N	\N	2026-09-08 11:49:26	2026-09-08 11:49:26
18	4	24	ATK-0024	Air minum galon 19 liter	pcs	27	27	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:23:50
19	4	3	ATK-0003	Amplop kabinet coklat	pak	31	31	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:23:55
20	4	26	ATK-0026	Baterai AA isi 4	pak	7	7	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:23:59
21	4	21	ATK-0021	Gula pasir kemasan 1 kilogram	kg	10	10	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:04
22	4	10	ATK-0010	Isi stapler nomor 10	box	38	37	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:11
23	4	20	ATK-0020	Kantong sampah ukuran besar	pak	23	23	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:18
24	4	1	ATK-0001	Kertas HVS A4 80 gram	rim	40	39	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:25
25	4	2	ATK-0002	Kertas HVS F4 70 gram	rim	15	15	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:30
26	4	4	ATK-0004	Kertas struk kasir	roll	20	20	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:42
27	4	22	ATK-0022	Kopi bubuk kemasan	pak	11	11	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:46
28	4	13	ATK-0013	Lakban bening 2 inci	roll	34	34	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:52
29	4	25	ATK-0025	Lampu LED 12 watt	pcs	38	38	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:24:57
30	4	11	ATK-0011	Map plastik berkancing	pcs	103	100	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:06
43	4	16	ATK-0016	Toner printer laser	pcs	6	6	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:12
42	4	18	ATK-0018	Tisu gulung	roll	43	43	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:16
41	4	15	ATK-0015	Tinta printer warna	botol	8	8	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:25
40	4	14	ATK-0014	Tinta printer hitam	botol	12	12	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:36
39	4	23	ATK-0023	Teh celup kotak isi 25	box	10	10	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:39
38	4	9	ATK-0009	Stapler ukuran sedang	pcs	14	14	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:44
37	4	8	ATK-0008	Spidol papan tulis hitam	pcs	8	8	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:48
36	4	17	ATK-0017	Sabun cuci tangan isi ulang	botol	21	21	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:52
35	4	6	ATK-0006	Pulpen tinta hitam 0,5 mm	pcs	28	28	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:25:56
31	4	12	ATK-0012	Ordner arsip folio	pcs	24	24	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:26:01
32	4	19	ATK-0019	Pembersih lantai	botol	9	8	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:26:06
33	4	7	ATK-0007	Pensil kayu 2B	pcs	66	65	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:26:10
34	4	5	ATK-0005	Pulpen tinta biru 0,5 mm	pcs	180	180	t	\N	\N	2026-09-08 12:23:04	2026-09-08 12:26:16
\.


--
-- Data for Name: supply_opnames; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_opnames (id, code, name, scope_category, scope_location_id, status, started_at, finished_at, adjusted_at, adjusted_by_user_id, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	OP/2026/09/0001	Opname alat tulis akhir September 2026	alat_tulis	\N	selesai	2026-09-08 11:29:45	2026-09-08 11:34:23	2026-09-08 11:34:47	2	\N	2	2026-09-08 11:28:41	2026-09-08 11:34:47
2	OP/2026/09/0002	[DATA UJI] Uji peringatan pergerakan stok	pantry	\N	dibatalkan	2026-09-08 11:44:59	\N	\N	\N	\N	2	2026-09-08 11:44:09	2026-09-08 11:47:49
3	OP/2026/09/0003	[DATA UJI] Uji ulang peringatan pergerakan stok	pantry	\N	dibatalkan	2026-09-08 11:50:18	\N	\N	\N	\N	2	2026-09-08 11:49:16	2026-09-08 11:50:34
4	OP/2026/09/0004	Opname ATK Sep 2026	\N	\N	selesai	2026-09-08 12:23:19	2026-09-08 12:26:26	\N	\N	Opname Sep ATK 2026	1	2026-09-08 12:22:52	2026-09-08 12:26:26
\.


--
-- Data for Name: supply_purchase_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_purchase_lines (id, supply_purchase_id, supply_item_id, quantity_ordered, unit_price, notes, created_at, updated_at) FROM stdin;
1	1	8	12	8500.00	\N	2026-09-08 10:05:18	2026-09-08 10:05:18
2	1	23	10	22000.00	\N	2026-09-08 10:05:41	2026-09-08 10:05:41
3	1	4	12	6500.00	\N	2026-09-08 10:05:55	2026-09-08 10:05:55
4	2	19	6	18000.00	\N	2026-09-08 10:12:06	2026-09-08 10:12:06
5	2	15	4	95000.00	\N	2026-09-08 10:12:29	2026-09-08 10:12:29
6	3	12	5	35000.00	\N	2026-09-08 10:23:42	2026-09-08 10:23:42
\.


--
-- Data for Name: supply_purchases; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_purchases (id, code, kind, vendor_id, supplier_name, order_date, expected_date, description, notes, status, submitted_at, approved_by_user_id, approved_at, approval_note, approval_skipped_reason, rejection_reason, closed_at, closing_reason, created_by_user_id, created_at, updated_at) FROM stdin;
2	PP/2026/09/0002	langsung	\N	Toko Sinar Jaya, Jalan Kaliurang	2026-09-08	\N	Belanja mendadak barang kebersihan dan tinta printer	\N	selesai	2026-09-08 10:12:46	\N	2026-09-08 10:12:46	\N	Pembelian langsung, barangnya sudah di tangan saat dicatat	\N	\N	\N	2	2026-09-08 10:11:41	2026-09-08 10:12:46
1	PP/2026/09/0001	pesanan	2	\N	2026-09-08	2026-09-15	Pengadaan ATK yang stoknya habis, September	\N	selesai	2026-09-08 10:06:19	2	2026-09-08 10:06:46	Setuju, harga sesuai penawaran	\N	\N	\N	\N	2	2026-09-08 10:04:46	2026-09-08 10:20:59
3	PP/2026/09/0003	pesanan	2	\N	2026-09-08	\N	Pesanan uji penutupan, sisa dibatalkan rekanan	\N	selesai	2026-09-08 10:23:58	2	2026-09-08 10:24:14	\N	\N	\N	2026-09-08 10:24:40	Rekanan membatalkan, ordner kosong sampai akhir tahun	2	2026-09-08 10:23:16	2026-09-08 10:24:40
\.


--
-- Data for Name: supply_receipt_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_receipt_lines (id, supply_receipt_id, supply_purchase_line_id, quantity, supply_transaction_id, created_at, updated_at) FROM stdin;
1	1	3	12	161	2026-09-08 10:09:58	2026-09-08 10:09:58
2	1	1	12	162	2026-09-08 10:09:58	2026-09-08 10:09:58
3	1	2	4	163	2026-09-08 10:09:58	2026-09-08 10:09:58
4	2	5	4	164	2026-09-08 10:12:46	2026-09-08 10:12:46
5	2	4	6	165	2026-09-08 10:12:46	2026-09-08 10:12:46
6	3	2	6	166	2026-09-08 10:20:59	2026-09-08 10:20:59
\.


--
-- Data for Name: supply_receipts; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_receipts (id, code, supply_purchase_id, receipt_date, delivery_note_number, notes, received_by_user_id, created_at, updated_at) FROM stdin;
1	PN/2026/09/0001	1	2026-09-08	SJ-4471	Teh baru datang 4 box, sisanya menyusul minggu depan	2	2026-09-08 10:09:58	2026-09-08 10:09:58
2	PN/2026/09/0002	2	2026-09-08	NOTA-88123	\N	2	2026-09-08 10:12:46	2026-09-08 10:12:46
3	PN/2026/09/0003	1	2026-09-08	SJ-4488	\N	2	2026-09-08 10:20:59	2026-09-08 10:20:59
\.


--
-- Data for Name: supply_request_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_request_lines (id, supply_request_id, supply_item_id, quantity_requested, quantity_issued, notes, supply_transaction_id, created_at, updated_at) FROM stdin;
3	1	8	6	0	Untuk papan tulis ruang rapat	\N	2026-09-08 05:58:06	2026-09-08 06:01:50
1	1	1	5	5	Untuk cetak berkas rapat	158	2026-09-08 05:57:27	2026-09-08 06:02:15
2	1	4	4	4	\N	159	2026-09-08 05:57:47	2026-09-08 06:02:15
4	2	1	3	3	\N	160	2026-09-08 06:10:21	2026-09-08 06:11:32
5	3	15	2	\N	\N	\N	2026-09-08 06:13:14	2026-09-08 06:13:14
6	3	8	4	\N	\N	\N	2026-09-08 06:13:53	2026-09-08 06:13:53
\.


--
-- Data for Name: supply_requests; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_requests (id, code, employee_id, department_id, purpose, needed_date, notes, status, submitted_at, approver_employee_id, approved_at, approval_note, approval_skipped_reason, issued_by_user_id, issued_at, issue_note, rejection_reason, rejected_stage, created_by_user_id, created_at, updated_at) FROM stdin;
1	PB/2026/09/0001	5	5	Perlengkapan tulis dan pantry rapat tutup buku September	2026-09-11	\N	diserahkan	2026-09-08 05:59:00	\N	2026-09-08 05:59:00	\N	Departemen yang dibebani belum punya kepala departemen	2	2026-09-08 06:02:15	Spidol kosong, diserahkan menyusul setelah pembelian	\N	\N	2	2026-09-08 05:56:00	2026-09-08 06:02:15
2	PB/2026/09/0002	5	5	Kertas cetak laporan keuangan kuartal tiga	\N	\N	diserahkan	2026-09-08 06:10:37	6	2026-09-08 06:11:08	Setuju, kertas memang habis di ruang keuangan	\N	2	2026-09-08 06:11:32	\N	\N	\N	2	2026-09-08 06:09:48	2026-09-08 06:11:32
3	PB/2026/09/0003	5	5	Tinta printer dan spidol untuk ruang keuangan	\N	\N	dibatalkan	2026-09-08 06:14:07	6	2026-09-08 06:14:55	\N	\N	\N	\N	\N	\N	\N	2	2026-09-08 06:12:47	2026-09-08 06:16:14
\.


--
-- Data for Name: supply_transactions; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.supply_transactions (id, code, supply_item_id, type, quantity, unit_price, transaction_date, department_id, employee_id, supplier, reference, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	MP/2026/09/0001	1	masuk	65	62000.00	2026-05-26	\N	\N	Toko contoh untuk peragaan	FKT-62648	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
2	MP/2026/09/0002	1	keluar	-6	\N	2026-06-06	1	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
3	MP/2026/09/0003	1	keluar	-9	\N	2026-06-24	8	4	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
4	MP/2026/09/0004	1	keluar	-2	\N	2026-07-20	1	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
5	MP/2026/09/0005	2	masuk	28	58000.00	2026-05-22	\N	\N	Toko contoh untuk peragaan	FKT-95993	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
6	MP/2026/09/0006	2	keluar	-5	\N	2026-06-06	1	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
7	MP/2026/09/0007	2	keluar	-2	\N	2026-06-17	4	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
8	MP/2026/09/0008	2	keluar	-6	\N	2026-06-28	2	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
9	MP/2026/09/0009	2	keluar	-3	\N	2026-07-15	3	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
10	MP/2026/09/0010	2	keluar	-4	\N	2026-08-21	3	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:53	2026-09-06 21:23:53
11	MP/2026/09/0011	3	masuk	51	34000.00	2026-05-10	\N	\N	Toko contoh untuk peragaan	FKT-97394	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
12	MP/2026/09/0012	3	keluar	-6	\N	2026-06-06	4	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
13	MP/2026/09/0013	3	keluar	-3	\N	2026-06-24	2	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
14	MP/2026/09/0014	3	keluar	-3	\N	2026-07-08	5	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
15	MP/2026/09/0015	3	keluar	-4	\N	2026-07-24	8	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
16	MP/2026/09/0016	3	keluar	-4	\N	2026-08-01	3	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
17	MP/2026/09/0017	4	masuk	30	6500.00	2026-05-17	\N	\N	Toko contoh untuk peragaan	FKT-33871	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
18	MP/2026/09/0018	4	keluar	-1	\N	2026-06-06	1	4	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
19	MP/2026/09/0019	4	keluar	-5	\N	2026-06-16	6	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
20	MP/2026/09/0020	4	keluar	-1	\N	2026-07-18	4	3	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
21	MP/2026/09/0021	4	keluar	-6	\N	2026-07-06	8	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
22	MP/2026/09/0022	4	keluar	-5	\N	2026-07-16	6	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
23	MP/2026/09/0023	5	masuk	241	4200.00	2026-05-16	\N	\N	Toko contoh untuk peragaan	FKT-67979	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
24	MP/2026/09/0024	5	keluar	-19	\N	2026-06-06	7	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
25	MP/2026/09/0025	5	keluar	-23	\N	2026-06-16	6	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
26	MP/2026/09/0026	5	keluar	-18	\N	2026-07-20	2	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
27	MP/2026/09/0027	5	keluar	-1	\N	2026-07-24	2	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
28	MP/2026/09/0028	6	masuk	117	4200.00	2026-05-12	\N	\N	Toko contoh untuk peragaan	FKT-62418	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
29	MP/2026/09/0029	6	keluar	-23	\N	2026-06-06	6	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
30	MP/2026/09/0030	6	keluar	-1	\N	2026-06-19	6	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
31	MP/2026/09/0031	6	keluar	-24	\N	2026-07-12	6	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:54	2026-09-06 21:23:54
32	MP/2026/09/0032	6	keluar	-26	\N	2026-07-18	3	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
33	MP/2026/09/0033	6	keluar	-13	\N	2026-08-21	5	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
34	MP/2026/09/0034	7	masuk	122	3100.00	2026-05-15	\N	\N	Toko contoh untuk peragaan	FKT-46617	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
35	MP/2026/09/0035	7	keluar	-13	\N	2026-06-06	8	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
36	MP/2026/09/0036	7	keluar	-13	\N	2026-06-27	8	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
37	MP/2026/09/0037	7	keluar	-14	\N	2026-06-26	1	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
38	MP/2026/09/0038	7	keluar	-14	\N	2026-07-24	7	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
39	MP/2026/09/0039	7	keluar	-2	\N	2026-08-09	5	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
40	MP/2026/09/0040	8	masuk	22	11500.00	2026-05-23	\N	\N	Toko contoh untuk peragaan	FKT-36455	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
41	MP/2026/09/0041	8	keluar	-2	\N	2026-06-06	5	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
42	MP/2026/09/0042	8	keluar	-5	\N	2026-06-21	2	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
43	MP/2026/09/0043	8	keluar	-1	\N	2026-07-10	4	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
44	MP/2026/09/0044	8	keluar	-3	\N	2026-08-05	2	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
45	MP/2026/09/0045	8	keluar	-6	\N	2026-08-05	2	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
46	MP/2026/09/0046	8	keluar	-5	\N	2026-08-15	6	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
47	MP/2026/09/0047	9	masuk	24	38000.00	2026-05-12	\N	\N	Toko contoh untuk peragaan	FKT-44887	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
48	MP/2026/09/0048	9	keluar	-1	\N	2026-06-06	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
49	MP/2026/09/0049	9	keluar	-1	\N	2026-06-19	5	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
50	MP/2026/09/0050	9	keluar	-4	\N	2026-06-28	7	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
51	MP/2026/09/0051	9	keluar	-4	\N	2026-08-11	1	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
52	MP/2026/09/0052	10	masuk	53	3800.00	2026-05-11	\N	\N	Toko contoh untuk peragaan	FKT-39229	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
53	MP/2026/09/0053	10	keluar	-1	\N	2026-06-06	3	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
54	MP/2026/09/0054	10	keluar	-2	\N	2026-06-20	5	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:55	2026-09-06 21:23:55
55	MP/2026/09/0055	10	keluar	-9	\N	2026-06-28	7	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
56	MP/2026/09/0056	10	keluar	-3	\N	2026-08-11	8	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
57	MP/2026/09/0057	11	masuk	193	5400.00	2026-05-22	\N	\N	Toko contoh untuk peragaan	FKT-60577	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
58	MP/2026/09/0058	11	keluar	-17	\N	2026-06-06	8	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
59	MP/2026/09/0059	11	keluar	-19	\N	2026-06-19	7	11	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
60	MP/2026/09/0060	11	keluar	-3	\N	2026-07-02	7	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
61	MP/2026/09/0061	11	keluar	-19	\N	2026-08-05	4	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
62	MP/2026/09/0062	11	keluar	-17	\N	2026-08-09	7	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
63	MP/2026/09/0063	11	keluar	-18	\N	2026-09-06	7	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
64	MP/2026/09/0064	12	masuk	50	27000.00	2026-05-09	\N	\N	Toko contoh untuk peragaan	FKT-30121	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
65	MP/2026/09/0065	12	keluar	-6	\N	2026-06-06	2	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
66	MP/2026/09/0066	12	keluar	-1	\N	2026-06-23	6	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
67	MP/2026/09/0067	12	keluar	-6	\N	2026-07-16	8	4	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
68	MP/2026/09/0068	12	keluar	-1	\N	2026-07-03	7	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
69	MP/2026/09/0069	12	keluar	-8	\N	2026-08-09	2	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
70	MP/2026/09/0070	12	keluar	-4	\N	2026-09-06	3	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
71	MP/2026/09/0071	13	masuk	57	12000.00	2026-05-06	\N	\N	Toko contoh untuk peragaan	FKT-10285	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
72	MP/2026/09/0072	13	keluar	-4	\N	2026-06-06	6	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
73	MP/2026/09/0073	13	keluar	-3	\N	2026-06-21	6	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
74	MP/2026/09/0074	13	keluar	-6	\N	2026-07-18	4	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
75	MP/2026/09/0075	13	keluar	-3	\N	2026-07-21	5	13	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
76	MP/2026/09/0076	13	keluar	-3	\N	2026-08-21	7	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:56	2026-09-06 21:23:56
77	MP/2026/09/0077	13	keluar	-2	\N	2026-08-20	1	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
78	MP/2026/09/0078	14	masuk	23	92000.00	2026-05-26	\N	\N	Toko contoh untuk peragaan	FKT-82851	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
79	MP/2026/09/0079	14	keluar	-2	\N	2026-06-06	7	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
80	MP/2026/09/0080	14	keluar	-2	\N	2026-06-25	8	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
81	MP/2026/09/0081	14	keluar	-1	\N	2026-06-28	2	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
82	MP/2026/09/0082	14	keluar	-3	\N	2026-08-08	4	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
83	MP/2026/09/0083	14	keluar	-3	\N	2026-08-25	5	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
84	MP/2026/09/0084	15	masuk	15	98000.00	2026-05-17	\N	\N	Toko contoh untuk peragaan	FKT-51284	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
85	MP/2026/09/0085	15	keluar	-1	\N	2026-06-06	2	11	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
86	MP/2026/09/0086	15	keluar	-2	\N	2026-06-17	7	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
87	MP/2026/09/0087	15	keluar	-1	\N	2026-07-06	8	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
88	MP/2026/09/0088	15	keluar	-3	\N	2026-08-08	2	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
89	MP/2026/09/0089	15	keluar	-4	\N	2026-08-21	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
90	MP/2026/09/0090	16	masuk	12	780000.00	2026-05-19	\N	\N	Toko contoh untuk peragaan	FKT-44357	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
91	MP/2026/09/0091	16	keluar	-2	\N	2026-06-06	1	7	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
92	MP/2026/09/0092	16	keluar	-1	\N	2026-06-16	4	13	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
93	MP/2026/09/0093	16	keluar	-2	\N	2026-07-14	5	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
94	MP/2026/09/0094	16	keluar	-1	\N	2026-08-05	6	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
95	MP/2026/09/0095	17	masuk	40	26000.00	2026-05-26	\N	\N	Toko contoh untuk peragaan	FKT-53124	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
96	MP/2026/09/0096	17	keluar	-4	\N	2026-06-06	2	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
97	MP/2026/09/0097	17	keluar	-1	\N	2026-06-17	5	3	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
98	MP/2026/09/0098	17	keluar	-4	\N	2026-07-04	4	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
99	MP/2026/09/0099	17	keluar	-4	\N	2026-07-12	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
100	MP/2026/09/0100	17	keluar	-4	\N	2026-07-20	6	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
101	MP/2026/09/0101	17	keluar	-2	\N	2026-08-20	4	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
102	MP/2026/09/0102	18	masuk	66	8500.00	2026-05-16	\N	\N	Toko contoh untuk peragaan	FKT-61044	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
103	MP/2026/09/0103	18	keluar	-5	\N	2026-06-06	3	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
104	MP/2026/09/0104	18	keluar	-9	\N	2026-06-16	1	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
105	MP/2026/09/0105	18	keluar	-9	\N	2026-07-04	1	13	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
106	MP/2026/09/0106	19	masuk	18	21000.00	2026-05-20	\N	\N	Toko contoh untuk peragaan	FKT-81853	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
107	MP/2026/09/0107	19	keluar	-2	\N	2026-06-06	3	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
108	MP/2026/09/0108	19	keluar	-1	\N	2026-06-28	8	1	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
109	MP/2026/09/0109	19	keluar	-3	\N	2026-07-16	6	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
110	MP/2026/09/0110	19	keluar	-1	\N	2026-07-03	6	3	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
111	MP/2026/09/0111	19	keluar	-4	\N	2026-07-24	8	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
112	MP/2026/09/0112	19	keluar	-4	\N	2026-07-26	7	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
113	MP/2026/09/0113	20	masuk	31	19500.00	2026-05-19	\N	\N	Toko contoh untuk peragaan	FKT-62636	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:57	2026-09-06 21:23:57
114	MP/2026/09/0114	20	keluar	-1	\N	2026-06-06	8	13	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
115	MP/2026/09/0115	20	keluar	-2	\N	2026-06-27	6	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
116	MP/2026/09/0116	20	keluar	-5	\N	2026-07-08	5	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
117	MP/2026/09/0117	21	masuk	24	16500.00	2026-05-08	\N	\N	Toko contoh untuk peragaan	FKT-77168	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
118	MP/2026/09/0118	21	keluar	-1	\N	2026-06-06	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
119	MP/2026/09/0119	21	keluar	-4	\N	2026-06-20	6	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
120	MP/2026/09/0120	21	keluar	-4	\N	2026-07-20	2	13	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
121	MP/2026/09/0121	21	keluar	-4	\N	2026-07-06	5	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
122	MP/2026/09/0122	21	keluar	-1	\N	2026-07-28	3	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
123	MP/2026/09/0123	22	masuk	22	24000.00	2026-05-17	\N	\N	Toko contoh untuk peragaan	FKT-88850	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
124	MP/2026/09/0124	22	keluar	-4	\N	2026-06-06	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
125	MP/2026/09/0125	22	keluar	-1	\N	2026-06-15	2	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
126	MP/2026/09/0126	22	keluar	-3	\N	2026-07-04	1	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
127	MP/2026/09/0127	22	keluar	-1	\N	2026-07-03	7	3	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
128	MP/2026/09/0128	22	keluar	-2	\N	2026-08-09	7	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
129	MP/2026/09/0129	23	masuk	21	12500.00	2026-05-18	\N	\N	Toko contoh untuk peragaan	FKT-19782	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
130	MP/2026/09/0130	23	keluar	-4	\N	2026-06-06	8	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
131	MP/2026/09/0131	23	keluar	-4	\N	2026-06-26	4	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
132	MP/2026/09/0132	23	keluar	-1	\N	2026-07-10	4	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
133	MP/2026/09/0133	23	keluar	-4	\N	2026-07-30	6	5	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
134	MP/2026/09/0134	23	keluar	-4	\N	2026-08-13	5	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
135	MP/2026/09/0135	23	keluar	-4	\N	2026-09-06	5	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
136	MP/2026/09/0136	24	masuk	43	21000.00	2026-05-07	\N	\N	Toko contoh untuk peragaan	FKT-62735	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
137	MP/2026/09/0137	24	keluar	-1	\N	2026-06-06	1	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
138	MP/2026/09/0138	24	keluar	-4	\N	2026-06-18	1	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
139	MP/2026/09/0139	24	keluar	-4	\N	2026-06-24	8	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
140	MP/2026/09/0140	24	keluar	-1	\N	2026-08-05	1	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
141	MP/2026/09/0141	24	keluar	-6	\N	2026-08-05	8	4	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
142	MP/2026/09/0142	25	masuk	58	34000.00	2026-05-11	\N	\N	Toko contoh untuk peragaan	FKT-99306	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
143	MP/2026/09/0143	25	keluar	-3	\N	2026-06-06	4	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
144	MP/2026/09/0144	25	keluar	-4	\N	2026-06-15	2	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
145	MP/2026/09/0145	25	keluar	-5	\N	2026-06-24	1	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
146	MP/2026/09/0146	25	keluar	-2	\N	2026-07-15	7	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
147	MP/2026/09/0147	25	keluar	-2	\N	2026-08-25	4	9	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
148	MP/2026/09/0148	25	keluar	-4	\N	2026-09-06	8	8	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:58	2026-09-06 21:23:58
149	MP/2026/09/0149	26	masuk	21	27500.00	2026-05-12	\N	\N	Toko contoh untuk peragaan	FKT-74472	[DATA DEMO] Pembelian awal untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
150	MP/2026/09/0150	26	keluar	-2	\N	2026-06-06	3	\N	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
151	MP/2026/09/0151	26	keluar	-2	\N	2026-06-16	3	6	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
152	MP/2026/09/0152	26	keluar	-1	\N	2026-07-10	2	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
153	MP/2026/09/0153	26	keluar	-4	\N	2026-08-02	4	2	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
154	MP/2026/09/0154	26	keluar	-4	\N	2026-08-29	1	10	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
155	MP/2026/09/0155	26	keluar	-1	\N	2026-07-21	2	12	\N	\N	[DATA DEMO] Pengambilan untuk peragaan.	\N	2026-09-06 21:23:59	2026-09-06 21:23:59
156	MP/2026/09/0156	2	keluar	-3	\N	2026-09-06	1	\N	\N	\N	Uji verifikasi kiriman C	2	2026-09-06 21:48:01	2026-09-06 21:48:01
157	MP/2026/09/0157	2	masuk	10	61500.00	2026-09-06	\N	\N	\N	\N	\N	2	2026-09-06 21:48:44	2026-09-06 21:48:44
158	MP/2026/09/0158	1	keluar	-5	\N	2026-09-08	5	5	\N	PB/2026/09/0001	Penyerahan permintaan PB/2026/09/0001. Perlengkapan tulis dan pantry rapat tutup buku September	2	2026-09-08 06:02:15	2026-09-08 06:02:15
159	MP/2026/09/0159	4	keluar	-4	\N	2026-09-08	5	5	\N	PB/2026/09/0001	Penyerahan permintaan PB/2026/09/0001. Perlengkapan tulis dan pantry rapat tutup buku September	2	2026-09-08 06:02:15	2026-09-08 06:02:15
160	MP/2026/09/0160	1	keluar	-3	\N	2026-09-08	5	5	\N	PB/2026/09/0002	Penyerahan permintaan PB/2026/09/0002. Kertas cetak laporan keuangan kuartal tiga	2	2026-09-08 06:11:32	2026-09-08 06:11:32
161	MP/2026/09/0161	4	masuk	12	6500.00	2026-09-08	\N	\N	PT. Abadi Jaya	PN/2026/09/0001	Penerimaan PN/2026/09/0001 atas pembelian PP/2026/09/0001. Pengadaan ATK yang stoknya habis, September	2	2026-09-08 10:09:58	2026-09-08 10:09:58
162	MP/2026/09/0162	8	masuk	12	8500.00	2026-09-08	\N	\N	PT. Abadi Jaya	PN/2026/09/0001	Penerimaan PN/2026/09/0001 atas pembelian PP/2026/09/0001. Pengadaan ATK yang stoknya habis, September	2	2026-09-08 10:09:58	2026-09-08 10:09:58
163	MP/2026/09/0163	23	masuk	4	22000.00	2026-09-08	\N	\N	PT. Abadi Jaya	PN/2026/09/0001	Penerimaan PN/2026/09/0001 atas pembelian PP/2026/09/0001. Pengadaan ATK yang stoknya habis, September	2	2026-09-08 10:09:58	2026-09-08 10:09:58
164	MP/2026/09/0164	15	masuk	4	95000.00	2026-09-08	\N	\N	Toko Sinar Jaya, Jalan Kaliurang	PN/2026/09/0002	Penerimaan PN/2026/09/0002 atas pembelian PP/2026/09/0002. Belanja mendadak barang kebersihan dan tinta printer	2	2026-09-08 10:12:46	2026-09-08 10:12:46
165	MP/2026/09/0165	19	masuk	6	18000.00	2026-09-08	\N	\N	Toko Sinar Jaya, Jalan Kaliurang	PN/2026/09/0002	Penerimaan PN/2026/09/0002 atas pembelian PP/2026/09/0002. Belanja mendadak barang kebersihan dan tinta printer	2	2026-09-08 10:12:46	2026-09-08 10:12:46
166	MP/2026/09/0166	23	masuk	6	22000.00	2026-09-08	\N	\N	PT. Abadi Jaya	PN/2026/09/0003	Penerimaan PN/2026/09/0003 atas pembelian PP/2026/09/0001. Pengadaan ATK yang stoknya habis, September	2	2026-09-08 10:20:59	2026-09-08 10:20:59
167	MP/2026/09/0167	8	koreksi_kurang	-4	\N	2026-09-08	\N	\N	\N	\N	Empat spidol kering dan dibuang setelah lembar hitungan opname diisi	2	2026-09-08 11:32:50	2026-09-08 11:32:50
168	MP/2026/09/0168	13	koreksi_kurang	-2	\N	2026-09-08	\N	\N	\N	OP/2026/09/0001	Hasil opname OP/2026/09/0001. Hitungan fisik 34 roll, catatan 36 roll. Dua roll terpakai untuk kemasan kiriman, tidak sempat dicatat	2	2026-09-08 11:34:47	2026-09-08 11:34:47
169	MP/2026/09/0169	11	koreksi_tambah	3	\N	2026-09-08	\N	\N	\N	OP/2026/09/0001	Hasil opname OP/2026/09/0001. Hitungan fisik 103 pcs, catatan 100 pcs. Tiga pcs ketemu di laci meja resepsionis	2	2026-09-08 11:34:47	2026-09-08 11:34:47
170	MP/2026/09/0170	6	koreksi_kurang	-2	\N	2026-09-08	\N	\N	\N	OP/2026/09/0001	Hasil opname OP/2026/09/0001. Hitungan fisik 28 pcs, catatan 30 pcs. Dua batang kering, dibuang	2	2026-09-08 11:34:47	2026-09-08 11:34:47
171	MP/2026/09/0171	21	keluar	-2	\N	2026-09-08	1	\N	\N	\N	[DATA UJI] Dipakai pantry saat opname OP/2026/09/0002 sedang berjalan.	2	2026-09-08 11:47:08	2026-09-08 11:47:08
172	MP/2026/09/0172	21	koreksi_tambah	2	\N	2026-09-08	\N	\N	\N	\N	[DATA UJI] Mengembalikan stok gula pasir setelah mutasi uji 2 kg pada pemeriksaan kiriman O. Bukan koreksi hasil hitungan fisik.	2	2026-09-08 11:49:57	2026-09-08 11:49:57
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, is_super_admin, is_active, last_login_at) FROM stdin;
1	Administrator	admin@gais.test	\N	$2y$12$0cYMELknjB4TBFne0fJOcO7UIAOjCNKYkMQ7xM82c3MKqVXP4kRTq	\N	2026-09-06 09:04:37	2026-09-08 05:50:04	t	t	2026-09-08 05:50:04
2	tester	test@gais.test	\N	$2y$12$kBy.zUvUMndI57PapXUvkOPHDEx198Iibthp4iWJk2.TmPb2ywDNu	\N	2026-09-06 15:26:03	2026-09-08 05:53:07	f	t	2026-09-08 05:53:07
\.


--
-- Data for Name: vehicle_bookings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicle_bookings (id, code, requester_employee_id, department_id, destination, purpose, passenger_count, needs_driver, start_at, end_at, status, vehicle_id, driver_employee_id, assigned_by_user_id, assigned_at, approver_employee_id, approved_at, approval_note, approval_skipped_reason, rejection_reason, closed_at, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	PK/2026/09/0001	1	1	[DATA UJI] Gudang Bekasi	[DATA UJI] Mengantar sampel barang dan menjemput dokumen.	3	t	2026-09-10 08:00:00	2026-09-10 16:00:00	selesai	1	2	2	2026-09-07 11:23:41	\N	2026-09-07 11:22:43	\N	Departemen pemohon belum punya kepala departemen	\N	2026-09-07 11:25:34	\N	2	2026-09-07 11:22:43	2026-09-07 11:25:34
3	PK/2026/09/0003	1	1	[DATA UJI] Bandara Soekarno Hatta	[DATA UJI] Menjemput tamu kantor pusat.	2	f	2026-09-10 15:00:00	2026-09-10 20:00:00	disetujui	\N	\N	\N	\N	\N	2026-09-07 11:30:46	\N	Departemen pemohon belum punya kepala departemen	\N	\N	\N	2	2026-09-07 11:30:46	2026-09-07 11:30:46
2	PK/2026/09/0002	1	1	[DATA UJI] Kantor pajak Jaksel	[DATA UJI] Menyerahkan berkas pajak tahunan.	2	f	2026-09-10 13:00:00	2026-09-10 18:00:00	ditugaskan	1	\N	2	2026-09-07 11:30:57	\N	2026-09-07 11:23:13	\N	Departemen pemohon belum punya kepala departemen	\N	\N	\N	2	2026-09-07 11:23:13	2026-09-07 11:30:57
\.


--
-- Data for Name: vehicle_documents; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicle_documents (id, vehicle_id, type, document_number, issued_date, expires_at, issuer, cost, file_path, original_name, size_bytes, notes, uploaded_by_user_id, created_at, updated_at) FROM stdin;
1	1	pajak_tahunan	SKPD-2025-0001	2025-08-20	2026-08-20	Samsat Jakarta Selatan	3450000.00	\N	\N	\N	\N	2	2026-09-07 10:38:20	2026-09-07 10:38:20
2	1	kir	KIR-778	2026-04-01	2026-10-01	Dishub DKI Jakarta	275000.00	\N	\N	\N	\N	2	2026-09-07 10:38:41	2026-09-07 10:38:41
3	1	pajak_tahunan	SKPD-2025-0001	2026-09-07	2027-08-20	Samsat Jakarta Selatan	3720000.00	\N	\N	\N	\N	2	2026-09-07 10:39:26	2026-09-07 10:39:26
\.


--
-- Data for Name: vehicle_photos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicle_photos (id, vehicle_id, type, name, file_path, original_name, size_bytes, taken_date, odometer_km, notes, uploaded_by_user_id, created_at, updated_at) FROM stdin;
1	1	depan	[DATA UJI] Tampak depan saat pendataan	foto-kendaraan/01M1XQZ5F4XN7XWB7SC5JYKXKV.png	data-uji-tampak-depan.png	1810	2026-09-07	84210	\N	2	2026-09-07 10:52:34	2026-09-07 10:52:34
\.


--
-- Data for Name: vehicle_refuelings; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicle_refuelings (id, vehicle_id, filled_at, odometer_km, liters, cost, station, fuel_type, is_full_tank, driver_employee_id, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	1	2026-08-14	83500	31.00	465000.00	[DATA UJI] SPBU Cakung	bensin	t	\N	\N	2	2026-09-07 11:29:23	2026-09-07 11:29:23
2	1	2026-09-04	84210	52.00	780000.00	[DATA UJI] SPBU Bekasi	bensin	t	\N	\N	2	2026-09-07 11:29:41	2026-09-07 11:29:41
\.


--
-- Data for Name: vehicle_trips; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicle_trips (id, vehicle_id, vehicle_booking_id, driver_employee_id, departed_at, returned_at, start_odometer_km, end_odometer_km, destination, purpose, notes, created_by_user_id, created_at, updated_at) FROM stdin;
1	1	1	2	2026-09-10 08:05:00	2026-09-10 16:20:00	84210	84356	[DATA UJI] Gudang Bekasi	[DATA UJI] Mengantar sampel barang.	\N	2	2026-09-07 11:24:57	2026-09-07 11:25:34
\.


--
-- Data for Name: vehicles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vehicles (id, asset_id, plate_number, vehicle_type, usage_mode, chassis_number, engine_number, color, production_year, fuel_type, transmission, seat_capacity, payload_kg, default_driver_employee_id, last_odometer_km, last_odometer_date, notes, is_active, created_at, updated_at) FROM stdin;
1	148	B 1234 UJI	mobil_penumpang	pool	MHKA1234567890123	\N	\N	2018	bensin	\N	\N	\N	\N	84356	2026-09-10	[DATA UJI] dibuat untuk pengujian kiriman H	t	2026-09-07 10:30:24	2026-09-07 11:25:34
\.


--
-- Data for Name: vendor_bill_lines; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendor_bill_lines (id, vendor_bill_id, expense_category_id, department_id, description, amount, created_at, updated_at) FROM stdin;
1	1	6	5	Porsi lantai 2 dan 3	4500000.00	2026-09-07 12:39:01	2026-09-07 12:39:01
2	1	6	4	Porsi lantai 1 dan lobi	3200000.00	2026-09-07 12:39:28	2026-09-07 12:39:28
3	1	6	\N	Koridor dan area parkir bersama	1300000.00	2026-09-07 12:39:40	2026-09-07 12:39:40
4	2	5	4	Pemakaian gedung utama	2750000.00	2026-09-07 12:50:02	2026-09-07 12:50:02
5	3	10	4	Pengadaan ATK September sesuai PP/2026/09/0001	400000.00	2026-09-08 10:15:36	2026-09-08 10:21:55
\.


--
-- Data for Name: vendor_bills; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendor_bills (id, code, vendor_id, invoice_number, invoice_date, due_date, description, status, approved_by_user_id, approved_at, rejection_reason, paid_date, payment_reference, paid_by_user_id, file_path, original_name, size_bytes, notes, created_by_user_id, created_at, updated_at, supply_purchase_id) FROM stdin;
1	TG/2026/09/0001	1	INV-SAT-2608	2026-08-31	2026-09-15	[DATA UJI] Jasa kebersihan dan keamanan gedung Head Office periode Agustus 2026.	dibayar	2	2026-09-07 12:45:33	\N	2026-09-07	TRF-BCA-20260907-118	2	\N	\N	\N	\N	2	2026-09-07 12:37:59	2026-09-07 12:48:58	\N
2	TG/2026/09/0002	1	INV-PLN-0926	2026-09-01	2026-08-25	[DATA UJI] Tagihan uji untuk memeriksa jalur penolakan dan perbaikan.	draft	\N	\N	Pembebanan seharusnya dibagi dengan departemen Operations, bukan seluruhnya ke General Affair.	\N	\N	\N	\N	\N	\N	\N	2	2026-09-07 12:49:33	2026-09-07 12:50:50	\N
3	TG/2026/09/0003	2	INV-2026-4471	2026-09-08	\N	Faktur pengadaan ATK September, seluruh pesanan ditagih di muka	dibatalkan	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	2	2026-09-08 10:14:06	2026-09-08 10:22:48	1
\.


--
-- Data for Name: vendors; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.vendors (id, code, name, type, specialization, contact_person, phone, email, address, tax_number, notes, is_active, created_at, updated_at) FROM stdin;
1	VND-AC-01	[DATA UJI] Sejuk Abadi Teknik	jasa	AC dan pendingin ruangan	Bagian layanan	\N	\N	\N	\N	\N	t	2026-09-07 05:11:52	2026-09-07 05:11:52
2	VND-001	PT. Abadi Jaya	jasa	Konsultan	Toto  Priyono	08112502883	toto.priyono@gmail.com	Sidorejo selomartani kalasan sleman yk	123452345	ok	t	2026-09-07 06:56:02	2026-09-07 06:56:02
\.


--
-- Data for Name: work_order_attachments; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.work_order_attachments (id, work_order_id, type, name, file_path, original_name, size_bytes, notes, uploaded_by_user_id, created_at, updated_at) FROM stdin;
1	5	foto_kerusakan	foto	lampiran-perintah-kerja/01M1X9E4N58FEV0EGRAPPKAGYE.jpeg	genset.jpeg	53041	rusak	1	2026-09-07 06:38:36	2026-09-07 06:38:36
\.


--
-- Data for Name: work_orders; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.work_orders (id, code, asset_id, type, maintenance_schedule_id, priority, status, reported_date, reported_by_employee_id, problem, scheduled_date, vendor_id, technician_employee_id, completed_date, work_done, cost, condition_after, cancel_reason, created_by_user_id, created_at, updated_at, maintenance_visit_id, service_request_id) FROM stdin;
1	WO/2026/09/0001	2	preventif	1	normal	selesai	2026-09-07	\N	[DATA UJI] Servis rutin dan cuci AC\n\nCuci filter dan evaporator\nPeriksa tekanan freon	2027-03-05	1	\N	2026-09-07	Servis lengkap, filter diganti, kompresor diperiksa.	1250000.00	\N	\N	2	2026-09-07 05:15:04	2026-09-07 05:16:55	3	\N
2	WO/2026/09/0002	34	korektif	\N	normal	dibuka	2026-09-07	2	layar blank	2026-09-18	1	8	\N	\N	\N	\N	\N	1	2026-09-07 05:29:12	2026-09-07 05:29:12	\N	\N
5	WO/2026/09/0005	\N	korektif	\N	tinggi	dibuka	2026-09-07	8	AC berasap\n\nPer pagi ini ac mulai berasap...\n\nLokasi: Head Office	2026-09-10	1	8	\N	\N	\N	\N	\N	1	2026-09-07 06:37:28	2026-09-07 06:37:28	\N	1
\.


--
-- Name: asset_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.asset_categories_id_seq', 13, true);


--
-- Name: asset_disposals_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.asset_disposals_id_seq', 1, true);


--
-- Name: asset_documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.asset_documents_id_seq', 1, false);


--
-- Name: asset_transfers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.asset_transfers_id_seq', 2, true);


--
-- Name: assets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.assets_id_seq', 154, true);


--
-- Name: audit_logs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.audit_logs_id_seq', 905, true);


--
-- Name: budgets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.budgets_id_seq', 10, true);


--
-- Name: business_trip_expenses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.business_trip_expenses_id_seq', 11, true);


--
-- Name: business_trip_participants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.business_trip_participants_id_seq', 4, true);


--
-- Name: business_trips_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.business_trips_id_seq', 2, true);


--
-- Name: cleaning_inspection_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cleaning_inspection_lines_id_seq', 5, true);


--
-- Name: cleaning_inspections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.cleaning_inspections_id_seq', 3, true);


--
-- Name: departments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.departments_id_seq', 8, true);


--
-- Name: depreciation_entries_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.depreciation_entries_id_seq', 267, true);


--
-- Name: depreciation_periods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.depreciation_periods_id_seq', 3, true);


--
-- Name: employees_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.employees_id_seq', 13, true);


--
-- Name: expense_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.expense_categories_id_seq', 12, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: incident_reports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.incident_reports_id_seq', 1, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: letters_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.letters_id_seq', 3, true);


--
-- Name: locations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.locations_id_seq', 21, true);


--
-- Name: maintenance_schedules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.maintenance_schedules_id_seq', 1, true);


--
-- Name: maintenance_visits_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.maintenance_visits_id_seq', 4, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 77, true);


--
-- Name: modules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.modules_id_seq', 38, true);


--
-- Name: number_sequence_periods_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.number_sequence_periods_id_seq', 401, true);


--
-- Name: number_sequences_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.number_sequences_id_seq', 67, true);


--
-- Name: parcel_shipments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.parcel_shipments_id_seq', 1, true);


--
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.permissions_id_seq', 168, true);


--
-- Name: reimbursement_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.reimbursement_lines_id_seq', 6, true);


--
-- Name: reimbursements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.reimbursements_id_seq', 4, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.roles_id_seq', 4, true);


--
-- Name: security_shifts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.security_shifts_id_seq', 4, true);


--
-- Name: service_areas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.service_areas_id_seq', 4, true);


--
-- Name: service_request_attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.service_request_attachments_id_seq', 1, false);


--
-- Name: service_request_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.service_request_categories_id_seq', 9, true);


--
-- Name: service_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.service_requests_id_seq', 3, true);


--
-- Name: service_staff_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.service_staff_id_seq', 3, true);


--
-- Name: settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.settings_id_seq', 17, true);


--
-- Name: stock_opname_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.stock_opname_lines_id_seq', 493, true);


--
-- Name: stock_opnames_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.stock_opnames_id_seq', 4, true);


--
-- Name: supply_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_items_id_seq', 27, true);


--
-- Name: supply_opname_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_opname_lines_id_seq', 43, true);


--
-- Name: supply_opnames_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_opnames_id_seq', 4, true);


--
-- Name: supply_purchase_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_purchase_lines_id_seq', 6, true);


--
-- Name: supply_purchases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_purchases_id_seq', 3, true);


--
-- Name: supply_receipt_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_receipt_lines_id_seq', 6, true);


--
-- Name: supply_receipts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_receipts_id_seq', 3, true);


--
-- Name: supply_request_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_request_lines_id_seq', 6, true);


--
-- Name: supply_requests_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_requests_id_seq', 3, true);


--
-- Name: supply_transactions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.supply_transactions_id_seq', 172, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 2, true);


--
-- Name: vehicle_bookings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicle_bookings_id_seq', 3, true);


--
-- Name: vehicle_documents_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicle_documents_id_seq', 3, true);


--
-- Name: vehicle_photos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicle_photos_id_seq', 1, true);


--
-- Name: vehicle_refuelings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicle_refuelings_id_seq', 2, true);


--
-- Name: vehicle_trips_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicle_trips_id_seq', 1, true);


--
-- Name: vehicles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vehicles_id_seq', 1, true);


--
-- Name: vendor_bill_lines_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vendor_bill_lines_id_seq', 5, true);


--
-- Name: vendor_bills_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vendor_bills_id_seq', 3, true);


--
-- Name: vendors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.vendors_id_seq', 2, true);


--
-- Name: work_order_attachments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.work_order_attachments_id_seq', 1, true);


--
-- Name: work_orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.work_orders_id_seq', 5, true);


--
-- Name: asset_categories asset_categories_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_categories
    ADD CONSTRAINT asset_categories_code_unique UNIQUE (code);


--
-- Name: asset_categories asset_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_categories
    ADD CONSTRAINT asset_categories_pkey PRIMARY KEY (id);


--
-- Name: asset_disposals asset_disposals_asset_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_asset_id_unique UNIQUE (asset_id);


--
-- Name: asset_disposals asset_disposals_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_code_unique UNIQUE (code);


--
-- Name: asset_disposals asset_disposals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_pkey PRIMARY KEY (id);


--
-- Name: asset_documents asset_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_documents
    ADD CONSTRAINT asset_documents_pkey PRIMARY KEY (id);


--
-- Name: asset_transfers asset_transfers_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_code_unique UNIQUE (code);


--
-- Name: asset_transfers asset_transfers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_pkey PRIMARY KEY (id);


--
-- Name: assets assets_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_code_unique UNIQUE (code);


--
-- Name: assets assets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_pkey PRIMARY KEY (id);


--
-- Name: audit_logs audit_logs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_pkey PRIMARY KEY (id);


--
-- Name: budgets budgets_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_pkey PRIMARY KEY (id);


--
-- Name: budgets budgets_unik; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_unik UNIQUE (department_id, expense_category_id, fiscal_year);


--
-- Name: business_trip_expenses business_trip_expenses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_expenses
    ADD CONSTRAINT business_trip_expenses_pkey PRIMARY KEY (id);


--
-- Name: business_trip_participants business_trip_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_participants
    ADD CONSTRAINT business_trip_participants_pkey PRIMARY KEY (id);


--
-- Name: business_trip_participants business_trip_participants_unik; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_participants
    ADD CONSTRAINT business_trip_participants_unik UNIQUE (business_trip_id, employee_id);


--
-- Name: business_trips business_trips_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_code_unique UNIQUE (code);


--
-- Name: business_trips business_trips_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cleaning_inspection_lines cleaning_inspection_lines_cleaning_inspection_id_service_area_i; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspection_lines
    ADD CONSTRAINT cleaning_inspection_lines_cleaning_inspection_id_service_area_i UNIQUE (cleaning_inspection_id, service_area_id);


--
-- Name: cleaning_inspection_lines cleaning_inspection_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspection_lines
    ADD CONSTRAINT cleaning_inspection_lines_pkey PRIMARY KEY (id);


--
-- Name: cleaning_inspections cleaning_inspections_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspections
    ADD CONSTRAINT cleaning_inspections_code_unique UNIQUE (code);


--
-- Name: cleaning_inspections cleaning_inspections_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspections
    ADD CONSTRAINT cleaning_inspections_pkey PRIMARY KEY (id);


--
-- Name: departments departments_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_code_unique UNIQUE (code);


--
-- Name: departments departments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_pkey PRIMARY KEY (id);


--
-- Name: depreciation_entries depreciation_entries_asset_id_period_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_entries
    ADD CONSTRAINT depreciation_entries_asset_id_period_unique UNIQUE (asset_id, period);


--
-- Name: depreciation_entries depreciation_entries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_entries
    ADD CONSTRAINT depreciation_entries_pkey PRIMARY KEY (id);


--
-- Name: depreciation_periods depreciation_periods_period_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_periods
    ADD CONSTRAINT depreciation_periods_period_unique UNIQUE (period);


--
-- Name: depreciation_periods depreciation_periods_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_periods
    ADD CONSTRAINT depreciation_periods_pkey PRIMARY KEY (id);


--
-- Name: employees employees_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_email_unique UNIQUE (email);


--
-- Name: employees employees_nip_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_nip_unique UNIQUE (nip);


--
-- Name: employees employees_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_pkey PRIMARY KEY (id);


--
-- Name: employees employees_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_user_id_unique UNIQUE (user_id);


--
-- Name: expense_categories expense_categories_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expense_categories
    ADD CONSTRAINT expense_categories_code_unique UNIQUE (code);


--
-- Name: expense_categories expense_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.expense_categories
    ADD CONSTRAINT expense_categories_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: incident_reports incident_reports_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_code_unique UNIQUE (code);


--
-- Name: incident_reports incident_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: letters letters_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_code_unique UNIQUE (code);


--
-- Name: letters letters_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_pkey PRIMARY KEY (id);


--
-- Name: locations locations_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_code_unique UNIQUE (code);


--
-- Name: locations locations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_pkey PRIMARY KEY (id);


--
-- Name: maintenance_schedules maintenance_schedules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT maintenance_schedules_pkey PRIMARY KEY (id);


--
-- Name: maintenance_visits maintenance_visits_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: modules modules_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modules
    ADD CONSTRAINT modules_code_unique UNIQUE (code);


--
-- Name: modules modules_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.modules
    ADD CONSTRAINT modules_pkey PRIMARY KEY (id);


--
-- Name: number_sequence_periods number_sequence_periods_number_sequence_id_period_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequence_periods
    ADD CONSTRAINT number_sequence_periods_number_sequence_id_period_unique UNIQUE (number_sequence_id, period);


--
-- Name: number_sequence_periods number_sequence_periods_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequence_periods
    ADD CONSTRAINT number_sequence_periods_pkey PRIMARY KEY (id);


--
-- Name: number_sequences number_sequences_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequences
    ADD CONSTRAINT number_sequences_code_unique UNIQUE (code);


--
-- Name: number_sequences number_sequences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequences
    ADD CONSTRAINT number_sequences_pkey PRIMARY KEY (id);


--
-- Name: parcel_shipments parcel_shipments_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_code_unique UNIQUE (code);


--
-- Name: parcel_shipments parcel_shipments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: permission_role permission_role_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_pkey PRIMARY KEY (role_id, permission_id);


--
-- Name: permission_user permission_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_user
    ADD CONSTRAINT permission_user_pkey PRIMARY KEY (user_id, permission_id);


--
-- Name: permissions permissions_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_key_unique UNIQUE (key);


--
-- Name: permissions permissions_module_id_action_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_module_id_action_unique UNIQUE (module_id, action);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: reimbursement_lines reimbursement_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursement_lines
    ADD CONSTRAINT reimbursement_lines_pkey PRIMARY KEY (id);


--
-- Name: reimbursements reimbursements_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_code_unique UNIQUE (code);


--
-- Name: reimbursements reimbursements_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_pkey PRIMARY KEY (id);


--
-- Name: role_user role_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_pkey PRIMARY KEY (role_id, user_id);


--
-- Name: roles roles_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_code_unique UNIQUE (code);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: security_shifts security_shifts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_pkey PRIMARY KEY (id);


--
-- Name: security_shifts security_shifts_unik; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_unik UNIQUE (shift_date, shift, service_staff_id);


--
-- Name: service_areas service_areas_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_areas
    ADD CONSTRAINT service_areas_code_unique UNIQUE (code);


--
-- Name: service_areas service_areas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_areas
    ADD CONSTRAINT service_areas_pkey PRIMARY KEY (id);


--
-- Name: service_request_attachments service_request_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_attachments
    ADD CONSTRAINT service_request_attachments_pkey PRIMARY KEY (id);


--
-- Name: service_request_categories service_request_categories_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_categories
    ADD CONSTRAINT service_request_categories_code_unique UNIQUE (code);


--
-- Name: service_request_categories service_request_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_categories
    ADD CONSTRAINT service_request_categories_pkey PRIMARY KEY (id);


--
-- Name: service_requests service_requests_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_code_unique UNIQUE (code);


--
-- Name: service_requests service_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_pkey PRIMARY KEY (id);


--
-- Name: service_staff service_staff_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_staff
    ADD CONSTRAINT service_staff_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: settings settings_key_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_key_unique UNIQUE (key);


--
-- Name: settings settings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (id);


--
-- Name: stock_opname_lines stock_opname_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_pkey PRIMARY KEY (id);


--
-- Name: stock_opname_lines stock_opname_lines_stock_opname_id_asset_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_stock_opname_id_asset_id_unique UNIQUE (stock_opname_id, asset_id);


--
-- Name: stock_opnames stock_opnames_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_code_unique UNIQUE (code);


--
-- Name: stock_opnames stock_opnames_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_pkey PRIMARY KEY (id);


--
-- Name: supply_items supply_items_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_items
    ADD CONSTRAINT supply_items_code_unique UNIQUE (code);


--
-- Name: supply_items supply_items_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_items
    ADD CONSTRAINT supply_items_pkey PRIMARY KEY (id);


--
-- Name: supply_opname_lines supply_opname_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines
    ADD CONSTRAINT supply_opname_lines_pkey PRIMARY KEY (id);


--
-- Name: supply_opname_lines supply_opname_lines_supply_opname_id_supply_item_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines
    ADD CONSTRAINT supply_opname_lines_supply_opname_id_supply_item_id_unique UNIQUE (supply_opname_id, supply_item_id);


--
-- Name: supply_opnames supply_opnames_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames
    ADD CONSTRAINT supply_opnames_code_unique UNIQUE (code);


--
-- Name: supply_opnames supply_opnames_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames
    ADD CONSTRAINT supply_opnames_pkey PRIMARY KEY (id);


--
-- Name: supply_purchase_lines supply_purchase_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchase_lines
    ADD CONSTRAINT supply_purchase_lines_pkey PRIMARY KEY (id);


--
-- Name: supply_purchase_lines supply_purchase_lines_supply_purchase_id_supply_item_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchase_lines
    ADD CONSTRAINT supply_purchase_lines_supply_purchase_id_supply_item_id_unique UNIQUE (supply_purchase_id, supply_item_id);


--
-- Name: supply_purchases supply_purchases_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases
    ADD CONSTRAINT supply_purchases_code_unique UNIQUE (code);


--
-- Name: supply_purchases supply_purchases_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases
    ADD CONSTRAINT supply_purchases_pkey PRIMARY KEY (id);


--
-- Name: supply_receipt_lines supply_receipt_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines
    ADD CONSTRAINT supply_receipt_lines_pkey PRIMARY KEY (id);


--
-- Name: supply_receipt_lines supply_receipt_lines_supply_receipt_id_supply_purchase_line_id_; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines
    ADD CONSTRAINT supply_receipt_lines_supply_receipt_id_supply_purchase_line_id_ UNIQUE (supply_receipt_id, supply_purchase_line_id);


--
-- Name: supply_receipts supply_receipts_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipts
    ADD CONSTRAINT supply_receipts_code_unique UNIQUE (code);


--
-- Name: supply_receipts supply_receipts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipts
    ADD CONSTRAINT supply_receipts_pkey PRIMARY KEY (id);


--
-- Name: supply_request_lines supply_request_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines
    ADD CONSTRAINT supply_request_lines_pkey PRIMARY KEY (id);


--
-- Name: supply_request_lines supply_request_lines_supply_request_id_supply_item_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines
    ADD CONSTRAINT supply_request_lines_supply_request_id_supply_item_id_unique UNIQUE (supply_request_id, supply_item_id);


--
-- Name: supply_requests supply_requests_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_code_unique UNIQUE (code);


--
-- Name: supply_requests supply_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_pkey PRIMARY KEY (id);


--
-- Name: supply_transactions supply_transactions_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_code_unique UNIQUE (code);


--
-- Name: supply_transactions supply_transactions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vehicle_bookings vehicle_bookings_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_code_unique UNIQUE (code);


--
-- Name: vehicle_bookings vehicle_bookings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_pkey PRIMARY KEY (id);


--
-- Name: vehicle_documents vehicle_documents_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_documents
    ADD CONSTRAINT vehicle_documents_pkey PRIMARY KEY (id);


--
-- Name: vehicle_photos vehicle_photos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_photos
    ADD CONSTRAINT vehicle_photos_pkey PRIMARY KEY (id);


--
-- Name: vehicle_refuelings vehicle_refuelings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_refuelings
    ADD CONSTRAINT vehicle_refuelings_pkey PRIMARY KEY (id);


--
-- Name: vehicle_trips vehicle_trips_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips
    ADD CONSTRAINT vehicle_trips_pkey PRIMARY KEY (id);


--
-- Name: vehicles vehicles_asset_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_asset_id_unique UNIQUE (asset_id);


--
-- Name: vehicles vehicles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_pkey PRIMARY KEY (id);


--
-- Name: vehicles vehicles_plate_number_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_plate_number_unique UNIQUE (plate_number);


--
-- Name: vendor_bill_lines vendor_bill_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bill_lines
    ADD CONSTRAINT vendor_bill_lines_pkey PRIMARY KEY (id);


--
-- Name: vendor_bills vendor_bills_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_code_unique UNIQUE (code);


--
-- Name: vendor_bills vendor_bills_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_pkey PRIMARY KEY (id);


--
-- Name: vendors vendors_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_code_unique UNIQUE (code);


--
-- Name: vendors vendors_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendors
    ADD CONSTRAINT vendors_pkey PRIMARY KEY (id);


--
-- Name: work_order_attachments work_order_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_order_attachments
    ADD CONSTRAINT work_order_attachments_pkey PRIMARY KEY (id);


--
-- Name: work_orders work_orders_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_code_unique UNIQUE (code);


--
-- Name: work_orders work_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_pkey PRIMARY KEY (id);


--
-- Name: asset_categories_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_categories_name_index ON public.asset_categories USING btree (name);


--
-- Name: asset_disposals_disposal_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_disposals_disposal_date_index ON public.asset_disposals USING btree (disposal_date);


--
-- Name: asset_disposals_method_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_disposals_method_index ON public.asset_disposals USING btree (method);


--
-- Name: asset_documents_asset_id_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_documents_asset_id_type_index ON public.asset_documents USING btree (asset_id, type);


--
-- Name: asset_transfers_asset_id_transfer_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_transfers_asset_id_transfer_date_index ON public.asset_transfers USING btree (asset_id, transfer_date);


--
-- Name: asset_transfers_transfer_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX asset_transfers_transfer_date_index ON public.asset_transfers USING btree (transfer_date);


--
-- Name: assets_asset_category_id_location_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_asset_category_id_location_id_index ON public.assets USING btree (asset_category_id, location_id);


--
-- Name: assets_lease_end_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_lease_end_date_index ON public.assets USING btree (lease_end_date);


--
-- Name: assets_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_name_index ON public.assets USING btree (name);


--
-- Name: assets_ownership_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_ownership_type_index ON public.assets USING btree (ownership_type);


--
-- Name: assets_serial_number_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_serial_number_index ON public.assets USING btree (serial_number);


--
-- Name: assets_status_condition_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_status_condition_index ON public.assets USING btree (status, condition);


--
-- Name: assets_warranty_until_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX assets_warranty_until_index ON public.assets USING btree (warranty_until);


--
-- Name: audit_logs_auditable_type_auditable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX audit_logs_auditable_type_auditable_id_index ON public.audit_logs USING btree (auditable_type, auditable_id);


--
-- Name: audit_logs_event_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX audit_logs_event_created_at_index ON public.audit_logs USING btree (event, created_at);


--
-- Name: budgets_fiscal_year_department_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX budgets_fiscal_year_department_id_index ON public.budgets USING btree (fiscal_year, department_id);


--
-- Name: business_trip_expenses_business_trip_id_expense_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX business_trip_expenses_business_trip_id_expense_date_index ON public.business_trip_expenses USING btree (business_trip_id, expense_date);


--
-- Name: business_trips_employee_id_start_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX business_trips_employee_id_start_date_index ON public.business_trips USING btree (employee_id, start_date);


--
-- Name: business_trips_status_start_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX business_trips_status_start_date_index ON public.business_trips USING btree (status, start_date);


--
-- Name: cleaning_inspection_lines_cleaning_inspection_id_checked_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cleaning_inspection_lines_cleaning_inspection_id_checked_index ON public.cleaning_inspection_lines USING btree (cleaning_inspection_id, checked);


--
-- Name: cleaning_inspections_status_inspection_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cleaning_inspections_status_inspection_date_index ON public.cleaning_inspections USING btree (status, inspection_date);


--
-- Name: departments_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX departments_name_index ON public.departments USING btree (name);


--
-- Name: depreciation_entries_depreciation_period_id_asset_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX depreciation_entries_depreciation_period_id_asset_id_index ON public.depreciation_entries USING btree (depreciation_period_id, asset_id);


--
-- Name: depreciation_entries_period_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX depreciation_entries_period_index ON public.depreciation_entries USING btree (period);


--
-- Name: employees_full_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX employees_full_name_index ON public.employees USING btree (full_name);


--
-- Name: expense_categories_is_active_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX expense_categories_is_active_code_index ON public.expense_categories USING btree (is_active, code);


--
-- Name: incident_reports_category_severity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX incident_reports_category_severity_index ON public.incident_reports USING btree (category, severity);


--
-- Name: incident_reports_occurred_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX incident_reports_occurred_at_index ON public.incident_reports USING btree (occurred_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: letters_category_logged_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX letters_category_logged_date_index ON public.letters USING btree (category, logged_date);


--
-- Name: letters_direction_logged_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX letters_direction_logged_date_index ON public.letters USING btree (direction, logged_date);


--
-- Name: locations_type_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX locations_type_name_index ON public.locations USING btree (type, name);


--
-- Name: maintenance_schedules_asset_id_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX maintenance_schedules_asset_id_is_active_index ON public.maintenance_schedules USING btree (asset_id, is_active);


--
-- Name: maintenance_schedules_is_active_next_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX maintenance_schedules_is_active_next_due_date_index ON public.maintenance_schedules USING btree (is_active, next_due_date);


--
-- Name: maintenance_visits_asset_id_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX maintenance_visits_asset_id_due_date_index ON public.maintenance_visits USING btree (asset_id, due_date);


--
-- Name: maintenance_visits_maintenance_schedule_id_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX maintenance_visits_maintenance_schedule_id_due_date_index ON public.maintenance_visits USING btree (maintenance_schedule_id, due_date);


--
-- Name: maintenance_visits_status_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX maintenance_visits_status_due_date_index ON public.maintenance_visits USING btree (status, due_date);


--
-- Name: modules_group_sort_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX modules_group_sort_index ON public.modules USING btree ("group", sort);


--
-- Name: parcel_shipments_shipped_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX parcel_shipments_shipped_date_index ON public.parcel_shipments USING btree (shipped_date);


--
-- Name: parcel_shipments_status_request_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX parcel_shipments_status_request_date_index ON public.parcel_shipments USING btree (status, request_date);


--
-- Name: reimbursement_lines_expense_category_id_expense_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reimbursement_lines_expense_category_id_expense_date_index ON public.reimbursement_lines USING btree (expense_category_id, expense_date);


--
-- Name: reimbursement_lines_reimbursement_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reimbursement_lines_reimbursement_id_index ON public.reimbursement_lines USING btree (reimbursement_id);


--
-- Name: reimbursements_department_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reimbursements_department_id_index ON public.reimbursements USING btree (department_id);


--
-- Name: reimbursements_employee_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reimbursements_employee_id_status_index ON public.reimbursements USING btree (employee_id, status);


--
-- Name: reimbursements_status_submitted_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX reimbursements_status_submitted_at_index ON public.reimbursements USING btree (status, submitted_at);


--
-- Name: security_shifts_attendance_shift_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX security_shifts_attendance_shift_date_index ON public.security_shifts USING btree (attendance, shift_date);


--
-- Name: security_shifts_shift_date_shift_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX security_shifts_shift_date_shift_index ON public.security_shifts USING btree (shift_date, shift);


--
-- Name: service_areas_category_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_areas_category_is_active_index ON public.service_areas USING btree (category, is_active);


--
-- Name: service_areas_frequency_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_areas_frequency_is_active_index ON public.service_areas USING btree (frequency, is_active);


--
-- Name: service_request_attachments_service_request_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_request_attachments_service_request_id_index ON public.service_request_attachments USING btree (service_request_id);


--
-- Name: service_request_categories_is_active_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_request_categories_is_active_code_index ON public.service_request_categories USING btree (is_active, code);


--
-- Name: service_requests_department_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_requests_department_id_status_index ON public.service_requests USING btree (department_id, status);


--
-- Name: service_requests_requester_employee_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_requests_requester_employee_id_status_index ON public.service_requests USING btree (requester_employee_id, status);


--
-- Name: service_requests_status_priority_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_requests_status_priority_index ON public.service_requests USING btree (status, priority);


--
-- Name: service_staff_kind_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX service_staff_kind_is_active_index ON public.service_staff USING btree (kind, is_active);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: stock_opname_lines_asset_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX stock_opname_lines_asset_code_index ON public.stock_opname_lines USING btree (asset_code);


--
-- Name: stock_opname_lines_stock_opname_id_checked_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX stock_opname_lines_stock_opname_id_checked_index ON public.stock_opname_lines USING btree (stock_opname_id, checked);


--
-- Name: stock_opnames_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX stock_opnames_status_index ON public.stock_opnames USING btree (status);


--
-- Name: supply_items_category_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_items_category_index ON public.supply_items USING btree (category);


--
-- Name: supply_items_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_items_is_active_index ON public.supply_items USING btree (is_active);


--
-- Name: supply_opname_lines_supply_opname_id_checked_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_opname_lines_supply_opname_id_checked_index ON public.supply_opname_lines USING btree (supply_opname_id, checked);


--
-- Name: supply_opnames_status_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_opnames_status_created_at_index ON public.supply_opnames USING btree (status, created_at);


--
-- Name: supply_purchase_lines_supply_item_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_purchase_lines_supply_item_id_index ON public.supply_purchase_lines USING btree (supply_item_id);


--
-- Name: supply_purchases_kind_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_purchases_kind_status_index ON public.supply_purchases USING btree (kind, status);


--
-- Name: supply_purchases_status_order_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_purchases_status_order_date_index ON public.supply_purchases USING btree (status, order_date);


--
-- Name: supply_purchases_vendor_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_purchases_vendor_id_index ON public.supply_purchases USING btree (vendor_id);


--
-- Name: supply_receipt_lines_supply_purchase_line_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_receipt_lines_supply_purchase_line_id_index ON public.supply_receipt_lines USING btree (supply_purchase_line_id);


--
-- Name: supply_receipts_supply_purchase_id_receipt_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_receipts_supply_purchase_id_receipt_date_index ON public.supply_receipts USING btree (supply_purchase_id, receipt_date);


--
-- Name: supply_request_lines_supply_item_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_request_lines_supply_item_id_index ON public.supply_request_lines USING btree (supply_item_id);


--
-- Name: supply_requests_department_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_requests_department_id_status_index ON public.supply_requests USING btree (department_id, status);


--
-- Name: supply_requests_employee_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_requests_employee_id_status_index ON public.supply_requests USING btree (employee_id, status);


--
-- Name: supply_requests_status_submitted_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_requests_status_submitted_at_index ON public.supply_requests USING btree (status, submitted_at);


--
-- Name: supply_transactions_supply_item_id_transaction_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_transactions_supply_item_id_transaction_date_index ON public.supply_transactions USING btree (supply_item_id, transaction_date);


--
-- Name: supply_transactions_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX supply_transactions_type_index ON public.supply_transactions USING btree (type);


--
-- Name: vehicle_bookings_requester_employee_id_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_bookings_requester_employee_id_status_index ON public.vehicle_bookings USING btree (requester_employee_id, status);


--
-- Name: vehicle_bookings_status_start_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_bookings_status_start_at_index ON public.vehicle_bookings USING btree (status, start_at);


--
-- Name: vehicle_bookings_vehicle_id_start_at_end_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_bookings_vehicle_id_start_at_end_at_index ON public.vehicle_bookings USING btree (vehicle_id, start_at, end_at);


--
-- Name: vehicle_documents_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_documents_expires_at_index ON public.vehicle_documents USING btree (expires_at);


--
-- Name: vehicle_documents_vehicle_id_type_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_documents_vehicle_id_type_expires_at_index ON public.vehicle_documents USING btree (vehicle_id, type, expires_at);


--
-- Name: vehicle_photos_vehicle_id_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_photos_vehicle_id_type_index ON public.vehicle_photos USING btree (vehicle_id, type);


--
-- Name: vehicle_refuelings_vehicle_id_filled_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_refuelings_vehicle_id_filled_at_index ON public.vehicle_refuelings USING btree (vehicle_id, filled_at);


--
-- Name: vehicle_refuelings_vehicle_id_odometer_km_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_refuelings_vehicle_id_odometer_km_index ON public.vehicle_refuelings USING btree (vehicle_id, odometer_km);


--
-- Name: vehicle_trips_vehicle_booking_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_trips_vehicle_booking_id_index ON public.vehicle_trips USING btree (vehicle_booking_id);


--
-- Name: vehicle_trips_vehicle_id_departed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicle_trips_vehicle_id_departed_at_index ON public.vehicle_trips USING btree (vehicle_id, departed_at);


--
-- Name: vehicles_usage_mode_is_active_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicles_usage_mode_is_active_index ON public.vehicles USING btree (usage_mode, is_active);


--
-- Name: vehicles_vehicle_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vehicles_vehicle_type_index ON public.vehicles USING btree (vehicle_type);


--
-- Name: vendor_bill_lines_expense_category_id_department_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendor_bill_lines_expense_category_id_department_id_index ON public.vendor_bill_lines USING btree (expense_category_id, department_id);


--
-- Name: vendor_bill_lines_vendor_bill_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendor_bill_lines_vendor_bill_id_index ON public.vendor_bill_lines USING btree (vendor_bill_id);


--
-- Name: vendor_bills_due_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendor_bills_due_date_index ON public.vendor_bills USING btree (due_date);


--
-- Name: vendor_bills_status_invoice_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendor_bills_status_invoice_date_index ON public.vendor_bills USING btree (status, invoice_date);


--
-- Name: vendor_bills_vendor_id_invoice_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendor_bills_vendor_id_invoice_date_index ON public.vendor_bills USING btree (vendor_id, invoice_date);


--
-- Name: vendors_is_active_name_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX vendors_is_active_name_index ON public.vendors USING btree (is_active, name);


--
-- Name: work_order_attachments_work_order_id_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX work_order_attachments_work_order_id_type_index ON public.work_order_attachments USING btree (work_order_id, type);


--
-- Name: work_orders_asset_id_reported_date_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX work_orders_asset_id_reported_date_index ON public.work_orders USING btree (asset_id, reported_date);


--
-- Name: work_orders_status_priority_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX work_orders_status_priority_index ON public.work_orders USING btree (status, priority);


--
-- Name: work_orders_type_status_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX work_orders_type_status_index ON public.work_orders USING btree (type, status);


--
-- Name: asset_disposals asset_disposals_approved_by_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_approved_by_employee_id_foreign FOREIGN KEY (approved_by_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: asset_disposals asset_disposals_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE RESTRICT;


--
-- Name: asset_disposals asset_disposals_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_disposals
    ADD CONSTRAINT asset_disposals_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: asset_documents asset_documents_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_documents
    ADD CONSTRAINT asset_documents_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: asset_documents asset_documents_uploaded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_documents
    ADD CONSTRAINT asset_documents_uploaded_by_user_id_foreign FOREIGN KEY (uploaded_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE RESTRICT;


--
-- Name: asset_transfers asset_transfers_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_from_custodian_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_from_custodian_employee_id_foreign FOREIGN KEY (from_custodian_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_from_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_from_department_id_foreign FOREIGN KEY (from_department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_from_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_from_location_id_foreign FOREIGN KEY (from_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_handed_over_by_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_handed_over_by_employee_id_foreign FOREIGN KEY (handed_over_by_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_received_by_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_received_by_employee_id_foreign FOREIGN KEY (received_by_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_to_custodian_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_to_custodian_employee_id_foreign FOREIGN KEY (to_custodian_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_to_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_to_department_id_foreign FOREIGN KEY (to_department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: asset_transfers asset_transfers_to_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.asset_transfers
    ADD CONSTRAINT asset_transfers_to_location_id_foreign FOREIGN KEY (to_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: assets assets_asset_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_asset_category_id_foreign FOREIGN KEY (asset_category_id) REFERENCES public.asset_categories(id) ON DELETE RESTRICT;


--
-- Name: assets assets_custodian_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_custodian_employee_id_foreign FOREIGN KEY (custodian_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: assets assets_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: assets assets_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.assets
    ADD CONSTRAINT assets_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: audit_logs audit_logs_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.audit_logs
    ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: budgets budgets_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: budgets budgets_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: budgets budgets_expense_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.budgets
    ADD CONSTRAINT budgets_expense_category_id_foreign FOREIGN KEY (expense_category_id) REFERENCES public.expense_categories(id) ON DELETE CASCADE;


--
-- Name: business_trip_expenses business_trip_expenses_business_trip_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_expenses
    ADD CONSTRAINT business_trip_expenses_business_trip_id_foreign FOREIGN KEY (business_trip_id) REFERENCES public.business_trips(id) ON DELETE CASCADE;


--
-- Name: business_trip_participants business_trip_participants_business_trip_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_participants
    ADD CONSTRAINT business_trip_participants_business_trip_id_foreign FOREIGN KEY (business_trip_id) REFERENCES public.business_trips(id) ON DELETE CASCADE;


--
-- Name: business_trip_participants business_trip_participants_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trip_participants
    ADD CONSTRAINT business_trip_participants_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE RESTRICT;


--
-- Name: business_trips business_trips_advance_paid_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_advance_paid_by_user_id_foreign FOREIGN KEY (advance_paid_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: business_trips business_trips_approver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_approver_employee_id_foreign FOREIGN KEY (approver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: business_trips business_trips_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: business_trips business_trips_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE RESTRICT;


--
-- Name: business_trips business_trips_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE RESTRICT;


--
-- Name: business_trips business_trips_settled_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.business_trips
    ADD CONSTRAINT business_trips_settled_by_user_id_foreign FOREIGN KEY (settled_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: cleaning_inspection_lines cleaning_inspection_lines_cleaning_inspection_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspection_lines
    ADD CONSTRAINT cleaning_inspection_lines_cleaning_inspection_id_foreign FOREIGN KEY (cleaning_inspection_id) REFERENCES public.cleaning_inspections(id) ON DELETE CASCADE;


--
-- Name: cleaning_inspection_lines cleaning_inspection_lines_service_area_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspection_lines
    ADD CONSTRAINT cleaning_inspection_lines_service_area_id_foreign FOREIGN KEY (service_area_id) REFERENCES public.service_areas(id) ON DELETE SET NULL;


--
-- Name: cleaning_inspections cleaning_inspections_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspections
    ADD CONSTRAINT cleaning_inspections_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: cleaning_inspections cleaning_inspections_scope_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cleaning_inspections
    ADD CONSTRAINT cleaning_inspections_scope_location_id_foreign FOREIGN KEY (scope_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: departments departments_head_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_head_employee_id_foreign FOREIGN KEY (head_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: departments departments_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.departments
    ADD CONSTRAINT departments_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: depreciation_entries depreciation_entries_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_entries
    ADD CONSTRAINT depreciation_entries_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: depreciation_entries depreciation_entries_depreciation_period_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_entries
    ADD CONSTRAINT depreciation_entries_depreciation_period_id_foreign FOREIGN KEY (depreciation_period_id) REFERENCES public.depreciation_periods(id) ON DELETE CASCADE;


--
-- Name: depreciation_periods depreciation_periods_closed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.depreciation_periods
    ADD CONSTRAINT depreciation_periods_closed_by_user_id_foreign FOREIGN KEY (closed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: employees employees_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: employees employees_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.employees
    ADD CONSTRAINT employees_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_closed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_closed_by_user_id_foreign FOREIGN KEY (closed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_reported_by_staff_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_reported_by_staff_id_foreign FOREIGN KEY (reported_by_staff_id) REFERENCES public.service_staff(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_security_shift_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_security_shift_id_foreign FOREIGN KEY (security_shift_id) REFERENCES public.security_shifts(id) ON DELETE SET NULL;


--
-- Name: incident_reports incident_reports_service_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_reports
    ADD CONSTRAINT incident_reports_service_request_id_foreign FOREIGN KEY (service_request_id) REFERENCES public.service_requests(id) ON DELETE SET NULL;


--
-- Name: letters letters_assigned_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_assigned_employee_id_foreign FOREIGN KEY (assigned_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: letters letters_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: letters letters_handed_over_to_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_handed_over_to_employee_id_foreign FOREIGN KEY (handed_over_to_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: letters letters_signer_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.letters
    ADD CONSTRAINT letters_signer_employee_id_foreign FOREIGN KEY (signer_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: locations locations_parent_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.locations
    ADD CONSTRAINT locations_parent_id_foreign FOREIGN KEY (parent_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: maintenance_schedules maintenance_schedules_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT maintenance_schedules_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: maintenance_schedules maintenance_schedules_technician_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT maintenance_schedules_technician_employee_id_foreign FOREIGN KEY (technician_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: maintenance_schedules maintenance_schedules_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_schedules
    ADD CONSTRAINT maintenance_schedules_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: maintenance_visits maintenance_visits_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: maintenance_visits maintenance_visits_closed_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_closed_by_user_id_foreign FOREIGN KEY (closed_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: maintenance_visits maintenance_visits_maintenance_schedule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_maintenance_schedule_id_foreign FOREIGN KEY (maintenance_schedule_id) REFERENCES public.maintenance_schedules(id) ON DELETE CASCADE;


--
-- Name: maintenance_visits maintenance_visits_technician_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_technician_employee_id_foreign FOREIGN KEY (technician_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: maintenance_visits maintenance_visits_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.maintenance_visits
    ADD CONSTRAINT maintenance_visits_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: number_sequence_periods number_sequence_periods_number_sequence_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.number_sequence_periods
    ADD CONSTRAINT number_sequence_periods_number_sequence_id_foreign FOREIGN KEY (number_sequence_id) REFERENCES public.number_sequences(id) ON DELETE CASCADE;


--
-- Name: parcel_shipments parcel_shipments_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: parcel_shipments parcel_shipments_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE RESTRICT;


--
-- Name: parcel_shipments parcel_shipments_requester_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_requester_employee_id_foreign FOREIGN KEY (requester_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: parcel_shipments parcel_shipments_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.parcel_shipments
    ADD CONSTRAINT parcel_shipments_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: permission_role permission_role_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: permission_role permission_role_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_role
    ADD CONSTRAINT permission_role_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: permission_user permission_user_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_user
    ADD CONSTRAINT permission_user_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: permission_user permission_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permission_user
    ADD CONSTRAINT permission_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: permissions permissions_module_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_module_id_foreign FOREIGN KEY (module_id) REFERENCES public.modules(id) ON DELETE CASCADE;


--
-- Name: reimbursement_lines reimbursement_lines_expense_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursement_lines
    ADD CONSTRAINT reimbursement_lines_expense_category_id_foreign FOREIGN KEY (expense_category_id) REFERENCES public.expense_categories(id) ON DELETE CASCADE;


--
-- Name: reimbursement_lines reimbursement_lines_reimbursement_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursement_lines
    ADD CONSTRAINT reimbursement_lines_reimbursement_id_foreign FOREIGN KEY (reimbursement_id) REFERENCES public.reimbursements(id) ON DELETE CASCADE;


--
-- Name: reimbursements reimbursements_approver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_approver_employee_id_foreign FOREIGN KEY (approver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: reimbursements reimbursements_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: reimbursements reimbursements_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: reimbursements reimbursements_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE CASCADE;


--
-- Name: reimbursements reimbursements_paid_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_paid_by_user_id_foreign FOREIGN KEY (paid_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: reimbursements reimbursements_verified_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reimbursements
    ADD CONSTRAINT reimbursements_verified_by_user_id_foreign FOREIGN KEY (verified_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: role_user role_user_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- Name: role_user role_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: security_shifts security_shifts_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: security_shifts security_shifts_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: security_shifts security_shifts_replacement_staff_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_replacement_staff_id_foreign FOREIGN KEY (replacement_staff_id) REFERENCES public.service_staff(id) ON DELETE SET NULL;


--
-- Name: security_shifts security_shifts_service_staff_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.security_shifts
    ADD CONSTRAINT security_shifts_service_staff_id_foreign FOREIGN KEY (service_staff_id) REFERENCES public.service_staff(id) ON DELETE CASCADE;


--
-- Name: service_areas service_areas_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_areas
    ADD CONSTRAINT service_areas_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: service_areas service_areas_service_staff_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_areas
    ADD CONSTRAINT service_areas_service_staff_id_foreign FOREIGN KEY (service_staff_id) REFERENCES public.service_staff(id) ON DELETE SET NULL;


--
-- Name: service_request_attachments service_request_attachments_service_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_attachments
    ADD CONSTRAINT service_request_attachments_service_request_id_foreign FOREIGN KEY (service_request_id) REFERENCES public.service_requests(id) ON DELETE CASCADE;


--
-- Name: service_request_attachments service_request_attachments_uploaded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_request_attachments
    ADD CONSTRAINT service_request_attachments_uploaded_by_user_id_foreign FOREIGN KEY (uploaded_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_accepted_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_accepted_by_user_id_foreign FOREIGN KEY (accepted_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_approver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_approver_employee_id_foreign FOREIGN KEY (approver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_requester_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_requester_employee_id_foreign FOREIGN KEY (requester_employee_id) REFERENCES public.employees(id) ON DELETE CASCADE;


--
-- Name: service_requests service_requests_service_request_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_service_request_category_id_foreign FOREIGN KEY (service_request_category_id) REFERENCES public.service_request_categories(id) ON DELETE SET NULL;


--
-- Name: service_requests service_requests_work_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_requests
    ADD CONSTRAINT service_requests_work_order_id_foreign FOREIGN KEY (work_order_id) REFERENCES public.work_orders(id) ON DELETE SET NULL;


--
-- Name: service_staff service_staff_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_staff
    ADD CONSTRAINT service_staff_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: service_staff service_staff_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.service_staff
    ADD CONSTRAINT service_staff_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: stock_opname_lines stock_opname_lines_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: stock_opname_lines stock_opname_lines_checked_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_checked_by_user_id_foreign FOREIGN KEY (checked_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: stock_opname_lines stock_opname_lines_expected_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_expected_location_id_foreign FOREIGN KEY (expected_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: stock_opname_lines stock_opname_lines_found_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_found_location_id_foreign FOREIGN KEY (found_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: stock_opname_lines stock_opname_lines_stock_opname_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opname_lines
    ADD CONSTRAINT stock_opname_lines_stock_opname_id_foreign FOREIGN KEY (stock_opname_id) REFERENCES public.stock_opnames(id) ON DELETE CASCADE;


--
-- Name: stock_opnames stock_opnames_adjusted_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_adjusted_by_user_id_foreign FOREIGN KEY (adjusted_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: stock_opnames stock_opnames_finished_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_finished_by_user_id_foreign FOREIGN KEY (finished_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: stock_opnames stock_opnames_scope_asset_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_scope_asset_category_id_foreign FOREIGN KEY (scope_asset_category_id) REFERENCES public.asset_categories(id) ON DELETE SET NULL;


--
-- Name: stock_opnames stock_opnames_scope_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_scope_department_id_foreign FOREIGN KEY (scope_department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: stock_opnames stock_opnames_scope_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_scope_location_id_foreign FOREIGN KEY (scope_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: stock_opnames stock_opnames_started_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.stock_opnames
    ADD CONSTRAINT stock_opnames_started_by_user_id_foreign FOREIGN KEY (started_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_items supply_items_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_items
    ADD CONSTRAINT supply_items_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: supply_opname_lines supply_opname_lines_supply_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines
    ADD CONSTRAINT supply_opname_lines_supply_item_id_foreign FOREIGN KEY (supply_item_id) REFERENCES public.supply_items(id) ON DELETE CASCADE;


--
-- Name: supply_opname_lines supply_opname_lines_supply_opname_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines
    ADD CONSTRAINT supply_opname_lines_supply_opname_id_foreign FOREIGN KEY (supply_opname_id) REFERENCES public.supply_opnames(id) ON DELETE CASCADE;


--
-- Name: supply_opname_lines supply_opname_lines_supply_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opname_lines
    ADD CONSTRAINT supply_opname_lines_supply_transaction_id_foreign FOREIGN KEY (supply_transaction_id) REFERENCES public.supply_transactions(id) ON DELETE SET NULL;


--
-- Name: supply_opnames supply_opnames_adjusted_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames
    ADD CONSTRAINT supply_opnames_adjusted_by_user_id_foreign FOREIGN KEY (adjusted_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_opnames supply_opnames_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames
    ADD CONSTRAINT supply_opnames_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_opnames supply_opnames_scope_location_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_opnames
    ADD CONSTRAINT supply_opnames_scope_location_id_foreign FOREIGN KEY (scope_location_id) REFERENCES public.locations(id) ON DELETE SET NULL;


--
-- Name: supply_purchase_lines supply_purchase_lines_supply_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchase_lines
    ADD CONSTRAINT supply_purchase_lines_supply_item_id_foreign FOREIGN KEY (supply_item_id) REFERENCES public.supply_items(id) ON DELETE CASCADE;


--
-- Name: supply_purchase_lines supply_purchase_lines_supply_purchase_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchase_lines
    ADD CONSTRAINT supply_purchase_lines_supply_purchase_id_foreign FOREIGN KEY (supply_purchase_id) REFERENCES public.supply_purchases(id) ON DELETE CASCADE;


--
-- Name: supply_purchases supply_purchases_approved_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases
    ADD CONSTRAINT supply_purchases_approved_by_user_id_foreign FOREIGN KEY (approved_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_purchases supply_purchases_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases
    ADD CONSTRAINT supply_purchases_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_purchases supply_purchases_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_purchases
    ADD CONSTRAINT supply_purchases_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- Name: supply_receipt_lines supply_receipt_lines_supply_purchase_line_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines
    ADD CONSTRAINT supply_receipt_lines_supply_purchase_line_id_foreign FOREIGN KEY (supply_purchase_line_id) REFERENCES public.supply_purchase_lines(id) ON DELETE CASCADE;


--
-- Name: supply_receipt_lines supply_receipt_lines_supply_receipt_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines
    ADD CONSTRAINT supply_receipt_lines_supply_receipt_id_foreign FOREIGN KEY (supply_receipt_id) REFERENCES public.supply_receipts(id) ON DELETE CASCADE;


--
-- Name: supply_receipt_lines supply_receipt_lines_supply_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipt_lines
    ADD CONSTRAINT supply_receipt_lines_supply_transaction_id_foreign FOREIGN KEY (supply_transaction_id) REFERENCES public.supply_transactions(id) ON DELETE SET NULL;


--
-- Name: supply_receipts supply_receipts_received_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipts
    ADD CONSTRAINT supply_receipts_received_by_user_id_foreign FOREIGN KEY (received_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_receipts supply_receipts_supply_purchase_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_receipts
    ADD CONSTRAINT supply_receipts_supply_purchase_id_foreign FOREIGN KEY (supply_purchase_id) REFERENCES public.supply_purchases(id) ON DELETE CASCADE;


--
-- Name: supply_request_lines supply_request_lines_supply_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines
    ADD CONSTRAINT supply_request_lines_supply_item_id_foreign FOREIGN KEY (supply_item_id) REFERENCES public.supply_items(id) ON DELETE CASCADE;


--
-- Name: supply_request_lines supply_request_lines_supply_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines
    ADD CONSTRAINT supply_request_lines_supply_request_id_foreign FOREIGN KEY (supply_request_id) REFERENCES public.supply_requests(id) ON DELETE CASCADE;


--
-- Name: supply_request_lines supply_request_lines_supply_transaction_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_request_lines
    ADD CONSTRAINT supply_request_lines_supply_transaction_id_foreign FOREIGN KEY (supply_transaction_id) REFERENCES public.supply_transactions(id) ON DELETE SET NULL;


--
-- Name: supply_requests supply_requests_approver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_approver_employee_id_foreign FOREIGN KEY (approver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: supply_requests supply_requests_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_requests supply_requests_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE CASCADE;


--
-- Name: supply_requests supply_requests_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE CASCADE;


--
-- Name: supply_requests supply_requests_issued_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_requests
    ADD CONSTRAINT supply_requests_issued_by_user_id_foreign FOREIGN KEY (issued_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_transactions supply_transactions_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: supply_transactions supply_transactions_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: supply_transactions supply_transactions_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_employee_id_foreign FOREIGN KEY (employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: supply_transactions supply_transactions_supply_item_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.supply_transactions
    ADD CONSTRAINT supply_transactions_supply_item_id_foreign FOREIGN KEY (supply_item_id) REFERENCES public.supply_items(id) ON DELETE CASCADE;


--
-- Name: vehicle_bookings vehicle_bookings_approver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_approver_employee_id_foreign FOREIGN KEY (approver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: vehicle_bookings vehicle_bookings_assigned_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_assigned_by_user_id_foreign FOREIGN KEY (assigned_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_bookings vehicle_bookings_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_bookings vehicle_bookings_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: vehicle_bookings vehicle_bookings_driver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_driver_employee_id_foreign FOREIGN KEY (driver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: vehicle_bookings vehicle_bookings_requester_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_requester_employee_id_foreign FOREIGN KEY (requester_employee_id) REFERENCES public.employees(id) ON DELETE CASCADE;


--
-- Name: vehicle_bookings vehicle_bookings_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_bookings
    ADD CONSTRAINT vehicle_bookings_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE SET NULL;


--
-- Name: vehicle_documents vehicle_documents_uploaded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_documents
    ADD CONSTRAINT vehicle_documents_uploaded_by_user_id_foreign FOREIGN KEY (uploaded_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_documents vehicle_documents_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_documents
    ADD CONSTRAINT vehicle_documents_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: vehicle_photos vehicle_photos_uploaded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_photos
    ADD CONSTRAINT vehicle_photos_uploaded_by_user_id_foreign FOREIGN KEY (uploaded_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_photos vehicle_photos_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_photos
    ADD CONSTRAINT vehicle_photos_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: vehicle_refuelings vehicle_refuelings_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_refuelings
    ADD CONSTRAINT vehicle_refuelings_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_refuelings vehicle_refuelings_driver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_refuelings
    ADD CONSTRAINT vehicle_refuelings_driver_employee_id_foreign FOREIGN KEY (driver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: vehicle_refuelings vehicle_refuelings_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_refuelings
    ADD CONSTRAINT vehicle_refuelings_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: vehicle_trips vehicle_trips_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips
    ADD CONSTRAINT vehicle_trips_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vehicle_trips vehicle_trips_driver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips
    ADD CONSTRAINT vehicle_trips_driver_employee_id_foreign FOREIGN KEY (driver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: vehicle_trips vehicle_trips_vehicle_booking_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips
    ADD CONSTRAINT vehicle_trips_vehicle_booking_id_foreign FOREIGN KEY (vehicle_booking_id) REFERENCES public.vehicle_bookings(id) ON DELETE SET NULL;


--
-- Name: vehicle_trips vehicle_trips_vehicle_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicle_trips
    ADD CONSTRAINT vehicle_trips_vehicle_id_foreign FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: vehicles vehicles_default_driver_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vehicles
    ADD CONSTRAINT vehicles_default_driver_employee_id_foreign FOREIGN KEY (default_driver_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: vendor_bill_lines vendor_bill_lines_department_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bill_lines
    ADD CONSTRAINT vendor_bill_lines_department_id_foreign FOREIGN KEY (department_id) REFERENCES public.departments(id) ON DELETE SET NULL;


--
-- Name: vendor_bill_lines vendor_bill_lines_expense_category_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bill_lines
    ADD CONSTRAINT vendor_bill_lines_expense_category_id_foreign FOREIGN KEY (expense_category_id) REFERENCES public.expense_categories(id) ON DELETE CASCADE;


--
-- Name: vendor_bill_lines vendor_bill_lines_vendor_bill_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bill_lines
    ADD CONSTRAINT vendor_bill_lines_vendor_bill_id_foreign FOREIGN KEY (vendor_bill_id) REFERENCES public.vendor_bills(id) ON DELETE CASCADE;


--
-- Name: vendor_bills vendor_bills_approved_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_approved_by_user_id_foreign FOREIGN KEY (approved_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vendor_bills vendor_bills_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vendor_bills vendor_bills_paid_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_paid_by_user_id_foreign FOREIGN KEY (paid_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: vendor_bills vendor_bills_supply_purchase_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_supply_purchase_id_foreign FOREIGN KEY (supply_purchase_id) REFERENCES public.supply_purchases(id) ON DELETE SET NULL;


--
-- Name: vendor_bills vendor_bills_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.vendor_bills
    ADD CONSTRAINT vendor_bills_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE CASCADE;


--
-- Name: work_order_attachments work_order_attachments_uploaded_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_order_attachments
    ADD CONSTRAINT work_order_attachments_uploaded_by_user_id_foreign FOREIGN KEY (uploaded_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: work_order_attachments work_order_attachments_work_order_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_order_attachments
    ADD CONSTRAINT work_order_attachments_work_order_id_foreign FOREIGN KEY (work_order_id) REFERENCES public.work_orders(id) ON DELETE CASCADE;


--
-- Name: work_orders work_orders_asset_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_asset_id_foreign FOREIGN KEY (asset_id) REFERENCES public.assets(id) ON DELETE CASCADE;


--
-- Name: work_orders work_orders_created_by_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_created_by_user_id_foreign FOREIGN KEY (created_by_user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_maintenance_schedule_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_maintenance_schedule_id_foreign FOREIGN KEY (maintenance_schedule_id) REFERENCES public.maintenance_schedules(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_maintenance_visit_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_maintenance_visit_id_foreign FOREIGN KEY (maintenance_visit_id) REFERENCES public.maintenance_visits(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_reported_by_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_reported_by_employee_id_foreign FOREIGN KEY (reported_by_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_service_request_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_service_request_id_foreign FOREIGN KEY (service_request_id) REFERENCES public.service_requests(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_technician_employee_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_technician_employee_id_foreign FOREIGN KEY (technician_employee_id) REFERENCES public.employees(id) ON DELETE SET NULL;


--
-- Name: work_orders work_orders_vendor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.work_orders
    ADD CONSTRAINT work_orders_vendor_id_foreign FOREIGN KEY (vendor_id) REFERENCES public.vendors(id) ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

\unrestrict TscsQJZ9UC7Z59nOJwe6bPZdvv88foFAurhIjgrnwLWx5bolG4H0HxjXZRMqlzJ

